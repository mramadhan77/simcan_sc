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


class CetakSshSubKelompokController extends Controller
{

  public function printSshSubKelompok()
  {
    Template::settingPage('P','F4');
    Template::setHeader('P');
    PDF::SetFont('helvetica', '', 6);
                
    $data = DB::SELECT('SELECT a.id_golongan_ssh, a.jenis_ssh, a.no_urut, a.uraian_golongan_ssh,
            (SELECT COUNT(c.id_kelompok_ssh) FROM ref_ssh_sub_kelompok AS c
				INNER JOIN ref_ssh_kelompok AS b ON c.id_kelompok_ssh = b.id_kelompok_ssh WHERE b.id_golongan_ssh = a.id_golongan_ssh) + 
			(SELECT COUNT(c.id_kelompok_ssh) FROM ref_ssh_kelompok AS c
                WHERE c.id_golongan_ssh = a.id_golongan_ssh) + 1 AS level_1
            FROM ref_ssh_golongan AS a ORDER BY a.no_urut');

    $jum_level_1 = 1;
    $jum_level_2 = 1;
    $jum_level_3 = 1;
    $jum_level_4 = 1;
    $html ='';
    $html .=  '<html><head>';
    $html .= '</head><body>';
    $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Daftar Sub Kelompok Standar Satuan Harga</div>';
    $html .='<br>';
    $html .='<table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th width="5%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">No Urut</th>
                    <th width="95%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">Uraian Golongan / Kelompok / Sub Kelompok</th>                    
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
        $html .='<td colspan="4" width="95%" style="text-align: left; font-weight:bold; font-size:8px;"><div>'.$row->uraian_golongan_ssh.'</div>';
        $html .='</td>';      
        $html .='</tr>'; 
        $kel = DB::SELECT('SELECT a.id_kelompok_ssh, a.id_golongan_ssh, a.no_urut, a.uraian_kelompok_ssh,
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
            $html .='<td colspan="2" width="90%" style="text-align: left; font-size:7px;">'.$kels->uraian_kelompok_ssh.'</td>';
            $jum_level_2 = $jum_level_2-$kels->level_2-1;
            $html .='</tr>'; 
            $skel = DB::SELECT('SELECT a.id_sub_kelompok_ssh, a.id_kelompok_ssh, a.no_urut, a.uraian_sub_kelompok_ssh
                FROM ref_ssh_sub_kelompok AS a WHERE a.id_kelompok_ssh='.$kels->id_kelompok_ssh.' ORDER BY a.no_urut'); 
                foreach ($skel as $skels) { 
                            $html .='<tr nobr="true">';
                            $html .='<td width="8%" style="text-align: center; font-size:7px;">'.$row->no_urut.'.'.$kels->no_urut.'.'.$skels->no_urut.'</td>';
                            $html .='<td width="82%" style="text-align: left; font-size:7px;">'.$skels->uraian_sub_kelompok_ssh.'</td>';                            
                            $html .='</tr>';
                            $jum_level_1 = $jum_level_2-$kels->level_2;                     
                }

        };
    };

    $html .= '</tbody></table>';

    PDF::writeHTML($html, true, false, true, false, '');
    Template::setFooter('P');

    // close and output PDF document
    PDF::Output('SubKelompokSSH.pdf', 'I');
  }

}

