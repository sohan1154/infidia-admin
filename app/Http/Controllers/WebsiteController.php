<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use Image;

class WebsiteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index() {
        return view('website/index');
    }

    public function about() {
        return view('website/about');
    }

    public function business() {
        return view('website/business');
    }

    public function privacy() {
        return view('website/privacy');
    }

    public function terms() {
        return view('website/terms');
    }

    public function contact() {
        return view('website/contact');
    }
	
}
