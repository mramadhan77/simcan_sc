<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

use App\Http\Requests;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\CekAkses;
use Auth;
use App\Models\RefUnit;
use App\Models\RefSubUnit;
use App\Models\RefDataSubUnit;

class RefUnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function index(){
        // if(Auth::check()){ 
            return view('parameter.ref_unit');
        // } else {
            // return view ( 'errors.401' );
        // } 
        
    }

    public function getListUrusan()
    {
        $getListUrusan=DB::select('SELECT (@id:=@id+1) as no_urut, kd_urusan, nm_urusan FROM ref_urusan, (SELECT @id:=0) x');

        return DataTables::of($getListUrusan)
        ->addColumn('details_url', function($getListUrusan) {
                return url('admin/parameter/unit/getListBidang/' . $getListUrusan->kd_urusan);
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

    public function getListUnit($id_bidang)
    {
        $getListUnit=DB::select('SELECT (@id:=@id+1) as no_urut, c.id_unit, c.id_bidang, c.kd_unit, c.nm_unit, 
                    b.nm_bidang, a.nm_urusan
                    FROM ref_unit c
                    INNER JOIN ref_bidang b ON c.id_bidang=b.id_bidang
                    INNER JOIN ref_urusan a ON b.kd_urusan=a.kd_urusan, (SELECT @id:=0) x WHERE c.id_bidang='.$id_bidang);

        return DataTables::of($getListUnit)
        ->addColumn('action',function($getListUnit){
                    return '
                        <button id="btnEditUnit" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Unit" title="Edit Unit" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusUnit" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Unit" title="Hapus Unit" class="btn btn-danger btn-sm"><i class="fa fa-trash fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }

    public function getListSubUnit($id_unit)
    {
        $getListDesa=DB::select('SELECT (@id:=@id+1) as no_urut, id_sub_unit, id_unit, kd_sub, nm_sub
            FROM ref_sub_unit, (SELECT @id:=0) x WHERE id_unit='.$id_unit);

        return DataTables::of($getListDesa)
            ->addColumn('action',function($getListDesa){
                    return '
                        <button id="btnEditSubUnit" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Sub Unit" title="Edit Sub Unit" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusSubUnit" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Sub Unit" title="Hapus Sub Unit" class="btn btn-danger btn-sm"><i class="fa fa-trash fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }

    public function getListDataSubUnit($id_unit)
    {
        $getListDesa=DB::select('SELECT (@id:=@id+1) as no_urut, tahun,id_rincian_unit, id_sub_unit, alamat_sub_unit, 
            kota_sub_unit, nama_jabatan_pimpinan_skpd, nama_pimpinan_skpd, nip_pimpinan_skpd
            FROM ref_data_sub_unit, (SELECT @id:=0) x WHERE id_sub_unit='.$id_unit);

        return DataTables::of($getListDesa)
            ->addColumn('action',function($getListDesa){
                    return '
                        <button id="btnEditDataUnit" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Data Rincian Sub Unit" title="Edit Data Rincian Sub Unit" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusDataUnit" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Data Rincian Sub Unit" title="Hapus Data Rincian Sub Unit" class="btn btn-danger btn-sm"><i class="fa fa-trash fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }


    public function addUnit (Request $req)
    {
        $bidang = DB::SELECT('SELECT * FROM ref_bidang WHERE id_bidang='.$req->id_bidang);
        
        $data = new RefUnit();
        $data->id_bidang= $req->id_bidang;
        $data->kd_unit= $req->kd_unit;
        $data->nm_unit= $req->nm_unit;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editUnit (Request $req)
    {
        $data = RefUnit::find($req->id_unit) ;
        $data->id_bidang= $req->id_bidang;
        $data->kd_unit= $req->kd_unit;
        $data->nm_unit= $req->nm_unit;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusUnit (Request $req)
    {
        RefUnit::where('id_unit',$req->id_unit)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    } 

    public function addSubUnit (Request $req)
    {
        $data = new RefSubUnit();
        $data->id_unit= $req->id_unit;
        $data->kd_sub= $req->kd_sub;
        $data->nm_sub= $req->nm_sub;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editSubUnit (Request $req)
    {
        $data = RefSubUnit::find($req->id_sub_unit) ;
        $data->id_unit= $req->id_unit;
        $data->kd_sub= $req->kd_sub;
        $data->nm_sub= $req->nm_sub;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusSubUnit (Request $req)
    {
        RefSubUnit::where('id_sub_unit',$req->id_sub_unit)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }

    public function addDataSubUnit (Request $req)
    {
        $data = new RefDataSubUnit();
        $data->id_sub_unit= $req->id_sub_unit;
        $data->tahun= $req->tahun;
        $data->nama_jabatan_pimpinan_skpd= $req->nama_jabatan_pimpinan_skpd;
        $data->nip_pimpinan_skpd= $req->nip_pimpinan_skpd;
        $data->nama_pimpinan_skpd= $req->nama_pimpinan_skpd;
        $data->alamat_sub_unit= $req->alamat_sub_unit;
        $data->kota_sub_unit= $req->kota_sub_unit;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editDataSubUnit (Request $req)
    {
        $data = RefDataSubUnit::find($req->id_rincian_unit) ;
        $data->id_sub_unit= $req->id_sub_unit;
        $data->tahun= $req->tahun;
        $data->nama_jabatan_pimpinan_skpd= $req->nama_jabatan_pimpinan_skpd;
        $data->nip_pimpinan_skpd= $req->nip_pimpinan_skpd;
        $data->nama_pimpinan_skpd= $req->nama_pimpinan_skpd;
        $data->alamat_sub_unit= $req->alamat_sub_unit;
        $data->kota_sub_unit= $req->kota_sub_unit;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusDataSubUnit (Request $req)
    {
        RefDataSubUnit::where('id_rincian_unit',$req->id_rincian_unit)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }   

    public function TestApi (Request $req)
    {
        $bidang = DB::SELECT('SELECT * FROM ref_bidang WHERE id_bidang='.$req->id_bidang);
        
        $client = new Client(['headers' => ['content-type' => 'application/json','Accept' => 'application/json']]);
        $response = $client->get('http://localhost/simdaApiKeu/public/api/getUnit');
        // [   'headers' => ['content-type' => 'application/json','Accept' => 'application/json'],
        //     'json' => [  
        //         'kd_unit' => $req->kd_unit,
        //         'kd_bidang' => $bidang[0]->kd_bidang,
        //         'kd_urusan' => $bidang[0]->kd_urusan,
        //         'nm_unit' => $req->nm_unit,
        //     ], 
        // ]);
        return json_encode($response->getBody());
        // $response = $client->Request('POST','localhost/simdaApiKeu/public/api/TmbUnit',
        // [
        //         'form_params' => [  
        //                 'kd_unit' => $req->kd_unit,
        //                 'kd_bidang' => $bidang[0]->kd_bidang,
        //                 'kd_urusan' => $bidang[0]->kd_urusan,
        //                 'nm_unit' => $req->nm_unit,
        //         ],                
        //         // 'exceptions' => false
        // ]);

        
    }

}