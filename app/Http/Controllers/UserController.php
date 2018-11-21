<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Database\QueryException;
use App\Role;


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
      $roles = Role::orderBy('role_name', 'ASC')->get();
      return view('users.create', array(
        'roles' => $roles,
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
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'role_code' => $request->get('role_code'),
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
      $user = User::find($id);

      return view('users.show', array(
        'user' => $user,
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

      $roles = Role::orderBy('role_name', 'ASC')->get();
      $user = User::find($id);
      return view('users.edit', array(
        'user' => $user,
        'roles' => $roles,
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
            $user->role_code = $request->get('role_code');
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
            $user->role_code = $request->get('role_code');
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
