<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Material;
use App\Destination;
use App\ShipmentCondition;
use App\ShipmentSchedule;
use DataTables;
use Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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
            'SX'
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
        $materials = Material::orderBy('material_number', 'ASC')->get();
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        $shipment_conditions = ShipmentCondition::orderBy('shipment_condition_code', 'ASC')->get();        

        return view('shipment_schedules.index', array(
            'shipment_schedules' => $shipment_schedules,
            'materials' => $materials,
            'destinations' => $destinations,
            'shipment_conditions' => $shipment_conditions,
            'hpls' => $this->hpl
        ))->with('page', 'Shipment Schedule');
        //
    }

    public function fetchShipment()
    {
        $shipment_schedules = ShipmentSchedule::leftJoin("materials","materials.material_number","=","shipment_schedules.material_number")
        ->leftJoin("shipment_conditions","shipment_conditions.shipment_condition_code","=","shipment_schedules.shipment_condition_code")
        ->leftJoin("destinations","destinations.destination_code","=","shipment_schedules.destination_code")
        ->leftJoin("weekly_calendars","weekly_calendars.week_date","=","shipment_schedules.st_date")
        ->select('shipment_schedules.id','shipment_schedules.quantity','shipment_schedules.sales_order','materials.material_description','shipment_conditions.shipment_condition_name',"shipment_schedules.hpl","shipment_schedules.st_date","shipment_schedules.bl_date", DB::raw("DATE_FORMAT(shipment_schedules.st_month, '%b-%Y') as st_month"), "destinations.destination_shortname", "shipment_schedules.material_number", "weekly_calendars.week_name")
        ->orderByRaw('st_date DESC', 'shipment_schedules.material_number ASC')
        ->get();

        return DataTables::of($shipment_schedules)
        ->addColumn('action', function($shipment_schedules){
            return '
            <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$shipment_schedules->id.')">View</button>
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$shipment_schedules->id.')">Edit</button>';

            // --- DELETE BUTTON
            // <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$shipment_schedules->id.',\''.$shipment_schedules->material_number.'\',\''.$shipment_schedules->st_date.'\')">Delete</button>
        })

        ->rawColumns(['action' => 'action'])
        ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
                'st_date' => date('Y-m-d', strtotime(str_replace('/','-', $request->get('st_date')))),
                'bl_date' => date('Y-m-d', strtotime(str_replace('/','-', $request->get('bl_date')))),
                'quantity' => $request->get('quantity'),
                'created_by' => $id
            ]);

            $shipment_schedule->save();

            $response = array(
                'status' => true
            );

            return Response::json($response);
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                $response = array(
                    'status' => false,
                    'message' => "already exist"
                );

                return Response::json($response);
            }
            else{
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );

                return Response::json($response);
            }
        }
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

    public function view(Request $request)
    {
        $query = "select st_month, st_date, sales_order, CONCAT(shipment.material_number,' - ',material_description) material, shipment.quantity, users.`name`, material_description, CONCAT(materials.origin_group_code,' - ',origin_group_name) as origin_group, CONCAT(destinations.destination_code,' - ',destinations.destination_name) as destination, CONCAT(shipment_conditions.shipment_condition_code,' - ',shipment_conditions.shipment_condition_name) shipment_condition, bl_date, weekly_calendars.week_name, shipment.created_at, shipment.hpl, shipment.updated_at from
        (select st_month, sales_order, shipment_condition_code, destination_code, material_number, hpl, st_date, bl_date, quantity, created_by, created_at, updated_at from shipment_schedules where id = "
        .$request->get('id').") as shipment
        left join materials on materials.material_number = shipment.material_number
        left join destinations on shipment.destination_code = destinations.destination_code
        left join shipment_conditions on shipment.shipment_condition_code = shipment_conditions.shipment_condition_code
        left join weekly_calendars on shipment.st_date = weekly_calendars.week_date
        left join origin_groups on origin_groups.origin_group_code = materials.origin_group_code
        left join users on shipment.created_by = users.id";

        $shipment = DB::select($query);

        $response = array(
            'status' => true,
            'datas' => $shipment
        );

        return Response::json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchEdit(Request $request)
    {
        $shipment_schedule = ShipmentSchedule::select(db::raw("DATE_FORMAT(st_month,'%m/%Y') st_month"),"sales_order","shipment_condition_code","destination_code","material_number","hpl",db::raw("DATE_FORMAT(st_date,'%d/%m/%Y') st_date"),db::raw("DATE_FORMAT(bl_date,'%d/%m/%Y') bl_date"),"quantity")
        ->find($request->get("id"));

        $response = array(
            'status' => true,
            'datas' => $shipment_schedule
        );

        return Response::json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        try
        {
            $st_month = date('Y-m-d', strtotime(str_replace('/','-','01/' . $request->get('st_month'))));
            $shipment_schedule = ShipmentSchedule::find($request->get("id"));

            $shipment_schedule->st_month = $st_month;
            $shipment_schedule->sales_order = $request->get('sales_order');
            $shipment_schedule->shipment_condition_code = $request->get('shipment_condition_code');
            $shipment_schedule->destination_code = $request->get('destination_code');
            $shipment_schedule->material_number = $request->get('material_number');
            $shipment_schedule->hpl = $request->get('hpl');
            $shipment_schedule->st_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('st_date'))));
            $shipment_schedule->bl_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('bl_date'))));
            $shipment_schedule->quantity = $request->get('quantity');
            $shipment_schedule->save();
            
            $response = array(
                'status' => true,
                'datas' => $shipment_schedule
            );

            return Response::json($response);
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                $response = array(
                    'status' => true,
                    'datas' => "Shipment Scedule already exist"
                );

                return Response::json($response);
            }
            else{
                $response = array(
                    'status' => true,
                    'datas' => $e->getMessage()
                );

                return Response::json($response);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $shipment_schedule = ShipmentSchedule::find($id);
        $shipment_schedule->delete();

        $response = array(
            'status' => true
        );

        return Response::json($response);
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
