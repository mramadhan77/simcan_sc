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

//Route laporan
Route::get('/printHitungSimulasiASB/{id_perhitungan}/{id_aktivitas}/{d1}/{d2}','Laporan\CetakASBAktivitasHitungController@printASBAktivitas')->name('CetakSimulasiASB1');
Route::get('/printHitungSimulasiASB2/{id_aktivitas}/{d1}/{d2}','Laporan\CetakASBAktivitasHitung2Controller@printASBAktivitas');
Route::get('/printHitungRumusASB/{id_aktivitas}','Laporan\CetakASBAktivitasRumusController@printASBAktivitas');
Route::get('/printAktivitasASB/{id_aktivitas}','Laporan\CetakASBAktivitasRinciController@printASBAktivitas');
Route::get('/printListASB/{perkada}','Laporan\CetakListASBController@printListASB');
Route::get('/printDuplikasiASB/{perkada}','Laporan\CetakListASBRedundantController@printDuplikasiASB');
Route::get('/printValiditasASB/{perkada}','Laporan\CetakASBAktivitasValiditasController@printValiditasASB');

Route::get('/PrintKompilasiProgramdanPaguRenja/{id_unit}','Laporan\CetakRenjaController@KompilasiProgramdanPaguRenja');
// Route::get('/PrintKompilasiKegiatandanPaguRenja/{id_unit}','Laporan\CetakRenjaController@KompilasiKegiatandanPaguRenja');
Route::get('/PrintCekASBRenja/{id_unit}','Laporan\CetakRenjaController@CekSSHRancanganRenja');
Route::get('/PrintCekSSHRenja/{id_unit}','Laporan\CetakRenjaController@CekSSHRancanganRenja');
Route::get('/Print_T_VI_C_1/{id_unit}','Laporan\CetakRenjaController@T_VI_C_1');

Route::get('/PrintKompilasiProgramdanPagu/{tahun}','Laporan\CetakRkpdController@T_V_C_66');
// Route::get('/PrintKompilasiProgramdanPagu','Laporan\CetakRpjmdController@KompilasiProgramdanPagu');

//Report SSH
Route::get('/printSsh', 'TrxCetakSshAsbController@dash_cetak_ssh');
Route::get('/printGolonganSsh', 'Laporan\CetakSshController@printGolonganSsh');
Route::get('/printKelompokSsh', 'Laporan\CetakSshKelompokController@printSshKelompok');
Route::get('/printSubKelompokSsh', 'Laporan\CetakSshSubKelompokController@printSshSubKelompok');
Route::get('/printTarifSsh', 'Laporan\CetakSshTarifController@printSshTarif');
// Route::get('/printPerkadaSsh/{id_perkada}/{id_zona}', 'Laporan\CetakSshPerkadaController@printSshPerkada');
Route::get('/printPerkadaSsh/{id_perkada}/{id_zona}', 'Laporan\CetakSshPerkadaTarif2Controller@printSshPerkadaTarif');
Route::get('/printItemSshX', 'Laporan\CetakSshPerkadaTarif2Controller@printSshItemDetail');

Route::group(['prefix' => 'cetakssh'], function () {
    Route::get('/getPerkadaSsh', 'TrxCetakSshAsbController@getPerkadaSsh');
    Route::get('/getZonaPerkada/{id_perkada}', 'TrxCetakSshAsbController@getZonaPerkada');
    Route::get('/jenis_report_ssh', 'TrxCetakSshAsbController@jenis_report_ssh');
    Route::get('/getGolonganSsh', 'TrxCetakSshAsbController@getGolonganSsh');
    Route::get('/getKelompokSsh/{id_golongan}', 'TrxCetakSshAsbController@getKelompokSsh');
    Route::get('/getSubKelompokSsh/{id_kelompok}', 'TrxCetakSshAsbController@getSubKelompokSsh');
    
});

