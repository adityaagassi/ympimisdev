<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\ShipmentCondition;
use Illuminate\Database\QueryException;

class ShipmentConditionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipment_conditions = ShipmentCondition::orderBy('shipment_condition_code', 'ASC')
        ->with(array('user'))
        ->get();

        return view('shipment_conditions.index', array(
            'shipment_conditions' => $shipment_conditions
        ))->with('page', 'Shipment Condition');

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('shipment_conditions.create')->with('page', 'Shipment Condition');
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
        try{

            $id = Auth::id();
            $shipment_condition = new ShipmentCondition([
              'shipment_condition_code' => $request->get('shipment_condition_code'),
              'shipment_condition_name' => $request->get('shipment_condition_name'),
              'created_by' => $id
          ]);

            $shipment_condition->save();
            return redirect('/index/shipment_condition')->with('status', 'New shipment condition has been created.')->with('page', 'Shipment Condition');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Shipment condition code or shipment condition name already exist.')->with('page', 'Shipment Condition');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Shipment Condition');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shipment_condition = ShipmentCondition::find($id);
        $users = User::orderBy('name', 'ASC')->get();

        return view('shipment_conditions.show', array(
            'shipment_condition' => $shipment_condition,
            'users' => $users,
        ))->with('page', 'Shipment Condition');
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
        $shipment_condition = ShipmentCondition::find($id);

        return view('shipment_conditions.edit', array(
            'shipment_condition' => $shipment_condition
        ))->with('page', 'Shipment Condition');
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
        try{

            $shipment_condition = ShipmentCondition::find($id);
            $shipment_condition->shipment_condition_code = $request->get('shipment_condition_code');
            $shipment_condition->shipment_condition_name = $request->get('shipment_condition_name');
            $shipment_condition->save();

            return redirect('/index/shipment_condition')->with('status', 'Shipment condition data has been edited.')->with('page', 'Shipment Condition');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Shipment condition code or shipment condition name already exist.')->with('page', 'Shipment Condition');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Shipment Condition');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shipment_condition = ShipmentCondition::find($id);
        $shipment_condition->delete();

        return redirect('/index/shipment_condition')->with('status', 'Shipment condition has been deleted.')->with('page', 'Shipment Condition');
        //
    }
}
