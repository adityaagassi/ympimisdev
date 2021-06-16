<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Exception;
use Carbon\Carbon;

use App\EmployeeSync;
use App\User;
use App\DataMaterials;
use App\LogDataMaterials;

use DataTables;
use DateTime;
use Response;
use PDF;
use Excel;

class StockAktualController extends Controller
{
    public function __construct(){
      $this->middleware('auth');     

        $this->reicive = [
            'MSCR',
            'WSCR'
        ];

        $this->category_reason = [
            'Material Jelek',
            'Material Salah'
        ];

        $this->category = [
            'PANTHOM',
            'NON PANTHOM'
        ];
    }

    public function IndexMonitoring(){
        $reason = db::select('SELECT reason, reason_name FROM scrap_reasons ORDER BY reason ASC');
        $storage_location = db::select('SELECT storage_location FROM storage_locations');
        // dd($storage_location);


        return view('stock.monitoring', array(
            'reason' => $reason,
            'title' => 'Monitoring Stock',
            'title_jp' => 'スクラップ材料',
            'storage_locations' => $storage_location,
            'category_reason' => $this->category_reason,
            'reicive' => $this->reicive,
            'category' => $this->category
        ))->with('page', 'Scrap');
    }

    public function ResumeStockAktual(Request $request){
        try {
            $loc = $request->get('loc');
            // $date_from = $request->get('date_from');
            // $date_to = $request->get('date_to');
            $filter = $request->get('filter');

            if ($filter != null && $filter == 'lebih_besar') {
                $ftr = "ROUND((Actual/Ideal)*100,2) < 100";
            }
            else if($filter != null && $filter == 'kurang_dari'){
                $ftr = "ROUND((Actual/Ideal)*100,2) > 100";
            } 
            // if ($date_from == "") {
            //      if ($date_to == "") {
            //           $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
            //           $last = "LAST_DAY(NOW())";
            //      }else{
            //           $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
            //           $last = "'".$date_to."'";
            //      }
            // }else{
            //      if ($date_to == "") {
            //           $first = "'".$date_from."'";
            //           $last = "LAST_DAY(NOW())";
            //      }else{
            //           $first = "'".$date_from."'";
            //           $last = "'".$date_to."'";
            //      }
            // }

            if (($filter) == null) {
            $resumes = DB::SELECT("select id, store, category, material_number, material_description, ideal, actual, location, ROUND((actual/ideal)*100,2) as bagi from data_materials WHERE location = '".$loc."' ORDER BY bagi DESC
                ");
            }
            else{
            $resumes = DB::SELECT("select id, store, category, material_number, material_description, ideal, actual, location, ROUND((actual/ideal)*100,2) as bagi from data_materials WHERE location = '".$loc."'
                AND ".$ftr." order by bagi desc, material_number asc");
            }

            $response = array(
                'status' => true,
                'resumes' => $resumes,
                // 'resumes1' => $resumes1
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

    public function FatchMonitoring(Request $request)
      {
          $today     = date("Y-m-d");
          $tahun = date('Y');
          $dateto = $request->get('dateto');
          $location = $request->get('loc');
          if ($dateto != "") {
            // $data = db::select("            
            // SELECT
            //     material_number,
            //     location,
            //     ideal as Ideal,
            //     actual as Actual
            // FROM
            //     data_materials
            // WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'");

            $data = db::select("
            SELECT
            material_number,
            location,
            ideal as Ideal,
            actual as Actual,
            ROUND((Actual/Ideal)*100,2) as bagi 
            FROM
            data_materials 
            WHERE location = '".$location."'
            and DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'
            and ROUND((Actual/Ideal)*100,2) < 100
            order by actual desc
            limit 10
            ");
            $data1 = db::select("
            SELECT
            material_number,
            location,
            ideal as Ideal,
            actual as Actual,
            ROUND((Actual/Ideal)*100,2) as bagi 
            FROM
            data_materials 
            WHERE location = '".$location."'
            and DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'
            and ROUND((Actual/Ideal)*100,2) > 100
            order by actual desc
            limit 10
            ");
          }else{
            $data = db::select("
            SELECT
            material_number,
            location,
            ideal as Ideal,
            actual as Actual,
            ROUND((Actual/Ideal)*100,2) as bagi 
            FROM
            data_materials 
            WHERE
            location = '".$location."'
            and ROUND((Actual/Ideal)*100,2) < 100
            order by bagi desc, material_number asc
            limit 10
            ");
            $data1 = db::select("
            SELECT
            material_number,
            location,
            ideal as Ideal,
            actual as Actual,
            ROUND((Actual/Ideal)*100,2) as bagi 
            FROM
            data_materials 
            WHERE
            location = '".$location."'
            and ROUND((Actual/Ideal)*100,2) > 100
            order by bagi desc, material_number asc
            limit 10
            ");
            // dd($data);
          }

          
          $response = array(
            'status' => true,
            'datas' => $data,
            'datas1' => $data1,
            'tahun' => $tahun,
            'dateto' => $dateto,
            'location' => $location
        );

          return Response::json($response); 
      }

      public function AuditStockAktualHome(){
        $reason = db::select('SELECT reason, reason_name FROM scrap_reasons ORDER BY reason ASC');
        $storage_location = db::select('SELECT storage_location FROM storage_locations');
        $store = db::select("SELECT DISTINCT store from `data_materials` ORDER BY store ASC");


        return view('stock.home', array(
            'reason' => $reason,
            'title' => 'Audit Stock Actual',
            'title_jp' => 'スクラップ材料',
            'storage_locations' => $storage_location,
            'stores' => $store
        ))->with('page', 'Scrap');
    }

    public function FatchListStock(Request $request){
        $loc = $request->get('loc');
        $store = $request->get('store');

        if ($store == null) {
            $lists = db::select("SELECT id, store, category, material_number, material_description, ideal, actual, location from `data_materials` where location = '".$loc."'");
        }else{
            $lists = db::select("SELECT id, store, category, material_number, material_description, ideal, actual, location from `data_materials` where location = '".$loc."' and store = '".$store."'");
        }
        

        $response = array(
            'status' => true,
            'lists' => $lists,
            'location' => $loc
        );
        return Response::json($response);
    }

    public function UpdateStock(Request $request){
        $id = Auth::id();
        
        try{
            $materials = DataMaterials::find($request->get('id'));
            $materials->actual = $request->get('quantity');
            $materials->remark = '1';
            $materials->created_by = $id;
            $materials->save();            
        }
        catch(\Exception $e){
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Stock Update Successful'
        );
        return Response::json($response);
    }

    public function UpdateStockResume(Request $request){
        
        $loc = $request->get('loc');
        $store = $request->get('store1');
        // if ($store == null) {
        //     $resumes = db::select("SELECT dm.store, dm.category, dm.material_number, dm.material_description, dm.actual, u.`name`, dm.updated_at FROM data_materials dm
        // LEFT JOIN users u on u.id = dm.created_by WHERE dm.location = '".$loc."' AND dm.remark = '1' ORDER BY dm.material_number");
        // }else{
        //     $resumes = db::select("SELECT dm.store, dm.category, dm.material_number, dm.material_description, dm.actual, u.`name`, dm.updated_at FROM data_materials dm
        // LEFT JOIN users u on u.id = dm.created_by WHERE dm.location = '".$loc."' AND dm.store = '".$store."' AND dm.remark = '1' ORDER BY dm.material_number");
        // }

        $resumes = db::select("SELECT dm.store, dm.category, dm.material_number, dm.material_description, dm.category, dm.ideal, dm.actual, u.`name`, dm.updated_at FROM data_materials dm
        LEFT JOIN users u on u.id = dm.created_by WHERE dm.location = '".$loc."' AND dm.remark = '1' ORDER BY dm.material_number");

        $response = array(
            'status' => true,
            'resumes' => $resumes,
            'location' => $loc
        );
        return Response::json($response);
    }

    public function UploadMasterIdeal()
    {
        $title = 'Upload Master Ideal';
        $title_jp = '??';

        $storage_location = db::select('SELECT storage_location FROM storage_locations');

        return view('stock.upload_master', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'storage_locations' => $storage_location
        ))->with('page', 'Upload Transaksi')
        ->with('head', 'Upload Transaksi');
    }

    public function ImportMasterIdeal(Request $request){
    if($request->hasFile('upload_file')) {
        try{                
            $file = $request->file('upload_file');
            $file_name = 'data_materials_'. date("ymd_h.i") .'.'.$file->getClientOriginalExtension();
            $file->move(public_path('insert/master_actual/'), $file_name);

            $excel = public_path('insert/master_actual/') . $file_name;

            $rows = Excel::load($excel, function($reader) {
                $reader->noHeading();
                $reader->skipRows(1);

                $reader->each(function($row) {
                });
            })->get();

            // $rows = $rows->toArray();

            for ($i=0; $i < count($rows); $i++) {


                    $master = DataMaterials::updateOrCreate([
                        'store' => $rows[$i][1],
                        'category' => $rows[$i][2],
                        'material_number' => $rows[$i][3],
                        'material_description' => $rows[$i][4],
                        'ideal' => $rows[$i][5],
                        'actual' => $rows[$i][6],
                        'location' => $rows[$i][7],
                        'remark' => null,
                        'print' => null,
                        'status' => null,
                        'quantity' => null,
                        'created_by' => Auth::id(),
                        'created_at' => date("Y-m-d H:i:s"),
                        'deleted_at' => null,
                        'updated_at' => null
                    ]);

                    $master->save();
                if ($rows[$i][0] != "") {

                    // var_dump($data2);

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

    public function DownloadMasterIdeal(Request $request){

        $time = date('d-m-Y H;i;s');
        $lokasi = $request->get('select_loc');
        $dm = DataMaterials::where('location', '=', $lokasi)->get();

        foreach ($dm as $key ) {
        $log_data_materials = new LogDataMaterials([
            'id_before' => $key->id,
            'store' => $key->store,
            'category' => $key->category,
            'material_number' => $key->material_number,
            'material_description' => $key->material_description,
            'ideal' => $key->ideal,
            'actual' => $key->actual,
            'location' => $key->location,
            'remark' => '2',
            'print' => $key->print,
            'status' => $key->status,
            'quantity' => $key->quantity,
            'created_by' => Auth::id(),
            'created_at' => date("Y-m-d H:i:s"),
            'deleted_at' => null,
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        $log_data_materials->save();
        $key->forceDelete();
        }

        $download = db::select("select id, store, category, material_number, material_description, ideal, actual, location, remark, print, status, quantity, created_by, created_at, deleted_at, updated_at from log_data_materials where location = '".$lokasi."' and remark = '2' order by id");

        $data = array(
            'download' => $download
        );

        ob_clean();

        Excel::create('data_materials '.$time, function($excel) use ($data){
            $excel->sheet('1', function($sheet) use ($data) {
              return $sheet->loadView('stock.download_stock', $data);
          });
        })->export('xlsx');
        return Response::json($response);
    }

}