//Route Laporan Dash
Route::group(['prefix' => 'cetak'], function () {
    Route::any('/rpjmd', ['uses'=>'TrxCetak5TahunanController@dash_cetak_rpjmd'])->middleware('menu:20');
    Route::any('/renstra', ['uses'=>'TrxCetak5TahunanController@dash_cetak_renstra'])->middleware('menu:30');
    Route::any('/jenis_rpjmd', ['uses'=>'TrxCetak5TahunanController@jenis_rpjmd']);
    Route::any('/jenis_renstra', ['uses'=>'TrxCetak5TahunanController@jenis_renstra']);
    Route::any('/getDokumenRpjmd', ['uses'=>'TrxCetak5TahunanController@getDokumenRpjmd']);
    Route::any('/getMisiRpjmd', ['uses'=>'TrxCetak5TahunanController@getMisiRpjmd']);
    Route::any('/getTujuanRpjmd/{misi}', ['uses'=>'TrxCetak5TahunanController@getTujuanRpjmd']);
    Route::any('/getSasaranRpjmd/{tujuan}', ['uses'=>'TrxCetak5TahunanController@getSasaranRpjmd']);
    Route::any('/getDokumenRenstra/{id_unit}', ['uses'=>'TrxCetak5TahunanController@getDokumenRenstra']);


    Route::any('/rkpd', ['uses'=>'TrxCetakTahunanController@dash_cetak_rkpd'])->middleware('menu:40');
    Route::any('/rkpdfinal', ['uses'=>'TrxCetakTahunanController@dash_cetak_rkpd_final'])->middleware('menu:40');
    Route::any('/renja', ['uses'=>'TrxCetakTahunanController@dash_cetak_renja'])->middleware('menu:50');
    Route::any('/ranwalrenja', ['uses'=>'TrxCetakTahunanController@dash_cetak_ranwalrenja'])->middleware('menu:50');
    Route::any('/musren',['uses'=> 'TrxCetakTahunanController@dash_cetak_musren'])->middleware('menu:60');
    Route::any('/forum',['uses'=> 'TrxCetakTahunanController@dash_cetak_forum'])->middleware('menu:60');
    Route::any('/pokir', ['uses'=>'TrxCetakTahunanController@dash_cetak_pokir'])->middleware('menu:50');
    Route::any('/prarka',['uses'=> 'TrxCetakTahunanController@dash_cetak_pra_rka'])->middleware('menu:50');

    Route::any('/jenis_renja',['uses'=> 'RefCetakController@jenis_renja']);
    Route::any('/jenis_renja_ranwal',['uses'=> 'RefCetakController@jenis_renja_ranwal']);
    Route::any('/jenis_musrenbang',['uses'=> 'RefCetakController@jenis_musrenbang']);
    Route::any('/jenis_forum',['uses'=> 'RefCetakController@jenis_forum']);
    Route::any('/jenis_rkpd_final',['uses'=> 'RefCetakController@jenis_rpkd_final']);
    
    Route::any('/getProgramRanwalRenja/{unit}/{tahun}', 'RefCetakController@getProgram_RanwalRenja');
    Route::any('/getKegiatanRanwalRenja/{program}', 'RefCetakController@getKegiatan_RanwalRenja');
    
    Route::any('/getProgramRancanganRenja/{unit}/{tahun}', 'RefCetakController@getProgram_RancanganRenja');
    Route::any('/getKegiatanRancanganRenja/{program}', 'RefCetakController@getKegiatan_RancanganRenja');
    
    Route::any('/getProgramForum/{unit}/{tahun}', 'RefCetakController@getProgram_Forum');
    Route::any('/getKegiatanForum/{program}', 'RefCetakController@getKegiatan_Forum');
    
    Route::any('/getProgramRkpdFinal/{unit}/{tahun}', 'RefCetakController@getProgram_Rkpd_Final');
    Route::any('/getKegiatanRkpdFinal/{program}', 'RefCetakController@getKegiatan_Rkpd_Final');
    
    Route::any('/apbd', ['uses'=>'TrxCetakTahunanController@dash_cetak_apbd']);
    Route::any('/jenis_apbd',['uses'=> 'RefCetakController@jenis_APBD']);
    Route::any('/getProgramAPBD/{unit}/{tahun}', 'RefCetakController@getProgram_APBD');
    Route::any('/getKegiatanAPBD/{program}', 'RefCetakController@getKegiatan_APBD');
    Route::any('/getDokAPBD/{itahun}',['uses'=> 'RefCetakController@getDokAPBD']);

});

