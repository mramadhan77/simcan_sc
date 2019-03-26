<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use DB;
use Response;
use Session;
use Auth;
use App\Models\RefSshGolongan;
use App\Models\RefSshKelompok;
use App\Models\RefSshSubKelompok;
use App\Models\RefSshTarif;
use App\Models\RefSshRekening;
use App\Models\RefRek5;
use App\Models\RefSatuan;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;



class RefSshController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

    public function index()
    {
      
        if(Session::has('tahun')){ 
            return view('ssh.index');
          } else {
            return redirect('home');
        }
    }

    public function getRefSatuan()
    {
      $refsatuan=DB::select('SELECT id_satuan,uraian_satuan,singkatan_satuan FROM ref_satuan Order By uraian_satuan');
      return json_encode($refsatuan);
    }

    public function getCariRekening()
    {
      $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
            kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 where kd_rek_1=5 and kd_rek_2=2) b, (SELECT @id:=0) a');

      return DataTables::of($refrekening)
      ->make(true);
    }

    public function getGolongan()
    {
      $refgolongan = DB::select('SELECT id_golongan_ssh, no_urut, uraian_golongan_ssh FROM ref_ssh_golongan ORDER BY no_urut asc');

      return DataTables::of($refgolongan)
        ->addColumn('action', function ($refgolongan) {
            return '
            <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a class="edit-golongan dropdown-item" data-id_gol_edit="'.$refgolongan->id_golongan_ssh.'" data-no_urut_gol_edit="'.$refgolongan->no_urut.'" data-ur_gol_edit="'.$refgolongan->uraian_golongan_ssh.'"><i class="glyphicon glyphicon-edit"></i> Ubah Golongan</a>
                  </li>
                  <li>
                    <a class="delete-golongan dropdown-item" data-id_gol_hapus="'.$refgolongan->id_golongan_ssh.'" data-ur_gol_hapus="'.$refgolongan->uraian_golongan_ssh.'"><i class="glyphicon glyphicon-trash"></i> Hapus Golongan</a>
                  </li>                         
                </ul>
            </div>
            ';})
        ->make(true);
    }

    public function getKelompok($id_golongan_ssh)
    {
       $sshkelompok = DB::select('SELECT b.no_urut, a.id_golongan_ssh,a.no_urut as no_urut_gol, a.uraian_golongan_ssh, b.id_kelompok_ssh, b.uraian_kelompok_ssh FROM ref_ssh_kelompok b
          INNER JOIN ref_ssh_golongan a ON b.id_golongan_ssh = a.id_golongan_ssh WHERE b.id_golongan_ssh='.$id_golongan_ssh.' ORDER BY a.no_urut asc, b.no_urut asc');

      return DataTables::of($sshkelompok)
                  ->addColumn('action', function ($sshkelompok) {
                      return '
                      <div class="btn-group">
                      <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                          <a class="edit-kelompok dropdown-item" data-id_gol_kel="'.$sshkelompok->id_golongan_ssh.'" data-no_urut_kel="'.$sshkelompok->no_urut.'" data-id_kel="'.$sshkelompok->id_kelompok_ssh.'" data-ur_kel="'.$sshkelompok->uraian_kelompok_ssh.'"><i class="glyphicon glyphicon-edit"></i> Ubah Kelompok</a>
                        </li>
                        <li>
                          <a class="delete-kelompok dropdown-item" data-id_gol_kel_hapus="'.$sshkelompok->id_golongan_ssh.'" data-id_kel_hapus="'.$sshkelompok->id_kelompok_ssh.'" data-ur_kel_hapus="'.$sshkelompok->uraian_kelompok_ssh.'"><i class="glyphicon glyphicon-trash"></i> Hapus Kelompok</a>
                        </li>                         
                      </ul>
                      </div>
            
                    ';})
                  ->make(true);
    }

    public function getSubKelompok($id_kelompok_ssh)
    {
       $sshsubkelompok = DB::select('SELECT c.id_sub_kelompok_ssh, c.no_urut, c.uraian_sub_kelompok_ssh,  a.id_golongan_ssh,a.no_urut as no_urut_gol, a.uraian_golongan_ssh, b.id_kelompok_ssh, b.no_urut as no_urut_kel, b.uraian_kelompok_ssh 
          FROM ref_ssh_sub_kelompok c
          INNER JOIN ref_ssh_kelompok b ON c.id_kelompok_ssh = b.id_kelompok_ssh
          INNER JOIN ref_ssh_golongan a ON b.id_golongan_ssh = a.id_golongan_ssh 
          WHERE c.id_kelompok_ssh='.$id_kelompok_ssh.' ORDER BY a.no_urut asc, b.no_urut asc, c.no_urut asc');
                  

      return DataTables::of($sshsubkelompok)
                  ->addColumn('action', function ($sshsubkelompok) {
                      return '
                      <div class="btn-group">
                      <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                          <a class="edit-subkelompok dropdown-item" data-id_gol_sub="'.$sshsubkelompok->id_golongan_ssh.'" data-id_kel_sub="'.$sshsubkelompok->id_kelompok_ssh.'" data-id_sub="'.$sshsubkelompok->id_sub_kelompok_ssh.'" data-no_urut_sub="'.$sshsubkelompok->no_urut.'" data-ur_sub="'.$sshsubkelompok->uraian_sub_kelompok_ssh.'"><i class="glyphicon glyphicon-edit"></i> Ubah Sub Kelompok</a>
                        </li>
                        <li>
                          <a class="delete-subkelompok dropdown-item" data-id_kel_sub="'.$sshsubkelompok->id_kelompok_ssh.'" data-id_sub="'.$sshsubkelompok->id_sub_kelompok_ssh.'" data-ur_sub="'.$sshsubkelompok->uraian_sub_kelompok_ssh.'"><i class="glyphicon glyphicon-trash"></i> Hapus Sub Kelompok</a>
                        </li>                         
                      </ul>
                      </div>
                      ';})
                      ->make(true);
    }

    public function getTarif($id_sub_kelompok_ssh)
    {
       $sshtarif = DB::select('SELECT d.id_tarif_ssh,d.no_urut, d.uraian_tarif_ssh,d.id_satuan,c.id_sub_kelompok_ssh, c.no_urut as no_urut_skel, 
          c.uraian_sub_kelompok_ssh,  a.id_golongan_ssh,a.no_urut as no_urut_gol, a.uraian_golongan_ssh, b.id_kelompok_ssh, b.no_urut as no_urut_kel, 
          b.uraian_kelompok_ssh, d.keterangan_tarif_ssh, d.status_data 
          FROM ref_ssh_tarif d 
          INNER JOIN ref_ssh_sub_kelompok c ON d.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh 
          INNER JOIN ref_ssh_kelompok b ON c.id_kelompok_ssh = b.id_kelompok_ssh 
          INNER JOIN ref_ssh_golongan a ON b.id_golongan_ssh = a.id_golongan_ssh 
          WHERE d.id_sub_kelompok_ssh='.$id_sub_kelompok_ssh.' ORDER BY a.no_urut asc, b.no_urut asc, c.no_urut asc, d.no_urut asc');

      return DataTables::of($sshtarif)
                  ->addColumn('action', function ($sshtarif) {
                    return '
                      <div class="btn-group">
                      <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                          <a class="edit-tarif dropdown-item" data-id_gol_item="'.$sshtarif->id_golongan_ssh.'" data-id_kel_item="'.$sshtarif->id_kelompok_ssh.'" data-id_sub_item="'.$sshtarif->id_sub_kelompok_ssh.'" data-id_item="'.$sshtarif->id_tarif_ssh.'" data-no_urut_item="'.$sshtarif->no_urut.'" data-id_satuan="'.$sshtarif->id_satuan.'" data-ur_item="'.$sshtarif->uraian_tarif_ssh.'"><i class="glyphicon glyphicon-edit"></i> Ubah Item</a>
                        </li>
                        <li>
                          <a class="delete-tarif dropdown-item" data-id_item="'.$sshtarif->id_tarif_ssh.'" data-ur_item="'.$sshtarif->uraian_tarif_ssh.'"><i class="glyphicon glyphicon-trash"></i> Hapus Item</a>
                        </li>                         
                      </ul>
                      </div>
                      ';})
                  ->make(true);
    }

    public function getRekening($id_tarif_ssh)
    {
       $sshrekening = DB::select('SELECT e.id_rekening_ssh, e.id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",kd_rek_3,".",kd_rek_4,".",kd_rek_5) as kd_rekening, f.nama_kd_rek_5 as ur_rekening ,e.uraian_tarif_ssh,d.id_tarif_ssh,d.no_urut as no_urut_tarif, d.uraian_tarif_ssh,d.id_satuan,c.id_sub_kelompok_ssh, c.no_urut as no_urut_skel, c.uraian_sub_kelompok_ssh,  a.id_golongan_ssh,a.no_urut as no_urut_gol, a.uraian_golongan_ssh, b.id_kelompok_ssh, b.no_urut as no_urut_kel, b.uraian_kelompok_ssh 
          FROM ref_ssh_rekening e
          INNER JOIN ref_rek_5 f ON e.id_rekening = f.id_rekening
          INNER JOIN ref_ssh_tarif d ON e.id_tarif_ssh = d.id_tarif_ssh
          INNER JOIN ref_ssh_sub_kelompok c ON d.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh
          INNER JOIN ref_ssh_kelompok b ON c.id_kelompok_ssh = b.id_kelompok_ssh
          INNER JOIN ref_ssh_golongan a ON b.id_golongan_ssh = a.id_golongan_ssh 
          WHERE e.id_tarif_ssh='.$id_tarif_ssh.' ORDER BY a.no_urut asc, b.no_urut asc, c.no_urut asc, d.no_urut asc');

      return DataTables::of($sshrekening)
          ->addColumn('action', function ($sshrekening) {
            return '
              <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a class="edit-rekening dropdown-item" data-id_gol_item="'.$sshrekening->id_golongan_ssh.'" data-id_kel_item="'.$sshrekening->id_kelompok_ssh.'" data-id_sub_item="'.$sshrekening->id_sub_kelompok_ssh.'" data-id_item="'.$sshrekening->id_tarif_ssh.'" data-id_rekening_edit="'.$sshrekening->id_rekening.'" data-id_rek_edit="'.$sshrekening->id_rekening_ssh.'" data-kd_rekening="'.$sshrekening->kd_rekening.'" data-ur_rekening="'.$sshrekening->ur_rekening.'"><i class="glyphicon glyphicon-edit"></i> Ubah Rekening</a>
                  </li>
                  <li>
                    <a class="delete-rekening dropdown-item" data-id_rek_hapus="'.$sshrekening->id_rekening_ssh.'" data-ur_rekening="'.$sshrekening->ur_rekening.'"><i class="glyphicon glyphicon-trash"></i> Hapus Rekening</a>
                  </li>                         
                </ul>
              </div>
              ';})
            ->make(true);
    }

    public function addGolongan(Request $req)
     {
         try{$data = new RefSshGolongan ();
         $data->no_urut = $req->no_urut_gol ;
         $data->uraian_golongan_ssh = $req->ur_golongan ;
         $data->save (['timestamps' => false]);
         return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
     }

    public function editGolongan(Request $req)
     {
         try{$data = RefSshGolongan::find($req->id_gol_edit);
         $data->no_urut = $req->no_urut_gol_edit ;
         $data->uraian_golongan_ssh = $req->ur_gol_edit ;
         $data->save (['timestamps' => false]);
         return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
     }

    public function hapusGolongan(Request $req)
     {
        RefSshGolongan::where('id_golongan_ssh',$req->id_gol_hapus)->delete ();
       	return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
     }

    public function addKelompok(Request $req)
      {
          try{$data = new RefSshKelompok ();
          $data->id_golongan_ssh = $req->id_gol_kel ;
          $data->no_urut = $req->no_urut_kel ;
          $data->uraian_kelompok_ssh = $req->ur_kel_ssh ;
          $data->save (['timestamps' => false]);
          return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
      }

    public function editKelompok(Request $req)
      {
          try{$data = RefSshKelompok::find($req->id_kel_edit);
          $data->id_golongan_ssh = $req->id_gol_kel_edit ;
          $data->no_urut = $req->no_urut_kel_edit ;
          $data->uraian_kelompok_ssh = $req->ur_kel_edit ;
          $data->save (['timestamps' => false]);
          return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
      }

    public function hapusKelompok(Request $req)
      {
          RefSshKelompok::where('id_kelompok_ssh',$req->id_kel_hapus)->delete ();
          return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
      }

    public function addSubKelompok(Request $req)
        {
            try{$data = new RefSshSubKelompok ();
            $data->id_kelompok_ssh = $req->id_kel_sub ;
            $data->no_urut = $req->no_urut_sub ;
            $data->uraian_sub_kelompok_ssh = $req->ur_subkel_ssh ;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
        }

    public function editSubKelompok(Request $req)
        {
            try{$data = RefSshSubKelompok::find($req->id_subkel_edit);
            $data->id_kelompok_ssh = $req->id_kel_sub ;
            $data->no_urut = $req->no_urut_sub ;
            $data->uraian_sub_kelompok_ssh = $req->ur_subkel_ssh ;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
        }

    public function hapusSubKelompok(Request $req)
        {
            RefSshSubKelompok::where('id_sub_kelompok_ssh',$req->id_subkel_hapus)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        }

    public function addItem(Request $req)
        {
            try{$data = new RefSshTarif ();
            $data->id_sub_kelompok_ssh = $req->id_sub_item ;
            $data->no_urut = $req->no_urut_item ;
            $data->uraian_tarif_ssh = $req->ur_item_ssh ;
            $data->keterangan_tarif_ssh = $req->ket_tarif_ssh ;            
            $data->id_satuan = $req->id_satuan ;           
            $data->status_data = 0 ;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
        }

    public function editItem(Request $req)
        {
            try{$data = RefSshTarif::find($req->id_item_edit);
            $data->id_sub_kelompok_ssh = $req->id_sub_item ;
            $data->no_urut = $req->no_urut_item ;
            $data->uraian_tarif_ssh = $req->ur_item_ssh ;
            $data->keterangan_tarif_ssh = $req->ket_tarif_ssh ;  
            $data->id_satuan = $req->id_satuan ;                       
            $data->status_data = $req->status_data ;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
        }

    public function hapusItem(Request $req)
        {
            RefSshTarif::where('id_tarif_ssh',$req->id_item_hapus)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        }

    public function addRekeningSsh(Request $req)
        {
            try{$data = new RefSshRekening ();
            $data->id_tarif_ssh = $req->id_tarif_ssh ;
            $data->id_rekening = $req->id_rekening ;
            $data->uraian_tarif_ssh = '-' ;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
        }

    public function editRekeningSsh(Request $req)
        {
            try{$data = RefSshRekening::find($req->id_rek_edit);
            $data->id_tarif_ssh = $req->id_tarif_ssh ;
            $data->id_rekening = $req->id_rekening ;
            $data->uraian_tarif_ssh = '-' ;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
        }

    public function hapusRekeningSsh(Request $req)
        {
            RefSshRekening::where('id_rekening_ssh',$req->id_rek_hapus)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        }
}
