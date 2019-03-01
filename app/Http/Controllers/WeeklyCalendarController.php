<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\WeeklyCalendar;
use App\User;
use Illuminate\Support\Facades\DB;

class WeeklyCalendarController extends Controller
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
        // $weekly_calendars = WeeklyCalendar::orderBy('week_name', 'ASC')
        // ->get();

        // echo $weekly_calendars;

        $query = "select A.fiscal_year, B.week_name, date_from, date_to from

        (SELECT fiscal_year, week_name, min(week_date) as date_from FROM `weekly_calendars` group by fiscal_year, week_name) as A,

        (SELECT fiscal_year, week_name, max(week_date) as date_to FROM `weekly_calendars` group by fiscal_year, week_name) AS B

        where A.fiscal_year = B.fiscal_year and A.week_name = B.week_name order by date_to desc";


        $weekly_calendars = DB::select($query);

        return view('weekly_calendars.index', array(
            'weekly_calendars' => $weekly_calendars
        ))->with('page', 'Weekly Calendar');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('weekly_calendars.create')->with('page', 'Weekly Calendar');
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
        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('date_from').'-1 day')));
        $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('date_from'))));
        $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('date_to'))));

        $datediff = ((strtotime($dateTo)-strtotime($dateFrom))/86400)+1;
        $id = Auth::id();
        try
        {
            for ($i=0; $i < $datediff ; $i++) {

                $date_from = date('Y-m-d', strtotime($date_from.'+1 day'));

                $weekly_calendar = new WeeklyCalendar([
                    'fiscal_year' => $request->get('fiscal_year'),
                    'week_name' => $request->get('week_name'),
                    'week_date' => $date_from,
                    'created_by' => $id
                ]);
                $weekly_calendar->save();
            }

            return redirect('/index/weekly_calendar')
            ->with('status', 'New weekly calendar has been created.')
            ->with('page', 'Weekly Calendar');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()
                ->with('error', 'Weekly calendar for preferred fiscal year, week name and date already exist.')
                ->with('page', 'Weekly Calendar');
            }
            else{
                return back()
                ->with('error', $e->getMessage())
                ->with('page', 'Weekly Calendar');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($week_name, $fiscal_year)
    {
        $query = "select A.fiscal_year, A.week_name, date_from, date_to, A.created_by, A.created_at, A.updated_at from

        (SELECT fiscal_year, week_name, min(week_date) as date_from, max(created_by) as created_by, max(created_at) as created_at, max(updated_at) as updated_at FROM `weekly_calendars` group by fiscal_year, week_name) as A,

        (SELECT fiscal_year, week_name, max(week_date) as date_to, max(created_by) as created_by, max(created_at) as created_at, max(updated_at) as updated_at FROM `weekly_calendars` group by fiscal_year, week_name) AS B

        where A.fiscal_year = B.fiscal_year and A.week_name = B.week_name and A.week_name = :week_name and A.fiscal_year = :fiscal_year";


        $weekly_calendar = DB::select($query, ['week_name' => $week_name, 'fiscal_year' => $fiscal_year])[0];

        $user = User::find($weekly_calendar->created_by);

        return view('weekly_calendars.show', array(
            'weekly_calendar' => $weekly_calendar,
            'user' => $user
        ))
        ->with('page', 'Weekly Calendar');
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($week_name, $fiscal_year)
    {
        $query = "select A.fiscal_year, B.week_name, date_from, date_to from

        (SELECT fiscal_year, week_name, min(week_date) as date_from FROM `weekly_calendars` group by fiscal_year, week_name) as A,

        (SELECT fiscal_year, week_name, max(week_date) as date_to FROM `weekly_calendars` group by fiscal_year, week_name) AS B

        where A.fiscal_year = B.fiscal_year and A.week_name = B.week_name and A.week_name = :week_name and A.fiscal_year = :fiscal_year";


        $weekly_calendar = DB::select($query, ['week_name' => $week_name, 'fiscal_year' => $fiscal_year])[0];

        // $weekly_calendar = json_decode(json_encode($weekly_calendar), true);



        // echo $weekly_calendar;
        // dd($weekly_calendar);

        return view('weekly_calendars.edit', array(
            'weekly_calendar' => $weekly_calendar
        ))
        ->with('page', 'Weekly Calendar');

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $week_name, $fiscal_year)
    {
        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('date_from').'-1 day')));
        $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('date_from'))));
        $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('date_to'))));

        $datediff = ((strtotime($dateTo)-strtotime($dateFrom))/86400)+1;

        $id = Auth::id();

        try{

            $weekly_calendar = WeeklyCalendar::where('week_name', '=', $week_name)
            ->where('fiscal_year', '=', $fiscal_year);

            $weekly_calendar->forceDelete();

            for ($i=0; $i < $datediff ; $i++) { 

                $date_from = date('Y-m-d', strtotime($date_from.'+1 day'));

                $weekly_calendar = new WeeklyCalendar([
                    'fiscal_year' => $request->get('fiscal_year'),
                    'week_name' => $request->get('week_name'),
                    'week_date' => $date_from,
                    'created_by' => $id
                ]);

                $weekly_calendar->save();
            }

            return redirect('/index/weekly_calendar')
            ->with('status', 'Weekly calendar data has been edited.')
            ->with('page', 'Weekly Calendar');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()
                ->with('error', 'Weekly calendar for preferred fiscal year, week name and date already exist.')
                ->with('page', 'Weekly Calendar');
            }
            else{
                return back()
                ->with('error', $e->getMessage())
                ->with('page', 'Weekly Calendar');
            }
        }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($week_name, $fiscal_year)
    {

        $weekly_calendar = WeeklyCalendar::where('week_name', '=', $week_name)
        ->where('fiscal_year', '=', $fiscal_year);

        $weekly_calendar->forceDelete();

        return redirect('/index/weekly_calendar')
        ->with('status', 'Weekly calendar has been deleted.')
        ->with('page', 'Weekly Calendar');

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
        try{
            if($request->hasFile('weekly_calendar')){
                $id = Auth::id();
                $file = $request->file('weekly_calendar');
                $data = file_get_contents($file);
                $rows = explode("\r\n", $data);
                foreach ($rows as $row)
                {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);
                        $date_from = date('Y-m-d', strtotime(str_replace('/','-',$row[2]).'-1 day'));
                        $datediff = ((strtotime(str_replace('/','-',$row[3]))-strtotime(str_replace('/','-',$row[2])))/86400)+1;

                        for ($i=0; $i < $datediff ; $i++) 
                        { 
                            $date_from = date('Y-m-d', strtotime($date_from.'+1 day'));

                            $weekly_calendar = new WeeklyCalendar([
                              'fiscal_year' => $row[0],
                              'week_name' => $row[1],
                              'week_date' => $date_from,
                              'created_by' => $id
                          ]);
                            $weekly_calendar->save();
                        }
                    }
                }   

                return redirect('/index/weekly_calendar')->with('status', 'New weekly calendar has been imported.')->with('page', 'Weekly Calendar');   
            }
            else
            {
                return redirect('/index/weekly_calendar')->with('error', 'Please select a file.')->with('page', 'Weekly Calendar');
            }

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Weekly calendar with preferred fiscal year, week name and date already exist.')->with('page', 'Weekly Calendar');
            }

        }
        //
    }

}
