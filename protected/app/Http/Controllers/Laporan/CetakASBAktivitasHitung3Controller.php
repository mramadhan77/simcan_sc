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


class CetakASBAktivitasHitungController extends Controller
{

    public function printASBAktivitas($id_perhitungan,$id_aktivitas,$v1,$v2)
  {
  	
  	Template::settingPagePotrait();
    Template::headerPotrait();

  	PDF::SetFont('helvetica', '', 6); 
  	$countrow=0;
  	$totalrow=30;
  	
  	// column titles
  	$header = array('Komponen Belanja','koef. 1','koef. 2','koef. 3','Harga (Rp.)','','Total (Rp.)');
  	
  	// Colors, line width and bold font
  	PDF::SetFillColor(200, 200, 200);
  	PDF::SetTextColor(0);
  	PDF::SetDrawColor(255, 255, 255);
  	PDF::SetLineWidth(0.3);

  	$pemda=Session::get('xPemda');
  	$akt=$id_aktivitas;
  	$sum=0;

    $ASBAktivitas = DB::select('select distinct trx_asb_aktivitas.id_satuan_2,trx_asb_aktivitas.id_satuan_1,
        trx_asb_perkada.nomor_perkada,trx_asb_perkada.tahun_berlaku,trx_asb_kelompok.uraian_kelompok_asb,
        trx_asb_sub_kelompok.uraian_sub_kelompok_asb,trx_asb_sub_sub_kelompok.uraian_sub_sub_kelompok_asb,
        trx_asb_aktivitas.nm_aktivitas_asb,trx_asb_aktivitas.volume_1,rs1.uraian_satuan as satuan1,
        trx_asb_aktivitas.volume_2,rs2.uraian_satuan as satuan2,trx_asb_perkada.tanggal_perkada from trx_asb_aktivitas
        left join ref_satuan as rs1 ON trx_asb_aktivitas.id_satuan_1 = rs1.id_satuan
        left join ref_satuan as rs2 ON trx_asb_aktivitas.id_satuan_2 = rs2.id_satuan
        join trx_asb_sub_sub_kelompok ON trx_asb_aktivitas.id_asb_sub_sub_kelompok = trx_asb_sub_sub_kelompok.id_asb_sub_sub_kelompok
        join trx_asb_sub_kelompok ON trx_asb_sub_sub_kelompok.id_asb_sub_kelompok = trx_asb_sub_kelompok.id_asb_sub_kelompok
        join trx_asb_kelompok ON trx_asb_sub_kelompok.id_asb_kelompok = trx_asb_kelompok.id_asb_kelompok
        join trx_asb_perkada ON trx_asb_kelompok.id_asb_perkada = trx_asb_perkada.id_asb_perkada
        join trx_asb_perhitungan_rinci ON trx_asb_aktivitas.id_aktivitas_asb = trx_asb_perhitungan_rinci.id_aktivitas_asb
        where trx_asb_aktivitas.id_aktivitas_asb ='.$akt.' and trx_asb_perhitungan_rinci.id_perhitungan ='.$id_perhitungan);


      $ASBZona = DB::SELECT('SELECT b.tahun_perhitungan, a.id_zona FROM trx_asb_perhitungan_rinci as a  
            INNER JOIN trx_asb_perhitungan as b ON a.id_perhitungan = b.id_perhitungan
            where a.id_perhitungan='.$id_perhitungan.'
            GROUP BY b.tahun_perhitungan, a.id_zona, a.id_perhitungan ORDER BY a.id_zona LIMIT 1');
        foreach($ASBZona as $rowZona) {
            $zona=$rowZona->id_zona; 
            $tahunhitung = $rowZona->tahun_perhitungan;          
      }

  	PDF::SetFont('helvetica', 'B', 5);
  	foreach($ASBAktivitas as $row) {
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
      PDF::Cell('20', '3', 'Tahun Perhitungan', 0, 0, 'L', 0);
      PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
      PDF::Cell('20', '3', $tahunhitung, 0, 0, 'L', 0);
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
  		PDF::Cell('40', '8', 'Indikator Output', 0, 0, 'L', 0);
  		PDF::Cell('5', '8', ':', 0, 0, 'L', 0);
  		PDF::Cell('100', '8', $v1 .' '.$row->satuan1 .' x '.$v2 .' '.$row->satuan2, 0, 0, 'L', 0);
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
  		// $ASBKomponen = TrxAsbKomponen::select('nm_komponen_asb','id_komponen_asb')
  		// ->where('trx_asb_komponen.id_aktivitas_asb','=',$akt)
  		// ->get();

      $ASBKomponen = DB::select('select nm_komponen_asb,id_komponen_asb from trx_asb_komponen
                    where trx_asb_komponen.id_aktivitas_asb ='.$akt);

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
  			// $ASBKomponenRinci = TrxAsbKomponenRinci::select ('trx_asb_komponen_rinci.id_komponen_asb','trx_asb_komponen_rinci.ket_group','trx_asb_perhitungan_rinci.id_zona')
  			// ->join('trx_asb_perhitungan_rinci','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','trx_asb_perhitungan_rinci.id_komponen_asb_rinci')
  			// ->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
  			// ->where('trx_asb_komponen_rinci.id_komponen_asb','=',$row2->id_komponen_asb)
  			// ->distinct()
  			// ->get();

        $ASBKomponenRinci = DB:: select('SELECT distinct trx_asb_komponen_rinci.id_komponen_asb, trx_asb_komponen_rinci.ket_group, 
                      trx_asb_perhitungan_rinci.id_zona FROM trx_asb_komponen_rinci
                      inner join trx_asb_perhitungan_rinci ON trx_asb_komponen_rinci.id_komponen_asb_rinci=trx_asb_perhitungan_rinci.id_komponen_asb_rinci
                      where trx_asb_perhitungan_rinci.id_perhitungan='.$id_perhitungan.' and trx_asb_komponen_rinci.id_komponen_asb='.$row2->id_komponen_asb.' and trx_asb_perhitungan_rinci.id_zona='.$zona.' and trx_asb_perhitungan_rinci.id_aktivitas_asb='.$akt);

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
  					PDF::AddPage('P');
  					$countrow=0;
  					for($i = 0; $i < $num_headers; ++$i) {
  						PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
  					}
  					PDF::Ln();
  					$countrow++;
  				}
  				
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
                           when 3 then trx_asb_komponen_rinci.koefisien1
                           when 6 then CEIL('.$v2.'/trx_asb_aktivitas.range_max1)
                           when 7 then '.$v2.'
                           when 8 then '.$v1.'
                          else  trx_asb_komponen_rinci.koefisien2
                          end 
                        end as koefisien2,
                      ifnull(rs2.uraian_satuan,"N/A") as satuan2,
                      trx_asb_komponen_rinci.koefisien3,
                      ifnull(rs3.uraian_satuan,"N/A") as satuan3,
                      trx_asb_perhitungan_rinci.harga_satuan,case trx_asb_komponen_rinci.jenis_biaya when 1 then "Fix" else "Variable" end as jenis_biaya'))
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
					PDF::MultiCell($w2[0], 10, '', 0, 'L', 0, 0);
					PDF::MultiCell($w2[1], 10, '', 0, 'L', 0, 0);
					PDF::MultiCell($w2[2], 10, $row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya, 0, 'L', 0, 0);
					PDF::MultiCell($w2[3], 10, number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1, 0, 'C', 0, 0);
					PDF::MultiCell($w2[4], 10, number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2, 0, 'C', 0, 0);
					PDF::MultiCell($w2[5], 10, number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3, 0, 'C', 0, 0);
					PDF::MultiCell($w2[6], 10, number_format($row4->harga_satuan,2,',','.'), 0, 'C', 0, 0);
					PDF::MultiCell($w2[7], 10, '=', 0, 'C', 0, 0);
					PDF::MultiCell($w2[8], 10, number_format($row4->koefisien1*$row4->koefisien2*$row4->koefisien3*$row4->harga_satuan,2,',','.'), 0, 'R', 0, 0);
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
					$sum=$sum+$row4->koefisien1*$row4->koefisien2*$row4->koefisien3*$row4->harga_satuan;
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
  	$ASBKomponenRinci1 = DB::table('trx_asb_komponen_rinci')
  	->select(DB::raw('CAST(sum(IFNULL(trx_asb_komponen_rinci.koefisien1,1)*IFNULL(trx_asb_komponen_rinci.koefisien2,1)*IFNULL(trx_asb_komponen_rinci.koefisien3,1)*ref_ssh_perkada_tarif.jml_rupiah) as Decimal(15,3)) as koef,"V1" as var'))
  	->join('trx_asb_komponen','trx_asb_komponen_rinci.id_komponen_asb','=','trx_asb_komponen.id_komponen_asb')
  	->join('trx_asb_aktivitas','trx_asb_komponen.id_aktivitas_asb','=','trx_asb_aktivitas.id_aktivitas_asb')
  	->join('ref_ssh_perkada_tarif','trx_asb_komponen_rinci.id_tarif_ssh','=','ref_ssh_perkada_tarif.id_tarif_ssh')
  	->join('trx_asb_perhitungan_rinci','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','trx_asb_perhitungan_rinci.id_komponen_asb_rinci')
  	->join('ref_ssh_perkada_zona','ref_ssh_perkada_tarif.id_zona_perkada','=','ref_ssh_perkada_zona.id_zona_perkada')
  	->join('ref_ssh_perkada','ref_ssh_perkada_zona.id_perkada','=','ref_ssh_perkada.id_perkada')
  	->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
  	->where('trx_asb_komponen_rinci.jenis_biaya','=',3)
  	//	->where('ref_ssh_perkada_tarif.id_zona_perkada','=',$zona)
  	->where('trx_asb_aktivitas.id_aktivitas_asb','=',$akt)
  	->whereRaw('((`trx_asb_komponen_rinci`.`id_satuan1` = trx_asb_aktivitas.id_satuan_1 and ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) <> trx_asb_aktivitas.id_satuan_2 and ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0) <> trx_asb_aktivitas.id_satuan_2)
				or (ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) = trx_asb_aktivitas.id_satuan_1 and `trx_asb_komponen_rinci`.`id_satuan1` <> trx_asb_aktivitas.id_satuan_2 and ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0) <> trx_asb_aktivitas.id_satuan_2)
				or (ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0) = trx_asb_aktivitas.id_satuan_1 and ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) <> trx_asb_aktivitas.id_satuan_2 and `trx_asb_komponen_rinci`.`id_satuan1` <> trx_asb_aktivitas.id_satuan_2))
      ')
      ->groupBy('var')
      ->get();
      
      $ASBKomponenRinci2 = DB::table('trx_asb_komponen_rinci')
      ->select(DB::raw('CAST(IFNULL(sum(IFNULL(trx_asb_komponen_rinci.koefisien1,1)*IFNULL(trx_asb_komponen_rinci.koefisien2,1)*IFNULL(trx_asb_komponen_rinci.koefisien3,1)*ref_ssh_perkada_tarif.jml_rupiah),0) as Decimal(15,3)) as koef,"V1" as var'))
      ->join('trx_asb_komponen','trx_asb_komponen_rinci.id_komponen_asb','=','trx_asb_komponen.id_komponen_asb')
      ->join('trx_asb_aktivitas','trx_asb_komponen.id_aktivitas_asb','=','trx_asb_aktivitas.id_aktivitas_asb')
      ->join('ref_ssh_perkada_tarif','trx_asb_komponen_rinci.id_tarif_ssh','=','ref_ssh_perkada_tarif.id_tarif_ssh')
      ->join('trx_asb_perhitungan_rinci','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','trx_asb_perhitungan_rinci.id_komponen_asb_rinci')
      ->join('ref_ssh_perkada_zona','ref_ssh_perkada_tarif.id_zona_perkada','=','ref_ssh_perkada_zona.id_zona_perkada')
      ->join('ref_ssh_perkada','ref_ssh_perkada_zona.id_perkada','=','ref_ssh_perkada.id_perkada')
      ->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
      ->where('trx_asb_komponen_rinci.jenis_biaya','=',3)
      // ->where('ref_ssh_perkada_tarif.id_zona_perkada','=',$zona)
      ->where('trx_asb_aktivitas.id_aktivitas_asb','=',$akt)
      ->whereRaw('((`trx_asb_komponen_rinci`.`id_satuan1` = trx_asb_aktivitas.id_satuan_2 and ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) <> trx_asb_aktivitas.id_satuan_1 and ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0) <> trx_asb_aktivitas.id_satuan_1)
				or (ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) = trx_asb_aktivitas.id_satuan_2 and `trx_asb_komponen_rinci`.`id_satuan1` <> trx_asb_aktivitas.id_satuan_1 and ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0) <> trx_asb_aktivitas.id_satuan_1)
				or (ifnull(ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0),0) = trx_asb_aktivitas.id_satuan_2 and ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) <> trx_asb_aktivitas.id_satuan_1 and `trx_asb_komponen_rinci`.`id_satuan1` <> trx_asb_aktivitas.id_satuan_1))
			')
			->groupBy('var')
			->get();
			
			$ASBKomponenRinci4 = DB::table('trx_asb_komponen_rinci')
			->select(DB::raw('CAST(IFNULL(sum(IFNULL(trx_asb_komponen_rinci.koefisien1,1)*IFNULL(trx_asb_komponen_rinci.koefisien2,1)*IFNULL(trx_asb_komponen_rinci.koefisien3,1)*ref_ssh_perkada_tarif.jml_rupiah),0) as Decimal(15,3)) as koef,"V1" as var'))
			->join('trx_asb_komponen','trx_asb_komponen_rinci.id_komponen_asb','=','trx_asb_komponen.id_komponen_asb')
			->join('trx_asb_aktivitas','trx_asb_komponen.id_aktivitas_asb','=','trx_asb_aktivitas.id_aktivitas_asb')
			->join('ref_ssh_perkada_tarif','trx_asb_komponen_rinci.id_tarif_ssh','=','ref_ssh_perkada_tarif.id_tarif_ssh')
			->join('trx_asb_perhitungan_rinci','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','trx_asb_perhitungan_rinci.id_komponen_asb_rinci')
			->join('ref_ssh_perkada_zona','ref_ssh_perkada_tarif.id_zona_perkada','=','ref_ssh_perkada_zona.id_zona_perkada')
			->join('ref_ssh_perkada','ref_ssh_perkada_zona.id_perkada','=','ref_ssh_perkada.id_perkada')
			->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
			->where('trx_asb_komponen_rinci.jenis_biaya','=',3)
			->where('trx_asb_aktivitas.id_aktivitas_asb','=',$akt)
			->whereRaw('((`trx_asb_komponen_rinci`.`id_satuan1` = trx_asb_aktivitas.id_satuan_1 and ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) = trx_asb_aktivitas.id_satuan_2)
				or (`trx_asb_komponen_rinci`.`id_satuan1` = trx_asb_aktivitas.id_satuan_1 and ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0) = trx_asb_aktivitas.id_satuan_2)
				or (ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) = trx_asb_aktivitas.id_satuan_1 and `trx_asb_komponen_rinci`.`id_satuan1` = trx_asb_aktivitas.id_satuan_2)
				or(ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) = trx_asb_aktivitas.id_satuan_1 and ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0) = trx_asb_aktivitas.id_satuan_2)
				or (ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0) = trx_asb_aktivitas.id_satuan_1 and `trx_asb_komponen_rinci`.`id_satuan1` = trx_asb_aktivitas.id_satuan_2)
				or (ifnull(`trx_asb_komponen_rinci`.`id_satuan3`,0) = trx_asb_aktivitas.id_satuan_1 and ifnull(`trx_asb_komponen_rinci`.`id_satuan2`,0) = trx_asb_aktivitas.id_satuan_2))
			')
			->groupBy('var')
			->get();
			
			$ASBKomponenRinci3 = DB::table('trx_asb_komponen_rinci')
			->select(DB::raw('CAST(IFNULL(sum(IFNULL(trx_asb_komponen_rinci.koefisien1,1)*IFNULL(trx_asb_komponen_rinci.koefisien2,1)*IFNULL(trx_asb_komponen_rinci.koefisien3,1)*ref_ssh_perkada_tarif.jml_rupiah),0) as Decimal(15,3)) as koef,"V1" as var'))
			->join('trx_asb_komponen','trx_asb_komponen_rinci.id_komponen_asb','=','trx_asb_komponen.id_komponen_asb')
			->join('trx_asb_aktivitas','trx_asb_komponen.id_aktivitas_asb','=','trx_asb_aktivitas.id_aktivitas_asb')
			->join('ref_ssh_perkada_tarif','trx_asb_komponen_rinci.id_tarif_ssh','=','ref_ssh_perkada_tarif.id_tarif_ssh')
			->join('trx_asb_perhitungan_rinci','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','trx_asb_perhitungan_rinci.id_komponen_asb_rinci')
			->join('ref_ssh_perkada_zona','ref_ssh_perkada_tarif.id_zona_perkada','=','ref_ssh_perkada_zona.id_zona_perkada')
			->join('ref_ssh_perkada','ref_ssh_perkada_zona.id_perkada','=','ref_ssh_perkada.id_perkada')
			->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
			->where('trx_asb_komponen_rinci.jenis_biaya','=',1)
			//->where('ref_ssh_perkada_tarif.id_zona_perkada','=',$zona)
			->where('trx_asb_aktivitas.id_aktivitas_asb','=',$akt)
			->groupBy('var')
			->get();
			
			$ASBKomponenRinci5 = DB::table('trx_asb_komponen_rinci')
			->select(DB::raw('CAST(IFNULL(sum(IFNULL(trx_asb_komponen_rinci.koefisien1,1)*IFNULL(trx_asb_komponen_rinci.koefisien2,1)*IFNULL(trx_asb_komponen_rinci.koefisien3,1)*ref_ssh_perkada_tarif.jml_rupiah),0) as Decimal(15,3)) as koef,"DV1" as var, ifnull(trx_asb_aktivitas.range_max,1) as range_max'))
			->join('trx_asb_komponen','trx_asb_komponen_rinci.id_komponen_asb','=','trx_asb_komponen.id_komponen_asb')
			->join('trx_asb_aktivitas','trx_asb_komponen.id_aktivitas_asb','=','trx_asb_aktivitas.id_aktivitas_asb')
			->join('ref_ssh_perkada_tarif','trx_asb_komponen_rinci.id_tarif_ssh','=','ref_ssh_perkada_tarif.id_tarif_ssh')
			->join('trx_asb_perhitungan_rinci','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','trx_asb_perhitungan_rinci.id_komponen_asb_rinci')
			->join('ref_ssh_perkada_zona','ref_ssh_perkada_tarif.id_zona_perkada','=','ref_ssh_perkada_zona.id_zona_perkada')
			->join('ref_ssh_perkada','ref_ssh_perkada_zona.id_perkada','=','ref_ssh_perkada.id_perkada')
			->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
			->where('trx_asb_komponen_rinci.jenis_biaya','=',2)
			->where('trx_asb_komponen_rinci.hub_driver','=',1)
			//->where('ref_ssh_perkada_tarif.id_zona_perkada','=',$zona)
			->where('trx_asb_aktivitas.id_aktivitas_asb','=',$akt)
			->groupBy('var')
			->groupBy('trx_asb_aktivitas.range_max')
			->get();
			
			$ASBKomponenRinci6 = DB::table('trx_asb_komponen_rinci')
			->select(DB::raw('CAST(IFNULL(sum(IFNULL(trx_asb_komponen_rinci.koefisien1,1)*IFNULL(trx_asb_komponen_rinci.koefisien2,1)*IFNULL(trx_asb_komponen_rinci.koefisien3,1)*ref_ssh_perkada_tarif.jml_rupiah),0) as Decimal(15,3)) as koef,"DV2" as var,ifnull(trx_asb_aktivitas.range_max,1) as range_max'))
			->join('trx_asb_komponen','trx_asb_komponen_rinci.id_komponen_asb','=','trx_asb_komponen.id_komponen_asb')
			->join('trx_asb_aktivitas','trx_asb_komponen.id_aktivitas_asb','=','trx_asb_aktivitas.id_aktivitas_asb')
			->join('ref_ssh_perkada_tarif','trx_asb_komponen_rinci.id_tarif_ssh','=','ref_ssh_perkada_tarif.id_tarif_ssh')
			->join('trx_asb_perhitungan_rinci','trx_asb_komponen_rinci.id_komponen_asb_rinci','=','trx_asb_perhitungan_rinci.id_komponen_asb_rinci')
			->join('ref_ssh_perkada_zona','ref_ssh_perkada_tarif.id_zona_perkada','=','ref_ssh_perkada_zona.id_zona_perkada')
			->join('ref_ssh_perkada','ref_ssh_perkada_zona.id_perkada','=','ref_ssh_perkada.id_perkada')->where('trx_asb_perhitungan_rinci.id_perhitungan','=',$id_perhitungan)
			->where('trx_asb_komponen_rinci.jenis_biaya','=',2)
			->where('trx_asb_komponen_rinci.hub_driver','=',2)
			//->where('ref_ssh_perkada_tarif.id_zona_perkada','=',$zona)
			->where('trx_asb_aktivitas.id_aktivitas_asb','=',$akt)
			->groupBy('var')
			->groupBy('trx_asb_aktivitas.range_max')
			->get();
			
			//return count($ASBKomponenRinci2);
			PDF::SetFont('helvetica', 'B', 8);
			
			PDF::Cell(array_sum($w), 0, '', 'T');
			PDF::Ln();
			
			
			if(count($ASBKomponenRinci1)==0)
			{
				$rincikoef1=0;
				$rincivar1=".V1";
				
			}
			else
			{
				$rincikoef1=$ASBKomponenRinci1[0]->koef;
				$rincivar1=".V1";
			}
			if(count($ASBKomponenRinci2)==0)
			{
				$rincikoef2=0;
				$rincivar2=".V2";
				
			}
			else
			{
				$rincikoef2=$ASBKomponenRinci2[0]->koef;
				$rincivar2=".V2";
			}
			if(count($ASBKomponenRinci3)==0)
			{
				$rincikoef3=0;
				
				
			}
			else
			{
				$rincikoef3=$ASBKomponenRinci3[0]->koef;
				
			}
			
			if(count($ASBKomponenRinci4)==0)
			{
				$rincikoef4=0;
				$rincivar4=".V1V2";
			}
			else
			{
				$rincikoef4=$ASBKomponenRinci4[0]->koef;
				$rincivar4=".V1V2";
			}
			
			if(count($ASBKomponenRinci5)==0)
			{
				$rincikoef5=0;
				$rincivar5=".DV1";
				$range5=1;
			}
			else
			{
				$rincikoef5=$ASBKomponenRinci5[0]->koef;
				$rincivar5=".DV1";
				$range5=$ASBKomponenRinci5[0]->range_max;
			}
			
			if(count($ASBKomponenRinci6)==0)
			{
				$rincikoef6=0;
				$rincivar6=".DV2";
				$range6=1;
				
			}
			else
			{
				$rincikoef6=$ASBKomponenRinci6[0]->koef;
				$rincivar6=".DV2";
				$range6=$ASBKomponenRinci6[0]->range_max;
			}
			
			$nilaiV1=$rincikoef1*$v1;
			$nilaiV2=$rincikoef2*$v2;
			$nilaiV1V2=$rincikoef4*$v1*$v2;
			$nilaiDV1=$rincikoef5*(ceil($v1/$range5));
			$nilaiDV2=$rincikoef6*(ceil($v2/$range6));
			$nilaiTotal=$nilaiV1+$nilaiV2+$nilaiV1V2+$rincikoef3+$nilaiDV1+$nilaiDV2;
			PDF::Cell('158', '3', '', 0, 0, 'L', 0);
			PDF::Cell(25, 5, 'Nilai ASB (Rp.) :', 0, 'R', 0, 0);
			PDF::Ln();
			$countrow++;
			if($countrow>=$totalrow)
			{
				PDF::AddPage('P');
				$countrow=0;
				$countrow++;
			}
			PDF::Cell('53', '3', '', 0, 0, 'L', 0);
			PDF::Cell(130, 5, 'Y = '. number_format($nilaiV1, 2, ',', '.').' + '.number_format($nilaiV2, 2, ',','.').' + ' .number_format($nilaiV1V2, 2, ',', '.').' + ' .number_format($nilaiDV1, 2, ',', '.').' + ' .number_format($nilaiDV2, 2, ',', '.').' + '.number_format($rincikoef3, 2, ',','.').' = '.number_format($nilaiTotal, 2, ',','.'), 0, 'R', 0, 0);
			PDF::Ln();
			

    Template::footerPotrait();
			// ---------------------------------------------------------
			
			// close and output PDF document
			PDF::Output('ASBAktivitasKomponenRinciHitung.pdf', 'I');
  }  
  
}
