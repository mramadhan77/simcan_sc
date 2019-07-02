<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\CekAkses;
use Auth;

class RefParameterController extends Controller
{

public function __construct()
{
        $this->middleware('auth');
}

public function getAspek(){
    $Aspek=DB::select('SELECT id_aspek, uraian_aspek_pembangunan, status_data, created_at, updated_at
        FROM ref_aspek_pembangunan');
    return json_encode($Aspek);
}
    
public function getKegRef($id_program)
{
   $KegRef=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.id_kegiatan, a.id_program, a.kd_kegiatan, a.nm_kegiatan,
        CONCAT(LEFT(CONCAT(0,d.kd_urusan),2),".",RIGHT(CONCAT(0,c.kd_bidang),2),".",RIGHT(CONCAT("00",b.kd_program),3),".",RIGHT(CONCAT("00",a.kd_kegiatan),3)) AS kd_kegiatan
        FROM ref_kegiatan a
        INNER JOIN ref_program b ON a.id_program=b.id_program
        INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
        INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan,(SELECT @id:=0) x
        WHERE a.id_program='.$id_program);

   return DataTables::of($KegRef)
   ->make(true);
}

public function getProgRef($id_bidang)
{
   $ProgRef=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.id_program, CONCAT(LEFT(CONCAT(0,c.kd_urusan),2),".",RIGHT(CONCAT(0,b.kd_bidang),2),".",RIGHT(CONCAT("00",a.kd_program),3)) AS kd_program, a.uraian_program  
            FROM ref_program a
            INNER JOIN ref_bidang b ON a.id_bidang = b.id_bidang
            INNER JOIN ref_urusan c ON b.kd_urusan = c.kd_urusan,(SELECT @id:=0) x WHERE a.id_bidang='.$id_bidang.'');

   return DataTables::of($ProgRef)
   ->make(true);
}

public function getTahun()
{
    $getTahun=DB::select('select tahun_1 as tahun from ref_tahun
      union all
      select tahun_2 as tahun from ref_tahun
      union all
      select tahun_3 as tahun from ref_tahun
      union all
      select tahun_4 as tahun from ref_tahun
      union all
      select tahun_5 as tahun from ref_tahun');
    return json_encode($getTahun);
}

public function getUrusan(){
      $urusan=DB::select('SELECT kd_urusan, nm_urusan FROM ref_urusan');
        return json_encode($urusan);
}

public function getBidang($id_urusan){
        $bidang=DB::select('SELECT * FROM ref_bidang WHERE kd_urusan='.$id_urusan);        
        return json_encode($bidang);
}

public function getBidang2(){
    $bidang=DB::select('SELECT *, CONCAT(kd_urusan,".",kd_bidang," - ",nm_bidang) AS uraian_bidang FROM ref_bidang');        
    return json_encode($bidang);
}

public function getSumberDana()
{
   $getSB=DB::SELECT('SELECT id_sumber_dana, uraian_sumber_dana FROM ref_sumber_dana');
   return json_encode($getSB);
}

public function getKecamatan()
{
   $getKecamatan=DB::SELECT('SELECT id_pemda, kd_kecamatan, id_kecamatan, nama_kecamatan FROM ref_kecamatan');
   return json_encode($getKecamatan);
}

public function getDesaAll()
{
   $getDesa=DB::SELECT('SELECT id_kecamatan,kd_desa,id_desa,status_desa,nama_desa,id_zona
            FROM ref_desa');
   return json_encode($getDesa);
}

public function getDesa($id_kecamatan)
{
   $getDesa=DB::SELECT('SELECT a.id_kecamatan,a.kd_desa,a.id_desa,a.status_desa,a.nama_desa,a.id_zona, COALESCE((SELECT DISTINCT x.id_lokasi FROM ref_lokasi AS x 
        WHERE x.id_desa = a.id_desa AND x.jenis_lokasi = 0),0) AS id_lokasi
   FROM ref_desa AS a WHERE a.id_kecamatan='.$id_kecamatan);
   return json_encode($getDesa);
}

public function getUnit()
{
   $getSubUnit=DB::SELECT('SELECT (@id:=@id+1) as no_urut, b.id_unit, b.kd_unit, b.nm_unit, b.id_bidang, 
        CONCAT(RIGHT(CONCAT("0",c.kd_urusan),2),".",RIGHT(CONCAT("0",c.kd_bidang),2),".",RIGHT(CONCAT("0",b.kd_unit),2)," -- ",b.nm_unit) AS nama_display
        FROM ref_unit AS b 
        INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang, (SELECT @id:=0) x');
   return json_encode($getSubUnit);
}

public function getSubUnit($id_unit)
{
   $getSubUnit=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_sub_unit, a.id_unit, a.kd_sub, a.nm_sub,
        CONCAT(RIGHT(CONCAT("0",c.kd_urusan),2),".",RIGHT(CONCAT("0",c.kd_bidang),2),".",RIGHT(CONCAT("0",b.kd_unit),2),".",RIGHT(CONCAT("000",a.kd_sub),4)," -- ",a.nm_sub) AS nama_display 
        FROM ref_sub_unit AS a
        INNER JOIN ref_unit AS b ON a.id_unit = b.id_unit
        INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang, (SELECT @id:=0) x  WHERE a.id_unit='.$id_unit);

   return json_encode($getSubUnit);
}

public function getSubUnitTable($id_unit)
{
   $getSubUnit=DB::SELECT('SELECT (@id:=@id+1) as no_urut,id_sub_unit, id_unit, kd_sub, nm_sub
        FROM ref_sub_unit, (SELECT @id:=0) x  WHERE id_unit='.$id_unit);
   return DataTables::of($getSubUnit)
   ->make(true);
}

public function getUnit2($bidang)
{
    $getUnit=DB::select('SELECT id_unit, nm_unit FROM ref_unit where id_bidang='.$bidang);
    return json_encode($getUnit);
}

public function getSub2($unit)
{
    $getSub=DB::select('SELECT id_sub_unit, nm_sub FROM ref_sub_unit where id_unit='.$unit);
    return json_encode($getSub);
}

public function getProgram_Renja($unit,$tahun)
{
    $getProgram=DB::select('SELECT id_renja_program, uraian_program_renstra FROM trx_renja_rancangan_program where id_unit='.$unit.' and tahun_renja='.$tahun);
    return json_encode($getProgram);
}
public function getKegiatan_Renja($program)
{
    $getKegiatan=DB::select('SELECT id_renja, uraian_kegiatan_renstra FROM trx_renja_rancangan where id_renja_program='.$program);
    return json_encode($getKegiatan);
}

public function getZonaSSH()
{
   $getZonaSSH=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.id_zona, a.keterangan_zona, a.diskripsi_zona
          FROM ref_ssh_zona AS a, (SELECT @id:=0) x');
   return json_encode($getZonaSSH);
}

public function getZonaAktif()
{
   $getZona=DB::SELECT('SELECT DISTINCT b.id_zona, c.keterangan_zona
            FROM ref_ssh_perkada a
            INNER JOIN ref_ssh_perkada_zona b ON a.id_perkada = b.id_perkada
            INNER JOIN ref_ssh_zona c ON b.id_zona = c.id_zona
            WHERE a.flag = 1');
   return json_encode($getZona);
}

public function getRefSatuan()
    {
      $refsatuan=DB::select('SELECT id_satuan,uraian_satuan,singkatan_satuan FROM ref_satuan Order By uraian_satuan');
      return json_encode($refsatuan);
    }

public function getLokasiLuarDaerah()
{
   $getLokasiLuar=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.id_lokasi, a.jenis_lokasi, a.nama_lokasi,  
            a.keterangan_lokasi FROM ref_lokasi a  
            WHERE a.jenis_lokasi = 99) a, (SELECT @id:=0) x ');

   return DataTables::of($getLokasiLuar)
   ->make(true);
}

public function getLokasiTeknis()
{
   $getLokasiTeknis=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.id_lokasi, a.jenis_lokasi, a.nama_lokasi,  
            a.keterangan_lokasi FROM ref_lokasi a  
            WHERE a.jenis_lokasi <> 0 AND a.jenis_lokasi <> 99) a, (SELECT @id:=0) x ');

   return DataTables::of($getLokasiTeknis)
   ->make(true);
}

public function getLokasiDesa($kecamatan)
{
   $getLokasiDesa=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* FROM (SELECT a.id_lokasi, a.jenis_lokasi, a.nama_lokasi, a.id_desa, 
            a.keterangan_lokasi, b.id_kecamatan, b.nama_desa, b.kd_desa
            FROM ref_lokasi a LEFT OUTER JOIN ref_desa b on a.id_desa=b.id_desa 
            WHERE b.id_kecamatan = '.$kecamatan.') a, (SELECT @id:=0) x ');

   return DataTables::of($getLokasiDesa)
   ->make(true);
}

public function getAktivitasASB($tahun)
{
   $getASB=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.* 
          FROM (SELECT a.tahun_perhitungan, b.id_aktivitas_asb, c.nm_aktivitas_asb, c.id_satuan_1, c.id_satuan_2, c.diskripsi_aktivitas, 
          d.uraian_satuan as uraian_satuan_1,COALESCE(e.uraian_satuan,"Kosong") as uraian_satuan_2
          FROM trx_asb_perhitungan_rinci b
          INNER JOIN trx_asb_perhitungan a ON b.id_perhitungan = a.id_perhitungan
          INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
          LEFT OUTER JOIN ref_satuan d ON c.id_satuan_1 = d.id_satuan
          LEFT OUTER JOIN ref_satuan e ON c.id_satuan_2 = e.id_satuan 
          WHERE a.tahun_perhitungan = '.$tahun.' 
          GROUP BY a.tahun_perhitungan, b.id_aktivitas_asb, c.nm_aktivitas_asb, c.id_satuan_1, c.id_satuan_2, c.diskripsi_aktivitas,d.uraian_satuan,e.uraian_satuan) a, (SELECT @id:=0) x ');

   return DataTables::of($getASB)
   ->make(true);
}

public function getRefIndikator(){
      $refindikator=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_indikator,a.kualitas_indikator,
            a.jenis_indikator, a.sifat_indikator, a.nm_indikator, a.flag_iku,
            a.asal_indikator, a.sumber_data_indikator,a.type_indikator,a.id_satuan_output,b.uraian_satuan,
            CASE a.type_indikator
                WHEN 1 THEN "Hasil"
                WHEN 0 THEN "Keluaran"
                WHEN 2 THEN "Dampak"
                WHEN 3 THEN "Masukan"
            END AS type_display,
            CASE a.jenis_indikator
                WHEN 1 THEN "Positif"
                WHEN 0 THEN "Negatif"
            END AS jenis_display,
            CASE a.sifat_indikator
                WHEN 0 THEN "Incremental"
                WHEN 1 THEN "Absolut"
                WHEN 2 THEN "Komulatif"
            END AS sifat_display,
            CASE a.kualitas_indikator
                WHEN 0 THEN "Kualitas"
                WHEN 1 THEN "Kuantitas"
                WHEN 2 THEN "Persentase"
                WHEN 3 THEN "Rata-Rata"
                WHEN 4 THEN "Rasio"
            END AS kualitas_display
            FROM ref_indikator AS a
            LEFT OUTER JOIN ref_satuan AS b ON a.id_satuan_output = b.id_satuan, 
            (SELECT @id:=0) x');
      return DataTables::of($refindikator)
      ->make(true);
    }

public function getRekeningSsh($id,$tarif)
    {
      $cekRek = DB::SELECT('SELECT x.id_tarif_ssh, x.uraian_tarif_ssh, COUNT(y.id_rekening_ssh) AS jml_rekening FROM ref_ssh_tarif x
        LEFT OUTER JOIN ref_ssh_rekening y ON x.uraian_tarif_ssh = y.id_tarif_ssh
        WHERE x.id_tarif_ssh = '.$tarif.'
        GROUP BY x.id_tarif_ssh, x.uraian_tarif_ssh'); 

      if($id == 1){
        if($cekRek[0]->jml_rekening > 0){
            $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
                FROM (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
                a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
                FROM ref_rek_5 a
                INNER JOIN ref_ssh_rekening b ON a.id_rekening = b.id_rekening
                where a.kd_rek_1=5 and a.kd_rek_2=2 and b.id_tarif_ssh ='.$tarif.') b, (SELECT @id:=0) a');
        } else {
            $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
                FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
                kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
                FROM ref_rek_5 where kd_rek_1=4 OR kd_rek_1=5 OR kd_rek_1=6) b, (SELECT @id:=0) a');
        }
      };

      if($id == 0){
        $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
            kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 where kd_rek_1=5 and (kd_rek_2=2 OR kd_rek_2=3)) b, (SELECT @id:=0) a');
      };

      if($id == 2){
        $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
            kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 where kd_rek_1=5 and kd_rek_2=1) b, (SELECT @id:=0) a');
      };
      if($id == 3){
        $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
            kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 where kd_rek_1=4) b, (SELECT @id:=0) a');
      };
      if($id == 4){
        $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
            FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
            kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
            FROM ref_rek_5 where kd_rek_1=6) b, (SELECT @id:=0) a');
      };

