<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Categories;
use App\Models\ProductCategories;
use App\Models\Products;
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

class ImportcsvController extends Controller
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

        $id = Auth::user()->id;
        $role = Auth::user()->role;

        if(!in_array($role, ['Admin', 'Seller'])){
            Auth::logout();
            redirect('login');
        }

        if($role == 'Seller'){
            $sellers = User::where('id',$id)->where('role','Seller')->orderBy('name','asc')->get();
        } else {
            $sellers = User::where('role','Seller')->orderBy('name','asc')->get();
        }

        return view('importcsv/index',compact('sellers', 'role'));
    }

    public function add(Request $request){
        
        $role = Auth::user()->role;

        $input = $request->all();

        $categories = Categories::pluck('id', 'name');

        if($request->hasFile('csvfile')){

            $filename = $request->file('csvfile')->getRealPath();
            $seller_id = $input['seller_id'];

            // The nested array to hold all the unsave records
            $unsave_data = [];

            // store all products data with complete information 
            $allProducts = [];

            // Open the file for reading
            if (($h = fopen($filename, "r")) !== FALSE) {
                
                echo '<pre>';

                // Each line in the file is converted into an individual array that we call $data
                // The items of the array are comma separated
                $attrCount = 0;
                $productCount = 0;
                $count = 0;
                while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
                    
                    if(!$count) {
                        print_r($data);
                        $count++;
                        continue;
                    }
                    
                    if(!empty($data[0])) {

                        $productCount++;
                        $attrCount = 0; // reset counter
                        
                        // product info
                        $allProducts[$productCount]['name'] = $data[0];
                        $allProducts[$productCount]['category_id'] = (!empty($categories[$data[1]])) ? $categories[$data[1]] : 0;
                        $allProducts[$productCount]['user_id'] = $seller_id;
                        $allProducts[$productCount]['sku'] = $seller_id . '-' . $data[2];
                        $allProducts[$productCount]['description'] = $data[3];
                        $allProducts[$productCount]['meta_key'] = $data[4];
                        $allProducts[$productCount]['meta_description'] = $data[5];
                        $allProducts[$productCount]['weight'] = $data[9];
                        $allProducts[$productCount]['status'] = $data[10];
                        $allProducts[$productCount]['return_policy'] = $data[11];
                        $allProducts[$productCount]['warranty'] = $data[12];
                        $allProducts[$productCount]['shipping_time'] = $data[13];
                        
                        // prices info
                        $allProducts[$productCount]['prices']['base_price'] = $data[6];
                        $allProducts[$productCount]['prices']['sale_price'] = $data[7];
                        
                        // stocks info
                        $allProducts[$productCount]['stocks']['qty'] = $data[8];
                    }

                    // saving image at over server from remote server 
                    if(!empty($data[14])) {

                        $profilePic = self::saveImage($data[14], 'images');

                        if(!empty($allProducts[$productCount]['name'])) {
                            $allProducts[$productCount]['images'][] = $profilePic;
                        }
                    }

                    // work on attr section
                    if(!empty($data[15])) {

                        $attrCount++;

                        $attrs_with_values = [];

                        $attrs = explode(',', $data[15]);
                        $attrs_values = explode(',', $data[16]);
                        foreach($attrs as $key => $attr) {
                            $attrs_with_values[] = [
                                'attribute_type' => $attr,
                                'attribute_value' => $attrs_values[$key]
                            ];
                        }
                        
                        // attrs info
                        $allProducts[$productCount]['attr'][$attrCount]['base_price'] = $data[17];
                        $allProducts[$productCount]['attr'][$attrCount]['sale_price'] = $data[18];
                        $allProducts[$productCount]['attr'][$attrCount]['qty'] = $data[19];
                        $allProducts[$productCount]['attr'][$attrCount]['attr'] = ($attrs_with_values);
                    }

                    // saving image at over server from remote server
                    if(!empty($data[20])) {
                        $attrImages = self::saveImage($data[20], 'images');
                        
                        if(!empty($allProducts[$productCount]['name'])) {
                            $allProducts[$productCount]['attr'][$attrCount]['images'][] = $attrImages;
                        }
                    }
                    
                    $count++;
                }
                
                // Close the file
                fclose($h);
            }
        }
        
        $unsavedRows = [];
        foreach($allProducts as $product) {

            $image_array = $aatr_image_array = [];
            $validator = validator::make($product, [
                'name' => 'required|max:255',
                'sku' => 'required|max:100|unique:products', 
                'user_id' => 'required', 
                'category_id' => 'required', 
                //'base_price' => 'required', 
                //'sale_price' => 'required',  
                //'image' => 'required',
                //'status' => 'required',
                'warranty' => 'required|max:255',
                'shipping_time' => 'required|max:255',
            ]);

            if ($validator->fails()) {
                $unsavedRows[] = $product['sku'];
            } else {
        
                $products = Products::create($product);
                
                $lastInsertId = DB::getPdo()->lastInsertId();

                // create directory to upload images in it
                createProductImageDirectories($lastInsertId);

                // upload product images
                if(!empty($product['images'])) {

                    $images = [];
                    $counter = 0;
                    foreach ($product['images'] as $image) {

                        $image_name = '';
                        $uploadpath = public_path('images/products/'.$lastInsertId.'/');

                        if (1) {
                            $image_prefix = 'product_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                            $ext = substr($image, strrpos($image, '.'));
                            $image_name = $image_prefix . '.' . $ext;
                            $image_array[] = $image_name;
                            $image_resize = Image::make($image);
                            //$image_resize->resize(1024, 1024);
                            $image_resize->save(public_path('images/products/'.$lastInsertId.'/' .$image_name));
                            $image_resize->resize(75, 75);
                            $image_resize->save(public_path('images/products/'.$lastInsertId.'/thumb/' .$image_name));
                            $image_resize->resize(480,320);
                            $image_resize->save(public_path('images/products/'.$lastInsertId.'/medium/' .$image_name));

                            $images[] = $image_name;
                        }
                    }

                    $images['image'] = serialize($images); 
                    
                    $products->images()->create($images);
                }

                // upload product attributes images
                if(!empty($product['attr'])) {

                    $attribute_data = [];
                    foreach($product['attr'] as $value) {

                        if(!empty($value['images'])) {

                            $images = [];
                            foreach ($value['images'] as $aatr_image) {

                                $aatr_image_name = '';
                                $aatr_uploadpath = public_path('/images/products/'.$lastInsertId.'/attr');

                                if (1) {
                                    $aatr_image_prefix = 'product_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                                    $aatr_ext = substr($aatr_image, strrpos($aatr_image, '.'));
                                    $aatr_image_name = $aatr_image_prefix . '.' . $aatr_ext;
                                    $aatr_image_array[] = $aatr_image_name;
                                    $image_resize = Image::make($aatr_image);
                                    //$image_resize->resize(1024, 1024);
                                    $image_resize->save(public_path('images/products/'.$lastInsertId.'/attr/' .$aatr_image_name));
                                    $image_resize->resize(75, 75);
                                    $image_resize->save(public_path('images/products/'.$lastInsertId.'/attr/thumb/' .$aatr_image_name));
                                    $image_resize->resize(480,320);
                                    $image_resize->save(public_path('images/products/'.$lastInsertId.'/attr/medium/' .$aatr_image_name));

                                    $images[] = $aatr_image_name;
                                }
                            }
                        }

                        $singleAttr = new ProductAttributes();

                        $singleAttr->base_price = $value['base_price'];
                        $singleAttr->sale_price = $value['sale_price'];
                        $singleAttr->qty = $value['qty'];
                        $singleAttr->attrs = serialize($value['attr']);
                        $singleAttr->images = serialize($images);

                        $attribute_data[] = $singleAttr;
                    }
                    
                    $products->productAttributes()->saveMany($attribute_data);
                }
                
                $products->price()->create($product['prices']);
                $products->stock()->create($product['stocks']);
            }
        }
        
        if(!empty($unsavedRows)) {
            return redirect()->action('ImportcsvController@index')->with('alert-danger', 'Some Products Not Imported Successfully');
        } else {
            return redirect()->action('ImportcsvController@index')->with('alert-success', 'Product Imported Successfully');
        }
    }

    public function saveImage($imageUrl, $destination) {
        
        return $imageUrl;

        $imageName = substr($imageUrl, (strrpos($imageUrl, '/')+1));
        $imageExt = substr($imageUrl, strrpos($imageUrl, '.'));
      
        $imageNewName = date('YmdHis').'-'.rand(000, 999).$imageExt;
      
        // copy image from remote server
        // copy($imageUrl, $destination.'/'.$imageNewName);
      
        return $imageNewName;
    }

}
