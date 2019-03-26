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


Route::any('/admin/parameter', function(){
    return view('layouts.parameterlayout');
})->middleware('menu:1');


Route::get('/ta/{tahun}', function($tahun) {
    Session::put('tahun', $tahun);
    return redirect()->back();
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
    
    Route::get('/getGolPerkada/{id_test}','RefSshPerkadaController@getGolongan');
    Route::get('/getKelPerkada/{id_test}/{id_gol}','RefSshPerkadaController@getKelompok');
    Route::get('/getSKelPerkada/{id_test}/{id_gol}/{id_kel}','RefSshPerkadaController@getSubKelompok');
    Route::get('/getTarifPerkada2/{id_test}/{id_gol}/{id_kel}/{is_subkel}','RefSshPerkadaController@getTarif2');

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