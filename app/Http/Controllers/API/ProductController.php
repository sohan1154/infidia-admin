<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User;
use App\Models\SuperCategory;
use App\Models\Categories;
use App\Models\BusinessCategories;
use App\Models\Products;
use App\Models\UserAddress;
use App\Models\ProductAttributes;
use App\Models\Price;
use App\Models\Stock;
use App\Models\Meta;
use App\Models\Images;
use App\Models\Carts;
use App\Models\Wishlists;
use URL;
use DB;
use Image;

class ProductController extends Controller
{

	public $successStatus = 200;

	/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function getAttributes(Request $request){
		$input = $request->all();
		$cat_id = $input['cat_id'];
		$attr_id = getAtributeId($input['cat_id']);
		$attrData = attributes($attr_id);
		$result = array();
	    if($result){	
			foreach($attrData as $key=>$value){	
			    $result[$key]['type']      = $value->type;	
			    $result[$key]['attr_name']      = $value->name;	
				$attribute_option          = unserialize($value->attribute_option);
				$result[$key]['value'] = array();			
				foreach($attribute_option as $subKey=>$value){				
					$result[$key]['value'][$subKey]['value'] = $value;
				}		
		    }
		}
	    return response()->json(['status'=>'1','msg'=>'Shop List','shoplist'=>$result], $this->successStatus);
	}

	public function storeListBasedCatList(Request $request){

		$input = $request->all();
		$cat_id = $input['cat_id'];
		
		$categories = Categories::where('id', $cat_id)->orWhere('parent_id', $cat_id)->get();

		$categoryIds = [];
		foreach($categories as $category) {
			$categoryIds[] = $category->id;
			$categoryIds[] = $category->parent_id;
		}

		$businessIds = BusinessCategories::whereIn('category_id', $categoryIds)->pluck('user_id');

		$query = User::query();

		$query->where('status', '1');
		$query->where('role', 'Seller');

		if(!empty($cat_id)) {
			$query->whereIn('id', $businessIds);
		}

		if(!empty($input['search'])) {
			
			$search = trim($input['search']);

			$query->where('name', 'like', '%'.$search.'%');
		}

		$query->orderBy('name','asc');
		$user = $query->get();	

		$result = array();
		foreach($user as $key=>$userData){	
		    $shops = UserAddress::where('user_id',$userData->id)->first();	
			$result[$key]['name']      = $userData->name;		
			$result[$key]['store_id']   = $userData->id!='' ? $userData->id : '';	
			$result[$key]['image']     = userProfile($userData->id);
            if(isset($shops)){
            	$result[$key]['distance']  = $shops->distance!='' ? $shops->distance : '0';		
			    $result[$key]['min_order'] = $shops->min_order!='' ? '₹'.$shops->min_order : 'N/A';
			    $result[$key]['location']  = $shops->location!=''  ? $shops->location : '';		
				$result[$key]['house_no']  = $shops->house_no!='' ? $shops->house_no : '';		
				$result[$key]['address']   = $shops->address!='' ? $shops->address : '';		
				$result[$key]['landmark']  = $shops->landmark!='' ? $shops->landmark : '';		
				$result[$key]['type']  	   = $shops->type!='' ? $shops->type : '';
            } else {
            	$result[$key]['distance']  = $result[$key]['min_order'] = $result[$key]['location']  = $result[$key]['house_no']  = $result[$key]['address']   = $result[$key]['landmark']  =$result[$key]['type']=''; 
            }

			$result[$key]['rating']    = getRating($userData->id);		
		}
		
		return response()->json(['status'=>'1','msg'=>'Shop List','data'=>$result], $this->successStatus);
	}

	public function productListBasedCatList(Request $request){
		$input = $request->all();
		$cat_id = $input['cat_id'];

		//$parent_id = catParentId($input['cat_id']);
		//$user = User::whereRaw('FIND_IN_SET('.$parent_id.',category)')->where('status',1)->where('is_deleted',0)->get();
		
		$businessIds = getBusinessIds($input['cat_id']);
		// $user = User::whereIn('id', $businessIds)->where('status',1)->where('is_deleted',0)->get();

		$query = Products::query();

		$query->where('status','=','1');

		if(!empty($cart_id)) {
			$query->whereIn('id', $businessIds);
		}

		if(!empty($input['search'])) {
			
			$search = trim($input['search']);

			$query->where('name', 'like', '%'.$search.'%');
		}

		$query->orderBy('name','asc');
		$products = $query->get();	

		$result = array();
		foreach($products as $key=>$userData){	
		    $shops = UserAddress::where('user_id',$userData->user_id)->first();	
			$result[$key]['name']      = $userData->name;		
			$result[$key]['store_name']      = @$userData->user->name;		
			$result[$key]['product_id']        = $userData->id!='' ? $userData->id : '';		
			$result[$key]['store_id']   = $userData->user_id!='' ? $userData->user_id : '';	
			$result[$key]['image']     = productFirstImages($userData->id);
			$result[$key]['product_categories']     = getProductCategories($userData->id);
            if(isset($shops)){
            	$result[$key]['distance']  = $shops->distance!='' ? $shops->distance : '0';		
			    $result[$key]['min_order'] = $shops->min_order!='' ? '₹'.$shops->min_order : 'N/A';
			    $result[$key]['location']  = $shops->location!=''  ? $shops->location : '';		
				$result[$key]['house_no']  = $shops->house_no!='' ? $shops->house_no : '';		
				$result[$key]['address']   = $shops->address!='' ? $shops->address : '';		
				$result[$key]['landmark']  = $shops->landmark!='' ? $shops->landmark : '';		
				$result[$key]['type']  	   = $shops->type!='' ? $shops->type : '';
            } else {
            	$result[$key]['distance']  = $result[$key]['min_order'] = $result[$key]['location']  = $result[$key]['house_no']  = $result[$key]['address']   = $result[$key]['landmark']  =$result[$key]['type']=''; 
            }

			$result[$key]['rating']    = getRating($userData->id);		
		}
		
		// return response()->json(['status'=>'1','msg'=>'Product List','data'=>$products], $this->successStatus);
		return response()->json(['status'=>'1','msg'=>'Product List','data'=>$result], $this->successStatus);
	}

	public function getStorePageData(Request $request) {

		$input = $request->all();

		$user = User::find($input['shop_id']);

		$store_info = [
			'id' => $user->id,
			'name' => $user->name,
		    'address' => @$user->userAddress->location,
		    'rating' => $user->rating,
		    'min_order' => (!empty($user->userAddress->min_order)) ? '₹'.@$user->userAddress->min_order : 'N/A',
		    'avg_delivery_time' => (!empty($user->userAddress->avg_delivery_time)) ? @$user->userAddress->avg_delivery_time : 'N/A',
		];

		$businessCategories = BusinessCategories::where('user_id','=',$input['shop_id'])->where('parent_id', '!=', '0')->get();	
		
		$business_categories = array();
		foreach($businessCategories as $key=>$value) {

		    $business_categories[] = [
				'id' => $value->id,
				'category_id' => $value->category_id,
				'parent_id' => $value->parent_id,
				'name' => $value->category->name,
			];
		}
		
		return response()->json([
			'status'=>'1',
			'msg'=>'store information and categories',
			'store_info'=>$store_info,
			'business_categories'=>$business_categories,
		], $this->successStatus);
	}

