<?php

use App\User;
use App\Models\ProfilePictures;
use App\Models\Subscriptions;
use App\Models\ProductCategories;
use App\Models\Categories;
use App\Models\BusinessCategories;
use App\Models\ProductAttributes;
use App\Models\AttributesSet;
use App\Models\Attribute;
use App\Models\Images;
use App\Models\Stock;
use App\Models\Meta;
use App\Models\Price;
use App\Models\Orders;
use App\Models\Products;
use App\Models\UserAddress;
use App\Models\Payments;
use App\Models\Wishlists;
use App\Models\Carts;
use App\Models\Reviews;
use App\Models\OrderReturn;

function getStatus($status) {

    $statusList = ['Inactive', 'Active'];

    return $statusList[$status];
}

function formatedDate($date, $formate = 'h:i a, d F, Y')
{   
    return date($formate, strtotime($date));
}

function getStockLabel($status) {

    return 'In Stock';
}

function getUrlOrAuthId($request) {
    $input = $request->all();
    return (!empty($input['userid'])) ? base64_decode($input['userid']) : Auth::user()->id;
}

function userProfile($user_id)
{	
    $profile = ProfilePictures::where('user_id', $user_id)->first();
	
    if($profile){
        if($profile->picture!='')
        {  
            if (strpos($profile->picture, 'http') !== false) { 
                $pic = $profile->picture;
            } else { 
                $pic =  url('/').'/images/profile/'.$profile->picture;
            }
        } else { 
                $pic =  url('/').'/no-image/user.png';
        }
    } else { 
            $pic =  url('/').'/no-image/user.png';
    }    
    return $pic;
}

function getProductCategories($user_id)
{
    $catId = ProductCategories::where('user_id', $user_id)->pluck('category_id');
    $getCategories = Categories::whereIn('id',$catId)->where('is_deleted',0)->where('status','1')->orderBy('name','asc')->pluck('name', 'id')->toArray();

    $categories = [];
    foreach($getCategories as $id => $name) {

        $categoryProductCount = Products::where('category_id', $id)->where('user_id', $user_id)->count();

        if($categoryProductCount)
            $categories[] = ['id' => $id, 'name' => $name];
    }

    return $categories;
}

function CategoryImage($id=null)
{
    $image = Categories::where('id', $id)->first(['image']);
    if($image){
        if($image->image!='')
        {  
            $pic =  url('/').'/images/categories/'.$image->image;
        } else { 
            $pic =  url('/').'/images/profile/no-image.png';
        }
    } else { 
            $pic =  url('/').'/images/profile/no-image.png';
    }    
    return $pic;
}

function userProfilePath($user_id=null)
{
    $profile = ProfilePictures::where('user_id', $user_id)->first(['picture']);
    if($profile){
        if($profile->picture!='')
        {  
            if (strpos($profile->picture, 'http') !== false) { 
                $pic = $profile->picture;
            } else { 
                $pic =  $profile->picture;
            }
        } else { 
                $pic =  'no-image.png';
        }
    } else { 
            $pic =  'no-image.png';
    }    
    return $pic;
}

function SubscriptionPlan($plan_id=null)
{
    $plan = Subscriptions::where('id', $plan_id)->first();
    return $plan;
}

function wishlistRecode($pid=null,$uid=null){
    $count = Wishlists::where('product_id', $pid)->where('user_id', $uid)->count();
    if($count>0){
      return 1;
    } else {
      return 0;
    }
    
}

/*function getMaximumRecode($pid=null,$uid=null,$uid=null){
    $count = Wishlists::where('product_id', $pid)->where('user_id', $uid)->count();
    if($count>0){
      return 1;
    } else {
      return 0;
    }
    
}*/

function getCartQty($pid=null,$ppid=null,$uid=null){
    $count = Carts::where('product_id', $pid)->where('product_attr_id',$ppid)->where('user_id', $uid)->first(['qty']);
    if($count){
      return $count->qty;
    } else {
      return 0;
    }
    
}

function paymentTnxDta($id=null)
{
    $id = Payments::where('order_id', $id)->first(['tnx_id']);
    return $id->tnx_id;
}

function shop_address($user_id=null)
{
    $address = Subscriptions::where('id', $plan_id)->first(['shop_address']);
    if($address){
        if($address->shop_address!='')
        {  
            $shopaddress = $address->shop_address;
        } else { 
            $shopaddress =  '';
        }
    } else { 
            $shopaddress =  '';
    }    
    return $shopaddress;
}

