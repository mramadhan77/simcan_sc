<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use DB;
use Response;
use Session;
use Auth;
use App\Models\RefSshZona;
use App\Models\RefSshTarif;
use App\Models\RefSshRekening;
use App\Models\RefRek5;
use App\Models\RefSshPerkada;
use App\Models\RefSshPerkadaZona;
use App\Models\RefSshPerkadaTarif;



class RefSshPerkadaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
      $refzona = RefSshZona::select(['id_zona','keterangan_zona'])->get();

    //   if(Auth::check()){ 
          if(Session::has('tahun')){ 
              return view('ssh.perkada.index')->with(compact('refzona'));
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }      
    }

    public function getCountStatus($flag)
        {
          $getCS=DB::select('SELECT IFNULL( (SELECT count(*) FROM ref_ssh_perkada  where flag='.$flag.' group by flag),0) as status_flag');

          return json_encode($getCS);
        }

    public function getRekening()
    {
      $refrekening = DB::table('ref_ssh_rekening')
          ->join('ref_rek_5','ref_rek_5.id_rekening','=','ref_ssh_rekening.id_rekening')
          ->select('ref_ssh_rekening.id_rekening','ref_ssh_rekening.id_tarif_ssh','ref_rek_5.kd_rek_1','ref_rek_5.kd_rek_2','ref_rek_5.kd_rek_3','ref_rek_5.kd_rek_4','ref_rek_5.kd_rek_5','ref_rek_5.nama_kd_rek_5')
          ->where('id_tarif_ssh','=',$_POST['id_tarif'])
          ->get();
      echo json_encode($refrekening);

    }

    public function getItemSSH($like_cari)
    {

        $refitemssh=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT a.id_tarif_ssh, a.id_sub_kelompok_ssh,c.uraian_sub_kelompok_ssh, a.uraian_tarif_ssh, a.id_satuan, 
              b.uraian_satuan, b.singkatan_satuan, COALESCE(d.jml_rupiah,0), a.keterangan_tarif_ssh
              FROM ref_ssh_tarif a
              INNER JOIN ref_ssh_sub_kelompok c ON a.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh
              INNER JOIN ref_satuan b ON a.id_satuan = b.id_satuan
              LEFT OUTER JOIN (SELECT x.*
              FROM ref_ssh_perkada_tarif x
              INNER JOIN ref_ssh_perkada_zona y ON x.id_zona_perkada = y.id_zona_perkada
              INNER JOIN ref_ssh_perkada z ON y.id_perkada = z.id_perkada
              WHERE z.flag = 1 AND y.id_zona=1) d ON a.id_tarif_ssh = d.id_tarif_ssh
              where a.status_data = 0 AND LOWER(a.uraian_tarif_ssh) like "%'.$like_cari.'%") b, (SELECT @id:=0) a');

      return DataTables::of($refitemssh)
        ->make(true);
    }

    public function getGolongan($id_test)
    {
      $refgolongan = DB::select('SELECT (@id:=@id+1) as no_urut, x.* FROM
      (SELECT q.id_golongan_ssh, q.uraian_golongan_ssh
              FROM ref_ssh_perkada_tarif c
              INNER JOIN ref_ssh_perkada_zona b ON c.id_zona_perkada = b.id_zona_perkada
              INNER JOIN ref_ssh_perkada a ON b.id_perkada = a.id_perkada
              INNER JOIN ref_ssh_tarif n ON c.id_tarif_ssh = n.id_tarif_ssh 
              INNER JOIN ref_ssh_sub_kelompok o ON n.id_sub_kelompok_ssh = o.id_sub_kelompok_ssh 
              INNER JOIN ref_ssh_kelompok p ON o.id_kelompok_ssh = p.id_kelompok_ssh 
              INNER JOIN ref_ssh_golongan q ON p.id_golongan_ssh = q.id_golongan_ssh
              WHERE c.id_zona_perkada='.$id_test.' GROUP BY q.id_golongan_ssh, q.uraian_golongan_ssh
              ORDER BY q.id_golongan_ssh, q.uraian_golongan_ssh) x ,
              (SELECT @id:=0) z');

      return DataTables::of($refgolongan)
        ->make(true);
    }

    public function getKelompok($id_test,$id_golongan_ssh)
    {
       $sshkelompok = DB::select('SELECT (@id:=@id+1) as no_urut, x.* FROM
       (SELECT p.id_golongan_ssh, p.id_kelompok_ssh, p.uraian_kelompok_ssh
               FROM ref_ssh_perkada_tarif c
               INNER JOIN ref_ssh_perkada_zona b ON c.id_zona_perkada = b.id_zona_perkada
               INNER JOIN ref_ssh_perkada a ON b.id_perkada = a.id_perkada
               INNER JOIN ref_ssh_tarif n ON c.id_tarif_ssh = n.id_tarif_ssh 
               INNER JOIN ref_ssh_sub_kelompok o ON n.id_sub_kelompok_ssh = o.id_sub_kelompok_ssh 
               INNER JOIN ref_ssh_kelompok p ON o.id_kelompok_ssh = p.id_kelompok_ssh 
               INNER JOIN ref_ssh_golongan q ON p.id_golongan_ssh = q.id_golongan_ssh
               WHERE c.id_zona_perkada='.$id_test.' AND q.id_golongan_ssh = '.$id_golongan_ssh.'
               GROUP BY p.id_golongan_ssh, p.id_kelompok_ssh, p.uraian_kelompok_ssh
               ORDER BY p.id_golongan_ssh, p.id_kelompok_ssh, p.uraian_kelompok_ssh) x ,
               (SELECT @id:=0) z');

      return DataTables::of($sshkelompok)
        ->make(true);
    }

    public function getSubKelompok($id_test,$id_golongan_ssh,$id_kelompok_ssh)
    {
       $sshsubkelompok = DB::select('SELECT (@id:=@id+1) as no_urut, x.* FROM
       (SELECT o.id_kelompok_ssh, o.id_sub_kelompok_ssh, o.uraian_sub_kelompok_ssh
               FROM ref_ssh_perkada_tarif c
               INNER JOIN ref_ssh_perkada_zona b ON c.id_zona_perkada = b.id_zona_perkada
               INNER JOIN ref_ssh_perkada a ON b.id_perkada = a.id_perkada
               INNER JOIN ref_ssh_tarif n ON c.id_tarif_ssh = n.id_tarif_ssh 
               INNER JOIN ref_ssh_sub_kelompok o ON n.id_sub_kelompok_ssh = o.id_sub_kelompok_ssh 
               INNER JOIN ref_ssh_kelompok p ON o.id_kelompok_ssh = p.id_kelompok_ssh 
               INNER JOIN ref_ssh_golongan q ON p.id_golongan_ssh = q.id_golongan_ssh
               WHERE c.id_zona_perkada='.$id_test.' AND q.id_golongan_ssh = '.$id_golongan_ssh.' AND p.id_kelompok_ssh = '.$id_kelompok_ssh.'
               GROUP BY o.id_kelompok_ssh, o.id_sub_kelompok_ssh, o.uraian_sub_kelompok_ssh
               ORDER BY o.id_kelompok_ssh, o.id_sub_kelompok_ssh, o.uraian_sub_kelompok_ssh) x ,
               (SELECT @id:=0) z');                  

      return DataTables::of($sshsubkelompok)
          ->make(true);
    }

    public function getTarif2($id_test,$id_golongan_ssh,$id_kelompok_ssh,$id_sub_kelompok_ssh)
    {
    $perkadatarif = DB::select('SELECT n.no_urut as no_tarif, uraian_tarif_ssh as ur_tarif, c.id_tarif_perkada, (@id:=@id+1) as no_urut, c.id_tarif_ssh,
        c.jml_rupiah, b.id_zona_perkada, b.no_urut as no_zona, b.id_zona, b.nama_zona, 
        m.keterangan_zona as ur_zona, a.id_perkada, a.nomor_perkada as no_perkada, a.tanggal_perkada, 
        a.tahun_berlaku, a.uraian_perkada, a.flag, r.uraian_satuan
        FROM ref_ssh_perkada_tarif c
        INNER JOIN ref_ssh_perkada_zona b ON c.id_zona_perkada = b.id_zona_perkada
        INNER JOIN ref_ssh_perkada a ON b.id_perkada = a.id_perkada
        INNER JOIN ref_ssh_zona m ON b.id_zona = m.id_zona
        INNER JOIN ref_ssh_tarif n ON c.id_tarif_ssh = n.id_tarif_ssh  
        INNER JOIN ref_ssh_sub_kelompok o ON n.id_sub_kelompok_ssh = o.id_sub_kelompok_ssh 
        INNER JOIN ref_ssh_kelompok p ON o.id_kelompok_ssh = p.id_kelompok_ssh 
        INNER JOIN ref_ssh_golongan q ON p.id_golongan_ssh = q.id_golongan_ssh
        INNER JOIN ref_satuan r ON n.id_satuan = r.id_satuan, (SELECT @id:=0) x 
        WHERE c.id_zona_perkada='.$id_test.' AND q.id_golongan_ssh = '.$id_golongan_ssh.' AND p.id_kelompok_ssh = '.$id_kelompok_ssh.' AND o.id_sub_kelompok_ssh = '.$id_sub_kelompok_ssh.'
        ORDER BY a.id_perkada asc, b.id_zona_perkada asc, c.no_urut asc');

     return DataTables::of($perkadatarif)
                 ->addColumn('action', function ($perkadatarif) {
                   if ($perkadatarif->flag==0)
                     return '
                      <div class="btn-group">
                      <button type="button" class="btn btn-info  dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li>
                              <a class="edit-tarif dropdown-item"  data-id_perkada="'.$perkadatarif->id_perkada.'" data-id_tarif_perkada="'.$perkadatarif->id_tarif_perkada.'" data-no_urut="'.$perkadatarif->no_urut.'" data-id_zona_perkada="'.$perkadatarif->id_zona_perkada.'"  data-id_zona="'.$perkadatarif->id_zona.'" data-id_tarif="'.$perkadatarif->id_tarif_ssh.'" data-ur_tarif="'.$perkadatarif->ur_tarif.'" data-jml_rupiah="'.$perkadatarif->jml_rupiah.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data Tarif Item</a>
                          </li>
                          <li>
                              <a class="delete-tarif dropdown-item" data-id_tarif_perkada="'.$perkadatarif->id_tarif_perkada.'" data-ur_tarif="'.$perkadatarif->ur_tarif.'" data-ur_zona="'.$perkadatarif->ur_zona.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Tarif Item</a>
                          </li>                        
                      </ul>
                      </div>
                     ';
                   })
                 ->make(true);
    }

    public function getTarif($id_test)
    {
    $perkadatarif = DB::select('SELECT n.no_urut as no_tarif, uraian_tarif_ssh as ur_tarif, c.id_tarif_perkada, (@id:=@id+1) as no_urut, c.id_tarif_ssh,
        c.jml_rupiah, b.id_zona_perkada, b.no_urut as no_zona, b.id_zona, b.nama_zona, 
        m.keterangan_zona as ur_zona, a.id_perkada, a.nomor_perkada as no_perkada, a.tanggal_perkada, 
        a.tahun_berlaku, a.uraian_perkada, a.flag, o.uraian_satuan
        FROM ref_ssh_perkada_tarif c
        INNER JOIN ref_ssh_perkada_zona b ON c.id_zona_perkada = b.id_zona_perkada
        INNER JOIN ref_ssh_perkada a ON b.id_perkada = a.id_perkada
        INNER JOIN ref_ssh_zona m ON b.id_zona = m.id_zona
        INNER JOIN ref_ssh_tarif n ON c.id_tarif_ssh = n.id_tarif_ssh 
        INNER JOIN ref_satuan o ON o.id_satuan = n.id_satuan, (SELECT @id:=0) x 
        WHERE c.id_zona_perkada='.$id_test.' ORDER BY a.id_perkada asc, b.id_zona_perkada asc, c.no_urut asc');

     return DataTables::of($perkadatarif)
                 ->addColumn('action', function ($perkadatarif) {
                   if ($perkadatarif->flag==0)
                     return '
                      <div class="btn-group">
                      <button type="button" class="btn btn-info  dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li>
                              <a class="edit-tarif dropdown-item"  data-id_perkada="'.$perkadatarif->id_perkada.'" data-id_tarif_perkada="'.$perkadatarif->id_tarif_perkada.'" data-no_urut="'.$perkadatarif->no_urut.'" data-id_zona_perkada="'.$perkadatarif->id_zona_perkada.'"  data-id_zona="'.$perkadatarif->id_zona.'" data-id_tarif="'.$perkadatarif->id_tarif_ssh.'" data-ur_tarif="'.$perkadatarif->ur_tarif.'" data-jml_rupiah="'.$perkadatarif->jml_rupiah.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data Tarif Item</a>
                          </li>
                          <li>
                              <a class="delete-tarif dropdown-item" data-id_tarif_perkada="'.$perkadatarif->id_tarif_perkada.'" data-ur_tarif="'.$perkadatarif->ur_tarif.'" data-ur_zona="'.$perkadatarif->ur_zona.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Tarif Item</a>
                          </li>                        
                      </ul>
                      </div>
                     ';
                   })
                 ->make(true);
    }

    public function getZona($id_test)
    {
       $perkadazona = DB::select('SELECT b.id_zona_perkada, b.no_urut, b.id_zona, b.nama_zona, m.keterangan_zona as ur_zona, a.id_perkada, a.nomor_perkada as no_perkada, 
          a.tanggal_perkada, a.tahun_berlaku, a.uraian_perkada, a.flag, 
          CASE a.flag
            WHEN 0 THEN "Draft"
            WHEN 1 THEN "Aktif"
            WHEN 2 THEN "Tidak Aktif"
          END AS status_perkada, 
          COALESCE(c.jml_item,0) as jml_item
          FROM ref_ssh_perkada_zona b
          INNER JOIN ref_ssh_perkada a ON b.id_perkada = a.id_perkada
          INNER JOIN ref_ssh_zona m ON b.id_zona = m.id_zona          
          LEFT OUTER JOIN (SELECT COUNT(id_tarif_perkada) as jml_item, id_zona_perkada FROM ref_ssh_perkada_tarif GROUP BY id_zona_perkada) c ON b.id_zona = c.id_zona_perkada 
          WHERE b.id_perkada='.$id_test.' ORDER BY a.id_perkada asc, b.no_urut asc');

      return DataTables::of($perkadazona)
                  ->addColumn('action', function ($perkadazona) {
                    if ($perkadazona->flag==0)
                      return '
                      <div class="btn-group">
                      <button type="button" class="btn btn-info  dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li>
                              <a class="edit-zona dropdown-item"  data-id_perkada="'.$perkadazona->id_perkada.'" data-no_urut="'.$perkadazona->no_urut.'" data-id_zona_perkada="'.$perkadazona->id_zona_perkada.'" data-id_zona="'.$perkadazona->id_zona.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data Zona Perkada</a>
                          </li>
                          <li>
                              <a class="delete-zona dropdown-item" data-no_perkada="'.$perkadazona->no_perkada.'" data-id_zona_perkada="'.$perkadazona->id_zona_perkada.'" data-keterangan_zona="'.$perkadazona->ur_zona.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Zona</a>
                          </li>  
                          <li>
                              <a class="copy-item dropdown-item" data-no_perkada="'.$perkadazona->no_perkada.'" data-id_zona_perkada="'.$perkadazona->id_zona_perkada.'" data-keterangan_zona="'.$perkadazona->ur_zona.'"><i class="fa fa-files-o fa-fw fa-lg"></i> Copy Data Item SSH</a>
                          </li>                             
                          <li>
                              <a class="tambah-item dropdown-item" data-no_perkada="'.$perkadazona->no_perkada.'" data-id_zona_perkada="'.$perkadazona->id_zona_perkada.'" data-keterangan_zona="'.$perkadazona->ur_zona.'"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Item SSH</a>
                          </li>                          
                      </ul>
                      </div>
                      ';
                    })
                  ->make(true);
    }

    public function getPerkada()
    {
      $refperkada = DB::select('SELECT id_perkada, nomor_perkada, tanggal_perkada, tahun_berlaku, uraian_perkada, flag, CASE flag
        WHEN 0 THEN "Draft"
        WHEN 1 THEN "Aktif"
        WHEN 2 THEN "Tidak Aktif"
        END AS status_perkada
        FROM ref_ssh_perkada ORDER BY id_perkada asc');

      return DataTables::of($refperkada)
        ->addColumn('action', function ($refperkada) {
            if ($refperkada->flag==0)
              return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info  dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-perkada dropdown-item" data-id_perkada="'.$refperkada->id_perkada.'" data-no_perkada="'.$refperkada->nomor_perkada.'" data-tgl_perkada="'.$refperkada->tanggal_perkada.'" data-thn_perkada="'.$refperkada->tahun_berlaku.'" data-ur_perkada="'.$refperkada->uraian_perkada.'" data-flag_perkada="'.$refperkada->flag.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data Perkada</a>
                        </li>
                        <li>
                            <a class="delete-perkada dropdown-item" data-id_perkada="'.$refperkada->id_perkada.'" data-no_perkada="'.$refperkada->nomor_perkada.'" data-ur_perkada="'.$refperkada->uraian_perkada.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Perkada</a>
                        </li>
                        <li>
                            <a class="edit-status dropdown-item" data-id_perkada="'.$refperkada->id_perkada.'" data-no_perkada="'.$refperkada->nomor_perkada.'" data-ur_perkada="'.$refperkada->uraian_perkada.'" data-flag_perkada="'.$refperkada->flag.'"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Ubah Status Perkada</a>
                        </li>                          
                    </ul>
              </div>
              ';
            if ($refperkada->flag==1)
                return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info  dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>                    
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-status dropdown-item" data-id_perkada="'.$refperkada->id_perkada.'" data-no_perkada="'.$refperkada->nomor_perkada.'" data-ur_perkada="'.$refperkada->uraian_perkada.'" data-flag_perkada="'.$refperkada->flag.'"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Ubah Status Perkada</a>
                        </li>                          
                    </ul>
              </div>
                ';
            return 'Cancel';
          })
        ->make(true);
    }

    public function getDataPerkada(Request $req)
    {
      $getData = DB::select('SELECT id_perkada, nomor_perkada, tanggal_perkada, tahun_berlaku, uraian_perkada, flag, CASE flag
        WHEN 0 THEN "Draft"
        WHEN 1 THEN "Aktif"
        WHEN 2 THEN "Tidak Aktif"
        END AS status_perkada
        FROM ref_ssh_perkada
        WHERE id_perkada <> '.$req->id_perkada.'
        ORDER BY id_perkada asc');

      return json_encode($getData);
    }

    public function getDataZona($id_perkada)
    {
      $getData = DB::select('SELECT a.id_zona_perkada,a.no_urut,a.id_perkada,a.id_zona,a.nama_zona,b.keterangan_zona
          FROM ref_ssh_perkada_zona a
          INNER JOIN ref_ssh_zona b ON a.id_zona = b.id_zona
          WHERE a.id_perkada='.$id_perkada.'');

      return json_encode($getData);
    }

    public function copyTarifRef(Request $req)
    {

    $result=DB::Insert('INSERT INTO ref_ssh_perkada_tarif(no_urut,id_tarif_ssh,id_tarif_old,id_rekening,id_zona_perkada,jml_rupiah)
              SELECT @id:=@id+1 as no_urut, a.* FROM (SELECT id_tarif_ssh,0 AS id_tarif_old,null as id_rekening,
              '.$req->id_zona_perkada.' AS id_zona_perkada,0 as jml_rupiah FROM ref_ssh_tarif WHERE status_data = 0) a, (SELECT @id:=0) c');

    return redirect()->action('RefSshPerkadaController@index');

    }

    public function copyTarifPerkada(Request $req)
    {

    $result=DB::Insert('INSERT INTO ref_ssh_perkada_tarif(no_urut,id_tarif_ssh,id_tarif_old,id_rekening,id_zona_perkada,jml_rupiah)
              SELECT @id:=@id+1 as no_urut,a.* 
              FROM (SELECT a.id_tarif_ssh,a.id_tarif_perkada, a.id_rekening,'.$req->id_zona_perkada_new.',a.jml_rupiah
              FROM ref_ssh_perkada_tarif a
              INNER JOIN ref_ssh_perkada_zona b ON a.id_zona_perkada = b.id_zona_perkada
              INNER JOIN ref_ssh_perkada c ON b.id_perkada = c.id_perkada
              WHERE a.id_zona_perkada='.$req->id_zona_perkada.' AND c.id_perkada = '.$req->id_perkada.' ) a ,(SELECT @id:=0) c');

    return redirect()->action('RefSshPerkadaController@index');

    }

    public function addPerkada(Request $req)
     {
          try{
           $data = new RefSshPerkada ();
           $data->nomor_perkada = $req->no_perkada ;
           $data->tanggal_perkada = $req->tgl_perkada ;
           $data->tahun_berlaku = $req->thn_perkada ;
           $data->uraian_perkada = $req->ur_perkada ;
           $data->flag = 0 ;
           $data->save (['timestamps' => false]);
           return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
     }

    public function addZonaPerkada(Request $req)
      {
          try{
          $data = new RefSshPerkadaZona ();
          $data->no_urut = $req->no_urut ;
          $data->id_perkada = $req->id_perkada ;
          $data->id_zona = $req->id_zona ;
          $data->save (['timestamps' => false]);
          return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
      }

    public function addTarifPerkada(Request $req)
      {
          try{
          $data = new RefSshPerkadaTarif ();
          $data->no_urut = $req->no_urut ;
          $data->id_tarif_ssh = $req->id_tarif_ssh ;
          $data->id_zona_perkada = $req->id_zona_perkada ;
          $data->id_rekening = null ;
          $data->jml_rupiah = $req->jml_rupiah ;
          $data->save (['timestamps' => false]);
          return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
      }

    public function editPerkada(Request $req)
     {
         try{
         $data = RefSshPerkada::find($req->id_perkada_edit);
         $data->nomor_perkada = $req->no_perkada_edit ;
         $data->tanggal_perkada = $req->tgl_perkada_edit ;
         $data->tahun_berlaku = $req->thn_perkada_edit ;
         $data->uraian_perkada = $req->ur_perkada_edit ;
         $data->flag = 0 ;
         $data->save (['timestamps' => false]);
         return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
     }

    public function editZonaPerkada(Request $req)
      {
           try{
           $data = RefSshPerkadaZona::find($req->id_zona_perkada);
           $data->no_urut = $req->no_urut ;
           $data->id_perkada = $req->id_perkada ;
           $data->id_zona = $req->id_zona ;
           $data->save (['timestamps' => false]);
           return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
      }

    public function editTarifPerkada(Request $req)
      {
            try{
            $data = RefSshPerkadaTarif::find($req->id_tarif_perkada);
            $data->no_urut = $req->no_urut ;
            $data->id_tarif_ssh = $req->id_tarif_ssh ;
            $data->id_zona_perkada = $req->id_zona_perkada ;
            // $data->id_rekening = $req->id_rekening ;
            $data->jml_rupiah = $req->jml_rupiah ;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
      }

    public function statusPerkada(Request $req)
      {
        RefSshPerkada::where('flag',1)
                    ->update(['flag' => 2]);

        RefSshPerkada::where('id_perkada',$req->id_perkada)
                      ->update(['flag' => $req->flag_perkada]);

        return response ()->json (['pesan'=>'Data Berhasil Diubah Status','status_pesan'=>'1']);
      }

    public function hapusPerkada(Request $req)
     {
        RefSshPerkada::where('id_perkada',$req->id_perkada_hapus)->delete ();
       	return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
     }

    public function hapusZonaPerkada(Request $req)
      {
        RefSshPerkadaZona::where('id_zona_perkada',$req->id_zona_perkada)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
      }

    public function hapusTarifPerkada(Request $req)
      {
        RefSshPerkadaTarif::where('id_tarif_perkada',$req->id_tarif_perkada)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
      }


}
