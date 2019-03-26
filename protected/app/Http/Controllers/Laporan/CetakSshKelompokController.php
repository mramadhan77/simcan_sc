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


class CetakSshKelompokController extends Controller
{

  public function printSshKelompok()
  {
    Template::settingPage('P','F4');
    Template::setHeader('P');
    PDF::SetFont('helvetica', '', 6);
                
    $data = DB::SELECT('SELECT a.id_golongan_ssh, a.jenis_ssh, a.no_urut, a.uraian_golongan_ssh,
            (SELECT COUNT(c.id_kelompok_ssh)+1 FROM ref_ssh_kelompok AS c WHERE c.id_golongan_ssh = a.id_golongan_ssh) AS level_1
            FROM ref_ssh_golongan AS a ORDER BY a.no_urut');

    $jum_level_1 = 1;
    
    $html ='';
    $html .=  '<html><head>';
    // $html .=  '<style>
    //                 td, th {
    //                     border: 1px solid #000000;
    //                     padding: 50px;
    //                 }
    //                 </style>';
    $html .= '</head><body>';
    $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Daftar Kelompok Standar Satuan Harga</div>';
    $html .='<br>';
    $html .='<table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">No Urut</th>
                    <th width="90%" style="text-align: center; vertical-align:middle; font-weight:bold; font-size:8px;">Uraian Golongan / Kelompok</th>                    
                </tr>
                </thead>
            <tbody >';
    foreach ($data as $row){
        $html .='<tr nobr="true">';
        if ($jum_level_1 <= 1){
            $html .='<td width="10%" rowspan="'.$row->level_1.'" style="padding: 50px; text-align: center; font-weight:bold; font-size:8px;">'.$row->no_urut.'</td>';            
            $jum_level_1 = $row->level_1;  
        } else {
            $jum_level_1 = $jum_level_1 - 1; 
        };       
        $html .='<td colspan="2" width="90%" style="text-align: left; font-weight:bold; font-size:8px;"><div>'.$row->uraian_golongan_ssh.'</div>';
        $html .='</td>';        
        $html .='</tr>';
        $kel = DB::SELECT('SELECT id_kelompok_ssh, id_golongan_ssh, no_urut, uraian_kelompok_ssh
            FROM ref_ssh_kelompok WHERE id_golongan_ssh='.$row->id_golongan_ssh.' ORDER BY no_urut');
        foreach ($kel as $kels){
            $html .='<tr nobr="true">';
            $html .='<td width="10%" style="text-align: center; font-size:7px;">'.$row->no_urut.'.'.$kels->no_urut.'</td>';
            $html .='<td width="80%" style="text-align: left; font-size:7px;">'.$kels->uraian_kelompok_ssh.'</td>';
            $jum_level_1 = $jum_level_1 - 1; 
            $html .='</tr>';
        };
    };

    $html .= '</tbody></table>';

    PDF::writeHTML($html, true, false, true, false, '');
    Template::setFooter('P');

    // close and output PDF document
    PDF::Output('kelompokSSH.pdf', 'I');
  }

}

