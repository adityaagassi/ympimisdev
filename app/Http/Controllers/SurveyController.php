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
		$title_jp = 'エマージェンシーサーベイ';

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
				department_shortname
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
					count( miraimobile.emergency_surveys.employee_id ) AS count_tidak,
					0 AS count_all,
					0 AS count_iya,
					COALESCE ( employee_syncs.department, '' ) AS department 
				FROM
					employee_syncs
					LEFT JOIN miraimobile.emergency_surveys ON miraimobile.emergency_surveys.employee_id = employee_syncs.employee_id 
				WHERE
					employee_syncs.end_date IS NULL 
					AND jawaban = 'Tidak' 
					AND miraimobile.emergency_surveys.keterangan = '".$keterangan."'  
				GROUP BY
					department UNION ALL
				SELECT
					0 AS count_tidak,
					0 AS count_all,
					count( miraimobile.emergency_surveys.employee_id ) AS count_iya,
					COALESCE ( employee_syncs.department, '' ) AS department 
				FROM
					employee_syncs
					LEFT JOIN miraimobile.emergency_surveys ON miraimobile.emergency_surveys.employee_id = employee_syncs.employee_id 
				WHERE
					employee_syncs.end_date IS NULL 
					AND jawaban = 'Iya' 
					AND miraimobile.emergency_surveys.keterangan = '".$keterangan."' 
				GROUP BY
					department 
				) a
				LEFT JOIN departments ON a.department = departments.department_name 
			WHERE a.department != ''
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

			if ($dept == "MNGT") {
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
						LEFT JOIN miraimobile.emergency_surveys ON employee_syncs.employee_id = miraimobile.emergency_surveys.employee_id 
						and keterangan = '".$keterangan."'
					WHERE
						employee_syncs.end_date IS NULL 
						AND employee_syncs.department IS NULL 
						AND miraimobile.emergency_surveys.employee_id IS NULL");
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
						LEFT JOIN miraimobile.emergency_surveys ON employee_syncs.employee_id = miraimobile.emergency_surveys.employee_id 
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
						LEFT JOIN miraimobile.emergency_surveys ON employee_syncs.employee_id = miraimobile.emergency_surveys.employee_id 
						AND keterangan = '".$keterangan."'
						JOIN departments ON department_name = employee_syncs.department 
					WHERE
						employee_syncs.end_date IS NULL 
						AND miraimobile.emergency_surveys.employee_id IS NULL 
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
						LEFT JOIN miraimobile.emergency_surveys ON employee_syncs.employee_id = miraimobile.emergency_surveys.employee_id 
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

	public function indexSurveyCovid()
	{
		$title = 'Survey Covid-19';
		$title_jp = 'コロナ調査';

		return view('survey.index_covid', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Survey Covid')->with('head','Survey Covid');
	}

	public function fetchSurveyCovid(Request $request)
	{
		try {

			if ($request->get('tanggal') == "") {
				$survey = DB::SELECT("
					SELECT
						SUM( a.count_sudah ) AS sudah,
						SUM( a.count_belum ) AS belum,
						a.department,
						department_shortname
					FROM
						(
					SELECT
						count( miraimobile.survey_logs.employee_id ) AS count_sudah,
						0 AS count_belum,
						COALESCE (employee_syncs.department, '' ) AS department 
					FROM
						miraimobile.survey_logs
						JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id 
					WHERE employee_syncs.employee_id != 'PI1612005'
					GROUP BY
						employee_syncs.department
						
						UNION ALL
					SELECT
						0 AS count_sudah,
						count( employee_syncs.employee_id ) AS count_belum,
						COALESCE ( employee_syncs.department, '' ) AS department 
					FROM
						miraimobile.survey_logs
						RIGHT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id 
					WHERE
						miraimobile.survey_logs.employee_id IS NULL 
						AND employee_syncs.end_date IS NULL 
					  	AND employee_syncs.employee_id != 'PI1612005'
					GROUP BY
						employee_syncs.department 
						) a
						LEFT JOIN departments ON a.department = departments.department_name 
					WHERE a.department != ''
					GROUP BY
						a.department,
						departments.department_shortname
					");
			}
			else{

				$date = date('Y-m-d', strtotime($request->get("tanggal")));

				$survey = DB::SELECT("
					SELECT
						SUM( a.count_sudah ) AS sudah,
						SUM( a.count_belum ) AS belum,
						a.department,
						department_shortname
					FROM
						(
					SELECT
						count( miraimobile.survey_covid_logs.employee_id ) AS count_sudah,
						0 AS count_belum,
						COALESCE (employee_syncs.department, '' ) AS department 
					FROM
						miraimobile.survey_covid_logs
						JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id 
					WHERE 
						miraimobile.survey_covid_logs.tanggal = '".$date."' 
						AND employee_syncs.employee_id != 'PI1612005'
					GROUP BY
						employee_syncs.department
						
						UNION ALL

					SELECT 
						0 AS count_sudah,
						SUM(IF(mobile.employee_id is null,1,0)) as count_belum,
						COALESCE ( employee_syncs.department, '' ) AS department 
					FROM 
						(SELECT * from miraimobile.survey_covid_logs where tanggal = '".$date."') as mobile
						RIGHT JOIN employee_syncs ON employee_syncs.employee_id = mobile.employee_id 
						WHERE
						employee_syncs.end_date IS NULL 
						AND employee_syncs.employee_id != 'PI1612005'
						GROUP BY employee_syncs.department
					) a
						LEFT JOIN departments ON a.department = departments.department_name 
					WHERE a.department != ''
					GROUP BY
						a.department,
						departments.department_shortname
					");
			}

			if ($request->get('tanggal') == "") {

				$nilai = DB::SELECT("
				SELECT
					sum(CASE WHEN miraimobile.survey_logs.total <= 35 THEN 1 ELSE 0 END) AS jumlah_rendah,
					sum(CASE WHEN miraimobile.survey_logs.total > 35 AND miraimobile.survey_logs.total <= 80 THEN 1 ELSE 0 END) AS jumlah_sedang,
					sum(CASE WHEN miraimobile.survey_logs.total > 80 THEN 1 ELSE 0 END) AS jumlah_tinggi
				FROM
					miraimobile.survey_logs
					JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id 
				WHERE
					miraimobile.survey_logs.survey_code = 'covid' 
				");
			}
			else{

				$date = date('Y-m-d', strtotime($request->get("tanggal")));

				$nilai = DB::SELECT("
				SELECT
					sum( CASE WHEN miraimobile.survey_covid_logs.total <= 35 THEN 1 ELSE 0 END ) AS jumlah_rendah,
					sum( CASE WHEN miraimobile.survey_covid_logs.total > 35 AND miraimobile.survey_covid_logs.total <= 80 THEN 1 ELSE 0 END ) AS jumlah_sedang,
					sum( CASE WHEN miraimobile.survey_covid_logs.total > 80 THEN 1 ELSE 0 END ) AS jumlah_tinggi 
				FROM
					miraimobile.survey_covid_logs
					JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id 
				WHERE
					miraimobile.survey_covid_logs.tanggal = '".$date."'
				");
			}

			$response = array(
				'status' => true,
				'survey' => $survey,
				'nilai' => $nilai
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

	public function fetchSurveyCovidDetail(Request $request)
	{
		try {
		  $answer = $request->get('answer');
            $dept = $request->get('dept');

               if ($answer == "Belum") {
				if ($request->get('tanggal') == "") {
					$survey = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_logs
                          RIGHT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
                          department_shortname = '".$dept."'
                          and miraimobile.survey_logs.employee_id is null
					 and employee_syncs.employee_id != 'PI1612005'
                          and employee_syncs.end_date is null");


					$survey_info = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_logs
                          RIGHT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
                          miraimobile.survey_logs.employee_id is null
					 and employee_syncs.employee_id != 'PI1612005'
                          and employee_syncs.end_date is null");
			        }
			        else{
			            $date = date('Y-m-d', strtotime($request->get("tanggal")));

			            $survey = DB::SELECT("SELECT
							employee_syncs.employee_id,
							employee_syncs.name,
							COALESCE ( department_shortname, '' ) AS department 
						FROM
							( SELECT * FROM miraimobile.survey_covid_logs WHERE tanggal = '".$date."' ) AS mobile
							RIGHT JOIN employee_syncs ON employee_syncs.employee_id = mobile.employee_id
							JOIN departments ON department_name = employee_syncs.department 
						WHERE
							department_shortname = '".$dept."' 
							AND mobile.employee_id IS NULL 
							AND employee_syncs.employee_id != 'PI1612005'
							AND employee_syncs.end_date IS NULL");

			            $survey_info = DB::SELECT("
			            	SELECT
							employee_syncs.employee_id,
							employee_syncs.name,
							COALESCE ( department_shortname, '' ) AS department 
						FROM
							( SELECT * FROM miraimobile.survey_covid_logs WHERE tanggal = '".$date."' ) AS mobile
							RIGHT JOIN employee_syncs ON employee_syncs.employee_id = mobile.employee_id
							JOIN departments ON department_name = employee_syncs.department 
						WHERE
							mobile.employee_id IS NULL 
							AND employee_syncs.employee_id != 'PI1612005'
							AND employee_syncs.end_date IS NULL
					");
			        }

                     
                }else{
                	if ($request->get('tanggal') == "") {
                     	$survey = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_logs
                          LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
                          department_shortname = '".$dept."'
					 and employee_syncs.employee_id != 'PI1612005'
                          and employee_syncs.end_date is null");

                     	$survey_info = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_logs
                          LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
					 employee_syncs.employee_id != 'PI1612005'
                          and employee_syncs.end_date is null");

                 	}
			        else{
			            $date = date('Y-m-d', strtotime($request->get("tanggal")));

			            $survey = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_covid_logs
                          LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
                          department_shortname = '".$dept."'
                          and miraimobile.survey_covid_logs.tanggal = '".$date."'
                          and employee_syncs.end_date is null");

			           $survey_info = DB::SELECT("SELECT
	                     employee_syncs.employee_id,
	                     employee_syncs.name,
	                     COALESCE(department_shortname,'') as department
	                     FROM
	                     miraimobile.survey_covid_logs
	                     LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
	                     join departments on department_name = employee_syncs.department
	                     WHERE
	                     miraimobile.survey_covid_logs.tanggal = '".$date."'
	                     and employee_syncs.end_date is null");
			         }
               }

               
		  	$category = $request->get('category');

               if ($request->get('tanggal') == "") {
	               if ($category == "Rendah") {
	               	$survey_category = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_logs
                          LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
					 employee_syncs.employee_id != 'PI1612005'
					 and total <= 35
                          and employee_syncs.end_date is null");
	               }
	               else if ($category == "Sedang"){
	               	$survey_category = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_logs
                          LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
					 employee_syncs.employee_id != 'PI1612005'
					 and total > 35 and total <= 80
                          and employee_syncs.end_date is null");
	               }
	               else {
	               	$survey_category = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_logs
                          LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
					 employee_syncs.employee_id != 'PI1612005'
					 and total > 80
                          and employee_syncs.end_date is null");
	               }
	           }

	           else {
	               if ($category == "Rendah") {
	               	$survey_category = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_covid_logs
                          LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
					 employee_syncs.employee_id != 'PI1612005'
					 and total <= 35
					 and miraimobile.survey_covid_logs.tanggal = '".$date."'
                          and employee_syncs.end_date is null");
	               }
	               else if ($category == "Sedang"){
	               	$survey_category = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_covid_logs
                          LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
					 employee_syncs.employee_id != 'PI1612005'
					 and total > 35 and total <= 80
					 and miraimobile.survey_covid_logs.tanggal = '".$date."'
                          and employee_syncs.end_date is null");
	               }
	               else {
	               	$survey_category = DB::SELECT("SELECT
                          employee_syncs.employee_id,
                          employee_syncs.name,
                          COALESCE(department_shortname,'') as department
                          FROM
                          miraimobile.survey_covid_logs
                          LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
                          join departments on department_name = employee_syncs.department
                          WHERE
					 employee_syncs.employee_id != 'PI1612005'
					 and total > 80
					 and miraimobile.survey_covid_logs.tanggal = '".$date."'
                          and employee_syncs.end_date is null");
	               }
	          }

			$response = array(
				'status' => true,
				'survey' => $survey,
				'survey_info' => $survey_info,
				'survey_category' => $survey_category
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

	public function indexSurveyCovidReport()
	{
		$title = 'Report Survey Covid-19';
		$title_jp = 'コロナ調査報告';

		return view('survey.report_covid', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Report Survey Covid')->with('head','Report Survey Covid');
	}

	public function fetchSurveyCovidReport(Request $request)
	{
		try {
			$date_from = $request->get('tanggal_from');
	        $date_to = $request->get('tanggal_to');
	        if ($date_from == "") {
	             if ($date_to == "") {
	                  $where1 = "WHERE DATE( miraimobile.survey_logs.created_at ) BETWEEN DATE(NOW() - INTERVAL 7 DAY) AND DATE(NOW())";
	                  $where2 = "WHERE DATE( miraimobile.survey_covid_logs.created_at ) BETWEEN DATE(NOW() - INTERVAL 7 DAY) AND DATE(NOW())";
	             }else{
	                  $where1 = "WHERE DATE( miraimobile.survey_logs.created_at ) BETWEEN DATE('".$date_to."' - INTERVAL 7 DAY) AND '".$date_to."'";
	                  $where2 = "WHERE DATE( miraimobile.survey_covid_logs.created_at ) BETWEEN DATE('".$date_to."' - INTERVAL 7 DAY) AND '".$date_to."'";
	             }
	        }else{
	             if ($date_to == "") {
	                  $where1 = "WHERE DATE( miraimobile.survey_logs.created_at ) BETWEEN '".$date_from."' AND DATE(NOW())";
	                  $where2 = "WHERE DATE( miraimobile.survey_covid_logs.created_at ) BETWEEN '".$date_from."' AND DATE(NOW())";
	             }else{
	                  $where1 = "WHERE DATE( miraimobile.survey_logs.created_at ) BETWEEN '".$date_from."' AND '".$date_to."'";
	                  $where2 = "WHERE DATE( miraimobile.survey_covid_logs.created_at ) BETWEEN '".$date_from."' AND '".$date_to."'";
	             }
	        }

			$survey = DB::SELECT("
				SELECT
				miraimobile.survey_logs.id as id_survey,
				miraimobile.survey_logs.employee_id,
				employee_syncs.name,
				employee_syncs.department,
				employee_syncs.section,
				employee_syncs.`group`,
				employee_syncs.sub_group,
				miraimobile.survey_logs.tanggal,
				miraimobile.survey_logs.question,
				miraimobile.survey_logs.answer,
				miraimobile.survey_logs.poin,
				miraimobile.survey_logs.total,
				miraimobile.survey_logs.keterangan,
				miraimobile.survey_logs.created_at
			FROM
				miraimobile.survey_logs
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
				".$where1."
			UNION 

			SELECT
				miraimobile.survey_covid_logs.id as id_survey,
				miraimobile.survey_covid_logs.employee_id,
				employee_syncs.name,
				employee_syncs.department,
				employee_syncs.section,
				employee_syncs.`group`,
				employee_syncs.sub_group,
				miraimobile.survey_covid_logs.tanggal,
				miraimobile.survey_covid_logs.question,
				miraimobile.survey_covid_logs.answer,
				miraimobile.survey_covid_logs.poin,
				miraimobile.survey_covid_logs.total,
				miraimobile.survey_covid_logs.keterangan,
				miraimobile.survey_covid_logs.created_at
			FROM
				miraimobile.survey_covid_logs
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id 
				".$where2."
				order by created_at desc 
");
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

	public function fetchSurveyCovidReportDetail(Request $request)
	{
		try {
			$survey = DB::SELECT("
			SELECT
				miraimobile.survey_logs.id as id_survey,
				miraimobile.survey_logs.employee_id,
				employee_syncs.name,
				employee_syncs.department,
				employee_syncs.section,
				employee_syncs.`group`,
				employee_syncs.sub_group,
				miraimobile.survey_logs.tanggal,
				miraimobile.survey_logs.question,
				miraimobile.survey_logs.answer,
				miraimobile.survey_logs.poin,
				miraimobile.survey_logs.total,
				miraimobile.survey_logs.keterangan
			FROM
				miraimobile.survey_logs
				JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
				where miraimobile.survey_logs.employee_id = '".$request->get('employee_id')."'
				and miraimobile.survey_logs.tanggal = '".$request->get('tanggal')."'
				
			UNION 

			SELECT
				miraimobile.survey_covid_logs.id as id_survey,
				miraimobile.survey_covid_logs.employee_id,
				employee_syncs.name,
				employee_syncs.department,
				employee_syncs.section,
				employee_syncs.`group`,
				employee_syncs.sub_group,
				miraimobile.survey_covid_logs.tanggal,
				miraimobile.survey_covid_logs.question,
				miraimobile.survey_covid_logs.answer,
				miraimobile.survey_covid_logs.poin,
				miraimobile.survey_covid_logs.total,
				miraimobile.survey_covid_logs.keterangan 
			FROM
				miraimobile.survey_covid_logs
				JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
				where miraimobile.survey_covid_logs.employee_id = '".$request->get('employee_id')."'
				and miraimobile.survey_covid_logs.tanggal = '".$request->get('tanggal')."'
			");
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
