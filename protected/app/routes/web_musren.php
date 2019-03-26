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

//POKIR DEWAN
Route::group(['prefix' => 'pokir', 'middleware' => ['auth', 'menu:503']], function () {
    Route::get('/','TrxPokirController@index');
    Route::get('/getDesa/{id_kecamatan}','TrxPokirController@getDesa');
    Route::get('/getDesaAll','TrxPokirController@getDesaAll');
    Route::get('/getData/{id_tahun}','TrxPokirController@getData');
    Route::get('/getUsulanPokir/{id_pokir}','TrxPokirController@getUsulanPokir');
    Route::get('/getLokasiPokir/{id_usulan}','TrxPokirController@getLokasiPokir');

    Route::any('/getNoUsulan/{id_pokir}','TrxPokirController@getNoUsulan');
    Route::any('/addIdentitas','TrxPokirController@addIdentitas');
    Route::any('/editIdentitas','TrxPokirController@editIdentitas');
    Route::any('/hapusIdentitas','TrxPokirController@hapusIdentitas');

    Route::any('/addUsulan','TrxPokirController@addUsulan');
    Route::any('/editUsulan','TrxPokirController@editUsulan');
    Route::any('/hapusUsulan','TrxPokirController@hapusUsulan');

    Route::any('/addLokasi','TrxPokirController@addLokasi');
    Route::any('/editLokasi','TrxPokirController@editLokasi');
    Route::any('/hapusLokasi','TrxPokirController@hapusLokasi');

    Route::any('/verpokir','TrxTLPokirController@index');
    Route::get('/getDataPokir','TrxTLPokirController@getDataPokir');
    Route::any('/importData','TrxTLPokirController@importData');    
    Route::any('/unloadData','TrxTLPokirController@unloadData');
    Route::any('/Posting','TrxTLPokirController@Posting');
    Route::get('/getDataTL/{id_tahun}','TrxTLPokirController@getData');
    Route::any('/editTlUsulan','TrxTLPokirController@editUsulan');

    Route::any('/tlpokir','TrxPokirUnitController@index');
    Route::any('/getUnit','TrxPokirUnitController@getUnit');
    Route::any('/importPokirUnit','TrxPokirUnitController@importData');   
    Route::any('/unloadDataUnit','TrxPokirUnitController@unloadData');
    Route::any('/PostingUnit','TrxPokirUnitController@Posting');
    Route::get('/getDataUnit/{id_tahun}/{id_unit}','TrxPokirUnitController@getData');
    Route::get('/getDataAktivitas/{id_tahun}/{id_unit}','TrxPokirUnitController@getDataAktivitas');
    Route::any('/editPokirUnit','TrxPokirUnitController@editPokirUnit');

});

//PRA MUSRENBANG PROVINSI
Route::group(['prefix' => 'pramusren', 'middleware' => ['auth', 'menu:503']], function () {
    Route::get('/','TrxUsulanKabController@index');
    Route::get('/getData/{id_tahun}','TrxUsulanKabController@getData');
    Route::get('/getDataLokasi/{id_usulan}','TrxUsulanKabController@getDataLokasi');
    Route::any('/getKabupaten','TrxUsulanKabController@getKabupaten');

    Route::any('/getNoUsulan','TrxUsulanKabController@getNoUsulan');

    Route::any('/addUsulan','TrxUsulanKabController@addUsulan');
    Route::any('/editUsulan','TrxUsulanKabController@editUsulan');
    Route::any('/hapusUsulan','TrxUsulanKabController@hapusUsulan');

    Route::any('/addLokasi','TrxUsulanKabController@addLokasi');
    Route::any('/editLokasi','TrxPokirController@editLokasi');
    Route::any('/hapusLokasi','TrxUsulanKabController@hapusLokasi');

});

