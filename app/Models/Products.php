<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Products  extends Authenticatable
{
    use Notifiable;
	use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'user_id', 'sku', 'barcode', 'name', 'description', 'weight', 'weight_unit', 'return_policy', 'warranty', 'shipping_time', 'meta_key', 'meta_description', 'is_display_outof_stock_product', 'status', 'created_at', 'updated_at'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleted(function($website){
            $website->price()->delete();
            $website->stock()->delete();
            $website->meta()->delete();
            $website->images()->delete();
        });
    }

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function categories() {
        return $this->belongsTo('App\Models\Categories','category_id');
    }
	
	public function price() {
		return $this->hasOne('App\Models\Price');
    }

	public function stock() {
		return $this->hasOne('App\Models\Stock');
    }

	public function meta() {
		return $this->hasOne('App\Models\Meta');
    }

	// public function image() {
	// 	return $this->hasOne('App\Models\Image');
    // }

    public function images() {
        return $this->hasOne('App\Models\Images');
    }

    public function productAttributes() {
        return $this->hasMany('App\Models\ProductAttributes');
    }

}
