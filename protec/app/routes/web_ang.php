<?php

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::group(['prefix' => 'anggaran', 'middleware' => ['auth']], function () {
    Route::get('/getSubUnit/{id_unit}', 'TrxRenjaRancanganBLangsungController@getSubUnit');
    Route::get('/getSumberDana', 'TrxRenjaRancanganBLangsungController@getSumberDana');
    Route::get('/getKecamatan', 'TrxRenjaRancanganBLangsungController@getKecamatan');
    Route::get('/getLokasiDesa/{kecamatan}', 'TrxRenjaRancanganBLangsungController@getLokasiDesa');
    Route::get('/getLokasiTeknis', 'TrxRenjaRancanganBLangsungController@getLokasiTeknis');
    Route::get('/getLokasiLuarDaerah', 'TrxRenjaRancanganBLangsungController@getLokasiLuarDaerah');
    Route::get('/getRekening/{id}/{tarif}', 'TrxRenjaRancanganBLController@getRekening');
    Route::get('/getRekeningDapat', 'TrxRenjaRancanganBLController@getRekeningDapat');
    Route::get('/getRekeningBTL', 'TrxRenjaRancanganBLController@getRekeningBTL');
    Route::get('/getZonaSSH', 'TrxRenjaRancanganBLangsungController@getZonaSSH');
    Route::get('/getItemSSH/{id_zona}/{param_like}', 'TrxRenjaRancanganBLangsungController@getItemSSH');
});

Route::group(['prefix' => 'ppas', 'middleware' => ['auth', 'menu:70']], function () {
    Route::get('/', 'Ang\TrxPpasController@index');
    Route::get('/getDataRekap','Ang\TrxPpasController@getDataRekap');
    Route::post('/importData','Ang\TrxPpasController@importData');
    Route::post('/unLoadData','Ang\TrxPpasController@unLoadData');

    Route::any('/getDataDokumen', 'Ang\TrxPpasController@getDataDokumen');
    Route::any('/getDataPerencana', 'Ang\TrxPpasController@getDataPerencana');
    Route::any('/addDokumen', 'Ang\TrxPpasController@addDokumen');
    Route::any('/editDokumen', 'Ang\TrxPpasController@editDokumen');
    Route::any('/hapusDokumen', 'Ang\TrxPpasController@hapusDokumen');
    Route::any('/postDokumen', 'Ang\TrxPpasController@postDokumen'); 
    Route::any('/getDataDokReferensi', 'Ang\TrxPpasController@getDataDokReferensi'); 
    
    Route::any('/progpemda', 'Ang\TrxPpasController@progpemda');
    Route::any('/progopd', 'Ang\TrxPpasController@progopd');
    Route::any('/sesuai', 'Ang\TrxPpasController@sesuai');

    Route::get('/getRefUnit','Ang\TrxPpasController@getRefUnit');
    Route::any('/getSelectProgram/{id_tahun}', 'Ang\TrxPpasController@getSelectProgram');

    Route::get('/getData','Ang\TrxPpasRkpdController@getData');
    Route::get('/getDokumenKeuangan', 'Ang\TrxPpasRkpdController@getDokumenKeuangan');
    Route::get('/getRefIndikator', 'Ang\TrxPpasRkpdController@getRefIndikator');
    Route::get('/getRefUnit', 'Ang\TrxPpasRkpdController@getRefUnit');
    Route::get('/getRefProgramRPJMD', 'Ang\TrxPpasRkpdController@getRefProgramRPJMD');
    Route::get('/getUrusan', 'Ang\TrxPpasRkpdController@getUrusan');
    Route::get('/getBidang/{id_urusan}', 'Ang\TrxPpasRkpdController@getBidang');

    Route::get('/getIndikatorRKPD/{id_rkpd}','Ang\TrxPpasRkpdController@getIndikatorRKPD');
    Route::get('/getUrusanRKPD/{id_rkpd}','Ang\TrxPpasRkpdController@getUrusanRKPD');
    Route::get('/getPelaksanaRKPD/{id_rkpd}/{id_urusan}','Ang\TrxPpasRkpdController@getPelaksanaRKPD');

    Route::post('/addProgramRkpd','Ang\TrxPpasRkpdController@addProgramRkpd');
    Route::post('/editProgramRKPD','Ang\TrxPpasRkpdController@editProgramRKPD');
    Route::post('/postProgram','Ang\TrxPpasRkpdController@postProgram');
    Route::post('/hapusProgramRKPD','Ang\TrxPpasRkpdController@hapusProgramRKPD');

    Route::post('/addIndikatorRKPD','Ang\TrxPpasRkpdController@addIndikatorRKPD');
    Route::post('/editIndikatorRKPD','Ang\TrxPpasRkpdController@editIndikatorRKPD');
    Route::post('/postIndikatorRKPD','Ang\TrxPpasRkpdController@postIndikatorRKPD');
    Route::post('/hapusIndikatorRKPD','Ang\TrxPpasRkpdController@hapusIndikatorRKPD');

    Route::post('/addUrusanRKPD','Ang\TrxPpasRkpdController@addUrusanRKPD');
    Route::post('/hapusUrusanRKPD','Ang\TrxPpasRkpdController@hapusUrusanRKPD');

    Route::post('/addPelaksanaRKPD','Ang\TrxPpasRkpdController@addPelaksanaRKPD');
    Route::post('/editPelaksanaRKPD','Ang\TrxPpasRkpdController@editPelaksanaRKPD');
    Route::post('/postPelaksanaRKPD','Ang\TrxPpasRkpdController@postPelaksanaRKPD');
    Route::post('/hapusPelaksanaRKPD','Ang\TrxPpasRkpdController@hapusPelaksanaRKPD');
    Route::post('/PostingPelaksanaRKPD','Ang\TrxPpasRkpdController@PostingPelaksanaRKPD');
});

