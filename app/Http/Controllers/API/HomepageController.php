<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Models\Products; 
use App\Models\Subscriptions; 
use App\Models\Categories;
use App\Models\BusinessCategories;
use App\Models\UserAddress; 
use App\Models\HomePageBanner; 
use App\Models\ProfilePictures; 
use App\Models\Pages; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Illuminate\Support\Str;
use URL;
use DB;
use Helper;

class HomepageController extends Controller
{

	public $successStatus = 200;
	
	public function home_banner(Request $request){
		$input = $request->all();
		
		if(isset($input['user_id']) && !empty($input['user_id'])){
			$users = User::findOrFail($input['user_id']);
			$data['app_id'] = $input['app_id'];
			$users->fill($data)->save();
		}
		
		$getSellers = User::where('status','=','1')->where('role', 'Seller')->where('email', '!=', '')->get();
		$sellers = array(); 
	    foreach ($getSellers as $key => $value) {
	    	$sellers[$key]['seller_id'] = $value->id;
	    	$sellers[$key]['name'] = $value->name;
		    $sellers[$key]['mobile'] = $value->mobile;
		    $sellers[$key]['email'] = $value->email;
		    $sellers[$key]['address'] = @$value->userAddress->location;
		    $sellers[$key]['min_order'] = (!empty($value->userAddress->min_order)) ? '₹'.@$value->userAddress->min_order : 'N/A';
		    $sellers[$key]['image'] = url('/').'/images/profile/'.@$value->profilePicture->picture;
		}
		
		$HomePageBanner = HomePageBanner::where('status','=','1')->get();
		$banner = array(); 
	    foreach ($HomePageBanner as $key => $value) {
	    	$banner[$key]['banner_name'] = $value->banner_name!='' ? $value->banner_name : '';
		    $banner[$key]['banner_description'] = $value->banner_description!='' ? $value->banner_description : '';
		    $banner[$key]['role'] = $value->role!='' ? $value->role : '';
		    $banner[$key]['banner_image'] = url('/').'/images/banners/'.$value->banner_image;
		}
		
	    $categories = BusinessCategories::where('user_id', 1)->where('parent_id', 0)->get(); 
		
		$cat = array(); 
	    foreach ($categories as $key => $catvalue) {
			if($catvalue->category->status=='1'){
				$cat[$key]['id'] = $catvalue->category->id;
				$cat[$key]['name'] = $catvalue->category->name;
				if($catvalue->category->image==''){
					$cat[$key]['image'] = ''; 
				} else {
					$cat[$key]['image'] = url('/').'/images/categories/'.$catvalue->category->image;
				} 
			}
	    }
		
	    // $cat = array_chunk($cat,2);
		return response()->json(['status'=>'1','msg'=>'Home Screen','banners'=>$banner, 'categories'=>$cat, 'sellers' => $sellers], $this->successStatus);
	}

