<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\HomePageBanner;
// use App\User;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use Image;

class BannerController extends Controller
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
        $banners = HomePageBanner::all();

        return view('banners/index',compact('banners'));
    }
    
	public function create(){
        
        $users = User::where('role', 'Seller')->where('status', 1)->pluck('name', 'id');
        
        return view('banners/create',compact('users'));
    }
    
	public function add(Request $request){

        $input = $request->all();
        
		$request->validate([
            'banner_name' => 'required',
            'banner_image' => 'required',
            'status' => 'required',
        ]);
		
        $image_name = '';
        if($request->hasFile('banner_image')) {

            if (!empty($request->file('banner_image'))) {
                $image_prefix = 'banner_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                $ext = $request->file('banner_image')->getClientOriginalExtension();
                $image_name = $image_prefix . '.' . $ext;
                $original_name = $request->file('banner_image')->getClientOriginalName();
                $image_resize = Image::make($request->file('banner_image')->getRealPath());
                $image_resize->resize(1024,768);
                $image_resize->save(public_path('images/banners/' .$image_name));
            }
        }

		if(empty($input['banner_image'])) {
			//$input['banner_image'];
		} else {
			$input['banner_image'] = $image_name;
		}

        $banners = HomePageBanner::create($input);

        return redirect()->action('BannerController@index')->with('alert-success', 'Banner Added Successfully');
    }
    
	public function update($bid=null){
        $id = base64_decode($bid);
		if ($id == '') {
            return 'URL NOT FOUND';
        }
		
		$banners = HomePageBanner::find($id);
		if (empty($banners)) {
            return 'URL NOT FOUND';
        }
        
        $users = User::where('role', 'Seller')->where('status', 1)->pluck('name', 'id');
        
        return view('banners/edit',compact('banners', 'users'));
	}
	
	public function edit(Request $request, $bid) {

        $id = base64_decode($bid);
        if ($id == '') {
            return 'URL NOT FOUND';
        }

        $banners = HomePageBanner::findOrFail($id);

        if (empty($banners)) {
            return 'URL NOT FOUND';
        }

        $input = $request->all();
        
		$request->validate([
            'banner_name' => 'required',
            //'banner_image' => 'required',
            'status' => 'required',
        ]);
        
        $image_name = '';
       if($request->hasFile('banner_image')) {

            $original_name = $request->file('banner_image')->getClientOriginalName();

            if (!empty($request->file('banner_image'))) {
                $image_prefix = 'banner_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                $ext = $request->file('banner_image')->getClientOriginalExtension();
                $image_name = $image_prefix . '.' . $ext;
                $original_name = $request->file('banner_image')->getClientOriginalName();
                $image_resize = Image::make($request->file('banner_image')->getRealPath());
                $image_resize->resize(1024,768);
                $image_resize->save(public_path('images/banners/' .$image_name));
            }
        }

        if(empty($input['banner_image'])) {
			//$input['banner_image'];
		} else {
			$input['banner_image'] = $image_name;
		}
		
        $banners->fill($input)->save();

        return redirect()->action('BannerController@index')->with('alert-success', 'Banner Updated Successfully');
    }
	
	public function delete($bid) {
        $id = base64_decode($bid);

        HomePageBanner::find($id)->delete();

		return redirect()->action('BannerController@index')->with('alert-success', 'Banner Deleted Successfully');
    }

	public function status($ids,$status) {   
        $ids = base64_decode($ids);

        $banners =  HomePageBanner::find($ids);
        if (empty($banners)) {
            return 'URL NOT FOUND';
        }

        $input['status'] = $status;
        unset($input['_token']);
        
        $banners->fill($input)->save();

        return redirect()->action('BannerController@index')->with('alert-success', 'Banner Status Updated Successfully');
    }
    
	public function bulkAction(Request $request) {
        $input = $request->all();
        dump($input);die;

        $ids = [];
        foreach($input['rows'] as $value) {
            if(!empty($value['status']))
                $ids[] = $value['id'];
        }
        dump($ids);die;

        if($input['action'] = 'active') {
            $message = 'Banner Status Updated Successfully';
            HomePageBanner::whereIn('id', $ids)->update(['status' => 1]);
        }
        elseif($input['action'] = 'deactive') {
            $message = 'Banner Status Updated Successfully';
            HomePageBanner::whereIn('id', $ids)->update(['status' => 0]);
        }
        elseif($input['action'] = 'delete') {
            $message = 'Banner Deleted Successfully';
            HomePageBanner::whereIn('id', $ids)->delete();
        }
        else {
            $message = 'Rows are not selected';
        }

        return redirect()->action('BannerController@index')->with('alert-success', $message);
    }
	
}
