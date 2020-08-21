<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;

use App\User; 
use App\Models\UserAddress; 
use App\Models\ProfilePictures; 
use App\Models\Settings; 
use App\Models\Categories;
use App\Models\Subscriptions;
use App\Models\Products;
use App\Models\Orderdelivery;
use App\Models\Orders; 
use App\Models\OrderReturn;
use App\Models\Payments; 
use App\Models\Reviews; 
use App\Models\Carts; 
use App\Models\Help; 
use App\Models\Wishlists;
use App\Models\Contactus;
use App\Models\Feedback;
use Validator;
use URL;
use DB;
use Mail;
use Config;

class UserController extends Controller
{

public $successStatus = 200;

/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){

        if(Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => request('role')])){ 
            $user = Auth::user();
			if($user->is_deleted=='1'){
				return response()->json(['status'=>'0','msg'=>'Your Account has been blocked.','error'=>'Unauthorised'],  $this->successStatus); 
			}
			if($user->is_verified=='0'){
				return response()->json(['status'=>'0','msg'=>'Your Account Not Verified.','error'=>'Unauthorised'],  $this->successStatus); 
			}
			if($user->status=='0'){
				return response()->json(['status'=>'0','msg'=>'Your Account Not Active.','error'=>'Unauthorised'],  $this->successStatus); 
			}
			$profile_data = ProfilePictures::where('user_id', '=', $user->id)->first();
			$user->profile_pic = "";
			if(isset($profile_data->picture)){
				$user->profile_pic = $profile_data->picture;
			}
			$user['cart_count']     = Carts::where('user_id',$user->id)->count();
			$user['wishlist_count'] = Wishlists::where('user_id',$user->id)->count();
            $user['profile_pic'] = userProfile($user->id);
			$token = $user->createToken('MyApp')->accessToken;
			
			return response()->json([
				'status'=>'1',
				'msg'=>'You are successfully logged in.',
				'user'=>$user,
				'token'=>$token,
				//'success' => $success
			], $this->successStatus);
        } 
        else{ 
            return response()->json(['status'=>'0','msg'=>'Invalid username or password','error'=>'Unauthorised'],  $this->successStatus); 
        } 
    }

    public function changePassword(Request $request){ 
    	$input = $request->all();
    	$userCount = User::where('id',$input['id'])->count();
    	$user = User::where('id',$input['id'])->first();
        if($userCount>0){ 
        	if(Auth::attempt(['id' => request('id'), 'password' => request('old_password')])){
        		$input['password'] = bcrypt($input['password']); 
        		unset($input['old_password']);
				User::where('id',$input['id'])->update($input); 
				$success['token'] =  $user->createToken('MyApp')-> accessToken;
				$status = 1;
            	$msg = 'User Password has been changed.';
        	} else {
        		$status =0;
        		$msg = 'Old Password is not valid.';
        	}
        	
            return response()->json(['user'=>$user,'status'=>$status,'msg'=>$msg,'success' => '1'], $this->successStatus);
        } 
        else{ 
            return response()->json(['status'=>'0','msg'=>'This user does not exists','error'=>'Unauthorised'],  $this->successStatus); 
        } 
    }

    public function fbLogin(Request $request){ 

        $input 		= $request->all();  
        $success 	= array();  	
    	$user  		= User::where('fb_id',$input['fb_id'])->first();
    	$ImgInput['picture']  = $input['picture_large'];
    	$ImgInput['status']   = 1;
    	unset($input['picture_large']);
    	if (empty($user)) {  
    	    if((isset($input['mobile']) && $input['mobile']!='') && (isset($input['mobile']) && $input['mobile']!='null')){ 
    	   	    $userData = User::where('mobile',$input['mobile'])->first();
    	   	    if($userData){
	    	   	    $userEmail= User::where('email',$input['email'])->where('id','!=',$userData->id)->count();
	    	   	    if ($userEmail>0) { 
	    	   	    	$status = 0;
			            $msg = "Email Address Already Exist";            
			        } else {
			        	if($input['role']=='Buyer'){
			        		$input['status'] = '1';
			        	}
						else if($input['role']==''){
							$input['status'] = '1';
						}
			        	User::where('mobile',$input['mobile'])->update($input);
			        	$ImgInput['user_id']  = $userData->id;
			        	$ImgInput['status']   = 1;
			        	if($ImgInput['picture']!=''){		        		
			        		
			        		$profileCount = ProfilePictures::where('user_id',$userData->id)->count();
			        		if($profileCount>0){ 
			        			ProfilePictures::where('user_id',$userData->id)->update($ImgInput);
			        		} else {
			        			ProfilePictures::create($ImgInput);
			        		}
			        		
			        	}
			        	
			        	$success['user_id'] =  $userData->id;
			        	$success['name'] =  $input['name'];
				        $success['email'] =  $input['email'];
				        $status = 1;
				        $msg = "Logged";
			        }
			    } else {  
			    	/* $status = 0;
	    	    	$msg = "Firstly Verify Mobile Number"; */
					$status = 1;
				    $msg = "Logged";
					User::create($input);
	    	    }     	
    	    } else { 
    	    	$status = 0; 
    	    	$msg = "Firstly Verify Mobile Number";
    	    }           
        } else { 
        	$userEmail= User::where('email',$input['email'])->where('id','!=',$user->id)->count();
        	$userMobile= User::where('mobile',$input['mobile'])->where('id','!=',$user->id)->count();
	   	    if ($userEmail>0) { 
	   	    	$status = 0;
	            $msg = "Email Address Already Exist";            
	        } else if ($userMobile>0) { 
	        	$status = 0;
	            $msg = "Mobile Number Already Used";            
	        } else {
	        	if($input['role']=='Buyer'){
	        		$input['status'] = '1';
	        	}
				else if($input['role']==''){
							$input['status'] = '1';
				}
	        	User::where('fb_id',$input['fb_id'])->update($input);
	        	$ImgInput['user_id']  = $user->id;
		        
	        	if($ImgInput['picture']!=''){		        		
	        		$profileCount = ProfilePictures::where('user_id',$user->id)->count();
	        		if($profileCount>0){
	        			ProfilePictures::where('user_id',$user->id)->update($ImgInput);
	        		} else {
	        			ProfilePictures::create($ImgInput);
	        		}
	        		
	        	} 
	        	$success['user_id'] =  $user->id;
	        	$success['name'] =  $user->name;
		        $success['email'] =  $user->email;
		        $status = 1;
		        $msg = "Logged";

	        }
        } 
        return response()->json(['status'=>$status,'msg'=>$msg,'userData'=>$success], $this->successStatus);
     
    }	
	
	public function sendOtp(Request $request){

		$input = $request->all();

		// $validator = Validator::make($request->all(), [ 
        //     'mobile' => 'required | unique:users'
		// ]);
		
		// return ['login' => 'User login'];
		if($input['role']=='Seller') {
			$input['is_verified'] = 0;
		} else {
			$input['is_verified'] = 1;
		}

		$profile_data = User::where('mobile',$input['mobile'])->where('role',$input['role'])->where('status',1)->where('is_deleted', '!=', '1')->first();
		// $profile_data = User::where('mobile',$input['mobile'])->where('is_deleted', '!=', '1')->first();
		
		// if ($profile_data==1) { 
        //   return response()->json(['msg'=>"The mobile no. has already been taken.", 'status'=>0]);
		// }

		// return $profile_data;die;
		
		// dump($profile_data);die;
		
		if(!$profile_data) {

			// $userInfo = [
			// 	'mobile' => $input['mobile'],
			// ];

			$profile_data = User::create($input);
		}

		$authkey = "248695AyFi3XmHtL05bf7bf4f";
		$otp = 123456; // rand(100000,999999);
		$msg = "Use ".$otp." as your login OTP. OTP is Confidential";
		$sender = "OTPSMS";
		$mobile = "+91".$profile_data['mobile'];
		
		//echo "http://control.msg91.com/api/sendotp.php?authkey=".$authkey."&message=Your verification code is ".$otp."&sender=".$sender."&mobile=".$mobile."&otp_expiry=max:10&otp=".$otp."";die;
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "http://control.msg91.com/api/sendotp.php?authkey=".$authkey."&message=Use ".$otp." as your login OTP. OTP is Confidential&sender=".$sender."&mobile=".$mobile."&otp=".$otp."",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$response_arr = json_decode($response);
		curl_close($curl);

		if ($err) {
			return response()->json(['status'=>'0','msg'=>$err], $this->successStatus); 
		} else {
			return response()->json(['status'=>'1','success'=>$response,'msg'=>"OTP send successfully"], $this->successStatus); 
		}
	}

	public function resendOtp(Request $request)
	{
		//echo request('retryType');die;
		$validator = Validator::make($request->all(), [ 
            'mobile' => 'required',
            'retryType' => 'required'
		]);
		
		if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
		
		$authkey = "248695AyFi3XmHtL05bf7bf4f";
		$mobile = "+91".request('mobile');
		$retrytype = request('retryType');
			
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://control.msg91.com/api/retryotp.php?authkey=".$authkey."&mobile=".$mobile."&retrytype=".$retrytype,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_HTTPHEADER => array(
			"content-type: application/x-www-form-urlencoded"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$response_arr = json_decode($response);
		curl_close($curl);

		if ($err) {
		  return response()->json(['status'=>'0','msg'=>$err], $this->successStatus); 
		} else {
		  if($response_arr->type == "error"){
				return response()->json(['status'=>'0','success'=>$response,'msg'=>str_replace("_"," ",$response_arr->message)], $this->successStatus); 
			}
			else{
				return response()->json(['status'=>'1','success'=>$response,'msg'=>str_replace("_"," ",$response_arr->message)], $this->successStatus); 
			}
		}	
	}
	
	public function verifyOtp(Request $request)
	{
		$input = $request->all();

		// $validator = Validator::make($request->all(), [ 
        //     'mobile' => 'required',
        //     'otp' => 'required',
        //     'role' => 'required'
		// ]);
		
		// if ($validator->fails()) { 
        //     return response()->json(['error'=>$validator->errors()], 401);            
		// }
		
		$authkey = "248695AyFi3XmHtL05bf7bf4f";
		$otp = request('otp');
		$mobile = "+91".request('mobile');
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://control.msg91.com/api/verifyRequestOTP.php?authkey=".$authkey."&mobile=".$mobile."&otp=".$otp,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_HTTPHEADER => array(
			"content-type: application/x-www-form-urlencoded"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$response_arr = json_decode($response);
		
		curl_close($curl);

		if ($err) {
			
		  return response()->json(['status'=>'0','msg'=>$err], $this->successStatus); 
		} else {

			if($response_arr->type == "error"){
				return response()->json(['status'=>'0','success'=>$response,'msg'=>str_replace("_"," ",$response_arr->message)], $this->successStatus); 
			} else{
				unset($input['otp']);
				$user = User::where('mobile',$input['mobile'])->where('role',$input['role'])->first();

				// send message to seller app
				if($user->role == "seller" && !empty($user->email)){
					if($user->is_deleted=='1'){
						return response()->json(['status'=>'0','msg'=>'Your Account has been blocked.','error'=>'Unauthorised'],  $this->successStatus); 
					}
					if($user->is_verified=='0'){
						return response()->json(['status'=>'0','msg'=>'Your Account Not Verified.','error'=>'Unauthorised'],  $this->successStatus); 
					}
					if($user->status=='0'){
						return response()->json(['status'=>'0','msg'=>'Your Account Not Active.','error'=>'Unauthorised'],  $this->successStatus); 
					}
				}

				// register new user
				if (empty($user['mobile'])) {
					$user = User::create($input);
				}

				//return response()->json(['status'=>'1','success'=>$response,'msg'=>str_replace("_"," ",$response_arr->message)], $this->successStatus); 


				$profile_data = ProfilePictures::where('user_id', '=', $user->id)->first();
				$user->profile_pic = "";
				if(isset($profile_data->picture)){
					$user->profile_pic = $profile_data->picture;
				}
				if($user->role == "seller" && empty($user->email)){
					$user->name = '';
					$user->email = '';
				}
				else if(empty($user->email)){
					$user->name = 'Guest';
					$user->email = 'guest@example.com';
				}
				$user['cart_count']     = Carts::where('user_id',$user->id)->count();
				$user['wishlist_count'] = Wishlists::where('user_id',$user->id)->count();
				$user['profile_pic'] = userProfile($user->id);
				$token = $user->createToken('MyApp')->accessToken;
				
				return response()->json([
					'status'=>'1',
					'msg'=>str_replace("_"," ",$response_arr->message),
					'user'=>$user,
					'token'=>$token,
					//'success' => $success
				], $this->successStatus); 
			}
		}
	}
	
    public function register(Request $request) 
    {
		$input = $request->all();
		
		$user = User::where('mobile',$input['mobile'])->where('status',1)->first();
		//print_r($user);
    	$useremail = User::where('email',$input['email'])->count();

       /*$validator = Validator::make($request->all(), [ 
            //'name' => 'required', 
            'email' => 'unique:users', 
            'mobile' => 'required', 
            'role' => 'required',
            //'password' => 'required',
        ]);

		if ($validator->fails()) { 
            return response()->json(['status'=>'0','msg'=>"Email Address Already Exist"], $this->successStatus);            
        }*/
		$input['password'] = bcrypt($input['password']);
        if ($useremail>0 && !empty($user['fb_id'])) {
			//$user = User::fill($user['id'])->save();
			$user = User::find($user['id'])->update(['password' => $input['password']]);
			//print_r($user);die('here');
		}
		elseif($useremail>0){
			return response()->json(['status'=>'0','msg'=>"Email Address Already Exist"], $this->successStatus); 
		}
		
        $input['name'] = ucfirst($input['name']);
		if($input['role']=='Buyer'){
			$input['status'] = '1';
		}
		elseif($input['role']=='Seller'){
			$input['is_verified'] = 0;
		}
		
		if (!empty($user) && empty($user->email)) {
			//$new_user = User::create($input);
			
			if(!$user->update($input)) {
				
				return response()->json(['status'=>'0','msg'=>"Error in user registration, Please try again."], $this->successStatus); 
			}
			//print_r($user->id);echo'<br><br>';
			//$new_user = User::find($user->id)->first();
			$new_user = User::where('id', $user->id)->first();
			//print_r($new_user);
			$uuid = $new_user->id;
			$userDetails = $input;
			$userDetails['user_id'] = $uuid;
			
			//print_r($userDetails);die;
			$userdata = UserAddress::create($userDetails);

			$success['token'] =  $new_user->createToken('MyApp')->accessToken; 
			$success['name'] =  $new_user->name;
			$success['email'] =  $input['email'];
			
			$nam = $success['name'];
			$email = $success['email'];
			$data = array('name'=>$nam,'email'=>$email);
			// Mail::send(['html'=>'mail'], $data, function ($m) use ($new_user) {
			// 	$m->from('hello@app.com', 'You have been Registered');
			// 	//$m->cc(['jvaleur@twelfthman.co']);
			// 	$m->to($new_user->email, $new_user->name)->subject('Your Reminder!');
			// });
			
            return response()->json(['status'=>'1','msg'=>'User Registered Successfully.','success'=>$success], $this->successStatus);            
        } else{
        	return response()->json(['status'=>'1','msg'=>'User Updated Successfully.'], $this->successStatus);
        }
	}
	
	public function contactUs(Request $request) 
    {
		$input = $request->all();
		die('here');

		$validator = Validator::make($request->all(), [ 
            'first_name' => 'required', 
            'last_name' => 'required', 
            'email' => 'required', 
            'message' => 'required', 
        ]);

		if ($validator->fails()) { 
            return response()->json(['status'=>'0','msg'=>"Something went wrong please try again or contact to system administrator."], $this->successStatus);            
        }
		
		$input['first_name'] = $input['first_name'];
		$input['last_name'] = $input['last_name'];
		$input['email'] = $input['email'];
		$input['message'] = $input['message'];
		
		if (Contactus::save($input)) {
			
			//$data = array('name'=>$nam,'email'=>$email);
			// Mail::send(['html'=>'mail'], $data, function ($m) use ($new_user) {
			// 	$m->from('hello@app.com', 'You have been Registered');
			// 	//$m->cc(['jvaleur@twelfthman.co']);
			// 	$m->to($new_user->email, $new_user->name)->subject('Your Reminder!');
			// });
			
            return response()->json(['status'=>'1','msg'=>'Your request subimmeted successfully.','success'=>$success], $this->successStatus);            
        } else{
        	return response()->json(['status'=>'1','msg'=>'Error in request submission.'], $this->successStatus);
        }
    }

    public function updateSubscriptionplans(Request $request) 
    { 
    	$input = $request->all();
    	$user = User::where('email',$input['email'])->first();

        if (empty($user)) { 
            return response()->json(['status'=>'0','msg'=>'User not found'], $this->successStatus);            
        }
        //$inputuser['status'] = '1';
        $inputuser['plan_id'] = $input['plan_id'];
        $inputuser['payment_id'] = $input['payment_id'];
		$user->fill($inputuser)->save(); 
        $success['name'] =  $user->name;
        $success['email'] =  $user->email;
        $setting = Settings::where('slug','Admin_Email')->first();
        
        /*$data = [
            'name' => $user->name,
            'email' => $user->email,
        ];
        $this->mailer->send('email.access-request', $data, function ($mail) use ($user) {
            $mail->to($setting->value);
            $mail->subject('User pay for Subscription plan');
            $mail->from(config('mail.from.address'), $event->staffAccessKey->user->company->name ?? '');
        });*/
		return response()->json(['status'=>'1','msg'=>'User Registered Successfully.','success'=>$success], $this->successStatus);
    }

    public function update_profile(Request $request) 
    {
		$input = $request->all();
		
		 $validator = Validator::make($request->all(), [
            'id' => 'required',
			'image' => 'required'
        ]);
        if ($validator->fails()) 
		{
            return response()->json(['error'=>$validator->errors()], 401);
        }
		
		$profile_data = ProfilePictures::where('user_id', '=', $input['id'])->first();
		$url = uniqid().'.png';
		$uploadPath = public_path() . "/images/profile/" .$url;
        $input['image'] = str_replace("data:image/jpeg;base64,","",$input['image']);
        $input['image'] = str_replace(" ","+",$input['image']);
		$data = base64_decode($input['image']);
		file_put_contents($uploadPath , $data);


		if(!isset($profile_data->id) && empty($profile_data->id)){
			$profile_pic_query = ProfilePictures::create(
				['picture' => $url, 'user_id'=>$input['id'], 'status' => '1', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]
			);
			$last_inserted_id = $profile_pic_query->id;
			
			$user = User::where('id', $input['id'])
            ->update(['profile_pic' => $last_inserted_id]);	
		}
		else{
			$profile_pic_query = ProfilePictures::where('id', $profile_data->id)
            ->update(['picture' => $url, 'updated_at'=> date('Y-m-d h:i:s')]);	
			
			$user = User::where('id', $input['id'])
            ->update(['profile_pic' => $profile_data->id]);	
		}
		$urlImage = URL::to('/').'/public/images/profile/'.$url;
        return response()->json(['status'=>'1','msg'=>'User Profile Picture Updated Successfully.','url'=>$urlImage], $this->successStatus);
    }

	public function update_profile_picture(Request $request){
		$input = $request->all();
		$validator = Validator::make($request->all(), [
            'id' => 'required',
			'image' => 'required'
        ]);
		if ($validator->fails()) 
		{
            return response()->json(['error'=>$validator->errors()], 200);
        }
		
		$profile_data = ProfilePictures::where('user_id', '=', $input['id'])->first();
		
		if($request->hasFile('image')) {
				$image_name = '';
				$uploadpath = public_path().'\images\profile';
				$original_name = $request->file('image')->getClientOriginalName();
				if (!empty($request->file('image'))) {
					$image_prefix = 'profile_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
					$ext = $request->file('image')->getClientOriginalExtension();
					$image_name = $image_prefix . '.' . $ext;
					$request->file('image')->move($uploadpath, $image_name);
				}
		}

		$input['image'] = URL::to('/').'/public/images/profile/'.$image_name;

		
		if(!isset($profile_data->id) && empty($profile_data->id)){
			$profile_pic_query = ProfilePictures::create(
				['picture' => $input['image'], 'user_id'=>$input['id'], 'status' => '1', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]
			);
			$last_inserted_id = $profile_pic_query->id;
			
			$user = User::where('id', $input['id'])
            ->update(['profile_pic' => $last_inserted_id]);	
		}
		else{
			$profile_pic_query = ProfilePictures::where('id', $profile_data->id)
            ->update(['picture' => $input['image'], 'updated_at'=> date('Y-m-d h:i:s')]);	
			
			$user = User::where('id', $input['id'])
            ->update(['profile_pic' => $profile_data->id]);	
		}
		return response()->json(['status'=>'1','img_url'=>$input['image'], 'msg'=>'User Profile Picture Updated Successfully.'], $this->successStatus);
	}

	public function userDetails(Request $request){
		$input = $request->all();
		$validator = Validator::make($request->all(), [ 
            'user_id' => 'required'
        ]);
		if ($validator->fails()) { 
            return response()->json(['success'=>'0','msg'=>'User Id Required'], 200);            
        }

        $user = User::where('id',$input['user_id'])->first();
        $UserOtherDetails = UserAddress::where('user_id',$input['user_id'])->first();
        if ($user=='') { 
            return response()->json(['success'=>'0','msg'=>'This user not Exist'], 200);            
        }
		$userDetails['name'] = $user->name!='' ? $user->name : '';
		$userDetails['role'] = $user->role!='' ? $user->role : '';
		$userDetails['email'] = $user->email!='' ? $user->email : '';
		$userDetails['mobile'] = $user->mobile!='' ? $user->mobile : '';
		$category = explode(',', $user->category);
		$category = Categories::where('status','=','1')->whereIn('id', $category)->orderBy('name','asc')->get();
		$categories = array();
		foreach($category as $key=>$value){
		
			$categories[$key]['cat_id'] = $value->id;
			$categories[$key]['cat_name'] = $value->name;
			$categories[$key]['cat_image'] = $value->image;
			$categories[$key]['cat_description'] = $value->description;
			
			$categories[$key]['sub_category'] = array();
			foreach($value->children as $subKey=>$subcategory){
				$categories[$key]['sub_category'][$subKey]['id'] = $subcategory->id;
				$categories[$key]['sub_category'][$subKey]['name'] = $subcategory->name;
				$categories[$key]['sub_category'][$subKey]['image'] = $subcategory->image;
				$categories[$key]['sub_category'][$subKey]['description'] = $subcategory->description!='' ? $subcategory->description : '';
			}
		}
		if($user->plan_id!=''){
           $planDetails 				=  SubscriptionPlan($user->plan_id);
           $userDetails['plan_id'] 		=  $planDetails->id!='' ? $planDetails->id : '';
           $userDetails['plan_name'] 	=  $planDetails->name!='' ? $planDetails->name : '';
           $userDetails['feature'] 		=  $planDetails->feature!='' ? $planDetails->feature : '';
		} else {
 		   $userDetails['plan_id']      = $userDetails['plan_name'] = $userDetails['feature'] = '';
		}
		$userDetails['profile']         = userProfile($input['user_id']);
		$userDetails['city']         	= $UserOtherDetails->city!='' ? $UserOtherDetails->city : '';
		$userDetails['state']        	= $UserOtherDetails->state!='' ? $UserOtherDetails->state : '';
		$userDetails['country']      	= $UserOtherDetails->country!='' ? $UserOtherDetails->country : '';
		$userDetails['shop_address'] 	= $UserOtherDetails->shop_address!='' ? $UserOtherDetails->shop_address : '';
		$userDetails['min_order_price'] = $UserOtherDetails->min_order!='' ? (int)$UserOtherDetails->min_order : '';
		$userDetails['categories']   = 	$categories;	
		return response()->json(['status'=>'1','msg'=>'success','userDetails'=>$userDetails], $this->successStatus);		
	}

    public function updateUserDetails(Request $request){
    	$input = $request->all();
    	$userBasicData = $userBasicDataOther = array();
    	$email = User::where('id', '!=' ,$input['user_id'])->where('email',$input['email'])->count();
    	//$mobile = User::where('id', '!=' ,$input['user_id'])->where('mobile',$input['mobile'])->count();
    	$userupdate = User::where('id',$input['user_id'])->first();
    	$userupdateother = UserAddress::where('user_id',$input['user_id'])->first();
        $validator = Validator::make($request->all(), [ 
			'user_id' => 'required', 
            'name' => 'required', 
            //'email' => 'unique:users', 
           // 'mobile' => 'required', 
        ]);

        if (empty($userupdate)) { 
            return response()->json(['status'=>'0','msg'=>'User not found'], $this->successStatus);            
        }
        // if ($mobile) { 
            // return response()->json(['status'=>'0','msg'=>"Mobile Number Already Exist"], $this->successStatus);
        // }
        if ($email) { 
            return response()->json(['status'=>'0','msg'=>"Email Address Already Exist"], $this->successStatus);
        }
        
		$userBasicData['name'] 			= $input['name'];
		if(!empty($input['email'])){
			$userBasicData['email'] 			= $input['email'];
		}
		if(!empty($input['mobile'])){
			$userBasicData['mobile'] 			= $input['mobile'];
		}
        if(isset($input['category'])){
        	$userBasicData['category'] 			= $input['category']; 
        }
               

		$userupdate->fill($userBasicData)->save();
		if(isset($input['shop_address'])){
        	$userBasicDataOther['shop_address'] = $input['shop_address']; 
        }
        if(isset($input['city'])){
        	$userBasicDataOther['city']         = $input['city']; 
        }
        if(isset($input['state'])){
        	$userBasicDataOther['state']        = $input['state'];
        }
        if(isset($input['country'])){
        	$userBasicDataOther['country']      = $input['country'];
        }
        if(isset($input['min_order'])){
        	$userBasicDataOther['min_order']      = $input['min_order'];
        }        
        
        
		$userupdateother->fill($userBasicDataOther)->save();


		$user = User::where('id',$input['user_id'])->first();
        $UserOtherDetails = UserAddress::where('user_id',$input['user_id'])->first();
        if ($user=='') { 
            return response()->json(['success'=>'0','msg'=>'This user not Exist'], 200);            
        }
		$userDetails['id'] = $user->id;
		$userDetails['name'] = $user->name!='' ? $user->name : '';
		//s$userDetails['role'] = $user->role!='' ? $user->role : '';
		$userDetails['email'] = $user->email!='' ? $user->email : '';
		$userDetails['mobile'] = $user->mobile!='' ? $user->mobile : '';
		$category = explode(',', $user->category);
		$category = Categories::where('status','=','1')->whereIn('id', $category)->orderBy('name','asc')->get();
		$categories = array();
		foreach($category as $key=>$value){
		
			$categories[$key]['cat_id'] = $value->id;
			$categories[$key]['cat_name'] = $value->name;
			$categories[$key]['cat_image'] = $value->image;
			$categories[$key]['cat_description'] = $value->description;
			
			$categories[$key]['sub_category'] = array();
			if(!empty($value->children)) {
				foreach($value->children as $subKey=>$subcategory){
					$categories[$key]['sub_category'][$subKey]['id'] = $subcategory->id;
					$categories[$key]['sub_category'][$subKey]['name'] = $subcategory->name;
					$categories[$key]['sub_category'][$subKey]['image'] = $subcategory->image;
					$categories[$key]['sub_category'][$subKey]['description'] = $subcategory->description;
				}
			}
		}
		if($user->plan_id!=''){
           $planDetails 				=  SubscriptionPlan($user->plan_id);
           $userDetails['plan_id'] 		=  $planDetails->id!='' ? $planDetails->id : '';
           $userDetails['plan_name'] 	=  $planDetails->name!='' ? $planDetails->name : '';
           $userDetails['feature'] 		=  $planDetails->feature!='' ? $planDetails->feature : '';
		} else {
 		   $userDetails['plan_id']      = $userDetails['plan_name'] = $userDetails['feature'] = '';
		}
		$userDetails['profile']         = userProfile($input['user_id']);
		$userDetails['city']         = $UserOtherDetails->city!='' ? $UserOtherDetails->city : '';
		$userDetails['state']        = $UserOtherDetails->state!='' ? $UserOtherDetails->state : '';
		$userDetails['country']      = $UserOtherDetails->country!='' ? $UserOtherDetails->country : '';
		$userDetails['shop_address'] = $UserOtherDetails->shop_address!='' ? $UserOtherDetails->shop_address : '';
		$userDetails['categories']   = 	$categories;

		return response()->json(['status'=>'1','msg'=>'User profile updated successfully.','userDetails'=>$userDetails], $this->successStatus);

    }

    public function sendMessage() {
	    $content      = array(
	        "en" => 'English Message'
	    );
	    $hashes_array = array();
	    array_push($hashes_array, array(
	        "id" => "like-button",
	        "text" => "Like",
	        "icon" => "http://i.imgur.com/N8SN8ZS.png",
	        "url" => "https://yoursite.com"
	    ));
	    array_push($hashes_array, array(
	        "id" => "like-button-2",
	        "text" => "Like2",
	        "icon" => "http://i.imgur.com/N8SN8ZS.png",
	        "url" => "https://yoursite.com"
	    ));
	    $fields = array(
	        'app_id' => "e00b88d3-771e-4e0f-b4ae-229b6ef450cc",
	        'included_segments' => array(
	            'All'
	        ),
	        'data' => array(
	            "foo" => "bar"
	        ),
	        'contents' => $content,
	        'web_buttons' => $hashes_array
	    );
	    //header('Content-Type: application/json; charset=utf-8');
	    $fields = json_encode($fields);
	    print("\nJSON sent:\n");
	    print($fields);
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	        'Content-Type: application/json; charset=utf-8',
	        'Authorization: Basic OTI0NTVhZjMtNTgyNi00MzA5LWI4OWEtOTdlNzI0NzdhZTM2'
	    ));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, TRUE);
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    
	    $response = curl_exec($ch);
	    curl_close($ch);
	    
	    return $response;
	}
	
	public function userCashOrder(Request $request) {

		$input = $request->all();
		
		$input['order_status']		= 'Pending';	

		$order 						= Orders::create($input);				
		$last_inserted_id	 		= $order->id;

		$orderid['order_id'] 		= 'ORD'.$last_inserted_id.date('Y');
		$order_id 					= Orders::where('id',$last_inserted_id)->update($orderid);

		// set cart as emtpy 
		Carts::where('user_id',$input['user_id'])->delete();

		$paymentDetails['status'] 	= 1;
		$paymentDetails['order_id'] = $last_inserted_id;
		$paymentDetails['tnx_id'] 	= null;
		$paymentDetails['payment_mode'] = 'Cash';
		$order = Payments::create($paymentDetails);	
				
		$content_deriver = array("en" => "Order ID - ".$orderid['order_id'] );
		#set your message heading here
		$heading_deriver = array("en" => "A new order has been placed." );
		#set repponse text 
		$data_response_deriver=array("value" => "Please pickup accordingly.");
				
		$content_seller = array("en" => "Order ID - ".$orderid['order_id'] );
		#set your message heading here
		$heading_seller = array("en" => "A new order has been placed on your store." );
		#set repponse text 
		$data_response_seller=array("value" => "Please act accordingly.");
				
		$notification_ids_deriver = User::where('role','Delivery')->get();
		$notification_ids_seller = User::Where('id',$input['seller_id'])->first();
		$ids = "";
		
		foreach($notification_ids_deriver as $value){
			
			$one_signal_app_id = '0fb436c7-12b5-400b-a2a9-d11e4ec7dd12';
			$one_signal_token_id = 'ZDExZTljYjYtZDA5Mi00M2RkLThjOWYtODU3ZGNjNjBhZDJm';
			$player_ids = $value->app_id;			
			
			$data_response = $this->send_notification($one_signal_app_id,$one_signal_token_id,$heading_deriver,$content_deriver,$data_response_deriver,$player_ids);
		}
			
		$ids_driver = ltrim($ids, ',');
		
		// seller notification
		$one_signal_app_id = 'dbd6f0a0-49ab-4f94-b8ff-3e524149cccf';
		$one_signal_token_id = 'Zjk4MDEzYmUtODM0YS00MDUzLWIyODgtOTZmYWNkNWIzYjg0';
		$player_ids = $notification_ids_seller->app_id;
		
		$this->send_notification($one_signal_app_id,$one_signal_token_id,$heading_seller,$content_seller,$data_response_seller,$player_ids); 
		
		return response()->json(['status'=>'1','msg'=>'Order Successfully'], $this->successStatus);
	}
	
	public function userOrder(Request $request) {

		$input = $request->all();
				
		// $storeId =  unserialize($input['product_id']);
		// if(isset($storeId['1'])){
		//    $UserId = $storeId['1'];
		// } else {
		// 	$UserId ='0';
		// }

		// $sellerId = getSellerUserId($UserId);
		// if(isset($sellerId->user_id)){
		// 	$sellerIduser_id = $sellerId->user_id;
		// } else {
		// 	$sellerIduser_id = 0;
		// }
					
		// if(!isset($input['seller_id'])){
		// 	$input['seller_id']			= $sellerIduser_id;	
		// }

		$input['order_status']		= 'Pending';	

		$order 						= Orders::create($input);				
		$last_inserted_id	 		= $order->id;

		$orderid['order_id'] 		= 'ORD'.$last_inserted_id.date('Y');
		$order_id 					= Orders::where('id',$last_inserted_id)->update($orderid);

		// set cart as emtpy 
		Carts::where('user_id',$input['user_id'])->delete();

		$paymentDetails['status'] 	= $input['payment_status'];
		$paymentDetails['order_id'] = $last_inserted_id;
		$paymentDetails['tnx_id'] 	= $input['tnx_id'];
		$paymentDetails['payment_mode'] = 'Online';
		$order = Payments::create($paymentDetails);	
				
		$content_deriver = array("en" => "Order ID - ".$orderid['order_id'] );
		#set your message heading here
		$heading_deriver = array("en" => "A new order has been placed." );
		#set repponse text 
		$data_response_deriver=array("value" => "Please pickup accordingly.");
				
		$content_seller = array("en" => "Order ID - ".$orderid['order_id'] );
		#set your message heading here
		$heading_seller = array("en" => "A new order has been placed on your store." );
		#set repponse text 
		$data_response_seller=array("value" => "Please act accordingly.");
				
		$notification_ids_deriver = User::where('role','Delivery')->get();
		$notification_ids_seller = User::Where('id',$input['seller_id'])->first();
		$ids = "";
		
		foreach($notification_ids_deriver as $value){
			
			$one_signal_app_id = '0fb436c7-12b5-400b-a2a9-d11e4ec7dd12';
			$one_signal_token_id = 'ZDExZTljYjYtZDA5Mi00M2RkLThjOWYtODU3ZGNjNjBhZDJm';
			$player_ids = $value->app_id;			
			
			$data_response = $this->send_notification($one_signal_app_id,$one_signal_token_id,$heading_deriver,$content_deriver,$data_response_deriver,$player_ids);
		}
			
		$ids_driver = ltrim($ids, ',');
		
		// seller notification
		$one_signal_app_id = 'dbd6f0a0-49ab-4f94-b8ff-3e524149cccf';
		$one_signal_token_id = 'Zjk4MDEzYmUtODM0YS00MDUzLWIyODgtOTZmYWNkNWIzYjg0';
		$player_ids = $notification_ids_seller->app_id;
		
		$this->send_notification($one_signal_app_id,$one_signal_token_id,$heading_seller,$content_seller,$data_response_seller,$player_ids); 
		
		return response()->json(['status'=>'1','msg'=>'Order Successfully'], $this->successStatus);
	}

    public function userOrder1(Request $request){
		$input 						= $request->all();
		
		
		/*echo serialize(explode(',',$input['product_id']));
		print_r($input);die;*/
		/*e00b88d3-771e-4e0f-b4ae-229b6ef450cc*/
		$ch = curl_init();
		$code = $input['app_id'];
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/apps/e00b88d3-771e-4e0f-b4ae-229b6ef450cc");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Basic OTI0NTVhZjMtNTgyNi00MzA5LWI4OWEtOTdlNzI0NzdhZTM2'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		$response = json_decode($response, true);
		curl_close($ch);
		
		$message = "Success";
		//Checking error key esists or not in response 
		if(array_key_exists('errors',$response)){
			$message = $response['errors'][0];
		}


		$response = $this->sendMessage();
		$return["allresponses"] = $response;
		$return = json_encode($return);

		$data = json_decode($response, true);
		print_r($data);
		$id = $data['id'];
		print_r($id);

		print("\n\nJSON received:\n");
		print($return);
		print("\n");
		die;
		
		
		$input['order_status']		= 'Pending';		
		$order 						= Orders::create($input);				
		$last_inserted_id	 		= $order->id;		
		$orderid['order_id'] 		= 'ORD'.$last_inserted_id.date('Y');
		$order_id 					= Orders::where('id',$last_inserted_id)->update($orderid);
		Carts::where('user_id',$input['user_id'])->delete();
		$paymentDetails['status'] 	= $input['payment_status'];
		$paymentDetails['tnx_id'] 	= $input['tnx_id'];
		$paymentDetails['order_id'] = $last_inserted_id;
		$order = Payments::create($paymentDetails);	
		
		return response()->json(['status'=>'1','msg'=>'Order Successfully'], $this->successStatus);
		
	}

    public function OrderDetails(Request $request){
		$input = $request->all();
	    $value = Orders::where('id',$input['id'])->first();  
		
			$orderDetails['id']              = $value->id;
			$orderDetails['order_id']        = $value->order_id;
			$orderDetails['order_status']    = $value->order_status;
			$orderDetails['user_id']         = $value->user_id;
			$orderDetails['total_amount']    = $value->total_amount;
			$orderDetails['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$productId[]                     = array();			
			$userData 						 = userDetails($value->user_id);
			$orderDetails['user_details']    = array();
			$orderDetails['product_details'] = array();
			$proId    = $productId 			 = unserialize($value->product_id); 
			$productAttrData = [];
			if(!empty($value->product_attr_id)) {
				$product_attr = $value->product_attr_id;					
				$allAttrId = unserialize($product_attr);
				$productAttrData = array_filter($allAttrId);
			}
			$productData                     = ProductDetails(serialize(array_filter($productId)));
			$productQtyData                  = array_filter(unserialize($value->qty));
			$orderDetails['total_item']      = sizeof($productData);
			$amount                          = unserialize($value->amount);
			$qty                             = unserialize($value->qty);
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){

					$orderDetails['product_details'][$subKey]['id'] 	= $productvalue->id;
					if(in_array($subKey, $qty)){
						$orderDetails['product_details'][$subKey]['qty'] 	= $qty[$subKey];
					} else {
						$orderDetails['product_details'][$subKey]['qty'] 	= 0;
					}
					if(in_array($subKey, $amount)){
						$orderDetails['product_details'][$subKey]['amount']	= $amount[$subKey];
					} else {
						$orderDetails['product_details'][$subKey]['amount']	= 0;
					}
					
					
					$orderDetails['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails['product_details'][$subKey]['image'] 	= $image;

					if(count($productAttrData)>0){
						$attrName = getAttrName($productAttrData[$subKey]);
						if($attrName){			
							foreach($attrName as $subKey1=>$value){				
								$orderDetails['product_details'][$subKey]['attr_name']['name'] = $subKey1;
								$orderDetails['product_details'][$subKey]['attr_name']['value'] = $value;

							} 
					    }
					    $orderDetails['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
					} else {
						$orderDetails['product_details'][$subKey]['attr_name']['name'] = '';
						$orderDetails['product_details'][$subKey]['attr_name']['value'] = '';
						$orderDetails['product_details'][$subKey]['attr_id'] 	= '';
					}
					if(count($productAttrData)>0){					
					    $orderDetails['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
					} else {
						$orderDetails['product_details'][$subKey]['qty'] 		= 0;
					}
					$orderDetails['product_details'][$subKey]['attr_name'] = array();
					
				}
		    }  
			/*foreach($userData as $subKey=>$subvalue){
				$orderDetails['user_details']['id'] = $userData->id;
				$orderDetails['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
				$orderDetails['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
				$orderDetails['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
				$orderDetails['user_details']['image'] = userProfile($userData->id);
			}*/
			if($userData){
				foreach($userData as $subKey=>$subvalue){
					$orderDetails['user_details']['id'] = $userData->id;
					$orderDetails['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
					$orderDetails['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
					$orderDetails['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
					$orderDetails['user_details']['image']= userProfile($userData->id);
				}
			} else {
				    $orderDetails['user_details']['id'] = "";
					$orderDetails['user_details']['name'] = '';	
					$orderDetails['user_details']['email'] = '';	
					$orderDetails['user_details']['mobile'] = '';	
					$orderDetails['user_details']['image'] = '';
			}
			
		
			
		return response()->json(['status'=>'1','msg'=>'Order Details','orderDetails'=>$orderDetails], $this->successStatus);		
	}

	public function updateOrderStatusByDelivery(Request $request){
		$input 		= $request->all();
		$orderData  = Orders::where('id',$input['id'])->first();
		if($orderData->order_status==$input['order_status']){
			$msg        = 'Order accepted by Delivery Boy.';
			$status     = 0;
		} else {
			//echo "test";die;
			$inputs['order_status'] =$input['order_status'];
			$msg        = 'Order Accepted Successfully';
			$status     = 1;
			$orders 	= Orders::where('id',$input['id'])->update($inputs);
			$orderdelevery = Orderdelivery::where("order_id",$input['id'])->count();
			
			
			 $users = User::where('id',$orderData->user_id)->first();
			
			$one_signal_app_id = '22b6ce69-73a4-40a2-bd88-7d419dc13851';
			$one_signal_token_id = 'OTI0NTVhZjMtNTgyNi00MzA5LWI4OWEtOTdlNzI0NzdhZTM2';
			$player_ids = $users->app_id;
			
			$content_seller = array("en" => "Order ID - ".$orderData->order_id );
			#set your message heading here
			$heading_seller = array("en" => "Your order is on the way." );
			#set repponse text 
			$data_response_seller=array("value" => "Please act accordingly.");
			
			
			$this->send_notification($one_signal_app_id,$one_signal_token_id,$heading_seller,$content_seller,$data_response_seller,$player_ids); 
			
			
			if($orderdelevery==0){
				$data['order_id'] = $input['id'];
				$data['delivery_id'] = $input['user_id'];
				
				Orderdelivery::create($data);
			}else{
				Orderdelivery::where('order_id',$input['id'])->delete();
				$data['order_id'] = $input['id'];
				$data['delivery_id'] = $input['user_id'];
				Orderdelivery::create($data);
			}

		}
		
		$lat = $input['latitude'];
		$lng = $input['longitude'];
		$sql = "SELECT   *, ( 6371 * acos( cos( radians({$lat}) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( `latitude` ) ) ) ) AS distance
					FROM `UserAddresses` where status = 1 
					HAVING distance <= 40";

		$userAddress = DB::select($sql);
		$pid = $orderDetails = array();
        if($userAddress){
           foreach ($userAddress as $key => $value) {        	
	        	$pid[] = $value->id;
	        }
	    }
	    $condition = '';
	    
			$result = Orders::where('order_status','Pending')->whereIn('shipping_address',$pid)->get();
	    
	     // print_r($result);die;
        foreach($result as $key=>$value){
		
			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$productId[] = array();			
			$userData = userDetails($value->user_id);
			$orderDetails[$key]['user_details']    = array();
			$orderDetails[$key]['product_details'] = array();
			$proId    = unserialize($value->product_id);			
			foreach($proId as $Key=>$productbvalue){
				//if(in_array($productbvalue, $pid)){
				   $productId[]	= $productbvalue;				   
				//}
			} 
			$productAttrData = [];
			if(!empty($value->product_attr_id)) {
				$product_attr = $value->product_attr_id;					
				$allAttrId = unserialize($product_attr);
				$productAttrData = array_filter($allAttrId);
			}
			$productData = ProductDetails(serialize(array_filter($productId)));
			$productQtyData  = array_filter(unserialize($value->qty));
			$orderDetails[$key]['total_item']    = sizeof($productData);
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){
					$orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
					$orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;

					if(count($productAttrData)>0){
						if(in_array($subKey, $productAttrData)){
							$attrName = getAttrName($productAttrData[$subKey]);
							if($attrName){			
								foreach($attrName as $subKey1=>$value){				
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
								} 
						    }
						    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
						}  else {
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
						}   
					    
					} else {
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
					}


					if(in_array($subKey, $productQtyData)){
						if(isset($productQtyData[$subKey])){
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
						} else {
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
						}
						
					} else {
						$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
					}
					$orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
					
				}
		    }  
		    if($userData){
				foreach($userData as $subKey=>$subvalue){
					$orderDetails[$key]['user_details']['id'] = $userData->id;
					$orderDetails[$key]['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
					$orderDetails[$key]['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($userData->id);
				}
			} else {
				    $orderDetails[$key]['user_details']['id'] = "";
					$orderDetails[$key]['user_details']['name'] = '';	
					$orderDetails[$key]['user_details']['email'] = '';	
					$orderDetails[$key]['user_details']['mobile'] = '';	
					$orderDetails[$key]['user_details']['image'] = '';
			}
			
		}
			
		return response()->json(['status'=>$status,'msg'=>$msg,'orderDetails'=>$orderDetails], $this->successStatus);
	}

	public function DeliveryProcessingOrderStatus(Request $request){
		$input = $request->all();

		$orderData  = Orders::where('id',$input['id'])->first();
		if($orderData->order_status==$input['order_status']){
			$msg        = 'Order Staus already updated.';
			$status     = 0;
		} else {
			$inputs['order_status'] =$input['order_status'];
			$msg        = 'Order Delivered Successfully';
			$status     = 1;
			$orders 	= Orders::where('id',$input['id'])->update($inputs);
			//$orderdelevery = Orderdelivery::where("order_id",$input['id'])->count();
		}

		$orderDetails = array();
	   // $result = Orders::where('order_status','Accept')->where('user_id',$input['user_id'])->get();  
	    $result = Orders::where('order_status','Accept')->join('orderdeliveries', 'orderdeliveries.order_id', '=', 'orders.id')
            
            ->select('orderdeliveries.*', 'orders.*')
            ->where('orderdeliveries.delivery_id',$input['user_id'])
            ->get();
            //print_r($result);die;
        foreach($result as $key=>$value){
        	
			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$productId[] = array();			
			$userData = userDetails($value->user_id);
			$pid = unserialize($value->product_id);
			$sellerData = userDetails($value->seller_id);

			$orderDetails[$key]['user_details']    = array();
			$orderDetails[$key]['seller_details']    = array();
			$orderDetails[$key]['product_details'] = array();
			$proId    = unserialize($value->product_id);			
			foreach($proId as $Key=>$productbvalue){
				if(in_array($productbvalue, $pid)){
				   $productId[]	= $productbvalue;				   
				}
			} 
			$productAttrData = [];
			if(!empty($value->product_attr_id)) {
				$product_attr = $value->product_attr_id;					
				$allAttrId = unserialize($product_attr);
				$productAttrData = array_filter($allAttrId);
			}
			$productData = ProductDetails(serialize(array_filter($productId)));
			$productQtyData  = array_filter(unserialize($value->qty));
			$orderDetails[$key]['total_item']    = sizeof($productData);
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){
					$orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
					$orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;

					if(count($productAttrData)>0){
						if(in_array($subKey, $productAttrData)){
							$attrName = getAttrName($productAttrData[$subKey]);
							if($attrName){			
								foreach($attrName as $subKey1=>$value){				
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
								} 
						    }
						    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
						}  else {
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
						}   
					    
					} else {
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
					}


					if(in_array($subKey, $productQtyData)){
						if(isset($productQtyData[$subKey])){
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
						} else {
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
						}
						
					} else {
						$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
					}
					$orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
					
				}
		    }
		    

		    if($userData){ 
		    		$orderDetails[$key]['user_details']['id'] = $userData->id;
					$orderDetails[$key]['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
					$orderDetails[$key]['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($userData->id);
				/*foreach($userData as $subKey=>$subvalue){
					
					$orderDetails[$key]['user_details']['id'] = $subvalue->id;
					$orderDetails[$key]['user_details']['name'] = $subvalue->name!='' ? $subvalue->name : '';	
					$orderDetails[$key]['user_details']['email'] = $subvalue->email!='' ? $subvalue->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $subvalue->mobile!='' ? $subvalue->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($subvalue->id);
				}*/
			} else {
				    $orderDetails[$key]['user_details']['id'] = "";
					$orderDetails[$key]['user_details']['name'] = '';	
					$orderDetails[$key]['user_details']['email'] = '';	
					$orderDetails[$key]['user_details']['mobile'] = '';	
					$orderDetails[$key]['user_details']['image'] = '';
			}
			if($sellerData){
					$orderDetails[$key]['seller_details']['id'] = $sellerData->id;
					$orderDetails[$key]['seller_details']['name'] = $sellerData->name!='' ? $sellerData->name : '';	
					$orderDetails[$key]['seller_details']['email'] = $sellerData->email!='' ? $sellerData->email : '';	
					$orderDetails[$key]['seller_details']['mobile'] = $sellerData->mobile!='' ? $sellerData->mobile : '';	
					$orderDetails[$key]['seller_details']['image'] = userProfile($sellerData->id);
				/*foreach($sellerData as $subKey1=>$subvalue1){
					$orderDetails[$key]['seller_details']['id'] = $subvalue1->id;
					$orderDetails[$key]['seller_details']['name'] = $subvalue1->name!='' ? $subvalue1->name : '';	
					$orderDetails[$key]['seller_details']['email'] = $subvalue1->email!='' ? $subvalue1->email : '';	
					$orderDetails[$key]['seller_details']['mobile'] = $subvalue1->mobile!='' ? $subvalue1->mobile : '';	
					$orderDetails[$key]['seller_details']['image'] = userProfile($subvalue1->id);
				}*/
			} else {
				    $orderDetails[$key]['seller_details']['id'] = "";
					$orderDetails[$key]['seller_details']['name'] = '';	
					$orderDetails[$key]['seller_details']['email'] = '';	
					$orderDetails[$key]['seller_details']['mobile'] = '';	
					$orderDetails[$key]['seller_details']['image'] = '';
			}
			
		}
			
		return response()->json(['status'=>'1','msg'=>'New Order List','orderDetails'=>$orderDetails], $this->successStatus);		
	}

	public function newDeliveryOrderDetails(Request $request){
		$input = $request->all();
		$lat = $input['latitude'];
		$lng = $input['longitude'];
		$allAttrId = array();
		if(isset($input['user_id']) && !empty($input['user_id'])){
			$users = User::findOrFail($input['user_id']);
			$data['app_id'] = $input['app_id'];
			$users->fill($data)->save();
		}
		
		$sql = "SELECT   *,
    ( 6371 * acos( cos( radians({$lat}) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( `latitude` ) ) ) ) AS distance
FROM `UserAddresses` where status = 1 
HAVING distance <= 40";

		$userAddress = DB::select($sql);
		$pid = $orderDetails = array();
        if($userAddress){
           foreach ($userAddress as $key => $value) {        	
	        	$pid[] = $value->id;
	        }
	    } 
	    $condition = '';
		
		$ids = join("','",$pid);   
		
		$sql2 = "SELECT * FROM orders WHERE id IN ('$ids') and order_status ='Pending'";
		$result = DB::select($sql2);
		//print_r($result);die;
	    //$result = Orders::where('shipping_address',$pid)->get();  
		
        foreach($result as $key=>$value){

			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['qty']   		   = unserialize($value->qty);
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$productId[] = array();			
			$userData = userDetails($value->user_id);
			
			$sellerData = userDetails($value->seller_id);

			$orderDetails[$key]['user_details']    = array();
			$orderDetails[$key]['seller_details']    = array();
			$orderDetails[$key]['product_details'] = array();
			
			$proId    = unserialize($value->product_id);
				//print_r($pid);die;		
			foreach($proId as $Key=>$productbvalue){
				if(in_array($productbvalue, $pid)){
				   $productId[]	= $productbvalue;				   
				}
			} 
			$product_attr = $value->product_attr_id;					
			$allAttrId = unserialize($product_attr);
			//echo "<pre>";print_r($allAttrId);die;
			//$productAttrData = array_filter($allAttrId);
			$productAttrData = $allAttrId;
			//print_r($productId);die;
			$productData = ProductDetails(serialize(array_filter($productId)));
			//print_r($productData);die;
			$productQtyData  = array_filter(unserialize($value->qty));
			$orderDetails[$key]['total_item']  = sizeof($productData);
			
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){

					$orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
					$orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;

					if(count($productAttrData)>0){
						if(in_array($subKey, $productAttrData)){
							$attrName = getAttrName($productAttrData[$subKey]);
							if($attrName){			
								foreach($attrName as $subKey1=>$value){				
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
								} 
						    }
						    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
						} else {
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
						}   
					    
					} else {
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
					}

					/*if(in_array($subKey, $productQtyData)){
						$orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
					} else {
						$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
					}*/
					$orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
					
				}
		    }
		    

		    if($userData){ 
		    		$orderDetails[$key]['user_details']['id'] = $userData->id;
					$orderDetails[$key]['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
					$orderDetails[$key]['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($userData->id);
				/*foreach($userData as $subKey=>$subvalue){
					
					$orderDetails[$key]['user_details']['id'] = $subvalue->id;
					$orderDetails[$key]['user_details']['name'] = $subvalue->name!='' ? $subvalue->name : '';	
					$orderDetails[$key]['user_details']['email'] = $subvalue->email!='' ? $subvalue->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $subvalue->mobile!='' ? $subvalue->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($subvalue->id);
				}*/
			} else {
				    $orderDetails[$key]['user_details']['id'] = "";
					$orderDetails[$key]['user_details']['name'] = '';	
					$orderDetails[$key]['user_details']['email'] = '';	
					$orderDetails[$key]['user_details']['mobile'] = '';	
					$orderDetails[$key]['user_details']['image'] = '';
			}
			if($sellerData){
					$orderDetails[$key]['seller_details']['id'] = $sellerData->id;
					$orderDetails[$key]['seller_details']['name'] = $sellerData->name!='' ? $sellerData->name : '';	
					$orderDetails[$key]['seller_details']['email'] = $sellerData->email!='' ? $sellerData->email : '';	
					$orderDetails[$key]['seller_details']['mobile'] = $sellerData->mobile!='' ? $sellerData->mobile : '';	
					$orderDetails[$key]['seller_details']['image'] = userProfile($sellerData->id);
				/*foreach($sellerData as $subKey1=>$subvalue1){
					$orderDetails[$key]['seller_details']['id'] = $subvalue1->id;
					$orderDetails[$key]['seller_details']['name'] = $subvalue1->name!='' ? $subvalue1->name : '';	
					$orderDetails[$key]['seller_details']['email'] = $subvalue1->email!='' ? $subvalue1->email : '';	
					$orderDetails[$key]['seller_details']['mobile'] = $subvalue1->mobile!='' ? $subvalue1->mobile : '';	
					$orderDetails[$key]['seller_details']['image'] = userProfile($subvalue1->id);
				}*/
			} else {
				    $orderDetails[$key]['seller_details']['id'] = "";
					$orderDetails[$key]['seller_details']['name'] = '';	
					$orderDetails[$key]['seller_details']['email'] = '';	
					$orderDetails[$key]['seller_details']['mobile'] = '';	
					$orderDetails[$key]['seller_details']['image'] = '';
			}
			
		}
			
		return response()->json(['status'=>'1','msg'=>'New Order List','orderDetails'=>$orderDetails], $this->successStatus);		
	}

	public function newDeliveryProcessingOrder(Request $request){
		$input = $request->all();
		$orderDetails = array();
	   // $result = Orders::where('order_status','Accept')->where('user_id',$input['user_id'])->get();  
	    $result = Orders::where('order_status','Accept')->join('orderdeliveries', 'orderdeliveries.order_id', '=', 'orders.id')
            
            ->select('orderdeliveries.*', 'orders.*')
            ->where('orderdeliveries.delivery_id',$input['user_id'])
            ->get();
            //echo "<pre>";print_r($result);die;
        foreach($result as $key=>$value){

			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$productId[] = array();			
			$userData = userDetails($value->user_id);
			$pid = unserialize($value->product_id);
			$sellerData = userDetails($value->seller_id);

			$orderDetails[$key]['user_details']    = array();
			$orderDetails[$key]['seller_details']    = array();
			$orderDetails[$key]['product_details'] = array();
			$proId    = unserialize($value->product_id);			
			foreach($proId as $Key=>$productbvalue){
				if(in_array($productbvalue, $pid)){
				   $productId[]	= $productbvalue;				   
				}
			} 
			
			$productAttrData = [];
			if(!empty($value->product_attr_id)) {
				$product_attr = $value->product_attr_id;					
				$allAttrId = unserialize($product_attr);
				$productAttrData = array_filter($allAttrId);
			}
			$productData = ProductDetails(serialize(array_filter($productId)));
			$productQtyData  = array_filter(unserialize($value->qty));
			$orderDetails[$key]['total_item']    = sizeof($productData);
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){
					$orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
					$orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;

					if(count($productAttrData)>0){
						if(in_array($subKey, $productAttrData)){
							$attrName = getAttrName($productAttrData[$subKey]);
							if($attrName){			
								foreach($attrName as $subKey1=>$value){				
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
								} 
						    }
						    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
						}  else {
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
						}   
					    
					} else {
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
					}


					if(in_array($subKey, $productQtyData)){
						if(isset($productQtyData[$subKey])){
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
						} else {
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
						}
						
					} else {
						$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
					}
					$orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
					
				}
		    }
		    

		    if($userData){ 
		    		$orderDetails[$key]['user_details']['id'] = $userData->id;
					$orderDetails[$key]['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
					$orderDetails[$key]['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($userData->id);
				/*foreach($userData as $subKey=>$subvalue){
					
					$orderDetails[$key]['user_details']['id'] = $subvalue->id;
					$orderDetails[$key]['user_details']['name'] = $subvalue->name!='' ? $subvalue->name : '';	
					$orderDetails[$key]['user_details']['email'] = $subvalue->email!='' ? $subvalue->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $subvalue->mobile!='' ? $subvalue->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($subvalue->id);
				}*/
			} else {
				    $orderDetails[$key]['user_details']['id'] = "";
					$orderDetails[$key]['user_details']['name'] = '';	
					$orderDetails[$key]['user_details']['email'] = '';	
					$orderDetails[$key]['user_details']['mobile'] = '';	
					$orderDetails[$key]['user_details']['image'] = '';
			}
			if($sellerData){
					$orderDetails[$key]['seller_details']['id'] = $sellerData->id;
					$orderDetails[$key]['seller_details']['name'] = $sellerData->name!='' ? $sellerData->name : '';	
					$orderDetails[$key]['seller_details']['email'] = $sellerData->email!='' ? $sellerData->email : '';	
					$orderDetails[$key]['seller_details']['mobile'] = $sellerData->mobile!='' ? $sellerData->mobile : '';	
					$orderDetails[$key]['seller_details']['image'] = userProfile($sellerData->id);
				/*foreach($sellerData as $subKey1=>$subvalue1){
					$orderDetails[$key]['seller_details']['id'] = $subvalue1->id;
					$orderDetails[$key]['seller_details']['name'] = $subvalue1->name!='' ? $subvalue1->name : '';	
					$orderDetails[$key]['seller_details']['email'] = $subvalue1->email!='' ? $subvalue1->email : '';	
					$orderDetails[$key]['seller_details']['mobile'] = $subvalue1->mobile!='' ? $subvalue1->mobile : '';	
					$orderDetails[$key]['seller_details']['image'] = userProfile($subvalue1->id);
				}*/
			} else {
				    $orderDetails[$key]['seller_details']['id'] = "";
					$orderDetails[$key]['seller_details']['name'] = '';	
					$orderDetails[$key]['seller_details']['email'] = '';	
					$orderDetails[$key]['seller_details']['mobile'] = '';	
					$orderDetails[$key]['seller_details']['image'] = '';
			}
			
		}
			
		return response()->json(['status'=>'1','msg'=>'New Order List','orderDetails'=>$orderDetails], $this->successStatus);		
	}

	public function DeliveryOrderHistory(Request $request){
		$input = $request->all();
		$orderDetails = array();
	    //$result = Orders::whereIn('order_status',array('Delievred','Confirmed','Canceled'))->where('user_id',$input['user_id'])->get();
		
	    $result = Orders::whereIn('order_status',array('Delivered','Confirmed','Canceled'))->join('orderdeliveries', 'orderdeliveries.order_id', '=', 'orders.id')
            
            ->select('orderdeliveries.*', 'orders.*')
            ->where('orderdeliveries.delivery_id',$input['user_id'])
            ->get();
	    if(!empty($result)){  
        foreach($result as $key=>$value){

			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$productId[] = array();			
			$userData = userDetails($value->user_id);
			
			$sellerData = userDetails($value->seller_id);
			$pid = unserialize($value->product_id);

			$orderDetails[$key]['user_details']    = array();
			$orderDetails[$key]['seller_details']    = array();
			$orderDetails[$key]['product_details'] = array();
			$proId    = unserialize($value->product_id);			
			foreach($proId as $Key=>$productbvalue){
				if(in_array($productbvalue, $pid)){
				   $productId[]	= $productbvalue;				   
				}
			} 
			$productAttrData = [];
			if(!empty($value->product_attr_id)) {
				$product_attr = $value->product_attr_id;					
				$allAttrId = unserialize($product_attr);
				$productAttrData = array_filter($allAttrId);
			}
			$productData = ProductDetails(serialize(array_filter($productId)));
			$productQtyData  = array_filter(unserialize($value->qty));
			$orderDetails[$key]['total_item']    = sizeof($productData);
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){
					$orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
					$orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;

					if(count($productAttrData)>0){
						if(in_array($subKey, $productAttrData)){
							$attrName = getAttrName($productAttrData[$subKey]);
							if($attrName){			
								foreach($attrName as $subKey1=>$value){				
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
								} 
						    }
						    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
						}  else {
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
						}   
					    
					} else {
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
					}


					if(in_array($subKey, $productQtyData)){
						if(isset($productQtyData[$subKey])){
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
						} else {
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
						}
						
					} else {
						$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
					}
					$orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
					
				}
		    }
		    

		    if($userData){ 
		    		$orderDetails[$key]['user_details']['id'] = $userData->id;
					$orderDetails[$key]['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
					$orderDetails[$key]['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($userData->id);
				/*foreach($userData as $subKey=>$subvalue){
					
					$orderDetails[$key]['user_details']['id'] = $subvalue->id;
					$orderDetails[$key]['user_details']['name'] = $subvalue->name!='' ? $subvalue->name : '';	
					$orderDetails[$key]['user_details']['email'] = $subvalue->email!='' ? $subvalue->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $subvalue->mobile!='' ? $subvalue->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($subvalue->id);
				}*/
			} else {
				    $orderDetails[$key]['user_details']['id'] = "";
					$orderDetails[$key]['user_details']['name'] = '';	
					$orderDetails[$key]['user_details']['email'] = '';	
					$orderDetails[$key]['user_details']['mobile'] = '';	
					$orderDetails[$key]['user_details']['image'] = '';
			}
			if($sellerData){
					$orderDetails[$key]['seller_details']['id'] = $sellerData->id;
					$orderDetails[$key]['seller_details']['name'] = $sellerData->name!='' ? $sellerData->name : '';	
					$orderDetails[$key]['seller_details']['email'] = $sellerData->email!='' ? $sellerData->email : '';	
					$orderDetails[$key]['seller_details']['mobile'] = $sellerData->mobile!='' ? $sellerData->mobile : '';	
					$orderDetails[$key]['seller_details']['image'] = userProfile($sellerData->id);
				/*foreach($sellerData as $subKey1=>$subvalue1){
					$orderDetails[$key]['seller_details']['id'] = $subvalue1->id;
					$orderDetails[$key]['seller_details']['name'] = $subvalue1->name!='' ? $subvalue1->name : '';	
					$orderDetails[$key]['seller_details']['email'] = $subvalue1->email!='' ? $subvalue1->email : '';	
					$orderDetails[$key]['seller_details']['mobile'] = $subvalue1->mobile!='' ? $subvalue1->mobile : '';	
					$orderDetails[$key]['seller_details']['image'] = userProfile($subvalue1->id);
				}*/
			} else {
				    $orderDetails[$key]['seller_details']['id'] = "";
					$orderDetails[$key]['seller_details']['name'] = '';	
					$orderDetails[$key]['seller_details']['email'] = '';	
					$orderDetails[$key]['seller_details']['mobile'] = '';	
					$orderDetails[$key]['seller_details']['image'] = '';
			}
			
		}
		return response()->json(['status'=>'1','msg'=>'New Order List','orderDetails'=>$orderDetails], $this->successStatus);
	}else{
		return response()->json(['status'=>'0','msg'=>'No Order List','orderDetails'=>''], $this->successStatus);
		
	}
			
				
	}



	public function newOrderDetails(Request $request){
		$input = $request->all();
        $products = Products::where('user_id',$input['user_id'])->get(['id']);
        $pid = $orderDetails = array();
        if($products){
           foreach ($products as $key => $value) {        	
	        	$pid[] = $value->id;
	        }
	    }
	    $condition = '';
		
	    $query = Orders::where('order_status','Pending');
	    $result = Orders::where('order_status','Pending')->where('seller_id',$input['user_id'])->orWhere(function($query) use($pid){
            foreach ($pid as $key=> $id){
                $query->orWhere('product_id','like','%"' . $id . '"%');
            }
        })->get();  
		//dd($result);
        foreach($result as $key=>$value){
		
			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$productId[] = array();			
			$userData = userDetails($value->user_id);
			$orderDetails[$key]['user_details']    = array();
			$orderDetails[$key]['product_details'] = array();
			$proId    = unserialize($value->product_id);			
			foreach($proId as $Key=>$productbvalue){
				if(in_array($productbvalue, $pid)){
				   $productId[]	= $productbvalue;				   
				}
			}

			$productAttrData = [];
			if(!empty($value->product_attr_id)) {
				$product_attr = $value->product_attr_id;					
				$allAttrId = unserialize($product_attr);
				$productAttrData = array_filter($allAttrId);
			}
			$productData = ProductDetails(serialize(array_filter($productId)));
			$productQtyData  = array_filter(unserialize($value->qty));
			$orderDetails[$key]['total_item']    = sizeof($productData);
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){
					$orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
					$orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;

					if(count($productAttrData)>0){
						if(in_array($subKey, $productAttrData)){
							$attrName = getAttrName($productAttrData[$subKey]);
							if($attrName){			
								foreach($attrName as $subKey1=>$value){				
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
								} 
						    }
						    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
						}  else {
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
						}   
					    
					} else {
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
					}


					if(in_array($subKey, $productQtyData)){
						if(isset($productQtyData[$subKey])){
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
						} else {
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
						}
						
					} else {
						$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
					}
					$orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
					
				}
		    }  
		    if($userData){
				foreach($userData as $subKey=>$subvalue){
					$orderDetails[$key]['user_details']['id'] = $userData->id;
					$orderDetails[$key]['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
					$orderDetails[$key]['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($userData->id);
				}
			} else {
				    $orderDetails[$key]['user_details']['id'] = "";
					$orderDetails[$key]['user_details']['name'] = '';	
					$orderDetails[$key]['user_details']['email'] = '';	
					$orderDetails[$key]['user_details']['mobile'] = '';	
					$orderDetails[$key]['user_details']['image'] = '';
			}
			
		}
			
		return response()->json(['status'=>'1','msg'=>'New Order List','orderDetails'=>$orderDetails], $this->successStatus);		
	}

	public function processOrderDetails(Request $request){
		$input = $request->all();
        $products = Products::where('user_id',$input['user_id'])->get(['id']);
        $pid = $orderDetails = array();
        if($products){
           foreach ($products as $key => $value) {        	
	        	$pid[] = $value->id;
	        }
	    }
		
	    $condition = '';
	    $query = Orders::where('order_status','Confirmed');
	    $result = Orders::where('order_status','Confirmed')->where('seller_id',$input['user_id'])->orWhere(function($query) use($pid){
	    	foreach ($pid as $key=> $id){
            	$query->orWhere('product_id','like','%"'.trim($id).'"%');
            }
        })->get();  
        foreach($result as $key=>$value){
		
			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$productId = array();			
			$userData = userDetails($value->user_id);
			$orderDetails[$key]['user_details']    = array();
			$orderDetails[$key]['product_details'] = array();
			$proId    = unserialize($value->product_id);			
			foreach($proId as $Key=>$productbvalue){
				if(in_array($productbvalue, $pid)){
				   $productId[]	= $productbvalue;				   
				}
			} 
			
			$productAttrData = [];
			if(!empty($value->product_attr_id)) {
				$product_attr = $value->product_attr_id;					
				$allAttrId = unserialize($product_attr);
				$productAttrData = array_filter($allAttrId);
			}
			$productData = ProductDetails(serialize(array_filter($productId)));
			$productQtyData  = array_filter(unserialize($value->qty));
			$orderDetails[$key]['total_item']    = sizeof($productData);
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){
					$orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
					$orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;

					if(count($productAttrData)>0){
						if(in_array($subKey, $productAttrData)){
							$attrName = getAttrName($productAttrData[$subKey]);
							if($attrName){			
								foreach($attrName as $subKey1=>$value){				
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
								} 
						    }
						    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
						}  else {
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
						}   
					    
					} else {
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
					}


					if(in_array($subKey, $productQtyData)){
						if(isset($productQtyData[$subKey])){
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
						} else {
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
						}
						
					} else {
						$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
					}
					$orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
					
				}
		    }  
			if($userData){
				foreach($userData as $subKey=>$subvalue){
					$orderDetails[$key]['user_details']['id'] = $userData->id;
					$orderDetails[$key]['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
					$orderDetails[$key]['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($userData->id);
				}
			} else {
				    $orderDetails[$key]['user_details']['id'] = "";
					$orderDetails[$key]['user_details']['name'] = '';	
					$orderDetails[$key]['user_details']['email'] = '';	
					$orderDetails[$key]['user_details']['mobile'] = '';	
					$orderDetails[$key]['user_details']['image'] = '';
			}
			
		}
			
		return response()->json(['status'=>'1','msg'=>'Processing Order List','orderDetails'=>$orderDetails], $this->successStatus);		
	}

	public function DelievredOrderDetails(Request $request){
		$input = $request->all();
        $products = Products::where('user_id',$input['user_id'])->get(['id']);
        $pid = $orderDetails = array();
        if($products){
           foreach ($products as $key => $value) {        	
	        	$pid[] = $value->id;
	        }
	    }
	    $condition = '';
	    $query = Orders::where('order_status','Delivered');
	    $result = Orders::where('order_status','Delivered')->where('seller_id',$input['user_id'])->orWhere(function($query) use($pid){
	    	foreach ($pid as $key=> $id){
            	$query->orWhere('product_id','like','%"'.trim($id).'"%');
            }
        })->get();  
        foreach($result as $key=>$value){
		
			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$productId[] = array();			
			$userData = userDetails($value->user_id);
			$orderDetails[$key]['user_details']    = array();
			$orderDetails[$key]['product_details'] = array();
			$proId    = unserialize($value->product_id);			
			foreach($proId as $Key=>$productbvalue){
				if(in_array($productbvalue, $pid)){
				   $productId[]	= $productbvalue;				   
				}
			} 

			$productAttrData = [];
			if(!empty($value->product_attr_id)) {
				$product_attr = $value->product_attr_id;					
				$allAttrId = unserialize($product_attr);
				$productAttrData = array_filter($allAttrId);
			}

			$productData = ProductDetails(serialize(array_filter($productId)));
			$productQtyData  = array_filter(unserialize($value->qty));
			$orderDetails[$key]['total_item']    = sizeof($productData);
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){
					$orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
					$orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;
					if(count($productAttrData)>0){
						if(in_array($subKey, $productAttrData)){
							$attrName = getAttrName($productAttrData[$subKey]);
							if($attrName){			
								foreach($attrName as $subKey1=>$value){				
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
									$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
								} 
						    }
						    $orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
						}  else {
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
							$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
						}   
					    
					} else {
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = '';
						$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= '';
					}


					if(in_array($subKey, $productQtyData)){
						if(isset($productQtyData[$subKey])){
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
						} else {
							$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
						}
						
					} else {
						$orderDetails[$key]['product_details'][$subKey]['qty'] 		= 0;
					}
					$orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
					
				}
		    } 
			if($userData){
				foreach($userData as $subKey=>$subvalue){
					$orderDetails[$key]['user_details']['id'] = $userData->id;
					$orderDetails[$key]['user_details']['name'] = $userData->name!='' ? $userData->name : '';	
					$orderDetails[$key]['user_details']['email'] = $userData->email!='' ? $userData->email : '';	
					$orderDetails[$key]['user_details']['mobile'] = $userData->mobile!='' ? $userData->mobile : '';	
					$orderDetails[$key]['user_details']['image'] = userProfile($userData->id);
				}
			} else {
				    $orderDetails[$key]['user_details']['id'] = "";
					$orderDetails[$key]['user_details']['name'] = '';	
					$orderDetails[$key]['user_details']['email'] = '';	
					$orderDetails[$key]['user_details']['mobile'] = '';	
					$orderDetails[$key]['user_details']['image'] = '';
			}
			
		}
			
		return response()->json(['status'=>'1','msg'=>'Delievred Order List','orderDetails'=>$orderDetails], $this->successStatus);		
	}

	public function PastOrderDetails(Request $request){
		$input = $request->all();
	    $result = Orders::where('user_id',$input['user_id'])->where(function($query) {
	    	$query->orWhere('order_status','Delievred');
            $query->orWhere('order_status','Cancelled');
        })->get();  
        $orderDetails = array();
        foreach($result as $key=>$value){
		
			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'DONE':'Pending';
			
			$orderDetails[$key]['product_details'] = array(); 
			$product_attr = $value->product_attr_id;					
			$allAttrId = unserialize($product_attr);
			$productAttrData = $allAttrId;
			$productData = ProductDetails($value->product_id);
			$productQtyData  = array_filter(unserialize($value->qty));
			$orderDetails[$key]['total_item']    = sizeof($productData);
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){
					$orderDetails[$key]['product_details'][$subKey]['id'] 	= $productvalue->id;
					$orderDetails[$key]['product_details'][$subKey]['name'] 	= $productvalue->name!='' ? $productvalue->name : '';	
					$orderDetails[$key]['product_details'][$subKey]['sku']   = $productvalue->sku!='' ? $productvalue->sku : '';	
					$orderDetails[$key]['product_details'][$subKey]['keywords'] = $productvalue->keywords!='' ? $productvalue->keywords : '';
					$image = productFirstImages($productvalue->id); 
					$orderDetails[$key]['product_details'][$subKey]['image'] 	= $image;

					$attrName = getAttrName($productAttrData[$subKey]);
					$orderDetails[$key]['product_details'][$subKey]['qty'] 		= $productQtyData[$subKey];
					$orderDetails[$key]['product_details'][$subKey]['attr_id'] 	= $productAttrData[$subKey];
					$orderDetails[$key]['product_details'][$subKey]['attr_name'] = array();
					if($attrName){			
						foreach($attrName as $subKey1=>$value){				
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
						} 
					}	
				}
		    }  
			
		}
			
		return response()->json(['status'=>'1','msg'=>'Delievred Order List','orderDetails'=>$orderDetails], $this->successStatus);		
	}

	public function PastBuyerOrderDetails(Request $request){
		
		$input = $request->all();

		$result = Orders::where('user_id',$input['user_id'])->orderBy('id','desc')->get();  
		
        $orderDetails = array();
        foreach($result as $key=>$value){

			$orderDetails[$key]['id']              = $value->id;
			$orderDetails[$key]['order_id']        = $value->order_id;
			$orderDetails[$key]['is_cancelled']     = $value->is_cancelled;
			$orderDetails[$key]['order_status']    = $value->order_status;
			$orderDetails[$key]['user_id']         = $value->user_id;
			$orderDetails[$key]['total_amount']    = $value->total_amount;
			$orderDetails[$key]['created_at']      = date('Y-m-d H:i A',strtotime($value->created_at));
			$orderDetails[$key]['payment_status']  = $value->payment_status == 1? 'Completed':'Pending';
			
			$orderDetails[$key]['product_details'] = array(); 
			$product_attr = $value->product_attr_id;					
			$allAttrId = unserialize($product_attr);
			$productAttrData = $allAttrId;
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

					/*if(in_array($subKey, $productAttrData)){
						$attrName = getAttrName($productAttrData[$subKey]);
					} else {
						$attrName = '';
					}
					*/
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
					/*if($attrName){			
						foreach($attrName as $subKey1=>$value){				
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['name'] = $subKey1;
							$orderDetails[$key]['product_details'][$subKey]['attr_name']['value'] = $value;
						} 
					}*/	
				}
		    }  
		}
			
		return response()->json(['status'=>'1','msg'=>'Order List','orderDetails'=>$orderDetails], $this->successStatus);		
	}

	public function getBuyerSingleOrderDetail(Request $request){
		
		$input = $request->all();

		$result = Orders::where('id', $input['order_id'])->orderBy('id','desc')->get();  
		
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

		return response()->json([
			'status'=>'1',
			'msg'=>'Order List',
			'orderDetails'=>$orderDetails[$key]
		], $this->successStatus);		
	}

	public function updateOrderStatus(Request $request){
		$input 		= $request->all();
		$orders 	= Orders::where('id',$input['id'])->update($input);
		return response()->json(['status'=>'1','msg'=>'Order Status Updated Successfully','orderData'=>$input], $this->successStatus);
	}

	public function updateOrderCancelStatus(Request $request) {

		$input 		= $request->all();

		$order_details = Orders::find($input['id']);

		if(empty($order_details)) {
			return response()->json(['status'=>'0','msg'=>'Order not found, Please contact to admin.'], $this->successStatus);
		}
		elseif($order_details->is_cancelled) {
			return response()->json(['status'=>'0','msg'=>'Order is already cancelled.'], $this->successStatus);
		}
		else {
			$input['is_cancelled'] = 1;
			$input['order_status'] = 'Cancelled';
			Orders::where('id',$input['id'])->update($input); // update order status as returned
		}

		return response()->json(['status'=>'1','msg'=>'Your Order Cancellation Request Submited Successfully'], $this->successStatus);
		exit;

		
		$order_details = Orders::find($input['id']); // get updated value of that order
		
		$order_d = array();
		$morder = $order_d['order_id'] = $order_details->id;
		
		$order_d['total_amount'] = $order_details->total_amount;
		
		$order_d['product_id'] = $order_details->product_id;
		
		$uamount = $order_d['total_amount'] = $order_details->total_amount;
		$order_d['user_id'] = $order_details->user_id;
		$uname = User::find($order_details->user_id)->name;
		$uemail = User::find($order_details->user_id)->email;
		
		$order_d['seller_id'] = $order_details->seller_id;
		$usellemail = User::find($order_details->seller_id)->email;
	
		$productdata 		= ProductDetails($order_details->product_id);
		
		$productAttrdata 	= ProductmultAttrDetails($order_details->product_attr_id);
		$amount             = unserialize($order_details->amount);
		$qty                = unserialize($order_details->qty);
		
		$data = array('uname'=>$uname,'uamount'=>$uamount,'morder'=>$morder,'productdata'=>$productdata,'productAttrdata'=>$productAttrdata);
				Mail::send(['html'=>'returnMail'], $data, function ($m) use ($uemail)  {
					$m->from('test@hello.com', 'Your Order Cancellation Request Submited Successfully');
					//$m->cc(['vadimm@getnada.com']);
					$m->to($uemail)->subject('Your Reminder!');
		}); 
		Mail::send(['html'=>'returnMailSeller'], $data, function ($m) use ($usellemail)  {
					$m->from('test@hello.com', 'Your Order Cancellation Request Submited Successfully');
					//$m->cc(['vadimm@getnada.com']);
					$m->to($usellemail)->subject('Your Reminder!');
		}); 
		return response()->json(['status'=>'1','msg'=>'Your Order Cancellation Request Submited Successfully'], $this->successStatus);
	}

	public function updateOrderReturnStatus(Request $request){
		
		$input = $request->all();

		if(empty($input['id']) || empty($input['product_id']) || empty($input['qty'])) {
			return response()->json(['status'=>'0','msg'=>'Error in information receiving, Please contact to admin.'], $this->successStatus);
		}
		elseif ($input['qty'] < 1) {
			return response()->json(['status'=>'0','msg'=>'Please send valid informaion of product quantity.'], $this->successStatus);
		}
		
		$order_details = Orders::find($input['id']);

		if(empty($order_details)) {
			return response()->json(['status'=>'0','msg'=>'Order not found, Please contact to admin.'], $this->successStatus);
		} 
		elseif($order_details->is_cancelled) {
			return response()->json(['status'=>'0','msg'=>'Order is cancelled, You can not retutn item.'], $this->successStatus);
		}
		else {

			$productData = ProductDetails($order_details->product_id); // get all products of that order
			$productQtyData  = array_filter(unserialize($order_details->qty));
			if(!empty($productData)){
				foreach($productData as $subKey=>$productvalue){
					
					if(isset($productQtyData[$subKey])){
						$orderedProducts[$productvalue->id]['qty'] = $productQtyData[$subKey];					
					} else {
						$orderedProducts[$productvalue->id]['qty'] = 0;
					}
				}
		    }

			$alreadyReturnedQty = 0;
			if($order_details->returnedOrders) {
				foreach($order_details->returnedOrders as $returnedProduct) {
					if($returnedProduct->product_id == $input['product_id']) {
						$alreadyReturnedQty += $returnedProduct->qty;
					}
				}
			}
			
			$orderedQty = $orderedProducts[$input['product_id']]['qty'];

			if($alreadyReturnedQty > $orderedQty) {
				return response()->json(['status'=>'0','msg'=>'You have left only ' . ($orderedQty - $alreadyReturnedQty) .' quantity.'], $this->successStatus);
			}
			elseif(($input['qty'] + $alreadyReturnedQty) > $orderedQty) {
				return response()->json(['status'=>'0','msg'=>'You have left only ' . ($orderedQty - $alreadyReturnedQty) .' quantity.'], $this->successStatus);
			}
			elseif($alreadyReturnedQty == $orderedQty) {
				return response()->json(['status'=>'0','msg'=>'Product is already returned.'], $this->successStatus);
			}
		}

		$orderReturn = [
			'order_id' => $input['id'],
			'product_id' => $input['product_id'],
			'user_id' => $order_details->user_id,
			'seller_id' => $order_details->seller_id,
			'qty' => $input['qty'],
			'return_status' => 'Pending',
		];
		OrderReturn::create($orderReturn);

		$orderData = getOrderProductData($orderReturn['order_id'], $orderReturn['product_id']);
		
		return response()->json([
			'status'=>'1',
			'msg'=>'Your Order Cancellation Request Submited Successfully',
			'orderData' => $orderData,
		], $this->successStatus);

		exit;

	
		
		$order_d = array();
		$morder = $order_d['order_id'] = $order_details->id;
		
		$order_d['total_amount'] = $order_details->total_amount;
		
		$order_d['product_id'] = $order_details->product_id;
		
		$uamount = $order_d['total_amount'] = $order_details->total_amount;
		$order_d['user_id'] = $order_details->user_id;
		$uname = User::find($order_details->user_id)->name;
		$uemail = User::find($order_details->user_id)->email;
		
		$order_d['seller_id'] = $order_details->seller_id;
		$usellemail = User::find($order_details->seller_id)->email;
		
		$productdata 		= ProductDetails($order_details->product_id);
		
		$productAttrdata 	= ProductmultAttrDetails($order_details->product_attr_id);
		$amount             = unserialize($order_details->amount);
		$qty                = unserialize($order_details->qty);
		
		$data = array('uname'=>$uname,'uamount'=>$uamount,'morder'=>$morder,'productdata'=>$productdata,'productAttrdata'=>$productAttrdata);
				Mail::send(['html'=>'returnMail'], $data, function ($m) use ($uemail)  {
					$m->from('test@hello.com', 'Your Order Return Request Submited Successfully');
					//$m->cc(['vadimm@getnada.com']);
					$m->to($uemail)->subject('Your Reminder!');
		}); 
		Mail::send(['html'=>'returnMailSeller'], $data, function ($m) use ($usellemail)  {
					$m->from('test@hello.com', 'Your Order Return Request Submited Successfully');
					//$m->cc(['vadimm@getnada.com']);
					$m->to($usellemail)->subject('Your Reminder!');
		}); 
		return response()->json(['status'=>'1','msg'=>'Your Order Return Request Submited Successfully','orderData'=>$input], $this->successStatus);
	}
	
	public function helpSection(Request $request){
		$input = $request->all();
		$help = Help::create($input);
		$help_title = $help->help_title;
		$help_description = $help->help_description;
		// dd($help_description);
		// die;
		$help_data = Help::where('user_id',$input['user_id'])->get();
		$uname = User::find($help->user_id)->name;
		$uemail = User::find($help->user_id)->email;
		
		$data = array('help_title'=>$help_title,'help_description'=>$help_description,'uname'=>$uname);
				Mail::send(['html'=>'helpmail'], $data, function ($m) use ($uemail)  {
					$m->from('test@hello.com', 'Help request submited successfully');
					$m->cc(['peeyushyadav136@gmail.com']);
					$m->to($uemail)->subject('Your Reminder!');
		}); 
		
		return response()->json(['status'=>'1','msg'=>'Your Request is submited successfully, we will contact you soon','help_data'=>$help_data], $this->successStatus);
	}

	public function reviewRating(Request $request){
		$input 		        = $request->all();
		$ReviewsData        = array();
		$input['seller_id'] = getSellerByOrderId($input['order_id']);
		$Reviews  	        = Reviews::where('user_id',$input['user_id'])->where('order_id',$input['order_id'])->count();
		if($Reviews>0){
			$msg            = "Alraedy given rating";
		} else {
			$ReviewsData  	= Reviews::create($input);
			$totalReview    = Reviews::where('seller_id',$input['seller_id'])->sum('rating');
			$totalCount     = Reviews::where('seller_id',$input['seller_id'])->count();
			$ratingInput['rating']  = ceil($totalReview/$totalCount);
			User::where('id',$input['seller_id'])->update($ratingInput);
			$msg            = "Thank you for taking your time for the rating";
		}
		
		return response()->json(['status'=>'1','msg'=>$msg,'data'=>$ReviewsData], $this->successStatus);
	}

	public function reviewOrder(Request $request){
		$input 		        = $request->all();
		$ReviewsData        = array();
		$user_id = $input["user_id"];
		
		/* $sql = "SELECT o.id as ids FROM orders as o RIGHT JOIN reviews as r on o.id != r.order_id where o.user_id = '$user_id' and o.order_status='Delievred'"; */
		$sql = "select id from orders where user_id = '$user_id'  and order_status='Delivered' and id NOT IN (select order_id from reviews)";

		
		$orderDeliverd = DB::select($sql);
		//print_r($orderDeliverd);die;
		if($orderDeliverd){
			$status = 1;
		}else{
			$status = 0;
		}
		
		return response()->json(['status'=>$status,'data'=>$orderDeliverd], $this->successStatus);
	}
	
	public function send_notification($one_signal_app_id,$one_signal_token_id,$heading,$content,$data_response,$player_ids)
	{
		$fields = array(
			  'app_id' => $one_signal_app_id,
			  #set your player ids here
			  //'include_player_ids' => ['b84c3112-bc6a-477c-b6f9-f81319cd3df6','199a241c-ff3e-408c-955c-45b6d06a1926','e00b88d3-771e-4e0f-b4ae-229b6ef450cc'],
			  //'include_player_ids' => [$value->app_id],
			  'include_player_ids' => [$player_ids],
			  'contents' => $content,
			  'headings' => $heading,
			  'data' =>$data_response
		);
		#decode your basic field and send in post field
		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		#YOUR_REST_API_KEY paste here and set headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
						  'Authorization: Basic '.$one_signal_token_id));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);
		#decode onesignal api response
		$response = json_decode($response);
		//echo "<pre>";print_r($response);die;
		return $response;
	}

	public function shareFeedback(Request $request) {
		
		$input = $request->all();

		if(Feedback::create($input)) {
			return response()->json(['status'=>'1','msg'=>"Your feedback submitted sucessfully."], $this->successStatus);
		} else {
			return response()->json(['status'=>'0','msg'=>"Error in feedback submission, Please try again."], $this->successStatus);
		}
	}

}
		