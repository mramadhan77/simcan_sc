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

//ASB
Route::get('/modul5', function () {return view('layouts.app4');})->middleware('auth');

//Dashboard
Route::any('/kin', 'Kin\KinRefSotkController@index_sakip')->middleware('auth');

// Indikator Kinerja Utama
Route::group(['prefix' => '/iku','middleware' => ['auth','menu:92']], function () {
    Route::any('/', 'Kin\KinIkuPemdaController@index');
    Route::any('/getDokumen', 'Kin\KinIkuPemdaController@getDokumen');
    Route::any('/getUnit', 'Kin\KinIkuPemdaController@getUnit');
    Route::any('/addDokumen', 'Kin\KinIkuPemdaController@addDokumen');
    Route::any('/editDokumen', 'Kin\KinIkuPemdaController@editDokumen');
    Route::any('/delDokumen', 'Kin\KinIkuPemdaController@delDokumen');
    Route::any('/transIndikatorSasaran', 'Kin\KinIkuPemdaController@transIndikatorSasaran');    
    Route::any('/getSasaran/{id_dokumen}', 'Kin\KinIkuPemdaController@getSasaran');
    Route::any('/getIndikatorSasaran/{id_sasaran}', 'Kin\KinIkuPemdaController@getIndikatorSasaran');
    Route::any('/editIndikatorSasaran', 'Kin\KinIkuPemdaController@editIndikatorSasaran');

    Route::group(['prefix' => '/opd','middleware' => ['auth','menu:92']], function () {
        Route::any('/', 'Kin\KinIkuOpdController@index');
        Route::any('/getDokumen/{id_unit}', 'Kin\KinIkuOpdController@getDokumen');
        Route::any('/addDokumen', 'Kin\KinIkuOpdController@addDokumen');
        Route::any('/editDokumen', 'Kin\KinIkuOpdController@editDokumen');
        Route::any('/delDokumen', 'Kin\KinIkuOpdController@delDokumen');
        Route::any('/transIndikatorSasaran', 'Kin\KinIkuOpdController@transIndikatorSasaran');    
        Route::any('/getSasaran/{id_dokumen}', 'Kin\KinIkuOpdController@getSasaran');
        Route::any('/getIndikatorSasaran/{id_sasaran}', 'Kin\KinIkuOpdController@getIndikatorSasaran');
        Route::any('/editIndikatorSasaran', 'Kin\KinIkuOpdController@editIndikatorSasaran');
        Route::any('/getProgram/{id_dokumen}', 'Kin\KinIkuOpdController@getProgram');
        Route::any('/getIndikatorProgram/{id_sasaran}', 'Kin\KinIkuOpdController@getIndikatorProgram');
        Route::any('/getKegiatan/{id_dokumen}', 'Kin\KinIkuOpdController@getKegiatan');
        Route::any('/getIndikatorKegiatan/{id_sasaran}', 'Kin\KinIkuOpdController@getIndikatorKegiatan');
        Route::any('/editIndikatorProgram', 'Kin\KinIkuOpdController@editIndikatorProgram');
        Route::any('/editIndikatorKegiatan', 'Kin\KinIkuOpdController@editIndikatorKegiatan');
        Route::any('/getEselon3/{id_unit}', 'Kin\KinIkuOpdController@getEselon3');
        Route::any('/getEselon4/{id_unit}', 'Kin\KinIkuOpdController@getEselon4');

    });

});

