<?php

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

//RANWAL RKPD
Route::group(['prefix' => 'ranwalrkpd'], function () {
    Route::get('/', 'TrxRanwalRKPDController@index');
    Route::get('/getJmlData', 'TrxRanwalRKPDController@getJmlData');
    Route::get('/loadData', 'TrxRanwalRKPDController@loadData');
    Route::get('/getRePostingData/{tahun}', 'TrxRanwalRKPDController@getRePostingData');
    Route::get('/getDataRekap/{tahun}', 'TrxRanwalRKPDController@getDataRekap');
    Route::POST('/unLoadProgramRkpd', 'TrxRanwalRKPDController@unLoadProgramRkpd');
    Route::get('/getCheck/{id}', 'TrxRanwalRKPDController@getCheck');
    Route::get('/getRefIndikator', 'TrxRanwalRKPDController@getRefIndikator');
    Route::get('/getRefUnit', 'TrxRanwalRKPDController@getRefUnit');
    Route::get('/getRefProgramRPJMD', 'TrxRanwalRKPDController@getRefProgramRPJMD');
    Route::get('/getUrusan', 'TrxRanwalRKPDController@getUrusan');
    Route::get('/getBidang/{id_urusan}', 'TrxRanwalRKPDController@getBidang');

    Route::any('/prosesTransferData', 'TrxRanwalRKPDController@prosesTransferData');
    Route::any('/transferIndikator/{tahun_rkpd}', 'TrxRanwalRKPDController@transferIndikator');
    Route::any('/transferUrusan/{tahun_rkpd}', 'TrxRanwalRKPDController@transferUrusan');
    Route::any('/transferPelaksana/{tahun_rkpd}', 'TrxRanwalRKPDController@transferPelaksana');

    Route::any('/ReprosesTransferData', 'TrxRanwalRKPDController@ReprosesTransferData');
    Route::any('/RetransferIndikator', 'TrxRanwalRKPDController@RetransferIndikator');
    Route::any('/RetransferUrusan', 'TrxRanwalRKPDController@RetransferUrusan');
    Route::any('/RetransferPelaksana', 'TrxRanwalRKPDController@RetransferPelaksana');
    Route::any('/RetransferUpdate', 'TrxRanwalRKPDController@RetransferUpdate');

    Route::any('/Dokumen', 'TrxRanwalRKPDDokController@index');
    Route::any('/getDataDokumen', 'TrxRanwalRKPDDokController@getDataDokumen');
    Route::any('/getDataPerencana', 'TrxRanwalRKPDDokController@getDataPerencana');
    Route::any('/addDokumen', 'TrxRanwalRKPDDokController@addDokumen');
    Route::any('/editDokumen', 'TrxRanwalRKPDDokController@editDokumen');
    Route::any('/hapusDokumen', 'TrxRanwalRKPDDokController@hapusDokumen');
    Route::any('/postDokumen', 'TrxRanwalRKPDDokController@postDokumen');

    Route::group(['prefix' => 'blangsung', 'middleware' => ['auth', 'menu:402']], function() {
        Route::get('/', 'TrxRanwalRKPDController@belanjalangsung');
        Route::get('/getData', 'TrxRanwalRKPDController@getData');
        Route::get('/getDataBtl', 'TrxRanwalRKPDController@getDataBtl');
        Route::get('/getDataDapat', 'TrxRanwalRKPDController@getDataDapat');
        Route::get('/getIndikatorRKPD/{id_rkpd}', 'TrxRanwalRKPDController@getIndikatorRKPD');
        Route::get('/getUrusanRKPD/{id_rkpd}', 'TrxRanwalRKPDController@getUrusanRKPD');
        Route::post('/postProgram', 'TrxRanwalRKPDController@postProgram');
        Route::get('/getPelaksanaRKPD/{id_rkpd}/{id_urusan}', 'TrxRanwalRKPDController@getPelaksanaRKPD');

        Route::post('/addPelaksanaRKPD', 'TrxRanwalRKPDController@addPelaksanaRKPD');
        Route::post('/editPelaksanaRKPD', 'TrxRanwalRKPDController@editPelaksanaRKPD');
        Route::post('/hapusPelaksanaRKPD', 'TrxRanwalRKPDController@hapusPelaksanaRKPD');
        Route::post('/postPelaksanaRKPD', 'TrxRanwalRKPDController@postPelaksanaRKPD');

        Route::post('/tambahUrusanRKPD', 'TrxRanwalRKPDController@addUrusanRKPD');
        Route::post('/hapusUrusanRKPD', 'TrxRanwalRKPDController@hapusUrusanRKPD');
        
        Route::post('/tambahIndikatorRKPD', 'TrxRanwalRKPDController@addIndikatorRKPD');
        Route::post('/editIndikatorRKPD', 'TrxRanwalRKPDController@editIndikatorRKPD');
        Route::post('/hapusIndikatorRKPD', 'TrxRanwalRKPDController@hapusIndikatorRKPD');
        Route::post('/postIndikatorRKPD', 'TrxRanwalRKPDController@postIndikatorRKPD');

        Route::post('/tambahProgramRKPD', 'TrxRanwalRKPDController@addProgramRkpd');
        Route::post('/editProgramRKPD', 'TrxRanwalRKPDController@editProgramRKPD');
        Route::post('/hapusProgramRKPD', 'TrxRanwalRKPDController@hapusProgramRKPD');
        
    });


    // btl
    Route::group(['prefix' => 'btl', 'middleware' => ['auth', 'menu:402']], function() {
        Route::get('/', 'TrxRanwalRKPDController@tidaklangsung');
    });

    // pdt
    Route::group(['prefix' => 'pdt', 'middleware' => ['auth', 'menu:402']], function() {
    	Route::get('/', 'TrxRanwalRKPDController@dapat');
    });
});


