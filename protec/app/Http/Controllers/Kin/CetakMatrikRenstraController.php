<?php

namespace App\Http\Controllers\Kin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\TemplateReport As Template;
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
    
    Template::settingPage('L','F4');
    Template::setHeader('L');
    
    // set font
    PDF::SetFont('helvetica', '', 6);

    $data_unit = DB::SELECT('SELECT a.id_rpjmd, a.id_renstra, a.id_unit, a.nomor_renstra, a.tanggal_renstra, a.uraian_renstra, a.nm_pimpinan, a.nip_pimpinan, 
            a.jabatan_pimpinan, a.sumber_data, a.created_at, a.update_at, b.nm_unit, CONCAT(c.kd_urusan,".",c.kd_bidang,".",b.kd_unit) AS kd_unit
            FROM trx_renstra_dokumen AS a
            INNER JOIN ref_unit AS b ON a.id_unit = b.id_unit
            INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang
            WHERE a.id_unit='.$unit.' AND a.id_renstra='.$id_renstra.' LIMIT 1');

    $data = DB::SELECT('SELECT a.no_urut, a.id_visi_renstra, y.id_tujuan_renstra, z.id_sasaran_renstra,
            (SELECT COUNT(f.id_program_renstra) FROM trx_renstra_program f 
            INNER JOIN trx_renstra_sasaran e ON f.id_sasaran_renstra = e.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan b ON e.id_tujuan_renstra = b.id_tujuan_renstra
            INNER JOIN trx_renstra_misi c ON b.id_misi_renstra = c.id_misi_renstra 
            WHERE b.id_tujuan_renstra = y.id_tujuan_renstra) AS level_1,
            (SELECT COUNT(f.id_program_renstra) FROM trx_renstra_program f 
            INNER JOIN trx_renstra_sasaran e ON f.id_sasaran_renstra = e.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan b ON e.id_tujuan_renstra = b.id_tujuan_renstra
            INNER JOIN trx_renstra_misi c ON b.id_misi_renstra = c.id_misi_renstra 
            WHERE e.id_sasaran_renstra = z.id_sasaran_renstra) AS level_2,
            a.uraian_visi_renstra, x.uraian_misi_renstra, y.uraian_tujuan_renstra, z.uraian_sasaran_renstra, 
            r.uraian_program_renstra
            FROM trx_renstra_visi as a
            INNER JOIN trx_renstra_misi as x ON a.id_visi_renstra = x.id_visi_renstra
            INNER JOIN trx_renstra_tujuan as y ON x.id_misi_renstra = y.id_misi_renstra
            INNER JOIN trx_renstra_sasaran as z ON y.id_tujuan_renstra = z.id_tujuan_renstra
            INNER JOIN trx_renstra_program as r ON z.id_sasaran_renstra = r.id_sasaran_renstra
            WHERE a.id_unit ='.$unit.' AND a.id_renstra='.$id_renstra.' AND x.no_urut <> 99 AND x.no_urut <> 98 AND x.no_urut <> 97');

    $jum_level_1 = 1;
    $jum_level_2 = 1;
    $jum_level_3 = 1;
    $jum_level_4 = 1;
    $jum_level_5 = 1;
    $jum_level_6 = 1;
    $jum_level_7 = 1;
    $jum_level_8 = 1;
    $jum_level_9 = 1;
    $html ='';
    $html .=  '<html><head>';
    $html .= '</head><body>';
    foreach ($data_unit as $units){
        $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Matrik Rencana Strategis<br>';
        $html .='<span style="text-align: center; font-size:14px; font-weight: bold;">'.$units->kd_unit.' - '.$units->nm_unit.'</span><br>';
        $html .='<span style="text-align: center; font-size:10px; font-weight: bold; font-style: italic"> Dokumen Renstra Nomor : '.$units->nomor_renstra.'</span></div>';
    };
    $html .='<br>';
    $html .='<table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th colspan="2" style="text-align: center; vertical-align:middle">Tujuan</th>
                    <th colspan="2" style="text-align: center; vertical-align:middle">Sasaran</th>
                    <th colspan="3" style="text-align: center; vertical-align:middle">Cara Mencapai Tujuan dan Sasaran</th>
                </tr>
                <tr>
                    <th style="text-align: center; vertical-align:middle">Tujuan</th>
                    <th style="text-align: center; vertical-align:middle">Indikator dan Target</th>
                    <th style="text-align: center; vertical-align:middle">Sasaran</th>
                    <th style="text-align: center; vertical-align:middle">Indikator dan Target</th>
                    <th style="text-align: center; vertical-align:middle">Strategi</th>
                    <th style="text-align: center; vertical-align:middle">Kebijakan</th>
                    <th style="text-align: center; vertical-align:middle">Program</th>
                </tr>
                </thead>
            <tbody >';
    
    foreach ($data as $row){
        
        $tujuan = DB::SELECT('SELECT (@id:=@id+1) AS urut, a.thn_id, a.no_urut, a.id_tujuan_renstra, a.id_indikator_tujuan_renstra, a.id_perubahan, a.kd_indikator,
            a.uraian_indikator_sasaran_renstra, a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2,
            a.angka_tahun3, a.angka_tahun4, a.angka_tahun5, a.angka_akhir_periode, a.sumber_data, a.created_at, a.updated_at, COALESCE(b.nm_indikator,"Kosong") AS nm_indikator
            FROM trx_renstra_tujuan_indikator AS a
            INNER JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator,(SELECT @id:=0) x
            WHERE a.id_tujuan_renstra='.$row->id_tujuan_renstra);   
        
        $sasaran = DB::SELECT('SELECT (@id:=@id+1) AS urut, a.thn_id, a.no_urut, a.id_sasaran_renstra, a.id_indikator_sasaran_renstra, a.id_perubahan, a.kd_indikator,
            a.uraian_indikator_sasaran_renstra, a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2,
            a.angka_tahun3, a.angka_tahun4, a.angka_tahun5, a.angka_akhir_periode, a.sumber_data, a.created_at, a.updated_at, COALESCE(b.nm_indikator,"Kosong") AS nm_indikator
            FROM trx_renstra_sasaran_indikator AS a
            INNER JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator,(SELECT @id:=0) x
            WHERE a.id_sasaran_renstra='.$row->id_sasaran_renstra); 

        $strategi = DB::SELECT('SELECT (@id:=@id+1) AS urut, thn_id, no_urut, id_sasaran_renstra, id_strategi_renstra, id_perubahan, uraian_strategi_renstra, sumber_data, created_at, updated_at
            FROM trx_renstra_strategi,(SELECT @id:=0) x
            WHERE id_sasaran_renstra='.$row->id_sasaran_renstra); 

        $kebijakan = DB::SELECT('SELECT (@id:=@id+1) AS urut, thn_id, no_urut, id_sasaran_renstra, id_kebijakan_renstra, id_perubahan, uraian_kebijakan_renstra, sumber_data, created_at, update_at
            FROM trx_renstra_kebijakan,(SELECT @id:=0) x
            WHERE id_sasaran_renstra='.$row->id_sasaran_renstra); 

        $html.='<tr nobr="true">';
        if ($jum_level_1 <= 1){
            $html.='<td rowspan="'.$row->level_1.'" style="text-align: justify;">'.$row->uraian_tujuan_renstra.'</td>';
            $jum_level_1 = $row->level_1;  
        } else {
            $jum_level_1 = $jum_level_1 - 1; 
        }; 
        if ($jum_level_2 <= 1){
            $html.='<td rowspan="'.$row->level_1.'" style="text-align: justify;">';        
            $html.='<table border="0">';
            foreach ($tujuan as $tujuans){
                $html.='<tr><td  width="10%" style="text-align: center; vertical-align:bottom"> '.$tujuans->urut.' </td>';    
                $html.='<td  width="90%" style="text-align: left; vertical-align:bottom">'.$tujuans->nm_indikator.'</td></tr>';
                // $html.='<tr><td width="50%" > Tahun 1 : '.$tujuans->angka_tahun1.'</td></tr>'; 
                // $html.='<tr><td width="50%" > Tahun 2 : '.$tujuans->angka_tahun2.'</td></tr>'; 
                // $html.='<tr><td width="50%" > Tahun 3 : '.$tujuans->angka_tahun3.'</td></tr>'; 
                // $html.='<tr><td width="50%" > Tahun 4 : '.$tujuans->angka_tahun4.'</td></tr>'; 
                // $html.='<tr><td width="50%" > Tahun 5 : '.$tujuans->angka_tahun5.'</td></tr>';
            };
            $html.='</table>';
            $html.='</td>';
            $jum_level_2 = $row->level_1;  
        } else {
            $jum_level_2 = $jum_level_2 - 1; 
        }; 
        if ($jum_level_3 <= 1){
            $html.='<td rowspan="'.$row->level_2.'" style="text-align: justify;">'.$row->uraian_sasaran_renstra.'</td>';
            $jum_level_3 = $row->level_2;  
        } else {
            $jum_level_3 = $jum_level_3 - 1; 
        };  
        if ($jum_level_4 <= 1){
            $html.='<td rowspan="'.$row->level_2.'" style="text-align: justify;">';
            $html.='<table border="0">';
            foreach ($sasaran as $sasarans){
                $html.='<tr><td  width="10%" style="text-align: center; vertical-align:bottom"> '.$sasarans->urut.' </td>';    
                $html.='<td  width="90%" style="text-align: left; vertical-align:bottom">'.$sasarans->nm_indikator.'</td></tr>';
                // $html.='<tr><td width="50%" > Tahun 1 : '.$tujuans->angka_tahun1.'</td></tr>'; 
                // $html.='<tr><td width="50%" > Tahun 2 : '.$tujuans->angka_tahun2.'</td></tr>'; 
                // $html.='<tr><td width="50%" > Tahun 3 : '.$tujuans->angka_tahun3.'</td></tr>'; 
                // $html.='<tr><td width="50%" > Tahun 4 : '.$tujuans->angka_tahun4.'</td></tr>'; 
                // $html.='<tr><td width="50%" > Tahun 5 : '.$tujuans->angka_tahun5.'</td></tr>';
            };
            $html.='</table>';
            $html.='</td>';
            $jum_level_4 = $row->level_2;  
        } else {
            $jum_level_4 = $jum_level_4 - 1; 
        };  
        if ($jum_level_5 <= 1){
            $html.='<td rowspan="'.$row->level_2.'" style="text-align: justify;">';
            $html.='<table border="0">';
            foreach ($strategi as $strategis){
                $html.='<tr><td  width="10%" style="text-align: center; vertical-align:bottom"> '.$strategis->urut.' </td>';    
                $html.='<td  width="90%" style="text-align: left; vertical-align:bottom">'.$strategis->uraian_strategi_renstra.'</td></tr>';
            };
            $html.='</table>';
            $html.='</td>';
            $jum_level_5 = $row->level_2;  
        } else {
            $jum_level_5 = $jum_level_5 - 1; 
        };  
        if ($jum_level_6 <= 1){
            $html.='<td rowspan="'.$row->level_2.'" style="text-align: justify;">';
            $html.='<table border="0">';
            foreach ($kebijakan as $kebijakans){
                $html.='<tr><td  width="10%" style="text-align: center; vertical-align:bottom"> '.$kebijakans->urut.' </td>';    
                $html.='<td  width="90%" style="text-align: left; vertical-align:bottom">'.$kebijakans->uraian_kebijakan_renstra.'</td></tr>';
            };
            $html.='</table>';
            $html.='</td>';
            $jum_level_6 = $row->level_2;  
        } else {
            $jum_level_6 = $jum_level_6 - 1; 
        };
        // if ($jum_level_7 <= 1){
        //     $html.='<td rowspan="'.$row->level_3.'" style="text-align: justify;">'.$row->uraian_program_renstra.'</td>';
        //     $jum_level_7 = $row->level_3;  
        // } else {
        //     $jum_level_7 = $jum_level_7 - 1; 
        // };      
        $html.='<td style="text-align: justify;">'.$row->uraian_program_renstra.'</td>';
        $html.='</tr>';
    };  
    
    $html .= '</tbody></table>';

    Template::setFooter();
    PDF::writeHTML($html, true, false, true, false, '');
    // ---------------------------------------------------------

    // close and output PDF document
    PDF::Output('MatrikRenstraOPD.pdf', 'I');
  }

}
