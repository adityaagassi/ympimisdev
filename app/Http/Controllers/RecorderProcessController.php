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
use Response;
use DataTables;
use Excel;
use App\User;
use File;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class RecorderProcessController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->product_type = ['YRS 20 G Ivory',
                        'YRS 20 G Pink',
                        'YRS 23 G Brown',
                        'YRS 23 G Green',
                    	'YRS 24 B Blue',
                    	'YRS 24 B Red',
                    	'YRF 21 Ivory',
                    	'YRF 21 Beige'];

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
		return view('recorder.process.index_push_block')->with('page', 'Process Assy Recorder')->with('head', 'Recorder Push Block Check')->with('title', 'Recorder Push Block Check')->with('title_jp', '???')->with('name', $name)->with('product_type', $this->product_type)->with('batas_bawah', '3')->with('batas_atas', '17')->with('remark', $remark);
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
                    'production_date' => $request->get('production_date'),
                    'head' => $head[$i],
                    'block' => $block[$i],
                    'push_pull' => $push_pull[$i],
                    'judgement' => $judgement[$i],
                    'pic_check' => $request->get('pic_check'),
                    'created_by' => $id_user
                ]);

                $bodyHtml = "<html><h2>NG Report of Recorder Push Block Check</h2><p>Location : ".$push_block_code."</p><p>Check Date : ".$check_date."</p><p>Product Type : ".$product_type."</p><p>Head : ".$head[$i]."</p><p>Block : ".$block[$i]."</p><p>Push Pull : ".$push_pull[$i]."</p><p>Judgement : ".$judgement[$i]."</p></html>";

                if($judgement[$i] == 'NG'){
                  foreach($this->mail as $mail_to){
                    Mail::raw([], function($message) use($bodyHtml,$mail_to) {
                        $message->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia');
                        $message->to($mail_to);
                        $message->subject('NG Report of Recorder Push Block Check');
                        $message->setBody($bodyHtml, 'text/html' );
                        // $message->addPart("5% off its awesome\n\nGo get it now!", 'text/plain');
                    });
                  }
                }
              }

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
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
}
