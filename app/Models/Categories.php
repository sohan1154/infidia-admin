<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Categories extends Authenticatable
{
    use Notifiable;
	use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'name', 'status', 'image', 'type', 'is_deleted', 'created_at', 'updated_at'
    ];

    public function parentCategory() {
        return $this->belongsTo('App\Models\Categories', 'parent_id');
    }

    public function attributes() {
        return $this->hasMany('App\Models\Attribute', 'category_id');
    }

}
