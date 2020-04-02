<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Response;
use PDF;
use Excel;
use App\Meeting;
use App\MeetingDetail;
use App\MeetingLog;
use App\EmployeeSync;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
	private $location;
	public function __construct()
	{
		$employees = EmployeeSync::orderBy('employee_id', 'asc')
		->get();

		$this->middleware('auth');
		$this->employee = $employees;
		$this->location = [
			'Filling Room',
			'Meeting Room 1',
			'Meeting Room 2',
			'Meeting Room 3',
			'Training Room 1',
			'Training Room 2',
			'Training Room 3'
		];
	}

	public function indexMeeting(){
		return view('meetings.index', array(
			'locations' => $this->location,
			'employees' => $this->employee
		))->with('page', 'Meeting')->with('head', 'Meeting List');
	}

	public function indexMeetingAttendance(Request $request){
		$meetings = Meeting::where('meetings.status', '=', 'open')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'meetings.organizer_id')
		->select('meetings.id', 'meetings.subject', 'employee_syncs.name', db::raw('date_format(start_time, "%d-%b-%Y") as date'), db::raw('concat(date_format(start_time, "%k:%i"), " - ", date_format(end_time, "%k:%i")) as duration'))
		->orderBy('start_time', 'asc')
		->get();

		$title = "Meeting/Training Attendance List";
		$title_jp = "会議の参加者リスト";

		return view('meetings.list', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'meetings' => $meetings
		))->with('page', 'Meeting')->with('head', 'Meeting List');
	}

	public function downloadMeeting(Request $request){

		$reports = Meeting::where('meetings.id', '=', $request->get('id'))
		->leftJoin('meeting_details', 'meeting_details.meeting_id', '=', 'meetings.id')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'meeting_details.employee_id')
		->select('meetings.id', 'meetings.start_time', 'meetings.end_time', 'meetings.subject', 'meeting_details.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'meeting_details.status', 'meeting_details.attend_time', 'meetings.status as meeting_status')
		->get();


		if($reports[0]->meeting_status == 'close'){
			$reports = Meeting::where('meetings.id', '=', $request->get('id'))
			->leftJoin('meeting_logs', 'meeting_logs.meeting_id', '=', 'meetings.id')
			->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'meeting_logs.employee_id')
			->select('meetings.id', 'meetings.start_time', 'meetings.end_time', 'meetings.subject', 'meeting_logs.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'meeting_logs.status', 'meeting_logs.attend_time', 'meetings.status as meeting_status')
			->get();
		}


		$paths = array();

		if($request->get('cat') == 'pdf'){
			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			$pdf->setPaper('A4', 'potrait');
			$pdf->loadView('meetings.report', array(
				'reports' => $reports,
			));
			$pdf->save(public_path() . "/meetings/" . $reports[0]->id . ".pdf");

			$path = "meetings/" . $reports[0]->id . ".pdf";

			array_push($paths, 
				[
					"download" => asset($path),
					"filename" => $reports[0]->id . ".pdf"
				]);

		// return view('meetings.report', array(
		// 	'reports' => $reports
		// ))->with('page', 'Meeting')->with('head', 'Meeting List');

			$response = array(
				'status' => true,
				'message' => 'Download success',
				'paths' => $paths
			);
			return Response::json($response);

		}

		$report_array[] = array('id', 'name', 'department', 'status', 'attendance', 'attend_time');

		if($request->get('cat') == 'xls'){
			foreach ($reports as $key) {
				if($key['employee_id'] != ""){
					$attend = "";
					if($key['status'] == 0){
						$attend = 'Tidak Hadir';
					}
					else{
						$attend = 'Hadir';
					}
					$report_array[] = array(
						'id'=>$key['employee_id'],
						'name'=>$key['name'],
						'department'=>$key['department'],
						'status'=>$key['status'],
						'attendance'=>$attend,
						'attend_time'=>$key['attend_time']
					);
				}
			}

			ob_clean();
			Excel::create('Attendance List', function($excel) use ($report_array){
				$excel->setTitle('Attendance List');
				$excel->sheet('Attendance List', function($sheet) use ($report_array){
					$sheet->fromArray($report_array, null, 'A1', false, false);
				});
			})->store('xlsx', public_path() . "/meetings/");

			$path = "meetings/Attendance List.xlsx";

			array_push($paths, 
				[
					"download" => asset($path),
					"filename" => "Attendance List.xlsx"
				]);

			$response = array(
				'status' => true,
				'message' => 'Download success',
				'paths' => $paths
			);
			return Response::json($response);
		}
	}

	public function scanMeetingAttendance(Request $request){
		$id = Auth::id();
		$employee = db::table('employees')->where('tag', '=', $request->get('tag'))->first();

		if($employee == null){
			$response = array(
				'status' => false,
				'message' => 'ID Card not found'
			);
			return Response::json($response);
		}

		try{
			$meeting_detail = MeetingDetail::where('meeting_id', '=', $request->get('meeting_id'))
			->where('employee_id', '=', $employee->employee_id)
			->first();

			
			if($meeting_detail != null){

				if($meeting_detail->status != 0){
					$response = array(
						'status' => false,
						'message' => 'Already attended',
					);
					return Response::json($response);
				}

				$meeting_detail->employee_tag = $employee->tag;
				$meeting_detail->status = '1';
				$meeting_detail->attend_time = date('Y-m-d H:i:s');
			}
			else{
				$meeting_detail = new MeetingDetail([
					'meeting_id' => $request->get('meeting_id'),
					'emplyee_tag' => $employee->tag,
					'emplyee_id' => $employee->employee_id,
					'status' => 2,
					'attend_time' => date('Y-m-d H:i:s'),
					'created_by' => $id
				]);
			}

			$meeting_detail->save();
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
			'message' => 'Attendance success'
		);
		return Response::json($response);
	}

	public function fetchAddParticipant(Request $request){
		if(strlen($request->get('assignment')) == 0 && strlen($request->get('position')) == 0 && strlen($request->get('department')) == 0 && strlen($request->get('employee_id')) == 0){
			$response = array(
				'status' => false,
				'message' => 'Please select parameter to add participant'
			);
			return Response::json($response);
		}

		$participants = EmployeeSync::select('employee_id', 'assignment', 'position', 'department', 'name');

		if($request->get('id') == 'param'){
			if(strlen($request->get('assignment')) > 0){
				$participants = $participants->where('assignment', '=', $request->get('assignment'));
			}
			if(strlen($request->get('position')) > 0){
				$participants = $participants->where('position', '=', $request->get('position'));
			}
			if(strlen($request->get('department')) > 0){
				$participants = $participants->where('department', '=', $request->get('department'));			
			}
			if(strlen($request->get('employee_id')) > 0){
				$participants = $participants->where('employee_id', '=', $request->get('employee_id'));			
			}	
		}
		else{
			if(strlen($request->get('id')) > 0){
				$participants = $participants->where('employee_id', '=', $request->get('id'));			
			}
		}

		$participants = $participants->get();

		$response = array(
			'status' => true,
			'message' => 'Participant added',
			'participants' => $participants
		);
		return Response::json($response);
	}

	public function fetchMeetingAttendance(Request $request){

		$attendances = Meeting::leftJoin('meeting_details', 'meetings.id', '=', 'meeting_details.meeting_id')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'meeting_details.employee_id')
		->leftJoin('employee_syncs as org', 'org.employee_id', '=', 'meetings.organizer_id')
		// ->leftJoin('employee_syncs as org', 'employee_syncs.employee_id', '=', 'meetings.organizer_id')
		->where('meetings.id', '=', $request->get('id'))
		->select('org.name as organizer_name', db::raw('date_format(meetings.start_time, "%a, %d %b %Y %H:%i") as start_time'), db::raw('date_format(meetings.end_time, "%a, %d %b %Y %H:%i") as end_time'), db::raw('timestampdiff(minute, meetings.start_time, meetings.end_time) as diff'), db::raw('if(meetings.start_time < meeting_details.attend_time, 1, 0) as late'), 'meetings.organizer_id', 'meetings.subject', 'meeting_details.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'meeting_details.attend_time', 'meeting_details.status', 'meetings.status as meeting_status')
		->orderBy('meeting_details.status', 'asc')
		->get();

		if($attendances[0]->meeting_status == 'close'){
			$response = array(
				'status' => false,
				'message' => 'This meeting already closed.'
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'attendances' => $attendances
		);
		return Response::json($response);
	}

	public function fetchMeeting(Request $request){
		$meetings = Meeting::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'meetings.organizer_id');

		if(strlen($request->get('dateFrom')) > 0){
			$dateFrom = date('Y-m-d', strtotime($request->get('dateFrom')));
			$meetings = $meetings->whereRaw("date(meetings.start_time) >= '".$dateFrom."'");
		}
		if(strlen($request->get('dateTo')) > 0){
			$dateTo = date('Y-m-d', strtotime($request->get('dateTo')));
			$meetings = $meetings->whereRaw("date(meetings.end_time) <= '".$dateTo."'");
		}
		if($request->get('location') != null){
			$meetings = $meetings->whereIn('meetings.location', $request->get('location'));
		}
		if(strlen($request->get('status')) > 0 && $request->get('status') != 'all'){
			$meetings = $meetings->where('meetings.status', '=', $request->get('status'));
		}
		if(strlen($request->get('dateFrom')) == 0 && strlen($request->get('dateTo')) == 0 && $request->get('location') == null && strlen($request->get('status')) == 0){
			$meetings = $meetings->where('meetings.status', '=', 'open');			
		}

		$meetings = $meetings->select('meetings.id', db::raw('date_format(start_time, "%d-%b-%Y") as date'), 'meetings.subject', 'meetings.location', 'employee_syncs.name', db::raw('concat(date_format(start_time, "%k:%i"), " - ", date_format(end_time, "%k:%i")) as duration'), 'meetings.status')
		->orderByRaw('meetings.status desc, meetings.start_time desc')
		->get();

		$response = array(
			'status' => true,
			'meetings' => $meetings
		);
		return Response::json($response);
	}

	public function createMeeting(Request $request){
		$id = Auth::id();

		try{
			$meeting = new Meeting([
				'subject' => $request->get('subject'),
				'description' => $request->get('description'),
				'location' => $request->get('location'),
				'start_time' => $request->get('start_time'),
				'end_time' => $request->get('end_time'),
				'status' => 'open',
				'organizer_id' => Auth::user()->username,
				'created_by' => Auth::id()
			]);
			$meeting->save();

			$attendances = $request->get('attendances');

			for ($i=0; $i < count($attendances); $i++) { 
				$meeting_details = new MeetingDetail([
					'meeting_id' => $meeting->id,
					'employee_id' => $attendances[$i],
					'status' => 0,
					'created_by' => $id
				]);
				$meeting_details->save();
			};
			
		}
		catch (QueryException $e){
			$error_code = $e->errorInfo[1];
			if($error_code == 1062){

			}
			else{
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		$response = array(
			'status' => true,
			'message' => 'Create meeting success'
		);
		return Response::json($response);
	}

	public function editMeeting(Request $request){
		$meeting = Meeting::find($request->get('id'));

		if(Auth::user()->username != $meeting->organizer_id){
			$response = array(
				'status' => false,
				'message' => "You don't have permission"
			);
			return Response::json($response);
		}

		try{

			if($request->get('status') == 'close'){
				$qry = "
				insert into meeting_logs (meeting_id, employee_tag, employee_id, `status`, remark, attend_time, organizer_id, `subject`, description, location, start_time, end_time, created_by, created_at, updated_at)
				select meeting_details.meeting_id, meeting_details.employee_tag, meeting_details.employee_id, meeting_details.`status`, meeting_details.remark, meeting_details.attend_time, meetings.organizer_id, meetings.`subject`, meetings.description, meetings.location, meetings.start_time, meetings.end_time, meetings.created_by, now(), now() from meeting_details left join meetings on meetings.id = meeting_details.meeting_id where meeting_details.meeting_id = '".$request->get('id')."'";

				$logs = db::select($qry);

				$delete_details = MeetingDetail::where('meeting_id', '=', $request->get('id'))->forceDelete();

				$meeting->subject = $request->get('subject');
				$meeting->description = $request->get('description');
				$meeting->location = $request->get('location');
				$meeting->start_time = $request->get('start_time');
				$meeting->end_time = $request->get('end_time');
				$meeting->status = $request->get('status');
				$meeting->save();
			}
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
			'message' => 'Edit meeting success'
		);
		return Response::json($response);
	}

	public function deleteMeeting(Request $request){
		if($request->get('cat') == 'audience'){
			$delete = MeetingDetail::where('meeting_details.id', '=', $request->get('id'))
			->leftJoin('meetings', 'meetings.id', '=', 'meeting_details.meeting_id')
			->first();

			if($delete == null){
				$response = array(
					'status' => false,
					'message' => "This meeting already closed"
				);
				return Response::json($response);
			}

			if(Auth::user()->username != $delete->organizer_id){
				$response = array(
					'status' => false,
					'message' => "You don't have permission"
				);
				return Response::json($response);
			}

			$delete->delete();
		}
		else if($request->get('cat') == 'meeting'){
			$delete = Meeting::where('meetings.id', '=', $request->get('id'))
			->first();

			if(Auth::user()->username != $delete->organizer_id){
				$response = array(
					'status' => false,
					'message' => "You don't have permission"
				);
				return Response::json($response);
			}

			$delete2 = MeetingDetail::where('meeting_details.meeting_id', '=', $delete->id)
			->delete();
			$delete->delete();
		}

		$response = array(
			'status' => true,
			'message' => 'Delete '.$request->get('cat').' success'
		);
		return Response::json($response);
	}

	public function fetchMeetingDetail(Request $request){
		$meeting = Meeting::where('meetings.id', '=', $request->get('id'))
		->select('meetings.id', 'meetings.subject', 'meetings.description', 'meetings.location', db::raw('date_format(meetings.start_time, "%Y-%m-%d %k:%i") as start_time'), db::raw('date_format(meetings.end_time, "%Y-%m-%d %k:%i") as end_time'), 'meetings.status')
		->first();

		if($meeting->status == 'open'){
			$meeting_details = MeetingDetail::where('meeting_details.meeting_id', '=', $request->get('id'))
			->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'meeting_details.employee_id')
			->select('meeting_details.id', 'meeting_details.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'meeting_details.status')
			->orderBy('meeting_details.id', 'asc')
			->get();
		}
		else{
			$meeting_details = MeetingLog::where('meeting_logs.meeting_id', '=', $request->get('id'))
			->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'meeting_logs.employee_id')
			->select('meeting_logs.id', 'meeting_logs.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'meeting_logs.status')
			->orderBy('meeting_logs.id', 'asc')
			->get();
		}

		$response = array(
			'status' => true,
			'meeting' => $meeting,
			'meeting_details' => $meeting_details
		);
		return Response::json($response);
	}
}
