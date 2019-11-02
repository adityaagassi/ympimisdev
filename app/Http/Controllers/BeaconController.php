<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\User;
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
}
