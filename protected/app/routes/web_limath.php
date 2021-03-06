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

//Pra RPJMD
Route::group(['prefix' => 'prarpjmd', 'middleware' => ['auth', 'menu:20']], function() {
    Route::group(['prefix' => 'prioritas', 'middleware' => ['auth', 'menu:20']], function() {
        Route::get('/', 'TrxRpjmdPrioritasController@index');
    });
});

//RPJMD FINAL
Route::group(['prefix' => 'rpjmd', 'middleware' => ['auth', 'menu:20']], function() {
        Route::get('/', 'TrxRpjmdController@index');
        Route::get('/getJnsDokumen', 'TrxRpjmdController@getJnsDokumen');
        Route::get('/getDokumen', 'TrxRpjmdController@getDokumen');
        Route::get('/getDokumenRef', 'TrxRpjmdController@getDokumenRef');
        
        Route::any('/getRpjmdChart/{id_rpjmd}', 'TrxRpjmdController@indexChart');
        Route::get('/pdtRpjmd/{id_visi_rpjmd}','TrxRpjmdController@getPendapatanRPJMD');
        Route::get('/btlRpjmd/{id_visi_rpjmd}','TrxRpjmdController@getBtlRPJMD');
    //RPJMD Dokumen
        Route::get('/getDokumenRpjmd', 'TrxRpjmdController@getDokumenRpjmd');
        Route::post('/addDokumen', 'TrxRpjmdController@addDokumen');
        Route::post('/editDokumen', 'TrxRpjmdController@editDokumen');
        Route::post('/deleteDokumen', 'TrxRpjmdController@deleteDokumen');
        // Route::post('/postingDokumen', 'TrxRpjmdController@postingDokumen');
    //RPJMD Visi        
        Route::get('/visi/{id_rpjmd}', 'TrxRpjmdController@getVisiRPJMD');
        Route::post('/addVisi', ['uses'=>'TrxRpjmdController@addVisi','as'=>'AddVisi']);
        Route::post('/editVisi', ['uses'=>'TrxRpjmdController@editVisi','as'=>'EditVisi']);
        Route::post('/deleteVisi', ['uses'=>'TrxRpjmdController@deleteVisi','as'=>'DeleteVisi']);
    //RPJMD Misi
        Route::get('/misi/{id_visi_rpjmd}','TrxRpjmdController@getMisiRPJMD');
        Route::post('/addMisi', ['uses'=>'TrxRpjmdController@addMisi','as'=>'AddMisi']);
        Route::post('/editMisi', ['uses'=>'TrxRpjmdController@editMisi','as'=>'EditMisi']);
        Route::post('/deleteMisi', ['uses'=>'TrxRpjmdController@deleteMisi','as'=>'DeleteMisi']);
    //RPJMD Tujuan        
        Route::get('/tujuan/{id_misi_rpjmd}','TrxRpjmdController@getTujuanRPJMD');
        Route::post('/addTujuan', ['uses'=>'TrxRpjmdController@addTujuan','as'=>'AddTujuan']);
        Route::post('/editTujuan', ['uses'=>'TrxRpjmdController@editTujuan','as'=>'EditTujuan']);
        Route::post('/deleteTujuan', ['uses'=>'TrxRpjmdController@deleteTujuan','as'=>'DeleteTujuan']);
    //RPJMD Sasaran
        Route::get('/sasaran/{id_tujuan_rpjmd}','TrxRpjmdController@getSasaranRPJMD');
        Route::post('/addSasaran', ['uses'=>'TrxRpjmdController@addSasaran','as'=>'AddSasaran']);
        Route::post('/editSasaran', ['uses'=>'TrxRpjmdController@editSasaran','as'=>'EditSasaran']);
        Route::post('/deleteSasaran', ['uses'=>'TrxRpjmdController@deleteSasaran','as'=>'DeleteSasaran']);
    //RPJMD Kebijakan
        Route::get('/kebijakan/{id_sasaran_rpjmd}','TrxRpjmdController@getKebijakanRPJMD');
        Route::post('/addKebijakan', ['uses'=>'TrxRpjmdController@addKebijakan','as'=>'AddKebijakan']);
        Route::post('/editKebijakan', ['uses'=>'TrxRpjmdController@editKebijakan','as'=>'EditKebijakan']);
        Route::post('/deleteKebijakan', ['uses'=>'TrxRpjmdController@deleteKebijakan','as'=>'DeleteKebijakan']);
    //RPJMD Strategi
        Route::get('/strategi/{id_sasaran_rpjmd}','TrxRpjmdController@getStrategiRPJMD');
        Route::post('/addStrategi', ['uses'=>'TrxRpjmdController@addStrategi','as'=>'AddStrategi']);
        Route::post('/editStrategi', ['uses'=>'TrxRpjmdController@editStrategi','as'=>'EditStrategi']);
        Route::post('/deleteStrategi', ['uses'=>'TrxRpjmdController@deleteStrategi','as'=>'DeleteStrategi']);
    //RPJMD Program
        Route::get('/program/{id_sasaran_rpjmd}','TrxRpjmdController@getProgramRPJMD');
        Route::post('/addProgram', ['uses'=>'TrxRpjmdController@addProgram','as'=>'AddProgram']);
        Route::post('/editProgram', ['uses'=>'TrxRpjmdController@editProgram','as'=>'EditProgram']);
        Route::post('/deleteProgram', ['uses'=>'TrxRpjmdController@deleteProgram','as'=>'DeleteProgram']);
        Route::get('/programindikator/{id_program_rpjmd}','TrxRpjmdController@getIndikatorProgramRPJMD');
        Route::get('/programurusan/{id_program_rpjmd}','TrxRpjmdController@getUrusanProgramRPJMD');
    //RPJMD Urusan    
        Route::get('/getUrusan/{id_program_rpjmd}','TrxRpjmdController@getUrusan');        
        Route::get('/getBidang/{id_urusan}','TrxRpjmdController@getBidang');
        Route::post('/addUrusan', 'TrxRpjmdController@addUrusan');
        Route::post('/editUrusan', 'TrxRpjmdController@editUrusan');
        Route::post('/delUrusan', 'TrxRpjmdController@delUrusan');    
    //RPJMD Pelaksana
        Route::get('/programpelaksana/{id_urbid_rpjmd}','TrxRpjmdController@getPelaksanaProgramRPJMD');
        Route::get('/getUnitPelaksana/{id_program_rpjmd}/{id_bidang}','TrxRpjmdController@getUnitPelaksana');
        Route::post('/addPelaksana', 'TrxRpjmdController@addPelaksana');
        Route::post('/delPelaksana', 'TrxRpjmdController@delPelaksana');
        Route::any('/ReprosesPivotPelaksana', 'TrxRpjmdController@ReprosesPivotPelaksana');
        Route::any('/RePivotRenstra', 'TrxRpjmdController@RePivotRenstra');
    //RPJMD Tujuan Indikator
        Route::get('/getIndikatorTujuan/{id_tujuan_rpjmd}','TrxRpjmdTujuanIndikatorController@getIndikatorTujuan');
        Route::post('/addIndikatorTujuan','TrxRpjmdTujuanIndikatorController@addIndikator');
        Route::post('/editIndikatorTujuan','TrxRpjmdTujuanIndikatorController@editIndikator');
        Route::post('/delIndikatorTujuan','TrxRpjmdTujuanIndikatorController@delIndikator');
     //RPJMD Sasaran Indikator
        Route::get('/getIndikatorSasaran/{id_sasaran_rpjmd}','TrxRpjmdSasaranIndikatorController@getIndikatorSasaran');
        Route::post('/addIndikatorSasaran','TrxRpjmdSasaranIndikatorController@addIndikator');
        Route::post('/editIndikatorSasaran','TrxRpjmdSasaranIndikatorController@editIndikator');
        Route::post('/delIndikatorSasaran','TrxRpjmdSasaranIndikatorController@delIndikator');
    //RPJMD Program Indikator
        Route::get('/getIndikatorProgram/{id_program_rpjmd}','TrxRpjmdProgramIndikatorController@getIndikatorProgram');
        Route::post('/addIndikatorProgram','TrxRpjmdProgramIndikatorController@addIndikator');
        Route::post('/editIndikatorProgram','TrxRpjmdProgramIndikatorController@editIndikator');
        Route::post('/delIndikatorProgram','TrxRpjmdProgramIndikatorController@delIndikator');

    Route::group(['prefix' => 'rancangan', 'middleware' => ['auth', 'menu:20']], function() {
        Route::get('/', 'TrxRpjmdRancanganController@index');
        Route::get('/getDokumen', 'TrxRpjmdRancanganController@getDokumen');
        Route::post('/addDokRpjmd', ['uses'=>'TrxRpjmdRancanganController@addDokRpjmd','as'=>'AddDokRpjmd']);
        Route::post('/editDokRpjmd', ['uses'=>'TrxRpjmdRancanganController@editDokRpjmd','as'=>'editDokRpjmd']);
        Route::post('/delDokRpjmd', ['uses'=>'TrxRpjmdRancanganController@delDokRpjmd','as'=>'delDokRpjmd']);
    });

});

