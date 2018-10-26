<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OriginGroup;
use App\Inventory;
use Response;
use DataTables;

class InventoryController extends Controller
{

    private $plant;

    public function __construct()
    {
        $this->middleware('auth');
        $this->plant = [
            '8190',
            '8191',
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plants = $this->plant;
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'asc')->get();
        return view('inventories.index', array(
            'plants' => $plants,
            'origin_groups' => $origin_groups,
        ))->with('page', 'Location Stock');
        //
    }

    public function fetch(Request $request)
    {
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

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
