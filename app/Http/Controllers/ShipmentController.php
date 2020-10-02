<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShipmentController extends Controller
{
	public function indexShipmentProgress(){
		$title = "Shipment Progress";
		$title_jp = "出荷結果";

		return view('shipments.shipment_progress', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', $title)->with('head', $title);
	}
}
