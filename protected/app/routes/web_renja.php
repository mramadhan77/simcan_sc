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

//ForumSKPD
Route::group(['prefix' => 'forumskpd', 'middleware' => ['auth', 'menu:60']], function() {
    Route::get('/', 'TrxForumSkpdController@index');
    Route::get('/getSelectProgram/{unit}/{tahun}', 'TrxForumSkpdController@getSelectProgram');

    Route::group(['prefix' => 'loadData', 'middleware' => ['auth', 'menu:606']], function() {
        Route::get('/', 'TrxForumSkpdController@loadData');
        Route::get('/getProgramRkpd/{tahun}/{unit}','TrxForumSkpdController@getProgramRkpd');
        Route::post('/insertProgramRkpd', 'TrxForumSkpdController@insertProgramRkpd');
        Route::post('/unLoadProgramRkpd', 'TrxForumSkpdController@unLoadProgramRkpd');
    });

    Route::get('/dokumen', 'TrxForumSkpdDokuController@index');
        Route::get('/getDataDokumen/{id_unit}', 'TrxForumSkpdDokuController@getDataDokumen');
        Route::any('/addDokumen', 'TrxForumSkpdDokuController@addDokumen');
        Route::any('/editDokumen', 'TrxForumSkpdDokuController@editDokumen');
        Route::any('/postDokumen', 'TrxForumSkpdDokuController@postDokumen');
        Route::any('/hapusDokumen', 'TrxForumSkpdDokuController@hapusDokumen');
        Route::any('/getUnit', 'TrxForumSkpdDokuController@getUnit');

    Route::group(['prefix' => 'verifikasi', 'middleware' => ['auth', 'menu:401']], function() {
        Route::get('/', 'TrxForumSkpdVerifikasiController@index');
        Route::get('/getProgramRkpdForum/{tahun}','TrxForumSkpdVerifikasiController@getProgramRkpdForum');
        Route::get('/getUnitForumPD/{id_ranwal}','TrxForumSkpdVerifikasiController@getUnitForumPD');
        Route::get('/getProgForumPD/{id_forum}','TrxForumSkpdVerifikasiController@getProgForumPD');
        Route::post('/postBappeda', 'TrxForumSkpdVerifikasiController@postBappeda');
    });

    Route::group(['prefix' => 'forum', 'middleware' => ['auth', 'menu:607']], function() {
        Route::get('/getUnitRenja', 'TrxForumSkpdController@getUnit');
        Route::get('/getProgramRkpd/{tahun}/{unit}','TrxForumSkpdController@getProgramRkpdForum');
        Route::get('/getChildBidang/{id_unit}/{id_ranwal}','TrxForumSkpdController@getChildBidang');
        Route::any('/getProgramRenja/{tahun}/{unit}/{id_forum}/{bidang}','TrxForumSkpdController@getProgramRenja');
        Route::any('/getIndikatorRenja/{id_forum_program}','TrxForumSkpdController@getIndikatorRenja');
        Route::any('/getKegiatanRenja/{id_program}','TrxForumSkpdController@getKegiatanRenja');
        Route::any('/getIndikatorKegiatan/{id_forum_skpd}','TrxForumSkpdController@getIndikatorKegiatan');
        Route::any('/getAktivitas/{id_forum}','TrxForumSkpdController@getAktivitas');
        Route::any('/getPelaksanaAktivitas/{id_aktivitas}','TrxForumSkpdController@getPelaksanaAktivitas');
        Route::any('/getLokasiAktivitas/{id_pelaksana}','TrxForumSkpdController@getLokasiAktivitas');
        Route::get('/getChildUsulan/{id_lokasi}','TrxForumSkpdController@getChildUsulan');    
        Route::get('/getBelanja/{id_lokasi}','TrxForumSkpdController@getBelanja');

        Route::post('/getHitungASB', 'TrxForumSkpdController@getHitungASB');
        Route::post('/unloadASB', 'TrxForumSkpdController@unloadASB');
        Route::get('/getLokasiCopy/{id_aktivitas}', 'TrxForumSkpdController@getLokasiCopy');
        Route::post('/getBelanjaCopy', 'TrxForumSkpdController@getBelanjaCopy');

        Route::post('/AddProgRenja','TrxForumSkpdController@AddProgRenja');
        Route::post('/editProgRenja','TrxForumSkpdController@editProgRenja');
        Route::post('/hapusProgRenja','TrxForumSkpdController@hapusProgRenja');
        Route::post('/postProgRenja','TrxForumSkpdController@postProgRenja');

        Route::post('/addKegRenja','TrxForumSkpdController@addKegRenja');
        Route::post('/editKegRenja','TrxForumSkpdController@editKegRenja');
        Route::post('/hapusKegRenja','TrxForumSkpdController@hapusKegRenja');
        Route::post('/postKegRenja','TrxForumSkpdController@postKegRenja');

        Route::post('/addAktivitas','TrxForumSkpdController@addAktivitas');
        Route::post('/editAktivitas','TrxForumSkpdController@editAktivitas');
        Route::post('/hapusAktivitas','TrxForumSkpdController@hapusAktivitas');
        Route::post('/postAktivitas','TrxForumSkpdController@postAktivitas');

        Route::post('/addPelaksana','TrxForumSkpdController@addPelaksana');
        Route::post('/editPelaksana','TrxForumSkpdController@editPelaksana');
        Route::post('/hapusPelaksana','TrxForumSkpdController@hapusPelaksana');

        Route::post('/addLokasi','TrxForumSkpdController@addLokasi');
        Route::post('/editLokasi','TrxForumSkpdController@editLokasi');
        Route::post('/hapusLokasi','TrxForumSkpdController@hapusLokasi');

        Route::post('/addUsulan','TrxForumSkpdController@addUsulan');
        Route::post('/editUsulan','TrxForumSkpdController@editUsulan');
        Route::post('/hapusUsulan','TrxForumSkpdController@hapusUsulan');

        Route::post('/addBelanja','TrxForumSkpdController@addBelanja');
        Route::post('/editBelanja','TrxForumSkpdController@editBelanja');
        Route::post('/hapusBelanja','TrxForumSkpdController@hapusBelanja');

        Route::post('/addIndikatorProg','TrxForumSkpdController@addIndikatorProg');
        Route::post('/editIndikatorProg','TrxForumSkpdController@editIndikatorProg');
        Route::post('/postIndikatorProg','TrxForumSkpdController@postIndikatorProg');
        Route::post('/delIndikatorProg','TrxForumSkpdController@delIndikatorProg');

        Route::post('/addIndikatorKeg','TrxForumSkpdController@addIndikatorKeg');
        Route::post('/editIndikatorKeg','TrxForumSkpdController@editIndikatorKeg');
        Route::post('/postIndikatorKeg','TrxForumSkpdController@postIndikatorKeg');
        Route::post('/delIndikatorKeg','TrxForumSkpdController@delIndikatorKeg');
        
        Route::get('/dehashPemda','TrxForumSkpdController@dehashPemda');

    });
});