	public function shoplistFilter(Request $request){
		$input = $request->all();
		$lat = $input['latitude'];
		$lng = $input['longitude'];
		if(isset($input['order'])){
			$order = $input['order'];
		} else {
			$order = 'Desc';
		}
		if(isset($input['rating'])){
			$rating = $input['rating'];
		} else {
			$rating = 'Desc';
		}
		if(isset($input['min_distance'])){
			$min_distance = $input['min_distance'];
		} else {
			$min_distance = '';
		}
		if(isset($input['max_distance'])){
			$max_distance = $input['max_distance'];
		} else {
			$max_distance = '';
		}

		if(isset($input['star'])){
			$star = $input['star'];
		} else {
			$star = '';
		}

		if($min_distance !='' && $max_distance !=''){ 
			$distance = 'distance >= "'.$min_distance.'" and distance <= "'.$max_distance.'"';
		} else {  
			$distance = 'distance <= 20000';
		}

		$id = array();
		$ratingdata = User::where('status','=','1')->where('is_deleted','=','0')->where('role','Seller')->where('rating','<=',$star)->orderBy('rating','asc')->get();
		
         foreach ($ratingdata as $key => $value) {
         	$id[] = $value->id;
         }

         $idval = implode(",",$id);
         if(isset($star)){
         	$sql = "SELECT   *,
			( 6371 * acos( cos( radians({$lat}) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( `latitude` ) ) ) ) AS distance
			FROM `UserAddresses` where status = 1 and user_id IN (".$idval.") Group By user_id
			HAVING ".$distance."
			ORDER BY user_id ".$order;

         } else {
         	$sql = "SELECT   *,
			( 6371 * acos( cos( radians({$lat}) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( `latitude` ) ) ) ) AS distance
			FROM `UserAddresses` where status = 1 Group By user_id
			HAVING ".$distance."
			ORDER BY distance ".$order;
         }
		$result = array();
		$shops = DB::select($sql);
		$shops_count = count($shops);
		if($shops_count > 0)
		{
		
		foreach($shops as $key=>$value){
			$result[$key]['id']        = $value->user_id;		
			$result[$key]['name']      = userName($value->user_id);		
			$result[$key]['user_id']   = $value->user_id!='' ? $value->user_id : '';		
			$result[$key]['location']  = $value->location!='' ? $value->location : '';		
			$result[$key]['house_no']  = $value->house_no!='' ? $value->house_no : '';		
			$result[$key]['address']   = $value->address!='' ? $value->address : '';		
			$result[$key]['landmark']  = $value->landmark!='' ? $value->landmark : '';		
			$result[$key]['type']  	   = $value->type!='' ? $value->type : '';	
			$result[$key]['distance']  = $value->distance!='' ? $value->distance : '0';		
			$result[$key]['rating']    = getRating($value->user_id);	
			$result[$key]['total_rating']    = getTotalRating($value->user_id);	
			$result[$key]['min_order'] = $value->min_order!='' ? $value->min_order : '0';
			$result[$key]['avg_delivery_time'] = $value->avg_delivery_time!='' ? $value->avg_delivery_time : 'N/A';
			$result[$key]['image']     = userProfile($value->user_id);
			$result[$key]['product_categories']     = getProductCategories($value->user_id);
		}
		
		if(isset($input['order'])  && $input['order']=='ASC'){
			usort($result, function ($item1, $item2) {
			return $item1['name'] <=> $item2['name'];
		});
		} elseif($input['order']=='DESC') {
			usort($result, function ($item1, $item2) {
			return $item2['name'] <=> $item1['name'];
		});
		}
		
		$message = 'Shop List';
		}
		else{
			$message = 'No Shops Found';
		}
		return response()->json(['status'=>'1','msg'=>$message,'shoplist'=>$result], $this->successStatus);
	}

	public function shop_list(Request $request){
		$input = $request->all();
		$lat = $input['latitude'];
		$lng = $input['longitude'];
		
		$sql = "SELECT   UserAddresses.*,
			( 6371 * acos( cos( radians({$lat}) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( `latitude` ) ) ) ) AS distance
			FROM `UserAddresses` inner join users on `users`.id = `UserAddresses`.user_id where UserAddresses.status = 1 and users.status = 1 and users.role = 'Seller' and users.is_verified = 1 and users.is_deleted = 0 Group By UserAddresses.user_id
			HAVING distance <= 20000 ORDER BY distance ASC";
		$shops = DB::select($sql);

		$result = array();
		foreach($shops as $key=>$value){		
			$result[$key]['id']        = $value->user_id;		
			$result[$key]['name']      = userName($value->user_id);		
			$result[$key]['user_id']   = $value->user_id!='' ? $value->user_id : '';		
			$result[$key]['location']  = $value->location!='' ? $value->location : '';		
			$result[$key]['house_no']  = $value->house_no!='' ? $value->house_no : '';		
			$result[$key]['address']   = $value->address!='' ? $value->address : '';		
			$result[$key]['landmark']  = $value->landmark!='' ? $value->landmark : '';		
			$result[$key]['type']  	   = $value->type!='' ? $value->type : '';	
			$result[$key]['distance']  = $value->distance!='' ? $value->distance : '0';		
			$result[$key]['rating']    = getRating($value->user_id);	
			$result[$key]['total_rating']    = getTotalRating($value->user_id);	
			$result[$key]['min_order'] = $value->min_order!='' ? $value->min_order : '0';
			$result[$key]['avg_delivery_time'] = $value->avg_delivery_time!='' ? $value->avg_delivery_time : 'N/A';
			$result[$key]['image']     = userProfile($value->user_id);
			$result[$key]['product_categories']     = getProductCategories($value->user_id);
		}

		return response()->json(['status'=>'1','msg'=>'Shop List','shoplist'=>$result], $this->successStatus);
	}
	
