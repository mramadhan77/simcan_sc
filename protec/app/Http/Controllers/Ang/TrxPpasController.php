<?php

namespace App\Http\Controllers\Ang;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use DB;
use Response;
use Session;
use Auth;
use CekAkses;
use App\Fungsi as Fungsi;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use Doctrine\DBAL\Query\QueryException;
use App\Models\RefUnit;
use App\Models\Ang\TrxAnggaranDokumen;
use App\Models\Ang\TrxAnggaranProgram;
use App\Models\Ang\TrxAnggaranIndikator;
use App\Models\Ang\TrxAnggaranUrusan;
use App\Models\Ang\TrxAnggaranPelaksana;
use App\Models\Ang\TrxAnggaranProgramPd;
use App\Models\Ang\TrxAnggaranProgIndikatorPd;
use App\Models\Ang\TrxAnggaranKegiatanPd;
use App\Models\Ang\TrxAnggaranKegIndikatorPd;
use App\Models\Ang\TrxAnggaranPelaksanaPd;
use App\Models\Ang\TrxAnggaranAntivitasPd;
use App\Models\Ang\TrxAnggaranLokasiPd;
use App\Models\Ang\TrxAnggaranBelanjaPd;



class TrxPpasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   public function index(Request $request, Builder $htmlBuilder)
    {
        if(Session::has('tahun')){           
            return view('ppas.doku'); 
        } else {
            return redirect('home');
        }
    }
    
    public function progpemda(Request $request, Builder $htmlBuilder)
    {
        if(Session::has('tahun')){           
            return view('ppas.progpemda'); 
        } else {
            return redirect('home');
        }
    }

    public function progopd(Request $request, Builder $htmlBuilder)
    {
        if(Session::has('tahun')){
            return view('ppas.progopd');  
        } else {
            return redirect('home');
        }
    }

    public function sesuai(Request $request, Builder $htmlBuilder)
    {
        if(Session::has('tahun')){
            return view('ppas.sesuai');  
        } else {
            return redirect('home');
        }
    }

    public function getDataDokumen()
    {
         $getDataDokumen = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_dokumen_keu, a.jns_dokumen_keu, a.kd_dokumen_keu,a.id_perubahan, a.id_dokumen_ref, 
                    a.tahun_anggaran, a.nomor_keu, a.tanggal_keu, a.uraian_perkada, a.id_unit_ppkd, a.jabatan_tandatangan, 
                    a.nama_tandatangan, a.nip_tandatangan, a.flag, a.created_at, a.updated_at, b.nm_unit,
                    CASE a.flag
                        WHEN 0 THEN "fa fa-question"
                        WHEN 1 THEN "fa fa-check-square-o"
                    END AS status_icon,
                    CASE a.flag
                        WHEN 0 THEN "red"
                        WHEN 1 THEN "green"
                    END AS warna
                    FROM trx_anggaran_dokumen AS a
                    INNER JOIN ref_unit AS b ON a.id_unit_ppkd = b.id_unit, (SELECT @id:=0) z
                    WHERE a.jns_dokumen_keu = 0 AND a.kd_dokumen_keu=0');

        return DataTables::of($getDataDokumen)
        ->addColumn('action', function ($getDataDokumen) {
            if ($getDataDokumen->flag==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnEditDokumen" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-warning"></i> Ubah Dokumen PPAS</a>
                        </li>
                        <li>
                            <a id="btnProsesLoad" class="dropdown-item"><i class="fa fa-refresh fa-fw fa-lg text-primary"></i> Load Data RKPD</a>
                        </li>  
                        <li>
                            <a id="btnLihatRekap" class="dropdown-item"><i class="fa fa-binoculars fa-fw fa-lg text-info"></i> RKAP Data PPAS</a>
                        </li>  
                        <li>
                            <a id="btnPostingRkpd" class="dropdown-item"><i class="fa fa-check fa-fw fa-lg text-success"></i> Posting Dokumen PPAS</a>
                        </li>                          
                    </ul>
                </div>
            ';
            if ($getDataDokumen->flag==1)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnLihatRekap" class="dropdown-item"><i class="fa fa-binoculars fa-fw fa-lg text-info"></i> RKAP Data PPAS</a>
                        </li>  
                        <li>
                            <a id="btnPostingRkpd" class="dropdown-item"><i class="fa fa-times fa-fw fa-lg text-danger"></i> Un-Posting Dokumen RKPD</a>
                        </li>                          
                    </ul>
                </div>
            ';
        })
        ->make(true);
    }

public function getDataRekap(Request $req){
        $data = DB::SELECT('SELECT CONCAT(RIGHT(CONCAT("0",v.kd_urusan),2),".",RIGHT(CONCAT("0",v.kd_bidang),2),".",RIGHT(CONCAT("000",u.kd_unit),3)) AS kd_unit,
                y.id_dokumen_keu, y.id_unit, u.nm_unit, SUM(y.jml_program) AS jml_program, SUM(y.jml_kegiatan) AS jml_kegiatan, 
                SUM(y.jml_pelaksana) AS jml_pelaksana, SUM(y.jml_aktivitas) AS jml_aktivitas, SUM(y.jml_pendapatan) AS jml_pendapatan, 
                SUM(y.jml_belanja) AS jml_belanja, SUM(y.jml_pembiayaan) AS jml_pembiayaan
                FROM (SELECT x.id_dokumen_keu, x.id_unit, COUNT(x.id_program_pd) AS jml_program, 0 AS jml_kegiatan, 0 AS jml_pelaksana, 
                0 AS jml_aktivitas, 0 AS jml_pendapatan, 0 AS jml_belanja, 0 AS jml_pembiayaan 
                FROM trx_anggaran_program_pd AS x 
                GROUP BY x.id_dokumen_keu, x.id_unit
                UNION
                SELECT d.id_dokumen_keu, d.id_unit, 0 AS jml_program,COUNT(e.id_kegiatan_pd) AS jml_kegiatan, 0 AS jml_pelaksana, 
                0 AS jml_aktivitas, 0 AS jml_pendapatan, 0 AS jml_belanja, 0 AS jml_pembiayaan  
                FROM trx_anggaran_program_pd AS d
                INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                GROUP BY d.id_dokumen_keu, d.id_unit
                UNION
                SELECT x.id_dokumen_keu, x.id_unit, 0 AS jml_program, 0 AS jml_kegiatan, COUNT(x.id_sub_unit) AS jml_pelaksana, 
                0 AS jml_aktivitas, 0 AS jml_pendapatan, 0 AS jml_belanja, 0 AS jml_pembiayaan  
                FROM (SELECT d.id_dokumen_keu, d.id_unit, f.id_sub_unit
                FROM trx_anggaran_program_pd AS d
                INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                INNER JOIN trx_anggaran_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                GROUP BY d.id_dokumen_keu, d.id_unit, f.id_sub_unit) AS x
                GROUP BY x.id_dokumen_keu, x.id_unit
                UNION
                SELECT d.id_dokumen_keu, d.id_unit, 0 AS jml_program, 0 AS jml_kegiatan, 0 AS jml_pelaksana, 
                COUNT(g.id_aktivitas_pd) AS jml_aktivitas, 0 AS jml_pendapatan, 0 AS jml_belanja, 0 AS jml_pembiayaan
                FROM trx_anggaran_program_pd AS d
                INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                INNER JOIN trx_anggaran_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                INNER JOIN trx_anggaran_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd
                GROUP BY d.id_dokumen_keu, d.id_unit
                UNION
                SELECT x.id_dokumen_keu, x.id_unit, 0 AS jml_program, 0 AS jml_kegiatan, 0 AS jml_pelaksana, 0 AS jml_aktivitas, 
                SUM(x.jml_pendapatan) AS jml_pendapatan,SUM(x.jml_belanja) AS jml_belanja, (SUM(x.jml_pembiayaan_terima) - SUM(x.jml_pembiayaan_keluar)) AS jml_pembiayaan
                FROM (SELECT d.id_dokumen_keu, d.id_unit, 
                CASE WHEN i.kd_rek_1 = 4 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_pendapatan,
                CASE WHEN i.kd_rek_1 = 5 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_belanja,
                CASE WHEN i.kd_rek_1 = 6 AND i.kd_rek_2 = 1 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_pembiayaan_terima,
                CASE WHEN i.kd_rek_1 = 6 AND i.kd_rek_2 = 2 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_pembiayaan_keluar
                FROM trx_anggaran_program_pd AS d
                INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                INNER JOIN trx_anggaran_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                INNER JOIN trx_anggaran_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd
                INNER JOIN trx_anggaran_belanja_pd AS h ON g.id_aktivitas_pd = h.id_aktivitas_pd
                INNER JOIN ref_rek_5 AS i ON h.id_rekening_ssh = i.id_rekening) AS x
                GROUP BY x.id_dokumen_keu, x.id_unit) AS y
                INNER JOIN ref_unit AS u ON y.id_unit = u.id_unit
                INNER JOIN ref_bidang AS v ON u.id_bidang = v.id_bidang
                WHERE y.id_dokumen_keu = '.$req->id_dokumen_keu.'
                GROUP BY y.id_dokumen_keu, y.id_unit, u.nm_unit, u.kd_unit, v.kd_urusan, v.kd_bidang
                ORDER BY u.kd_unit, v.kd_urusan, v.kd_bidang');
                
        return DataTables::of($data)->make(true);
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
        $dataProgrkpd=DB::INSERT('INSERT INTO trx_anggaran_program (id_dokumen_keu, id_rkpd_ranwal, id_rkpd_final, no_urut, tahun_anggaran, jenis_belanja, id_rkpd_rpjmd, thn_id_rpjmd, 
            id_visi_rpjmd, id_misi_rpjmd, id_tujuan_rpjmd, id_sasaran_rpjmd, id_program_rpjmd, uraian_program_rpjmd, pagu_rkpd, pagu_keuangan, keterangan_program, ket_usulan, 
            status_pelaksanaan, status_data,  sumber_data)
            SELECT DISTINCT b.id_dokumen_keu, a.id_rkpd_ranwal,a.id_rkpd_rancangan, a.no_urut, a.tahun_rkpd, a.jenis_belanja, a.id_rkpd_rpjmd, a.thn_id_rpjmd,
            a.id_visi_rpjmd, a.id_misi_rpjmd, a.id_tujuan_rpjmd, a.id_sasaran_rpjmd, a.id_program_rpjmd, a.uraian_program_rpjmd,
            a.pagu_ranwal, a.pagu_ranwal, a.keterangan_program, a.ket_usulan, 0, 0, 0
            FROM trx_rkpd_final AS a 
            INNER JOIN trx_anggaran_dokumen AS b ON a.id_dokumen = b.id_dokumen_ref
			WHERE (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3) AND a.status_data=2 AND b.id_dokumen_keu='.$req->id_dokumen_rkpd);
        if($dataProgrkpd == 0){
            return response ()->json (['pesan'=>'Data Program RKPD Final gagal di-Load','status_pesan'=>'0']);
        } else {
            $dataIndikatorrkpd=DB::INSERT('INSERT INTO trx_anggaran_indikator (tahun_rkpd, no_urut, id_anggaran_pemda, id_indikator_rkpd_final, id_perubahan, kd_indikator, 
            uraian_indikator_program_rkpd, tolok_ukur_indikator, target_keuangan, target_rkpd, indikator_input, target_input, id_satuan_input, indikator_output, 
            id_satuan_output, status_data, sumber_data)
                SELECT DISTINCT  a.tahun_anggaran, b.no_urut, a.id_anggaran_pemda, b.id_indikator_program_rkpd, b.id_perubahan, b.kd_indikator, b.uraian_indikator_program_rkpd, 
                b.tolok_ukur_indikator, b.target_rpjmd, b.target_rkpd, b.indikator_input, COALESCE(b.target_input,0), b.id_satuan_input, b.indikator_output, b.id_satuan_ouput, 
                0, 0 FROM trx_rkpd_final_indikator AS b 
                INNER JOIN trx_anggaran_program AS a ON a.id_rkpd_final = b.id_rkpd_rancangan 
                WHERE a.id_dokumen_keu='.$req->id_dokumen_rkpd);
            if($dataIndikatorrkpd == 0){
                return response ()->json (['pesan'=>'Data Indikator Program RKPD Final gagal di-Load','status_pesan'=>'0']);
            } else {
                $dataUrusanrkpd=DB::INSERT('INSERT INTO trx_anggaran_urusan (tahun_anggaran, no_urut, id_anggaran_pemda, id_urusan_rkpd_final, id_bidang, sumber_data)
                    SELECT DISTINCT a.tahun_anggaran, b.no_urut, a.id_anggaran_pemda, b.id_urusan_rkpd, b.id_bidang, 0 
                    FROM trx_anggaran_program AS a 
                    INNER JOIN trx_rkpd_final_urusan AS b ON a.id_rkpd_final = b.id_rkpd_rancangan 
                    WHERE a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                if($dataUrusanrkpd == 0){
                    return response ()->json (['pesan'=>'Data Urusan Program RKPD Final gagal di-Load','status_pesan'=>'0']);
                } else {
                    $dataPelaksanarkpd=DB::INSERT('INSERT INTO trx_anggaran_pelaksana (tahun_anggaran, no_urut, id_anggaran_pemda, id_pelaksana_rkpd_final, 
                    id_urusan_anggaran, id_unit, pagu_rkpd_final, pagu_anggaran, hak_akses, sumber_data, status_pelaksanaan, ket_pelaksanaan, status_data)
                        SELECT DISTINCT a.tahun_anggaran, c.no_urut, a.id_anggaran_pemda, c.id_pelaksana_rkpd, b.id_urusan_anggaran, c.id_unit, c.pagu_rpjmd, c.pagu_rkpd, c.hak_akses, c.sumber_data, c.status_pelaksanaan, c.ket_pelaksanaan, 0
                        FROM trx_anggaran_program AS a
                        INNER JOIN trx_anggaran_urusan AS b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                        INNER JOIN trx_rkpd_final_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd_final
                        WHERE a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                    if($dataPelaksanarkpd == 0){
                         return response ()->json (['pesan'=>'Data Unit Pelaksana Program RKPD Final gagal di-Load','status_pesan'=>'0']);
                    } else {
                        $dataProgpd=DB::INSERT('INSERT INTO trx_anggaran_program_pd (id_pelaksana_anggaran, kd_dokumen_keu, jns_dokumen_keu, id_perubahan, id_dokumen_keu, tahun_anggaran,
                            jenis_belanja, no_urut, id_unit, id_program_pd_rkpd_final, id_program_renstra, uraian_program_renstra, id_program_ref,
                            pagu_rkpd_final, pagu_anggaran, sumber_data, status_pelaksanaan, ket_usulan, status_data)
                            SELECT c.id_pelaksana_anggaran, '.$req->kd_dokumen_keu.', '.$req->jns_dokumen_keu.', '.$req->id_perubahan.', '.$req->id_dokumen_rkpd.', z.tahun_forum, 
                            z.jenis_belanja, z.no_urut, z.id_unit, z.id_program_pd, z.id_program_renstra, z.uraian_program_renstra, z.id_program_ref, z.pagu_forum, z.pagu_forum, 0,
                            0, z.ket_usulan, 0
                            FROM trx_anggaran_program AS a
                            INNER JOIN trx_anggaran_urusan AS b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                            INNER JOIN trx_anggaran_pelaksana AS c ON b.id_urusan_anggaran = c.id_urusan_anggaran
                            INNER JOIN trx_rkpd_final_program_pd AS z ON c.id_pelaksana_rkpd_final = z.id_rkpd_rancangan
                            WHERE  (z.status_pelaksanaan <> 2 AND z.status_pelaksanaan <> 3) AND a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                        if($dataProgpd == 0){
                            return response ()->json (['pesan'=>'Data Program Perangkat Daerah RKPD Final gagal di-Load','status_pesan'=>'0']);
                        } else {
                            $dataInprogpd=DB::INSERT('INSERT INTO trx_anggaran_prog_indikator_pd (tahun_anggaran, no_urut, id_program_pd, id_indikator_rkpd_final,
                                id_program_renstra, id_perubahan, kd_indikator, uraian_indikator_program, tolok_ukur_indikator, target_renstra, target_renja, indikator_output, 
                                id_satuan_output, indikator_input, target_input, id_satuan_input, status_data, sumber_data)    
                                SELECT a.tahun_anggaran, e.no_urut, d.id_program_pd, e.id_indikator_program,e.id_program_renstra, e.id_perubahan, e.kd_indikator, 
                                e.uraian_indikator_program, e.tolok_ukur_indikator, e.target_renja, e.target_renja, e.indikator_output, e.id_satuan_ouput, e.indikator_input, 
                                COALESCE(e.target_input,0), e.id_satuan_input,  0, 0
                                FROM trx_anggaran_program AS a
                                INNER JOIN trx_anggaran_urusan AS b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                                INNER JOIN trx_anggaran_pelaksana AS c ON b.id_urusan_anggaran = c.id_urusan_anggaran
                                INNER JOIN trx_anggaran_program_pd AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                                INNER JOIN trx_rkpd_final_prog_indikator_pd AS e ON d.id_program_pd_rkpd_final = e.id_program_pd
                                WHERE a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                            if($dataInprogpd == 0){
                                return response ()->json (['pesan'=>'Data Indikator Program Perangkat Daerah RKPD Final gagal di-Load','status_pesan'=>'0']);
                            } else {
                                $dataKegpd=DB::INSERT('INSERT INTO trx_anggaran_kegiatan_pd (id_kegiatan_pd_rkpd_final, id_program_pd, id_unit, tahun_anggaran, no_urut, id_renja, 
                                    id_rkpd_renstra, id_program_renstra, id_kegiatan_renstra, id_kegiatan_ref, uraian_kegiatan_forum, pagu_tahun_kegiatan, pagu_kegiatan_renstra, 
                                    pagu_plus1_renja, pagu_plus1_forum, pagu_forum, keterangan_status, status_data, status_pelaksanaan, sumber_data, kelompok_sasaran)  
                                    SELECT e.id_kegiatan_pd, d.id_program_pd, d.id_unit, d.tahun_anggaran, e.no_urut, e.id_renja, e.id_rkpd_renstra,e.id_program_renstra,
                                    e.id_kegiatan_renstra,e.id_kegiatan_ref,e.uraian_kegiatan_forum,e.pagu_forum,e.pagu_kegiatan_renstra,
                                    e.pagu_plus1_forum,e.pagu_plus1_forum,e.pagu_forum,e.keterangan_status,0,e.status_pelaksanaan,0,"" as kelompok_sasaran 
                                    FROM trx_anggaran_program AS a
                                    INNER JOIN trx_anggaran_urusan AS b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                                    INNER JOIN trx_anggaran_pelaksana AS c ON b.id_urusan_anggaran = c.id_urusan_anggaran
                                    INNER JOIN trx_anggaran_program_pd AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                                    INNER JOIN trx_rkpd_final_kegiatan_pd AS e ON d.id_program_pd_rkpd_final = e.id_program_pd
                                    WHERE  (e.status_pelaksanaan <> 2 AND e.status_pelaksanaan <> 3) AND a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                                if($dataKegpd == 0){
                                    return response ()->json (['pesan'=>'Data Kegiatan Perangkat Daerah RKPD Final gagal di-Load','status_pesan'=>'0']);
                                } else {
                                    $dataInkegpd=DB::INSERT('INSERT INTO trx_anggaran_keg_indikator_pd (tahun_anggaran, no_urut, id_kegiatan_pd, id_indikator_rkpd_final, 
                                        id_perubahan,kd_indikator, uraian_indikator_kegiatan, tolok_ukur_indikator, target_renstra, 
                                        target_renja, indikator_output, id_satuan_output, indikator_input,target_input, id_satuan_input, status_data, sumber_data)  
                                        SELECT e.tahun_anggaran, f.no_urut, e.id_kegiatan_pd, f.id_program_renstra, 0,
                                        f.kd_indikator, f.uraian_indikator_kegiatan, f.tolok_ukur_indikator, f.target_renja, f.target_renja, 
                                        f.indikator_output, f.id_satuan_ouput, f.indikator_input,f.target_input, f.id_satuan_input, 0, 0
                                        FROM trx_anggaran_program AS a
                                        INNER JOIN trx_anggaran_urusan AS b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                                        INNER JOIN trx_anggaran_pelaksana AS c ON b.id_urusan_anggaran = c.id_urusan_anggaran
                                        INNER JOIN trx_anggaran_program_pd AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                                        INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                        INNER JOIN trx_rkpd_final_keg_indikator_pd AS f ON e.id_kegiatan_pd_rkpd_final = f.id_kegiatan_pd
                                        WHERE a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                                    if($dataInkegpd == 0){
                                        return response ()->json (['pesan'=>'Data Indikator Kegiatan Perangkat Daerah RKPD Final gagal di-Load','status_pesan'=>'0']);
                                    } else {
                                        $dataPelSubUnit=DB::INSERT('INSERT INTO trx_anggaran_pelaksana_pd (tahun_anggaran, no_urut, id_kegiatan_pd, id_pelaksana_rkpd_final, id_sub_unit, 
                                            id_pelaksana_renja, id_lokasi, sumber_data, ket_pelaksana, status_pelaksanaan, status_data) 
                                            SELECT e.tahun_anggaran, f.no_urut, e.id_kegiatan_pd, f.id_pelaksana_pd ,f.id_sub_unit, f.id_pelaksana_renja, f.id_lokasi, 0, f.ket_pelaksana, 0, 0
                                            FROM trx_anggaran_program AS a
                                            INNER JOIN trx_anggaran_urusan AS b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                                            INNER JOIN trx_anggaran_pelaksana AS c ON b.id_urusan_anggaran = c.id_urusan_anggaran
                                            INNER JOIN trx_anggaran_program_pd AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                                            INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                            INNER JOIN trx_rkpd_final_pelaksana_pd AS f ON e.id_kegiatan_pd_rkpd_final = f.id_kegiatan_pd
                                            WHERE f.status_pelaksanaan <> 1 AND a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                                        if($dataPelSubUnit == 0){
                                            return response ()->json (['pesan'=>'Data Sub Unit Pelaksana Kegiatan RKPD Final gagal di-Load','status_pesan'=>'0']);
                                        } else {
                                            $dataAktivitas=DB::INSERT('INSERT INTO trx_anggaran_aktivitas_pd 
                                                (id_aktivitas_rkpd_final,id_pelaksana_pd, tahun_anggaran, no_urut, sumber_aktivitas, sumber_dana, id_perubahan, id_aktivitas_asb,
                                                id_program_nasional, id_program_provinsi, uraian_aktivitas_kegiatan, volume_aktivitas_1, volume_rkpd_1, id_satuan_1, volume_aktivitas_2, 
                                                volume_rkpd_2, id_satuan_2, jenis_kegiatan, pagu_rkpd, pagu_anggaran, status_data, status_pelaksanaan, keterangan_aktivitas,
                                                group_keu, sumber_data, id_satuan_publik)
                                                SELECT g.id_aktivitas_pd,f.id_pelaksana_pd, f.tahun_anggaran, g.no_urut, g.sumber_aktivitas, g.sumber_dana, 0 as id_perubahan, g.id_aktivitas_asb, 
                                                g.id_program_nasional, g.id_program_provinsi, g.uraian_aktivitas_kegiatan, g.volume_forum_1, g.volume_forum_1, g.id_satuan_1, g.volume_forum_2, 
                                                g.volume_forum_2, g.id_satuan_2, g.jenis_kegiatan, g.pagu_aktivitas_forum, g.pagu_aktivitas_forum, 0 as status_data, g.status_pelaksanaan, g.keterangan_aktivitas, 0 as group_keu,0 as sumber_data, g.id_satuan_publik 
                                                FROM trx_anggaran_program AS a
                                                INNER JOIN trx_anggaran_urusan AS b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                                                INNER JOIN trx_anggaran_pelaksana AS c ON b.id_urusan_anggaran = c.id_urusan_anggaran
                                                INNER JOIN trx_anggaran_program_pd AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                                                INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                                INNER JOIN trx_anggaran_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                                                INNER JOIN trx_rkpd_final_aktivitas_pd AS g ON f.id_pelaksana_rkpd_final = g.id_pelaksana_pd
                                                WHERE  g.status_pelaksanaan <> 1 AND a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                                            if($dataAktivitas == 0){
                                                return response ()->json (['pesan'=>'Data Aktivitas Perangkat Daerah Kegiatan RKPD Final gagal di-Load','status_pesan'=>'0']);
                                            } else {
                                                $dataLokasi=DB::INSERT('INSERT INTO trx_anggaran_lokasi_pd (id_lokasi_rkpd_final, id_aktivitas_pd, tahun_anggaran, no_urut, jenis_lokasi, 
                                                        id_lokasi, id_lokasi_teknis, volume_1, volume_usulan_1, volume_2, volume_usulan_2, id_satuan_1, id_satuan_2, id_desa, id_kecamatan, 
                                                        rt, rw, uraian_lokasi, lat, lang, status_data, status_pelaksanaan, ket_lokasi, sumber_data)
                                                        SELECT h.id_lokasi_pd,  g.id_aktivitas_pd, g.tahun_anggaran, h.no_urut, h.jenis_lokasi, h.id_lokasi, h.id_lokasi_teknis, h.volume_1, 
                                                        h.volume_1, h.volume_2, h.volume_2, h.id_satuan_1, h.id_satuan_2, h.id_desa, h.id_kecamatan, h.rt, h.rw, h.uraian_lokasi, 
                                                        h.lat, h.lang, 0, 0, h.ket_lokasi,0    
                                                        FROM trx_anggaran_program AS a
                                                        INNER JOIN trx_anggaran_urusan AS b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                                                        INNER JOIN trx_anggaran_pelaksana AS c ON b.id_urusan_anggaran = c.id_urusan_anggaran
                                                        INNER JOIN trx_anggaran_program_pd AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                                                        INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                                        INNER JOIN trx_anggaran_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                                                        INNER JOIN trx_anggaran_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd
                                                        INNER JOIN trx_rkpd_final_lokasi_pd AS h ON g.id_aktivitas_rkpd_final = h.id_aktivitas_pd
                                                        WHERE h.status_pelaksanaan NOT IN (2,3,4)  AND a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                                                if($dataLokasi == 0){
                                                    return response ()->json (['pesan'=>'Data Lokasi Aktivitas Kegiatan RKPD Final gagal di-Load','status_pesan'=>'0']);
                                                } else {
                                                    $dataBelanja=DB::INSERT('INSERT INTO trx_anggaran_belanja_pd (id_aktivitas_pd, id_belanja_rkpd_final, tahun_anggaran, no_urut, id_zona_ssh, 
                                                            sumber_belanja, id_aktivitas_asb, id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2,
                                                            koefisien, harga_satuan, jml_belanja, volume_1_rkpd, volume_2_rkpd, koefisien_rkpd, harga_satuan_rkpd, jml_belanja_rkpd, 
                                                            status_data, sumber_data)
                                                            SELECT g.id_aktivitas_pd, h.id_belanja_forum, g.tahun_anggaran, h.no_urut, h.id_zona_ssh, h.sumber_belanja, h.id_aktivitas_asb, 
                                                            h.id_item_ssh, h.id_rekening_ssh, h.uraian_belanja, h.volume_1_forum, h.id_satuan_1_forum, h.volume_2_forum, h.id_satuan_2_forum,1, h.harga_satuan_forum, h.jml_belanja_forum, h.volume_1_forum, h.volume_2_forum, 1, h.harga_satuan_forum, h.jml_belanja_forum, 0, 0
                                                            FROM trx_anggaran_program AS a
                                                            INNER JOIN trx_anggaran_urusan AS b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                                                            INNER JOIN trx_anggaran_pelaksana AS c ON b.id_urusan_anggaran = c.id_urusan_anggaran
                                                            INNER JOIN trx_anggaran_program_pd AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                                                            INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                                            INNER JOIN trx_anggaran_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                                                            INNER JOIN trx_anggaran_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd
                                                            INNER JOIN trx_rkpd_final_belanja_pd AS h ON g.id_aktivitas_rkpd_final = h.id_aktivitas_pd
                                                            WHERE a.id_dokumen_keu='.$req->id_dokumen_rkpd);
                                                    if($dataBelanja == 0){
                                                        return response ()->json (['pesan'=>'Data Rincian Belanja RKPD Final gagal di-Load','status_pesan'=>'0']);
                                                    } else {
                                                        return response ()->json (['pesan'=>'Data PPAS Berhasil di-Load','status_pesan'=>'1']);
                                                    }
                                                } 
                                            } 
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function getDataPerencana()
    {
        $dataPerencana = DB::SELECT('SELECT a.kd_kab,a.id_pemda,a.prefix_pemda,a.nm_prov,a.nm_kabkota,a.ibu_kota,a.nama_jabatan_kepala_daerah,
            a.nama_kepala_daerah,a.nama_jabatan_sekretariat_daerah,a.nama_sekretariat_daerah,a.nip_sekretariat_daerah,a.unit_perencanaan,a.nama_kepala_bappeda,
            a.nip_kepala_bappeda,a.unit_keuangan,a.nama_kepala_bpkad,a.nip_kepala_bpkad,b.nm_unit
            FROM ref_pemda AS a
            LEFT OUTER JOIN ref_unit AS b ON a.unit_keuangan = b.id_unit LIMIT 1');
        return json_encode($dataPerencana);
    }

    public function getDataDokReferensi(Request $req)
    {
        $dataDokumen = DB::SELECT('SELECT id_dokumen_rkpd, nomor_rkpd, tanggal_rkpd, uraian_perkada, flag, CONCAT(nomor_rkpd," (Tahun Anggaran:",tahun_rkpd,")") as nomor_display 
            FROM trx_rkpd_final_dokumen WHERE tahun_rkpd = '.$req->tahun.' AND flag=1');
        return json_encode($dataDokumen);
    }

    public function addDokumen(Request $req)
    {
        try{
            $data = new TrxAnggaranDokumen;
            $data->jns_dokumen_keu = 0;
            $data->kd_dokumen_keu = 0;
            $data->id_perubahan = 0;
            $data->id_dokumen_ref = $req->id_dokumen_ref;
            $data->tahun_anggaran = Session::get('tahun');
            $data->nomor_keu = $req->nomor_keu;
            $data->tanggal_keu = $req->tanggal_keu;
            $data->uraian_perkada = $req->uraian_perkada;
            $data->id_unit_ppkd = $req->id_unit_ppkd;
            $data->jabatan_tandatangan = "Kepala";
            $data->nama_tandatangan = $req->nama_tandatangan;
            $data->nip_tandatangan = $req->nip_tandatangan;
            $data->flag = 0;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

    public function editDokumen(Request $req)
    {
        try{
            $data = TrxAnggaranDokumen::find($req->id_dokumen_keu);
            $data->id_dokumen_ref = $req->id_dokumen_ref;
            $data->tahun_anggaran = Session::get('tahun');
            $data->nomor_keu = $req->nomor_keu;
            $data->tanggal_keu = $req->tanggal_keu;
            $data->uraian_perkada = $req->uraian_perkada;
            $data->id_unit_ppkd = $req->id_unit_ppkd;
            $data->jabatan_tandatangan = "Kepala";
            $data->nama_tandatangan = $req->nama_tandatangan;
            $data->nip_tandatangan = $req->nip_tandatangan;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

    public function hapusDokumen(Request $req)
    {
        $result = TrxAnggaranDokumen::destroy($req->id_dokumen_keu);    
        if($result != 0){
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
        }
    }

    public function unLoadData(Request $req)
    {
        $result = DB::SELECT('SELECT id_rkpd_rancangan FROM trx_rkpd_final WHERE status_data=0 AND id_rkpd_rancangan = '.$req->id_rkpd_rancangan);

        if($result != Null){
            try{
                $result=DB::DELETE('DELETE FROM trx_rkpd_final WHERE id_rkpd_rancangan = '.$req->id_rkpd_rancangan);
                return response ()->json (['pesan'=>'Data Program Berhasil Unload','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Program Gagal Unload ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Program Gagal Unload, Lihat Status Program','status_pesan'=>'0']);
        }
    }

    public function postDokumen(Request $req)
    {
        $cek = DB::SELECT('SELECT x.tahun_rkpd, COALESCE(COUNT(x.id_rkpd_rancangan),0) AS jml, 
            COALESCE((SELECT COUNT(a.id_rkpd_rancangan) AS jml FROM trx_rkpd_final AS a 
            LEFT OUTER JOIN trx_rkpd_final AS b ON a.tahun_rkpd = b.tahun_rkpd AND a.id_rkpd_rancangan = b.id_rkpd_rancangan
            WHERE b.id_rkpd_rancangan is null AND a.status_data = 2 AND (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3) 
            AND a.tahun_rkpd = x.tahun_rkpd GROUP BY a.tahun_rkpd, a.status_data),0) AS jml_load 
            FROM trx_rkpd_final AS x 
            WHERE x.status_data = 2 AND (x.status_pelaksanaan <> 2 AND x.status_pelaksanaan <> 3) AND x.tahun_rkpd ='.$req->tahun_rkpd.'
            GROUP BY x.tahun_rkpd, x.status_data');

        if($cek != null && ($cek[0]->jml - $cek[0]->jml_load) <> $cek[0]->jml ){
            $data = DB::UPDATE('UPDATE trx_rkpd_final_dokumen SET flag ='.$req->flag.' WHERE tahun_rkpd='.$req->tahun_rkpd.' AND id_dokumen_rkpd='.$req->id_dokumen_rkpd);       
            if($data != 0){
                $dataProg=DB::UPDATE('UPDATE trx_rkpd_final SET status_data ='.$req->status.', id_dokumen='.$req->id_dokumen_rkpd.' WHERE tahun_rkpd='.$req->tahun_rkpd.' AND status_data='.$req->status_awal);
                if($dataProg != 0){
                    return response ()->json (['pesan'=>'Data Berhasil Posting','status_pesan'=>'1']);
                } else {
                    return response ()->json (['pesan'=>'Data Gagal Diposting (1cprPD)','status_pesan'=>'0']);
                }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Diposting (0cdrPD)','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Gagal Proses, Dokumen sudah dipakai pada tahap selanjutnya (0cdrPD)','status_pesan'=>'0']);
        }
    }
}