//RENSTRA
Route::group(['prefix' => 'renstra', 'middleware' => ['auth', 'menu:30']], function() {
    Route::get('/', 'TrxRenstraController@index');

    //RENSTRA VISI
    Route::get('/visi/{id_unit}', 'TrxRenstraVisiController@getVisiRenstra');
    //RENSTRA MISI
    Route::get('/misi/{id_visi_renstra}', 'TrxRenstraMisiController@getMisiRenstra');
    //RENSTRA TUJUAN
    Route::get('/tujuan/{id_misi_renstra}', 'TrxRenstraTujuanController@getTujuanRenstra');
    Route::post('/addTujuanRenstra', 'TrxRenstraTujuanController@addTujuanRenstra');
    Route::post('/editTujuanRenstra', 'TrxRenstraTujuanController@editTujuanRenstra');
    Route::post('/hapusTujuanRenstra', 'TrxRenstraTujuanController@hapusTujuanRenstra');
    Route::get('/getIndikatorTujuan/{id_tujuan_renstra}','TrxRenstraTujuanIndikatorController@getIndikatorTujuan');
    Route::post('/addIndikatorTujuan','TrxRenstraTujuanIndikatorController@addIndikator');
    Route::post('/editIndikatorTujuan','TrxRenstraTujuanIndikatorController@editIndikator');
    Route::post('/delIndikatorTujuan','TrxRenstraTujuanIndikatorController@delIndikator');
    //RENSTRA SASARAN
    Route::get('/sasaran/{id_tujuan_renstra}', 'TrxRenstraSasaranController@getSasaranRenstra');
    Route::get('/getSasaranRPJMD/{id_unit}', 'TrxRenstraSasaranController@getSasaranRPJMD');
    Route::post('/addSasaran','TrxRenstraSasaranController@addSasaran');
    Route::post('/editSasaran','TrxRenstraSasaranController@editSasaran');
    Route::post('/delSasaran','TrxRenstraSasaranController@delSasaran');
    Route::get('/getCariTujuanRenstra/{id_sasaran}', 'TrxRenstraSasaranController@getCariTujuanRenstra');
    //Renstra Sasaran Indikator
    Route::get('/getIndikatorSasaran/{id_sasaran_rpjmd}','TrxRenstraSasaranIndikatorController@getIndikatorSasaran');
    Route::get('/getIndikatorSasaranRpjmd/{id_sasaran_rpjmd}', 'TrxRenstraSasaranIndikatorController@getIndikatorSasaranRpjmd');
    Route::post('/addIndikatorSasaran','TrxRenstraSasaranIndikatorController@addIndikator');
    Route::post('/editIndikatorSasaran','TrxRenstraSasaranIndikatorController@editIndikator');
    Route::post('/delIndikatorSasaran','TrxRenstraSasaranIndikatorController@delIndikator');
    //Renstra Kebijakan/Strategi
    Route::get('/kebijakan/{id_sasaran_renstra}', 'TrxRenstraController@getKebijakanRenstra');
    Route::get('/strategi/{id_sasaran_renstra}', 'TrxRenstraController@getStrategiRenstra');
    //RENSTRA PROGRAM
    Route::get('/program/{id_sasaran_renstra}', 'TrxRenstraProgramController@getProgramRenstra');
    Route::get('/getProgramRPJMD/{id_unit}', 'TrxRenstraProgramController@getProgramRPJMD'); 
    Route::get('/getCariSasaranRenstra/{id_unit}', 'TrxRenstraProgramController@getCariSasaranRenstra'); 
    Route::get('/getBidangRef/{id_unit}/{id_program}', 'TrxRenstraProgramController@getBidangRef'); 
    Route::get('/getProgramRef/{id_unit}', 'TrxRenstraProgramController@getProgramRef'); 
    Route::post('/addProgram','TrxRenstraProgramController@addProgram');
    Route::post('/editProgram','TrxRenstraProgramController@editProgram');
    Route::post('/delProgram','TrxRenstraProgramController@delProgram');
    //Renstra Program Indikator
    Route::get('/getIndikatorProgram/{id_program_renstra}', 'TrxRenstraProgramIndikatorController@getIndikatorProgram');
    Route::get('/getIndikatorSasaranRenstra/{id_program_renstra}', 'TrxRenstraProgramIndikatorController@getIndikatorSasaranRenstra');
    Route::post('/addIndikatorProgram','TrxRenstraProgramIndikatorController@addIndikator');
    Route::post('/editIndikatorProgram','TrxRenstraProgramIndikatorController@editIndikator');
    Route::post('/delIndikatorProgram','TrxRenstraProgramIndikatorController@delIndikator');   
    //RENSTRA KEGIATAN
    Route::get('/kegiatan/{id_program_renstra}', 'TrxRenstraKegiatanController@getKegiatanRenstra');
    Route::get('/getKegiatanRef/{id_program}', 'TrxRenstraKegiatanController@getKegiatanRef');
    Route::post('/addKegiatan', 'TrxRenstraKegiatanController@addKegiatan');
    Route::post('/editKegiatan', 'TrxRenstraKegiatanController@editKegiatan');
    Route::post('/delKegiatan', 'TrxRenstraKegiatanController@delKegiatan');

    Route::get('/getIndikatorKegiatan/{id_kegiatan_renstra}', 'TrxRenstraKegiatanIndikatorController@getIndikatorKegiatan');
    Route::post('/addIndikatorKegiatan','TrxRenstraKegiatanIndikatorController@addIndikator');
    Route::post('/editIndikatorKegiatan','TrxRenstraKegiatanIndikatorController@editIndikator');
    Route::post('/delIndikatorKegiatan','TrxRenstraKegiatanIndikatorController@delIndikator');

    Route::get('/kegiatanindikator/{id_kegiatan_renstra}', 'TrxRenstraController@getKegiatanIndikator');
    Route::get('/kegiatanpelaksana/{id_kegiatan_renstra}', 'TrxRenstraController@getKegiatanPelaksana');

    Route::post('/editindikatorprogram', 'TrxRenstraController@editIndikatorProgram');
    Route::post('/editindikatorkegiatan', 'TrxRenstraController@editIndikatorKegiatan');
    Route::post('/getsubunit/{id_sub_unit}', 'TrxRenstraController@getSubUnit');
});