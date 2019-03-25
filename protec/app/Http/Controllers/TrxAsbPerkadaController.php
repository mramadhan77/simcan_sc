<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use DB;
use Validator;
use Session;
use Response;
use Auth;
use App\Models\RefSatuan;
use App\Models\TrxAsbAktivitas;
use App\Models\TrxAsbKomponen;
use App\Models\TrxAsbPerkada;
use App\Models\TrxAsbKelompok;
use App\Models\TrxAsbKomponenRinci;
use App\Models\TrxAsbSubKelompok;
use App\Models\TrxAsbSubSubKelompok;
use App\CekAkses;


class TrxAsbPerkadaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
      $refsatuan=DB::select('SELECT null as id_satuan,null as uraian_satuan,null as singkatan_satuan from ref_satuan
              UNION SELECT a.* FROM
              (SELECT id_satuan,uraian_satuan,singkatan_satuan FROM ref_satuan order by uraian_satuan) a');

    //   if(Auth::check()){ 
        $akses = new CekAkses();
        if ($akses->get(805) != true) {
            return redirect('error404'); //Anda juga dapat memberikan alert pemberitahuan disini
        } else {
          return view('asb.aktivitas.index')->with(compact('refsatuan'));
        }
    //   } else {
        // return view ( 'errors.401' );
    //   }
      
    }

    public function getCariKomponen()
    {
      $carikomponen=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT min(a.id_komponen_asb) as id_komponen_asb,sum(a.id_komponen_asb) as temp_komponen, a.nm_komponen_asb, a.id_rekening,CONCAT(b.kd_rek_1,".",b.kd_rek_2,".",b.kd_rek_3,".",b.kd_rek_4,".",b.kd_rek_5,"--",b.nama_kd_rek_5) as nm_rekening FROM trx_asb_komponen a LEFT OUTER JOIN ref_rek_5 b ON a.id_rekening = b.id_rekening GROUP BY a.nm_komponen_asb, a.id_rekening,b.kd_rek_1,b.kd_rek_2,b.kd_rek_3,b.kd_rek_4,b.kd_rek_5,b.nama_kd_rek_5) b, (SELECT @id:=0) a');

      return DataTables::of($carikomponen)
      ->make(true);
    }

    public function getGrouping()
    {
      $getGrouping=DB::select('SELECT ket_group FROM trx_asb_komponen_rinci GROUP BY ket_group
                  HAVING ket_group<>"" AND LENGTH(TRIM(ket_group))>1 ORDER BY ket_group');
      return json_encode($getGrouping);
    }

    public function getCariKelompok()
    {
      $carikelompok=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT min(id_asb_kelompok) as id_asb_kelompok, uraian_kelompok_asb, sum(id_asb_kelompok) as temp_id FROM trx_asb_kelompok GROUP BY uraian_kelompok_asb) b, (SELECT @id:=0) a');

      return DataTables::of($carikelompok)
      ->make(true);
    }

    public function getNoPerkada()
    {
      $getnomor=DB::select('SELECT id_asb_perkada, nomor_perkada, tanggal_perkada, tahun_berlaku, uraian_perkada, flag FROM trx_asb_perkada');

      return json_encode($getnomor);
    }

    public function getCountStatus($flag)
    {
      $getCS=DB::select('SELECT IFNULL( (SELECT count(*) FROM trx_asb_perkada  where flag='.$flag.' group by flag),0) as status_flag');

      return json_encode($getCS);
    }

    public function getTempKelompok($id_asb_kelompok)
    {
        $getTempSKel=DB::select('SELECT id_asb_kelompok,id_asb_perkada, uraian_kelompok_asb, temp_id
            FROM trx_asb_kelompok WHERE id_asb_kelompok = '.$id_asb_kelompok.'');

        return json_encode($getTempSKel);
    }

    public function getTempSubKelompok($id_asb_kelompok)
    {
        $getTempSKel=DB::select('SELECT id_asb_sub_kelompok,uraian_sub_kelompok_asb FROM trx_asb_sub_kelompok WHERE id_asb_kelompok = '.$id_asb_kelompok.'');

        return json_encode($getTempSKel);
    }

    public function getTempSubSubKelompok($id_asb_kelompok,$id_asb_sub_kelompok)
    {
        $getTempSSKel=DB::select('SELECT a.id_asb_sub_kelompok,a.id_asb_sub_sub_kelompok,a.uraian_sub_sub_kelompok_asb FROM trx_asb_sub_sub_kelompok a INNER JOIN trx_asb_sub_kelompok b ON a.id_asb_sub_kelompok = b.id_asb_sub_kelompok WHERE b.id_asb_kelompok = '.$id_asb_kelompok.' and b.id_asb_sub_kelompok='.$id_asb_sub_kelompok.'');

        return json_encode($getTempSSKel);
    }

    public function getTempAktivitas($id_asb_kelompok,$id_asb_sub_sub_kelompok)
    {
        $getTempAkt=DB::select('SELECT a.id_asb_sub_sub_kelompok,a.id_aktivitas_asb,a.nm_aktivitas_asb,a.satuan_aktivitas,a.diskripsi_aktivitas,a.volume_1, a.id_satuan_1, a.sat_derivatif_1,a.volume_2,a.id_satuan_2,a.sat_derivatif_2,a.range_max,a.kapasitas_max,a.range_max1,a.kapasitas_max1 FROM trx_asb_aktivitas a INNER JOIN trx_asb_sub_sub_kelompok b ON a.id_asb_sub_sub_kelompok = b.id_asb_sub_sub_kelompok INNER JOIN trx_asb_sub_kelompok c ON b.id_asb_sub_kelompok = c.id_asb_sub_kelompok WHERE c.id_asb_kelompok = '.$id_asb_kelompok.' and a.id_asb_sub_sub_kelompok='.$id_asb_sub_sub_kelompok.'');

        return json_encode($getTempAkt);
    }

    public function getTempKomponen($id_asb_kelompok,$id_aktivitas_asb)
    {
        $getTempKom=DB::select('SELECT d.id_aktivitas_asb,d.id_komponen_asb,d.nm_komponen_asb,d.id_rekening FROM trx_asb_komponen d
                    INNER JOIN trx_asb_aktivitas a ON d.id_aktivitas_asb=a.id_aktivitas_asb
                    INNER JOIN trx_asb_sub_sub_kelompok b ON a.id_asb_sub_sub_kelompok = b.id_asb_sub_sub_kelompok
                    INNER JOIN trx_asb_sub_kelompok c ON b.id_asb_sub_kelompok = c.id_asb_sub_kelompok WHERE c.id_asb_kelompok = '.$id_asb_kelompok.' 
                    and a.id_aktivitas_asb='.$id_aktivitas_asb.'');

        return json_encode($getTempKom);
    }

    public function getTempRincian($id_asb_kelompok,$id_komponen_asb)
    {
        $getTempRinc=DB::select('SELECT id_komponen_asb_rinci,e.id_komponen_asb,jenis_biaya, id_tarif_ssh, koefisien1, id_satuan1, sat_derivatif1, koefisien2, id_satuan2, sat_derivatif2, koefisien3, id_satuan3, satuan, ket_group, hub_driver 
          FROM trx_asb_komponen_rinci e 
          INNER JOIN trx_asb_komponen d ON e.id_komponen_asb = d.id_komponen_asb
          INNER JOIN trx_asb_aktivitas a ON d.id_aktivitas_asb=a.id_aktivitas_asb
          INNER JOIN trx_asb_sub_sub_kelompok b ON a.id_asb_sub_sub_kelompok = b.id_asb_sub_sub_kelompok
          INNER JOIN trx_asb_sub_kelompok c ON b.id_asb_sub_kelompok = c.id_asb_sub_kelompok WHERE c.id_asb_kelompok = '.$id_asb_kelompok.' and e.id_komponen_asb='.$id_komponen_asb.'');

        return json_encode($getTempRinc);
    }


    public function CopyKelompok(Request $req)
    {

      DB::INSERT('INSERT INTO trx_asb_kelompok(id_asb_perkada, uraian_kelompok_asb,temp_id)
                  SELECT '.$req->id_asb_perkada.',CONCAT(a.uraian_kelompok_asb,"_copy"),'.$req->temp_id.' FROM
                  (SELECT id_asb_kelompok,uraian_kelompok_asb FROM trx_asb_kelompok WHERE id_asb_kelompok='.$req->id_asb_kelompok.') a');

    }

    public function CopySubKelompok(Request $req)
    {

      DB::INSERT('INSERT INTO trx_asb_sub_kelompok(id_asb_kelompok,uraian_sub_kelompok_asb,temp_id)
                SELECT b.id_asb_kelompok, a.uraian_sub_kelompok_asb,'.$req->temp_id_n.' FROM
                (SELECT uraian_sub_kelompok_asb FROM trx_asb_sub_kelompok
                WHERE id_asb_kelompok = '.$req->id_asb_kelompok.' and id_asb_sub_kelompok = '.$req->id_asb_sub_kelompok.') a,(SELECT id_asb_kelompok FROM trx_asb_kelompok WHERE temp_id = '.$req->temp_id_o.') b');
    }

    public function CopySubSubKelompok(Request $req)
    {

      DB::INSERT('INSERT INTO trx_asb_sub_sub_kelompok(id_asb_sub_kelompok,uraian_sub_sub_kelompok_asb,temp_id)
                SELECT b.id_asb_sub_kelompok, a.uraian_sub_sub_kelompok_asb,'.$req->temp_id_n.' FROM
                (SELECT id_asb_sub_sub_kelompok,uraian_sub_sub_kelompok_asb FROM trx_asb_sub_sub_kelompok a WHERE a.id_asb_sub_kelompok = '.$req->id_asb_sub_kelompok.' and a.id_asb_sub_sub_kelompok='.$req->id_asb_sub_sub_kelompok.') a,(SELECT id_asb_sub_kelompok FROM trx_asb_sub_kelompok WHERE temp_id = '.$req->temp_id_o.') b');

    }

    public function CopyAktivitas(Request $req)
    {

      DB::INSERT('INSERT INTO trx_asb_aktivitas (id_asb_sub_sub_kelompok, nm_aktivitas_asb, satuan_aktivitas, diskripsi_aktivitas, volume_1, id_satuan_1, 
                sat_derivatif_1, volume_2, id_satuan_2, sat_derivatif_2, range_max, kapasitas_max, range_max1, kapasitas_max1,temp_id)
                SELECT b.id_asb_sub_sub_kelompok, a.nm_aktivitas_asb, a.satuan_aktivitas, a.diskripsi_aktivitas, a.volume_1, a.id_satuan_1, a.sat_derivatif_1, a.volume_2, a.id_satuan_2, a.sat_derivatif_2, a.range_max, a.kapasitas_max, a.range_max1, a.kapasitas_max1,'.$req->temp_id_n.' FROM
                (SELECT nm_aktivitas_asb, satuan_aktivitas, diskripsi_aktivitas, volume_1, id_satuan_1, sat_derivatif_1, volume_2, id_satuan_2, sat_derivatif_2, range_max, kapasitas_max, range_max1, kapasitas_max1 FROM trx_asb_aktivitas a WHERE a.id_asb_sub_sub_kelompok  = '.$req->id_asb_sub_sub_kelompok.' and a.id_aktivitas_asb = '.$req->id_aktivitas_asb.') a,(SELECT id_asb_sub_sub_kelompok FROM trx_asb_sub_sub_kelompok WHERE temp_id = '.$req->temp_id_o.') b');

    }

    public function CopyKomponen2(Request $req)
    {

      DB::INSERT('INSERT INTO trx_asb_komponen(id_aktivitas_asb,nm_komponen_asb,id_rekening,temp_id)
                  SELECT b.id_aktivitas_asb,a.nm_komponen_asb, a.id_rekening,'.$req->temp_id_n.' FROM
                  (SELECT nm_komponen_asb, id_rekening
                  FROM trx_asb_komponen d WHERE d.id_aktivitas_asb = '.$req->id_aktivitas_asb.' and d.id_komponen_asb = '.$req->id_komponen_asb.') a,
                  (SELECT id_aktivitas_asb FROM trx_asb_aktivitas WHERE temp_id = '.$req->temp_id_o.') b');

    }

    public function CopyRincian(Request $req)
    {

      DB::INSERT('INSERT INTO trx_asb_komponen_rinci(id_komponen_asb,jenis_biaya,id_tarif_ssh,koefisien1,id_satuan1,sat_derivatif1,
                  koefisien2,id_satuan2,sat_derivatif2,koefisien3,id_satuan3,satuan,ket_group,hub_driver) SELECT b.id_komponen_asb,a.* 
                  FROM (SELECT jenis_biaya, id_tarif_ssh, koefisien1, id_satuan1, sat_derivatif1, koefisien2, id_satuan2, sat_derivatif2, koefisien3, id_satuan3, satuan, ket_group, hub_driver FROM trx_asb_komponen_rinci e WHERE e.id_komponen_asb = '.$req->id_komponen_asb.' and e.id_komponen_asb_rinci='.$req->id_komponen_asb_rinci.') a, (Select id_komponen_asb FROM trx_asb_komponen where temp_id='.$req->temp_id_o.') b');

    }


    public function CopyKomponen(Request $req)
    {

      $resultkomp=DB::INSERT('INSERT INTO trx_asb_komponen(id_aktivitas_asb,nm_komponen_asb,id_rekening,temp_id)
                  SELECT '.$req->id_aktivitas_asb.',a.nm_komponen_asb, a.id_rekening,'.$req->temp_id.' FROM
                  (SELECT a.id_komponen_asb, a.nm_komponen_asb, a.id_rekening 
                  FROM trx_asb_komponen a WHERE a.id_komponen_asb='.$req->id_komponen_asb.') a');

      $resultrinci=DB::INSERT('INSERT INTO trx_asb_komponen_rinci(id_komponen_asb,jenis_biaya,id_tarif_ssh,koefisien1,id_satuan1,sat_derivatif1,
                  koefisien2,id_satuan2,sat_derivatif2,koefisien3,id_satuan3,satuan,ket_group,hub_driver) SELECT b.id_komponen_asb,a.* 
                  FROM (SELECT jenis_biaya,id_tarif_ssh,koefisien1,id_satuan1,sat_derivatif1,koefisien2,id_satuan2,sat_derivatif2,koefisien3,
                  id_satuan3,satuan,ket_group,hub_driver FROM trx_asb_komponen_rinci WHERE id_komponen_asb = '.$req->id_komponen_asb.') a,
                  (Select id_komponen_asb FROM trx_asb_komponen where temp_id='.$req->temp_id.') b');

    }
    
    public function getItemSSH($like_cari)
    {
      $refitemssh=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT a.id_tarif_ssh, a.id_sub_kelompok_ssh,c.uraian_sub_kelompok_ssh, a.uraian_tarif_ssh, a.id_satuan, 
              b.uraian_satuan, b.singkatan_satuan, COALESCE(d.jml_rupiah,0) as jml_rupiah, a.keterangan_tarif_ssh
              FROM ref_ssh_tarif a
              INNER JOIN ref_ssh_sub_kelompok c ON a.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh
              INNER JOIN ref_satuan b ON a.id_satuan = b.id_satuan
              LEFT OUTER JOIN (SELECT x.*
              FROM ref_ssh_perkada_tarif x
              INNER JOIN ref_ssh_perkada_zona y ON x.id_zona_perkada = y.id_zona_perkada
              INNER JOIN ref_ssh_perkada z ON y.id_perkada = z.id_perkada
              WHERE z.flag = 1 AND y.id_zona=1) d ON a.id_tarif_ssh = d.id_tarif_ssh
              where LOWER(a.uraian_tarif_ssh) like "%'.$like_cari.'%") b, (SELECT @id:=0) a');      
      return DataTables::of($refitemssh)
      ->make(true);
    }

    public function getRekening()
    {
      $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
            kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 where kd_rek_1=5 and kd_rek_2=2) b, (SELECT @id:=0) a');

      return DataTables::of($refrekening)
      ->make(true);
    }

    public function getRefSatuan()
    {
      $refsatuan=DB::select('SELECT id_satuan,uraian_satuan,singkatan_satuan FROM ref_satuan Order By uraian_satuan');
      return json_encode($refsatuan);
    }

    public function getRefSatuanDer($id_aktivitas,$id_satuan)
    {
      $refsatuander=DB::select('SELECT z.* FROM (SELECT a.id_aktivitas_asb,a.id_satuan_1,a.sat_derivatif_1,b.uraian_satuan
          FROM trx_asb_aktivitas a
          LEFT OUTER JOIN ref_satuan b ON a.sat_derivatif_1 = b.id_satuan
          UNION
          SELECT a.id_aktivitas_asb,a.id_satuan_2,a.sat_derivatif_2,b.uraian_satuan
          FROM trx_asb_aktivitas a
          LEFT OUTER JOIN ref_satuan b ON a.sat_derivatif_2 = b.id_satuan) z 
          WHERE z.id_aktivitas_asb='.$id_aktivitas.' and z.id_satuan_1='.$id_satuan.' Order By z.uraian_satuan');
      return json_encode($refsatuander);
    }

    public function getPerkada()
    {
        $trxasbperkada=DB::select('SELECT (@id:=@id+1) AS no_urut, id_asb_perkada, nomor_perkada, tanggal_perkada, tahun_berlaku, uraian_perkada, flag, CASE flag
              WHEN 0 THEN "Draft"
              WHEN 1 THEN "Aktif"
              WHEN 2 THEN "Tidak Aktif"
              END AS flag_display
              FROM trx_asb_perkada, (SELECT @id:=0) a');

        return DataTables::of($trxasbperkada)
        ->addColumn('action', function ($trxasbperkada) {
          if ($trxasbperkada->flag==0)
          return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-perkada dropdown-item" data-id_perkada="'.$trxasbperkada->id_asb_perkada.'" data-no_perkada="'.$trxasbperkada->nomor_perkada.'" data-ur_perkada="'.$trxasbperkada->uraian_perkada.'" data-tgl_perkada="'.$trxasbperkada->tanggal_perkada.'" data-thn_perkada="'.$trxasbperkada->tahun_berlaku.'" data-flag_perkada="'.$trxasbperkada->flag.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data Perkada</a>
                        </li>
                        <li>
                            <a class="delete-perkada dropdown-item" data-id_perkada="'.$trxasbperkada->id_asb_perkada.'" data-no_perkada="'.$trxasbperkada->nomor_perkada.'" data-ur_perkada="'.$trxasbperkada->uraian_perkada.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Perkada</a>
                        </li>
                        <li>
                            <a class="edit-status dropdown-item" data-id_perkada="'.$trxasbperkada->id_asb_perkada.'" data-no_perkada="'.$trxasbperkada->nomor_perkada.'" data-flag_perkada="'.$trxasbperkada->flag.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Status Perkada</a>
                        </li>
                        <li>
                            <a class="cetak-perkada dropdown-item" data-id_perkada="'.$trxasbperkada->id_asb_perkada.'" data-no_perkada="'.$trxasbperkada->nomor_perkada.'" data-flag_perkada="'.$trxasbperkada->flag.'"><i class="fa fa-print fa-fw fa-lg text-info"></i> Cetak List ASB</a>
                        </li>
                        <li>
                            <a class="cetak-duplikasi dropdown-item" data-id_perkada="'.$trxasbperkada->id_asb_perkada.'" data-no_perkada="'.$trxasbperkada->nomor_perkada.'" data-flag_perkada="'.$trxasbperkada->flag.'"><i class="fa fa-clone fa-fw fa-lg text-danger"></i> Cetak Duplikasi ASB</a>
                        </li>                           
                    </ul>
                </div>
              ';
              if ($trxasbperkada->flag==1)
              return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="edit-status dropdown-item" data-id_perkada="'.$trxasbperkada->id_asb_perkada.'" data-no_perkada="'.$trxasbperkada->nomor_perkada.'" data-flag_perkada="'.$trxasbperkada->flag.'"><i class="glyphicon glyphicon-pencil"></i> Ubah Status Perkada</a>
                            </li> 
                            <li>
                                <a class="cetak-perkada dropdown-item" data-id_perkada="'.$trxasbperkada->id_asb_perkada.'" data-no_perkada="'.$trxasbperkada->nomor_perkada.'" data-flag_perkada="'.$trxasbperkada->flag.'"><i class="fa fa-print fa-fw fa-lg text-info"></i> Cetak List ASB</a>
                            </li>
                            <li>
                                <a class="cetak-duplikasi dropdown-item" data-id_perkada="'.$trxasbperkada->id_asb_perkada.'" data-no_perkada="'.$trxasbperkada->nomor_perkada.'" data-flag_perkada="'.$trxasbperkada->flag.'"><i class="fa fa-clone fa-fw fa-lg text-danger"></i> Cetak Duplikasi ASB</a>
                            </li>                         
                        </ul>
                    </div>
                  ';
                })
        ->make(true);

    }

    public function getKelompok($id_perkada)
    {
      $trxkelompok=DB::select('SELECT (@id:=@id+1) AS no_urut, a.*
                FROM (SELECT a.id_asb_kelompok, a.id_asb_perkada, a.uraian_kelompok_asb, b.nomor_perkada, b.flag
                FROM trx_asb_kelompok a
                INNER JOIN trx_asb_perkada b ON a.id_asb_perkada=b.id_asb_perkada
                WHERE a.id_asb_perkada ='.$id_perkada.') a ,(SELECT @id:=0) z');
      return DataTables::of($trxkelompok)
      ->addColumn('action', function ($trxkelompok) {
          if ($trxkelompok->flag==0)
          return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-asbkelompok dropdown-item" data-id_perkada="'.$trxkelompok->id_asb_perkada.'" data-id_kelompok="'.$trxkelompok->id_asb_kelompok.'" data-flag_perkada="'.$trxkelompok->flag.'" data-uraian_kelompok="'.$trxkelompok->uraian_kelompok_asb.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data Kelompok</a>
                        </li>
                        <li>
                            <a class="hapus-asbkelompok dropdown-item" data-id_perkada="'.$trxkelompok->id_asb_perkada.'" data-id_kelompok="'.$trxkelompok->id_asb_kelompok.'" data-flag_perkada="'.$trxkelompok->flag.'" data-uraian_kelompok="'.$trxkelompok->uraian_kelompok_asb.'" data-no_perkada="'.$trxkelompok->nomor_perkada.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Kelompok</a>
                        </li>                         
                    </ul>
                </div>
              ';
            })
      ->make(true);
    }

    public function getSubKelompok($id_kelompok)
    {
      $trxsubkelompok=DB::select('SELECT (@id:=@id+1) AS no_urut, a.*
                    FROM (SELECT c.id_asb_sub_kelompok,c.id_asb_kelompok,c.uraian_sub_kelompok_asb,b.uraian_kelompok_asb,a.nomor_perkada,a.flag,a.id_asb_perkada 
                    FROM trx_asb_sub_kelompok c
                    INNER JOIN trx_asb_kelompok b ON c.id_asb_kelompok = b.id_asb_kelompok
                    INNER JOIN trx_asb_perkada a ON b.id_asb_perkada=a.id_asb_perkada
                    WHERE c.id_asb_kelompok='.$id_kelompok.') a ,(SELECT @id:=0) z');

      return DataTables::of($trxsubkelompok)
      ->addColumn('action', function ($trxsubkelompok) {
          if ($trxsubkelompok->flag==0)
          return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-subkelompok dropdown-item" data-id_perkada="'.$trxsubkelompok->id_asb_perkada.'" data-id_kelompok="'.$trxsubkelompok->id_asb_kelompok.'" data-flag_perkada="'.$trxsubkelompok->flag.'" data-uraian_kelompok="'.$trxsubkelompok->uraian_kelompok_asb.'" data-uraian_subkelompok="'.$trxsubkelompok->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxsubkelompok->id_asb_sub_kelompok.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data SubKelompok</a>
                        </li>
                        <li>
                            <a class="hapus-subkelompok dropdown-item" data-id_perkada="'.$trxsubkelompok->id_asb_perkada.'" data-id_kelompok="'.$trxsubkelompok->id_asb_kelompok.'" data-flag_perkada="'.$trxsubkelompok->flag.'" data-uraian_kelompok="'.$trxsubkelompok->uraian_kelompok_asb.'" data-no_perkada="'.$trxsubkelompok->nomor_perkada.'" data-uraian_subkelompok="'.$trxsubkelompok->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxsubkelompok->id_asb_sub_kelompok.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data SubKelompok</a>
                        </li>                         
                    </ul>
                </div>
              ';
            })
      ->make(true);
    }

    public function getSubsubkel($id_subkel)
    {
        $trxsubsubkel=DB::select('SELECT (@id:=@id+1) AS no_urut, a.*
                FROM (SELECT m.id_asb_sub_sub_kelompok, m.uraian_sub_sub_kelompok_asb,c.id_asb_sub_kelompok,c.uraian_sub_kelompok_asb,b.id_asb_kelompok,
                b.uraian_kelompok_asb,a.id_asb_perkada,a.nomor_perkada,a.flag
                FROM trx_asb_sub_sub_kelompok m
                INNER JOIN trx_asb_sub_kelompok c ON m.id_asb_sub_kelompok = c.id_asb_sub_kelompok
                INNER JOIN trx_asb_kelompok b ON c.id_asb_kelompok = b.id_asb_kelompok
                INNER JOIN trx_asb_perkada a ON b.id_asb_perkada=a.id_asb_perkada
                WHERE m.id_asb_sub_kelompok='.$id_subkel.') a ,(SELECT @id:=0) z');

        return DataTables::of($trxsubsubkel)
        ->addColumn('action', function ($trxsubsubkel) {
            if ($trxsubsubkel->flag==0)
              return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-sskel dropdown-item" data-id_perkada="'.$trxsubsubkel->id_asb_perkada.'" data-id_kelompok="'.$trxsubsubkel->id_asb_kelompok.'" data-flag_perkada="'.$trxsubsubkel->flag.'" data-uraian_kelompok="'.$trxsubsubkel->uraian_kelompok_asb.'" data-no_perkada="'.$trxsubsubkel->nomor_perkada.'" data-uraian_subkelompok="'.$trxsubsubkel->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxsubsubkel->id_asb_sub_kelompok.'" data-uraian_subsubkelompok="'.$trxsubsubkel->uraian_sub_sub_kelompok_asb.'" data-id_subsubkelompok="'.$trxsubsubkel->id_asb_sub_sub_kelompok.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Sub Sub Kelompok</a>
                        </li>
                        <li>
                            <a class="delete-sskel dropdown-item" data-id_perkada="'.$trxsubsubkel->id_asb_perkada.'" data-id_kelompok="'.$trxsubsubkel->id_asb_kelompok.'" data-flag_perkada="'.$trxsubsubkel->flag.'" data-uraian_kelompok="'.$trxsubsubkel->uraian_kelompok_asb.'" data-no_perkada="'.$trxsubsubkel->nomor_perkada.'" data-uraian_subkelompok="'.$trxsubsubkel->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxsubsubkel->id_asb_sub_kelompok.'" data-uraian_subsubkelompok="'.$trxsubsubkel->uraian_sub_sub_kelompok_asb.'" data-id_subsubkelompok="'.$trxsubsubkel->id_asb_sub_sub_kelompok.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Sub Sub Kelompok</a>
                        </li>                        
                    </ul>
                </div>
              ';
          })
        ->make(true);
    }

    public function getAktivitas($id_subsubkel)
    {
        $trxasbaktivitas=DB::select('SELECT (@id:=@id+1) AS no_urut, a.*
                FROM (SELECT p.uraian_satuan as driver1,q.uraian_satuan as driver2, m.id_asb_sub_sub_kelompok, m.uraian_sub_sub_kelompok_asb,d.id_aktivitas_asb, d.nm_aktivitas_asb, d.satuan_aktivitas, d.diskripsi_aktivitas, d.volume_1, d.id_satuan_1, d.volume_2, d.id_satuan_2, d.range_max, d.kapasitas_max,d.range_max1, d.kapasitas_max1,c.id_asb_sub_kelompok,c.uraian_sub_kelompok_asb,b.id_asb_kelompok,d.sat_derivatif_1,d.sat_derivatif_2,
                b.uraian_kelompok_asb,a.id_asb_perkada,a.nomor_perkada,a.flag
                FROM trx_asb_aktivitas d                
                LEFT OUTER JOIN ref_satuan p ON d.id_satuan_1 = p.id_satuan
                LEFT OUTER JOIN ref_satuan q ON d.id_satuan_2 = q.id_satuan
                INNER JOIN trx_asb_sub_sub_kelompok m ON d.id_asb_sub_sub_kelompok = m.id_asb_sub_sub_kelompok
                INNER JOIN trx_asb_sub_kelompok c ON m.id_asb_sub_kelompok = c.id_asb_sub_kelompok
                INNER JOIN trx_asb_kelompok b ON c.id_asb_kelompok = b.id_asb_kelompok
                INNER JOIN trx_asb_perkada a ON b.id_asb_perkada=a.id_asb_perkada
                WHERE d.id_asb_sub_sub_kelompok='.$id_subsubkel.') a ,(SELECT @id:=0) z');

        return DataTables::of($trxasbaktivitas)
        ->addColumn('action', function ($trxasbaktivitas) {
            if ($trxasbaktivitas->flag==0)
              return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-aktivitas dropdown-item" data-id_perkada="'.$trxasbaktivitas->id_asb_perkada.'" data-id_kelompok="'.$trxasbaktivitas->id_asb_kelompok.'" data-flag_perkada="'.$trxasbaktivitas->flag.'" data-uraian_kelompok="'.$trxasbaktivitas->uraian_kelompok_asb.'" data-no_perkada="'.$trxasbaktivitas->nomor_perkada.'" data-uraian_subkelompok="'.$trxasbaktivitas->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxasbaktivitas->id_asb_sub_kelompok.'" data-id_aktivitas_asb="'.$trxasbaktivitas->id_aktivitas_asb.'" data-ur_aktivitas="'.$trxasbaktivitas->nm_aktivitas_asb.'" data-satuan_aktivitas="'.$trxasbaktivitas->satuan_aktivitas.'" data-volume1="'.$trxasbaktivitas->volume_1.'" data-volume2="'.$trxasbaktivitas->volume_2.'" data-id_satuan1="'.$trxasbaktivitas->id_satuan_1.'" data-id_satuan2="'.$trxasbaktivitas->id_satuan_2.'" data-diskripsi_aktivitas="'.$trxasbaktivitas->diskripsi_aktivitas.'" data-range_max="'.$trxasbaktivitas->range_max.'" data-kapasitas_max="'.$trxasbaktivitas->kapasitas_max.'" data-range_max1="'.$trxasbaktivitas->range_max1.'" data-kapasitas_max1="'.$trxasbaktivitas->kapasitas_max1.'" data-uraian_subsubkelompok="'.$trxasbaktivitas->uraian_sub_sub_kelompok_asb.'" data-id_subsubkelompok="'.$trxasbaktivitas->id_asb_sub_sub_kelompok.'" data-id_sat_derivatif1="'.$trxasbaktivitas->sat_derivatif_1.'" data-id_sat_derivatif2="'.$trxasbaktivitas->sat_derivatif_2.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data Aktivitas</a>
                        </li>
                        <li>
                            <a class="delete-aktivitas dropdown-item" data-id_perkada="'.$trxasbaktivitas->id_asb_perkada.'" data-id_kelompok="'.$trxasbaktivitas->id_asb_kelompok.'" data-flag_perkada="'.$trxasbaktivitas->flag.'" data-uraian_kelompok="'.$trxasbaktivitas->uraian_kelompok_asb.'" data-no_perkada="'.$trxasbaktivitas->nomor_perkada.'" data-uraian_subkelompok="'.$trxasbaktivitas->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxasbaktivitas->id_asb_sub_kelompok.'" data-id_aktivitas_asb="'.$trxasbaktivitas->id_aktivitas_asb.'" data-ur_aktivitas="'.$trxasbaktivitas->nm_aktivitas_asb.'" data-uraian_subsubkelompok="'.$trxasbaktivitas->uraian_sub_sub_kelompok_asb.'" data-id_subsubkelompok="'.$trxasbaktivitas->id_asb_sub_sub_kelompok.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Aktivitas</a>
                        </li>
                        <li>
                            <a class="cetak-aktivitas dropdown-item" data-id_perkada="'.$trxasbaktivitas->id_asb_perkada.'" data-id_kelompok="'.$trxasbaktivitas->id_asb_kelompok.'" data-flag_perkada="'.$trxasbaktivitas->flag.'" data-uraian_kelompok="'.$trxasbaktivitas->uraian_kelompok_asb.'" data-no_perkada="'.$trxasbaktivitas->nomor_perkada.'" data-uraian_subkelompok="'.$trxasbaktivitas->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxasbaktivitas->id_asb_sub_kelompok.'" data-id_aktivitas_asb="'.$trxasbaktivitas->id_aktivitas_asb.'" data-ur_aktivitas="'.$trxasbaktivitas->nm_aktivitas_asb.'" data-uraian_subsubkelompok="'.$trxasbaktivitas->uraian_sub_sub_kelompok_asb.'" data-id_subsubkelompok="'.$trxasbaktivitas->id_asb_sub_sub_kelompok.'"><i class="fa fa-print fa-fw fa-lg"></i> Cetak Aktivitas ASB</a>
                        </li>                         
                    </ul>
                </div>
              ';
              if ($trxasbaktivitas->flag!=0)
              return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="cetak-aktivitas dropdown-item" data-id_perkada="'.$trxasbaktivitas->id_asb_perkada.'" data-id_kelompok="'.$trxasbaktivitas->id_asb_kelompok.'" data-flag_perkada="'.$trxasbaktivitas->flag.'" data-uraian_kelompok="'.$trxasbaktivitas->uraian_kelompok_asb.'" data-no_perkada="'.$trxasbaktivitas->nomor_perkada.'" data-uraian_subkelompok="'.$trxasbaktivitas->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxasbaktivitas->id_asb_sub_kelompok.'" data-id_aktivitas_asb="'.$trxasbaktivitas->id_aktivitas_asb.'" data-ur_aktivitas="'.$trxasbaktivitas->nm_aktivitas_asb.'" data-uraian_subsubkelompok="'.$trxasbaktivitas->uraian_sub_sub_kelompok_asb.'" data-id_subsubkelompok="'.$trxasbaktivitas->id_asb_sub_sub_kelompok.'"><i class="fa fa-print fa-fw fa-lg"></i> Cetak Aktivitas ASB</a>
                        </li>                         
                    </ul>
                </div>
              ';
          })
        ->make(true);
    }

    public function getKomponen($id_aktivitas_asb)
    {
      $trxasbkomponen=DB::select('SELECT (@id:=@id+1) AS no_urut, a.*,IF(a.id_satuan_2=0,"N/A",CONCAT(a.driver1," & ",a.driver2)) AS driver3,
                    IF(a.sat_derivatif_1=0,"N/A",a.driver_derivatif_1) AS driver4,
                    IF(a.sat_derivatif_2=0,"N/A",a.driver_derivatif_2) AS driver5,
                    IF(a.sat_derivatif_1=0 OR a.sat_derivatif_2=0,"N/A",CONCAT(a.driver_derivatif_1," & ",a.driver_derivatif_2)) AS driver6,
                    IF(a.sat_derivatif_1=0 OR a.id_satuan_2=0,"N/A",CONCAT(a.driver_derivatif_1," & ",a.driver2)) AS driver7,
                    IF(a.sat_derivatif_2=0,"N/A",CONCAT(a.driver_derivatif_2," & ",a.driver1)) AS driver8
                    FROM (SELECT  m.id_asb_sub_sub_kelompok, m.uraian_sub_sub_kelompok_asb,e.id_komponen_asb, e.nm_komponen_asb, e.id_rekening,CONCAT(f.kd_rek_1,".",f.kd_rek_2,".",f.kd_rek_3,".",f.kd_rek_4,".",f.kd_rek_5) AS kd_rekening,(f.nama_kd_rek_5) as nm_rekening,d.id_aktivitas_asb, d.nm_aktivitas_asb, d.satuan_aktivitas, d.diskripsi_aktivitas, d.volume_1, d.id_satuan_1,d.volume_2, IF(COALESCE(d.id_satuan_2,0)=0 OR d.id_satuan_2=-1,0,d.id_satuan_2) AS id_satuan_2, d.range_max, d.kapasitas_max,c.id_asb_sub_kelompok,c.uraian_sub_kelompok_asb,b.id_asb_kelompok,b.uraian_kelompok_asb,a.id_asb_perkada,a.nomor_perkada,a.flag, d.kapasitas_max1,d.range_max1,x.uraian_satuan as driver1,IF(COALESCE(d.id_satuan_2,0)=0,"N/A",y.uraian_satuan) AS driver2,IF(COALESCE(d.id_satuan_2,0)=0,1,2) AS jml_driver, IF(COALESCE(d.sat_derivatif_1,0)=0 OR d.sat_derivatif_1=-1,0,d.sat_derivatif_1) AS sat_derivatif_1, IF(COALESCE(d.sat_derivatif_1,0)=0 OR d.sat_derivatif_1=-1,"N/A",p.uraian_satuan) AS driver_derivatif_1, IF(COALESCE(d.sat_derivatif_2,0)=0 OR d.sat_derivatif_2=-1,0,d.sat_derivatif_2) AS sat_derivatif_2, IF(COALESCE(d.sat_derivatif_2,0)=0 OR d.sat_derivatif_2=-1,"N/A",q.uraian_satuan) AS driver_derivatif_2
                    FROM trx_asb_komponen e
                    INNER JOIN ref_rek_5 f ON e.id_rekening = f.id_rekening
                    INNER JOIN trx_asb_aktivitas d ON e.id_aktivitas_asb = d.id_aktivitas_asb
                    INNER JOIN trx_asb_sub_sub_kelompok m ON d.id_asb_sub_sub_kelompok = m.id_asb_sub_sub_kelompok
                    INNER JOIN trx_asb_sub_kelompok c ON m.id_asb_sub_kelompok = c.id_asb_sub_kelompok
                    INNER JOIN trx_asb_kelompok b ON c.id_asb_kelompok = b.id_asb_kelompok
                    INNER JOIN trx_asb_perkada a ON b.id_asb_perkada=a.id_asb_perkada
                    INNER JOIN ref_satuan x ON d.id_satuan_1=x.id_satuan
                    LEFT OUTER JOIN ref_satuan y ON d.id_satuan_2=y.id_satuan
                    LEFT OUTER JOIN ref_satuan p ON d.sat_derivatif_1=p.id_satuan
                    LEFT OUTER JOIN ref_satuan q ON d.sat_derivatif_2=q.id_satuan
                    WHERE e.id_aktivitas_asb ='.$id_aktivitas_asb.') a ,(SELECT @id:=0) z');

      return DataTables::of($trxasbkomponen)
        ->addColumn('action', function ($trxasbkomponen) {
          if ($trxasbkomponen->flag==0)
          return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-komponen dropdown-item" data-id_perkada="'.$trxasbkomponen->id_asb_perkada.'" data-id_kelompok="'.$trxasbkomponen->id_asb_kelompok.'" data-flag_perkada="'.$trxasbkomponen->flag.'" data-uraian_kelompok="'.$trxasbkomponen->uraian_kelompok_asb.'" data-no_perkada="'.$trxasbkomponen->nomor_perkada.'" data-uraian_subkelompok="'.$trxasbkomponen->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxasbkomponen->id_asb_sub_kelompok.'" data-id_aktivitas_asb="'.$trxasbkomponen->id_aktivitas_asb.'" data-ur_aktivitas="'.$trxasbkomponen->nm_aktivitas_asb.'" data-satuan_aktivitas="'.$trxasbkomponen->satuan_aktivitas.'" data-volume1="'.$trxasbkomponen->volume_1.'" data-volume2="'.$trxasbkomponen->volume_2.'" data-id_satuan1="'.$trxasbkomponen->id_satuan_1.'" data-id_satuan2="'.$trxasbkomponen->id_satuan_2.'" data-diskripsi_aktivitas="'.$trxasbkomponen->diskripsi_aktivitas.'" data-range_max="'.$trxasbkomponen->range_max.'" data-kapasitas_max="'.$trxasbkomponen->kapasitas_max.'" data-range_max1="'.$trxasbkomponen->range_max1.'" data-kapasitas_max1="'.$trxasbkomponen->kapasitas_max1.'" data-id_komponen="'.$trxasbkomponen->id_komponen_asb.'" data-uraian_komponen="'.$trxasbkomponen->nm_komponen_asb.'" data-id_rekening="'.$trxasbkomponen->id_rekening.'" data-kd_rekening="'.$trxasbkomponen->kd_rekening.'" data-nm_rekening="'.$trxasbkomponen->nm_rekening.'" data-uraian_subsubkelompok="'.$trxasbkomponen->uraian_sub_sub_kelompok_asb.'" data-id_subsubkelompok="'.$trxasbkomponen->id_asb_sub_sub_kelompok.'" data-driver1="'.$trxasbkomponen->driver1.'" data-driver2="'.$trxasbkomponen->driver3.'" data-driver1="'.$trxasbkomponen->driver3.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data Komponen</a>
                        </li>
                        <li>
                            <a class="delete-komponen dropdown-item" data-id_perkada="'.$trxasbkomponen->id_asb_perkada.'" data-id_kelompok="'.$trxasbkomponen->id_asb_kelompok.'" data-flag_perkada="'.$trxasbkomponen->flag.'" data-uraian_kelompok="'.$trxasbkomponen->uraian_kelompok_asb.'" data-no_perkada="'.$trxasbkomponen->nomor_perkada.'" data-uraian_subkelompok="'.$trxasbkomponen->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxasbkomponen->id_asb_sub_kelompok.'" data-id_aktivitas="'.$trxasbkomponen->id_aktivitas_asb.'" data-ur_aktivitas="'.$trxasbkomponen->nm_aktivitas_asb.'" data-id_komponen="'.$trxasbkomponen->id_komponen_asb.'" data-uraian_komponen="'.$trxasbkomponen->nm_komponen_asb.'" data-uraian_subsubkelompok="'.$trxasbkomponen->uraian_sub_sub_kelompok_asb.'" data-id_subsubkelompok="'.$trxasbkomponen->id_asb_sub_sub_kelompok.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Komponen</a>
                        </li>                        
                    </ul>
                </div>
              ';
          })
        ->make(true);
    }

    public function getRincian($id_komponen_asb)
    {
      $trxasbrinci=DB::select('SELECT (@id:=@id+1) AS no_urut, a.* FROM (SELECT m.id_asb_sub_sub_kelompok, m.uraian_sub_sub_kelompok_asb,f.id_komponen_asb_rinci, f.jenis_biaya, f.id_tarif_ssh, g.uraian_tarif_ssh, f.koefisien1, f.id_satuan1, f.koefisien2, f.ket_group,f.hub_driver,f.sat_derivatif1,
             f.id_satuan2, f.koefisien3, f.id_satuan3, f.satuan, e.id_komponen_asb, e.nm_komponen_asb, e.id_rekening, 
             d.id_aktivitas_asb, d.nm_aktivitas_asb, d.satuan_aktivitas, d.diskripsi_aktivitas, d.volume_1, d.id_satuan_1, 
             d.volume_2, d.id_satuan_2, d.range_max, d.kapasitas_max, d.kapasitas_max1,d.range_max1,c.id_asb_sub_kelompok,c.uraian_sub_kelompok_asb,b.id_asb_kelompok,
             b.uraian_kelompok_asb,a.id_asb_perkada,a.nomor_perkada,a.flag, 
             CASE f.jenis_biaya 
                WHEN 1 THEN "Fix Cost" 
                -- WHEN 2 THEN "Mixed Variable" 
                ELSE "Variable Cost" 
              END AS biaya_display
            FROM trx_asb_komponen_rinci f
            INNER JOIN ref_ssh_tarif g ON f.id_tarif_ssh = g.id_tarif_ssh
            INNER JOIN trx_asb_komponen e ON f.id_komponen_asb = e.id_komponen_asb
            INNER JOIN trx_asb_aktivitas d ON e.id_aktivitas_asb = d.id_aktivitas_asb
            INNER JOIN trx_asb_sub_sub_kelompok m ON d.id_asb_sub_sub_kelompok = m.id_asb_sub_sub_kelompok
            INNER JOIN trx_asb_sub_kelompok c ON m.id_asb_sub_kelompok = c.id_asb_sub_kelompok
            INNER JOIN trx_asb_kelompok b ON c.id_asb_kelompok = b.id_asb_kelompok
            INNER JOIN trx_asb_perkada a ON b.id_asb_perkada=a.id_asb_perkada WHERE f.id_komponen_asb='.$id_komponen_asb.') a ,(SELECT @id:=0) z');

      return DataTables::of($trxasbrinci)
      ->addColumn('action', function ($trxasbrinci) {
      if ($trxasbrinci->flag==0)
          return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-rinci dropdown-item" data-id_perkada="'.$trxasbrinci->id_asb_perkada.'" data-id_kelompok="'.$trxasbrinci->id_asb_kelompok.'" data-flag_perkada="'.$trxasbrinci->flag.'" data-uraian_kelompok="'.$trxasbrinci->uraian_kelompok_asb.'" data-no_perkada="'.$trxasbrinci->nomor_perkada.'" data-uraian_subkelompok="'.$trxasbrinci->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxasbrinci->id_asb_sub_kelompok.'" data-id_aktivitas_asb="'.$trxasbrinci->id_aktivitas_asb.'" data-ur_aktivitas="'.$trxasbrinci->nm_aktivitas_asb.'" data-satuan_aktivitas="'.$trxasbrinci->satuan_aktivitas.'" data-koefisien1="'.$trxasbrinci->koefisien1.'" data-koefisien2="'.$trxasbrinci->koefisien2.'" data-koefisien3="'.$trxasbrinci->koefisien3.'" data-id_satuan1="'.$trxasbrinci->id_satuan1.'" data-id_satuan2="'.$trxasbrinci->id_satuan2.'" data-id_satuan3="'.$trxasbrinci->id_satuan3.'" data-diskripsi_aktivitas="'.$trxasbrinci->diskripsi_aktivitas.'" data-range_max="'.$trxasbrinci->range_max.'" data-kapasitas_max="'.$trxasbrinci->kapasitas_max.'" data-range_max1="'.$trxasbrinci->range_max1.'" data-kapasitas_max1="'.$trxasbrinci->kapasitas_max1.'" data-id_komponen="'.$trxasbrinci->id_komponen_asb.'" data-uraian_komponen="'.$trxasbrinci->nm_komponen_asb.'" data-id_rekening="'.$trxasbrinci->id_rekening.'" data-id_komponen="'.$trxasbrinci->id_komponen_asb.'" data-id_komponen_rinci="'.$trxasbrinci->id_komponen_asb_rinci.'" data-jenis_biaya="'.$trxasbrinci->jenis_biaya.'" data-id_tarif_ssh="'.$trxasbrinci->id_tarif_ssh.'" data-ur_tarif_ssh="'.$trxasbrinci->uraian_tarif_ssh.'" data-uraian_subsubkelompok="'.$trxasbrinci->uraian_sub_sub_kelompok_asb.'" data-id_subsubkelompok="'.$trxasbrinci->id_asb_sub_sub_kelompok.'" data-ket_group="'.$trxasbrinci->ket_group.'" data-hub_driver="'.$trxasbrinci->hub_driver.'" data-sat_derivatif1="'.$trxasbrinci->sat_derivatif1.'"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Data Komponen Rinci</a>
                        </li>
                        <li>
                            <a class="delete-rincian dropdown-item" data-id_perkada="'.$trxasbrinci->id_asb_perkada.'" data-id_kelompok="'.$trxasbrinci->id_asb_kelompok.'" data-flag_perkada="'.$trxasbrinci->flag.'" data-uraian_kelompok="'.$trxasbrinci->uraian_kelompok_asb.'" data-no_perkada="'.$trxasbrinci->nomor_perkada.'" data-uraian_subkelompok="'.$trxasbrinci->uraian_sub_kelompok_asb.'" data-id_subkelompok="'.$trxasbrinci->id_asb_sub_kelompok.'" data-id_aktivitas="'.$trxasbrinci->id_aktivitas_asb.'" data-ur_aktivitas="'.$trxasbrinci->nm_aktivitas_asb.'" data-id_komponen="'.$trxasbrinci->id_komponen_asb.'" data-uraian_komponen="'.$trxasbrinci->nm_komponen_asb.'" data-id_komponen_rinci="'.$trxasbrinci->id_komponen_asb_rinci.'" data-jenis_biaya="'.$trxasbrinci->jenis_biaya.'" data-id_tarif_ssh="'.$trxasbrinci->id_tarif_ssh.'" data-ur_tarif_ssh="'.$trxasbrinci->uraian_tarif_ssh.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Komponen Rinci</a>
                        </li>                        
                    </ul>
                </div>
              ';
          })
        ->make(true);
    }

    public function addKelompok(Request $req)
      {
         $data = new TrxAsbKelompok ();
         $data->uraian_kelompok_asb = $req->ur_asb_kel ;
         $data->id_asb_perkada = $req->id_perkada_kel ;
         $data->save (['timestamps' => false]);
         return response ()->json ( $data );
      }

    public function editKelompok(Request $req)
      {
          $data = TrxAsbKelompok::find($req->id_asb_kel_edit);
          $data->uraian_kelompok_asb = $req->ur_asb_kel_edit ;
          $data->id_asb_perkada = $req->id_perkada_kel_edit ;
          $data->save (['timestamps' => false]);
          return response ()->json ( $data );
      }

    public function hapusKelompok(Request $req)
      {
        TrxAsbKelompok::where('id_asb_kelompok',$req->id_asb_kel_del)->delete ();
        return response ()->json ();
      }

    public function addSubKelompok(Request $req)
      {
         $data = new TrxAsbSubKelompok ();
         $data->uraian_sub_kelompok_asb = $req->ur_asb_subkel ;
         $data->id_asb_kelompok = $req->id_kel_subkel ;
         $data->save (['timestamps' => false]);
         return response ()->json ( $data );
      }

    public function editSubKelompok(Request $req)
      {
          $data = TrxAsbSubKelompok::find($req->id_asb_subkel_edit);
           $data->uraian_sub_kelompok_asb = $req->ur_asb_subkel_edit ;
         $data->id_asb_kelompok = $req->id_kel_subkel_edit ;
          $data->save (['timestamps' => false]);
          return response ()->json ( $data );
      }

    public function hapusSubKelompok(Request $req)
      {
        TrxAsbSubKelompok::where('id_asb_sub_kelompok',$req->id_asb_subkel_del)->delete ();
        return response ()->json ();
      }

    public function addSubSubKelompok(Request $req)
      {
         $data = new TrxAsbSubSubKelompok ();
         $data->uraian_sub_sub_kelompok_asb = $req->ur_asb_ssubkel ;
         $data->id_asb_sub_kelompok = $req->id_subkel_ssubkel ;
         $data->save (['timestamps' => false]);
         return response ()->json ( $data );
      }

    public function editSubSubKelompok(Request $req)
      {
          $data = TrxAsbSubSubKelompok::find($req->id_asb_ssubkel_edit);
          $data->uraian_sub_sub_kelompok_asb = $req->ur_asb_ssubkel_edit ;
          $data->id_asb_sub_kelompok = $req->id_subkel_ssubkel_edit ;
          $data->save (['timestamps' => false]);
          return response ()->json ( $data );
      }

    public function hapusSubSubKelompok(Request $req)
      {
        TrxAsbSubSubKelompok::where('id_asb_sub_sub_kelompok',$req->id_asb_ssubkel_del)->delete ();
        return response ()->json ();
      }


    public function addKomponen(Request $req)
     {
         $data = new TrxAsbKomponen ();
         $data->nm_komponen_asb = $req->nm_komponen ;
         $data->id_aktivitas_asb = $req->id_aktivitas_komp ;
         $data->id_rekening = $req->id_rekening_komp ;
         $data->save (['timestamps' => false]);
         return response ()->json ( $data );
     }

    public function editKomponen(Request $req)
      {
          $data = TrxAsbKomponen::find($req->id_komponen_asb);
          $data->nm_komponen_asb = $req->nm_komponen ;
          $data->id_aktivitas_asb = $req->id_aktivitas_komp ;
          $data->id_rekening = $req->id_rekening_komp ;
          $data->save (['timestamps' => false]);
          return response ()->json ( $data );
      }

    public function hapusKomponen(Request $req)
      {
        TrxAsbKomponen::where('id_komponen_asb',$req->id_komponen_hapus)->delete ();
        return response ()->json ();
      }

    public function addRincian(Request $req)
       {
           $data = new TrxAsbKomponenRinci ();
           $data->id_komponen_asb = $req->id_komponen_asb ;
           $data->id_tarif_ssh = $req->id_tarif_ssh ;
           $data->koefisien1 = $req->koefisien1 ;
           $data->koefisien2 = $req->koefisien2 ;
           $data->koefisien3 = $req->koefisien3 ;
           $data->id_satuan1 = $req->id_satuan1 ;
           $data->id_satuan2 = $req->id_satuan2 ;
           $data->id_satuan3 = $req->id_satuan3 ;
           $data->sat_derivatif1 = $req->sat_derivatif1;
           $data->sat_derivatif2 = null;
           $data->jenis_biaya = $req->jenis_biaya ;
           $data->hub_driver = $req->hub_driver;
           $data->ket_group = $req->ket_group;
           $data->save (['timestamps' => false]);
           return response ()->json ( $data );
       }

      public function editRincian(Request $req)
        {
           $data = TrxAsbKomponenRinci::find($req->id_komponen_asb_rinci);
           $data->id_komponen_asb = $req->id_komponen_asb ;
           $data->id_tarif_ssh = $req->id_tarif_ssh ;
           $data->koefisien1 = $req->koefisien1 ;
           $data->koefisien2 = $req->koefisien2 ;
           $data->koefisien3 = $req->koefisien3 ;
           $data->id_satuan1 = $req->id_satuan1 ;
           $data->id_satuan2 = $req->id_satuan2 ;
           $data->id_satuan3 = $req->id_satuan3 ;
           $data->sat_derivatif1 = $req->sat_derivatif1;
           $data->sat_derivatif2 = null;
           $data->jenis_biaya = $req->jenis_biaya ;
           $data->hub_driver = $req->hub_driver;
           $data->ket_group = $req->ket_group;
           $data->save (['timestamps' => false]);
           return response ()->json ( $data );
        }

      public function hapusRincian(Request $req)
        {
          TrxAsbKomponenRinci::where('id_komponen_asb_rinci',$req->id_komponen_asb_rinci)->delete ();
          return response ()->json ();
        }

    public function addPerkada(Request $req)
     {
         $data = new TrxAsbPerkada ();
         $data->nomor_perkada = $req->no_perkada ;
         $data->tanggal_perkada = $req->tgl_perkada ;
         $data->tahun_berlaku = $req->thn_perkada ;
         $data->uraian_perkada = $req->ur_perkada ;
         $data->flag = 0 ;
         $data->save (['timestamps' => false]);
         return response ()->json ( $data );
     }

     public function editPerkada(Request $req)
      {
          $data = TrxAsbPerkada::find($req->id_perkada_edit);
          $data->nomor_perkada = $req->no_perkada_edit ;
          $data->tanggal_perkada = $req->tgl_perkada_edit ;
          $data->tahun_berlaku = $req->thn_perkada_edit ;
          $data->uraian_perkada = $req->ur_perkada_edit ;
          $data->flag = 0 ;
          $data->save (['timestamps' => false]);
          return response ()->json ( $data );
      }

      public function statusPerkada(Request $req)
        {
          TrxAsbPerkada::where('flag',1)
                      ->update(['flag' => 2]);

          TrxAsbPerkada::where('id_asb_perkada',$req->id_perkada)
                        ->update(['flag' => $req->flag_perkada]);

          return response ()->json ();
        }

      public function hapusPerkada(Request $req)
       {
          TrxAsbPerkada::where('id_asb_perkada',$req->id_perkada_hapus)->delete ();
         	return response ()->json ();
       }

      public function addAktivitas(Request $req)
        {
            $data = new TrxAsbAktivitas ();
            $data->id_asb_sub_sub_kelompok = $req->id_asb_sub_sub_kelompok ;
            $data->nm_aktivitas_asb = $req->nm_aktivitas_asb ;
            $data->satuan_aktivitas = null ;
            $data->diskripsi_aktivitas = $req->diskripsi_aktivitas ;
            $data->volume_1 = 1;
            $data->id_satuan_1 = $req->id_satuan1 ;
            $data->sat_derivatif_1 = $req->sat_derivatif1;
            $data->volume_2 = 1 ;
            $data->id_satuan_2 = $req->id_satuan2 ;
            $data->sat_derivatif_2 = $req->sat_derivatif2;
            $data->range_max = $req->range_max;
            $data->kapasitas_max = $req->kapasitas_max;
            $data->range_max1 = $req->range_max1;
            $data->kapasitas_max1 = $req->kapasitas_max1;
            $data->save (['timestamps' => false]);
            return response ()->json ( $data );
        }

      public function editAktivitas(Request $req)
         {
            $data = TrxAsbAktivitas::find($req->id_aktivitas_asb);
            $data->id_asb_sub_sub_kelompok = $req->id_asb_sub_sub_kelompok ;
            $data->nm_aktivitas_asb = $req->nm_aktivitas_asb ;
            $data->satuan_aktivitas = null ;
            $data->diskripsi_aktivitas = $req->diskripsi_aktivitas ;
            $data->volume_1 = 1;
            $data->id_satuan_1 = $req->id_satuan1 ;
            $data->sat_derivatif_1 = $req->sat_derivatif1;
            $data->volume_2 = 1 ;
            $data->id_satuan_2 = $req->id_satuan2 ;
            $data->sat_derivatif_2 = $req->sat_derivatif2;
            $data->range_max = $req->range_max;
            $data->kapasitas_max = $req->kapasitas_max;
            $data->range_max1 = $req->range_max1;
            $data->kapasitas_max1 = $req->kapasitas_max1;
            $data->save (['timestamps' => false]);
            return response ()->json ( $data );
         }

      public function hapusAktivitas(Request $req)
          {
            TrxAsbAktivitas::where('id_aktivitas_asb',$req->id_aktivitas_asb)->delete ();
            return response ()->json ();
          }

}
