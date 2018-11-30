<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\BatchSetting;


class BatchSettingController extends Controller
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
        $batch_settings = BatchSetting::orderBy('batch_time', 'ASC')
        ->get();

        return view('batch_settings.index', array(
            'batch_settings' => $batch_settings
        ))->with('page', 'Batch Setting');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('batch_settings.create')->with('page', 'Batch Setting');
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
        $upload = $request->has('upload') ? '1' : '0';
        $download = $request->has('download') ? '1' : '0';

        try{
            $id = Auth::id();
            $batch_setting = new BatchSetting([
              'batch_time' => date('H:i:s', strtotime($request->get('batch_time'))),
              'upload' => $upload,
              'download' => $download,
              'remark' => $request->get('remark'),
              'created_by' => $id
          ]);

            $batch_setting->save();
            return redirect('/index/batch_setting')->with('status', 'New batch has been created.')->with('page', 'Batch Setting');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Batch remark with preferred time already exist.')->with('page', 'Batch Setting');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Batch Setting');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $batch_setting = BatchSetting::find($id);
        return view('batch_settings.show', array(
            'batch_setting' => $batch_setting,
        ))->with('page', 'Batch Setting');
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
        $batch_setting = BatchSetting::find($id);
        return view('batch_settings.edit', array(
            'batch_setting' => $batch_setting,
        ))->with('page', 'Batch Setting');
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
        $upload = $request->has('upload') ? '1' : '0';
        $download = $request->has('download') ? '1' : '0';
        try{

            $batch_setting = BatchSetting::find($id);
            $batch_setting->batch_time = $request->get('batch_time');
            $batch_setting->upload = $upload;
            $batch_setting->download = $download;
            $batch_setting->remark = $request->get('remark');
            $batch_setting->save();

            return redirect('/index/batch_setting')->with('status', 'Batch data has been edited.')->with('page', 'Batch Setting');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Batch remark with preferred time already exist.')->with('page', 'Batch Setting');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Batch Setting');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $batch_setting = BatchSetting::find($id);
        $batch_setting->delete();

        return redirect('/index/batch_setting')->with('status', 'Batch has been deleted.')->with('page', 'Batch Setting');
        //
    }
}