	public function productList(Request $request){
		$input = $request->all();

		$productCategoryIds = Categories::where('parent_id', $input['product_category_id'])->pluck('id');

		$query = Products::query();
		$query->where('status','=','1');
		$query->where('user_id','=',$input['shop_id']);
		$query->whereIn('category_id', $productCategoryIds);

		if(!empty($input['search'])) {
			
			$search = trim($input['search']);

			$query->where(function($subQuery) use ($search) {
			
				$subQuery->where('sku', 'like', '%'.$search.'%')
					->orWhere('name', 'like', '%'.$search.'%')
					->orWhere('meta_key', 'like', '%'.$search.'%')
					->orWhere('meta_description', 'like', '%'.$search.'%');
			});
		}

		$query->orderBy('name','asc');
		$products = $query->get();	
		
		$result = array();
		foreach($products as $key=>$value) {

		    $productAttrDetails                 = productAttrDetails($value->id);
		    $productAttrImages                  = productImagesData($value->id);
		    $productStocksQty                   = productStocksQty($value->id);
		    $productAttrmetaData                = metaData($value->id);
			$productproductPrice                = productPrice($value->id);
			
		    $result[$key]['id'] 				= $value->id;
			$result[$key]['name'] 				= $value->name!='' ? $value->name : '';
			//$result[$key]['wishlist']			= wishlistRecode($value->id,$input['user_id']);
			$result[$key]['sale_price'] 		= productSalePrice($value->id);
			$result[$key]['base_price'] 		= productBasePrice($value->id);			
			$result[$key]['category_id'] 		= $value->category_id!='' ? $value->category_id : '0';
			$result[$key]['category_name'] 		= categoryName($value->category_id);
			//$result[$key]['sub_category_id'] 	= $value->sub_cat!='' ? $value->sub_cat : '0';
			//$result[$key]['sub_category_name'] 	= categoryName($value->sub_cat);
			$result[$key]['seller_name'] 		= userName($value->user_id);
			//$result[$key]['product_status'] 	= $value->status;
			if($productStocksQty){
				//$stock_status = $productStocksQty->stock_status;
				$qty          = $productStocksQty->qty;
			} else {
				//$stock_status = 1;
				$qty          = 0;
			}
			// $result[$key]['stock'] 				= $stock_status=='1' ? 'In Stock' : 'Out of Stock';
			// $result[$key]['stock_status'] 		= (int)$stock_status;
			$result[$key]['qty'] 				= $qty;
			$result[$key]['keywords'] 		= $value->meta_key;
			$result[$key]['meta_description'] 	= $value->meta_description;
			$result[$key]['sku'] 				= $value->sku;
			//$result[$key]['attribute_set_id'] 	= $value->attribute_set_id!='' ? $value->attribute_set_id : '0';
			//$result[$key]['attribute_set_name'] = attrName($value->attribute_set_id);
			$result[$key]['description'] 		= $value->description;
			$result[$key]['weight'] 			= $value->weight!='' ? $value->weight : '0';
			$result[$key]['return_policy'] 		= $value->return_policy;
			$result[$key]['warranty'] 			= $value->warranty;
			$result[$key]['shipping_time'] 		= $value->shipping_time;
			
			$productImages = unserialize($productAttrImages);
			if($productImages){
				
				// get single image only
				foreach($productImages as $subKey=>$image){
					if($image!=''){
						$imagemedium = URL::to('/').'/images/products/'.$value->id.'/medium/'.$image;
						$imagethumb = URL::to('/').'/images/products/'.$value->id.'/thumb/'.$image;
						$images = URL::to('/').'/images/products/'.$value->id.'/'.$image;
					} else {
						$images = '';
						$imagemedium = '';
						$imagethumb = '';
					}
					// $result[$key]['product_images'][$subKey]['image']  = $images;				
					// $result[$key]['product_images'][$subKey]['medium'] = $imagemedium;				
					// $result[$key]['product_images'][$subKey]['thumb']  = $imagethumb;
					$result[$key]['product_image']  = $imagethumb;
					
				break;
				}

			} else {
				// $result[$key]['product_images']  = array();
				$result[$key]['product_image']  = '';
			}

			//dump($productAttrDetails);die;
			$result[$key]['product_attribute']  = array();
			if(!empty($productAttrDetails)) {
				foreach($productAttrDetails as $subKey=>$attr){
					// $result[$key]['product_attribute'][$subKey]['stock'] 		= $attr->stock=='1' ? 'In Stock' : 'Out of Stock';
					// $result[$key]['product_attribute'][$subKey]['stock_status'] = (int)$attr->stock;
					$result[$key]['product_attribute'][$subKey]['id']   			= $attr->id;
					$result[$key]['product_attribute'][$subKey]['price'] 			= $attr->sale_price;
					$result[$key]['product_attribute'][$subKey]['sale_price'] 		= $attr->sale_price;
					$result[$key]['product_attribute'][$subKey]['base_price'] 		= $attr->base_price;
					$result[$key]['product_attribute'][$subKey]['qty'] 				= $attr->qty;
					$attrattr_image = unserialize($attr->images);
					
					$attrattrName = unserialize($attr->attrs);
					$result[$key]['product_attribute'][$subKey]['attrs'] = array();	
					$i =0 ;		
					foreach($attrattrName as $subKey1=>$attrValue){				
						$result[$key]['product_attribute'][$subKey]['attrs'][$i]['name']  = $attrValue['attribute_type'];
						$result[$key]['product_attribute'][$subKey]['attrs'][$i]['value'] = $attrValue['attribute_value'];
						$i++; 
					}
					
					$result[$key]['product_attribute'][$subKey]['product_images'] = array();
					if($attrattr_image){
						foreach($attrattr_image as $subKey1=>$attrImg){
							if($attrImg!=''){
								$attrImgs = URL::to('/').'/images/products/'.$value->id.'/attr/'.$attrImg;
								$attrImgmedium = URL::to('/').'/images/products/'.$value->id.'/attr/medium/'.$attrImg;
								$attrImgthumb = URL::to('/').'/images/products/'.$value->id.'/attr/thumb/'.$attrImg;
							} else {
								$attrImgs = '';
								$attrImgmedium =  '';
								$attrImgthumb = '';
							}
							$result[$key]['product_attribute'][$subKey]['product_images'][$subKey1]['image']  = $attrImgs;
							$result[$key]['product_attribute'][$subKey]['product_images'][$subKey1]['medium']  = $attrImgmedium;
							$result[$key]['product_attribute'][$subKey]['product_images'][$subKey1]['thumb']  = $attrImgthumb;
						}	
					} else {
						$result[$key]['product_attribute'][$subKey]['product_images'] = array();
					}			
				}
			}
		}
		
		return response()->json(['status'=>'1','msg'=>'products','products'=>$result], $this->successStatus);
	}