	public function categories(Request $request){
		
		//$input = $request->all();

		$categories = BusinessCategories::where('user_id', 1)->where('parent_id', 0)->get();
		
		$result = array();
		foreach($categories as $key=>$value){
			
			if($value->category->status=='1'){
				$catList = [];
				$catList['id'] = $value->category->id;
				$catList['name'] = $value->category->name;
				$catList['image'] = URL::to('/').'/images/categories/'.$value->category->image;

				$subcategories = BusinessCategories::where('user_id', 1)->where('parent_id', $value->id)->get();
				
				$sub_category = [];
				foreach($subcategories as $subKey=>$subcategory){
					if($subcategory->category->status=='1'){
						
						$sub_category[] = [
							'id' => $subcategory->category->id,
							'name' => $subcategory->category->name,
							'image' => URL::to('/').'/images/categories/'.$subcategory->category->image,
						];
					}	
				}
				$catList['sub_category'] = $sub_category;

				$result[] = $catList;
			}
		}

		//$result = array_chunk($result,2);
		
		return response()->json(['status'=>'1','msg'=>'categories','categories'=>$result], $this->successStatus);
	}

	public function categoriesSearch(Request $request){
		
		$input = $request->all();		
		$categories = Categories::where('status','=','1')->where('is_deleted','0')->where('name', 'like', '%' . $input['name'] . '%')->where('parent_id', '!=', 0 )->orderBy('name','asc')->get();
		
		$id = [];
		$parent_id = [];
		foreach($categories as $key=>$value){
			$parent_id[] = $value->parent_id;
			$id[] = $value->id;
		}
		$categorieslist = Categories::where('status','=','1')->where('is_deleted','0')->whereIn('id',$parent_id)->where('parent_id', '=', 0 )->orderBy('name','asc')->get();
		$result = array();
		/*print_r($parent_id);
		print_r($id);*/
		foreach($categorieslist as $key=>$value){
		
			$result[$key]['id'] = $value->id;
			$result[$key]['name'] = $value->name;
			$result[$key]['image'] = URL::to('/').'/public/images/'.$value->image;
			$result[$key]['description'] = $value->description!='' ? $value->description : '';
			
			$result[$key]['sub_category'] = array();
			$count = 0;
			foreach($value->children as $subKey=>$subcategory){
				if(in_array($subcategory->id, $id)){
					if($subcategory->status=='1'){

						$result[$key]['sub_category'][$count]['id'] = $subcategory->id;
						$result[$key]['sub_category'][$count]['name'] = $subcategory->name;
						$result[$key]['sub_category'][$count]['image'] = URL::to('/').'/public/images/'.$subcategory->image;
						$result[$key]['sub_category'][$count]['description'] = $subcategory->description!='' ? $value->description : '';
						$count++;
					}
				}
								
			}
		}
		//$result = array_chunk($result,2);
		
		return response()->json(['status'=>'1','msg'=>'categories','categories'=>$result], $this->successStatus);
	}

	public function subscriptionplans(){
		
		$subscriptions = Subscriptions::where('status','=','1')->orderBy('id','asc')->get();		
		$result = array();
		foreach($subscriptions as $key=>$value){
		
			$result[$key]['id'] = $value->id;
			$result[$key]['name'] = $value->name;
			$result[$key]['feature'] = $value->feature!='' ? $value->feature : '';
			$result[$key]['price'] = $value->price;
		}
		return response()->json(['status'=>'1','msg'=>'categories','categories'=>$result], $this->successStatus);
	}

	public function allCategories(){
		
		$categories = Categories::where('status','=','1')->where('is_deleted','0')->where('parent_id', '=', 0 )->orderBy('name','asc')->get();		
		$result = array();
		foreach($categories as $key=>$value){
		
			$result[$key]['id'] = $value->id;
			$result[$key]['name'] = $value->name!='' ? $value->name : '';
			$result[$key]['image'] = $value->image;
			$result[$key]['description'] = $value->description!='' ? $value->description : '';
		}
		return response()->json(['status'=>'1','msg'=>'categories','categories'=>$result], $this->successStatus);
	}

	public function subCategories(Request $request){
		$input = $request->all();
		$categories = Categories::where('status','=','1')->where('is_deleted','0')->where('parent_id', '=',$input['cat_id'] )->orderBy('name','asc')->get();		
		$result = array();
		foreach($categories as $key=>$value){
		
			$result[$key]['id'] = $value->id;
			$result[$key]['name'] = $value->name;
			
			if($value->image!=''){
		        $image =  url('/').'/images/'.$value->image;
		    } else { 
		        $image =  url('/').'/images/profile/no-image.png';
		    } 
		    $result[$key]['image'] = $image;
			$result[$key]['description'] = $value->description;
		}
		return response()->json(['status'=>'1','msg'=>'subcategories','subcategories'=>$result], $this->successStatus);
	}
	
