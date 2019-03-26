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
use App\Models\RefKecamatan;
use App\Models\RefDesa;

class RefKecamatanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
	public function index(){
        if(Auth::check()){ 
            return view('parameter.ref_kecamatan');
        } else {
            return view ( 'errors.401' );
        } 
        
    }

    public function getListKecamatan($id_kab)
    {
        $getListKecamatan=DB::select('SELECT (@id:=@id+1) as no_urut, id_pemda, kd_kecamatan, id_kecamatan, nama_kecamatan
                    FROM ref_kecamatan, (SELECT @id:=0) x WHERE id_pemda='.$id_kab);

        return DataTables::of($getListKecamatan)
            ->addColumn('action',function($getListKecamatan){
                    return '
                        <button id="btnEditKecamatan" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Kecamatan" title="Edit Kecamatan" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }

    public function getListKabKota()
    {
        $getListKabKota=DB::select('SELECT (@id:=@id+1) as no_urut, id_pemda, id_prov, id_kab, kd_kab, nama_kab_kota FROM ref_kabupaten, (SELECT @id:=0) x');

        return DataTables::of($getListKabKota)
        //     ->addColumn('action',function($getListKabKota){
        //             return '
        //                 <button id="btnEditKecamatan" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Kecamatan" title="Edit Kecamatan" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
        //             ' ;
        //     })
        ->make(true);
    }

    public function getListDesa($id_kecamatan)
    {
        $getListDesa=DB::select('SELECT (@id:=@id+1) as no_urut, a.id_kecamatan, a.kd_desa, a.id_desa, a.status_desa, a.nama_desa, a.id_zona, CASE a.status_desa
                WHEN 1 THEN "Kelurahan"
                WHEN 2 THEN "Desa"
                END as status_display FROM ref_desa a, (SELECT @id:=0) x WHERE a.id_kecamatan='.$id_kecamatan);

        return DataTables::of($getListDesa)
            ->addColumn('action',function($getListDesa){
                    return '
                        <button id="btnEditDesa" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Desa/Kelurahan" title="Edit Desa/Kelurahan" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }


    public function addKecamatan (Request $req)
    {
        $data = new RefKecamatan();
        $data->id_pemda= $req->id_pemda;
        $data->kd_kecamatan= $req->kd_kecamatan;
        $data->nama_kecamatan= $req->nama_kecamatan;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editKecamatan (Request $req)
    {
        $data = RefKecamatan::find($req->id_kecamatan) ;        
        $data->id_pemda= $req->id_pemda;
        $data->kd_kecamatan= $req->kd_kecamatan;
        $data->nama_kecamatan= $req->nama_kecamatan;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function addDesa (Request $req)
    {
        $data = new RefDesa();
        $data->id_kecamatan= $req->id_kecamatan;
        $data->kd_desa= $req->kd_desa;
        $data->status_desa= $req->status_desa;
        $data->nama_desa= $req->nama_desa;
        $data->id_zona= $req->id_zona;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editDesa (Request $req)
    {
        $data = RefDesa::find($req->id_desa) ;        
        $data->id_kecamatan= $req->id_kecamatan;
        $data->kd_desa= $req->kd_desa;
        $data->status_desa= $req->status_desa;
        $data->nama_desa= $req->nama_desa;
        $data->id_zona= $req->id_zona;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusKecamatan (Request $req)
    {
        RefKecamatan::where('id_kecamatan',$req->id_kecamatan)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }   

}