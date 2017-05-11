<?php

namespace App\Http\Controllers;

use App\Stat;
use App\User;
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

    public function index() {
	    $users = User::all();
	    $months = [1,2,3,4,5,6,7,8,9,10,11,12];
    	return view('home', compact('users', 'months'));
    }

    public function date($date)
    {
		$users = User::all();
		$month = intval(substr($date, 0, 2));
		$year = intval(substr($date, -4));
        return view('date', compact('users', 'month', 'year'));
    }
}
