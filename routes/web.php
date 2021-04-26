<?php

use Illuminate\Support\Facades\Auth;
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
    return view('auth.login');
});

Auth::routes(['register' => false]);
Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('/invoices', 'InvoicesController');
    Route::resource('/categories', 'SectionsController');
    Route::resource('/products', 'ProductsController');
    Route::resource('/InvoiceAttachments', 'InvoiceAttachmentsController');
    Route::get('/section/{id}', 'InvoicesController@getProducts');
    Route::get('/InvoicesDetails/{id}', 'InvoicesDetailsController@edit');
    Route::get('download/{invoice_number}/{file_name}', 'InvoicesDetailsController@getFile');
    Route::get('view_file/{invoice_number}/{file_name}', 'InvoicesDetailsController@openFile');
    Route::post('delete_file', 'InvoicesDetailsController@destroy')->name('delete_file');
    Route::get('/edit_invoice/{id}', 'InvoicesController@edit');
    Route::get('/{page}', 'AdminController@index');
});




