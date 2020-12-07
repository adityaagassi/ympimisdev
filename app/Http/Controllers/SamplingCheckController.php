<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\SamplingCheck;
use App\SamplingCheckDetail;
use App\WeeklyCalendar;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class SamplingCheckController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$samplingCheck = SamplingCheck::where('activity_list_id',$id)->orderBy('sampling_checks.id','desc')
            ->get();


    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $frequency = $activityList->frequency;
        $leader = $activityList->leader_dept;
        // var_dump($productionAudit);
        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

    	$data = array('sampling_check' => $samplingCheck,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
    				  'departments' => $departments,
                      'frequency' => $frequency,
                      'leader' => $leader,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('sampling_check.index', $data
    		)->with('page', 'Sampling Check');
    }

    function filter_sampling(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $frequency = $activityList->frequency;
        $leader = $activityList->leader_dept;
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $sub_section = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        if($request->get('subsection') != null && strlen($request->get('month')) != null){
            $subsection = $request->get('subsection');
            // $date = date('Y-m',$request->get('month'));
            // $year = date_format($date, 'Y');
            // $month = date_format($date, 'm');
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $samplingCheck = SamplingCheck::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('sampling_checks.id','desc')
                ->get();
        }
        elseif ($request->get('month') > null && $request->get('subsection') == null) {
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $samplingCheck = SamplingCheck::where('activity_list_id',$id)
                // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('sampling_checks.id','desc')
                ->get();
        }
        elseif($request->get('subsection') > null && strlen($request->get('month')) == null){
            $subsection = $request->get('subsection');
            $samplingCheck = SamplingCheck::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                ->orderBy('sampling_checks.id','desc')
                ->get();
        }
        else{
            $samplingCheck = SamplingCheck::where('activity_list_id',$id)
                ->orderBy('sampling_checks.id','desc')
                ->get();
        }
        $data = array(
                      'sampling_check' => $samplingCheck,
                      'subsection' => $sub_section,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'departments' => $departments,
                      'frequency' => $frequency,
                      'leader' => $leader,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('sampling_check.index', $data
            )->with('page', 'Sampling Check');
    }

    function show($id,$sampling_id)
    {
        $activityList = ActivityList::find($id);
        $samplingCheck = SamplingCheck::find($sampling_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        // }
        $data = array('sampling_check' => $samplingCheck,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('sampling_check.view', $data
            )->with('page', 'Sampling Check');
    }

    public function destroy($id,$sampling_id)
    {
      $samplingCheck = SamplingCheck::find($sampling_id);
      $samplingCheck->delete();

      return redirect('/index/sampling_check/index/'.$id)
        ->with('status', 'Sampling Check has been deleted.')
        ->with('page', 'Sampling Check');        
    }

    function create($id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";

        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $data = array('product' => $product,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('sampling_check.create', $data
            )->with('page', 'Training Report');
    }

    function store(Request $request,$id)
    {
            $month = date("m",strtotime($request->input('date')));
            $week = WeeklyCalendar::where('week_date',$request->get('date'))->get();
            foreach($week as $week){
                $week_name = $week->week_name;
            }
            $id_user = Auth::id();
            SamplingCheck::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'subsection' => $request->input('subsection'),
                'month' => $month,
                'date' => $request->input('date'),
                'week_name' => $week_name,
                'product' => $request->input('product'),
                'no_seri_part' => $request->input('no_seri_part'),
                'jumlah_cek' => $request->input('jumlah_cek'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/sampling_check/index/'.$id)
            ->with('page', 'Sampling Check')->with('status', 'New Sampling Check has been created.');
    }

    function edit($id,$sampling_check_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";
        $section = DB::select($querySection);

        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $sampling_check = SamplingCheck::find($sampling_check_id);

        $data = array('product' => $product,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'sampling_check' => $sampling_check,
                      'id' => $id);
        return view('sampling_check.edit', $data
            )->with('page', 'Sampling Check');
    }

    function update(Request $request,$id,$sampling_check_id)
    {
        try{
                $month = date("m",strtotime($request->get('date')));
                $week = WeeklyCalendar::where('week_date',$request->get('date'))->get();
                foreach($week as $week){
                    $week_name = $week->week_name;
                }
                $sampling_check = SamplingCheck::find($sampling_check_id);
                $sampling_check->activity_list_id = $id;
                $sampling_check->department = $request->get('department');
                $sampling_check->section = $request->get('section');
                $sampling_check->product = $request->get('product');
                $sampling_check->month = $month;
                $sampling_check->subsection = $request->get('subsection');
                $sampling_check->date = $request->get('date');
                $sampling_check->week_name = $week_name;
                $sampling_check->no_seri_part = $request->get('no_seri_part');
                $sampling_check->jumlah_cek = $request->get('jumlah_cek');
                $sampling_check->leader = $request->get('leader');
                $sampling_check->foreman = $request->get('foreman');
                $sampling_check->save();

            return redirect('/index/sampling_check/index/'.$id)->with('status', 'Sampling Check data has been updated.')->with('page', 'Sampling Check');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Sampling Check already exist.')->with('page', 'Training Report');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Sampling Check');
            }
          }
    }

    function details($sampling_id)
    {
        $samplingCheckDetails = SamplingCheckDetail::where('sampling_check_id',$sampling_id)
            ->get();

        $samplingCheck = SamplingCheck::find($sampling_id);

        $activity_name = $samplingCheck->activity_lists->activity_name;
        $departments = $samplingCheck->activity_lists->departments->department_name;
        $id_departments = $samplingCheck->activity_lists->departments->id;
        $activity_alias = $samplingCheck->activity_lists->activity_alias;
        $activity_id = $samplingCheck->activity_lists->id;
        $leader = $samplingCheck->activity_lists->leader_dept;

        $data = array('sampling_check_details' => $samplingCheckDetails,
                      'sampling_check' => $samplingCheck,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'sampling_id' => $sampling_id,
                      'leader' => $leader,
                      'activity_id' => $activity_id,
                      'id_departments' => $id_departments);
        return view('sampling_check.details', $data
            )->with('page', 'Sampling Check Details');
    }

    public function destroydetails($id,$sampling_check_details_id)
    {
      $SamplingCheckDetail = SamplingCheckDetail::find($sampling_check_details_id);
      $SamplingCheckDetail->delete();

      return redirect('/index/sampling_check/details/'.$id)
        ->with('status', 'Sampling Check Details has been deleted.')
        ->with('page', 'Sampling Check Details');
        //
    }

    function createdetails($sampling_id)
    {
        $samplingCheck = SamplingCheck::find($sampling_id);
        // var_dump($samplingCheck);
        // $activityList = ActivityList::find($id);

        $activity_name = $samplingCheck->activity_lists->activity_name;
        $departments = $samplingCheck->activity_lists->departments->department_name;
        $id_departments = $samplingCheck->activity_lists->departments->id;
        $activity_alias = $samplingCheck->activity_lists->activity_alias;

        $queryLeaderForeman = "SELECT DISTINCT
            ( employee_syncs.name ),
            employee_syncs.employee_id 
        FROM
            employee_syncs
        WHERE
            ( employee_syncs.department like '%".$departments."%' AND employee_syncs.position = 'Leader' ) 
            OR (
            employee_syncs.department like '%".$departments."%' 
            AND employee_syncs.position = 'Foreman')";
        $queryForeman = "SELECT DISTINCT
            ( employee_syncs.name ),
            employee_syncs.employee_id 
        FROM
            employee_syncs
        WHERE
            (
            employee_syncs.department like '%".$departments."%' 
            AND employee_syncs.position = 'Foreman')";

        $leaderForeman = DB::select($queryLeaderForeman);
        $foreman = DB::select($queryForeman);

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";
        $section = DB::select($querySection);

        
        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department like '%".$departments."%'";
        $operator = DB::select($queryOperator);

        $data = array(
                      'operator' => $operator,
                      'samplingCheck' => $samplingCheck,
                      'leaderForeman' => $leaderForeman,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'sampling_id' => $sampling_id);
        return view('sampling_check.createdetails', $data
            )->with('page', 'Sampling Check');
    }

    function storedetails(Request $request,$sampling_id)
    {
            // $month = date("m",strtotime($request->input('date')));
            $tujuan_upload = 'data_file/sampling_check';
            $date = date('Y-m-d');

            $file = $request->file('file');
            $nama_file = $file->getClientOriginalName();
            $file->getClientOriginalName();
            $file->move($tujuan_upload,$file->getClientOriginalName());

            $id_user = Auth::id();
            SamplingCheckDetail::create([
                'sampling_check_id' => $sampling_id,
                'point_check' => $request->input('point_check'),
                'hasil_check' => $request->input('hasil_check'),
                'picture_check' => $nama_file,
                'pic_check' => $request->input('pic_check'),
                'sampling_by' => $request->input('sampling_by'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/sampling_check/details/'.$sampling_id)
            ->with('page', 'Sampling Check')->with('status', 'New Sampling Check Details has been created.');
    }

    function editdetails($id,$sampling_check_details_id)
    {
        $sampling_check = SamplingCheck::find($id);
        // var_dump($samplingCheck);
        // $activityList = ActivityList::find($id);

        $activity_name = $sampling_check->activity_lists->activity_name;
        $departments = $sampling_check->activity_lists->departments->department_name;
        $id_departments = $sampling_check->activity_lists->departments->id;
        $activity_alias = $sampling_check->activity_lists->activity_alias;

        $queryLeaderForeman = "SELECT DISTINCT
            ( employee_syncs.name ),
            employee_syncs.employee_id 
        FROM
            employee_syncs
        WHERE
            ( employee_syncs.department like '%".$departments."%' AND employee_syncs.position = 'Leader' ) 
            OR (
            employee_syncs.department like '%".$departments."%' 
            AND employee_syncs.position = 'Foreman')";
        $queryForeman = "SELECT DISTINCT
            ( employee_syncs.name ),
            employee_syncs.employee_id 
        FROM
            employee_syncs
        WHERE
            (
            employee_syncs.department like '%".$departments."%' 
            AND employee_syncs.position = 'Foreman')";

        $leaderForeman = DB::select($queryLeaderForeman);
        $foreman = DB::select($queryForeman);

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";
        $section = DB::select($querySection);

        
        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department like '%".$departments."%'";
        $operator = DB::select($queryOperator);

        $samplingCheckDetails = SamplingCheckDetail::find($sampling_check_details_id);

        $data = array(
                      'leaderForeman' => $leaderForeman,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'operator' => $operator,
                      'section' => $section,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'sampling_check' => $sampling_check,
                      'sampling_check_details' => $samplingCheckDetails,
                      'sampling_check_details_id' => $sampling_check_details_id,
                      'id' => $id);
        return view('sampling_check.editdetails', $data
            )->with('page', 'Sampling Check');
    }

    function updatedetails(Request $request,$id,$sampling_check_details_id)
    {
        try{
            $tujuan_upload = 'data_file/sampling_check';
            $date = date('Y-m-d');

            if ($request->file('file') != null) {
                $file = $request->file('file');
                $nama_file = $file->getClientOriginalName();
                $file->getClientOriginalName();
                $file->move($tujuan_upload,$file->getClientOriginalName());

                $sampling_check_details = SamplingCheckDetail::find($sampling_check_details_id);
                $sampling_check_details->sampling_check_id = $id;
                $sampling_check_details->point_check = $request->get('point_check');
                $sampling_check_details->hasil_check = $request->get('hasil_check');
                $sampling_check_details->picture_check = $nama_file;
                $sampling_check_details->pic_check = $request->get('pic_check');
                $sampling_check_details->sampling_by = $request->get('sampling_by');
                $sampling_check_details->save();
            }
            else{
                $sampling_check_details = SamplingCheckDetail::find($sampling_check_details_id);
                $sampling_check_details->sampling_check_id = $id;
                $sampling_check_details->point_check = $request->get('point_check');
                $sampling_check_details->hasil_check = $request->get('hasil_check');
                $sampling_check_details->picture_check = $request->get('picture_check');;
                $sampling_check_details->pic_check = $request->get('pic_check');
                $sampling_check_details->sampling_by = $request->get('sampling_by');
                $sampling_check_details->save();
            }

            return redirect('/index/sampling_check/details/'.$id)->with('status', 'Sampling Check Details has been updated.')->with('page', 'Sampling Check');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Sampling Check Details already exist.')->with('page', 'Training Report');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Sampling Check');
            }
          }
    }

    function report_sampling_check($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }
        // $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        $bulan = date('Y-m');
        return view('sampling_check.report_sampling_check',  array('title' => 'Report Sampling Check',
            'title_jp' => 'Report Sampling Check',
            'id' => $id,
            'departments' => $departments,
            // 'bulan' => $bulan,
        ))->with('page', 'Report Sampling Check');
    }

    public function fetchReport(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select week_date, count(*) as jumlah_sampling_check from weekly_calendars join sampling_checks on sampling_checks.date = weekly_calendars.week_date join activity_lists on activity_lists.id = sampling_checks.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."' and sampling_checks.deleted_at is null GROUP BY week_date");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        // 'bulan' => $request->get("tgl")

      );

      return Response::json($response);
    }

    public function detail_sampling_check(Request $request, $id){
      $week_date = $request->get("week_date");
        $query = "select *,CONCAT(activity_lists.id, '/', sampling_checks.subsection, '/', DATE_FORMAT(sampling_checks.date,'%Y-%m')) as linkurl, sampling_checks.id as sampling_check_id from sampling_checks join activity_lists on activity_lists.id = sampling_checks.activity_list_id where department_id = '".$id."' and activity_type = 'Sampling Check' and date = '".$week_date."' and sampling_checks.deleted_at is null";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);

    }

    function print_sampling($id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($month != null){
            $querySamplingCheck = "select *, sampling_checks.id as id_sampling_check
                from sampling_checks
                join activity_lists on activity_lists.id = sampling_checks.activity_list_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."' 
                and sampling_checks.deleted_at is null";
            $samplingCheck = DB::select($querySamplingCheck);
            $samplingCheck2 = DB::select($querySamplingCheck);
        }
        $jml_null = 0;
        foreach($samplingCheck2 as $samplingCheck2){
            $date = $samplingCheck2->date;
            $foreman = $samplingCheck2->foreman;
            $section = $samplingCheck2->section;
            $subsection = $samplingCheck2->subsection;
            $leader = $samplingCheck2->leader;
            if ($samplingCheck2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $samplingCheck2->approved_date;

            $sampling_point[] = DB::select("select * from sampling_check_details where sampling_check_id = '".$samplingCheck2->id_sampling_check."'");
            $sampling_point_count[] = count($sampling_point);
        }
        if($samplingCheck == null){
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{

            $monthTitle = date("F Y", strtotime($month));

             $pdf = \App::make('dompdf.wrapper');
           $pdf->getDomPDF()->set_option("enable_php", true);
           $pdf->setPaper('A4', 'landscape');

           $pdf->loadView('sampling_check.print', array(
               'subsection' => $subsection,
              'month' => $month,
              'leader' => $leader,
              'foreman' => $foreman,
              'section' => $section,
              'subsection' => $subsection,
              'date' => $date,
              'jml_null' => $jml_null,
              'monthTitle' => $monthTitle,
              'approved_date' => $approved_date,
              'samplingCheck' => $samplingCheck,
              'departments' => $departments,
              'activity_name' => $activity_name,
              'sampling_point' => $sampling_point,
              'sampling_point_count' => $sampling_point_count,
              'activity_alias' => $activity_alias,
              'id' => $id,
              'id_departments' => $id_departments
           ));

           return $pdf->stream("Sampling Check ".$leader." (".$monthTitle.").pdf");
        }
    }

    function print_sampling_email($id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($month != null){
            $querySamplingCheck = "select *, sampling_checks.id as id_sampling_check
                from sampling_checks
                join activity_lists on activity_lists.id = sampling_checks.activity_list_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."' 
                and sampling_checks.deleted_at is null";
            $samplingCheck = DB::select($querySamplingCheck);
            $samplingCheck2 = DB::select($querySamplingCheck);
        }
        $jml_null = 0;
        foreach($samplingCheck2 as $samplingCheck2){
            $date = $samplingCheck2->date;
            $foreman = $samplingCheck2->foreman;
            $section = $samplingCheck2->section;
            $subsection = $samplingCheck2->subsection;
            $leader = $samplingCheck2->leader;
            if ($samplingCheck2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $samplingCheck2->approved_date;
        }
        $monthTitle = date("F Y", strtotime($month));
        if($samplingCheck == null){
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'month' => $month,
                          'monthTitle' => $monthTitle,
                          'leader' => $leader,
                          'role_code' => Auth::user()->role_code,
                          'foreman' => $foreman,
                          'section' => $section,
                          'jml_null' => $jml_null,
                          'approved_date' => $approved_date,
                          'date' => $date,
                          'samplingCheck' => $samplingCheck,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('sampling_check.print_email', $data
                )->with('page', 'Sampling Check');
        }
    }

    function print_sampling_chart($id,$subsection,$month)
    {
        $activityList = ActivityList::find($id);
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($subsection != null && $month != null){
            $querySamplingCheck = "select *, sampling_checks.id as id_sampling_check
                from sampling_checks
                join activity_lists on activity_lists.id = sampling_checks.activity_list_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and sampling_checks.subsection = '".$subsection."' 
                and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."' 
                and sampling_checks.deleted_at is null";
            $samplingCheck = DB::select($querySamplingCheck);
            $samplingCheck2 = DB::select($querySamplingCheck);
        }
        // var_dump($subsection);
        $jml_null = 0;
        foreach($samplingCheck2 as $samplingCheck2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $samplingCheck2->date;
            $foreman = $samplingCheck2->foreman;
            $section = $samplingCheck2->section;
            $subsection = $samplingCheck2->subsection;
            $month = $samplingCheck2->month;
            $leader = $samplingCheck2->leader;
            if ($samplingCheck2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $samplingCheck2->approved_date;
        }
        if($samplingCheck == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'month' => $month,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'section' => $section,
                          'jml_null' => $jml_null,
                          'approved_date' => $approved_date,
                          'subsection' => $subsection,
                          'month' => $month,
                          'date' => $date,
                          'samplingCheck' => $samplingCheck,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('sampling_check.print_chart', $data
                )->with('page', 'Sampling Check');
        }
    }

    public function sendemail(Request $request,$id)
      {
          $subsection = $request->get('subsection');
          $month = $request->get('month');
          // $date = date('Y-m-d', strtotime($request->get('date')));
          $query_sampling_check = "select *, sampling_checks.id as id_sampling_check
            from sampling_checks
            join activity_lists on activity_lists.id =  sampling_checks.activity_list_id
            join departments on departments.id =  activity_lists.department_id
            where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
            and subsection = '".$subsection."'
            and activity_list_id = '".$id."'
            and sampling_checks.deleted_at is null";
          $sampling_check = DB::select($query_sampling_check);
          $sampling_check2 = DB::select($query_sampling_check);
          $sampling_check3 = DB::select($query_sampling_check);

          // var_dump($sampling_check2);

          if($sampling_check2 != null){
            foreach($sampling_check2 as $sampling_check2){
                $foreman = $sampling_check2->foreman;
                $id_sampling_check = $sampling_check2->id_sampling_check;
                $send_status = $sampling_check2->send_status;
              }

              foreach ($sampling_check3 as $sampling_check3) {
                    $scheck = SamplingCheck::find($sampling_check3->id_sampling_check);
                    $scheck->send_status = "Sent";
                    $scheck->send_date = date('Y-m-d');
                    $scheck->save();
              }

              $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
              $email = DB::select($queryEmail);
              foreach($email as $email){
                $mail_to = $email->email;            
              }
          }else{
            return redirect('/index/sampling_check/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Sampling Check');
          }

          if($send_status == "Sent"){
            return redirect('/index/sampling_check/index/'.$id)->with('error', 'Data pernah dikirim.')->with('page', 'Sampling Check');
          }
          elseif($sampling_check != null){
              Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($sampling_check, 'sampling_check'));
              return redirect('/index/sampling_check/index/'.$id)->with('status', 'Your E-mail has been sent.')->with('page', 'Sampling Check');
          }
          else{
            return redirect('/index/sampling_check/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Sampling Check');
          }
      }

      public function approval(Request $request,$id,$month)
      {
          $approve = $request->get('approve');
          if(count($approve) > 0){
            foreach($approve as $approve){
                $sampling_check = SamplingCheck::find($approve);
                $month = substr($sampling_check->date,0,7);
                $date = $sampling_check->date;
                $sampling_check->approval = "Approved";
                $sampling_check->approved_date = date('Y-m-d');
                $sampling_check->save();
              }
              return redirect('/index/sampling_check/print_sampling_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'Sampling Check');
          }
          else{
            return redirect('/index/sampling_check/print_sampling_email/'.$id.'/'.$month)->with('error', 'Not Approved.')->with('page', 'Sampling Check');
          }
      }
}