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
use App\MenuForm;
use Auth;
use App\Models\RefSetting;


class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    } 

    public function hashPemda($data)
    {
        $xHash=hash('sha256','M4h4th1r4rkh4n4th1ef');
        $result=openssl_encrypt($data,"AES-128-ECB",$xHash);
        $result=base64_encode($result);
        return $result;
    }

    public function dehashPemda($data)
    {
        $xHash=hash('sha256','M4h4th1r4rkh4n4th1ef');        
        $result=openssl_decrypt(base64_decode($data),"AES-128-ECB",$xHash); 
        return $result;
    }

    public function dePemda()
    {
        $nav = require(base_path().'/config/menu.php');
        $xPemda = $nav['nav'];
        $xHash=hash('sha256','M4h4th1r4rkh4n4th1ef');
        

        $test = new MenuForm;
        $json = $test->reveal($nav['state']);

        if ($json == 'prod') {
           $result=openssl_decrypt(base64_decode($xPemda),"AES-128-ECB",$xHash); 
        } else {
           $result="SIMULASI 99.99";  
        }

        return $result;
    }

    public function getState()
    {
        $state = require(base_path().'/config/menu.php');

        $test = new MenuForm;
        $result=$test->reveal($state["state"]);

        if ($result =='demo'){
          $getState = 0;
        } else {
          $getState = 1;
        }
        return json_encode($getState);
    }

    public function index()
    {
        // if(Auth::check()){ 
            return view('parameter.ref_setting');
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function getListSetting()
    {
        $getListSetting=DB::select('SELECT (@id:=@id+1) as no_urut, a.tahun_rencana, a.jenis_rw, a.jml_rw, a.pagu_rw, 
                    a.jenis_desa, a.jml_desa, a.pagu_desa, a.jenis_kecamatan, a.jml_kecamatan, a.pagu_kecamatan, a.status_setting,a.deviasi_pagu,
                    CASE a.status_setting
                        WHEN 0 THEN "fa fa-question fa-lg fa-fw text-warning"
                        WHEN 1 THEN "fa fa-check-square-o fa-lg fa-fw text-success"
                        WHEN 2 THEN "fa fa-ban fa-lg fa-fw text-danger"
                    END AS status_icon 
                    FROM ref_setting AS a, 
                    (SELECT @id:=0) x');

        return DataTables::of($getListSetting)
            ->addColumn('action',function($getListSetting){
                if($getListSetting->status_setting != 2){
                    return '
                        <button id="btnEditSetting" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Setting" title="Edit Setting" class="btn btn-success btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusSetting" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Setting" title="Hapus Setting" class="btn btn-danger btn-sm"><i class="fa fa-trash-o fa-fw"></i></button>
                        <button id="btnAktivSetting" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Aktivasi Setting" title="Aktivasi Setting" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i></button>
                    ' ;}
            })
        ->make(true);
    }

    public function addSetting (Request $req)
    {
        $data = new RefSetting ();
        $data->tahun_rencana= $req->tahun_rencana;
        $data->jenis_rw= $req->jenis_rw;
        $data->jml_rw= $req->jml_rw;
        $data->pagu_rw= $req->pagu_rw;
        $data->jenis_desa= $req->jenis_desa;
        $data->jml_desa= $req->jml_desa;
        $data->pagu_desa= $req->pagu_desa;
        $data->jenis_kecamatan= $req->jenis_kecamatan;
        $data->jml_kecamatan= $req->jml_kecamatan;
        $data->pagu_kecamatan= $req->pagu_kecamatan;
        $data->deviasi_pagu= $req->deviasi_pagu;
        $data->status_setting= 0;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editSetting (Request $req)
    {
        $data = RefSetting::find($req->tahun_rencana) ;
        $data->jenis_rw= $req->jenis_rw;
        $data->jml_rw= $req->jml_rw;
        $data->pagu_rw= $req->pagu_rw;
        $data->jenis_desa= $req->jenis_desa;
        $data->jml_desa= $req->jml_desa;
        $data->pagu_desa= $req->pagu_desa;
        $data->jenis_kecamatan= $req->jenis_kecamatan;
        $data->jml_kecamatan= $req->jml_kecamatan;
        $data->pagu_kecamatan= $req->pagu_kecamatan;
        $data->deviasi_pagu= $req->deviasi_pagu;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusSetting (Request $req)
    {
        RefSetting::where('tahun_rencana',$req->tahun_rencana)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }

    public function postSetting (Request $req)
    {
        $cek=$this->cekAktif();

        $data = RefSetting::find($req->tahun_rencana) ;
        $data->status_setting= $req->status_setting;

        if($cek[0]->posting > 1){
            return response ()->json (['pesan'=>'Data Gagal Posting, Jumlah Yang Aktif lebih dari 2 tahun','status_pesan'=>'0']);
        } else {
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Posting','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Posting ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }

    public function cekAktif(){
            $cekAktif=DB::select('SELECT COALESCE(COUNT(a.tahun_rencana),0) as posting FROM ref_setting AS a where a.status_setting = 1');
    return $cekAktif;
    }

    
}
