<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Container;
use Illuminate\Database\QueryException;

class ContainerController extends Controller
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
        $containers = Container::orderBy('container_code', 'ASC')
        ->with(array('user'))
        ->get();

        return view('containers.index', array(
            'containers' => $containers
        ));

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('containers.create');
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
            $container = new Container([
              'container_code' => $request->get('container_code'),
              'container_name' => $request->get('container_name'),
              'created_by' => $id
          ]);

            $container->save();
            return redirect('/index/container')->with('status', 'New container has been created.');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
            // self::delete($lid);
                return back()->with('error', 'Container code or container name already exist.');
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
        $container = Container::find($id);
        $users = User::orderBy('name', 'ASC')->get();

        return view('containers.show', array(
            'container' => $container,
            'users' => $users,
        ));
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
        $container = Container::find($id);

        return view('containers.edit', array(
            'container' => $container
        ));
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

        $container = Container::find($id);
        $container->container_code = $request->get('container_code');
        $container->container_name = $request->get('container_name');
        $container->save();

        return redirect('/index/container')->with('status', 'Container data has been edited.');

    }
    catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
            // self::delete($lid);
            return back()->with('error', 'Container code or container name already exist.');
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
        $container = Container::find($id);
        $container->delete();

        return redirect('/index/container')->with('status', 'Container has been deleted.');

        //
    }
}
