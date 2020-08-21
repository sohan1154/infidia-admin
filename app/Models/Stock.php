<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Stock extends Authenticatable
{
    use Notifiable;
    use HasApiTokens, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'products_id', 'qty', 'stock_status','created_at', 'updated_at'
    ];
	
	public function products() {
		return $this->belongsTo('App\Models\Products', 'foreign_key', 'products_id');
    }
	
}