Route::group(['prefix' => 'Apbd', 'middleware' => ['auth', 'menu:71']], function () {
    Route::get('/', 'Ang\TrxApbdController@index');
    Route::get('/getDataRekap','Ang\TrxApbdController@getDataRekap');
    Route::post('/importData','Ang\TrxApbdController@importData');
    Route::post('/unLoadData','Ang\TrxApbdController@unLoadData');

    Route::any('/getDataDokumen', 'Ang\TrxApbdController@getDataDokumen');
    Route::any('/getDataPerencana', 'Ang\TrxApbdController@getDataPerencana');
    Route::any('/addDokumen', 'Ang\TrxApbdController@addDokumen');
    Route::any('/editDokumen', 'Ang\TrxApbdController@editDokumen');
    Route::any('/hapusDokumen', 'Ang\TrxApbdController@hapusDokumen');
    Route::any('/postDokumen', 'Ang\TrxApbdController@postDokumen'); 
    Route::any('/getDataDokReferensi', 'Ang\TrxApbdController@getDataDokReferensi'); 
    
    Route::any('/progpemda', 'Ang\TrxApbdController@progpemda');
    Route::any('/progopd', 'Ang\TrxApbdController@progopd');
    Route::any('/sesuai', 'Ang\TrxApbdController@sesuai');
    Route::any('/PostUnit/{id_unit}', 'Ang\TrxApbdController@PostUnit');

    Route::get('/getRefUnit','Ang\TrxApbdController@getRefUnit');
    Route::any('/getSelectProgram/{id_tahun}', 'Ang\TrxApbdController@getSelectProgram');

    Route::get('/getData','Ang\TrxApbdRkpdController@getData');
    Route::get('/getDokumenKeuangan', 'Ang\TrxApbdRkpdController@getDokumenKeuangan');
    Route::get('/getRefIndikator', 'Ang\TrxApbdRkpdController@getRefIndikator');
    Route::get('/getRefUnit', 'Ang\TrxApbdRkpdController@getRefUnit');
    Route::get('/getRefSubUnit', 'Ang\TrxApbdRkpdController@getRefSubUnit');
    Route::get('/getRefProgramRPJMD', 'Ang\TrxApbdRkpdController@getRefProgramRPJMD');
    Route::get('/getUrusan', 'Ang\TrxApbdRkpdController@getUrusan');
    Route::get('/getBidang/{id_urusan}', 'Ang\TrxApbdRkpdController@getBidang');

    Route::get('/getIndikatorRKPD/{id_rkpd}','Ang\TrxApbdRkpdController@getIndikatorRKPD');
    Route::get('/getUrusanRKPD/{id_rkpd}','Ang\TrxApbdRkpdController@getUrusanRKPD');
    Route::get('/getPelaksanaRKPD/{id_rkpd}/{id_urusan}','Ang\TrxApbdRkpdController@getPelaksanaRKPD');

    Route::post('/addProgramRkpd','Ang\TrxApbdRkpdController@addProgramRkpd');
    Route::post('/editProgramRKPD','Ang\TrxApbdRkpdController@editProgramRKPD');
    Route::post('/postProgram','Ang\TrxApbdRkpdController@postProgram');
    Route::post('/hapusProgramRKPD','Ang\TrxApbdRkpdController@hapusProgramRKPD');

    Route::post('/addIndikatorRKPD','Ang\TrxApbdRkpdController@addIndikatorRKPD');
    Route::post('/editIndikatorRKPD','Ang\TrxApbdRkpdController@editIndikatorRKPD');
    Route::post('/postIndikatorRKPD','Ang\TrxApbdRkpdController@postIndikatorRKPD');
    Route::post('/hapusIndikatorRKPD','Ang\TrxApbdRkpdController@hapusIndikatorRKPD');

    Route::post('/addUrusanRKPD','Ang\TrxApbdRkpdController@addUrusanRKPD');
    Route::post('/hapusUrusanRKPD','Ang\TrxApbdRkpdController@hapusUrusanRKPD');

    Route::post('/addPelaksanaRKPD','Ang\TrxApbdRkpdController@addPelaksanaRKPD');
    Route::post('/editPelaksanaRKPD','Ang\TrxApbdRkpdController@editPelaksanaRKPD');
    Route::post('/postPelaksanaRKPD','Ang\TrxApbdRkpdController@postPelaksanaRKPD');
    Route::post('/hapusPelaksanaRKPD','Ang\TrxApbdRkpdController@hapusPelaksanaRKPD');
    Route::post('/PostingPelaksanaRKPD','Ang\TrxApbdRkpdController@PostingPelaksanaRKPD');

    Route::get('/getProgramRkpd','Ang\TrxApbdRenjaController@getProgramRkpd');
    Route::get('/getChildBidang/{id_unit}/{id_rkpd}','Ang\TrxApbdRenjaController@getChildBidang');

    Route::get('/getProgramRenja/{id_unit}/{id_pelaksana}','Ang\TrxApbdRenjaController@getProgramRenja');
    Route::get('/getIndikatorRenja/{id_program}','Ang\TrxApbdRenjaController@getIndikatorRenja');
    Route::get('/getKegiatanRenja/{id_program}','Ang\TrxApbdRenjaController@getKegiatanRenja');
    Route::get('/getIndikatorKegiatan/{id_kegiatan}','Ang\TrxApbdRenjaController@getIndikatorKegiatan');
    Route::get('/getPelaksanaAktivitas/{id_kegiatan}','Ang\TrxApbdRenjaController@getPelaksanaAktivitas');
    Route::get('/getAktivitas/{id_pelaksana}','Ang\TrxApbdRenjaController@getAktivitas');
    Route::get('/getLokasiAktivitas/{id_aktivitas}','Ang\TrxApbdRenjaController@getLokasiAktivitas');
    Route::get('/getBelanja/{id_aktivitas}','Ang\TrxApbdRenjaController@getBelanja');

    Route::post('/AddProgRenja','Ang\TrxApbdRenjaController@AddProgRenja');
    Route::post('/editProgRenja','Ang\TrxApbdRenjaController@editProgRenja');
    Route::post('/hapusProgRenja','Ang\TrxApbdRenjaController@hapusProgRenja');
    Route::post('/postProgRenja','Ang\TrxApbdRenjaController@postProgRenja');

    Route::post('/addIndikatorProg','Ang\TrxApbdRenjaController@addIndikatorProg');
    Route::post('/editIndikatorProg','Ang\TrxApbdRenjaController@editIndikatorProg');
    Route::post('/postIndikatorProg','Ang\TrxApbdRenjaController@postIndikatorProg');    
    Route::post('/delIndikatorProg','Ang\TrxApbdRenjaController@delIndikatorProg');

    Route::post('/addKegRenja','Ang\TrxApbdRenjaController@addKegRenja');
    Route::post('/editKegRenja','Ang\TrxApbdRenjaController@editKegRenja');
    Route::post('/hapusKegRenja','Ang\TrxApbdRenjaController@hapusKegRenja');
    Route::post('/postKegRenja','Ang\TrxApbdRenjaController@postKegRenja');

    Route::post('/addIndikatorKeg','Ang\TrxApbdRenjaController@addIndikatorKeg');
    Route::post('/editIndikatorKeg','Ang\TrxApbdRenjaController@editIndikatorKeg');
    Route::post('/postIndikatorKeg','Ang\TrxApbdRenjaController@postIndikatorKeg');    
    Route::post('/delIndikatorKeg','Ang\TrxApbdRenjaController@delIndikatorKeg');

    Route::post('/addPelaksana','Ang\TrxApbdRenjaController@addPelaksana');
    Route::post('/editPelaksana','Ang\TrxApbdRenjaController@editPelaksana');
    Route::post('/hapusPelaksana','Ang\TrxApbdRenjaController@hapusPelaksana');

    Route::post('/addAktivitas','Ang\TrxApbdRenjaController@addAktivitas');
    Route::post('/editAktivitas','Ang\TrxApbdRenjaController@editAktivitas');
    Route::post('/postAktivitas','Ang\TrxApbdRenjaController@postAktivitas');    
    Route::post('/hapusAktivitas','Ang\TrxApbdRenjaController@hapusAktivitas');

    Route::post('/addLokasi','Ang\TrxApbdRenjaController@addLokasi');
    Route::post('/editLokasi','Ang\TrxApbdRenjaController@editLokasi');
    Route::post('/hapusLokasi','Ang\TrxApbdRenjaController@hapusLokasi');

    Route::post('/getHitungASB','Ang\TrxApbdRenjaController@getHitungASB');
    Route::post('/unloadASB','Ang\TrxApbdRenjaController@unloadASB');

    Route::post('/addBelanja','Ang\TrxApbdRenjaController@addBelanja');
    Route::post('/editBelanja','Ang\TrxApbdRenjaController@editBelanja');
    Route::post('/hapusBelanja','Ang\TrxApbdRenjaController@hapusBelanja');
    Route::get('/getLokasiCopy/{id_unit}', 'Ang\TrxApbdRenjaController@getLokasiCopy'); 
    Route::any('/getBelanjaCopy', 'Ang\TrxApbdRenjaController@getBelanjaCopy'); 

    Route::group(['prefix' => 'pagu', 'middleware' => ['auth', 'menu:71']], function () {
        Route::get('/getProgramRenja/{id_unit}','Ang\TrxApbdPaguController@getProgramRenja');
        Route::get('/getIndikatorRenja/{id_program}','Ang\TrxApbdPaguController@getIndikatorRenja');
        Route::get('/getKegiatanRenja/{id_program}','Ang\TrxApbdPaguController@getKegiatanRenja');
        Route::get('/getIndikatorKegiatan/{id_kegiatan}','Ang\TrxApbdPaguController@getIndikatorKegiatan');
        Route::get('/getPelaksanaAktivitas/{id_kegiatan}','Ang\TrxApbdPaguController@getPelaksanaAktivitas');
        Route::get('/getAktivitas/{id_pelaksana}','Ang\TrxApbdPaguController@getAktivitas');
        Route::get('/getLokasiAktivitas/{id_aktivitas}','Ang\TrxApbdPaguController@getLokasiAktivitas');
        Route::get('/getBelanja/{id_aktivitas}','Ang\TrxApbdPaguController@getBelanja');

        Route::post('/AddProgRenja','Ang\TrxApbdPaguController@AddProgRenja');
        Route::post('/editProgRenja','Ang\TrxApbdPaguController@editProgRenja');
        Route::post('/hapusProgRenja','Ang\TrxApbdPaguController@hapusProgRenja');
        Route::post('/postProgRenja','Ang\TrxApbdPaguController@postProgRenja');

        Route::post('/addIndikatorProg','Ang\TrxApbdPaguController@addIndikatorProg');
        Route::post('/editIndikatorProg','Ang\TrxApbdPaguController@editIndikatorProg');
        Route::post('/postIndikatorProg','Ang\TrxApbdPaguController@postIndikatorProg');    
        Route::post('/delIndikatorProg','Ang\TrxApbdPaguController@delIndikatorProg');

        Route::post('/addKegRenja','Ang\TrxApbdPaguController@addKegRenja');
        Route::post('/editKegRenja','Ang\TrxApbdPaguController@editKegRenja');
        Route::post('/hapusKegRenja','Ang\TrxApbdPaguController@hapusKegRenja');
        Route::post('/postKegRenja','Ang\TrxApbdPaguController@postKegRenja');

        Route::post('/addIndikatorKeg','Ang\TrxApbdPaguController@addIndikatorKeg');
        Route::post('/editIndikatorKeg','Ang\TrxApbdPaguController@editIndikatorKeg');
        Route::post('/postIndikatorKeg','Ang\TrxApbdPaguController@postIndikatorKeg');    
        Route::post('/delIndikatorKeg','Ang\TrxApbdPaguController@delIndikatorKeg');

        Route::post('/addPelaksana','Ang\TrxApbdPaguController@addPelaksana');
        Route::post('/editPelaksana','Ang\TrxApbdPaguController@editPelaksana');
        Route::post('/hapusPelaksana','Ang\TrxApbdPaguController@hapusPelaksana');

        Route::post('/addAktivitas','Ang\TrxApbdPaguController@addAktivitas');
        Route::post('/editAktivitas','Ang\TrxApbdPaguController@editAktivitas');
        Route::post('/postAktivitas','Ang\TrxApbdPaguController@postAktivitas');    
        Route::post('/hapusAktivitas','Ang\TrxApbdPaguController@hapusAktivitas');

        Route::post('/addLokasi','Ang\TrxApbdPaguController@addLokasi');
        Route::post('/editLokasi','Ang\TrxApbdPaguController@editLokasi');
        Route::post('/hapusLokasi','Ang\TrxApbdPaguController@hapusLokasi');

        Route::post('/getHitungASB','Ang\TrxApbdPaguController@getHitungASB');
        Route::post('/unloadASB','Ang\TrxApbdPaguController@unloadASB');

        Route::post('/addBelanja','Ang\TrxApbdPaguController@addBelanja');
        Route::post('/editBelanja','Ang\TrxApbdPaguController@editBelanja');
        Route::post('/hapusBelanja','Ang\TrxApbdPaguController@hapusBelanja');
        Route::get('/getLokasiCopy/{id_unit}', 'Ang\TrxApbdPaguController@getLokasiCopy'); 
        Route::any('/getBelanjaCopy', 'Ang\TrxApbdPaguController@getBelanjaCopy');
    });
});

