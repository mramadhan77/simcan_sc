<?php


if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

//Login
Route::get('/', 'WelcomeController@index');

Route::get('/login', function () {return view('auth.login');}); //ganti yang lebih susah, jangan login

//Home
Route::get('/home', 'HomeController@index');
Route::get('/getUser', 'HomeController@getUser');
Route::post('/gantiPass', 'HomeController@gantiPass');
Auth::routes();



Route::any('/unit/TestApi', 'RefUnitController@TestApi');

// parameter
Route::group(['prefix' => '/admin/parameter', 'middleware' => ['auth']], function() {
    
    Route::any('/', function(){
        return view('layouts.parameterlayout');
    })->middleware('menu:1');

    Route::any('/getAspek', 'RefParameterController@getAspek');
    Route::any('/getUrusan', 'RefParameterController@getUrusan');
    Route::any('/getBidang/{id_urusan}', 'RefParameterController@getBidang');
    Route::any('/getBidang2', 'RefParameterController@getBidang2');

    Route::any('/getUnit', 'RefParameterController@getUnit');
    Route::any('/getUnitPelaksana', 'RefParameterController@getUnitPelaksana');
    Route::any('/getSubUnit/{id_unit}', 'RefParameterController@getSubUnit');
    Route::any('/getKegRef/{id_program}', 'RefParameterController@getKegRef');
    Route::any('/getProgRef/{id_bidang}', 'RefParameterController@getProgRef');
    Route::get('/getProgRenstra/{id_unit}', 'RefParameterController@getProgRenstra');
    Route::get('/getKegRenstra/{id_unit}/{id_program}', 'RefParameterController@getKegRenstra');
    Route::any('/getSumberDana', 'RefParameterController@getSumberDana');
    Route::any('/getKecamatan', 'RefParameterController@getKecamatan');
    Route::any('/getDesaAll', 'RefParameterController@getDesaAll');
    Route::any('/getDesa/{id_kecamatan}', 'RefParameterController@getDesa');
    Route::any('/getSubUnitTable/{id_unit}', 'RefParameterController@getSubUnitTable');
    Route::any('/getRefSatuan', 'RefParameterController@getRefSatuan');
    Route::any('/getLokasiLuarDaerah', 'RefParameterController@getLokasiLuarDaerah');
    Route::any('/getLokasiTeknis', 'RefParameterController@getLokasiTeknis');
    Route::any('/getLokasiDesa/{id_kecamatan}', 'RefParameterController@getLokasiDesa');
    Route::any('/getAktivitasASB/{id_tahun}', 'RefParameterController@getAktivitasASB');
    Route::any('/getRefIndikator', 'RefParameterController@getRefIndikator');
    Route::any('/getRekeningSsh/{id}/{id_tarif}', 'RefParameterController@getRekeningSsh');    
    Route::any('/getTahun', 'RefParameterController@getTahun');

    Route::any('/getZonaSSH', 'RefParameterController@getZonaSSH');
    Route::get('/getZonaAktif', 'RefParameterController@getZonaAktif');
    Route::get('/getItemSSH/{id_zona}/{param_like}', 'RefParameterController@getItemSSH');

    Route::any('/getUnit2/{bidang}', 'RefParameterController@getUnit2');
    Route::any('/getSub2/{unit}', 'RefParameterController@getSub2');
    Route::any('/getUnitUser', 'RefParameterController@getUnitUser');
    Route::any('/getSubUnitUser', 'RefParameterController@getSubUnitUser');
    Route::any('/getProgramRenja/{unit}/{tahun}', 'RefParameterController@getProgram_renja');
    Route::any('/getKegiatanRenja/{program}', 'RefParameterController@getKegiatan_renja');
    
    Route::get('/getRekening/{id}/{tarif}', 'RefParameterController@getRekening');
    Route::get('/getRekeningDapat', 'RefParameterController@getRekeningDapat');
    Route::get('/getRekeningBTL', 'RefParameterController@getRekeningBTL');

    //USER
    Route::group(['prefix'=>'/others','middleware'=>['auth']],function(){
        Route::any('/', 'RefParameterLainnyaController@index');
        Route::any('/getDataJenis', 'RefParameterLainnyaController@getDataJenis');
        Route::any('/hapusJenisLokasi', 'RefParameterLainnyaController@hapusJenisLokasi');
        Route::any('/addJenisLokasi', 'RefParameterLainnyaController@addJenisLokasi');
        Route::any('/getSumberDana', 'RefParameterLainnyaController@getSumberDana');
        Route::any('/hapusSumberDana', 'RefParameterLainnyaController@hapusSumberDana');
        Route::any('/addSumberDana', 'RefParameterLainnyaController@addSumberDana');
    });

    Route::group(['prefix' => '/user', 'middleware' => ['auth', 'menu:110']], function() {
        Route::any('/', 'UserController@index');
        Route::any('/getUnit', 'UserController@getUnit');
        Route::any('/getGroup', 'UserController@getGroup');
        Route::any('/getUnitIndex', 'UserController@getUnitIndex');
        Route::any('/getListUnit/{id_user}', 'UserController@getListUnit');
        Route::any('/getListDesa/{id_user}', 'UserController@getListDesa');
        Route::any('/getListKab/{id_user}', 'UserController@getListKab');
        Route::any('/getKecamatan', 'UserController@getKecamatan');
        Route::any('/getDesa/{id_kecamatan}', 'UserController@getDesa');
        Route::any('/getKab', 'UserController@getKab');

        Route::any('/addUser', 'UserController@addUser');
        Route::any('/editUser', 'UserController@editUser');
        Route::any('/gantiPass', 'UserController@gantiPass');
        Route::any('/hapusUser', 'UserController@hapusUser');

        Route::any('/cekUserAdmin', 'UserController@cekUserAdmin');

        Route::any('/addUnit', 'UserController@addUnit');
        Route::any('/hapusUnit', 'UserController@hapusUnit');

        Route::any('/addWilayah', 'UserController@addWilayah');
        Route::any('/hapusWilayah', 'UserController@hapusWilayah');

        Route::group(['prefix' => '/group', 'middleware' => ['auth', 'menu:110']], function() {
            Route::any('/', 'RefGroupMenuController@group');            
            Route::any('/peranGroup', 'RefGroupMenuController@getPeranGroup');
            Route::any('/{id}/akses', 'RefGroupMenuController@akses');
            Route::any('/addGroup', 'RefGroupMenuController@addGroup');
            Route::any('/editGroup', 'RefGroupMenuController@editGroup');
            Route::any('/hapusGroup', 'RefGroupMenuController@hapusGroup');
        });

        Route::group(['prefix' => '/peran', 'middleware' => ['auth', 'menu:110']], function() {
            Route::any('/', 'RefGroupMenuController@getPeran');
            Route::any('/addPeran', 'RefGroupMenuController@addPeran');
            Route::any('/editPeran', 'RefGroupMenuController@editPeran');
            Route::any('/hapusPeran', 'RefGroupMenuController@hapusPeran');
        });
        
    });

    //Kecamatan/Desa
    Route::group(['prefix' => '/kecamatan', 'middleware' => ['auth', 'menu:102']], function() {
        Route::any('/', 'RefKecamatanController@index');
        Route::any('/getListKabKota', 'RefKecamatanController@getListKabKota');
        Route::any('/getListKecamatan/{id_kab}', 'RefKecamatanController@getListKecamatan');
        Route::any('/getListDesa/{id_kecamatan}', 'RefKecamatanController@getListDesa');

        Route::any('/addKecamatan', 'RefKecamatanController@addKecamatan');
        Route::any('/editKecamatan', 'RefKecamatanController@editKecamatan');
        Route::any('/addDesa', 'RefKecamatanController@addDesa');
        Route::any('/editDesa', 'RefKecamatanController@editDesa');
    });
    // }); 
    
    //Unit Organisasi
    Route::group(['prefix' => '/unit', 'middleware' => ['auth', 'menu:103']], function() {
        Route::any('/', 'RefUnitController@index');
        Route::any('/getListUrusan', 'RefUnitController@getListUrusan');
        Route::any('/getListBidang/{id_urusan}', 'RefUnitController@getListBidang');
        Route::any('/getListUnit/{id_bidang}', 'RefUnitController@getListUnit');
        Route::any('/getListSubUnit/{id_unit}', 'RefUnitController@getListSubUnit');
        Route::any('/getListDataSubUnit/{id_unit}', 'RefUnitController@getListDataSubUnit');

        Route::any('/addUnit', 'RefUnitController@addUnit');
        Route::any('/editUnit', 'RefUnitController@editUnit');
        Route::any('/hapusUnit', 'RefUnitController@hapusUnit');
        Route::any('/addSubUnit', 'RefUnitController@addSubUnit');
        Route::any('/editSubUnit', 'RefUnitController@editSubUnit');
        Route::any('/hapusSubUnit', 'RefUnitController@hapusSubUnit');
        Route::any('/addDataSubUnit', 'RefUnitController@addDataSubUnit');
        Route::any('/editDataSubUnit', 'RefUnitController@editDataSubUnit');
        Route::any('/hapusDataSubUnit', 'RefUnitController@hapusDataSubUnit');
    });
    
    //Unit Organisasi
    Route::group(['prefix' => '/rekening', 'middleware' => ['auth', 'menu:105']], function() {
        Route::any('/', 'RefRekeningController@index');
        Route::any('/getListAkun', 'RefRekeningController@getListAkun');
        Route::any('/getListGolongan/{id_akun}', 'RefRekeningController@getListGolongan');
        Route::any('/getListJenis/{id_akun}/{id_golongan}', 'RefRekeningController@getListJenis');
        Route::any('/getListObyek/{id_akun}/{id_golongan}/{id_jenis}', 'RefRekeningController@getListObyek');
        Route::any('/getListRincian/{id_akun}/{id_golongan}/{id_jenis}/{id_obyek}', 'RefRekeningController@getListRincian');

        Route::any('/addRek4', 'RefRekeningController@addRek4');
        Route::any('/editRek4', 'RefRekeningController@editRek4');
        Route::any('/hapusRek4', 'RefRekeningController@hapusRek4');
        Route::any('/addRek5', 'RefRekeningController@addRek5');
        Route::any('/editRek5', 'RefRekeningController@editRek5');
        Route::any('/hapusRek5', 'RefRekeningController@hapusRek5');


        Route::any('/{id}/view', 'RekeningController@view');
        // rek2
        Route::group(['prefix' => '/{kd_rek_1}/rek2'], function() {
            Route::any('/', 'RekeningController@rek2');
        });
        // rek3
        Route::group(['prefix' => '/{kd_rek_2}/rek3'], function() {
            Route::any('/', 'RekeningController@rek3');
        });
    });
    
    //Program/Kegiatan
    Route::group(['prefix' => '/program', 'middleware' => ['auth', 'menu:106']], function() {
        Route::any('/', 'RefProgramController@index');
        Route::any('/getListUrusan', 'RefProgramController@getListUrusan');
        Route::any('/getListBidang/{id_urusan}', 'RefProgramController@getListBidang');
        Route::any('/getListProgram/{id_bidang}', 'RefProgramController@getListProgram');
        Route::any('/getListKegiatan/{id_program}', 'RefProgramController@getListKegiatan');

        Route::any('/addProgram', 'RefProgramController@addProgram');
        Route::any('/editProgram', 'RefProgramController@editProgram');
        Route::any('/hapusProgram', 'RefProgramController@hapusProgram');
        Route::any('/addKegiatan', 'RefProgramController@addKegiatan');
        Route::any('/editKegiatan', 'RefProgramController@editKegiatan');
        Route::any('/hapusKegiatan', 'RefProgramController@hapusKegiatan');
    });

    Route::group(['prefix' => '/kegiatan', 'middleware' => ['auth', 'menu:106']], function() {
        Route::any('/{id}', 'KegiatanController@index');
        Route::any('/tambah/{id}', 'KegiatanController@create');
        Route::any('/{id}/ubah', 'KegiatanController@update');
        Route::any('/{id}/view', 'KegiatanController@view');
        Route::post('/{id}/delete', 'KegiatanController@delete');
    }); 

    //Lokasi
    Route::group(['prefix' => '/lokasi', 'middleware' => ['auth', 'menu:107']], function() {
        Route::any('/', 'RefLokasiController@index');
        Route::any('/getListLokasi', 'RefLokasiController@getListLokasi');
        Route::any('/addLokasi', 'RefLokasiController@addLokasi');
        Route::any('/editLokasi', 'RefLokasiController@editLokasi');
        Route::any('/hapusLokasi', 'RefLokasiController@hapusLokasi');
        Route::any('/insertWilayah', 'RefLokasiController@insertWilayah');
        Route::any('/getJenisLokasi', 'RefLokasiController@getJenisLokasi');

        Route::any('/getDataJenis', 'RefLokasiController@getDataJenis');
        Route::any('/hapusJenisLokasi', 'RefLokasiController@hapusJenisLokasi');
        Route::any('/addJenisLokasi', 'RefLokasiController@addJenisLokasi');
    }); 
    
    //Indikator
    Route::group(['prefix' => '/indikator', 'middleware' => ['auth', 'menu:108']], function() {
        Route::any('/', 'RefIndikatorController@index');
        Route::any('/getListIndikator', 'RefIndikatorController@getListIndikator');
        Route::any('/addIndikator', 'RefIndikatorController@addIndikator');
        Route::any('/editIndikator', 'RefIndikatorController@editIndikator');
        Route::any('/hapusIndikator', 'RefIndikatorController@hapusIndikator');

        Route::any('/tambah', 'SatuanindikatorController@create');
        Route::any('/{id}/ubah', 'SatuanindikatorController@update');
        Route::any('/{id}/view', 'SatuanindikatorController@view');
        Route::post('/{id}/delete', 'SatuanindikatorController@delete');
    });
}); 