//MUSRENBANG-RKPD
Route::group(['prefix' => 'musrenrkpd', 'middleware' => ['auth', 'menu:60']], function() {
    Route::get('/', 'TrxMusrenbangRkpdController@index');
    Route::get('/getDataRekap','TrxMusrenbangRkpdController@getDataRekap');
    Route::get('/getRefUnit','TrxMusrenbangRkpdController@getRefUnit');
    Route::any('/loadData', 'TrxMusrenbangRkpdController@loadData')->middleware('auth', 'menu:403');  
    Route::any('/blangsung', 'TrxMusrenbangRkpdController@blangsung');
    Route::any('/getSelectProgram/{id_tahun}', 'TrxMusrenbangRkpdController@getSelectProgram');

    Route::any('/getDataDokumen', 'TrxMusrenbangRkpdController@getDataDokumen');
    Route::any('/getDataPerencana', 'TrxMusrenbangRkpdController@getDataPerencana');
    Route::any('/addDokumen', 'TrxMusrenbangRkpdController@addDokumen');
    Route::any('/editDokumen', 'TrxMusrenbangRkpdController@editDokumen');
    Route::any('/hapusDokumen', 'TrxMusrenbangRkpdController@hapusDokumen');

    Route::post('/importData','TrxMusrenbangRkpdController@importData');
    Route::post('/unLoadData','TrxMusrenbangRkpdController@unLoadData');

    Route::any('/dokumen', 'TrxMusrenbangRkpdController@doku');
    Route::any('/getDataDokumen', 'TrxMusrenbangRkpdController@getDataDokumen');  
    Route::any('/getDataPerencana', 'TrxMusrenbangRkpdController@getDataPerencana'); 
    Route::any('/addDokumen', 'TrxMusrenbangRkpdController@addDokumen');
    Route::any('/editDokumen', 'TrxMusrenbangRkpdController@editDokumen');
    Route::any('/hapusDokumen', 'TrxMusrenbangRkpdController@hapusDokumen');
    Route::any('/postDokumen', 'TrxMusrenbangRkpdController@postDokumen'); 

    Route::get('/getData','TrxMusrenbangRkpdEditController@getData');
    Route::get('/getRefIndikator', 'TrxMusrenbangRkpdEditController@getRefIndikator');
    Route::get('/getRefUnit', 'TrxMusrenbangRkpdEditController@getRefUnit');
    Route::get('/getRefProgramRPJMD', 'TrxMusrenbangRkpdEditController@getRefProgramRPJMD');
    Route::get('/getUrusan', 'TrxMusrenbangRkpdEditController@getUrusan');
    Route::get('/getBidang/{id_urusan}', 'TrxMusrenbangRkpdEditController@getBidang');

    Route::get('/getIndikatorRKPD/{id_rkpd}','TrxMusrenbangRkpdEditController@getIndikatorRKPD');
    Route::get('/getUrusanRKPD/{id_rkpd}','TrxMusrenbangRkpdEditController@getUrusanRKPD');
    Route::get('/getPelaksanaRKPD/{id_rkpd}/{id_urusan}','TrxMusrenbangRkpdEditController@getPelaksanaRKPD');

    Route::post('/addProgramRkpd','TrxMusrenbangRkpdEditController@addProgramRkpd');
    Route::post('/editProgramRKPD','TrxMusrenbangRkpdEditController@editProgramRKPD');
    Route::post('/postProgram','TrxMusrenbangRkpdEditController@postProgram');
    Route::post('/hapusProgramRKPD','TrxMusrenbangRkpdEditController@hapusProgramRKPD');

    Route::post('/addIndikatorRKPD','TrxMusrenbangRkpdEditController@addIndikatorRKPD');
    Route::post('/editIndikatorRKPD','TrxMusrenbangRkpdEditController@editIndikatorRKPD');
    Route::post('/postIndikatorRKPD','TrxMusrenbangRkpdEditController@postIndikatorRKPD');
    Route::post('/hapusIndikatorRKPD','TrxMusrenbangRkpdEditController@hapusIndikatorRKPD');

    Route::post('/addUrusanRKPD','TrxMusrenbangRkpdEditController@addUrusanRKPD');
    Route::post('/hapusUrusanRKPD','TrxMusrenbangRkpdEditController@hapusUrusanRKPD');

    Route::post('/addPelaksanaRKPD','TrxMusrenbangRkpdEditController@addPelaksanaRKPD');
    Route::post('/editPelaksanaRKPD','TrxMusrenbangRkpdEditController@editPelaksanaRKPD');
    Route::post('/postPelaksanaRKPD','TrxMusrenbangRkpdEditController@postPelaksanaRKPD');
    Route::post('/hapusPelaksanaRKPD','TrxMusrenbangRkpdEditController@hapusPelaksanaRKPD');
    Route::post('/PostingPelaksanaRKPD','TrxMusrenbangRkpdEditController@PostingPelaksanaRKPD');

    Route::any('/sesuai', 'TrxMusrenbangRkpdController@sesuai');
    Route::get('/getProgramRkpd/{tahun}/{id_unit}','TrxMusrenbangRkpdSesuaiController@getProgramRkpd');
    Route::get('/getChildBidang/{id_unit}/{id_rkpd}','TrxMusrenbangRkpdSesuaiController@getChildBidang');

    Route::get('/getProgramRenja/{id_unit}/{id_pelaksana}','TrxMusrenbangRkpdSesuaiController@getProgramRenja');
    Route::get('/getIndikatorRenja/{id_program}','TrxMusrenbangRkpdSesuaiController@getIndikatorRenja');
    Route::get('/getKegiatanRenja/{id_program}','TrxMusrenbangRkpdSesuaiController@getKegiatanRenja');
    Route::get('/getIndikatorKegiatan/{id_kegiatan}','TrxMusrenbangRkpdSesuaiController@getIndikatorKegiatan');
    Route::get('/getPelaksanaAktivitas/{id_kegiatan}','TrxMusrenbangRkpdSesuaiController@getPelaksanaAktivitas');
    Route::get('/getAktivitas/{id_pelaksana}','TrxMusrenbangRkpdSesuaiController@getAktivitas');
    Route::get('/getLokasiAktivitas/{id_aktivitas}','TrxMusrenbangRkpdSesuaiController@getLokasiAktivitas');
    Route::get('/getBelanja/{id_aktivitas}','TrxMusrenbangRkpdSesuaiController@getBelanja');

    Route::post('/AddProgRenja','TrxMusrenbangRkpdSesuaiController@AddProgRenja');
    Route::post('/editProgRenja','TrxMusrenbangRkpdSesuaiController@editProgRenja');
    Route::post('/hapusProgRenja','TrxMusrenbangRkpdSesuaiController@hapusProgRenja');
    Route::post('/postProgRenja','TrxMusrenbangRkpdSesuaiController@postProgRenja');

    Route::post('/addIndikatorProg','TrxMusrenbangRkpdSesuaiController@addIndikatorProg');
    Route::post('/editIndikatorProg','TrxMusrenbangRkpdSesuaiController@editIndikatorProg');
    Route::post('/postIndikatorProg','TrxMusrenbangRkpdSesuaiController@postIndikatorProg');    
    Route::post('/delIndikatorProg','TrxMusrenbangRkpdSesuaiController@delIndikatorProg');

    Route::post('/addKegRenja','TrxMusrenbangRkpdSesuaiController@addKegRenja');
    Route::post('/editKegRenja','TrxMusrenbangRkpdSesuaiController@editKegRenja');
    Route::post('/hapusKegRenja','TrxMusrenbangRkpdSesuaiController@hapusKegRenja');
    Route::post('/postKegRenja','TrxMusrenbangRkpdSesuaiController@postKegRenja');

    Route::post('/addIndikatorKeg','TrxMusrenbangRkpdSesuaiController@addIndikatorKeg');
    Route::post('/editIndikatorKeg','TrxMusrenbangRkpdSesuaiController@editIndikatorKeg');
    Route::post('/postIndikatorKeg','TrxMusrenbangRkpdSesuaiController@postIndikatorKeg');    
    Route::post('/delIndikatorKeg','TrxMusrenbangRkpdSesuaiController@delIndikatorKeg');

    Route::post('/addPelaksana','TrxMusrenbangRkpdSesuaiController@addPelaksana');
    Route::post('/editPelaksana','TrxMusrenbangRkpdSesuaiController@editPelaksana');
    Route::post('/hapusPelaksana','TrxMusrenbangRkpdSesuaiController@hapusPelaksana');

    Route::post('/addAktivitas','TrxMusrenbangRkpdSesuaiController@addAktivitas');
    Route::post('/editAktivitas','TrxMusrenbangRkpdSesuaiController@editAktivitas');
    Route::post('/postAktivitas','TrxMusrenbangRkpdSesuaiController@postAktivitas');    
    Route::post('/hapusAktivitas','TrxMusrenbangRkpdSesuaiController@hapusAktivitas');

    Route::post('/addLokasi','TrxMusrenbangRkpdSesuaiController@addLokasi');
    Route::post('/editLokasi','TrxMusrenbangRkpdSesuaiController@editLokasi');
    Route::post('/hapusLokasi','TrxMusrenbangRkpdSesuaiController@hapusLokasi');

    Route::post('/getHitungASB','TrxMusrenbangRkpdSesuaiController@getHitungASB');
    Route::post('/unloadASB','TrxMusrenbangRkpdSesuaiController@unloadASB');

    Route::post('/addBelanja','TrxMusrenbangRkpdSesuaiController@addBelanja');
    Route::post('/editBelanja','TrxMusrenbangRkpdSesuaiController@editBelanja');
    Route::post('/hapusBelanja','TrxMusrenbangRkpdSesuaiController@hapusBelanja');
    Route::get('/getLokasiCopy/{id_unit}', 'TrxMusrenbangRkpdSesuaiController@getLokasiCopy');   
});

