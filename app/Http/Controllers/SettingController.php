<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Settings;
use App\Http\Controllers\Controller;
use Validator;
use URL;

class SettingController extends Controller
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
        $settings = Settings::all();
        return view('settings/index',compact('settings'));
    }
	
	public function update(Request $request){
		$input = $request->all();
		print_r($input);
		
		foreach(array_combine($input['option_id'],$input['option_value']) as $id=>$option_value){
			$setting = Settings::findOrFail($id);
			$data['option_value'] = $option_value;
			
			Settings::where('id',$id)->update($data);
			$setting->fill($data)->save();
		}
        return redirect()->action('SettingController@index')->with('alert-success', 'Setting Updated Successfully');
    }
	######
	
	######
	
	######
	
	######
	
	
	
	
}
