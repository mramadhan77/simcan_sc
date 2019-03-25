<?php

namespace App\Http\Controllers\Can;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\CekAkses;
use App\Http\Controllers\SettingController;
use Auth;
// use App\Models\Can\TrxMusrekabDokumen;
// use App\Models\Can\TrxMusrekab;
// use App\Models\Can\TrxMusrekabIndikator;
// use App\Models\Can\TrxMusrekabUrusan;
// use App\Models\Can\TrxMusrekabPelaksana;
// use App\Models\Can\TrxMusrekabProgramPd;
// use App\Models\Can\TrxMusrekabKegiatanPd;
// use App\Models\Can\TrxMusrekabPelaksanaPd;
// use App\Models\Can\TrxMusrekabAktivitasPd;
// use App\Models\Can\TrxMusrekabLokasiPd;
// use App\Models\Can\TrxMusrekabBelanjaPd;



class TrxMusrenbangRkpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getDataDokumen()
    {
        $getDataDokumen = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_dokumen_rkpd,a.nomor_rkpd,a.tanggal_rkpd,a.tahun_rkpd,
                a.uraian_perkada, a.id_unit_perencana, a.jabatan_tandatangan,a.nama_tandatangan,a.nip_tandatangan,a.flag,b.nm_unit,
                CASE a.flag
                    WHEN 0 THEN "fa fa-question"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_icon,
                CASE a.flag
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna
                FROM trx_musrenkab AS a
                INNER JOIN ref_unit AS b ON a.id_unit_perencana = b.id_unit,
                (SELECT @id:=0) z');

        return DataTables::of($getDataDokumen)
        ->addColumn('action', function ($getDataDokumen) {
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnRekapRkpd" class="dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg"></i> Lihat Rekap RKPD</a>
                        </li>
                        <li>
                            <a id="btnEditDokumen" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Dokumen RKPD</a>
                        </li>
                        <li>
                            <a id="btnPostingRkpd" class="dropdown-item"><i class="fa fa-check fa-fw fa-lg"></i> Posting Dokumen RKPD</a>
                        </li>                          
                    </ul>
                </div>
            ';
        })
        ->make(true);
    }

    public function getDataRekap()
    {
        $data = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_musrenkab, a.id_rkpd_rancangan, a.tahun_rkpd, a.uraian_program_rpjmd, a.pagu_ranwal, a.status_data, a.sumber_data, COUNT(c.id_unit) AS jml_unit, 
                COUNT(d.id_program_pd) AS jml_prog_renja, SUM(d.pagu_forum) AS pagu_prog_renja,  COUNT(e.id_kegiatan_pd) AS jml_kegiatan, SUM(e.pagu_forum) AS pagu_kegiatan, 
                COUNT(g.id_aktivitas_pd) AS jml_aktivitas, SUM(e.pagu_forum) AS pagu_aktivitas
                FROM trx_musrenkab AS a
                LEFT OUTER JOIN trx_musrenkab_urusan AS b ON a.id_musrenkab = b.id_musrenkab
                LEFT OUTER JOIN trx_musrenkab_pelaksana AS c ON b.id_musrenkab = c.id_musrenkab AND b.id_urusan_rkpd = c.id_urusan_rkpd
                LEFT OUTER JOIN trx_musrenkab_program_pd AS d ON c.id_pelaksana_rkpd = d.id_pelaksana_rkpd
                LEFT OUTER JOIN trx_musrenkab_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                LEFT OUTER JOIN trx_musrenkab_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                LEFT OUTER JOIN trx_musrenkab_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd,
                (SELECT @id:=0) z
                WHERE a.tahun_rkpd='.Session::get('tahun').' GROUP BY a.id_musrenkab, a.id_rkpd_rancangan, a.tahun_rkpd, a.uraian_program_rpjmd, a.pagu_ranwal, a.status_data, a.sumber_data');

        return DataTables::of($data)
        ->addColumn('action', function ($data) {
            return '
                <button id="btnUnload" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="" title="" class="btn btn-danger">
                <i class="fa fa-reply fa-fw fa-lg"></i> Un-Load Data</button>
                ';
            })        
        ->make(true);

    }

    public function getUnit(Request $request){
        $unit = \App\Models\RefUnit::select();
        if(isset(Auth::user()->getUserSubUnit)){
            foreach(Auth::user()->getUserSubUnit as $data){
                $unit->orWhere(['id_unit' => $data->kd_unit]);                
            }
        }
        $unit = $unit->get();
        if($request->ajax()){
          return json_encode($unit);
        }
    }

