<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;
use Yajra\Datatables\Datatables;
use Session;
use DB;
use Validator;
use Response;
use Auth;
use App\Models\RefSshZona;


class RefSshZonaController extends Controller
{
  public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // if(Auth::check()){ 
            return view('ssh.zona.index');
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function getdata(Datatables $datatables)
    {
        $refzona = DB::select('SELECT  id_zona, keterangan_zona, diskripsi_zona
                  FROM ref_ssh_zona a, (select @id:=0) b');
        
        return DataTables::of($refzona)
          ->addColumn('action', function ($refzona) {
              return '
              <div class="btn-group">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="glyphicon glyphicon-wrench"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a class="edit-modal dropdown-item" data-id_zona="'.$refzona->id_zona.'" data-keterangan_zona="'.$refzona->keterangan_zona.'" data-diskripsi_zona="'.$refzona->diskripsi_zona.'"><i class="glyphicon glyphicon-edit"></i> Ubah Zona ASB</a>
                  </li>
                  <li>
                    <a class="delete-modal dropdown-item" data-id_zona="'.$refzona->id_zona.'" data-keterangan_zona="'.$refzona->keterangan_zona.'" data-diskripsi_zona="'.$refzona->diskripsi_zona.'"><i class="glyphicon glyphicon-trash"></i> Hapus Zona ASB</a>
                  </li>                        
                </ul>
              </div>
              ';})
          ->addIndexColumn()
          ->make(true);
    }

    public function store(Request $req)
    {
        try{
        $data = new RefSshZona ();
        $data->keterangan_zona = $req->ket_zona ;
        $data->diskripsi_zona = $req->diskripsi_zona ;
    		$data->save (['timestamps' => false]);
  			
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

    public function update(Request $req)
    {
        try{
        $data = RefSshZona::find($req->id) ;
    		$data->keterangan_zona = $req->ket_zona ;
        $data->diskripsi_zona = $req->diskripsi_zona ;
    		$data->save (['timestamps' => false]);
    		
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

    public function destroy(Request $req)
    {
        RefSshZona::where('id_zona',$req->id_zona)->delete ();
      	return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
    }
}
