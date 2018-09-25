<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Status;

class StatusController extends Controller
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
        $statuses = Status::orderBy('status_code', 'ASC')
        ->get();

        return view('statuses.index', array(
            'statuses' => $statuses
        ))->with('page', 'Status');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('statuses.create')->with('page', 'Status');
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
        try{

            $id = Auth::id();
            $status = new Status([
              'status_code' => $request->get('status_code'),
              'status_name' => $request->get('status_name'),
              'created_by' => $id
          ]);

            $status->save();
            return redirect('/index/status')->with('status', 'New status has been created.')->with('page', 'Status');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Status code or status name already exist.')->with('page', 'Status');
            }

        }
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
        $status = Status::find($id);
        $users = User::orderBy('name', 'ASC')->get();

        return view('statuses.show', array(
            'status' => $status,
        ))->with('page', 'Status');
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
        $status = Status::find($id);
        return view('statuses.edit', array(
            'status' => $status,
        ))->with('page', 'Status');
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
        try{

            $status = Status::find($id);
            $status->status_code = $request->get('status_code');
            $status->status_name = $request->get('status_name');
            $status->save();

            return redirect('/index/status')->with('status', 'Status data has been edited.')->with('page', 'Status');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Status code or status name already exist.')->with('page', 'Status');
            }
        }
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
        $status = Status::find($id);
        $status->delete();

        return redirect('/index/status')->with('status', 'Status has been deleted.')->with('page', 'Status');
        //
    }
}
