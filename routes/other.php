<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'SiteController@index');
Route::get('/oauth', 'SiteController@oauth');
Route::get('/twitterOAuth', 'SiteController@twitterOAuth')->name('twitterOAuth');

Route::get('/test/{comment}', 'SiteController@emailTest');

Route::get('/email/view/{token}', 'EmailController@view');

Route::get('/emailTest/example', 'EmailTestController@example');
Route::get('/emailTest/resetPassword', 'EmailTestController@resetPassword');
Route::get('/emailTest/confirmEmail', 'EmailTestController@confirmEmail');
Route::get('/emailTest/balanceChanged', 'EmailTestController@balanceChanged')->name('admin.emails.balanceChanged');
Route::get('/emailTest/usersBulkImport', 'EmailTestController@usersBulkImport');
Route::get('/test', 'SiteController@test');

Route::group(['prefix' => 'api/docs'], function () {
    Route::get('/ru', 'ApiDocsController@docsRu');
    Route::get('/en', 'ApiDocsController@docsEn');
    Route::get('/yaml/ru', 'ApiDocsController@specificationRu')->name('api.specification.ru');
    Route::get('/yaml/en', 'ApiDocsController@specificationEn')->name('api.specification.en');
});