//RANCANGAN RKPD
Route::group(['prefix' => 'rancanganrkpd', 'middleware' => ['auth', 'menu:40']], function () {  
    Route::get('/', 'TrxRancangRKPDController@index');
    Route::get('/getDataRekap','TrxRancangRKPDController@getDataRekap');
    Route::get('/getRefUnit','TrxRancangRKPDController@getRefUnit');
    Route::any('/loadData', 'TrxRancangRKPDController@loadData')->middleware('auth', 'menu:403');  
    Route::any('/blangsung', 'TrxRancangRKPDController@blangsung');
    Route::any('/getSelectProgram/{id_tahun}', 'TrxRancangRKPDController@getSelectProgram');

    Route::any('/getDataDokumen', 'TrxRancangRKPDController@getDataDokumen');
    Route::any('/getDataPerencana', 'TrxRancangRKPDController@getDataPerencana');
    Route::any('/addDokumen', 'TrxRancangRKPDController@addDokumen');
    Route::any('/editDokumen', 'TrxRancangRKPDController@editDokumen');
    Route::any('/hapusDokumen', 'TrxRancangRKPDController@hapusDokumen');

    Route::post('/importData','TrxRancangRKPDController@importData');
    Route::post('/unLoadData','TrxRancangRKPDController@unLoadData');
    Route::any('/getLoop','TrxRancangRKPDEditController@getLoop');

    Route::get('/getData','TrxRancangRKPDEditController@getData');
    Route::get('/getRefIndikator', 'TrxRancangRKPDEditController@getRefIndikator');
    Route::get('/getRefUnit', 'TrxRancangRKPDEditController@getRefUnit');
    Route::get('/getRefProgramRPJMD', 'TrxRancangRKPDEditController@getRefProgramRPJMD');
    Route::get('/getUrusan', 'TrxRancangRKPDEditController@getUrusan');
    Route::get('/getBidang/{id_urusan}', 'TrxRancangRKPDEditController@getBidang');

    Route::get('/getIndikatorRKPD/{id_rkpd}','TrxRancangRKPDEditController@getIndikatorRKPD');
    Route::get('/getUrusanRKPD/{id_rkpd}','TrxRancangRKPDEditController@getUrusanRKPD');
    Route::get('/getPelaksanaRKPD/{id_rkpd}/{id_urusan}','TrxRancangRKPDEditController@getPelaksanaRKPD');

    Route::post('/addProgramRkpd','TrxRancangRKPDEditController@addProgramRkpd');
    Route::post('/editProgramRKPD','TrxRancangRKPDEditController@editProgramRKPD');
    Route::post('/postProgram','TrxRancangRKPDEditController@postProgram');
    Route::post('/hapusProgramRKPD','TrxRancangRKPDEditController@hapusProgramRKPD');

    Route::post('/addIndikatorRKPD','TrxRancangRKPDEditController@addIndikatorRKPD');
    Route::post('/editIndikatorRKPD','TrxRancangRKPDEditController@editIndikatorRKPD');
    Route::post('/postIndikatorRKPD','TrxRancangRKPDEditController@postIndikatorRKPD');
    Route::post('/hapusIndikatorRKPD','TrxRancangRKPDEditController@hapusIndikatorRKPD');

    Route::post('/addUrusanRKPD','TrxRancangRKPDEditController@addUrusanRKPD');
    Route::post('/hapusUrusanRKPD','TrxRancangRKPDEditController@hapusUrusanRKPD');

    Route::post('/addPelaksanaRKPD','TrxRancangRKPDEditController@addPelaksanaRKPD');
    Route::post('/editPelaksanaRKPD','TrxRancangRKPDEditController@editPelaksanaRKPD');
    Route::post('/postPelaksanaRKPD','TrxRancangRKPDEditController@postPelaksanaRKPD');
    Route::post('/hapusPelaksanaRKPD','TrxRancangRKPDEditController@hapusPelaksanaRKPD');
    Route::post('/PostingPelaksanaRKPD','TrxRancangRKPDEditController@PostingPelaksanaRKPD');

    Route::any('/sesuai', 'TrxRancangRKPDController@sesuai');
    Route::get('/getProgramRkpd/{tahun}/{id_unit}','TrxRancangRKPDSesuaiController@getProgramRkpd');
    Route::get('/getChildBidang/{id_unit}/{id_rkpd}','TrxRancangRKPDSesuaiController@getChildBidang');

    Route::get('/getProgramRenja/{id_unit}/{id_pelaksana}','TrxRancangRKPDSesuaiController@getProgramRenja');
    Route::get('/getIndikatorRenja/{id_program}','TrxRancangRKPDSesuaiController@getIndikatorRenja');
    Route::get('/getKegiatanRenja/{id_program}','TrxRancangRKPDSesuaiController@getKegiatanRenja');
    Route::get('/getIndikatorKegiatan/{id_kegiatan}','TrxRancangRKPDSesuaiController@getIndikatorKegiatan');
    Route::get('/getPelaksanaAktivitas/{id_kegiatan}','TrxRancangRKPDSesuaiController@getPelaksanaAktivitas');
    Route::get('/getAktivitas/{id_pelaksana}','TrxRancangRKPDSesuaiController@getAktivitas');
    Route::get('/getLokasiAktivitas/{id_aktivitas}','TrxRancangRKPDSesuaiController@getLokasiAktivitas');
    Route::get('/getBelanja/{id_aktivitas}','TrxRancangRKPDSesuaiController@getBelanja');

    Route::post('/AddProgRenja','TrxRancangRKPDSesuaiController@AddProgRenja');
    Route::post('/editProgRenja','TrxRancangRKPDSesuaiController@editProgRenja');
    Route::post('/hapusProgRenja','TrxRancangRKPDSesuaiController@hapusProgRenja');
    Route::post('/postProgRenja','TrxRancangRKPDSesuaiController@postProgRenja');

    Route::post('/addIndikatorProg','TrxRancangRKPDSesuaiController@addIndikatorProg');
    Route::post('/editIndikatorProg','TrxRancangRKPDSesuaiController@editIndikatorProg');
    Route::post('/postIndikatorProg','TrxRancangRKPDSesuaiController@postIndikatorProg');    
    Route::post('/delIndikatorProg','TrxRancangRKPDSesuaiController@delIndikatorProg');

    Route::post('/addKegRenja','TrxRancangRKPDSesuaiController@addKegRenja');
    Route::post('/editKegRenja','TrxRancangRKPDSesuaiController@editKegRenja');
    Route::post('/hapusKegRenja','TrxRancangRKPDSesuaiController@hapusKegRenja');
    Route::post('/postKegRenja','TrxRancangRKPDSesuaiController@postKegRenja');

    Route::post('/addIndikatorKeg','TrxRancangRKPDSesuaiController@addIndikatorKeg');
    Route::post('/editIndikatorKeg','TrxRancangRKPDSesuaiController@editIndikatorKeg');
    Route::post('/postIndikatorKeg','TrxRancangRKPDSesuaiController@postIndikatorKeg');    
    Route::post('/delIndikatorKeg','TrxRancangRKPDSesuaiController@delIndikatorKeg');

    Route::post('/addPelaksana','TrxRancangRKPDSesuaiController@addPelaksana');
    Route::post('/editPelaksana','TrxRancangRKPDSesuaiController@editPelaksana');
    Route::post('/hapusPelaksana','TrxRancangRKPDSesuaiController@hapusPelaksana');

    Route::post('/addAktivitas','TrxRancangRKPDSesuaiController@addAktivitas');
    Route::post('/editAktivitas','TrxRancangRKPDSesuaiController@editAktivitas');
    Route::post('/postAktivitas','TrxRancangRKPDSesuaiController@postAktivitas');    
    Route::post('/hapusAktivitas','TrxRancangRKPDSesuaiController@hapusAktivitas');

    Route::post('/addLokasi','TrxRancangRKPDSesuaiController@addLokasi');
    Route::post('/editLokasi','TrxRancangRKPDSesuaiController@editLokasi');
    Route::post('/hapusLokasi','TrxRancangRKPDSesuaiController@hapusLokasi');

    Route::post('/getHitungASB','TrxRancangRKPDSesuaiController@getHitungASB');
    Route::post('/unloadASB','TrxRancangRKPDSesuaiController@unloadASB');

    Route::post('/addBelanja','TrxRancangRKPDSesuaiController@addBelanja');
    Route::post('/editBelanja','TrxRancangRKPDSesuaiController@editBelanja');
    Route::post('/hapusBelanja','TrxRancangRKPDSesuaiController@hapusBelanja');
    Route::get('/getLokasiCopy/{id_unit}', 'TrxRancangRKPDSesuaiController@getLokasiCopy');

    Route::any('/dokumen', 'TrxRancangRKPDDokController@index');
    Route::any('/getDataDokumen', 'TrxRancangRKPDDokController@getDataDokumen');  
    Route::any('/getDataPerencana', 'TrxRancangRKPDDokController@getDataPerencana'); 
    Route::any('/addDokumen', 'TrxRancangRKPDDokController@addDokumen');
    Route::any('/editDokumen', 'TrxRancangRKPDDokController@editDokumen');
    Route::any('/hapusDokumen', 'TrxRancangRKPDDokController@hapusDokumen');
    Route::any('/postDokumen', 'TrxRancangRKPDDokController@postDokumen');  
});

