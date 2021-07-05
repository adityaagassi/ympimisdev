<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Response;
use Auth;
use App\CodeGenerator;
use App\Ticket;
use App\TicketAttachment;
use App\TicketApprover;
use App\TicketCostdown;
use App\TicketTimeline;
use App\TicketEquipment;
use App\TicketPic;
use App\EmployeeSync;
use App\User;

class TicketController extends Controller
{
	private $category;
	private $priority;
	private $difficulty;
	private $status;
	private $costdown;
	
	public function __construct()
	{
		$this->middleware('auth');
		$this->category = [
			'Perbaikan Hardware/Software',
			'Perbaikan Jaringan',
			'Pengadaan Hardware/Software',
			'Pemasangan Hardware/Software',
			'Pembuatan Aplikasi Baru',
			'Pengembangan Aplikasi Lama',
			'Perbaikan Aplikasi Error/Bug'
		];
		$this->priority = [
			'Very High',
			'High',
			'Normal',
			'Low',
		];
		$this->difficulty = [
			'S (>3 Months)',
			'A (<3 Months)',
			'B (<1 Month)',
			'C (<1 Week)'
		];
		$this->status = [
			'Approval',
			'Waiting',
			'InProgress',
			'OnHold',
			'Finished'
		];
		$this->costdown = [
			'Manpower',
			'Overtime',
			'Efficiency',
			'Material',
			'Safety',
			'5S',
			'Quality',
			'Delivery'
		];
		$this->timeline_category = [
			'Programming',
			'Meeting',
			'Installation',
			'Repair',
			'Design',
			'Training',
			'Trial',
			'Go Live'
		];
	}

	public function indexTicketMonitoring($id){
		if($id == 'mis'){
			$title = "MIS Ticket Monitoring";
			$title_jp = "";
			$pics = TicketPic::where('remark', '=', $id)->get();

			return view('about_mis.ticket.monitoring', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'pics' => $pics
			))->with('page', 'MIS Ticket')->with('head', 'Ticket');
		}

