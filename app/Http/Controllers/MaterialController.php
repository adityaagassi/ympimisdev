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
	private $category;
	private $hpl;
  private $valcl;
  public function __construct()
  {
    $this->middleware('auth');
    $this->hpl = [
     'ASBELL&BOW',
     'ASBODY',
     'ASFG',
     'ASKEY',
     'ASNECK',
     'ASPAD',
     'ASPART',
     'CASE',
     'CLBARREL',
     'CLBELL',
     'CLFG',
     'CLKEY',
     'CLLOWER',
     'CLPART',
     'CLUPPER',
     'FLBODY',
     'FLFG',
     'FLFOOT',
     'FLHEAD',
     'FLKEY',
     'FLPAD',
     'FLPART',
     'MOUTHPIECE',
     'PN',
     'PN PARTS',
     'RC',
     'TSBELL&BOW',
     'TSBODY',
     'TSFG',
     'TSKEY',
     'TSNECK',
     'TSPART',
     'VENOVA',
   ];
   $this->category = [
     'FG',
     'KD',
     'WIP',
   ];
   $this->valcl = [
    '9010',
    '9030',
    '9040',
    '9041',
  ];
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
    	))->with('page', 'Material');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	$hpls = $this->hpl;
    	$categories = $this->category;
      $valcls = $this->valcl;
      $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();
      return view('materials.create', array(
        'origin_groups' => $origin_groups,
        'hpls' => $hpls,
        'valcls' => $valcls,
        'categories' => $categories,
      ))->with('page', 'Material');
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
    			'mrpc' => $request->get('mrpc'),
          'valcl' => $request->get('valcl'),
          'origin_group_code' => $request->get('origin_group_code'),
          'hpl' => $request->get('hpl'),
          'category' => $request->get('category'),
          'model' => $request->get('model'),
          'created_by' => $id
        ]);

    		$material->save();
    		return redirect('/index/material')->with('status', 'New material has been created.')->with('page', 'Material');

    	}
    	catch (QueryException $e){
    		$error_code = $e->errorInfo[1];
    		if($error_code == 1062){
    			return back()->with('error', 'Material number already exist.')->with('page', 'Material');
    		}
    		else{
    			return back()->with('error', $e->getMessage())->with('page', 'Material');
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
    	$material = Material::find($id);

    	return view('materials.show', array(
    		'material' => $material,
    	))->with('page', 'Material');
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
    	$hpls = $this->hpl;
    	$categories = $this->category;
      $valcls = $this->valcl;
      $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();
      $material = Material::find($id);
      return view('materials.edit', array(
        'material' => $material,
        'origin_groups' => $origin_groups,
        'hpls' => $hpls,
        'valcls' => $valcls,
        'categories' => $categories,
        // 'model' => $model,
      ))->with('page', 'Material');
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

    		$material = Material::find($id);
    		$material->material_number = $request->get('material_number');
    		$material->material_description = $request->get('material_description');
    		$material->base_unit = $request->get('base_unit');
    		$material->issue_storage_location = $request->get('issue_storage_location');
    		$material->mrpc = $request->get('mrpc');
        $material->valcl = $request->get('valcl');
        $material->origin_group_code = $request->get('origin_group_code');
        $material->hpl = $request->get('hpl');
        $material->category = $request->get('category');
        $material->model = $request->get('model');
        $material->save();

        return redirect('/index/material')->with('status', 'Material data has been edited.')->with('page', 'Material');

      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
         return back()->with('error', 'Material number already exist.')->with('page', 'Material');
       }
       else{
         return back()->with('error', $e->getMessage())->with('page', 'Material');
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
    	$material = Material::find($id);
    	$material->forceDelete();

    	return redirect('/index/material')->with('status', 'Material has been deleted.')->with('page', 'Material');
        //
    }

        /**
     * Import resource from Text File.
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
                $material_number = '';
                if(strlen($row[0]) == 6){
                  $material_number = "0" . $row[0];
                }
                elseif(strlen($row[0]) == 5){
                  $material_number = "00" . $row[0];
                }
                else{
                  $material_number = $row[0];
                }
                $origin_group_code = '';
                if(strlen($row[6]) == 2){
                  $origin_group_code = "0".$row[6];
                }
                else{
                  $origin_group_code = $row[6];
                }
                $material = new Material([
                  'material_number' => $material_number,
                  'material_description' => $row[1],
                  'base_unit' => $row[2],
                  'issue_storage_location' => $row[3],
                  'mrpc' => $row[4],
                  'valcl' => $row[5],
                  'origin_group_code' => $origin_group_code,
                  'hpl' => $row[7],
                  'category' => $row[8],
                  'model' => $row[9],
                  'created_by' => $id,
                ]);

                $material->save();
              }
            }
            return redirect('/index/material')->with('status', 'New materials has been imported.')->with('page', 'Material');

          }
          else
          {
            return redirect('/index/material')->with('error', 'Please select a file.')->with('page', 'Material');
          }
        }
      }
