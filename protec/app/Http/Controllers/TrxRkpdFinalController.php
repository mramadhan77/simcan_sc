<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\CekAkses;
use App\Http\Controllers\SettingController;
use Auth;
use App\Models\Can\TrxRkpdFinalDokumen;
use App\Models\Can\TrxRkpdFinal;
use App\Models\Can\TrxRkpdFinalIndikator;
use App\Models\Can\TrxRkpdFinalUrusan;
use App\Models\Can\TrxRkpdFinalPelaksana;
use App\Models\Can\TrxRkpdFinalProgramPd;
use App\Models\Can\TrxRkpdFinalKegiatanPd;
use App\Models\Can\TrxRkpdFinalPelaksanaPd;
use App\Models\Can\TrxRkpdFinalAktivitasPd;
use App\Models\Can\TrxRkpdFinalLokasiPd;
use App\Models\Can\TrxRkpdFinalBelanjaPd;



class TrxRkpdFinalController extends Controller
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
                FROM trx_rkpd_final_dokumen AS a
                INNER JOIN ref_unit AS b ON a.id_unit_perencana = b.id_unit,
                (SELECT @id:=0) z');

        return DataTables::of($getDataDokumen)
        ->addColumn('action', function ($getDataDokumen) {
            if ($getDataDokumen->flag==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnEditDokumen" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-warning"></i> Ubah Dokumen RKPD</a>
                        </li>
                        <li>
                            <a id="btnPostingRkpd" class="dropdown-item"><i class="fa fa-check fa-fw fa-lg text-success"></i> Posting Dokumen RKPD</a>
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
                            <a id="btnEditDokumen" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-warning"></i> Ubah Dokumen RKPD</a>
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

public function getDataRekap(){
        $data = DB::SELECT('SELECT (@id:=@id+1) as no_urut, x.id_rkpd_rancangan, x.tahun_rkpd, 
            x.uraian_program_rpjmd, x.pagu_ranwal, x.status_data, x.sumber_data, 
            (SELECT COUNT(a.id_unit) FROM trx_rkpd_final_pelaksana a
                INNER JOIN  trx_rkpd_final_urusan b ON a.id_urusan_rkpd = b.id_urusan_rkpd
                WHERE b.id_rkpd_rancangan = x.id_rkpd_rancangan GROUP BY b.id_rkpd_rancangan) AS jml_unit,
            (SELECT COUNT(d.id_program_pd) FROM trx_rkpd_final_program_pd d
                INNER JOIN trx_rkpd_final_pelaksana c ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                INNER JOIN trx_rkpd_final_urusan b ON c.id_urusan_rkpd = b.id_urusan_rkpd
                WHERE b.id_rkpd_rancangan = x.id_rkpd_rancangan GROUP BY b.id_rkpd_rancangan) AS jml_prog_renja,
            (SELECT SUM(d.pagu_forum) FROM trx_rkpd_final_program_pd d
                INNER JOIN trx_rkpd_final_pelaksana c ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                INNER JOIN trx_rkpd_final_urusan b ON c.id_urusan_rkpd = b.id_urusan_rkpd
                WHERE b.id_rkpd_rancangan = x.id_rkpd_rancangan GROUP BY b.id_rkpd_rancangan) AS pagu_prog_renja,
            (SELECT COUNT(e.id_kegiatan_pd) FROM trx_rkpd_final_kegiatan_pd AS e 
                INNER JOIN trx_rkpd_final_program_pd d ON d.id_program_pd = e.id_program_pd
                INNER JOIN trx_rkpd_final_pelaksana c ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                INNER JOIN trx_rkpd_final_urusan b ON c.id_urusan_rkpd = b.id_urusan_rkpd
                WHERE b.id_rkpd_rancangan = x.id_rkpd_rancangan GROUP BY b.id_rkpd_rancangan)  AS jml_kegiatan,
            (SELECT SUM(e.pagu_forum) FROM trx_rkpd_final_kegiatan_pd AS e 
                INNER JOIN trx_rkpd_final_program_pd d ON d.id_program_pd = e.id_program_pd
                INNER JOIN trx_rkpd_final_pelaksana c ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                INNER JOIN trx_rkpd_final_urusan b ON c.id_urusan_rkpd = b.id_urusan_rkpd
                WHERE b.id_rkpd_rancangan = x.id_rkpd_rancangan GROUP BY b.id_rkpd_rancangan)  AS pagu_kegiatan,
            (SELECT COUNT(g.id_aktivitas_pd) FROM trx_rkpd_final_aktivitas_pd AS g
                INNER JOIN trx_rkpd_final_pelaksana_pd AS f ON f.id_pelaksana_pd = g.id_pelaksana_pd
                INNER JOIN trx_rkpd_final_kegiatan_pd AS e ON e.id_kegiatan_pd = f.id_kegiatan_pd
                INNER JOIN trx_rkpd_final_program_pd d ON d.id_program_pd = e.id_program_pd
                INNER JOIN trx_rkpd_final_pelaksana c ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                INNER JOIN trx_rkpd_final_urusan b ON c.id_urusan_rkpd = b.id_urusan_rkpd
                WHERE b.id_rkpd_rancangan = x.id_rkpd_rancangan GROUP BY b.id_rkpd_rancangan)  AS jml_aktivitas, 
            (SELECT SUM(g.pagu_aktivitas_forum) FROM trx_rkpd_final_aktivitas_pd AS g
                INNER JOIN trx_rkpd_final_pelaksana_pd AS f ON f.id_pelaksana_pd = g.id_pelaksana_pd
                INNER JOIN trx_rkpd_final_kegiatan_pd AS e ON e.id_kegiatan_pd = f.id_kegiatan_pd
                INNER JOIN trx_rkpd_final_program_pd d ON d.id_program_pd = e.id_program_pd
                INNER JOIN trx_rkpd_final_pelaksana c ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                INNER JOIN trx_rkpd_final_urusan b ON c.id_urusan_rkpd = b.id_urusan_rkpd
                WHERE b.id_rkpd_rancangan = x.id_rkpd_rancangan GROUP BY b.id_rkpd_rancangan)  AS pagu_aktivitas
            FROM trx_rkpd_final AS x,
                (SELECT @id:=0) z
                WHERE x.tahun_rkpd='.Session::get('tahun'));

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


    public function loadData()
    {
        // if(Auth::check()){  
            if(Session::has('tahun')){           
                return view('rkpd.load'); 
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
                return view('rkpd.index');  
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
                return view('rkpd.blangsung');  
            } else {
                return redirect('home');
            }
            
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function doku(Request $request, Builder $htmlBuilder)
    {
        // if(Auth::check()){
            if(Session::has('tahun')){
                return view('rkpd.doku');  
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
        FROM trx_rkpd_ranhir  AS a 
        LEFT OUTER JOIN trx_rkpd_final AS b ON a.tahun_rkpd = b.tahun_rkpd AND a.id_rkpd_rancangan = b.id_forum_rkpdprog,
                (SELECT @id:=0) z
        WHERE b.id_forum_rkpdprog is null AND a.status_data = 2 AND (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3) AND a.tahun_rkpd ='.$tahun_rkpd);

        return DataTables::of($getSelect)
        ->addColumn('action',function($getSelect){
          return '
              <button id="btnReLoad" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="" title="" class="btn btn-primary">
              <i class="fa fa-download fa-fw fa-lg"></i> Load Data</button>
          ' ;})
        ->make(true);
    }

    public function importData(Request $req){
        $dataProgrkpd=DB::INSERT('INSERT INTO trx_rkpd_final (id_rkpd_ranwal,id_forum_rkpdprog, no_urut, tahun_rkpd, jenis_belanja, id_rkpd_rpjmd, thn_id_rpjmd, id_visi_rpjmd, 
            id_misi_rpjmd, id_tujuan_rpjmd, id_sasaran_rpjmd, id_program_rpjmd, uraian_program_rpjmd, pagu_rpjmd, pagu_ranwal, keterangan_program, ket_usulan, 
            status_pelaksanaan, status_data, sumber_data, id_dokumen)
            SELECT DISTINCT a.id_rkpd_ranwal,a.id_rkpd_rancangan, a.no_urut, a.tahun_rkpd, a.jenis_belanja, a.id_rkpd_rpjmd, a.thn_id_rpjmd,
            a.id_visi_rpjmd, a.id_misi_rpjmd, a.id_tujuan_rpjmd, a.id_sasaran_rpjmd, a.id_program_rpjmd, a.uraian_program_rpjmd,
            a.pagu_rpjmd, a.pagu_ranwal, a.keterangan_program, a.ket_usulan, 0, 0, 0, 0
            FROM trx_rkpd_ranhir AS a WHERE (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3)  AND a.id_rkpd_rancangan='.$req->id_rkpd_ranwal);
        if($dataProgrkpd == 0){
            return response ()->json (['pesan'=>'Data Program Rancangan RKPD gagal di-Load','status_pesan'=>'0']);
        } else {
            $dataIndikatorrkpd=DB::INSERT('INSERT INTO trx_rkpd_final_indikator (tahun_rkpd, no_urut, id_rkpd_rancangan, id_indikator_program_rkpd, id_perubahan, kd_indikator, uraian_indikator_program_rkpd, tolok_ukur_indikator, target_rpjmd, target_rkpd, indikator_input, target_input, id_satuan_input, indikator_output, id_satuan_ouput, status_data, sumber_data)
                SELECT DISTINCT  a.tahun_rkpd, b.no_urut, a.id_rkpd_rancangan, b.id_indikator_program_rkpd, b.id_perubahan, b.kd_indikator, b.uraian_indikator_program_rkpd, 
                b.tolok_ukur_indikator, b.target_rpjmd, b.target_rkpd, b.indikator_input, COALESCE(b.target_input,0), b.id_satuan_input, b.indikator_output, b.id_satuan_ouput, 
                0, 0 FROM trx_rkpd_ranhir_indikator AS b 
                INNER JOIN trx_rkpd_final AS a ON a.id_forum_rkpdprog = b.id_rkpd_rancangan 
                WHERE a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
            if($dataIndikatorrkpd == 0){
                return response ()->json (['pesan'=>'Data Indikator Program Rancangan RKPD gagal di-Load','status_pesan'=>'0']);
            } else {
                $dataUrusanrkpd=DB::INSERT('INSERT INTO trx_rkpd_final_urusan (tahun_rkpd, no_urut, id_rkpd_rancangan, id_urusan_rkpd, id_bidang, sumber_data)
                    SELECT DISTINCT a.tahun_rkpd, b.no_urut, a.id_rkpd_rancangan, b.id_urusan_rkpd, b.id_bidang, 0 FROM trx_rkpd_final AS a
                    INNER JOIN trx_rkpd_ranhir_urusan AS b ON a.id_forum_rkpdprog = b.id_rkpd_rancangan WHERE a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                if($dataUrusanrkpd == 0){
                    return response ()->json (['pesan'=>'Data Urusan Program Rancangan RKPD gagal di-Load','status_pesan'=>'0']);
                } else {
                    $dataPelaksanarkpd=DB::INSERT('INSERT INTO trx_rkpd_final_pelaksana (tahun_rkpd, no_urut, id_rkpd_rancangan, id_pelaksana_rpjmd, 
                    id_urusan_rkpd, id_unit, pagu_rpjmd, pagu_rkpd, hak_akses, sumber_data, status_pelaksanaan, ket_pelaksanaan, status_data)
                        SELECT DISTINCT a.tahun_rkpd, c.no_urut, a.id_rkpd_rancangan, c.id_pelaksana_rkpd, c.id_urusan_rkpd, c.id_unit, c.pagu_rpjmd, c.pagu_rkpd, c.hak_akses, c.sumber_data, c.status_pelaksanaan, c.ket_pelaksanaan, 0
                        FROM trx_rkpd_final AS a
                        INNER JOIN trx_rkpd_ranhir_urusan AS b ON a.id_forum_rkpdprog = b.id_rkpd_rancangan
                        INNER JOIN trx_rkpd_ranhir_pelaksana AS c ON c.id_urusan_rkpd = b.id_urusan_rkpd
                        WHERE a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                    if($dataPelaksanarkpd == 0){
                         return response ()->json (['pesan'=>'Data Unit Pelaksana Program Rancangan RKPD gagal di-Load','status_pesan'=>'0']);
                    } else {
                        $dataProgpd=DB::INSERT('INSERT INTO trx_rkpd_final_program_pd (id_rkpd_rancangan, tahun_forum, jenis_belanja, no_urut, id_unit, id_forum_program, id_renja_program, id_program_renstra, uraian_program_renstra, id_program_ref, pagu_tahun_renstra, pagu_forum, sumber_data, status_pelaksanaan, ket_usulan, status_data, id_dokumen)
                            SELECT c.id_pelaksana_rkpd, z.tahun_forum, z.jenis_belanja, z.no_urut, z.id_unit, z.id_program_pd, z.id_rkpd_rancangan, 
                            z.id_program_renstra, z.uraian_program_renstra, z.id_program_ref, z.pagu_forum, z.pagu_forum, 0,
                            0, z.ket_usulan, 0, 0
                            FROM trx_rkpd_final AS a
                            INNER JOIN trx_rkpd_final_urusan AS b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
                            INNER JOIN trx_rkpd_final_pelaksana AS c ON b.id_urusan_rkpd = c.id_urusan_rkpd
                            INNER JOIN trx_rkpd_ranhir_program_pd AS z ON c.id_pelaksana_rpjmd = z.id_rkpd_rancangan
                            WHERE  (z.status_pelaksanaan <> 2 AND z.status_pelaksanaan <> 3) AND a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                        if($dataProgpd == 0){
                            return response ()->json (['pesan'=>'Data Program Perangkat Daerah gagal di-Load','status_pesan'=>'0']);
                        } else {
                            $dataInprogpd=DB::INSERT('INSERT INTO trx_rkpd_final_prog_indikator_pd (tahun_renja, no_urut, id_program_pd, id_program_forum,
                            id_program_renstra, id_perubahan, kd_indikator, uraian_indikator_program, tolok_ukur_indikator, 
                            target_renstra, target_renja, indikator_output, id_satuan_ouput, indikator_input, target_input, id_satuan_input, status_data, sumber_data)    
                                SELECT e.tahun_renja, e.no_urut, d.id_program_pd,e.id_indikator_program,e.id_program_renstra, e.id_perubahan, e.kd_indikator, 
                                e.uraian_indikator_program, e.tolok_ukur_indikator, e.target_renja, e.target_renja, e.indikator_output,  e.id_satuan_ouput, e.indikator_input, 
                                COALESCE(e.target_input,0), e.id_satuan_input,  0, 0
                                FROM trx_rkpd_final AS a
                                INNER JOIN trx_rkpd_final_urusan AS b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
                                INNER JOIN trx_rkpd_final_pelaksana AS c ON b.id_urusan_rkpd = c.id_urusan_rkpd
                                INNER JOIN trx_rkpd_final_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                                INNER JOIN trx_rkpd_ranhir_prog_indikator_pd AS e ON d.id_forum_program = e.id_program_pd
                                WHERE a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                            if($dataInprogpd == 0){
                                return response ()->json (['pesan'=>'Data Indikator Program Perangkat Daerah gagal di-Load','status_pesan'=>'0']);
                            } else {
                                $dataKegpd=DB::INSERT('INSERT INTO trx_rkpd_final_kegiatan_pd (id_forum_skpd, id_program_pd, id_unit, tahun_forum, 
                                no_urut, id_renja, id_rkpd_renstra, id_program_renstra, 
                                    id_kegiatan_renstra, id_kegiatan_ref, uraian_kegiatan_forum, pagu_tahun_kegiatan, pagu_kegiatan_renstra, pagu_plus1_renja, 
                                    pagu_plus1_forum, pagu_forum, keterangan_status, status_data, status_pelaksanaan, sumber_data)  
                                    SELECT e.id_kegiatan_pd, d.id_program_pd, d.id_unit, d.tahun_forum, e.no_urut, e.id_renja, e.id_rkpd_renstra,e.id_program_renstra,
                                    e.id_kegiatan_renstra,e.id_kegiatan_ref,e.uraian_kegiatan_forum,e.pagu_forum,e.pagu_kegiatan_renstra,
                                    e.pagu_plus1_renja,e.pagu_plus1_forum,e.pagu_forum,e.keterangan_status,0,e.status_pelaksanaan,0 
                                    FROM trx_rkpd_final AS a
                                    INNER JOIN trx_rkpd_final_urusan AS b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
                                    INNER JOIN trx_rkpd_final_pelaksana AS c ON b.id_urusan_rkpd = c.id_urusan_rkpd
                                    INNER JOIN trx_rkpd_final_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                                    INNER JOIN trx_rkpd_ranhir_kegiatan_pd AS e ON d.id_forum_program = e.id_program_pd
                                    WHERE  (e.status_pelaksanaan <> 2 AND e.status_pelaksanaan <> 3) AND a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                                if($dataKegpd == 0){
                                    return response ()->json (['pesan'=>'Data Kegiatan Perangkat Daerah RKPD gagal di-Load','status_pesan'=>'0']);
                                } else {
                                    $dataInkegpd=DB::INSERT('INSERT INTO trx_rkpd_final_keg_indikator_pd (tahun_renja, no_urut, id_kegiatan_pd, id_program_renstra, id_perubahan,kd_indikator, 
                                        uraian_indikator_kegiatan, tolok_ukur_indikator, target_renstra, 
                                        target_renja, indikator_output, id_satuan_ouput, indikator_input,target_input, id_satuan_input, 
                                        status_data, sumber_data)  
                                        SELECT e.tahun_forum, f.no_urut, e.id_kegiatan_pd, f.id_program_renstra, 0,
                                        f.kd_indikator, f.uraian_indikator_kegiatan, f.tolok_ukur_indikator, f.target_renja, f.target_renja, 
                                        f.indikator_output, f.id_satuan_ouput, f.indikator_input,f.target_input, f.id_satuan_input, 0, 0
                                        FROM trx_rkpd_final AS a
                                        INNER JOIN trx_rkpd_final_urusan AS b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
                                        INNER JOIN trx_rkpd_final_pelaksana AS c ON b.id_urusan_rkpd = c.id_urusan_rkpd
                                        INNER JOIN trx_rkpd_final_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                                        INNER JOIN trx_rkpd_final_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                        INNER JOIN trx_rkpd_ranhir_keg_indikator_pd AS f ON e.id_forum_skpd = f.id_kegiatan_pd
                                        WHERE a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                                    if($dataInkegpd == 0){
                                        return response ()->json (['pesan'=>'Data Indikator Kegiatan Perangkat Daerah gagal di-Load','status_pesan'=>'0']);
                                    } else {
                                        $dataPelSubUnit=DB::INSERT('INSERT INTO trx_rkpd_final_pelaksana_pd (tahun_forum, no_urut, id_kegiatan_pd, id_pelaksana_forum, id_sub_unit, id_pelaksana_renja, 
                                            id_lokasi, sumber_data, ket_pelaksana, status_pelaksanaan, status_data) 
                                            SELECT e.tahun_forum, f.no_urut, e.id_kegiatan_pd, f.id_pelaksana_pd ,f.id_sub_unit, f.id_pelaksana_renja, f.id_lokasi, 0, f.ket_pelaksana, 0, 0
                                            FROM trx_rkpd_final AS a
                                            INNER JOIN trx_rkpd_final_urusan AS b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
                                            INNER JOIN trx_rkpd_final_pelaksana AS c ON b.id_urusan_rkpd = c.id_urusan_rkpd
                                            INNER JOIN trx_rkpd_final_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                                            INNER JOIN trx_rkpd_final_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                            INNER JOIN trx_rkpd_ranhir_pelaksana_pd AS f ON e.id_forum_skpd = f.id_kegiatan_pd
                                            WHERE f.status_pelaksanaan <> 1 AND a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                                        if($dataPelSubUnit == 0){
                                            return response ()->json (['pesan'=>'Data Sub Unit Pelaksana Kegiatan gagal di-Load','status_pesan'=>'0']);
                                        } else {
                                            $dataAktivitas=DB::INSERT('INSERT INTO trx_rkpd_final_aktivitas_pd 
                                                (id_aktivitas_forum,id_pelaksana_pd, tahun_forum, no_urut, sumber_aktivitas, id_aktivitas_asb,
                                                id_aktivitas_renja, uraian_aktivitas_kegiatan, volume_aktivitas_1, volume_forum_1, id_satuan_1, volume_aktivitas_2, volume_forum_2, id_satuan_2,
                                                id_program_nasional, id_program_provinsi, jenis_kegiatan, sumber_dana, pagu_aktivitas_renja, pagu_aktivitas_forum, pagu_musren, status_data,
                                                status_pelaksanaan, keterangan_aktivitas, status_musren, sumber_data, id_satuan_publik)
                                                SELECT g.id_aktivitas_pd,f.id_pelaksana_pd, f.tahun_forum, g.no_urut, g.sumber_aktivitas, g.id_aktivitas_asb, 
                                                g.id_aktivitas_renja, g.uraian_aktivitas_kegiatan,g.volume_forum_1, g.volume_forum_1, g.id_satuan_1, g.volume_forum_2, 
                                                g.volume_forum_2, g.id_satuan_2, g.id_program_nasional, g.id_program_provinsi,g.jenis_kegiatan, g.sumber_dana, g.pagu_aktivitas_forum, 
                                                g.pagu_aktivitas_forum, g.pagu_musren, 0, g.status_pelaksanaan, g.keterangan_aktivitas, g.status_musren, 0, g.id_satuan_publik 
                                                FROM trx_rkpd_final AS a
                                                INNER JOIN trx_rkpd_final_urusan AS b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
                                                INNER JOIN trx_rkpd_final_pelaksana AS c ON b.id_urusan_rkpd = c.id_urusan_rkpd
                                                INNER JOIN trx_rkpd_final_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                                                INNER JOIN trx_rkpd_final_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                                INNER JOIN trx_rkpd_final_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                                                INNER JOIN trx_rkpd_ranhir_aktivitas_pd AS g ON f.id_pelaksana_forum = g.id_pelaksana_pd
                                                    WHERE  g.status_pelaksanaan <> 1 AND a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                                            if($dataAktivitas == 0){
                                                return response ()->json (['pesan'=>'Data Aktivitas Perangkat Daerah Kegiatan gagal di-Load','status_pesan'=>'0']);
                                            } else {
                                                $dataLokasi=DB::INSERT('INSERT INTO trx_rkpd_final_lokasi_pd (id_lokasi_forum, tahun_forum, no_urut, id_aktivitas_pd, id_lokasi, id_lokasi_renja, id_lokasi_teknis, 
                                                        jenis_lokasi, volume_1, volume_usulan_1, volume_2, volume_usulan_2, id_satuan_1, id_satuan_2, id_desa, id_kecamatan, rt, rw, uraian_lokasi, lat, 
                                                        lang, status_data, status_pelaksanaan, ket_lokasi, sumber_data)
                                                        SELECT h.id_lokasi_pd, g.tahun_forum, h.no_urut, g.id_aktivitas_pd, h.id_lokasi, h.id_lokasi_renja, h.id_lokasi_teknis, h.jenis_lokasi, h.volume_1, 
                                                        h.volume_1, h.volume_2, h.volume_2, h.id_satuan_1, h.id_satuan_2, h.id_desa, h.id_kecamatan, h.rt, h.rw, h.uraian_lokasi, 
                                                        h.lat, h.lang, 0, 0, h.ket_lokasi,0    
                                                        FROM trx_rkpd_final AS a
                                                        INNER JOIN trx_rkpd_final_urusan AS b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
                                                        INNER JOIN trx_rkpd_final_pelaksana AS c ON b.id_urusan_rkpd = c.id_urusan_rkpd
                                                        INNER JOIN trx_rkpd_final_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                                                        INNER JOIN trx_rkpd_final_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                                        INNER JOIN trx_rkpd_final_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                                                        INNER JOIN trx_rkpd_final_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd
                                                        INNER JOIN trx_rkpd_ranhir_lokasi_pd AS h ON g.id_aktivitas_forum = h.id_aktivitas_pd
                                                        WHERE h.status_pelaksanaan NOT IN (2,3,4)  AND a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                                                if($dataLokasi == 0){
                                                    return response ()->json (['pesan'=>'Data Lokasi Aktivitas Kegiatan gagal di-Load','status_pesan'=>'0']);
                                                } else {
                                                    $dataBelanja=DB::INSERT('INSERT INTO trx_rkpd_final_belanja_pd (tahun_forum, no_urut, id_aktivitas_pd, id_belanja_forum, id_zona_ssh, id_belanja_renja, 
                                                            sumber_belanja, id_aktivitas_asb, id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, harga_satuan, 
                                                            jml_belanja, volume_1_forum, id_satuan_1_forum, volume_2_forum, id_satuan_2_forum, harga_satuan_forum, jml_belanja_forum, status_data, sumber_data)
                                                            SELECT g.tahun_forum, h.no_urut, g.id_aktivitas_pd, h.id_belanja_forum, h.id_zona_ssh, h.id_belanja_renja, h.sumber_belanja, h.id_aktivitas_asb, 
                                                            h.id_item_ssh, h.id_rekening_ssh, h.uraian_belanja, h.volume_1_forum, h.id_satuan_1_forum, h.volume_2_forum, h.id_satuan_2_forum, h.harga_satuan_forum, 
                                                            h.jml_belanja_forum, h.volume_1_forum, h.id_satuan_1_forum, h.volume_2_forum, h.id_satuan_2_forum, h.harga_satuan_forum, h.jml_belanja_forum, 0, 0
                                                            FROM trx_rkpd_final AS a
                                                            INNER JOIN trx_rkpd_final_urusan AS b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
                                                            INNER JOIN trx_rkpd_final_pelaksana AS c ON b.id_urusan_rkpd = c.id_urusan_rkpd
                                                            INNER JOIN trx_rkpd_final_program_pd AS d ON c.id_pelaksana_rkpd = d.id_rkpd_rancangan
                                                            INNER JOIN trx_rkpd_final_kegiatan_pd AS e ON d.id_program_pd = e.id_program_pd
                                                            INNER JOIN trx_rkpd_final_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                                                            INNER JOIN trx_rkpd_final_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd
                                                            INNER JOIN trx_rkpd_ranhir_belanja_pd AS h ON g.id_aktivitas_forum = h.id_aktivitas_pd
                                                            WHERE a.id_forum_rkpdprog='.$req->id_rkpd_ranwal);
                                                    if($dataBelanja == 0){
                                                        return response ()->json (['pesan'=>'Data Rincian Belanja gagal di-Load','status_pesan'=>'0']);
                                                    } else {
                                                        return response ()->json (['pesan'=>'Data Musrenbang RKPD Berhasil di-Load','status_pesan'=>'1']);
                                                    }
                                                    
                                                } ///1
                                                
                                            } //2
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
        $dataPerencana = DB::SELECT('SELECT a.kd_kab,a.id_pemda,a.prefix_pemda,a.nm_prov,a.nm_kabkota,a.ibu_kota,a.nama_jabatan_kepala_daerah,a.nama_kepala_daerah,a.nama_jabatan_sekretariat_daerah,a.nama_sekretariat_daerah,a.nip_sekretariat_daerah,a.unit_perencanaan,a.nama_kepala_bappeda,a.nip_kepala_bappeda,a.unit_keuangan,a.nama_kepala_bpkad,a.nip_kepala_bpkad,b.nm_unit
            FROM ref_pemda AS a
            LEFT OUTER JOIN ref_unit AS b ON a.unit_perencanaan = b.id_unit LIMIT 1');

        return json_encode($dataPerencana);
    }

    public function addDokumen(Request $req)
    {
        try{
            $data = new TrxRkpdFinalDokumen;
            $data->nomor_rkpd = $req->nomor_rkpd ;
            $data->tanggal_rkpd = $req->tanggal_rkpd ;
            $data->tahun_rkpd = $req->tahun_rkpd ;
            $data->uraian_perkada = $req->uraian_perkada ;
            $data->id_unit_perencana = $req->id_unit_perencana ;
            $data->jabatan_tandatangan = "Kepala" ;
            $data->nama_tandatangan = $req->nama_tandatangan ;
            $data->nip_tandatangan = $req->nip_tandatangan ;
            $data->flag = 0 ;
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
            $data = TrxRkpdFinalDokumen::find($req->id_dokumen_rkpd);
            $data->nomor_rkpd = $req->nomor_rkpd ;
            $data->tanggal_rkpd = $req->tanggal_rkpd ;
            $data->tahun_rkpd = $req->tahun_rkpd ;
            $data->uraian_perkada = $req->uraian_perkada ;
            $data->id_unit_perencana = $req->id_unit_perencana ;
            $data->jabatan_tandatangan = "Kepala" ;
            $data->nama_tandatangan = $req->nama_tandatangan ;
            $data->nip_tandatangan = $req->nip_tandatangan ;
            // $data->flag = $req->flag ;
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
        $result = TrxRkpdFinalDokumen::destroy($req->id_dokumen_rkpd);
    
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
        // $cek = DB::SELECT('SELECT x.tahun_rkpd, COALESCE(COUNT(x.id_rkpd_rancangan),0) AS jml, 
        //     COALESCE((SELECT COUNT(a.id_rkpd_rancangan) AS jml FROM trx_rkpd_final AS a 
        //     LEFT OUTER JOIN trx_rkpd_final AS b ON a.tahun_rkpd = b.tahun_rkpd AND a.id_rkpd_rancangan = b.id_rkpd_rancangan
        //     WHERE b.id_rkpd_rancangan is null AND a.status_data = 2 AND (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3) 
        //     AND a.tahun_rkpd = x.tahun_rkpd GROUP BY a.tahun_rkpd, a.status_data),0) AS jml_load 
        //     FROM trx_rkpd_final AS x 
        //     WHERE x.status_data = 2 AND (x.status_pelaksanaan <> 2 AND x.status_pelaksanaan <> 3) AND x.tahun_rkpd ='.$req->tahun_rkpd.'
        //     GROUP BY x.tahun_rkpd, x.status_data');

        // if($cek != null && ($cek[0]->jml - $cek[0]->jml_load) <> $cek[0]->jml ){
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
        // } else {
            // return response ()->json (['pesan'=>'Data Gagal Proses, Dokumen sudah dipakai pada tahap selanjutnya (0cdrPD)','status_pesan'=>'0']);
        // }
    }
    
}
