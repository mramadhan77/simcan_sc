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


class CetakASBAktivitasRumusController extends Controller
{

	public function printASBAktivitas($id_aktivitas)
  {
  	
    Template::settingPagePotrait();
    Template::headerPotrait();

    PDF::SetFont('helvetica', '', 6);
    $countrow=0;
    $totalrow=30;

    $header = array('Komponen Belanja','koef. 1','koef. 2','koef. 3','Harga (Rp.)','','Total (Rp.)');

    PDF::SetFillColor(200, 200, 200);
    PDF::SetTextColor(0);
    PDF::SetDrawColor(255, 255, 255);
    PDF::SetLineWidth(0.3);

    $pemda=Session::get('xPemda');
    $akt=$id_aktivitas;   
    $sum=0;

    $ASBAktivitas = DB::SELECT('SELECT a.id_satuan_2,a.id_satuan_1, e.nomor_perkada,
            f.tahun_perhitungan AS tahun_berlaku,d.uraian_kelompok_asb,c.uraian_sub_kelompok_asb,b.uraian_sub_sub_kelompok_asb,a.nm_aktivitas_asb,a.volume_1,
            rs1.uraian_satuan as satuan1,a.volume_2,rs2.uraian_satuan as satuan2,e.tanggal_perkada,f.id_perhitungan
            FROM trx_asb_aktivitas as a
            LEFT JOIN ref_satuan as rs1 on a.id_satuan_1 = rs1.id_satuan
            LEFT JOIN ref_satuan as rs2 on a.id_satuan_2 = rs2.id_satuan
            INNER JOIN trx_asb_sub_sub_kelompok as b on a.id_asb_sub_sub_kelompok = b.id_asb_sub_sub_kelompok
            INNER JOIN trx_asb_sub_kelompok as c on b.id_asb_sub_kelompok = c.id_asb_sub_kelompok
            INNER JOIN trx_asb_kelompok as d on c.id_asb_kelompok = d.id_asb_kelompok
            INNER JOIN trx_asb_perkada as e on d.id_asb_perkada = e.id_asb_perkada
            INNER JOIN trx_asb_perhitungan as f on e.id_asb_perkada = f.id_perkada
            WHERE f.tahun_perhitungan = '.Session::get('tahun').' AND a.id_aktivitas_asb='.$akt);

    PDF::SetFont('helvetica', 'B', 5);

    foreach($ASBAktivitas as $row) {
        $ASBZona = DB::SELECT('SELECT a.id_zona FROM trx_asb_perhitungan_rinci as a  where a.id_perhitungan='.$row->id_perhitungan.'
            GROUP BY a.id_zona, a.id_perhitungan ORDER BY a.id_zona LIMIT 1');
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
        PDF::Cell('20', '3', 'Tahun Perhitungan', 0, 0, 'L', 0);
        PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
        PDF::Cell('20', '3', $row->tahun_berlaku, 0, 0, 'L', 0);
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
    	
        $ASBKomponen = DB::SELECT('SELECT nm_komponen_asb, id_komponen_asb FROM trx_asb_komponen WHERE id_aktivitas_asb='. $akt);

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

            $ASBKomponenRinci = DB:: select('SELECT DISTINCT a.id_komponen_asb, COALESCE(a.ket_group,"-") as ket_group, 
                      b.id_zona FROM trx_asb_komponen_rinci as a
                      INNER JOIN trx_asb_perhitungan_rinci as b ON a.id_komponen_asb_rinci=b.id_komponen_asb_rinci
                      WHERE b.id_perhitungan='.$row->id_perhitungan.' and a.id_komponen_asb='.$row2->id_komponen_asb.' and b.id_zona='.$zona.' and b.id_aktivitas_asb='.$akt);

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

            $ASBKomponenRinci = DB::SELECT('SELECT q.uraian_tarif_ssh,a.koefisien1,ifnull(case jenis_biaya when 2 then case hub_driver when 1 then rs4.uraian_satuan else rs5.uraian_satuan end  else rs1.uraian_satuan end,"N/A") as satuan1,a.koefisien2,ifnull(rs2.uraian_satuan,"N/A") as satuan2,a.koefisien3,ifnull(rs3.uraian_satuan,"N/A") as satuan3,p.jml_rupiah,case a.jenis_biaya when 1 then "Fix" when 2 then "Dependent" else "Independent" end as jenis_biaya FROM trx_asb_komponen_rinci AS a
                INNER JOIN trx_asb_komponen AS b ON a.id_komponen_asb=b.id_komponen_asb
                INNER JOIN trx_asb_aktivitas AS c ON b.id_aktivitas_asb=c.id_aktivitas_asb
                LEFT OUTER JOIN ref_satuan as rs1 ON a.id_satuan1=rs1.id_satuan
                LEFT OUTER JOIN ref_satuan as rs2 ON a.id_satuan2=rs2.id_satuan
                LEFT OUTER JOIN ref_satuan as rs3 ON a.id_satuan3=rs3.id_satuan
                LEFT OUTER JOIN ref_satuan as rs4 ON a.sat_derivatif1=rs4.id_satuan
                LEFT OUTER JOIN ref_satuan as rs5 ON a.sat_derivatif2=rs5.id_satuan
                LEFT OUTER JOIN ref_ssh_perkada_tarif as p ON a.id_tarif_ssh=p.id_tarif_ssh
                LEFT OUTER JOIN ref_ssh_tarif as q ON p.id_tarif_ssh =  q.id_tarif_ssh
                INNER JOIN ref_ssh_perkada_zona as r ON p.id_zona_perkada=r.id_zona_perkada
                INNER JOIN ref_ssh_perkada as s ON r.id_perkada = s.id_perkada
                WHERE  s.tahun_berlaku='.Session::get('tahun').' AND b.id_komponen_asb='.$row3->id_komponen_asb.' AND r.id_zona='.$zona.'
                AND COALESCE(a.ket_group,"-")="'.$row3->ket_group.'"');

    		foreach($ASBKomponenRinci as $row4) {
    			PDF::MultiCell($w2[0], 10, '', 0, 'L', 0, 0);
    			PDF::MultiCell($w2[1], 10, '', 0, 'L', 0, 0);
    			PDF::MultiCell($w2[2], 10, $row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[3], 10, number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1, 0, 'C', 0, 0);
    			PDF::MultiCell($w2[4], 10, number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2, 0, 'C', 0, 0);
    			PDF::MultiCell($w2[5], 10, number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3, 0, 'C', 0, 0);
    			PDF::MultiCell($w2[6], 10, number_format($row4->jml_rupiah,4,',','.'), 0, 'C', 0, 0);
    			PDF::MultiCell($w2[7], 10, '=', 0, 'C', 0, 0);
    			PDF::MultiCell($w2[8], 10, number_format($row4->koefisien1*$row4->koefisien2*$row4->koefisien3*$row4->jml_rupiah,4,',','.'), 0, 'C', 0, 0);
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
    		}
    	}
    	
    }
    }

    $ASBKomponenRinci1 = DB::SELECT('SELECT CAST(sum(IFNULL(a.koefisien1,1)*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*d.jml_rupiah) as Decimal(10,3)) as koef,
        "V1" as var  FROM trx_asb_komponen_rinci AS a
        INNER JOIN trx_asb_komponen AS b ON a.id_komponen_asb=b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas AS c ON b.id_aktivitas_asb=c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif AS d ON a.id_tarif_ssh=d.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona as e ON d.id_zona_perkada=e.id_zona_perkada
        INNER JOIN ref_ssh_perkada as f ON e.id_perkada = f.id_perkada
        WHERE a.jenis_biaya=3 AND f.tahun_berlaku='.Session::get('tahun').' AND e.id_zona='.$zona.' AND c.id_aktivitas_asb='.$akt.' 
        AND ((a.id_satuan1 =c.id_satuan_1 and ifnull(a.id_satuan2,0) <> c.id_satuan_2 and ifnull(a.id_satuan3,0) <> c.id_satuan_2)
                or (ifnull(a.id_satuan2,0) = c.id_satuan_1 and a.id_satuan1 <> c.id_satuan_2 and ifnull(a.id_satuan3,0) <> c.id_satuan_2)
                or (ifnull(a.id_satuan3,0) = c.id_satuan_1 and ifnull(a.id_satuan2,0) <> c.id_satuan_2 and a.id_satuan1 <> c.id_satuan_2))');

     $ASBKomponenRinci2 = DB::SELECT('SELECT CAST(sum(IFNULL(a.koefisien1,1)*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*d.jml_rupiah) as Decimal(10,3)) as koef,
        "V1" as var  FROM trx_asb_komponen_rinci AS a
        INNER JOIN trx_asb_komponen AS b ON a.id_komponen_asb=b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas AS c ON b.id_aktivitas_asb=c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif AS d ON a.id_tarif_ssh=d.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona as e ON d.id_zona_perkada=e.id_zona_perkada
        INNER JOIN ref_ssh_perkada as f ON e.id_perkada = f.id_perkada
        WHERE a.jenis_biaya=3 AND f.tahun_berlaku='.Session::get('tahun').' AND e.id_zona='.$zona.' AND c.id_aktivitas_asb='.$akt.' 
        AND ((a.id_satuan1 =c.id_satuan_2 and ifnull(a.id_satuan2,0) <> c.id_satuan_1 and ifnull(a.id_satuan3,0) <> c.id_satuan_1)
                or (ifnull(a.id_satuan2,0) = c.id_satuan_2 and a.id_satuan1 <> c.id_satuan_1 and ifnull(a.id_satuan3,0) <> c.id_satuan_1)
                or (ifnull(a.id_satuan3,0) = c.id_satuan_2 and ifnull(a.id_satuan2,0) <> c.id_satuan_1 and a.id_satuan1 <> c.id_satuan_1))');

    $ASBKomponenRinci4 = DB::SELECT('SELECT CAST(sum(IFNULL(a.koefisien1,1)*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*d.jml_rupiah) as Decimal(10,3)) as koef,
        "V1" as var  FROM trx_asb_komponen_rinci AS a
        INNER JOIN trx_asb_komponen AS b ON a.id_komponen_asb=b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas AS c ON b.id_aktivitas_asb=c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif AS d ON a.id_tarif_ssh=d.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona as e ON d.id_zona_perkada=e.id_zona_perkada
        INNER JOIN ref_ssh_perkada as f ON e.id_perkada = f.id_perkada
        WHERE a.jenis_biaya=3 AND f.tahun_berlaku='.Session::get('tahun').' AND e.id_zona='.$zona.' AND c.id_aktivitas_asb='.$akt.' 
        AND ((a.id_satuan1 = c.id_satuan_1 and ifnull(a.id_satuan2,0) = c.id_satuan_2)
                or (a.id_satuan1 = c.id_satuan_1 and ifnull(a.id_satuan3,0) = c.id_satuan_2)
                or (ifnull(a.id_satuan2,0) = c.id_satuan_1 and a.id_satuan1 = c.id_satuan_2)
                or (ifnull(a.id_satuan2,0) = c.id_satuan_1 and ifnull(a.id_satuan3,0) = c.id_satuan_2)
                or (ifnull(a.id_satuan3,0) = c.id_satuan_1 and a.id_satuan1 = c.id_satuan_2)
                or (ifnull(a.id_satuan3,0) = c.id_satuan_1 and ifnull(a.id_satuan2,0) = c.id_satuan_2))');
    
    $ASBKomponenRinci3 = DB::SELECT('SELECT CAST(sum(IFNULL(a.koefisien1,1)*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*d.jml_rupiah) as Decimal(10,3)) as koef,
        "V1" as var  FROM trx_asb_komponen_rinci AS a
        INNER JOIN trx_asb_komponen AS b ON a.id_komponen_asb=b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas AS c ON b.id_aktivitas_asb=c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif AS d ON a.id_tarif_ssh=d.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona as e ON d.id_zona_perkada=e.id_zona_perkada
        INNER JOIN ref_ssh_perkada as f ON e.id_perkada = f.id_perkada
        WHERE a.jenis_biaya=1 AND f.tahun_berlaku='.Session::get('tahun').' AND e.id_zona='.$zona.' AND c.id_aktivitas_asb='.$akt);

    $ASBKomponenRinci5 = DB::SELECT('SELECT CAST(sum(IFNULL(a.koefisien1,1)*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*d.jml_rupiah) as Decimal(10,3)) as koef,
        "V1" as var  FROM trx_asb_komponen_rinci AS a
        INNER JOIN trx_asb_komponen AS b ON a.id_komponen_asb=b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas AS c ON b.id_aktivitas_asb=c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif AS d ON a.id_tarif_ssh=d.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona as e ON d.id_zona_perkada=e.id_zona_perkada
        INNER JOIN ref_ssh_perkada as f ON e.id_perkada = f.id_perkada
        WHERE a.jenis_biaya=2 AND a.hub_driver=1 AND f.tahun_berlaku='.Session::get('tahun').' AND e.id_zona='.$zona.' AND c.id_aktivitas_asb='.$akt);

	$ASBKomponenRinci6 = DB::SELECT('SELECT CAST(sum(IFNULL(a.koefisien1,1)*IFNULL(a.koefisien2,1)*IFNULL(a.koefisien3,1)*d.jml_rupiah) as Decimal(10,3)) as koef,
        "V1" as var  FROM trx_asb_komponen_rinci AS a
        INNER JOIN trx_asb_komponen AS b ON a.id_komponen_asb=b.id_komponen_asb
        INNER JOIN trx_asb_aktivitas AS c ON b.id_aktivitas_asb=c.id_aktivitas_asb
        INNER JOIN ref_ssh_perkada_tarif AS d ON a.id_tarif_ssh=d.id_tarif_ssh
        INNER JOIN ref_ssh_perkada_zona as e ON d.id_zona_perkada=e.id_zona_perkada
        INNER JOIN ref_ssh_perkada as f ON e.id_perkada = f.id_perkada
        WHERE a.jenis_biaya=2 AND a.hub_driver=2 AND f.tahun_berlaku='.Session::get('tahun').' AND e.id_zona='.$zona.' AND c.id_aktivitas_asb='.$akt);
   
    PDF::SetFont('helvetica', 'B', 8);		
    
    PDF::Cell(array_sum($w), 0, '', 'T');
    PDF::Ln();
    if($countrow>=$totalrow)
    {
    	PDF::AddPage('P');
    	$countrow=0;
    	PDF::Ln();
    	$countrow++;
    }
    PDF::Cell('153', '3', '', 0, 0, 'L', 0);
    PDF::Cell(30, 5, 'Rumus Umum ASB :', 0, 'R', 0, 0);
    PDF::Ln();
    if($countrow>=$totalrow)
    {
    	PDF::Ln();
        PDF::AddPage('P');
        $countrow=0;
    	$countrow++;
    }
    
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
    }
    else
    {
    	$rincikoef5=$ASBKomponenRinci5[0]->koef;
    	$rincivar5=".DV1";
    }
    
    if(count($ASBKomponenRinci6)==0)
    {
    	$rincikoef6=0;
    	$rincivar6=".DV2";
    }
    else
    {
    	$rincikoef6=$ASBKomponenRinci6[0]->koef;
    	$rincivar6=".DV2";
    }
    
    PDF::Cell('63', '3', '', 0, 0, 'L', 0);
    PDF::Cell('120', 5, 'Y = '. number_format($rincikoef1, 2, ',', '.').''.$rincivar1.' + '.number_format($rincikoef2, 2, ',','.').$rincivar2.' + ' .number_format($rincikoef4, 2, ',', '.').''.$rincivar4.' + ' .number_format($rincikoef5, 2, ',', '.').''.$rincivar5.' + ' .number_format($rincikoef6, 2, ',', '.').''.$rincivar6.' + '.number_format($rincikoef3, 2, ',','.'), 0, 'R', 0, 0);
    
    PDF::Ln();
    if($countrow>=$totalrow)
    {
    	PDF::AddPage('P');
    	$countrow=0;
    	PDF::Ln();
    	$countrow++;
    }

    Template::footerPotrait();

    PDF::Output('ASBAktivitasKomponenRinciRumus.pdf', 'I');
  }

}
