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
use App\Models\RefSumberDana;
use App\Models\RefJenisLokasi;

class RefParameterLainnyaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
	public function index(){

        // if(Auth::check()){ 
            return view('parameter.ref_lainnya');
        // } else {
            // return view ( 'errors.401' );
        // } 
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

    public function getSumberDana()
    {
        $getListLokasi=DB::select('SELECT (@id:=@id+1) as no_urut, a.id_sumber_dana, a.uraian_sumber_dana
                    FROM ref_sumber_dana AS a, (SELECT @id:=0) x');

        return DataTables::of($getListLokasi)
            ->addColumn('action',function($getListLokasi){
                // if($getListLokasi->jml_lokasi==0){
                    return '
                        <button id="btnHapusSumber" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Sumber Dana" title="Hapus Sumber Dana" class="btn btn-danger btn-sm"><i class="fa fa-trash-o fa-fw fa-lg"></i>Hapus Sumber Dana</button>
                    ' ;
                // }
            })
        ->make(true);
    }   

    public function hapusSumberDana (Request $req)
    {
        $result = RefSumberDana::destroy($req->id_sumber_dana);

        if($result!=0){
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
        };
    }

    public function addSumberDana (Request $req)
    {
        $id = DB::SELECT('select MAX(id_sumber_dana) as id_auto from ref_sumber_dana');
        
        $data = new RefSumberDana();
        $data->id_sumber_dana= $id[0]->id_auto + 1;
        $data->uraian_sumber_dana= $req->uraian_sumber_dana;

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