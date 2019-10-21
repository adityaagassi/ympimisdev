<?php

namespace App\Http\Controllers;

// use Maatwebsite\Excel\Excel;

use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Excel;
use App\Exports\POExport;
use Response;
use File;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\PurchaseOrder;
use App\CodeGenerator;
use App\PoList;
use App\PoListFile;
use App\Http\Controllers\Controller;

class PurchaseOrderController extends Controller
{
	private $excel;
	public function __construct(Excel $excel){
		$this->middleware('auth');
		$this->pgr = [
			'G01',
			'G08',
		];
		$this->excel = $excel;
	}

	public function indexPoList(){
		$pgrs = $this->pgr;
		return view('purchase_orders.list', array(
			'pgrs' => $pgrs,
		))->with('page', 'Purchase Order List')->with('head', 'Purchase Order');
	}

	public function indexPoArchive(){
		$pgrs = $this->pgr;
		return view('purchase_orders.archive', array(
			'pgrs' => $pgrs,
		))->with('page', 'Purchase Order Archive')->with('head', 'Purchase Order');
	}

	public function indexPoCreate(){
		$code_generator = CodeGenerator::where('note', '=', 'PO')->first();
		if($code_generator->prefix != strtoupper(date('My-'))){
			db::table('code_generators')->where('note', '=', 'PO')->update([
				'prefix' => strtoupper(date('My-')),
				'index' => 0,
			]);
		}
		$pgrs = $this->pgr;
		$shipment_conditions = db::table('shipment_conditions')->orderBy('shipment_condition_code', 'asc')->get();
		return view('purchase_orders.create', array(
			'pgrs' => $pgrs,
			'shipment_conditions' => $shipment_conditions,
		))->with('page', 'Purchase Order Create')->with('head', 'Purchase Order');
	}

	public function indexPoRevise(){
		$pgrs = $this->pgr;
		$shipment_conditions = db::table('shipment_conditions')->orderBy('shipment_condition_code', 'asc')->get();
		return view('purchase_orders.revise', array(
			'pgrs' => $pgrs,
			'shipment_conditions' => $shipment_conditions,
		))->with('page', 'Purchase Order Create')->with('head', 'Purchase Order');
	}

	public function generatePoRevise(Request $request){
		$orderNos = explode(",", $request->get('orderNo'));
		foreach ($orderNos as $orderNo) {
			$check = PurchaseOrder::where('order_no', '=', $orderNo)->first();
			if($check == null){
				$response = array(
					'status' => false,
					'message' => 'Order code not found.'
				);
				return Response::json($response);
			}
		}

		if(strlen($request->get('delivDate')) > 0 && strlen($request->get('orderNo')) > 0 && strlen($request->get('shipmentCondition')) >0){

			$id = Auth::id();
			$shipment_condition = db::table('shipment_conditions')->where('shipment_conditions.shipment_condition_code', '=', $request->get('shipmentCondition'))->first();
			$purchase_orders = db::table('purchase_orders')->whereIn('purchase_orders.order_no', $orderNos)
			->leftJoin('po_lists', function($join){
				$join->on('po_lists.purchdoc', '=', 'purchase_orders.purchdoc');
				$join->on('po_lists.item', '=', 'purchase_orders.item');
			})
			->update([
				'purchase_orders.order_qty' => db::raw('po_lists.order_qty'),
				'purchase_orders.price' => db::raw('po_lists.price'),
				'purchase_orders.amount' => db::raw('po_lists.order_qty*po_lists.price'),
				'purchase_orders.sc' => $shipment_condition->shipment_condition_code,
				'purchase_orders.sc_name' => $shipment_condition->shipment_condition_name,
				'purchase_orders.rev_no' => db::raw('purchase_orders.rev_no+1'),
				'purchase_orders.rev_date' => date('Y-m-d'),
				'purchase_orders.deliv_date' => date('Y-m-d', strtotime($request->get('delivDate'))),
				'purchase_orders.updated_at' => date('Y-m-d H:i:s'),
			]);

			$paths = array();
			foreach ($orderNos as $orderNo) {
				$purchase_orders = PurchaseOrder::where('order_no', '=', $orderNo)
				->select('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', db::raw('group_concat(item) as item'), 'material', 'description', 'deliv_date', db::raw('sum(order_qty) as order_qty'), 'base_unit_of_measure', db::raw('sum(price) as price'), db::raw('sum(amount) as amount'))
				->groupBy('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', 'material', 'description', 'deliv_date', 'base_unit_of_measure')
				->get();
				$total_amount = PurchaseOrder::where('order_no', '=', $orderNo)->sum('amount');

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setPaper('A4', 'potrait');
				$pdf->loadView('purchase_orders.purchase_order_pdf', array(
					'purchase_orders' => $purchase_orders,
				));
				$pdf->save(public_path() . "/purchase_orders/" . $purchase_orders[0]->order_no . '-R' . $purchase_orders[0]->rev_no . ".pdf");


				$path = "purchase_orders/" . $purchase_orders[0]->order_no . '-R' . $purchase_orders[0]->rev_no . ".pdf";
				array_push($paths, 
					[
						"download" => asset($path),
						"filename" => $purchase_orders[0]->order_no . '-R' . $purchase_orders[0]->rev_no . ".pdf"
					]);


				if($pdf){
					$po_file = PolistFile::updateOrCreate([
						'order_no' => $orderNo, 
						'file_name' => $purchase_orders[0]->order_no . '-R' . $purchase_orders[0]->rev_no . '.pdf'
					],
					[
						'order_no' => $orderNo,
						'file_name' => $purchase_orders[0]->order_no . '-R' . $purchase_orders[0]->rev_no . '.pdf',
						'created_by' => $id
					]);
				}
			}
			$response = array(
				'status' => true,
				'message' => 'Purchase order(s) revised.',
				'file_paths' => $paths,
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'All parameters must be filled.',
			);
			return Response::json($response);
		}
	}

