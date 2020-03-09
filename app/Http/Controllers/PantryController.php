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
use App\User;

class PantryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexDisplayPantryVisit(){
        $title = 'Pantry Visitor Monitoring';
        $title_jp = '給湯室の来室者監視';

        return view('pantry.visitor', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Pantry')->with('head', 'Pantry');
    }

// public function gw_send_sms($user,$pass,$sms_from,$sms_to,$sms_msg)  
// {           
//     $query_string = "api.aspx?apiusername=".$user."&apipassword=".$pass;
//     $query_string .= "&senderid=".rawurlencode($sms_from)."&mobileno=".rawurlencode($sms_to);
//     $query_string .= "&message=".rawurlencode(stripslashes($sms_msg)) . "&languagetype=1";        
//     $url = "http://gateway.onewaysms.com.au:10001/".$query_string;       
//     $fd = @implode('', file($url));      
//     if ($fd)  
//     {                       
//         if ($fd > 0) {
//             Print("MT ID : " . $fd);
//             $ok = "success";
//         }        
//         else {
//             print("Please refer to API on Error : " . $fd);
//             $ok = "fail";
//         }
//     }           
//     else      
//     {                       
//         $ok = "fail";       
//     }           
//     return $ok;  
// } 

    public function fetchPantryVisitorDetail(Request $request){
        $date = date('Y-m-d');
        $query = "";

        if(strlen($request->get('tanggal')) > 0){
            $date = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        if($request->get('type') == 'duration'){
            $query = "SELECT
            final.employee_id,
            ympimis.employee_syncs.name,
            IF
            (
            duration < 1, '<1 Min', IF ( duration >= 1 
            AND duration < 2, '<2 Min', IF ( duration >= 2 
            AND duration < 3, '<3 Min', IF ( duration >= 3 
            AND duration < 4, '<4 Min', IF ( duration >= 4 
            AND duration < 5, '<5 Min', IF ( duration >= 5 
            AND duration < 6, '<6 Min', IF ( duration >= 6 
            AND duration < 7, '<7 Min', IF ( duration >= 7 
            AND duration < 8,
            '<8 Min',
            '>8 Min' 
            ) 
            ) 
            ) 
            ) 
            ) 
            ) 
            ) 
            ) AS dur,
            duration 
            FROM
            (
            SELECT
            employee_id,
            SUM( round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) ) AS duration 
            FROM
            pantry_logs 
            WHERE
            date( in_time ) = '".$date."' 
            GROUP BY
            employee_id 
            ) AS final
            LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = final.employee_id 
            HAVING
            dur = '".$request->get('category')."' 
            ORDER BY
            duration desc";
        }
        else{
            $query = "SELECT
            pantry_logs.employee_id,
            ympimis.employee_syncs.name,
            concat( DATE_FORMAT( in_time, '%H:00' ), ' - ', DATE_FORMAT( date_add( in_time, INTERVAL 1 HOUR ), '%H:00' ) ) AS jam,
            sum( round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) ) AS duration 
            FROM
            pantry_logs
            LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = pantry_logs.employee_id 
            WHERE
            date( in_time ) = '2020-03-05' 
            GROUP BY
            pantry_logs.employee_id,
            concat( DATE_FORMAT( in_time, '%H:00' ), ' - ', DATE_FORMAT( date_add( in_time, INTERVAL 1 HOUR ), '%H:00' ) ),
            ympimis.employee_syncs.name 
            HAVING
            jam = '".$request->get('category')."'
            ORDER BY
            duration desc";
        }

        $details = db::connection('pantry')->select($query);

