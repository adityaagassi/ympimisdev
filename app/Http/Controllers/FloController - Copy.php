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
use Mike42\Escpos\Printer;
use DataTables;
use Yajra\DataTables\Exception;
use Response;

class FloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_sn()
    {
        $materials = Material::orderBy('material_number', 'asc')->get();
        $flos = Flo::orderBy('flo_number', 'asc')->get();
        return view('flos.flo_sn', array(
            'materials' => $materials,
            'flos' => $flos
        ))->with('page', 'FLO Serial Number');
        //
    }

    public function index_pd()
    {
        $materials = Material::orderBy('material_number', 'asc')->get();
        $flos = Flo::orderBy('flo_number', 'asc')->get();
        return view('flos.flo_pd', array(
            'materials' => $materials,
            'flos' => $flos
        ))->with('page', 'FLO Production Date');
        //
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
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> barcode($flo->flo_number);
                $printer -> text(strtoupper($flo->destination_shortname));
                $printer -> text(date('d F Y', strtotime($flo->st_date)));
                $printer -> text('By ' . strtoupper($flo->shipment_condition_name));
                $printer -> text(strtoupper($flo->material_number));
                $printer -> text(strtoupper($flo->material_description));
                $printer -> cut();
                $printer -> close();

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

    public function scan_serial_number_sn(Request $request)
    {
        try{
          $id = Auth::id();
          if (strlen($request->get('serial_number')) == 8){
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
                'message' => "FLO fulfillment success."
            );
            return Response::json($response);
        }
        else{
            $response = array(
                'status' => false,
                'message' => "Serial number does not matches."
            );
            return Response::json($response);
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

public function scan_material_number_sn(Request $request)
{
    $material_number = $request->get('material_number');
    $flo_number = $request->get('flo_number');

    $flo = DB::table('flos')
    ->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
    ->where('flos.flo_number', '=', $flo_number)
    ->where('shipment_schedules.material_number' , '=', $material_number)
    ->select('flos.quantity', 'flos.actual')
    ->first();

    if($flo != null){
        if($flo->quantity > $flo->actual){
            $response = array(
                'status' => true,
                'message' => "Material number matches."
            );
            return Response::json($response);
        }
        else{
            $response = array(
                'status' => false,
                'message' => "FLO already fulfilled."
            );
            return Response::json($response);
        }

    }
    else{
        $response = array(
            'status' => false,
            'message' => "Material number does not match with FLO."
        );
        return Response::json($response);
    }


}

public function scan_flo_number(Request $request)
{
    $flo_number = $request->get('flo_number');

    $flo = DB::table('flos')->where('flo_number', '=', $flo_number)->first();

    if ($flo == null){
        $response = array(
            'status' => false,
            'message' => "FLO number does not match."
        );
        return Response::json($response);
    }
    else{
        $response = array(
            'status' => true,
            'message' => "FLO number matches.",
        );
        return Response::json($response);
    }
}


public function index_scan_flo_number(Request $request)
{
    $flo_number = $request->get('flo_number');

    $flos = DB::table('flo_details')
    ->leftJoin('flos', 'flo_details.flo_number', '=', 'flos.flo_number')
    ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id','=', 'shipment_schedules.id')
    ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
    ->where('flo_details.flo_number', '=', $flo_number)
    ->select('shipment_schedules.material_number', 'materials.material_description', 'flo_details.serial_number', 'flo_details.id')
    ->get();

    return DataTables::of($flos)
    ->addColumn('action', function($flos){
        return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $flos->id . '"><i class="glyphicon glyphicon-trash"></i></a>';
    })
    ->make(true);

}

    /**
     * Print FLO based on weekly shipment schedule priority.
     *
     * @return \Illuminate\Http\Response
     */
    public function print_flo(Request $request)
    {

     $id = Auth::id();
     $material = Material::where('material_number', '=', $request->get('material_number'))
     ->first();

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

     if($shipment_schedule == null){
        return back()->with('error', 'There is no shipment schedule for '. $material->material_number . ' - ' . $material->material_description . ' yet.');
    }
    else{
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

            /**
            * Install the printer using USB printing support, and the "Generic / Text Only" driver,
            * then share it (you can use a firewall so that it can only be seen locally).
            * 
            * Use a WindowsPrintConnector with the share name to print.
            * 
            * Troubleshooting: Fire up a command prompt, and ensure that (if your printer is shared as
            * "Receipt Printer), the following commands work:
            * 
            *      echo "Hello World" > testfile
            *      print /D:"\\%COMPUTERNAME%\FLO Printer" testfile
            *      del testfile
            */
            try {

                $connector = new WindowsPrintConnector("FLO Printer");
                $printer = new Printer($connector);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> barcode($flo_number);
                $printer -> text(strtoupper($shipment_schedule->destination_shortname));
                $printer -> text(date('d F Y', strtotime($shipment_schedule->st_date)));
                $printer -> text('By ' . strtoupper($shipment_schedule->shipment_condition_name));
                $printer -> text(strtoupper($shipment_schedule->material_number));
                $printer -> text(strtoupper($shipment_schedule->material_description));
                $printer -> cut();
                $printer -> close();

                $flo = new Flo([
                    'flo_number' => $flo_number,
                    'shipment_schedule_id' => $shipment_schedule->id,
                    'quantity' => $shipment_schedule->flo_quantity,
                    'created_by' => $id
                ]);
                $flo->save();

                $code_generator->index = $code_generator->index+1;
                $code_generator->save();

                return back()->with('status', 'New FLO has been printed.')->with('page', 'FLO Serial Number');
            } 
            catch(\Exception $e){
                return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
            }
        }
    }

    public function destroy_serial_number_sn(Request $request)
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

}
