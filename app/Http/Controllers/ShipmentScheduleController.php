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
    private $hpl;

    public function __construct()
    {
        $this->middleware('auth');
        $this->hpl = [
            'ASBELL&BOW',
            'ASBODY',
            'ASFG',
            'ASKEY',
            'ASNECK',
            'ASPAD',
            'ASPART',
            'CASE',
            'CLBARREL',
            'CLBELL',
            'CLFG',
            'CLKEY',
            'CLLOWER',
            'CLPART',
            'CLUPPER',
            'FLBODY',
            'FLFG',
            'FLFOOT',
            'FLHEAD',
            'FLKEY',
            'FLPAD',
            'FLPART',
            'MOUTHPIECE',
            'PN',
            'PN PARTS',
            'RC',
            'TSBELL&BOW',
            'TSBODY',
            'TSFG',
            'TSKEY',
            'TSNECK',
            'TSPART',
            'VENOVA',
        ];
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
        $hpls = $this->hpl;
        $shipment_conditions = ShipmentCondition::orderBy('shipment_condition_code', 'ASC')->get();
        $materials = Material::orderBy('material_number', 'ASC')->get();
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        return view('shipment_schedules.create', array(
            'destinations' => $destinations,
            'materials' => $materials,
            'shipment_conditions' => $shipment_conditions,
            'hpls' => $hpls
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
        $hpls = $this->hpl;

        $shipment_schedule = ShipmentSchedule::find($id);
        return view('shipment_schedules.show', array(
            'shipment_schedule' => $shipment_schedule,
        ))->with('page', 'Shipment Schedule');
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
        $hpls = $this->hpl;
        $shipment_conditions = ShipmentCondition::orderBy('shipment_condition_code', 'ASC')->get();
        $materials = Material::orderBy('material_number', 'ASC')->get();
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        $shipment_schedule = ShipmentSchedule::find($id);
        return view('shipment_schedules.edit', array(
            'destinations' => $destinations,
            'materials' => $materials,
            'shipment_conditions' => $shipment_conditions,
            'shipment_schedule' => $shipment_schedule,
            'hpls' => $hpls
        ))->with('page', 'Shipment Schedule');
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
        try
        {
            $id = Auth::id();
            $st_month = date('Y-m-d', strtotime(str_replace('/','-','01/' . $request->get('st_month'))));
            $shipment_schedule = ShipmentSchedule::find($id);

            $shipment_schedule->st_month = $st_month;
            $shipment_schedule->sales_order = $request->get('sales_order');
            $shipment_schedule->shipment_condition_code = $request->get('shipment_condition_code');
            $shipment_schedule->destination_code = $request->get('destination_shortname');
            $shipment_schedule->material_number = $request->get('material_number');
            $shipment_schedule->hpl = $request->get('hpl');
            $shipment_schedule->bl_date = $request->get('bl_date');
            $shipment_schedule->st_date = $request->get('st_date');
            $shipment_schedule->quantity = $request->get('quantity');
            $shipment_schedule->created_by = $id;
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shipment_schedule = ShipmentSchedule::find($id);
        $shipment_schedule->delete();

        return redirect('/index/shipment_schedule')
        ->with('status', 'Shipment schedule has been deleted.')
        ->with('page', 'Shipment Schedule');
        //
    }

    public function import(Request $request)
    {
     if($request->hasFile('shipment_schedule')){

        $id = Auth::id();

        $file = $request->file('shipment_schedule');
        $data = file_get_contents($file);

        $rows = explode("\r\n", $data);
        foreach ($rows as $row)
        {
            if (strlen($row) > 0) {
                $row = explode("\t", $row);
                $shipment_schedule = new ShipmentSchedule([
                    'st_month' => date('Y-m-d', strtotime(str_replace('/','-',$row[0]))),
                    'sales_order' => $row[1],
                    'shipment_condition_code' => $row[2],
                    'destination_code' => $row[3],
                    'material_number' => $row[4],
                    'hpl' => $row[5],
                    'st_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[6]))),
                    'bl_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[7]))),
                    'quantity' => $row[8],
                    'created_by' => $id,
                ]);

                $shipment_schedule->save();
            }
        }
        return redirect('/index/shipment_schedule')->with('status', 'New shipment schedules has been imported.')->with('page', 'Shipment Schedule');

    }
    else
    {
        return redirect('/index/shipment_schedule')->with('error', 'Please select a file.')->with('page', 'Shipment Schedule');
    }
}

}
