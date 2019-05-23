<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function indexReportLeaveControl()
	{
		return view('employees.report.leave_control')->with('page', 'Leave Control')->with('head', 'Employees');
	}
}