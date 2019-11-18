<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\User;
use App\CubeaconReader;
use Response;
use DataTables;

class BeaconController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{	
		return view('beacons.warehouse.map')->with('page', 'cubeacon');
	}

	public function getUser()
	{
		$users = User::select('major','minor',db::raw('acronym(name) as kode'))
		->get();

		$response = array(
			'status' => true,
			'data' => $users,
		);
		return Response::json($response);
	}

	
	public function master_beacon()
	{
		$cr = CubeaconReader::select('cubeacon_readers.*')->get();

		return view('beacons.master_beacon.create', array(
			'cr' => $cr
		))->with('page', 'Master Beacon');
	}

	public function daftar(Request $request)
	{
		// insert data ke table pegawai
		$id_user = Auth::id();

		$CubeaconReader = new CubeaconReader([
			'uuid' => $request->get('UUID'),
			'name' => $request->get('Name'),
			'created_by' => $id_user
		]);

		$CubeaconReader->save();

		return redirect('/index/master_beacon')
		->with('status', 'New Master Beacon has been created.')
		->with('page', 'Master Beacon List');
	}


	public function edit(Request $request)
	{
		try{
			$beacon = CubeaconReader::find($request->get("id"));
			$uuid = $beacon->uuid;
			$name = $beacon->name;
            // $beacon->uuid = $request->get('uuid');
            // $beacon->name = $request->get('name');
			

			$response = array(
				'status' => true,
				'uuid' => $uuid,
				'name' => $name
			);
			return Response::json($response);

		}
		catch (QueryException $beacon){
			$error_code = $beacon->errorInfo[1];
			if($error_code == 1062){
				$response = array(
					'status' => false,
					'datas' => "Name already exist",
				);
				return Response::json($response);
			}
			else{
				$response = array(
					'status' => false,
					'datas' => "Update  Error.",
				);
				return Response::json($response);
			}
		}
	}

	public function delete($id)
	{
		$beacon = CubeaconReader::find($id);
		$beacon->delete();

		return redirect('/index/master_beacon')
		->with('status', 'beacon has been deleted.')
		->with('page', 'beacon');
	}
}
