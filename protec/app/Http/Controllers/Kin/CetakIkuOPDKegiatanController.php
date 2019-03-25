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


class CetakIkuOPDKegiatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function IKUKegiatanOPD($id_dokumen,$id_esl3)
    {
        Template::settingPage('L','F4');
        Template::setHeader('L');

        $pemda = Session::get('xPemda');
        $jum_level_1 = 1;
        $nomor = 1;

        if($id_esl3 > 0) {
            $query = ' AND d.id_esl4='.$id_esl3;
        } else {
            $query = '';
        }


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
            $html .= '<div style="text-align: center; font-size:14px; font-weight: bold;">INDIKATOR KINERJA UTAMA KEGIATAN PERANGKAT DAERAH<br>';
            $html .= '<span style="text-align: center; font-size:14px; font-weight: bold;">'.$units->nm_unit.'</span><br>';
            $html .= '<span style="text-align: center; font-size:14px; font-weight: bold;"> PERIODE '.$units->tahun_1.' sampai dengan '.$units->tahun_5.'</span></div>';
        };
        
        
        PDF::SetFont('helvetica', '', 10);
        $html .= '<br>';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                    <td width="5%"  style="text-align: center; valign: middle; font-weight: bold;" >NO</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold;" >KEGIATAN PERANGKAT DAERAH</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold;" >INDIKATOR KINERJA UTAMA</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold;" >PENJELASAN / FORM PERHITUNGAN</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold;" >SUMBER DATA</td>
                    <td width="15%"  style="text-align: center; valign: middle; font-weight: bold;" >PENANGGUNG JAWAB</td>            
                </tr>            
            </thead>';
        $html .= '<tbody>';
        
        $indsas=DB::SELECT('SELECT j.nm_kegiatan,b.uraian_indikator_kegiatan_renstra,c.metode_penghitungan,c.sumber_data_indikator, d.flag_iku, d.id_esl4, i.nama_eselon,
            (SELECT CASE COALESCE(p.id_kegiatan_renstra,0) WHEN 0 THEN 1 ELSE
                            COUNT(p.id_indikator_kegiatan_renstra) END AS level FROM trx_renstra_kegiatan_indikator p 
                            WHERE p.id_kegiatan_renstra=a.id_kegiatan_renstra
                            GROUP BY p.id_kegiatan_renstra) AS level_1 
            FROM trx_renstra_kegiatan a
            INNER JOIN trx_renstra_kegiatan_indikator b ON a.id_kegiatan_renstra=b.id_kegiatan_renstra
            INNER JOIN kin_trx_iku_opd_kegiatan d on b.id_indikator_kegiatan_renstra=d.id_indikator_kegiatan_renstra
            INNER JOIN kin_trx_iku_opd_program e ON d.id_iku_opd_program=e.id_iku_opd_program
            INNER JOIN kin_trx_iku_opd_sasaran g ON e.id_iku_opd_sasaran=g.id_iku_opd_sasaran
            INNER JOIN kin_trx_iku_opd_dok h ON g.id_dokumen = h.id_dokumen
            LEFT OUTER JOIN ref_indikator c ON b.kd_indikator=c.id_indikator
            LEFT OUTER JOIN ref_sotk_level_3 i ON d.id_esl4=i.id_sotk_es4
            INNER JOIN ref_kegiatan j ON a.id_kegiatan_ref=j.id_kegiatan
            WHERE d.flag_iku = 1 AND h.id_dokumen='.$id_dokumen.' '.$query);
            
        foreach ($indsas as $row) {
            $html.='<tr nobr="true">'; 
            $html.='<td width="5%" style="text-align: center; font-weight: normal;">'.$nomor.'</td>';   
            if ($jum_level_1 <= 1){
                $html.='<td width="20%" rowspan="'.$row->level_1.'" style="font-weight: normal;" >'.$row->nm_kegiatan.'</td>';
                $jum_level_1 = $row->level_1; 
                $nomor++; 
            } else {
                $jum_level_1 = $jum_level_1 - 1; 
            };
            $html.='<td width="20%" style="font-weight: normal;" >'.$row->uraian_indikator_kegiatan_renstra.'</td>';
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
        PDF::Output('IKUKegiatanOPD.pdf', 'I');
    }

}
