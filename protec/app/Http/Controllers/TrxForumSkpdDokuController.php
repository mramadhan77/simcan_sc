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
use App\Models\TrxForumSkpdDokumen;
use App\Models\RefUnit;
use App\Models\RefSubUnit;


class TrxForumSkpdDokuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getDataDokumen($id_unit)
    {
        $getDataDokumen = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_dokumen_ranwal,a.nomor_ranwal,a.tanggal_ranwal,a.tahun_ranwal,
                a.uraian_perkada, a.id_unit_renja, a.jabatan_tandatangan,a.nama_tandatangan,a.nip_tandatangan,a.flag,b.nm_unit,
                CASE a.flag
                    WHEN 0 THEN "fa fa-question"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_icon,
                CASE a.flag
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna
                FROM trx_forum_skpd_dokumen AS a
                INNER JOIN ref_unit AS b ON a.id_unit_renja = b.id_unit,
                (SELECT @id:=0) z WHERE a.id_unit_renja='.$id_unit);

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

    public function index(Request $request, Builder $htmlBuilder)
    {
        // if(Auth::check()){ 
            return view('forumskpd.doku');
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function addDokumen(Request $req)
    {
        try{
            $data = new TrxForumSkpdDokumen;
            $data->nomor_ranwal = $req->nomor_rkpd ;
            $data->tanggal_ranwal = $req->tanggal_rkpd ;
            $data->tahun_ranwal = $req->tahun_rkpd ;
            $data->uraian_perkada = $req->uraian_perkada ;
            $data->id_unit_renja = $req->id_unit_perencana ;
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
            $data = TrxForumSkpdDokumen::find($req->id_dokumen_rkpd);
            $data->nomor_ranwal = $req->nomor_rkpd ;
            $data->tanggal_ranwal = $req->tanggal_rkpd ;
            $data->tahun_ranwal = $req->tahun_rkpd ;
            $data->uraian_perkada = $req->uraian_perkada ;
            $data->id_unit_renja = $req->id_unit_perencana ;
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

    public function postDokumen(Request $req)
    {
        $cekForum=DB::SELECT('SELECT tahun_forum, id_unit, COALESCE(count(id_forum_program),0) as jml FROM trx_rkpd_rancangan_program_pd WHERE tahun_forum = '.$req->tahun_rkpd.' 
            AND id_unit='.$req->id_unit.' GROUP BY tahun_forum, id_unit');
        $CheckProgram=DB::SELECT('SELECT a.tahun_forum, a.id_unit, SUM(IF(a.status_data=0,1,0)) as jml_0, SUM(IF(a.status_data=1,1,0)) as jml_1
            FROM trx_forum_skpd_program AS a
            WHERE a.tahun_forum = '.$req->tahun_rkpd.' AND a.id_unit='.$req->id_unit.' GROUP BY a.tahun_forum, a.id_unit');

        if($req->flag==1){
                if($CheckProgram[0]->jml_1==0){
                    return response ()->json (['pesan'=>'Data Gagal Diposting, Silahkan Cek Posting Program Forum SKPD','status_pesan'=>'0']);
                } else {        
                    $data = DB::UPDATE('UPDATE trx_forum_skpd_dokumen SET flag ='.$req->flag.' WHERE tahun_ranwal='.$req->tahun_rkpd.' AND id_dokumen_ranwal='.$req->id_dokumen_rkpd);
                       
                    if($data != 0){
                        $dataRKPD=DB::UPDATE('UPDATE trx_forum_skpd_program_ranwal SET status_data=1 WHERE id_unit='.$req->id_unit.' AND tahun_forum='.$req->tahun_rkpd);
                        $dataProg=DB::UPDATE('UPDATE trx_forum_skpd_program SET status_data ='.$req->status.', id_dokumen ='.$req->id_dokumen_rkpd.' WHERE tahun_forum='.$req->tahun_rkpd.' AND status_data='.$req->status_awal.' AND id_unit='.$req->id_unit);
                
                        if($dataProg != 0){
                            return response ()->json (['pesan'=>'Data Berhasil Posting','status_pesan'=>'1']);
                        } else {
                            return response ()->json (['pesan'=>'Data Gagal Diposting (1cprPD)','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Diposting TEST (0cdrPD)','status_pesan'=>'0']);
                    }
                }
        } else {
            if($cekForum == null) {
            $data = DB::UPDATE('UPDATE trx_forum_skpd_dokumen SET flag ='.$req->flag.' WHERE tahun_ranwal='.$req->tahun_rkpd.' AND id_dokumen_ranwal='.$req->id_dokumen_rkpd);                       
                    if($data != 0){
                        $dataRKPD=DB::UPDATE('UPDATE trx_forum_skpd_program_ranwal SET status_data=0 WHERE id_unit='.$req->id_unit.' AND tahun_forum='.$req->tahun_rkpd);
                        $dataProg=DB::UPDATE('UPDATE trx_forum_skpd_program SET status_data ='.$req->status.', id_dokumen ='.$req->id_dokumen_rkpd.' WHERE tahun_forum='.$req->tahun_rkpd.' AND status_data='.$req->status_awal.' AND id_unit='.$req->id_unit);
                
                        if($dataProg != 0){
                            return response ()->json (['pesan'=>'Data Berhasil Un-Posting','status_pesan'=>'1']);
                        } else {
                            return response ()->json (['pesan'=>'Data Gagal Un-posting (U1cprPD)','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Unposting (U0cdrPD)','status_pesan'=>'0']);
                    }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Proses, Dokumen sudah dipakai di - Rancangan RKPD (0cdrPD)','status_pesan'=>'0']);
            }
        }
    }

    public function hapusDokumen(Request $req)
    {
        $result = TrxForumSkpdDokumen::destroy($req->id_dokumen_rkpd);
    
        if($result != 0){
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
        }
    }

    public function checkPosting($tahun, $id_unit)
    {
       $CheckProgram=DB::SELECT('SELECT a.tahun_forum, a.id_unit, SUM(IF(a.status_data=0,1,0)) as jml_0, SUM(IF(a.status_data=1,1,0)) as jml_1
            FROM trx_forum_skpd_program AS a
            WHERE a.tahun_forum = '.$tahun.' AND a.id_unit='.$id_unit.' 
            GROUP BY a.tahun_forum, a.id_unit');

       return ($CheckProgram);
    }
    
}
