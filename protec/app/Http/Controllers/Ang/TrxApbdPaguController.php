<?php

namespace App\Http\Controllers\Ang;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\QueryException;
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
use App\Models\Ang\TrxAnggaranAktivitasPd;
use App\Models\Ang\TrxAnggaranLokasiPd;
use App\Models\Ang\TrxAnggaranBelanjaPd;


class TrxApbdPaguController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getData()
    {
        $dataForum = TrxAnggaranKegiatanPd::paginate(15);
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

    public function getSelectProgram($id_unit,$tahun_rkpd){
        $getSelect=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, a.* FROM 
                (SELECT a.tahun_renja, a.id_rkpd_ranwal, c.uraian_program_rpjmd
                FROM trx_renja_rancangan_program AS a
                INNER JOIN trx_rkpd_ranwal as c ON a.id_rkpd_ranwal =  c.id_rkpd_ranwal
				LEFT OUTER JOIN trx_forum_skpd_program as d ON a.id_renja_program = d.id_renja_program
				WHERE d.id_renja_program is null AND a.status_data = 2 and (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3 and a.id_unit='.$id_unit.' AND a.tahun_renja='.$tahun_rkpd.')) a, 
                (SELECT @id:=0) x GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.uraian_program_rpjmd');
        return DataTables::of($getSelect)
        ->addColumn('action',function($getSelect){
          return '
              <button id="btnReLoad" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="" title="" class="btn btn-primary">
              <i class="fa fa-download fa-fw fa-lg"></i> Load Data</button>
          ' ;})
        ->make(true);
    }

    public function getProgramRkpd(Request $req)
    {
        $getRenja=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_anggaran_pemda, a.id_rkpd_ranwal, a.tahun_anggaran, a.id_program_rpjmd, 
            b.id_unit, a.uraian_program_rpjmd, a.pagu_rkpd, a.pagu_keuangan, a.keterangan_program, a.jenis_belanja,
            a.status_data, a.sumber_data, c.jns_dokumen_keu, c.kd_dokumen_keu, c.id_perubahan,
            SUM(COALESCE((SELECT COUNT(x.id_program_pd) AS jml_program 
            FROM trx_anggaran_program_pd AS x 
            WHERE x.id_pelaksana_anggaran = b.id_pelaksana_anggaran AND x.id_unit = b.id_unit AND x.tahun_anggaran = a.tahun_anggaran
            GROUP BY x.id_pelaksana_anggaran, x.tahun_anggaran, x.id_unit ),0)) as jml_program, 
            SUM(COALESCE((SELECT COUNT(y.id_kegiatan_pd) AS jml_kegiatan 
            FROM trx_anggaran_program_pd AS x 
            INNER JOIN trx_anggaran_kegiatan_pd AS y ON x.id_program_pd = y.id_program_pd
            WHERE x.id_pelaksana_anggaran = b.id_pelaksana_anggaran AND x.id_unit = b.id_unit AND x.tahun_anggaran = a.tahun_anggaran
            GROUP BY x.id_pelaksana_anggaran, x.tahun_anggaran, x.id_unit ),0)) as jml_kegiatan,
            SUM(COALESCE((SELECT SUM(y.pagu_forum) AS jml_pagu 
            FROM trx_anggaran_program_pd AS x 
            INNER JOIN trx_anggaran_kegiatan_pd AS y ON x.id_program_pd = y.id_program_pd
            WHERE x.id_pelaksana_anggaran = b.id_pelaksana_anggaran AND x.id_unit = b.id_unit AND x.tahun_anggaran = a.tahun_anggaran
            GROUP BY x.id_pelaksana_anggaran, x.tahun_anggaran, x.id_unit),0)) as jml_pagu,
            SUM(COALESCE((SELECT SUM(r.pagu_anggaran) AS jml_pagu_aktivitas 
            FROM trx_anggaran_program_pd AS x 
            INNER JOIN trx_anggaran_kegiatan_pd AS y ON x.id_program_pd = y.id_program_pd
            INNER JOIN trx_anggaran_pelaksana_pd AS z ON y.id_kegiatan_pd = z.id_kegiatan_pd
            INNER JOIN trx_anggaran_aktivitas_pd AS r ON z.id_pelaksana_pd = r.id_pelaksana_pd
            WHERE x.id_pelaksana_anggaran = b.id_pelaksana_anggaran AND x.id_unit = b.id_unit AND x.tahun_anggaran = a.tahun_anggaran
            GROUP BY x.id_pelaksana_anggaran, x.tahun_anggaran, x.id_unit),0)) as jml_pagu_aktivitas, 
            SUM(COALESCE((SELECT COUNT(r.id_aktivitas_pd) AS jml_aktivitas 
            FROM trx_anggaran_program_pd AS x 
            INNER JOIN trx_anggaran_kegiatan_pd AS y ON x.id_program_pd = y.id_program_pd
            INNER JOIN trx_anggaran_pelaksana_pd AS z ON y.id_kegiatan_pd = z.id_kegiatan_pd
            INNER JOIN trx_anggaran_aktivitas_pd AS r ON z.id_pelaksana_pd = r.id_pelaksana_pd
            WHERE x.id_pelaksana_anggaran = b.id_pelaksana_anggaran AND x.id_unit = b.id_unit AND x.tahun_anggaran = a.tahun_anggaran
            GROUP BY x.id_pelaksana_anggaran, x.tahun_anggaran, x.id_unit),0)) as jml_aktivitas,
            CASE a.status_data
                WHEN 0 THEN "fa fa-question"
                WHEN 1 THEN "fa fa-check-square-o"
            END AS status_icon,
            CASE a.status_data
                WHEN 0 THEN "red"
                WHEN 1 THEN "green"
            END AS warna 
            FROM trx_anggaran_program AS a
            INNER JOIN trx_anggaran_urusan AS d ON a.id_anggaran_pemda = d.id_anggaran_pemda
            INNER JOIN trx_anggaran_pelaksana AS b ON d.id_urusan_anggaran = b.id_urusan_anggaran
			INNER JOIN trx_anggaran_dokumen AS c ON a.id_dokumen_keu = c.id_dokumen_keu, 
            (SELECT @id:=0) AS p WHERE a.tahun_anggaran = '.$req->tahun.' AND b.id_unit ='.$req->unit.' AND a.id_dokumen_keu ='.$req->dokumen.'
            GROUP BY a.id_anggaran_pemda, a.id_rkpd_ranwal, a.tahun_anggaran, a.id_program_rpjmd, a.id_dokumen_keu,
            b.id_unit, a.uraian_program_rpjmd, a.pagu_rkpd, a.pagu_keuangan, a.keterangan_program, a.jenis_belanja,
            a.status_data, a.sumber_data, c.jns_dokumen_keu, c.kd_dokumen_keu, c.id_perubahan');

          return DataTables::of($getRenja)

            ->addColumn('details_url', function($getRenja) {
                    return url('Apbd/getChildBidang/'.$getRenja->id_unit.'/'. $getRenja->id_anggaran_pemda);
                })
            ->make(true);
    }

    public function getChildBidang($id_unit,$id_anggaran_pemda)
    {
        $getRenja = DB::SELECT('SELECT DISTINCT '.Session::get('tahun').' AS tahun_anggaran, b.id_anggaran_pemda,d.kd_bidang,d.nm_bidang,c.id_unit,
                c.id_pelaksana_anggaran, a.uraian_program_rpjmd, b.id_bidang,c.hak_akses, e.jns_dokumen_keu, e.kd_dokumen_keu, e.id_perubahan,
                CONCAT(RIGHT(CONCAT("0",d.kd_urusan),2),".",RIGHT(CONCAT("0",d.kd_bidang),2)) AS kode_bid  
                FROM trx_anggaran_program a
                INNER JOIN trx_anggaran_urusan b ON a.id_anggaran_pemda = b.id_anggaran_pemda
                INNER JOIN trx_anggaran_pelaksana c ON b.id_urusan_anggaran = c.id_urusan_anggaran
                INNER JOIN ref_bidang d ON b.id_bidang = d.id_bidang
                INNER JOIN trx_anggaran_dokumen AS e ON a.id_dokumen_keu = e.id_dokumen_keu
                WHERE c.id_unit='. $id_unit.' AND b.id_anggaran_pemda='.$id_anggaran_pemda.'
                ORDER BY b.id_anggaran_pemda,d.kd_bidang,c.id_unit, b.id_bidang');

        return Datatables::of($getRenja)
            ->addColumn('action',function($getRenja){
                return '
                    <button type="button" class="btnViewProgSkpd btn btn-primary btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-list-alt fa-fw fa-lg"></i></span>Lihat Program SKPD</button>
                    ';

            })
            ->make(true);
    }

    public function getProgramRenja($id_unit)
    {
      $getProgramRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.id_program_ref, a.uraian_program_renstra, b.id_sub_unit, 
                        CONCAT(d.kd_urusan,".",d.kd_bidang,".",c.kd_program) as kd_program_ref,a.id_dokumen_keu, SUM(b.jml_pendapatan) AS jml_pendapatan ,
                        SUM(b.jml_belanja) AS jml_belanja, SUM((b.jml_pembiayaan_terima-b.jml_pembiayaan_keluar)) AS pembiayaan_netto
                        FROM trx_anggaran_program_pd AS a
                        INNER JOIN (SELECT x.id_program_pd, x.id_sub_unit , SUM(x.jml_pendapatan) AS jml_pendapatan, SUM(x.jml_belanja) AS jml_belanja,
                            SUM(x.jml_pembiayaan_terima) AS jml_pembiayaan_terima, SUM(x.jml_pembiayaan_keluar) AS jml_pembiayaan_keluar
                            FROM (SELECT e.id_program_pd, f.id_sub_unit, 
                            CASE WHEN i.kd_rek_1 = 4 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_pendapatan,
                            CASE WHEN i.kd_rek_1 = 5 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_belanja,
                            CASE WHEN i.kd_rek_1 = 6 AND i.kd_rek_2 = 1 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_pembiayaan_terima,
                            CASE WHEN i.kd_rek_1 = 6 AND i.kd_rek_2 = 2 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_pembiayaan_keluar
                            FROM trx_anggaran_kegiatan_pd AS e
                            INNER JOIN trx_anggaran_pelaksana_pd AS f ON e.id_kegiatan_pd = f.id_kegiatan_pd
                            INNER JOIN trx_anggaran_aktivitas_pd AS g ON f.id_pelaksana_pd = g.id_pelaksana_pd
                            INNER JOIN trx_anggaran_belanja_pd AS h ON g.id_aktivitas_pd = h.id_aktivitas_pd
                            INNER JOIN ref_rek_5 AS i ON h.id_rekening_ssh = i.id_rekening) x
                            WHERE x.id_sub_unit ='.$id_unit.' 
                            GROUP BY x.id_program_pd, x.id_sub_unit ) b ON a.id_program_pd = b.id_program_pd 
                        INNER JOIN ref_program AS c ON a.id_program_ref = c.id_program
						INNER JOIN ref_bidang AS d ON c.id_bidang = d.id_bidang
                        WHERE b.id_sub_unit ='.$id_unit.' AND a.tahun_anggaran='.Session::get('tahun').' AND a.jns_dokumen_keu=1 AND a.kd_dokumen_keu=0
                        GROUP BY a.id_program_ref, a.uraian_program_renstra, b.id_sub_unit, a.id_dokumen_keu) a, (SELECT @id:=0) z');

      return DataTables::of($getProgramRenja)
        // ->addColumn('action', function ($getProgramRenja) {
        //     return '
        //             <div class="btn-group">
        //             <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
        //             <ul class="dropdown-menu dropdown-menu-right">
        //                 <li>
        //                     <a class="view-kegiatan dropdown-item"><i class="fa fa-briefcase fa-fw fa-lg"></i> Lihat Kegiatan SKPD</a>
        //                 </li>
        //             </ul>
        //             </div>
        //     ';
        // })        
        ->addColumn('action', function ($getProgramRenja) {
            return '<button id="view-kegiatan" type="button" class="btn btn-info btn-sm btn-labeled view-kegiatan"><span class="btn-label"><i class="fa fa-briefcase fa-fw fa-lg"></i></span>Kegiatan SKPD</button>
                ';
        })
        ->make(true);
    }

    public function getKegiatanRenja($id_program)
    {
      $getKegiatanRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.id_kegiatan_pd, a.id_program_pd, a.id_unit, 
            a.tahun_anggaran, a.no_urut, a.id_renja, a.id_rkpd_renstra, x.status_data AS status_program,
            a.id_program_renstra, a.id_kegiatan_renstra, a.id_kegiatan_ref,c.kd_kegiatan, c.nm_kegiatan, 
            a.uraian_kegiatan_forum, a.pagu_tahun_kegiatan, e.uraian_kegiatan_renstra, a.kelompok_sasaran,
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
            COALESCE((SELECT SUM(x.pagu_forum) FROM trx_anggaran_kegiatan_pd x WHERE x.id_kegiatan_pd=a.id_kegiatan_pd 
                GROUP BY x.id_kegiatan_pd),0) AS jml_pagu,
            COALESCE((SELECT COUNT(x.id_aktivitas_pd) as jml_aktivitas
            FROM trx_anggaran_aktivitas_pd x 
            INNER JOIN trx_anggaran_pelaksana_pd y ON x.id_pelaksana_pd = y.id_pelaksana_pd
            WHERE x.status_data = 1 AND y.id_kegiatan_pd = a.id_kegiatan_pd
            GROUP BY y.id_kegiatan_pd,x.status_data),0) as jml_aktivitas,
            COALESCE((SELECT SUM(x.pagu_anggaran) as jml_aktivitas
            FROM trx_anggaran_aktivitas_pd x 
            INNER JOIN trx_anggaran_pelaksana_pd y ON x.id_pelaksana_pd = y.id_pelaksana_pd
            WHERE x.status_data = 1 AND y.id_kegiatan_pd = a.id_kegiatan_pd
            GROUP BY y.id_kegiatan_pd,x.status_data),0) as jml_pagu_aktivitas
            FROM trx_anggaran_kegiatan_pd a
            INNER JOIN trx_anggaran_program_pd AS x ON a.id_program_pd=x.id_program_pd
            INNER JOIN (Select a.id_kegiatan, a.id_program, a.nm_kegiatan,
            CONCAT(RIGHT(CONCAT(0,d.kd_urusan),2),".",RIGHT(CONCAT(0,c.kd_bidang),2),".",RIGHT(CONCAT("00",b.kd_program),3),".",
            RIGHT(CONCAT("00",a.kd_kegiatan),3)) AS kd_kegiatan
            FROM ref_kegiatan a
            INNER JOIN ref_program b ON a.id_program=b.id_program
            INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
            INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan) c ON a.id_kegiatan_ref=c.id_kegiatan                 
            LEFT OUTER JOIN trx_renstra_kegiatan e ON a.id_kegiatan_renstra = e.id_kegiatan_renstra   
            WHERE a.id_program_pd='.$id_program.') a,(SELECT @id:=0) z');

      return DataTables::of($getKegiatanRenja)
        // ->addColumn('action', function ($getKegiatanRenja) {
        //         return '                         
        //         <div class="btn-group">
        //                 <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
        //                     <ul class="dropdown-menu dropdown-menu-right">
        //                        <li>
        //                            <a id="btnViewPelaksana" class="dropdown-item"><i class="fa fa-users fa-fw fa-lg"></i> Lihat Pelaksana</a>
        //                        </li>                       
        //                     </ul>
        //         </div>';
        // })
        ->addColumn('action', function ($getKegiatanRenja) {
            return '<button id="btnViewPelaksana" type="button" class="btn btn-info btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-users fa-fw fa-lg"></i></span>Lihat Pelaksana</button>
                ';
        })
        ->make(true);
    } 

public function getAktivitas($id_forum_skpd)
{
   $getAktivitas=DB::SELECT('SELECT (@id:=@id+1) as urut, a.id_aktivitas_pd, a.id_pelaksana_pd, a.tahun_anggaran, a.no_urut, 
        a.sumber_aktivitas, a.id_aktivitas_asb, a.id_aktivitas_rkpd_final, a.uraian_aktivitas_kegiatan, 
        COALESCE(a.id_satuan_publik,0) as id_satuan_publik,a.volume_aktivitas_1, 
        COALESCE(a.id_satuan_1,-1) as id_satuan_1, a.volume_aktivitas_2, a.volume_rkpd_1, a.volume_rkpd_2, a.id_satuan_publik,a.group_keu,
        COALESCE(a.id_satuan_2,0) as id_satuan_2, a.id_program_nasional, a.id_program_provinsi, a.jenis_kegiatan, a.sumber_dana, a.pagu_rkpd, 
        a.pagu_anggaran,  a.status_data,  a.sumber_data,a.status_pelaksanaan, a.keterangan_aktivitas,
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
            WHEN 1 THEN COALESCE(d.volume_1,0)
        END AS jml_vol_1,
        CASE a.sumber_aktivitas
            WHEN 0 THEN 
                CASE a.id_satuan_publik 
                WHEN 1 THEN COALESCE(d.jml_vol_lok,0)
                WHEN 0 THEN COALESCE(a.volume_aktivitas_2,0)
                END
            WHEN 1 THEN COALESCE(d.volume_2,0)
        END AS jml_vol_2,
        COALESCE(e.jml_belanja,0) as jml_belanja 
        FROM trx_anggaran_aktivitas_pd a
        LEFT OUTER JOIN ref_satuan b ON a.id_satuan_1 = b.id_satuan
        LEFT OUTER JOIN ref_satuan c ON a.id_satuan_2 = c.id_satuan
        LEFT OUTER JOIN (SELECT a.tahun_anggaran, a.id_aktivitas_pd,
            CASE b.id_satuan_publik 
                WHEN 0 THEN sum(a.volume_1)
                WHEN 1 THEN sum(a.volume_2)
            END AS jml_vol_lok,
            SUM(IF(a.id_satuan_1 <> -1 AND a.id_satuan_1 <> 0, a.volume_1, 0)) as volume_1,
            SUM(IF(a.id_satuan_2 <> -1 AND a.id_satuan_2 <> 0, a.volume_2, 0)) as volume_2
            FROM trx_anggaran_lokasi_pd AS a
            INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
            WHERE a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3                    
            GROUP BY a.tahun_anggaran, a.id_aktivitas_pd, b.id_satuan_publik) d ON a.id_aktivitas_pd = d.id_aktivitas_pd
        LEFT OUTER JOIN (SELECT a.tahun_anggaran, a.id_aktivitas_pd, Sum(a.jml_belanja) as jml_belanja
            FROM trx_anggaran_belanja_pd AS a
            GROUP BY a.tahun_anggaran,a.id_aktivitas_pd) e ON a.id_aktivitas_pd = e.id_aktivitas_pd,
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
   $getPelaksana=DB::SELECT('SELECT (@id:=@id+1) as urut,z.* FROM (SELECT a.id_pelaksana_pd, a.tahun_anggaran, a.no_urut, a.id_kegiatan_pd, a.id_sub_unit, 
        a.id_pelaksana_rkpd_final, a.id_lokasi, a.sumber_data, a.ket_pelaksana, a.id_pelaksana_renja,a.hak_akses,
        a.status_pelaksanaan, d.nm_sub, a.status_data, f.status_data AS status_kegiatan,
        COALESCE((SELECT COUNT(Y.id_lokasi_pd) AS jml_lokasi
            FROM trx_anggaran_aktivitas_pd AS X
            INNER JOIN trx_anggaran_lokasi_pd AS Y ON X.id_aktivitas_pd = Y.id_aktivitas_pd
            WHERE X.id_pelaksana_pd = a.id_pelaksana_pd
            GROUP BY X.id_pelaksana_pd),0) as jml_lokasi, 
        COALESCE((SELECT SUM(X.jml_belanja) AS jml_belanja
            FROM trx_anggaran_belanja_pd AS X
            INNER JOIN trx_anggaran_aktivitas_pd AS Y ON X.id_aktivitas_pd = Y.id_aktivitas_pd
            WHERE Y.status_data = 1 AND Y.id_pelaksana_pd = a.id_pelaksana_pd
            GROUP BY Y.status_data, Y.id_pelaksana_pd),0) as jml_pagu,
        COALESCE((SELECT SUM(X.pagu_anggaran) as jml_pagu_aktivitas 
        FROM trx_anggaran_aktivitas_pd AS X 
            WHERE X.status_data = 1 AND X.id_pelaksana_pd = a.id_pelaksana_pd
            GROUP BY X.id_pelaksana_pd, X.status_data),0) as jml_pagu_aktivitas,
        CASE a.status_data
            WHEN 0 THEN "fa fa-question"
            WHEN 1 THEN "fa fa-check-square-o"
        END AS status_icon,
        CASE a.status_data
            WHEN 0 THEN "red"
            WHEN 1 THEN "green"
        END AS warna, 
        e.jml_pendapatan, e.jml_belanja, (e.jml_pembiayaan_terima-e.jml_pembiayaan_keluar) AS pembiayaan_netto,
                    COALESCE(g.nama_lokasi,"Kosong") AS nama_lokasi
        FROM trx_anggaran_pelaksana_pd AS a
                    LEFT JOIN ref_lokasi AS g ON a.id_lokasi = g.id_lokasi
        INNER JOIN trx_anggaran_kegiatan_pd AS f ON a.id_kegiatan_pd = f.id_kegiatan_pd
        INNER JOIN ref_sub_unit AS d ON a.id_sub_unit = d.id_sub_unit
        INNER JOIN (SELECT x.id_pelaksana_pd, SUM(x.jml_pendapatan) AS jml_pendapatan, SUM(x.jml_belanja) AS jml_belanja,
            SUM(x.jml_pembiayaan_terima) AS jml_pembiayaan_terima, SUM(x.jml_pembiayaan_keluar) AS jml_pembiayaan_keluar
            FROM (SELECT g.id_pelaksana_pd,  
            CASE WHEN i.kd_rek_1 = 4 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_pendapatan,
            CASE WHEN i.kd_rek_1 = 5 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_belanja,
            CASE WHEN i.kd_rek_1 = 6 AND i.kd_rek_2 = 1 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_pembiayaan_terima,
            CASE WHEN i.kd_rek_1 = 6 AND i.kd_rek_2 = 2 THEN COALESCE(h.jml_belanja,0) ELSE 0 END AS jml_pembiayaan_keluar
            FROM  trx_anggaran_aktivitas_pd AS g 
            INNER JOIN trx_anggaran_belanja_pd AS h ON g.id_aktivitas_pd = h.id_aktivitas_pd
            INNER JOIN ref_rek_5 AS i ON h.id_rekening_ssh = i.id_rekening) x 
            GROUP BY x.id_pelaksana_pd) AS e ON a.id_pelaksana_pd = e.id_pelaksana_pd) z,
        (SELECT @id:=0) x WHERE z.id_kegiatan_pd ='.$id_aktivitas);

   return DataTables::of($getPelaksana)
   ->addColumn('action', function ($getPelaksana) {
        return '<button id="view-aktivitas" type="button" class="btn btn-info btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-calendar fa-fw fa-lg"></i></span>Lihat Aktivitas</button>
            ';
   })
   ->make(true);
}

public function getLokasiAktivitas($id_aktivitas_pd)
{
   $LokAktiv=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_anggaran, a.no_urut, a.id_aktivitas_pd, a.id_lokasi_pd, a.id_lokasi_rkpd_final, a.id_lokasi, b.nama_lokasi, 
            a.volume_1, a.volume_2,a.volume_usulan_1, a.volume_usulan_2, c.id_satuan_1, c.id_satuan_2, a.jenis_lokasi, 
            a.id_desa, a.id_kecamatan, a.rt, a.rw, a.uraian_lokasi, a.lat, a.lang, a.status_data, a.sumber_data, a.status_pelaksanaan, a.ket_lokasi, 
                           CASE a.status_data
                               WHEN 0 THEN "fa fa-question"
                               WHEN 1 THEN "fa fa-check-square-o"
                           END AS status_icon,
                           CASE a.status_data
                               WHEN 0 THEN "red"
                               WHEN 1 THEN "green"
                           END AS warna,
                           CASE a.sumber_data
                               WHEN 0 THEN "PPAS"
                               WHEN 1 THEN "APBD"
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
               FROM trx_anggaran_lokasi_pd a
               INNER JOIN ref_lokasi b on a.id_lokasi = b.id_lokasi
               INNER JOIN trx_anggaran_aktivitas_pd c ON a.id_aktivitas_pd = c.id_aktivitas_pd) a,
            (SELECT @id:=0) x WHERE a.id_aktivitas_pd='.$id_aktivitas_pd);

   return DataTables::of($LokAktiv)
   ->addColumn('action', function ($LokAktiv) {
        return '<button id="btnEditLokasi" type="button" class="btn btn-warning btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-pencil fa-lg fa-fw"></i></span>Edit Lokasi Aktivitas</button>
        ';
   })
   ->make(true);
}

public function getBelanja($id_aktivitas_pd){
   $getBelanja=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_anggaran,a.no_urut as urut,a.id_belanja_pd,a.id_aktivitas_pd, p.status_data AS status_aktivitas,
        a.id_zona_ssh,a.id_belanja_rkpd_final,a.sumber_belanja,a.id_aktivitas_asb,a.id_item_ssh,a.id_rekening_ssh,a.uraian_belanja,
        a.volume_1, a.id_satuan_1,a.volume_2,a.id_satuan_2,a.koefisien, a.harga_satuan,a.jml_belanja, a.status_data,a.sumber_data,
        COALESCE(b.uraian_tarif_ssh,a.uraian_belanja) as uraian_tarif_ssh,c.uraian_satuan as satuan_1, d.uraian_satuan as satuan_2,
            a.volume_1_rkpd,a.volume_2_rkpd,a.koefisien_rkpd,a.harga_satuan_rkpd,a.jml_belanja_rkpd,
            e.kd_rekening, e.nm_rekening, f.nm_aktivitas_asb,
                    CASE a.status_data
                        WHEN 0 THEN "fa fa-question fa-fw fa-lg"
                        WHEN 1 THEN "fa fa-check-square-o fa-fw fa-lg"
                    END AS status_icon,
                    CASE a.status_data
                        WHEN 0 THEN "red"
                        WHEN 1 THEN "green"
                    END AS warna
        FROM trx_anggaran_belanja_pd a
        LEFT OUTER JOIN ref_ssh_tarif b on a.id_item_ssh = b.id_tarif_ssh
        LEFT OUTER JOIN ref_satuan c on a.id_satuan_1 = c.id_satuan
        LEFT OUTER JOIN ref_satuan d on a.id_satuan_2 = d.id_satuan
        LEFT OUTER JOIN (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
                        a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
                        FROM ref_rek_5 a) e on a.id_rekening_ssh = e.id_rekening
        LEFT OUTER JOIN trx_asb_aktivitas f on a.id_aktivitas_asb = f.id_aktivitas_asb
        INNER JOIN trx_anggaran_aktivitas_pd p on a.id_aktivitas_pd = p.id_aktivitas_pd
        WHERE a.id_aktivitas_pd='.$id_aktivitas_pd.') a,
        (SELECT @id:=0) x ');

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

public function getLokasiCopy($id_unit){
   $getBelanja=DB::SELECT('SELECT (@id:=@id+1) as urut, e.*, c.id_unit
        FROM trx_anggaran_aktivitas_pd e
        INNER JOIN trx_anggaran_pelaksana_pd d ON e.id_pelaksana_pd = d.id_pelaksana_pd
        INNER JOIN trx_anggaran_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
        INNER JOIN trx_anggaran_program_pd b ON a.id_program_pd = b.id_program_pd
        INNER JOIN trx_anggaran_pelaksana c ON b.id_pelaksana_anggaran = c.id_pelaksana_anggaran,
        (SELECT @id:=0) x
        WHERE c.id_unit='.$id_unit.' AND e.sumber_aktivitas=1 AND e.tahun_anggaran='.Session::get('tahun'));

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

    $cekProgramRkpd = DB::SELECT('SELECT e.id_aktivitas_pd, c.hak_akses, c.status_data AS status_rkpd, b.status_data AS status_program, c.status_data AS status_kegiatan, e.status_data AS status_aktivitas
        FROM trx_anggaran_aktivitas_pd e
        INNER JOIN trx_anggaran_pelaksana_pd d ON e.id_pelaksana_pd = d.id_pelaksana_pd
        INNER JOIN trx_anggaran_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
        INNER JOIN trx_anggaran_program_pd b ON a.id_program_pd = b.id_program_pd
        INNER JOIN trx_anggaran_pelaksana c ON b.id_pelaksana_anggaran = c.id_pelaksana_anggaran
        WHERE e.id_aktivitas_pd='.$req->id_aktivitas_pd);

    if($cekProgramRkpd != NULL && $cekProgramRkpd[0]->status_rkpd == 0 && $cekProgramRkpd[0]->status_program == 0 && $cekProgramRkpd[0]->status_kegiatan == 0 && $cekProgramRkpd[0]->status_aktivitas == 0) {
        $getBelanja=DB::INSERT('INSERT INTO trx_anggaran_belanja_pd (tahun_anggaran, no_urut, id_aktivitas_pd, id_belanja_rkpd_final, id_zona_ssh, 
            sumber_belanja, id_aktivitas_asb, id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, 
            volume_2, id_satuan_2, koefisien, harga_satuan, jml_belanja, volume_1_rkpd, volume_2_rkpd, 
            koefisien_rkpd, harga_satuan_rkpd, jml_belanja_rkpd, status_data, sumber_data) 
            SELECT tahun_anggaran, no_urut, '.$req->id_aktivitas_new.', id_belanja_pd, id_zona_ssh,  
            sumber_belanja, id_aktivitas_asb, id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, 
            koefisien, harga_satuan, jml_belanja, 0, 0, 0, 0, 0, 0, 4 
            FROM trx_anggaran_belanja_pd where id_aktivitas_pd ='.$req->id_aktivitas_pd);

       if($getBelanja!=0) {
            return response ()->json (['pesan'=>'Data Berhasil Dicopy','status_pesan'=>'1']);
        } else {
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal DiCopy ('.$error_code.')','status_pesan'=>'0']);
        };
    } else {
        return response ()->json (['pesan'=>'Gagal Posting, Cek Status RKPD, Program, Kegiatan, dan Aktivitas..','status_pesan'=>'0']);
    }
}

public function getHitungASB(Request $req){
    $cekProgramRkpd = DB::SELECT('SELECT e.id_aktivitas_pd, c.hak_akses, c.status_data AS status_rkpd, b.status_data     AS status_program, c.status_data AS status_kegiatan, e.status_data AS status_aktivitas
        FROM trx_anggaran_aktivitas_pd e
        INNER JOIN trx_anggaran_pelaksana_pd d ON e.id_pelaksana_pd = d.id_pelaksana_pd
        INNER JOIN trx_anggaran_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
        INNER JOIN trx_anggaran_program_pd b ON a.id_program_pd = b.id_program_pd
        INNER JOIN trx_anggaran_pelaksana c ON b.id_pelaksana_anggaran = c.id_pelaksana_anggaran
        WHERE e.id_aktivitas_pd='.$req->id_aktivitas_pd);

    if($cekProgramRkpd != NULL && $cekProgramRkpd[0]->status_rkpd == 0 && $cekProgramRkpd[0]->status_program == 0 && $cekProgramRkpd[0]->status_kegiatan == 0 && $cekProgramRkpd[0]->status_aktivitas == 0) {
        $getHitung=DB::INSERT('INSERT INTO trx_anggaran_belanja_pd (tahun_anggaran, no_urut, id_aktivitas_pd, id_belanja_rkpd_final, id_zona_ssh, sumber_belanja, id_aktivitas_asb, 
            id_item_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, koefisien, harga_satuan, jml_belanja, volume_1_rkpd,  volume_2_rkpd, koefisien_rkpd,
            harga_satuan_rkpd, jml_belanja_rkpd, status_data, sumber_data)
            SELECT '.Session::get('tahun').', (@id:=@id+1) as no_urut,'.$req->id_aktivitas_pd.',0,1,0, a.id_aktivitas_asb, a.id_tarif_ssh, a.id_rekening, a.nm_aktivitas_asb,
                '.$req->volume_1.','.$req->id_satuan_1.','.$req->volume_2.','.$req->id_satuan_2.',1,a.harga_satuan,a.jml_pagu,0,0,1,0,0,0,1 FROM (
                SELECT a.id_aktivitas_asb, b.nm_aktivitas_asb,b.id_satuan_1,b.id_satuan_2,a.id_tarif_ssh, 
                PaguASB(b.jenis_biaya,b.hub_driver,'.$req->volume_1.','.$req->volume_2.',b.r1,b.r2,b.km1,b.km2,b.kf1,b.kf2,b.kf3,a.harga_satuan) AS jml_pagu, a.harga_satuan, b.koef, b.id_rekening
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
                LEFT OUTER JOIN ref_satuan g ON c.sat_derivatif_2 = g.id_satuan) b ON a.id_aktivitas_asb = b.id_aktivitas_asb AND a.id_komponen_asb = b.id_komponen_asb 
                AND a.id_komponen_asb_rinci = b.id_komponen_asb_rinci
                WHERE a.id_aktivitas_asb='.$req->id_aktivitas_asb.') a, (SELECT @id:=0) z');

        if($getHitung != 0){
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); 
        } else {
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }

    } else {
        return response ()->json (['pesan'=>'Gagal Posting, Cek Status RKPD, Program, Kegiatan, dan Aktivitas..','status_pesan'=>'0']);
    }
}

public function unloadASB(Request $req){

    $cekProgramRkpd = DB::SELECT('SELECT e.id_aktivitas_pd, c.hak_akses, c.status_data AS status_rkpd, b.status_data AS status_program, c.status_data AS status_kegiatan, e.status_data AS status_aktivitas
            FROM trx_anggaran_aktivitas_pd e
            INNER JOIN trx_anggaran_pelaksana_pd d ON e.id_pelaksana_pd = d.id_pelaksana_pd
            INNER JOIN trx_anggaran_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd b ON a.id_program_pd = b.id_program_pd
            INNER JOIN trx_anggaran_pelaksana c ON b.id_pelaksana_anggaran = c.id_pelaksana_anggaran
            WHERE e.id_aktivitas_pd='.$req->id_aktivitas_pd);

    if($cekProgramRkpd != NULL && $cekProgramRkpd[0]->status_rkpd == 0 && $cekProgramRkpd[0]->status_program == 0 && $cekProgramRkpd[0]->status_kegiatan == 0 && $cekProgramRkpd[0]->status_aktivitas == 0) {

        $getHitung=DB::DELETE('DELETE FROM trx_anggaran_belanja_pd 
                    WHERE id_aktivitas_asb='.$req->id_aktivitas_asb.' AND id_aktivitas_pd='.$req->id_aktivitas_pd);
        if($getHitung != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']); 
        } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
        }
    } else {
        return response ()->json (['pesan'=>'Gagal Posting, Cek Status RKPD, Program, Kegiatan, dan Aktivitas..','status_pesan'=>'0']);
    }

}

public function getBidang($id_unit,$id_ranwal){
        $urusan=DB::select('SELECT a.tahun_rkpd, a.id_rkpd_rancangan, a.id_bidang, d.nm_bidang, b.id_unit
            FROM trx_anggaran_urusan a
            INNER JOIN trx_anggaran_pelaksana b ON a.id_urusan_rkpd = b.id_urusan_rkpd
            INNER JOIN ref_bidang d ON a.id_bidang = d.id_bidang
            WHERE b.id_unit='.$id_unit.' and a.id_rkpd_rancangan='.$id_ranwal);
        
        return json_encode($urusan);
}

public function AddProgRenja(Request $req){

// $cekProgramRkpd = DB::SELECT('SELECT a.status_data FROM trx_anggaran a
//     INNER JOIN trx_anggaran_urusan b ON a.id_rkpd_rancangan = b.id_rkpd_rancangan
//     INNER JOIN trx_anggaran_pelaksana c ON b.id_urusan_rkpd = c.id_urusan_rkpd
//     WHERE c.id_pelaksana_rkpd='.$req->id_rkpd_rancangan);

$cek = DB::SELECT('SELECT c.hak_akses, a.status_data FROM trx_anggaran_program a
    INNER JOIN trx_anggaran_urusan b ON a.id_anggaran_pemda = b.id_anggaran_pemda
    INNER JOIN trx_anggaran_pelaksana c ON b.id_urusan_anggaran = c.id_urusan_anggaran
    WHERE c.id_pelaksana_anggaran'.$req->id_pelaksana_anggaran);

        $data = new TrxAnggaranProgramPd();
        $data->id_pelaksana_anggaran= $req->id_pelaksana_anggaran;
        $data->tahun_anggaran= Session::get('tahun');
        $data->jenis_belanja= $req->jenis_belanja;
        $data->no_urut= $req->no_urut;
        $data->id_unit= $req->id_unit; 
        $data->id_program_pd_rkpd_final= 0;
        $data->id_program_renstra= $req->id_program_renstra;
        $data->uraian_program_renstra= $req->uraian_program_renstra;
        $data->id_program_ref= $req->id_program_ref;
        $data->pagu_rkpd_final= 0;
        $data->pagu_anggaran= $req->pagu_anggaran;
        $data->sumber_data= 1;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->ket_usulan= $req->ket_usulan;
        $data->status_data= 0;   
        $data->id_dokumen_keu= $req->id_dokumen_keu;
        $data->jns_dokumen_keu= $req->jns_dokumen_keu;
        $data->kd_dokumen_keu= $req->kd_dokumen_keu;
        $data->id_perubahan= $req->id_perubahan;

if($cek != null && $cek[0]->hak_akses == 0 && $cek[0]->status_data <> 0){
    return response ()->json (['pesan'=>'Maaf Tidak Diberi Akses untuk Menambah Program','status_pesan'=>'0']);
} else {
    try{        
        $data->save (['timestamps' => false]);
        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
    }
}

public function editProgRenja(Request $req){
$cek = DB::SELECT('SELECT a.id_program_pd, c.hak_akses
            FROM trx_anggaran_program_pd a
            INNER JOIN trx_anggaran_pelaksana c ON a.id_pelaksana_anggaran = c.id_pelaksana_anggaran
            WHERE a.id_program_pd='.$req->id_program_pd.' GROUP BY a.id_program_pd, c.hak_akses');

$data = TrxAnggaranProgramPd::find($req->id_program_pd);
        $data->id_pelaksana_anggaran= $req->id_pelaksana_anggaran;
        $data->jenis_belanja= $req->jenis_belanja;
        $data->no_urut= $req->no_urut;
        $data->id_program_renstra= $req->id_program_renstra;
        $data->uraian_program_renstra= $req->uraian_program_renstra;
        $data->id_program_ref= $req->id_program_ref;
        $data->pagu_anggaran= $req->pagu_anggaran;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->ket_usulan= $req->ket_usulan;
        $data->status_data= $req->status_data;
        $data->id_dokumen_keu= $req->id_dokumen_keu;
        $data->jns_dokumen_keu= $req->jns_dokumen_keu;
        $data->kd_dokumen_keu= $req->kd_dokumen_keu;
        $data->id_perubahan= $req->id_perubahan;

if($cek != null && $cek[0]->hak_akses == 0){
    return response ()->json (['pesan'=>'Maaf Tidak Diberi Akses untuk merubah','status_pesan'=>'0']);
} else {
    try{        
        $data->save (['timestamps' => false]);
        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

}

public function hapusProgRenja(Request $req){
    TrxAnggaranProgramPd::where('id_program_pd',$req->id_program_pd)->delete();
    return response ()->json (['pesan'=>'Data Berhasil dihapus']);
}

public function postProgRenja(Request $req)
    {
        $data = TrxAnggaranProgramPd::find($req->id_program_pd);
        $data->status_data= $req->status_data;

        $cekRkpd = DB::SELECT('SELECT a.status_data AS status_rkpd FROM trx_anggaran_pelaksana AS a
            INNER JOIN trx_anggaran_program_pd AS b ON a.id_pelaksana_anggaran = b.id_pelaksana_anggaran
            WHERE b.id_program_pd='.$req->id_program_pd);

        $cek = DB::SELECT('SELECT a.id_program_pd, a.pagu_forum, (COALESCE(a.pagu_forum,0) - COALESCE(b.pagu_kegiatan,0)) AS selisih  
            FROM trx_anggaran_program_pd a
            LEFT OUTER JOIN (SELECT id_program_pd, COALESCE(SUM(pagu_forum),0) AS pagu_kegiatan FROM trx_anggaran_kegiatan_pd
            WHERE status_data = 1 GROUP BY id_program_pd, status_data) b
            ON a.id_program_pd=b.id_program_pd
            WHERE a.id_program_pd='.$req->id_program_pd);

        if($cekRkpd != null && $cekRkpd[0]->status_rkpd == 0){
            if($cek != null && $cek[0]->selisih == 0){
                try{
                    $data->save (['timestamps' => false]);
                    return response ()->json (['pesan'=>'Data Berhasil Diposting','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Gagal Diposting ('.$error_code.')','status_pesan'=>'0']);
                }
            } else {
                return response ()->json (['pesan'=>'Data Jumlah Pagu Program dengan Kegiatan yang telah diposting Tidak Sama','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Status Pelaksana RKPD telah di Posting','status_pesan'=>'0']);
        }
    }

public function addKegRenja(Request $req){

$cek = DB::SELECT('SELECT a.hak_akses FROM trx_anggaran_pelaksana a
        INNER JOIN trx_anggaran_program_pd b ON a.id_pelaksana_anggaran = b.id_pelaksana_anggaran
        WHERE b.id_program_pd = '.$req->id_program_pd.' GROUP BY b.id_program_pd,a.hak_akses');

        $data = new TrxAnggaranKegiatanPd();
        $data->id_program_pd= $req->id_program_pd;
        $data->id_unit= $req->id_unit;
        $data->tahun_anggaran= Session::get('tahun');
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
        $data->kelompok_sasaran= $req->kelompok_sasaran;
        $data->status_data= 0;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->sumber_data=1;

if($cek != null && $cek[0]->hak_akses == 0){
    return response ()->json (['pesan'=>'Maaf Tidak Diberi Akses untuk Menambah Kegiatan','status_pesan'=>'0']);
} else {
    try{
        $data->save (['timestamps' => false]);
        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}
}

public function editKegRenja(Request $req){
$cek = DB::SELECT('SELECT a.hak_akses FROM trx_anggaran_pelaksana a
        INNER JOIN trx_anggaran_program_pd b ON a.id_pelaksana_anggaran = b.id_pelaksana_anggaran
        WHERE b.id_program_pd = '.$req->id_program_pd.' GROUP BY b.id_program_pd,a.hak_akses');

        $data = TrxAnggaranKegiatanPd::find($req->id_kegiatan_pd);
        $data->no_urut= $req->no_urut;
        $data->id_rkpd_renstra= $req->id_rkpd_renstra;
        $data->id_program_renstra= $req->id_program_renstra;
        $data->id_kegiatan_renstra= $req->id_kegiatan_renstra;
        $data->id_kegiatan_ref= $req->id_kegiatan_ref;
        $data->uraian_kegiatan_forum= $req->uraian_kegiatan_forum;
        $data->pagu_plus1_forum= $req->pagu_plus1_forum;
        $data->pagu_forum= $req->pagu_forum;
        $data->keterangan_status= $req->keterangan_status;
        $data->kelompok_sasaran= $req->kelompok_sasaran;
        $data->status_data= $req->status_data;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
if($cek != null && $cek[0]->hak_akses == 0){
    return response ()->json (['pesan'=>'Maaf Tidak Diberi Akses untuk Menambah Kegiatan','status_pesan'=>'0']);
} else {
    try{
        $data->save (['timestamps' => false]);
        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

}

public function postKegRenja(Request $req)
    {
        $data = TrxAnggaranKegiatanPd::find($req->id_kegiatan_pd);
        $data->status_data= $req->status_data;

        $cek = DB::SELECT('SELECT c.id_kegiatan_pd, c.status_data, c.pagu_forum, COALESCE(b.jml_pagu_aktivitas,0) as jml_pagu_aktivitas, 
            COALESCE(c.pagu_forum,0) - COALESCE(b.jml_pagu_aktivitas,0) AS selisih 
            FROM (SELECT a.tahun_anggaran, b.id_kegiatan_pd,
                SUM(a.pagu_aktivitas_forum) AS jml_pagu_aktivitas
                FROM trx_anggaran_aktivitas_pd a 
                INNER JOIN trx_anggaran_pelaksana_pd b ON a.id_pelaksana_pd = b.id_pelaksana_pd
                WHERE a.status_data = 1
                GROUP BY a.tahun_anggaran, b.id_kegiatan_pd, a.status_data) b
            INNER JOIN trx_anggaran_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            WHERE c.id_kegiatan_pd='.$req->id_kegiatan_pd);

        if($cek != null && $cek[0]->selisih == 0){
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
    $result = TrxAnggaranKegiatanPd::where('id_kegiatan_pd',$req->id_kegiatan_pd)->delete ();
    
    if($result != 0){
        return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
    } else {
        return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
    }
    

}

public function addAktivitas(Request $req)
    {
            $data = new TrxAnggaranAktivitasPd;
            $data->id_pelaksana_pd= $req->id_pelaksana_pd;
            $data->tahun_anggaran= Session::get('tahun');
            $data->no_urut= $req->no_urut;
            $data->sumber_aktivitas= $req->sumber_aktivitas;
            $data->id_aktivitas_asb= $req->id_aktivitas_asb;
            $data->id_aktivitas_rkpd_final= 0;
            $data->uraian_aktivitas_kegiatan= $req->uraian_aktivitas_kegiatan;
            $data->volume_aktivitas_1= 0;
            $data->volume_rkpd_1= 0;
            $data->id_satuan_1= $req->id_satuan_1;
            $data->volume_aktivitas_2= 0;
            $data->volume_rkpd_2= 0;
            $data->id_satuan_2= $req->id_satuan_2;
            $data->id_program_nasional= 0;
            $data->id_program_provinsi= 0;
            $data->jenis_kegiatan= $req->jenis_kegiatan;
            $data->sumber_dana= $req->sumber_dana;
            $data->id_satuan_publik= $req->id_satuan_publik;
            $data->pagu_rkpd= 0;
            $data->pagu_anggaran= $req->pagu_anggaran;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->keterangan_aktivitas= $req->keterangan_aktivitas;
            $data->group_keu= $req->group_keu;
            $data->sumber_data= 1;
            $data->status_data= 0;
        
            $cek = DB::SELECT('SELECT d.status_data AS status_program,
            CASE WHEN b.status_data=0 AND c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_anggaran_pelaksana_pd b
            INNER JOIN trx_anggaran_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd d ON c.id_program_pd = d.id_program_pd
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
        $data = TrxAnggaranAktivitasPd::find($req->id_aktivitas_pd);
            $data->no_urut= $req->no_urut;
            $data->sumber_aktivitas= $req->sumber_aktivitas;
            $data->id_aktivitas_asb= $req->id_aktivitas_asb;
            $data->id_aktivitas_rkpd_final= $req->id_aktivitas_renja;
            $data->uraian_aktivitas_kegiatan= $req->uraian_aktivitas_kegiatan;
            // $data->volume_aktivitas_1= $req->volume_aktivitas_1;
            $data->id_satuan_1= $req->id_satuan_1;
            // $data->volume_aktivitas_2= $req->volume_aktivitas_2;
            $data->id_satuan_2= $req->id_satuan_2;
            $data->jenis_kegiatan= $req->jenis_kegiatan;
            $data->sumber_dana= $req->sumber_dana;
            $data->id_satuan_publik= $req->id_satuan_publik;
            $data->pagu_anggaran= $req->pagu_anggaran;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->keterangan_aktivitas= $req->keterangan_aktivitas;
            $data->group_keu= $req->group_keu;

        $cek = DB::SELECT('SELECT a.status_data AS status_aktivitas, c.status_data AS status_kegiatan, d.status_data AS status_program,
            CASE WHEN a.status_data=0 AND c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_anggaran_aktivitas_pd a
            INNER JOIN trx_anggaran_pelaksana_pd b ON a.id_pelaksana_pd = b.id_pelaksana_pd
            INNER JOIN trx_anggaran_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd d ON c.id_program_pd = d.id_program_pd
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
        $data = TrxAnggaranAktivitasPd::find($req->id_aktivitas_pd);
        $data->status_data= $req->status_data;

        $cekProgramRkpd = DB::SELECT('SELECT e.id_aktivitas_pd, c.hak_akses, c.status_data AS status_rkpd, b.status_data AS status_program, c.status_data AS status_kegiatan
            FROM trx_anggaran_aktivitas_pd e
            INNER JOIN trx_anggaran_pelaksana_pd d ON e.id_pelaksana_pd = d.id_pelaksana_pd
            INNER JOIN trx_anggaran_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd b ON a.id_program_pd = b.id_program_pd
            INNER JOIN trx_anggaran_pelaksana c ON b.id_pelaksana_rkpd = c.id_pelaksana_rkpd
            WHERE e.id_aktivitas_pd='.$req->id_aktivitas_pd);

        $cek = DB::SELECT('SELECT a.tahun_forum,a.id_aktivitas_pd, SUM(a.pagu_aktivitas_forum) as pagu_aktivitas, COALESCE(sum(e.jml_belanja),0) as pagu_belanja, 
            SUM(a.pagu_aktivitas_forum)-COALESCE(SUM(e.jml_belanja),0) as selisih, COALESCE(SUM(b.jml_lokasi),0) AS jml_lokasi
            FROM trx_anggaran_aktivitas_pd a
            LEFT OUTER JOIN (SELECT id_aktivitas_pd, COUNT(*) AS jml_lokasi FROM trx_anggaran_lokasi_pd
            WHERE status_pelaksanaan = 0 OR status_pelaksanaan = 1
            GROUP BY id_aktivitas_pd, status_pelaksanaan) AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
            LEFT OUTER JOIN (SELECT a.id_aktivitas_pd, Sum(a.jml_belanja_forum) as jml_belanja
            FROM trx_anggaran_belanja_pd AS a GROUP BY a.id_aktivitas_pd) e ON a.id_aktivitas_pd = e.id_aktivitas_pd
            WHERE a.id_aktivitas_pd='.$req->id_aktivitas_pd.' GROUP BY a.tahun_forum,a.id_aktivitas_pd');

        if($cekProgramRkpd != NULL && $cekProgramRkpd[0]->status_rkpd == 0 && $cekProgramRkpd[0]->status_program == 0 && $cekProgramRkpd[0]->status_kegiatan == 0) {        
            if($req->status_data == 1) {
                    if($cek[0]->selisih == 0 && $cek[0]->jml_lokasi > 0){
                        try{
                            $data->save (['timestamps' => false]);
                            return response ()->json (['pesan'=>'Data Berhasil Diposting','status_pesan'=>'1']);
                        }
                        catch(QueryException $e){
                            $error_code = $e->errorInfo[1] ;
                            return response ()->json (['pesan'=>'Data Gagal Diposting ('.$error_code.')','status_pesan'=>'0']);
                        }
                    } else {
                         return response ()->json (['pesan'=>'Cek Pagu Aktivitas, Jumlah Belanja dan Jumlah Lokasi Aktivitas','status_pesan'=>'0']);
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
        } else {
            return response ()->json (['pesan'=>'Gagal Posting, Cek Status RKPD, Program dan Kegiatan..','status_pesan'=>'0']);
        }
    }

public function hapusAktivitas(Request $req)
      {
        $cek = DB::SELECT('SELECT a.status_data AS status_aktivitas, c.status_data AS status_kegiatan, d.status_data AS status_program,
            CASE WHEN a.status_data=0 AND c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_anggaran_aktivitas_pd a
            INNER JOIN trx_anggaran_pelaksana_pd b ON a.id_pelaksana_pd = b.id_pelaksana_pd
            INNER JOIN trx_anggaran_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd d ON c.id_program_pd = d.id_program_pd
            WHERE a.id_aktivitas_pd='.$req->id_aktivitas_pd);

        if($cek[0]->status_data==0){
            TrxAnggaranAktivitasPd::where('id_aktivitas_pd',$req->id_aktivitas_pd)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
        } else {
             return response ()->json (['pesan'=>'Data Aktivitas telah diposting, Data tidak dapat dihapus','status_pesan'=>'0']);
        }
      }

public function addPelaksana(Request $req)
    {
        
            $data = new TrxAnggaranPelaksanaPd;
            $data->tahun_anggaran= Session::get('tahun');
            $data->no_urut= $req->no_urut;
            $data->id_kegiatan_pd= $req->id_kegiatan_pd;
            $data->id_sub_unit= $req->id_sub_unit;
            $data->id_lokasi= $req->id_lokasi;
            $data->sumber_data= 1;
            $data->ket_pelaksana= $req->ket_pelaksana;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->status_data= $req->status_data;
            $data->hak_akses= $req->hak_akses;
        
        $cek = DB::SELECT('SELECT c.status_data AS status_kegiatan, d.status_data AS status_program,
            CASE WHEN c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_anggaran_kegiatan_pd c
            INNER JOIN trx_anggaran_program_pd d ON c.id_program_pd = d.id_program_pd
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
        
            $data = TrxAnggaranPelaksanaPd::find($req->id_pelaksana_pd);
            $data->no_urut= $req->no_urut;
            $data->id_sub_unit= $req->id_sub_unit;
            $data->id_lokasi= $req->id_lokasi;
            $data->ket_pelaksana= $req->ket_pelaksana;
            $data->status_pelaksanaan= $req->status_pelaksanaan;
            $data->status_data= $req->status_data;
            $data->hak_akses= $req->hak_akses;

        $cek = DB::SELECT('SELECT c.status_data AS status_kegiatan, d.status_data AS status_program,
            CASE WHEN c.status_data=0 AND d.status_data=0 THEN 0 ELSE 1 END AS status_data
            FROM trx_anggaran_kegiatan_pd c
            INNER JOIN trx_anggaran_program_pd d ON c.id_program_pd = d.id_program_pd
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
            FROM trx_anggaran_pelaksana_pd b
            INNER JOIN trx_anggaran_kegiatan_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd d ON c.id_program_pd = d.id_program_pd
            WHERE b.id_pelaksana_pd='.$req->id_pelaksana_pd);

        if($cek[0]->status_data==0){
            TrxAnggaranPelaksanaPd::where('id_pelaksana_pd',$req->id_pelaksana_pd)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
        } else {
            return response ()->json (['pesan'=>'Data Kegiatan telah diposting, Data tidak dapat dihapus','status_pesan'=>'0']);
        }
      }

public function addLokasi(Request $req)
    {

    $cekProgramRkpd = DB::SELECT('SELECT e.id_aktivitas_pd, c.hak_akses, c.status_data AS status_rkpd, b.status_data AS status_program, c.status_data AS status_kegiatan, e.status_data AS status_aktivitas
            FROM trx_anggaran_aktivitas_pd e
            INNER JOIN trx_anggaran_pelaksana_pd d ON e.id_pelaksana_pd = d.id_pelaksana_pd
            INNER JOIN trx_anggaran_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd b ON a.id_program_pd = b.id_program_pd
            INNER JOIN trx_anggaran_pelaksana c ON b.id_pelaksana_anggaran = c.id_pelaksana_anggaran
            WHERE e.id_aktivitas_pd='.$req->id_aktivitas_pd);

        
            $data = new TrxAnggaranLokasiPd;
            $data->tahun_anggaran = Session::get('tahun');
            $data->no_urut = $req->no_urut ;
            $data->id_aktivitas_pd = $req->id_aktivitas_pd ;
            $data->jenis_lokasi = $req->jenis_lokasi ;
            $data->id_lokasi = $req->id_lokasi ;
            $data->id_lokasi_teknis = $req->id_lokasi_teknis ;
            $data->id_lokasi_rkpd_final = 0 ;
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

    if($cekProgramRkpd != NULL && $cekProgramRkpd[0]->status_rkpd == 0 && $cekProgramRkpd[0]->status_program == 0 && $cekProgramRkpd[0]->status_kegiatan == 0 && $cekProgramRkpd[0]->status_aktivitas == 0) {         
        try{    
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    } else {
        return response ()->json (['pesan'=>'Gagal Posting, Cek Status RKPD, Program, Kegiatan, dan Aktivitas..','status_pesan'=>'0']);
    }
    }

public function editLokasi(Request $req)
    {
        $cekProgramRkpd = DB::SELECT('SELECT e.id_aktivitas_pd, c.hak_akses, c.status_data AS status_rkpd, b.status_data AS status_program, c.status_data AS status_kegiatan, e.status_data AS status_aktivitas
            FROM trx_anggaran_aktivitas_pd e
            INNER JOIN trx_anggaran_pelaksana_pd d ON e.id_pelaksana_pd = d.id_pelaksana_pd
            INNER JOIN trx_anggaran_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd b ON a.id_program_pd = b.id_program_pd
            INNER JOIN trx_anggaran_pelaksana c ON b.id_pelaksana_anggaran = c.id_pelaksana_anggaran
            WHERE e.id_aktivitas_pd='.$req->id_aktivitas_pd);

            $data = TrxAnggaranLokasiPd::find($req->id_lokasi_pd);
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

        if($cekProgramRkpd != NULL && $cekProgramRkpd[0]->status_rkpd == 0 && $cekProgramRkpd[0]->status_program == 0 && $cekProgramRkpd[0]->status_kegiatan == 0 && $cekProgramRkpd[0]->status_aktivitas == 0) {         
            try{ 
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
              }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
              }
        } else {
            return response ()->json (['pesan'=>'Gagal Posting, Cek Status RKPD, Program, Kegiatan, dan Aktivitas..','status_pesan'=>'0']);
        }
    }

public function hapusLokasi(Request $req)
      {
        TrxAnggaranLokasiPd::where('id_lokasi_pd',$req->id_lokasi_pd)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

public function addUsulan(Request $req)
    {
        try{
            $data = new TrxAnggaranKegiatanPdUsulan;
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
            $data = TrxAnggaranKegiatanPdUsulan::find($req->id_sumber_usulan);
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
        TrxAnggaranKegiatanPdUsulan::where('id_sumber_usulan',$req->id_sumber_usulan)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

    public function addBelanja(Request $req)
    {
        $cekProgramRkpd = DB::SELECT('SELECT e.id_aktivitas_pd, c.hak_akses, c.status_data AS status_rkpd, b.status_data AS status_program, c.status_data AS status_kegiatan, e.status_data AS status_aktivitas
            FROM trx_anggaran_aktivitas_pd e
            INNER JOIN trx_anggaran_pelaksana_pd d ON e.id_pelaksana_pd = d.id_pelaksana_pd
            INNER JOIN trx_anggaran_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd b ON a.id_program_pd = b.id_program_pd
            INNER JOIN trx_anggaran_pelaksana c ON b.id_pelaksana_anggaran = c.id_pelaksana_anggaran
            WHERE e.id_aktivitas_pd='.$req->id_aktivitas_pd);

            $data = new TrxAnggaranBelanjaPd;
            $data->tahun_anggaran= Session::get('tahun');
            $data->id_belanja_rkpd_final=0;
            $data->no_urut= $req->no_urut;
            $data->id_aktivitas_pd= $req->id_aktivitas_pd;
            $data->id_zona_ssh= $req->id_zona_ssh;
            $data->sumber_belanja= $req->sumber_belanja;
            $data->id_aktivitas_asb= 0;
            $data->id_item_ssh= $req->id_item_ssh;
            $data->id_rekening_ssh= $req->id_rekening_ssh;
            $data->uraian_belanja= $req->uraian_belanja;
            $data->volume_1_rkpd= 0;
            $data->volume_2_rkpd= 0;
            $data->harga_satuan_rkpd= 0;
            $data->jml_belanja_rkpd= 0;
            $data->volume_1= $req->volume_1;
            $data->id_satuan_1= $req->id_satuan_1;
            $data->volume_2= $req->volume_2;
            $data->id_satuan_2= $req->id_satuan_2;
            $data->harga_satuan= $req->harga_satuan;
            $data->jml_belanja= $req->jml_belanja;
            $data->koefisien= $req->koefisien;
            $data->koefisien_rkpd= 1;
            $data->sumber_data= 4;
            $data->status_data= 0;

        if($cekProgramRkpd != NULL && $cekProgramRkpd[0]->status_rkpd == 0 && $cekProgramRkpd[0]->status_program == 0 && $cekProgramRkpd[0]->status_kegiatan == 0 && $cekProgramRkpd[0]->status_aktivitas == 0) {
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
              }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
              }
        } else {
            return response ()->json (['pesan'=>'Gagal Posting, Cek Status RKPD, Program, Kegiatan, dan Aktivitas..','status_pesan'=>'0']);
        }
    }

public function editBelanja(Request $req)
    {
        $cekProgramRkpd = DB::SELECT('SELECT e.id_aktivitas_pd, c.hak_akses, c.status_data AS status_rkpd, b.status_data AS status_program, c.status_data AS status_kegiatan, e.status_data AS status_aktivitas
            FROM trx_anggaran_aktivitas_pd e
            INNER JOIN trx_anggaran_pelaksana_pd d ON e.id_pelaksana_pd = d.id_pelaksana_pd
            INNER JOIN trx_anggaran_kegiatan_pd a ON d.id_kegiatan_pd = a.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd b ON a.id_program_pd = b.id_program_pd
            INNER JOIN trx_anggaran_pelaksana c ON b.id_pelaksana_anggaran = c.id_pelaksana_anggaran
            WHERE e.id_aktivitas_pd='.$req->id_aktivitas_pd);

            $data = TrxAnggaranBelanjaPd::find($req->id_belanja_pd);
            $data->no_urut= $req->no_urut;
            $data->id_zona_ssh= $req->id_zona_ssh;
            $data->id_aktivitas_asb= $req->id_aktivitas_asb;
            $data->id_item_ssh= $req->id_item_ssh;
            $data->id_rekening_ssh= $req->id_rekening_ssh;
            $data->uraian_belanja= $req->uraian_belanja;
            $data->volume_1= $req->volume_1;
            $data->id_satuan_1= $req->id_satuan_1;
            $data->volume_2= $req->volume_2;
            $data->id_satuan_2= $req->id_satuan_2;
            $data->harga_satuan= $req->harga_satuan;
            $data->jml_belanja= $req->jml_belanja;
            $data->koefisien= $req->koefisien;
            $data->status_data= 0;

        if($cekProgramRkpd != NULL && $cekProgramRkpd[0]->status_rkpd == 0 && $cekProgramRkpd[0]->status_program == 0 && $cekProgramRkpd[0]->status_kegiatan == 0 && $cekProgramRkpd[0]->status_aktivitas == 0) {
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
              }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
              }
        } else {
            return response ()->json (['pesan'=>'Gagal Posting, Cek Status RKPD, Program, Kegiatan, dan Aktivitas..','status_pesan'=>'0']);
        }
    }

public function hapusBelanja(Request $req)
      {
        TrxAnggaranBelanjaPd::where('id_belanja_pd',$req->id_belanja_pd)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

public function addIndikatorProg(Request $req)
    {
            $data = new TrxAnggaranProgIndikatorPd;
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

        $cek = DB::SELECT('SELECT * from trx_anggaran_program_pd WHERE id_program_pd='.$req->id_program_pd);
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
            $data = TrxAnggaranProgIndikatorPd::find($req->id_indikator_program);
            $data->no_urut = $req->no_urut ;
            // $data->id_program_pd = $req->id_program_pd ;
            $data->kd_indikator = $req->kd_indikator ;
            $data->uraian_indikator_program =  $req->uraian_indikator_program ;
            $data->tolok_ukur_indikator = $req->tolok_ukur_indikator ;
            $data->target_renja =  $req->target_renja ;
            $data->id_satuan_ouput =  $req->id_satuan_output ;

        $cek = DB::SELECT('SELECT * from trx_anggaran_program_pd WHERE id_program_pd='.$req->id_program_pd);
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
        $data = TrxAnggaranProgIndikatorPd::find($req->id_indikator_program);
        $data->status_data = $req->status_data;
        
        $cek = DB::SELECT('SELECT a.status_data from trx_anggaran_program_pd a
            INNER JOIN trx_anggaran_prog_indikator_pd b ON a.id_program_pd = b.id_program_pd
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
        
        $cek = DB::SELECT('SELECT a.status_data from trx_anggaran_program_pd a
            INNER JOIN trx_anggaran_prog_indikator_pd b ON a.id_program_pd = b.id_program_pd
            WHERE b.id_indikator_program='.$req->id_indikator_program.' GROUP BY a.status_data, b.id_indikator_program');
            
        if($cek[0]->status_data == 0){            
            TrxAnggaranProgIndikatorPd::where('id_indikator_program',$req->id_indikator_program)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1'] );
        } else {
            return response ()->json (['pesan'=>'Data Gagal Status Program telah diposting','status_pesan'=>'0']);
        }
    }

public function addIndikatorKeg(Request $req)
    {
            $data = new TrxAnggaranKegIndikatorPd;
            $data->tahun_renja = $req->tahun_renja;
            $data->no_urut = $req->no_urut ;
            $data->id_kegiatan_pd = $req->id_kegiatan_pd ;
            $data->id_perubahan = 0 ;
            $data->kd_indikator = $req->kd_indikator ;
            $data->uraian_indikator_kegiatan =  $req->uraian_indikator_kegiatan ;
            $data->tolok_ukur_indikator = $req->tolok_ukur_indikator ;
            $data->target_renstra = 0 ;
            $data->target_renja =  $req->target_renja ;
            $data->id_satuan_output =  $req->id_satuan_ouput ;
            $data->status_data= 0;
            $data->sumber_data= 1;
        
        $cek = DB::SELECT('SELECT * FROM trx_anggaran_kegiatan_pd WHERE id_kegiatan_pd = '.$req->id_kegiatan_pd);
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
        $cek = DB::SELECT('SELECT * FROM trx_anggaran_kegiatan_pd WHERE id_kegiatan_pd = '.$req->id_kegiatan_pd);

            $data = TrxAnggaranKegIndikatorPd::find($req->id_indikator_kegiatan);
            $data->no_urut = $req->no_urut ;
            $data->kd_indikator = $req->kd_indikator ;
            $data->uraian_indikator_kegiatan =  $req->uraian_indikator_kegiatan ;
            $data->tolok_ukur_indikator = $req->tolok_ukur_indikator ;
            $data->target_renja =  $req->target_renja ;
            $data->id_satuan_output =  $req->id_satuan_output ;

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
        $cek = DB::SELECT('SELECT a.status_data FROM trx_anggaran_kegiatan_pd a
            INNER JOIN trx_anggaran_keg_indikator_pd b ON a.id_kegiatan_pd = b.id_kegiatan_pd
            WHERE b.id_indikator_kegiatan = '.$req->id_indikator_kegiatan.' group by a.status_data,b.id_indikator_kegiatan');
                   
            $data = TrxAnggaranKegIndikatorPd::find($req->id_indikator_kegiatan);
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
        $cek = DB::SELECT('SELECT a.status_data FROM trx_anggaran_kegiatan_pd a
            INNER JOIN trx_anggaran_keg_indikator_pd b ON a.id_kegiatan_pd = b.id_kegiatan_pd
            WHERE b.id_indikator_kegiatan = '.$req->id_indikator_kegiatan.' group by a.status_data,b.id_indikator_kegiatan');

        if($cek[0]->status_data == 0){
            TrxAnggaranKegIndikatorPd::where('id_indikator_kegiatan',$req->id_indikator_kegiatan)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1'] );
        } else {
            return response ()->json (['pesan'=>'Data Gagal Status Kegiatan telah diposting','status_pesan'=>'0']);
        }
    }
    
}
