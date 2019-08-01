<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\PnPainica;
use App\PnCodePainica;
use App\PnOperator;
use App\HeaderBensuki;
use App\DetailBensuki;
use App\Incoming;
use App\PnCodeOperator;
use Response;
use App\PnInventorie;
use App\PnLogProces;
use App\PnLogNg;
use DataTables;
use Carbon\Carbon;


class Pianica extends Controller
{

    private $mesin;
    private $shift;
    public function __construct()
    {
      $this->mesin = [
          'H1',
          'H2',
          'H3',
          'M1',
          'M2',
          'M3',
      ];
      $this->shift = [
          'M',
          'B',
          'H',
      ];

      $this->model = [
          'P-25',
          'P-32',
          'P-37',
      ];

      $this->line = [
          '1',
          '2',
          '3',
          '4',
          '5',
      ];

      $this->bagian = [
          'bensuki',
          'benage',
          'pureto',
          'kensa awal',
          'kensa akhir',
          'kakuning visual',
      ];
  }

  public function index()
  {

    return view('pianica.index')->with('page', 'Bensuki');
}

public function bensuki()
{
    $mesins = $this->mesin;
    $shifts = $this->shift;
    $models = $this->model;

    $low ="select op.nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
    LEFT JOIN pn_code_operators as code
    on op.nik = code.nik
    where code.kode like '%LOW' and code.bagian='bensuki' ORDER BY code.kode asc";
    $lows = DB::select($low);

    $high ="select op.nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
    LEFT JOIN pn_code_operators as code
    on op.nik = code.nik
    where code.kode like '%HIGH' and code.bagian='bensuki' ORDER BY code.kode asc";
    $highs = DB::select($high);

    $middle ="select op.nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
    LEFT JOIN pn_code_operators as code
    on op.nik = code.nik
    where code.kode like '%MIDDLE' and code.bagian='bensuki' ORDER BY code.kode asc";
    $middles = DB::select($middle);

    $bennuki = "select op.nik, op.nama, code.kode from pn_operators as op
    LEFT JOIN pn_code_operators as code
    on op.nik = code.nik
    where code.bagian='bennuki'";
    $bennukis = DB::select($bennuki);

    return view('pianica.bensuki3',array(
        'shifts' => $shifts,
        'mesins' => $mesins,
        'models' => $models,
        'lows' => $lows,
        'low' => $lows,
        'highs' => $highs,
        'high' => $highs,
        'middles' => $middles,
        'middle' => $middles,
        'bennukis' => $bennukis,        
    ))->with('page', 'Bentsuki');
}


public function pureto()
{

    $models = $this->model;

    $low ="select op.nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
    LEFT JOIN pn_code_operators as code
    on op.nik = code.nik
    where code.kode like '%LOW' and code.bagian='bensuki' ORDER BY code.kode asc";
    $lows = DB::select($low);

    $high ="select op.nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
    LEFT JOIN pn_code_operators as code
    on op.nik = code.nik
    where code.kode like '%HIGH' and code.bagian='bensuki' ORDER BY code.kode asc";
    $highs = DB::select($high);

    $middle ="select op.nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
    LEFT JOIN pn_code_operators as code
    on op.nik = code.nik
    where code.kode like '%MIDDLE' and code.bagian='bensuki' ORDER BY code.kode asc";
    $middles = DB::select($middle);

    $bennuki = "select op.nik, op.nama, code.kode from pn_operators as op
    LEFT JOIN pn_code_operators as code
    on op.nik = code.nik
    where code.bagian='bennuki'";
    $bennukis = DB::select($bennuki);

    return view('pianica.pureto',array(        
        'models' => $models,
        'lows' => $lows,
        'low' => $lows,
        'highs' => $highs,
        'high' => $highs,
        'middles' => $middles,
        'middle' => $middles,
        'bennukis' => $bennukis,        
    ))->with('page', 'Pureto');
}

public function kensaawal()
{

    $models = $this->model;

    $query ="SELECT ng_name, id from ng_lists where location ='PN_Kensa_Awal' ORDER BY ng_name asc";

    $ng_list =DB::select($query);

    return view('pianica.kensaawal',array(        
        'ng_list' => $ng_list,
    ))->with('page', 'Kensa Awal');
}

public function kensaakhir()
{

    $models = $this->model;

    $query ="SELECT ng_name, id from ng_lists where location ='PN_Kensa_Akhir' ORDER BY ng_name asc";

    $ng_list =DB::select($query);

    return view('pianica.kensaakhir',array(        
        'ng_list' => $ng_list,
    ))->with('page', 'Kensa Akhir');
}


public function kakuningvisual()
{

    $models = $this->model;

    $query ="select ng_name, SUBSTRING_INDEX(location, 'Visual_', -1) as location, id from ng_lists where location like 'PN_Kakuning_Visual%' ";

    $ng_list =DB::select($query);

    return view('pianica.kakuningvisual',array(        
        'ng_list' => $ng_list,
    ))->with('page', 'Kakuning Visual');
}



///-------------- operator 
public function op()
{
    $lines = $this->line;
    $bagians = $this->bagian;
    return view('pianica.op',array(        
        'lines' => $lines,
        'bagians' => $bagians,
    ))->with('page', 'Operator');
}

public function fillop($value='')
{
    $op = "select * from pn_operators";
    $ops = DB::select($op);
    return DataTables::of($ops)

    ->addColumn('edit', function($ops){
        return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
    })
    ->addColumn('hapus', function($ops){
        return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="detailReport(id)" id="' . $ops->id . '">Delete</a>';
    })
    ->rawColumns(['edit' => 'edit', 'hapus'=>'hapus'])

    ->make(true);
}


public function editop(Request $request)
{
    $id_op = PnOperator::where('id', '=', $request->get('id'))->get();

    $response = array(
        'status' => true,
        'id_op' => $id_op,          
    );
    return Response::json($response);
}

public function updateop(Request $request){
    $id_user = Auth::id();
    
    try {  
        $op = PnOperator::where('id','=', $request->get('id'))       
        ->first(); 
        $op->tag = $request->get('tag');
        $op->nama = $request->get('nama');
        $op->nik = $request->get('nik');
        $op->line = $request->get('line');
        $op->bagian = $request->get('bagian');
        $op->created_by = $id_user;

        $op->save();

        $response = array(
          'status' => true,
          'message' => 'Update Success',
      );
        return redirect('/index/Op')->with('status', 'Update operator success')->with('page', 'Master Operator');
    }catch (QueryException $e){
        return redirect('/index/Op')->with('error', $e->getMessage())->with('page', 'Master Operator');
    }

}

public function addop(Request $request){
    $id_user = Auth::id();
    
    try { 

        $head = new PnOperator([
        'tag' => $request->get('tag'),
        'nama' => $request->get('nama'),
        'nik' => $request->get('nik'),
        'line' => $request->get('line'),
        'bagian' => $request->get('bagian'),
            'created_by' => $id_user
        ]);
        $head->save();



        $response = array(
          'status' => true,
          'message' => 'Add Operator Success',
      );
        return redirect('/index/Op')->with('status', 'Update operator success')->with('page', 'Master Operator');
    }catch (QueryException $e){
        return redirect('/index/Op')->with('error', $e->getMessage())->with('page', 'Master Operator');
    }

}



public function otokensa()
{
 return view('pianica.otokensa')->with('page', 'Otokensa');
}


//---------------------bensuki--------------

public function input(Request $request)
{
    $id_user = Auth::id();
    $id_head = HeaderBensuki::select('id')
    ->orderBy('id','desc')
    ->first();
    $id = $id_head->id+1;
    try {
        $head = new HeaderBensuki([
            'model' => $request->get('model'),
            'kode_op_bensuki' => $request->get('kodebensuki'),
            'nik_op_bensuki' => $request->get('nikbensuki'),
            'kode_op_plate' => $request->get('kodeplate'),
            'nik_op_plate' => $request->get('nikplate'),
            'shift' => $request->get('shift'),
            'mesin' =>  $request->get('mesin'),
            'line' =>  $request->get('line'),
            'created_by' => $id_user
        ]);
        $head->save();
        $ng = $request->get('ng');
        $rows = explode(",", $ng);
        foreach ($rows as $row) 
        {
            $ng2 = explode("-", $row);           
            $detail = new DetailBensuki([
                'id_bensuki' => $id,
                'ng' => $ng2[0],
                'posisi' =>  $ng2[1],           
                'created_by' => $id_user
            ]);
            $detail->save();            
        }
        $response = array(
            'status' => true,
            'invoice_number' => 'tes'
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

public function input2(Request $request)
{   
    $id_user = Auth::id();
    try {
        $incoming = new Incoming([
            'model' => $request->get('model'),
            'qty' => $request->get('qty'),
            'entry_date' => $request->get('entrydate'),
            'created_by' => $id_user
            
        ]);
        $incoming->save();

        $response = array(
            'status' => true,
            'invoice_number' => 'tes'
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

//--------------pureto -----------------------------

public function op_pureto(Request $request)
{  

    $op_pureto = PnOperator::where('tag', '=', $request->get('pureto'))
    ->where('bagian','=' ,$request->get('op'))
    ->select('nik', 'nama', 'tag')
    ->first();

    if($op_pureto == null){
        $response = array(
            'status' => false,
            'message' => 'Tag not registered',
        );
        return Response::json($response);
    }
    else{
        $response = array(
            'status' => true,
            'message' => 'RFID found',
            'nama' => $op_pureto->nama,
            'nik' => $op_pureto->nik,
        );
        return Response::json($response);
    }
}

public function savepureto(Request $request){
    $id_user = Auth::id();
    try {

       $inventori =  PnInventorie::updateOrCreate(
        [

            'tag' => $request->get('tag'),
            
        ],
        [
         'line' => $request->get('line'),
         'tag' => $request->get('tag'),
         'model' => $request->get('model'),
         'location' => $request->get('location'),
         'qty' => $request->get('qty'),
         'status' => '1',
         'created_by' => $request->get('pureto'),
     ]
 );


       $log = new PnLogProces([
        'line' => $request->get('line'),
        'operator' => $request->get('bensuki'),
        'tag' => $request->get('tag'),
        'model' => $request->get('model'),
        'location' => $request->get('location'),
        'qty' => $request->get('qty'),
        'created_by' => $request->get('pureto'),

    ]);

       $log->save();
       $inventori->save();

       $response = array(
        'status' => true,
        'message' => 'Input Success'
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

//------------ kensa awal ----------

public function savekensaawal(Request $request){
    $id_user = Auth::id();
    try {
        $inventori =  PnInventorie::updateOrCreate(
            [           
                'tag' => $request->get('tag'),            
            ],
            [
             'line' => $request->get('line'),
             'tag' => $request->get('tag'),
             'model' => $request->get('model'),
             'location' => $request->get('location'),
             'qty' => $request->get('qty'),
             'status' => '1',
             'created_by' => $request->get('op'),
         ]);

        $log = new PnLogProces([
            'line' => $request->get('line'),
            'operator' => $request->get('op'),
            'tag' => $request->get('tag'),
            'model' => $request->get('model'),
            'location' => $request->get('location'),
            'qty' => $request->get('qty'),
            'created_by' => $request->get('op'),
        ]);

        $ng = $request->get('ng');
        if($ng !=""){
            $rows = explode(",", $ng);
            foreach ($rows as $row) 
            {                          
                $detail = new PnLogNg([                
                    'ng' => $row,                
                    'line' => $request->get('line'),
                    'operator' => $request->get('op'),
                    'tag' => $request->get('tag'),
                    'model' => $request->get('model'),
                    'location' => $request->get('location'),
                    'qty' => $request->get('qty'),
                    'created_by' => $request->get('op'),
                ]);
                $detail->save();            
            }
        }

        $log->save();
        $inventori->save();

        $response = array(
            'status' => true,
            'message' => 'Input Success'
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

// delete kakuning 

public function deleteInv(Request $request)
{
    $op = PnInventorie::where('tag','=', $request->get('tag')); 
    $op->delete();
    $response = array(
            'status' => true,
            'message' => 'Delete Success'
        );
}

public function tag_model(Request $request)
{  

    $model = PnInventorie::where('tag', '=', $request->get('tag'))    
    ->select('model')
    ->first();

    if($model == null){
        $response = array(
            'status' => false,
            'message' => 'Tag not registered',
        );
        return Response::json($response);
    }
    else{
        $response = array(
            'status' => true,
            'message' => 'RFID found',
            'model' => $model->model,
            
        );
        return Response::json($response);
    }
}


public function total_ng(Request $request)
{  
    $date = date('Y-m-d');
    $query ="
    select sum(total) as total, ng_name from (
    select * from (
    select sum(jml) as total, ng_name from (
    select v.*, count(pn_log_ngs.ng) as jml from (  

    select b.id,ng_name, tag, model from (
    select * from ng_lists where location ='".$request->get('location')."'
    ) b
    CROSS join 
    (
    select * from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location ='".$request->get('location')."' and line = '".$request->get('line')."'
    ) f
    GROUP BY id,tag, ng_name    

    ) as v
    left join pn_log_ngs on ng = v.id and pn_log_ngs.tag = v.tag
    WHERE DATE_FORMAT(pn_log_ngs.created_at,'%Y-%m-%d')='".$date."'
    group by v.ng_name,v.tag,v.model,v.id

    ) d GROUP BY d.ng_name ORDER BY d.ng_name asc
    ) a
    union all
    select 0 as total,ng_name from ng_lists WHERE location ='".$request->get('location')."'
    ) d
    GROUP BY ng_name
    ";



    $query2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."' and line = '".$request->get('line')."'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."' and line = '".$request->get('line')."')  a";

    $total_ng =DB::select($query);
    $total =DB::select($query2);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',
        'model' => $total_ng,
        'total' => $total,

    );
    return Response::json($response);

}

public function total_ng_all(Request $request)
{  
    $date = date('Y-m-d');
    
    $query2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."' ) a";

    
    $total =DB::select($query2);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'total' => $total,

    );
    return Response::json($response);

}
public function GetNgBensuki(Request $request)
{
    $date = date('Y-m-d');
    
    $query2 ="select process_code as line, COALESCE(total,0) as total from ( 
SELECT COUNT(model) as total, line from header_bensukis
WHERE DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') ='".$date."' GROUP BY line
)
a
RIGHT JOIN  
(SELECT process_code from processes where remark ='pn') b
on a.line=b.process_code";

    
    $total =DB::select($query2);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'total' => $total,

    );
    return Response::json($response);
}

public function GetNgBensukiAll(Request $request)
{
    $date = date('Y-m-d');
    
    $query2 ="SELECT COUNT(model) as total from header_bensukis WHERE DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') ='".$date."' ";

     $query3 ="SELECT sum(total) as total, sum(total_ng) as ng from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Pureto'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Pureto' ) a";
    
    $total =DB::select($query2);
    $totalAll =DB::select($query3);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'total' => $total,
        'totalAll' => $totalAll,

    );
    return Response::json($response);
}

public function total_ng_all_line(Request $request)
{  
    $date = date('Y-m-d');
    
    $query2 =" SELECT COALESCE(total,0) as total, COALESCE(ng,0) as ng , process_code from ( SELECT sum(total) as total, sum(total_ng) as ng , line from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng , line from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."' GROUP BY tag,line
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng , line from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."' GROUP BY tag,line ) a GROUP BY line ORDER BY line asc ) a
RIGHT JOIN 
(SELECT process_code from processes where remark ='pn') b

on a.line=b.process_code  ORDER BY process_code asc
";

    
    $total =DB::select($query2);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'total' => $total,

    );
    return Response::json($response);

}


//--------------kensa akhir

public function savekensaakhir(Request $request){
    $id_user = Auth::id();
    try {        

        $ng = $request->get('ng');
        if($ng !=""){
            $rows = explode(",", $ng);
            foreach ($rows as $row) 
            {                          
                $detail = new PnLogNg([                
                    'ng' => $row,                
                    'line' => $request->get('line'),
                    'operator' => $request->get('op'),
                    'tag' => $request->get('tag'),
                    'model' => $request->get('model'),
                    'location' => $request->get('location'),
                    'qty' => $request->get('qty'),
                    'created_by' => $request->get('op'),
                ]);
                $detail->save(); 
                

            }

            $inventori =  PnInventorie::updateOrCreate(
                [           
                    'tag' => $request->get('tag'),            
                ],
                [
                 'line' => $request->get('line'),
                 'tag' => $request->get('tag'),
                 'model' => $request->get('model'),
                 'location' => $request->get('location'),
                 'qty' => $request->get('qty'),
                 'status' => '0',
                 'created_by' => $request->get('op'),
             ]);

            $log = new PnLogProces([
                'line' => $request->get('line'),
                'operator' => $request->get('op'),
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
            ]);
            $log->save();
            $inventori->save(); 

        }else{
            $inventori =  PnInventorie::updateOrCreate(
                [           
                    'tag' => $request->get('tag'),            
                ],
                [
                 'line' => $request->get('line'),
                 'tag' => $request->get('tag'),
                 'model' => $request->get('model'),
                 'location' => $request->get('location'),
                 'qty' => $request->get('qty'),
                 'status' => '1',
                 'created_by' => $request->get('op'),
             ]);

            $log = new PnLogProces([
                'line' => $request->get('line'),
                'operator' => $request->get('op'),
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
            ]);
            $log->save();
            $inventori->save(); 
        }



        $response = array(
            'status' => true,
            'message' => 'Input Success'
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


/// Laporan bensuki

 public function reportBensuki()
  {

    return view('pianica.reportBensuki')->with('page', 'Report Bentsuki');
}

public function getTotalNG(Request $request)
{


    $datep = $request->get('datep');

    if ($datep != "") {
    $date = $datep;
    // $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
    $date = date('Y-m-d');
    // $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }

      $query = "SELECT ngH,sum(totalH+totalL) as total from (
    SELECT ngH,count(b.ng) as totalL from (
    select ng_name as ngH from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='LOW' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."' ) b
    on a.ngH = b.ng 
    GROUP BY a.ngH ORDER BY ngH asc
    ) a
    LEFT JOIN (

    SELECT ngL,count(b.ng) as totalH from (
    select ng_name as ngL from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='HIGH' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."' ) b
    on a.ngL = b.ng 
    GROUP BY a.ngL ORDER BY ngL asc) b
    on a.ngH = b.ngL
    GROUP BY ngH";

$query2="SELECT ngH,count(b.ng) as totalL from (
select ng_name as ngH from ng_lists WHERE location='PN_Bensuki' 
)a
left JOIN  (
SELECT ng from detail_bensukis WHERE posisi='LOW' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."') b
on a.ngH = b.ng 
GROUP BY a.ngH ORDER BY ngH asc";

$query3="SELECT ngH,count(b.ng) as totalH from (
select ng_name as ngH from ng_lists WHERE location='PN_Bensuki' 
)a
left JOIN  (
SELECT ng from detail_bensukis WHERE posisi='HIGH' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."') b
on a.ngH = b.ng 
GROUP BY a.ngH ORDER BY ngH asc";

$tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from header_bensukis where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') = '".$date."'  ORDER BY created_at desc limit 1";

$tgl2 =DB::select($tgl);

$total =DB::select($query);

$totalL =DB::select($query2);
$totalH =DB::select($query3);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,
        'ngL' => $totalL,
        'ngH' => $totalH,
        'tgl' => $tgl2,

    );
    return Response::json($response);
}


public function getMesinNg(Request $request)
{

$datep = $request->get('datep');

    if ($datep != "") {
    $date = $datep;
    // $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
    $date = date('Y-m-d');
    // $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }
    $query="SELECT ng_name as mesin, COALESCE(ng,0) as ng from (
SELECT mesin, COUNT(ng) as ng  FROM header_bensukis
LEFT JOIN detail_bensukis ON  header_bensukis.ID = detail_bensukis.id_bensuki where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') = '".$date."'
GROUP BY mesin ORDER BY mesin asc )
a RIGHT JOIN (
SELECT ng_name from ng_lists WHERE location='PN_Bensuki_Mesin'
)b on a.mesin = b.ng_name ORDER BY mesin asc";
$tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from header_bensukis where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') = '".$date."'  ORDER BY created_at desc limit 1";

$tgl2 =DB::select($tgl);
    $total =DB::select($query);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,
        'tgl' => $tgl2,
        

    );
    return Response::json($response);
}

// ----------- display

 public function display()
  {

    return view('pianica.display')->with('page', 'Production Result');
}

public function getTarget(Request $request){
   
            $hpl = "where materials.category = 'FG' and materials.origin_group_code = '073'";
        

        $first = date('Y-m-01');
        if(date('Y-m-d') != date('Y-m-01')){
            $last = date('Y-m-d', strtotime(Carbon::yesterday()));
        }
        else{
            $last = date('Y-m-d');
        }
        $now = date('Y-m-d');

        if($first != $now){
            $debt = "union all

            select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
            (
            select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

            union all

            select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
            ) as debt
            group by material_number";
        }
        else{
            $debt= "";
        }
        

        $query = "select result.material_number, materials.material_description as model, sum(result.debt) as debt, sum(result.plan) as plan, sum(result.actual) as actual from
        (
        select material_number, 0 as debt, sum(quantity) as plan, 0 as actual 
        from production_schedules 
        where due_date = '". $now ."' 
        group by material_number

        union all

        select material_number, 0 as debt, 0 as plan, sum(quantity) as actual 
        from flo_details 
        where date(created_at) = '". $now ."'  
        group by material_number

        ".$debt."

        ) as result
        left join materials on materials.material_number = result.material_number
        ". $hpl ."
        group by result.material_number, materials.material_description
        having sum(result.debt) <> 0 or sum(result.plan) <> 0 or sum(result.actual) <> 0";

        $tableData = DB::select($query);


        $response = array(
            'status' => true,
            'target' => $tableData,
            
        );
        return Response::json($response);
    }


    /// Laporan Kensa Awal

 public function reportAwal()
  {

    return view('pianica.reportAwal')->with('page', 'Report Kensa Awal');
}

public function getKensaAwalALL(Request $request)
{
    $datep = $request->get('datep');

    if ($datep != "") {
    $date = $datep;
    $date2 = date('Y-m-d',strtotime($date));
    $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
    $date = date('Y-m-d');
    $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }

    $query="SELECT b.ng_name, COALESCE(a.total,0) total from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal' 
-- and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc";

 $querylas="SELECT b.ng_name, COALESCE(a.total,0) total from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal' 
-- and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc";

 $query2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' 
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' )  a";

    $querylas2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' 
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' )  a";


    $tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kensa_Awal' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'  ORDER BY created_at desc limit 1";

    $tgly = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kensa_Awal' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'  ORDER BY created_at desc limit 1";

    $tgl2 =DB::select($tgl);
    $tgl2y =DB::select($tgly);

    $total_ng =DB::select($query2);
    $total_nglas =DB::select($querylas2);

    $total =DB::select($query);
    $totallas =DB::select($querylas);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,
        'total' => $total_ng,

        'nglas' => $totallas,
        'totallas' => $total_nglas,
        'tgl' => $tgl2,
        'tgly' => $tgl2y,

        

    );
    return Response::json($response);
}

 /// Laporan Kensa Awal Line

 public function reportAwalLine()
  {

    return view('pianica.reportAwalLine')->with('page', 'Report Kensa Awal All Line');
}

public function getKensaAwalALLLine(Request $request)
{
     $datep = $request->get('datep');

    if ($datep != "") {
    $date = $datep;
    // $date2 = date('Y-m-d',strtotime($date));
    $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
    $date = date('Y-m-d');
    $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }

    $query="SELECT 1_5.*, b.total_5 from (
select 1_4.*, b.total_4 from(
SELECT 1_3.ng_name, 1_3.total_1, 1_3.total_2, b.total_3 from (
SELECT a.ng_name, a.total_1, b.total_2 from (
SELECT b.ng_name, COALESCE(a.total,0) total_1 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='1' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 ) a
left join (select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_2 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )c)b on a.ng_name = b.ng_name
 ) 1_3
 left join (select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_3 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='3' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_3.ng_name = b.ng_name
 ) 1_4 
 LEFT JOIN
 (
 select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_4 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='4' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_4.ng_name = b.ng_name
 ) 1_5 
 LEFT JOIN
 ( 
 select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_5 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='5' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_5.ng_name = b.ng_name
 ";

 $querylas="SELECT 1_5.*, b.total_5 from (
select 1_4.*, b.total_4 from(
SELECT 1_3.ng_name, 1_3.total_1, 1_3.total_2, b.total_3 from (
SELECT a.ng_name, a.total_1, b.total_2 from (
SELECT b.ng_name, COALESCE(a.total,0) total_1 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='1' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 ) a
left join (select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_2 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )c)b on a.ng_name = b.ng_name
 ) 1_3
 left join (select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_3 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='3' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_3.ng_name = b.ng_name
 ) 1_4 
 LEFT JOIN
 (
 select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_4 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='4' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_4.ng_name = b.ng_name
 ) 1_5 
 LEFT JOIN
 ( 
 select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_5 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='5' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_5.ng_name = b.ng_name
 ";

 $query2="SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='1'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='1' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='2'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='2' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='3'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='3' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='4'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='4' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='5'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='5' )  a";


 $query2las="SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='1'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='1' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='2'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='2' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='3'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='3' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='4'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='4' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='5'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='5' )  a";

    $tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' ORDER BY created_at desc limit 1";
    $tgl2 =DB::select($tgl);

    $total_ng =DB::select($query2);
    $total_nglas =DB::select($query2las);

    $total =DB::select($query);
    $totallas =DB::select($querylas);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,
        'total' => $total_ng,
        'nglas' => $totallas,
        'totallas' => $total_nglas,
         'tgl' => $tgl2,
        

    );
    return Response::json($response);
}


    /// Laporan Kensa Akhir

 public function reportAkhir()
  {

    return view('pianica.reportAkhir')->with('page', 'Report Kensa Akhir');
}

public function getKensaAkhirALL(Request $request)
{
    $datep = $request->get('datep');

    if ($datep != "") {
    $date = $datep;
    $date2 = date('Y-m-d',strtotime($date));
    $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
    $date = date('Y-m-d');
    $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }

    $query="SELECT b.ng_name, COALESCE(a.total,0) total from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir' 
-- and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc";

 $querylas="SELECT b.ng_name, COALESCE(a.total,0) total from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir' 
-- and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc";

 $query2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' 
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' )  a";

    $querylas2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' 
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' )  a";


    $tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces  where location='PN_Kensa_Akhir' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'   ORDER BY created_at desc limit 1";

     $tgly = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kensa_Akhir' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'  ORDER BY created_at desc limit 1";


    $tgl2 =DB::select($tgl);

    $tgl2 =DB::select($tgl);
    $tgl2y =DB::select($tgly);

    $total_ng =DB::select($query2);
    $total_nglas =DB::select($querylas2);

    $total =DB::select($query);
    $totallas =DB::select($querylas);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,
        'total' => $total_ng,

        'nglas' => $totallas,
        'totallas' => $total_nglas,
        'tgl' => $tgl2,
        'tgly' => $tgl2y,

        

    );
    return Response::json($response);
}

 /// Laporan Kensa Akhir Line

 public function reportAkhirLine()
  {

    return view('pianica.reportAkhirLine')->with('page', 'Report Kensa Akhir All Line');
}

public function getKensaAkhirALLLine(Request $request)
{
    $datep = $request->get('datep');

    if ($datep != "") {
    $date = $datep;
    $date2 = date('Y-m-d',strtotime($date));
    $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
    $date = date('Y-m-d');
    $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }

    $query="SELECT 1_5.*, b.total_5 from (
select 1_4.*, b.total_4 from(
SELECT 1_3.ng_name, 1_3.total_1, 1_3.total_2, b.total_3 from (
SELECT a.ng_name, a.total_1, b.total_2 from (
SELECT b.ng_name, COALESCE(a.total,0) total_1 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='1' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 ) a
left join (select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_2 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )c)b on a.ng_name = b.ng_name
 ) 1_3
 left join (select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_3 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='3' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_3.ng_name = b.ng_name
 ) 1_4 
 LEFT JOIN
 (
 select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_4 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='4' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_4.ng_name = b.ng_name
 ) 1_5 
 LEFT JOIN
 ( 
 select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_5 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='5' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_5.ng_name = b.ng_name
 ";

 $querylas="SELECT 1_5.*, b.total_5 from (
select 1_4.*, b.total_4 from(
SELECT 1_3.ng_name, 1_3.total_1, 1_3.total_2, b.total_3 from (
SELECT a.ng_name, a.total_1, b.total_2 from (
SELECT b.ng_name, COALESCE(a.total,0) total_1 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='1' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 ) a
left join (select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_2 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )c)b on a.ng_name = b.ng_name
 ) 1_3
 left join (select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_3 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='3' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_3.ng_name = b.ng_name
 ) 1_4 
 LEFT JOIN
 (
 select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_4 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='4' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_4.ng_name = b.ng_name
 ) 1_5 
 LEFT JOIN
 ( 
 select * from (
 SELECT b.ng_name, COALESCE(a.total,0) total_5 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='5' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
 on a.ng = b.id ORDER BY ng_name asc
 )a)b on 1_5.ng_name = b.ng_name
 ";

 $query2="SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='1'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='1' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='2'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='2' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='3'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='3' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='4'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='4' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='5'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='5' )  a";


 $query2las="SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='1'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='1' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='2'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='2' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='3'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='3' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='4'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='4' )  a
        
        union all
        
        SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='5'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='5' )  a";

    $tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kensa_Akhir' and DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."'  ORDER BY created_at desc limit 1";
    $tgl2 =DB::select($tgl);

    $total_ng =DB::select($query2);
    $total_nglas =DB::select($query2las);

    $total =DB::select($query);
    $totallas =DB::select($querylas);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,
        'total' => $total_ng,
        'nglas' => $totallas,
        'totallas' => $total_nglas,
         'tgl' => $tgl2,
        

    );
    return Response::json($response);
}


 /// Laporan Kensa Awal

 public function reportVisual()
  {

    return view('pianica.reportVisual')->with('page', 'Report Kakunin Visual');
}

public function getKensaVisualALL(Request $request)
{
    $datep = $request->get('datep');

    if ($datep != "") {
    $date = $datep;
    $date2 = date('Y-m-d',strtotime($date));
    $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
    $date = date('Y-m-d');
    $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }

    $query="SELECT SUM(total) as tot, location from (
select b.id, b.location, COALESCE(a.total,0) as total from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kakuning_Visual' and DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' GROUP BY ng
) a
RIGHT JOIN  
(
SELECT id,location from ng_lists WHERE location LIKE 'PN_Kakuning_Visual%'
)b
on a.ng = b.id
) c GROUP BY location
";

 $querylas="SELECT SUM(total) as tot, location from (
select b.id, b.location, COALESCE(a.total,0) as total from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kakuning_Visual' and DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' GROUP BY ng
) a
RIGHT JOIN  
(
SELECT id,location from ng_lists WHERE location LIKE 'PN_Kakuning_Visual%'
)b
on a.ng = b.id
) c GROUP BY location
";



    $tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces  where location='PN_Kakuning_Visual' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'   ORDER BY created_at desc limit 1";

     $tgly = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kakuning_Visual' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'  ORDER BY created_at desc limit 1";

    $tgl2 =DB::select($tgl);
    $tgl2y =DB::select($tgly);

  

    $total =DB::select($query);
    $totallas =DB::select($querylas);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,       
        'nglas' => $totallas,
        'tgl' => $tgl2,
        'tgly' => $tgl2y,

        

    );
    return Response::json($response);
}



/// Laporan Kensa Awal

 public function recordPianica()
  {

    return view('pianica.record')->with('page', 'Pianica Inventori');
}

public function recordPianica2(Request $request){
    $flo_detailsTable = DB::table('pn_log_proces')
    
    ->select('tag', 'model', 'qty', db::raw('date_format(updated_at, "%d-%b-%Y") as st_date'), DB::raw('(CASE WHEN location = "PN_Pureto" THEN "Pureto" WHEN location = "PN_Kensa_Awal" THEN "Kensa Awal" WHEN location = "PN_Kensa_Akhir" THEN "Kensa Akhir" ELSE "Kakunin Visual" END) AS location') );

    if(strlen($request->get('datefrom')) > 0){
        $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
        $flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), '>=', $date_from);
    }

    if(strlen($request->get('code')) > 0){
        $code = $request->get('code');
        $flo_detailsTable = $flo_detailsTable->where('location','=', $code );
    }

    if(strlen($request->get('dateto')) > 0){
        $date_to = date('Y-m-d', strtotime($request->get('dateto')));
        $flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), '<=', $date_to);
    }

    $stamp_detail = $flo_detailsTable->orderBy('updated_at', 'desc')->get();

    return DataTables::of($stamp_detail)->make(true);
}

