<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Material;
use App\CodeGenerator;
use App\Flo;
use App\FloSerialNumber;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use DataTables;

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
        return view('flos.flo_sn', array(
            'materials' => $materials
        ))->with('page', 'FLO Serial Number');
        //
    }

    public function scan_flo_number_sn(Request $request)
    {
        // if($request->ajax()){
            $flo_number = $request->get('flo_number');
            $flos = DB::table('flo_serial_numbers')
            ->leftJoin('flos', 'flo_serial_numbers.flo_number', '=', 'flos.flo_number')
            ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id','=', 'shipment_schedules.id')
            ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
            ->where('flo_serial_numbers.flo_number', '=', $flo_number)
            ->select('shipment_schedules.material_number', 'materials.material_description', 'flo_serial_numbers.serial_number')
            ->get();
            return DataTables::of($flos)->make(true);
    }

    /**
     * Print FLO based on weekly shipment schedule priority.
     *
     * @return \Illuminate\Http\Response
     */
    public function print_sn(Request $request)
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
       ->select('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'materials.material_description', 'shipment_schedules.st_date', DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_row, material_volumes.lot_row, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'))
       ->groupBy('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', 'shipment_schedules.quantity', 'material_volumes.lot_row', 'shipment_schedules.st_date', 'materials.material_description')
       ->having('flo_quantity', '>' , '0')
       ->first();

       if($shipment_schedule == null)
       {
        return redirect('/index/flo_sn')->with('error', 'There is no shipment schedule for '. $material->material_number . ' - ' . $material->material_description . ' yet.');
    }
    else
    {
        $prefix_now = date("Y").date("m");
        $code_generator = CodeGenerator::where('note','=','flo')->first();
        $material_number = $request->get('material_number');

        if ($prefix_now != $code_generator->prefix)
        {
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
            *      print /D:"\\%COMPUTERNAME%\Receipt Printer" testfile
            *      del testfile
            */
            // try {

                // $connector = new WindowsPrintConnector("FLO Printer");
                // $printer = new Printer($connector);
                // $printer -> setJustification(Printer::JUSTIFY_CENTER);
                // $printer -> barcode($flo_number);
                // $printer -> text(strtoupper($shipment_schedule->destination_shortname));
                // $printer -> text(date('d F Y', strtotime($shipment_schedule->st_date)));
                // $printer -> text('By ' . strtoupper($shipment_schedule->shipment_condition_name));
                // $printer -> text(strtoupper($shipment_schedule->material_number));
                // $printer -> text(strtoupper($shipment_schedule->material_description));
                // $printer -> cut();
                // $printer -> close();

            $flo = new Flo([
                'flo_number' => $flo_number,
                'shipment_schedule_id' => $shipment_schedule->id,
                'quantity' => $shipment_schedule->flo_quantity,
                'created_by' => $id
            ]);
            $flo->save();


            $code_generator->index = $code_generator->index+1;
            $code_generator->save();

            return redirect('/index/flo_sn')->with('status', 'New FLO has been printed.')->with('page', 'FLO Serial Number');
            // } 
            // catch(\Exception $e) 
            // {
            //     return redirect("/index/flo_sn")->with("error", "Couldn't print to this printer: " . $e->getMessage() . "\n");
            // }
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
