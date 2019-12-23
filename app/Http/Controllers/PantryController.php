<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use DataTables;
use Response;
use App\PantryOrder;
use App\PantryMenu;
use App\PantryLog;

class PantryController extends Controller
{
    public function __construct()
	{
		$this->middleware('auth');
	}

	public function pesanmenu()
	{
		$title = 'Pantry Drink Order';
		$title_jp = '給湯室飲み物注文';
		$menus = PantryMenu::whereNull('deleted_at')
        ->get();

        $username = Auth::user()->username;
        $user = "select name from employees where employee_id='$username'";
       	$users = DB::select($user);

		return view('pantry.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'menus' => $menus,
			'users' => $users
		))->with('page', 'Pantry');
	}

	public function daftarpesanan(){
		$title = 'Pantry Drink Order List';
		$title_jp = '???';
		$orders = PantryOrder::where('status', 'confirmed')
        ->get();

		return view('pantry.pesanan', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'orders' => $orders
		))->with('page', 'Pesanan Pantry');
	}

	public function fetchMenu(Request $request){

		$pemesan = Auth::user()->username;

		$pantry = PantryMenu::find($request->get("id"));
		$menu = $pantry->menu;
		// if ($request->get('id') == 1) {
		// 	$menu = "Tea";
		// } else if ($request->get('id') == 2) {
		// 	$menu = "Coffee";
		// } else if ($request->get('id') == 3) {
		// 	$menu = "Oca";
		// } else if ($request->get('id') == 4) {
		// 	$menu = "Water";
		// }

		$response = array(
			'status' => true,
			'menu' => $menu,
			'pemesan' => $pemesan
		);
		return Response::json($response);
	}

	public function inputMenu(Request $request){
		try{
			$id_user = Auth::id();
            $menu = new PantryOrder([
                'pemesan' => $request->get('pemesan'),
                'minuman' => $request->get('menu'),
                'keterangan' => $request->get('keterangan'),
                'gula' => $request->get('gula'),
                'jumlah' => $request->get('jumlah'),
                'tempat' => $request->get('tempat'),
                'status' => 'unconfirmed',
                'tgl_pesan' => date('Y-m-d'),
                'created_by' => $id_user
            ]);

            $menu->save();

			$response = array(
				'status' => true,
				'message' => 'Minuman Berhasil Dikonfirmasi',
			);
			return Response::json($response);
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function deleteMenu(Request $request)
    {
        $pantry = PantryOrder::find($request->get("id"));
        $pantry->delete();

        $response = array(
	    	'status' => true
	    );
	    return Response::json($response);
    }

	public function fetchpesanan(Request $request){

		$datenow = date('Y-m-d');

		$lists = PantryOrder::where('pemesan', '=', $request->get('pemesan'))
		->where('status', '=', 'unconfirmed')
		->where('tgl_pesan', '=', $datenow)
		->get();

		$response = array(
			'status' => true,
			'lists' => $lists,
		);
		return Response::json($response);
	}

	public function konfirmasipesanan(Request $request)
    {
    	try{
	        $pantry = 

	        PantryOrder::where('pemesan', '=', $request->get("pemesan"))->update([
				'status' => 'confirmed'
			]);
			// PantryOrder::find($request->get("pemesan"));
	  //       $pantry->status = 'confirmed';
	  //       $pantry->save();

	        $response = array(
		    	'status' => true,
		    	'message' => 'Pesanan Berhasil Dikonfirmasi',
		    );
		    return Response::json($response);

		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

    }



    //CRUD Menu Gambar

    public function daftarmenu(){
		$title = 'Daftar Minuman Pantry';
		$title_jp = '???';

		$menus = PantryMenu::orderBy('id', 'ASC')
        ->get();

		return view('pantry.menu', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'menus' => $menus
		))->with('page', 'Pantry');
	}

    public function create_menu()
    {
        return view('pantry.create_menu')->with('page', 'Pantry');
    }

    public function create_menu_action(Request $request)
    {
        try{

        	$file = $request->file('gambar');
        	$tujuan_upload = 'images/minuman';
        	$namafile = $file->getClientOriginalName();

			$file->move($tujuan_upload,$namafile);

            $id = Auth::id();
            $menu = new PantryMenu([
              'menu' => $request->get('menu'),
              'gambar' => $namafile,
              'created_by' => $id
          	]);

            $menu->save();
            return redirect('/index/pantry/menu')->with('status', 'Menu has been created.')->with('page', 'Pantry');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Menu name already exist.')->with('page', 'Pantry');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Pantry');
            }
        }
    }

    public function edit_menu($id)
    {
        $menus = PantryMenu::find($id);

        return view('pantry.edit_menu', array(
            'menus' => $menus
        ))->with('page', 'Pantry');
    }

    public function edit_menu_action(Request $request, $id)
    {
        try{
        	$file = $request->file('gambar');

            if ($file != NULL) {
            	$tujuan_upload = 'images/minuman';
	        	$namafile = $file->getClientOriginalName();
				$file->move($tujuan_upload,$namafile);

	            $menu = PantryMenu::find($id);
	            $menu->menu = $request->get('menu');
	            $menu->gambar = $namafile;
	            $menu->save();
	        }
	        else{
	        	$menu = PantryMenu::find($id);
	            $menu->menu = $request->get('menu');
	            $menu->save();
	        }

            return redirect('/index/pantry/menu')->with('status', 'Menu data has been edited.')->with('page', 'Pantry');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Menu name already exist.')->with('page', 'Pantry');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Pantry');
            }
        } 
    }

    public function delete_menu($id)
    {
        $menu = PantryMenu::find($id);
        $menu->delete();

        return redirect('/index/pantry/menu')->with('status', 'Menu has been deleted.')->with('page', 'Pantry');
    }

	function filter(Request $request)
    {
        $detailpesanan = DB::table('pantry_orders')
        ->select('name','minuman','keterangan','gula','jumlah','tempat','status')
        ->join('employees','employees.employee_id','=','pantry_orders.pemesan')
        ->where('pemesan','=',Auth::user()->username)
        ->orWhere(function ($query) {
		    $query->where('status', '=', 'proses')
		          ->where('status', '=', 'confirmed');
		})
        ->whereNull('pantry_orders.deleted_at');

        $detailpesanan = $detailpesanan->orderBy('pantry_orders.id', 'DESC');
        $pesanan = $detailpesanan->get();

        return DataTables::of($pesanan)

        ->editColumn('status',function($pesanan){
            if($pesanan->status == "confirmed") {
              return '<label class="label label-danger">Waiting Confirmation</label>';
            }
            else if($pesanan->status == "proses") {
              return '<label class="label label-primary">Making Your Orders</label>';
            }
          })


        ->rawColumns(['status' => 'status'])
        ->make(true);
    }

    public function daftarkonfirmasi(){
		$title = 'Pantry Drink Confirmation';
		$title_jp = '???';

		return view('pantry.pesanan_konfirmasi', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Pesanan Pantry');
	}

	function filterkonfirmasi(Request $request)
    {
        $detailpesanan = DB::table('pantry_orders')
        ->select('pantry_orders.id','name','minuman','keterangan','gula','jumlah','tempat','status')
        ->join('employees','employees.employee_id','=','pantry_orders.pemesan')
        ->where('status', '=', 'confirmed')
        ->orwhere('status', '=', 'proses')
        ->whereNull('pantry_orders.deleted_at');

        $detailpesanan = $detailpesanan->orderBy('pantry_orders.id', 'DESC');
        $pesanan = $detailpesanan->get();

		$response = array(
			'status' => true,
			'pesanan' => $pesanan
		);
		return Response::json($response);
    }

    public function konfirmasi(Request $request){
		try{
            $pantry = PantryOrder::find($request->get("id"));
            $pantry->status = 'proses';
            $pantry->save();

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
              'datas' => "Error",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Error",
            );
             return Response::json($response);
         	}
	    }
	}

    public function selesaikan(Request $request){
		try{
			$id_user = Auth::id();

			$pantry = PantryOrder::find($request->get("id"));

            $log = new PantryLog([
                'pemesan' => $pantry->pemesan,
                'minuman' => $pantry->minuman,
                'keterangan' => $pantry->keterangan,
                'gula' => $pantry->gula,
                'jumlah' => $pantry->jumlah,
                'tempat' => $pantry->tempat,
                'tgl_pesan' => $pantry->created_at,
                'tgl_dibuat' => date('Y-m-d H:i:s'),
                'created_by' => $id_user
            ]);

            $log->save();

           	$pantry->delete();

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
              'datas' => "Error",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Error",
            );
             return Response::json($response);
            }
        } 
    }
}