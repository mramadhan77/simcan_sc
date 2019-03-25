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
use Auth;

class TrxRenjaRancanganPenyesuaianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request, Builder $htmlBuilder, $id = null)
    {
        $unit = \App\Models\RefSubUnit::select();
        if(isset(Auth::user()->getUserSubUnit)){
            foreach(Auth::user()->getUserSubUnit as $data){
                $unit->orWhere(['id_unit' => $data->kd_unit, 'kd_sub' => $data->kd_sub]);                
            }
        }
        $unit = $unit->get();

        return view('renja.penyesuaian')->with(compact('unit'));
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
            // return Datatables::of()->make(true);
            return json_encode($unit);
        }
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

    public function addProgramRenja(Request $req)
    {
    try{
        $data = new TrxRenjaRancanganProgram();

        $data->no_urut = $req->no_urut ;
        $data->tahun_renja = $req->tahun_renja ;
        $data->id_rkpd_ranwal = $req->id_rkpd_ranwal ;
        $data->jenis_belanja = $req->jenis_belanja ;
        $data->id_unit = $req->id_unit ;
        $data->id_visi_renstra = $req->id_visi_renstra ;
        $data->id_misi_renstra = $req->id_misi_renstra ;
        $data->id_tujuan_renstra = $req->id_tujuan_renstra ;
        $data->id_sasaran_renstra = $req->id_sasaran_renstra ;
        $data->id_program_renstra = $req->id_program_renstra ;
        $data->id_program_ref = $req->id_program_ref ;
        $data->uraian_program_renstra = $req->uraian_program_renstra ;
        $data->status_program_rkpd = 4 ;
        $data->sumber_data_rkpd = 1 ;
        $data->ket_usulan = $req->ket_usulan ;
        $data->status_pelaksanaan = 4 ;
        $data->status_data = 0 ;        
        $data->sumber_data = 1 ;
        $data->save (['timestamps' => false]);

        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
         // $error_code = $e->getMessage() ;
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editProgram(Request $req)
    {
        $cek=$this->getCekProgram($req->tahun_renja,$req->id_renja_program);

            $data = TrxRenjaRancanganProgram::find($req->id_renja_program);
            $data->no_urut = $req->no_urut ;
            $data->id_rkpd_ranwal = $req->id_rkpd_ranwal ;
            $data->jenis_belanja = $req->jenis_belanja ;
            $data->id_program_ref = $req->id_program_ref ;
            $data->id_unit = $req->id_unit ;
            $data->id_visi_renstra = $req->id_visi_renstra ;
            $data->id_misi_renstra = $req->id_misi_renstra ;
            $data->id_tujuan_renstra = $req->id_tujuan_renstra ;
            $data->id_sasaran_renstra = $req->id_sasaran_renstra ;
            $data->id_program_renstra = $req->id_program_renstra ;
            $data->id_program_ref = $req->id_program_ref ;
            $data->uraian_program_renstra = $req->uraian_program_renstra ;
            $data->ket_usulan = $req->ket_usulan ;
            $data->status_data = $req->status_data ;
            $data->status_pelaksanaan = $req->status_pelaksanaan ;

            if($req->status_data==1){
                if($cek[0]->cek_indikator==0 && $cek[0]->cek_kegiatan==0){
                    try{
                        $data->save (['timestamps' => false]);
                        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                      }
                      catch(QueryException $e){
                         $error_code = $e->errorInfo[1] ;
                         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                      }                  
                } else {
                          return response ()->json (['pesan'=>'Maaf Ada Indikator/Kegiatan yang Belum direviu','status_pesan'=>'0']);                     
                      }       
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

    public function postProgram(Request $req)
    {
        $cek=$this->getCekProgram($req->tahun_renja,$req->id_renja_program);        

            $data = TrxRenjaRancanganProgram::find($req->id_renja_program);
            $data->status_data = $req->status_data ;

            if($req->status_data==1){
              if($req->status_pelaksanaan==2 && $req->status_pelaksanaan=3){
                  if($cek[0]->cek_indikator==0 && $cek[0]->cek_kegiatan==0){
                    try{
                        $data->save (['timestamps' => false]);
                        return response ()->json (['pesan'=>'Data Program Renja Berhasil di-Posting','status_pesan'=>'1']);
                      }
                      catch(QueryException $e){
                         $error_code = $e->errorInfo[1] ;
                         return response ()->json (['pesan'=>'Data Program Renja Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
                      } 
                    } else {
                        return response ()->json (['pesan'=>'Maaf Program Renja ada indikator/kegiatan yang belum direviu','status_pesan'=>'0']);
                      }                  
                  } else {
                    try{
                        $data->save (['timestamps' => false]);
                        return response ()->json (['pesan'=>'Data Program Renja Berhasil di-Posting','status_pesan'=>'1']);
                      }
                      catch(QueryException $e){
                         $error_code = $e->errorInfo[1] ;
                         return response ()->json (['pesan'=>'Data Program Renja Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
                      }                                             
                  }       
              } else {
                  try{
                      $data->save (['timestamps' => false]);
                      return response ()->json (['pesan'=>'Data Program Renja Berhasil di-UnPosting','status_pesan'=>'1']);
                    }
                    catch(QueryException $e){
                       $error_code = $e->errorInfo[1] ;
                       return response ()->json (['pesan'=>'Data Program Renja Gagal di-UnPosting ('.$error_code.')','status_pesan'=>'0']);
                    }
              }
          }

    public function hapusProgram(Request $req)
      {
        TrxRenjaRancanganProgram::where('id_renja_program',$req->id_renja_program)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
      }

    public function addIndikatorRenja(Request $req){
      try{
                $data= new TrxRenjaRancanganProgramIndikator();
                $data->tahun_renja=$req->tahun_renja ; 
                $data->no_urut=$req->no_urut; 
                $data->id_renja_program=$req->id_renja_program; 
                $data->id_perubahan=0;
                $data->kd_indikator=$req->kd_indikator; 
                $data->uraian_indikator_program_renja=$req->uraian_indikator; 
                $data->tolok_ukur_indikator=$req->tolok_ukur_indikator; 
                $data->target_renstra=0; 
                $data->target_renja=$req->target_renja; 
                $data->status_data=0;
                $data->sumber_data=1;
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }
    }

    public function editIndikatorRenja(Request $req)
    {
      $CekProgram=$this->CheckProgram($req->id_renja_program);
     
        $data= TrxRenjaRancanganProgramIndikator::find($req->id_indikator_program_renja);
        $data->no_urut=$req->no_urut; 
        $data->id_renja_program=$req->id_renja_program; 
        $data->kd_indikator=$req->kd_indikator; 
        $data->uraian_indikator_program_renja=$req->uraian_indikator; 
        $data->tolok_ukur_indikator=$req->tolok_ukur_indikator;
        $data->target_renja=$req->target_renja; 
        $data->status_data=$req->status_data;

        if($req->target_renja > 0){
          try{ 
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }

        } else {
          if($CekProgram[0]->status_pelaksanaan==2 || $CekProgram[0]->status_pelaksanaan==3){
              try{ 
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
              }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
              }
            } else {
              return response ()->json (['pesan'=>'Jumlah Target Indikator harus lebih besar dari 0','status_pesan'=>'0']);
            }
          }

    }

    public function hapusIndikatorRenja(Request $req)
      {
        TrxRenjaRancanganProgramIndikator::where('id_indikator_program_renja',$req->id_indikator_program_renja)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

    public function addKegiatanRenja(Request $req)
    {
    try{
        $data = new TrxRenjaRancangan();
        $data->no_urut = $req->no_urut ;
        $data->tahun_renja = $req->tahun_renja ;
        $data->id_renja_program = $req->id_renja_program ;
        $data->id_rkpd_ranwal = $req->id_rkpd_ranwal ;
        $data->id_unit = $req->id_unit ;
        $data->id_visi_renstra = $req->id_visi_renstra ;
        $data->id_misi_renstra = $req->id_misi_renstra ;
        $data->id_tujuan_renstra = $req->id_tujuan_renstra ;
        $data->id_sasaran_renstra = $req->id_sasaran_renstra ;
        $data->id_program_renstra = $req->id_program_renstra ;
        $data->id_kegiatan_renstra = $req->id_kegiatan_renstra ;
        $data->id_kegiatan_ref = $req->id_kegiatan_ref ;
        $data->uraian_kegiatan_renstra = $req->uraian_kegiatan_renstra ;
        $data->pagu_tahun_renstra = 0;
        $data->pagu_tahun_kegiatan = $req->pagu_tahun_kegiatan ;
        $data->pagu_tahun_selanjutnya = $req->pagu_tahun_selanjutnya ;
        $data->status_pelaksanaan_kegiatan = 4 ;
        $data->ket_usulan = $req->ket_usulan ;
        $data->status_data = 0 ;        
        $data->sumber_data = 1 ;
        $data->save (['timestamps' => false]);
        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }
    }

    public function editKegiatanRenja(Request $req)
    {
        $cek=$this->getCekKegiatan($req->tahun_renja,$req->id_renja);
    
        $data = TrxRenjaRancangan::find($req->id_renja);
        $data->no_urut = $req->no_urut ;
        $data->tahun_renja = $req->tahun_renja ;
        $data->id_renja_program = $req->id_renja_program ;
        $data->id_rkpd_ranwal = $req->id_rkpd_ranwal ;
        $data->id_unit = $req->id_unit ;
        $data->id_visi_renstra = $req->id_visi_renstra ;
        $data->id_misi_renstra = $req->id_misi_renstra ;
        $data->id_tujuan_renstra = $req->id_tujuan_renstra ;
        $data->id_sasaran_renstra = $req->id_sasaran_renstra ;
        $data->id_program_renstra = $req->id_program_renstra ;
        $data->id_kegiatan_renstra = $req->id_kegiatan_renstra ;
        $data->id_kegiatan_ref = $req->id_kegiatan_ref ;
        $data->uraian_kegiatan_renstra = $req->uraian_kegiatan_renstra ;
        $data->pagu_tahun_kegiatan = $req->pagu_tahun_kegiatan ;
        $data->pagu_tahun_selanjutnya = $req->pagu_tahun_selanjutnya ;
        $data->status_pelaksanaan_kegiatan = $req->status_pelaksanaan_kegiatan ;
        $data->ket_usulan = $req->ket_usulan ;
        $data->status_data = $req->status_data ;

        if($req->status_data==1){
            if($cek[0]->cek_indikator==0){
                try{
                    $data->save (['timestamps' => false]);
                    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                  }
                  catch(QueryException $e){
                     $error_code = $e->errorInfo[1] ;
                     return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                  }                  
            } else {
                      return response ()->json (['pesan'=>'Maaf Ada Indikator yang Belum direviu','status_pesan'=>'0']);                     
                  }       
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

    public function postKegiatanRenja(Request $req)
    {
        $cek=$this->getCekKegiatan($req->tahun_renja,$req->id_renja);
    
        $data = TrxRenjaRancangan::find($req->id_renja);
        $data->status_data = $req->status_data ;

        if($req->status_data==1){
          if($req->status_pelaksanaan_kegiatan!=2 && $req->status_pelaksanaan_kegiatan!=3){
            if($cek[0]->cek_indikator==0){                
              try{
                  $data->save (['timestamps' => false]);
                  return response ()->json (['pesan'=>'Data Kegiatan Renja Berhasil di-Posting','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                   $error_code = $e->errorInfo[1] ;
                   return response ()->json (['pesan'=>'Data Kegiatan Renja Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
                }  
              } else {
                return response ()->json (['pesan'=>'Maaf Kegiatan Renja Ada Indikator yang Belum direviu','status_pesan'=>'0']);
              }                
          } else {
              try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Kegiatan Renja Berhasil di-Posting','status_pesan'=>'1']);
              }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Kegiatan Renja Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
              } 
            }       
        } else {
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Kegiatan Renja Berhasil di-UnPosting','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Kegiatan Renja Gagal di-UnPosting ('.$error_code.')','status_pesan'=>'0']);
          }
        }
    }

    public function hapusKegiatanRenja(Request $req)
      {
        TrxRenjaRancangan::where('id_renja',$req->id_renja)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

    public function addIndikatorKeg(Request $req){
      try{
                $data= new TrxRenjaRancanganIndikator();
                $data->tahun_renja= $req->tahun_renja;
                $data->no_urut= $req->no_urut;
                $data->id_renja= $req->id_renja;
                $data->id_perubahan= 0;
                $data->kd_indikator= $req->kd_indikator;
                $data->uraian_indikator_kegiatan_renja= $req->uraian_indikator_kegiatan_renja;
                $data->tolok_ukur_indikator= $req->tolok_ukur_indikator;
                $data->angka_tahun= $req->angka_tahun;
                $data->angka_renstra= 0;
                $data->status_data= 0;
                $data->sumber_data= 1;
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }
    }

    public function editIndikatorKeg(Request $req)
    {
    
        $CekKegiatan=$this->CheckKegiatan($req->id_renja);
        $CekProgram=$this->CheckProgram($req->id_renja_program);
     
        $data= TrxRenjaRancanganIndikator::find($req->id_indikator_kegiatan_renja);
        $data->no_urut= $req->no_urut;
        $data->id_renja= $req->id_renja;
        $data->kd_indikator= $req->kd_indikator;
        $data->uraian_indikator_kegiatan_renja= $req->uraian_indikator_kegiatan_renja;
        $data->tolok_ukur_indikator= $req->tolok_ukur_indikator;
        $data->angka_tahun= $req->angka_tahun;
        $data->status_data= $req->status_data;

        if($req->angka_tahun > 0){
          try{ 
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }

        } else {
          if($CekProgram[0]->status_pelaksanaan!=2 || $CekProgram[0]->status_pelaksanaan!=3){
            if($CekKegiatan[0]->status_pelaksanaan_kegiatan==2 || $CekKegiatan[0]->status_pelaksanaan_kegiatan==3){
              try{ 
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
              }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
              }
            } else {
              return response ()->json (['pesan'=>'Jumlah Target Indikator harus lebih besar dari 0','status_pesan'=>'0']);
            }
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

    }

    public function hapusIndikatorKeg(Request $req)
      {
        TrxRenjaRancanganIndikator::where('id_indikator_kegiatan_renja',$req->id_indikator_kegiatan_renja)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
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

    public function transProgramIndikatorRenja(Request $req)
    {
      $result=DB::INSERT('INSERT INTO trx_renja_rancangan_program_indikator(tahun_renja,no_urut,id_renja_program,id_program_renstra,
                id_perubahan,kd_indikator,uraian_indikator_program_renja,tolok_ukur_indikator,target_renstra,target_renja)
                SELECT a.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja_program,a.id_program_renstra,a.id_perubahan,a.kd_indikator,
                a.uraian_indikator_program_renstra,a.tolok_ukur_indikator,a.angka_tahun,a.angka_tahun FROM
                (SELECT DISTINCT a.id_unit,f.id_program_renstra,f.id_perubahan,f.kd_indikator,f.uraian_indikator_program_renstra,
                f.tolok_ukur_indikator,f.angka_tahun'.$this->getTahunKe($req->tahun_renja).' as angka_tahun, h.tahun_'.$this->getTahunKe($req->tahun_renja).' AS tahun_renja FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                INNER JOIN trx_renstra_program_indikator AS f ON f.id_program_renstra = e.id_program_renstra,ref_tahun AS h) a
                INNER JOIN trx_renja_rancangan_program b
                ON a.tahun_renja = b.tahun_renja AND a.id_unit = b.id_unit AND a.id_program_renstra = b.id_program_renstra,
                (SELECT @id:=0) c WHERE a.tahun_renja='.$req->tahun_renja.' and a.id_unit='.$req->id_unit);

    }

    public function getTransKegiatan($tahun,$unit,$id_program)
    {
        $getTransKegiatan=DB::SELECT('SELECT a.tahun_rkpd,(@id:=@id+1) AS no_urut,b.id_renja_program,a.id_rkpd_renstra,b.id_rkpd_ranwal,a.id_unit,a.id_visi_renstra,
                a.id_misi_renstra,a.id_tujuan_renstra,a.id_sasaran_renstra,a.id_program_renstra,b.uraian_program_renstra,
                a.id_kegiatan_renstra,a.uraian_kegiatan_renstra,a.pagu_tahun_kegiatan as pagu_tahun_renstra,a.pagu_tahun_kegiatan, b.status_program_rkpd
                FROM trx_rkpd_renstra AS a
                INNER JOIN trx_renja_rancangan_program AS b ON a.tahun_rkpd = b.tahun_renja AND a.id_program_rpjmd = b.id_program_rpjmd
                AND a.id_unit = b.id_unit AND a.id_visi_renstra = b.id_visi_renstra AND a.id_misi_renstra = b.id_misi_renstra
                AND a.id_tujuan_renstra = b.id_tujuan_renstra AND a.id_sasaran_renstra = b.id_sasaran_renstra AND a.id_program_renstra = b.id_program_renstra,
                (SELECT @id:=0) c WHERE a.tahun_rkpd='.$tahun.' and a.id_unit='.$unit.' and a.id_program_renstra='.$id_program);
        
        return json_encode($getTransKegiatan);
    }

    public function transKegiatanRenja(Request $req)
    {
        $data = new TrxRenjaRancangan();
        $data->no_urut = $req->no_urut ;
        $data->tahun_renja = $req->tahun_renja ;
        $data->id_renja_program = $req->id_renja_program ;
        $data->id_rkpd_ranwal = $req->id_rkpd_ranwal ;
        $data->id_rkpd_renstra = $req->id_rkpd_renstra ;
        $data->id_unit = $req->id_unit ;
        $data->id_visi_renstra = $req->id_visi_renstra ;
        $data->id_misi_renstra = $req->id_misi_renstra ;
        $data->id_tujuan_renstra = $req->id_tujuan_renstra ;
        $data->id_sasaran_renstra = $req->id_sasaran_renstra ;
        $data->id_program_renstra = $req->id_program_renstra ;
        $data->uraian_program_renstra = $req->uraian_program_renstra ;
        $data->id_kegiatan_renstra = $req->id_kegiatan_renstra ;
        $data->id_kegiatan_ref = $req->id_kegiatan_ref ;
        $data->uraian_kegiatan_renstra = $req->uraian_kegiatan_renstra ;
        $data->pagu_tahun_renstra = 0;
        $data->pagu_tahun_kegiatan = $req->pagu_tahun_kegiatan ;
        $data->pagu_tahun_selanjutnya = $req->pagu_tahun_selanjutnya ;
        $data->status_pelaksanaan_kegiatan = 4 ;
        $data->ket_usulan = $req->ket_usulan ;
        $data->status_data = 0 ;        
        $data->sumber_data = 1 ;
        $data->save (['timestamps' => false]);

        return response ()->json ($data);
    }

    public function transKegiatanIndikatorRenja(Request $req)
    {
      try{
        $result=DB::INSERT('INSERT INTO trx_renja_rancangan_indikator(tahun_renja,no_urut,id_renja,id_perubahan,kd_indikator,
              uraian_indikator_kegiatan_renja,tolok_ukur_indikator,angka_tahun,angka_renstra,status_data,sumber_data)
              SELECT b.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja,0 as id_perubahan,a.kd_indikator,a.uraian_indikator_kegiatan,
              a.tolokukur_kegiatan,a.target_output,a.target_output,0 as status_data,0 
              FROM trx_rkpd_renstra_indikator AS a
              INNER JOIN trx_renja_rancangan AS b ON b.tahun_renja = a.tahun_rkpd AND b.id_rkpd_renstra = a.id_rkpd_renstra,
              (SELECT @id:=0) c WHERE b.tahun_renja='.$req->tahun_renja.' and b.id_unit='.$req->id_unit);

        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         // $error_code = $e->getMessage() ;
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }

    }

    public function getProgramRkpd($tahun_renja,$id_unit)
    {
      $programrenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT b.id_rkpd_ranwal, b.tahun_renja, b.id_unit, b.uraian_rkpd, b.uraian_program_rpjmd, SUM(COALESCE(a.jml_program,0)) as jml_program, SUM(COALESCE(a.jml_indikator,0)) as jml_indikator, SUM(COALESCE(a.jml_kegiatan,0)) as jml_kegiatan, 
                    SUM(COALESCE(a.jml_pagu,0)) as jml_pagu FROM 
                    (SELECT x.id_rkpd_ranwal, x.tahun_renja,x.id_unit, x.uraian_program_rpjmd as uraian_rkpd, d.uraian_program_rpjmd
                    FROM trx_renja_rancangan_program_ranwal x 
                    INNER JOIN trx_rkpd_ranwal a ON a.id_rkpd_ranwal = x.id_rkpd_ranwal and a.tahun_rkpd = x.tahun_renja
                    LEFT OUTER JOIN trx_rpjmd_program d ON a.id_sasaran_rpjmd = d.id_sasaran_rpjmd AND a.id_program_rpjmd = d.id_program_rpjmd) b
                    LEFT OUTER JOIN
                    (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_program_rpjmd, a.id_unit,  
                    COUNT(a.id_renja_program)as jml_program, sum(c.jml_indikator) as jml_indikator, sum(b.jml_kegiatan) as jml_kegiatan, 
                          sum(b.jml_pagu) as jml_pagu
                    FROM trx_renja_rancangan_program a 
                    LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, a.id_program_renstra, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator
                    FROM trx_renja_rancangan_program_indikator a
                    LEFT OUTER JOIN trx_renja_rancangan_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program AND a.id_program_renstra = b.id_program_renstra 
                    GROUP BY a.tahun_renja, a.id_renja_program, a.id_program_renstra) c 
                    ON a.tahun_renja = c.tahun_renja AND a.id_renja_program = c.id_renja_program AND a.id_program_renstra= c.id_program_renstra 
                    LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_program_renstra, a.id_unit, 
                    COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan, COALESCE(SUM(a.pagu_tahun_kegiatan)) AS jml_pagu
                    FROM trx_renja_rancangan a
                    GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_program_renstra,a.id_unit) b 
                    ON a.tahun_renja = b.tahun_renja AND a.id_renja_program = b.id_renja_program AND a.id_program_renstra= b.id_program_renstra                  
                    GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_program_rpjmd,a.id_unit) a
                    ON b.tahun_renja = a.tahun_renja AND b.id_rkpd_ranwal = a.id_rkpd_ranwal AND b.id_unit = a.id_unit 
                    WHERE b.id_unit ='.$id_unit.' AND b.tahun_renja='.$tahun_renja.' GROUP BY b.id_rkpd_ranwal, b.tahun_renja, b.id_unit, b.uraian_rkpd, b.uraian_program_rpjmd) a,(SELECT @id:=0) z');

      return DataTables::of($programrenja)
        ->addColumn('action',function($programrenja){
            return '<a class="view-rekap btn btn-info btn-labeled" data-id_rkpd_ranwal="'.$programrenja->id_rkpd_ranwal.'" data-id_unit="'.$programrenja->id_unit.'" data-tahun_renja="'.$programrenja->tahun_renja.'" data-uraian_program_rpjmd="'.$programrenja->uraian_program_rpjmd.'" data-uraian_rkpd="'.$programrenja->uraian_rkpd.'"><span class="btn-label"><i class="fa fa-briefcase fa-fw fa-lg"></i></span>Lihat Program Renja</a>';
        })
          ->make(true);
    }

    public function getCekProgram($id_tahun,$id_renja){
    $CekProgram=DB::select('SELECT a.tahun_renja, a.id_renja_program, 
            COALESCE(e.jml_indikator,0) - COALESCE(e.jml_0i,0) as cek_indikator,
            COALESCE(f.jml_kegiatan,0) - COALESCE(f.jml_0k,0) as cek_kegiatan,
            COALESCE(f.jml_pagu,0) as jml_pagu
            FROM trx_renja_rancangan_program a
            LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator,
            COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0i
            FROM trx_renja_rancangan_program_indikator a
            LEFT OUTER JOIN trx_renja_rancangan_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program
            GROUP BY a.tahun_renja, a.id_renja_program) e
            ON a.tahun_renja = e.tahun_renja AND a.id_renja_program = e.id_renja_program
            LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit,
            COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan, COALESCE(SUM(a.pagu_tahun_kegiatan)) AS jml_pagu,
            COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0k
            FROM trx_renja_rancangan a
            WHERE a.status_pelaksanaan_kegiatan <> 2 AND a.status_pelaksanaan_kegiatan <> 3 
            GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit) f
            ON a.tahun_renja = f.tahun_renja AND a.id_renja_program = f.id_renja_program
            WHERE a.tahun_renja='.$id_tahun.' AND a.id_renja_program='.$id_renja);

    return $CekProgram;

    }

    public function getProgramRenja($tahun_renja,$id_unit,$id_ranwal)
    {
      $getProgramRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_renja, a.no_urut, a.id_renja_program, a.id_rkpd_ranwal, a.id_program_rpjmd, a.jenis_belanja,
            a.id_unit, a.id_visi_renstra, a.id_misi_renstra, a.id_tujuan_renstra, a.id_sasaran_renstra, 
            a.id_program_renstra, a.uraian_program_renstra as uraian_program_renja, a.pagu_tahun_ranwal, a.pagu_tahun_renstra, 
            a.status_program_rkpd, a.sumber_data_rkpd, a.sumber_data, a.ket_usulan, a.status_data, b.nm_unit, c.uraian_program_renstra,
            a.id_program_ref,d.kd_program,d.uraian_program,COALESCE(e.jml_indikator,0) as jml_indikator,COALESCE(e.jml_0i,0) as jml_0i,COALESCE(f.jml_kegiatan,0) as jml_kegiatan,COALESCE(f.jml_pagu,0) as jml_pagu,COALESCE(f.jml_0k,0) as jml_0k,a.status_pelaksanaan,
              CASE a.status_data
                  WHEN 0 THEN "fa fa-question"
                  WHEN 1 THEN "fa fa-check-square-o"
              END AS status_icon,
              CASE a.status_data
                  WHEN 0 THEN "red"
                  WHEN 1 THEN "green"
              END AS warna 
            FROM trx_renja_rancangan_program a
            INNER JOIN ref_unit b ON a.id_unit = b.id_unit
            LEFT OUTER JOIN trx_renstra_program c ON a.id_program_renstra = c.id_program_renstra
            LEFT OUTER JOIN (select a.id_program, CONCAT(LEFT(CONCAT(0,c.kd_urusan),2),".",RIGHT(CONCAT(b.kd_bidang,0),2),".",a.kd_program) AS kd_program, a.uraian_program  
            FROM ref_program a
            INNER JOIN ref_bidang b ON a.id_bidang = b.id_bidang
            INNER JOIN ref_urusan c ON b.kd_urusan = c.kd_urusan) d ON a.id_program_ref = d.id_program
            LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator,
            COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0i
            FROM trx_renja_rancangan_program_indikator a
            LEFT OUTER JOIN trx_renja_rancangan_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program
            GROUP BY a.tahun_renja, a.id_renja_program) e
            ON a.tahun_renja = e.tahun_renja AND a.id_renja_program = e.id_renja_program
            LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit,
            COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan, COALESCE(SUM(a.pagu_tahun_kegiatan)) AS jml_pagu,
            COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0k
            FROM trx_renja_rancangan a
            GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit) f
            ON a.tahun_renja = f.tahun_renja AND a.id_renja_program = f.id_renja_program
            WHERE a.id_unit ='.$id_unit.' AND a.tahun_renja='.$tahun_renja.' AND a.id_rkpd_ranwal='.$id_ranwal.') a,(SELECT @id:=0) z');

      return DataTables::of($getProgramRenja)
      ->addColumn('details_url', function($getProgramRenja) {
                    return url('renja/sesuai/getIndikatorRenja/'.$getProgramRenja->id_renja_program);
                })
        ->addColumn('action', function ($getProgramRenja) {
          if ($getProgramRenja->status_data==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="add-indikator dropdown-item" data-id_rkpd_ranwal="'.$getProgramRenja->id_rkpd_ranwal.'" data-id_renja_program="'.$getProgramRenja->id_renja_program.'" data-id_program_rpjmd="'.$getProgramRenja->id_program_rpjmd.'" data-uraian_program_renja="'.$getProgramRenja->uraian_program_renja.'" data-id_program_renstra="'.$getProgramRenja->id_program_renstra.'" data-uraian_program_renstra="'.$getProgramRenja->uraian_program_renstra.'" data-tahun_renja="'.$getProgramRenja->tahun_renja.'" data-status_data="'.$getProgramRenja->status_data.'" data-sumber_data="'.$getProgramRenja->sumber_data.'" data-status_pelaksanaan="'.$getProgramRenja->status_pelaksanaan.'"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Program</a>
                        </li>
                        <li>
                            <a class="view-kegiatan dropdown-item" data-id_rkpd_ranwal="'.$getProgramRenja->id_rkpd_ranwal.'" data-id_renja_program="'.$getProgramRenja->id_renja_program.'" data-id_program_rpjmd="'.$getProgramRenja->id_program_rpjmd.'" data-uraian_program_renja="'.$getProgramRenja->uraian_program_renja.'" data-id_program_renstra="'.$getProgramRenja->id_program_renstra.'" data-uraian_program_renstra="'.$getProgramRenja->uraian_program_renstra.'" data-tahun_renja="'.$getProgramRenja->tahun_renja.'" data-status_data="'.$getProgramRenja->status_data.'" data-sumber_data="'.$getProgramRenja->sumber_data.'" data-status_pelaksanaan="'.$getProgramRenja->status_pelaksanaan.'" data-id_program_ref="'.$getProgramRenja->id_program_ref.'"><i class="fa fa-building-o fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                        </li>
                        <li>
                            <a class="edit-program dropdown-item" data-id_rkpd_ranwal="'.$getProgramRenja->id_rkpd_ranwal.'" data-id_renja_program="'.$getProgramRenja->id_renja_program.'" data-no_urut="'.$getProgramRenja->no_urut.'" data-uraian_program_renja="'.$getProgramRenja->uraian_program_renja.'" data-uraian_program_renstra="'.$getProgramRenja->uraian_program_renstra.'" data-id_visi_renstra="'.$getProgramRenja->id_visi_renstra.'" data-id_misi_renstra="'.$getProgramRenja->id_misi_renstra.'" data-id_tujuan_renstra="'.$getProgramRenja->id_tujuan_renstra.'" data-id_sasaran_renstra="'.$getProgramRenja->id_sasaran_renstra.'" data-id_program_renstra="'.$getProgramRenja->id_program_renstra.'" data-id_program_ref="'.$getProgramRenja->id_program_ref.'" data-uraian_program_ref="'.$getProgramRenja->uraian_program.'" data-kd_program="'.$getProgramRenja->kd_program.'" data-tahun_renja="'.$getProgramRenja->tahun_renja.'" data-status_data="'.$getProgramRenja->status_data.'" data-id_unit="'.$getProgramRenja->id_unit.'" data-nm_unit="'.$getProgramRenja->nm_unit.'" data-jenis_belanja="'.$getProgramRenja->jenis_belanja.'" data-ket_usulan="'.$getProgramRenja->ket_usulan.'" data-sumber_data="'.$getProgramRenja->sumber_data.'" data-status_pelaksanaan="'.$getProgramRenja->status_pelaksanaan.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Program Renja</a>
                        </li>
                        <li>
                            <a id="btnUnProgram" class="dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Renja</a>
                        </li>                          
                    </ul>
                </div>
            ';
          if ($getProgramRenja->status_data==1)
            return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-kegiatan dropdown-item" data-id_rkpd_ranwal="'.$getProgramRenja->id_rkpd_ranwal.'" data-id_renja_program="'.$getProgramRenja->id_renja_program.'" data-id_program_rpjmd="'.$getProgramRenja->id_program_rpjmd.'" data-uraian_program_renja="'.$getProgramRenja->uraian_program_renja.'" data-id_program_renstra="'.$getProgramRenja->id_program_renstra.'" data-uraian_program_renstra="'.$getProgramRenja->uraian_program_renstra.'" data-tahun_renja="'.$getProgramRenja->tahun_renja.'" data-status_data="'.$getProgramRenja->status_data.'" data-sumber_data="'.$getProgramRenja->sumber_data.'" data-status_pelaksanaan="'.$getProgramRenja->status_pelaksanaan.'" data-id_program_ref="'.$getProgramRenja->id_program_ref.'"><i class="fa fa-building-o fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                        </li>
                        <li>
                            <a id="btnUnProgram" class="dropdown-item"><i class="fa fa-times fa-fw fa-lg"></i> Un-Posting Renja</a>
                        </li>                        
                    </ul>
              </div>
            ';
        })
        ->make(true);
    }

    public function getIndikatorRenja($id_rkpd)
    {
      $indikatorProg=DB::select('SELECT a.tahun_renja,(@id:=@id+1) as urut,b.id_rkpd_ranwal,a.id_indikator_program_renja,a.kd_indikator,
                a.uraian_indikator_program_renja,a.tolok_ukur_indikator,a.target_renstra,a.target_renja,
                a.status_data,a.sumber_data, b.status_pelaksanaan as status_pelaksanaan_renja,
                b.status_data as status_program_renja,b.sumber_data as sumber_program_renja,a.id_renja_program,
                            CASE a.status_data
                              WHEN 0 THEN "fa fa-question"
                              WHEN 1 THEN "fa fa-check-square-o"
                            END AS status_icon,
                          CASE a.status_data
                              WHEN 0 THEN "red"
                              WHEN 1 THEN "green"
                          END AS warna  
                FROM trx_renja_rancangan_program_indikator a
                INNER JOIN trx_renja_rancangan_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program 
                ,(SELECT @id:=0) x where a.id_renja_program='.$id_rkpd);

      return DataTables::of($indikatorProg)
        ->addColumn('action', function ($indikatorProg) {
          if($indikatorProg->status_program_renja==0 ){
            if($indikatorProg->status_pelaksanaan_renja!=2 && $indikatorProg->status_pelaksanaan_renja!=3){
              return '<button class="edit-indikator btn btn-warning btn-labeled" data-toggle="tooltip" title="Edit Indikator Program Renja" data-id_indikator_program_renja="'.$indikatorProg->id_indikator_program_renja.'" data-id_renja_program="'.$indikatorProg->id_renja_program.'" data-id_rkpd_ranwal="'.$indikatorProg->id_rkpd_ranwal.'" data-no_urut="'.$indikatorProg->urut.'" data-uraian_indikator_program_renja="'.$indikatorProg->uraian_indikator_program_renja.'" data-ur_tolokukur_renja="'.$indikatorProg->tolok_ukur_indikator.'" data-kd_indikator_renja="'.$indikatorProg->kd_indikator.'" data-status_data="'.$indikatorProg->status_data.'" data-sumber_data="'.$indikatorProg->sumber_data.'" data-target_renstra="'.$indikatorProg->target_renstra.'" data-target_renja="'.$indikatorProg->target_renja.'"><span class="btn-label"><i class="fa fa-pencil fa-fw fa-lg"></i></span>Edit Indikator</button>';
              }
            }

            // if($indikatorProg->status_program_renja==1 ){
            //     return '<a class="view-indikator dropdown-item" data-id_rkpd_ranwal="'.$getProgramRenja->id_rkpd_ranwal.'" data-id_renja_program="'.$getProgramRenja->id_renja_program.'" data-id_program_rpjmd="'.$getProgramRenja->id_program_rpjmd.'" data-uraian_program_renja="'.$getProgramRenja->uraian_program_renja.'" data-id_program_renstra="'.$getProgramRenja->id_program_renstra.'" data-uraian_program_renstra="'.$getProgramRenja->uraian_program_renstra.'" data-tahun_renja="'.$getProgramRenja->tahun_renja.'" data-status_data="'.$getProgramRenja->status_data.'" data-sumber_data="'.$getProgramRenja->sumber_data.'" data-status_pelaksanaan="'.$getProgramRenja->status_pelaksanaan.'"><i class="fa fa-bullseye fa-fw fa-lg"></i> Lihat Indikator</a>';
            //   }
          })
        ->make(true); 
    }

    public function getCekKegiatan($id_tahun,$id_renja){
    $CekKegiatan=DB::select('SELECT a.tahun_renja, a.id_renja, 
            COALESCE(COUNT(a.id_indikator_kegiatan_renja),0) - COALESCE(COUNT(CASE WHEN a.status_data=1 THEN status_data END),0) as cek_indikator
            FROM trx_renja_rancangan_indikator a
            WHERE a.tahun_renja='.$id_tahun.' AND a.id_renja='.$id_renja.' GROUP BY a.tahun_renja, a.id_renja');
    return $CekKegiatan;

    } 

    public function getKegiatanRenja($id_program)
    {
      $getKegiatanRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_renja, a.no_urut, a.id_renja, a.id_renja_program, a.id_rkpd_renstra, a.id_rkpd_ranwal, a.id_unit, a.id_visi_renstra, a.id_misi_renstra, a.id_tujuan_renstra, a.id_sasaran_renstra,a.id_program_renstra, a.uraian_program_renstra, a.id_kegiatan_renstra, a.id_kegiatan_ref, a.uraian_kegiatan_renstra as uraian_kegiatan_renja, a.pagu_tahun_renstra, a.pagu_tahun_kegiatan, a.pagu_tahun_selanjutnya, a.status_pelaksanaan_kegiatan, a.sumber_data, a.ket_usulan, a.status_data, COALESCE(b.jml_indikator,0) as jml_indikator, COALESCE(b.jml_0i,0) as jml_0i,c.uraian_kegiatan_renstra,d.id_program_ref,
            CASE a.status_data
                          WHEN 0 THEN "fa fa-question"
                          WHEN 1 THEN "fa fa-check-square-o"
                      END AS status_icon,
                      CASE a.status_data
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna, e.kd_kegiatan, e.nm_kegiatan,f.nm_unit, d.status_data as status_program 
            FROM trx_renja_rancangan a
            LEFT OUTER JOIN (SELECT tahun_renja, id_renja, count(id_indikator_kegiatan_renja) as jml_indikator ,
            COUNT(CASE WHEN status_data=1 THEN status_data END) as jml_0i
            FROM trx_renja_rancangan_indikator
            GROUP BY tahun_renja, id_renja) b ON a.tahun_renja=b.tahun_renja AND a.id_renja = b.id_renja
            LEFT OUTER JOIN trx_renstra_kegiatan c ON a.id_program_renstra = c.id_program_renstra AND a.id_kegiatan_renstra = c.id_kegiatan_renstra
            INNER JOIN trx_renja_rancangan_program d ON a.id_renja_program = d.id_renja_program
            INNER JOIN (Select a.id_kegiatan, a.id_program, a.nm_kegiatan,
                    CONCAT(LEFT(CONCAT(0,d.kd_urusan),2),".",RIGHT(CONCAT(0,c.kd_bidang),2),".",RIGHT(CONCAT("00",b.kd_program),3),".",RIGHT(CONCAT("00",a.kd_kegiatan),3)) AS kd_kegiatan
                    FROM ref_kegiatan a
                    INNER JOIN ref_program b ON a.id_program=b.id_program
                    INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
                    INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan) e
                    ON a.id_kegiatan_ref = e.id_kegiatan
            INNER JOIN ref_unit f ON a.id_unit = f.id_unit
            WHERE a.id_renja_program='.$id_program.') a,(SELECT @id:=0) z');

      return DataTables::of($getKegiatanRenja)
      ->addColumn('details_url', function($getKegiatanRenja) {
                    return url('renja/sesuai/getIndikatorKegiatan/'.$getKegiatanRenja->id_renja);
                })
        ->addColumn('action', function ($getKegiatanRenja) {
          if ($getKegiatanRenja->status_data==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="add-indikatorKeg dropdown-item" data-id_rkpd_ranwal="'.$getKegiatanRenja->id_rkpd_ranwal.'" data-id_renja_program="'.$getKegiatanRenja->id_renja_program.'" data-uraian_kegiatan_renja="'.$getKegiatanRenja->uraian_kegiatan_renja.'" data-uraian_kegiatan_renstra="'.$getKegiatanRenja->uraian_kegiatan_renstra.'" data-tahun_renja="'.$getKegiatanRenja->tahun_renja.'" data-status_data="'.$getKegiatanRenja->status_data.'" data-sumber_data="'.$getKegiatanRenja->sumber_data.'" data-status_pelaksanaan="'.$getKegiatanRenja->status_pelaksanaan_kegiatan.'" data-id_renja="'.$getKegiatanRenja->id_renja.'"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Kegiatan</a>
                        </li>
                        <li>
                            <a class="edit-kegiatan dropdown-item" data-tahun_renja="'.$getKegiatanRenja->tahun_renja.'"
                                data-no_urut="'.$getKegiatanRenja->no_urut.'"
                                data-id_renja="'.$getKegiatanRenja->id_renja.'"
                                data-id_renja_program="'.$getKegiatanRenja->id_renja_program.'"
                                data-id_rkpd_renstra="'.$getKegiatanRenja->id_rkpd_renstra.'"
                                data-id_rkpd_ranwal="'.$getKegiatanRenja->id_rkpd_ranwal.'"
                                data-id_unit="'.$getKegiatanRenja->id_unit.'"
                                data-nm_unit="'.$getKegiatanRenja->nm_unit.'"
                                data-id_visi_renstra="'.$getKegiatanRenja->id_visi_renstra.'"
                                data-id_misi_renstra="'.$getKegiatanRenja->id_misi_renstra.'"
                                data-id_tujuan_renstra="'.$getKegiatanRenja->id_tujuan_renstra.'"
                                data-id_sasaran_renstra="'.$getKegiatanRenja->id_sasaran_renstra.'"
                                data-id_program_renstra="'.$getKegiatanRenja->id_program_renstra.'"
                                data-uraian_program_renstra="'.$getKegiatanRenja->uraian_program_renstra.'"
                                data-id_kegiatan_renstra="'.$getKegiatanRenja->id_kegiatan_renstra.'"
                                data-id_kegiatan_ref="'.$getKegiatanRenja->id_kegiatan_ref.'"
                                data-kd_kegiatan="'.$getKegiatanRenja->kd_kegiatan.'"
                                data-nm_kegiatan="'.$getKegiatanRenja->nm_kegiatan.'"
                                data-uraian_kegiatan_renja="'.$getKegiatanRenja->uraian_kegiatan_renja.'"
                                data-uraian_kegiatan_renstra="'.$getKegiatanRenja->uraian_kegiatan_renstra.'"
                                data-pagu_tahun_renstra="'.$getKegiatanRenja->pagu_tahun_renstra.'"
                                data-pagu_tahun_kegiatan="'.$getKegiatanRenja->pagu_tahun_kegiatan.'"
                                data-pagu_tahun_selanjutnya="'.$getKegiatanRenja->pagu_tahun_selanjutnya.'"
                                data-status_pelaksanaan_kegiatan="'.$getKegiatanRenja->status_pelaksanaan_kegiatan.'"
                                data-sumber_data="'.$getKegiatanRenja->sumber_data.'"
                                data-ket_usulan="'.$getKegiatanRenja->ket_usulan.'"
                                data-status_data="'.$getKegiatanRenja->status_data.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Kegiatan Renja</a>
                        </li>
                        <li>
                            <a id="btnUnKegiatan" class="dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Renja</a>
                        </li>                           
                    </ul>
                </div>
            ';
          if ($getKegiatanRenja->status_data==1 && $getKegiatanRenja->status_program!=1 )
            return '<a id="btnUnKegiatan" class="btn btn-warning btn-labeled"><span class="btn-label"><i class="fa fa-times fa-fw fa-lg"></i></span>Un-Posting Renja</a>
            ';
        })
        ->make(true);
    } 

    public function getIndikatorKegiatan($id_renja)
    {
      $indikatorKeg=DB::select('SELECT (@id:=@id+1) as urut,a.tahun_renja, a.no_urut, a.id_renja, a.id_indikator_kegiatan_renja, a.id_perubahan, a.kd_indikator, a.uraian_indikator_kegiatan_renja, a.tolok_ukur_indikator, a.angka_tahun, a.angka_renstra, a.status_data, a.sumber_data, CASE a.status_data
              WHEN 0 THEN "fa fa-question"
              WHEN 1 THEN "fa fa-check-square-o"
          END AS status_icon,
          CASE a.status_data
              WHEN 0 THEN "red"
              WHEN 1 THEN "green"
          END AS warna,b.status_pelaksanaan_kegiatan, b.status_data as status_kegiatan
            FROM trx_renja_rancangan_indikator a
            INNER JOIN trx_renja_rancangan b ON a.id_renja = b.id_renja,(SELECT @id:=0) x where a.id_renja='.$id_renja);

      return DataTables::of($indikatorKeg)
        ->addColumn('action', function ($indikatorKeg) {
          if($indikatorKeg->status_kegiatan==0 ){
          if($indikatorKeg->status_pelaksanaan_kegiatan!=2 && $indikatorKeg->status_pelaksanaan_kegiatan!=3){
            return '<a class="edit-indikatorKeg btn btn-warning btn-labeled" data-tahun_renja="'.$indikatorKeg->tahun_renja.'"
                    data-no_urut="'.$indikatorKeg->no_urut.'"
                    data-id_renja="'.$indikatorKeg->id_renja.'"
                    data-id_indikator_kegiatan_renja="'.$indikatorKeg->id_indikator_kegiatan_renja.'"
                    data-id_perubahan="'.$indikatorKeg->id_perubahan.'"
                    data-kd_indikator="'.$indikatorKeg->kd_indikator.'"
                    data-uraian_indikator_kegiatan_renja="'.$indikatorKeg->uraian_indikator_kegiatan_renja.'"
                    data-tolok_ukur_indikator="'.$indikatorKeg->tolok_ukur_indikator.'"
                    data-angka_tahun="'.$indikatorKeg->angka_tahun.'"
                    data-angka_renstra="'.$indikatorKeg->angka_renstra.'"
                    data-status_data="'.$indikatorKeg->status_data.'"
                    data-sumber_data="'.$indikatorKeg->sumber_data.'"
                    data-status_pelaksanaan_kegiatan="'.$indikatorKeg->status_pelaksanaan_kegiatan.'"
                    data-status_kegiatan="'.$indikatorKeg->status_kegiatan.'">
                    <span class="btn-label"><i class="fa fa-pencil fa-fw fa-lg"></i></span>Edit Indikator</a>';
            }
            }
          })
        ->make(true); 
    } 

public function getProgRenstra($id_unit)
{
   $ProgRenstra=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut,e.id_visi_renstra,d.id_misi_renstra,c.id_tujuan_renstra,b.id_sasaran_renstra,a.id_program_renstra, a.uraian_program_renstra, f.uraian_program, f.id_program
        FROM trx_renstra_program a
        INNER JOIN trx_renstra_sasaran b on a.id_sasaran_renstra = b.id_sasaran_renstra
        INNER JOIN trx_renstra_tujuan c on b.id_tujuan_renstra = c.id_tujuan_renstra
        INNER JOIN trx_renstra_misi d on c.id_misi_renstra = d.id_misi_renstra
        INNER JOIN trx_renstra_visi e on d.id_visi_renstra = e.id_visi_renstra
        INNER JOIN ref_program f on a.id_program_ref = f.id_program,(SELECT @id:=0) x
        WHERE e.id_unit='.$id_unit);

   return DataTables::of($ProgRenstra)
   ->make(true);
}

public function getKegRenstra($id_unit,$id_program)
{
   $KegRenstra=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut,e.id_visi_renstra,d.id_misi_renstra,c.id_tujuan_renstra,b.id_sasaran_renstra,a.id_program_renstra, f.id_kegiatan_renstra, f.id_kegiatan_ref, f.uraian_kegiatan_renstra, g.nm_kegiatan,g.id_kegiatan
        FROM trx_renstra_kegiatan f
        INNER JOIN trx_renstra_program a on f.id_program_renstra = a.id_program_renstra
        INNER JOIN trx_renstra_sasaran b on a.id_sasaran_renstra = b.id_sasaran_renstra
        INNER JOIN trx_renstra_tujuan c on b.id_tujuan_renstra = c.id_tujuan_renstra
        INNER JOIN trx_renstra_misi d on c.id_misi_renstra = d.id_misi_renstra
        INNER JOIN trx_renstra_visi e on d.id_visi_renstra = e.id_visi_renstra
        INNER JOIN ref_kegiatan g on f.id_kegiatan_ref = g.id_kegiatan,(SELECT @id:=0) x
        WHERE e.id_unit='.$id_unit.' AND f.id_program_renstra='.$id_program);

   return DataTables::of($KegRenstra)
   ->make(true);
}

public function getKegRef($id_program)
{
   $KegRef=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.id_kegiatan, a.id_program, a.kd_kegiatan, a.nm_kegiatan,
        CONCAT(LEFT(CONCAT(0,d.kd_urusan),2),".",RIGHT(CONCAT(0,c.kd_bidang),2),".",RIGHT(CONCAT("00",b.kd_program),3),".",RIGHT(CONCAT("00",a.kd_kegiatan),3)) AS kd_kegiatan
        FROM ref_kegiatan a
        INNER JOIN ref_program b ON a.id_program=b.id_program
        INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
        INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan,(SELECT @id:=0) x
        WHERE a.id_program='.$id_program);

   return DataTables::of($KegRef)
   ->make(true);
}

public function getProgRef($id_bidang)
{
   $ProgRef=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.id_program, CONCAT(LEFT(CONCAT(0,c.kd_urusan),2),".",RIGHT(CONCAT(0,b.kd_bidang),2),".",RIGHT(CONCAT("00",a.kd_program),3)) AS kd_program, a.uraian_program  
            FROM ref_program a
            INNER JOIN ref_bidang b ON a.id_bidang = b.id_bidang
            INNER JOIN ref_urusan c ON b.kd_urusan = c.kd_urusan,(SELECT @id:=0) x WHERE a.id_bidang='.$id_bidang.'');

   return DataTables::of($ProgRef)
   ->make(true);
}

public function getUrusan(){
      $urusan=DB::select('SELECT kd_urusan, nm_urusan FROM ref_urusan');
        return json_encode($urusan);
}

public function getBidang($id_unit,$id_ranwal){
        $urusan=DB::select('SELECT a.tahun_rkpd, a.id_rkpd_ranwal, a.id_bidang, d.nm_bidang, b.id_unit
            FROM trx_rkpd_ranwal_urusan a
            INNER JOIN trx_rkpd_ranwal_pelaksana b ON a.id_rkpd_ranwal=b.id_rkpd_ranwal AND a.id_urusan_rkpd = b.id_urusan_rkpd
            INNER JOIN ref_bidang d ON a.id_bidang = d.id_bidang
            WHERE b.id_unit='.$id_unit.' and a.id_rkpd_ranwal='.$id_ranwal);
        
        return json_encode($urusan);
}

public function getRefIndikator(){
      $refindikator=DB::SELECT('SELECT (@id:=@id+1) as no_urut, id_indikator, jenis_indikator,  
          sifat_indikator, nm_indikator, flag_iku, asal_indikator, sumber_data_indikator,
          case jenis_indikator 
          when 1 then "Positif"
          when 2 then "Negatif"
          end as jenis_display,
          case sifat_indikator
          when 0 then "Belum terindentifikasi" 
          when 1 then "Incremental"
          when 2 then "Absolut"
          when 3 then "Komulatif"
          end as sifat_display
          FROM ref_indikator,(SELECT @id:=0 ) var_id           
          where asal_indikator <> 0');
      return DataTables::of($refindikator)
      ->make(true);
    }

public function getCheckKegiatan($id_renja)
{
   $getCheckKegiatan=DB::SELECT('SELECT tahun_renja, id_renja, (COALESCE(count(id_indikator_kegiatan_renja),0) - COALESCE(COUNT(CASE WHEN status_data=1 THEN status_data END),0)) as jml_check 
        FROM trx_renja_rancangan_indikator
        WHERE id_renja = '.$id_renja.'
        GROUP BY tahun_renja, id_renja');

   return json_encode($getCheckKegiatan);
}

public function getCheckProgram($id_program)
{
   $getCheckProgram=DB::SELECT('SELECT a.tahun_renja, a.id_renja_program, 
        (COALESCE(e.jml_indikator,0) - COALESCE(e.jml_0i,0)) as jml_0i,
        (COALESCE(f.jml_kegiatan,0) - COALESCE(f.jml_0k,0)) as jml_0k
        FROM trx_renja_rancangan_program a
        INNER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator,
        COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0i
        FROM trx_renja_rancangan_program_indikator a
        GROUP BY a.tahun_renja, a.id_renja_program) e
        ON a.tahun_renja = e.tahun_renja AND a.id_renja_program = e.id_renja_program
        INNER JOIN (SELECT a.tahun_renja, a.id_renja_program,
        COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan,
        COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0k
        FROM trx_renja_rancangan a
        GROUP BY a.tahun_renja, a.id_renja_program) f
        ON a.tahun_renja = f.tahun_renja AND a.id_renja_program = f.id_renja_program
        WHERE a.id_renja_program = '.$id_program);

   return json_encode($getCheckProgram);
}

public function CheckProgram($id_program)
{
   $CheckProgram=DB::SELECT('SELECT * FROM trx_renja_rancangan_program
        WHERE id_renja_program = '.$id_program.' LIMIT 1');

   return ($CheckProgram);
}

public function CheckKegiatan($id_renja)
{
   $CheckKegiatan=DB::SELECT('SELECT * FROM trx_renja_rancangan
        WHERE id_renja = '.$id_renja.' LIMIT 1');

   return ($CheckKegiatan);
}

}