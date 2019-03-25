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
use App\Models\TrxForumSkpdProgramRanwal;
use App\Models\TrxForumSkpdProgram;
use App\Models\TrxForumSkpd;
use App\Models\TrxForumSkpdAktivitas;
use App\Models\TrxForumSkpdPelaksana;
use App\Models\TrxForumSkpdLokasi;
use App\Models\TrxForumSkpdUsulan;
use App\Models\TrxForumSkpdBelanja;
use App\Models\RefUnit;
use App\Models\RefSubUnit;
use Auth;

class TrxForumSkpdLoadController extends Controller
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
            // return Datatables::of()->make(true);
            return json_encode($unit);
        }
    }

    protected function getUserSub(){
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

    public function getSelectProgram($id_unit,$tahun_rkpd){
        $getSelect=DB::SELECT('SELECT a.* FROM trx_renja_ranwal_program a
            LEFT OUTER JOIN trx_renja_rancangan_program b ON a.tahun_renja = b.tahun_renja AND a.id_renja_program = b.id_renja_ranwal
            WHERE b.id_renja_ranwal is null AND a.status_data = 2 AND (a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3) AND 
            a.id_unit = '.$id_unit.' AND a.tahun_renja ='.$tahun_rkpd);
        return json_encode($getSelect);
    }

    public function unloadRenja(Request $req)
    {
      
      $result=DB::Delete('DELETE FROM trx_renja_rancangan_program WHERE tahun_renja='.$req->tahun_renja.' and id_unit='.$req->id_unit.' AND id_renja_program='.$req->id_rkpd_ranwal);

        if($result!=0){
            return response ()->json (['pesan'=>'Data Berhasil di-Unload','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal di-Unload','status_pesan'=>'0']);
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
