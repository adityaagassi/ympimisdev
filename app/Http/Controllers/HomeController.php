<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;

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
