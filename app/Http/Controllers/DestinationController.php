<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Destination;
use Illuminate\Database\QueryException;

class DestinationController extends Controller
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
        $destinations = Destination::orderBy('destination_code', 'ASC')
        ->get();

        return view('destinations.index', array(
            'destinations' => $destinations
        ))->with('page', 'Destination');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('destinations.create')->with('page', 'Destination');
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
            $destination = new Destination([
              'destination_code' => $request->get('destination_code'),
              'destination_name' => $request->get('destination_name'),
              'destination_shortname' => $request->get('destination_shortname'),
              'created_by' => $id
          ]);

            $destination->save();
            return redirect('/index/destination')->with('status', 'New destination has been created.')->with('page', 'Destination');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Destination code or destination name already exist.')->with('page', 'Destination');
            }

        }
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
        $destination = Destination::find($id);
        $users = User::orderBy('name', 'ASC')->get();

        return view('destinations.show', array(
            'destination' => $destination,
            'users' => $users,
        ))->with('page', 'Destination');
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
        $destination = Destination::find($id);

        return view('destinations.edit', array(
            'destination' => $destination
        ))->with('page', 'Destination');
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

            $destination = Destination::find($id);
            $destination->destination_code = $request->get('destination_code');
            $destination->destination_name = $request->get('destination_name');
            $destination->destination_shortname = $request->get('destination_shortname');
            $destination->save();

            return redirect('/index/destination')->with('status', 'Destination data has been edited.')->with('page', 'Destination');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Destination code or destination name already exist.')->with('page', 'Destination');
            }

        } 
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
        $destination = Destination::find($id);
        $destination->delete();

        return redirect('/index/destination')->with('status', 'Destination has been deleted.')->with('page', 'Destination');
        //
    }
}