public function importData(Request $req){
    $dataProgrkpd=DB::INSERT('INSERT INTO trx_musrenkab (id_rkpd_ranwal,id_rkpd_rancangan, no_urut, tahun_rkpd, jenis_belanja, id_rkpd_rpjmd, thn_id_rpjmd, id_visi_rpjmd, 
        id_misi_rpjmd, id_tujuan_rpjmd, id_sasaran_rpjmd, id_program_rpjmd, uraian_program_rpjmd, pagu_rpjmd, pagu_ranwal, keterangan_program, ket_usulan, 
        status_pelaksanaan, status_data, sumber_data, id_dokumen)
        SELECT DISTINCT a.id_rkpd_ranwal,a.id_rkpd_rancangan, a.no_urut, a.tahun_rkpd, a.jenis_belanja, a.id_rkpd_rpjmd, a.thn_id_rpjmd,
        a.id_visi_rpjmd, a.id_misi_rpjmd, a.id_tujuan_rpjmd, a.id_sasaran_rpjmd, a.id_program_rpjmd, a.uraian_program_rpjmd,
        a.pagu_rpjmd, a.pagu_ranwal, a.keterangan_program, a.ket_usulan, 0, 0, 0, 0
        FROM trx_rkpd_rancangan AS a
        WHERE (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3) AND a.status_data = 2 AND a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
    if($dataProgrkpd == 0){
        return response ()->json (['pesan'=>'Data Program Ranwal RKPD gagal di-Load','status_pesan'=>'0']);
    } else {
        $dataIndikatorrkpd=DB::INSERT('INSERT INTO trx_musrenkab_indikator (tahun_rkpd, no_urut, id_musrenkab, id_indikator_program_rkpd, id_perubahan, kd_indikator, uraian_indikator_program_rkpd, tolok_ukur_indikator, target_rpjmd, target_rkpd, indikator_input, target_input, id_satuan_input, indikator_output, id_satuan_ouput, status_data, sumber_data)
            SELECT DISTINCT  a.tahun_rkpd, b.no_urut, a.id_musrenkab, b.id_indikator_program_rkpd, b.id_perubahan, b.kd_indikator, b.uraian_indikator_program_rkpd, 
            b.tolok_ukur_indikator, b.target_rpjmd, b.target_rkpd, b.indikator_input, COALESCE(b.target_input,0), b.id_satuan_input, b.indikator_output, b.id_satuan_ouput, 
            0, 0 FROM trx_rkpd_rancangan_indikator AS b 
            INNER JOIN trx_rkpd_rancangan AS c ON b.id_rkpd_rancangan = c.id_rkpd_rancangan
            INNER JOIN trx_musrenkab AS a ON c.id_rkpd_rancangan = a.id_rkpd_rancangan
            WHERE b.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
        if($dataIndikatorrkpd == 0){
            return response ()->json (['pesan'=>'Data Indikator Program Ranwal RKPD gagal di-Load','status_pesan'=>'0']);
        } else {
            $dataUrusanrkpd=DB::INSERT('INSERT INTO trx_musrenkab_urusan (tahun_rkpd, no_urut, id_musrenkab, id_urusan_rkpd, id_bidang, sumber_data)
                SELECT DISTINCT a.tahun_rkpd, b.no_urut, a.id_musrenkab, b.id_urusan_rkpd, b.id_bidang, 0 
                FROM trx_musrenkab AS a
                INNER JOIN trx_rkpd_rancangan_urusan AS b ON b.id_rkpd_rancangan= a.id_rkpd_rancangan 
                WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
            if($dataUrusanrkpd == 0){
                return response ()->json (['pesan'=>'Data Urusan Program Ranwal RKPD gagal di-Load','status_pesan'=>'0']);
            } else {
                $dataPelaksanarkpd=DB::INSERT('INSERT INTO trx_musrenkab_pelaksana (tahun_rkpd, no_urut, id_musrenkab, id_pelaksana_rpjmd, id_urusan_rkpd, id_unit, pagu_rpjmd, pagu_rkpd, hak_akses, sumber_data, status_pelaksanaan, ket_pelaksanaan, status_data)
                    SELECT DISTINCT a.tahun_rkpd, c.no_urut, a.id_musrenkab, c.id_pelaksana_rpjmd, c.id_urusan_rkpd, c.id_unit, c.pagu_rpjmd, c.pagu_rkpd, 
                    c.hak_akses, c.sumber_data, c.status_pelaksanaan, c.ket_pelaksanaan, 0
                    FROM trx_musrenkab AS a
                    INNER JOIN trx_rkpd_rancangan_urusan AS b ON b.id_rkpd_rancangan = a.id_rkpd_rancangan
                    INNER JOIN trx_rkpd_rancangan_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                    WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
                if($dataPelaksanarkpd == 0){
                     return response ()->json (['pesan'=>'Data Unit Pelaksana Program Ranwal RKPD gagal di-Load','status_pesan'=>'0']);
                } else {
                    $dataProgpd=DB::INSERT('INSERT INTO trx_musrenkab_program_pd (id_pelaksana_rkpd, tahun_forum, jenis_belanja, no_urut, id_unit, id_rkpd_rancangan, id_renja_program, id_program_renstra, uraian_program_renstra, id_program_ref, pagu_tahun_renstra, pagu_forum, sumber_data, status_pelaksanaan, ket_usulan, status_data, id_dokumen)
                        SELECT c.id_pelaksana_rkpd, f.tahun_forum, f.jenis_belanja, f.no_urut, f.id_unit, f.id_rkpd_rancangan, f.id_renja_program,
                        f.id_program_renstra, f.uraian_program_renstra, f.id_program_ref, f.pagu_tahun_renstra, f.pagu_forum, 0,
                        f.status_pelaksanaan, f.ket_usulan, 0, 0
                        FROM trx_musrenkab AS a                        
                        INNER JOIN trx_musrenkab_urusan AS b ON b.id_musrenkab = a.id_musrenkab
                        INNER JOIN trx_musrenkab_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                        INNER JOIN trx_rkpd_rancangan_urusan AS d ON a.id_rkpd_rancangan = d.id_rkpd_rancangan
                        INNER JOIN trx_rkpd_rancangan_pelaksana AS e ON d.id_urusan_rkpd = e.id_urusan_rkpd
                        INNER JOIN trx_rkpd_rancangan_program_pd AS f ON e.id_pelaksana_rkpd = f.id_rkpd_rancangan
                        WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
                    if($dataProgpd == 0){
                        return response ()->json (['pesan'=>'Data Program Perangkat Daerah gagal di-Load','status_pesan'=>'0']);
                    } else {
                        $dataInprogpd=DB::INSERT('INSERT INTO trx_musrenkab_prog_indikator_pd (tahun_renja, no_urut, id_program_pd, id_program_forum,id_program_renstra, id_indikator_program, id_perubahan, kd_indikator, uraian_indikator_program, tolok_ukur_indikator, target_renstra, target_renja, indikator_output, id_satuan_ouput, indikator_input, target_input, id_satuan_input, status_data, sumber_data)    
                            SELECT f.tahun_forum, f.no_urut, f.id_program_pd,f.id_forum_program,f.id_program_renstra, g.id_indikator_program, g.id_perubahan, g.kd_indikator, 
                            g.uraian_indikator_program, g.tolok_ukur_indikator, g.target_renstra, g.target_renja, g.indikator_output,  g.id_satuan_ouput, g.indikator_input, 
                            COALESCE(g.target_input,0), g.id_satuan_input,  0, 0
                            FROM trx_musrenkab AS a                       
                            INNER JOIN trx_musrenkab_urusan AS b ON b.id_musrenkab = a.id_musrenkab
                            INNER JOIN trx_musrenkab_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                            INNER JOIN trx_rkpd_rancangan_urusan AS d ON a.id_rkpd_rancangan = d.id_rkpd_rancangan
                            INNER JOIN trx_rkpd_rancangan_pelaksana AS e ON d.id_urusan_rkpd = e.id_urusan_rkpd
                            INNER JOIN trx_rkpd_rancangan_program_pd AS f ON e.id_pelaksana_rkpd = f.id_rkpd_rancangan
                            INNER JOIN trx_forum_skpd_program_indikator AS g ON f.id_forum_program = g.id_forum_program
                            WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
                        if($dataInprogpd == 0){
                            return response ()->json (['pesan'=>'Data Indikator Program Perangkat Daerah gagal di-Load','status_pesan'=>'0']);
                        } else {
                            $dataKegpd=DB::INSERT('INSERT INTO trx_musrenkab_kegiatan_pd (id_forum_skpd, id_program_pd, id_unit, tahun_forum, no_urut, id_renja, id_rkpd_renstra, id_program_renstra, 
                                id_kegiatan_renstra, id_kegiatan_ref, uraian_kegiatan_forum, pagu_tahun_kegiatan, pagu_kegiatan_renstra, pagu_plus1_renja, 
                                pagu_plus1_forum, pagu_forum, keterangan_status, status_data, status_pelaksanaan, sumber_data)  
                                SELECT e.id_forum_skpd, d.id_program_pd, d.id_unit, d.tahun_forum, e.no_urut, e.id_renja, e.id_rkpd_renstra,e.id_program_renstra,e.id_kegiatan_renstra,e.id_kegiatan_ref,
                                e.uraian_kegiatan_forum,e.pagu_kegiatan_renstra,e.pagu_tahun_kegiatan,e.pagu_plus1_renja,e.pagu_plus1_forum,e.pagu_forum,e.keterangan_status,0,e.status_pelaksanaan,0 
                                FROM trx_musrenkab AS a
                                INNER JOIN trx_rkpd_rancangan_urusan AS b ON b.id_rkpd_rancangan = a.id_rkpd_rancangan
                                INNER JOIN trx_rkpd_rancangan_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                                INNER JOIN trx_rkpd_rancangan_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                                INNER JOIN trx_forum_skpd AS e ON d.id_forum_program = e.id_forum_program
                                WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
                    //         if($dataKegpd == 0){
                    //             return response ()->json (['pesan'=>'Data Kegiatan Perangkat Daerah RKPD gagal di-Load','status_pesan'=>'0']);
                    //         } else {
                    //             $dataInkegpd=DB::INSERT('INSERT INTO trx_musrenkab_keg_indikator_pd (tahun_renja, no_urut, id_kegiatan_pd, id_program_renstra, id_indikator_kegiatan, id_perubahan,
                    //                  kd_indikator, uraian_indikator_kegiatan, tolok_ukur_indikator, target_renstra, target_renja, indikator_output, id_satuan_ouput, indikator_input,
                    //                   target_input, id_satuan_input, status_data, sumber_data)  
                    //                 SELECT e.tahun_forum, f.no_urut, e.id_kegiatan_pd, f.id_program_renstra, f.id_indikator_kegiatan, 0,
                    //                  f.kd_indikator, f.uraian_indikator_kegiatan, f.tolok_ukur_indikator, f.target_renstra, f.target_renja, f.indikator_output, f.id_satuan_ouput, f.indikator_input,
                    //                  f.target_input, f.id_satuan_input, 0, 0
                    //                 FROM trx_musrenkab AS a
                    //                 INNER JOIN trx_rkpd_rancangan_urusan AS b ON b.id_rkpd_rancangan = a.id_rkpd_rancangan
                    //                 INNER JOIN trx_rkpd_rancangan_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                    //                 INNER JOIN trx_rkpd_rancangan_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                    //                 INNER JOIN trx_rkpd_rancangan_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                    //                 INNER JOIN trx_forum_skpd_kegiatan_indikator AS f ON e.id_forum_skpd = f.id_forum_skpd
                    //                 WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
                    //             if($dataInkegpd == 0){
                    //                 return response ()->json (['pesan'=>'Data Indikator Kegiatan Perangkat Daerah gagal di-Load','status_pesan'=>'0']);
                    //             } else {
                    //                 $dataPelSubUnit=DB::INSERT('INSERT INTO trx_musrenkab_pelaksana_pd (tahun_forum, no_urut, id_kegiatan_pd, id_pelaksana_forum, id_sub_unit, id_pelaksana_renja, 
                    //                     id_lokasi, sumber_data, ket_pelaksana, status_pelaksanaan, status_data) 
                    //                     SELECT e.tahun_forum, f.no_urut, e.id_kegiatan_pd, f.id_pelaksana_forum ,f.id_sub_unit, f.id_pelaksana_renja, f.id_lokasi, 0, f.ket_pelaksana, 0, 0
                    //                     FROM trx_musrenkab AS a
                    //                     INNER JOIN trx_rkpd_rancangan_urusan AS b ON b.id_rkpd_rancangan = a.id_rkpd_rancangan
                    //                     INNER JOIN trx_rkpd_rancangan_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                    //                     INNER JOIN trx_rkpd_rancangan_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                    //                     INNER JOIN trx_rkpd_rancangan_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                    //                     INNER JOIN trx_forum_skpd_pelaksana AS f ON e.id_forum_skpd = f.id_aktivitas_forum
                    //                     WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
                    //                 if($dataPelSubUnit == 0){
                    //                     return response ()->json (['pesan'=>'Data Sub Unit Pelaksana Kegiatan gagal di-Load','status_pesan'=>'0']);
                    //                 } else {
                    //                     $dataAktivitas=DB::INSERT('INSERT INTO trx_musrenkab_aktivitas_pd (id_aktivitas_forum,id_pelaksana_pd, tahun_forum, no_urut, sumber_aktivitas, id_aktivitas_asb, 
                    //                             id_aktivitas_renja, uraian_aktivitas_kegiatan, volume_aktivitas_1, volume_forum_1, id_satuan_1, volume_aktivitas_2, volume_forum_2, id_satuan_2, 
                    //                             id_program_nasional, id_program_provinsi, jenis_kegiatan, sumber_dana, pagu_aktivitas_renja, pagu_aktivitas_forum, pagu_musren, status_data, 
                    //                             status_pelaksanaan, keterangan_aktivitas, status_musren, sumber_data, id_satuan_publik)
                    //                             SELECT g.id_aktivitas_forum,f.id_pelaksana_pd, f.tahun_forum, g.no_urut, g.sumber_aktivitas, g.id_aktivitas_asb, g.id_aktivitas_renja, g.uraian_aktivitas_kegiatan, 
                    //                             g.volume_aktivitas_1, g.volume_forum_1, g.id_satuan_1, g.volume_aktivitas_2, g.volume_forum_2, g.id_satuan_2, g.id_program_nasional, g.id_program_provinsi, 
                    //                             g.jenis_kegiatan, g.sumber_dana, g.pagu_aktivitas_renja, g.pagu_aktivitas_forum, g.pagu_musren, 0, g.status_pelaksanaan, g.keterangan_aktivitas, g.status_musren, 
                    //                             0, g.id_satuan_publik
                    //                             FROM trx_musrenkab AS a
                    //                             INNER JOIN trx_rkpd_rancangan_urusan AS b ON b.id_rkpd_rancangan = a.id_rkpd_rancangan
                    //                             INNER JOIN trx_rkpd_rancangan_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                    //                             INNER JOIN trx_rkpd_rancangan_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                    //                             INNER JOIN trx_rkpd_rancangan_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                    //                             INNER JOIN trx_rkpd_rancangan_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                    //                             INNER JOIN trx_forum_skpd_aktivitas AS g ON f.id_pelaksana_forum = g.id_forum_skpd
                    //                             WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
                    //                     if($dataAktivitas == 0){
                    //                         return response ()->json (['pesan'=>'Data Aktivitas Perangkat Daerah Kegiatan gagal di-Load','status_pesan'=>'0']);
                    //                     } else {
                    //                         $dataLokasi=DB::INSERT('INSERT INTO trx_musrenkab_lokasi_pd (id_lokasi_forum, tahun_forum, no_urut, id_aktivitas_pd, id_lokasi, id_lokasi_renja, id_lokasi_teknis, 
                    //                                 jenis_lokasi, volume_1, volume_usulan_1, volume_2, volume_usulan_2, id_satuan_1, id_satuan_2, id_desa, id_kecamatan, rt, rw, uraian_lokasi, lat, 
                    //                                 lang, status_data, status_pelaksanaan, ket_lokasi, sumber_data)
                    //                                 SELECT h.id_lokasi_forum, g.tahun_forum, h.no_urut, g.id_aktivitas_pd, h.id_lokasi, h.id_lokasi_renja, h.id_lokasi_teknis, h.jenis_lokasi, h.volume_1, 
                    //                                 h.volume_usulan_1, h.volume_2, h.volume_usulan_2, h.id_satuan_1, h.id_satuan_2, h.id_desa, h.id_kecamatan, h.rt, h.rw, h.uraian_lokasi, 
                    //                                 h.lat, h.lang, 0, 0, h.ket_lokasi,0 
                    //                                 FROM trx_musrenkab AS a
                    //                                 INNER JOIN trx_rkpd_rancangan_urusan AS b ON b.id_rkpd_rancangan = a.id_rkpd_rancangan
                    //                                 INNER JOIN trx_rkpd_rancangan_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                    //                                 INNER JOIN trx_rkpd_rancangan_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                    //                                 INNER JOIN trx_rkpd_rancangan_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                    //                                 INNER JOIN trx_rkpd_rancangan_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                    //                                 INNER JOIN trx_rkpd_rancangan_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd
                    //                                 INNER JOIN trx_forum_skpd_lokasi AS h ON g.id_aktivitas_forum = h.id_pelaksana_forum
                    //                                 WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
                    //                         if($dataLokasi == 0){
                    //                             return response ()->json (['pesan'=>'Data Lokasi Aktivitas Kegiatan gagal di-Load','status_pesan'=>'0']);
                    //                         } else {
                    //                             $dataBelanja=DB::INSERT('INSERT INTO trx_musrenkab_belanja_pd (tahun_forum, no_urut, id_aktivitas_pd, id_belanja_forum, id_zona_ssh, id_belanja_renja, 
                    //                                     sumber_belanja, id_aktivitas_asb, id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, harga_satuan, 
                    //                                     jml_belanja, volume_1_forum, id_satuan_1_forum, volume_2_forum, id_satuan_2_forum, harga_satuan_forum, jml_belanja_forum, status_data, sumber_data)
                    //                                     SELECT g.tahun_forum, h.no_urut, g.id_aktivitas_pd, h.id_belanja_forum, h.id_zona_ssh, h.id_belanja_renja, h.sumber_belanja, h.id_aktivitas_asb, 
                    //                                     h.id_item_ssh, h.id_rekening_ssh, h.uraian_belanja, h.volume_1, h.id_satuan_1, h.volume_2, h.id_satuan_2, h.harga_satuan_forum, h.jml_belanja_forum, 
                    //                                     h.volume_1_forum, h.id_satuan_1_forum, h.volume_2_forum, h.id_satuan_2_forum, h.harga_satuan_forum, h.jml_belanja_forum, 0, 0
                    //                                     FROM trx_musrenkab AS a
                    //                                     INNER JOIN trx_rkpd_rancangan_urusan AS b ON b.id_rkpd_rancangan = a.id_rkpd_rancangan
                    //                                     INNER JOIN trx_rkpd_rancangan_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                    //                                     INNER JOIN trx_rkpd_rancangan_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                    //                                     INNER JOIN trx_rkpd_rancangan_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                    //                                     INNER JOIN trx_rkpd_rancangan_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                    //                                     INNER JOIN trx_rkpd_rancangan_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd
                    //                                     INNER JOIN trx_forum_skpd_belanja AS h ON g.id_aktivitas_forum = h.id_lokasi_forum
                    //                                     WHERE a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
                    //                             if($dataBelanja == 0){
                    //                                 return response ()->json (['pesan'=>'Data Rincian Belanja gagal di-Load','status_pesan'=>'0']);
                    //                             } else {
                    //                                 return response ()->json (['pesan'=>'Data Awal Musrenbang RKPD Berhasil di-Load','status_pesan'=>'1']);
                    //                             }                                                
                    //                         }   
                    //                     }
                    //                 }
                                }
                    }
                }
            }
        }
    }
}

    public function loadData()
    {
        // if(Auth::check()){  
            if(Session::has('tahun')){           
                return view('can.musrenrkpd.load'); 
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
        // if(Auth::check()){
            if(Session::has('tahun')){
                return view('can.musrenrkpd.index');  
            } else {
                return redirect('home');
            }            
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function sesuai(Request $request, Builder $htmlBuilder)
    {
        // if(Auth::check()){
            if(Session::has('tahun')){
                return view('can.musrenrkpd.blangsung');  
            } else {
                return redirect('home');
            }            
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function getSelectProgram($tahun_rkpd){
        $getSelect=DB::SELECT('SELECT (@id:=@id+1) as urut, a.id_rkpd_ranwal,a.id_rkpd_rancangan, a.tahun_rkpd, a.jenis_belanja, a.id_rkpd_rpjmd, a.thn_id_rpjmd,
        a.id_visi_rpjmd, a.id_misi_rpjmd, a.id_tujuan_rpjmd, a.id_sasaran_rpjmd, a.id_program_rpjmd, a.uraian_program_rpjmd
        FROM trx_rkpd_rancangan AS a
        LEFT OUTER JOIN trx_musrenkab AS c ON a.tahun_rkpd = c.tahun_rkpd AND a.id_rkpd_rancangan = c.id_rkpd_rancangan, (SELECT @id:=0) z
        WHERE c.id_rkpd_rancangan is null AND a.status_data = 2 AND (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3) AND a.tahun_rkpd ='.$tahun_rkpd);

        return DataTables::of($getSelect)
        ->addColumn('action',function($getSelect){
          return '
              <button id="btnReLoad" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="" title="" class="btn btn-primary">
              <i class="fa fa-download fa-fw fa-lg"></i> Load Data</button>
          ' ;})
        ->make(true);
    }

    public function getDataPerencana()
    {
        $dataPerencana = DB::SELECT('SELECT a.id_rkpd_ranwal,b.id_forum_rkpdprog, a.no_urut, a.tahun_rkpd, a.jenis_belanja, a.id_rkpd_rpjmd, a.thn_id_rpjmd,
                    a.id_visi_rpjmd, a.id_misi_rpjmd, a.id_tujuan_rpjmd, a.id_sasaran_rpjmd, a.id_program_rpjmd, a.uraian_program_rpjmd,
                    a.pagu_rpjmd, b.pagu_ranwal, a.keterangan_program, a.ket_usulan, 0, 0, 0, 0
                    FROM trx_rkpd_ranwal AS a
                    INNER JOIN trx_forum_skpd_program_ranwal AS b ON a.tahun_rkpd = b.tahun_forum AND a.id_rkpd_ranwal = b.id_rkpd_ranwal
                            LEFT OUTER JOIN trx_rkpd_rancangan AS c ON b.tahun_forum = c.tahun_rkpd AND b.id_rkpd_ranwal = c.id_rkpd_ranwal
                    WHERE c.id_rkpd_ranwal is null');

        return json_encode($dataPerencana);
    }

    public function unLoadData(Request $req)
    {
        try{
            $result=DB::DELETE('DELETE FROM trx_musrenkab WHERE id_musrenkab = '.$req->id_musrenkab);
            return response ()->json (['pesan'=>'Data Program Berhasil Unload','status_pesan'=>'1']);
        }
        catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Program Gagal Unload ('.$error_code.')','status_pesan'=>'0']);
        }
    }
          
}
