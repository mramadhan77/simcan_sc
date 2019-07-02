<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Laporan\TemplateReport AS Template;
use App\Fungsi as Fungsi;
use CekAkses;
use Validator;
use Response;
use Session;
use PDF;
use Auth;

class CetakMatrikRenstraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

  public function printRenstra($unit, $id_renstra)
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

    $data = DB::SELECT('SELECT a.no_urut, a.id_visi_renstra, (SELECT COUNT(g.id_kegiatan_renstra) FROM trx_renstra_kegiatan g
            INNER JOIN trx_renstra_program f ON g.id_program_renstra = f.id_program_renstra
            INNER JOIN trx_renstra_sasaran e ON f.id_sasaran_renstra = e.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan b ON e.id_tujuan_renstra = b.id_tujuan_renstra
            INNER JOIN trx_renstra_misi c ON b.id_misi_renstra = c.id_misi_renstra 
            WHERE c.id_visi_renstra = a.id_visi_renstra) AS level_1,
            (SELECT COUNT(g.id_kegiatan_renstra) FROM trx_renstra_kegiatan g
            INNER JOIN trx_renstra_program f ON g.id_program_renstra = f.id_program_renstra
            INNER JOIN trx_renstra_sasaran e ON f.id_sasaran_renstra = e.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan b ON e.id_tujuan_renstra = b.id_tujuan_renstra
            INNER JOIN trx_renstra_misi c ON b.id_misi_renstra = c.id_misi_renstra 
            WHERE c.id_misi_renstra = x.id_misi_renstra) AS level_2,
            (SELECT COUNT(g.id_kegiatan_renstra) FROM trx_renstra_kegiatan g
            INNER JOIN trx_renstra_program f ON g.id_program_renstra = f.id_program_renstra
            INNER JOIN trx_renstra_sasaran e ON f.id_sasaran_renstra = e.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan b ON e.id_tujuan_renstra = b.id_tujuan_renstra
            INNER JOIN trx_renstra_misi c ON b.id_misi_renstra = c.id_misi_renstra 
            WHERE b.id_tujuan_renstra = y.id_tujuan_renstra) AS level_3,
            (SELECT COUNT(g.id_kegiatan_renstra) FROM trx_renstra_kegiatan g
            INNER JOIN trx_renstra_program f ON g.id_program_renstra = f.id_program_renstra
            INNER JOIN trx_renstra_sasaran e ON f.id_sasaran_renstra = e.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan b ON e.id_tujuan_renstra = b.id_tujuan_renstra
            INNER JOIN trx_renstra_misi c ON b.id_misi_renstra = c.id_misi_renstra 
            WHERE e.id_sasaran_renstra = z.id_sasaran_renstra) AS level_4,
            (SELECT COUNT(g.id_kegiatan_renstra) FROM trx_renstra_kegiatan g
            INNER JOIN trx_renstra_program f ON g.id_program_renstra = f.id_program_renstra
            INNER JOIN trx_renstra_sasaran e ON f.id_sasaran_renstra = e.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan b ON e.id_tujuan_renstra = b.id_tujuan_renstra
            INNER JOIN trx_renstra_misi c ON b.id_misi_renstra = c.id_misi_renstra 
            WHERE f.id_program_renstra = r.id_program_renstra) AS level_5,
            a.uraian_visi_renstra, x.uraian_misi_renstra, y.uraian_tujuan_renstra, z.uraian_sasaran_renstra, 
            r.uraian_program_renstra, s.uraian_kegiatan_renstra
            FROM trx_renstra_visi as a
            INNER JOIN trx_renstra_misi as x ON a.id_visi_renstra = x.id_visi_renstra
            INNER JOIN trx_renstra_tujuan as y ON x.id_misi_renstra = y.id_misi_renstra
            INNER JOIN trx_renstra_sasaran as z ON y.id_tujuan_renstra = z.id_tujuan_renstra
            INNER JOIN trx_renstra_program as r ON z.id_sasaran_renstra = r.id_sasaran_renstra
            INNER JOIN trx_renstra_kegiatan as s ON r.id_program_renstra = s.id_program_renstra
            WHERE a.id_unit ='.$unit.' AND a.id_renstra='.$id_renstra.' AND x.no_urut <> 99 AND x.no_urut <> 98 AND x.no_urut <> 97');

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
    $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Matrik Rencana Strategis</div>';
    foreach ($data_unit as $units){
        $html .='<div style="text-align: center; font-size:14px; font-weight: bold;">'.$units->kd_unit.' - '.$units->nm_unit.'</div>';
        $html .='<div style="text-align: center; font-size:10px; font-weight: bold; font-style: italic"> Dokumen Renstra Nomor : '.$units->nomor_renstra.'</div>';
    };
    $html .='<br>';
    $html .='<table border="0" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th style="text-align: center; vertical-align:middle">Visi</th>
                    <th style="text-align: center; vertical-align:middle">Misi</th>
                    <th style="text-align: center; vertical-align:middle">Tujuan</th>
                    <th style="text-align: center; vertical-align:middle">Sasaran</th>
                    <th style="text-align: center; vertical-align:middle">Program</th>
                    <th style="text-align: center; vertical-align:middle">Kegiatan</th>
                </tr>
                </thead>
            <tbody >';
    
    foreach ($data as $row){
        $html.='<tr nobr="true">';
        if ($jum_level_1 <= 1){
            $html.='<td rowspan="'.$row->level_1.'" style="text-align: justify;">'.$row->uraian_visi_renstra.'</td>';
            $jum_level_1 = $row->level_1;  
        } else {
            $jum_level_1 = $jum_level_1 - 1; 
        }; 
        if ($jum_level_2 <= 1){
            $html.='<td rowspan="'.$row->level_2.'" style="text-align: justify;">'.$row->uraian_misi_renstra.'</td>';
            $jum_level_2 = $row->level_2;  
        } else {
            $jum_level_2 = $jum_level_2 - 1; 
        }; 
        if ($jum_level_3 <= 1){
            $html.='<td rowspan="'.$row->level_3.'" style="text-align: justify;">'.$row->uraian_tujuan_renstra.'</td>';
            $jum_level_3 = $row->level_3;  
        } else {
            $jum_level_3 = $jum_level_3 - 1; 
        }; 
        if ($jum_level_4 <= 1){
            $html.='<td rowspan="'.$row->level_4.'" style="text-align: justify;">'.$row->uraian_sasaran_renstra.'</td>';
            $jum_level_4 = $row->level_4;  
        } else {
            $jum_level_4 = $jum_level_4 - 1; 
        }; 
        if ($jum_level_5 <= 1){
            $html.='<td rowspan="'.$row->level_5.'" style="text-align: justify;">'.$row->uraian_program_renstra.'</td>';
            $jum_level_5 = $row->level_5;  
        } else {
            $jum_level_5 = $jum_level_5 - 1; 
        };        
        $html.='<td style="text-align: justify;">'.$row->uraian_kegiatan_renstra.'</td>';
        $html.='</tr>';
    };  
    
    $html .= '</tbody></table>';

    PDF::writeHTML($html, true, false, true, false, '');
    Template::footerLandscape();
    // ---------------------------------------------------------

    // close and output PDF document
    PDF::Output('MatrikRenstraOPD.pdf', 'I');
  }

}
