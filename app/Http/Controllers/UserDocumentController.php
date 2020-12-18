<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\UserDocument;
use App\EmployeeSync;
use Response;
use DataTables;


class UserDocumentController extends Controller
{
	private $category;
	private $status;
	private $condition;

	public function __construct()
	{
		$this->middleware('auth');
		$this->category = [
			'PASPOR',
			'KITAS',
			'MERP',
			'SKLD',
			'SKJ',
			'NOTIF'
		];
		$this->status = [
			'Active',
			'Inactive'
		];
		$this->condition = [
			'Safe',
			'InProcess',
			'AtRisk'
		];
	}

	public function index(){
		$document_numbers = UserDocument::select('document_number')->get();
		$users = UserDocument::leftJoin('users', 'users.username', '=', 'user_documents.employee_id')->select('user_documents.employee_id', 'users.name')->distinct()->get();

		$employees = EmployeeSync::get();

		return view('user_documents.index', array(
			'categories' => $this->category,
			'document_numbers' => $document_numbers,
			'users' => $users,
			'employees' => $employees,
		))->with('page', 'User Document');
	}

	public function fetchUserDocument(Request $request){
		$document = UserDocument::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'user_documents.employee_id');

		if($request->get('documentNumber') != null){
			$document = $document->whereIn('user_documents.document_number', $request->get('documentNumber'));
		}

		if($request->get('employeId') != null){
			$document = $document->whereIn('user_documents.employee_id', $request->get('employeId'));
		}

		if($request->get('category') != null){
			$document = $document->whereIn('user_documents.category', $request->get('category'));
		}

		$document = $document->select(
			'user_documents.document_number',
			'user_documents.employee_id',
			'employee_syncs.name',
			'employee_syncs.position',
			'user_documents.valid_from',
			'user_documents.valid_to',
			'user_documents.category',
			'user_documents.status',
			'user_documents.condition'
		)
		->orderBy(db::raw('FIELD(user_documents.status ,"Active", "Inactive")'))
		->orderBy(db::raw('FIELD(user_documents.condition ,"Expired", "At Risk", "Safe")'))
		->orderBy('employee_id', 'asc')
		->get();

		return DataTables::of($document)
		->addColumn('button', function($document){
			if($document->status == 'Active'){
				return '<button style="margin-right: 2px;" onClick="showRenew(this)" id="'.$document->document_number.'" class="btn btn-xs btn-primary form-control">Renew</button><button onClick="showUpdate(this)" id="'.$document->document_number.'+Inactive" class="btn btn-xs btn-warning form-control">Inactive</button>';
			}else if($document->status == 'Inactive'){
				return '<button style="margin-right: 2px;" onClick="showRenew(this)" id="'.$document->document_number.'" class="btn btn-xs btn-primary form-control">Renew</button><button onClick="showUpdate(this)" id="'.$document->document_number.'+Active" class="btn btn-xs btn-success form-control">Active</button>';
			}
			
		})
		->rawColumns([ 'button' => 'button'])
		->make(true);
	}

	public function fetchUserDocumentDetail(Request $request){
		$document = UserDocument::leftJoin('users', 'users.username', '=', 'user_documents.employee_id');

		if($request->get('documentNumber') != null){
			$document = $document->where('user_documents.document_number', '=', $request->get('documentNumber'));
		}

		$document = $document->select('user_documents.document_number', 'user_documents.employee_id', 'users.name', 'user_documents.valid_from', 'user_documents.valid_to', 'user_documents.category', 'user_documents.status')->get();

		$response = array(
			'status' => true,
			'document' => $document,
		);
		return Response::json($response);
	}

	public function fetchResumeUserDocument(){
		$resume =UserDocument::where('status', 'Active')
		->select('category', 'condition', db::raw('COUNT(id) AS quantity'))
		->groupBy('category', 'condition')
		->get();

		$response = array(
			'status' => true,
			'resume' => $resume
		);
		return Response::json($response);
	}

	public function fetchUserDocumentRenew(Request $request){	
		try{		
			$renew_document = UserDocument::where('employee_id', $request->get('employee_id'))
			->where('category', $request->get('category'))
			->update([
				'document_number' => $request->get('documentNumber'),
				'valid_from' => $request->get('validFrom'),
				'valid_to' => $request->get('validTo'),
			]);

			// $safe = UserDocument::where(db::raw('DATEDIFF(valid_to, NOW())'), '>', 'reminder')->update([
			// 	'condition' => 'Safe',
			// ]);

			// $at_risk = UserDocument::where(db::raw('DATEDIFF(valid_to, NOW())'), '<', 'reminder')->update([
			// 	'condition' => 'At Risk',
			// ]);			

			// $expired = UserDocument::where(db::raw('now()'), '>', 'valid_to')->update([
			// 	'condition' => 'Expired',
			// ]);

			$safe = db::select("UPDATE user_documents
				SET `condition` = 'Safe'
				WHERE DATEDIFF(valid_to, NOW()) > reminder");

			$at_risk = db::select("UPDATE user_documents
				SET `condition` = 'At Risk'
				WHERE DATEDIFF(valid_to, NOW()) < reminder");

			$expired = db::select("UPDATE user_documents
				SET `condition` = 'Expired'
				WHERE now() > valid_to");

			$response = array(
				'status' => true,
			);
			return Response::json($response);
		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

	}

	public function fetchUserDocumentUpdate(Request $request){	
		try{		
			$renew_document = UserDocument::where('document_number', '=', $request->get('documentNumber'))->update([
				'status' => $request->get('status'),
			]);

			$response = array(
				'status' => true,
			);
			return Response::json($response);
		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchUserDocumentCreate(Request $request){
		if($request->get('documentNumber') != null && $request->get('employeId') != null && $request->get('category') != null && $request->get('validFrom') != null && $request->get('validTo') != null){
			try{		
				$document_number = $request->get('documentNumber');
				$employe_id = $request->get('employeId');
				$category = $request->get('category');
				$valid_from = date_create($request->get('validFrom'));
				$valid_to = date_create($request->get('validTo'));

				//define reminder
				$reminder = 0;
				if($category == 'Passport'){
					$reminder = 210;
				}elseif ($category == 'IMTA') {
					$reminder = 90;
				}

				//define condition
				$condition = '';
				$diff = date_diff($valid_to, $valid_from);
				$diff = $diff->format('%a');
				if($diff > $reminder){
					$condition = 'Safe';
				}else{
					$condition = 'At Risk';	
				}

				$document = new UserDocument([
					'category' => $category,
					'document_number' => $document_number,
					'employee_id' => $employe_id,
					'valid_from' => $valid_from,
					'valid_to' => $valid_to,
					'status' => 'Active',
					'condition' => $condition,
					'created_by' => Auth::id(),
					'reminder' => $reminder,
				]);
				$document->save();

				$response = array(
					'status' => true,
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
			);
			return Response::json($response);
		}
	}



}