//Report Renja
// Route::get('/PrintPraRKA/{id_renja}', 'Laporan\CetakRenjaController@PraRKA');
Route::get('/PrintPraRKA/{id_renja}/{sub}', 'Laporan\CetakRenjaController@PraRKA');
Route::get('/PrintPraRKA2/{id_sub}/{tahun}', 'Laporan\CetakRenjaController@PraRKA2');
Route::get('/PrintAPBD/{tahun}', 'Laporan\CetakRenjaController@Apbd');
Route::get('/PrintRingkasAPBD/{tahun}', 'Laporan\CetakRenjaController@RingkasApbd');
Route::get('/PrintKompilasiKegiatandanPaguRenja/{id_unit}/{tahun}', 'Laporan\CetakRenjaController@KompilasiKegiatandanPaguRenja');
Route::get('/PrintRingkasanRenjaUrusan/{tahun}','Laporan\CetakRenjaController@ApbdOrganisasi');
Route::get('/PrintRingkasanRenjaUrusan1/{tahun}','Laporan\CetakRenjaController@ApbdUrusan');
Route::get('/PrintKompilasiAktivitasRenja/{id_renja}', 'Laporan\CetakRenjaController@KompilasiAktivitas');
Route::get('/PrintPrakiraanMaju/{id_sub}/{tahun}', 'Laporan\CetakRenjaController@PrakiraanMaju');
Route::get('/PrintBandingPaguBelanja/{id_unit}/{tahun}', 'Laporan\CetakRenjaController@BandingPaguBelanja');

//Report Ranwal
Route::any('/PrintKompilasiProgramRanwalRenja','Laporan\CetakRanwalRenjaController@KompilasiProgramdanPaguRanwalRenja');
Route::any('/PrintKompilasiKegiatanRanwalRenja','Laporan\CetakRanwalRenjaController@KompilasiKegiatandanPaguRanwalRenja');
Route::any('/CekRanwalRenja','Laporan\CetakRanwalRenjaController@CekProgressRanwalRenja');
Route::get('/PrintMatrikSasProgRenjaRanwal','Laporan\CetakRanwalRenjaController@SasaranProgramRenjaRanwal');
Route::get('/PrintMusrenRanwal/{tahun}','Laporan\CetakMusrenController@MusrenRanwalRenja');

//Report Forum
Route::get('/PrintKompilasiKegiatandanPaguF/{id_unit}/{tahun}', 'Laporan\CetakForumController@KompilasiKegiatandanPaguForum');
Route::get('/PrintAPBDF/{tahun}', 'Laporan\CetakForumController@Apbd');
Route::get('/PrintPraRKAF/{id_renja}/{sub}', 'Laporan\CetakForumController@PraRKA');
Route::get('/PrintPraRKA2F/{id_sub}/{tahun}', 'Laporan\CetakForumController@PraRKA2');
Route::get('/PrintRingkasAPBDF/{tahun}', 'Laporan\CetakForumController@RingkasApbd');
Route::get('/PrintPrakiraanMajuF/{id_sub}/{tahun}', 'Laporan\CetakForumController@PrakiraanMaju');

//Report RKPD Final
Route::get('/PrintAPBDRF/{tahun}', 'Laporan\CetakRkpdFinalController@Apbd');
Route::get('/PrintPraRKARF/{id_renja}/{sub}', 'Laporan\CetakRkpdFinalController@PraRKA');
Route::get('/PrintPraRKA2RF/{id_sub}/{tahun}', 'Laporan\CetakRkpdFinalController@PraRKA2');
Route::get('/PrintRingkasAPBDRF/{tahun}', 'Laporan\CetakRkpdFinalController@RingkasApbd');


//Report RPJMD
Route::get('/printRPJMDTSK','Laporan\CetakRpjmdController@perumusanAKPembangunan');
Route::get('/printProgPrio','Laporan\CetakRpjmdController@perumusanProgramPrioritasPemda');
Route::get('/PrintProyeksiPendapatan','Laporan\CetakRpjmdController@ProyeksiPendapatan');
Route::get('/PrintReviewRanwalRKPD','Laporan\CetakRpjmdController@ReviewRanwalRKPD');
Route::get('/PrintRumusanReviewRanwal','Laporan\CetakRpjmdController@RumusanProgKeg');
Route::get('/PrintProgPaguRenstra','Laporan\CetakRpjmdController@KompilasiProgramdanPaguRenstra');
Route::get('/printPokir','Laporan\CetakRpjmdController@KompilasiPokir');