        $response = array(
            'status' => true,
            'details' => $details
        );
        return Response::json($response);
    }

    public function pesanmenu()
    {
        $title = 'Pantry Item Order';
        $title_jp = '給湯室注文品';
        $menus = PantryMenu::whereNull('deleted_at')->get();

        $menutop = PantryMenu::whereNull('deleted_at')->limit(3)->get();
        $menubot = PantryMenu::whereNull('deleted_at')->skip(3)->limit(3)->get();

        $username = Auth::user()->username;
        $user = "select name from users where username='$username'";
        $users = DB::select($user);

        return view('pantry.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'menus' => $menus,
            'menutop' => $menutop,
            'menubot' => $menubot,
            'users' => $users
        ))->with('page', 'Pantry')->with('head', 'Pantry');
    }

    public function daftarpesanan(){
        $title = 'Pantry Order List';
        $title_jp = '注文内容';
        $orders = PantryOrder::where('status', 'confirmed')
        ->get();

        return view('pantry.pesanan', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'orders' => $orders
        ))->with('page', 'Pantry Orders')->with('head', 'Pantry');
    }

    public function fetchPantryRealtimeVisitor() {
        $visitors = db::connection('pantry')->table('pantry_lists')
        ->leftJoin('ympimis.employee_syncs', 'ympimis.employee_syncs.employee_id', '=', 'pantry_lists.employee_id')
        ->select('pantry_lists.employee_id', 'ympimis.employee_syncs.name', 'pantry_lists.in_time')
        ->orderBy('pantry_lists.in_time', 'desc')
        ->get();

        $response = array(
            'status' => true,
            'visitors' => $visitors
        );
        return Response::json($response);
    }

    public function fetchPantryVisitor(Request $request){
        $date = date('Y-m-d');

        if(strlen($request->get('tanggal')) > 0){
            $date = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $query = "SELECT
        jam,
        count( employee_id ) AS qty_visit 
        FROM
        (
        SELECT DISTINCT
        pantry_logs.employee_id,
        concat( DATE_FORMAT( in_time, '%H:00' ), ' - ', DATE_FORMAT( date_add( in_time, INTERVAL 1 HOUR ), '%H:00' ) ) AS jam 
        FROM
        pantry_logs
        LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = pantry_logs.employee_id 
        WHERE
        date( in_time ) = '".$date."' 
        GROUP BY
        pantry_logs.employee_id,
        concat( DATE_FORMAT( in_time, '%H:00' ), ' - ', DATE_FORMAT( date_add( in_time, INTERVAL 1 HOUR ), '%H:00' ) ) 
        ) AS final 
        GROUP BY
        jam";

        $query2 = "SELECT
        count( employee_id ) AS qty_employee,
        sum( duration ) AS qty_duration 
        FROM
        (
        SELECT
        employee_id,
        SUM( round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) ) AS duration 
        FROM
        pantry_logs 
        WHERE
        date( in_time ) = '".$date."' 
        GROUP BY
        employee_id ) AS final";

        $query3 = "SELECT
        count(employee_id) as qty_employee, duration, indek 
        FROM
        (
        SELECT
        employee_id,
        IF
        (
        duration < 1, '<1 Min', IF ( duration >= 1 
        AND duration < 2, '<2 Min', IF ( duration >= 2 
        AND duration < 3, '<3 Min', IF ( duration >= 3 
        AND duration < 4, '<4 Min', IF ( duration >= 4 
        AND duration < 5, '<5 Min', IF ( duration >= 5 
        AND duration < 6, '<6 Min', IF ( duration >= 6 
        AND duration < 7, '<7 Min', IF ( duration >= 7 
        AND duration < 8,
        '<8 Min',
        '>8 Min' 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) AS duration,
        IF
        (
        duration < 1, 1, IF ( duration >= 1 
        AND duration < 2, 2, IF ( duration >= 2 
        AND duration < 3, 3, IF ( duration >= 3 
        AND duration < 4, 4, IF ( duration >= 4 
        AND duration < 5, 5, IF ( duration >= 5 
        AND duration < 6, 6, IF ( duration >= 6 
        AND duration < 7, 7, IF ( duration >= 7 
        AND duration < 8,
        8,
        9 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) AS indek 
        FROM
        (
        SELECT
        employee_id,
        SUM( round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) ) AS duration 
        FROM
        pantry_logs 
        WHERE
        date( in_time ) = '".$date."' 
        GROUP BY
        employee_id 
        ) AS final 
        ) AS final2
        group by duration, indek order by indek asc";

        $query4 = "SELECT
        office_members.employee_id,
        employee_syncs.NAME 
        FROM
        office_members
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = office_members.employee_id 
        WHERE
        office_members.employee_id NOT IN ( SELECT employee_id FROM ympipantry.pantry_logs WHERE date( in_time ) = '".$date."' ) 
        AND office_members.remark = 'office' 
        AND office_members.employee_id LIKE 'PI%' 
        AND employee_syncs.employee_id IS NOT NULL";

        $hourly = db::connection('pantry')->select($query);
        $total = db::connection('pantry')->select($query2);
        $duration = db::connection('pantry')->select($query3);
        $novisit = db::select($query4);

        $response = array(
            'status' => true,
            'hourly' => $hourly,
            'total' => $total,
            'duration' => $duration,
            'novisit' => $novisit

        );
        return Response::json($response);
    }

    public function fetchMenu(Request $request){

        $pemesan = Auth::user()->username;

        $pantry = PantryMenu::find($request->get("id"));
        $menu = $pantry->menu;
// if ($request->get('id') == 1) {
//     $menu = "Tea";
// } else if ($request->get('id') == 2) {
//     $menu = "Coffee";
// } else if ($request->get('id') == 3) {
//     $menu = "Oca";
// } else if ($request->get('id') == 4) {
//     $menu = "Water";
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
                'informasi' => $request->get('informasi'),
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
                'message' => 'Pilih Item Terlebih Dahulu <br>注文品を予め選択してください',
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
            $emp = User::where('username','=',$request->get("pemesan"))->first();
            $name = $emp->name;

            PantryOrder::where('pemesan', '=', $request->get("pemesan"))->update([
                'status' => 'confirmed'
            ]);
// PantryOrder::find($request->get("pemesan"));
// $pantry->status = 'confirmed';
// $pantry->save();

            $query_string = "api.aspx?apiusername=API3Y9RTZ5R6Y&apipassword=API3Y9RTZ5R6Y3Y9RT";
            $query_string .= "&senderid=".rawurlencode("PT YMPI")."&mobileno=".rawurlencode("62811372398");
            $query_string .= "&message=".rawurlencode(stripslashes("Ada Pesanan Pantry Dari ".$name.", Mohon untuk segera dibuatkan. Terimakasih")) . "&languagetype=1";        
            $url = "http://gateway.onewaysms.co.id:10002/".$query_string;       
            $fd = @implode('', file($url));

// $sms = gw_send_sms('API3Y9RTZ5R6Y','API3Y9RTZ5R6Y3Y9RT','YMPI','6285645896741','Terdapat Order Pantry');

            $response = array(
                'status' => true,
                'message' => 'Pesanan Berhasil Dikonfirmasi'
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
        ))->with('page', 'Pantry Menu')->with('head', 'Pantry');
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
        ->select('name','minuman','informasi','keterangan','gula','jumlah','tempat','status')
        ->join('users','users.username','=','pantry_orders.pemesan')
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

    public function getPesanan()
    {
        $detailpesanan = DB::table('pantry_orders')
        ->select('name','minuman','informasi','keterangan','gula','jumlah','tempat','status')
        ->join('users','users.username','=','pantry_orders.pemesan')
        ->where('pemesan','=',Auth::user()->username)
        ->orWhere(function ($query) {
            $query->where('status', '=', 'proses')
            ->where('status', '=', 'confirmed');
        })
        ->whereNull('pantry_orders.deleted_at');

        $detailpesanan = $detailpesanan->orderBy('pantry_orders.id', 'DESC');
        $pesanan = $detailpesanan->get();

        $response = array(
            'status' => true,
            'pesanan' => $pesanan
        );
        return Response::json($response);
    }

    public function daftarkonfirmasi(){
        $title = 'Pantry Order Confirmation';
        $title_jp = '注文内容確認';

        return view('pantry.pesanan_konfirmasi', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'Pesanan Pantry');
    }

    function filterkonfirmasi(Request $request)
    {
        $detailpesanan = DB::table('pantry_orders')
        ->select('pantry_orders.id','name','minuman','informasi','keterangan','gula','jumlah','tempat','status')
        ->join('users','users.username','=','pantry_orders.pemesan')
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
                'informasi' => $pantry->informasi,
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