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


class CetakMatrikRpjmdAllController extends Controller
{

  public function printMatrikRpjmd($id_rpjmd)
  {
    
    Template::settingPageLandscape();
    Template::headerLandscape();
    // PDF::SetFooterMargin(50);
    
    // set font
    PDF::SetFont('helvetica', '', 6);

    $data_unit = DB::SELECT('SELECT a.id_rpjmd, a.id_rpjmd_old, a.thn_dasar, a.tahun_1, a.tahun_2, a.tahun_3, a.tahun_4, a.tahun_5, a.no_perda, a.tgl_perda, 
        a.id_revisi, a.id_status_dokumen, a.sumber_data, a.created_at, a.updated_at
        FROM trx_rpjmd_dokumen AS a WHERE a.id_rpjmd ='.$id_rpjmd);

    $data = DB::SELECT('SELECT a.id_rpjmd, a.id_visi_rpjmd, d.id_sasaran_rpjmd,e.id_program_rpjmd,c.id_tujuan_rpjmd,
        (SELECT COUNT(p.id_program_rpjmd) FROM trx_rpjmd_program p
        INNER JOIN trx_rpjmd_sasaran q ON p.id_sasaran_rpjmd = q.id_sasaran_rpjmd
        INNER JOIN trx_rpjmd_tujuan r ON q.id_tujuan_rpjmd = r.id_tujuan_rpjmd
        INNER JOIN trx_rpjmd_misi s ON r.id_misi_rpjmd = s.id_misi_rpjmd 
        WHERE s.id_visi_rpjmd = a.id_visi_rpjmd) AS level_1,
        (SELECT COUNT(p.id_program_rpjmd) FROM trx_rpjmd_program p
        INNER JOIN trx_rpjmd_sasaran q ON p.id_sasaran_rpjmd = q.id_sasaran_rpjmd
        INNER JOIN trx_rpjmd_tujuan r ON q.id_tujuan_rpjmd = r.id_tujuan_rpjmd
        INNER JOIN trx_rpjmd_misi s ON r.id_misi_rpjmd = s.id_misi_rpjmd 
        WHERE s.id_misi_rpjmd = b.id_misi_rpjmd) AS level_2,
        (SELECT COUNT(p.id_program_rpjmd) FROM trx_rpjmd_program p
        INNER JOIN trx_rpjmd_sasaran q ON p.id_sasaran_rpjmd = q.id_sasaran_rpjmd
        INNER JOIN trx_rpjmd_tujuan r ON q.id_tujuan_rpjmd = r.id_tujuan_rpjmd
        WHERE r.id_tujuan_rpjmd = c.id_tujuan_rpjmd) AS level_3,
        (SELECT COUNT(p.id_program_rpjmd) FROM trx_rpjmd_program p
        INNER JOIN trx_rpjmd_sasaran q ON p.id_sasaran_rpjmd = q.id_sasaran_rpjmd
        WHERE q.id_sasaran_rpjmd = d.id_sasaran_rpjmd) AS level_4,
        a.uraian_visi_rpjmd, b.uraian_misi_rpjmd, c.uraian_tujuan_rpjmd, d.uraian_sasaran_rpjmd, e.uraian_program_rpjmd, e.total_pagu
        FROM trx_rpjmd_visi AS a
        INNER JOIN trx_rpjmd_misi as b ON a.id_visi_rpjmd = b.id_visi_rpjmd
        INNER JOIN trx_rpjmd_tujuan as c ON b.id_misi_rpjmd = c.id_misi_rpjmd
        INNER JOIN trx_rpjmd_sasaran as d ON c.id_tujuan_rpjmd = d.id_tujuan_rpjmd
        INNER JOIN trx_rpjmd_program as e ON d.id_sasaran_rpjmd = e.id_sasaran_rpjmd
        WHERE a.id_rpjmd ='.$id_rpjmd);

    $jum_level_1 = 1;
    $jum_level_2 = 1;
    $jum_level_3 = 1;
    $jum_level_4 = 1;
    $html ='';
    $html .=  '<html>';
    $html .= '<head>';
    $html .=  '<style>
                    td, th {                        
                    }
                </style>';
    $html .= '</head>';
    $html .= '<body>';
    $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Matrik Rencana Pembangunan Jangka Menengah (RPJMD)</div>';
    foreach ($data_unit as $units){
        $html .='<div style="text-align: center; font-size:14px; font-weight: bold;"> PERDA Nomor : '.$units->no_perda. '</div>';
        $html .='<div style="text-align: center; font-size:10px; font-weight: bold; font-style: italic"> Periode '.$units->tahun_1.' sampai dengan '.$units->tahun_5.'</div>';
    };
    $html .='<br>';
    $html .='<table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th style="text-align: center; vertical-align:middle">Visi</th>
                    <th style="text-align: center; vertical-align:middle">Misi</th>
                    <th style="text-align: center; vertical-align:middle">Tujuan</th>
                    <th style="text-align: center; vertical-align:middle">Sasaran</th>
                    <th style="text-align: center; vertical-align:middle">Program</th>
                </tr>
                </thead>
            <tbody >';
    
    foreach ($data as $row){
        $html.='<tr nobr="true">';
        if ($jum_level_1 <= 1){
            $html.='<td rowspan="'.$row->level_1.'" style="padding: 50px; text-align: justify;">'.$row->uraian_visi_rpjmd.'</td>';
            $jum_level_1 = $row->level_1;  
        } else {
            $jum_level_1 = $jum_level_1 - 1; 
        }; 
        if ($jum_level_2 <= 1){
            $html.='<td rowspan="'.$row->level_2.'" style="padding: 50px; text-align: justify;">'.$row->uraian_misi_rpjmd.'</td>';
            $jum_level_2 = $row->level_2;  
        } else {
            $jum_level_2 = $jum_level_2 - 1; 
        }; 
        if ($jum_level_3 <= 1){
            $html.='<td rowspan="'.$row->level_3.'" style="padding: 50px; text-align: justify;"><div>'.$row->uraian_tujuan_rpjmd.'</div>';
            $html.='<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
            $tujuan = DB::SELECT('SELECT (@id:=@id+1) AS urut, a.thn_id, a.no_urut, a.id_tujuan_rpjmd, a.id_indikator_tujuan_rpjmd, a.id_perubahan, a.kd_indikator, 
                a.uraian_indikator_sasaran_rpjmd, a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2, 
                a.angka_tahun3, a.angka_tahun4, a.angka_tahun5, a.angka_akhir_periode, a.sumber_data, a.created_at, a.updated_at, COALESCE(b.nm_indikator,"Kosong") AS nm_indikator
                FROM trx_rpjmd_tujuan_indikator AS a                
                INNER JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator,(SELECT @id:=0) x
                WHERE a.id_tujuan_rpjmd='.$row->id_tujuan_rpjmd);            
            $html.='<table border="0" cellpadding="0" cellspacing="0">';
            foreach ($tujuan as $tujuans){
                $html.='<tr><td width="10%" style="text-align: center;"> '.$tujuans->urut.' </td>';    
                $html.='<td width="90%" style="text-align: justify;">'.$tujuans->nm_indikator.'</td></tr>';
            }
            $html.='</table>';
            $html.='</td>';
            $jum_level_3 = $row->level_3;  
        } else {
            $jum_level_3 = $jum_level_3 - 1; 
        }; 
        if ($jum_level_4 <= 1){
            $html.='<td rowspan="'.$row->level_4.'" style="padding: 50px; text-align: justify;"><div>'.$row->uraian_sasaran_rpjmd.'</div>';
            $html.='<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
            $sasaran = DB::SELECT('SELECT (@id:=@id+1) AS urut, a.thn_id, a.no_urut, a.id_sasaran_rpjmd, a.id_indikator_sasaran_rpjmd, a.id_perubahan, a.kd_indikator, 
                a.uraian_indikator_sasaran_rpjmd, a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2, 
                a.angka_tahun3, a.angka_tahun4, a.angka_tahun5, a.angka_akhir_periode, a.sumber_data, a.created_at, a.updated_at, COALESCE(b.nm_indikator,"Kosong") AS nm_indikator
                FROM trx_rpjmd_sasaran_indikator AS a                
                INNER JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator,(SELECT @id:=0) x
                WHERE a.id_sasaran_rpjmd='.$row->id_sasaran_rpjmd);            
            $html.='<table border="0" cellpadding="0" cellspacing="0">';
            foreach ($sasaran as $sasarans){
                $html.='<tr><td width="10%" style="text-align: center;"> '.$sasarans->urut.' </td>';    
                $html.='<td width="90%" style="text-align: justify;">'.$sasarans->nm_indikator.'</td></tr>';
            }
            $html.='</table>';
            $html.='</td>';
            
            $jum_level_4 = $row->level_4;  
        } else {
            $jum_level_4 = $jum_level_4 - 1; 
        };       
        $html.='<td style="padding: 50px; text-align: justify;" ><div>'.$row->uraian_program_rpjmd.'</div>
                <div><span style="font-weight: bold; font-style: italic">Pagu : Rp'.number_format($row->total_pagu,0,",",".").'</span></div>';
        $html.='<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
        $program = DB::SELECT('SELECT (@id:=@id+1) AS urut, a.thn_id, a.no_urut, a.id_program_rpjmd, a.id_indikator_program_rpjmd, a.id_perubahan, a.id_indikator, 
            a.uraian_indikator_program_rpjmd, a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2, 
            a.angka_tahun3, a.angka_tahun4, a.angka_tahun5, a.angka_akhir_periode, a.sumber_data, a.created_at, a.updated_at, COALESCE(b.nm_indikator,"Kosong") AS nm_indikator
            FROM trx_rpjmd_program_indikator AS a                
            INNER JOIN ref_indikator AS b ON a.id_indikator = b.id_indikator,(SELECT @id:=0) x
            WHERE a.id_program_rpjmd='.$row->id_program_rpjmd);
        $html.='<table border="0" cellpadding="0" cellspacing="0">';
            foreach ($program as $programs){
                $html.='<tr><td width="10%" style="text-align: center;"> '.$programs->urut.' </td>';    
                $html.='<td width="90%" style="text-align: justify;">'.$programs->nm_indikator.'</td></tr>';
            }
        $html.='</table>';
        $html.='</td>';
        $html.='</tr>';
    };  
    
    $html .= '</tbody></table>';

    PDF::writeHTML($html, true, false, true, false, '');
    Template::footerLandscape();
    // ---------------------------------------------------------

    // close and output PDF document
    PDF::Output('MatrikRpjmd.pdf', 'I');
  }

}
