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
use App\Models\TrxRenjaRanwalDokumen;


class TrxRanwalRenjaDokController extends Controller
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
                FROM trx_renja_ranwal_dokumen AS a
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
            return view('ranwalrenja.doku');
        // } else {
            // return view ( 'errors.401' );
        // }
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
            $data = new TrxRenjaRanwalDokumen;
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
            $data = TrxRenjaRanwalDokumen::find($req->id_dokumen_rkpd);
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
        $CheckProgram=DB::SELECT('SELECT a.tahun_renja, a.id_unit, SUM(IF(a.status_data=0,1,0)) as jml_0, SUM(IF(a.status_data=1,1,0)) as jml_1
            FROM trx_renja_ranwal_program AS a
            WHERE a.tahun_renja = '.$req->tahun_rkpd.' AND a.id_unit='.$req->id_unit.' GROUP BY a.tahun_renja, a.id_unit');
        
        $cek = DB::SELECT('SELECT a.tahun_renja, a.id_unit, (COALESCE(a.jml_rancangan,0) + COALESCE(b.jml_musren_rw,0) + COALESCE(c.jml_musrendes,0) + COALESCE(d.jml_musrencam,0)) as jml_cek FROM 
                (SELECT a.tahun_renja, a.id_unit, count(b.id_renja_ranwal) as jml_rancangan
                FROM trx_renja_ranwal_program AS a 
                INNER JOIN trx_renja_rancangan_program AS b ON a.id_renja_program = b.id_renja_ranwal
                GROUP BY a.tahun_renja, a.id_unit) AS a
                LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_unit, count(d.id_renja) as jml_musren_rw
                FROM trx_renja_ranwal_program AS a 
                INNER JOIN trx_renja_ranwal_program AS b ON a.id_renja_program = b.id_renja_ranwal
                INNER JOIN trx_renja_ranwal_kegiatan AS c ON b.id_renja_program = c.id_renja_program
                INNER JOIN trx_musrendes_rw AS d ON c.id_renja = d.id_renja
                GROUP BY a.tahun_renja, a.id_unit) AS b ON a.tahun_renja = b.tahun_renja AND a.id_unit = b.id_unit
                LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_unit, count(d.id_renja) as jml_musrendes
                FROM trx_renja_ranwal_program AS a 
                INNER JOIN trx_renja_ranwal_program AS b ON a.id_renja_program = b.id_renja_ranwal
                INNER JOIN trx_renja_ranwal_kegiatan AS c ON b.id_renja_program = c.id_renja_program
                INNER JOIN trx_musrendes AS d ON c.id_renja = d.id_renja
                GROUP BY a.tahun_renja, a.id_unit) AS c ON a.tahun_renja = c.tahun_renja AND a.id_unit = c.id_unit
                LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_unit, count(d.id_renja) as jml_musrencam
                FROM trx_renja_ranwal_program AS a 
                INNER JOIN trx_renja_ranwal_program AS b ON a.id_renja_program = b.id_renja_ranwal
                INNER JOIN trx_renja_ranwal_kegiatan AS c ON b.id_renja_program = c.id_renja_program
                INNER JOIN trx_musrencam AS d ON c.id_renja = d.id_renja
                GROUP BY a.tahun_renja, a.id_unit) AS d ON a.tahun_renja = d.tahun_renja AND a.id_unit = d.id_unit
                WHERE a.tahun_renja = '.$req->tahun_rkpd.' AND a.id_unit='.$req->id_unit);

        if($req->flag==1){
                if($CheckProgram[0]->jml_1==0){
                    return response ()->json (['pesan'=>'Data Gagal Diposting, Silahkan Cek Posting Program Ranwal RKPD','status_pesan'=>'0']);
                } else {        
                    $data = DB::UPDATE('UPDATE trx_renja_ranwal_dokumen SET flag ='.$req->flag.' WHERE tahun_ranwal='.$req->tahun_rkpd.' AND id_dokumen_ranwal='.$req->id_dokumen_rkpd);
                       
                    if($data != 0){
                        $dataProg=DB::UPDATE('UPDATE trx_renja_ranwal_program SET status_data ='.$req->status.', id_dokumen ='.$req->id_dokumen_rkpd.' WHERE tahun_renja='.$req->tahun_rkpd.' AND status_data='.$req->status_awal.' AND id_unit='.$req->id_unit);
                
                        if($dataProg != 0){
                            return response ()->json (['pesan'=>'Data Berhasil Posting','status_pesan'=>'1']);
                        } else {
                            return response ()->json (['pesan'=>'Data Gagal Diposting (1cprPD)','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Diposting (0cdrPD)','status_pesan'=>'0']);
                    }
                }
        }
        if($req->flag==0){
            if($cek == null){
            $data = DB::UPDATE('UPDATE trx_renja_ranwal_dokumen SET flag ='.$req->flag.' WHERE tahun_ranwal='.$req->tahun_rkpd.' AND id_dokumen_ranwal='.$req->id_dokumen_rkpd);                       
                    if($data != 0){
                        $dataProg=DB::UPDATE('UPDATE trx_renja_ranwal_program SET status_data ='.$req->status.' and id_dokumen ='.$req->id_dokumen_rkpd.' WHERE tahun_renja='.$req->tahun_rkpd.' AND status_data='.$req->status_awal.' AND id_unit='.$req->id_unit);
                
                        if($dataProg != 0){
                            return response ()->json (['pesan'=>'Data Berhasil Un-Posting','status_pesan'=>'1']);
                        } else {
                            return response ()->json (['pesan'=>'Data Gagal Un-posting (U1cprPD)','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Unposting (U0cdrPD)','status_pesan'=>'0']);
                    }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Proses, Dokumen sudah dipakai di - Rancangan Renja dan atau Musrenbang (0cdrPD)','status_pesan'=>'0']);
            }
        }
    }

    public function hapusDokumen(Request $req)
    {
        $result = TrxRenjaRanwalDokumen::destroy($req->id_dokumen_rkpd);
    
        if($result != 0){
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
        }
    }

    public function checkPosting($tahun, $id_unit)
    {
       $CheckProgram=DB::SELECT('SELECT a.tahun_renja, a.id_unit, SUM(IF(a.status_data=0,1,0)) as jml_0, SUM(IF(a.status_data=1,1,0)) as jml_1
            FROM trx_renja_ranwal_program AS a
            WHERE a.tahun_renja = '.$tahun.' AND a.id_unit='.$id_unit.' 
            GROUP BY a.tahun_renja, a.id_unit');

       return ($CheckProgram);
    }

          
}
