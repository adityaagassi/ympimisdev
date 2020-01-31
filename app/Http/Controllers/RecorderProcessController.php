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
use App\CodeGenerator;
use App\User;
use App\RcPushPullLog;
use App\RcCameraKangoLog;
use App\PlcCounter;
use App\Libraries\ActMLEasyIf;
use Response;
use DataTables;
use Excel;
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
                  'YRF 21 IVORY'];

      $this->mail = ['budhi.apriyanto@music.yamaha.com',
                    'khoirul.umam@music.yamaha.com',
                    'aditya.agassi@music.yamaha.com',
                    'takashi.ohkubo@music.yamaha.com',
                    'eko.prasetyo.wicaksono@music.yamaha.com'];
    }

  public function index(){
		return view('recorder.process.index')->with('page', 'Recorder')->with('head', 'Assembly Process');
	}

	public function index_push_block($remark){
		$name = Auth::user()->name;
		return view('recorder.process.index_push_block')->with('page', 'Process Assy Recorder')->with('head', 'Recorder Push Block Check')->with('title', 'Recorder Push Block Check')->with('title_jp', 'リコーダープッシュブロック検査')->with('name', $name)->with('product_type', $this->product_type)->with('batas_bawah', '3')->with('batas_atas', '17')->with('batas_tinggi', '0.2')->with('remark', $remark);
	}

  public function scanPushPullOperator(Request $request){

    $tag = $request->get('employee_id');

    if(strlen($tag) > 9){
      $tag = substr($tag,0,10);
    }

    $employee = db::table('employees')->where('tag', 'like', '%'.$tag.'%')->first();

    if(count($employee) > 0 ){
      $response = array(
        'status' => true,
        'message' => 'Logged In',
        'employee' => $employee
      );
      return Response::json($response);
    }
    else{
      $response = array(
        'status' => false,
        'message' => 'Tag Invalid'
      );
      return Response::json($response);
    }
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
                  'jumlah_cek' => '32',
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
      $date7days = DB::SELECT('select week_date from weekly_calendars where remark != "H" and week_date BETWEEN DATE(NOW()) - INTERVAL 7 DAY and DATE(NOW())');

      return view('recorder.display.push_block_check_monitoring')->with('page', 'Recorder Push Block Check Monitoring')->with('head', 'Recorder Process Monitoring')->with('title', 'Recorder Process Monitoring')->with('title_jp', 'リコーダー製造工程管理')->with('name', $name)->with('product_type', $this->product_type)->with('remark', $remark)->with('date', $date7days);
    }

    public function fetch_push_block_check_monitoring(Request $request,$remark){
    $date = '';
    if(strlen($request->get("bulan")) > 0){
      $date = date('Y-m-d', strtotime($request->get("bulan")));
    }else{
      $date = date('Y-m-d');
    }

    // $monthTitle = date("F Y", strtotime($date));

    $date7days = DB::SELECT("select week_date from weekly_calendars where remark != 'H' and week_date BETWEEN DATE('".$date."') - INTERVAL 7 DAY and DATE('".$date."')");

    // $datenew[] = '';

    // for($i = 0;$i<count($date7days);$i++) {
    //   $datenew[] = date('d F Y',strtotime($date7days[$i]));
    // }

    $data = db::select("select week_date,
    (select count(*) from push_block_recorders where push_block_code = 'First Shot Approval' and DATE(check_date) = week_date) 
    as countfsa,
    (select count(*) from push_block_recorders where push_block_code = 'First Shot Approval' and DATE(check_date) = week_date and judgement = 'NG') 
    as countfsappng,
    (select count(*) from push_block_recorders where push_block_code = 'First Shot Approval' and DATE(check_date) = week_date and judgement2 = 'NG') 
    as countfsahng,
    (select count(*) from push_block_recorders where push_block_code = 'After Injection' and DATE(check_date) = week_date) 
    as countai,
    (select count(*) from push_block_recorders where push_block_code = 'After Injection' and DATE(check_date) = week_date and judgement = 'NG') 
    as countaippng,
    (select count(*) from push_block_recorders where push_block_code = 'After Injection' and DATE(check_date) = week_date and judgement2 = 'NG') 
    as countaihng,
    (select count(*) from rc_push_pull_logs where DATE(check_date) = week_date) 
    as countppassy,
    (select count(*) from rc_push_pull_logs where DATE(check_date) = week_date and judgement = 'NG') 
    as countppassyng,
    (select count(*) from rc_camera_kango_logs where DATE(check_date) = week_date) 
    as countck,
    (select count(*) from rc_camera_kango_logs where DATE(check_date) = week_date and judgement = 'NG') 
    as countckng
    from weekly_calendars 
    where remark != 'H'
    and week_date BETWEEN DATE('".$date."') - INTERVAL 7 DAY and DATE('".$date."')");

    $response = array(
      'status' => true,
      'datas' => $data,
      'date7days' => $date7days,
      // 'datenew' => $datenew,
      'date' => $date,
      // 'remark' => $remark,
      // 'monthTitle' => $monthTitle,
    );
    return Response::json($response);
  }

  public function fetch_height_check_monitoring(Request $request,$remark){
    $date = '';
    if(strlen($request->get("bulan")) > 0){
      $date = date('Y-m-d', strtotime($request->get("bulan")));
    }else{
      $date = date('Y-m-d');
    }

    // $monthTitle = date("F Y", strtotime($date));

    $data = db::select("select DISTINCT(pic_check),
        sum(jumlah_cek) as jumlah_cek,
        COALESCE((select SUM((CHAR_LENGTH(push_pull_ng_value) - CHAR_LENGTH(REPLACE(push_pull_ng_value, ',', '')) + 1)) from push_block_recorder_resumes where push_pull_ng_value != 'OK' and remark = '".$remark."' and pic_check  = pushresume1.pic_check and check_date = '".$date."'),0)
        as jumlah_ng_push_pull,
        COALESCE((select SUM((CHAR_LENGTH(height_ng_value) - CHAR_LENGTH(REPLACE(height_ng_value, ',', '')) + 1)) from push_block_recorder_resumes where height_ng_value != 'OK' and remark = '".$remark."' and pic_check  = pushresume1.pic_check and check_date = '".$date."'),0)
        as jumlah_ng_height 
        from push_block_recorder_resumes as pushresume1
        where check_date = '".$date."' 
        and remark = '".$remark."' 
        GROUP BY pic_check");

    $response = array(
      'status' => true,
      'datas' => $data,
      'date' => $date,
      'remark' => $remark,
      // 'monthTitle' => $monthTitle,
    );
    return Response::json($response);
  }

  public function detail_monitoring(Request $request)
  {
    if ($request->get("tanggal") == null) {
      $tanggal = date('Y-m-d');
    }
    else{
      $tanggal = $request->get("tanggal");
    }
    $pic = $request->get("pic");
    $remark = $request->get("remark");

    $query = "select * from push_block_recorder_resumes where check_date = '".$tanggal."' and pic_check = '".$pic."' and remark = '".$remark."'";

    $detail = db::select($query);

    $response = array(
      'status' => true,
      'tanggal' => $tanggal,
      'pic' => $pic,
      'remark' => $remark,
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

    function get_push_pull(Request $request)
    {
          try{
            $detail = PushBlockRecorder::find($request->get("id"));
            $data = array('push_block_id' => $detail->id,
                          'check_date' => $detail->check_date,
                          'injection_date' => $detail->injection_date,
                          'product_type' => $detail->product_type,
                          'head' => $detail->head,
                          'block' => $detail->block,
                          'push_pull' => $detail->push_pull,
                          'judgement' => $detail->judgement,
                          'ketinggian' => $detail->ketinggian,
                          'judgement2' => $detail->judgement2,
                          'pic_check' => $detail->pic_check);

            $response = array(
              'status' => true,
              'data' => $data
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

    function update(Request $request,$id)
    {
        try{
                $push_pull = PushBlockRecorder::find($id);
                $push_pull->push_pull = $request->get('push_pull');
                $push_pull->judgement = $request->get('judgement');
                $push_pull->ketinggian = $request->get('ketinggian');
                $push_pull->judgement2 = $request->get('judgement2');
                $push_pull->save();

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

    public function index_push_pull(){
      $name = Auth::user()->name;

      $push_pull = RcPushPullLog::get();
      if (count($push_pull) > 0) {
        foreach ($push_pull as $key) {
          $check_number = $key->check_number;
        }
      }else{
        $check_number = 0;
      }
      return view('recorder.process.index_push_pull')->with('page', 'Process Assy Recorder')->with('head', 'Recorder Push Pull Check')->with('title', 'Recorder Push Pull Check')->with('title_jp', 'リコーダープッシュプールチェック')->with('name', $name)->with('product_type', $this->product_type)->with('batas_bawah', '3')->with('batas_atas', '17')->with('check_number', $check_number);
    }

    public function fetchResultPushPull()
    {
      try{
            $detail = RcPushPullLog::orderBy('id','DESC')->get();
            $detail2 = RcPushPullLog::get();

            $response = array(
              'status' => true,
              'data' => $detail,
              'data2' => $detail2
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

    public function fetchResultCamera()
    {
      try{
            $detail = RcCameraKangoLog::orderBy('id','DESC')->get();
            $detail_middle = RcCameraKangoLog::where('remark','Middle')->get();
            $detail_stamp = RcCameraKangoLog::where('remark','Stamp')->get();

            $response = array(
              'status' => true,
              'data' => $detail,
              'data_middle' => $detail_middle,
              'data_stamp' => $detail_stamp
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

    public function email($value,$judgement,$model,$checked_at,$pic_check,$remark)
    {
      // $bodyHtml2 = "<html><h2>NG Report of Push Pull Check Recorder リコーダープッシュプールチェック</h2><p>Model : ".$model."</p><p>Check Date : ".$checked_at."</p><p>Value : ".$value."</p><p>Judgement : ".$judgement."</p></html>";

      // $mail_to = 'budhi.apriyanto@music.yamaha.com';

      // Mail::raw([], function($message) use($bodyHtml2,$mail_to) {
      //     $message->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia');
      //     $message->to($mail_to);
      //     $message->subject('NG Report of Recorder Push Pull Check');
      //     $message->setBody($bodyHtml2, 'text/html' );
      //     // $message->addPart("5% off its awesome\n\nGo get it now!", 'text/plain');
      // });

      $data_push_pull = array('value' => $value,
      'judgement' => $judgement,
      'checked_at' => $checked_at,
      'model' => $model,
      'remark' => $remark,
      'pic_check' => $pic_check, );
      // var_dump($data_push_pull);
      // foreach ($data_push_pull as $key) {
        // var_dump($data_push_pull['judgement']);
      // }

      foreach($this->mail as $mail_to){
          Mail::to($mail_to)->send(new SendEmail($data_push_pull, 'push_pull'));
      }

      //http://172.17.128.87/miraidev/public/post/display/email/2.3/NG/YRS%2023%20IVORY/2020-01-21%2015:30:18
    }

    public function store_push_pull(Request $request)
    {
      try{
        // if ($request->get('originGroupCode') =='072') {
          $plc = new ActMLEasyIf(2);
          $counter_push_pull = $plc->read_data('D75', 1);
          $value_push_pull = $plc->read_data('D250', 1);
          $plc_counter = PlcCounter::where('origin_group_code', '=', '072_1')->first();
        // }
        $data = $counter_push_pull[0];
        $datavalue = $value_push_pull[0] / 120;
        // $datavalue = '2.9';
        // $data = 2;
        // var_dump($counter_push_pull);
        // var_dump($value_push_pull);


        if($plc_counter->plc_counter != $counter_push_pull[0]){

          // if(Auth::user()->role_code == "OP-PushPull-RC"){

          if ($datavalue > 0) {
            $id = Auth::id();

            $plc_counter->plc_counter = $data;

            if ($datavalue < 3 || $datavalue > 17) {
              $judgement = 'NG';
              $data_push_pull = array(
                  'value' => $datavalue,
                  'judgement' => $judgement,
                  'checked_at' => $request->get('check_date'),
                  'model' => $request->get('model'),
                  'remark' => 'Push Pull Check RC Assy',
                  'pic_check' => $request->get('pic_check'), );
              // var_dump($data_push_pull);
              // foreach ($data_push_pull as $key) {
                // var_dump($data_push_pull['judgement']);
              // }

              // foreach($this->mail as $mail_to){
              //     Mail::to($mail_to)->send(new SendEmail($data_push_pull, 'push_pull'));
              // }
            }else{
              $judgement = 'OK';
            }

            $push_pull = RcPushPullLog::create(
              [
                'model' => $request->get('model'),
                'check_date' => $request->get('check_date'),
                'value_check' => $datavalue,
                // 'value_check' => '2.9',
                'judgement' => $judgement,
                'pic_check' => $request->get('pic_check'),
                'created_by' => $id,
              ]
            );

            try{
                $plc_counter->save();
                $push_pull->save();
            }
            catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }

            $response = array(
              'status' => true,
              'statusCode' => 'push_pull',
              'message' => 'Push Pull success',
              'data' => $plc_counter->plc_counter,
              'counter' => $data,
              'value' => $datavalue,
              'judgement' => $judgement,
              'counter_all' => $counter_push_pull,
              'value_all' => $value_push_pull

            );
            return Response::json($response);
          }
            
          // }
        }
        else{
          $response = array(
            'status' => true,
            'statusCode' => 'noData',
          );
          return Response::json($response);
        }
      }
      catch (\Exception $e){
        $response = array(
          'status' => false,
          'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function store_camera(Request $request)
    {
      try{
        // if ($request->get('originGroupCode') =='072') {
        //   $plc = new ActMLEasyIf(2);
        //   $datas = $plc->read_data('D0', 1);
        //   $plc2 = new ActMLEasyIf(2);
        //   $datas2 = $plc2->read_data('D0', 1);
        $plc_counter = PlcCounter::where('origin_group_code', '=', '072_2')->first();
        $plc_counter2 = PlcCounter::where('origin_group_code', '=', '072_3')->first();
        // }
        // $data = $datas[0];
        // $data2 = $datas2[0];
        $data = 1;
        $data2 = 1;

        //MIDDLE
        if($plc_counter->plc_counter != $data){

          // if(Auth::user()->role_code == "OP-PushPull-RC"){

            $id = Auth::id();

            $plc_counter->plc_counter = $data;

            // if ($request->get('value_check') < 3 || $request->get('value_check') > 17) {
              $judgement = 'NG';
              $data_push_pull = array(
                  'value' => 'B',
                  'judgement' => $judgement,
                  'checked_at' => $request->get('check_date'),
                  'model' => $request->get('model'),
                  'remark' => 'Middle Camera Check RC Assy',
                  'pic_check' => $request->get('pic_check'), );
              // var_dump($data_push_pull);
              // foreach ($data_push_pull as $key) {
                // var_dump($data_push_pull['judgement']);
              // }

              // foreach($this->mail as $mail_to){
              //     Mail::to($mail_to)->send(new SendEmail($data_push_pull, 'push_pull'));
              // }
            // }else{
              // $judgement = 'OK';
            // }

            $camera = RcCameraKangoLog::create(
              [
                'model' => $request->get('model'),
                'check_date' => $request->get('check_date'),
                // 'value_check' => $request->get('value_check'),
                'value_check' => 'B',
                'judgement' => $judgement,
                'remark' => 'Middle',
                'pic_check' => $request->get('pic_check'),
                'created_by' => $id,
              ]
            );

            try{
                $plc_counter->save();
                $camera->save();
            }
            catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }

            $response = array(
              'status' => true,
              'statusCode' => 'camera',
              'message' => 'Push Pull success',
              'data' => $plc_counter->plc_counter
            );
            return Response::json($response);
          // }
        }

        //STAMP
        else if($plc_counter2->plc_counter != $data2){

          // if(Auth::user()->role_code == "OP-PushPull-RC"){

            $id = Auth::id();

            $plc_counter2->plc_counter = $data2;

            // if ($request->get('value_check') < 3 || $request->get('value_check') > 17) {
              $judgement = 'NG';
              $data_push_pull = array(
                  'value' => 'B',
                  'judgement' => $judgement,
                  'checked_at' => $request->get('check_date'),
                  'model' => $request->get('model'),
                  'remark' => 'Stamp',
                  'pic_check' => $request->get('pic_check'), );
              // var_dump($data_push_pull);
              // foreach ($data_push_pull as $key) {
                // var_dump($data_push_pull['judgement']);
              // }

              // foreach($this->mail as $mail_to){
              //     Mail::to($mail_to)->send(new SendEmail($data_push_pull, 'push_pull'));
              // }
            // }else{
              // $judgement = 'OK';
            // }

            $camera2 = RcCameraKangoLog::create(
              [
                'model' => $request->get('model'),
                'check_date' => $request->get('check_date'),
                // 'value_check' => $request->get('value_check'),
                'value_check' => 'B',
                'judgement' => $judgement,
                'remark' => 'Stamp Camera Check RC Assy',
                'pic_check' => $request->get('pic_check'),
                'created_by' => $id,
              ]
            );

            try{
                $plc_counter2->save();
                $camera2->save();
            }
            catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }

            $response = array(
              'status' => true,
              'statusCode' => 'camera2',
              'message' => 'Push Pull success',
              'data' => $plc_counter2->plc_counter
            );
            return Response::json($response);
          // }
        }
        else{
          $response = array(
            'status' => true,
            'statusCode' => 'noData',
          );
          return Response::json($response);
        }
      }
      catch (\Exception $e){
        $response = array(
          'status' => false,
          'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }
}
  