	public function home_categories(Request $request){
		$input = $request->all();
		//$result = array_chunk($result,2);
		return response()->json(['status'=>'1','msg'=>'Home Category'], $this->successStatus);
	}
	
	public function add_address(Request $request){

		$input = $request->all();

		$id = (!empty($input['id'])) ? $input['id'] : 0;

		$numaddress = UserAddress::where('id',$id)->where('user_id',$input['user_id'])->first();

		if(!empty($numaddress)){ 

			$numaddress = UserAddress::findOrFail($numaddress->id);
			$numaddress->fill($input)->save();
			$msg = 'Address updated successfully';
			
		} else { 
			$numaddress = UserAddress::create($input);
			$msg = 'Address added successfully';
		}

		return response()->json(['status'=>'1','msg'=>$msg,'addresses'=>$numaddress], $this->successStatus);
	}

	public function delete_address(Request $request){
		$input = $request->all();
		
		
		$address = UserAddress::where('status','=','1')
					->where('user_id', '=', $input['user_id'])
					->where('id', '=', $input['id'])
					->delete();

	    return response()->json(['status'=>'1','msg'=>'Address Deleted Successfully','address'=>$input], $this->successStatus);
	}
    
	public function fetch_address(Request $request){
		$input = $request->all();
		
		
		$address = UserAddress::where('status','=','1')
					->where('user_id', '=', $input['user_id'])
					->where('status', '=', 1)
					//->where('type', '=', $input['type'])
					->orderBy('id','desc')->get();

	    $result = array();
		foreach($address as $key=>$value){
		
			$result[$key]['id'] = $value->id;
			$result[$key]['location'] = $value->location!='' ? $value->location : '';
			$result[$key]['shop_address'] = $value->shop_address!='' ? $value->shop_address : '';
			$result[$key]['house_no'] = $value->house_no!='' ? $value->house_no : '';
			$result[$key]['address'] = $value->address!='' ? $value->address : '';
			$result[$key]['country'] = $value->country!='' ? $value->country : '';
			$result[$key]['state'] = $value->state!='' ? $value->state : '';
			$result[$key]['city'] = $value->city!='' ? $value->city : '';
			$result[$key]['landmark'] = $value->landmark!='' ? $value->landmark : '';
			$result[$key]['type'] = $value->type!='' ? $value->type : '';
			$result[$key]['latitude'] = $value->latitude!='' ? $value->latitude : '';
			$result[$key]['longitude'] = $value->longitude!='' ? $value->longitude : '';
		}				
		
		return response()->json(['status'=>'1','msg'=>'success','addresses'=>$result], $this->successStatus);
	}

	public function searchAPI(Request $request){
		$input         = $request->all();
		$id            = array();	
		$productData   = Products::where('name', 'like', '%' . $input['search'] )->get(['user_id']);
		foreach ($productData as $key => $value) {
			$id[] = $value->user_id;
		}
		$user = User::whereIn('id',$id)->orWhere('name', 'like', '%' . $input['search'] . '%')->where('status', '1')->where('is_deleted',0)->where('role', 'Seller')->get();
		$result = array();
		foreach($user as $key=>$userData){	
		    $shops = UserAddress::where('user_id',$userData->id)->first();	
			$result[$key]['image']     = userProfile($userData->id);	
			$result[$key]['name']      = $userData->name;		
			$result[$key]['id']        = $userData->id!='' ? $userData->id : '';		
			$result[$key]['user_id']   = $userData->id!='' ? $userData->id : '';
			if(isset($shops->location)){
				$result[$key]['location']  = $shops->location!='' ? $shops->location : '';
			}else {
				$result[$key]['location']  = '';
			}

			if(isset($shops->house_no)){
				$result[$key]['house_no']  = $shops->house_no!='' ? $shops->house_no : '';
			}else {
				$result[$key]['house_no']  = '';
			}

			if(isset($shops->address)){
				$result[$key]['address']  = $shops->address!='' ? $shops->address : '';
			}else {
				$result[$key]['address']  = '';
			}	

			if(isset($shops->landmark)){
				$result[$key]['landmark']  = $shops->landmark!='' ? $shops->landmark : '';
			}else {
				$result[$key]['landmark']  = '';
			}

			if(isset($shops->type)){
				$result[$key]['type']  = $shops->type!='' ? $shops->type : '';
			}else {
				$result[$key]['type']  = '';
			}	
					
			$rating = getRating($userData->id);	
			$result[$key]['rating']    =  isset($rating) ?$rating :0;	
			
			if(isset($shops->min_order)){
				$result[$key]['min_order'] = $shops->min_order!='' ? $shops->min_order : '0';
			}else {
				$result[$key]['min_order']  = 0;
			}
		
			//$result[$key]['distance']  = $value->distance!='' ? $value->distance : '0';		
			$result[$key]['total_rating']    = getTotalRating($userData->user_id);	
			$result[$key]['avg_delivery_time'] = $shops->avg_delivery_time!='' ? $shops->avg_delivery_time : 'N/A';
			$result[$key]['product_categories']     = getProductCategories($userData->id);
	    }
		return response()->json(['status'=>'1','msg'=>'Shop List','shoplist'=>$result], $this->successStatus);	
	} 
	
