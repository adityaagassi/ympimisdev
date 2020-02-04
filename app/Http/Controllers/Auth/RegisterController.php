<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\EmployeeSync;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data) {
        $emp = EmployeeSync::where('employee_id', '=', strtolower($data['username']))->first();

        if(!$emp){
            // return redirect(url('register'))->with('error', 'NIK Not Exists')->with('page', 'Register');
            // return redirect('172.17.128.4/mirai/public');
            // $response = array(
            //     'status' => true,
            //     'error' => 'NIK Does Not Exists!',
            // );
            // return Response::json($response);
        }

        return User::create([
            'name' => ucwords($emp->name),
            'email' => strtolower($data['email']),
            'password' => bcrypt($data['password']),
            'username' => strtolower($data['username']),
            'role_code' => 'emp-srv',
            'avatar' => strtolower($data['username']).'jpg',
            'created_by' => '1'
        ]);
    }

    // protected function create(Request $data) {
    //     $emp = EmployeeSync::where('employee_id', '=', strtolower($data->get('username')))->first();

    //     if(!$emp){
    //         // return redirect(url('register'))->with('error', 'NIK Not Exists')->with('page', 'Register');
    //         // return redirect('172.17.128.4/mirai/public');
    //         // $response = array(
    //         //     'status' => true,
    //         //     'error' => 'NIK Does Not Exists!',
    //         // );
    //         // return Response::json($response);
    //     }

    //     return User::create([
    //         'name' => ucwords($emp->name),
    //         'email' => strtolower($data->get('email')),
    //         'password' => bcrypt($data->get('password')),
    //         'username' => strtolower($data->get('username')),
    //         'role_code' => 'emp-srv',
    //         'avatar' => 'image-user.png',
    //         'created_by' => '1'
    //     ]);
    // }
}
