<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\CekAkses;
use Auth;
use App\Models\RefLokasi;
use App\Models\RefJenisLokasi;

class RefLokasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
	public function index(){
        // if(Auth::check()){ 
            return view('parameter.ref_lokasi');
        // } else {
            // return view ( 'errors.401' );
        // }
        
    }

    public function getListLokasi()
    {
        $getListLokasi=DB::select('SELECT (@id:=@id+1) as no_urut, a.id_lokasi, a.jenis_lokasi, a.nama_lokasi,
                    a.id_desa, a.id_desa_awal, a.id_desa_akhir, a.koordinat_1, a.koordinat_2, a.koordinat_3,
                    a.koordinat_4,  a.luasan_kawasan, a.satuan_luas, a.keterangan_lokasi, b.uraian_satuan as ur_satuan_luas,
                    a.panjang, a.satuan_panjang, c.uraian_satuan as ur_satuan_panjang, a.lebar, a.satuan_lebar, d.uraian_satuan as ur_satuan_lebar,
                    e.nm_jenis As lokasi_display
                    FROM ref_lokasi AS a
                    INNER JOIN ref_jenis_lokasi AS e ON a.jenis_lokasi = e.id_jenis
                    LEFT OUTER JOIN ref_satuan AS b ON a.satuan_luas = b.id_satuan
                    LEFT OUTER JOIN ref_satuan AS c ON a.satuan_panjang = c.id_satuan
                    LEFT OUTER JOIN ref_satuan AS d ON a.satuan_lebar = d.id_satuan, 
                    (SELECT @id:=0) x');

        return DataTables::of($getListLokasi)
            ->addColumn('action',function($getListLokasi){
                // if()
                    return '
                        <button id="btnEditLokasi" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Lokasi" title="Edit Lokasi" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw fa-lg"></i></button>
                        <button id="btnHapusLokasi" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Lokasi" title="Hapus Lokasi" class="btn btn-danger btn-sm"><i class="fa fa-trash-o fa-fw fa-lg"></i></button>
                    ' ;
            })
        ->make(true);
    }

    public function getDataJenis()
    {
        $getListLokasi=DB::select('SELECT (@id:=@id+1) as no_urut, a.id_jenis, a.nm_jenis, Count(b.id_lokasi) as jml_lokasi
                    FROM ref_jenis_lokasi AS a
                    LEFT OUTER JOIN ref_lokasi AS b ON a.id_jenis = b.jenis_lokasi, (SELECT @id:=0) x
                    GROUP BY a.nm_jenis, a.id_jenis ORDER BY a.id_jenis');

        return DataTables::of($getListLokasi)
            ->addColumn('action',function($getListLokasi){
                if($getListLokasi->jml_lokasi==0){
                    return '
                        <button id="btnHapusJenis" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Jenis Lokasi" title="Hapus Jenis Lokasi" class="btn btn-danger btn-sm"><i class="fa fa-trash-o fa-fw fa-lg"></i>Hapus Jenis Lokasi</button>
                    ' ;
                }
            })
        ->make(true);
    }

    public function insertWilayah(){
        $query = DB::INSERT('INSERT INTO ref_lokasi(jenis_lokasi,nama_lokasi,id_desa,keterangan_lokasi)
                    SELECT 99,CONCAT("Kecamatan ",a.nama_kecamatan) as nama_sumber,
                    9999 as id_sumber,"Hasil Import" as keterangan
                    FROM ref_kecamatan AS a
                    LEFT OUTER JOIN (SELECT jenis_lokasi, SUBSTRING(p.nama_lokasi, 11, 100) AS nama_sumber FROM ref_lokasi p WHERE id_desa=9999) AS b
                    ON a.nama_kecamatan = b.nama_sumber
                    WHERE b.nama_sumber IS NULL
                    UNION SELECT 0, CASE a.status_desa
                        WHEN 1 THEN CONCAT("Kelurahan ",a.nama_desa)
                        WHEN 2 THEN CONCAT("Desa ",a.nama_desa)
                        END AS nama_sumber,
                    a.id_desa AS id_sumber,
                    "Hasil Import" AS keterangan
                    FROM ref_desa AS a
                    LEFT OUTER JOIN ref_lokasi AS b ON a.id_desa = b.id_desa
                    WHERE b.id_desa IS NULL
                ');

        if($query ==1 ){
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); 
        } else {
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
        
    }

    public function getJenisLokasi(){
      $jenis=DB::select('SELECT id_jenis, nm_jenis FROM ref_jenis_lokasi WHERE id_jenis <> 0');
        return json_encode($jenis);
    }

    public function addLokasi (Request $req)
    {
        $data = new RefLokasi();
        $data->jenis_lokasi= $req->jenis_lokasi;
        $data->nama_lokasi= $req->nama_lokasi;
        // $data->id_desa= $req->id_desa;
        // $data->id_desa_awal= $req->id_desa_awal;
        // $data->id_desa_akhir= $req->id_desa_akhir;
        $data->luasan_kawasan= $req->luasan_kawasan;
        $data->satuan_luas= $req->satuan_luas;
        $data->panjang= $req->panjang;
        $data->satuan_panjang= $req->satuan_panjang;
        $data->lebar= $req->lebar;
        $data->satuan_lebar= $req->satuan_lebar;
        $data->keterangan_lokasi= $req->keterangan_lokasi;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editLokasi (Request $req)
    {
        $data = RefLokasi::find($req->id_lokasi) ;
        $data->nama_lokasi= $req->nama_lokasi;
        // $data->id_desa= $req->id_desa;
        // $data->id_desa_awal= $req->id_desa_awal;
        // $data->id_desa_akhir= $req->id_desa_akhir;
        $data->luasan_kawasan= $req->luasan_kawasan;
        $data->satuan_luas= $req->satuan_luas;
        $data->panjang= $req->panjang;
        $data->satuan_panjang= $req->satuan_panjang;
        $data->lebar= $req->lebar;
        $data->satuan_lebar= $req->satuan_lebar;
        $data->keterangan_lokasi= $req->keterangan_lokasi;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusLokasi (Request $req)
    {
        $result = RefLokasi::where('id_lokasi',$req->id_lokasi)->delete ();
        
        if($result!=0){
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
        };
    } 

    public function hapusJenisLokasi (Request $req)
    {
        $result = RefJenisLokasi::destroy($req->id_jenis);

        if($result!=0){
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
        };
    }

    public function addJenisLokasi (Request $req)
    {
        $id = DB::SELECT('select MAX(id_jenis) as id_auto from ref_jenis_lokasi where id_jenis <> 99');

        if($id[0]->id_auto==98){
            $id_jenis = 100;
        } else {
            $id_jenis = $id[0]->id_auto + 1 ;
        }

        $data = new RefJenisLokasi();
        $data->nm_jenis= $req->nm_jenis;
        $data->id_jenis= $id_jenis;

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