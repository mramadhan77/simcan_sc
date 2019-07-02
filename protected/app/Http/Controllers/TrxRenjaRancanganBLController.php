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
use App\Models\TrxRenjaRancanganAktivitas;
use App\Models\TrxRenjaRancanganLokasi;
use App\Models\TrxRenjaRancanganBelanja;
use Auth;

class TrxRenjaRancanganBLController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, Builder $htmlBuilder, $id = null)
    {
        // if(Auth::check()){ 
            $unit = \App\Models\RefSubUnit::select();
            if(isset(Auth::user()->getUserSubUnit)){
                foreach(Auth::user()->getUserSubUnit as $data){
                    $unit->orWhere(['id_unit' => $data->kd_unit, 'kd_sub' => $data->kd_sub]);                
                }
            }
            $unit = $unit->get();
            return view('renja.index')->with(compact('unit'));
        // } else {
            // return view ( 'errors.401' );
        // }
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
        $data->id_renja_ranwal = $req->id_renja_ranwal ;
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
            $data->status_pelaksanaan = $req->status_pelaksanaan ;
                    
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
              catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }

    }

    public function postProgram(Request $req)
    {
        $cek=$this->getCekProgram($req->tahun_renja,$req->id_renja_program);        

            $data = TrxRenjaRancanganProgram::find($req->id_renja_program);
            $data->status_data = $req->status_data ;

            if($req->status_data==1){
              if($req->status_pelaksanaan!=2 || $req->status_pelaksanaan!=3){
                  if($cek[0]->cek_kegiatan==0){
                    try{
                          $data->save (['timestamps' => false]);
                          return response ()->json (['pesan'=>'Data Program Renja Berhasil di-Posting','status_pesan'=>'1']);
                        }
                        catch(QueryException $e){
                           $error_code = $e->errorInfo[1] ;
                           return response ()->json (['pesan'=>'Data Program Renja Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
                        } 
                      } else {
                        return response ()->json (['pesan'=>'Data Program Renja Gagal di-Posting (Silahkan Cek Kegiatan)','status_pesan'=>'0']);
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

        if($req->target_renja >= 0){
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
        $result = TrxRenjaRancanganProgramIndikator::where('id_indikator_program_renja',$req->id_indikator_program_renja)->delete ();
        if($result!=0){
          return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        } else {
          return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
        }
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
            if($cek[0]->jml_aktivitas!=0){
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
              if($cek[0]->jml_aktivitas!=0){
                try{
                    $data->save (['timestamps' => false]);
                    return response ()->json (['pesan'=>'Data Kegiatan Renja Berhasil di-Posting','status_pesan'=>'1']);
                  }
                  catch(QueryException $e){
                     $error_code = $e->errorInfo[1] ;
                     return response ()->json (['pesan'=>'Data Kegiatan Renja Gagal di-Posting ','status_pesan'=>'0']);
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

        if($req->angka_tahun >= 0){
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


    public function addAktivitas(Request $req)
    {
        try{
            $data = new TrxRenjaRancanganAktivitas;
            $data->tahun_renja= $req->tahun_renja;
            $data->no_urut= $req->no_urut;
            $data->id_renja= $req->id_renja;
            $data->sumber_aktivitas= $req->sumber_aktivitas;
            $data->id_aktivitas_asb= $req->id_aktivitas_asb;
            $data->uraian_aktivitas_kegiatan= $req->uraian_aktivitas_kegiatan;
            $data->tolak_ukur_aktivitas= $req->tolak_ukur_aktivitas;
            $data->target_output_aktivitas= $req->target_output_aktivitas;
            $data->id_program_nasional= $req->id_program_nasional;
            $data->id_program_provinsi= $req->id_program_provinsi;
            $data->jenis_kegiatan= $req->jenis_kegiatan;
            $data->sumber_dana= $req->sumber_dana;
            $data->pagu_aktivitas= $req->pagu_aktivitas;
            $data->id_satuan_publik= $req->id_satuan_publik;
            $data->pagu_musren= $req->pagu_musren;
            $data->status_data= 0;
            $data->status_musren= $req->status_musren;
            $data->volume_1= $req->volume_1;
            $data->volume_2= $req->volume_2;
            $data->id_satuan_1= $req->id_satuan_1;
            $data->id_satuan_2= $req->id_satuan_2;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

    public function editAktivitas(Request $req)
    {
            $data = TrxRenjaRancanganAktivitas::find($req->id_aktivitas_renja);
            $data->tahun_renja= $req->tahun_renja;
            $data->no_urut= $req->no_urut;
            $data->id_renja= $req->id_renja;
            $data->sumber_aktivitas= $req->sumber_aktivitas;
            $data->id_aktivitas_asb= $req->id_aktivitas_asb;
            $data->uraian_aktivitas_kegiatan= $req->uraian_aktivitas_kegiatan;
            $data->tolak_ukur_aktivitas= $req->tolak_ukur_aktivitas;
            $data->target_output_aktivitas= $req->target_output_aktivitas;
            $data->id_program_nasional= $req->id_program_nasional;
            $data->id_program_provinsi= $req->id_program_provinsi;
            $data->jenis_kegiatan= $req->jenis_kegiatan;
            $data->sumber_dana= $req->sumber_dana;
            $data->pagu_aktivitas= $req->pagu_aktivitas;
            $data->id_satuan_publik= $req->id_satuan_publik;
            $data->pagu_musren= $req->pagu_musren;
            $data->status_data= $req->status_data;
            $data->status_musren= $req->status_musren;
            $data->volume_1= $req->volume_1;
            $data->volume_2= $req->volume_2;
            $data->id_satuan_1= $req->id_satuan_1;
            $data->id_satuan_2= $req->id_satuan_2;

            try{ 
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); }
            catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }  
          
    }

    public function postAktivitas(Request $req)
    {
            $data = TrxRenjaRancanganAktivitas::find($req->id_aktivitas_renja);
            $data->status_data= $req->status_data;

            if($req->status_data==1){
            if($req->jml_belanja >= 0){
              // if($req->jml_belanja <= ($req->jml_pagu*(103/100)) && $req->jml_belanja >= ($req->jml_pagu*(97/100))){
                try{ 
                $data->save (['timestamps' => false]);
                    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                }
              // } else {
              //    return response ()->json (['pesan'=>'Jumlah Pagu Belanja Masih Belum Sesuai dengan Pagu Aktivitas','status_pesan'=>'0']); 
              // }
            } else {
                return response ()->json (['pesan'=>'Jumlah Pagu Belanja Masih Kosong (Rp. 0,00)','status_pesan'=>'0']);
              } 
            } else {
              try{ 
                $data->save (['timestamps' => false]);
                    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                }  
            }            
          
    }

    public function hapusAktivitas(Request $req)
      {
        TrxRenjaRancanganAktivitas::where('id_aktivitas_renja',$req->id_aktivitas_renja)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

    public function addPelaksana(Request $req)
    {
        try{
            $data = new TrxRenjaRancanganPelaksana;
            $data->tahun_renja= $req->tahun_renja;
            $data->no_urut= $req->no_urut;
            $data->id_renja= $req->id_renja;
            $data->id_aktivitas_renja= $req->id_aktivitas_renja;
            $data->id_sub_unit= $req->id_sub_unit;
            $data->id_lokasi= $req->id_lokasi;
            $data->status_data= 0;
            $data->status_pelaksanaan= null;
            $data->ket_usul= null;
            $data->sumber_data= 1;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

    public function editPelaksana(Request $req)
    {      
      $cek=$this->getCekPelaksana($req->tahun_renja,$req->id_pelaksana_renja);

            $data = TrxRenjaRancanganPelaksana::find($req->id_pelaksana_renja);
            $data->tahun_renja= $req->tahun_renja;
            $data->no_urut= $req->no_urut;
            $data->id_renja= $req->id_renja;
            $data->id_aktivitas_renja= $req->id_aktivitas_renja;
            $data->id_sub_unit= $req->id_sub_unit;
            $data->id_lokasi= $req->id_lokasi;
            $data->status_data= $req->status_data;
            $data->status_pelaksanaan= null;
            $data->ket_usul= null;

            if($req->status_data==1){
              if($cek[0]->jml_pagu!=0){
                try{ 
                  $data->save (['timestamps' => false]);
                  return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); }
                catch(QueryException $e){
                  $error_code = $e->errorInfo[1] ;
                  return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                } 
              } else {
                return response ()->json (['pesan'=>'Pelaksana belum memiliki aktivitas/pagu aktivitas masih kosong','status_pesan'=>'0']);
              }
            } else {
              try{ 
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); }
              catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
              } 
            }
            
    }

    public function hapusPelaksana(Request $req)
      {
        $data = TrxRenjaRancanganPelaksana::where('id_pelaksana_renja',$req->id_pelaksana_renja)->delete();
        if($data != 0){
          return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        } else {
          return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
        }
        
      }

        public function addLokasi(Request $req)
    {
        try{
            $data = new TrxRenjaRancanganLokasi;
            $data->tahun_renja = $req->tahun_renja ;
            $data->no_urut = $req->no_urut ;
            $data->id_pelaksana_renja = $req->id_pelaksana_renja ;
            $data->jenis_lokasi = $req->jenis_lokasi ;
            $data->id_lokasi = $req->id_lokasi ;
            $data->volume_1 = $req->volume_1 ;
            $data->volume_2 = $req->volume_2 ;
            $data->id_satuan_1 = $req->id_satuan_1 ;
            $data->id_satuan_2 = $req->id_satuan_2 ;
            $data->uraian_lokasi = $req->uraian_lokasi ;
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
            $data = TrxRenjaRancanganLokasi::find($req->id_lokasi_renja);
            $data->tahun_renja = $req->tahun_renja ;
            $data->no_urut = $req->no_urut ;
            $data->id_pelaksana_renja = $req->id_pelaksana_renja ;
            $data->jenis_lokasi = $req->jenis_lokasi ;
            $data->id_lokasi = $req->id_lokasi ;            
            $data->volume_1 = $req->volume_1 ;
            $data->volume_2 = $req->volume_2 ;
            $data->id_satuan_1 = $req->id_satuan_1 ;
            $data->id_satuan_2 = $req->id_satuan_2 ;
            $data->uraian_lokasi = $req->uraian_lokasi ;
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
        TrxRenjaRancanganLokasi::where('id_lokasi_renja',$req->id_lokasi_renja)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

    public function addBelanja(Request $req)
    {
        try{
            $data = new TrxRenjaRancanganBelanja;
            $data->tahun_renja= $req->tahun_renja;
            $data->no_urut = $req->no_urut ;
            $data->id_lokasi_renja= $req->id_lokasi_renja;
            $data->id_zona_ssh = $req->id_zona_ssh ;
            $data->sumber_aktivitas = $req->sumber_aktivitas ;
            $data->id_aktivitas_asb = $req->id_aktivitas_asb ;
            $data->id_tarif_ssh = $req->id_tarif_ssh ;
            $data->id_rekening_ssh = $req->id_rekening_ssh ;
            $data->uraian_belanja = $req->uraian_belanja ;
            $data->volume_1 = $req->volume_1 ;
            $data->id_satuan_1 = $req->id_satuan_1 ;
            $data->volume_2 = $req->volume_2 ;
            $data->id_satuan_2 = $req->id_satuan_2 ;
            $data->harga_satuan = $req->harga_satuan ;
            $data->jml_belanja = $req->jml_belanja ;
            $data->status_data= 0;
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
            $data = TrxRenjaRancanganBelanja::find($req->id_belanja_renja);
            $data->tahun_renja= $req->tahun_renja;
            $data->no_urut = $req->no_urut ;
            $data->id_lokasi_renja= $req->id_lokasi_renja;
            $data->id_zona_ssh = $req->id_zona_ssh ;
            $data->sumber_aktivitas = $req->sumber_aktivitas ;
            $data->id_aktivitas_asb = $req->id_aktivitas_asb ;
            $data->id_tarif_ssh = $req->id_tarif_ssh ;
            $data->id_rekening_ssh = $req->id_rekening_ssh ;
            $data->uraian_belanja = $req->uraian_belanja ;
            $data->volume_1 = $req->volume_1 ;
            $data->id_satuan_1 = $req->id_satuan_1 ;
            $data->volume_2 = $req->volume_2 ;
            $data->id_satuan_2 = $req->id_satuan_2 ;
            $data->harga_satuan = $req->harga_satuan ;
            $data->jml_belanja = $req->jml_belanja ;
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
        $getHapus = TrxRenjaRancanganBelanja::where('id_belanja_renja',$req->id_belanja_renja)->delete();

        if($getHapus != 0){
        return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']); 
        } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
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

    public function getProgramRkpd($tahun_renja,$id_unit)
    {
      $programrenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT b.id_renja_ranwal, b.id_rkpd_ranwal, b.tahun_renja, b.id_unit, b.uraian_rkpd, b.uraian_program_rpjmd, SUM(COALESCE(a.jml_program,0)) as jml_program, SUM(COALESCE(a.jml_indikator,0)) as jml_indikator, SUM(COALESCE(a.jml_kegiatan,0)) as jml_kegiatan, 
                    SUM(COALESCE(a.jml_pagu,0)) as jml_pagu FROM 
                    (SELECT x.id_renja_ranwal, x.id_rkpd_ranwal, x.tahun_renja,x.id_unit, x.uraian_program_rpjmd as uraian_rkpd, d.uraian_program_rpjmd
                    FROM trx_renja_rancangan_program_rkpd x 
                    INNER JOIN trx_rkpd_ranwal a ON a.id_rkpd_ranwal = x.id_rkpd_ranwal and a.tahun_rkpd = x.tahun_renja
                    LEFT OUTER JOIN trx_rpjmd_program d ON a.id_sasaran_rpjmd = d.id_sasaran_rpjmd AND a.id_program_rpjmd = d.id_program_rpjmd) b
                    LEFT OUTER JOIN
                    (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_program_rpjmd, a.id_unit,  
                    COUNT(a.id_renja_program)as jml_program, sum(c.jml_indikator) as jml_indikator, sum(b.jml_kegiatan) as jml_kegiatan, 
                          sum(b.jml_pagu) as jml_pagu
                    FROM trx_renja_rancangan_program a 
                    LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator
                    FROM trx_renja_rancangan_program_indikator a
                    LEFT OUTER JOIN trx_renja_rancangan_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program
                    GROUP BY a.tahun_renja, a.id_renja_program) c 
                    ON a.tahun_renja = c.tahun_renja AND a.id_renja_program = c.id_renja_program
                    LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit, 
                    COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan, COALESCE(SUM(a.pagu_tahun_kegiatan)) AS jml_pagu
                    FROM trx_renja_rancangan a
                    GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit) b 
                    ON a.tahun_renja = b.tahun_renja AND a.id_renja_program = b.id_renja_program                
                    GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_program_rpjmd,a.id_unit) a
                    ON b.tahun_renja = a.tahun_renja AND b.id_rkpd_ranwal = a.id_rkpd_ranwal AND b.id_unit = a.id_unit 
                    WHERE b.id_unit ='.$id_unit.' AND b.tahun_renja='.$tahun_renja.' GROUP BY b.id_renja_ranwal, b.id_rkpd_ranwal, b.tahun_renja, b.id_unit, b.uraian_rkpd, b.uraian_program_rpjmd) a,(SELECT @id:=0) z');

      return DataTables::of($programrenja)
        ->addColumn('action',function($programrenja){
            return '<a class="view-rekap btn btn-info btn-labeled"><span class="btn-label"><i class="fa fa-briefcase fa-fw fa-lg"></i></span>Lihat Program Renja</a>';
        })
          ->make(true);
    }

    public function getCekProgram($id_tahun,$id_renja){
    $CekProgram=DB::select('SELECT a.tahun_renja, a.id_renja_program, 
            COALESCE(e.jml_indikator,0) - COALESCE(e.jml_0i,0) as cek_indikator,
            COALESCE(f.jml_kegiatan,0) - COALESCE(f.jml_0k,0) as cek_kegiatan,
            COALESCE(f.jml_pagu,0) as jml_pagu,
            COALESCE(e.jml_indikator,0) as jml_indikator,
            COALESCE(f.jml_kegiatan,0) as jml_kegiatan
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

    public function getProgramRenja($tahun_renja,$id_unit)
    {
      $getProgramRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_renja, a.no_urut, a.id_renja_program, a.id_rkpd_ranwal, a.id_program_rpjmd, a.jenis_belanja,
            a.id_unit, a.id_visi_renstra, a.id_misi_renstra, a.id_tujuan_renstra, a.id_sasaran_renstra, 
            a.id_program_renstra, a.uraian_program_renstra as uraian_program_renja, a.pagu_tahun_ranwal, a.pagu_tahun_renstra, 
            a.status_program_rkpd, a.sumber_data_rkpd, a.sumber_data, a.ket_usulan, a.status_data, b.nm_unit, c.uraian_program_renstra,
            a.id_program_ref,d.kd_program,d.uraian_program,COALESCE(e.jml_indikator,0) as jml_indikator,COALESCE(e.jml_0i,0) as jml_0i,COALESCE(f.jml_kegiatan,0) as jml_kegiatan,COALESCE(f.jml_pagu,0) as jml_pagu,COALESCE(f.jml_0k,0) as jml_0k,a.status_pelaksanaan,
              CASE a.status_data
              WHEN 0 THEN "fa fa-question"
              WHEN 1 THEN "fa fa-check-square-o"
              WHEN 2 THEN "fa fa-thumbs-o-up"
              ELSE "fa fa-exclamation"
              END AS status_icon,
              CASE a.status_data
                  WHEN 0 THEN "red"
                  WHEN 1 THEN "green"
                  WHEN 2 THEN "blue"
                  ELSE "red"
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
            WHERE a.id_unit ='.$id_unit.' AND a.tahun_renja='.$tahun_renja.') a,(SELECT @id:=0) z');

      return DataTables::of($getProgramRenja)
        ->addColumn('details_url', function($getProgramRenja) {
                    return url('renja/blang/getIndikatorRenja/'.$getProgramRenja->id_renja_program);
                })
        ->addColumn('action', function ($getProgramRenja) {
          if ($getProgramRenja->status_data==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        
                        <li>
                            <a class="view-kegiatan dropdown-item"><i class="fa fa-building-o fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                        </li>
                        <li>
                            <a class="edit-program dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg"></i> Lihat Program Renja</a>
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
                            <a class="view-kegiatan dropdown-item"><i class="fa fa-building-o fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                        </li>
                        <li>
                            <a id="btnUnProgram" class="dropdown-item"><i class="fa fa-times fa-fw fa-lg"></i> Un-Posting Renja</a>
                        </li>                        
                    </ul>
              </div>
            ';

          if ($getProgramRenja->status_data==2)
            return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-kegiatan dropdown-item"><i class="fa fa-building-o fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
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
              return '<button class="edit-indikator btn btn-warning btn-labeled" data-toggle="tooltip" title="Edit Indikator Program Renja"><span class="btn-label"><i class="fa fa-list-alt fa-fw fa-lg"></i></span>Lihat Indikator</button>';
              }
            }
          })
        ->make(true); 
    }

    public function getCekKegiatan($id_tahun,$id_renja){
      $CekKegiatan=DB::select('SELECT a.tahun_renja, a.id_renja, b.cek_indikator, a.pagu_tahun_kegiatan-COALESCE(g.jml_pagu,0) as cek_pagu, COALESCE(g.jml_aktivitas,0) as jml_aktivitas
              FROM trx_renja_rancangan a
              LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja, 
              COALESCE(COUNT(a.id_indikator_kegiatan_renja),0) - COALESCE(COUNT(CASE WHEN a.status_data=1 THEN status_data END),0) as cek_indikator
              FROM trx_renja_rancangan_indikator a
              GROUP BY a.tahun_renja, a.id_renja) b ON a.tahun_renja = b.tahun_renja AND a.id_renja = b.id_renja
              LEFT OUTER JOIN (SELECT a.tahun_renja,a.id_renja,COALESCE(COUNT(a.id_pelaksana_renja),0) as jml_pelaksana,COALESCE(SUM(b.jml_aktivitas),0) as jml_aktivitas,
              COALESCE(SUM(b.jml_pagu),0) as jml_pagu
              FROM trx_renja_rancangan_pelaksana a
              LEFT OUTER JOIN (SELECT COUNT(a.id_aktivitas_renja) as jml_aktivitas,
              SUM(a.pagu_aktivitas) as jml_pagu, a.tahun_renja, a.id_renja
              FROM trx_renja_rancangan_aktivitas AS a 
              WHERE a.status_data = 1 GROUP BY a.tahun_renja, a.id_renja) b ON a.tahun_renja = b.tahun_renja AND a.id_pelaksana_renja = b.id_renja
              WHERE a.status_data = 1 GROUP BY a.tahun_renja,a.id_renja, a.status_data) g ON a.tahun_renja = g.tahun_renja AND a.id_renja = g.id_renja
              WHERE a.tahun_renja='.$id_tahun.' AND a.id_renja='.$id_renja);
      return $CekKegiatan;
    } 

    public function getKegiatanRenja($id_program)
    {
      $getKegiatanRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_renja, a.no_urut, a.id_renja, a.id_renja_program, a.id_rkpd_renstra, a.id_rkpd_ranwal, a.id_unit, a.id_visi_renstra, a.id_misi_renstra, a.id_tujuan_renstra, a.id_sasaran_renstra,a.id_program_renstra, a.uraian_program_renstra, a.id_kegiatan_renstra, a.id_kegiatan_ref, a.uraian_kegiatan_renstra as uraian_kegiatan_renja, a.pagu_tahun_renstra, a.pagu_tahun_kegiatan, a.pagu_tahun_selanjutnya, a.status_pelaksanaan_kegiatan, a.sumber_data, a.ket_usulan, a.status_data, COALESCE(b.jml_indikator,0) as jml_indikator, COALESCE(b.jml_0i,0) as jml_0i,c.uraian_kegiatan_renstra,d.id_program_ref,g.jml_pelaksana, g.jml_pagu, g.jml_aktivitas,
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
            LEFT OUTER JOIN (SELECT a.tahun_renja,a.id_renja,COALESCE(COUNT(a.id_pelaksana_renja),0) as jml_pelaksana,COALESCE(SUM(b.jml_aktivitas),0) as jml_aktivitas,
            COALESCE(SUM(b.jml_pagu),0) as jml_pagu
            FROM trx_renja_rancangan_pelaksana a
            LEFT OUTER JOIN (SELECT COUNT(a.id_aktivitas_renja) as jml_aktivitas,
            SUM(a.pagu_aktivitas) as jml_pagu, a.tahun_renja, a.id_renja
            FROM trx_renja_rancangan_aktivitas AS a
            GROUP BY a.tahun_renja, a.id_renja) b ON a.tahun_renja = b.tahun_renja AND a.id_pelaksana_renja = b.id_renja
            INNER JOIN ref_sub_unit d ON a.id_sub_unit = d.id_sub_unit
            LEFT OUTER JOIN ref_lokasi e ON a.id_lokasi = e.id_lokasi
            GROUP BY a.tahun_renja,a.id_renja) g ON a.tahun_renja = g.tahun_renja AND a.id_renja = g.id_renja 
            WHERE a.id_renja_program='.$id_program.') a,(SELECT @id:=0) z');

      return DataTables::of($getKegiatanRenja)
      ->addColumn('details_url', function($getKegiatanRenja) {
                    return url('renja/blang/getIndikatorKegiatan/'.$getKegiatanRenja->id_renja);
                })
        ->addColumn('action', function ($getKegiatanRenja) {
          if ($getKegiatanRenja->status_program!=2){
          if ($getKegiatanRenja->status_data==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-pelaksana dropdown-item">
                                <i class="fa fa-male fa-fw fa-lg"></i> Lihat Pelaksana</a>
                        </li>
                        <li>
                            <a class="edit-kegiatan dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                        </li>
                        <li>
                            <a id="btnUnKegiatan" class="dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Renja</a>
                        </li>                           
                    </ul>
                </div>
            ';
          if ($getKegiatanRenja->status_data==1 && $getKegiatanRenja->status_program!=1 )
            return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-pelaksana dropdown-item">
                                <i class="fa fa-male fa-fw fa-lg"></i> Lihat Pelaksana</a>
                        </li>
                        <li>
                            <a class="edit-kegiatan dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                        </li>
                        <li>
                            <a id="btnUnKegiatan" class="dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Un-Posting Renja</a>
                        </li>                           
                    </ul>
                </div>
            ';
          if ($getKegiatanRenja->status_data==1 && $getKegiatanRenja->status_program==1 )
            return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-pelaksana dropdown-item">
                                <i class="fa fa-male fa-fw fa-lg"></i> Lihat Pelaksana</a>
                        </li>
                        <li>
                            <a class="edit-kegiatan dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                        </li>                        
                    </ul>
              </div>
            ';
          } else {
            return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-pelaksana dropdown-item">
                                <i class="fa fa-male fa-fw fa-lg"></i> Lihat Pelaksana</a>
                        </li>
                        <li>
                            <a class="edit-kegiatan dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                        </li>                        
                    </ul>
              </div>
            ';
          }
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
            return '<a class="edit-indikatorKeg btn btn-warning btn-labeled">
                    <span class="btn-label"><i class="fa fa-list-alt fa-fw fa-lg"></i></span>Lihat Indikator</a>';
            }
            }
          })
        ->make(true); 
    }

public function getAktivitas($id_pelaksana)
{
   $getAktivitas=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.tahun_renja,a. no_urut as nomor, a.id_aktivitas_renja, a.id_renja, 
                a.sumber_aktivitas, a.id_aktivitas_asb,a.uraian_aktivitas_kegiatan, a.tolak_ukur_aktivitas, 
                a.target_output_aktivitas,a.id_satuan_publik, a.id_program_nasional, a.id_program_provinsi, a.jenis_kegiatan, 
                a.sumber_dana, a.pagu_aktivitas, a.pagu_musren, a.status_data, a.status_musren, 
                a.pagu_aktivitas-(a.pagu_aktivitas*(a.pagu_musren/100)) as jml_musren_aktivitas,
                COALESCE(a.volume_1,0) as volume_1, COALESCE(a.volume_2,0) as volume_2, COALESCE(a.id_satuan_1,0) as id_satuan_1, COALESCE(a.id_satuan_2,0) as id_satuan_2, i.status_data as status_program,
                c.uraian_satuan as uraian_satuan_1, d.uraian_satuan as uraian_satuan_2, a.pagu_rata2,
                CASE a.status_data
                          WHEN 0 THEN "fa fa-question fa-fw fa-lg"
                          WHEN 1 THEN "fa fa-check-square-o fa-fw fa-lg"
                      END AS status_icon,
                CASE a.status_data
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna,
                CASE a.sumber_aktivitas
                          WHEN 0 THEN "fa fa-registered"
                          WHEN 1 THEN ""
                      END AS img,COALESCE(g.jml_vol_lok,0) as jml_vol_lok,
                COALESCE(f.jml_pagu,0) as jml_pagu_belanja,
                CASE a.sumber_aktivitas
                  WHEN 0 THEN 
                      CASE a.id_satuan_publik 
                        WHEN 0 THEN COALESCE(g.jml_vol_lok,0)
                        WHEN 1 THEN COALESCE(a.volume_1,0)
                      END
                  WHEN 1 THEN
                    COALESCE(g.volume_1,0)
                END AS jml_vol_1,
                CASE a.sumber_aktivitas
                  WHEN 0 THEN 
                      CASE a.id_satuan_publik 
                        WHEN 1 THEN COALESCE(g.jml_vol_lok,0)
                        WHEN 0 THEN COALESCE(a.volume_2,0)
                      END
                  WHEN 1 THEN
                    COALESCE(g.volume_2,0)
                END AS jml_vol_2 
                FROM trx_renja_rancangan_aktivitas a 
                LEFT OUTER JOIN trx_asb_aktivitas b ON a.id_aktivitas_asb = b.id_aktivitas_asb
                LEFT OUTER JOIN ref_satuan c ON a.id_satuan_1 = c.id_satuan
                LEFT OUTER JOIN ref_satuan d ON a.id_satuan_2 = d.id_satuan
                INNER JOIN trx_renja_rancangan_pelaksana e ON a.id_renja = e.id_pelaksana_renja
                LEFT OUTER JOIN (SELECT id_lokasi_renja, COALESCE(SUM(jml_belanja),0) as jml_pagu  FROM trx_renja_rancangan_belanja 
                GROUP BY id_lokasi_renja) f ON a.id_aktivitas_renja = f.id_lokasi_renja
                LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_pelaksana_renja,
                CASE b.id_satuan_publik 
                  WHEN 0 THEN sum(a.volume_1)
                  WHEN 1 THEN sum(a.volume_2)
                END AS jml_vol_lok,
                SUM(IF(a.id_satuan_1 <> -1 AND a.id_satuan_1 <> 0, a.volume_1, 0)) as volume_1,
                SUM(IF(a.id_satuan_2 <> -1 AND a.id_satuan_2 <> 0, a.volume_2, 0)) as volume_2 
                FROM trx_renja_rancangan_lokasi a
                INNER JOIN trx_renja_rancangan_aktivitas b ON a.id_pelaksana_renja = b.id_aktivitas_renja
                GROUP BY a.tahun_renja, a.id_pelaksana_renja,b.id_satuan_publik) g ON a.id_aktivitas_renja = g.id_pelaksana_renja
                INNER JOIN trx_renja_rancangan h ON e.id_renja = h.id_renja
                INNER JOIN trx_renja_rancangan_program i ON h.id_renja_program = i.id_renja_program,
                (SELECT @id:=0) x WHERE a.id_renja='.$id_pelaksana);

   return DataTables::of($getAktivitas)
       ->addColumn('details_url', function($getAktivitas) {
                    return url('renja/blang/getLokasiAktivitas/'.$getAktivitas->id_aktivitas_renja);
                })
       ->addColumn('action', function ($getAktivitas) {
          if($getAktivitas->status_program!=2){
            if($getAktivitas->status_data==0)
            return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnTambahLokasi" class="add-lokasi dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Lokasi</a>
                        </li>
                        <li>
                            <a class="view-belanja dropdown-item">
                                <i class="fa fa-shopping-cart fa-fw fa-lg text-primary"></i> Lihat Rincian Belanja</a>
                        </li>
                        <li>
                            <a class="edit-aktivitas dropdown-item">
                                <i class="fa fa-list-alt fa-fw fa-lg text-warning"></i> Lihat Aktivitas Renja</a>
                        </li>
                        <li>
                            <a class="post-aktivitas dropdown-item">
                                <i class="fa fa-check-square-o fa-fw fa-lg text-success"></i> Posting Aktivitas Renja</a>
                        </li>                           
                    </ul>
                </div>                         
            ';
            if($getAktivitas->status_data==1)
            return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-belanja dropdown-item">
                                <i class="fa fa-shopping-cart fa-fw fa-lg text-primary"></i> Lihat Rincian Belanja</a>
                        </li>
                        <li>
                            <a class="edit-aktivitas dropdown-item">
                                <i class="fa fa-list-alt fa-fw fa-lg text-warning"></i> Lihat Aktivitas Renja</a>
                        </li>
                        <li>
                            <a class="post-aktivitas dropdown-item">
                                <i class="fa fa-times fa-fw fa-lg text-danger"></i> Un-Posting Aktivitas Renja</a>
                        </li>                           
                    </ul>
                </div>                         
            ';
          } else {
            return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-belanja dropdown-item">
                                <i class="fa fa-shopping-cart fa-fw fa-lg text-primary"></i> Lihat Rincian Belanja</a>
                        </li>
                        <li>
                            <a class="edit-aktivitas dropdown-item">
                                <i class="fa fa-list-alt fa-fw fa-lg text-warning"></i> Lihat Aktivitas Renja</a>
                        </li>                        
                    </ul>
                </div>                         
            ';
          }
        })
   ->make(true);
}

public function getCekPelaksana($id_tahun,$id_pelaksana){
      $CekPelaksana=DB::select('SELECT a.tahun_renja,a.id_pelaksana_renja,COALESCE(COUNT(a.id_pelaksana_renja),0) as jml_pelaksana,
            COALESCE(SUM(b.jml_aktivitas),0) as jml_aktivitas, COALESCE(SUM(b.jml_pagu),0) as jml_pagu
            FROM trx_renja_rancangan_pelaksana a
            LEFT OUTER JOIN (SELECT COUNT(a.id_aktivitas_renja) as jml_aktivitas,
            SUM(a.pagu_aktivitas) as jml_pagu, a.tahun_renja, a.id_renja
            FROM trx_renja_rancangan_aktivitas AS a
            GROUP BY a.tahun_renja, a.id_renja) b ON a.tahun_renja = b.tahun_renja AND a.id_pelaksana_renja = b.id_renja
            WHERE a.tahun_renja='.$id_tahun.' AND a.id_pelaksana_renja='.$id_pelaksana.' 
            GROUP BY a.tahun_renja,a.id_pelaksana_renja');
      return $CekPelaksana;
} 

public function getPelaksanaAktivitas($id_renja){
   $getPelaksana=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_renja,a.no_urut,a.id_pelaksana_renja,a.id_renja,
            a.id_aktivitas_renja,a.id_lokasi, COALESCE(e.nama_lokasi,"Belum ditentukan") as nama_lokasi,a.id_sub_unit,a.status_data,a.status_pelaksanaan,a.ket_usul,a.sumber_data,f.status_data as status_kegiatan,
            d.nm_sub,COALESCE(b.jml_aktivitas,0) as jml_aktivitas,
            COALESCE(b.jml_pagu,0) as jml_pagu,
            CASE a.status_data
                WHEN 0 THEN "fa fa-question"
                WHEN 1 THEN "fa fa-check-square-o"
            END AS status_icon,
            CASE a.status_data
                WHEN 0 THEN "red"
                WHEN 1 THEN "green"
            END AS warna 
            FROM trx_renja_rancangan_pelaksana a
            LEFT OUTER JOIN (SELECT COUNT(a.id_aktivitas_renja) as jml_aktivitas,
            SUM(a.pagu_aktivitas) as jml_pagu, a.tahun_renja, a.id_renja
            FROM trx_renja_rancangan_aktivitas AS a
            GROUP BY a.tahun_renja, a.id_renja) b ON a.tahun_renja = b.tahun_renja AND a.id_pelaksana_renja = b.id_renja
            INNER JOIN ref_sub_unit d ON a.id_sub_unit = d.id_sub_unit
            LEFT OUTER JOIN ref_lokasi e ON a.id_lokasi = e.id_lokasi
            INNER JOIN trx_renja_rancangan f ON a.id_renja = f.id_renja
            GROUP BY a.tahun_renja,a.no_urut,a.id_pelaksana_renja,a.id_renja,a.id_aktivitas_renja,a.id_lokasi, e.nama_lokasi,a.id_sub_unit,a.status_data,a.status_pelaksanaan,a.ket_usul,a.sumber_data,d.nm_sub,b.jml_aktivitas,b.jml_pagu,f.status_data) a,
            (SELECT @id:=0) x WHERE a.id_renja='.$id_renja);

   return DataTables::of($getPelaksana)
   ->addColumn('action', function ($getPelaksana) {
        if($getPelaksana->status_kegiatan!=0)
        return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-aktivitas dropdown-item">
                                <i class="fa fa-briefcase fa-fw fa-lg"></i> Lihat Aktivitas</a>
                        </li>
                        <li>
                            <a class="edit-pelaksana dropdown-item">
                                <i class="fa fa-pencil fa-fw fa-lg"></i> Lihat Pelaksana Kegiatan</a>
                        </li>                     
                    </ul>
                </div>
            ';
        if($getPelaksana->status_kegiatan==0)
        return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-aktivitas dropdown-item">
                                <i class="fa fa-briefcase fa-fw fa-lg"></i> Lihat Aktivitas</a>
                        </li>
                        <li>
                            <a class="edit-pelaksana dropdown-item">
                                <i class="fa fa-list-alt fa-fw fa-lg"></i> Lihat Pelaksana Kegiatan</a>
                        </li>                       
                    </ul>
                </div>
            ';
   })
   ->make(true);
}

public function getLokasiAktivitas($id_aktivitas)
{
   $LokAktiv=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_renja,a.no_urut,a.id_pelaksana_renja,
        a.id_lokasi_renja,a.jenis_lokasi,a.id_lokasi,b.nama_lokasi,a.uraian_lokasi,COALESCE(SUM(c.jml_belanja),0) as jml_pagu, e.sumber_aktivitas,e.id_aktivitas_asb,e.uraian_aktivitas_kegiatan, a.volume_1, a.volume_2, a.id_satuan_1, a.id_satuan_2,m.nama_desa,n.nama_kecamatan,e.status_musren,e.id_satuan_publik,
        CASE a.id_satuan_1
          WHEN null THEN "Belum Dipilih"
          WHEN 0 THEN "N/A"
          ELSE o.uraian_satuan
          END AS sat1_display,
        CASE a.id_satuan_2
          WHEN null THEN "Belum Dipilih" 
          WHEN 0 THEN "N/A" 
          ELSE p.uraian_satuan 
          END AS sat2_display  
        FROM trx_renja_rancangan_lokasi a
        INNER JOIN ref_lokasi b on a.id_lokasi = b.id_lokasi
        LEFT OUTER JOIN trx_renja_rancangan_belanja c ON a.id_lokasi_renja = c.id_lokasi_renja
        INNER JOIN trx_renja_rancangan_aktivitas e ON a.id_pelaksana_renja = e.id_aktivitas_renja        
        LEFT OUTER JOIN ref_satuan o ON a.id_satuan_1 = o.id_satuan
        LEFT OUTER JOIN ref_satuan p ON a.id_satuan_2 = p.id_satuan
        LEFT OUTER JOIN ref_lokasi q ON a.id_lokasi = q.id_lokasi
        LEFT OUTER JOIN ref_desa m ON q.id_desa = m.id_desa
        LEFT OUTER JOIN ref_kecamatan n ON m.id_kecamatan = n.id_kecamatan
        GROUP BY a.tahun_renja,a.no_urut,a.id_pelaksana_renja,a.id_lokasi_renja,a.jenis_lokasi,
        a.id_lokasi,b.nama_lokasi,a.uraian_lokasi, e.sumber_aktivitas,e.id_aktivitas_asb,e.uraian_aktivitas_kegiatan,a.volume_1, a.volume_2, a.id_satuan_1, a.id_satuan_2,o.uraian_satuan,p.uraian_satuan,m.nama_desa,n.nama_kecamatan,e.status_musren,e.id_satuan_publik) a,(SELECT @id:=0) x WHERE a.id_pelaksana_renja='.$id_aktivitas);

   return DataTables::of($LokAktiv)
   ->addColumn('action', function ($LokAktiv) {
        return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-lokasi dropdown-item">
                                <i class="fa fa-pencil fa-fw fa-lg"></i> Edit Lokasi Aktivitas</a>
                        </li>                          
                    </ul>
                </div>
        ';
   })
   ->make(true);
}

public function getLokasiCopy($id_aktivitas_renja){
   $getBelanja=DB::SELECT('SELECT (@id:=@id+1) as urut, c.id_unit, a.* FROM trx_renja_rancangan_aktivitas AS a
          INNER JOIN trx_renja_rancangan_pelaksana AS b ON a.id_renja = b.id_pelaksana_renja
          INNER JOIN trx_renja_rancangan AS c ON b.id_renja = c.id_renja,
          (SELECT @id:=0) x WHERE c.id_unit='.$id_aktivitas_renja.' AND a.sumber_aktivitas=1 AND a.tahun_renja='.Session::get('tahun'));

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
   $getBelanja=DB::INSERT('INSERT INTO trx_renja_rancangan_belanja (tahun_renja, no_urut, id_lokasi_renja,id_zona_ssh,sumber_aktivitas,
            id_aktivitas_asb,id_tarif_ssh,id_rekening_ssh,uraian_belanja,volume_1,id_satuan_1,volume_2,
            id_satuan_2,harga_satuan,jml_belanja,status_data)
            SELECT a.tahun_renja, a.no_urut, '.$req->id_lokasi_new.',a.id_zona_ssh,a.sumber_aktivitas,
            a.id_aktivitas_asb,a.id_tarif_ssh,a.id_rekening_ssh,a.uraian_belanja,a.volume_1,a.id_satuan_1,a.volume_2,
            a.id_satuan_2,a.harga_satuan,a.jml_belanja,a.status_data
            FROM trx_renja_rancangan_belanja AS a WHERE a.id_lokasi_renja='.$req->id_lokasi);

   if($getBelanja!=0) {
        return response ()->json (['pesan'=>'Data Berhasil Dicopy','status_pesan'=>'1']);
    } else {
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal DiCopy ('.$error_code.')','status_pesan'=>'0']);
    };
}

public function getBelanja($id_lokasi){
   $getBelanja=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_renja,a.no_urut,a.id_belanja_renja,a.id_lokasi_renja,
            a.id_zona_ssh,a.sumber_aktivitas,a.id_aktivitas_asb,a.id_tarif_ssh,a.id_rekening_ssh,
            a.uraian_belanja,a.volume_1,a.id_satuan_1,a.volume_2,a.id_satuan_2,a.harga_satuan,
            a.jml_belanja,a.status_data,COALESCE(b.uraian_tarif_ssh,a.uraian_belanja) as uraian_tarif_ssh,c.uraian_satuan as satuan_1, d.uraian_satuan as satuan_2, COALESCE(e.kd_rekening,"0.0.0.0.0") as kd_rekening ,COALESCE(e.nm_rekening,a.uraian_belanja) as nm_rekening, f.nm_aktivitas_asb,
                        CASE a.status_data
                            WHEN 0 THEN "fa fa-question"
                            WHEN 1 THEN "fa fa-check-square-o"
                        END AS status_icon,
                        CASE a.status_data
                            WHEN 0 THEN "red"
                            WHEN 1 THEN "green"
                        END AS warna,
                        CASE a.sumber_aktivitas
                            WHEN 0 THEN a.uraian_belanja
                            WHEN 1 THEN b.uraian_tarif_ssh
                            WHEN 2 THEN a.uraian_belanja
                        END AS uraian_belanja_display
            FROM trx_renja_rancangan_belanja a
            LEFT OUTER JOIN ref_ssh_tarif b on a.id_tarif_ssh = b.id_tarif_ssh
            LEFT OUTER JOIN ref_satuan c on a.id_satuan_1 = c.id_satuan
            LEFT OUTER JOIN ref_satuan d on a.id_satuan_2 = d.id_satuan
            LEFT OUTER JOIN (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
                            a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
                            FROM ref_rek_5 a) e on a.id_rekening_ssh = e.id_rekening
            LEFT OUTER JOIN trx_asb_aktivitas f on a.id_aktivitas_asb = f.id_aktivitas_asb) a,
            (SELECT @id:=0) x WHERE a.id_lokasi_renja='.$id_lokasi);

   return DataTables::of($getBelanja)
   ->addColumn('action', function ($getBelanja) {
        return '
            <a type="button" class="edit-belanja btn btn-warning btn-labeled">
            <span class="btn-label"><i class="fa fa-pencil fa-fw fa-lg"></i></span>Edit Belanja</a>                         
            ';
   })
   ->make(true);
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

public function cekDeviasi(){
    $cekDeviasi=DB::select('SELECT a.* FROM ref_setting AS a
            WHERE a.status_setting = 1 ORDER BY a.tahun_rencana LIMIT 1');
    return $cekDeviasi; 
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

public function getAksesTambah($id_unit,$id_ranwal){
        $getAksesTambah=DB::select('SELECT a.tahun_rkpd, a.id_rkpd_ranwal, a.id_bidang, d.nm_bidang, b.id_unit
            FROM trx_rkpd_ranwal_urusan a
            INNER JOIN trx_rkpd_ranwal_pelaksana b ON a.id_rkpd_ranwal=b.id_rkpd_ranwal AND a.id_urusan_rkpd = b.id_urusan_rkpd
            INNER JOIN ref_bidang d ON a.id_bidang = d.id_bidang
            WHERE b.id_unit='.$id_unit.' and a.id_rkpd_ranwal='.$id_ranwal);
        
        return json_encode($getAksesTambah);
}


public function getHitungPaguASB(Request $req)
{
   $getHitungPaguASB=DB::SELECT('SELECT d.id_aktivitas_asb, SUM(PaguASB(f.jenis_biaya,f.hub_driver,'.$req->volume_1.','.$req->volume_2.',d.range_max,d.range_max1,d.kapasitas_max,d.kapasitas_max1,f.koefisien1,f.koefisien2,f.koefisien3,f.jml_rupiah)) AS jml_pagu FROM trx_asb_aktivitas d
        INNER JOIN trx_asb_komponen e ON d.id_aktivitas_asb = e.id_aktivitas_asb
        INNER JOIN (SELECT a.*, b.jml_rupiah,b.id_zona FROM trx_asb_komponen_rinci a
        INNER JOIN (SELECT a.id_perkada,a.flag,c.jml_rupiah,b.id_zona,c.id_tarif_ssh
        FROM ref_ssh_perkada a
        INNER JOIN ref_ssh_perkada_zona b ON a.id_perkada = b.id_perkada
        INNER JOIN ref_ssh_perkada_tarif c ON c.id_zona_perkada = b.id_zona_perkada
        WHERE a.flag=1 AND a.tahun_berlaku='.Session::get('tahun').' AND b.id_zona=1) b ON a.id_tarif_ssh = b.id_tarif_ssh) f 
        ON e.id_komponen_asb = f.id_komponen_asb
        WHERE d.id_aktivitas_asb='.$req->id_asb.' GROUP BY d.id_aktivitas_asb');
   
   return json_encode($getHitungPaguASB);
}

public function getItemSSH($id_zona,$like_cari)
{

   $getItem=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, b.id_zona, c.id_tarif_ssh, d.uraian_tarif_ssh, 
            c.jml_rupiah, d.id_satuan, e.uraian_satuan, q.id_sub_kelompok_ssh,q.uraian_sub_kelompok_ssh,f.jml_rekening
            FROM ref_ssh_perkada a
            INNER JOIN ref_ssh_perkada_zona b ON a.id_perkada = b.id_perkada
            INNER JOIN ref_ssh_perkada_tarif c ON b.id_zona_perkada = c.id_zona_perkada
            INNER JOIN ref_ssh_tarif d ON c.id_tarif_ssh = d.id_tarif_ssh
            INNER JOIN ref_ssh_sub_kelompok q ON d.id_sub_kelompok_ssh = q.id_sub_kelompok_ssh
            INNER JOIN ref_satuan e ON d.id_satuan = e.id_satuan
            LEFT OUTER JOIN (select id_tarif_ssh, coalesce(count(id_rekening_ssh),0) as jml_rekening 
            from ref_ssh_rekening group by id_tarif_ssh) f ON d.id_tarif_ssh = f.id_tarif_ssh, (SELECT @id:=0) x  
            WHERE a.flag = 1 and b.id_zona = '.$id_zona.' AND LOWER(d.uraian_tarif_ssh) like "%'.$like_cari.'%"');

   return DataTables::of($getItem)
   ->make(true);
}

public function getRekening($id,$tarif)
    {
      if($id > 0){
        $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
                a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
                FROM ref_rek_5 a
                INNER JOIN ref_ssh_rekening b ON a.id_rekening = b.id_rekening
                where (a.kd_rek_1=5 or a.kd_rek_1=4) and b.id_tarif_ssh ='.$tarif.') b, (SELECT @id:=0) a');
      } else {
        $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
            kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 where kd_rek_1=5 or kd_rek_1=4) b, (SELECT @id:=0) a');
      }

      return DataTables::of($refrekening)
      ->make(true);
    }

public function getRekeningBTL()
    {
      
      $refrekeningBtl=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
            a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 a where a.kd_rek_1=5 or a.kd_rek_2=1) b, (SELECT @id:=0) a');

      return DataTables::of($refrekeningBtl)
      ->make(true);
    }

public function getRekeningDapat()
    {
      
      $refrekeningDapat=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
            a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 a where a.kd_rek_1=4) b, (SELECT @id:=0) a');

      return DataTables::of($refrekeningDapat)
      ->make(true);
    }

public function getHitungASB(Request $req){

    // if($req->jns_biaya==0){
    //   $biaya='  b.jenis_biaya=1';
    // } else {
    //   if($req->jns_biaya==1){
    //     $biaya='  b.jenis_biaya<>1';
    //   } else {
    //     $biaya='b.jenis_biaya in (1,2,3)';
    //   }
    // }
   if($req->volume_1 == null || $req->volume_1 == 0){
          $volume_1 = 1;
      } else {
          $volume_1 = $req->volume_1;
      };

      if($req->volume_2 == null || $req->volume_2 == 0){
          $volume_2 = 1;
      } else {
          $volume_2 = $req->volume_2;
      };

    if($req->jns_biaya==1){
        $getHitung=DB::INSERT('INSERT INTO trx_renja_rancangan_belanja(tahun_renja, no_urut, id_lokasi_renja, id_zona_ssh, sumber_aktivitas, id_aktivitas_asb, id_tarif_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, harga_satuan, jml_belanja, status_data)
          VALUES('.$req->tahun_renja.',1,'.$req->id_lokasi_renja.',1,2,'.$req->id_aktivitas_asb.',0,0,"'.$req->nama_aktivitas.'",'.$volume_1.','.$req->id_satuan_1.','.$volume_2.','.$req->id_satuan_2.','.$req->pagu_rata2.','.$req->pagu_asb.',0)');
      } else {        
        $getHitung=DB::INSERT('INSERT INTO trx_renja_rancangan_belanja(tahun_renja, no_urut, id_lokasi_renja, id_zona_ssh, sumber_aktivitas, id_aktivitas_asb, id_tarif_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, harga_satuan, jml_belanja, status_data)
        SELECT '.$req->tahun_renja.',(@id:=@id+1) as no_urut,'.$req->id_lokasi_renja.',1,0,a.id_aktivitas_asb,0,0,a.nm_aktivitas_asb,'.$volume_1.',a.id_satuan_1,'.$volume_2.',a.id_satuan_2,a.jml_pagu,a.jml_pagu,0 FROM (
                SELECT a.id_aktivitas_asb, b.nm_aktivitas_asb,b.id_satuan_1,b.id_satuan_2,
                SUM(PaguASB(b.jenis_biaya,b.hub_driver,'.$volume_1.','.$volume_2.',b.r1,b.r2,b.km1,b.km2,b.kf1,b.kf2,b.kf3,a.harga_satuan)) AS jml_pagu
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
                 WHERE a.id_aktivitas_asb='.$req->id_aktivitas_asb.'
                 GROUP BY a.id_aktivitas_asb, b.nm_aktivitas_asb,b.id_satuan_1,b.id_satuan_2) a, (SELECT @id:=0) z');
      }
    
    if($getHitung != 0){
        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); 
    } else {
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

// public function getHitungASBMusren(Request $req){

//     $getHitung=DB::INSERT('INSERT INTO trx_renja_rancangan_belanja(tahun_renja, no_urut, id_lokasi_renja, id_zona_ssh, sumber_aktivitas, id_aktivitas_asb, id_tarif_ssh, id_rekening_ssh, uraian_belanja, volume_1, id_satuan_1, volume_2, id_satuan_2, harga_satuan, jml_belanja, status_data)
//         SELECT '.$req->tahun_renja.',a.no_urut,'.$req->id_lokasi_renja.','.$req->id_zona_ssh.','.$req->sumber_aktivitas.',a.id_aktivitas_asb,a.id_tarif_ssh,a.id_rekening,a.nm_aktivitas_asb,'.$req->volume_1.',a.id_satuan_1,'.$req->volume_2.',a.id_satuan_2,a.jml_pagu,a.jml_pagu,0 FROM (
//         SELECT (@id:=@id+1) as no_urut, a.id_aktivitas_asb, a.id_komponen_asb, a.id_komponen_asb_rinci, a.id_tarif_ssh, 
//                 a.id_zona, a.harga_satuan, b.nm_aktivitas_asb,b.jenis_biaya,COALESCE(b.hub_driver,0) as hub_driver,COALESCE(b.koef,0) as koef,COALESCE(b.r1,0)as r1,COALESCE(b.r2,0) as r2,b.id_satuan_1,b.sat_derivatif_1,b.id_satuan_2,b.sat_derivatif_2,b.sat_display_1,b.sat_display_2,COALESCE(b.kf1,0) as kf1,COALESCE(b.kf2,0) as kf2,COALESCE(b.kf3,0) as kf3,COALESCE(b.km1,0) as km1,COALESCE(b.km2,0) as km2,b.id_rekening,
//                 PaguASB(b.jenis_biaya,b.hub_driver,'.$req->volume_1.','.$req->volume_2.',b.r1,b.r2,b.km1,b.km2,b.kf1,b.kf2,b.kf3,a.harga_satuan) AS jml_pagu
//                 FROM trx_asb_perhitungan_rinci a
//                 INNER JOIN (SELECT a.id_komponen_asb_rinci,c.id_aktivitas_asb,c.nm_aktivitas_asb,b.id_komponen_asb,a.id_tarif_ssh,a.jenis_biaya,a.hub_driver,a.koefisien1 * a.koefisien2*a.koefisien3 as koef,
//                 c.range_max as r1, c.range_max1 as r2,c.id_satuan_1,c.sat_derivatif_1,c.id_satuan_2,c.sat_derivatif_2,
//                 case when COALESCE(c.sat_derivatif_1,0) < 1 then d.uraian_satuan else e.uraian_satuan end as sat_display_1,
//                 case when COALESCE(c.id_satuan_2,0) > 0 then 
//                 (case when COALESCE(c.sat_derivatif_2,0) < 1 then f.uraian_satuan else g.uraian_satuan end )
//                     else "NA" end as sat_display_2, COALESCE(a.koefisien1,0) as kf1, COALESCE(a.koefisien2,0) as kf2, COALESCE(a.koefisien3,0) as kf3, 
//                 COALESCE(c.kapasitas_max,0) as km1,COALESCE(c.kapasitas_max1,0) as km2,b.id_rekening
//                 FROM trx_asb_komponen_rinci a
//                 INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
//                 INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
//                 INNER JOIN ref_satuan d ON c.id_satuan_1 = d.id_satuan
//                 LEFT OUTER JOIN ref_satuan e ON c.sat_derivatif_1 = e.id_satuan
//                 LEFT OUTER JOIN ref_satuan f ON c.id_satuan_2 = f.id_satuan
//                 LEFT OUTER JOIN ref_satuan g ON c.sat_derivatif_2 = g.id_satuan) b ON a.id_aktivitas_asb = b.id_aktivitas_asb and a.id_komponen_asb = b.id_komponen_asb and a.id_komponen_asb_rinci = b.id_komponen_asb_rinci, (SELECT @id:=0) z
//                 WHERE a.id_aktivitas_asb='.$req->id_asb.' AND a.id_zona='.$req->id_zona_ssh') a');
    
//     if($getHitung != 0){
//         return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); 
//     } else {
//         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
//     }
// }

public function unloadASB(Request $req){
    $getHitung=DB::DELETE('DELETE FROM trx_renja_rancangan_belanja 
                WHERE id_aktivitas_asb='.$req->id_aktivitas_asb.' AND id_lokasi_renja='.$req->id_lokasi_renja);
    // return json_encode($getHitung);
    if($getHitung != 0){
        return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']); 
    } else {
        return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
    }

}



}