// cascading
Route::group(['prefix' => '/cascading','middleware' => ['auth','menu:93']], function () {
    Route::any('/', 'Kin\TrxRenstraKinController@index');
    Route::any('/getTujuanRenstra/{id_unit}', 'Kin\TrxRenstraKinController@getTujuanRenstra');
    Route::any('/getIndikatorTujuan/{id_tujuan}', 'Kin\TrxRenstraKinController@getIndikatorTujuan');
    Route::any('/getSasaranRenstra/{id_tujuan}', 'Kin\TrxRenstraKinController@getSasaranRenstra');
    Route::any('/getIndikatorSasaran/{id_sasaran}', 'Kin\TrxRenstraKinController@getIndikatorSasaran');
    Route::any('/getSasaranProgram/{id_sasaran_renstra}', 'Kin\TrxRenstraKinController@getSasaranProgram');
    Route::any('/getIndikatorProgram/{id_sasaran}', 'Kin\TrxRenstraKinController@getIndikatorProgram');
    Route::post('/addHasilProgram','Kin\TrxRenstraKinController@addHasilProgram');
    Route::post('/editHasilProgram','Kin\TrxRenstraKinController@editHasilProgram');
    Route::post('/delHasilProgram','Kin\TrxRenstraKinController@delHasilProgram');
    Route::get('/getProgramRenstra/{id_sasaran_renstra}', 'Kin\TrxRenstraKinController@getProgramRenstra');
    Route::get('/getProgramIndikatorRenstra/{id_sasaran_renstra}', 'Kin\TrxRenstraKinController@getProgramIndikatorRenstra');
    Route::post('/addIndikatorProgram','Kin\TrxRenstraKinController@addIndikatorProgram');
    Route::post('/delIndikatorProgram','Kin\TrxRenstraKinController@delIndikatorProgram');
    Route::any('/getSasaranKegiatan/{id_sasaran_renstra}', 'Kin\TrxRenstraKinController@getSasaranKegiatan');
    Route::any('/getIndikatorKegiatan/{id_sasaran}', 'Kin\TrxRenstraKinController@getIndikatorKegiatan');
    Route::post('/addHasilKegiatan','Kin\TrxRenstraKinController@addHasilKegiatan');
    Route::post('/editHasilKegiatan','Kin\TrxRenstraKinController@editHasilKegiatan');
    Route::post('/delHasilKegiatan','Kin\TrxRenstraKinController@delHasilKegiatan');
    Route::get('/getKegiatanRenstra/{id_sasaran_renstra}', 'Kin\TrxRenstraKinController@getKegiatanRenstra');
    Route::get('/getKegiatanIndikatorRenstra/{id_sasaran_renstra}', 'Kin\TrxRenstraKinController@getKegiatanIndikatorRenstra');
    Route::post('/addIndikatorKegiatan','Kin\TrxRenstraKinController@addIndikatorKegiatan');
    Route::post('/delIndikatorKegiatan','Kin\TrxRenstraKinController@delIndikatorKegiatan');
});

// Pohon Kinerja
Route::group(['prefix' => '/pokin','middleware' => ['auth','menu:93']], function () {
        Route::any('/', 'Kin\KinPokinController@index');
        Route::any('/jenis_pokin', 'Kin\KinPokinController@jenis_pokin');
        Route::any('/getRpjmdChart/{id_rpjmd}', 'Kin\KinPokinController@indexChart');        
        Route::any('/getRenstraChart/{id_renstra}/{id_unit}', 'Kin\KinPokinController@indexChartPD');             
        Route::any('/getRenstraSasaranChart/{id_renstra}/{id_unit}', 'Kin\KinPokinController@indexChartSasaranPD');       
        Route::any('/getLintasChart/{id_rpjmd}', 'Kin\KinPokinController@indexChartLintas');
});