	public function generatePoRevise2(Request $request){
		if($request->hasFile('filePurchaseOrder')){
			$id = Auth::id();
			$file = $request->file('filePurchaseOrder');
			$data = file_get_contents($file);
			$rows = explode("\r\n", $data);
			$purchdocitems = array();
			foreach ($rows as $row){
				if (strlen($row) > 0) {
					$row = explode("\t", $row);
					try{
						if(preg_match('/^[0-9]+$/', $row[0])){
							if(!in_array($row[0] . sprintf("%'.0" . 5 . "d", trim($row[1], ' ')), $purchdocitems)){
								array_push($purchdocitems, $row[0] . sprintf("%'.0" . 5 . "d", trim($row[1], ' ')));
							}

							$po_list = db::table('purchase_order_temporaries')->insert([
								'purchdoc' => $row[0],
								'item' => sprintf("%'.0" . 5 . "d", trim($row[1], ' ')),
								'deliv_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[2]))),
								'order_qty' => str_replace('"','',str_replace(',','',$row[3]))
							]);

							$temporaries = db::table('purchase_order_temporaries')
							->where(db::raw('concat(purchase_order_temporaries.purchdoc, purchase_order_temporaries.item)'), '=', $row[0] . sprintf("%'.0" . 5 . "d", trim($row[1], ' ')))
							->leftJoin('purchase_orders', function($join){
								$join->on('purchase_orders.purchdoc', '=', 'purchase_order_temporaries.purchdoc');
								$join->on('purchase_orders.item', '=', 'purchase_order_temporaries.item');
							})
							->update([
								'purchase_order_temporaries.rev_no' => db::raw('purchase_orders.rev_no+1'),
								'purchase_order_temporaries.order_date' => db::raw('purchase_orders.order_date'),
								'purchase_order_temporaries.order_no' => db::raw('purchase_orders.order_no'),
							]);
						}
					}
					catch(\Exception $e){
						db::table('purchase_order_temporaries')->truncate();
						return response()->json([
							'status' => false,
							'message' => $e->getMessage(),
						]);
					}
				}
			}

			$delete_po = PurchaseOrder::whereIn(db::raw('concat(purchdoc, item)'), $purchdocitems)
			->forceDelete();

			$check = db::table('purchase_order_temporaries')
			->leftJoin('po_lists', function($join){
				$join->on('po_lists.purchdoc', '=', 'purchase_order_temporaries.purchdoc');
				$join->on('po_lists.item', '=', 'purchase_order_temporaries.item');
			})
			->select('purchase_order_temporaries.purchdoc', 'purchase_order_temporaries.item', 'po_lists.order_qty', db::raw('sum(purchase_order_temporaries.order_qty) as new_qty'))
			->groupBy('purchase_order_temporaries.purchdoc', 'purchase_order_temporaries.item', 'po_lists.order_qty')
			->having('po_lists.order_qty', '<>', db::raw('sum(purchase_order_temporaries.order_qty)'))
			->first();

			if($check != null){
				db::table('purchase_order_temporaries')->truncate();
				return response()->json([
					'status' => false,
					'message' => 'Order quantity did not match with po list.',
				]);
			}

			$po_lists = db::table('purchase_order_temporaries')
			->leftJoin('po_lists', function($join){
				$join->on('po_lists.purchdoc', '=', 'purchase_order_temporaries.purchdoc');
				$join->on('po_lists.item', '=', 'purchase_order_temporaries.item');
			})
			->leftJoin('vendors', 'vendors.vendor', '=', 'po_lists.vendor')
			->leftJoin('mrps', 'mrps.mrp', '=', 'po_lists.pgr')
			->leftJoin('countries', 'countries.country_code', '=', 'vendors.cty')
			->leftJoin('payment_terms', 'vendors.tpay', '=', 'payment_terms.payment_code')
			->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'vendors.sc')
			->select('po_lists.purchdoc', 'purchase_order_temporaries.order_no', 'purchase_order_temporaries.order_date', 'po_lists.pgr', db::raw('mrps.name as pgr_name'), 'purchase_order_temporaries.rev_no', 'po_lists.vendor', db::raw('vendors.name as vendor_name'), 'vendors.street', 'vendors.city', 'vendors.postl_code', 'countries.country_name', 'vendors.salesperson', 'vendors.sc', 'shipment_conditions.shipment_condition_name', 'vendors.tpay', 'payment_terms.payment_name', 'vendors.telephone_1', 'vendors.fax_number', 'vendors.incot', 'vendors.crcy', 'purchase_order_temporaries.item', 'po_lists.material', 'po_lists.description', 'purchase_order_temporaries.deliv_date', 'purchase_order_temporaries.order_qty', 'po_lists.base_unit_of_measure', 'po_lists.price', db::raw('round(po_lists.price*purchase_order_temporaries.order_qty, 2) as amount'))
			->orderBy('po_lists.vendor', 'asc')->orderBy('purchase_order_temporaries.purchdoc', 'asc')->orderBy('purchase_order_temporaries.item', 'asc')
			->get();


			$orders = array();

			foreach ($po_lists as $po_list) {
				if(!in_array($po_list->order_no, $orders)){
					array_push($orders, $po_list->order_no);
				}
				$data = [
					'purchdoc' => $po_list->purchdoc,
					'order_no' => $po_list->order_no,
					'order_date' => $po_list->order_date,
					'pgr' => $po_list->pgr,
					'pgr_name' => $po_list->pgr_name,
					'rev_no' => $po_list->rev_no,
					'rev_date' => date('Y-m-d'),
					'vendor' => $po_list->vendor,
					'name' => $po_list->vendor_name,
					'street' => $po_list->street,
					'city' => $po_list->city,
					'postl_code' => $po_list->postl_code,
					'cty' => $po_list->country_name,
					'salesperson' => $po_list->salesperson,
					'sc' => $po_list->sc,
					'sc_name' => $po_list->shipment_condition_name,
					'tpay' => $po_list->tpay,
					'tpay_name' => $po_list->payment_name,
					'telephone' => $po_list->telephone_1,
					'fax_number' => $po_list->fax_number,
					'incot' => $po_list->incot,
					'curr' => $po_list->crcy,
					'item' => $po_list->item,
					'material' => $po_list->material,
					'description' => $po_list->description,
					'deliv_date' => $po_list->deliv_date,
					'order_qty' => $po_list->order_qty,
					'base_unit_of_measure' => $po_list->base_unit_of_measure,
					'price' => $po_list->price,
					'amount' => $po_list->amount,
					'created_by' => $id,
				];
				try{
					$purchase_order = new PurchaseOrder($data);
					$purchase_order->save();	
				}
				catch(\Exception $e){
					return response()->json([
						'status' => false,
						'message' => $e->getMessage(),
					]);
				}
			}

			$paths = array();
			foreach ($orders as $order) {
				$purchase_orders = PurchaseOrder::where('order_no', '=', $order)
				->select('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', db::raw('group_concat(item) as item'), 'material', 'description', 'deliv_date', db::raw('sum(order_qty) as order_qty'), 'base_unit_of_measure', db::raw('sum(price) as price'), db::raw('sum(amount) as amount'))
				->groupBy('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', 'material', 'description', 'deliv_date', 'base_unit_of_measure')
				->get();
				$total_amount = PurchaseOrder::where('order_no', '=', $order)->sum('amount');

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setPaper('A4', 'potrait');
				$pdf->loadView('purchase_orders.purchase_order_pdf', array(
					'purchase_orders' => $purchase_orders,
				));
				$pdf->save(public_path() . "/purchase_orders/" . $order . '-R' . $purchase_orders[0]->rev_no . ".pdf");


				$path = "purchase_orders/" . $order . '-R' . $purchase_orders[0]->rev_no . ".pdf";
				array_push($paths, 
					[
						"download" => asset($path),
						"filename" => $order . '-R' . $purchase_orders[0]->rev_no . ".pdf"
					]);

				if($pdf){
					$po_file = new PolistFile([
						'order_no' => $order,
						'file_name' => $order . '-R' . $purchase_orders[0]->rev_no . '.pdf',
						'created_by' => $id
					]);
					$po_file->save();
				}
			}

			
			db::table('purchase_order_temporaries')->truncate();
			$response = array(
				'status' => true,
				'message' => 'Purchase order(s) revised.',
				'file_paths' => $paths,
			);
			return Response::json($response);

		}
		else{
			return response()->json([
				'status' => false,
				'message' => 'Please select a file to generate.',
			]);
		}
	}

	public function generatePoRevise3(Request $request){
		$orderNos = explode(",", $request->get('orderNo'));
		foreach ($orderNos as $orderNo) {
			$check = PurchaseOrder::where('order_no', '=', $orderNo)->first();
			if($check == null){
				$response = array(
					'status' => false,
					'message' => 'Order code not found.'
				);
				return Response::json($response);
			}
		}

		if(strlen($request->get('delivDate')) > 0 && strlen($request->get('orderNo')) > 0 && strlen($request->get('shipmentCondition')) >0){

			$id = Auth::id();
			$shipment_condition = db::table('shipment_conditions')->where('shipment_conditions.shipment_condition_code', '=', $request->get('shipmentCondition'))->first();
			$purchase_orders = db::table('purchase_orders')->whereIn('purchase_orders.order_no', $orderNos)
			->leftJoin('po_lists', function($join){
				$join->on('po_lists.purchdoc', '=', 'purchase_orders.purchdoc');
				$join->on('po_lists.item', '=', 'purchase_orders.item');
			})
			->update([
				'purchase_orders.order_qty' => db::raw('po_lists.order_qty'),
				'purchase_orders.price' => db::raw('po_lists.price'),
				'purchase_orders.amount' => db::raw('po_lists.order_qty*po_lists.price'),
				'purchase_orders.sc' => $shipment_condition->shipment_condition_code,
				'purchase_orders.sc_name' => $shipment_condition->shipment_condition_name,
				'purchase_orders.rev_no' => db::raw('purchase_orders.rev_no+1'),
				'purchase_orders.rev_date' => date('Y-m-d'),
				'purchase_orders.deliv_date' => date('Y-m-d', strtotime($request->get('delivDate'))),
				'purchase_orders.updated_at' => date('Y-m-d H:i:s'),
			]);

			$paths = array();
			foreach ($orderNos as $orderNo) {
				$purchase_orders = PurchaseOrder::where('order_no', '=', $orderNo)
				->select('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', db::raw('group_concat(item) as item'), 'material', 'description', 'deliv_date', db::raw('sum(order_qty) as order_qty'), 'base_unit_of_measure', db::raw('sum(price) as price'), db::raw('sum(amount) as amount'))
				->groupBy('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', 'material', 'description', 'deliv_date', 'base_unit_of_measure')
				->get();
				$total_amount = PurchaseOrder::where('order_no', '=', $orderNo)->sum('amount');

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setPaper('A4', 'potrait');
				$pdf->loadView('purchase_orders.purchase_order_pdf', array(
					'purchase_orders' => $purchase_orders,
				));
				$pdf->save(public_path() . "/purchase_orders/" . $purchase_orders[0]->order_no . '-R' . $purchase_orders[0]->rev_no . ".pdf");


				$path = "purchase_orders/" . $purchase_orders[0]->order_no . '-R' . $purchase_orders[0]->rev_no . ".pdf";
				array_push($paths, 
					[
						"download" => asset($path),
						"filename" => $purchase_orders[0]->order_no . '-R' . $purchase_orders[0]->rev_no . ".pdf"
					]);

				if($pdf){
					$po_file = new PolistFile([
						'order_no' => $orderNo,
						'file_name' => $purchase_orders[0]->order_no . '-R' . $purchase_orders[0]->rev_no . '.pdf',
						'created_by' => $id
					]);
					$po_file->save();
				}
			}
			$response = array(
				'status' => true,
				'message' => 'Purchase order(s) revised.',
				'file_paths' => $paths,
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'All parameters must be filled.',
			);
			return Response::json($response);
		}
	}

	public function generatePoCreate(Request $request){
		$purchdoc = explode(",", $request->get('purchdoc'));

		$po_list_check = PoList::whereIn('purchdoc', $purchdoc)->where('remark', '=', 'converted')->first();

		if($po_list_check != null){
			$response = array(
				'status' => false,
				'message' => 'Purchase doc. ' . $po_list_check->purchdoc . ' already converted.',
			);
			return Response::json($response);
		}

		if(strlen($request->get('delivDate')) > 0 && strlen($request->get('purchdoc')) > 0 && strlen($request->get('shipmentCondition')) >0){
			$id = Auth::id();

			$po_lists = db::table('po_lists')->whereIn('po_lists.purchdoc', $purchdoc)
			->leftJoin('vendors', 'vendors.vendor', '=', 'po_lists.vendor')
			->leftJoin('materials', 'materials.material_number', '=', 'po_lists.material')
			->leftJoin('mrps', 'mrps.mrp', '=', 'po_lists.pgr')
			->leftJoin('countries', 'countries.country_code', '=', 'vendors.cty')
			->leftJoin('payment_terms', 'vendors.tpay', '=', 'payment_terms.payment_code')
			->select('po_lists.purchdoc', 'po_lists.pgr', db::raw('mrps.name as pgr_name'), 'po_lists.vendor', db::raw('vendors.name as vendor_name'), 'vendors.street', 'vendors.city', 'vendors.postl_code', 'countries.country_name', 'vendors.salesperson', 'vendors.tpay', 'payment_terms.payment_name', 'vendors.telephone_1', 'vendors.fax_number', 'vendors.incot', 'vendors.crcy', 'po_lists.item', 'po_lists.material', 'po_lists.description', 'po_lists.order_qty', 'po_lists.base_unit_of_measure', 'po_lists.price', db::raw('round(po_lists.price*po_lists.order_qty, 2) as amount'), 'materials.hpl')
			->orderBy('materials.hpl', 'asc')->orderBy('po_lists.material', 'asc')->orderBy('po_lists.purchdoc', 'asc')->orderBy('po_lists.item', 'asc')
			->get();

			$shipment_condition = db::table('shipment_conditions')->where('shipment_conditions.shipment_condition_code', '=', $request->get('shipmentCondition'))->first();

			$hpls = array();
			$orders = array();

			foreach ($po_lists as $po_list) {
				if(!in_array($po_list->hpl, $hpls)){
					array_push($hpls, $po_list->hpl);
					$code_generator = CodeGenerator::where('note', '=', 'PO')->first();
					$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
					$order_no = $po_list->hpl . '-' . $code_generator->prefix . $number;
					array_push($orders, $order_no);
					$code_generator->index = $code_generator->index+1;
					$code_generator->save();
				}
				$data = [
					'purchdoc' => $po_list->purchdoc,
					'order_no' => $order_no,
					'order_date' => date('Y-m-d'),
					'pgr' => $po_list->pgr,
					'pgr_name' => $po_list->pgr_name,
					'rev_no' => 0,
					'vendor' => $po_list->vendor,
					'name' => $po_list->vendor_name,
					'street' => $po_list->street,
					'city' => $po_list->city,
					'postl_code' => $po_list->postl_code,
					'cty' => $po_list->country_name,
					'salesperson' => $po_list->salesperson,
					'sc' => $shipment_condition->shipment_condition_code,
					'sc_name' => $shipment_condition->shipment_condition_name,
					'tpay' => $po_list->tpay,
					'tpay_name' => $po_list->payment_name,
					'telephone' => $po_list->telephone_1,
					'fax_number' => $po_list->fax_number,
					'incot' => $po_list->incot,
					'curr' => $po_list->crcy,
					'item' => $po_list->item,
					'material' => $po_list->material,
					'description' => $po_list->description,
					'deliv_date' => date('Y-m-d', strtotime($request->get('delivDate'))),
					'order_qty' => $po_list->order_qty,
					'base_unit_of_measure' => $po_list->base_unit_of_measure,
					'price' => $po_list->price,
					'amount' => $po_list->amount,
					'created_by' => $id,
				];
				try{
					$purchase_order = new PurchaseOrder($data);
					$purchase_order->save();	
				}
				catch(\Exception $e){
					return response()->json([
						'status' => false,
						'message' => $e->getMessage(),
					]);
				}
			}

			$paths = array();
			foreach ($orders as $order) {
				//will be edited for summary PO
				$purchase_orders = PurchaseOrder::where('order_no', '=', $order)
				->select('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', db::raw('group_concat(item) as item'), 'material', 'description', 'deliv_date', db::raw('sum(order_qty) as order_qty'), 'base_unit_of_measure', 'price', db::raw('sum(amount) as amount'))
				->groupBy('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', 'material', 'description', 'deliv_date', 'base_unit_of_measure', 'price')
				->orderBy('material', 'asc')->orderBy('purchdoc', 'asc')->orderBy('item', 'asc')
				->get();
				$total_amount = PurchaseOrder::where('order_no', '=', $order)->sum('amount');

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setPaper('A4', 'potrait');
				$pdf->loadView('purchase_orders.purchase_order_pdf', array(
					'purchase_orders' => $purchase_orders,
				));
				$pdf->save(public_path() . "/purchase_orders/" . $order . ".pdf");


				$path = "purchase_orders/" . $order . ".pdf";
				array_push($paths, 
					[
						"download" => asset($path),
						"filename" => $order . ".pdf"
					]);

				if($pdf){
					$po_file = new PolistFile([
						'order_no' => $order,
						'file_name' => $order . '.pdf',
						'created_by' => $id
					]);
					$po_file->save();
				}
			}

			$po_list = PoList::whereIn('purchdoc', $purchdoc)->update([
				'remark' => 'converted',
			]);

			$response = array(
				'status' => true,
				'message' => 'Purchase order(s) created.',
				'file_paths' => $paths,
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'All parameters must be filled.',
			);
			return Response::json($response);
		}
	}

	public function generatePoCreate2(Request $request){

		if($request->hasFile('filePurchaseOrder')){
			$id = Auth::id();
			$file = $request->file('filePurchaseOrder');
			$data = file_get_contents($file);
			$rows = explode("\r\n", $data);
			$purchdocitems = array();
			foreach ($rows as $row){
				if (strlen($row) > 0) {
					$row = explode("\t", $row);
					try{
						if(preg_match('/^[0-9]+$/', $row[0])){
							if(!in_array($row[0] . sprintf("%'.0" . 5 . "d", trim($row[1], ' ')), $purchdocitems)){
								array_push($purchdocitems, $row[0] . sprintf("%'.0" . 5 . "d", trim($row[1], ' ')));
							}
							$po_list = db::table('purchase_order_temporaries')->insert([
								'purchdoc' => $row[0],
								'item' => sprintf("%'.0" . 5 . "d", trim($row[1], ' ')),
								'deliv_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[2]))),
								'order_qty' => str_replace('"','',str_replace(',','',$row[3]))
							]);							
						}
					} 
					catch(\Exception $e){
						db::table('purchase_order_temporaries')->truncate();
						return response()->json([
							'status' => false,
							'message' => $e->getMessage(),
						]);
					}
				}
			}

			$check = db::table('purchase_order_temporaries')
			->leftJoin('po_lists', function($join){
				$join->on('po_lists.purchdoc', '=', 'purchase_order_temporaries.purchdoc');
				$join->on('po_lists.item', '=', 'purchase_order_temporaries.item');
			})
			->select('purchase_order_temporaries.purchdoc', 'purchase_order_temporaries.item', 'po_lists.order_qty', db::raw('sum(purchase_order_temporaries.order_qty) as new_qty'), 'po_lists.remark')
			->groupBy('purchase_order_temporaries.purchdoc', 'purchase_order_temporaries.item', 'po_lists.order_qty', 'po_lists.remark')
			->having('po_lists.order_qty', '<>', db::raw('sum(purchase_order_temporaries.order_qty)'))
			->orHaving('po_lists.remark', '=', 'converted')
			->first();

			if($check != null){
				db::table('purchase_order_temporaries')->truncate();
				return response()->json([
					'status' => false,
					'message' => 'Order quantity did not match with po list. Or Purchdoc with specified item already converted.',
				]);
			}

			$po_lists = db::table('purchase_order_temporaries')
			->leftJoin('po_lists', function($join){
				$join->on('po_lists.purchdoc', '=', 'purchase_order_temporaries.purchdoc');
				$join->on('po_lists.item', '=', 'purchase_order_temporaries.item');
			})
			->leftJoin('vendors', 'vendors.vendor', '=', 'po_lists.vendor')
			->leftJoin('mrps', 'mrps.mrp', '=', 'po_lists.pgr')
			->leftJoin('countries', 'countries.country_code', '=', 'vendors.cty')
			->leftJoin('payment_terms', 'vendors.tpay', '=', 'payment_terms.payment_code')
			->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'vendors.sc')
			->select('po_lists.purchdoc', 'po_lists.pgr', db::raw('mrps.name as pgr_name'), 'po_lists.vendor', db::raw('vendors.name as vendor_name'), 'vendors.street', 'vendors.city', 'vendors.postl_code', 'countries.country_name', 'vendors.salesperson', 'vendors.sc', 'shipment_conditions.shipment_condition_name', 'vendors.tpay', 'payment_terms.payment_name', 'vendors.telephone_1', 'vendors.fax_number', 'vendors.incot', 'vendors.crcy', 'purchase_order_temporaries.item', 'po_lists.material', 'po_lists.description', 'purchase_order_temporaries.deliv_date', 'purchase_order_temporaries.order_qty', 'po_lists.base_unit_of_measure', 'po_lists.price', db::raw('round(po_lists.price*purchase_order_temporaries.order_qty, 2) as amount'))
			->orderBy('po_lists.vendor', 'asc')->orderBy('purchase_order_temporaries.purchdoc', 'asc')->orderBy('purchase_order_temporaries.item', 'asc')
			->get();

			$vendors = array();
			$orders = array();

			foreach ($po_lists as $po_list) {
				if(!in_array($po_list->vendor, $vendors)){
					array_push($vendors, $po_list->vendor);
					$code_generator = CodeGenerator::where('note', '=', 'PO')->first();
					$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
					$order_no = $po_list->vendor . '-' . $code_generator->prefix . $number;
					array_push($orders, $order_no);
					$code_generator->index = $code_generator->index+1;
					$code_generator->save();
				}
				$data = [
					'purchdoc' => $po_list->purchdoc,
					'order_no' => $order_no,
					'order_date' => date('Y-m-d'),
					'pgr' => $po_list->pgr,
					'pgr_name' => $po_list->pgr_name,
					'rev_no' => 0,
					// 'rev_date' => date('0000-00-00'),
					'vendor' => $po_list->vendor,
					'name' => $po_list->vendor_name,
					'street' => $po_list->street,
					'city' => $po_list->city,
					'postl_code' => $po_list->postl_code,
					'cty' => $po_list->country_name,
					'salesperson' => $po_list->salesperson,
					'sc' => $po_list->sc,
					'sc_name' => $po_list->shipment_condition_name,
					'tpay' => $po_list->tpay,
					'tpay_name' => $po_list->payment_name,
					'telephone' => $po_list->telephone_1,
					'fax_number' => $po_list->fax_number,
					'incot' => $po_list->incot,
					'curr' => $po_list->crcy,
					'item' => $po_list->item,
					'material' => $po_list->material,
					'description' => $po_list->description,
					'deliv_date' => $po_list->deliv_date,
					'order_qty' => $po_list->order_qty,
					'base_unit_of_measure' => $po_list->base_unit_of_measure,
					'price' => $po_list->price,
					'amount' => $po_list->amount,
					'created_by' => $id,
				];
				try{
					$purchase_order = new PurchaseOrder($data);
					$purchase_order->save();	
				}
				catch(\Exception $e){
					return response()->json([
						'status' => false,
						'message' => $e->getMessage(),
					]);
				}
			}

			$paths = array();
			foreach ($orders as $order) {
				//will be edited for summary PO
				$purchase_orders = PurchaseOrder::where('order_no', '=', $order)
				->select('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', db::raw('group_concat(item) as item'), 'material', 'description', 'deliv_date', db::raw('sum(order_qty) as order_qty'), 'base_unit_of_measure', 'price', db::raw('sum(amount) as amount'))
				->groupBy('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', 'material', 'description', 'deliv_date', 'base_unit_of_measure', 'price')
				->get();
				$total_amount = PurchaseOrder::where('order_no', '=', $order)->sum('amount');

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setPaper('A4', 'potrait');
				$pdf->loadView('purchase_orders.purchase_order_pdf', array(
					'purchase_orders' => $purchase_orders,
				));
				$pdf->save(public_path() . "/purchase_orders/" . $order . ".pdf");


				$path = "purchase_orders/" . $order . ".pdf";
				array_push($paths, 
					[
						"download" => asset($path),
						"filename" => $order . ".pdf"
					]);

				if($pdf){
					$po_file = new PolistFile([
						'order_no' => $order,
						'file_name' => $order . '.pdf',
						'created_by' => $id
					]);
					$po_file->save();
				}
			}

			$po_list = PoList::whereIn(db::raw('concat(purchdoc, item)'), $purchdocitems)->update([
				'remark' => 'converted',
			]);

			db::table('purchase_order_temporaries')->truncate();
			$response = array(
				'status' => true,
				'message' => 'Purchase order(s) created.',
				'file_paths' => $paths,
			);
			return Response::json($response);
		}
		else{
			return response()->json([
				'status' => false,
				'message' => 'Please select a file to generate.',
			]);
		}
	}

	public function generatePoCreate3(Request $request){
		$purchdoc = explode(",", $request->get('purchdoc'));

		$po_list_check = PoList::whereIn('purchdoc', $purchdoc)->where('remark', '=', 'converted')->first();

		if($po_list_check != null){
			$response = array(
				'status' => false,
				'message' => 'Purchase doc. ' . $po_list_check->purchdoc . ' already converted.',
			);
			return Response::json($response);
		}

		if(strlen($request->get('delivDate')) > 0 && strlen($request->get('purchdoc')) > 0 && strlen($request->get('shipmentCondition')) >0){
			$id = Auth::id();

			$po_lists = db::table('po_lists')->whereIn('po_lists.purchdoc', $purchdoc)
			->leftJoin('vendors', 'vendors.vendor', '=', 'po_lists.vendor')
			->leftJoin('mrps', 'mrps.mrp', '=', 'po_lists.pgr')
			->leftJoin('countries', 'countries.country_code', '=', 'vendors.cty')
			->leftJoin('payment_terms', 'vendors.tpay', '=', 'payment_terms.payment_code')
			->select('po_lists.purchdoc', 'po_lists.pgr', db::raw('mrps.name as pgr_name'), 'po_lists.vendor', db::raw('vendors.name as vendor_name'), 'vendors.street', 'vendors.city', 'vendors.postl_code', 'countries.country_name', 'vendors.salesperson', 'vendors.tpay', 'payment_terms.payment_name', 'vendors.telephone_1', 'vendors.fax_number', 'vendors.incot', 'vendors.crcy', 'po_lists.item', 'po_lists.material', 'po_lists.description', 'po_lists.order_qty', 'po_lists.base_unit_of_measure', 'po_lists.price', db::raw('round(po_lists.price*po_lists.order_qty, 2) as amount'))
			->orderBy('po_lists.vendor', 'asc')->orderBy('po_lists.purchdoc', 'asc')->orderBy('po_lists.item', 'asc')
			->get();

			$shipment_condition = db::table('shipment_conditions')->where('shipment_conditions.shipment_condition_code', '=', $request->get('shipmentCondition'))->first();

			$vendors = array();
			$orders = array();

			foreach ($po_lists as $po_list) {
				if(!in_array($po_list->vendor, $vendors)){
					array_push($vendors, $po_list->vendor);
					$code_generator = CodeGenerator::where('note', '=', 'PO')->first();
					$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
					$order_no = $po_list->vendor . '-' . $code_generator->prefix . $number;
					array_push($orders, $order_no);
					$code_generator->index = $code_generator->index+1;
					$code_generator->save();
				}
				$data = [
					'purchdoc' => $po_list->purchdoc,
					'order_no' => $order_no,
					'order_date' => date('Y-m-d'),
					'pgr' => $po_list->pgr,
					'pgr_name' => $po_list->pgr_name,
					'rev_no' => 0,
					'vendor' => $po_list->vendor,
					'name' => $po_list->vendor_name,
					'street' => $po_list->street,
					'city' => $po_list->city,
					'postl_code' => $po_list->postl_code,
					'cty' => $po_list->country_name,
					'salesperson' => $po_list->salesperson,
					'sc' => $shipment_condition->shipment_condition_code,
					'sc_name' => $shipment_condition->shipment_condition_name,
					'tpay' => $po_list->tpay,
					'tpay_name' => $po_list->payment_name,
					'telephone' => $po_list->telephone_1,
					'fax_number' => $po_list->fax_number,
					'incot' => $po_list->incot,
					'curr' => $po_list->crcy,
					'item' => $po_list->item,
					'material' => $po_list->material,
					'description' => $po_list->description,
					'deliv_date' => date('Y-m-d', strtotime($request->get('delivDate'))),
					'order_qty' => $po_list->order_qty,
					'base_unit_of_measure' => $po_list->base_unit_of_measure,
					'price' => $po_list->price,
					'amount' => $po_list->amount,
					'created_by' => $id,
				];
				try{
					$purchase_order = new PurchaseOrder($data);
					$purchase_order->save();	
				}
				catch(\Exception $e){
					return response()->json([
						'status' => false,
						'message' => $e->getMessage(),
					]);
				}
			}

			$paths = array();
			foreach ($orders as $order) {
				//will be edited for summary PO
				$purchase_orders = PurchaseOrder::where('order_no', '=', $order)
				->select('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', db::raw('group_concat(item) as item'), 'material', 'description', 'deliv_date', db::raw('sum(order_qty) as order_qty'), 'base_unit_of_measure', 'price', db::raw('sum(amount) as amount'))
				->groupBy('purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', 'material', 'description', 'deliv_date', 'base_unit_of_measure', 'price')
				->get();
				$total_amount = PurchaseOrder::where('order_no', '=', $order)->sum('amount');

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setPaper('A4', 'potrait');
				$pdf->loadView('purchase_orders.purchase_order_pdf', array(
					'purchase_orders' => $purchase_orders,
				));
				$pdf->save(public_path() . "/purchase_orders/" . $order . ".pdf");


				$path = "purchase_orders/" . $order . ".pdf";
				array_push($paths, 
					[
						"download" => asset($path),
						"filename" => $order . ".pdf"
					]);

				if($pdf){
					$po_file = new PolistFile([
						'order_no' => $order,
						'file_name' => $order . '.pdf',
						'created_by' => $id
					]);
					$po_file->save();
				}
			}

			$po_list = PoList::whereIn('purchdoc', $purchdoc)->update([
				'remark' => 'converted',
			]);

			$response = array(
				'status' => true,
				'message' => 'Purchase order(s) created.',
				'file_paths' => $paths,
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'All parameters must be filled.',
			);
			return Response::json($response);
		}
	}

	public function importPoList(Request $request){
		if($request->hasFile('filePoList')){
			$id = Auth::id();
			$file = $request->file('filePoList');
			$data = file_get_contents($file);
			$rows = explode("\r\n", $data);

			foreach ($rows as $row){
				if (strlen($row) > 0) {
					$row = explode("\t", $row);
					try{
						if($row[5] != "" && preg_match('/^[0-9]+$/', $row[11])){
							$price = 0;
							if($row[1] == 'G01'){
								$material = db::table('materials')->where('materials.material_number', '=', $row[5])->select('materials.std_price')->first();
								$price = $material->std_price;
							}
							if($row[1] == "G08"){
								$price = str_replace('"','',str_replace(',','',$row[19]));
							}
							
							$reply_date = null;
							$create_date = null;
							if(strlen($row[22]) > 0){
								$reply_date = date('Y-m-d', strtotime($row[22]));
							}
							if(strlen($row[23]) > 0){
								$create_date = date('Y-m-d', strtotime($row[23]));
							}

							$po_list = PoList::updateOrCreate(
								['purchdoc' => $row[11], 'item' => sprintf("%'.0" . 5 . "d", trim($row[12], ' '))],
								[
									'porg' => $row[0],
									'pgr' => $row[1],
									'vendor' => $row[2],
									'name' => $row[3],
									'country' => $row[4],
									'material' => $row[5],
									'description' => $row[6],
									'plnt' => $row[7],
									'sloc' => $row[8],
									'sc_vendor' => $row[9],
									'cost_ctr' => $row[10],
									'purchdoc' => $row[11],
									'item' => sprintf("%'.0" . 5 . "d", trim($row[12], ' ')),
									'acctassigcat' => $row[13],
									'order_date' => date('Y-m-d', strtotime($row[14])),
									'deliv_date' => date('Y-m-d', strtotime($row[15])),
									'order_qty' => str_replace('"','',str_replace(',','',$row[16])),
									'deliv_qty' => str_replace('"','',str_replace(',','',$row[17])),
									'base_unit_of_measure' => $row[18],
									'price' => $price,
									'curr' => $row[20],
									'order_no' => $row[21],
									'reply_date' => $reply_date,
									'create_date' => $create_date,
									'delay' => $row[24],
									'reply_qty' => str_replace('"','',str_replace(',','',$row[25])),
									'comment' => $row[26],
									'del' => $row[27],
									'incomplete' => $row[28],
									'compl' => $row[29],
									'ctr' => $row[30],
									'spt' => $row[31],
									'stock' => str_replace('"','',str_replace(',','',$row[25])),
									'lt' => $row[33],
									'dsf' => $row[34],
									'die_end' => $row[35],
									'created_by' => $id
								]
							);							
						}
					}
					catch(\Exception $e){
						return back()->with('error', $e->getMessage())->with('page', 'Purchase Order List')->with('head', 'Purchase Order');
					}
				}
			}
			return back()->with('success', 'PO List successfully imported.')->with('page', 'Please select a file.')->with('head', 'Purchase Order');
		}
		else{
			return back()->with('error', $e->getMessage())->with('page', 'Please select a file.')->with('head', 'Purchase Order');
		}
	}

	public function fetchDownloadPo(Request $request){
		$query = "select po_list_files.file_name from 
		(select distinct purchdoc, order_no from purchase_orders where purchdoc = '" . $request->get('purchdoc') . "') as a left join po_list_files on po_list_files.order_no = a.order_no";
		$po_list_files = db::select($query);

		$response = array(
			'status' => true,
			'files' => $po_list_files,
		);
		return Response::json($response);
	}

	public function downloadPo(Request $request){
		$filenames = $request->get('file_name');
		$paths = array();

		if(is_array($filenames)){
			foreach ($filenames as $filename) {
				$path = "purchase_orders/" . $filename;
				array_push($paths, 
					[
						"download" => asset($path),
						"filename" => $filename
					]);
			}
		}
		else{
			$path = "purchase_orders/" . $filenames;
			array_push($paths, 
				[
					"download" => asset($path),
					"filename" => $filenames
				]);
		}

		$response = array(
			'status' => true,
			'file_paths' => $paths,
		);
		return Response::json($response);
	}

	public function fetchPoArchive(Request $request){
		$po_files = db::table('po_list_files')
		->leftJoin(db::raw('(select distinct purchdoc, order_no from purchase_orders) as po'), 'po.order_no', '=', 'po_list_files.order_no')
		->leftJoin('users', 'users.id', '=', 'po_list_files.created_by')
		->select('po.purchdoc', 'po_list_files.order_no', 'po_list_files.file_name', db::raw('upper(users.username) as username'), 'po_list_files.created_at');

		if(strlen($request->get('createdfrom')) > 0){
			$createdfrom = date('Y-m-d', strtotime($request->get('createdfrom')));
			$po_files = $po_files->where(db::raw('date_format(po_list_files.created_at, "%Y-%m-%d")'), '>=', $createdfrom);
		}

		if(strlen($request->get('createdto')) > 0){
			$createdto = date('Y-m-d', strtotime($request->get('createdto')));
			$po_files = $po_files->where(db::raw('date_format(po_list_files.created_at, "%Y-%m-%d")'), '<=', $createdto);		
		}

		$po_files = $po_files->get();

		return DataTables::of($po_files)
		->addColumn('filename', function($po_files){
			return '<a href="javascript:void(0)" data-toggle="modal" onClick="downloadPo(id)" id="' . $po_files->file_name . '">' . $po_files->file_name . '</a>';
		})
		->rawColumns(['filename' => 'filename'])
		->make(true);
	}

	public function fetchPoList(Request $request){
		$po_lists = PoList::select('po_lists.id', 'po_lists.pgr', 'po_lists.vendor', 'po_lists.purchdoc', 'po_lists.item', 'po_lists.material', 'po_lists.description', 'po_lists.order_date', 'po_lists.deliv_date', 'po_lists.order_qty', 'po_lists.price', 'po_lists.curr', 'po_lists.remark');

		if($request->get('pgr') != null){
			$po_lists = $po_lists->whereIn('po_lists.pgr', $request->get('pgr'));
		}

		if($request->get('status') == '0'){
			$po_lists = $po_lists->whereNull('po_lists.remark');
			// $po_lists = $po_lists->where('po_file.remark', '=', null);
		}

		if($request->get('status') == '1'){
			// $po_lists = $po_lists->where('po_file.att', '=', 'converted');
			$po_lists = $po_lists->whereNotNull('po_lists.remark');
		}

		if(strlen($request->get('order_date_from')) > 0){
			$order_date_from = date('Y-m-d', strtotime($request->get('order_date_from')));
			$po_lists = $po_lists->where(DB::raw('DATE_FORMAT(po_lists.order_date, "%Y-%m-%d")'), '>=', $order_date_from);
		}

		if(strlen($request->get('order_date_to')) > 0){
			$order_date_to = date('Y-m-d', strtotime($request->get('order_date_to')));
			$po_lists = $po_lists->where(DB::raw('DATE_FORMAT(po_lists.order_date, "%Y-%m-%d")'), '<=', $order_date_to);
		}

		if(strlen($request->get('deliv_date_from')) > 0){
			$deliv_date_from = date('Y-m-d', strtotime($request->get('deliv_date_from')));
			$po_lists = $po_lists->where(DB::raw('DATE_FORMAT(po_lists.deliv_date, "%Y-%m-%d")'), '>=', $deliv_date_from);
		}

		if(strlen($request->get('deliv_date_to')) > 0){
			$deliv_date_to = date('Y-m-d', strtotime($request->get('deliv_date_to')));
			$po_lists = $po_lists->where(DB::raw('DATE_FORMAT(po_lists.deliv_date, "%Y-%m-%d")'), '<=', $deliv_date_to);
		}

		if(strlen($request->get('purchdoc')) > 0){
			$purchdoc = explode(",", $request->get('purchdoc'));
			$po_lists = $po_lists->whereIn('po_lists.purchdoc', $purchdoc);
		}

		if(strlen($request->get('material')) > 0){
			$material = explode(",", $request->get('material'));
			$po_lists = $po_lists->whereIn('po_lists.material', $material);
		}

		if(strlen($request->get('item')) > 0){
			$item = explode(",", $request->get('item'));
			$po_lists = $po_lists->whereIn('po_lists.item', $item);
		}

		if(strlen($request->get('vendor')) > 0){
			$vendor = explode(",", $request->get('vendor'));
			$po_lists = $po_lists->whereIn('po_lists.vendor', $vendor);
		}

		$po_lists = $po_lists->get();

		// $response = array(
		// 	'status' => true,
		// 	'file_paths' => $po_lists,
		// );
		// return Response::json($response);

		return DataTables::of($po_lists)
		->addColumn('action', function($po_lists){
			return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editPoList(id)" id="' . $po_lists->id . '"><i class="fa fa-edit"> Edit</i></a>';
		})
		->addColumn('status', function($po_lists){
			if($po_lists->remark == 'converted'){
				return '<a href="javascript:void(0)" onClick="modalDownload(id)" id="' . $po_lists->purchdoc . '"> Converted(' . $po_lists->att . ')</a>';
			}
			else{
				return '-';
			}
		})
		->rawColumns(['action' => 'action', 'status'=>'status'])
		->make(true);
	}

	// public function exportPoList(Request $request)
	// {
	// 	return Excel::create(new POExport($request->get('vendor'),$request->get('material'),$request->get('purchdoc'),$request->get('order_date_from'), $request->get('order_date_to'), $request->get('deliv_date_from'), $request->get('deliv_date_to'), $request->get('status'), $request->get('pgr')), 'po_list.xlsx');
	// }

	public function export(Request $request)
	{
		$po_lists = PoList::leftJoin('purchase_orders',  function($join)
		{
			$join->on('purchase_orders.purchdoc', '=', 'po_lists.purchdoc');
			$join->on('purchase_orders.item', '=', 'po_lists.item');
		})
		->select('po_lists.porg', 'po_lists.pgr', 'po_lists.vendor', 'po_lists.NAME', 'po_lists.country', 'po_lists.material', 'po_lists.description', 'po_lists.plnt', 'po_lists.sloc', 'po_lists.sc_vendor', 'po_lists.cost_ctr', 'po_lists.purchdoc', db::raw('GROUP_CONCAT(po_lists.item) as item'), 'po_lists.acctassigcat', db::raw('SUM(po_lists.order_qty) as order_qty'), 'po_lists.deliv_qty', 'po_lists.base_unit_of_measure', 'po_lists.price', 'po_lists.curr', 'po_lists.reply_date', 'po_lists.create_date', 'po_lists.delay', 'po_lists.reply_qty', 'po_lists.comment', 'po_lists.del', 'po_lists.incomplete','po_lists.compl', 'po_lists.ctr', 'po_lists.spt', 'po_lists.stock', 'po_lists.lt', 'po_lists.dsf', 'po_lists.die_end', 'purchase_orders.order_no', 'purchase_orders.deliv_date', 'purchase_orders.order_date');

		if($request->get('pgr') != null){
			$pgr = explode(',', $request->get('pgr'));
			$po_lists = $po_lists->whereIn('po_lists.pgr', $pgr);
		}

		if(strlen($request->get('status')) == 0){
			$po_lists = $po_lists->where('po_file.remark', '=', null);
		}

		if(strlen($request->get('status')) == 1){
			$po_lists = $po_lists->where('po_file.att', '=', 'converted');
		}

		if(strlen($request->get('order_date_from')) > 0){
			$order_date_from = date('Y-m-d', strtotime($request->get('order_date_from')));
			$po_lists = $po_lists->where(DB::raw('DATE_FORMAT(po_lists.order_date, "%Y-%m-%d")'), '>=', $order_date_from);
		}

		if(strlen($request->get('order_date_to')) > 0){
			$order_date_to = date('Y-m-d', strtotime($request->get('order_date_to')));
			$po_lists = $po_lists->where(DB::raw('DATE_FORMAT(po_lists.order_date, "%Y-%m-%d")'), '<=', $order_date_to);
		}

		if(strlen($request->get('deliv_date_from')) > 0){
			$deliv_date_from = date('Y-m-d', strtotime($request->get('deliv_date_from')));
			$po_lists = $po_lists->where(DB::raw('DATE_FORMAT(po_lists.deliv_date, "%Y-%m-%d")'), '>=', $deliv_date_from);
		}

		if(strlen($request->get('deliv_date_to')) > 0){
			$deliv_date_to = date('Y-m-d', strtotime($request->get('deliv_date_to')));
			$po_lists = $po_lists->where(DB::raw('DATE_FORMAT(po_lists.deliv_date, "%Y-%m-%d")'), '<=', $deliv_date_to);
		}

		if(strlen($request->get('purchdoc')) > 0){
			$purchdoc = explode(",", $request->get('purchdoc'));
			$po_lists = $po_lists->whereIn('po_lists.purchdoc', $purchdoc);
		}

		if(strlen($request->get('material')) > 0){
			$material = explode(",", $request->get('material'));
			$po_lists = $po_lists->whereIn('po_lists.material', $material);
		}

		if(strlen($request->get('item')) > 0){
			$item = explode(",", $request->get('item'));
			$po_lists = $po_lists->whereIn('po_lists.item', $item);
		}

		if(strlen($request->get('vendor')) > 0){
			$vendor = explode(",", $request->get('vendor'));
			$po_lists = $po_lists->whereIn('po_lists.vendor', $vendor);
		}

		$po_lists = $po_lists->groupBy('po_lists.porg','po_lists.pgr','po_lists.vendor','po_lists.NAME','po_lists.country','po_lists.material','po_lists.description','po_lists.plnt','po_lists.sloc','po_lists.sc_vendor','po_lists.cost_ctr','po_lists.purchdoc','po_lists.acctassigcat','po_lists.deliv_qty','po_lists.base_unit_of_measure','po_lists.price','po_lists.curr','po_lists.reply_date','po_lists.create_date','po_lists.delay','po_lists.reply_qty','po_lists.comment','po_lists.del','po_lists.incomplete','po_lists.compl','po_lists.ctr','po_lists.spt','po_lists.stock','po_lists.lt','po_lists.dsf','po_lists.die_end','purchase_orders.order_no','purchase_orders.deliv_date','purchase_orders.order_date');

		$po_lists = $po_lists->distinct()->get()->toArray();

		$po_array[] = array('porg', 'pgr','vendor','NAME','country','material','description','plnt','sloc','sc_vendor','cost_ctr','purchdoc','item','acctassigcat','order_date','deliv_date','order_qty','deliv_qty','base_unit_of_measure','price','curr','reply_date','create_date','delay','order_no','reply_qty','comment','del','incomplete','compl','ctr','spt','stock','lt','dsf','die_end','order_code');

		foreach ($po_lists as $key) {
			$po_array[] = array(
				'porg' => $key['porg'],
				'pgr' => $key['pgr'],
				'vendor' => $key['vendor'],
				'NAME' => $key['NAME'],
				'country' => $key['country'],
				'material' => $key['material'],
				'description' => $key['description'],
				'plnt' => $key['plnt'],
				'sloc' => $key['sloc'],
				'sc_vendor' => $key['sc_vendor'],
				'cost_ctr' => $key['cost_ctr'],
				'purchdoc' => $key['purchdoc'],
				'item' => $key['item'],
				'acctassigcat' => $key['acctassigcat'],
				'order_date' => $key['order_date'],
				'deliv_date' => $key['deliv_date'],
				'order_qty' => $key['order_qty'],
				'deliv_qty' => $key['deliv_qty'],
				'base_unit_of_measure' => $key['base_unit_of_measure'],
				'price' => $key['price'],
				'curr' => $key['curr'],
				'order_no' => '',
				'reply_date' => $key['reply_date'],
				'create_date' => $key['create_date'],
				'delay' => $key['delay'],
				'reply_qty' => $key['reply_qty'],
				'comment' => $key['comment'],
				'del' => $key['del'],
				'incomplete' => $key['incomplete'],
				'compl' => $key['compl'],
				'ctr' => $key['ctr'],
				'spt' => $key['spt'],
				'stock' => $key['stock'],
				'lt' => $key['lt'],
				'dsf' => $key['dsf'],
				'die_end' => $key['die_end'],
				'order_code' => $key['order_no']
			);
		}

		Excel::create('PO List', function($excel) use ($po_array){
			$excel->setTitle('PO List');
			$excel->sheet('PO Data', function($sheet) use ($po_array){
				$sheet->fromArray($po_array, null, 'A1', false, false);
			});
		})->download('xlsx');
	}
}