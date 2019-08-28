<?php

\Route::group([
    'middleware' => ['auth:api-admin', 'api', 'set-language-from-partner', 'role:partner-or-cashier'],
    'prefix' => 'api',
], function ($router) {
    Route::post('/insales/orderWebhook', "Api\InsalesController@webhook")->middleware('only-partners')->name('insales.orderWebhook');
    Route::post('/shopify/orderWebhook', "Api\ShopifyController@webhook")->middleware('only-partners')->name('shopify.orderWebhook');
    Route::post('/eventbrite/orderWebhook', "Api\EventbriteController@webhook")->middleware('only-partners')->name('eventbrite.orderWebhook');
    Route::post('/bitrix/storeOrder', "Api\BitrixController@storeOrder")->middleware('only-partners')->name('eventbrite.orderWebhook');
    Route::post('/partner/createFromGiftd', "Api\PartnerController@createFromGiftd");
    Route::post('/partner/setPassword', "Api\PartnerController@setPassword");
    Route::any('/partner/getSignupBonus', "Api\PartnerController@getSignupBonus");
    Route::any('/partner/changeLanguage', "Api\PartnerController@changeLanguage");
    Route::any('/partner/changeCurrency', "Api\PartnerController@changeCurrency");
    Route::post('v1/user.giveBonus', "PublicApi\UserController@giveBonus")->middleware('only-partners')->name('api.public.user.giveBonus');
});