	############# Product Seaarch ###############
	
	public function searchProduct(Request $request){
		$input = $request->all();
		$productsearch = '';
		if(isset($input['search'])) {
			
			$productsearch = $input['search'];
		}
		
		$products = Products::where('status','=','1')->where('user_id','=',$input['shop_id'])->where('category_id', $input['product_category_id'])->orderBy('name','asc')->where('name', 'like', '%' . $productsearch  . '%' )->get();
		//dd($products);		
		//$productData   = Products::where('name', 'like', '%' . $input['search'] )->get(['user_id']);
		$result = array();
		foreach($products as $key=>$value){

			

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
					$result[$key]['product_images'][$subKey]['image']  = $images;				
					$result[$key]['product_images'][$subKey]['medium'] = $imagemedium;				
					$result[$key]['product_images'][$subKey]['thumb']  = $imagethumb;				
				}
			} else {
				$result[$key]['product_images']  = array();
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
					
					$result[$key]['product_attribute'][$subKey]['image'] = array();
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
							$result[$key]['product_attribute'][$subKey]['image'][$subKey1]['image']  = $attrImgs;
							$result[$key]['product_attribute'][$subKey]['image'][$subKey1]['medium']  = $attrImgmedium;
							$result[$key]['product_attribute'][$subKey]['image'][$subKey1]['thumb']  = $attrImgthumb;
						}	
					} else {
						$result[$key]['product_attribute'][$subKey]['image'] = array();
					}			
				}
			}
		
		//     $productAttrDetails                 = productAttrDetails($value->id);
		//     $productAttrImages                  = productImagesData($value->id);
		    
		//     $productStocksQty                   = productStocksQty($value->id);
		//     $productAttrmetaData                = metaData($value->id);
		//     $productproductPrice                = productPrice($value->id);
		//     $result[$key]['id'] 				= $value->id;
		// 	$result[$key]['name'] 				= $value->name!='' ? $value->name : '';
		// 	$result[$key]['wishlist']			= wishlistRecode($value->id,$input['user_id']);
		// 	$result[$key]['sale_price'] 		= productSalePrice($value->id);
		// 	$result[$key]['base_price'] 		= productBasePrice($value->id);			
		// 	$result[$key]['category_id'] 		= $value->category_id!='' ? $value->category_id : '0';
		// 	$result[$key]['category_name'] 		= categoryName($value->category_id);
		// 	$result[$key]['sub_category_id'] 	= $value->sub_cat!='' ? $value->sub_cat : '0';
		// 	$result[$key]['sub_category_name'] 	= categoryName($value->sub_cat);
		// 	$result[$key]['seller_name'] 		= userName($value->user_id);
		// 	$result[$key]['product_status'] 	= $value->status;
		// 	if($productStocksQty){
		// 		$stock_status = $productStocksQty->stock_status;
		// 		$qty          = $productStocksQty->qty;
		// 	} else {
		// 		$stock_status = 1;
		// 		$qty          = 0;
		// 	}
		// 	$result[$key]['stock'] 				= $stock_status=='1' ? 'In Stock' : 'Out of Stock';
		// 	$result[$key]['stock_status'] 		= (int)$stock_status;
		// 	$result[$key]['qty'] 				= $qty;
		// 	if($productAttrmetaData){
		// 		$result[$key]['keywords'] 		= $productAttrmetaData->keywords!='' ? $productAttrmetaData->keywords : '';
		// 		$result[$key]['meta_description'] 	= $productAttrmetaData->meta_description!='' ? $productAttrmetaData->meta_description : '';
		// 	} else {
		// 		$result[$key]['keywords'] 		= '';
		// 		$result[$key]['meta_description'] 		= '';
		// 	}
			
		// 	$result[$key]['sku'] 				= $value->sku!='' ? $value->sku : '';
		// 	$result[$key]['attribute_set_id'] 	= $value->attribute_set_id!='' ? $value->attribute_set_id : '0';
		// 	$result[$key]['attribute_set_name'] = attrName($value->attribute_set_id);
		// 	$result[$key]['description'] 		= $value->description!='' ? $value->description : '';
			
		// 	$result[$key]['weight'] 			= $value->weight!='' ? $value->weight : '0';
		// 	$result[$key]['product_images']  = array();
		// 	$productImages = unserialize($productAttrImages);
		// 	if($productImages){
		// 		foreach($productImages as $subKey=>$image){
		// 			if($image!=''){
		// 				//$images = URL::to('/').'/public/images/'.$image;
						
		// 				$imagemedium = URL::to('/').'/public/images/medium/'.$image;
		// 				$imagethumb = URL::to('/').'/public/images/thumb/'.$image;
		// 				$images = URL::to('/').'/public/images/'.$image;
		// 			} else {
		// 				$images = '';
		// 				$imagemedium = '';
		// 				$imagethumb = '';
		// 			}
		// 			$result[$key]['product_images'][$subKey]['image']  = $images;				
		// 			$result[$key]['product_images'][$subKey]['medium'] = $imagemedium;				
		// 			$result[$key]['product_images'][$subKey]['thumb']  = $imagethumb;				
		// 		}
		// 	} else {
		// 		$result[$key]['product_images']  = array();
		// 	}
		// 	$result[$key]['product_attribute']  = array();
		// 	foreach($productAttrDetails as $subKey=>$attr){
		// 		$result[$key]['product_attribute'][$subKey]['stock'] 		= $attr->stock=='1' ? 'In Stock' : 'Out of Stock';
		// 		$result[$key]['product_attribute'][$subKey]['stock_status'] = (int)$attr->stock;
		// 		$result[$key]['product_attribute'][$subKey]['price'] 		= $attr->attr_price;
		// 		$attrattr_image = unserialize($attr->attr_image);
		// 		$attrattrName = unserialize($attr->attr_name);
		// 		$result[$key]['product_attribute'][$subKey]['attr_name'] = array();	
		// 		$i =0 ;		
		// 		foreach($attrattrName as $subKey1=>$value){				
		// 			$result[$key]['product_attribute'][$subKey]['attr_name'][$i]['name']  = $subKey1;
		// 			$result[$key]['product_attribute'][$subKey]['attr_name'][$i]['value'] = $value;
		// 		$i++; }
		// 		$result[$key]['product_attribute'][$subKey]['id']   		= $attr->id;
		// 		$result[$key]['product_attribute'][$subKey]['image'] = array();
		// 		if($attrattr_image){
		// 		foreach($attrattr_image as $subKey1=>$attrImg){
		// 			if($attrImg!=''){
		// 				$attrImgs = URL::to('/').'/public/images/attr/'.$attrImg;
		// 				$attrImgmedium = URL::to('/').'/public/images/attr/medium/'.$attrImg;
		// 				$attrImgthumb = URL::to('/').'/public/images/attr/thumb/'.$attrImg;
		// 			} else {
		// 				$attrImgs = '';
		// 				$attrImgmedium =  '';
		// 				$attrImgthumb = '';
		// 			}
		// 			$result[$key]['product_attribute'][$subKey]['image'][$subKey1]['image']  = $attrImgs;
		// 			$result[$key]['product_attribute'][$subKey]['image'][$subKey1]['medium']  = $attrImgmedium;
		// 			$result[$key]['product_attribute'][$subKey]['image'][$subKey1]['thumb']  = $attrImgthumb;
		// 		}	
		// 		} else {
		// 			$result[$key]['product_attribute'][$subKey]['image']  = array();
		// 		}			
		// 	}
		}
		
		return response()->json(['status'=>'1','msg'=>'products','products'=>$result], $this->successStatus);
	}
	
	public function pages(Request $request){
		
		$input = $request->all();

		$page_slug = $input['slug'];
		$page = Pages::where('page_key', $page_slug)->first();
		
		if(!empty($page)) {
			return response()->json([
				'status'=>'1',
				'msg'=>'Page description',
				'page_data'=>$page
			], $this->successStatus);
		} else {
			return response()->json([
				'status'=>'0',
				'msg'=>'Page not found',
			], $this->successStatus);
		}
	}	
	
	############################
}