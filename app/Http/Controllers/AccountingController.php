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
use App\AccSupplier;
use App\AccItem;

class AccountingController extends Controller
{

	//==================================//
	//			Master supplier 		//
	//==================================//


	public function master_supplier() {
		$title = 'Supplier';
		$title_jp = '???';

		$status = AccSupplier::select('acc_suppliers.supplier_status')
        ->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

        $city = AccSupplier::select('acc_suppliers.supplier_city')
        ->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

		return view('accounting_purchasing.master.supplier', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'status' => $status,
			'city' => $city,
		))->with('page', 'Supplier')->with('head', 'Supplier');
	}


    public function fetch_supplier(Request $request){
		$supplier = AccSupplier::orderBy('acc_suppliers.id', 'asc');

		if($request->get('status') != null){
			$supplier = $supplier->whereIn('acc_suppliers.supplier_status', $request->get('status'));
		}

		if($request->get('city') != null){
			$supplier = $supplier->whereIn('acc_suppliers.supplier_city', $request->get('city'));
		}

		$supplier = $supplier->select('*')
		->get();

		return DataTables::of($supplier)->make(true);
	}

	//==================================//
	//			Master Item				//
	//==================================//

	public function master_item() {
		$title = 'Purchase Item';
		$title_jp = '???';

		$uom = AccItem::select('acc_items.uom')
        ->whereNull('acc_items.deleted_at')
        ->distinct()
        ->get();

		return view('accounting_purchasing.master.purchase_item', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'uom' => $uom,
		))->with('page', 'Purchase Item')->with('head', 'Purchase Item');
	}

	public function fetch_item(Request $request){
		$items = AccItem::orderBy('acc_items.id', 'asc');

		if($request->get('uom') != null){
			$items = $items->whereIn('acc_items.uom', $request->get('uom'));
		}

		$items = $items->select('acc_items.id','acc_items.no_item','acc_items.kategori','acc_items.nama','acc_items.uom','acc_items.detail_1','acc_items.detail_2','acc_items.harga','acc_items.lot','acc_items.moq','acc_items.leadtime','acc_items.coo')
		->get();

		return DataTables::of($items)->make(true);
	}
}
