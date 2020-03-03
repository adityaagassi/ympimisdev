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
use App\PushBlockRecorderTemp;
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
      $this->product_type = ['YRS-27III //J',
                            'YRS-28BIII //J',
                            'YRF-21//ID',
                            'YRS-20BB //ID',
                            'YRS-20BG //ID',
                            'YRS-20BP //ID',
                            'YRS-23 //ID',
                            'YRS-23CA //ID',
                            'YRS-24B //ID',
                            'YRS-23K//ID',
                            'YRS-24BK//ID',
                            'YRS-20GP //ID',
                            'YRS-20GG //ID',
                            'YRS-20GB //ID',
                            'YRS-20BR //ID',
                            'YRS-23  //WOFB   ID',
                            'YRS-24BUKII //ID',
                            'YRS-24BCA //ID',
                            'YRS-23BR//ID',
                            'YRS-24BBR//ID',
                            'YRS-20BB // WOFB ID',
                            'YRS-20BG// WOFB ID',
                            'YRS-20BP// WOFB ID',
                            'YRS-20GBK//ID',
                            'YRS-20GGK//ID',
                            'YRS-20GPK//ID',
                            'YRS-24B // WOFB ID',
                            'YRF-21K//ID',
                            'YRS-24B//MX ID',
                            'YRS TRANSLUCENT (FSA)',
                            'YRS BROWN (FSA)',
                            'YRS IVORY (FSA)',
                            'YRF-21 (FSA)'];

      $this->mesin = ['#1',
                      '#2',
                      '#3',
                      '#4',
                      '#5',
                      '#6',
                      '#7',
                      '#8',
                      '#9',
                      '#10',
                      '#11',];

      $this->mail = ['budhi.apriyanto@music.yamaha.com',
                    'khoirul.umam@music.yamaha.com',
                    'aditya.agassi@music.yamaha.com',
                    'takashi.ohkubo@music.yamaha.com',
                    'eko.prasetyo.wicaksono@music.yamaha.com'];
      // $this->array_push_pull = [];
      $this->checked_at_time = date('Y-m-d H:i:s');
    }

  public function index(){
		return view('recorder.process.index')->with('page', 'Recorder')->with('head', 'Assembly Process');
	}

	public function index_push_block($remark){
		$name = Auth::user()->name;
		return view('recorder.process.index_push_block')->with('page', 'Process Assy Recorder')->with('head', 'Recorder Push Block Check')->with('title', 'Recorder Push Block Check')->with('title_jp', 'リコーダープッシュブロック検査')->with('name', $name)->with('product_type', $this->product_type)->with('mesin', $this->mesin)->with('mesin2', $this->mesin)->with('batas_bawah', '3')->with('batas_atas', '17')->with('batas_tinggi', '0.2')->with('remark', $remark);
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
                    'injection_date_head' => $request->get('injection_date_head'),
                    'mesin_head' => $request->get('mesin_head'),
                    'injection_date_block' => $request->get('injection_date_block'),
                    'mesin_block' => $request->get('mesin_block'),
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
                $temptemp = PushBlockRecorderTemp::where('head',$head[$i])->where('block',$block[$i])->where('push_block_code',$push_block_code)->delete();
                // foreach ($temptemp as $key) {
                //   $delete = PushBlockRecorderTemp::find($key->id);
                //   $delete->delete();
                // }
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

    function create_temp(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $head = $request->get('head');
              $block = $request->get('block');
              $push_block_code = $request->get('push_block_code');
              for($i = 0; $i<16;$i++){
                $check_date = $request->get('check_date');
                $product_type = $request->get('product_type');
                PushBlockRecorderTemp::create([
                  'push_block_code' => $request->get('push_block_code'),
                    'check_date' => $request->get('check_date'),
                    'injection_date_head' => $request->get('injection_date_head'),
                    'mesin_head' => $request->get('mesin_head'),
                    'injection_date_block' => $request->get('injection_date_block'),
                    'mesin_block' => $request->get('mesin_block'),
                    'product_type' => $request->get('product_type'),
                    'head' => $head[$i],
                    'block' => $block[$i],
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
                'message' => 'Success Create Temp',
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

    public function get_temp(Request $request){
        $array_head = $request->get('array_head');
        $array_block = $request->get('array_block');
        $remark = $request->get('remark');

        $temp = [];

        // $ng_temp = PushBlockRecorderTemp::where('mesin',$mesin)->get();
        for($i = 0; $i < 8; $i++){
          for($j = 0; $j < 4; $j++){
            $temptemp = PushBlockRecorderTemp::where('head',$array_head[$j])->where('block',$array_block[$i])->where('push_block_code',$remark)->get();
            if (count($temptemp) > 0) {
              $temp[] = $temptemp;
            }
          }
        }

        $response = array(
            'status' => true,            
            'datas' => $temp,
            'message' => 'Success get Temp'
        );
        return Response::json($response);
    }

    function update_temp(Request $request)
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
              $notes = $request->get('notes');
              for($i = 0; $i<16;$i++){
                $temptemp = PushBlockRecorderTemp::where('head',$head[$i])->where('block',$block[$i])->where('push_block_code',$push_block_code)->get();
                foreach ($temptemp as $key) {
                  $update = PushBlockRecorderTemp::find($key->id);
                  $update->push_pull = $push_pull[$i];
                  $update->judgement = $judgement[$i];
                  $update->ketinggian = $ketinggian[$i];
                  $update->judgement2 = $judgementketinggian[$i];
                  $update->notes = $notes;
                  $update->save();
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
              $notes = $request->get('notes');

              PushBlockRecorderResume::create([
                'remark' => $remark,
                  'check_date' => $request->get('check_date'),
                  'injection_date_head' => $request->get('injection_date_head'),
                  'mesin_head' => $request->get('mesin_head'),
                  'injection_date_block' => $request->get('injection_date_block'),
                  'mesin_block' => $request->get('mesin_block'),
                  'product_type' => $request->get('product_type'),
                  'head' => $head,
                  'block' => $block,
                  'push_pull_ng_name' => $push_pull_ng_name,
                  'push_pull_ng_value' => $push_pull_ng_value,
                  'height_ng_name' => $height_ng_name,
                  'height_ng_value' => $height_ng_value,
                  'jumlah_cek' => '32',
                  'pic_check' => $request->get('pic_check'),
                  'notes' => $notes,
                  'created_by' => $id_user
              ]);

              if($push_pull_ng_name != 'OK'){
                $data_push_pull = array(
                  'push_block_code' => $remark,
                  'check_date' => $request->get('check_date'),
                  'injection_date_head' => $request->get('injection_date_head'),
                  'mesin_head' => $request->get('mesin_head'),
                  'injection_date_block' => $request->get('injection_date_block'),
                  'mesin_block' => $request->get('mesin_block'),
                  'product_type' => $request->get('product_type'),
                  'head' => $head,
                  'block' => $block,
                  'push_pull_ng_name' => $request->get('push_pull_ng_name2'),
                  'push_pull_ng_value' => $request->get('push_pull_ng_value2'),
                  'pic_check' => $request->get('pic_check'),
                );
                // foreach($this->mail as $mail_to){
                    Mail::to($this->mail)->send(new SendEmail($data_push_pull, 'push_pull_check'));
                // }
              }

              if($height_ng_name != 'OK'){
                $data_height = array(
                  'push_block_code' => $remark,
                  'check_date' => $request->get('check_date'),
                  'injection_date_head' => $request->get('injection_date_head'),
                  'mesin_head' => $request->get('mesin_head'),
                  'injection_date_block' => $request->get('injection_date_block'),
                  'mesin_block' => $request->get('mesin_block'),
                  'product_type' => $request->get('product_type'),
                  'head' => $head,
                  'block' => $block,
                  'height_ng_name' => $request->get('height_ng_name2'),
                  'height_ng_value' => $request->get('height_ng_value2'),
                  'pic_check' => $request->get('pic_check'),
                );
                // foreach($this->mail as $mail_to){
                    Mail::to($this->mail)->send(new SendEmail($data_height, 'height_check'));
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

    public function filter_resume_push_block(Request $request,$remark)
    {
      // $judgement = $request->get('judgement');
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

      // $judgement = '';
      // if($request->get('judgement') != null){
      //   $judgements =  explode(",", $request->get('judgement'));
      //   for ($i=0; $i < count($judgements); $i++) {
      //     $judgement = $judgement."'".$judgements[$i]."'";
      //     if($i != (count($judgements)-1)){
      //       $judgement = $judgement.',';
      //     }
      //   }
      //   $judgementin = " and `judgement` in (".$judgement.") ";
      //   $judgementin2 = " or `judgement2` in (".$judgement.") ";
      // }
      // else{
      //   $judgementin = "";
      //   $judgementin2 = "";
      // }

      $push_block_check = DB::SELECT("SELECT * FROM `push_block_recorder_resumes` where remark = '".$remark."' ".$date." ORDER BY push_block_recorder_resumes.id desc");

      $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark,);
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
                          'injection_date_head' => $detail->injection_date_head,
                          'mesin_head' => $detail->mesin_head,
                          'injection_date_block' => $detail->injection_date_block,
                          'mesin_block' => $detail->mesin_block,
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
      return view('recorder.process.index_push_pull')->with('page', 'Process Assy Recorder')->with('head', 'Recorder Camera Kango Check')->with('title', 'Recorder Camera Kango Check')->with('title_jp', 'リコーダープッシュプールチェック')->with('name', $name)->with('product_type', $this->product_type)->with('batas_bawah', '3')->with('batas_atas', '17');
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
          $counter_push_pull = $plc->read_data('D275', 1);
          $value_push_pull = $plc->read_data('D250', 1);

          $data = $counter_push_pull[0];
          $datavalue = $value_push_pull[0] / 120;

          $valuebefore = 0;

          $plc_counter = PlcCounter::where('origin_group_code', '=', '072_1')->first();
          $pushpull = RcPushPullLog::orderBy('rc_push_pull_logs.id','DESC')->first();
          // var_dump($pushpull);
          // foreach ($pushpull as $pushpull) {
            if (count($pushpull) == 0) {
              $valuebefore = 0;
            }else{
              $valuebefore = $pushpull->value_check;
            }
          // }
        // }
        
        // $datavalue = '2.9';
        // $data = 11;
        // var_dump($counter_push_pull);
        // var_dump($value_push_pull);


        if($plc_counter->plc_counter != $data){

          if(Auth::user()->role_code == "OP-Assy-RC"){

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

            // if ($request->get('check_date') == $this->checked_at_time) {
            //   $array_push_pull[] = $datavalue;
            //   $this->checked_at_time = $request->get('check_date');
            // }
            // else{
            //   $datavalue = max($array_push_pull);
              if ($valuebefore != $datavalue) {
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
              }
            // }

            

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
              'valuebefore' => $valuebefore
              // 'counter_all' => $counter_push_pull,
              // 'value_all' => $value_push_pull

            );
            return Response::json($response);
          }
            
          }
        }
        else{
          $response = array(
            'status' => false,
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

        $jumlah_perolehan = 0;

        $date = date('Y-m-d');
        $plc_counter = PlcCounter::where('origin_group_code', '=', '072_2')->first();

        $perolehan = DB::SELECT("select count(*) as jumlah from rc_camera_kango_logs where DATE(check_date) = '".$date."'");

        if (count($perolehan) > 0) {
          foreach ($perolehan as $key) {
            $jumlah_perolehan = $key->jumlah;
            // $jumlah_perolehan = convertToK($key->jumlah);
          }
        }
        else{
          $jumlah_perolehan = 0;
        }
        // $plc_counter2 = PlcCounter::where('origin_group_code', '=', '072_3')->first();
        // }
        // $data = $datas[0];
        // $data2 = $datas2[0];
        $filenamefix = '';
        foreach (glob(public_path('RCImages/Cam1/*.txt')) as $filename) {
            // echo "$filename size " . filesize($filename) . "\n";
          // var_dump();
          $data = substr($filename,-9,5);
          $filenamefix = $filename;
          // File::delete($filename);
        }
        // $data2 = 1;

        //MIDDLE
        if($plc_counter->plc_counter != $data){

          $file = File::get($filenamefix);
          $filepecah = explode(' ', $file);
          // var_dump($filepecah[3]);
          $judgement = substr($filepecah[3], 7,2);
          if(Auth::user()->role_code == "OP-Assy-RC"){

            $id = Auth::id();

            $plc_counter->plc_counter = $data;

            // if ($request->get('value_check') < 3 || $request->get('value_check') > 17) {
              // $judgement = 'NG';
              $data_push_pull = array(
                  'value' => $request->get('value_check'),
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
                'value_check' => $request->get('value_check'),
                'judgement' => $judgement,
                'remark' => 'Middle',
                'file' => 'RC_'.$data.'.bmp',
                'pic_check' => $request->get('pic_check'),
                'created_by' => $id,
              ]
            );

            try{
                File::delete(glob(public_path('RCImages/Cam1/*.txt')));
                // File::delete(glob(public_path('RCImages/Cam1/*.bmp')));
                $plc_counter->save();
                $camera->save();
            }
            catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
                'jumlah_perolehan' => $jumlah_perolehan
              );
              return Response::json($response);
            }

            $response = array(
              'status' => true,
              'statusCode' => 'camera',
              'message' => 'Push Pull success',
              'data' => $plc_counter->plc_counter,
              'judgement' => $judgement,
              'jumlah_perolehan' => $jumlah_perolehan
            );
            return Response::json($response);
          }
        }
        else{
          $response = array(
            'status' => true,
            'statusCode' => 'noData',
            'data' => $data,
            'plc' => $plc_counter->plc_counter,
            'filename' => $filenamefix,
            'jumlah_perolehan' => $jumlah_perolehan
          );
          return Response::json($response);
        }
      }
      catch (\Exception $e){
        $response = array(
          'status' => false,
          'jumlah_perolehan' => $jumlah_perolehan,
          'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function store_camera2(Request $request)
    {
      try{
        // if ($request->get('originGroupCode') =='072') {
        //   $plc = new ActMLEasyIf(2);
        //   $datas = $plc->read_data('D0', 1);
        //   $plc2 = new ActMLEasyIf(2);
        //   $datas2 = $plc2->read_data('D0', 1);
        $plc_counter = PlcCounter::where('origin_group_code', '=', '072_3')->first();
        // $plc_counter2 = PlcCounter::where('origin_group_code', '=', '072_3')->first();
        // }
        // $data = $datas[0];
        // $data2 = $datas2[0];
        $filenamefix = '';
        foreach (glob(public_path('RCImages/Cam2/*.txt')) as $filename) {
            // echo "$filename size " . filesize($filename) . "\n";
          // var_dump();
          $data = substr($filename,-9,5);
          $filenamefix = $filename;
          // File::delete($filename);
        }

        // $filenamefix2 = '';
        // foreach (glob(public_path('RCImages/Cam2/*.txt')) as $filename2) {
        //     // echo "$filename size " . filesize($filename) . "\n";
        //   // var_dump();
        //   $data2 = substr($filename2,-9,5);
        //   $filenamefix2 = $filename2;
        //   // File::delete($filename);
        // }
        // $data2 = 1;

        //STAMP
        if($plc_counter->plc_counter != $data){

          $file = File::get($filenamefix);
          $filepecah = explode(' ', $file);
          // var_dump($filepecah[3]);
          $judgement = substr($filepecah[3], 7,2);
          if(Auth::user()->role_code == "OP-Assy-RC"){

            $id = Auth::id();

            $plc_counter->plc_counter = $data;

            // if ($request->get('value_check') < 3 || $request->get('value_check') > 17) {
              // $judgement = 'NG';
              $data_push_pull = array(
                  'value' => $request->get('value_check'),
                  'judgement' => $judgement,
                  'checked_at' => $request->get('check_date'),
                  'model' => $request->get('model'),
                  'remark' => 'Stamp Camera Check RC Assy',
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
                'value_check' => $request->get('value_check'),
                'judgement' => $judgement,
                'remark' => 'Stamp',
                'file' => 'RC_'.$data.'.bmp',
                'pic_check' => $request->get('pic_check'),
                'created_by' => $id,
              ]
            );

            try{
                File::delete(glob(public_path('RCImages/Cam2/*.txt')));
                // File::delete(glob(public_path('RCImages/Cam2/*.bmp')));
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
              'data' => $plc_counter->plc_counter,
              'judgement' => $judgement
            );
            return Response::json($response);
          }
        }
        else{
          $response = array(
            'status' => true,
            'statusCode' => 'noData',
            'data' => $data,
            'plc' => $plc_counter->plc_counter,
            'filename' => $filenamefix
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

    public function index_resume_assy_rc()
    {
      $code = RcPushPullLog::orderBy('rc_push_pull_logs.id', 'asc')
        ->get();


        return view('recorder.report.resume_assy_rc',array(
          'code' => $code,
        ))->with('page', 'Process Assy RC')->with('head', 'Assembly Process');
    }

    public function filter_assy_rc(Request $request){
      if($request->get('process') == 'Middle Check'){
        $rc_assy_detail = DB::table('rc_camera_kango_logs')
      
        ->select('rc_camera_kango_logs.id','rc_camera_kango_logs.model', 'rc_camera_kango_logs.value_check', 'rc_camera_kango_logs.judgement','rc_camera_kango_logs.pic_check', db::raw('date_format(rc_camera_kango_logs.created_at, "%d-%b-%Y") as st_date') );

        if(strlen($request->get('datefrom')) > 0){
          $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_camera_kango_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        // if(strlen($request->get('code')) > 0){
        //   $code = $request->get('code');
        //   $rc_assy_detail = $rc_assy_detail->where('rc_camera_kango_logs.process_code','=', $code );
        // }

        if(strlen($request->get('dateto')) > 0){
          $date_to = date('Y-m-d', strtotime($request->get('dateto')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_camera_kango_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        $rc_detail = $rc_assy_detail->orderBy('rc_camera_kango_logs.created_at', 'desc')->where('rc_camera_kango_logs.remark', 'Middle')->get();
      }else if($request->get('process') == 'Stamp Check'){
        $rc_assy_detail = DB::table('rc_camera_kango_logs')
      
        ->select('rc_camera_kango_logs.id','rc_camera_kango_logs.model', 'rc_camera_kango_logs.value_check', 'rc_camera_kango_logs.judgement','rc_camera_kango_logs.pic_check', db::raw('date_format(rc_camera_kango_logs.created_at, "%d-%b-%Y") as st_date') );

        if(strlen($request->get('datefrom')) > 0){
          $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_camera_kango_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        // if(strlen($request->get('code')) > 0){
        //   $code = $request->get('code');
        //   $rc_assy_detail = $rc_assy_detail->where('rc_camera_kango_logs.process_code','=', $code );
        // }

        if(strlen($request->get('dateto')) > 0){
          $date_to = date('Y-m-d', strtotime($request->get('dateto')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_camera_kango_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        $rc_detail = $rc_assy_detail->orderBy('rc_camera_kango_logs.created_at', 'desc')->where('rc_camera_kango_logs.remark', 'Stamp')->get();
      }else{
        $rc_assy_detail = DB::table('rc_push_pull_logs')
      
        ->select('rc_push_pull_logs.id','rc_push_pull_logs.model', 'rc_push_pull_logs.value_check', 'rc_push_pull_logs.judgement','rc_push_pull_logs.pic_check', db::raw('date_format(rc_push_pull_logs.created_at, "%d-%b-%Y") as st_date') );

        if(strlen($request->get('datefrom')) > 0){
          $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_push_pull_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        // if(strlen($request->get('code')) > 0){
        //   $code = $request->get('code');
        //   $rc_assy_detail = $rc_assy_detail->where('rc_push_pull_logs.process_code','=', $code );
        // }

        if(strlen($request->get('dateto')) > 0){
          $date_to = date('Y-m-d', strtotime($request->get('dateto')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_push_pull_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        $rc_detail = $rc_assy_detail->orderBy('rc_push_pull_logs.created_at', 'desc')->get();
      }
      

      return DataTables::of($rc_detail)
      ->addColumn('action', function($rc_detail){
        return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $rc_detail->id . '"><i class="glyphicon glyphicon-trash"></i></a>';
      })
      ->make(true);
    }

    public function index_rc_picking_result()
    {
      return view('recorder.display.rc_picking_result',array(
        // 'code' => $code,
      ))->with('page', 'Recorder Assembly Picking Result')->with('jp', '???')->with('head', 'Assembly Process');
    }

    public function fetch_rc_picking_result(Request $request)
    {
      $date = '';
      if(strlen($request->get("date")) > 0){
        $date = date('Y-m-d', strtotime($request->get("date")));
      }else{
        $date = date('Y-m-d');
      }

      // var_dump($date);

      $monthTitle = date("F Y", strtotime($date));

      // $datenew[] = '';

      // for($i = 0;$i<count($date7days);$i++) {
      //   $datenew[] = date('d F Y',strtotime($date7days[$i]));
      // }

      $data = db::select("select SUM(quantity) as plan,materials.surface,materials.key,CONCAT(materials.key,' - ',materials.surface) as colorkey,(select count(id) as actual from rc_camera_kango_logs where remark = 'Middle' and DATE(created_at) = '".$date."' and model = materials.surface and value_check = materials.key) as actual from production_schedules join materials on production_schedules.material_number = materials.material_number where due_date = '".$date."' and materials.origin_group_code = '072' and materials.category = 'FG' GROUP BY colorkey,surface,materials.key");

      $response = array(
        'status' => true,
        'datas' => $data,
        // 'date7days' => $date7days,
        // 'datenew' => $datenew,
        'date' => $date,
        // 'remark' => $remark,
        'monthTitle' => $monthTitle,
      );
      return Response::json($response);


    }

    public function index_torque($remark){
      $name = Auth::user()->name;
      return view('recorder.process.index_torque')->with('page', 'Process Assy Recorder')->with('head', 'Recorder Torque Check')->with('title', 'Recorder Torque Check')->with('title_jp', '???')->with('name', $name)->with('product_type', $this->product_type)->with('mesin', $this->mesin)->with('mesin2', $this->mesin)->with('batas_bawah_hm', '15')->with('batas_atas_hm', '73')->with('batas_bawah_mf', '15')->with('batas_atas_mf', '78')->with('remark', $remark);
    }
}
  