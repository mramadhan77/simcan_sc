<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Http\Requests;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use DB;
use Validator;
use Response;
use Auth;
use App\Models\RefTahun;
use App\Models\RefSatuan;
use App\Models\RefSshZona;
use App\Models\RefSshTarif;
use App\Models\RefSshRekening;
use App\Models\RefRek5;
use App\Models\RefSshPerkada;
use App\Models\RefSshPerkadaZona;
use App\Models\RefSshPerkadaTarif;
use App\Models\TrxAsbPerhitungan;
use App\Models\TrxAsbPerhitunganAktivitas;
use App\Models\TrxAsbPerhitunganKomponen;
use App\Models\TrxAsbPerhitunganRinci;
use App\Models\TrxAsbAktivitas;
use App\Models\RefAsbPerkada;
use App\Models\RefAsbKomponen;
use App\Models\RefAsbKomponenRinci;
use App\CekAkses;


class TrxAsbPerhitunganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {            
            $refperkada=DB::select('SELECT id_asb_perkada, nomor_perkada, tanggal_perkada, tahun_berlaku, uraian_perkada, flag FROM trx_asb_perkada where flag=1');

            $reftahun=DB::select('SELECT a.tahun FROM (
                SELECT tahun_1 as tahun, 1 as tahun_ke from ref_tahun
                UNION
                SELECT tahun_2 as tahun, 2 as tahun_ke from ref_tahun
                UNION
                SELECT tahun_3 as tahun, 3 as tahun_ke from ref_tahun
                UNION
                SELECT tahun_4 as tahun, 4 as tahun_ke from ref_tahun
                UNION
                SELECT tahun_5 as tahun, 5 as tahun_ke from ref_tahun) a');