Route::get('/printRpjmdHtml','Laporan\CetakRpjmdHtmlController@index');
Route::get('/btnPrintSasProg/{tujuan}/{sasaran}','Laporan\CetakRpjmdHtmlController@SasaranProgram');

//Report Musrenbang
Route::get('/PrintCekASBForum/{id_unit}','Laporan\CetakForumController@CekASBforum');
Route::get('/PrintUsulanRW','Laporan\CetakMusrendesController@printusulanrw');
Route::get('/UsulanPerUnit/{id_unit}/{tahun}','Laporan\CetakMusrenController@UsulanPerUnit');
Route::get('/UsulanPerKecamatan/{id_kecamatan}/{tahun}','Laporan\CetakMusrenController@UsulanPerKecamatan');

//Report Pokir
Route::get('/printUsulanPokir','Laporan\CetakListUsulanPokirController@printListUsulanPokir');
Route::get('/printTLPokir','Laporan\CetakListUsulanPokirController@printListTLPokir');
Route::get('/printTLUnitPokir','Laporan\CetakListUsulanPokirController@printListTLUnitPokir');

Route::get('/PrintPdrb/{tahun}','Laporan\CetakDataDasarController@printPdrb');
Route::get('/PrintPdrbHb/{tahun}','Laporan\CetakDataDasarController@printPdrbHb');
Route::get('/PrintAmh/{tahun}','Laporan\CetakDataDasarController@printAmh');
Route::get('/Printratalamasekolah/{tahun}','Laporan\CetakDataDasarController@printRLS');
Route::get('/Printsenior/{tahun}','Laporan\CetakDataDasarController@printSeniOR');
Route::get('/PrintAps/{tahun}','Laporan\CetakDataDasarController@printAps');
Route::get('/PrintKts/{tahun}','Laporan\CetakDataDasarController@printKts');
Route::get('/Printgurumurid/{tahun}','Laporan\CetakDataDasarController@printGuruMurid');
Route::get('/Printinvestor/{tahun}','Laporan\CetakDataDasarController@printInvestor');
Route::get('/Printinvestasi/{tahun}','Laporan\CetakDataDasarController@printInvestasi');


Route::get('/SinkronSasaran/{unit}/{id_renstra}','Laporan\CetakSinkronisasiSasaranController@printSinkronisasi');
Route::get('/CetakRenstraAll/{unit}/{id_renstra}','Laporan\CetakMatrikRenstraAllController@printRenstra');
Route::get('/CetakRenstra/{unit}/{id_renstra}','Laporan\CetakMatrikRenstraController@printRenstra');
Route::get('/CetakMatrikRpjmd/{id_rpjmd}','Laporan\CetakMatrikRpjmdController@printMatrikRpjmd');
Route::get('/CetakMatrikRpjmdAll/{id_rpjmd}','Laporan\CetakMatrikRpjmdAllController@printMatrikRpjmd');
Route::get('/CetakMatrikRpjmdFull/{id_rpjmd}','Laporan\CetakMatrikRpjmdFullController@printMatrikRpjmd');
Route::get('/SasaranProgram/{tujuan}/{sasaran}','Laporan\CetakRpjmdHtmlController@SasaranProgram');


//Report APBD
//Route::get('/PrintKompilasiKegiatandanPaguF/{id_unit}/{tahun}', 'Laporan\CetakForumController@KompilasiKegiatandanPaguForum');
Route::get('/PrintAPBDAP', 'Laporan\CetakAPBDController@Apbd');
Route::get('/PrintPraRKAAP', 'Laporan\CetakAPBDController@PraRKA');
Route::get('/PrintPraRKA2AP', 'Laporan\CetakAPBDController@PraRKA2');
Route::get('/PrintRingkasAPBDAP', 'Laporan\CetakAPBDController@RingkasApbd');
Route::get('/PrintMatrikSasProgRenjaFinal','Laporan\CetakAPBDController@SasaranProgramRenjaFinal');
//Route::get('/PrintPrakiraanMajuF/{id_sub}/{tahun}', 'Laporan\CetakForumController@PrakiraanMaju');