//report monthly
public function reportDayAwal()
  {

    return view('pianica.reportAwalMonthly')->with('page', 'Monthly Report');
}


public function reportDayAwalData(Request $request){
    
    $from = $request->get('datefrom');
    $to = $request->get('dateto');

    $process = $request->get('code');   
    
    if ($from == "") {
        $from = date('Y-m-01');
    }
    if ($to == "") {      
       $to = date('Y-m-d');
    }

    $query = "SELECT biri.tgl, biri, oktaf, tinggi, rendah from (
SELECT week_date as tgl,COALESCE(a.total,0) as biri from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='Biri'
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl)
 
 Biri
 
 LEFT JOIN
 (
 
SELECT week_date as tgl,COALESCE(a.total,0) as oktaf from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='Oktaf'
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl 
 ) oktaf 
 on biri.tgl = oktaf.tgl
 
 LEFT JOIN
 (
 
SELECT week_date as tgl,COALESCE(a.total,0) as tinggi from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='T. Tinggi'
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl 
 ) tinggi 
 on biri.tgl = tinggi.tgl
 
 LEFT JOIN
 (
 
SELECT week_date as tgl,COALESCE(a.total,0) as rendah from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='T. Rendah'
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl 
 ) rendah
 on biri.tgl = rendah.tgl ";


 $query2=" SELECT 'visual' as pro,  tgl, COALESCE(frame,0) as frame, COALESCE(rl,0) as rl, COALESCE(lower,0) as lower, COALESCE(handle,0) as handle, COALESCE(button,0) as button, COALESCE(pianica,0) as pianica from (

