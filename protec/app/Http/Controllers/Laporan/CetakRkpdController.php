<?php
namespace App\Http\Controllers\Laporan;

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
use App\Models\TrxRenjaRancangan;
use App\Models\TrxRenjaRancanganProgram;
use App\Models\TrxRenjaRancanganProgramIndikator;
use App\Models\RefSshRekening;
use App\Models\RefRek5;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;

class CetakRkpdController extends Controller
{

    public function T_V_C_66($tahun)
    {
        $countrow = 0;
        $totalrow = 55;
        
        
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('SSH Kelompok');
        
        // set default header data
        PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 011', PDF_HEADER_STRING);
        
        // set header and footer fonts
        PDF::setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        PDF::setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        
        // set default monospaced font
        PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        PDF::SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
        
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
        PDF::AddPage('L');
        
        // column titles
        $header = array(
            'No',
            'SKPD',
            'Program',
            'Indikator Kinerja',
            'Target Kinerja',
            'Pagu Indikatif'
        );
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0);
        // $font=PDF::addTTFfont('Elibyy\TCPDF\Facades\tahoma.ttf', 'TrueTypeUnicode', '', 32);
        // PDF::SetFont($font, 'B', 10);
        PDF::SetFont('helvetica', 'B', 10);
        
        // Header
        PDF::Cell('240', 5, Session::get('xPemda') , 1, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Cell('240', 5, 'Kompilasi Program dan Pagu Indikatif Tiap SKPD	', 1, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::SetFont('', 'B');
        PDF::SetFont('helvetica', 'B', 8);
        
        // Header Column
        
        $wh = array(
            10,
            20,
            70,
            70,
            35,
            35
        );
        $w = array(
            10,195,35
        );
        $w1 = array(
            10,
            20,
            70,
            70,
            35,
            35
        );
        
        $num_headers = count($header);
        for ($i = 0; $i < $num_headers; ++ $i) {
            PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
        }
        PDF::Ln();
        $countrow ++;
        // Color and font restoration
        
        PDF::SetFillColor(224, 235, 255);
        PDF::SetTextColor(0);
        PDF::SetFont('helvetica', '', 6);
        // Data
        $fill = 0;
        $selisih_tahun=1;
        $tahun2=DB::select('SELECT '.$tahun.'-ref_tahun.tahun_0 as selisih
FROM
ref_tahun
');
        foreach ($tahun2 as $row5)
        {
        $selisih_tahun=$row5->selisih;
        }
        $Unit=DB::select('select DISTINCT 
u.id_unit,
u.nm_unit,
case '.$selisih_tahun.' 
when 1 then sum(p.pagu_tahun1)
when 2 then sum(p.pagu_tahun2)
when 3 then sum(p.pagu_tahun3)
when 4 then sum(p.pagu_tahun4)
when 5 then sum(p.pagu_tahun5)
end as sub_total
				from trx_renstra_visi AS v
INNER JOIN trx_renstra_misi AS m ON m.id_visi_renstra = v.id_visi_renstra
INNER JOIN trx_renstra_tujuan AS t ON t.id_misi_renstra = m.id_misi_renstra
INNER JOIN trx_renstra_sasaran AS s ON s.id_tujuan_renstra = t.id_tujuan_renstra
INNER JOIN trx_renstra_program AS p ON p.id_sasaran_renstra = s.id_sasaran_renstra
INNER JOIN ref_unit AS u ON v.id_unit = u.id_unit
where m.no_urut not in (98,99)
group by id_unit,nm_unit
				');
        foreach ($Unit as $row) {
            PDF::SetFont('helvetica', 'B', 8);
            PDF::MultiCell($w[0], 5, '', 0, 'L', 0, 0);
            PDF::MultiCell($w[1], 5, $row->nm_unit, 0, 'L', 0, 0);
            PDF::MultiCell($w[2], 5, number_format($row->sub_total, 2, ',', '.'), 0, 'R', 0, 0);
            PDF::Ln();
            $countrow ++;
            if ($countrow >= $totalrow) {
                PDF::AddPage('L');
                $countrow = 0;
                for ($i = 0; $i < $num_headers; ++ $i) {
                    PDF::SetFont('helvetica', 'B', 8);
                    PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                }
                
                PDF::Ln();
                $countrow ++;
            }
            // $fill=!$fill;
            $program = DB::select('SELECT
p.id_program_renstra,
p.uraian_program_renstra,
case '.$selisih_tahun.' 
when 1 then p.pagu_tahun1
when 2 then p.pagu_tahun2
when 3 then p.pagu_tahun3
when 4 then p.pagu_tahun4
when 5 then p.pagu_tahun5
end as pagu,

u.nm_unit
FROM
trx_renstra_visi AS v
INNER JOIN trx_renstra_misi AS m ON m.id_visi_renstra = v.id_visi_renstra
INNER JOIN trx_renstra_tujuan AS t ON t.id_misi_renstra = m.id_misi_renstra
INNER JOIN trx_renstra_sasaran AS s ON s.id_tujuan_renstra = t.id_tujuan_renstra
INNER JOIN trx_renstra_program AS p ON p.id_sasaran_renstra = s.id_sasaran_renstra
INNER JOIN ref_unit AS u ON v.id_unit = u.id_unit
where u.id_unit = '.$row->id_unit.' and m.no_urut not in (98,99)');
            foreach ($program as $row2) {
                $height=ceil(strlen($row2->uraian_program_renstra)/67)*3;
                PDF::SetFont('helvetica', 'B', 6);
                PDF::MultiCell($w1[0], $height, '', 0, 'L', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 0, 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->uraian_program_renstra, 0, 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, '', 0, 'L', 0, 0);
                PDF::MultiCell($w1[4], $height,  '', 0, 'L', 0, 0);
                PDF::MultiCell($w1[5], $height, number_format($row2->pagu, 2, ',', '.'), 0, 'R', 0, 0);
                PDF::Ln();
                $countrow=$countrow+ceil(strlen($row2->uraian_program_renstra)/67);
                if ($countrow >= $totalrow) {
                    PDF::AddPage('L');
                    $countrow = 0;
                    for ($i = 0; $i < $num_headers; ++ $i) {
                        PDF::SetFont('helvetica', 'B', 8);
                        PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                    }
                    PDF::Ln();
                    $countrow ++;
                }
                $indikator = DB::select('select uraian_indikator_program_renstra,
case '.$selisih_tahun.'  
when 1 then angka_tahun1
when 2 then angka_tahun2
when 3 then angka_tahun3
when 4 then angka_tahun4
when 5 then angka_tahun5
end as angka
from trx_renstra_program_indikator
where id_program_renstra = '.$row2->id_program_renstra);
    
                
                foreach ($indikator as $row3) {
                    $height=ceil(strlen($row3->uraian_indikator_program_renstra)/67)*2.5;
                    PDF::SetFont('helvetica', '', 6);
                    PDF::MultiCell($w1[0], $height, '', 0, 'L', 0, 0);
                    PDF::MultiCell($w1[1], $height, '', 0, 'L', 0, 0);
                    PDF::MultiCell($w1[2], $height, '', 0, 'L', 0, 0);
                    PDF::MultiCell($w1[3], $height, $row3->uraian_indikator_program_renstra, 0, 'L', 0, 0);
                    PDF::MultiCell($w1[4], $height, number_format($row3->angka, 2, ',', '.'), 0, 'R', 0, 0);
                    PDF::MultiCell($w1[5], $height, '', 0, 'L', 0, 0);
                   
                    PDF::Ln();
                    $countrow =$countrow+ceil(strlen($row3->uraian_indikator_program_renstra)/67);
                    if ($countrow >= $totalrow) {
                        PDF::AddPage('L');
                        $countrow = 0;
                        for ($i = 0; $i < $num_headers; ++ $i) {
                            PDF::SetFont('helvetica', 'B', 8);
                            PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                        }
                        PDF::Ln();
                        $countrow ++;
                    }
                    // $fill=!$fill;
                }
                
                // $fill=!$fill;
            }
            }
        
        PDF::Cell(array_sum($w), 0, '', 'T');
        
        // ---------------------------------------------------------
        
        // close and output PDF document
        PDF::Output('KompilasiProgramPagu.pdf', 'I');
    }

    public function T_V_C_53()
    {
        $countrow = 0;
        $totalrow = 18;
        
        
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('SSH Kelompok');
        
        // set default header data
        PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 011', PDF_HEADER_STRING);
        
        // set header and footer fonts
        PDF::setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
            ));
        PDF::setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
            ));
        
        // set default monospaced font
        PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        PDF::SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
        
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
        PDF::AddPage('L');
        
        // column titles
        $header = array(
            'No',
            'Uraian',
            'Realisasi Tahun (n-3)',
            'Realisasi Tahun (n-2)',
            'Tahun Berjalan (n-1)',
            'Proyeksi /Target pada Tahun Rencana (n)',
            'Proyeksi/Target pada Tahun (n+1)'
        );
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0);
        // $font=PDF::addTTFfont('Elibyy\TCPDF\Facades\tahoma.ttf', 'TrueTypeUnicode', '', 32);
        // PDF::SetFont($font, 'B', 10);
        PDF::SetFont('helvetica', 'B', 10);
        
        // Header
        PDF::Cell('275', 5, 'PEMERINTAH DAERAH KABUPATEN PURWOREJO', 1, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Cell('275', 5, 'Realisasi dan Proyeksi/Target Pendapatan Pemerintah Daerah	', 1, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::SetFont('', 'B');
        PDF::SetFont('helvetica', 'B', 8);
        
        // Header Column
        
        $wh = array(
            15,
            110,
            30,
            30,
            30,
            30,
            30
        );
        $w = array(
            15,265
        );
        $w1 = array(
            15,
            5,
            105,
            30,
            30,
            30,
            30,
            30
        );
        
        $num_headers = count($header);
        for ($i = 0; $i < $num_headers; ++ $i) {
            PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
        }
        PDF::Ln();
        $countrow ++;
        // Color and font restoration
        
        PDF::SetFillColor(224, 235, 255);
        PDF::SetTextColor(0);
        PDF::SetFont('helvetica', '', 6);
        // Data
        $fill = 0;
        $RKPDTujuanSasaran= DB::select('select d.no_urut as no_tujuan,
c.no_urut as no_sasaran,
c.id_sasaran_rpjmd,
c.uraian_sasaran_rpjmd
from trx_rpjmd_sasaran c
inner join trx_rpjmd_tujuan d
on c.id_tujuan_rpjmd=d.id_tujuan_rpjmd
INNER JOIN trx_rpjmd_misi e
on d.id_misi_rpjmd=e.id_misi_rpjmd
where e.no_urut=98
union ALL
select d.no_urut as no_tujuan,c.no_urut as no_misi,c.id_sasaran_rpjmd,uraian_sasaran_rpjmd from trx_rpjmd_sasaran c
inner join trx_rpjmd_tujuan d
on c.id_tujuan_rpjmd=d.id_tujuan_rpjmd
INNER JOIN trx_rpjmd_misi e
on d.id_misi_rpjmd=e.id_misi_rpjmd
where e.no_urut=99
union ALL
select d.no_urut as no_tujuan,c.no_urut as no_misi,c.id_sasaran_rpjmd,uraian_sasaran_rpjmd from trx_rpjmd_sasaran c
inner join trx_rpjmd_tujuan d
on c.id_tujuan_rpjmd=d.id_tujuan_rpjmd
INNER JOIN trx_rpjmd_misi e
on d.id_misi_rpjmd=e.id_misi_rpjmd
where e.no_urut not in (98,99)');
        foreach($RKPDTujuanSasaran as $row) {
            PDF::SetFont('helvetica', 'B', 8);
            PDF::MultiCell($w[0], 10, '', 0, 'L', 0, 0);
            PDF::MultiCell($w[1], 10, $row->nm_unit, 0, 'L', 0, 0);
            PDF::MultiCell($w[2], 10, number_format($row->sub_total, 2, ',', '.'), 0, 'R', 0, 0);
            PDF::Ln();
            $countrow ++;
            if ($countrow >= $totalrow) {
                PDF::AddPage('L');
                $countrow = 0;
                for ($i = 0; $i < $num_headers; ++ $i) {
                    PDF::SetFont('helvetica', 'B', 8);
                    PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                }
                
                PDF::Ln();
                $countrow ++;
            }
            // $fill=!$fill;
            $program = DB::select('select DISTINCT
                 a.id_program_rpjmd,
                a.uraian_program_rpjmd,a.total_pagu
				from trx_rpjmd_program a
				inner join trx_rpjmd_program_urusan b
				on a.id_program_rpjmd=b.id_program_rpjmd
				inner join trx_rpjmd_program_indikator c
				on a.id_program_rpjmd=c.id_program_rpjmd
				inner join trx_rpjmd_program_pelaksana d
				on b.id_urbid_rpjmd=d.id_urbid_rpjmd
				inner join ref_unit e
				on d.id_unit=e.id_unit
				where e.id_unit = '.$row->id_unit);
            foreach ($program as $row2) {
                PDF::SetFont('helvetica', 'B', 6);
                PDF::MultiCell($w1[0], 10, '', 0, 'L', 0, 0);
                PDF::MultiCell($w1[1], 10, '', 0, 'L', 0, 0);
                PDF::MultiCell($w1[2], 10, $row2->uraian_program_rpjmd, 0, 'L', 0, 0);
                PDF::MultiCell($w1[3], 10, '', 0, 'L', 0, 0);
                PDF::MultiCell($w1[4], 10,  '', 0, 'L', 0, 0);
                PDF::MultiCell($w1[5], 10, number_format($row2->total_pagu, 2, ',', '.'), 0, 'R', 0, 0);
                PDF::Ln();
                $countrow ++;
                if ($countrow >= $totalrow) {
                    PDF::AddPage('L');
                    $countrow = 0;
                    for ($i = 0; $i < $num_headers; ++ $i) {
                        PDF::SetFont('helvetica', 'B', 8);
                        PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                    }
                    PDF::Ln();
                    $countrow ++;
                }
                $indikator = DB::select('select DISTINCT c.id_indikator,
                    c.uraian_indikator_program_rpjmd,
                    c.angka_awal_periode
					from trx_rpjmd_program a
					inner join trx_rpjmd_program_urusan b
					on a.id_program_rpjmd=b.id_program_rpjmd
					inner join trx_rpjmd_program_indikator c
					on a.id_program_rpjmd=c.id_program_rpjmd
					inner join trx_rpjmd_program_pelaksana d
					on b.id_urbid_rpjmd=d.id_urbid_rpjmd
					inner join ref_unit e
					on d.id_unit=e.id_unit
					where a.id_program_rpjmd = '.$row2->id_program_rpjmd.' and e.id_unit = '.$row->id_unit  );
                
                
                foreach ($indikator as $row3) {
                    PDF::SetFont('helvetica', '', 6);
                    PDF::MultiCell($w1[0], 10, '', 0, 'L', 0, 0);
                    PDF::MultiCell($w1[1], 10, '', 0, 'L', 0, 0);
                    PDF::MultiCell($w1[2], 10, '', 0, 'L', 0, 0);
                    PDF::MultiCell($w1[3], 10, $row3->uraian_indikator_program_rpjmd, 0, 'L', 0, 0);
                    PDF::MultiCell($w1[4], 10, number_format($row3->angka_awal_periode, 2, ',', '.'), 0, 'R', 0, 0);
                    PDF::MultiCell($w1[5], 10, '', 0, 'L', 0, 0);
                    
                    PDF::Ln();
                    $countrow ++;
                    if ($countrow >= $totalrow) {
                        PDF::AddPage('L');
                        $countrow = 0;
                        for ($i = 0; $i < $num_headers; ++ $i) {
                            PDF::SetFont('helvetica', 'B', 8);
                            PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                        }
                        PDF::Ln();
                        $countrow ++;
                    }
                    // $fill=!$fill;
                }
                
                // $fill=!$fill;
            }
        }
        
        PDF::Cell(array_sum($w), 0, '', 'T');
        
        // ---------------------------------------------------------
        
        // close and output PDF document
        PDF::Output('ProyeksiPendapatan.pdf', 'I');
    }
    
}

