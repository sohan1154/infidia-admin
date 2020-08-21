<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Orders  extends Authenticatable
{
    use Notifiable;
	use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'user_id', 'seller_id', 'order_id', 'total_amount', 'amount','qty', 'payment_status', 'shipping_address', 'billing_address', 'order_status', 'shipping_status', 'is_cancelled', 'return_status' 
    ];
    
	public function product() {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
    
	public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

	public function seller() {
        return $this->belongsTo('App\User', 'seller_id');
    }

	public function returnedOrders() {
        return $this->hasMany('App\Models\OrderReturn', 'order_id', 'id');
    }

}
