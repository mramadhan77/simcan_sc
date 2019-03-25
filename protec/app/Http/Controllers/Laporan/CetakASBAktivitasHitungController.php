<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Laporan\TemplateReport As Template;
use DB;
use Response;
use Session;
use PDF;
use Auth;
use App\Models\RefASBCluster;
use App\Models\RefASBKomponen;
use App\Models\RefAsbKomponenRinci;
use App\Models\TrxAsbAktivitas;
use App\Models\TrxAsbKomponen;
use App\Models\TrxAsbKomponenRinci;
use App\Models\TrxAsbAktivitasKomponen;
use App\Models\RefSatuan;

class CetakASBAktivitasHitungController extends Controller
{

  public function printASBAktivitas($id_perhitungan,$id_aktivitas,$v1,$v2)
  {
      Template::settingPagePotrait();
      Template::headerPotrait();

      PDF::SetFont('helvetica', '', 6);

      $countrow=0;
      $totalrow=75;
      
      // column titles
      $header = array('Komponen Belanja','koef. 1','koef. 2','koef. 3','Harga (Rp.)','','Total (Rp.)');
      
      // Colors, line width and bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0.3);

      $pemda=session::get('xPemda');

      $id_hitung=$id_perhitungan;
      $akt=$id_aktivitas;
      $zona=1;
      $sum=0;
      
      //   	$ASBAktivitas = TrxASBAktivitas::select('trx_asb_aktivitas.id_satuan_2','trx_asb_aktivitas.id_satuan_1',
      //   			'trx_asb_perkada.nomor_perkada','trx_asb_perkada.tahun_berlaku','trx_asb_kelompok.uraian_kelompok_asb',
      //   			'trx_asb_sub_kelompok.uraian_sub_kelompok_asb','trx_asb_sub_sub_kelompok.uraian_sub_sub_kelompok_asb',
      //   			'trx_asb_aktivitas.nm_aktivitas_asb','trx_asb_aktivitas.volume_1','rs1.uraian_satuan as satuan1',
      //   			'trx_asb_aktivitas.volume_2','rs2.uraian_satuan as satuan2','trx_asb_perkada.tanggal_perkada')
      //   	->leftjoin('ref_satuan as rs1','trx_asb_aktivitas.id_satuan_1','=','rs1.id_satuan')
      //   	->leftjoin('ref_satuan as rs2','trx_asb_aktivitas.id_satuan_2','=','rs2.id_satuan')
      //   	->join('trx_asb_sub_sub_kelompok','trx_asb_aktivitas.id_asb_sub_sub_kelompok','=','trx_asb_sub_sub_kelompok.id_asb_sub_sub_kelompok')
      //   	->join('trx_asb_sub_kelompok','trx_asb_sub_sub_kelompok.id_asb_sub_kelompok','=','trx_asb_sub_kelompok.id_asb_sub_kelompok')
      //   	->join('trx_asb_kelompok','trx_asb_sub_kelompok.id_asb_kelompok','=','trx_asb_kelompok.id_asb_kelompok')
      //   	->join('trx_asb_perkada','trx_asb_kelompok.id_asb_perkada','=','trx_asb_perkada.id_asb_perkada')
      //   	->join('trx_asb_perhitungan_rinci','trx_asb_aktivitas.id_aktivitas_asb','=','trx_asb_perhitungan_rinci.id_aktivitas_asb')
      //   	->where('trx_asb_aktivitas.id_aktivitas_asb','=',$akt)
      //   	->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
      //   	->distinct()
      //   	->get();
       $ASBAktivitas = DB::select('select distinct trx_asb_perkada.nomor_perkada,trx_asb_perkada.tahun_berlaku,trx_asb_kelompok.uraian_kelompok_asb,
        trx_asb_sub_kelompok.uraian_sub_kelompok_asb,trx_asb_sub_sub_kelompok.uraian_sub_sub_kelompok_asb,
        trx_asb_aktivitas.nm_aktivitas_asb,trx_asb_aktivitas.volume_1,rs1.uraian_satuan as satuan1,trx_asb_aktivitas.id_satuan_1,
        trx_asb_aktivitas.volume_2,
        CASE trx_asb_aktivitas.id_satuan_2
          WHEN 0 THEN "Tidak Digunakan"
          WHEN -1 THEN "Belum Ditentukan"
          WHEN Null THEN "Kosong"
        ELSE rs2.uraian_satuan  END as satuan2,trx_asb_perkada.tanggal_perkada, trx_asb_aktivitas.sat_derivatif_1,
        CASE trx_asb_aktivitas.sat_derivatif_1
          WHEN 0 THEN "Tidak Digunakan"
          WHEN -1 THEN "Belum Ditentukan"
          WHEN Null THEN "Kosong"
        ELSE rs3.uraian_satuan END AS uraian_derivatif_1, trx_asb_aktivitas.range_max, trx_asb_aktivitas.kapasitas_max, trx_asb_aktivitas.id_satuan_2, 
        trx_asb_aktivitas.sat_derivatif_2, 
        CASE trx_asb_aktivitas.sat_derivatif_2
          WHEN 0 THEN "Tidak Digunakan"
          WHEN -1 THEN "Belum Ditentukan"
          WHEN Null THEN "Kosong"
        ELSE rs4.uraian_satuan END AS uraian_derivatif_2, trx_asb_aktivitas.range_max1, trx_asb_aktivitas.kapasitas_max1 from trx_asb_aktivitas
          left join ref_satuan as rs1 ON trx_asb_aktivitas.id_satuan_1 = rs1.id_satuan
          left join ref_satuan as rs2 ON trx_asb_aktivitas.id_satuan_2 = rs2.id_satuan
          LEFT JOIN ref_satuan as rs3 ON trx_asb_aktivitas.sat_derivatif_1 = rs3.id_satuan
          LEFT JOIN ref_satuan as rs4 ON trx_asb_aktivitas.sat_derivatif_2 = rs4.id_satuan
          join trx_asb_sub_sub_kelompok ON trx_asb_aktivitas.id_asb_sub_sub_kelompok = trx_asb_sub_sub_kelompok.id_asb_sub_sub_kelompok
          join trx_asb_sub_kelompok ON trx_asb_sub_sub_kelompok.id_asb_sub_kelompok = trx_asb_sub_kelompok.id_asb_sub_kelompok
          join trx_asb_kelompok ON trx_asb_sub_kelompok.id_asb_kelompok = trx_asb_kelompok.id_asb_kelompok
          join trx_asb_perkada ON trx_asb_kelompok.id_asb_perkada = trx_asb_perkada.id_asb_perkada
          join trx_asb_perhitungan_rinci ON trx_asb_aktivitas.id_aktivitas_asb = trx_asb_perhitungan_rinci.id_aktivitas_asb
          where trx_asb_aktivitas.id_aktivitas_asb ='.$akt.' and trx_asb_perhitungan_rinci.id_perhitungan ='.$id_perhitungan);

      PDF::SetFont('helvetica', 'B', 5);
      foreach($ASBAktivitas as $row) {
          PDF::Cell('143', '3', '', 0, 0, 'L', 0);
          PDF::Cell('15', '3', 'Nomor Perkada', 0, 0, 'L', 0);
          PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
          PDF::Cell('20', '3', $row->nomor_perkada . '  Tahun ' . $row->tahun_berlaku, 0, 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('143', '3', '', 0, 0, 'L', 0);
          PDF::Cell('15', '3', 'Tanggal Perkada', 0, 0, 'L', 0);
          PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
          PDF::Cell('20', '3', date('d F Y', strtotime($row->tanggal_perkada)), 0, 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Ln();
          $countrow++;
      }
      PDF::SetFont('helvetica', 'B', 10);
      PDF::Cell('180', 5, $pemda, 'L', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('180', 5, 'ANALISIS STANDAR BIAYA AKTIVITAS KOMPONEN RINCI ', 'L', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Ln();
      $countrow++;
      PDF::SetFont('helvetica', 'B', 9);
      foreach($ASBAktivitas as $row) {
          
          PDF::Cell('40', '5', 'Kelompok', 0, 0, 'L', 0);
          PDF::Cell('5', '5', ':', 0, 0, 'L', 0);
          PDF::Cell('100', '5', $row->uraian_kelompok_asb, 0, 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('40', '5', 'Sub Kelompok', 0, 0, 'L', 0);
          PDF::Cell('5', '5', ':', 0, 0, 'L', 0);
          PDF::Cell('100', '5', $row->uraian_sub_kelompok_asb, 0, 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('40', '5', 'Sub Sub Kelompok', 0, 0, 'L', 0);
          PDF::Cell('5', '5', ':', 0, 0, 'L', 0);
          PDF::Cell('100', '5', $row->uraian_sub_sub_kelompok_asb, 0, 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('40', '5', 'Aktivitas', 0, 0, 'L', 0);
          PDF::Cell('5', '5', ':', 0, 0, 'L', 0);
          PDF::Cell('100', '5', $row->nm_aktivitas_asb, 0, 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('40', '5', 'Indikator Output', 0, 0, 'L', 0);
          PDF::Cell('5', '5', ':', 0, 0, 'L', 0);
          if($row->id_satuan_2 != 0 && $row->id_satuan_2 != -1  && $row->id_satuan_2 != null) {
            PDF::Cell('100', '5', $v1 .' '.$row->satuan1 .' x '.$v2 .' '.$row->satuan2, 0, 0, 'L', 0);
          } else {
            PDF::Cell('100', '5', $v1 .' '.$row->satuan1 , 0, 0, 'L', 0);
          }
          PDF::Ln();
          $countrow++; 
          PDF::Cell('40', '5', '', 0, 0, 'L', 0);    
          PDF::Cell('20', '5', 'Pemicu Biaya 1 ', 0, 0, 'L', 0, '',1);
          PDF::Cell(3, 5, ':', 0, 0, 'L', 0, '',1);
          if($row->id_satuan_1 == -1 || $row->id_satuan_1 == null) { PDF::SetTextColor(204,0,0); }
          PDF::Cell('25', '5', $row->satuan1, 0, 0, 'L', 0, '',1);
          PDF::SetTextColor(0,0,0);
          PDF::Cell('15', '5', 'Derivatif 1 ', 0, 0, 'L', 0, '',1);
          PDF::Cell(3, 5, ':', 0, 0, 'L', 0, '',1);
          if($row->sat_derivatif_1 == -1 || $row->sat_derivatif_1 == null) { PDF::SetTextColor(204,0,0); }
          PDF::Cell('25', '5', $row->uraian_derivatif_1, 0, 0, 'L', 0, '',1);
          PDF::SetTextColor(0,0,0);
          PDF::Cell('10', '5', 'Range ', 0, 0, 'L', 0, '',1);
          PDF::Cell(3, 5, ':', 0, 0, 'L', 0, '',1);
          if($row->range_max <= 0 || $row->range_max== Null) { PDF::SetTextColor(204,0,0); }
          PDF::Cell('10', '5', $row->range_max, 0, 0, 'L', 0, '',1);
          PDF::SetTextColor(0,0,0);
          PDF::Cell('15', '5', 'Kapasitas ', 0, 0, 'L', 0, '',1);
          PDF::Cell(3, 5, ':', 0, 0, 'L', 0, '',1);
          if($row->kapasitas_max <= 0 || $row->kapasitas_max== Null) { PDF::SetTextColor(204,0,0); }
          PDF::Cell('10', '5', $row->kapasitas_max, 0, 0, 'L', 0, '',1);
          PDF::SetTextColor(0,0,0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('40', '5', '', 0, 0, 'L', 0);
          PDF::Cell('20', '5', 'Pemicu Biaya 2 ', 0, 0, 'L', 0, '',1);
          PDF::Cell(3, 5, ':', 0, 0, 'L', 0, '',1);
          if($row->id_satuan_2 == -1 || $row->id_satuan_2 == null) { PDF::SetTextColor(204,0,0); }
          PDF::Cell('25', '5', $row->satuan2, 0, 0, 'L', 0, '',1);
          PDF::SetTextColor(0,0,0);
          PDF::Cell('15', '5', 'Derivatif 2 ', 0, 0, 'L', 0, '',1);
          PDF::Cell(3, 5, ':', 0, 0, 'L', 0, '',1);
          if($row->sat_derivatif_2 == -1 || $row->sat_derivatif_2 == null) { PDF::SetTextColor(204,0,0); }
          PDF::Cell('25', '5', $row->uraian_derivatif_2, 0, 0, 'L', 0, '',1);
          PDF::SetTextColor(0,0,0);
          PDF::Cell('10', '5', 'Range ', 0, 0, 'L', 0, '',1);
          PDF::Cell(3, 5, ':', 0, 0, 'L', 0, '',1);
          if($row->range_max1 <= 0 || $row->range_max1== Null) { PDF::SetTextColor(204,0,0); }
          PDF::Cell('10', '5', $row->range_max1, 0, 0, 'L', 0, '',1);
          PDF::SetTextColor(0,0,0);
          PDF::Cell('15', '5', 'Kapasitas ', 0, 0, 'L', 0, '',1);
          PDF::Cell(3, 5, ':', 0, 0, 'L', 0, '',1);
          if($row->kapasitas_max1 <= 0 || $row->kapasitas_max1== Null) { PDF::SetTextColor(204,0,0); }
          PDF::Cell('10', '5', $row->kapasitas_max1, 0, 0, 'L', 0, '',1);
          PDF::SetTextColor(0,0,0);
          PDF::Ln();
          $countrow++;
          PDF::Ln();
          $countrow++;
          
          PDF::SetFont('', 'B');
          PDF::SetFont('helvetica', 'B', 8);
          PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
          // Header Column
          $wh = array(46,26,26,26,26,4,26);
          $w = array(80,100);
          $w1 = array(5,75,100);
          $w2 = array(3,3,40,26,26,26,26,4,26);
          
          $num_headers = count($header);
          for($i = 0; $i < $num_headers; ++$i) {
              PDF::Cell($wh[$i], 7, $header[$i], 0, 0, 'C', 0);
          }
          //PDF::Cell($wh[$num_headers-1], 7, $header[$num_headers-1], 'LR', 0, 'C', 1);
          PDF::Ln();
          $countrow++;
          // Color and font restoration
          
          PDF::SetFillColor(224, 235, 255);
          PDF::SetTextColor(0);
          PDF::SetFont('helvetica', '', 7);
          // Data
          $ASBKomponen = TrxAsbKomponen::select('nm_komponen_asb','id_komponen_asb')
          ->where('trx_asb_komponen.id_aktivitas_asb','=',$akt)
          ->get();
          foreach($ASBKomponen as $row2) {
              PDF::Cell($w[0], 5, $row2->nm_komponen_asb, 0, 0, 'L', 0);
              PDF::Cell($w[1], 5, '', 0, 0, 'L', 0);
              PDF::Ln();
              $countrow++;
              if($countrow>=$totalrow)
              {
                  PDF::AddPage('P');
                  $countrow=0;
                  for($i = 0; $i < $num_headers; ++$i) {
                      PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
                  }
                  PDF::Ln();
                  $countrow++;
              }
              //   			$ASBKomponenRinci = TrxAsbKomponenRinci::select ('trx_asb_komponen_rinci.id_komponen_asb','trx_asb_komponen_rinci.ket_group','trx_asb_perhitungan_rinci.id_zona')
              //   			->join('trx_asb_perhitungan_rinci','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','trx_asb_perhitungan_rinci.id_komponen_asb_rinci')
              //   			->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
              //   			->where('trx_asb_komponen_rinci.id_komponen_asb','=',$row2->id_komponen_asb)
              //   			->distinct()
              //   			->get();
              $ASBKomponenRinci = DB:: select('select distinct trx_asb_komponen_rinci.id_komponen_asb, trx_asb_komponen_rinci.ket_group,
                      trx_asb_perhitungan_rinci.id_zona FROM trx_asb_komponen_rinci
                      inner join trx_asb_perhitungan_rinci ON trx_asb_komponen_rinci.id_komponen_asb_rinci=trx_asb_perhitungan_rinci.id_komponen_asb_rinci
                      where trx_asb_perhitungan_rinci.id_perhitungan='.$id_perhitungan.' and trx_asb_komponen_rinci.id_komponen_asb='.$row2->id_komponen_asb);
              foreach($ASBKomponenRinci as $row3) {
                  PDF::Cell($w1[0], 3, '', 0, 0, 'L', 0);
                  if($row3->ket_group == null)
                  {
                      PDF::Cell($w1[1], 3, '-', 0, 0, 'L', 0);
                  }
                  else
                  {
                      PDF::Cell($w1[1], 3, $row3->ket_group, 0, 0, 'L', 0);
                  }
                  PDF::Cell($w1[2], 3, '', 0, 0, 'L', 0);
                  PDF::Ln();
                  $countrow++;
                  if($countrow>=$totalrow)
                  {
                      PDF::AddPage('P');
                      $countrow=0;
                      for($i = 0; $i < $num_headers; ++$i) {
                          PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
                      }
                      PDF::Ln();
                      $countrow++;
                  }
                  $zona=$row3->id_zona;
                  //   				$ASBKomponenRinci = DB::table('trx_asb_komponen_rinci')
                  //   				->select(DB::raw(' ref_ssh_tarif.uraian_tarif_ssh,
                  // 				case jenis_biaya when 3 then
                  //                         case trx_asb_komponen_rinci.id_satuan1 when ' .$row->id_satuan_1.' then '.$v1.'
                  //                         when ' .$row->id_satuan_2.' then '.$v2.'
                  //                         else trx_asb_komponen_rinci.koefisien1 end
                  // 				when 2 then
                  //                         case trx_asb_komponen_rinci.id_satuan1
                  //                         when ' .$row->id_satuan_1.' then CEIL('.$v1.'/trx_asb_aktivitas.range_max)
                  //                         when ' .$row->id_satuan_2.' then CEIL('.$v2.'/trx_asb_aktivitas.range_max1)
                  //                         else trx_asb_komponen_rinci.koefisien1 end
                  // 				else trx_asb_komponen_rinci.koefisien1 END  as koefisien1,
                  // 				ifnull(case jenis_biaya when 2 then case hub_driver when 1 then rs4.uraian_satuan else rs5.uraian_satuan end  else rs1.uraian_satuan end,"N/A") as satuan1,
                  // 				case jenis_biaya when 3 then
                  //                         case trx_asb_komponen_rinci.id_satuan2 when 0 then 1
                  //                         when ' .$row->id_satuan_1.' then '.$v1.'
                  //                         when ' .$row->id_satuan_2.' then '.$v2.'
                  //                         else trx_asb_komponen_rinci.koefisien2 end
                  // 				else trx_asb_komponen_rinci.koefisien2 END  as koefisien2,
                  // 				ifnull(rs2.uraian_satuan,"N/A") as satuan2,
                  //                 trx_asb_komponen_rinci.koefisien3,ifnull(rs3.uraian_satuan,"N/A") as satuan3,
                  // 				ref_ssh_perkada_tarif.jml_rupiah,case trx_asb_komponen_rinci.jenis_biaya when 1 then "Fix" when 2 then "Dependent" else "Independent" end as jenis_biaya'))
                  // 				->join('trx_asb_komponen','trx_asb_komponen_rinci.id_komponen_asb','=','trx_asb_komponen.id_komponen_asb')
                  // 				->leftjoin('ref_satuan as rs1','trx_asb_komponen_rinci.id_satuan1','=','rs1.id_satuan')
                  // 				->leftjoin('ref_satuan as rs2','trx_asb_komponen_rinci.id_satuan2','=','rs2.id_satuan')
                  // 				->leftjoin('ref_satuan as rs3','trx_asb_komponen_rinci.id_satuan3','=','rs3.id_satuan')
                  // 				->leftjoin('ref_satuan as rs4','trx_asb_komponen_rinci.sat_derivatif1','=','rs4.id_satuan')
                  // 				->leftjoin('ref_satuan as rs5','trx_asb_komponen_rinci.sat_derivatif2','=','rs5.id_satuan')
                  // 				->leftjoin('ref_ssh_tarif','trx_asb_komponen_rinci.id_tarif_ssh','=','ref_ssh_tarif.id_tarif_ssh')
                  // 				->leftjoin('ref_ssh_perkada_tarif','trx_asb_komponen_rinci.id_tarif_ssh','=','ref_ssh_perkada_tarif.id_tarif_ssh')
                  // 				->join('trx_asb_aktivitas','trx_asb_komponen.id_aktivitas_asb','=','trx_asb_aktivitas.id_aktivitas_asb')
                  // 				->join('trx_asb_perhitungan_rinci As rinci1','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','rinci1.id_komponen_asb_rinci')
                  // 				->join('trx_asb_perhitungan','rinci1.id_perhitungan','=','trx_asb_perhitungan.id_perhitungan')
                  // 				->join('ref_ssh_perkada_zona','ref_ssh_perkada_tarif.id_zona_perkada','=','ref_ssh_perkada_zona.id_zona_perkada')
                  // 				->join('ref_ssh_perkada','ref_ssh_perkada_zona.id_perkada','=','ref_ssh_perkada.id_perkada')
                  // 				->where('trx_asb_perhitungan.id_perhitungan','=',$id_perhitungan)
                  // 				->where('trx_asb_komponen.id_komponen_asb','=',$row3->id_komponen_asb)
                  // 				->where('trx_asb_komponen_rinci.ket_group','=',$row3->ket_group)
                  // 				->where('ref_ssh_perkada_zona.id_zona','=',$row3->id_zona)
                  // 				->get();
                  // $ASBKomponenRinci = DB::select('select ref_ssh_tarif.uraian_tarif_ssh,
                  //     case trx_asb_komponen_rinci.jenis_biaya
                  //       when 1 then trx_asb_komponen_rinci.koefisien1
                  //       else
                  //         case trx_asb_komponen_rinci.hub_driver
                  //          when 1 then '.$v1.'
                  //          when 2 then '.$v2.'
                  //          when 3 then '.$v1.'
                  //          when 4 then CEIL('.$v1.'/trx_asb_aktivitas.range_max)
                  //          when 5 then CEIL('.$v2.'/trx_asb_aktivitas.range_max1)
                  //          when 6 then CEIL('.$v1.'/trx_asb_aktivitas.range_max)
                  //          when 7 then CEIL('.$v1.'/trx_asb_aktivitas.range_max)
                  //          when 8 then CEIL('.$v2.'/trx_asb_aktivitas.range_max1)
                  //         else trx_asb_komponen_rinci.koefisien1
                  //         end
                  //       end as koefisien1,
                  //     ifnull(rs1.uraian_satuan,"N/A") as satuan1,
                  //     case trx_asb_komponen_rinci.jenis_biaya
                  //       when 1 then trx_asb_komponen_rinci.koefisien2
                  //       else
                  //         case trx_asb_komponen_rinci.hub_driver
                  //          when 3 then trx_asb_komponen_rinci.koefisien1
                  //          when 6 then CEIL('.$v2.'/trx_asb_aktivitas.range_max1)
                  //          when 7 then '.$v2.'
                  //          when 8 then '.$v1.'
                  //         else  trx_asb_komponen_rinci.koefisien2
                  //         end
                  //       end as koefisien2,
                  //     ifnull(rs2.uraian_satuan,"N/A") as satuan2,
                  //     trx_asb_komponen_rinci.koefisien3,
                  //     ifnull(rs3.uraian_satuan,"N/A") as satuan3,
                  //     ref_ssh_perkada_tarif.jml_rupiah,case trx_asb_komponen_rinci.jenis_biaya when 1 then "Fix" else "Variable" end as jenis_biaya FROM trx_asb_komponen_rinci
                  //     inner join trx_asb_komponen ON trx_asb_komponen_rinci.id_komponen_asb = trx_asb_komponen.id_komponen_asb
                  //     left join ref_satuan as rs1 ON trx_asb_komponen_rinci.id_satuan1 = rs1.id_satuan
                  //     left join ref_satuan as rs2 ON trx_asb_komponen_rinci.id_satuan2 = rs2.id_satuan
                  //     left join ref_satuan as rs3 ON trx_asb_komponen_rinci.id_satuan3 = rs3.id_satuan
                  //     left join ref_satuan as rs4 ON trx_asb_komponen_rinci.sat_derivatif1 = rs4.id_satuan
                  //     left join ref_satuan as rs5 ON trx_asb_komponen_rinci.sat_derivatif2 = rs5.id_satuan
                  //     left join ref_ssh_tarif ON trx_asb_komponen_rinci.id_tarif_ssh = ref_ssh_tarif.id_tarif_ssh
                  //     left join ref_ssh_perkada_tarif ON trx_asb_komponen_rinci.id_tarif_ssh = ref_ssh_perkada_tarif.id_tarif_ssh
                  //     join trx_asb_aktivitas ON trx_asb_komponen.id_aktivitas_asb = trx_asb_aktivitas.id_aktivitas_asb
                  //     join trx_asb_perhitungan_rinci As rinci1 ON trx_asb_komponen_rinci.id_komponen_asb_rinci = rinci1.id_komponen_asb_rinci
                  //     join trx_asb_perhitungan ON rinci1.id_perhitungan = trx_asb_perhitungan.id_perhitungan
                  //     join ref_ssh_perkada_zona ON ref_ssh_perkada_tarif.id_zona_perkada = ref_ssh_perkada_zona.id_zona_perkada
                  //     join ref_ssh_perkada ON ref_ssh_perkada_zona.id_perkada = ref_ssh_perkada.id_perkada
                  //     where trx_asb_perhitungan.id_perhitungan = '.$id_perhitungan.' and trx_asb_komponen.id_komponen_asb = '.$row3->id_komponen_asb.' and ref_ssh_perkada_zona.id_zona = '.$row3->id_zona.' and trx_asb_komponen_rinci.ket_group = "'. $row3->ket_group.'"');

                  $ASBKomponenRinci = DB::table('trx_asb_komponen_rinci')
                  ->select(DB::raw('ref_ssh_tarif.uraian_tarif_ssh,
                              case trx_asb_komponen_rinci.jenis_biaya
                                when 1 then trx_asb_komponen_rinci.koefisien1
                                else 
                                  case coalesce(trx_asb_komponen_rinci.hub_driver,0)
                                   when 1 then '.$v1.'
                                   when 2 then '.$v2.'
                                   when 3 then '.$v1.'
                                   when 4 then CEIL('.$v1.'/trx_asb_aktivitas.range_max)
                                   when 5 then CEIL('.$v2.'/trx_asb_aktivitas.range_max1)
                                   when 6 then CEIL('.$v1.'/trx_asb_aktivitas.range_max)
                                   when 7 then CEIL('.$v1.'/trx_asb_aktivitas.range_max)
                                   when 8 then CEIL('.$v2.'/trx_asb_aktivitas.range_max1)
                                  else trx_asb_komponen_rinci.koefisien1
                                  end 
                                end as koefisien1,
                              ifnull(rs1.uraian_satuan,"N/A") as satuan1,
                              case trx_asb_komponen_rinci.jenis_biaya
                                when 1 then trx_asb_komponen_rinci.koefisien2
                                else 
                                  case coalesce(trx_asb_komponen_rinci.hub_driver,0)
                                   when 3 then '.$v2.'
                                   when 6 then CEIL('.$v2.'/trx_asb_aktivitas.range_max1)
                                   when 7 then '.$v2.'
                                   when 8 then '.$v1.'
                                  else  trx_asb_komponen_rinci.koefisien2
                                  end 
                                end as koefisien2,
                              ifnull(rs2.uraian_satuan,"N/A") as satuan2,
                              trx_asb_komponen_rinci.koefisien3,
                              ifnull(rs3.uraian_satuan,"N/A") as satuan3,
                              trx_asb_perhitungan_rinci.harga_satuan as jml_rupiah,case trx_asb_komponen_rinci.jenis_biaya when 1 then "Fix" else "Variable" end as jenis_biaya'))
                ->join('trx_asb_komponen','trx_asb_komponen_rinci.id_komponen_asb','=','trx_asb_komponen.id_komponen_asb')
                ->leftjoin('ref_satuan as rs1','trx_asb_komponen_rinci.id_satuan1','=','rs1.id_satuan')
                ->leftjoin('ref_satuan as rs2','trx_asb_komponen_rinci.id_satuan2','=','rs2.id_satuan')
                ->leftjoin('ref_satuan as rs3','trx_asb_komponen_rinci.id_satuan3','=','rs3.id_satuan')
                ->leftjoin('ref_satuan as rs4','trx_asb_komponen_rinci.sat_derivatif1','=','rs4.id_satuan')
                ->leftjoin('ref_satuan as rs5','trx_asb_komponen_rinci.sat_derivatif2','=','rs5.id_satuan')
                ->leftjoin('ref_ssh_tarif','trx_asb_komponen_rinci.id_tarif_ssh','=','ref_ssh_tarif.id_tarif_ssh')
                ->join('trx_asb_aktivitas','trx_asb_komponen.id_aktivitas_asb','=','trx_asb_aktivitas.id_aktivitas_asb')
                ->join('trx_asb_perhitungan_rinci','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','trx_asb_perhitungan_rinci.id_komponen_asb_rinci')
                ->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
                ->where('trx_asb_komponen.id_komponen_asb','=',$row3->id_komponen_asb)
                ->where('trx_asb_komponen_rinci.ket_group','=',$row3->ket_group)
                ->where('trx_asb_komponen.id_aktivitas_asb','=',$akt)
                ->where('trx_asb_perhitungan_rinci.id_zona','=',$zona)
                ->distinct()
                ->get();
                  
                  
                  foreach($ASBKomponenRinci as $row4) {
                      $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
                          +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
                          +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
                          +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;

                          PDF::MultiCell($w2[0], $height, '', 0, 'L', 0, 0);
                          PDF::MultiCell($w2[1], $height, '', 0, 'L', 0, 0);
                          PDF::MultiCell($w2[2], $height, $row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya, 0, 'L', 0, 0);
                          PDF::MultiCell($w2[3], $height, number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1, 0, 'C', 0, 0);
                          PDF::MultiCell($w2[4], $height, number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2, 0, 'C', 0, 0);
                          PDF::MultiCell($w2[5], $height, number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3, 0, 'C', 0, 0);
                          PDF::MultiCell($w2[6], $height, number_format($row4->jml_rupiah,2,',','.'), 0, 'C', 0, 0);
                          PDF::MultiCell($w2[7], $height, '=', 0, 'C', 0, 0);
                          PDF::MultiCell($w2[8], $height, number_format($row4->koefisien1*$row4->koefisien2*$row4->koefisien3*$row4->jml_rupiah,2,',','.'), 0, 'R', 0, 0);
                          PDF::Ln();                          
                          $countrow++;
                          $countrow=$countrow+($height/4);
                          if($countrow>=$totalrow)
                          {
                              PDF::AddPage('P');
                              $countrow=0;
                              for($i = 0; $i < $num_headers; ++$i) {
                                  PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
                              }
                              PDF::Ln();
                              $countrow++;
                          }
                          $sum=$sum+$row4->koefisien1*$row4->koefisien2*$row4->koefisien3*$row4->jml_rupiah;
                  }
              }
              
          }
      }
      PDF::SetFont('helvetica', 'B', 7);
      PDF::MultiCell(154, 5, 'Total   = ', 0, 'R', 0, 0);
      PDF::MultiCell(26, 5, number_format($sum,2,',','.'), 0, 'R', 0, 0);
      PDF::Ln();
      $countrow++;
      if($countrow>=$totalrow)
      {
          PDF::AddPage('P');
          $countrow=0;
          $countrow++;
      }
      
    $ASBKomponenRinci1 = DB::SELECT('SELECT CAST(sum(IFNULL(a.koefisien1,1)*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.harga_satuan) as Decimal(15,3)) as koef_fix
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN trx_asb_perhitungan_rinci g ON a.id_komponen_asb_rinci = g.id_komponen_asb_rinci
        INNER JOIN trx_asb_perhitungan h ON g.id_perhitungan = h.id_perhitungan
        WHERE g.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya = 1 AND g.id_perhitungan = '.$id_perhitungan.'
        GROUP BY g.id_zona, c.id_aktivitas_asb, a.jenis_biaya');
      
    $ASBKomponenRinci2 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.harga_satuan) as Decimal(15,3)) as koef_v1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN trx_asb_perhitungan_rinci g ON a.id_komponen_asb_rinci = g.id_komponen_asb_rinci
        INNER JOIN trx_asb_perhitungan h ON g.id_perhitungan = h.id_perhitungan
        WHERE g.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 1 AND g.id_perhitungan = '.$id_perhitungan.'
        GROUP BY g.id_zona, c.id_aktivitas_asb, a.jenis_biaya');
      
    $ASBKomponenRinci3 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.harga_satuan) as Decimal(15,3)) as koef_v2
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN trx_asb_perhitungan_rinci g ON a.id_komponen_asb_rinci = g.id_komponen_asb_rinci
        INNER JOIN trx_asb_perhitungan h ON g.id_perhitungan = h.id_perhitungan
        WHERE g.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 2 AND g.id_perhitungan = '.$id_perhitungan.'
        GROUP BY g.id_zona, c.id_aktivitas_asb, a.jenis_biaya');
      
    $ASBKomponenRinci4 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien3,1)*g.harga_satuan) as Decimal(15,3)) as koef_v1v2
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN trx_asb_perhitungan_rinci g ON a.id_komponen_asb_rinci = g.id_komponen_asb_rinci
        INNER JOIN trx_asb_perhitungan h ON g.id_perhitungan = h.id_perhitungan
        WHERE g.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 3 AND g.id_perhitungan = '.$id_perhitungan.'
        GROUP BY g.id_zona, c.id_aktivitas_asb, a.jenis_biaya');
      
    $ASBKomponenRinci5 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.harga_satuan) as Decimal(15,3)) as koef_d1, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN trx_asb_perhitungan_rinci g ON a.id_komponen_asb_rinci = g.id_komponen_asb_rinci
        INNER JOIN trx_asb_perhitungan h ON g.id_perhitungan = h.id_perhitungan
        WHERE g.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 4 AND g.id_perhitungan = '.$id_perhitungan.'
        GROUP BY g.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');
      
    $ASBKomponenRinci6 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.harga_satuan) as Decimal(15,3)) as koef_d2, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN trx_asb_perhitungan_rinci g ON a.id_komponen_asb_rinci = g.id_komponen_asb_rinci
        INNER JOIN trx_asb_perhitungan h ON g.id_perhitungan = h.id_perhitungan
        WHERE g.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 5 AND g.id_perhitungan = '.$id_perhitungan.'
        GROUP BY g.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');

    $ASBKomponenRinci7 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien3,1)*g.harga_satuan) as Decimal(15,3)) as koef_d1d2, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN trx_asb_perhitungan_rinci g ON a.id_komponen_asb_rinci = g.id_komponen_asb_rinci
        INNER JOIN trx_asb_perhitungan h ON g.id_perhitungan = h.id_perhitungan
        WHERE g.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 6 AND g.id_perhitungan = '.$id_perhitungan.'
        GROUP BY g.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');

    $ASBKomponenRinci8 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien3,1)*g.harga_satuan) as Decimal(15,3)) as koef_v1d2, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN trx_asb_perhitungan_rinci g ON a.id_komponen_asb_rinci = g.id_komponen_asb_rinci
        INNER JOIN trx_asb_perhitungan h ON g.id_perhitungan = h.id_perhitungan
        WHERE g.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 7 AND g.id_perhitungan = '.$id_perhitungan.'
        GROUP BY g.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');

    $ASBKomponenRinci9 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien3,1)*g.harga_satuan) as Decimal(15,3)) as koef_d1v2, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN trx_asb_perhitungan_rinci g ON a.id_komponen_asb_rinci = g.id_komponen_asb_rinci
        INNER JOIN trx_asb_perhitungan h ON g.id_perhitungan = h.id_perhitungan
        WHERE g.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 8 AND g.id_perhitungan = '.$id_perhitungan.'
        GROUP BY g.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');
      
      //return count($ASBKomponenRinci2);
      PDF::SetFont('helvetica', 'B', 8);
      
      PDF::Cell(array_sum($w), 0, '', 'T');
      PDF::Ln();
      
      
      if(count($ASBKomponenRinci1)==0)
      {
        $rincikoef1="";
        $rincikoef1b=0;
        $rincivar1="";
        
      }
      else
      {
        $rincikoef1= number_format($ASBKomponenRinci1[0]->koef_fix, 2, ',', '.') ;
        $rincikoef1b=$ASBKomponenRinci1[0]->koef_fix;
        $rincivar1="";
      }

      if(count($ASBKomponenRinci2)==0)
      {
        $rincikoef2a="";
        $rincikoef2b=0;
        $rincikoef2c="";
        $rincikoef2="";
        $rincivar2="";
        
      }
      else
      {
        if(count($ASBKomponenRinci1)==0){
              $rincivar2="";
        } else {
              $rincivar2=" + ";}
        $rincikoef2="(".number_format($ASBKomponenRinci2[0]->koef_v1, 2, ',', '.'). "x". $row->satuan1.")";
        $rincikoef2c="(".number_format($ASBKomponenRinci2[0]->koef_v1, 2, ',', '.'). "x". number_format($v1, 0, ',', '.').")";
        $rincikoef2a=number_format($ASBKomponenRinci2[0]->koef_v1*$v1, 2, ',', '.');
        $rincikoef2b=$ASBKomponenRinci2[0]->koef_v1*$v1;
      }


      if(count($ASBKomponenRinci3)==0)
      {
        $rincikoef3="";
        $rincikoef3a="";
        $rincikoef3b=0;
        $rincikoef3c="";
        $rincivar3="";        
      }
      else
      {
        if(count($ASBKomponenRinci2)==0){
              $rincivar3="";
        } else {
              $rincivar3=" + ";}
        $rincikoef3="(".number_format($ASBKomponenRinci3[0]->koef_v2, 2, ',', '.')."x".$row->satuan2.")" ;
        $rincikoef3c="(".number_format($ASBKomponenRinci3[0]->koef_v2, 2, ',', '.'). "x". number_format($v2, 0, ',', '.').")";
        $rincikoef3a=number_format($ASBKomponenRinci3[0]->koef_v2*$v2, 2, ',', '.');
        $rincikoef3b=$ASBKomponenRinci3[0]->koef_v2*$v2;        
      }
      
      if(count($ASBKomponenRinci4)==0)
      {
        $rincikoef4="";
        $rincikoef4a="";
        $rincikoef4b=0;
        $rincikoef4c="";
        $rincivar4="";        
      }
      else
      {
        $rincivar4=" + ";
        $rincikoef4="(".number_format($ASBKomponenRinci4[0]->koef_v1v2, 2, ',', '.')."x".$row->satuan1."x".$row->satuan2.")" ;
        $rincikoef4c="(".number_format($ASBKomponenRinci4[0]->koef_v1v2, 2, ',', '.'). "x". number_format($v1, 0, ',', '.')."x". number_format($v2,0, ',', '.').")";
        $rincikoef4a=number_format($ASBKomponenRinci4[0]->koef_v1v2*$v1*$v2, 2, ',', '.');
        $rincikoef4b=$ASBKomponenRinci4[0]->koef_v1v2*$v1*$v2;        
      }
      
      if(count($ASBKomponenRinci5)==0)
      {
        $rincikoef5="";
        $rincikoef5a="";
        $rincikoef5b=0;
        $rincikoef5c="";
        $rincivar5="";        
      }
      else
      {
        $rincivar5=" + ";
        $rincikoef5="(".number_format($ASBKomponenRinci5[0]->koef_d1, 2, ',', '.')."x".$row->uraian_derivatif_1.")" ;
        $rincikoef5a=number_format($ASBKomponenRinci5[0]->koef_d1*(ceil($v1/$ASBKomponenRinci5[0]->range_max)), 2, ',', '.'); 
        $rincikoef5c="(".number_format($ASBKomponenRinci5[0]->koef_d1, 2, ',', '.'). "x". number_format(ceil($v1/$ASBKomponenRinci5[0]->range_max), 0, ',', '.').")";
        $rincikoef5b=$ASBKomponenRinci5[0]->koef_d1*(ceil($v1/$ASBKomponenRinci5[0]->range_max));

      }
      
      if(count($ASBKomponenRinci6)==0)
      {
        $rincikoef6="";
        $rincikoef6a="";
        $rincikoef6b=0;
        $rincikoef6c="";
        $rincivar6="";        
      }
      else
      {
        $rincivar6=" + ";
        $rincikoef6="(".number_format($ASBKomponenRinci6[0]->koef_d2, 2, ',', '.')."x".$row->uraian_derivatif_2.")" ;
        $rincikoef6a=number_format($ASBKomponenRinci6[0]->koef_d2*(ceil($v2/$ASBKomponenRinci6[0]->range_max1)), 2, ',', '.');        
        $rincikoef6c="(".number_format($ASBKomponenRinci6[0]->koef_d2, 2, ',', '.'). "x". number_format(ceil($v2/$ASBKomponenRinci6[0]->range_max1), 0, ',', '.').")";
        $rincikoef6b=$ASBKomponenRinci6[0]->koef_d2*(ceil($v2/$ASBKomponenRinci6[0]->range_max1));        
      }

      if(count($ASBKomponenRinci7)==0)
      {
        $rincikoef7="";
        $rincikoef7a="";
        $rincikoef7b=0;
        $rincikoef7c="";
        $rincivar7="";        
      }
      else
      {
        $rincivar7=" + ";
        $rincikoef7="(".number_format($ASBKomponenRinci7[0]->koef_d1d2, 2, ',', '.')."x".$row->uraian_derivatif_1."x".$row->uraian_derivatif_2.")" ; 
        $rincikoef7a=number_format($ASBKomponenRinci7[0]->koef_d1d2*(ceil($v1/$ASBKomponenRinci7[0]->range_max))*(ceil($v2/$ASBKomponenRinci7[0]->range_max1)), 2, ',', '.');         
        $rincikoef7c="(".number_format($ASBKomponenRinci7[0]->koef_d1d2, 2, ',', '.'). "x". number_format(ceil($v1/$ASBKomponenRinci7[0]->range_max), 0, ',', '.')."x". number_format(ceil($v2/$ASBKomponenRinci7[0]->range_max1),0, ',', '.').")";
        $rincikoef7b=$ASBKomponenRinci7[0]->koef_d1d2*(ceil($v1/$ASBKomponenRinci7[0]->range_max))*(ceil($v2/$ASBKomponenRinci7[0]->range_max1));       
      }

      if(count($ASBKomponenRinci8)==0)
      {
        $rincikoef8="";
        $rincikoef8a="";
        $rincikoef8b=0;
        $rincikoef8c="";
        $rincivar8="";        
      }
      else
      {
        $rincivar8=" + ";
        $rincikoef8="(".number_format($ASBKomponenRinci8[0]->koef_v1d2, 2, ',', '.')."x".$row->uraian_derivatif_1."x".$row->satuan2.")" ;
        $rincikoef8a=number_format($ASBKomponenRinci8[0]->koef_v1d2*$v2*(ceil($v1/$ASBKomponenRinci8[0]->range_max)), 2, ',', '.');         
        $rincikoef8c="(".number_format($ASBKomponenRinci8[0]->koef_v1d2, 2, ',', '.'). "x". number_format(ceil($v1/$ASBKomponenRinci8[0]->range_max), 0, ',', '.')."x". number_format($v2,0, ',', '.').")"; 
        $rincikoef8b=$ASBKomponenRinci8[0]->koef_v1d2*$v2*(ceil($v1/$ASBKomponenRinci8[0]->range_max));       
      }

      if(count($ASBKomponenRinci9)==0)
      {
        $rincikoef9="";
        $rincikoef9a="";
        $rincikoef9b=0;
        $rincikoef9c="";
        $rincivar9="";        
      }
      else
      {
        $rincivar9=" + ";
        $rincikoef9="(".number_format($ASBKomponenRinci9[0]->koef_d1v2, 2, ',', '.')."x".$row->satuan1."x".$row->uraian_derivatif_2.")" ;
        $rincikoef9a=number_format($ASBKomponenRinci9[0]->koef_d1v2*$v1*(ceil($v2/$ASBKomponenRinci9[0]->range_max1)), 2, ',', '.');         
        $rincikoef9c="(".number_format($ASBKomponenRinci9[0]->koef_d1v2, 2, ',', '.'). "x". number_format($v1, 0, ',', '.')."x". number_format(ceil($v2/$ASBKomponenRinci9[0]->range_max1),0, ',', '.').")"; 
        $rincikoef9b=$ASBKomponenRinci9[0]->koef_d1v2*$v1*(ceil($v2/$ASBKomponenRinci9[0]->range_max1));        
      }
      

      $nilaiTotal=$rincikoef1b+$rincikoef2b+$rincikoef3b+$rincikoef4b+$rincikoef5b+$rincikoef6b+$rincikoef7b+$rincikoef8b+$rincikoef9b;

      PDF::SetTextColor(0,0,0);
      PDF::Cell(0, 5, 'Rumus Perhitungan ASB :', 0, 0, 'L', 0, '',1);
      PDF::Ln();
      $countrow++;
      if($countrow>=$totalrow)
      {
        PDF::AddPage('P');
        $countrow=0;
        $countrow++;
      }
      PDF::SetTextColor(34,68,136);
      PDF::Cell(0, 5, 'Y = '. $rincikoef1.''.$rincivar2.''. $rincikoef2.''.$rincivar3.''. $rincikoef3.''.$rincivar4.''. $rincikoef4.''.$rincivar5.''. $rincikoef5.''.$rincivar6.''. $rincikoef6.''.$rincivar7.''. $rincikoef7.''.$rincivar8.''. $rincikoef8.''.$rincivar9.''. $rincikoef9.'',  0, 0, 'L', 0, '',1);
      PDF::Ln();
      PDF::Ln();
      PDF::SetTextColor(0,0,0);
      PDF::Cell(0, 5, 'Cara Perhitungan Simulasi ASB :', 0, 0, 'L', 0, '',1);
      PDF::Ln();

      PDF::SetTextColor(34,68,136);
      PDF::Cell(0, 5, 'Y = '. $rincikoef1.''.$rincivar2.''. $rincikoef2c.''.$rincivar3.''. $rincikoef3c.''.$rincivar4.''. $rincikoef4c.''.$rincivar5.''. $rincikoef5c.''.$rincivar6.''. $rincikoef6c.''.$rincivar7.''. $rincikoef7c.''.$rincivar8.''. $rincikoef8c.''.$rincivar9.''. $rincikoef9c.'',  0, 0, 'L', 0, '',1);
      PDF::Ln();

      // PDF::Cell('40', '3', '', 0, 0, 'L', 0);
      PDF::Cell(0, 5, 'Y = '. $rincikoef1.''.$rincivar2.''. $rincikoef2a.''.$rincivar3.''. $rincikoef3a.''.$rincivar4.''. $rincikoef4a.''.$rincivar5.''. $rincikoef5a.''.$rincivar6.''. $rincikoef6a.''.$rincivar7.''. $rincikoef7a.''.$rincivar8.''. $rincikoef8a.''.$rincivar9.''. $rincikoef9a.'', 0, 0, 'L', 0, '',1);
      PDF::Ln();

      // PDF::Cell('40', '3', '', 0, 0, 'L', 0);
      PDF::Cell(0, 5, 'Y = '. number_format($nilaiTotal, 2, ',','.'),  0, 0, 'L', 0, '',1);
      PDF::Ln();
      
			// ---------------------------------------------------------
			
			// close and output PDF document
			PDF::Output('ASBAktivitasKomponenRinciHitung.pdf', 'I');
  }
  
  
  
}
