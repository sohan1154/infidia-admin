<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('', 'WebsiteController@index')->name('website-home');
Route::get('about', 'WebsiteController@about')->name('about');
Route::get('business', 'WebsiteController@business')->name('business');
Route::get('privacy', 'WebsiteController@privacy')->name('privacy');
Route::get('terms', 'WebsiteController@terms')->name('terms');
Route::get('contact', 'WebsiteController@contact')->name('contact');

Route::get('admin/', function () {
    return view('auth.login');
});
Route::get('seller/', function () {
    return view('auth.login');
});

Route::get('logout', function () {
    if (Auth::check()) {
        
        $role = Auth::user()->role;
        Auth::logout();
        if($role == "seller") {
            return redirect('/seller');
        } else {
            return redirect('/admin');
        }
    } else {
        return redirect('/seller');
    }
});

Auth::routes();

Route::get('thankyou', 'OrderController@thankyou');

Route::get('home', 'HomeController@index')->name('home');
Route::get('dashboard', 'HomeController@index')->name('dashboard');

/*---------------Categories Routes Start---------------*/
Route::get('categories', 'CategoriesController@category')->name('category');
Route::get('add-category', 'CategoriesController@add_category')->name('add_category');
Route::get('edit-category/{id}', 'CategoriesController@edit_category')->name('edit_category');
Route::get('category/status/{id}/{status}', 'CategoriesController@status_category');
Route::post('create-category', 'CategoriesController@insert_cat');
Route::post('update-category/{id}', 'CategoriesController@update_cat');
Route::get('delete-category/{id}', 'CategoriesController@delete_cat');

Route::get('product-categories/{parent_id}', 'CategoriesController@product_categories');
Route::get('add-product-category/{parent_id}', 'CategoriesController@add_product_category');
Route::post('create-product-category', 'CategoriesController@insert_product_cat');
Route::get('edit-product-category/{parent_id}/{id}', 'CategoriesController@edit_product_category');
Route::post('update-product-category/{id}', 'CategoriesController@update_product_cat');
Route::get('delete-product-category/{parent_id}/{id}', 'CategoriesController@delete_product_cat');

Route::get('business-categories', 'BusinessCategoriesController@index');
Route::get('add-business-categories', 'BusinessCategoriesController@add');
Route::get('add-business-sub-categories/{id}', 'BusinessCategoriesController@add_sub_categories');
Route::post('create-business-categories', 'BusinessCategoriesController@create');
Route::get('delete-business-category/{id}', 'BusinessCategoriesController@delete');

Route::get('business-product-categories/{category_id}', 'BusinessCategoriesController@business_product_categories');
Route::get('add-business-product-categories/{category_id}', 'BusinessCategoriesController@add_business_product_categories');
Route::post('create-business-product-categories', 'BusinessCategoriesController@create_business_product_categories');
Route::get('delete-business-product-category/{category_id}/{id}', 'BusinessCategoriesController@delete_business_product_category');
/*---------------Categories Routes End---------------*/

/*---------------Products Routes Start---------------*/
Route::get('products', 'ProductController@index')->name('index');
Route::get('product/create', 'ProductController@create')->name('create');
Route::get('product/add-attrs-in-form', 'ProductController@addAttrsInForm');
Route::get('product/get-attributes/{category_id}', 'ProductController@getAttributes');
Route::post('product/add', 'ProductController@add');
Route::get('deleteProductAttrBox', 'ProductController@deleteProductAttrBox');
Route::post('removeImage', 'ProductController@removeImage');
Route::post('removeAttrImage', 'ProductController@removeAttrImage');
Route::get('product/update/{id}', 'ProductController@update');
Route::post('product/edit/{id}', 'ProductController@edit');
Route::get('delete-product/{id}', 'ProductController@delete');
Route::get('products/status/{id}/{status}', 'ProductController@status');
Route::get('products/view/{id}', 'ProductController@view');
Route::post('products/checkSku', 'ProductController@checkSku');
Route::post('importExcel', 'ProductController@importExcel');
/*---------------Products Routes End---------------*/

/*---------------Subscriptions Routes Start---------------*/
Route::get('subscriptions', 'SubscriptionController@index')->name('index');
Route::get('subscriptions/create', 'SubscriptionController@create')->name('create');
Route::post('subscriptions/add', 'SubscriptionController@add');
Route::get('subscriptions/update/{id}', 'SubscriptionController@update');
Route::post('subscriptions/edit/{id}', 'SubscriptionController@edit');
Route::get('delete-subscriptions/{id}', 'SubscriptionController@delete');
Route::get('subscriptions/status/{id}/{status}', 'SubscriptionController@status');
/*---------------Subscriptions Routes End---------------*/

