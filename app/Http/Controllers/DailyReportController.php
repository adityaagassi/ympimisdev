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
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('daily_reports.index')->with('page', 'Daily Report');
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
        ->orderBy('daily_reports.begin_date', 'desc')
        ->get();

        return DataTables::of($daily_reports)
        ->addColumn('action', function($daily_reports){
            return '<a href="javascript:void(0)" class="btn btn-xs btn-info" onClick="detailReport(id)" id="' . $daily_reports->report_code . '">Details</a>';
        })
        ->addColumn('attach', function($daily_reports){
            if($daily_reports->att > 0){
                return '<a href="javascript:void(0)" id="' . $daily_reports->report_code . '" onClick="downloadAtt(id)" class="fa fa-paperclip"> ' . $daily_reports->att . ' Att</a>';
            }
            else{
                return '-';
            }
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
        //
    }
}
