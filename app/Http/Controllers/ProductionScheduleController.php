<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Material;
use App\Destination;
use App\ProductionSchedule;
use App\OriginGroup;
use App\FloDetail;
use DataTables;
use Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ProductionScheduleController extends Controller
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
      $production_schedules = ProductionSchedule::orderByRaw('due_date DESC', 'material_number ASC')
      ->get();

      $materials = Material::orderBy('material_number', 'ASC')->get();
      $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();

      return view('production_schedules.index', array(
        'origin_groups' => $origin_groups,
        'materials' => $materials
      ))->with('page', 'Production Schedule');
        //
    }

    public function fetchSchedule(Request $request)
    {
      $production_schedules = ProductionSchedule::leftJoin("materials","materials.material_number","=","production_schedules.material_number")
      ->leftJoin("origin_groups","origin_groups.origin_group_code","=","materials.origin_group_code")
      ->select('production_schedules.id','production_schedules.material_number','production_schedules.due_date','production_schedules.quantity','materials.material_description','origin_groups.origin_group_name')
      ->orderByRaw('due_date DESC', 'production_schedules.material_number ASC')
      ->get();

      return DataTables::of($production_schedules)
      ->addColumn('action', function($production_schedules){
        return '
        <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$production_schedules->id.')">View</button>
        <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$production_schedules->id.')">Edit</button>
        <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$production_schedules->id.',\''.$production_schedules->material_number.'\',\''.$production_schedules->due_date.'\')">Delete</button>';
      })

      ->rawColumns(['action' => 'action'])
      ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $materials = Material::orderBy('material_number', 'ASC')->get();
      return view('production_schedules.create', array(
        'materials' => $materials
      ))->with('page', 'Production Schedule');
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
      $due_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('due_date'))));
      try
      {
        $id = Auth::id();
        $production_schedule = new ProductionSchedule([
          'material_number' => $request->get('material_number'),
          'due_date' => $due_date,
          'quantity' => $request->get('quantity'),
          'created_by' => $id
        ]);

        $production_schedule->save();    

        $response = array(
          'status' => true,
          'production_schedule' => $production_schedule,
        );
        return Response::json($response);
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return redirect('/index/production_schedule')->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
        }
        else{
          return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
        }
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
      $query = "select production_schedule.material_number, production_schedule.due_date, production_schedule.quantity, users.`name`, material_description, origin_group_name, production_schedule.created_at, production_schedule.updated_at from
      (select material_number, due_date, quantity, created_by, created_at, updated_at from production_schedules where id = "
      .$request->get('id').") as production_schedule
      left join materials on materials.material_number = production_schedule.material_number
      left join origin_groups on origin_groups.origin_group_code = materials.origin_group_code
      left join users on production_schedule.created_by = users.id";

      $production_schedule = DB::select($query);

      $response = array(
        'status' => true,
        'datas' => $production_schedule
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
      // $materials = Material::orderBy('material_number', 'ASC')->get();
      $production_schedule = ProductionSchedule::find($request->get("id"));

      $response = array(
        'status' => true,
        'datas' => $production_schedule
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
      $due_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('due_date'))));

      try{
        $production_schedule = ProductionSchedule::find($request->get('id'));
        $production_schedule->quantity = $request->get('quantity');
        $production_schedule->save();

        $response = array(
          'status' => true,
          'datas' => $production_schedule
        );
        return Response::json($response);
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return redirect('/index/production_schedule')->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
        }
        else{
          return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
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
      $production_schedule = ProductionSchedule::find($request->get("id"));
      $production_schedule->forceDelete();

      $response = array(
        'status' => true
      );
      return Response::json($response);
    }

    public function destroy(Request $request){

      $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
      $date_to = date('Y-m-d', strtotime($request->get('dateto')));

      $materials = Material::whereIn('origin_group_code', $request->get('origin_group'))->select('material_number')->get();

      $production_schedule = ProductionSchedule::where('due_date', '>=', $date_from)
      ->where('due_date', '<=', $date_to)
      ->whereIn('material_number', $materials)
      ->forceDelete();

      return redirect('/index/production_schedule')
      ->with('status', 'Production schedules has been deleted.')
      ->with('page', 'Production Schedule');
    }

    /**
     * Import resource from Text File.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
      try{
        if($request->hasFile('production_schedule')){
                // ProductionSchedule::truncate();

          $id = Auth::id();

          $file = $request->file('production_schedule');
          $data = file_get_contents($file);

          $rows = explode("\r\n", $data);
          foreach ($rows as $row)
          {
            if (strlen($row) > 0) {
              $row = explode("\t", $row);
              $production_schedule = new ProductionSchedule([
                'material_number' => $row[0],
                'due_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[1]))),
                'quantity' => $row[2],
                'created_by' => $id,
              ]);

              $production_schedule->save();
            }
          }
          return redirect('/index/production_schedule')->with('status', 'New production schedule has been imported.')->with('page', 'Production Schedule');
        }
        else
        {
          return redirect('/index/production_schedule')->with('error', 'Please select a file.')->with('page', 'Production Schedule');
        }
      }

      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return back()->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
        }
        else{
          return back()->with('error', $e->getMessage())->with('page', 'Production Schedule');
        }

      }
            //
    }

    public function indexProductionData()
    {
      $periods = DB::table('shipment_schedules')->select('st_month')->distinct()->get();
      $origin_groups = DB::table('origin_groups')->get();
      $materials = Material::orderBy('material_number', 'ASC')->get();

      return view('production_schedules.data', array(
        'periods' => $periods,
        'origin_groups' => $origin_groups,
        'materials' => $materials,
        'title' => 'Production Schedule Data',
        'title_jp' => '??'
      ))->with('page', 'Production Schedule');
    }


    public function fetchProductionData(Request $request)
    {

      // PRODUCTION SCHEDULE

      $production_sch = ProductionSchedule::leftJoin("materials", "materials.material_number" ,"=" ,"production_schedules.material_number")
      ->where("due_date", ">=", "2019-09-01")
      ->where("category", "=", "FG");

      if ($request->get("dateTo")) {
        $production_sch = $production_sch->where("due_date", "<=", $request->get("dateTo"));
      }

      $production_sch = $production_sch->select("due_date", "production_schedules.material_number", "material_description", "quantity","origin_group_code")
      ->get();
      

      // ACT PACKING

      $flo = FloDetail::leftJoin("materials", "materials.material_number" ,"=" ,"flo_details.material_number")
      ->where(db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d")'), ">=", "2019-09-01")
      ->where("category", "=", "FG");
      
      if ($request->get("dateTo")) {
        $flo = $flo->where(db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d")'), "<=", $request->get("dateTo"));
      }

      $flo = $flo->select("flo_details.material_number", db::raw('sum(flo_details.quantity) as packing'), db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d") as date'))
      ->groupBy("flo_details.material_number", db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d")'))
      ->get();


      // DELIVERY

      if ($request->get("dateTo")) {
        $where = ' AND DATE_FORMAT(deliv.created_at, "%Y-%m-%d") <= "'.$request->get("dateTo").'"';
      } else {
        $where = '';
      }


      $q_deliv = 'select * from (select flomaster.flo_number, flomaster.material_number, sum(flomaster.actual) deliv, flomaster.`status`, DATE_FORMAT(deliv.created_at, "%Y-%m-%d") date from
      (select flos.flo_number, flos.material_number, actual, `status` from flos where `status` NOT IN (0,1,"m")) as flomaster left join 
      (select flo_number, created_at from flo_logs where status_code = 2) as deliv on flomaster.flo_number = deliv.flo_number
      where DATE_FORMAT(deliv.created_at, "%Y-%m-%d") >= "2019-09-01" '. $where .'
      group by flomaster.material_number, DATE_FORMAT(deliv.created_at, "%Y-%m-%d")) alls
      left join materials on materials.material_number = alls.material_number
      where category = "FG"';

      $deliv = db::select($q_deliv);

      $response = array(
        'status' => true,
        'production_sch' => $production_sch,
        'packing' => $flo,
        'deliv' => $deliv
      );
      return Response::json($response);
    }
  }
