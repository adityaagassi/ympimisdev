<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Response;

class SurveyController extends Controller
{
    public function __construct(){
		$this->middleware('auth');
	}

	public function indexSurvey()
	{
		$title = 'Emergency Survey';
		$title_jp = '??';

		return view('survey.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Emergency Survey')->with('head','Emergency Survey');
	}

	public function fetchSurvey(Request $request)
	{
		try {
			if ($request->get('keterangan') == null) {
				$keterangan = "Emergency 1";
			}else{
				$keterangan = $request->get('keterangan');
			}

			$survey = DB::SELECT("SELECT
				SUM( a.count_tidak ) AS tidak,
				sum( a.count_all ) - (
				SUM( a.count_tidak )+ SUM( a.count_iya )) AS belum,
				sum( a.count_iya ) AS iya,
				a.department,
				COALESCE ( departments.department_shortname, '' ) AS department_shortname 
			FROM
				(
				SELECT
					0 AS count_tidak,
					count( employee_syncs.employee_id ) AS count_all,
					0 AS count_iya,
					COALESCE ( employee_syncs.department, '' ) AS department 
				FROM
					employee_syncs 
				WHERE
					employee_syncs.end_date IS NULL 
				GROUP BY
					department UNION ALL
				SELECT
					count( emergency_surveys.employee_id ) AS count_tidak,
					0 AS count_all,
					0 AS count_iya,
					COALESCE ( employee_syncs.department, '' ) AS department 
				FROM
					employee_syncs
					LEFT JOIN emergency_surveys ON emergency_surveys.employee_id = employee_syncs.employee_id 
				WHERE
					employee_syncs.end_date IS NULL 
					AND jawaban = 'Tidak' 
					AND emergency_surveys.keterangan = '".$keterangan."'  
				GROUP BY
					department UNION ALL
				SELECT
					0 AS count_tidak,
					0 AS count_all,
					count( emergency_surveys.employee_id ) AS count_iya,
					COALESCE ( employee_syncs.department, '' ) AS department 
				FROM
					employee_syncs
					LEFT JOIN emergency_surveys ON emergency_surveys.employee_id = employee_syncs.employee_id 
				WHERE
					employee_syncs.end_date IS NULL 
					AND jawaban = 'Iya' 
					AND emergency_surveys.keterangan = '".$keterangan."' 
				GROUP BY
					department 
				) a
				LEFT JOIN departments ON a.department = departments.department_name 
			GROUP BY
				a.department,
				departments.department_shortname");

			$response = array(
				'status' => true,
				'survey' => $survey,
				'keterangan' => $keterangan,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function fetchSurveyDetail(Request $request)
	{
		try {
			$answer = $request->get('answer');
			$dept = $request->get('dept');
			if ($answer == 'No') {
				$answer = 'Tidak';
			}else if($answer == 'Yes'){
				$answer = 'Iya';
			}else{
				$answer = null;
			}

			if ($request->get('keterangan') == null) {
				$keterangan = "Emergency 1";
			}else{
				$keterangan = $request->get('keterangan');
			}

			if ($dept == "") {
				if ($answer == null) {
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'' AS department,
						COALESCE ( jawaban, '' ) AS jawaban,
						COALESCE ( hubungan, '' ) AS hubungan,
						COALESCE ( nama, '' ) AS nama
					FROM
						employee_syncs
						LEFT JOIN emergency_surveys ON employee_syncs.employee_id = emergency_surveys.employee_id 
						and keterangan = '".$keterangan."'
					WHERE
						employee_syncs.end_date IS NULL 
						AND employee_syncs.department IS NULL 
						AND emergency_surveys.employee_id IS NULL");
				}else{
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'' AS department,
						COALESCE ( jawaban, '' ) AS jawaban,
						COALESCE ( hubungan, '' ) AS hubungan,
						COALESCE ( nama, '' ) AS nama
					FROM
						employee_syncs
						LEFT JOIN emergency_surveys ON employee_syncs.employee_id = emergency_surveys.employee_id 
						AND keterangan = '".$keterangan."' 
					WHERE
						employee_syncs.end_date IS NULL 
						AND employee_syncs.department IS NULL 
						AND jawaban = '".$answer."'");
				}
			}else{
				if ($answer == null) {
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'' AS department,
						COALESCE ( jawaban, '' ) AS jawaban,
						COALESCE ( hubungan, '' ) AS hubungan,
						COALESCE ( nama, '' ) AS nama
					FROM
						employee_syncs
						LEFT JOIN emergency_surveys ON employee_syncs.employee_id = emergency_surveys.employee_id 
						AND keterangan = '".$keterangan."'
						JOIN departments ON department_name = employee_syncs.department 
					WHERE
						employee_syncs.end_date IS NULL 
						AND emergency_surveys.employee_id IS NULL 
						AND department_shortname = '".$dept."'");
				}else{
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'' AS department,
						COALESCE ( jawaban, '' ) AS jawaban,
						COALESCE ( hubungan, '' ) AS hubungan,
						COALESCE ( nama, '' ) AS nama 
					FROM
						employee_syncs
						LEFT JOIN emergency_surveys ON employee_syncs.employee_id = emergency_surveys.employee_id 
						AND keterangan = '".$keterangan."'
						JOIN departments ON department_name = employee_syncs.department 
					WHERE
						employee_syncs.end_date IS NULL 
						AND department_shortname = '".$dept."' 
						AND jawaban = '".$answer."'");
				}
			}

			$response = array(
				'status' => true,
				'survey' => $survey,
				'keterangan' => $keterangan,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}
}
