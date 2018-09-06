<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Material;
use App\Destination;
use App\ShipmentCondition;
use App\ShipmentSchedule;
use Illuminate\Database\QueryException;

class ShipmentScheduleController extends Controller
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
    public function index()
    {
        $shipment_schedules = ShipmentSchedule::orderByRaw('st_date DESC', 'material_number ASC')
        ->get();

        return view('shipment_schedules.index', array(
            'shipment_schedules' => $shipment_schedules
        ))->with('page', 'Shipment Schedule');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shipment_conditions = ShipmentCondition::orderBy('shipment_condition_code', 'ASC')->get();
        $materials = Material::orderBy('material_number', 'ASC')->get();
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        return view('shipment_schedules.create', array(
            'destinations' => $destinations,
            'materials' => $materials,
            'shipment_conditions' => $shipment_conditions
        ))->with('page', 'Shipment Schedule');
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
        try
        {
            $id = Auth::id();
            $st_month = date('Y-m-d', strtotime(str_replace('/','-','01/' . $request->get('st_month'))));

            $shipment_schedule = new ShipmentSchedule([
                'st_month' => $st_month,
                'sales_order' => $request->get('sales_order'),
                'shipment_condition_code' => $request->get('shipment_condition_code'),
                'destination_code' => $request->get('destination_code'),
                'material_number' => $request->get('material_number'),
                'hpl' => $request->get('hpl'),
                'bl_date' => $request->get('bl_date'),
                'st_date' => $request->get('st_date'),
                'quantity' => $request->get('quantity'),
                'created_by' => $id
            ]);

            $shipment_schedule->save();    
            return redirect('/index/shipment_schedule')->with('status', 'New shipment schedule has been created.')->with('page', 'Shipment Schedule');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Shipment schedule for preferred data already exist.')->with('page', 'Shipment Schedule');
            }
        }
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
        $shipment_schedule = ShipmentSchedule::find($id);
        $shipment_schedule->forceDelete();

        return redirect('/index/shipment_schedule')
        ->with('status', 'Shipment schedule has been deleted.')
        ->with('page', 'Shipment Schedule');
        //
    }
}
