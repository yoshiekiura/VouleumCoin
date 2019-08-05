<?php

namespace App\Http\Controllers;


use App\Models\User;
use IcoData;
use Auth;
use Cookie;



class HomeController extends Controller
{

    public function home()
    {

        return view('home');
    }



}
