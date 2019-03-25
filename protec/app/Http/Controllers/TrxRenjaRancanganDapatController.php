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

class TrxRenjaRancanganDapatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request, Builder $htmlBuilder, $id = null)
    {
        $unit = \App\RefSubUnit::select();
        if(isset(Auth::user()->getUserSubUnit)){
            foreach(Auth::user()->getUserSubUnit as $data){
                $unit->orWhere(['id_unit' => $data->kd_unit, 'kd_sub' => $data->kd_sub]);                
            }
        }
        $unit = $unit->get();

        return view('renja.blangsung')->with(compact('unit'));
    }

    public function getUnit(Request $request){
        $unit = \App\RefUnit::select();
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


    public function editKegiatanRenja(Request $req)
    {
        $pagu=$this->getPaguAktivitas($req->tahun_renja,$req->id_renja);

        
            $data = TrxRenjaRancangan::find($req->id_renja);
            $data->pagu_musrenbang = $req->pagu_musrenbang ;
            $data->status_rancangan = $req->status_rancangan ;

            if($req->status_rancangan==1){
                if($req->pagu_kegiatan==$pagu[0]->jml_pagu && $req->pagu_musren==$pagu[0]->jml_musren){
                    try{
                        $data->save (['timestamps' => false]);
                        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                      }
                    catch(QueryException $e){
                         $error_code = $e->errorInfo[1] ;
                         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                      }
                  } else {
                      return response ()->json (['pesan'=>'Maaf Jumlah Pagu Aktivitas/Musrenbang tidak sama dengan Pagu Kegiatan','status_pesan'=>'0']);                     
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
        $pagu=$this->getPaguPelaksana($req->tahun_renja,$req->id_aktivitas_renja);

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

            if($req->status_data==1 && $req->status_musren==0){
               if($req->pagu_aktivitas==$pagu[0]->jml_pagu){
                 try{ 
                    $data->save (['timestamps' => false]);
                    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); }
                catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                } 
               } else {
                 return response ()->json (['pesan'=>'Maaf Jumlah Pagu Aktivitas tidak sama dengan Pagu Pelaksana','status_pesan'=>'0']);   
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
        try{
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
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }
    public function hapusPelaksana(Request $req)
      {
        TrxRenjaRancanganPelaksana::where('id_pelaksana_renja',$req->id_pelaksana_renja)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
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
        TrxRenjaRancanganBelanja::where('id_belanja_renja',$req->id_belanja_renja)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

    public function getProgramRenja($tahun_renja,$id_unit)
    {
      $getProgramRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_renja, a.no_urut, a.id_renja_program, 
            a.id_rkpd_ranwal, a.id_program_rpjmd, a.id_unit, a.id_visi_renstra, a.id_misi_renstra, a.id_tujuan_renstra, 
            a.id_sasaran_renstra,a.id_program_renstra, a.uraian_program_renstra as uraian_program_renja, a.pagu_tahun_ranwal, 
            a.pagu_tahun_renstra, a.status_program_rkpd, a.sumber_data_rkpd, a.sumber_data, a.ket_usulan, a.status_data, b.nm_unit, 
            a.id_program_ref,a.status_pelaksanaan,
            COALESCE(f.jml_kegiatan,0) as jml_kegiatan,
            COALESCE(f.jml_pagu,0) as jml_pagu,
            COALESCE(f.jml_0k,0) as jml_0k,
            COALESCE(f.jml_musren,0) as jml_musren,
            COALESCE(f.jml_aktivitas,0) as jml_aktivitas,
            COALESCE(f.jml_pagu_aktivitas,0) as jml_pagu_aktivitas,
            COALESCE(f.jml_musren_aktivitas,0) as jml_musren_aktivitas
            FROM trx_renja_rancangan_program a
            INNER JOIN ref_unit b ON a.id_unit = b.id_unit            
            LEFT OUTER JOIN (SELECT a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit,
            COALESCE(COUNT(a.id_renja),0) AS jml_kegiatan, 
            COALESCE(SUM(a.pagu_tahun_kegiatan)) AS jml_pagu,
            COUNT(CASE WHEN a.status_data = 1 THEN a.status_data END) as jml_0k,
            COALESCE(SUM(a.pagu_tahun_kegiatan*(a.pagu_musrenbang/100))) AS jml_musren,
            COALESCE(sum(b.jml_aktivitas),0) as jml_aktivitas,
            COALESCE(sum(b.jml_pagu_aktivitas),0) as jml_pagu_aktivitas,
            COALESCE(sum(b.jml_musren_aktivitas),0) as jml_musren_aktivitas
            FROM trx_renja_rancangan a
            LEFT OUTER JOIN (SELECT tahun_renja, id_renja, COUNT(id_aktivitas_renja) as jml_aktivitas, SUM(pagu_aktivitas) as jml_pagu_aktivitas, SUM(pagu_aktivitas*pagu_musren) as jml_musren_aktivitas
            FROM trx_renja_rancangan_aktivitas
            GROUP BY tahun_renja, id_renja) b ON a.tahun_renja=a.tahun_renja AND a.id_renja = b.id_renja
            GROUP BY a.tahun_renja, a.id_rkpd_ranwal, a.id_renja_program, a.id_unit) f
            ON a.tahun_renja = f.tahun_renja AND a.id_renja_program = f.id_renja_program
            WHERE a.status_data=1 and a.id_unit ='.$id_unit.' AND a.tahun_renja='.$tahun_renja.') a,(SELECT @id:=0) z');

      return DataTables::of($getProgramRenja)
        ->addColumn('action', function ($getProgramRenja) {
            return '
            <button id="btnViewKegiatan" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="Pilih Unit" title="Pilih Unit" class="btn btn-primary btn-sm"><i class="fa fa-check-square-o fa-fw"></i> Lihat Kegiatan Renja</button>';
        })
        ->make(true);
    }

    
    public function getKegiatanRenja($id_program)
    {
      $getKegiatanRenja=DB::select('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_renja, a.no_urut, a.id_renja, a.id_renja_program, 
            a.id_rkpd_renstra, a.id_rkpd_ranwal, a.id_unit, a.id_visi_renstra, a.id_misi_renstra, a.id_tujuan_renstra, 
            a.id_sasaran_renstra,a.id_program_renstra, a.uraian_program_renstra, a.id_kegiatan_renstra,
            a.id_kegiatan_ref, a.uraian_kegiatan_renstra as uraian_kegiatan_renja, a.pagu_tahun_renstra, 
            a.pagu_tahun_kegiatan, a.pagu_tahun_selanjutnya, a.status_pelaksanaan_kegiatan, a.sumber_data, 
            a.ket_usulan, a.status_data, c.uraian_kegiatan_renstra,d.id_program_ref,a.status_rancangan, 
            e.kd_kegiatan, e.nm_kegiatan,f.nm_unit,a.pagu_musrenbang as persen_musren,(a.pagu_tahun_kegiatan*(a.pagu_musrenbang/100)) as pagu_musrenbang,
            COALESCE(b.jml_aktivitas,0) as jml_aktivitas, 
            COALESCE(b.jml_pagu_aktivitas,0) as jml_pagu_aktivitas,
            COALESCE(b.jml_musren_aktivitas,0) as jml_musren_aktivitas,
            CASE a.status_rancangan
                          WHEN 0 THEN "fa fa-question"
                          WHEN 1 THEN "fa fa-check-square-o"
                      END AS status_icon,
                      CASE a.status_rancangan
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna 
            FROM trx_renja_rancangan a
            LEFT OUTER JOIN (SELECT tahun_renja, id_renja, COUNT(id_aktivitas_renja) as jml_aktivitas, SUM(pagu_aktivitas) as jml_pagu_aktivitas, SUM(pagu_aktivitas*(pagu_musren/100)) as jml_musren_aktivitas
            FROM trx_renja_rancangan_aktivitas
            WHERE trx_renja_rancangan_aktivitas.status_data=1
            GROUP BY tahun_renja, id_renja,status_data) b ON a.tahun_renja=b.tahun_renja AND a.id_renja = b.id_renja
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
        ->addColumn('action', function ($getKegiatanRenja) {
          // if ($getKegiatanRenja->status_rancangan==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="glyphicon glyphicon-wrench"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="view-aktivitas btn btn-xs">
                                <i class="glyphicon glyphicon-tower"></i> Lihat Aktivitas</a>
                        </li>
                        <li>
                            <a class="edit-kegiatan btn btn-xs">
                                <i class="glyphicon glyphicon-pencil"></i> Ubah Kegiatan Renja</a>
                        </li>                          
                    </ul>
                </div>
            ';
          // if ($getKegiatanRenja->status_rancangan==1)
          //   return '<a class="view-aktivitas btn btn-xs" data-id_rkpd_ranwal="'.$getKegiatanRenja->id_rkpd_ranwal.'" data-id_renja_program="'.$getKegiatanRenja->id_renja_program.'" data-uraian_kegiatan_renja="'.$getKegiatanRenja->uraian_kegiatan_renja.'" data-uraian_kegiatan_renstra="'.$getKegiatanRenja->uraian_kegiatan_renstra.'" data-tahun_renja="'.$getKegiatanRenja->tahun_renja.'" data-status_data="'.$getKegiatanRenja->status_data.'" data-sumber_data="'.$getKegiatanRenja->sumber_data.'" data-status_pelaksanaan="'.$getKegiatanRenja->status_pelaksanaan_kegiatan.'" data-id_renja="'.$getKegiatanRenja->id_renja.'"><i class="glyphicon glyphicon-tower"></i> Lihat Indikator Kegiatan</a>
          //   ';
        })
        ->make(true);
    } 

public function getAktivitas($id_renja)
{
   $getAktivitas=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.tahun_renja,a. no_urut, a.id_aktivitas_renja, a.id_renja, 
                a.sumber_aktivitas, a.id_aktivitas_asb,a.uraian_aktivitas_kegiatan, a.tolak_ukur_aktivitas, 
                a.target_output_aktivitas,a.id_satuan_publik, a.id_program_nasional, a.id_program_provinsi, a.jenis_kegiatan, 
                a.sumber_dana, a.pagu_aktivitas, a.pagu_musren, a.status_data, a.status_musren, 
                (a.pagu_aktivitas*(a.pagu_musren/100)) as jml_musren_aktivitas, COALESCE(b.jml_pagu,0) as jml_pagu_pelaksana,
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
                      END AS img 
                FROM trx_renja_rancangan_aktivitas a
                LEFT OUTER JOIN (SELECT a.tahun_renja,a.id_aktivitas_renja, COALESCE(SUM(c.jml_belanja),0) as jml_pagu
                FROM trx_renja_rancangan_pelaksana a 
                LEFT OUTER JOIN trx_renja_rancangan_lokasi b ON a.id_pelaksana_renja = b.id_pelaksana_renja
                LEFT OUTER JOIN trx_renja_rancangan_belanja c ON b.id_lokasi_renja = c.id_lokasi_renja
                WHERE a.status_data = 2
                GROUP BY a.tahun_renja,a.id_aktivitas_renja) b ON a.id_aktivitas_renja = b.id_aktivitas_renja ,
                (SELECT @id:=0) x WHERE id_renja='.$id_renja);

   return DataTables::of($getAktivitas)
   ->addColumn('action', function ($getAktivitas) {
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="glyphicon glyphicon-wrench"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="view-pelaksana btn btn-xs">
                                <i class="glyphicon glyphicon-tower"></i> Lihat Pelaksana</a>
                        </li>
                        <li>
                            <a class="edit-aktivitas btn btn-xs">
                                <i class="glyphicon glyphicon-pencil"></i> Ubah Aktivitas Renja</a>
                        </li>                          
                    </ul>
                </div>
            ';
        })
   ->make(true);
}

public function getPelaksanaAktivitas($id_aktivitas){
   $getPelaksana=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_renja,a.no_urut,a.id_pelaksana_renja,a.id_renja,a.id_aktivitas_renja,a.id_lokasi, e.nama_lokasi,
            a.id_sub_unit,a.status_data,a.status_pelaksanaan,a.ket_usul,a.sumber_data,d.nm_sub,
            COALESCE(b.jml_lokasi,0) as jml_lokasi, COALESCE(SUM(c.jml_belanja),0) as jml_pagu,
            CASE a.status_data
                WHEN 0 THEN "fa fa-question"
                WHEN 1 THEN "fa fa-check-square-o"
            END AS status_icon,
            CASE a.status_data
                WHEN 0 THEN "red"
                WHEN 1 THEN "green"
            END AS warna 
            FROM trx_renja_rancangan_pelaksana a 
            LEFT OUTER JOIN (select id_pelaksana_renja, id_lokasi_renja,COUNT(DISTINCT (id_lokasi_renja),0) as jml_lokasi 
                        FROM trx_renja_rancangan_lokasi GROUP BY id_pelaksana_renja, id_lokasi_renja) b ON a.id_pelaksana_renja = b.id_pelaksana_renja
            LEFT OUTER JOIN trx_renja_rancangan_belanja c ON b.id_lokasi_renja = c.id_lokasi_renja
            INNER JOIN ref_sub_unit d ON a.id_sub_unit = d.id_sub_unit
            LEFT OUTER JOIN ref_lokasi e ON a.id_lokasi = e.id_lokasi
            GROUP BY a.tahun_renja,a.no_urut,a.id_pelaksana_renja,a.id_renja,a.id_aktivitas_renja,
            a.id_sub_unit,a.status_data,a.status_pelaksanaan,a.ket_usul,a.sumber_data,d.nm_sub,
                    a.id_lokasi, e.nama_lokasi,b.jml_lokasi) a,
            (SELECT @id:=0) x WHERE a.id_aktivitas_renja='.$id_aktivitas);

   return DataTables::of($getPelaksana)
   ->addColumn('action', function ($getPelaksana) {
        return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="glyphicon glyphicon-wrench"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="view-lokasi btn btn-xs">
                                <i class="glyphicon glyphicon-tower"></i> Lihat Lokasi Aktivitas</a>
                        </li>
                        <li>
                            <a class="edit-pelaksana btn btn-xs">
                                <i class="glyphicon glyphicon-pencil"></i> Edit Pelaksana Aktivitas</a>
                        </li>                          
                    </ul>
                </div>
            ';
   })
   ->make(true);
}

public function getLokasiAktivitas($id_pelaksana)
{
   $LokAktiv=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_renja,a.no_urut,a.id_pelaksana_renja,
        a.id_lokasi_renja,a.jenis_lokasi,a.id_lokasi,b.nama_lokasi,a.uraian_lokasi,COALESCE(SUM(c.jml_belanja),0) as jml_pagu, e.sumber_aktivitas,e.id_aktivitas_asb,e.uraian_aktivitas_kegiatan, a.volume_1, a.volume_2, a.id_satuan_1, a.id_satuan_2 
        FROM trx_renja_rancangan_lokasi a
        INNER JOIN ref_lokasi b on a.id_lokasi = b.id_lokasi
        LEFT OUTER JOIN trx_renja_rancangan_belanja c ON a.id_lokasi_renja = c.id_lokasi_renja
        INNER JOIN trx_renja_rancangan_pelaksana d ON a.id_pelaksana_renja=d.id_pelaksana_renja
        INNER JOIN trx_renja_rancangan_aktivitas e ON d.id_aktivitas_renja = e.id_aktivitas_renja
        GROUP BY a.tahun_renja,a.no_urut,a.id_pelaksana_renja,a.id_lokasi_renja,a.jenis_lokasi,
        a.id_lokasi,b.nama_lokasi,a.uraian_lokasi, e.sumber_aktivitas,e.id_aktivitas_asb,e.uraian_aktivitas_kegiatan,a.volume_1, a.volume_2, a.id_satuan_1, a.id_satuan_2 ) a,(SELECT @id:=0) x WHERE a.id_pelaksana_renja='.$id_pelaksana);

   return DataTables::of($LokAktiv)
   ->addColumn('action', function ($LokAktiv) {
        return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="glyphicon glyphicon-wrench"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="view-belanja btn btn-xs">
                                <i class="glyphicon glyphicon-tower"></i> Lihat Rincian Belanja</a>
                        </li>
                        <li>
                            <a class="edit-lokasi btn btn-xs">
                                <i class="glyphicon glyphicon-pencil"></i> Edit Lokasi Aktivitas</a>
                        </li>                          
                    </ul>
                </div>
        ';
   })
   ->make(true);
}

public function getBelanja($id_lokasi){
   $getBelanja=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.* FROM (SELECT a.tahun_renja,a.no_urut,a.id_belanja_renja,a.id_lokasi_renja,
            a.id_zona_ssh,a.sumber_aktivitas,a.id_aktivitas_asb,a.id_tarif_ssh,a.id_rekening_ssh,
            a.uraian_belanja,a.volume_1,a.id_satuan_1,a.volume_2,a.id_satuan_2,a.harga_satuan,
            a.jml_belanja,a.status_data,b.uraian_tarif_ssh,c.uraian_satuan as satuan_1, d.uraian_satuan as satuan_2,
            e.kd_rekening, e.nm_rekening, f.nm_aktivitas_asb,
                        CASE a.status_data
                            WHEN 0 THEN "fa fa-question"
                            WHEN 1 THEN "fa fa-check-square-o"
                        END AS status_icon,
                        CASE a.status_data
                            WHEN 0 THEN "red"
                            WHEN 1 THEN "green"
                        END AS warna
            FROM trx_renja_rancangan_belanja a
            INNER JOIN ref_ssh_tarif b on a.id_tarif_ssh = b.id_tarif_ssh
            LEFT OUTER JOIN ref_satuan c on a.id_satuan_1 = c.id_satuan
            LEFT OUTER JOIN ref_satuan d on a.id_satuan_2 = d.id_satuan
            INNER JOIN (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
                            a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
                            FROM ref_rek_5 a) e on a.id_rekening_ssh = e.id_rekening
            LEFT OUTER JOIN trx_asb_aktivitas f on a.id_aktivitas_asb = f.id_aktivitas_asb) a,
            (SELECT @id:=0) x WHERE a.id_lokasi_renja='.$id_lokasi);

   return DataTables::of($getBelanja)
   ->addColumn('action', function ($getBelanja) {
        return '
            <a type="button" class="edit-belanja btn btn-warning btn-sm btn-labeled" 
            data-tahun_renja="'.$getBelanja->tahun_renja.'"
            data-no_urut="'.$getBelanja->no_urut.'"
            data-id_belanja_renja="'.$getBelanja->id_belanja_renja.'"
            data-id_lokasi_renja="'.$getBelanja->id_lokasi_renja.'"
            data-id_zona_ssh="'.$getBelanja->id_zona_ssh.'"
            data-sumber_aktivitas="'.$getBelanja->sumber_aktivitas.'"
            data-id_aktivitas_asb="'.$getBelanja->id_aktivitas_asb.'"
            data-id_tarif_ssh="'.$getBelanja->id_tarif_ssh.'"
            data-id_rekening_ssh="'.$getBelanja->id_rekening_ssh.'"
            data-uraian_belanja="'.$getBelanja->uraian_belanja.'"
            data-volume_1="'.$getBelanja->volume_1.'"
            data-id_satuan_1="'.$getBelanja->id_satuan_1.'"
            data-volume_2="'.$getBelanja->volume_2.'"
            data-id_satuan_2="'.$getBelanja->id_satuan_2.'"
            data-harga_satuan="'.$getBelanja->harga_satuan.'"
            data-jml_belanja="'.$getBelanja->jml_belanja.'"
            data-status_data="'.$getBelanja->status_data.'"
            data-uraian_tarif_ssh="'.$getBelanja->uraian_tarif_ssh.'"
            data-satuan_1="'.$getBelanja->satuan_1.'"
            data-satuan_2="'.$getBelanja->satuan_2.'"
            data-kd_rekening="'.$getBelanja->kd_rekening .'"
            data-nm_rekening="'.$getBelanja->nm_rekening .'"
            data-nm_aktivitas_asb="'.$getBelanja->nm_aktivitas_asb.'">
            <span class="btn-label"><i class="glyphicon glyphicon-pencil"></i></span>Edit Belanja</a>                         
            ';
   })
   ->make(true);
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

public function getSumberDana()
{
   $getSB=DB::SELECT('SELECT id_sumber_dana, uraian_sumber_dana FROM ref_sumber_dana');

   return json_encode($getSB);
}

public function getKecamatan()
{
   $getSB=DB::SELECT('SELECT id_pemda, kd_kecamatan, id_kecamatan, nama_kecamatan
            FROM ref_kecamatan');

   return json_encode($getSB);
}

public function getLokasiLuarDaerah()
{
   $getASB=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.id_lokasi, a.jenis_lokasi, a.nama_lokasi,  
            a.keterangan_lokasi FROM ref_lokasi a  
            WHERE a.jenis_lokasi = 99) a, (SELECT @id:=0) x ');

   return DataTables::of($getASB)
   ->make(true);
}

public function getLokasiTeknis()
{
   $getASB=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.id_lokasi, a.jenis_lokasi, a.nama_lokasi,  
            a.keterangan_lokasi FROM ref_lokasi a  
            WHERE a.jenis_lokasi <> 0 AND a.jenis_lokasi <> 99) a, (SELECT @id:=0) x ');

   return DataTables::of($getASB)
   ->make(true);
}

public function getLokasiDesa($kecamatan)
{
   $getASB=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.id_lokasi, a.jenis_lokasi, a.nama_lokasi, a.id_desa, 
            a.keterangan_lokasi, b.id_kecamatan, b.nama_desa, b.kd_desa
            FROM ref_lokasi a INNER JOIN ref_desa b on a.id_desa=b.id_desa 
            WHERE b.id_kecamatan = '.$kecamatan.') a, (SELECT @id:=0) x ');

   return DataTables::of($getASB)
   ->make(true);
}

public function getAktivitasASB($tahun)
{
   $getASB=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.tahun_perhitungan, b.id_aktivitas_asb, c.nm_aktivitas_asb
          FROM trx_asb_perhitungan_rinci b
          INNER JOIN trx_asb_perhitungan a ON b.id_perhitungan = a.id_perhitungan
          INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb 
          WHERE a.tahun_perhitungan = '.$tahun.' 
          GROUP BY a.tahun_perhitungan, b.id_aktivitas_asb, c.nm_aktivitas_asb) a, (SELECT @id:=0) x ');

   return DataTables::of($getASB)
   ->make(true);
}

public function getSubUnit($id_unit)
{
   $SBRef=DB::SELECT('SELECT (@id:=@id+1) as no_urut,id_sub_unit, id_unit, kd_sub, nm_sub
        FROM ref_sub_unit,(SELECT @id:=0) x
        WHERE id_unit='.$id_unit);

   return DataTables::of($SBRef)
   ->make(true);
}

public function getZonaSSH()
{
   $getZona=DB::SELECT('SELECT DISTINCT b.id_zona, c.keterangan_zona
            FROM ref_ssh_perkada a
            INNER JOIN ref_ssh_perkada_zona b ON a.id_perkada = b.id_perkada
            INNER JOIN ref_ssh_zona c ON b.id_zona = c.id_zona
            WHERE a.flag = 1');

   return json_encode($getZona);
}

public function getSatuanPublik($id_asb)
{
   $getZona=DB::SELECT('SELECT a.id_aktivitas_asb, a.id_satuan_1 as id_satuan_publik,b.uraian_satuan
            FROM trx_asb_aktivitas a
            INNER JOIN ref_satuan b ON a.id_satuan_1 = b.id_satuan
            WHERE a.id_aktivitas_asb ='.$id_asb.'  
            UNION
            SELECT a.id_aktivitas_asb, a.id_satuan_2 as id_satuan_publik,b.uraian_satuan
            FROM trx_asb_aktivitas a
            INNER JOIN ref_satuan b ON a.id_satuan_2 = b.id_satuan
            WHERE a.id_aktivitas_asb ='.$id_asb);

   return json_encode($getZona);
}

public function getItemSSH($id_zona,$like_cari)
{
   $getItem=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, b.id_zona, c.id_tarif_ssh, d.uraian_tarif_ssh, 
            c.jml_rupiah, d.id_satuan, e.uraian_satuan, f.jml_rekening
            FROM ref_ssh_perkada a
            INNER JOIN ref_ssh_perkada_zona b ON a.id_perkada = b.id_perkada
            INNER JOIN ref_ssh_perkada_tarif c ON b.id_zona_perkada = c.id_zona_perkada
            INNER JOIN ref_ssh_tarif d ON c.id_tarif_ssh = d.id_tarif_ssh
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
                where a.kd_rek_1=5 and a.kd_rek_2=2 and b.id_tarif_ssh ='.$tarif.') b, (SELECT @id:=0) a');
      } else {
        $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
            kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 where kd_rek_1=5 and kd_rek_2=2) b, (SELECT @id:=0) a');
      }

      return DataTables::of($refrekening)
      ->make(true);
    }



public function getHitungASB($id_asb,$id_zona){
    $getHitung=DB::SELECT('SELECT a.id_aktivitas_asb, a.id_komponen_asb, a.id_komponen_asb_rinci, a.id_tarif_ssh, 
                a.id_zona, a.harga_satuan, a.jml_pagu,b.nm_aktivitas_asb,b.jenis_biaya,COALESCE(b.hub_driver,0) as hub_driver,COALESCE(b.koef,0) as koef,COALESCE(b.r1,0)as r1,COALESCE(b.r2,0) as r2,b.id_satuan_1,b.sat_derivatif_1,b.id_satuan_2,b.sat_derivatif_2,b.sat_display_1,b.sat_display_2,COALESCE(b.kf1,0) as kf1,COALESCE(b.kf2,0) as kf2,COALESCE(b.kf3,0) as kf3,COALESCE(b.km1,0) as km1,COALESCE(b.km2,0) as km2,b.id_rekening
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
                WHERE a.id_aktivitas_asb='.$id_asb.' AND a.id_zona='.$id_zona);

    return json_encode($getHitung);

}

public function unloadASB(Request $req){
    $getHitung=DB::DELETE('DELETE FROM trx_renja_rancangan_belanja 
                WHERE id_aktivitas_asb='.$req->id_aktivitas_asb.' AND id_lokasi_renja='.$req->id_lokasi_renja);
    return json_encode($getHitung);

}

}