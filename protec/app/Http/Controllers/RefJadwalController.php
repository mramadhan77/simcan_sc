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
use App\Models\RefJadwal;
use App\Http\Controllers\SettingController;
use App\MenuForm;
use App\CekAkses;
use Auth;

class RefJadwalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // if(Auth::check()){ 
            return view('parameter.ref_jadwal');
        // } else {
            // return view ( 'errors.401' );
        // } 
        
    }

    public function getJadwal($tahun)
    {
        $getJadwal = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.id_langkah, a.jenis_dokumen, a.nm_langkah, c.jenis_proses, b.tahun,
                        b.id_proses, b.uraian_proses, b.tgl_mulai, b.tgl_akhir, b.status_proses,
                        b.created_at, b.updated_at,
                        CASE b.status_proses
                            WHEN 0 THEN "Belum Dilaksanakan"                           
                            WHEN 1 THEN "Proses Pelaksanaan"
                            WHEN 2 THEN "Telah Dilaksanakan"                          
                            WHEN 3 THEN "Waktu Kedaluwarsa"
                            WHEN 4 THEN "Batal Dilaksanakan"
                            ELSE "Belum Ada Data"                       
                        END AS status_display
                        FROM ref_langkah AS a
                        LEFT OUTER JOIN (SELECT * FROM ref_jadwal WHERE tahun = '.$tahun.')  AS b ON a.id_langkah = b.id_langkah
                        LEFT OUTER JOIN ref_dokumen AS c ON a.jenis_dokumen = c.id_dokumen
                        WHERE (c.jenis_proses = 0 OR c.jenis_proses = 1)
                        ORDER BY c.jenis_proses,a.id_langkah, a.jenis_dokumen ) a, 
                        (SELECT @id:=0) z');

        return DataTables::of($getJadwal)
            ->addColumn('action', function ($getJadwal) {
                return '<a class="btnEditJadwal btn btn-labeled btn-primary" data-toggle="tooltip" title="Edit"><span class="btn-label"><i class="fa fa-pencil fa-fw fa-lg"></i></span> Edit</a>';
                })
        ->make(true);
    }

    public function getTahunJadwal()
    {
        $getJadwal = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT tahun FROM ref_jadwal group by tahun ORDER BY tahun) a, (SELECT @id:=0) z');

        return DataTables::of($getJadwal)
            ->addColumn('action', function ($getJadwal) {
                return '
                    <a class="btnEditTahun btn btn-labeled btn-primary" data-toggle="tooltip" title="Edit"><span class="btn-label"><i class="fa fa-pencil fa-fw fa-lg"></i></span> Edit</a>
                    <a class="btnHapusTahun btn btn-labeled btn-danger" data-toggle="tooltip" title="Hapus"><span class="btn-label"><i class="fa fa-trash fa-fw fa-lg"></i></span> Hapus</a>
                ';})
        ->make(true);
    }

    public function addJadwal(Request $req)
    {
        $xData=$this->cekJadwal($req->tahun,$req->id_langkah,$req->jenis_proses);

        if ($xData != null){
            $data = RefJadwal::find($req->id_proses) ;
            $data->tahun = $req->tahun ;
            $data->id_langkah = $req->id_langkah ;
            $data->jenis_proses = $req->jenis_proses ;
            $data->uraian_proses = $req->uraian_proses ;
            $data->tgl_mulai = $req->tgl_mulai ;
            $data->tgl_akhir = $req->tgl_akhir ;
            $data->status_proses = $req->status_proses ;
        } else {
            $data = new RefJadwal () ;
            $data->tahun = $req->tahun ;
            $data->id_langkah = $req->id_langkah ;
            $data->jenis_proses = $req->jenis_proses ;
            $data->uraian_proses = $req->uraian_proses ;
            $data->tgl_mulai = $req->tgl_mulai ;
            $data->tgl_akhir = $req->tgl_akhir ;
            $data->status_proses = $req->status_proses ;
        }
       
        try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusJadwal(Request $req)
    {
        $data = RefJadwal::where('tahun', $req->tahun);
        try{
            $data->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function cekJadwal($id_tahun,$id_langkah,$jenis_proses){
    $cekJadwal=DB::select('SELECT COUNT(id_proses) as jml_data FROM ref_jadwal WHERE tahun ='.$id_tahun.' AND id_langkah = '.$id_langkah.' AND jenis_proses ='.$jenis_proses.' GROUP BY tahun,id_langkah,jenis_proses');
    return $cekJadwal;
    }

    public function curJadwal(){
    $curJadwal=DB::select('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.id_langkah, a.jenis_dokumen, a.nm_langkah, c.jenis_proses, b.tahun,
                        b.id_proses, b.uraian_proses, b.tgl_mulai, b.tgl_akhir, b.status_proses,
                        b.created_at, b.updated_at,                                         
                        CASE b.status_proses
                            WHEN 0 THEN "Belum Dilaksanakan"                           
                            WHEN 1 THEN "Proses Pelaksanaan"
                            WHEN 2 THEN "Telah Dilaksanakan"                          
                            WHEN 3 THEN "Waktu Kedaluwarsa"
                            WHEN 4 THEN "Batal Dilaksanakan"
                            ELSE "Kosong"                       
                        END AS status_display
                        FROM ref_langkah AS a
                        INNER JOIN ref_jadwal AS b ON a.id_langkah = b.id_langkah
                        INNER JOIN ref_dokumen AS c ON a.jenis_dokumen = c.id_dokumen
                        WHERE (c.jenis_proses = 0 OR c.jenis_proses = 1) AND (b.tgl_mulai <= now() and b.tgl_akhir >= now()) ) a, 
                        (SELECT @id:=0) z');
    return $curJadwal;
    }

    public function tlJadwal($tahun)
    {
        $getJadwal = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.id_langkah, a.jenis_dokumen, a.nm_langkah, c.jenis_proses, b.tahun,
                        b.id_proses, b.uraian_proses, b.tgl_mulai, b.tgl_akhir, b.status_proses,
                        b.created_at, b.updated_at,
                        CASE b.status_proses
                            WHEN 0 THEN "Belum Dilaksanakan"                           
                            WHEN 1 THEN "Proses Pelaksanaan"
                            WHEN 2 THEN "Telah Dilaksanakan"                          
                            WHEN 3 THEN "Waktu Kedaluwarsa"
                            WHEN 4 THEN "Batal Dilaksanakan"
                            ELSE "Belum Ada Data"                       
                        END AS status_display,                        
                        CASE WHEN b.tgl_mulai <= Now() AND b.tgl_akhir >= Now() THEN "fa fa-check-circle fa-fw fa-lg text-success"
                            WHEN b.tgl_mulai < Now() AND b.tgl_akhir < Now() THEN "fa fa-times-circle fa-fw fa-lg text-danger"
                            WHEN b.tgl_mulai > Now() AND b.tgl_akhir > Now() THEN "fa fa-info-circle fa-fw fa-lg text-info"
                        END AS status_real
                        FROM ref_langkah AS a
                        LEFT OUTER JOIN (SELECT * FROM ref_jadwal WHERE tahun = '.$tahun.')  AS b ON a.id_langkah = b.id_langkah
                        LEFT OUTER JOIN ref_dokumen AS c ON a.jenis_dokumen = c.id_dokumen
                        WHERE (c.jenis_proses = 0 OR c.jenis_proses = 1)
                        ORDER BY b.tgl_mulai, c.jenis_proses,a.id_langkah, a.jenis_dokumen ) a, 
                        (SELECT @id:=0) z');

        return json_encode($getJadwal);
    }

    public function getTahunSetting(){
        $tahun=DB::select('SELECT a.tahun_rencana FROM ref_setting AS a ORDER BY a.tahun_rencana LIMIT 2');
        if($tahun == null){
                $tahun=date('Y');
                return json_encode($tahun); 
            } else {
                return json_encode($tahun); 
            }
        
    }  
    
    public function putTahunSetting(Request $request){  
        if (Session::has('tahun')) {
            Session::forget('tahun');
            Session::put('tahun',$request->tahun_rencana); 
        } else {
            Session::put('tahun',$request->tahun_rencana); 
        };
    }

}
