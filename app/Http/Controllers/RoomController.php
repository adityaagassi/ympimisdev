<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\ActMLEasyIf;
use Response;

class RoomController extends Controller
{
	public function fetchToilet(Request $request)
	{
		$datas = array();

		$location = $request->get('location');

		if($location == 'buffing'){
			$plc = new ActMLEasyIf(1);
			for ($i=1; $i < 9 ; $i++) {
				array_push($datas, $plc->read_data('D'.$i, 1));
			}

			// $datas = [1,1,0,0,1,1,1,1,0,1,0];
		}
		if($location == 'office'){
			$plc = new ActMLEasyIf(2);
			for ($i=0; $i < 7 ; $i++) {
				array_push($datas, $plc->read_data('D0', $i));
			}

			// $datas = [1,1,0,0,1,1,1];
		}

		$response = array(
			'status' => true,
			'datas' => $datas
		);
		return Response::json($response);
	}

	public function indexToilet(){
		return view('rooms.index_toilet')->with('page', 'Toilet');
	}

	public function indexRoomToilet($id)
	{
		if($id == 'buffing'){
			$title = 'Buffing Toilet Information';
			$title_jp = '??';

			return view('rooms.buffingToilet', array(
				'title' => $title,
				'title_jp' => $title_jp
			))->with('page', 'toilet');			
		}
		if($id == 'office'){
			$title = 'Office Toilet Information';
			$title_jp = '??';

			return view('rooms.officeToilet', array(
				'title' => $title,
				'title_jp' => $title_jp
			))->with('page', 'toilet');

		}
	}
}
