<?php

namespace App\Http\Middleware;
use App\Settings;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
    'password/reset*',
    'update_password*',
    'password/email*',
    'password/reset*',
    'password/reset*',
    'product*',
    'shop_list',
    'updateUserDetails',
    'removeImage',
    'removeAttrImage',
    'removeAttrData',
    'category*',
    'checkSku',
    'deleteProductFromWishlist',
  ];
    
}
