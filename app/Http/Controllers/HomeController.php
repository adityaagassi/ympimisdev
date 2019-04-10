<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('home')->with('page', 'Dashboard');
    }

    public function indexAboutMIS(){
        $projects = db::table('mis_investments')->orderby('start_date', 'asc')
        ->leftJoin('mis_investment_details', 'mis_investments.project', '=', 'mis_investment_details.project')
        ->select('mis_investments.project', 'mis_investments.description', 'mis_investments.start_date', db::raw('if(mis_investments.finish_date is null or mis_investments.finish_date = "0000-00-00", "On Going", mis_investments.finish_date) as finish_date'), db::raw('coalesce(sum((mis_investment_details.qty*mis_investment_details.price)),0) as total_investment'))
        ->groupBy('mis_investments.project', 'mis_investments.description', 'mis_investments.start_date', 'mis_investments.finish_date')
        ->get();

        return view('about_mis.about_mis', array(
            'projects' => $projects,
        ))->with('page', 'About MIS');
    }

    public function download($reference_file){
        if (file_exists(public_path() . "/manuals/" . $reference_file)) {
            header("Content-Length: " . filesize(public_path() . "/manuals/" . $reference_file));
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $reference_file);
            readfile(public_path() . "/manuals/" . $reference_file);
            exit();
        }
        else {
            return view('404');
        }
    }
}
