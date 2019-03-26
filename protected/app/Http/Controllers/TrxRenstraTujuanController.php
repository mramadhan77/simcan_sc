<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Auth;
use CekAkses;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\RefPemda;
use App\Models\RefUnit;
use App\Models\RefIndikator;
use App\Models\RefUrusan;
use App\Models\RefBidang;
use App\Models\Can\TrxRenstraTujuan;

class TrxRenstraTujuanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getTujuanRenstra($id_misi_renstra)
    {
        $tujuanrenstra=DB::SELECT('SELECT CONCAT(a.no_urut,".",b.no_urut) AS kd_misi, c.thn_id,c.no_urut,
        c.id_misi_renstra,c.id_tujuan_renstra,c.id_perubahan,c.uraian_tujuan_renstra,c.sumber_data
        FROM trx_renstra_visi AS a
        INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
        INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
        WHERE c.id_misi_renstra='.$id_misi_renstra.' ORDER BY c.no_urut ASC' );

        return DataTables::of($tujuanrenstra)
        ->addColumn('details_url', function($tujuanrenstra) {
            return url('renstra/getIndikatorTujuan/'.$tujuanrenstra->id_tujuan_renstra);
        })
        ->addColumn('action', function ($tujuanrenstra) {
            return 
            '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a class="btnDetailTujuan dropdown-item"><i class="fa fa-bullseye fa-fw fa-lg text-success"></i> Detail Data Tujuan</a>
                  </li>
                  <li>
                      <a class="btnAddTujuanIndikator dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Tujuan</a>
                  </li>
                </ul>
              </div>'
              ;})
        ->make(true);
    }

     public function addTujuanRenstra(Request $req)
    {
        try{
            $data = new TrxRenstraTujuan;
            $data->thn_id=$req->thn_id;
            $data->no_urut=$req->no_urut;
            $data->id_misi_renstra=$req->id_misi_renstra;
            $data->id_perubahan=$req->id_perubahan;
            $data->uraian_tujuan_renstra=$req->uraian_tujuan_renstra;
            $data->sumber_data=1;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

    public function editTujuanRenstra(Request $req)
    {
        try{
            $data = TrxRenstraTujuan::find($req->id_tujuan_renstra);
            $data->no_urut=$req->no_urut;
            $data->id_perubahan=$req->id_perubahan;
            $data->uraian_tujuan_renstra=$req->uraian_tujuan_renstra;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

    public function hapusTujuanRenstra(Request $req)
    {
        $result = TrxRenstraTujuan::where('id_tujuan_renstra',$req->id_tujuan_renstra)->delete();   
        if($result != 0){
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
        }
    }

}
