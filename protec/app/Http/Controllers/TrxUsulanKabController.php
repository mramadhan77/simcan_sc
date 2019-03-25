<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\TrxUsulanKab;
use Auth;



class TrxUsulanKabController extends Controller
{
  public function __construct()
    {
        $this->middleware('auth');
    }

    public function getData($id_tahun)
    {
        $datapokir=DB::select('SELECT (@id:=@id+1) as no_urut, a.id_usulan_kab, a.id_tahun, a.id_kab, a.id_unit, a.no_urut, a.judul_usulan, a.uraian_usulan,
        a.volume, a.id_satuan, a.pagu, a.created_at, a.updated_at, a.entry_by, c.id_prov, c.kd_kab, c.nama_kab_kota, b.nm_unit, d.uraian_satuan, a.sumber_usulan
        FROM trx_usulan_kab AS a
        INNER JOIN ref_unit AS b ON a.id_unit = b.id_unit
        INNER JOIN ref_kabupaten AS c ON a.id_kab = c.id_kab
        INNER JOIN ref_satuan AS d ON a.id_satuan = d.id_satuan, (Select @id:=0) j WHERE id_tahun='.$id_tahun);

        return DataTables::of($datapokir)
          ->addColumn('action', function ($datapokir) {
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi<span class="caret"></span></button>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a data-toggle="tooltip" title="Edit Usulan Kabupaten/Kota" id="edit-usulankab" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Edit Usulan</a>
                        </li>
                        <li>
                            <a data-toggle="tooltip" title="Lokasi Usulan Kabupaten/Kota" id="lokasi-usulankab" class="dropdown-item"><i class="fa fa-location-arrow fa-fw fa-lg text-primary"></i> Lokasi Usulan</a>
                        </li>
                        <li>
                            <a data-toggle="tooltip" title="Hapus Usulan" id="hapus-usulankab" class="dropdown-item"><i class="fa fa-trash fa-fw fa-lg text-danger"></i> Hapus Usulan</a>
                        </li>                       
                    </ul>
                </div>
              ';})
          ->make(true);
    }

    public function getDataLokasi($id_usulan)
    {
        $getRenja = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_pokir_usulan, a.id_pokir_lokasi, a.id_kecamatan, a.id_desa,
              a.rw, a.rt, a.diskripsi_lokasi, b.kd_desa, b.nama_desa, c.kd_kecamatan, c.nama_kecamatan
              FROM trx_pokir_lokasi AS a
              Left OUTER JOIN ref_desa AS b ON a.id_kecamatan = b.id_kecamatan AND a.id_desa = b.id_desa
              INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan, (SELECT @id:=0) x 
              WHERE a.id_pokir_usulan='.$id_usulan);

        return Datatables::of($getRenja)
            ->addColumn('action',function($getRenja){
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

    public function getKabupaten()
    {
        $getKabupaten=DB::select('SELECT a.id_pemda, a.id_prov, a.id_kab, a.kd_kab, a.nama_kab_kota, CONCAT(a.id_prov,".",RIGHT(CONCAT("00",a.kd_kab),2)) as kode_kab
                      FROM ref_kabupaten AS a');
        return json_encode($getKabupaten);
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
      // if(Auth::check()){ 
            return view('pramusren.index');
        // } else {
            // return view ( 'errors.401' );
        // }
      
    }

    public function getNoUsulan(Request $request){

      $result = DB::SELECT('SELECT COALESCE(MAX(a.no_urut),0)+1 as no_max FROM trx_usulan_kab AS a 
              WHERE a.id_kab ='.$request->id_kab.' and a.id_tahun = '.$request->tahun);
      return $result;

    }

    public function addUsulan(Request $req)
     {
          $data = new TrxUsulanKab;
          $data->id_tahun = $req->id_tahun;
          $data->id_kab = $req->id_kab;
          $data->id_unit = $req->id_unit;
          $data->no_urut = $req->no_urut;
          $data->judul_usulan = $req->judul_usulan;
          $data->uraian_usulan = $req->uraian_usulan;
          $data->volume = $req->volume;
          $data->id_satuan = $req->id_satuan;
          $data->pagu = $req->pagu;
          $data->entry_by = Auth::User()->id;;
          $data->sumber_usulan = $req->sumber_usulan;
        try{
          $data->save (['timestamps' => true]);
          return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
     }

    public function editUsulan(Request $req)
      {
          $data = TrxUsulanKab::find($req->id_usulan_kab);
          $data->id_tahun = $req->id_tahun;
          $data->id_kab = $req->id_kab;
          $data->id_unit = $req->id_unit;
          $data->no_urut = $req->no_urut;
          $data->judul_usulan = $req->judul_usulan;
          $data->uraian_usulan = $req->uraian_usulan;
          $data->volume = $req->volume;
          $data->id_satuan = $req->id_satuan;
          $data->pagu = $req->pagu;
          $data->entry_by = Auth::User()->id;;
          $data->sumber_usulan = $req->sumber_usulan;       
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
          TrxUsulanKab::where('id_usulan_kab',$req->id_usulan_kab)->delete ();          
          return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        }
        catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
        }
      }

public function addLokasi(Request $req)
     {
        try{
          $data = new TrxPokirLokasi;
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
