<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Libraries\ActMLEasyIf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\User;
use Response;
use App\AccSupplier;
use App\PchCatalog;
use App\AccItem;
use File;


class CatalogController extends Controller
{

	public function index_catalog(){
		$title = "Catalog Item";
		$title_jp = "";

		$vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
		->distinct()
		->get();

		$edit_vendor   = db::select('SELECT DISTINCT code_vendor,supplier FROM pch_catalogs ORDER BY code_vendor');
		$uom   = db::select('SELECT DISTINCT uom FROM pch_catalogs');

		return view('catalog.catalog_item', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => $vendor,
			'uom' => $uom,
			'edit_vendor' => $edit_vendor

		))->with('head', 'Catalog')->with('page', 'Catalog Item');
	}

	public function create_catalog(Request $request)
	{
		try {
			$date = date('Y-m-d');
			$supplier_code = $request->get('supplier_code');
			$user = Auth::user()->username;

			$pecah=explode("-",$supplier_code);
			$gmc = $request->get('gmc');
			$description = $request->get('description');
			$uom = $request->get('uom');
			$supplier = $request->get('supplier');
			$size = $request->get('size');

			if (count($request->file('foto')) > 0) {
				$num = 1;
				$file = $request->file('foto');
				$nama = $file->getClientOriginalName();
				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);

				$att = $gmc.'.'.$extension;

				$file->move('images/pch_katalog/', $att);

			} else {
				$att = null;
			}

			$catalog = new PchCatalog([
				'gmc' => $gmc,
				'desc' => $description,
				'uom' => $uom,
				'code_vendor' => $pecah[0],
				'supplier' => $pecah[1],
				'size' => $size,
				'foto' => $att,
				'created_by' => Auth::user()->username
			]);
			$catalog->save();
			$response = array(
				'status' => true,
				'catalog' => $catalog

			);
			return Response::json($response);		
		} catch (QueryException $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}


	public function fetch_catalog(Request $request){
		try {
			if ($request->get('tanggal')) {
				$tanggals = $request->get('tanggal');
			} else {
				$tanggals = date('Y-m-d');
			}
			$catalog_detail = db::select('SELECT
				gmc,id,
				`desc`,
				uom,
				supplier,
				size, code_vendor, foto
				FROM
				pch_catalogs');

			$response = array(
				'status' => true,
				'catalogs' => $catalog_detail
			);
			return Response::json($response);
		}catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchEditCatalog(Request $request){

		$edit_catalog = PchCatalog::select('id', 'gmc', 'desc','uom', 'supplier', 'size','code_vendor','foto')
		->where('pch_catalogs.id', '=', $request->get('id'))
		->get();

		$response = array(
			'status' => true,
			'edit_catalog' => $edit_catalog
		);
		return Response::json($response);
	}

	public function editSaveCatalog(Request $request){

		try {
			$cob = $request->get('cob');
			$gmc = $request->get('gmc_edit');
			$gmc_key = $request->get('supplier_edit');

			$catalog = PchCatalog::where('id',$cob)->first();

			if($request->file('foto_edit') != ''){        
				$path = public_path().'/images/pch_katalog/';

          //code for remove old file
				if($catalog->foto != ''  && $catalog->foto != null){
					$file_old = $path.$catalog->foto;
					unlink($file_old);
				}

          //upload new file
				$file = $request->file('foto_edit');
				$nama = $file->getClientOriginalName();
				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);

				$att = $gmc.'.'.$extension;
				$file->move($path, $att);
          //for update in table
				$catalog->update(['foto' => $att]);
			}

			$pecah=explode("-",$gmc_key);
			$catalog->gmc = $request->get('gmc_edit');
			$catalog->desc = $request->get('description_edit');
			$catalog->uom = $request->get('uom_edit');
			// $catalog->supplier = $vendor->vendor_code;

			$catalog->code_vendor = $pecah[0];
			$catalog->supplier = $pecah[1];

			if ($request->get('size_edit') == "-") {
				$sz = null;
			}
			else{
				$sz = $request->get('size_edit');
			}
			$catalog->size = $sz;

			$catalog->save();

			$response = array(
				'status' => true
			);
			return Response::json($response);

		}catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteCatalog(Request $request){
		try{

			$catalog = PchCatalog::where('id',$request->get('id'))->first();
			$path = public_path().'/images/pch_katalog/';
			if($catalog->foto != ''  && $catalog->foto != null){
				$file_old = $path.$catalog->foto;
				unlink($file_old);
				$delete = DB::table('pch_catalogs')
				->where('id', '=', $request->get('id'))
				->delete();

				$response = array(
					'status' => true,
					'message' => 'Delete successful',   
				);
			}else if ($catalog->foto == "" && $catalog->foto == null) {
				$delete = DB::table('pch_catalogs')
				->where('id', '=', $request->get('id'))
				->delete();	
				$response = array(
					'status' => true,
					'message' => 'Delete successful',   
				);
			}
			else{	
				$response = array(
					'status' => false,
					'message' => 'Error Delete Data',   
				);
			}

			return Response::json($response);
		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function showImage(Request $request){

		$img = PchCatalog::select('id','foto')
		->where('pch_catalogs.id', '=', $request->get('id'))
		->get();

		$response = array(
			'status' => true,
			'show_img' => $img
		);
		return Response::json($response);
	}

	public function CheckItem(Request $request){
		try {
			$keyword = $request->get('keyword');

			// $history = PchCatalog::whereNull('pch_catalogs.deleted_at');
			$history = PchCatalog::select('pch_catalogs.*')->whereNull('pch_catalogs.deleted_at');

			if($request->get('keyword') != null){
				$history = $history->where('gmc', 'like', '%' . $request->get('keyword') . '%');
			}
			$history = $history->get();
			
			$response = array(
				'status' => true,
				'check' => $history
			);
			return Response::json($response);
		}catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	

}
