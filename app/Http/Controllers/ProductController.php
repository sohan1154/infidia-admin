<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Super_category;
use App\Models\Categories;
use App\Models\ProductCategories;
use App\Models\Products;
use App\Models\Attributes_set;
use App\Models\ProductAttributes;
use App\Models\Attribute;
use App\Models\Price;
use App\Models\Stock;
use App\Models\Meta;
use App\Models\Images;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Input;
use DB;
use Session;
use Excel;
use Image;
use Auth;

class ProductController extends Controller
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
    public function index(Request $request){

        $user_id = getUrlOrAuthId($request);
        $role = Auth::user()->role;
        $sellerName = userName($user_id);

        if($role=='Buyer'){
            Auth::logout();
            redirect('login');
        }

        $products = Products::with('categories','price')->where('user_id',$user_id)->orderBy('name','asc')->get();

        return view('products/index',compact('products', 'user_id', 'sellerName'));
    }

    public function view($pid){
        $id = base64_decode($pid);
        
        $products = Products::where('id',$id)->first();

        return view('products/view',compact('products'));
    }

    public function deleteProductAttrBox(Request $request){

        $input  = $request->all();

        ProductAttributes::where('id',$input['id'])->delete();
        
        return ['status' => true, 'message' => 'Attribute deleted successfully'];
    }

    public function removeImage(Request $request){

        $input  = $request->all();

        $images = Images::where('products_id',$input['pid'])->first(['image']);
        
        if($images->image!=''){
            $images = unserialize($images->image);
            foreach ($images as $key => $value ) {
                if ($key==$input['id']) {
                    unset($images[$key]);
                }
            }
        }

        $data['image'] = serialize(array_values($images));

        Images::where('products_id',$input['pid'])->update($data);
        
        return ['status' => true, 'message' => 'Image deleted successfully'];
    }

    public function removeAttrImage(Request $request){

        $input  = $request->all();

        $images = ProductAttributes::where('id',$input['attrId'])->first(['images']);
        
        if($images->images!=''){
            $images = unserialize($images->images);
            foreach ($images as $key => $value ) {
                if ($key==$input['id']) {
                    unset($images[$key]);
                }
            }
        }
        
        $data['images'] = serialize(array_values($images));
        
        ProductAttributes::where('id',$input['attrId'])->update($data);
        
        return ['status' => true, 'message' => 'Image deleted successfully'];
    }

    public function checkSku(Request $request){
        $input        = $request->all();
        $countProduct = Products::where('sku',$input['sku'])->count();
        if($countProduct>0){
            $valid = 'false';
        } else {
            $valid = 'true';
        }
        echo $valid;die;
    }
    
    public function addAttrsInForm(Request $request){
        $input = $request->all();

        $index = $input['number'];
        $user_id = $input['user_id'];
        
        $attributes = Attribute::whereIn('id', $input['attrs'])->pluck('name','id');

        return view('products/attribute',compact('index', 'attributes', 'user_id'));
    }
    
    public function getAttributes($category_id){

        $attributes = Attribute::where('category_id', $category_id)->pluck('name','id');

        return view('products/get_attributes',compact('attributes'));
    }

    public function create(Request $request){
        
        $user_id = getUrlOrAuthId($request);
        $role = Auth::user()->role;
        $sellerName = userName($user_id);

        $catId = ProductCategories::where('user_id', $user_id)->pluck('category_id');
        $categories = Categories::whereIn('id',$catId)->where('is_deleted',0)->where('status','1')->orderBy('parent_id','asc')->get();         
        return view('products/create',compact('categories', 'user_id', 'sellerName'));
	}

    public function add(Request $request){
        
        $role = Auth::user()->role;

        $input = $request->all();

        $input['sku'] = $input['user_id'] . '-' . $input['sku'];
        $input['is_display_outof_stock_product'] = (!empty($input['is_display_outof_stock_product'])) ? 1 : 0;
        
        $image_array = $aatr_image_array = [];
        $validator = validator::make($request->all(), [
            'name' => 'required|max:255',
            'sku' => 'required|max:100|unique:products', 
            'barcode' => 'nullable|max:100|unique:products', 
            //'gst' => 'nullable|regex:/^\d+(\.\d{2,2})?$/',
            'user_id' => 'required', 
            'category_id' => 'required', 
            'base_price' => 'required', 
            'sale_price' => 'required',  
            'image' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->action('ProductController@create', ['userid' =>  base64_encode($input['user_id'])])
                            ->withErrors($validator)
                            ->with('alert-danger','Error in Product save, Please resolve these error first then try again.');
        }

        $products = Products::create($input);
        
        $lastInsertId = $attribute_data['product_id'] = DB::getPdo()->lastInsertId();

        // create directory to upload images in it
        createProductImageDirectories($lastInsertId);

        // upload product images
        if($request->hasFile('image')) {

            $images = [];
            foreach ($request->file('image') as $key=>$image) {
                
                $image_name = '';
                $uploadpath = public_path('images/products/'.$lastInsertId.'/');
                $original_name = $image->getClientOriginalName();

                if (!$image->isValid() || empty($uploadpath)) {
                    return $image_name;
                }

                if ($image->isValid()) {
                    $image_prefix = 'product_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                    $ext = $image->getClientOriginalExtension();
                    $image_name = $image_prefix . '.' . $ext;
                    $image_array[] = $image_name;
                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(1024, 1024);
                    $image_resize->save(public_path('images/products/'.$lastInsertId.'/' .$image_name));
                    $image_resize->resize(75, 75);
                    $image_resize->save(public_path('images/products/'.$lastInsertId.'/thumb/' .$image_name));
                    $image_resize->resize(480,320);
                    $image_resize->save(public_path('images/products/'.$lastInsertId.'/medium/' .$image_name));
                    $image->move($uploadpath, $image_name);

                    $images[] = $image_name;
                }
            }

            $images['image'] = serialize($images); 
            
            $products->images()->create($images);
        }

        // upload product attributes images
        if(!empty($input['attr'])) {

            $attribute_data = [];
            foreach($input['attr'] as $value) {

                if(!empty($value['image'])) {

                    $images = [];
                    foreach ($value['image'] as $aatr_image) {

                        $aatr_image_name = '';
                        $aatr_uploadpath = public_path('/images/products/'.$lastInsertId.'/attr');
                        $aatr_original_name = $aatr_image->getClientOriginalName();

                        if (!$aatr_image->isValid() || empty($aatr_uploadpath)) {
                            return $aatr_image_name;
                        }

                        if ($aatr_image->isValid()) {
                            $aatr_image_prefix = 'product_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                            $aatr_ext = $aatr_image->getClientOriginalExtension();
                            $aatr_image_name = $aatr_image_prefix . '.' . $aatr_ext;
                            $aatr_image_array[] = $aatr_image_name;
                            $image_resize = Image::make($aatr_image->getRealPath());
                            $image_resize->resize(75, 75);
                            $image_resize->save(public_path('images/products/'.$lastInsertId.'/attr/thumb/' .$aatr_image_name));
                            $image_resize->resize(480,320);
                            $image_resize->save(public_path('images/products/'.$lastInsertId.'/attr/medium/' .$aatr_image_name));
                            $aatr_image->move($aatr_uploadpath, $aatr_image_name);

                            $images[] = $aatr_image_name;
                        }
                    }
                }

                $singleAttr = new ProductAttributes();

                $singleAttr->sku = $input['user_id'] . '-' . $value['sku'];
                $singleAttr->barcode = $value['barcode'];
                $singleAttr->base_price = $value['base_price'];
                $singleAttr->sale_price = $value['sale_price'];
                $singleAttr->qty = $value['qty'];
                $singleAttr->attrs = serialize($value['attr']);
                $singleAttr->images = serialize($images);

                $attribute_data[] = $singleAttr;
            }
            
            $products->productAttributes()->saveMany($attribute_data);
        }
        
		$products->price()->create($input);
		$products->stock()->create($input);
        
        $params = [];
        if($role == 'Admin') {
            $params = ['userid' =>  base64_encode($input['user_id'])];
        }
        
        return redirect()->action('ProductController@index', $params)->with('alert-success', 'Product Added Successfully');
    }

	public function update(Request $request, $pid){

        $user_id = getUrlOrAuthId($request);
        $role = Auth::user()->role;
        $sellerName = userName($user_id);

        $id = base64_decode($pid);
		if ($id == '') {
            return 'URL NOT FOUND';
        }
        
		$product = Products::findOrFail($id);		
		if (empty($product)) {
            return redirect()->action('ProductController@index', ['userid' =>  base64_encode($input['user_id'])])->with('alert-danger', 'Product not found.');
        }

        $catId = ProductCategories::where('user_id', $user_id)->pluck('category_id');
        $categories = Categories::whereIn('id',$catId)->where('is_deleted',0)->where('status','1')->orderBy('name','asc')->get();         
        
        $attributes = Attribute::where('category_id', $product->category_id)->pluck('name','id');

        return view('products/edit',compact('product', 'user_id', 'categories', 'attributes'));
    }

	public function edit(Request $request, $pid) {
        
        $input = $request->all();

        $id = base64_decode($pid);
        $role = Auth::user()->role;

        if ($id == '') {
            return 'URL NOT FOUND';
        }

        $product =  Products::findOrFail($id);

        if (empty($product)) {
            return redirect()->action('ProductController@index', ['userid' =>  base64_encode($input['user_id'])])->with('alert-danger', 'Product not found.');
        }

        $user_id = $product->user_id;

        $image_array = $aatr_image_array = [];
        $validator = validator::make($request->all(), [
            'name' => 'required|max:255|unique:products,'.$id,
            'sku' => 'required|max:100|unique:products,'.$id, 
            'barcode' => 'nullable|max:100|unique:products,'.$id, 
            //'gst' => 'nullable|regex:/^\d+(\.\d{2,2})?$/',
            'user_id' => 'required', 
            'category_id' => 'required', 
            'base_price' => 'required', 
            'sale_price' => 'required',  
            'image' => 'required',
            'status' => 'required',
        ]);
		
		unset($input['_token']);
        unset($input['user_id']);

        $input['is_display_outof_stock_product'] = (!empty($input['is_display_outof_stock_product'])) ? 1 : 0;
        
        $product->fill($input)->save();

        // create directory to upload images in it
        createProductImageDirectories($id);

        // upload product images
        if($request->hasFile('image')) {

            $images = [];
            foreach ($request->file('image') as $key=>$image) {
                
                $image_name = '';
                $uploadpath = public_path('images/products/'.$id.'/');
                $original_name = $image->getClientOriginalName();

                if (!$image->isValid() || empty($uploadpath)) {
                    return $image_name;
                }

                if ($image->isValid()) {
                    $image_prefix = 'product_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                    $ext = $image->getClientOriginalExtension();
                    $image_name = $image_prefix . '.' . $ext;
                    $image_array[] = $image_name;
                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(1024, 1024);
                    $image_resize->save(public_path('images/products/'.$id.'/' .$image_name));
                    $image_resize->resize(75, 75);
                    $image_resize->save(public_path('images/products/'.$id.'/thumb/' .$image_name));
                    $image_resize->resize(480,320);
                    $image_resize->save(public_path('images/products/'.$id.'/medium/' .$image_name));
                    $image->move($uploadpath, $image_name);

                    $images[] = $image_name;
                }
            }

            if(!empty($images)) {
                $productImage =  Images::where('products_id',$id)->first();

                if(!empty($productImage)) {
                    $arrayImage       = unserialize($product->images->image);
                    $mergeArray       = array_merge($arrayImage, $images);

                    $productImages['image']   = serialize($mergeArray);
                    
                    $productImage->fill($productImages)->save();
                } else {

                    $images['image'] = serialize($images); 

                    $product->images()->create($images);
                }
            }
        }

        // upload product attributes images
        if(!empty($input['attr'])) {

            $attribute_data = [];
            $images = [];
            foreach($input['attr'] as $value) {

                if(!empty($value['image'])) {
                    
                    $images = [];
                    foreach ($value['image'] as $aatr_image) {

                        $aatr_image_name = '';
                        $aatr_uploadpath = public_path('/images/products/'.$id.'/attr');
                        $aatr_original_name = $aatr_image->getClientOriginalName();

                        if (!$aatr_image->isValid() || empty($aatr_uploadpath)) {
                            return $aatr_image_name;
                        }

                        if ($aatr_image->isValid()) {
                            $aatr_image_prefix = 'product_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                            $aatr_ext = $aatr_image->getClientOriginalExtension();
                            $aatr_image_name = $aatr_image_prefix . '.' . $aatr_ext;
                            $aatr_image_array[] = $aatr_image_name;
                            $image_resize = Image::make($aatr_image->getRealPath());
                            $image_resize->resize(75, 75);
                            $image_resize->save(public_path('images/products/'.$id.'/attr/thumb/' .$aatr_image_name));
                            $image_resize->resize(480,320);
                            $image_resize->save(public_path('images/products/'.$id.'/attr/medium/' .$aatr_image_name));
                            $aatr_image->move($aatr_uploadpath, $aatr_image_name);

                            $images[] = $aatr_image_name;
                        }
                    }
                }

                if(!empty($value['id'])) {
                    $productAttributes =  ProductAttributes::where('id', $value['id'])->first();

                    if(!empty($images) && !empty($productAttributes->images)) {
        
                        $arrayImage       = unserialize($productAttributes->images);
                        $mergeArray       = array_merge($arrayImage, $images);
                        
                    } else {
    
                        $mergeArray = unserialize($productAttributes->images);
                    }

                    $productAttributes->sku = $value['sku'];
                    $productAttributes->barcode = $value['barcode'];
                    $productAttributes->base_price = $value['base_price'];
                    $productAttributes->sale_price = $value['sale_price'];
                    $productAttributes->qty = $value['qty'];
                    $productAttributes->attrs = serialize($value['attr']);
                    $productAttributes->images = serialize($mergeArray);

                    $productAttributes->save();
                } else {
                    $productAttributes = new ProductAttributes();

                    $productAttributes->sku = $user_id . '-' . $value['sku'];
                    $productAttributes->barcode = $value['barcode'];
                    $productAttributes->base_price = $value['base_price'];
                    $productAttributes->sale_price = $value['sale_price'];
                    $productAttributes->qty = $value['qty'];
                    $productAttributes->attrs = serialize($value['attr']);
                    $productAttributes->images = serialize($images);
                    
                    $product->productAttributes()->saveMany([$productAttributes]);
                }

                $images = [];
            }
        }
        
        $productStock     =  Products::find($id)->stock;
        if($productStock){

            $productStock->fill($input)->save();
        } else {

            $inputs['stock_status'] = $input['stock_status'];
            $inputs['qty'] = $input['qty'];
            $inputs['products_id'] = $input['products_id'];

            Stock::create($inputs);
        }

        $productPrice     =  Products::find($id)->price;
        if($productPrice){
            $productPrice->fill($input)->save();
        } else {
            $inputs['base_price'] = $input['base_price'];
            $inputs['sale_price'] = $input['sale_price'];
            $inputs['products_id'] = $input['products_id'];
            Price::create($inputs);
        }

        $params = [];
        if($role == 'Admin') {
            $params = ['userid' =>  base64_encode($user_id)];
        }

        return redirect()->action('ProductController@index', $params)->with('alert-success', 'Product Updated Successfully');
    }

	public function delete($pid) {
        $id = base64_decode($pid);
        $role = Auth::user()->role;
        //$product = Products::find($id)->delete();
		$products = Products::find($id);
		$products->delete();		
		$products->price()->delete();
		$products->stock()->delete();
		$products->meta()->delete();
		$products->images()->delete();
        //Price::delete();
        
        $params = [];
        if($role == 'Admin') {
            $params = ['userid' => base64_encode($products->user_id)];
        }

		return redirect()->action('ProductController@index', $params)->with('alert-success', 'Product Deleted Successfully');
    }
	
	public function status($pid,$status) {  
        $ids = base64_decode($pid);   
        $role = Auth::user()->role;
        $products =  Products::find($ids);
        if (empty($products)) {
            return 'URL NOT FOUND';
        }

        $input['status'] = $status;
        unset($input['_token']);
        
        $products->fill($input)->save();

        $params = [];
        if($role == 'Admin') {
            $params = ['userid' => base64_encode($products->user_id)];
        }

        return redirect()->action('ProductController@index', $params)->with('alert-success', 'Product Status Updated Successfully');
    }
	
	public function importExcel(Request $request) {

        if($request->hasFile('import_file')){
            Excel::load($request->file('import_file')->getRealPath(), function ($reader) {
                foreach ($reader->toArray() as $key => $row) {
                    
					$error = array();
					$categories = Categories::where('name',$row['category'])->where('is_deleted',0)->first();
					if(empty($categories['id'])){
						$error['category'] = "Category Name does not exist in System";
					}
					
					if(empty($error)){
						
						$attribute_sets = Attributes_set::where('name',$row['attribute'])->first(); 
						
						$data['category_id'] = $categories['id'];
						$data['attribute_set_id'] = $attribute_sets['id'];
						
						$data['name'] = $row['name'];
						$data['description'] = $row['description'];
						$data['short_description'] = $row['short_description'];
						$data['base_price'] = $row['base_price'];
						$data['sale_price'] = $row['sale_price'];
						
						$all_images = explode('|',$row['image']);
						$all_images = serialize($all_images);
						
						$data['image'] = $all_images;
						
						$data['keyword'] = $row['keyword'];
						$data['meta_description'] = $row['meta_description'];
						$data['qty'] = $row['qty'];
						$data['stock_status'] = $row['stock_status'];

						if(!empty($data)) {
							$products = Products::create($data);
							$products->price()->create($data);
							$products->stock()->create($data);
							$products->meta()->create($data);
							$products->images()->create($data);
						}
					} else {
						
					}
                }
            });
        }

        Session::put('success', 'Your file successfully import in database!!!');
        return back();
    }

}
