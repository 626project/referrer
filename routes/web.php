<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/invite/{code}', 'TgBotController@invite')->name('tg_bot.invite');
Route::post('/tg', 'TgBotController@index')->name('tg_bot');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', 'DashboardController@dashboard')->name('dashboard');
    Route::get('/create-link', 'DashboardController@create_link_page')->name('dashboard.link.create.page');
    Route::post('/create-link', 'DashboardController@create_link')->name('dashboard.link.create');
    Route::get('/profile', 'ProfileController@show')->name('profile.show');
    Route::put('/profile', 'ProfileController@edit')->name('profile.edit');
    Route::group(['prefix' => '/links/{id}'], function () {
        Route::get('', 'DashboardController@show_tg_users')->name('dashboard.link.tg.users');
        Route::get('download', 'DashboardController@download')->name('dashboard.link.tg.users.download');
        Route::get('delete', 'DashboardController@delete_link')->name('dashboard.link.delete');
    });
});

Auth::routes();

Route::get('/home', 'DashboardController@index')->name('home');
