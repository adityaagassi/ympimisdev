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
use PDF;
use Excel;
use App\AccExchangeRate;
use App\AccItem;
use App\AccItemCategory;
use App\AccActual;
use App\AccActualLog;
use App\AccReceive;
use App\AccReceiveReport;
use App\AccBudget;
use App\AccBudgetHistory;
use App\AccBudgetTransfer;
use App\AccSupplier;
use App\AccPurchaseRequisition;
use App\AccPurchaseRequisitionItem;
use App\AccPurchaseOrder;
use App\AccPurchaseOrderDetail;
use App\AccInvestment;
use App\AccInvestmentDetail;
use App\AccInvestmentBudget;
use App\AccInvoice;
use App\AccInvoiceReceiveReport;
use App\AccInvoicePaymentTerm;
use App\AccPaymentRequest;
use App\AccCashPayment;

use App\MisInventory;

use App\EmployeeSync;
use App\UtilityItemNumber;
use App\UtilityOrder;

use App\FixedAssetClasification;
use App\FixedAssetRegistration;
use App\FixedAssetInvoice;
use App\FixedAssetInvoiceDetail;
use App\CodeGenerator;

use App\CanteenPurchaseRequisition;
use App\CanteenPurchaseRequisitionItem;
use App\CanteenBudgetHistory;
use App\CanteenPurchaseOrder;
use App\CanteenPurchaseOrderDetail;
use App\CanteenItem;
use App\CanteenReceive;
use Carbon\Carbon;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->dept = ['Management Information System Department', 'Accounting Department', 'Woodwind Instrument - Final Assembly (WI-FA) Department', 'Educational Instrument (EI) Department', 'General Affairs Department', 'Human Resources Department', 'Logistic Department', 'Maintenance Department', 'Woodwind Instrument - Key Parts Process (WI-KPP) Department', 'Procurement Department', 'Production Control Department', 'Production Engineering Department', 'Purchasing Control Department', 'Quality Assurance Department', 'Woodwind Instrument - Welding Process (WI-WP) Department', 'Woodwind Instrument - Body Parts Process (WI-BPP) Department', 'Woodwind Instrument - Surface Treatment (WI-ST) Department'];

        $this->uom = ['bag', 'bar', 'belt', 'box', 'Btg', 'Btl', 'buah', 'buku', 'Can', 'Case', 'cps', 'day', 'Drum', 'galon', 'gr','job', 'JRG', 'Kg', 'kgm', 'Lbr', 'lbs', 'month', 'License', 'Lot',  'ltr', 'lubang', 'm²', 'm3', 'Mtr', 'Pack', 'package', 'pad', 'pail', 'pair', 'pc', 'Pce', 'Pcs', 'Rim', 'roll', 'sample', 'Set','sheet', 'tbg', 'titik'];

        $this->transportation = ['AIR', 'BOAT', 'COURIER SERVICE', 'DHL', 'FEDEX', 'SUV-Car'];

        $this->delivery = ['CIF Surabaya', 'CIP', 'Cost And Freight ', 'Delivered At Frontier', 'Delivered Duty Paid', 'Delivered Duty Unpaid', 'Delivered Ex Quay', 'Ex Works', 'Ex Factory', 'Ex Ship', 'FRANCO', 'Franco', 'FOB', 'Flee Alongside Ship', 'Free Carrier (FCA)', 'Letter Of Credits','DAP Consignee (Surabaya Factory)'];

        // $this->dgm = 'PI1910003';
        // $this->gm = 'PI1206001';

        $this->dgm = 'PI0109004';
        $this->gm = 'PI1206001';
        $this->gm_acc = 'PI1712018';

        $this->manager_acc = 'PI9902017/Romy Agung Kurniawan'; //Pak Romy
        $this->dir_acc = 'PI1712018/Kyohei Iida'; //Pak Ida
        $this->presdir = 'PI1301001/Hiroshi Ura'; //Pak Ura
    }

    //==================================//
    //          Master supplier         //
    //==================================//
    public function master_supplier()
    {
        $title = 'Supplier';
        $title_jp = '調達会社';

        $status = AccSupplier::select('acc_suppliers.supplier_status')->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

        $city = AccSupplier::select('acc_suppliers.supplier_city')->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

        return view('accounting_purchasing.master.supplier', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'status' => $status,
            'city' => $city,
        ))->with('page', 'Supplier')
        ->with('head', 'Supplier');
    }

    public function fetch_supplier(Request $request)
    {
        $supplier = AccSupplier::orderBy('acc_suppliers.supplier_name', 'asc');

        if ($request->get('status') != null)
        {
            $supplier = $supplier->whereIn('acc_suppliers.supplier_status', $request->get('status'));
        }

        if ($request->get('city') != null)
        {
            $supplier = $supplier->whereIn('acc_suppliers.supplier_city', $request->get('city'));
        }

        $supplier = $supplier->select('*')
        ->get();

        return DataTables::of($supplier)
        ->addColumn('action', function ($supplier)
        {
            $id = $supplier->id;
            if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PCH" || Auth::user()->role_code == "PCH-SPL") {
                return ' 
                <a href="supplier/update/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a> 
                <a href="supplier/delete/' . $id . '" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</a>
                ';
            }else{
                return '-';       
            }
        })
        ->rawColumns(['action' => 'action'])

        ->make(true);
    }

    public function create_supplier()
    {
        $title = 'Create Supplier';
        $title_jp = '調達会社データを作成';

        return view('accounting_purchasing.master.create_supplier', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'Supplier');
    }

    public function create_supplier_post(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $supplier = AccSupplier::create(['vendor_code' => $request->get('vendor_code') ,
               'supplier_name' => $request->get('supplier_name') ,
               'supplier_address' => $request->get('supplier_address') ,
               'supplier_city' => $request->get('supplier_city') ,
               'supplier_phone' => $request->get('supplier_phone') ,
               'supplier_fax' => $request->get('supplier_fax') ,
               'contact_name' => $request->get('contact_name') ,
               'supplier_npwp' => $request->get('supplier_npwp') ,
               'supplier_duration' => $request->get('supplier_duration') ,
               'position' => $request->get('position') ,
               'supplier_status' => $request->get('supplier_status') ,
               'created_by' => $id_user
           ]);

            $supplier->save();

            $response = array(
                'status' => true,
                'datas' => "Berhasil"
            );
            return Response::json($response);

        }
        catch(QueryException $e)
        {
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
        try
        {
            $id_user = Auth::id();

            $inv = AccSupplier::where('id', $request->get('id'))
            ->update(['vendor_code' => $request->get('vendor_code') , 'supplier_name' => $request->get('supplier_name') , 'supplier_address' => $request->get('supplier_address') , 'supplier_city' => $request->get('supplier_city') , 'supplier_phone' => $request->get('supplier_phone') , 'supplier_fax' => $request->get('supplier_fax') , 'contact_name' => $request->get('contact_name') , 'supplier_npwp' => $request->get('supplier_npwp') , 'supplier_duration' => $request->get('supplier_duration') , 'position' => $request->get('position') , 'supplier_status' => $request->get('supplier_status') , 'created_by' => $id_user
        ]);

            $response = array(
                'status' => true,
                'datas' => "Berhasil"
            );
            return Response::json($response);

        }
        catch(QueryException $e)
        {
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
    //            Master Item           //
    //==================================//
    public function master_item()
    {
        $title = 'Purchase Item';
        $title_jp = '購入アイテム';

        // $uom = AccItem::select('acc_items.uom')
        //       ->whereNull('acc_items.deleted_at')
        //       ->distinct()
        //       ->get();
        $item_categories = AccItemCategory::select('acc_item_categories.*')->whereNull('acc_item_categories.deleted_at')
        ->get();

        return view('accounting_purchasing.master.purchase_item', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'uom' => $this->uom,
            'item_category' => $item_categories,
        ))->with('page', 'Purchase Item')
        ->with('head', 'Purchase Item');
    }

    public function fetch_item(Request $request)
    {
        $items = AccItem::select('acc_items.id', 'acc_items.kode_item', 'acc_items.kategori', 'acc_items.deskripsi', 'acc_items.uom', 'acc_items.spesifikasi', 'acc_items.harga', 'acc_items.lot', 'acc_items.moq', 'acc_items.leadtime', 'acc_items.currency');

        if ($request->get('keyword') != null)
        {
            $items = $items->where('deskripsi', 'like', '%' . $request->get('keyword') . '%')
            ->orWhere('spesifikasi', 'like', '%' . $request->get('keyword') . '%');
        }

        if ($request->get('category') != null)
        {
            $items = $items->where('acc_items.kategori', $request->get('category'));
        }

        if ($request->get('uom') != null)
        {
            $items = $items->whereIn('acc_items.uom', $request->get('uom'));
        }

        $items = $items->orderBy('acc_items.id', 'ASC')
        ->get();

        return DataTables::of($items)
        ->addColumn('action', function ($items)
        {
            $id = $items->id;

            if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PCH" || Auth::user()->role_code == "PCH-SPL") {
                return ' 
                <a href="purchase_item/update/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a> 
                <a href="purchase_item/delete/' . $id . '" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</a>
                ';
            }else{
                return '-';                
            }
        })
        ->addColumn('image', function ($items)
        {
            $item_code = $items->kode_item;

            if (file_exists(public_path() .'/images/purchase_item/'.$item_code.'.jpg')) {
                return '<img src="'.url('images/purchase_item').'/'.$item_code.'.jpg" width="250">';
            }
            else if (file_exists(public_path() .'/images/purchase_item/'.$item_code.'.png')) {
                return '<img src="'.url('images/purchase_item').'/'.$item_code.'.png" width="250">';
            }
            else{
                return '-';
            }

        })
        ->rawColumns(['action' => 'action','image' => 'image'])
        ->make(true);
    }

    public function create_item()
    {
        $title = 'Create Item';
        $title_jp = '購入アイテムを作成';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $item_categories = AccItemCategory::select('acc_item_categories.*')->whereNull('acc_item_categories.deleted_at')
        ->get();

        return view('accounting_purchasing.master.create_purchase_item', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'item_category' => $item_categories,
            'uom' => $this->uom
        ))
        ->with('page', 'Purchase Item');
    }

    public function create_item_post(Request $request)
    {
        try
        {
            // $tujuan_upload = 'images/purchase_item';

            // if ($request->file('item_file') != NULL)
            // {
            //     $file = $request->file('item_file');
            //     $nama = $file->getClientOriginalName();
            //     $file->move($tujuan_upload,$nama);
            // }

            $id_user = Auth::id();

            $item = AccItem::create(['kode_item' => $request->get('item_code') , 'kategori' => $request->get('item_category') , 'deskripsi' => $request->get('item_desc') , 'uom' => $request->get('item_uom') , 'spesifikasi' => $request->get('item_spec') , 'harga' => $request->get('item_price') , 'lot' => $request->get('item_lot') , 'moq' => $request->get('item_moq') , 'leadtime' => $request->get('item_leadtime') , 'currency' => $request->get('item_currency') , 'created_by' => $id_user]);

            $item->save();

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
                'id' => $item->id
            );
            return Response::json($response);

        }
        catch(QueryException $e)
        {
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
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $item_categories = AccItemCategory::select('acc_item_categories.*')->whereNull('acc_item_categories.deleted_at')
        ->get();

        return view('accounting_purchasing.master.edit_purchase_item', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'item' => $item,
            'employee' => $emp,
            'item_category' => $item_categories,
            'uom' => $this->uom
        ))
        ->with('page', 'Purchase Item');
    }

    public function update_item_post(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $inv = AccItem::where('id', $request->get('id'))
            ->update(['kode_item' => $request->get('item_code') , 'kategori' => $request->get('item_category') , 'deskripsi' => $request->get('item_desc') , 'uom' => $request->get('item_uom') , 'spesifikasi' => $request->get('item_spec') , 'harga' => $request->get('item_price') , 'lot' => $request->get('item_lot') , 'moq' => $request->get('item_moq') , 'leadtime' => $request->get('item_leadtime') , 'currency' => $request->get('item_currency') , 'created_by' => $id_user]);

            $response = array(
                'status' => true,
                'datas' => "Berhasil"
            );
            return Response::json($response);

        }
        catch(QueryException $e)
        {
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

        $query = "SELECT kode_item FROM `acc_items` where kategori='$kategori' order by kode_item DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if ($nomorurut != null)
        {
            $nomor = substr($nomorurut[0]->kode_item, -3);
            $nomor = $nomor + 1;
            $nomor = sprintf('%03d', $nomor);

        }
        else
        {
            $nomor = "001";
        }

        $result['no_urut'] = $nomor;

        return json_encode($result);
    }

    //==================================//
    //       Create Item Category       //
    //==================================//
    public function create_item_category()
    {
        $title = 'Create Item Category';
        $title_jp = '購入アイテムの種類を作成';

        return view('accounting_purchasing.master.create_category_item', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'Purchase Item');
    }

    public function create_item_category_post(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $item_category = AccItemCategory::create(['category_id' => $request->get('category_id') , 'category_name' => $request->get('category_name') , 'created_by' => $id_user]);

            $item_category->save();

            $response = array(
                'status' => true,
                'datas' => "Berhasil"
            );
            return Response::json($response);

        }
        catch(QueryException $e)
        {
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
    public function exchange_rate()
    {
        $title = 'Exchange Rate';
        $title_jp = '為替レート';

        return view('accounting_purchasing.master.exchange_rate', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Exchange Rate')
        ->with('head', 'Exchange Rate');
    }

    public function fetch_exchange_rate(Request $request)
    {
        $exchange = AccExchangeRate::orderBy('acc_exchange_rates.id', 'desc');

        if ($request->get('tanggal') != null)
        {
            $exchange = $exchange->where('acc_exchange_rates.periode', $request->get('tanggal') . "-01");
        }

        $exchange = $exchange->select('*')
        ->get();

        return DataTables::of($exchange)
        ->editColumn('periode', function ($exchange)
        {
            return date('Y F', strtotime($exchange->periode));
        })
        ->addColumn('action', function ($exchange)
        {
            $id = $exchange->id;
            return '  
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete(' . $id . ')"><i class="fa fa-trash"></i> Delete</button>
            ';
        })
        ->rawColumns(['action' => 'action'])

        ->make(true);
    }

    public function create_exchange_rate(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $rate = AccExchangeRate::create(['periode' => $request->get('periode') . "-01", 'currency' => $request->get('currency') , 'rate' => $request->get('rate') , 'created_by' => $id_user]);

            $rate->save();

            $response = array(
                'status' => true,
                'datas' => "Berhasil"
            );
            return Response::json($response);

        }
        catch(QueryException $e)
        {
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
    //       Purchase Requisition       //
    //==================================//
    

    public function purchase_requisition()
    {
        $title = 'Purchase Requisition';
        $title_jp = '購入申請';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $staff = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
            where end_date is null and (position like '%Staff%')");

        $items = db::select("select kode_item, kategori, deskripsi from acc_items where deleted_at is null");
        $dept = $this->dept;

        return view('accounting_purchasing.purchase_requisition', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'items' => $items,
            'dept' => $dept,
            'staff' => $staff,
            'uom' => $this->uom
        ))
        ->with('page', 'Purchase Requisition')
        ->with('head', 'PR');
    }

    public function fetch_purchase_requisition(Request $request)
    {
        $tanggal = "";
        $adddepartment = "";
        $restrict_dept = "";

        if (strlen($request->get('datefrom')) > 0)
        {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and A.submission_date >= '" . $datefrom . " 00:00:00' ";
            if (strlen($request->get('dateto')) > 0)
            {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and A.submission_date  <= '" . $dateto . " 23:59:59' ";
            }
        }

        if ($request->get('department') != null)
        {
            $departments = $request->get('department');
            $deptlength = count($departments);
            $department = "";

            for ($x = 0;$x < $deptlength;$x++)
            {
                $department = $department . "'" . $departments[$x] . "'";
                if ($x != $deptlength - 1)
                {
                    $department = $department . ",";
                }
            }
            $adddepartment = "and A.department in (" . $department . ") ";
        }


        //Get Employee Department
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('department')
        ->first();

        if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PCH" || strpos($emp_dept->department, 'Procurement Department') !== false || strpos($emp_dept->department, 'Purchasing Control Department') !== false) {
            $restrict_dept = "";
        }
        else{
            $restrict_dept = "and department like '%".$emp_dept->department."%'";
        }


        $qry = "SELECT  * FROM acc_purchase_requisitions A WHERE A.deleted_at IS NULL " . $tanggal . "" . $adddepartment . "" . $restrict_dept. " order by A.id DESC";

        $pr = DB::select($qry);

        return DataTables::of($pr)
        ->editColumn('submission_date', function ($pr)
        {
            return $pr->submission_date;
        })
        ->editColumn('note', function ($pr)
        {
            $note = "";
            if ($pr->note != null)
            {
                $note = $pr->note;
            }
            else
            {
                $note = '-';
            }

            return $note;
        })
        ->editColumn('status', function ($pr)
        {
            $id = $pr->id;

            if ($pr->posisi == "user" && $pr->status == "approval")
            {
                return '<label class="label label-danger">Not Sent</a>';
            }
            else if($pr->posisi == "manager" && $pr->status == "approval")
            {
                return '<label class="label label-warning">Approval Manager</a>';
            }
            else if($pr->posisi == "dgm" && $pr->status == "approval")
            {
                return '<label class="label label-warning">Approval DGM</a>';
            }
            else if($pr->posisi == "gm" && $pr->status == "approval")
            {
                return '<label class="label label-warning">Approval GM</a>';
            }
            else if ($pr->status == "approval_acc")
            {
                return '<label class="label label-info">Diverifikasi Purchasing</a>';
            }
            else if ($pr->status == "received")
            {
                return '<label class="label label-success">Diterima Purchasing</a>';
            }

        })
        ->addColumn('action', function ($pr)
        {
            $id = $pr->id;

            // <a href="purchase_requisition/detail/' . $id . '" class="btn btn-info btn-xs" data-toggle="tooltip" title="Detail PR"><i class="fa fa-eye"></i></a>
            // <a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="editPR(' . $id . ')" data-toggle="tooltip" title="Detail PR"><i class="fa fa-edit"></i> Detail</a>

            if ($pr->posisi == "user" && $pr->status == "approval") {
                return '
                <button class="btn btn-xs btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;"  onclick="sendEmail('.$id.')"><i class="fa fa-envelope"></i> Send Email</button>
                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="editPR(' . $id . ')" data-toggle="tooltip" title="Edit PR"><i class="fa fa-edit"></i> Edit PR</a>
                <a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
                <a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="deleteConfirmationPR('.$id.')" data-toggle="modal" data-target="#modalDeletePR"  title="Delete PR"><i class="fa fa-trash"></i> Delete PR</a>
                ';

            }

            // if($pr->status == "approval"){
            //     return '

            //     <a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
            //     ';
            // }
            else{
                return '
                <a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
                ';
            }


        })
        ->editColumn('file', function ($pr)
        {

            $data = json_decode($pr->file);

            $fl = "";

            if ($pr->file != null)
            {
                for ($i = 0;$i < count($data);$i++)
                {
                    $fl .= '<a href="files/pr/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
                }
            }
            else
            {
                $fl = '-';
            }

            return $fl;
        })
        ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file'])
        ->make(true);
    }

    public function fetchItemList(Request $request)
    {
        $items = AccItem::select('acc_items.kode_item', 'acc_items.deskripsi')
        ->get();

        $response = array(
            'status' => true,
            'item' => $items
        );

        return Response::json($response);
    }

    public function prgetitemdesc(Request $request)
    {
        $html = array();
        $kode_item = AccItem::where('kode_item', $request->kode_item)
        ->get();
        foreach ($kode_item as $item)
        {
            $html = array(
                'deskripsi' => $item->deskripsi,
                'spesifikasi' => $item->spesifikasi,
                'uom' => $item->uom,
                'price' => $item->harga,
                'currency' => $item->currency,
                'moq' => $item->moq,
                'peruntukan' => $item->peruntukan,
                'kebutuhan' => $item->kebutuhan,
            );

        }

        return json_encode($html);
    }

    public function fetchBudgetList(Request $request)
    {
        $budgets = AccBudget::select('acc_budgets.budget_no', 'acc_budgets.description')
        ->where('category', '=', 'Expenses')
        ->where('periode', '=', 'FY198')
        ->distinct();

        if ($request->get('department') == "General Affairs Department") {
            $dept = "Human Resources Department";
            $budgets->where('department', '=', $dept);
        }
        else if($request->get('department') == "Purchasing Control Department") {
            $dept = "Procurement Department";
            $budgets->where('department', '=', $dept);
        }
        // else if($request->get('department') == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
        //     $dept = "Woodwind Instrument - Key Parts Process (WI-KPP) Department";
        //     $budgets->where('department', '=', $dept);
        // }
        // else if($request->get('department') == "Woodwind Instrument - Surface Treatment (WI-ST) Department") {
        //     $dept = "Woodwind Instrument - Welding Process (WI-WP) Department";
        //     $budgets->where('department', '=', $dept);
        // }
        else if ($request->get('department') == "Management Information System Department") {
        }
        else{
            $dept = $request->get('department');
            $budgets->where('department', '=', $dept);
        }

        $budget_all = $budgets->get();

        $response = array(
            'status' => true,
            'budget' => $budget_all
        );

        return Response::json($response);
    }

    public function get_exchange_rate(Request $request)
    {
        $html = array();
        $date = date('Y-m') . "-01";

        if ($date == "")
        {

        }

        $rate_a = AccExchangeRate::where('periode', '=', $date)->orderBy('rate', 'DESC')
        ->get();

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
        $namabulan = date('F');
        $bulan = strtolower(date('M'));

        // $tglnow = date('Y-m-d');
        // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

        // foreach ($fy as $fys) {
        //     $fiscal = $fys->fiscal_year;
        // }

        $getbulan = AccBudget::select('budget_no', 'periode')
        ->where('budget_no', $request->budget_no)
        ->first();

        if ($getbulan->periode == "FY198") {
            $bulan = strtolower(date('M'));
        }
        else{
            $bulan = "apr";
        }

        // $date = date('Y-m-d', strtotime(date('Y-m-d') . ' + 21 days'));
        // $bulan = date("m", strtotime($date));

        $budget_no = AccBudget::SELECT('*',$bulan.'_sisa_budget as budget_now')
        ->where('budget_no', $request->budget_no)->get();

        foreach ($budget_no as $budget)
        {
            $html = array(
                'description' => $budget->description,
                'amount' => $budget->amount,
                'account' => $budget->account_name,
                'category' => $budget->category,
                'apr' => $budget->apr_sisa_budget,
                'may' => $budget->may_sisa_budget,
                'jun' => $budget->jun_sisa_budget,
                'jul' => $budget->jul_sisa_budget,
                'aug' => $budget->aug_sisa_budget,
                'sep' => $budget->sep_sisa_budget,
                'oct' => $budget->oct_sisa_budget,
                'nov' => $budget->nov_sisa_budget,
                'dec' => $budget->dec_sisa_budget,
                'jan' => $budget->jan_sisa_budget,
                'feb' => $budget->feb_sisa_budget,
                'mar' => $budget->mar_sisa_budget,
                'budget_now' => $budget->budget_now,
                'namabulan' => $namabulan,
                'periode' => $budget->periode
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
        $sect = $request->sect;
        $grp = $request->grp;

        $query = "SELECT no_pr FROM `acc_purchase_requisitions` where department = '$dept' and DATE_FORMAT(submission_date, '%y') = '$tahun' and month(submission_date) = '$bulan' order by id DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if ($nomorurut != null)
        {
            $nomor = substr($nomorurut[0]->no_pr, -3);
            $nomor = $nomor + 1;
            $nomor = sprintf('%03d', $nomor);
        }
        else
        {
            $nomor = "001";
        }

        if ($dept == "Management Information System Department")
        {
            $dept = "IT";
        }
        else if ($dept == "Accounting Department")
        {
            $dept = "AC";
        }
        else if ($dept == "Woodwind Instrument - Final Assembly (WI-FA) Department")
        {
            $dept = "AS";
        }
        else if ($dept == "Educational Instrument (EI) Department")
        {
            $dept = "EI";
        }
        else if ($dept == "General Affairs Department")
        {
            $dept = "GA";
        }
        else if ($dept == "Human Resources Department")
        {
            $dept = "HR";
        }
        else if ($dept == "Logistic Department")
        {
            $dept = "LG";
        }
        else if ($dept == "Maintenance Department")
        {
            $dept = "PM";
        }
        else if ($dept == "Woodwind Instrument - Key Parts Process (WI-KPP) Department")
        {
            $dept = "KP";
        }
        else if ($dept == "Woodwind Instrument - Body Parts Process (WI-BPP) Department")
        {
            $dept = "BP";
        }
        else if ($dept == "Procurement Department" || $dept == "Purchasing Control Department")
        {
            $dept = "PH";
        }
        else if ($dept == "Production Control Department")
        {
            $dept = "PC";
        }
        else if ($dept == "Production Engineering Department")
        {
            $dept = "PE";
        }
        else if ($dept == "Quality Assurance Department")
        {
            if ($grp == "Standardization Process Control Section") {
                $dept = "SR";
            }
            else if($sect == "Chemical Process Control Section"){
                $dept = "CM";
            }
            else{
                $dept = "QC";
            }
        }
        else if ($dept == "Woodwind Instrument - Welding Process (WI-WP) Department")
        {
            $dept = "WP";
        }
        else if ($dept == "Woodwind Instrument - Surface Treatment (WI-ST) Department"){
            $dept = "ST";
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

        try
        {
            $staff = null;
            $manager = null;
            $manager_name = null;
            $posisi = null;
            $dgm = null;
            $gm = null;

            //jika PE maka Pak Alok

            if($request->get('department') == "Production Engineering Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Maintenance Department' and position = 'manager'");
            }

            //Jika Pch / Proc maka pak imron
            else if($request->get('department') == "Purchasing Control Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Procurement Department' and position = 'manager'");
            }

            //Jika GA pak arief
            else if($request->get('department') == "General Affairs Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Human Resources Department' and position = 'manager'");
            }

            //Jika KP maka EI
            else if($request->get('department') == "Woodwind Instrument - Key Parts Process (WI-KPP) Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Educational Instrument (EI) Department' and position = 'manager'");
            }

            //Jika BP maka WP
            else if($request->get('department') == "Woodwind Instrument - Welding Process (WI-WP) Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Woodwind Instrument - Body Parts Process (WI-BPP) Department' and position = 'manager'");
            }

            else
            {
                // Get Manager
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '" . $request->get('department') . "' and position = 'manager'");
            }

            // Jika ada staff
            if ($request->get('staff') != "") {

                $posisi = "staff";
                $staff = $request->get('staff');

                foreach ($manag as $mg)
                {
                    $manager = $mg->employee_id;
                    $manager_name = $mg->name;
                }
            }

            //cek manager ada atau tidak

            else if ($manag != null)
            {
                // $posisi = "manager";
                $posisi = "user";

                foreach ($manag as $mg)
                {
                    $manager = $mg->employee_id;
                    $manager_name = $mg->name;
                }
            }


            // Jika gaada manager di departemen itu
            else
            {
                // $posisi = "dgm";
                $posisi = "user";
            }

            //Cek File
            $files = array();
            $file = new AccPurchaseRequisition();
            if ($request->file('reportAttachment') != NULL)
            {
                if ($files = $request->file('reportAttachment'))
                {
                    foreach ($files as $file)
                    {
                        $nama = $file->getClientOriginalName();
                        $file->move('files/pr', $nama);
                        $data[] = $nama;
                    }
                }
                $file->filename = json_encode($data);
            }
            else
            {
                $file->filename = NULL;
            }

            $submission_date = $request->get('submission_date');
            $po_date = date('Y-m-d', strtotime($submission_date . ' + 7 days'));


            if($request->get('department') == "Human Resources Department" || $request->get('department') == "General Affairs Department"){
                $dgm = null;

                //GM Pak Arief
                $getgm = EmployeeSync::select('employee_id', 'name', 'position')
                ->where('employee_id','=','PI9709001')
                ->first();

                $gm = $getgm->employee_id;
            }
            //if Production Support Division Maka GM Pak Budhi
            else if($request->get('department') == "Logistic Department" || $request->get('department') == "Production Control Department" || $request->get('department') == "Purchasing Control Department" || $request->get('department') == "Procurement Department" ){
                $dgm = null;
                $gm = $this->dgm;
            }
            //if accounting maka GM Pak IDA
            else if($request->get('department') == "Accounting Department"){
                $dgm = null;
                $gm = $this->gm_acc;

            }
            //Selain Itu GM Pak Hayakawa
            else{
                $dgm = $this->dgm;
                $gm = $this->gm;
            }


            $data = new AccPurchaseRequisition([
                'no_pr' => $request->get('no_pr') , 
                'emp_id' => $request->get('emp_id') , 
                'emp_name' => $request->get('emp_name') , 
                'department' => $request->get('department') , 
                'section' => $request->get('section') , 
                'submission_date' => $submission_date, 
                'po_due_date' => $po_date, 
                'note' => $request->get('note') , 
                'file' => $file->filename, 
                'file_pdf' => 'PR'.$request->get('no_pr').'.pdf', 
                'posisi' => $posisi, 
                'status' => 'approval', 
                'no_budget' => $request->get('budget_no'), 
                'staff' => $staff,
                'manager' => $manager,
                'manager_name' => $manager_name,
                'dgm' => $dgm, 
                'gm' => $gm, 
                'created_by' => $id
            ]);

            $data->save();

            // $mod_date = date('Y-m-d', strtotime($request->get('submission_date') . ' + 21 days'));

            for ($i = 1;$i <= $lop;$i++)
            {
                $item_code = "item_code" . $i;
                $item_desc = "item_desc" . $i;
                $item_spec = "item_spec" . $i;
                $item_stock = "item_stock" . $i;
                $item_request_date = "req_date" . $i;
                $item_currency = "item_currency" . $i;
                $item_currency_text = "item_currency_text" . $i;
                $item_price = "item_price" . $i;
                $item_qty = "qty" . $i;
                $item_uom = "uom" . $i;
                $item_amount = "amount" . $i;
                $peruntukan = "tujuan_peruntukan" . $i;
                $kebutuhan = "tujuan_kebutuhan" . $i;
                // $item_budget = "budget".$i;
                $status = "";
                //Jika ada value kosong
                if ($request->get($item_code) == "kosong")
                {
                    $request->get($item_code) == "";
                }

                //Jika item kosong
                if ($request->get($item_code) != null)
                {
                    $status = "fixed";
                }
                else
                {
                    $status = "sementara";
                }

                if ($request->get($item_currency) != "")
                {
                    $current = $request->get($item_currency);
                }
                else if ($request->get($item_currency_text) != "")
                {
                    $current = $request->get($item_currency_text);
                }

                //get only number
                // $price_real = preg_replace('/[^0-9]/', '', $request->get($item_price));
                // $amount = preg_replace('/[^0-9]/', '', $request->get($item_amount));


                $data2 = new AccPurchaseRequisitionItem([
                    'no_pr' => $request->get('no_pr') , 
                    'item_code' => $request->get($item_code) ,
                    'item_desc' => $request->get($item_desc) ,
                    'item_spec' => $request->get($item_spec) ,
                    'item_stock' => $request->get($item_stock) , 
                    'item_request_date' => $request->get($item_request_date), 
                    'item_currency' => $current, 
                    'item_price' => $request->get($item_price), 
                    'item_qty' => $request->get($item_qty),
                    'item_uom' => $request->get($item_uom),
                    'item_amount' => $request->get($item_amount),
                    'peruntukan' => $request->get($peruntukan),
                    'kebutuhan' => $request->get($kebutuhan),
                    'status' => $status,
                    'created_by' => $id
                ]);

                $data2->save();

                $dollar = "konversi_dollar" . $i;

                $getbulan = AccBudget::select('budget_no', 'periode')
                ->where('budget_no', $request->get('budget_no'))
                ->first();

                if ($getbulan->periode == "FY198") {
                    $month = strtolower(date('M'));
                }
                else{
                    $month = "apr";
                }

                // $month = strtolower(date("M",strtotime($request->get('submission_date'))));

                $data3 = new AccBudgetHistory([
                    'budget' => $request->get('budget_no'),
                    'budget_month' => $month,
                    'budget_date' => date('Y-m-d'),
                    'category_number' => $request->get('no_pr'),
                    'no_item' => $request->get($item_desc),
                    'beg_bal' => $request->get('budget'),
                    'amount' => $request->get($dollar),
                    'status' => 'PR',
                    'created_by' => $id
                ]);

                $data3->save();


                $updatekebutuhan = AccItem::where('kode_item','=',$request->get($item_code))->update([
                    'peruntukan' => $request->get($peruntukan),
                    'kebutuhan' => $request->get($kebutuhan)
                ]);

            }

            $totalPembelian = $request->get('TotalPembelian');
            if ($totalPembelian != null) {
                // $datePembelian = date('Y-m-d');
                // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$datePembelian'");
                // foreach ($fy as $fys) {
                //     $fiscal = $fys->fiscal_year;
                // }
                // $bulan = strtolower(date("M",strtotime($datePembelian))); //aug,sep,oct

                $getbulan = AccBudget::select('budget_no', 'periode')
                ->where('budget_no', $request->get('budget_no'))
                ->first();

                if ($getbulan->periode == "FY198") {
                    $bulan = strtolower(date('M'));
                    $fiscal = "FY198";
                }
                else{
                    $bulan = "apr";
                    $fiscal = "FY199";
                }

                $sisa_bulan = $bulan.'_sisa_budget';                    
                //get Data Budget Based On Periode Dan Nomor
                $budget = AccBudget::where('budget_no','=',$request->get('budget_no'))->first();
                //perhitungan 
                $total = $budget->$sisa_bulan - $totalPembelian;

                if ($total < 0 ) {
                    return false;
                }

                $dataupdate = AccBudget::where('budget_no',$request->get('budget_no'))->update([
                    $sisa_bulan => $total
                ]);
            }


            $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
            ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
            ->join('acc_budget_histories', function($join) {
               $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
               $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
           })
            ->where('acc_purchase_requisitions.id', '=', $data->id)
            ->distinct()
            ->get();

            $exchange_rate = AccExchangeRate::select('*')
            ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
            ->where('currency','!=','USD')
            ->orderBy('currency','ASC')
            ->get();

            //SELECT * FROM `acc_purchase_requisitions` left join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr join acc_budget_histories on acc_purchase_requisition_items.no_pr = acc_budget_histories.category_number and acc_purchase_requisition_items.item_desc = acc_budget_histories.no_item where acc_purchase_requisitions.id = "45" 

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'landscape');

            $pdf->loadView('accounting_purchasing.report.report_pr', array(
                'pr' => $detail_pr,
                'rate' => $exchange_rate
            ));

            $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

            // $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.staff = users.username where acc_purchase_requisitions.id = " . $data->id;
            // $mailtoo = DB::select($mails);

            // // Jika gaada staff
            // if ($mailtoo == null)
            // {   
            //     //ke manager
            //     $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.manager = users.username where acc_purchase_requisitions.id = " . $data->id;
            //     $mailtoo = DB::select($mails);

            //     // Jika Gaada Manager
            //     if ($mailtoo == null)
            //     { 
            //         // ke DGM
            //         $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = " . $data->id;
            //         $mailtoo = DB::select($mails);
            //     }

            // }

            // $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $data->id;
            // $purchaserequisition = db::select($isimail);

            // Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($purchaserequisition, 'purchase_requisition'));

            return redirect('/purchase_requisition')->with('status', 'PR Berhasil Dibuat')
            ->with('page', 'Purchase Requisition');
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_requisition')->with('error', $e->getMessage())
            ->with('page', 'Purchase Requisition');
        }
    }

    public function pr_send_email(Request $request){
        $pr = AccPurchaseRequisition::find($request->get('id'));

        try{
            if ($pr->posisi == "user")
            {
                $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.staff = users.username where acc_purchase_requisitions.id = ".$request->get('id');
                $mailtoo = DB::select($mails);

                $pr->posisi = "staff";

                    // Jika gaada staff
                if ($mailtoo == null)
                {   
                        //ke manager
                    $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.manager = users.username where acc_purchase_requisitions.id = ".$request->get('id');
                    $mailtoo = DB::select($mails);

                    $pr->posisi = "manager";

                        // Jika Gaada Manager
                    if ($mailtoo == null)
                    { 
                            // ke DGM
                        $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = ".$request->get('id');
                        $mailtoo = DB::select($mails);

                        $pr->posisi = "dgm";
                    }
                }

                $pr->save();

                $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id = ".$request->get('id');
                $purchaserequisition = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($purchaserequisition, 'purchase_requisition'));

                $response = array(
                  'status' => true,
                  'datas' => "Berhasil"
              );

                return Response::json($response);
            }
        } 
        catch (Exception $e) {
            $response = array(
              'status' => false,
              'datas' => "Gagal"
          );
            return Response::json($response);
        }
    }

    //==================================//
    //          Detail PR               //
    //==================================//
    public function detail_purchase_requisition($id)
    {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

        $pr = AccPurchaseRequisition::find($id);

        $items = AccPurchaseRequisitionItem::select('acc_purchase_requisition_items.*')->join('acc_purchase_requisitions', 'acc_purchase_requisition_items.no_pr', '=', 'acc_purchase_requisitions.no_pr')
        ->where('acc_purchase_requisitions.id', '=', $id)->get();

        return view('accounting_purchasing.detail_purchase_requisition', array(
            'pr' => $pr,
            'items' => $items
        ))->with('page', 'Purchase Requisition');
    }

    public function check_purchase_requisition($id)
    {
        $emp_id = Auth::user()->username;

        $pr = AccPurchaseRequisition::find($id);

        $items = AccPurchaseRequisitionItem::select('acc_purchase_requisition_items.*')->join('acc_purchase_requisitions', 'acc_purchase_requisition_items.no_pr', '=', 'acc_purchase_requisitions.no_pr')
        ->where('acc_purchase_requisitions.id', '=', $id)->get();

        $path = '/pr_list/' . $pr->file_pdf;            
        $file_path = asset($path);

        return view('accounting_purchasing.check_purchase_requisition', array(
            'pr' => $pr,
            'items' => $items,
            'file_path' => $file_path,
            'uom' => $this->uom
        ))->with('page', 'Purchase Requisition');
    }

    public function checked_purchase_requisition(Request $request, $id){

        $pr = AccPurchaseRequisition::find($id);

        if ($pr->posisi == "staff")
        {
            if ($pr->manager != null) {
                $pr->posisi = 'manager';

                $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.manager = users.username where acc_purchase_requisitions.id = " .$id;
                $mailtoo = DB::select($mails);
            }
            else{
                $pr->posisi = 'dgm';

                $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = " .$id;
                $mailtoo = DB::select($mails);
            }

            $pr->save();

            $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
            ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
            ->join('acc_budget_histories', function($join) {
               $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
               $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
           })
            ->where('acc_purchase_requisitions.id', '=', $id)
            ->distinct()
            ->get();

            $exchange_rate = AccExchangeRate::select('*')
            ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
            ->where('currency','!=','USD')
            ->orderBy('currency','ASC')
            ->get();


            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'landscape');

            $pdf->loadView('accounting_purchasing.report.report_pr', array(
                'pr' => $detail_pr,
                'rate' => $exchange_rate
            ));

            $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

            $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $id;
            $pr_isi = db::select($isimail);

            //CEK APAR

            $apar = UtilityItemNumber::pluck('item_number');

            $pr_item = AccPurchaseRequisitionItem::where('no_pr', '=', $pr_isi[0]->no_pr)->whereIn('item_code', $apar)->get();

            if ($pr_item->count() > 0) {
                $cek_apar = db::select("SELECT item_number, utility_code, utilities.id from utilities
                    left join utility_item_numbers on utilities.type = utility_item_numbers.utility_type and utilities.capacity = utility_item_numbers.utility_capacity and utilities.`order` = utility_item_numbers.remark
                    where utilities.remark = 'APAR'
                    and DATE_SUB(exp_date, INTERVAL 3 MONTH) >= DATE(now()) and DATE_SUB(exp_date, INTERVAL 3 MONTH) <= DATE_FORMAT(DATE_ADD(now(), INTERVAL 6 DAY),'%Y-%m-%d')");

                foreach ($pr_item as $itm) {
                    for ($i=1; $i < $pr_item->item_qty; $i++) { 
                        foreach ($cek_apar as $apar_exp) {
                            if ($apar_exp->item_number == $itm->item_code) {
                                $ord = new UtilityOrder;
                                $ord->utility_id = $apar_exp->id;
                                $ord->no_pr = $itm->no_pr;
                                $ord->pr_date = $itm->item_request_date;
                                $ord->created_by = 'PI1404002';

                                $ord->save();
                            }
                        }
                    }
                }
            }

            Mail::to($mailtoo)->send(new SendEmail($pr_isi, 'purchase_requisition'));

            return redirect('/purchase_requisition/check/'.$id)->with('status', 'PR Berhasil Dicek & Dikirim')
            ->with('page', 'Purchase Requisition');
        }

        else if($pr->posisi == "pch"){
            $pr->receive_date = date('Y-m-d');
            $pr->status = 'received';
            $pr->save();

            $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
            ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
            ->join('acc_budget_histories', function($join) {
               $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
               $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
           })
            ->where('acc_purchase_requisitions.id', '=', $id)
            ->distinct()
            ->get();

            $exchange_rate = AccExchangeRate::select('*')
            ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
            ->where('currency','!=','USD')
            ->orderBy('currency','ASC')
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'landscape');

            $pdf->loadView('accounting_purchasing.report.report_pr', array(
                'pr' => $detail_pr,
                'rate' => $exchange_rate
            ));

            $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");


            return redirect('/purchase_requisition/check/'.$id)->with('status', 'PR Sudah Berhasil Diterima')
            ->with('page', 'Purchase Requisition');
        }
    }


    //==================================//
    //          Report PR               //
    //==================================//
    public function report_purchase_requisition($id){

        $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
        ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
        ->join('acc_budget_histories', function($join) {
           $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
           $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
       })
        ->where('acc_purchase_requisitions.id', '=', $id)
        ->distinct()
        ->get();

        $exchange_rate = AccExchangeRate::select('*')
        ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
        ->where('currency','!=','USD')
        ->orderBy('currency','ASC')
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');

        $pdf->loadView('accounting_purchasing.report.report_pr', array(
            'pr' => $detail_pr,
            'rate' => $exchange_rate
        ));

        // $pdf->save(public_path() . "/pr/" . $reports[0]->id . ".pdf");

        $path = "pr_list/" . $detail_pr[0]->no_pr . ".pdf";
        return $pdf->stream("PR ".$detail_pr[0]->no_pr. ".pdf");

        // return view('accounting_purchasing.report.report_pr', array(
        //  'pr' => $detail_pr,
        // ))->with('page', 'PR')->with('head', 'PR List');
    }

    //==================================//
    //          Verifikasi PR           //
    //==================================//
    public function verifikasi_purchase_requisition($id)
    {
        $pr = AccPurchaseRequisition::find($id);

        $items = AccPurchaseRequisitionItem::select('acc_purchase_requisition_items.*')->join('acc_purchase_requisitions', 'acc_purchase_requisition_items.no_pr', '=', 'acc_purchase_requisitions.no_pr')
        ->where('acc_purchase_requisitions.id', '=', $id)->get();

        $path = '/pr_list/' . $pr->file_pdf;            
        $file_path = asset($path);

        return view('accounting_purchasing.verifikasi.pr_verifikasi', array(
            'pr' => $pr,
            'items' => $items,
            'file_path' => $file_path,
        ))->with('page', 'Purchase Requisition');
    }

    public function approval_purchase_requisition(Request $request, $id)
    {
        $approve = $request->get('approve');

        if ($approve == "1") {

            $pr = AccPurchaseRequisition::find($id);

            if ($pr->posisi == "manager")
            {

                if ($pr->dgm != null) {
                    $pr->posisi = "dgm";
                    $pr->approvalm = "Approved";
                    $pr->dateapprovalm = date('Y-m-d H:i:s');

                    $mailto = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = '" . $pr->id . "'";
                    $mails = DB::select($mailto);

                    foreach ($mails as $mail)
                    {
                        $mailtoo = $mail->email;
                    }
                }
                else{
                    $pr->posisi = "gm";
                    $pr->approvalm = "Approved";
                    $pr->dateapprovalm = date('Y-m-d H:i:s');

                    $mailto = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.gm = users.username where acc_purchase_requisitions.id = '" . $pr->id . "'";
                    $mails = DB::select($mailto);

                    foreach ($mails as $mail)
                    {
                        $mailtoo = $mail->email;
                    }
                }
            }

            else if ($pr->posisi == "dgm")
            {

                $pr->posisi = "gm";
                $pr->approvaldgm = "Approved";
                $pr->dateapprovaldgm = date('Y-m-d H:i:s');

                $mailto = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.gm = users.username where acc_purchase_requisitions.id = '" . $pr->id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }
            }

            else if ($pr->posisi == "gm")
            {

                $pr->posisi = 'pch';
                $pr->approvalgm = "Approved";
                $pr->dateapprovalgm = date('Y-m-d H:i:s');
                $pr->status = "approval_acc";

                //kirim email ke Mas Shega & Mas Hamzah
                $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = 'Purchasing Control Department' and (employee_id = 'PI1810020'  or employee_id = 'PI0904006')";
                $mailtoo = DB::select($mails);
            }

            $pr->save();

            $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
            ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
            ->join('acc_budget_histories', function($join) {
               $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
               $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
           })
            ->where('acc_purchase_requisitions.id', '=', $id)
            ->distinct()
            ->get();

            $exchange_rate = AccExchangeRate::select('*')
            ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
            ->where('currency','!=','USD')
            ->orderBy('currency','ASC')
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'landscape');

            $pdf->loadView('accounting_purchasing.report.report_pr', array(
                'pr' => $detail_pr,
                'rate' => $exchange_rate
            ));

            $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

            $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $pr->id;
            $pr_isi = db::select($isimail);

            Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($pr_isi, 'purchase_requisition'));

            return redirect('/purchase_requisition/verifikasi/' . $id)->with('status', 'PR Approved')
            ->with('page', 'Purchase Requisition');
        }
        else
        {
            return redirect('/purchase_requisition/verifikasi/' . $id)->with('error', 'PR Not Approved')
            ->with('page', 'Purchase Requisition');
        }
    }

    public function prapprovalmanager($id){
        $pr = AccPurchaseRequisition::find($id);
        try{
            if ($pr->posisi == "manager")
            {

                if ($pr->dgm != null) {
                    $pr->posisi = "dgm";
                    $pr->approvalm = "Approved";
                    $pr->dateapprovalm = date('Y-m-d H:i:s');

                    $mailto = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = '" . $pr->id . "'";
                    $mails = DB::select($mailto);

                    foreach ($mails as $mail)
                    {
                        $mailtoo = $mail->email;
                    }

                    $pr->save();

                    $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
                    ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
                    ->join('acc_budget_histories', function($join) {
                       $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
                       $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
                   })
                    ->where('acc_purchase_requisitions.id', '=', $id)
                    ->distinct()
                    ->get();

                    $exchange_rate = AccExchangeRate::select('*')
                    ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
                    ->where('currency','!=','USD')
                    ->orderBy('currency','ASC')
                    ->get();


                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->getDomPDF()->set_option("enable_php", true);
                    $pdf->setPaper('A4', 'landscape');

                    $pdf->loadView('accounting_purchasing.report.report_pr', array(
                        'pr' => $detail_pr,
                        'rate' => $exchange_rate
                    ));

                    $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

                    $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $pr->id;
                    $pr_isi = db::select($isimail);

                    Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($pr_isi, 'purchase_requisition'));

                    $message = 'PR dengan Nomor '.$pr->no_pr;
                    $message2 ='Berhasil di approve';
                }
                else{

                    $pr->posisi = "gm";
                    $pr->approvalm = "Approved";
                    $pr->dateapprovalm = date('Y-m-d H:i:s');
                    
                    $mailto = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.gm = users.username where acc_purchase_requisitions.id = '" . $pr->id . "'";
                    $mails = DB::select($mailto);

                    foreach ($mails as $mail)
                    {
                        $mailtoo = $mail->email;
                    }

                    $pr->save();

                    $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
                    ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
                    ->join('acc_budget_histories', function($join) {
                       $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
                       $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
                   })
                    ->where('acc_purchase_requisitions.id', '=', $id)
                    ->distinct()
                    ->get();

                    $exchange_rate = AccExchangeRate::select('*')
                    ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
                    ->where('currency','!=','USD')
                    ->orderBy('currency','ASC')
                    ->get();


                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->getDomPDF()->set_option("enable_php", true);
                    $pdf->setPaper('A4', 'landscape');

                    $pdf->loadView('accounting_purchasing.report.report_pr', array(
                        'pr' => $detail_pr,
                        'rate' => $exchange_rate
                    ));

                    $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

                    $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $pr->id;
                    $pr_isi = db::select($isimail);

                    Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($pr_isi, 'purchase_requisition'));

                    $message = 'PR dengan Nomor '.$pr->no_pr;
                    $message2 ='Berhasil di approve';

                    $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
                    ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
                    ->join('acc_budget_histories', function($join) {
                       $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
                       $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
                   })
                    ->where('acc_purchase_requisitions.id', '=', $id)
                    ->distinct()
                    ->get();

                    $exchange_rate = AccExchangeRate::select('*')
                    ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
                    ->where('currency','!=','USD')
                    ->orderBy('currency','ASC')
                    ->get();


                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->getDomPDF()->set_option("enable_php", true);
                    $pdf->setPaper('A4', 'landscape');

                    $pdf->loadView('accounting_purchasing.report.report_pr', array(
                        'pr' => $detail_pr,
                        'rate' => $exchange_rate
                    ));

                    $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");
                }


            }
            else{
                $message = 'PR dengan Nomor. '.$pr->no_pr;
                $message2 ='Sudah di approve/reject';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $pr->no_pr,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $pr->no_pr,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    public function prapprovaldgm($id){
        $pr = AccPurchaseRequisition::find($id);
        try{
            if ($pr->posisi == "dgm")
            {

                $pr->posisi = "gm";
                $pr->approvaldgm = "Approved";
                $pr->dateapprovaldgm = date('Y-m-d H:i:s');

                $mailto = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.gm = users.username where acc_purchase_requisitions.id = '" . $pr->id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }

                $pr->save();

                $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
                ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
                ->join('acc_budget_histories', function($join) {
                   $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
                   $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
               })
                ->where('acc_purchase_requisitions.id', '=', $id)
                ->distinct()
                ->get();

                $exchange_rate = AccExchangeRate::select('*')
                ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
                ->where('currency','!=','USD')
                ->orderBy('currency','ASC')
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'landscape');

                $pdf->loadView('accounting_purchasing.report.report_pr', array(
                    'pr' => $detail_pr,
                    'rate' => $exchange_rate
                ));

                $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

                $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $pr->id;
                $pr_isi = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($pr_isi, 'purchase_requisition'));

                $message = 'PR dengan Nomor '.$pr->no_pr;
                $message2 ='Berhasil di approve';

                $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
                ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
                ->join('acc_budget_histories', function($join) {
                   $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
                   $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
               })
                ->where('acc_purchase_requisitions.id', '=', $id)
                ->distinct()
                ->get();

                $exchange_rate = AccExchangeRate::select('*')
                ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
                ->where('currency','!=','USD')
                ->orderBy('currency','ASC')
                ->get();


                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'landscape');

                $pdf->loadView('accounting_purchasing.report.report_pr', array(
                    'pr' => $detail_pr,
                    'rate' => $exchange_rate
                ));

                $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");
            }
            else{
                $message = 'PR dengan Nomor. '.$pr->no_pr;
                $message2 ='Sudah di approve/reject';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $pr->no_pr,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $pr->no_pr,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    public function prapprovalgm($id){
        $pr = AccPurchaseRequisition::find($id);
        try{
            if ($pr->posisi == "gm")
            {
                $pr->posisi = 'pch';
                $pr->approvalgm = "Approved";
                $pr->dateapprovalgm = date('Y-m-d H:i:s');
                $pr->status = "approval_acc";

                //kirim email ke Mas Shega & Mas Hamzah
                $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = 'Purchasing Control Department' and (employee_id = 'PI1810020'  or employee_id = 'PI0904006')";
                $mailtoo = DB::select($mails);

                $pr->save();

                $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
                ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
                ->join('acc_budget_histories', function($join) {
                   $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
                   $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
               })
                ->where('acc_purchase_requisitions.id', '=', $id)
                ->distinct()
                ->get();

                $exchange_rate = AccExchangeRate::select('*')
                ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
                ->where('currency','!=','USD')
                ->orderBy('currency','ASC')
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'landscape');

                $pdf->loadView('accounting_purchasing.report.report_pr', array(
                    'pr' => $detail_pr,
                    'rate' => $exchange_rate
                ));

                $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

                $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $pr->id;
                $pr_isi = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($pr_isi, 'purchase_requisition'));

                $message = 'PR No '.$pr->no_pr;
                $message2 ='Successfully Approved';

                $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
                ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
                ->join('acc_budget_histories', function($join) {
                   $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
                   $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
               })
                ->where('acc_purchase_requisitions.id', '=', $id)
                ->distinct()
                ->get();

                $exchange_rate = AccExchangeRate::select('*')
                ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
                ->where('currency','!=','USD')
                ->orderBy('currency','ASC')
                ->get();


                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'landscape');

                $pdf->loadView('accounting_purchasing.report.report_pr', array(
                    'pr' => $detail_pr,
                    'rate' => $exchange_rate
                ));

                $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");
            }
            else{
                $message = 'PR dengan Nomor. '.$pr->no_pr;
                $message2 ='Sudah di approve/reject';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $pr->no_pr,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $pr->no_pr,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    public function prreject(Request $request, $id)
    {
        $pr = AccPurchaseRequisition::find($id);

        if ($pr->posisi == "manager" || $pr->posisi == "dgm" || $pr->posisi == "gm")
        {
            $pr->datereject = date('Y-m-d H:i:s');
            $pr->posisi = "user";
            $pr->approvalm = null;
            $pr->dateapprovalm = null;
            $pr->approvaldgm = null;
            $pr->dateapprovaldgm = null;
        }

        $pr->save();

        $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $pr->id;
        $tolak = db::select($isimail);

        //kirim email ke User
        $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.emp_id = users.username where acc_purchase_requisitions.id ='" . $pr->id . "'";
        $mailtoo = DB::select($mails);

        Mail::to($mailtoo)->send(new SendEmail($tolak, 'purchase_requisition'));

        $message = 'PR dengan Nomor. '.$pr->no_pr;
        $message2 ='Tidak Disetujui';

        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $pr->no_pr,
            'message' => $message,
            'message2' => $message2,
        ))->with('page', 'Approval');

    }

    

    public function reject_purchase_requisition(Request $request, $id)
    {
        $alasan = $request->get('alasan');

        $pr = AccPurchaseRequisition::find($id);

        if ($pr->posisi == "manager" || $pr->posisi == "dgm" || $pr->posisi == "gm")
        {
            $pr->alasan = $alasan;
            $pr->datereject = date('Y-m-d H:i:s');
            $pr->posisi = "user";
            $pr->approvalm = null;
            $pr->dateapprovalm = null;
            $pr->approvaldgm = null;
            $pr->dateapprovaldgm = null;
        }

        $pr->save();

        $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $pr->id;
        $tolak = db::select($isimail);

        //kirim email ke User
        $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.emp_id = users.username where acc_purchase_requisitions.id ='" . $pr->id . "'";
        $mailtoo = DB::select($mails);

        Mail::to($mailtoo)->send(new SendEmail($tolak, 'purchase_requisition'));
        return redirect('/purchase_requisition/verifikasi/' . $id)->with('status', 'PR Not Approved')
        ->with('page', 'Purchase Requisition');
    }

    public function edit_purchase_requisition(Request $request)
    {
        $purchase_requistion = AccPurchaseRequisition::find($request->get('id'));
        $purchase_requistion_item = AccPurchaseRequisition::select('acc_purchase_requisition_items.*','acc_budget_histories.budget', 'acc_budget_histories.budget_month', 'acc_budget_histories.budget_date', 'acc_budget_histories.category_number','acc_budget_histories.no_item','acc_budget_histories.amount','acc_budget_histories.beg_bal','acc_purchase_requisition_items.peruntukan','acc_purchase_requisition_items.kebutuhan')
        ->join('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
        // ->leftJoin('acc_items', 'acc_purchase_requisition_items.item_code', '=', 'acc_items.kode_item')
        ->join('acc_budget_histories', function($join) {
           $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
           $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
       })
        ->where('acc_purchase_requisitions.id', '=', $request->get('id'))
        ->whereNull('acc_purchase_requisition_items.sudah_po')
        ->get();

        $response = array(
            'status' => true,
            'purchase_requisition' => $purchase_requistion,
            'purchase_requisition_item' => $purchase_requistion_item
        );
        return Response::json($response);
    }

    public function edit_investment_po(Request $request)
    {
        $investment = AccInvestment::find($request->get('id'));
        $investment_detail = AccInvestment::select('acc_investment_details.*')
        ->join('acc_investment_details','acc_investments.reff_number', '=','acc_investment_details.reff_number')
        ->where('acc_investments.id','=',$request->get('id'))
        ->whereNull('acc_investment_details.sudah_po')
        ->get();;

        $response = array(
            'status' => true,
            'investment' => $investment,
            'investment_detail' => $investment_detail
        );
        return Response::json($response);
    }

    public function detail_pr_po(Request $request)
    {
        $purchase_requistion = AccPurchaseRequisition::find($request->get('id'));
        $purchase_requistion_item = AccPurchaseRequisition::select('acc_purchase_requisition_items.*','acc_budget_histories.budget', 'acc_budget_histories.budget_month', 'acc_budget_histories.budget_date', 'acc_budget_histories.category_number','acc_budget_histories.no_item','acc_budget_histories.amount','acc_budget_histories.beg_bal','acc_purchase_requisition_items.peruntukan','acc_purchase_requisition_items.kebutuhan')
        ->join('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
        // ->leftJoin('acc_items', 'acc_purchase_requisition_items.item_code', '=', 'acc_items.kode_item')
        ->join('acc_budget_histories', function($join) {
           $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
           $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
       })
        ->where('acc_purchase_requisitions.id', '=', $request->get('id'))
        ->get();

        $response = array(
            'status' => true,
            'purchase_requisition' => $purchase_requistion,
            'purchase_requisition_item' => $purchase_requistion_item
        );
        return Response::json($response);
    }

    public function update_purchase_requisition(Request $request)
    {
        $id = Auth::id();
        $lop2 = $request->get('lop2');
        $lop = explode(',', $request->get('looping'));
        try
        {
            foreach ($lop as $lp)
            {
                $item_code = "item_code_edit" . $lp;
                $item_desc = "item_desc_edit" . $lp;
                $item_spec = "item_spec_edit" . $lp;
                $tujuan_stock = "tujuan_stock_edit" . $lp;
                $item_uom = "uom_edit" . $lp;
                $item_req = "req_date_edit" . $lp;
                $item_qty = "qty_edit" . $lp;
                $item_price = "item_price_edit" . $lp;
                $item_amount = "amount_edit" . $lp;
                $tujuan_peruntukan = "tujuan_peruntukan_edit" . $lp;
                $tujuan_kebutuhan = "tujuan_kebutuhan_edit" . $lp;

                // $amount = preg_replace('/[^0-9]/', '', $request->get($item_amount));

                $data2 = AccPurchaseRequisitionItem::where('id', $lp)->update([
                  'item_code' => $request->get($item_code), 
                  'item_desc' => $request->get($item_desc), 
                  'item_spec' => $request->get($item_spec),
                  'item_stock' => $request->get($tujuan_stock), 
                  'item_uom' => $request->get($item_uom), 
                  'item_request_date' => $request->get($item_req), 
                  'item_qty' => $request->get($item_qty),
                  'item_price' => $request->get($item_price),
                  'item_amount' => $request->get($item_amount),
                  'peruntukan' => $request->get($tujuan_peruntukan), 
                  'kebutuhan' => $request->get($tujuan_kebutuhan), 
                  'created_by' => $id
              ]);

                $dataupdate_item = AccItem::where('kode_item', $request->get($item_code))->update([
                  'peruntukan' => $request->get($tujuan_peruntukan), 
                  'kebutuhan' => $request->get($tujuan_kebutuhan)
              ]);

            }

            for ($i = 2;$i <= $lop2;$i++)
            {

                $item_code = "item_code" . $i;
                $item_desc = "item_desc" . $i;
                $item_spec = "item_spec" . $i;
                $item_req = "req_date" . $i;
                $item_currency = "item_currency" . $i;
                $item_currency_text = "item_currency_text" . $i;
                $item_price = "item_price" . $i;
                $item_qty = "qty" . $i;
                $item_uom = "uom" . $i;
                $item_amount = "amount" . $i;
                $dollar = "konversi_dollar" . $i;
                $peruntukan = "tujuan_peruntukan" . $i;
                $kebutuhan = "tujuan_kebutuhan" . $i;
                $tujuan_stock = "tujuan_stock" . $i;
                $status = "";

                //Jika ada value kosong
                if ($request->get($item_code) == "kosong")
                {
                    $request->get($item_code) == "";
                }

                //Jika item kosong
                if ($request->get($item_code) != null)
                {
                    $status = "fixed";
                }
                else
                {
                    $status = "sementara";
                }

                if ($request->get($item_currency) != "")
                {
                    $current = $request->get($item_currency);
                }
                else if ($request->get($item_currency_text) != "")
                {
                    $current = $request->get($item_currency_text);
                }

                //get only number
                // $price_real = preg_replace('/[^0-9]/', '', $request->get($item_price));
                // $amount = preg_replace('/[^0-9]/', '', $request->get($item_amount));



                $updatekebutuhan = AccItem::where('kode_item','=',$request->get($item_code))->update([
                    'peruntukan' => $request->get($peruntukan),
                    'kebutuhan' => $request->get($kebutuhan)
                ]);

                $data2 = new AccPurchaseRequisitionItem([
                    'no_pr' => $request->get('no_pr_edit') , 
                    'item_code' => $request->get($item_code) , 
                    'item_desc' => $request->get($item_desc) , 
                    'item_spec' => $request->get($item_spec) ,
                    'item_stock' => $request->get($tujuan_stock) , 
                    'item_request_date' => $request->get($item_req) , 
                    'item_currency' => $current,
                    'item_price' => $request->get($item_price),
                    'item_qty' => $request->get($item_qty) , 
                    'item_uom' => $request->get($item_uom) , 
                    'item_amount' => $request->get($item_amount), 
                    'peruntukan' => $request->get($peruntukan) , 
                    'kebutuhan' => $request->get($kebutuhan), 
                    'status' => $status, 
                    'created_by' => $id
                ]);

                $data2->save();

                // $datenow = date('Y-m-d');

                // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$datenow'");

                // foreach ($fy as $fys) {
                //     $fiscal = $fys->fiscal_year;
                // }

                // $bulan = strtolower(date("M",strtotime($datenow)));


                $getbulan = AccBudget::select('budget_no', 'periode')
                ->where('budget_no', $request->get('no_budget_edit'))
                ->first();

                if ($getbulan->periode == "FY198") {
                    $bulan = strtolower(date('M'));
                }
                else{
                    $bulan = "apr";
                }

                $sisa_bulan = $bulan.'_sisa_budget';

                 //get Data Budget Based On Periode Dan Nomor
                $budgetdata = AccBudget::where('budget_no','=',$request->get('no_budget_edit'))->first();

                //Get Amount Di PO
                $total_dollar = $request->get($dollar);

                $totalminusPO = $budgetdata->$sisa_bulan - $total_dollar;

                // Setelah itu update data budgetnya dengan yang actual
                $dataupdate = AccBudget::where('budget_no',$request->get('no_budget_edit'))
                ->update([
                    $sisa_bulan => $totalminusPO
                ]);

                // $month = strtolower(date("M",strtotime($request->get('tgl_pengajuan_edit'))));
                $begbal = $request->get('SisaBudgetEdit') + $request->get('TotalPembelianEdit');

                $getbulan = AccBudget::select('budget_no', 'periode')
                ->where('budget_no', $request->get('no_budget_edit'))
                ->first();

                if ($getbulan->periode == "FY198") {
                    $month = strtolower(date('M'));
                }
                else{
                    $month = "apr";
                }

                $data3 = new AccBudgetHistory([
                    'budget' => $request->get('no_budget_edit'),
                    'budget_month' => $month,
                    'budget_date' => date('Y-m-d'),
                    'category_number' => $request->get('no_pr_edit'),
                    'beg_bal' => $begbal,
                    'no_item' => $request->get($item_desc),
                    'amount' => $request->get($dollar),
                    'status' => 'PR',
                    'created_by' => $id
                ]);

                $data3->save();
            }

            $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
            ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
            ->join('acc_budget_histories', function($join) {
               $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
               $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
           })
            ->where('acc_purchase_requisitions.id', '=', $request->get('id_edit_pr'))
            ->distinct()
            ->get();

            $exchange_rate = AccExchangeRate::select('*')
            ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
            ->where('currency','!=','USD')
            ->orderBy('currency','ASC')
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'landscape');

            $pdf->loadView('accounting_purchasing.report.report_pr', array(
                'pr' => $detail_pr,
                'rate' => $exchange_rate
            ));

            $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

            // for ($i = 2;$i <= $lop2;$i++)
            // {
            //     $item_code = "item_code" . $i;
            //     $item_desc = "item_desc" . $i;
            //     $item_spec = "item_spec" . $i;
            //     $item_uom = "uom" . $i;

            //     $data = new AccPurchaseRequisitionItem(['item_code' => $request->get($item_code) , 'item_desc' => $request->get($item_desc) , 'item_spec' => $request->get($item_spec) ,'item_uom' => $request->get($item_uom) , 'created_by' => $id]);

            //     $data->save();
            // }

            return redirect('/purchase_requisition')
            ->with('status', 'Purchase Requisition Berhasil Dirubah')
            ->with('page', 'Purchase Requisition');
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_requisition')->with('error', $e->getMessage())
            ->with('page', 'Purchase Requisition');
        }
    }

    public function jurnal_po(){
        $title = "Jurnal PO";
        $title_jp = "";

        return view('accounting_purchasing.report.jurnal_po', array(
            'title' => $title,
            'title_jp' => $title_jp
        ));
    }

    public function fetchJurnal(){
        $employee_id = Auth::user()->username;
        $employee = EmployeeSync::where('employee_id', '=', $employee_id)->first();

        $where = "";

        if($employee->department != 'Management Information System Department' && $employee->department != 'Human Resources Department' && Auth::user()->role_code != 'S'){
            $where = "WHERE a.department = '".$employee->department."'";
        }

        $jurnal = db::select("SELECT supplier_code,supplier_name,acc_purchase_order_details.no_po , nama_item, delivery_date FROM `acc_purchase_order_details` JOIN acc_purchase_orders on acc_purchase_orders.no_po = acc_purchase_order_details.no_po");

        $response = array(
            'status' => true,
            'jurnal' => $jurnal
        );
        return Response::json($response);
    }

    public function delivery_control(){
        $title = "Delivery Control";
        $title_jp = "";

        return view('accounting_purchasing.report.delivery_control', array(
            'title' => $title,
            'title_jp' => $title_jp
        ));
    }

    //==================================//
    //          Purchase Order          //
    //==================================//
    public function purchase_order()
    {
        $title = 'Purchase Order';
        $title_jp = '発注依頼';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

        $authorized2 = EmployeeSync::select('employee_id', 'name')->where('position', '=', 'Manager')
        ->where('department', 'like', '%Procurement%')
        ->first();

        $authorized3 = EmployeeSync::select('employee_id', 'name')
        ->where('position', '=', 'General Manager')
        ->where('division', '=', 'Production Support Division')
        ->first();

        // $authorized4 = EmployeeSync::select('employee_id', 'name')->where('position', '=', 'Director')
        // ->Orwhere('position', '=', 'General Manager')
        // ->get();

        return view('accounting_purchasing.purchase_order', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'vendor' => $vendor,
            'delivery' => $this->delivery,
            'transportation' => $this->transportation,
            'authorized2' => $authorized2,
            'authorized3' => $authorized3,
            // 'authorized4' => $authorized4,
            'uom' => $this->uom
        ))
        ->with('page', 'Purchase Order')
        ->with('head', 'Purchase Order');
    }

    public function delete_purchase_requisition(Request $request)
    {
        try
        {
            $pr = AccPurchaseRequisition::find($request->get('id'));

            $budget_log = AccBudgetHistory::where('category_number', '=', $pr->no_pr)
            ->get();

            // $date = date('Y-m-d');
            // //FY
            // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$date'");
            // foreach ($fy as $fys) {
            //     $fiscal = $fys->fiscal_year;
            // }

            foreach ($budget_log as $log) {
                $sisa_bulan = $log->budget_month.'_sisa_budget';
                $budget = AccBudget::where('budget_no', $log->budget)->first();

                $total = $budget->$sisa_bulan + $log->amount; //add total
                $dataupdate = AccBudget::where('budget_no', $log->budget)->update([
                    $sisa_bulan => $total
                ]);
            }

            $delete_budget_log = AccBudgetHistory::where('category_number', '=', $pr->no_pr)->delete();
            $delete_pr_item = AccPurchaseRequisitionItem::where('no_pr', '=', $pr->no_pr)->delete();
            $delete_pr = AccPurchaseRequisition::where('no_pr', '=', $pr->no_pr)->delete();

            $response = array(
                'status' => true,
            );

            return Response::json($response);
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_requisition')->with('error', $e->getMessage())
            ->with('page', 'Purchase Requisition');
        }
    }

    public function delete_item_pr(Request $request)
    {
        try
        {
            $master_item = AccPurchaseRequisitionItem::find($request->get('id'));

            $budget_log = AccBudgetHistory::where('no_item', '=', $master_item->item_desc)
            ->where('category_number', '=', $master_item->no_pr)
            ->first();

            // $date = date('Y-m-d');
            // //FY
            // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$date'");
            // foreach ($fy as $fys) {
            //     $fiscal = $fys->fiscal_year;
            // }

            $sisa_bulan = $budget_log->budget_month.'_sisa_budget';

            $budget = AccBudget::where('budget_no', $budget_log->budget)->first();

            $total = $budget->$sisa_bulan + $budget_log->amount; //add total

            $dataupdate = AccBudget::where('budget_no', $budget_log->budget)->update([
                $sisa_bulan => $total
            ]);

            $delete_budget_log = AccBudgetHistory::where('no_item', '=', $master_item->item_desc)
            ->where('category_number', '=', $master_item->no_pr)
            ->delete();

            $delete_item = AccPurchaseRequisitionItem::where('id', '=', $request->get('id'))->delete();

            $response = array(
                'status' => true,
            );

            return Response::json($response);

        }
        catch(QueryException $e)
        {
            return redirect('/purchase_requisition')->with('error', $e->getMessage())
            ->with('page', 'Purchase Requisition');
        }

    }

    public function fetch_purchase_order(Request $request)
    {
        $tanggal = "";
        $adddepartment = "";

        if (strlen($request->get('datefrom')) > 0)
        {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and A.tgl_po >= '" . $datefrom . " 00:00:00' ";
            if (strlen($request->get('dateto')) > 0)
            {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and A.tgl_po  <= '" . $dateto . " 23:59:59' ";
            }
        }

        $qry = "SELECT * FROM acc_purchase_orders A WHERE A.deleted_at IS NULL " . $tanggal . " order by A.id DESC";
        $po = DB::select($qry);

        return DataTables::of($po)

        ->editColumn('tgl_po', function ($po)
        {
            return date('Y-m-d', strtotime($po->tgl_po));
        })

        ->editColumn('no_po_sap', function ($po)
        {
            $id = $po->id;

            $po_sap = "";
            if ($po->no_po_sap == null && $po->status == "not_sap")
            {
                $po_sap = '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-md" onClick="editSAP('.$id.','.$po->no_po_sap.')"><i class="fa fa-edit"></i> NO PO SAP</a>';
            }
            else if ($po->no_po_sap != null){
                $po_sap = $po->no_po_sap;
                $po_sap .= '&nbsp;<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-md" onClick="editSAP('.$id.','.$po->no_po_sap.')"><i class="fa fa-edit"></i></a>';
            }
            else
            {
                $po_sap = '-';
            }

            return $po_sap;
        })

        ->editColumn('status', function ($po)
        {
            $id = $po->id;

            if ($po->posisi == "staff_pch")
            {
                return '<label class="label label-danger">Staff Purchasing</label>';
            }

            else if ($po->posisi == "manager_pch")
            {
                return '<label class="label label-warning">Diverifikasi Manager</label>';
            }

            else if ($po->posisi == "dgm_pch")
            {
                return '<label class="label label-primary">Diverifikasi GM Support</label>';
            }

            else if ($po->posisi == "gm_pch")
            {
                return '<label class="label label-primary">Diverifikasi GM</label>';
            }

            else if ($po->posisi == "pch")
            {
                return '<label class="label label-success">Sudah Diverifikasi</label>';
            }

        })
        ->addColumn('action', function ($po)
        {
            $id = $po->id;
            if ($po->posisi == "staff_pch") {
                return '
                <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i> Edit</a>
                <a href="purchase_order/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
                <button class="btn btn-xs btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;"  onclick="sendEmail(' . $id .')"><i class="fa fa-envelope"></i> Send Email</button>
                <a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="cancelPO('.$id.')" data-toggle="modal" data-target="#modalcancelPO"  title="Delete PO"><i class="fa fa-trash"></i> Delete PO</a>
                ';
            }

            else if ($po->posisi == "pch") {
                return '
                <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i> Edit</a>
                <a href="purchase_order/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"> Report</i></a>
                <a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="cancelPO('.$id.')" data-toggle="modal" data-target="#modalcancelPO"  title="Cancel PO"><i class="fa fa-close"></i> Cancel PO</a>
                ';
            }

            else{
                return '
                <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i> Edit</a>
                <a href="purchase_order/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
                <label class="label label-success">Email Sudah Dikirim</label>
                ';   
            }
        })
        ->rawColumns(['status' => 'status', 'action' => 'action', 'no_po_sap' => 'no_po_sap'])
        ->make(true);
    }

    public function fetch_po_outstanding_pr(Request $request)
    {
        $qry = "SELECT distinct acc_purchase_requisitions.id, acc_purchase_requisitions.no_pr,department,submission_date,emp_id,emp_name,no_budget,file,posisi,`status`,file_pdf FROM `acc_purchase_requisitions` join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where receive_date is not null and acc_purchase_requisition_items.sudah_po is null and acc_purchase_requisitions.deleted_at is null";
        $pr = DB::select($qry);

        return DataTables::of($pr)

        ->editColumn('submission_date', function ($pr)
        {
            return $pr->submission_date;
        })

        ->editColumn('file', function ($pr)
        {
            $data = json_decode($pr->file);

            $fl = "";

            if ($pr->file != null)
            {
                for ($i = 0;$i < count($data);$i++)
                {
                    $fl .= '<a href="files/pr/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
                }
            }
            else
            {
                $fl = '-';
            }

            return $fl;
        })

        ->addColumn('action', function ($pr)
        {
            $id = $pr->id;

            return '
            <a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="editPR(' . $id . ')" data-toggle="tooltip" title="Edit PR"><i class="fa fa-edit"></i> Edit</a>
            <a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
            <a href="javascript:void(0)" class="btn btn-xs  btn-primary" onClick="detailPR(' . $id . ')" style="margin-right:5px;" data-toggle="tooltip" title="Detail PR"><i class="fa fa-eye"></i> Detail Item</a>
            ';
        })

        ->rawColumns(['file' => 'file', 'action' => 'action'])
        ->make(true);
    }

    public function get_nomor_po(Request $request)
    {
        $datenow = date('Y-m-d');
        $tahun = date('y');
        $bulan = date('m');

        $result['tahun'] = $tahun;
        $result['bulan'] = $bulan;

        return json_encode($result);
    }

    public function pogetsupplier(Request $request)
    {
        $html = array();
        $vendor_code = AccSupplier::where('vendor_code', $request->supplier_code)
        ->get();
        foreach ($vendor_code as $supp)
        {
            $html = array(
                'name' => $supp->supplier_name,
                'duration' => $supp->supplier_duration,
                'status' => $supp->supplier_status,
            );
        }
        return json_encode($html);
    }

    public function get_budget_name(Request $request)
    {
        $html = array();
        
        $tahun = date('Y');
        $namabulan = date('F');

        $getbulan = AccBudget::select('budget_no', 'periode')
        ->where('budget_no',$request->budget)
        ->first();

        if ($getbulan->periode == "FY198") {
            $bulan = strtolower(date('M'));
        }
        else{
            $bulan = "apr";
        }

        // $tglnow = date('Y-m-d');
        // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

        // foreach ($fy as $fys) {
        //     $fiscal = $fys->fiscal_year;
        // }



        $budget_no = AccBudget::SELECT('*',$bulan.'_sisa_budget as budget_now')->where('budget_no', $request->budget)->get();

        foreach ($budget_no as $budget)
        {
            $html = array(
                'budget_desc' => $budget->description,
                'budget_now' => $budget->budget_now
            );

        }
        return json_encode($html);
    }

    public function pogetname(Request $request)
    {
        $html = array();
        $emp = EmployeeSync::where('employee_id', $request->authorized4)
        ->get();
        foreach ($emp as $name)
        {
            $html = array(
                'name' => $name->name,
            );
        }
        return json_encode($html);
    }

    public function fetchPrList(Request $request)
    {
        $pr = AccPurchaseRequisition::select('acc_purchase_requisitions.no_pr')->join('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
        ->whereNull('sudah_po')
        ->whereNotNull('receive_date')
        ->distinct()
        ->get();

        $response = array(
            'status' => true,
            'pr' => $pr
        );

        return Response::json($response);
    }

    public function pilihPR(Request $request)
    {
        $html = array();
        $list_item = AccPurchaseRequisitionItem::where('no_pr', $request->no_pr)
        ->whereNull('sudah_po')
        ->get();

        $lists = "<option value=''>-- Pilih Item --</option>";
        foreach ($list_item as $item)
        {
            $lists .= "<option value='" . $item->item_code . "'>" . $item->item_desc . "</option>"; 
            
        }
        return json_encode($lists);
    }

    public function pogetitem(Request $request)
    {
        $html = array();
        $kode_item = AccPurchaseRequisitionItem::join('acc_purchase_requisitions', 'acc_purchase_requisition_items.no_pr', '=', 'acc_purchase_requisitions.no_pr')
        ->where('item_code', $request->item_code)
        ->where('acc_purchase_requisition_items.no_pr', $request->no_pr)
        ->get();

        $last_price = AccPurchaseOrderDetail::select('goods_price')
        ->where('no_item', $request->item_code)
        ->orderBy('id','desc')
        ->get();

        if(count($last_price) > 0){
            $last = $last_price[0]->goods_price;
        }else{
            $last = 0;
        }

        foreach ($kode_item as $item)
        {
            $html = array(
                'item_code' => $item->item_code,
                'item_desc' => $item->item_desc,
                'item_spec' => $item->item_spec,
                'item_request_date' => $item->item_request_date,
                'item_qty' => $item->item_qty,
                'item_uom' => $item->item_uom,
                'item_currency' => $item->item_currency,
                'item_price' => $item->item_price,
                'last_price' => $last,
                'no_budget' => $item->no_budget,
            );

        }

        return json_encode($html);
    }

    public function create_purchase_order(Request $request)
    {
        $id = Auth::id();

        $lop = $request->get('lop');
        $nopo = $request->get('no_po1') . $request->get('no_po2');

        $total_dollar = 0;

        try
        {
            $last = substr($nopo, -2);

            $query = "SELECT * FROM `cost_centers2` where code = '$last'";
            $cost_center = DB::select($query);

            foreach ($cost_center as $cc) {
                $cost = $cc->cost_center;
            }

            $data = new AccPurchaseOrder([
                'remark' => $request->get('remark'), 
                'no_po' => $nopo, 
                'tgl_po' => $request->get('tgl_po') , 
                'supplier_code' => $request->get('supplier_code') , 
                'supplier_name' => $request->get('supplier_name') , 
                'supplier_due_payment' => $request->get('supplier_due_payment') , 
                'supplier_status' => $request->get('supplier_status') , 
                'material' => $request->get('material') , 
                'vat' => $request->get('price_vat') , 
                'transportation' => $request->get('transportation') , 
                'delivery_term' => $request->get('delivery_term') , 
                'holding_tax' => $request->get('holding_tax') , 
                'currency' => $request->get('currency') , 
                'buyer_id' => $request->get('buyer_id') , 
                'buyer_name' => $request->get('buyer_name') , 
                'authorized2' => $request->get('authorized2') , 
                'authorized2_name' => $request->get('authorized2_name') , 
                'authorized3' => $request->get('authorized3') , 
                'authorized3_name' => $request->get('authorized3_name') , 
                // 'authorized4' => $request->get('authorized4') , 
                // 'authorized4_name' => $request->get('authorized4_name'), 
                'file_pdf' => $nopo.'.pdf' , 
                'note' => $request->get('note') , 
                'cost_center' => $cost , 
                'posisi' => 'staff_pch', 
                'status' => 'pch', 
                'created_by' => $id
            ]);

            $data->save();

            for ($i = 1;$i <= $lop;$i++)
            {
                if($request->get('remark') == "PR"){
                    $no_pr = "no_pr" . $i;
                }
                else if($request->get('remark') == "Investment"){
                    $no_pr = "reff_number" . $i;
                }
                $no_item = "no_item" . $i;
                $nama_item = "nama_item" . $i;
                $item_budget = "item_budget" . $i;
                $delivery_date = "delivery_date" . $i;
                $qty = "qty" . $i;
                $uom = "uom" . $i;
                $goods_price = "goods_price" . $i;
                $last_price = "last_price" . $i;
                $service_price = "service_price" . $i;
                $konversi_dollar = "konversi_dollar" . $i;
                $gl_number = "gl_number" . $i;

                $data2 = new AccPurchaseOrderDetail([
                    'no_po' => $nopo, 
                    'no_pr' => $request->get($no_pr) , 
                    'no_item' => $request->get($no_item) , 
                    'nama_item' => $request->get($nama_item) , 
                    'budget_item' => $request->get($item_budget) , 
                    'delivery_date' => $request->get($delivery_date) , 
                    'qty' => $request->get($qty) , 
                    'qty_receive' => 0, 
                    'uom' => $request->get($uom) , 
                    'goods_price' => $request->get($goods_price) , 
                    'last_price' => $request->get($last_price) , 
                    'service_price' => $request->get($service_price) , 
                    'konversi_dollar' => $request->get($konversi_dollar) , 
                    'gl_number' => $request->get($gl_number) ,
                    'created_by' => $id
                ]);

                $data2->save();

                //Update Status Sudah PO

                if($request->get('remark') == "PR"){
                    $data3 = AccPurchaseRequisitionItem::where('item_code', $request->get($no_item))
                    ->where('no_pr', $request->get($no_pr))
                    ->update(['sudah_po' => 'true', ]);
                }
                else if($request->get('remark') == "Investment"){
                    $data3 = AccInvestmentDetail::where('no_item', $request->get($no_item))
                    ->where('reff_number', $request->get($no_pr))
                    ->update(['sudah_po' => 'true', ]);
                }

                //Update Harga + Currency Di Master Item
                if ($request->get($goods_price) != 0) {
                    $data4 = AccItem::where('kode_item', $request->get($no_item))
                    ->update([
                        'harga' => $request->get($goods_price),
                        'currency' => $request->get('currency')
                    ]);
                }
                else if ($request->get($service_price) != 0) {
                    $data4 = AccItem::where('kode_item', $request->get($no_item))
                    ->update([
                        'harga' => $request->get($service_price), 
                        'currency' => $request->get('currency')
                    ]);
                }

                //Get Total Amount From Budget Log

                $data5 = AccBudgetHistory::where('budget', $request->get($item_budget))
                ->where('category_number',$request->get($no_pr))
                ->where('no_item',$request->get($nama_item))
                ->first();

                $amount = $data5->amount;


                // $datenow = date('Y-m-d');

                // //Get Data From Budget Master

                // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$datenow'");

                // foreach ($fy as $fys) {
                //     $fiscal = $fys->fiscal_year;
                // }

                $getbulan = AccBudget::select('budget_no', 'periode')
                ->where('budget_no', $request->get($item_budget))
                ->first();

                if ($getbulan->periode == "FY198") {
                    $bulan = strtolower(date('M'));
                }
                else{
                    $bulan = "apr";
                }
                
                // $bulan = strtolower(date("M",strtotime($datenow)));

                $sisa_bulan = $bulan.'_sisa_budget';

                //get Data Budget Based On Periode Dan Nomor
                $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget))->first();

                //Tambahkan Budget Dengan Yang Ada Di Log
                $totalPlusPR = $budgetdata->$sisa_bulan + $amount;

                $updatebudget = AccBudget::where('budget_no',$request->get($item_budget))
                ->update([
                    $sisa_bulan => $totalPlusPR
                ]);

                //get Data Budget Based On Periode Dan Nomor
                $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget))->first();

                //Get Amount Di PO
                $total_dollar = $request->get($konversi_dollar);

                $totalminusPO = $budgetdata->$sisa_bulan - $total_dollar;

                if ($totalminusPO < 0) {

                    //Tambahkan Budget Dengan Yang Ada Di Log
                    $totalMinPR = $budgetdata->$sisa_bulan - $amount;

                    $updatebudget = AccBudget::where('budget_no',$request->get($item_budget))
                    ->update([
                        $sisa_bulan => $totalMinPR
                    ]);

                    return redirect('/purchase_order')->with('error', 'Total Melebihi Budget')
                    ->with('page', 'Purchase Order');
                } 

                // Setelah itu update data budgetnya dengan yang actual
                $dataupdate = AccBudget::where('budget_no',$request->get($item_budget))
                ->update([
                    $sisa_bulan => $totalminusPO
                ]);

                $updatebudgetlog = AccBudgetHistory::where('budget', $request->get($item_budget))
                ->where('category_number',$request->get($no_pr))
                ->where('no_item',$request->get($nama_item))
                ->update([
                    'budget_month_po' => $bulan,
                    'po_number' => $nopo,
                    'amount_po' => $total_dollar,
                    'status' => 'PO'
                ]);
            }

            if($request->get('remark') == "Investment"){
                $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_investments.applicant_department')
                ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                ->leftJoin('acc_investments', 'acc_purchase_order_details.no_pr', '=', 'acc_investments.reff_number')
                ->where('acc_purchase_orders.id', '=', $data->id)
                ->get();
            }

            else if($request->get('remark') == "PR"){
                $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_purchase_requisitions.department')
                ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                ->leftJoin('acc_purchase_requisitions', 'acc_purchase_order_details.no_pr', '=', 'acc_purchase_requisitions.no_pr')
                ->where('acc_purchase_orders.id', '=', $data->id)
                ->get();
            }

            $pr = AccPurchaseOrder::select('no_pr')
            ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
            ->where('acc_purchase_orders.id', '=', $data->id)
            ->distinct()
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('accounting_purchasing.report.report_po', array(
                'po' => $detail_po,
                'pr' => $pr
            ));

            $pdf->save(public_path() . "/po_list/".$detail_po[0]->no_po.".pdf");


            if($request->get('remark') == "Investment"){
                return redirect('/purchase_order_investment')
                ->with('status', 'Purchase Order Investment Berhasil Dibuat')
                ->with('page', 'Purchase Order Investment');
            }

            else if($request->get('remark') == "PR"){
                return redirect('/purchase_order')
                ->with('status', 'Purchase Order PR Berhasil Dibuat')
                ->with('page', 'Purchase Order');
            }
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_order')->with('error', $e->getMessage())
            ->with('page', 'Purchase Order');
        }
    }

    public function edit_purchase_order(Request $request)
    {

        $purchase_order = AccPurchaseOrder::find($request->get('id'));
        $purchase_order_detail = AccPurchaseOrder::join('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')->where('acc_purchase_orders.id', '=', $request->get('id'))
        ->get();

        $response = array(
            'status' => true,
            'purchase_order' => $purchase_order,
            'purchase_order_detail' => $purchase_order_detail
        );
        return Response::json($response);
    }

    public function update_purchase_order(Request $request)
    {
        $id = Auth::id();

        $lop2 = $request->get('lop2');
        $po_data = AccPurchaseOrder::find($request->get('id_edit'));
        $lop = explode(',', $request->get('looping'));

        try
        {
            //Update PO
            $data3 = AccPurchaseOrder::where('no_po', $request->get('no_po_edit'))
            ->update([
                'supplier_code' => $request->get('supplier_code_edit') , 
                'supplier_name' => $request->get('supplier_name_edit') , 
                'supplier_due_payment' => $request->get('supplier_due_payment_edit') , 
                'supplier_status' => $request->get('supplier_status_edit') , 
                'material' => $request->get('material_edit') , 
                'vat' => $request->get('price_vat_edit') , 
                'transportation' => $request->get('transportation_edit') , 
                'delivery_term' => $request->get('delivery_term_edit') , 
                'holding_tax' => $request->get('holding_tax_edit') , 
                'currency' => $request->get('currency_edit') , 
                // 'authorized4' => $request->get('authorized4_edit') , 
                // 'authorized4_name' => $request->get('authorized4_name_edit') , 
                'note' => $request->get('note_edit') 
            ]);

            if ($po_data->posisi == "pch") {
                $data5 = AccPurchaseOrder::where('no_po', $request->get('no_po_edit'))
                ->update([
                    'revised' => 'true', 
                    'revised_date' => date('Y-m-d'),
                    'posisi' => 'staff_pch',
                    'status' => 'pch',
                    'approval_authorized2' => null,
                    'date_approval_authorized2' => null,
                    'approval_authorized3' => null,
                    'date_approval_authorized3' => null
                ]);
            }



            foreach ($lop as $lp)
            {
                $no_pr = "no_pr" . $lp;
                $no_item = "no_item" . $lp;
                $nama_item = "nama_item" . $lp;
                $item_budget = "item_budget" . $lp;
                $delivery_date = "delivery_date" . $lp;
                $qty = "qty" . $lp;
                $uom = "uom_edit" . $lp;
                $goods_price = "goods_price" . $lp;
                $last_price = "last_price" . $lp;
                $service_price = "service_price" . $lp;
                $konversi_dollar = "konversi_dollar" . $lp;
                $gl_number = "gl_edit" . $lp;

                $data2 = AccPurchaseOrderDetail::where('id', $lp)->update([
                    'no_item' => $request->get($no_item) , 
                    'nama_item' => $request->get($nama_item) , 
                    'budget_item' => $request->get($item_budget) , 
                    'delivery_date' => $request->get($delivery_date) , 
                    'qty' => $request->get($qty) , 
                    'uom' => $request->get($uom) , 
                    'goods_price' => $request->get($goods_price) , 
                    'last_price' => $request->get($last_price) , 
                    'service_price' => $request->get($service_price) , 
                    'konversi_dollar' => $request->get($konversi_dollar) , 
                    'gl_number' => $request->get($gl_number) , 
                    'created_by' => $id
                ]);

                 //Update Harga + Currency Di Master Item
                if ($request->get($goods_price) != 0) {
                    $updateitempo = AccItem::where('kode_item', $request->get($no_item))
                    ->update([
                        'harga' => $request->get($goods_price),
                        'currency' => $request->get('currency_edit')
                    ]);
                }
                else if ($request->get($service_price) != 0) {
                    $updateitempo = AccItem::where('kode_item', $request->get($no_item))
                    ->update([
                        'harga' => $request->get($service_price), 
                        'currency' => $request->get('currency_edit')
                    ]);
                }

                if ($data2) {

                    $getbudgetlog = AccBudgetHistory::where('budget', $request->get($item_budget))
                    ->where('po_number',$request->get('no_po_edit'))
                    ->where('no_item',$request->get($nama_item))
                    ->first();

                    $counter = $getbudgetlog->amount_po;
                    $date = $getbudgetlog->budget_month_po;

                    // $datenow = date('Y-m-d');

                    // //Get Data From Budget Master

                    // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$datenow'");

                    // foreach ($fy as $fys) {
                    //     $fiscal = $fys->fiscal_year;
                    // }

                    $sisa_bulan = $date.'_sisa_budget';

                    //get Data Budget Based On Periode Dan Nomor
                    $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget))->first();

                    $totalOld = $budgetdata->$sisa_bulan + $counter;

                    $updatebudget = AccBudget::where('budget_no',$request->get($item_budget))
                    ->update([
                        $sisa_bulan => $totalOld
                    ]);

                    //get Data Budget Based On Periode Dan Nomor
                    $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget))->first();

                    $total_dollar = $request->get($konversi_dollar);

                    $totalNew = $budgetdata->$sisa_bulan - $total_dollar;

                    $dataupdate = AccBudget::where('budget_no',$request->get($item_budget))
                    ->update([
                        $sisa_bulan => $totalNew
                    ]);

                    $data5 = AccBudgetHistory::where('budget', $request->get($item_budget))
                    ->where('po_number',$request->get('no_po_edit'))
                    ->where('no_item',$request->get($nama_item))
                    ->update([
                        'amount_po' => $request->get($konversi_dollar),
                        'created_by' => $id
                    ]);
                }

            }

            for ($i = 2;$i <= $lop2;$i++)
            {
                if ($po_data->remark == "PR"){
                    $no_pr2 = "no_pr" . $i;
                }
                else if ($po_data->remark == "Investment"){
                    $no_pr2 = "reff_number" . $i;
                }
                $no_item2 = "no_item" . $i;
                $nama_item2 = "nama_item" . $i;
                $item_budget2 = "item_budget" . $i;
                $delivery_date2 = "delivery_date" . $i;
                $qty2 = "qty" . $i;
                $uom2 = "uom" . $i;
                $goods_price2 = "goods_price" . $i;
                $last_price2 = "last_price" . $i;
                $service_price2 = "service_price" . $i;
                $konversi_dollar2 = "konversi_dollar" . $i;
                $gl_number2 = "gl_number" . $i;

                $data = new AccPurchaseOrderDetail([
                    'no_po' => $request->get('no_po_edit') , 
                    'no_pr' => $request->get($no_pr2) , 
                    'no_item' => $request->get($no_item2) , 
                    'nama_item' => $request->get($nama_item2) , 
                    'budget_item' => $request->get($item_budget2) , 
                    'delivery_date' => $request->get($delivery_date2) , 
                    'qty' => $request->get($qty2) , 
                    'qty_receive' => 0 , 
                    'uom' => $request->get($uom2) , 
                    'goods_price' => $request->get($goods_price2) , 
                    'last_price' => $request->get($last_price2) , 
                    'service_price' => $request->get($service_price2) , 
                    'konversi_dollar' => $request->get($konversi_dollar2) , 
                    'gl_number' => $request->get($gl_number2) , 
                    'created_by' => $id
                ]);

                $data->save();

                if ($po_data->remark == "PR") {
                    $data3 = AccPurchaseRequisitionItem::where('item_code', $request->get($no_item2))
                    ->where('no_pr', $request->get($no_pr2))
                    ->update([
                        'sudah_po' => 'true'
                    ]);

                }else if ($po_data->remark == "Investment"){
                    $data3 = AccInvestmentDetail::where('no_item', $request->get($no_item2))
                    ->where('reff_number', $request->get($no_pr2))
                    ->update([
                        'sudah_po' => 'true'
                    ]);
                }

                

                 //Get Total Amount From Budget Log

                $data5 = AccBudgetHistory::where('budget', $request->get($item_budget2))
                ->where('category_number',$request->get($no_pr2))
                ->where('no_item',$request->get($nama_item2))
                ->first();

                $amount = $data5->amount;
                // $datenow = date('Y-m-d');

                // //Get Data From Budget Master

                // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$datenow'");

                // foreach ($fy as $fys) {
                //     $fiscal = $fys->fiscal_year;
                // }
                
                // $bulan = strtolower(date("M",strtotime($datenow)));

                $getbulan = AccBudget::select('budget_no', 'periode')
                ->where('budget_no', $request->get($item_budget2))
                ->first();

                if ($getbulan->periode == "FY198") {
                    $bulan = strtolower(date('M'));
                }
                else{
                    $bulan = "apr";
                }

                $sisa_bulan = $bulan.'_sisa_budget';

                //get Data Budget Based On Periode Dan Nomor
                $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget2))->first();

                //Tambahkan Budget Dengan Yang Ada Di Log
                $totalPlusPR = $budgetdata->$sisa_bulan + $amount;

                $updatebudget = AccBudget::where('budget_no',$request->get($item_budget2))
                ->update([
                    $sisa_bulan => $totalPlusPR
                ]);

                //get Data Budget Based On Periode Dan Nomor
                $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget2))->first();

                //Get Amount Di PO
                $total_dollar = $request->get($konversi_dollar2);

                $totalminusPO = $budgetdata->$sisa_bulan - $total_dollar;

                // Setelah itu update data budgetnya dengan yang actual
                $dataupdate = AccBudget::where('budget_no',$request->get($item_budget2))
                ->update([
                    $sisa_bulan => $totalminusPO
                ]);

                $updatebudgetlog = AccBudgetHistory::where('budget', $request->get($item_budget2))
                ->where('category_number',$request->get($no_pr2))
                ->where('no_item',$request->get($nama_item2))
                ->update([
                    'budget_month_po' => $bulan,
                    'po_number' => $request->get('no_po_edit'),
                    'amount_po' => $total_dollar,
                    'status' => 'PO'
                ]);
                
            }

            $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*')
            ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
            ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
            ->leftJoin('acc_purchase_requisitions', 'acc_purchase_order_details.no_pr', '=', 'acc_purchase_requisitions.no_pr')
            ->where('acc_purchase_orders.no_po', '=', $request->get('no_po_edit'))
            ->get();

            $pr = AccPurchaseOrder::select('no_pr')
            ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
            ->where('acc_purchase_orders.no_po', '=', $request->get('no_po_edit'))
            ->distinct()
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('accounting_purchasing.report.report_po', array(
                'po' => $detail_po,
                'pr' => $pr
            ));

            $pdf->save(public_path() . "/po_list/".$detail_po[0]->no_po.".pdf");


            if ($po_data->remark == "PR"){
                return redirect('/purchase_order')
                ->with('status', 'Purchase Order Berhasil Dirubah')
                ->with('page', 'Purchase Order');
                
            } else if ($po_data->remark == "Investment"){
                return redirect('/purchase_order_investment')
                ->with('status', 'Purchase Order Investment Berhasil Dirubah')
                ->with('page', 'Purchase Order Investment');
            }
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_order')->with('error', $e->getMessage())
            ->with('page', 'Purchase Order');
        }
    }

    public function edit_sap(Request $request)
    {
        try{
            $po = AccPurchaseOrder::find($request->get("id"));
            $po->no_po_sap = $request->get('no_po_sap');
            $po->status = 'sap';
            $po->save();

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
              'datas' => "NO PO Already Exist",
          );
             return Response::json($response);
         }
         else{
             $response = array(
              'status' => false,
              'datas' => "Update NO PO Error.",
          );
             return Response::json($response);
         }
     }
 }

 public function delete_item_po(Request $request)
 {
    try
    {
        $item = AccPurchaseOrderDetail::find($request->get('id'));
        
        if (strlen($item->no_pr) == 9) {
            $data3 = AccPurchaseRequisitionItem::where('item_code', $item->no_item)
            ->where('no_pr', $item->no_pr)
            ->update(['sudah_po' => null ]);
        }
        else{
            $data3 = AccInvestmentDetail::where('no_item', $item->no_item)
            ->where('reff_number', $item->no_pr)
            ->update(['sudah_po' => null]);
        }

        

        $budget_log = AccBudgetHistory::where('no_item', '=', $item->nama_item)
        ->where('po_number', '=', $item->no_po)
        ->first();

        // $date = date('Y-m-d');

        //     //FY
        // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$date'");
        // foreach ($fy as $fys) {
        //     $fiscal = $fys->fiscal_year;
        // }


        $sisa_bulan = $budget_log->budget_month_po.'_sisa_budget';        
        $budget = AccBudget::where('budget_no', $budget_log->budget)->first();

            $total = $budget->$sisa_bulan + $budget_log->amount_po; //add total

            $dataupdate = AccBudget::where('budget_no', $budget_log->budget)
            ->update([
                $sisa_bulan => $total
            ]);

            $update_budget_log = AccBudgetHistory::where('no_item', '=', $item->nama_item)
            ->where('po_number', '=', $item->no_po)
            ->update([
                'budget_month_po' => null,
                'po_number' => null,
                'amount_po' => null,
                'status' => 'PR'
            ]);

            $budget = AccBudget::where('budget_no', $budget_log->budget)->first();

            $totalAfterMinusPR = $budget->$sisa_bulan - $budget_log->amount;

            $dataupdate = AccBudget::where('budget_no', $budget_log->budget)
            ->update([
                $sisa_bulan => $totalAfterMinusPR
            ]);

            $master = AccPurchaseOrderDetail::where('id', '=', $request->get('id'))->delete();

        }
        catch(QueryException $e)
        {
            return redirect('/purchase_order')->with('error', $e->getMessage())
            ->with('page', 'Purchase Order');
        }
    }

    public function po_send_email(Request $request){
        $po = AccPurchaseOrder::find($request->get('id'));

        try{
            if ($po->posisi == "staff_pch")
            {
                $po->posisi = "manager_pch";

                $mailto = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.authorized2 = users.username where acc_purchase_orders.id = '" . $request->get('id') . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }
                $po->save();

                    // $isimail = AccPurchaseOrder::select('acc_purchase_orders.*', 'acc_budget_histories.budget', DB::raw("SUM(acc_budget_histories.amount_po) as amount"))
                    // ->join('acc_budget_histories','acc_purchase_orders.no_po','=','acc_budget_histories.po_number')
                    // ->where('acc_purchase_orders.id', '=', $request->get('id'))
                    // ->get();

                // $isimail = "
                // select t1.*,  IF(t1.goods_price != 0,sum(t1.goods_price*t1.qty),sum(t1.service_price*t1.qty)) as amount from 
                // (SELECT
                // acc_purchase_orders.*,
                // acc_purchase_order_details.budget_item,
                // acc_purchase_order_details.goods_price,
                // acc_purchase_order_details.service_price,
                // acc_purchase_order_details.qty
                // FROM
                // acc_purchase_orders
                // JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
                // WHERE
                // acc_purchase_orders.id = ".$request->get('id').")
                // t1";


                $isimail = "
                SELECT acc_purchase_orders.*,
                acc_purchase_order_details.nama_item,
                acc_purchase_order_details.budget_item,
                acc_purchase_order_details.delivery_date,
                acc_purchase_order_details.goods_price,
                acc_purchase_order_details.service_price,
                acc_purchase_order_details.qty,
                acc_purchase_order_details.uom
                FROM
                acc_purchase_orders
                JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
                WHERE
                acc_purchase_orders.id = ".$request->get('id')."";

                $po_isi = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($po_isi, 'purchase_order'));

                $response = array(
                  'status' => true,
                  'datas' => "Berhasil"
              );

                return Response::json($response);
            }
            else{

            }


        } catch (Exception $e) {
         $response = array(
          'status' => false,
          'datas' => "Gagal"
      );

         return Response::json($response);
     }
 }

 public function cancel_purchase_order(Request $request)
 {
    try
    {
        $po = AccPurchaseOrder::find($request->get('id'));
        $date = date('Y-m-d');

        $budget_log = AccBudgetHistory::where('po_number', '=', $po->no_po)
        ->get();

        if ($budget_log != null) {

            foreach ($budget_log as $log) {
                $sisa_bulan = $log->budget_month_po.'_sisa_budget';
                $budget = AccBudget::where('budget_no', $log->budget)->first();

                    $total = $budget->$sisa_bulan + $log->amount_po; //add total PO
                    $dataupdate = AccBudget::where('budget_no', $log->budget)->update([
                        $sisa_bulan => $total
                    ]);

                    //get Data Budget Based On Periode Dan Nomor
                    $budgetdata = AccBudget::where('budget_no', $log->budget)->first();

                    $totalNew = $budgetdata->$sisa_bulan - $log->amount; //minus amount PR

                    $dataupdate = AccBudget::where('budget_no', $log->budget)
                    ->update([
                        $sisa_bulan => $totalNew
                    ]);
                }

                $data5 = AccBudgetHistory::where('po_number', $po->no_po)
                ->update([
                    'budget_month_po' => null,
                    'po_number' => null,
                    'amount_po' => null,
                    'status' => 'PR',
                    'created_by' => Auth::id()
                ]);
            }

            // $delete_history = AccBudgetHistory::where('po_number', '=', $po->no_po)->delete();



            if($po->remark == "PR"){

                $data3 = AccPurchaseOrderDetail::where('no_po', $po->no_po)
                ->select('*')
                ->get();
                
                foreach ($data3 as $datapr) {
                    $updatepr = AccPurchaseRequisitionItem::where('item_code', $datapr->no_item)
                    ->where('no_pr', $datapr->no_pr)
                    ->update(['sudah_po' => null ]);
                }
            }
            else if($po->remark == "Investment"){

                $data3 = AccPurchaseOrderDetail::where('no_po', $po->no_po)
                ->select('*')
                ->get();
                
                foreach ($data3 as $datainv) {
                    $data3 = AccInvestmentDetail::where('no_item', $datainv->no_item)
                    ->where('reff_number', $datainv->no_pr)
                    ->update(['sudah_po' => null]);
                }
            }

            $delete_po_item = AccPurchaseOrderDetail::where('no_po', '=', $po->no_po)->delete();
            $delete_po = AccPurchaseOrder::where('no_po', '=', $po->no_po)->delete();

            $response = array(
                'status' => true,
            );

            return Response::json($response);

        }
        catch(QueryException $e)
        {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );

            return Response::json($response);
        }

    }


    //==================================//
    //          Verifikasi PO           //
    //==================================//

    public function verifikasi_purchase_order($id)
    {
        $po = AccPurchaseOrder::find($id);

        $path = '/po_list/' . $po->file_pdf;            
        $file_path = asset($path);

        return view('accounting_purchasing.verifikasi.po_verifikasi', array(
            'po' => $po,
            'file_path' => $file_path,
        ))->with('page', 'Purchase Order');
    }


    public function approval_purchase_order(Request $request, $id)
    {
        $approve = $request->get('approve');

        $_IP_ADDRESS = $_SERVER['REMOTE_ADDR'];

        $_PERINTAH = "arp -a $_IP_ADDRESS";
        ob_start();
        system($_PERINTAH);
        $_HASIL = ob_get_contents();
        ob_clean();
        $_PECAH = strstr($_HASIL, $_IP_ADDRESS);
        
        if ($_PECAH == FALSE) {
            $_HASIL = $_IP_ADDRESS;
        }else{
            $_PECAH_STRING = explode($_IP_ADDRESS, str_replace(" ", "", $_PECAH));
            $_HASIL = substr($_PECAH_STRING[1], 0, 17);               
        }

        if ($approve == "1") {

            $po = AccPurchaseOrder::find($id);

            if ($po->posisi == "manager_pch")
            {
                $po->posisi = "dgm_pch";
                $po->approval_authorized2 = "Approved";
                $po->date_approval_authorized2 = date('Y-m-d H:i:s');
                $po->autentikasi_2 = $_HASIL;

                $mailto = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.authorized3 = users.username where acc_purchase_orders.id = '" . $id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }
            }
            else if ($po->posisi == "dgm_pch")
            {
                $po->posisi = 'pch';
                $po->approval_authorized3 = "Approved";
                $po->date_approval_authorized3 = date('Y-m-d H:i:s');
                $po->autentikasi_3 = $_HASIL;
                $po->status = "not_sap";

                    //kirim email Staff Purchasing sebagai pemberitahuan
                $mailto = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.buyer_id = users.username where acc_purchase_orders.id = '" . $id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }

                // $po->posisi = "gm_pch";
                // $po->approval_authorized3 = "Approved";
                // $po->date_approval_authorized3 = date('Y-m-d H:i:s');


                // $mailto = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.authorized4 = users.username where acc_purchase_orders.id = '" . $id . "'";
                // $mails = DB::select($mailto);

                // foreach ($mails as $mail)
                // {
                //     $mailtoo = $mail->email;
                // }
            }
            // else if ($po->posisi == "gm_pch")
            // {
            //     $po->posisi = 'pch';
            //     $po->approval_authorized4 = "Approved";
            //     $po->date_approval_authorized4 = date('Y-m-d H:i:s');
            //     // $po->autentikasi_4 = $_HASIL;
            //     $po->status = "not_sap";

            //         //kirim email Staff Purchasing sebagai pemberitahuan
            //     $mailto = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.buyer_id = users.username where acc_purchase_orders.id = '" . $id . "'";
            //     $mails = DB::select($mailto);

            //     foreach ($mails as $mail)
            //     {
            //         $mailtoo = $mail->email;
            //     }
            // }

            $po->save();

            if($po->remark == "Investment"){
                $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_investments.applicant_department')
                ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                ->leftJoin('acc_investments', 'acc_purchase_order_details.no_pr', '=', 'acc_investments.reff_number')
                ->where('acc_purchase_orders.id', '=', $id)
                ->get();
            }

            else if($po->remark == "PR"){
                $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_purchase_requisitions.department')
                ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                ->leftJoin('acc_purchase_requisitions', 'acc_purchase_order_details.no_pr', '=', 'acc_purchase_requisitions.no_pr')
                ->where('acc_purchase_orders.id', '=', $id)
                ->get();
            }

            $pr = AccPurchaseOrder::select('no_pr')
            ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
            ->where('acc_purchase_orders.id', '=', $id)
            ->distinct()
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('accounting_purchasing.report.report_po', array(
                'po' => $detail_po,
                'pr' => $pr
            ));

            $pdf->save(public_path() . "/po_list/".$detail_po[0]->no_po.".pdf");

            // $isimail = "select acc_purchase_orders.*, acc_budget_histories.budget, SUM(acc_budget_histories.amount_po) as amount FROM acc_purchase_orders join acc_budget_histories on acc_purchase_orders.no_po = acc_budget_histories.po_number where acc_purchase_orders.id = ".$id;

            // $isimail = "
            // select t1.*,  IF(t1.goods_price != 0,sum(t1.goods_price*t1.qty),sum(t1.service_price*t1.qty)) as amount from 
            // (SELECT
            // acc_purchase_orders.*,
            // acc_purchase_order_details.budget_item,
            // acc_purchase_order_details.goods_price,
            // acc_purchase_order_details.service_price,
            // acc_purchase_order_details.qty
            // FROM
            // acc_purchase_orders
            // JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
            // WHERE
            // acc_purchase_orders.id = ".$id.")
            // t1";

            $isimail = "
            SELECT acc_purchase_orders.*,
            acc_purchase_order_details.nama_item,
            acc_purchase_order_details.budget_item,
            acc_purchase_order_details.delivery_date,
            acc_purchase_order_details.goods_price,
            acc_purchase_order_details.service_price,
            acc_purchase_order_details.qty,
            acc_purchase_order_details.uom
            FROM
            acc_purchase_orders
            JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
            WHERE
            acc_purchase_orders.id = ".$id."";

            $po_isi = db::select($isimail);

            Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($po_isi, 'purchase_order'));

            return redirect('/purchase_order/verifikasi/' . $id)->with('status', 'Purchase Order Approved')
            ->with('page', 'Purchase Requisition');
        }
        else
        {
            return redirect('/purchase_order/verifikasi/' . $id)->with('error', 'Purchase Order Not Approved')
            ->with('page', 'Purchase Requisition');
        }
    }

    public function poapprovalmanager($id){
        $po = AccPurchaseOrder::find($id);
        try{
            if ($po->posisi == "manager_pch")
            {
                $po->posisi = "dgm_pch";
                $po->approval_authorized2 = "Approved";
                $po->date_approval_authorized2 = date('Y-m-d H:i:s');

                $mailto = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.authorized3 = users.username where acc_purchase_orders.id = '" . $id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }

                $po->save();

                if($po->remark == "Investment"){
                    $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_investments.applicant_department')
                    ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                    ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                    ->leftJoin('acc_investments', 'acc_purchase_order_details.no_pr', '=', 'acc_investments.reff_number')
                    ->where('acc_purchase_orders.id', '=', $id)
                    ->get();
                }

                else if($po->remark == "PR"){
                    $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_purchase_requisitions.department')
                    ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                    ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                    ->leftJoin('acc_purchase_requisitions', 'acc_purchase_order_details.no_pr', '=', 'acc_purchase_requisitions.no_pr')
                    ->where('acc_purchase_orders.id', '=', $id)
                    ->get();
                }
                
                $pr = AccPurchaseOrder::select('no_pr')
                ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                ->where('acc_purchase_orders.id', '=', $id)
                ->distinct()
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_po', array(
                    'po' => $detail_po,
                    'pr' => $pr
                ));

                $pdf->save(public_path() . "/po_list/".$detail_po[0]->no_po.".pdf");

                // $isimail = "select acc_purchase_orders.*, acc_budget_histories.budget, SUM(acc_budget_histories.amount_po) as amount FROM acc_purchase_orders join acc_budget_histories on acc_purchase_orders.no_po = acc_budget_histories.po_number where acc_purchase_orders.id = ".$id;

                // $isimail = "
                // select t1.*,  IF(t1.goods_price != 0,sum(t1.goods_price*t1.qty),sum(t1.service_price*t1.qty)) as amount from 
                // (SELECT
                // acc_purchase_orders.*,
                // acc_purchase_order_details.budget_item,
                // acc_purchase_order_details.goods_price,
                // acc_purchase_order_details.service_price,
                // acc_purchase_order_details.qty
                // FROM
                // acc_purchase_orders
                // JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
                // WHERE
                // acc_purchase_orders.id = ".$id.")
                // t1";

                $isimail = "
                SELECT acc_purchase_orders.*,
                acc_purchase_order_details.nama_item,
                acc_purchase_order_details.budget_item,
                acc_purchase_order_details.delivery_date,
                acc_purchase_order_details.goods_price,
                acc_purchase_order_details.service_price,
                acc_purchase_order_details.qty,
                acc_purchase_order_details.uom
                FROM
                acc_purchase_orders
                JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
                WHERE
                acc_purchase_orders.id = ".$id."";

                $po_isi = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($po_isi, 'purchase_order'));

                $message = 'PO dengan Nomor '.$po->no_po;
                $message2 ='Berhasil di approve';
            }
            else{
                $message = 'PO dengan Nomor. '.$po->no_po;
                $message2 ='Sudah di approve/reject';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $po->no_pr,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $po->no_pr,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    public function poapprovaldgm($id){
        $po = AccPurchaseOrder::find($id);
        try{
            if ($po->posisi == "dgm_pch")
            {
                $po->posisi = 'pch';
                $po->approval_authorized3 = "Approved";
                $po->date_approval_authorized3 = date('Y-m-d H:i:s');
                $po->status = "not_sap";

                    //kirim email Staff Purchasing sebagai pemberitahuan
                $mailto = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.buyer_id = users.username where acc_purchase_orders.id = '" . $id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }
                
                // $po->posisi = "gm_pch";
                // $po->approval_authorized3 = "Approved";
                // $po->date_approval_authorized3 = date('Y-m-d H:i:s');

                // $mailto = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.authorized4 = users.username where acc_purchase_orders.id = '" . $id . "'";
                // $mails = DB::select($mailto);

                // foreach ($mails as $mail)
                // {
                //     $mailtoo = $mail->email;
                // }

                $po->save();

                if($po->remark == "Investment"){
                    $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_investments.applicant_department')
                    ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                    ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                    ->leftJoin('acc_investments', 'acc_purchase_order_details.no_pr', '=', 'acc_investments.reff_number')
                    ->where('acc_purchase_orders.id', '=', $id)
                    ->get();
                }

                else if($po->remark == "PR"){
                    $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_purchase_requisitions.department')
                    ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                    ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                    ->leftJoin('acc_purchase_requisitions', 'acc_purchase_order_details.no_pr', '=', 'acc_purchase_requisitions.no_pr')
                    ->where('acc_purchase_orders.id', '=', $id)
                    ->get();
                }

                $pr = AccPurchaseOrder::select('no_pr')
                ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                ->where('acc_purchase_orders.id', '=', $id)
                ->distinct()
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_po', array(
                    'po' => $detail_po,
                    'pr' => $pr
                ));

                $pdf->save(public_path() . "/po_list/".$detail_po[0]->no_po.".pdf");

                // $isimail = "
                // select t1.*,  IF(t1.goods_price != 0,sum(t1.goods_price*t1.qty),sum(t1.service_price*t1.qty)) as amount from 
                // (SELECT
                // acc_purchase_orders.*,
                // acc_purchase_order_details.budget_item,
                // acc_purchase_order_details.goods_price,
                // acc_purchase_order_details.service_price,
                // acc_purchase_order_details.qty
                // FROM
                // acc_purchase_orders
                // JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
                // WHERE
                // acc_purchase_orders.id = ".$id.")
                // t1";

                $isimail = "
                SELECT acc_purchase_orders.*,
                acc_purchase_order_details.nama_item,
                acc_purchase_order_details.budget_item,
                acc_purchase_order_details.delivery_date,
                acc_purchase_order_details.goods_price,
                acc_purchase_order_details.service_price,
                acc_purchase_order_details.qty,
                acc_purchase_order_details.uom
                FROM
                acc_purchase_orders
                JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
                WHERE
                acc_purchase_orders.id = ".$id."";

                $po_isi = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($po_isi, 'purchase_order'));

                $message = 'PO dengan Nomor '.$po->no_po;
                $message2 ='Berhasil di approve';
            }
            else{
                $message = 'PO dengan Nomor. '.$po->no_po;
                $message2 ='Sudah di approve/reject';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $po->no_pr,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $po->no_pr,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    public function poapprovalgm($id){
        $po = AccPurchaseOrder::find($id);
        try{
            if ($po->posisi == "gm_pch")
            {
                $po->posisi = 'pch';
                $po->approval_authorized4 = "Approved";
                $po->date_approval_authorized4 = date('Y-m-d H:i:s');
                $po->status = "not_sap";

                    //kirim email Staff Purchasing sebagai pemberitahuan
                $mailto = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.buyer_id = users.username where acc_purchase_orders.id = '" . $id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }

                $po->save();

                if($po->remark == "Investment"){
                    $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_investments.applicant_department')
                    ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                    ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                    ->leftJoin('acc_investments', 'acc_purchase_order_details.no_pr', '=', 'acc_investments.reff_number')
                    ->where('acc_purchase_orders.id', '=', $id)
                    ->get();
                }

                else if($po->remark == "PR"){
                    $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_purchase_requisitions.department')
                    ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                    ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                    ->leftJoin('acc_purchase_requisitions', 'acc_purchase_order_details.no_pr', '=', 'acc_purchase_requisitions.no_pr')
                    ->where('acc_purchase_orders.id', '=', $id)
                    ->get();
                }

                $pr = AccPurchaseOrder::select('no_pr')
                ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
                ->where('acc_purchase_orders.id', '=', $id)
                ->distinct()
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_po', array(
                    'po' => $detail_po,
                    'pr' => $pr
                ));

                $pdf->save(public_path() . "/po_list/".$detail_po[0]->no_po.".pdf");

                // $isimail = "
                // select t1.*,  IF(t1.goods_price != 0,sum(t1.goods_price*t1.qty),sum(t1.service_price*t1.qty)) as amount from 
                // (SELECT
                // acc_purchase_orders.*,
                // acc_purchase_order_details.budget_item,
                // acc_purchase_order_details.goods_price,
                // acc_purchase_order_details.service_price,
                // acc_purchase_order_details.qty
                // FROM
                // acc_purchase_orders
                // JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
                // WHERE
                // acc_purchase_orders.id = ".$id.")
                // t1";

                $isimail = "
                SELECT acc_purchase_orders.*,
                acc_purchase_order_details.nama_item,
                acc_purchase_order_details.budget_item,
                acc_purchase_order_details.delivery_date,
                acc_purchase_order_details.goods_price,
                acc_purchase_order_details.service_price,
                acc_purchase_order_details.qty,
                acc_purchase_order_details.uom
                FROM
                acc_purchase_orders
                JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
                WHERE
                acc_purchase_orders.id = ".$id."";

                $po_isi = db::select($isimail);

                Mail::to($mailtoo)->send(new SendEmail($po_isi, 'purchase_order'));

                $message = 'PO No. '.$po->no_po;
                $message2 ='Successfully Approved';
            }
            else{
                $message = 'PO No. '.$po->no_po;
                $message2 ='Already approved/rejected';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $po->no_po,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $po->no_po,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    public function poreject(Request $request, $id)
    {
        $po = AccPurchaseOrder::find($id);

        if ($po->posisi == "manager_pch" || $po->posisi == "dgm_pch" || $po->posisi == "gm_pch")
        {
            $po->datereject = date('Y-m-d H:i:s');
            $po->posisi = "staff_pch";
            $po->approval_authorized2 = null;
            $po->date_approval_authorized2 = null;
            $po->approval_authorized3 = null;
            $po->date_approval_authorized3 = null;
            $po->approval_authorized4 = null;
            $po->date_approval_authorized4 = null;
        }
        $po->save();

        // $isimail = "
        // select t1.*,  IF(t1.goods_price != 0,sum(t1.goods_price*t1.qty),sum(t1.service_price*t1.qty)) as amount from 
        // (SELECT
        // acc_purchase_orders.*,
        // acc_purchase_order_details.budget_item,
        // acc_purchase_order_details.goods_price,
        // acc_purchase_order_details.service_price,
        // acc_purchase_order_details.qty
        // FROM
        // acc_purchase_orders
        // JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
        // WHERE
        // acc_purchase_orders.id = ".$po->id.")
        // t1";

        $isimail = "
        SELECT acc_purchase_orders.*,
        acc_purchase_order_details.nama_item,
        acc_purchase_order_details.budget_item,
        acc_purchase_order_details.delivery_date,
        acc_purchase_order_details.goods_price,
        acc_purchase_order_details.service_price,
        acc_purchase_order_details.qty,
        acc_purchase_order_details.uom
        FROM
        acc_purchase_orders
        JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
        WHERE
        acc_purchase_orders.id = ".$po->id."";

        $tolak = db::select($isimail);

        //kirim email ke Buyer
        $mails = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.buyer_id = users.username where acc_purchase_orders.id ='" . $po->id . "'";
        $mailtoo = DB::select($mails);


        Mail::to($mailtoo)->send(new SendEmail($tolak, 'purchase_order'));

        $message = 'PO dengan Nomor. '.$po->no_po;
        $message2 ='Tidak Disetujui';

        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $po->no_po,
            'message' => $message,
            'message2' => $message2,
        ))->with('page', 'Approval');
    }

    public function reject_purchase_order(Request $request, $id)
    {
        $alasan = $request->get('alasan');

        $po = AccPurchaseOrder::find($id);

        if ($po->posisi == "manager_pch" || $po->posisi == "dgm_pch" || $po->posisi == "gm_pch")
        {
            $po->reject = $alasan;
            $po->datereject = date('Y-m-d H:i:s');
            $po->posisi = "staff_pch";
            $po->approval_authorized2 = null;
            $po->date_approval_authorized2 = null;
            $po->approval_authorized3 = null;
            $po->date_approval_authorized3 = null;
            $po->approval_authorized4 = null;
            $po->date_approval_authorized4 = null;
        }

        $po->save();

        // $isimail = "
        // select t1.*,  IF(t1.goods_price != 0,sum(t1.goods_price*t1.qty),sum(t1.service_price*t1.qty)) as amount from 
        // (SELECT
        // acc_purchase_orders.*,
        // acc_purchase_order_details.budget_item,
        // acc_purchase_order_details.goods_price,
        // acc_purchase_order_details.service_price,
        // acc_purchase_order_details.qty
        // FROM
        // acc_purchase_orders
        // JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
        // WHERE
        // acc_purchase_orders.id = ".$po->id.")
        // t1";

        $isimail = "
        SELECT acc_purchase_orders.*,
        acc_purchase_order_details.nama_item,
        acc_purchase_order_details.budget_item,
        acc_purchase_order_details.delivery_date,
        acc_purchase_order_details.goods_price,
        acc_purchase_order_details.service_price,
        acc_purchase_order_details.qty,
        acc_purchase_order_details.uom
        FROM
        acc_purchase_orders
        JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
        WHERE
        acc_purchase_orders.id = ".$po->id."";

        $tolak = db::select($isimail);

            //kirim email ke Buyer
        $mails = "select distinct email from acc_purchase_orders join users on acc_purchase_orders.buyer_id = users.username where acc_purchase_orders.id ='" . $po->id . "'";
        $mailtoo = DB::select($mails);

        Mail::to($mailtoo)->send(new SendEmail($tolak, 'purchase_order'));
        return redirect('/purchase_order/verifikasi/' . $id)->with('status', 'PO Not Approved')
        ->with('page', 'Purchase Requisition');
    }

    //==================================//
    //          Report PO               //
    //==================================//
    public function report_purchase_order($id){

        $po = AccPurchaseOrder::find($id);

        if($po->remark == "Investment"){
            $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_investments.applicant_department')
            ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
            ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
            ->leftJoin('acc_investments', 'acc_purchase_order_details.no_pr', '=', 'acc_investments.reff_number')
            ->where('acc_purchase_orders.id', '=', $id)
            ->get();
        }

        else if($po->remark == "PR"){
            $detail_po = AccPurchaseOrder::select('acc_purchase_orders.*','acc_suppliers.*','acc_purchase_order_details.*','acc_purchase_requisitions.department')
            ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
            ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
            ->leftJoin('acc_purchase_requisitions', 'acc_purchase_order_details.no_pr', '=', 'acc_purchase_requisitions.no_pr')
            ->where('acc_purchase_orders.id', '=', $id)
            ->get();
        }

        $pr = AccPurchaseOrder::select('no_pr')
        ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
        ->where('acc_purchase_orders.id', '=', $id)
        ->distinct()
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('accounting_purchasing.report.report_po', array(
            'po' => $detail_po,
            'pr' => $pr
        ));

                // $pdf->save(public_path() . "/pr/" . $reports[0]->id . ".pdf");

        $path = "po_list/" . $detail_po[0]->no_po . ".pdf";
        return $pdf->stream("PO ".$detail_po[0]->no_po. ".pdf");

                // return view('accounting_purchasing.report.report_po', array(
                //  'po' => $detail_po,
                //  'pr' => $pr
                // ))->with('page', 'PO')->with('head', 'PO List');
    }

    public function exportPO(Request $request){

        $time = date('d-m-Y H;i;s');

        $tanggal = "";

        if (strlen($request->get('datefrom')) > 0)
        {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and tgl_po >= '" . $datefrom . " 00:00:00' ";
            if (strlen($request->get('dateto')) > 0)
            {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and tgl_po  <= '" . $dateto . " 23:59:59' ";
            }
        }

        $po_detail = db::select(
            "
            Select acc_purchase_order_details.id,acc_purchase_orders.no_po, acc_purchase_orders.no_po_sap, acc_purchase_orders.remark, acc_purchase_orders.note, acc_purchase_order_details.no_pr, acc_purchase_orders.tgl_po, acc_purchase_orders.supplier_code , acc_purchase_orders.supplier_name, acc_purchase_orders.currency, acc_purchase_orders.material, acc_purchase_orders.buyer_name, acc_purchase_order_details.no_item, acc_purchase_order_details.nama_item, acc_purchase_order_details.delivery_date, acc_purchase_order_details.qty, acc_purchase_order_details.uom, acc_purchase_order_details.goods_price, acc_purchase_order_details.service_price, acc_purchase_order_details.budget_item, acc_purchase_orders.cost_center, acc_purchase_order_details.gl_number, acc_purchase_requisitions.emp_name, acc_investments.applicant_name from acc_purchase_orders left join acc_purchase_order_details on acc_purchase_orders.no_po = acc_purchase_order_details.no_po left join acc_purchase_requisitions on acc_purchase_order_details.no_pr = acc_purchase_requisitions.no_pr left join acc_investments on acc_purchase_order_details.no_pr = acc_investments.reff_number WHERE acc_purchase_orders.deleted_at IS NULL " . $tanggal . " order by acc_purchase_orders.no_po,id ASC
            ");

        // and acc_purchase_orders.posisi = 'pch' and acc_purchase_orders.`status` = 'not_sap' and no_po_sap is null 

        $data = array(
            'po_detail' => $po_detail
        );

        ob_clean();

        Excel::create('PO List '.$time, function($excel) use ($data){
            $excel->sheet('Location', function($sheet) use ($data) {
              return $sheet->loadView('accounting_purchasing.purchase_order_excel', $data);
          });
        })->export('xlsx');
    }

    public function update_purchase_requisition_po(Request $request)
    {
        $id = Auth::id();
        $lop3 = $request->get('lop3');
        $lop = explode(',', $request->get('looping_pr'));

        $total_nambah = 0;
        $total_update = 0;
        $counter = 0;

        try
        {
            foreach ($lop as $lp)
            {
                $item_code = "item_code_edit" . $lp;
                $item_desc = "item_desc_edit" . $lp;
                $item_spec = "item_spec_edit" . $lp;
                $item_stock = "item_stock_edit" . $lp;
                $item_uom = "uom_edit" . $lp;
                $item_req = "req_date_edit" . $lp;
                $item_qty = "qty_edit" . $lp;
                $item_price = "item_price_edit" . $lp;
                $item_amount = "amount_edit" . $lp;

            // $amount = preg_replace('/[^0-9]/', '', $request->get($item_amount));

                $getitem = AccPurchaseRequisitionItem::where('id', $lp)->first();

                $data10 = AccBudgetHistory::where('no_item',$getitem->item_desc)
                ->update([
                    'no_item' => $request->get($item_desc),
                ]);


                $data2 = AccPurchaseRequisitionItem::where('id', $lp)->update([
                  'item_code' => $request->get($item_code), 
                  'item_desc' => $request->get($item_desc), 
                  'item_spec' => $request->get($item_spec),
                  'item_stock' => $request->get($item_stock), 
                  'item_uom' => $request->get($item_uom), 
                  'item_request_date' => $request->get($item_req), 
                  'item_qty' => $request->get($item_qty),
                  'item_price' => $request->get($item_price),
                  'item_amount' => $request->get($item_amount),
                  'created_by' => $id
              ]);


                if ($data2) {
                    $konversi = "konversi_dollar" . $lp;

                    $getamount = AccBudgetHistory::where('budget', $request->get('no_budget_edit'))
                    ->where('category_number',$request->get('no_pr_edit'))
                    ->where('no_item',$request->get($item_desc))
                    ->first();

                    $counter = $counter + $getamount->amount;

                    $data5 = AccBudgetHistory::where('budget', $request->get('no_budget_edit'))
                    ->where('category_number',$request->get('no_pr_edit'))
                    ->where('no_item',$request->get($item_desc))
                    ->update([
                        'amount' => $request->get($konversi),
                        'created_by' => $id
                    ]);

                    $total_update = $total_update + $request->get($konversi);
                }
            }

            for ($i = 2;$i <= $lop3;$i++)
            {

                $item_code = "item_code" . $i;
                $item_desc = "item_desc" . $i;
                $item_spec = "item_spec" . $i;
                $item_stock = "item_stock" . $i;
                $item_req = "req_date" . $i;
                $item_currency = "item_currency" . $i;
                $item_currency_text = "item_currency_text" . $i;
                $item_price = "item_price" . $i;
                $item_qty = "qty" . $i;
                $item_uom = "uom" . $i;
                $item_amount = "amount" . $i;
                $status = "";

                    //Jika ada value kosong
                if ($request->get($item_code) == "kosong")
                {
                    $request->get($item_code) == "";
                }

                    //Jika item kosong
                if ($request->get($item_code) != null)
                {
                    $status = "fixed";
                }
                else
                {
                    $status = "sementara";
                }

                if ($request->get($item_currency) != "")
                {
                    $current = $request->get($item_currency);
                }
                else if ($request->get($item_currency_text) != "")
                {
                    $current = $request->get($item_currency_text);
                }

                    //get only number
            // $price_real = preg_replace('/[^0-9]/', '', $request->get($item_price));
            // $amount = preg_replace('/[^0-9]/', '', $request->get($item_amount));

                $data2 = new AccPurchaseRequisitionItem([
                    'no_pr' => $request->get('no_pr_edit') , 
                    'item_code' => $request->get($item_code) , 
                    'item_desc' => $request->get($item_desc) , 
                    'item_spec' => $request->get($item_spec) ,
                    'item_stock' => $request->get($item_stock) , 
                    'item_request_date' => $request->get($item_req) , 
                    'item_currency' => $current,
                    'item_price' => $request->get($item_price),
                    'item_qty' => $request->get($item_qty) , 
                    'item_uom' => $request->get($item_uom) , 
                    'item_amount' => $request->get($item_amount), 
                    'status' => $status, 
                    'created_by' => $id
                ]);

                $data2->save();

                $dollar = "konversi_dollar" . $i;
                $month = strtolower(date("M",strtotime($request->get('tgl_pengajuan_edit'))));
                $begbal = $request->get('SisaBudgetEdit') + $request->get('TotalPembelianEdit');

                $data3 = new AccBudgetHistory([
                    'budget' => $request->get('no_budget_edit'),
                    'budget_month' => $month,
                    'budget_date' => $request->get('tgl_pengajuan_edit'),
                    'category_number' => $request->get('no_pr_edit'),
                    'beg_bal' => $begbal,
                    'no_item' => $request->get($item_desc),
                    'amount' => $request->get($dollar),
                    'created_by' => $id
                ]);

                $data3->save();

                $total_nambah += $request->get($dollar);
            }

            // $datePembelian = $request->get('tgl_pengajuan_edit');

            // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$datePembelian'");
            // foreach ($fy as $fys) {
            //     $fiscal = $fys->fiscal_year;
            // }

            $getbulan = AccBudget::select('budget_no', 'periode')
            ->where('budget_no', $request->get('no_budget_edit'))
            ->first();

            if ($getbulan->periode == "FY198") {
                $bulan = strtolower(date('M'));
            }
            else{
                $bulan = "apr";
            }

            // $bulan = strtolower(date("M",strtotime($datePembelian))); //aug,sep,oct

            $sisa_bulan = $bulan.'_sisa_budget';                    
            //get Data Budget Based On Periode Dan Nomor
            $budget = AccBudget::where('budget_no','=',$request->get('no_budget_edit'))->first();
            
            $total = $budget->$sisa_bulan + $counter - $total_update - $total_nambah;

            if ($total < 0) {
                return redirect('/purchase_order')->with('error', 'Total Melebihi Budget')
                ->with('page', 'Purchase Order');
            } else{
                $dataupdate = AccBudget::where('budget_no',$request->get('no_budget_edit'))->update([
                    $sisa_bulan => $total
                ]);
            }

            $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
            ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
            ->join('acc_budget_histories', function($join) {
               $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
               $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
           })
            ->where('acc_purchase_requisitions.id', '=', $request->get('id_edit_pr'))
            ->distinct()
            ->get();

            $exchange_rate = AccExchangeRate::select('*')
            ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
            ->where('currency','!=','USD')
            ->orderBy('currency','ASC')
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'landscape');

            $pdf->loadView('accounting_purchasing.report.report_pr', array(
                'pr' => $detail_pr,
                'rate' => $exchange_rate
            ));

            $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");


            return redirect('/purchase_order')
            ->with('status', 'Purchase Requisition Berhasil Dirubah')
            ->with('page', 'Purchase Order');
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_order')->with('error', $e->getMessage())
            ->with('page', 'Purchase Order');
        }
    }

    public function update_investment_po(Request $request)
    {
        $id = Auth::id();
        $lop = explode(',', $request->get('looping_inv'));
        $id_inv = $request->get('id_edit_inv');

        $invest = AccInvestment::find($id_inv);
        $judul = substr($invest->reff_number, 0, 7);
        
        try
        {
            foreach ($lop as $lp)
            {
                $item_code = "no_item_edit" . $lp;
                $item_desc = "detail_edit" . $lp;

                $getitem = AccInvestmentDetail::where('id', $lp)->first();

                $data10 = AccBudgetHistory::where('no_item',$getitem->detail)
                ->update([
                    'no_item' => $request->get($item_desc),
                ]);

                $data2 = AccInvestmentDetail::where('id', $lp)->update([
                  'no_item' => $request->get($item_code), 
                  'detail' => $request->get($item_desc), 
                  'created_by' => $id
              ]);
            }

            $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
            ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
            ->where('acc_investments.id', '=', $id_inv)
            ->get();

            $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
            ->where('acc_investments.id', '=', $id_inv)
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('Legal', 'potrait');

            $pdf->loadView('accounting_purchasing.report.report_investment', array(
                'inv' => $detail_inv,
                'inv_budget' => $inv_budget
            ));

            $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

            return redirect('/purchase_order_investment')
            ->with('status', 'Investment Berhasil Dirubah')
            ->with('page', 'Purchase Order Investment');
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_order_investment')->with('error', $e->getMessage())
            ->with('page', 'Purchase Order Investment');
        }
    }

    public function pogetiteminvest(Request $request)
    {
        $html = array();
        $kode_item = AccInvestmentDetail::join('acc_investments', 'acc_investment_details.reff_number', '=', 'acc_investments.reff_number')
        ->join('acc_investment_budgets','acc_investments.reff_number','=','acc_investment_budgets.reff_number')
        ->join('acc_items','acc_investment_details.no_item','=','acc_items.kode_item')
        ->where('acc_investment_details.reff_number', $request->reff_number)
        ->where('acc_investment_details.no_item', $request->no_item)
        ->get();

        $last_price = AccPurchaseOrderDetail::select('goods_price')
        ->where('no_item', $request->item_code)
        ->orderBy('id','desc')
        ->get();

        if(count($last_price) > 0){
            $last = $last_price[0]->goods_price;
        }else{
            $last = 0;
        }

        foreach ($kode_item as $item)
        {
            $html = array(
                'no_item' => $item->no_item,
                'deskripsi' => $item->detail,
                'uom' => $item->uom,
                'qty' => $item->qty,
                'price' => $item->price,
                'currency' => $item->currency,
                'amount' => $item->amount,
                'delivery_date' => $item->delivery_order,
                'last_price' => $last,
                'budget_no' => $item->budget_no,
            );

        }

        return json_encode($html);
    }


    //==================================//
    //            Investment            //
    //==================================//


    public function Investment()
    {
        $title = 'Investment';
        $title_jp = '投資申請';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $dept = $this->dept;

        return view('accounting_purchasing.investment', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'dept' => $dept
        ))->with('page', 'Investment')
        ->with('head', 'inv');
    }

    public function fetch_investment(Request $request)
    {
        $tanggal = "";
        $adddepartment = "";
        $restrict_dept = "";

        if (strlen($request->get('datefrom')) > 0)
        {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and acc_investments.submission_date >= '" . $datefrom . " 00:00:00' ";
            if (strlen($request->get('dateto')) > 0)
            {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and acc_investments.submission_date  <= '" . $dateto . " 23:59:59' ";
            }
        }

        if ($request->get('department') != null)
        {
            $departments = $request->get('department');
            $deptlength = count($departments);
            $department = "";

            for ($x = 0;$x < $deptlength;$x++)
            {
                $department = $department . "'" . $departments[$x] . "'";
                if ($x != $deptlength - 1)
                {
                    $department = $department . ",";
                }
            }
            $adddepartment = "and acc_investments.applicant_department in (" . $department . ") ";
        }

        //Get Employee Department
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('department')
        ->first();

        if (Auth::user()->role_code == "MIS" || $emp_dept->department == "Accounting Department") {
            $restrict_dept = "";
        }
        else{
            $restrict_dept = "and applicant_department = '".$emp_dept->department."'";
        }

        $qry = "SELECT  * FROM acc_investments
        WHERE acc_investments.deleted_at IS NULL " . $tanggal . "" . $adddepartment . "" . $restrict_dept. "
        ORDER BY acc_investments.id DESC";

        $invest = DB::select($qry);

        return DataTables::of($invest)
        ->editColumn('submission_date', function ($invest)
        {
            return date('d F Y', strtotime($invest->submission_date));
        })

        ->editColumn('supplier_code', function ($invest)
        {
            return $invest->supplier_code.' - '.$invest->supplier_name;
        })
        ->editColumn('file', function ($invest)
        {
            $data = json_decode($invest->file);

            $fl = "";

            if ($invest->file != null)
            {
                for ($i = 0;$i < count($data);$i++)
                {
                    $fl .= '<a href="files/investment/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
                }
            }
            else
            {
                $fl = '-';
            }

            return $fl;
        })
        ->editColumn('status', function ($invest)
        {
            $id = $invest->id;
            
            if ($invest->posisi == "user" && $invest->status == "approval")
            {
                return '<label class="label label-danger">Belum Dikirim</label>';
            }
            if ($invest->posisi == "user" && $invest->status == "comment")
            {
                return '<label class="label label-warning">Commended</label>';
            }
            else if ($invest->posisi == "acc_budget" || $invest->posisi == "acc_pajak")
            {
                return '<label class="label label-warning">Verifikasi Oleh Accounting</label>';
            }
            else if ($invest->posisi == "manager")
            {
                return '<label class="label label-warning">Diverifikasi Manager</label>';
            }
            else if ($invest->posisi == "dgm")
            {
                return '<label class="label label-warning">Diverifikasi DGM</label>';
            }
            else if ($invest->posisi == "gm")
            {
                return '<label class="label label-warning">Diverifikasi GM</label>';
            }
            else if ($invest->posisi == "manager_acc")
            {
                return '<label class="label label-warning">Diverifikasi Manager Accounting</label>';
            }
            else if ($invest->posisi == "direktur_acc")
            {
                return '<label class="label label-warning">Diverifikasi Direktur Accounting</label>';
            }
            else if ($invest->posisi == "presdir")
            {
                return '<label class="label label-warning">Diverifikasi Presdir</label>';
            }
            else if ($invest->posisi == "finished")
            {
                return '<label class="label label-success">Telah Diverifikasi</label>';
            }

        })
        ->addColumn('action', function ($invest)
        {
            $id = $invest->id;
            
            if ($invest->posisi == "user")
            {
                return '
                <a href="investment/detail/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a>
                <a href="investment/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report Investment</a>
                <a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="deleteConfirmationInvestment('.$id.')" data-toggle="modal" data-target="#modalDeleteInvestment"  title="Delete Investment"><i class="fa fa-trash"></i> Delete Investment</a>
                ';
            }
            else if ($invest->posisi == "acc_budget" || $invest->posisi == "acc_pajak")
            {
                return '<a href="investment/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report Investment</a>';
            }
            else if ($invest->posisi == "acc" || $invest->posisi == "manager" || $invest->posisi == "dgm" || $invest->posisi == "gm" || $invest->posisi == "manager_acc" || $invest->posisi == "direktur_acc" || $invest->posisi == "presdir" || $invest->posisi == "finished")
            {
                return '
                <a href="investment/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report Investment</a>
                ';
            }
        })

        // ->editColumn('bukti_adagio', function ($invest)
        // {
        //     $id = $invest->id;

        //     $bukti = "";
        //     if ($invest->bukti_adagio == null && $invest->status == "adagio")
        //     {
        //         $bukti = '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-success" class="btn btn-primary btn-md" onClick="uploadBukti('.$id.')"><i class="fa fa-file-excel-o"></i> Upload Bukti Approval</a>';
        //     }
        //     else if ($invest->bukti_adagio != null){
        //         $bukti = '<a href="files/investment/adagio/'.$invest->bukti_adagio.'" target="_blank" class="fa fa-paperclip"> '.$invest->bukti_adagio.'</a>';   
        //     }
        //     else
        //     {
        //         $bukti = '-';
        //     }

        //     return $bukti;
        // })

        ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file', 'supplier_code' => 'supplier_code'])
        ->make(true);
    }

    public function create_investment()
    {
        $title = 'Buat Form Investment';
        $title_jp = '投資申請書を作成';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
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
        try
        {
            $id_user = Auth::id();
            
            //Cek File
            $files = array();
            $file = new AccInvestment();
            if ($request->file('attachment') != NULL)
            {
                if ($files = $request->file('attachment'))
                {
                    foreach ($files as $file)
                    {
                        $nama = $file->getClientOriginalName();
                        $file->move('files/investment', $nama);
                        $data[] = $nama;
                    }
                }
                $file->filename = json_encode($data);
            }
            else
            {
                $file->filename = NULL;
            }

            $manager = null;
            $dgm = null;
            $gm = null;

            //Get Manager

            if ($request->get('applicant_department') == "Production Engineering Department")
            {
                $getmanager = EmployeeSync::select('employee_id', 'name', 'position', 'section')
                ->whereNull('end_date')
                ->where('department','=','Maintenance Department')
                ->where('position','=','Manager')
                ->first();
            }
            else if ($request->get('applicant_department') == "General Affairs Department")
            {
                $getmanager = EmployeeSync::select('employee_id', 'name', 'position', 'section')
                ->whereNull('end_date')
                ->where('department','=','Human Resources Department')
                ->where('position','=','Manager')
                ->first();
            }
            else if ($request->get('applicant_department') == "Purchasing Control Department")
            {
                $getmanager = EmployeeSync::select('employee_id', 'name', 'position', 'section')
                ->whereNull('end_date')
                ->where('department','=','Procurement Department')
                ->where('position','=','Manager')
                ->first();
            }
            else if ($request->get('applicant_department') == "Woodwind Instrument - Key Parts Process (WI-KPP) Department")
            {
                $getmanager = EmployeeSync::select('employee_id', 'name', 'position', 'section')
                ->whereNull('end_date')
                ->where('department','=','Educational Instrument (EI) Department')
                ->where('position','=','Manager')
                ->first();
            }
            else if ($request->get('applicant_department') == "Woodwind Instrument - Welding Process (WI-WP) Department")
            {
                $getmanager = EmployeeSync::select('employee_id', 'name', 'position', 'section')
                ->whereNull('end_date')
                ->where('department','=','Woodwind Instrument - Body Parts Process (WI-BPP) Department')
                ->where('position','=','Manager')
                ->first();
            }
            else
            {
                $getmanager = EmployeeSync::select('employee_id', 'name', 'position', 'section')
                ->whereNull('end_date')
                ->where('department','=',$request->get('applicant_department'))
                ->where('position','=','Manager')
                ->first();
            }


            if ($getmanager != null)
            {
                $manager = $getmanager->employee_id."/".$getmanager->name;
            }


            //Get DGM & GM

            $getdgm = EmployeeSync::select('employee_id', 'name', 'position')
            ->whereNull('end_date')
            ->where('employee_id', '=', 'PI0109004')
            ->first();


            if($request->get('applicant_department') == "Human Resources Department" || $request->get('applicant_department') == "General Affairs Department"){
                $dgm = null;

                //GM Pak Arief
                $getgm = EmployeeSync::select('employee_id', 'name', 'position')
                ->where('employee_id','=','PI9709001')
                ->first();

                $gm = $getgm->employee_id."/".$getgm->name;
            }
            else if($request->get('applicant_department') == "Logistic Department" || $request->get('applicant_department') == "Production Control Department" || $request->get('applicant_department') == "Purchasing Control Department" || $request->get('applicant_department') == "Procurement Department"){
                $dgm = null;
                //GM Pak Budhi
                $gm = $getdgm->employee_id."/".$getdgm->name;
            }
            else{

                $dgm = $getdgm->employee_id."/".$getdgm->name;
                //GM Pak Hayakawa
                $getgm = EmployeeSync::select('employee_id', 'name', 'position')
                ->where('employee_id','=','PI1206001')
                ->first();

                $gm = $getgm->employee_id."/".$getgm->name;
            }

            if($request->get('applicant_department') == "Accounting Department"){
                $manager = null;
                $dgm = null;
                $gm = null;
            }

            $inv = AccInvestment::create([
                'applicant_id' => $request->get('applicant_id') , 
                'applicant_name' => $request->get('applicant_name') , 
                'applicant_department' => $request->get('applicant_department') , 
                'reff_number' => $request->get('reff_number') , 
                'submission_date' => $request->get('submission_date') , 
                'category' => $request->get('category') , 
                'subject' => $request->get('subject') , 
                'type' => $request->get('type') , 
                'objective' => $request->get('objective') , 
                'objective_detail' => $request->get('objective_detail') , 
                'supplier_code' => $request->get('vendor') , 
                'supplier_name' => $request->get('vendor_name') , 
                'date_order' => $request->get('date_order') , 
                'delivery_order' => $request->get('date_delivery') , 
                'payment_term' => $request->get('payment_term') , 
                'currency' => $request->get('currency') ,
                'note' => $request->get('note') , 
                'quotation_supplier' => $request->get('quotation_supplier') , 
                'file' => $file->filename , 
                'posisi' => 'user', 
                'status' => 'approval',
                'approval_manager' => $manager,
                'approval_dgm' => $dgm,
                'approval_gm' => $gm,
                'approval_manager_acc' => $this->manager_acc,
                'approval_dir_acc' => $this->dir_acc,
                'approval_presdir' => $this->presdir,
                'created_by' => $id_user
            ]);

            $inv->save();

            return redirect('/investment/detail/'.$inv->id)->with('status', 'Investment Berhasil Dibuat')
            ->with('page', 'Form Investment');


        }
        catch(QueryException $e)
        {
            return redirect('/investment/create')->with('error', 'Investment Gagal Dibuat')
            ->with('page', 'Form Investment');
        }
    }

    public function get_nomor_inv(Request $request)
    {
        $datenow = date('Y-m-d');
        $bulan = date('m');

        $query = "SELECT fiscal_year FROM `weekly_calendars` where week_date = '$datenow'";
        $fy = DB::select($query);

        if ($fy[0]->fiscal_year == "FY197") {
            $tahun = '20';
        }else if ($fy[0]->fiscal_year == "FY198") {
            $tahun = '21';
        }else if ($fy[0]->fiscal_year == "FY199") {
            $tahun = '22';
        }

        $query = "SELECT reff_number FROM `acc_investments` order by reff_number DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if ($nomorurut != null)
        {
            $nomor = substr($nomorurut[0]->reff_number,3,4);
            $nomor = $nomor + 1;
            $nomor = sprintf('%04d', $nomor);
        }
        else
        {
            $nomor = "0001";
        }

        $result['tahun'] = $tahun;
        $result['bulan'] = $bulan;
        $result['no_urut'] = $nomor;

        return json_encode($result);
    }

    public function delete_investment(Request $request)
    {
        try
        {
            $invest = AccInvestment::find($request->get('id'));
            // $date = date('Y-m-d');

            $budget_log = AccBudgetHistory::where('category_number', '=', $invest->reff_number)
            ->get();

            if ($budget_log != null) {
                //FY
                // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$date'");

                // foreach ($fy as $fys) {
                //     $fiscal = $fys->fiscal_year;
                // }

                foreach ($budget_log as $log) {
                    $sisa_bulan = $log->budget_month.'_sisa_budget';
                    $budget = AccBudget::where('budget_no', $log->budget)->first();

                    $total = $budget->$sisa_bulan + $log->amount; //add total
                    $dataupdate = AccBudget::where('budget_no', $log->budget)->update([
                        $sisa_bulan => $total
                    ]);
                }
            }

            $delete_history = AccBudgetHistory::where('category_number', '=', $invest->reff_number)->delete();
            $delete_budget_log = AccInvestmentBudget::where('reff_number', '=', $invest->reff_number)->delete();
            $delete_inv_item = AccInvestmentDetail::where('reff_number', '=', $invest->reff_number)->delete();
            $delete_inv = AccInvestment::where('reff_number', '=', $invest->reff_number)->delete();

            $response = array(
                'status' => true,
            );

            return Response::json($response);
        }
        catch(QueryException $e)
        {
            return redirect('/investment')->with('error', $e->getMessage())
            ->with('page', 'Investment');
        }
    }

    public function fetchInvBudgetList(Request $request)
    {
        if ($request->get('category') == "Investment") {
            $cat = "Fixed Asset";
        }
        else if($request->get('category') == "Expense"){
            $cat = "Expenses";
        }

        if ($request->get('department') == "General Affairs Department") {
            $dept = "Human Resources Department";
        } 
        else if($request->get('department') == "Purchasing Control Department") {
            $dept = "Procurement Department";
        }
        // else if($request->get('department') == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
        //     $dept = "Woodwind Instrument - Key Parts Process (WI-KPP) Department";
        // }
        // else if($request->get('department') == "Woodwind Instrument - Surface Treatment (WI-ST) Department") {
        //     $dept = "Woodwind Instrument - Welding Process (WI-WP) Department";
        // }
        else{
            $dept = $request->get('department');
        }

        if($request->get('budget') == "On Budget"){

            if ($request->get('category') == "Investment") {

                $type = "";

                $budgets = AccBudget::select('acc_budgets.budget_no', 'acc_budgets.description', 'acc_budgets.periode')
                ->where('department', '=', $dept)
                ->where('category', '=', $cat)
                ->where('periode', '=', 'FY198')
                ->distinct()
                ->get();

            }
            //jika expense maka filter berdasarkan type
            else{
                if($request->get('type') != "Others") {
                    if($request->get('type') == "Office Supplies") {
                        $type = "Office supplies/facilities";
                    }
                    else if($request->get('type') == "Repair and Maintenance"){
                        $type = "Repair Maintenance";
                    }
                    else if($request->get('type') == "Tools, Jigs and Furniture"){
                        $type = "Tool, Jig, and Furniture";
                    }
                    else if($request->get('type') == "Moulding"){
                        $type = "Molding";
                    }
                    else{
                        $type = $request->get('type');
                    }
                    
                    $budgets = AccBudget::select('acc_budgets.budget_no', 'acc_budgets.description', 'acc_budgets.periode')
                    ->where('department', '=', $dept)
                    ->where('category', '=', $cat)
                    ->where('account_name', '=', $type)
                    ->where('periode', '=', 'FY198')
                    ->distinct()
                    ->get();
                }
                else{
                    $budgets = AccBudget::select('acc_budgets.budget_no', 'acc_budgets.description', 'acc_budgets.periode')
                    ->where('department', '=', $dept)
                    ->where('category', '=', $cat)
                    ->where('periode', '=', 'FY198')
                    ->distinct()
                    ->get();
                }

                
            }
        }
        else if ($request->get('budget') == "Shifting") {
            $type = "";

            $budgets = AccBudget::select('acc_budgets.budget_no', 'acc_budgets.description', 'acc_budgets.periode')
            ->where('department', '=', $dept)
            ->where('category', '=', $cat)
            ->where('periode', '=', 'FY198')
            ->distinct()
            ->get();
        }

        $response = array(
            'status' => true,
            'budget' => $budgets
        );

        return Response::json($response);
    }

    public function detail_investment($id)
    {
        $title = 'Detail Form Investment';
        $title_jp = '投資申請内容';

        $inv = AccInvestment::find($id);

        $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')->where('acc_investments.id', '=', $id)->get();

        $inv_item = AccInvestment::join('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')->where('acc_investments.id', '=', $id)->get();

        $emp = EmployeeSync::where('employee_id', $inv->applicant_id)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

        $items = db::select("select kode_item, kategori, deskripsi, spesifikasi from acc_items where deleted_at is null");

        return view('accounting_purchasing.investment_detail', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'investment' => $inv,
            'investment_budget' => $inv_budget,
            'investment_item' => $inv_item,
            'employee' => $emp,
            'vendor' => $vendor,
            'items' => $items,
            'uom' => $this->uom
        ))->with('page', 'Form Investment');
    }

    public function detail_investment_post(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $judul = substr($request->get('reff_number'), 0, 7);
            $jumlah = $request->get('jumlah');

            $inv = AccInvestment::where('id', $request->get('id'))
            ->update([
                'applicant_id' => $request->get('applicant_id') , 
                'applicant_name' => $request->get('applicant_name') , 
                'applicant_department' => $request->get('applicant_department') , 
                'reff_number' => $request->get('reff_number') , 
                'submission_date' => $request->get('submission_date') , 
                'category' => $request->get('category') , 
                'subject' => $request->get('subject') , 
                'subject_jpy' => $request->get('subject_jpy'), 
                'type' => $request->get('type') , 
                'objective' => $request->get('objective') , 
                'objective_detail' => $request->get('objective_detail') , 
                'objective_detail_jpy' => $request->get('objective_detail_jpy') , 
                'supplier_code' => $request->get('supplier') , 
                'supplier_name' => $request->get('supplier_name') , 
                'date_order' => $request->get('date_order') , 
                'delivery_order' => $request->get('date_delivery') , 
                'payment_term' => $request->get('payment_term') , 
                'note' => $request->get('note') , 
                'quotation_supplier' => $request->get('quotation_supplier'),
                'currency' => $request->get('currency') , 
                'pdf' => 'INV_'.$judul.'.pdf' ,
                'created_by' => $id_user
            ]);


            for ($i = 0;$i < $jumlah;$i++)
            {
                $category_budget = $request->get('budget_cat');
                if($category_budget[$i] != 'Out Of Budget'){

                    $budget_no = $request->get('budget');
                    $budget_name = $request->get('budget_name');
                    $budget_sisa = $request->get('sisa');
                    $budget_amount = $request->get('amount');
                    $budget_amount_ori = $request->get('amount_ori');

                    $data2 = AccInvestmentBudget::firstOrNew([
                        'reff_number' => $request->get('reff_number'),
                        'category_budget' => $category_budget[$i],
                        'budget_no' => $budget_no[$i] 
                    ]);

                    $data2->budget_name = $budget_name[$i];
                    $data2->sisa = $budget_sisa[$i];
                    $data2->total = $budget_amount[$i];
                    $data2->total_ori = $budget_amount_ori[$i];
                    $data2->created_by = $id_user;

                    $investment_item = AccInvestment::join('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')->where('acc_investments.id', '=', $request->get('id'))->get();

                    for ($z=0; $z < count($investment_item); $z++) { 

                        $getbulan = AccBudget::select('budget_no', 'periode')
                        ->where('budget_no',$budget_no[$i])
                        ->first();

                        if ($getbulan->periode == "FY198") {
                            $month = strtolower(date('M'));
                        }
                        else{
                            $month = "apr";
                        }

                        $data3 = AccBudgetHistory::firstOrNew([
                            'category_number' => $request->get('reff_number'),
                            'budget' => $budget_no[$i],
                            'no_item' => $investment_item[$z]->detail,
                        ]);

                        $data3->budget = $budget_no[$i];
                        $data3->budget_month = $month;
                        $data3->budget_date = date('Y-m-d');
                        $data3->category_number = $request->get('reff_number');
                        $data3->no_item = $investment_item[$z]->detail;
                        $data3->beg_bal = $budget_sisa[$i];
                        $data3->amount = $investment_item[$z]->dollar;
                        $data3->status = 'Investment';
                        $data3->created_by = $id_user;
                        $data3->save();
                    }

                    $totalPembelian = $budget_amount[$i];

                    if ($totalPembelian != null) {

                        $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
                        ->where('acc_investments.id', '=', $request->get('id'))->get();

                        if (count($inv_budget) == 0) {
                            // $datePembelian = date('Y-m-d');
                            // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$datePembelian'");

                            // foreach ($fy as $fys) {
                            //     $fiscal = $fys->fiscal_year;
                            // }

                            $getbulan = AccBudget::select('budget_no', 'periode')
                            ->where('budget_no',$budget_no[$i])
                            ->first();

                            if ($getbulan->periode == "FY198") {
                                $bulan = strtolower(date('M'));
                            }
                            else{
                                $bulan = "apr";
                            }

                            $sisa_bulan = $bulan.'_sisa_budget';                    
                            //get Data Budget Based On Periode Dan Nomor

                            $budget = AccBudget::where('budget_no','=',$budget_no[$i])->first();
                            
                            //perhitungan 
                            $total = $budget->$sisa_bulan - $totalPembelian;
                            $dataupdate = AccBudget::where('budget_no','=',$budget_no[$i])
                            ->update([
                                $sisa_bulan => $total
                            ]);

                        }
                    }
                }
                else{ // kalo out of budget
                    $category_budget = $request->get('budget_cat');
                    $budget_amount = $request->get('amount');
                    $budget_amount_ori = $request->get('amount_ori');

                    if ($request->get('category') == "Investment") {
                        $budget_no = "NoBdgFA198";
                    }else if ($request->get('category') == "Expense"){
                        $budget_no = "NoBdgExp198";
                    }

                    $data2 = AccInvestmentBudget::firstOrNew([
                        'reff_number' => $request->get('reff_number'),
                        'category_budget' => $category_budget[$i],
                        'budget_no' => $budget_no
                    ]);

                    $data2->total = $budget_amount[$i];
                    $data2->total_ori = $budget_amount_ori[$i];
                    $data2->created_by = $id_user;

                    $investment_item = AccInvestment::join('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')->where('acc_investments.id', '=', $request->get('id'))->get();

                    for ($z=0; $z < count($investment_item); $z++) { 

                        $getbulan = AccBudget::select('budget_no', 'periode')
                        ->where('budget_no',$budget_no)
                        ->first();

                        if ($getbulan->periode == "FY198") {
                            $month = strtolower(date('M'));
                        }
                        else{
                            $month = "apr";
                        }

                        $data3 = AccBudgetHistory::firstOrNew([
                            'category_number' => $request->get('reff_number'),
                            'budget' => $budget_no,
                            'no_item' => $investment_item[$z]->detail,
                        ]);

                        $data3->budget = $budget_no;
                        $data3->budget_month = $month;
                        $data3->budget_date = date('Y-m-d');
                        $data3->category_number = $request->get('reff_number');
                        $data3->no_item = $investment_item[$z]->detail;
                        $data3->beg_bal = 0;
                        $data3->amount = $investment_item[$z]->dollar;
                        $data3->status = 'Investment';
                        $data3->created_by = $id_user;
                        $data3->save();
                    }
                }
                $data2->save();
            }



            $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
            ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
            ->where('acc_investments.id', '=', $request->get('id'))
            ->get();

            $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
            ->where('acc_investments.id', '=', $request->get('id'))
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('Legal', 'potrait');

            $pdf->loadView('accounting_purchasing.report.report_investment', array(
                'inv' => $detail_inv,
                'inv_budget' => $inv_budget
            ));

            $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

            $response = array(
                'status' => true,
                'datas' => 'Data Berhasil Diubah'
            );
            return Response::json($response);

        }
        catch(QueryException $e)
        {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage()
            );
            return Response::json($response);
        }
    }


    public function check_investment_pch($id)
    {
        $emp_id = Auth::user()->username;

        $investment = AccInvestment::find($id);

        $path = '/investment_list/' . $investment->pdf;            
        $file_path = asset($path);

        return view('accounting_purchasing.check_investment', array(
            'investment' => $investment,
            'file_path' => $file_path,
        ))->with('page', 'Investment');
    }

    public function checked_investment(Request $request, $id){

        $invest = AccInvestment::find($id);
        $invest->receive_date = date('Y-m-d');
        $invest->save();

        return redirect('/investment/check_pch/'.$id)->with('status', 'Investment Sudah Berhasil Diterima')
        ->with('page', 'Invesment');
    }

        //Item Invesment
    public function fetch_investment_item($id)
    {
        $investment = AccInvestment::find($id);

        $investment_item = AccInvestmentDetail::leftJoin("acc_investments", "acc_investment_details.reff_number", "=", "acc_investments.reff_number")->select('acc_investment_details.*', 'acc_investments.currency')
        ->where('acc_investment_details.reff_number', '=', $investment->reff_number)
        ->get();

        return DataTables::of($investment_item)
        ->editColumn('price', function ($investment_item)
        {
            if ($investment_item->currency == "IDR") {
                $cur = "Rp.";
            }
            else if ($investment_item->currency == "USD") {
                $cur = "$";
            }
            else if($investment_item->currency == "JPY"){
                $cur = "¥";
            }

            return $cur." ".number_format($investment_item->price,2,",",".");
        })
        ->editColumn('amount', function ($investment_item)
        {
            if ($investment_item->currency == "IDR") {
                $cur = "Rp.";
            }
            else if ($investment_item->currency == "USD") {
                $cur = "$";
            }
            else if($investment_item->currency == "JPY"){
                $cur = "¥";
            }
            return $cur." ".number_format($investment_item->amount,2,",",".");
        })
        ->addColumn('action', function ($investment_item)
        {
            return '
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit(' . $investment_item->id . ')"><i class="fa fa-edit"></i> Edit</button>
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete(' . $investment_item->id . ',\'' . $investment_item->reff_number . '\')"><i class="fa fa-trash"></i> Delete</button>
            ';
        })
        ->rawColumns(['price' => 'price','amount' => 'amount', 'action' => 'action'])
        ->make(true);
    }

    public function getitemdesc(Request $request)
    {
        $html = array();
        $kode_item = AccItem::where('kode_item', $request->kode_item)
        ->get();
        foreach ($kode_item as $item)
        {
            $html = array(
                'detail' => $item->deskripsi,
                'harga' => $item->harga
            );

        }

        return json_encode($html);
    }

    public function gettotalamount(Request $request)
    {
        $html = array();
        $total = 0;
        $itemDetail = AccInvestmentDetail::where('reff_number', $request->reff_number)
        ->get();
        foreach ($itemDetail as $item)
        {
            $total += $item->amount;
        }

        return $total;
    }

    public function create_investment_item(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $item = new AccInvestmentDetail([
                'reff_number' => $request->get('reff_number') , 
                'no_item' => $request->get('kode_item') , 
                'detail' => $request->get('detail_item') , 
                'qty' => $request->get('jumlah_item') , 
                'uom' => $request->get('uom') , 
                'price' => $request->get('price_item') , 
                'amount' => $request->get('amount_item') , 
                'dollar' => $request->get('dollar') , 
                'created_by' => $id_user
            ]);

            $item->save();

            $response = array(
                'status' => true,
                'item' => $item
            );
            return Response::json($response);
        }
        catch(QueryException $e)
        {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062)
            {
                $response = array(
                    'status' => false,
                    'item' => "Item already exist"
                );
                return Response::json($response);
            }
            else
            {
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
        try
        {
            $items = AccInvestmentDetail::find($request->get("id"));
            $items->no_item = $request->get('kode_item');
            $items->detail = $request->get('detail_item');
            $items->qty = $request->get('jumlah_item');
            $items->uom = $request->get('uom');
            $items->price = $request->get('price_item');
            $items->amount = $request->get('amount_item');
            $items->dollar = $request->get('dollar');
            $items->save();

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
            );
            return Response::json($response);

        }
        catch(QueryException $e)
        {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062)
            {
                $response = array(
                    'status' => false,
                    'datas' => $e->getMessage() ,
                );
                return Response::json($response);
            }
            else
            {
                $response = array(
                    'status' => false,
                    'datas' => $e->getMessage() ,
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


        //==================================//
        //        Report Investment         //
        //==================================//
    public function report_investment($id){

        $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty' , 'acc_investment_details.uom', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
        ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
        ->where('acc_investments.id', '=', $id)
        ->get();

        $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
        ->where('acc_investments.id', '=', $id)
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('Legal', 'potrait');

        $pdf->loadView('accounting_purchasing.report.report_investment', array(
            'inv' => $detail_inv,
            'inv_budget' => $inv_budget
        ));

        return $pdf->stream($detail_inv[0]->reff_number. ".pdf");

            // return view('accounting_purchasing.report.report_investment', array(
            //  'inv' => $detail_inv,
            //  'inv_budget' => $inv_budget
            // ))->with('page', 'Inv')->with('head', 'Inv List');
    }

    public function investment_send_email(Request $request){
        $inv = AccInvestment::find($request->get('id'));

        try{
            if ($inv->posisi == "user")
            {
                $inv->posisi = "acc_budget";
                $inv->save();

                    //Kirim Ke Bu Laila
                $mails = "select distinct email from users where users.username = 'PI0902001'";
                $mailtoo = DB::select($mails);

                $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$inv->id;
                $invest = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($invest, 'investment'));

                $response = array(
                  'status' => true,
                  'datas' => "Berhasil"
              );

                return Response::json($response);
            }

        } catch (Exception $e) {
            $response = array(
              'status' => false,
              'datas' => "Gagal"
          );

            return Response::json($response);
        }
    }

    public function check_investment($id){

        $invest = AccInvestment::find($id);
        $investment_item = AccInvestment::join('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')->where('acc_investments.id', '=', $id)->get();

        $path = '/investment_list/' . $invest->pdf;            
        $file_path = asset($path);

        return view('accounting_purchasing.investment_check', array(
            'invest' => $invest,
            'invest_item' => $investment_item,
            'file_path' => $file_path,
        ))->with('page', 'Investment');

    }

    public function check_investment_budget(Request $request, $id){

        $invest = AccInvestment::find($id);

        if ($invest->posisi == "acc_budget")
        {

            $files = array();
            $file = new AccInvestment();
            if ($request->file('attachment') != NULL)
            {
                if ($files = $request->file('attachment'))
                {
                    foreach ($files as $file)
                    {
                        $nama = $file->getClientOriginalName();
                        $file->move('files/investment', $nama);
                        $data[] = $nama;
                    }
                }
                $file->filename = json_encode($data);
            }

                //Kirim Ke Bu Yeny
            $invest->posisi = 'acc_pajak';
            $invest->approval_acc_budget = date('Y-m-d H:i:s');
            $invest->ycj_approval = $request->get('ycj_approval');
            if ($request->file('attachment') != NULL){
                $invest->file = $file->filename;
            }
            $invest->save();

            $judul = substr($invest->reff_number, 0, 7);

            $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
            ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
            ->where('acc_investments.id', '=', $id)
            ->get();

            $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
            ->where('acc_investments.id', '=', $id)
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('Legal', 'potrait');

            $pdf->loadView('accounting_purchasing.report.report_investment', array(
                'inv' => $detail_inv,
                'inv_budget' => $inv_budget
            ));

            $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

            $mails = "select distinct email from users where users.username = 'PI9802001'";
            $mailtoo = DB::select($mails);

            $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$invest->id;
            $investe = db::select($isimail);

            Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

            return redirect('/investment/check/'.$id)->with('status', 'Investment Berhasil Dicek & Dikirim')
            ->with('page', 'Investment');
        }

        else if($invest->posisi == "acc_pajak"){
            // $invest->posisi = 'adagio';
            // $invest->status = 'adagio';
            $invest->approval_acc_pajak = date('Y-m-d H:i:s');
            if ($invest->approval_manager != null) {        
                $invest->posisi = 'manager';

                $sendemail = explode("/", $invest->approval_manager);
                $mails = "select distinct email from users where users.username = '".$sendemail[0]."'";
                $mailtoo = DB::select($mails);
            }
            else if($invest->approval_dgm != null){
                $invest->posisi = 'dgm';
                $sendemail = explode("/", $invest->approval_dgm);
                $mails = "select distinct email from users where users.username = '".$sendemail[0]."'";
                $mailtoo = DB::select($mails);
            }
            else if($invest->approval_gm != null){
                $invest->posisi = 'gm';
                $sendemail = explode("/", $invest->approval_gm);
                $mails = "select distinct email from users where users.username = '".$sendemail[0]."'";
                $mailtoo = DB::select($mails);
            }
            else{
                $invest->posisi = 'manager_acc';
                $sendemail = explode("/", $invest->approval_manager_acc);
                $mails = "select distinct email from users where users.username = '".$sendemail[0]."'";
                $mailtoo = DB::select($mails);
            }


            $invest->pkp = $request->get('pkp');
            $invest->npwp = $request->get('npwp');
            $invest->certificate = $request->get('certificate');
            $invest->total = $request->get('total');
            $invest->service = $request->get('service');
            $invest->save();


            $jumlah = $request->get('jumlahitem');
            for ($i=1; $i < (int) $jumlah; $i++) { 
                $investitem = AccInvestmentDetail::where('id', $request->get('id_item'.$i))
                ->update([
                    'vat_status' => $request->get('vat_item'.$i), 
                ]);

            }

            $judul = substr($invest->reff_number, 0, 7);

            $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
            ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
            ->where('acc_investments.id', '=', $id)
            ->get();

            $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
            ->where('acc_investments.id', '=', $id)
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('Legal', 'potrait');

            $pdf->loadView('accounting_purchasing.report.report_investment', array(
                'inv' => $detail_inv,
                'inv_budget' => $inv_budget
            ));

            $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

            $mails = "select distinct email from users where users.username = '".$invest->applicant_id."'";
            $mailcc = DB::select($mails);

            $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$invest->id;
            $investe = db::select($isimail);

            // Mail::to($mailtoo)->cc($mailcc)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

            Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

            return redirect('/investment/check/'.$id)->with('status', 'Investment Berhasil Dicek')
            ->with('page', 'Investment');
        }
    }

    public function delete_investment_budget(Request $request)
    {
        try
        {

            $get_budget_item = AccInvestmentBudget::find($request->get('id'));

            if ($get_budget_item->category_budget != "Out Of Budget") {
                $budget_log = AccBudgetHistory::where('budget', '=', $get_budget_item->budget_no)
                ->where('category_number', '=', $get_budget_item->reff_number)
                ->get();

                // $date = date('Y-m-d');
                // //FY
                // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$date'");
                // foreach ($fy as $fys) {
                //     $fiscal = $fys->fiscal_year;
                // }

                $sisa_bulan = $budget_log[0]->budget_month.'_sisa_budget';

                $budget = AccBudget::where('budget_no', $get_budget_item->budget_no)->first();

                $total = $budget->$sisa_bulan + $get_budget_item->total; //add total

                $dataupdate = AccBudget::where('budget_no', $get_budget_item->budget_no)->update([
                    $sisa_bulan => $total
                ]);

                $delete_budget_log = AccBudgetHistory::where('budget', '=', $get_budget_item->budget_no)
                ->where('category_number', '=', $get_budget_item->reff_number)
                ->delete();

                $master = AccInvestmentBudget::where('id', '=', $request->get('id'))->delete();

            }else{

                $delete_budget_log = AccBudgetHistory::where('budget', '=', $get_budget_item->budget_no)
                ->where('category_number', '=', $get_budget_item->reff_number)
                ->delete();

                $master = AccInvestmentBudget::where('id', '=', $request->get('id'))->delete();
            }


            $response = array(
              'status' => true,
              'datas' => "Berhasil Hapus Data",
          );
            
            return Response::json($response);

        }
        catch(QueryException $e)
        {
            $response = array(
              'status' => false,
              'datas' => "Berhasil Hapus Data",
          );
            
            return Response::json($response);
        }
    }

    //APPROVAL INVESTMENT

    public function investment_approvalmanager($id){
        $invest = AccInvestment::find($id);
        try{
            if ($invest->posisi == "manager")
            {

                if ($invest->applicant_department == "Human Resources Department" || $invest->applicant_department == "General Affairs Department") {
                    $invest->posisi = "gm";
                }
                else{
                    $invest->posisi = "dgm";           
                }



                $invest->approval_manager = $invest->approval_manager."/Approved/".date('Y-m-d H:i:s');
                $invest->save();         


                $judul = substr($invest->reff_number, 0, 7);

                $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
                ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('Legal', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_investment', array(
                    'inv' => $detail_inv,
                    'inv_budget' => $inv_budget
                ));

                $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

                if ($invest->approval_dgm != null) {
                    $user = explode("/", $invest->approval_dgm);
                }
                else if($invest->approval_gm != null) {
                    $user = explode("/", $invest->approval_gm);
                }


                $mails = "select distinct email from users where users.username = '".$user[0]."'";
                $mailtoo = DB::select($mails);

                $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$invest->id;
                $investe = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

                $message = 'Investment '.$invest->reff_number;
                $message2 ='Approved Successfully';
            }
            else{
                $message = 'Investment '.$invest->reff_number;
                $message2 ='Already Approved / Rejected';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    public function investment_approvaldgm($id){
        $invest = AccInvestment::find($id);
        try{
            if ($invest->posisi == "dgm")
            {

                $invest->posisi = "gm";
                $invest->approval_dgm = $invest->approval_dgm."/Approved/".date('Y-m-d H:i:s');
                $invest->save();

                $judul = substr($invest->reff_number, 0, 7);

                $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
                ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('Legal', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_investment', array(
                    'inv' => $detail_inv,
                    'inv_budget' => $inv_budget
                ));

                $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

                $user = explode("/", $invest->approval_gm);

                $mails = "select distinct email from users where users.username = '".$user[0]."'";
                $mailtoo = DB::select($mails);

                $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$invest->id;
                $investe = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

                $message = 'Investment '.$invest->reff_number;
                $message2 ='Approved Successfully';
            }
            else{
                $message = 'Investment '.$invest->reff_number;
                $message2 ='Already Approved / Rejected';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }


    public function investment_approvalgm($id){
        $invest = AccInvestment::find($id);
        try{
            if ($invest->posisi == "gm")
            {
                $invest->posisi = "manager_acc";
                $invest->approval_gm = $invest->approval_gm."/Approved/".date('Y-m-d H:i:s');
                $invest->save();

                $judul = substr($invest->reff_number, 0, 7);

                $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
                ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('Legal', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_investment', array(
                    'inv' => $detail_inv,
                    'inv_budget' => $inv_budget
                ));

                $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

                $user = explode("/", $invest->approval_manager_acc);

                $mails = "select distinct email from users where users.username = '".$user[0]."'";
                $mailtoo = DB::select($mails);

                $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$invest->id;
                $investe = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

                $message = 'Investment '.$invest->reff_number;
                $message2 ='Approved Successfully';
            }
            else{
                $message = 'Investment '.$invest->reff_number;
                $message2 ='Already Approved / Rejected';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }


    public function investment_approvalmanageracc($id){
        $invest = AccInvestment::find($id);
        try{
            if ($invest->posisi == "manager_acc")
            {
                $invest->posisi = "direktur_acc";
                $invest->approval_manager_acc = $invest->approval_manager_acc."/Approved/".date('Y-m-d H:i:s');
                $invest->save();

                $judul = substr($invest->reff_number, 0, 7);

                $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
                ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('Legal', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_investment', array(
                    'inv' => $detail_inv,
                    'inv_budget' => $inv_budget
                ));

                $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

                $user = explode("/", $invest->approval_dir_acc);

                $mails = "select distinct email from users where users.username = '".$user[0]."'";
                $mailtoo = DB::select($mails);

                $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$invest->id;
                $investe = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

                $message = 'Investment '.$invest->reff_number;
                $message2 ='Approved Successfully';
            }
            else{
                $message = 'Investment '.$invest->reff_number;
                $message2 ='Already Approved / Rejected';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    public function investment_approvaldiracc($id){
        $invest = AccInvestment::find($id);
        try{
            if ($invest->posisi == "direktur_acc")
            {
                $invest->posisi = "presdir";
                $invest->approval_dir_acc = $invest->approval_dir_acc."/Approved/".date('Y-m-d H:i:s');
                $invest->save();

                $judul = substr($invest->reff_number, 0, 7);

                $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
                ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('Legal', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_investment', array(
                    'inv' => $detail_inv,
                    'inv_budget' => $inv_budget
                ));

                $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

                $user = explode("/", $invest->approval_presdir);

                $mails = "select distinct email from users where users.username = '".$user[0]."'";
                $mailtoo = DB::select($mails);

                $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$invest->id;
                $investe = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

                $message = 'Investment '.$invest->reff_number;
                $message2 ='Approved Successfully';
            }
            else{
                $message = 'Investment '.$invest->reff_number;
                $message2 ='Already Approved / Rejected';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    public function investment_approvalpresdir($id){
        $invest = AccInvestment::find($id);
        try{
            if ($invest->posisi == "presdir")
            {
                $invest->posisi = "finished";
                $invest->approval_presdir = $invest->approval_presdir."/Approved/".date('Y-m-d H:i:s');
                $invest->save();

                $judul = substr($invest->reff_number, 0, 7);

                $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
                ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('Legal', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_investment', array(
                    'inv' => $detail_inv,
                    'inv_budget' => $inv_budget
                ));

                $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

                //kirim email ke Mas Shega & Mas Hamzah
                $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = 'Purchasing Control Department' and (employee_id = 'PI1810020'  or employee_id = 'PI0904006')";
                $mailtoo = DB::select($mails);


                $mailcc = "select distinct email from users where users.username = '".$invest->applicant_id."'";
                $mailtoocc = DB::select($mailcc);

                $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$invest->id;
                $investe = db::select($isimail);

                Mail::to($mailtoo)->cc($mailtoocc)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

                $message = 'Investment '.$invest->reff_number;
                $message2 ='Approved Successfully';
            }
            else{
                $message = 'Investment '.$invest->reff_number;
                $message2 ='Already Approved / Rejected';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $invest->reff_number,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    //comment
    public function investment_comment($id){
        $invest = AccInvestment::find($id);
        try{

            return view('accounting_purchasing.verifikasi.investment_comment', array(
                'invest' => $invest
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.investment_comment', array(
                'head' => $invest->reff_number,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }

    //comment
    public function investment_comment_msg($id){
        $invest = AccInvestment::find($id);

        return view('accounting_purchasing.verifikasi.investment_comment_msg', array(
            'invest' => $invest
        ))->with('page', 'Approval');

    }

    public function investment_comment_post(Request $request,$id)
    {

      $investment = AccInvestment::find($id);

      if ($investment->posisi != "user") {

        $comment = $request->get('question');
        $investment->comment_note = $comment;

        if ($investment->posisi == "manager") {
            $keterangan = $investment->approval_manager;
        }
        else if($investment->posisi == "dgm"){
            $keterangan = $investment->approval_dgm;
        }
        else if($investment->posisi == "gm"){
            $keterangan = $investment->approval_gm;
        }
        else if($investment->posisi == "manager_acc"){
            $keterangan = $investment->approval_manager_acc;
        }
        else if($investment->posisi == "direktur_acc"){
            $keterangan = $investment->approval_dir_acc;
        }
        else if($investment->posisi == "presdir"){
            $keterangan = $investment->approval_presdir;
        }

        $investment->comment = $investment->posisi."/".$keterangan;
        $investment->status = "comment";
        $investment->posisi = "user";

        $investment->save();

            //kirim email ke Applicant
        $mails = "select distinct email from users where users.username = '".$investment->applicant_id."'";
        $mailtoo = DB::select($mails);

        $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$id;
        $tolak = db::select($isimail);

        Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($tolak, 'investment'));

    } else if($investment->posisi == "user"){

        $investment->reply = $request->get('answer');
        $pos = explode("/", $investment->comment);
        $investment->posisi = $pos[0];

        $investment->save();

                //kirim email ke Penanya
        $mails = "select distinct email from users where users.username = '".$pos[1]."'";
        $mailtoo = DB::select($mails);


        $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$id;
        $tolak = db::select($isimail);

        Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($tolak, 'investment'));

        $investment->status = 'approval';
        $investment->save();
    }

    return redirect('/investment/comment_msg/'.$id)->with('success', 'Investment Approved')->with('page', 'Investment');
}

    //Reject Investment

public function investment_reject(Request $request, $id)
{
    $invest = AccInvestment::find($id);

    if ($invest->posisi == "manager")
    {
        $invest->posisi = "user";         
        $invest->reject = "manager/".date('Y-m-d H:i:s');
    }
    else if($invest->posisi == "dgm"){
        $invest->posisi = "user";         
        $invest->reject = "dgm/".date('Y-m-d H:i:s');

        $manager = explode("/", $invest->approval_manager);
        if(count($manager) > 1){
            $invest->approval_manager = $manager[0]."/".$manager[1];
        }
    }
    else if($invest->posisi == "gm"){
        $invest->posisi = "user";         
        $invest->reject = "gm/".date('Y-m-d H:i:s');

        $manager = explode("/", $invest->approval_manager);
        $dgm = explode("/", $invest->approval_dgm);

        if(count($manager) > 1){
            $invest->approval_manager = $manager[0]."/".$manager[1];
        }
        if(count($dgm) > 1){
            $invest->approval_dgm = $dgm[0]."/".$dgm[1];
        } 
    }
    else if($invest->posisi == "manager_acc"){
        $invest->posisi = "user";         
        $invest->reject = "manager_acc/".date('Y-m-d H:i:s');

        $manager = explode("/", $invest->approval_manager);
        $dgm = explode("/", $invest->approval_dgm);
        $gm = explode("/", $invest->approval_gm);

        if(count($manager) > 1){
            $invest->approval_manager = $manager[0]."/".$manager[1];
        }
        if(count($dgm) > 1){
            $invest->approval_dgm = $dgm[0]."/".$dgm[1];
        }
        if(count($gm) > 1){
            $invest->approval_gm = $gm[0]."/".$gm[1];
        } 
    }
    else if($invest->posisi == "direktur_acc"){
        $invest->posisi = "user";         
        $invest->reject = "direktur_acc/".date('Y-m-d H:i:s');

        $manager = explode("/", $invest->approval_manager);
        $dgm = explode("/", $invest->approval_dgm);
        $gm = explode("/", $invest->approval_gm);
        $manager_acc = explode("/", $invest->approval_manager_acc);

        if(count($manager) > 1){
            $invest->approval_manager = $manager[0]."/".$manager[1];
        }
        if(count($dgm) > 1){
            $invest->approval_dgm = $dgm[0]."/".$dgm[1];
        }
        if(count($gm) > 1){
            $invest->approval_gm = $gm[0]."/".$gm[1];
        } 
        if(count($manager_acc) > 1){
            $invest->approval_manager_acc = $manager_acc[0]."/".$manager_acc[1];
        } 
    }
    else if($invest->posisi == "presdir"){
        $invest->posisi = "user";         
        $invest->reject = "presdir/".date('Y-m-d H:i:s');

        $manager = explode("/", $invest->approval_manager);
        $dgm = explode("/", $invest->approval_dgm);
        $gm = explode("/", $invest->approval_gm);
        $manager_acc = explode("/", $invest->approval_manager_acc);
        $dir_acc = explode("/", $invest->approval_dir_acc);

        if(count($manager) > 1){
            $invest->approval_manager = $manager[0]."/".$manager[1];
        }
        if(count($dgm) > 1){
            $invest->approval_dgm = $dgm[0]."/".$dgm[1];
        }
        if(count($gm) > 1){
            $invest->approval_gm = $gm[0]."/".$gm[1];
        } 
        if(count($manager_acc) > 1){
            $invest->approval_manager_acc = $manager_acc[0]."/".$manager_acc[1];
        } 
        if(count($dir_acc) > 1){
            $invest->approval_dir_acc = $dir_acc[0]."/".$dir_acc[1];
        } 
    }

    $invest->save();

    $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$invest->id;
    $tolak = db::select($isimail);

            //kirim email ke Buyer
    $mails = "select distinct email from users where users.username = '".$invest->applicant_id."'";
    $mailtoo = DB::select($mails);

    Mail::to($mailtoo)->send(new SendEmail($tolak, 'investment'));

    $message = 'Investment '.$invest->reff_number;
    $message2 ='Rejected';

    return view('accounting_purchasing.verifikasi.pr_message', array(
        'head' => $invest->reff_number,
        'message' => $message,
        'message2' => $message2,
    ))->with('page', 'Approval');
}



        //reject_acc

public function investment_reject_acc(Request $request,$id)
{
  $reject_note = $request->get('alasan');

  $investment = AccInvestment::find($id);
  $investment->reject_note = $reject_note;

  if ($investment->posisi == "acc_budget") {
    $investment->posisi = "user";
    $investment->reject = "acc_budget/".date('Y-m-d H:i:s');
}
else if($investment->posisi == "acc_pajak"){
    $investment->posisi = "user";
    $investment->reject = "acc_pajak/".date('Y-m-d H:i:s');
}

$investment->save();

$isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$id;
$tolak = db::select($isimail);

              //kirim email ke Buyer
$mails = "select distinct email from users where users.username = '".$investment->applicant_id."'";
$mailtoo = DB::select($mails);

Mail::to($mailtoo)->send(new SendEmail($tolak, 'investment'));
return redirect('/investment/check/'.$id)->with('error', 'Investment Not Approved')->with('page', 'Investment');
}



        //ADAGIO


public function post_adagio(Request $request)
{
    try
    {
        $id_user = Auth::id();

        $files = array();
        $file = new AccInvestment();
        if ($request->file('file') != NULL)
        {
            $file = $request->file('file');
            $nama = $file->getClientOriginalName();
            $file->move('files/investment/adagio', $nama);
        }
        else
        {
            $nama = NULL;
        }

        $inv = AccInvestment::where('id', $request->get('id_edit'))
        ->update(['bukti_adagio' => $nama, 'status' => 'completed' ,'created_by' => $id_user]);


        return redirect('/investment')->with('status', 'Bukti Approval Adagio Berhasil Diunggah')
        ->with('page', 'Form Investment');


    }
    catch(QueryException $e)
    {
        return redirect('/investment')->with('error', 'Investment Gagal Dibuat')
        ->with('page', 'Form Investment');
    }
}

public function fetch_history_pembelian(Request $request){
    $history = AccPurchaseOrder::whereNull('acc_purchase_orders.deleted_at');

    if($request->get('keyword') != null){
        $history = $history->where('nama_item', 'like', '%' . $request->get('keyword') . '%');
    }
    $history = $history->join('acc_purchase_order_details','acc_purchase_orders.no_po','=','acc_purchase_order_details.no_po');
    $history = $history->select('acc_purchase_orders.supplier_name','acc_purchase_order_details.no_po','acc_purchase_order_details.nama_item','acc_purchase_order_details.goods_price','acc_purchase_order_details.service_price','acc_purchase_orders.tgl_po','acc_purchase_orders.currency');
    $history = $history->orderBy('acc_purchase_orders.tgl_po', 'desc');
    $history = $history->get();

    $response = array(
        'status' => true,
        'history' => $history
    );
    return Response::json($response);
}


        //==================================//
        //     Purchase Order Investment    //
        //==================================//
public function purchase_order_investment()
{
    $title = 'Purchase Order Investment';
    $title_jp = '投資申請に対する発注依頼';

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
    ->first();

    $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
    ->distinct()
    ->get();

    $authorized2 = EmployeeSync::select('employee_id', 'name')->where('position', '=', 'Manager')
    ->where('department', '=', 'Procurement Department')
    ->first();

    $authorized3 = EmployeeSync::select('employee_id', 'name')
    ->where('position', '=', 'General Manager')
    ->where('division', '=', 'Production Support Division')
    ->first();

    // $authorized4 = EmployeeSync::select('employee_id', 'name')->where('position', '=', 'Director')
    // ->Orwhere('position', '=', 'General Manager')
    // ->get();

    return view('accounting_purchasing.purchase_order_investment', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'employee' => $emp,
        'vendor' => $vendor,
        'delivery' => $this->delivery,
        'transportation' => $this->transportation,
        'authorized2' => $authorized2,
        'authorized3' => $authorized3,
        // 'authorized4' => $authorized4,
        'uom' => $this->uom
    ))
    ->with('page', 'Purchase Order Investment')
    ->with('head', 'Purchase Order Investment');
}

public function fetch_po_outstanding_investment(Request $request)
{
    $qry = "SELECT DISTINCT acc_investments.* FROM `acc_investments` join acc_investment_details on acc_investments.reff_number = acc_investment_details.reff_number where acc_investment_details.sudah_po is null and acc_investments.deleted_at is null and acc_investments.posisi = 'finished' and acc_investments.receive_date is not null";
    $invest = DB::select($qry);

    return DataTables::of($invest)

    ->editColumn('submission_date', function ($invest)
    {
        return date('d F Y', strtotime($invest->submission_date));
    })

    ->editColumn('file', function ($invest)
    {
        $data = json_decode($invest->file);
        $fl = "";
        if ($invest->file != null)
        {
            for ($i = 0;$i < count($data);$i++)
            {
                $fl .= '<a href="files/investment/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
            }
        }
        else
        {
            $fl = '-';
        }
        return $fl;
    })

    ->addColumn('action', function ($invest)
    {
        $id = $invest->id;

        return '
        <a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="editInvestment('.$id.')" data-toggle="tooltip" title="Edit Investment"><i class="fa fa-edit"></i> Edit</a>
        <a href="investment/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        <a href="javascript:void(0)" class="btn btn-xs  btn-primary" onClick="detailInvestment(' . $id . ')" style="margin-right:5px;" data-toggle="tooltip" title="Detail Investment"><i class="fa fa-eye"></i> Detail Item</a>
        ';
    })

    ->rawColumns(['file' => 'file', 'action' => 'action'])
    ->make(true);
}

public function fetch_investment_detail(Request $request)
{
    $investment = AccInvestment::find($request->get('id'));
    $investment_item = AccInvestment::join('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')->where('acc_investments.id', '=', $request->get('id'))
    ->get();

    $response = array(
        'status' => true,
        'investment' => $investment,
        'investment_item' => $investment_item
    );
    return Response::json($response);
}

public function fetchInvList(Request $request)
{
    $inv = AccInvestment::select('acc_investments.reff_number')->join('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
    ->whereNull('sudah_po')
    ->where('acc_investments.posisi','=','finished')
    ->distinct()
    ->get();

    $response = array(
        'status' => true,
        'investment' => $inv
    );

    return Response::json($response);
}

public function pilihInvestment(Request $request)
{
    $html = array();
    $list_item = AccInvestmentDetail::where('reff_number', $request->reff_number)
    ->whereNull('sudah_po')
    ->get();

    $lists = "<option value=''>-- Pilih Item --</option>";
    foreach ($list_item as $item)
    {
        $lists .= "<option value='".$item->no_item."'>".$item->no_item." - ".$item->detail."</option>"; 

    }
    return json_encode($lists);
}

    //==================================//
    //        Budget Information        //
    //==================================//

public function budget_info()
{
    $title = 'Budget Information';
    $title_jp = '予算情報';

    $status = AccBudget::select('*')->whereNull('acc_budgets.deleted_at')
    ->distinct()
    ->get();

    $dept = db::select("select DISTINCT department from employee_syncs");
    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('department')
    ->first();

    $fy = db::select("select distinct periode from acc_budgets");

    return view('accounting_purchasing.master.budget_info', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'emp_dept' => $emp_dept,
        'department' => $dept,
        'fy' => $fy
    ))->with('page', 'Budget Information')
    ->with('head', 'Budget Information');
}

public function fetch_budget_info(Request $request)
{

        //Get Employee Department
        // $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
        // ->select('department')
        // ->first();

        // $budget = AccBudget::orderBy('acc_budgets.budget_no', 'asc');

        // if ($request->get('periode') != null)
        // {
        //     $budget = $budget->whereIn('acc_budgets.periode', $request->get('periode'));
        // }

        // if ($request->get('category') != null)
        // {
        //     $budget = $budget->whereIn('acc_budgets.category', $request->get('category'));
        // }

        // if (Auth::user()->role_code == "MIS" || $emp_dept->department == "Accounting Department" || $emp_dept->department == "Procurement Department" || $emp_dept->department == "Purchasing Control Department") {

        // }
        // else if ($emp_dept->department == "General Affairs Department"){
        //     $budget = $budget->where('department','=','Human Resources Department');
        // }
        // else if($emp_dept->department == "Purchasing Control Department") {
        //     $budget = $budget->where('department','=','Procurement Department');
        // }
        // else {
        //     $budget = $budget->where('department','=',$emp_dept->department);
        // }

        // $budget = $budget->select('*')->get();

        // return DataTables::of($budget)

        // ->editColumn('amount', function ($budget)
        // {
        //     return '$'.$budget->amount;
        // })

        // ->addColumn('action', function ($budget)
        // {
        //     $id = $budget->id;

        //     return ' 
        //     <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$id.')"><i class="fa fa-eye"></i> Detail Sisa Budget</button>
        //     ';
        // })

        // ->rawColumns(['action' => 'action'])
        // ->make(true);

    $category = $request->get('category');

    if ($category != null)
    {
      $cattt = json_encode($category);
      $catt = str_replace(array("[","]"),array("(",")"),$cattt);

      $cat = 'and category in'.$catt;
  }
  else {
      $cat = '';
  }

  $periode = $request->get('periode');

  if ($periode != null)
  {
      $period = json_encode($periode);
      $perio = str_replace(array("[","]"),array("(",")"),$period);

      $per = 'and periode in'.$perio;
  }
  else {
      $per = '';
  }

  $bulan = $request->get('bulan');

  if ($bulan != null)
  {
      $bula = json_encode($bulan);
      $bul = str_replace(array("[","]"),array("(",")"),$bula);

      $bu = 'and budget_month in'.$bul;
  }
  else {
      $bu = '';
  }


  $department = $request->get('department');

  if ($department != null) {

      if ($department[0] == "Maintenance Department") {
        array_push($department,"Production Engineering Department");
    }

    else if ($department[0] == "Purchasing Control Department") {
        array_push($department,"Procurement Department");
    }

    else if ($department[0] == "General Affairs Department") {
        array_push($department,"Human Resources Department");
    }
    else if ($department[0] == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
        array_push($department,"Woodwind Instrument - Key Parts Process (WI-KPP) Department");
    }
    else if ($department[0] == "Woodwind Instrument - Surface Treatment (WI-ST) Department") {
        array_push($department,"Woodwind Instrument - Welding Process (WI-WP) Department");
    }


    $deptt = json_encode($department);
    $dept = str_replace(array("[","]"),array("(",")"),$deptt);

    $dep = 'and department in'.$dept;
} else {
  $dep = '';
}

        // $data = db::select('
        //     SELECT periode, budget_no, department, description, amount, account_name, category, SUM(PR) as PR, SUM(investment) as Investment, SUM(PO) as PO, SUM(Actual) as Actual FROM
        //         (
        //         SELECT
        //             periode,
        //             budget_no,
        //             department,
        //             description,
        //             amount,
        //             account_name,
        //             category,
        //             0 AS PR,
        //             0 AS Investment,
        //             0 AS PO,
        //             0 AS Actual 
        //         FROM
        //             acc_budgets 

        //         UNION ALL

        //         SELECT
        //             acc_budgets.periode,
        //             budget,
        //             acc_budgets.description,
        //             acc_budgets.department,
        //             acc_budgets.amount,
        //             acc_budgets.account_name,
        //             acc_budgets.category,
        //             sum( CASE WHEN `status` = "PR" THEN acc_budget_histories.amount ELSE 0 END ) AS PR,
        //             sum( CASE WHEN `status` = "Investment" THEN acc_budget_histories.amount ELSE 0 END ) AS Investment,
        //             sum( CASE WHEN `status` = "PO" THEN acc_budget_histories.amount_po ELSE 0 END ) AS PO,
        //             sum( CASE WHEN `status` = "Actual" THEN acc_budget_histories.amount_receive ELSE 0 END ) AS Actual 
        //         FROM
        //             acc_budget_histories
        //             JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
        //         WHERE
        //             acc_budgets.deleted_at IS NULL 
        //     GROUP BY
        //         budget) acc
        //     group by budget_no
        // ');

//         SELECT periode, budget_no, department, description, amount, account_name, category
// -- , 
// -- SUM(PR) as PR, SUM(investment) as Investment, SUM(PO) as PO, SUM(Actual) as Actual 
// FROM acc_budgets LEFT JOIN 
//     (
//     SELECT
//         budget_no,
//         0 AS PR,
//         0 AS Investment,
//         0 AS PO,
//         0 AS Actual 
//     FROM
//         acc_budgets 
//         UNION ALL
//     SELECT
//         budget,
//         sum( CASE WHEN `status` = "PR" THEN acc_budget_histories.amount ELSE 0 END ) AS PR,
//         sum( CASE WHEN `status` = "Investment" THEN acc_budget_histories.amount ELSE 0 END ) AS Investment,
//         sum( CASE WHEN `status` = "PO" THEN acc_budget_histories.amount_po ELSE 0 END ) AS PO,
//         sum( CASE WHEN `status` = "Actual" THEN acc_budget_histories.amount_receive ELSE 0 END ) AS Actual 
//     FROM
//         acc_budget_histories
//         JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
//     WHERE
//         acc_budgets.deleted_at IS NULL 
//     GROUP BY
//         budget 
//     ) 
//     AS acc
//     on acc_budgets.budget_no = acc.budget_no

// group by acc_budgets.budget_no

// SELECT periode, budget_no, department, description, amount, account_name, category FROM acc_budgets

$data = db::select('
  SELECT
  ( SELECT periode FROM acc_budgets WHERE budget_no = a.budget_no ) AS periode,
  a.budget_no,
  ( SELECT department FROM acc_budgets WHERE budget_no = a.budget_no ) AS department,
  ( SELECT description FROM acc_budgets WHERE budget_no = a.budget_no ) AS description,
  ( SELECT amount FROM acc_budgets WHERE budget_no = a.budget_no ) AS amount,
  ( SELECT account_name FROM acc_budgets WHERE budget_no = a.budget_no ) AS account_name,
  ( SELECT category FROM acc_budgets WHERE budget_no = a.budget_no ) AS category,
  ( SELECT deleted_at FROM acc_budgets WHERE budget_no = a.budget_no ) AS deleted,
  SUM( a.PR ) AS PR,
  SUM( a.investment ) AS Investment,
  SUM( a.PO ) AS PO,
  SUM( a.Transfer ) AS Transfer,
  SUM( a.actual ) AS Actual
  FROM
  (
  SELECT
  budget_no,
  0 AS PR,
  0 AS Investment,
  0 AS PO,
  0 AS Transfer,
  0 AS Actual 
  FROM
  acc_budgets 

  UNION ALL

  SELECT
  budget,
  sum( CASE WHEN `status` = "PR" THEN acc_budget_histories.amount ELSE 0 END ) AS PR,
  sum( CASE WHEN `status` = "Investment" THEN acc_budget_histories.amount ELSE 0 END ) AS Investment,
  sum( CASE WHEN `status` = "PO" THEN acc_budget_histories.amount_po ELSE 0 END ) AS PO,
  0 AS Transfer,
  sum( CASE WHEN `status` = "Actual" THEN acc_budget_histories.amount_receive ELSE 0 END ) AS Actual 
  FROM
  acc_budget_histories
  JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
  WHERE
  acc_budgets.deleted_at IS NULL
  '.$bu.'
  GROUP BY
  budget, acc_budgets.id

  UNION ALL

  SELECT
  budget_no,
  0 AS PR,
  0 AS Investment,
  0 AS PO,
  0 AS Transfer,
  SUM(local_amount) as Actual
  FROM
  acc_actual_logs 
  WHERE
  acc_actual_logs.deleted_at IS NULL
  and investment_no is null
  GROUP BY
  budget_no

  UNION ALL

  SELECT 
  budget_from,
  0 AS PR,
  0 AS Investment,
  0 AS PO,
  -SUM(amount) as Transfer,
  0 as Actual
  FROM  acc_budget_transfers
  WHERE acc_budget_transfers.deleted_at IS NULL
  and posisi = "acc"
  GROUP BY
  budget_from

  UNION ALL

  SELECT 
  budget_to,
  0 AS PR,
  0 AS Investment,
  0 AS PO,
  SUM(amount) as Transfer,
  0 as Actual
  FROM acc_budget_transfers
  WHERE acc_budget_transfers.deleted_at IS NULL
  and posisi = "acc"
  GROUP BY
  budget_to
  ) a 
  GROUP BY
  a.budget_no
  HAVING
  a.budget_no IS NOT NULL and a.budget_no <> "" and deleted IS NULL
  '.$dep.' '.$cat.' '.$per.'
  ');

$response = array(
    'status' => true,
    'datas' => $data
);

return Response::json($response); 

        // where a.deleted_at is null            
        // '.$dep.' '.$cat.' '.$per.'


}

public function budget_edit(Request $request){
    try {
        $simulasi = $request->get('simulasi');
        $sisa = $request->get('sisa');

        $simulasi_jan = $simulasi[0];
        $simulasi_feb = $simulasi[1];
        $simulasi_mar = $simulasi[2];
        $simulasi_apr = $simulasi[3];
        $simulasi_may = $simulasi[4];
        $simulasi_jun = $simulasi[5];
        $simulasi_jul = $simulasi[6];
        $simulasi_aug = $simulasi[7];
        $simulasi_sep = $simulasi[8];
        $simulasi_oct = $simulasi[9];
        $simulasi_nov = $simulasi[10];
        $simulasi_des = $simulasi[11];
        $sisa_jan = $sisa[0];
        $sisa_feb = $sisa[1];
        $sisa_mar = $sisa[2];
        $sisa_apr = $sisa[3];
        $sisa_may = $sisa[4];
        $sisa_jun = $sisa[5];
        $sisa_jul = $sisa[6];
        $sisa_aug = $sisa[7];
        $sisa_sep = $sisa[8];
        $sisa_oct = $sisa[9];
        $sisa_nov = $sisa[10];
        $sisa_des = $sisa[11];

        $amount = $simulasi[0] + $simulasi[1] + $simulasi[2] + $simulasi[3] + $simulasi[4] + $simulasi[5] + $simulasi[6] + $simulasi[7] + $simulasi[8] + $simulasi[9] + $simulasi[10] + $simulasi[11];

        $updatebudget = AccBudget::where('budget_no','=',$request->get('budget'))
        ->update([
            'amount' => $amount,
            'jan_after_adj' => $simulasi_jan,
            'feb_after_adj' => $simulasi_feb,
            'mar_after_adj' => $simulasi_mar,
            'apr_after_adj' => $simulasi_apr,
            'may_after_adj' => $simulasi_may,
            'jun_after_adj' => $simulasi_jun,
            'jul_after_adj' => $simulasi_jul,
            'aug_after_adj' => $simulasi_aug,
            'sep_after_adj' => $simulasi_sep,
            'oct_after_adj' => $simulasi_oct,
            'nov_after_adj' => $simulasi_nov,
            'dec_after_adj' => $simulasi_des,
            'jan_sisa_budget' => $sisa_jan,
            'feb_sisa_budget' => $sisa_feb,
            'mar_sisa_budget' => $sisa_mar,
            'apr_sisa_budget' => $sisa_apr,
            'may_sisa_budget' => $sisa_may,
            'jun_sisa_budget' => $sisa_jun,
            'jul_sisa_budget' => $sisa_jul,
            'aug_sisa_budget' => $sisa_aug,
            'sep_sisa_budget' => $sisa_sep,
            'oct_sisa_budget' => $sisa_oct,
            'nov_sisa_budget' => $sisa_nov,
            'dec_sisa_budget' => $sisa_des
        ]);

        $response = array(
            'status' => true,
            'datas' => 'Berhasil Update Data',
        );
        return Response::json($response);
    } catch (QueryException $e){
       $error_code = $e->errorInfo[1];
       if($error_code == 1062){
        $response = array(
         'status' => false,
         'datas' => 'Errpr'
     );
        return Response::json($response);
    }
    else{
        $response = array(
         'status' => false,
         'datas' => $e->getMessage()
     );
        return Response::json($response);
    }
}
}

public function budget_log()
{
    $title = 'Budget Log';
    $title_jp = '予算台帳';

    $data = AccBudgetHistory::select('*')->whereNull('acc_budget_histories.deleted_at')
    ->distinct()
    ->get();

    $status = AccBudgetHistory::select('status')->whereNull('acc_budget_histories.deleted_at')
    ->distinct()
    ->get();

    return view('accounting_purchasing.master.budget_log', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'status' => $status
    ))->with('page', 'Budget Log')
    ->with('head', 'Budget Log');
}

public function fetch_budget_log(Request $request)
{
    $budgetlog = AccBudgetHistory::where('id','>','3135')->orderBy('acc_budget_histories.id', 'desc');

    if ($request->get('status') != null)
    {
        $budgetlog = $budgetlog->whereIn('acc_budget_histories.status', $request->get('status'));
    }

    $budgetlog = $budgetlog->select('*')
    ->get();

    return DataTables::of($budgetlog)

    ->editColumn('budget_month', function ($budgetlog)
    {
        if ($budgetlog->status == "PR" || $budgetlog->status == "Investment") {
            return $budgetlog->budget_month;           
        }
        else if ($budgetlog->status == "PO") {
            return $budgetlog->budget_month. ' - ' .$budgetlog->budget_month_po;           
        }
        else if ($budgetlog->status == "Actual") {
            return $budgetlog->budget_month. ' - ' .$budgetlog->budget_month_po. ' - ' .$budgetlog->budget_month_receive;           
        }

    })

    ->addColumn('action', function ($budgetlog)
    {
        $id = $budgetlog->id;
        if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "ACC" || Auth::user()->role_code == "ACC-SPL") {
            return ' 
            <a href="" class="btn btn-warning btn-xs"><i class="fa fa-eye"></i></a> 
            ';
        }else{
            return '-';       
        }
    })
    ->rawColumns(['budget_month' => 'budget_month','action' => 'action'])

    ->make(true);
}

public function budget_detail(Request $request)
{
    $detail = AccBudget::where('budget_no','=',$request->get('id'))->first();

    $detail_penggunaan = db::select('
        SELECT
        CASE    
        WHEN
        a.bulan = "Jan" THEN
        13 
        WHEN a.bulan = "Feb" THEN
        14 
        WHEN a.bulan = "Mar" THEN
        15 
        WHEN a.bulan = "Apr" THEN
        4 
        WHEN a.bulan = "May" THEN
        5 
        WHEN a.bulan = "Jun" THEN
        6 
        WHEN a.bulan = "Jul" THEN
        7 
        WHEN a.bulan = "Aug" THEN
        8 
        WHEN a.bulan = "Sep" THEN
        9 
        WHEN a.bulan = "Oct" THEN
        10 
        WHEN a.bulan = "Nov" THEN
        11 
        WHEN a.bulan = "Dec" THEN
        12 
        END AS month_number,
        a.bulan,
        ROUND( SUM( a.actual ), 2 ) AS Actual,
        ROUND( SUM( a.PR ), 2 ) AS PR,
        ROUND( SUM( a.Investment ), 2 ) AS Investment,
        ROUND( SUM( a.PO ), 2 ) AS PO
        FROM
        (
        SELECT
        budget_month_receive as bulan,
        ROUND( sum( CASE WHEN `status` = "Actual" THEN acc_budget_histories.amount_receive ELSE 0 END ), 2 ) AS Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets on acc_budgets.budget_no = acc_budget_histories.budget 
        WHERE
        budget_month_receive IS NOT NULL 
        AND budget_no = "'.$request->get('id').'"
        GROUP BY
        budget_month_receive 
        
        UNION ALL
        
        SELECT
        month_date AS bulan,
        ROUND( SUM( local_amount ), 2 ) AS Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_actual_logs
        LEFT JOIN acc_budgets on acc_budgets.budget_no = acc_actual_logs.budget_no 
        WHERE
        acc_actual_logs.deleted_at IS NULL 
        AND acc_budgets.budget_no = "'.$request->get('id').'" 
        AND acc_actual_logs.investment_no is null
        GROUP BY
        month_date 
        
        UNION ALL
        
        SELECT 
        budget_month AS bulan,
        0 AS Actual,            
        ROUND( sum( CASE WHEN `status` = "PR" THEN acc_budget_histories.amount ELSE 0 END ), 2 ) AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets on acc_budgets.budget_no = acc_budget_histories.budget 
        WHERE
        budget_month IS NOT NULL 
        AND budget_no = "'.$request->get('id').'"
        GROUP BY
        budget_month
        
        UNION ALL
        
        SELECT 
        budget_month AS bulan,
        0 AS Actual,            
        0 AS PR,
        ROUND( sum( CASE WHEN `status` = "Investment" THEN acc_budget_histories.amount ELSE 0 END ), 2 ) AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets on acc_budgets.budget_no = acc_budget_histories.budget 
        WHERE
        budget_month IS NOT NULL 
        AND budget_no = "'.$request->get('id').'"
        GROUP BY
        budget_month
        
        UNION ALL
        
        SELECT 
        budget_month_po AS bulan,
        0 AS Actual,            
        0 AS PR,
        0 AS Investment,
        ROUND( sum( CASE WHEN `status` = "PO" and budget_month_po = budget_month THEN acc_budget_histories.amount_po ELSE 0 END ), 2 ) AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets on acc_budgets.budget_no = acc_budget_histories.budget 
        WHERE
        budget_month IS NOT NULL 
        AND budget_no = "'.$request->get('id').'"
        GROUP BY
        budget_month_po
        ) a 
        GROUP BY
        a.bulan 
        HAVING
        a.bulan IS NOT NULL 
        ORDER BY
        month_number
        ');

    $response = array(
        'status' => true,
        'datas' => $detail,
        'data_penggunaan' => $detail_penggunaan
    );

    return Response::json($response);
}

public function import_budget(Request $request){
    if($request->hasFile('upload_file')) {
        try{                
            $file = $request->file('upload_file');
            $file_name = 'budget'.'('. date("ymd_h.i") .')'.'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/budget/'), $file_name);
            $excel = public_path('uploads/budget/') . $file_name;

            $rows = Excel::load($excel, function($reader) {
                $reader->noHeading();
                        //Skip Header
                $reader->skipRows(1);
            })->get();

            $rows = $rows->toArray();

            for ($i=0; $i < count($rows); $i++) {
                if ($rows[$i][0] != "") {
                    $periode = $rows[$i][0];
                    $budget_no = $rows[$i][1];
                    $department = $rows[$i][2];
                    $description = $rows[$i][3];
                    $amount = $rows[$i][4];
                    $env = $rows[$i][5];
                    $purpose = $rows[$i][6];
                    $pic = $rows[$i][7];
                    $account = $rows[$i][8];
                    $category = $rows[$i][9];
                    $apr_awal = $rows[$i][10];
                    $may_awal = $rows[$i][11];
                    $jun_awal = $rows[$i][12];
                    $jul_awal = $rows[$i][13];
                    $aug_awal = $rows[$i][14];
                    $sep_awal = $rows[$i][15];
                    $oct_awal = $rows[$i][16];
                    $nov_awal = $rows[$i][17];
                    $dec_awal = $rows[$i][18];
                    $jan_awal = $rows[$i][19];
                    $feb_awal = $rows[$i][20];
                    $mar_awal = $rows[$i][21];
                    $adj_frc = $rows[$i][22];
                    $apr_adj = $rows[$i][23];
                    $may_adj = $rows[$i][24];
                    $jun_adj = $rows[$i][25];
                    $jul_adj = $rows[$i][26];
                    $aug_adj = $rows[$i][27];
                    $sep_adj = $rows[$i][28];
                    $oct_adj = $rows[$i][29];
                    $nov_adj = $rows[$i][30];
                    $dec_adj = $rows[$i][31];
                    $jan_adj = $rows[$i][32];
                    $feb_adj = $rows[$i][33];
                    $mar_adj = $rows[$i][34];
                    $apr_sisa = $rows[$i][35];
                    $may_sisa = $rows[$i][36];
                    $jun_sisa = $rows[$i][37];
                    $jul_sisa = $rows[$i][38];
                    $aug_sisa = $rows[$i][39];
                    $sep_sisa = $rows[$i][40];
                    $oct_sisa = $rows[$i][41];
                    $nov_sisa = $rows[$i][42];
                    $dec_sisa = $rows[$i][43];
                    $jan_sisa = $rows[$i][44];
                    $feb_sisa = $rows[$i][45];
                    $mar_sisa = $rows[$i][46];

                    $data2 = AccBudget::firstOrNew(['periode' => $periode, 'budget_no' => $budget_no]);
                    $data2->department = $department;
                    $data2->description = $description;
                    $data2->amount = $amount;
                    $data2->env = $env;
                    $data2->purpose = $purpose;
                    $data2->pic = $pic;
                    $data2->account_name = $account;
                    $data2->category = $category;
                    // $data2->apr_budget_awal = $apr_awal;
                    // $data2->may_budget_awal = $may_awal;
                    // $data2->jun_budget_awal = $jun_awal;
                    // $data2->jul_budget_awal = $jul_awal;
                    // $data2->aug_budget_awal = $aug_awal;
                    // $data2->sep_budget_awal = $sep_awal;
                    // $data2->oct_budget_awal = $oct_awal;
                    // $data2->nov_budget_awal = $nov_awal;
                    // $data2->dec_budget_awal = $dec_awal;
                    // $data2->jan_budget_awal = $jan_awal;
                    // $data2->feb_budget_awal = $feb_awal;
                    // $data2->mar_budget_awal = $mar_awal;
                    // $data2->adj_frc = $adj_frc;
                    $data2->apr_after_adj = $apr_adj;
                    $data2->may_after_adj = $may_adj;
                    $data2->jun_after_adj = $jun_adj;
                    $data2->jul_after_adj = $jul_adj;
                    $data2->aug_after_adj = $aug_adj;
                    $data2->sep_after_adj = $sep_adj;
                    $data2->oct_after_adj = $oct_adj;
                    $data2->nov_after_adj = $nov_adj;
                    $data2->dec_after_adj = $dec_adj;
                    $data2->jan_after_adj = $jan_adj;
                    $data2->feb_after_adj = $feb_adj;
                    $data2->mar_after_adj = $mar_adj;
                    // $data2->apr_sisa_budget = $apr_sisa;
                    // $data2->may_sisa_budget = $may_sisa;
                    $data2->jun_sisa_budget = $jun_sisa;
                    $data2->jul_sisa_budget = $jul_sisa;
                    $data2->aug_sisa_budget = $aug_sisa;
                    $data2->sep_sisa_budget = $sep_sisa;
                    $data2->oct_sisa_budget = $oct_sisa;
                    $data2->nov_sisa_budget = $nov_sisa;
                    $data2->dec_sisa_budget = $dec_sisa;
                    $data2->jan_sisa_budget = $jan_sisa;
                    $data2->feb_sisa_budget = $feb_sisa;    
                    $data2->mar_sisa_budget = $mar_sisa;
                    $data2->created_by = Auth::id();
                    $data2->save();
                }
                
            }       

            $response = array(
                'status' => true,
                'message' => 'Upload file success',
            );
            return Response::json($response);

        }catch(\Exception $e){
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }else{
        $response = array(
            'status' => false,
            'message' => 'Upload failed, File not found',
        );
        return Response::json($response);
    }
}

public function budget_control()
{
        // $title = 'Budget Report & Control';
        // $     = '予算報告・管理';

    $title = 'Budget Summary';
    $title_jp = '予算要約';

    $dept = db::select("select DISTINCT department from employee_syncs");
    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('department')
    ->first();

    return view('accounting_purchasing.display.budget_summary', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'emp_dept' => $emp_dept,
        'department' => $dept
    ))->with('page', 'Budget Summary')
    ->with('head', 'Budget Summary');
}

public function fetch_budget_table(Request $request)
{

  $datefrom = $request->get('datefrom');
  $dateto = $request->get('dateto');

  if ($datefrom == "") {
      $date = ''; 
  }

  if($datefrom != ""){

      if ($dateto != "") {
          $date = 'and DATE_FORMAT(budget_date,"%Y-%m") between '.$datefrom.' and '.$dateto.''; 
      }

      $date = 'and DATE_FORMAT(budget_date,"%Y-%m") = "'.$datefrom.'"'; 
  }

  $department = $request->get('department');

  if ($department != null) {
      $deptt = json_encode($department);
      $dept = str_replace(array("[","]"),array("(",")"),$deptt);

      $dep = 'and acc_budgets.department in'.$dept;
  } else {
      $dep = '';
  }

  $data = db::select('
    SELECT
    budget,
    acc_budgets.amount,
    acc_budgets.description,
    acc_budgets.department,
    sum(CASE WHEN `status` = "PR" THEN acc_budget_histories.amount ELSE 0 END ) AS PR,
    sum(CASE WHEN `status` = "Investment" THEN acc_budget_histories.amount ELSE 0 END ) AS Investment,
    sum(CASE WHEN `status` = "PO" THEN acc_budget_histories.amount_po ELSE 0 END ) AS PO,
    sum(CASE WHEN `status` = "Actual" THEN acc_budget_histories.amount_receive ELSE 0 END ) AS Actual
    FROM
    acc_budget_histories 
    JOIN 
    acc_budgets
    ON 
    acc_budget_histories.budget = acc_budgets.budget_no
    WHERE
    acc_budgets.deleted_at IS NULL '.$date.' '.$dep.'
    GROUP BY
    budget,amount,description,department
    ');

  $response = array(
    'status' => true,
    'datas' => $data
);

  return Response::json($response); 
}

public function fetch_budget_summary(Request $request)
{

    $category = $request->get('category');

    if ($category != null) {
        $cattt = json_encode($category);
        $catt = str_replace(array("[","]"),array("(",")"),$cattt);

        $cat = 'and acc_budgets.category in '.$catt;
    } else {
        $cat = '';
    }

    $tglnow = date('Y-m-d');
    $tahun = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");


    $fy = $request->get('fy');

    if ($fy != null) {
        $fiscal_year = json_encode($fy);
        $fiscal_y = str_replace(array("[","]"),array("(",")"),$fiscal_year);

        $fiscal = 'and acc_budgets.periode in '.$fiscal_y;
    } else {
        $fiscal = 'and acc_budgets.periode = "'.$tahun[0]->fiscal_year.'"';
    }

    $resume = db::select('
        SELECT
        periode,
        SUM( apr_after_adj ) AS apr_simulasi,
        SUM( may_after_adj ) AS may_simulasi,
        SUM( jun_after_adj ) AS jun_simulasi,
        SUM( jul_after_adj ) AS jul_simulasi,
        SUM( aug_after_adj ) AS aug_simulasi,
        SUM( sep_after_adj ) AS sep_simulasi,
        SUM( oct_after_adj ) AS oct_simulasi,
        SUM( nov_after_adj ) AS nov_simulasi,
        SUM( dec_after_adj ) AS dec_simulasi,
        SUM( jan_after_adj ) AS jan_simulasi,
        SUM( feb_after_adj ) AS feb_simulasi,
        SUM( mar_after_adj ) AS mar_simulasi
        FROM
        acc_budgets
        where acc_budgets.deleted_at is null
        '.$fiscal.'
        '.$cat.'
        GROUP BY periode
        ');

    
// SELECT
//     DATE_FORMAT( receive_date, '%b' ) AS bulan,
//     ROUND( sum( acc_actuals.amount_dollar ), 2 ) AS Actual,
//     0 AS PR,
//     0 AS Investment,
//     0 AS PO 
// FROM
//     acc_actuals
//     JOIN acc_purchase_order_details ON RIGHT ( CONCAT( SPLIT_STRING ( acc_purchase_order_details.no_po, '-', 1 ), SPLIT_STRING(acc_purchase_order_details.no_po, '-', 2 )), 10 ) = acc_actuals.no_po 
//     AND acc_purchase_order_details.no_item = acc_actuals.item_no 
// WHERE
//     DATE_FORMAT( receive_date, '%b' ) IS NOT NULL 
//     AND budget_item IN ( SELECT budget_no FROM acc_budgets WHERE category = "Expenses" ) 
// GROUP BY
//     DATE_FORMAT(receive_date,'%b')

// SELECT
//     budget_month_receive AS bulan,
//     ROUND( sum( CASE WHEN `status` = "Actual" THEN acc_budget_histories.amount_receive ELSE 0 END ), 2 ) AS Actual,         
//     0 AS PR,
//     0 AS Investment,
//     0 AS PO
// FROM
//     acc_budget_histories
//     LEFT JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
// WHERE
//     budget_month_receive IS NOT NULL 
//     '.$fiscal.'
//     '.$cat.' 
// GROUP BY
//     budget_month_receive 

    $act = db::select('
        SELECT
        CASE    
        WHEN
        a.bulan = "Jan" THEN
        13 
        WHEN a.bulan = "Feb" THEN
        14 
        WHEN a.bulan = "Mar" THEN
        15 
        WHEN a.bulan = "Apr" THEN
        4 
        WHEN a.bulan = "May" THEN
        5 
        WHEN a.bulan = "Jun" THEN
        6 
        WHEN a.bulan = "Jul" THEN
        7 
        WHEN a.bulan = "Aug" THEN
        8 
        WHEN a.bulan = "Sep" THEN
        9 
        WHEN a.bulan = "Oct" THEN
        10 
        WHEN a.bulan = "Nov" THEN
        11 
        WHEN a.bulan = "Dec" THEN
        12 
        END AS month_number,
        a.bulan,
        ROUND( SUM( a.actual ), 2 ) AS Actual,
        ROUND( SUM( a.PR ), 2 ) AS PR,
        ROUND( SUM( a.Investment ), 2 ) AS Investment,
        ROUND( SUM( a.PO ), 2 ) AS PO
        FROM
        (
        SELECT
        DATE_FORMAT( receive_date, "%b" ) AS bulan,
        ROUND( sum( acc_actuals.amount_dollar ), 2 ) AS Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO 
        FROM
        acc_actuals
        LEFT JOIN acc_budgets ON acc_actuals.budget_no = acc_budgets.budget_no 
        WHERE
        DATE_FORMAT( receive_date, "%b" ) IS NOT NULL 
        '.$fiscal.'
        '.$cat.'   
        GROUP BY
        DATE_FORMAT( receive_date, "%b" )
        
        UNION ALL
        
        SELECT
        month_date AS bulan,
        ROUND( SUM( local_amount ), 2 ) AS Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_actual_logs
        LEFT JOIN acc_budgets ON acc_actual_logs.budget_no = acc_budgets.budget_no 
        WHERE
        acc_actual_logs.deleted_at IS NULL 
        '.$fiscal.'
        '.$cat.'   
        GROUP BY
        month_date 
        
        UNION ALL
        
        SELECT 
        budget_month AS bulan,
        0 AS Actual,            
        ROUND( sum( CASE WHEN `status` = "PR" THEN acc_budget_histories.amount ELSE 0 END ), 2 ) AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
        WHERE
        budget_month IS NOT NULL 
        '.$fiscal.'
        '.$cat.'  
        GROUP BY
        budget_month
        
        UNION ALL
        
        SELECT 
        budget_month AS bulan,
        0 AS Actual,            
        0 AS PR,
        ROUND( sum( CASE WHEN `status` = "Investment" THEN acc_budget_histories.amount ELSE 0 END ), 2 ) AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
        WHERE
        budget_month IS NOT NULL 
        '.$fiscal.'
        '.$cat.'  
        GROUP BY
        budget_month
        
        UNION ALL
        
        SELECT 
        budget_month_po AS bulan,
        0 AS Actual,            
        0 AS PR,
        0 AS Investment,
        ROUND( sum( CASE WHEN `status` = "PO" THEN acc_budget_histories.amount_po ELSE 0 END ), 2 ) AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
        WHERE
        budget_month_po IS NOT NULL 
        '.$fiscal.'
        '.$cat.'  
        GROUP BY
        budget_month_po
        
        ) a 
        GROUP BY
        a.bulan 
        HAVING
        a.bulan IS NOT NULL 
        ORDER BY
        month_number
        ');

    $category = db::select('
        SELECT
        category,
        sum(amount) AS amount
        FROM
        acc_budgets
        WHERE
        acc_budgets.deleted_at IS NULL
        '.$fiscal.'
        GROUP BY
        category
        ');

    $act_category = db::select('
        SELECT
        category,
        ROUND( SUM( a.actual ), 2 ) AS Actual,
        ROUND( SUM( a.PR ), 2 ) AS PR,
        ROUND( SUM( a.Investment ), 2 ) AS Investment,
        ROUND( SUM( a.PO ), 2 ) AS PO
        FROM
        (
        SELECT
        category,
        ROUND( sum( CASE WHEN `status` = "Actual" THEN acc_budget_histories.amount_receive ELSE 0 END ), 2 ) AS Actual,   
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN   acc_budgets ON acc_budgets.budget_no = acc_budget_histories.budget
        WHERE
        category is not null
        '.$fiscal.'
        GROUP BY
        category 
        
        UNION ALL
        
        SELECT
        category,
        ROUND( SUM( local_amount ), 2 ) AS Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_actual_logs
        LEFT JOIN acc_budgets ON acc_actual_logs.budget_no = acc_budgets.budget_no 
        WHERE
        acc_actual_logs.deleted_at IS NULL 
        '.$fiscal.'
        GROUP BY
        category
        
        UNION ALL
        
        SELECT 
        category,
        0 AS Actual,            
        ROUND( sum( CASE WHEN `status` = "PR" THEN acc_budget_histories.amount ELSE 0 END ), 2 ) AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN   acc_budgets ON acc_budgets.budget_no = acc_budget_histories.budget
        WHERE
        category is not null
        '.$fiscal.'
        GROUP BY
        category
        
        UNION ALL
        
        SELECT 
        category,
        0 AS Actual,            
        0 AS PR,
        ROUND( sum( CASE WHEN `status` = "Investment" THEN acc_budget_histories.amount ELSE 0 END ), 2 ) AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN   acc_budgets ON acc_budgets.budget_no = acc_budget_histories.budget
        WHERE
        category is not null
        '.$fiscal.'
        GROUP BY
        category
        
        UNION ALL
        
        SELECT 
        category,
        0 AS Actual,            
        0 AS PR,
        0 AS Investment,
        ROUND( sum( CASE WHEN `status` = "PO" THEN acc_budget_histories.amount_po ELSE 0 END ), 2 ) AS PO
        FROM
        acc_budget_histories
        LEFT JOIN   acc_budgets ON acc_budgets.budget_no = acc_budget_histories.budget
        WHERE
        category is not null
        '.$fiscal.' 
        GROUP BY
        category
        
        ) a 
        GROUP BY
        a.category 
        HAVING
        a.category IS NOT NULL 

        ');

    // $type = db::select('
    //     SELECT
    //     account_name,
    //     sum(amount) AS amount
    //     FROM
    //     acc_budgets
    //     WHERE
    //     acc_budgets.deleted_at IS NULL
    //     GROUP BY
    //     account_name
    //     ');

    $response = array(
        'status' => true,
        'cat_budget' => $category,
        'resume' => $resume,
        'act' => $act,
        'act_category' => $act_category
    );

    return Response::json($response); 
}

public function budget_monthly()
{
    $title = 'Budget Monthly';
    $title_jp = '月次予算';

    $dept = db::select("select DISTINCT department from employee_syncs");

    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('department')
    ->first();

    return view('accounting_purchasing.display.budget_monthly', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'emp_dept' => $emp_dept,
        'department' => $dept
    ))->with('page', 'Budget Monthly')
    ->with('head', 'Budget Monthly');
}


public function fetch_budget_monthly(Request $request)
{

    $category = $request->get('category');

    if ($category != null) {
        $cattt = json_encode($category);
        $catt = str_replace(array("[","]"),array("(",")"),$cattt);

        $cat = 'and acc_budgets.category in '.$catt;
    } else {
        $cat = '';
    }

    $tglnow = date('Y-m-d');
    $tahun = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");


    $fy = $request->get('fy');

    if ($fy != null) {
        $fiscal_year = json_encode($fy);
        $fiscal_y = str_replace(array("[","]"),array("(",")"),$fiscal_year);

        $fiscal = 'and acc_budgets.periode in '.$fiscal_y;
    } else {
        $fiscal = 'and acc_budgets.periode = "'.$tahun[0]->fiscal_year.'"';
    }

    $department = $request->get('department');

    if ($department != null) {
        $dept = 'and acc_budgets.department = "'.$department.'"';
    } else {
        $dept = '';
    }

    $resume = db::select('
        SELECT
        periode,
        SUM( apr_after_adj ) AS apr_simulasi,
        SUM( may_after_adj ) AS may_simulasi,
        SUM( jun_after_adj ) AS jun_simulasi,
        SUM( jul_after_adj ) AS jul_simulasi,
        SUM( aug_after_adj ) AS aug_simulasi,
        SUM( sep_after_adj ) AS sep_simulasi,
        SUM( oct_after_adj ) AS oct_simulasi,
        SUM( nov_after_adj ) AS nov_simulasi,
        SUM( dec_after_adj ) AS dec_simulasi,
        SUM( jan_after_adj ) AS jan_simulasi,
        SUM( feb_after_adj ) AS feb_simulasi,
        SUM( mar_after_adj ) AS mar_simulasi
        FROM
        acc_budgets
        where acc_budgets.deleted_at is null
        '.$fiscal.'
        '.$cat.'
        '.$dept.'
        GROUP BY periode
        ');

    $act = db::select('
        SELECT
        CASE    
        WHEN
        a.bulan = "Jan" THEN
        13 
        WHEN a.bulan = "Feb" THEN
        14 
        WHEN a.bulan = "Mar" THEN
        15 
        WHEN a.bulan = "Apr" THEN
        4 
        WHEN a.bulan = "May" THEN
        5 
        WHEN a.bulan = "Jun" THEN
        6 
        WHEN a.bulan = "Jul" THEN
        7 
        WHEN a.bulan = "Aug" THEN
        8 
        WHEN a.bulan = "Sep" THEN
        9 
        WHEN a.bulan = "Oct" THEN
        10 
        WHEN a.bulan = "Nov" THEN
        11 
        WHEN a.bulan = "Dec" THEN
        12 
        END AS month_number,
        a.bulan,
        ROUND( SUM( a.actual ), 2 ) AS Actual,
        ROUND( SUM( a.PR ), 2 ) AS PR,
        ROUND( SUM( a.Investment ), 2 ) AS Investment,
        ROUND( SUM( a.PO ), 2 ) AS PO
        FROM
        (

        SELECT
        DATE_FORMAT( receive_date, "%b" ) AS bulan,
        ROUND( sum( acc_actuals.amount_dollar ), 2 ) AS Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO 
        FROM
        acc_actuals
        LEFT JOIN acc_budgets ON acc_actuals.budget_no = acc_budgets.budget_no 
        WHERE
        DATE_FORMAT( receive_date, "%b" ) IS NOT NULL 
        '.$fiscal.'
        '.$cat.' 
        '.$dept.'
        GROUP BY
        DATE_FORMAT( receive_date, "%b" )
        
        UNION ALL
        
        SELECT
        month_date AS bulan,
        ROUND( SUM( local_amount ), 2 ) AS Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_actual_logs
        LEFT JOIN acc_budgets ON acc_actual_logs.budget_no = acc_budgets.budget_no 
        WHERE
        acc_actual_logs.deleted_at IS NULL 
        '.$fiscal.'
        '.$cat.'   
        '.$dept.'
        GROUP BY
        month_date 
        
        UNION ALL
        
        SELECT 
        budget_month AS bulan,
        0 AS Actual,            
        ROUND( sum( CASE WHEN `status` = "PR" THEN acc_budget_histories.amount ELSE 0 END ), 2 ) AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
        WHERE
        budget_month IS NOT NULL 
        '.$fiscal.'
        '.$cat.'  
        '.$dept.'
        GROUP BY
        budget_month
        
        UNION ALL
        
        SELECT 
        budget_month AS bulan,
        0 AS Actual,            
        0 AS PR,
        ROUND( sum( CASE WHEN `status` = "Investment" THEN acc_budget_histories.amount ELSE 0 END ), 2 ) AS Investment,
        0 AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
        WHERE
        budget_month IS NOT NULL 
        '.$fiscal.'
        '.$cat.'  
        '.$dept.'
        GROUP BY
        budget_month
        
        UNION ALL
        
        SELECT 
        budget_month_po AS bulan,
        0 AS Actual,            
        0 AS PR,
        0 AS Investment,
        ROUND( sum( CASE WHEN `status` = "PO" THEN acc_budget_histories.amount_po ELSE 0 END ), 2 ) AS PO
        FROM
        acc_budget_histories
        LEFT JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
        WHERE
        budget_month_po IS NOT NULL 
        '.$fiscal.'
        '.$cat.'  
        '.$dept.'
        GROUP BY
        budget_month_po
        
        ) a 
        WHERE a.Actual != 0 or a.PR != 0 or a.PO != 0 or a.Investment != 0
        GROUP BY
        a.bulan 
        HAVING
        a.bulan IS NOT NULL 
        ORDER BY
        month_number
        ');

    $response = array(
        'status' => true,
        'resume' => $resume,
        'act' => $act
    );

    return Response::json($response); 
}


public function fetch_budget_monthly_table(Request $request)
{

    $category = $request->get('category');

    if ($category != null) {
        $cattt = json_encode($category);
        $catt = str_replace(array("[","]"),array("(",")"),$cattt);

        $cat = 'and acc_budgets.category in '.$catt;
    } else {
        $cat = '';
    }

    $tglnow = date('Y-m-d');
    $tahun = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");


    $fy = $request->get('fy');

    if ($fy != null) {
        $fiscal_year = json_encode($fy);
        $fiscal_y = str_replace(array("[","]"),array("(",")"),$fiscal_year);

        $fiscal = 'and acc_budgets.periode in '.$fiscal_y;
    } else {
        $fiscal = 'and acc_budgets.periode = "'.$tahun[0]->fiscal_year.'"';
    }

    $department = $request->get('department');

    if ($department != null) {
        $dept = 'and acc_budgets.department = "'.$department.'"';
    } else {
        $dept = '';
    }

    $data = db::select('select * from acc_budgets where acc_budgets.deleted_at is null '.$fiscal.' '.$cat.' '.$dept);

    $act = db::select('
        SELECT
        CASE    
        WHEN
        a.bulan = "Jan" THEN
        13 
        WHEN a.bulan = "Feb" THEN
        14 
        WHEN a.bulan = "Mar" THEN
        15 
        WHEN a.bulan = "Apr" THEN
        4 
        WHEN a.bulan = "May" THEN
        5 
        WHEN a.bulan = "Jun" THEN
        6 
        WHEN a.bulan = "Jul" THEN
        7 
        WHEN a.bulan = "Aug" THEN
        8 
        WHEN a.bulan = "Sep" THEN
        9 
        WHEN a.bulan = "Oct" THEN
        10 
        WHEN a.bulan = "Nov" THEN
        11 
        WHEN a.bulan = "Dec" THEN
        12 
        END AS month_number,
        a.bulan,
        a.budget_no,
        ROUND( SUM( a.actual ), 2 ) AS Actual
        FROM
        (

        SELECT
        DATE_FORMAT(receive_date, "%b") AS bulan,
        acc_actuals.budget_no,
        ROUND( SUM( amount_dollar ), 2 ) AS Actual,       
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_actuals
        LEFT JOIN acc_budgets ON acc_actuals.budget_no = acc_budgets.budget_no 
        WHERE
        DATE_FORMAT(receive_date, "%b") IS NOT NULL
        '.$fiscal.' '.$cat.' '.$dept.'
        GROUP BY
        DATE_FORMAT(receive_date, "%b"), budget_no

        
        UNION ALL
        
        SELECT
        month_date AS bulan,
        acc_actual_logs.budget_no,
        ROUND( SUM( local_amount ), 2 ) AS Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_actual_logs
        LEFT JOIN acc_budgets ON acc_actual_logs.budget_no = acc_budgets.budget_no 
        WHERE
        acc_actual_logs.deleted_at IS NULL
        '.$fiscal.' '.$cat.' '.$dept.'
        GROUP BY
        month_date, budget_no

        UNION ALL
        
        SELECT
        "Apr" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets

        UNION ALL
        
        SELECT
        "May" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Jun" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Jul" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Aug" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Sep" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Oct" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Nov" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Dec" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Jan" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Feb" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets
        
        UNION ALL
        
        SELECT
        "Mar" as bulan,
        acc_budgets.budget_no,
        0 as Actual,
        0 AS PR,
        0 AS Investment,
        0 AS PO
        FROM
        acc_budgets

        ) a 
        GROUP BY
        a.bulan, a.budget_no
        HAVING
        a.bulan IS NOT NULL and a.budget_no IS NOT NULL
        ORDER BY
        budget_no, month_number
        ');

    //actual 

    // SELECT
    // budget_month_receive AS bulan,
    // budget_no,
    // ROUND( sum( CASE WHEN `status` = "Actual" THEN acc_budget_histories.amount_receive ELSE 0 END ), 2 ) AS Actual,         
    // 0 AS PR,
    // 0 AS Investment,
    // 0 AS PO
    // FROM
    // acc_budget_histories
    // LEFT JOIN acc_budgets ON acc_budget_histories.budget = acc_budgets.budget_no 
    // WHERE
    // budget_month_receive IS NOT NULL
    // GROUP BY
    // budget_month_receive, budget_no

$response = array(
    'status' => true,
    'datas' => $data,
    'actual' => $act
);

return Response::json($response); 
}

public function fetch_budget_detail(Request $request){
    $budget = $request->get("budget");
    $status = $request->get("status");


        // $qry = "select acc_budget_histories.* from acc_budgets
        // left join acc_budget_histories on acc_budget_histories.budget = acc_budgets.budget_no
        // left join acc_actual_logs on acc_actual_logs.budget_no = acc_budgets.budget_no
        // where budget = '".$budget."' and status='".$status."'";

    if ($status == "Transfer") {
        $qry = "
        select *, 'Transfer' as status from acc_budget_transfers
        where acc_budget_transfers.budget_from = '".$budget."' or acc_budget_transfers.budget_to = '".$budget."'  ";
    }
    else if($status == "Actual")
            // select acc_budget_histories.budget,acc_budget_histories.budget_month_receive, acc_budget_histories.category_number,acc_budget_histories.po_number,acc_budget_histories.no_item,acc_budget_histories.amount_receive,acc_actual_logs.budget_no, acc_actual_logs.month_date,acc_actual_logs.description, acc_actual_logs.local_amount, 'Actual' as status from acc_budgets
            //     left join acc_budget_histories on acc_budget_histories.budget = acc_budgets.budget_no
            //     left join acc_actual_logs on acc_actual_logs.budget_no = acc_budgets.budget_no
            //     where acc_budget_histories.budget = '".$budget."' and status = '".$status."'  or acc_actual_logs.budget_no = '".$budget."' ";
        $qry = "
    SELECT
    a.budget_no,
    a.category_number,
    a.po_number,
    a.type,
    a.month_date,
    a.description,
    a.amount,
    a.`status`
    FROM (
    select 
    budget as budget_no, 
    category_number,
    po_number,
    '' as type,
    budget_month_receive as month_date,
    no_item as description,
    amount_receive as amount,
    'Actual' as `status`
    from acc_budget_histories
    where acc_budget_histories.budget = '".$budget."' 
    and `status` = 'Actual'

    UNION ALL

    select
    acc_actual_logs.budget_no, 
    '' as category_number,
    '' as po_number,
    acc_actual_logs.type,
    acc_actual_logs.month_date,
    acc_actual_logs.description, 
    acc_actual_logs.local_amount as amount,
    'Actual' as `status`
    from acc_actual_logs
    where acc_actual_logs.budget_no = '".$budget."' 
    and acc_actual_logs.investment_no is null
    ) a        
    ";
    else{
        $qry = "
        select * from acc_budget_histories
        where acc_budget_histories.budget = '".$budget."' and status = '".$status."'";
    }



    $bud = DB::select($qry);

    $response = array(
        'status' => true,
        'datas' => $bud
    );

    return Response::json($response); 
}

        //==================================//
        //          Receive Goods           //
        //==================================//

public function receive_goods()
{
    $title = 'Receive Goods';
    $title_jp = '着荷品';

    $status = AccActual::select('*')->whereNull('acc_actuals.deleted_at')
    ->distinct()
    ->get();

    return view('accounting_purchasing.master.receive_goods', array(
        'title' => $title,
        'title_jp' => $title_jp,
    ))->with('page', 'Receive Goods')
    ->with('head', 'Receive Goods');
}

public function fetch_receive(Request $request)
{
    $actual = AccActual::orderBy('acc_actuals.id', 'desc');

    if ($request->get('category') != null)
    {
        $actual = $actual->whereIn('acc_actuals.category', $request->get('category'));
    }

    $actual = $actual->select('*')->get();

    return DataTables::of($actual)

    ->editColumn('vendor_code', function ($actual)
    {
        return $actual->vendor_code. ' - ' .$actual->vendor_name;
    })

    ->editColumn('amount', function ($actual)
    {
        return '$'.$actual->amount;
    })

    ->addColumn('action', function ($actual)
    {
        $id = $actual->id;

        return ' 
        <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$id.')"><i class="fa fa-eye"></i> Detail</button>
        ';
    })

    ->rawColumns(['action' => 'action'])
    ->make(true);
}

public function receive_detail(Request $request)
{
    $detail = AccActual::find($request->get('id'));

    $response = array(
        'status' => true,
        'datas' => $detail,
    );

    return Response::json($response);
}

public function import_receive(Request $request){
    if($request->hasFile('upload_file')) {
        try{                
            $file = $request->file('upload_file');
            $file_name = 'receive_'. date("ymd_h.i") .'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/receive/'), $file_name);
            $excel = public_path('uploads/receive/') . $file_name;

            $rows = Excel::load($excel, function($reader) {
                $reader->noHeading();
                        //Skip Header
                $reader->skipRows(1);
            })->get();

            $rows = $rows->toArray();

            for ($i=0; $i < count($rows); $i++) {
                $currency  = $rows[$i][1];
                $vendor_code = $rows[$i][4];
                $vendor_name = $rows[$i][5];                    
                $receive_date = $rows[$i][14];
                $document_no = $rows[$i][15];
                $invoice_no = $rows[$i][17];    
                $no_po_sap_urut = $rows[$i][18];
                $no_po = $rows[$i][19];
                $category = $rows[$i][20];
                $item_description = $rows[$i][22];
                $qty = $rows[$i][25];
                $uom = $rows[$i][26];
                $price = $rows[$i][27];
                $amount = $rows[$i][28];
                $amount_dollar = $rows[$i][31];
                $gl_number = $rows[$i][40];
                $gl_description = $rows[$i][41];
                $cost_center = $rows[$i][42];
                $cost_description = $rows[$i][43];
                $pch_code = $rows[$i][44];

                $no_po_sap = explode("-", trim($no_po_sap_urut));
                        $item_no = substr($item_description,0,7); //get 7 char kode item

                        if($pch_code == "G40" && ($category == "A" || $category == "K")) {

                            $actual = AccActual::where('document_no', $document_no)
                            ->select('document_no')
                            ->first();

                            if (count($actual) == 0) { //kalo insert

                               $data2 = AccActual::create([
                                'currency' => $currency,
                                'vendor_code' => $vendor_code,
                                'vendor_name' => $vendor_name,
                                'receive_date' => $receive_date,
                                'document_no' => $document_no,
                                'invoice_no' => $invoice_no,
                                'no_po_sap' => (int)$no_po_sap[0],
                                'no_urut' => $no_po_sap[1],
                                'no_po' => $no_po,
                                'category' => $category,
                                'item_no' => $item_no,
                                'item_description' => $item_description,
                                'qty' => $qty,
                                'uom' => $uom,
                                'price' => $price,
                                'amount' => $amount,
                                'amount_dollar' => $amount_dollar,
                                'gl_number' => $gl_number,
                                'gl_description' => $gl_description,
                                'cost_center' => $cost_center,
                                'cost_description' => $cost_description,
                                'pch_code' => $pch_code,
                                'created_by' => Auth::id()
                            ]);

                               $data2->save();

                               //Get PO
                               $po_detail = AccPurchaseOrder::join('acc_purchase_order_details','acc_purchase_orders.no_po','=','acc_purchase_order_details.no_po')
                               ->where('no_po_sap', (int)$no_po_sap[0])
                               ->where('no_item',$item_no)
                               ->first();

                             // $total_all = $po_detail->qty_receive + $qty;

                                //Update QTY RECEIVE PO
                             // $update_qty_receive = AccPurchaseOrderDetail::where('no_po','=',$po_detail->no_po)
                             // ->where('no_item','=',$item_no)
                             // ->update(['qty_receive' => $total_all, 'date_receive' => $receive_date]);

                                //get log amount

                               $budget_log = AccBudgetHistory::where('po_number','=',$po_detail->no_po)
                               ->where(DB::raw('SUBSTRING(no_item, 1, 7)'),'=',$item_no)
                               ->where('budget','=',$po_detail->budget_item)
                               ->first();

                               $amount_po = $budget_log->amount_po;
                               $datereceive = $data2->receive_date;
                                //Get Data From Budget Master
                               $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$datereceive'");

                               foreach ($fy as $fys) {
                                $fiscal = $fys->fiscal_year;
                            }

                            $bulan = strtolower(date("M",strtotime($datereceive)));

                            $sisa_bulan = $bulan.'_sisa_budget';

                            if ($budget_log->status != "Actual") {
                                    //get Data Budget Based On Periode Dan Nomor
                                $budgetdata = AccBudget::where('budget_no','=',$po_detail->budget_item)->first();

                                // ->where('periode','=', $fiscal)

                                    //Tambahkan Budget Skrg Dengan PO yang Ada Di Log
                                $totalPlusPO = $budgetdata->$sisa_bulan + $amount_po;

                                $updatebudget = AccBudget::where('budget_no','=',$po_detail->budget_item)
                                ->update([
                                    $sisa_bulan => $totalPlusPO
                                ]);
                            }

                                // get Data Budget Based On Periode Dan Nomor
                            $budgetdata = AccBudget::where('budget_no','=',$po_detail->budget_item)->first();

                                // Kurangi dengan amount_dollar
                            $totalminusreceive = $budgetdata->$sisa_bulan - $amount_dollar;

                                // Setelah itu update data budgetnya dengan yang actual
                            $dataupdate = AccBudget::where('budget_no','=',$po_detail->budget_item)
                            ->update([
                                $sisa_bulan => $totalminusreceive
                            ]);

                               //Update Log Budget
                            $total_budget_ori = $budget_log->amount_original + $amount;
                            $total_budget = $budget_log->amount_receive + $amount_dollar;

                            $update_budget_log = AccBudgetHistory::where('po_number','=',$po_detail->no_po)
                            ->where(DB::raw('SUBSTRING(no_item, 1, 7)'),'=',$item_no)
                            ->where('budget','=',$po_detail->budget_item)
                            ->update([
                                'budget_month_receive' => strtolower(date("M",strtotime($datereceive))),
                                'currency_original' => $currency,
                                'amount_original' => $total_budget_ori,
                                'amount_receive' => $total_budget,
                                'status' => 'Actual'
                            ]);


                           } else if (count($actual) > 0){ //kalo update

                            $data2 = AccActual::where('document_no','=',$document_no)
                            ->update([
                                'currency' => $currency,
                                'vendor_code' => $vendor_code,
                                'vendor_name' => $vendor_name,
                                'receive_date' => $receive_date,
                                'document_no' => $document_no,
                                'invoice_no' => $invoice_no,
                                'no_po_sap' => (int)$no_po_sap[0],
                                'no_urut' => $no_po_sap[1],
                                'no_po' => $no_po,
                                'category' => $category,
                                'item_no' => $item_no,
                                'item_description' => $item_description,
                                'qty' => $qty,
                                'uom' => $uom,
                                'price' => $price,
                                'amount' => $amount,
                                'amount_dollar' => $amount_dollar,
                                'gl_number' => $gl_number,
                                'gl_description' => $gl_description,
                                'cost_center' => $cost_center,
                                'cost_description' => $cost_description,
                                'pch_code' => $pch_code,
                                'created_by' => Auth::id()
                            ]);

                            
                           //Get PO
                            // $po_detail = AccPurchaseOrder::join('acc_purchase_order_details','acc_purchase_orders.no_po','=','acc_purchase_order_details.no_po')
                            // ->where('no_po_sap', $no_po_sap[0])
                            // ->where('no_item',$item_no)
                            // ->first();

                            // $data2->currency = $currency;
                            // $data2->vendor_code = $vendor_code;
                            // $data2->vendor_name = $vendor_name;
                            // $data2->receive_date = $receive_date;
                            // $data2->document_no = $document_no;
                            // $data2->invoice_no = $invoice_no;
                            // $data2->no_po_sap = $no_po_sap[0];
                            // $data2->no_urut = $no_po_sap[1];
                            // $data2->no_po = $no_po;
                            // $data2->category = $category;
                            // $data2->item_no = $item_no;
                            // $data2->item_description = $item_description;
                            // $data2->qty = $qty;
                            // $data2->uom = $uom;
                            // $data2->price = $price;
                            // $data2->amount = $amount;
                            // $data2->amount_dollar = $amount_dollar;
                            // $data2->gl_number = $gl_number;
                            // $data2->gl_description = $gl_description;
                            // $data2->cost_center = $cost_center;
                            // $data2->cost_description = $cost_description;
                            // $data2->pch_code = $pch_code;
                            // $data2->created_by = Auth::id();
                            // $data2->save(); 
                        }

                        //GET DATA
                        // $datapo = AccPurchaseOrderDetail::where('no_po','=',$po_detail->no_po)
                        // ->where('no_item','=',$item_no)
                        // ->first();

                        //UPDATE STATUS
                        // if ($datapo->qty_receive >= $datapo->qty) {
                        //     $update_qty_receive = AccPurchaseOrderDetail::where('no_po','=',$po_detail->no_po)
                        //     ->where('no_item','=',$item_no)
                        //     ->update(['status' => 'close']);
                        // }
                    }
                }       

                $response = array(
                    'status' => true,
                    'message' => 'Upload Berhasil',
                );
                return Response::json($response);

            }catch(\Exception $e){
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }else{
            $response = array(
                'status' => false,
                'message' => 'Upload failed, File not found',
            );
            return Response::json($response);
        }
    }


        //==================================//
        //       Monitoring & Display       //
        //==================================//

    public function monitoringPR(){

          // $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");
      $dept = db::select("select DISTINCT department from employee_syncs");
      $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('department')
      ->first();

      return view('accounting_purchasing.display.pr_monitoring',  
        array(
          'title' => 'Purchase Requisition Monitoring & Control', 
          'title_jp' => 'PR監視・管理',
          'department' => $dept,
          'emp_dept' => $emp_dept
      )
    )->with('page', 'Purchase Requisition Control');
  }

  public function fetchMonitoringPR(Request $request)
  {
      $tahun = date('Y');

      $datefrom = $request->get('datefrom');
      $dateto = $request->get('dateto');

      if ($datefrom == "") {
          $datefrom = date('Y-m', strtotime(carbon::now()->subMonth(11)));
      }

      if ($dateto == "") {
          $dateto = date('Y-m', strtotime(carbon::now()));
      }

      $department = $request->get('department');

      if ($department != null) {
          if ($department[0] == "Maintenance Department") {
              array_push($department,"Production Engineering Department");
          }

          else if ($department[0] == "Procurement Department") {
              array_push($department,"Purchasing Control Department");
          }

          else if ($department[0] == "Human Resources Department") {
              array_push($department,"General Affairs Department");
          }

          else if ($department[0] == "Educational Instrument (EI) Department") {
              array_push($department,"Woodwind Instrument - Key Parts Process (WI-KPP) Department");
          }

          else if ($department[0] == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
            array_push($department,"Woodwind Instrument - Welding Process (WI-WP) Department");
        }

        $deptt = json_encode($department);
        $dept = str_replace(array("[","]"),array("(",")"),$deptt);

        $dep = 'and acc_purchase_requisitions.department in '.$dept;
    } else {
      $dep = '';
  }

  $data = db::select("
    SELECT
    count( no_pr ) AS jumlah,
    monthname( submission_date ) AS bulan,
    YEAR ( submission_date ) AS tahun,
    sum( CASE WHEN receive_date IS NULL THEN 1 ELSE 0 END ) AS NotSigned,
    sum( CASE WHEN receive_date IS NOT NULL THEN 1 ELSE 0 END ) AS Signed 
    FROM
    acc_purchase_requisitions 
    WHERE
    acc_purchase_requisitions.deleted_at IS NULL 
    AND DATE_FORMAT( submission_date, '%Y-%m' ) BETWEEN '".$datefrom."' AND '".$dateto."' ".$dep." 
    GROUP BY
    bulan,
    tahun 
    ORDER BY
    tahun,
    MONTH ( submission_date ) ASC
    ");

  $data_pr_belum_po = db::select("
    SELECT
    acc_purchase_requisitions.no_pr,
    sum( CASE WHEN sudah_po IS NULL THEN 1 ELSE 0 END ) AS belum_po,
    sum( CASE WHEN sudah_po IS NOT NULL THEN 1 ELSE 0 END ) AS sudah_po 
    FROM
    acc_purchase_requisitions
    LEFT JOIN acc_purchase_requisition_items ON acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr 
    WHERE
    acc_purchase_requisitions.deleted_at IS NULL 
    AND receive_date IS NOT NULL 
    AND DATE_FORMAT( submission_date, '%Y-%m' ) BETWEEN '".$datefrom."' AND '".$dateto."' ".$dep." 
    GROUP BY
    no_pr 
    ORDER BY
    submission_date ASC");

      // $data_po_belum_receive = db::select("
      //   SELECT
      //   acc_purchase_requisitions.no_pr,
      //   sum( CASE WHEN acc_purchase_order_details.`status` IS NULL THEN 1 ELSE 0 END ) AS belum_close,
      //   sum( CASE WHEN acc_purchase_order_details.`status` IS NOT NULL THEN 1 ELSE 0 END ) AS sudah_close 
      //   FROM
      //   acc_purchase_requisitions
      //   LEFT JOIN acc_purchase_requisition_items ON acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr 
      //   LEFT JOIN acc_purchase_order_details on acc_purchase_requisition_items.no_pr = acc_purchase_order_details.no_pr and acc_purchase_requisition_items.item_code = acc_purchase_order_details.no_item
      //   WHERE
      //   acc_purchase_requisitions.deleted_at IS NULL 
      //   AND acc_purchase_requisition_items.sudah_po IS NOT NULL 
      //   AND acc_purchase_requisitions.receive_date IS NOT NULL
      //   AND DATE_FORMAT( submission_date, '%Y-%m' ) 
      //   BETWEEN '".$datefrom."' AND '".$dateto."' ".$dep." 
      //   GROUP BY
      //   no_pr 
      //   ORDER BY
      //   submission_date ASC
      //   ");

  $response = array(
    'status' => true,
    'datas' => $data,
    'data_pr_belum_po' => $data_pr_belum_po,
        // 'data_po_belum_receive' => $data_po_belum_receive,
    'tahun' => $tahun,
    'datefrom' => $datefrom,
    'dateto' => $dateto,
    'department' => $department
);

  return Response::json($response); 
}

public function fetchMonitoringPROutstanding(Request $request)
{

  $tahun = date('Y');

  $datefrom = $request->get('datefrom');
  $dateto = $request->get('dateto');

  if ($datefrom == "") {
      $datefrom = date('Y-m', strtotime(carbon::now()->subMonth(11)));
  }

  if ($dateto == "") {
      $dateto = date('Y-m', strtotime(carbon::now()));
  }

  $department = $request->get('department');

  if ($department != null) {

    if ($department[0] == "Maintenance Department") {
      array_push($department,"Production Engineering Department");
  }

  else if ($department[0] == "Procurement Department") {
      array_push($department,"Purchasing Control Department");
  }

  else if ($department[0] == "Human Resources Department") {
      array_push($department,"General Affairs Department");
  }

  else if ($department[0] == "Educational Instrument (EI) Department") {
      array_push($department,"Woodwind Instrument - Key Parts Process (WI-KPP) Department");
  }

  else if ($department[0] == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
    array_push($department,"Woodwind Instrument - Welding Process (WI-WP) Department");
}

$deptt = json_encode($department);
$dept = str_replace(array("[","]"),array("(",")"),$deptt);

$dep = 'and acc_purchase_requisitions.department in '.$dept;
} else {
  $dep = '';
}

$data = db::select("
    select acc_purchase_requisitions.no_pr,acc_purchase_requisitions.department, sum(case when sudah_po is null then 1 else 0 end) as belum_po, sum(case when sudah_po is not null then 1 else 0 end) as sudah_po from acc_purchase_requisitions left join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.deleted_at is null and receive_date is not null and DATE_FORMAT(submission_date,'%Y-%m') between '".$datefrom."' and '".$dateto."' ".$dep."  GROUP BY no_pr order by submission_date ASC");

$response = array(
    'status' => true,
    'datas' => $data,
    'tahun' => $tahun,
    'datefrom' => $datefrom,
    'dateto' => $dateto,
    'departement' => $dep
);

return Response::json($response); 
}

public function monitoringPrPch(){

          // $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");
  $dept = db::select("select DISTINCT department from employee_syncs");

  return view('accounting_purchasing.display.pr_monitoring_pch',  
    array(
      'title' => 'Purchase Requisition Monitoring & Control', 
      'title_jp' => 'PR監視・管理',
      'department' => $dept
  )
)->with('page', 'Purchase Requisition Control');
}


public function fetchMonitoringPRPch(Request $request){

  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $last = AccPurchaseRequisition::whereNull('receive_date')
  ->whereNull('deleted_at')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(submission_date) as tanggal'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
}else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
    }
}
}


if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
}

$department = $request->get('department');

if ($department != null) {
  $deptt = json_encode($department);
  $dept = str_replace(array("[","]"),array("(",")"),$deptt);

  $dep = 'and acc_purchase_requisitions.department in'.$dept;
} else {
  $dep = '';
}

          //per tgl
$data = db::select("
   select date.week_date, coalesce(belum_diterima.total, 0) as jumlah_belum, coalesce(sudah_diterima.total, 0) as jumlah_sudah from 
   (select week_date from weekly_calendars 
   where date(week_date) >= '".$datefrom."'
   and date(week_date) <= '".$dateto."') date
   left join
   (select date(submission_date) as date, count(id) as total from acc_purchase_requisitions
   where date(submission_date) >= '".$datefrom."' and date(submission_date) <= '".$dateto."' and acc_purchase_requisitions.deleted_at is null and receive_date is null  ".$dep."
   group by date(submission_date)) belum_diterima
   on date.week_date = belum_diterima.date
   left join
   (select date(submission_date) as date, count(id) as total from acc_purchase_requisitions
   where date(submission_date) >= '".$datefrom."' and date(submission_date) <= '".$dateto."' and acc_purchase_requisitions.deleted_at is null and receive_date is not null ".$dep."
   group by date(submission_date)) sudah_diterima
   on date.week_date = sudah_diterima.date
   order by week_date asc");

          //per department
$data_dept = db::select("
    select dept.department, coalesce(pr.total, 0) as jumlah_dept from 
    (select distinct department from employee_syncs where department is not null) dept
    left join
    (select department, count(id) as total from acc_purchase_requisitions
    where acc_purchase_requisitions.deleted_at is null 
    group by department) pr
    on dept.department = pr.department
    order by department asc");

$data_pr_belum_po = db::select("
    select acc_purchase_requisitions.no_pr, sum(case when sudah_po is null then 1 else 0 end) as belum_po, sum(case when sudah_po is not null then 1 else 0 end) as sudah_po from acc_purchase_requisitions left join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.deleted_at is null and receive_date is not null ".$dep."  GROUP BY no_pr order by submission_date asc");

$data_investment_belum_po = db::select("
    SELECT
    acc_investments.reff_number,
    sum( CASE WHEN sudah_po IS NULL THEN 1 ELSE 0 END ) AS belum_po,
    sum( CASE WHEN sudah_po IS NOT NULL THEN 1 ELSE 0 END ) AS sudah_po 
    FROM
    acc_investments
    LEFT JOIN acc_investment_details ON acc_investments.reff_number = acc_investment_details.reff_number 
    WHERE
    acc_investments.deleted_at IS NULL
    AND posisi = 'finished' 
    GROUP BY
    reff_number 
    ORDER BY
    submission_date ASC
    ");

$year = date('Y');

$response = array(
    'status' => true,
    'datas' => $data,
    'data_dept' => $data_dept,
    'data_pr_belum_po' => $data_pr_belum_po,
    'data_investment_belum_po' => $data_investment_belum_po,
    'year' => $year
);

return Response::json($response);
}

public function fetchtablePR(Request $request)
{
  $datefrom = $request->get('datefrom');
  $dateto = $request->get('dateto');

  if ($datefrom == "") {
      $datefrom = date('Y-m', strtotime(carbon::now()->subMonth(11)));
  }

  if ($dateto == "") {
      $dateto = date('Y-m', strtotime(carbon::now()));
  }

  $department = $request->get('department');

  if ($department != null) {

      if ($department[0] == "Maintenance Department") {
          array_push($department,"Production Engineering Department");
      }

      else if ($department[0] == "Procurement Department") {
          array_push($department,"Purchasing Control Department");
      }

      else if ($department[0] == "Human Resources Department") {
          array_push($department,"General Affairs Department");
      }

      else if ($department[0] == "Educational Instrument (EI) Department") {
          array_push($department,"Woodwind Instrument - Key Parts Process (WI-KPP) Department");
      }

      else if ($department[0] == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
        array_push($department,"Woodwind Instrument - Welding Process (WI-WP) Department");
    }

    $deptt = json_encode($department);
    $dept = str_replace(array("[","]"),array("(",")"),$deptt);

    $dep = 'and acc_purchase_requisitions.department in'.$dept;
} else {
  $dep = '';
}


$data = db::select("
    SELECT
    acc_purchase_requisitions.id,
    no_pr,
    emp_id,
    emp_name,
    department_shortname,
    section,
    no_budget,
    submission_date,
    po_due_date,
    receive_date,
    file,
    posisi,
    `status`,
    staff,
    manager,
    manager_name,
    ( SELECT `name` FROM employee_syncs WHERE employee_id = dgm ) AS dgm,
    ( SELECT `name` FROM employee_syncs WHERE employee_id = gm ) AS gm,
    approvalm,
    dateapprovalm,
    approvaldgm,
    dateapprovaldgm,
    approvalgm,
    dateapprovalgm,
    alasan,
    datereject 
    FROM
    acc_purchase_requisitions
    JOIN departments ON acc_purchase_requisitions.department = department_name 
    WHERE
    acc_purchase_requisitions.STATUS != 'received' 
    AND posisi != 'user'
    AND acc_purchase_requisitions.deleted_at IS NULL 
    AND DATE_FORMAT( submission_date, '%Y-%m' ) BETWEEN '".$datefrom."' 
    AND '".$dateto."' ".$dep." 
    ORDER BY submission_date ASC");


$data_pr_belum_po = db::select("
    SELECT 
    acc_purchase_requisitions.no_pr,
    departments.department_shortname,
    acc_purchase_requisition_items.item_code,
    acc_purchase_requisition_items.item_desc,
    acc_purchase_requisition_items.item_request_date 
    FROM
    acc_purchase_requisitions
    LEFT JOIN acc_purchase_requisition_items ON acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr
    JOIN departments ON acc_purchase_requisitions.department = departments.department_name 
    WHERE
    acc_purchase_requisitions.deleted_at IS NULL 
    AND receive_date IS NOT NULL 
    AND sudah_po IS NULL 
    AND DATE_FORMAT( submission_date, '%Y-%m' ) BETWEEN '".$datefrom."' 
    AND '".$dateto."' ".$dep." 
    ORDER BY submission_date ASC");


  // $data_po_belum_receive = db::select("
  //   SELECT DISTINCT
  //   acc_purchase_requisitions.no_pr, 
  //   departments.department_shortname,
  //   acc_purchase_orders.no_po,
  //   acc_purchase_orders.tgl_po,
  //   acc_purchase_orders.supplier_name,
  //   acc_purchase_order_details.nama_item,
  //   IF(acc_purchase_orders.posisi = 'pch', 'PO Terkirim', 'PO Approval') as status_po
  //   FROM acc_purchase_orders
  //   LEFT JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po
  //   LEFT JOIN acc_purchase_requisitions ON acc_purchase_order_details.no_pr = acc_purchase_requisitions.no_pr
  //   LEFT JOIN acc_purchase_requisition_items ON acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr
  //   JOIN departments ON acc_purchase_requisitions.department = departments.department_name 
  //   WHERE
  //   acc_purchase_requisitions.deleted_at IS NULL 
  //   AND acc_purchase_requisitions.receive_date IS NOT NULL
  //   AND acc_purchase_requisition_items.sudah_po IS NOT NULL 
  //   AND acc_purchase_orders.deleted_at IS NULL
  //   AND acc_purchase_order_details.`status` IS NULL 
  //   AND DATE_FORMAT( tgl_po, '%Y-%m' ) BETWEEN '".$datefrom."' AND '".$dateto."' 
  //   ".$dep." 
  //   ORDER BY
  //   tgl_po ASC ");

$response = array(
    'status' => true,
    'datas' => $data,
    'data_pr_belum_po' => $data_pr_belum_po
);

return Response::json($response); 
}

public function fetchtablePRPch(Request $request)
{
  $datefrom = $request->get('datefrom');
  $dateto = $request->get('dateto');

  if ($datefrom == "") {
      $datefrom = date('Y-m', strtotime(carbon::now()->subMonth(11)));
  }

  if ($dateto == "") {
      $dateto = date('Y-m', strtotime(carbon::now()));
  }

  $department = $request->get('department');

  if ($department != null) {
      $deptt = json_encode($department);
      $dept = str_replace(array("[","]"),array("(",")"),$deptt);

      $dep = 'and acc_purchase_requisitions.department in'.$dept;
  } else {
      $dep = '';
  }


  $data = db::select("select id, no_pr, emp_id, emp_name, department, section, no_budget, submission_date, po_due_date, receive_date, file, posisi, `status`, staff, manager, manager_name, (select `name` from employee_syncs where employee_id = dgm) as dgm, (select `name` from employee_syncs where employee_id = gm) as gm , approvalm, dateapprovalm, approvaldgm, dateapprovaldgm, approvalgm, dateapprovalgm, alasan, datereject from acc_purchase_requisitions where acc_purchase_requisitions.status = 'approval_acc' and acc_purchase_requisitions.deleted_at is null and DATE_FORMAT(submission_date,'%Y-%m') between '".$datefrom."' and '".$dateto."' ".$dep." order by submission_date asc");


  $data_investment = db::select("
    select * from acc_investments where acc_investments.posisi = 'finished' and acc_investments.receive_date is null and acc_investments.deleted_at is null and DATE_FORMAT(submission_date,'%Y-%m') between '".$datefrom."' and '".$dateto."' order by submission_date asc");

  $data_pr_belum_po = db::select("select acc_purchase_requisitions.no_pr,acc_purchase_requisitions.receive_date, acc_purchase_requisitions.department, acc_purchase_requisition_items.item_code, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.item_request_date from acc_purchase_requisitions left join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.deleted_at is null and receive_date is not null and sudah_po is null and DATE_FORMAT(submission_date,'%Y-%m') between '".$datefrom."' and '".$dateto."' ".$dep." order by submission_date ASC");

  $response = array(
    'status' => true,
    'datas' => $data,
    'data_pr_belum_po' => $data_pr_belum_po,
    'data_investment' => $data_investment
);

  return Response::json($response); 
}

public function detailMonitoringPR(Request $request){

  $bulan = $request->get("bulan");
  $status = $request->get("status");
  $tglfrom = $request->get("tglfrom");
  $tglto = $request->get("status");
  $department = $request->get("department");

  $status_sign = "";

  if ($status == "Sign Not Completed") {
      $status_sign = "and receive_date is null";
  }
  else if ($status == "Sign Completed") {
      $status_sign = "and receive_date is not null";
  }


  if ($department != null) {
      $deptt = json_encode($department);
      $dept = str_replace(array("[","]"),array("(",")"),$deptt);
      $dep = 'and acc_purchase_requisitions.department in'.$dept;
  } else {
      $dep = '';
  }


  $pr = DB::select("
    SELECT
            * 
    FROM
    acc_purchase_requisitions 
    WHERE
    deleted_at IS NULL 
    AND monthname( submission_date ) = '".$bulan."' 
    AND DATE_FORMAT( submission_date, '%Y-%m' ) BETWEEN '".$tglfrom."' 
    AND '".$tglto."' ".$dep." ".$status_sign."
    ");

  return DataTables::of($pr)
  ->editColumn('submission_date', function ($pr)
  {
    return $pr->submission_date;
})
  ->editColumn('note', function ($pr)
  {
    $note = "";
    if ($pr->note != null)
    {
        $note = $pr->note;
    }
    else
    {
        $note = '-';
    }

    return $note;
})
  ->editColumn('status', function ($pr)
  {
    $id = $pr->id;

    if ($pr->status == "approval")
    {
        return '<label class="label label-warning">Approval</a>';
    }
    else if ($pr->status == "approval_acc")
    {
        return '<label class="label label-info">Diverifikasi Purchasing</a>';
    }
    else if ($pr->status == "received")
    {
        return '<label class="label label-success">Diterima Purchasing</a>';
    }

})
  ->addColumn('action', function ($pr)
  {
    $id = $pr->id;

                // <a href="purchase_requisition/detail/' . $id . '" class="btn btn-info btn-xs" data-toggle="tooltip" title="Detail PR"><i class="fa fa-eye"></i></a>

    if($pr->status == "approval"){
        return '
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        ';
    }
    else{
        return '
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        ';
    }


})
  ->editColumn('file', function ($pr)
  {

    $data = json_decode($pr->file);

    $fl = "";

    if ($pr->file != null)
    {
        for ($i = 0;$i < count($data);$i++)
        {
            $fl .= '<a href="../files/pr/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
        }
    }
    else
    {
        $fl = '-';
    }

    return $fl;
})
  ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file'])
  ->make(true);
}

public function detailMonitoringPRPch(Request $request){

  $tanggal = $request->get("tanggal");
  $status = $request->get("status");
  $tglfrom = $request->get("tglfrom");
  $tglto = $request->get("status");
  $department = $request->get("department");

  $status_sign = "";

  if ($status == "PR Incompleted") {
      $status_sign = "and receive_date is null";
  }
  else if ($status == "PR Completed") {
      $status_sign = "and receive_date is not null";
  }

  $qry = "SELECT  * FROM acc_purchase_requisitions WHERE deleted_at IS NULL and submission_date = '".$tanggal."' and DATE_FORMAT(submission_date,'%Y-%m') between '".$tglfrom."' and '".$tglto."' ".$department." ".$status_sign." ";

  $pr = DB::select($qry);

  return DataTables::of($pr)
  ->editColumn('submission_date', function ($pr)
  {
    return $pr->submission_date;
})
  ->editColumn('note', function ($pr)
  {
    $note = "";
    if ($pr->note != null)
    {
        $note = $pr->note;
    }
    else
    {
        $note = '-';
    }

    return $note;
})
  ->editColumn('status', function ($pr)
  {
    $id = $pr->id;

    if ($pr->status == "approval")
    {
        return '<label class="label label-warning">Approval</a>';
    }
    else if ($pr->status == "approval_acc")
    {
        return '<label class="label label-info">Diverifikasi Purchasing</a>';
    }
    else if ($pr->status == "received")
    {
        return '<label class="label label-success">Diterima Purchasing</a>';
    }

})
  ->addColumn('action', function ($pr)
  {
    $id = $pr->id;

                // <a href="purchase_requisition/detail/' . $id . '" class="btn btn-info btn-xs" data-toggle="tooltip" title="Detail PR"><i class="fa fa-eye"></i></a>

    if($pr->status == "approval"){
        return '
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        ';
    }
    else{
        return '
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        ';
    }


})
  ->editColumn('file', function ($pr)
  {

    $data = json_decode($pr->file);

    $fl = "";

    if ($pr->file != null)
    {
        for ($i = 0;$i < count($data);$i++)
        {
            $fl .= '<a href="../files/pr/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
        }
    }
    else
    {
        $fl = '-';
    }

    return $fl;
})
  ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file'])
  ->make(true);
}

public function detailMonitoringPRPO(Request $request){

    $pr = $request->get("pr");
    $status = $request->get("status");
    $tglfrom = $request->get("tglfrom");
    $tglto = $request->get("status");
    $department = $request->get("department");

    $status_sign = "";

    if ($status == "Belum PO") {
        $status_sign = "and sudah_po is null";
    }
    else if ($status == "Sudah PO") {
        $status_sign = "and sudah_po is not null";
    }


    if ($department != null) {
      $deptt = json_encode($department);
      $dept = str_replace(array("[","]"),array("(",")"),$deptt);
      $dep = 'and acc_purchase_requisitions.department in'.$dept;
  } else {
      $dep = '';
  }


  $qry = "SELECT acc_purchase_requisitions.submission_date, acc_purchase_requisition_items.*, (select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order, (select supplier_name from acc_purchase_order_details join acc_purchase_orders on acc_purchase_orders.no_po = acc_purchase_order_details.no_po where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY acc_purchase_order_details.created_at desc limit 1) as last_vendor from acc_purchase_requisitions left join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr WHERE acc_purchase_requisition_items.deleted_at is NULL and acc_purchase_requisitions.no_pr = '".$pr."' and DATE_FORMAT(submission_date,'%Y-%m') between '".$tglfrom."' and '".$tglto."' ".$dep." ".$status_sign." ";

  $pr = DB::select($qry);

  return DataTables::of($pr)
  ->editColumn('item_request_date', function ($pr)
  {
    return date('d-M-Y', strtotime($pr->item_request_date)) ;
})
  ->editColumn('item_qty', function ($pr)
  {
    return $pr->item_qty.' '.$pr->item_uom;
})
  ->editColumn('item_price', function ($pr)
  {
    return number_format($pr->item_price,0,",",".");
})
  ->editColumn('item_amount', function ($pr)
  {
    return number_format($pr->item_amount,0,",",".");
})
  ->editColumn('last_order', function ($pr)
  {
    if ($pr->last_order != null) {
        return date('d-M-Y', strtotime($pr->last_order)) ;
    }
    else{
        return '-';
    }
})

  ->editColumn('status', function ($pr)
  {
    if ($pr->sudah_po == null) {
        return '<span class="label label-danger">Belum PO</span>';
    }
    else if ($pr->sudah_po != null) {
        return '<span class="label label-success">Sudah PO</span>';
    }
})

  ->rawColumns(['status' => 'status'])
  ->make(true);
}

public function detailMonitoringPRActual(Request $request){

    $pr = $request->get("pr");
    $status = $request->get("status");
    $tglfrom = $request->get("tglfrom");
    $tglto = $request->get("status");
    $department = $request->get("department");


    $status_sign = "";

    if ($status == "Belum Datang") {
        $status_sign = "and acc_purchase_order_details.`status` is null";
    }
    else if ($status == "Sudah Datang") {
        $status_sign = "and acc_purchase_order_details.`status` is not null";
    }

    if ($department != null) {
      $deptt = json_encode($department);
      $dept = str_replace(array("[","]"),array("(",")"),$deptt);
      $dep = 'and acc_purchase_requisitions.department in'.$dept;
  } else {
      $dep = '';
  }



  $practual = DB::select("
    SELECT acc_purchase_orders.*, acc_purchase_order_details.*, acc_purchase_requisitions.department FROM acc_purchase_orders
    LEFT JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po
    LEFT JOIN acc_purchase_requisitions ON acc_purchase_order_details.no_pr = acc_purchase_requisitions.no_pr
    WHERE
    acc_purchase_orders.deleted_at IS NULL
    AND acc_purchase_order_details.no_pr = '".$pr."'
    AND DATE_FORMAT( submission_date, '%Y-%m' ) BETWEEN '".$tglfrom."' 
    AND '".$tglto."' ".$dep." ".$status_sign."
    ");

  return DataTables::of($practual)

  ->editColumn('tgl_po', function ($practual)
  {
    return $practual->tgl_po;
})
  ->editColumn('status', function ($practual)
  {
    if ($practual->status == null) {
        return '<span class="label label-danger">Belum Close</span>';
    }
    else if ($practual->status != null) {
        return '<span class="label label-success">Sudah Close</span>';
    }

})
  ->rawColumns(['status' => 'status'])
  ->make(true);
}



public function monitoringPO(){

    return view('accounting_purchasing.display.po_monitoring',  
        array(
          'title' => 'Purchase Order Monitoring', 
          'title_jp' => 'PO管理',
      )
    )->with('page', 'Purchase Order Monitoring');
}

public function fetchMonitoringPO(Request $request){

  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $last = AccPurchaseOrder::where('status','=','pch')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tgl_po) as tanggal'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
}
else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
    }
}
}


if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
}

          //per tgl
$data = db::select("
    select date.week_date, coalesce(belum_diterima.total, 0) as jumlah_belum, coalesce(sudah_diterima.total, 0) as jumlah_sudah from 
    (select week_date from weekly_calendars 
    where date(week_date) >= '".$datefrom."'
    and date(week_date) <= '".$dateto."') date
    join
    (select date(tgl_po) as date, count(id) as total from acc_purchase_orders
    where date(tgl_po) >= '".$datefrom."' and date(tgl_po) <= '".$dateto."' and acc_purchase_orders.deleted_at is null and posisi = 'pch' and (`status` = 'sap' or `status` = 'not_sap')
    group by date(tgl_po)) sudah_diterima
    on date.week_date = sudah_diterima.date
    left join
    (select date(tgl_po) as date, count(id) as total from acc_purchase_orders
    where date(tgl_po) >= '".$datefrom."' and date(tgl_po) <= '".$dateto."' and acc_purchase_orders.deleted_at is null and `status` != 'sap'
    group by date(tgl_po)) belum_diterima
    on date.week_date = belum_diterima.date
    order by week_date asc
    ");

$year = date('Y');

$response = array(
    'status' => true,
    'datas' => $data,
    'year' => $year
);

return Response::json($response);
}

public function detailMonitoringPO(Request $request){

  $tanggal = $request->get("tanggal");
  $status = $request->get("status");
  $tglfrom = $request->get("tglfrom");
  $tglto = $request->get("tglto");


  $status_sign = "";

  if ($status == "PO Incompleted") {
      $status_sign = "and status != 'sap'";
  }
  else if ($status == "PO Completed") {
      $status_sign = "and posisi = 'pch' and status = 'sap'";
  }


  $qry = "SELECT * FROM acc_purchase_orders WHERE deleted_at IS NULL and DATE_FORMAT(tgl_po,'%Y-%m-%d') = '".$tanggal."' ".$status_sign." order by id DESC";
  $po = DB::select($qry);

  return DataTables::of($po)

  ->editColumn('tgl_po', function ($po)
  {
    return date('Y-m-d', strtotime($po->tgl_po));
})

  ->editColumn('no_po_sap', function ($po)
  {
    $id = $po->id;

    $po_sap = "";
    if ($po->no_po_sap == null && $po->status == "not_sap")
    {
        $po_sap = '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-md" onClick="editSAP(' . $id . ')"><i class="fa fa-edit"></i> NO PO SAP</a>';
    }
    else if ($po->no_po_sap != null){
        $po_sap = $po->no_po_sap;   
    }
    else
    {
        $po_sap = '-';
    }

    return $po_sap;
})

  ->editColumn('status', function ($po)
  {
    $id = $po->id;

    if ($po->posisi == "staff_pch")
    {
        return '<label class="label label-danger">Staff Purchasing</label>';
    }

    else if ($po->posisi == "manager_pch")
    {
        return '<label class="label label-warning">Diverifikasi Manager</label>';
    }

    else if ($po->posisi == "dgm_pch")
    {
        return '<label class="label label-primary">Diverifikasi GM Support</label>';
    }

    else if ($po->posisi == "gm_pch")
    {
        return '<label class="label label-primary">Diverifikasi GM</label>';
    }

    else if ($po->posisi == "pch")
    {
        return '<label class="label label-success">Sudah Diverifikasi</label>';
    }

})
  ->addColumn('action', function ($po)
  {
    $id = $po->id;
    if ($po->posisi == "staff_pch") {
        return '
        <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i> Edit</a>
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        <button class="btn btn-xs btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;"  onclick="sendEmail(' . $id .')"><i class="fa fa-envelope"></i> Send Email</button>
        ';
    }

    else if ($po->posisi == "pch") {
        return '
        <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i> Edit</a>
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"> Report</i></a>
        ';
    }

    else{
        return '
        <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i> Edit</a>
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        <label class="label label-success">Email Sudah Dikirim</label>
        ';   
    }
})
  ->rawColumns(['status' => 'status', 'action' => 'action', 'no_po_sap' => 'no_po_sap'])
  ->make(true);
}

public function fetchtablePO(Request $request)
{
  $datefrom = $request->get('datefrom');
  $dateto = $request->get('dateto');

  if ($datefrom == "") {
      $datefrom = date('Y-m-d', strtotime(carbon::now()->subMonth(11)));
  }

  if ($dateto == "") {
      $dateto = date('Y-m-d', strtotime(carbon::now()));
  }

  $data = db::select("

    SELECT
    t1.*,IF(t1.goods_price != 0,sum( t1.goods_price * t1.qty ),sum( t1.service_price * t1.qty )) AS amount 
    FROM
    (
    SELECT
    acc_purchase_orders.*,
    date(acc_purchase_orders.tgl_po) as po_date,
    acc_purchase_order_details.budget_item,
    acc_purchase_order_details.goods_price,
    acc_purchase_order_details.service_price,
    acc_purchase_order_details.qty 
    FROM
    acc_purchase_orders
    JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po 
    WHERE
    acc_purchase_orders.`status` != 'sap' 
    AND acc_purchase_orders.deleted_at IS NULL 
    AND DATE_FORMAT( tgl_po, '%Y-%m-%d' ) BETWEEN  '".$datefrom."' AND '".$dateto."' 
    ORDER BY
    tgl_po ASC 
    ) t1
    GROUP BY t1.no_po");

          //select id, remark, no_po, no_po_sap, DATE_FORMAT(tgl_po,'%Y-%m-%d'),supplier_code, supplier_name, supplier_status, buyer_id, buyer_name, authorized2, authorized2_name, approval_authorized2, date_approval_authorized2, authorized3, authorized3_name, date_approval_authorized3, approval_authorized3, authorized4, authorized4_name, date_approval_authorized4, approval_authorized4, reject, datereject, posisi, `status` from acc_purchase_orders where acc_purchase_orders.`status` != 'sap' and acc_purchase_orders.deleted_at is null and DATE_FORMAT(tgl_po,'%Y-%m-%d') between '2020-08-01' and '2020-09-01'  order by tgl_po asc

  $response = array(
    'status' => true,
    'datas' => $data
);

  return Response::json($response); 
}

public function investmentControl(){

    $dept = db::select("select DISTINCT department from employee_syncs");
    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('department')
    ->first();

    return view('accounting_purchasing.display.investment_control',  
        array(
          'title' => 'Investment Monitoring & Control', 
          'title_jp' => '投資監視・管理',
          'emp_dept' => $emp_dept,
          'department' => $dept
      )
    )->with('page', 'Investment Control');

}

public function fetchInvestmentControl(Request $request){

  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $last = AccInvestment::where('posisi','!=','finished')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(submission_date) as tanggal'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
}
else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
    }
}
}


if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
}

$department = $request->get('department');

if ($department != null) {

    if ($department[0] == "Maintenance Department") {
      array_push($department,"Production Engineering Department");
  }

  else if ($department[0] == "Procurement Department") {
      array_push($department,"Purchasing Control Department");
  }

  else if ($department[0] == "Human Resources Department") {
      array_push($department,"General Affairs Department");
  }
  else if ($department[0] == "Educational Instrument (EI) Department") {
      array_push($department,"Woodwind Instrument - Key Parts Process (WI-KPP) Department");
  }

  else if ($department[0] == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
    array_push($department,"Woodwind Instrument - Welding Process (WI-WP) Department");
}

$deptt = json_encode($department);
$dept = str_replace(array("[","]"),array("(",")"),$deptt);

$dep = 'and acc_investments.applicant_department in '.$dept;
} else {
  $dep = '';
}

          //per tgl
$data = db::select("
   SELECT
   date2.week_date,
   date2.week_name,
   sum( date2.not_finish ) AS undone,
   sum( date2.finish ) done 
   FROM
   (
   SELECT
   date.week_date,
   date.week_name,
   COALESCE ( not_finish.total, 0 ) AS not_finish,
   COALESCE ( finish.total, 0 ) AS finish 
   FROM
   ( SELECT week_date, week_name FROM weekly_calendars WHERE date( week_date ) >= '".$datefrom."' AND date( week_date ) <= '".$dateto."' ) date
   LEFT JOIN (
   SELECT
   date( submission_date ) AS date,
   count( id ) AS total 
   FROM
   acc_investments 
   WHERE
   date( submission_date ) >= '".$datefrom."' 
   AND date( submission_date ) <= '".$dateto."' ".$dep." 
   AND acc_investments.deleted_at IS NULL 
   AND posisi = 'finished' 
   GROUP BY
   date( submission_date )) finish ON date.week_date = finish.date
   LEFT JOIN (
   SELECT
   date( submission_date ) AS date,
   count( id ) AS total 
   FROM
   acc_investments 
   WHERE
   date( submission_date ) >= '".$datefrom."' 
   AND date( submission_date ) <= '".$dateto."' ".$dep." 
   AND acc_investments.deleted_at IS NULL 
   AND `posisi` != 'finished' 
   GROUP BY
   date( submission_date )) not_finish ON date.week_date = not_finish.date 
   ORDER BY
   week_date ASC 
   ) date2 
   GROUP BY
   date2.week_name       
   ORDER BY week_date ASC   
   ");

$data_investment_belum_po = db::select("
    SELECT
    acc_investments.reff_number,
    sum( CASE WHEN sudah_po IS NULL THEN 1 ELSE 0 END ) AS belum_po,
    sum( CASE WHEN sudah_po IS NOT NULL THEN 1 ELSE 0 END ) AS sudah_po 
    FROM
    acc_investments
    LEFT JOIN acc_investment_details ON acc_investments.reff_number = acc_investment_details.reff_number 
    WHERE
    acc_investments.deleted_at IS NULL
    AND posisi = 'finished' 
    ".$dep." 
    GROUP BY
    reff_number 
    ORDER BY
    submission_date ASC
    ");

// $data_investment_belum_receive = db::select("
//     SELECT
//     acc_investments.reff_number,
//     sum( CASE WHEN acc_purchase_order_details.`status` IS NULL THEN 1 ELSE 0 END ) AS belum_close,
//     sum( CASE WHEN acc_purchase_order_details.`status` IS NOT NULL THEN 1 ELSE 0 END ) AS sudah_close 
//     FROM
//     acc_investments
//     LEFT JOIN acc_investment_details ON acc_investments.reff_number = acc_investment_details.reff_number 
//     LEFT JOIN acc_purchase_order_details on acc_investment_details.reff_number = acc_purchase_order_details.no_pr 
//     and acc_investment_details.no_item = acc_purchase_order_details.no_item
//     WHERE
//     acc_investments.deleted_at IS NULL 
//     AND acc_investment_details.sudah_po IS NOT NULL 
//     AND acc_investments.receive_date IS NOT NULL
//     ".$dep." 
//     GROUP BY
//     reff_number 
//     ORDER BY
//     submission_date ASC
//     ");

$year = date('Y');

$response = array(
    'status' => true,
    'datas' => $data,
    'year' => $year,
    'department' => $dep,
    'data_investment_belum_po' => $data_investment_belum_po
    // 'data_investment_belum_receive' => $data_investment_belum_receive
);

return Response::json($response);
}



public function fetchtableinv(Request $request)
{
  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $last = AccInvestment::where('posisi','!=','finished')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(submission_date) as tanggal'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
}else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
    }
}
}


if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
}


$department = $request->get('department');

if ($department != null) {

    if ($department[0] == "Maintenance Department") {
      array_push($department,"Production Engineering Department");
  }

  else if ($department[0] == "Procurement Department") {
      array_push($department,"Purchasing Control Department");
  }

  else if ($department[0] == "Human Resources Department") {
      array_push($department,"General Affairs Department");
  }
  else if ($department[0] == "Educational Instrument (EI) Department") {
      array_push($department,"Woodwind Instrument - Key Parts Process (WI-KPP) Department");
  }

  else if ($department[0] == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
    array_push($department,"Woodwind Instrument - Welding Process (WI-WP) Department");
}

$deptt = json_encode($department);
$dept = str_replace(array("[","]"),array("(",")"),$deptt);

$dep = 'and acc_investments.applicant_department in'.$dept;
} else {
  $dep = '';
}

$data = db::select("
    SELECT 
    acc_investments.*, departments.department_shortname
    FROM
    acc_investments
    JOIN 
    departments
    on acc_investments.applicant_department = departments.department_name
    WHERE
    acc_investments.`posisi` != 'finished' 
    AND acc_investments.deleted_at IS NULL 
    AND DATE_FORMAT( submission_date, '%Y-%m-%d' ) BETWEEN '".$datefrom."' 
    AND '".$dateto."' ".$dep."
    ORDER BY
    reff_number ASC
    ");

$data_investment_belum_po = db::select("
    SELECT
    acc_investments.*,
    acc_investment_details.*,
    departments.department_shortname 
    FROM
    acc_investments
    LEFT JOIN acc_investment_details ON acc_investments.reff_number = acc_investment_details.reff_number
    JOIN departments ON acc_investments.applicant_department = departments.department_name 
    WHERE
    acc_investments.deleted_at IS NULL 
    AND sudah_po IS NULL 
    AND posisi = 'finished' ".$dep." 
    ORDER BY
    submission_date ASC
    ");

// $data_po_belum_receive = db::select("
//     SELECT DISTINCT
//     acc_investments.reff_number, 
//     departments.department_shortname,
//     acc_purchase_orders.no_po,
//     acc_purchase_orders.tgl_po,
//     acc_purchase_orders.supplier_name,
//     acc_purchase_order_details.nama_item,
//     IF(acc_purchase_orders.posisi = 'pch', 'PO Terkirim', 'PO Approval') as status_po
//     FROM acc_purchase_orders
//     LEFT JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po
//     LEFT JOIN acc_investments ON acc_purchase_order_details.no_pr = acc_investments.reff_number
//     LEFT JOIN acc_investment_details ON acc_investments.reff_number = acc_investment_details.reff_number
//     JOIN departments ON acc_investments.applicant_department = departments.department_name 
//     WHERE
//     acc_investments.deleted_at IS NULL 
//     AND acc_investments.receive_date IS NOT NULL
//     AND acc_investment_details.sudah_po IS NOT NULL 
//     AND acc_purchase_orders.deleted_at IS NULL
//     AND acc_purchase_order_details.`status` IS NULL 
//     AND DATE_FORMAT( tgl_po, '%Y-%m' ) BETWEEN '".$datefrom."' AND '".$dateto."' 
//     ".$dep." 
//     ORDER BY
//     tgl_po ASC 
//     ");

$response = array(
    'status' => true,
    'datas' => $data,
    'data_investment_belum_po' => $data_investment_belum_po
    // 'data_po_belum_receive' => $data_po_belum_receive
);

return Response::json($response); 
}


public function detailMonitoringInv(Request $request){

  $week = $request->get("week");
  $status = $request->get("status");
  $tglfrom = $request->get("tglfrom");
  $tglto = $request->get("status");
  $department = $request->get("department");

  $status_sign = "";

  if ($status == "Investment Incompleted") {
      $status_sign = "and posisi != 'finished'";
  }
  else if ($status == "Investment Completed") {
      $status_sign = "and posisi = 'finished'";
  }

  $qry = "SELECT acc_investments.*, weekly_calendars.week_name FROM acc_investments JOIN weekly_calendars on acc_investments.submission_date = weekly_calendars.week_date WHERE acc_investments.deleted_at is null and week_name = '".$week."' ".$department." ".$status_sign." ORDER BY acc_investments.id DESC";


  $invest = DB::select($qry);

  return DataTables::of($invest)
  ->editColumn('submission_date', function ($invest)
  {
    return date('d F Y', strtotime($invest->submission_date));
})

  ->editColumn('supplier_code', function ($invest)
  {
    return $invest->supplier_code.' - '.$invest->supplier_name;
})
  ->editColumn('file', function ($invest)
  {
    $data = json_decode($invest->file);

    $fl = "";

    if ($invest->file != null)
    {
        for ($i = 0;$i < count($data);$i++)
        {
            $fl .= '<a href="files/investment/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
        }
    }
    else
    {
        $fl = '-';
    }

    return $fl;
})
  ->editColumn('status', function ($invest)
  {
    $id = $invest->id;

    if ($invest->posisi == "user" && $invest->status == "approval")
    {
        return '<label class="label label-danger">Belum Dikirim</label>';
    }
    if ($invest->posisi == "user" && $invest->status == "comment")
    {
        return '<label class="label label-warning">Commented</label>';
    }
    else if ($invest->posisi == "acc_budget" || $invest->posisi == "acc_pajak")
    {
        return '<label class="label label-warning">Verifikasi Oleh Accounting</label>';
    }
    else if ($invest->posisi == "manager")
    {
        return '<label class="label label-warning">Diverifikasi Manager</label>';
    }
    else if ($invest->posisi == "dgm")
    {
        return '<label class="label label-warning">Diverifikasi DGM</label>';
    }
    else if ($invest->posisi == "gm")
    {
        return '<label class="label label-warning">Diverifikasi GM</label>';
    }
    else if ($invest->posisi == "manager_acc")
    {
        return '<label class="label label-warning">Diverifikasi Manager Accounting</label>';
    }
    else if ($invest->posisi == "direktur_acc")
    {
        return '<label class="label label-warning">Diverifikasi Direktur Accounting</label>';
    }
    else if ($invest->posisi == "presdir")
    {
        return '<label class="label label-warning">Diverifikasi Presdir</label>';
    }
    else if ($invest->posisi == "finished")
    {
        return '<label class="label label-success">Telah Diverifikasi</label>';
    }

})
  ->addColumn('action', function ($invest)
  {
    $id = $invest->id;

    if ($invest->posisi == "user")
    {
        return '
        <a href="detail/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a>
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report PDF</a>
        ';
    }
    else if ($invest->posisi == "acc_budget" || $invest->posisi == "acc_pajak")
    {
        return '<a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report PDF</a>';

    }
    else if ($invest->posisi == "acc" || $invest->posisi == "manager" || $invest->posisi == "dgm" || $invest->posisi == "gm" || $invest->posisi == "manager_acc" || $invest->posisi == "direktur_acc" || $invest->posisi == "presdir" || $invest->posisi == "finished")
    {
        return '
        <a href="report/' . $id . '" target="_blank" class="btn btn-warning btn-md" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report Investment</a>
        ';
    }
})

  ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file', 'supplier_code' => 'supplier_code'])
  ->make(true);
}

public function detailMonitoringInvTable(Request $request){

    $reff_number = $request->get("reff");
    $status = $request->get("status");
    $tglfrom = $request->get("tglfrom");
    $tglto = $request->get("status");
    $department = $request->get("department");

    $status_sign = "";

    if ($status == "Belum PO") {
        $status_sign = "and sudah_po is null";
    }
    else if ($status == "Sudah PO") {
        $status_sign = "and sudah_po is not null";
    }

    $qry = "SELECT acc_investments.reff_number,acc_investments.submission_date, acc_investment_details.* from acc_investments left join acc_investment_details on acc_investments.reff_number = acc_investment_details.reff_number WHERE acc_investment_details.deleted_at is NULL and acc_investments.reff_number = '".$reff_number."' ".$department." ".$status_sign." ";

    $inv = DB::select($qry);

    return DataTables::of($inv)
    ->editColumn('submission_date', function ($inv)
    {
        return $inv->submission_date;
    })
    ->editColumn('status', function ($inv)
    {
        if ($inv->sudah_po == null) {
            return '<span class="label label-danger">Belum PO</span>';
        }
        else if ($inv->sudah_po != null) {
            return '<span class="label label-success">Sudah PO</span>';
        }
    })
    ->rawColumns(['status' => 'status'])
    ->make(true);
}


public function detailMonitoringInvActual(Request $request){

    $reff_number = $request->get("reff");
    $status = $request->get("status");
    $tglfrom = $request->get("tglfrom");
    $tglto = $request->get("status");
    $department = $request->get("department");

    $status_sign = "";

    if ($status == "Belum Datang") {
        $status_sign = "and acc_purchase_order_details.`status` is null";
    }
    else if ($status == "Sudah Datang") {
        $status_sign = "and acc_purchase_order_details.`status` is not null";
    }

    $inv = DB::select("
        SELECT acc_purchase_orders.*, acc_purchase_order_details.*, acc_investments.applicant_department FROM acc_purchase_orders
        LEFT JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po
        LEFT JOIN acc_investments ON acc_purchase_order_details.no_pr = acc_investments.reff_number
        WHERE
        acc_purchase_orders.deleted_at IS NULL
        AND acc_purchase_order_details.no_pr = '".$reff_number."'
        AND DATE_FORMAT( submission_date, '%Y-%m' ) BETWEEN '".$tglfrom."' 
        AND '".$tglto."' ".$department." ".$status_sign."

        ");

    return DataTables::of($inv)

    ->editColumn('tgl_po', function ($inv)
    {
        return $inv->tgl_po;
    })
    ->editColumn('status', function ($inv)
    {
        if ($inv->status == null) {
            return '<span class="label label-danger">Belum Close</span>';
        }
        else if ($inv->status != null) {
            return '<span class="label label-success">Sudah Close</span>';
        }

    })
    ->rawColumns(['status' => 'status'])
    ->make(true);
}




    //==================================//
    //    Upload Transaksi Diluar PO    //
    //==================================//

public function upload_transaksi()
{
    $title = 'Upload Transaksi';
    $title_jp = '取引処理のアップロード';

    $status = AccActualLog::select('*')->whereNull('acc_actual_logs.deleted_at')
    ->distinct()
    ->get();

    return view('accounting_purchasing.master.upload_transaksi', array(
        'title' => $title,
        'title_jp' => $title_jp,
    ))->with('page', 'Upload Transaksi')
    ->with('head', 'Upload Transaksi');
}

public function fetch_upload_transaksi(Request $request)
{
    $actual = AccActualLog::orderBy('acc_actual_logs.id', 'desc');

    if ($request->get('periode') != null)
    {
        $actual = $actual->whereIn('acc_actual_logs.periode', $request->get('periode'));
    }

    if ($request->get('month') != null)
    {
        $actual = $actual->where('acc_actual_logs.month_date', $request->get('month'));
    }

    $actual = $actual->select('*')->get();

    return DataTables::of($actual)

    ->editColumn('amount', function ($actual)
    {
        if ($actual->currency == "USD") {
            return "$ ".$actual->amount;   
        } else if ($actual->currency == "JPY") {
            return "¥ ".$actual->amount;   
        } else if ($actual->currency == "IDR") {
            return "Rp. ".$actual->amount;   
        }
    })

    ->addColumn('action', function ($actual)
    {
        $id = $actual->id;

        return ' 
        <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Details" onclick="modalDelete('.$id.')"><i class="fa fa-trash"></i> Delete</button>
        ';
    })

    ->rawColumns(['action' => 'action'])
    ->make(true);
}


public function import_transaksi(Request $request){
    if($request->hasFile('upload_file')) {
        try{                
            $file = $request->file('upload_file');
            $file_name = 'transaksi_'. date("ymd_h.i") .'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/transaksi/'), $file_name);
            $excel = public_path('uploads/transaksi/') . $file_name;

            $rows = Excel::load($excel, function($reader) {
                $reader->noHeading();
                    //Skip Header
                $reader->skipRows(1);
            })->get();

            $rows = $rows->toArray();

            for ($i=0; $i < count($rows); $i++) {
                if ($rows[$i][0] != "") {
                    $periode = "";
                    $document_no = "";
                    $type = "";                    
                    $description = "";            
                    $reference = "";
                    $gl_number = "";
                    $post_date = "";    
                    $local_amount = "";
                    $local_currency = "";
                    $amount = "";
                    $currency = "";
                    $budget_no = "";
                    $investment_no = "";
                    $month_date = "";

                    $periode  = $rows[$i][0];
                    $document_no = $rows[$i][1];
                    $type = $rows[$i][2];                    
                    $description = $rows[$i][3];            
                    $reference = $rows[$i][4];
                    $gl_number = $rows[$i][5];
                    $post_date = $rows[$i][6];    
                    $local_amount = $rows[$i][7];
                    $local_currency = $rows[$i][8];
                    $amount = $rows[$i][9];
                    $currency = $rows[$i][10];
                    $budget_no = $rows[$i][11];
                    $investment_no = $rows[$i][12];
                    $month_date = $rows[$i][13];

                    $data2 = AccActualLog::create([
                        'periode' => $periode,
                        'document_no' => $document_no,
                        'type' => $type,
                        'description' => $description,
                        'reference' => $reference,
                        'gl_number' => $gl_number,
                        'post_date' => $post_date,
                        'local_amount' => $local_amount,
                        'local_currency' => $local_currency,
                        'amount' => $amount,
                        'currency' => $currency,
                        'budget_no' => $budget_no,
                        'investment_no' => $investment_no,
                        'month_date' => $month_date,
                        'created_by' => Auth::id()
                    ]);

                    $data2->save();

                    if ($budget_no != "" || $budget_no != null) {
                        $bulan = strtolower(date("M",strtotime($post_date)));
                        $sisa_bulan = $bulan.'_sisa_budget';

                        $budgetdata = AccBudget::where('budget_no','=',$budget_no)->where('periode','=', $periode)->first();

                            //Kurangi Budget Skrg Dengan Actual
                        $total = $budgetdata->$sisa_bulan - $local_amount;

                        $updatebudget = AccBudget::where('budget_no','=',$budget_no)->where('periode','=', $periode)
                        ->update([
                            $sisa_bulan => $total
                        ]);
                    }


                }
            }       

            $response = array(
                'status' => true,
                'message' => 'Upload Berhasil',
            );
            return Response::json($response);

        }catch(\Exception $e){
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }else{
        $response = array(
            'status' => false,
            'message' => 'Upload failed, File not found',
        );
        return Response::json($response);
    }
}


public function delete_transaksi(Request $request)
{
    $actual = AccActualLog::find($request->get("id"));


    $bulan = strtolower(date("M",strtotime($actual->post_date)));
    $sisa_bulan = $bulan.'_sisa_budget';

    $budgetdata = AccBudget::where('budget_no','=',$actual->budget_no)->where('periode','=', $actual->periode)->first();

        //Kurangi Budget Skrg Dengan Actual
    $total = $budgetdata->$sisa_bulan + $actual->local_amount;

    $updatebudget = AccBudget::where('budget_no','=',$actual->budget_no)->where('periode','=', $actual->periode)
    ->update([
        $sisa_bulan => $total
    ]);

    $actual->delete();

    $response = array(
        'status' => true
    );

    return Response::json($response);
}

public function exportOutstandingPR(Request $request){

    $time = date('d-m-Y H;i;s');

    $tanggal = "";

    if (strlen($request->get('date_pr')) > 0)
    {
        $datepr = date('Y-m-d', strtotime($request->get('date_pr')));
    }

    $pr_detail = db::select(
        "SELECT
        acc_purchase_requisitions.no_budget,
        '' AS no_po,
        acc_purchase_requisitions.no_pr,
        acc_purchase_requisition_items.item_desc,
        acc_purchase_requisitions.department,
        acc_purchase_requisition_items.item_currency,
        acc_purchase_requisition_items.item_qty,
        acc_purchase_requisition_items.item_price,
        acc_purchase_requisition_items.item_amount,
        DATE_FORMAT(acc_purchase_requisitions.submission_date,'%Y-%m-%d') as submission_date,
        monthname( submission_date ) AS periode 
        FROM
        acc_purchase_requisitions
        JOIN acc_purchase_requisition_items ON acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr 
        WHERE
        acc_purchase_requisitions.deleted_at IS NULL 
        AND sudah_po IS NULL 
        AND submission_date <= '".$datepr."'

        UNION ALL

        SELECT
        acc_purchase_requisitions.no_budget,
        acc_purchase_order_details.no_po,
        acc_purchase_requisitions.no_pr,
        acc_purchase_requisition_items.item_desc,
        acc_purchase_requisitions.department,
        acc_purchase_requisition_items.item_currency,
        acc_purchase_requisition_items.item_qty,
        acc_purchase_requisition_items.item_price,
        acc_purchase_requisition_items.item_amount,
        DATE_FORMAT(acc_purchase_requisitions.submission_date,'%Y-%m-%d') as submission_date,
        monthname( submission_date ) AS periode 
        FROM
        acc_purchase_requisitions
        JOIN acc_purchase_requisition_items ON acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr
        JOIN acc_purchase_order_details ON acc_purchase_order_details.no_pr = acc_purchase_requisition_items.no_pr and acc_purchase_order_details.nama_item = acc_purchase_requisition_items.item_desc
        WHERE
        acc_purchase_requisitions.deleted_at IS NULL 
        AND sudah_po IS NOT NULL 
        AND submission_date <= '".$datepr."' AND DATE_FORMAT(acc_purchase_order_details.created_at,'%Y-%m-%d') > '".$datepr."'");

    $data = array(
        'pr_detail' => $pr_detail
    );

    ob_clean();

    Excel::create('Outstanding PR List '.$datepr, function($excel) use ($data){
        $excel->sheet('Location', function($sheet) use ($data) {
          return $sheet->loadView('accounting_purchasing.report.outstanding_pr_excel', $data);
      });
    })->export('xlsx');
}

public function exportOutstandingInvestment(Request $request){
    $time = date('d-m-Y H;i;s');
    $tanggal = "";

    if (strlen($request->get('date_inv')) > 0)
    {
        $dateinv = date('Y-m-d', strtotime($request->get('date_inv')));
    }

    $inv_detail = db::select(
        "
        SELECT
        acc_investment_budgets.budget_no,
        '' AS no_po,
        acc_investments.reff_number,
        acc_investment_details.detail,
        acc_investments.applicant_department,
        acc_investments.currency,
        acc_investment_details.qty,
        acc_investment_details.price,
        acc_investment_details.amount,
        DATE_FORMAT(acc_investments.submission_date,'%Y-%m-%d') as submission_date,
        monthname( submission_date ) AS periode 
        FROM
        acc_investments
        JOIN acc_investment_details ON acc_investments.reff_number = acc_investment_details.reff_number 
        join acc_investment_budgets ON acc_investments.reff_number = acc_investment_budgets.reff_number
        WHERE
        acc_investments.deleted_at IS NULL 
        AND sudah_po IS NULL 
        AND submission_date <= '".$dateinv."'


        UNION ALL

        SELECT
        acc_investment_budgets.budget_no,
        acc_purchase_order_details.no_po,
        acc_investments.reff_number,
        acc_investment_details.detail,
        acc_investments.applicant_department,
        acc_investments.currency,
        acc_investment_details.qty,
        acc_investment_details.price,
        acc_investment_details.amount,
        DATE_FORMAT(acc_investments.submission_date,'%Y-%m-%d') as submission_date,
        monthname( submission_date ) AS periode 
        FROM
        acc_investments
        JOIN acc_investment_details ON acc_investments.reff_number = acc_investment_details.reff_number
        join acc_investment_budgets ON acc_investments.reff_number = acc_investment_budgets.reff_number
        JOIN acc_purchase_order_details ON acc_purchase_order_details.no_pr = acc_investment_details.reff_number and acc_purchase_order_details.nama_item = acc_investment_details.detail 
        WHERE
        acc_investments.deleted_at IS NULL 
        AND sudah_po IS NOT NULL 
        AND submission_date <= '".$dateinv."' AND DATE_FORMAT(acc_purchase_order_details.created_at,'%Y-%m-%d') > '".$dateinv."'");

    $data = array(
        'inv_detail' => $inv_detail
    );

    ob_clean();

    Excel::create('Outstanding Investment List '.$dateinv, function($excel) use ($data){
        $excel->sheet('Location', function($sheet) use ($data) {
          return $sheet->loadView('accounting_purchasing.report.outstanding_inv_excel', $data);
      });
    })->export('xlsx');
}

public function exportOutstandingPO(Request $request){
    $time = date('d-m-Y H;i;s');
    $tanggal = "";

    if (strlen($request->get('date_po')) > 0)
    {
        $datepo = date('Y-m-d', strtotime($request->get('date_po')));
    }

    $po_detail = db::select(
        "
        SELECT
        acc_purchase_orders.remark,
        acc_purchase_requisitions.department AS department_pr,
        acc_investments.applicant_department AS department_investment,
        acc_purchase_order_details.budget_item,
        acc_purchase_order_details.no_po,
        acc_purchase_order_details.no_pr,
        acc_purchase_order_details.nama_item,
        acc_purchase_orders.currency,
        acc_purchase_order_details.qty,
        acc_purchase_order_details.goods_price,
        acc_purchase_order_details.service_price,
        DATE_FORMAT( tgl_po, '%Y-%m-%d' ) AS tgl_po,
        monthname( tgl_po ) AS periode 
        FROM
        acc_purchase_orders
        JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po
        LEFT JOIN acc_purchase_requisitions ON acc_purchase_order_details.no_pr = acc_purchase_requisitions.no_pr
        LEFT JOIN acc_investments ON acc_purchase_order_details.no_pr = acc_investments.reff_number
        JOIN acc_budget_histories ON acc_budget_histories.category_number = acc_purchase_order_details.no_pr and acc_budget_histories.no_item = acc_purchase_order_details.nama_item
        WHERE
        acc_purchase_orders.deleted_at IS NULL 
        AND acc_budget_histories.`status` != 'Actual'
        AND DATE_FORMAT( tgl_po, '%Y-%m-%d' ) <= '".$datepo."'");

    $data = array(
        'po_detail' => $po_detail
    );

    ob_clean();

    Excel::create('Outstanding PO List '.$datepo, function($excel) use ($data){
        $excel->sheet('Location', function($sheet) use ($data) {
          return $sheet->loadView('accounting_purchasing.report.outstanding_po_excel', $data);
      });
    })->export('xlsx');
}

public function outstanding_all_equipment()
{
    $title = 'Outstanding PR PO Investment';
    $title_jp = '';

    return view('accounting_purchasing.master.outstanding', array(
        'title' => $title,
        'title_jp' => $title_jp,
    ))->with('page', 'Data Outstanding')
    ->with('head', 'Data Outstanding');
}

    //==================================//
    //          Transfer Budget         //
    //==================================//

public function transfer_budget()
{
    $title = 'Transfer Budget';
    $title_jp = '予算流用';

    $status = AccBudgetTransfer::select('*')
    ->whereNull('acc_budget_transfers.deleted_at')
    ->distinct()
    ->get();

    $budgets = AccBudget::select('acc_budgets.budget_no', 'acc_budgets.description')
    ->where('periode','=','FY198')
    ->distinct()
    ->get();

    return view('accounting_purchasing.master.transfer_budget', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'budgets' => $budgets
    ))->with('page', 'Transfer Budget')
    ->with('head', 'Transfer Budget');
}

public function transfer_budget_post(Request $request)
{
    try {

        $budget_from = $request->get('budget_from');
        $budget_to = $request->get('budget_to');

        $department_from = AccBudget::select('department')
        ->where('budget_no','=',$budget_from)
        ->first();

        $department_to = AccBudget::select('department')
        ->where('budget_no','=',$budget_to)
        ->first();

        $manager_from = null;
        $manager_name_from = null;

        $manager_to = null;
        $manager_name_to = null;

        $posisi = null;

        if($department_from->department == "Production Engineering Department")
        {
            $managfrom = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Maintenance Department' and position = 'manager'");
        }
        else if($department_from->department == "Purchasing Control Department")
        {
            $managfrom = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Procurement Department' and position = 'manager'");
        }
        else if($department_from->department == "General Affairs Department")
        {
            $managfrom = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Human Resources Department' and position = 'manager'");
        }
        else if($department_from->department == "Management Information System Department")
        {
            $managfrom = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and position = 'Deputy General Manager'");
        }
        else
        {
            $managfrom = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$department_from->department."' and position = 'manager'");
        }


            //cek manager ada atau tidak
        if ($managfrom != null)
        {
            $posisi = "manager_from";

            foreach ($managfrom as $mgfrom)
            {
                $manager_from = $mgfrom->employee_id;
                $manager_name_from = $mgfrom->name;
            }
        }



        if($department_to->department == "Production Engineering Department")
        {
            $managto = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Maintenance Department' and position = 'manager'");
        }
        else if($department_to->department == "Purchasing Control Department")
        {
            $managto = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Procurement Department' and position = 'manager'");
        }
        else if($department_to->department == "General Affairs Department")
        {
            $managto = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Human Resources Department' and position = 'manager'");
        }
        else if($department_to->department == "Management Information System Department")
        {
            $managto = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and position = 'Deputy General Manager'");
        }
        else
        {
            $managto = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$department_to->department."' and position = 'manager'");
        }
            //cek manager ada atau tidak
        if ($managto != null)
        {
            foreach ($managto as $mgto)
            {
                $manager_to = $mgto->employee_id;
                $manager_name_to = $mgto->name;
            }
        }

        $id_user = Auth::id();

        $acc = AccBudgetTransfer::create([
           'request_date' => date('Y-m-d'),
           'budget_from' => $budget_from,
           'budget_to' => $budget_to,
           'amount' => $request->get('amount'),
           'approval_f' => $manager_from,
           'approval_t' => $manager_to,
           'approval_from' => $manager_name_from,
           'approval_to' => $manager_name_to,
           'posisi' => $posisi,
           'created_by' => $id_user
       ]);

        $acc->save();

        $nik_manager = explode("-",$acc->approval_f);

        $mails = "select distinct email from acc_budget_transfers join users on acc_budget_transfers.approval_f = users.username where acc_budget_transfers.id = ".$acc->id;

        $mailtoo = DB::select($mails);

        $isimail = "select * FROM acc_budget_transfers where acc_budget_transfers.id =". $acc->id;
        $isitransfer = db::select($isimail);

        Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($isitransfer, 'transfer_budget'));

        $response = array(
            'status' => true,
            'datas' => "Berhasil",
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

public function transfer_budget_post_new(Request $request)
{
    try {

        $budget_from = $request->get('budget_from');
        $budget_to = $request->get('budget_to');
        $amount = $request->get('amount');
        $id_user = Auth::id();

        $date = date('Y-m-d');
                //FY
        $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$date'");

        foreach ($fy as $fys) {
            $fiscal = $fys->fiscal_year;
        }

        $sisa_bulan = strtolower(date('M')).'_sisa_budget';
        $sisa_simulasi_bulan = strtolower(date('M')).'_after_adj';

        $budget_from_awal = AccBudget::where('budget_no', $budget_from)->where('periode', $fiscal)->first();

        // Dikurangi dulu dari budget awal
        $totalfrom = $budget_from_awal->$sisa_bulan - $amount; //sisa budget
        $simulasi_from = $budget_from_awal->$sisa_simulasi_bulan - $amount;
        $amountfrom = $budget_from_awal->amount - $amount;

        if ($totalfrom < 0) {
            $response = array(
                'status' => false,
                'datas' => "Budget Awal Tidak Mencukupi",
            );
            return Response::json($response);
        }

        $dataupdate = AccBudget::where('budget_no', $budget_from)->where('periode', $fiscal)->update([
            $sisa_bulan => $totalfrom,
            $sisa_simulasi_bulan => $simulasi_from,
            'amount' => $amountfrom
        ]);

        // Ditambah Ke Budget Tujuan

        $budget_to_akhir = AccBudget::where('budget_no', $budget_to)->where('periode', $fiscal)->first();

        $totalto = $budget_to_akhir->$sisa_bulan + $amount; //sisa budget 
        $simulasito = $budget_to_akhir->$sisa_simulasi_bulan + $amount;
        $amountto = $budget_to_akhir->amount + $amount;

        $dataupdate = AccBudget::where('budget_no', $budget_to)->where('periode', $fiscal)->update([
            $sisa_bulan => $totalto,
            $sisa_simulasi_bulan => $simulasito,
            'amount' => $amountto
        ]);

        $acc = AccBudgetTransfer::create([
            'request_date' => date('Y-m-d'),
            'budget_from' => $budget_from,
            'budget_to' => $budget_to,
            'amount' => $amount,
            'note' => $request->get('note'),
            'approval_f' => null,
            'approval_t' => null,
            'approval_from' => null,
            'approval_to' => null,
            'posisi' => null,
            'created_by' => $id_user
        ]);

        $acc->save();


        $response = array(
            'status' => true,
            'datas' => "Berhasil",
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

public function fetch_transfer_budget(){
    $data = db::select('select * from acc_budget_transfers where deleted_at is null order by id desc');

    $response = array(
        'status' => true,
        'datas' => $data
    );

    return Response::json($response);
}

public function transfer_approvalfrom($id){

    $transfer = AccBudgetTransfer::find($id);

    try{
        if ($transfer->posisi == "manager_from")
        {
            $transfer->posisi = "manager_to";
            $transfer->date_approval_from = "Approval_".date('Y-m-d');

            $mailto = "select distinct email from acc_budget_transfers join users on acc_budget_transfers.approval_t = users.username where acc_budget_transfers.id = '".$id."'";
            $mails = DB::select($mailto);
            foreach ($mails as $mail)
            {
                $mailtoo = $mail->email;
            }
            $transfer->save();

            $isimail = "select * from acc_budget_transfers where acc_budget_transfers.id = ".$id;

            $transfer_isi = db::select($isimail);

            Mail::to($mailtoo)->send(new SendEmail($transfer_isi, 'transfer_budget'));

            $message = 'Transfer Budget Dari '.$transfer->budget_from.' Ke '.$transfer->budget_to ;
            $message2 ='Berhasil di approve';
        }
        else{
            $message = 'Transfer Budget Dari '.$transfer->budget_from.' Ke '.$transfer->budget_to ;
            $message2 ='Sudah di approve/reject';
        }

        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $transfer->budget_from,
            'message' => $message,
            'message2' => $message2,
        ))->with('page', 'Approval');

    } catch (Exception $e) {
        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $transfer->budget_to,
            'message' => 'Error',
            'message2' => $e->getMessage(),
        ))->with('page', 'Approval');
    }
}


public function transfer_approvalto($id){

    $transfer = AccBudgetTransfer::find($id);

    try{
        if ($transfer->posisi == "manager_to")
        {
            $transfer->posisi = "acc";
            $transfer->date_approval_to = "Approval_".date('Y-m-d');

                //Mulai proses perhitungan Budget

            $date = date('Y-m-d');
                //FY
            $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$date'");

            foreach ($fy as $fys) {
                $fiscal = $fys->fiscal_year;
            }

            $sisa_bulan = strtolower(date('M')).'_sisa_budget';

            $budget_from = AccBudget::where('budget_no', $transfer->budget_from)->where('periode', $fiscal)->first();

                // Dikurangi dulu dari budget awal
                $totalfrom = $budget_from->$sisa_bulan - $transfer->amount; //sisa budget 

                $dataupdate = AccBudget::where('budget_no', $transfer->budget_from)->where('periode', $fiscal)->update([
                    $sisa_bulan => $totalfrom
                ]);

                // Ditambah Ke Budget Tujuan

                $budget_to = AccBudget::where('budget_no', $transfer->budget_to)->where('periode', $fiscal)->first();

                $totalto = $budget_to->$sisa_bulan + $transfer->amount; //sisa budget 

                $dataupdate = AccBudget::where('budget_no', $transfer->budget_to)->where('periode', $fiscal)->update([
                    $sisa_bulan => $totalto
                ]);

                $mailto = "select distinct email from users where username = 'PI0902001'"; // kirim bu laila
                $mails = DB::select($mailto);
                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }

                $transfer->save();

                $isimail = "select * from acc_budget_transfers where acc_budget_transfers.id = ".$id;

                $transfer_isi = db::select($isimail);

                Mail::to($mailtoo)->send(new SendEmail($transfer_isi, 'transfer_budget'));

                $message = 'Transfer Budget Dari '.$transfer->budget_from.' Ke '.$transfer->budget_to ;
                $message2 ='Berhasil di approve';
            }
            else{

                $message = 'Transfer Budget Dari '.$transfer->budget_from.' Ke '.$transfer->budget_to ;
                $message2 ='Sudah di approve/reject';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $transfer->budget_from,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $transfer->budget_to,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }


    //==================================//
    //      Verifikasi Investment       //
    //==================================//
    public function verifikasi_investment($id)
    {
        $inv = AccInvestment::find($id);

        $path = '/investment_list/'.$inv->pdf;
        $file_path = asset($path);

        return view('accounting_purchasing.verifikasi.investment_verifikasi', array(
            'investment' => $inv,
            'file_path' => $file_path,
        ))->with('page', 'Investment');
    }

    public function approval_investment(Request $request, $id)
    {
        try {

            $approve = $request->get('approve');

            if ($approve == "1") {

                $investment = AccInvestment::find($id);

                if ($investment->posisi == "manager")
                {

                    if ($investment->approval_dgm != null) {
                        $investment->posisi = "dgm";
                        $investment->approval_manager = $investment->approval_manager."/Approved/".date('Y-m-d H:i:s');

                        $dgm_id = explode("/", $investment->approval_dgm);
                        $mailto = "select distinct email from users where username = '" .$dgm_id[0]. "'";

                        $mails = DB::select($mailto);

                        foreach ($mails as $mail)
                        {
                            $mailtoo = $mail->email;
                        }
                    }
                    else{
                        $investment->posisi = "gm";
                        $investment->approval_manager = $investment->approval_manager."/Approved/".date('Y-m-d H:i:s');

                        $gm_id = explode("/", $investment->approval_gm);
                        $mailto = "select distinct email from users where username = '" .$gm_id[0]. "'";

                        $mails = DB::select($mailto);

                        foreach ($mails as $mail)
                        {
                            $mailtoo = $mail->email;
                        }
                    }
                }

                else if ($investment->posisi == "dgm")
                {
                    $investment->posisi = "gm";
                    $investment->approval_dgm = $investment->approval_dgm."/Approved/".date('Y-m-d H:i:s');

                    $gm_id = explode("/", $investment->approval_gm);
                    $mailto = "select distinct email from users where username = '" .$gm_id[0]. "'";

                    $mails = DB::select($mailto);

                    foreach ($mails as $mail)
                    {
                        $mailtoo = $mail->email;
                    }
                }

                else if ($investment->posisi == "gm")
                {

                    $investment->posisi = "manager_acc";
                    $investment->approval_gm = $investment->approval_gm."/Approved/".date('Y-m-d H:i:s');

                    $manager_acc_id = explode("/", $investment->approval_manager_acc);
                    $mailto = "select distinct email from users where username = '" .$manager_acc_id[0]. "'";

                    $mails = DB::select($mailto);

                    foreach ($mails as $mail)
                    {
                        $mailtoo = $mail->email;
                    }
                }

                else if ($investment->posisi == "manager_acc")
                {

                    $investment->posisi = "direktur_acc";
                    $investment->approval_manager_acc = $investment->approval_manager_acc."/Approved/".date('Y-m-d H:i:s');

                    $direktur_acc_id = explode("/", $investment->approval_dir_acc);
                    $mailto = "select distinct email from users where username = '" .$direktur_acc_id[0]. "'";

                    $mails = DB::select($mailto);

                    foreach ($mails as $mail)
                    {
                        $mailtoo = $mail->email;
                    }
                }

                else if ($investment->posisi == "direktur_acc")
                {

                    $investment->posisi = "presdir";
                    $investment->approval_dir_acc = $investment->approval_dir_acc."/Approved/".date('Y-m-d H:i:s');

                    $presdir_id = explode("/", $investment->approval_presdir);
                    $mailto = "select distinct email from users where username = '" .$presdir_id[0]. "'";

                    $mails = DB::select($mailto);

                    foreach ($mails as $mail)
                    {
                        $mailtoo = $mail->email;
                    }
                }

                else if ($investment->posisi == "presdir")
                {

                    $investment->posisi = "finished";
                    $investment->approval_presdir = $investment->approval_presdir."/Approved/".date('Y-m-d H:i:s');

                    //kirim email ke Mas Shega & Mas Hamzah
                    $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = 'Purchasing Control Department' and (employee_id = 'PI1810020'  or employee_id = 'PI0904006')";
                    $mailtoo = DB::select($mails);
                }

                $investment->save();

                $judul = substr($investment->reff_number, 0, 7);

                $detail_inv = AccInvestment::select('acc_investments.*','acc_investment_details.no_item', 'acc_investment_details.detail', 'acc_investment_details.qty', 'acc_investment_details.price', 'acc_investment_details.vat_status', 'acc_investment_details.amount')
                ->leftJoin('acc_investment_details', 'acc_investments.reff_number', '=', 'acc_investment_details.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $inv_budget = AccInvestment::join('acc_investment_budgets', 'acc_investments.reff_number', '=', 'acc_investment_budgets.reff_number')
                ->where('acc_investments.id', '=', $id)
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('Legal', 'potrait');

                $pdf->loadView('accounting_purchasing.report.report_investment', array(
                    'inv' => $detail_inv,
                    'inv_budget' => $inv_budget
                ));

                $pdf->save(public_path() . "/investment_list/INV_".$judul.".pdf");

                $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$investment->id;
                $investe = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($investe, 'investment'));

                return redirect('/investment/verifikasi/'.$id)->with('status', 'Investment Approved Successfully')
                ->with('page', 'Investment');
            }
            else
            {
                return redirect('/investment/verifikasi/' . $id)->with('error', 'Investment Not Approved')
                ->with('page', 'Investment');
            }
            
        } catch (Exception $e) {

            return redirect('/investment/verifikasi/' . $id)->with('error', 'Investment Not Approved')
            ->with('page', 'Investment');

        }
        
    }

    public function reject_investment(Request $request, $id)
    {
        try {

            $reject_note = $request->get('alasan');

            $investment = AccInvestment::find($id);
            $investment->reject_note = $reject_note;

            if ($investment->posisi == "acc_budget") {
                $investment->posisi = "user";
                $investment->reject = "acc_budget/".date('Y-m-d H:i:s');
            }
            else if($investment->posisi == "acc_pajak"){
                $investment->posisi = "user";
                $investment->reject = "acc_pajak/".date('Y-m-d H:i:s');
            }
            else if($investment->posisi == "manager"){
                $investment->posisi = "user";
                $investment->reject = "manager/".date('Y-m-d H:i:s');
            }
            else if($investment->posisi == "dgm"){
                $investment->posisi = "user";
                $investment->reject = "dgm/".date('Y-m-d H:i:s');

                $manager = explode("/", $investment->approval_manager);
                if(count($manager) > 1){
                    $investment->approval_manager = $manager[0]."/".$manager[1];
                }
            }
            else if($investment->posisi == "gm"){
                $investment->posisi = "user";
                $investment->reject = "gm/".date('Y-m-d H:i:s');

                $manager = explode("/", $investment->approval_manager);
                $dgm = explode("/", $investment->approval_dgm);

                if(count($manager) > 1){
                    $investment->approval_manager = $manager[0]."/".$manager[1];
                }
                if(count($dgm) > 1){
                    $investment->approval_dgm = $dgm[0]."/".$dgm[1];
                } 
            }
            else if($investment->posisi == "manager_acc"){
                $investment->posisi = "user";
                $investment->reject = "manager_acc/".date('Y-m-d H:i:s');

                $manager = explode("/", $investment->approval_manager);
                $dgm = explode("/", $investment->approval_dgm);
                $gm = explode("/", $investment->approval_gm);

                if(count($manager) > 1){
                    $investment->approval_manager = $manager[0]."/".$manager[1];
                }
                if(count($dgm) > 1){
                    $investment->approval_dgm = $dgm[0]."/".$dgm[1];
                }
                if(count($gm) > 1){
                    $investment->approval_gm = $gm[0]."/".$gm[1];
                }

            }
            else if($investment->posisi == "direktur_acc"){
                $investment->posisi = "user";
                $investment->reject = "direktur_acc/".date('Y-m-d H:i:s');

                $manager = explode("/", $investment->approval_manager);
                $dgm = explode("/", $investment->approval_dgm);
                $gm = explode("/", $investment->approval_gm);
                $manager_acc = explode("/", $investment->approval_manager_acc);

                if(count($manager) > 1){
                    $investment->approval_manager = $manager[0]."/".$manager[1];
                }
                if(count($dgm) > 1){
                    $investment->approval_dgm = $dgm[0]."/".$dgm[1];
                }
                if(count($gm) > 1){
                    $investment->approval_gm = $gm[0]."/".$gm[1];
                } 
                if(count($manager_acc) > 1){
                    $investment->approval_manager_acc = $manager_acc[0]."/".$manager_acc[1];
                } 

            }
            else if($investment->posisi == "presdir"){
                $investment->posisi = "user";
                $investment->reject = "presdir/".date('Y-m-d H:i:s');

                $manager = explode("/", $investment->approval_manager);
                $dgm = explode("/", $investment->approval_dgm);
                $gm = explode("/", $investment->approval_gm);
                $manager_acc = explode("/", $investment->approval_manager_acc);
                $dir_acc = explode("/", $investment->approval_dir_acc);

                if(count($manager) > 1){
                    $investment->approval_manager = $manager[0]."/".$manager[1];
                }
                if(count($dgm) > 1){
                    $investment->approval_dgm = $dgm[0]."/".$dgm[1];
                }
                if(count($gm) > 1){
                    $investment->approval_gm = $gm[0]."/".$gm[1];
                } 
                if(count($manager_acc) > 1){
                    $investment->approval_manager_acc = $manager_acc[0]."/".$manager_acc[1];
                } 
                if(count($dir_acc) > 1){
                    $investment->approval_dir_acc = $dir_acc[0]."/".$dir_acc[1];
                }

            }

            $investment->save();

            $isimail = "select acc_investments.*, acc_investment_budgets.budget_no, acc_investment_budgets.budget_name, acc_investment_budgets.category_budget, acc_investment_budgets.sisa as total_budget, acc_investment_budgets.total as total_pengeluaran FROM acc_investments join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number where acc_investments.id = ".$id;
            $tolak = db::select($isimail);

            //kirim email ke Buyer
            $mails = "select distinct email from users where users.username = '".$investment->applicant_id."'";
            $mailtoo = DB::select($mails);

            Mail::to($mailtoo)->send(new SendEmail($tolak, 'investment'));
            return redirect('/investment/check/'.$id)->with('error', 'Investment Not Approved')->with('page', 'Investment');

        } catch (Exception $e) {

            return redirect('/investment/check/'.$id)->with('error', 'Reject Error')->with('page', 'Investment');
        }
    }



    // Terima Barang

    public function wh_receive_equipment(){
        $title = 'Receive Barang';
        $title_jp = '備品受取';


        $po_detail = db::select("
            SELECT DISTINCT acc_purchase_order_details.no_po
            from acc_purchase_orders 
            join acc_purchase_order_details on
            acc_purchase_orders.no_po = acc_purchase_order_details.no_po
            WHERE
            posisi = 'pch' and acc_purchase_order_details.`status` is null order by acc_purchase_order_details.id
            ");

        return view('accounting_purchasing.master.receive_wh', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'po_detail' => $po_detail
        ))->with('page', 'Receive Warehouse')->with('head', 'Receive Equipment Warehouse');
    }

    public function wh_receive_ga(){
        $title = 'Receive Barang GA';
        $title_jp = '総務課備品受取';

        $po_detail = db::select("
            SELECT DISTINCT
            acc_purchase_order_details.no_po 
            FROM
            acc_purchase_orders
            JOIN acc_purchase_order_details ON acc_purchase_orders.no_po = acc_purchase_order_details.no_po
            WHERE
            acc_purchase_orders.posisi = 'pch' 
            AND acc_purchase_order_details.`status` IS NULL 
            AND acc_purchase_orders.no_po like '%GA%'
            ORDER BY
            acc_purchase_order_details.id 
            ");

        return view('accounting_purchasing.master.receive_ga', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'po_detail' => $po_detail
        ))->with('page', 'Receive GA')->with('head', 'Receive GA');
    }

    public function wh_receive_kantin(){
        $title = 'Receive Barang Kantin';
        $title_jp = '';

        $po_detail = db::select("
            SELECT DISTINCT
            canteen_purchase_order_details.no_po 
            FROM
            canteen_purchase_orders
            JOIN canteen_purchase_order_details ON canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po
            WHERE
            canteen_purchase_orders.posisi = 'pch' 
            AND canteen_purchase_order_details.`status` IS NULL 
            ORDER BY
            canteen_purchase_order_details.id 
            ");

        return view('accounting_purchasing.master.receive_ga_kantin', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'po_detail' => $po_detail
        ))->with('page', 'Receive GA Kantin')->with('head', 'Receive GA Kantin');
    }

    public function fetch_receive_equipment(Request $request){
        $data = db::select("
            SELECT
            acc_purchase_order_details.* 
            from acc_purchase_orders 
            join acc_purchase_order_details on
            acc_purchase_orders.no_po = acc_purchase_order_details.no_po
            WHERE
            acc_purchase_orders.deleted_at is null and
            acc_purchase_orders.no_po = '". $request->get('no_po')."'" );

        $response = array(
            'status' => true,
            'datas' => $data,
        );
        return Response::json($response);
    }

    public function fetch_receive_kantin(Request $request){
        $data = db::select("
            SELECT
            canteen_purchase_order_details.* 
            from canteen_purchase_orders 
            join canteen_purchase_order_details on
            canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po
            WHERE
            canteen_purchase_orders.deleted_at is null and
            canteen_purchase_orders.no_po = '". $request->get('no_po')."'" 
        );

        $response = array(
            'status' => true,
            'datas' => $data,
        );
        return Response::json($response);
    }

    public function update_receive(Request $request){
        try {
            $item = $request->get('item');

            foreach ($item as $itm) {

                if($itm['qty'] == "" || $itm['date'] == ""){
                    $response = array(
                       'status' => false,
                       'message' => 'Mohon Diisi Jumlah & Tanggal Kedatangan',
                   );
                    return Response::json($response);
                }

                else {
                    $po_detail = AccPurchaseOrderDetail::where('id', $itm['id'])
                    ->first();

                    if($po_detail->qty < ($po_detail->qty_receive + (int) $itm['qty']) ){
                        $response = array(
                           'status' => false,
                           'message' => 'Jumlah Yang Dimasukkan Melebihi Pembelian Barang',
                       );
                        return Response::json($response);
                    }

                    $inv = AccPurchaseOrderDetail::where('id', $itm['id'])
                    ->update([
                        'qty_receive' => $po_detail->qty_receive + (int) $itm['qty'],
                        'date_receive' => $itm['date'],
                        'surat_jalan' => $itm['surat_jalan']
                    ]);


                    $receive = AccReceive::create([
                        'no_po' => $itm['no_po'],
                        'no_item' => $itm['no_item'],
                        'nama_item' => $po_detail->nama_item,
                        'qty' => $po_detail->qty,
                        'qty_receive' => $itm['qty'],
                        'date_receive' => $itm['date'],
                        'surat_jalan' => $itm['surat_jalan'],
                        'created_by' => Auth::id()
                    ]);

                    $receive->save();

                    $like = $receive->no_po;

                    if (strpos($like, 'IT') !== false) {
                        $inventory = MisInventory::create([
                            'description' => $receive->nama_item,
                            'qty' => $receive->qty_receive,
                            'created_by' => $receive->created_by,
                            'condition' => 'OK',
                            'id_order' => $receive->id
                        ]);

                        $inventory->save();

                        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0103002' or employee_id = 'PI0906001'";
                        $mailtoo = DB::select($mails);

                        $mailscc = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where department = 'Management Information System Department'
                        and employee_id not in('PI0103002', 'PI0906001') and end_date is null";
                        $mailtoocc = DB::select($mailscc);

                        $isimail = "select mis_inventories.id, mis_inventories.description, mis_inventories.qty, mis_inventories.`condition`, users.`name` as nama, mis_inventories.created_at as tanggal, acc_receives.no_po as no_po, acc_receives.no_item as no_item, acc_receives.nama_item from mis_inventories join users on mis_inventories.created_by = users.id 
                        join acc_receives on mis_inventories.id_order = acc_receives.id where mis_inventories.id = ".$inventory->id;                

                        $inventory = db::select($isimail);
                        Mail::to($mailtoo)->cc($mailtoocc)->send(new SendEmail($inventory, 'barang_mis'));
                    }



                    

                    // $budget_log = AccBudgetHistory::where('po_number','=',$itm['no_po'])
                    // ->where(DB::raw('SUBSTRING(no_item, 1, 7)'),'=',$itm['no_item'])
                    // ->first();

                    // var_dump($itm['no_po']);
                    // var_dump($itm['no_item']);
                    // var_dump($budget_log);

                    // $update_budget_log = AccBudgetHistory::where('po_number','=',$itm['no_po'])
                    // ->where(DB::raw('SUBSTRING(no_item, 1, 7)'),'=',$itm['no_item'])
                    // ->update([
                    //     'budget_month_receive' => strtolower(date('M')),
                    //     'amount_receive' => $budget_log->amount_po,
                    //     'status' => 'Actual'
                    // ]);

                    //GET DATA
                    $datapo =  AccPurchaseOrderDetail::where('id', $itm['id'])
                    ->first();

                    //UPDATE STATUS
                    if ($datapo->qty_receive >= $datapo->qty) {
                        $update_qty_receive = AccPurchaseOrderDetail::where('id','=',$itm['id'])
                        ->update(['status' => 'close']);
                    }
                }

            }

                    // 
            
            // for ($i=0; $i < count($id); $i++) { 
            //     $po_id = AccPurchaseOrderDetail::where('id', $id)->first();

            //     $total_all = $po_id->qty_receive + $qty;

            // }

            $response = array(
                'status' => true,
                'message' => 'Update Receive Berhasil'
            );
            return Response::json($response);
        } 
        catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function update_receive_ga(Request $request){
        try {
            $item = $request->get('item');

            foreach ($item as $itm) {
                if ($itm['qty'] != null && $itm['date'] != null) {

                    //Get PO
                    $po_detail = AccPurchaseOrderDetail::where('id', $itm['id'])
                    ->first();

                    if($po_detail->qty < ($po_detail->qty_receive + (int) $itm['qty']) ){
                        $response = array(
                           'status' => false,
                           'message' => 'Jumlah Yang Dimasukkan Melebihi Pembelian Barang',
                       );
                        return Response::json($response);
                    }

                    $inv = AccPurchaseOrderDetail::where('id', $itm['id'])
                    ->update([
                        'qty_receive' => $po_detail->qty_receive + (int) $itm['qty'],
                        'date_receive' => $itm['date'],
                        'surat_jalan' => $itm['surat_jalan']
                    ]);


                    $receive = AccReceive::create([
                        'no_po' => $itm['no_po'],
                        'no_item' => $itm['no_item'],
                        'nama_item' => $po_detail->nama_item,
                        'qty' => $po_detail->qty,
                        'qty_receive' => $itm['qty'],
                        'date_receive' => $itm['date'],
                        'surat_jalan' => $itm['surat_jalan'],
                        'created_by' => Auth::id()
                    ]);

                    $receive->save();

                    $budget_log = AccBudgetHistory::where('po_number','=',$itm['no_po'])
                    ->where(DB::raw('SUBSTRING(no_item, 1, 7)'),'=',$itm['no_item'])
                    ->first();

                    $update_budget_log = AccBudgetHistory::where('po_number','=',$itm['no_po'])
                    ->where(DB::raw('SUBSTRING(no_item, 1, 7)'),'=',$itm['no_item'])
                    ->update([
                        'budget_month_receive' => strtolower(date('M')),
                        'amount_receive' => $budget_log->amount_po,
                        'status' => 'Actual'
                    ]);

                    $datapo =  AccPurchaseOrderDetail::where('id', $itm['id'])
                    ->first();

                    if ($datapo->qty_receive >= $datapo->qty) {
                        $update_qty_receive = AccPurchaseOrderDetail::where('id','=',$itm['id'])
                        ->update(['status' => 'close']);
                    }
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Update Receive Berhasil'
            );
            return Response::json($response);
        } 
        catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function update_receive_kantin(Request $request){
        try {
            $item = $request->get('item');

            foreach ($item as $itm) {

                if($itm['qty'] == "" || $itm['date'] == ""){
                    $response = array(
                       'status' => false,
                       'message' => 'Mohon Diisi Jumlah & Tanggal Kedatangan',
                   );
                    return Response::json($response);
                }

                else {
                    $po_detail = CanteenPurchaseOrderDetail::where('id', $itm['id'])
                    ->first();

                    if($po_detail->qty < ($po_detail->qty_receive + (int) $itm['qty']) ){
                        $response = array(
                           'status' => false,
                           'message' => 'Jumlah Yang Dimasukkan Melebihi Pembelian Barang',
                       );
                        return Response::json($response);
                    }

                    $inv = CanteenPurchaseOrderDetail::where('id', $itm['id'])
                    ->update([
                        'qty_receive' => $po_detail->qty_receive + (int) $itm['qty'],
                        'date_receive' => $itm['date'],
                        'surat_jalan' => $itm['surat_jalan']
                    ]);


                    $receive = CanteenReceive::create([
                        'no_po' => $itm['no_po'],
                        'no_item' => $itm['no_item'],
                        'nama_item' => $po_detail->nama_item,
                        'qty' => $po_detail->qty,
                        'qty_receive' => $itm['qty'],
                        'date_receive' => $itm['date'],
                        'surat_jalan' => $itm['surat_jalan'],
                        'created_by' => Auth::id()
                    ]);

                    $receive->save();

                    $budget_log = CanteenBudgetHistory::where('po_number','=',$itm['no_po'])
                    ->where(DB::raw('SUBSTRING(no_item, 1, 7)'),'=',$itm['no_item'])
                    ->first();

                    $update_budget_log = CanteenBudgetHistory::where('po_number','=',$itm['no_po'])
                    ->where(DB::raw('SUBSTRING(no_item, 1, 7)'),'=',$itm['no_item'])
                    ->update([
                        'budget_month_receive' => strtolower(date('M')),
                        'amount_receive' => $budget_log->amount_po,
                        'status' => 'Actual'
                    ]);

                    $datapo = CanteenPurchaseOrderDetail::where('id', $itm['id'])->first();

                    if ($datapo->qty_receive >= $datapo->qty) {
                        $update_qty_receive = CanteenPurchaseOrderDetail::where('id','=',$itm['id'])
                        ->update(['status' => 'close']);
                    }
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Update Receive Kantin Berhasil'
            );
            return Response::json($response);
        } 
        catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function wh_print_equipment(){
        $title = 'Print Barang';
        $title_jp = '備品ラベル印刷';

        $po_detail = db::select("
            SELECT DISTINCT acc_receives.no_po from acc_receives order by id desc");

        return view('accounting_purchasing.master.receive_print_wh', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'po_detail' => $po_detail
        ))->with('page', 'Print Warehouse')->with('head', 'Receive Equipment Warehouse');
    }

    public function fetch_print_equipment(Request $request){
        $data = db::select("
            SELECT
            acc_receives.* 
            from acc_receives 
            WHERE
            acc_receives.no_po = '". $request->get('no_po')."'" );

        $response = array(
            'status' => true,
            'datas' => $data,
        );
        return Response::json($response);
    }

    public function label_kedatangan($id){

        //Get PO
        $po_receive = AccReceive::where('id', $id)
        ->first();

        return view('accounting_purchasing.report.label_kedatangan', array(
            'kode_item' => $po_receive->no_item,
            'description' => $po_receive->nama_item,
            'po' => $po_receive->no_po,
            'date' => $po_receive->date_receive,
            'quantity' => $po_receive->qty_receive
        ));
    }

    // Terima Barang

    public function wh_cetak_bukti(){
        $title = 'Kirim Barang';
        $title_jp = '備品送付';

        return view('accounting_purchasing.master.receive_cetak_bukti', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'Receive Bukti')->with('head', 'Receive Cetak Bukti');
    }

    public function fetch_cetak_bukti(Request $request){

        //Get PO
        $receive = AccReceive::where('no_po', $request->get('no_po'))
        ->where('no_item', $request->get('no_item'))
        ->where('qty_receive',$request->get('qty'))
        ->first();

        $response = array(
            'status' => true,
            'datas' => $receive,
        );
        return Response::json($response);
    }

    public function create_cetak_bukti(Request $request){
        try {
            $receive = $request->get('receive');
            
            $id_print = $request->get('tanggal_terima').' '.$request->get('date_terima');

            foreach ($receive as $rcv) {

                $receive = AccReceiveReport::create([
                    'id_print' => 'Receive_'.$id_print,
                    'no_po' => $rcv['no_po'],
                    'no_item' => $rcv['no_item'],
                    'nama_item' => $rcv['nama_item'],
                    'qty_receive' => $rcv['qty_receive'],
                    'date_receive' => $rcv['date_receive'],
                    'created_by' => Auth::id()
                ]);

                $receive->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Cetak Bukti Penerimaan Berhasil'
            );
            return Response::json($response);
        } 
        catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function bukti_penerimaan()
    {
      try {
        $receive = DB::SELECT("SELECT DISTINCT id_print from acc_receive_reports order by id desc");

        $response = array(
          'status' => true,
          'receive' => $receive
      );

        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
          'status' => false,
          'message'=> $e->getMessage()
      );

        return Response::json($response); 
    }
}

public function cetak_bukti_penerimaan($id)
{

   $receives = db::select("
    SELECT 
    acc_receive_reports.no_po,
    acc_receive_reports.nama_item,
    acc_receive_reports.qty_receive,
    acc_receive_reports.date_receive,
    acc_purchase_orders.supplier_name
    FROM
    acc_receive_reports 
    left join acc_purchase_orders
    on acc_purchase_orders.no_po = acc_receive_reports.no_po
    WHERE
    id_print = '".$id."'
    AND acc_receive_reports.deleted_at IS NULL
    ");


   $pdf = \App::make('dompdf.wrapper');
   $pdf->getDomPDF()->set_option("enable_php", true);
   $pdf->setPaper('A4', 'potrait');

   $pdf->loadView('accounting_purchasing.master.receive_print_report', array(
      'receives' => $receives,
  ));

   return $pdf->stream($id.".pdf");
}

public function cek_kedatangan(){
    $title = 'Cek Kedatangan';
    $title_jp = '備品着荷確認';

    $po_detail = db::select("SELECT DISTINCT acc_receives.no_po from acc_receives order by id desc");

    return view('accounting_purchasing.master.receive_cek_kedatangan', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'po_detail' => $po_detail
    ))->with('page', 'Cek Kedatangan')->with('head', 'Receive Equipment Warehouse');
}

public function fetch_kedatangan(Request $request)
{

    $tanggal = "";
    $keyword = "";
    $po = "";

    if (strlen($request->get('tanggal')) > 0)
    {
        $tanggal = "and acc_receives.date_receive = '".$request->get('tanggal')."'";
    }
    if (strlen($request->get('keyword')) > 0)
    {
        $keyword = "and acc_receives.nama_item LIKE '%".$request->get('keyword')."%'";
    }

    if (strlen($request->get('no_po')) > 0)
    {
        $po = "and acc_receives.no_po = '".$request->get('no_po')."'";
    }

    $kedatangan = DB::select("
     SELECT DISTINCT
     acc_receives.*,
     acc_purchase_order_details.no_pr,
     IF
     ( goods_price != 0, goods_price, service_price ) AS price,
     acc_purchase_orders.no_po_sap,
     acc_purchase_orders.supplier_code,
     acc_purchase_orders.supplier_name 
     FROM
     `acc_receives`
     LEFT JOIN acc_purchase_order_details ON acc_receives.no_po = acc_purchase_order_details.no_po
     JOIN acc_purchase_orders ON acc_purchase_orders.no_po = acc_receives.no_po 
     AND acc_receives.no_item = acc_purchase_order_details.no_item 
     WHERE
     acc_receives.deleted_at IS NULL 
     and acc_purchase_orders.deleted_at IS NULL 
     ".$tanggal."
     ".$keyword."
     ".$po."
     ORDER BY
     acc_receives.id desc");

    return DataTables::of($kedatangan)
    ->addColumn('amount_po', function ($kedatangan)
    {
        $price = $kedatangan->price;
        $qty_receive = $kedatangan->qty_receive;

        return number_format($price * $qty_receive,2,".",",");  
    })

    ->editColumn('supplier_code', function ($kedatangan)
    {
        return $kedatangan->supplier_code.' - '.$kedatangan->supplier_name;  
    })

    ->editColumn('price', function ($kedatangan)
    {
        return number_format($kedatangan->price,2,".",",");  
    })

    ->rawColumns(['price' => 'price','amount_po' => 'amount_po'])
    ->make(true);
}

public function cek_kedatangan_ga(){
    $title = 'Cek Kedatangan GA';
    $title_jp = '総務課備品着艦確認';

    $po_detail = db::select("SELECT DISTINCT acc_receives.no_po from acc_receives order by id desc");

    return view('accounting_purchasing.master.receive_cek_kedatangan_ga', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'po_detail' => $po_detail
    ))->with('page', 'Cek Kedatangan GA')->with('head', 'Receive Equipment Warehouse');
}

public function fetch_kedatangan_ga(Request $request)
{

    $tanggal = "";
    $keyword = "";
    $po = "";

    if (strlen($request->get('tanggal')) > 0)
    {
        $tanggal = "and acc_receives.date_receive = '".$request->get('tanggal')."'";
    }
    if (strlen($request->get('keyword')) > 0)
    {
        $keyword = "and acc_receives.nama_item LIKE '%".$request->get('keyword')."%'";
    }

    if (strlen($request->get('no_po')) > 0)
    {
        $po = "and acc_receives.no_po = '".$request->get('no_po')."'";
    }

    $kedatangan = DB::select("
        SELECT DISTINCT
        acc_receives.*, acc_purchase_order_details.no_pr, IF(goods_price != 0,goods_price,service_price) as price, acc_purchase_orders.no_po_sap, acc_purchase_orders.supplier_code, acc_purchase_orders.supplier_name
        FROM
        `acc_receives`
        LEFT JOIN acc_purchase_order_details ON acc_receives.no_po = acc_purchase_order_details.no_po 
        JOIN acc_purchase_orders on acc_purchase_orders.no_po = acc_receives.no_po
        AND acc_receives.no_item = acc_purchase_order_details.no_item
        WHERE acc_receives.deleted_at IS NULL and acc_receives.no_po LIKE '%GA%' " . $tanggal . " " . $keyword . " " . $po . " order by acc_receives.id DESC");

    return DataTables::of($kedatangan)
    ->addColumn('amount_po', function ($kedatangan)
    {
        $price = $kedatangan->price;
        $qty_receive = $kedatangan->qty_receive;

        return number_format($price * $qty_receive,2,".",",");  
    })

    ->editColumn('supplier_code', function ($kedatangan)
    {
        return $kedatangan->supplier_code.' - '.$kedatangan->supplier_name;  
    })

    ->editColumn('price', function ($kedatangan)
    {
        return number_format($kedatangan->price,2,".",",");  
    })

    ->rawColumns(['price' => 'price','amount_po' => 'amount_po'])
    ->make(true);
}

public function cek_kedatangan_kantin(){
    $title = 'Cek Kedatangan Kantin';
    $title_jp = '';

    $po_detail = db::select("SELECT DISTINCT canteen_receives.no_po from canteen_receives order by id desc");

    return view('accounting_purchasing.master.receive_cek_kedatangan_kantin', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'po_detail' => $po_detail
    ))->with('page', 'Cek Kedatangan GA')->with('head', 'Receive Equipment Warehouse');
}

public function fetch_kedatangan_kantin(Request $request)
{

    $tanggal = "";
    $keyword = "";
    $po = "";

    if (strlen($request->get('tanggal')) > 0)
    {
        $tanggal = "and canteen_receives.date_receive = '".$request->get('tanggal')."'";
    }
    if (strlen($request->get('keyword')) > 0)
    {
        $keyword = "and canteen_receives.nama_item LIKE '%".$request->get('keyword')."%'";
    }

    if (strlen($request->get('no_po')) > 0)
    {
        $po = "and canteen_receives.no_po = '".$request->get('no_po')."'";
    }

    $kedatangan = DB::select("
        SELECT DISTINCT
        canteen_receives.*, canteen_purchase_order_details.no_pr, IF(goods_price != 0,goods_price,service_price) as price, canteen_purchase_orders.supplier_code, canteen_purchase_orders.supplier_name
        FROM
        `canteen_receives`
        LEFT JOIN canteen_purchase_order_details ON canteen_receives.no_po = canteen_purchase_order_details.no_po 
        JOIN canteen_purchase_orders on canteen_purchase_orders.no_po = canteen_receives.no_po
        AND canteen_receives.no_item = canteen_purchase_order_details.no_item
        WHERE canteen_receives.deleted_at IS NULL and canteen_receives.no_po LIKE '%GA%' " . $tanggal . " " . $keyword . " " . $po . " order by canteen_receives.id DESC");

    return DataTables::of($kedatangan)
    ->addColumn('amount_po', function ($kedatangan)
    {
        $price = $kedatangan->price;
        $qty_receive = $kedatangan->qty_receive;

        return number_format($price * $qty_receive,2,".",",");  
    })

    ->editColumn('supplier_code', function ($kedatangan)
    {
        return $kedatangan->supplier_code.' - '.$kedatangan->supplier_name;  
    })

    ->editColumn('price', function ($kedatangan)
    {
        return number_format($kedatangan->price,2,".",",");  
    })

    ->rawColumns(['price' => 'price','amount_po' => 'amount_po'])
    ->make(true);
}

public function fetch_outstanding_kantin(Request $request)
{
    $tanggal = "";

    if (strlen($request->get('tanggal')) > 0)
    {
        $tanggal = "and delivery_date = '".$request->get('tanggal')."'";

        if (strlen($request->get('tanggal_ke')) > 0) {
            $tanggal = "and delivery_date >= '".$request->get('tanggal')."'";
            $tanggal = $tanggal . "and delivery_date  <= '" .$request->get('tanggal_ke')."'";
        }
    }
    else{
        $tanggal = "and delivery_date = '".date('Y-m-d')."'";
    }

    $kedatangan = DB::select("
        SELECT
        canteen_purchase_order_details.*
        FROM
        canteen_purchase_orders
        JOIN canteen_purchase_order_details ON canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po
        WHERE
        canteen_purchase_orders.deleted_at IS NULL ". $tanggal . "");

    return DataTables::of($kedatangan)
    ->editColumn('goods_price', function ($kedatangan)
    {
        if ($kedatangan->goods_price == 0) {
            $price = $kedatangan->service_price;
        }else{
            $price = $kedatangan->goods_price;
        }

        return number_format($price,2,".",",");  
    })

    ->editColumn('qty_receive', function ($kedatangan)
    {
        if ($kedatangan->qty_receive != 0) {
            $qty_receive = $kedatangan->qty_receive;
        }else{
            $qty_receive = '-';
        }

        return $qty_receive;  
    })

    ->editColumn('date_receive', function ($kedatangan)
    {
        if ($kedatangan->date_receive != null) {
            $date_receive = $kedatangan->date_receive;
        }else{
            $date_receive = '-';
        }

        return $date_receive;  
    })

    ->rawColumns(['price' => 'price', 'qty_receive' => 'qty_receive', 'date_receive' => 'date_receive'])
    ->make(true);
}

public function exportInvestment(Request $request){

    $time = date('d-m-Y H;i;s');

    $tanggal = "";

    if (strlen($request->get('bulan')) > 0)
    {
        $datefrom = date('Y-m-d', strtotime($request->get('bulan')));
        $tanggal = "and submission_date = '" . $datefrom . "'";
    }

    $investment_detail = db::select(
        "SELECT DISTINCT acc_investments.reff_number, acc_investments.category, acc_investments.type, acc_investments.objective, ycj_approval, submission_date, acc_investments.note, acc_investment_budgets.budget_no, acc_investments.applicant_department, acc_investment_details.detail, acc_investments.currency, acc_investment_details.amount, acc_investments.supplier_code, acc_investments.supplier_name, acc_investment_budgets.category_budget, acc_investments.delivery_order, acc_purchase_order_details.no_po, acc_investment_details.dollar, acc_investments.posisi from acc_investments left join acc_investment_details on acc_investments.reff_number = acc_investment_details.reff_number left join acc_investment_budgets on acc_investments.reff_number = acc_investment_budgets.reff_number left join acc_purchase_order_details on acc_investments.reff_number = acc_purchase_order_details.no_pr and acc_investment_details.detail = acc_purchase_order_details.nama_item WHERE acc_investments.deleted_at IS NULL " . $tanggal . " order by acc_investments.reff_number ASC");

    $data = array(
        'investment_detail' => $investment_detail
    );

    ob_clean();

    Excel::create('Investment List '.$time, function($excel) use ($data){
        $excel->sheet('Data', function($sheet) use ($data) {
          return $sheet->loadView('accounting_purchasing.investment_excel', $data);
      });
    })->export('xlsx');
}



    // Invoice Check

public function invoice_receive_report()
{
    $title = 'Receive Report Master';
    $title_jp = '受取報告マスター';

    $status = AccInvoiceReceiveReport::select('*')->whereNull('acc_invoice_receive_reports.deleted_at')
    ->distinct()
    ->get();

    return view('accounting_purchasing.invoice.receive_report', array(
        'title' => $title,
        'title_jp' => $title_jp,
    ))->with('page', 'Receive Report')
    ->with('head', 'Receive Report');
}

public function invoice_fetch_receive()
{
  try {
    $invoice = DB::SELECT("SELECT DISTINCT tanggal_upload from acc_invoice_receive_reports order by id desc");

    $jumlah = DB::SELECT("SELECT count(id) as jumlah from acc_invoice_receive_reports ");

    $response = array(
      'status' => true,
      'invoice' => $invoice,
      'jumlah' => $jumlah
  );

    return Response::json($response); 
} catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message'=> $e->getMessage()
  );

    return Response::json($response); 
}
}

    // public function invoice_fetch_receive(Request $request)
    // {
    //     $actual = AccInvoiceReceiveReport::orderBy('acc_invoice_receive_reports.id', 'desc');

    //     if ($request->get('category') != null)
    //     {
    //         $actual = $actual->whereIn('acc_invoice_receive_reports.category', $request->get('category'));
    //     }

    //     $actual = $actual->select('*')->get();

    //     return DataTables::of($actual)

    //     ->editColumn('vendor_code', function ($actual)
    //     {
    //         return $actual->vendor_code. ' - ' .$actual->vendor_name;
    //     })

    //     ->editColumn('amount', function ($actual)
    //     {
    //         return '$'.$actual->amount;
    //     })

    //     ->addColumn('action', function ($actual)
    //     {
    //         $id = $actual->id;

    //         return ' 
    //         <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$id.')"><i class="fa fa-eye"></i> Detail</button>
    //         ';
    //     })

    //     ->rawColumns(['action' => 'action'])
    //     ->make(true);
    // }

public function invoice_import_receive(Request $request){
    if($request->hasFile('upload_file')) {
        try{                
            $file = $request->file('upload_file');
            $file_name = 'receive_'. date("ymd_h.i") .'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/invoice/'), $file_name);
            $excel = public_path('uploads/invoice/') . $file_name;

            $rows = Excel::load($excel, function($reader) {
                $reader->noHeading();
                $reader->skipRows(1);
            })->get();

            $rows = $rows->toArray();

            for ($i=0; $i < count($rows); $i++) {
                $currency  = $rows[$i][1];
                $vendor_code = $rows[$i][4];
                $vendor_name = $rows[$i][5];                    
                $receive_date = $rows[$i][14];
                $document_no = $rows[$i][15];
                $invoice_no = $rows[$i][17];    
                $no_po_sap_urut = $rows[$i][18];
                $no_po = $rows[$i][19];
                $category = $rows[$i][20];
                $material = $rows[$i][21];
                $item_description = $rows[$i][22];
                $qty = $rows[$i][23];
                $uom = $rows[$i][24];
                $price = $rows[$i][27];
                $amount = $rows[$i][28];
                $amount_dollar = $rows[$i][31];
                $gl_number = $rows[$i][40];
                $gl_description = $rows[$i][41];
                $cost_center = $rows[$i][42];
                $cost_description = $rows[$i][43];
                $pch_code = $rows[$i][44];

                $no_po_sap = explode("-", trim($no_po_sap_urut));
                    $item_no = substr($item_description,0,7); //get 7 char kode item


                    $actual = AccInvoiceReceiveReport::where('document_no', $document_no)
                    ->select('document_no')
                    ->first();

                    if (count($actual) == 0) { //kalo insert

                       $data2 = AccInvoiceReceiveReport::create([
                        'tanggal_upload' => date('Y-m-d'),
                        'currency' => $currency,
                        'vendor_code' => $vendor_code,
                        'vendor_name' => $vendor_name,
                        'receive_date' => $receive_date,
                        'document_no' => $document_no,
                        'invoice_no' => $invoice_no,
                        'no_po_sap' => (int)$no_po_sap[0],
                        'no_urut' => $no_po_sap[1],
                        'no_po' => $no_po,
                        'category' => $category,
                        'material' => $material,
                        'item_no' => $item_no,
                        'item_description' => $item_description,
                        'qty' => $qty,
                        'uom' => $uom,
                        'price' => $price,
                        'amount' => $amount,
                        'amount_dollar' => $amount_dollar,
                        'gl_number' => $gl_number,
                        'gl_description' => $gl_description,
                        'cost_center' => $cost_center,
                        'cost_description' => $cost_description,
                        'pch_code' => $pch_code,
                        'created_by' => Auth::id()
                    ]);

                       $data2->save();

                   } else if (count($actual) > 0){ 

                    $data2 = AccInvoiceReceiveReport::where('document_no','=',$document_no)
                    ->update([
                        'tanggal_upload' => date('Y-m-d'),
                        'currency' => $currency,
                        'vendor_code' => $vendor_code,
                        'vendor_name' => $vendor_name,
                        'receive_date' => $receive_date,
                        'document_no' => $document_no,
                        'invoice_no' => $invoice_no,
                        'no_po_sap' => (int)$no_po_sap[0],
                        'no_urut' => $no_po_sap[1],
                        'no_po' => $no_po,
                        'category' => $category,
                        'material' => $material,
                        'item_no' => $item_no,
                        'item_description' => $item_description,
                        'qty' => $qty,
                        'uom' => $uom,
                        'price' => $price,
                        'amount' => $amount,
                        'amount_dollar' => $amount_dollar,
                        'gl_number' => $gl_number,
                        'gl_description' => $gl_description,
                        'cost_center' => $cost_center,
                        'cost_description' => $cost_description,
                        'pch_code' => $pch_code,
                        'created_by' => Auth::id()
                    ]);
                }
            }       

            $response = array(
                'status' => true,
                'message' => 'Upload Berhasil',
            );
            return Response::json($response);

        }catch(\Exception $e){
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }else{
        $response = array(
            'status' => false,
            'message' => 'Upload failed, File not found',
        );
        return Response::json($response);
    }
}

public function invoice_fetch_receive_data(Request $request){
    $tanggal = $request->get("tanggal");

    $qry = "select * from acc_invoice_receive_reports where tanggal_upload = '".$tanggal."' ";

    $bud = DB::select($qry);

    $response = array(
        'status' => true,
        'datas' => $bud
    );

    return Response::json($response); 
}

public function index_invoice(){

    $title = "Tanda Terima";
    $title_jp = "領収書";

    $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
    ->distinct()
    ->get();

    $payment_term = AccInvoicePaymentTerm::select('*')->whereNull('deleted_at')
    ->distinct()
    ->get();

    $no_po = AccInvoiceReceiveReport::select('no_po_sap')->whereNull('deleted_at')
    ->distinct()
    ->get();

    $surat_jalan = AccInvoiceReceiveReport::select('invoice_no')->whereNull('deleted_at')
    ->distinct()
    ->get();

    return view('accounting_purchasing.invoice.tanda_terima', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'vendor' => $vendor,
        'payment_term' => $payment_term,
        'no_po' => $no_po,
        'surat_jalan' => $surat_jalan
    ));

}

public function fetch_invoice(){
    $invoice = db::select("SELECT
            *
        FROM
        acc_invoices
        order by id desc
        ");

    $response = array(
        'status' => true,
        'invoice' => $invoice
    );
    return Response::json($response);
}

public function create_invoice(Request $request){
    try{
        $invoice = new AccInvoice([
            'invoice_date' => $request->input('invoice_date'),
            'supplier_code' => $request->input('supplier_code'),
            'supplier_name' => $request->input('supplier_name'),
            'kwitansi' => $request->input('kwitansi'),
            'invoice_no' => $request->input('invoice_no'),
            'surat_jalan' => $request->input('surat_jalan'),
            'bap' => $request->input('bap'),
            'npwp' => $request->input('npwp'),
            'faktur_pajak' => $request->input('faktur_pajak'),
            'po_number' => $request->input('po_number'),
            'payment_term' => $request->input('payment_term'),
            'surat_jalan' => $request->input('surat_jalan'),
            'currency' => $request->input('currency'),
            'amount' => $request->input('amount'),
            'do_date' => $request->input('do_date'),
            'due_date' => $request->input('due_date'),
            'distribution_date' => $request->input('distribution_date'),
            'created_by' => Auth::user()->username,
            'created_name' => Auth::user()->name
        ]);

        $invoice->save();

        $response = array(
            'status' => true,
            'message' => 'New Invoice Successfully Added'
        );
        return Response::json($response);
    }
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function fetch_invoice_detail(Request $request){
    $invoice = AccInvoice::find($request->get('id'));

    $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
    ->distinct()
    ->get();

    $payment_term = AccInvoicePaymentTerm::select('*')->whereNull('deleted_at')
    ->distinct()
    ->get();

    $no_po = AccInvoiceReceiveReport::select('no_po_sap')->whereNull('deleted_at')
    ->distinct()
    ->get();

    $surat_jalan = AccInvoiceReceiveReport::select('invoice_no')->whereNull('deleted_at')
    ->distinct()
    ->get();

    $response = array(
        'status' => true,
        'invoice' => $invoice,
        'vendor' => $vendor,
        'payment_term' => $payment_term,
        'no_po' => $no_po,
        'surat_jalan' => $surat_jalan
    );
    return Response::json($response);
}

public function edit_invoice(Request $request){
    try{
        $invoice = AccInvoice::where('id', '=', $request->get('id_edit'))->first();

        $invoice->invoice_date = $request->input('invoice_date');
        $invoice->supplier_code = $request->input('supplier_code');
        $invoice->supplier_name = $request->input('supplier_name');
        $invoice->kwitansi = $request->input('kwitansi');
        $invoice->invoice_no = $request->input('invoice_no');
        $invoice->surat_jalan = $request->input('surat_jalan');
        $invoice->bap = $request->input('bap');
        $invoice->npwp = $request->input('npwp');
        $invoice->faktur_pajak = $request->input('faktur_pajak');
        $invoice->po_number = $request->input('po_number');
        $invoice->payment_term = $request->input('payment_term');
        $invoice->surat_jalan = $request->input('surat_jalan');
        $invoice->currency = $request->input('currency');
        $invoice->amount = $request->input('amount');
        $invoice->do_date = $request->input('do_date');
        $invoice->due_date = $request->input('due_date');
        $invoice->distribution_date = $request->input('distribution_date');
        $invoice->created_by = Auth::user()->username;
        $invoice->save();

        $response = array(
            'status' => true,
            'message' => 'Invoice Updated'
        );
        return Response::json($response);
    }
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function report_invoice($id){
    $invoice = AccInvoice::join('acc_suppliers','acc_invoices.supplier_code','=','acc_suppliers.vendor_code')
    ->where('acc_invoices.id','=',$id)->first();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('accounting_purchasing.invoice.report_invoice', array(
        'invoice' => $invoice,
        'id' => $id
    ));
    return $pdf->stream("Tanda Terima ".$invoice->invoice_no. ".pdf");
}

public function export_tanda_terima(Request $request){

    $time = date('d-m-Y H;i;s');
    $tanggal = "";

    if (strlen($request->get('datefrom')) > 0)
    {
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        $tanggal = "and invoice_date >= '" . $datefrom . " 00:00:00' ";
        if (strlen($request->get('dateto')) > 0)
        {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
            $tanggal = $tanggal . "and invoice_date  <= '" . $dateto . " 23:59:59' ";
        }
    }

    $tanda_terima = db::select(
        "Select acc_invoices.* from acc_invoices join acc_suppliers on acc_invoices.supplier_code = acc_suppliers.vendor_code WHERE acc_invoices.deleted_at IS NULL " . $tanggal . " order by acc_invoices.id ASC");
        // and acc_purchase_orders.posisi = 'pch' and acc_purchase_orders.`status` = 'not_sap' and no_po_sap is null 

    $data = array(
        'tanda_terima' => $tanda_terima
    );

    ob_clean();

    Excel::create('Tanda Terima List '.$time, function($excel) use ($data){
        $excel->sheet('Location', function($sheet) use ($data) {
          return $sheet->loadView('accounting_purchasing.invoice.invoice_excel', $data);
      });
    })->export('xlsx');
}

    //=============================================================//
    //                     FIXED ASSET                             //
    //=============================================================//


public function indexFixedAsset()
{
    $title = 'Index Fixed Asset';
    $title_jp = '??';

    return view('fixed_asset.index', array(
        'title' => $title,
        'title_jp' => $title_jp
    ))->with('page', 'Fixed Asset Index');  
}

public function indexAssetRegistration()
{
    $title = 'Asset Registration Form';
    $title_jp = '??';

    $clasification = FixedAssetClasification::select('id', 'category', 'category_code', 'clasification_name', 'life_time')->get();
    $exchange_rate = AccExchangeRate::select('currency', 'rate', 'periode')->orderBy('id', 'desc')->limit(3)->get();

    return view('fixed_asset.form.registration_form', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'clasification' => $clasification,
        'exchange_rate' => $exchange_rate
    ))->with('page', 'Fixed Asset Registration Form');  
}

public function indexAssetInvoice()
{
    $title = 'Asset Invoice Upload Form';
    $title_jp = '??';

    $inv = AccInvestment::where('category', '=', 'Investment')->select('reff_number', 'applicant_name')->get();

    return view('fixed_asset.form.invoice_form', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'investment' => $inv
    ))->with('page', 'Fixed Asset Invoice Upload Form');  
}

public function fetchAssetInvoice(Request $request)
{
    $data_invoice = FixedAssetInvoice::leftJoin('acc_investments', 'acc_investments.reff_number', '=', 'fixed_asset_invoices.investment_number')
    // ->where('acc_investments.applicant_id', '=', Auth::user()->username)
    ->select('fixed_asset_invoices.form_id', 'fixed_asset_invoices.investment_number', 'fixed_asset_invoices.invoice_number', 'fixed_asset_invoices.invoice_name', 'fixed_asset_invoices.fixed_asset_name', 'acc_investments.type', 'acc_investments.applicant_name', 'fixed_asset_invoices.created_at', 'fixed_asset_invoices.status')
    ->get();

    $response = array(
        'status' => true,
        'invoices' => $data_invoice
    );
    return Response::json($response);
}

public function assetSendInvoice(Request $request)
{
    $mail_to = [];
    $investment_number = $request->get('investment_number');
    $len = json_decode($request->get('len'));
    
    // $invoice_num = json_decode($request->get('invoice_num'));
    // $invoice_name = json_decode($request->get('invoice_name'));
    // $invoice_file = json_decode($request->get('invoice_file'));

    // $vendor = json_decode($request->get('vendor'));
    // $currency = json_decode($request->get('currency'));
    // $amount = json_decode($request->get('amount'));
    // $amount_usd = json_decode($request->get('amount_usd'));


    $date = date('Y-m-d');
    $prefix_now = 'FAR'.date("y").date("m");
    $code_generator = CodeGenerator::where('note','=','fixed_asset_register')->first();

    if ($prefix_now != $code_generator->prefix){
        $code_generator->prefix = $prefix_now;
        $code_generator->index = '0';
        $code_generator->save();
    }

    $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
    $form_id = $code_generator->prefix . $number;
    $code_generator->index = $code_generator->index+1;
    $code_generator->save();

    
    $inv_mail = AccInvestment::where('reff_number', '=', $investment_number)
    ->leftJoin('users', 'users.username', '=', 'acc_investments.applicant_id')
    ->first();

    array_push($mail_to, $inv_mail->email);

    try{
        for ($i=0; $i < $len; $i++) { 

            //FILE HANDLER
            if($request->hasFile('invoice_file_'.$i)) {
                $file = $request->file('invoice_file_'.$i);
                $file_name = $form_id.date('His').$i.'.'.$file->getClientOriginalExtension();
                $file->move(public_path('files/fixed_asset/'), $file_name);
            }

            $invoice_reg = new FixedAssetInvoice([
                'form_id' => $form_id,
                'investment_number' => $investment_number,
                'att' => $file_name,
                'status' => 'created',
                'created_for' => $inv_mail->applicant_id,
                'created_by' => Auth::user()->username
            ]);
            $invoice_reg->save();

            //DETAIL ITEM
            // for ($z=0; $z < count($vendor); $z++) { 
            //     if ($vendor[$z]->invoice == $invoice_num[$i]) {
            //         $invoice_detail = new FixedAssetInvoiceDetail([
            //             'invoice_id' => $invoice_reg->id,
            //             'vendor' => $vendor[$z]->vendor,
            //             'currency' => $currency[$z]->currency,
            //             'amount' => $amount[$z]->amount,
            //             'amount_usd' => $amount_usd[$z]->amount_usd,
            //             'remark' => null,
            //             'created_by' => Auth::user()->username
            //         ]);
            //         $invoice_detail->save();
            //     }
            // }

        }

        //SEND EMAIL

        $data_mail = FixedAssetInvoice::leftJoin('acc_investments', 'acc_investments.reff_number', '=', 'fixed_asset_invoices.investment_number')
        ->where('fixed_asset_invoices.form_id', '=', $form_id)
        ->select('fixed_asset_invoices.form_id', 'fixed_asset_invoices.investment_number', 'acc_investments.type', 'fixed_asset_invoices.att')
        ->get();

        Mail::to($mail_to)
        ->bcc(['nasiqul.ibat@music.yamaha.com'])
        ->send(new SendEmail($data_mail, 'fixed_asset_invoice'));

        $response = array(
            'status' => true,
        );
        return Response::json($response);

    } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function fetchAssetRegistration(Request $request)
{
    $asset = FixedAssetRegistration::leftJoin('fixed_asset_clasifications', 'fixed_asset_clasifications.category_code', '=', 'fixed_asset_registrations.clasification_id')
    ->select('fixed_asset_registrations.id','fixed_asset_registrations.asset_name', 'fixed_asset_registrations.invoice_number', 'fixed_asset_registrations.investment_number', 'fixed_asset_registrations.vendor', 'fixed_asset_registrations.pic', 'fixed_asset_registrations.location', 'fixed_asset_registrations.amount', 'fixed_asset_registrations.amount_usd', 'fixed_asset_registrations.request_date', 'fixed_asset_registrations.currency', 'fixed_asset_registrations.budget_number', 'fixed_asset_registrations.usage_estimation', 'fixed_asset_registrations.status', 'fixed_asset_clasifications.clasification_name', 'fixed_asset_clasifications.life_time')
    ->where('fixed_asset_registrations.created_by', '=', Auth::user()->username)
    ->get();

    $req_asset = FixedAssetInvoice::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'fixed_asset_invoices.created_for')
    ->leftJoin('acc_investments', 'acc_investments.reff_number', '=','fixed_asset_invoices.investment_number')
    ->leftJoin('acc_investment_budgets', 'acc_investment_budgets.reff_number', '=','fixed_asset_invoices.investment_number')
    ->where('created_for', '=', Auth::user()->username)
    ->select('form_id', 'fixed_asset_invoices.investment_number', 'created_for', 'name', db::raw('GROUP_CONCAT(att) as atts'), 'acc_investments.supplier_name', 'acc_investments.currency', 'acc_investment_budgets.total_ori', 'acc_investment_budgets.total', 'acc_investment_budgets.budget_no', 'employee_syncs.section')
    ->groupBy('form_id', 'fixed_asset_invoices.investment_number', 'created_for', 'name', 'acc_investments.supplier_name', 'acc_investments.currency', 'acc_investment_budgets.total_ori', 'acc_investment_budgets.total', 'acc_investment_budgets.budget_no', 'employee_syncs.section');

    if(strlen($request->get('form_id')) > 0 ){
        $req_asset = $req_asset->where('form_id', '=', $request->get('form_id'));
    }

    $req_asset = $req_asset->get();

    $response = array(
        'status' => true,
        'assets' => $asset,
        'asset_requests' => $req_asset
    );
    return Response::json($response);
}

public function fetchAssetRegistrationById(Request $request)
{
    $asset = FixedAssetRegistration::leftJoin('fixed_asset_clasifications', 'fixed_asset_clasifications.category_code', '=', 'fixed_asset_registrations.clasification_id')
    ->select('fixed_asset_registrations.id','fixed_asset_registrations.asset_name', 'fixed_asset_registrations.invoice_number', 'fixed_asset_registrations.invoice_name', 'fixed_asset_registrations.investment_number', 'fixed_asset_registrations.vendor', 'fixed_asset_registrations.pic', 'fixed_asset_registrations.location', 'fixed_asset_registrations.amount', 'fixed_asset_registrations.amount_usd', 'fixed_asset_registrations.request_date', 'fixed_asset_registrations.currency', 'fixed_asset_registrations.budget_number', 'fixed_asset_registrations.usage_estimation', 'fixed_asset_registrations.status', 'fixed_asset_clasifications.clasification_name', 'fixed_asset_clasifications.life_time', 'fixed_asset_clasifications.category', 'fixed_asset_registrations.pic', 'fixed_asset_registrations.location', 'fixed_asset_registrations.usage_term', 'fixed_asset_registrations.usage_estimation')
    ->where('fixed_asset_registrations.id', '=', $request->get('id'))
    ->first();

    $response = array(
        'status' => true,
        'asset' => $asset
    );
    return Response::json($response);
}

public function assetRegistration(Request $request)
{
    $asset_reg = new FixedAssetRegistration([
        'form_number' => $request->get('form_number'),
        'asset_name' => $request->get('item_name'),
        'invoice_number' => $request->get('invoice_number'),
        'invoice_name' => $request->get('invoice_name'),
        'clasification_id' => $request->get('clasification'),
        'vendor' => $request->get('vendor'),
        'currency' => $request->get('currency'),
        'amount' => $request->get('amount'),
        'amount_usd' => $request->get('amount_usd'),
        'pic' => $request->get('pic'),
        'location' => $request->get('location'),
        'investment_number' => $request->get('investment_number'),
        'budget_number' => $request->get('budget'),
        'usage_term' => $request->get('usage_term'),
        'usage_estimation' => $request->get('usage_est'),
        'status' => 'created',
        'created_by' => Auth::user()->username
    ]);
    $asset_reg->save();


    $new_asset = FixedAssetRegistration::where('id', '=', $asset_reg->id)
    ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'fixed_asset_registrations.created_by')
    ->select('fixed_asset_registrations.id', 'fixed_asset_registrations.form_number', 'fixed_asset_registrations.asset_name', 'fixed_asset_registrations.invoice_number', 'fixed_asset_registrations.vendor', 'fixed_asset_registrations.currency', 'fixed_asset_registrations.amount', 'fixed_asset_registrations.amount_usd', 'fixed_asset_registrations.pic', 'fixed_asset_registrations.investment_number', 'fixed_asset_registrations.sap_file', 'fixed_asset_registrations.created_at', 'employee_syncs.name','fixed_asset_registrations.budget_number', 'fixed_asset_registrations.usage_term', 'fixed_asset_registrations.usage_estimation')
    ->first();

    $datas = array(
        'status' => 'FA CONTROL',
        'assets' => $new_asset
    );

    Mail::to(['ismail.husen@music.yamaha.com'])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($datas, 'fixed_asset_registrations'));

    $response = array(
        'status' => true,
        'asset' => $asset_reg
    );
    return Response::json($response);
}

public function updateAssetRegistration(Request $request)
{
    if (count($request->file('sap_file')) > 0) {
        $file = $request->file('sap_file');

        $nama = $file->getClientOriginalName();

        $filename = pathinfo($nama, PATHINFO_FILENAME);
        $extension = pathinfo($nama, PATHINFO_EXTENSION);

        $att = 'SAP_FILE_'.date('YmdHis').'.'.$extension;

        $file->move('files/fixed_asset/sap_file/', $att);

    } else {
        $att = null;
    }


    $asset_upd = FixedAssetRegistration::where('id', '=', $request->get("id"))
    ->update([
        'invoice_name' => $request->get('invoice_name'),
        'asset_name' => $request->get('item_name'),
        'invoice_number' => $request->get('invoice_number'),
        'investment_number' => $request->get('investment_number'),
        'budget_number' => $request->get('budget'),
        'vendor' => $request->get('vendor'),
        'currency' => $request->get('currency'),
        'amount' => $request->get('amount'),
        'amount_usd' => $request->get('amount_usd'),
        'pic' => $request->get('pic'),
        'location' => $request->get('location'),
        'category_code' => $request->get('category_code'),
        'category' => $request->get('category'),
        'sap_id' => $request->get('sap_id'),
        'request_date' => $request->get('reg_date'),
        'depreciation_key' => $request->get('depreciation'),
        'sap_file' => $att,
        'status' => 'checked'
    ]);

    $new_asset = FixedAssetRegistration::where('fixed_asset_registrations.id', '=', $request->get("id"))
    ->leftJoin('fixed_asset_clasifications', 'fixed_asset_clasifications.category_code', '=', 'fixed_asset_registrations.clasification_id')
    ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'fixed_asset_registrations.created_by')
    ->select('fixed_asset_registrations.id', 'fixed_asset_registrations.form_number', 'fixed_asset_registrations.asset_name', 'fixed_asset_registrations.invoice_number', 'fixed_asset_registrations.vendor', 'fixed_asset_registrations.currency', 'fixed_asset_registrations.amount', 'fixed_asset_registrations.amount_usd', 'fixed_asset_registrations.pic', 'fixed_asset_registrations.investment_number', 'fixed_asset_registrations.location', 'fixed_asset_registrations.created_at', 'employee_syncs.name', 'fixed_asset_registrations.budget_number', 'fixed_asset_registrations.usage_term', 'fixed_asset_registrations.usage_estimation','fixed_asset_registrations.category', 'fixed_asset_registrations.category_code', 'fixed_asset_registrations.sap_id', 'fixed_asset_registrations.depreciation_key', db::raw('fixed_asset_clasifications.category as category_name'), 'fixed_asset_clasifications.clasification_name', 'fixed_asset_clasifications.life_time', 'fixed_asset_registrations.created_by', 'fixed_asset_registrations.sap_file')
    ->first();

    $atts = FixedAssetInvoice::where('form_id', '=', $new_asset->form_number)->select('att')->get();

    $data = [
        'assets' => $new_asset,
        'status' => 'APPROVAL MANAGER',
        'att' => $atts
    ];

    $mailto = db::select('SELECT email FROM send_emails where remark = (select department from employee_syncs where employee_id = "'.$new_asset->created_by.'")');

    Mail::to($mailto)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'fixed_asset_registrations'));

    $response = array(
        'status' => true,
        'asset' => $asset_upd
    );
    return Response::json($response);
}

public function approvalAsset($id, $user, $app_stat)
{
    $title = 'Approval New Asset Registration';
    $title_jp = '??';


    if ($app_stat == 'approve') {
        $message2 = 'Successfully Approved';
        $stat = true;
    } else {
        $message2 = 'Successfully Rejected';
        $stat = false;
    }

    if ($user == 'manager_user') {
        // MANAGER APPLICANT
        $message = 'Registration New Asset Form';
        $asset_upd = FixedAssetRegistration::where('id', '=', $id)
        ->update([
            'manager_app_date' => date('Y-m-d H:i:s'),
            'status' => 'approved_manager'
        ]);

        if ($stat) {
            $new_asset = FixedAssetRegistration::where('fixed_asset_registrations.id', '=', $id)
            ->leftJoin('fixed_asset_clasifications', 'fixed_asset_clasifications.category_code', '=', 'fixed_asset_registrations.clasification_id')
            ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'fixed_asset_registrations.created_by')
            ->select('fixed_asset_registrations.id', 'fixed_asset_registrations.asset_name', 'fixed_asset_registrations.invoice_number', 'fixed_asset_registrations.vendor', 'fixed_asset_registrations.currency', 'fixed_asset_registrations.amount', 'fixed_asset_registrations.amount_usd', 'fixed_asset_registrations.pic', 'fixed_asset_registrations.investment_number', 'fixed_asset_registrations.location', 'fixed_asset_registrations.created_at', 'employee_syncs.name', 'fixed_asset_registrations.budget_number', 'fixed_asset_registrations.usage_term', 'fixed_asset_registrations.usage_estimation','fixed_asset_registrations.category', 'fixed_asset_registrations.category_code', 'fixed_asset_registrations.sap_id', 'fixed_asset_registrations.depreciation_key', db::raw('fixed_asset_clasifications.category as category_name'), 'fixed_asset_clasifications.clasification_name', 'fixed_asset_clasifications.life_time', 'fixed_asset_registrations.created_by')
            ->first();

            $atts = FixedAssetInvoice::where('form_id', '=', $new_asset->form_number)->select('att')->get();

            $data = [
                'assets' => $new_asset,
                'status' => 'APPROVAL MANAGER FA',
                'att' => $atts
            ];

            $mailto = db::select('SELECT email FROM send_emails where remark = "Accounting Department"');

            Mail::to($mailto)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'fixed_asset_registrations'));
        }
    } else if ($user == 'manager_fa') {
        // MANAGER FA

        $message = 'Registration New Asset Form';
        $asset_upd = FixedAssetRegistration::where('id', '=', $id)
        ->update([
            'manager_fa_date' => date('Y-m-d H:i:s'),
            'status' => 'approved_manager_fa'
        ]);
    }

    return view('fixed_asset.approval_message', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'message' => $message,
        'message2' => $message2,
        'status' => $stat
    ))->with('page', 'Fixed Asset Approval');  
}

    //=============================================================//
    //                     Payment Request                         //
    //=============================================================//

public function IndexPaymentRequest(){

    $title = "Payment Request";
    $title_jp = "支払リクエスト";

    $employees = EmployeeSync::orderBy('department', 'asc')->get();

    $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
    ->distinct()
    ->get();

    $payment_term = AccInvoicePaymentTerm::select('*')->whereNull('deleted_at')
    ->distinct()
    ->get();

    return view('accounting_purchasing.payment_request.index_payment', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'employees' => $employees,
        'vendor' => $vendor,
        'payment_term' => $payment_term
    ));
}

public function fetchPaymentRequest(){
    $payment = db::select("SELECT
                    *
        FROM
        acc_payment_requests
        order by id desc
        ");

    $response = array(
        'status' => true,
        'payment' => $payment
    );
    return Response::json($response);
}

public function fetchPaymentRequestDetail(Request $request){

    $payment = AccPaymentRequest::find($request->get('id'));

    $employees = EmployeeSync::orderBy('department', 'asc')->get();

    $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
    ->distinct()
    ->get();

    $payment_term = AccInvoicePaymentTerm::select('*')->whereNull('deleted_at')
    ->distinct()
    ->get();

    $response = array(
        'status' => true,
        'payment' => $payment,
        'vendor' => $vendor,
        'payment_term' => $payment_term
    );
    return Response::json($response);
}

public function createPaymentRequest(Request $request){
    try{

        $manager = null;
        $manager_name = null;
        $dgm = null;
        $gm = null;

        $tujuan_upload = 'files/payment';

        $file = $request->file('file_attach');
        if ($file != null || $file != "") {
            $nama = $file->getClientOriginalName();
            $filename = pathinfo($nama, PATHINFO_FILENAME);
            $extension = pathinfo($nama, PATHINFO_EXTENSION);
            $filename = md5($filename.date('YmdHisa')).'.'.$extension;
            $file->move($tujuan_upload,$filename);
        }else{
            $filename = null;
        }

        if($request->input('kind_of') == "Electricity" || $request->input('kind_of') == "Gas" || $request->input('kind_of') == "Water")
        {
            $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Maintenance Department' and position = 'manager'");
        }

        if ($manag != null)
        {
            foreach ($manag as $mg)
            {
                $manager = $mg->employee_id;
                $manager_name = $mg->name;
            }

            $dgm = $this->dgm;
            $gm = $this->gm;
        }
        else{
            $manager = null;
            $manager_name = null;
            $dgm = null;
            $gm = null;
        }

        $payment = new AccPaymentRequest([
            'payment_date' => $request->input('payment_date'),
            'supplier_code' => $request->input('supplier_code'),
            'supplier_name' => $request->input('supplier_name'),
            'currency' => $request->input('currency'),
            'payment_term' => $request->input('payment_term'),
            'payment_due_date' => $request->input('payment_due_date'),
            'amount' => $request->input('amount'),
            'kind_of' => $request->input('kind_of'),
            'attach_document' => $request->input('attach_document'),
            'file' => $filename,
            'pdf' => 'Payment '.$request->input('kind_of'). ' '.date('d-M-y', strtotime($request->input('payment_date'))).'.pdf',
            'posisi' => 'user', 
            'status' => 'approval', 
            'manager' => $manager,
            'manager_name' => $manager_name,
            'dgm' => $dgm, 
            'gm' => $gm,
            'created_by' => Auth::user()->username,
            'created_name' => Auth::user()->name
        ]);

        $payment->save();

        $payment_data = AccPaymentRequest::where('id','=',$payment->id)->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
            'payment' => $payment_data,
            'id' => $payment->id
        ));

        $pdf->save(public_path() . "/payment_list/Payment ".$request->input('kind_of'). " ".date('d-M-y', strtotime($request->input('payment_date'))).".pdf");


        $response = array(
            'status' => true,
            'message' => 'New Payment Request Successfully Added'
        );
        return Response::json($response);
    }
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function editPaymentRequest(Request $request){
    try{
        $payment = AccPaymentRequest::where('id', '=', $request->get('id_edit'))->first();

        $file = $request->file('file_attach');
        $tujuan_upload = 'files/payment';

        if ($file != null || $file != "") {
            $nama = $file->getClientOriginalName();
            $filename = pathinfo($nama, PATHINFO_FILENAME);
            $extension = pathinfo($nama, PATHINFO_EXTENSION);
            $filename = md5($filename.date('YmdHisa')).'.'.$extension;
            $file->move($tujuan_upload,$filename);
        }else{
            $filename = $payment->file;
        }

        $document_attach = "";

        if ($request->input('attach_document') != null || $request->input('attach_document') != "") {
            $document_attach = $request->input('attach_document');
        }
        else{
            $document_attach = $payment->attach_document;
        }

        $payment->payment_date = $request->input('payment_date');
        $payment->supplier_code = $request->input('supplier_code');
        $payment->supplier_name = $request->input('supplier_name');
        $payment->currency = $request->input('currency');
        $payment->payment_term = $request->input('payment_term');
        $payment->payment_due_date = $request->input('payment_due_date');
        $payment->amount = $request->input('amount');
        $payment->kind_of = $request->input('kind_of');
        $payment->attach_document = $request->input('attach_document');
        $payment->file = $filename;
        $payment->created_by = Auth::user()->username;
        $payment->save();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
            'payment' => $payment,
            'id' => $payment->id
        ));

        $pdf->save(public_path() . "/payment_list/Payment ".$request->input('kind_of'). " ".date('d-M-y', strtotime($request->input('payment_date'))).".pdf");

        $response = array(
            'status' => true,
            'message' => 'Payment Request Updated'
        );
        return Response::json($response);
    }
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function reportPaymentRequest($id){
    $payment = AccPaymentRequest::find($id);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
        'payment' => $payment,
        'id' => $id
    ));
    return $pdf->stream("Payment ".$payment->kind_of. " ".date('d-M-y', strtotime($payment->payment_date)).".pdf");
}

public function emailPaymentRequest(Request $request){
    $pr = AccPaymentRequest::find($request->get('id'));

    try{
        if ($pr->posisi == "user")
        {
            $mails = "select distinct email from acc_payment_requests join users on acc_payment_requests.manager = users.username where acc_payment_requests.id = ".$request->get('id');
            $mailtoo = DB::select($mails);

            $pr->posisi = "manager";
            $pr->save();

            $isimail = "select * from acc_payment_requests where acc_payment_requests.id = ".$request->get('id');
            $payment = db::select($isimail);

            Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($payment, 'payment_request'));

            $response = array(
              'status' => true,
              'datas' => "Berhasil"
          );

            return Response::json($response);
        }
    } 
    catch (Exception $e) {
        $response = array(
          'status' => false,
          'datas' => "Gagal"
      );
        return Response::json($response);
    }
}

public function paymentapprovalmanager($id){
    $pr = AccPaymentRequest::find($id);
    try{
        if ($pr->posisi == "manager")
        {
            if ($pr->dgm != null) {
                $pr->posisi = "dgm";
                $pr->status_manager = "Approved/".date('Y-m-d H:i:s');

                $mailto = "select distinct email from acc_payment_requests join users on acc_payment_requests.dgm = users.username where acc_payment_requests.id = '" . $id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }

                $pr->save();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');

                $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
                    'payment' => $pr,
                    'id' => $id
                ));

                $pdf->save(public_path() . "/payment_list/Payment ".$pr->kind_of. " ".date('d-M-y', strtotime($pr->payment_date)).
                    ".pdf");

                $isimail = "select * from acc_payment_requests where acc_payment_requests.id = ".$id;
                $payment = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($payment, 'payment_request'));

                $message = 'Payment Request '.$pr->kind_of;
                $message2 ='Successfully Approved';
            }
            
            else{

                $pr->posisi = "gm";
                $pr->status_manager = "Approved/".date('Y-m-d H:i:s');

                $mailto = "select distinct email from acc_payment_requests join users on acc_payment_requests.gm = users.username where acc_payment_requests.id = '" . $id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }

                $pr->save();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');

                $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
                    'payment' => $pr,
                    'id' => $id
                ));

                $pdf->save(public_path() . "/payment_list/Payment ".$pr->kind_of. " ".date('d-M-y', strtotime($pr->payment_date)).
                    ".pdf");

                $isimail = "select * from acc_payment_requests where acc_payment_requests.id = ".$id;
                $payment = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($payment, 'payment_request'));

                $message = 'Payment Request '.$pr->kind_of;
                $message2 ='Successfully Approved';

                
                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');

                $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
                    'payment' => $pr,
                    'id' => $id
                ));

                $pdf->save(public_path() . "/payment_list/Payment ".$pr->kind_of. " ".date('d-M-y', strtotime($pr->payment_date)).
                    ".pdf");
            }
        }
        else{
            $message = 'Payment Request '.$pr->kind_of;
            $message2 ='Already Approved / Rejected';
        }

        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $pr->kind_of,
            'message' => $message,
            'message2' => $message2,
        ))->with('page', 'Payment Request');

    } catch (Exception $e) {
        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $pr->kind_of,
            'message' => 'Error',
            'message2' => $e->getMessage(),
        ))->with('page', 'Payment Request');
    }
}

public function paymentapprovaldgm($id){
    $pr = AccPaymentRequest::find($id);
    try{
        if ($pr->posisi == "dgm")
        {
            $pr->posisi = "gm";
            $pr->status_dgm = "Approved/".date('Y-m-d H:i:s');

            $mailto = "select distinct email from acc_payment_requests join users on acc_payment_requests.gm = users.username where acc_payment_requests.id = '" . $pr->id . "'";
            $mails = DB::select($mailto);

            foreach ($mails as $mail)
            {
                $mailtoo = $mail->email;
            }

            $pr->save();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
                'payment' => $pr,
                'id' => $id
            ));

            $pdf->save(public_path() . "/payment_list/Payment ".$pr->kind_of. " ".date('d-M-y', strtotime($pr->payment_date)).
                ".pdf");

            $isimail = "select * from acc_payment_requests where acc_payment_requests.id = ".$id;
            $payment = db::select($isimail);

            Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($payment, 'payment_request'));

            $message = 'Payment Request '.$pr->kind_of;
            $message2 ='Successfully Approved';

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
                'payment' => $pr,
                'id' => $id
            ));

            $pdf->save(public_path() . "/payment_list/Payment ".$pr->kind_of. " ".date('d-M-y', strtotime($pr->payment_date)).
                ".pdf");
        }
        else{
            $message = 'Payment Request '.$pr->kind_of;
            $message2 ='Already Approved / Rejected';
        }

        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $pr->kind_of,
            'message' => $message,
            'message2' => $message2,
        ))->with('page', 'Payment Request');

    } catch (Exception $e) {
        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $pr->kind_of,
            'message' => 'Error',
            'message2' => $e->getMessage(),
        ))->with('page', 'Payment Request');
    }
}

public function paymentapprovalgm($id){
    $pr = AccPaymentRequest::find($id);
    try{
        if ($pr->posisi == "gm")
        {
            $pr->posisi = 'acc';
            $pr->status_gm = "Approved/".date('Y-m-d H:i:s');
            $pr->status = "approval_acc";

                //kirim email ke Mbak Laila & Mbak Afifah
            $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = 'Accounting Department' and (employee_id = 'PI0902001'  or employee_id = 'PI1505001')";
            $mailtoo = DB::select($mails);

            $pr->save();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
                'payment' => $pr,
                'id' => $id
            ));

            $pdf->save(public_path() . "/payment_list/Payment ".$pr->kind_of. " ".date('d-M-y', strtotime($pr->payment_date)).
                ".pdf");

            $isimail = "select * from acc_payment_requests where acc_payment_requests.id = ".$id;
            $payment = db::select($isimail);

            Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($payment, 'payment_request'));

            $message = 'Payment Request '.$pr->kind_of;
            $message2 ='Successfully Approved';

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('accounting_purchasing.payment_request.report_payment_request', array(
                'payment' => $pr,
                'id' => $id
            ));

            $pdf->save(public_path() . "/payment_list/Payment ".$pr->kind_of. " ".date('d-M-y', strtotime($pr->payment_date)).
                ".pdf");
        }
        else{
            $message = 'Payment Request '.$pr->kind_of;
            $message2 ='Already Approved / Rejected';
        }

        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $pr->kind_of,
            'message' => $message,
            'message2' => $message2,
        ))->with('page', 'Payment Request');

    } catch (Exception $e) {
        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $pr->kind_of,
            'message' => 'Error',
            'message2' => $e->getMessage(),
        ))->with('page', 'Payment Request');
    }
}

public function paymentreceiveacc($id){
    $pr = AccPaymentRequest::find($id);
    try{
        if ($pr->posisi == "acc")
        {
            $pr->posisi = 'received';
            $pr->status_dgm = "Approved/".date('Y-m-d H:i:s');
            $pr->status = "received";

            $pr->save();

            $message = 'Payment Request '.$pr->kind_of;
            $message2 ='Successfully Received';
        }
        else{
            $message = 'Payment Request '.$pr->kind_of;
            $message2 ='Already Approved / Rejected';
        }

        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $pr->kind_of,
            'message' => $message,
            'message2' => $message2,
        ))->with('page', 'Payment Request');

    } catch (Exception $e) {
        return view('accounting_purchasing.verifikasi.pr_message', array(
            'head' => $pr->kind_of,
            'message' => 'Error',
            'message2' => $e->getMessage(),
        ))->with('page', 'Payment Request');
    }
}

public function paymentreject(Request $request, $id)
{
    $pr = AccPaymentRequest::find($id);

    if ($pr->posisi == "manager" || $pr->posisi == "dgm" || $pr->posisi == "gm")
    {
        $pr->datereject = date('Y-m-d H:i:s');
        $pr->posisi = "user";
        $pr->status_manager = null;
        $pr->status_dgm = null;
    }

    $pr->save();

    $isimail = "select * from acc_payment_requests where acc_payment_requests.id = ".$id;
    $tolak = db::select($isimail);

        //kirim email ke User
    $mails = "select distinct email from acc_payment_requests join users on acc_payment_requests.created_by = users.username where acc_payment_requests.id ='" . $id . "'";
    $mailtoo = DB::select($mails);

    Mail::to($mailtoo)->send(new SendEmail($tolak, 'payment_request'));

    $message = 'Payment Request '.$pr->kind_of;
    $message2 = 'Not Approved';

    return view('accounting_purchasing.verifikasi.pr_message', array(
        'head' => $pr->kind_of,
        'message' => $message,
        'message2' => $message2,
    ))->with('page', 'Payment Request');
}

public function verifikasi_purchase_requisition_canteen($id)
{
    $pr = CanteenPurchaseRequisition::find($id);

    $items = CanteenPurchaseRequisitionItem::select('canteen_purchase_requisition_items.*')->join('canteen_purchase_requisitions', 'canteen_purchase_requisition_items.no_pr', '=', 'canteen_purchase_requisitions.no_pr')
    ->where('canteen_purchase_requisitions.id', '=', $id)->get();

    $path = '/kantin/pr_list/' . $pr->file_pdf;            
    $file_path = asset($path);

    return view('accounting_purchasing.verifikasi.pr_verifikasi_canteen', array(
        'pr' => $pr,
        'items' => $items,
        'file_path' => $file_path,
    ))->with('page', 'Purchase Requisition Canteen');
}

public function approval_purchase_requisition_canteen(Request $request, $id)
{
    $approve = $request->get('approve');

    if ($approve == "1") {

        $pr = CanteenPurchaseRequisition::find($id);

        if ($pr->posisi == "manager")
        {
            $pr->posisi = "gm";
            $pr->approvalm = "Approved";
            $pr->dateapprovalm = date('Y-m-d H:i:s');

            $mailto = "select distinct email from canteen_purchase_requisitions join users on canteen_purchase_requisitions.gm = users.username where canteen_purchase_requisitions.id = '" . $pr->id . "'";
            $mails = DB::select($mailto);

            foreach ($mails as $mail)
            {
                $mailtoo = $mail->email;
            }
        }

        else if ($pr->posisi == "gm")
        {

            $pr->posisi = 'pch';
            $pr->approvalgm = "Approved";
            $pr->dateapprovalgm = date('Y-m-d H:i:s');
            $pr->status = "approval_acc";

                //kirim email ke Mas Shega & Mas Hamzah
            $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = 'Purchasing Control Department' and (employee_id = 'PI1810020'  or employee_id = 'PI0904006')";
            $mailtoo = DB::select($mails);
        }

        $pr->save();

        $detail_pr = CanteenPurchaseRequisition::select('canteen_purchase_requisitions.*','canteen_purchase_requisition_items.*','canteen_budget_histories.beg_bal','canteen_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
        ->leftJoin('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
        ->join('canteen_budget_histories', function($join) {
            $join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
            $join->on('canteen_budget_histories.no_item','=', 'canteen_purchase_requisition_items.item_desc');
        })
        ->where('canteen_purchase_requisitions.id', '=', $id)
        ->distinct()
        ->get();

        $exchange_rate = AccExchangeRate::select('*')
        ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
        ->where('currency','!=','USD')
        ->orderBy('currency','ASC')
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('general_affairs.report.report_pr', array(
            'pr' => $detail_pr,
            'rate' => $exchange_rate
        ));

        $pdf->save(public_path() . "/kantin/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

        $isimail = "select canteen_purchase_requisitions.*,canteen_purchase_requisition_items.item_desc, canteen_purchase_requisition_items.item_qty, canteen_purchase_requisition_items.item_uom, canteen_purchase_requisition_items.item_price, canteen_purchase_requisition_items.item_amount FROM canteen_purchase_requisitions join canteen_purchase_requisition_items on canteen_purchase_requisitions.no_pr = canteen_purchase_requisition_items.no_pr where canteen_purchase_requisitions.id = ".$id;
        $purchaserequisition = db::select($isimail);

        Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($purchaserequisition, 'canteen_purchase_requisition'));

        return redirect('/canteen/purchase_requisition/verifikasi/' . $id)->with('status', 'PR Canteen Approved')
        ->with('page', 'Purchase Requisition Canteen');
    }
    else
    {
        return redirect('/canteen/purchase_requisition/verifikasi/' . $id)->with('error', 'PR Canteen Not Approved')
        ->with('page', 'Purchase Requisition Canteen');
    }
}

public function reject_purchase_requisition_canteen(Request $request, $id)
{
    $alasan = $request->get('alasan');

    $pr = CanteenPurchaseRequisition::find($id);

    if ($pr->posisi == "manager" || $pr->posisi == "gm")
    {
        $pr->alasan = $alasan;
        $pr->datereject = date('Y-m-d H:i:s');
        $pr->posisi = "user";
        $pr->approvalm = null;
        $pr->dateapprovalm = null;
    }

    $pr->save();

    $isimail = "select canteen_purchase_requisitions.*,canteen_purchase_requisition_items.item_desc, canteen_purchase_requisition_items.item_qty, canteen_purchase_requisition_items.item_uom, canteen_purchase_requisition_items.item_price, canteen_purchase_requisition_items.item_amount FROM canteen_purchase_requisitions join canteen_purchase_requisition_items on canteen_purchase_requisitions.no_pr = canteen_purchase_requisition_items.no_pr where canteen_purchase_requisitions.id = ".$pr->id;

    $tolak = db::select($isimail);

        //kirim email ke User
    $mails = "select distinct email from canteen_purchase_requisitions join users on canteen_purchase_requisitions.emp_id = users.username where canteen_purchase_requisitions.id ='" . $pr->id . "'";
    $mailtoo = DB::select($mails);

    Mail::to($mailtoo)->send(new SendEmail($tolak, 'canteen_purchase_requisition'));
    return redirect('/canteen/purchase_requisition/verifikasi/' . $id)->with('status', 'PR Canteen Not Approved')
    ->with('page', 'Purchase Requisition Canteen');
}


public function monitoringPRCanteen(){

    $dept = db::select("select DISTINCT department from employee_syncs");

    return view('accounting_purchasing.display.pr_canteen_monitoring',  
        array(
          'title' => 'Purchase Requisition Canteen Monitoring', 
          'title_jp' => 'PR監視・管理',
          'department' => $dept,
      )
    )->with('page', 'Purchase Requisition Canteen');
}

public function fetchMonitoringPRCanteen(Request $request){

  $datefrom = date("Y-m-d", strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $last = CanteenPurchaseRequisition::whereNull('receive_date')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(submission_date) as tanggal'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
}
else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
    }
}
}

if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
}

        //per tgl
$data = db::select("
   select date.week_date, coalesce(belum_diterima.total, 0) as jumlah_belum, coalesce(sudah_diterima.total, 0) as jumlah_sudah from 
   (select week_date from weekly_calendars 
   where date(week_date) >= '".$datefrom."'
   and date(week_date) <= '".$dateto."') date
   left join
   (select date(submission_date) as date, count(id) as total from canteen_purchase_requisitions
   where date(submission_date) >= '".$datefrom."' and date(submission_date) <= '".$dateto."' and canteen_purchase_requisitions.deleted_at is null and receive_date is null 
   group by date(submission_date)) belum_diterima
   on date.week_date = belum_diterima.date
   left join
   (select date(submission_date) as date, count(id) as total from canteen_purchase_requisitions
   where date(submission_date) >= '".$datefrom."' and date(submission_date) <= '".$dateto."' and canteen_purchase_requisitions.deleted_at is null and receive_date is not null
   group by date(submission_date)) sudah_diterima
   on date.week_date = sudah_diterima.date
   order by week_date asc");

$data_pr_belum_po = db::select("
    select canteen_purchase_requisitions.no_pr, sum(case when sudah_po is null then 1 else 0 end) as belum_po, sum(case when sudah_po is not null then 1 else 0 end) as sudah_po from canteen_purchase_requisitions left join canteen_purchase_requisition_items on canteen_purchase_requisitions.no_pr = canteen_purchase_requisition_items.no_pr where canteen_purchase_requisitions.deleted_at is null and receive_date is not null GROUP BY no_pr order by submission_date asc");

$year = date('Y');

$response = array(
    'status' => true,
    'datas' => $data,
    'data_pr_belum_po' => $data_pr_belum_po,
    'year' => $year
);

return Response::json($response);
}


public function fetchtablePRCanteen(Request $request)
{
  $datefrom = $request->get('datefrom');
  $dateto = $request->get('dateto');

  if ($datefrom == "") {
      $datefrom = date('Y-m', strtotime(carbon::now()->subMonth(11)));
  }

  if ($dateto == "") {
      $dateto = date('Y-m', strtotime(carbon::now()));
  }

  $data = db::select("select id, no_pr, emp_id, emp_name, department, section, no_budget, submission_date, receive_date, file, posisi, `status`, manager, manager_name, (select `name` from employee_syncs where employee_id = gm) as gm , approvalm, dateapprovalm, approvalgm, dateapprovalgm, alasan, datereject from canteen_purchase_requisitions where canteen_purchase_requisitions.STATUS != 'received' and canteen_purchase_requisitions.deleted_at is null and DATE_FORMAT(submission_date,'%Y-%m') between '".$datefrom."' and '".$dateto."' order by submission_date asc");
  $response = array(
    'status' => true,
    'datas' => $data
);

  return Response::json($response); 
}

public function detailMonitoringPRCanteen(Request $request){

  $tanggal = $request->get("tanggal");
  $status = $request->get("status");
  $tglfrom = $request->get("tglfrom");
  $tglto = $request->get("status");

  $status_sign = "";

  if ($status == "PR Incompleted") {
      $status_sign = "and receive_date is null";
  }
  else if ($status == "PR Completed") {
      $status_sign = "and receive_date is not null";
  }

  $qry = "SELECT  * FROM canteen_purchase_requisitions WHERE deleted_at IS NULL and submission_date = '".$tanggal."' and DATE_FORMAT(submission_date,'%Y-%m') between '".$tglfrom."' and '".$tglto."' ".$status_sign." ";

  $pr = DB::select($qry);

  return DataTables::of($pr)
  ->editColumn('submission_date', function ($pr)
  {
    return $pr->submission_date;
})
  ->editColumn('note', function ($pr)
  {
    $note = "";
    if ($pr->note != null)
    {
        $note = $pr->note;
    }
    else
    {
        $note = '-';
    }

    return $note;
})
  ->editColumn('status', function ($pr)
  {
    $id = $pr->id;

    if ($pr->status == "approval")
    {
        return '<label class="label label-warning">Approval</a>';
    }
    else if ($pr->status == "approval_acc")
    {
        return '<label class="label label-info">Diverifikasi Purchasing</a>';
    }
    else if ($pr->status == "received")
    {
        return '<label class="label label-success">Diterima Purchasing</a>';
    }

})
  ->addColumn('action', function ($pr)
  {
    $id = $pr->id;

                    // <a href="purchase_requisition/detail/' . $id . '" class="btn btn-info btn-xs" data-toggle="tooltip" title="Detail PR"><i class="fa fa-eye"></i></a>

    if($pr->status == "approval"){
        return '
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        ';
    }
    else{
        return '
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        ';
    }


})
  ->editColumn('file', function ($pr)
  {

    $data = json_decode($pr->file);

    $fl = "";

    if ($pr->file != null)
    {
        for ($i = 0;$i < count($data);$i++)
        {
            $fl .= '<a href="../files/pr_kantin/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
        }
    }
    else
    {
        $fl = '-';
    }

    return $fl;
})
  ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file'])
  ->make(true);
}

public function detailMonitoringPRPOCanteen(Request $request){

    $pr = $request->get("pr");
    $status = $request->get("status");
    $tglfrom = $request->get("tglfrom");
    $tglto = $request->get("status");

    $status_sign = "";

    if ($status == "Belum PO") {
        $status_sign = "and sudah_po is null";
    }
    else if ($status == "Sudah PO") {
        $status_sign = "and sudah_po is not null";
    }


    $qry = "SELECT canteen_purchase_requisitions.no_pr,canteen_purchase_requisitions.submission_date, canteen_purchase_requisition_items.*, (select DATE(created_at) from canteen_purchase_order_details where canteen_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order, (select supplier_name from canteen_purchase_order_details join canteen_purchase_orders on canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po where canteen_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY canteen_purchase_order_details.created_at desc limit 1) as last_vendor from canteen_purchase_requisitions left join canteen_purchase_requisition_items on canteen_purchase_requisitions.no_pr = canteen_purchase_requisition_items.no_pr WHERE canteen_purchase_requisition_items.deleted_at is NULL and canteen_purchase_requisitions.no_pr = '".$pr."' and DATE_FORMAT(submission_date,'%Y-%m') between '".$tglfrom."' and '".$tglto."' ".$status_sign." ";

    $pr = DB::select($qry);

    return DataTables::of($pr)
    ->editColumn('item_request_date', function ($pr)
    {
        return date('d-M-Y', strtotime($pr->item_request_date)) ;
    })
    ->editColumn('item_qty', function ($pr)
    {
        return $pr->item_qty.' '.$pr->item_uom;
    })
    ->editColumn('item_price', function ($pr)
    {
        return number_format($pr->item_price,0,",",".");
    })
    ->editColumn('item_amount', function ($pr)
    {
        return number_format($pr->item_amount,0,",",".");
    })
    ->editColumn('last_order', function ($pr)
    {
        if ($pr->last_order != null) {
            return date('d-M-Y', strtotime($pr->last_order)) ;
        }
        else{
            return '-';
        }
    })

    ->editColumn('status', function ($pr)
    {
        if ($pr->sudah_po == null) {
            return '<span class="label label-danger">Belum PO</span>';
        }
        else if ($pr->sudah_po != null) {
            return '<span class="label label-success">Sudah PO</span>';
        }
    })
    ->rawColumns(['status' => 'status'])
    ->make(true);
}


    //==================================//
    //      Purchase Order Kantin       //
    //==================================//

public function purchase_order_canteen()
{
    $title = 'Purchase Order Canteen';
    $title_jp = '';

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
    ->first();

    $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
    ->distinct()
    ->get();

    $authorized2 = EmployeeSync::select('employee_id', 'name')->where('position', '=', 'Manager')
    ->where('department', 'like', '%Procurement%')
    ->first();

    return view('accounting_purchasing.kantin.purchase_order_kantin', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'employee' => $emp,
        'vendor' => $vendor,
        'delivery' => $this->delivery,
        'transportation' => $this->transportation,
        'authorized2' => $authorized2,
        'uom' => $this->uom
    ))
    ->with('page', 'Purchase Order Canteen')
    ->with('head', 'Purchase Order Canteen');
}

public function fetch_purchase_order_canteen(Request $request)
{
    $tanggal = "";
    $adddepartment = "";

    if (strlen($request->get('datefrom')) > 0)
    {
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        $tanggal = "and A.tgl_po >= '" . $datefrom . " 00:00:00' ";
        if (strlen($request->get('dateto')) > 0)
        {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
            $tanggal = $tanggal . "and A.tgl_po  <= '" . $dateto . " 23:59:59' ";
        }
    }

    $qry = "SELECT * FROM canteen_purchase_orders A WHERE A.deleted_at IS NULL " . $tanggal . " order by A.id DESC";
    $po = DB::select($qry);

    return DataTables::of($po)

    ->editColumn('tgl_po', function ($po)
    {
        return date('Y-m-d', strtotime($po->tgl_po));
    })

    ->editColumn('status', function ($po)
    {
        $id = $po->id;

        if ($po->posisi == "staff_pch")
        {
            return '<label class="label label-danger">Staff Purchasing</label>';
        }

        else if ($po->posisi == "manager_pch")
        {
            return '<label class="label label-warning">Diverifikasi Manager</label>';
        }

        else if ($po->posisi == "pch")
        {
            return '<label class="label label-success">Sudah Diverifikasi</label>';
        }

    })
    ->addColumn('action', function ($po)
    {
        $id = $po->id;
        if ($po->posisi == "staff_pch") {
            return '
            <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i> Edit</a>
            <a href="canteen/purchase_order/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
            <button class="btn btn-xs btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;"  onclick="sendEmail(' . $id .')"><i class="fa fa-envelope"></i> Send Email</button>
            <a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="cancelPO('.$id.')" data-toggle="modal" data-target="#modalcancelPO"  title="Delete PO"><i class="fa fa-trash"></i> Delete PO</a>
            ';
        }

        else if ($po->posisi == "pch") {
            return '
            <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i> Edit</a>
            <a href="canteen/purchase_order/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"> Report</i></a>
            <a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="cancelPO('.$id.')" data-toggle="modal" data-target="#modalcancelPO"  title="Cancel PO"><i class="fa fa-close"></i> Cancel PO</a>
            ';
        }

        else{
            return '
            <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i> Edit</a>
            <a href="canteen/purchase_order/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
            <label class="label label-success">Email Sudah Dikirim</label>
            ';   
        }
    })
    ->rawColumns(['status' => 'status', 'action' => 'action'])
    ->make(true);
}

public function fetch_po_outstanding_canteen(Request $request)
{
    $qry = "SELECT distinct canteen_purchase_requisitions.id, canteen_purchase_requisitions.no_pr,department,submission_date,emp_id,emp_name,no_budget,file,posisi,`status`,file_pdf FROM `canteen_purchase_requisitions` join canteen_purchase_requisition_items on canteen_purchase_requisitions.no_pr = canteen_purchase_requisition_items.no_pr where receive_date is not null and canteen_purchase_requisition_items.sudah_po is null and canteen_purchase_requisitions.deleted_at is null";
    $pr = DB::select($qry);

    return DataTables::of($pr)

    ->editColumn('submission_date', function ($pr)
    {
        return $pr->submission_date;
    })

    ->editColumn('file', function ($pr)
    {
        $data = json_decode($pr->file);

        $fl = "";

        if ($pr->file != null)
        {
            for ($i = 0;$i < count($data);$i++)
            {
                $fl .= '<a href="files/pr/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
            }
        }
        else
        {
            $fl = '-';
        }

        return $fl;
    })

    ->addColumn('action', function ($pr)
    {
        $id = $pr->id;

        return '
        <a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="editPR(' . $id . ')" data-toggle="tooltip" title="Edit PR"><i class="fa fa-edit"></i> Edit</a>
        <a href="canteen/purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
        <a href="javascript:void(0)" class="btn btn-xs  btn-primary" onClick="detailPR(' . $id . ')" style="margin-right:5px;" data-toggle="tooltip" title="Detail PR"><i class="fa fa-eye"></i> Detail Item</a>
        ';
    })

    ->rawColumns(['file' => 'file', 'action' => 'action'])
    ->make(true);
}

public function detail_pr_po_canteen(Request $request)
{
    $purchase_requistion = CanteenPurchaseRequisition::find($request->get('id'));
    $purchase_requistion_item = CanteenPurchaseRequisition::select('canteen_purchase_requisition_items.*','canteen_budget_histories.budget', 'canteen_budget_histories.budget_month', 'canteen_budget_histories.budget_date', 'canteen_budget_histories.category_number','canteen_budget_histories.no_item','canteen_budget_histories.amount','canteen_budget_histories.beg_bal')
    ->join('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
        // ->leftJoin('acc_items', 'canteen_purchase_requisition_items.item_code', '=', 'acc_items.kode_item')
    ->join('canteen_budget_histories', function($join) {
       $join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
       $join->on('canteen_budget_histories.no_item','=', 'canteen_purchase_requisition_items.item_desc');
   })
    ->where('canteen_purchase_requisitions.id', '=', $request->get('id'))
    ->get();

    $response = array(
        'status' => true,
        'purchase_requisition' => $purchase_requistion,
        'purchase_requisition_item' => $purchase_requistion_item
    );
    return Response::json($response);
}

public function update_purchase_requisition_canteen_po(Request $request)
{
    $id = Auth::id();
    $lop3 = $request->get('lop3');
    $lop = explode(',', $request->get('looping_pr'));

    $total_nambah = 0;
    $total_update = 0;
    $counter = 0;

    try
    {
        foreach ($lop as $lp)
        {
            $item_code = "item_code_edit" . $lp;
            $item_desc = "item_desc_edit" . $lp;
            $item_uom = "uom_edit" . $lp;
            $item_req = "req_date_edit" . $lp;
            $item_qty = "qty_edit" . $lp;
            $item_price = "item_price_edit" . $lp;
            $item_amount = "amount_edit" . $lp;

            $getitem = CanteenPurchaseRequisitionItem::where('id', $lp)->first();

            $data10 = CanteenBudgetHistory::where('no_item',$getitem->item_desc)
            ->update([
                'no_item' => $request->get($item_desc),
            ]);


            $data2 = CanteenPurchaseRequisitionItem::where('id', $lp)->update([
              'item_code' => $request->get($item_code), 
              'item_desc' => $request->get($item_desc), 
              'item_uom' => $request->get($item_uom), 
              'item_request_date' => $request->get($item_req), 
              'item_qty' => $request->get($item_qty),
              'item_price' => $request->get($item_price),
              'item_amount' => $request->get($item_amount),
              'created_by' => $id
          ]);


            if ($data2) {
                $konversi = "konversi_dollar" . $lp;

                $getamount = CanteenBudgetHistory::where('budget', $request->get('no_budget_edit'))
                ->where('category_number',$request->get('no_pr_edit'))
                ->where('no_item',$request->get($item_desc))
                ->first();

                $counter = $counter + $getamount->amount;

                $data5 = CanteenBudgetHistory::where('budget', $request->get('no_budget_edit'))
                ->where('category_number',$request->get('no_pr_edit'))
                ->where('no_item',$request->get($item_desc))
                ->update([
                    'amount' => $request->get($konversi),
                    'created_by' => $id
                ]);

                $total_update = $total_update + $request->get($konversi);
            }
        }

        for ($i = 2;$i <= $lop3;$i++)
        {

            $item_code = "item_code" . $i;
            $item_desc = "item_desc" . $i;
            $item_req = "req_date" . $i;
            $item_currency = "item_currency" . $i;
            $item_currency_text = "item_currency_text" . $i;
            $item_price = "item_price" . $i;
            $item_qty = "qty" . $i;
            $item_uom = "uom" . $i;
            $item_amount = "amount" . $i;

                    //Jika ada value kosong
            if ($request->get($item_code) == "kosong")
            {
                $request->get($item_code) == "";
            }

            if ($request->get($item_currency) != "")
            {
                $current = $request->get($item_currency);
            }
            else if ($request->get($item_currency_text) != "")
            {
                $current = $request->get($item_currency_text);
            }

            $data2 = new CanteenPurchaseRequisitionItem([
                'no_pr' => $request->get('no_pr_edit') , 
                'item_code' => $request->get($item_code) , 
                'item_desc' => $request->get($item_desc) ,
                'item_request_date' => $request->get($item_req) , 
                'item_currency' => $current,
                'item_price' => $request->get($item_price),
                'item_qty' => $request->get($item_qty) , 
                'item_uom' => $request->get($item_uom) , 
                'item_amount' => $request->get($item_amount), 
                'created_by' => $id
            ]);

            $data2->save();

            $dollar = "konversi_dollar" . $i;
            $month = strtolower(date("M",strtotime($request->get('tgl_pengajuan_edit'))));
            $begbal = $request->get('SisaBudgetEdit') + $request->get('TotalPembelianEdit');

            $data3 = new CanteenBudgetHistory([
                'budget' => $request->get('no_budget_edit'),
                'budget_month' => $month,
                'budget_date' => $request->get('tgl_pengajuan_edit'),
                'category_number' => $request->get('no_pr_edit'),
                'beg_bal' => $begbal,
                'no_item' => $request->get($item_desc),
                'amount' => $request->get($dollar),
                'created_by' => $id
            ]);

            $data3->save();

            $total_nambah += $request->get($dollar);
        }

        $getbulan = AccBudget::select('budget_no', 'periode')
        ->where('budget_no', $request->get('no_budget_edit'))
        ->first();

        if ($getbulan->periode == "FY198") {
            $bulan = strtolower(date('M'));
        }
        else{
            $bulan = "apr";
        }

            // $bulan = strtolower(date("M",strtotime($datePembelian))); //aug,sep,oct

        $sisa_bulan = $bulan.'_sisa_budget';                    
            //get Data Budget Based On Periode Dan Nomor
        $budget = AccBudget::where('budget_no','=',$request->get('no_budget_edit'))->first();

        $total = $budget->$sisa_bulan + $counter - $total_update - $total_nambah;

        if ($total < 0) {
            return redirect('/purchase_order_canteen')->with('error', 'Total Melebihi Budget')
            ->with('page', 'Purchase Order Canteen');
        } else{
            $dataupdate = AccBudget::where('budget_no',$request->get('no_budget_edit'))->update([
                $sisa_bulan => $total
            ]);
        }

        $detail_pr = CanteenPurchaseRequisition::select('canteen_purchase_requisitions.*','canteen_purchase_requisition_items.*','canteen_budget_histories.beg_bal','canteen_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
        ->leftJoin('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
        ->join('canteen_budget_histories', function($join) {
            $join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
            $join->on('canteen_budget_histories.no_item','=', 'canteen_purchase_requisition_items.item_desc');
        })
        ->where('canteen_purchase_requisitions.id', '=', $request->get('id_edit_pr'))
        ->distinct()
        ->get();

        $exchange_rate = AccExchangeRate::select('*')
        ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
        ->where('currency','!=','USD')
        ->orderBy('currency','ASC')
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('general_affairs.report.report_pr', array(
            'pr' => $detail_pr,
            'rate' => $exchange_rate
        ));

        $pdf->save(public_path() . "/kantin/pr_list/PR".$request->get('no_pr_edit').".pdf");


        return redirect('/purchase_order_canteen')
        ->with('status', 'Purchase Requisition Berhasil Dirubah')
        ->with('page', 'Purchase Order Canteen');
    }
    catch(QueryException $e)
    {
        return redirect('/purchase_order_canteen')->with('error', $e->getMessage())
        ->with('page', 'Purchase Order Canteen');
    }
}

public function fetchPrKantinList(Request $request)
{
    $pr = CanteenPurchaseRequisition::select('canteen_purchase_requisitions.no_pr')->join('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
    ->whereNull('sudah_po')
    ->whereNotNull('receive_date')
    ->distinct()
    ->get();

    $response = array(
        'status' => true,
        'pr' => $pr
    );

    return Response::json($response);
}

public function pilihPrKantin(Request $request)
{
    $html = array();
    $list_item = CanteenPurchaseRequisitionItem::where('no_pr', $request->no_pr)
    ->whereNull('sudah_po')
    ->get();

    $lists = "<option value=''>-- Pilih Item --</option>";
    foreach ($list_item as $item)
    {
        $lists .= "<option value='" . $item->item_code . "'>" . $item->item_desc . "</option>"; 

    }
    return json_encode($lists);
}

public function pogetitemkantin(Request $request)
{
    $html = array();
    $kode_item = CanteenPurchaseRequisitionItem::join('canteen_purchase_requisitions', 'canteen_purchase_requisition_items.no_pr', '=', 'canteen_purchase_requisitions.no_pr')
    ->where('item_code', $request->item_code)
    ->where('canteen_purchase_requisition_items.no_pr', $request->no_pr)
    ->get();

    $last_price = CanteenPurchaseOrderDetail::select('goods_price')
    ->where('no_item', $request->item_code)
    ->orderBy('id','desc')
    ->get();

    if(count($last_price) > 0){
        $last = $last_price[0]->goods_price;
    }else{
        $last = 0;
    }

    foreach ($kode_item as $item)
    {
        $html = array(
            'item_code' => $item->item_code,
            'item_desc' => $item->item_desc,
            'item_request_date' => $item->item_request_date,
            'item_qty' => $item->item_qty,
            'item_uom' => $item->item_uom,
            'item_currency' => $item->item_currency,
            'item_price' => $item->item_price,
            'last_price' => $last,
            'no_budget' => $item->no_budget,
        );

    }

    return json_encode($html);
}

public function create_purchase_order_canteen(Request $request)
{
    $id = Auth::id();

    $lop = $request->get('lop');
    $nopo = $request->get('no_po1') . $request->get('no_po2');

    $total_dollar = 0;

    try
    {
        $last = substr($nopo, -2);

        $query = "SELECT * FROM `cost_centers2` where code = '$last'";
        $cost_center = DB::select($query);

        foreach ($cost_center as $cc) {
            $cost = $cc->cost_center;
        }

        $data = new CanteenPurchaseOrder([
            'remark' => $request->get('remark'), 
            'no_po' => $nopo, 
            'tgl_po' => $request->get('tgl_po') , 
            'supplier_code' => $request->get('supplier_code') , 
            'supplier_name' => $request->get('supplier_name') , 
            'supplier_due_payment' => $request->get('supplier_due_payment') , 
            'supplier_status' => $request->get('supplier_status') , 
            'material' => $request->get('material') , 
            'vat' => $request->get('price_vat') , 
            'transportation' => $request->get('transportation') , 
            'delivery_term' => $request->get('delivery_term') , 
            'holding_tax' => $request->get('holding_tax') , 
            'currency' => $request->get('currency') , 
            'buyer_id' => $request->get('buyer_id') , 
            'buyer_name' => $request->get('buyer_name') , 
            'authorized2' => $request->get('authorized2') , 
            'authorized2_name' => $request->get('authorized2_name') , 
            'file_pdf' => $nopo.'.pdf' , 
            'note' => $request->get('note') , 
            'cost_center' => $cost , 
            'posisi' => 'staff_pch', 
            'status' => 'pch', 
            'created_by' => $id
        ]);

        $data->save();

        for ($i = 1;$i <= $lop;$i++)
        {
            if($request->get('remark') == "Kantin"){
                $no_pr = "no_pr" . $i;
            }
            $no_item = "no_item" . $i;
            $nama_item = "nama_item" . $i;
            $item_budget = "item_budget" . $i;
            $delivery_date = "delivery_date" . $i;
            $qty = "qty" . $i;
            $uom = "uom" . $i;
            $goods_price = "goods_price" . $i;
            $last_price = "last_price" . $i;
            $service_price = "service_price" . $i;
            $konversi_dollar = "konversi_dollar" . $i;
            $gl_number = "gl_number" . $i;

            $data2 = new CanteenPurchaseOrderDetail([
                'no_po' => $nopo, 
                'no_pr' => $request->get($no_pr) , 
                'no_item' => $request->get($no_item) , 
                'nama_item' => $request->get($nama_item) , 
                'budget_item' => $request->get($item_budget) , 
                'delivery_date' => $request->get($delivery_date) , 
                'qty' => $request->get($qty) , 
                'qty_receive' => 0, 
                'uom' => $request->get($uom) , 
                'goods_price' => $request->get($goods_price) , 
                'last_price' => $request->get($last_price) , 
                'service_price' => $request->get($service_price) , 
                'konversi_dollar' => $request->get($konversi_dollar) , 
                'gl_number' => $request->get($gl_number) ,
                'created_by' => $id
            ]);

            $data2->save();

                //Update Status Sudah PO

            if($request->get('remark') == "Kantin"){
                $data3 = CanteenPurchaseRequisitionItem::where('item_code', $request->get($no_item))
                ->where('no_pr', $request->get($no_pr))
                ->update(['sudah_po' => 'true', ]);
            }

                //Update Harga + Currency Di Master Item
            if ($request->get($goods_price) != 0) {
                $data4 = CanteenItem::where('kode_item', $request->get($no_item))
                ->update([
                    'harga' => $request->get($goods_price),
                    'currency' => $request->get('currency')
                ]);
            }
            else if ($request->get($service_price) != 0) {
                $data4 = CanteenItem::where('kode_item', $request->get($no_item))
                ->update([
                    'harga' => $request->get($service_price), 
                    'currency' => $request->get('currency')
                ]);
            }

                //Get Total Amount From Budget Log

            $data5 = CanteenBudgetHistory::where('budget', $request->get($item_budget))
            ->where('category_number',$request->get($no_pr))
            ->where('no_item',$request->get($nama_item))
            ->first();

            $amount = $data5->amount;

            $getbulan = AccBudget::select('budget_no', 'periode')
            ->where('budget_no', $request->get($item_budget))
            ->first();

            if ($getbulan->periode == "FY198") {
                $bulan = strtolower(date('M'));
            }
            else{
                $bulan = "apr";
            }

            $sisa_bulan = $bulan.'_sisa_budget';

                //get Data Budget Based On Periode Dan Nomor
            $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget))->first();

                //Tambahkan Budget Dengan Yang Ada Di Log
            $totalPlusPR = $budgetdata->$sisa_bulan + $amount;

            $updatebudget = AccBudget::where('budget_no',$request->get($item_budget))
            ->update([
                $sisa_bulan => $totalPlusPR
            ]);

                //get Data Budget Based On Periode Dan Nomor
            $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget))->first();

                //Get Amount Di PO
            $total_dollar = $request->get($konversi_dollar);

            $totalminusPO = $budgetdata->$sisa_bulan - $total_dollar;

            if ($totalminusPO < 0) {

                    //Tambahkan Budget Dengan Yang Ada Di Log
                $totalMinPR = $budgetdata->$sisa_bulan - $amount;

                $updatebudget = AccBudget::where('budget_no',$request->get($item_budget))
                ->update([
                    $sisa_bulan => $totalMinPR
                ]);

                return redirect('/purchase_order_canteen')->with('error', 'Total Melebihi Budget')
                ->with('page', 'Purchase Order Canteen');
            } 

                // Setelah itu update data budgetnya dengan yang actual
            $dataupdate = AccBudget::where('budget_no',$request->get($item_budget))
            ->update([
                $sisa_bulan => $totalminusPO
            ]);

            $updatebudgetlog = CanteenBudgetHistory::where('budget', $request->get($item_budget))
            ->where('category_number',$request->get($no_pr))
            ->where('no_item',$request->get($nama_item))
            ->update([
                'budget_month_po' => $bulan,
                'po_number' => $nopo,
                'amount_po' => $total_dollar,
                'status' => 'PO'
            ]);
        }

        if($request->get('remark') == "Kantin"){
            $detail_po = CanteenPurchaseOrder::select('canteen_purchase_orders.*','acc_suppliers.*','canteen_purchase_order_details.*','canteen_purchase_requisitions.department')
            ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
            ->leftJoin('acc_suppliers', 'canteen_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
            ->leftJoin('canteen_purchase_requisitions', 'canteen_purchase_order_details.no_pr', '=', 'canteen_purchase_requisitions.no_pr')
            ->where('canteen_purchase_orders.id', '=', $data->id)
            ->get();
        }

        $pr = CanteenPurchaseOrder::select('no_pr')
        ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
        ->where('canteen_purchase_orders.id', '=', $data->id)
        ->distinct()
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('accounting_purchasing.report.report_po_kantin', array(
            'po' => $detail_po,
            'pr' => $pr
        ));

        $pdf->save(public_path() . "/kantin/po_list/".$detail_po[0]->no_po.".pdf");

        if($request->get('remark') == "Kantin"){
            return redirect('/purchase_order_canteen')
            ->with('status', 'Purchase Order Kantin Berhasil Dibuat')
            ->with('page', 'Purchase Order Canteen');
        }
    }
    catch(QueryException $e)
    {
        return redirect('/purchase_order_canteen')->with('error', $e->getMessage())
        ->with('page', 'Purchase Order Canteen');
    }
}

public function edit_purchase_order_canteen(Request $request)
{

    $purchase_order = CanteenPurchaseOrder::find($request->get('id'));
    $purchase_order_detail = CanteenPurchaseOrder::join('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')->where('canteen_purchase_orders.id', '=', $request->get('id'))
    ->get();

    $response = array(
        'status' => true,
        'purchase_order' => $purchase_order,
        'purchase_order_detail' => $purchase_order_detail
    );
    return Response::json($response);
}

public function update_purchase_order_canteen(Request $request)
{
    $id = Auth::id();

    $lop2 = $request->get('lop2');
    $po_data = CanteenPurchaseOrder::find($request->get('id_edit'));
    $lop = explode(',', $request->get('looping'));

    try
    {
            //Update PO
        $data3 = CanteenPurchaseOrder::where('no_po', $request->get('no_po_edit'))
        ->update([
            'supplier_code' => $request->get('supplier_code_edit') , 
            'supplier_name' => $request->get('supplier_name_edit') , 
            'supplier_due_payment' => $request->get('supplier_due_payment_edit') , 
            'supplier_status' => $request->get('supplier_status_edit') , 
            'material' => $request->get('material_edit') , 
            'vat' => $request->get('price_vat_edit') , 
            'transportation' => $request->get('transportation_edit') , 
            'delivery_term' => $request->get('delivery_term_edit') , 
            'holding_tax' => $request->get('holding_tax_edit') , 
            'currency' => $request->get('currency_edit') , 
            'note' => $request->get('note_edit') 
        ]);

        if ($po_data->posisi == "pch") {
            $data5 = CanteenPurchaseOrder::where('no_po', $request->get('no_po_edit'))
            ->update([
                'revised' => 'true', 
                'revised_date' => date('Y-m-d'),
                'posisi' => 'staff_pch',
                'status' => 'pch',
                'approval_authorized2' => null,
                'date_approval_authorized2' => null
            ]);
        }



        foreach ($lop as $lp)
        {
            $no_pr = "no_pr" . $lp;
            $no_item = "no_item" . $lp;
            $nama_item = "nama_item" . $lp;
            $item_budget = "item_budget" . $lp;
            $delivery_date = "delivery_date" . $lp;
            $qty = "qty" . $lp;
            $uom = "uom_edit" . $lp;
            $goods_price = "goods_price" . $lp;
            $last_price = "last_price" . $lp;
            $service_price = "service_price" . $lp;
            $konversi_dollar = "konversi_dollar" . $lp;
            $gl_number = "gl_edit" . $lp;

            $data2 = CanteenPurchaseOrderDetail::where('id', $lp)->update([
                'no_item' => $request->get($no_item) , 
                'nama_item' => $request->get($nama_item) , 
                'budget_item' => $request->get($item_budget) , 
                'delivery_date' => $request->get($delivery_date) , 
                'qty' => $request->get($qty) , 
                'uom' => $request->get($uom) , 
                'goods_price' => $request->get($goods_price) , 
                'last_price' => $request->get($last_price) , 
                'service_price' => $request->get($service_price) , 
                'konversi_dollar' => $request->get($konversi_dollar) , 
                'gl_number' => $request->get($gl_number) , 
                'created_by' => $id
            ]);

                 //Update Harga + Currency Di Master Item
            if ($request->get($goods_price) != 0) {
                $updateitempo = CanteenItem::where('kode_item', $request->get($no_item))
                ->update([
                    'harga' => $request->get($goods_price),
                    'currency' => $request->get('currency_edit')
                ]);
            }
            else if ($request->get($service_price) != 0) {
                $updateitempo = CanteenItem::where('kode_item', $request->get($no_item))
                ->update([
                    'harga' => $request->get($service_price), 
                    'currency' => $request->get('currency_edit')
                ]);
            }

            if ($data2) {

                $getbudgetlog = CanteenBudgetHistory::where('budget', $request->get($item_budget))
                ->where('po_number',$request->get('no_po_edit'))
                ->where('no_item',$request->get($nama_item))
                ->first();

                $counter = $getbudgetlog->amount_po;
                $date = $getbudgetlog->budget_month_po;

                $sisa_bulan = $date.'_sisa_budget';

                    //get Data Budget Based On Periode Dan Nomor
                $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget))->first();

                $totalOld = $budgetdata->$sisa_bulan + $counter;

                $updatebudget = AccBudget::where('budget_no',$request->get($item_budget))
                ->update([
                    $sisa_bulan => $totalOld
                ]);

                    //get Data Budget Based On Periode Dan Nomor
                $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget))->first();

                $total_dollar = $request->get($konversi_dollar);

                $totalNew = $budgetdata->$sisa_bulan - $total_dollar;

                $dataupdate = AccBudget::where('budget_no',$request->get($item_budget))
                ->update([
                    $sisa_bulan => $totalNew
                ]);

                $data5 = CanteenBudgetHistory::where('budget', $request->get($item_budget))
                ->where('po_number',$request->get('no_po_edit'))
                ->where('no_item',$request->get($nama_item))
                ->update([
                    'amount_po' => $request->get($konversi_dollar),
                    'created_by' => $id
                ]);
            }

        }

        for ($i = 2;$i <= $lop2;$i++)
        {
            $no_pr2 = "no_pr" . $i;
            $no_item2 = "no_item" . $i;
            $nama_item2 = "nama_item" . $i;
            $item_budget2 = "item_budget" . $i;
            $delivery_date2 = "delivery_date" . $i;
            $qty2 = "qty" . $i;
            $uom2 = "uom" . $i;
            $goods_price2 = "goods_price" . $i;
            $last_price2 = "last_price" . $i;
            $service_price2 = "service_price" . $i;
            $konversi_dollar2 = "konversi_dollar" . $i;
            $gl_number2 = "gl_number" . $i;

            $data = new CanteenPurchaseOrderDetail([
                'no_po' => $request->get('no_po_edit') , 
                'no_pr' => $request->get($no_pr2) , 
                'no_item' => $request->get($no_item2) , 
                'nama_item' => $request->get($nama_item2) , 
                'budget_item' => $request->get($item_budget2) , 
                'delivery_date' => $request->get($delivery_date2) , 
                'qty' => $request->get($qty2) , 
                'qty_receive' => 0 , 
                'uom' => $request->get($uom2) , 
                'goods_price' => $request->get($goods_price2) , 
                'last_price' => $request->get($last_price2) , 
                'service_price' => $request->get($service_price2) , 
                'konversi_dollar' => $request->get($konversi_dollar2) , 
                'gl_number' => $request->get($gl_number2) , 
                'created_by' => $id
            ]);

            $data->save();


            $data3 = CanteenPurchaseRequisitionItem::where('item_code', $request->get($no_item2))
            ->where('no_pr', $request->get($no_pr2))
            ->update([
                'sudah_po' => 'true'
            ]);


             //Get Total Amount From Budget Log

            $data5 = CanteenBudgetHistory::where('budget', $request->get($item_budget2))
            ->where('category_number',$request->get($no_pr2))
            ->where('no_item',$request->get($nama_item2))
            ->first();

            $amount = $data5->amount;

            $getbulan = AccBudget::select('budget_no', 'periode')
            ->where('budget_no', $request->get($item_budget2))
            ->first();

            if ($getbulan->periode == "FY198") {
                $bulan = strtolower(date('M'));
            }
            else{
                $bulan = "apr";
            }

            $sisa_bulan = $bulan.'_sisa_budget';

                //get Data Budget Based On Periode Dan Nomor
            $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget2))->first();

                //Tambahkan Budget Dengan Yang Ada Di Log
            $totalPlusPR = $budgetdata->$sisa_bulan + $amount;

            $updatebudget = AccBudget::where('budget_no',$request->get($item_budget2))
            ->update([
                $sisa_bulan => $totalPlusPR
            ]);

                //get Data Budget Based On Periode Dan Nomor
            $budgetdata = AccBudget::where('budget_no','=',$request->get($item_budget2))->first();

                //Get Amount Di PO
            $total_dollar = $request->get($konversi_dollar2);

            $totalminusPO = $budgetdata->$sisa_bulan - $total_dollar;

                // Setelah itu update data budgetnya dengan yang actual
            $dataupdate = AccBudget::where('budget_no',$request->get($item_budget2))
            ->update([
                $sisa_bulan => $totalminusPO
            ]);

            $updatebudgetlog = CanteenBudgetHistory::where('budget', $request->get($item_budget2))
            ->where('category_number',$request->get($no_pr2))
            ->where('no_item',$request->get($nama_item2))
            ->update([
                'budget_month_po' => $bulan,
                'po_number' => $request->get('no_po_edit'),
                'amount_po' => $total_dollar,
                'status' => 'PO'
            ]);

        }

        $detail_po = CanteenPurchaseOrder::select('canteen_purchase_orders.*','acc_suppliers.*','canteen_purchase_order_details.*')
        ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
        ->leftJoin('acc_suppliers', 'canteen_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
        ->leftJoin('canteen_purchase_requisitions', 'canteen_purchase_order_details.no_pr', '=', 'canteen_purchase_requisitions.no_pr')
        ->where('canteen_purchase_orders.no_po', '=', $request->get('no_po_edit'))
        ->get();

        $pr = CanteenPurchaseOrder::select('no_pr')
        ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
        ->where('canteen_purchase_orders.no_po', '=', $request->get('no_po_edit'))
        ->distinct()
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('accounting_purchasing.report.report_po_kantin', array(
            'po' => $detail_po,
            'pr' => $pr
        ));

        $pdf->save(public_path() . "/kantin/po_list/".$detail_po[0]->no_po.".pdf");

        if ($po_data->remark == "Kantin"){
            return redirect('/purchase_order_canteen')
            ->with('status', 'Purchase Order Canteen Berhasil Dirubah')
            ->with('page', 'Purchase Order Canteen');
        }
    }
    catch(QueryException $e)
    {
        return redirect('/purchase_order_canteen')->with('error', $e->getMessage())
        ->with('page', 'Purchase Order Canteen');
    }
}

public function report_purchase_order_canteen($id){

    $po = CanteenPurchaseOrder::find($id);

    if($po->remark == "Kantin"){
        $detail_po = CanteenPurchaseOrder::select('canteen_purchase_orders.*','acc_suppliers.*','canteen_purchase_order_details.*','canteen_purchase_requisitions.department')
        ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
        ->leftJoin('acc_suppliers', 'canteen_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
        ->leftJoin('canteen_purchase_requisitions', 'canteen_purchase_order_details.no_pr', '=', 'canteen_purchase_requisitions.no_pr')
        ->where('canteen_purchase_orders.id', '=', $id)
        ->get();
    }

    $pr = CanteenPurchaseOrder::select('no_pr')
    ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
    ->where('canteen_purchase_orders.id', '=', $id)
    ->distinct()
    ->get();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('accounting_purchasing.report.report_po_kantin', array(
        'po' => $detail_po,
        'pr' => $pr
    ));

    $path = "kantin/po_list/" . $detail_po[0]->no_po . ".pdf";
    return $pdf->stream("PO ".$detail_po[0]->no_po. ".pdf");
}

public function delete_item_po_canteen(Request $request)
{
    try
    {
        $item = CanteenPurchaseOrderDetail::find($request->get('id'));

        $data3 = CanteenPurchaseRequisitionItem::where('item_code', $item->no_item)
        ->where('no_pr', $item->no_pr)
        ->update(['sudah_po' => null ]);        

        $budget_log = CanteenBudgetHistory::where('no_item', '=', $item->nama_item)
        ->where('po_number', '=', $item->no_po)
        ->first();


        $sisa_bulan = $budget_log->budget_month_po.'_sisa_budget';        
        $budget = AccBudget::where('budget_no', $budget_log->budget)->first();

            $total = $budget->$sisa_bulan + $budget_log->amount_po; //add total

            $dataupdate = AccBudget::where('budget_no', $budget_log->budget)
            ->update([
                $sisa_bulan => $total
            ]);

            $update_budget_log = CanteenBudgetHistory::where('no_item', '=', $item->nama_item)
            ->where('po_number', '=', $item->no_po)
            ->update([
                'budget_month_po' => null,
                'po_number' => null,
                'amount_po' => null,
                'status' => 'PR'
            ]);

            $budget = AccBudget::where('budget_no', $budget_log->budget)->first();

            $totalAfterMinusPR = $budget->$sisa_bulan - $budget_log->amount;

            $dataupdate = AccBudget::where('budget_no', $budget_log->budget)
            ->update([
                $sisa_bulan => $totalAfterMinusPR
            ]);

            $master = CanteenPurchaseOrderDetail::where('id', '=', $request->get('id'))->delete();

        }
        catch(QueryException $e)
        {
            return redirect('/purchase_order_canteen')->with('error', $e->getMessage())
            ->with('page', 'Purchase Order Canteen');
        }
    }


    public function po_send_email_canteen(Request $request){
        $po = CanteenPurchaseOrder::find($request->get('id'));

        try{
            if ($po->posisi == "staff_pch")
            {
                $po->posisi = "manager_pch";

                $mailto = "select distinct email from canteen_purchase_orders join users on canteen_purchase_orders.authorized2 = users.username where canteen_purchase_orders.id = '" . $request->get('id') . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }
                $po->save();


                $isimail = "
                SELECT canteen_purchase_orders.*,
                canteen_purchase_order_details.nama_item,
                canteen_purchase_order_details.budget_item,
                canteen_purchase_order_details.delivery_date,
                canteen_purchase_order_details.goods_price,
                canteen_purchase_order_details.service_price,
                canteen_purchase_order_details.qty,
                canteen_purchase_order_details.uom
                FROM
                canteen_purchase_orders
                JOIN canteen_purchase_order_details ON canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po 
                WHERE
                canteen_purchase_orders.id = ".$request->get('id')."";

                $po_isi = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($po_isi, 'canteen_purchase_order'));

                $response = array(
                  'status' => true,
                  'datas' => "Berhasil"
              );

                return Response::json($response);
            }
            else{

            }


        } catch (Exception $e) {
         $response = array(
          'status' => false,
          'datas' => "Gagal"
      );

         return Response::json($response);
     }
 }

 public function cancel_purchase_order_canteen(Request $request)
 {
    try
    {
        $po = CanteenPurchaseOrder::find($request->get('id'));
        $date = date('Y-m-d');

        $budget_log = CanteenBudgetHistory::where('po_number', '=', $po->no_po)
        ->get();

        if ($budget_log != null) {

            foreach ($budget_log as $log) {
                $sisa_bulan = $log->budget_month_po.'_sisa_budget';
                $budget = AccBudget::where('budget_no', $log->budget)->first();

                    $total = $budget->$sisa_bulan + $log->amount_po; //add total PO
                    $dataupdate = AccBudget::where('budget_no', $log->budget)->update([
                        $sisa_bulan => $total
                    ]);

                    //get Data Budget Based On Periode Dan Nomor
                    $budgetdata = AccBudget::where('budget_no', $log->budget)->first();

                    $totalNew = $budgetdata->$sisa_bulan - $log->amount; //minus amount PR

                    $dataupdate = AccBudget::where('budget_no', $log->budget)
                    ->update([
                        $sisa_bulan => $totalNew
                    ]);
                }

                $data5 = CanteenBudgetHistory::where('po_number', $po->no_po)
                ->update([
                    'budget_month_po' => null,
                    'po_number' => null,
                    'amount_po' => null,
                    'status' => 'PR',
                    'created_by' => Auth::id()
                ]);
            }

            if($po->remark == "Kantin"){
                $data3 = CanteenPurchaseOrderDetail::where('no_po', $po->no_po)
                ->select('*')
                ->get();
                
                foreach ($data3 as $datapr) {
                    $updatepr = CanteenPurchaseRequisitionItem::where('item_code', $datapr->no_item)
                    ->where('no_pr', $datapr->no_pr)
                    ->update(['sudah_po' => null ]);
                }
            }

            $delete_po_item = CanteenPurchaseOrderDetail::where('no_po', '=', $po->no_po)->delete();
            $delete_po = CanteenPurchaseOrder::where('no_po', '=', $po->no_po)->delete();

            $response = array(
                'status' => true,
            );

            return Response::json($response);

        }
        catch(QueryException $e)
        {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );

            return Response::json($response);
        }

    }

    public function poapprovalmanagercanteen($id){
        $po = CanteenPurchaseOrder::find($id);
        try{
            if ($po->posisi == "manager_pch")
            {
                $po->posisi = 'pch';
                $po->approval_authorized2 = "Approved";
                $po->date_approval_authorized2 = date('Y-m-d H:i:s');
                $po->status = "sap";

                $mailto = "select distinct email from canteen_purchase_orders join users on canteen_purchase_orders.buyer_id = users.username where canteen_purchase_orders.id = '" . $id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }

                $po->save();

                if($po->remark == "Kantin"){
                    $detail_po = CanteenPurchaseOrder::select('canteen_purchase_orders.*','acc_suppliers.*','canteen_purchase_order_details.*','canteen_purchase_requisitions.department')
                    ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
                    ->leftJoin('acc_suppliers', 'canteen_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
                    ->leftJoin('canteen_purchase_requisitions', 'canteen_purchase_order_details.no_pr', '=', 'canteen_purchase_requisitions.no_pr')
                    ->where('canteen_purchase_orders.id', '=', $id)
                    ->get();
                }

                $pr = CanteenPurchaseOrder::select('no_pr')
                ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
                ->where('canteen_purchase_orders.id', '=', $id)
                ->distinct()
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');


                $pdf->loadView('accounting_purchasing.report.report_po_kantin', array(
                    'po' => $detail_po,
                    'pr' => $pr
                ));

                $pdf->save(public_path() . "/kantin/po_list/".$detail_po[0]->no_po.".pdf");

                $isimail = "
                SELECT canteen_purchase_orders.*,
                canteen_purchase_order_details.nama_item,
                canteen_purchase_order_details.budget_item,
                canteen_purchase_order_details.delivery_date,
                canteen_purchase_order_details.goods_price,
                canteen_purchase_order_details.service_price,
                canteen_purchase_order_details.qty,
                canteen_purchase_order_details.uom
                FROM
                canteen_purchase_orders
                JOIN canteen_purchase_order_details ON canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po 
                WHERE
                canteen_purchase_orders.id = ".$id."";

                $po_isi = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($po_isi, 'canteen_purchase_order'));

                $message = 'PO Kantin dengan Nomor '.$po->no_po;
                $message2 ='Berhasil di approve';
            }
            else{
                $message = 'PO Kantin dengan Nomor. '.$po->no_po;
                $message2 ='Sudah di approve/reject';
            }

            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $po->no_pr,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Approval');

        } catch (Exception $e) {
            return view('accounting_purchasing.verifikasi.pr_message', array(
                'head' => $po->no_pr,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Approval');
        }
    }


   //Monitoring PR PO

    public function monitoringPOCanteen(){
        return view('accounting_purchasing.display.po_canteen_monitoring',  
            array(
              'title' => 'Purchase Order Canteen Monitoring', 
              'title_jp' => 'PO管理',
          )
        )->with('page', 'Purchase Order Canteen Monitoring');
    }

    public function fetchMonitoringPOCanteen(Request $request){

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = CanteenPurchaseOrder::where('status','=','pch')
      ->orderBy('tanggal', 'asc')
      ->select(db::raw('date(tgl_po) as tanggal'))
      ->first();

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
    }
    else{
        if($last){
          $tanggal = date_create($last->tanggal);
          $now = date_create(date('Y-m-d'));
          $interval = $now->diff($tanggal);
          $diff = $interval->format('%a%');

          if($diff > 30){
            $datefrom = date('Y-m-d', strtotime($last->tanggal));
        }
    }
}


if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
}

              //per tgl
$data = db::select("
    select date.week_date, coalesce(belum_diterima.total, 0) as jumlah_belum, coalesce(sudah_diterima.total, 0) as jumlah_sudah from 
    (select week_date from weekly_calendars 
    where date(week_date) >= '".$datefrom."'
    and date(week_date) <= '".$dateto."') date
    left join
    (select date(tgl_po) as date, count(id) as total from canteen_purchase_orders
    where date(tgl_po) >= '".$datefrom."' and date(tgl_po) <= '".$dateto."' and canteen_purchase_orders.deleted_at is null and posisi = 'pch' and `status` = 'sap'
    group by date(tgl_po)) sudah_diterima
    on date.week_date = sudah_diterima.date
    left join
    (select date(tgl_po) as date, count(id) as total from canteen_purchase_orders
    where date(tgl_po) >= '".$datefrom."' and date(tgl_po) <= '".$dateto."' and canteen_purchase_orders.deleted_at is null and `status` != 'sap'
    group by date(tgl_po)) belum_diterima
    on date.week_date = belum_diterima.date
    WHERE belum_diterima.total != 0 or sudah_diterima.total != 0
    order by week_date asc
    ");

$year = date('Y');

$response = array(
    'status' => true,
    'datas' => $data,
    'year' => $year
);

return Response::json($response);
}

public function detailMonitoringPOCanteen(Request $request){

  $tanggal = $request->get("tanggal");
  $status = $request->get("status");
  $tglfrom = $request->get("tglfrom");
  $tglto = $request->get("tglto");

  $status_sign = "";

  if ($status == "PO Incompleted") {
      $status_sign = "and status != 'sap'";
  }
  else if ($status == "PO Completed") {
      $status_sign = "and posisi = 'pch' and status = 'sap'";
  }


  $qry = "SELECT * FROM canteen_purchase_orders WHERE deleted_at IS NULL and DATE_FORMAT(tgl_po,'%Y-%m-%d') = '".$tanggal."' ".$status_sign." order by id DESC";
  $po = DB::select($qry);

  return DataTables::of($po)

  ->editColumn('tgl_po', function ($po)
  {
    return date('Y-m-d', strtotime($po->tgl_po));
})


  ->editColumn('status', function ($po)
  {
    $id = $po->id;

    if ($po->posisi == "staff_pch")
    {
        return '<label class="label label-danger">Staff Purchasing</label>';
    }

    else if ($po->posisi == "manager_pch")
    {
        return '<label class="label label-warning">Diverifikasi Manager</label>';
    }

    else if ($po->posisi == "pch")
    {
        return '<label class="label label-success">Sudah Diverifikasi</label>';
    }

})
  ->rawColumns(['status' => 'status'])
  ->make(true);
}

public function fetchtablePOCanteen(Request $request)
{
  $datefrom = $request->get('datefrom');
  $dateto = $request->get('dateto');

  if ($datefrom == "") {
      $datefrom = date('Y-m-d', strtotime(carbon::now()->subMonth(11)));
  }

  if ($dateto == "") {
      $dateto = date('Y-m-d', strtotime(carbon::now()));
  }

  $data = db::select("

    SELECT
    t1.*,IF(t1.goods_price != 0,sum( t1.goods_price * t1.qty ),sum( t1.service_price * t1.qty )) AS amount 
    FROM
    (
    SELECT
    canteen_purchase_orders.*,
    date(canteen_purchase_orders.tgl_po) as po_date,
    canteen_purchase_order_details.budget_item,
    canteen_purchase_order_details.goods_price,
    canteen_purchase_order_details.service_price,
    canteen_purchase_order_details.qty 
    FROM
    canteen_purchase_orders
    JOIN canteen_purchase_order_details ON canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po 
    WHERE
    canteen_purchase_orders.`status` != 'sap' 
    AND canteen_purchase_orders.deleted_at IS NULL 
    AND DATE_FORMAT( tgl_po, '%Y-%m-%d' ) BETWEEN  '".$datefrom."' AND '".$dateto."' 
    ORDER BY
    tgl_po ASC 
    ) t1
    GROUP BY t1.no_po");

  $response = array(
    'status' => true,
    'datas' => $data
);

  return Response::json($response); 
}

public function verifikasi_purchase_order_canteen($id)
{
    $po = CanteenPurchaseOrder::find($id);

    $path = '/kantin/po_list/' . $po->file_pdf;            
    $file_path = asset($path);

    return view('accounting_purchasing.verifikasi.po_verifikasi_canteen', array(
        'po' => $po,
        'file_path' => $file_path,
    ))->with('page', 'Purchase Order Canteen');
}


public function approval_purchase_order_canteen(Request $request, $id)
{
    $approve = $request->get('approve');
    if ($approve == "1") {
        $po = CanteenPurchaseOrder::find($id);

        if ($po->posisi == "manager_pch")
        {

            $po->posisi = 'pch';
            $po->approval_authorized2 = "Approved";
            $po->date_approval_authorized2 = date('Y-m-d H:i:s');
            $po->status = "not_sap";

            $mailto = "select distinct email from canteen_purchase_orders join users on canteen_purchase_orders.buyer_id = users.username where canteen_purchase_orders.id = '" . $id . "'";
            $mails = DB::select($mailto);

            foreach ($mails as $mail)
            {
                $mailtoo = $mail->email;
            }
        }

        $po->save();

        if($po->remark == "Kantin"){
            $detail_po = CanteenPurchaseOrder::select('canteen_purchase_orders.*','acc_suppliers.*','canteen_purchase_order_details.*','canteen_purchase_requisitions.department')
            ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
            ->leftJoin('acc_suppliers', 'canteen_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
            ->leftJoin('canteen_purchase_requisitions', 'canteen_purchase_order_details.no_pr', '=', 'canteen_purchase_requisitions.no_pr')
            ->where('canteen_purchase_orders.id', '=', $id)
            ->get();
        }

        $pr = CanteenPurchaseOrder::select('no_pr')
        ->leftJoin('canteen_purchase_order_details', 'canteen_purchase_orders.no_po', '=', 'canteen_purchase_order_details.no_po')
        ->where('canteen_purchase_orders.id', '=', $id)
        ->distinct()
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');


        $pdf->loadView('accounting_purchasing.report.report_po_kantin', array(
            'po' => $detail_po,
            'pr' => $pr
        ));

        $pdf->save(public_path() . "/kantin/po_list/".$detail_po[0]->no_po.".pdf");

        $isimail = "
        SELECT canteen_purchase_orders.*,
        canteen_purchase_order_details.nama_item,
        canteen_purchase_order_details.budget_item,
        canteen_purchase_order_details.delivery_date,
        canteen_purchase_order_details.goods_price,
        canteen_purchase_order_details.service_price,
        canteen_purchase_order_details.qty,
        canteen_purchase_order_details.uom
        FROM
        canteen_purchase_orders
        JOIN canteen_purchase_order_details ON canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po 
        WHERE
        canteen_purchase_orders.id = ".$id."";

        $po_isi = db::select($isimail);

        Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($po_isi, 'canteen_purchase_order'));

        return redirect('/canteen/purchase_order/verifikasi/' . $id)->with('status', 'Purchase Order Approved')
        ->with('page', 'Purchase Order Canteen');
    }
    else
    {
        return redirect('/canteen/purchase_order/verifikasi/' . $id)->with('error', 'Purchase Order Not Approved')
        ->with('page', 'Purchase Order Canteen');
    }
}

public function reject_purchase_order_canteen(Request $request, $id)
{
    $alasan = $request->get('alasan');

    $po = CanteenPurchaseOrder::find($id);

    if ($po->posisi == "manager_pch")
    {
        $po->reject = $alasan;
        $po->datereject = date('Y-m-d H:i:s');
        $po->posisi = "staff_pch";
        $po->approval_authorized2 = null;
        $po->date_approval_authorized2 = null;
    }

    $po->save();

    $isimail = "
    SELECT canteen_purchase_orders.*,
    canteen_purchase_order_details.nama_item,
    canteen_purchase_order_details.budget_item,
    canteen_purchase_order_details.delivery_date,
    canteen_purchase_order_details.goods_price,
    canteen_purchase_order_details.service_price,
    canteen_purchase_order_details.qty,
    canteen_purchase_order_details.uom
    FROM
    canteen_purchase_orders
    JOIN canteen_purchase_order_details ON canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po 
    WHERE
    canteen_purchase_orders.id = ".$po->id."";

    $tolak = db::select($isimail);

    $mails = "select distinct email from canteen_purchase_orders join users on canteen_purchase_orders.buyer_id = users.username where canteen_purchase_orders.id ='" . $po->id . "'";
    $mailtoo = DB::select($mails);

    Mail::to($mailtoo)->send(new SendEmail($tolak, 'canteen_purchase_order'));
    return redirect('/canteen/purchase_order/verifikasi/' . $id)->with('status', 'PO Not Approved')
    ->with('page', 'Purchase Order Canteen');
}

public function fetch_history_pembelian_canteen(Request $request){
    $history = CanteenPurchaseOrder::whereNull('canteen_purchase_orders.deleted_at');

    if($request->get('keyword') != null){
        $history = $history->where('nama_item', 'like', '%' . $request->get('keyword') . '%');
    }
    $history = $history->join('canteen_purchase_order_details','canteen_purchase_orders.no_po','=','canteen_purchase_order_details.no_po');
    $history = $history->select('canteen_purchase_orders.supplier_name','canteen_purchase_order_details.no_po','canteen_purchase_order_details.nama_item','canteen_purchase_order_details.goods_price','canteen_purchase_order_details.service_price','canteen_purchase_orders.tgl_po','canteen_purchase_orders.currency');
    $history = $history->orderBy('canteen_purchase_orders.tgl_po', 'desc');
    $history = $history->get();

    $response = array(
        'status' => true,
        'history' => $history
    );
    return Response::json($response);
}

public function porejectKatin(Request $request, $id)
{
    $po = CanteenPurchaseOrder::find($id);

    if ($po->posisi == "manager_pch")
    {
        $po->datereject = date('Y-m-d H:i:s');
        $po->posisi = "staff_pch";
        $po->approval_authorized2 = null;
        $po->date_approval_authorized2 = null;
    }
    $po->save();

    $isimail = "
    SELECT canteen_purchase_orders.*,
    canteen_purchase_order_details.nama_item,
    canteen_purchase_order_details.budget_item,
    canteen_purchase_order_details.delivery_date,
    canteen_purchase_order_details.goods_price,
    canteen_purchase_order_details.service_price,
    canteen_purchase_order_details.qty,
    canteen_purchase_order_details.uom
    FROM
    canteen_purchase_orders
    JOIN canteen_purchase_order_details ON canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po 
    WHERE
    canteen_purchase_orders.id = ".$po->id."";

    $tolak = db::select($isimail);

        //kirim email ke Buyer
    $mails = "select distinct email from canteen_purchase_orders join users on canteen_purchase_orders.buyer_id = users.username where canteen_purchase_orders.id ='" . $po->id . "'";
    $mailtoo = DB::select($mails);


    Mail::to($mailtoo)->send(new SendEmail($tolak, 'canteen_purchase_order'));

    $message = 'PO dengan Nomor. '.$po->no_po;
    $message2 ='Tidak Disetujui';

    return view('accounting_purchasing.verifikasi.pr_message', array(
        'head' => $po->no_po,
        'message' => $message,
        'message2' => $message2,
    ))->with('page', 'Approval');
}

public function exportPOKantin(Request $request){

    $time = date('d-m-Y H;i;s');

    $tanggal = "";

    if (strlen($request->get('datefrom')) > 0)
    {
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        $tanggal = "and tgl_po >= '" . $datefrom . " 00:00:00' ";
        if (strlen($request->get('dateto')) > 0)
        {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
            $tanggal = $tanggal . "and tgl_po  <= '" . $dateto . " 23:59:59' ";
        }
    }

    $po_detail = db::select(
        "
        Select canteen_purchase_order_details.id,canteen_purchase_orders.no_po, canteen_purchase_orders.remark, canteen_purchase_orders.note, canteen_purchase_order_details.no_pr, canteen_purchase_orders.tgl_po, canteen_purchase_orders.supplier_code , canteen_purchase_orders.supplier_name, canteen_purchase_orders.currency, canteen_purchase_orders.material, canteen_purchase_orders.buyer_name, canteen_purchase_order_details.no_item, canteen_purchase_order_details.nama_item, canteen_purchase_order_details.delivery_date, canteen_purchase_order_details.qty, canteen_purchase_order_details.uom, canteen_purchase_order_details.goods_price, canteen_purchase_order_details.service_price, canteen_purchase_order_details.budget_item, canteen_purchase_orders.cost_center, canteen_purchase_order_details.gl_number, acc_purchase_requisitions.emp_name, acc_investments.applicant_name from canteen_purchase_orders left join canteen_purchase_order_details on canteen_purchase_orders.no_po = canteen_purchase_order_details.no_po left join acc_purchase_requisitions on canteen_purchase_order_details.no_pr = acc_purchase_requisitions.no_pr left join acc_investments on canteen_purchase_order_details.no_pr = acc_investments.reff_number WHERE canteen_purchase_orders.deleted_at IS NULL " . $tanggal . " order by canteen_purchase_orders.no_po,id ASC
        ");

    $data = array(
        'po_detail' => $po_detail
    );

    ob_clean();

    Excel::create('PO Kantin List '.$time, function($excel) use ($data){
        $excel->sheet('Location', function($sheet) use ($data) {
          return $sheet->loadView('accounting_purchasing.purchase_order_excel_canteen', $data);
      });
    })->export('xlsx');
}

// Cash Payment

public function IndexCashPayment(){

    $title = "Cash Payment";
    $title_jp = "";

    return view('accounting_purchasing.cash_payment.index', array(
        'title' => $title,
        'title_jp' => $title_jp
    ));
}

public function fetchCashPayment(){
    $cash = db::select("SELECT
            *
        FROM
        acc_cash_payments
        order by id desc
        ");

    $response = array(
        'status' => true,
        'cash' => $cash
    );
    return Response::json($response);
}

public function fetchCashPaymentDetail(Request $request){

    $cash = AccCashPayment::find($request->get('id'));

    $response = array(
        'status' => true,
        'cash' => $cash,
    );
    return Response::json($response);
}

public function createCashPayment(Request $request){
    try{
        $manager = null;
        $manager_name = null;
        $gm = null;
        $direktur = "PI9709001";
        $presdir = null;

        $tujuan_upload = 'files/cash_payment';

        $file = $request->file('file_attach');
        if ($file != null || $file != "") {
            $nama = $file->getClientOriginalName();
            $filename = pathinfo($nama, PATHINFO_FILENAME);
            $extension = pathinfo($nama, PATHINFO_EXTENSION);
            $filename = md5($filename.date('YmdHisa')).'.'.$extension;
            $file->move($tujuan_upload,$filename);
        }else{
            $filename = null;
        }

        $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Accounting Department' and position = 'manager'");

        if ($manag != null)
        {
            foreach ($manag as $mg)
            {
                $manager = $mg->employee_id;
                $manager_name = $mg->name;
            }

        }
        else{
            $manager = null;
            $manager_name = null;
            $direktur = null;
        }

        $cash = new AccCashPayment([
            'submission_date' => $request->input('submission_date'),
            'category' => $request->input('category'),
            'currency' => $request->input('currency'),
            'amount' => $request->input('amount'),
            'remark' => $request->input('remark'),
            'file' => $filename,
            'pdf' => 'Cash Payment '.$request->input('remark'). ' '.date('d-M-y', strtotime($request->input('submission_date'))).'.pdf',
            'posisi' => 'user', 
            'status' => 'approval', 
            'manager' => $manager,
            'manager_name' => $manager_name,
            'direktur' => $direktur,
            'presdir' => $presdir,
            'created_by' => Auth::user()->username,
            'created_name' => Auth::user()->name
        ]);

        $cash->save();

        $cash_data = AccCashPayment::where('id','=',$cash->id)->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('accounting_purchasing.cash_payment.report_cash_payment', array(
            'cash' => $cash_data,
            'id' => $cash->id
        ));

        $pdf->save(public_path() . "/cash_list/Cash Payment ".$request->input('remark'). " ".date('d-M-y', strtotime($request->input('submission_date'))).".pdf");


        $response = array(
            'status' => true,
            'message' => 'New Cash Payment Successfully Added'
        );
        return Response::json($response);
    }
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function editCashPayment(Request $request){
    try{
        $cash = AccCashPayment::where('id', '=', $request->get('id_edit'))->first();

        $file = $request->file('file_attach');
        $tujuan_upload = 'files/cash_payment';

        if ($file != null || $file != "") {
            $nama = $file->getClientOriginalName();
            $filename = pathinfo($nama, PATHINFO_FILENAME);
            $extension = pathinfo($nama, PATHINFO_EXTENSION);
            $filename = md5($filename.date('YmdHisa')).'.'.$extension;
            $file->move($tujuan_upload,$filename);
        }else{
            $filename = $cash->file;
        }

        $cash->submission_date = $request->input('submission_date');
        $cash->category = $request->input('category');
        $cash->currency = $request->input('currency');
        $cash->amount = $request->input('amount');
        $cash->remark = $request->input('remark');
        $cash->file = $filename;
        $cash->created_by = Auth::user()->username;
        $cash->created_name = Auth::user()->name;
        $cash->save();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('accounting_purchasing.cash_payment.report_cash_payment', array(
            'cash' => $cash,
            'id' => $cash->id
        ));

        $pdf->save(public_path() . "/cash_list/Cash Payment ".$request->input('remark'). " ".date('d-M-y', strtotime($request->input('submission_date'))).".pdf");

        $response = array(
            'status' => true,
            'message' => 'Cash Payment Updated'
        );
        return Response::json($response);
    }
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function reportCashPayment($id){
    $cash = AccCashPayment::find($id);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('accounting_purchasing.cash_payment.report_cash_payment', array(
        'cash' => $cash,
        'id' => $id
    ));
    return $pdf->stream("Cash Payment ".$cash->remark. " ".date('d-M-y', strtotime($cash->submission_date)).".pdf");
}

public function emailCashPayment(Request $request){
    $cash = AccCashPayment::find($request->get('id'));

    try{
        if ($cash->posisi == "user")
        {
            $mails = "select distinct email from acc_cash_payments join users on acc_cash_payments.manager = users.username where acc_cash_payments.id = ".$request->get('id');
            $mailtoo = DB::select($mails);

            $cash->posisi = "manager";
            $cash->save();

            $isimail = "select * from acc_cash_payments where acc_cash_payments.id = ".$request->get('id');
            $cash_payment = db::select($isimail);

            Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($cash_payment, 'cash_payment'));

            $response = array(
              'status' => true,
              'datas' => "Berhasil"
          );

            return Response::json($response);
        }
    } 
    catch (Exception $e) {
        $response = array(
          'status' => false,
          'datas' => "Gagal"
      );
        return Response::json($response);
    }
}


}