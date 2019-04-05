<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VisitorController extends Controller
{
	public function index()
	{
		return view('visitors.index')->with('page', 'Visitor Index');
	}

	public function registration()
	{
		return view('visitors.registration')->with('page', 'Visitor Registration');
	}
}
