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

//PDRB
Route::group(['prefix' => '/pdrb', 'middleware' => ['auth', 'menu:109']], function() {
    Route::any('/', 'RefPDRBController@index');
    Route::any('/getListpdrb', 'RefPDRBController@getListpdrb');
    Route::any('/getTahunpdrb', 'RefPDRBController@getTahunpdrb');
    Route::any('/getKecamatanpdrb', 'RefPDRBController@getKecamatanpdrb');
    Route::any('/getSektorpdrb/{tahun}/{kecamatan}', 'RefPDRBController@getSektorpdrb');
    Route::any('/addPdrb', 'RefPDRBController@addpdrb');
    Route::any('/getEditpdrb/{id}', 'RefPDRBController@getEditpdrb');
    Route::any('/editPdrb', 'RefPDRBController@editpdrb');
    Route::any('/hapusPdrb', 'RefPDRBController@hapuspdrb');
    
});
    //PDRB-HB
    Route::group(['prefix' => '/pdrbhb', 'middleware' => ['auth', 'menu:109']], function() {
        Route::any('/', 'RefPDRBHBController@index');
        Route::any('/getListpdrbhb', 'RefPDRBHBController@getListpdrbhb');
        Route::any('/getTahunpdrbhb', 'RefPDRBHBController@getTahunpdrbhb');
        Route::any('/getKecamatanpdrbhb', 'RefPDRBHBController@getKecamatanpdrbhb');
        Route::any('/getSektorpdrbhb/{tahun}/{kecamatan}', 'RefPDRBHBController@getSektorpdrbhb');
        Route::any('/addPdrbhb', 'RefPDRBHBController@addpdrbhb');
        Route::any('/getEditpdrbhb/{id}', 'RefPDRBHBController@getEditpdrbhb');
        Route::any('/editPdrbhb', 'RefPDRBHBController@editpdrbhb');
        Route::any('/hapusPdrbhb', 'RefPDRBHBController@hapuspdrbhb');
        
    });
        //AMH
        Route::group(['prefix' => '/amh', 'middleware' => ['auth', 'menu:109']], function() {
            Route::any('/', 'RefAMHController@index');
            Route::any('/getListamh', 'RefAMHController@getListamh');
            Route::any('/getTahunamh', 'RefAMHController@getTahunamh');
            Route::any('/getKecamatanamh', 'RefAMHController@getKecamatanamh');
            Route::any('/getSektoramh/{tahun}/{kecamatan}', 'RefAMHController@getSektoramh');
            Route::any('/addamh', 'RefAMHController@addamh');
            Route::any('/getEditamh/{id}', 'RefAMHController@getEditamh');
            Route::any('/editamh', 'RefAMHController@editamh');
            Route::any('/hapusamh', 'RefAMHController@hapusamh');
            
        });
        
            //RataLamaSekolah
            Route::group(['prefix' => '/ratalamasekolah', 'middleware' => ['auth', 'menu:109']], function() {
                Route::any('/', 'RefRataLamaSekolahController@index');
                Route::any('/getListratalamasekolah', 'RefRataLamaSekolahController@getListratalamasekolah');
                Route::any('/getTahunratalamasekolah', 'RefRataLamaSekolahController@getTahunratalamasekolah');
                Route::any('/getKecamatanratalamasekolah', 'RefRataLamaSekolahController@getKecamatanratalamasekolah');
                Route::any('/getSektorratalamasekolah/{tahun}/{kecamatan}', 'RefRataLamaSekolahController@getSektorratalamasekolah');
                Route::any('/addratalamasekolah', 'RefRataLamaSekolahController@addratalamasekolah');
                Route::any('/getEditratalamasekolah/{id}', 'RefRataLamaSekolahController@getEditratalamasekolah');
                Route::any('/editratalamasekolah', 'RefRataLamaSekolahController@editratalamasekolah');
                Route::any('/hapusratalamasekolah', 'RefRataLamaSekolahController@hapusratalamasekolah');
                
            });
            
                //SeniOR
                Route::group(['prefix' => '/senior', 'middleware' => ['auth', 'menu:109']], function() {
                    Route::any('/', 'RefSeniORController@index');
                    Route::any('/getListsenior', 'RefSeniORController@getListsenior');
                    Route::any('/getTahunsenior', 'RefSeniORController@getTahunsenior');
                    Route::any('/getKecamatansenior', 'RefSeniORController@getKecamatansenior');
                    Route::any('/getSektorsenior/{tahun}/{kecamatan}', 'RefSeniORController@getSektorsenior');
                    Route::any('/addsenior', 'RefSeniORController@addsenior');
                    Route::any('/getEditsenior/{id}', 'RefSeniORController@getEditsenior');
                    Route::any('/editsenior', 'RefSeniORController@editsenior');
                    Route::any('/hapussenior', 'RefSeniORController@hapussenior');
                    
                });
                    //aps
                    Route::group(['prefix' => '/aps', 'middleware' => ['auth', 'menu:109']], function() {
                        Route::any('/', 'RefAPSController@index');
                        Route::any('/getListaps', 'RefAPSController@getListaps');
                        Route::any('/getTahunaps', 'RefAPSController@getTahunaps');
                        Route::any('/getTingkataps', 'RefAPSController@getTingkataps');
                        Route::any('/getKecamatanaps', 'RefAPSController@getKecamatanaps');
                        Route::any('/getSektoraps/{tahun}/{kecamatan}/{tingkat}', 'RefAPSController@getSektoraps');
                        Route::any('/addaps', 'RefAPSController@addaps');
                        Route::any('/getEditaps/{id}', 'RefAPSController@getEditaps');
                        Route::any('/editaps', 'RefAPSController@editaps');
                        Route::any('/hapusaps', 'RefAPSController@hapusaps');
                        
                    });
                        //kts
                        Route::group(['prefix' => '/kts', 'middleware' => ['auth', 'menu:109']], function() {
                            Route::any('/', 'RefKTSController@index');
                            Route::any('/getListkts', 'RefKTSController@getListkts');
                            Route::any('/getTahunkts', 'RefKTSController@getTahunkts');
                            Route::any('/getTingkatkts', 'RefKTSController@getTingkatkts');
                            Route::any('/getKecamatankts', 'RefKTSController@getKecamatankts');
                            Route::any('/getSektorkts/{tahun}/{kecamatan}/{tingkat}', 'RefKTSController@getSektorkts');
                            Route::any('/addkts', 'RefKTSController@addkts');
                            Route::any('/getEditkts/{id}', 'RefKTSController@getEditkts');
                            Route::any('/editkts', 'RefKTSController@editkts');
                            Route::any('/hapuskts', 'RefKTSController@hapuskts');
                            
                        });
                            //gurumurid
                            Route::group(['prefix' => '/gurumurid', 'middleware' => ['auth', 'menu:109']], function() {
                                Route::any('/', 'RefGuruMuridController@index');
                                Route::any('/getListgurumurid', 'RefGuruMuridController@getListgurumurid');
                                Route::any('/getTahungurumurid', 'RefGuruMuridController@getTahungurumurid');
                                Route::any('/getTingkatgurumurid', 'RefGuruMuridController@getTingkatgurumurid');
                                Route::any('/getKecamatangurumurid', 'RefGuruMuridController@getKecamatangurumurid');
                                Route::any('/getSektorgurumurid/{tahun}/{kecamatan}/{tingkat}', 'RefGuruMuridController@getSektorgurumurid');
                                Route::any('/addgurumurid', 'RefGuruMuridController@addgurumurid');
                                Route::any('/getEditgurumurid/{id}', 'RefGuruMuridController@getEditgurumurid');
                                Route::any('/editgurumurid', 'RefGuruMuridController@editgurumurid');
                                Route::any('/hapusgurumurid', 'RefGuruMuridController@hapusgurumurid');
                                
                            });
                                //investor
                                Route::group(['prefix' => '/investor', 'middleware' => ['auth', 'menu:109']], function() {
                                    Route::any('/', 'RefInvestorController@index');
                                    Route::any('/getListinvestor', 'RefInvestorController@getListinvestor');
                                    Route::any('/getTahuninvestor', 'RefInvestorController@getTahuninvestor');
                                    Route::any('/getTingkatinvestor', 'RefInvestorController@getTingkatinvestor');
                                    Route::any('/getKecamataninvestor', 'RefInvestorController@getKecamataninvestor');
                                    Route::any('/getSektorinvestor/{tahun}/{kecamatan}', 'RefInvestorController@getSektorinvestor');
                                    Route::any('/addinvestor', 'RefInvestorController@addinvestor');
                                    Route::any('/getEditinvestor/{id}', 'RefInvestorController@getEditinvestor');
                                    Route::any('/editinvestor', 'RefInvestorController@editinvestor');
                                    Route::any('/hapusinvestor', 'RefInvestorController@hapusinvestor');
                                    
                                });
                                    //investasi
                                    Route::group(['prefix' => '/investasi', 'middleware' => ['auth', 'menu:109']], function() {
                                        Route::any('/', 'RefInvestasiController@index');
                                        Route::any('/getListinvestasi', 'RefInvestasiController@getListinvestasi');
                                        Route::any('/getTahuninvestasi', 'RefInvestasiController@getTahuninvestasi');
                                        Route::any('/getTingkatinvestasi', 'RefInvestasiController@getTingkatinvestasi');
                                        Route::any('/getKecamataninvestasi', 'RefInvestasiController@getKecamataninvestasi');
                                        Route::any('/getSektorinvestasi/{tahun}/{kecamatan}', 'RefInvestasiController@getSektorinvestasi');
                                        Route::any('/addinvestasi', 'RefInvestasiController@addinvestasi');
                                        Route::any('/getEditinvestasi/{id}', 'RefInvestasiController@getEditinvestasi');
                                        Route::any('/editinvestasi', 'RefInvestasiController@editinvestasi');
                                        Route::any('/hapusinvestasi', 'RefInvestasiController@hapusinvestasi');                                        
                                    });
