<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Material;
use App\Destination;
use App\ProductionSchedule;
use App\OriginGroup;
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
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'asc')->get();

        return view('production_schedules.index', array(
            'production_schedules' => $production_schedules,
            'origin_groups' => $origin_groups,
        ))->with('page', 'Production Schedule');
        //
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
            return redirect('/index/production_schedule')->with('status', 'New production schedule has been created.')->with('page', 'Production Schedule');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
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
        $production_schedule = ProductionSchedule::find($id);
        return view('production_schedules.show', array(
            'production_schedule' => $production_schedule,
        ))->with('page', 'Production Schedule');
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
        $materials = Material::orderBy('material_number', 'ASC')->get();
        $production_schedule = ProductionSchedule::find($id);
        return view('production_schedules.edit', array(
            'production_schedule' => $production_schedule,
            'materials' => $materials,
        ))->with('page', 'Production Schedule');
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
        $due_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('due_date'))));

        try{
            $production_schedule = ProductionSchedule::find($id);
            $production_schedule->material_number = $request->get('material_number');
            $production_schedule->due_date = $due_date;
            $production_schedule->quantity = $request->get('quantity');
            $production_schedule->save();

            return redirect('/index/production_schedule')->with('status', 'Production schedule data has been edited.')->with('page', 'Production Schedule');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
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
        $production_schedule = ProductionSchedule::find($id);
        $production_schedule->forceDelete();

        return redirect('/index/production_schedule')
        ->with('status', 'Production schedule has been deleted.')
        ->with('page', 'Production Schedule');
        //
    }

    public function delete(Request $request){

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
}
