<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Models\Orders;
use App\Models\OrderReturn;
use App\Models\Orderdelivery;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use Auth;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
           $this->middleware('auth', ['except' => ['thankyou']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        
        $user_id = getUrlOrAuthId($request);
        //$role_id = Auth::user()->role;
        $role_id = getUserRole($user_id);

        $userName = userName($user_id);

        $roleInUrl = getRoleBaseUrl($role_id);
        
        $query = Orders::query();

        if($role_id=='Admin'){
            //$query->where('seller_id', $user_id);
        }
        else if($role_id=='Seller'){
            $query->where('seller_id', $user_id);
        }
        else if($role_id=='Buyer'){
            $query->where('user_id', $user_id);
        }
        else if($role_id=='Delivery'){
            $query->where('delivery_boy_id', $user_id);
        }
        else {
            Auth::logout();
            redirect('login');
        }

        $query->orderBy('id', 'desc');

        $orders = $query->get();

        return view('orders/index',compact('orders', 'user_id', 'roleInUrl', 'userName'));
    }
    
	public function view(Request $request, $pid){
        $id = base64_decode($pid);

        $user_id = getUrlOrAuthId($request);
        //$role_id = Auth::user()->role;
        $role_id = getUserRole($user_id);

        $userName = userName($user_id);

        $roleInUrl = getRoleBaseUrl($role_id);

        $orders = Orders::where('id',$id)->first();
        $deliveryBoy = Orderdelivery::where('order_id',$orders->id)->first();
        
        return view('orders/view',compact('orders', 'deliveryBoy', 'user_id', 'roleInUrl', 'userName'));
    }
	
	public function thankyou(){
        return view('thankyou');
    }

    /**
     * Show the application
     *
     * @return \Illuminate\Http\Response
     */
    public function returned_orders(Request $request){
        
        $user_id = getUrlOrAuthId($request);
        //$role_id = Auth::user()->role;
        $role_id = getUserRole($user_id);

        $userName = userName($user_id);

        $roleInUrl = getRoleBaseUrl($role_id);
        
        $query = Orders::query();

        if($role_id=='Admin'){
            //$query->where('seller_id', $user_id);
        }
        else if($role_id=='Seller'){
            $query->where('seller_id', $user_id);
        }
        else if($role_id=='Buyer'){
            $query->where('user_id', $user_id);
        }
        else if($role_id=='Delivery'){
            $query->where('delivery_boy_id', $user_id);
        }
        else {
            Auth::logout();
            redirect('login');
        }

        $productRefundedOrderIds = OrderReturn::pluck('order_id', 'order_id');

        $query->whereIn('id', $productRefundedOrderIds)->orWhere('is_cancelled', 1);

        $query->orderBy('id', 'desc');

        $orders = $query->get();

        return view('orders/returned_orders',compact('orders', 'user_id', 'roleInUrl', 'userName'));
    }
    
    public function view_returned_products(Request $request, $pid){
        $id = base64_decode($pid);

        $user_id = getUrlOrAuthId($request);
        //$role_id = Auth::user()->role;
        $role_id = getUserRole($user_id);

        $userName = userName($user_id);

        $roleInUrl = getRoleBaseUrl($role_id);

        $orders = Orders::where('id',$id)->first();
        $deliveryBoy = Orderdelivery::where('order_id',$orders->id)->first();
        
        return view('orders/view_returned_products',compact('orders', 'deliveryBoy', 'user_id', 'roleInUrl', 'userName'));
    }
    
    public function accept_return_request(Request $request, $pid){

        try {
            $id = base64_decode($pid);

            $user_id = getUrlOrAuthId($request);
            //$role_id = Auth::user()->role;
            $role_id = getUserRole($user_id);

            $userName = userName($user_id);

            $roleInUrl = getRoleBaseUrl($role_id);

            $orders = Orders::where('id',$id)->first();

            if(empty($orders)) {
                $result = array( 'status' => 'success', 'message' => 'Record deleted sucessfully.');
            }

        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            $result = array('status' => 'error', 'message' => $errorMsg);
        }
        
		return back()->with($result['status'], $result['message']);
    }
	
}
