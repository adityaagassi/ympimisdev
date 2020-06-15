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

use App\AccExchangeRate;
use App\AccItem;
use App\AccItemCategory;
use App\AccBudget;
use App\AccSupplier;
use App\AccPurchaseRequisition;
use App\AccPurchaseRequisitionItem;
use App\AccPurchaseOrder;
use App\AccPurchaseOrderDetail;
use App\AccInvestment;
use App\AccInvestmentDetail;
use App\EmployeeSync;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class AccountingController extends Controller
{
	public function __construct() {
		$this->dept = [
	        'Management Information System',
	        'Accounting',
	        'Assembly (WI-A)',
	        'Educational Instrument (EI)',
	        'General Affairs',
	        'Human Resources',
	        'Logistic',
	        'Maintenance',
	        'Parts Process (WI-PP)',
	        'Procurement',
	        'Production Control',
	        'Production Engineering',
	        'Purchasing',
	        'Quality Assurance',
	        'Welding-Surface Treatment (WI-WST)'
	    ];

      $this->uom = [
          'Pce',
          'Pc',
          'Pack',
          'Roll',
          'Lot',
          'Set',
          'Btg',
          'Rim',
          'Unit',
          'Kg',
          'Ltr',
          'Meter'
      ];

      $this->transportation = [
          'AIR',
          'BOAT',
          'COURIER SERVICE',
          'DHL',
          'FEDEX',
          'SUV-Car'
      ];

      $this->delivery = [
          'CIF Surabaya',
          'CIP',
          'Cost And Freight ',
          'Delivered At Frontier',
          'Delivered Duty Paid',
          'Delivered Duty Unpaid',
          'Delivered Ex Quay',
          'Ex Works',
          'Ex Factory',
          'Ex Ship',
          'FRANCO',
          'Franco',
          'Flee Alongside Ship',
          'Free Carrier (FCA)',
          'Letter Of Credits',

      ];

      $this->dgm = 'PI0109004'; // Pak Budhi
      $this->gm = 'PI1206001'; // Pak Hayakawa
	}

	//==================================//
	//			Master supplier 		//
	//==================================//

	public function master_supplier() {
		$title = 'Supplier';
		$title_jp = '調達会社';

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
		$supplier = AccSupplier::orderBy('acc_suppliers.id', 'desc');

		if($request->get('status') != null){
			$supplier = $supplier->whereIn('acc_suppliers.supplier_status', $request->get('status'));
		}

		if($request->get('city') != null){
			$supplier = $supplier->whereIn('acc_suppliers.supplier_city', $request->get('city'));
		}

		$supplier = $supplier->select('*')
		->get();

		return DataTables::of($supplier)

    ->addColumn('action', function($supplier){
      $id = $supplier->id;
      return ' 
        <a href="supplier/update/'.$id.'" class="btn btn-primary btn-xs">Edit</a> 
        <a href="supplier/delete/'.$id.'" class="btn btn-danger btn-xs">Delete</a>
      ';
    })

    ->rawColumns(['action' => 'action'])

    ->make(true);
	}

  public function create_supplier(){
      $title = 'Create Supplier';
      $title_jp = '調達会社データを作成';

      return view('accounting_purchasing.master.create_supplier', array(
          'title' => $title,
          'title_jp' => $title_jp
      ))->with('page', 'Supplier');
  }

  public function create_supplier_post(Request $request)
    {
         try {
              $id_user = Auth::id();

              $supplier = AccSupplier::create([
                 'supplier_name' => $request->get('supplier_name'),
                 'supplier_address' => $request->get('supplier_address'),
                 'supplier_city' => $request->get('supplier_city'),
                 'supplier_phone' => $request->get('supplier_phone'),
                 'supplier_fax' => $request->get('supplier_fax'),
                 'contact_name' => $request->get('contact_name'),
                 'supplier_npwp' => $request->get('supplier_npwp'),
                 'supplier_duration' => $request->get('supplier_duration'),
                 'position' => $request->get('position'),
                 'supplier_status' => $request->get('supplier_status'),
                 'created_by' => $id_user
              ]);

              $supplier->save();

              $response = array(
                'status' => true,
                'datas' => "Berhasil"
              );
              return Response::json($response);

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }
    }

    public function update_supplier($id)
    {
        $title = 'Update Supplier';
        $title_jp = '調達会社データを作成';

        $supp = AccSupplier::find($id);

        return view('accounting_purchasing.master.edit_supplier', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'supplier' => $supp
        ))->with('page', 'Supplier');
      }

      public function update_supplier_post(Request $request)
      {
        try {
          $id_user = Auth::id();

          $inv = AccSupplier::where('id',$request->get('id'))
          ->update([
               'supplier_name' => $request->get('supplier_name'),
               'supplier_address' => $request->get('supplier_address'),
               'supplier_city' => $request->get('supplier_city'),
               'supplier_phone' => $request->get('supplier_phone'),
               'supplier_fax' => $request->get('supplier_fax'),
               'contact_name' => $request->get('contact_name'),
               'supplier_npwp' => $request->get('supplier_npwp'),
               'supplier_duration' => $request->get('supplier_duration'),
               'position' => $request->get('position'),
               'supplier_status' => $request->get('supplier_status'),
               'created_by' => $id_user
          ]);

          $response = array(
            'status' => true,
            'datas' => "Berhasil"
          );
          return Response::json($response);

        } catch (QueryException $e){
          $response = array(
               'status' => false,
               'datas' => $e->getMessage()
          );
          return Response::json($response);
        }
      }

    public function delete_supplier($id)
      {
          $supplier = AccSupplier::find($id);
          $supplier->delete();

          return redirect('/index/supplier')
          ->with('success', 'Supplier has been deleted.')
          ->with('page', 'Supplier');
      }

	//==================================//
	//			      Master Item				    //
	//==================================//

	public function master_item() {
		$title = 'Purchase Item';
		$title_jp = '購入アイテム';

		// $uom = AccItem::select('acc_items.uom')
    //       ->whereNull('acc_items.deleted_at')
    //       ->distinct()
    //       ->get();

    $item_categories = AccItemCategory::select('acc_item_categories.*')
        ->whereNull('acc_item_categories.deleted_at')
        ->get();

		return view('accounting_purchasing.master.purchase_item', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'uom' => $this->uom,
      'item_category' => $item_categories,
		))->with('page', 'Purchase Item')->with('head', 'Purchase Item');
	}

	public function fetch_item(Request $request){
		$items = AccItem::select('acc_items.id','acc_items.kode_item','acc_items.kategori','acc_items.deskripsi','acc_items.uom','acc_items.spesifikasi','acc_items.harga','acc_items.lot','acc_items.moq','acc_items.leadtime','acc_items.currency');

    if($request->get('keyword') != null){
      $items = $items->where('deskripsi', 'like', '%'.$request->get('keyword').'%')
      ->orWhere('spesifikasi', 'like', '%' . $request->get('keyword') . '%');
    }

    if($request->get('category') != null){
      $items = $items->where('acc_items.kategori', $request->get('category'));
    }

    if($request->get('uom') != null){
      $items = $items->whereIn('acc_items.uom', $request->get('uom'));
    }

    $items = $items->orderBy('acc_items.id','ASC')
    ->get();

		return DataTables::of($items)

    ->addColumn('action', function($items){
      $id = $items->id;
      return ' 
        <a href="purchase_item/update/'.$id.'" class="btn btn-primary btn-xs">Edit</a> 
        <a href="purchase_item/delete/'.$id.'" class="btn btn-danger btn-xs">Delete</a>
      ';
    })

    ->rawColumns(['action' => 'action'])
    ->make(true);
	}

  public function create_item(){
        $title = 'Create Item';
        $title_jp = '購入アイテムを作成';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

        $item_categories = AccItemCategory::select('acc_item_categories.*')
        ->whereNull('acc_item_categories.deleted_at')
        ->get();

        return view('accounting_purchasing.master.create_purchase_item', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'item_category' => $item_categories,
            'uom' => $this->uom
        ))->with('page', 'Purchase Item');
  }

  public function create_item_post(Request $request)
    {
         try {
              $id_user = Auth::id();

              $item = AccItem::create([
                 'kode_item' => $request->get('item_code'),
                 'kategori' => $request->get('item_category'),
                 'deskripsi' => $request->get('item_desc'),
                 'uom' => $request->get('item_uom'),
                 'spesifikasi' => $request->get('item_spec'),
                 'harga' => $request->get('item_price'),
                 'lot' => $request->get('item_lot'),
                 'moq' => $request->get('item_moq'),
                 'leadtime' => $request->get('item_leadtime'),
                 'currency' => $request->get('item_currency'),
                 'created_by' => $id_user
              ]);

              $item->save();

              $response = array(
                'status' => true,
                'datas' => "Berhasil"
              );
              return Response::json($response);

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }
    }

    public function update_item($id)
    {
        $title = 'Edit Item';
        $title_jp = '購入アイテムを編集';

        $item = AccItem::find($id);
        
        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

        $item_categories = AccItemCategory::select('acc_item_categories.*')
        ->whereNull('acc_item_categories.deleted_at')
        ->get();

        return view('accounting_purchasing.master.edit_purchase_item', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'item' => $item,
            'employee' => $emp,
            'item_category' => $item_categories,
            'uom' => $this->uom
        ))->with('page', 'Purchase Item');
      }

      public function update_item_post(Request $request)
      {
        try {
          $id_user = Auth::id();

          $inv = AccItem::where('id',$request->get('id'))
          ->update([
               'kode_item' => $request->get('item_code'),
               'kategori' => $request->get('item_category'),
               'deskripsi' => $request->get('item_desc'),
               'uom' => $request->get('item_uom'),
               'spesifikasi' => $request->get('item_spec'),
               'harga' => $request->get('item_price'),
               'lot' => $request->get('item_lot'),
               'moq' => $request->get('item_moq'),
               'leadtime' => $request->get('item_leadtime'),
               'currency' => $request->get('item_currency'),
               'created_by' => $id_user
          ]);

          $response = array(
            'status' => true,
            'datas' => "Berhasil"
          );
          return Response::json($response);

        } catch (QueryException $e){
          $response = array(
               'status' => false,
               'datas' => $e->getMessage()
          );
          return Response::json($response);
        }
      }

      public function delete_item($id)
      {
          $items = AccItem::find($id);
          $items->delete();

          return redirect('/index/purchase_item')
          ->with('status', 'Item has been deleted.')
          ->with('page', 'Purchase Item');
      }

      public function get_kode_item(Request $request)
      {
        $kategori = $request->kategori;

        $query = "SELECT kode_item FROM `acc_items` where kategori='$kategori' order by id DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if ($nomorurut != null) {
          $nomor = substr($nomorurut[0]->kode_item, -3);
          $nomor = $nomor + 1;
          $nomor = sprintf('%03d', $nomor);
          
        } else {
          $nomor = "001";
        }

        $result['no_urut'] = $nomor;
        
        return json_encode($result);
      }


      //==================================//
      //       Create Item Category       //
      //==================================//

      public function create_item_category(){
        $title = 'Create Item Category';
        $title_jp = '購入アイテムの種類を作成';

        return view('accounting_purchasing.master.create_category_item', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'Purchase Item');
      }

      public function create_item_category_post(Request $request)
      {
         try {
              $id_user = Auth::id();

              $item_category = AccItemCategory::create([
                 'category_id' => $request->get('category_id'),
                 'category_name' => $request->get('category_name'),
                 'created_by' => $id_user
              ]);

              $item_category->save();

              $response = array(
                'status' => true,
                'datas' => "Berhasil"
              );
              return Response::json($response);

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }
      }

      //==================================//
      //          Exchange Rate           //
      //==================================//

      public function exchange_rate() {
        $title = 'Exchange Rate';
        $title_jp = '為替レート';

        return view('accounting_purchasing.master.exchange_rate', array(
          'title' => $title,
          'title_jp' => $title_jp,
        ))->with('page', 'Exchange Rate')->with('head', 'Exchange Rate');
      }


      public function fetch_exchange_rate(Request $request){
        $exchange = AccExchangeRate::orderBy('acc_exchange_rates.id', 'desc');

        if($request->get('tanggal') != null){
          $exchange = $exchange->where('acc_exchange_rates.periode', $request->get('tanggal')."-01");
        }

        $exchange = $exchange->select('*')
        ->get();

        return DataTables::of($exchange)

        ->editColumn('periode',function($exchange){
            return date('Y F', strtotime($exchange->periode));
          })

        ->addColumn('action', function($exchange){
          $id = $exchange->id;
          return '  
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$id.')">Delete</button>
          ';
        })

        ->rawColumns(['action' => 'action'])

        ->make(true);
      }

      public function create_exchange_rate(Request $request){
         try {
              $id_user = Auth::id();

              $rate = AccExchangeRate::create([
                 'periode' => $request->get('periode')."-01",
                 'currency' => $request->get('currency'),
                 'rate' => $request->get('rate'),
                 'created_by' => $id_user
              ]);

              $rate->save();

              $response = array(
                'status' => true,
                'datas' => "Berhasil"
              );
              return Response::json($response);

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }

      }

      public function delete_exchange_rate(Request $request)
      {
        $exchange = AccExchangeRate::find($request->get("id"));
        $exchange->delete();

        $response = array(
          'status' => true
        );
        return Response::json($response);
      }



    	//==================================//
    	//		   Purchase Requisition 		  //
    	//==================================//


    	public function purchase_requisition(){
          $title = 'Purchase Requisition';
          $title_jp = '購入申請';

          $emp = EmployeeSync::where('employee_id', Auth::user()->username)
          ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

          $items = db::select("select kode_item, kategori, deskripsi from acc_items where deleted_at is null");
          $dept = $this->dept;

          return view('accounting_purchasing.purchase_requisition', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'items' => $items,
            'dept' => $dept
          ))->with('page', 'Purchase Requisition')->with('head', 'PR');
      }

      public function fetch_purchase_requisition(Request $request)
	    {
  	    $tanggal = "";
  	    $adddepartment = "";
  	
  	    if(strlen($request->get('datefrom')) > 0){
	          $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
	          $tanggal = "and A.submission_date >= '".$datefrom." 00:00:00' ";
	          if(strlen($request->get('dateto')) > 0){
	               $dateto = date('Y-m-d', strtotime($request->get('dateto')));
	               $tanggal = $tanggal."and A.submission_date  <= '".$dateto." 23:59:59' ";
	          }
	     }

	     if($request->get('department') != null) {
	          $departments = $request->get('department');
	          $deptlength = count($departments);
	          $department = "";

	          for($x = 0; $x < $deptlength; $x++) {
	               $department = $department."'".$departments[$x]."'";
	               if($x != $deptlength-1){
	                    $department = $department.",";
	               }
	          }
	          $adddepartment = "and A.Department in (".$department.") ";
	     }

	     $qry = "SELECT	* FROM acc_purchase_requisitions A WHERE A.deleted_at IS NULL ".$tanggal."".$adddepartment." order by A.id DESC";

	     $pr = DB::select($qry);
	     
	     return DataTables::of($pr)

	     ->editColumn('submission_date',function($pr){
          return date('d F Y', strtotime($pr->submission_date));
        })

       ->editColumn('note',function($pr){
          $note = "";
          if($pr->note != null){
              $note = $pr->note;
          }
          else{
              $note = '-';
          }

          return $note;
        })

	     ->editColumn('status',function($pr){
        	$id = $pr->id;

          if($pr->status == "approval") {
            return '<label class="label label-warning">Approval</a>';
          } 
          else if($pr->status == "approval_acc") {
            return '<label class="label label-success">Diverifikasi Accounting</a>';
          } 
        	
        })

       ->addColumn('action',function($pr){
        $id = $pr->id;
        return '<a href="purchase_requisition/detail/'.$id.'" class="btn btn-primary btn-sm">Detail</a>';
       })

        ->editColumn('file',function($pr){

            $data = json_decode($pr->file);

            $fl = "";

            if($pr->file != null){
              for ($i = 0; $i < count($data); $i++) {
                $fl .= '<a href="files/pr/'.$data[$i].'" target="_blank" class="fa fa-paperclip"></a>';
              }
            }
            else{
                $fl = '-';
            }

            return $fl;
        })

	     ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file'])
	     ->make(true);
	}

  public function fetchItemList(Request $request)
  {
    $items = AccItem::select('acc_items.kode_item','acc_items.deskripsi')->limit(50)->get();

    $response = array(
      'status' => true,
      'item' => $items
    );

    return Response::json($response);
  }

  public function prgetitemdesc(Request $request)
  {
      $html = array();
      $kode_item = AccItem::where('kode_item',$request->kode_item)->get();
      foreach ($kode_item as $item) {
          $html = array(
            'deskripsi' => $item->deskripsi,
            'spesifikasi' => $item->spesifikasi,
            'uom' => $item->uom,
            'price' => $item->harga,
            'currency' => $item->currency
          );

      }

      return json_encode($html);
  }

  public function fetchBudgetList(Request $request)
  {
    $budgets = AccBudget::select('acc_budgets.budget_no','acc_budgets.description')
    ->where('department','=',$request->get('department'))
    ->where('category','=','Expenses')
    ->distinct()
    ->get();

    $response = array(
      'status' => true,
      'budget' => $budgets
    );

    return Response::json($response);
  }

  public function get_exchange_rate(Request $request)
  {
      $html = array();
      $date = date('Y-m')."-01";

      if ($date == "") {
          
      }

      $rate_a = AccExchangeRate::where('periode','=',$date)->orderBy('rate','DESC')->get();

      // foreach ($rate_a as $rate) {
      //     $html = array(
      //       'currency' => $rate->currency,
      //       'rate' => $rate->rate
      //     );

      // }

      return json_encode($rate_a);
  }

  public function prgetbudgetdesc(Request $request)
  {
      $html = array();

      $tahun = date('Y');

      $date = date('Y-m-d', strtotime(date('Y-m-d'). ' + 21 days'));
      $bulan = date("m",strtotime($date));

      $budget_no = AccBudget::where('budget_no',$request->budget_no)->where('periode',$tahun)->get();
      foreach ($budget_no as $budget) {
          $html = array(
            'description' => $budget->description,
            'amount' => $budget->amount,
            'account' => $budget->account_name,
            'category' => $budget->category,
            'apr' => $budget->apr,
            'may' => $budget->may,
            'jun' => $budget->jun,
            'jul' => $budget->jul,
            'aug' => $budget->aug,
            'sep' => $budget->sep,
            'oct' => $budget->oct,
            'nov' => $budget->nov,
            'dec' => $budget->dec,
            'jan' => $budget->jan,
            'feb' => $budget->feb,
            'mar' => $budget->mar,
            'bulan' => $bulan
          );

      }

      return json_encode($html);
  }

  public function get_nomor_pr(Request $request)
  {
      $datenow = date('Y-m-d');
      $tahun = date('y');
      $bulan = date('m');
      $dept = $request->dept;

      $query = "SELECT no_pr FROM `acc_purchase_requisitions` where department = '$dept' and DATE_FORMAT(submission_date, '%y') = '$tahun' and month(submission_date) = '$bulan' order by id DESC LIMIT 1";
      $nomorurut = DB::select($query);

      if ($nomorurut != null) {
        // foreach ($nomordepan as $nomors) {
        //   $nomor = $nomors->id+1;
        // }
        $nomor = substr($nomorurut[0]->no_pr, -3);
        $nomor = $nomor + 1;
        $nomor = sprintf('%03d', $nomor);
      }
      else{
        $nomor = "001";
      }

      if ($dept == "Management Information System") {
        $dept = "IT";
      }
      else if ($dept == "Accounting"){
        $dept = "AC";
      }
      else if ($dept == "Assembly (WI-A)"){
        $dept = "AS";
      }
      else if ($dept == "Educational Instrument (EI)"){
        $dept = "EI";
      }
      else if ($dept == "General Affairs"){
        $dept = "GA";
      }
      else if ($dept == "Human Resources"){
        $dept = "HR";
      }
      else if ($dept == "Logistic"){
        $dept = "LO";
      }
      else if ($dept == "Maintenance"){
        $dept = "PM";
      }
      else if ($dept == "Parts Process (WI-PP)"){
        $dept = "TP";
      }
      else if ($dept == "Procurement" || $dept == "Purchasing"){
        $dept = "PH";
      }
      else if ($dept == "Production Control"){
        $dept = "PC";
      }
      else if ($dept == "Production Engineering"){
        $dept = "PE";
      }
      else if ($dept == "Quality Assurance"){
        $dept = "QA";
      }
      else if ($dept == "Welding-Surface Treatment (WI-WST)"){
        $dept = "WS";
      }

      $result['tahun'] = $tahun;
      $result['bulan'] = $bulan;
      $result['dept'] = $dept;
      $result['no_urut'] = $nomor;
      
      return json_encode($result);
  }

	public function create_purchase_requisition(Request $request)
    {
        $id = Auth::id();

        $lop = $request->get('lop');

        try{

          //getManager From Department

          $manager = null;
          $posisi = null;

          if ($request->get('department') == "Production Engineering") {
            $manager = 'PI0703002';
          }
          else{
            $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$request->get('department')."' and position = 'manager'");
          }

          if ($manag != null) {
            $posisi = "manager";
            
            foreach ($manag as $mn) {
              $manager = $mg->employee_id;
            }

          } else{
              $posisi = "dgm"; 
          }

          //Cek File

          $files=array();
          $file = new AccPurchaseRequisition();
          if ($request->file('reportAttachment') != NULL) {
            if($files=$request->file('reportAttachment')) {
              foreach($files as $file){
                $nama=$file->getClientOriginalName();
                $file->move('files/pr',$nama);
                $data[]=$nama;
              }
            }            
            $file->filename=json_encode($data);           
          }
          else {
            $file->filename=NULL;
          }

          
          $submission_date = $request->get('submission_date');
          $po_date = date('Y-m-d', strtotime($submission_date. ' + 7 days'));

        	$data = new AccPurchaseRequisition([
	            'no_pr' => $request->get('no_pr'),
	            'emp_id' => $request->get('emp_id'),
	            'emp_name' => $request->get('emp_name'),
	            'department' => $request->get('department'),
	            'group' => $request->get('group'),
	            'submission_date' => $submission_date,
              'po_due_date' => $po_date,
	            'note' => $request->get('note'),
              'file' => $file->filename,
	            'posisi' => $posisi,
	            'status' => 'approval',
              'no_budget' => $request->get('budget_no'),
              'manager' => $manager,
              'dgm' => $this->dgm,
              'gm' => $this->gm,
	            'created_by' => $id
	        ]);

	        $data->save();

          $mod_date = date('Y-m-d', strtotime($request->get('submission_date'). ' + 21 days'));

            for ($i=1; $i <= $lop ; $i++) {
                $item_code = "item_code".$i;
                $item_desc = "item_desc".$i;
                $item_spec = "item_spec".$i;
                $item_currency = "item_currency".$i;
                $item_currency_text = "item_currency_text".$i;
                $item_price = "item_price".$i;
                $item_qty = "qty".$i;
                $item_amount = "amount".$i;
                // $item_budget = "budget".$i;

                $status = "";                
                //Jika ada value kosong
                if ($request->get($item_code) == "kosong") {
                   $request->get($item_code) == "";
                }

                //Jika item kosong
                if ($request->get($item_code) != null) {
                   $status = "fixed";
                }
                else{
                   $status = "sementara"; 
                }

                if ($request->get($item_currency) != ""){
                    $current = $request->get($item_currency);
                }
                else if ($request->get($item_currency_text) != ""){
                    $current = $request->get($item_currency_text);
                }

                //get only number
                $price_real = preg_replace('/[^0-9]/', '', $request->get($item_price));
                $amount = preg_replace('/[^0-9]/', '', $request->get($item_amount));

                $data2 = new AccPurchaseRequisitionItem([
                    'no_pr' => $request->get('no_pr'),
                    'item_code' => $request->get($item_code),
                    'item_desc' => $request->get($item_desc),
                    'item_spec' => $request->get($item_spec),
                    'item_request_date' => $mod_date,
                    'item_currency' => $current,
                    'item_price' => $price_real,
                    'item_qty' => $request->get($item_qty),
                    'item_amount' => $amount,
                    'status' => $status,
                    'created_by' => $id
                ]);

                $data2->save();
            }

            $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.manager = users.username where acc_purchase_requisitions.id = ".$data->id;
            $mailtoo = DB::select($mails);
            
            if ($mailtoo == null) { // Jika Gaada Manager
              $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = ".$data->id;
              $mailtoo = DB::select($mails);
            }

            $isimail = "select * FROM acc_purchase_requisitions where acc_purchase_requisitions.id = ".$data->id;
            $purchaserequisition = db::select($isimail);

            Mail::to($mailtoo)->send(new SendEmail($purchaserequisition, 'purchase_requisition'));

            return redirect('/purchase_requisition')->with('status', 'PR Berhasil Dibuat')->with('page', 'Purchase Requisition');
        }
        catch (QueryException $e){
            return redirect('/purchase_requisition')->with('error', $e->getMessage())->with('page', 'Purchase Requisition');
        }
    }



    //==================================//
    //          Detail PR               //
    //==================================//

    public function detail_purchase_requisition($id)
    {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
        
        $pr = AccPurchaseRequisition::find($id);

        $items = AccPurchaseRequisitionItem::select('acc_purchase_requisition_items.*')
        ->join('acc_purchase_requisitions','acc_purchase_requisition_items.no_pr','=','acc_purchase_requisitions.no_pr')
        ->where('acc_purchase_requisitions.id','=',$id)
        ->get();

        return view('accounting_purchasing.detail_purchase_requisition', array(
            'pr' => $pr,
            'items' => $items
        ))->with('page', 'Purchase Requisition');
    }

    //==================================//
    //          Verifikasi PR           //
    //==================================//

    public function verifikasi_purchase_requisition($id){
        $pr = AccPurchaseRequisition::find($id);
        
        $items = AccPurchaseRequisitionItem::select('acc_purchase_requisition_items.*')
        ->join('acc_purchase_requisitions','acc_purchase_requisition_items.no_pr','=','acc_purchase_requisitions.no_pr')
        ->where('acc_purchase_requisitions.id','=',$id)
        ->get();

        return view('accounting_purchasing.verifikasi.verifikasi_pr', array(
          'pr' => $pr,
          'items' => $items
        ))->with('page', 'Purchase Requisition');
    }

    public function approval_purchase_requisition(Request $request,$id)
    {
      $approve = $request->get('approve');

      if(count($approve) == 3){
        $pr = AccPurchaseRequisition::find($id);

        if ($pr->posisi == "manager") {
          $pr->posisi = "dgm";
          $pr->approvalm = "Approved";
          $pr->dateapprovalm = date('Y-m-d H:i:s');

          $mailto = "select distinct employees.name,email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = '".$pr->id."'";
          $mails = DB::select($mailto);

          foreach($mails as $mail){
            $mailtoo = $mail->email;
          }
        }

        else if ($pr->posisi == "dgm") {
          $pr->posisi = "gm";
          $pr->approvaldgm = "Approved";
          $pr->dateapprovaldgm = date('Y-m-d H:i:s');

          $mailto = "select distinct employees.name,email from acc_purchase_requisitions join users on acc_purchase_requisitions.gm = users.username where acc_purchase_requisitions.id = '".$pr->id."'";
          $mails = DB::select($mailto);

          foreach($mails as $mail){
            $mailtoo = $mail->email;
          }
        }

        else if ($pr->posisi == "gm") {

          $pr->posisi = 'acc';
          $pr->approvalgm = "Approved";
          $pr->dateapprovalgm = date('Y-m-d H:i:s');
          $pr->status = "approval_acc";

          //kirim email ke Mas Shega & Mas Erlangga
          $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = 'Purchasing Control' and (employee_id = 'PI1908032' or employee_id = 'PI1810020')";
          $mailtoo = DB::select($mails);
        }

        $isimail = "select * FROM acc_purchase_requisitions where acc_purchase_requisitions.id = ".$pr->id;
        $pr_isi = db::select($isimail);

        Mail::to($mailtoo)->send(new SendEmail($pr_isi, 'purchase_requisition'));
        $pr->save();

        return redirect('/purchase_requisition/verifikasi/'.$id)->with('status', 'PR Approved')->with('page', 'Purchase Requisition');
      }
      else{
        return redirect('/purchase_requisition/verifikasi/'.$id)->with('error', 'PR Not Approved')->with('page', 'Purchase Requisition');
      }          
    }

    public function reject_purchase_requisition(Request $request,$id)
      {
          $alasan = $request->get('alasan');

          $pr = AccPurchaseRequisition::find($id);
          
          if ($pr->posisi == "manager" || $pr->posisi == "dgm" || $pr->posisi == "gm") {
            $pr->alasan = $alasan;
            $pr->datereject = date('Y-m-d H:i:s');
            $pr->posisi = "user";
            $pr->approvalm = null;
            $pr->dateapprovalm = null;
            $pr->approvaldgm = null;
            $pr->dateapprovaldgm = null;
          }

          $pr->save();

          $isimail = "select * FROM acc_purchase_requisitions where acc_purchase_requisitions.id = ".$pr->id;
          $tolak = db::select($isimail);

          //kirim email ke User 
          $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.emp_id = users.username where acc_purchase_requisitions.id ='".$pr->id."'";
          $mailtoo = DB::select($mails);

          Mail::to($mailtoo)->send(new SendEmail($tolak, 'purchase_requisition'));
          return redirect('/purchase_requisition/verifikasi/'.$id)->with('status', 'PR Not Approved')->with('page', 'Purchase Requisition');
      }


    //==================================//
    //          Purchase Order          //
    //==================================//

    public function purchase_order(){
          $title = 'Purchase Order';
          $title_jp = '';

          $emp = EmployeeSync::where('employee_id', Auth::user()->username)
          ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

          $vendor = AccSupplier::select('acc_suppliers.*')
          ->whereNull('acc_suppliers.deleted_at')
          ->distinct()
          ->get();

          $authorized2 = EmployeeSync::select('employee_id','name')
          ->where('position','=','Manager')
          ->where('department','=','Procurement')
          ->first();

          $authorized3 = EmployeeSync::select('employee_id','name')
          ->where('position','=','Director')
          ->Orwhere('position','=','General Manager')
          ->get();

          return view('accounting_purchasing.purchase_order', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'vendor' => $vendor,
            'delivery' => $this->delivery,
            'transportation' => $this->transportation,
            'authorized2' => $authorized2,
            'authorized3' => $authorized3,
          ))->with('page', 'Purchase Order')->with('head', 'Purchase Order');
      }

      public function fetch_purchase_order(Request $request)
      {
        $tanggal = "";
        $adddepartment = "";
    
        if(strlen($request->get('datefrom')) > 0){
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and A.tgl_po >= '".$datefrom." 00:00:00' ";
            if(strlen($request->get('dateto')) > 0){
                 $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                 $tanggal = $tanggal."and A.tgl_po  <= '".$dateto." 23:59:59' ";
            }
       }

       $qry = "SELECT * FROM acc_purchase_orders A WHERE A.deleted_at IS NULL ".$tanggal." order by A.id DESC";
       $po = DB::select($qry);
       
       return DataTables::of($po)

       ->editColumn('tgl_po',function($po){
          return date('d F Y', strtotime($po->tgl_po));
        })

       ->editColumn('note',function($po){
          $note = "";
          if($po->note != null){
              $note = $po->note;
          }
          else{
              $note = '-';
          }

          return $note;
        })

       ->editColumn('status',function($po){
          $id = $po->id;

          if($po->status == "approval") {
            return '<label class="label label-warning">Approval</a>';
          } 
          else if($po->status == "SAP") {
            return '<label class="label label-success">Diverifikasi</a>';
          } 
          
        })

       ->addColumn('action',function($po){
          $id = $po->id;
          return '<a href="purchase_order/detail/'.$id.'" class="btn btn-primary btn-sm">Detail</a>';
       })

        ->editColumn('file',function($po){
            $data = json_decode($po->file);

            $fl = "";

            if($po->file != null){
              for ($i = 0; $i < count($data); $i++) {
                $fl .= '<a href="files/pr/'.$data[$i].'" target="_blank" class="fa fa-paperclip"></a>';
              }
            }
            else{
                $fl = '-';
            }

            return $fl;
        })

       ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file'])
       ->make(true);
  }

  public function get_nomor_po(Request $request)
  {
      $datenow = date('Y-m-d');
      $tahun = date('y');
      $bulan = date('m');
      $dept = $request->dept;

      $query = "SELECT no_po FROM `acc_purchase_orders` where DATE_FORMAT(tgl_po, '%y') = '$tahun' and month(tgl_po) = '$bulan' order by id DESC LIMIT 1";
      $nomorurut = DB::select($query);

      if ($nomorurut != null) {
        $nomor = substr($nomorurut[0]->no_po, -3);
        $nomor = $nomor + 1;
        $nomor = sprintf('%03d', $nomor);
      }
      else{
        $nomor = "001";
      }

      if ($dept == "Management Information System") {
        $dept = "IT";
      }
      else if ($dept == "Accounting"){
        $dept = "AC";
      }
      else if ($dept == "Assembly (WI-A)"){
        $dept = "AS";
      }
      else if ($dept == "Educational Instrument (EI)"){
        $dept = "EI";
      }
      else if ($dept == "General Affairs"){
        $dept = "GA";
      }
      else if ($dept == "Human Resources"){
        $dept = "HR";
      }
      else if ($dept == "Logistic"){
        $dept = "LO";
      }
      else if ($dept == "Maintenance"){
        $dept = "PM";
      }
      else if ($dept == "Parts Process (WI-PP)"){
        $dept = "TP";
      }
      else if ($dept == "Procurement" || $dept == "Purchasing"){
        $dept = "PH";
      }
      else if ($dept == "Production Control"){
        $dept = "PC";
      }
      else if ($dept == "Production Engineering"){
        $dept = "PE";
      }
      else if ($dept == "Quality Assurance"){
        $dept = "QA";
      }
      else if ($dept == "Welding-Surface Treatment (WI-WST)"){
        $dept = "WS";
      }

      $result['tahun'] = $tahun;
      $result['bulan'] = $bulan;
      $result['dept'] = $dept;
      $result['no_urut'] = $nomor;
      
      return json_encode($result);
  }

  public function pogetsupplier(Request $request)
  {
      $html = array();
      $supplier_name = AccSupplier::where('supplier_name',$request->supplier_name)->get();
      foreach ($supplier_name as $supp) {
          $html = array(
            'duration' => $supp->supplier_duration,
            'status' => $supp->supplier_status,
          );
      }
      return json_encode($html);
  }

  public function create_purchase_order_post(Request $request)
    {
         try {
              $id_user = Auth::id();

              $po = AccPurchaseOrder::create([

                  'created_by' => $id_user
              ]);

              $po->save();

              $response = array(
                'status' => true,
                'datas' => "Berhasil",
                'id' => $po->id
              );
              return Response::json($response);

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }
    }








    //==================================//
  	//			      Investment 				    //
  	//==================================//


    public function Investment(){
        $title = 'Investment';
        $title_jp = '投資申請';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

        $dept = $this->dept;

        return view('accounting_purchasing.investment', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'dept' => $dept
        ))->with('page', 'Investment')->with('head', 'inv');
    }

  	public function fetch_investment(Request $request)
  	{
	     $tanggal = "";
	     $adddepartment = "";
	
	     if(strlen($request->get('datefrom')) > 0){
	          $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
	          $tanggal = "and acc_investments.submission_date >= '".$datefrom." 00:00:00' ";
	          if(strlen($request->get('dateto')) > 0){
	               $dateto = date('Y-m-d', strtotime($request->get('dateto')));
	               $tanggal = $tanggal."and acc_investments.submission_date  <= '".$dateto." 23:59:59' ";
	          }
	     }

	     if($request->get('department') != null) {
	          $departments = $request->get('department');
	          $deptlength = count($departments);
	          $department = "";

	          for($x = 0; $x < $deptlength; $x++) {
	               $department = $department."'".$departments[$x]."'";
	               if($x != $deptlength-1){
	                    $department = $department.",";
	               }
	          }
	          $adddepartment = "and acc_investments.Department in (".$department.") ";
	     }

	     $qry = "SELECT	* FROM acc_investments
	     WHERE acc_investments.deleted_at IS NULL ".$tanggal."".$adddepartment."
	     ORDER BY acc_investments.id DESC";

	     $invest = DB::select($qry);
	     
	     return DataTables::of($invest)

		 ->editColumn('submission_date',function($invest){
            return date('d F Y', strtotime($invest->submission_date));
          })

	     ->editColumn('status',function($invest){
	     	$id = $invest->id;
	     	if($invest->status == "aw"){
	            return '<a href="purchase_requisition/detail/'.$id.'" class="btn btn-primary btn-xs">Detail</a>';
          	}

          })
	     ->rawColumns(['status' => 'status'])
	     ->make(true);
	}

	public function create_investment(){
		    $title = 'Buat Form Investment';
        $title_jp = '投資申請書を作成';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

        $vendor = AccSupplier::select('acc_suppliers.*')
        ->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

        return view('accounting_purchasing.investment_create', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'vendor' => $vendor
        ))->with('page', 'Form Investment');
	}

	public function create_investment_post(Request $request)
    {
         try {
              $id_user = Auth::id();

              $inv = AccInvestment::create([
                   'investment_no' => $request->get('investment_no'),
                   'applicant_id' => $request->get('applicant_id'),
                   'applicant_name' => $request->get('applicant_name'),
                   'applicant_department' => $request->get('applicant_department'),
                   'reff_number' => $request->get('reff_number'),
                   'submission_date' => $request->get('submission_date'),
                   'category' => $request->get('category'),
                   'subject' => $request->get('subject'),
                   'type' => $request->get('type'),
                   'objective' => $request->get('objective'),
                   'objective_detail' => $request->get('objective_detail'),
                   'desc_supplier' => $request->get('desc_supplier'),
                   'created_by' => $id_user
              ]);

              $inv->save();

              $response = array(
                'status' => true,
                'datas' => "Berhasil",
                'id' => $inv->id
              );
              return Response::json($response);

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }
    }

  public function detail_investment($id)
	{
      $title = 'Detail Form Investment';
      $title_jp = '投資申請内容';

      $inv = AccInvestment::find($id);
      
      $emp = EmployeeSync::where('employee_id', $inv->applicant_id)
      ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

      $vendor = AccSupplier::select('acc_suppliers.*')
      ->whereNull('acc_suppliers.deleted_at')
      ->distinct()
      ->get();

      $items = db::select("select kode_item, kategori, deskripsi, spesifikasi from acc_items where deleted_at is null LIMIT 50");

      return view('accounting_purchasing.investment_detail', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'investment' => $inv,
          'employee' => $emp,
          'vendor' => $vendor,
          'items' => $items
      ))->with('page', 'Form Investment');
	}

	public function detail_investment_post(Request $request)
    {
         try {
              $id_user = Auth::id();

              $inv = AccInvestment::where('id',$request->get('id'))
              ->update([
                   'applicant_id' => $request->get('applicant_id'),
                   'applicant_name' => $request->get('applicant_name'),
                   'applicant_department' => $request->get('applicant_department'),
                   'reff_number' => $request->get('reff_number'),
                   'submission_date' => $request->get('submission_date'),
                   'category' => $request->get('category'),
                   'subject' => $request->get('subject'),
                   'type' => $request->get('type'),
                   'objective' => $request->get('objective'),
                   'objective_detail' => $request->get('objective_detail'),
                   'desc_supplier' => $request->get('desc_supplier'),
                   'created_by' => $id_user
              ]);


              $response = array(
                'status' => true,
                'datas' => "Berhasil"
              );
              return Response::json($response);

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }
    }



    //Item Invesment

    public function fetch_investment_item($id)
    {
        $investment = AccInvestment::find($id);

        $investment_item = AccInvestmentDetail::leftJoin("acc_investments","acc_investment_details.reff_number","=","acc_investments.reff_number")
        ->select('acc_investment_details.*')
        ->where('acc_investment_details.reff_number','=',$investment->reff_number)
        ->get();

        return DataTables::of($investment_item)

          ->editColumn('amount',function($investment_item){
            return $investment_item->amount. ' ,00';
          })
          
          ->addColumn('action', function($investment_item){
            return '
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$investment_item->id.')">Edit</button>
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$investment_item->id.',\''.$investment_item->reff_number.'\')">Delete</button>';
          })

      ->rawColumns(['amount' => 'amount','action' => 'action'])
      ->make(true);
    }

    public function getitemdesc(Request $request)
    {
      $html = array();
      $kode_item = AccItem::where('kode_item',$request->kode_item)->get();
      foreach ($kode_item as $item) {
          $html = array(
            'detail' => $item->deskripsi
          );

      }

      return json_encode($html);
    }

    public function create_investment_item(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $item = new AccInvestmentDetail([
                'reff_number' => $request->get('reff_number'),
                'kode_item' => $request->get('kode_item'),
                'detail' => $request->get('detail_item'),
                'qty' => $request->get('jumlah_item'),
                'price' => $request->get('price_item'),
                'amount' => $request->get('amount_item'),
                'created_by' => $id_user
            ]);

            $item->save();

            $response = array(
              'status' => true,
              'item' => $item
            );
            return Response::json($response);
        }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'item' => "Item already exist"
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'item' => "Item not created."
            );
             return Response::json($response);
           }
        }
    }

    public function fetch_investment_item_edit(Request $request)
    {
      $items = AccInvestmentDetail::find($request->get("id"));

      $response = array(
        'status' => true,
        'datas' => $items,
      );
      return Response::json($response);
    }

    public function edit_investment_item(Request $request)
    {
        try{
            $items = AccInvestmentDetail::find($request->get("id"));
            $items->kode_item = $request->get('kode_item');
            $items->detail = $request->get('detail_item');
            $items->qty = $request->get('jumlah_item');
            $items->price = $request->get('price_item');
            $items->amount = $request->get('amount_item');
            $items->save();

            $response = array(
              'status' => true,
              'datas' => "Berhasil",
            );
            return Response::json($response);

          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => $e->getMessage(),
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => $e->getMessage(),
            );
             return Response::json($response);
            }
        }
    }

    public function delete_investment_item(Request $request)
    {
      $items = AccInvestmentDetail::find($request->get("id"));
      $items->forceDelete();

      $response = array(
        'status' => true
      );
      return Response::json($response);
    }
}
