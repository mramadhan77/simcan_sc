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
use App\Models\RefAsbKomponen;
use App\Models\RefAsbKomponenRinci;
use App\Models\TrxAsbAktivitas;
use App\Models\TrxAsbKomponen;
use App\Models\TrxAsbKomponenRinci;
use App\Models\TrxAsbAktivitasKomponen;
use App\Models\RefSatuan;

class CetakASBAktivitasHitung2Controller extends Controller
{

  public function printASBAktivitas($id_aktivitas,$v1,$v2)
  {
  	
    Template::settingPagePotrait();
    Template::headerPotrait();

  	$countrow=0;
  	$totalrow=30;
  	
  	PDF::SetFont('helvetica', '', 6);

  	$header = array('Komponen Belanja','koef. 1','koef. 2','koef. 3','Harga (Rp.)','','Total (Rp.)');
  	
  	// Colors, line width and bold font
  	PDF::SetFillColor(200, 200, 200);
  	PDF::SetTextColor(0);
  	PDF::SetDrawColor(255, 255, 255);
  	PDF::SetLineWidth(0.3);

  	$pemda=Session::get('xPemda');
  	$akt=$id_aktivitas;
  	$sum=0;

    $ASBAktivitas = DB::SELECT('SELECT DISTINCT e.nomor_perkada,e.tahun_berlaku,d.uraian_kelompok_asb,
        c.uraian_sub_kelompok_asb,b.uraian_sub_sub_kelompok_asb,a.nm_aktivitas_asb,a.volume_1,e.id_asb_perkada,
        CASE 
          WHEN a.id_satuan_1 = 0 THEN "Tidak Digunakan"
          WHEN a.id_satuan_1 = -1 THEN "Belum Ditentukan"
          WHEN a.id_satuan_1 IS Null THEN "Kosong"
        ELSE rs1.uraian_satuan END as satuan1, a.id_satuan_1, a.volume_2,
        CASE 
          WHEN a.id_satuan_2 = 0 THEN "Tidak Digunakan"
          WHEN a.id_satuan_2 = -1 THEN "Belum Ditentukan"
          WHEN a.id_satuan_2 IS Null THEN "Kosong"
        ELSE rs2.uraian_satuan  END as satuan2,e.tanggal_perkada, a.sat_derivatif_1,
        CASE 
          WHEN a.sat_derivatif_1 = 0 THEN "Tidak Digunakan"
          WHEN a.sat_derivatif_1 = -1 THEN "Belum Ditentukan"
          WHEN a.sat_derivatif_1 IS Null THEN "Kosong"
        ELSE rs3.uraian_satuan END AS uraian_derivatif_1, a.range_max, a.kapasitas_max, a.id_satuan_2, 
        a.sat_derivatif_2, 
        CASE 
          WHEN a.sat_derivatif_2 = 0 THEN "Tidak Digunakan"
          WHEN a.sat_derivatif_2 = -1 THEN "Belum Ditentukan"
          WHEN a.sat_derivatif_2 IS Null THEN "Kosong"
        ELSE rs4.uraian_satuan END AS uraian_derivatif_2, a.range_max1, a.kapasitas_max1 
        FROM trx_asb_aktivitas as a
        LEFT JOIN ref_satuan as rs1 ON a.id_satuan_1 = rs1.id_satuan
        LEFT JOIN ref_satuan as rs2 ON a.id_satuan_2 = rs2.id_satuan
        LEFT JOIN ref_satuan as rs3 ON a.sat_derivatif_1 = rs3.id_satuan
        LEFT JOIN ref_satuan as rs4 ON a.sat_derivatif_2 = rs4.id_satuan
        INNER JOIN  trx_asb_sub_sub_kelompok  as b ON a.id_asb_sub_sub_kelompok = b.id_asb_sub_sub_kelompok
        INNER JOIN  trx_asb_sub_kelompok as c ON b.id_asb_sub_kelompok = c.id_asb_sub_kelompok
        INNER JOIN  trx_asb_kelompok as d ON c.id_asb_kelompok = d.id_asb_kelompok
        INNER JOIN  trx_asb_perkada  as e ON d.id_asb_perkada = e.id_asb_perkada
        WHERE a.id_aktivitas_asb ='.$akt);

  	PDF::SetFont('helvetica', 'B', 5);
  	foreach($ASBAktivitas as $row) {
      $ASBZona = DB::SELECT('SELECT a.id_zona FROM ref_ssh_perkada_zona as a 
        INNER JOIN ref_ssh_perkada as b WHERE b.tahun_berlaku='.Session::get('tahun').'
        GROUP BY a.id_zona, b.tahun_berlaku ORDER BY a.id_zona LIMIT 1');

        foreach($ASBZona as $rowZona) {
            $zona=$rowZona->id_zona;    
      }
  		PDF::Cell('135', '3', '', 0, 0, 'L', 0);
      PDF::Cell('20', '3', 'Nomor Perkada', 0, 0, 'L', 0);
      PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
      PDF::Cell('20', '3', $row->nomor_perkada , 0, 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('135', '3', '', 0, 0, 'L', 0);
      PDF::Cell('20', '3', 'Tanggal Perkada', 0, 0, 'L', 0);
      PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
      PDF::Cell('20', '3', date('d F Y', strtotime($row->tanggal_perkada)), 0, 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('135', '3', '', 0, 0, 'L', 0);
      PDF::Cell('20', '3', 'Tahun SSH', 0, 0, 'L', 0);
      PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
      PDF::Cell('20', '3', Session::get('tahun'), 0, 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      PDF::Ln();
      $countrow++;
  	}
  	PDF::SetFont('helvetica', 'B', 10);
  	PDF::Cell('180', 5, 'ANALISIS STANDAR BIAYA AKTIVITAS KOMPONEN RINCI', 'L', 0, 'C', 0);
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

  		$wh = array(46,26,26,26,26,4,26);
  		$w = array(80,100);
  		$w1 = array(5,75,100);
  		$w2 = array(3,3,40,26,26,26,26,4,26);


  		$num_headers = count($header);
  		for($i = 0; $i < $num_headers; ++$i) {
  			PDF::Cell($wh[$i], 7, $header[$i], 0, 0, 'C', 0);
  		}

  		PDF::Ln();
  		$countrow++;
  		
  		PDF::SetFillColor(224, 235, 255);
  		PDF::SetTextColor(0);
  		PDF::SetFont('helvetica', '', 7);

      $ASBKomponen = DB::SELECT('SELECT nm_komponen_asb,id_komponen_asb FROM trx_asb_komponen
                    WHERE trx_asb_komponen.id_aktivitas_asb ='.$akt);

  		foreach($ASBKomponen as $row2) {
  			PDF::Cell($w[0], 5, $row2->nm_komponen_asb, 0, 0, 'L', 0);
  			PDF::Cell($w[1], 5, '', 0, 0, 'L', 0);
  			PDF::Ln();
  			$countrow++;
  			if($countrow>=$totalrow)
  			{
  				PDF::AddPage('P','A4');
  				$countrow=0;
  				for($i = 0; $i < $num_headers; ++$i) {
  					PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
  				}
  				PDF::Ln();
  				$countrow++;
  			}

        $ASBKomponenRinci = DB:: SELECT('SELECT DISTINCT a.id_komponen_asb, COALESCE(a.ket_group,"-") as  ket_group
          FROM trx_asb_komponen_rinci AS a WHERE a.id_komponen_asb='.$row2->id_komponen_asb);

  			foreach($ASBKomponenRinci as $row3) {
  				PDF::Cell($w1[0], 5, '', 0, 0, 'L', 0);
  				if($row3->ket_group == null)
  				{
  					PDF::Cell($w1[1], 5, '-', 0, 0, 'L', 0);
  				}
  				else
  				{
  					PDF::Cell($w1[1], 5, $row3->ket_group, 0, 0, 'L', 0);
  				}
  				PDF::Cell($w1[2], 5, '', 0, 0, 'L', 0);
  				PDF::Ln();
  				$countrow++;
  				if($countrow>=$totalrow)
  				{
  					PDF::AddPage('P','A4');
  					$countrow=0;
  					for($i = 0; $i < $num_headers; ++$i) {
  						PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
  					}
  					PDF::Ln();
  					$countrow++;
  				}

        $ASBKomponenRinci =DB::SELECT('SELECT DISTINCT c.uraian_tarif_ssh,a.id_komponen_asb_rinci,
                      case a.jenis_biaya
                        when 1 then a.koefisien1
                        else 
                          case coalesce(a.hub_driver,0)
                           when 1 then '.$v1.'
                           when 2 then '.$v2.'
                           when 3 then '.$v1.'
                           when 4 then CEIL('.$v1.'/d.range_max)
                           when 5 then CEIL('.$v2.'/d.range_max1)
                           when 6 then CEIL('.$v1.'/d.range_max)
                           when 7 then CEIL('.$v1.'/d.range_max)
                           when 8 then CEIL('.$v2.'/d.range_max1)
                          else a.koefisien1
                          end 
                        end as koefisien1,
                      ifnull(rs1.uraian_satuan,"N/A") as satuan1,
                      case a.jenis_biaya
                        when 1 then a.koefisien2
                        else 
                          case coalesce(a.hub_driver,0)
                           when 3 then '.$v2.'
                           when 6 then CEIL('.$v2.'/d.range_max1)
                           when 7 then '.$v2.'
                           when 8 then '.$v1.'
                          else  a.koefisien2
                          end 
                        end as koefisien2,
                      ifnull(rs2.uraian_satuan,"N/A") as satuan2,
                      a.koefisien3,
                      ifnull(rs3.uraian_satuan,"N/A") as satuan3,
                      e.jml_rupiah,case a.jenis_biaya when 1 then "Fix" else "Variable" end as jenis_biaya
                      FROM trx_asb_komponen_rinci  as a                    
                      INNER JOIN trx_asb_komponen as b ON a.id_komponen_asb=b.id_komponen_asb
                      LEFT OUTER JOIN ref_satuan as rs1 ON a.id_satuan1=rs1.id_satuan
                      LEFT OUTER JOIN ref_satuan as rs2 ON a.id_satuan2=rs2.id_satuan
                      LEFT OUTER JOIN ref_satuan as rs3 ON a.id_satuan3=rs3.id_satuan
                      LEFT OUTER JOIN ref_satuan as rs4 ON a.sat_derivatif1=rs4.id_satuan
                      LEFT OUTER JOIN ref_satuan as rs5 ON a.sat_derivatif2=rs5.id_satuan
                      LEFT OUTER JOIN ref_ssh_tarif as c ON a.id_tarif_ssh=c.id_tarif_ssh
                      INNER JOIN trx_asb_aktivitas as d ON b.id_aktivitas_asb=d.id_aktivitas_asb
                      INNER JOIN ref_ssh_perkada_tarif as e ON a.id_tarif_ssh=e.id_tarif_ssh
                      INNER JOIN ref_ssh_perkada_zona as f ON e.id_zona_perkada=f.id_zona_perkada
                      INNER JOIN ref_ssh_perkada as g ON f.id_perkada=g.id_perkada
                      WHERE g.flag=1 AND g.tahun_berlaku='.Session::get('tahun').'        
                      AND b.id_komponen_asb='.$row3->id_komponen_asb.'
                      AND COALESCE(a.ket_group,"-") ="'.$row3->ket_group.'"
                      AND b.id_aktivitas_asb='.$akt.'
                      AND f.id_zona='.$zona);

				foreach($ASBKomponenRinci as $row4) {
          $ASBValiditas = DB::SELECT('SELECT a.jenis_biaya, a.hub_driver, a.id_satuan1, a.id_satuan2, c.id_satuan_1, c.id_satuan_2, c.sat_derivatif_1, 
                              c.sat_derivatif_2, a.koefisien1, a.koefisien2, a.koefisien3,
                              CASE a.jenis_biaya
                              WHEN 2 THEN 
                                CASE a.hub_driver
                                  WHEN 1 THEN 
                                        CASE a.id_satuan1
                                          WHEN c.id_satuan_1 THEN 1
                                          ELSE 0 END
                                  WHEN 2 THEN
                                        CASE a.id_satuan1
                                        WHEN c.id_satuan_2 THEN 1
                                          ELSE 0 END
                                  WHEN 3 THEN
                                        CASE a.id_satuan1
                                        WHEN c.id_satuan_1 THEN 
                                            CASE a.id_satuan2
                                            WHEN c.id_satuan_2 THEN 1
                                              ELSE 0 END 
                                        ELSE 0 END
                                  WHEN 4 THEN
                                        CASE a.id_satuan1
                                        WHEN c.sat_derivatif_1 THEN 1
                                          ELSE 0 END
                                  WHEN 5 THEN
                                        CASE a.id_satuan1
                                        WHEN c.sat_derivatif_2 THEN 1
                                          ELSE 0 END
                                  WHEN 6 THEN
                                        CASE a.id_satuan1
                                        WHEN c.sat_derivatif_1 THEN 
                                            CASE a.id_satuan2
                                            WHEN c.sat_derivatif_2 THEN 1
                                              ELSE 0 END 
                                        ELSE 0 END
                                  WHEN 7 THEN
                                        CASE a.id_satuan1
                                        WHEN c.sat_derivatif_1 THEN 
                                            CASE a.id_satuan2
                                            WHEN c.id_satuan_2 THEN 1
                                              ELSE 0 END 
                                        ELSE 0 END
                                  WHEN 8 THEN
                                        CASE a.id_satuan1
                                        WHEN c.sat_derivatif_2 THEN 
                                            CASE a.id_satuan2
                                            WHEN c.id_satuan_1 THEN 1
                                              ELSE 0 END 
                                        ELSE 0 END
                                  ELSE
                                  0 END 
                              ELSE 1 END AS cek_1, 
                              CASE WHEN (a.koefisien1 > 0) THEN 
                                  CASE WHEN (a.koefisien2 > 0) THEN
                                      CASE WHEN (a.koefisien3 > 0) THEN 1       
                                      ELSE 0 END    
                                    ELSE 0 END
                                ELSE 0 END AS cek_2
                              FROM trx_asb_komponen_rinci a
                              INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
                              INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
                              WHERE a.id_komponen_asb_rinci='.$row4->id_komponen_asb_rinci);

          foreach($ASBValiditas as $row99) {
          if ($row99->cek_1 == 0 ||  $row99->cek_2 == 0) {
            PDF::SetTextColor(204,0,0);
          }  
          }        
					PDF::MultiCell($w2[0], 10, '', 0, 'L', 0, 0);
					PDF::MultiCell($w2[1], 10, '', 0, 'L', 0, 0);
					PDF::MultiCell($w2[2], 10, $row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya, 0, 'L', 0, 0);
					PDF::MultiCell($w2[3], 10, number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1, 0, 'C', 0, 0);
					PDF::MultiCell($w2[4], 10, number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2, 0, 'C', 0, 0);
					PDF::MultiCell($w2[5], 10, number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3, 0, 'C', 0, 0);
					PDF::MultiCell($w2[6], 10, number_format($row4->jml_rupiah,2,',','.'), 0, 'C', 0, 0);
					PDF::MultiCell($w2[7], 10, '=', 0, 'C', 0, 0);
					PDF::MultiCell($w2[8], 10, number_format($row4->koefisien1*$row4->koefisien2*$row4->koefisien3*$row4->jml_rupiah,2,',','.'), 0, 'R', 0, 0);
          PDF::SetTextColor(0,0,0);
					PDF::Ln();
					$countrow++;
					if($countrow>=$totalrow)
					{
						PDF::AddPage('P','A4');
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
  		PDF::AddPage('P','A4');
  		$countrow=0;
  		$countrow++;
  	}
  	$ASBKomponenRinci1 = DB::SELECT('SELECT CAST(sum(IFNULL(a.koefisien1,1)*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.jml_rupiah) as Decimal(15,3)) as koef_fix
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif g ON a.id_tarif_ssh = g.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona h ON g.id_zona_perkada = h.id_zona_perkada
        INNER JOIN ref_ssh_perkada i ON h.id_perkada = i.id_perkada
        WHERE i.flag = 1 AND i.tahun_berlaku='.Session::get('tahun').'  AND h.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya = 1 GROUP BY h.id_zona, c.id_aktivitas_asb, a.jenis_biaya');
      
    $ASBKomponenRinci2 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.jml_rupiah) as Decimal(15,3)) as koef_v1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif g ON a.id_tarif_ssh = g.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona h ON g.id_zona_perkada = h.id_zona_perkada
        INNER JOIN ref_ssh_perkada i ON h.id_perkada = i.id_perkada
        WHERE i.flag = 1 AND i.tahun_berlaku='.Session::get('tahun').'  AND h.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 1
        GROUP BY h.id_zona, c.id_aktivitas_asb, a.jenis_biaya');
      
      $ASBKomponenRinci3 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.jml_rupiah) as Decimal(15,3)) as koef_v2
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif g ON a.id_tarif_ssh = g.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona h ON g.id_zona_perkada = h.id_zona_perkada
        INNER JOIN ref_ssh_perkada i ON h.id_perkada = i.id_perkada
        WHERE i.flag = 1 AND i.tahun_berlaku='.Session::get('tahun').'  AND h.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 2
        GROUP BY h.id_zona, c.id_aktivitas_asb, a.jenis_biaya');
      
      $ASBKomponenRinci4 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien3,1)*g.jml_rupiah) as Decimal(15,3)) as koef_v1v2
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif g ON a.id_tarif_ssh = g.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona h ON g.id_zona_perkada = h.id_zona_perkada
        INNER JOIN ref_ssh_perkada i ON h.id_perkada = i.id_perkada
        WHERE i.flag = 1 AND i.tahun_berlaku='.Session::get('tahun').'  AND h.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 3
        GROUP BY h.id_zona, c.id_aktivitas_asb, a.jenis_biaya');
      
      $ASBKomponenRinci5 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.jml_rupiah) as Decimal(15,3)) as koef_d1, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif g ON a.id_tarif_ssh = g.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona h ON g.id_zona_perkada = h.id_zona_perkada
        INNER JOIN ref_ssh_perkada i ON h.id_perkada = i.id_perkada
        WHERE i.flag = 1 AND i.tahun_berlaku='.Session::get('tahun').'  AND h.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 4
        GROUP BY h.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');
      
      $ASBKomponenRinci6 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*g.jml_rupiah) as Decimal(15,3)) as koef_d2, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif g ON a.id_tarif_ssh = g.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona h ON g.id_zona_perkada = h.id_zona_perkada
        INNER JOIN ref_ssh_perkada i ON h.id_perkada = i.id_perkada
        WHERE i.flag = 1 AND i.tahun_berlaku='.Session::get('tahun').'  AND h.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 5
        GROUP BY h.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');

      $ASBKomponenRinci7 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien3,1)*g.jml_rupiah) as Decimal(15,3)) as koef_d1d2, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif g ON a.id_tarif_ssh = g.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona h ON g.id_zona_perkada = h.id_zona_perkada
        INNER JOIN ref_ssh_perkada i ON h.id_perkada = i.id_perkada
        WHERE i.flag = 1 AND i.tahun_berlaku='.Session::get('tahun').'  AND h.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 6
        GROUP BY h.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');

