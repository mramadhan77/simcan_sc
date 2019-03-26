<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::group(['prefix' => '/xml'], function() {
//     Route::any('/getUnit',  'KirimXmlController@getUnit');
//     Route::any('/getProgram',  'KirimXmlController@getProgram');
//     Route::any('/getUrusan',  'KirimXmlController@getUrusan');
//     Route::any('/getUrusan',  'KirimXmlController@getUrusan');
//     Route::any('/getMisiRenstra',  'KirimXmlController@getMisiRenstra');
    
//     Route::any('/getUnitx',  'Api\KirimXmlController@getUnitx');
// });

Route::group(['prefix' => '/'], function() {
    Route::any('/getUnitx',  'Api\KirimXmlFinalController@getUnitx');
    Route::any('/getItemSSH',  'Api\KirimXmlFinalController@getItemSSH');
    Route::any('/getRefRek',  'Api\KirimXmlFinalController@getRefRek');
    Route::any('/getRefRek1',  'Api\KirimXmlFinalController@getRefRek1');
    Route::any('/getRefRek2',  'Api\KirimXmlFinalController@getRefRek2');
    Route::any('/getRefRek3',  'Api\KirimXmlFinalController@getRefRek3');
    Route::any('/getRefRek4',  'Api\KirimXmlFinalController@getRefRek4');
    Route::any('/getRefRek5',  'Api\KirimXmlFinalController@getRefRek5');
	Route::any('/getTaBelanja/{unit}',  'Api\KirimXmlFinalController@getTaBelanja');
	Route::any('/getTaBelanjaItem',  'Api\KirimXmlFinalController@getTaBelanjaItem');
	Route::any('/getTaPendapatan',  'Api\KirimXmlFinalController@getTaPendapatan');
	Route::any('/getTaPendapatanRinc',  'Api\KirimXmlFinalController@getTaPendapatanRinc');
	Route::any('/getTaPembiayaan',  'Api\KirimXmlFinalController@getTaPembiayaan');
	Route::any('/getRenstra',  'Api\KirimXmlFinalController@getRenstra');
	Route::any('/getTaSubUnit',  'Api\KirimXmlFinalController@getTaSubUnit');
	Route::any('/getTaMisi',  'Api\KirimXmlFinalController@getTaMisi');
	Route::any('/getTaTujuan',  'Api\KirimXmlFinalController@getTaTujuan');
	Route::any('/getTaSasaran',  'Api\KirimXmlFinalController@getTaSasaran');
	Route::any('/getTaProgram',  'Api\KirimXmlFinalController@getTaProgram');
	Route::any('/getTaKegiatan',  'Api\KirimXmlFinalController@getTaKegiatan');
	Route::any('/getUnit',  'Api\KirimXmlFinalController@getUnit');
    Route::any('/getProgram',  'Api\KirimXmlFinalController@getProgram');
    Route::any('/getUrusan',  'Api\KirimXmlFinalController@getUrusan');
    Route::any('/cekRefRek5',  'Api\KirimXmlFinalController@cekRefRek5');
});