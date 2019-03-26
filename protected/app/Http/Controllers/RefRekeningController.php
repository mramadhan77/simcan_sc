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
use App\Models\RefRek4;
use App\Models\RefRek5;

class RefRekeningController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function index(){

        // if(Auth::check()){ 
            return view('parameter.ref_rekening');
        // } else {
            // return view ( 'errors.401' );
        // } 
    }

    public function getListAkun()
    {
        $getListAkun=DB::select('SELECT (@id:=@id+1) as no_urut, kd_rek_1, nama_kd_rek_1 FROM ref_rek_1, (SELECT @id:=0) x');

        return DataTables::of($getListAkun)
        ->make(true);
    }

    public function getListGolongan($id_akun)
    {
        $getListGolongan=DB::select('SELECT (@id:=@id+1) as no_urut, b.kd_rek_1, b.kd_rek_2, b.nama_kd_rek_2, a.nama_kd_rek_1
                FROM ref_rek_2 b
                INNER JOIN ref_rek_1 a ON b.kd_rek_1=a.kd_rek_1, (SELECT @id:=0) x WHERE b.kd_rek_1='.$id_akun);

        return DataTables::of($getListGolongan)
        ->make(true);
    }

    public function getListJenis($id_akun,$id_golongan)
    {
        $getListJenis=DB::select('SELECT (@id:=@id+1) as no_urut, c.kd_rek_1, c.kd_rek_2, c.kd_rek_3, c.nama_kd_rek_3, 
                b.nama_kd_rek_2, a.nama_kd_rek_1
                FROM ref_rek_3 c
                INNER JOIN ref_rek_2 b ON c.kd_rek_1 = b.kd_rek_1 and c.kd_rek_2 = b.kd_rek_2
                INNER JOIN ref_rek_1 a ON b.kd_rek_1=a.kd_rek_1, 
                (SELECT @id:=0) x WHERE c.kd_rek_1='.$id_akun.' AND c.kd_rek_2='.$id_golongan);

        return DataTables::of($getListJenis)
        ->make(true);
    }

    public function getListObyek($id_akun,$id_golongan,$id_jenis)
    {
        $getListObyek=DB::select('SELECT (@id:=@id+1) as no_urut, d.kd_rek_1, d.kd_rek_2, d.kd_rek_3, d.kd_rek_4, d.nama_kd_rek_4,
                c.nama_kd_rek_3, b.nama_kd_rek_2, a.nama_kd_rek_1
                FROM ref_rek_4 d
                INNER JOIN ref_rek_3 c ON d.kd_rek_1 = c.kd_rek_1 and d.kd_rek_2 = c.kd_rek_2 and d.kd_rek_3= c.kd_rek_3
                INNER JOIN ref_rek_2 b ON c.kd_rek_1 = b.kd_rek_1 and c.kd_rek_2 = b.kd_rek_2
                INNER JOIN ref_rek_1 a ON b.kd_rek_1=a.kd_rek_1, (SELECT @id:=0) x 
                WHERE d.kd_rek_1='.$id_akun.' AND d.kd_rek_2='.$id_golongan.' AND d.kd_rek_3='.$id_jenis);

        return DataTables::of($getListObyek)
            ->addColumn('action',function($getListObyek){
                    return '
                        <button id="btnEditObyek" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Obyek" title="Edit Obyek" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusObyek" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Obyek" title="Hapus Obyek" class="btn btn-danger btn-sm"><i class="fa fa-trash fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }

    public function getListRincian($id_akun,$id_golongan,$id_jenis,$id_obyek)
    {
        $getListRincian=DB::select('SELECT (@id:=@id+1) as no_urut, e.id_rekening, e.kd_rek_1, e.kd_rek_2, e.kd_rek_3, 
                    e.kd_rek_4, e.kd_rek_5, e.nama_kd_rek_5, d.nama_kd_rek_4, c.nama_kd_rek_3, b.nama_kd_rek_2, a.nama_kd_rek_1
                    FROM ref_rek_5 e
                    INNER JOIN ref_rek_4 d ON e.kd_rek_1 = d.kd_rek_1 and e.kd_rek_2 = d.kd_rek_2 and e.kd_rek_3= d.kd_rek_3 and e.kd_rek_4 = d.kd_rek_4
                    INNER JOIN ref_rek_3 c ON d.kd_rek_1 = c.kd_rek_1 and d.kd_rek_2 = c.kd_rek_2 and d.kd_rek_3= c.kd_rek_3
                    INNER JOIN ref_rek_2 b ON c.kd_rek_1 = b.kd_rek_1 and c.kd_rek_2 = b.kd_rek_2
                    INNER JOIN ref_rek_1 a ON b.kd_rek_1 = a.kd_rek_1, 
                    (SELECT @id:=0) x WHERE e.kd_rek_1='.$id_akun.' AND e.kd_rek_2='.$id_golongan.' AND e.kd_rek_3='.$id_jenis.' AND e.kd_rek_4='.$id_obyek);

        return DataTables::of($getListRincian)
            ->addColumn('action',function($getListRincian){
                    return '
                        <button id="btnEditRincian" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Rincian Obyek" title="Edit Rincian Obyek" class="btn btn-warning btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusRincian" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Rincian Obyek" title="Hapus Rincian Obyek" class="btn btn-danger btn-sm"><i class="fa fa-trash fa-fw"></i></button>
                    ' ;
            })
        ->make(true);
    }


    public function addRek4 (Request $req)
    {
        if($req->kd_rek_1 == 5 && $req->kd_rek_2 == 2 && $req->kd_rek_3 == 3){
            return response ()->json (['pesan'=>'Maaf Rekening Belanja Modal tidak bisa ditambahkan','status_pesan'=>'0']);
        } else {
                try{
                    $result=DB::INSERT('INSERT INTO ref_rek_4 (kd_rek_1, kd_rek_2, kd_rek_3, kd_rek_4, nama_kd_rek_4)
                        VALUES ('.$req->kd_rek_1.', '.$req->kd_rek_2.', '.$req->kd_rek_3.', '.$req->kd_rek_4.', "'.$req->nama_kd_rek_4.'")');
                    if ($result!=0){
                        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);  
                    }           
        
                }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                }
        }
    }

    public function editRek4 (Request $req)
    {
        if($req->kd_rek_1 == 5 && $req->kd_rek_2 == 2 && $req->kd_rek_3 == 3){
            return response ()->json (['pesan'=>'Maaf Rekening Belanja Modal tidak bisa diubah','status_pesan'=>'0']);
        } else {
            try{            
                $result = DB::UPDATE('UPDATE ref_rek_4
                SET kd_rek_1='.$req->kd_rek_1.',
                    kd_rek_2='.$req->kd_rek_2.',
                    kd_rek_3='.$req->kd_rek_3.',
                    kd_rek_4='.$req->kd_rek_4.',
                    nama_kd_rek_4="'.$req->nama_kd_rek_4.'" WHERE 
                    kd_rek_1='.$req->kd_rek_1.' AND kd_rek_2='.$req->kd_rek_2.' AND kd_rek_3='.$req->kd_rek_3.' 
                    AND kd_rek_4='.$req->kd_rek_4a.'');

                if ($result!=0){
                        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);  
                    }
            } 
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }

    public function hapusRek4 (Request $req)
    {
        if($req->kd_rek_1 == 5 && $req->kd_rek_2 == 2 && $req->kd_rek_3 == 3){
            return response ()->json (['pesan'=>'Maaf Rekening Belanja Modal tidak bisa dihapus','status_pesan'=>'0']);
        } else {
            try{
                $result = DB::DELETE('DELETE FROM ref_rek_4 WHERE 
                        kd_rek_1='.$req->kd_rek_1.' AND kd_rek_2='.$req->kd_rek_2.' AND kd_rek_3='.$req->kd_rek_3.' 
                        AND kd_rek_4='.$req->kd_rek_4.'');

                if ($result!=0){
                            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
                        } else {
                            return response ()->json (['pesan'=>'Data Gagal dihapus ('.$error_code.')','status_pesan'=>'0']);  
                        }
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal dihapus ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }  

    public function addRek5 (Request $req)
    {
        $data = new RefRek5();
        $data->kd_rek_1= $req->kd_rek_1;
        $data->kd_rek_2= $req->kd_rek_2;
        $data->kd_rek_3= $req->kd_rek_3;
        $data->kd_rek_4= $req->kd_rek_4;
        $data->kd_rek_5= $req->kd_rek_5;
        $data->nama_kd_rek_5= $req->nama_kd_rek_5;

        if($req->kd_rek_1 == 5 && $req->kd_rek_2 == 2 && $req->kd_rek_3 == 3){
            return response ()->json (['pesan'=>'Maaf Rekening Belanja Modal tidak bisa ditambahkan','status_pesan'=>'0']);
        } else {
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }

    public function editRek5 (Request $req)
    {
        $data = RefRek5::find($req->id_rekening);
        $data->kd_rek_1= $req->kd_rek_1;
        $data->kd_rek_2= $req->kd_rek_2;
        $data->kd_rek_3= $req->kd_rek_3;
        $data->kd_rek_4= $req->kd_rek_4;
        $data->kd_rek_5= $req->kd_rek_5;
        $data->nama_kd_rek_5= $req->nama_kd_rek_5;

        $cek = DB::SELECT('SELECT * FROM ref_rek_5 WHERE id_rekening='.$req->id_rekening);

        if($cek[0]->kd_rek_1 == 5 && $cek[0]->kd_rek_2 == 2 && $cek[0]->kd_rek_3 == 3){
            return response ()->json (['pesan'=>'Maaf Rekening Belanja Modal tidak bisa dihapus','status_pesan'=>'0']);
        } else {
            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }

    public function hapusRek5 (Request $req)
    {
        $cek = DB::SELECT('SELECT * FROM ref_rek_5 WHERE id_rekening='.$req->id_rekening);

        if($cek[0]->kd_rek_1 == 5 && $cek[0]->kd_rek_2 == 2 && $cek[0]->kd_rek_3 == 3){
            return response ()->json (['pesan'=>'Maaf Rekening Belanja Modal tidak bisa dihapus','status_pesan'=>'0']);
        } else {
            RefRek5::where('id_rekening',$req->id_rekening)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil dihapus']);
        }
    }   

}