            $refZona=DB::select('SELECT b.id_zona,d.keterangan_zona
                FROM ref_ssh_perkada a
                INNER JOIN ref_ssh_perkada_zona b ON a.id_perkada = b.id_perkada
                INNER JOIN ref_ssh_zona d ON b.id_zona = d.id_zona
                WHERE a.flag=1
                GROUP BY b.id_zona,d.keterangan_zona');

    //   if(Auth::check()){ 
          $akses = new CekAkses();
          if ($akses->get(806) != true) {
              return redirect('error404');
          } else {             
            return view('asb.perhitungan.index')->with(compact('refperkada','reftahun','refZona'));
          }
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function getTahunHitung()
    {
      $getTahun=DB::select('SELECT tahun_perhitungan FROM trx_asb_perhitungan group by tahun_perhitungan');
      return json_encode($getTahun);
    }

    public function getPerkadaSimulasi($tahun)
    {
      $getPerkadaSimulasi=DB::select('SELECT a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, b.nomor_perkada, a.flag_aktif 
          FROM trx_asb_perhitungan a
          INNER JOIN trx_asb_perkada b ON a.id_perkada = b.id_asb_perkada 
          WHERE a.tahun_perhitungan='.$tahun.'');

      return json_encode($getPerkadaSimulasi);
    }

    public function getAktivitasSimulasi($id_perhitungan)
    {
      $getAktivitasSimulasi=DB::select('SELECT a.id_perhitungan, b.id_aktivitas_asb, c.nm_aktivitas_asb
          FROM trx_asb_perhitungan_rinci b
          INNER JOIN trx_asb_perhitungan a ON b.id_perhitungan = a.id_perhitungan
          INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb 
          WHERE a.id_perhitungan='.$id_perhitungan.'
          GROUP BY a.id_perhitungan, b.id_aktivitas_asb, c.nm_aktivitas_asb');

      return json_encode($getAktivitasSimulasi);
    }

    public function datahitung()
    {
      $datahitung=DB::select('SELECT a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, c.nomor_perkada, c.tanggal_perkada, CASE a.flag_aktif WHEN 0 THEN "Draft" WHEN 1 THEN "Aktif" WHEN 2 THEN "Tidak Aktif" END AS status_perkada
              FROM trx_asb_perhitungan a
              INNER JOIN trx_asb_perkada c ON a.id_perkada = c.id_asb_perkada');

        return DataTables::of($datahitung)
            ->addColumn('action', function ($datahitung) {
              if ($datahitung->flag_aktif==0)
              return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info  dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-status dropdown-item" data-id_perhitungan="'.$datahitung->id_perhitungan.'" data-tahun_perhitungan="'.$datahitung->tahun_perhitungan.'" data-no_perkada="'.$datahitung->nomor_perkada.'" data-flag_aktif="'.$datahitung->flag_aktif.'"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Ubah Status Perhitungan</a>
                        </li> 
                        <li>
                            <a class="hapus-perhitungan dropdown-item" data-id_perhitungan="'.$datahitung->id_perhitungan.'" data-tahun_perhitungan="'.$datahitung->tahun_perhitungan.'" data-no_perkada="'.$datahitung->nomor_perkada.'" data-status_perkada="'.$datahitung->status_perkada.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Data Perkada</a>
                        </li>                                                 
                    </ul>
              </div>
              ';
              if ($datahitung->flag_aktif==1)
              return '
              <div class="btn-group">
                      <button type="button" class="btn btn-info  dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li>
                              <a class="edit-status dropdown-item" data-id_perhitungan="'.$datahitung->id_perhitungan.'" data-tahun_perhitungan="'.$datahitung->tahun_perhitungan.'" data-no_perkada="'.$datahitung->nomor_perkada.'" data-flag_aktif="'.$datahitung->flag_aktif.'"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Ubah Status Perhitungan</a>
                          </li>                                                    
                      </ul>
                </div>
                ';
            })

        ->make(true);
    }

    public function datax($id_perhitungan)
    {
     
     $datax=DB::select('SELECT DISTINCT a.tahun_perhitungan,a.id_perhitungan,a.id_perkada,a.flag_aktif,
              CASE a.flag_aktif WHEN 0 THEN "Draft" WHEN 1 THEN "Aktif" WHEN 2 THEN "Tidak Aktif" END AS status_perkada,c.nomor_perkada,c.tanggal_perkada,c.tahun_berlaku,c.uraian_perkada,c.flag,b.id_perhitungan_rinci,b.id_asb_kelompok,
              d.uraian_kelompok_asb,b.id_asb_sub_kelompok,e.uraian_sub_kelompok_asb,b.id_asb_sub_sub_kelompok,f.uraian_sub_sub_kelompok_asb,b.id_aktivitas_asb,g.id_satuan_1,g1.uraian_satuan,g.range_max,g.kapasitas_max,
              g.id_satuan_2,g2.uraian_satuan,g.range_max1,g.kapasitas_max1,b.id_komponen_asb,h.nm_komponen_asb,h.id_rekening,
              h1.nama_kd_rek_5,h1.kd_rek_1,h1.kd_rek_2,h1.kd_rek_3,h1.kd_rek_4,h1.kd_rek_5,b.id_komponen_asb_rinci,
              i.jenis_biaya,i.id_tarif_ssh,i4.uraian_tarif_ssh,i.id_satuan1,i1.uraian_satuan,i.koefisien1,i.id_satuan2,
              i2.uraian_satuan,i.koefisien2,i.id_satuan3,i3.uraian_satuan,i.koefisien3,i.ket_group,i.hub_driver,b.harga_satuan,b.jml_pagu 
              FROM ((((((((((((((trx_asb_komponen_rinci i 
              INNER JOIN trx_asb_perhitungan_rinci b ON (i.id_komponen_asb_rinci = b.id_komponen_asb_rinci))
              INNER JOIN ref_ssh_tarif i4 ON (i.id_tarif_ssh = i4.id_tarif_ssh)) 
              LEFT OUTER JOIN ref_satuan i1 ON (i1.id_satuan = i.id_satuan1))
              LEFT OUTER JOIN ref_satuan i2 ON (i2.id_satuan = i.id_satuan2))
              LEFT OUTER JOIN ref_satuan i3 ON (i3.id_satuan = i.id_satuan3))
              INNER JOIN trx_asb_perhitungan a ON (b.id_perhitungan = a.id_perhitungan))
              INNER JOIN trx_asb_perkada c ON (a.id_perkada = c.id_asb_perkada))
              INNER JOIN trx_asb_kelompok d ON (b.id_asb_kelompok = d.id_asb_kelompok))
              INNER JOIN trx_asb_sub_kelompok e ON (b.id_asb_sub_kelompok = e.id_asb_sub_kelompok))
              INNER JOIN trx_asb_sub_sub_kelompok f ON (f.id_asb_sub_sub_kelompok = b.id_asb_sub_sub_kelompok))
              INNER JOIN trx_asb_aktivitas g ON (g.id_aktivitas_asb = b.id_aktivitas_asb))
              LEFT OUTER JOIN ref_satuan g2 ON (g2.id_satuan = g.id_satuan_2))
              LEFT OUTER JOIN ref_satuan g1 ON (g1.id_satuan = g.id_satuan_1))
              INNER JOIN trx_asb_komponen h ON (h.id_komponen_asb = b.id_komponen_asb))
              INNER JOIN ref_rek_5 h1 ON (h1.id_rekening = h.id_rekening)
              WHERE a.id_perhitungan = '.$id_perhitungan.'');

        return DataTables::of($datax)
        ->make(true);
    }

    public function datakelompok($id_perhitungan)
    {
      $datakelompok=DB::select('SELECT (@id:=@id+1) as no_urut,x.*
              FROM (SELECT c.id_asb_kelompok, d.uraian_kelompok_asb, SUM(c.jml_pagu) AS jml_pagu, a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada
              FROM trx_asb_perhitungan_rinci c
              INNER JOIN trx_asb_perhitungan a on c.id_perhitungan = a.id_perhitungan
              INNER JOIN trx_asb_perkada b ON a.id_perkada = b.id_asb_perkada
              INNER JOIN trx_asb_kelompok d ON c.id_asb_kelompok = d.id_asb_kelompok
              WHERE c.id_perhitungan = '.$id_perhitungan.'
              GROUP BY a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada,c.id_asb_kelompok,d.uraian_kelompok_asb) x, (SELECT @id:=0) y');

      return DataTables::of($datakelompok)
      ->make(true);
    }

    public function datasubkelompok($id_kelompok,$id_perhitungan)
    {
      $datasubkelompok=DB::select('SELECT (@id:=@id+1) as no_urut,x.*
              FROM (SELECT c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb, c.id_asb_kelompok, d.uraian_kelompok_asb, SUM(c.jml_pagu) AS jml_pagu, a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada
              FROM trx_asb_perhitungan_rinci c
              INNER JOIN trx_asb_perhitungan a on c.id_perhitungan = a.id_perhitungan
              INNER JOIN trx_asb_perkada b ON a.id_perkada = b.id_asb_perkada
              INNER JOIN trx_asb_kelompok d ON c.id_asb_kelompok = d.id_asb_kelompok
              INNER JOIN trx_asb_sub_kelompok e ON c.id_asb_sub_kelompok = e.id_asb_sub_kelompok
              WHERE c.id_asb_kelompok = '.$id_kelompok.' and c.id_perhitungan = '.$id_perhitungan.' 
              GROUP BY a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada,c.id_asb_kelompok,d.uraian_kelompok_asb,c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb) x, (SELECT @id:=0) y');

      return DataTables::of($datasubkelompok)
      ->make(true);
    }

    public function datasubsubkelompok($id_subkelompok,$id_perhitungan)
    {
      $datasubsubkelompok=DB::select('SELECT (@id:=@id+1) as no_urut,x.*
              FROM (SELECT c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb, c.id_asb_kelompok, d.uraian_kelompok_asb,c.id_asb_sub_sub_kelompok,f.uraian_sub_sub_kelompok_asb,SUM(c.jml_pagu) AS jml_pagu, a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada
              FROM trx_asb_perhitungan_rinci c
              INNER JOIN trx_asb_perhitungan a on c.id_perhitungan = a.id_perhitungan
              INNER JOIN trx_asb_perkada b ON a.id_perkada = b.id_asb_perkada
              INNER JOIN trx_asb_kelompok d ON c.id_asb_kelompok = d.id_asb_kelompok
              INNER JOIN trx_asb_sub_kelompok e ON c.id_asb_sub_kelompok = e.id_asb_sub_kelompok
              INNER JOIN trx_asb_sub_sub_kelompok f ON c.id_asb_sub_sub_kelompok = f.id_asb_sub_sub_kelompok
              WHERE e.id_asb_sub_kelompok = '.$id_subkelompok.' and c.id_perhitungan = '.$id_perhitungan.' 
              GROUP BY a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada,c.id_asb_kelompok,d.uraian_kelompok_asb,c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb,c.id_asb_sub_sub_kelompok,f.uraian_sub_sub_kelompok_asb) x, (SELECT @id:=0) y');

      return DataTables::of($datasubsubkelompok)
      ->make(true);
    }

    public function datazona($id_subkelompok,$id_perhitungan)
    {
      $datasubsubkelompok=DB::select('SELECT (@id:=@id+1) as no_urut,x.*
              FROM (SELECT c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb, c.id_asb_kelompok, d.uraian_kelompok_asb,c.id_asb_sub_sub_kelompok,f.uraian_sub_sub_kelompok_asb,SUM(c.jml_pagu) AS jml_pagu, a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada, c.id_zona, g.keterangan_zona
              FROM trx_asb_perhitungan_rinci c
              INNER JOIN trx_asb_perhitungan a on c.id_perhitungan = a.id_perhitungan
              INNER JOIN trx_asb_perkada b ON a.id_perkada = b.id_asb_perkada
              INNER JOIN trx_asb_kelompok d ON c.id_asb_kelompok = d.id_asb_kelompok
              INNER JOIN trx_asb_sub_kelompok e ON c.id_asb_sub_kelompok = e.id_asb_sub_kelompok
              INNER JOIN trx_asb_sub_sub_kelompok f ON c.id_asb_sub_sub_kelompok = f.id_asb_sub_sub_kelompok
              INNER JOIN ref_ssh_zona g ON c.id_zona = g.id_zona
              WHERE c.id_asb_sub_sub_kelompok = '.$id_subkelompok.' and c.id_perhitungan = '.$id_perhitungan.' 
              GROUP BY a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada,c.id_asb_kelompok,d.uraian_kelompok_asb,c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb,c.id_asb_sub_sub_kelompok,f.uraian_sub_sub_kelompok_asb, c.id_zona, g.keterangan_zona) x, (SELECT @id:=0) y');

      return DataTables::of($datasubsubkelompok)
      ->make(true);
    }

    public function dataaktivitas($id_subkelompok,$id_perhitungan,$id_zona)
    {

      $dataaktivitas=DB::select('SELECT (@id:=@id+1) as no_urut,x.*
              FROM (SELECT c.id_aktivitas_asb, f.nm_aktivitas_asb,x.uraian_satuan as satuan1,y.uraian_satuan as satuan2, f.range_max1, f.range_max, f.kapasitas_max,f.kapasitas_max1,c.id_asb_sub_sub_kelompok,g.uraian_sub_sub_kelompok_asb,c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb, c.id_asb_kelompok, d.uraian_kelompok_asb, SUM(c.jml_pagu) AS jml_pagu, a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada
              FROM trx_asb_perhitungan_rinci c
              INNER JOIN trx_asb_perhitungan a on c.id_perhitungan = a.id_perhitungan
              INNER JOIN trx_asb_perkada b ON a.id_perkada = b.id_asb_perkada
              INNER JOIN trx_asb_kelompok d ON c.id_asb_kelompok = d.id_asb_kelompok
              INNER JOIN trx_asb_sub_kelompok e ON c.id_asb_sub_kelompok = e.id_asb_sub_kelompok
              INNER JOIN trx_asb_sub_sub_kelompok g ON c.id_asb_sub_sub_kelompok = g.id_asb_sub_sub_kelompok
              INNER JOIN trx_asb_aktivitas f ON c.id_aktivitas_asb = f.id_aktivitas_asb
              LEFT OUTER JOIN ref_satuan x ON f.id_satuan_1 = x.id_satuan
              LEFT OUTER JOIN ref_satuan y ON f.id_satuan_2 = y.id_satuan
              WHERE c.id_asb_sub_sub_kelompok = '.$id_subkelompok.' and c.id_perhitungan = '.$id_perhitungan.' and c.id_zona = '.$id_zona.' GROUP BY a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada,c.id_asb_kelompok,d.uraian_kelompok_asb,c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb,c.id_aktivitas_asb, f.nm_aktivitas_asb,f.satuan_aktivitas, x.uraian_satuan,y.uraian_satuan, f.range_max1, f.range_max, f.kapasitas_max,f.kapasitas_max1,c.id_asb_sub_sub_kelompok,g.uraian_sub_sub_kelompok_asb,c.id_zona) x, (SELECT @id:=0) y');

      return DataTables::of($dataaktivitas)
            ->addColumn('action', function ($dataaktivitas) {
              return '<button class="print-hitungaktivitas btn btn-labeled btn-primary" data-id_aktivitas_asb="'.$dataaktivitas->id_aktivitas_asb.'"><span class="btn-label"><i class="fa fa-print fa-fw fa-lg"></i></span>Cetak Perhitungan Aktivitas</button>';})
      ->make(true);
    }

    public function datakomponen($id_aktivitas,$id_perhitungan,$id_zona)
    {

      $datakomponen=DB::select('SELECT (@id:=@id+1) as no_urut,x.*
              FROM (SELECT c.id_komponen_asb, g.nm_komponen_asb,CONCAT(h.kd_rek_1,".",h.kd_rek_2,".",h.kd_rek_3,".",h.kd_rek_4,".",h.kd_rek_5) AS kd_rekening,(h.nama_kd_rek_5) as nm_rekening,c.id_aktivitas_asb, f.nm_aktivitas_asb,f.satuan_aktivitas, f.range_max, f.kapasitas_max,c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb, c.id_asb_kelompok, d.uraian_kelompok_asb, SUM(c.jml_pagu) AS jml_pagu, a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada,c.id_asb_sub_sub_kelompok,p.uraian_sub_sub_kelompok_asb
              FROM trx_asb_perhitungan_rinci c
              INNER JOIN trx_asb_perhitungan a on c.id_perhitungan = a.id_perhitungan
              INNER JOIN trx_asb_perkada b ON a.id_perkada = b.id_asb_perkada
              INNER JOIN trx_asb_kelompok d ON c.id_asb_kelompok = d.id_asb_kelompok
              INNER JOIN trx_asb_sub_kelompok e ON c.id_asb_sub_kelompok = e.id_asb_sub_kelompok
              INNER JOIN trx_asb_sub_sub_kelompok p ON c.id_asb_sub_sub_kelompok = p.id_asb_sub_sub_kelompok
              INNER JOIN trx_asb_aktivitas f ON c.id_aktivitas_asb = f.id_aktivitas_asb
              INNER JOIN trx_asb_komponen g ON c.id_komponen_asb = g.id_komponen_asb
              INNER JOIN ref_rek_5 h ON g.id_rekening = h.id_rekening
              WHERE c.id_aktivitas_asb = '.$id_aktivitas.' and c.id_perhitungan = '.$id_perhitungan.' and c.id_zona = '.$id_zona.' GROUP BY a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada,c.id_asb_kelompok,d.uraian_kelompok_asb,c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb,c.id_aktivitas_asb, f.nm_aktivitas_asb,f.satuan_aktivitas, f.range_max, f.kapasitas_max,c.id_komponen_asb, g.nm_komponen_asb,
h.kd_rek_1,h.kd_rek_2,h.kd_rek_3,h.kd_rek_4,h.kd_rek_5,h.nama_kd_rek_5,c.id_asb_sub_sub_kelompok,p.uraian_sub_sub_kelompok_asb,c.id_zona) x, (SELECT @id:=0) y');

      return DataTables::of($datakomponen)
      ->make(true);
    }

    public function datarinci($id_komponen,$id_perhitungan,$id_zona)
    {
      $datarincian=DB::select('SELECT (@id:=@id+1) as no_urut,x.*
              FROM (SELECT c.id_komponen_asb_rinci, i.jenis_biaya,
              CASE i.jenis_biaya
              WHEN 1 THEN "Fix Cost"
              WHEN 2 THEN "Variable Cost"
              END AS jenis_display,i.koefisien1,i.koefisien2,i.koefisien3, c.id_tarif_ssh,j.uraian_tarif_ssh,k.uraian_satuan, 
c.harga_satuan, c.jml_pagu, c.id_komponen_asb, g.nm_komponen_asb,CONCAT(h.kd_rek_1,".",h.kd_rek_2,".",h.kd_rek_3,".",h.kd_rek_4,".",h.kd_rek_5,"--",h.nama_kd_rek_5) as nm_rekening,c.id_aktivitas_asb, f.nm_aktivitas_asb, f.satuan_aktivitas, f.range_max, f.kapasitas_max, c.id_asb_sub_kelompok, e.uraian_sub_kelompok_asb, c.id_asb_kelompok, d.uraian_kelompok_asb, a.tahun_perhitungan, a.id_perhitungan, a.id_perkada, a.flag_aktif, b.nomor_perkada
              FROM trx_asb_perhitungan_rinci c
              INNER JOIN trx_asb_perhitungan a on c.id_perhitungan = a.id_perhitungan
              INNER JOIN trx_asb_perkada b ON a.id_perkada = b.id_asb_perkada
              INNER JOIN trx_asb_kelompok d ON c.id_asb_kelompok = d.id_asb_kelompok
              INNER JOIN trx_asb_sub_kelompok e ON c.id_asb_sub_kelompok = e.id_asb_sub_kelompok
              INNER JOIN trx_asb_aktivitas f ON c.id_aktivitas_asb = f.id_aktivitas_asb
              INNER JOIN trx_asb_komponen g ON c.id_komponen_asb = g.id_komponen_asb
              INNER JOIN ref_rek_5 h ON g.id_rekening = h.id_rekening
              INNER JOIN trx_asb_komponen_rinci i ON c.id_komponen_asb_rinci = i.id_komponen_asb_rinci
              INNER JOIN ref_ssh_tarif j ON c.id_tarif_ssh = j.id_tarif_ssh
              INNER JOIN ref_satuan k ON j.id_satuan = k.id_satuan
              WHERE c.id_komponen_asb = '.$id_komponen.' and c.id_perhitungan = '.$id_perhitungan.' and c.id_zona = '.$id_zona.') x, (SELECT @id:=0) y');

      return DataTables::of($datarincian)
      ->make(true);
    }


public function getDataASB($id_asb_perkada)
    {
        $getDataASB = DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, a.id_aktivitas_asb, a.nm_aktivitas_asb,
                a.id_satuan_1, b.uraian_satuan as uraian_satuan_1, a.id_satuan_2, 
                CASE a.id_satuan_2 
                 WHEN -1 THEN "N/A"
                 WHEN 0 THEN "Kosong"
                 ELSE c.uraian_satuan
                END AS uraian_satuan_2
                FROM trx_asb_aktivitas AS a
                LEFT OUTER JOIN ref_satuan AS b ON a.id_satuan_1 = b.id_satuan
                LEFT OUTER JOIN ref_satuan AS c ON a.id_satuan_2 = c.id_satuan
                INNER JOIN trx_asb_sub_sub_kelompok AS d ON a.id_asb_sub_sub_kelompok = d.id_asb_sub_sub_kelompok
                INNER JOIN trx_asb_sub_kelompok AS e ON d.id_asb_sub_kelompok = e.id_asb_sub_kelompok
                INNER JOIN trx_asb_kelompok AS f ON e.id_asb_kelompok = f.id_asb_kelompok
                INNER JOIN trx_asb_perkada AS g ON f.id_asb_perkada = g.id_asb_perkada
								INNER JOIN (SELECT a.id_perhitungan, b.id_aktivitas_asb, c.nm_aktivitas_asb
                    FROM trx_asb_perhitungan_rinci b
                    INNER JOIN trx_asb_perhitungan a ON b.id_perhitungan = a.id_perhitungan
                    INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb 
                    WHERE a.id_perhitungan='.$id_asb_perkada.'
                    GROUP BY a.id_perhitungan, b.id_aktivitas_asb, c.nm_aktivitas_asb) h ON a.id_aktivitas_asb = h.id_aktivitas_asb,    
                (SELECT @id:=0) AS z');

        return DataTables::of($getDataASB)
            ->addColumn('action',function($getDataASB){
                return '
                    <button id="btnPilihASB" type="button" class="btn btn-success btn-labeled" title="Pilih Aktivitas yang akan dilakukan simulasi"><span class="btn-label"><i class="fa fa-check fa-fw fa-lg"></i></span>Pilih</button>
                    ' ;
            })
        ->make(true);
    }



    public function addPerhitungan(Request $req){

        $data = new TrxAsbPerhitungan();

        $data->tahun_perhitungan = $req->tahun_perhitungan ;
        $data->id_perkada = $req->id_perkada ;
        $data->flag_aktif= 0;

        $data->save (['timestamps' => false]);

    }

    public function addPerhitunganRinci(Request $req){

        $data = new TrxAsbPerhitunganRinci();

        $data->id_perhitungan= $req->id_perhitungan;
        $data->id_asb_kelompok= $req->id_asb_kelompok;
        $data->id_asb_sub_kelompok= $req->id_asb_sub_kelompok;
        $data->id_asb_sub_sub_kelompok= $req->id_asb_sub_sub_kelompok;
        $data->id_aktivitas_asb= $req->id_aktivitas_asb;
        $data->id_komponen_asb= $req->id_komponen_asb;
        $data->id_komponen_asb_rinci= $req->id_komponen_asb_rinci;
        $data->id_tarif_ssh= $req->id_tarif_ssh;
        $data->id_zona= $req->id_zona;
        $data->harga_satuan= $req->harga_satuan;
        $data->jml_pagu= $req->jml_pagu;
        $data->kfix= $req->kfix;
        $data->kmax= $req->kmax;
        $data->kdv1= $req->kdv1;
        $data->kr1= $req->kr1;
        $data->kdv2= $req->kdv2;
        $data->kr2= $req->kr2;
        $data->kiv1= $req->kiv1;
        $data->kiv2= $req->kiv2;
        $data->kiv3= $req->kiv3;

        $data->save (['timestamps' => false]);

    }

    public function GetHitungASB(Request $req){

      $result = DB::SELECT('SELECT b.id_perhitungan,a.id_asb_kelompok,a.id_asb_sub_kelompok,a.id_asb_sub_sub_kelompok,a.id_aktivitas_asb,a.id_komponen_asb,a.id_komponen_asb_rinci,a.id_tarif_ssh,a.id_zona,a.jml_rupiah,a.jml_pagu FROM (SELECT a.id_asb_perkada,b.id_asb_kelompok,c.id_asb_sub_kelompok,g.id_asb_sub_sub_kelompok,d.id_aktivitas_asb,e.id_komponen_asb,f.id_komponen_asb_rinci,f.id_tarif_ssh,f.id_zona,f.jml_rupiah,f.koefisien1,f.koefisien2,f.koefisien3,PaguASB(f.jenis_biaya,f.hub_driver,1,1,d.range_max,d.range_max1,d.kapasitas_max,d.kapasitas_max1,f.koefisien1,f.koefisien2,f.koefisien3,f.jml_rupiah) AS jml_pagu
        FROM trx_asb_perkada a
        INNER JOIN trx_asb_kelompok b ON b.id_asb_perkada = a.id_asb_perkada
        INNER JOIN trx_asb_sub_kelompok c ON c.id_asb_kelompok = b.id_asb_kelompok
        INNER JOIN trx_asb_sub_sub_kelompok g ON c.id_asb_sub_kelompok = g.id_asb_sub_kelompok
        INNER JOIN trx_asb_aktivitas d ON g.id_asb_sub_sub_kelompok = d.id_asb_sub_sub_kelompok
        INNER JOIN trx_asb_komponen e ON d.id_aktivitas_asb = e.id_aktivitas_asb
        INNER JOIN (
        SELECT a.*, b.jml_rupiah,b.id_zona 
        FROM trx_asb_komponen_rinci a
        INNER JOIN 
        (
        SELECT a.id_perkada,nomor_perkada,tanggal_perkada,tahun_berlaku,uraian_perkada,flag,b.id_zona_perkada,
        id_zona,id_tarif_perkada,id_tarif_ssh,id_rekening,jml_rupiah
        FROM ref_ssh_perkada a
        INNER JOIN ref_ssh_perkada_zona b ON a.id_perkada = b.id_perkada
        INNER JOIN ref_ssh_perkada_tarif c ON c.id_zona_perkada = b.id_zona_perkada
        WHERE a.flag=1) b ON a.id_tarif_ssh = b.id_tarif_ssh) f ON e.id_komponen_asb = f.id_komponen_asb) a INNER JOIN trx_asb_perhitungan b ON a.id_asb_perkada = b.id_perkada where b.tahun_perhitungan ='.$req->tahun_perhitungan.' and a.id_zona='.$req->id_zona);

      return json_encode($result);
    }


    public function ProsesHitungAsb(Request $req)
    {
      $result=DB::Insert('INSERT INTO trx_asb_perhitungan_rinci (id_perhitungan, id_asb_kelompok, id_asb_sub_kelompok,id_asb_sub_sub_kelompok, id_aktivitas_asb, id_komponen_asb, id_komponen_asb_rinci, id_tarif_ssh, id_zona, harga_satuan, jml_pagu) SELECT b.id_perhitungan,a.id_asb_kelompok,a.id_asb_sub_kelompok,a.id_asb_sub_sub_kelompok,a.id_aktivitas_asb,a.id_komponen_asb,a.id_komponen_asb_rinci,a.id_tarif_ssh,a.id_zona,a.jml_rupiah,a.jml_pagu FROM (SELECT a.id_asb_perkada,b.id_asb_kelompok,c.id_asb_sub_kelompok,g.id_asb_sub_sub_kelompok,d.id_aktivitas_asb,e.id_komponen_asb,f.id_komponen_asb_rinci,f.id_tarif_ssh,f.id_zona,f.jml_rupiah,f.koefisien1,f.koefisien2,f.koefisien3,PaguASB(f.jenis_biaya,f.hub_driver,1,1,d.range_max,d.range_max1,d.kapasitas_max,d.kapasitas_max1,f.koefisien1,f.koefisien2,f.koefisien3,f.jml_rupiah) AS jml_pagu
        FROM trx_asb_perkada a
        INNER JOIN trx_asb_kelompok b ON b.id_asb_perkada = a.id_asb_perkada
        INNER JOIN trx_asb_sub_kelompok c ON c.id_asb_kelompok = b.id_asb_kelompok
        INNER JOIN trx_asb_sub_sub_kelompok g ON c.id_asb_sub_kelompok = g.id_asb_sub_kelompok
        INNER JOIN trx_asb_aktivitas d ON g.id_asb_sub_sub_kelompok = d.id_asb_sub_sub_kelompok
        INNER JOIN trx_asb_komponen e ON d.id_aktivitas_asb = e.id_aktivitas_asb
        INNER JOIN (
        SELECT a.*, b.jml_rupiah,b.id_zona 
        FROM trx_asb_komponen_rinci a
        INNER JOIN 
        (
        SELECT a.id_perkada,nomor_perkada,tanggal_perkada,tahun_berlaku,uraian_perkada,flag,b.id_zona_perkada,
        id_zona,id_tarif_perkada,id_tarif_ssh,id_rekening,jml_rupiah
        FROM ref_ssh_perkada a
        INNER JOIN ref_ssh_perkada_zona b ON a.id_perkada = b.id_perkada
        INNER JOIN ref_ssh_perkada_tarif c ON c.id_zona_perkada = b.id_zona_perkada
        WHERE a.flag=1 AND a.tahun_berlaku='.$req->tahun_perhitungan.') b ON a.id_tarif_ssh = b.id_tarif_ssh) f ON e.id_komponen_asb = f.id_komponen_asb) a INNER JOIN trx_asb_perhitungan b ON a.id_asb_perkada = b.id_perkada 
        where b.tahun_perhitungan ='.$req->tahun_perhitungan.'');

    }

    public function UbahStatus(Request $req)
         {
            $data = TrxAsbPerhitungan::find($req->id_perhitungan);
            $data->flag_aktif = $req->flag_aktif ;
            $data->save (['timestamps' => false]);
            return response ()->json ( $data );
         }

      public function hapusPerhitungan(Request $req)
          {
            TrxAsbPerhitungan::where('id_perhitungan',$req->id_perhitungan)->delete ();
            return response ()->json ();
          }

}