//USULAN RW
Route::group(['prefix' => 'musrenrw', 'middleware' => ['auth', 'menu:601']], function() {
    Route::get('/', 'TrxMusrenbangRwController@index');
    Route::get('/getData/{id_desa}', 'TrxMusrenbangRwController@getData');
    Route::get('/getDataASB', 'TrxMusrenbangRwController@getDataASB');
    Route::post('/addMusrenbangRw', 'TrxMusrenbangRwController@addMusrendesRw');
    Route::post('/editMusrenbangRw', 'TrxMusrenbangRwController@editMusrendesRw');
    Route::post('/hapusMusrenbangRw', 'TrxMusrenbangRwController@hapusMusrendesRw');
    Route::post('/postMusrendesRw', 'TrxMusrenbangRwController@postMusrendesRw');
    Route::get('/getHitungASB/{id_asb}/{id_zona}/{vol1}/{vol2}', 'TrxMusrenbangRwController@getHitungASB');  

});

//MUSRENBANG DESA
Route::group(['prefix' => 'musrendes', 'middleware' => ['auth', 'menu:603']], function() {
    Route::get('/', 'TrxMusrenbangDesController@index');
    Route::get('/getData/{id_desa}', 'TrxMusrenbangDesController@getData');
    Route::get('/getLokasi/{id_musrendes}', 'TrxMusrenbangDesController@getLokasi');
    Route::post('/ImportDataRW', 'TrxMusrenbangDesController@ImportDataRW');
    Route::post('/unLoadData', 'TrxMusrenbangDesController@unLoadData');

    Route::get('/getDataASB', 'TrxMusrenbangDesController@getDataASB');
    Route::get('/getHitungASB/{id_asb}/{id_zona}/{vol1}/{vol2}', 'TrxMusrenbangDesController@getHitungASB'); 

    Route::post('/addMusrenDesa', 'TrxMusrenbangDesController@addMusrendes');
    Route::post('/editMusrenDesa', 'TrxMusrenbangDesController@editMusrendes');
    Route::post('/hapusMusrenDesa', 'TrxMusrenbangDesController@hapusMusrendes');
    Route::post('/postMusrenDesa', 'TrxMusrenbangDesController@postMusrendes');

    Route::post('/addMusrenDesaLokasi', 'TrxMusrenbangDesController@addMusrendesLokasi');
    Route::post('/editMusrenDesaLokasi', 'TrxMusrenbangDesController@editMusrendesLokasi');
    Route::post('/hapusMusrenDesaLokasi', 'TrxMusrenbangDesController@hapusMusrendesLokasi');

});