/*---------------resest password routes---------------*/
Route::get('verifyresetpasswordtoken/{token}', 'HomeController@verifyResetPasswordToken');
Route::post('update_password', 'Auth\PasswordResetController@updatePassword');
Route::get('update_password', 'Auth\PasswordResetController@updatePassword');

/*---------------Users Routes Start---------------*/
Route::get('users/{role}', 'UsersController@user')->name('user');
Route::get('users/{role}/view/{id}', 'UsersController@view');
Route::get('users/{role}/edit/{id}', 'UsersController@edit_user')->name('edit_user');
Route::post('update-user/{id}/{role}', 'UsersController@update_user');
Route::get('users/status/{id}/{status}/{role}', 'UsersController@status');
Route::get('users/verify/{id}', 'UsersController@verify');
Route::get('users/delete/{id}/{role}', 'UsersController@delete');
/*---------------Users Routes End---------------*/

/*---------------Banners Routes Start---------------*/
Route::get('banners', 'BannerController@index');
Route::get('banners/create', 'BannerController@create')->name('create');
Route::post('banners/add', 'BannerController@add');

Route::get('banners/update/{id}', 'BannerController@update')->name('create');
Route::post('banners/edit/{id}', 'BannerController@edit');
Route::get('delete-banner/{id}', 'BannerController@delete');
Route::get('banners/status/{id}/{status}', 'BannerController@status');
/*---------------Banners Routes End---------------*/

/*---------------Attributes Routes Start---------------*/
Route::get('attributes', 'AttributeController@index');
Route::get('attributes/create', 'AttributeController@create')->name('create');
Route::post('attributes/add', 'AttributeController@add');
Route::get('attributes/status/{id}/{status}', 'AttributeController@status');
Route::get('attributes/update/{id}', 'AttributeController@update')->name('create');
Route::post('attributes/edit/{id}', 'AttributeController@edit');
Route::get('delete-attribute/{id}', 'AttributeController@delete');
/*---------------Attributes Routes End---------------*/

/*---------------Pages Routes Start---------------*/
Route::get('contactus', 'ContactusController@index');
/*---------------Attributes Routes End---------------*/

/*---------------Pages Routes Start---------------*/
Route::get('pages', 'PageController@index');
Route::get('pages/create', 'PageController@create')->name('create');
Route::post('pages/add', 'PageController@add');

Route::get('pages/update/{id}', 'PageController@update')->name('create');
Route::post('pages/edit/{id}', 'PageController@edit');
Route::get('delete-page/{id}', 'PageController@delete');
Route::get('pages/status/{id}/{status}', 'PageController@status');

Route::post('pages/upload-image', 'PageController@uploadImage')->name('ckeditor.upload');
/*---------------Pages Routes End---------------*/

/*---------------Settings Routes Start---------------*/
Route::get('settings', 'SettingController@index');
Route::post('settings-update', 'SettingController@update');
/*---------------Settings Routes End---------------*/

/*--------------- Rating---------------*/
Route::get('ratings', 'RatingController@index');

/*--------------- Orders---------------*/
Route::get('orders', 'OrderController@index');
Route::get('orders/view/{id}', 'OrderController@view');
Route::get('returned-orders', 'OrderController@returned_orders')->name('returned-orders');
Route::get('orders/view-returned-products/{id}', 'OrderController@view_returned_products')->name('view-returned-products');
Route::get('orders/accept-return-request/{id}', 'OrderController@accept_return_request')->name('accept-return-request');
/*--------------- Orders End---------------*/

/*--------------- customers---------------*/
Route::get('customers', 'CustomersController@index');
Route::get('customers/view/{id}', 'CustomersController@view');
Route::get('customers/orders/{id}', 'CustomersController@orders');
Route::get('customers/order-view/{user_id}/{order_id}', 'CustomersController@orderView');
/*--------------- customers End---------------*/

/*--------------- Importc CSV---------------*/
Route::get('importcsv', 'ImportcsvController@index');
Route::post('importcsv/add', 'ImportcsvController@add');
/*--------------- Import CSV End---------------*/

// Feedbacks 
Route::get('feedbacks', 'FeedbacksController@index')->name('feedbacks-index');
Route::get('feedbacks/view/{id}', 'FeedbacksController@view')->name('feedback-view');
Route::get('feedbacks/status/{id}/{status}', 'FeedbacksController@status')->name('feedback-status');
Route::get('feedbacks/delete/{id}', 'FeedbacksController@delete')->name('feedback-delete');
