<?php
namespace App\Http\Controllers\Kin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\TemplateReport As Template;
use App\Fungsi as Fungsi;
use Response;
use Session;
use PDF;
use App\Models\RefUnit;

class CetakRenaksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
           
    public function Renaksi(Request $request)
    {
        
        Template::settingPage('L','F4');
        Template::setHeader('L');
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        $data_unit = DB::SELECT('SELECT a.id_renstra,  a.nomor_renstra, a.tanggal_renstra,
            a.sumber_data, b.nm_unit,(SELECT tahun_1 FROM ref_tahun) AS tahun1,(SELECT tahun_5 FROM ref_tahun) AS tahun5
            FROM trx_renstra_dokumen AS a
            INNER JOIN ref_unit b ON a.id_unit=b.id_unit 
            WHERE a.id_unit ='.$request->id_unit);
        
        $data = DB::SELECT('SELECT a.id_sasaran_renstra,d.id_program_renstra,f.id_kegiatan_renstra,b.id_perkin_sasaran,
                n.id_perkin_kegiatan,a.uraian_sasaran_renstra,d.uraian_program_renstra,f.uraian_kegiatan_renstra,
                (SELECT count(g.id_kegiatan_renstra) FROM trx_renstra_kegiatan g
                INNER JOIN trx_renstra_program h ON g.id_program_renstra=h.id_program_renstra
                INNER JOIN trx_renstra_sasaran i ON h.id_sasaran_renstra=i.id_sasaran_renstra
                INNER JOIN kin_trx_perkin_opd_sasaran o ON i.id_sasaran_renstra=o.id_sasaran_renstra
                INNER JOIN kin_trx_perkin_opd_dok p ON o.id_dokumen_perkin=p.id_dokumen_perkin
                INNER JOIN kin_trx_perkin_es4_kegiatan q ON g.id_kegiatan_renstra=q.id_kegiatan_renstra
                WHERE i.id_sasaran_renstra=a.id_sasaran_renstra) AS level_1,
                (SELECT count(g.id_kegiatan_renstra) FROM trx_renstra_kegiatan g
                INNER JOIN trx_renstra_program h ON g.id_program_renstra=h.id_program_renstra
                INNER JOIN trx_renstra_sasaran i ON h.id_sasaran_renstra=i.id_sasaran_renstra
                INNER JOIN kin_trx_perkin_opd_sasaran o ON i.id_sasaran_renstra=o.id_sasaran_renstra
                INNER JOIN kin_trx_perkin_opd_dok p ON o.id_dokumen_perkin=p.id_dokumen_perkin
                INNER JOIN kin_trx_perkin_es4_kegiatan q ON g.id_kegiatan_renstra=q.id_kegiatan_renstra
                WHERE h.id_program_renstra=d.id_program_renstra) AS level_2,
                (SELECT count(g.id_kegiatan_renstra) FROM trx_renstra_kegiatan g
                INNER JOIN trx_renstra_program h ON g.id_program_renstra=h.id_program_renstra
                INNER JOIN trx_renstra_sasaran i ON h.id_sasaran_renstra=i.id_sasaran_renstra
                INNER JOIN kin_trx_perkin_opd_sasaran o ON i.id_sasaran_renstra=o.id_sasaran_renstra
                INNER JOIN kin_trx_perkin_opd_dok p ON o.id_dokumen_perkin=p.id_dokumen_perkin
                INNER JOIN kin_trx_perkin_es4_kegiatan q ON g.id_kegiatan_renstra=q.id_kegiatan_renstra
                WHERE g.id_kegiatan_renstra=f.id_kegiatan_renstra) AS level_3
                FROM trx_renstra_sasaran a
                INNER JOIN kin_trx_perkin_opd_sasaran b ON a.id_sasaran_renstra=b.id_sasaran_renstra
                INNER JOIN kin_trx_perkin_opd_dok c ON b.id_dokumen_perkin=c.id_dokumen_perkin
                INNER JOIN trx_renstra_program d ON a.id_sasaran_renstra=d.id_sasaran_renstra
                INNER JOIN kin_trx_perkin_opd_program e ON d.id_program_renstra=e.id_program_renstra
                INNER JOIN trx_renstra_kegiatan f ON d.id_program_renstra=f.id_program_renstra
                INNER JOIN kin_trx_perkin_es4_kegiatan n ON f.id_kegiatan_renstra=n.id_kegiatan_renstra
                INNER JOIN trx_renstra_tujuan j ON a.id_tujuan_renstra=j.id_tujuan_renstra
                INNER JOIN trx_renstra_misi k ON j.id_misi_renstra=k.id_misi_renstra
                INNER JOIN trx_renstra_visi m ON k.id_visi_renstra=m.id_visi_renstra
                WHERE m.id_unit='.$request->id_unit.'  and c.tahun='.$request->tahun);
        
        $jum_level_1 = 1;
        $jum_level_2 = 1;
        
        $html ='';
        $html .=  '<html>';
        $html .= '<head>';
        $html .= '</head>';
        $html .= '<body>';
        $nm_unit='';
        foreach ($data_unit AS $units){
            
            $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">'.$units->nm_unit.'</div>';
            $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">Rencana Aksi Kinerja </div>';
            $html .='<div style="text-align: center; font-size:10px; font-weight: bold; font-style: italic"> Tahun '.$request->tahun.'</div>';
            $nm_unit=$units->nm_unit;
        };
        $html .='<br>';
        $html .='<table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    
                    <th colspan="2" style="text-align: center; vertical-align:middle">Sasaran</th>
                    <th rowspan="2" style="text-align: center; vertical-align:middle">Program</th>
                    <th colspan="2" style="text-align: center; vertical-align:middle">Kegiatan</th>
                </tr>
                <tr>                
                    <th  style="text-align: center; vertical-align:middle">Uraian</th>
                    <th  style="text-align: center; vertical-align:middle">Indikator</th>
                    <th  style="text-align: center; vertical-align:middle">Uraian</th>
                    <th  style="text-align: center; vertical-align:middle">Indikator</th>
                </tr>

                </thead>
            <tbody >';
        foreach ($data AS $row){
            $html.='<tr nobr="true">';
            
            if ($jum_level_1 <= 1){
                $html.='<td rowspan="'.$row->level_1.'" style="padding: 50px; text-align: left;"><div>'.$row->uraian_sasaran_renstra.'</div></td>';
                $html.='<td rowspan="'.$row->level_1.'" style="padding: 0px; text-align: left;">';
                
                $sasaran = DB::SELECT('SELECT (@id:=@id+1) AS urut,c.uraian_indikator_sasaran_renstra,a.id_perkin_sasaran,coalesce(b.target_t1,"kosong") AS target_t1 ,coalesce(b.target_t2,"kosong")  AS target_t2,
                coalesce(b.target_t3,"kosong") AS target_t3,coalesce(b.target_t4,"kosong") AS target_t4
                    FROM kin_trx_perkin_opd_sasaran a
                    LEFT OUTER JOIN kin_trx_perkin_opd_sasaran_indikator b ON a.id_perkin_sasaran=b.id_perkin_sasaran
					LEFT OUTER JOIN trx_renstra_sasaran_indikator c ON b.id_indikator_sasaran_renstra=c.id_indikator_sasaran_renstra,(SELECT @id:=0) x
                    WHERE a.id_perkin_sasaran='.$row->id_perkin_sasaran);
                $html.='<table border="0" cellpadding="0" cellspacing="0">';
                $html.='<tr>
                <td width="5%" style="text-align: center;">No</td>
                <td width="55%" style="text-align: center;">Indikator</td>
                <td width="10%" style="text-align: center;">Triwulan 1</td>
                <td width="10%" style="text-align: center;">Triwulan 2</td>
                <td width="10%" style="text-align: center;">Triwulan 3</td>
                <td width="10%" style="text-align: center;">Triwulan 4</td>
                </tr>';
                foreach ($sasaran AS $row2){
                    $html.='<tr>
                <td width="5%" style="text-align: center;">'.$row2->urut.'</td>
                <td width="55%"  style="text-align: left;">'.$row2->uraian_indikator_sasaran_renstra.'</td>
                <td width="10%"  style="text-align: right;">'.$row2->target_t1.' </td>
                <td width="10%"  style="text-align: right;">'.$row2->target_t2.' </td>
                <td width="10%"  style="text-align: right;">'.$row2->target_t3.' </td>
                <td width="10%"  style="text-align: right;">'.$row2->target_t4.' </td>
                </tr>';
                }
                $html.='</table>';
                $html.='</td>';
                $jum_level_1 = $row->level_1;
            } else {
                $jum_level_1 = $jum_level_1 - 1;
            };
            if ($jum_level_2 <= 1){
            $html.='<td rowspan="'.$row->level_2.'" style="padding: 50px; text-align: left;"><div>'.$row->uraian_program_renstra.'</div></td>';
            $jum_level_2 = $row->level_2;
            } else {
                $jum_level_2 = $jum_level_2 - 1;
            };
            $html.='<td style="padding: 50px; text-align: justify;" ><div>'.$row->uraian_kegiatan_renstra.'</div></td>';
            
            
            $kegiatan = DB::SELECT('SELECT (@id:=@id+1) AS urut,c.uraian_indikator_kegiatan_renstra,a.id_perkin_kegiatan,coalesce(b.target_t1,"kosong") AS target_t1 ,coalesce(b.target_t2,"kosong")  AS target_t2,
                coalesce(b.target_t3,"kosong") AS target_t3,coalesce(b.target_t4,"kosong") AS target_t4
                    FROM kin_trx_perkin_es4_kegiatan a
                    LEFT OUTER JOIN kin_trx_perkin_es4_kegiatan_indikator b ON a.id_perkin_kegiatan=b.id_perkin_kegiatan
										INNER JOIN trx_renstra_kegiatan_indikator c ON b.id_indikator_kegiatan_renstra=c.id_indikator_kegiatan_renstra,(SELECT @id:=0) x
                    WHERE a.id_perkin_kegiatan='.$row->id_perkin_kegiatan);
            $html.='<td><table border="0" cellpadding="0" cellspacing="0">';
            $html.='<tr>
                <td width="5%" style="text-align: center;">No</td>
                <td width="55%" style="text-align: center;">Indikator</td>
                <td width="10%" style="text-align: center;">Triwulan 1</td>
                <td width="10%" style="text-align: center;">Triwulan 2</td>
                <td width="10%" style="text-align: center;">Triwulan 3</td>
                <td width="10%" style="text-align: center;">Triwulan 4</td>
                </tr>';
            foreach ($kegiatan AS $row2){
                $html.='<tr>
                <td width="5%" style="text-align: center;">'.$row2->urut.'</td>
                <td width="55%"  style="text-align: justify;">'.$row2->uraian_indikator_kegiatan_renstra.'</td>
                <td width="10%"  style="text-align: right;">'.$row2->target_t1.' </td>
                <td width="10%"  style="text-align: right;">'.$row2->target_t2.' </td>
                <td width="10%"  style="text-align: right;">'.$row2->target_t3.' </td>
                <td width="10%"  style="text-align: right;">'.$row2->target_t4.' </td>
                </tr>';
                
            }
            
            $html.='</table>';
            $html.='</td>';
            $html.='</tr>';
        };
        
       
        $html .= '</tbody></table></html>';
        
        Template::setFooter();
        PDF::writeHTML($html, true, false, true, false, '');
        // ---------------------------------------------------------
        
        // close and output PDF document
        PDF::Output('RencanaAksi-'.$request->nm_unit.'.pdf', 'I');
    }
    
    
}