SELECT 'visual' as pro,frame.tgl, frame,rl,lower,handle,button,pianica from (
SELECT week_date as tgl,sum(a.total) as frame from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date) frame
 
 LEFT JOIN 
 
 (
 SELECT week_date as tgl,sum(a.total) as rl from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) rl
 on frame.tgl = rl.tgl
 
 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as lower from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) lower
 on frame.tgl = lower.tgl
 
 
 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as handle from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Handle'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) handle
 on frame.tgl = handle.tgl
 
 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as button from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Button'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) button
 on frame.tgl = button.tgl
 
  LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as pianica from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Pianica'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) pianica
 on frame.tgl = pianica.tgl ) a 

 ";

 $query3="
 SELECT week_date, 
count(IF(ng ='Celah Lebar' and posisi ='low' ,1,null)) as CLebarL,
count(IF(ng ='Celah Lebar' and posisi ='high' ,1,null)) as CLebarH,
count(IF(ng ='Celah Sempit' and posisi ='low' ,1,null)) as CSempitL,
count(IF(ng ='Celah Sempit' and posisi ='high' ,1,null)) as CSempitH,
count(IF(ng ='Kepala Rusak' and posisi ='low' ,1,null)) as KRusakL,
count(IF(ng ='Kepala Rusak' and posisi ='high' ,1,null)) as KRusakH,
count(IF(ng ='Kotor' and posisi ='low' ,1,null)) as KotorL,
count(IF(ng ='Kotor' and posisi ='high' ,1,null)) as KotorH,
count(IF(ng ='Lekukan' and posisi ='low' ,1,null)) as LekukanL,
count(IF(ng ='Lekukan' and posisi ='high' ,1,null)) as LekukanH,
count(IF(ng ='Lengkung' and posisi ='low' ,1,null)) as LengkungL,
count(IF(ng ='Lengkung' and posisi ='high' ,1,null)) as LengkungH,
count(IF(ng ='Lepas' and posisi ='low' ,1,null)) as LepasL,
count(IF(ng ='Lepas' and posisi ='high' ,1,null)) as LepasH,
count(IF(ng ='Longgar' and posisi ='low' ,1,null)) as LonggarL,
count(IF(ng ='Longgar' and posisi ='high' ,1,null)) as LonggarH,
count(IF(ng ='Melekat' and posisi ='low' ,1,null)) as MelekatL,
count(IF(ng ='Melekat' and posisi ='high' ,1,null)) as MelekatH,
count(IF(ng ='Pangkal Menempel' and posisi ='low' ,1,null)) as PMenempelL,
count(IF(ng ='Pangkal Menempel' and posisi ='high' ,1,null)) as PMenempelH,
count(IF(ng ='Panjang' and posisi ='low' ,1,null)) as PanjangL,
count(IF(ng ='Panjang' and posisi ='high' ,1,null)) as PanjangH,
count(IF(ng ='Patah' and posisi ='low' ,1,null)) as PatahL,
count(IF(ng ='Patah' and posisi ='high' ,1,null)) as PatahH,
count(IF(ng ='Salah Posisi' and posisi ='low' ,1,null)) as SPosisiL,
count(IF(ng ='Salah Posisi' and posisi ='high' ,1,null)) as SPosisiH,
count(IF(ng ='Terbalik' and posisi ='low' ,1,null)) as TerbalikL,
count(IF(ng ='Terbalik' and posisi ='high' ,1,null)) as TerbalikH,
count(IF(ng ='Ujung Menempel' and posisi ='low' ,1,null)) as UMenempelL,
count(IF(ng ='Ujung Menempel' and posisi ='high' ,1,null)) as UMenempelH
from 
(SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."') s
left join (
SELECT ng, posisi, created_at FROM detail_bensukis) as d on DATE_FORMAT(d.created_at,'%Y-%m-%d') = s.week_date
group by week_date
 ";

 $query4="
 SELECT week_date, 
count(IF(ng ='Celah Lebar' ,1,null)) as CLebar,
count(IF(ng ='Celah Sempit' ,1,null)) as CSempit,
count(IF(ng ='Kepala Rusak' ,1,null)) as KRusak,
count(IF(ng ='Kotor' ,1,null)) as Kotor,
count(IF(ng ='Lekukan' ,1,null)) as Lekukan,
count(IF(ng ='Lengkung' ,1,null)) as Lengkung,
count(IF(ng ='Lepas' ,1,null)) as Lepas,
count(IF(ng ='Longgar' ,1,null)) as Longgar,
count(IF(ng ='Melekat' ,1,null)) as Melekat,
count(IF(ng ='Pangkal Menempel' ,1,null)) as PMenempel,
count(IF(ng ='Panjang' ,1,null)) as Panjang,
count(IF(ng ='Patah' ,1,null)) as Patah,
count(IF(ng ='Salah Posisi' ,1,null)) as SPosisi,
count(IF(ng ='Terbalik' ,1,null)) as Terbalik,
count(IF(ng ='Ujung Menempel',1,null)) as UMenempel
from 
(SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."') s
left join (
SELECT ng, posisi, created_at FROM detail_bensukis) as d on DATE_FORMAT(d.created_at,'%Y-%m-%d') = s.week_date
group by week_date
 ";

 if ( $process == "PN_Kensa_Akhir" || $process == "PN_Kensa_Awal") {
     $record =DB::select($query); 
    }else if($process == "PN_Kakuning_Visual"){
    $record =DB::select($query2); 
    }else{
    $record =DB::select($query3); 
    } 



    return DataTables::of($record)->make(true);
    
}


public function reportDayAwalDataGrafik(Request $request){
    
    $from = $request->get('datefrom');
    $to = $request->get('dateto');

    $process = $request->get('code');   
    
    if ($from == "") {
        $from = date('Y-m-01');
    }
    if ($to == "") {      
       $to = date('Y-m-d');
    }

    if ($process == "") {
         $process = 'PN_Kensa_Awal'; 
    }

    $query = "SELECT 'awal' as pro,biri.tgl, biri, oktaf, tinggi, rendah,COALESCE(target,0) target from (
SELECT week_date as tgl,COALESCE(a.total,0) as biri from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='Biri'
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl)
 
 Biri
 
 LEFT JOIN
 (
 
SELECT week_date as tgl,COALESCE(a.total,0) as oktaf from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='Oktaf'
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl 
 ) oktaf 
 on biri.tgl = oktaf.tgl
 
 LEFT JOIN
 (
 
SELECT week_date as tgl,COALESCE(a.total,0) as tinggi from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='T. Tinggi'
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl 
 ) tinggi 
 on biri.tgl = tinggi.tgl
 
 LEFT JOIN
 (
 
SELECT week_date as tgl,COALESCE(a.total,0) as rendah from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='T. Rendah'
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl 
 ) rendah
 on biri.tgl = rendah.tgl 

left join (
SELECT SUM(total) as target, due_date from (
SELECT material_number, due_date, sum(quantity) as total from production_schedules WHERE material_number in (
SELECT material_number from materials where materials.category = 'FG' and materials.origin_group_code = '073')
GROUP BY material_number, due_date
) a GROUP BY due_date
) target on biri.tgl = target.due_date
 ";

 $query2=" SELECT 'visual' as pro, tgl, COALESCE(frame,0) as frame, COALESCE(rl,0) as rl, COALESCE(lower,0) as lower, COALESCE(handle,0) as handle, COALESCE(button,0) as button, COALESCE(pianica,0) as pianica, COALESCE(target,0) target from (

SELECT 'visual' as pro,frame.tgl, frame,rl,lower,handle,button,pianica, target from (
SELECT week_date as tgl,sum(a.total) as frame from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date) frame
 
 LEFT JOIN 
 
 (
 SELECT week_date as tgl,sum(a.total) as rl from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) rl
 on frame.tgl = rl.tgl
 
 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as lower from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) lower
 on frame.tgl = lower.tgl
 
 
 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as handle from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Handle'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) handle
 on frame.tgl = handle.tgl
 
 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as button from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Button'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) button
 on frame.tgl = button.tgl
 
  LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as pianica from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Pianica'  
) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date
 
 ) pianica
 on frame.tgl = pianica.tgl  


