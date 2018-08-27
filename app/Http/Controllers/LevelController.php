<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Level;
use App\User;
use Illuminate\Database\QueryException;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $levels = Level::orderBy('level_name', 'ASC')
        ->with(array('user'))
        ->get();

        return view('levels.index', array(
            'levels' => $levels
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
        return view('levels.create');
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
            $level = new Level([
              'level_name' => $request->get('level_name'),
              'created_by' => $id
          ]);
            $level->save();
            return redirect('/index/level')->with('status', 'New level has been created.');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Level name already exist.');
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
        $users = User::orderBy('name', 'ASC')->get();
        $level = Level::find($id);

        return view('levels.show', array(
            'level' => $level,
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
        $level = Level::find($id);
        return view('levels.edit', array(
            'level' => $level
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

                $level = Level::find($id);
                $level->level_name = $request->get('level_name');
                $level->save();

                return redirect('/index/level')->with('status', 'Level data has been edited.');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Level name already exist.');
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
        $level = Level::find($id);
        $level->delete();
        //
        return redirect('/index/level')->with('status', 'User has been deleted.');
        //
    }
}
