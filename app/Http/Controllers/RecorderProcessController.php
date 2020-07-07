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
use App\PushBlockParameter;
use App\CodeGenerator;
use App\User;
use App\RcPushPullLog;
use App\RcCameraKangoLog;
use App\PlcCounter;
use App\PushBlockTorqueTemp;
use App\PushBlockTorque;
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

      $this->molding = ['HJ 01',
                      'HJ 02',
                      'HJ 03',
                      'HJ 04',
                      'HJ 05',
                      'BL 01',
                      'BL 02',
                      'BL 03',
                      'BL 04'];

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
		return view('recorder.process.index_push_block')
    ->with('page', 'Process Assy Recorder')
    ->with('head', 'Recorder Push Block Check')
    ->with('title', 'Recorder Push Block Check')
    ->with('title_jp', 'リコーダープッシュブロック検査')
    ->with('name', $name)
    ->with('product_type', $this->product_type)
    ->with('mesin', $this->mesin)
    ->with('mesin2', $this->mesin)
    ->with('mesin3', $this->mesin)
    ->with('batas_bawah', '3')
    ->with('batas_atas', '17')
    ->with('batas_tinggi', '0.2')
    ->with('remark', $remark);
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

    function fetch_mesin_parameter(Request $request)
    {
          try{
            $mesin = $request->get("mesin");
            $remark = $request->get("remark");

            $detail = PushBlockParameter::where('mesin',$mesin)->where('push_block_code',$remark)->orderBy('id','desc')->first();

            $response = array(
              'status' => true,
              // 'id' => $detail->id,
              'detail' => $detail
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Get Data Failed",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Get Data Failed",
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
                  'push_block_id_gen' => $request->get('push_block_id_gen'),
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
              if ($push_block_code == 'After Injection') {
                $front = 'AI';
              }else{
                $front = 'FSA';
              }

              $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$request->get('pic_check');
              for($i = 0; $i<16;$i++){
                $check_date = $request->get('check_date');
                $product_type = $request->get('product_type');
                PushBlockRecorderTemp::create([
                  'push_block_code' => $request->get('push_block_code'),
                  'push_block_id_gen' => $push_block_id_gen,
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
              }

              $response = array(
                'status' => true,
                'message' => 'Success Create Temp',
                'push_block_id_gen' => $push_block_id_gen
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
        $product_type = $request->get('product_type');

        $temp = [];

        // $ng_temp = PushBlockRecorderTemp::where('mesin',$mesin)->get();
        for($i = 0; $i < 8; $i++){
          for($j = 0; $j < 4; $j++){
            $temptemp = PushBlockRecorderTemp::where('head',$array_head[$j])->where('block',$array_block[$i])->where('push_block_code',$remark)->where('product_type',$product_type)->get();
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
                'push_block_id_gen' => $request->get('push_block_id_gen'),
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
                $contactList = [];
                $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
                // $contactList[1] = 'aditya.agassi@music.yamaha.com';
                    Mail::to($this->mail)->bcc($contactList,'Contact List')->send(new SendEmail($data_height, 'height_check'));
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

    function create_parameter(Request $request)
    {
          try{    
              $id_user = Auth::id();

              $name = Auth::user()->name;

              $front = 'FSA';

              if ($request->get('pic_check') == null) {
                $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$name;
              }else{
                $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$request->get('pic_check');
              }

              PushBlockParameter::create([
                'push_block_code' => $request->get('push_block_code'),
                'push_block_id_gen' => $push_block_id_gen,
                  'check_date' => $request->get('check_date'),
                  'reason' => $request->get('reason'),
                  'product_type' => $request->get('product_type'),
                  'mesin' => $request->get('mesin'),
                  'molding' => $request->get('molding'),
                  'nh' => $request->get('nh'),
                  'h1' => $request->get('h1'),
                  'h2' => $request->get('h2'),
                  'h3' => $request->get('h3'),
                  'dryer' => $request->get('dryer'),
                  'mtc_temp' => $request->get('mtc_temp'),
                  'mtc_press' => $request->get('mtc_press'),
                  'chiller_temp' => $request->get('chiller_temp'),
                  'chiller_press' => $request->get('chiller_press'),
                  'clamp' => $request->get('clamp'),
                  'ph4' => $request->get('ph4'),
                  'ph3' => $request->get('ph3'),
                  'ph2' => $request->get('ph2'),
                  'ph1' => $request->get('ph1'),
                  'trh3' => $request->get('trh3'),
                  'trh2' => $request->get('trh2'),
                  'trh1' => $request->get('trh1'),
                  'vh' => $request->get('vh'),
                  'pi' => $request->get('pi'),
                  'ls10' => $request->get('ls10'),
                  'vi5' => $request->get('vi5'),
                  'vi4' => $request->get('vi4'),
                  'vi3' => $request->get('vi3'),
                  'vi2' => $request->get('vi2'),
                  'vi1' => $request->get('vi1'),
                  'ls4' => $request->get('ls4'),
                  'ls4d' => $request->get('ls4d'),
                  'ls4c' => $request->get('ls4c'),
                  'ls4b' => $request->get('ls4b'),
                  'ls4a' => $request->get('ls4a'),
                  'ls5' => $request->get('ls5'),
                  've1' => $request->get('ve1'),
                  've2' => $request->get('ve2'),
                  'vr' => $request->get('vr'),
                  'ls31a' => $request->get('ls31a'),
                  'ls31' => $request->get('ls31'),
                  'srn' => $request->get('srn'),
                  'rpm' => $request->get('rpm'),
                  'bp' => $request->get('bp'),
                  'tr1inj' => $request->get('tr1inj'),
                  'tr3cool' => $request->get('tr3cool'),
                  'tr4int' => $request->get('tr4int'),
                  'mincush' => $request->get('mincush'),
                  'fill' => $request->get('fill'),
                  'circletime' => $request->get('circletime'),
                  'created_by' => $id_user
              ]);

              $response = array(
                'status' => true,
                'message' => 'Success Create Parameter',
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
        // $from = date('Y-m').'-01';
        // $to = date('Y-m-d');
        // $push_block_check = PushBlockRecorder::where('push_block_code',$remark)->orderBy('push_block_recorders.id','desc')->whereBetween('check_date', [$from, $to])
        //       ->get();

        $data = array(
                      // 'push_block_check' => $push_block_check,
          'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
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

      if($request->get('mesin_head') != null){
        $mesin_head = " and `mesin_head` =  '".$request->get('mesin_head')."'";
      }
      else{
        $mesin_head = "";
      }

      if($request->get('mesin_block') != null){
        $mesin_block = " and `mesin_block` =  '".$request->get('mesin_block')."'";
      }
      else{
        $mesin_block = "";
      }

      $push_block_check = DB::SELECT("SELECT * FROM `push_block_recorders` where push_block_code = '".$remark."' ".$date." ".$judgementin." ".$judgementin2." ".$mesin_head." ".$mesin_block." ORDER BY push_block_recorders.id desc");

      $data = array('push_block_check' => $push_block_check,
        'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
                      'remark' => $remark,);
      return view('recorder.report.report_push_block', $data
        )->with('page', 'Report Push Block Check')->with('remark', $remark);
    }

    public function resume_push_block($remark)
    {
        $push_block_check = PushBlockRecorderResume::where('remark',$remark)->orderBy('push_block_recorder_resumes.id','desc')->get();

        $auth = Auth::user()->role_code;

        $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark,
                      'auth' => $auth,
                      'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
                      'mesin3' => $this->mesin,
                      'mesin4' => $this->mesin,
                      'product_type' => $this->product_type);
      return view('recorder.report.resume_push_block', $data
        )->with('page', 'Resume Push Block Check')->with('remark', $remark);
    }

    public function filter_resume_push_block(Request $request,$remark)
    {
      $date_from = $request->get('date_from');
      $date_to = $request->get('date_to');
      $datenow = date('Y-m-d');

      $auth = Auth::user()->role_code;

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

      if($request->get('mesin_head') != null){
        $mesin_head = " and `mesin_head` =  '".$request->get('mesin_head')."'";
      }
      else{
        $mesin_head = "";
      }

      if($request->get('mesin_block') != null){
        $mesin_block = " and `mesin_block` =  '".$request->get('mesin_block')."'";
      }
      else{
        $mesin_block = "";
      }

      $push_block_check = DB::SELECT("SELECT * FROM `push_block_recorder_resumes` where remark = '".$remark."' ".$date." ".$mesin_head." ".$mesin_block." ORDER BY push_block_recorder_resumes.id desc");

      $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark,
                      'auth' => $auth,
                      'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
                      'mesin3' => $this->mesin,
                      'mesin4' => $this->mesin,
                      'product_type' => $this->product_type);
      return view('recorder.report.resume_push_block', $data
        )->with('page', 'Resume Push Block Check')->with('remark', $remark);
    }

    public function get_resume(Request $request)
    {
      $id = $request->get('id');

      $data = DB::SELECT("SELECT * FROM `push_block_recorder_resumes` where id = '".$id."'");

      $response = array(
        'status' => true,
        'data' => $data,
      );
      return Response::json($response);
    }

    public function update_resume(Request $request,$id)
    {

      $resume_push_block = PushBlockRecorderResume::where('remark',$request->get('remark'))->where('id',$id)->first();
      $resume_push_block->injection_date_head = $request->get('injection_date_head');
      $resume_push_block->injection_date_block = $request->get('injection_date_block');
      $resume_push_block->mesin_head = $request->get('mesin_head');
      $resume_push_block->mesin_block = $request->get('mesin_block');
      $resume_push_block->product_type = $request->get('product_type');
      $pic_check = $resume_push_block->pic_check;

      if ($request->get('remark') == 'After Injection') {
        $front = 'AI';
      }else{
        $front = 'FSA';
      }

      $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$pic_check;

      $resume_push_block->push_block_id_gen = $push_block_id_gen;

      $push_block_check = PushBlockRecorder::where('push_block_code',$request->get('remark'))->get();
      foreach ($push_block_check as $key) {
        $push_block = PushBlockRecorder::find($key->id);
        $push_block->injection_date_head = $request->get('injection_date_head');
        $push_block->injection_date_block = $request->get('injection_date_block');
        $push_block->mesin_head = $request->get('mesin_head');
        $push_block->mesin_block = $request->get('mesin_block');
        $push_block->product_type = $request->get('product_type');
        $push_block->push_block_id_gen = $push_block_id_gen;
        try {
          $push_block->save();
        } catch (\Exception $e) {
          $response = array(
            'status' => false
          );
          return Response::json($response);
        }
      }

      try {
        $resume_push_block->save();
      } catch (\Exception $e) {
        $response = array(
          'status' => false
        );
        return Response::json($response);
      }

      $response = array(
        'status' => true,
      );
      return Response::json($response);
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

    public function indexMachineParameter($remark)
    {
      $parameters = PushBlockParameter::where('push_block_code',$remark)->orderBy('push_block_parameters.id', 'desc')
        ->get();

      return view('recorder.process.index_machine_parameter')
      ->with('page', 'Machine Parameter')
      ->with('head', 'Machine Parameter')
      ->with('title', 'Machine Parameter')
      ->with('title_jp', '機械条件')
      ->with('mesin', $this->mesin)
      ->with('mesin2', $this->mesin)
      ->with('mesin3', $this->mesin)
      ->with('molding', $this->molding)
      ->with('molding2', $this->molding)
      ->with('molding3', $this->molding)
      ->with('parameter', $parameters)
      ->with('product_type', $this->product_type)
      ->with('product_type2', $this->product_type);
    }

    function get_parameter(Request $request)
    {
          try{
            $detail = PushBlockParameter::find($request->get("id"));
            $data = array('push_block_id' => $detail->id,
                          'check_date' => $detail->check_date,
                          'reason' => $detail->reason,
                          'product_type' => $detail->product_type,
                          'mesin' => $detail->mesin,
                          'molding' => $detail->molding,
                          'nh' => $detail->nh,
                          'h1' => $detail->h1,
                          'h2' => $detail->h2,
                          'h3' => $detail->h3,
                          'dryer' => $detail->dryer,
                          'mtc_temp' => $detail->mtc_temp,
                          'mtc_press' => $detail->mtc_press,
                          'chiller_temp' => $detail->chiller_temp,
                          'chiller_press' => $detail->chiller_press,
                          'clamp' => $detail->clamp,
                          'ph4' => $detail->ph4,
                          'ph3' => $detail->ph3,
                          'ph2' => $detail->ph2,
                          'ph1' => $detail->ph1,
                          'trh3' => $detail->trh3,
                          'trh2' => $detail->trh2,
                          'trh1' => $detail->trh1,
                          'vh' => $detail->vh,
                          'pi' => $detail->pi,
                          'ls10' => $detail->ls10,
                          'vi5' => $detail->vi5,
                          'vi4' => $detail->vi4,
                          'vi3' => $detail->vi3,
                          'vi2' => $detail->vi2,
                          'vi1' => $detail->vi1,
                          'ls4' => $detail->ls4,
                          'ls4d' => $detail->ls4d,
                          'ls4c' => $detail->ls4c,
                          'ls4b' => $detail->ls4b,
                          'ls4a' => $detail->ls4a,
                          'ls5' => $detail->ls5,
                          've1' => $detail->ve1,
                          've2' => $detail->ve2,
                          'vr' => $detail->vr,
                          'ls31a' => $detail->ls31a,
                          'ls31' => $detail->ls31,
                          'srn' => $detail->srn,
                          'rpm' => $detail->rpm,
                          'bp' => $detail->bp,
                          'tr1inj' => $detail->tr1inj,
                          'tr3cool' => $detail->tr3cool,
                          'tr4int' => $detail->tr4int,
                          'mincush' => $detail->mincush,
                          'fill' => $detail->fill,
                          'circletime' => $detail->circletime,
                        );

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
              'datas' => "Gagal Ambil Data",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Gagal Ambil Data.",
            );
             return Response::json($response);
            }
        }
    }

    function update_parameter(Request $request,$id)
    {
          try{    
              $id_user = Auth::id();

              $parameter = PushBlockParameter::find($id);
              $parameter->reason = $request->get('reason');
              $parameter->product_type = $request->get('product_type');
              $parameter->mesin = $request->get('mesin');
              $parameter->molding = $request->get('molding');
              $parameter->nh = $request->get('nh');
              $parameter->h1 = $request->get('h1');
              $parameter->h2 = $request->get('h2');
              $parameter->h3 = $request->get('h3');
              $parameter->dryer = $request->get('dryer');
              $parameter->mtc_temp = $request->get('mtc_temp');
              $parameter->mtc_press = $request->get('mtc_press');
              $parameter->chiller_temp = $request->get('chiller_temp');
              $parameter->chiller_press = $request->get('chiller_press');
              $parameter->clamp = $request->get('clamp');
              $parameter->ph4 = $request->get('ph4');
              $parameter->ph3 = $request->get('ph3');
              $parameter->ph2 = $request->get('ph2');
              $parameter->ph1 = $request->get('ph1');
              $parameter->trh3 = $request->get('trh3');
              $parameter->trh2 = $request->get('trh2');
              $parameter->trh1 = $request->get('trh1');
              $parameter->vh = $request->get('vh');
              $parameter->pi = $request->get('pi');
              $parameter->ls10 = $request->get('ls10');
              $parameter->vi5 = $request->get('vi5');
              $parameter->vi4 = $request->get('vi4');
              $parameter->vi3 = $request->get('vi3');
              $parameter->vi2 = $request->get('vi2');
              $parameter->vi1 = $request->get('vi1');
              $parameter->ls4 = $request->get('ls4');
              $parameter->ls4d = $request->get('ls4d');
              $parameter->ls4c = $request->get('ls4c');
              $parameter->ls4b = $request->get('ls4b');
              $parameter->ls4a = $request->get('ls4a');
              $parameter->ls5 = $request->get('ls5');
              $parameter->ve1 = $request->get('ve1');
              $parameter->ve2 = $request->get('ve2');
              $parameter->vr = $request->get('vr');
              $parameter->ls31a = $request->get('ls31a');
              $parameter->ls31 = $request->get('ls31');
              $parameter->srn = $request->get('srn');
              $parameter->rpm = $request->get('rpm');
              $parameter->bp = $request->get('bp');
              $parameter->tr1inj = $request->get('tr1inj');
              $parameter->tr3cool = $request->get('tr3cool');
              $parameter->tr4int = $request->get('tr4int');
              $parameter->mincush = $request->get('mincush');
              $parameter->fill = $request->get('fill');
              $parameter->circletime = $request->get('circletime');
              $parameter->save();

              $response = array(
                'status' => true,
                'message' => 'Success Create Parameter',
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

    function delete_parameter(Request $request,$id)
    {
          try{    
              $id_user = Auth::id();

              $parameter = PushBlockParameter::find($id);
              $parameter->delete();

              return redirect('/index/machine_parameter')
              ->with('status', 'Parameter has been deleted.')
              ->with('page', 'Machine Parameter');
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    public function filterMachineParameter(Request $request)
    {
      $mesin = $request->get('mesin_filter');
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

      $mesin = '';
      if($request->get('mesin_filter') != null){
        $mesins =  explode(",", $request->get('mesin_filter'));
        for ($i=0; $i < count($mesins); $i++) {
          $mesin = $mesin."'".$mesins[$i]."'";
          if($i != (count($mesins)-1)){
            $mesin = $mesin.',';
          }
        }
        $mesinin = " and `mesin` in (".$mesin.") ";
      }
      else{
        $mesinin = "";
      }

      $parameters = DB::SELECT("SELECT
        * 
      FROM
        push_block_parameters 
        where push_block_code = 'First Shot Approval'
        ".$date." ".$mesinin." ORDER BY
          push_block_parameters.id DESC");

      return view('recorder.process.index_machine_parameter')
      ->with('page', 'Machine Parameter')
      ->with('head', 'Machine Parameter')
      ->with('title', 'Machine Parameter')
      ->with('title_jp', '???')
      ->with('mesin', $this->mesin)
      ->with('mesin2', $this->mesin)
      ->with('mesin3', $this->mesin)
      ->with('molding', $this->molding)
      ->with('molding2', $this->molding)
      ->with('molding3', $this->molding)
      ->with('parameter', $parameters)
      ->with('product_type', $this->product_type)
      ->with('product_type2', $this->product_type);
    }

    public function index_torque($remark){
      $name = Auth::user()->name;
      return view('recorder.process.index_torque')
      ->with('page', 'Process Assy Recorder')
      ->with('head', 'Recorder Torque Check')
      ->with('title', 'Recorder Torque Check')
      ->with('title_jp', '???')
      ->with('name', $name)
      ->with('product_type', $this->product_type)
      ->with('mesin', $this->mesin)
      ->with('mesin2', $this->mesin)
      ->with('batas_bawah_hm', '15')
      ->with('batas_atas_hm', '73')
      ->with('batas_bawah_mf', '15')
      ->with('batas_atas_mf', '78')
      ->with('remark', $remark);
    }

    function fetchResumeTorque(Request $request)
    {
          try{
            $middle_id = $request->get("middle_id");

            $detail_middle = PushBlockMaster::find($middle_id);

            // var_dump($detail_middle->cavity_1);
            $cav_middle = array(
                      '1' => $detail_middle->cavity_1,
                      '2' => $detail_middle->cavity_2,
                      '3' => $detail_middle->cavity_3,
                      '4' => $detail_middle->cavity_4 );

            $head_foot_id = $request->get("head_foot_id");

            $detail_head_foot = PushBlockMaster::find($head_foot_id);

            if ($detail_head_foot->cavity_5 == null) {
              $cav_head_foot = array(
                      '1' => $detail_head_foot->cavity_1,
                      '2' => $detail_head_foot->cavity_2,
                      '3' => $detail_head_foot->cavity_3,
                      '4' => $detail_head_foot->cavity_4 );
            }else{
              $cav_head_foot = array(
                      '1' => $detail_head_foot->cavity_1,
                      '2' => $detail_head_foot->cavity_2,
                      '3' => $detail_head_foot->cavity_3,
                      '4' => $detail_head_foot->cavity_4,
                      '5' => $detail_head_foot->cavity_5,
                      '6' => $detail_head_foot->cavity_6, );
            }

            $response = array(
              'status' => true,
              'detail_middle' => $detail_middle,
              'detail_head_foot' => $detail_head_foot,
              'cav_middle' => $cav_middle,
              'cav_head_foot' => $cav_head_foot,
            );
            return Response::json($response);

          }
          catch (Exception $e){
             $response = array(
              'status' => false,
              'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    function create_torque(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $torque_1 = $request->get('torque_1');
              $torque_2 = $request->get('torque_2');
              $torque_3 = $request->get('torque_3');
              $average = $request->get('average');
              $middle = $request->get('middle');
              $head_foot = $request->get('head_foot');
              $push_block_code = $request->get('push_block_code');
              $judgement = $request->get('judgement');

              if ($push_block_code == 'After Injection') {
                $front = 'AI';
              }else{
                $front = 'FSA';
              }

              $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$request->get('pic_check');

              for($i = 0; $i<count($middle);$i++){
                PushBlockTorque::create([
                  'push_block_code' => $request->get('push_block_code'),
                  'push_block_id_gen' => $push_block_id_gen,
                    'check_date' => $request->get('check_date'),
                    'check_type' => $request->get('check_type'),
                    'injection_date_middle' => $request->get('injection_date_middle'),
                    'mesin_middle' => $request->get('mesin_middle'),
                    'injection_date_head_foot' => $request->get('injection_date_head_foot'),
                    'mesin_head_foot' => $request->get('mesin_head_foot'),
                    'product_type' => $request->get('product_type'),
                    'middle' => $middle[$i],
                    'head_foot' => $head_foot[$i],
                    'torque1' => $torque_1[$i],
                    'torque2' => $torque_2[$i],
                    'torque3' => $torque_3[$i],
                    'torqueavg' => $average[$i],
                    'judgement' => $judgement[$i],
                    'pic_check' => $request->get('pic_check'),
                    'notes' => $request->get('notes'),
                    'created_by' => $id_user
                ]);
                $temptemp = PushBlockTorqueTemp::where('middle',$middle[$i])->where('head_foot',$head_foot[$i])->where('push_block_code',$push_block_code)->where('check_type',$request->get('check_type'))->delete();
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

    public function get_temp_torque(Request $request){
        $array_middle = $request->get('array_middle');
        $array_head_foot = $request->get('array_head_foot');
        $remark = $request->get('remark');
        $product_type = $request->get('product_type');
        $check_type = $request->get('check_type');

        $temp = [];

        $indexHeadFoot = (int)$request->get('indexHeadFoot');

        $index = $indexHeadFoot * 4;

        for($i = 0; $i < $index; $i++){
            $temptemp = PushBlockTorqueTemp::where('middle',$array_middle[$i])->where('head_foot',$array_head_foot[$i])->where('push_block_code',$remark)->where('product_type',$product_type)->where('check_type',$check_type)->first();
            
            if (count($temptemp) > 0) {
              $temp[] = array('check_date' => $temptemp->check_date,
              'check_type' => $temptemp->check_type,
              'injection_date_middle' => $temptemp->injection_date_middle,
              'injection_date_head_foot' => $temptemp->injection_date_head_foot,
              'mesin_middle' => $temptemp->mesin_middle,
              'mesin_head_foot' => $temptemp->mesin_head_foot,
              'product_type' => $temptemp->product_type,
              'middle' => $temptemp->middle,
              'head_foot' => $temptemp->head_foot,
              'torque1' => $temptemp->torque1,
              'torque2' => $temptemp->torque2,
              'torque3' => $temptemp->torque3,
              'torqueavg' => $temptemp->torqueavg,
              'judgement' => $temptemp->judgement,
              'pic_check' => $temptemp->pic_check,
              'notes' => $temptemp->notes, );
            }
        }

        $response = array(
            'status' => true,            
            'datas' => $temp,
            'message' => 'Success get Temp'
        );
        return Response::json($response);
    }

    function create_temp_torque(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $middle = $request->get('middle');
              $head_foot = $request->get('head_foot');
              $push_block_code = $request->get('push_block_code');

              if ($push_block_code == 'After Injection') {
                $front = 'AI';
              }else{
                $front = 'FSA';
              }

              $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$request->get('pic_check');

              for($i = 0; $i<count($middle);$i++){
                PushBlockTorqueTemp::create([
                  'push_block_code' => $request->get('push_block_code'),
                  'push_block_id_gen' => $push_block_id_gen,
                    'check_date' => $request->get('check_date'),
                    'check_type' => $request->get('check_type'),
                    'injection_date_middle' => $request->get('injection_date_middle'),
                    'mesin_middle' => $request->get('mesin_middle'),
                    'injection_date_head_foot' => $request->get('injection_date_head_foot'),
                    'mesin_head_foot' => $request->get('mesin_head_foot'),
                    'product_type' => $request->get('product_type'),
                    'middle' => $middle[$i],
                    'head_foot' => $head_foot[$i],
                    'pic_check' => $request->get('pic_check'),
                    'created_by' => $id_user
                ]);
              }

              $response = array(
                'status' => true,
                'message' => 'Success Create Temp',
                'push_block_id_gen' => $push_block_id_gen
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

    function update_temp_torque(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $torque_1 = $request->get('torque_1');
              $torque_2 = $request->get('torque_2');
              $torque_3 = $request->get('torque_3');
              $average = $request->get('average');
              $middle = $request->get('middle');
              $check_type = $request->get('check_type');
              $head_foot = $request->get('head_foot');
              $push_block_code = $request->get('push_block_code');
              $judgement = $request->get('judgement');
              $notes = $request->get('notes');
              for($i = 0; $i<count($middle);$i++){
                $temptemp = PushBlockTorqueTemp::where('middle',$middle[$i])->where('head_foot',$head_foot[$i])->where('push_block_code',$push_block_code)->where('check_type',$check_type)->get();
                foreach ($temptemp as $key) {
                  $update = PushBlockTorqueTemp::find($key->id);
                  $update->torque1 = $torque_1[$i];
                  $update->torque2 = $torque_2[$i];
                  $update->torque3 = $torque_3[$i];
                  $update->torqueavg = $average[$i];
                  $update->judgement = $judgement[$i];
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

    public function report_torque_check($remark)
    {
        // $from = date('Y-m').'-01';
        // $to = date('Y-m-d');
        // $push_block_check = PushBlockRecorder::where('push_block_code',$remark)->orderBy('push_block_recorders.id','desc')->whereBetween('check_date', [$from, $to])
        //       ->get();

        $data = array(
                      // 'push_block_check' => $push_block_check,
                      'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
                      'remark' => $remark,);
      return view('recorder.report.report_torque_check', $data
        )->with('page', 'Report Torque Check')->with('remark', $remark);
    }

    public function filter_report_torque_check(Request $request,$remark)
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
      }
      else{
        $judgementin = "";
      }

      if($request->get('check_type') != null){
        $check_type = " and `check_type` =  '".$request->get('check_type')."'";
      }
      else{
        $check_type = "";
      }

      if($request->get('mesin_middle') != null){
        $mesin_middle = " and `mesin_middle` =  '".$request->get('mesin_middle')."'";
      }
      else{
        $mesin_middle = "";
      }

      if($request->get('mesin_head_foot') != null){
        $mesin_head_foot = " and `mesin_head_foot` =  '".$request->get('mesin_head_foot')."'";
      }
      else{
        $mesin_head_foot = "";
      }

      $torque_check = DB::SELECT("SELECT * FROM `push_block_torques` where push_block_code = '".$remark."' ".$date." ".$judgementin." ".$check_type." ".$mesin_middle." ".$mesin_head_foot." ORDER BY push_block_torques.id desc");

      $data = array('torque_check' => $torque_check,
                      'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
                      'remark' => $remark,);
      return view('recorder.report.report_torque_check', $data
        )->with('page', 'Report Torque Check')->with('remark', $remark);
    }

    public function index_torque_ai($remark){
      $name = Auth::user()->name;
      return view('recorder.process.index_torque_ai')
      ->with('page', 'Process Assy Recorder')
      ->with('head', 'Recorder Torque Check')
      ->with('title', 'Recorder Torque Check')
      ->with('title_jp', '???')
      ->with('name', $name)
      ->with('product_type', $this->product_type)
      ->with('mesin', $this->mesin)
      ->with('mesin2', $this->mesin)
      ->with('mesin3', $this->mesin)
      ->with('batas_bawah_hm', '15')
      ->with('batas_atas_hm', '73')
      ->with('batas_bawah_mf', '15')
      ->with('batas_atas_mf', '78')
      ->with('remark', $remark);
    }

    function fetchResumeTorqueAi(Request $request)
    {
          try{
            $middle_id = $request->get("middle_id");

            $detail_middle = PushBlockMaster::find($middle_id);

            // var_dump($detail_middle->cavity_1);
            $cav_middle = array(
                      '1' => $detail_middle->cavity_1,
                      '2' => $detail_middle->cavity_2,
                      '3' => $detail_middle->cavity_3,
                      '4' => $detail_middle->cavity_4 );

            $head_foot_id = $request->get("head_foot_id");

            $detail_head_foot = PushBlockMaster::find($head_foot_id);

            if ($detail_head_foot->cavity_5 == null) {
              $cav_head_foot = array(
                      '1' => $detail_head_foot->cavity_1,
                      '2' => $detail_head_foot->cavity_2,
                      '3' => $detail_head_foot->cavity_3,
                      '4' => $detail_head_foot->cavity_4 );
            }else{
              $cav_head_foot = array(
                      '1' => $detail_head_foot->cavity_1,
                      '2' => $detail_head_foot->cavity_2,
                      '3' => $detail_head_foot->cavity_3,
                      '4' => $detail_head_foot->cavity_4,
                      '5' => $detail_head_foot->cavity_5,
                      '6' => $detail_head_foot->cavity_6, );
            }

            $response = array(
              'status' => true,
              'detail_middle' => $detail_middle,
              'detail_head_foot' => $detail_head_foot,
              'cav_middle' => $cav_middle,
              'cav_head_foot' => $cav_head_foot,
            );
            return Response::json($response);

          }
          catch (Exception $e){
             $response = array(
              'status' => false,
              'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }
}
  