// Parameter AKIP
Route::group(['prefix' => '/kinparam','middleware' => ['auth','menu:91']], function () {
    Route::group(['prefix' => '/sotk','middleware' => ['auth','menu:91']], function () {
        Route::any('/', 'Kin\KinRefSotkController@index');
        Route::any('/getUnitSotk', 'Kin\KinRefSotkController@getUnitSotk');
        Route::any('/getSotkLevel1/{id_unit}', 'Kin\KinRefSotkController@getSotkLevel1');
        Route::any('/addLevel1', 'Kin\KinRefSotkController@addLevel1');
        Route::any('/editLevel1', 'Kin\KinRefSotkController@editLevel1');
        Route::any('/delLevel1', 'Kin\KinRefSotkController@delLevel1');
        Route::any('/getSotkLevel2/{id_eselon2}', 'Kin\KinRefSotkController@getSotkLevel2');
        Route::any('/addLevel2', 'Kin\KinRefSotkController@addLevel2');
        Route::any('/editLevel2', 'Kin\KinRefSotkController@editLevel2');
        Route::any('/delLevel2', 'Kin\KinRefSotkController@delLevel2');
        Route::any('/getSotkLevel3/{id_eselon2}', 'Kin\KinRefSotkController@getSotkLevel3');
        Route::any('/addLevel3', 'Kin\KinRefSotkController@addLevel3');
        Route::any('/editLevel3', 'Kin\KinRefSotkController@editLevel3');
        Route::any('/delLevel3', 'Kin\KinRefSotkController@delLevel3');
        Route::any('/getSotkChart/{id_unit}', 'Kin\KinRefSotkController@indexChart');
    });
    Route::group(['prefix' => '/pegawai','middleware' => ['auth','menu:91']], function () {
        Route::any('/', 'Kin\KinRefPegawaiController@index');
        Route::any('/getPegawai', 'Kin\KinRefPegawaiController@getPegawai');
        Route::any('/getPegawaiPangkat/{id_pegawai}', 'Kin\KinRefPegawaiController@getPegawaiPangkat');
        Route::any('/getPegawaiUnit/{id_pegawai}', 'Kin\KinRefPegawaiController@getPegawaiUnit');
        Route::any('/getSotkLevel/{id_unit}/{id_level}', 'Kin\KinRefPegawaiController@getSotkLevel');
        Route::any('/addPegawai', 'Kin\KinRefPegawaiController@addPegawai');
        Route::any('/editPegawai', 'Kin\KinRefPegawaiController@editPegawai');
        Route::any('/delPegawai', 'Kin\KinRefPegawaiController@delPegawai');
        Route::any('/jenis_pangkat', 'Kin\KinRefPegawaiController@jenis_pangkat');
        Route::any('/addPangkat', 'Kin\KinRefPegawaiController@addPangkat');
        Route::any('/editPangkat', 'Kin\KinRefPegawaiController@editPangkat');
        Route::any('/delPangkat', 'Kin\KinRefPegawaiController@delPangkat');
        Route::any('/addUnitJabatan', 'Kin\KinRefPegawaiController@addUnitJabatan');
        Route::any('/editUnitJabatan', 'Kin\KinRefPegawaiController@editUnitJabatan');
        Route::any('/delUnitJabatan', 'Kin\KinRefPegawaiController@delUnitJabatan');
    });
    
});

