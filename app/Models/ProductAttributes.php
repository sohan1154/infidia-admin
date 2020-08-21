<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductAttributes  extends Authenticatable
{
    use Notifiable;
	use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'products_id', 'sku', 'barcode', 'base_price', 'sale_price', 'qty', 'attrs', 'images', 'created_at', 'updated_at'
    ];

    public function product() {
		return $this->hasOne('App\Models\Products', 'foreign_key', 'products_id');
    }
    
}