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
use App\Models\RefSatuan;


class RefSatuanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // if(Auth::check()){ 
            return view('parameter.ref_satuan');
        // } else {
            // return view ( 'errors.401' );
        // } 
    }

    public function getdata(Datatables $datatables)
    {
        $refsatuan = DB::SELECT('SELECT id_satuan,uraian_satuan,singkatan_satuan,scope_pemakaian FROM ref_satuan');
          
        return DataTables::of($refsatuan)
          ->addColumn('action', function ($refsatuan) {
              return '<button class="edit-modal btn btn-sm btn-warning" data-id_satuan="'.$refsatuan->id_satuan.'" data-uraian_satuan="'.$refsatuan->uraian_satuan.'" data-singkatan_satuan="'.$refsatuan->singkatan_satuan.'" data-scope_pemakaian="'.$refsatuan->scope_pemakaian.'"><i class="fa fa-pencil fa-fw"></i></button>
              <button class="delete-modal btn btn-sm btn-danger" data-id_satuan="'.$refsatuan->id_satuan.'" data-uraian_satuan="'.$refsatuan->uraian_satuan.'"><i class="fa fa-trash fa-fw"></i></button>
              ';})
          ->make(true);
    }

    public function tambah(Request $req)
    {
        $data = new RefSatuan ();
        $data->uraian_satuan = $req->ur_satuan ;
        $data->singkatan_satuan = $req->sing_satuan ;
        $data->scope_pemakaian = $req->scope_pemakaian ;
    	try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function edit(Request $req)
    {
        $data = RefSatuan::find($req->id_satuan) ;
        $data->uraian_satuan = $req->ur_satuan ;
        $data->singkatan_satuan = $req->sing_satuan ;
        $data->scope_pemakaian = $req->scope_pemakaian ;
    	try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapus(Request $req)
    {
        RefSatuan::where('id_satuan',$req->id_satuan)->delete ();
      	return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }
}
