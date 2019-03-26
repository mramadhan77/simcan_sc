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
use App\Models\RefProgram;
use App\Models\RefKegiatan;

class RefProgramController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
	public function index(){
        // if(Auth::check()){ 
            return view('parameter.ref_program');
        // } else {
            // return view ( 'errors.401' );
        // } 
        
    }

    public function getListUrusan()
    {
        $getListUrusan=DB::select('SELECT (@id:=@id+1) as no_urut, kd_urusan, nm_urusan FROM ref_urusan, (SELECT @id:=0) x');

        return DataTables::of($getListUrusan)
        ->addColumn('details_url', function($getListUrusan) {
                return url('admin/parameter/program/getListBidang/' . $getListUrusan->kd_urusan);
            })
        ->make(true);
    }

    public function getListBidang($id_urusan)
    {
        $getListBidang=DB::select('SELECT (@id:=@id+1) as no_urut, b.id_bidang, b.kd_urusan, b.kd_bidang, b.nm_bidang, 
                b.kd_fungsi, a.nm_urusan 
                FROM ref_bidang b 
                INNER JOIN ref_urusan a ON b.kd_urusan=a.kd_urusan, (SELECT @id:=0) x WHERE b.kd_urusan='.$id_urusan);

        return DataTables::of($getListBidang)
        ->make(true);
    }

    public function getListProgram($id_bidang)
    {
        $getListProgram=DB::select('SELECT (@id:=@id+1) as no_urut, c.id_bidang, c.id_program, c.kd_program, c.uraian_program,
                b.nm_bidang, a.nm_urusan
                FROM ref_program c
                INNER JOIN ref_bidang b ON c.id_bidang=b.id_bidang
                INNER JOIN ref_urusan a ON b.kd_urusan=a.kd_urusan, (SELECT @id:=0) x WHERE c.id_bidang='.$id_bidang);

        return DataTables::of($getListProgram)
            ->addColumn('action',function($getListProgram){
                    return '
                        <button id="btnEditProgram" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Program" title="Edit Program" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusProgram" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Program" title="Hapus Program" class="btn btn-danger btn-sm"><i class="fa fa-trash fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }

    public function getListKegiatan($id_program)
    {
        $getListKegiatan=DB::select('SELECT (@id:=@id+1) as no_urut, d.id_kegiatan, d.id_program, d.kd_kegiatan, d.nm_kegiatan, 
                    c.uraian_program,b.nm_bidang, a.nm_urusan
                    FROM ref_kegiatan d
                    INNER JOIN ref_program c ON d.id_program = c.id_program
                    INNER JOIN ref_bidang b ON c.id_bidang=b.id_bidang
                    INNER JOIN ref_urusan a ON b.kd_urusan=a.kd_urusan, 
                    (SELECT @id:=0) x WHERE d.id_program='.$id_program);

        return DataTables::of($getListKegiatan)
            ->addColumn('action',function($getListKegiatan){
                    return '
                        <button id="btnEditKegiatan" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Kegiatan" title="Edit Kegiatan" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusKegiatan" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Kegiatan" title="Hapus Kegiatan" class="btn btn-danger btn-sm"><i class="fa fa-trash fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }


    public function addProgram (Request $req)
    {
        $data = new RefProgram();
        $data->id_bidang= $req->id_bidang;
        $data->kd_program= $req->kd_program;
        $data->uraian_program= $req->nm_program;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editProgram (Request $req)
    {
        $data = RefProgram::find($req->id_program) ;        
        $data->id_bidang= $req->id_bidang;
        $data->kd_program= $req->kd_program;
        $data->uraian_program= $req->nm_program;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusProgram (Request $req)
    {
        RefProgram::where('id_program',$req->id_program)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }  

    public function addKegiatan (Request $req)
    {
        $data = new RefKegiatan();
        $data->id_program= $req->id_program;
        $data->kd_kegiatan= $req->kd_kegiatan;
        $data->nm_kegiatan= $req->nm_kegiatan;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editKegiatan (Request $req)
    {
        $data = RefKegiatan::find($req->id_kegiatan);
        $data->id_program= $req->id_program;
        $data->kd_kegiatan= $req->kd_kegiatan;
        $data->nm_kegiatan= $req->nm_kegiatan;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusKegiatan (Request $req)
    {
        RefKegiatan::where('id_kegiatan',$req->id_kegiatan)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }   

}