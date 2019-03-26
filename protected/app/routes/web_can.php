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

//Login
Route::get('/', 'WelcomeController@index');

Route::get('/login', function () {return view('auth.login');}); //ganti yang lebih susah, jangan login

//Home
Route::get('/home', 'HomeController@index');
Route::get('/getUser', 'HomeController@getUser');
Route::post('/gantiPass', 'HomeController@gantiPass');
Auth::routes();

//Jangka Pendek
Route::get('/modul2', function () {return view('layouts.app');})->middleware('auth');

//Jangka Panjang
Route::get('/modul1', function () {return view('layouts.app1');})->middleware('auth');

//Anggaran
Route::get('/modul3', function () {return view('layouts.app2');})->middleware('auth');

//ASB
Route::get('/modul0', function () {return view('layouts.app0');})->middleware('auth');

//ASB
Route::get('/modul4', function () {return view('layouts.app3');})->middleware('auth');

//Dashboard
Route::get('/rpjmd/dash','Chart\ChartRPJMDController@chartjs');
Route::get('/rpjmd/misi5tahun','Chart\ChartRPJMDController@misi5tahun_view');
Route::get('/rpjmd/misi1tahun','Chart\ChartRPJMDController@misi1tahun_view');
Route::get('/rpjmd/urusan5tahun','Chart\ChartRPJMDController@urusan5tahun_view');
Route::get('/rpjmd/urusan1','Chart\ChartRPJMDController@urusan1_view');
Route::get('/rpjmd/urusan2','Chart\ChartRPJMDController@urusan2_view');
Route::get('/rpjmd/urusan3','Chart\ChartRPJMDController@urusan3_view');
Route::get('/rpjmd/urusan4','Chart\ChartRPJMDController@urusan4_view');
Route::get('/rpjmd/bidang5tahun','Chart\ChartRPJMDController@bidang5tahun_view');
Route::get('/rkpd/dash','WelcomeController@index_tahunan');
Route::get('/asb/dash','WelcomeController@index_asb');
Route::get('/parameter/dash','WelcomeController@index_parameter');
Route::get('/agenda/tlJadwal/{tahun}', 'RefJadwalController@tlJadwal');
Route::get('/getTahunSetting', 'RefJadwalController@getTahunSetting');
Route::post('/putTahunSetting', 'RefJadwalController@putTahunSetting');

