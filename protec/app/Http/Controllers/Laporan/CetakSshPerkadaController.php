<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Response;
use Session;
use PDF;
use App\TemplateReport As Template;


class CetakSshPerkadaController extends Controller
{
	
	public function printSshPerkada($id_perkada,$id_zona)
	{
		Template::settingPage('P','F4');
		Template::setHeader('P');
		PDF::SetFont('helvetica', '', 6);

		if($id_zona > 0) {
			$query=' AND a.id_zona='.$id_zona.' ORDER BY a.no_urut LIMIT 1';
		} else {
			$query=' ORDER BY a.no_urut LIMIT 1';
		}

		$perkada = DB::SELECT('SELECT DISTINCT id_perkada, nomor_perkada, tanggal_perkada, tahun_berlaku, id_perkada_induk, 
			id_perubahan, uraian_perkada, `status`, flag, created_at, updated_at
			FROM ref_ssh_perkada WHERE id_perkada='.$id_perkada.' LIMIT 1');

		foreach ($perkada as $perkadas){
			$jum_level_1 = 1;
			$jum_level_2 = 1;
			$jum_level_3 = 1;
			$jum_level_4 = 1;
			$html ='';
			$html .=  '<html><head>';
			$html .= '</head><body>';
			$html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Daftar Rincian Standar Satuan Harga</div>';		
			$html .='<div style="text-align: center; font-size:10px; font-weight: bold;">Perkada SSH Nomor : '.$perkadas->nomor_perkada.'</div>';
			$html .='<br>';
			$zona = DB::SELECT('SELECT DISTINCT a.id_zona_perkada, a.no_urut, a.id_perkada, a.id_perubahan, a.id_zona, a.nama_zona, b.diskripsi_zona, b.keterangan_zona
				FROM ref_ssh_perkada_zona AS a
				INNER JOIN ref_ssh_zona AS b ON a.id_zona = b.id_zona WHERE a.id_perkada='.$perkadas->id_perkada.' '.$query);
			foreach ($zona as $zonas){
				$html .='<div style="text-align: left; font-size:10px; font-weight: bold;">Zona Wilayah Pemberlakukan SSH : '.$zonas->keterangan_zona.'</div>';
				$html .='<br>';
				$html .='<table border="1" cellpadding="4" cellspacing="0">
						<thead>
							<tr>
								<th width="5%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">No Urut</th>
								<th width="67%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">Uraian Golongan / Kelompok / Sub Kelompok / Item</th>
								<th width="14%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">Satuan</th>
								<th width="14%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">Harga Satuan (Rp)</th>                    
							</tr>
							</thead>
						<tbody >';
							
				$data = DB::SELECT('SELECT a.id_golongan_ssh, a.jenis_ssh, a.no_urut, a.uraian_golongan_ssh,
					(SELECT COUNT(x.id_tarif_ssh) FROM ref_ssh_perkada_tarif AS x
					INNER JOIN ref_ssh_tarif AS d ON x.id_tarif_ssh = d.id_tarif_ssh
					INNER JOIN ref_ssh_sub_kelompok AS c ON d.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh
					INNER JOIN ref_ssh_kelompok AS b ON c.id_kelompok_ssh = b.id_kelompok_ssh WHERE b.id_golongan_ssh = a.id_golongan_ssh AND x.id_zona_perkada='.$zonas->id_zona_perkada.') + 
					(SELECT COUNT(c.id_kelompok_ssh) FROM ref_ssh_sub_kelompok AS c
					INNER JOIN ref_ssh_kelompok AS b ON c.id_kelompok_ssh = b.id_kelompok_ssh WHERE b.id_golongan_ssh = a.id_golongan_ssh) +
					(SELECT COUNT(c.id_kelompok_ssh) FROM ref_ssh_kelompok AS c WHERE c.id_golongan_ssh = a.id_golongan_ssh) + 1 AS level_1
					FROM ref_ssh_golongan AS a ORDER BY a.no_urut');
				foreach ($data as $row){                                           
					$reset_level_2=1;  
					$html .='<tr nobr="true">';
					if ($jum_level_1 <= 1){
						$html .='<td width="5%" rowspan="'.$row->level_1.'" style="padding: 50px; text-align: center; font-weight:bold; font-size:8px;">'.$row->no_urut.'</td>';            
						$jum_level_1 = $row->level_1;  
					} else {
						$jum_level_1 = $jum_level_1 - 1; 
					};       
					$html .='<td colspan="6" width="95%" style="text-align: left; font-weight:bold; font-size:8px;"><div>'.$row->uraian_golongan_ssh.'</div>';
					$html .='</td>';      
					$html .='</tr>'; 
					$kel = DB::SELECT('SELECT a.id_kelompok_ssh, a.id_golongan_ssh, a.no_urut, a.uraian_kelompok_ssh,
						(SELECT COUNT(x.id_tarif_ssh) FROM ref_ssh_perkada_tarif AS x
							INNER JOIN ref_ssh_tarif AS d ON x.id_tarif_ssh = d.id_tarif_ssh
							INNER JOIN ref_ssh_sub_kelompok AS c ON d.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh 
							WHERE c.id_kelompok_ssh = a.id_kelompok_ssh AND x.id_zona_perkada='.$zonas->id_zona_perkada.') +
						(SELECT COUNT(c.id_kelompok_ssh) FROM ref_ssh_sub_kelompok AS c
						WHERE c.id_kelompok_ssh = a.id_kelompok_ssh) + 1 AS level_2
						FROM ref_ssh_kelompok AS a WHERE a.id_golongan_ssh='.$row->id_golongan_ssh.' ORDER BY a.no_urut');
					foreach ($kel as $kels){  
						$html .='<tr nobr="true">';
						if ($jum_level_2 <= 1){
							$html .='<td width="5%" rowspan="'.$kels->level_2.'" style="padding: 50px; text-align: center; font-weight:bold; font-size:7px;">'.$row->no_urut.'.'.$kels->no_urut.'</td>';            
							$jum_level_2 = $kels->level_2;  
						} else {
							$jum_level_2 = $jum_level_2 - 2; 
						};  
						$html .='<td colspan="5" width="90%" style="text-align: left; font-size:7px;">'.$kels->uraian_kelompok_ssh.'</td>';
						$jum_level_2 = $jum_level_2-$kels->level_2-2;
						$html .='</tr>'; 
						$skel = DB::SELECT('SELECT a.id_sub_kelompok_ssh, a.id_kelompok_ssh, a.no_urut, a.uraian_sub_kelompok_ssh,
							(SELECT COUNT(x.id_tarif_ssh) FROM ref_ssh_perkada_tarif AS x
							INNER JOIN ref_ssh_tarif AS d ON x.id_tarif_ssh = d.id_tarif_ssh WHERE d.id_sub_kelompok_ssh = a.id_sub_kelompok_ssh 
							AND x.id_zona_perkada='.$zonas->id_zona_perkada.') + 1 AS level_3
							FROM ref_ssh_sub_kelompok AS a WHERE a.id_kelompok_ssh='.$kels->id_kelompok_ssh.' ORDER BY a.no_urut'); 
						foreach ($skel as $skels) { 
							$html .='<tr nobr="true">';
							if ($jum_level_3 <= 1){
								$html .='<td width="8%" rowspan="'.$skels->level_3.'" style="padding: 50px; text-align: center; font-weight:bold; font-size:7px;">'.$row->no_urut.'.'.$kels->no_urut.'.'.$skels->no_urut.'</td>';            
								$jum_level_3 = $skels->level_3;  
							} else {
								$jum_level_3 = $jum_level_3 - 3; 
							}; 
							$html .='<td colspan="4" width="82%" style="text-align: left; font-size:7px;">'.$skels->uraian_sub_kelompok_ssh.'</td>'; 							
							$jum_level_3 = $jum_level_3-$skels->level_3-1;                                
							$html .='</tr>';
							$tarif = DB::SELECT('SELECT (@id:=@id+1) AS no_urut, a.id_tarif_perkada, a.id_tarif_ssh, a.no_urut AS urut, b.id_sub_kelompok_ssh, b.uraian_tarif_ssh, a.id_zona_perkada, b.id_satuan, c.uraian_satuan, a.jml_rupiah
							FROM ref_ssh_perkada_tarif AS a
								INNER JOIN	ref_ssh_tarif AS b ON a.id_tarif_ssh = b.id_tarif_ssh
								LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan = c.id_satuan, (SELECT @id:=0) x
								WHERE b.id_sub_kelompok_ssh='.$skels->id_sub_kelompok_ssh.' AND a.id_zona_perkada='.$zonas->id_zona_perkada.' ORDER BY a.no_urut'); 
							foreach ($tarif as $tarifs) { 
										$html .='<tr nobr="true">';
										$html .='<td width="8%" style="text-align: center; font-size:7px;">'.$row->no_urut.'.'.$kels->no_urut.'.'.$skels->no_urut.'.'.$tarifs->no_urut.'</td>';
										$html .='<td width="46%" style="text-align: left; font-size:7px;">'.$tarifs->uraian_tarif_ssh.'</td>'; 
										$html .='<td width="14%" style="text-align: center; font-size:7px;">'.$tarifs->uraian_satuan.'</td>';  
										$html .='<td width="14%" style="text-align: right; font-size:7px;">'.number_format($tarifs->jml_rupiah,0,",",".").'</td>';							
										$jum_level_1 = $jum_level_3-$skels->level_3;                                
										$html .='</tr>';                
							};                
						};
					};
				};
			};
		};

		$html .= '</tbody></table>';

		PDF::writeHTML($html, true, false, true, false, '');
		Template::setFooter('P');

		// close and output PDF document
		PDF::Output('PerkadaSSH.pdf', 'I');
	}

	// public function printSshPerkada($id_perkada,$id_zona)
	// {
	// 	Template::settingPage('P','F4');
	// 	Template::setHeader('P');
	// 	PDF::SetFont('helvetica', '', 6);

	// 	$view = \View::make('myview_name');
	// 	$html = $view->render();
	// 	// PDF::writeHTML(view('your.view')->render());

	// 	PDF::writeHTML($html, true, false, true, false, '');
	// 	Template::setFooter('P');

	// 	// close and output PDF document
	// 	PDF::Output('PerkadaSSH.pdf', 'I');
	// }
	
}
