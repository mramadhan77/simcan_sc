<?php
namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Response;
use Session;
use PDF;
use DB;
use Auth;
use CekAkses;
use App\Models\RefUnit;
use App\Http\Controllers\Laporan\TemplateReport as Template;

class CetakRkpdFinalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ///////////////////////////////////////////////// End TIDAK DIPAKAI/////////////////////////////////////////////////////////////////////////////////////////////
    public function PraRKA2($sub_unit, $tahun)
    {
        $countrow = 0;
        $totalrow = 37;
        $id_renja = 20;
        // $sub_unit=7;
        $nama_sub = "";
        $pagu_prog_peg = 0;
        $pagu_prog_bj = 0;
        $pagu_prog_mod = 0;
        $pagu_prog = 0;
        $pagu_prog_1 = 0;
        $pemda = Session::get('xPemda');
        
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('Pra RKA');
        
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
            'INDIKATOR'
        );
        $header2 = array(
            'SKPD/Program/Kegiatan',
            'Uraian Indikator',
            'Tolak Ukur',
            'Target Renstra',
            'Target Renja',
            'Status Indikator',
            'Pagu Renstra Program/Kegiatan',
            'Pagu Program/Kegiatan',
            'Status Program/Kegiatan'
        );
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0);
        PDF::SetLineStyle(array(
            'width' => 0.1,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0.1,
            'color' => array(
                0,
                0,
                0
            )
        ));
        PDF::SetFont('helvetica', '', 10);
        
        // Header
        $sub = DB::select('SELECT
	a.tahun_forum,
	g.kd_urusan,
	f.kd_bidang,
	e.kd_unit,
	d.kd_sub,
	d.nm_sub,
	e.nm_unit,
	g.nm_urusan,
	f.nm_bidang
FROM
	trx_rkpd_final_kegiatan_pd a
INNER JOIN trx_rkpd_final_program_pd b ON a.id_program_pd = b.id_program_pd
INNER JOIN trx_rkpd_final_pelaksana_pd c ON a.id_kegiatan_pd = c.id_kegiatan_pd
INNER JOIN ref_sub_unit d ON c.id_sub_unit = d.id_sub_unit
INNER JOIN ref_unit e ON d.id_unit = e.id_unit
INNER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
INNER JOIN ref_urusan g ON f.kd_urusan = g.kd_urusan
WHERE
	c.id_sub_unit =' . $sub_unit . ' and b.tahun_forum=' . $tahun . ' limit 1');
        foreach ($sub as $row) {
            $countrow ++;
            // $nama_keg=$row->uraian_kegiatan_renstra;
            PDF::SetFont('helvetica', 'B', 10);
            PDF::Cell('275', 5, $pemda, 'LRT', 0, 'C', 0);
            PDF::Ln();
            $countrow ++;
            PDF::SetFont('helvetica', 'B', 8);
            PDF::Cell('275', 5, 'Tahun Anggaran ' . $row->tahun_forum, 'LRB', 0, 'C', 0);
            PDF::Ln();
            $countrow ++;
            // PDF::SetFont('', 'B');
            PDF::SetFont('helvetica', 'B', 7);
            PDF::Cell('30', 5, 'Urusan Pemerintahan', 'LT', 0, 'L', 0);
            PDF::Cell('5', 5, ':', 'T', 0, 'L', 0);
            PDF::Cell('15', 5, $row->kd_urusan . '.' . $row->kd_bidang, 'T', 0, 'L', 0);
            PDF::Cell('225', 5, $row->nm_urusan . ' ' . $row->nm_bidang, 'RT', 0, 'L', 0);
            PDF::Ln();
            $countrow ++;
            PDF::Cell('30', 5, 'Perangkat Daerah', 'L', 0, 'L', 0);
            PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
            PDF::Cell('15', 5, $row->kd_urusan . '.' . $row->kd_bidang . '.' . $row->kd_unit, 0, 0, 'L', 0);
            PDF::Cell('225', 5, $row->nm_unit, 'R', 0, 'L', 0);
            PDF::Ln();
            $countrow ++;
            PDF::Cell('30', 5, 'Sub Perangkat Daerah', 'L', 0, 'L', 0);
            PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
            PDF::Cell('15', 5, $row->kd_urusan . '.' . $row->kd_bidang . '.' . $row->kd_unit . '.' . $row->kd_sub, 0, 0, 'L', 0);
            PDF::Cell('225', 5, $row->nm_sub, 'R', 0, 'L', 0);
            PDF::Ln();
            $countrow ++;
            // PDF::Cell('30', 5, 'Program', 'L', 0, 'L', 0);
            // PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
            // PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_unit.'.'.$row->kd_sub.'.'.$row->no_urut_pro, 0, 0, 'L', 0);
            // PDF::Cell('135', 5,$row->uraian_program_renstra, 'R', 0, 'L', 0);
            // PDF::Ln();
            // PDF::Cell('30', 5, 'Kegiatan', 'LB', 0, 'L', 0);
            // PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
            // PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_unit.'.'.$row->kd_sub.'.'.$row->no_urut_pro.'.'.$row->no_urut_keg, 'B', 0, 'L', 0);
            // PDF::Cell('135', 5,$row->uraian_kegiatan_renstra, 'RB', 0, 'L', 0);
            // PDF::Ln();
            $nama_sub = $nama_sub . $row->nm_sub;
        }
        
        // Header Column///////////////////////////////////////////////////////////////////////////////////////////////////////////
        PDF::Cell('275', 5, 'REKAPITULASI BELANJA LANGSUNG MENURUT PROGRAM DAN PER KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
        PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
        PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
        PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
        PDF::Cell('112', 5, 'JUMLAH', 'LB', 0, 'C', 0);
        PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
        PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
        PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
        PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
        PDF::Cell('112', 5, 'Tahun n', 'LB', 0, 'C', 0);
        PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::MultiCell('25', 15, 'KODE', 'LB', 'C', 0, 0);
        PDF::MultiCell('70', 15, 'URAIAN / INDIKATOR', 'LB', 'C', 0, 0);
        PDF::MultiCell('20', 15, 'LOKASI', 'LB', 'C', 0, 0);
        PDF::MultiCell('20', 15, 'TARGET KINERJA (KUANTITATIF)', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 15, 'Blj. Pegawai', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 15, 'Blj. Barang & Jasa ', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 15, 'Blj. Modal', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 15, 'Jumlah', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 15, 'Tahun n+1', 'LRB', 'C', 0, 0);
        PDF::Ln();
        $countrow = $countrow + 1;
        
        PDF::MultiCell('25', 5, '1', 'LB', 'C', 0, 0);
        PDF::MultiCell('70', 5, '2', 'LB', 'C', 0, 0);
        PDF::MultiCell('20', 5, '3', 'LB', 'C', 0, 0);
        PDF::MultiCell('20', 5, '4', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 5, '5', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 5, '6 ', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 5, '7', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 5, '8 = 5+6+7', 'LB', 'C', 0, 0);
        PDF::MultiCell('28', 5, '9', 'LRB', 'C', 0, 0);
        PDF::Ln();
        $countrow ++;
        
        // PDF::Cell('25', 5, '1', 'LB', 0, 'C', 0);
        // PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
        // PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
        // PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
        // PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
        // PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
        // PDF::Ln();
        
        // End Header Column/////////////////////////////////////////////////////////////////////////////////////////////////////
        $tahunn1 = $tahun + 1;
        $prog = DB::select('SELECT
	a. id_forum_program,
	a.kode,
	a.kd_program,
	a.uraian_program_renstra,
	sum(a.blj_peg) AS blj_peg,
	sum(a.blj_bj) AS blj_bj,
	sum(a.blj_modal) AS blj_modal,
	b.pagu_forum,
	a.pagu_tahun_program
FROM
	(
		SELECT
			a.id_program_pd as id_forum_program,
			CONCAT(
				o.kd_urusan,
				".",
				o.kd_bidang,
				"  ",
				g.kd_urusan,
				".",
				f.kd_bidang,
				".",
				e.kd_unit,
				".",
				d.kd_sub,
				" "
			) AS kode,
			n.kd_program,
			a.uraian_program_renstra,
			CASE m.kd_rek_3
		WHEN 1 THEN
			(i.jml_belanja_forum)
		ELSE
			0
		END AS blj_peg,
		CASE m.kd_rek_3
	WHEN 2 THEN
		(i.jml_belanja_forum)
	ELSE
		0
	END AS blj_bj,
	CASE m.kd_rek_3
WHEN 3 THEN
	(i.jml_belanja_forum)
ELSE
	0
END AS blj_modal,
 m.kd_rek_3,
 p.pagu_tahun_program
FROM
	trx_rkpd_final_program_pd AS a
INNER JOIN trx_rkpd_final_kegiatan_pd AS b ON b.id_program_pd = a.id_program_pd
INNER JOIN trx_rkpd_final_pelaksana_pd AS c ON c.id_kegiatan_pd = b.id_kegiatan_pd
LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit = d.id_sub_unit
LEFT OUTER JOIN ref_unit e ON d.id_unit = e.id_unit
LEFT OUTER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
LEFT OUTER JOIN ref_urusan g ON f.kd_urusan = g.kd_urusan
INNER JOIN trx_rkpd_final_aktivitas_pd AS h ON c.id_pelaksana_pd = h.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd AS i ON h.id_aktivitas_pd = i.id_aktivitas_pd
INNER JOIN ref_ssh_tarif j ON i.id_item_ssh = j.id_tarif_ssh
LEFT OUTER JOIN ref_rek_5 k ON i.id_rekening_ssh = k.id_rekening
LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4 = l.kd_rek_4
AND k.kd_rek_3 = l.kd_rek_3
AND k.kd_rek_2 = l.kd_rek_2
AND k.kd_rek_1 = l.kd_rek_1
LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3 = m.kd_rek_3
AND l.kd_rek_2 = m.kd_rek_2
AND l.kd_rek_1 = m.kd_rek_1
INNER JOIN ref_program n ON a.id_program_ref = n.id_program
INNER JOIN ref_bidang o ON o.id_bidang = n.id_bidang
LEFT OUTER JOIN (
	SELECT
		id_program_renstra,
		pagu_tahun_program
	FROM
		trx_rkpd_renstra
	WHERE
		tahun_rkpd =' . $tahunn1 . ' GROUP BY
		id_program_renstra,
		pagu_tahun_program
) p ON a.id_program_renstra = p.id_program_renstra
WHERE
	c.id_sub_unit =' . $sub_unit . '   AND k.kd_rek_1 = 5
AND k.kd_rek_2 = 2
AND a.tahun_forum = ' . $tahun . '
 ) a
INNER JOIN trx_rkpd_final_program_pd b ON a.id_forum_program = b.id_program_pd
GROUP BY
	a.id_forum_program,
	a.kode,
	a.kd_program,
	a.uraian_program_renstra,
	b.pagu_forum,
	a.pagu_tahun_program
');
        foreach ($prog as $row) {
            $pagu_prog = $pagu_prog + $row->blj_peg + $row->blj_bj + $row->blj_modal;
            $pagu_prog_peg = $pagu_prog_peg + $row->blj_peg;
            $pagu_prog_bj = $pagu_prog_bj + $row->blj_bj;
            $pagu_prog_mod = $pagu_prog_mod + $row->blj_modal;
            $pagu_prog_1 = $pagu_prog_1 + $row->pagu_tahun_program;
            PDF::SetFont('helvetica', 'B', 7);
            $height = ceil((strlen($row->uraian_program_renstra) / 51)) * 4;
            $kode = "";
            if (strlen($row->kd_program) == 2) {
                $kode = $row->kode . $row->kd_program;
            } else {
                $kode = $row->kode . '0' . $row->kd_program;
            }
            $countrow = $countrow + $height / 4;
            
            if ($countrow >= $totalrow) {
                PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                PDF::AddPage('L');
                $countrow = 0;
                
                PDF::Cell('275', 5, 'REKAPITULASI BELANJA LANGSUNG MENURUT PROGRAM DAN PER KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                PDF::Ln();
                PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('112', 5, 'JUMLAH', 'LB', 0, 'C', 0);
                PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                PDF::Ln();
                $countrow ++;
                PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('112', 5, 'Tahun n', 'LB', 0, 'C', 0);
                PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                PDF::Ln();
                $countrow ++;
                PDF::MultiCell('25', 15, 'KODE', 'LB', 'C', 0, 0);
                PDF::MultiCell('70', 15, 'URAIAN / INDIKATOR', 'LB', 'C', 0, 0);
                PDF::MultiCell('20', 15, 'LOKASI', 'LB', 'C', 0, 0);
                PDF::MultiCell('20', 15, 'TARGET KINERJA (KUANTITATIF)', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 15, 'Blj. Pegawai', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 15, 'Blj. Barang & Jasa ', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 15, 'Blj. Modal', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 15, 'Jumlah', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 15, 'Tahun n+1', 'LRB', 'C', 0, 0);
                PDF::Ln();
                $countrow = $countrow + 3;
                PDF::MultiCell('25', 5, '1', 'LB', 'C', 0, 0);
                PDF::MultiCell('70', 5, '2', 'LB', 'C', 0, 0);
                PDF::MultiCell('20', 5, '3', 'LB', 'C', 0, 0);
                PDF::MultiCell('20', 5, '4', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 5, '5', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 5, '6 ', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 5, '7', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 5, '8 = 5+6+7', 'LB', 'C', 0, 0);
                PDF::MultiCell('28', 5, '9', 'LRB', 'C', 0, 0);
                PDF::Ln();
                $countrow ++;
            }
            PDF::MultiCell('25', $height, $kode, 'L', 'L', 0, 0);
            PDF::MultiCell('70', $height, $row->uraian_program_renstra, 'L', 'L', 0, 0);
            PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
            PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
            PDF::MultiCell('28', $height, number_format($row->blj_peg, 2, ',', '.'), 'LBT', 'R', 0, 0);
            PDF::MultiCell('28', $height, number_format($row->blj_bj, 2, ',', '.'), 'LBT', 'R', 0, 0);
            PDF::MultiCell('28', $height, number_format($row->blj_modal, 2, ',', '.'), 'LBT', 'R', 0, 0);
            PDF::MultiCell('28', $height, number_format($row->blj_peg + $row->blj_bj + $row->blj_modal, 2, ',', '.'), 'LBT', 'R', 0, 0);
            PDF::MultiCell('28', $height, number_format($row->pagu_tahun_program, 2, ',', '.'), 1, 'R', 0, 0);
            PDF::Ln();
            $indikatorprog = DB::select('SELECT DISTINCT

d.uraian_program_renstra,
	b.uraian_indikator_program,
	b.tolok_ukur_indikator,
	b.target_renstra,
	b.target_renja,
	f.singkatan_satuan
FROM
	trx_rkpd_final_program_pd d
INNER JOIN trx_rkpd_final_prog_indikator_pd b ON d.id_program_pd = b.id_program_pd
INNER JOIN trx_rkpd_final_kegiatan_pd g ON d.id_program_pd = g.id_program_pd
INNER JOIN trx_rkpd_final_pelaksana_pd h ON g.id_kegiatan_pd = h.id_kegiatan_pd
LEFT OUTER JOIN ref_satuan f ON b.id_satuan_ouput = f.id_satuan
WHERE
	h.id_sub_unit =' . $sub_unit . ' AND d.id_forum_program = ' . $row->id_forum_program);
            
            foreach ($indikatorprog as $row3) {
                PDF::SetFont('helvetica', 'B', 7);
                $height = ceil((strlen($row3->uraian_indikator_program) / 49)) * 4;
                $kode = "";
                if (strlen($row->kd_program) == 2) {
                    $kode = $row->kode . $row->kd_program;
                } else {
                    $kode = $row->kode . '0' . $row->kd_program;
                }
                $countrow = $countrow + $height / 4;
                
                if ($countrow >= $totalrow) {
                    PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                    PDF::AddPage('L');
                    $countrow = 0;
                    
                    PDF::Cell('275', 5, 'REKAPITULASI BELANJA LANGSUNG MENURUT PROGRAM DAN PER KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                    PDF::Ln();
                    PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('112', 5, 'JUMLAH', 'LB', 0, 'C', 0);
                    PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                    PDF::Ln();
                    $countrow ++;
                    PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('112', 5, 'Tahun n', 'LB', 0, 'C', 0);
                    PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                    PDF::Ln();
                    $countrow ++;
                    PDF::MultiCell('25', 15, 'KODE', 'LB', 'C', 0, 0);
                    PDF::MultiCell('70', 15, 'URAIAN / INDIKATOR', 'LB', 'C', 0, 0);
                    PDF::MultiCell('20', 15, 'LOKASI', 'LB', 'C', 0, 0);
                    PDF::MultiCell('20', 15, 'TARGET KINERJA (KUANTITATIF)', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Blj. Pegawai', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Blj. Barang & Jasa ', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Blj. Modal', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Jumlah', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Tahun n+1', 'LRB', 'C', 0, 0);
                    PDF::Ln();
                    $countrow = $countrow + 3;
                    PDF::MultiCell('25', 5, '1', 'LB', 'C', 0, 0);
                    PDF::MultiCell('70', 5, '2', 'LB', 'C', 0, 0);
                    PDF::MultiCell('20', 5, '3', 'LB', 'C', 0, 0);
                    PDF::MultiCell('20', 5, '4', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '5', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '6 ', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '7', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '8 = 5+6+7', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '9', 'LRB', 'C', 0, 0);
                    PDF::Ln();
                    $countrow ++;
                }
                PDF::MultiCell('25', $height, '', 'L', 'L', 0, 0);
                PDF::MultiCell('3', $height, '', 'L', 'L', 0, 0);
                PDF::MultiCell('67', $height, '- ' . $row3->uraian_indikator_program, 0, 'L', 0, 0);
                PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
                PDF::MultiCell('20', $height, $row3->target_renja . ' ' . $row3->singkatan_satuan, 'L', 'L', 0, 0);
                PDF::MultiCell('28', $height, '', 'LBT', 'R', 0, 0);
                PDF::MultiCell('28', $height, '', 'LBT', 'R', 0, 0);
                PDF::MultiCell('28', $height, '', 'LBT', 'R', 0, 0);
                PDF::MultiCell('28', $height, '', 'LBT', 'R', 0, 0);
                PDF::MultiCell('28', $height, '', 1, 'R', 0, 0);
                PDF::Ln();
            }
            $keg = DB::select('SELECT
	a.id_forum_skpd,
	a.kd_kegiatan,
	a.uraian_kegiatan_forum,
	COALESCE (c.gablok, "belum ada") AS gablok,
	sum(a.blj_peg) AS blj_peg,
	sum(a.blj_bj) AS blj_bj,
	sum(a.blj_modal) AS blj_modal,
	b.pagu_forum,
	a.pagu_tahun_kegiatan
FROM
	(
		SELECT
			b.id_kegiatan_pd as id_forum_skpd,
			n.kd_kegiatan,
			b.uraian_kegiatan_forum,
			CASE m.kd_rek_3
		WHEN 1 THEN
			(i.jml_belanja_forum)
		ELSE
			0
		END AS blj_peg,
		CASE m.kd_rek_3
	WHEN 2 THEN
		(i.jml_belanja_forum)
	ELSE
		0
	END AS blj_bj,
	CASE m.kd_rek_3
WHEN 3 THEN
	(i.jml_belanja_forum)
ELSE
	0
END AS blj_modal,
 m.kd_rek_3,
 q.pagu_tahun_kegiatan
FROM
	trx_rkpd_final_kegiatan_pd b
INNER JOIN trx_rkpd_final_pelaksana_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit = d.id_sub_unit
LEFT OUTER JOIN ref_unit e ON d.id_unit = e.id_unit
LEFT OUTER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
LEFT OUTER JOIN ref_urusan g ON f.kd_urusan = g.kd_urusan
INNER JOIN trx_rkpd_final_aktivitas_pd h ON c.id_pelaksana_pd = h.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd i ON h.id_aktivitas_pd = i.id_belanja_pd
INNER JOIN ref_ssh_tarif j ON i.id_item_ssh = j.id_tarif_ssh
LEFT OUTER JOIN ref_rek_5 k ON i.id_rekening_ssh = k.id_rekening
LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4 = l.kd_rek_4
AND k.kd_rek_3 = l.kd_rek_3
AND k.kd_rek_2 = l.kd_rek_2
AND k.kd_rek_1 = l.kd_rek_1
LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3 = m.kd_rek_3
AND l.kd_rek_2 = m.kd_rek_2
AND l.kd_rek_1 = m.kd_rek_1
INNER JOIN ref_kegiatan n ON b.id_kegiatan_ref = n.id_kegiatan
INNER JOIN ref_program o ON o.id_program = n.id_program
INNER JOIN ref_bidang p ON o.id_bidang = p.id_bidang
LEFT OUTER JOIN (
	SELECT
		id_kegiatan_renstra,
		pagu_tahun_kegiatan
	FROM
		trx_rkpd_renstra
	WHERE
		tahun_rkpd = 2020
	GROUP BY
		id_kegiatan_renstra,
		pagu_tahun_kegiatan
) q ON b.id_kegiatan_renstra = q.id_kegiatan_renstra
WHERE
	b.id_program_pd =' . $row->id_forum_program . '   AND k.kd_rek_1 = 5
AND k.kd_rek_2 = 2
	) a
INNER JOIN trx_rkpd_final_kegiatan_pd b ON a.id_forum_skpd = b.id_kegiatan_pd
LEFT OUTER JOIN (
	SELECT
		GROUP_CONCAT(c.nama_lokasi) AS gablok,
		d.id_kegiatan_pd as id_forum_skpd
	FROM
		trx_rkpd_final_kegiatan_pd AS d 
INNER JOIN trx_rkpd_final_pelaksana_pd AS a ON d.id_kegiatan_pd = a.id_kegiatan_pd
	INNER JOIN trx_rkpd_final_aktivitas_pd AS b ON a.id_pelaksana_pd = b.id_pelaksana_pd
	LEFT OUTER JOIN trx_rkpd_final_lokasi_pd e ON b.id_aktivitas_pd = e.id_aktivitas_pd
	INNER JOIN ref_lokasi c ON e.id_lokasi = c.id_lokasi
	GROUP BY
		d.id_kegiatan_pd
) c ON b.id_forum_skpd = c.id_forum_skpd
GROUP BY
	a.id_forum_skpd,
	a.kd_kegiatan,
	a.uraian_kegiatan_forum,
	c.gablok,
	b.pagu_forum,
	a.pagu_tahun_kegiatan');
            foreach ($keg as $row2) {
                $height1 = ceil((strlen($row2->uraian_kegiatan_forum) / 54)) * 4;
                $height2 = ceil((strlen($row2->gablok) / 15)) * 4;
                $height = max($height1, $height2);
                PDF::SetFont('helvetica', '', 7);
                $kode2 = "";
                if (strlen($row2->kd_kegiatan) == 2) {
                    $kode2 = $kode . '.' . $row2->kd_kegiatan;
                } else {
                    $kode2 = $kode . '.0' . $row2->kd_kegiatan;
                }
                $countrow = $countrow + $height / 4;
                
                if ($countrow >= $totalrow) {
                    PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                    PDF::AddPage('L');
                    $countrow = 0;
                    PDF::Cell('275', 5, 'REKAPITULASI BELANJA LANGSUNG MENURUT PROGRAM DAN PER KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                    PDF::Ln();
                    PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('112', 5, 'JUMLAH', 'LB', 0, 'C', 0);
                    PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                    PDF::Ln();
                    $countrow ++;
                    PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('112', 5, 'Tahun n', 'LB', 0, 'C', 0);
                    PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                    PDF::Ln();
                    $countrow ++;
                    PDF::MultiCell('25', 15, 'KODE', 'LB', 'C', 0, 0);
                    PDF::MultiCell('70', 15, 'URAIAN / INDIKATOR', 'LB', 'C', 0, 0);
                    PDF::MultiCell('20', 15, 'LOKASI', 'LB', 'C', 0, 0);
                    PDF::MultiCell('20', 15, 'TARGET KINERJA (KUANTITATIF)', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Blj. Pegawai', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Blj. Barang & Jasa ', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Blj. Modal', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Jumlah', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 15, 'Tahun n+1', 'LRB', 'C', 0, 0);
                    PDF::Ln();
                    $countrow = $countrow + 3;
                    PDF::MultiCell('25', 5, '1', 'LB', 'C', 0, 0);
                    PDF::MultiCell('70', 5, '2', 'LB', 'C', 0, 0);
                    PDF::MultiCell('20', 5, '3', 'LB', 'C', 0, 0);
                    PDF::MultiCell('20', 5, '4', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '5', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '6 ', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '7', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '8 = 5+6+7', 'LB', 'C', 0, 0);
                    PDF::MultiCell('28', 5, '9', 'LRB', 'C', 0, 0);
                    PDF::Ln();
                    $countrow ++;
                }
                
                PDF::MultiCell('25', $height, $kode2, 'L', 'L', 0, 0);
                PDF::MultiCell('6', $height, '', 'L', 'L', 0, 0);
                PDF::MultiCell('64', $height, $row2->uraian_kegiatan_forum, '', 'L', 0, 0);
                PDF::MultiCell('20', $height, $row2->gablok, 'L', 'L', 0, 0);
                PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
                PDF::MultiCell('28', $height, number_format($row2->blj_peg, 2, ',', '.'), 'L', 'R', 0, 0);
                PDF::MultiCell('28', $height, number_format($row2->blj_bj, 2, ',', '.'), 'L', 'R', 0, 0);
                PDF::MultiCell('28', $height, number_format($row2->blj_modal, 2, ',', '.'), 'L', 'R', 0, 0);
                PDF::MultiCell('28', $height, number_format($row2->blj_peg + $row2->blj_bj + $row2->blj_modal, 2, ',', '.'), 'L', 'R', 0, 0);
                PDF::MultiCell('28', $height, number_format($row2->pagu_tahun_kegiatan, 2, ',', '.'), 'LR', 'R', 0, 0);
                PDF::Ln();
                $indikator = DB::select('select  distinct d.uraian_kegiatan_forum,b.uraian_indikator_kegiatan,
b.tolok_ukur_indikator,b.target_renstra,b.target_renja,f.singkatan_satuan,
case b.status_data when 1 then "Telah direview" else "Belum direview" end as status_indikator
from  trx_rkpd_final_kegiatan_pd d
inner JOIN trx_rkpd_final_keg_indikator_pd b
on d.id_kegiatan_pd=b.id_kegiatan_pd
inner join trx_rkpd_final_pelaksana_pd h on d.id_kegiatan_pd=h.id_kegiatan_pd
left outer join ref_satuan f
on b.id_satuan_ouput=f.id_satuan

 where h.id_sub_unit=' . $sub_unit . ' and d.id_program_pd=' . $row->id_forum_program . ' and d.id_kegiatan_pd=' . $row2->id_forum_skpd);
                foreach ($indikator as $row4) {
                    $height = ceil((strlen($row4->uraian_indikator_kegiatan) / 48)) * 4;
                    
                    PDF::SetFont('helvetica', '', 7);
                    $kode2 = "";
                    if (strlen($row2->kd_kegiatan) == 2) {
                        $kode2 = $kode . '.' . $row2->kd_kegiatan;
                    } else {
                        $kode2 = $kode . '.0' . $row2->kd_kegiatan;
                    }
                    $countrow = $countrow + $height / 4;
                    
                    if ($countrow >= $totalrow) {
                        PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                        PDF::AddPage('L');
                        $countrow = 0;
                        PDF::Cell('275', 5, 'REKAPITULASI BELANJA LANGSUNG MENURUT PROGRAM DAN PER KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                        PDF::Ln();
                        PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('112', 5, 'JUMLAH', 'LB', 0, 'C', 0);
                        PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                        PDF::Ln();
                        $countrow ++;
                        PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('112', 5, 'Tahun n', 'LB', 0, 'C', 0);
                        PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                        PDF::Ln();
                        $countrow ++;
                        PDF::MultiCell('25', 15, 'KODE', 'LB', 'C', 0, 0);
                        PDF::MultiCell('70', 15, 'URAIAN / INDIKATOR', 'LB', 'C', 0, 0);
                        PDF::MultiCell('20', 15, 'LOKASI', 'LB', 'C', 0, 0);
                        PDF::MultiCell('20', 15, 'TARGET KINERJA (KUANTITATIF)', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 15, 'Blj. Pegawai', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 15, 'Blj. Barang & Jasa ', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 15, 'Blj. Modal', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 15, 'Jumlah', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 15, 'Tahun n+1', 'LRB', 'C', 0, 0);
                        PDF::Ln();
                        $countrow = $countrow + 3;
                        PDF::MultiCell('25', 5, '1', 'LB', 'C', 0, 0);
                        PDF::MultiCell('70', 5, '2', 'LB', 'C', 0, 0);
                        PDF::MultiCell('20', 5, '3', 'LB', 'C', 0, 0);
                        PDF::MultiCell('20', 5, '4', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 5, '5', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 5, '6 ', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 5, '7', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 5, '8 = 5+6+7', 'LB', 'C', 0, 0);
                        PDF::MultiCell('28', 5, '9', 'LRB', 'C', 0, 0);
                        PDF::Ln();
                        $countrow ++;
                    }
                    
                    PDF::MultiCell('25', $height, '', 'L', 'L', 0, 0);
                    PDF::MultiCell('9', $height, '', 'L', 'L', 0, 0);
                    PDF::MultiCell('61', $height, '- ' . $row4->uraian_indikator_kegiatan, '', 'L', 0, 0);
                    PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
                    PDF::MultiCell('20', $height, $row4->target_renja . ' ' . $row4->singkatan_satuan, 'L', 'L', 0, 0);
                    PDF::MultiCell('28', $height, '', 'L', 'R', 0, 0);
                    PDF::MultiCell('28', $height, '', 'L', 'R', 0, 0);
                    PDF::MultiCell('28', $height, '', 'L', 'R', 0, 0);
                    PDF::MultiCell('28', $height, '', 'L', 'R', 0, 0);
                    PDF::MultiCell('28', $height, '', 'LR', 'R', 0, 0);
                    PDF::Ln();
                }
            }
        }
        PDF::SetFont('helvetica', 'B', 8);
        PDF::MultiCell('135', 7, 'Total : ', 'LT', 'R', 0, 0);
        PDF::MultiCell('28', 7, number_format($pagu_prog_peg, 2, ',', '.'), 'LT', 'R', 0, 0);
        PDF::MultiCell('28', 7, number_format($pagu_prog_bj, 2, ',', '.'), 'LT', 'R', 0, 0);
        PDF::MultiCell('28', 7, number_format($pagu_prog_mod, 2, ',', '.'), 'LT', 'R', 0, 0);
        PDF::MultiCell('28', 7, number_format($pagu_prog, 2, ',', '.'), 'LT', 'R', 0, 0);
        PDF::MultiCell('28', 7, number_format($pagu_prog_1, 2, ',', '.'), 1, 'R', 0, 0);
        PDF::Ln();
        $countrow ++;
        PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
        // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
        
        // close and output PDF document
        PDF::Output('PraRKA2-' . $nama_sub . '.pdf', 'I');
    }

    public function Apbd($tahun)
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
        $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">REKAPITULASI RENCANA PENDAPATAN DAN BELANJA  PERANGKAT DAERAH</div>';
        $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . $pemda . '</div>';
        $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Tahun Anggaran : ' . $tahun . '</div>';
        $html .= '<br>';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="7%" rowspan="3" style="padding: 50px; text-align: center; font-weight: bold;" >Kode</td>
                        <td width="15%" rowspan="3" style="padding: 50px; text-align: center; font-weight: bold;" >Perangkat Daerah</td>
                        <td width="13%" rowspan="3" style="padding: 50px; text-align: center; font-weight: bold;" >Pendapatan</td>
                        <td width="52%" colspan="4" style="padding: 50px; text-align: center; font-weight: bold;" >Belanja</td>
                        <td width="13%" rowspan="3" style="padding: 50px; text-align: center; font-weight: bold;" >Total Belanja</td>
                </tr>
                <tr height=19 >
                        <td rowspan="2" style="padding: 50px; text-align: center; font-weight: bold;">Belanja Tidak Langsung</td>
                        <td colspan="3" style="padding: 50px; text-align: center; font-weight: bold; ">Belanja Langsung</td>
                </tr>
                <tr height=19 >
                        <td style="padding: 50px; text-align: center; font-weight: bold;">Belanja Pegawai</td>
                        <td style="padding: 50px; text-align: center; font-weight: bold;">Belanja Barang  &amp; Jasa</td>
                        <td style="padding: 50px; text-align: center; font-weight: bold;">Belanja Modal</td>
                </tr>
            </thead>';
        
        $apbd = DB::SELECT('SELECT concat(a.kd_urusan,".",a.kd_bidang,".",a.kd_unit) AS kode,a.id_unit,a.nm_unit, sum(a.jml_pend) AS pend,
             sum(a.jml_btl) AS btl,
            sum(a.jml_peg) AS peg, sum(a.jml_bj) AS bj, sum(a.jml_mod) AS modal  FROM
            (SELECT f.kd_urusan,f.kd_bidang,e.kd_unit,e.id_unit,e.nm_unit,
            case o.kd_rek_1 when 4 then i.jml_belanja_forum else 0 end AS jml_pend,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 1 then i.jml_belanja_forum else 0 END
            else 0 end AS jml_btl,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 2 then
            		case m.kd_rek_3 when 1 then i.jml_belanja_forum else 0 end
            	else 0 END
            else 0 end AS jml_peg,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 2 then
            		case m.kd_rek_3 when 2 then i.jml_belanja_forum else 0 end
            	else 0 END
            else 0 end AS jml_bj,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 2 then
            		case m.kd_rek_3 when 3 then i.jml_belanja_forum else 0 end
            	else 0 END
            else 0 end AS jml_mod
	From trx_rkpd_final_program_pd AS a
INNER JOIN trx_rkpd_final_kegiatan_pd AS b ON b.id_program_pd = a.id_program_pd
INNER JOIN trx_rkpd_final_pelaksana_pd AS c ON c.id_kegiatan_pd = b.id_kegiatan_pd
LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit = d.id_sub_unit
LEFT OUTER JOIN ref_unit e ON d.id_unit = e.id_unit
LEFT OUTER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
LEFT OUTER JOIN ref_urusan g ON f.kd_urusan = g.kd_urusan
INNER JOIN trx_rkpd_final_aktivitas_pd AS h ON c.id_pelaksana_pd = h.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd AS i ON h.id_aktivitas_pd = i.id_aktivitas_pd
            INNER JOIN ref_ssh_tarif j ON i.id_item_ssh=j.id_tarif_ssh
            LEFT OUTER  join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
            LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1
            LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1
            LEFT OUTER JOIN ref_rek_2 n ON m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1
            LEFT OUTER JOIN ref_rek_1 o ON n.kd_rek_1=o.kd_rek_1
            WHERE a.tahun_forum=' . $tahun . ' and h.status_pelaksanaan=0 ) a GROUP BY a.kd_urusan,a.kd_bidang,a.kd_unit,a.id_unit,a.nm_unit  ');
        
        // header
        $html .= '<tbody>';
        foreach ($apbd as $row) {
            
            PDF::SetFont('helvetica', '', 9);
            $html .= '<tr nobr="true">';
            $html .= '<td width="7%" style="padding: 50px; text-align: justify;"><div>' . $row->kode . '</div></td>';
            $html .= '<td width="15%" style="padding: 50px; text-align: justify;"><div>' . $row->nm_unit . '</div></td>';
            $html .= '<td width="13%" style="padding: 50px; text-align: right;"><div>' . number_format($row->pend, 2, ',', '.') . '</div></td>';
            $html .= '<td width="13%" style="padding: 50px; text-align: right;"><div>' . number_format($row->btl, 2, ',', '.') . '</div></td>';
            $html .= '<td width="13%" style="padding: 50px; text-align: right;"><div>' . number_format($row->peg, 2, ',', '.') . '</div></td>';
            $html .= '<td width="13%" style="padding: 50px; text-align: right;"><div>' . number_format($row->bj, 2, ',', '.') . '</div></td>';
            $html .= '<td width="13%" style="padding: 50px; text-align: right;"><div>' . number_format($row->modal, 2, ',', '.') . '</div></td>';
            $html .= '<td width="13%" style="padding: 50px; text-align: right;"><div>' . number_format($row->btl + $row->peg + $row->bj + $row->modal, 2, ',', '.') . '</div></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>
                  </body>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        PDF::Output('Apbd-' . $pemda . '.pdf', 'I');
    }

    public function PraRKA($id_kegiatan, $sub_unit)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
        
        $nama_keg = "";
        
        // set document information
        // ----------
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        // add a page
        
        // column titles
        
        // Colors, line width AND bold font
        
        // Header
        $kegiatan = DB::SELECT('SELECT a.tahun_forum,g.kd_urusan,f.kd_bidang,e.kd_unit,d.kd_sub,h.kd_program AS no_urut_pro,i.kd_kegiatan AS no_urut_keg,
          b.uraian_kegiatan_forum,a.uraian_program_renstra, d.nm_sub, e.nm_unit,g.nm_urusan, f.nm_bidang
          FROM trx_rkpd_final_program_pd AS a
INNER JOIN trx_rkpd_final_kegiatan_pd AS b ON b.id_program_pd = a.id_program_pd
INNER JOIN trx_rkpd_final_pelaksana_pd AS c ON c.id_kegiatan_pd = b.id_kegiatan_pd

          INNER JOIN ref_sub_unit d on c.id_sub_unit=d.id_sub_unit
          INNER JOIN ref_unit e on d.id_unit=e.id_unit
          INNER JOIN ref_bidang f on e.id_bidang=f.id_bidang
          INNER JOIN ref_urusan g on f.kd_urusan=g.kd_urusan
          INNER JOIN ref_program h on a.id_program_ref=h.id_program
          INNER JOIN ref_kegiatan i on b.id_kegiatan_ref=i.id_kegiatan
          where b.id_kegiatan_pd=' . $id_kegiatan . ' AND c.id_sub_unit=' . $sub_unit);
        
        foreach ($kegiatan as $row) {
            $html = '';
            $html .= '<html>';
            $html .= '<head>';
            $html .= '<style>
                    td, th {
                    }
                </style>';
            $html .= '</head>';
            $html .= '<body>';
            PDF::SetFont('helvetica', 'B', 9);
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
            
            $html .= '<div style="text-align: center; font-size:9px; font-weight: bold;"> Tahun Anggaran : ' . $row->tahun_forum . '</div>';
            $html .= '</table>';
            
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
            
            PDF::SetFont('helvetica', '', 8);
            $html .= '<table border="0" cellpadding="4" cellspacing="0">';
            $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Urusan Pemerintahan</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="13%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_urusan . '.' . $row->kd_bidang . '</td>
                        <td width="63%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->nm_urusan . ' ' . $row->nm_bidang . '</td>
                        
                </tr>';
            $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Perangkat Daerah</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="13%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_urusan . '.' . $row->kd_bidang . '.' . $row->kd_unit . '</td>
                        <td width="63%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->nm_unit . '</td>
                            
                </tr>';
            $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Sub Perangkat Daerah</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="13%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_urusan . '.' . $row->kd_bidang . '.' . $row->kd_unit . '.' . $row->kd_sub . '</td>
                        <td width="63%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->nm_sub . '</td>
                            
                </tr>';
            $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Program</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="13%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_urusan . '.' . $row->kd_bidang . '.' . $row->kd_unit . '.' . $row->kd_sub . '.' . $row->no_urut_pro . '</td>
                        <td width="63%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->uraian_program_renstra . '</td>
                            
                </tr>';
            $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Kegiatan</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="13%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_urusan . '.' . $row->kd_bidang . '.' . $row->kd_unit . '.' . $row->kd_sub . '.' . $row->no_urut_pro . '.' . $row->no_urut_keg . '</td>
                        <td width="63%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->uraian_kegiatan_forum . '</td>
                            
                </tr>';
            $html .= '</table>';
            
            $html .= '</table>';
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
            
            PDF::SetFont('helvetica', '', 8);
            $html .= '<table border="0" cellpadding="4" cellspacing="0">';
            $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Lokasi</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>';
            
            $lokasi = DB::SELECT('SELECT a.uraian_kegiatan_forum,e.nama_lokasi
					FROM trx_rkpd_final_kegiatan_pd AS a
INNER JOIN trx_rkpd_final_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
INNER JOIN trx_rkpd_final_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd

INNER JOIN trx_rkpd_final_lokasi_pd AS d ON d.id_aktivitas_pd = c.id_aktivitas_pd
          INNER JOIN ref_lokasi e on d.id_lokasi=e.id_lokasi
          where a.id_kegiatan_pd=' . $id_kegiatan . ' AND b.id_sub_unit=' . $sub_unit);
            $c = 0;
            $gablok = "";
            foreach ($lokasi as $row) {
                if ($c == 0) {
                    $gablok = $gablok . '' . $row->nama_lokasi;
                } else {
                    $gablok = $gablok . ', ' . $row->nama_lokasi;
                }
                $c = $c + 1;
            }
            $html .= '<td width="76%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $gablok . '</td>
                </tr>';
            $html .= '</table>';
            
            $html .= '</table>';
            $pagu = DB::SELECT('SELECT a.tahun_forum-c.tahun_rkpd AS selisih,c.tahun_rkpd,c.pagu_tahun_kegiatan,a.pagu_tahun_kegiatan AS pagu_n
            FROM trx_rkpd_final_kegiatan_pd a
            INNER JOIN trx_rkpd_renstra b ON a.id_rkpd_renstra=b.id_rkpd_renstra
            INNER JOIN trx_rkpd_renstra c ON b.id_kegiatan_renstra=c.id_kegiatan_renstra
            WHERE a.id_kegiatan_pd=' . $id_kegiatan . ' AND (a.tahun_forum-c.tahun_rkpd in (-1,0,1)) order by c.tahun_rkpd ASC ');
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
            
            PDF::SetFont('helvetica', '', 8);
            $html .= '<table border="0" cellpadding="4" cellspacing="0">';
            
            foreach ($pagu as $row) {
                if ($pagu > 0) {
                    if ($row->selisih == 1) {
                        $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Jumlah Tahun n-1</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="27%"   style="padding: 50px; font-size:8px; text-align: right;" >Rp.' . number_format($row->pagu_tahun_kegiatan, 2, ',', '.') . '</td>
                        <td width="49%"   style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        </tr>';
                    } else if ($row->selisih == 0) {
                        $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Jumlah Tahun n</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="27%"   style="padding: 50px; font-size:8px; text-align: right;" >Rp.' . number_format($row->pagu_n, 2, ',', '.') . '</td>
                        <td width="49%"   style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        </tr>';
                    } else if ($row->selisih == - 1) {
                        $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Jumlah Tahun n+1</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="27%"   style="padding: 50px; font-size:8px; text-align: right;" >Rp.' . number_format($row->pagu_tahun_kegiatan, 2, ',', '.') . '</td>
                        <td width="49%"   style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        </tr>';
                    }
                }
            }
            $html .= '</table>';
            
            $html .= '</table>';
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
            $html .= '<tr height=19>
                        <td width="100%"  style="padding: 50px; font-size:8px; text-align: center;" >INDIKATOR DAN TOLOK UKUR KINERJA BELANJA LANGSUNG</td>
                        </tr>';
            PDF::SetFont('helvetica', '', 8);
            $html .= '</table>';
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
            $html .= '<tr height=19>
                        <td width="22%"  style="padding: 50px; font-size:8px; text-align: center;" >INDIKATOR</td>
                        <td width="48%"  style="padding: 50px; font-size:8px; text-align: center;" >TOLOK UKUR KINERJA</td>
                        <td width="30%"  style="padding: 50px; font-size:8px; text-align: center;" >TARGET KINERJA</td>
                        </tr>';
            PDF::SetFont('helvetica', '', 8);
            $html .= '</table>';
            $pagu2 = DB::SELECT('SELECT a.pagu_tahun_kegiatan FROM trx_rkpd_final_kegiatan_pd a WHERE a.id_kegiatan_pd=' . $id_kegiatan);
            foreach ($pagu2 as $row) {
                $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
                $html .= '<tr height=19>
                        <td width="22%"  style="padding: 50px; font-size:8px; text-align: left;" >MASUKAN</td>
                        <td width="48%"  style="padding: 50px; font-size:8px; text-align: left;" >Jumlah Dana</td>
                        <td width="30%"  style="padding: 50px; font-size:8px; text-align: left;" >Rp.' . number_format($row->pagu_tahun_kegiatan, 2, ',', '.') . '</td>
                        </tr>';
                PDF::SetFont('helvetica', '', 8);
                $html .= '</table>';
            }
            $ind = DB::SELECT('SELECT c.nm_indikator,b.target_renja, d.uraian_satuan FROM trx_rkpd_final_kegiatan_pd a
                INNER JOIN trx_rkpd_final_keg_indikator_pd b ON a.id_kegiatan_pd=b.id_kegiatan_pd
                left outer join ref_indikator c on b.kd_indikator=c.id_indikator
                left outer join ref_satuan d on b.id_satuan_ouput=d.id_satuan
                WHERE a.id_kegiatan_pd=' . $id_kegiatan);
            
            $c = 0;
            foreach ($ind as $row) {
                $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
                if ($c == 0) {
                    $html .= '<tr height=19>
                        <td width="22%"  style="padding: 50px; font-size:8px; text-align: left;" >KELUARAN</td>
                        <td width="48%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->nm_indikator . '</td>
                        <td width="30%"  style="padding: 50px; font-size:8px; text-align: left;" >' . number_format($row->target_renja, 2, ',', '.') . ' ' . $row->uraian_satuan . '</td>
                        </tr>';
                } else {
                    $html .= '<tr height=19>
                        <td width="22%"  style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        <td width="48%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->nm_indikator . '</td>
                        <td width="30%"  style="padding: 50px; font-size:8px; text-align: left;" >' . number_format($row->target_renja, 2, ',', '.') . ' ' . $row->uraian_satuan . '</td>
                        </tr>';
                }
                PDF::SetFont('helvetica', '', 8);
                $html .= '</table>';
                $c ++;
            }
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
            $html .= '<tr height=19>
                        <td width="100%"  style="padding: 50px; font-size:8px; text-align: center;" >RINCIAN BELANJA MENURUT PROGRAM DAN KEGIATAN PERANGKAT DAERAH</td>
                        </tr>';
            PDF::SetFont('helvetica', '', 8);
            $html .= '</table>';
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
            $html .= '<thead>
                        <tr height=15  nobr="true">
                        <td width="10%" rowspan="2" style="padding: 50px; font-size:8px; text-align: center;" >KODE REKENING</td>
                        <td width="40%" rowspan="2" style="padding: 50px; font-size:8px; text-align: center;" >URAIAN</td>
                        <td width="35%" colspan="3" style="padding: 50px; font-size:8px; text-align: center;" >RINCIAN PERHITUNGAN</td>
                        <td width="15%" rowspan="2" style="padding: 50px; font-size:8px; text-align: center;" >JUMLAH (Rp.)</td>
                        </tr>';
            $html .= '<tr height=15  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >VOLUME</td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >SATUAN</td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" >HARGA (Rp.)</td>
                        </tr>';
            $html .= '<tr height=14  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >1</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: center;" >2</td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >3</td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >4</td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" >5</td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" >6</td>
                        </tr>';
            PDF::SetFont('helvetica', '', 8);
            $html .= '</thead>
                             <tbody>';
            
            $rek1 = DB::SELECT('SELECT
	k.kd_rek_1,
	k.nama_kd_rek_1,
	sum(e.jml_belanja_forum) AS jumlah
FROM
	trx_rkpd_final_kegiatan_pd AS a
INNER JOIN trx_rkpd_final_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
INNER JOIN trx_rkpd_final_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd
INNER JOIN ref_ssh_tarif f ON e.id_item_ssh = f.id_tarif_ssh
LEFT OUTER JOIN ref_rek_5 g ON e.id_rekening_ssh = g.id_rekening
LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4 = h.kd_rek_4
AND g.kd_rek_3 = h.kd_rek_3
AND g.kd_rek_2 = h.kd_rek_2
AND g.kd_rek_1 = h.kd_rek_1
LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3 = i.kd_rek_3
AND h.kd_rek_2 = i.kd_rek_2
AND h.kd_rek_1 = i.kd_rek_1
LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2 = j.kd_rek_2
AND i.kd_rek_1 = j.kd_rek_1
LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1 = k.kd_rek_1

          where  a.id_kegiatan_pd=' . $id_kegiatan . ' AND b.id_sub_unit=' . $sub_unit . ' GROUP BY k.kd_rek_1,k.nama_kd_rek_1');
            foreach ($rek1 as $row) {
                $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->nama_kd_rek_1 . '</td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                $rek2 = DB::SELECT('SELECT j.kd_rek_2,j.nama_kd_rek_2,sum(e.jml_belanja_forum) AS jumlah
                    FROM 	trx_rkpd_final_kegiatan_pd AS a
INNER JOIN trx_rkpd_final_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
INNER JOIN trx_rkpd_final_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd
                    INNER JOIN ref_ssh_tarif f on e.id_item_ssh=f.id_tarif_ssh
                    LEFT OUTER join ref_rek_5 g on e.id_rekening_ssh=g.id_rekening
                    LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                    LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                    LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                    LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
                    
                    where a.id_kegiatan_pd=' . $id_kegiatan . ' AND b.id_sub_unit=' . $sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' GROUP BY j.kd_rek_2,j.nama_kd_rek_2');
                foreach ($rek2 as $row2) {
                    $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '.' . $row2->kd_rek_2 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;"><table border="0" cellpadding="0" cellspacing="0"><tr><td width="5%"></td>  <td width="95%">' . $row2->nama_kd_rek_2 . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row2->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                    $rek3 = DB::SELECT('SELECT i.kd_rek_3,i.nama_kd_rek_3,sum(e.jml_belanja_forum) AS jumlah
                        FROM 	trx_rkpd_final_kegiatan_pd AS a
INNER JOIN trx_rkpd_final_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
INNER JOIN trx_rkpd_final_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd

                        INNER JOIN ref_ssh_tarif f on e.id_item_ssh=f.id_tarif_ssh
                        LEFT OUTER join ref_rek_5 g on e.id_rekening_ssh=g.id_rekening
                        LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                        LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                        LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                        LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
                        where a.id_kegiatan_pd=' . $id_kegiatan . ' AND b.id_sub_unit=' . $sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . ' GROUP BY i.kd_rek_3,i.nama_kd_rek_3');
                    foreach ($rek3 as $row3) {
                        $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '.' . $row2->kd_rek_2 . '.' . $row3->kd_rek_3 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="10%"></td>  <td width="90%">' . $row3->nama_kd_rek_3 . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row3->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                        $rek4 = DB::SELECT('SELECT h.kd_rek_4,h.nama_kd_rek_4,sum(e.jml_belanja_forum) AS jumlah
                            FROM 	trx_rkpd_final_kegiatan_pd AS a
INNER JOIN trx_rkpd_final_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
INNER JOIN trx_rkpd_final_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd

                            INNER JOIN ref_ssh_tarif f on e.id_item_ssh=f.id_tarif_ssh
                            LEFT OUTER join ref_rek_5 g on e.id_rekening_ssh=g.id_rekening
                            LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                            LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                            LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                            LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
                            WHERE a.id_kegiatan_pd=' . $id_kegiatan . ' AND b.id_sub_unit=' . $sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . ' AND i.kd_rek_3=' . $row3->kd_rek_3 . ' GROUP BY h.kd_rek_4,h.nama_kd_rek_4');
                        foreach ($rek4 as $row4) {
                            $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '.' . $row2->kd_rek_2 . '.' . $row3->kd_rek_3 . '.' . $row4->kd_rek_4 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="15%"></td>  <td width="85%">' . $row4->nama_kd_rek_4 . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row4->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                            $rek5 = DB::SELECT('SELECT g.kd_rek_5, g.nama_kd_rek_5, sum(e.jml_belanja_forum) AS jumlah
                            FROM 	trx_rkpd_final_kegiatan_pd AS a
INNER JOIN trx_rkpd_final_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
INNER JOIN trx_rkpd_final_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd

                            INNER JOIN ref_ssh_tarif f on e.id_item_ssh=f.id_tarif_ssh
                            LEFT OUTER join ref_rek_5 g on e.id_rekening_ssh=g.id_rekening
                            LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                            LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                            LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                            LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
                            WHERE a.id_kegiatan_pd=' . $id_kegiatan . ' AND b.id_sub_unit=' . $sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . ' AND i.kd_rek_3=' . $row3->kd_rek_3 . ' AND h.kd_rek_4=' . $row4->kd_rek_4 . ' GROUP BY g.kd_rek_5,g.nama_kd_rek_5');
                            foreach ($rek5 as $row5) {
                                $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '.' . $row2->kd_rek_2 . '.' . $row3->kd_rek_3 . '.' . $row4->kd_rek_4 . '.' . $row5->kd_rek_5 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="20%"></td>  <td width="80%">' . $row5->nama_kd_rek_5 . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row5->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                                $akt = DB::SELECT('SELECT c.id_aktivitas_pd as id_aktivitas_forum,c.uraian_aktivitas_kegiatan,sum(e.jml_belanja_forum) AS jumlah
                                FROM 	trx_rkpd_final_kegiatan_pd AS a
INNER JOIN trx_rkpd_final_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
INNER JOIN trx_rkpd_final_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd

                                INNER JOIN ref_ssh_tarif f on e.id_item_ssh=f.id_tarif_ssh
                                LEFT OUTER join ref_rek_5 g on e.id_rekening_ssh=g.id_rekening
                                LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                                LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                                LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                                LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
                                where a.id_kegiatan_pd=' . $id_kegiatan . ' AND b.id_sub_unit=' . $sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . ' AND i.kd_rek_3=' . $row3->kd_rek_3 . ' AND h.kd_rek_4=' . $row4->kd_rek_4 . ' AND g.kd_rek_5=' . $row5->kd_rek_5 . ' GROUP BY c.id_aktivitas_pd, c.uraian_aktivitas_kegiatan');
                                foreach ($akt as $row6) {
                                    $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="25%"></td>  <td width="75%">' . $row6->uraian_aktivitas_kegiatan . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row6->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                                    $belanja = DB::SELECT(' SELECT e.id_rekening_ssh,a.uraian_kegiatan_forum,c.uraian_aktivitas_kegiatan,
                                    CONCAT(GantiEnter(f.uraian_tarif_ssh),COALESCE(f.keterangan_tarif_ssh,CONCAT(" - ",f.keterangan_tarif_ssh),"")) AS uraian_tarif_ssh
                                    , e.volume_1_forum as volume_1,m.uraian_satuan AS satuan1
                                    ,e.volume_2_forum as volume_2,n.uraian_satuan AS satuan2
                                    ,e.harga_satuan_forum as harga_satuan, e.jml_belanja_forum as jml_belanja
                                    FROM 	trx_rkpd_final_kegiatan_pd AS a
INNER JOIN trx_rkpd_final_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
INNER JOIN trx_rkpd_final_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
INNER JOIN trx_rkpd_final_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd

                                    INNER JOIN ref_ssh_tarif f on e.id_item_ssh=f.id_tarif_ssh
                                    LEFT OUTER join ref_rek_5 g on e.id_rekening_ssh=g.id_rekening
                                    LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                                    LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                                    LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                                    LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1 LEFT OUTER JOIN ref_satuan m ON e.id_satuan_1_forum=m.id_satuan
                                    LEFT OUTER JOIN ref_satuan n ON e.id_satuan_2_forum=n.id_satuan
                                    where a.id_kegiatan_pd=' . $id_kegiatan . ' AND b.id_sub_unit=' . $sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . '
                                    AND i.kd_rek_3=' . $row3->kd_rek_3 . ' AND h.kd_rek_4=' . $row4->kd_rek_4 . ' AND g.kd_rek_5=' . $row5->kd_rek_5 . ' AND e.id_aktivitas_pd =' . $row6->id_aktivitas_forum);
                                    foreach ($belanja as $row7) {
                                        if ($row7->satuan2 > 0) {
                                            $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="30%"></td>  <td width="70%"> ' . $row7->uraian_tarif_ssh . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >' . number_format($row7->volume_1 * $row7->volume_2, 2, ',', '.') . '</td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >' . $row7->satuan1 . ' x ' . $row7->satuan2 . '</td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" >' . number_format($row7->harga_satuan, 2, ',', '.') . '</td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row7->jml_belanja, 2, ',', '.') . '</td>
                        </tr>';
                                        } else {
                                            $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="30%"></td>  <td width="70%"> ' . $row7->uraian_tarif_ssh . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >' . number_format($row7->volume_1 * $row7->volume_2, 2, ',', '.') . '</td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >' . $row7->satuan1 . '</td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" >' . number_format($row7->harga_satuan, 2, ',', '.') . '</td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row7->jml_belanja, 2, ',', '.') . '</td>
                        </tr>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            $html .= '</tbody>
                     </table>
                    </body>
                   </html>  ';
            
            $html .= '</body>
            </html>';
        }
        
        // close AND output PDF document
        // $template = new TemplateReport();
        
        PDF::writeHTML($html, true, false, true, false, '');
        
        Template::footerLandscape();
        PDF::Output('PraRKA-' . $nama_keg . '.pdf', 'I');
    }

    public function RingkasApbd($tahun)
    {
        $countrow = 0;
        $totalrow = 55;
        $id_renja = 20;
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
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('Pra RKA');
        
        // set default header data
        PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 011', PDF_HEADER_STRING);
        
        // set header AND footer fonts
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
        PDF::AddPage('P');
        
        // column titles
        $header = array(
            'INDIKATOR'
        );
        $header2 = array(
            'SKPD/Program/Kegiatan',
            'Uraian Indikator',
            'Tolak Ukur',
            'Target Renstra',
            'Target Renja',
            'Status Indikator',
            'Pagu Renstra Program/Kegiatan',
            'Pagu Program/Kegiatan',
            'Status Program/Kegiatan'
        );
        // $tahun=DB::SELECT('SELECT tahun_renja FROM trx_renja_rancangan_program
        // GROUP BY tahun_renja');
        // Colors, line width AND bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0);
        PDF::SetLineStyle(array(
            'width' => 0.1,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0.1,
            'color' => array(
                0,
                0,
                0
            )
        ));
        PDF::SetFont('helvetica', '', 10);
        // foreach($tahun AS $row)
        // {
        PDF::SetFont('helvetica', 'B', 10);
        PDF::Cell('185', 5, $pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::SetFont('helvetica', 'B', 8);
        PDF::Cell('185', 5, 'Tahun Anggaran : ' . $tahun, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::SetFont('helvetica', 'B', 9);
        PDF::Cell('185', 5, 'RINGKASAN PENDAPATAN, BELANJA DAN PEMBIAYAAN PERANGKAT DAERAH', 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Ln();
        $countrow ++;
        // }
        /*
         * $rek1 = DB::SELECT('SELECT o.kd_rek_1,o.nama_kd_rek_1,COALESCE(sum(a.jml_belanja_forum),0) AS blj FROM ref_rek_5 k
         *
         * INNER JOIN ref_rek_1 o ON k.kd_rek_1=o.kd_rek_1
         * left OUTER JOIN (SELECT a.tahun_forum,a.id_rekening_ssh, a.jml_belanja_forum FROM
         * trx_forum_skpd_belanja a
         *
         * inner join
         * trx_forum_skpd_aktivitas b on a.id_lokasi_forum=b.id_aktivitas_forum
         * WHERE a.tahun_forum=' . $tahun . ' and b.status_pelaksanaan=0) a
         * ON k.id_rekening=a.id_rekening_ssh WHERE o.kd_rek_1 in (4,5,6) GROUP BY o.kd_rek_1,o.nama_kd_rek_1');
         */
        // header
        $rek1 = DB::SELECT('SELECT O.kd_rek_1,O.nama_kd_rek_1, COALESCE(SUM(A.jml_belanja_forum),0) AS blj 
            FROM ref_rek_1 O LEFT OUTER JOIN 
						               (SELECT
	X.tahun_forum,
	X.id_rekening_ssh,
	Z.Kd_Rek_1,
	Z.Kd_Rek_2,
	Z.Kd_Rek_3,
	Z.Kd_Rek_4,
	Z.Kd_Rek_5,
	X.jml_belanja_forum
FROM
	trx_rkpd_final_belanja_pd X
INNER JOIN trx_rkpd_final_aktivitas_pd Y ON X.id_aktivitas_pd = Y.id_aktivitas_pd
INNER JOIN Ref_Rek_5 Z ON X.id_rekening_ssh = Z.id_rekening
	
		                        WHERE X.tahun_forum=' . $tahun . '   AND Y.status_pelaksanaan = 0
													) A ON O.kd_rek_1 = A.kd_rek_1 
						 WHERE O.kd_rek_1 IN (4,5,6) 
						 GROUP BY O.kd_rek_1,O.nama_kd_rek_1');
        PDF::SetFont('helvetica', 'B', 8);
        PDF::MultiCell('30', 7, 'Kode Rekening', 'LBT', 'C', 0, 0);
        PDF::MultiCell('115', 7, 'Uraian', 'LBT', 'C', 0, 0);
        PDF::MultiCell('40', 7, 'Jumlah (Rp)', 1, 'C', 0, 0);
        PDF::Ln();
        $countrow ++;
        PDF::MultiCell('30', 7, '1', 'LBT', 'C', 0, 0);
        PDF::MultiCell('115', 7, '2', 'LBT', 'C', 0, 0);
        PDF::MultiCell('40', 7, '3', 1, 'C', 0, 0);
        PDF::Ln();
        $countrow ++;
        $gb = 0;
        $temp = 0;
        $temp2 = 0;
        foreach ($rek1 as $row) {
            if ($gb == 2) {
                PDF::SetFont('helvetica', 'B', 8);
                PDF::MultiCell('30', 5, '', 'L', 'L', 0, 0);
                PDF::MultiCell('115', 5, 'SURPLUS/(DEFISIT)', 'L', 'R', 0, 0);
                PDF::MultiCell('40', 5, number_format(($temp - $temp2), 2, ',', '.'), 'LRB', 'R', 0, 0);
                PDF::Ln();
                $countrow ++;
            }
            $height = ceil((PDF::GetStringWidth($row->nama_kd_rek_1) / 115)) * 3;
            PDF::SetFont('helvetica', 'B', 8);
            PDF::MultiCell('30', $height, $row->kd_rek_1, 'L', 'L', 0, 0);
            PDF::MultiCell('115', $height, $row->nama_kd_rek_1, 'L', 'L', 0, 0);
            PDF::MultiCell('40', $height, number_format($row->blj, 2, ',', '.'), 'LRB', 'R', 0, 0);
            PDF::Ln();
            if ($gb == 0) {
                $temp = $row->blj;
            }
            if ($gb == 1) {
                $temp2 = $row->blj;
            }
            
            $countrow ++;
            $gb ++;
            
            if ($countrow >= $totalrow) {
                PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                PDF::AddPage('P');
                $countrow = 0;
                PDF::SetFont('helvetica', 'B', 8);
                PDF::MultiCell('30', 7, 'Kode Rekening', 'LBT', 'C', 0, 0);
                PDF::MultiCell('115', 7, 'Uraian', 'LBT', 'C', 0, 0);
                PDF::MultiCell('40', 7, 'Jumlah (Rp)', 1, 'C', 0, 0);
                PDF::Ln();
                $countrow ++;
                PDF::MultiCell('30', 7, '1', 'LBT', 'C', 0, 0);
                PDF::MultiCell('115', 7, '2', 'LBT', 'C', 0, 0);
                PDF::MultiCell('40', 7, '3', 1, 'C', 0, 0);
                PDF::Ln();
                $countrow ++;
            }
            
            /*
             * $rek2 = DB::SELECT('SELECT n.kd_rek_2,n.nama_kd_rek_2,COALESCE(sum(a.jml_belanja_forum),0) AS blj FROM ref_rek_5 k
             * INNER JOIN ref_rek_2 n ON k.kd_rek_2=n.kd_rek_2 AND k.kd_rek_1=n.kd_rek_1
             * LEFT OUTER JOIN (SELECT a.tahun_forum,a.id_rekening_ssh, a.jml_belanja_forum FROM trx_forum_skpd_belanja a inner join
             * trx_forum_skpd_aktivitas b on a.id_lokasi_forum=b.id_aktivitas_forum
             * WHERE a.tahun_forum=' . $tahun . ' and b.status_pelaksanaan=0) a
             * ON k.id_rekening=a.id_rekening_ssh WHERE k.kd_rek_1 in (4,5,6) AND k.kd_rek_1=' . $row->kd_rek_1 . ' GROUP BY n.kd_rek_2,n.nama_kd_rek_2');
             */
            $rek2 = DB::SELECT('SELECT O.kd_rek_2,O.nama_kd_rek_2, COALESCE(SUM(A.jml_belanja_forum),0) AS blj 
            FROM ref_rek_2 O LEFT OUTER JOIN 
						               (SELECT
	X.tahun_forum,
	X.id_rekening_ssh,
	Z.Kd_Rek_1,
	Z.Kd_Rek_2,
	Z.Kd_Rek_3,
	Z.Kd_Rek_4,
	Z.Kd_Rek_5,
	X.jml_belanja_forum
FROM
	trx_rkpd_final_belanja_pd X
INNER JOIN trx_rkpd_final_aktivitas_pd Y ON X.id_aktivitas_pd = Y.id_aktivitas_pd
INNER JOIN Ref_Rek_5 Z ON X.id_rekening_ssh = Z.id_rekening
	
		                        WHERE X.tahun_forum=' . $tahun . '  AND Y.status_pelaksanaan = 0
													) A ON O.kd_rek_1 = A.kd_rek_1 and O.kd_rek_2 = A.kd_rek_2 
						 WHERE O.kd_rek_1 IN (4,5,6) AND O.kd_rek_1=' . $row->kd_rek_1 . '  
						 GROUP BY O.kd_rek_2,O.nama_kd_rek_2');
            foreach ($rek2 as $row2) {
                $height = ceil((PDF::GetStringWidth($row2->nama_kd_rek_2) / 110)) * 3;
                PDF::SetFont('helvetica', 'B', 7);
                PDF::MultiCell('30', $height, $row->kd_rek_1 . '.' . $row2->kd_rek_2, 'L', 'L', 0, 0);
                PDF::MultiCell('5', $height, '', 'L', 'L', 0, 0);
                PDF::MultiCell('110', $height, $row2->nama_kd_rek_2, 0, 'L', 0, 0);
                PDF::MultiCell('40', $height, number_format($row2->blj, 2, ',', '.'), 'LRB', 'R', 0, 0);
                PDF::Ln();
                $countrow ++;
                
                if ($countrow >= $totalrow) {
                    PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                    PDF::AddPage('P');
                    $countrow = 0;
                    PDF::SetFont('helvetica', 'B', 8);
                    PDF::MultiCell('30', 7, 'Kode Rekening', 'LBT', 'C', 0, 0);
                    PDF::MultiCell('115', 7, 'Uraian', 'LBT', 'C', 0, 0);
                    PDF::MultiCell('40', 7, 'Jumlah (Rp)', 1, 'C', 0, 0);
                    PDF::Ln();
                    $countrow ++;
                    PDF::MultiCell('30', 7, '1', 'LBT', 'C', 0, 0);
                    PDF::MultiCell('115', 7, '2', 'LBT', 'C', 0, 0);
                    PDF::MultiCell('40', 7, '3', 1, 'C', 0, 0);
                    PDF::Ln();
                    $countrow ++;
                }
                
                /*
                 * $rek3 = DB::SELECT('SELECT m.kd_rek_3,m.nama_kd_rek_3,coalesce(sum(a.jml_belanja_forum),0) AS blj FROM ref_rek_5 k
                 *
                 * INNER JOIN ref_rek_3 m ON k.kd_rek_3=m.kd_rek_3 AND k.kd_rek_2=m.kd_rek_2 AND k.kd_rek_1=m.kd_rek_1
                 *
                 * LEFT OUTER JOIN (SELECT a.tahun_forum,a.id_rekening_ssh, a.jml_belanja_forum FROM trx_forum_skpd_belanja a inner join
                 * trx_forum_skpd_aktivitas b on a.id_lokasi_forum=b.id_aktivitas_forum
                 * WHERE a.tahun_forum=' . $tahun . ' and b.status_pelaksanaan=0) a
                 * ON k.id_rekening=a.id_rekening_ssh WHERE k.kd_rek_1 in (4,5,6) AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND k.kd_rek_2=' . $row2->kd_rek_2 . '
                 * GROUP BY m.kd_rek_3,m.nama_kd_rek_3');
                 */
                $rek3 = DB::SELECT('SELECT O.kd_rek_3,O.nama_kd_rek_3, COALESCE(SUM(A.jml_belanja_forum),0) AS blj 
            FROM ref_rek_3 O LEFT OUTER JOIN 
						               (SELECT
	X.tahun_forum,
	X.id_rekening_ssh,
	Z.Kd_Rek_1,
	Z.Kd_Rek_2,
	Z.Kd_Rek_3,
	Z.Kd_Rek_4,
	Z.Kd_Rek_5,
	X.jml_belanja_forum
FROM
	trx_rkpd_final_belanja_pd X
INNER JOIN trx_rkpd_final_aktivitas_pd Y ON X.id_aktivitas_pd = Y.id_aktivitas_pd
INNER JOIN Ref_Rek_5 Z ON X.id_rekening_ssh = Z.id_rekening
	
		                        WHERE X.tahun_forum=' . $tahun . '   AND Y.status_pelaksanaan = 0
													) A ON O.kd_rek_1 = A.kd_rek_1 and O.kd_rek_2 = A.kd_rek_2 and O.kd_rek_3 = A.kd_rek_3 
						 WHERE O.kd_rek_1 IN (4,5,6) AND O.kd_rek_1=' . $row->kd_rek_1 . ' AND O.kd_rek_2=' . $row2->kd_rek_2 . ' 
						 GROUP BY O.kd_rek_3,O.nama_kd_rek_3');
                
                foreach ($rek3 as $row3) {
                    $height = ceil((PDF::GetStringWidth($row3->nama_kd_rek_3) / 105)) * 3;
                    PDF::SetFont('helvetica', '', 7);
                    PDF::MultiCell('30', $height, $row->kd_rek_1 . '.' . $row2->kd_rek_2 . '.' . $row3->kd_rek_3, 'L', 'L', 0, 0);
                    PDF::MultiCell('10', $height, '', 'L', 'L', 0, 0);
                    PDF::MultiCell('105', $height, $row3->nama_kd_rek_3, 0, 'L', 0, 0);
                    PDF::MultiCell('40', $height, number_format($row3->blj, 2, ',', '.'), 'LR', 'R', 0, 0);
                    PDF::Ln();
                    $countrow ++;
                    
                    if ($countrow >= $totalrow) {
                        PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                        PDF::AddPage('P');
                        $countrow = 0;
                        PDF::SetFont('helvetica', 'B', 8);
                        PDF::MultiCell('30', 7, 'Kode Rekening', 'LBT', 'C', 0, 0);
                        PDF::MultiCell('115', 7, 'Uraian', 'LBT', 'C', 0, 0);
                        PDF::MultiCell('40', 7, 'Jumlah (Rp)', 1, 'C', 0, 0);
                        PDF::Ln();
                        $countrow ++;
                        PDF::MultiCell('30', 7, '1', 'LBT', 'C', 0, 0);
                        PDF::MultiCell('115', 7, '2', 'LBT', 'C', 0, 0);
                        PDF::MultiCell('40', 7, '3', 1, 'C', 0, 0);
                        PDF::Ln();
                        $countrow ++;
                    }
                }
            }
        }
        PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
        $template = new TemplateReport();
        $template->footerPotrait();
        PDF::Output('RingkasApbd-' . $pemda . '.pdf', 'I');
    }


    // /////////////////////////////////// PR 17 Oktober 2018 /////////////////////////////////////////////////////////////////////////////////////////////////
    }

