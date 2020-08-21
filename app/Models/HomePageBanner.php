<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class HomePageBanner extends Authenticatable
{
    use Notifiable;
	use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'banner_name', 'banner_image', 'external_link', 'banner_description', 'status', 'created_at', 'updated_at'
    ];

    function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

}