function userDetails($user_id=null)
{
    $user = User::where('id', $user_id)->first();
    if($user){
            $userDetails = $user;
    } else { 
            $userDetails =  '';
    }    
    return $userDetails;
}

function userName($user_id=null)
{ 
    $user = User::where('id', $user_id)->first(['name']);
    if($user){
        if($user->name!=''){
                $userName = $user->name;
        } else { 
                $userName =  '';
        } 
    } else { 
            $userName =  '';
    }        
    return $userName;
}

function getUserRole($user_id)
{
    $result = '';

    $user = User::where('id', $user_id)->first(['role']);

    if($user){
        $result = $user->role;
    }

    return $result;
}

function getRoleBaseUrl($role)
{
    $list = [
        'Admin' => 'Admin', 
        'Seller' => 'Seller', 
        'Buyer' => 'Buyer', 
        'Delivery' => 'delivery_boy',
    ];

    return $list[$role];
}

function getBusinessIds($id=null)
{
    $business_categories = BusinessCategories::where('category_id', $id)->get();

    $result = [];
    foreach($business_categories as $value){
        $result[] = $value->user_id;
    }
        
    return $result;
}

function catParentId($id=null)
{
    $cat = Categories::where('id', $id)->first(['parent_id']);
    if($cat->parent_id!=''){
            $cat = $cat->parent_id;
    } else { 
            $cat =  '0';
    }    
    return $cat;
}

function getAtributeId($id=null)
{
    $cat = Categories::where('id', $id)->first(['attr_id']);
    if($cat->attr_id!=''){
            $cat = $cat->attr_id;
    } else { 
            $cat =  '0';
    }    
    return $cat;
}

function attributes($id=null)
{
    $cat = AttributesSet::where('id', $id)->first();
    if($cat){
            $cat = $cat->attribute_id;
            $catattr = explode(",",$cat);
            if($catattr){
                $attrVal = Attribute::whereIn('id', $catattr)->get();                
            }
    } else { 
            $attrVal =  '0';
    }    
    return $attrVal;
}

function getAllAttributes($id=null)
{
    $attrId   = explode(',', $id);
    $attrData = Attribute::whereIn('id', $attrId)->get();
    if(!empty($attrData)){
        $attrVal = $attrData;
    } else { 
        $attrVal =  '0';
    }    
    return $attrVal;
}

function attrName($attr_id=null)
{
    $attrName = AttributesSet::where('id', $attr_id)->first(['name']);
    if(isset($attrName)){ 
        if(isset($attrName->name) && $attrName->name!=''){
                $attrName = $attrName->name;
        } else { 
                $attrName =  '';
        } 
    } else { 
            $attrName =  '';
    }    
    return $attrName;
}

function productAttrDetails($id=null)
{
    $product = ProductAttributes::where('products_id', $id)->get();
    if($product){
            $Details = $product;
    } else { 
            $Details =  '';
    }    
    
    return $Details;
}
function productImagesData($id=null){
    $product = Images::where('products_id', $id)->first(['image']);
    
    if($product){
        if($product->image){
            
                $Details = $product->image;
        } else { 
                $Details =  '';
        } 
    } else { 
            $Details =  '';
    }       
    return $Details;

}
function productImages($id=null)
{   
    $product = Images::where('products_id', $id)->first(['image']);
    print_r($product);die;
    if($product){
        if($product->image){
                $Details = $product->image;
        } else { 
                $Details =  '';
        } 
    } else { 
            $Details =  '';
    }       
    return $Details;
}

function productFirstImages($id=null)
{
    $product = Images::where('products_id', $id)->first(['image']);
    if($product){
        if($product->image!=''){
            $image = unserialize($product->image);
            $Details =  url('/').'/images/products/'.$id.'/thumb/'.$image[0];
        } else { 
            $Details =  '';
        } 
    } else { 
            $Details =  url('/').'/images/profile/no-image.png';
    }       
    return $Details;
}

function productStocksQty($id=null)
{
    $product = Stock::where('products_id', $id)->first(['stock_status','qty']);
    if($product){
            $Details = $product;
    } else { 
            $Details =  '';
    }    
    return $Details;
}

function productPrice($id=null)
{
    $product = Price::where('products_id', $id)->first();
    if($product){
            $Details = $product;
    } else { 
            $Details =  '';
    }    
    return $Details;
}

