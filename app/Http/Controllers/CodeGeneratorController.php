<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\CodeGenerator;
use App\User;

class CodeGeneratorController extends Controller
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
        $code_generators = CodeGenerator::orderBy('note', 'ASC')
        ->get();

        return view('code_generators.index', array(
            'code_generators' => $code_generators
        ))->with('page', 'Code Generator');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('code_generators.create')->with('page', 'Code Generator');;
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
        try
        {
            $id = Auth::id();
            $code_generator = new CodeGenerator([
              'prefix' => $request->get('prefix'),
              'length' => $request->get('length'),
              'index' => $request->get('index'),
              'note' => $request->get('note'),
              'created_by' => $id
          ]);

            $code_generator->save();
            return redirect('/index/code_generator')->with('status', 'New code generator has been created.')->with('page', 'Code Generator');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Code generator with preferred note already exist.')->with('page', 'Code Generator');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Code Generator');
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
        $code_generator = CodeGenerator::find($id);
        $users = User::orderBy('name', 'ASC')->get();

        return view('code_generators.show', array(
            'code_generator' => $code_generator,
            'users' => $users,
        ))->with('page', 'Code Generator');
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
        $code_generator = CodeGenerator::find($id);

        return view('code_generators.edit', array(
            'code_generator' => $code_generator
        ))->with('page', 'Code Generator');
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

            $code_generator = CodeGenerator::find($id);
            $code_generator->prefix = $request->get('prefix');
            $code_generator->length = $request->get('length');
            $code_generator->index = $request->get('index');
            $code_generator->note = $request->get('note');
            $code_generator->save();

            return redirect('/index/code_generator')->with('status', 'Code generator data has been edited.')->with('page', 'Code Generator');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Code generator note ready exist.')->with('page', 'Code Generator');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Code Generator');
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
        $code_generator = CodeGenerator::find($id);
        $code_generator->forceDelete();

        return redirect('/index/code_generator')
        ->with('status', 'Code generator has been deleted.')
        ->with('page', 'Code Generator');
    }
}
