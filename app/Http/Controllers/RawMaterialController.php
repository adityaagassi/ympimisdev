<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use DataTables;
use Response;
use File;

class RawMaterialController extends Controller
{
	public function indexStorage(){
		return view('raw_materials.storage_location_stock')->with('page', 'Upload Storage')->with('head', 'Raw Material Monitoring');
	}

	public function indexSmbmr(){
		return view('raw_materials.material_list_by_model')->with('page', 'Upload SMBMR')->with('head', 'Raw Material Monitoring');
	}
}
