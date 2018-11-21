<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WjoController extends Controller
{

	function public index_wjo(){
		return view("wjos.mis.index");
	}
    //
}
