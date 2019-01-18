<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OriginGroup;
use App\Inventory;
use App\Material;
use Response;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{

    private $plant;
    private $transaction_status;

    public function __construct()
    {
        $this->middleware('auth');
        $this->plant = [
            '8190',
            '8191',
        ];
        $this->transaction_status = [
            'Uploaded',
            'Not Uploaded',
        ];
    }

    public function index()
    {
        $plants = $this->plant;
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'asc')->get();
        return view('inventories.indexInventory', array(
            'plants' => $plants,
            'origin_groups' => $origin_groups,
        ))->with('page', 'Location Stock');
    }

    public function indexCompletion(){
        $transaction_statuses = $this->transaction_status;
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'asc')->get();
        return view('inventories.indexCompletion', array(
            'origin_groups' => $origin_groups,
            'transaction_statuses' => $transaction_statuses,
        ))->with('page', 'Completion Transaction')->with('head', 'Transaction');
    }

    public function indexTransfer(){
        $transaction_statuses = $this->transaction_status;
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'asc')->get();
        return view('inventories.indexTransfer', array(
            'origin_groups' => $origin_groups,
            'transaction_statuses' => $transaction_statuses,
        ))->with('page', 'Transfer Transaction')->with('head', 'Transaction');
    }

    public function fetchCompletion(Request $request){
        $completions = db::table('flo_details')
        ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
        ->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
        ->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
        ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code');

        if(strlen($request->get('dateFrom')) > 0){
            $date_from = date('Y-m-d', strtotime($request->get('dateFrom')));
            $completions = $completions->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        if(strlen($request->get('dateTo')) > 0){
            $date_to = date('Y-m-d', strtotime($request->get('dateTo')));
            $completions = $completions->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        if(strlen($request->get('materialNumber')) > 0){
            $material_number = explode(",", $request->get('materialNumber'));
            $completions = $completions->whereIn('flo_details.material_number', $material_number);
        }

        if(strlen($request->get('storageLocation')) > 0){
            $storage_location = explode(",", $request->get('storageLocation'));
            $completions = $completions->whereIn('materials.issue_storage_location', $storage_location);
        }

        if($request->get('originGroup') != null){
            $completions = $completions->whereIn('materials.origin_group_code', $request->get('originGroup'));
        }

        if($request->get('transactionStatus') == 'Uploaded'){
            $completions = $completions->whereNotNull('flo_details.completion');
        }
        elseif($request->get('transactionStatus') == 'Not Uploaded'){
            $completions = $completions->whereNull('flo_details.completion');

        }

        $completions = $completions->orderBy('flo_details.created_at', 'asc')
        ->select(
            'flo_details.material_number',
            'materials.material_description',
            'materials.issue_storage_location',
            'flo_details.quantity',
            db::raw('if(flos.shipment_schedule_id = 0, "Maedaoshi", destinations.destination_shortname) as destination'),
            db::raw('if(flo_details.completion is not null, flo_details.completion, "-") as completion'),
            'flo_details.created_at'
        )
        ->get();

        $response = array(
            'status' => true,
            'tableData' => $completions,
        );
        return Response::json($response);
    }

    public function fetchTransfer(Request $request){
        $transfers = db::table('flo_details')
        ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
        ->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
        ->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
        ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
        ->leftJoin('flo_logs', 'flo_logs.flo_number', '=', 'flo_details.flo_number')
        ->where('flo_logs.status_code', '=', '2')
        ->whereIn('flos.status', ['2','3','4']);

        if(strlen($request->get('dateFrom')) > 0){
            $date_from = date('Y-m-d', strtotime($request->get('dateFrom')));
            $transfers = $transfers->where(DB::raw('DATE_FORMAT(flo_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        if(strlen($request->get('dateTo')) > 0){
            $date_to = date('Y-m-d', strtotime($request->get('dateTo')));
            $transfers = $transfers->where(DB::raw('DATE_FORMAT(flo_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        if(strlen($request->get('materialNumber')) > 0){
            $material_number = explode(",", $request->get('materialNumber'));
            $transfers = $transfers->whereIn('flo_details.material_number', $material_number);
        }

        if(strlen($request->get('storageLocation')) > 0){
            $storage_location = explode(",", $request->get('storageLocation'));
            $transfers = $transfers->whereIn('materials.issue_storage_location', $storage_location);
        }

        if($request->get('originGroup') != null){
            $transfers = $transfers->whereIn('materials.origin_group_code', $request->get('originGroup'));
        }

        if($request->get('transactionStatus') == 'Uploaded'){
            $transfers = $transfers->whereNotNull('flo_details.transfer');
        }
        elseif($request->get('transactionStatus') == 'Not Uploaded'){
            $transfers = $transfers->whereNull('flo_details.transfer');

        }

        $transfers = $transfers->orderBy('flo_details.created_at', 'asc')
        ->select(
            'flo_details.material_number',
            'materials.material_description',
            'materials.issue_storage_location',
            'flo_details.quantity',
            db::raw('if(flos.shipment_schedule_id = 0, "Maedaoshi", destinations.destination_shortname) as destination'),
            db::raw('if(flo_details.transfer is not null, flo_details.transfer, "-") as transfer'),
            'flo_logs.created_at'
        )
        ->get();

        $response = array(
            'status' => true,
            'tableData' => $transfers,
        );
        return Response::json($response);
    }

    public function fetch(Request $request){
        $inventory = Inventory::leftJoin('materials', 'materials.material_number', '=', 'inventories.material_number')
        ->leftJoin('origin_groups', 'origin_groups.origin_group_code', '=', 'materials.origin_group_code')
        ->select('inventories.plant', 'origin_groups.origin_group_name', 'inventories.material_number', 'materials.material_description', 'inventories.storage_location', 'inventories.quantity', 'inventories.updated_at');

        if($request->get('plant') != null){
            $inventory = $inventory->whereIn('plant', $request->get('plant'));
        }

        if($request->get('origin_group') != null){
            $inventory = $inventory->whereIn('materials.origin_group_code', $request->get('origin_group'));
        }

        if(strlen($request->get('material_number')) > 0){
            $material_number = explode(",", $request->get('material_number'));
            $inventory = $inventory->whereIn('inventories.material_number', $material_number);
        }

        if(strlen($request->get('storage_location')) > 0){
            $storage_location = explode(",", $request->get('storage_location'));
            $inventory = $inventory->whereIn('inventories.storage_location', $storage_location);
        }

        $inventoryTable = $inventory->orderBy('inventories.material_number', 'asc')->get();

        return DataTables::of($inventoryTable)->make(true);
    }

    public function downloadCompletion(Request $request){
        $file_path = "/uploads/sap/completions/" . $request->get('referenceFile');
        $path = url($file_path);

        $response = array(
            'file_path' => $path,
        );
        return Response::json($response);
    }

    public function downloadTransfer(Request $request){
        $file_path = "/uploads/sap/transfers/" . $request->get('referenceFile');
        $path = url($file_path);

        $response = array(
            'file_path' => $path,
        );
        return Response::json($response);
    }
}
