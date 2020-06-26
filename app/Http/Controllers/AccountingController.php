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
    public function __construct()
    {
        $this->dept = ['Management Information System', 'Accounting', 'Assembly (WI-A)', 'Educational Instrument (EI)', 'General Affairs', 'Human Resources', 'Logistic', 'Maintenance', 'Parts Process (WI-PP)', 'Procurement', 'Production Control', 'Production Engineering', 'Purchasing', 'Quality Assurance', 'Welding-Surface Treatment (WI-WST)'];

        $this->uom = ['bag', 'bar', 'batang', 'belt', 'botol', 'bottle', 'box', 'Btg', 'Btl', 'btng', 'buah', 'buku', 'Can', 'Case', 'container', 'cps', 'day', 'days', 'dos', 'doz', 'Drum', 'dus', 'dz', 'dzn', 'EA', 'G', 'galon', 'gr', 'hari', 'hour', 'job', 'JRG', 'kaleng', 'ken', 'Kg', 'kgm', 'klg', 'L', 'Lbr', 'lbs', 'lembar', 'License', 'lisence', 'lisensi', 'lmbr', 'lonjor', 'Lot', 'ls', 'ltr', 'lubang', 'lusin', 'm', 'm2', 'm²', 'm3', 'malam', 'meter', 'ml', 'month', 'Mtr', 'night', 'OH', 'Ons', 'orang', 'OT', 'Pac', 'Pack', 'package', 'pad', 'pail', 'pair', 'pairs', 'pak', 'Pasang', 'pc', 'Pca', 'Pce', 'Pck', 'pcs', 'Person', 'pick up', 'pil', 'ply', 'point', 'pot', 'prs', 'prsn', 'psc', 'PSG', 'psn', 'Rim', 'rol', 'roll', 'rolls', 'sak', 'sampel', 'sample', 'Set', 'Set', 'Sets', 'sheet', 'shoot', 'slop', 'sum', 'tank', 'tbg', 'time', 'titik', 'ton', 'tube', 'Um', 'Unit', 'user', 'VA', 'yard', 'zak'

    ];

    $this->transportation = ['AIR', 'BOAT', 'COURIER SERVICE', 'DHL', 'FEDEX', 'SUV-Car'];

    $this->delivery = ['CIF Surabaya', 'CIP', 'Cost And Freight ', 'Delivered At Frontier', 'Delivered Duty Paid', 'Delivered Duty Unpaid', 'Delivered Ex Quay', 'Ex Works', 'Ex Factory', 'Ex Ship', 'FRANCO', 'Franco', 'Flee Alongside Ship', 'Free Carrier (FCA)', 'Letter Of Credits',

];

        $this->dgm = 'PI0109004'; // Pak Budhi
        $this->gm = 'PI1206001'; // Pak Hayakawa
        
    }

    //==================================//
    //      Master supplier     //
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
            return ' 
            <a href="supplier/update/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a> 
            <a href="supplier/delete/' . $id . '" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
            ';
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

            $supplier = AccSupplier::create(['vendor_code' => $request->get('vendor_code') , 'supplier_name' => $request->get('supplier_name') , 'supplier_address' => $request->get('supplier_address') , 'supplier_city' => $request->get('supplier_city') , 'supplier_phone' => $request->get('supplier_phone') , 'supplier_fax' => $request->get('supplier_fax') , 'contact_name' => $request->get('contact_name') , 'supplier_npwp' => $request->get('supplier_npwp') , 'supplier_duration' => $request->get('supplier_duration') , 'position' => $request->get('position') , 'supplier_status' => $request->get('supplier_status') , 'created_by' => $id_user]);

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
            ->update(['vendor_code' => $request->get('vendor_code') , 'supplier_name' => $request->get('supplier_name') , 'supplier_address' => $request->get('supplier_address') , 'supplier_city' => $request->get('supplier_city') , 'supplier_phone' => $request->get('supplier_phone') , 'supplier_fax' => $request->get('supplier_fax') , 'contact_name' => $request->get('contact_name') , 'supplier_npwp' => $request->get('supplier_npwp') , 'supplier_duration' => $request->get('supplier_duration') , 'position' => $request->get('position') , 'supplier_status' => $request->get('supplier_status') , 'created_by' => $id_user]);

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
            return ' 
            <a href="purchase_item/update/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a> 
            <a href="purchase_item/delete/' . $id . '" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
            ';
        })
        ->rawColumns(['action' => 'action'])
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
            $id_user = Auth::id();

            $item = AccItem::create(['kode_item' => $request->get('item_code') , 'kategori' => $request->get('item_category') , 'deskripsi' => $request->get('item_desc') , 'uom' => $request->get('item_uom') , 'spesifikasi' => $request->get('item_spec') , 'harga' => $request->get('item_price') , 'lot' => $request->get('item_lot') , 'moq' => $request->get('item_moq') , 'leadtime' => $request->get('item_leadtime') , 'currency' => $request->get('item_currency') , 'created_by' => $id_user]);

            $item->save();

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

        $query = "SELECT kode_item FROM `acc_items` where kategori='$kategori' order by id DESC LIMIT 1";
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
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete(' . $id . ')"><i class="fa fa-trash"></i></button>
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

        $items = db::select("select kode_item, kategori, deskripsi from acc_items where deleted_at is null");
        $dept = $this->dept;

        return view('accounting_purchasing.purchase_requisition', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'items' => $items,
            'dept' => $dept,
            'uom' => $this->uom
        ))
        ->with('page', 'Purchase Requisition')
        ->with('head', 'PR');
    }

    public function fetch_purchase_requisition(Request $request)
    {
        $tanggal = "";
        $adddepartment = "";

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
            $adddepartment = "and A.Department in (" . $department . ") ";
        }

        $qry = "SELECT  * FROM acc_purchase_requisitions A WHERE A.deleted_at IS NULL " . $tanggal . "" . $adddepartment . " order by A.id DESC";

        $pr = DB::select($qry);

        return DataTables::of($pr)
        ->editColumn('submission_date', function ($pr)
        {
            return date('d F Y', strtotime($pr->submission_date));
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
                return '<label class="label label-success">Diverifikasi Accounting</a>';
            }

        })
        ->addColumn('action', function ($pr)
        {
            $id = $pr->id;
            return '
            <a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="editPR(' . $id . ')" data-toggle="tooltip" title="Edit PR"><i class="fa fa-edit"></i></a>
            <a href="purchase_requisition/detail/' . $id . '" class="btn btn-info btn-xs" data-toggle="tooltip" title="Detail PR"><i class="fa fa-eye"></i></a>
            <a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i></a>
            ';
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
                'currency' => $item->currency
            );

        }

        return json_encode($html);
    }

    public function fetchBudgetList(Request $request)
    {
        $budgets = AccBudget::select('acc_budgets.budget_no', 'acc_budgets.description')->where('department', '=', $request->get('department'))
        ->where('category', '=', 'Expenses')
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

        $date = date('Y-m-d', strtotime(date('Y-m-d') . ' + 21 days'));
        $bulan = date("m", strtotime($date));

        $budget_no = AccBudget::where('budget_no', $request->budget_no)
        ->where('periode', $tahun)->get();
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

        if ($nomorurut != null)
        {
            // foreach ($nomordepan as $nomors) {
            //   $nomor = $nomors->id+1;
            // }
            $nomor = substr($nomorurut[0]->no_pr, -3);
            $nomor = $nomor + 1;
            $nomor = sprintf('%03d', $nomor);
        }
        else
        {
            $nomor = "001";
        }

        if ($dept == "Management Information System")
        {
            $dept = "IT";
        }
        else if ($dept == "Accounting")
        {
            $dept = "AC";
        }
        else if ($dept == "Assembly (WI-A)")
        {
            $dept = "AS";
        }
        else if ($dept == "Educational Instrument (EI)")
        {
            $dept = "EI";
        }
        else if ($dept == "General Affairs")
        {
            $dept = "GA";
        }
        else if ($dept == "Human Resources")
        {
            $dept = "HR";
        }
        else if ($dept == "Logistic")
        {
            $dept = "LO";
        }
        else if ($dept == "Maintenance")
        {
            $dept = "PM";
        }
        else if ($dept == "Parts Process (WI-PP)")
        {
            $dept = "TP";
        }
        else if ($dept == "Procurement" || $dept == "Purchasing")
        {
            $dept = "PH";
        }
        else if ($dept == "Production Control")
        {
            $dept = "PC";
        }
        else if ($dept == "Production Engineering")
        {
            $dept = "PE";
        }
        else if ($dept == "Quality Assurance")
        {
            $dept = "QA";
        }
        else if ($dept == "Welding-Surface Treatment (WI-WST)")
        {
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

        try
        {

            //getManager From Department
            $manager = null;
            $posisi = null;

            if ($request->get('department') == "Production Engineering")
            {
                $manager = 'PI0703002';
            }
            else
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '" . $request->get('department') . "' and position = 'manager'");
            }

            if ($manag != null)
            {
                $posisi = "manager";

                foreach ($manag as $mn)
                {
                    $manager = $mg->employee_id;
                }
            }
            else
            {
                $posisi = "dgm";
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

            $data = new AccPurchaseRequisition(['no_pr' => $request->get('no_pr') , 'emp_id' => $request->get('emp_id') , 'emp_name' => $request->get('emp_name') , 'department' => $request->get('department') , 'group' => $request->get('group') , 'submission_date' => $submission_date, 'po_due_date' => $po_date, 'note' => $request->get('note') , 'file' => $file->filename, 'file_pdf' => 
                'PR'.$request->get('no_pr').'.pdf', 'posisi' => $posisi, 'status' => 'approval', 'no_budget' => $request->get('budget_no') , 'manager' => $manager, 'dgm' => $this->dgm, 'gm' => $this->gm, 'created_by' => $id]);

            $data->save();

            $mod_date = date('Y-m-d', strtotime($request->get('submission_date') . ' + 21 days'));

            for ($i = 1;$i <= $lop;$i++)
            {
                $item_code = "item_code" . $i;
                $item_desc = "item_desc" . $i;
                $item_spec = "item_spec" . $i;
                $item_stock = "item_stock" . $i;
                $item_currency = "item_currency" . $i;
                $item_currency_text = "item_currency_text" . $i;
                $item_price = "item_price" . $i;
                $item_qty = "qty" . $i;
                $item_uom = "uom" . $i;
                $item_amount = "amount" . $i;
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
                $price_real = preg_replace('/[^0-9]/', '', $request->get($item_price));
                $amount = preg_replace('/[^0-9]/', '', $request->get($item_amount));

                $data2 = new AccPurchaseRequisitionItem([
                    'no_pr' => $request->get('no_pr') , 
                    'item_code' => $request->get($item_code) , 
                    'item_desc' => $request->get($item_desc) , 
                    'item_spec' => $request->get($item_spec) ,
                    'item_stock' => $request->get($item_stock) , 
                    'item_request_date' => $mod_date, 
                    'item_currency' => $current, 'item_price' => $price_real, 'item_qty' => $request->get($item_qty) , 'item_uom' => $request->get($item_uom) , 'item_amount' => $amount, 'status' => $status, 'created_by' => $id]);

                $data2->save();
            }

            $detail_pr = AccPurchaseRequisition::select('*')
            ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
            ->where('acc_purchase_requisitions.id', '=', $data->id)
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('accounting_purchasing.report.report_pr', array(
                'pr' => $detail_pr,
            ));

            $pdf->save(public_path() . "/purchase_requisition/PR".$detail_pr[0]->no_pr.".pdf");

            $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.manager = users.username where acc_purchase_requisitions.id = " . $data->id;
            $mailtoo = DB::select($mails);

            if ($mailtoo == null)
            { // Jika Gaada Manager
                $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = " . $data->id;
                $mailtoo = DB::select($mails);
            }

            $isimail = "select * FROM acc_purchase_requisitions where acc_purchase_requisitions.id = " . $data->id;
            $purchaserequisition = db::select($isimail);

            Mail::to($mailtoo)->send(new SendEmail($purchaserequisition, 'purchase_requisition'));

            return redirect('/purchase_requisition2')->with('status', 'PR Berhasil Dibuat')
            ->with('page', 'Purchase Requisition');
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_requisition')->with('error', $e->getMessage())
            ->with('page', 'Purchase Requisition');
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


    //==================================//
    //          Report PR               //
    //==================================//
    public function report_purchase_requisition($id){

        $detail_pr = AccPurchaseRequisition::select('*')
        ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
        ->where('acc_purchase_requisitions.id', '=', $id)
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('accounting_purchasing.report.report_pr', array(
            'pr' => $detail_pr,
        ));

        // $pdf->save(public_path() . "/meetings/" . $reports[0]->id . ".pdf");

        $path = "purchase_requisition/" . $detail_pr[0]->no_pr . ".pdf";
        return $pdf->stream("PR ".$detail_pr[0]->no_pr. ".pdf");

        // return view('accounting_purchasing.report.report_pr', array(
        //  'pr' => $detail_pr,
        // ))->with('page', 'Meeting')->with('head', 'Meeting List');
    }

    //==================================//
    //          Verifikasi PR           //
    //==================================//
    public function verifikasi_purchase_requisition($id)
    {
        $pr = AccPurchaseRequisition::find($id);

        $items = AccPurchaseRequisitionItem::select('acc_purchase_requisition_items.*')->join('acc_purchase_requisitions', 'acc_purchase_requisition_items.no_pr', '=', 'acc_purchase_requisitions.no_pr')
        ->where('acc_purchase_requisitions.id', '=', $id)->get();

        return view('accounting_purchasing.verifikasi.verifikasi_pr', array(
            'pr' => $pr,
            'items' => $items
        ))->with('page', 'Purchase Requisition');
    }

    public function approval_purchase_requisition(Request $request, $id)
    {
        $approve = $request->get('approve');

        if (count($approve) == 3)
        {
            $pr = AccPurchaseRequisition::find($id);

            if ($pr->posisi == "manager")
            {
                $pr->posisi = "dgm";
                $pr->approvalm = "Approved";
                $pr->dateapprovalm = date('Y-m-d H:i:s');

                $mailto = "select distinct employees.name,email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = '" . $pr->id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }
            }

            else if ($pr->posisi == "dgm")
            {
                $pr->posisi = "gm";
                $pr->approvaldgm = "Approved";
                $pr->dateapprovaldgm = date('Y-m-d H:i:s');

                $mailto = "select distinct employees.name,email from acc_purchase_requisitions join users on acc_purchase_requisitions.gm = users.username where acc_purchase_requisitions.id = '" . $pr->id . "'";
                $mails = DB::select($mailto);

                foreach ($mails as $mail)
                {
                    $mailtoo = $mail->email;
                }
            }

            else if ($pr->posisi == "gm")
            {

                $pr->posisi = 'acc';
                $pr->approvalgm = "Approved";
                $pr->dateapprovalgm = date('Y-m-d H:i:s');
                $pr->status = "approval_acc";

                //kirim email ke Mas Shega & Mas Erlangga
                $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = 'Purchasing Control' and (employee_id = 'PI1908032' or employee_id = 'PI1810020')";
                $mailtoo = DB::select($mails);
            }

            $isimail = "select * FROM acc_purchase_requisitions where acc_purchase_requisitions.id = " . $pr->id;
            $pr_isi = db::select($isimail);

            Mail::to($mailtoo)->send(new SendEmail($pr_isi, 'purchase_requisition'));
            $pr->save();

            return redirect('/purchase_requisition/verifikasi/' . $id)->with('status', 'PR Approved')
            ->with('page', 'Purchase Requisition');
        }
        else
        {
            return redirect('/purchase_requisition/verifikasi/' . $id)->with('error', 'PR Not Approved')
            ->with('page', 'Purchase Requisition');
        }
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

        $isimail = "select * FROM acc_purchase_requisitions where acc_purchase_requisitions.id = " . $pr->id;
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
        $purchase_requistion_item = AccPurchaseRequisition::join('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')->where('acc_purchase_requisitions.id', '=', $request->get('id'))
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
                $item_uom = "uom_edit" . $lp;
                $item_req = "req_date_edit" . $lp;

                $data2 = AccPurchaseRequisitionItem::where('id', $lp)->update([
                  'item_code' => $request->get($item_code), 
                  'item_desc' => $request->get($item_desc), 
                  'item_spec' => $request->get($item_spec), 
                  'item_uom' => $request->get($item_uom), 
                  'item_request_date' => $request->get($item_req), 
                  'created_by' => $id
              ]);

            }

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

    //==================================//
    //          Purchase Order          //
    //==================================//
    public function purchase_order()
    {
        $title = 'Purchase Order';
        $title_jp = '';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

        $authorized2 = EmployeeSync::select('employee_id', 'name')->where('position', '=', 'Manager')
        ->where('department', '=', 'Procurement')
        ->first();

        $authorized3 = EmployeeSync::select('employee_id', 'name')->where('position', '=', 'Director')
        ->Orwhere('position', '=', 'General Manager')
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
            'uom' => $this->uom
        ))
        ->with('page', 'Purchase Order')
        ->with('head', 'Purchase Order');
    }

    public function delete_item_pr(Request $request)
    {

        try
        {
            $master = AccPurchaseRequisitionItem::where('id', '=', $request->get('id'))
            ->delete();
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
            return date('d F Y', strtotime($po->tgl_po));
        })
        ->editColumn('note', function ($po)
        {
            $note = "";
            if ($po->note != null)
            {
                $note = $po->note;
            }
            else
            {
                $note = '-';
            }

            return $note;
        })
        ->editColumn('status', function ($po)
        {
            $id = $po->id;

            if ($po->status == "notsend")
            {
                return '<label class="label label-danger">Staff PCH</a>';
            }

            else if ($po->status == "SAP")
            {
                return '<label class="label label-success">Diverifikasi</a>';
            }

        })
        ->addColumn('action', function ($po)
        {
            $id = $po->id;
            return '
                <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="editPO(' . $id . ')"><i class="fa fa-edit"></i></a>
                <a href="purchase_order/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="PO Report PDF"><i class="fa fa-file-pdf-o"></i></a>
            ';
        })
        ->rawColumns(['status' => 'status', 'action' => 'action'])
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

    public function pogetname(Request $request)
    {
        $html = array();
        $emp = EmployeeSync::where('employee_id', $request->authorized3)
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

        $lists = "<option value=''>-- Pilih Kota --</option>";
        foreach ($list_item as $item)
        {
            $lists .= "<option value='" . $item->item_code . "'>" . $item->item_desc . "</option>"; // Tambahkan tag option ke variabel $lists
            
        }
        return json_encode($lists);
    }

    public function pogetitem(Request $request)
    {
        $html = array();
        $kode_item = AccPurchaseRequisitionItem::join('acc_purchase_requisitions', 'acc_purchase_requisition_items.no_pr', '=', 'acc_purchase_requisitions.no_pr')->where('item_code', $request->item_code)
        ->where('acc_purchase_requisition_items.no_pr', $request->no_pr)
        ->get();
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

        try
        {
            $last = substr($nopo, -2);

            $query = "SELECT * FROM `cost_centers2` where code = '$last'";
            $cost_center = DB::select($query);

            foreach ($cost_center as $cc) {
                $cost = $cc->cost_center;
            }

            $data = new AccPurchaseOrder(['no_po' => $nopo, 'tgl_po' => $request->get('tgl_po') , 'supplier_code' => $request->get('supplier_code') , 'supplier_name' => $request->get('supplier_name') , 'supplier_due_payment' => $request->get('supplier_due_payment') , 'supplier_status' => $request->get('supplier_status') , 'material' => $request->get('material') , 'vat' => $request->get('price_vat') , 'transportation' => $request->get('transportation') , 'delivery_term' => $request->get('delivery_term') , 'holding_tax' => $request->get('holding_tax') , 'currency' => $request->get('currency') , 'buyer_id' => $request->get('buyer_id') , 'buyer_name' => $request->get('buyer_name') , 'authorized2' => $request->get('authorized2') , 'authorized2_name' => $request->get('authorized2_name') , 'authorized3' => $request->get('authorized3') , 'authorized3_name' => $request->get('authorized3_name') , 'note' => $request->get('note') , 'cost_center' => $cost , 'posisi' => 'staff_pch', 'status' => 'notsend', 'created_by' => $id]);

            $data->save();

            for ($i = 1;$i <= $lop;$i++)
            {
                $no_pr = "no_pr" . $i;
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

                $data2 = new AccPurchaseOrderDetail(['no_po' => $nopo, 'no_pr' => $request->get($no_pr) , 'no_item' => $request->get($no_item) , 'nama_item' => $request->get($nama_item) , 'budget_item' => $request->get($item_budget) , 'delivery_date' => $request->get($delivery_date) , 'qty' => $request->get($qty) , 'uom' => $request->get($uom) , 'goods_price' => $request->get($goods_price) , 'last_price' => $request->get($last_price) , 'service_price' => $request->get($service_price) , 'konversi_dollar' => $request->get($konversi_dollar) , 'gl_number' => $request->get($gl_number) ,'created_by' => $id]);

                $data2->save();

                $data3 = AccPurchaseRequisitionItem::where('item_code', $request->get($no_item))->where('no_pr', $request->get($no_pr))->update(['sudah_po' => 'true', ]);
            }

            return redirect('/purchase_order')
            ->with('status', 'Purchase Order Berhasil Dibuat')
            ->with('page', 'Purchase Order');
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

        $lop = explode(',', $request->get('looping'));

        try
        {

            $data3 = AccPurchaseOrder::where('no_po', $request->get('no_po_edit'))
            ->update(['supplier_code' => $request->get('supplier_code_edit') , 'supplier_name' => $request->get('supplier_name_edit') , 'supplier_due_payment' => $request->get('supplier_due_payment_edit') , 'supplier_status' => $request->get('supplier_status_edit') , 'material' => $request->get('material_edit') , 'vat' => $request->get('price_vat_edit') , 'transportation' => $request->get('transportation_edit') , 'delivery_term' => $request->get('delivery_term_edit') , 'holding_tax' => $request->get('holding_tax_edit') , 'currency' => $request->get('currency_edit') , 'authorized3' => $request->get('authorized3_edit') , 'authorized3_name' => $request->get('authorized3_name_edit') , 'note' => $request->get('note_edit') , ]);

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
                $gl_number = "gl_number" . $lp;

                $data2 = AccPurchaseOrderDetail::where('id', $lp)->update(['no_item' => $request->get($no_item) , 'nama_item' => $request->get($nama_item) , 'budget_item' => $request->get($item_budget) , 'delivery_date' => $request->get($delivery_date) , 'qty' => $request->get($qty) , 'uom' => $request->get($uom) , 'goods_price' => $request->get($goods_price) , 'last_price' => $request->get($last_price) , 'service_price' => $request->get($service_price) , 'konversi_dollar' => $request->get($konversi_dollar) , 'gl_number' => $request->get($gl_number) , 'created_by' => $id]);

                // $data2->save();
                
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

                $data = new AccPurchaseOrderDetail(['no_po' => $request->get('no_po_edit') , 'no_pr' => $request->get($no_pr2) , 'no_item' => $request->get($no_item2) , 'nama_item' => $request->get($nama_item2) , 'budget_item' => $request->get($item_budget2) , 'delivery_date' => $request->get($delivery_date2) , 'qty' => $request->get($qty2) , 'uom' => $request->get($uom2) , 'goods_price' => $request->get($goods_price2) , 'last_price' => $request->get($last_price2) , 'service_price' => $request->get($service_price2) , 'konversi_dollar' => $request->get($konversi_dollar2) , 'gl_number' => $request->get($gl_number2) , 'created_by' => $id]);

                $data->save();
                $data3 = AccPurchaseRequisitionItem::where('item_code', $request->get($no_item2))->where('no_pr', $request->get($no_pr2))->update(['sudah_po' => 'true', ]);
            }

            return redirect('/purchase_order')
            ->with('status', 'Purchase Order Berhasil Dirubah')
            ->with('page', 'Purchase Order');
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_order')->with('error', $e->getMessage())
            ->with('page', 'Purchase Order');
        }
    }

    public function delete_item_po(Request $request)
    {

        try
        {
            $item = AccPurchaseOrderDetail::find($request->get('id'));

            $data3 = AccPurchaseRequisitionItem::where('item_code', $item->no_item)
            ->where('no_pr', $item->no_pr)
            ->update(['sudah_po' => null, ]);

            $master = AccPurchaseOrderDetail::where('id', '=', $request->get('id'))
            ->delete();

        }
        catch(QueryException $e)
        {
            return redirect('/purchase_order')->with('error', $e->getMessage())
            ->with('page', 'Purchase Order');
        }

    }

    //==================================//
    //          Report PO               //
    //==================================//
    public function report_purchase_order($id){

        $detail_po = AccPurchaseOrder::select('*')
        ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
        ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
        ->where('acc_purchase_orders.id', '=', $id)
        ->get();

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

        // $pdf->save(public_path() . "/meetings/" . $reports[0]->id . ".pdf");

        $path = "purchase_requisition/" . $detail_po[0]->no_po . ".pdf";
        return $pdf->stream("PR ".$detail_po[0]->no_po. ".pdf");

        // return view('accounting_purchasing.report.report_po', array(
        //  'po' => $detail_po,
        //  'pr' => $pr
        // ))->with('page', 'Meeting')->with('head', 'Meeting List');
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
            $adddepartment = "and acc_investments.Department in (" . $department . ") ";
        }

        $qry = "SELECT  * FROM acc_investments
        WHERE acc_investments.deleted_at IS NULL " . $tanggal . "" . $adddepartment . "
        ORDER BY acc_investments.id DESC";

        $invest = DB::select($qry);

        return DataTables::of($invest)
        ->editColumn('submission_date', function ($invest)
        {
            return date('d F Y', strtotime($invest->submission_date));
        })
        ->editColumn('status', function ($invest)
        {
            $id = $invest->id;
            if ($invest->status == "aw")
            {
                return '<a href="purchase_requisition/detail/' . $id . '" class="btn btn-primary btn-xs">Detail</a>';
            }

        })->rawColumns(['status' => 'status'])
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

            $inv = AccInvestment::create(['investment_no' => $request->get('investment_no') , 'applicant_id' => $request->get('applicant_id') , 'applicant_name' => $request->get('applicant_name') , 'applicant_department' => $request->get('applicant_department') , 'reff_number' => $request->get('reff_number') , 'submission_date' => $request->get('submission_date') , 'category' => $request->get('category') , 'subject' => $request->get('subject') , 'type' => $request->get('type') , 'objective' => $request->get('objective') , 'objective_detail' => $request->get('objective_detail') , 'desc_supplier' => $request->get('desc_supplier') , 'created_by' => $id_user]);

            $inv->save();

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
                'id' => $inv->id
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

    public function detail_investment($id)
    {
        $title = 'Detail Form Investment';
        $title_jp = '投資申請内容';

        $inv = AccInvestment::find($id);

        $emp = EmployeeSync::where('employee_id', $inv->applicant_id)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
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
        try
        {
            $id_user = Auth::id();

            $inv = AccInvestment::where('id', $request->get('id'))
            ->update(['applicant_id' => $request->get('applicant_id') , 'applicant_name' => $request->get('applicant_name') , 'applicant_department' => $request->get('applicant_department') , 'reff_number' => $request->get('reff_number') , 'submission_date' => $request->get('submission_date') , 'category' => $request->get('category') , 'subject' => $request->get('subject') , 'type' => $request->get('type') , 'objective' => $request->get('objective') , 'objective_detail' => $request->get('objective_detail') , 'desc_supplier' => $request->get('desc_supplier') , 'created_by' => $id_user]);

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

    //Item Invesment
    public function fetch_investment_item($id)
    {
        $investment = AccInvestment::find($id);

        $investment_item = AccInvestmentDetail::leftJoin("acc_investments", "acc_investment_details.reff_number", "=", "acc_investments.reff_number")->select('acc_investment_details.*')
        ->where('acc_investment_details.reff_number', '=', $investment->reff_number)
        ->get();

        return DataTables::of($investment_item)
        ->editColumn('amount', function ($investment_item)
        {
            return $investment_item->amount . ' ,00';
        })
        ->addColumn('action', function ($investment_item)
        {
            return '
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit(' . $investment_item->id . ')">Edit</button>
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete(' . $investment_item->id . ',\'' . $investment_item->reff_number . '\')">Delete</button>';
        })

        ->rawColumns(['amount' => 'amount', 'action' => 'action'])
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

            $item = new AccInvestmentDetail(['reff_number' => $request->get('reff_number') , 'kode_item' => $request->get('kode_item') , 'detail' => $request->get('detail_item') , 'qty' => $request->get('jumlah_item') , 'price' => $request->get('price_item') , 'amount' => $request->get('amount_item') , 'created_by' => $id_user]);

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
}

