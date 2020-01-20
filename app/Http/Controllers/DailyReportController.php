<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\DailyReport;
use App\DailyReportAttachment;
use File;
use DataTables;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Response;


class DailyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->cat = [
            'Network Maintenance',
            'Hardware Maintenance',
            'Hardware Installation',
            'Software Installation',
            'Design',
            'System Analysis',
            'System Programming',
            'Bug & Error',
            'Trial & Training'

        ];

        $this->loc = [
            'Management Information System',
            'Accounting',
            'Assembly (WI-A)',
            'Educational Instrument (EI)',
            'General Affairs',
            'Human Resources',
            'Logistic',
            'Maintenance',
            'Parts Process (WI-PP)',
            'Procurement',
            'Production Control',
            'Production Engineering',
            'Purchasing',
            'Quality Assurance',
            'Welding-Surface Treatment (WI-WST)'
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     $cat = $this->cat;
     $loc = $this->loc;
     return view('daily_reports.index', array(
        'cat' => $cat,
        'loc' => $loc,
        
    ))->with('page', 'Daily Report');
        //
 }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $id = Auth::id();

        $code_generator = CodeGenerator::where('note','=','report')->first();
        $number = sprintf("%'.0" . $code_generator->length . "d\n" , $code_generator->index);
        $code = $number+1;
        $number1 = sprintf("%'.0" . $code_generator->length . "d" , $code);
        $lop = $request->get('lop');
        try{
            for ($i=1; $i <= $lop ; $i++) {
                $description = "description".$i;
                $duration = "duration".$i;

                $data = new DailyReport([
                    'report_code' => $code_generator->prefix . $number1,
                    'category' => $request->get('category'),
                    'description' => $request->get($description),
                    'location' => $request->get('location'),
                    'duration' => $request->get($duration),
                    'begin_date' => $request->get('datebegin'),
                    'target_date' => $request->get('datetarget'),
                    'finished_date' => $request->get('datefinished'), 
                    'created_by' => $id
                ]);
                $data->save();
            }
            if($request->hasFile('reportAttachment')){
                $files = $request->file('reportAttachment');
                foreach ($files as $file) 
                {
                    $number= $code_generator->prefix . $number1;
                    $data = file_get_contents($file);
                    $photo_number = $number . $file->getClientOriginalName() ;
                    $ext = $file->getClientOriginalExtension();
                    $filepath = public_path() . "/uploads/dailyreports/" . $photo_number;
                    $attachment = new DailyReportAttachment([
                        'report_code' => $code_generator->prefix . $number1,
                        'file_name' =>  $photo_number,
                        'file_path' => "/uploads/dailyreports/",
                        'created_by' => $id,
                    ]);
                    $attachment->save();
                    File::put($filepath, $data);
                }
            }

            $code_generator->index = $code_generator->index+1;
            $code_generator->save();
            return redirect('/index/daily_report')->with('status', 'Crete daily report success')->with('page', 'Daily Report');
        }
        catch (QueryException $e){
            return redirect('/index/daily_report')->with('error', $e->getMessage())->with('page', 'Daily Report');
        }
    }

    public function fetchDailyReport(){
        $daily_reports = DailyReport::leftJoin('users', 'users.id', '=', 'daily_reports.created_by')
        ->leftJoin('roles', 'users.role_code', '=', 'roles.role_code')
        ->leftJoin(db::raw('(select report_code, count(file_name) as att from daily_report_attachments group by report_code) as daily_report_attachments'), 'daily_report_attachments.report_code', '=', 'daily_reports.report_code')
        ->select('roles.role_code', 'users.name', 'daily_reports.category', 'daily_reports.description', 'daily_reports.location', 'daily_reports.begin_date', 'daily_reports.target_date', 'daily_reports.finished_date', 'daily_reports.report_code', 'daily_report_attachments.att', db::raw('concat(round(time_to_sec(daily_reports.duration)/60, 0), " Min") as duration'))
        ->distinct()
        ->orderByRaw('daily_reports.begin_date desc, users.name asc')
        ->limit(500)
        ->get();

        return DataTables::of($daily_reports)
        ->addColumn('action', function($daily_reports){
            return '<a href="javascript:void(0)" class="btn btn-xs btn-info" onClick="detailReport(id)" id="' . $daily_reports->report_code . '">Details</a>';
        })
        ->addColumn('attach', function($daily_reports){
            if($daily_reports->att > 0){
                return '<a href="javascript:void(0)" id="' . $daily_reports->report_code . '" onClick="downloadAtt(id)" class="fa fa-paperclip"> ' . $daily_reports->att . '</a>';
            }
            else{
                return '-';
            }
        })
        ->addColumn('action', function($daily_reports){
            return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editReport(id)" id="' . $daily_reports->report_code . '"><i class="fa fa-edit"></i></a>';
        })
        ->rawColumns(['action' => 'action', 'attach' => 'attach'])
        ->make(true);
    }

    public function fetchDailyReportDetail(Request $request){
        $daily_reports = DailyReport::where('report_code', '=', $request->get('report_code'))->select('description', 'duration')->get();

        $response = array(
            'status' => true,
            'daily_reports' => $daily_reports,
        );
        return Response::json($response);
    }

    public function downloadDailyReport(Request $request){
        $report_attachments = DailyReportAttachment::where('report_code', '=', $request->get('report_code'))->get();

        $zip = new ZipArchive();
        $zip_name = $request->get('report_code').".zip";
        $zip_path = public_path() . '/' . $zip_name;
        File::delete($zip_path);
        $zip->open($zip_name, ZipArchive::CREATE);

        foreach ($report_attachments as $report_attachment) {
            $file_path = public_path() . $report_attachment->file_path . $report_attachment->file_name;
            $file_name = $report_attachment->file_name;
            $zip->addFile($file_path, $file_name);
        }
        $zip->close();

        $path = asset($zip_name);

        $response = array(
            'status' => true,
            'file_path' => $path,
        );
        return Response::json($response);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $daily_reports = DailyReport::where('report_code', '=', $request->get('report_code'))->get();
        $daily_reportsHead = DailyReport::where('report_code', '=', $request->get('report_code'))->select('report_code','category','location','begin_date','finished_date','target_date')->distinct()->get();
        $response = array(
            'status' => true,
            'daily_reports' => $daily_reports,
            'daily_reportsHead' => $daily_reportsHead,
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
    public function update(Request $request)
    {
        try{
         $id_user = Auth::id();
         $ids = $request->get('report_id');
         $lop = $request->get('lop2');
         if($ids != null){
            foreach ($ids as $id) 
            {
                $description = "description".$id;
                $duration = "duration".$id;
                $head = DailyReport::where('id','=', $id)
                ->withTrashed()       
                ->first();
                $head->category = $request->get('category');
                $head->location = $request->get('location');
                $head->begin_date = $request->get('begindate');
                $head->target_date = $request->get('targetdate');
                $head->finished_date = $request->get('finisheddate');
                $head->description = $request->get($description);
                $head->duration = $request->get($duration);
                $head->save();
            }
        }
        else{
            return redirect('/index/daily_report')->with('error', 'All report details must be filled.')->with('page', 'Daily Report');  
        }

        for ($i=2; $i <= $lop ; $i++) {
            $description = "description".$i;
            $duration = "duration".$i;

            $data = new DailyReport([
                'report_code' => $request->get('report_code'),
                'category' => $request->get('category'),
                'description' => $request->get($description),
                'location' => $request->get('location'),
                'duration' => $request->get($duration),
                'begin_date' => $request->get('begindate'),
                'target_date' => $request->get('targetdate'),
                'finished_date' => $request->get('finisheddate'), 
                'created_by' => $id_user
            ]);
            $data->save();
        }

        return redirect('/index/daily_report')->with('status', 'Update daily report success')->with('page', 'Daily Report');
    }
    catch (QueryException $e){
        return redirect('/index/daily_report')->with('error', $e->getMessage())->with('page', 'Daily Report');
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
       try{
        $master = DailyReport::where('id','=' ,$request->get('id'))
        ->delete();
    }catch (QueryException $e){
        return redirect('/index/daily_report')->with('error', $e->getMessage())->with('page', 'Daily Report');
    }

}
}
