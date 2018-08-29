<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Material;
use App\OriginGroup;
use Illuminate\Database\QueryException;
use File;

class MaterialController extends Controller
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
        $materials = Material::orderBy('material_number', 'ASC')
        ->get();

        return view('materials.index', array(
            'materials' => $materials
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
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();
        return view('materials.create', array(
            'origin_groups' => $origin_groups
        ));
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
            $material = new Material([
              'material_number' => $request->get('material_number'),
              'material_description' => $request->get('material_description'),
              'base_unit' => $request->get('base_unit'),
              'issue_storage_location' => $request->get('issue_storage_location'),
              'origin_group_code' => $request->get('origin_group_code'),
              'created_by' => $id
          ]);

            $material->save();
            return redirect('/index/material')->with('status', 'New material has been created.');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Material number already exist.');
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
        $material = Material::find($id);
        $material->forceDelete();

        return redirect('/index/material')->with('status', 'Material has been deleted.');
        //
    }

        /**
     * Import material from Text File
     *
     * @return List Transfer
     *
     */
        public function import(Request $request)
        {
            if($request->hasFile('material')){
                Material::truncate();

                $id = Auth::id();

                $file = $request->file('material');
                $data = file_get_contents($file);

                $rows = explode("\r\n", $data);
                foreach ($rows as $row)
                {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);
                        $material = new Material([
                            'material_number' => $row[0],
                            'material_description' => $row[1],
                            'base_unit' => $row[2],
                            'issue_storage_location' => $row[3],
                            'origin_group_code' => $row[4],
                            'created_by' => $id,
                        ]);

                        $material->save();
                    }
                }
                return redirect('/index/material')->with('status', 'New materials has been imported.');

            }
            else
            {
                return redirect('/index/material')->with('error', 'Please select a file.');
            }
            
        //
        }
    }
