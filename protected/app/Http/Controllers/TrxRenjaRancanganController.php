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
use App\Models\TrxRenjaRancanganProgram;
use App\Models\TrxRenjaRancanganProgramIndikator;
use App\Models\TrxRenjaRancangan;
use App\Models\TrxRenjaRancanganIndikator;
use App\Models\TrxRenjaRancanganPelaksana;
use App\Models\RefUnit;
use App\Models\RefSubUnit;
use Auth;

class TrxRenjaRancanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function loadData(Request $request, Builder $htmlBuilder, $id = null)
    {
        // if(Auth::check()){ 
            $unit = RefSubUnit::select();
            if(isset(Auth::user()->getUserSubUnit)){
                foreach(Auth::user()->getUserSubUnit as $data){
                    $unit->orWhere(['id_unit' => $data->kd_unit]);                
                }
            }
            $unit = $unit->get();
            return view('renja.load')->with(compact('unit'));
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function getUnit(Request $request){
        $unit = RefUnit::select();
        if(isset(Auth::User()->getUserSubUnit)){
            foreach(Auth::User()->getUserSubUnit as $data){
                $unit->Where(['id_unit' => $data->kd_unit]);                
            }
        }
        $unit = $unit->get();
        if($request->ajax()){
            return json_encode($unit);
        }
    }

    protected function getUserSub(){
        /* value references
        * $this->userSub return an array
        * $this->count return int
        */
        $userSub = Auth::User()->getUserSubUnit()->get();
        $countUserSub = count($userSub);
        return (object)['userSub' => $userSub, 'count' => $countUserSub];
    }

    protected function getUserVisi(){
        /* count User sub first
        * if user sub not exist, pupolate all
        * if exist, populate only with user sub criteria
        */
        $user = $this->getUserSub();

        $renstraVisi = \App\Models\TrxRenstraVisi::select();
        if($user->count != 0){
            foreach ($user->userSub as $data) {
                // criteria for renstra visi based on user sub
                $renstraVisi->orWhere(['id_unit' => $data->kd_unit]);
            }
        }
        $renstraVisi = $renstraVisi->get();
        return $renstraVisi;
    }

    public function getTahunKe($tahun)
    {
        
        $tahunke=DB::select('SELECT a.tahun_ke FROM (SELECT tahun_1 as tahun, 1 as tahun_ke from ref_tahun
                UNION SELECT tahun_2 as tahun, 2 as tahun_ke from ref_tahun
                UNION SELECT tahun_3 as tahun, 3 as tahun_ke from ref_tahun
                UNION SELECT tahun_4 as tahun, 4 as tahun_ke from ref_tahun
                UNION SELECT tahun_5 as tahun, 5 as tahun_ke from ref_tahun) a
                WHERE a.tahun='.$tahun);
        foreach($tahunke as $tahunX) {
            return $tahunX->tahun_ke;
        }
    }

    public function transProgramRenja(Request $req)
    {
        $transProgram=DB::INSERT('INSERT INTO trx_renja_rancangan_program (tahun_renja, no_urut, id_renja_program, jenis_belanja, id_renja_ranwal, id_rkpd_ranwal, id_program_rpjmd, id_bidang, id_unit, id_visi_renstra, id_misi_renstra, id_tujuan_renstra, id_sasaran_renstra, id_program_renstra, uraian_program_renstra, id_program_ref, pagu_tahun_ranwal, pagu_tahun_renstra, status_program_rkpd, sumber_data_rkpd, sumber_data, status_pelaksanaan, ket_usulan, status_data)
            SELECT a.tahun_renja,a.no_urut, a.id_renja_program, a.jenis_belanja, a.id_renja_program, a.id_rkpd_ranwal, a.id_program_rpjmd, a.id_bidang,a.id_unit,a.id_visi_renstra,a.id_misi_renstra,a.id_tujuan_renstra, a.id_sasaran_renstra,a.id_program_renstra,a.uraian_program_renstra,a.id_program_ref,a.pagu_tahun_ranwal, a.pagu_tahun_renstra,a.status_program_rkpd,a.sumber_data_rkpd,0 as sumber_data,a.status_pelaksanaan,a.ket_usulan,0 as status_data FROM trx_renja_ranwal_program AS a WHERE a.id_renja_program = '.$req->id_rkpd_ranwal.' AND a.id_unit = '.$req->id_unit.' AND a.tahun_renja = '.$req->tahun_renja);

        if($transProgram == 0){
            return response ()->json (['pesan'=>'Data Gagal Import Program Renja','status_pesan'=>'0']);
        } else {
            $transProgramIndikator=DB::INSERT('INSERT INTO trx_renja_rancangan_program_indikator (tahun_renja, no_urut, id_renja_program, id_program_renstra, id_indikator_program_renja, id_perubahan, kd_indikator, uraian_indikator_program_renja, tolok_ukur_indikator, target_renstra, target_renja, indikator_output,id_satuan_output,indikator_input,target_input,id_satuan_input, status_data, sumber_data)
                SELECT a.tahun_renja, a.no_urut,a.id_renja_program,a.id_program_renstra,a.id_indikator_program_renja,
                a.id_perubahan,a.kd_indikator,a.uraian_indikator_program_renja,a.tolok_ukur_indikator,a.target_renstra,
                a.target_renja,a.indikator_output,a.id_satuan_output,a.indikator_input,a.target_input,a.id_satuan_input,
                0 as status_data,0 as sumber_data
                FROM trx_renja_ranwal_program_indikator AS a
                INNER JOIN trx_renja_ranwal_program As b ON a.tahun_renja = b.tahun_renja AND a.id_renja_program = b.id_renja_program
                WHERE a.id_renja_program = '.$req->id_rkpd_ranwal.' AND b.id_unit = '.$req->id_unit.' AND a.tahun_renja = '.$req->tahun_renja)
                ;
            if($transProgramIndikator == 0){
                return response ()->json (['pesan'=>'Data Gagal Import Indikator Program Renja','status_pesan'=>'0']);
            } else {
                $transKegiatan=DB::INSERT('INSERT INTO trx_renja_rancangan(tahun_renja, no_urut, id_renja, id_renja_program, id_rkpd_renstra, id_rkpd_ranwal, id_unit, id_visi_renstra, id_misi_renstra, id_tujuan_renstra, id_sasaran_renstra, id_program_renstra, uraian_program_renstra, id_kegiatan_renstra, id_kegiatan_ref, uraian_kegiatan_renstra, pagu_tahun_renstra, pagu_tahun_kegiatan, pagu_tahun_selanjutnya, status_pelaksanaan_kegiatan, pagu_musrenbang, sumber_data, ket_usulan, status_data, status_rancangan)
                    SELECT a.tahun_renja, a.no_urut, a.id_renja, a.id_renja_program, a.id_rkpd_renstra, a.id_rkpd_ranwal, a.id_unit, a.id_visi_renstra, a.id_misi_renstra, a.id_tujuan_renstra, a.id_sasaran_renstra, a.id_program_renstra, a.uraian_program_renstra, a.id_kegiatan_renstra, a.id_kegiatan_ref, a.uraian_kegiatan_renstra, a.pagu_tahun_renstra, a.pagu_tahun_kegiatan, a.pagu_tahun_selanjutnya, a.status_pelaksanaan_kegiatan, a.pagu_musrenbang, 0 as sumber_data, a.ket_usulan, 0 as status_data, 0 as status_rancangan
                    FROM trx_renja_ranwal_kegiatan AS a
                    INNER JOIN trx_renja_ranwal_program As b ON a.tahun_renja = b.tahun_renja AND a.id_renja_program = b.id_renja_program
                    WHERE (a.status_pelaksanaan_kegiatan <> 2 AND a.status_pelaksanaan_kegiatan <> 3) AND a.id_renja_program = '.$req->id_rkpd_ranwal.' AND b.id_unit = '.$req->id_unit.' AND a.tahun_renja = '.$req->tahun_renja)
                    ;
                if($transKegiatan == 0){
                    return response ()->json (['pesan'=>'Data Gagal Import Kegiatan Renja','status_pesan'=>'0']);
                } else {
                    $transKegiatanIndikator=DB::INSERT('INSERT INTO trx_renja_rancangan_indikator(tahun_renja, no_urut, id_renja, id_indikator_kegiatan_renja, id_perubahan, kd_indikator, uraian_indikator_kegiatan_renja, tolok_ukur_indikator, angka_tahun, angka_renstra, id_satuan_output, status_data, sumber_data)
                        SELECT a.tahun_renja, a.no_urut, a.id_renja, a.id_indikator_kegiatan_renja, a.id_perubahan, a.kd_indikator, a.uraian_indikator_kegiatan_renja, a.tolok_ukur_indikator, a.angka_tahun, a.angka_renstra, a.id_satuan_output, 0 as status_data,0 as sumber_data
                        FROM trx_renja_ranwal_kegiatan_indikator AS a
                        INNER JOIN trx_renja_ranwal_kegiatan AS c ON a.tahun_renja = c.tahun_renja AND a.id_renja = c.id_renja
                        INNER JOIN trx_renja_ranwal_program As b ON c.tahun_renja = b.tahun_renja AND c.id_renja_program = b.id_renja_program
                        WHERE (c.status_pelaksanaan_kegiatan <> 2 AND c.status_pelaksanaan_kegiatan <> 3) AND b.id_renja_program = '.$req->id_rkpd_ranwal.' AND b.id_unit = '.$req->id_unit.' AND b.tahun_renja = '.$req->tahun_renja)
                        ;
                    if($transKegiatanIndikator == 0){
                        return response ()->json (['pesan'=>'Data Gagal Import Indikator Kegiatan Renja','status_pesan'=>'0']);
                    } else {
                        $transPelaksana=DB::INSERT('INSERT INTO trx_renja_rancangan_pelaksana (tahun_renja, no_urut, id_pelaksana_renja, id_renja, id_aktivitas_renja, id_sub_unit, status_data, status_pelaksanaan, ket_usul, sumber_data, id_lokasi)
                            SELECT a.tahun_renja, a.no_urut, a.id_pelaksana_renja, a.id_renja, a.id_aktivitas_renja, a.id_sub_unit, 0 AS status_data,  0 AS status_pelaksanaan, a.ket_usul, 0 AS sumber_data, a.id_lokasi
                            FROM trx_renja_ranwal_pelaksana AS a
                            INNER JOIN trx_renja_ranwal_kegiatan AS c ON a.tahun_renja = c.tahun_renja AND a.id_renja = c.id_renja
                            INNER JOIN trx_renja_ranwal_program As b ON c.tahun_renja = b.tahun_renja AND c.id_renja_program = b.id_renja_program
                            WHERE (c.status_pelaksanaan_kegiatan <> 2 AND c.status_pelaksanaan_kegiatan <> 3) AND b.id_renja_program = '.$req->id_rkpd_ranwal.' AND b.id_unit = '.$req->id_unit.' AND b.tahun_renja = '.$req->tahun_renja)
                            ;
                        if($transPelaksana == 0){
                            return response ()->json (['pesan'=>'Data Gagal Import Pelaksana Kegiatan Renja','status_pesan'=>'0']);
                        } else {
                            $transAktivitas=DB::INSERT('INSERT INTO trx_renja_rancangan_aktivitas (tahun_renja, no_urut, id_aktivitas_renja, id_renja, sumber_aktivitas, id_aktivitas_asb, id_satuan_publik, uraian_aktivitas_kegiatan, tolak_ukur_aktivitas, target_output_aktivitas, id_program_nasional, id_program_provinsi, jenis_kegiatan, sumber_dana, pagu_aktivitas, pagu_musren, status_data, status_musren, volume_1, volume_2, id_satuan_1, id_satuan_2, pagu_rata2)
                                SELECT a.tahun_renja, a.no_urut, a.id_aktivitas_renja, a.id_renja, a.sumber_aktivitas, a.id_aktivitas_asb, a.id_satuan_publik, a.uraian_aktivitas_kegiatan, a.tolak_ukur_aktivitas, a.target_output_aktivitas, a.id_program_nasional, a.id_program_provinsi, a.jenis_kegiatan, a.sumber_dana, a.pagu_aktivitas, a.pagu_musren, 0 as status_data, a.status_musren, a.volume_1, a.volume_2, a.id_satuan_1, a.id_satuan_2, a.pagu_rata2
                                FROM trx_renja_ranwal_aktivitas AS a 
                                INNER JOIN trx_renja_ranwal_pelaksana AS d ON a.tahun_renja = d.tahun_renja AND a.id_renja = d.id_pelaksana_renja
                                INNER JOIN trx_renja_ranwal_kegiatan AS c ON d.tahun_renja = c.tahun_renja AND d.id_renja = c.id_renja
                                INNER JOIN trx_renja_ranwal_program As b ON c.tahun_renja = b.tahun_renja AND c.id_renja_program = b.id_renja_program
                                WHERE (c.status_pelaksanaan_kegiatan <> 2 AND c.status_pelaksanaan_kegiatan <> 3) AND b.id_renja_program = '.$req->id_rkpd_ranwal.' AND b.id_unit = '.$req->id_unit.' AND b.tahun_renja = '.$req->tahun_renja)
                                ;
                            if($transAktivitas == 0){
                                return response ()->json (['pesan'=>'Data Gagal Import Aktivitas Kegiatan Renja','status_pesan'=>'0']);
                            } else {
                                return response ()->json (['pesan'=>'Import Data Rancangan Awal Renja berhasil','status_pesan'=>'1']);
                            }
                        }
                    }
                }
            }
        }
    }

    public function getSelectProgram($id_unit,$tahun_rkpd){
        $getSelect=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM trx_renja_ranwal_program a
            LEFT OUTER JOIN trx_renja_rancangan_program b ON a.tahun_renja = b.tahun_renja AND a.id_renja_program = b.id_renja_ranwal, (SELECT @id:=0) d
            WHERE b.id_renja_ranwal is null AND a.status_data = 2 AND (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3) AND 
            a.id_unit = '.$id_unit.' AND a.tahun_renja ='.$tahun_rkpd);
        // return json_encode($getSelect);
        return DataTables::of($getSelect)
        ->addColumn('action',function($getSelect){
          return '
              <button id="btnReLoad" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="" title="" class="btn btn-primary">
              <i class="fa fa-download fa-fw fa-lg"></i> Load Data</button>
          ' ;})
        ->make(true);
    }

    public function unloadRenja(Request $req)
    {
        $cek = DB::SELECT('SELECT id_renja_program, status_data FROM trx_renja_rancangan_program WHERE tahun_renja='.$req->tahun_renja.' and id_unit='.$req->id_unit.' AND id_renja_program='.$req->id_rkpd_ranwal);

        if($cek[0]->status_data == 0){
            $result=DB::Delete('DELETE FROM trx_renja_rancangan_program WHERE tahun_renja='.$req->tahun_renja.' and id_unit='.$req->id_unit.' AND id_renja_program='.$req->id_rkpd_ranwal);
            if($result!=0){
                return response ()->json (['pesan'=>'Data Berhasil di-Unload','status_pesan'=>'1']);
            } else {
                return response ()->json (['pesan'=>'Data Gagal di-Unload','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Maaf Status Data Usulan telah di-posting, tidak dapat dilakukan unload..','status_pesan'=>'0']);
        }

    }

    public function getRekapProgram($tahun_renja,$id_unit)
    {
      $rekaptrans=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (
                    SELECT a.id_unit, a.tahun_renja, a.id_renja_program, a.jenis_belanja, a.id_renja_ranwal, a.uraian_program_renstra, a.id_program_ref, COALESCE(b.jml_indikator,0) as jml_indikator, COALESCE(c.jml_kegiatan,0) as jml_kegiatan, COALESCE(d.jml_pelaksana,0) as jml_pelaksana, COALESCE(e.jml_aktivitas,0) as jml_aktivitas, COALESCE(e.jml_pagu,0) as jml_pagu
                        FROM trx_renja_rancangan_program AS a
                        LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator
                        FROM trx_renja_rancangan_program_indikator a GROUP BY a.tahun_renja, a.id_renja_program) b 
                        ON a.tahun_renja = b.tahun_renja AND a.id_renja_program = b.id_renja_program
                        LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan
                        FROM trx_renja_rancangan a GROUP BY a.tahun_renja, a.id_renja_program) c 
                        ON a.tahun_renja = c.tahun_renja AND a.id_renja_program = c.id_renja_program
                        LEFT OUTER JOIN (SELECT a.tahun_renja, b.id_renja_program, COALESCE(COUNT(a.id_pelaksana_renja),0) AS jml_pelaksana
                        FROM trx_renja_rancangan_pelaksana a 
                        LEFT OUTER JOIN trx_renja_rancangan b ON a.tahun_renja=b.tahun_renja AND a.id_renja = b.id_renja
                        GROUP BY a.tahun_renja, b.id_renja_program) d 
                        ON a.tahun_renja = d.tahun_renja AND a.id_renja_program = d.id_renja_program
                        LEFT OUTER JOIN (SELECT a.tahun_renja, b.id_renja_program, COALESCE(COUNT(c.id_aktivitas_renja),0) AS jml_aktivitas, COALESCE(SUM(c.pagu_aktivitas),0) AS jml_pagu
                        FROM trx_renja_rancangan_aktivitas c
                        LEFT OUTER JOIN trx_renja_rancangan_pelaksana a ON c.tahun_renja = a.tahun_renja AND c.id_renja = a.id_pelaksana_renja
                        LEFT OUTER JOIN trx_renja_rancangan b ON a.tahun_renja=b.tahun_renja AND a.id_renja = b.id_renja
                        GROUP BY a.tahun_renja, b.id_renja_program) e 
                        ON a.tahun_renja = e.tahun_renja AND a.id_renja_program = e.id_renja_program                  
                        WHERE a.id_unit ='.$id_unit.' AND a.tahun_renja='.$tahun_renja.') a,(SELECT @id:=0) z');
      return DataTables::of($rekaptrans)
            ->addColumn('action', function ($dataranwal) {
                return '<a class="btnUnload btn btn-labeled btn-danger" data-toggle="tooltip" title="Unload Program Renja"><span class="btn-label"><i class="fa fa-reply fa-fw fa-lg"></i></span> Unload Data</a>';
                })
          ->make(true);
    }   


}