Route::group(['prefix' => '/admin/update', 'middleware' => ['auth', 'menu:9']], function() {
    Route::get('/',  'UpdateController@index');
    Route::any('/execute',  'UpdateController@update');
    Route::any('/updateDB',  'UpdateController@updateDB');
    Route::any('/encryptDB',  'UpdateController@encryptDB');
    Route::any('/getApp',  'UpdateController@getApi');
    Route::any('/getUpdate',  'UpdateController@getUpdate');
    Route::any('/getJmlTable',  'UpdateController@getJmlTable');
    Route::any('/BuatTable',  'UpdateController@BuatTable');
    Route::any('/BuatKolom',  'UpdateController@BuatKolom');
    Route::any('/BuatFungsi',  'UpdateController@BuatFungsi');
    Route::any('/BuatTrigger',  'UpdateController@BuatTrigger');
    Route::any('/BuatForeignKey',  'UpdateController@BuatForeignKey');
    Route::any('/UpdateAtribut',  'UpdateController@UpdateAtribut');
    Route::any('/UpdateAtributUnik',  'UpdateController@UpdateAtributUnik');
    Route::any('/TambahAtributUnik',  'UpdateController@TambahAtributUnik');
    Route::any('/TambahRefLog',  'UpdateController@TambahRefLog');    
    Route::any('/UpdateEnter',  'UpdateController@UpdateEnter');
    Route::any('/TestApiSimda',  'UpdateController@TestApiSimda');
});


