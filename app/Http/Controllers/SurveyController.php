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
			$survey = DB::SELECT("SELECT
				SUM( a.count_tidak ) AS tidak,
				sum( a.count_belum ) AS belum,
				sum( a.count_iya ) AS iya,
				a.department,
				COALESCE(departments.department_shortname,'') as department_shortname
			FROM
				(
			SELECT
				count( emergency_surveys.employee_id ) AS count_tidak,
				0 AS count_belum,
				0 AS count_iya,
				COALESCE ( department, '' ) AS department 
			FROM
				emergency_surveys
				JOIN employee_syncs ON employee_syncs.employee_id = emergency_surveys.employee_id 
			WHERE
				answer = 'Tidak' 
			GROUP BY
				department UNION ALL
			SELECT
				0 AS count_tidak,
				count( employee_syncs.employee_id ) AS count_belum,
				0 AS count_iya,
				COALESCE ( department, '' ) AS department 
			FROM
				emergency_surveys
				RIGHT JOIN employee_syncs ON employee_syncs.employee_id = emergency_surveys.employee_id 
			WHERE
				emergency_surveys.employee_id IS NULL 
				AND employee_syncs.end_date IS NULL 
			GROUP BY
				department UNION ALL
			SELECT
				0 AS count_tidak,
				0 AS count_belum,
				count( emergency_surveys.employee_id ) AS count_iya,
				COALESCE ( department, '' ) AS department 
			FROM
				emergency_surveys
				JOIN employee_syncs ON employee_syncs.employee_id = emergency_surveys.employee_id 
			WHERE
				answer = 'Iya' 
				AND employee_syncs.end_date IS NULL 
			GROUP BY
				department 
				) a 

				left join departments on a.department = departments.department_name
			GROUP BY
				a.department,departments.department_shortname");

			$response = array(
				'status' => true,
				'survey' => $survey,
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

			if ($dept == "") {
				if ($answer == null) {
					$surveys = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						COALESCE(department_shortname,'') as department,
						COALESCE(answer,'') as answer,
						COALESCE(relationship,'') as relationship,
						COALESCE(family_name,'') as family_name
					FROM
						emergency_surveys
						RIGHT JOIN employee_syncs ON employee_syncs.employee_id = emergency_surveys.employee_id
					WHERE
						department IS NULL
						and emergency_surveys.employee_id is null
						and employee_syncs.end_date is null");
				}else{
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						COALESCE(department_shortname,'') as department,
						COALESCE(answer,'') as answer,
						COALESCE(relationship,'') as relationship,
						COALESCE(family_name,'') as family_name
					FROM
						emergency_surveys
						RIGHT JOIN employee_syncs ON employee_syncs.employee_id = emergency_surveys.employee_id
					WHERE
						department IS NULL
						and answer = '".$answer."'
						and employee_syncs.end_date is null");
				}
			}else{
				if ($answer == null) {
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						COALESCE(department_shortname,'') as department,
						COALESCE(answer,'') as answer,
						COALESCE(relationship,'') as relationship,
						COALESCE(family_name,'') as family_name
					FROM
						emergency_surveys
						RIGHT JOIN employee_syncs ON employee_syncs.employee_id = emergency_surveys.employee_id
						join departments on department_name = employee_syncs.department
					WHERE
						department_shortname = '".$dept."'
						and emergency_surveys.employee_id is null
						and employee_syncs.end_date is null");
				}else{
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						COALESCE(department_shortname,'') as department,
						COALESCE(answer,'') as answer,
						COALESCE(relationship,'') as relationship,
						COALESCE(family_name,'') as family_name
					FROM
						emergency_surveys
						RIGHT JOIN employee_syncs ON employee_syncs.employee_id = emergency_surveys.employee_id
						join departments on department_name = employee_syncs.department
					WHERE
						department_shortname = '".$dept."'
						and answer = '".$answer."'
						and employee_syncs.end_date is null");
				}
			}

			$response = array(
				'status' => true,
				'survey' => $survey,
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
