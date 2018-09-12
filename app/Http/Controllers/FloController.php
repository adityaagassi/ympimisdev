<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Material;
use App\CodeGenerator;
use App\Flo;
use Illuminate\Support\Facades\DB;

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

    /**
     * Print FLO based on weekly shipment schedule priority.
     *
     * @return \Illuminate\Http\Response
     */
    public function print_sn(Request $request)
    {

        $shipment_schedule = DB::table('shipment_schedules')
        ->leftJoin('flos', 'shipment_schedules.id' , '=', 'flos.shipment_schedule_id')
        ->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
        ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
        ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
        ->where('shipment_schedules.material_number', '=' , $request->get('material_number'))
        ->orderBy('shipment_schedules.st_date', 'asc')
        ->select('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_row, material_volumes.lot_row, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'))
        ->groupBy('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', 'shipment_schedules.quantity', 'material_volumes.lot_row', 'shipment_schedules.st_date')
        ->having('flo_quantity', '>' , '0')
        ->take(1)
        ->get();


        if($shipment_schedule->isEmpty())
        {
            return redirect('/index/flo_sn')->with('error', 'There is not shipment schedule for . "$material_number" . yet');
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

            print $shipment_schedule. '<br>' .$flo_number;
        }
        
        //
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
