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
use App\Models\TrxIsianDataDasar;
use App\Models\RefDesa;

class RefAMHController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        // if(Auth::check()){ 
            return view('dasar.ref_amh');
        // } else {
            // return view ( 'errors.401' );
        // }        
    }
    public function getListamh()
    {
        $getListamh=DB::select('SELECT (@id:=@id+1) as no_urut,
c.id_isian_tabel_dasar,c.tahun,
ifnull(d.nama_kecamatan,"-") as nama_kecamatan,
b.nama_kolom,
ifnull(d.id_kecamatan,"-") as id_kecamatan,
b.id_kolom_tabel_dasar,
ifnull(c.nmin1,"0") as nmin1,
ifnull(c.nmin2,"0") as nmin2,
ifnull(c.nmin3,"0") as nmin3,
ifnull(c.nmin4,"0") as nmin4,
ifnull(c.nmin5,"0") as nmin5
FROM
ref_tabel_dasar AS a
INNER JOIN ref_kolom_tabel_dasar AS b ON b.id_tabel_dasar = a.id_tabel_dasar
LEFT OUTER JOIN trx_isian_data_dasar AS c ON c.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
LEFT OUTER JOIN ref_kecamatan AS d ON c.id_kecamatan = d.id_kecamatan
            
, (SELECT @id:=0) x
where a.id_tabel_dasar=3 and b.level>0
');
        
        return DataTables::of($getListamh)
        ->addColumn('action',function($getListamh){
            return '
                        <button id="btnEditamh" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit amh" title="Edit amh" class="btn btn-primary btn-sm" data-id_isian_tabel_dasar="'.$getListamh->id_isian_tabel_dasar.'"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusamh" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus amh" title="Hapus amh" class="btn btn-danger btn-sm" data-id_isian_tabel_dasar="'.$getListamh->id_isian_tabel_dasar.'"><i class="fa fa-trash fa-fw"></i></button>
                    ' ;
        })
        
        ->make(true);
    }
    public function getTahunamh()
    {
        $getTahun=DB::select('
        select tahun_1 as tahun from ref_tahun
union ALL
select tahun_2 from ref_tahun
union ALL
select tahun_3 from ref_tahun
union ALL
select tahun_4 from ref_tahun
union ALL
select tahun_5 from ref_tahun
');
        return json_encode($getTahun);
    }
    public function getKecamatanamh()
    {
        $getKecamatan=DB::select('
        select id_kecamatan,nama_kecamatan from ref_kecamatan
');
        return json_encode($getKecamatan);
    }
    public function getSektoramh($tahun,$kecamatan)
    {
        $getSektor=DB::select('
        select id_kolom_tabel_dasar,nama_kolom from ref_kolom_tabel_dasar
where id_kolom_tabel_dasar not in (select id_kolom_tabel_dasar from trx_isian_data_dasar where tahun='.$tahun.' and id_kecamatan='.$kecamatan.')
 and  id_tabel_dasar=3 and level<>0
');
        return json_encode($getSektor);
    }
    public function addamh (Request $req)
    {
        $data = new TrxIsianDataDasar();
        $data->id_kolom_tabel_dasar= $req->id_kolom_tabel_dasar;
        $data->id_kecamatan= $req->id_kecamatan;
        $data->nmin1= $req->nmin1;
        $data->nmin2= $req->nmin2;
        $data->nmin3= $req->nmin3;
        $data->nmin4= $req->nmin4;
        $data->nmin5= $req->nmin5;
        $data->tahun= $req->tahun;
        $data->nmin1_persen= $req->nmin1_persen;
        $data->nmin2_persen= $req->nmin2_persen;
        $data->nmin3_persen= $req->nmin3_persen;
        $data->nmin4_persen= $req->nmin4_persen;
        $data->nmin5_persen= $req->nmin5_persen;
        
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }
    public function getEditamh($id)
    {
        $getListamh=DB::select('SELECT c.id_isian_tabel_dasar,
c.tahun,
            
b.nama_kolom,
ifnull(d.id_kecamatan,"-") as id_kecamatan,
b.id_kolom_tabel_dasar,
ifnull(c.nmin1,"0") as nmin1,
ifnull(c.nmin2,"0") as nmin2,
ifnull(c.nmin3,"0") as nmin3,
ifnull(c.nmin4,"0") as nmin4,
ifnull(c.nmin5,"0") as nmin5
FROM
ref_tabel_dasar AS a
INNER JOIN ref_kolom_tabel_dasar AS b ON b.id_tabel_dasar = a.id_tabel_dasar
LEFT OUTER JOIN trx_isian_data_dasar AS c ON c.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
LEFT OUTER JOIN ref_kecamatan AS d ON c.id_kecamatan = d.id_kecamatan
where a.id_tabel_dasar=3 and b.level>0 and id_isian_tabel_dasar='.$id);
        
        return DataTables::of($getListamh)
        ->make(true);
    }
    public function editamh (Request $req)
    {
        $data = TrxIsianDataDasar::find($req->id_isian_tabel_dasar) ;
        //$data->id_kolom_tabel_dasar= $req->id_kolom_tabel_dasar;
        //$data->id_kecamatan= $req->id_kecamatan;
        $data->nmin1= $req->nmin1;
        $data->nmin2= $req->nmin2;
        $data->nmin3= $req->nmin3;
        $data->nmin4= $req->nmin4;
        $data->nmin5= $req->nmin5;
        //$data->tahun= $req->tahun;
//         $data->nmin1_persen= $req->nmin1_persen;
//         $data->nmin2_persen= $req->nmin2_persen;
//         $data->nmin3_persen= $req->nmin3_persen;
//         $data->nmin4_persen= $req->nmin4_persen;
//         $data->nmin5_persen= $req->nmin5_persen;
        
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }
    public function hapusamh (Request $req)
    {
        TrxIsianDataDasar::where('id_isian_tabel_dasar',$req->id_isian_tabel_dasar)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }
    
}