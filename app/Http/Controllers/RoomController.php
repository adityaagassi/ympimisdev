<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Libraries\ActMLEasyIf;
use Response;

class RoomController extends Controller
{
	public function fetchPLC()
	{	
		$datas = [1,1,0,0,1,1,1,1,0,1,0];


		// $plc = new ActMLEasyIf(0);
		// $data = $plc->read_data('D0', 5);

		$response = array(
			'status' => true,
			'datas' => $datas
		);
		return Response::json($response);
	}

	public function indexBuffingToilet()
	{
		$title = 'Buffing Toilet Information';
		$title_jp = '??';

		return view('rooms.buffingToilet', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'toilet');
	}
}
