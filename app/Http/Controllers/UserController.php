<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = User::all()->toArray();
        // return view('users.index', compact('users'));
        $users = User::orderBy('name', 'ASC')
        ->wherenull('deleted_at')
        ->get();
        return view('users.index', array(
            'users' => $users
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
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
                  'created_by' => $id
              ]);

                $user->save();
                return redirect('/index/user')->with('status', 'New user has been created.');
            }
            else{
                return back()->withErrors(['password' => ['Password confirmation is invalid.']]); 
            }  
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Username or e-mail already exist.');
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
        $user = User::find($id);
        return view('users.show', compact('user', 'id'));
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
        $user = User::find($id);
        return view('users.edit', compact('user', 'id'));
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
                $user->save();
                return redirect('/index/user')->with('status', 'User data has been edited.');
            }
            else
            {
                return back()->withErrors(['password' => ['Password confirmation is invalid.']]);
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
                $user->save();
                return redirect('/index/user')->with('status', 'User data has been edited.');
            }
            else
            {
                return back()->withErrors(['password' => ['Password confirmation is invalid.']]);
            }
        }


        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Username or e-mail already exist.');
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
        $date = date('Y-m-d H:i:s');
        $user = User::find($id);
        $user->deleted_at = $date;
        $user->save();
        //
        return redirect('/index/user')->with('status', 'User has been deleted.');
    }
}
