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
use App\Models\TrxRenjaRanwalProgram;
use App\Models\TrxRenjaRanwalProgramIndikator;
use App\Models\TrxRenjaRanwalKegiatan;
use App\Models\TrxRenjaRanwalIndikator;
use App\Models\TrxRenjaRanwalPelaksana;
use App\Models\TrxRenjaRanwalAktivitas;
use Auth;

class TrxRenjaRanwalSesuaiController extends Controller
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
            return view('ranwalrenja.index')->with(compact('unit'));
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
                WHERE a.status_data = 0 and a.tahun_rkpd = '.$tahun.' AND a.id_unit ='.$unit.') a, (SELECT @id:=0) b');
        
        return json_encode($getTransProgram);
    }

    public function addProgramRenja(Request $req)
    {
    $cek=$this->getCekAksesProgram($req->id_rkpd_ranwal,$req->id_unit);
    
    if($cek[0]->hak_akses == 1) {
        try{
            $data = new TrxRenjaRanwalProgram();
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
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    } else {
       return response ()->json (['pesan'=>'Maaf Anda Tidak Berhak Menambah Program, Hubungi Bappeda','status_pesan'=>'0']); 
    }
    
    }

    public function editProgram(Request $req)
    {
        
            $data = TrxRenjaRanwalProgram::find($req->id_renja_program);
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

            $data = TrxRenjaRanwalProgram::find($req->id_renja_program);
            $data->status_data = $req->status_data ;

            if($req->status_data==1){
              if($req->status_pelaksanaan!=2 || $req->status_pelaksanaan!=3){
                if($cek[0]->jml_indikator!=0 && $cek[0]->jml_kegiatan!=0 && $cek[0]->jml_pagu!=0) {
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
                      return response ()->json (['pesan'=>'Maaf Indikator/Kegiatan/Pagu belum ada','status_pesan'=>'0']);  
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
        TrxRenjaRanwalProgram::where('id_renja_program',$req->id_renja_program)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
      }

    public function addIndikatorRenja(Request $req){
      try{
                $data= new TrxRenjaRanwalProgramIndikator();
                $data->tahun_renja=$req->tahun_renja ; 
                $data->no_urut=$req->no_urut; 
                $data->id_renja_program=$req->id_renja_program; 
                $data->id_perubahan=0;
                $data->kd_indikator=$req->kd_indikator; 
                $data->uraian_indikator_program_renja=$req->uraian_indikator; 
                $data->tolok_ukur_indikator=$req->tolok_ukur_indikator;
                $data->id_satuan_output= $req->id_satuan_output; 
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
     
        $data= TrxRenjaRanwalProgramIndikator::find($req->id_indikator_program_renja);
        $data->no_urut=$req->no_urut; 
        $data->id_renja_program=$req->id_renja_program; 
        $data->kd_indikator=$req->kd_indikator; 
        $data->uraian_indikator_program_renja=$req->uraian_indikator; 
        $data->tolok_ukur_indikator=$req->tolok_ukur_indikator;
        $data->target_renja=$req->target_renja;
        $data->id_satuan_output= $req->id_satuan_output; 
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
        $result = TrxRenjaRanwalProgramIndikator::where('id_indikator_program_renja',$req->id_indikator_program_renja)->delete ();
        if($result!=0){
          return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        } else {
          return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
        }
      }

    public function addKegiatanRenja(Request $req)
    {
    $cek=$this->getCekAksesProgram($req->id_rkpd_ranwal,$req->id_unit);
    
    if($cek[0]->hak_akses == 1) {
    try{
        $data = new TrxRenjaRanwalKegiatan();
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
    } else {
       return response ()->json (['pesan'=>'Maaf Anda Tidak Berhak Menambah Kegiatan, Hubungi Bappeda','status_pesan'=>'0']); 
    }
    }

    public function editKegiatanRenja(Request $req)
    {
        $cek=$this->getCekKegiatan($req->tahun_renja,$req->id_renja);
    
        $data = TrxRenjaRanwalKegiatan::find($req->id_renja);
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
    
        $data = TrxRenjaRanwalKegiatan::find($req->id_renja);
        $data->status_data = $req->status_data ;

        if($req->status_data==1){
          if($req->status_pelaksanaan_kegiatan!=2 && $req->status_pelaksanaan_kegiatan!=3){
            // if($req->status_musren==1){
            if($cek[0]->cek_indikator==0 && $cek[0]->cek_pagu==0){                
              try{
                  $data->save (['timestamps' => false]);
                  return response ()->json (['pesan'=>'Data Kegiatan Renja Berhasil di-Posting','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                   $error_code = $e->errorInfo[1] ;
                   return response ()->json (['pesan'=>'Data Kegiatan Renja Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
                }  
              } else {
                if($cek[0]->cek_pagu!=0) {
                    return response ()->json (['pesan'=>'Maaf Jumlah Pagu Kegiatan Tidak Sama Aktivitas dan atau Pelaksana belum direviu','status_pesan'=>'0']);
                } 
                if($cek[0]->cek_indikator!=0) {
                    return response ()->json (['pesan'=>'Maaf Kegiatan Renja Ada Indikator yang Belum direviu','status_pesan'=>'0']);
                }                
              }
            // } else {
            //   try{
            //     $data->save (['timestamps' => false]);
            //     return response ()->json (['pesan'=>'Data Kegiatan Renja Berhasil di-Posting','status_pesan'=>'1']);
            //   }
            //   catch(QueryException $e){
            //      $error_code = $e->errorInfo[1] ;
            //      return response ()->json (['pesan'=>'Data Kegiatan Renja Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
            //   } 
            // }                
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
        TrxRenjaRanwalKegiatan::where('id_renja',$req->id_renja)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

    public function addIndikatorKeg(Request $req){
      try{
                $data= new TrxRenjaRanwalIndikator();
                $data->tahun_renja= $req->tahun_renja;
                $data->no_urut= $req->no_urut;
                $data->id_renja= $req->id_renja;
                $data->id_perubahan= 0;
                $data->kd_indikator= $req->kd_indikator;
                $data->uraian_indikator_kegiatan_renja= $req->uraian_indikator_kegiatan_renja;
                $data->tolok_ukur_indikator= $req->tolok_ukur_indikator;
                $data->angka_tahun= $req->angka_tahun;
                $data->angka_renstra= 0;
                $data->id_satuan_output= $req->id_satuan_output;
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
     
        $data= TrxRenjaRanwalIndikator::find($req->id_indikator_kegiatan_renja);
        $data->no_urut= $req->no_urut;
        $data->id_renja= $req->id_renja;
        $data->kd_indikator= $req->kd_indikator;
        $data->uraian_indikator_kegiatan_renja= $req->uraian_indikator_kegiatan_renja;
        $data->tolok_ukur_indikator= $req->tolok_ukur_indikator;
        $data->angka_tahun= $req->angka_tahun;
        $data->id_satuan_output= $req->id_satuan_output;
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
        // if()
        TrxRenjaRanwalIndikator::where('id_indikator_kegiatan_renja',$req->id_indikator_kegiatan_renja)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }


    public function addAktivitas(Request $req)
    {
        try{
            $data = new TrxRenjaRanwalAktivitas;
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
            $data->pagu_rata2= $req->pagu_rata2;
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
            $data = TrxRenjaRanwalAktivitas::find($req->id_aktivitas_renja);
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
            $data->pagu_rata2= $req->pagu_rata2;
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

    public function hapusAktivitas(Request $req)
    {
      TrxRenjaRanwalAktivitas::where('id_aktivitas_renja',$req->id_aktivitas_renja)->delete ();
      return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
    }

    public function addPelaksana(Request $req)
    {
        try{
            $data = new TrxRenjaRanwalPelaksana;
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

            $data = TrxRenjaRanwalPelaksana::find($req->id_pelaksana_renja);
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
        $data = TrxRenjaRanwalPelaksana::where('id_pelaksana_renja',$req->id_pelaksana_renja)->delete();
        if($data != 0){
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
                    FROM trx_renja_ranwal_program_rkpd x 
                    INNER JOIN trx_rkpd_ranwal a ON a.id_rkpd_ranwal = x.id_rkpd_ranwal and a.tahun_rkpd = x.tahun_renja
                    LEFT OUTER JOIN trx_rpjmd_program d ON a.id_sasaran_rpjmd = d.id_sasaran_rpjmd AND a.id_program_rpjmd = d.id_program_rpjmd) b
                    LEFT OUTER JOIN
                    (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_program_rpjmd, a.id_unit,  
                    COUNT(a.id_renja_program)as jml_program, sum(c.jml_indikator) as jml_indikator, sum(b.jml_kegiatan) as jml_kegiatan, 
                          sum(b.jml_pagu) as jml_pagu
                    FROM trx_renja_ranwal_program a 
                    LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator
                    FROM trx_renja_ranwal_program_indikator a
                    LEFT OUTER JOIN trx_renja_ranwal_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program
                    GROUP BY a.tahun_renja, a.id_renja_program) c 
                    ON a.tahun_renja = c.tahun_renja AND a.id_renja_program = c.id_renja_program
                    LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit, 
                    COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan, COALESCE(SUM(a.pagu_tahun_kegiatan)) AS jml_pagu
                    FROM trx_renja_ranwal_kegiatan a
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
            FROM trx_renja_ranwal_program a
            LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator,
            COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0i
            FROM trx_renja_ranwal_program_indikator a
            LEFT OUTER JOIN trx_renja_ranwal_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program
            GROUP BY a.tahun_renja, a.id_renja_program) e
            ON a.tahun_renja = e.tahun_renja AND a.id_renja_program = e.id_renja_program
            LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit,
            COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan, COALESCE(SUM(a.pagu_tahun_kegiatan)) AS jml_pagu,
            COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0k
            FROM trx_renja_ranwal_kegiatan a
            WHERE a.status_pelaksanaan_kegiatan <> 2 AND a.status_pelaksanaan_kegiatan <> 3 
            GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit) f
            ON a.tahun_renja = f.tahun_renja AND a.id_renja_program = f.id_renja_program
            WHERE a.tahun_renja='.$id_tahun.' AND a.id_renja_program='.$id_renja);

    return $CekProgram;
    }

    public function getCekAksesProgram($id_rkpd_ranwal,$id_unit){
    $CekProgram=DB::select('SELECT b.id_rkpd_ranwal, b.id_unit, b.hak_akses, b.sumber_data, b.status_pelaksanaan
            FROM trx_renja_ranwal_program_rkpd AS a
            INNER JOIN trx_rkpd_ranwal_pelaksana AS b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal AND a.id_unit = b.id_unit
            WHERE b.id_rkpd_ranwal='.$id_rkpd_ranwal.' AND a.id_unit='.$id_unit);
    return $CekProgram;
    }

    // public function getCekAksesProgram($id_rkpd_ranwal,$id_unit){
    // $CekProgram=DB::select('SELECT b.id_rkpd_ranwal, b.id_unit, 1  as hak_akses, b.sumber_data, b.status_pelaksanaan
    //         FROM trx_renja_ranwal_program_rkpd AS a
    //         INNER JOIN trx_rkpd_ranwal_pelaksana AS b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal AND a.id_unit = b.id_unit
    //         WHERE b.id_rkpd_ranwal='.$id_rkpd_ranwal.' AND a.id_unit='.$id_unit);
    // return $CekProgram;
    // }

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
              WHEN 2 THEN "fa fa-thumbs-o-up"
              ELSE "fa fa-exclamation"
          END AS status_icon,
          CASE a.status_data
              WHEN 0 THEN "red"
              WHEN 1 THEN "green"
              WHEN 2 THEN "blue"
              ELSE "red"
          END AS warna   
            FROM trx_renja_ranwal_program a
            INNER JOIN ref_unit b ON a.id_unit = b.id_unit
            LEFT OUTER JOIN trx_renstra_program c ON a.id_program_renstra = c.id_program_renstra
            LEFT OUTER JOIN (select a.id_program, CONCAT(LEFT(CONCAT(0,c.kd_urusan),2),".",RIGHT(CONCAT(b.kd_bidang,0),2),".",a.kd_program) AS kd_program, a.uraian_program  
            FROM ref_program a
            INNER JOIN ref_bidang b ON a.id_bidang = b.id_bidang
            INNER JOIN ref_urusan c ON b.kd_urusan = c.kd_urusan) d ON a.id_program_ref = d.id_program
            LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator,
            COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0i
            FROM trx_renja_ranwal_program_indikator a
            LEFT OUTER JOIN trx_renja_ranwal_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program
            GROUP BY a.tahun_renja, a.id_renja_program) e
            ON a.tahun_renja = e.tahun_renja AND a.id_renja_program = e.id_renja_program
            LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit,
            COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan, COALESCE(SUM(a.pagu_tahun_kegiatan)) AS jml_pagu,
            COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0k
            FROM trx_renja_ranwal_kegiatan a
            GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit) f
            ON a.tahun_renja = f.tahun_renja AND a.id_renja_program = f.id_renja_program
            WHERE a.id_unit ='.$id_unit.' AND a.tahun_renja='.$tahun_renja.' AND a.id_rkpd_ranwal='.$id_ranwal.') a,(SELECT @id:=0) z');

      return DataTables::of($getProgramRenja)
      ->addColumn('details_url', function($getProgramRenja) {
                    return url('ranwalrenja/sesuai/getIndikatorRenja/'.$getProgramRenja->id_renja_program);
                })
        ->addColumn('action', function ($getProgramRenja) {
          if ($getProgramRenja->status_data==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="add-indikator dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Program</a>
                        </li>
                        <li>
                            <a class="view-kegiatan dropdown-item"><i class="fa fa-building-o fa-fw fa-lg"></i> Lihat Kegiatan Renja</a>
                        </li>
                        <li>
                            <a class="edit-program dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Program Renja</a>
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
                a.uraian_indikator_program_renja,a.tolok_ukur_indikator,a.target_renstra,a.target_renja,a.id_satuan_output,
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
                FROM trx_renja_ranwal_program_indikator a
                INNER JOIN trx_renja_ranwal_program b ON a.tahun_renja=b.tahun_renja AND a.id_renja_program=b.id_renja_program 
                ,(SELECT @id:=0) x where a.id_renja_program='.$id_rkpd);

      return DataTables::of($indikatorProg)
        ->addColumn('action', function ($indikatorProg) {
          if($indikatorProg->status_program_renja==0 ){
            if($indikatorProg->status_pelaksanaan_renja!=2 && $indikatorProg->status_pelaksanaan_renja!=3){
              return '
                <button class="edit-indikator btn btn-warning" data-toggle="tooltip" title="Edit Indikator Program Renja"><i class="fa fa-pencil fa-fw fa-lg"></i></button>
                <button class="reviu-indikator btn btn-primary" data-toggle="tooltip" title="Reviu Indikator Program Renja"><i class="fa fa-check-square-o fa-fw fa-lg"></i></button>
                ';}
            }
          })
        ->make(true); 
    }

    public function getCekKegiatan($id_tahun,$id_renja){
      $CekKegiatan=DB::select('SELECT a.tahun_renja, a.id_renja, b.cek_indikator, a.pagu_tahun_kegiatan-COALESCE(g.jml_pagu,0) as cek_pagu
              FROM trx_renja_ranwal_kegiatan a
              LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_renja, 
              COALESCE(COUNT(a.id_indikator_kegiatan_renja),0) - COALESCE(COUNT(CASE WHEN a.status_data=1 THEN status_data END),0) as cek_indikator
              FROM trx_renja_ranwal_kegiatan_indikator a
              GROUP BY a.tahun_renja, a.id_renja) b ON a.tahun_renja = b.tahun_renja AND a.id_renja = b.id_renja
              LEFT OUTER JOIN (SELECT a.tahun_renja,a.id_renja,COALESCE(COUNT(a.id_pelaksana_renja),0) as jml_pelaksana,COALESCE(SUM(b.jml_aktivitas),0) as jml_aktivitas,
              COALESCE(SUM(b.jml_pagu),0) as jml_pagu
              FROM trx_renja_ranwal_pelaksana a
              LEFT OUTER JOIN (SELECT COUNT(a.id_aktivitas_renja) as jml_aktivitas,
              SUM(a.pagu_aktivitas) as jml_pagu, a.tahun_renja, a.id_renja
              FROM trx_renja_ranwal_aktivitas AS a
              GROUP BY a.tahun_renja, a.id_renja) b ON a.tahun_renja = b.tahun_renja AND a.id_pelaksana_renja = b.id_renja
              GROUP BY a.tahun_renja,a.id_renja, a.status_data) g ON a.tahun_renja = g.tahun_renja AND a.id_renja = g.id_renja
              WHERE a.tahun_renja='.$id_tahun.' AND a.id_renja='.$id_renja);
      return $CekKegiatan;
    } 

    public function getKegiatanRenja($id_program)
    {
      $getKegiatanRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_renja, a.no_urut, a.id_renja, a.id_renja_program, a.id_rkpd_renstra, a.id_rkpd_ranwal, 
      a.id_unit, a.id_visi_renstra, a.id_misi_renstra, a.id_tujuan_renstra, a.id_sasaran_renstra,a.id_program_renstra, a.uraian_program_renstra, 
      a.id_kegiatan_renstra, a.id_kegiatan_ref, a.uraian_kegiatan_renstra as uraian_kegiatan_renja, a.pagu_tahun_renstra, a.pagu_tahun_kegiatan, 
      a.pagu_tahun_selanjutnya, a.status_pelaksanaan_kegiatan, a.sumber_data, a.ket_usulan, a.status_data, COALESCE(b.jml_indikator,0) as jml_indikator, 
      COALESCE(b.jml_0i,0) as jml_0i,c.uraian_kegiatan_renstra,d.id_program_ref,g.jml_pelaksana, g.jml_pagu, g.jml_aktivitas,
            CASE a.status_data
                          WHEN 0 THEN "fa fa-question"
                          WHEN 1 THEN "fa fa-check-square-o"
                      END AS status_icon,
                      CASE a.status_data
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna, e.kd_kegiatan, e.nm_kegiatan,f.nm_unit, d.status_data as status_program 
            FROM trx_renja_ranwal_kegiatan a
            LEFT OUTER JOIN (SELECT tahun_renja, id_renja, count(id_indikator_kegiatan_renja) as jml_indikator ,
            COUNT(CASE WHEN status_data=1 THEN status_data END) as jml_0i
            FROM trx_renja_ranwal_kegiatan_indikator
            GROUP BY tahun_renja, id_renja) b ON a.tahun_renja=b.tahun_renja AND a.id_renja = b.id_renja
            LEFT OUTER JOIN trx_renstra_kegiatan c ON a.id_program_renstra = c.id_program_renstra AND a.id_kegiatan_renstra = c.id_kegiatan_renstra
            INNER JOIN trx_renja_ranwal_program d ON a.id_renja_program = d.id_renja_program
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
            FROM trx_renja_ranwal_pelaksana a
            LEFT OUTER JOIN (SELECT COUNT(a.id_aktivitas_renja) as jml_aktivitas,
            SUM(a.pagu_aktivitas) as jml_pagu, a.tahun_renja, a.id_renja
            FROM trx_renja_ranwal_aktivitas AS a
            GROUP BY a.tahun_renja, a.id_renja) b ON a.tahun_renja = b.tahun_renja AND a.id_pelaksana_renja = b.id_renja
            INNER JOIN ref_sub_unit d ON a.id_sub_unit = d.id_sub_unit
            LEFT OUTER JOIN ref_lokasi e ON a.id_lokasi = e.id_lokasi
            GROUP BY a.tahun_renja,a.id_renja) g ON a.tahun_renja = g.tahun_renja AND a.id_renja = g.id_renja 
            WHERE a.id_renja_program='.$id_program.') a,(SELECT @id:=0) z');

      return DataTables::of($getKegiatanRenja)
      ->addColumn('details_url', function($getKegiatanRenja) {
                    return url('ranwalrenja/sesuai/getIndikatorKegiatan/'.$getKegiatanRenja->id_renja);
                })
        ->addColumn('action', function ($getKegiatanRenja) {
          if($getKegiatanRenja->status_program !=2){
            if ($getKegiatanRenja->status_data==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="add-indikatorKeg dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Kegiatan</a>
                        </li>
                        <li>
                            <a class="view-pelaksana dropdown-item">
                                <i class="fa fa-male fa-fw fa-lg"></i> Lihat Pelaksana</a>
                        </li>
                        <li>
                            <a class="edit-kegiatan dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Kegiatan Renja</a>
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
      $indikatorKeg=DB::select('SELECT (@id:=@id+1) as urut,a.tahun_renja, a.no_urut, a.id_renja, a.id_indikator_kegiatan_renja, a.id_perubahan, a.kd_indikator, 
            a.uraian_indikator_kegiatan_renja, a.tolok_ukur_indikator, a.angka_tahun, a.angka_renstra, a.status_data, a.sumber_data,a.id_satuan_output, 
            CASE a.status_data
                WHEN 0 THEN "fa fa-question"
                WHEN 1 THEN "fa fa-check-square-o"
            END AS status_icon,
            CASE a.status_data
                WHEN 0 THEN "red"
                WHEN 1 THEN "green"
            END AS warna,b.status_pelaksanaan_kegiatan, b.status_data as status_kegiatan
            FROM trx_renja_ranwal_kegiatan_indikator a
            INNER JOIN trx_renja_ranwal_kegiatan b ON a.id_renja = b.id_renja,(SELECT @id:=0) x where a.id_renja='.$id_renja);

      return DataTables::of($indikatorKeg)
        ->addColumn('action', function ($indikatorKeg) {
          if($indikatorKeg->status_kegiatan==0 ){
          if($indikatorKeg->status_pelaksanaan_kegiatan!=2 && $indikatorKeg->status_pelaksanaan_kegiatan!=3){
            return '<a class="edit-indikatorKeg btn btn-warning btn-labeled">
                    <span class="btn-label"><i class="fa fa-pencil fa-fw fa-lg"></i></span>Edit Indikator</a>';
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
                a.sumber_dana, a.pagu_aktivitas, a.pagu_musren, f.status_data, a.status_musren, 
                (a.pagu_aktivitas*(a.pagu_musren/100)) as jml_musren_aktivitas,
                COALESCE(a.volume_1,0) as volume_1, COALESCE(a.volume_2,0) as volume_2, a.id_satuan_1, a.id_satuan_2,
                c.uraian_satuan as uraian_satuan_1, d.uraian_satuan as uraian_satuan_2, 
                CASE f.status_data
                          WHEN 0 THEN "fa fa-question fa-fw fa-lg"
                          WHEN 1 THEN "fa fa-check-square-o fa-fw fa-lg"
                      END AS status_icon,
                CASE f.status_data
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna,
                CASE a.sumber_aktivitas
                          WHEN 0 THEN "fa fa-registered"
                          WHEN 1 THEN ""
                      END AS img 
                FROM trx_renja_ranwal_aktivitas a 
                LEFT OUTER JOIN trx_asb_aktivitas b ON a.id_aktivitas_asb = b.id_aktivitas_asb
                LEFT OUTER JOIN ref_satuan c ON a.id_satuan_1 = c.id_satuan
                LEFT OUTER JOIN ref_satuan d ON a.id_satuan_2 = d.id_satuan
                INNER JOIN trx_renja_ranwal_pelaksana e ON a.id_renja = e.id_pelaksana_renja
                INNER JOIN trx_renja_ranwal_kegiatan f ON e.id_renja = f.id_renja,
                (SELECT @id:=0) x WHERE a.id_renja='.$id_pelaksana);

   return DataTables::of($getAktivitas)
   ->addColumn('action', function ($getAktivitas) {
            return '
                <a type="button" class="edit-aktivitas btn btn-warning btn-labeled">
                <span class="btn-label"><i class="fa fa-pencil fa-fw fa-lg"></i></span>Edit Aktivitas</a>                         
            ';
        })
   ->make(true);
}

public function getCekPelaksana($id_tahun,$id_pelaksana){
      $CekPelaksana=DB::select('SELECT a.tahun_renja,a.id_pelaksana_renja,COALESCE(COUNT(a.id_pelaksana_renja),0) as jml_pelaksana,
            COALESCE(SUM(b.jml_aktivitas),0) as jml_aktivitas, COALESCE(SUM(b.jml_pagu),0) as jml_pagu
            FROM trx_renja_ranwal_pelaksana a
            LEFT OUTER JOIN (SELECT COUNT(a.id_aktivitas_renja) as jml_aktivitas,
            SUM(a.pagu_aktivitas) as jml_pagu, a.tahun_renja, a.id_renja
            FROM trx_renja_ranwal_aktivitas AS a
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
            FROM trx_renja_ranwal_pelaksana a
            LEFT OUTER JOIN (SELECT COUNT(a.id_aktivitas_renja) as jml_aktivitas,
            SUM(a.pagu_aktivitas) as jml_pagu, a.tahun_renja, a.id_renja
            FROM trx_renja_ranwal_aktivitas AS a
            GROUP BY a.tahun_renja, a.id_renja) b ON a.tahun_renja = b.tahun_renja AND a.id_pelaksana_renja = b.id_renja
            INNER JOIN ref_sub_unit d ON a.id_sub_unit = d.id_sub_unit
            LEFT OUTER JOIN ref_lokasi e ON a.id_lokasi = e.id_lokasi
            INNER JOIN trx_renja_ranwal_kegiatan f ON a.id_renja = f.id_renja
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
                                <i class="fa fa-pencil fa-fw fa-lg"></i> Edit Pelaksana Kegiatan</a>
                        </li>                       
                    </ul>
                </div>
            ';
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
          sifat_indikator, nm_indikator, flag_iku, asal_indikator, sumber_data_indikator,id_satuan_output,
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
        FROM trx_renja_ranwal_kegiatan_indikator
        WHERE id_renja = '.$id_renja.'
        GROUP BY tahun_renja, id_renja');

   return json_encode($getCheckKegiatan);
}

public function getCheckProgram($id_program)
{
   $getCheckProgram=DB::SELECT('SELECT a.tahun_renja, a.id_renja_program, 
        (COALESCE(e.jml_indikator,0) - COALESCE(e.jml_0i,0)) as jml_0i,
        (COALESCE(f.jml_kegiatan,0) - COALESCE(f.jml_0k,0)) as jml_0k
        FROM trx_renja_ranwal_program a
        INNER JOIN (SELECT a.tahun_renja, a.id_renja_program, COALESCE(COUNT(a.id_indikator_program_renja),0) AS jml_indikator,
        COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0i
        FROM trx_renja_ranwal_program_indikator a
        GROUP BY a.tahun_renja, a.id_renja_program) e
        ON a.tahun_renja = e.tahun_renja AND a.id_renja_program = e.id_renja_program
        INNER JOIN (SELECT a.tahun_renja, a.id_renja_program,
        COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan,
        COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0k
        FROM trx_renja_ranwal_kegiatan a
        GROUP BY a.tahun_renja, a.id_renja_program) f
        ON a.tahun_renja = f.tahun_renja AND a.id_renja_program = f.id_renja_program
        WHERE a.id_renja_program = '.$id_program);

   return json_encode($getCheckProgram);
}

public function CheckProgram($id_program)
{
   $CheckProgram=DB::SELECT('SELECT * FROM trx_renja_ranwal_program
        WHERE id_renja_program = '.$id_program.' LIMIT 1');

   return ($CheckProgram);
}

public function CheckKegiatan($id_renja)
{
   $CheckKegiatan=DB::SELECT('SELECT * FROM trx_renja_ranwal_kegiatan
        WHERE id_renja = '.$id_renja.' LIMIT 1');

   return ($CheckKegiatan);
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
        WHERE a.flag=1 AND a.tahun_berlaku='.Session::get('tahun').' AND b.id_zona=1) b 
        ON a.id_tarif_ssh = b.id_tarif_ssh) f ON e.id_komponen_asb = f.id_komponen_asb
        WHERE d.id_aktivitas_asb='.$req->id_asb.' GROUP BY d.id_aktivitas_asb');
   
   return json_encode($getHitungPaguASB);
}

}