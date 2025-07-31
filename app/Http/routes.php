<?php

Route::get('robots.txt', 'PageController@robots')->name('robots');

Route::group(['middleware' => ['redirects']], function() {
    Route::get('/', ['as' => 'main', 'uses' => 'WelcomeController@index']);

        Route::any('news', ['as' => 'news', function () {
        abort(404, 'Страница не найдена');
    }]);

    Route::any('contact', ['as' => 'news', function () {
        abort(404, 'Страница не найдена');
    }]);

    Route::any('{alias}', ['as' => 'default', 'uses' => 'PageController@page'])
        ->where('alias', '([A-Za-z0-9\-\/_]+)');
});
