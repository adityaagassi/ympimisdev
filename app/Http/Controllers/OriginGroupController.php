<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\OriginGroup;
use Illuminate\Database\QueryException;

class OriginGroupController extends Controller
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
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')
        ->with(array('user'))
        ->get();

        return view('origin_groups.index', array(
            'origin_groups' => $origin_groups
        ));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('origin_groups.create');
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
            $origin_group = new OriginGroup([
              'origin_group_code' => $request->get('origin_group_code'),
              'origin_group_name' => $request->get('origin_group_name'),
              'created_by' => $id
          ]);

            $origin_group->save();
            return redirect('/index/origin_group')->with('status', 'New origin group has been created.');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Origin group code or origin group name already exist.');
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
        $origin_group = OriginGroup::find($id);
        $users = User::orderBy('name', 'ASC')->get();

        return view('origin_groups.show', array(
            'origin_group' => $origin_group,
            'users' => $users,
        ));
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
        $origin_group = OriginGroup::find($id);

        return view('origin_groups.edit', array(
            'origin_group' => $origin_group
        ));
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

            $origin_group = OriginGroup::find($id);
            $origin_group->origin_group_code = $request->get('origin_group_code');
            $origin_group->origin_group_name = $request->get('origin_group_name');
            $origin_group->save();

            return redirect('/index/origin_group')->with('status', 'Origin group data has been edited.');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Origin group code or origin group name already exist.');
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
        $origin_group = OriginGroup::find($id);
        $origin_group->delete();

        return redirect('/index/origin_group')->with('status', 'Origin group has been deleted.');
        //
    }
}
