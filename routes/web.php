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

Route::get('/', function () {
    return redirect('/home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/home/create', 'HomeController@create')->name('create');
Route::post('/home/edit', 'HomeController@edit')->name('edit');
Route::post('/home/password', 'HomeController@password')->name('password');
Route::get('/home/export', 'HomeController@export')->name('export');