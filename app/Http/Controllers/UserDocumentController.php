<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\UserDocument;
use Response;

class UserDocumentController extends Controller
{
	private $category;
	private $status;
	private $condition;

	public function __construct()
	{
		$this->middleware('auth');
		$this->category = [
			'Passport',
			'IMTA',
		];
		$this->status = [
			'Active',
			'Inactive',
		];
		$this->condition = [
			'Safe',
			'InProcess',
			'AtRisk',
		];
	}

	public function index()
	{
		$document_numbers = UserDocument::select('document_number')->get();
		$users = UserDocument::leftJoin('users', 'users.username', '=', 'user_documents.employee_id')->select('user_documents.employee_id', 'users.name')->distinct()->get();
		return view('user_documents.index', array(
			'categories' => $this->category,
			'document_numbers' => $document_numbers,
			'users' => $users,
		))->with('page', 'User Document');
	}
}
