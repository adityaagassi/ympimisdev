<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\TagMaterial;
use App\MiddleInventory;
use App\BarrelQueue;
use App\Barrel;
use App\BarrelQueueInactive;
use App\BarrelLog;
use App\BarrelMachine;
use App\BarrelMachineLog;
use App\CodeGenerator;
use App\MiddleNgLog;
use App\MiddleLog;
use App\ErrorLog;
use App\Material;
use App\Employee;
use App\Mail\SendEmail;
use App\RfidBuffingInventory;
use Illuminate\Support\Facades\Mail;

class TrialController extends Controller
{

	public function trialmail(){
		$mail_to = db::table('send_emails')
		->where('remark', '=', 'upload')
		->WhereNull('deleted_at')
		->orWhere('remark', '=', 'superman')
		->WhereNull('deleted_at')
		->select('email')
		->get();

		Mail::raw('Hi, welcome user!', function ($message) {
			$message->to(['asd@gmail.com', '123@gmail.com'])->subject('tess');
		});
	}

	public function tes(){

		$title = 'Saxophone Buffing Work Order';
		$title_jp = 'asdasd';
		$json = file_get_contents('https://spreadsheets.google.com/feeds/cells/1X0747aH4wM_jUNcQIAyKjbkl4A4-bRaxv6R-pByDfK0/1/public/full?alt=json');
		$obj = json_decode($json, TRUE);

		return view('trial', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'dd' => $obj
		));
	}


	public function tes2()
	{
		$title = 'Emergency Conditions Response';
		$title_jp = '';
		$json = file_get_contents('https://spreadsheets.google.com/feeds/cells/1BqvAO5r-O0HFIR_fM13P84QRmN3OFnjzW9-4iENxOwU/1/public/full?alt=json');
		$obj = json_decode($json, TRUE);

		return view('trial', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'dd' => $obj
		));
	}

	public function fetch_data(Request $request)
	{
		$emp_q = "select count(employees.employee_id) as total, department from employees join
		(SELECT employee_id, department
		FROM mutation_logs
		WHERE id IN (
		SELECT MAX(id)
		FROM mutation_logs
		GROUP BY employee_id
		)) mut on mut.employee_id = employees.employee_id
		where end_date is null
		group by department
		";

		$emp_bagian = db::select("SELECT employee_id, department FROM mutation_logs
			WHERE id IN (
			SELECT MAX(id)
			FROM mutation_logs
			where employee_id in (".$request->get('nik').")
			GROUP BY employee_id
		)");

		$emp = db::select($emp_q);

		$response = array(
			'status' => true,
			'emp_datas' => $emp,
			'emp_bagian' => $emp_bagian
		);
		return Response::json($response);
	}

}
