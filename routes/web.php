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
//
//Route::get('/', function () {
//    return view('auth.login');
//});
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/get_top_user', 'ActivityLogController@getTopUser');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/leader-board', 'HomeController@leaderBoard')->name('home');
Route::get('/explore-character', 'HomeController@exploreCharacter')->name('home');
Route::get('/ontology-update', 'HomeController@ontologyUpdate')->name('home');
Route::get('/shared-character', 'HomeController@sharedCharacter')->name('home');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {

});
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::post('send-mail', 'HomeController@sendMail');