left join (
SELECT SUM(total) as target, due_date from (
SELECT material_number, due_date, sum(quantity) as total from production_schedules WHERE material_number in (
SELECT material_number from materials where materials.category = 'FG' and materials.origin_group_code = '073')
GROUP BY material_number, due_date
) a GROUP BY due_date
) target on frame.tgl = target.due_date
) a ORDER BY a.tgl asc

 ";

 $query3 ="

 SELECT week_date as tgl, 
count(IF(ng ='Celah Lebar' and posisi ='low' ,1,null)) as CLebarL,
count(IF(ng ='Celah Lebar' and posisi ='high' ,1,null)) as CLebarH,
count(IF(ng ='Celah Sempit' and posisi ='low' ,1,null)) as CSempitL,
count(IF(ng ='Celah Sempit' and posisi ='high' ,1,null)) as CSempitH,
count(IF(ng ='Kepala Rusak' and posisi ='low' ,1,null)) as KRusakL,
count(IF(ng ='Kepala Rusak' and posisi ='high' ,1,null)) as KRusakH,
count(IF(ng ='Kotor' and posisi ='low' ,1,null)) as KotorL,
count(IF(ng ='Kotor' and posisi ='high' ,1,null)) as KotorH,
count(IF(ng ='Lekukan' and posisi ='low' ,1,null)) as LekukanL,
count(IF(ng ='Lekukan' and posisi ='high' ,1,null)) as LekukanH,
count(IF(ng ='Lengkung' and posisi ='low' ,1,null)) as LengkungL,
count(IF(ng ='Lengkung' and posisi ='high' ,1,null)) as LengkungH,
count(IF(ng ='Lepas' and posisi ='low' ,1,null)) as LepasL,
count(IF(ng ='Lepas' and posisi ='high' ,1,null)) as LepasH,
count(IF(ng ='Longgar' and posisi ='low' ,1,null)) as LonggarL,
count(IF(ng ='Longgar' and posisi ='high' ,1,null)) as LonggarH,
count(IF(ng ='Melekat' and posisi ='low' ,1,null)) as MelekatL,
count(IF(ng ='Melekat' and posisi ='high' ,1,null)) as MelekatH,
count(IF(ng ='Pangkal Menempel' and posisi ='low' ,1,null)) as PMenempelL,
count(IF(ng ='Pangkal Menempel' and posisi ='high' ,1,null)) as PMenempelH,
count(IF(ng ='Panjang' and posisi ='low' ,1,null)) as PanjangL,
count(IF(ng ='Panjang' and posisi ='high' ,1,null)) as PanjangH,
count(IF(ng ='Patah' and posisi ='low' ,1,null)) as PatahL,
count(IF(ng ='Patah' and posisi ='high' ,1,null)) as PatahH,
count(IF(ng ='Salah Posisi' and posisi ='low' ,1,null)) as SPosisiL,
count(IF(ng ='Salah Posisi' and posisi ='high' ,1,null)) as SPosisiH,
count(IF(ng ='Terbalik' and posisi ='low' ,1,null)) as TerbalikL,
count(IF(ng ='Terbalik' and posisi ='high' ,1,null)) as TerbalikH,
count(IF(ng ='Ujung Menempel' and posisi ='low' ,1,null)) as UMenempelL,
count(IF(ng ='Ujung Menempel' and posisi ='high' ,1,null)) as UMenempelH
from 
(SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."') s
left join (
SELECT ng, posisi, created_at FROM detail_bensukis) as d on DATE_FORMAT(d.created_at,'%Y-%m-%d') = s.week_date
group by week_date
 ";

 $query4="
 select a.*, COALESCE(target,0) target from (
 SELECT week_date as tgl, 
count(IF(ng ='Celah Lebar' ,1,null)) as CLebar,
count(IF(ng ='Celah Sempit' ,1,null)) as CSempit,
count(IF(ng ='Kepala Rusak' ,1,null)) as KRusak,
count(IF(ng ='Kotor' ,1,null)) as Kotor,
count(IF(ng ='Lekukan' ,1,null)) as Lekukan,
count(IF(ng ='Lengkung' ,1,null)) as Lengkung,
count(IF(ng ='Lepas' ,1,null)) as Lepas,
count(IF(ng ='Longgar' ,1,null)) as Longgar,
count(IF(ng ='Melekat' ,1,null)) as Melekat,
count(IF(ng ='Pangkal Menempel' ,1,null)) as PMenempel,
count(IF(ng ='Panjang' ,1,null)) as Panjang,
count(IF(ng ='Patah' ,1,null)) as Patah,
count(IF(ng ='Salah Posisi' ,1,null)) as SPosisi,
count(IF(ng ='Terbalik' ,1,null)) as Terbalik,
count(IF(ng ='Ujung Menempel',1,null)) as UMenempel
from 
(SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."') s
left join (
SELECT ng, posisi, created_at FROM detail_bensukis) as d on DATE_FORMAT(d.created_at,'%Y-%m-%d') = s.week_date
group by week_date ) a

left join (
SELECT SUM(total) as target, due_date from (
SELECT material_number, due_date, sum(quantity) as total from production_schedules WHERE material_number in (
SELECT material_number from materials where materials.category = 'FG' and materials.origin_group_code = '073')
GROUP BY material_number, due_date
) a GROUP BY due_date
) target on a.tgl = target.due_date  ORDER BY tgl asc

 ";


     if ( $process == "PN_Kensa_Akhir" || $process == "PN_Kensa_Awal") {
     $record =DB::select($query); 
    }else if($process == "PN_Kakuning_Visual"){
    $record =DB::select($query2); 
    }else{
    $record =DB::select($query4); 
    } 

    $response = array(
        'status' => true,
        'message' => 'NG Record found',  
        'record' => $record,       

    );
    return Response::json($response);
}

//detail chart


public function getKensaVisualALL2(Request $request)
{
    $date = date('Y-m-d', strtotime($request->get('tgl')));
    $ng = $request->get('ng');

    $query="
    SELECT ng_lists.ng_name as ng, COUNT(qty) as total from pn_log_ngs 
LEFT JOIN ng_lists on pn_log_ngs.ng = ng_lists.id
WHERE pn_log_ngs.location='PN_Kakuning_Visual' and ng_lists.location like '%_".$ng."' AND DATE_FORMAT(pn_log_ngs.created_at,'%Y-%m-%d')='".$date."'
GROUP BY ng_lists.ng_name
    ";

    $total =DB::select($query);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total, 
        'asd'=> $date,      
      

    );
    return Response::json($response);
}

