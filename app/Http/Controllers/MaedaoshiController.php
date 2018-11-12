<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use DataTables;
use Yajra\DataTables\Exception;

class MaedaoshiController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function index(){
		return view('flos.flo_maedaoshi')
		->with('page', 'FLO Maedaoshi');
	}
    //
}
