<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Navigation;
use App\Permission;

class NavigationController extends Controller
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
        $navigations = Navigation::orderBy('ID', 'ASC')
        ->where('navigation_code', '<>', 'Dashboard')
        ->get();

        return view('navigations.index', array(
            'navigations' => $navigations
        ))->with('page', 'Navigation');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('navigations.create')->with('page', 'Navigation');
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
            $navigation = new Navigation([
                'navigation_code' => $request->get('navigation_code'),
                'navigation_name' => $request->get('navigation_name'),
                'created_by' => $id,
            ]);
            $navigation->save();

            $permission = new Permission([
                'role_code' => 'S',
                'navigation_code' => $request->get('navigation_code'),
                'created_by' => $id
            ]);
            $permission->save();

            return redirect('/index/navigation')->with('status', 'New navigation has been created.')->with('page', 'Navigation');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Navigation code already exist.')->with('page', 'Navigation');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Navigation');
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
        $navigation = Navigation::find($id);
        return view('navigations.show', array(
            'navigation' => $navigation,
        ))->with('page', 'Navigation');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $navigation = Navigation::find($id);
        return view('navigations.edit', array(
            'navigation' => $navigation,
        ))->with('page', 'Navigation');
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
            $navigation = Navigation::find($id);
            $navigation->navigation_code = $request->get('navigation_code');
            $navigation->navigation_name = $request->get('navigation_name');
            $navigation->save();

            return redirect('/index/navigation')->with('status', 'Navigation data has been edited.')->with('page', 'Navigation');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Navigation code already exist.')->with('page', 'Navigation');
            }
            else{
                back()->with('error', $e->getMessage())->with('page', 'Navigation');
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
        $navigation = Navigation::find($id);
        $navigation->forceDelete();

        return redirect('/index/navigation')->with('status', 'Navigation has been deleted.')->with('page', 'Navigation');
    }
}
