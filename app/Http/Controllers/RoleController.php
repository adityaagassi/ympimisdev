<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Role;
use App\Navigation;
use App\Permission;
use Illuminate\Database\QueryException;

class RoleController extends Controller
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
        $roles = Role::orderBy('id', 'ASC')
        ->get();

        return view('roles.index', array(
            'roles' => $roles
        ))->with('page', 'Role');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $navigations = Navigation::orderBy('ID', 'asc')
        ->get();

        return view('roles.create', array(
            'navigations' => $navigations,
        ))->with('page', 'Role');
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
            $role = new Role([
                'role_code' => $request->get('role_code'),
                'role_name' => $request->get('role_name'),
                'created_by' => $id
            ]);
            $role->save();

            $navigations = $request->get('navigation_code');

            foreach ($navigations as $navigation) {
                $permission = new Permission([
                    'role_code' => $request->get('role_code'),
                    'navigation_code' => $navigation,
                    'created_by' => $id
                ]);
                $permission->save();
            }

            return redirect('/index/role')->with('status', 'New role has been created.')->with('page', 'Role');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Role code already exist.')->with('page', 'Destination');
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
        $role = Role::find($id);
        $permissions = Permission::where('role_code', '=', $role->role_code)->orderBy('id', 'asc')->get();

        return view('roles.show', array(
            'role' => $role,
            'permissions' => $permissions,
        ))->with('page', 'Role');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $navigations = Navigation::orderBy('id', 'asc')->get();
        $permissions = Permission::where('role_code', '=', $role->role_code)->orderBy('id', 'asc')->get();

        $perm[] = '';
        foreach ($permissions as $permission) {
            $perm[] = $permission->navigation_code;
        }

        return view('roles.edit', array(
            'role' => $role,
            'navigations' => $navigations,
            'permissions' => $perm,
        ))->with('page', 'Role');
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

            $role = Role::find($id);
            $role->role_name = $request->get('role_name');
            $role->save();

            $permissions = Permission::where('role_code', '=', $role->role_code);
            $permissions->forceDelete();

            $navigations = $request->get('navigation_code');

            foreach ($navigations as $navigation) {
                $permission = new Permission([
                    'role_code' => $role->role_code,
                    'navigation_code' => $navigation,
                    'created_by' => $id
                ]);
                $permission->save();
            }

            return redirect('/index/role')->with('status', 'Role data has been edited.')->with('page', 'Role');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Role name already exist.')->with('page', 'Role');
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
        $role = Role::find($id);

        $permissions = Permission::where('role_code', '=', $role->role_code);

        $permissions->forceDelete();
        $role->forceDelete();

        return redirect('/index/role')->with('status', 'Role has been deleted.')->with('page', 'Role');
        //
    }
}