<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

use App\Http\Requests;
use Illuminate\Support\Facades\File;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\CekAkses;
use Auth;
use App\Models\RefIndikator;

class RefIndikatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
	public function index(){
        return view('parameter.ref_indikator');        
    }

    public function getListIndikator()
    {
        $getListIndikator=DB::select('SELECT (@id:=@id+1) as no_urut, a.id_indikator,a.kualitas_indikator,
                    a.jenis_indikator, a.sifat_indikator, a.nm_indikator, a.flag_iku,
                    a.asal_indikator, a.sumber_data_indikator,a.type_indikator,a.id_satuan_output,
                    a.id_bidang, a.id_aspek,
                    CASE a.type_indikator
                        WHEN 1 THEN "Hasil"
                        WHEN 0 THEN "Keluaran"
                        WHEN 2 THEN "Dampak"
                        WHEN 3 THEN "Masukan"
                    END AS type_display,
                    CASE a.jenis_indikator
                        WHEN 1 THEN "Positif"
                        WHEN 0 THEN "Negatif"
                    END AS sifat_display,
                    CASE a.sifat_indikator
                        WHEN 0 THEN "Incremental"
                        WHEN 1 THEN "Absolut"
                        WHEN 2 THEN "Komulatif"
                    END AS teknik_display,
                    CASE a.kualitas_indikator
                        WHEN 0 THEN "Kualitas"
                        WHEN 1 THEN "Kuantitas"
                        WHEN 2 THEN "Persentase"
                        WHEN 3 THEN "Rata-Rata"
                        WHEN 4 THEN "Rasio"
                    END AS kualitas_display
                    FROM
                    ref_indikator AS a, 
                    (SELECT @id:=0) x');

        return DataTables::of($getListIndikator)
            ->addColumn('action',function($getListIndikator){
                    return '
                        <button id="btnEditIndikator" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Indikator" title="Edit Indikator" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusIndikator" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Indikator" title="Hapus Indikator" class="btn btn-danger btn-sm"><i class="fa fa-trash-o fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }

    public function addIndikator (Request $req)
    {
        $data = new RefIndikator();
        $data->jenis_indikator= $req->jenis_indikator;
        $data->nm_indikator= $req->nm_indikator;
        $data->sifat_indikator= $req->sifat_indikator;
        $data->type_indikator= $req->type_indikator;
        $data->kualitas_indikator= $req->kualitas_indikator;
        $data->asal_indikator= 9;
        $data->sumber_data_indikator=$req->sumber_data_indikator;
        $data->id_satuan_output=$req->id_satuan_output;
        $data->id_bidang=$req->id_bidang;
        $data->id_aspek=$req->id_aspek;
        $data->nama_file=null;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editIndikator (Request $req)
    {
        // $file = $req->file('metode_hitung');
        // $ext = $file->getClientOriginalExtension();
        // $newName = rand(100000,1001238912).".".$ext;
        // $file->move('storage/imgs',$newName);

        $data = RefIndikator::find($req->id_indikator) ;
        $data->jenis_indikator= $req->jenis_indikator;
        $data->sifat_indikator= $req->sifat_indikator;
        $data->type_indikator= $req->type_indikator;
        $data->nm_indikator= $req->nm_indikator;
        $data->kualitas_indikator= $req->kualitas_indikator;
        $data->sumber_data_indikator=$req->sumber_data_indikator;
        $data->id_satuan_output=$req->id_satuan_output;
        $data->id_bidang=$req->id_bidang;
        $data->id_aspek=$req->id_aspek;
        $data->nama_file=null;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusIndikator (Request $req)
    {
        RefIndikator::where('id_indikator',$req->id_indikator)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }   

}