//MUSRENBANG KECAMATAN
Route::group(['prefix' => 'musrencam', 'middleware' => ['auth', 'menu:605']], function() {
    Route::get('/', 'TrxMusrenbangCamController@index');
    Route::post('/importData', 'TrxMusrenbangCamController@importData');
    Route::post('/unLoadData', 'TrxMusrenbangCamController@unLoadData');

    Route::get('/getData/{id_kecamatan}', 'TrxMusrenbangCamController@getData');
    Route::get('/getLokasiData/{id_musrencam}', 'TrxMusrenbangCamController@getLokasiData');

    Route::get('/getLoadData/{id_kecamatan}', 'TrxMusrenbangCamController@getLoadData');
    Route::get('/getLokasi/{id_musrencam}', 'TrxMusrenbangCamController@getLokasi');

    Route::get('/loadData', 'TrxMusrenbangCamController@loadData');
    Route::get('/postingData', 'TrxMusrenbangCamController@postingData');

    Route::post('/addMusrenCamLokasi', 'TrxMusrenbangCamController@addMusrenCamLokasi');
    Route::post('/editMusrenCamLokasi', 'TrxMusrenbangCamController@editMusrenCamLokasi');
    Route::post('/hapusMusrenCamLokasi', 'TrxMusrenbangCamController@hapusMusrenCamLokasi');

    Route::post('/addMusrenCam', 'TrxMusrenbangCamController@addMusrenCam');
    Route::post('/editMusrenCam', 'TrxMusrenbangCamController@editMusrenCam');
    Route::post('/postMusrenCam', 'TrxMusrenbangCamController@postMusrenCam');
    Route::post('/hapusMusrenCam', 'TrxMusrenbangCamController@hapusMusrenCam');
});