	public function sellerProductList(Request $request){
		$input = $request->all();
		$products = Products::where('status','=','1')->orderBy('name','asc')->where('user_id',$input['user_id'])->get();	
		$result = array();
		foreach($products as $key=>$value){
		    $productAttrDetails                 = productAttrDetails($value->id);
		    $productAttrImages                  = productImagesData($value->id);
		    $productStocksQty                   = productStocksQty($value->id);
		    $result[$key]['id'] 				= $value->id;
			$result[$key]['name'] 				= $value->name!='' ? $value->name : '';
			$result[$key]['category_id'] 		= $value->category_id!='' ? $value->category_id : '0';
			$result[$key]['category_name'] 		= categoryName($value->category_id);
			$result[$key]['sub_category_id'] 	= $value->sub_cat!='' ? $value->sub_cat : '0';
			$result[$key]['sub_category_name'] 	= categoryName($value->sub_cat);
			$result[$key]['seller_name'] 		= userName($value->user_id);
			if($productStocksQty){
				$stock_status = $productStocksQty->stock_status;
				$qty          = $productStocksQty->qty;
			} else {
				$stock_status = '1';
				$qty          = '0';
			}
			$result[$key]['stock'] 				= $stock_status!='1' ? 'In Stock' : 'Out of Stock';
			$result[$key]['stock_status'] 		= (int)$stock_status;
			$result[$key]['qty'] 				= $qty;
			$result[$key]['keywords'] 			= $value->keywords!='' ? $value->keywords : '';
			$result[$key]['sku'] 				= $value->sku!='' ? $value->sku : '';
			$result[$key]['attribute_set_id'] 	= $value->attribute_set_id!='' ? $value->attribute_set_id : '0';
			//$result[$key]['attribute_set_name'] = attrName($value->attribute_set_id);
			$result[$key]['description'] 		= $value->description!='' ? $value->description : '';
			$result[$key]['meta_description'] 	= $value->meta_description!='' ? $value->meta_description : '';
			$result[$key]['weight'] 			= $value->weight!='' ? $value->weight : '0';
			$result[$key]['return_policy'] 		= $value->return_policy;
			$result[$key]['warranty'] 			= $value->warranty;
			$result[$key]['shipping_time'] 		= $value->shipping_time;
			$image = productFirstImages($value->id); 
			$result[$key]['thumb_image'] 	    = $image;
			$result[$key]['sale_price'] 	    = productSalePrice($value->id);
			$result[$key]['base_price'] 	    = productBasePrice($value->id);
			$result[$key]['product_images']     = array();
			$productImages = unserialize($productAttrImages);
			if($productImages){
				foreach($productImages as $subKey=>$image){
					if($image!=''){
						$images = URL::to('/').'/images/products/'.$value->id.'/'.$image;
						$imagemedium = URL::to('/').'/images/products/'.$value->id.'/medium/'.$image;
						$imagethumb = URL::to('/').'/images/products/'.$value->id.'/thumb/'.$image;
					} else {
						$images = '';
						$imagemedium = '';
						$imagethumb = '';
					}
					$result[$key]['product_images'][$subKey]['image']  = $images;				
					$result[$key]['product_images'][$subKey]['medium']  = $imagemedium;				
					$result[$key]['product_images'][$subKey]['thumb']  = $imagethumb;				
				}
			} else {
				$result[$key]['product_images']  = array();
			}
			$result[$key]['product_attribute']  = array();
			foreach($productAttrDetails as $subKey=>$attr){
				$result[$key]['product_attribute'][$subKey]['stock'] 		= $attr->stock=='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['product_attribute'][$subKey]['price'] 		= $attr->attr_price;
				$result[$key]['product_attribute'][$subKey]['id']   		= $attr->id;
				$attrattr_image = unserialize($attr->attr_image);
				if($attrattr_image){
				foreach($attrattr_image as $subKey1=>$attrImg){
					if($attrImg!=''){
						$attrImgs = URL::to('/').'/images/attr/'.$attrImg;
						$attrImgmedium = URL::to('/').'/images/attr/medium/'.$attrImg;
						$attrImgthumb = URL::to('/').'/images/attr/thumb/'.$attrImg;
					} else {
						$attrImgs = '';
						$attrImgmedium =  '';
						$attrImgthumb = '';
					}
					$result[$key]['product_attribute'][$subKey]['product_images'][$subKey1]['image']  = $attrImgs;
					$result[$key]['product_attribute'][$subKey]['product_images'][$subKey1]['medium']  = $attrImgmedium;
					$result[$key]['product_attribute'][$subKey]['product_images'][$subKey1]['thumb']  = $attrImgthumb;
				}	
				} else {
					$result[$key]['product_attribute'][$subKey]['product_images']  = array();
				}			
			}
		}
		
		return response()->json(['status'=>'1','msg'=>'products','products'=>$result], $this->successStatus);
	}

	public function productDetails(Request $request){
		$input = $request->all();
		$products = Products::where('status','=','1')->orderBy('name','asc')->where('id',$input['product_id'])->get();	
		$result = array();
		foreach($products as $key=>$value){
			$getCartQty = getCartQty($value->id,'0',$value->user_id);
			/*print_r($getCartQty);die;*/
		    $productAttrDetails                 = productAttrDetails($value->id);
			//print_r($productAttrDetails);die;
		    $productAttrImages                  = productImagesData($value->id);
		    $productAttrmetaData                = metaData($value->id);
		    $productStocksQty                   = productStocksQty($value->id);
		    $productproductPrice                = productPrice($value->id);
		    $result[$key]['id'] 				= $value->id;
		    $result[$key]['seller_id'] 			= $value->user->id;
		    $result[$key]['seller_name'] 			    = $value->user->name;
			$result[$key]['name'] 				= $value->name!='' ? $value->name : '';
			$result[$key]['sale_price'] 		= productSalePrice($value->id);
			$result[$key]['base_price'] 		= productBasePrice($value->id);
			$result[$key]['category_id'] 		= $value->category_id!='' ? $value->category_id : '0';
			$result[$key]['category_name'] 		= categoryName($value->category_id);
			$result[$key]['sub_category_id'] 	= $value->sub_cat!='' ? $value->sub_cat : '0';
			$result[$key]['sub_category_name'] 	= categoryName($value->sub_cat);
			$result[$key]['seller_name'] 		= userName($value->user_id);
			$result[$key]['product_cart_qty'] 	= getCartQty($input['product_id'],'0',$input['user_id']);
			if($productStocksQty){
				$stock_status = $productStocksQty->stock_status;
				$qty          = $productStocksQty->qty;
			} else {
				$stock_status = 1;
				$qty          = 0;
			}
			
			//$result[$key]['stock'] 				= $stock_status=='1' ? 'In Stock' : 'Out of Stock';
			//$result[$key]['stock_status'] 		= (int)$stock_status;
			$result[$key]['qty'] 				= $qty;
			if($productAttrmetaData){
				$result[$key]['keywords'] 		= $productAttrmetaData->keywords!='' ? $productAttrmetaData->keywords : '';
				$result[$key]['meta_description'] 	= $productAttrmetaData->meta_description!='' ? $productAttrmetaData->meta_description : '';
			} else {
				$result[$key]['keywords'] 		= '';
				$result[$key]['meta_description'] 		= '';
			}
			$result[$key]['sku'] 				= $value->sku!='' ? $value->sku : '';
			$result[$key]['description'] 		= $value->description!='' ? $value->description : '';
			$result[$key]['wishlist']			= wishlistRecode($value->id,$input['user_id']);
			$result[$key]['weight'] 			= $value->weight!='' ? $value->weight : '0';

			$result[$key]['return_policy'] 			= $value->return_policy != '' ? $value->return_policy : 'N/A';
			$result[$key]['warranty'] 			= $value->warranty != '' ? $value->warranty : 'N/A';
			$result[$key]['shipping_time'] 			= $value->shipping_time != '' ? $value->shipping_time : 'N/A';

			$result[$key]['product_images']  = array();
			$productImages = unserialize($productAttrImages);
			if($productImages){
				foreach($productImages as $subKey=>$image){
					if($image!=''){
						$images = URL::to('/').'/images/products/'.$value->id.'/'.$image;
						$imagesmedium = URL::to('/').'/images/products/'.$value->id.'/medium/'.$image;
						$imagesthumb = URL::to('/').'/images/products/'.$value->id.'/thumb/'.$image;
					} else {
						$images = '';
						$imagesmedium = '';
						$imagesthumb = '';
					}
					$result[$key]['product_images'][$subKey]['image']  = $images;				
					$result[$key]['product_images'][$subKey]['medium']  = $imagesmedium;				
					$result[$key]['product_images'][$subKey]['thumb']  = $imagesthumb;				
				}
			}
			
			$j=0;
			$result[$key]['product_attribute'] = array();
			foreach($productAttrDetails as $subKey=>$attr) {
				
				// $result[$key]['product_attribute'][$subKey]['stock'] 		= $attr->attr_stock=='1' ? 'In Stock' : 'Out of Stock';
				// $result[$key]['product_attribute'][$subKey]['stock_status'] = (int)$attr->attr_stock;
				// $result[$key]['product_attribute'][$subKey]['price'] 		= $attr->attr_price;
				// $result[$key]['product_attribute'][$subKey]['qty'] 		    = unserialize($attr->attr_qty);
				// $result[$key]['product_attribute'][$subKey]['product_cart_qty']= getCartQty($input['product_id'],$attr->id,$input['user_id']);
				
				$result[$key]['product_attribute'][$subKey]['id'] = $attr->id;
				$result[$key]['product_attribute'][$subKey]['products_id'] = $attr->products_id;
				$result[$key]['product_attribute'][$subKey]['sku'] = $attr->sku;
				$result[$key]['product_attribute'][$subKey]['barcode'] = $attr->barcode;
				$result[$key]['product_attribute'][$subKey]['base_price'] = $attr->base_price;
				$result[$key]['product_attribute'][$subKey]['sale_price'] = $attr->sale_price;
				$result[$key]['product_attribute'][$subKey]['qty'] = $attr->qty;

				$i =0 ;
				$attrattrName = unserialize($attr->attrs);
				foreach($attrattrName as $subKey1=>$attrValue){				
					$result[$key]['product_attribute'][$subKey]['attrs'][$i]['name']  = $attrValue['attribute_type'];
					$result[$key]['product_attribute'][$subKey]['attrs'][$i]['value'] = $attrValue['attribute_value'];
					$i++; 
				}
				
				$attrattr_image = unserialize($attr->images);
				if($attrattr_image) {
					foreach($attrattr_image as $subKey1=>$attrImg){
						if($attrImg!=''){

							$attrImgs = URL::to('/').'/images/products/'.$value->id.'/attr/'.$attrImg;
							$attrImgmedium = URL::to('/').'/images/products/'.$value->id.'/attr/medium/'.$attrImg;
							$attrImgthumb = URL::to('/').'/images/products/'.$value->id.'/attr/thumb/'.$attrImg;

						} else {
							$attrImgs = '';
							$attrImgmedium =  '';
							$attrImgthumb = '';
						}
						$result[$key]['product_attribute'][$subKey]['product_images'][$subKey1]['image']  = $attrImgs;
						$result[$key]['product_attribute'][$subKey]['product_images'][$subKey1]['medium']  = $attrImgmedium;
						$result[$key]['product_attribute'][$subKey]['product_images'][$subKey1]['thumb']  = $attrImgthumb;
					}	
				} else {
					$result[$key]['product_attribute'][$subKey]['product_images']  = array();
				}

				$j++;	
			}

			// available product attributes
			$attributesArr = [];
			$result[$key]['all_attributes'] = [];
			foreach($result[$key]['product_attribute'] as $itmAsAttribute) {
				foreach($itmAsAttribute['attrs'] as $attributeAndValues) {
					
					$attributesArr[] = $attributeAndValues['name'];
				}
			}
			$attributesArr = array_values(array_unique($attributesArr));
			
			foreach($result[$key]['product_attribute'] as $itmAsAttribute) {
				foreach($itmAsAttribute['attrs'] as $attributeAndValues) {

					$attrArrIndex = array_search($attributeAndValues['name'], $attributesArr);

					$result[$key]['all_attributes'][$attrArrIndex]['attr_type'] = $attributeAndValues['name'];
					$result[$key]['all_attributes'][$attrArrIndex]['attr_values'][] = $attributeAndValues['value'];
				}
			}
		}
		
		return response()->json(['status'=>'1','msg'=>'product detail','product'=>$result[0]], $this->successStatus);
	}

