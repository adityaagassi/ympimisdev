<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Level;
use App\Department;
use Illuminate\Database\QueryException;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
      $this->middleware('auth');
    }
    
    public function index()
    {
      $created_by = User::orderBy('name', 'ASC')
      ->get();

      $users = User::orderBy('name', 'ASC')
      ->get();

      return view('users.index', array(
        'users' => $users,
        'created_by' => $created_by
      ))->with('page', 'User');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $levels = Level::orderBy('level_name', 'ASC')->get();
      $departments = Department::orderBy('department_name', 'ASC')->get();
      return view('users.create', array(
        'levels' => $levels,
        'departments' => $departments,
      ))->with('page', 'User');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $requesst
     * @return \Illuminate\Http\Response
     */
    public function store(request $request)
    {
      try{
        if($request->get('password') == $request->get('password_confirmation')){
          $id = Auth::id();
          $user = new User([
            'user' => $request->get('user'),
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'level_id' => $request->get('level'),
            'department_id' => $request->get('department'),
            'created_by' => $id
          ]);

          $user->save();
          return redirect('/index/user')->with('status', 'New user has been created.')->with('page', 'User');
        }
        else{
          return back()->withErrors(['password' => ['Password confirmation is invalid.']])->with('page', 'User'); 
        }  
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
            // self::delete($lid);
          return back()->with('error', 'Username or e-mail already exist.')->with('page', 'User');
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

      $created_bys = User::orderBy('name', 'ASC')->get();
      $levels = Level::orderBy('level_name', 'ASC')->get();
      $user = User::find($id);

      return view('users.show', array(
        'user' => $user,
        'levels' => $levels,
        'created_bys' => $created_bys
      ))->with('page', 'User');
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

      $levels = Level::orderBy('level_name', 'ASC')->get();
      $departments = Department::orderBy('department_name', 'ASC')->get();
      $user = User::find($id);
      return view('users.edit', array(
        'user' => $user,
        'levels' => $levels,
        'departments' => $departments,
      ))->with('page', 'User');
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

        if(strlen($request->get('password'))>0 || strlen($request->get('password_confirmation')>0)){
          if($request->get('password') == $request->get('password_confirmation')){
            $user = User::find($id);
            $user->name = $request->get('name');
            $user->username = $request->get('username');
            $user->email = $request->get('email');
            $user->password = $request->get('password');
            $user->level_id = $request->get('level');
            $user->department_id = $request->get('department');
            $user->save();
            return redirect('/index/user')->with('status', 'User data has been edited.')->with('page', 'User');
          }
          else
          {
            return back()->withErrors(['password' => ['Password confirmation is invalid.']])->with('page', 'User');
          }
        }
        elseif ($request->get('password')=='' || $request->get('password_confirmation')=='') {
          if($request->get('password') == $request->get('password_confirmation')){
            $user = User::find($id);
            $user->name = $request->get('name');
            $user->username = $request->get('username');
            $user->email = $request->get('email');
                // $user->password = $request->get('password');
            $user->level_id = $request->get('level');
            $user->department_id = $request->get('department');
            $user->save();
            return redirect('/index/user')->with('status', 'User data has been edited.')->with('page', 'User');
          }
          else
          {
            return back()->withErrors(['password' => ['Password confirmation is invalid.']])->with('page', 'User');
          }
        }


      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
            // self::delete($lid);
          return back()->with('error', 'Username or e-mail already exist.')->with('page', 'User');
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
      $user = User::find($id);
      $user->delete();

      return redirect('/index/user')->with('status', 'User has been deleted.')->with('page', 'User');
        //
    }
  }
