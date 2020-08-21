<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\User;
use App\Models\Orders;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use DB;
use Image;
use Auth;

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
        $sellerUser      = User::where('role','Seller')->where('is_deleted','0')->whereNotNull('name')->count();
        $buyerUser       = User::where('role','Buyer')->where('is_deleted','0')->whereNotNull('name')->count();
        $deliveryUser    = User::where('role','Delivery')->where('is_deleted','0')->whereNotNull('name')->count();
		
        $userRecode      = User::where('is_deleted','0')->select("created_at" ,DB::raw("(COUNT(*)) as total"))->orderBy('created_at','desc')->groupBy(DB::raw("YEAR(created_at),MONTH(created_at)"))->get();
        $userRecodecount = User::where('is_deleted','0')->select(DB::raw("(COUNT(*)) as total"))->orderBy('total','desc')->groupBy(DB::raw("YEAR(created_at),MONTH(created_at)"))->first();

        $seller_id = Auth::user()->id;
        $role_id = Auth::user()->role;
		$orderRecode = array();
        if($role_id=='Buyer'){
            Auth::logout();
            redirect('login');
        } else if($role_id=='Seller'){
			$orders          = Orders::where('seller_id',$seller_id)->count();
			$payment         = Orders::where('seller_id',$seller_id)->sum('total_amount');
            $orderRecode     = DB::table("orders")->select("created_at" ,DB::raw("(COUNT(*)) as total"))->where('seller_id',$seller_id)->orderBy('created_at','desc')->groupBy(DB::raw("YEAR(created_at),MONTH(created_at)"))->get();

            $orderRecodecount= DB::table("orders")->select(DB::raw("(COUNT(*)) as total"))->orderBy('total','desc')->where('seller_id',$seller_id)->groupBy(DB::raw("YEAR(created_at),MONTH(created_at)"))->first();
        } else if($role_id=='Admin'){
			
			$orders          = Orders::count();
			$payment         = Orders::sum('total_amount');
            $orderRecode     = DB::table("orders")->select("created_at" ,DB::raw("(COUNT(*)) as total"))->orderBy('created_at','desc')->groupBy(DB::raw("YEAR(created_at),MONTH(created_at)"))->get();

            $orderRecodecount= DB::table("orders")->select(DB::raw("(COUNT(*)) as total"))->orderBy('total','desc')->groupBy(DB::raw("YEAR(created_at),MONTH(created_at)"))->first();
        }        
			
        return view('home',compact('sellerUser','buyerUser','deliveryUser','orders','payment','userRecode','userRecodecount','orderRecode','orderRecodecount'));
    }
	
	public function verifyResetPasswordToken(Request $request,$token) {
        $user = User::where('random_token',$token)->first();
        if ($user['token']==$token) {
            echo 'Authorised Access';
            $category->fill($input)->save();
        } else {
            echo 'Unauthorised Access';
        }
        return view('verifyresetpasswordtoken',compact('user','token'));
    }

}