	public function sellerProductCategories(Request $request){
				
		$input = $request->all();
		$user = User::where('id',$input['user_id'])->first(['category']);
		if($user){
			if($user->category!=''){
				$category = explode(',', $user->category);			    
			} else {
				$category = array();
			}
		    
		} else {
			$category = array();
		}
		$categories = Categories::where('status','=','1')->whereIn('id', $category)->orderBy('name','asc')->get();
		
		$result = array();
		foreach($categories as $key=>$value){
		
			$result[$key]['id'] = $value->id;
			$result[$key]['name'] = $value->name;
			$result[$key]['image'] = $value->image;
			$result[$key]['description'] = $value->description;
			
			$result[$key]['sub_category'] = array();
			foreach($value->children as $subKey=>$subcategory){
				$result[$key]['sub_category'][$subKey]['id'] = $subcategory->id;
				$result[$key]['sub_category'][$subKey]['name'] = $subcategory->name;
				$result[$key]['sub_category'][$subKey]['image'] = $subcategory->image;
				$result[$key]['sub_category'][$subKey]['description'] = $subcategory->description;
			}
		}
		
		return response()->json(['status'=>'1','msg'=>'categories','categories'=>$result], $this->successStatus);
	}

	public function addProducts(Request $request) 
    { 
    	$input 							= $request->all();
        $image_array 					= $aatr_image_array = [];
        $input['category_id']  			= catParentId($input['sub_cat']);
        $input['attribute_set_id']  	= getAtributeId($input['sub_cat']);
        if($request->hasFile('image')) {           

            foreach ($request->file('image') as $image) {
                
                $image_name = '';
                $uploadpath = public_path().'\images';
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
	                $image_resize->resize(75, 75);
	                $image_resize->save(public_path('images/thumb/' .$image_name));
	                $image_resize->resize(480,320);
	                $image_resize->save(public_path('images/medium/' .$image_name));
                    $image->move($uploadpath, $image_name);
                }
            }
        }

        $input['status'] 				= '1';        
        $input['product_status'] 		= '1';        
        $input['image'] 				= serialize($image_array);        
        $attribute_data 				= $attributeData = [];   
        
        $attribute_data['attr_stock'] 	= $input['attr_stock'];
        $attribute_data['attr_price'] 	= $input['attr_price'];
             
        $products = Products::create($input);
        $attribute_data['product_id'] = DB::getPdo()->lastInsertId(); 

        $new_array = [];
        foreach($input['attr_name'] as $attr => $attr_array){
            foreach($attr_array as $key => $value){ 
                $new_array[$key][$attr] = $value;
            }
        }
        if(isset($input['attr_stock']) && count($input['attr_stock'])>0){
			foreach ($input['attr_stock'] as $key => $value) {
			
				$attribute_data['attr_name'] = $new_array[$key];
				$attribute_data['attr_stock'] = $value;
				$attribute_data['attr_price'] = $input['attr_price'][$key];

				if($input['attr_image'][$key]!='') {   

					foreach ($input['attr_image'][$key] as $aatr_image) {  

						$aatr_image_name = '';
						$aatr_uploadpath = public_path().'\images\attr';
						$aatr_original_name = $aatr_image->getClientOriginalName();

						if (!$aatr_image->isValid() || empty($aatr_uploadpath)) {
							return $aatr_image_name;
						}

						if ($aatr_image->isValid()) {  
							$aatr_image_prefix = 'product_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
							$aatr_ext = $aatr_image->getClientOriginalExtension();
							$aatr_image_name = $aatr_image_prefix . '.' . $aatr_ext;
							$aatr_image_array[] = $aatr_image_name;
							$image_resize->resize(75, 75);
							$image_resize->save(public_path('images/attr/thumb/' .$aatr_image_name));
							$image_resize->resize(480,320);
							$image_resize->save(public_path('images/attr/medium/' .$aatr_image_name));
							$aatr_image->move($aatr_uploadpath, $aatr_image_name);
						}

					}
				}    
				$attribute_data['attr_image'] = serialize($aatr_image_array);
				
				$attribute_data['attr_name'] = serialize($attribute_data['attr_name']);
				ProductAttributes::create($attribute_data);
			}
        }
		$products->price()->create($input);
		$products->stock()->create($input);
		$products->meta()->create($input);
		$products->image()->create($input);