      $ASBKomponenRinci8 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien3,1)*g.jml_rupiah) as Decimal(15,3)) as koef_v1d2, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif g ON a.id_tarif_ssh = g.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona h ON g.id_zona_perkada = h.id_zona_perkada
        INNER JOIN ref_ssh_perkada i ON h.id_perkada = i.id_perkada
        WHERE i.flag = 1 AND i.tahun_berlaku='.Session::get('tahun').'  AND h.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 7
        GROUP BY h.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');

      $ASBKomponenRinci9 = DB::SELECT('SELECT CAST(sum(1*IFNULL(a.koefisien3,1)*g.jml_rupiah) as Decimal(15,3)) as koef_d1v2, c.range_max, c.range_max1
        FROM trx_asb_komponen_rinci a
        INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif g ON a.id_tarif_ssh = g.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona h ON g.id_zona_perkada = h.id_zona_perkada
        INNER JOIN ref_ssh_perkada i ON h.id_perkada = i.id_perkada
        WHERE i.flag = 1 AND i.tahun_berlaku='.Session::get('tahun').'  AND h.id_zona = '.$zona.' and c.id_aktivitas_asb= '.$akt.' AND a.jenis_biaya <> 1 AND a.hub_driver = 8
        GROUP BY h.id_zona, c.id_aktivitas_asb, a.jenis_biaya, c.range_max, c.range_max1');

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
      //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
      // PDF::Cell('15', '3', '', 0, 0, 'L', 0);

      $countrow++;
      if($countrow>=$totalrow)
      {
        PDF::AddPage('P','A4');
        $countrow=0;
        $countrow++;
      }
      // PDF::SetY(-60);
      PDF::Cell(array_sum($w), 0, '', 'T');
      PDF::Ln();
      PDF::SetTextColor(0,0,0);
      PDF::Cell(0, 5, 'Rumus Perhitungan ASB :', 0, 0, 'L', 0, '',1);
      PDF::Ln();
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
      PDF::Cell(array_sum($w), 0, '', 'T');
      PDF::Ln();

      $countrow++;
      if($countrow>=$totalrow)
      {
        PDF::AddPage('P','A4');
        $countrow=0;
        $countrow++;
      }      

    Template::footerPotrait();

    PDF::Output('ASBAktivitasKomponenRinciHitung.pdf', 'I');
  }  
  
}
