<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\ProductionAudit;
use App\PointCheckAudit;
use App\User;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Excel;
use App\PresenceLog;
use App\Division;
use App\Department;
use App\Section;
use App\SubSection;
use App\Group;
use App\Grade;
use App\Position;
use App\CostCenter;
use App\PromotionLog;
use App\Mutationlog;
use App\HrQuestionLog;
use App\HrQuestionDetail;
use App\Employee;
use App\EmploymentLog;
use App\OrganizationStructure;
use File;
use DateTime;
use Illuminate\Support\Arr;

class ProductionAuditController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id,$productproduct,$prosesproses)
    {
        $activityList = ActivityList::find($id);
    	$productionAudit = ProductionAudit::where('activity_list_id',$id)
                ->whereHas('point_check_audit', function ($query) use($productproduct) {
                        $query->where('product','=', $productproduct);
                    })
                ->whereHas('point_check_audit', function ($query2) use($prosesproses) {
                        $query2->where('proses','=', $prosesproses);
                    })
                ->get();
        // var_dump($productionAudit);
        // $queryProduct = "select * from origin_groups";
        // $product = DB::select($queryProduct);
        // $product2 = DB::select($queryProduct);

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        // var_dump($productionAudit);

    	$data = array('production_audit' => $productionAudit,
                      'product' => $productproduct,
                      'proses' => $prosesproses,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('production_audit.index', $data
    		)->with('page', 'Production Audit');
    }

    function details($id)
    {
        $activityList = ActivityList::find($id);
        $queryProductionAudit = "select DISTINCT(proses), product from point_check_audits where activity_list_id='".$id."'";
        $productionAudit = DB::select($queryProductionAudit);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;

        $data = array('production_audit' => $productionAudit,
                      // 'product' => $product,
                      // 'proses' => $proses,
                      // 'product2' => $product2,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('production_audit.details', $data
            )->with('page', 'Production Audit');
    }

    function filter_audit(Request $request,$id,$product,$proses)
    {
        $activityList = ActivityList::find($id);

        if(strlen($request->get('date')) != null){
            $date = date('Y-m-d', strtotime($request->get('date')));
            $productionAudit = ProductionAudit::where('activity_list_id',$id)
                ->whereHas('point_check_audit', function ($query) use($product) {
                        $query->where('product','=', $product);
                    })
                ->whereHas('point_check_audit', function ($query2) use($proses) {
                        $query2->where('proses','=', $proses);
                    })
                ->where('date',$date)
                ->get();
        }
        else{
            $productionAudit = ProductionAudit::where('activity_list_id',$id)
                ->whereHas('point_check_audit', function ($query) use($product) {
                        $query->where('product','=', $product);
                    })
                ->whereHas('point_check_audit', function ($query2) use($proses) {
                        $query2->where('proses','=', $proses);
                    })
                ->get();
        }
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $data = array(
                      'product' => $product,
                      'proses' => $proses,
                      'production_audit' => $productionAudit,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('production_audit.index', $data
            )->with('page', 'Production Audit');
    }

    function show($id,$audit_id)
    {
        $activityList = ActivityList::find($id);
        $productionAudit = ProductionAudit::find($audit_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        // }
        $data = array('production_audit' => $productionAudit,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('production_audit.view', $data
            )->with('page', 'Production Audit');
    }

    public function destroy($id,$audit_id,$product,$proses)
    {
      $production_audit = ProductionAudit::find($audit_id);
      $production_audit->delete();

      return redirect('/index/production_audit/index/'.$id.'/'.$product.'/'.$proses)
        ->with('status', 'Audit has been deleted.')
        ->with('page', 'Production Audit');
        //
    }

    function create($id,$product,$proses)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;

        $queryLeaderForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group` = 'leader')
            or (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";
        $leaderForeman = DB::select($queryLeaderForeman);
        $leaderForeman2 = DB::select($queryLeaderForeman);

        // $queryProduct = "select * from origin_groups";
        // $product = DB::select($queryProduct);

        $querypic = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $pic = DB::select($querypic);

        // $queryProses = "select DISTINCT(point_check_audits.proses),point_check_audits.product from point_check_audits where point_check_audits.activity_list_id = '".$id."'";
        // $proses = DB::select($queryProses);
        $data = array('product' => $product,
                      'leaderForeman' => $leaderForeman,
                      'pic' => $pic,
                      'leaderForeman2' => $leaderForeman2,
                      'departments' => $departments,
                      'proses' => $proses,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('production_audit.create', $data
            )->with('page', 'Production Audit');
    }

    function store(Request $request,$id,$product,$proses)
    {
            $id_user = Auth::id();
            $tujuan_upload = 'data_file';
            $date = date('Y-m-d');

            $file = $request->file('file');
            $nama_file = $file->getClientOriginalName();
            $file->getClientOriginalName();
            $file->move($tujuan_upload,$file->getClientOriginalName());

            // $messages = ['required' => ':attributes tidak boleh kosong.'];
            // $this->validate($request,['point_check' => 'required',
            //     'cara_cek' => 'required',
            //     'proses' => 'required'],$messages);

            ProductionAudit::create([
                'activity_list_id' => $id,
                'point_check_audit_id' => $request->input('point_check'),
                'date' => $date,
                'foto_kondisi_aktual' => $nama_file,
                'kondisi' => $request->input('kondisi'),
                'pic' => $request->input('pic'),
                'auditor' => $request->input('auditor'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/production_audit/index/'.$id.'/'.$product.'/'.$proses)
            ->with('page', 'Production Audit')->with('status', 'New Audit has been created.');
    }

    function edit($id,$audit_id,$product,$proses)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;

        $queryLeaderForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group` = 'leader')
            or (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";
        $leaderForeman = DB::select($queryLeaderForeman);
        $leaderForeman2 = DB::select($queryLeaderForeman);

        $productionAudit = ProductionAudit::find($audit_id);

        $querypic = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $pic = DB::select($querypic);

        $queryPointCheck = "SELECT * FROM `point_check_audits` where product = '".$productionAudit->point_check_audit->product."' and proses = '".$productionAudit->point_check_audit->proses."'";
        $pointcheck = DB::select($queryPointCheck);

        $data = array('production_audit' => $productionAudit,
                      'product' => $product,
                      'pointcheck' => $pointcheck,
                      'pic' => $pic,
                      'proses' => $proses,
                      'leaderForeman' => $leaderForeman,
                      'leaderForeman2' => $leaderForeman2,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('production_audit.edit', $data
            )->with('page', 'Production Audit');
    }

    function update(Request $request,$id,$audit_id,$product,$proses)
    {
        try{
            $tujuan_upload = 'data_file';
            $date = date('Y-m-d');

            if($request->file('file') != null){
                $file = $request->file('file');
                $nama_file = $file->getClientOriginalName();
                $file->getClientOriginalName();
                $file->move($tujuan_upload,$file->getClientOriginalName());

                $production_audit = ProductionAudit::find($audit_id);
                $production_audit->activity_list_id = $id;
                $production_audit->point_check_audit_id = $request->get('point_check');
                $production_audit->date = $request->get('date');
                $production_audit->foto_kondisi_aktual = $nama_file;
                $production_audit->kondisi = $request->get('kondisi');
                $production_audit->pic = $request->get('pic');
                $production_audit->auditor = $request->get('auditor');
                $production_audit->save();
            }else{
                $production_audit = ProductionAudit::find($audit_id);
                $production_audit->activity_list_id = $id;
                $production_audit->point_check_audit_id = $request->get('point_check');
                $production_audit->date = $request->get('date');
                $production_audit->foto_kondisi_aktual = $request->get('foto_kondisi_aktual');
                $production_audit->kondisi = $request->get('kondisi');
                $production_audit->pic = $request->get('pic');
                $production_audit->auditor = $request->get('auditor');
                $production_audit->save();
            }
            return redirect('/index/production_audit/index/'.$id.'/'.$product.'/'.$proses)->with('status', 'Audit data has been updated.')->with('page', 'Production Audit');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Audit already exist.')->with('page', 'Production Audit');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Production Audit');
            }
          }
    }

    public function get_by_country(Request $request)
    {
        // abort_unless(\Gate::allows('city_access'), 401);
        $point_check = PointCheckAudit::where('proses',$request->proses);
        // if (!$request->proses) {
        //     $html = '<option value="">'.trans('global.pleaseSelect').'</option>';
        // } else {
            $html = '';
            $cities = PointCheckAudit::where('proses',$request->proses)->where('product',$request->product)->get();
            foreach ($cities as $city) {
                $html .= '<option value="'.$city->id.'">'.$city->point_check.' - '.$city->cara_cek.'</option>';
            }
        // }

        return response()->json(['html' => $html]);
    }

    function print_audit(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        if($request->get('product') != null && strlen($request->get('date')) != null && $request->get('proses') != null){
            $origin_group = $request->get('product');
            $proses = $request->get('proses');
            $date = date('Y-m-d', strtotime($request->get('date')));
            $queryProductionAudit = "select *, employees1.name as pic_name,
                    employees2.name as auditor_name
                    from production_audits 
                    join point_check_audits on point_check_audits.id = production_audits.point_check_audit_id 
                    join activity_lists on activity_lists.id =  production_audits.activity_list_id
                    join departments on departments.id =  activity_lists.department_id
                    join employees as employees1 on employees1.employee_id = production_audits.pic
                    join employees as employees2 on employees2.employee_id = production_audits.auditor
                    where production_audits.date='".$date."' 
                    and point_check_audits.product = '".$origin_group."' 
                    and point_check_audits.proses = '".$proses."' and production_audits.deleted_at is null";
            $productionAudit = DB::select($queryProductionAudit);
        }
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        foreach($productionAudit as $productAudit){
            $product = $productAudit->product;
            $proses = $productAudit->proses;
            $date_audit = $productAudit->date;
            $foreman = $productAudit->foreman;
        }
        if($productionAudit == null){
            return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
        }else{
            $data = array(
                          'proses' => $proses,
                          'product' => $product,
                          'foreman' => $foreman,
                          'date_audit' => $date_audit,
                          'production_audit' => $productionAudit,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('production_audit.print', $data
                )->with('page', 'Production Audit');
        }
    }

    function report_audit($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }
        // $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        $bulan = date('Y-m');
        return view('production_audit.report_audit',  array('title' => 'Production Audit',
            'title_jp' => 'Production Audit',
            'id' => $id,
            'departments' => $departments,
            // 'bulan' => $bulan,
        ))->with('page', 'Report Audit');
    }

    public function fetchReport(Request $request,$id)
    {
      if($request->get('tgl') != null){
        $bulan = $request->get('tgl');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select activity_list_id, count(*) as jumlah_semua, activity_name, sum(case when production_audits.kondisi = 'Good' then 1 else 0 end) as jumlah_good, sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) as jumlah_not_good from production_audits join activity_lists on activity_lists.id = production_audits.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(production_audits.date,'%Y-%m') <= '".$bulan."' GROUP BY activity_lists.activity_name, activity_list_id");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        'bulan' => $request->get("tgl")

      );

      return Response::json($response);
    }

    public function detailProductionAudit(Request $request, $id){
      $activity_name = $request->get("activity_name");
      $kondisi = $request->get("kondisi");
        $query = "select *,employees1.name as pic_name,employees2.name as auditor_name from production_audits join point_check_audits on point_check_audits.id = production_audits.point_check_audit_id join activity_lists on activity_lists.id = production_audits.activity_list_id join employees as employees1 on employees1.employee_id = production_audits.pic join employees as employees2 on employees2.employee_id = production_audits.auditor where activity_name = '".$activity_name."' and production_audits.kondisi = '".$kondisi."'";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);
    }

    function signature()
    {
        return view('production_audit.signature'
            )->with('page', 'Production Audit');
    }

    public function save_signature(Request $request)
    {
        $result = array();

          $imagedata = base64_decode( $request->get('img_data'));
          $filename = md5(date("dmYhisA"));
          //Location to where you want to created sign image
          $file_name = './data_file/sign/'.$filename.'.png';
          file_put_contents($file_name,$imagedata);
          $result['status'] = 1;
          $result['file_name'] = $file_name;
          echo json_encode($result);
          
        DB::table('signatures')->insert([
            'filename' => $filename,
            'sign1' => $filename.'.png'
        ]);
        // }

        return response()->json(['html' => $html]);
    }
}
