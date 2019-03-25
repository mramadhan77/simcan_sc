<?php
namespace App\Http\Controllers\Kin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Response;
use Session;
use PDF;
use DB;
use App\Models\RefUnit;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Http\Controllers\Laporan\TemplateReport as Template;

class CetakSakipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

        public function RKT($unit)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
        //$unit = 1;
        $pemda = Session::get('xPemda');
        $nm_unit = DB::select('select nm_unit from ref_unit where id_unit=' . $unit);
        foreach ($nm_unit as $row2) {
            // set document information
            
            // set image scale factor
            // PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            // set some language-dependent strings (optional)
            // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            // require_once(dirname(__FILE__).'/lang/eng.php');
            // $pdf->setLanguageArray($l);
            // }
            
            // ---------------------------------------------------------
            
            // set font
            PDF::SetFont('helvetica', '', 6);
            
            // add a page
            
            // foreach($tahun AS $row)
            // {
            $html = '';
            $html .= '<html>';
            $html .= '<head>';
            $html .= '<style>
                    td, th {
                    }
                </style>';
            $html .= '</head>';
            $html .= '<body>';
            PDF::SetFont('helvetica', 'B', 10);
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">RENCANA KERJA TAHUNAN</div>';
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $pemda . '</div>';
            
            $html .= '<br>';
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $row2->nm_unit . '</div>';
            
            $html .= '<br>';
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="30%" style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN</td>
                        <td width="50%" style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA</td>
                        <td width="20%" style="padding: 50px; text-align: center; font-weight: bold;" >TARGET</td>
            
                </tr>
               
                <tr height=19 >
                        <td width="30%" style="padding: 50px; text-align: center; font-weight: bold;">1</td>
                        <td width="50%" style="padding: 50px; text-align: center; font-weight: bold; ">2</td>
                        <td width="20%" style="padding: 50px; text-align: center; font-weight: bold; ">3</td>
                        
                </tr>

            </thead>';
            $html .= '<tbody>';
            $rkt = DB::select('select e.id_unit,a.uraian_sasaran_renstra, f.nm_indikator
, b.angka_tahun1, b.angka_tahun2, b.angka_tahun3, b.angka_tahun4, b.angka_tahun5,g.uraian_satuan 
from trx_renstra_sasaran a
inner join trx_renstra_sasaran_indikator b
on a.id_sasaran_renstra=b.id_sasaran_renstra
inner join trx_renstra_tujuan c
on a.id_tujuan_renstra=c.id_tujuan_renstra
INNER JOIN trx_renstra_misi d
on c.id_misi_renstra=d.id_misi_renstra
inner join trx_renstra_visi e
on d.id_visi_renstra=e.id_visi_renstra
LEFT OUTER JOIN ref_indikator f
on b.kd_indikator=f.id_indikator
LEFT OUTER JOIN ref_satuan g
on f.id_satuan_output=g.id_satuan
where e.id_unit=' . $unit);
            foreach ($rkt as $row) {
                $html .= '<tr><td rowspan="5" width="30%" font-weight: lighter;>' . $row->uraian_sasaran_renstra . '</td>
                     <td rowspan="5" width="50%">' . $row->nm_indikator . '</td>
                     <td width="20%">' . $row->angka_tahun1 . ' ' . $row->uraian_satuan . '</td> </tr>';
                $html .= '<tr>
                     <td width="20%">' . $row->angka_tahun2 . ' ' . $row->uraian_satuan . '</td> </tr>';
                $html .= '<tr>
                     <td width="20%">' . $row->angka_tahun3 . ' ' . $row->uraian_satuan . '</td> </tr>';
                $html .= '<tr>
                     <td width="20%">' . $row->angka_tahun4 . ' ' . $row->uraian_satuan . '</td> </tr>';
                $html .= '<tr>
                     <td width="20%">' . $row->angka_tahun5 . ' ' . $row->uraian_satuan . '</td> </tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>
                  </body>';
        }
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('RKT-' . $pemda . '.pdf', 'I');
    }

    public function Tapkin($unit)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
       // $unit = 1;
        $pemda = Session::get('xPemda');
        
        $nm_unit = DB::select('select nm_unit from ref_unit where id_unit=' . $unit);
        foreach ($nm_unit as $row2) {
            // set document information
            
            // set image scale factor
            // PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            // set some language-dependent strings (optional)
            // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            // require_once(dirname(__FILE__).'/lang/eng.php');
            // $pdf->setLanguageArray($l);
            // }
            
            // ---------------------------------------------------------
            
            // set font
            PDF::SetFont('helvetica', '', 6);
            
            // add a page
            
            // foreach($tahun AS $row)
            // {
            $html = '';
            $html .= '<html>';
            $html .= '<head>';
            $html .= '<style>
                    td, th {
                    }
                </style>';
            $html .= '</head>';
            $html .= '<body>';
            PDF::SetFont('helvetica', 'B', 10);
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">PENETAPAN KINERJA</div>';
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $pemda . '</div>';
            
            $html .= '<br>';
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $row2->nm_unit . '</div>';
            
            $html .= '<br>';
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="30%" style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN STRATEGIS</td>
                        <td width="50%" style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA</td>
                        <td width="20%" style="padding: 50px; text-align: center; font-weight: bold;" >TARGET</td>
            
                </tr>
            
                <tr height=19 >
                        <td width="30%" style="padding: 50px; text-align: center; font-weight: bold;">1</td>
                        <td width="50%" style="padding: 50px; text-align: center; font-weight: bold;">2</td>
                        <td width="20%" style="padding: 50px; text-align: center; font-weight: bold;">3</td>
            
                </tr>
            
            </thead>';
            $html .= '<tbody>';
            $rkt = DB::select('select e.id_unit,a.uraian_sasaran_renstra, f.nm_indikator
, b.angka_tahun1, b.angka_tahun2, b.angka_tahun3, b.angka_tahun4, b.angka_tahun5,g.uraian_satuan 
from kin_trx_perkin_opd_sasaran x
inner join trx_renstra_sasaran a
on x.id_sasaran_renstra=a.id_sasaran_renstra
inner join trx_renstra_sasaran_indikator b
on a.id_sasaran_renstra=b.id_sasaran_renstra
inner join trx_renstra_tujuan c
on a.id_tujuan_renstra=c.id_tujuan_renstra
INNER JOIN trx_renstra_misi d
on c.id_misi_renstra=d.id_misi_renstra
inner join trx_renstra_visi e
on d.id_visi_renstra=e.id_visi_renstra
LEFT OUTER JOIN ref_indikator f
on b.kd_indikator=f.id_indikator
LEFT OUTER JOIN ref_satuan g
on f.id_satuan_output=g.id_satuan
where e.id_unit=' . $unit);
            foreach ($rkt as $row) {
                $html .= '<tr><td rowspan="5" width="30%" font-weight: lighter;>' . $row->uraian_sasaran_renstra . '</td>
                     <td rowspan="5" width="50%">' . $row->nm_indikator . '</td>
                     <td width="20%">' . $row->angka_tahun1 . ' ' . $row->uraian_satuan . '</td> </tr>';
                $html .= '<tr>
                     <td width="20%">' . $row->angka_tahun2 . ' ' . $row->uraian_satuan . '</td> </tr>';
                $html .= '<tr>
                     <td width="20%">' . $row->angka_tahun3 . ' ' . $row->uraian_satuan . '</td> </tr>';
                $html .= '<tr>
                     <td width="20%">' . $row->angka_tahun4 . ' ' . $row->uraian_satuan . '</td> </tr>';
                $html .= '<tr>
                     <td width="20%">' . $row->angka_tahun5 . ' ' . $row->uraian_satuan . '</td> </tr>';
            }
            
            $html .= '</tbody>';
            $html .= '</table>
                  </body>';
        }
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('Tapkin-' . $pemda . '.pdf', 'I');
    }

    public function Urkin($unit)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
       // $unit=1;
        $pemda = Session::get('xPemda');
        $nm_unit = DB::select('select nm_unit from ref_unit where id_unit=' . $unit);
        foreach ($nm_unit as $row2) {
            // set document information
            
            // set image scale factor
            // PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            // set some language-dependent strings (optional)
            // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            // require_once(dirname(__FILE__).'/lang/eng.php');
            // $pdf->setLanguageArray($l);
            // }
            
            // ---------------------------------------------------------
            
            // set font
            PDF::SetFont('helvetica', '', 6);
            
            // add a page
            
            // foreach($tahun AS $row)
            // {
            $html = '';
            $html .= '<html>';
            $html .= '<head>';
            $html .= '<style>
                    td, th {
                    }
                </style>';
            $html .= '</head>';
            $html .= '<body>';
            PDF::SetFont('helvetica', 'B', 10);
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">PENGUKURAN KINERJA</div>';
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $pemda . '</div>';
            
            $html .= '<br>';
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $row2->nm_unit . '</div>';
            
            $html .= '<br>';
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN STRATEGIS</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >TARGET</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >REALISASI</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >%</td>  
            
                </tr>
            
                <tr height=19 >
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >1</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >2</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >3</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >4</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >5</td>
            
                </tr>
            
            </thead>';
            $html .= '<tbody>';
            $rkt = DB::select('select e.id_unit,a.uraian_sasaran_renstra, f.nm_indikator
, b.angka_tahun1, b.angka_tahun2, b.angka_tahun3, b.angka_tahun4, b.angka_tahun5,g.uraian_satuan
from kin_trx_perkin_opd_sasaran x
inner join trx_renstra_sasaran a
on x.id_sasaran_renstra=a.id_sasaran_renstra
inner join trx_renstra_sasaran_indikator b
on a.id_sasaran_renstra=b.id_sasaran_renstra
inner join trx_renstra_tujuan c
on a.id_tujuan_renstra=c.id_tujuan_renstra
INNER JOIN trx_renstra_misi d
on c.id_misi_renstra=d.id_misi_renstra
inner join trx_renstra_visi e
on d.id_visi_renstra=e.id_visi_renstra
LEFT OUTER JOIN ref_indikator f
on b.kd_indikator=f.id_indikator
LEFT OUTER JOIN ref_satuan g
on f.id_satuan_output=g.id_satuan
where e.id_unit=' . $unit);
            foreach ($rkt as $row) {
                $html .= '<tr><td rowspan="5" width="27%" font-weight: lighter;>' . $row->uraian_sasaran_renstra . '</td>
                     <td rowspan="5" width="28%">' . $row->nm_indikator . '</td>
                     <td width="15%">' . $row->angka_tahun1 . ' ' . $row->uraian_satuan . '</td>
                     <td width="15%"></td> 
                     <td width="15%"></td> </tr>';
                $html .= '<tr>
                     <td width="15%">' . $row->angka_tahun2 . ' ' . $row->uraian_satuan . '</td>
<td width="15%"></td><td width="15%"></td> </tr>';
                $html .= '<tr>
            <td width="15%">' . $row->angka_tahun3 . ' ' . $row->uraian_satuan . '</td><td width="15%"></td>
                     <td width="15%"></td> </tr>';
                $html .= '<tr>
                     <td width="15%">' . $row->angka_tahun4 . ' ' . $row->uraian_satuan . '</td> <td width="15%"></td>
                     <td width="15%"></td> </tr>';
                $html .= '<tr>
            <td width="15%">' . $row->angka_tahun5 . ' ' . $row->uraian_satuan . '</td> <td width="15%"></td>
                     <td width="15%"></td> </tr>';
            }
            
            $html .= '</tbody>';
            $html .= '</table>
                  </body>';
        }
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('PengukuranKinerja-' . $pemda . '.pdf', 'I');
    }

    public function IKUSasaran($unit)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
        
        $id_kegiatan = 20;
        $sub_unit = 7;
        $nama_sub = "";
        $pagu_skpd_peg = 0;
        $pagu_skpd_bj = 0;
        $pagu_skpd_mod = 0;
        $pagu_skpd_pend = 0;
        $pagu_skpd_btl = 0;
        $pagu_skpd = 0;
        $pemda = Session::get('xPemda');
        
        // set document information
        
        // set image scale factor
        // PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        // require_once(dirname(__FILE__).'/lang/eng.php');
        // $pdf->setLanguageArray($l);
        // }
        
        // ---------------------------------------------------------
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        // add a page
        
        // foreach($tahun AS $row)
        // {
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
        PDF::SetFont('helvetica', 'B', 10);
        $nm_unit=DB::select('
select nm_unit from ref_unit
where id_unit='.$unit);
        foreach ($nm_unit as $row)
        {
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">INDIKATOR KINERJA UTAMA</div>';
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $row->nm_unit . '</div>';
        }
        $html .= '<br>';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN STRATEGIS</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA UTAMA</td>
                        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >PENJELASAN / FORM PERHITUNGAN</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >SUMBER DATA</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >PENANGGUNG JAWAB</td>
            
                </tr>
            
                <tr height=19 >
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >1</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >2</td>
                        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >3</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >4</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >5</td>
            
                </tr>
            
            </thead>';
        $html .= '<tbody>';
        $indsas=DB::select('select a.uraian_sasaran_renstra,b.uraian_indikator_sasaran_renstra,c.metode_penghitungan,c.sumber_data_indikator 
from trx_renstra_sasaran a
inner join trx_renstra_sasaran_indikator b
on a.id_sasaran_renstra=b.id_sasaran_renstra
inner join kin_trx_iku_opd_sasaran d
on b.id_indikator_sasaran_renstra=d.id_indikator_sasaran_renstra
left outer join ref_indikator c
on b.kd_indikator=c.id_indikator
inner join trx_renstra_tujuan e
on a.id_tujuan_renstra=e.id_tujuan_renstra
inner join trx_renstra_misi f
on e.id_misi_renstra=f.id_misi_renstra
inner join trx_renstra_visi g
on f.id_visi_renstra=g.id_visi_renstra
where g.id_unit='.$unit);
        foreach ($indsas as $row)
        {
            $html .= '<tr>
            <td width="27%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_renstra.'</td>
            <td width="28%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_sasaran_renstra.'</td>
            <td width="20%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->metode_penghitungan.'</td>
            <td width="15%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->sumber_data_indikator.'</td>
            <td width="10%"  style="padding: 50px; text-align: left; font-weight: normal;" ></td>
            </tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        
        $html .= '
                  </body>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('IKU Sasaran-' . $pemda . '.pdf', 'I');
    }
    
    public function IKUSasaranPemda()
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
        
        $id_kegiatan = 20;
        $sub_unit = 7;
        $nama_sub = "";
        $pagu_skpd_peg = 0;
        $pagu_skpd_bj = 0;
        $pagu_skpd_mod = 0;
        $pagu_skpd_pend = 0;
        $pagu_skpd_btl = 0;
        $pagu_skpd = 0;
        $pemda = Session::get('xPemda');
        
        // set document information
        
        // set image scale factor
        // PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        // require_once(dirname(__FILE__).'/lang/eng.php');
        // $pdf->setLanguageArray($l);
        // }
        
        // ---------------------------------------------------------
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        // add a page
        
        // foreach($tahun AS $row)
        // {
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
        PDF::SetFont('helvetica', 'B', 10);
       
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">INDIKATOR KINERJA UTAMA</div>';
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $pemda . '</div>';
        
        $html .= '<br>';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN STRATEGIS</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA UTAMA</td>
                        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >PENJELASAN / FORM PERHITUNGAN</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >SUMBER DATA</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >PENANGGUNG JAWAB</td>
            
                </tr>
            
                <tr height=19 >
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >1</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >2</td>
                        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >3</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >4</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >5</td>
            
                </tr>
            
            </thead>';
        $html .= '<tbody>';
        $indsas=DB::select('select a.uraian_sasaran_rpjmd,b.uraian_indikator_sasaran_rpjmd,c.metode_penghitungan,c.sumber_data_indikator 
from trx_rpjmd_sasaran a
inner join trx_rpjmd_sasaran_indikator b
on a.id_sasaran_rpjmd=b.id_sasaran_rpjmd
inner join kin_trx_iku_pemda_rinci d
on b.id_indikator_sasaran_rpjmd=d.id_indikator_sasaran_rpjmd
left outer join ref_indikator c
on b.kd_indikator=c.id_indikator

');
        foreach ($indsas as $row)
        {
            $html .= '<tr>
            <td width="27%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_rpjmd.'</td>
            <td width="28%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_sasaran_rpjmd.'</td>
            <td width="20%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->metode_penghitungan.'</td>
            <td width="15%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->sumber_data_indikator.'</td>
            <td width="10%"  style="padding: 50px; text-align: left; font-weight: normal;" ></td>
            </tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        
        $html .= '
                  </body>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('IKU Sasaran-' . $pemda . '.pdf', 'I');
    }
    
    
    public function IKUProgram($unit)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
        
        $id_kegiatan = 20;
        $sub_unit = 7;
        $nama_sub = "";
        $pagu_skpd_peg = 0;
        $pagu_skpd_bj = 0;
        $pagu_skpd_mod = 0;
        $pagu_skpd_pend = 0;
        $pagu_skpd_btl = 0;
        $pagu_skpd = 0;
        $pemda = Session::get('xPemda');
        
        // set document information
        
        // set image scale factor
        // PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        // require_once(dirname(__FILE__).'/lang/eng.php');
        // $pdf->setLanguageArray($l);
        // }
        
        // ---------------------------------------------------------
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        // add a page
        
        // foreach($tahun AS $row)
        // {
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
        PDF::SetFont('helvetica', 'B', 10);
        $nm_unit=DB::select('
select nm_unit from ref_unit
where id_unit='.$unit);
        foreach ($nm_unit as $row)
        {
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">INDIKATOR KINERJA UTAMA</div>';
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $row->nm_unit . '</div>';
        }
        $html .= '<br>';
      
        $indprog=DB::select('select a.uraian_sasaran_program,b.uraian_indikator_program_renstra,c.metode_penghitungan,c.sumber_data_indikator
from trx_renstra_program a
inner join trx_renstra_program_indikator b
on a.id_program_renstra=b.id_program_renstra
inner join kin_trx_iku_opd_program d
on b.id_indikator_program_renstra=d.id_indikator_program_renstra
left outer join ref_indikator c
on b.kd_indikator=c.id_indikator
inner join trx_renstra_sasaran h
on a.id_sasaran_renstra=h.id_sasaran_renstra
inner join trx_renstra_tujuan e
on h.id_tujuan_renstra=e.id_tujuan_renstra
inner join trx_renstra_misi f
on e.id_misi_renstra=f.id_misi_renstra
inner join trx_renstra_visi g
on f.id_visi_renstra=g.id_visi_renstra
where g.id_unit='.$unit);
       
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN PROGRAM</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA UTAMA</td>
                        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >PENJELASAN / FORM PERHITUNGAN</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >SUMBER DATA</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >PENANGGUNG JAWAB</td>
            
                </tr>
            
                <tr height=19 >
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >1</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >2</td>
                        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >3</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >4</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >5</td>
            
                </tr>
            
            </thead>';
        $html .= '<tbody>';
        foreach ($indprog as $row)
        {
            $html .= '<tr>
            <td width="27%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_program.'</td>
            <td width="28%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_program_renstra.'</td>
            <td width="20%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->metode_penghitungan.'</td>
            <td width="15%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->sumber_data_indikator.'</td>
            <td width="10%"  style="padding: 50px; text-align: left; font-weight: normal;" ></td>
            </tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<br><br>';
      
        $html .= '</body>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('IKU Program-' . $pemda . '.pdf', 'I');
    }
    
    public function IKUKegiatan($unit)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
        
        $id_kegiatan = 20;
        $sub_unit = 7;
        $nama_sub = "";
        $pagu_skpd_peg = 0;
        $pagu_skpd_bj = 0;
        $pagu_skpd_mod = 0;
        $pagu_skpd_pend = 0;
        $pagu_skpd_btl = 0;
        $pagu_skpd = 0;
        $pemda = Session::get('xPemda');
        
        // set document information
        
        // set image scale factor
        // PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        // require_once(dirname(__FILE__).'/lang/eng.php');
        // $pdf->setLanguageArray($l);
        // }
        
        // ---------------------------------------------------------
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        // add a page
        
        // foreach($tahun AS $row)
        // {
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
        PDF::SetFont('helvetica', 'B', 10);
        $nm_unit=DB::select('
select nm_unit from ref_unit
where id_unit='.$unit);
        foreach ($nm_unit as $row)
        {
        $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">INDIKATOR KINERJA UTAMA</div>';
        $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $row->nm_unit . '</div>';
        }
        $html .= '<br>';
      
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN KEGIATAN</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA UTAMA</td>
                        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >PENJELASAN / FORM PERHITUNGAN</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >SUMBER DATA</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >PENANGGUNG JAWAB</td>
            
                </tr>
            
                <tr height=19 >
                        <td width="27%"  style="padding: 50px; text-align: center; font-weight: bold;" >1</td>
                        <td width="28%"  style="padding: 50px; text-align: center; font-weight: bold;" >2</td>
                        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >3</td>
                        <td width="15%"  style="padding: 50px; text-align: center; font-weight: bold;" >4</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >5</td>
            
                </tr>
            
            </thead>';
        $html .= '<tbody>';
        
        $indkeg=DB::select('select a.uraian_sasaran_kegiatan,b.uraian_indikator_kegiatan_renstra,c.metode_penghitungan,c.sumber_data_indikator
from trx_renstra_kegiatan a
inner join trx_renstra_kegiatan_indikator b
on a.id_kegiatan_renstra=b.id_kegiatan_renstra
INNER JOIN kin_trx_iku_opd_kegiatan d
on b.id_indikator_kegiatan_renstra=d.id_indikator_kegiatan_renstra
left outer join ref_indikator c
on b.kd_indikator=c.id_indikator
inner join trx_renstra_program i 
on a.id_program_renstra=i.id_program_renstra
inner join trx_renstra_sasaran h
on i.id_sasaran_renstra=h.id_sasaran_renstra
inner join trx_renstra_tujuan e
on h.id_tujuan_renstra=e.id_tujuan_renstra
inner join trx_renstra_misi f
on e.id_misi_renstra=f.id_misi_renstra
inner join trx_renstra_visi g
on f.id_visi_renstra=g.id_visi_renstra
where g.id_unit='.$unit);
        foreach ($indkeg as $row)
        {
            $html .= '<tr>
            <td width="27%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_kegiatan.'</td>
            <td width="28%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_kegiatan_renstra.'</td>
            <td width="20%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->metode_penghitungan.'</td>
            <td width="15%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->sumber_data_indikator.'</td>
            <td width="10%"  style="padding: 50px; text-align: left; font-weight: normal;" ></td>
            </tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>
                  </body>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('IKU Kegiatan-' . $pemda . '.pdf', 'I');
    }
    
    
    public function RencanaAksi()
    {
        
    }
    public function PK_Pemda($tahun)
    {
        Template::settingPagePotrait();
        $pemda = Session::get('xPemda');
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
        PDF::SetFont('helvetica', 'B', 10);
        $html .= '<table cellpadding="0" cellspacing="0" style="border-bottom-style: double;">';
            $html .= '<tr>';
            $html .= '<th width="90%"  style="text-align: center; font-size:13px; font-weight: bold;">'.$pemda.'</th>';
        $html .= '<th width="10%" rowspan="2" style="text-align: right; font-size:16px; font-weight: bold;"><img src="vendor/default.png" class="img-thumbnail" width="75" height="90" ></th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th width="90%"  style="text-align: center; font-size:12px; font-weight: bold;">PERJANJIAN KINERJA TAHUN '.$tahun.'</th>';
        $html .= '</tr>
                    </table>';
        $html .= '<br>';
        $html .= '<br>';
        
        $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
        $html .= '<tr>';
        $html .= '<td>Dalam rangka mewujudkan manajemen pemerintahan yang efektif, transparan dan  akuntabel  serta berorientasi  pada hasil, yang  bertanda tangan  di  bawah ini:</td>';
        $html .= '</tr></table>';
        $html .= '<br>';
        
        $kada=DB::select('select nama_kepala_daerah, nama_jabatan_kepala_daerah
                          from ref_pemda
                          LIMIT 1');
        foreach ($kada as $row)
        {
        $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
        $html .= '<tr>';
        $html .= '<td width="5%"></td>';
        $html .= '<td width="10%">Nama</td>';
        $html .= '<td width="3%">:</td>';
        $html .= '<td width="82%">'.$row->nama_kepala_daerah.'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="5%"></td>';
        $html .= '<td width="10%">Jabatan</td>';
        $html .= '<td width="3%">:</td>';
        $html .= '<td width="82%">'.$row->nama_jabatan_kepala_daerah.'</td>';
        
        $html .= '</tr></table>';
        
        $html .= '<br>';
        $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
        $html .= '<tr>';
        $html .= '<td>berjanji  akan  mewujudkan  target  kinerja  yang  seharusnya  sesuai  lampiran perjanjian ini, dalam rangka mencapai target kinerja jangka menengah seperti yang telah ditetapkan dalam dokumen perencanaan.</td>';
        $html .= '</tr></table>';
        $html .= '<br>';
        $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
        $html .= '<tr>';
        $html .= '<td>Keberhasilan   dan   kegagalan   pencapaian   target   kinerja   tersebut   menjadi tanggung jawab kami.</td>';
        $html .= '</tr></table>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<table cellpadding="4" cellspacing="4" style="text-align: right; font-size:10px; font-weight: normal;" >';
        $html .= '<tr>';
     
        $html .= '<td>'.$row->nama_jabatan_kepala_daerah.'</td>';
        $html .= '</tr>';
        $html .= '<tr><td></td></tr>';
        $html .= '<tr><td></td></tr>';
        $html .= '<tr><td></td></tr>';
        $html .= '<tr>';
        $html .= '<td>'.$row->nama_kepala_daerah.'</td>';
        
        $html .= '</tr></table>';
        
        }
       // $html.='<table cellpadding="0" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '</body></html>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::AddPage('P');
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
       
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<thead>
        <tr height=19>
       
        <td width="40%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN STRATEGIS</td>
        <td width="40%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA</td>
        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >TARGET</td>
        </tr></thead>';
        $sasaran=DB::select('select a.uraian_sasaran_rpjmd,b.uraian_indikator_sasaran_rpjmd,
case (select tahun_5 from ref_tahun)-'.$tahun.' 
when 0 then b.angka_tahun5
when 1 then b.angka_tahun4
when 2 then b.angka_tahun3
when 3 then b.angka_tahun2
else b.angka_tahun1 end as angka_tahun,
d.uraian_satuan
 from trx_rpjmd_sasaran a
LEFT OUTER  join trx_rpjmd_sasaran_indikator b
on a.id_sasaran_rpjmd=b.id_sasaran_rpjmd
LEFT OUTER JOIN ref_indikator c
on b.kd_indikator=c.id_indikator
LEFT OUTER JOIN ref_satuan d
on c.id_satuan_output=d.id_satuan');
        $html .= '<tbody>';
        foreach($sasaran as $row)
        {
         $html.='<tr>
        <td width="40%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_rpjmd.'</td>
        <td width="40%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_sasaran_rpjmd.'</td>
        <td width="20%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.number_format($row->angka_tahun, 2, ',', '.').' '.$row->uraian_satuan.'</td>
        </tr>';   
        }
        $html .= '</tbody>';
                  $html .= '  </table>';
                  
                  $html .= '<br>';
                  $html .= '<br>';
                  $html .= '<br>';
                  $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
                  $html .= '<thead>
        <tr height=19>
                      
        <td width="70%"  style="padding: 50px; text-align: center; font-weight: bold;" >PROGRAM</td>
        <td width="30%"  style="padding: 50px; text-align: center; font-weight: bold;" >ANGGARAN (Rp.)</td>
        </tr></thead>';
                  $program=DB::select('select a.uraian_program_rpjmd,
case  (select tahun_5 from ref_tahun)-'.$tahun.'  
when 0 then a.pagu_tahun5
when 1 then a.pagu_tahun4
when 2 then a.pagu_tahun3
when 3 then a.pagu_tahun2
else a.pagu_tahun1 end as pagu_tahun

from trx_rpjmd_program a');
                  $html .= '<tbody>';
                  foreach($program as $row)
                  {
                      $html.='<tr>
        <td width="70%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_program_rpjmd.'</td>
        <td width="30%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->pagu_tahun, 2, ',', '.').'</td>
        </tr>';
                  }
                  $html .= '</tbody>';
                  $html .= '  </table>';
     
        // $html.='<table cellpadding="0" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '</body></html>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('PKPemda-' . $pemda . '.pdf', 'I');
    }

    public function PK_OPD($tahun, $unit)
    {
        Template::settingPagePotrait();
        $pemda = Session::get('xPemda');
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
        PDF::SetFont('helvetica', 'B', 10);
        $html .= '<table cellpadding="0" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<tr>';
        $html .= '<th width="90%"  style="text-align: center; font-size:13px; font-weight: bold;">'.$pemda.'</th>';
        $html .= '<th width="10%" rowspan="3" style="text-align: right; font-size:16px; font-weight: bold;"><img src="vendor/default.png" class="img-thumbnail" width="75" height="90" ></th>';
        $html .= '</tr>';
        $nm_unit=DB::select('
select nm_unit from ref_unit
where id_unit='.$unit);
        $nama_unit='';
        foreach ($nm_unit as $row)
        {
        $html .= '<tr>';
        $html .= '<th width="90%"  style="text-align: center; font-size:12px; font-weight: bold;">'.$row->nm_unit.'</th>';
        $html .= '</tr>';
        $nama_unit=$row->nm_unit;
        }
        $html .= '<tr>';
        $html .= '<th width="90%"  style="text-align: center; font-size:12px; font-weight: bold;">PERJANJIAN KINERJA TAHUN '.$tahun.'</th>';
        $html .= '</tr>';
        $html .= '</table>';
        
        $html .= '<br>';
        $html .= '<br>';
        
        $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
        $html .= '<tr>';
        $html .= '<td>Dalam rangka mewujudkan manajemen pemerintahan yang efektif, transparan dan  akuntabel  serta berorientasi  pada hasil, kami yang  bertanda tangan  di  bawah ini:</td>';
        $html .= '</tr></table>';
        $html .= '<br>';
        ///////////////////// INI MASIH DIHARDCODE /////////////////////////////
       $nm_kada='';
       $jab_kada='';
        $kadin=DB::select('select nama_kepala_daerah, nama_jabatan_kepala_daerah
                          from ref_pemda
                          LIMIT 1');
        foreach ($kadin as $row)
        {
            $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Nama</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">NAMA KEPALA DINAS</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Jabatan</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">JABATAN KEPALA DINAS</td>';
            
            $html .= '</tr> ';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">NIP</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">NIP KEPALA DINAS</td>';
            
            $html .= '</tr></table>';
            
        }
        /////////////////////////////////////////////////////////////////////////
        $html .= '<br>';
        $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
        $html .= '<tr>';
        $html .= '<td>selanjutnya disebut pihak pertama</td>';
        $html .= '</tr></table>';
        $html .= '<br>';
        $kada=DB::select('select nama_kepala_daerah, nama_jabatan_kepala_daerah
                          from ref_pemda
                          LIMIT 1');
        foreach ($kada as $row)
        {
            $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Nama</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$row->nama_kepala_daerah.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Jabatan</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$row->nama_jabatan_kepala_daerah.'</td>';
            
            $html .= '</tr></table>';
            $nm_kada=$row->nama_kepala_daerah;
            $jab_kada=$row->nama_jabatan_kepala_daerah;
        }
        $html .= '<br>';
        $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
        $html .= '<tr>';
        $html .= '<td>selaku atasan pihak pertama, selanjutnya disebut pihak kedua</td>';
        $html .= '</tr></table>';
        
            $html .= '<br>';
            $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
            $html .= '<tr>';
            $html .= '<td>Pihak  pertama  berjanji  akan  mewujudkan  target  kinerja  yang  seharusnya sesuai  lampiran  perjanjian  ini,  dalam  rangka  mencapai  target  kinerja  jangka menengah   seperti   yang   telah   ditetapkan   dalam   dokumen   perencanaan. Keberhasilan   dan   kegagalan   pencapaian   target   kinerja   tersebut   menjadi tanggung jawab kami.</td>';
            $html .= '</tr></table>';
            $html .= '<br>';
            $html .= '<table cellpadding="4" cellspacing="4" style="text-align: justify; font-size:10px; font-weight: normal;" >';
            $html .= '<tr>';
            $html .= '<td>Pihak kedua akan melakukan supervisi yang diperlukan serta akan melakukan evaluasi terhadap capaian kinerja dari perjanjian ini dan mengambil tindakan yang diperlukan dalam rangka pemberian penghargaan dan sanksi.</td>';
            $html .= '</tr></table>';
            $html .= '<br>';
            $html .= '<br>';
            $html .= '<br>';
            $html .= '<br>';
            $html .= '<br>';
            $html .= '<br>';
            $html .= '<table cellpadding="4" cellspacing="4" style="text-align: center; font-size:10px; font-weight: normal;" >';
            $html .= '<tr>';
            $html .= '<td width="50%">'.$jab_kada.'</td>';
            //////////////// HARD CODE /////////////////////////
            $html .= '<td width="50%">'."Pihak Pertama".'</td>';
            ///////////////////////////////////////////////////
           
            $html .= '</tr>';
            $html .= '<tr><td></td></tr>';
            $html .= '<tr><td></td></tr>';
            $html .= '<tr><td></td></tr>';
            $html .= '<tr>';
            $html .= '<td width="50%">'.$nm_kada.'</td>';
            ////////////// HARD CODE /////////////////////////
            $html .= '<td width="50%">'."NAMA KEPALA DINAS".'</td>';
            /////////////////////////////////////////////////
            
            
            $html .= '</tr></table>';
            
        
        // $html.='<table cellpadding="0" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '</body></html>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::AddPage('P');
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
        
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<thead>
        <tr height=19>
            
        <td width="40%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN STRATEGIS</td>
        <td width="40%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA</td>
        <td width="20%"  style="padding: 50px; text-align: center; font-weight: bold;" >TARGET</td>
        </tr></thead>';
        $sasaran=DB::select('select COALESCE(a.uraian_sasaran_renstra,"tidak ada data") as uraian_sasaran_renstra,COALESCE(b.uraian_indikator_sasaran_renstra,"tidak ada data") as uraian_indikator_sasaran_renstra,
COALESCE(case (select tahun_5 from ref_tahun)-'.$tahun.'
when 0 then b.angka_tahun5
when 1 then b.angka_tahun4
when 2 then b.angka_tahun3
when 3 then b.angka_tahun2
else b.angka_tahun1 end,"tidak ada data") as angka_tahun,
COALESCE(d.uraian_satuan,"tidak ada data") as uraian_satuan
 from trx_renstra_sasaran a
LEFT OUTER  join trx_renstra_sasaran_indikator b
on a.id_sasaran_renstra=b.id_sasaran_renstra
LEFT OUTER JOIN ref_indikator c
on b.kd_indikator=c.id_indikator
LEFT OUTER JOIN ref_satuan d
on c.id_satuan_output=d.id_satuan
inner join trx_renstra_tujuan e 
on a.id_tujuan_renstra=e.id_tujuan_renstra
inner join trx_renstra_misi f
on e.id_misi_renstra=f.id_misi_renstra
inner join trx_renstra_visi g
on f.id_visi_renstra=g.id_visi_renstra
where g.id_unit='.$unit);
        $html .= '<tbody>';
        foreach($sasaran as $row)
        {
            $html.='<tr>
        <td width="40%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_renstra.'</td>
        <td width="40%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_sasaran_renstra.'</td>';
            if(is_numeric($row->angka_tahun))
            {
            $html.='<td width="20%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.number_format($row->angka_tahun, 2, ',', '.').' '.$row->uraian_satuan.'</td>
        </tr>';
            }
            else 
            {
                $html.='<td width="20%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->angka_tahun.'</td>
        </tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '  </table>';
        
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<thead>
        <tr height=19>
            
        <td width="70%"  style="padding: 50px; text-align: center; font-weight: bold;" >PROGRAM</td>
        <td width="30%"  style="padding: 50px; text-align: center; font-weight: bold;" >ANGGARAN (Rp.)</td>
        </tr></thead>';
        $program=DB::select('select a.uraian_program_renstra,
case  (select tahun_5 from ref_tahun)-'.$tahun.'
when 0 then a.pagu_tahun5
when 1 then a.pagu_tahun4
when 2 then a.pagu_tahun3
when 3 then a.pagu_tahun2
else a.pagu_tahun1 end as pagu_tahun
            
from trx_renstra_program a
inner join trx_renstra_sasaran b 
on a.id_sasaran_renstra=b.id_sasaran_renstra
inner join trx_renstra_tujuan e 
on b.id_tujuan_renstra=e.id_tujuan_renstra
inner join trx_renstra_misi f
on e.id_misi_renstra=f.id_misi_renstra
inner join trx_renstra_visi g
on f.id_visi_renstra=g.id_visi_renstra
where g.id_unit='.$unit);
        $html .= '<tbody>';
        foreach($program as $row)
        {
            $html.='<tr>
        <td width="70%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_program_renstra.'</td>
        <td width="30%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->pagu_tahun, 2, ',', '.').'</td>
        </tr>';
        }
        $html .= '</tbody>';
        $html .= '  </table>';
        
        // $html.='<table cellpadding="0" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '</body></html>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('PKOPD-' . $nama_unit . '.pdf', 'I');
    }
    
    
}