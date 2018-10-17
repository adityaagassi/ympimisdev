<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Department;

class DepartmentController extends Controller
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
        $departments = Department::orderBy('ID', 'ASC')
        ->get();

        return view('departments.index', array(
            'departments' => $departments
        ))->with('page', 'Department');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('departments.create')->with('page', 'Department');
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
            $department = new Department([
                'department_code' => $request->get('department_code'),
                'department_name' => $request->get('department_name'),
                'created_by' => $id
            ]);

            $department->save();
            return redirect('/index/department')->with('status', 'New department has been created.')->with('page', 'Department');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Department code or department name already exist.')->with('page', 'Department');
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
        $department = Department::find($id);
        return view('departments.show', array(
            'department' => $department,
        ))->with('page', 'Department');
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
        $department = Department::find($id);
        return view('departments.edit', array(
            'department' => $department,
        ))->with('page', 'Department');
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

            $department = Department::find($id);
            $department->department_code = $request->get('department_code');
            $department->department_name = $request->get('department_name');
            $department->save();

            return redirect('/index/department')->with('status', 'Department data has been edited.')->with('page', 'Department');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Department code or department name already exist.')->with('page', 'Department');
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
        $department = Department::find($id);
        $department->delete();

        return redirect('/index/department')->with('status', 'Department has been deleted.')->with('page', 'Department');
        //
    }
}
