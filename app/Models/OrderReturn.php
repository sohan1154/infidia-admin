<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OrderReturn  extends Authenticatable
{
	use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'product_id', 'user_id', 'seller_id', 'qty', 'return_status'
    ];
    
	public function order() {
        return $this->belongsTo('App\Models\Orders', 'order_id');
    }
    
	public function product() {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }
    
	public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

	public function seller() {
        return $this->belongsTo('App\\User', 'seller_id');
    }
}
