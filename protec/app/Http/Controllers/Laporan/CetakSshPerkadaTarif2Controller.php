<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Response;
use Session;
use PDF;
use DB;
use App\Http\Controllers\Laporan\TemplateReport As Template;

ini_set('memory_limit','512M');
ini_set('max_execution_time', 0);

class CetakSshPerkadaTarif2Controller extends Controller
{

	public function printSshItemDetail() {
	  
		Template::settingPagePotrait();
    	Template::headerPotrait();
		
		PDF::SetFont('helvetica', '', 10);		
		// PDF::AddPage('P');

		$countrow=0;
		$totalrow=30;
		
		$header = array('Kode / Uraian','Satuan');
		
		PDF::SetFillColor(200, 200, 200);
		PDF::SetTextColor(0);
		PDF::SetDrawColor(255, 255, 255);
		PDF::SetLineWidth(0);
		PDF::SetFont('helvetica', 'B', 12);
		
		PDF::Cell('180', 5, Session::get('xPemda'), 1, 0, 'C', 0);
		PDF::Ln();
		$countrow++;
		PDF::Cell('180', 5, 'DAFTAR ITEM STANDAR SATUAN HARGA', 1, 0, 'C', 0);
		PDF::Ln();
		PDF::Ln();
		$countrow++;
		PDF::SetFont('', 'B');
		PDF::SetFont('helvetica', 'B', 10);
		
		$wh = array(150,30);
		$w = array(5,145,30);	
		$w1 = array(5,7,138,30);	
		$w2 = array(5,7,13,125,30);	
		$w3 = array(5,7,13,18,107,30);	
		$num_headers = count($header);
		for($i = 0; $i < $num_headers; ++$i) {
				PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
		}
		PDF::Ln();
		$countrow++;
		
		// PDF::SetFillColor(224, 235, 255);
		PDF::SetTextColor(0);
		PDF::SetFont('helvetica', '', 8);
		
		$fill = 0;
		$height = 8;		
		$sshGolongan = DB::SELECT('SELECT DISTINCT id_golongan_ssh, no_urut, uraian_golongan_ssh, 
			CONCAT(no_urut," - ",uraian_golongan_ssh) AS uraian_golongan FROM ref_ssh_golongan 
			WHERE no_urut < 900 ORDER BY no_urut');
		
		// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

		foreach($sshGolongan as $row) {
			PDF::MultiCell($w[0], $height, $row->no_urut, 0, 'L', 0,0);
			PDF::MultiCell($w[1], $height, $row->uraian_golongan_ssh, 0, 'L', 0,0);
			PDF::MultiCell($w[2], $height, '', 0, 'L', 0, 0);
			
			PDF::Ln();
			$countrow++;
			if($countrow>=$totalrow)
			{					
				Template::footerPotrait();
				PDF::SetFont('helvetica', 'B', 10);
				PDF::AddPage('P');
				$countrow=0;
				for($i = 0; $i < $num_headers; ++$i) {
					PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
				}
				PDF::Ln();
				$countrow++;
				PDF::SetFont('helvetica', '', 8);
			}
			
			$sshKelompok = DB::SELECT('SELECT DISTINCT a.id_golongan_ssh, b.id_kelompok_ssh, b.no_urut, b.uraian_kelompok_ssh,
				CONCAT(a.no_urut,".",b.no_urut) AS uraian_kelompok
				FROM ref_ssh_golongan AS a
				INNER JOIN ref_ssh_kelompok AS b ON b.id_golongan_ssh = a.id_golongan_ssh
				WHERE b.id_golongan_ssh='.$row->id_golongan_ssh.' ORDER BY b.no_urut');

			foreach($sshKelompok as $row2) {
				PDF::MultiCell($w1[0], $height, '', 0, 'L', 0, 0);
				PDF::MultiCell($w1[1], $height, $row2->uraian_kelompok, 0, 'L', 0, 0);
				PDF::MultiCell($w1[2], $height, $row2->uraian_kelompok_ssh, 0, 'L', 0, 0);
				PDF::MultiCell($w1[3], $height, '', 0, 'L', 0, 0);
				PDF::Ln();
				$countrow++;
				if($countrow>=$totalrow)
				{				
					Template::footerPotrait();
					PDF::SetFont('helvetica', 'B', 10);
					PDF::AddPage('P');
					$countrow=0;
					for($i = 0; $i < $num_headers; ++$i) {
						PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
					}
					PDF::Ln();
					$countrow++;
					PDF::SetFont('helvetica', '', 8);
				}
				$sshSubKelompok = DB::SELECT('SELECT DISTINCT a.id_golongan_ssh, b.id_kelompok_ssh, c.id_sub_kelompok_ssh, c.no_urut, c.uraian_sub_kelompok_ssh,
					CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut) AS uraian_subkelompok
					FROM ref_ssh_golongan AS a
					INNER JOIN ref_ssh_kelompok AS b ON b.id_golongan_ssh = a.id_golongan_ssh
					INNER JOIN ref_ssh_sub_kelompok AS c ON c.id_kelompok_ssh = b.id_kelompok_ssh
					WHERE c.id_kelompok_ssh='.$row2->id_kelompok_ssh.' ORDER BY c.no_urut');

				foreach($sshSubKelompok as $row3) {
					PDF::MultiCell($w2[0], $height, '', 0, 'L', 0, 0);
					PDF::MultiCell($w2[1], $height, '', 0, 'L', 0, 0);
					PDF::MultiCell($w2[2], $height, $row3->uraian_subkelompok, 0, 'L', 0, 0);
					PDF::MultiCell($w2[3], $height, $row3->uraian_sub_kelompok_ssh, 0, 'L', 0, 0);
					PDF::MultiCell($w2[4], $height, '',  0, 'C', 0, 0);
					PDF::Ln();
					$countrow++;
					if($countrow>=$totalrow)
					{				
						Template::footerPotrait();
						PDF::SetFont('helvetica', 'B', 10);
						PDF::AddPage('P');
						$countrow=0;
						for($i = 0; $i < $num_headers; ++$i) {
							PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
						}
						PDF::Ln();
						$countrow++;
						PDF::SetFont('helvetica', '', 8);
					}

					$SshPerkadaTarif = DB::SELECT('SELECT a.id_golongan_ssh, b.id_kelompok_ssh, c.id_sub_kelompok_ssh, d.id_tarif_ssh,
						d.no_urut, d.uraian_tarif_ssh, d.id_satuan, e.uraian_satuan,
						CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) AS uraian_item
						FROM  ref_ssh_golongan AS a
						INNER JOIN ref_ssh_kelompok AS b ON b.id_golongan_ssh = a.id_golongan_ssh
						INNER JOIN ref_ssh_sub_kelompok AS c ON c.id_kelompok_ssh = b.id_kelompok_ssh
						INNER JOIN ref_ssh_tarif AS d ON d.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh
						INNER JOIN ref_satuan AS e ON d.id_satuan = e.id_satuan
						WHERE d.id_sub_kelompok_ssh = '.$row3->id_sub_kelompok_ssh.' ORDER BY d.no_urut');

					foreach($SshPerkadaTarif as $row4) {
					
						// $height1=ceil((PDF::GetStringWidth($row4->id_tarif_ssh)/$w1[3]))*3;
						// $height2=ceil((PDF::GetStringWidth($row4->uraian_tarif_ssh)/$w1[7]))*3;
						// $height3=ceil((PDF::GetStringWidth($row4->uraian_satuan)/$w1[8]))*3;						
						
						// $maxhigh =array($height1,$height2,$height3);
						// $height = max($maxhigh);
						PDF::MultiCell($w3[0], $height, '', 0, 'L', 0, 0);
						PDF::MultiCell($w3[1], $height, '', 0, 'L', 0, 0);
						PDF::MultiCell($w3[2], $height, '', 0, 'L', 0, 0);
						PDF::MultiCell($w3[3], $height, $row4->uraian_item, 0, 'L', 0, 0);
						PDF::MultiCell($w3[4], $height, $row4->uraian_tarif_ssh, 0, 'L', 0, 0);
						PDF::MultiCell($w3[5], $height, $row4->uraian_satuan, 0, 'C', 0, 0);
						PDF::Ln();
						$countrow++;
						if($countrow>=$totalrow)
						{				
							Template::footerPotrait();
							PDF::SetFont('helvetica', 'B', 10);
							PDF::AddPage('P');
							$countrow=0;
							for($i = 0; $i < $num_headers; ++$i) {
								PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
							}
							PDF::Ln();
							$countrow++;
							PDF::SetFont('helvetica', '', 8);
						}
					}
				}
			}
		}
		PDF::Cell(array_sum($w), 0, '', 'T');
		Template::footerPotrait();
		
		PDF::Output('DaftarItemSSH.pdf', 'I');															
  }

  public function printSshPerkadaTarif($id_perkada,$id_zona) {
	  
	Template::settingPagePotrait();
    Template::headerPotrait();
		
	PDF::SetFont('helvetica', '', 10);	

	$countrow=0;
	$totalrow=30;
	
	$header = array('Kode / Uraian','Satuan','Harga');
	
	PDF::SetFillColor(200, 200, 200);
	PDF::SetTextColor(0);
	PDF::SetDrawColor(255, 255, 255);
	PDF::SetLineWidth(0);
	PDF::SetFont('helvetica', 'B', 12);
	
	PDF::Cell('180', 5, Session::get('xPemda'), 1, 0, 'C', 0);
	PDF::Ln();
	$countrow++;
	PDF::Cell('180', 5, 'DAFTAR TARIF ITEM STANDAR SATUAN HARGA', 1, 0, 'C', 0);
	PDF::Ln();
	PDF::Ln();
	$countrow++;
	PDF::SetFont('', 'B');
	PDF::SetFont('helvetica', 'B', 10);
	
	$wh = array(140,20,20);
	$w = array(5,135,20,20);	
	$w1 = array(5,7,128,20,20);	
	$w2 = array(5,7,13,115,20,20);	
	$w3 = array(5,7,13,18,97,20,20);	
	$num_headers = count($header);
	for($i = 0; $i < $num_headers; ++$i) {
			PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
	}
	PDF::Ln();
	$countrow++;
	
	// PDF::SetFillColor(224, 235, 255);
	PDF::SetTextColor(0);
	PDF::SetFont('helvetica', '', 7);
	
	$fill = 0;
	$height = 8;		
	$sshGolongan = DB::SELECT('SELECT DISTINCT id_golongan_ssh, no_urut, uraian_golongan_ssh, 
		CONCAT(no_urut," - ",uraian_golongan_ssh) AS uraian_golongan FROM ref_ssh_golongan 
		WHERE no_urut < 900 ORDER BY no_urut');
	
	// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

	foreach($sshGolongan as $row) {
		PDF::MultiCell($w[0], $height, $row->no_urut, 0, 'L', 0,0);
		PDF::MultiCell($w[1], $height, $row->uraian_golongan_ssh, 0, 'L', 0,0);
		PDF::MultiCell($w[2], $height, '', 0, 'L', 0, 0);
		PDF::MultiCell($w[3], $height, '', 0, 'L', 0, 0);
		
		PDF::Ln();
		$countrow++;
		if($countrow>=$totalrow)
		{					
			PDF::SetFont('helvetica', 'B', 10);
			PDF::AddPage('P');
			$countrow=0;
			for($i = 0; $i < $num_headers; ++$i) {
				PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
			}
			PDF::Ln();
			$countrow++;
			PDF::SetFont('helvetica', '', 7);
		}
		
		$sshKelompok = DB::SELECT('SELECT DISTINCT a.id_golongan_ssh, b.id_kelompok_ssh, b.no_urut, b.uraian_kelompok_ssh,
			CONCAT(a.no_urut,".",b.no_urut) AS uraian_kelompok
			FROM ref_ssh_golongan AS a
			INNER JOIN ref_ssh_kelompok AS b ON b.id_golongan_ssh = a.id_golongan_ssh
			WHERE b.id_golongan_ssh='.$row->id_golongan_ssh.' ORDER BY b.no_urut');

		foreach($sshKelompok as $row2) {
			PDF::MultiCell($w1[0], $height, '', 0, 'L', 0, 0);
			PDF::MultiCell($w1[1], $height, $row2->uraian_kelompok, 0, 'L', 0, 0);
			PDF::MultiCell($w1[2], $height, $row2->uraian_kelompok_ssh, 0, 'L', 0, 0);
			PDF::MultiCell($w1[3], $height, '', 0, 'L', 0, 0);
			PDF::MultiCell($w1[4], $height, '', 0, 'L', 0, 0);
			PDF::Ln();
			$countrow++;
			if($countrow>=$totalrow)
			{				
				Template::footerPotrait();
				PDF::SetFont('helvetica', 'B', 10);
				PDF::AddPage('P');
				$countrow=0;
				for($i = 0; $i < $num_headers; ++$i) {
					PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
				}
				PDF::Ln();
				$countrow++;
				PDF::SetFont('helvetica', '', 7);
			}
			$sshSubKelompok = DB::SELECT('SELECT DISTINCT a.id_golongan_ssh, b.id_kelompok_ssh, c.id_sub_kelompok_ssh, c.no_urut, c.uraian_sub_kelompok_ssh,
				CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut) AS uraian_subkelompok
				FROM ref_ssh_golongan AS a
				INNER JOIN ref_ssh_kelompok AS b ON b.id_golongan_ssh = a.id_golongan_ssh
				INNER JOIN ref_ssh_sub_kelompok AS c ON c.id_kelompok_ssh = b.id_kelompok_ssh
				WHERE c.id_kelompok_ssh='.$row2->id_kelompok_ssh.' ORDER BY c.no_urut');

			foreach($sshSubKelompok as $row3) {
				PDF::MultiCell($w2[0], $height, '', 0, 'L', 0, 0);
				PDF::MultiCell($w2[1], $height, '', 0, 'L', 0, 0);
				PDF::MultiCell($w2[2], $height, $row3->uraian_subkelompok, 0, 'L', 0, 0);
				PDF::MultiCell($w2[3], $height, $row3->uraian_sub_kelompok_ssh, 0, 'L', 0, 0);
				PDF::MultiCell($w2[4], $height, '',  0, 'C', 0, 0);
				PDF::MultiCell($w2[5], $height, '',  0, 'C', 0, 0);
				PDF::Ln();
				$countrow++;
				if($countrow>=$totalrow)
				{				
					Template::footerPotrait();
					PDF::SetFont('helvetica', 'B', 10);
					PDF::AddPage('P');
					$countrow=0;
					for($i = 0; $i < $num_headers; ++$i) {
						PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
					}
					PDF::Ln();
					$countrow++;
					PDF::SetFont('helvetica', '', 7);
				}

				$SshPerkadaTarif = DB::SELECT('SELECT a.id_golongan_ssh, b.id_kelompok_ssh, c.id_sub_kelompok_ssh, d.id_tarif_ssh,
					d.no_urut, d.uraian_tarif_ssh, CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) AS uraian_item,
					d.id_satuan, e.uraian_satuan, f.jml_rupiah, g.id_zona, h.id_perkada
					FROM ref_ssh_golongan AS a
					INNER JOIN ref_ssh_kelompok AS b ON b.id_golongan_ssh = a.id_golongan_ssh
					INNER JOIN ref_ssh_sub_kelompok AS c ON c.id_kelompok_ssh = b.id_kelompok_ssh
					INNER JOIN ref_ssh_tarif AS d ON d.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh
					INNER JOIN ref_satuan AS e ON d.id_satuan = e.id_satuan
					INNER JOIN ref_ssh_perkada_tarif AS f ON d.id_tarif_ssh = f.id_tarif_ssh
					INNER JOIN ref_ssh_perkada_zona AS g ON f.id_zona_perkada = g.id_zona_perkada
					INNER JOIN ref_ssh_perkada AS h ON g.id_perkada = h.id_perkada
					WHERE d.id_sub_kelompok_ssh = '.$row3->id_sub_kelompok_ssh.' AND g.id_zona='.$id_zona.' AND h.id_perkada='.$id_perkada.' ORDER BY d.no_urut');

				foreach($SshPerkadaTarif as $row4) {
				
					// $height1=ceil((PDF::GetStringWidth($row4->id_tarif_ssh)/$w1[3]))*3;
					// $height2=ceil((PDF::GetStringWidth($row4->uraian_tarif_ssh)/$w1[7]))*3;
					// $height3=ceil((PDF::GetStringWidth($row4->uraian_satuan)/$w1[8]))*3;						
					
					// $maxhigh =array($height1,$height2,$height3);
					// $height = max($maxhigh);
					PDF::MultiCell($w3[0], $height, '', 0, 'L', 0, 0);
					PDF::MultiCell($w3[1], $height, '', 0, 'L', 0, 0);
					PDF::MultiCell($w3[2], $height, '', 0, 'L', 0, 0);
					PDF::MultiCell($w3[3], $height, $row4->uraian_item, 0, 'L', 0, 0);
					PDF::MultiCell($w3[4], $height, $row4->uraian_tarif_ssh, 0, 'L', 0, 0);
					PDF::MultiCell($w3[5], $height, $row4->uraian_satuan, 0, 'C', 0, 0);
					PDF::MultiCell($w3[6], $height, number_format($row4->jml_rupiah, 2, ',', '.'), 0, 'R', 0, 0);
					PDF::Ln();
					$countrow++;
					if($countrow>=$totalrow)
					{				
						Template::footerPotrait();
						PDF::SetFont('helvetica', 'B', 10);
						PDF::AddPage('P');
						$countrow=0;
						for($i = 0; $i < $num_headers; ++$i) {
							PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
						}
						PDF::Ln();
						$countrow++;
						PDF::SetFont('helvetica', '', 7);
					}
				}
			}
		}
	}
	PDF::Cell(array_sum($w), 0, '', 'T');	
	Template::footerPotrait();
	
	PDF::Output('PerkadaTarifSSH.pdf', 'I');															
}

}

