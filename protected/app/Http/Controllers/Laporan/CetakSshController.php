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


class CetakSshController extends Controller
{

  public function printGolonganSsh()
  {
    Template::settingPage('P','F4');
    Template::setHeader('P');
    PDF::SetFont('helvetica', '', 8);
                
    $data = DB::SELECT('SELECT a.id_golongan_ssh, a.jenis_ssh, a.no_urut, a.uraian_golongan_ssh
                FROM ref_ssh_golongan AS a ORDER BY a.no_urut');

    $jum_level_1 = 1;
    $jum_level_2 = 1;
    $jum_level_3 = 1;
    $jum_level_4 = 1;
    $jum_level_5 = 1;
    $html ='';
    $html .=  '<html><head>';
    $html .=  '<style>
                    td, th {
                        border: 1px solid #000000;
                        padding: 50px;
                    }
                    </style>';
    $html .= '</head><body>';
    $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Daftar Golongan Standar Satuan Harga</div>';
    $html .='<br>';
    $html .='<table border="0" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center; vertical-align:middle">No Urut</th>
                    <th width="90%" style="text-align: center; vertical-align:middle">Golongan SSH</th>
                </tr>
                </thead>
            <tbody >';
    foreach ($data as $row){
        $html .='<tr nobr="true">';
        $html .='<td width="10%" style="text-align: center;">'.$row->no_urut.'</td>';
        $html .='<td width="90%" style="text-align: left;">'.$row->uraian_golongan_ssh.'</td>';
        $html .='</tr>';
    };

    $html .= '</tbody></table>';

    PDF::writeHTML($html, true, false, true, false, '');
    Template::setFooter('P');

    // close and output PDF document
    PDF::Output('golonganSSH.pdf', 'I');
  }

}
