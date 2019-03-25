<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\TrxPokir;
use App\Models\TrxPokirUsulan;
use App\Models\TrxPokirLokasi;
use Auth;



class TrxPokirController extends Controller
{
    public function getData($id_tahun)
    {
        $datapokir=DB::select('SELECT (@id:=@id+1) as no_urut,id_tahun,trx_pokir.id_pokir,tanggal_pengusul,asal_pengusul, CASE asal_pengusul
                WHEN 0 THEN "Fraksi"
                WHEN 1 THEN "Pimpinan"
                WHEN 2 THEN "Badan Musyawarah"
                WHEN 3 THEN "Komisi"
                WHEN 4 THEN "Badan Legislasi Daerah"
                WHEN 5 THEN "Badan Anggaran"
                WHEN 6 THEN "Badan Kehormatan"
                WHEN 7 THEN "Panitia Ad Hoc"
                ELSE "Kelangkapan Dewan Lainnya"
                END AS display_pengusul,jabatan_pengusul,CASE jabatan_pengusul
                WHEN 0 THEN "Ketua"
                WHEN 1 THEN "Wakil Ketua"
                WHEN 2 THEN "Sekretaris"
                WHEN 3 THEN "Bendahara"
                WHEN 4 THEN "Anggota"
                ELSE "Jabatan Lainnya"
                END AS display_jabatan,nama_pengusul,nomor_anggota,daerah_pemilihan,media_pokir, COALESCE(b.jml_pokir,0) as jml_pokir 
                FROM trx_pokir 
								LEFT OUTER JOIN (SELECT id_pokir, count(COALESCE(id_pokir_tl,0)) as jml_pokir FROM trx_pokir_tl GROUP BY id_pokir ) b ON trx_pokir.id_pokir = b.id_pokir, 
                (Select @id:=0) j WHERE id_tahun='.$id_tahun.' AND entried_at='.Auth::User()->id);

        return DataTables::of($datapokir)
          ->addColumn('action', function ($datapokir) {

            if($datapokir->jml_pokir == 0)
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi<span class="caret"></span></button>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a data-toggle="tooltip" title="Lihat Rincian Usulan Pokir" id="btnViewRincian" class="dropdown-item"><i class="fa fa-eye fa-fw fa-lg"></i> Lihat Rincian</a>
                        </li>
                        <li>
                            <a data-toggle="tooltip" title="Edit Identitas Pengusul Pokir" id="edit-idenpokir" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit Identitas</a>
                        </li>
                        <li>
                            <a data-toggle="tooltip" title="Hapus Pokir" id="hapus-pokir" class="dropdown-item"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Pokir</a>
                        </li>
                        <li>
                            <a data-toggle="tooltip" title="Pencetakan Usulan Pokir per Pengusul" id="cetak-pokir" class="dropdown-item"><i class="fa fa-print fa-fw fa-lg text-info"></i> Cetak Pokir per Pengusul</a>
                        </li>                         
                    </ul>
                </div>
              ';

              if($datapokir->jml_pokir != 0)
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi<span class="caret"></span></button>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a data-toggle="tooltip" title="Lihat Rincian Usulan Pokir" id="btnViewRincian" class="dropdown-item"><i class="fa fa-eye fa-fw fa-lg"></i> Lihat Rincian</a>
                        </li>
                        <li>
                            <a data-toggle="tooltip" title="Pencetakan Usulan Pokir per Pengusul" id="cetak-pokir" class="dropdown-item"><i class="fa fa-print fa-fw fa-lg text-info"></i> Cetak Pokir per Pengusul</a>
                        </li>                         
                    </ul>
                </div>
              ';
            
            })
          ->make(true);
    }

    public function getUsulanPokir($id_pokir)
    {
        $getRenja = DB::SELECT('SELECT a.id_pokir, a.id_pokir_usulan, a.no_urut, a.id_judul_usulan, a.diskripsi_usulan,
                  a.volume, a.id_satuan, a.jml_anggaran, b.uraian_satuan, a.id_unit, c.nm_unit, COALESCE(d.jml_pokir,0) as jml_pokir 
                  FROM trx_pokir_usulan AS a                  
								  LEFT OUTER JOIN (SELECT id_pokir, count(COALESCE(id_pokir_tl,0)) as jml_pokir FROM trx_pokir_tl GROUP BY id_pokir ) d ON a.id_pokir = d.id_pokir
                  LEFT OUTER JOIN ref_satuan AS b ON a.id_satuan = b.id_satuan
                  LEFT OUTER JOIN ref_unit AS c ON a.id_unit = c.id_unit
                  WHERE a.id_pokir='.$id_pokir);

        return Datatables::of($getRenja)
            ->addColumn('action',function($getRenja){
              if($getRenja->jml_pokir == 0)
                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi<span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnLokasiPokir" class="dropdown-item"><i class="fa fa-location-arrow fa-fw fa-lg"></i> Lihat Lokasi</a>
                        </li>
                        <li>
                            <a id="btnEditUsulanPokir" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Rincian</a>
                        </li>
                    </ul>
                    </div>
                    ' ;
              if($getRenja->jml_pokir != 0)
                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi<span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnLokasiPokir" class="dropdown-item"><i class="fa fa-location-arrow fa-fw fa-lg"></i> Lihat Lokasi</a>
                        </li>
                    </ul>
                    </div>
                    ' ;

            })
            ->make(true);
    }

    public function getLokasiPokir($id_usulan)
    {
        $getRenja = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_pokir_usulan, a.id_pokir_lokasi, a.id_kecamatan, a.id_desa,
              a.rw, a.rt, a.diskripsi_lokasi, b.kd_desa, b.nama_desa, c.kd_kecamatan, c.nama_kecamatan, COALESCE(d.jml_pokir,0) as jml_pokir
              FROM trx_pokir_lokasi AS a
              INNER JOIN trx_pokir_usulan AS e ON a.id_pokir_usulan = e.id_pokir_usulan                  
							LEFT OUTER JOIN (SELECT id_pokir, count(COALESCE(id_pokir_tl,0)) as jml_pokir FROM trx_pokir_tl GROUP BY id_pokir ) d ON e.id_pokir = d.id_pokir
              Left OUTER JOIN ref_desa AS b ON a.id_kecamatan = b.id_kecamatan AND a.id_desa = b.id_desa
              INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan, (SELECT @id:=0) x 
              WHERE a.id_pokir_usulan='.$id_usulan);

        return Datatables::of($getRenja)
            ->addColumn('action',function($getRenja){              
              if($getRenja->jml_pokir == 0)
                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi<span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnEditLokasiPokir" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Lokasi</a>
                        </li>
                    </ul>
                    </div>
                    ' ;
            })
            ->make(true);
    }

    public function getDesa($id_kecamatan)
    {
        $getRenja=DB::select('SELECT b.id_kecamatan, b.kd_desa, b.id_desa, b.status_desa, b.nama_desa, b.id_zona
                FROM ref_desa AS b WHERE b.id_kecamatan='.$id_kecamatan);

        return json_encode($getRenja);
    }

    public function getDesaAll()
    {
        $getRenja=DB::select('SELECT b.id_kecamatan, b.kd_desa, b.id_desa, b.status_desa, b.nama_desa, b.id_zona
                FROM ref_desa AS b');

        return json_encode($getRenja);
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
      if(Auth::check()){ 
        return view('pokir.index');
      } else {
        return view ( 'errors.401' );
      }
    }

    public function addIdentitas(Request $req)
     {
          $data = new TrxPokir;
          $data->id_tahun= $req->id_tahun;
          $data->tanggal_pengusul= $req->tanggal_pengusul;
          $data->asal_pengusul= $req->asal_pengusul;
          $data->jabatan_pengusul= $req->jabatan_pengusul;
          $data->nama_pengusul= $req->nama_pengusul;
          $data->nomor_anggota= $req->nomor_anggota;
          $data->media_pokir= $req->media_pokir;
          $data->bukti_dokumen= null; 
          $data->entried_at = Auth::User()->id;
        try{
          $data->save (['timestamps' => true]);
          return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
     }

    public function editIdentitas(Request $req)
      {
          $data = TrxPokir::find($req->id_pokir);
          $data->id_tahun= $req->id_tahun;
          $data->tanggal_pengusul= $req->tanggal_pengusul;
          $data->asal_pengusul= $req->asal_pengusul;
          $data->jabatan_pengusul= $req->jabatan_pengusul;
          $data->nama_pengusul= $req->nama_pengusul;
          $data->nomor_anggota= $req->nomor_anggota;
          $data->media_pokir= $req->media_pokir;
          $data->entried_at = Auth::User()->id;
          // $data->bukti_dokumen= $req->bukti_dokumen; 
        
        try{
          $data->save (['timestamps' => true]);
          return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
      }

    public function getNoUsulan($id_pokir){
      $result = DB::SELECT('SELECT COALESCE(MAX(a.no_urut),0)+1 as no_max FROM trx_pokir_usulan AS a 
              WHERE a.id_pokir ='.$id_pokir);
      return $result;
    }

    public function hapusIdentitas(Request $req)
      {
        
        try{
          $result=DB::DELETE('DELETE FROM trx_pokir WHERE id_pokir='.$req->id_pokir);
          
          return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        }
        catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
        }
      }

    public function addUsulan(Request $req)
     {
      $cek = DB::SELECT('SELECT id_pokir, count(COALESCE(id_pokir_tl,0)) as jml_pokir FROM trx_pokir_tl WHERE id_pokir='.$req->id_pokir.' GROUP BY id_pokir ');

      $data = new TrxPokirUsulan;
      $data->id_pokir= $req->id_pokir;
      $data->no_urut= $req->no_urut;
      $data->id_judul_usulan= $req->id_judul_usulan;
      $data->diskripsi_usulan= $req->diskripsi_usulan;
      $data->volume= $req->volume;
      $data->id_unit= $req->id_unit;
      $data->id_satuan= $req->id_satuan;
      $data->jml_anggaran= $req->jml_anggaran;
      $data->entried_at = Auth::User()->id;

       if($cek != null){
         if($cek[0]->jml_pokir <> 0) {
           return response ()->json (['pesan'=>'Maaf Data Usulan Sudah Tidak Dapat Ditambahkan Kembali, Status Sudah diverifikasi..','status_pesan'=>'0']);
         } else {
          try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
              $error_code = $e->errorInfo[1] ;
              return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
         }
       } else {
         try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
              $error_code = $e->errorInfo[1] ;
              return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
       }        
     }

    public function editUsulan(Request $req)
      {
          $data = TrxPokirUsulan::find($req->id_pokir_usulan);
          $data->id_pokir= $req->id_pokir;
          $data->no_urut= $req->no_urut;
          $data->id_judul_usulan= $req->id_judul_usulan;
          $data->diskripsi_usulan= $req->diskripsi_usulan;
          $data->volume= $req->volume;
          $data->id_unit= $req->id_unit;
          $data->id_satuan= $req->id_satuan;
          $data->jml_anggaran= $req->jml_anggaran;
          $data->entried_at = Auth::User()->id;        
        try{
          $data->save (['timestamps' => true]);
          return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
      }

    public function hapusUsulan(Request $req)
      {
        
        try{
          TrxPokirUsulan::where('id_pokir_usulan',$req->id_pokir_usulan)->delete ();          
          return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        }
        catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
        }
      }

public function addLokasi(Request $req)
     {
      $cek = DB::SELECT('SELECT a.id_pokir_usulan, COALESCE(d.jml_pokir,0) as jml_pokir FROM trx_pokir_usulan a 
        LEFT OUTER JOIN (SELECT id_pokir, count(COALESCE(id_pokir_tl,0)) as jml_pokir FROM trx_pokir_tl GROUP BY id_pokir) d ON a.id_pokir = d.id_pokir
        WHERE a.id_pokir_usulan='.$req->id_pokir_usulan);

        $data = new TrxPokirLokasi;
        $data->id_pokir_usulan= $req->id_pokir_usulan;
        $data->id_kecamatan= $req->id_kecamatan;
        $data->id_desa= $req->id_desa;
        $data->diskripsi_lokasi= $req->diskripsi_lokasi;
        $data->rt= $req->rt;
        $data->rw= $req->rw;

        if($cek != null){
         if($cek[0]->jml_pokir <> 0) {
           return response ()->json (['pesan'=>'Maaf Data Usulan Sudah Tidak Dapat Ditambahkan Kembali, Status Sudah diverifikasi..','status_pesan'=>'0']);
         } else {
          try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
              $error_code = $e->errorInfo[1] ;
              return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
         }
       } else {
         try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
              $error_code = $e->errorInfo[1] ;
              return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
       }
     }

public function editLokasi(Request $req)
      {
        
        try{
          $data = TrxPokirLokasi::find($req->id_pokir_lokasi);
          $data->id_pokir_usulan= $req->id_pokir_usulan;
          $data->id_kecamatan= $req->id_kecamatan;
          $data->id_desa= $req->id_desa;
          $data->diskripsi_lokasi= $req->diskripsi_lokasi;
          $data->rt= $req->rt;
          $data->rw= $req->rw;
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
        
        try{
          TrxPokirLokasi::where('id_pokir_lokasi',$req->id_pokir_lokasi)->delete ();          
          return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        }
        catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
        }
      }


}