//Ranwal Renja
Route::group(['prefix' => 'ranwalrenja', 'middleware' => ['auth', 'menu:50']], function () {
    Route::any('/loadData', 'TrxRenjaRanwalController@loadData');
    Route::get('/getUnitRenja', 'TrxRenjaRanwalController@getUnit');
    Route::get('/getSelectProgram/{unit}/{tahun}', 'TrxRenjaRanwalController@getSelectProgram');
    Route::get('/getProgramRkpd/{tahun}/{unit}', 'TrxRenjaRanwalController@getProgramRkpd');
    Route::get('/getRekapProgram/{tahun}/{unit}/{ranwal}', 'TrxRenjaRanwalController@getRekapProgram');

    Route::get('/getTahunKe/{tahun}', 'TrxRenjaRanwalController@getTahunKe');
    Route::post('/transProgramRKPD', 'TrxRenjaRanwalController@transProgramRKPD');
    Route::post('/unloadRenja', 'TrxRenjaRanwalController@unloadRenja');
    
    Route::get('/dokumen', 'TrxRanwalRenjaDokController@index');
    Route::get('/getDataDokumen/{id_unit}', 'TrxRanwalRenjaDokController@getDataDokumen');
    Route::any('/addDokumen', 'TrxRanwalRenjaDokController@addDokumen');
    Route::any('/editDokumen', 'TrxRanwalRenjaDokController@editDokumen');
    Route::any('/postDokumen', 'TrxRanwalRenjaDokController@postDokumen');
    Route::any('/hapusDokumen', 'TrxRanwalRenjaDokController@hapusDokumen');

    Route::group(['prefix' => 'sesuai', 'middleware' => ['auth', 'menu:501']], function() {
        Route::get('/', 'TrxRenjaRanwalSesuaiController@index');
        Route::get('/getUnitRenja', 'TrxRenjaRanwalSesuaiController@getUnit');
        Route::get('/getProgramRkpd/{tahun}/{unit}', 'TrxRenjaRanwalSesuaiController@getProgramRkpd');
        Route::get('/getProgramRenja/{tahun}/{unit}/{ranwal}', 'TrxRenjaRanwalSesuaiController@getProgramRenja');
        Route::get('/getIndikatorRenja/{id_program}', 'TrxRenjaRanwalSesuaiController@getIndikatorRenja');
        Route::get('/getKegiatanRenja/{id_program}', 'TrxRenjaRanwalSesuaiController@getKegiatanRenja');
        Route::get('/getIndikatorKegiatan/{id_renja}', 'TrxRenjaRanwalSesuaiController@getIndikatorKegiatan');

        Route::get('/getCheckProgram/{id_program}', 'TrxRenjaRanwalSesuaiController@getCheckProgram');
        Route::get('/getCheckKegiatan/{id_renja}', 'TrxRenjaRanwalSesuaiController@getCheckKegiatan');
        Route::get('/CheckProgram/{id_program}', 'TrxRenjaRanwalSesuaiController@CheckProgram');
        Route::get('/CheckKegiatan/{id_renja}', 'TrxRenjaRanwalSesuaiController@CheckKegiatan');


        Route::get('/getProgRenstra/{id_unit}', 'TrxRenjaRanwalSesuaiController@getProgRenstra');
        Route::get('/getKegRenstra/{id_unit}/{id_program}', 'TrxRenjaRanwalSesuaiController@getKegRenstra');
        Route::get('/getProgRef/{id_bidang}', 'TrxRenjaRanwalSesuaiController@getProgRef');
        Route::get('/getKegRef/{id_program}', 'TrxRenjaRanwalSesuaiController@getKegRef');
        Route::get('/getUrusan', 'TrxRenjaRanwalSesuaiController@getUrusan');
        Route::get('/getBidang/{id_unit}/{id_ranwal}', 'TrxRenjaRanwalSesuaiController@getBidang');
        Route::get('/getRefIndikator', 'TrxRenjaRanwalSesuaiController@getRefIndikator');

        Route::get('/getAktivitas/{id_pelaksana}', 'TrxRenjaRanwalSesuaiController@getAktivitas');
        Route::get('/getPelaksanaAktivitas/{id_renja}', 'TrxRenjaRanwalSesuaiController@getPelaksanaAktivitas');

        Route::post('/addProgram', 'TrxRenjaRanwalSesuaiController@addProgramRenja');
        Route::post('/editProgram', 'TrxRenjaRanwalSesuaiController@editProgram');
        Route::post('/postProgram', 'TrxRenjaRanwalSesuaiController@postProgram');
        Route::post('/hapusProgram', 'TrxRenjaRanwalSesuaiController@hapusProgram');

        Route::post('/addKegiatanRenja', 'TrxRenjaRanwalSesuaiController@addKegiatanRenja');
        Route::post('/editKegiatanRenja', 'TrxRenjaRanwalSesuaiController@editKegiatanRenja');
        Route::post('/postKegiatanRenja', 'TrxRenjaRanwalSesuaiController@postKegiatanRenja');
        Route::post('/postKegiatanRanwal', 'TrxRenjaRanwalSesuaiController@postKegiatanRanwal');
        Route::post('/hapusKegiatanRenja', 'TrxRenjaRanwalSesuaiController@hapusKegiatanRenja');

        Route::post('/addIndikatorRenja', 'TrxRenjaRanwalSesuaiController@addIndikatorRenja');
        Route::post('/editIndikatorRenja', 'TrxRenjaRanwalSesuaiController@editIndikatorRenja');
        Route::post('/hapusIndikatorRenja', 'TrxRenjaRanwalSesuaiController@hapusIndikatorRenja');

        Route::post('/addIndikatorKeg', 'TrxRenjaRanwalSesuaiController@addIndikatorKeg');
        Route::post('/editIndikatorKeg', 'TrxRenjaRanwalSesuaiController@editIndikatorKeg');
        Route::post('/hapusIndikatorKeg', 'TrxRenjaRanwalSesuaiController@hapusIndikatorKeg');

        Route::post('/addAktivitas', 'TrxRenjaRanwalSesuaiController@addAktivitas');
        Route::post('/editAktivitas', 'TrxRenjaRanwalSesuaiController@editAktivitas');
        Route::post('/hapusAktivitas', 'TrxRenjaRanwalSesuaiController@hapusAktivitas');
        Route::get('/getHitungPaguASB', 'TrxRenjaRanwalSesuaiController@getHitungPaguASB');


        Route::post('/addPelaksana', 'TrxRenjaRanwalSesuaiController@addPelaksana');
        Route::post('/editPelaksana', 'TrxRenjaRanwalSesuaiController@editPelaksana');
        Route::post('/hapusPelaksana', 'TrxRenjaRanwalSesuaiController@hapusPelaksana');

    });
    
});

