<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    public function index()
    {
		$time_access = \Auth::user()->time_access;
		$left_hours = 0;
		$left_minutes = 0;
		
		if($time_access > time()){
			
			$left_hours = floor(($time_access-time())/3600);
			if($left_hours) $left_minutes = floor((($time_access - (time()+($left_hours*3600)))/3600)*60); 
			else $left_minutes = floor(($time_access-time())/60); 
		}
        return view('home')->with(['left_hours' => $left_hours, 'left_minutes' =>$left_minutes]);
    }
}
