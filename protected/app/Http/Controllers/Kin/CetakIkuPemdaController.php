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


class CetakIkuPemdaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function IKUSasaranPemda($id_rpjmd)
    {
        Template::settingPage('L','F4');
        Template::setHeader('L');

        $pemda = Session::get('xPemda');
        $jum_level_1 = 1;
        $nomor = 1;

        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        // $html .= '<style>'.file_get_contents('css/bootstrap.min.css').'</style>';
        $html .= '</head>';
        $html .= '<body>';
        $data_unit = DB::SELECT('SELECT a.id_rpjmd, a.id_rpjmd_old, a.thn_dasar, a.tahun_1, a.tahun_2, a.tahun_3, a.tahun_4, a.tahun_5, a.no_perda, a.tgl_perda, 
            a.id_revisi, a.id_status_dokumen, a.sumber_data, a.created_at, a.updated_at
            FROM trx_rpjmd_dokumen AS a WHERE a.id_rpjmd ='.$id_rpjmd);

        foreach ($data_unit as $units){
            $html .= '<div style="text-align: center; font-size:14px; font-weight: bold;">INDIKATOR KINERJA UTAMA<br>';
            $html .= 'PERIODE '.$units->tahun_1.' sampai dengan '.$units->tahun_5.'</div>';
        };
        
        
        PDF::SetFont('times', '', 10);
        $html .= '<br>';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr >
                    <td width="5%"  style="text-align: center; valign: middle; font-weight: bold; " >NO</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold; " >SASARAN STRATEGIS</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold; " >INDIKATOR KINERJA UTAMA</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold; " >PENJELASAN / FORM PERHITUNGAN</td>
                    <td width="20%"  style="text-align: center; valign: middle; font-weight: bold; " >SUMBER DATA</td>
                    <td width="15%"  style="text-align: center; valign: middle; font-weight: bold; " >PENANGGUNG JAWAB</td>            
                </tr>            
            </thead>';
        $html .= '<tbody>';
        
        $indsas=DB::SELECT('SELECT a.uraian_sasaran_rpjmd,b.uraian_indikator_sasaran_rpjmd,c.metode_penghitungan,c.sumber_data_indikator, 
            d.unit_penanggung_jawab, COALESCE(e.nm_unit,"Semua OPD") AS nm_unit,
            (SELECT CASE COALESCE(p.id_sasaran_rpjmd,0) WHEN 0 THEN 1 ELSE
                COUNT(p.id_indikator_sasaran_rpjmd) END AS level FROM trx_rpjmd_sasaran_indikator p 
                INNER JOIN kin_trx_iku_pemda_rinci q ON p.id_indikator_sasaran_rpjmd=q.id_indikator_sasaran_rpjmd
                WHERE p.id_sasaran_rpjmd=a.id_sasaran_rpjmd AND q.flag_iku = 1
                GROUP BY p.id_sasaran_rpjmd) AS level_1
            FROM trx_rpjmd_sasaran a
            LEFT OUTER JOIN trx_rpjmd_sasaran_indikator b ON a.id_sasaran_rpjmd=b.id_sasaran_rpjmd
            LEFT OUTER JOIN kin_trx_iku_pemda_rinci d ON b.id_indikator_sasaran_rpjmd=d.id_indikator_sasaran_rpjmd
            LEFT OUTER JOIN ref_unit e ON d.unit_penanggung_jawab = e.id_unit
            LEFT OUTER JOIN ref_indikator c ON b.kd_indikator=c.id_indikator
            WHERE d.flag_iku = 1');
            
        foreach ($indsas as $row) {
            $html.='<tr nobr="true">';   
            if ($jum_level_1 <= 1){
                $html.='<td width="5%" rowspan="'.$row->level_1.'" style="text-align: center; font-weight: normal;">'.$nomor.'</td>';
                $html.='<td width="20%" rowspan="'.$row->level_1.'" style="font-weight: normal;" >'.$row->uraian_sasaran_rpjmd.'</td>';
                $jum_level_1 = $row->level_1; 
                $nomor++; 
            } else {
                $jum_level_1 = $jum_level_1 - 1; 
            };
            $html.='<td width="20%" style="font-weight: normal;" >'.$row->uraian_indikator_sasaran_rpjmd.'</td>';
            $html.='<td width="20%" style="font-weight: normal;" >'.$row->metode_penghitungan.'</td>';
            $html.='<td width="20%" style="font-weight: normal;" >'.$row->sumber_data_indikator.'</td>';
            $html.='<td width="15%" style="font-weight: normal;" >'.$row->nm_unit.'</td>';
            $html.='</tr>';
            
        };
        $html .= '</tbody>';
        $html .= '</table>';  

        $html .= '</body>';
        
        Template::setFooter(); 
        PDF::writeHTML($html, true, false, true, false, '');
        
        PDF::Output('IKUSasaranPemda.pdf', 'I');
    }

}