// Perjanjian Kinerja
Route::group(['prefix' => '/perkin','middleware' => ['auth','menu:93']], function () {
    Route::any('/', 'Kin\KinPerkinEs2Controller@index');
    Route::any('/getUnit', 'Kin\KinPerkinEs2Controller@getUnit');
    Route::any('/getDokumen/{id_unit}', 'Kin\KinPerkinEs2Controller@getDokumen');
    Route::any('/getJabatan/{id_unit}', 'Kin\KinPerkinEs2Controller@getJabatan');
    Route::any('/getPegawai/{id_jabatan}', 'Kin\KinPerkinEs2Controller@getPegawai');
    Route::any('/getPejabat/{id_pegawai}', 'Kin\KinPerkinEs2Controller@getPejabat');
    Route::any('/addDokumen', 'Kin\KinPerkinEs2Controller@addDokumen');
    Route::any('/editDokumen', 'Kin\KinPerkinEs2Controller@editDokumen');
    Route::any('/delDokumen', 'Kin\KinPerkinEs2Controller@delDokumen');
    Route::any('/transSasaranRenstra', 'Kin\KinPerkinEs2Controller@transSasaranRenstra');
    Route::any('/getSasaran/{id_dokumen}', 'Kin\KinPerkinEs2Controller@getSasaran');
    Route::any('/getIndikatorSasaran/{id_sasaran}', 'Kin\KinPerkinEs2Controller@getIndikatorSasaran');
    Route::any('/editIndikatorSasaran', 'Kin\KinPerkinEs2Controller@editIndikatorSasaran');
    Route::any('/getProgram/{id_sasaran}', 'Kin\KinPerkinEs2Controller@getProgram');
    Route::any('/getEselon3/{id_eselon}', 'Kin\KinPerkinEs2Controller@getEselon3');
    Route::any('/editProgram', 'Kin\KinPerkinEs2Controller@editProgram');

    Route::group(['prefix' => '/es3','middleware' => ['auth','menu:93']], function () {
        Route::any('/', 'Kin\KinPerkinEs3Controller@index');
        Route::any('/getEselon3/{id_eselon}', 'Kin\KinPerkinEs3Controller@getEselon3');
        Route::any('/getEselon4/{id_eselon}', 'Kin\KinPerkinEs3Controller@getEselon4');
        Route::any('/getDokumenEs2/{id_eselon}/{tahun}', 'Kin\KinPerkinEs3Controller@getDokumenEs2');
        Route::any('/getUnit', 'Kin\KinPerkinEs3Controller@getUnit');
        Route::any('/getDokumen/{id_unit}', 'Kin\KinPerkinEs3Controller@getDokumen');
        Route::any('/getJabatan/{id_unit}', 'Kin\KinPerkinEs3Controller@getJabatan');
        Route::any('/getPegawai', 'Kin\KinPerkinEs3Controller@getPegawai');
        Route::any('/getPejabat/{id_pegawai}', 'Kin\KinPerkinEs3Controller@getPejabat');
        Route::any('/addDokumen', 'Kin\KinPerkinEs3Controller@addDokumen');
        Route::any('/editDokumen', 'Kin\KinPerkinEs3Controller@editDokumen');
        Route::any('/delDokumen', 'Kin\KinPerkinEs3Controller@delDokumen');
        Route::any('/transSasaranRenstra', 'Kin\KinPerkinEs3Controller@transSasaranRenstra');
        Route::any('/getSasaran/{id_dokumen}', 'Kin\KinPerkinEs3Controller@getSasaran');
        Route::any('/getIndikatorSasaran/{id_sasaran}', 'Kin\KinPerkinEs3Controller@getIndikatorSasaran');
        Route::any('/editIndikatorSasaran', 'Kin\KinPerkinEs3Controller@editIndikatorSasaran');
        Route::any('/getProgram/{id_sasaran}', 'Kin\KinPerkinEs3Controller@getProgram');
        Route::any('/editProgram', 'Kin\KinPerkinEs3Controller@editProgram');
    });

    Route::group(['prefix' => '/es4','middleware' => ['auth','menu:93']], function () {
        Route::any('/', 'Kin\KinPerkinEs4Controller@index');
        Route::any('/getEselon3/{id_eselon}', 'Kin\KinPerkinEs4Controller@getEselon3');
        Route::any('/getEselon4/{id_eselon}', 'Kin\KinPerkinEs4Controller@getEselon4');
        Route::any('/getDokumenEs2/{id_eselon}/{tahun}', 'Kin\KinPerkinEs4Controller@getDokumenEs2');
        Route::any('/getUnit', 'Kin\KinPerkinEs4Controller@getUnit');
        Route::any('/getDokumen/{id_unit}', 'Kin\KinPerkinEs4Controller@getDokumen');
        Route::any('/getJabatan/{id_unit}', 'Kin\KinPerkinEs4Controller@getJabatan');
        Route::any('/getPegawai', 'Kin\KinPerkinEs4Controller@getPegawai');
        Route::any('/getPejabat/{id_pegawai}', 'Kin\KinPerkinEs4Controller@getPejabat');
        Route::any('/addDokumen', 'Kin\KinPerkinEs4Controller@addDokumen');
        Route::any('/editDokumen', 'Kin\KinPerkinEs4Controller@editDokumen');
        Route::any('/delDokumen', 'Kin\KinPerkinEs4Controller@delDokumen');
        Route::any('/transSasaranRenstra', 'Kin\KinPerkinEs4Controller@transSasaranRenstra');
        Route::any('/getSasaran/{id_dokumen}', 'Kin\KinPerkinEs4Controller@getSasaran');
        Route::any('/getIndikatorSasaran/{id_sasaran}', 'Kin\KinPerkinEs4Controller@getIndikatorSasaran');
        Route::any('/editIndikatorSasaran', 'Kin\KinPerkinEs4Controller@editIndikatorSasaran');
        Route::any('/getProgram/{id_sasaran}', 'Kin\KinPerkinEs4Controller@getProgram');
        Route::any('/editKegiatan', 'Kin\KinPerkinEs4Controller@editKegiatan');
    });
    
});

