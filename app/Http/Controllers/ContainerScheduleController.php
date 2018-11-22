<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\ContainerSchedule;
use App\Container;
use App\Destination;
use App\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ContainerScheduleController extends Controller
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
        $container_schedules = ContainerSchedule::OrderBy('id', 'asc')
        ->get();

        $tes = DB::table('container_schedules')->get();

        return view('container_schedules.index', array(
            'container_schedules' => $container_schedules
        ))->with('page', 'Container Schedule');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $containers = Container::orderBy('container_code', 'ASC')->get();
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        return view('container_schedules.create', array(
            'destinations' => $destinations,
            'containers' => $containers
        ))->with('page', 'Container Schedule');
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
            $code_generator = CodeGenerator::where('note', '=', 'container')->first();
            $prefix_now = date("Y").date("m");

            if ($prefix_now != $code_generator->prefix){
                $code_generator->prefix = $prefix_now;
                $code_generator->index = '0';
                $code_generator->save();
            }

            $number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index);
            $container_id = $code_generator->prefix . $number+1;

            $code_generator->index = $code_generator->index+1;
            $code_generator->save();

            $id = Auth::id();
            $container_schedule = new ContainerSchedule([
                'container_id' => $container_id,
                'container_code' => $request->get('container_code'),
                'destination_code' => $request->get('destination_code'),
                'shipment_date' => date('Y-m-d', strtotime(str_replace('/','-', $request->get('shipment_date')))),
                'created_by' => $id
            ]);

            $container_schedule->save();
            return redirect('/index/container_schedule')->with('status', 'New container schedule has been created.')->with('page', 'Container Schedule');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Container ID from system is invalid.')->with('page', 'Container Schedule');
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
        $container_schedule = ContainerSchedule::find($id);
        return view('container_schedules.show', array(
            'container_schedule' => $container_schedule,
        ))->with('page', 'Container Schedule');
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
        $containers = Container::orderBy('container_code', 'ASC')->get();
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        $container_schedule = ContainerSchedule::find($id);
        return view('container_schedules.edit', array(
            'container_schedule' => $container_schedule,
            'containers' => $containers,
            'destinations' => $destinations,
        ))->with('page', 'Container Schedule');
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

            $container_schedule = ContainerSchedule::find($id);
            $container_schedule->container_code = $request->get('container_code');
            $container_schedule->destination_code = $request->get('destination_code');
            $container_schedule->shipment_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('shipment_date'))));
            $container_schedule->save();

            return redirect('/index/container_schedule')->with('status', 'Container schedule data has been edited.')->with('page', 'Container Schedule');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Container schedule with preferred destination and shipment date already exist.')->with('page', 'Container Schedule');
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
        $container_schedule = ContainerSchedule::find($id);
        $container_schedule->forceDelete();

        return redirect('/index/container_schedule')
        ->with('status', 'Container Schedule has been deleted.')
        ->with('page', 'Container Schedule');
        //
    }

    /**
     * Import resource from Text File.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $code_generator2 = CodeGenerator::where('note', '=', 'container')->first();
        $prefix_now = date("Y").date("m");

        if ($prefix_now != $code_generator2->prefix){
            $code_generator2->prefix = $prefix_now;
            $code_generator2->index = '0';
            $code_generator2->save();
        }

        try{
            if($request->hasFile('container_schedule')){
                // ContainerSchedule::truncate();

                $id = Auth::id();

                $file = $request->file('container_schedule');
                $data = file_get_contents($file);

                $rows = explode("\r\n", $data);
                foreach ($rows as $row)
                {
                    if (strlen($row) > 0) {

                        $code_generator = CodeGenerator::where('note', '=', 'container')->first();

                        $number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index);
                        $container_id = $code_generator->prefix . $number+1;

                        $code_generator->index = $code_generator->index+1;
                        $code_generator->save();

                        $row = explode("\t", $row);
                        $container_schedule = new ContainerSchedule([
                            'container_id' => $container_id,
                            'container_code' => $row[0],
                            'destination_code' => $row[1],
                            'shipment_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[2]))),
                            'created_by' => $id,
                        ]);

                        $container_schedule->save();
                    }
                }
                return redirect('/index/container_schedule')->with('status', 'New container schedule has been imported.')->with('page', 'Container Schedule');

            }
            else
            {
                return redirect('/index/container_schedule')->with('error', 'Please select a file.')->with('page', 'Container Schedule');
            }
        }
        
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Container with preferred destination and shipment date already exist.')->with('page', 'Container Schedule');
            }

        }
            //
    }
}
