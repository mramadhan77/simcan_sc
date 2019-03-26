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

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::group(['prefix' => 'transfer', 'middleware' => ['auth', 'menu:702']], function () {
    Route::get('/hapusdata', 'TransferController@hapusdataindex');
    Route::get('/proseshapusdataumum', 'TransferController@proseshapusdataumum');    
    
    Route::get('/transferurbid', 'TransferController@trfurbidindex');
    Route::get('/prosestrfApiurbid', 'TransferController@prosestrfApiurbid');    
    
    Route::get('/transferunit', 'TransferController@trfunitindex');
    Route::get('/prosestrfApiunit', 'TransferController@prosestrfApiunit');
    
    Route::get('/transferrekening', 'TransferController@trfrekeningindex');
    Route::get('/cekrefrek5', 'TransferController@getApiRefRek5');
    Route::get('/prosetrfrefrek5', 'TransferController@prosestrfApiRefRek5');
    
    Route::get('/transferprogram', 'TransferController@trfprogramindex');
    Route::get('/prosestrfApiprogram', 'TransferController@prosestrfApirogram');
    
    Route::get('/transferrenstra', 'TransferController@trfrenstraindex');
    Route::get('/prosetrfrenstra', 'TransferController@prosestrfApiRenstra');
    
    Route::get('/transferpendapatan', 'TransferController@trfpendapatanindex');
    Route::get('/prosetrfpendapatan', 'TransferController@prosestrfApiPendapatan');
    
    Route::get('/transferbelanja', 'TransferController@trfbelanjaindex');
    Route::get('/prosetrfbelanja', 'TransferController@prosestrfApiBelanja');
	
	Route::get('/getApiRealisasi', 'TransferController@getApiRealisasiindex');
	Route::get('/getDataApiRealisasi', 'TransferController@getApiRealisasi');
});