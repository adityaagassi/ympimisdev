<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Material;
use App\MaterialVolume;
use Illuminate\Database\QueryException;

class MaterialVolumeController extends Controller
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
        $material_volumes = MaterialVolume::orderBy('material_number', 'ASC')
        ->get();

        return view('material_volumes.index', array(
            'material_volumes' => $material_volumes
        ))->with('page', 'Material Volume');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $materials = Material::orderBy('material_number', 'ASC')->get();
        return view('material_volumes.create', array(
            'materials' => $materials
        ))->with('page', 'Material Volume');
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
            $material_volume = new MaterialVolume([
              'material_number' => $request->get('material_number'),
              'category' => $request->get('category'),
              'lot' => $request->get('lot'),
              'length' => $request->get('length'),
              'width' => $request->get('width'),
              'height' => $request->get('height'),
              'created_by' => $id
          ]);

            $material_volume->save();
            return redirect('/index/material_volume')->with('status', 'New material volume has been created.')->with('page', 'Material Volume');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Material volume already exist.')->with('page', 'Material Volume');
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
        $material_volume = MaterialVolume::find($id);
        return view('material_volumes.show', array(
            'material_volume' => $material_volume,
        ))->with('page', 'Material Volume');
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
        $material_volume = MaterialVolume::find($id);
        return view('material_volumes.edit', array(
            'material_volume' => $material_volume,
        ))->with('page', 'Material Volume');
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
            $material_volume = MaterialVolume::find($id);
            $material_volume->category = $request->get('category');
            $material_volume->lot = $request->get('lot');
            $material_volume->length = $request->get('length');
            $material_volume->width = $request->get('width');
            $material_volume->height = $request->get('height');
            $material_volume->save();

            return redirect('/index/material_volume')->with('status', 'Material volume data has been edited.')->with('page', 'Material Volume');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Material volume already exist.')->with('page', 'Material Volume');
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
        $material_volume = MaterialVolume::find($id);
        $material_volume->forceDelete();

        return redirect('/index/material_volume')->with('status', 'Material volume has been deleted.')->with('page', 'Material Volume');
        //
    }

    /**
     * Import material volume from textfile.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        if($request->hasFile('material_volume')){
                MaterialVolume::truncate();

                $id = Auth::id();

                $file = $request->file('material_volume');
                $data = file_get_contents($file);

                $rows = explode("\r\n", $data);
                foreach ($rows as $row)
                {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);
                        $material_volume = new MaterialVolume([
                            'material_number' => $row[0],
                            'category' => $row[1],
                            'lot' => $row[2],
                            'length' => $row[3],
                            'width' => $row[4],
                            'height' => $row[5],
                            'created_by' => $id,
                        ]);

                        $material_volume->save();
                    }
                }
                return redirect('/index/material_volume')->with('status', 'New material volumes has been imported.')->with('page', 'Material Volume');

            }
            else
            {
                return redirect('/index/material_volume')->with('error', 'Please select a file.')->with('page', 'Material Volume');
            }
        //
    }
}