//Renja
Route::group(['prefix' => 'renja', 'middleware' => ['auth', 'menu:50']], function () {
    Route::any('/loadData', 'TrxRenjaRancanganController@loadData')->middleware('auth', 'menu:501');
    Route::get('/getUnitRenja', 'TrxRenjaRancanganController@getUnit');
    Route::get('/getSelectProgram/{unit}/{tahun}', 'TrxRenjaRancanganController@getSelectProgram');
    Route::get('/getProgramRkpd/{tahun}/{unit}', 'TrxRenjaRancanganController@getProgramRkpd');
    Route::get('/getRekapProgram/{tahun}/{unit}', 'TrxRenjaRancanganController@getRekapProgram');

    Route::get('/getTransProgram/{tahun}/{unit}', 'TrxRenjaRancanganController@getTransProgram');
    Route::post('/transProgramRKPD', 'TrxRenjaRancanganController@transProgramRKPD');
    Route::post('/unloadRenja', 'TrxRenjaRancanganController@unloadRenja');
    Route::post('/transProgramRenja', 'TrxRenjaRancanganController@transProgramRenja');
    Route::any('/transProgramIndikatorRenja', 'TrxRenjaRancanganController@transProgramIndikatorRenja');

    Route::get('/getTahunKe/{tahun}', 'TrxRenjaRancanganController@getTahunKe');

    Route::get('/getTransKegiatan/{tahun_renja}/{id_tahun}/{id_program_renstra}', 'TrxRenjaRancanganController@getTransKegiatan');
    Route::any('/transKegiatanRenja', 'TrxRenjaRancanganController@transKegiatanRenja');
    Route::any('/transKegiatanIndikatorRenja', 'TrxRenjaRancanganController@transKegiatanIndikatorRenja');
    
    Route::get('/', 'TrxRenjaController@index');
    Route::get('/blangsung', 'TrxRenjaController@belanjalangsung');

    Route::get('/program/{tahun_renja}/{id_unit}', 'TrxRenjaController@getProgramRenja');
    Route::get('/programindikator/{tahun_renja}/{id_unit}/{id_program}', 'TrxRenjaController@getProgramIndikatorRenja');
    Route::get('/kegiatanrenja/{tahun_renja}/{id_unit}/{id_program}', 'TrxRenjaController@getKegiatanRenja');
    Route::get('/kegiatanindikatorenja/{tahun_renja}/{id_unit}/{id_renja}', 'TrxRenjaController@getKegiatanIndikatorRenja');
    Route::get('/aktivitasrenja/{tahun_renja}/{id_unit}/{id_renja}', 'TrxRenjaController@getAktivitasRenja');

    Route::get('/dokumen', 'TrxRenjaRancanganDokController@index');
    Route::get('/getDataDokumen/{id_unit}', 'TrxRenjaRancanganDokController@getDataDokumen');
    Route::any('/addDokumen', 'TrxRenjaRancanganDokController@addDokumen');
    Route::any('/editDokumen', 'TrxRenjaRancanganDokController@editDokumen');
    Route::any('/postDokumen', 'TrxRenjaRancanganDokController@postDokumen');
    Route::any('/hapusDokumen', 'TrxRenjaRancanganDokController@hapusDokumen');

    //Load dan Proses Renja
    Route::group(['prefix' => 'sesuai', 'middleware' => ['auth', 'menu:501']], function() {
        Route::get('/', 'TrxRenjaRancanganPenyesuaianController@index');
        Route::get('/getUnitRenja', 'TrxRenjaRancanganPenyesuaianController@getUnit');
        Route::get('/getProgramRkpd/{tahun}/{unit}', 'TrxRenjaRancanganPenyesuaianController@getProgramRkpd');
        Route::get('/getProgramRenja/{tahun}/{unit}/{ranwal}', 'TrxRenjaRancanganPenyesuaianController@getProgramRenja');
        Route::get('/getIndikatorRenja/{id_program}', 'TrxRenjaRancanganPenyesuaianController@getIndikatorRenja');
        Route::get('/getKegiatanRenja/{id_program}', 'TrxRenjaRancanganPenyesuaianController@getKegiatanRenja');
        Route::get('/getIndikatorKegiatan/{id_renja}', 'TrxRenjaRancanganPenyesuaianController@getIndikatorKegiatan');

        Route::get('/getCheckProgram/{id_program}', 'TrxRenjaRancanganPenyesuaianController@getCheckProgram');
        Route::get('/getCheckKegiatan/{id_renja}', 'TrxRenjaRancanganPenyesuaianController@getCheckKegiatan');
        Route::get('/CheckProgram/{id_program}', 'TrxRenjaRancanganPenyesuaianController@CheckProgram');
        Route::get('/CheckKegiatan/{id_renja}', 'TrxRenjaRancanganPenyesuaianController@CheckKegiatan');

        Route::get('/getProgRenstra/{id_unit}', 'TrxRenjaRancanganPenyesuaianController@getProgRenstra');
        Route::get('/getKegRenstra/{id_unit}/{id_program}', 'TrxRenjaRancanganPenyesuaianController@getKegRenstra');
        Route::get('/getProgRef/{id_bidang}', 'TrxRenjaRancanganPenyesuaianController@getProgRef');
        Route::get('/getKegRef/{id_program}', 'TrxRenjaRancanganPenyesuaianController@getKegRef');
        Route::get('/getUrusan', 'TrxRenjaRancanganPenyesuaianController@getUrusan');
        Route::get('/getBidang/{id_unit}/{id_ranwal}', 'TrxRenjaRancanganPenyesuaianController@getBidang');
        Route::get('/getRefIndikator', 'TrxRenjaRancanganPenyesuaianController@getRefIndikator');

        Route::post('/addProgram', 'TrxRenjaRancanganPenyesuaianController@addProgramRenja');
        Route::post('/editProgram', 'TrxRenjaRancanganPenyesuaianController@editProgram');
        Route::post('/postProgram', 'TrxRenjaRancanganPenyesuaianController@postProgram');
        Route::post('/hapusProgram', 'TrxRenjaRancanganPenyesuaianController@hapusProgram');

        Route::post('/addKegiatanRenja', 'TrxRenjaRancanganPenyesuaianController@addKegiatanRenja');
        Route::post('/editKegiatanRenja', 'TrxRenjaRancanganPenyesuaianController@editKegiatanRenja');
        Route::post('/postKegiatanRenja', 'TrxRenjaRancanganPenyesuaianController@postKegiatanRenja');
        Route::post('/hapusKegiatanRenja', 'TrxRenjaRancanganPenyesuaianController@hapusKegiatanRenja');

        Route::post('/addIndikatorRenja', 'TrxRenjaRancanganPenyesuaianController@addIndikatorRenja');
        Route::post('/editIndikatorRenja', 'TrxRenjaRancanganPenyesuaianController@editIndikatorRenja');
        Route::post('/hapusIndikatorRenja', 'TrxRenjaRancanganPenyesuaianController@hapusIndikatorRenja');

        Route::post('/addIndikatorKeg', 'TrxRenjaRancanganPenyesuaianController@addIndikatorKeg');
        Route::post('/editIndikatorKeg', 'TrxRenjaRancanganPenyesuaianController@editIndikatorKeg');
        Route::post('/hapusIndikatorKeg', 'TrxRenjaRancanganPenyesuaianController@hapusIndikatorKeg');
    });

    Route::group(['prefix' => 'blang', 'middleware' => ['auth', 'menu:501']], function() {
        
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

        Route::get('/getIndikatorRenja/{id_program}', 'TrxRenjaRancanganBLangsungController@getIndikatorRenja');

        Route::post('/getHitungASB', 'TrxRenjaRancanganBLController@getHitungASB');
        Route::get('/getHitungASBMusren', 'TrxRenjaRancanganBLController@getHitungASBMusren');
        Route::post('/unloadASB', 'TrxRenjaRancanganBLController@unloadASB');
        
        Route::get('/getBelanjaCopy/{id_lokasi}', 'TrxRenjaRancanganBLController@getBelanjaCopy');
        
        Route::get('/getProgramDapatRenja/{tahun}/{unit}', 'TrxRenjaRancanganBLangsungController@getProgramDapatRenja');
        Route::get('/getProgramBtlRenja/{tahun}/{unit}', 'TrxRenjaRancanganBLangsungController@getProgramBtlRenja');

        Route::get('/getLokasiAktivitas/{id_pelaksana}', 'TrxRenjaRancanganBLController@getLokasiAktivitas');
        Route::get('/getBelanja/{id_lokasi}', 'TrxRenjaRancanganBLController@getBelanja');
        
        Route::get('/getLokasiCopy/{id_aktivitas}', 'TrxRenjaRancanganBLController@getLokasiCopy');
        Route::post('/getBelanjaCopy', 'TrxRenjaRancanganBLController@getBelanjaCopy');

        Route::get('/getPaguPelaksana/{tahun}/{aktivitas}', 'TrxRenjaRancanganBLController@getPaguPelaksana');

        Route::get('/', 'TrxRenjaRancanganBLController@index');
        Route::get('/getUnitRenja', 'TrxRenjaRancanganBLController@getUnit');
        Route::get('/getProgramRkpd/{tahun}/{unit}', 'TrxRenjaRancanganBLController@getProgramRkpd');
        Route::get('/getProgramRenja/{tahun}/{unit}', 'TrxRenjaRancanganBLController@getProgramRenja');
        Route::get('/getIndikatorRenja/{id_program}', 'TrxRenjaRancanganBLController@getIndikatorRenja');
        Route::get('/getKegiatanRenja/{id_program}', 'TrxRenjaRancanganBLController@getKegiatanRenja');
        Route::get('/getIndikatorKegiatan/{id_renja}', 'TrxRenjaRancanganBLController@getIndikatorKegiatan');

        Route::get('/getCheckProgram/{id_program}', 'TrxRenjaRancanganBLController@getCheckProgram');
        Route::get('/getCheckKegiatan/{id_renja}', 'TrxRenjaRancanganBLController@getCheckKegiatan');
        Route::get('/CheckProgram/{id_program}', 'TrxRenjaRancanganBLController@CheckProgram');
        Route::get('/CheckKegiatan/{id_renja}', 'TrxRenjaRancanganBLController@CheckKegiatan');


        Route::get('/getProgRenstra/{id_unit}', 'TrxRenjaRancanganBLController@getProgRenstra');
        Route::get('/getKegRenstra/{id_unit}/{id_program}', 'TrxRenjaRancanganBLController@getKegRenstra');
        Route::get('/getProgRef/{id_bidang}', 'TrxRenjaRancanganBLController@getProgRef');
        Route::get('/getKegRef/{id_program}', 'TrxRenjaRancanganBLController@getKegRef');
        Route::get('/getUrusan', 'TrxRenjaRancanganBLController@getUrusan');
        Route::get('/getBidang/{id_unit}/{id_ranwal}', 'TrxRenjaRancanganBLController@getBidang');
        Route::get('/getRefIndikator', 'TrxRenjaRancanganBLController@getRefIndikator');

        Route::get('/getAktivitas/{id_pelaksana}', 'TrxRenjaRancanganBLController@getAktivitas');
        Route::get('/getPelaksanaAktivitas/{id_renja}', 'TrxRenjaRancanganBLController@getPelaksanaAktivitas');

        Route::post('/addProgram', 'TrxRenjaRancanganBLController@addProgramRenja');
        Route::post('/editProgram', 'TrxRenjaRancanganBLController@editProgram');
        Route::post('/postProgram', 'TrxRenjaRancanganBLController@postProgram');
        Route::post('/hapusProgram', 'TrxRenjaRancanganBLController@hapusProgram');

        Route::post('/addKegiatanRenja', 'TrxRenjaRancanganBLController@addKegiatanRenja');
        Route::post('/editKegiatanRenja', 'TrxRenjaRancanganBLController@editKegiatanRenja');
        Route::post('/postKegiatanRenja', 'TrxRenjaRancanganBLController@postKegiatanRenja');
        Route::post('/hapusKegiatanRenja', 'TrxRenjaRancanganBLController@hapusKegiatanRenja');

        Route::post('/addIndikatorRenja', 'TrxRenjaRancanganBLController@addIndikatorRenja');
        Route::post('/editIndikatorRenja', 'TrxRenjaRancanganBLController@editIndikatorRenja');
        Route::post('/hapusIndikatorRenja', 'TrxRenjaRancanganBLController@hapusIndikatorRenja');

        Route::post('/addIndikatorKeg', 'TrxRenjaRancanganBLController@addIndikatorKeg');
        Route::post('/editIndikatorKeg', 'TrxRenjaRancanganBLController@editIndikatorKeg');
        Route::post('/hapusIndikatorKeg', 'TrxRenjaRancanganBLController@hapusIndikatorKeg');

        Route::post('/addAktivitas', 'TrxRenjaRancanganBLController@addAktivitas');
        Route::post('/editAktivitas', 'TrxRenjaRancanganBLController@editAktivitas');
        Route::post('/hapusAktivitas', 'TrxRenjaRancanganBLController@hapusAktivitas');
        Route::post('/postAktivitas', 'TrxRenjaRancanganBLController@postAktivitas');
        Route::get('/getHitungPaguASB', 'TrxRenjaRancanganBLController@getHitungPaguASB');


        Route::post('/addPelaksana', 'TrxRenjaRancanganBLController@addPelaksana');
        Route::post('/editPelaksana', 'TrxRenjaRancanganBLController@editPelaksana');
        Route::post('/hapusPelaksana', 'TrxRenjaRancanganBLController@hapusPelaksana');

        Route::post('/addLokasi', 'TrxRenjaRancanganBLController@addLokasi');
        Route::post('/editLokasi', 'TrxRenjaRancanganBLController@editLokasi');
        Route::post('/hapusLokasi', 'TrxRenjaRancanganBLController@hapusLokasi');

        Route::post('/addBelanja', 'TrxRenjaRancanganBLController@addBelanja');
        Route::post('/editBelanja', 'TrxRenjaRancanganBLController@editBelanja');
        Route::post('/hapusBelanja', 'TrxRenjaRancanganBLController@hapusBelanja');
    });

    // pdt
    Route::group(['prefix' => 'dapat', 'middleware' => ['auth', 'menu:501']], function() {
        Route::get('/', 'TrxRenjaRancanganBLangsungController@index_dapat');
    	// Route::get('/', 'TrxRenjaController@pdt');
        
        Route::get('/{id}/pelaksana', 'TrxRenjaController@pdtpelaksana');
        Route::any('/{id}/pelaksana/tambah', 'TrxRenjaController@pdtpelaksanatambah');
        Route::delete('/{id}/pelaksana/{unit_id}/delete', 'TrxRenjaController@pdtpelaksanadelete');
        
        Route::get('/{id}/pelaksana/{sub_unit_id}/belanja', 'TrxRenjaController@pdtpelaksanabelanja');
        Route::get('/{id}/pelaksana/{sub_unit_id}/belanja/tambah', 'TrxRenjaController@pdtpelaksanabelanjatambah');
        Route::get('/{id}/pelaksana/{sub_unit_id}/belanja/{belanja_id}/ubah', 'TrxRenjaController@pdtpelaksanabelanjaubah');
        Route::delete('/{id}/pelaksana/{sub_unit_id}/belanja/{belanja_id}/delete', 'TrxRenjaController@pdtpelaksanabelanjadelete');    

        Route::any('/{id}/indikator/tambah', 'TrxRenjaController@pdtindikatortambah');
        Route::any('/{id}/indikator/{indikator_id}/ubah', 'TrxRenjaController@pdtindikatorubah');
        Route::delete('/{id}/indikator/{indikator_id}/delete', 'TrxRenjaController@pdtindikatordelete');

        Route::any('/{id}/kebijakan/tambah', 'TrxRenjaController@pdtkebijakantambah');
        Route::any('/{id}/kebijakan/{kebijakan_id}/ubah', 'TrxRenjaController@pdtkebijakanubah');
        Route::delete('/{id}/kebijakan/{kebijakan_id}/delete', 'TrxRenjaController@pdtkebijakandelete');
    }); 

    // btl
    Route::group(['prefix' => 'btl', 'middleware' => ['auth', 'menu:501']], function() {
        Route::get('/', 'TrxRenjaRancanganBLangsungController@index_btl');
    	// Route::get('/', 'TrxRenjaController@btl');
        
        Route::get('/{id}/pelaksana', 'TrxRenjaController@btlpelaksana');
        Route::any('/{id}/pelaksana/tambah', 'TrxRenjaController@btlpelaksanatambah');
        Route::delete('/{id}/pelaksana/{unit_id}/delete', 'TrxRenjaController@btlpelaksanadelete');
        
        Route::get('/{id}/pelaksana/{sub_unit_id}/belanja', 'TrxRenjaController@btlpelaksanabelanja');
        Route::get('/{id}/pelaksana/{sub_unit_id}/belanja/tambah', 'TrxRenjaController@btlpelaksanabelanjatambah');
        Route::get('/{id}/pelaksana/{sub_unit_id}/belanja/{belanja_id}/ubah', 'TrxRenjaController@btlpelaksanabelanjaubah');
        Route::delete('/{id}/pelaksana/{sub_unit_id}/belanja/{belanja_id}/delete', 'TrxRenjaController@btlpelaksanabelanjadelete');

        Route::any('/{id}/indikator/tambah', 'TrxRenjaController@btlindikatortambah');
        Route::any('/{id}/indikator/{indikator_id}/ubah', 'TrxRenjaController@btlindikatorubah');
        Route::delete('/{id}/indikator/{indikator_id}/delete', 'TrxRenjaController@btlindikatordelete');

        Route::any('/{id}/kebijakan/tambah', 'TrxRenjaController@btlkebijakantambah');
        Route::any('/{id}/kebijakan/{kebijakan_id}/ubah', 'TrxRenjaController@btlkebijakanubah');
        Route::delete('/{id}/kebijakan/{kebijakan_id}/delete', 'TrxRenjaController@btlkebijakandelete');
    });        
});

Route::group(['prefix' => 'renjafinal', 'middleware' => ['auth', 'menu:50']], function () {
    Route::get('/', 'TrxRenjaFinalController@index');
    Route::get('/loadData', 'TrxRenjaFinalController@loadData');
    Route::get('/dokumen', 'TrxRenjaFinalController@dokumen');  
    Route::get('/blangsung', 'TrxRenjaFinalController@blangsung');       
});