public function getKensaBensuki2(Request $request)
{
    $date = date('Y-m-d', strtotime($request->get('tgl')));
    $ng = $request->get('ng');

    if ($ng =="Mesin 1") {
       $ng = "H1";
    }

    if ($ng =="Mesin 2") {
       $ng = "H2";
    }

    if ($ng =="Mesin 3") {
       $ng = "H3";
    }

    if ($ng =="Mesin 4") {
       $ng = "M1";
    }

    if ($ng =="Mesin 5") {
       $ng = "M2";
    }

    if ($ng =="Mesin 6") {
       $ng = "M3";
    }


    $query="    
    SELECT posisi,ng,COUNT(posisi) as total, GROUP_CONCAT(id_bensuki) as id from detail_bensukis where id_bensuki in (
    SELECT id from header_bensukis where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') = '".$date."' and mesin='".$ng."'
    ) GROUP BY ng,posisi ORDER BY posisi asc
    ";

    $total =DB::select($query);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,     
      

    );
    return Response::json($response);
}

public function getKensaBensuki3(Request $request)
{
    
    $ng = $request->get('id');


    $query=" 
    SELECT model,ben.nik as opbennik,ben.nama as opbennama, line, plate.nik as opplatenik, plate.nama as opplatenama,ben.shift from (
    SELECT header_bensukis.id,pn_operators.nik, pn_operators.nama, header_bensukis.line, model,IF(header_bensukis.shift='M','Shift 1','Shift 3') as shift from header_bensukis 
    LEFT JOIN pn_operators on header_bensukis.nik_op_bensuki = pn_operators.nik
    WHERE header_bensukis.id in (".$ng.") 
    )ben
    LEFT JOIN
    (
    SELECT header_bensukis.id,pn_operators.nik, pn_operators.nama from header_bensukis 
    LEFT JOIN pn_operators on header_bensukis.nik_op_plate = pn_operators.nik
    WHERE header_bensukis.id in (".$ng.") 
    ) plate 
    on ben.id = plate.id
    ";

    $total =DB::select($query);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,     
      

    );
    return Response::json($response);
}
///---------------------- master code op

