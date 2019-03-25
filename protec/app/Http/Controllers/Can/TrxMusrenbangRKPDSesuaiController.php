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
use App\Models\RefUnit;
use App\Models\RefSubUnit;
use App\Models\TrxRkpdRancanganDokumen;
use App\Models\TrxRkpdRancangan;
use App\Models\TrxRkpdRancanganIndikator;
use App\Models\TrxRkpdRancanganUrusan;
use App\Models\TrxRkpdRancanganPelaksana;
use App\Models\TrxRkpdRancanganProgramPd;
use App\Models\TrxRkpdRancanganProgIndikatorPd;
use App\Models\TrxRkpdRancanganKegiatanPd;
use App\Models\TrxRkpdRancanganKegIndikatorPd;
use App\Models\TrxRkpdRancanganPelaksanaPd;
use App\Models\TrxRkpdRancanganAktivitasPd;
use App\Models\TrxRkpdRancanganLokasiPd;
use App\Models\TrxRkpdRancanganBelanjaPd;


class TrxRancangRKPDSesuaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getData()
    {
        $dataForum = TrxRkpdRancanganKegiatanPd::paginate(15);
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

    public function testsql()

    { 
        // $users = DB::connection('sqlsrv')->select('SELECT * FROM Ref_Bidang');
        // $users = (Auth::check());
        $users = Session::has('tahun');
        return dd($users);
    }

    public function getSelectProgram($id_unit,$tahun_rkpd){
        $getSelect=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, a.* FROM 
                (SELECT a.tahun_renja, a.id_rkpd_ranwal, c.uraian_program_rpjmd
                FROM trx_renja_rancangan_program AS a
                INNER JOIN trx_rkpd_ranwal as c ON a.id_rkpd_ranwal =  c.id_rkpd_ranwal
				LEFT OUTER JOIN trx_forum_skpd_program as d ON a.id_renja_program = d.id_renja_program
				WHERE d.id_renja_program is null AND a.status_data = 2 and (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3 and a.id_unit='.$id_unit.' AND a.tahun_renja='.$tahun_rkpd.')) a, 
                (SELECT @id:=0) x GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.uraian_program_rpjmd');
        // return json_encode($getSelect);
        return DataTables::of($getSelect)
        ->addColumn('action',function($getSelect){
          return '
              <button id="btnReLoad" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="" title="" class="btn btn-primary">
              <i class="fa fa-download fa-fw fa-lg"></i> Load Data</button>
          ' ;})
        ->make(true);

    }


    public function getProgramRkpd($tahun,$unit)
    {
        $getRenja=DB::select('SELECT (@id:=@id+1) as no_urut,a.id_rkpd_rancangan, a.id_rkpd_ranwal, a.tahun_rkpd, a.id_program_rpjmd, 
                    b.id_unit, a.uraian_program_rpjmd, a.pagu_rpjmd, a.pagu_ranwal, a.keterangan_program, a.jenis_belanja,
                    a.status_data, a.sumber_data, COALESCE(SUM(c.jml_program),0) as jml_program, COALESCE(SUM(c.jml_kegiatan),0) as jml_kegiatan,
                    COALESCE(SUM(c.jml_pagu),0) as jml_pagu,COALESCE(SUM(c.jml_pagu_aktivitas),0) as jml_pagu_aktivitas, COALESCE(SUM(c.jml_aktivitas),0) as jml_aktivitas,
                    CASE a.status_data
                          WHEN 0 THEN "fa fa-question"
                          WHEN 1 THEN "fa fa-check-square-o"
                      END AS status_icon,
                      CASE a.status_data
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna 
                    FROM trx_rkpd_rancangan a
                    INNER JOIN trx_rkpd_rancangan_urusan d ON a.id_rkpd_rancangan = d.id_rkpd_rancangan
                    INNER JOIN trx_rkpd_rancangan_pelaksana b ON d.id_rkpd_rancangan = b.id_rkpd_rancangan AND d.id_urusan_rkpd = b.id_urusan_rkpd
                    LEFT OUTER JOIN(SELECT c.id_rkpd_rancangan, c.tahun_forum, c.id_unit, SUM(c.jml_program) AS jml_program,SUM(d.jml_kegiatan) AS jml_kegiatan, SUM(d.jml_pagu) AS jml_pagu, 
                    SUM(e.jml_aktivitas) AS jml_aktivitas, SUM(e.jml_pagu_aktivitas) AS jml_pagu_aktivitas 
                    FROM (SELECT a.id_rkpd_rancangan, a.id_program_pd, a.tahun_forum, a.id_unit, count(DISTINCT a.id_program_pd) as jml_program 
                    FROM trx_rkpd_rancangan_program_pd a GROUP BY a.id_rkpd_rancangan, a.id_program_pd, a.tahun_forum, a.id_unit ) c
                    LEFT OUTER JOIN( SELECT b.id_program_pd, a.id_kegiatan_pd,b.tahun_forum,b.id_unit,COUNT(DISTINCT a.id_kegiatan_pd) as jml_kegiatan,SUM(DISTINCT a.pagu_forum) as jml_pagu 
                    FROM trx_rkpd_rancangan_kegiatan_pd a INNER JOIN trx_rkpd_rancangan_program_pd b ON a.id_program_pd = b.id_program_pd 
                    GROUP BY b.id_program_pd, a.id_kegiatan_pd,b.tahun_forum,b.id_unit,a.id_kegiatan_pd ) d ON c.id_program_pd = d.id_program_pd AND c.id_unit = d.id_unit
                    LEFT OUTER JOIN (SELECT a.id_kegiatan_pd,b.tahun_forum,b.id_unit,COALESCE(count(DISTINCT c.id_aktivitas_pd)) AS jml_aktivitas, SUM(DISTINCT c.pagu_aktivitas_forum) AS jml_pagu_aktivitas 
                    FROM trx_rkpd_rancangan_aktivitas_pd c
                    INNER JOIN trx_rkpd_rancangan_pelaksana_pd d ON c.id_pelaksana_pd = d.id_pelaksana_pd
                    INNER JOIN trx_rkpd_rancangan_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
                    INNER JOIN trx_rkpd_rancangan_program_pd b ON a.id_program_pd = b.id_program_pd
                    GROUP BY a.id_kegiatan_pd,b.tahun_forum,b.id_unit) e 
                    ON d.id_kegiatan_pd = e.id_kegiatan_pd AND d.id_unit = e.id_unit
                    GROUP BY c.id_rkpd_rancangan, c.tahun_forum, c.id_unit) c ON c.id_rkpd_rancangan = b.id_pelaksana_rkpd AND c.id_unit = b.id_unit, 
                    (SELECT @id:=0) x  
                    WHERE a.tahun_rkpd = '.$tahun.' AND b.id_unit ='.$unit.' GROUP BY a.id_rkpd_rancangan, a.id_rkpd_ranwal, a.tahun_rkpd, a.id_program_rpjmd, 
                    b.id_unit, a.uraian_program_rpjmd, a.pagu_rpjmd, a.pagu_ranwal, a.keterangan_program, a.jenis_belanja, a.status_data, a.sumber_data');

          return DataTables::of($getRenja)

            ->addColumn('details_url', function($getRenja) {
                    return url('rancanganrkpd/getChildBidang/'.$getRenja->id_unit.'/'. $getRenja->id_rkpd_rancangan);
                })
            ->make(true);
    }

    public function getChildBidang($id_unit,$id_rkpd_rancangan)
    {
        $getRenja = DB::SELECT('SELECT DISTINCT '.Session::get('tahun').' AS tahun_forum, b.id_rkpd_rancangan,d.kd_bidang,d.nm_bidang,c.id_unit,c.id_pelaksana_rkpd, a.uraian_program_rpjmd, b.id_bidang  
                FROM trx_rkpd_rancangan a
                INNER JOIN trx_rkpd_rancangan_urusan b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
                INNER JOIN trx_rkpd_rancangan_pelaksana c ON b.id_urusan_rkpd = c.id_urusan_rkpd AND b.id_rkpd_rancangan = c.id_rkpd_rancangan
                INNER JOIN ref_bidang d ON b.id_bidang = d.id_bidang
                WHERE c.id_unit='. $id_unit.' AND b.id_rkpd_rancangan='.$id_rkpd_rancangan.'
                ORDER BY b.id_rkpd_rancangan,d.kd_bidang,c.id_unit, b.id_bidang');

        return Datatables::of($getRenja)
            ->addColumn('action',function($getRenja){
                return '
                    <button type="button" class="btnViewProgSkpd btn btn-primary btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-list-alt fa-fw fa-lg"></i></span>Lihat Program SKPD</button>
                    ';

            })
            ->make(true);
    }

    public function getProgramRenja($id_unit,$id_pelaksana_rkpd)
    {
      $getProgramRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.id_program_pd, c.id_pelaksana_rkpd, c.id_rkpd_rancangan, a.tahun_forum, a.no_urut, a.id_unit, b.nm_unit, a.id_renja_program, 
            a.id_program_renstra, a.uraian_program_renstra, a.id_program_ref, d.uraian_program, a.pagu_tahun_renstra, a.pagu_forum, a.sumber_data, a.status_pelaksanaan, a.jenis_belanja,g.uraian_program_renstra as program_renstra,
            a.ket_usulan, a.status_data,
            CASE a.status_data
                WHEN 0 THEN "fa fa-question"
                WHEN 1 THEN "fa fa-check-square-o"
            END AS status_icon,
            CASE a.status_data
                WHEN 0 THEN "red"
                WHEN 1 THEN "green"
            END AS warna,
            COALESCE(f.jml_kegiatan,0) as jml_kegiatan,
            COALESCE(f.jml_pagu,0) as jml_pagu,
            COALESCE(f.jml_0k,0) as jml_0k 
            FROM trx_rkpd_rancangan_program_pd a
            INNER JOIN ref_unit b ON a.id_unit = b.id_unit 
            INNER JOIN trx_rkpd_rancangan_pelaksana c ON a.id_rkpd_rancangan = c.id_pelaksana_rkpd
            INNER JOIN ref_program d ON a.id_program_ref = d.id_program
            LEFT OUTER JOIN trx_renstra_program g ON a.id_program_renstra = g.id_program_renstra
            LEFT OUTER JOIN (SELECT a.tahun_forum, a.id_program_pd, a.id_unit,
                        COALESCE(COUNT(a.id_kegiatan_pd),0) AS jml_kegiatan, 
                        COALESCE(SUM(a.pagu_forum)) AS jml_pagu,
                        COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0k
                        FROM trx_rkpd_rancangan_kegiatan_pd a
            GROUP BY a.tahun_forum, a.id_program_pd, a.id_unit) f
            ON a.tahun_forum = f.tahun_forum AND a.id_program_pd = f.id_program_pd
            WHERE a.id_unit ='.$id_unit.' AND a.tahun_forum='.Session::get('tahun').' AND c.id_pelaksana_rkpd='.$id_pelaksana_rkpd.' ) a,(SELECT @id:=0) z');

      return DataTables::of($getProgramRenja)
        ->addColumn('details_url', function($getProgramRenja) {
                    return url('rancanganrkpd/getIndikatorRenja/'.$getProgramRenja->id_program_pd);
                })
        ->addColumn('action', function ($getProgramRenja) {
            if($getProgramRenja->status_data == 0)
            return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="add-indikator dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Program</a>
                        </li>
                        <li>
                            <a class="view-kegiatan dropdown-item"><i class="fa fa-briefcase fa-fw fa-lg"></i> Lihat Kegiatan SKPD</a>
                        </li>
                        <li>
                            <a class="edit-ProgRenja dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit Program SKPD</a>
                        </li>
                        <li>
                            <a class="post-ProgRenja dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Program SKPD</a>
                        </li>
                    </ul>
                    </div>
            ';
            if($getProgramRenja->status_data == 1)
            return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-kegiatan dropdown-item"><i class="fa fa-briefcase fa-fw fa-lg"></i> Lihat Kegiatan SKPD</a>
                        </li>
                        <li>
                            <a class="edit-ProgRenja dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit Program SKPD</a>
                        </li>
                        <li>
                            <a class="unpost-ProgRenja dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Un-Posting Program SKPD</a>
                        </li>
                    </ul>
                    </div>
            ';
            if($getProgramRenja->status_data == 2)
            return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-kegiatan dropdown-item"><i class="fa fa-briefcase fa-fw fa-lg"></i> Lihat Kegiatan SKPD</a>
                        </li>
                        <li>
                            <a class="edit-ProgRenja dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit Program SKPD</a>
                        </li>
                    </ul>
                    </div>
            ';
        })
        ->make(true);
    }

    public function getIndikatorRenja($id_rkpd)
    {
      $indikatorProg=DB::select('SELECT (@id:=@id+1) as urut,a.tahun_renja, a.no_urut, a.id_program_pd, a.id_program_renstra,
                    a.id_indikator_program, a.id_perubahan, a.kd_indikator, a.uraian_indikator_program,
                    a.tolok_ukur_indikator, a.target_renstra, a.target_renja, a.indikator_output,
                    a.id_satuan_ouput, a.indikator_input, a.target_input, a.id_satuan_input, a.status_data, a.sumber_data,
                            CASE a.status_data
                              WHEN 0 THEN "fa fa-question"
                              WHEN 1 THEN "fa fa-check-square-o"
                            END AS status_icon,
                          CASE a.status_data
                              WHEN 0 THEN "red"
                              WHEN 1 THEN "green"
                          END AS warna  
                FROM trx_rkpd_rancangan_prog_indikator_pd AS a 
                ,(SELECT @id:=0) x where a.id_program_pd='.$id_rkpd);

      return DataTables::of($indikatorProg)
        ->addColumn('action', function ($indikatorProg) {
            if($indikatorProg->status_data==0 ){
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-indikator dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Lihat Indikator</a>
                        </li>
                        <li>
                            <a class="post-InProgRenja dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-danger"></i> Posting Indikator</a>
                        </li>
                    </ul>
                    </div>
                ';
            }
            if($indikatorProg->status_data==1 ){
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-indikator dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Lihat Indikator</a>
                        </li>
                        <li>
                            <a class="post-InProgRenja dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-danger"></i> Un-Posting Indikator</a>
                        </li>
                    </ul>
                    </div>
                ';
            }
          })
        ->make(true); 
    }

    public function getKegiatanRenja($id_program)
    {
      $getKegiatanRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.id_kegiatan_pd, a.id_program_pd, a.id_unit, 
            a.tahun_forum, a.no_urut, a.id_renja, a.id_rkpd_renstra, 
            a.id_program_renstra, a.id_kegiatan_renstra, a.id_kegiatan_ref,c.kd_kegiatan, c.nm_kegiatan, 
            a.uraian_kegiatan_forum, a.pagu_tahun_kegiatan, e.uraian_kegiatan_renstra,
            a.pagu_kegiatan_renstra, a.pagu_plus1_renja, a.pagu_plus1_forum, a.pagu_forum, a.keterangan_status, a.status_data, 
            a.status_pelaksanaan, a.sumber_data,
            CASE a.status_data
            WHEN 0 THEN "fa fa-question"
            WHEN 1 THEN "fa fa-check-square-o"
            END AS status_icon,
            CASE a.status_data
            WHEN 0 THEN "red"
            WHEN 1 THEN "green"
            END AS warna,
            COALESCE(SUM(a.pagu_forum)) AS jml_pagu,
            COALESCE(sum(b.jml_aktivitas),0) as jml_aktivitas,
            COALESCE(sum(b.jml_pagu_aktivitas),0) as jml_pagu_aktivitas,
            COALESCE(sum(b.jml_musren_aktivitas),0) as jml_musren_aktivitas
            FROM trx_rkpd_rancangan_kegiatan_pd a
            INNER JOIN (Select a.id_kegiatan, a.id_program, a.nm_kegiatan,
            CONCAT(LEFT(CONCAT(0,d.kd_urusan),2),".",RIGHT(CONCAT(0,c.kd_bidang),2),".",RIGHT(CONCAT("00",b.kd_program),3),".",RIGHT(CONCAT("00",a.kd_kegiatan),3)) AS kd_kegiatan
            FROM ref_kegiatan a
            INNER JOIN ref_program b ON a.id_program=b.id_program
            INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
            INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan) c ON a.id_kegiatan_ref=c.id_kegiatan
            LEFT OUTER JOIN (SELECT a.tahun_forum, b.id_kegiatan_pd, 
            COUNT(a.id_aktivitas_forum) as jml_aktivitas,
            SUM(a.pagu_aktivitas_forum) as jml_pagu_aktivitas,
            SUM(a.pagu_aktivitas_forum*(a.pagu_musren/100)) as jml_musren_aktivitas
            FROM trx_rkpd_rancangan_aktivitas_pd a 
            INNER JOIN trx_rkpd_rancangan_pelaksana_pd b ON a.id_pelaksana_pd = b.id_pelaksana_pd
            WHERE a.status_data = 1
            GROUP BY a.tahun_forum, b.id_kegiatan_pd, a.status_data) b 
            ON a.tahun_forum=a.tahun_forum AND a.id_kegiatan_pd = b.id_kegiatan_pd                   
            LEFT OUTER JOIN trx_renstra_kegiatan e ON a.id_kegiatan_renstra = e.id_kegiatan_renstra  
            WHERE a.id_program_pd='.$id_program.'  
            GROUP BY a.id_kegiatan_pd, a.id_program_pd, a.id_unit, a.tahun_forum, a.no_urut, a.id_renja, a.id_rkpd_renstra, 
            a.id_program_renstra, a.id_kegiatan_renstra, a.id_kegiatan_ref, a.uraian_kegiatan_forum, a.pagu_tahun_kegiatan, 
            a.pagu_kegiatan_renstra, a.pagu_forum, a.keterangan_status, a.status_data, e.uraian_kegiatan_renstra,
            a.status_pelaksanaan, a.sumber_data, a.pagu_plus1_renja, a.pagu_plus1_forum) a,(SELECT @id:=0) z');

      return DataTables::of($getKegiatanRenja)
      ->addColumn('details_url', function($getKegiatanRenja) {
                    return url('rancanganrkpd/getIndikatorKegiatan/'.$getKegiatanRenja->id_kegiatan_pd);
                })
        ->addColumn('action', function ($getKegiatanRenja) {
            if($getKegiatanRenja->status_data==0)
                return '                         
                <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="add-indikatorKeg dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Kegiatan</a>
                            </li>
                            <li>
                                <a id="btnViewPelaksana" class="dropdown-item"><i class="fa fa-users fa-fw fa-lg"></i> Lihat Pelaksana</a>
                            </li>
                            <li>
                                <a id="edit-kegiatan" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Kegiatan Renja</a>
                            </li> 
                            <li>
                                <a class="post-KegForum dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Kegiatan Forum</a>
                            </li>                         
                        </ul>
                    </div>
                ';
            if($getKegiatanRenja->status_data ==1)
                return '                         
                <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a id="btnViewPelaksana" class="dropdown-item"><i class="fa fa-users fa-fw fa-lg"></i> Lihat Pelaksana</a>
                            </li>
                            <li>
                                <a id="edit-kegiatan" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                            </li> 
                            <li>
                                <a class="post-KegForum dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Un-Posting Kegiatan Forum</a>
                            </li>                          
                        </ul>
                    </div>
                ';
            
            if($getKegiatanRenja->status_data ==2)
                return '                         
                <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a id="btnViewPelaksana" class="dropdown-item"><i class="fa fa-users fa-fw fa-lg"></i> Lihat Pelaksana</a>
                            </li>
                            <li>
                                <a id="edit-kegiatan" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                            </li>                         
                        </ul>
                    </div>
                ';
        })
        ->make(true);
    } 

    public function getIndikatorKegiatan($id_rkpd)
    {
      $indikatorProg=DB::select('SELECT (@id:=@id+1) as urut,a.tahun_renja, a.no_urut, a.id_kegiatan_pd,
                a.id_program_renstra, a.id_indikator_kegiatan, a.id_perubahan,
                a.kd_indikator, a.uraian_indikator_kegiatan, a.tolok_ukur_indikator,
                a.target_renstra, a.target_renja, a.indikator_output, a.id_satuan_ouput,
                a.indikator_input, a.target_input, a.id_satuan_input, a.status_data, a.sumber_data,
                            CASE a.status_data
                              WHEN 0 THEN "fa fa-question"
                              WHEN 1 THEN "fa fa-check-square-o"
                            END AS status_icon,
                          CASE a.status_data
                              WHEN 0 THEN "red"
                              WHEN 1 THEN "green"
                          END AS warna  
                FROM trx_rkpd_rancangan_keg_indikator_pd AS a 
                ,(SELECT @id:=0) x where a.id_kegiatan_pd='.$id_rkpd);

      return DataTables::of($indikatorProg)
        ->addColumn('action', function ($indikatorProg) {
          
            if($indikatorProg->status_data==0 ){
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-indikator_keg dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Lihat Indikator</a>
                        </li>
                        <li>
                            <a class="post-InKegRenja dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-danger"></i> Posting Indikator</a>
                        </li>
                    </ul>
                    </div>
                ';
            }
            if($indikatorProg->status_data==1 ){
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-indikator_keg dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Lihat Indikator</a>
                        </li>
                        <li>
                            <a class="post-InKegRenja dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-danger"></i> Un-Posting Indikator</a>
                        </li>
                    </ul>
                    </div>
                ';
            }

          })
        ->make(true); 
    }

public function getAktivitas($id_forum_skpd)
{
   $getAktivitas=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_aktivitas_pd, a.id_pelaksana_pd, a.tahun_forum, a.no_urut, 
                a.sumber_aktivitas, a.id_aktivitas_asb, a.id_aktivitas_renja, a.uraian_aktivitas_kegiatan, COALESCE(a.id_satuan_publik,0) as id_satuan_publik,
                a.volume_aktivitas_1, COALESCE(a.id_satuan_1,-1) as id_satuan_1, a.volume_aktivitas_2, 
                COALESCE(a.id_satuan_2,0) as id_satuan_2, a.id_program_nasional, 
                a.id_program_provinsi, a.jenis_kegiatan, a.sumber_dana, a.pagu_aktivitas_renja, 
                a.pagu_aktivitas_forum, a.pagu_musren, a.status_data, a.status_musren, a.sumber_data ,
                (a.pagu_aktivitas_forum*(a.pagu_musren/100)) as jml_musren_aktivitas, a.status_pelaksanaan, a.keterangan_aktivitas,
                COALESCE(b.uraian_satuan,"Kosong") as ur_satuan_1, COALESCE(c.uraian_satuan,"Kosong") as ur_satuan_2,
                CASE a.status_data
                          WHEN 0 THEN "fa fa-question fa-lg"
                          WHEN 1 THEN "fa fa-check-square-o fa-lg"
                      END AS status_icon,
                CASE a.status_data
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna,
                CASE a.sumber_aktivitas
                          WHEN 0 THEN "fa fa-registered"
                          WHEN 1 THEN ""
                      END AS img,
                CASE a.sumber_aktivitas
                  WHEN 0 THEN 
                      CASE a.id_satuan_publik 
                        WHEN 0 THEN COALESCE(d.jml_vol_lok,0)
                        WHEN 1 THEN COALESCE(a.volume_aktivitas_1,0)
                      END
                  WHEN 1 THEN
                    COALESCE(d.volume_1,0)
                END AS jml_vol_1,
                CASE a.sumber_aktivitas
                  WHEN 0 THEN 
                      CASE a.id_satuan_publik 
                        WHEN 1 THEN COALESCE(d.jml_vol_lok,0)
                        WHEN 0 THEN COALESCE(a.volume_aktivitas_2,0)
                      END
                  WHEN 1 THEN
                    COALESCE(d.volume_2,0)
                END AS jml_vol_2,
                COALESCE(e.jml_belanja,0) as jml_belanja 
                FROM trx_rkpd_rancangan_aktivitas_pd a
                LEFT OUTER JOIN ref_satuan b ON a.id_satuan_1 = b.id_satuan
                LEFT OUTER JOIN ref_satuan c ON a.id_satuan_2 = c.id_satuan
                LEFT OUTER JOIN (SELECT a.tahun_forum, a.id_aktivitas_pd,
                    CASE b.id_satuan_publik 
                      WHEN 0 THEN sum(a.volume_1)
                      WHEN 1 THEN sum(a.volume_2)
                    END AS jml_vol_lok,
                    SUM(IF(a.id_satuan_1 <> -1 AND a.id_satuan_1 <> 0, a.volume_1, 0)) as volume_1,
                    SUM(IF(a.id_satuan_2 <> -1 AND a.id_satuan_2 <> 0, a.volume_2, 0)) as volume_2
                    FROM trx_rkpd_rancangan_lokasi_pd AS a
                    INNER JOIN trx_rkpd_rancangan_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
                    WHERE a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3                    
                    GROUP BY a.tahun_forum, a.id_aktivitas_pd, b.id_satuan_publik) d ON a.id_aktivitas_pd = d.id_aktivitas_pd
                LEFT OUTER JOIN (SELECT a.tahun_forum, a.id_aktivitas_pd, Sum(a.jml_belanja_forum) as jml_belanja
                    FROM trx_rkpd_rancangan_belanja_pd AS a
                    GROUP BY a.tahun_forum,a.id_aktivitas_pd) e ON a.id_aktivitas_pd = e.id_aktivitas_pd,
                (SELECT @id:=0) x WHERE id_pelaksana_pd='.$id_forum_skpd);

   return DataTables::of($getAktivitas)
   ->addColumn('action', function ($getAktivitas) {
            if($getAktivitas->status_data == 0)
                return '                         
                <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a id="btnViewLokasi" class="dropdown-item"><i class="fa fa-location-arrow fa-lg fa-fw text-success"></i> Lihat Lokasi</a>
                            </li>
                            <li>
                                <a id="btnViewBelanja" class="dropdown-item"><i class="fa fa-shopping-cart fa-lg fa-fw text-primary"></i> Lihat Rincian Belanja</a>
                            </li>
                            <li>
                                <a id="btnEditAktivitas" class="dropdown-item"><i class="fa fa-pencil fa-lg fa-fw text-warning"></i> Ubah Aktivitas</a>
                            </li> 
                            <li>
                                <a class="post-AktivForum dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-danger"></i> Posting Aktivitas Forum</a>
                            </li>                           
                        </ul>
                    </div>
                ';
            if($getAktivitas->status_data == 1)
                return '                         
                <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a id="btnViewLokasi" class="dropdown-item"><i class="fa fa-location-arrow fa-lg fa-fw text-success"></i> Lihat Lokasi</a>
                            </li>
                            <li>
                                <a id="btnViewBelanja" class="dropdown-item"><i class="fa fa-shopping-cart fa-lg fa-fw text-primary"></i> Lihat Rincian Belanja</a>
                            </li>
                            <li>
                                <a id="btnEditAktivitas" class="dropdown-item"><i class="fa fa-pencil fa-lg fa-fw text-warning"></i> Lihat Aktivitas</a>
                            </li> 
                            <li>
                                <a class="post-AktivForum dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-danger"></i> Un-Posting Aktivitas Forum</a>
                            </li>                           
                        </ul>
                    </div>
                ';
            if($getAktivitas->status_data == 2)
                return '                         
                <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a id="btnViewLokasi" class="dropdown-item"><i class="fa fa-location-arrow fa-lg fa-fw text-success"></i> Lihat Lokasi</a>
                            </li>
                            <li>
                                <a id="btnViewBelanja" class="dropdown-item"><i class="fa fa-shopping-cart fa-lg fa-fw text-primary"></i> Lihat Rincian Belanja</a>
                            </li>
                            <li>
                                <a id="btnEditAktivitas" class="dropdown-item"><i class="fa fa-pencil fa-lg fa-fw text-warning"></i> Ubah Aktivitas</a>
                            </li>                          
                        </ul>
                    </div>
                ';
        })
   ->make(true);
}

public function getPelaksanaAktivitas($id_aktivitas){
   $getPelaksana=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.id_pelaksana_pd, a.tahun_forum, a.no_urut, 
            a.id_kegiatan_pd, a.id_sub_unit, a.id_pelaksana_renja, a.id_lokasi, a.sumber_data, a.ket_pelaksana, 
            a.status_pelaksanaan, d.nm_sub, e.nama_lokasi, a.status_data, COALESCE(b.jml_lokasi,0) as jml_lokasi, 
            COALESCE(c.jml_belanja,0) as jml_pagu,COALESCE(f.jml_pagu_aktivitas,0) as jml_pagu_aktivitas,
            CASE a.status_data
                WHEN 0 THEN "fa fa-question"
                WHEN 1 THEN "fa fa-check-square-o"
            END AS status_icon,
            CASE a.status_data
                WHEN 0 THEN "red"
                WHEN 1 THEN "green"
            END AS warna 
            FROM trx_rkpd_rancangan_pelaksana_pd a
            LEFT OUTER JOIN (SELECT b.id_pelaksana_pd, b.tahun_forum, Count(a.id_lokasi_forum) AS jml_lokasi
            FROM trx_rkpd_rancangan_aktivitas_pd AS b
            INNER JOIN trx_rkpd_rancangan_lokasi_pd AS a ON a.id_aktivitas_pd = b.id_aktivitas_pd
            GROUP BY b.id_pelaksana_pd, b.tahun_forum) b ON a.id_pelaksana_pd = b.id_pelaksana_pd
            LEFT OUTER JOIN (SELECT a.tahun_forum, Sum(a.jml_belanja_forum) AS jml_belanja, c.id_pelaksana_pd
            FROM trx_rkpd_rancangan_belanja_pd AS a
            INNER JOIN trx_rkpd_rancangan_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
            INNER JOIN trx_rkpd_rancangan_pelaksana_pd AS c ON b.id_pelaksana_pd = c.id_pelaksana_pd
            WHERE b.status_data = 1
            GROUP BY a.tahun_forum, c.id_pelaksana_pd) c ON a.id_pelaksana_pd = c.id_pelaksana_pd
            LEFT OUTER JOIN (SELECT b.id_pelaksana_pd, b.tahun_forum, SUM(b.pagu_aktivitas_forum) as jml_pagu_aktivitas 
            FROM trx_rkpd_rancangan_aktivitas_pd AS b WHERE b.status_data = 1 GROUP BY b.id_pelaksana_pd, b.tahun_forum, b.status_data) f ON a.id_pelaksana_pd = f.id_pelaksana_pd
            INNER JOIN ref_sub_unit d ON a.id_sub_unit = d.id_sub_unit
            LEFT OUTER JOIN ref_lokasi e ON a.id_lokasi = e.id_lokasi) a,
            (SELECT @id:=0) x WHERE a.id_kegiatan_pd ='.$id_aktivitas);

   return DataTables::of($getPelaksana)
   ->addColumn('action', function ($getPelaksana) {
        return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="view-aktivitas" class="dropdown-item"><i class="fa fa-calendar fa-fw fa-lg"></i> Lihat Aktivitas</a>
                        </li>
                        <li>
                            <a id="btnEditPelaksana" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit Pelaksana</a>
                        </li>                          
                    </ul>
                </div>
            ';
   })
   ->make(true);
}

public function getLokasiAktivitas($id_aktivitas_pd)
{
   $LokAktiv=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_forum, a.no_urut, a.id_aktivitas_pd, a.id_lokasi_pd,
                    a.id_lokasi_forum, a.id_lokasi, b.nama_lokasi, a.volume_1, a.volume_2,a.volume_usulan_1, a.volume_usulan_2, a.id_satuan_1, a.id_satuan_2,
                    a.id_lokasi_renja, a.jenis_lokasi, a.id_desa, a.id_kecamatan, a.rt, a.rw, 
                    a.uraian_lokasi, a.lat, a.lang, a.status_data, a.sumber_data, a.status_pelaksanaan, a.ket_lokasi, 
                                CASE a.status_data
                                    WHEN 0 THEN "fa fa-question"
                                    WHEN 1 THEN "fa fa-check-square-o"
                                END AS status_icon,
                                CASE a.status_data
                                    WHEN 0 THEN "red"
                                    WHEN 1 THEN "green"
                                END AS warna,
                                CASE a.sumber_data
                                    WHEN 0 THEN "Renja SKPD"
                                    WHEN 1 THEN "Musrenbang Desa"
                                    WHEN 2 THEN "Musrenbang Kecamatan"
                                    WHEN 3 THEN "Pokir Dewan"
                                    WHEN 4 THEN "Forum Perangkat Daerah"
                                    WHEN 5 THEN "Rancangan RKPD"
                                END AS sumber_display,
                                CASE a.status_pelaksanaan
                                    WHEN 0 THEN "Tanpa Perubahan"
                                    WHEN 1 THEN "Dengan Perubahan"
                                    WHEN 2 THEN "Digabungkan"
                                    WHEN 3 THEN "Ditolak"
                                    WHEN 4 THEN "Diluarkewenangan"
                                    WHEN 5 THEN "Dilimpahkan"
                                END AS usulan_display,
                                c.status_data as status_aktivitas
                    FROM trx_rkpd_rancangan_lokasi_pd a
                    INNER JOIN ref_lokasi b on a.id_lokasi = b.id_lokasi
                    INNER JOIN trx_rkpd_rancangan_aktivitas_pd c ON a.id_aktivitas_pd = c.id_aktivitas_pd) a,
                    (SELECT @id:=0) x WHERE a.id_aktivitas_pd='.$id_aktivitas_pd);

   return DataTables::of($LokAktiv)
   ->addColumn('details_url', function($LokAktiv) {
                    return url('forumskpd/forum/getChildUsulan/'.$LokAktiv->id_lokasi_forum);
                })
   ->addColumn('action', function ($LokAktiv) {
        return '<button id="btnEditLokasi" type="button" class="btn btn-warning btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-pencil fa-lg fa-fw"></i></span>Edit Lokasi Aktivitas</button>
        ';
   })
   ->make(true);
}

public function getChildUnit($id_forum,$id_bidang)
    {
        $getRenja = DB::SELECT('SELECT a.id_forum_rkpdprog, a.tahun_forum, a.id_bidang, a.id_unit,b.nm_unit
                    FROM trx_forum_skpd_program_ranwal a
                    INNER JOIN ref_unit b ON a.id_unit = b.id_unit
                    WHERE a.id_forum_rkpdprog='. $id_forum.' and a.id_bidang='.$id_bidang.' 
                    GROUP BY a.id_forum_rkpdprog, a.tahun_forum, a.id_bidang, a.id_unit,b.nm_unit
                    ORDER BY a.id_unit,b.nm_unit');

        return Datatables::of($getRenja)
            ->addColumn('action',function($getRenja){
                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench"></i></span>Aksi<span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnViewProgSkpd dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg"></i> Lihat Program SKPD</a>
                        </li>
                    </ul>
                    </div>
                    ' ;

            })
            ->make(true);
    }

public function getChildUsulan($id_lokasi)
    {
        $getRenja = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_sumber_usulan, a.sumber_usulan, a.id_lokasi_forum, a.id_ref_usulan, 
                    a.volume_1_usulan, a.volume_1_forum, a.volume_2_usulan, a.volume_2_forum, a.status_data,a.ket_usulan,a.uraian_usulan,
                    CASE a.status_data
                                    WHEN 0 THEN "fa fa-check fa-fw fa-lg"
                                    WHEN 1 THEN "fa fa-check-square-o fa-fw fa-lg"
                                    WHEN 2 THEN "fa fa-retweet fa-fw fa-lg"
                                    WHEN 3 THEN "fa fa-remove fa-fw fa-lg"
                                END AS status_icon,
                                CASE a.status_data
                                    WHEN 0 THEN "blue"
                                    WHEN 1 THEN "green"
                                    WHEN 2 THEN "yellow"
                                    WHEN 3 THEN "red"
                                END AS warna,
                                CASE a.sumber_usulan
                                    WHEN 0 THEN "Renja SKPD"
                                    WHEN 1 THEN "Musrenbang Desa"
                                    WHEN 2 THEN "Musrenbang Kecamatan"
                                    WHEN 3 THEN "Musrenbang Pokir Dewan"
                                    WHEN 4 THEN "Forum"
                                END AS display_sumber
                    FROM trx_forum_skpd_usulan a, (SELECT @id:=0) x 
                    WHERE a.id_lokasi_forum='. $id_lokasi);

        return Datatables::of($getRenja)
            ->addColumn('action',function($getRenja){
                return '
                    <button id="btnEditUsulan" type="button" title="Edit Data" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-lg fa-fw"></i></button>
                    <button id="btnPostUsulan" type="button" title="Posting"  class="btn btn-success btn-sm"><i class="fa fa-check-square-o fa-lg fa-fw"></i></button>
                    ';

            })
            ->make(true);
    }


public function getBelanja($id_aktivitas_pd){
   $getBelanja=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_forum,a.no_urut,a.id_belanja_pd,a.id_aktivitas_pd,
            a.id_zona_ssh,a.id_belanja_renja,a.sumber_belanja,a.id_aktivitas_asb,a.id_item_ssh,a.id_rekening_ssh,a.uraian_belanja,
            a.volume_1, a.id_satuan_1,a.volume_2,a.id_satuan_2,a.harga_satuan,a.jml_belanja, a.status_data,a.sumber_data,
            COALESCE(b.uraian_tarif_ssh,a.uraian_belanja) as uraian_tarif_ssh,c.uraian_satuan as satuan_1, d.uraian_satuan as satuan_2,a.volume_1_forum,a.id_satuan_1_forum,
            a.id_satuan_2_forum,a.volume_2_forum,a.harga_satuan_forum,a.jml_belanja_forum,g.uraian_satuan as satuan_1_forum,
            h.uraian_satuan as satuan_2_forum, e.kd_rekening, e.nm_rekening, f.nm_aktivitas_asb,
                        CASE a.status_data
                            WHEN 0 THEN "fa fa-question fa-fw fa-lg"
                            WHEN 1 THEN "fa fa-check-square-o fa-fw fa-lg"
                        END AS status_icon,
                        CASE a.status_data
                            WHEN 0 THEN "red"
                            WHEN 1 THEN "green"
                        END AS warna
            FROM trx_rkpd_rancangan_belanja_pd a
            LEFT OUTER JOIN ref_ssh_tarif b on a.id_item_ssh = b.id_tarif_ssh
            LEFT OUTER JOIN ref_satuan c on a.id_satuan_1 = c.id_satuan
            LEFT OUTER JOIN ref_satuan d on a.id_satuan_2 = d.id_satuan
            LEFT OUTER JOIN ref_satuan g on a.id_satuan_1_forum = g.id_satuan
            LEFT OUTER JOIN ref_satuan h on a.id_satuan_2_forum = h.id_satuan
            LEFT OUTER JOIN (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
                            a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
                            FROM ref_rek_5 a) e on a.id_rekening_ssh = e.id_rekening
            LEFT OUTER JOIN trx_asb_aktivitas f on a.id_aktivitas_asb = f.id_aktivitas_asb) a,
            (SELECT @id:=0) x WHERE a.id_aktivitas_pd='.$id_aktivitas_pd);

   return DataTables::of($getBelanja)
   ->addColumn('action', function ($getBelanja) {
        if($getBelanja->sumber_belanja==1)
            return '
                <a type="button" id="btnEditBelanja" class="btn btn-warning btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-pencil fa-lg fa-fw"></i></span>Edit Belanja</a>                         
                ';
        if($getBelanja->sumber_belanja==0)
            return '
                <a type="button" id="btnEditBelanja" class="btn btn-info btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-list-alt fa-lg fa-fw"></i></span>Lihat Belanja</a>                         
                ';
   })
   ->make(true);
}

public function getRenjaKeg()
    {
        $getRenja=DB::select('SELECT tahun_renja, no_urut, id_renja, id_renja_program, id_rkpd_renstra, id_rkpd_ranwal, id_unit, id_visi_renstra, id_misi_renstra, id_tujuan_renstra, id_sasaran_renstra, id_program_renstra, uraian_program_renstra, id_kegiatan_renstra, id_kegiatan_ref, uraian_kegiatan_renstra, pagu_tahun_renstra, pagu_tahun_kegiatan, pagu_tahun_selanjutnya, status_pelaksanaan_kegiatan, pagu_musrenbang, sumber_data, ket_usulan, status_data, status_rancangan
        FROM trx_renja_rancangan');

        return json_encode($getRenja);
    }

public function getLokasiCopy($id_aktivitas_forum){
   $getBelanja=DB::SELECT('SELECT (@id:=@id+1) as urut, a.* FROM trx_forum_skpd_aktivitas AS a,
            (SELECT @id:=0) x WHERE a.id_aktivitas_forum='.$id_aktivitas_forum.' AND a.sumber_aktivitas=1 AND a.tahun_forum='.Session::get('tahun'));

   return DataTables::of($getBelanja)
   ->addColumn('action', function ($getBelanja) {
        return '
            <a id="btnProsesCopyBelanja" type="button" class="edit-belanja btn btn-info btn-labeled">
            <span class="btn-label"><i class="fa fa-exchange fa-fw fa-lg"></i></span>Copy</a>                         
            ';
   })
   ->make(true);
}

public function getBelanjaCopy(Request $req){
   $getBelanja=DB::INSERT('INSERT INTO trx_forum_skpd_belanja (tahun_forum, no_urut, id_lokasi_forum, id_zona_ssh, id_belanja_renja, sumber_belanja, id_aktivitas_asb, id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, harga_satuan, jml_belanja, volume_1_forum, id_satuan_1_forum, volume_2_forum, id_satuan_2_forum, harga_satuan_forum, jml_belanja_forum, status_data, sumber_data)
        SELECT tahun_forum, no_urut, '.$req->id_aktivitas_new.', id_zona_ssh, id_belanja_renja, sumber_belanja, id_aktivitas_asb, id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, harga_satuan, jml_belanja, volume_1_forum, id_satuan_1_forum, volume_2_forum, id_satuan_2_forum, harga_satuan_forum, jml_belanja_forum, status_data, sumber_data FROM trx_forum_skpd_belanja where id_lokasi_forum ='.$req->id_aktivitas);
   if($getBelanja!=0) {
        return response ()->json (['pesan'=>'Data Berhasil Dicopy','status_pesan'=>'1']);
    } else {
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal DiCopy ('.$error_code.')','status_pesan'=>'0']);
    };
}

public function getHitungASB(Request $req){

    // if($req->jns_biaya==1){
    //     $getHitung=DB::INSERT('INSERT INTO trx_forum_skpd_belanja (tahun_forum, no_urut, id_lokasi_forum, id_zona_ssh, id_belanja_renja, sumber_belanja, id_aktivitas_asb, id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, harga_satuan, jml_belanja, volume_1_forum, id_satuan_1_forum, volume_2_forum, id_satuan_2_forum, harga_satuan_forum, jml_belanja_forum, status_data, sumber_data)
    //       VALUES('.$req->tahun_renja.',1,'.$req->id_lokasi_renja.',1,2,'.$req->id_aktivitas_asb.',0,0,"'.$req->nama_aktivitas.'",'.$req->volume_1.','.$req->id_satuan_1.','.$req->volume_2.','.$req->id_satuan_2.','.$req->pagu_rata2.','.$req->pagu_asb.',0)');
    //   } else {        
        $getHitung=DB::INSERT('INSERT INTO trx_rkpd_rancangan_belanja_pd (tahun_forum, no_urut, id_aktivitas_pd, id_belanja_forum, id_zona_ssh, id_belanja_renja, sumber_belanja, id_aktivitas_asb, 
            id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, harga_satuan, jml_belanja, volume_1_forum, id_satuan_1_forum, volume_2_forum, 
            id_satuan_2_forum, harga_satuan_forum, jml_belanja_forum, status_data, sumber_data)
            SELECT '.Session::get('tahun').', (@id:=@id+1) as no_urut,'.$req->id_aktivitas_pd.',0,1,0,0, a.id_aktivitas_asb, a.id_tarif_ssh, a.id_rekening, a.nm_aktivitas_asb,0,0,0,0,0,0,'.$req->volume_1.','.$req->id_satuan_1.','.$req->volume_2.','.$req->id_satuan_2.',a.harga_satuan,a.jml_pagu,0,1 FROM (
                SELECT a.id_aktivitas_asb, b.nm_aktivitas_asb,b.id_satuan_1,b.id_satuan_2,a.id_tarif_ssh, PaguASB(b.jenis_biaya,b.hub_driver,'.$req->volume_1.','.$req->volume_2.',b.r1,b.r2,b.km1,b.km2,b.kf1,b.kf2,b.kf3,a.harga_satuan) AS jml_pagu, a.harga_satuan, b.koef, b.id_rekening
                FROM trx_asb_perhitungan_rinci a
                INNER JOIN (SELECT a.id_komponen_asb_rinci,c.id_aktivitas_asb,c.nm_aktivitas_asb,b.id_komponen_asb,a.id_tarif_ssh,a.jenis_biaya,a.hub_driver,a.koefisien1 * a.koefisien2*a.koefisien3 as koef,
                c.range_max as r1, c.range_max1 as r2,c.id_satuan_1,c.sat_derivatif_1,c.id_satuan_2,c.sat_derivatif_2,
                case when COALESCE(c.sat_derivatif_1,0) < 1 then d.uraian_satuan else e.uraian_satuan end as sat_display_1,
                case when COALESCE(c.id_satuan_2,0) > 0 then 
                (case when COALESCE(c.sat_derivatif_2,0) < 1 then f.uraian_satuan else g.uraian_satuan end )
                    else "NA" end as sat_display_2, COALESCE(a.koefisien1,0) as kf1, COALESCE(a.koefisien2,0) as kf2, COALESCE(a.koefisien3,0) as kf3, 
                COALESCE(c.kapasitas_max,0) as km1,COALESCE(c.kapasitas_max1,0) as km2,b.id_rekening
                FROM trx_asb_komponen_rinci a
                INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
                INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
                INNER JOIN ref_satuan d ON c.id_satuan_1 = d.id_satuan
                LEFT OUTER JOIN ref_satuan e ON c.sat_derivatif_1 = e.id_satuan
                LEFT OUTER JOIN ref_satuan f ON c.id_satuan_2 = f.id_satuan
                LEFT OUTER JOIN ref_satuan g ON c.sat_derivatif_2 = g.id_satuan) b ON a.id_aktivitas_asb = b.id_aktivitas_asb and a.id_komponen_asb = b.id_komponen_asb and a.id_komponen_asb_rinci = b.id_komponen_asb_rinci
                 WHERE a.id_aktivitas_asb='.$req->id_aktivitas_asb.') a, (SELECT @id:=0) z');
      // }
    
    if($getHitung != 0){
        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); 
    } else {
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function unloadASB(Request $req){
    $getHitung=DB::DELETE('DELETE FROM trx_rkpd_rancangan_belanja_pd 
                WHERE id_aktivitas_asb='.$req->id_aktivitas_asb.' AND id_aktivitas_pd='.$req->id_aktivitas_pd);
    // return json_encode($getHitung);
    if($getHitung != 0){
        return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']); 
    } else {
        return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
    }

}

public function getPaguBelanja($id_tahun,$id_pelaksana_renja){
    $paguPelaksana=DB::select('SELECT a.id_pelaksana_renja, a.status_data, COALESCE(SUM(c.jml_belanja),0) as jml_pagu
            FROM trx_renja_rancangan_pelaksana a 
            LEFT OUTER JOIN trx_renja_rancangan_lokasi b ON a.id_pelaksana_renja = b.id_pelaksana_renja
            LEFT OUTER JOIN trx_renja_rancangan_belanja c ON b.id_lokasi_renja = c.id_lokasi_renja
            WHERE a.tahun_renja='.$id_tahun.' AND a.id_pelaksana_renja='.$id_pelaksana_renja.' AND a.status_data = 1  
            GROUP BY a.tahun_renja,a.id_aktivitas_renja,a.id_pelaksana_renja, a.status_data');
    return $paguPelaksana;

}

public function getPaguPelaksana($id_tahun,$id_aktivitas){
    $paguPelaksana=DB::select('SELECT COALESCE(SUM(c.jml_belanja),0) as jml_pagu
            FROM trx_renja_rancangan_pelaksana a 
            LEFT OUTER JOIN trx_renja_rancangan_lokasi b ON a.id_pelaksana_renja = b.id_pelaksana_renja
            LEFT OUTER JOIN trx_renja_rancangan_belanja c ON b.id_lokasi_renja = c.id_lokasi_renja
            WHERE a.tahun_renja='.$id_tahun.' AND a.id_aktivitas_renja='.$id_aktivitas.' AND a.status_data = 1  
            GROUP BY a.tahun_renja,a.id_aktivitas_renja');
    return $paguPelaksana;

}

public function getPaguAktivitas($id_tahun,$id_renja){
    $paguAktivitas=DB::select('SELECT a.tahun_renja, a.id_renja, COALESCE(COUNT(a.id_aktivitas_renja),0) as jml_aktivitas, 
            COALESCE(SUM(a.pagu_aktivitas),0) as jml_pagu, COALESCE(SUM(a.pagu_aktivitas*(a.pagu_musren/100)),0) as jml_musren
            FROM trx_renja_rancangan_aktivitas a
            WHERE a.status_data=1 AND a.tahun_renja='.$id_tahun.' AND a.id_renja='.$id_renja.' GROUP BY a.tahun_renja, a.id_renja, a.status_data');
    return $paguAktivitas;

} 

public function getBidang($id_unit,$id_ranwal){
        $urusan=DB::select('SELECT a.tahun_rkpd, a.id_rkpd_ranwal, a.id_bidang, d.nm_bidang, b.id_unit
            FROM trx_rkpd_ranwal_urusan a
            INNER JOIN trx_rkpd_ranwal_pelaksana b ON a.id_rkpd_ranwal=b.id_rkpd_ranwal AND a.id_urusan_rkpd = b.id_urusan_rkpd
            INNER JOIN ref_bidang d ON a.id_bidang = d.id_bidang
            WHERE b.id_unit='.$id_unit.' and a.id_rkpd_ranwal='.$id_ranwal);
        
        return json_encode($urusan);
}

public function AddProgRenja(Request $req){
    try{
        $data = new TrxRkpdRancanganProgramPd();
        $data->id_rkpd_rancangan= $req->id_rkpd_rancangan;
        $data->tahun_forum= Session::get('tahun');
        $data->jenis_belanja= $req->jenis_belanja;
        $data->no_urut= $req->no_urut;
        $data->id_unit= $req->id_unit; 
        $data->id_forum_program= 0;
        $data->id_renja_program= 0;
        $data->id_program_renstra= $req->id_program_renstra;
        $data->uraian_program_renstra= $req->uraian_program_renstra;
        $data->id_program_ref= $req->id_program_ref;
        $data->pagu_tahun_renstra= 0;
        $data->pagu_forum= $req->pagu_forum;
        $data->sumber_data= 1;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->ket_usulan= $req->ket_usulan;
        $data->status_data= 0;         
        $data->id_dokumen=0;
        $data->save (['timestamps' => false]);

    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function editProgRenja(Request $req){
    try{
        $data = TrxRkpdRancanganProgramPd::find($req->id_program_pd);
        // $data->tahun_forum= $req->tahun_forum;
        $data->jenis_belanja= $req->jenis_belanja;
        $data->no_urut= $req->no_urut;
        $data->id_unit= $req->id_unit;
        $data->id_renja_program= $req->id_renja_program;
        $data->id_program_renstra= $req->id_program_renstra;
        $data->uraian_program_renstra= $req->uraian_program_renstra;
        $data->id_program_ref= $req->id_program_ref;
        $data->pagu_tahun_renstra= $req->pagu_tahun_renstra;
        $data->pagu_forum= $req->pagu_forum;
        $data->sumber_data= $req->sumber_data;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->ket_usulan= $req->ket_usulan;
        $data->status_data= $req->status_data;
        $data->save (['timestamps' => false]);

    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }

}

public function hapusProgRenja(Request $req){
    TrxRkpdRancanganProgramPd::where('id_program_pd',$req->id_program_pd)->delete();
    return response ()->json (['pesan'=>'Data Berhasil dihapus']);

}

public function addKegRenja(Request $req){
    try{
        $data = new TrxRkpdRancanganKegiatanPd();
        $data->id_program_pd= $req->id_program_pd;
        $data->id_unit= $req->id_unit;
        $data->tahun_forum= Session::get('tahun');
        $data->no_urut= $req->no_urut;
        $data->id_renja= 0;
        $data->id_rkpd_renstra= $req->id_rkpd_renstra;
        $data->id_program_renstra= $req->id_program_renstra;
        $data->id_kegiatan_renstra= $req->id_kegiatan_renstra;
        $data->id_kegiatan_ref= $req->id_kegiatan_ref;
        $data->uraian_kegiatan_forum= $req->uraian_kegiatan_forum;
        $data->pagu_tahun_kegiatan= 0;
        $data->pagu_kegiatan_renstra= 0;
        $data->pagu_plus1_renja= 0;
        $data->pagu_plus1_forum= $req->pagu_plus1_forum;
        $data->pagu_forum= $req->pagu_forum;
        $data->keterangan_status= $req->keterangan_status;
        $data->status_data= 0;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->sumber_data=1;
        $data->save (['timestamps' => false]);

    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function editKegRenja(Request $req){
    try{
        $data = TrxRkpdRancanganKegiatanPd::find($req->id_kegiatan_pd);
        // $data->id_forum_program= $req->id_forum_program;
        // $data->id_unit= $req->id_unit;
        // $data->tahun_forum= $req->tahun_forum;
        $data->no_urut= $req->no_urut;
        // $data->id_renja= $req->id_renja;
        $data->id_rkpd_renstra= $req->id_rkpd_renstra;
        $data->id_program_renstra= $req->id_program_renstra;
        $data->id_kegiatan_renstra= $req->id_kegiatan_renstra;
        $data->id_kegiatan_ref= $req->id_kegiatan_ref;
        $data->uraian_kegiatan_forum= $req->uraian_kegiatan_forum;
        // $data->pagu_tahun_kegiatan= $req->pagu_tahun_kegiatan;
        // $data->pagu_kegiatan_renstra= $req->pagu_kegiatan_renstra;        
        // $data->pagu_plus1_renja= $req->pagu_plus1_renja;
        $data->pagu_plus1_forum= $req->pagu_plus1_forum;
        $data->pagu_forum= $req->pagu_forum;
        $data->keterangan_status= $req->keterangan_status;
        $data->status_data= $req->status_data;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->save (['timestamps' => false]);

    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }

}

public function postKegRenja(Request $req)
    {
        $data = TrxRkpdRancanganKegiatanPd::find($req->id_kegiatan_pd);
        $data->status_data= $req->status_data;

        $cek = DB::SELECT('SELECT c.id_kegiatan_pd, c.status_data, c.pagu_forum, COALESCE(b.jml_pagu_aktivitas,0) as jml_pagu_aktivitas, COALESCE(c.pagu_forum,0) - COALESCE(b.jml_pagu_aktivitas,0) AS selisih 
            FROM (SELECT a.tahun_forum, b.id_kegiatan_pd,
                        SUM(a.pagu_aktivitas_forum) AS jml_pagu_aktivitas
                        FROM trx_rkpd_rancangan_aktivitas_pd a 
                        INNER JOIN trx_rkpd_rancangan_pelaksana_pd b ON a.id_pelaksana_pd = b.id_pelaksana_pd
                        WHERE a.status_data = 1
                        GROUP BY a.tahun_forum, b.id_kegiatan_pd, a.status_data) b
            INNER JOIN trx_rkpd_rancangan_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            WHERE c.id_kegiatan_pd='.$req->id_kegiatan_pd);

        if($cek[0]->selisih == 0){
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Diposting','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Diposting ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
             return response ()->json (['pesan'=>'Data Jumlah Pagu Kegiatan dengan Aktivitas Tidak Sama','status_pesan'=>'0']);
        }
    }

public function hapusKegRenja(Request $req){
    $result = TrxRkpdRancanganKegiatanPd::where('id_kegiatan_pd',$req->id_kegiatan_pd)->delete ();
    
    if($result != 0){
        return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
    } else {
        return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
    }
    

}

public function addAktivitas(Request $req)
    {
            $data = new TrxRkpdRancanganAktivitasPd;
            $data->id_pelaksana_pd= $req->id_pelaksana_pd;
            $data->tahun_forum= Session::get('tahun');
            $data->no_urut= $req->no_urut;
            $data->sumber_aktivitas= $req->sumber_aktivitas;
            $data->id_aktivitas_asb= $req->id_aktivitas_asb;
            $data->id_aktivitas_renja= $req->id_aktivitas_renja;
            $data->uraian_aktivitas_kegiatan= $req->uraian_aktivitas_kegiatan;
            $data->volume_aktivitas_1= 0;
            $data->id_satuan_1= $req->id_satuan_1;
            $data->volume_aktivitas_2= 0;
            $data->id_satuan_2= $req->id_satuan_2;
            $data->id_program_nasional= 0;
            $data->id_program_provinsi= 0;
            $data->jenis_kegiatan= $req->jenis_kegiatan;
            $data->sumber_dana= $req->sumber_dana;
            $data->id_satuan_publik= $req->id_satuan_publik;
            $data->pagu_aktivitas_renja= $req->pagu_aktivitas_renja;
            $data->pagu_aktivitas_forum= $req->pagu_aktivitas_forum;
            $data->pagu_musren= $req->pagu_musren;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->keterangan_aktivitas= $req->keterangan_aktivitas;
            $data->status_musren= 0;
            $data->sumber_data= 1;
        
            $cek = DB::SELECT('SELECT d.status_data AS status_program,
            CASE WHEN b.status_data=0 AND c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_rkpd_rancangan_pelaksana_pd b
            INNER JOIN trx_rkpd_rancangan_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            INNER JOIN trx_rkpd_rancangan_program_pd d ON c.id_program_pd = d.id_program_pd
            WHERE b.id_pelaksana_pd='.$req->id_pelaksana_pd);

        if($cek[0]->status_data==0){
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Kegiatan telah diposting, Data tidak dapat diubah','status_pesan'=>'0']);
        }
    }

public function editAktivitas(Request $req)
    {
        $data = TrxRkpdRancanganAktivitasPd::find($req->id_aktivitas_pd);
            $data->no_urut= $req->no_urut;
            $data->sumber_aktivitas= $req->sumber_aktivitas;
            $data->id_aktivitas_asb= $req->id_aktivitas_asb;
            $data->id_aktivitas_renja= $req->id_aktivitas_renja;
            $data->uraian_aktivitas_kegiatan= $req->uraian_aktivitas_kegiatan;
            $data->volume_aktivitas_1= 0;
            $data->id_satuan_1= $req->id_satuan_1;
            $data->volume_aktivitas_2= 0;
            $data->id_satuan_2= $req->id_satuan_2;
            $data->id_program_nasional= 0;
            $data->id_program_provinsi= 0;
            $data->jenis_kegiatan= $req->jenis_kegiatan;
            $data->sumber_dana= $req->sumber_dana;
            $data->id_satuan_publik= $req->id_satuan_publik;
            $data->pagu_aktivitas_renja= $req->pagu_aktivitas_renja;
            $data->pagu_aktivitas_forum= $req->pagu_aktivitas_forum;
            $data->pagu_musren= $req->pagu_musren;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->keterangan_aktivitas= $req->keterangan_aktivitas;
            $data->status_musren= $req->status_musren;

        $cek = DB::SELECT('SELECT a.status_data AS status_aktivitas, c.status_data AS status_kegiatan, d.status_data AS status_program,
            CASE WHEN a.status_data=0 AND c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_rkpd_rancangan_aktivitas_pd a
            INNER JOIN trx_rkpd_rancangan_pelaksana_pd b ON a.id_pelaksana_pd = b.id_pelaksana_pd
            INNER JOIN trx_rkpd_rancangan_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            INNER JOIN trx_rkpd_rancangan_program_pd d ON c.id_program_pd = d.id_program_pd
            WHERE a.id_aktivitas_pd='.$req->id_aktivitas_pd);

        if($cek[0]->status_data==0){
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
        } else {
            return response ()->json (['pesan'=>'Data Aktivitas telah diposting, Data tidak dapat diubah','status_pesan'=>'0']);
        }
    }

public function postAktivitas(Request $req)
    {
        $data = TrxRkpdRancanganAktivitasPd::find($req->id_aktivitas_pd);
        $data->status_data= $req->status_data;

        $cek = DB::SELECT('SELECT a.tahun_forum,a.id_aktivitas_pd, sum(a.pagu_aktivitas_forum) as pagu_aktivitas, COALESCE(sum(e.jml_belanja),0) as pagu_belanja, 
            sum(a.pagu_aktivitas_forum)-COALESCE(sum(e.jml_belanja),0) as selisih
            FROM trx_rkpd_rancangan_aktivitas_pd a
            LEFT OUTER JOIN (SELECT a.tahun_forum, a.id_aktivitas_pd, Sum(a.jml_belanja_forum) as jml_belanja
            FROM trx_rkpd_rancangan_belanja_pd AS a GROUP BY a.tahun_forum,a.id_aktivitas_pd) e ON a.id_aktivitas_pd = e.id_aktivitas_pd
            WHERE a.id_aktivitas_pd='.$req->id_aktivitas_pd.' GROUP BY a.tahun_forum,a.id_aktivitas_pd');
        
        if($req->status_data == 1) {
                if($cek[0]->selisih == 0){
                    try{
                        $data->save (['timestamps' => false]);
                        return response ()->json (['pesan'=>'Data Berhasil Diposting','status_pesan'=>'1']);
                    }
                    catch(QueryException $e){
                        $error_code = $e->errorInfo[1] ;
                        return response ()->json (['pesan'=>'Data Gagal Diposting ('.$error_code.')','status_pesan'=>'0']);
                    }
                } else {
                     return response ()->json (['pesan'=>'Data Jumlah Pagu Aktivitas dengan Uraian Belanja Tidak Sama','status_pesan'=>'0']);
                }
        } else {
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Diposting','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Diposting ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }

public function hapusAktivitas(Request $req)
      {
        $cek = DB::SELECT('SELECT a.status_data AS status_aktivitas, c.status_data AS status_kegiatan, d.status_data AS status_program,
            CASE WHEN a.status_data=0 AND c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_rkpd_rancangan_aktivitas_pd a
            INNER JOIN trx_rkpd_rancangan_pelaksana_pd b ON a.id_pelaksana_pd = b.id_pelaksana_pd
            INNER JOIN trx_rkpd_rancangan_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            INNER JOIN trx_rkpd_rancangan_program_pd d ON c.id_program_pd = d.id_program_pd
            WHERE a.id_aktivitas_pd='.$req->id_aktivitas_pd);

        if($cek[0]->status_data==0){
            TrxRkpdRancanganAktivitasPd::where('id_aktivitas_pd',$req->id_aktivitas_pd)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
        } else {
             return response ()->json (['pesan'=>'Data Aktivitas telah diposting, Data tidak dapat dihapus','status_pesan'=>'0']);
        }
      }

public function addPelaksana(Request $req)
    {
        
            $data = new TrxRkpdRancanganPelaksanaPd;
            $data->tahun_forum= Session::get('tahun');
            $data->no_urut= $req->no_urut;
            $data->id_kegiatan_pd= $req->id_kegiatan_pd;
            $data->id_sub_unit= $req->id_sub_unit;
            $data->id_lokasi= $req->id_lokasi;
            $data->sumber_data= 1;
            $data->ket_pelaksana= $req->ket_pelaksana;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->status_data= $req->status_data;
        
        $cek = DB::SELECT('SELECT c.status_data AS status_kegiatan, d.status_data AS status_program,
            CASE WHEN c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_rkpd_rancangan_kegiatan_pd c
            INNER JOIN trx_rkpd_rancangan_program_pd d ON c.id_program_pd = d.id_program_pd
            WHERE c.id_kegiatan_pd='.$req->id_kegiatan_pd);

        if($cek[0]->status_data==0){
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
        } else {
             return response ()->json (['pesan'=>'Data Kegiatan telah diposting, Data tidak dapat dihapus','status_pesan'=>'0']);
        }
    }

public function editPelaksana(Request $req)
    {
        
            $data = TrxRkpdRancanganPelaksanaPd::find($req->id_pelaksana_pd);
            $data->no_urut= $req->no_urut;
            $data->id_sub_unit= $req->id_sub_unit;
            $data->id_lokasi= $req->id_lokasi;
            $data->ket_pelaksana= $req->ket_pelaksana;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->status_data= $req->status_data;

        $cek = DB::SELECT('SELECT c.status_data AS status_kegiatan, d.status_data AS status_program,
            CASE WHEN c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_rkpd_rancangan_kegiatan_pd c
            INNER JOIN trx_rkpd_rancangan_program_pd d ON c.id_program_pd = d.id_program_pd
            WHERE c.id_kegiatan_pd='.$req->id_kegiatan_pd);
        
        if($cek[0]->status_data==0){
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
        } else {
             return response ()->json (['pesan'=>'Data Kegiatan telah diposting, Data tidak dapat dihapus','status_pesan'=>'0']);
        }
    }
public function hapusPelaksana(Request $req)
      {
        $cek = DB::SELECT('SELECT d.status_data AS status_program,
            CASE WHEN b.status_data=0 AND c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_rkpd_rancangan_pelaksana_pd b
            INNER JOIN trx_rkpd_rancangan_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            INNER JOIN trx_rkpd_rancangan_program_pd d ON c.id_program_pd = d.id_program_pd
            WHERE b.id_pelaksana_pd='.$req->id_pelaksana_pd);

        if($cek[0]->status_data==0){
            TrxRkpdRancanganPelaksanaPd::where('id_pelaksana_pd',$req->id_pelaksana_pd)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
        } else {
            return response ()->json (['pesan'=>'Data Kegiatan telah diposting, Data tidak dapat dihapus','status_pesan'=>'0']);
        }
      }

public function addLokasi(Request $req)
    {
        try{
            $data = new TrxRkpdRancanganLokasiPd;
            $data->tahun_forum = Session::get('tahun');
            $data->no_urut = $req->no_urut ;
            $data->id_aktivitas_pd = $req->id_aktivitas_pd ;
            $data->jenis_lokasi = $req->jenis_lokasi ;
            $data->id_lokasi = $req->id_lokasi ;
            $data->id_lokasi_teknis = $req->id_lokasi_teknis ;
            $data->id_lokasi_renja = 0 ;
            $data->volume_1 = $req->volume_1 ;
            $data->volume_2 = $req->volume_2 ;
            $data->volume_usulan_1 = 0 ;
            $data->volume_usulan_2 = 0 ;
            $data->id_satuan_1 = $req->id_satuan_1 ;
            $data->id_satuan_2 = $req->id_satuan_2 ;
            $data->uraian_lokasi = $req->uraian_lokasi ;
            $data->ket_lokasi= $req->ket_lokasi;
            $data->status_data= 0;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->sumber_data= 5;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

public function editLokasi(Request $req)
    {
        try{
            $data = TrxRkpdRancanganLokasiPd::find($req->id_lokasi_pd);
            $data->no_urut = $req->no_urut ;
            $data->jenis_lokasi = $req->jenis_lokasi ;
            $data->id_lokasi = $req->id_lokasi ;
            $data->id_lokasi_teknis = $req->id_lokasi_teknis ;            
            $data->volume_1 = $req->volume_1 ;
            $data->volume_2 = $req->volume_2 ;
            $data->id_satuan_1 = $req->id_satuan_1 ;
            $data->id_satuan_2 = $req->id_satuan_2 ;
            $data->uraian_lokasi = $req->uraian_lokasi ;
            $data->ket_lokasi= $req->ket_lokasi;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->status_data= $req->status_data;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

public function hapusLokasi(Request $req)
      {
        TrxRkpdRancanganLokasiPd::where('id_lokasi_pd',$req->id_lokasi_pd)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

public function addUsulan(Request $req)
    {
        try{
            $data = new TrxRkpdRancanganKegiatanPdUsulan;
            $data->sumber_usulan= $req->sumber_usulan;
            $data->id_lokasi_forum= $req->id_lokasi_forum;
            $data->id_ref_usulan= $req->id_ref_usulan;
            $data->volume_1_usulan= $req->volume_1_usulan;
            $data->volume_1_forum= $req->volume_1_forum;
            $data->volume_2_usulan= $req->volume_2_usulan;
            $data->volume_2_forum= $req->volume_2_forum;
            $data->ket_usulan= $req->ket_usulan;            
            $data->uraian_usulan= $req->uraian_usulan;
            $data->status_data= $req->status_data;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

public function editUsulan(Request $req)
    {
        try{
            $data = TrxRkpdRancanganKegiatanPdUsulan::find($req->id_sumber_usulan);
            $data->sumber_usulan= $req->sumber_usulan;
            $data->id_lokasi_forum= $req->id_lokasi_forum;
            $data->id_ref_usulan= $req->id_ref_usulan;
            $data->volume_1_usulan= $req->volume_1_usulan;
            $data->volume_1_forum= $req->volume_1_forum;
            $data->volume_2_usulan= $req->volume_2_usulan;
            $data->volume_2_forum= $req->volume_2_forum;
            $data->ket_usulan= $req->ket_usulan;            
            $data->uraian_usulan= $req->uraian_usulan;
            $data->status_data= $req->status_data;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }
public function hapusUsulan(Request $req)
      {
        TrxRkpdRancanganKegiatanPdUsulan::where('id_sumber_usulan',$req->id_sumber_usulan)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

    public function addBelanja(Request $req)
    {
        
            $data = new TrxRkpdRancanganBelanjaPd;
            $data->tahun_forum= Session::get('tahun');
            $data->no_urut= $req->no_urut;
            $data->id_aktivitas_pd= $req->id_aktivitas_pd;
            $data->id_zona_ssh= $req->id_zona_ssh;
            $data->sumber_belanja= $req->sumber_belanja;
            $data->id_aktivitas_asb= 0;
            $data->id_item_ssh= $req->id_item_ssh;
            $data->id_rekening_ssh= $req->id_rekening_ssh;
            $data->uraian_belanja= $req->uraian_belanja;
            $data->volume_1= 0;
            $data->id_satuan_1= 0;
            $data->volume_2= 0;
            $data->id_satuan_2= 0;
            $data->harga_satuan= 0;
            $data->jml_belanja= 0;
            $data->volume_1_forum= $req->volume_1_forum;
            $data->id_satuan_1_forum= $req->id_satuan_1_forum;
            $data->volume_2_forum= $req->volume_2_forum;
            $data->id_satuan_2_forum= $req->id_satuan_2_forum;
            $data->harga_satuan_forum= $req->harga_satuan_forum;
            $data->jml_belanja_forum= $req->jml_belanja_forum;
            $data->sumber_data= 4;
            $data->status_data= 0;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

public function editBelanja(Request $req)
    {
        try{
            $data = TrxRkpdRancanganBelanjaPd::find($req->id_belanja_pd);
            $data->no_urut= $req->no_urut;
            $data->id_zona_ssh= $req->id_zona_ssh;
            $data->id_aktivitas_asb= $req->id_aktivitas_asb;
            $data->id_item_ssh= $req->id_item_ssh;
            $data->id_rekening_ssh= $req->id_rekening_ssh;
            $data->uraian_belanja= $req->uraian_belanja;
            $data->volume_1_forum= $req->volume_1_forum;
            $data->id_satuan_1_forum= $req->id_satuan_1_forum;
            $data->volume_2_forum= $req->volume_2_forum;
            $data->id_satuan_2_forum= $req->id_satuan_2_forum;
            $data->harga_satuan_forum= $req->harga_satuan_forum;
            $data->jml_belanja_forum= $req->jml_belanja_forum;
            $data->status_data= 0;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

public function hapusBelanja(Request $req)
      {
        TrxRkpdRancanganBelanjaPd::where('id_belanja_pd',$req->id_belanja_pd)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

public function addIndikatorProg(Request $req)
    {
            $data = new TrxRkpdRancanganProgIndikatorPd;
            $data->tahun_renja = $req->tahun_renja;
            $data->no_urut = $req->no_urut ;
            $data->id_program_pd = $req->id_program_pd ;
            $data->id_perubahan = 0 ;
            $data->kd_indikator = $req->kd_indikator ;
            $data->uraian_indikator_program =  $req->uraian_indikator_program ;
            $data->tolok_ukur_indikator = $req->tolok_ukur_indikator ;
            $data->target_renstra = 0 ;
            $data->target_renja =  $req->target_renja ;
            $data->id_satuan_ouput =  $req->id_satuan_output ;
            $data->status_data= 0;
            $data->sumber_data= 1;

        $cek = DB::SELECT('SELECT * from trx_rkpd_rancangan_program_pd WHERE id_program_pd='.$req->id_program_pd);
        if($cek[0]->status_data == 0){            
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
             return response ()->json (['pesan'=>'Data Gagal Status Program telah diposting','status_pesan'=>'0']);
        }
    }

public function editIndikatorProg(Request $req)
    {
            $data = TrxRkpdRancanganProgIndikatorPd::find($req->id_indikator_program);
            $data->no_urut = $req->no_urut ;
            // $data->id_program_pd = $req->id_program_pd ;
            $data->kd_indikator = $req->kd_indikator ;
            $data->uraian_indikator_program =  $req->uraian_indikator_program ;
            $data->tolok_ukur_indikator = $req->tolok_ukur_indikator ;
            $data->target_renja =  $req->target_renja ;
            $data->id_satuan_ouput =  $req->id_satuan_output ;

        $cek = DB::SELECT('SELECT * from trx_rkpd_rancangan_program_pd WHERE id_program_pd='.$req->id_program_pd);
        if($cek[0]->status_data == 0){            
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
             return response ()->json (['pesan'=>'Data Gagal Status Program telah diposting','status_pesan'=>'0']);
        }
    }

public function postIndikatorProg(Request $req)
    {
        $data = TrxRkpdRancanganProgIndikatorPd::find($req->id_indikator_program);
        $data->status_data = $req->status_data;
        
        $cek = DB::SELECT('SELECT a.status_data from trx_rkpd_rancangan_program_pd a
            INNER JOIN trx_rkpd_rancangan_prog_indikator_pd b ON a.id_program_pd = b.id_program_pd
            WHERE b.id_indikator_program='.$req->id_indikator_program.' GROUP BY a.status_data, b.id_indikator_program');

        if($cek[0]->status_data == 0){            
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
             return response ()->json (['pesan'=>'Data Gagal Status Program telah diposting','status_pesan'=>'0']);
        }
    }

public function delIndikatorProg(Request $req)
    {
        
        $cek = DB::SELECT('SELECT a.status_data from trx_rkpd_rancangan_program_pd a
            INNER JOIN trx_rkpd_rancangan_prog_indikator_pd b ON a.id_program_pd = b.id_program_pd
            WHERE b.id_indikator_program='.$req->id_indikator_program.' GROUP BY a.status_data, b.id_indikator_program');
            
        if($cek[0]->status_data == 0){            
            TrxRkpdRancanganProgIndikatorPd::where('id_indikator_program',$req->id_indikator_program)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1'] );
        } else {
            return response ()->json (['pesan'=>'Data Gagal Status Program telah diposting','status_pesan'=>'0']);
        }
    }

public function addIndikatorKeg(Request $req)
    {
            $data = new TrxRkpdRancanganKegIndikatorPd;
            $data->tahun_renja = $req->tahun_renja;
            $data->no_urut = $req->no_urut ;
            $data->id_kegiatan_pd = $req->id_kegiatan_pd ;
            $data->id_perubahan = 0 ;
            $data->kd_indikator = $req->kd_indikator ;
            $data->uraian_indikator_kegiatan =  $req->uraian_indikator_kegiatan ;
            $data->tolok_ukur_indikator = $req->tolok_ukur_indikator ;
            $data->target_renstra = 0 ;
            $data->target_renja =  $req->target_renja ;
            $data->id_satuan_ouput =  $req->id_satuan_ouput ;
            $data->status_data= 0;
            $data->sumber_data= 1;
        
        $cek = DB::SELECT('SELECT * FROM trx_rkpd_rancangan_kegiatan_pd WHERE id_kegiatan_pd = '.$req->id_kegiatan_pd);
        if($cek[0]->status_data == 0){
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Gagal Status Kegiatan telah diposting','status_pesan'=>'0']);
        }

        
    }

public function editIndikatorKeg(Request $req)
    {
        $cek = DB::SELECT('SELECT * FROM trx_rkpd_rancangan_kegiatan_pd WHERE id_kegiatan_pd = '.$req->id_kegiatan_pd);

            $data = TrxRkpdRancanganKegIndikatorPd::find($req->id_indikator_kegiatan);
            $data->no_urut = $req->no_urut ;
            $data->kd_indikator = $req->kd_indikator ;
            $data->uraian_indikator_kegiatan =  $req->uraian_indikator_kegiatan ;
            $data->tolok_ukur_indikator = $req->tolok_ukur_indikator ;
            $data->target_renja =  $req->target_renja ;
            $data->id_satuan_ouput =  $req->id_satuan_output ;

        if($cek[0]->status_data == 0){
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Gagal Status Kegiatan telah diposting','status_pesan'=>'0']);
        }
    }

public function postIndikatorKeg(Request $req)
    {
        $cek = DB::SELECT('SELECT a.status_data FROM trx_rkpd_rancangan_kegiatan_pd a
            INNER JOIN trx_rkpd_rancangan_keg_indikator_pd b ON a.id_kegiatan_pd = b.id_kegiatan_pd
            WHERE b.id_indikator_kegiatan = '.$req->id_indikator_kegiatan.' group by a.status_data,b.id_indikator_kegiatan');
                   
            $data = TrxRkpdRancanganKegIndikatorPd::find($req->id_indikator_kegiatan);
            $data->status_data = $req->status_data;

        if($cek[0]->status_data == 0){ 
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Gagal Status Kegiatan telah diposting','status_pesan'=>'0']);
        }
    }

public function delIndikatorKeg(Request $req)
    {
        $cek = DB::SELECT('SELECT a.status_data FROM trx_rkpd_rancangan_kegiatan_pd a
            INNER JOIN trx_rkpd_rancangan_keg_indikator_pd b ON a.id_kegiatan_pd = b.id_kegiatan_pd
            WHERE b.id_indikator_kegiatan = '.$req->id_indikator_kegiatan.' group by a.status_data,b.id_indikator_kegiatan');

        if($cek[0]->status_data == 0){
            TrxRkpdRancanganKegIndikatorPd::where('id_indikator_kegiatan',$req->id_indikator_kegiatan)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1'] );
        } else {
            return response ()->json (['pesan'=>'Data Gagal Status Kegiatan telah diposting','status_pesan'=>'0']);
        }
    }
    
}
