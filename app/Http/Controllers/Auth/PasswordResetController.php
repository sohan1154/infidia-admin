<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\User;
use App\Models\PasswordReset;
use Validator;

class PasswordResetController extends Controller
{
    public $successStatus = 200;
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
	 
	public function updatePassword(Request $request){
		dd($request->all());
	} 
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {             
            if (!$validator->errors()->toArray()['email']='') {
                $msg = $validator->errors()->toArray()['email']['0'];
            }

            return response()->json(['status'=>'0','msg'=>$msg], $this-> successStatus);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user)
            return response()->json([
                'status' => '0',
                'msg' => 'We can not find a user with that e-mail address.'
            ], $this-> successStatus);
        /*$passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
             ]
        );*/

        $existPasswordReset = PasswordReset::where('email', $request->email)->first();
        if (empty($existPasswordReset)) {
            $passwordReset = PasswordReset::create(
                [
                    'email' => $user->email,
                    'token' => str_random(60)
                ]
            );
        } else {
            /*$passwordReset = PasswordReset::where('email', $existPasswordReset->email)->update(['token' => str_random(60)]);*/
            $input['token'] = str_random(60); 
            $existPasswordReset->fill($input)->save();

            $passwordReset = PasswordReset::where('email', $request->email)->first();
        }

        if ($user && $passwordReset)
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );
        return response()->json([
            'status' => '1',
            'msg' => 'We have e-mailed your password reset link!'
        ]);
    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find(Request $request,$token)
    {
		$validator = Validator::make($request->all(), [ 
            'token' => 'required',
            
        ]);
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset)
            return response()->json([
                'status' => '0',
                'msg' => 'This password reset token is invalid.'
            ], $this-> successStatus);
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(180)->isPast()) {
            $passwordReset = PasswordReset::where('token', $passwordReset['token'])->delete();
            return response()->json([
                'status' => '0',
                'msg' => 'This password reset token is invalid.'
            ], $this-> successStatus);
        } /* else {
            $passwordReset['status'] ='1';
            $passwordReset['msg'] = 'This password reset token is valid.';
        } */
		$errors = $validator->messages();
       // return response()->json($passwordReset);
	   return view('verifyresetpasswordtoken',compact('passwordReset','errors'));
    }
     /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {   
        $input = $request->all();
        //echo '<pre>'; print_r($input); die;
        /*$request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);*/

        $validator = Validator::make($request->all(), [ 
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();
        if (!$passwordReset)
            return response()->json([
                'status' => '0',
                'msg' => 'This password reset token is invalid.'
            ], $this-> successStatus);
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user)
            return response()->json([
                'status' => '0',
                'msg' => 'We cant find a user with that e-mail address.'
            ], $this-> successStatus);
        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset = PasswordReset::where('token', $passwordReset['token'])->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        
        $user['status'] ='1';
        $user['msg'] = 'password reset successfully.';
        return response()->json($user);
    }
}