public function opcode()
{
 $dataop = "SELECT nik,nama from pn_operators";
  $op = DB::select($dataop);
 return view('pianica.opcode', array(
    'op' => $op))->with('page', 'Master Code Operator');
}

public function fillopcode($value='')
{
    $op = "SELECT pn_code_operators.id,pn_code_operators.bagian,pn_code_operators.kode,pn_code_operators.nik,pn_operators.nama from pn_code_operators
        LEFT JOIN pn_operators on pn_code_operators.nik = pn_operators.nik order By pn_code_operators.id asc";
    $ops = DB::select($op);
    return DataTables::of($ops)

    ->addColumn('edit', function($ops){
        return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
    })
    ->addColumn('hapus', function($ops){
        return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="detailReport(id)" id="' . $ops->id . '">Delete</a>';
    })
    ->rawColumns(['edit' => 'edit', 'hapus'=>'hapus'])

    ->make(true);
}

public function editopcode(Request $request)
{
    $id = $request->get('id');
    $op = "SELECT pn_code_operators.id,pn_code_operators.bagian,pn_code_operators.kode,pn_code_operators.nik,pn_operators.nama from pn_code_operators
        LEFT JOIN pn_operators on pn_code_operators.nik = pn_operators.nik where pn_code_operators.id ='".$id."'";
    $id_op = DB::select($op);
    $response = array(
        'status' => true,
        'id_op' => $id_op, 
    );
    return Response::json($response);
}

public function updateopcode(Request $request){
    $id_user = Auth::id();
    
    try {  
        $op = PnCodeOperator::where('id','=', $request->get('id'))       
        ->first();         
        $op->nik = $request->get('op');        
        $op->created_by = $id_user;
        $op->save();
        $response = array(
          'status' => true,
          'message' => 'Update Success',
      );
        return redirect('/index/Op_Code')->with('status', 'Update operator success')->with('page', 'Master Operator');
    }catch (QueryException $e){
        return redirect('/index/Op_Code')->with('error', $e->getMessage())->with('page', 'Master Operator');
    }

}

}
