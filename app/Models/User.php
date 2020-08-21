<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
	use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile','role','random_token','token_status','profile_pic','category','plan_id','status','is_verified','payment_id','fb_id','rating','is_deleted','app_id','app_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function userAddress()
    {
        return $this->hasOne('App\Models\UserAddress', 'user_id');
    }
    
    public function profilePicture()
    {
        return $this->hasOne('App\Models\ProfilePictures', 'user_id');
    }
    
}
