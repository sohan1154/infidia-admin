<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BusinessCategories extends Authenticatable
{
    use Notifiable;
	use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'category_id', 'user_id', 'created_at', 'updated_at'
    ];

    public function category() {
		  return $this->belongsTo('App\Models\Categories', 'category_id');
    }

    public function parentCategory() {
		  return $this->belongsTo('App\Models\BusinessCategories', 'parent_id');
    }

}
