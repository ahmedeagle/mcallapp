<?php


#### Start Authenticated Routes
Route::group(['middleware' => ['CheckManagerToken:manager-api']], function () {

    Route::prefix('{locale}')->middleware(['setAPILocale'])->where(['locale' => '[a-zA-Z]{2}'])->group(function () {

        ############### Start Visits Routes ##############
        Route::prefix('services')->group(function () {
            Route::post('/', 'ServiceController@index');
            Route::post('store', 'ServiceController@store');
            Route::post('edit', 'ServiceController@edit');
            Route::post('update', 'ServiceController@update');
            Route::post('delete', 'ServiceController@destroy');
        });
        ############## End Visits Routes ##############

        ############### Banners Routes ##############
        Route::prefix('banners')->group(function () {
            Route::post('/', 'BannerController@index');
            Route::post('create', 'BannerController@create');
            Route::post('store', 'BannerController@store');
            Route::post('edit', 'BannerController@edit');
            Route::post('update', 'BannerController@update');
            Route::post('delete', 'BannerController@destroy');
        });
        ############### End Banners Routes ##############

        ############### Banners Routes ##############
        Route::prefix('banners/V2')->group(function () {
            Route::post('/', 'BannersController@index');
            Route::post('create', 'BannersController@create');
            Route::post('store', 'BannersController@store');
            Route::post('edit', 'BannersController@edit');
            Route::post('update', 'BannersController@update');
            Route::post('delete', 'BannersController@destroy');
        });
        ############### End Banners Routes ##############
    });


});