// parameter
Route::group(['prefix' => '/admin/parameter', 'middleware' => ['auth']], function() {
    Route::any('/', function(){
        return view('layouts.parameterlayout');
    })->middleware('menu:1');

    Route::any('/getUnit', 'RefParameterController@getUnit');
    Route::any('/getSubUnit/{id_unit}', 'RefParameterController@getSubUnit');
    Route::any('/getKegRef/{id_program}', 'RefParameterController@getKegRef');
    Route::any('/getProgRef/{id_bidang}', 'RefParameterController@getProgRef');
    Route::get('/getProgRenstra/{id_unit}', 'RefParameterController@getProgRenstra');
    Route::get('/getKegRenstra/{id_unit}/{id_program}', 'RefParameterController@getKegRenstra');
    Route::any('/getUrusan', 'RefParameterController@getUrusan');
    Route::any('/getBidang/{id_urusan}', 'RefParameterController@getBidang');
    Route::any('/getSumberDana', 'RefParameterController@getSumberDana');
    Route::any('/getKecamatan', 'RefParameterController@getKecamatan');
    Route::any('/getDesaAll', 'RefParameterController@getDesaAll');
    Route::any('/getDesa/{id_kecamatan}', 'RefParameterController@getDesa');
    Route::any('/getSubUnitTable/{id_unit}', 'RefParameterController@getSubUnitTable');
    Route::any('/getZonaSSH', 'RefParameterController@getZonaSSH');
    Route::any('/getRefSatuan', 'RefParameterController@getRefSatuan');
    Route::any('/getLokasiLuarDaerah', 'RefParameterController@getLokasiLuarDaerah');
    Route::any('/getLokasiTeknis', 'RefParameterController@getLokasiTeknis');
    Route::any('/getLokasiDesa/{id_kecamatan}', 'RefParameterController@getLokasiDesa');
    Route::any('/getAktivitasASB/{id_tahun}', 'RefParameterController@getAktivitasASB');
    Route::any('/getRefIndikator', 'RefParameterController@getRefIndikator');
    Route::any('/getRekeningSsh/{id}/{id_tarif}', 'RefParameterController@getRekeningSsh');    
    Route::any('/getTahun', 'RefParameterController@getTahun');

    Route::any('/getUnit2/{bidang}', 'RefParameterController@getUnit2');
    Route::any('/getSub2/{unit}', 'RefParameterController@getSub2');
    Route::any('/getUnitUser', 'RefParameterController@getUnitUser');
    Route::any('/getSubUnitUser', 'RefParameterController@getSubUnitUser');
    Route::any('/getProgramRenja/{unit}/{tahun}', 'RefParameterController@getProgram_renja');
    Route::any('/getKegiatanRenja/{program}', 'RefParameterController@getKegiatan_renja');
    
    Route::get('/getRekening/{id}/{tarif}', 'RefParameterController@getRekening');
    Route::get('/getRekeningDapat', 'RefParameterController@getRekeningDapat');
    Route::get('/getRekeningBTL', 'RefParameterController@getRekeningBTL');
    Route::get('/getZonaAktif', 'RefParameterController@getZonaAktif');
    Route::get('/getItemSSH/{id_zona}/{param_like}', 'RefParameterController@getItemSSH');

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

Route::any('/admin/parameter', function(){
    return view('layouts.parameterlayout');
})->middleware('menu:1');

/* update app
* Tentative
*/

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



/* Choose Tahun Anggaran 
* After session created, redirect to referrer
* To call Session Value use this syntax
* Session::get('tahun')
*/

Route::get('/ta/{tahun}', function($tahun) {
    Session::put('tahun', $tahun);
    return redirect()->back();
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

//ZONA SSH
Route::group(['prefix' => 'zonassh', 'middleware' => ['auth', 'menu:801']], function () {
    Route::get('/', ['uses'=>'RefSshZonaController@index','as'=>'DaftarZona']);
    Route::get('/getdata', ['uses'=>'RefSshZonaController@getdata','as'=>'AmbilZona']);
    Route::post('/tambah', ['uses'=>'RefSshZonaController@store','as'=>'TambahZona']);
    Route::post('/update', ['uses'=>'RefSshZonaController@update','as'=>'UpdateZona']);
    Route::post('/delete', ['uses'=>'RefSshZonaController@destroy','as'=>'HapusZona']);
});

//SSH
Route::group(['prefix' => 'ssh', 'middleware' => ['auth', 'menu:802']], function () {
    Route::get('/', ['uses'=>'RefSshController@index','as'=>'DaftarSSH']);
    Route::get('/getGolongan','RefSshController@getGolongan');
    Route::get('/getRefSatuan','RefSshController@getRefSatuan');
    Route::get('/getKelompok/{id_golongan_ssh}','RefSshController@getKelompok');
    Route::get('/getSubKelompok/{id_kelompok_ssh}','RefSshController@getSubKelompok');
    Route::get('/getTarif/{id_sub_kelompok_ssh}','RefSshController@getTarif');
    Route::get('/getRekening/{id_tarif_ssh}','RefSshController@getRekening');
    Route::post('/addGolongan', ['uses'=>'RefSshController@addGolongan','as'=>'TambahGolongan']);
    Route::post('/editGolongan', ['uses'=>'RefSshController@editGolongan','as'=>'EditGolongan']);
    Route::post('/hapusGolongan', ['uses'=>'RefSshController@hapusGolongan','as'=>'HapusGolongan']);
    Route::post('/addKelompok', ['uses'=>'RefSshController@addKelompok','as'=>'TambahKelompok']);
    Route::post('/editKelompok', ['uses'=>'RefSshController@editKelompok','as'=>'EditKelompok']);
    Route::post('/hapusKelompok', ['uses'=>'RefSshController@hapusKelompok','as'=>'HapusKelompok']);
    Route::post('/addSubKelompok', ['uses'=>'RefSshController@addSubKelompok','as'=>'TambahSubKelompok']);
    Route::post('/editSubKelompok', ['uses'=>'RefSshController@editSubKelompok','as'=>'EditSubKelompok']);
    Route::post('/hapusSubKelompok', ['uses'=>'RefSshController@hapusSubKelompok','as'=>'HapusSubKelompok']);
    Route::post('/addItem', ['uses'=>'RefSshController@addItem','as'=>'TambahItem']);
    Route::post('/editItem', ['uses'=>'RefSshController@editItem','as'=>'EditItem']);
    Route::post('/hapusItem', ['uses'=>'RefSshController@hapusItem','as'=>'HapusItem']);
    Route::post('/addRekeningSsh', ['uses'=>'RefSshController@addRekeningSsh','as'=>'TambahRekening']);
    Route::post('/editRekeningSsh', ['uses'=>'RefSshController@editRekeningSsh','as'=>'EditRekening']);
    Route::post('/hapusRekeningSsh', ['uses'=>'RefSshController@hapusRekeningSsh','as'=>'HapusRekening']);

    Route::get('/getCariRekening','RefSshController@getCariRekening');
});

//SSH PERKADA
Route::group(['prefix' => 'sshperkada', 'middleware' => ['auth', 'menu:803']], function () {
    Route::get('/perkada', ['uses'=>'RefSshPerkadaController@index','as'=>"DaftarPerkadaSSH"]);
    Route::get('/getPerkada','RefSshPerkadaController@getPerkada');
    Route::get('/getZona/{id_test}','RefSshPerkadaController@getZona');
    Route::get('/getTarifPerkada/{id_test}','RefSshPerkadaController@getTarif');
    Route::post('/getRekening',['uses'=>'RefSshPerkadaController@getRekening','as'=>'GetRekening']);
    Route::post('/addPerkada', ['uses'=>'RefSshPerkadaController@addPerkada','as'=>'TambahPerkada']);
    Route::post('/editPerkada', ['uses'=>'RefSshPerkadaController@editPerkada','as'=>'EditPerkada']);
    Route::post('/hapusPerkada', ['uses'=>'RefSshPerkadaController@hapusPerkada','as'=>'HapusPerkada']);
    Route::post('/statusPerkada', ['uses'=>'RefSshPerkadaController@statusPerkada','as'=>'StatusPerkada']);
    Route::post('/addZonaPerkada', ['uses'=>'RefSshPerkadaController@addZonaPerkada','as'=>'TambahZonaPerkada']);
    Route::post('/editZonaPerkada', ['uses'=>'RefSshPerkadaController@editZonaPerkada','as'=>'EditZonaPerkada']);
    Route::post('/hapusZonaPerkada', ['uses'=>'RefSshPerkadaController@hapusZonaPerkada','as'=>'HapusZonaPerkada']);
    Route::post('/addTarifPerkada', ['uses'=>'RefSshPerkadaController@addTarifPerkada','as'=>'TambahTarifPerkada']);
    Route::post('/editTarifPerkada', ['uses'=>'RefSshPerkadaController@editTarifPerkada','as'=>'EditTarifPerkada']);
    Route::post('/hapusTarifPerkada', ['uses'=>'RefSshPerkadaController@hapusTarifPerkada','as'=>'HapusTarifPerkada']);
    Route::get('/cariItemSSH/{id_param}', 'RefSshPerkadaController@getItemSSH');

    Route::get('/getCountStatus/{flag}','RefSshPerkadaController@getCountStatus');

    Route::any('/getDataPerkada','RefSshPerkadaController@getDataPerkada');
    Route::any('/getDataZona/{id_perkada}','RefSshPerkadaController@getDataZona');
    Route::any('/copyTarifRef','RefSshPerkadaController@copyTarifRef');
    Route::any('/copyTarifPerkada','RefSshPerkadaController@copyTarifPerkada');

});

//ASB KOMPONEN
Route::group(['prefix' => 'asb', 'middleware' => ['auth', 'menu:804']], function () {
    Route::get('/komponen', ['uses'=>'RefAsbKomponenController@index','as'=>'DaftarKomponen']);
    Route::get('/komponen/datakomponen', ['uses'=>'RefAsbKomponenController@datakomponen','as'=>'AmbilKomponen']);
    Route::get('/komponen/datarinci/{id_komponen}', ['uses'=>'RefAsbKomponenController@datarinci','as'=>'AmbilKomponenRinci']);
    Route::post('/addKomponen', ['uses'=>'RefAsbKomponenController@addKomponen','as'=>'TambahKomponen']);
    Route::post('/editKomponen', ['uses'=>'RefAsbKomponenController@editKomponen','as'=>'EditKomponen']);
    Route::post('/hapusKomponen', ['uses'=>'RefAsbKomponenController@hapusKomponen','as'=>'HapusKomponen']);
    Route::post('/addRincian', ['uses'=>'RefAsbKomponenController@addRincian','as'=>'TambahRincian']);
    Route::post('/editRincian', ['uses'=>'RefAsbKomponenController@editRincian','as'=>'EditRincian']);
    Route::post('/hapusRincian', ['uses'=>'RefAsbKomponenController@hapusRincian','as'=>'HapusRincian']);
});

//ASB AKTIVITAS
Route::group(['prefix' => 'asb', 'middleware' => ['auth', 'menu:805']], function () {
    Route::get('/aktivitas', ['uses'=>'TrxAsbPerkadaController@index','as'=>'DaftarAktivitas']);
    Route::get('/getPerkada','TrxAsbPerkadaController@getPerkada');
    Route::get('/getGrouping','TrxAsbPerkadaController@getGrouping');    
    Route::get('/getKelompok/{id_perkada}','TrxAsbPerkadaController@getKelompok');
    Route::get('/getSubKelompok/{id_kelompok}','TrxAsbPerkadaController@getSubKelompok');
    Route::get('/getSubsubkel/{id_sub_kelomok}','TrxAsbPerkadaController@getSubsubkel');
    Route::get('/getAktivitas/{id_sub_sub_kelomok}','TrxAsbPerkadaController@getAktivitas');
    Route::get('/getKomponen/{id_aktivitas_asb}','TrxAsbPerkadaController@getKomponen');
    Route::get('/getRincian/{id_komponen_asb}','TrxAsbPerkadaController@getRincian');
    
    Route::post('/addKelompok','TrxAsbPerkadaController@addKelompok');
    Route::post('/editKelompok','TrxAsbPerkadaController@editKelompok');
    Route::post('/hapusKelompok','TrxAsbPerkadaController@hapusKelompok');
    
    Route::post('/addSubKelompok','TrxAsbPerkadaController@addSubKelompok');
    Route::post('/editSubKelompok','TrxAsbPerkadaController@editSubKelompok');
    Route::post('/hapusSubKelompok','TrxAsbPerkadaController@hapusSubKelompok');

    Route::post('/addSubSubKelompok','TrxAsbPerkadaController@addSubSubKelompok');
    Route::post('/editSubSubKelompok','TrxAsbPerkadaController@editSubSubKelompok');
    Route::post('/hapusSubSubKelompok','TrxAsbPerkadaController@hapusSubSubKelompok');
    
    Route::post('/addPerkada', ['uses'=>'TrxAsbPerkadaController@addPerkada','as'=>'TambahPerkadaASB']);
    Route::post('/editPerkada', ['uses'=>'TrxAsbPerkadaController@editPerkada','as'=>'EditPerkadaASB']);
    Route::post('/hapusPerkada', ['uses'=>'TrxAsbPerkadaController@hapusPerkada','as'=>'HapusPerkadaASB']);
    Route::post('/statusPerkada', ['uses'=>'TrxAsbPerkadaController@statusPerkada','as'=>'StatusPerkadaASB']);
    
    Route::post('/addAktivitas', ['uses'=>'TrxAsbPerkadaController@addAktivitas','as'=>'TambahAktivitasASB']);
    Route::post('/editAktivitas', ['uses'=>'TrxAsbPerkadaController@editAktivitas','as'=>'EditAktivitasASB']);
    Route::post('/hapusAktivitas', ['uses'=>'TrxAsbPerkadaController@hapusAktivitas','as'=>'HapusAktivitasASB']);
    
    Route::post('/addKomponenASB', ['uses'=>'TrxAsbPerkadaController@addKomponen','as'=>'TambahKomponenASB']);
    Route::post('/editKomponenASB', ['uses'=>'TrxAsbPerkadaController@editKomponen','as'=>'EditKomponenASB']);
    Route::post('/hapusKomponenASB', ['uses'=>'TrxAsbPerkadaController@hapusKomponen','as'=>'HapusKomponenASB']);

    Route::post('/addRincianASB', ['uses'=>'TrxAsbPerkadaController@addRincian','as'=>'TambahRincianASB']);
    Route::post('/editRincianASB', ['uses'=>'TrxAsbPerkadaController@editRincian','as'=>'EditRincianASB']);
    Route::post('/hapusRincianASB', ['uses'=>'TrxAsbPerkadaController@hapusRincian','as'=>'HapusRincianASB']);

    Route::get('/getRekening','TrxAsbPerkadaController@getRekening');
    Route::get('/getItemSSH/{param_like}','TrxAsbPerkadaController@getItemSSH');
    Route::get('/getRefSatuan','TrxAsbPerkadaController@getRefSatuan');
    Route::get('/getRefSatuanDer/{id_asb_aktivitas}/{id_satuan}','TrxAsbPerkadaController@getRefSatuanDer');
    Route::get('/getCariKomponen','TrxAsbPerkadaController@getCariKomponen');
    Route::get('/getCariKelompok','TrxAsbPerkadaController@getCariKelompok');

    Route::get('/getCountStatus/{flag}','TrxAsbPerkadaController@getCountStatus');

    Route::get('/getTempKelompok/{id}','TrxAsbPerkadaController@getTempKelompok');
    Route::get('/getTempSubKelompok/{id}','TrxAsbPerkadaController@getTempSubKelompok');
    Route::get('/getTempSubSubKelompok/{id}/{id_subkel}','TrxAsbPerkadaController@getTempSubSubKelompok');
    Route::get('/getTempAktivitas/{id}/{id_subsubkel}','TrxAsbPerkadaController@getTempAktivitas');
    Route::get('/getTempKomponen/{id}/{id_aktivitas}','TrxAsbPerkadaController@getTempKomponen');
    Route::get('/getTempRincian/{id}/{id_komponen}','TrxAsbPerkadaController@getTempRincian');

    Route::post('/CopyKomponen','TrxAsbPerkadaController@CopyKomponen');
    Route::post('/CopyKelompok','TrxAsbPerkadaController@CopyKelompok');
    Route::post('/CopySubKelompok','TrxAsbPerkadaController@CopySubKelompok');
    Route::post('/CopySubSubKelompok','TrxAsbPerkadaController@CopySubSubKelompok');
    Route::post('/CopyAktivitas','TrxAsbPerkadaController@CopyAktivitas');
    Route::post('/CopyKomponen2','TrxAsbPerkadaController@CopyKomponen2');
    Route::post('/CopyRincian','TrxAsbPerkadaController@CopyRincian');

});

Route::group(['prefix' => 'asb', 'middleware' => ['auth', 'menu:806']], function () {
    Route::get('/hitungasb', ['uses'=>'TrxAsbPerhitunganController@index','as'=>'DaftarPerhitungan']);
    Route::get('/hitungasb/datahitung', ['uses'=>'TrxAsbPerhitunganController@datahitung','as'=>'AmbilDataHitung']);
    Route::get('/hitungasb/datakelompok/{id_perhitungan}', 'TrxAsbPerhitunganController@datakelompok');
    Route::get('/hitungasb/datasubkelompok/{id_kelompok}/{id_perhitungan}', 'TrxAsbPerhitunganController@datasubkelompok');
    Route::get('/hitungasb/datasubsubkelompok/{id_kelompok}/{id_perhitungan}', 'TrxAsbPerhitunganController@datasubsubkelompok');
    Route::get('/hitungasb/datazona/{id_subkelompok}/{id_perhitungan}', 'TrxAsbPerhitunganController@datazona');
    Route::get('/hitungasb/dataaktivitas/{id_subkelompok}/{id_perhitungan}/{id_zona}', 'TrxAsbPerhitunganController@dataaktivitas');
    Route::get('/hitungasb/datakomponen/{id_aktivitas}/{id_perhitungan}/{id_zona}', 'TrxAsbPerhitunganController@datakomponen');
    Route::get('/hitungasb/datarinci/{id_komponen}/{id_perhitungan}/{id_zona}', 'TrxAsbPerhitunganController@datarinci');
    Route::any('/prosesASB', 'TrxAsbPerhitunganController@ProsesHitungAsb');

    Route::any('/getDataASB/{id_asb_perkada}', 'TrxAsbPerhitunganController@getDataASB');


    Route::post('/addPerhitungan', 'TrxAsbPerhitunganController@addPerhitungan');
    Route::post('/addPerhitunganRinci', 'TrxAsbPerhitunganController@addPerhitunganRinci');
    Route::get('/GetHitungASB', 'TrxAsbPerhitunganController@GetHitungASB');

    Route::post('/UbahStatus', 'TrxAsbPerhitunganController@UbahStatus');
    Route::post('/hapusPerhitungan', 'TrxAsbPerhitunganController@hapusPerhitungan');

    Route::get('/hitungasb/datax/{id_perhitungan}', 'TrxAsbPerhitunganController@datax');
    Route::get('/nilaiFix/{dv1}/{dv2}/{kmax1}/{kmax2}','TrxAsbPerhitunganController@nilaiFCost');
    Route::get('/nilaiDCost/{dv1}/{dv2}/{rmax1}/{rmax2}/{hub}','TrxAsbPerhitunganController@nilaiDCost');
    Route::get('/nilaiICost/{dv1}/{dv2}/{hub}','TrxAsbPerhitunganController@nilaiICost');

    Route::get('/cobaHitung','TrxAsbPerhitunganController@cobaHitung');

    Route::get('/getTahunHitung','TrxAsbPerhitunganController@getTahunHitung');
    Route::get('/getPerkadaSimulasi/{id_tahun}','TrxAsbPerhitunganController@getPerkadaSimulasi');
    Route::get('/getAktivitasSimulasi/{id_hitung}','TrxAsbPerhitunganController@getAktivitasSimulasi');

});

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


//RPJMD FINAL
Route::group(['prefix' => 'rpjmd', 'middleware' => ['auth', 'menu:20']], function() {
        Route::get('/', 'TrxRpjmdController@index');
        Route::get('/getDokumen', 'TrxRpjmdController@getDokumen');
        Route::get('/visi', 'TrxRpjmdController@getVisiRPJMD');
        Route::post('/editVisi', ['uses'=>'TrxRpjmdController@editVisi','as'=>'EditVisi']);
        Route::get('/misi/{id_visi_rpjmd}','TrxRpjmdController@getMisiRPJMD');
        Route::post('/editMisi', ['uses'=>'TrxRpjmdController@editMisi','as'=>'EditMisi']);
        Route::get('/tujuan/{id_misi_rpjmd}','TrxRpjmdController@getTujuanRPJMD');
        Route::post('/edittujuan', ['uses'=>'TrxRpjmdController@editTujuan','as'=>'Edittujuan']);
        Route::any('/getRpjmdChart/{id_rpjmd}', 'TrxRpjmdController@indexChart');
    //RPJMD Sasaran
        Route::get('/sasaran/{id_tujuan_rpjmd}','TrxRpjmdController@getSasaranRPJMD');
        Route::post('/editsasaran', ['uses'=>'TrxRpjmdController@editSasaran','as'=>'EditSasaran']);
        Route::get('/kebijakan/{id_sasaran_rpjmd}','TrxRpjmdController@getKebijakanRPJMD');
        Route::post('/editkebijakan', ['uses'=>'TrxRpjmdController@editKebijakan','as'=>'EditKebijakan']);
        Route::get('/strategi/{id_sasaran_rpjmd}','TrxRpjmdController@getStrategiRPJMD');
        Route::post('/editstrategi', ['uses'=>'TrxRpjmdController@editStrategi','as'=>'EditStrategi']);
    //RPJMD Program
        Route::get('/program/{id_sasaran_rpjmd}','TrxRpjmdController@getProgramRPJMD');
        Route::post('/editprogram', ['uses'=>'TrxRpjmdController@editProgram','as'=>'EditProgram']);
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
    Route::get('/getIndikatorSasaran/{id_sasaran_rpjmd}','TrxRenstraSasaranIndikatorController@getIndikatorSasaran');
    Route::get('/getIndikatorSasaranRpjmd/{id_sasaran_rpjmd}', 'TrxRenstraSasaranIndikatorController@getIndikatorSasaranRpjmd');
    Route::post('/addIndikatorSasaran','TrxRenstraSasaranIndikatorController@addIndikator');
    Route::post('/editIndikatorSasaran','TrxRenstraSasaranIndikatorController@editIndikator');
    Route::post('/delIndikatorSasaran','TrxRenstraSasaranIndikatorController@delIndikator');

    Route::get('/kebijakan/{id_sasaran_renstra}', 'TrxRenstraController@getKebijakanRenstra');
    Route::get('/strategi/{id_sasaran_renstra}', 'TrxRenstraController@getStrategiRenstra');
    //RENSTRA PROGRAM
    Route::get('/program/{id_sasaran_renstra}', 'TrxRenstraProgramController@getProgramRenstra');
    Route::get('/getIndikatorProgram/{id_program_renstra}', 'TrxRenstraProgramIndikatorController@getIndikatorProgram');
    Route::get('/getIndikatorSasaranRenstra/{id_program_renstra}', 'TrxRenstraProgramIndikatorController@getIndikatorSasaranRenstra');
    Route::post('/addIndikatorProgram','TrxRenstraProgramIndikatorController@addIndikator');
    Route::post('/editIndikatorProgram','TrxRenstraProgramIndikatorController@editIndikator');
    Route::post('/delIndikatorProgram','TrxRenstraProgramIndikatorController@delIndikator');    
    Route::post('/editProgram','TrxRenstraProgramController@editProgram');
    //RENSTRA KEGIATAN
    Route::get('/kegiatan/{id_program_renstra}', 'TrxRenstraKegiatanController@getKegiatanRenstra');
    Route::get('/getIndikatorKegiatan/{id_kegiatan_renstra}', 'TrxRenstraKegiatanIndikatorController@getIndikatorKegiatan');
    Route::post('/addIndikatorKegiatan','TrxRenstraKegiatanIndikatorController@addIndikator');
    Route::post('/editIndikatorKegiatan','TrxRenstraKegiatanIndikatorController@editIndikator');
    Route::post('/delIndikatorKegiatan','TrxRenstraKegiatanIndikatorController@delIndikator');

    Route::post('/editKegiatan', 'TrxRenstraKegiatanController@editKegiatan');

    Route::get('/kegiatanindikator/{id_kegiatan_renstra}', 'TrxRenstraController@getKegiatanIndikator');
    Route::get('/kegiatanpelaksana/{id_kegiatan_renstra}', 'TrxRenstraController@getKegiatanPelaksana');

    Route::post('/editindikatorprogram', 'TrxRenstraController@editIndikatorProgram');
    Route::post('/editindikatorkegiatan', 'TrxRenstraController@editIndikatorKegiatan');
    Route::post('/getsubunit/{id_sub_unit}', 'TrxRenstraController@getSubUnit');
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
