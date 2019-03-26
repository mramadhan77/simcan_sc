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
use App\Models\RefSshGolongan;
use App\Models\RefSshKelompok;
use App\Models\RefSshSubKelompok;
use App\Models\RefSshTarif;
use App\Models\RefSshRekening;
use App\Models\RefRek5;

ini_set('memory_limit','512M');
ini_set('max_execution_time', 0);
ini_set("pcre.backtrack_limit", "2000000");

class CetakSshTarifController extends Controller
{  

public function printSshTarif()
  { 

    ini_set('memory_limit','512M');
    ini_set('max_execution_time', 0);
    ini_set("pcre.backtrack_limit", "2000000");

	Template::settingPage('P','F4');
    Template::setHeader('P');
    PDF::SetFont('helvetica', '', 6);

    $data = DB::SELECT('SELECT a.id_golongan_ssh, a.jenis_ssh, a.no_urut, a.uraian_golongan_ssh,
		(SELECT COUNT(d.id_tarif_ssh) FROM ref_ssh_tarif AS d
		INNER JOIN ref_ssh_sub_kelompok AS c ON d.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh
		INNER JOIN ref_ssh_kelompok AS b ON c.id_kelompok_ssh = b.id_kelompok_ssh WHERE b.id_golongan_ssh = a.id_golongan_ssh) + 
		(SELECT COUNT(c.id_kelompok_ssh) FROM ref_ssh_sub_kelompok AS c
		INNER JOIN ref_ssh_kelompok AS b ON c.id_kelompok_ssh = b.id_kelompok_ssh WHERE b.id_golongan_ssh = a.id_golongan_ssh) +
		(SELECT COUNT(c.id_kelompok_ssh) FROM ref_ssh_kelompok AS c WHERE c.id_golongan_ssh = a.id_golongan_ssh) + 1 AS level_1
		FROM ref_ssh_golongan AS a ORDER BY a.no_urut');

    $jum_level_1 = 1;
    $jum_level_2 = 1;
    $jum_level_3 = 1;
    $jum_level_4 = 1;
    $html ='';
    $html .=  '<html><head>';
    $html .= '</head><body>';
    $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Daftar Item Standar Satuan Harga</div>';
    $html .='<br>';
    $html .='<table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th width="5%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">No Urut</th>
					<th width="81%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">Uraian Golongan / Kelompok / Sub Kelompok / Item</th>
					<th width="14%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">Satuan</th>                   
                </tr>
                </thead>
            <tbody >';
    foreach ($data as $row){                                           
        $reset_level_2=1;  
        $html .='<tr nobr="true">';
        if ($jum_level_1 <= 1){
            $html .='<td width="5%" rowspan="'.$row->level_1.'" style="padding: 50px; text-align: center; font-weight:bold; font-size:8px;">'.$row->no_urut.'</td>';            
            $jum_level_1 = $row->level_1;  
        } else {
            $jum_level_1 = $jum_level_1 - 1; 
        };       
        $html .='<td colspan="5" width="95%" style="text-align: left; font-weight:bold; font-size:8px;"><div>'.$row->uraian_golongan_ssh.'</div>';
        $html .='</td>';      
        $html .='</tr>'; 
        $kel = DB::SELECT('SELECT a.id_kelompok_ssh, a.id_golongan_ssh, a.no_urut, a.uraian_kelompok_ssh,
			(SELECT COUNT(d.id_tarif_ssh) FROM ref_ssh_tarif AS d
			INNER JOIN ref_ssh_sub_kelompok AS c ON d.id_sub_kelompok_ssh = c.id_sub_kelompok_ssh
			WHERE c.id_kelompok_ssh = a.id_kelompok_ssh) +
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
            $html .='<td colspan="4" width="90%" style="text-align: left; font-size:7px;">'.$kels->uraian_kelompok_ssh.'</td>';
            $jum_level_2 = $jum_level_2-$kels->level_2-2;
            $html .='</tr>'; 
            $skel = DB::SELECT('SELECT a.id_sub_kelompok_ssh, a.id_kelompok_ssh, a.no_urut, a.uraian_sub_kelompok_ssh,
				(SELECT COUNT(d.id_tarif_ssh) FROM ref_ssh_tarif AS d WHERE d.id_sub_kelompok_ssh = a.id_sub_kelompok_ssh) + 1 AS level_3
				FROM ref_ssh_sub_kelompok AS a WHERE a.id_kelompok_ssh='.$kels->id_kelompok_ssh.' ORDER BY a.no_urut'); 
            foreach ($skel as $skels) { 
				$html .='<tr nobr="true">';
				if ($jum_level_3 <= 1){
					$html .='<td width="8%" rowspan="'.$skels->level_3.'" style="padding: 50px; text-align: center; font-weight:bold; font-size:7px;">'.$row->no_urut.'.'.$kels->no_urut.'.'.$skels->no_urut.'</td>';            
					$jum_level_3 = $skels->level_3;  
				} else {
					$jum_level_3 = $jum_level_3 - 3; 
				}; 
				$html .='<td colspan="3" width="82%" style="text-align: left; font-size:7px;">'.$skels->uraian_sub_kelompok_ssh.'</td>'; 							
                $jum_level_3 = $jum_level_3-$skels->level_3-1;                                
				$html .='</tr>';
				$tarif = DB::SELECT('SELECT a.id_tarif_ssh, a.no_urut, a.id_sub_kelompok_ssh, a.uraian_tarif_ssh, a.keterangan_tarif_ssh, a.id_satuan, b.uraian_satuan
					FROM ref_ssh_tarif AS a
					LEFT OUTER JOIN ref_satuan AS b ON a.id_satuan = b.id_satuan 
					WHERE a.id_sub_kelompok_ssh='.$skels->id_sub_kelompok_ssh.' ORDER BY a.no_urut'); 
                foreach ($tarif as $tarifs) { 
                            $html .='<tr nobr="true">';
                            $html .='<td width="8%" style="text-align: center; font-size:7px;">'.$row->no_urut.'.'.$kels->no_urut.'.'.$skels->no_urut.'.'.$tarifs->no_urut.'</td>';
							$html .='<td width="60%" style="text-align: left; font-size:7px;">'.$tarifs->uraian_tarif_ssh.'</td>'; 
							$html .='<td width="14%" style="text-align: left; font-size:7px;">'.$tarifs->uraian_satuan.'</td>'; 							
                            $jum_level_1 = $jum_level_3-$skels->level_3;                                
                            $html .='</tr>';                
                };                
            };
        };
    };

    $html .= '</tbody></table>';   

    PDF::writeHTML($html, true, false, true, false, '');
    Template::setFooter('P');

    // close and output PDF document
    PDF::Output('TarifSSH.pdf', 'I');
}

}