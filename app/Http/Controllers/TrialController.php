<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Libraries\ActMLEasyIf;
use Response;

class TrialController extends Controller
{
	public function tes(){

		$H = array_fill(0, 21, 0);
		$c = 0;
		$d =0;

		Max Container Per Hari ($c) = roundup(Total Container / Hari Ekspor);

		for($i=0; $i<$z; $i++){
			for ($x=$e; $x>0; $x--){

				$c++;
				$H[$x] = $H[$x]+1;

				if ($c == $t) {
					return $H;	
				}
			}
		}


	}
}
