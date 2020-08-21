<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use App\User;
use App\Models\Products;
use App\Models\Orders;
use App\Models\UserAddress; 
use App\Models\Orderdelivery; 

class CustomersController extends Controller
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

    public function index(Request $request){

        $seller_id = getUrlOrAuthId($request);

        $getCustomerIds = Orders::where('seller_id', $seller_id)->groupBy('user_id')->pluck('user_id', 'id');

        $customers = User::whereIn('id',$getCustomerIds)->where('is_deleted','0')->get();
        
        return view('customers/index',compact('customers'));
	}

    public function orders(Request $request, $userId){

        $seller_id = getUrlOrAuthId($request);
        $user_id = base64_decode($userId);

        $orders = Orders::where('seller_id', $seller_id)->where('user_id', $user_id)->orderBy('id', 'desc')->get();

        $user = User::where('id', $user_id)->first();

        return view('customers/orders',compact('orders', 'user_id', 'user'));
	}
	
	public function view($userId){
        $id             = base64_decode($userId);
        $users          = User::where('id',$id)->first();
        $userAddress    = UserAddress::where('user_id',$id)->get();
        return view('customers/view',compact('users','userAddress'));
    }
	
	public function orderView(Request $request, $user_id, $orderId){
        $user_id = base64_decode($user_id);
        $orderId = base64_decode($orderId);

        $seller_id = getUrlOrAuthId($request);

        $orders = Orders::where('id',$orderId)->first();
        $deliveryBoy = Orderdelivery::where('order_id',$orders->id)->first();
        
        return view('customers/order-view',compact('orders', 'deliveryBoy', 'user_id'));
    }
	
}
