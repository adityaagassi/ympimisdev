<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Material;
use DataTables;
use Response;
use App\OriginGroup;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
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
     'SX'
   ];
   $this->category = [
     'FG',
     'KD',
     'WIP',
     'RAW'
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
      $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();

      return view('materials.index', array(
        'valcls' => $this->valcl,
        'hpls' => $this->hpl,
        'categories' => $this->category,
        'origin_groups' => $origin_groups
      ))->with('page', 'Material');
        //
    }

    public function fetchMaterial()
    {
      $materials = Material::leftJoin("origin_groups","origin_groups.origin_group_code","=","materials.origin_group_code")
      ->orderBy('material_number', 'ASC')
      ->select("materials.id","materials.material_number","materials.material_description","materials.base_unit","materials.issue_storage_location","materials.mrpc","materials.valcl","origin_groups.origin_group_name","materials.hpl","materials.category","materials.model")
      ->get();

      return DataTables::of($materials)
      ->addColumn('action', function($materials){
        return '
        <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$materials->id.')">View</button>
        <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$materials->id.')">Edit</button>
        <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$materials->id.',\''.$materials->material_number.'\')">Delete</button>';
      })

      ->rawColumns(['action' => 'action'])
      ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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

        $response = array(
          'status' => true,
          'materials' => "New Material has been created."
        );
        return Response::json($response);
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
         $response = array(
          'status' => true,
          'materials' => "Material already exist"
        );
         return Response::json($response);
       }
       else{
         $response = array(
          'status' => true,
          'materials' => "Material not created."
        );
         return Response::json($response);
       }
     }

   }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request)
    {
    	$query = "select mat.material_number, mat.base_unit, mat.issue_storage_location, users.`name`, material_description, origin_group_name, mat.created_at, mat.updated_at, mat.hpl, mat.category, mat.mrpc, mat.valcl from
      (select material_number, material_description, base_unit, issue_storage_location, mrpc, valcl, origin_group_code, hpl, category, created_by, created_at, updated_at from materials where id = "
      .$request->get('id').") as mat
      left join origin_groups on origin_groups.origin_group_code = mat.origin_group_code
      left join users on mat.created_by = users.id";

      $material = DB::select($query);

      $response = array(
        'status' => true,
        'datas' => $material,
      );
      return Response::json($response);
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchEdit(Request $request)
    {
    	$hpls = $this->hpl;
    	$categories = $this->category;
      $valcls = $this->valcl;
      $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();
      $material = Material::find($request->get("id"));

      $response = array(
        'status' => true,
        'datas' => $material,
      );
      return Response::json($response);
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
    	try{
    		$material = Material::find($request->get("id"));
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

        $response = array(
          'status' => true
        );
        return Response::json($response);

      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
         $response = array(
          'status' => true,
          'datas' => "Material already exist",
        );
         return Response::json($response);
       }
       else{
         $response = array(
          'status' => true,
          'datas' => "Update Material Error.",
        );
         return Response::json($response);
       }
     }
   }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
    	$material = Material::find($request->get("id"));
    	$material->forceDelete();

    	$response = array(
        'status' => true
      );
      return Response::json($response);
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