//RANCANGAN  AKHIR RKPD
Route::group(['prefix' => 'ranhirrkpd', 'middleware' => ['auth', 'menu:40']], function () {
    Route::get('/', 'TrxRkpdRanhirController@index');
    Route::get('/getDataRekap','TrxRkpdRanhirController@getDataRekap');
    Route::get('/getRefUnit','TrxRkpdRanhirController@getRefUnit');
    Route::any('/loadData', 'TrxRkpdRanhirController@loadData')->middleware('auth', 'menu:405');  
    Route::any('/blangsung', 'TrxRkpdRanhirController@blangsung');
    Route::any('/getSelectProgram/{id_tahun}', 'TrxRkpdRanhirController@getSelectProgram');

    Route::any('/getDataDokumen', 'TrxRkpdRanhirController@getDataDokumen');
    Route::any('/getDataPerencana', 'TrxRkpdRanhirController@getDataPerencana');
    Route::any('/addDokumen', 'TrxRkpdRanhirController@addDokumen');
    Route::any('/editDokumen', 'TrxRkpdRanhirController@editDokumen');
    Route::any('/hapusDokumen', 'TrxRkpdRanhirController@hapusDokumen');

    Route::post('/importData','TrxRkpdRanhirController@importData');
    Route::post('/unLoadData','TrxRkpdRanhirController@unLoadData');

    Route::any('/dokumen', 'TrxRkpdRanhirController@doku');
    Route::any('/getDataDokumen', 'TrxRkpdRanhirController@getDataDokumen');  
    Route::any('/getDataPerencana', 'TrxRkpdRanhirController@getDataPerencana'); 
    Route::any('/addDokumen', 'TrxRkpdRanhirController@addDokumen');
    Route::any('/editDokumen', 'TrxRkpdRanhirController@editDokumen');
    Route::any('/hapusDokumen', 'TrxRkpdRanhirController@hapusDokumen');
    Route::any('/postDokumen', 'TrxRkpdRanhirController@postDokumen'); 

    Route::get('/getData','TrxRkpdRanhirEditController@getData');
    Route::get('/getRefIndikator', 'TrxRkpdRanhirEditController@getRefIndikator');
    Route::get('/getRefUnit', 'TrxRkpdRanhirEditController@getRefUnit');
    Route::get('/getRefProgramRPJMD', 'TrxRkpdRanhirEditController@getRefProgramRPJMD');
    Route::get('/getUrusan', 'TrxRkpdRanhirEditController@getUrusan');
    Route::get('/getBidang/{id_urusan}', 'TrxRkpdRanhirEditController@getBidang');

    Route::get('/getIndikatorRKPD/{id_rkpd}','TrxRkpdRanhirEditController@getIndikatorRKPD');
    Route::get('/getUrusanRKPD/{id_rkpd}','TrxRkpdRanhirEditController@getUrusanRKPD');
    Route::get('/getPelaksanaRKPD/{id_rkpd}/{id_urusan}','TrxRkpdRanhirEditController@getPelaksanaRKPD');

    Route::post('/addProgramRkpd','TrxRkpdRanhirEditController@addProgramRkpd');
    Route::post('/editProgramRKPD','TrxRkpdRanhirEditController@editProgramRKPD');
    Route::post('/postProgram','TrxRkpdRanhirEditController@postProgram');
    Route::post('/hapusProgramRKPD','TrxRkpdRanhirEditController@hapusProgramRKPD');

    Route::post('/addIndikatorRKPD','TrxRkpdRanhirEditController@addIndikatorRKPD');
    Route::post('/editIndikatorRKPD','TrxRkpdRanhirEditController@editIndikatorRKPD');
    Route::post('/postIndikatorRKPD','TrxRkpdRanhirEditController@postIndikatorRKPD');
    Route::post('/hapusIndikatorRKPD','TrxRkpdRanhirEditController@hapusIndikatorRKPD');

    Route::post('/addUrusanRKPD','TrxRkpdRanhirEditController@addUrusanRKPD');
    Route::post('/hapusUrusanRKPD','TrxRkpdRanhirEditController@hapusUrusanRKPD');

    Route::post('/addPelaksanaRKPD','TrxRkpdRanhirEditController@addPelaksanaRKPD');
    Route::post('/editPelaksanaRKPD','TrxRkpdRanhirEditController@editPelaksanaRKPD');
    Route::post('/postPelaksanaRKPD','TrxRkpdRanhirEditController@postPelaksanaRKPD');
    Route::post('/hapusPelaksanaRKPD','TrxRkpdRanhirEditController@hapusPelaksanaRKPD');
    Route::post('/PostingPelaksanaRKPD','TrxRkpdRanhirEditController@PostingPelaksanaRKPD');

    Route::any('/sesuai', 'TrxRkpdRanhirController@sesuai');
    Route::get('/getProgramRkpd/{tahun}/{id_unit}','TrxRkpdRanhirSesuaiController@getProgramRkpd');
    Route::get('/getChildBidang/{id_unit}/{id_rkpd}','TrxRkpdRanhirSesuaiController@getChildBidang');

    Route::get('/getProgramRenja/{id_unit}/{id_pelaksana}','TrxRkpdRanhirSesuaiController@getProgramRenja');
    Route::get('/getIndikatorRenja/{id_program}','TrxRkpdRanhirSesuaiController@getIndikatorRenja');
    Route::get('/getKegiatanRenja/{id_program}','TrxRkpdRanhirSesuaiController@getKegiatanRenja');
    Route::get('/getIndikatorKegiatan/{id_kegiatan}','TrxRkpdRanhirSesuaiController@getIndikatorKegiatan');
    Route::get('/getPelaksanaAktivitas/{id_kegiatan}','TrxRkpdRanhirSesuaiController@getPelaksanaAktivitas');
    Route::get('/getAktivitas/{id_pelaksana}','TrxRkpdRanhirSesuaiController@getAktivitas');
    Route::get('/getLokasiAktivitas/{id_aktivitas}','TrxRkpdRanhirSesuaiController@getLokasiAktivitas');
    Route::get('/getBelanja/{id_aktivitas}','TrxRkpdRanhirSesuaiController@getBelanja');

    Route::post('/AddProgRenja','TrxRkpdRanhirSesuaiController@AddProgRenja');
    Route::post('/editProgRenja','TrxRkpdRanhirSesuaiController@editProgRenja');
    Route::post('/hapusProgRenja','TrxRkpdRanhirSesuaiController@hapusProgRenja');
    Route::post('/postProgRenja','TrxRkpdRanhirSesuaiController@postProgRenja');

    Route::post('/addIndikatorProg','TrxRkpdRanhirSesuaiController@addIndikatorProg');
    Route::post('/editIndikatorProg','TrxRkpdRanhirSesuaiController@editIndikatorProg');
    Route::post('/postIndikatorProg','TrxRkpdRanhirSesuaiController@postIndikatorProg');    
    Route::post('/delIndikatorProg','TrxRkpdRanhirSesuaiController@delIndikatorProg');

    Route::post('/addKegRenja','TrxRkpdRanhirSesuaiController@addKegRenja');
    Route::post('/editKegRenja','TrxRkpdRanhirSesuaiController@editKegRenja');
    Route::post('/hapusKegRenja','TrxRkpdRanhirSesuaiController@hapusKegRenja');
    Route::post('/postKegRenja','TrxRkpdRanhirSesuaiController@postKegRenja');

    Route::post('/addIndikatorKeg','TrxRkpdRanhirSesuaiController@addIndikatorKeg');
    Route::post('/editIndikatorKeg','TrxRkpdRanhirSesuaiController@editIndikatorKeg');
    Route::post('/postIndikatorKeg','TrxRkpdRanhirSesuaiController@postIndikatorKeg');    
    Route::post('/delIndikatorKeg','TrxRkpdRanhirSesuaiController@delIndikatorKeg');

    Route::post('/addPelaksana','TrxRkpdRanhirSesuaiController@addPelaksana');
    Route::post('/editPelaksana','TrxRkpdRanhirSesuaiController@editPelaksana');
    Route::post('/hapusPelaksana','TrxRkpdRanhirSesuaiController@hapusPelaksana');

    Route::post('/addAktivitas','TrxRkpdRanhirSesuaiController@addAktivitas');
    Route::post('/editAktivitas','TrxRkpdRanhirSesuaiController@editAktivitas');
    Route::post('/postAktivitas','TrxRkpdRanhirSesuaiController@postAktivitas');    
    Route::post('/hapusAktivitas','TrxRkpdRanhirSesuaiController@hapusAktivitas');

    Route::post('/addLokasi','TrxRkpdRanhirSesuaiController@addLokasi');
    Route::post('/editLokasi','TrxRkpdRanhirSesuaiController@editLokasi');
    Route::post('/hapusLokasi','TrxRkpdRanhirSesuaiController@hapusLokasi');

    Route::post('/getHitungASB','TrxRkpdRanhirSesuaiController@getHitungASB');
    Route::post('/unloadASB','TrxRkpdRanhirSesuaiController@unloadASB');

    Route::post('/addBelanja','TrxRkpdRanhirSesuaiController@addBelanja');
    Route::post('/editBelanja','TrxRkpdRanhirSesuaiController@editBelanja');
    Route::post('/hapusBelanja','TrxRkpdRanhirSesuaiController@hapusBelanja');
    Route::get('/getLokasiCopy/{id_unit}', 'TrxRkpdRanhirSesuaiController@getLokasiCopy');        

});