      return DataTables::of($refrekening)
      ->make(true);
    }

    public function getItemSSH($id_zona,$like_cari)
    {
    
       $getItem=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, b.id_zona, c.id_tarif_ssh, d.uraian_tarif_ssh, 
                c.jml_rupiah, d.id_satuan, e.uraian_satuan, q.id_sub_kelompok_ssh,q.uraian_sub_kelompok_ssh,f.jml_rekening, d.keterangan_tarif_ssh
                FROM ref_ssh_perkada a
                INNER JOIN ref_ssh_perkada_zona b ON a.id_perkada = b.id_perkada
                INNER JOIN ref_ssh_perkada_tarif c ON b.id_zona_perkada = c.id_zona_perkada
                INNER JOIN ref_ssh_tarif d ON c.id_tarif_ssh = d.id_tarif_ssh
                INNER JOIN ref_ssh_sub_kelompok q ON d.id_sub_kelompok_ssh = q.id_sub_kelompok_ssh
                INNER JOIN ref_satuan e ON d.id_satuan = e.id_satuan
                LEFT OUTER JOIN (select id_tarif_ssh, coalesce(count(id_rekening_ssh),0) as jml_rekening 
                from ref_ssh_rekening group by id_tarif_ssh) f ON d.id_tarif_ssh = f.id_tarif_ssh, (SELECT @id:=0) x  
                WHERE a.flag = 1 and a.tahun_berlaku <= '.Session::get('tahun').' and b.id_zona = '.$id_zona.' AND LOWER(d.uraian_tarif_ssh) like "%'.$like_cari.'%"');
    
       return DataTables::of($getItem)
       ->make(true);
    }
    
    public function getRekening($id,$tarif)
        {
          if($id > 0){
            $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
                FROM (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
                    a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
                    FROM ref_rek_5 a
                    INNER JOIN ref_ssh_rekening b ON a.id_rekening = b.id_rekening
                    where a.kd_rek_1=5 and a.kd_rek_2=2 and b.id_tarif_ssh ='.$tarif.') b, (SELECT @id:=0) a');
          } else {
            $refrekening=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
                FROM (SELECT id_rekening, CONCAT(kd_rek_1,".",kd_rek_2,".",
                kd_rek_3,".",kd_rek_4,".",kd_rek_5) AS kd_rekening, nama_kd_rek_5 as nm_rekening
                FROM ref_rek_5 where kd_rek_1=5 and (kd_rek_2=2 OR kd_rek_2=3)) b, (SELECT @id:=0) a');
          }
    
          return DataTables::of($refrekening)
          ->make(true);
        }
    
    public function getRekeningBTL()
        {
          
          $refrekeningBtl=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
                FROM (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
                a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
                FROM ref_rek_5 a where a.kd_rek_1=5 and a.kd_rek_2=1) b, (SELECT @id:=0) a');
    
          return DataTables::of($refrekeningBtl)
          ->make(true);
        }
    
    public function getRekeningDapat()
        {
          
          $refrekeningDapat=DB::select('SELECT (@id:=@id+1) as no_urut, b.* 
                FROM (SELECT a.id_rekening, CONCAT(a.kd_rek_1,".",a.kd_rek_2,".",
                a.kd_rek_3,".",a.kd_rek_4,".",a.kd_rek_5) AS kd_rekening, a.nama_kd_rek_5 as nm_rekening
                FROM ref_rek_5 a where a.kd_rek_1=4) b, (SELECT @id:=0) a');
    
          return DataTables::of($refrekeningDapat)
          ->make(true);
        }

    public function getProgRenstra($id_unit)
        {
           $ProgRenstra=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut,e.id_visi_renstra,d.id_misi_renstra,c.id_tujuan_renstra,b.id_sasaran_renstra,a.id_program_renstra, a.uraian_program_renstra, f.uraian_program, f.id_program
                FROM trx_renstra_program a
                INNER JOIN trx_renstra_sasaran b on a.id_sasaran_renstra = b.id_sasaran_renstra
                INNER JOIN trx_renstra_tujuan c on b.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_misi d on c.id_misi_renstra = d.id_misi_renstra
                INNER JOIN trx_renstra_visi e on d.id_visi_renstra = e.id_visi_renstra
                INNER JOIN ref_program f on a.id_program_ref = f.id_program,(SELECT @id:=0) x
                WHERE e.id_unit='.$id_unit);
        
           return DataTables::of($ProgRenstra)
           ->make(true);
        }
        
    public function getKegRenstra($id_unit,$id_program)
        {
           $KegRenstra=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut,e.id_visi_renstra,d.id_misi_renstra,c.id_tujuan_renstra,b.id_sasaran_renstra,a.id_program_renstra, f.id_kegiatan_renstra, f.id_kegiatan_ref, f.uraian_kegiatan_renstra, g.nm_kegiatan,g.id_kegiatan
                FROM trx_renstra_kegiatan f
                INNER JOIN trx_renstra_program a on f.id_program_renstra = a.id_program_renstra
                INNER JOIN trx_renstra_sasaran b on a.id_sasaran_renstra = b.id_sasaran_renstra
                INNER JOIN trx_renstra_tujuan c on b.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_misi d on c.id_misi_renstra = d.id_misi_renstra
                INNER JOIN trx_renstra_visi e on d.id_visi_renstra = e.id_visi_renstra
                INNER JOIN ref_kegiatan g on f.id_kegiatan_ref = g.id_kegiatan,(SELECT @id:=0) x
                WHERE e.id_unit='.$id_unit.' AND f.id_program_renstra='.$id_program);
        
           return DataTables::of($KegRenstra)
           ->make(true);
        }

    public function getUnitUser(Request $request){
            $unit = \App\Models\RefUnit::select();
            if(isset(Auth::user()->getUserSubUnit)){
                foreach(Auth::user()->getUserSubUnit as $data){
                    $unit->orWhere(['id_unit' => $data->kd_unit]);                
                }
            }
            $unit = $unit->get();
            if($request->ajax()){
              return json_encode($unit);
            }
        }
    
    public function getSubUnitUser(Request $request){
            $sunit = \App\Models\RefSubUnit::select();
            if(isset(Auth::user()->getUserSubUnit)){                
                foreach(Auth::user()->getUserSubUnit as $data){
                    $temp = \App\Models\UserSubUnit::select()
                            ->Where(['kd_sub' => $data->kd_sub],['kd_unit' => $data->kd_unit])
                            ->get();
                    if($temp->count() > 1){ 
                        foreach($temp as $datas){  
                            $sunit->orWhere(['id_sub_unit' => $data->kd_sub]);
                        }
                    }            
                }   
            }  
            $sunit->Where(['id_unit' => $request->kd_unit]);           
            $sunit = $sunit->get();
            if($request->ajax()){
              return json_encode($sunit);
            }
        }

      public function getUnitPelaksana()
    {
      
      $rpjmdpelaksana=DB::SELECT('SELECT (@id:=@id+1) as no_urut, b.id_unit, b.kd_unit, b.nm_unit, b.id_bidang, 
        CONCAT(RIGHT(CONCAT("0",c.kd_urusan),2),".",RIGHT(CONCAT("0",c.kd_bidang),2),".",RIGHT(CONCAT("0",b.kd_unit),2)," -- ",b.nm_unit) AS nama_display
        FROM ref_unit AS b 
        INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang, (SELECT @id:=0) x');

      return DataTables::of($rpjmdpelaksana)
            ->addColumn('action', function ($rpjmdpelaksana) {
            return '<a class="add-unitpelaksana btn btn-success btn-labeled"><span class="btn-label"><i class="fa fa-plus fa-fw fa-lg"></i></span>Tambahkan</a>';})
            ->make(true);
    }

}