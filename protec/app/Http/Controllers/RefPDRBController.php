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

class RefPDRBController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function index(){

        return view('dasar.ref_pdrb');
    }
    public function getListpdrb()
    {
        $getListpdrb=DB::select('SELECT (@id:=@id+1) as no_urut,
                    c.id_isian_tabel_dasar,c.tahun, 
                    ifnull(d.nama_kecamatan,"-") as nama_kecamatan,
                    b.nama_kolom,
                    ifnull(d.id_kecamatan,"-") as id_kecamatan,
                    b.id_kolom_tabel_dasar,
                    ifnull(c.nmin1,"0") as nmin1,
                    ifnull(c.nmin2,"0") as nmin2,
                    ifnull(c.nmin3,"0") as nmin3,
                    ifnull(c.nmin4,"0") as nmin4,
                    ifnull(c.nmin5,"0") as nmin5,
                    ifnull(c.nmin1_persen,"0") as nmin1_persen,
                    ifnull(c.nmin2_persen,"0") as nmin2_persen,
                    ifnull(c.nmin3_persen,"0") as nmin3_persen,
                    ifnull(c.nmin4_persen,"0") as nmin4_persen,
                    ifnull(c.nmin5_persen,"0") as nmin5_persen
                    FROM
                    ref_tabel_dasar AS a
                    INNER JOIN ref_kolom_tabel_dasar AS b ON b.id_tabel_dasar = a.id_tabel_dasar
                    LEFT OUTER JOIN trx_isian_data_dasar AS c ON c.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
                    LEFT OUTER JOIN ref_kecamatan AS d ON c.id_kecamatan = d.id_kecamatan, (SELECT @id:=0) x
                    where a.id_tabel_dasar=1 and b.level>0 ');

        return DataTables::of($getListpdrb)
            ->addColumn('action',function($getListpdrb){
                    return '
                        <button id="btnEditpdrb" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit pdrb" title="Edit pdrb" class="btn btn-primary btn-sm" data-id_isian_tabel_dasar="'.$getListpdrb->id_isian_tabel_dasar.'"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapuspdrb" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus pdrb" title="Hapus pdrb" class="btn btn-danger btn-sm" data-id_isian_tabel_dasar="'.$getListpdrb->id_isian_tabel_dasar.'"><i class="fa fa-trash fa-fw"></i></button>
                    ' ;
            })
            
        ->make(true);
    }
    public function getTahunpdrb()
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
    public function getKecamatanpdrb()
    {
        $getKecamatan=DB::select('
        select id_kecamatan,nama_kecamatan from ref_kecamatan
');
        return json_encode($getKecamatan);
    }
    public function getSektorpdrb($tahun,$kecamatan)
    {
        $getSektor=DB::select('
        select id_kolom_tabel_dasar,nama_kolom from ref_kolom_tabel_dasar 
where id_kolom_tabel_dasar not in (select id_kolom_tabel_dasar from trx_isian_data_dasar where tahun='.$tahun.' and id_kecamatan='.$kecamatan.')
 and  id_tabel_dasar=1 and level<>0
');
        return json_encode($getSektor);
    }
    public function addpdrb (Request $req)
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
    public function getEditpdrb($id)
    {
        $getListpdrb=DB::select('SELECT c.id_isian_tabel_dasar,
c.tahun,

b.nama_kolom,
ifnull(d.id_kecamatan,"-") as id_kecamatan,
b.id_kolom_tabel_dasar,
ifnull(c.nmin1,"0") as nmin1,
ifnull(c.nmin2,"0") as nmin2,
ifnull(c.nmin3,"0") as nmin3,
ifnull(c.nmin4,"0") as nmin4,
ifnull(c.nmin5,"0") as nmin5,
ifnull(c.nmin1_persen,"0") as nmin1_persen,
ifnull(c.nmin2_persen,"0") as nmin2_persen,
ifnull(c.nmin3_persen,"0") as nmin3_persen,
ifnull(c.nmin4_persen,"0") as nmin4_persen,
ifnull(c.nmin5_persen,"0") as nmin5_persen
FROM
ref_tabel_dasar AS a
INNER JOIN ref_kolom_tabel_dasar AS b ON b.id_tabel_dasar = a.id_tabel_dasar
LEFT OUTER JOIN trx_isian_data_dasar AS c ON c.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
LEFT OUTER JOIN ref_kecamatan AS d ON c.id_kecamatan = d.id_kecamatan
where a.id_tabel_dasar=1 and b.level>0 and id_isian_tabel_dasar='.$id);
        
        return DataTables::of($getListpdrb)
        ->make(true);
    }
    public function editpdrb (Request $req)
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
    public function hapuspdrb (Request $req)
    {
        TrxIsianDataDasar::where('id_isian_tabel_dasar',$req->id_isian_tabel_dasar)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }   

}