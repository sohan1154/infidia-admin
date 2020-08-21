<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriptions;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Input;
use DB;
use Session;
use Excel;

class SubscriptionController extends Controller
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
        $subscription = Subscriptions::get();
        return view('subscriptions/index',compact('subscription'));
    }

    public function create(){
        return view('subscriptions/create');
    }

    public function add(Request $request){


        $input = $request->all();

        /*$validator = validator::make($request->all(), [
            'name' => 'required|max:255',
            'price' => 'required',
        ]);*/
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required',
        ]);

       /* if ($validator->fails()) {
            return redirect()->action('SubscriptionController@create')
                            ->withErrors($validator)
                            ->with('alert-danger','Failed to add plan.');
        }*/
        $input['status'] = '1';
        $products = Subscriptions::create($input);
		
        return redirect()->action('SubscriptionController@index')->with('alert-success', 'Subscription Plan Added Successfully');
    }


	public function update($sid){
        $id = base64_decode($sid);
		if ($id == '') {
            return 'URL NOT FOUND';
        }
		$subscriptions = Subscriptions::find($id);
		if (empty($subscriptions)) {
            return 'URL NOT FOUND';
        } 
		return view('subscriptions/edit',compact('subscriptions'));
	}

	public function edit(Request $request, $ids) {
        $id = base64_decode($ids);
        if ($id == '') {
            return 'URL NOT FOUND';
        }

        $subscriptions =  Subscriptions::find($id);
        if (empty($subscriptions)) {
            return 'URL NOT FOUND';
        }

        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required',
        ]);

        $input = $request->all();
		
		unset($input['_token']);
		
		$subscriptions->fill($input)->save();

        return redirect()->action('SubscriptionController@index')->with('alert-success', 'Subscription Plan Updated Successfully');
    }

    public function status($ids,$status) {
        $id = base64_decode($ids);        
        $subscriptions =  Subscriptions::find($id);
        if (empty($subscriptions)) {
            return 'URL NOT FOUND';
        }

        $input['status'] = $status;
        unset($input['_token']);
        
        $subscriptions->fill($input)->save();

        return redirect()->action('SubscriptionController@index')->with('alert-success', 'Subscription Plan Status Updated Successfully');
    }


	public function delete($id) {
		
        $sid = base64_decode($id);
		$subscriptions = Subscriptions::find($sid);
		$subscriptions->delete();
		return redirect()->action('SubscriptionController@index')->with('alert-success', 'Subscription Plan Deleted Successfully');
    }

}
