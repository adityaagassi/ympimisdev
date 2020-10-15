<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\GeneralAttendance;
use App\GeneralAttendanceLog;
use App\Employee;
use App\EmployeeSync;
use App\GeneralTransportation;
use App\GeneralDoctor;
use App\CodeGenerator;
use App\GeneralShoesLog;
use App\GeneralShoesRequest;
use App\GeneralShoesStock;
use Auth;
use Response;

class GeneralController extends Controller{

	public function __construct(){
		$this->middleware('auth');

		$this->printers = [
			'Barcode Printer Sax',
			'Barrel-Printer',
			'FLO Printer 101',
			'FLO Printer 102',
			'FLO Printer 103',
			'FLO Printer 104',
			'FLO Printer 105',
			'FLO Printer LOG',
			'FLO Printer RC',
			'FLO Printer VN',
			'KDO ZPRO',
			'MIS',
			'MIS2',
			'Stockroom-Printer',
			'Welding-Printer'
		];

	}

	public function indexSafetyShoes(){
		$title = "Safety Shoes Control";
		$title_jp = "安全靴管理システム";

		$employees = EmployeeSync::orderBy('name', 'asc')->get();

		return view('general.safety_shoes.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees,
			'printers' => $this->printers
		));
	}

	public function indexMosaic(){
		$title = "Yamaha Day Mosaic Art Project";
		$title_jp = "";

		$mosaics = db::select("SELECT COALESCE
			( employee_syncs.department, 'Management' ) AS department,
			count( DISTINCT general_mosaics.employee_id ) AS count_person,
			count( general_mosaics.mosaic_id ) count_upload 
			FROM
			general_mosaics
			LEFT JOIN employee_syncs ON employee_syncs.employee_id = general_mosaics.employee_id 
			GROUP BY
			employee_syncs.department");

		return view('general.mosaic', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'mosaics' => $mosaics
		));
	}

	public function fetchMosaicDetail(){

	}

	public function indexReportSuratDokter(){
		$title = 'Laporan Dropbox Surat Dokter';
		$title_jp = '診断書のドロップボックス';

		return view('general.dropbox.surat_dokter_report', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Report Surat Dokter')
		->with('head', 'HR Report');
	}

	public function confirmSuratDokterReport(Request $request){
		try{

			$doctor = GeneralDoctor::where('id', '=', $request->get('id'))->first();

			if($request->get('status') == '1'){
				$doctor->remark = 1;
				$doctor->save();

				$response = array(
					'status' => true,
					'message' => 'Data berhasil dikonfirmasi'
				);
				return Response::json($response);
			}
			else if($request->get('status') == '2'){
				$doctor->remark = $request->get('status');
				$doctor->save();

				$response = array(
					'status' => true,
					'message' => 'Data berhasil ditolak'
				);
				return Response::json($response);
			}
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}		
	}

	public function fetchReportSuratDokter(Request $request){
		$month = date('Y-m');
		if(strlen($request->get('month_from')) > 0){
			$month = date('Y-m', strtotime($request->get('month_from')));
		}

		$doctors = db::select('SELECT
			date( g.created_at ) AS tanggal_pengajuan,
			g.id,
			g.employee_id,
			e.`name`,
			e.`department`,
			e.`section`,
			g.doctor_name,
			g.diagnose,
			g.date_from,
			g.date_to,
			g.attachment_file,
			g.remark,
			g.created_by,
			e2.`name` AS created_name 
			FROM
			`general_doctors` g
			LEFT JOIN employee_syncs AS e ON g.employee_id = e.employee_id
			LEFT JOIN employee_syncs e2 ON e2.employee_id = g.created_by 
			WHERE
			date_format( g.created_at, "%Y-%m" ) = "'.$month.'"');

		$datas = array();

		foreach ($doctors as $doctor) {
			array_push($datas, 
				[
					"tanggal_pengajuan" => $doctor->tanggal_pengajuan,
					"id" => $doctor->id,
					"employee_id" => $doctor->employee_id,
					"name" => $doctor->name,
					"department" => $doctor->department,
					"section" => $doctor->section,
					"doctor_name" => $doctor->doctor_name,
					"diagnose" => $doctor->diagnose,
					"date_from" => $doctor->date_from,
					"date_to" => $doctor->date_to,
					"remark" => $doctor->remark,
					"created_by" => $doctor->created_by,
					"created_name" => $doctor->created_name,
					"attachment_file" => asset('files/surat_dokter/'.$doctor->attachment_file)
				]);

		}

		$response = array(
			'status' => true,
			'doctors' => $datas,
			'period' => date('F Y', strtotime($month))
		);
		return Response::json($response);		
	}

	public function indexSuratDokter(){
		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();

		$employees = EmployeeSync::whereNull('end_date')
		->select('employee_id', 'name');

		if(Auth::user()->role_code != 'MIS' && Auth::user()->role_code != 'S' && Auth::user()->role_code != 'HR'){
			$employees = $employees->where('department', '=', $employee->department);
		}

		$employees = $employees->orderBy('employee_id', 'ASC')->get();

		$title = 'Dropbox Report Surat Dokter';
		$title_jp = '';

		return view('general.dropbox.surat_dokter', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees,
		))->with('page', 'Surat Dokter');

	}

	public function fetchSuratDokter(Request $request){
		$first = date('Y-m-d', strtotime("-90 days"));

		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();
		$general_doctors = db::table('general_doctors')->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_doctors.employee_id')
		->where('general_doctors.created_by', '=', Auth::user()->username);

		if(Auth::user()->role_code != 'MIS' && Auth::user()->role_code != 'S' && Auth::user()->role_code != 'HR'){
			$general_doctors = $general_doctors->where('employee_syncs.department', '=', $employee->department);
		}

		if(strlen($request->get('date_from')) > 0){
			$general_doctors = $general_doctors->where(DB::raw('DATE_FORMAT(general_doctors.created_at, "%Y-%m-%d")'), '>=', date('Y-m-d', strtotime($request->get('date_from'))));
		}
		if(strlen($request->get('date_to')) > 0){
			$general_doctors = $general_doctors->where(DB::raw('DATE_FORMAT(general_doctors.created_at, "%Y-%m-%d")'), '<=', date('Y-m-d', strtotime($request->get('date_to'))));
		}
		if(strlen($request->get('date_from')) == 0 && strlen($request->get('date_from')) == 0){
			$general_doctors = $general_doctors->where(DB::raw('DATE_FORMAT(general_doctors.created_at, "%Y-%m-%d")'), '>=', $first);		
		}

		$general_doctors = $general_doctors->whereNull('general_doctors.deleted_at')
		->select('general_doctors.id', 'general_doctors.employee_id', 'employee_syncs.name', 'general_doctors.doctor_name', 'general_doctors.diagnose', 'general_doctors.date_from', 'general_doctors.date_to', 'general_doctors.attachment_file', 'remark', 'created_by', db::raw('date(general_doctors.created_at) as created_at'))->get();

		$datas = array();

		foreach ($general_doctors as $general_doctor) {
			array_push($datas, 
				[
					"id" => $general_doctor->id,
					"employee_id" => $general_doctor->employee_id,
					"attachment_file" => asset('files/surat_dokter/'.$general_doctor->attachment_file),
					"name" => $general_doctor->name,
					"doctor_name" => $general_doctor->doctor_name,
					"diagnose" => $general_doctor->diagnose,
					"date_from" => $general_doctor->date_from,
					"date_to" => $general_doctor->date_to,
					"remark" => $general_doctor->remark,
					"created_by" => $general_doctor->created_by,
					"created_at" => $general_doctor->created_at
				]);
		}

		$response = array(
			'status' => true,
			'general_doctors' => $datas
		);
		return Response::json($response);
	}

	public function deleteSuratDokter(Request $request){
		try{
			$general_doctor = GeneralDoctor::where('id', '=', $request->get('id'))->forceDelete();	

			$response = array(
				'status' => true,
				'message' => 'Data berhasil dihapus'
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

	public function inputReceiveSafetyShoes(Request $request){
		$msg = $request->get('msg');
		$request_id = $request->get('request_id');

		if($msg == 'receive'){

			DB::beginTransaction();
			$request = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
			->where('general_shoes_requests.request_id', $request_id)
			->select(
				'employee_syncs.gender',
				'general_shoes_requests.size',
				'general_shoes_requests.employee_id',
				'general_shoes_requests.created_by',
				'employee_syncs.name',
				'employee_syncs.department',
				'employee_syncs.section',
				'employee_syncs.group',
				'employee_syncs.sub_group'
			)
			->get();

			for ($i=0; $i < count($request); $i++) {
				try {
					$log = new GeneralShoesLog([
						'gender' => $request[$i]['gender'],
						'size' => $request[$i]['size'],
						'quantity' => -1,
						'status' => 'Pinjam',
						'employee_id' => $request[$i]['employee_id'],
						'name' => $request[$i]['name'],
						'department' => $request[$i]['department'],
						'section' => $request[$i]['section'],
						'group' => $request[$i]['group'],
						'sub_group' => $request[$i]['sub_group'],
						'requested_by' => $request[$i]['created_by'],
						'created_by' => Auth::id()
					]);
					$log->save();


					$stock = GeneralShoesStock::where('gender', $request[$i]['gender'])
					->where('size', $request[$i]['size'])
					->first();

					$stock->quantity = $stock->quantity - 1;
					$stock->save();


				} catch (Exception $e) {
					DB::rollback();
					$response = array(
						'status' => false,
						'message' => $e->getMessage()
					);
					return Response::json($response);
				}			
			}

			try {
				$request = GeneralShoesRequest::where('request_id', $request_id)->forceDelete();
			} catch (Exception $e) {
				DB::rollback();
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}

			DB::commit();
			$response = array(
				'status' => true,
				'message' => 'Request Safety Shoes berhasil diterima'
			);
			return Response::json($response);


		}else{
			DB::beginTransaction();
			$request = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
			->where('general_shoes_requests.request_id', $request_id)
			->select('employee_syncs.gender', 'general_shoes_requests.size', db::raw('COUNT(general_shoes_requests.id) AS qty'))
			->groupBy('employee_syncs.gender', 'general_shoes_requests.size')
			->get();

			for ($i=0; $i < count($request); $i++) {
				$stock = GeneralShoesStock::where('gender', $request[$i]['gender'])
				->where('size', $request[$i]['size'])
				->first();

				$stock->temp_stock = $stock->temp_stock + $request[$i]['qty'];
				
				try {
					$stock->save();
				} catch (Exception $e) {
					DB::rollback();
					$response = array(
						'status' => false,
						'message' => $e->getMessage()
					);
					return Response::json($response);
				}
			}

			try {
				$request = GeneralShoesRequest::where('request_id', $request_id)->forceDelete();
			} catch (Exception $e) {
				DB::rollback();
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}

			DB::commit();
			$response = array(
				'status' => true,
				'message' => 'Request Safety Shoes berhasil ditolak'
			);
			return Response::json($response);
		}
		
	}

	public function inputSafetyShoes(Request $request){
		$stock = $request->get('stock');

		DB::beginTransaction();
		for ($i=0; $i < count($stock); $i++) {
			try {
				if($stock[$i]['status'] == 'Simpan'){
					$shoes = GeneralShoesStock::where('gender',  $stock[$i]['gender'])
					->where('size',  $stock[$i]['size'])
					->first();

					if($shoes){
						$shoes->temp_stock = $shoes->temp_stock + $stock[$i]['qty'];
						$shoes->quantity = $shoes->quantity + $stock[$i]['qty'];
						$shoes->save();
					}else{
						$shoes = new GeneralShoesStock([
							'gender' => $stock[$i]['gender'],
							'size' => $stock[$i]['size'],
							'temp_stock' => $stock[$i]['qty'],
							'quantity' => $stock[$i]['qty'],
							'created_by' => Auth::id()
						]);
						$shoes->save();
					}
				}

				$emp = EmployeeSync::where('employee_id', $stock[$i]['employee_id'])->first();

				$log = new GeneralShoesLog([
					'gender' => $stock[$i]['gender'],
					'size' => $stock[$i]['size'],
					'quantity' => $stock[$i]['qty'],
					'status' => $stock[$i]['status'],
					'employee_id' => $emp->employee_id,
					'name' => $emp->name,
					'department' => $emp->department,
					'section' => $emp->section,
					'group' => $emp->group,
					'sub_group' => $emp->sub_group,
					'created_by' => Auth::id()
				]);
				$log->save();
			} catch (Exception $e) {
				DB::rollback();
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}
		}

		DB::commit();
		$response = array(
			'status' => true,
			'message' => 'Safety Shoes berhasil ditambahkan'
		);
		return Response::json($response);
	}

	public function inputReqSafetyShoes(Request $request){
		$employee = $request->get('employee');
		$printer = $request->get('printer');

		DB::beginTransaction();
		for ($i=0; $i < count($employee); $i++) {
			$stock = GeneralShoesStock::where('gender',  $employee[$i]['gender'])
			->where('size',  $employee[$i]['size'])
			->first();

			if($stock){
				if($stock->temp_stock >= 1){
					$stock->temp_stock = $stock->temp_stock - 1;
					$stock->save();
				}else{
					DB::rollback();
					$response = array(
						'status' => false,
						'message' => 'Ukuran Sepatu '.$employee[$i]['size'].' untuk '.$employee[$i]['gender'].' stock tidak cukup'
					);
					return Response::json($response);
				}
			}else{
				DB::rollback();
				$response = array(
					'status' => false,
					'message' => 'Ukuran Sepatu '.$employee[$i]['size'].' untuk '.$employee[$i]['gender'].' tidak ada'
				);
				return Response::json($response);
			}			
		}
		DB::commit();


		$prefix_now = 'REQ'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','general-request')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$request_id = $code_generator->prefix . $number;
		$code_generator->index = $code_generator->index+1;
		$code_generator->save();


		for ($i=0; $i < count($employee); $i++) {
			try{
				$request = new GeneralShoesRequest([
					'request_id' => $request_id,
					'employee_id' => $employee[$i]['employee_id'],
					'size' => $employee[$i]['size'],
					'created_by' => Auth::id()
				]);
				$request->save();

			} catch (Exception $e) {
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}
		}

		$data = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
		->leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
		->where('general_shoes_requests.request_id', $request_id)
		->select(
			'employee_syncs.gender',
			'general_shoes_requests.size',
			'users.name',
			db::raw('COUNT(general_shoes_requests.id) AS qty')
		)
		->groupBy(
			'employee_syncs.gender',
			'general_shoes_requests.size',
			'users.name'
		)
		->orderBy('employee_syncs.gender', 'ASC')
		->orderBy('general_shoes_requests.size', 'ASC')
		->get();

		$this->safetyShoesSlip($data, $request_id, $printer, 'Print');

		$response = array(
			'status' => true,
			'message' => 'Safety Shoes berhasil ditambahkan'
		);
		return Response::json($response);
	}

	public function reprintReqSafetyShoes(Request $request){
		$printer = $request->get('printer');
		$request_id = $request->get('request_id');


		$data = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
		->leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
		->where('general_shoes_requests.request_id', $request_id)
		->select(
			'employee_syncs.gender',
			'general_shoes_requests.size',
			'users.name',
			db::raw('COUNT(general_shoes_requests.id) AS qty')
		)
		->groupBy(
			'employee_syncs.gender',
			'general_shoes_requests.size',
			'users.name'
		)
		->orderBy('employee_syncs.gender', 'ASC')
		->orderBy('general_shoes_requests.size', 'ASC')
		->get();

		$this->safetyShoesSlip($data, $request_id, $printer, 'Reprint');

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function safetyShoesSlip($data, $request_id, $printer_name, $remark){


		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$printer->initialize();

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text(strtoupper(" safety shoes request \n"));
		$printer->initialize();

		$printer->feed(1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->qrCode($request_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->setTextSize(1, 1);
		$printer->text($request_id."\n");
		$printer->feed(1);


		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$total_qty = 0;
		for ($i=0; $i < count($data); $i++) {
			$gender = $this->writeString('('.$data[$i]->gender.')', 2, ' ');
			$size = $this->writeString($data[$i]->size, 2, ' ');
			$qty = $this->writeString($data[$i]->qty, 2, ' ');

			$printer->text($gender." Size ".$size." -> ".$qty. " Pasang");
			$printer->feed(1);

		}
		$printer->feed(2);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		if($remark == 'Reprint'){
			$printer->text(" Reprint "."\n");
		}
		$printer->textRaw($data[0]->name."\n");
		$printer->textRaw("(".date("d-M-Y H:i:s").")\n");
		$printer->feed(2);
		$printer->cut();
		$printer->close();
	}

	public function writeString($text, $maxLength, $char) {
		if ($maxLength > 0) {
			$textLength = 0;
			if ($text != null) {
				$textLength = strlen($text);
			}
			else {
				$text = "";
			}
			for ($i = 0; $i < ($maxLength - $textLength); $i++) {
				$text .= $char;
			}
		}
		return strtoupper($text);
	}

	public function inputSuratDokter(Request $request){
		$filename = "";
		$file_destination = 'files/surat_dokter';

		if (count($request->file('attachment')) > 0) {
			$file = $request->file('attachment');
			$filename = md5($request->input('employee_id').date('YmdHis')).'.'.$request->input('extension');
			$file->move($file_destination, $filename);
		}

		try{
			GeneralDoctor::create([
				'employee_id' => $request->input('employee_id'),
				'doctor_name' => $request->input('doctor_name'),
				'diagnose' => $request->input('diagnose'),
				'date_from' => date('Y-m-d', strtotime($request->input('date_from'))),
				'date_to' => date('Y-m-d', strtotime($request->input('date_to'))),
				'attachment_file' => $filename,
				'remark' => 0,
				'created_by' => Auth::user()->username
			]);

			$response = array(
				'status' => true,
				'message' => 'Data baru berhasil ditambahkan'
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

	public function indexReportTransportation(){
		$title = 'Online Attendace And Transportation Report';
		$title_jp = '出社・移動費のオンライン報告';

		return view('general.dropbox.online_transportation_report', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Report Attendances & Tsransportations')
		->with('head', 'HR Report');
	}

	public function indexOnlineTransportation(){
		$title = 'Online Attendace And Transportation Record';
		$title_jp = '';

		$employee = EmployeeSync::leftJoin('domiciles', 'domiciles.employee_id', '=', 'employee_syncs.employee_id')
		->select('employee_syncs.employee_id', 'employee_syncs.name', 'employee_syncs.grade_code', 'employee_syncs.department', 'employee_syncs.section', 'domiciles.domicile_address', 'employee_syncs.zona')
		->where('employee_syncs.employee_id', '=', Auth::user()->username)
		->first();

		if(Auth::user()->role_code != 'MIS' && Auth::user()->role_code != 'S' && substr($employee->grade_code, 1, 1) != 'L' && substr($employee->grade_code, 1, 1) != 'M'){
			return view('404');
		}

		return view('general.dropbox.online_transportation', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee' => $employee
		))->with('head', 'Online Attendance And Transportation');
	}

	public function deleteOnlineTransportation(Request $request){
		try{
			$general_transporation = GeneralTransportation::where('id', '=', $request->get('id'))->first();
			$general_transporation->forceDelete();

			$response = array(
				'status' => true,
				'message' => 'Data berhasil dihapus'
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

	public function inputOnlineTransportation(Request $request){
		$filename = "";
		$file_destination = 'files/general_transportation';

		if (count($request->file('newAttachment')) > 0) {
			$file = $request->file('newAttachment');
			$filename = md5($request->input('employee_id').date('YmdHis')).'.'.$request->input('extension');
			$file->move($file_destination, $filename);
		}

		try{
			GeneralTransportation::create([
				'employee_id' => $request->input('employee_id'),
				'grade' => $request->input('grade'),
				'zona' => $request->input('zona'),
				'check_date' => date('Y-m-d', strtotime($request->input('newDate'))),
				'check_time' => date('H:i:s', strtotime($request->input('newTime'))),
				'attend_code' => $request->input('newAttend'),
				'vehicle' => $request->input('newVehicle'),
				'vehicle_number' => $request->input('newVehicleNumber'),
				'highway_bill' => $request->input('newHighwayBill'),
				'highway_amount' => $request->input('newHighwayAmount'),
				'distance' => $request->input('newDistance'),
				'highway_attachment' => $filename,
				'remark' => 0,
				'created_by' => Auth::id()
			]);

			$response = array(
				'status' => true,
				'message' => 'Data baru berhasil ditambahkan'
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

	public function confirmOnlineTransportationReport(Request $request){
		try{
			$transportation = GeneralTransportation::where('id', '=', $request->get('id'))->first();
			$transportation->remark = 1;
			$transportation->save();

			$response = array(
				'status' => true,
				'message' => 'Data berhasil dikonfirmasi'
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

	public function fetchRequestSafetyShoes(){

		$request = db::select("SELECT request.request_id, request.created_at, u.`name`, SUM(request.l) AS l, SUM(request.p) AS p FROM
			(SELECT r.request_id, date(r.created_at) AS created_at, r.created_by, IF(e.gender = 'L',1,0) AS l, IF(e.gender = 'P',1,0) AS p FROM general_shoes_requests r
			LEFT JOIN employee_syncs e ON e.employee_id = r.employee_id) request
			LEFT JOIN users u ON u.id = request.created_by
			GROUP BY request.request_id, request.created_at, u.`name`
			ORDER BY request.request_id");

		$response = array(
			'status' => true,
			'request' => $request
		);
		return Response::json($response);

		
	}

	public function fetchSafetyShoes(){
		$data = GeneralShoesStock::get();
		$max = GeneralShoesStock::max('size');
		$min = GeneralShoesStock::min('size');

		$response = array(
			'status' => true,
			'data' => $data,
			'max' => $max,
			'min' => $min
		);
		return Response::json($response);
	}

	public function fetchOnlineTransportationResumeReport(Request $request){
		$month = date('Y-m');
		if(strlen($request->get('month_from')) > 0){
			$month = date('Y-m', strtotime($request->get('month_from')));
		}

		$transportations = db::select("SELECT
			A.employee_id,
			employee_syncs.NAME,
			A.zona,
			A.att_in,
			A.att_out,
			A.remark_in,
			A.remark_out,
			A.id_in,
			A.id_out,
			A.grade,
			A.check_date,
			A.check_in,
			A.check_out,
			A.attend_code,
			A.attend_count,
			A.vehicle,
			A.vehicle_number,
			A.highway_bill_in,
			A.highway_bill_out,
			A.highway_amount_in,
			A.highway_amount_out,
			A.distance_in,
			A.distance_out,
			A.highway_amount_total,
			A.distance_total,
			A.remark 
			FROM
			(
			SELECT
			attendance.zona,
			max( attendance.att_in ) AS att_in,
			max( attendance.att_out ) AS att_out,
			max( attendance.remark_in ) AS remark_in,
			max( attendance.remark_out ) AS remark_out,
			max( attendance.id_in ) AS id_in,
			max( attendance.id_out ) AS id_out,
			attendance.employee_id,
			attendance.grade,
			attendance.check_date,
			max( attendance.check_in ) AS check_in,
			max( attendance.check_out ) AS check_out,
			attendance.attend_code,
			max( attendance.attend_count ) AS attend_count,
			max( attendance.vehicle ) AS vehicle,
			max( attendance.vehicle_number ) AS vehicle_number,
			max( attendance.highway_bill_in ) AS highway_bill_in,
			max( attendance.highway_bill_out ) AS highway_bill_out,
			max( attendance.highway_in ) AS highway_amount_in,
			max( attendance.highway_out ) AS highway_amount_out,
			max( attendance.distance_in ) AS distance_in,
			max( attendance.distance_out ) AS distance_out,
			max( attendance.highway_in ) + max( attendance.highway_out ) AS highway_amount_total,
			max( attendance.distance_in ) + max( attendance.distance_out ) AS distance_total,
			min( attendance.remark ) AS remark 
			FROM
			(
			SELECT
			zona,
			highway_attachment AS att_in,
			0 AS att_out,
			remark AS remark_in,
			0 AS remark_out,
			id AS id_in,
			0 AS id_out,
			employee_id,
			grade,
			check_date,
			check_time AS check_in,
			0 AS check_out,
			'hadir' AS attend_code,
			1 AS attend_count,
			vehicle,
			vehicle_number,
			highway_bill AS highway_bill_in,
			0 AS highway_bill_out,
			highway_amount AS highway_in,
			0 AS highway_out,
			distance AS distance_in,
			0 AS distance_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			DATE_FORMAT( check_date, '%Y-%m' ) = '".$month."'
			AND remark = 1 
			AND attend_code = 'in' UNION ALL
			SELECT
			zona,
			0 AS att_in,
			highway_attachment AS att_out,
			0 AS remark_in,
			remark AS remark_out,
			0 AS id_in,
			id AS id_out,
			employee_id,
			grade,
			check_date,
			0 AS check_in,
			check_time AS check_out,
			'hadir' AS attend_code,
			1 AS attend_count,
			vehicle,
			0 AS vehicle_number,
			0 AS highway_bill_in,
			highway_bill AS highway_bill_out,
			0 AS highway_in,
			highway_amount AS highway_out,
			0 AS distance_in,
			distance AS distance_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			DATE_FORMAT( check_date, '%Y-%m' ) = '".$month."'
			AND remark = 1  
			AND attend_code = 'out' UNION ALL
			SELECT
			zona,
			0 AS att_in,
			0 AS att_out,
			remark AS remark_in,
			0 AS remark_out,
			id AS id_in,
			0 AS id_out,
			employee_id,
			grade,
			check_date,
			0 AS check_in,
			0 AS check_out,
			attend_code,
			0 AS attend_count,
			0 AS vehicle,
			0 AS vehicle_number,
			0 AS highway_bill_in,
			0 AS highway_bill_out,
			0 AS highway_in,
			0 AS highway_out,
			0 AS distance_in,
			0 AS distance_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			DATE_FORMAT( check_date, '%Y-%m' ) = '".$month."'
			AND remark = 1 
			AND attend_code <> 'out' 
			AND attend_code <> 'in' 
			) AS attendance 
			GROUP BY
			zona,
			employee_id,
			grade,
			check_date,
			attend_code 
			) AS A
			LEFT JOIN employee_syncs ON A.employee_id = employee_syncs.employee_id 
			ORDER BY
			A.employee_id ASC,
			A.check_date ASC");

		$datas = array();

		foreach ($transportations as $transportation) {
			$fuel = 0;
			$divider = 0;
			$multiplier = 0;
			$grade = "";

			if($transportation->grade != null){
				$grade = $transportation->grade;
			}

			if(substr($grade, 1, 1) == 'M'){
				$divider = 5;
				$multiplier = 7650;
			}
			else if(substr($grade, 1, 1) == 'L'){
				$divider = 7;
				$multiplier = 7650;
			}
			else{
				$divider = $transportation->distance_total;
				$multiplier = 0;
			}

			if($transportation->vehicle == 'car'){
				if($transportation->distance_total <= 150){
					$fuel = ($transportation->distance_total/$divider)*$multiplier;
				}
				else{
					$fuel = (150/$divider)*$multiplier;						
				}
			}

			if($transportation->vehicle == 'other'){
				if($transportation->zona == '1'){
					$fuel = 11000;
				}
				else if($transportation->zona == '2'){
					$fuel = 12400;
				}
				else{
					$fuel = 17000;
				}
			}

			$total_amount = $fuel+$transportation->highway_amount_total;

			if(substr($grade, 1, 1) != 'M' || substr($grade, 1, 1) != 'L'){
				$total_amount = 0;
			}

			array_push($datas, 
				[
					"zona" => $transportation->zona,
					"att_in" => asset('files/general_transportation/'.$transportation->att_in),
					"att_out" => asset('files/general_transportation/'.$transportation->att_out),
					"remark_in" => $transportation->remark_in,
					"remark_out" => $transportation->remark_out,
					"id_in" => $transportation->id_in,
					"id_out" => $transportation->id_out,
					"employee_id" => $transportation->employee_id,
					"name" => $transportation->NAME,
					"grade" => $transportation->grade,
					"check_date" => $transportation->check_date,
					"check_in" => $transportation->check_in,
					"check_out" => $transportation->check_out,
					"attend_code" => $transportation->attend_code,
					"attend_count" => $transportation->attend_count,
					"vehicle" => $transportation->vehicle,
					"vehicle_number" => $transportation->vehicle_number,
					"highway_bill_in" => $transportation->highway_bill_in,
					"highway_bill_out" => $transportation->highway_bill_out,
					"highway_amount_in" => $transportation->highway_amount_in,
					"highway_amount_out" => $transportation->highway_amount_out,
					"distance_in" => $transportation->distance_in,
					"distance_out" => $transportation->distance_out,
					"highway_amount_total" => $transportation->highway_amount_total,
					"distance_total" => $transportation->distance_total,
					"remark" => $transportation->remark,
					"fuel" => $fuel,
					"total_amount" => $total_amount
				]);
		}

		$response = array(
			'status' => true,
			'transportations' => $datas,
		);
		return Response::json($response);

	}

	public function fetchOnlineTransportationReport(Request $request){
		$month = date('Y-m');
		if(strlen($request->get('month_from')) > 0){
			$month = date('Y-m', strtotime($request->get('month_from')));
		}

		$transportations = GeneralTransportation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_transportations.employee_id')
		->select('general_transportations.id', 'general_transportations.employee_id', 'employee_syncs.name', 'employee_syncs.grade_code', 'general_transportations.check_date', 'general_transportations.check_time', 'general_transportations.attend_code', 'general_transportations.vehicle', 'general_transportations.highway_bill', 'general_transportations.highway_amount', 'general_transportations.distance', 'general_transportations.highway_attachment')
		->where(db::raw('date_format(general_transportations.check_date, "%Y-%m")'), '=', $month)
		->where('general_transportations.remark', '=', 0)
		->get();

		$datas = array();

		foreach ($transportations as $transportation) {
			array_push($datas, 
				[
					"id" => $transportation->id,
					"employee_id" => $transportation->employee_id,
					"name" => $transportation->name,
					"grade_code" => $transportation->grade_code,
					"check_date" => $transportation->check_date,
					"check_time" => $transportation->check_time,
					"attend_code" => $transportation->attend_code,
					"vehicle" => $transportation->vehicle,
					"highway_bill" => $transportation->highway_bill,
					"highway_amount" => $transportation->highway_amount,
					"distance" => $transportation->distance,
					"highway_attachment" => asset('files/general_transportation/'.$transportation->highway_attachment)
				]);

		}

		$response = array(
			'status' => true,
			'transportations' => $datas,
			'period' => date('F Y', strtotime($month))
		);
		return Response::json($response);
	}

	public function fetchOnlineTransportation(Request $request){
		$date_from = date('Y-m-01');
		$date_to = date('Y-m-t');

		if(strlen($request->get('date_from')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('date_from')));
		}

		if(strlen($request->get('date_to')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('date_to')));
		}

		$transportations = db::select("
			SELECT
			calendar.week_date AS check_date,
			calendar.remark AS h,
			COALESCE ( attendance.zona, 0 ) AS zona,
			COALESCE ( attendance.att_in, 0 ) AS att_in,
			COALESCE ( attendance.att_out, 0 ) AS att_out,
			COALESCE ( attendance.remark_in, 0 ) AS remark_in,
			COALESCE ( attendance.remark_out, 0 ) AS remark_out,
			COALESCE ( attendance.id_in, 0 ) AS id_in,
			COALESCE ( attendance.id_out, 0 ) AS id_out,
			COALESCE ( attendance.employee_id, 0 ) AS employee_id,
			COALESCE ( attendance.grade, 0 ) AS grade,
			COALESCE ( attendance.check_in, 0 ) AS check_in,
			COALESCE ( attendance.check_out, 0 ) AS check_out,
			COALESCE ( attendance.attend_code, 0 ) AS attend_code,
			COALESCE ( attendance.vehicle, 0 ) AS vehicle,
			COALESCE ( attendance.vehicle_number, 0 ) AS vehicle_number,
			COALESCE ( attendance.highway_bill_in, 0 ) AS highway_bill_in,
			COALESCE ( attendance.highway_bill_out, 0 ) AS highway_bill_out,
			COALESCE ( attendance.highway_amount_in, 0 ) AS highway_amount_in,
			COALESCE ( attendance.highway_amount_out, 0 ) AS highway_amount_out,
			COALESCE ( attendance.distance_in, 0 ) AS distance_in,
			COALESCE ( attendance.distance_out, 0 ) AS distance_out,
			COALESCE ( attendance.highway_amount_total, 0 ) AS highway_amount_total,
			COALESCE ( attendance.distance_total, 0 ) AS distance_total,
			attendance.remark AS remark
			FROM
			( SELECT * FROM weekly_calendars WHERE week_date >= '".$date_from."' AND week_date <= '".$date_to."' ) AS calendar
			LEFT JOIN (
			SELECT
			zona,
			max( att_in ) AS att_in,
			max( att_out ) AS att_out,
			max( remark_in ) AS remark_in,
			max( remark_out ) AS remark_out,
			max( id_in ) AS id_in,
			max( id_out ) AS id_out,
			employee_id,
			grade,
			check_date,
			max( check_in ) AS check_in,
			max( check_out ) AS check_out,
			attend_code,
			max( vehicle ) AS vehicle,
			max( vehicle_number ) AS vehicle_number,
			max( highway_bill_in ) AS highway_bill_in,
			max( highway_bill_out ) AS highway_bill_out,
			max( highway_in ) AS highway_amount_in,
			max( highway_out ) AS highway_amount_out,
			max( distance_in ) AS distance_in,
			max( distance_out ) AS distance_out,
			max( highway_in ) + max( highway_out ) AS highway_amount_total,
			max( distance_in ) + max( distance_out ) AS distance_total,
			min( remark ) AS remark 
			FROM
			(
			SELECT
			zona,
			highway_attachment AS att_in,
			0 AS att_out,
			remark AS remark_in,
			0 AS remark_out,
			id AS id_in,
			0 AS id_out,
			employee_id,
			grade,
			check_date,
			check_time AS check_in,
			0 AS check_out,
			'hadir' AS attend_code,
			vehicle,
			vehicle_number,
			highway_bill AS highway_bill_in,
			0 AS highway_bill_out,
			highway_amount AS highway_in,
			0 AS highway_out,
			distance AS distance_in,
			0 AS distance_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			employee_id = '".Auth::user()->username."' 
			AND check_date >= '".$date_from."' 
			AND check_date <= '".$date_to."' 
			AND attend_code = 'in' UNION ALL
			SELECT
			zona,
			0 AS att_in,
			highway_attachment AS att_out,
			0 AS remark_in,
			remark AS remark_out,
			0 AS id_in,
			id AS id_out,
			employee_id,
			grade,
			check_date,
			0 AS check_in,
			check_time AS check_out,
			'hadir' AS attend_code,
			vehicle,
			0 AS vehicle_number,
			0 AS highway_bill_in,
			highway_bill AS highway_bill_out,
			0 AS highway_in,
			highway_amount AS highway_out,
			0 AS distance_in,
			distance AS distance_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			employee_id = '".Auth::user()->username."' 
			AND check_date >= '".$date_from."' 
			AND check_date <= '".$date_to."' 
			AND attend_code = 'out' UNION ALL
			SELECT
			zona,
			0 AS att_in,
			0 AS att_out,
			remark AS remark_in,
			0 AS remark_out,
			id AS id_in,
			0 AS id_out,
			employee_id,
			grade,
			check_date,
			0 AS check_in,
			0 AS check_out,
			attend_code,
			0 AS vehicle,
			0 AS vehicle_number,
			0 AS highway_bill_in,
			0 AS highway_bill_out,
			0 AS highway_in,
			0 AS highway_out,
			0 AS distance_in,
			0 AS distance_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			employee_id = '".Auth::user()->username."' 
			AND check_date >= '".$date_from."' 
			AND check_date <= '".$date_to."' 
			AND attend_code <> 'out' 
			AND attend_code <> 'in' 
			) AS A 
			GROUP BY
			zona,
			employee_id,
			grade,
			check_date,
			attend_code 
			) AS attendance ON calendar.week_date = attendance.check_date
			ORDER BY
			calendar.week_date ASC");

$datas = array();

foreach ($transportations as $transportation) {
	array_push($datas, 
		[
			"h" => $transportation->h,
			"zona" => $transportation->zona,
			"att_in" => asset('files/general_transportation/'.$transportation->att_in),
			"att_out" => asset('files/general_transportation/'.$transportation->att_out),
			"remark_in" => $transportation->remark_in,
			"remark_out" => $transportation->remark_out,
			"id_in" => $transportation->id_in,
			"id_out" => $transportation->id_out,
			"employee_id" => $transportation->employee_id,
			"grade" => $transportation->grade,
			"check_date" => $transportation->check_date,
			"check_in" => $transportation->check_in,
			"check_out" => $transportation->check_out,
			"attend_code" => $transportation->attend_code,
			"vehicle" => $transportation->vehicle,
			"vehicle_number" => $transportation->vehicle_number,
			"highway_bill_in" => $transportation->highway_bill_in,
			"highway_bill_out" => $transportation->highway_bill_out,
			"highway_amount_in" => $transportation->highway_amount_in,
			"highway_amount_out" => $transportation->highway_amount_out,
			"distance_in" => $transportation->distance_in,
			"distance_out" => $transportation->distance_out,
			"highway_amount_total" => $transportation->highway_amount_total,
			"distance_total" => $transportation->distance_total,
			"remark" => $transportation->remark
		]);

}

$response = array(
	'status' => true,
	'transportations' => $datas
);
return Response::json($response);
}

public function indexOmiVisitor(){
	$title = 'Koyami Visitor';
	$title_jp = '売店来客';

	return view('general.omi_visitor', array(
		'title' => $title,
		'title_jp' => $title_jp
	))->with('head', 'Koyami Visitor');
}

public function fetchOmiVisitor(){
	$visitors = db::connection('rfid')->table('omi_lists')->get();

	$response = array(
		'status' => true,
		'visitors' => $visitors
	);
	return Response::json($response);
}

public function indexGeneralPointingCall($id){
	if($id == 'japanese'){
		$title = 'Japanese Pointing Calls';
		$title_jp = '駐在員指差し呼称';

		return view('general.pointing_call.japanese', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'default_language' => 'jp',
			'location' => $id
		))->with('head', 'Pointing Calls');
	}
}

public function editGeneralPointingCallPic(Request $request){

	$pics = db::table('pointing_calls')
	->where('location', '=', $request->get('location'))
	->where('point_title', '=', $request->get('point_title'))
	->update([
		'remark' => 0
	]);

	$pic = db::table('pointing_calls')
	->where('id', '=', $request->get('id'))
	->update([
		'remark' => 1
	]);

	$response = array(
		'status' => true
	);
	return Response::json($response);


}

public function fetchGeneralPointingCall(Request $request){
	$pics = db::table('pointing_calls')
	->where('location', '=', $request->get('location'))
	->where('point_title', '=', 'pic')
	->whereNull('deleted_at')
	->get();

	$pointing_calls = db::table('pointing_calls')
	->where('location', '=', $request->get('location'))
	->where('point_title', '<>', 'pic')
	->where('point_title', '<>', 'safety_riding')
	->where('remark', '1')
	->whereNull('deleted_at')
	->get();

	$response = array(
		'status' => true,
		'pointing_calls' => $pointing_calls,
		'pics' => $pics
	);
	return Response::json($response);
}

public function indexGeneralAttendanceCheck(){
	$title = "Attendance Check";
	$title_jp = "";

	$purposes = GeneralAttendance::orderBy('purpose_code', 'asc')
	->select('purpose_code')
	->distinct()
	->get();

	return view('general.attendance_check', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'purposes' => $purposes
	))->with('head', 'GA Control')->with('page', 'Driver Control');
}

public function scanSafetyShoes(Request $request){

	$request_id = $request->get('request_id');

	$data = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
	->leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
	->where('general_shoes_requests.request_id', $request_id)
	->select(
		'employee_syncs.gender',
		'general_shoes_requests.size',
		'users.name',
		db::raw('COUNT(general_shoes_requests.id) AS qty')
	)
	->groupBy(
		'employee_syncs.gender',
		'general_shoes_requests.size',
		'users.name'
	)
	->orderBy('employee_syncs.gender', 'ASC')
	->orderBy('general_shoes_requests.size', 'ASC')
	->get();


	$response = array(
		'status' => true,
		'data' => $data
	);
	return Response::json($response);
}

public function scanGeneralAttendanceCheck(Request $request){
	$employee = Employee::where('tag', '=', $request->get('tag'))->first();

	if($employee == ""){
		$response = array(
			'status' => false,
			'message' => 'Tag karyawan tidak terdaftar, hubungi bagian MIS.'
		);
		return Response::json($response);
	}

	$attendance = GeneralAttendance::where('employee_id', '=', $employee->employee_id)
	->where('purpose_code', '=', $request->get('purpose_code'))
	->where('due_date', '=', date('Y-m-d'))
	->first();

	if($attendance == "" || $attendance->due_date > date('Y-m-d')){
		$response = array(
			'status' => false,
			'message' => 'Karyawan tidak ada pada schedule.'
		);
		return Response::json($response);
	}

	if($attendance->attend_date != null){
		$response = array(
			'status' => false,
			'message' => 'Karyawan sudah menghadiri schedule.'
		);
		return Response::json($response);
	}

	try{
		$attendance->attend_date = date('Y-m-d H:i:s');
		$attendance->save();

		$response = array(
			'status' => true,
			'message' => $employee->name.' berhasil hadir.'
		);
		return Response::json($response);

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
		'message' => 'Berhasil'
	);
	return Response::json($response);

}

public function fetchGeneralAttendanceCheck(Request $request){

	if(strlen($request->get('purpose_code')) == 0){
		$response = array(
			'status' => false,
			'message' => 'Silahkan memilih kode purpose'
		);
		return Response::json($response);
	}

	try{
		$now = date('Y-m-d');
			// $now = '2020-08-19';

		$query = "SELECT DISTINCT
		purpose_code,
		employee_id,
		due_date,
		NAME,
		departments.department_shortname AS department,
		attend_date 
		FROM
		(
		SELECT
		general_attendances.purpose_code,
		general_attendances.employee_id,
		general_attendances.due_date,
		employee_syncs.`name`,
		employee_syncs.department,
		DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
		FROM
		general_attendances
		LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
		WHERE
		general_attendances.due_date = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."' UNION ALL
		SELECT
		general_attendances.purpose_code,
		general_attendances.employee_id,
		general_attendances.due_date,
		employee_syncs.`name`,
		employee_syncs.department,
		DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
		FROM
		general_attendances
		LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
		WHERE
		DATE( general_attendances.attend_date ) = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."'
		) AS attendances 
		LEFT JOIN
		departments on departments.department_name = attendances.department
		WHERE employee_id like 'PI%'
		ORDER BY
		attend_date DESC,
		NAME ASC";

		$attendance_lists = db::select($query); 

		$response = array(
			'status' => true,
			'attendance_lists' => $attendance_lists
		);
		return Response::json($response);

	}
	catch(\Exception $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}		
}
}
