<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAddress extends Authenticatable
{
    use Notifiable;
	use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'location', 'landmark', 'house_no', 'address', 'type', 'latitude', 'longitude', 'status', 'min_order', 'avg_delivery_time', 'shop_address', 'city', 'country', 'state'
    ];
	
}
