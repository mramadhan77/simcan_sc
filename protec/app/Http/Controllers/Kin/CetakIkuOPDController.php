<?php

namespace App\Http\Controllers\Kin;

use Request;
use DB;
use Input;
use Response;
use Session;
use PDF;
use Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\TemplateReport As Template;

class CetakIkuOPDController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function IKUSasaranOPD($id_dokumen)
    {
        Template::settingPage('L','F4');
        Template::setHeader('L');        
        // set font

        $pemda = Session::get('xPemda');
        $jum_level_1 = 1;
        $nomor = 1;

        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '</head>';
        $html .= '<body>';
        $data_unit = DB::SELECT('SELECT a.id_rpjmd, a.id_renstra, a.id_unit, a.nomor_renstra, a.tanggal_renstra, a.uraian_renstra, 
            a.nm_pimpinan, a.nip_pimpinan, a.jabatan_pimpinan, a.sumber_data, a.created_at, a.update_at,
            b.tahun_1, b.tahun_2, b.tahun_3, b.tahun_4, b.tahun_5, c.nm_unit
            FROM trx_renstra_dokumen AS a
            INNER JOIN trx_rpjmd_dokumen AS b ON a.id_rpjmd = b.id_rpjmd
	        INNER JOIN ref_unit AS c ON a.id_unit = c.id_unit 
            INNER JOIN kin_trx_iku_opd_dok e ON a.id_renstra = e.id_renstra
            WHERE e.id_dokumen ='.$id_dokumen);

        
        // $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $pemda . '</div>';
        foreach ($data_unit as $units){
            $html .= '<div style="text-align: center; font-size:14px; font-weight: bold;">INDIKATOR KINERJA UTAMA<br>';
            $html .= '<span style="text-align: center; font-size:14px; font-weight: bold;">'.$units->nm_unit.'</span><br>';
            $html .='<span style="text-align: center; font-size:14px; font-weight: bold;"> PERIODE '.$units->tahun_1.' sampai dengan '.$units->tahun_5.'</span></div>';
        };
        
        
        PDF::SetFont('helvetica', '', 10);
        $html .= '<br>';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                    <td width="5%"  style="text-align: center; valign: middle; font-weight: bold;" >NO</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold;" >SASARAN STRATEGIS</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold;" >INDIKATOR KINERJA UTAMA</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold;" >PENJELASAN / FORM PERHITUNGAN</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold;" >SUMBER DATA</td>
                    <td width="15%"  style="text-align: center; valign: middle; font-weight: bold;" >PENANGGUNG JAWAB</td>            
                </tr>            
            </thead>';
        $html .= '<tbody>';
        
        $indsas=DB::SELECT('SELECT a.uraian_sasaran_renstra,b.uraian_indikator_sasaran_renstra,c.metode_penghitungan,
            c.sumber_data_indikator, d.flag_iku, f.nama_eselon,
            (SELECT CASE COALESCE(p.id_sasaran_renstra,0) WHEN 0 THEN 1 ELSE
                COUNT(p.id_indikator_sasaran_renstra) END AS level FROM trx_renstra_sasaran_indikator p 
                WHERE p.id_sasaran_renstra=a.id_sasaran_renstra
                GROUP BY p.id_sasaran_renstra) AS level_1
            FROM trx_renstra_sasaran a
            INNER join trx_renstra_sasaran_indikator b ON a.id_sasaran_renstra=b.id_sasaran_renstra
            INNER join kin_trx_iku_opd_sasaran d ON b.id_indikator_sasaran_renstra=d.id_indikator_sasaran_renstra
            left outer join ref_indikator c ON b.kd_indikator=c.id_indikator
            INNER JOIN kin_trx_iku_opd_dok e ON d.id_dokumen = e.id_dokumen
            INNER JOIN ref_sotk_level_1 f ON e.id_unit = f.id_unit
            WHERE d.flag_iku = 1 AND e.id_dokumen='.$id_dokumen);
            
        foreach ($indsas as $row) {
            $html.='<tr nobr="true">';   
            if ($jum_level_1 <= 1){
                $html.='<td width="5%" rowspan="'.$row->level_1.'" style="text-align: center; font-weight: normal;">'.$nomor.'</td>';
                $html.='<td width="20%" rowspan="'.$row->level_1.'" style="font-weight: normal;" >'.$row->uraian_sasaran_renstra.'</td>';
                $jum_level_1 = $row->level_1; 
                $nomor++; 
            } else {
                $jum_level_1 = $jum_level_1 - 1; 
            };
            $html.='<td width="20%" style="font-weight: normal;" >'.$row->uraian_indikator_sasaran_renstra.'</td>';
            $html.='<td width="20%" style="font-weight: normal;" >'.$row->metode_penghitungan.'</td>';
            $html.='<td width="20%" style="font-weight: normal;" >'.$row->sumber_data_indikator.'</td>';
            $html.='<td width="15%" style="font-weight: normal;" >'.$row->nama_eselon.'</td>';
            $html.='</tr>';
            
        };
        $html .= '</tbody>';
        $html .= '</table>';        
        $html .= '</body>';
        
        Template::setFooter();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('IKUSasaranOPD.pdf', 'I');
    }

}
