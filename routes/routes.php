<?php
    /***
     * Update needed to extract wanted routes from config ? done
     */
    Route::group(['middleware' => ['web']], function(){

        Route::get(
            'login/{provider}',
            'Arcyfa\EveSso\EveSsoController@redirectToProvider'
        )->where('provider', 'esi');
        Route::get(
            'login/{provider}/callback',
            'Arcyfa\EveSso\EveSsoController@handleProviderCallback'
        )->where('provider', 'esi');

        route::get(
            'success',
            'Arcyfa\EveSso\EveSsoController@success'
        )->name('success');

        route::get(
            'new_user',
            'Arcyfa\EveSso\EveSsoController@new_user'
        )->name('new_user');
    });
