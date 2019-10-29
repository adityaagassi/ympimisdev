<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use App\QcCpar;

class QcCarController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    
}
