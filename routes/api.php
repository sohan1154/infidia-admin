<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('sendOtp', 'API\UserController@sendOtp');
Route::get('contact-us', 'API\UserController@contactUs');
Route::post('contact-us', 'API\UserController@contactUs');
Route::post('resendOtp', 'API\UserController@resendOtp');
Route::post('verifyOtp', 'API\UserController@verifyOtp');
Route::post('changePassword', 'API\UserController@changePassword');
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::post('resetPassword', 'API\UserController@resetPassword');

Route::post('updateProfile', 'API\UserController@update_profile');
Route::post('updateProfilePicture', 'API\UserController@update_profile_picture');
Route::post('updateUserDetails', 'API\UserController@updateUserDetails');

Route::post('OrderDetails', 'API\UserController@OrderDetails');
Route::post('reviewRating', 'API\UserController@reviewRating');
Route::post('reviewOrder', 'API\UserController@reviewOrder');

//Route for Home screen 
Route::post('homePage', 'API\HomepageController@home_banner');
Route::post('categories', 'API\HomepageController@categories');
Route::post('categoriesSearch', 'API\HomepageController@categoriesSearch');
Route::post('add_address', 'API\HomepageController@add_address');
Route::post('delete_address', 'API\HomepageController@delete_address');
Route::post('fetch_address', 'API\HomepageController@fetch_address');
Route::get('allCategories', 'API\HomepageController@allCategories');
Route::post('subCategories', 'API\HomepageController@subCategories');
Route::post('updateSubscriptionplans', 'API\UserController@updateSubscriptionplans');
Route::post('shop_list', 'API\HomepageController@shop_list');
Route::post('shoplistFilter', 'API\HomepageController@shoplistFilter');
Route::post('userDetails', 'API\UserController@userDetails');
Route::post('fbLogin', 'API\UserController@fbLogin');
Route::post('newOrderDetails', 'API\UserController@newOrderDetails');
Route::post('newDeliveryOrderDetails', 'API\UserController@newDeliveryOrderDetails');
Route::post('newDeliveryProcessingOrder', 'API\UserController@newDeliveryProcessingOrder');
Route::post('DeliveryProcessingOrderStatus', 'API\UserController@DeliveryProcessingOrderStatus');
Route::post('DeliveryOrderHistory', 'API\UserController@DeliveryOrderHistory');
Route::post('processOrderDetails', 'API\UserController@processOrderDetails');
Route::post('DelievredOrderDetails', 'API\UserController@DelievredOrderDetails');
Route::post('updateOrderStatus', 'API\UserController@updateOrderStatus');
Route::post('updateOrderStatusByDelivery', 'API\UserController@updateOrderStatusByDelivery');
Route::post('PastOrderDetails', 'API\UserController@PastOrderDetails');
Route::post('PastBuyerOrderDetails', 'API\UserController@PastBuyerOrderDetails');
Route::post('getBuyerSingleOrderDetail', 'API\UserController@getBuyerSingleOrderDetail');
Route::post('updateOrderReturnStatus', 'API\UserController@updateOrderReturnStatus');
Route::post('updateOrderCancelStatus', 'API\UserController@updateOrderCancelStatus');
Route::post('helpSection', 'API\UserController@helpSection');
Route::post('shareFeedback', 'API\UserController@shareFeedback');

//Orders API
Route::post('userCashOrder', 'API\UserController@userCashOrder');
Route::post('userOrder', 'API\UserController@userOrder');

Route::get('subscriptionplans', 'API\HomepageController@subscriptionplans');
Route::post('searchAPI', 'API\HomepageController@searchAPI');
Route::post('searchProduct', 'API\HomepageController@searchProduct');

//product API
Route::post('getAttributes', 'API\ProductController@getAttributes');
Route::post('productDetails', 'API\ProductController@productDetails');
Route::get('productDetails', 'API\ProductController@productDetails');
Route::post('get-store-page-data', 'API\ProductController@getStorePageData');
Route::post('productList', 'API\ProductController@productList');
Route::post('storeListBasedCatList', 'API\ProductController@storeListBasedCatList');
Route::post('productListBasedCatList', 'API\ProductController@productListBasedCatList');
Route::post('sellerProductList', 'API\ProductController@sellerProductList');
Route::post('sellerProductCategories', 'API\ProductController@sellerProductCategories');
Route::post('addProducts', 'API\ProductController@addProducts');
Route::post('deleteProduct', 'API\ProductController@deleteProduct');
Route::post('addCart', 'API\ProductController@addCart');
Route::post('updateCart', 'API\ProductController@updateCart');
Route::post('emptyCart', 'API\ProductController@emptyCart');
Route::post('addOrRemoveWishlist', 'API\ProductController@addOrRemoveWishlist');
Route::post('updateWishlist', 'API\ProductController@updateWishlist');
Route::post('deleteProductFromCart', 'API\ProductController@deleteProductFromCart');
Route::post('deleteProductFromWishlist', 'API\ProductController@deleteProductFromWishlist');
Route::post('getCartProducts', 'API\ProductController@getCartProducts');

Route::post('getWishlistProducts', 'API\ProductController@getWishlistProducts');

Route::get('pages', 'API\HomepageController@pages');
//Route for Home screen 

Route::group(['middleware' => 'auth:api', 'prefix' => 'password'], function(){
	Route::post('details', 'API\UserController@details');
	Route::post('sendEmailLink', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});

/*reset password*/
Route::group([    
    'namespace' => 'Auth',    
    'middleware' => 'api',    
    'prefix' => 'password'
], function () {    
    Route::post('sendEmailLink', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});
