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
use App\Models\TrxForumSkpdProgramRanwal;
use App\Models\TrxForumSkpdProgram;
use App\Models\TrxForumSkpdProgramIndikator;
use App\Models\TrxForumSkpd;
use App\Models\TrxForumSkpdKegiatanIndikator;
use App\Models\TrxForumSkpdAktivitas;
use App\Models\TrxForumSkpdPelaksana;
use App\Models\TrxForumSkpdLokasi;
use App\Models\TrxForumSkpdUsulan;
use App\Models\TrxForumSkpdBelanja;
use App\Models\RefUnit;
use App\Models\RefSubUnit;


class TrxForumSkpdVerifikasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
        // if(Auth::check()){ 
            $unit = RefSubUnit::select();
            if(isset(Auth::user()->getUserSubUnit)){
                foreach(Auth::user()->getUserSubUnit as $data){
                    $unit->orWhere(['id_unit' => $data->kd_unit]);                
                }
            }
            $unit = $unit->get();
            return view('forumskpd.verifikasi');
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function getProgramRkpdForum(Request $req)
    {
        $getRenja=DB::select('SELECT (@id:=@id+1) as no_urut, a.tahun_rkpd,a.id_rkpd_ranwal,a.jenis_belanja,a.uraian_program_rpjmd, COALESCE(SUM(a.pagu_ranwal),0) AS jml_pagu_ranwal, 
                    COUNT(DISTINCT(b.id_unit)) AS jml_unit ,COUNT(DISTINCT(c.id_forum_program)) AS jml_prog_opd, COALESCE(SUM(c.pagu_forum),0) AS jml_pagu_program, COUNT(DISTINCT(d.id_forum_skpd)) AS jml_keg_opd, 
                    COALESCE(SUM(d.pagu_forum),0) AS jml_pagu_kegiatan, COALESCE(SUM(e.jml_aktivitas),0) AS jml_aktivitas_opd, COALESCE(SUM(e.jml_pagu_aktivitas),0) AS jml_pagu_aktivitas,
                    b.status_data,
                    CASE b.status_data
                        WHEN 0 THEN "fa fa-question"
                        WHEN 1 THEN "fa fa-check-square-o"
                        WHEN 2 THEN "fa fa-thumbs-o-up"
                        ELSE "fa fa-exclamation"
                    END AS status_icon,
                    CASE b.status_data
                        WHEN 0 THEN "red"
                        WHEN 1 THEN "green"
                        WHEN 2 THEN "blue"
                        ELSE "red"
                    END AS warna
                    FROM trx_rkpd_ranwal AS a
                    INNER JOIN trx_forum_skpd_program_ranwal AS b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal
                    LEFT OUTER JOIN (SELECT id_forum_rkpdprog,id_forum_program,pagu_forum FROM trx_forum_skpd_program WHERE status_data = 1) AS c ON b.id_forum_rkpdprog = c.id_forum_rkpdprog
                    LEFT OUTER JOIN (SELECT a.id_forum_program, a.id_forum_skpd, a.pagu_forum
                    FROM trx_forum_skpd a WHERE a.status_data = 1) AS d ON c.id_forum_program = d.id_forum_program
                    LEFT OUTER JOIN (SELECT a.tahun_forum, b.id_aktivitas_forum, 
                    COUNT(a.id_aktivitas_forum) as jml_aktivitas,
                    SUM(a.pagu_aktivitas_forum) as jml_pagu_aktivitas
                    FROM trx_forum_skpd_aktivitas a 
                    INNER JOIN trx_forum_skpd_pelaksana b ON a.id_forum_skpd = b.id_pelaksana_forum
                    WHERE a.status_data = 1
                    GROUP BY a.tahun_forum, b.id_aktivitas_forum, a.status_data) AS e ON d.id_forum_skpd = e.id_aktivitas_forum,(SELECT @id:=0) z
                    WHERE a.tahun_rkpd = '.$req->tahun.' GROUP BY a.tahun_rkpd,a.id_rkpd_ranwal,a.jenis_belanja,a.uraian_program_rpjmd, b.status_data');

          return DataTables::of($getRenja)
            ->addColumn('action',function($getRenja){
                if($getRenja->status_data==0)
                return '
                    <button type="button" class="post-ProgRKPD btn btn-info btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-check fa-fw fa-lg"></i></span>Posting Bappeda</button>
                    ' ;
                if($getRenja->status_data==1)
                return '
                    <button type="button" class="post-ProgRKPD btn btn-danger btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-times fa-fw fa-lg"></i></span>un-Posting Bappeda</button>
                    ' ;

            }) 
        ->make(true);
    }

    public function getUnitForumPD($id_rkpd_ranwal)
    {
        $getRenja = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.tahun_forum,a.id_rkpd_ranwal,a.jenis_belanja, a.id_unit, b.nm_unit,a.id_bidang,f.nm_bidang,a.id_forum_rkpdprog,
                    COUNT(DISTINCT(b.id_unit)) AS jml_unit ,COUNT(DISTINCT(c.id_forum_program)) AS jml_prog_opd, COALESCE(SUM(c.pagu_forum),0) AS jml_pagu_program, COUNT(DISTINCT(d.id_forum_skpd)) AS jml_keg_opd, 
                    COALESCE(SUM(d.pagu_forum),0) AS jml_pagu_kegiatan, COALESCE(SUM(e.jml_aktivitas),0) AS jml_aktivitas_opd, COALESCE(SUM(e.jml_pagu_aktivitas),0) AS jml_pagu_aktivitas
                    FROM trx_forum_skpd_program_ranwal AS a 
                    INNER JOIN ref_unit AS b ON a.id_unit = b.id_unit
                    INNER JOIN ref_bidang AS f ON a.id_bidang = f.id_bidang
                    LEFT OUTER JOIN (SELECT id_forum_rkpdprog,id_forum_program,pagu_forum FROM trx_forum_skpd_program WHERE status_data = 1) AS c ON a.id_forum_rkpdprog = c.id_forum_rkpdprog
                    LEFT OUTER JOIN (SELECT a.id_forum_program, a.id_forum_skpd, a.pagu_forum
                    FROM trx_forum_skpd a WHERE a.status_data = 1) AS d ON c.id_forum_program = d.id_forum_program
                    LEFT OUTER JOIN (SELECT a.tahun_forum, b.id_aktivitas_forum, 
                    COUNT(a.id_aktivitas_forum) as jml_aktivitas,
                    SUM(a.pagu_aktivitas_forum) as jml_pagu_aktivitas
                    FROM trx_forum_skpd_aktivitas a 
                    INNER JOIN trx_forum_skpd_pelaksana b ON a.id_forum_skpd = b.id_pelaksana_forum
                    WHERE a.status_data = 1
                    GROUP BY a.tahun_forum, b.id_aktivitas_forum, a.status_data) AS e ON d.id_forum_skpd = e.id_aktivitas_forum
                                ,(SELECT @id:=0) z
                    WHERE a.id_rkpd_ranwal='.$id_rkpd_ranwal.' GROUP BY a.tahun_forum,a.id_rkpd_ranwal,a.jenis_belanja, a.id_unit, b.nm_unit,a.id_bidang,f.nm_bidang,a.id_forum_rkpdprog');

        return Datatables::of($getRenja)
            ->make(true);
    }

    public function getProgForumPD($id_forum_rkpdprog)
    {
        $getRenja = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.tahun_forum,a.id_forum_rkpdprog, a.uraian_program_renstra, COALESCE(SUM(a.pagu_forum),0) AS jml_pagu_program, COUNT(DISTINCT(d.id_forum_skpd)) AS jml_keg_opd, 
                        COALESCE(SUM(d.pagu_forum),0) AS jml_pagu_kegiatan, COALESCE(SUM(e.jml_aktivitas),0) AS jml_aktivitas_opd, COALESCE(SUM(e.jml_pagu_aktivitas),0) AS jml_pagu_aktivitas
                        FROM trx_forum_skpd_program AS a 
                        LEFT OUTER JOIN (SELECT a.id_forum_program, a.id_forum_skpd, a.pagu_forum
                        FROM trx_forum_skpd a WHERE a.status_data = 1) AS d ON a.id_forum_program = d.id_forum_program
                        LEFT OUTER JOIN (SELECT a.tahun_forum, b.id_aktivitas_forum, 
                        COUNT(a.id_aktivitas_forum) as jml_aktivitas,
                        SUM(a.pagu_aktivitas_forum) as jml_pagu_aktivitas
                        FROM trx_forum_skpd_aktivitas a 
                        INNER JOIN trx_forum_skpd_pelaksana b ON a.id_forum_skpd = b.id_pelaksana_forum
                        WHERE a.status_data = 1
                        GROUP BY a.tahun_forum, b.id_aktivitas_forum, a.status_data) AS e ON d.id_forum_skpd = e.id_aktivitas_forum
                        ,(SELECT @id:=0) z
                        WHERE a.status_data = 1 AND a.id_forum_rkpdprog='.$id_forum_rkpdprog.' GROUP BY a.tahun_forum,a.id_forum_rkpdprog, a.uraian_program_renstra');

        return Datatables::of($getRenja)
            ->make(true);
    }

public function postBappeda(Request $req)
    {
        // $data = TrxForumSkpdProgramRanwal::find('id_rkpd_ranwal',$req->id_rkpd_ranwal);
        // $data->status_data= $req->status_data;

        // $cek = DB::SELECT('SELECT c.id_forum_skpd, c.status_data, c.pagu_forum, b.jml_pagu_aktivitas, c.pagu_forum - b.jml_pagu_aktivitas AS selisih 
        //     FROM (SELECT a.tahun_forum, b.id_aktivitas_forum,
        //                 SUM(a.pagu_aktivitas_forum) AS jml_pagu_aktivitas
        //                 FROM trx_forum_skpd_aktivitas a 
        //                 INNER JOIN trx_forum_skpd_pelaksana b ON a.id_forum_skpd = b.id_pelaksana_forum
        //                 WHERE a.status_data = 1
        //                 GROUP BY a.tahun_forum, b.id_aktivitas_forum, a.status_data) b
        //     INNER JOIN trx_forum_skpd c ON b.id_aktivitas_forum = c.id_forum_skpd
        //     WHERE c.id_rkpd_ranwal='.$req->id_rkpd_ranwal);

        // $cekStatus = 

        // if($cek[0]->selisih == 0){
            $data = DB::UPDATE('UPDATE trx_forum_skpd_program_ranwal SET status_data='.$req->status_data.' WHERE id_rkpd_ranwal='.$req->id_rkpd_ranwal);
            if($data !=0){
                return response ()->json (['pesan'=>'Data Berhasil Diposting','status_pesan'=>'1']);
            } else {
                return response ()->json (['pesan'=>'Data Gagal Diposting ('.$error_code.')','status_pesan'=>'0']);
            }
        // } else {
             // return response ()->json (['pesan'=>'Data Jumlah Pagu Kegiatan dengan Aktivitas Tidak Sama','status_pesan'=>'0']);
        // }
    }   
}