//RANCANGAN  RKPD FINAL
Route::group(['prefix' => 'rkpd', 'middleware' => ['auth', 'menu:40']], function () {
    Route::get('/', 'TrxRkpdFinalController@index');
    Route::get('/getDataRekap','TrxRkpdFinalController@getDataRekap');
    Route::get('/getRefUnit','TrxRkpdFinalController@getRefUnit');
    Route::any('/loadData', 'TrxRkpdFinalController@loadData')->middleware('auth', 'menu:405');  
    Route::any('/blangsung', 'TrxRkpdFinalController@blangsung');
    Route::any('/getSelectProgram/{id_tahun}', 'TrxRkpdFinalController@getSelectProgram');

    Route::any('/getDataDokumen', 'TrxRkpdFinalController@getDataDokumen');
    Route::any('/getDataPerencana', 'TrxRkpdFinalController@getDataPerencana');
    Route::any('/addDokumen', 'TrxRkpdFinalController@addDokumen');
    Route::any('/editDokumen', 'TrxRkpdFinalController@editDokumen');
    Route::any('/hapusDokumen', 'TrxRkpdFinalController@hapusDokumen');

    Route::post('/importData','TrxRkpdFinalController@importData');
    Route::post('/unLoadData','TrxRkpdFinalController@unLoadData');

    Route::any('/dokumen', 'TrxRkpdFinalController@doku');
    Route::any('/getDataDokumen', 'TrxRkpdFinalController@getDataDokumen');  
    Route::any('/getDataPerencana', 'TrxRkpdFinalController@getDataPerencana'); 
    Route::any('/addDokumen', 'TrxRkpdFinalController@addDokumen');
    Route::any('/editDokumen', 'TrxRkpdFinalController@editDokumen');
    Route::any('/hapusDokumen', 'TrxRkpdFinalController@hapusDokumen');
    Route::any('/postDokumen', 'TrxRkpdFinalController@postDokumen'); 

    Route::get('/getData','TrxRkpdFinalEditController@getData');
    Route::get('/getRefIndikator', 'TrxRkpdFinalEditController@getRefIndikator');
    Route::get('/getRefUnit', 'TrxRkpdFinalEditController@getRefUnit');
    Route::get('/getRefProgramRPJMD', 'TrxRkpdFinalEditController@getRefProgramRPJMD');
    Route::get('/getUrusan', 'TrxRkpdFinalEditController@getUrusan');
    Route::get('/getBidang/{id_urusan}', 'TrxRkpdFinalEditController@getBidang');

    Route::get('/getIndikatorRKPD/{id_rkpd}','TrxRkpdFinalEditController@getIndikatorRKPD');
    Route::get('/getUrusanRKPD/{id_rkpd}','TrxRkpdFinalEditController@getUrusanRKPD');
    Route::get('/getPelaksanaRKPD/{id_rkpd}/{id_urusan}','TrxRkpdFinalEditController@getPelaksanaRKPD');

    Route::post('/addProgramRkpd','TrxRkpdFinalEditController@addProgramRkpd');
    Route::post('/editProgramRKPD','TrxRkpdFinalEditController@editProgramRKPD');
    Route::post('/postProgram','TrxRkpdFinalEditController@postProgram');
    Route::post('/hapusProgramRKPD','TrxRkpdFinalEditController@hapusProgramRKPD');

    Route::post('/addIndikatorRKPD','TrxRkpdFinalEditController@addIndikatorRKPD');
    Route::post('/editIndikatorRKPD','TrxRkpdFinalEditController@editIndikatorRKPD');
    Route::post('/postIndikatorRKPD','TrxRkpdFinalEditController@postIndikatorRKPD');
    Route::post('/hapusIndikatorRKPD','TrxRkpdFinalEditController@hapusIndikatorRKPD');

    Route::post('/addUrusanRKPD','TrxRkpdFinalEditController@addUrusanRKPD');
    Route::post('/hapusUrusanRKPD','TrxRkpdFinalEditController@hapusUrusanRKPD');

    Route::post('/addPelaksanaRKPD','TrxRkpdFinalEditController@addPelaksanaRKPD');
    Route::post('/editPelaksanaRKPD','TrxRkpdFinalEditController@editPelaksanaRKPD');
    Route::post('/postPelaksanaRKPD','TrxRkpdFinalEditController@postPelaksanaRKPD');
    Route::post('/hapusPelaksanaRKPD','TrxRkpdFinalEditController@hapusPelaksanaRKPD');
    Route::post('/PostingPelaksanaRKPD','TrxRkpdFinalEditController@PostingPelaksanaRKPD');

    Route::any('/sesuai', 'TrxRkpdFinalController@sesuai');
    Route::get('/getProgramRkpd/{tahun}/{id_unit}','TrxRkpdFinalSesuaiController@getProgramRkpd');
    Route::get('/getChildBidang/{id_unit}/{id_rkpd}','TrxRkpdFinalSesuaiController@getChildBidang');

    Route::get('/getProgramRenja/{id_unit}/{id_pelaksana}','TrxRkpdFinalSesuaiController@getProgramRenja');
    Route::get('/getIndikatorRenja/{id_program}','TrxRkpdFinalSesuaiController@getIndikatorRenja');
    Route::get('/getKegiatanRenja/{id_program}','TrxRkpdFinalSesuaiController@getKegiatanRenja');
    Route::get('/getIndikatorKegiatan/{id_kegiatan}','TrxRkpdFinalSesuaiController@getIndikatorKegiatan');
    Route::get('/getPelaksanaAktivitas/{id_kegiatan}','TrxRkpdFinalSesuaiController@getPelaksanaAktivitas');
    Route::get('/getAktivitas/{id_pelaksana}','TrxRkpdFinalSesuaiController@getAktivitas');
    Route::get('/getLokasiAktivitas/{id_aktivitas}','TrxRkpdFinalSesuaiController@getLokasiAktivitas');
    Route::get('/getBelanja/{id_aktivitas}','TrxRkpdFinalSesuaiController@getBelanja');

    Route::post('/AddProgRenja','TrxRkpdFinalSesuaiController@AddProgRenja');
    Route::post('/editProgRenja','TrxRkpdFinalSesuaiController@editProgRenja');
    Route::post('/hapusProgRenja','TrxRkpdFinalSesuaiController@hapusProgRenja');
    Route::post('/postProgRenja','TrxRkpdFinalSesuaiController@postProgRenja');

    Route::post('/addIndikatorProg','TrxRkpdFinalSesuaiController@addIndikatorProg');
    Route::post('/editIndikatorProg','TrxRkpdFinalSesuaiController@editIndikatorProg');
    Route::post('/postIndikatorProg','TrxRkpdFinalSesuaiController@postIndikatorProg');    
    Route::post('/delIndikatorProg','TrxRkpdFinalSesuaiController@delIndikatorProg');

    Route::post('/addKegRenja','TrxRkpdFinalSesuaiController@addKegRenja');
    Route::post('/editKegRenja','TrxRkpdFinalSesuaiController@editKegRenja');
    Route::post('/hapusKegRenja','TrxRkpdFinalSesuaiController@hapusKegRenja');
    Route::post('/postKegRenja','TrxRkpdFinalSesuaiController@postKegRenja');

    Route::post('/addIndikatorKeg','TrxRkpdFinalSesuaiController@addIndikatorKeg');
    Route::post('/editIndikatorKeg','TrxRkpdFinalSesuaiController@editIndikatorKeg');
    Route::post('/postIndikatorKeg','TrxRkpdFinalSesuaiController@postIndikatorKeg');    
    Route::post('/delIndikatorKeg','TrxRkpdFinalSesuaiController@delIndikatorKeg');

    Route::post('/addPelaksana','TrxRkpdFinalSesuaiController@addPelaksana');
    Route::post('/editPelaksana','TrxRkpdFinalSesuaiController@editPelaksana');
    Route::post('/hapusPelaksana','TrxRkpdFinalSesuaiController@hapusPelaksana');

    Route::post('/addAktivitas','TrxRkpdFinalSesuaiController@addAktivitas');
    Route::post('/editAktivitas','TrxRkpdFinalSesuaiController@editAktivitas');
    Route::post('/postAktivitas','TrxRkpdFinalSesuaiController@postAktivitas');    
    Route::post('/hapusAktivitas','TrxRkpdFinalSesuaiController@hapusAktivitas');

    Route::post('/addLokasi','TrxRkpdFinalSesuaiController@addLokasi');
    Route::post('/editLokasi','TrxRkpdFinalSesuaiController@editLokasi');
    Route::post('/hapusLokasi','TrxRkpdFinalSesuaiController@hapusLokasi');

    Route::post('/getHitungASB','TrxRkpdFinalSesuaiController@getHitungASB');
    Route::post('/unloadASB','TrxRkpdFinalSesuaiController@unloadASB');

    Route::post('/addBelanja','TrxRkpdFinalSesuaiController@addBelanja');
    Route::post('/editBelanja','TrxRkpdFinalSesuaiController@editBelanja');
    Route::post('/hapusBelanja','TrxRkpdFinalSesuaiController@hapusBelanja');
    Route::get('/getLokasiCopy/{id_unit}', 'TrxRkpdFinalSesuaiController@getLokasiCopy');   
});