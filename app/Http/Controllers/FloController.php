<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Material;
use App\CodeGenerator;
use App\Flo;
use App\FloDetail;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use DataTables;
use Yajra\DataTables\Exception;
use Response;

class FloController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index_sn(){
        $flos = Flo::orderBy('flo_number', 'asc')
        ->where('status', '=', 0)
        ->get();
        return view('flos.flo_sn', array(
            'flos' => $flos
        ))->with('page', 'FLO Serial Number');
    }

    public function index_flo_detail(Request $request){
        $flo_details = DB::table('flo_details')
        ->leftJoin('flos', 'flo_details.flo_number', '=', 'flos.flo_number')
        ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id','=', 'shipment_schedules.id')
        ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
        ->where('flo_details.flo_number', '=', $request->get('flo_number'))
        ->where('flos.status', '=', 0)
        ->select('shipment_schedules.material_number', 'materials.material_description', 'flo_details.serial_number', 'flo_details.id')
        ->get();

        return DataTables::of($flo_details)
        ->addColumn('action', function($flo_details){
            return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $flo_details->id . '"><i class="glyphicon glyphicon-trash"></i></a>';
        })
        ->make(true);
    }

    public function index_flo(Request $request){
        $flos = DB::table('flos')
        ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id','=', 'shipment_schedules.id')
        ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
        ->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
        ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
        ->where('flos.status', '=', 1)
        ->select('flos.flo_number', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_conditions.shipment_condition_name', 'materials.material_number', 'materials.material_description', 'flos.actual', 'flos.id')
        ->get();

        return DataTables::of($flos)
        ->addColumn('action', function($flos){
            return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="cancelConfirmation(id)" id="' . $flos->id . '"><i class="glyphicon glyphicon-remove-sign"></i></a>';
        })
        ->make(true);
    }

    public function scan_material_number(Request $request){
        $flo = DB::table('flos')
        ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
        ->where('shipment_schedules.material_number', '=', $request->get('material_number'))
        ->where('flos.status', '=', '0')
        ->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
        ->first();

        if($flo == null ){
            $shipment_schedule = DB::table('shipment_schedules')
            ->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
            ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
            ->where('shipment_schedule_id', '=', $request->get('material_number'))
            ->orderBy('shipment_schedules.st_date', 'ASC')
            ->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'))
            ->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo')
            ->having('flo_quantity' , '>', 0)
            ->first();

            if($shipment_schedule == null){
                $response = array(
                    'status' => true,
                    'message' => 'Shipment schedule available',
                    'status_code' => 1001
                );
                return Response::json($response);
            }
            else{
                $response = array(
                    'status' => false,
                    'message' => 'There is no shipment schedule for '. $request->get('material_number') . ' yet.',
                );
                return Response::json($response);
            }
        }
        else{
            $response = array(
                'status' => true,
                'message' => 'Open FLO available',
                'flo_number' => $flo->flo_number,
                'status_code' => 1000
            ); 
            return Response::json($response);
        }
    }

    public function scan_serial_number(Request $request)
    {
        $id = Auth::id();
        try{
            if($request->get('flo_number') == ""){
                $shipment_schedule = DB::table('shipment_schedules')
                ->leftJoin('flos', 'shipment_schedules.id' , '=', 'flos.shipment_schedule_id')
                ->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
                ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
                ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
                ->where('shipment_schedules.material_number', '=' , $request->get('material_number'))
                ->orderBy('shipment_schedules.st_date', 'asc')
                ->select('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'materials.material_description', 'shipment_schedules.st_date', DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'))
                ->groupBy('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', 'shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.st_date', 'materials.material_description')
                ->having('flo_quantity', '>' , '0')
                ->first();

                if($shipment_schedule != null){
                    $prefix_now = date("Y").date("m");
                    $code_generator = CodeGenerator::where('note','=','flo')->first();
                    $material_number = $request->get('material_number');

                    if ($prefix_now != $code_generator->prefix){
                        $code_generator->prefix = $prefix_now;
                        $code_generator->index = '0';
                        $code_generator->save();
                    }

                    $number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index);
                    $flo_number = $code_generator->prefix . $number+1;

                    try {
                        $connector = new WindowsPrintConnector("FLO Printer");
                        $printer = new Printer($connector);

                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->feed(2);
                        $printer->barcode($flo_number, Printer::BARCODE_UPCA);
                        $printer->text($flo_number."\n\n");
                        $printer->setTextSize(6, 3);
                        $printer->text(strtoupper($shipment_schedule->destination_shortname."\n\n"));
                        $printer->initialize();

                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->setTextSize(4, 2);
                        $printer->text(date('d-M-Y', strtotime($shipment_schedule->st_date))."\n\n");
                        $printer->text(strtoupper($shipment_schedule->shipment_condition_name)."\n\n");

                        $printer->initialize();
                        $printer->setTextSize(2, 2);
                        $printer->text("   ".strtoupper($shipment_schedule->material_number)."\n");
                        $printer->text("   ".strtoupper($shipment_schedule->material_description)."\n");

                        $printer->initialize();
                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->text("------------------------------------");
                        $printer->feed(1);
                        $printer->text("|Qty:             |Qty:            |");
                        $printer->feed(1);
                        $printer->text("|                 |                |");
                        $printer->feed(1);
                        $printer->text("|                 |                |");
                        $printer->feed(1);
                        $printer->text("|                 |                |");
                        $printer->feed(1);
                        $printer->text("|Production       |Logistic        |");
                        $printer->feed(1);
                        $printer->text("------------------------------------");
                        $printer->feed(2);
                        $printer->cut();
                        $printer->close();

                        $flo = new Flo([
                            'flo_number' => $flo_number,
                            'shipment_schedule_id' => $shipment_schedule->id,
                            'quantity' => $shipment_schedule->flo_quantity,
                            'actual' => 1,
                            'created_by' => $id
                        ]);
                        $flo->save();

                        $flo_detail = new FloDetail([
                            'serial_number' => $request->get('serial_number'),
                            'flo_number' => $flo_number,
                            'quantity' => 1,
                            'created_by' => $id
                        ]);
                        $flo_detail->save();

                        $code_generator->index = $code_generator->index+1;
                        $code_generator->save();

                        $response = array(
                            'status' => true,
                            'message' => 'New FLO has been printed and FLO fulfillment success',
                            'flo_number' => $flo_number,
                            'code_status' => 1000
                        ); 
                        return Response::json($response);
                    }
                    catch(\Exception $e){
                        $response = array(
                            'status' => false,
                            'message' => "Couldn't print to this printer " . $e->getMessage() . "\n"
                        );
                        return Response::json($response);
                    }
                }
            }
            else{
                try{
                    $id = Auth::id();
                    $flo_detail = new FloDetail([
                        'serial_number' => $request->get('serial_number'),
                        'flo_number' => $request->get('flo_number'),
                        'quantity' => '1',
                        'created_by' => $id
                    ]);
                    $flo_detail->save();

                    $flo = Flo::where('flo_number', '=', $request->get('flo_number'))->first();
                    $flo->actual = $flo->actual+1;
                    $flo->save();

                    $response = array(
                        'status' => true,
                        'message' => 'FLO fulfillment success',
                        'code_status' => 1001
                    ); 
                    return Response::json($response);
                }
                catch (QueryException $e){
                    $error_code = $e->errorInfo[1];
                    if($error_code == 1062){
                        $response = array(
                            'status' => false,
                            'message' => "Serial number already exist."
                        );
                        return Response::json($response);
                    }
                }
            }
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                $response = array(
                    'status' => false,
                    'message' => "Serial number already exist."
                );
                return Response::json($response);
            }
        }
    }

    public function reprint_flo(Request $request)
    {
        $flo = DB::table('flos')
        ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id' , '=', 'shipment_schedules.id')
        ->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
        ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
        ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
        ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
        ->where('flos.flo_number', '=', $request->get('flo_number_reprint'))
        ->select('flos.flo_number', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_conditions.shipment_condition_name', 'shipment_schedules.material_number', 'materials.material_description')
        ->first();

        if($flo != null){
            try {

                $connector = new WindowsPrintConnector("FLO Printer");
                $printer = new Printer($connector);

                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->feed(2);
                $printer->barcode($flo->flo_number, Printer::BARCODE_UPCA);
                $printer->setTextSize(6, 1);
                $printer->text($flo->flo_number."\n\n");
                $printer->setTextSize(6, 3);
                $printer->text(strtoupper($flo->destination_shortname."\n\n"));
                $printer->initialize();

                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setTextSize(4, 2);
                $printer->text(date('d-M-Y', strtotime($flo->st_date))."\n\n");
                $printer->text(strtoupper($flo->shipment_condition_name)."\n\n");

                $printer->initialize();
                $printer->setTextSize(2, 2);
                $printer->text("   ".strtoupper($flo->material_number)."\n");
                $printer->text("   ".strtoupper($flo->material_description)."\n");

                $printer->initialize();
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("------------------------------------");
                $printer->feed(1);
                $printer->text("|Qty:             |Qty:            |");
                $printer->feed(1);
                $printer->text("|                 |                |");
                $printer->feed(1);
                $printer->text("|                 |                |");
                $printer->feed(1);
                $printer->text("|                 |                |");
                $printer->feed(1);
                $printer->text("|Production       |Logistic        |");
                $printer->feed(1);
                $printer->text("------------------------------------");
                $printer->feed(4);
                $printer->cut();
                $printer->close();

                return back()->with('status', 'FLO has been reprinted.')->with('page', 'FLO Serial Number');
            } 
            catch(\Exception $e){
                return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
            }
        }
        else{
            return back()->with('error', 'FLO number '. $request->get('flo_number') . 'not found.');
        }        
    }

    public function destroy_serial_number(Request $request)
    {
        $flo_detail = FloDetail::find($request->get('id'));
        $flo_detail->forceDelete();

        $flo = Flo::where('flo_number', '=', $request->get('flo_number'))->first();
        $flo->actual = $flo->actual-1;
        $flo->save();

        $response = array(
            'status' => true,
            'message' => "Data has been deleted.",
        );
        return Response::json($response);
    }

    public function cancel_flo_settlement(Request $request)
    {
        $flo = Flo::where('id', '=', $request->get('id'))->first();
        $flo->status = 0;
        $flo->save();

        $response = array(
        'status' => true,
        'message' => "FLO " . $request->get('flo_number') . "settlement has been canceled.",
        );
       return Response::json($response);
    }

    public function flo_settlement(Request $request)
    {
        $flo = Flo::where('flo_number', '=', $request->get('flo_number'))->first();
        $flo->status = 1;
        $flo->save();

        $response = array(
            'status' => true,
            'message' => "FLO " . $request->get('flo_number') . "has been settled.",
        );
        return Response::json($response);
    }

}