<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class WeldingProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function indexWeldingFL(){
		return view('processes.welding.index_fl')->with('page', 'Welding Process FL');
	}
}
