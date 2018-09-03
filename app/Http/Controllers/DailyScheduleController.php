<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Material;
use App\Destination;
use App\DailySchedule;
use Illuminate\Database\QueryException;

class DailyScheduleController extends Controller
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
        $daily_schedules = DailySchedule::orderByRaw('due_date DESC', 'due_date ASC')
        ->get();

        return view('daily_schedules.index', array(
            'daily_schedules' => $daily_schedules
        ))->with('page', 'Daily Schedule');
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
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        return view('daily_schedules.create', array(
            'destinations' => $destinations,
            'materials' => $materials
        ))->with('page', 'Daily Schedule');
        //
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
            $daily_schedule = new DailySchedule([
              'material_number' => $request->get('material_number'),
              'destination_code' => $request->get('destination_code'),
              'due_date' => $request->get('due_date'),
              'quantity' => $request->get('quantity'),
              'created_by' => $id
          ]);

            $daily_schedule->save();    
            return redirect('/index/daily_schedule')->with('status', 'New daily schedule has been created.')->with('page', 'Daily Schedule');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Daily for material with preferred destination and due date already exist.')->with('page', 'Daily Schedule');
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
        $materials = Material::orderBy('material_number', 'ASC')->get();
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        $daily_schedule = DailySchedule::find($id);
        return view('daily_schedules.edit', array(
            'daily_schedule' => $daily_schedule,
            'materials' => $materials,
            'destinations' => $destinations,
        ))->with('page', 'Daily Schedule');
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
        try{

            $daily_schedule = DailySchedule::find($id);
            $daily_schedule->material_number = $request->get('material_number');
            $daily_schedule->destination_code = $request->get('destination_code');
            $daily_schedule->due_date = $request->get('due_date');
            $daily_schedule->quantity = $request->get('quantity');
            $daily_schedule->save();

            return redirect('/index/daily_schedule')->with('status', 'Daily schedule data has been edited.')->with('page', 'Daily Schedule');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Daily schedule for material with preferred destination and shipment date already exist.')->with('page', 'Daily Schedule');
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
        $daily_schedule = DailySchedule::find($id);
        $daily_schedule->forceDelete();

        return redirect('/index/daily_schedule')
        ->with('status', 'Daily schedule has been deleted.')
        ->with('page', 'Daily Schedule');
        //
    }
}
