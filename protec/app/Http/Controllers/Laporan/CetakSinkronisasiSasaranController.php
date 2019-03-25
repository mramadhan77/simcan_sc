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
use App\Http\Controllers\Laporan\TemplateReport As Template;


class CetakSinkronisasiSasaranController extends Controller
{

  public function printSinkronisasi($unit, $id_renstra)
  {
    Template::settingPageLandscape();
    Template::headerLandscape();
    
    // set font
    PDF::SetFont('helvetica', '', 6);

    $data_unit = DB::SELECT('SELECT a.id_rpjmd, a.id_renstra, a.id_unit, a.nomor_renstra, a.tanggal_renstra, a.uraian_renstra, a.nm_pimpinan, a.nip_pimpinan, 
            a.jabatan_pimpinan, a.sumber_data, a.created_at, a.update_at, b.nm_unit, CONCAT(c.kd_urusan,".",c.kd_bidang,".",b.kd_unit) AS kd_unit
            FROM trx_renstra_dokumen AS a
            INNER JOIN ref_unit AS b ON a.id_unit = b.id_unit
            INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang
            WHERE a.id_unit='.$unit.' AND a.id_renstra='.$id_renstra.' LIMIT 1');

    $data = DB::SELECT('SELECT DISTINCT 
            (SELECT COUNT(m.id_program_renstra) FROM trx_renstra_program AS m
            INNER JOIN trx_renstra_sasaran AS n ON m.id_sasaran_renstra = n.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan AS o ON n.id_tujuan_renstra = o.id_tujuan_renstra
            INNER JOIN trx_renstra_misi AS p ON o.id_misi_renstra = p.id_misi_renstra
            INNER JOIN trx_renstra_visi AS q ON p.id_visi_renstra = q.id_visi_renstra 
            WHERE o.id_tujuan_renstra=c.id_tujuan_renstra) AS level_1,
            (SELECT COUNT(m.id_program_renstra) FROM trx_renstra_program AS m
            INNER JOIN trx_renstra_sasaran AS n ON m.id_sasaran_renstra = n.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan AS o ON n.id_tujuan_renstra = o.id_tujuan_renstra
            INNER JOIN trx_renstra_misi AS p ON o.id_misi_renstra = p.id_misi_renstra
            INNER JOIN trx_renstra_visi AS q ON p.id_visi_renstra = q.id_visi_renstra 
            WHERE n.id_sasaran_renstra=d.id_sasaran_renstra) AS level_2,
            e.id_program_rpjmd, d.id_sasaran_renstra, c.id_tujuan_renstra, 
            e.uraian_program_renstra, d.uraian_sasaran_renstra, c.uraian_tujuan_renstra,
            COALESCE(g.uraian_sasaran_rpjmd,"((null/kosong))") AS sasaran_sasaran ,h.uraian_sasaran_rpjmd AS sasaran_program,
            CASE  
                WHEN g.id_sasaran_rpjmd IS NULL THEN "Kosong"
                WHEN g.id_sasaran_rpjmd = f.id_sasaran_rpjmd THEN g.uraian_sasaran_rpjmd
            ELSE "Tidak Valid" END AS keterangan_valid,
            CASE d.id_sasaran_rpjmd WHEN f.id_sasaran_rpjmd THEN 0 ELSE 1 END AS valid
            FROM trx_renstra_visi AS a
            INNER JOIN trx_renstra_misi AS b ON a.id_visi_renstra = b.id_visi_renstra
            INNER JOIN trx_renstra_tujuan AS c ON b.id_misi_renstra = c.id_misi_renstra
            INNER JOIN trx_renstra_sasaran AS d ON c.id_tujuan_renstra = d.id_tujuan_renstra
            INNER JOIN trx_renstra_program AS e ON d.id_sasaran_renstra = e.id_sasaran_renstra
            LEFT OUTER JOIN trx_rpjmd_program AS f ON e.id_program_rpjmd = f.id_program_rpjmd
            LEFT OUTER JOIN trx_rpjmd_sasaran AS g ON d.id_sasaran_rpjmd = g.id_sasaran_rpjmd
            LEFT OUTER JOIN trx_rpjmd_sasaran AS h ON f.id_sasaran_rpjmd = h.id_sasaran_rpjmd
            WHERE a.id_unit ='.$unit.' AND a.id_renstra='.$id_renstra);

    $jum_level_1 = 1;
    $jum_level_2 = 1;
    $nomor_1 = 1;
    $html ='';
    $html .=  '<html><head>';
    $html .=  '<style>
                    td, th {
                        border: 1px solid #000000;
                        padding: 50px;
                    }
                    </style>';
    $html .= '</head><body>';
    $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Sinkronisasi Sasaran Renstra dengan RPJMD</div>';
    foreach ($data_unit as $units){
        $html .='<div style="text-align: center; font-size:14px; font-weight: bold;">'.$units->kd_unit.' - '.$units->nm_unit.'</div>';
        $html .='<div style="text-align: center; font-size:10px; font-weight: bold; font-style: italic"> Dokumen Renstra Nomor : '.$units->nomor_renstra.'</div>';
    };
    $html .='<br>';
    $html .='<table border="0" cellpadding="4" cellspacing="0">
            <thead >
                <tr style="background-color:#e5dede; text-align: center;" >
                    <th width="5%" rowspan="2" style="text-align: center; vertical-align:middle;">No</th>
                    <th width="20%" rowspan="2" style="text-align: center; vertical-align:middle;">Tujuan</th>
                    <th width="20%" rowspan="2" style="text-align: center; vertical-align:middle;">Sasaran</th>
                    <th width="20%" rowspan="2" style="text-align: center; vertical-align:middle;">Program</th>
                    <th width="25%" colspan="2" style="text-align: center; vertical-align:middle;">Sasaran RPJMD</th>
                    <th width="10%" rowspan="2" style="text-align: center; vertical-align:middle;">Keterangan</th>
                </tr>
                <tr style="background-color:#e5dede; text-align: center;">
                    <th width="13%" style="text-align: center;">Sasaran Renstra</th>
                    <th width="12%" style="text-align: center;">Program Renstra</th>
                </tr>
                </thead>
            <tbody >';
    
    foreach ($data as $row){
        $html.='<tr nobr="true">';
        if ($jum_level_1 <= 1){
            $html.='<td width="5%"  rowspan="'.$row->level_1.'" style="text-align: justify;">'.$nomor_1.'</td>';
            $html.='<td width="20%" rowspan="'.$row->level_1.'" style="text-align: justify;">'.$row->uraian_tujuan_renstra.'</td>';
            $jum_level_1 = $row->level_1;
            $nomor_1++;  
        } else {
            $jum_level_1 = $jum_level_1 - 1; 
        }; 
        if ($jum_level_2 <= 1){
            $html.='<td width="20%" rowspan="'.$row->level_2.'" style="text-align: justify;">'.$row->uraian_sasaran_renstra.'</td>';
            $jum_level_2 = $row->level_2;  
        } else {
            $jum_level_2 = $jum_level_2 - 1; 
        };       
        $html.='<td width="20%" style="text-align: justify;">'.$row->uraian_program_renstra.'</td>';      
        $html.='<td width="13%" style="text-align: justify;">'.$row->sasaran_sasaran.'</td>';      
        $html.='<td width="12%" style="text-align: justify;">'.$row->sasaran_program.'</td>';      
        $html.='<td width="10%" style="text-align: justify;">'.$row->keterangan_valid.'</td>'; 
        $html.='</tr>';
    };  
    
    $html .= '</tbody></table>';

    PDF::writeHTML($html, true, false, true, false, '');

    // ---------------------------------------------------------

    // close and output PDF document
    PDF::Output('SinkronisasiSasaran.pdf', 'I');
  }

}
