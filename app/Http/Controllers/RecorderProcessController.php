<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\ActivityList;
use App\PushBlockMaster;
use App\PushBlockRecorder;
use App\PushBlockRecorderResume;
use Response;
use DataTables;
use Excel;
use App\User;
use File;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class RecorderProcessController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->product_type = ['YRS 23 IVORY',
                        'YRS 24B IVORY',
                        'YRS 20BB BLUE',
                        'YRS 20BG GREEN',
                    	'YRS 20BP PINK',
                    	'YRS 20BR RED',
                    	'YRS 20GB BLUE',
                    	'YRS 20GG GREEN',
                      'YRS 20GP PINK',
                    'YRS 24BUK BROWN',
                  'YRF 21 IVORY',];

      $this->mail = ['budhi.apriyanto@music.yamaha.com',
                    'khoirul.umam@music.yamaha.com',
                    'aditya.agassi@music.yamaha.com',
                    'takashi.ohkubo@music.yamaha.com',
                    'eko.prasetyo.wicaksono@music.yamaha.com'];
    }

  public function index(){
		return view('recorder.process.index')->with('page', 'Process Assy Recorder')->with('head', 'Assembly Process');
	}

	public function index_push_block($remark){
		$name = Auth::user()->name;
		return view('recorder.process.index_push_block')->with('page', 'Process Assy Recorder')->with('head', 'Recorder Push Block Check')->with('title', 'Recorder Push Block Check')->with('title_jp', '???')->with('name', $name)->with('product_type', $this->product_type)->with('batas_bawah', '3')->with('batas_atas', '17')->with('batas_tinggi', '0.2')->with('remark', $remark);
	}

	function fetch_push_block(Request $request)
    {
          try{
          	$no_cavity = $request->get("no_cavity");

            $detail = PushBlockMaster::find($no_cavity);
        	$data = array('type' => $detail->type,
        			  'no_cavity' => $detail->no_cavity,
                      'cavity_1' => $detail->cavity_1,
                      'cavity_2' => $detail->cavity_2,
                      'cavity_3' => $detail->cavity_3,
                      'cavity_4' => $detail->cavity_4);

            $response = array(
              'status' => true,
              'datas' => $data,
              'id' => $detail->id,
              'cavity_1' => $detail->cavity_1,
              'cavity_2' => $detail->cavity_2,
              'cavity_3' => $detail->cavity_3,
              'cavity_4' => $detail->cavity_4,
              'cavity_5' => $detail->cavity_5,
              'cavity_6' => $detail->cavity_6,
              'cavity_7' => $detail->cavity_7,
              'cavity_8' => $detail->cavity_8,
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    function create(Request $request)
    {
        	try{    
              $id_user = Auth::id();
              $push_pull = $request->get('push_pull');
              $judgement = $request->get('judgement');
              $ketinggian = $request->get('ketinggian');
              $judgementketinggian = $request->get('judgementketinggian');
              $head = $request->get('head');
              $block = $request->get('block');
              $push_block_code = $request->get('push_block_code');
              for($i = 0; $i<16;$i++){
                $check_date = $request->get('check_date');
                $product_type = $request->get('product_type');
                PushBlockRecorder::create([
                  'push_block_code' => $request->get('push_block_code'),
                    'check_date' => $request->get('check_date'),
                    'injection_date' => $request->get('injection_date'),
                    'product_type' => $request->get('product_type'),
                    'head' => $head[$i],
                    'block' => $block[$i],
                    'push_pull' => $push_pull[$i],
                    'judgement' => $judgement[$i],
                    'ketinggian' => $ketinggian[$i],
                    'judgement2' => $judgementketinggian[$i],
                    'pic_check' => $request->get('pic_check'),
                    'created_by' => $id_user
                ]);

                // $bodyHtml = "<html><h2>NG Report of Recorder Push Block Check</h2><p>Location : ".$push_block_code."</p><p>Check Date : ".$check_date."</p><p>Product Type : ".$product_type."</p><p>Head : ".$head[$i]."</p><p>Block : ".$block[$i]."</p><p>Push Pull : ".$push_pull[$i]."</p><p>Judgement : ".$judgement[$i]."</p></html>";

                // $bodyHtml2 = "<html><h2>NG Report of Hight Gauge Check Block Recorder</h2><p>Location : ".$push_block_code."</p><p>Check Date : ".$check_date."</p><p>Product Type : ".$product_type."</p><p>Head : ".$head[$i]."</p><p>Block : ".$block[$i]."</p><p>Hight Gauge : ".$ketinggian[$i]."</p><p>Judgement : ".$judgementketinggian[$i]."</p></html>";

                // if($judgement[$i] == 'NG'){
                //   foreach($this->mail as $mail_to){
                //     Mail::raw([], function($message) use($bodyHtml,$mail_to) {
                //         $message->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia');
                //         $message->to($mail_to);
                //         $message->subject('NG Report of Recorder Push Block Check');
                //         $message->setBody($bodyHtml, 'text/html' );
                //         // $message->addPart("5% off its awesome\n\nGo get it now!", 'text/plain');
                //     });
                //   }
                // }
                // if($judgementketinggian[$i] == 'NG'){
                //   foreach($this->mail as $mail_to){
                //     Mail::raw([], function($message) use($bodyHtml2,$mail_to) {
                //         $message->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia');
                //         $message->to($mail_to);
                //         $message->subject('NG Report of Hight Gauge Check Block Recorder');
                //         $message->setBody($bodyHtml2, 'text/html' );
                //         // $message->addPart("5% off its awesome\n\nGo get it now!", 'text/plain');
                //     });
                //   }
                // }
              }

              $response = array(
                'status' => true,
              );
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function create_resume(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $push_pull_ng_name = $request->get('push_pull_ng_name');
              $push_pull_ng_value = $request->get('push_pull_ng_value');
              $height_ng_name = $request->get('height_ng_name');
              $height_ng_value = $request->get('height_ng_value');
              $head = $request->get('head');
              $block = $request->get('block');
              $remark = $request->get('remark');

              PushBlockRecorderResume::create([
                'remark' => $remark,
                  'check_date' => $request->get('check_date'),
                  'injection_date' => $request->get('injection_date'),
                  'product_type' => $request->get('product_type'),
                  'head' => $head,
                  'block' => $block,
                  'push_pull_ng_name' => $push_pull_ng_name,
                  'push_pull_ng_value' => $push_pull_ng_value,
                  'height_ng_name' => $height_ng_name,
                  'height_ng_value' => $height_ng_value,
                  'pic_check' => $request->get('pic_check'),
                  'created_by' => $id_user
              ]);

              if($push_pull_ng_name != 'OK'){
                $data_push_pull = array(
                  'push_block_code' => $remark,
                  'check_date' => $request->get('check_date'),
                  'injection_date' => $request->get('injection_date'),
                  'product_type' => $request->get('product_type'),
                  'head' => $head,
                  'block' => $block,
                  'push_pull_ng_name' => $request->get('push_pull_ng_name2'),
                  'push_pull_ng_value' => $request->get('push_pull_ng_value2'),
                  'pic_check' => $request->get('pic_check'),
                );
                foreach($this->mail as $mail_to){
                    Mail::to($mail_to)->send(new SendEmail($data_push_pull, 'push_pull_check'));
                }
              }

              if($height_ng_name != 'OK'){
                $data_height = array(
                  'push_block_code' => $remark,
                  'check_date' => $request->get('check_date'),
                  'injection_date' => $request->get('injection_date'),
                  'product_type' => $request->get('product_type'),
                  'head' => $head,
                  'block' => $block,
                  'height_ng_name' => $request->get('height_ng_name2'),
                  'height_ng_value' => $request->get('height_ng_value2'),
                  'pic_check' => $request->get('pic_check'),
                );
                foreach($this->mail as $mail_to){
                    Mail::to($mail_to)->send(new SendEmail($data_height, 'height_check'));
                }
              }

              $response = array(
                'status' => true,
              );
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function fetchResume(Request $request)
    {
          try{
          	$head_id = $request->get("head_id");

            $detail = PushBlockMaster::find($head_id);

            $response = array(
              'status' => true,
              'datas' => $detail,
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    public function report_push_block($remark)
    {
        $push_block_check = PushBlockRecorder::where('push_block_code',$remark)->orderBy('push_block_recorders.id','desc')
              ->get();

        $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark,);
      return view('recorder.report.report_push_block', $data
        )->with('page', 'Report Push Block Check')->with('remark', $remark);
    }

    public function filter_report_push_block(Request $request,$remark)
    {
      $judgement = $request->get('judgement');
      $date_from = $request->get('date_from');
      $date_to = $request->get('date_to');
      $datenow = date('Y-m-d');

      if($request->get('date_to') == null){
        if($request->get('date_from') == null){
          $date = "";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$datenow."'";
        }
      }
      elseif($request->get('date_to') != null){
        if($request->get('date_from') == null){
          $date = "and date(check_date) <= '".$date_to."'";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$date_to."'";
        }
      }

      $judgement = '';
      if($request->get('judgement') != null){
        $judgements =  explode(",", $request->get('judgement'));
        for ($i=0; $i < count($judgements); $i++) {
          $judgement = $judgement."'".$judgements[$i]."'";
          if($i != (count($judgements)-1)){
            $judgement = $judgement.',';
          }
        }
        $judgementin = " and `judgement` in (".$judgement.") ";
        $judgementin2 = " or `judgement2` in (".$judgement.") ";
      }
      else{
        $judgementin = "";
        $judgementin2 = "";
      }

      $push_block_check = DB::SELECT("SELECT * FROM `push_block_recorders` where push_block_code = '".$remark."' ".$date." ".$judgementin." ".$judgementin2." ORDER BY push_block_recorders.id desc");

      $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark,);
      return view('recorder.report.report_push_block', $data
        )->with('page', 'Report Push Block Check')->with('remark', $remark);
    }

    public function resume_push_block($remark)
    {
        $push_block_check = PushBlockRecorderResume::where('remark',$remark)->orderBy('push_block_recorder_resumes.id','desc')->get();

        $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark);
      return view('recorder.report.resume_push_block', $data
        )->with('page', 'Resume Push Block Check')->with('remark', $remark);
    }

    public function push_block_check_monitoring($remark){
      $name = Auth::user()->name;
      return view('recorder.display.push_block_check_monitoring')->with('page', 'Recorder Push Block Check Monitoring')->with('head', 'Recorder Push Block Check Monitoring')->with('title', 'Recorder Push Block Check Monitoring')->with('title_jp', '???')->with('name', $name)->with('product_type', $this->product_type)->with('remark', $remark);
    }

    public function fetch_push_block_check_monitoring(Request $request,$remark){
    $date = '';
    if(strlen($request->get("bulan")) > 0){
      $date = date('Y-m', strtotime($request->get("bulan")));
    }else{
      $date = date('Y-m');
    }

    $monthTitle = date("F Y", strtotime($date));

    $data = db::select("select 
        weekly_calendars.week_date as date,
                (select sum(case when judgement = 'OK' then 1 else 0 end) as jumlah_ok from push_block_recorders where push_block_code = '".$remark."' and DATE_FORMAT(date(check_date),'%Y-%m') = '".$date."' and date(check_date) = week_date GROUP BY check_date) 
        as jumlah_ok,
        (select sum(case when judgement = 'NG' then 1 else 0 end) as jumlah_ok from push_block_recorders where push_block_code = '".$remark."' and DATE_FORMAT(date(check_date),'%Y-%m') = '".$date."' and date(check_date) = week_date GROUP BY check_date) 
        as jumlah_ng
                        from weekly_calendars 
                        where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$date."'
                        and weekly_calendars.week_date not in (select tanggal from ftm.kalender)");

    $response = array(
      'status' => true,
      'datas' => $data,
      'date' => $date,
      'remark' => $remark,
      'monthTitle' => $monthTitle,
    );
    return Response::json($response);
  }

  public function fetch_height_check_monitoring(Request $request,$remark){
    $date = '';
    if(strlen($request->get("bulan")) > 0){
      $date = date('Y-m', strtotime($request->get("bulan")));
    }else{
      $date = date('Y-m');
    }

    $monthTitle = date("F Y", strtotime($date));

    $data = db::select("select 
        weekly_calendars.week_date as date,
                (select sum(case when judgement2 = 'OK' then 1 else 0 end) as jumlah_ok2 from push_block_recorders where push_block_code = '".$remark."' and DATE_FORMAT(date(check_date),'%Y-%m') = '".$date."' and date(check_date) = week_date GROUP BY check_date) 
        as jumlah_ok2,
        (select sum(case when judgement2 = 'NG' then 1 else 0 end) as jumlah_ok2 from push_block_recorders where push_block_code = '".$remark."' and DATE_FORMAT(date(check_date),'%Y-%m') = '".$date."' and date(check_date) = week_date GROUP BY check_date) 
        as jumlah_ng2
                        from weekly_calendars 
                        where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$date."'
                        and weekly_calendars.week_date not in (select tanggal from ftm.kalender)");

    $response = array(
      'status' => true,
      'datas' => $data,
      'date' => $date,
      'remark' => $remark,
      'monthTitle' => $monthTitle,
    );
    return Response::json($response);
  }

  public function detail_monitoring(Request $request)
  {
    $tanggal = $request->get("tanggal");
    $judgement = $request->get("judgement");
    if($judgement == 'Jumlah OK'){
      $jdgm = 'OK';
    }
    else{
      $jdgm = 'NG';
    }
    $remark = $request->get("remark");

    $query = "SELECT * FROM `push_block_recorders` where date(check_date) = '".$tanggal."' and judgement = '".$jdgm."' and push_block_code = '".$remark."'";

    $detail = db::select($query);

    $response = array(
      'status' => true,
      'jdgm' => $jdgm,
      'lists' => $detail,
    );
    return Response::json($response);
  }

  public function detail_monitoring2(Request $request)
  {
    $tanggal = $request->get("tanggal");
    $judgement = $request->get("judgement");
    if($judgement == 'Jumlah OK'){
      $jdgm = 'OK';
    }
    else{
      $jdgm = 'NG';
    }
    $remark = $request->get("remark");

    $query = "SELECT * FROM `push_block_recorders` where date(check_date) = '".$tanggal."' and judgement2 = '".$jdgm."' and push_block_code = '".$remark."'";

    $detail = db::select($query);

    $response = array(
      'status' => true,
      'jdgm' => $jdgm,
      'lists' => $detail,
    );
    return Response::json($response);
  }

  public function print_report_push_block(Request $request,$remark)
    {
      $date_from = $request->get('date_from');
      $date_to = $request->get('date_to');
      $datenow = date('Y-m-d');

      if($request->get('date_to') == null){
        if($request->get('date_from') == null){
          $date = "";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$datenow."'";
        }
      }
      elseif($request->get('date_to') != null){
        if($request->get('date_from') == null){
          $date = "and date(check_date) <= '".$date_to."'";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$date_to."'";
        }
      }

      $push_block_check = DB::SELECT("SELECT * FROM `push_block_recorders` where push_block_code = '".$remark."' ".$date." ORDER BY push_block_recorders.id desc");

      $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark,);
      return view('recorder.report.print_push_block', $data
        )->with('page', 'Print Push Block Check')->with('remark', $remark);
    }
}
  