//Referensi Satuan
Route::group(['prefix' => 'satuan', 'middleware' => ['auth', 'menu:111']], function () {
    Route::get('/', ['uses'=>'RefSatuanController@index','as'=>'DaftarSatuan']);
    Route::get('/getdata', ['uses'=>'RefSatuanController@getdata','as'=>'AmbilSatuan']);
    Route::post('/tambah', ['uses'=>'RefSatuanController@tambah','as'=>'TambahSatuan']);
    Route::post('/edit', ['uses'=>'RefSatuanController@edit','as'=>'UpdateSatuan']);
    Route::post('/hapus', ['uses'=>'RefSatuanController@hapus','as'=>'HapusSatuan']);
});

//Referensi Pemda
Route::group(['prefix' => 'pemda', 'middleware' => ['auth', 'menu:101']], function () {
    Route::get('/', 'RefPemdaController@index');
    Route::get('/getPemda', 'RefPemdaController@getPemda');
    Route::get('/getState', 'RefPemdaController@getState');
    Route::get('/getRefUnit', 'RefPemdaController@getRefUnit');
    Route::post('/editPemda', 'RefPemdaController@editPemda');
    Route::get('/getPemdaX1', 'RefPemdaController@getPemdaX1');
    Route::post('/getPemdaX', 'RefPemdaController@hashPemda');

});

//Referensi Setting
Route::group(['prefix' => 'setting', 'middleware' => ['auth', 'menu:101']], function () {
    Route::get('/', 'SettingController@index');
    Route::get('/getListSetting', 'SettingController@getListSetting');
    Route::post('/addSetting', 'SettingController@addSetting');
    Route::post('/editSetting', 'SettingController@editSetting');
    Route::post('/hapusSetting', 'SettingController@hapusSetting');
    Route::post('/postSetting', 'SettingController@postSetting');

});

//Referensi Agenda Kerja
Route::group(['prefix' => 'agenda', 'middleware' => ['auth', 'menu:101']], function () {
    Route::get('/', 'RefJadwalController@index');
    Route::get('/rinciagenda/{tahun}', 'RefJadwalController@getJadwal');
    Route::get('/rekapagenda', 'RefJadwalController@getTahunJadwal');
    // Route::get('/tlJadwal/{tahun}', 'RefJadwalController@tlJadwal');    
    Route::get('/curJadwal', 'RefJadwalController@curJadwal');
    Route::post('/addJadwal', 'RefJadwalController@addJadwal');
    Route::post('/hapusJadwal', 'RefJadwalController@hapusJadwal');

});