		return response()->json(['status'=>'1','msg'=>'Product Added Successfully.','product'=>$products], $this-> successStatus);
    }

    public function deleteProduct(Request $request){
		$input = $request->all();
		
		$products = Products::find($input['product_id']);
		$products->delete();		
		$products->price()->delete();
		$products->stock()->delete();
		$products->meta()->delete();
		$products->images()->delete();
		//Price::delete();
		
		return response()->json(['status'=>'1','msg'=>'Product Deleted Successfully','products'=>$input], $this->successStatus);
	}

	public function addCart(Request $request){
		$input 		= $request->all();
		$newCartVal = array();
		$cartData  	= Carts::where('user_id',$input['user_id'])->get();
		if(count($cartData)>0){ 
			$newSeller = getSellerId($input['product_id']);
			$oldseller = getSellerId($cartData[0]['product_id']);
			if($newSeller == $oldseller){
				$status = '1';
				$cartDatas  	= Carts::where('user_id',$input['user_id'])->where('product_id',$input['product_id'])->where('product_attr_id',$input['product_attr_id'])->first();
				if($cartDatas){
					$newCartVal['price'] =  $cartDatas->price+$input['price'];
					$newCartVal['qty']   =  $cartDatas->qty+$input['qty'];
					Carts::where('id',$cartDatas->id)->update($newCartVal);
					$msg    =  "Product added into cart";
				} else {
					$cart  	= Carts::create($input);
					$msg    = "Product added into cart";
				}

			} else {
                $msg = "Your cart contain product from different seller please select same seller's product or empty this cart ?";
				$status = '0';
			}			
		} else { 
			$cart  	= Carts::create($input);
			$msg    = "Product added into cart";	
			$status = '1';
		}	
		$cartRecode  	= Carts::where('user_id',$input['user_id'])->get();
		$count 		= count($cartRecode);			
		return response()->json(['status'=>$status,'msg'=>$msg ,'cartData'=>$cartRecode,'count'=>$count], $this->successStatus);
	}

	public function updateCart(Request $request){
		$input 		= $request->all();
		
		$cart  		= Carts::where('id',$input['cart_id'])->first();		
		
		$product = ProductAttributes::where('id',$cart->product_attr_id)->first();
		if(!empty($product->id)){
			$input['price'] = $product->attr_price;
			$cart->fill($input)->save();
			$cart['total'] = $input['qty'] * $input['price'];
		}
		else{
			$cart->fill($input)->save();
			$cart['total'] = $input['qty'] * $input['price'];
		}
		return response()->json(['status'=>'1','msg'=>'Update Cart Product','cartData'=>$cart], $this->successStatus);
	}
	
	public function emptyCart(Request $request){
		$input 		= $request->all();
		
		$cart  		= Carts::Where('user_id',$input['user_id'])->delete();
		//dd($cart);
		//$cart->fill($input)->save();
		//$cart['total'] = $input['qty'] * $input['price'];
		return response()->json(['status'=>'1','msg'=>'Empty Cart successfully','cartData'=>'0'], $this->successStatus);
	}
	
	public function updateCartddd(Request $request){
		$input 		= $request->all();
		$cart  		= Carts::where('id',$input['cart_id'])->first();
		$cart_id    = $input['cart_id'];
		unset($input['cart_id']);
		Carts::where('id',$cart_id)->update($input);
		return response()->json(['status'=>'1','msg'=>'Update Cart Product','cartData'=>$cart], $this->successStatus);
	}

	public function updateWishlist(Request $request){
		$input 		= $request->all();
		$Wish  		= Wishlists::where('id',$input['wishlist_id'])->first();
		$Wish->fill($input)->save();
		return response()->json(['status'=>'1','msg'=>'Update Wishlist Product','Data'=>$Wish], $this->successStatus);
	}

	public function addOrRemoveWishlist(Request $request){
		$input 		= $request->all();
		$DataWish   = Wishlists::where('user_id',$input['user_id'])->where('product_id',$input['product_id'])->where('product_attr_id',$input['product_attr_id'])->first();
		if($DataWish){
			$DataWish->delete();
			$msg    = "Product remove from wishlist";
			$status = 2;
		} else {
			$msg    = "Product added into wishlist";
			$data  	= Wishlists::create($input);
			$status = 1;
		}		
		$Data  	    = Wishlists::where('user_id',$input['user_id'])->get();
		$count 		= count($Data);
		return response()->json(['status'=>$status,'msg'=>$msg,'Data'=>$Data,'count'=>$count], $this->successStatus);
	}

	public function deleteProductFromCart(Request $request){
		$input = $request->all();
		Carts::where('id',$input['cart_id'])->delete();  
		$cartData  	= Carts::where('user_id',$input['user_id'])->get();
		$result     = array();
		//$cartData  	= Carts::where('user_id',$input['user_id'])->get();
		/*foreach ($cartData as $key => $value) {				
			
			$result[$key]['cart_id'] 				= $value->id;
			$result[$key]['price'] 					= $value->price!='' ? $value->price : 0;
			$result[$key]['qty'] 					= $value->qty!='' ? $value->qty : 0;
			$result[$key]['product_id'] 			= $value->product_id!='' ? $value->product_id : 0;
			$products = Products::where('status','=','1')->where('id','=',$value->product_id)->orderBy('name','asc')->get();
			$result[$key]['product_data']  = array();
			foreach($products as $key1=>$product){
			    
			    $productAttrImages                  = productImages($product->id);
			    $productStocksQty                   = productStocksQty($product->id);
			    $productAttrmetaData                = metaData($product->id);
			    
				$result[$key]['product_data'][$key1]['name'] 		        = $product->name!='' ? $product->name : '';
				$result[$key]['product_data'][$key1]['sale_price'] 			= productSalePrice($product->id);
				$result[$key]['product_data'][$key1]['base_price'] 			= productBasePrice($product->id);			
				$result[$key]['product_data'][$key1]['qty'] 		        = $product->qty!='' ? $product->qty : 0;
				
				if($productStocksQty){
					$stock_status = $productStocksQty->stock_status;
					$qty          = $productStocksQty->qty;
				} else {
					$stock_status = 1;
					$qty          = 0;
				}
				$result[$key]['product_data'][$key1]['stock'] 				= $stock_status!='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['product_data'][$key1]['qty'] 				= $qty;
				if($productAttrmetaData){
					$result[$key]['product_data'][$key1]['keywords'] 		= $productAttrmetaData->keywords!='' ? $productAttrmetaData->keywords : '';
					$result[$key]['product_data'][$key1]['meta_description'] 	= $productAttrmetaData->meta_description!='' ? $productAttrmetaData->meta_description : '';
				} else {
					$result[$key]['product_data'][$key1]['keywords'] 		= '';
					$result[$key]['product_data'][$key1]['meta_description'] 		= '';
				}
				
				$result[$key]['product_data'][$key1]['sku'] 				= $product->sku!='' ? $product->sku : '';
				$result[$key]['product_data'][$key1]['weight'] 			= $product->weight!='' ? $product->weight : '0';
				$productImages = unserialize($productAttrImages);
				if($productImages){
					$image = URL::to('/').'/images/'.$productImages[0];
					$result[$key]['product_data'][$key1]['product_images']  = $image;
				} else {
					$result[$key]['product_data'][$key1]['product_images']  = '';
				}
				$productAttrDetails                 = ProductAttributes::where('id',$value->product_attr_id)->first();
				if(isset($productAttrDetails->attr_stock)){
					$result[$key]['product_data'][$key1]['attr_stock_status'] = $productAttrDetails->attr_stock;
					$result[$key]['product_data'][$key1]['attr_stock'] 		= $productAttrDetails->attr_stock=='1' ? 'In Stock' : 'Out of Stock';
				} else {
					$result[$key]['product_data'][$key1]['attr_stock_status'] = 0;
					$result[$key]['product_data'][$key1]['attr_stock'] 		= 'Out of Stock';
				}

				if(isset($productAttrDetails->attr_price)){
					$result[$key]['product_data'][$key1]['attr_price'] 		= $productAttrDetails->attr_price;
				} else {
					$result[$key]['product_data'][$key1]['attr_price'] 		= 0;
				}	



				if(isset($productAttrDetails->attr_image)){
					$attrattr_image = unserialize($productAttrDetails->attr_image);

					$result[$key]['product_data'][$key1]['attr_id']   		= $productAttrDetails->id;
					if($attrattr_image){
						$attrImg = URL::to('/').'/images/attr/'.$attrattr_image[0];
						$result[$key]['product_data'][$key1]['attr_image']  = $attrImg;	
					} else {
						$result[$key]['product_data'][$key1]['attr_image']  = '';
					}
				} else {
					$result[$key]['product_data'][$key1]['attr_image']  = '';
				}
				

				
				

			}	
		}*/
		$cartData  	= Carts::where('user_id',$input['user_id'])->get();
		foreach ($cartData as $key => $value) {				
			$result[$key]['cart_id'] 				= $value->id;
			$result[$key]['price'] 					= $value->price!='' ? $value->price : 0;
			$result[$key]['qty'] 					= $value->qty!='' ? $value->qty : 0;
			$result[$key]['product_id'] 			= $value->product_id!='' ? $value->product_id : 0;
			$product = $products = Products::where('status','=','1')->where('id','=',$value->product_id)->orderBy('name','asc')->first();
			$result[$key]['product_data']  = array();
			
			/*foreach($products as $key1=>$product){*/
			    
			    $productAttrImages                  = productImagesData($product->id);
			    $productStocksQty                   = productStocksQty($product->id);
			    $productAttrmetaData                = metaData($product->id);
			    
				$result[$key]['name'] 		        = $product->name!='' ? $product->name : '';
				$result[$key]['sale_price'] 			= productSalePrice($product->id);
				$result[$key]['base_price'] 			= productBasePrice($product->id);			
				$result[$key]['qty_product'] 		        = $product->qty!='' ? $product->qty : 0;
				$serllerId = getSellerId($value->product_id);
				$user = User::where('id',$serllerId)->first();
				$result[$key]['seller_name'] = $user->name!='' ? $user->name : '';
				$result[$key]['seller_mobile'] = $user->mobile!='' ? $user->mobile : '';
				if($productStocksQty){
					$stock_status = $productStocksQty->stock_status;
					$qty          = $productStocksQty->qty;
				} else {
					$stock_status = 1;
					$qty          = 0;
				}
				$result[$key]['stock'] 				= $stock_status!='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['qty_product_stock'] 				= $qty;

				$serllerId = getSellerId($value->product_id);
				$user = User::where('id',$serllerId)->first();
				$UserOtherDetails = UserAddress::where('user_id',$serllerId)->first();
				if($user){
					$result[$key]['seller_name']    = $user->name!='' ? $user->name : '';
					$result[$key]['seller_profile'] = userProfile($input['user_id']);
					$result[$key]['city']         	= $UserOtherDetails->city!='' ? $UserOtherDetails->city : '';
					$result[$key]['state']        	= $UserOtherDetails->state!='' ? $UserOtherDetails->state : '';
					$result[$key]['country']      	= $UserOtherDetails->country!='' ? $UserOtherDetails->country : '';
					$result[$key]['shop_address'] 	= $UserOtherDetails->shop_address!='' ? $UserOtherDetails->shop_address : '';
				} else {
					$result[$key]['seller_name']    = '';
					$result[$key]['seller_profile'] = '';
					$result[$key]['city']         	= '';
					$result[$key]['state']        	= '';
					$result[$key]['country']      	= '';
					$result[$key]['shop_address'] 	= '';
				}
				

				if($productAttrmetaData){
					$result[$key]['keywords'] 		= $productAttrmetaData->keywords!='' ? $productAttrmetaData->keywords : '';
					$result[$key]['meta_description'] 	= $productAttrmetaData->meta_description!='' ? $productAttrmetaData->meta_description : '';
				} else {
					$result[$key]['keywords'] 		= '';
					$result[$key]['meta_description'] 		= '';
				}
				
				$result[$key]['sku'] 				= $product->sku!='' ? $product->sku : '';
				$result[$key]['weight'] 			= $product->weight!='' ? $product->weight : '0';
				$productImages = unserialize($productAttrImages);
				if($productImages){
					$image = URL::to('/').'/images/'.$productImages[0];
					$result[$key]['product_images']  = $image;
				} else {
					$result[$key]['product_images']  = '';
				}
				$productAttrDetails                 = ProductAttributes::where('id',$value->product_attr_id)->first();
				$result[$key]['attr_stock'] 		= $productAttrDetails['stock']=='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['attr_price'] 		= $productAttrDetails['attr_price'];
				$attrattr_image = unserialize($productAttrDetails['attr_image']);
				$result[$key]['attr_id']   		= $productAttrDetails['id'];

				if($attrattr_image){
					$attrImg = URL::to('/').'/images/attr/'.$attrattr_image[0];
					$result[$key]['attr_image']  = $attrImg;	
				} else {
					$result[$key]['attr_image']  = '';
				}
			/*}*/	
		}
		$count 		= count($cartData);
		return response()->json(['status'=>'1','msg'=>'Product Deleted From Cart Successfully','cartData'=>$result,'count'=>$count], $this->successStatus);
	}

	public function deleteProductFromWishlist(Request $request){
		$input = $request->all();
		Wishlists::where('id',$input['id'])->delete();  
		$Data  	    = Wishlists::where('user_id',$input['user_id'])->get();
		$result     = array();
		$cartData  	= Wishlists::where('user_id',$input['user_id'])->get();
		foreach ($cartData as $key => $value) {				
			
			$result[$key]['id'] 				= $value->id;
			$result[$key]['price'] 					= $value->price!='' ? $value->price : 0;
			$result[$key]['qty'] 					= $value->qty!='' ? $value->qty : 0;
			$result[$key]['product_id'] 			= $value->product_id!='' ? $value->product_id : 0;
			$products = Products::where('status','=','1')->where('id','=',$value->product_id)->orderBy('name','asc')->get();
			$result[$key]['product_data']  = array();
			foreach($products as $key1=>$product){
			    
			    $productAttrImages                  = productImagesData($product->id);
			    $productStocksQty                   = productStocksQty($product->id);
			    $productAttrmetaData                = metaData($product->id);
			    
				$result[$key]['product_data'][$key1]['name'] 		        = $product->name!='' ? $product->name : '';
				$result[$key]['product_data'][$key1]['sale_price'] 			= productSalePrice($product->id);
				$result[$key]['product_data'][$key1]['base_price'] 			= productBasePrice($product->id);			
				$result[$key]['product_data'][$key1]['qty'] 		        = $product->qty!='' ? $product->qty : 0;
				
				if($productStocksQty){
					$stock_status = $productStocksQty->stock_status;
					$qty          = $productStocksQty->qty;
				} else {
					$stock_status = 1;
					$qty          = 0;
				}
				$result[$key]['product_data'][$key1]['stock'] 				= $stock_status!='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['product_data'][$key1]['qty'] 				= $qty;
				if($productAttrmetaData){
					$result[$key]['product_data'][$key1]['keywords'] 		= $productAttrmetaData->keywords!='' ? $productAttrmetaData->keywords : '';
					$result[$key]['product_data'][$key1]['meta_description'] 	= $productAttrmetaData->meta_description!='' ? $productAttrmetaData->meta_description : '';
				} else {
					$result[$key]['product_data'][$key1]['keywords'] 		= '';
					$result[$key]['product_data'][$key1]['meta_description'] 		= '';
				}
				
				$result[$key]['product_data'][$key1]['sku'] 				= $product->sku!='' ? $product->sku : '';
				$result[$key]['product_data'][$key1]['weight'] 			= $product->weight!='' ? $product->weight : '0';
				$productImages = unserialize($productAttrImages);
				if($productImages){
					$image = URL::to('/').'/images/'.$productImages[0];
					$result[$key]['product_data'][$key1]['product_images']  = $image;
				} else {
					$result[$key]['product_data'][$key1]['product_images']  = '';
				}
				$productAttrDetails                 = ProductAttributes::where('id',$value->product_attr_id)->first();
				$result[$key]['product_data'][$key1]['attr_price'] 		=   $productAttrDetails['attr_price'];
				$result[$key]['product_data'][$key1]['attr_stock_status'] = $productAttrDetails['stock'];
				$result[$key]['product_data'][$key1]['attr_stock'] 		= $productAttrDetails['stock']=='1' ? 'In Stock' : 'Out of Stock';
				
				$attrattr_image = unserialize($productAttrDetails['attr_image']);
				$result[$key]['product_data'][$key1]['attr_id']   		= $productAttrDetails['id'];
				if($attrattr_image){
					$attrImg = URL::to('/').'/images/attr/'.$attrattr_image[0];
					$result[$key]['product_data'][$key1]['attr_image']  = $attrImg;	
				} else {
					$result[$key]['product_data'][$key1]['attr_image']  = '';
				}
			}	
		}
		$count 		= count($cartData);
		return response()->json(['status'=>'1','msg'=>'Product Deleted From Wishlist Successfully','Data'=>$result,'count'=>$count], $this->successStatus);
	}
    
	public function getCartProducts(Request $request){
		$input 		= $request->all();
		$result     = array();
		$cartData  	= Carts::where('user_id',$input['user_id'])->get();
		
		foreach ($cartData as $key => $value) {	
			$product = ProductAttributes::where('id',$value->product_attr_id)->first();
			if(!empty($product->id)){
				$price = $product->attr_price;
			}
			else{
				$product_price = Price::where('products_id',$value->product_id)->first();
				$price = $product_price->sale_price;
			} 
			
			$result[$key]['cart_id'] 				= $value->id;
			$price = $result[$key]['price'] 		= $price!='' ? $price : 0;
			$qty = $result[$key]['qty'] 			= $value->qty!='' ? $value->qty : 0;
			$result[$key]['total'] = $price * $qty;
			
			$result[$key]['product_id'] 			= $value->product_id!='' ? $value->product_id : 0;
			$product = $products = Products::where('status','=','1')->where('id','=',$value->product_id)->orderBy('name','asc')->first();
			$result[$key]['product_data']  = array();
			
			/*foreach($products as $key1=>$product){*/
			    
			    $productAttrImages                  = productImagesData($product->id);
			    $productStocksQty                   = productStocksQty($product->id);
			    $productAttrmetaData                = metaData($product->id);
			    
				$result[$key]['name'] 		        = $product->name!='' ? $product->name : '';
				$result[$key]['sale_price'] 			= productSalePrice($product->id);
				$result[$key]['base_price'] 			= productBasePrice($product->id);			
				$result[$key]['qty_product'] 		        = $product->qty!='' ? $product->qty : 0;
				$serllerId = getSellerId($value->product_id);
				$user = User::where('id',$serllerId)->first();
				$result[$key]['seller_name'] = $user->name!='' ? $user->name : '';
				$result[$key]['seller_mobile'] = $user->mobile!='' ? $user->mobile : '';
				if($productStocksQty){
					$stock_status = $productStocksQty->stock_status;
					$qty          = $productStocksQty->qty;
				} else {
					$stock_status = 1;
					$qty          = 0;
				}
				$result[$key]['stock'] 				= $stock_status!='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['qty_product_stock'] 				= $qty;

				$serllerId = getSellerId($value->product_id);
				$user = User::where('id',$serllerId)->first();
				// dd($user);
				$UserOtherDetails = UserAddress::where('user_id',$serllerId)->first();
				if($user){
					$result[$key]['seller_name']    = $user->name!='' ? $user->name : '';
					$result[$key]['seller_profile'] = userProfile($serllerId);
					$result[$key]['city']         	= $UserOtherDetails->city!='' ? $UserOtherDetails->city : '';
					$result[$key]['state']        	= $UserOtherDetails->state!='' ? $UserOtherDetails->state : '';
					$result[$key]['country']      	= $UserOtherDetails->country!='' ? $UserOtherDetails->country : '';
					$result[$key]['shop_address'] 	= $UserOtherDetails->shop_address!='' ? $UserOtherDetails->shop_address : '';
				} else {
					$result[$key]['seller_name']    = '';
					$result[$key]['seller_profile'] = '';
					$result[$key]['city']         	= '';
					$result[$key]['state']        	= '';
					$result[$key]['country']      	= '';
					$result[$key]['shop_address'] 	= '';
				}
				

				if($productAttrmetaData){
					$result[$key]['keywords'] 		= $productAttrmetaData->keywords!='' ? $productAttrmetaData->keywords : '';
					$result[$key]['meta_description'] 	= $productAttrmetaData->meta_description!='' ? $productAttrmetaData->meta_description : '';
				} else {
					$result[$key]['keywords'] 		= '';
					$result[$key]['meta_description'] 		= '';
				}
				
				$result[$key]['sku'] 				= $product->sku!='' ? $product->sku : '';
				$result[$key]['weight'] 			= $product->weight!='' ? $product->weight : '0';
				$productImages = unserialize($productAttrImages);
				if($productImages){
					// $image = URL::to('/').'/images/'.$productImages[0];
					$result[$key]['product_image']  = productFirstImages($product->id);
				} else {
					$result[$key]['product_image']  = '';
				}
				$productAttrDetails                 = ProductAttributes::where('id',$value->product_attr_id)->first();
				$result[$key]['attr_stock'] 		= $productAttrDetails['attr_stock']=='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['attr_price'] 		= $productAttrDetails['attr_price'];
				$result[$key]['attr_name'] 		= unserialize($productAttrDetails['attr_name']);
				$attrattr_image = unserialize($productAttrDetails['attr_image']);
				$result[$key]['attr_id']   		= $productAttrDetails['id'];

				if($attrattr_image){
					$attrImg = URL::to('/').'/images/attr/'.$attrattr_image[0];
					$result[$key]['attr_image']  = $attrImg;	
				} else {
					$result[$key]['attr_image']  = '';
				}
			/*}*/	
		}
		
		return response()->json(['status'=>'1','msg'=>'Cart Products List','cartData'=>$result], $this->successStatus);
	}

	public function getWishlistProducts(Request $request){
		$input 		= $request->all();
		$result     = array();
		$cartData  	= Wishlists::where('user_id',$input['user_id'])->get();
		foreach ($cartData as $key => $value) {				
			
			$result[$key]['id'] 					= $value->id;
			$result[$key]['price'] 					= $value->price!='' ? $value->price : 0;
			$result[$key]['qty'] 					= $value->qty!='' ? $value->qty : 0;
			$result[$key]['product_id'] 			= $value->product_id!='' ? $value->product_id : 0;

			$product = $products = Products::where('status','=','1')->where('id','=',$value->product_id)->orderBy('name','asc')->first();

			$result[$key]['product_data']  = array();
			/*foreach($products as $key1=>$product){
			    
			    $productAttrImages                  = productImages($product->id);
			    $productStocksQty                   = productStocksQty($product->id);
			    $productAttrmetaData                = metaData($product->id);
			    
				$result[$key]['product_data'][$key1]['name'] 		        = $product->name!='' ? $product->name : '';
				$result[$key]['product_data'][$key1]['sale_price'] 			= productSalePrice($product->id);
				$result[$key]['product_data'][$key1]['base_price'] 			= productBasePrice($product->id);			
				$result[$key]['product_data'][$key1]['qty'] 		        = $product->qty!='' ? $product->qty : 0;
				

				$serllerId = getSellerId($value->product_id);
				$user = User::where('id',$serllerId)->first();
				$UserOtherDetails = UserAddress::where('id',$serllerId)->first();

				if($user){
					$result[$key]['product_data'][$key1]['seller_name']    = $user->name!='' ? $user->name : '';
					$result[$key]['product_data'][$key1]['seller_profile'] = userProfile($input['user_id']);
					if($UserOtherDetails){
						
						$result[$key]['product_data'][$key1]['city']         	= $UserOtherDetails->city!='' ? $UserOtherDetails->city : '';
						$result[$key]['product_data'][$key1]['state']        	= $UserOtherDetails->state!='' ? $UserOtherDetails->state : '';
						$result[$key]['product_data'][$key1]['country']      	= $UserOtherDetails->country!='' ? $UserOtherDetails->country : '';
						$result[$key]['product_data'][$key1]['shop_address'] 	= $UserOtherDetails->shop_address!='' ? $UserOtherDetails->shop_address : '';
					} else {
						$result[$key]['product_data'][$key1]['city']         	= '';
						$result[$key]['product_data'][$key1]['state']        	= '';
						$result[$key]['product_data'][$key1]['country']      	= '';
						$result[$key]['product_data'][$key1]['shop_address'] 	= '';
					}
				} else {
					$result[$key]['product_data'][$key1]['seller_name']    = '';
					$result[$key]['product_data'][$key1]['seller_profile'] = '';
					$result[$key]['product_data'][$key1]['city']         	= '';
					$result[$key]['product_data'][$key1]['state']        	= '';
					$result[$key]['product_data'][$key1]['country']      	= '';
					$result[$key]['product_data'][$key1]['shop_address'] 	= '';
				}
				
				
				if($productStocksQty){
					$stock_status = $productStocksQty->stock_status;
					$qty          = $productStocksQty->qty;
				} else {
					$stock_status = 1;
					$qty          = 0;
				}
				$result[$key]['product_data'][$key1]['stock'] 				= $stock_status!='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['product_data'][$key1]['qty'] 				= $qty;
				if($productAttrmetaData){
					$result[$key]['product_data'][$key1]['keywords'] 		= $productAttrmetaData->keywords!='' ? $productAttrmetaData->keywords : '';
					$result[$key]['product_data'][$key1]['meta_description'] 	= $productAttrmetaData->meta_description!='' ? $productAttrmetaData->meta_description : '';
				} else {
					$result[$key]['product_data'][$key1]['keywords'] 		= '';
					$result[$key]['product_data'][$key1]['meta_description'] 		= '';
				}
				
				$result[$key]['product_data'][$key1]['sku'] 				= $product->sku!='' ? $product->sku : '';
				$result[$key]['product_data'][$key1]['weight'] 			= $product->weight!='' ? $product->weight : '0';
				$productImages = unserialize($productAttrImages);
				if($productImages){
					$image = URL::to('/').'/images/'.$productImages[0];
					$result[$key]['product_data'][$key1]['product_images']  = $image;
				} else {
					$result[$key]['product_data'][$key1]['product_images']  = '';
				}
				$productAttrDetails                 = ProductAttributes::where('id',$value->product_attr_id)->first();
				$result[$key]['product_data'][$key1]['attr_price'] 		=   $productAttrDetails['attr_price'];
				$result[$key]['product_data'][$key1]['attr_stock_status'] = $productAttrDetails['stock'];
				$result[$key]['product_data'][$key1]['attr_stock'] 		= $productAttrDetails['stock']=='1' ? 'In Stock' : 'Out of Stock';
				
				$attrattr_image = unserialize($productAttrDetails['attr_image']);
				$result[$key]['product_data'][$key1]['attr_id']   		= $productAttrDetails['id'];
				if(sizeof($attrattr_image)>0){
					$attrImg = URL::to('/').'/images/attr/'.$attrattr_image[0];
					$result[$key]['product_data'][$key1]['attr_image']  = $attrImg;	
				} else {
					$result[$key]['product_data'][$key1]['attr_image']  = '';
				}
			}*/	
			if($product){
			    $productAttrImages                  = productImagesData($product->id);
			    $productStocksQty                   = productStocksQty($product->id);
			    $productAttrmetaData                = metaData($product->id);
			    
				$result[$key]['name'] 		        = $product->name!='' ? $product->name : '';
				$result[$key]['sale_price'] 			= productSalePrice($product->id);
				$result[$key]['base_price'] 			= productBasePrice($product->id);			
				$result[$key]['qty'] 		        = $product->qty!='' ? $product->qty : 0;
				$serllerId = getSellerId($value->product_id);
				$user = User::where('id',$serllerId)->first();
				$result[$key]['seller_name'] = $user->name!='' ? $user->name : '';
				$result[$key]['seller_mobile'] = $user->mobile!='' ? $user->mobile : '';
				if($productStocksQty){
					$stock_status = $productStocksQty->stock_status;
					$qty          = $productStocksQty->qty;
				} else {
					$stock_status = 1;
					$qty          = 0;
				}
				$result[$key]['stock'] 				= $stock_status!='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['qty'] 				= $qty;

				$serllerId = getSellerId($value->product_id);
				$user = User::where('id',$serllerId)->first();
				$UserOtherDetails = UserAddress::where('user_id',$serllerId)->first();
				if($user){
					$result[$key]['seller_name']    = $user->name!='' ? $user->name : '';
					$result[$key]['seller_profile'] = userProfile($input['user_id']);
					$result[$key]['city']         	= $UserOtherDetails->city!='' ? $UserOtherDetails->city : '';
					$result[$key]['state']        	= $UserOtherDetails->state!='' ? $UserOtherDetails->state : '';
					$result[$key]['country']      	= $UserOtherDetails->country!='' ? $UserOtherDetails->country : '';
					$result[$key]['shop_address'] 	= $UserOtherDetails->shop_address!='' ? $UserOtherDetails->shop_address : '';
				} else {
					$result[$key]['seller_name']    = '';
					$result[$key]['seller_profile'] = '';
					$result[$key]['city']         	= '';
					$result[$key]['state']        	= '';
					$result[$key]['country']      	= '';
					$result[$key]['shop_address'] 	= '';
				}
				

				if($productAttrmetaData){
					$result[$key]['keywords'] 		= $productAttrmetaData->keywords!='' ? $productAttrmetaData->keywords : '';
					$result[$key]['meta_description'] 	= $productAttrmetaData->meta_description!='' ? $productAttrmetaData->meta_description : '';
				} else {
					$result[$key]['keywords'] 		= '';
					$result[$key]['meta_description'] 		= '';
				}
				
				$result[$key]['sku'] 				= $product->sku!='' ? $product->sku : '';
				$result[$key]['weight'] 			= $product->weight!='' ? $product->weight : '0';
				$productImages = unserialize($productAttrImages);
				if($productImages){
					// $image = URL::to('/').'/images/'.$productImages[0];
					$image = productFirstImages($product->id);
					$result[$key]['product_images']  = $image;
				} else {
					$result[$key]['product_images']  = '';
				}
				$productAttrDetails                 = ProductAttributes::where('id',$value->product_attr_id)->first();
				$result[$key]['attr_stock'] 		= $productAttrDetails['stock']=='1' ? 'In Stock' : 'Out of Stock';
				$result[$key]['attr_price'] 		= $productAttrDetails['attr_price'];
				$attrattr_image = unserialize($productAttrDetails['attr_image']);
				$result[$key]['attr_id']   		= $productAttrDetails['id'];
				if($attrattr_image){
					$attrImg = URL::to('/').'/images/attr/'.$attrattr_image[0];
					$result[$key]['attr_image']  = $attrImg;	
				} else {
					$result[$key]['attr_image']  = '';
				}
		    }		
		}
		return response()->json(['status'=>'1','msg'=>'Wishlist Products List','Data'=>$result], $this->successStatus);
	}

}