		if($id == 'borrow'){
			$title = "Peminjaman Laptop";
			$title_jp = "";

			return view('about_mis.ticket.borrow', array(
				'title' => $title,
				'title_jp' => $title_jp
			))->with('page', 'MIS Ticket')->with('head', 'Ticket');
		}
	}

	public function fetchTicketMonitoring(Request $request){

		$tickets = Ticket::orderBy('ticket_id', 'DESC')
		->leftJoin('departments', 'departments.department_name', '=', 'tickets.department')
		->select(
			'tickets.ticket_id',
			'tickets.status',
			'tickets.department',
			'tickets.category',
			'tickets.priority',
			'tickets.priority_reason',
			'tickets.case_title',
			'tickets.case_description',
			'tickets.case_before',
			'tickets.case_after',
			'tickets.document',
			'tickets.due_date_from',
			'tickets.due_date_to',
			'tickets.estimated_due_date_from',
			'tickets.estimated_due_date_to',
			'tickets.pic_id',
			'tickets.pic_name',
			'tickets.difficulty',
			'tickets.progress',
			'tickets.project_name',
			'tickets.remark',
			'tickets.created_by',
			'tickets.created_at',
			'tickets.updated_at',
			'departments.department_shortname'
		)
		->get();
		$ticket_approvers = TicketApprover::get();
		$counts = Ticket::leftJoin('departments', 'departments.department_name', '=', 'tickets.department')
		->select('departments.department_shortname', 'tickets.status', db::raw('count(tickets.ticket_id) as cnt'))
		->groupBy('departments.department_shortname', 'tickets.status')
		->orderBy('departments.department_shortname', 'ASC')
		->orderBy('tickets.status', 'ASC')
		->get();
		$departments = db::table('departments')
		->where('department_shortname', '!=', 'JPN')
		->orderBy('department_name', 'ASC')
		->get();
		$statuses = $this->status;

		$res1 = array();

		foreach($departments as $department){
			for($i = 0; $i < count($statuses); $i++){
				array_push($res1, [
					'department_shortname' => $department->department_shortname,
					'status' => $statuses[$i],
					'cnt' => 0
				]);
			}
		}

		$res2 = array();

		for($i = 0; $i < count($res1); $i++){
			$cnt = 0;
			foreach($counts as $count){
				if($count->department_shortname == $res1[$i]['department_shortname'] && $count->status == $res1[$i]['status']){
					$cnt = $count->cnt;
				}
			}
			array_push($res2, [
				'department_shortname' => $res1[$i]['department_shortname'],
				'status' => $res1[$i]['status'],
				'cnt' => $cnt
			]);
		}

		$response = array(
			'status' => true,
			'tickets' => $tickets,
			'counts' => $res2,
			'ticket_approvers' => $ticket_approvers
		);
		return Response::json($response);
	}

	public function indexTicketDetail($id){
		$title = "Ticket Detail";
		$title_jp = "";

		$ticket = Ticket::where('ticket_id', '=', $id)->first();
		$ticket_approver = TicketApprover::where('ticket_id', '=', $id)->get();
		$ticket_attachment = TicketAttachment::where('ticket_id', '=', $id)->get();
		$ticket_costdown = TicketCostdown::where('ticket_id', '=', $id)->get();
		$ticket_timeline = TicketTimeline::where('ticket_id', '=', $id)->orderBy('timeline_date', 'DESC')->get();
		$ticket_equipment = TicketEquipment::where('ticket_id', '=', $id)->get();
		$departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
		$equipments = db::select("SELECT
			ai.kode_item,
			ai.deskripsi,
			round( ai.harga / aer.rate, 2 ) AS price_usd 
			FROM
			acc_items AS ai
			LEFT JOIN ( SELECT * FROM acc_exchange_rates WHERE periode = '".date('Y-m-01')."' ) AS aer ON ai.currency = aer.currency");
		$mis_members = EmployeeSync::where('department', '=', 'Management Information System Department')->orderBy('hire_date', 'ASC')->whereNull('end_date')->get();

		return view('about_mis.ticket.detail', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'departments' => $departments,
			'ticket' => $ticket,
			'ticket_approver' => $ticket_approver,
			'ticket_attachment' => $ticket_attachment,
			'ticket_costdown' => $ticket_costdown,
			'ticket_timeline' => $ticket_timeline,
			'ticket_equipment' => $ticket_equipment,
			'mis_members' => $mis_members,
			'equipments' => $equipments,
			'categories' => $this->category,
			'priorities' => $this->priority,
			'difficulties' => $this->difficulty,
			'statuses' => $this->status,
			'costdowns' => $this->costdown,
			'timeline_categories' => $this->timeline_category
		))->with('page', 'MIS Ticket')->with('head', 'Ticket');

	}

	public function indexTicket($id){
		if($id == 'mis'){
			$title = "MIS Ticketing System";
			$title_jp = "";

			$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();
			$departments = db::table('departments')->orderBy('department_name', 'ASC')->get();

			return view('about_mis.ticket.index', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'employee' => $employee,
				'departments' => $departments,
				'categories' => $this->category,
				'priorities' => $this->priority,
				'difficulties' => $this->difficulty,
				'statuses' => $this->status,
				'costdowns' => $this->costdown
			))->with('page', 'MIS Ticket')->with('head', 'Ticket');
		}
	}

	public function editTicket(Request $request){
		try{
			$ticket = Ticket::where('ticket_id', '=', $request->input('ticket_id'))->first();

			$ticket->status = $request->input('status');
			$ticket->department = $request->input('department');
			$ticket->category = $request->input('category');
			$ticket->priority = $request->input('priority');
			$ticket->priority_reason = $request->input('reason');
			$ticket->case_title = $request->input('title');
			$ticket->case_description = $request->input('description');
			$ticket->case_before = $request->input('before');
			$ticket->case_after = $request->input('after');
			$ticket->document = $request->input('doc');
			$ticket->due_date_from = $request->input('due_from');
			$ticket->due_date_to = $request->input('due_to');
			$ticket->estimated_due_date_from = $request->input('estimated_due_from');
			$ticket->estimated_due_date_to = $request->input('estimated_due_to');
			$ticket->pic_id = $request->input('pic_id');
			$ticket->pic_name = $request->input('pic_name');
			$ticket->difficulty = $request->input('difficulty');
			$ticket->project_name = $request->input('project_name');

			if(count($request->input('costdown')) >0){
				foreach($request->input('costdown') as $costdown){
					$col = explode('~', $costdown);

					$ticket_costdown = new TicketCostdown([
						'ticket_id' => $request->input('ticket_id'),
						'category' => $col[0],
						'cost_description' => $col[1],
						'cost_amount' => $col[2]
					]);

					$ticket_costdown->save();

				}
			}

			if(count($request->input('equipment')) >0){
				foreach($request->input('equipment') as $equipment){
					$col = explode('~', $equipment);

					$ticket_equipment = new TicketEquipment([
						'ticket_id' => $request->input('ticket_id'),
						'item_id' => $col[0],
						'item_description' => $col[1],
						'quantity' => $col[2],
						'item_price' => $col[3],
						'created_by' => Auth::id()
					]);

					$ticket_equipment->save();

				}
			}

			$ticket->save();

			$response = array(
				'status' => true,
				'message' => 'Ticket berhasil diubah',
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

	public function fetchTicketPDF($id){
		$ticket = Ticket::where('ticket_id', '=', $id)->first();
		$costdown = TicketCostdown::where('ticket_id', '=', $id)->get();
		$approver = TicketApprover::where('ticket_id', '=', $id)->get();

		$data = [
			'ticket' => $ticket,
			'costdown' => $costdown,
			'approver' => $approver
		];

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A4', 'potrait');

		$pdf->loadView('about_mis.ticket.pdf_approval', array(
			'data' => $data,
		));

		return $pdf->stream("general.pointing_call.safety_riding_pdf");
	}

	public function fetchTicket(Request $request){
		$category = $request->get('category');

		$tickets = Ticket::orderBy('tickets.created_at', 'DESC');

		if($request->get('status') != 'all'){
			$tickets = $tickets->where('status', '=', $request->get('status'));
		}

		if(Auth::user()->role_code != 'MIS'){
			$tickets = $tickets->where(function($query){
				$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();
				$query->where('created_by', '=', Auth::id())
				->orWhere('department', '=', $employee->department);
			});
		}

		$tickets = $tickets->get();

		$response = array(
			'status' => true,
			'tickets' => $tickets
		);
		return Response::json($response);
	}

	public function approvalTicketMonitoring(Request $request){

	}

	public function approvalTicket(Request $request){
		try{
			$ticket_approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))
			->where('remark', '=', $request->get('code'))
			->first();

			if($ticket_approver->status != null){
				return view('about_mis.ticket.notification', array(
					'title' => 'Ticket Approval',
					'title_jp' => '',
					'code' => 1,
					'ticket_approver' => $ticket_approver

				))->with('page', 'MIS Ticket')->with('head', 'Ticket');
			}
			else{
				$ticket_approver->status = $request->get('status');
				$ticket_approver->approved_at = date('Y-m-d H:i:s');
				$ticket_approver->save();

				$ticket = Ticket::where('ticket_id', '=', $request->get('ticket_id'))->first();

				$next_email = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))
				->whereNull('status')
				->orderBy('id', 'ASC')
				->first();

				if($next_email != null){
					$cd = TicketCostdown::where('ticket_id', '=', $request->get('ticket_id'))->get();
					$attachment = TicketAttachment::where('ticket_id', '=', $request->get('ticket_id'))->first();
					$approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))->get();

					$data = [
						'code' => $next_email->remark,
						'ticket' => $ticket,
						'costdown' => $cd,
						'approver' => $approver,
						'filename' => $attachment->file_name
					];

					Mail::to($next_email->approver_email)
					->bcc(['aditya.agassi@music.yamaha.com'])
					->send(new SendEmail($data, 'mis_ticket_approval'));
				}
				else{
					$cd = TicketCostdown::where('ticket_id', '=', $request->get('ticket_id'))->get();
					$attachment = TicketAttachment::where('ticket_id', '=', $request->get('ticket_id'))->first();
					$approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))->get();
					$ticket->status = 'Waiting';
					$ticket->save();

					$data = [
						'code' => 'fully_approved',
						'ticket' => $ticket,
						'costdown' => $cd,
						'approver' => $approver,
						'filename' => $attachment->file_name
					];

					$cc = ['agus.yulianto@music.yamaha.com'];

					if(strlen($ticket->document) > 3){
						array_push($cc, 'evi.nur.cholifah@music.yamaha.com');
						array_push($cc, 'widura@music.yamaha.com');
					}

					Mail::to(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])
					->cc($cc)
					->send(new SendEmail($data, 'mis_ticket_approval'));
				}
			}

			return view('about_mis.ticket.notification', array(
				'title' => 'Ticket Approval',
				'title_jp' => '',
				'code' => 2,
				'ticket_approver' => $ticket_approver

			))->with('page', 'MIS Ticket')->with('head', 'Ticket Confirmation');
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputTicketTimeline(Request $request){
		try{
			$filename = "";
			if (count($request->file('attachment')) > 0) {
				$file_destination = 'files/mis_ticket';
				$file = $request->file('attachment');
				$filename = $request->input('ticket_id').date('YmdHis').'('.$request->input('file_name').').'.$request->input('extension');
				$file->move($file_destination, $filename);

				$ticket_attachment = new TicketAttachment([
					'ticket_id' => $request->input('ticket_id'),
					'file_name' => $filename,
					'file_extension' => $request->input('extension'),
					'remark' => 'timeline',
					'created_by' => Auth::id()
				]);
				$ticket_attachment->save();
			}

			$ticket_timeline = new TicketTimeline([
				'ticket_id' => $request->input('ticket_id'),
				'pic_id' => $request->input('pic_id'),
				'pic_name' => $request->input('pic_name'),
				'timeline_date' => $request->input('date'),
				'timeline_category' => $request->input('category'),
				'timeline_description' => $request->input('description'),
				'duration' => $request->input('duration'),
				'progress_update' => $request->input('progress'),
				'timeline_attachment' => $filename,
				'created_by' => Auth::id()
			]);
			$ticket_timeline->save();

			// $timelines = TicketTimeline::where('ticket_id', '=', $request->input('ticket_id'))->get();
			$ticket = Ticket::where('ticket_id', '=', $request->input('ticket_id'))->get();
			$ticket->progress = $request->input('progress');
			$ticket->save();

			$response = array(
				'status' => true,
				// 'timelines' => $timelines,
				// 'ticket' => $ticket,
				'message' => 'Timeline berhasil ditambahkan.',
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

	public function inputTicket(Request $request){
		try{
			$code_generator = CodeGenerator::where('note','=','mis_ticket')->first();
			$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
			$ticket_id = $code_generator->prefix . $number;
			$filename = null;

			$ticket = new Ticket([
				'ticket_id' => $ticket_id,
				'status' => 'Approval',
				'department' => $request->input('department'),
				'category' => $request->input('category'),
				'priority' => $request->input('priority'),
				'priority_reason' => $request->input('reason'),
				'case_title' => $request->input('title'),
				'case_description' => $request->input('description'),
				'case_before' => $request->input('before'),
				'case_after' => $request->input('after'),
				'document' => $request->input('doc'),
				'due_date_from' => $request->input('due_from'),
				'due_date_to' => $request->input('due_to'),
				'created_by' => Auth::id()
			]);

			if (count($request->file('attachment')) > 0) {
				$filename = "";
				$file_destination = 'files/mis_ticket';
				$file = $request->file('attachment');
				$filename = $ticket_id.date('YmdHis').'('.$request->input('file_name').').'.$request->input('extension');
				$file->move($file_destination, $filename);

				$ticket_attachment = new TicketAttachment([
					'ticket_id' => $ticket_id,
					'file_name' => $filename,
					'file_extension' => $request->input('extension'),
					'remark' => 'user',
					'created_by' => Auth::id()
				]);
				$ticket_attachment->save();
			}

			$send_email = db::table('send_emails')->where('remark', '=', $request->input('department'))->first();
			$user = User::where('email', '=', $send_email->email)->first();

			// if(Auth::user()->role_code != 'MIS'){
			$approve_manager = new TicketApprover([
				'ticket_id' => $ticket_id,
				'approver_id' => $user->username,
				'approver_name' => $user->name,
				'approver_email' => $send_email->email,
				'remark' => 'Manager'
			]);

			$approve_chief_mis = new TicketApprover([
				'ticket_id' => $ticket_id,
				'approver_id' => 'PI0103002',
				'approver_name' => 'Agus Yulianto',
				'approver_email' => 'agus.yulianto@music.yamaha.com',
				'remark' => 'Chief MIS'
			]);

			if($request->input('priority') == 'High' || $request->input('priority') == 'Very High'){
				$approve_manager_mis = new TicketApprover([
					'ticket_id' => $ticket_id,
					'approver_id' => 'PI0109004',
					'approver_name' => 'Budhi Apriyanto',
					'approver_email' => 'budhi.apriyanto@music.yamaha.com',
					'remark' => 'Manager MIS'
				]);
			}
			else if($request->input('category') == 'Pembuatan Aplikasi Baru' || $request->input('category') == 'Pengembangan Aplikasi Lama'){
				$approve_manager_mis = new TicketApprover([
					'ticket_id' => $ticket_id,
					'approver_id' => 'PI0109004',
					'approver_name' => 'Budhi Apriyanto',
					'approver_email' => 'budhi.apriyanto@music.yamaha.com',
					'remark' => 'Manager MIS'
				]);
			}
			// }	

			$cd = array();

			foreach($request->input('costdown') as $costdown){
				$col = explode('~', $costdown);

				$ticket_costdown = new TicketCostdown([
					'ticket_id' => $ticket_id,
					'category' => $col[0],
					'cost_description' => $col[1],
					'cost_amount' => $col[2]
				]);

				$ticket_costdown->save();

				array_push($cd, [
					'category' => $col[0],
					'cost_description' => $col[1],
					'cost_amount' => $col[2]
				]);
			}

			$code_generator->index = $code_generator->index+1;
			$approve_manager->save();
			$approve_chief_mis->save();
			$approve_manager_mis->save();
			$code_generator->save();
			$ticket->save();

			$approver = TicketApprover::where('ticket_id', '=', $ticket_id)->get();

			$data = [
				'code' => 'Manager',
				'ticket' => $ticket,
				'costdown' => $cd,
				'approver' => $approver,
				'filename' => $filename
			];

			// if(Auth::user()->role_code != 'MIS'){
			Mail::to($approve_manager->approver_email)
			->bcc(['aditya.agassi@music.yamaha.com'])
			->send(new SendEmail($data, 'mis_ticket_approval'));

			$response = array(
				'status' => true,
				'message' => 'Ticket berhasil dibuat, proses approval dikirim melalui email.',
			);
			return Response::json($response);
			// }
			// else{
			// 	$data = [
			// 		'code' => 'fully_approved',
			// 		'ticket' => $ticket,
			// 		'costdown' => $cd,
			// 		'approver' => $approver,
			// 		'filename' => $filename
			// 	];

			// 	Mail::to(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])
			// 	->cc($cc)
			// 	->send(new SendEmail($data, 'mis_ticket_approval'));

			// 	$response = array(
			// 		'status' => true,
			// 		'message' => 'Ticket berhasil dibuat',
			// 	);
			// 	return Response::json($response);
			// }
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
