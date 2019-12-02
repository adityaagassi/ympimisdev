<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\User;
use App\MasterBeacons;
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
		$cr = MasterBeacons::select('master_beacons.*')->get();

		return view('beacons.master_beacon.create', array(
			'cr' => $cr
		))->with('page', 'Master Beacon');
	}

	public function daftar(Request $request)
	{
		// insert data ke table master_cubeacons
		$id_user = Auth::id();

		$MasterBeacons = new MasterBeacons([
			'uuid' => $request->get('UUID'),
			'lokasi' => $request->get('lokasi'),
			'distance' => $request->get('distance')
			
		]);

		$MasterBeacons->save();

		return redirect('/index/master_beacon')
		->with('status', 'New Master Beacon has been created.')
		->with('page', 'Master Beacon List');
	}


	public function edit(Request $request)
	{
		try{
			$beacon = MasterBeacons::find($request->get("id"));
			$uuid = $beacon->uuid;
			$lokasi = $beacon->lokasi;
			$distance = $beacon->distance;
            // $beacon->uuid = $request->get('uuid');
            // $beacon->name = $request->get('name');
			

			$response = array(
				'status' => true,
				'uuid' => $uuid,
				'lokasi' => $lokasi,
				'distance' => $distance
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


	// public function jquerry_maker(elem1, elem2)
	// {
	// 	var e1Rect = elem1.getBoundingClientRect();
	// 	var e2Rect = elem2.getBoundingClientRect();
	// 	var dx = (e1Rect.left+(e1Rect.right-e1Rect.left)/2) - (e2Rect.left+(e2Rect.right-e2Rect.left)/2);
	// 	var dy = (e1Rect.top+(e1Rect.bottom-e1Rect.top)/2) - (e2Rect.top+(e2Rect.bottom-e2Rect.top)/2);
	// 	var dist = Math.sqrt(dx * dx + dy * dy);
	// 	return dist;
	// }

// function distance(lat1, lon1, lat2, lon2, unit) {
// 	if ((lat1 == lat2) && (lon1 == lon2)) {
// 		return 0;
// 	}
// 	else {
// 		var radlat1 = Math.PI * lat1/180;
// 		var radlat2 = Math.PI * lat2/180;
// 		var theta = lon1-lon2;
// 		var radtheta = Math.PI * theta/180;
// 		var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
// 		if (dist > 1) {
// 			dist = 1;
// 		}
// 		dist = Math.acos(dist);
// 		dist = dist * 180/Math.PI;
// 		dist = dist * 60 * 1.1515;
// 		if (unit=="K") { dist = dist * 1.609344 }
// 		if (unit=="N") { dist = dist * 0.8684 }
// 		return dist;
// 	}

// }
	// public function FunctionName()
	// {
	// 	$distance = 
	// }

	public function delete($id)
	{
		$beacon = MasterBeacons::find($id);
		$beacon->delete();

		return redirect('/index/master_beacon')
		->with('status', 'beacon has been deleted.')
		->with('page', 'beacon');
	}
}
