<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Pages;
use App\Http\Controllers\Controller;
use Validator;
use URL;

class PageController extends Controller
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
        $pages = Pages::all();
        return view('pages/index',compact('pages'));
    }
    
    public function create(){
        
        $pages = Pages::all();
        return view('pages/create',compact('pages'));
	}
    
    public function add(Request $request){


        $input = $request->all();
		 $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'status' => 'required',
			
        ]);
        if($request->hasFile('image')) {

            //$image_array = [];

            //foreach ($request->file('image') as $image) {
                
                $image = '';
                $uploadpath = public_path().'\images';
                //$original_name = $input['image']->getClientOriginalName();
				$original_name = $request->file('image')->getClientOriginalName();

                /*if (!$request->file('image')->isValid() || empty($uploadpath)) {
                    return $image;
                }*/
				//dd($input['image']);
                if (!empty($request->file('image'))) {
                    $image_prefix = 'banner_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                    $ext = $request->file('image')->getClientOriginalExtension();
                    $image = $image_prefix . '.' . $ext;
                    //$image_array[] = $image;
                    $request->file('image')->move($uploadpath, $image);
                }
            //}
        }

        // video upload
        if($request->hasFile('video_file')) {

            $file = '';
            $uploadpath = public_path().'\videos\pages';
            $original_name = $request->file('video_file')->getClientOriginalName();

            if (!empty($request->file('video_file'))) {
                $file_prefix = 'page_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                $ext = $request->file('video_file')->getClientOriginalExtension();
                $file = $file_prefix . '.' . $ext;
                $request->file('video_file')->move($uploadpath, $file);
            }
        }
		
		if(empty($image)){
			$input['image'] = '';
		}
		else{
			
			$input['image'] = $image;
		}
		
		//dd($input);

        $pages = Pages::create($input);

        return redirect()->action('PageController@index')->with('alert-success', 'Page Added Successfully');
    }
    
    public function update($pageid=null){
        $id = base64_decode($pageid);
		if ($id == '') {
            return 'URL NOT FOUND';
        }
		
		$pages = Pages::find($id);
		if (empty($pages)) {
            return 'URL NOT FOUND';
        }
        $pages = Pages::find($id);
		//dd($pages );
        return view('pages/edit',compact('pages'));
	}
    
    public function edit(Request $request, $pageid) {
        $id = base64_decode($pageid);
        if ($id == '') {
            return 'URL NOT FOUND';
        }

        $pages = Pages::findOrFail($id);

        if (empty($pages)) {
            return 'URL NOT FOUND';
        }

       

        $input = $request->all();
		$request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'status' => 'required',
			
        ]);
		//image update
       if($request->hasFile('image')) {

            //$image_array = [];

            //foreach ($request->file('image') as $image) {
                
                $image = '';
                $uploadpath = public_path().'\images';
                //$original_name = $input['image']->getClientOriginalName();
				$original_name = $request->file('image')->getClientOriginalName();

                /*if (!$request->file('image')->isValid() || empty($uploadpath)) {
                    return $image;
                }*/
				//dd($input['image']);
                if (!empty($request->file('image'))) {
                    $image_prefix = 'page_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                    $ext = $request->file('image')->getClientOriginalExtension();
                    $image = $image_prefix . '.' . $ext;
                    //$image_array[] = $image;
                    $request->file('image')->move($uploadpath, $image);
                }
            //}
        }

		// video upload
        if($request->hasFile('video_file')) {

            $file = '';
            $uploadpath = public_path().'\videos\pages';
            $original_name = $request->file('video_file')->getClientOriginalName();

            if (!empty($request->file('video_file'))) {
                $file_prefix = 'page_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                $ext = $request->file('video_file')->getClientOriginalExtension();
                $file = $file_prefix . '.' . $ext;
                $request->file('video_file')->move($uploadpath, $file);
            }
        }

        
       if(empty($image)){
			//$input['image'] = '';
		}
		else{
			$input['image'] = $image;
		}
        $pages->fill($input)->save();

        return redirect()->action('PageController@index')->with('alert-success', 'Page Updated Successfully');
    }
	
	public function delete($pageid) {
        $id = base64_decode($pageid);
        Pages::find($id)->delete(); 
		return redirect()->action('PageController@index')->with('alert-success', 'Page Deleted Successfully');
    }
	
	public function status($ids,$status) { 
        $ids = base64_decode($ids);       
        $pages =  Pages::find($ids);
        if (empty($pages)) {
            return 'URL NOT FOUND';
        }

        $input['status'] = $status;
        unset($input['_token']);
        
        $pages->fill($input)->save();

        return redirect()->action('PageController@index')->with('alert-success', 'Page Status Updated Successfully');
    }
	
	public function uploadImage(Request $request) {

        // dump($request->input('CKEditorFuncNum'));die;

        $uploadpath = public_path('/images/pages');

        if (!empty($request->file('upload'))) {
            $image_prefix = 'page_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
            $ext = $request->file('upload')->getClientOriginalExtension();
            $image = $image_prefix . '.' . $ext;
            $request->file('upload')->move($uploadpath, $image);

            $url = asset('images/pages/'.$image);
            die('Please copy this url and put into (Image Info -> URL) input box '.$url); die;

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $msg = 'Image uploaded successfully'; 
            $response = "<script>window.opener.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
               
            @header('Content-type: text/html; charset=utf-8'); 
            echo $response;
        }

    }
	
}