function productSalePrice($id=null)
{ 
    $product = Price::where('products_id', $id)->first(['sale_price']);
    if($product){
            $Details = $product->sale_price;
    } else { 
            $Details =  0;
    }    
    return $Details;
}
function productBasePrice($id=null)
{ 
    $product = Price::where('products_id', $id)->first(['base_price']);
    if($product){
            $Details = $product->base_price;
    } else { 
            $Details =  0;
    }    
    return $Details;
}
function categoryName($id=null)
{
    $catname = Categories::where('id', $id)->first(['name']);
    if($catname){
        if($catname->name!=''){
                $name = $catname->name;
        } else { 
                $name =  '';
        } 
    } else { 
            $name =  '';
    }       
    return $name;
}

function subCategoryName($id=null)
{
    $catname = Categories::where('parent_id', $id)->first(['name']);
    if($catname){
        if($catname->name!=''){
                $name = $catname->name;
        } else { 
                $name =  '';
        } 
    } else { 
            $name =  '';
    }       
    return $name;
}

function metaData($id=null)
{
	$meta = Meta::where('products_id', $id)->first();
        if($meta!=''){
                $name = $meta;
        } else { 
                $name =  '';
        }       
    return $name;
}

function subCatNameForSelected($id=null)
{
    $catname = Categories::where('parent_id', $id)->get();
    if($catname){
        $name = $catname; 
    } else { 
        $name =  '';
    }       
    return $name;
}
function Orderid($order_id=null)
{
    $orderid = Orders::where('order_id', $order_id)->first();
    return $orderid;
}
function OrderDetail($order_id=null)
{
    $orderid = Orders::where('id', $order_id)->first();
    return $orderid;
}
function ProductName($id=null)
{
    $productname = Products::where('id', $id)->first(['name']);
    if($productname){
        if($productname->name!=''){
                $name = $productname->name;
        } else { 
                $name =  '';
        } 
    } else { 
            $name =  '';
    }       
    return $name;
}

function returedProducts($id)
{   
    return OrderReturn::where('product_id', $id)->get();
}

function getOrderProductData($order_id, $product_id) {

    $result = Orders::where('id', $order_id)->orderBy('id','desc')->get();  
		
    $orderDetails = array();
    foreach($result as $key=>$value){

        $orderDetails[$key]['id']              = $value->id;
        $orderDetails[$key]['order_id']        = $value->order_id;
        $orderDetails[$key]['is_cancelled']    = $value->is_cancelled;
        $orderDetails[$key]['order_status']    = $value->order_status;
        $orderDetails[$key]['user_id']         = $value->user_id;
        $orderDetails[$key]['total_amount']    = $value->total_amount;
        $orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
        $orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'Completed':'Pending';
        
        $orderDetails[$key]['product_details'] = array(); 
        $product_attr = $value->product_attr_id;					
        $allAttrId = unserialize($product_attr);
        $productAttrData = $allAttrId;
        // $productData = ProductDetails(serialize(array($product_id))); // get all products of that order
        $productData = ProductDetails($value->product_id); // get all products of that order
        $productAmountData  = array_filter(unserialize($value->amount));
        $productQtyData  = array_filter(unserialize($value->qty));
        //$orderDetails[$key]['total_item']    = sizeof($productData);
        $orderDetails[$key]['total_item']    = array_sum(unserialize($value->qty));
        if(!empty($productData)){
            foreach($productData as $subKey=>$productvalue){
                $orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
                $orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
                $orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
                $orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
                $image = productFirstImages($productvalue->id); 
                $orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;

                if(isset($productQtyData[$subKey])){

                    $returedProducts = returedProducts($productvalue->id);

                    $orderDetails[$key]['product_details'][$subKey]['amount'] 	= $productAmountData[$subKey];					
                    $orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];					
                    // $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= array_key_exists($subKey, $productAttrData) ? $productAttrData[$subKey] : '';
                    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= @$productAttrData[$subKey];
                    $orderDetails[$key]['product_details'][$subKey]['retured_products'] = $returedProducts;
                    $orderDetails[$key]['product_details'][$subKey]['retured_products_total'] = count($returedProducts);
                } else {
                    $orderDetails[$key]['product_details'][$subKey]['amount'] 		= 0;
                    $orderDetails[$key]['product_details'][$subKey]['qty'] 			= 0;
                    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 		= '';
                    $orderDetails[$key]['product_details'][$subKey]['retured_products']	= [];
                    $orderDetails[$key]['product_details'][$subKey]['retured_products_total']	= [];
                }
                
                $orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
            }
        }
    }

    return $orderDetails[$key];
}

