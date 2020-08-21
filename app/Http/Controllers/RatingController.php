<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Models\Reviews;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use URL;

class RatingController extends Controller
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
        
		//dd($orders);
        $user_id = Auth::user()->id;
        $role_id = Auth::user()->role;
        if($role_id=='Buyer'){
            Auth::logout();
            redirect('login');
        } else if($role_id=='Seller'){
            $rating = Reviews::where('seller_id',$user_id)->get();
        } else if($role_id=='Admin'){
            $rating = Reviews::all();
        }
        return view('ratings/index',compact('rating'));
    }
	######
}