Route::group(['prefix' => '/real','middleware' => ['auth','menu:94']], function () {
    Route::any('/', 'Kin\KinRealEs2Controller@index');
    Route::any('/getUnit', 'Kin\KinRealEs2Controller@getUnit');
    Route::any('/getDokumen/{id_unit}', 'Kin\KinRealEs2Controller@getDokumen');
    Route::any('/getJabatan/{id_unit}', 'Kin\KinRealEs2Controller@getJabatan');
    Route::any('/getDokumenEs2/{id_eselon}/{tahun}', 'Kin\KinRealEs2Controller@getDokumenEs2');
    Route::any('/getDokRealEs3/{id_unit}/{tahun}/{triwulan}', 'Kin\KinRealEs2Controller@getDokRealEs3');
    Route::any('/getPegawai', 'Kin\KinRealEs2Controller@getPegawai');
    Route::any('/getPejabat/{id_pegawai}', 'Kin\KinRealEs2Controller@getPejabat');
    Route::any('/addDokumen', 'Kin\KinRealEs2Controller@addDokumen');
    Route::any('/editDokumen', 'Kin\KinRealEs2Controller@editDokumen');
    Route::any('/delDokumen', 'Kin\KinRealEs2Controller@delDokumen');
    Route::any('/transSasaranRenstra', 'Kin\KinRealEs2Controller@transSasaranRenstra');
    Route::any('/getSasaran/{id_dokumen}', 'Kin\KinRealEs2Controller@getSasaran');
    Route::any('/getIndikatorSasaran/{id_sasaran}', 'Kin\KinRealEs2Controller@getIndikatorSasaran');
    Route::any('/editIndikatorSasaran', 'Kin\KinRealEs2Controller@editIndikatorSasaran');
    Route::any('/getProgram/{id_sasaran}', 'Kin\KinRealEs2Controller@getProgram');
    Route::any('/getEselon3/{id_eselon}', 'Kin\KinRealEs2Controller@getEselon3');
    Route::any('/editProgram', 'Kin\KinRealEs2Controller@editProgram');
    Route::any('/getIndikatorProgramEs3/{id_dokumen}', 'Kin\KinRealEs2Controller@getIndikatorProgramEs3');
    Route::any('/reviuRealisasi', 'Kin\KinRealEs2Controller@reviuRealisasi');

    Route::group(['prefix' => '/es3','middleware' => ['auth','menu:94']], function () {
        Route::any('/', 'Kin\KinRealEs3Controller@index');
        Route::any('/getEselon3/{id_eselon}', 'Kin\KinRealEs3Controller@getEselon3');
        Route::any('/getEselon4/{id_eselon}', 'Kin\KinRealEs3Controller@getEselon4');
        Route::any('/getDokumenEs2/{id_eselon}/{tahun}', 'Kin\KinRealEs3Controller@getDokumenEs2');
        Route::any('/getDokRealEs4/{id_unit}/{tahun}/{triwulan}', 'Kin\KinRealEs3Controller@getDokRealEs4');
        Route::any('/getUnit', 'Kin\KinRealEs3Controller@getUnit');
        Route::any('/getDokumen/{id_unit}', 'Kin\KinRealEs3Controller@getDokumen');
        Route::any('/getJabatan/{id_unit}', 'Kin\KinRealEs3Controller@getJabatan');
        Route::any('/getPegawai', 'Kin\KinRealEs3Controller@getPegawai');
        Route::any('/getPejabat/{id_pegawai}', 'Kin\KinRealEs3Controller@getPejabat');
        Route::any('/addDokumen', 'Kin\KinRealEs3Controller@addDokumen');
        Route::any('/editDokumen', 'Kin\KinRealEs3Controller@editDokumen');
        Route::any('/delDokumen', 'Kin\KinRealEs3Controller@delDokumen');
        Route::any('/transSasaranRenstra', 'Kin\KinRealEs3Controller@transSasaranRenstra');
        Route::any('/getSasaran/{id_dokumen}', 'Kin\KinRealEs3Controller@getSasaran');
        Route::any('/getRealSasaran/{id_real_program}', 'Kin\KinRealEs3Controller@getRealSasaran');
        Route::any('/getIndikatorSasaran/{id_sasaran}', 'Kin\KinRealEs3Controller@getIndikatorSasaran');
        Route::any('/getRealIndikator/{id_real_perkin}', 'Kin\KinRealEs3Controller@getRealIndikator');
        Route::any('/editIndikatorSasaran', 'Kin\KinRealEs3Controller@editIndikatorSasaran');
        Route::any('/getProgram/{id_sasaran}', 'Kin\KinRealEs3Controller@getProgram');
        Route::any('/editProgram', 'Kin\KinRealEs3Controller@editProgram');
        Route::any('/getIndikatorKegiatanEs4/{id_dokumen}', 'Kin\KinRealEs3Controller@getIndikatorKegiatanEs4');
        Route::any('/reviuRealisasi', 'Kin\KinRealEs3Controller@reviuRealisasi');
    });

    Route::group(['prefix' => '/es4','middleware' => ['auth','menu:94']], function () {
        Route::any('/', 'Kin\KinRealEs4Controller@index');
        Route::any('/getEselon3/{id_eselon}', 'Kin\KinRealEs4Controller@getEselon3');
        Route::any('/getEselon4/{id_eselon}', 'Kin\KinRealEs4Controller@getEselon4');
        Route::any('/getDokumenEs2/{id_eselon}/{tahun}', 'Kin\KinRealEs4Controller@getDokumenEs2');
        Route::any('/getUnit', 'Kin\KinRealEs4Controller@getUnit');
        Route::any('/getDokumen/{id_unit}', 'Kin\KinRealEs4Controller@getDokumen');
        Route::any('/getJabatan/{id_unit}', 'Kin\KinRealEs4Controller@getJabatan');
        Route::any('/getPegawai', 'Kin\KinRealEs4Controller@getPegawai');
        Route::any('/getPejabat/{id_pegawai}', 'Kin\KinRealEs4Controller@getPejabat');
        Route::any('/addDokumen', 'Kin\KinRealEs4Controller@addDokumen');
        Route::any('/editDokumen', 'Kin\KinRealEs4Controller@editDokumen');
        Route::any('/delDokumen', 'Kin\KinRealEs4Controller@delDokumen');
        Route::any('/transSasaranRenstra', 'Kin\KinRealEs4Controller@transSasaranRenstra');
        Route::any('/getSasaran/{id_dokumen}', 'Kin\KinRealEs4Controller@getSasaran');
        Route::any('/getRealSasaran/{id_real_kegiatan}', 'Kin\KinRealEs4Controller@getRealSasaran');
        Route::any('/getIndikatorSasaran/{id_sasaran}', 'Kin\KinRealEs4Controller@getIndikatorSasaran');
        Route::any('/getRealIndikator/{id_real_perkin}', 'Kin\KinRealEs4Controller@getRealIndikator');
        Route::any('/editIndikatorSasaran', 'Kin\KinRealEs4Controller@editIndikatorSasaran');
        Route::any('/getProgram/{id_sasaran}', 'Kin\KinRealEs4Controller@getProgram');
        Route::any('/editKegiatan', 'Kin\KinRealEs4Controller@editKegiatan');
    });
    
});