function ProductDetails($id=null)
{
    $id = unserialize($id);
    $productdata = Products::whereIn('id', $id)->get(); // get multiple records
    if($productdata){
        $productData = $productdata;
    } else { 
        $productData =  '';
    }       
    return $productData;
}

function getProductDetails($id=null)
{   
    $productdata = Products::where('id', $id)->first(); // get single record
    if($productdata){
        $productData = $productdata;
    } else { 
        $productData =  '';
    }       
    return $productData;
}

function getSellerId($id=null)
{   
    $productdata = Products::where('id', $id)->first(['user_id']);
    if($productdata && $productdata->user_id!=''){
        $productData = $productdata->user_id;
    } else { 
        $productData =  '';
    }       
    return $productData;
}

function getSellerUserId($id)
{   
    /*$storeId     = unserialize($id);
    print_r($storeId);die;*/
    $productdata = Products::where('id', $id)->first(['user_id']);

    /*if($productdata && $productdata->user_id!=''){
        $productData = $productdata->user_id;
    } else { 
        $productData =  '';
    }*/   
    if($productdata){
        $productData = $productdata;
    } else {
        $productData = '';
    }
    return $productData;

}

function getSellerByOrderId($id=null)
{   
    $productdata = Orders::where('id', $id)->first(['product_id']);
    if($productdata){ 
        $productId   = unserialize($productdata->product_id);
		//$productId = array_keys($productId);	
		$productId = $productId[0];	
        if($productId){
        
            // if($productId[0]){                
				// $productId = $productId[0];
            // } else {
                // $productId = $productId[1];
            // } 
            $sellerId    = Products::where('id', $productId)->first(['user_id']);
            if($sellerId){
                $userId = $sellerId->user_id;
            } else {
                $userId = 0;
            }
        } else {
                $userId = 0;
            } 
    } else {
        $userId = 0;
    }        
    return $userId;
}

function getRating($id=null)
{   
    $data = User::where('id', $id)->first(['rating']);
    if($data){
        $rating = $data->rating;
    } else {
        $rating =  0;
    }
    return $rating;
}

function getTotalRating($id=null)
{
    return Reviews::where('seller_id', $id)->count();
}

function ProductmultAttrDetails($id=null)
{   
    $id = unserialize($id);
    $productAttrdata = ProductAttributes::whereIn('id', $id)->get();
    if($productAttrdata){
        $productAttrData = $productAttrdata;
    } else { 
        $productAttrData =  '';
    }       
    return $productAttrData;
}
function getAttrName($id=null)
{   
    $productAttrdata = ProductAttributes::where('id', $id)->first(['attr_name']);
    if($productAttrdata){
        if($productAttrdata->attr_name!=''){
            $productAttrData = unserialize($productAttrdata->attr_name);
        } else { 
            $productAttrData =  '';
        } 
    } else { 
        $productAttrData =  '';
    }          
    return $productAttrData;
}
function getAttrData($id=null)
{   
    $productAttrdata = ProductAttributes::where('id', $id)->first();
    if($productAttrdata){
        if($productAttrdata->attr_name!=''){
            $productAttrData = unserialize($productAttrdata->attr_name);
        } else { 
            $productAttrData =  '';
        } 
    } else { 
        $productAttrData =  '';
    }          
    return $productAttrData;
}
function UserAddress($id=null)
{
    $address = UserAddress::where('id', $id)->first();
        if($address!=''){
                $name = $address;
        } else { 
                $name =  '';
        }        
    return $name;
}

function getUserAddressByUserId($id=null)
{
    $address = UserAddress::where('user_id', $id)->first();
        if($address!=''){
                $name = $address;
        } else { 
                $name =  '';
        }        
    return $name;
}

function getSellerMinOrder($id=null)
{
    $address = UserAddress::where('user_id', $id)->first(['min_order']);
    
        if($address->min_order!=''){
                $name = $address->min_order;
        } else { 
                $name =  '';
        }        
    return $name;
}

function createProductImageDirectories($product_id)
{
    createDirectory('products', $product_id);
    createDirectory('products', $product_id.'/thumb');
    createDirectory('products', $product_id.'/medium');
    createDirectory('products', $product_id.'/attr');
    createDirectory('products', $product_id.'/attr/thumb');
    createDirectory('products', $product_id.'/attr/medium');

    return true;
}

function createDirectory($baseDir, $subDir, $permission=0777)
{
    $directory = public_path('images/'.$baseDir.'/'.$subDir.'/');

    if (!file_exists($directory)) {
        mkdir($directory);
        chmod($directory, $permission);
    }

    return true;
}