Route::group(['prefix' => 'GeserApbd', 'middleware' => ['auth', 'menu:71']], function () {
        Route::get('/', 'Ang\TrxGeserApbdController@index');
        Route::get('/getDataRekap','Ang\TrxGeserApbdController@getDataRekap');
        Route::post('/importData','Ang\TrxGeserApbdController@importData');
        Route::post('/unLoadData','Ang\TrxGeserApbdController@unLoadData');
    
        Route::any('/getDataDokumen', 'Ang\TrxGeserApbdController@getDataDokumen');
        Route::any('/getDataPerencana', 'Ang\TrxGeserApbdController@getDataPerencana');
        Route::any('/addDokumen', 'Ang\TrxGeserApbdController@addDokumen');
        Route::any('/editDokumen', 'Ang\TrxGeserApbdController@editDokumen');
        Route::any('/hapusDokumen', 'Ang\TrxGeserApbdController@hapusDokumen');
        Route::any('/postDokumen', 'Ang\TrxGeserApbdController@postDokumen'); 
        Route::any('/getDataDokReferensi', 'Ang\TrxGeserApbdController@getDataDokReferensi'); 
        
        Route::any('/progpemda', 'Ang\TrxGeserApbdController@progpemda');
        Route::any('/progopd', 'Ang\TrxGeserApbdController@progopd');
        Route::any('/sesuai', 'Ang\TrxGeserApbdController@sesuai');
    
        Route::get('/getRefUnit','Ang\TrxGeserApbdController@getRefUnit');
        Route::any('/getSelectProgram/{id_tahun}', 'Ang\TrxGeserApbdController@getSelectProgram');
    
        Route::get('/getData','Ang\TrxGeserApbdRkpdController@getData');
        Route::get('/getDokumenKeuangan', 'Ang\TrxGeserApbdRkpdController@getDokumenKeuangan');
        Route::get('/getRefIndikator', 'Ang\TrxGeserApbdRkpdController@getRefIndikator');
        Route::get('/getRefUnit', 'Ang\TrxGeserApbdRkpdController@getRefUnit');
        Route::get('/getRefSubUnit', 'Ang\TrxGeserApbdRkpdController@getRefSubUnit');
        Route::get('/getRefProgramRPJMD', 'Ang\TrxGeserApbdRkpdController@getRefProgramRPJMD');
        Route::get('/getUrusan', 'Ang\TrxGeserApbdRkpdController@getUrusan');
        Route::get('/getBidang/{id_urusan}', 'Ang\TrxGeserApbdRkpdController@getBidang');
    
        Route::get('/getIndikatorRKPD/{id_rkpd}','Ang\TrxGeserApbdRkpdController@getIndikatorRKPD');
        Route::get('/getUrusanRKPD/{id_rkpd}','Ang\TrxGeserApbdRkpdController@getUrusanRKPD');
        Route::get('/getPelaksanaRKPD/{id_rkpd}/{id_urusan}','Ang\TrxGeserApbdRkpdController@getPelaksanaRKPD');
    
        Route::post('/addProgramRkpd','Ang\TrxGeserApbdRkpdController@addProgramRkpd');
        Route::post('/editProgramRKPD','Ang\TrxGeserApbdRkpdController@editProgramRKPD');
        Route::post('/postProgram','Ang\TrxGeserApbdRkpdController@postProgram');
        Route::post('/hapusProgramRKPD','Ang\TrxGeserApbdRkpdController@hapusProgramRKPD');
    
        Route::post('/addIndikatorRKPD','Ang\TrxGeserApbdRkpdController@addIndikatorRKPD');
        Route::post('/editIndikatorRKPD','Ang\TrxGeserApbdRkpdController@editIndikatorRKPD');
        Route::post('/postIndikatorRKPD','Ang\TrxGeserApbdRkpdController@postIndikatorRKPD');
        Route::post('/hapusIndikatorRKPD','Ang\TrxGeserApbdRkpdController@hapusIndikatorRKPD');
    
        Route::post('/addUrusanRKPD','Ang\TrxGeserApbdRkpdController@addUrusanRKPD');
        Route::post('/hapusUrusanRKPD','Ang\TrxGeserApbdRkpdController@hapusUrusanRKPD');
    
        Route::post('/addPelaksanaRKPD','Ang\TrxGeserApbdRkpdController@addPelaksanaRKPD');
        Route::post('/editPelaksanaRKPD','Ang\TrxGeserApbdRkpdController@editPelaksanaRKPD');
        Route::post('/postPelaksanaRKPD','Ang\TrxGeserApbdRkpdController@postPelaksanaRKPD');
        Route::post('/hapusPelaksanaRKPD','Ang\TrxGeserApbdRkpdController@hapusPelaksanaRKPD');
        Route::post('/PostingPelaksanaRKPD','Ang\TrxGeserApbdRkpdController@PostingPelaksanaRKPD');
    
        Route::get('/getProgramRkpd','Ang\TrxGeserApbdRenjaController@getProgramRkpd');
        Route::get('/getChildBidang/{id_unit}/{id_rkpd}','Ang\TrxGeserApbdRenjaController@getChildBidang');
    
        Route::get('/getProgramRenja/{id_unit}/{id_pelaksana}','Ang\TrxGeserApbdRenjaController@getProgramRenja');
        Route::get('/getIndikatorRenja/{id_program}','Ang\TrxGeserApbdRenjaController@getIndikatorRenja');
        Route::get('/getKegiatanRenja/{id_program}','Ang\TrxGeserApbdRenjaController@getKegiatanRenja');
        Route::get('/getIndikatorKegiatan/{id_kegiatan}','Ang\TrxGeserApbdRenjaController@getIndikatorKegiatan');
        Route::get('/getPelaksanaAktivitas/{id_kegiatan}','Ang\TrxGeserApbdRenjaController@getPelaksanaAktivitas');
        Route::get('/getAktivitas/{id_pelaksana}','Ang\TrxGeserApbdRenjaController@getAktivitas');
        Route::get('/getLokasiAktivitas/{id_aktivitas}','Ang\TrxGeserApbdRenjaController@getLokasiAktivitas');
        Route::get('/getBelanja/{id_aktivitas}','Ang\TrxGeserApbdRenjaController@getBelanja');
    
        Route::post('/AddProgRenja','Ang\TrxGeserApbdRenjaController@AddProgRenja');
        Route::post('/editProgRenja','Ang\TrxGeserApbdRenjaController@editProgRenja');
        Route::post('/hapusProgRenja','Ang\TrxGeserApbdRenjaController@hapusProgRenja');
        Route::post('/postProgRenja','Ang\TrxGeserApbdRenjaController@postProgRenja');
    
        Route::post('/addIndikatorProg','Ang\TrxGeserApbdRenjaController@addIndikatorProg');
        Route::post('/editIndikatorProg','Ang\TrxGeserApbdRenjaController@editIndikatorProg');
        Route::post('/postIndikatorProg','Ang\TrxGeserApbdRenjaController@postIndikatorProg');    
        Route::post('/delIndikatorProg','Ang\TrxGeserApbdRenjaController@delIndikatorProg');
    
        Route::post('/addKegRenja','Ang\TrxGeserApbdRenjaController@addKegRenja');
        Route::post('/editKegRenja','Ang\TrxGeserApbdRenjaController@editKegRenja');
        Route::post('/hapusKegRenja','Ang\TrxGeserApbdRenjaController@hapusKegRenja');
        Route::post('/postKegRenja','Ang\TrxGeserApbdRenjaController@postKegRenja');
    
        Route::post('/addIndikatorKeg','Ang\TrxGeserApbdRenjaController@addIndikatorKeg');
        Route::post('/editIndikatorKeg','Ang\TrxGeserApbdRenjaController@editIndikatorKeg');
        Route::post('/postIndikatorKeg','Ang\TrxGeserApbdRenjaController@postIndikatorKeg');    
        Route::post('/delIndikatorKeg','Ang\TrxGeserApbdRenjaController@delIndikatorKeg');
    
        Route::post('/addPelaksana','Ang\TrxGeserApbdRenjaController@addPelaksana');
        Route::post('/editPelaksana','Ang\TrxGeserApbdRenjaController@editPelaksana');
        Route::post('/hapusPelaksana','Ang\TrxGeserApbdRenjaController@hapusPelaksana');
    
        Route::post('/addAktivitas','Ang\TrxGeserApbdRenjaController@addAktivitas');
        Route::post('/editAktivitas','Ang\TrxGeserApbdRenjaController@editAktivitas');
        Route::post('/postAktivitas','Ang\TrxGeserApbdRenjaController@postAktivitas');    
        Route::post('/hapusAktivitas','Ang\TrxGeserApbdRenjaController@hapusAktivitas');
    
        Route::post('/addLokasi','Ang\TrxGeserApbdRenjaController@addLokasi');
        Route::post('/editLokasi','Ang\TrxGeserApbdRenjaController@editLokasi');
        Route::post('/hapusLokasi','Ang\TrxGeserApbdRenjaController@hapusLokasi');
    
        Route::post('/getHitungASB','Ang\TrxGeserApbdRenjaController@getHitungASB');
        Route::post('/unloadASB','Ang\TrxGeserApbdRenjaController@unloadASB');
    
        Route::post('/addBelanja','Ang\TrxGeserApbdRenjaController@addBelanja');
        Route::post('/editBelanja','Ang\TrxGeserApbdRenjaController@editBelanja');
        Route::post('/hapusBelanja','Ang\TrxGeserApbdRenjaController@hapusBelanja');
        Route::get('/getLokasiCopy/{id_unit}', 'Ang\TrxGeserApbdRenjaController@getLokasiCopy'); 
        Route::any('/getBelanjaCopy', 'Ang\TrxGeserApbdRenjaController@getBelanjaCopy'); 
    
        Route::group(['prefix' => 'pagu', 'middleware' => ['auth', 'menu:71']], function () {
            Route::get('/getProgramRenja/{id_unit}','Ang\TrxGeserApbdPaguController@getProgramRenja');
            Route::get('/getIndikatorRenja/{id_program}','Ang\TrxGeserApbdPaguController@getIndikatorRenja');
            Route::get('/getKegiatanRenja/{id_program}','Ang\TrxGeserApbdPaguController@getKegiatanRenja');
            Route::get('/getIndikatorKegiatan/{id_kegiatan}','Ang\TrxGeserApbdPaguController@getIndikatorKegiatan');
            Route::get('/getPelaksanaAktivitas/{id_kegiatan}','Ang\TrxGeserApbdPaguController@getPelaksanaAktivitas');
            Route::get('/getAktivitas/{id_pelaksana}','Ang\TrxGeserApbdPaguController@getAktivitas');
            Route::get('/getLokasiAktivitas/{id_aktivitas}','Ang\TrxGeserApbdPaguController@getLokasiAktivitas');
            Route::get('/getBelanja/{id_aktivitas}','Ang\TrxGeserApbdPaguController@getBelanja');
    
            Route::post('/AddProgRenja','Ang\TrxGeserApbdPaguController@AddProgRenja');
            Route::post('/editProgRenja','Ang\TrxGeserApbdPaguController@editProgRenja');
            Route::post('/hapusProgRenja','Ang\TrxGeserApbdPaguController@hapusProgRenja');
            Route::post('/postProgRenja','Ang\TrxGeserApbdPaguController@postProgRenja');
    
            Route::post('/addIndikatorProg','Ang\TrxGeserApbdPaguController@addIndikatorProg');
            Route::post('/editIndikatorProg','Ang\TrxGeserApbdPaguController@editIndikatorProg');
            Route::post('/postIndikatorProg','Ang\TrxGeserApbdPaguController@postIndikatorProg');    
            Route::post('/delIndikatorProg','Ang\TrxGeserApbdPaguController@delIndikatorProg');
    
            Route::post('/addKegRenja','Ang\TrxGeserApbdPaguController@addKegRenja');
            Route::post('/editKegRenja','Ang\TrxGeserApbdPaguController@editKegRenja');
            Route::post('/hapusKegRenja','Ang\TrxGeserApbdPaguController@hapusKegRenja');
            Route::post('/postKegRenja','Ang\TrxGeserApbdPaguController@postKegRenja');
    
            Route::post('/addIndikatorKeg','Ang\TrxGeserApbdPaguController@addIndikatorKeg');
            Route::post('/editIndikatorKeg','Ang\TrxGeserApbdPaguController@editIndikatorKeg');
            Route::post('/postIndikatorKeg','Ang\TrxGeserApbdPaguController@postIndikatorKeg');    
            Route::post('/delIndikatorKeg','Ang\TrxGeserApbdPaguController@delIndikatorKeg');
    
            Route::post('/addPelaksana','Ang\TrxGeserApbdPaguController@addPelaksana');
            Route::post('/editPelaksana','Ang\TrxGeserApbdPaguController@editPelaksana');
            Route::post('/hapusPelaksana','Ang\TrxGeserApbdPaguController@hapusPelaksana');
    
            Route::post('/addAktivitas','Ang\TrxGeserApbdPaguController@addAktivitas');
            Route::post('/editAktivitas','Ang\TrxGeserApbdPaguController@editAktivitas');
            Route::post('/postAktivitas','Ang\TrxGeserApbdPaguController@postAktivitas');    
            Route::post('/hapusAktivitas','Ang\TrxGeserApbdPaguController@hapusAktivitas');
    
            Route::post('/addLokasi','Ang\TrxGeserApbdPaguController@addLokasi');
            Route::post('/editLokasi','Ang\TrxGeserApbdPaguController@editLokasi');
            Route::post('/hapusLokasi','Ang\TrxGeserApbdPaguController@hapusLokasi');
    
            Route::post('/getHitungASB','Ang\TrxGeserApbdPaguController@getHitungASB');
            Route::post('/unloadASB','Ang\TrxGeserApbdPaguController@unloadASB');
    
            Route::post('/addBelanja','Ang\TrxGeserApbdPaguController@addBelanja');
            Route::post('/editBelanja','Ang\TrxGeserApbdPaguController@editBelanja');
            Route::post('/hapusBelanja','Ang\TrxGeserApbdPaguController@hapusBelanja');
            Route::get('/getLokasiCopy/{id_unit}', 'Ang\TrxGeserApbdPaguController@getLokasiCopy'); 
            Route::any('/getBelanjaCopy', 'Ang\TrxGeserApbdPaguController@getBelanjaCopy');
        });
});