<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Attribute;
use App\Models\Categories;
use App\Http\Controllers\Controller;
use Validator;

class AttributeController extends Controller
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
		
		$attributes = Attribute::all();
		$categories = Categories::whereHas('attributes')->get();
		
		return view('attributes/index',compact('attributes', 'categories'));
    }
	
	public function create(){
        
        $categories = Categories::where('is_deleted',0)->where('status','1')->where('type', 'product')->orderBy('name','asc')->get();         
        
        return view('attributes/create', compact('categories'));
	}
	
	public function add(Request $request){
		$input = $request->all();

		$validator = validator::make($request->all(), [
            // 'name' => 'required|max:100|unique:attributes', 
            'name' => 'required|max:100', 
            'category_id' => 'required', 
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->action('AttributeController@create')
                            ->withErrors($validator)
                            ->with('alert-danger','Attribute can\'t be added please try again.');
        }
		
		Attribute::create($input);

		return redirect()->action('AttributeController@index')->with('alert-success', 'Attribute Added Successfully');
    }
	
	public function update($ids){

		$id = base64_decode($ids);
		if ($id == '') {
            return 'URL NOT FOUND';
        }
		
		$attributes = Attribute::find($id);
		if (empty($attributes)) {
            return 'URL NOT FOUND';
        }

        $categories = Categories::where('is_deleted',0)->where('status','1')->where('type', 'product')->orderBy('name','asc')->get();         
		
        return view('attributes/edit',compact('attributes', 'categories'));
	}
	
	public function edit(Request $request, $ids) {

		$id = base64_decode($ids);
		if ($id == '') {
            return 'URL NOT FOUND';
		}
		
		$validator = validator::make($request->all(), [
            'name' => 'required|max:100|unique:attributes,name,'.$id, 
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->action('AttributeController@update', [base64_encode($id)])
                            ->withErrors($validator)
                            ->with('alert-danger','Attribute can\'t be updated please try again.');
		}
		
		$attributes = Attribute::findOrFail($id);
		if (empty($attributes)) {
            return 'URL NOT FOUND';
        }
		$input = $request->all();
		
		$attributes->fill($input)->save();

		return redirect()->action('AttributeController@index')->with('alert-success', 'Attribute Updated Successfully');
    }
	
	public function delete($ids) {

		$id = base64_decode($ids);

		Attribute::find($id)->delete(); 
		
		return redirect()->action('AttributeController@index')->with('alert-success', 'Attribute Deleted Successfully');
    }

    public function status($ids,$status) {

        $id = base64_decode($ids);        
        $Attribute =  Attribute::find($id);
        if (empty($Attribute)) {
            return 'URL NOT FOUND';
        }

        $input['status'] = $status;
        unset($input['_token']);
        
        $Attribute->fill($input)->save();

        return redirect()->action('AttributeController@index')->with('alert-success', 'Attribute Status Updated Successfully');
    }
	
}