// Pelaporan
Route::group(['prefix' => '/lapor','middleware' => ['auth','menu:95']], function () {
    Route::any('/', 'Kin\KinReportTapkinController@index');
    Route::any('/jenis_pokin', 'Kin\KinReportTapkinController@jenis_pokin');
    Route::any('/getTahun', 'Kin\KinReportTapkinController@getTahun');
    Route::any('/getDokIkuPemda', 'Kin\KinReportTapkinController@getDokIkuPemda');
    Route::any('/getDokIkuOPD/{unit}', 'Kin\KinReportTapkinController@getDokIkuOPD');
    Route::any('/getSotkLevel1/{unit}', 'Kin\KinReportTapkinController@getSotkLevel1');
    Route::any('/getSotkLevel2/{unit}', 'Kin\KinReportTapkinController@getSotkLevel2');
    Route::any('/getSotkLevel3/{unit}', 'Kin\KinReportTapkinController@getSotkLevel3');
    Route::any('/CetakMatrikRpjmd/{dok}', 'Kin\CetakMatrikRpjmdController@printMatrikRpjmd');
    Route::any('/CetakRenstra/{unit}/{dok}', 'Kin\CetakMatrikRenstraController@printRenstra');
    Route::any('/CetakIkuPemda/{id_rpjmd}', 'Kin\CetakIkuPemdaController@IKUSasaranPemda');
    Route::any('/CetakIkuOPD/{id_dokumen}', 'Kin\CetakIkuOPDController@IKUSasaranOPD');
    Route::any('/CetakIkuProgOPD/{id_dokumen}/{id_eselon}', 'Kin\CetakIkuOPDProgramController@IKUProgramOPD');
    Route::any('/CetakIkuKegOPD/{id_dokumen}/{id_eselon}', 'Kin\CetakIkuOPDKegiatanController@IKUKegiatanOPD');
    Route::any('/CetakRKT/{unit}', 'Kin\CetakSakipController@RKT');
    Route::any('/CetakPerkinPemdaBA', 'Kin\CetakPerkinPemdaController@PerkinPemdaBA');
    Route::any('/CetakPerkinPemdaLamp', 'Kin\CetakPerkinPemdaController@PerkinPemdaLamp');
    Route::any('/CetakPerkinOPDBA', 'Kin\CetakPerkinEs2Controller@PerkinEs2BA');
    Route::any('/CetakPerkinOPDLamp', 'Kin\CetakPerkinEs2Controller@PerkinEs2Lamp');
    Route::any('/CetakPerkinEs3BA', 'Kin\CetakPerkinEs3Controller@PerkinEs3BA');
    Route::any('/CetakPerkinEs3Lamp', 'Kin\CetakPerkinEs3Controller@PerkinEs3Lamp');
    Route::any('/CetakPerkinEs4BA', 'Kin\CetakPerkinEs4Controller@PerkinEs4BA');
    Route::any('/CetakPerkinEs4Lamp', 'Kin\CetakPerkinEs4Controller@PerkinEs4Lamp');
    Route::any('/CetakUkurTriwEs4Lamp', 'Kin\CetakUkurTriwEs4Controller@UkurTriwEs4Lamp');
    Route::any('/CetakUkurTriwEs3Lamp', 'Kin\CetakUkurTriwEs3Controller@UkurTriwEs3Lamp');
    Route::any('/CetakUkurTriwEs2Lamp', 'Kin\CetakUkurTriwEs2Controller@UkurTriwEs2Lamp');
    Route::any('/CetakRenaksi', 'Kin\CetakRenaksiController@Renaksi');
});
