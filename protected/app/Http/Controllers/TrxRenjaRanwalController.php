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

class TrxRenjaRanwalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function loadData(Request $request, Builder $htmlBuilder, $id = null)
    {
        // if(Auth::check()){
            return view('ranwalrenja.load'); //->with(compact('unit'));
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

    protected function getUserMisi($no_urut){
        /* count user sub first
        * getUserVisi. If user visi = 0  && usersub != 0 populate nothing
        * if user sub not exist, populate all
        * 
        */
        $user = $this->getUserSub();
        $visi = $this->getUserVisi();
        // this line use to check if visi are 0
        $renstraMisi = [];
        if($user->count !=0){
            if(count($visi) != 0){
                $renstraMisi = \App\Models\TrxRenstraMisi::select();
                foreach($visi as $visi){
                    // $renstraMisi->orWhere(['id_visi_renstra' => $visi->id_visi_renstra, 'no_urut' => $no_urut]);
                    // codes above didn't work like Yii2. It result or-(or) query, not or-(and) as expected. (I think it changes after update composer. Latest L5.4 rollback to previous behavior) Already create issue in laravel-github to change orWhere behavior
                    // temporary we use raw instead. Please don't use following line with user input condition.
                    $renstraMisi->orWhereRaw("(id_visi_renstra = $visi->id_visi_renstra AND no_urut = $no_urut)");
                }
                $renstraMisi = $renstraMisi->get();
            }
        }else{
            $renstraMisi = \App\Models\TrxRenstraMisi::where(['no_urut' => $no_urut])->get();
        }
        return $renstraMisi;
    }

    public function getTransProgram($tahun,$unit)
    {
        $getTransProgram=DB::SELECT('SELECT a.tahun_rkpd,(@id:=@id+1) as no_urut,a.id_rkpd_ranwal,a.id_program_rpjmd,a.id_unit,
                a.id_visi_renstra,a.id_misi_renstra,a.id_tujuan_renstra,a.id_sasaran_renstra,
                     a.id_program_renstra,a.uraian_program_renstra,
                a.pagu_tahun_program,a.pagu_tahun_program,a.status_pelaksanaan,a.sumber_data,0,0 FROM
                (SELECT DISTINCT a.id_rkpd_rpjmd, a.tahun_rkpd, a.id_rkpd_ranwal, a.id_unit,b.id_sasaran_renstra,
                b.id_visi_renstra,b.id_misi_renstra,b.id_tujuan_renstra,b.id_program_renstra,a.id_program_rpjmd,
                b.pagu_tahun_program, a.status_pelaksanaan, a.sumber_data, c.uraian_program_renstra
                FROM ( SELECT DISTINCT a.id_rkpd_ranwal, a.tahun_rkpd, a.id_rkpd_rpjmd, c.id_unit,a.id_program_rpjmd, 
                a.uraian_program_rpjmd, a.status_data, c.status_pelaksanaan, c.sumber_data
                FROM trx_rkpd_ranwal a
                INNER JOIN trx_rkpd_ranwal_urusan b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal AND a.tahun_rkpd = b.tahun_rkpd
                INNER JOIN trx_rkpd_ranwal_pelaksana c ON b.id_rkpd_ranwal = c.id_rkpd_ranwal 
                AND b.tahun_rkpd = c.tahun_rkpd AND b.id_urusan_rkpd = c.id_urusan_rkpd
                GROUP BY a.id_rkpd_ranwal, a.tahun_rkpd, a.id_rkpd_rpjmd, c.id_unit,a.id_program_rpjmd, 
                a.uraian_program_rpjmd, a.status_data, c.status_pelaksanaan, c.sumber_data) a
                INNER JOIN trx_rkpd_renstra b ON a.id_rkpd_rpjmd = b.id_rkpd_rpjmd AND a.tahun_rkpd = b.tahun_rkpd AND a.id_unit = b.id_unit
                INNER JOIN trx_renstra_program AS c ON b.id_sasaran_renstra = c.id_sasaran_renstra 
                AND b.id_program_renstra = c.id_program_renstra AND b.id_program_rpjmd = c.id_program_rpjmd
                WHERE a.status_data = 2 and a.tahun_rkpd = '.$tahun.' AND a.id_unit ='.$unit.') a, (SELECT @id:=0) b');
        
        return json_encode($getTransProgram);
    }

    // public function transProgramRKPD(Request $req)
    // {
    //     $progRKPD=DB::INSERT('INSERT INTO trx_renja_ranwal_program_rkpd (tahun_renja, jenis_belanja, id_rkpd_ranwal, id_unit, uraian_program_rpjmd, status_pelaksanaan, sumber_data, jml_data, jml_baru, jml_lama, jml_tepat, jml_maju, jml_tunda, jml_batal)
    //                 SELECT  a.tahun_rkpd, a.jenis_belanja, a.id_rkpd_ranwal, c.id_unit, a.uraian_program_rpjmd, a.status_pelaksanaan, a.sumber_data,  
    //                 count(DISTINCT(c.sumber_data)) as data, 
    //                 COUNT(DISTINCT CASE WHEN c.sumber_data = 1 THEN c.sumber_data END) as baru, 
    //                 COUNT(DISTINCT CASE WHEN c.sumber_data = 0 THEN c.sumber_data END) as lama,
    //                 (COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 0 THEN c.status_pelaksanaan END) + 
    //                 COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 4 THEN c.status_pelaksanaan END)) as tepat,
    //                 COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 1 THEN c.status_pelaksanaan END) as maju,
    //                 COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 2 THEN c.status_pelaksanaan END) as tunda,
    //                 COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 3 THEN c.status_pelaksanaan END) as batal
    //                 FROM trx_rkpd_ranwal a
    //                 INNER JOIN trx_rkpd_ranwal_urusan b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal and a.tahun_rkpd = b.tahun_rkpd
    //                 INNER JOIN trx_rkpd_ranwal_pelaksana c ON b.id_rkpd_ranwal = c.id_rkpd_ranwal and b.tahun_rkpd = c.tahun_rkpd and b.id_urusan_rkpd = c.id_urusan_rkpd
    //                 WHERE a.status_data = 2 and a.tahun_rkpd = '.$req->tahun_renja.' AND c.id_unit ='.$req->id_unit.' AND a.id_rkpd_ranwal='.$req->id_rkpd_ranwal.' GROUP BY a.tahun_rkpd, a.jenis_belanja, a.id_rkpd_ranwal, c.id_unit, a.uraian_program_rpjmd, a.status_pelaksanaan, a.sumber_data');

    //     if($progRKPD==0){
    //         return response ()->json (['pesan'=>'Data Gagal Import Rekap Program RKPD','status_pesan'=>'0']);
    //     } else {
    //         $progRenja=DB::INSERT('INSERT INTO trx_renja_ranwal_program (jenis_belanja,tahun_renja, no_urut, id_renja_ranwal, 
    //             id_rkpd_ranwal, id_program_rpjmd, id_bidang, id_unit, id_visi_renstra, id_misi_renstra, 
    //             id_tujuan_renstra, id_sasaran_renstra, id_program_renstra, uraian_program_renstra, 
    //             pagu_tahun_ranwal, pagu_tahun_renstra, status_program_rkpd, sumber_data_rkpd, 
    //             sumber_data, ket_usulan, status_data,id_program_ref)
    //             SELECT a.jenis_belanja, a.tahun_rkpd,(@id:=@id+1) as no_urut,m.id_renja_ranwal,a.id_rkpd_ranwal,a.id_program_rpjmd,
    //             a.id_bidang, a.id_unit,a.id_visi_renstra,a.id_misi_renstra,a.id_tujuan_renstra,a.id_sasaran_renstra,
    //             a.id_program_renstra,a.uraian_program_renstra,a.pagu_tahun_program,a.pagu_tahun_program,
    //             a.status_pelaksanaan,a.sumber_data,0,null,0,a.id_program_ref FROM
    //            (SELECT DISTINCT a.jenis_belanja, a.id_rkpd_rpjmd, a.tahun_rkpd, a.id_rkpd_ranwal, a.id_bidang, a.id_unit,
    //             b.id_sasaran_renstra,b.id_visi_renstra,b.id_misi_renstra,b.id_tujuan_renstra,b.id_program_renstra,
    //             a.id_program_rpjmd,b.pagu_tahun_program, a.status_pelaksanaan, a.sumber_data, 
    //             c.uraian_program_renstra,c.id_program_ref
    //             FROM ( SELECT DISTINCT a.jenis_belanja, a.id_rkpd_ranwal, a.tahun_rkpd, a.id_rkpd_rpjmd, b.id_bidang, c.id_unit,a.id_program_rpjmd, 
    //             a.uraian_program_rpjmd, a.status_data, c.status_pelaksanaan, c.sumber_data
    //             FROM trx_rkpd_ranwal a
    //             INNER JOIN trx_rkpd_ranwal_urusan b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal AND a.tahun_rkpd = b.tahun_rkpd
    //             INNER JOIN trx_rkpd_ranwal_pelaksana c ON b.id_rkpd_ranwal = c.id_rkpd_ranwal 
    //             AND b.tahun_rkpd = c.tahun_rkpd AND b.id_urusan_rkpd = c.id_urusan_rkpd
    //             GROUP BY a.jenis_belanja, a.id_rkpd_ranwal, a.tahun_rkpd, a.id_rkpd_rpjmd, c.id_unit,a.id_program_rpjmd, 
    //             a.uraian_program_rpjmd, a.status_data, c.status_pelaksanaan, c.sumber_data, b.id_bidang) a
    //             INNER JOIN trx_rkpd_renstra b ON a.id_rkpd_rpjmd = b.id_rkpd_rpjmd AND a.tahun_rkpd = b.tahun_rkpd AND a.id_unit = b.id_unit
    //             INNER JOIN (SELECT b.id_bidang, a.* FROM trx_renstra_program AS a
	// 			INNER JOIN ref_program b ON a.id_program_ref = b.id_program)AS c ON b.id_sasaran_renstra = c.id_sasaran_renstra 
    //             AND b.id_program_renstra = c.id_program_renstra AND b.id_program_rpjmd = c.id_program_rpjmd AND a.id_bidang = c.id_bidang
    //             WHERE a.status_data = 2 and a.tahun_rkpd = '.$req->tahun_renja.' AND a.id_unit ='.$req->id_unit.' AND a.id_rkpd_ranwal='.$req->id_rkpd_ranwal.') a
    //             INNER JOIN trx_renja_ranwal_program_rkpd m ON a.tahun_rkpd = m.tahun_renja 
    //             AND a.id_unit = m.id_unit AND a.id_rkpd_ranwal= m.id_rkpd_ranwal, (SELECT @id:=0) b');

    //         if($progRenja==0){
    //             return response ()->json (['pesan'=>'Data Gagal Import Program Renja','status_pesan'=>'0']);
    //         } else {
    //             $indikatorProg=DB::INSERT('INSERT INTO trx_renja_ranwal_program_indikator(tahun_renja,no_urut,id_renja_program,id_program_renstra,
    //             id_perubahan,kd_indikator,uraian_indikator_program_renja,tolok_ukur_indikator,target_renstra,target_renja)
    //             SELECT a.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja_program,a.id_program_renstra,a.id_perubahan,a.kd_indikator,
    //             a.uraian_indikator_program_renstra,a.tolok_ukur_indikator,a.angka_tahun,a.angka_tahun FROM
    //             (SELECT DISTINCT a.id_unit,f.id_program_renstra,f.id_perubahan,f.kd_indikator,f.uraian_indikator_program_renstra,
    //             f.tolok_ukur_indikator,f.angka_tahun'.$this->getTahunKe($req->tahun_renja).' as angka_tahun, h.tahun_'.$this->getTahunKe($req->tahun_renja).' AS tahun_renja FROM trx_renstra_visi AS a
    //             INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
    //             INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
    //             INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
    //             INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
    //             INNER JOIN trx_renstra_program_indikator AS f ON f.id_program_renstra = e.id_program_renstra,ref_tahun AS h) a
    //             INNER JOIN trx_renja_ranwal_program b
    //             ON a.tahun_renja = b.tahun_renja AND a.id_unit = b.id_unit AND a.id_program_renstra = b.id_program_renstra,
    //             (SELECT @id:=0) c WHERE a.tahun_renja='.$req->tahun_renja.' and a.id_unit='.$req->id_unit.' AND b.id_rkpd_ranwal='.$req->id_rkpd_ranwal);
    //             if($indikatorProg==0){
    //                return response ()->json (['pesan'=>'Data Gagal Import Indikator Program Renja','status_pesan'=>'0']); 
    //             } else {
    //                $kegRenja=DB::INSERT('INSERT INTO trx_renja_ranwal_kegiatan(tahun_renja, no_urut, id_renja_program, id_rkpd_renstra, 
    //                 id_rkpd_ranwal, id_unit, id_visi_renstra, id_misi_renstra, id_tujuan_renstra, id_sasaran_renstra, 
    //                 id_program_renstra, uraian_program_renstra, id_kegiatan_renstra, uraian_kegiatan_renstra, 
    //                 pagu_tahun_renstra, pagu_tahun_kegiatan, pagu_tahun_selanjutnya, status_pelaksanaan_kegiatan, 
    //                 sumber_data, ket_usulan, status_data,id_kegiatan_ref)
    //                 SELECT a.tahun_rkpd,(@id:=@id+1) AS no_urut,b.id_renja_program,a.id_rkpd_renstra,b.id_rkpd_ranwal,
    //                 a.id_unit,a.id_visi_renstra,a.id_misi_renstra,a.id_tujuan_renstra,a.id_sasaran_renstra,
    //                 a.id_program_renstra,b.uraian_program_renstra,a.id_kegiatan_renstra,c.uraian_kegiatan_renstra,
    //                 a.pagu_tahun_kegiatan as pagu_tahun_renstra,a.pagu_tahun_kegiatan, 0,b.status_program_rkpd,0,null,0,c.id_kegiatan_ref
    //                 FROM trx_rkpd_renstra AS a
    //                 INNER JOIN trx_renja_ranwal_program AS b ON a.tahun_rkpd = b.tahun_renja AND a.id_program_rpjmd = b.id_program_rpjmd
    //                 AND a.id_unit = b.id_unit AND a.id_visi_renstra = b.id_visi_renstra AND a.id_misi_renstra = b.id_misi_renstra
    //                 AND a.id_tujuan_renstra = b.id_tujuan_renstra AND a.id_sasaran_renstra = b.id_sasaran_renstra AND a.id_program_renstra = b.id_program_renstra
    //                 INNER JOIN trx_renstra_kegiatan c ON a.id_program_renstra = c.id_program_renstra AND a.id_kegiatan_renstra=c.id_kegiatan_renstra,
    //                 (SELECT @id:=0) c WHERE a.tahun_rkpd='.$req->tahun_renja.' and a.id_unit='.$req->id_unit.'  AND b.id_rkpd_ranwal='.$req->id_rkpd_ranwal);
                    
    //                 if($kegRenja==0){
    //                     return response ()->json (['pesan'=>'Data Gagal Import Kegiatan Renja','status_pesan'=>'0']); 
    //                 } else {
    //                     $indikatorkeg=DB::INSERT('INSERT INTO trx_renja_ranwal_kegiatan_indikator(tahun_renja,no_urut,id_renja,id_perubahan,kd_indikator,
    //                           uraian_indikator_kegiatan_renja,tolok_ukur_indikator,angka_tahun,angka_renstra,status_data,sumber_data)
    //                           SELECT b.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja,0 as id_perubahan,a.kd_indikator,a.uraian_indikator_kegiatan,
    //                           a.tolokukur_kegiatan,a.target_output,a.target_output,0 as status_data,0 
    //                           FROM trx_rkpd_renstra_indikator AS a
    //                           INNER JOIN trx_renja_ranwal_kegiatan AS b ON b.tahun_renja = a.tahun_rkpd AND b.id_rkpd_renstra = a.id_rkpd_renstra,
    //                           (SELECT @id:=0) c WHERE b.tahun_renja='.$req->tahun_renja.' and b.id_unit='.$req->id_unit.'  AND b.id_rkpd_ranwal='.$req->id_rkpd_ranwal);
    //                     if($indikatorkeg==0){
    //                        return response ()->json (['pesan'=>'Data Gagal Import Indikator Kegiatan Renja','status_pesan'=>'0']);   
    //                     } else {
    //                         $pelaksanakeg=DB::INSERT('INSERT INTO trx_renja_ranwal_pelaksana(tahun_renja,no_urut,id_renja,id_aktivitas_renja,id_sub_unit,status_data,status_pelaksanaan,ket_usul,sumber_data,id_lokasi)
    //                           SELECT b.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja,0 as id_aktivitas_renja,a.id_sub_unit,0 as status_data,0 as status_pelaksanaan,Null as ket_usul, 0 as sumber_data, 0 as id_lokasi
    //                           FROM trx_rkpd_renstra_pelaksana AS a
    //                           INNER JOIN trx_renja_ranwal_kegiatan AS b ON b.tahun_renja = a.tahun_rkpd AND b.id_rkpd_renstra = a.id_rkpd_renstra,
    //                           (SELECT @id:=0) c WHERE b.tahun_renja='.$req->tahun_renja.' and b.id_unit='.$req->id_unit.'  AND b.id_rkpd_ranwal='.$req->id_rkpd_ranwal);
    //                         if($pelaksanakeg==0){
    //                             return response ()->json (['pesan'=>'Data Gagal Import Pelaksanaan Renja','status_pesan'=>'0']); 
    //                         } else {
    //                             return response ()->json (['pesan'=>'Data Berhasil Import Data','status_pesan'=>'1']);
    //                         }
    //                     }
    //                 }

    //             }
    //         }
    //     }
    // }

    public function transProgramRKPD(Request $req)
    {
        $tahun = DB::SELECT('SELECT a.tahun, a.tahun_ke, a.tahun_next, a.kolom FROM (
            SELECT tahun_0 as tahun, 0 as tahun_ke, 1 as tahun_next, "tahun_0" as kolom FROM ref_tahun
            UNION SELECT tahun_1 as tahun, 1 as tahun_ke, 2 as tahun_next, "tahun_1" as kolom FROM ref_tahun
            UNION SELECT tahun_2 as tahun, 2 as tahun_ke, 3 as tahun_next, "tahun_2" as kolom FROM ref_tahun
            UNION SELECT tahun_3 as tahun, 3 as tahun_ke, 4 as tahun_next, "tahun_3" as kolom FROM ref_tahun
            UNION SELECT tahun_4 as tahun, 4 as tahun_ke, 5 as tahun_next, "tahun_4" as kolom FROM ref_tahun
            UNION SELECT tahun_5 as tahun, 5 as tahun_ke, 5 as tahun_next, "tahun_5" as kolom FROM ref_tahun) a
            WHERE a.tahun='.$req->tahun_renja.' LIMIT 1');

        $progRKPD=DB::INSERT('INSERT INTO trx_renja_ranwal_program_rkpd (tahun_renja, jenis_belanja, id_rkpd_ranwal, id_unit, uraian_program_rpjmd, status_pelaksanaan, sumber_data, jml_data, jml_baru, jml_lama, jml_tepat, jml_maju, jml_tunda, jml_batal)
                    SELECT  a.tahun_rkpd, a.jenis_belanja, a.id_rkpd_ranwal, c.id_unit, a.uraian_program_rpjmd, a.status_pelaksanaan, a.sumber_data,  
                    count(DISTINCT(c.sumber_data)) as data, 
                    COUNT(DISTINCT CASE WHEN c.sumber_data = 1 THEN c.sumber_data END) as baru, 
                    COUNT(DISTINCT CASE WHEN c.sumber_data = 0 THEN c.sumber_data END) as lama,
                    (COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 0 THEN c.status_pelaksanaan END) + 
                    COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 4 THEN c.status_pelaksanaan END)) as tepat,
                    COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 1 THEN c.status_pelaksanaan END) as maju,
                    COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 2 THEN c.status_pelaksanaan END) as tunda,
                    COUNT(DISTINCT CASE WHEN c.status_pelaksanaan = 3 THEN c.status_pelaksanaan END) as batal
                    FROM trx_rkpd_ranwal a
                    INNER JOIN trx_rkpd_ranwal_urusan b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal and a.tahun_rkpd = b.tahun_rkpd
                    INNER JOIN trx_rkpd_ranwal_pelaksana c ON b.id_rkpd_ranwal = c.id_rkpd_ranwal and b.tahun_rkpd = c.tahun_rkpd and b.id_urusan_rkpd = c.id_urusan_rkpd
                    WHERE a.status_data = 2 AND a.tahun_rkpd = '.$tahun[0]->tahun.' AND c.id_unit ='.$req->id_unit.' AND a.id_rkpd_ranwal='.$req->id_rkpd_ranwal.' GROUP BY a.tahun_rkpd, a.jenis_belanja, a.id_rkpd_ranwal, c.id_unit, a.uraian_program_rpjmd, a.status_pelaksanaan, a.sumber_data');

        if($progRKPD==0){
            return response ()->json (['pesan'=>'Data Gagal Import Rekap Program RKPD','status_pesan'=>'0']);
        } else {
            $progRenja=DB::INSERT('INSERT INTO trx_renja_ranwal_program (jenis_belanja,tahun_renja, no_urut, id_renja_ranwal, 
                id_rkpd_ranwal, id_program_rpjmd, id_bidang, id_unit, id_visi_renstra, id_misi_renstra, 
                id_tujuan_renstra, id_sasaran_renstra, id_program_renstra, uraian_program_renstra, 
                pagu_tahun_ranwal, pagu_tahun_renstra, status_program_rkpd, sumber_data_rkpd, 
                sumber_data, ket_usulan, status_data,id_program_ref)
                SELECT a.jenis_belanja, a.tahun_rkpd,(@id:=@id+1) as no_urut,m.id_renja_ranwal,a.id_rkpd_ranwal,a.id_program_rpjmd,
                a.id_bidang, a.id_unit,a.id_visi_renstra,a.id_misi_renstra,a.id_tujuan_renstra,a.id_sasaran_renstra,
                a.id_program_renstra,a.uraian_program_renstra,a.pagu_tahun_program,a.pagu_tahun_program,
                a.status_pelaksanaan,a.sumber_data,0,null,0,a.id_program_ref FROM
               (SELECT DISTINCT a.jenis_belanja, a.id_rkpd_rpjmd, a.tahun_rkpd, a.id_rkpd_ranwal, a.id_bidang, a.id_unit,
                b.id_sasaran_renstra,b.id_visi_renstra,b.id_misi_renstra,b.id_tujuan_renstra,b.id_program_renstra,
                a.id_program_rpjmd,b.pagu_tahun_program, a.status_pelaksanaan, a.sumber_data, 
                b.uraian_program_renstra,b.id_program_ref
                FROM ( SELECT DISTINCT a.jenis_belanja, a.id_rkpd_ranwal, a.tahun_rkpd, a.id_rkpd_rpjmd, b.id_bidang, c.id_unit,a.id_program_rpjmd, 
                a.uraian_program_rpjmd, a.status_data, c.status_pelaksanaan, c.sumber_data
                FROM trx_rkpd_ranwal a
                INNER JOIN trx_rkpd_ranwal_urusan b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal AND a.tahun_rkpd = b.tahun_rkpd
                INNER JOIN trx_rkpd_ranwal_pelaksana c ON b.id_rkpd_ranwal = c.id_rkpd_ranwal 
                AND b.tahun_rkpd = c.tahun_rkpd AND b.id_urusan_rkpd = c.id_urusan_rkpd
                GROUP BY a.jenis_belanja, a.id_rkpd_ranwal, a.tahun_rkpd, a.id_rkpd_rpjmd, c.id_unit,a.id_program_rpjmd, 
                a.uraian_program_rpjmd, a.status_data, c.status_pelaksanaan, c.sumber_data, b.id_bidang) a
                INNER JOIN (SELECT DISTINCT e.id_program_rpjmd,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,
					e.id_program_renstra,e.id_program_ref,e.pagu_tahun'.$tahun[0]->tahun_ke.' as pagu_tahun_program,f.id_bidang,f.kd_program,   
					f.uraian_program AS uraian_program_ref, '.$tahun[0]->tahun.' as tahun_rkpd, e.uraian_program_renstra   
					FROM trx_renstra_visi AS a    
					INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra   
					INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra    
					INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra     
					INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                    INNER JOIN ref_program f ON e.id_program_ref = f.id_program) b ON a.id_program_rpjmd = b.id_program_rpjmd AND a.tahun_rkpd = b.tahun_rkpd AND a.id_unit = b.id_unit
                WHERE  a.status_data = 2 AND a.id_bidang=b.id_bidang AND a.tahun_rkpd = '.$tahun[0]->tahun.' AND a.id_unit ='.$req->id_unit.' AND a.id_rkpd_ranwal='.$req->id_rkpd_ranwal.') a
                INNER JOIN trx_renja_ranwal_program_rkpd m ON a.tahun_rkpd = m.tahun_renja 
                AND a.id_unit = m.id_unit AND a.id_rkpd_ranwal= m.id_rkpd_ranwal, (SELECT @id:=0) b');

            if($progRenja==0){
                return response ()->json (['pesan'=>'Data Gagal Import Program Renja','status_pesan'=>'0']);
            } else {
                $indikatorProg=DB::INSERT('INSERT INTO trx_renja_ranwal_program_indikator(tahun_renja,no_urut,id_renja_program,id_program_renstra,
                id_perubahan,kd_indikator,uraian_indikator_program_renja,tolok_ukur_indikator,target_renstra,target_renja)
                SELECT a.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja_program,a.id_program_renstra,a.id_perubahan,a.kd_indikator,
                a.uraian_indikator_program_renstra,a.tolok_ukur_indikator,a.angka_tahun,a.angka_tahun FROM
                (SELECT DISTINCT a.id_unit,f.id_program_renstra,f.id_perubahan,f.kd_indikator,f.uraian_indikator_program_renstra,
                f.tolok_ukur_indikator,f.angka_tahun'.$tahun[0]->tahun_ke.' as angka_tahun, '.$tahun[0]->tahun.' AS tahun_renja FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                INNER JOIN trx_renstra_program_indikator AS f ON f.id_program_renstra = e.id_program_renstra,ref_tahun AS h) a
                INNER JOIN trx_renja_ranwal_program b
                ON a.tahun_renja = b.tahun_renja AND a.id_unit = b.id_unit AND a.id_program_renstra = b.id_program_renstra,
                (SELECT @id:=0) c WHERE a.tahun_renja='.$tahun[0]->tahun.' and a.id_unit='.$req->id_unit.' AND b.id_rkpd_ranwal='.$req->id_rkpd_ranwal);
                if($indikatorProg==0){
                   return response ()->json (['pesan'=>'Data Gagal Import Indikator Program Renja','status_pesan'=>'0']); 
                } else {
                   $kegRenja=DB::INSERT('INSERT INTO trx_renja_ranwal_kegiatan(tahun_renja, no_urut, id_renja_program, id_rkpd_renstra, 
                    id_rkpd_ranwal, id_unit, id_visi_renstra, id_misi_renstra, id_tujuan_renstra, id_sasaran_renstra, 
                    id_program_renstra, uraian_program_renstra, id_kegiatan_renstra, uraian_kegiatan_renstra, 
                    pagu_tahun_renstra, pagu_tahun_kegiatan, pagu_tahun_selanjutnya, status_pelaksanaan_kegiatan, 
                    sumber_data, ket_usulan, status_data,id_kegiatan_ref)
                    SELECT a.tahun_rkpd,(@id:=@id+1) AS no_urut,b.id_renja_program,b.id_program_rpjmd,b.id_rkpd_ranwal,
                    a.id_unit,a.id_visi_renstra,a.id_misi_renstra,a.id_tujuan_renstra,a.id_sasaran_renstra,
                    a.id_program_renstra,b.uraian_program_renstra,a.id_kegiatan_renstra,a.uraian_kegiatan_renstra,
                    a.pagu_tahun_kegiatan as pagu_tahun_renstra,a.pagu_tahun_kegiatan, a.pagu_tahun_selanjutnya ,b.status_program_rkpd,0,null,0,a.id_kegiatan_ref
                    FROM (SELECT DISTINCT e.id_program_rpjmd,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,
					e.id_program_renstra,e.id_program_ref,e.pagu_tahun'.$tahun[0]->tahun_ke.' as pagu_program_renja,f.id_kegiatan_renstra,f.id_kegiatan_ref, 
					f.uraian_kegiatan_renstra,f.pagu_tahun'.$tahun[0]->tahun_ke.' as pagu_tahun_kegiatan, 
                    f.pagu_tahun'.$tahun[0]->tahun_next.' as pagu_tahun_selanjutnya, '.$tahun[0]->tahun.' as tahun_rkpd, g.kd_kegiatan, g.nm_kegiatan
					FROM trx_renstra_visi AS a    
					INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra   
					INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra    
					INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra     
					INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
					INNER JOIN trx_renstra_kegiatan AS f ON e.id_program_renstra = f.id_program_renstra
					INNER JOIN ref_kegiatan AS g ON f.id_kegiatan_ref = g.id_kegiatan) AS a
                    INNER JOIN trx_renja_ranwal_program AS b ON a.tahun_rkpd = b.tahun_renja AND a.id_program_renstra = b.id_program_renstra,
                    (SELECT @id:=0) c WHERE a.tahun_rkpd='.$tahun[0]->tahun.' and a.id_unit='.$req->id_unit.'  AND b.id_rkpd_ranwal='.$req->id_rkpd_ranwal);
                    
                    if($kegRenja==0){
                        return response ()->json (['pesan'=>'Data Gagal Import Kegiatan Renja','status_pesan'=>'0']); 
                    } else {
                        $indikatorkeg=DB::INSERT('INSERT INTO trx_renja_ranwal_kegiatan_indikator(tahun_renja,no_urut,id_renja,id_perubahan,kd_indikator,
                                uraian_indikator_kegiatan_renja,tolok_ukur_indikator,angka_tahun,angka_renstra,status_data,sumber_data)
                                SELECT b.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja,0 as id_perubahan,a.kd_indikator,a.uraian_indikator_kegiatan,
                                a.tolok_ukur_indikator,a.target_output,a.target_output,0 as status_data,0 
                                FROM (SELECT DISTINCT g.kd_indikator,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,  
                                f.id_kegiatan_renstra,g.uraian_indikator_kegiatan_renstra AS uraian_indikator_kegiatan,g.tolok_ukur_indikator,
                                g.angka_tahun'.$tahun[0]->tahun_ke.' as target_output,'.$tahun[0]->tahun.' as tahun_rkpd  
                                FROM trx_renstra_visi AS a    
                                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra 
                                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra  
                                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra 
                                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra  
                                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra  
                                INNER JOIN trx_renstra_kegiatan_indikator AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra) AS a
                                INNER JOIN trx_renja_ranwal_kegiatan AS b ON b.tahun_renja = a.tahun_rkpd AND b.id_kegiatan_renstra = a.id_kegiatan_renstra,
                                (SELECT @id:=0) c WHERE b.tahun_renja='.$tahun[0]->tahun.' and b.id_unit='.$req->id_unit.'  AND b.id_rkpd_ranwal='.$req->id_rkpd_ranwal);
                        if($indikatorkeg==0){
                           return response ()->json (['pesan'=>'Data Gagal Import Indikator Kegiatan Renja','status_pesan'=>'0']);   
                        } else {
                            $pelaksanakeg=DB::INSERT('INSERT INTO trx_renja_ranwal_pelaksana(tahun_renja,no_urut,id_renja,id_aktivitas_renja,id_sub_unit,status_data,status_pelaksanaan,ket_usul,sumber_data,id_lokasi)
                                SELECT b.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja,0 as id_aktivitas_renja,a.id_sub_unit,0 as status_data,0 as status_pelaksanaan,Null as ket_usul, 0 as sumber_data, 0 as id_lokasi
                                FROM (SELECT DISTINCT a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,  
                                f.id_kegiatan_renstra,g.id_sub_unit,'.$tahun[0]->tahun.' as tahun_rkpd  
                                FROM trx_renstra_visi AS a    
                                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra 
                                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra  
                                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra 
                                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra  
                                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra  
                                INNER JOIN trx_renstra_kegiatan_pelaksana AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra) AS a
                                INNER JOIN trx_renja_ranwal_kegiatan AS b ON b.tahun_renja = a.tahun_rkpd AND b.id_kegiatan_renstra = a.id_kegiatan_renstra,
                                (SELECT @id:=0) z WHERE b.tahun_renja='.$tahun[0]->tahun.' and b.id_unit='.$req->id_unit.'  AND b.id_rkpd_ranwal='.$req->id_rkpd_ranwal);
                            if($pelaksanakeg==0){
                                return response ()->json (['pesan'=>'Data Gagal Import Pelaksanaan Renja','status_pesan'=>'0']); 
                            } else {
                                return response ()->json (['pesan'=>'Data Berhasil Import Data','status_pesan'=>'1']);
                            }
                        }
                    }
                }
            }
        }
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
        $getSelect=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.tahun_rkpd, a.id_rkpd_ranwal, c.id_unit, a.uraian_program_rpjmd, 
                c.status_pelaksanaan FROM trx_rkpd_ranwal a
                INNER JOIN trx_rkpd_ranwal_urusan b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal and a.tahun_rkpd = b.tahun_rkpd
                INNER JOIN trx_rkpd_ranwal_pelaksana c ON b.id_rkpd_ranwal = c.id_rkpd_ranwal and b.tahun_rkpd = c.tahun_rkpd and b.id_urusan_rkpd = c.id_urusan_rkpd
                WHERE   a.status_data = 2 AND (c.status_pelaksanaan <> 2 AND c.status_pelaksanaan <> 3) and c.id_unit='.$id_unit.' AND a.tahun_rkpd='.$tahun_rkpd.' 
                GROUP BY a.status_data, a.id_rkpd_ranwal, a.tahun_rkpd, c.id_unit, c.status_pelaksanaan, a.uraian_program_rpjmd) a
                LEFT OUTER JOIN (SELECT * FROM trx_renja_ranwal_program_rkpd WHERE id_unit='.$id_unit.' AND tahun_renja='.$tahun_rkpd.') x 
                ON a.id_rkpd_ranwal = x.id_rkpd_ranwal AND a.tahun_rkpd = x.tahun_renja, (SELECT @id:=0) d WHERE x.id_renja_ranwal is null');

        // return json_encode($getSelect);
        return DataTables::of($getSelect)
        ->addColumn('action',function($getSelect){
          return '
              <button id="btnReLoad" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="" title="" class="btn btn-primary">
              <i class="fa fa-download fa-fw fa-lg"></i> Load Data</button>
          ' ;})
        ->make(true);

    }

    public function getProgramRkpd($tahun_renja,$id_unit)
    {
      
      $programrenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (
                    SELECT tahun_renja, id_rkpd_ranwal, id_unit, uraian_program_rpjmd, status_pelaksanaan, sumber_data, jml_data, jml_baru, jml_lama, jml_tepat, jml_maju, jml_tunda, jml_batal
                    FROM trx_renja_ranwal_program_rkpd
                    WHERE id_unit ='.$id_unit.' AND tahun_renja='.$tahun_renja.') a,(SELECT @id:=0) z ORDER BY tahun_renja, id_rkpd_ranwal');

      return DataTables::of($programrenja)
        ->addColumn('action',function($programrenja){
            return '
            <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="glyphicon glyphicon-wrench"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                        <a class="view-rekap dropdown-item" data-id_rkpd_ranwal="'.$programrenja->id_rkpd_ranwal.'"><i class="glyphicon glyphicon-eye-open"></i> Rincian</a>
                        </li>
                        <li>
                        <a class="unloadRenja dropdown-item" data-id_rkpd_ranwal="'.$programrenja->id_rkpd_ranwal.'" data-id_unit="'.$programrenja->id_unit.'" data-tahun_renja="'.$programrenja->tahun_renja.'"><i class="fa fa-chain-broken"></i> Unload Data</a>
                        </li>
                    </ul>
                </div>
                ' ;
        })
          ->make(true);
    }

    public function unloadRenja(Request $req)
    {
        $data_cek=$this->getChildRenja($req->id_rkpd_ranwal);

        if(count($data_cek) > 0){
            if($data_cek[0]->flag == 0){
                try{
                    $result=DB::Delete('DELETE FROM trx_renja_ranwal_program_rkpd WHERE tahun_renja='.$req->tahun_renja.' and id_unit='.$req->id_unit.' AND id_rkpd_ranwal='.$req->id_rkpd_ranwal);
                    if($result !=0){
                    return response ()->json (['pesan'=>'Data Berhasil di-Unload','status_pesan'=>'1']); 
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal di-Unload','status_pesan'=>'0']);
                    }        
                }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Gagal di-Unload ('.$error_code.')','status_pesan'=>'0']);
                }
            } else {
                return response ()->json (['pesan'=>'Data Program Gagal Unload. Silahkan Cek Status Program Renja, Program RKPD masih ada digunakan.. !','status_pesan'=>'0']);
            }
        } else {
            try{
                $result=DB::Delete('DELETE FROM trx_renja_ranwal_program_rkpd WHERE tahun_renja='.$req->tahun_renja.' and id_unit='.$req->id_unit.' AND id_rkpd_ranwal='.$req->id_rkpd_ranwal);
                if($result !=0){
                return response ()->json (['pesan'=>'Data Berhasil di-Unload','status_pesan'=>'1']); 
                } else {
                    return response ()->json (['pesan'=>'Data Gagal di-Unload','status_pesan'=>'0']);
                }        
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal di-Unload ('.$error_code.')','status_pesan'=>'0']);
            } 
        }
    }

    public function getChildRenja($id_rkpd_ranwal){
      $getChildRenja=DB::select('SELECT DISTINCT b.id_rkpd_ranwal, SUM(COALESCE(c.flag,0)) AS flag FROM trx_renja_ranwal_program b 
            LEFT OUTER JOIN trx_renja_ranwal_dokumen c ON b.id_dokumen = c.id_dokumen_ranwal WHERE b.id_rkpd_ranwal='.$id_rkpd_ranwal.'
			GROUP BY b.id_rkpd_ranwal ');
      return $getChildRenja;
    }

    public function getRekapProgram($tahun_renja,$id_unit,$id_ranwal)
    {
      $rekaptrans=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (
                    SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_program_rpjmd, a.uraian_program_renstra,  
                    COALESCE(COUNT(a.id_renja_program),0) as jml_program, COALESCE(c.jml_indikator,0) as jml_indikator, COALESCE(b.jml_kegiatan,0) as jml_kegiatan, 
                          COALESCE(b.jml_pagu) as jml_pagu
                    FROM trx_renja_ranwal_program a 
                    LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, a.id_program_renstra, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator
                    FROM trx_renja_ranwal_program_indikator a
                    LEFT OUTER JOIN trx_renja_ranwal_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program AND a.id_program_renstra = b.id_program_renstra 
                    GROUP BY a.tahun_renja, a.id_renja_program, a.id_program_renstra) c 
                    ON a.tahun_renja = c.tahun_renja AND a.id_renja_program = c.id_renja_program AND a.id_program_renstra= c.id_program_renstra 
                    LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_program_renstra, a.id_unit, 
                    COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan, COALESCE(SUM(a.pagu_tahun_kegiatan)) AS jml_pagu
                    FROM trx_renja_ranwal_kegiatan a
                    GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_program_renstra,a.id_unit) b 
                    ON a.tahun_renja = b.tahun_renja AND a.id_renja_program = b.id_renja_program AND a.id_program_renstra= b.id_program_renstra                  
                    WHERE a.id_unit ='.$id_unit.' AND a.tahun_renja='.$tahun_renja.' AND a.id_rkpd_ranwal='.$id_ranwal.' AND a.sumber_data = 0 GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_program_rpjmd, a.uraian_program_renstra,a.id_unit,c.jml_indikator,b.jml_kegiatan,b.jml_pagu,a.sumber_data) a,(SELECT @id:=0) z');

      return DataTables::of($rekaptrans)
          ->make(true);
    }   


}
