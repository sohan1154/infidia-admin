<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use App\User;
use App\Models\Products;
use App\Models\UserAddress; 
use App\Models\ProfilePictures; 

class UsersController extends Controller
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
    
	public function user($role){
        $users = User::where('is_deleted','0')->get();
        // $users = User::where('is_deleted','0')->whereNotNull('name')->get();
		//$users = User::where('id','21')->get();
		//dd($users);
        if($role=='delivery_boy'){
            $role = 'Delivery';
        }
		return view('users/user',compact('users','role'));
	}
	
	######
	public function edit_user($role,$uid){
        $id = base64_decode($uid);
		if ($id == '') {
            return 'URL NOT FOUND';
        }
		$user = User::find($id);
		if (empty($user)) {
			return 'URL NOT FOUND';
            //return redirect()->action('UsersController@edit_user',$id)->with('alert-danger', 'user not found');
        }		
		return view('users/edit_user',compact('user'));
	}

	public function update_user(Request $request, $id,$role) {
        if ($id == '') {
            return redirect()->action('UsersController@edit_user',$id)->with('alert-danger', 'user not found');
        }

        $user = User::findOrFail($id);

        if (empty($user)) {
            return redirect()->action('UsersController@edit_user',$id)->with('alert-danger', 'user not found');
        }

        $input = $request->all();

        /*if($request->input('image')) {

            $image_array = [];

            foreach ($request->input('image') as $image) {
                
                $image_name = '';
                $uploadpath = public_path().'\images';
                $original_name = $image;

                if (!empty($image)) {
                    $image_prefix = 'user_' . $original_name;
                    $image_name = $image_prefix;
                    $image_array[] = $image_name;
                    $image = move_uploaded_file($uploadpath, $image_name);
                }
            }
        }*/
        if($request->file('image')) {              
                $image_name = '';
                $uploadpath = public_path().'/images/profile';
                
                if (!empty($request->file('image'))) {
                    
                    $image_prefix = 'profile_' . rand(0, 999999999) . '_' . date('d_m_Y_h_i_s');
                    $ext = $request->file('image')->getClientOriginalExtension();
                    $image_name = $image_prefix . '.' . $ext;
                    $request->file('image')->move($uploadpath, $image_name);
                }
        } else { 
                $image_name  = $input['old_image'];
        }
        $user->fill($input)->save();
        $input['image'] = $image_name;
        $profile_data = ProfilePictures::where('user_id', '=', $user->id)->first();
        if(!isset($profile_data->id) && empty($profile_data->id)){
            $profile_pic_query = ProfilePictures::create(
                ['picture' => $input['image'], 'user_id'=>$user->id, 'status' => '1', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]
            );
            $last_inserted_id = $profile_pic_query->id;            
            $user = User::where('id', $user->id)
            ->update(['profile_pic' => $last_inserted_id]); 
        }
        else{
            $profile_pic_query = ProfilePictures::where('id', $profile_data->id)
            ->update(['picture' => $input['image'], 'updated_at'=> date('Y-m-d h:i:s')]);
            $user = User::where('id', $user->id)
            ->update(['profile_pic' => $profile_data->id]); 
        }       
        
        return redirect()->action('UsersController@user',$role)->with('alert-success', 'User Information Updated Successfully');
    }

	public function delete($userid,$role) {
        $id   = base64_decode($userid);
        $user = User::findOrFail($id);
        $input['is_deleted'] = '1';
        $user->fill($input)->save();
        $productinput['status'] = '0';
		Products::where('user_id',$id)->update($productinput);
        //User::findOrFail($id)->delete();   
        return redirect('users/'.$role)->with('alert-success', 'User Deleted Successfully');
    }

    public function status($ids,$status,$role) {
        $id = base64_decode($ids);        
        $users =  User::find($id);
        if (empty($users)) {
            return 'URL NOT FOUND';
        }

        $input['status'] = $status;
        unset($input['_token']);
        if($input['status']=='1'){
          $productinput['status'] = '1';  
          Products::where('user_id',$id)->where('status','2')->update($productinput);  
        } else {
          $productinput['status'] = '2';  
          Products::where('user_id',$id)->update($productinput);  
        }
        $users->fill($input)->save();

        return redirect('users/'.$role)->with('alert-success', 'User Status Updated Successfully');
    }

    public function verify($ids) {
        $id = base64_decode($ids);        
        $users =  User::find($id);
        if (empty($users)) {
            return 'URL NOT FOUND';
        }

        $input['is_verified'] = 1;
        $users->fill($input)->save();

        return redirect('users/seller')->with('alert-success', 'User verified Successfully');
    }

    public function view($role=null,$userId=null){
        $id             = base64_decode($userId);
        $users          = User::where('id',$id)->first();
        $userAddress    = UserAddress::where('user_id',$id)->get();
        return view('users/view',compact('users','userAddress'));
    }
	######
	
	
}
