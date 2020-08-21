<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Categories;
use App\Models\Orders;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use DB;
use Image;
use Auth;
class CategoriesController extends Controller
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
	
	public function category(){

        $categories = Categories::where('is_deleted',0)->where('type', 'business')->orderBy('id', 'asc')->get();
        
		return view('categories/index',compact('categories'));
	}
	
	public function add_category() {

        $categories = Categories::where('parent_id', 0)->where('is_deleted',0)->orderBy('name', 'asc')->get();

		return view('categories/add',compact('categories'));
	}

	public function insert_cat(Request $request){

		$input = $request->all();
		$validator = validator::make($request->all(), [
          'name' => 'required|max:255',//|unique:categories',
		  'status' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->action('CategoriesController@add_category')
                ->withErrors($validator)
                ->with('alert-danger','Category Name can not be same.');
        }

        if($request->file('image')) {

            $image_name = '';
            
            if (!empty($request->file('image'))) {
                
                $image_prefix = 'category_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                $ext = $request->file('image')->getClientOriginalExtension();
                $image_name = $image_prefix . '.' . $ext;
                $original_name = $request->file('image')->getClientOriginalName();
                $image_resize = Image::make($request->file('image')->getRealPath());
                $image_resize->resize(480,320);
                $image_resize->save(public_path('images/categories/' .$image_name));
            }
        }
	
		if(empty($input['image'])){
			//$input['banner_image'];
		} else{
			$input['image'] = $image_name;
		}
	
        $categories = Categories::create($input);

		return redirect()->action('CategoriesController@category')->with('alert-success', 'Category Added Successfully');
	}

	public function edit_category($uid){
        
        $id = base64_decode($uid);
		if ($id == '') {
            return 'URL NOT FOUND';
        }

		$category = Categories::find($id);
		if (empty($category)) {
            return 'URL NOT FOUND';
        }
        
        $categories = Categories::where('parent_id', 0)->where('is_deleted',0)->orderBy('name', 'asc')->get();

        return view('categories/edit',compact('category', 'categories'));
	}

    public function status_category($ids,$status) {
        $ids = base64_decode($ids);     
        $category =  Categories::find($ids);
        if (empty($category)) {
            return 'URL NOT FOUND';
        }

        $input['status'] = $status;
        unset($input['_token']);
        
        $category->fill($input)->save();

        return redirect()->back()->with('alert-success', 'Category Status Updated Successfully');
    }

	public function update_cat(Request $request, $id) {
		
        if ($id == '') {
            return 'URL NOT FOUND';
        }

        $category = Categories::findOrFail($id);

        if (empty($category)) {
            return 'URL NOT FOUND';
        }

        $input = $request->all();
		$request->validate([
            'name' => 'required|max:255',
            'status' => 'required',
			
        ]);
		if($request->file('image')) {
                
            $image_name = '';
            
            if (!empty($request->file('image'))) {
                
                $image_prefix = 'category_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                $ext = $request->file('image')->getClientOriginalExtension();
                $image_name = $image_prefix . '.' . $ext;
                $original_name = $request->file('image')->getClientOriginalName();
                $image_resize = Image::make($request->file('image')->getRealPath());
                $image_resize->resize(480,320);
                $image_resize->save(public_path('images/categories/' .$image_name));
            }
        }
		
		if(empty($input['image'])){
			//$input['banner_image'];
		} else{
			$input['image'] = $image_name;
		}
        
        $category->fill($input)->save();

        return redirect()->action('CategoriesController@category')->with('alert-success', 'Category Updated Successfully');
    }

	public function delete_cat($id) {
        
        $id = base64_decode($id);
        
        Categories::findOrFail($id)->delete();   
        
        return redirect()->action('CategoriesController@category')->with('alert-success', 'Category Deleted Successfully');
    }

    public function product_categories($parent_id){

        $parent_id_normal = base64_decode($parent_id);

        $parent_category = Categories::where('id', $parent_id_normal)->first();
        $categories = Categories::where('is_deleted',0)->where('parent_id', $parent_id_normal)->orderBy('id', 'asc')->get();
        
		return view('categories/list_product_category',compact('categories', 'parent_id', 'parent_category'));
    }
    
    public function add_product_category($parent_id) {
        
        $parent_id = base64_decode($parent_id);
        $parent_category = Categories::where('id', $parent_id)->first();

        return view('categories/add_product_category',compact('parent_id', 'parent_category'));
    }
    
    public function insert_product_cat(Request $request){

        $input = $request->all();
        
        $parent_id = base64_encode($input['parent_id']);

		$validator = validator::make($request->all(), [
          'name' => 'required|max:255',//|unique:categories',
		  'status' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->action('CategoriesController@add_product_category', [$parent_id])
                ->withErrors($validator)
                ->with('alert-danger','Category Name can not be same.');
        }
	
        $categories = Categories::create($input);

		return redirect()->action('CategoriesController@product_categories', [$parent_id])->with('alert-success', 'Category Added Successfully');
    }
    
    public function edit_product_category($parent_id, $uid){
        
        $parent_id = base64_decode($parent_id);

        $id = base64_decode($uid);

		if ($id == '') {
            return 'URL NOT FOUND';
        }

		$category = Categories::find($id);
		if (empty($category)) {
            return 'URL NOT FOUND';
        }
        
        $parent_category = Categories::where('id', $parent_id)->first();

        $categories = Categories::where('parent_id', 0)->where('is_deleted',0)->orderBy('name', 'asc')->get();

        return view('categories/edit_product_category',compact('category', 'parent_id', 'parent_category'));
    }
    
    public function update_product_cat(Request $request, $id) {
		
        if ($id == '') {
            return 'URL NOT FOUND';
        }

        $category = Categories::findOrFail($id);

        if (empty($category)) {
            return 'URL NOT FOUND';
        }

        $input = $request->all();
        $parent_id = base64_encode($input['parent_id']);

		$request->validate([
            'name' => 'required|max:255',
            'status' => 'required',
        ]);
        
        $category->fill($input)->save();

        return redirect()->action('CategoriesController@product_categories', [$parent_id])->with('alert-success', 'Category Updated Successfully');
    }

    public function delete_product_cat($parent_id, $id) {
        
        //$parent_id = base64_decode($parent_id);
        $id = base64_decode($id);
        
        Categories::findOrFail($id)->delete();   

        return redirect()->action('CategoriesController@product_categories', [$parent_id])->with('alert-success', 'Category Deleted Successfully');
    }

}
