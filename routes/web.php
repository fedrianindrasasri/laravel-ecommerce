<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes();

/*
    jadi ini grouping route, sehingga route didalamnya diawali dengan /administrator
    cth: ./administrator/category atau /administrator/product, dsb
*/
Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function(){
    // route /home kita masukkan kedalam grouping administrator
    Route::get('/home', 'HomeController@index')->name('home');

    // route baru dengan keyword resource agar semua route dibuat kecuali create dan show
    Route::resource('category', 'CategoryController')->except(['create', 'show']);

    Route::resource('product', 'ProductController')->except('show');
    Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.bulk');
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');
});
