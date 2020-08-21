<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\BusinessCategories;
use App\Models\ProductCategories;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use DB;
use Image;
use Auth;

class BusinessCategoriesController extends Controller
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
	
	public function index(){

        $user_id = Auth::user()->id;

        $categories = BusinessCategories::where('user_id', $user_id)->orderBy('parent_id', 'ASC')->get();
        
		return view('business-categories/index',compact('categories'));
	}

	public function add(){

        $user_id = Auth::user()->id;
        $parent_id = 0;
        $category_name = '';
        
        $already_selected_categories = BusinessCategories::where('user_id', $user_id)->pluck('category_id');
        
        $categories = Categories::whereNotIn('id', $already_selected_categories)->where('status','1')->where('is_deleted',0)->where('parent_id',0)->orderBy('name','asc')->get();        

        return view('business-categories/add',compact('categories', 'parent_id', 'category_name'));
    }
    
	public function add_sub_categories($parent_id){

        $user_id = Auth::user()->id;

        $parent_id = base64_decode($parent_id);

        $business_category = BusinessCategories::where('user_id', $user_id)->where('id', $parent_id)->first();
        $category_name = 'for ('.$business_category->category->name .')';

        $already_selected_categories = BusinessCategories::where('user_id', $user_id)->pluck('category_id');
        
        $categories = Categories::whereNotIn('id', $already_selected_categories)->where('status','1')->where('is_deleted',0)->where('parent_id', $business_category->category_id)->orderBy('name','asc')->get();        
    
        return view('business-categories/add',compact('categories', 'parent_id', 'category_name'));
	}

	public function create(Request $request) {
        
        $input = $request->all();
        $user_id = Auth::user()->id;

        if(empty($input['category_ids'])) {
            return redirect()->action('BusinessCategoriesController@index')->with('alert-danger', 'Please select categories');
        }
        
        $categories = [];
        foreach($input['category_ids'] as $value) {
            $categories[] = [
                'parent_id' => (!empty($input['parent_id'])) ? $input['parent_id'] : 0,
                'category_id' => $value,
                'user_id' => $user_id,
            ];
        }

        BusinessCategories::insert($categories);
        
        return redirect()->action('BusinessCategoriesController@index')->with('alert-success', 'Category Added Successfully');
    }

    public function delete($id) {
        $id = base64_decode($id);
        BusinessCategories::findOrFail($id)->delete();
        return redirect()->action('BusinessCategoriesController@index')->with('alert-success', 'Category Deleted Successfully');
    }

    public function business_product_categories($category_id){

        $user_id = Auth::user()->id;
        $category_id = base64_decode($category_id);

        $sub_category = Categories::where('id', $category_id)->first();
        
        $categories = ProductCategories::where('parent_id', $category_id)->where('user_id', $user_id)->orderBy('id', 'asc')->get();
        
		return view('business-categories/business_product_categories',compact('categories', 'category_id', 'sub_category'));
    }

    public function add_business_product_categories($category_id){

        $user_id = Auth::user()->id;

        $category_id = base64_decode($category_id);
        
        $category = Categories::where('id', $category_id)->first();
        
        $category_name = 'for ('.$category->name .')';

        $already_selected_categories = ProductCategories::where('user_id', $user_id)->pluck('category_id');
        
        $categories = Categories::whereNotIn('id', $already_selected_categories)->where('status','1')->where('is_deleted',0)->where('parent_id', $category->id)->orderBy('name','asc')->get();        
    
        return view('business-categories/add_business_product_categories',compact('categories', 'category_id', 'category_name'));
    }

    public function create_business_product_categories(Request $request) {
        
        $input = $request->all();
        $user_id = Auth::user()->id;
        
        $category_id = base64_encode($input['category_id']);

        if(empty($input['category_ids'])) {
            return redirect()->action('BusinessCategoriesController@business_product_categories', [$category_id])->with('alert-danger', 'Please select categories');
        }
        
        $categories = [];
        foreach($input['category_ids'] as $value) {
            $categories[] = [
                'parent_id' => $input['category_id'],
                'category_id' => $value,
                'user_id' => $user_id,
            ];
        }

        ProductCategories::insert($categories);
        
        return redirect()->action('BusinessCategoriesController@business_product_categories', [$category_id])->with('alert-success', 'Category Added Successfully');
    }
    
    public function delete_business_product_category($category_id, $id) {

        $id = base64_decode($id);
        
        ProductCategories::findOrFail($id)->delete();
        
        return redirect()->action('BusinessCategoriesController@business_product_categories', [$category_id])->with('alert-success', 'Category Deleted Successfully');
    }

}
