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

use App\Models\RefUnit;

class CetakAPBDController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function PraRKA2(Request $request)
    {
        $countrow = 0;
        $totalrow = 37;
        $id_renja = 20;
        // $request->sub_unit=7;
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
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        // add a page
        PDF::AddPage('L');
        
        // column titles
        $header = array('INDIKATOR');
        $header2 = array('SKPD/Program/Kegiatan','Uraian Indikator','Tolak Ukur', 'Target Renstra','Target Renja', 'Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
        
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
            'color' => array(0,0,0)
        ));
        PDF::SetFont('helvetica', '', 10);
        
        // Header
        $sub = DB::select('SELECT a.tahun_anggaran AS tahun_forum, g.kd_urusan, f.kd_bidang, e.kd_unit, d.kd_sub,
                d.nm_sub, e.nm_unit, g.nm_urusan, f.nm_bidang
                FROM trx_anggaran_kegiatan_pd b
                INNER JOIN trx_anggaran_program_pd a ON a.id_program_pd = b.id_program_pd
                INNER JOIN trx_anggaran_pelaksana_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
                INNER JOIN ref_sub_unit d ON c.id_sub_unit = d.id_sub_unit
                INNER JOIN ref_unit e ON d.id_unit = e.id_unit
                INNER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
                INNER JOIN ref_urusan g ON f.kd_urusan = g.kd_urusan
                INNER JOIN trx_anggaran_pelaksana AS j ON a.id_pelaksana_anggaran = j.id_pelaksana_anggaran
                INNER JOIN trx_anggaran_program AS l ON j.id_anggaran_pemda = l.id_anggaran_pemda
                INNER JOIN trx_anggaran_dokumen AS m ON l.id_dokumen_keu = m.id_dokumen_keu
                WHERE c.id_sub_unit =' . $request->sub_unit . ' AND b.tahun_anggaran=' . $request->tahun . ' AND m.id_dokumen_keu='.$request->id_dokumen.' limit 1');

        foreach ($sub AS $row) {
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
           
            $nama_sub = $nama_sub . $row->nm_sub;
        }

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

        $request->tahunn1 = $request->tahun + 1;
        $prog = DB::select('SELECT a.id_forum_program, a.kode, a.kd_program, a.uraian_program_renstra,
            SUM(a.blj_peg) AS blj_peg, SUM(a.blj_bj) AS blj_bj, SUM(a.blj_modal) AS blj_modal, b.pagu_anggaran AS pagu_forum, a.pagu_tahun_program
            FROM (SELECT a.id_program_pd AS id_forum_program, CONCAT(o.kd_urusan,".",o.kd_bidang,"  ",g.kd_urusan,".",f.kd_bidang,".",e.kd_unit,".",
            d.kd_sub," ") AS kode, n.kd_program, a.uraian_program_renstra, CASE m.kd_rek_3 WHEN 1 THEN (i.jml_belanja) ELSE 0 END AS blj_peg,
            CASE m.kd_rek_3 WHEN 2 THEN (i.jml_belanja) ELSE 0 END AS blj_bj,
            CASE m.kd_rek_3 WHEN 3 THEN (i.jml_belanja) ELSE 0 END AS blj_modal, m.kd_rek_3, p.pagu_tahun_program
            FROM trx_anggaran_program_pd AS a
            INNER JOIN trx_anggaran_kegiatan_pd AS b ON b.id_program_pd = a.id_program_pd
            INNER JOIN trx_anggaran_pelaksana_pd AS c ON c.id_kegiatan_pd = b.id_kegiatan_pd
            LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit = d.id_sub_unit
            LEFT OUTER JOIN ref_unit e ON d.id_unit = e.id_unit
            LEFT OUTER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
            LEFT OUTER JOIN ref_urusan g ON f.kd_urusan = g.kd_urusan
            INNER JOIN trx_anggaran_aktivitas_pd AS h ON c.id_pelaksana_pd = h.id_pelaksana_pd
            INNER JOIN trx_anggaran_belanja_pd AS i ON h.id_aktivitas_pd = i.id_aktivitas_pd
            INNER JOIN ref_ssh_tarif j ON i.id_item_ssh = j.id_tarif_ssh
            LEFT OUTER JOIN ref_rek_5 k ON i.id_rekening_ssh = k.id_rekening
            LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4 = l.kd_rek_4 AND k.kd_rek_3 = l.kd_rek_3 AND k.kd_rek_2 = l.kd_rek_2 AND k.kd_rek_1 = l.kd_rek_1
            LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3 = m.kd_rek_3 AND l.kd_rek_2 = m.kd_rek_2 AND l.kd_rek_1 = m.kd_rek_1
            INNER JOIN ref_program n ON a.id_program_ref = n.id_program
            INNER JOIN ref_bidang o ON o.id_bidang = n.id_bidang
            LEFT OUTER JOIN ( SELECT id_program_renstra, pagu_tahun_program FROM trx_rkpd_renstra
            WHERE tahun_rkpd =' . $request->tahunn1 . '  GROUP BY id_program_renstra, pagu_tahun_program ) p ON a.id_program_renstra = p.id_program_renstra
            INNER JOIN trx_anggaran_pelaksana AS q ON a.id_pelaksana_anggaran = q.id_pelaksana_anggaran
            INNER JOIN trx_anggaran_program AS r ON q.id_anggaran_pemda = r.id_anggaran_pemda
            INNER JOIN trx_anggaran_dokumen AS s ON r.id_dokumen_keu = s.id_dokumen_keu
            WHERE c.id_sub_unit =' . $request->sub_unit . ' AND k.kd_rek_1 = 5 AND k.kd_rek_2 = 2 AND a.tahun_anggaran = ' . $request->tahun . ' AND s.id_dokumen_keu='. $request->id_dokumen.' ) a
            INNER JOIN trx_anggaran_program_pd b ON a.id_forum_program = b.id_program_pd
            GROUP BY a.id_forum_program, a.kode, a.kd_program, a.uraian_program_renstra, b.pagu_anggaran, a.pagu_tahun_program ');

        foreach ($prog AS $row) {
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
            } ELSE {
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
            $indikatorprog = DB::select('SELECT DISTINCT d.uraian_program_renstra, b.uraian_indikator_program, 
                    b.tolok_ukur_indikator, b.target_renstra, b.target_renja, f.singkatan_satuan
                    FROM trx_anggaran_program_pd d
                    INNER JOIN trx_anggaran_prog_indikator_pd b ON d.id_program_pd = b.id_program_pd
                    INNER JOIN trx_anggaran_kegiatan_pd g ON d.id_program_pd = g.id_program_pd
                    INNER JOIN trx_anggaran_pelaksana_pd h ON g.id_kegiatan_pd = h.id_kegiatan_pd
                    LEFT OUTER JOIN ref_satuan f ON b.id_satuan_output = f.id_satuan
                    INNER JOIN trx_anggaran_pelaksana AS q ON d.id_pelaksana_anggaran = q.id_pelaksana_anggaran
                    INNER JOIN trx_anggaran_program AS r ON q.id_anggaran_pemda = r.id_anggaran_pemda
                    INNER JOIN trx_anggaran_dokumen AS s ON r.id_dokumen_keu = s.id_dokumen_keu
                    WHERE h.id_sub_unit =' . $request->sub_unit . ' AND s.id_dokumen_keu='. $request->id_dokumen.' AND d.id_program_pd = ' . $row->id_forum_program);
            
            foreach ($indikatorprog AS $row3) {
                PDF::SetFont('helvetica', 'B', 7);
                $height = ceil((strlen($row3->uraian_indikator_program) / 49)) * 4;
                $kode = "";
                if (strlen($row->kd_program) == 2) {
                    $kode = $row->kode . $row->kd_program;
                } ELSE {
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
            $keg = DB::select('SELECT a.id_forum_skpd, a.kd_kegiatan, a.uraian_kegiatan_forum, COALESCE (c.gablok, "belum ada") AS gablok,
                    SUM(a.blj_peg) AS blj_peg, SUM(a.blj_bj) AS blj_bj, SUM(a.blj_modal) AS blj_modal, b.pagu_forum, a.pagu_tahun_kegiatan
                    FROM (SELECT b.id_kegiatan_pd AS id_forum_skpd, n.kd_kegiatan, b.uraian_kegiatan_forum,
                    CASE m.kd_rek_3 WHEN 1 THEN (i.jml_belanja) ELSE 0 END AS blj_peg, CASE m.kd_rek_3 WHEN 2 THEN (i.jml_belanja) ELSE 0 END AS blj_bj,
                    CASE m.kd_rek_3 WHEN 3 THEN (i.jml_belanja) ELSE 0 END AS blj_modal, m.kd_rek_3, q.pagu_tahun_kegiatan
                    FROM trx_anggaran_kegiatan_pd b
                    INNER JOIN trx_anggaran_pelaksana_pd c ON b.id_kegiatan_pd = c.id_kegiatan_pd
                    LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit = d.id_sub_unit
                    LEFT OUTER JOIN ref_unit e ON d.id_unit = e.id_unit
                    LEFT OUTER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
                    LEFT OUTER JOIN ref_urusan g ON f.kd_urusan = g.kd_urusan
                    INNER JOIN trx_anggaran_aktivitas_pd h ON c.id_pelaksana_pd = h.id_pelaksana_pd
                    INNER JOIN trx_anggaran_belanja_pd i ON h.id_aktivitas_pd = i.id_aktivitas_pd
                    INNER JOIN ref_ssh_tarif j ON i.id_item_ssh = j.id_tarif_ssh
                    LEFT OUTER JOIN ref_rek_5 k ON i.id_rekening_ssh = k.id_rekening
                    LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4 = l.kd_rek_4 AND k.kd_rek_3 = l.kd_rek_3 AND k.kd_rek_2 = l.kd_rek_2 AND k.kd_rek_1 = l.kd_rek_1
                    LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3 = m.kd_rek_3 AND l.kd_rek_2 = m.kd_rek_2 AND l.kd_rek_1 = m.kd_rek_1
                    INNER JOIN ref_kegiatan n ON b.id_kegiatan_ref = n.id_kegiatan
                    INNER JOIN ref_program o ON o.id_program = n.id_program
                    INNER JOIN ref_bidang p ON o.id_bidang = p.id_bidang
                    LEFT OUTER JOIN ( SELECT id_kegiatan_renstra, pagu_tahun_kegiatan FROM trx_rkpd_renstra WHERE tahun_rkpd = '.$request->tahun.' 
                    GROUP BY id_kegiatan_renstra, pagu_tahun_kegiatan ) q ON b.id_kegiatan_renstra = q.id_kegiatan_renstra
                    WHERE b.id_program_pd = ' . $row->id_forum_program . ' AND  k.kd_rek_1 = 5 AND k.kd_rek_2 = 2 ) a
                    INNER JOIN trx_anggaran_kegiatan_pd b ON a.id_forum_skpd = b.id_kegiatan_pd
                    LEFT OUTER JOIN ( SELECT GROUP_CONCAT(c.nama_lokasi) AS gablok, d.id_kegiatan_pd AS id_forum_skpd
                    FROM trx_anggaran_kegiatan_pd AS d
                    INNER JOIN trx_anggaran_pelaksana_pd AS a ON d.id_kegiatan_pd = a.id_kegiatan_pd
                    INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_pelaksana_pd = b.id_pelaksana_pd
                    LEFT OUTER JOIN trx_anggaran_lokasi_pd e ON b.id_aktivitas_pd = e.id_aktivitas_pd
                    INNER JOIN ref_lokasi c ON e.id_lokasi = c.id_lokasi
                    GROUP BY d.id_kegiatan_pd ) c ON b.id_kegiatan_pd = c.id_forum_skpd
                    GROUP BY a.id_forum_skpd, a.kd_kegiatan, a.uraian_kegiatan_forum, c.gablok, b.pagu_forum, a.pagu_tahun_kegiatan');

            foreach ($keg AS $row2) {
                $height1 = ceil((strlen($row2->uraian_kegiatan_forum) / 54)) * 4;
                $height2 = ceil((strlen($row2->gablok) / 15)) * 4;
                $height = max($height1, $height2);
                PDF::SetFont('helvetica', '', 7);
                $kode2 = "";
                if (strlen($row2->kd_kegiatan) == 2) {
                    $kode2 = $kode . '.' . $row2->kd_kegiatan;
                } ELSE {
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
                $indikator = DB::select('SELECT DISTINCT d.uraian_kegiatan_forum, b.uraian_indikator_kegiatan, b.tolok_ukur_indikator, b.target_renstra,
                        b.target_renja, f.singkatan_satuan, CASE b.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" END AS status_indikator
                        FROM trx_anggaran_kegiatan_pd d
                        INNER JOIN trx_anggaran_keg_indikator_pd b ON d.id_kegiatan_pd = b.id_kegiatan_pd
                        INNER JOIN trx_anggaran_pelaksana_pd h ON d.id_kegiatan_pd = h.id_kegiatan_pd
                        LEFT OUTER JOIN ref_satuan f ON b.id_satuan_output = f.id_satuan
                        WHERE h.id_sub_unit =' . $request->sub_unit . ' AND d.id_program_pd=' . $row->id_forum_program . ' AND d.id_kegiatan_pd=' . $row2->id_forum_skpd);
                foreach ($indikator AS $row4) {
                    $height = ceil((strlen($row4->uraian_indikator_kegiatan) / 48)) * 4;
                    
                    PDF::SetFont('helvetica', '', 7);
                    $kode2 = "";
                    if (strlen($row2->kd_kegiatan) == 2) {
                        $kode2 = $kode . '.' . $row2->kd_kegiatan;
                    } ELSE {
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

        PDF::Output('PraRKA2-' . $nama_sub . '.pdf', 'I');
    }
    
    public function Apbd(Request $request)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
        
        $request->id_kegiatan = 20;
        $request->sub_unit = 7;
        $nama_sub = "";
        $pagu_skpd_peg = 0;
        $pagu_skpd_bj = 0;
        $pagu_skpd_mod = 0;
        $pagu_skpd_pend = 0;
        $pagu_skpd_btl = 0;
        $pagu_skpd = 0;
        $pemda = Session::get('xPemda');

        PDF::SetFont('helvetica', '', 6);
        
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
        $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> ' . Session::get('xPemda') . '</div>';
        $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Tahun Anggaran : ' . $request->tahun . '</div>';
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
        
        $apbd = DB::SELECT('SELECT concat(a.kd_urusan,".",a.kd_bidang,".",a.kd_unit) AS kode,a.id_unit,a.nm_unit, SUM(a.jml_pend) AS pend,
             SUM(a.jml_btl) AS btl,
            SUM(a.jml_peg) AS peg, SUM(a.jml_bj) AS bj, SUM(a.jml_mod) AS modal  FROM
            (SELECT f.kd_urusan,f.kd_bidang,e.kd_unit,e.id_unit,e.nm_unit,
            CASE o.kd_rek_1 WHEN 4 THEN i.jml_belanja ELSE 0 END AS jml_pend,
            CASE o.kd_rek_1 WHEN 5 THEN
            	CASE n.kd_rek_2 WHEN 1 THEN i.jml_belanja ELSE 0 END
            ELSE 0 END AS jml_btl,
            CASE o.kd_rek_1 WHEN 5 THEN
            	CASE n.kd_rek_2 WHEN 2 THEN
            		CASE m.kd_rek_3 WHEN 1 THEN i.jml_belanja ELSE 0 end
            	ELSE 0 END
            ELSE 0 END AS jml_peg,
            CASE o.kd_rek_1 WHEN 5 THEN
            	CASE n.kd_rek_2 WHEN 2 THEN
            		CASE m.kd_rek_3 WHEN 2 THEN i.jml_belanja ELSE 0 end
            	ELSE 0 END
            ELSE 0 END AS jml_bj,
            CASE o.kd_rek_1 WHEN 5 THEN
            	CASE n.kd_rek_2 WHEN 2 THEN
            		CASE m.kd_rek_3 WHEN 3 THEN i.jml_belanja ELSE 0 end
            	ELSE 0 END
            ELSE 0 END AS jml_mod
        From trx_anggaran_program_pd AS a
        INNER JOIN trx_anggaran_kegiatan_pd AS b ON b.id_program_pd = a.id_program_pd
        INNER JOIN trx_anggaran_pelaksana_pd AS c ON c.id_kegiatan_pd = b.id_kegiatan_pd
        LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit = d.id_sub_unit
        LEFT OUTER JOIN ref_unit e ON d.id_unit = e.id_unit
        LEFT OUTER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
        LEFT OUTER JOIN ref_urusan g ON f.kd_urusan = g.kd_urusan
        INNER JOIN trx_anggaran_aktivitas_pd AS h ON c.id_pelaksana_pd = h.id_pelaksana_pd
        INNER JOIN trx_anggaran_belanja_pd AS i ON h.id_aktivitas_pd = i.id_aktivitas_pd
        INNER JOIN ref_ssh_tarif j ON i.id_item_ssh=j.id_tarif_ssh
        LEFT OUTER  join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
        LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1
        LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1
        LEFT OUTER JOIN ref_rek_2 n ON m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1
        LEFT OUTER JOIN ref_rek_1 o ON n.kd_rek_1=o.kd_rek_1
        INNER JOIN trx_anggaran_pelaksana AS p ON a.id_pelaksana_anggaran = p.id_pelaksana_anggaran
        INNER JOIN trx_anggaran_program AS q ON p.id_anggaran_pemda = q.id_anggaran_pemda
        INNER JOIN trx_anggaran_dokumen AS r ON q.id_dokumen_keu = r.id_dokumen_keu
        WHERE a.tahun_anggaran=' . $request->tahun . '  AND h.status_pelaksanaan=0 AND r.id_dokumen_keu='. $request->id_dokumen.'
        ) a GROUP BY a.kd_urusan,a.kd_bidang,a.kd_unit,a.id_unit,a.nm_unit');

        $html .= '<tbody>';
        foreach ($apbd AS $row) {
            
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

     public function PraRKA(Request $request)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
        
        $nama_keg = "";
        
        // set font
        PDF::SetFont('helvetica', '', 6);

        $kegiatan = DB::SELECT('SELECT a.tahun_anggaran AS tahun_forum, g.kd_urusan, f.kd_bidang, e.kd_unit, d.kd_sub, h.kd_program AS no_urut_pro,
            i.kd_kegiatan AS no_urut_keg, b.uraian_kegiatan_forum, a.uraian_program_renstra, d.nm_sub, e.nm_unit, g.nm_urusan, f.nm_bidang
            FROM trx_anggaran_program_pd AS a
            INNER JOIN trx_anggaran_kegiatan_pd AS b ON b.id_program_pd = a.id_program_pd
            INNER JOIN trx_anggaran_pelaksana_pd AS c ON c.id_kegiatan_pd = b.id_kegiatan_pd
            INNER JOIN ref_sub_unit d ON c.id_sub_unit = d.id_sub_unit
            INNER JOIN ref_unit e ON d.id_unit = e.id_unit
            INNER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
            INNER JOIN ref_urusan g ON f.kd_urusan = g.kd_urusan
            INNER JOIN ref_program h ON a.id_program_ref = h.id_program
            INNER JOIN ref_kegiatan i ON b.id_kegiatan_ref = i.id_kegiatan
            INNER JOIN trx_anggaran_pelaksana AS j ON a.id_pelaksana_anggaran = j.id_pelaksana_anggaran
            INNER JOIN trx_anggaran_program AS l ON j.id_anggaran_pemda = l.id_anggaran_pemda
            INNER JOIN trx_anggaran_dokumen AS m ON l.id_dokumen_keu = m.id_dokumen_keu
            WHERE b.id_kegiatan_pd=' . $request->id_kegiatan . ' AND c.id_sub_unit=' . $request->sub_unit.' AND m.id_dokumen_keu=' .$request->id_dokumen);
        
            $html = '';
        foreach ($kegiatan AS $row) {
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
                    FROM trx_anggaran_kegiatan_pd AS a
                    INNER JOIN trx_anggaran_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
                    INNER JOIN trx_anggaran_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
                    INNER JOIN trx_anggaran_lokasi_pd AS d ON d.id_aktivitas_pd = c.id_aktivitas_pd
                    INNER JOIN ref_lokasi e ON d.id_lokasi=e.id_lokasi
                    WHERE a.id_kegiatan_pd=' . $request->id_kegiatan . ' AND b.id_sub_unit=' . $request->sub_unit);

            $c = 0;
            $gablok = "";
            foreach ($lokasi AS $row) {
                if ($c == 0) {
                    $gablok = $gablok . '' . $row->nama_lokasi;
                } ELSE {
                    $gablok = $gablok . ', ' . $row->nama_lokasi;
                }
                $c = $c + 1;
            }
            $html .= '<td width="76%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $gablok . '</td>
                </tr>';
            $html .= '</table>';
            
            $html .= '</table>';
            $pagu = DB::SELECT('SELECT a.tahun_anggaran - c.tahun_rkpd AS selisih, c.tahun_rkpd, c.pagu_tahun_kegiatan, a.pagu_tahun_kegiatan AS pagu_n
                    FROM trx_anggaran_kegiatan_pd a
                    INNER JOIN trx_rkpd_renstra b ON a.id_rkpd_renstra = b.id_rkpd_renstra
                    INNER JOIN trx_rkpd_renstra c ON b.id_kegiatan_renstra = c.id_kegiatan_renstra
                    WHERE a.id_kegiatan_pd=' . $request->id_kegiatan . ' AND (a.tahun_anggaran-c.tahun_rkpd in (-1,0,1)) order by c.tahun_rkpd ASC ');
            $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
            
            PDF::SetFont('helvetica', '', 8);
            $html .= '<table border="0" cellpadding="4" cellspacing="0">';
            
            foreach ($pagu AS $row) {
                if ($pagu > 0) {
                    if ($row->selisih == 1) {
                        $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Jumlah Tahun n-1</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="27%"   style="padding: 50px; font-size:8px; text-align: right;" >Rp.' . number_format($row->pagu_tahun_kegiatan, 2, ',', '.') . '</td>
                        <td width="49%"   style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        </tr>';
                    } ELSE if ($row->selisih == 0) {
                        $html .= '<tr height=19>
                        <td width="20%"  style="padding: 50px; font-size:8px; text-align: left;" >Jumlah Tahun n</td>
                        <td width="4%"   style="padding: 50px; font-size:8px; text-align: left;" >:</td>
                        <td width="27%"   style="padding: 50px; font-size:8px; text-align: right;" >Rp.' . number_format($row->pagu_n, 2, ',', '.') . '</td>
                        <td width="49%"   style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        </tr>';
                    } ELSE if ($row->selisih == - 1) {
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
            $pagu2 = DB::SELECT('SELECT a.pagu_tahun_kegiatan FROM trx_anggaran_kegiatan_pd  a WHERE a.id_kegiatan_pd=' . $request->id_kegiatan);
            foreach ($pagu2 AS $row) {
                $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
                $html .= '<tr height=19>
                        <td width="22%"  style="padding: 50px; font-size:8px; text-align: left;" >MASUKAN</td>
                        <td width="48%"  style="padding: 50px; font-size:8px; text-align: left;" >Jumlah Dana</td>
                        <td width="30%"  style="padding: 50px; font-size:8px; text-align: left;" >Rp.' . number_format($row->pagu_tahun_kegiatan, 2, ',', '.') . '</td>
                        </tr>';
                PDF::SetFont('helvetica', '', 8);
                $html .= '</table>';
            }
            $ind = DB::SELECT('SELECT b.uraian_indikator_kegiatan AS nm_indikator, b.target_renja, d.uraian_satuan
                FROM trx_anggaran_kegiatan_pd a
                INNER JOIN trx_anggaran_keg_indikator_pd b ON a.id_kegiatan_pd = b.id_kegiatan_pd
                LEFT OUTER JOIN ref_indikator c ON b.kd_indikator = c.id_indikator
                LEFT OUTER JOIN ref_satuan d ON b.id_satuan_output = d.id_satuan
                WHERE a.id_kegiatan_pd=' . $request->id_kegiatan);
            
            $c = 0;
            foreach ($ind AS $row) {
                $html .= '<table border="0.5" cellpadding="4" cellspacing="0">';
                if ($c == 0) {
                    $html .= '<tr height=19>
                        <td width="22%"  style="padding: 50px; font-size:8px; text-align: left;" >KELUARAN</td>
                        <td width="48%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->nm_indikator . '</td>
                        <td width="30%"  style="padding: 50px; font-size:8px; text-align: left;" >' . number_format($row->target_renja, 2, ',', '.') . ' ' . $row->uraian_satuan . '</td>
                        </tr>';
                } ELSE {
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
            
            $rek1 = DB::SELECT('SELECT k.kd_rek_1, k.nama_kd_rek_1, SUM(e.jml_belanja) AS jumlah
                FROM trx_anggaran_kegiatan_pd AS a
                INNER JOIN trx_anggaran_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
                INNER JOIN trx_anggaran_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
                INNER JOIN trx_anggaran_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd
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
                WHERE  a.id_kegiatan_pd=' . $request->id_kegiatan . ' AND b.id_sub_unit=' . $request->sub_unit . ' GROUP BY k.kd_rek_1,k.nama_kd_rek_1');

            foreach ($rek1 AS $row) {
                $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->nama_kd_rek_1 . '</td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                $rek2 = DB::SELECT('SELECT j.kd_rek_2,j.nama_kd_rek_2,SUM(e.jml_belanja) AS jumlah
                    FROM    trx_anggaran_kegiatan_pd AS a
                    INNER JOIN trx_anggaran_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
                    INNER JOIN trx_anggaran_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
                    INNER JOIN trx_anggaran_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd
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
                    WHERE a.id_kegiatan_pd=' . $request->id_kegiatan . ' AND b.id_sub_unit=' . $request->sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' GROUP BY j.kd_rek_2,j.nama_kd_rek_2');

                foreach ($rek2 AS $row2) {
                    $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '.' . $row2->kd_rek_2 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;"><table border="0" cellpadding="0" cellspacing="0"><tr><td width="5%"></td>  <td width="95%">' . $row2->nama_kd_rek_2 . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row2->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                    $rek3 = DB::SELECT('SELECT i.kd_rek_3,i.nama_kd_rek_3,SUM(e.jml_belanja) AS jumlah
                        FROM trx_anggaran_kegiatan_pd AS a
                        INNER JOIN trx_anggaran_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
                        INNER JOIN trx_anggaran_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
                        INNER JOIN trx_anggaran_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd
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
                        WHERE a.id_kegiatan_pd=' . $request->id_kegiatan . ' AND b.id_sub_unit=' . $request->sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . ' GROUP BY i.kd_rek_3,i.nama_kd_rek_3');
                    foreach ($rek3 AS $row3) {
                        $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '.' . $row2->kd_rek_2 . '.' . $row3->kd_rek_3 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="10%"></td>  <td width="90%">' . $row3->nama_kd_rek_3 . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row3->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                        $rek4 = DB::SELECT('SELECT h.kd_rek_4,h.nama_kd_rek_4,SUM(e.jml_belanja) AS jumlah
                            FROM trx_anggaran_kegiatan_pd AS a
                            INNER JOIN trx_anggaran_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
                            INNER JOIN trx_anggaran_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
                            INNER JOIN trx_anggaran_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd
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
                            WHERE a.id_kegiatan_pd=' . $request->id_kegiatan . ' AND b.id_sub_unit=' . $request->sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . ' AND i.kd_rek_3=' . $row3->kd_rek_3 . ' GROUP BY h.kd_rek_4,h.nama_kd_rek_4');
                        foreach ($rek4 AS $row4) {
                            $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '.' . $row2->kd_rek_2 . '.' . $row3->kd_rek_3 . '.' . $row4->kd_rek_4 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="15%"></td>  <td width="85%">' . $row4->nama_kd_rek_4 . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row4->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                            $rek5 = DB::SELECT('SELECT g.kd_rek_5, g.nama_kd_rek_5, SUM(e.jml_belanja) AS jumlah
                            FROM trx_anggaran_kegiatan_pd AS a
                            INNER JOIN trx_anggaran_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
                            INNER JOIN trx_anggaran_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
                            INNER JOIN trx_anggaran_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd
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
                            WHERE a.id_kegiatan_pd=' . $request->id_kegiatan . ' AND b.id_sub_unit=' . $request->sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . ' AND i.kd_rek_3=' . $row3->kd_rek_3 . ' AND h.kd_rek_4=' . $row4->kd_rek_4 . ' GROUP BY g.kd_rek_5,g.nama_kd_rek_5');
                            foreach ($rek5 AS $row5) {
                                $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" >' . $row->kd_rek_1 . '.' . $row2->kd_rek_2 . '.' . $row3->kd_rek_3 . '.' . $row4->kd_rek_4 . '.' . $row5->kd_rek_5 . '</td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="20%"></td>  <td width="80%">' . $row5->nama_kd_rek_5 . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" ></td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row5->jumlah, 2, ',', '.') . '</td>
                        </tr>';
                                $akt = DB::SELECT('SELECT c.id_aktivitas_pd AS id_aktivitas_forum,c.uraian_aktivitas_kegiatan,SUM(e.jml_belanja) AS jumlah
                                FROM trx_anggaran_kegiatan_pd AS a
                                INNER JOIN trx_anggaran_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
                                INNER JOIN trx_anggaran_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
                                INNER JOIN trx_anggaran_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd
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
                                WHERE a.id_kegiatan_pd=' . $request->id_kegiatan . ' AND b.id_sub_unit=' . $request->sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . ' AND i.kd_rek_3=' . $row3->kd_rek_3 . ' AND h.kd_rek_4=' . $row4->kd_rek_4 . ' AND g.kd_rek_5=' . $row5->kd_rek_5 . ' GROUP BY c.id_aktivitas_pd, c.uraian_aktivitas_kegiatan');
                                foreach ($akt AS $row6) {
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
                                    , e.volume_1 AS volume_1,m.uraian_satuan AS satuan1
                                    ,e.volume_2 AS volume_2,n.uraian_satuan AS satuan2
                                    ,e.harga_satuan AS harga_satuan, e.jml_belanja AS jml_belanja
                                    FROM trx_anggaran_kegiatan_pd AS a
                                    INNER JOIN trx_anggaran_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_kegiatan_pd
                                    INNER JOIN trx_anggaran_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd
                                    INNER JOIN trx_anggaran_belanja_pd AS e ON e.id_aktivitas_pd = c.id_aktivitas_pd
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
                                    LEFT OUTER JOIN ref_satuan m ON e.id_satuan_1=m.id_satuan
                                    LEFT OUTER JOIN ref_satuan n ON e.id_satuan_2=n.id_satuan
                                    WHERE a.id_kegiatan_pd=' . $request->id_kegiatan . ' AND b.id_sub_unit=' . $request->sub_unit . ' AND k.kd_rek_1=' . $row->kd_rek_1 . ' AND j.kd_rek_2=' . $row2->kd_rek_2 . '
                                    AND i.kd_rek_3=' . $row3->kd_rek_3 . ' AND h.kd_rek_4=' . $row4->kd_rek_4 . ' AND g.kd_rek_5=' . $row5->kd_rek_5 . ' AND e.id_aktivitas_pd =' . $row6->id_aktivitas_forum);
                                    foreach ($belanja AS $row7) {
                                        if ($row7->satuan2 > 0) {
                                            $html .= '<tr height=10  nobr="true">
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: left;" ></td>
                        <td width="40%"  style="padding: 50px; font-size:8px; text-align: left;" ><table border="0" cellpadding="0" cellspacing="0"><tr><td width="30%"></td>  <td width="70%"> ' . $row7->uraian_tarif_ssh . '</td></tr></table></td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >' . number_format($row7->volume_1 * $row7->volume_2, 2, ',', '.') . '</td>
                        <td width="10%"  style="padding: 50px; font-size:8px; text-align: center;" >' . $row7->satuan1 . ' x ' . $row7->satuan2 . '</td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: center;" >' . number_format($row7->harga_satuan, 2, ',', '.') . '</td>
                        <td width="15%"  style="padding: 50px; font-size:8px; text-align: right;" >' . number_format($row7->jml_belanja, 2, ',', '.') . '</td>
                        </tr>';
                                        } ELSE {
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

    public function RingkasApbd(Request $request)
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

        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('Pra RKA');

        PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 011', PDF_HEADER_STRING);

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

        PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

        PDF::SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
        
        PDF::AddPage('P');
        
        PDF::SetFont('helvetica', '', 6);

        $header = array('INDIKATOR');
        $header2 = array('SKPD/Program/Kegiatan', 'Uraian Indikator', 'Tolak Ukur', 'Target Renstra', 'Target Renja',
            'Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');

        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0);
        PDF::SetLineStyle(array(
            'width' => 0.1,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0.1,
            'color' => array( 0, 0, 0 )
        ));
        PDF::SetFont('helvetica', '', 10);

        PDF::SetFont('helvetica', 'B', 10);
        PDF::Cell('185', 5, Session::get('xPemda'), 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::SetFont('helvetica', 'B', 8);
        PDF::Cell('185', 5, 'Tahun Anggaran : ' . $request->tahun, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::SetFont('helvetica', 'B', 9);
        PDF::Cell('185', 5, 'RINGKASAN PENDAPATAN, BELANJA DAN PEMBIAYAAN PERANGKAT DAERAH', 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Ln();
        $countrow ++;

        $rek1 = DB::SELECT('SELECT O.kd_rek_1,O.nama_kd_rek_1, COALESCE(SUM(A.jml_belanja_forum),0) AS blj 
            FROM ref_rek_1 O 
            LEFT OUTER JOIN  (SELECT X.tahun_anggaran AS tahun_forum, X.id_rekening_ssh, Z.Kd_Rek_1, Z.Kd_Rek_2,
            Z.Kd_Rek_3, Z.Kd_Rek_4, Z.Kd_Rek_5, X.jml_belanja AS jml_belanja_forum
            FROM trx_anggaran_belanja_pd X
            INNER JOIN trx_anggaran_aktivitas_pd Y ON X.id_aktivitas_pd = Y.id_aktivitas_pd
            INNER JOIN ref_rek_5 Z ON X.id_rekening_ssh = Z.id_rekening
            INNER JOIN trx_anggaran_pelaksana_pd AS a ON Y.id_pelaksana_pd = a.id_pelaksana_pd
            INNER JOIN trx_anggaran_kegiatan_pd AS b ON a.id_kegiatan_pd = b.id_kegiatan_pd
            INNER JOIN trx_anggaran_program_pd AS c ON b.id_program_pd = c.id_program_pd
            INNER JOIN trx_anggaran_pelaksana AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
            INNER JOIN trx_anggaran_program AS e ON d.id_anggaran_pemda = e.id_anggaran_pemda
            INNER JOIN trx_anggaran_dokumen AS f ON e.id_dokumen_keu = f.id_dokumen_keu
            WHERE X.tahun_anggaran=' . $request->tahun . '   AND Y.status_pelaksanaan = 0 AND f.id_dokumen_keu='.$request->id_dokumen.'
            ) A ON O.kd_rek_1 = A.kd_rek_1  WHERE O.kd_rek_1 IN (4,5,6)  GROUP BY O.kd_rek_1,O.nama_kd_rek_1');

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
        foreach ($rek1 AS $row) {
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

            $rek2 = DB::SELECT('SELECT O.kd_rek_2,O.nama_kd_rek_2, COALESCE(SUM(A.jml_belanja_forum),0) AS blj 
                    FROM ref_rek_2 O 
                    LEFT OUTER JOIN (SELECT X.tahun_anggaran AS tahun_forum, X.id_rekening_ssh, Z.Kd_Rek_1, Z.Kd_Rek_2,
                    Z.Kd_Rek_3, Z.Kd_Rek_4, Z.Kd_Rek_5, X.jml_belanja AS jml_belanja_forum
                    FROM trx_anggaran_belanja_pd X
                    INNER JOIN trx_anggaran_aktivitas_pd Y ON X.id_aktivitas_pd = Y.id_aktivitas_pd
                    INNER JOIN ref_rek_5 Z ON X.id_rekening_ssh = Z.id_rekening
                    INNER JOIN trx_anggaran_pelaksana_pd AS a ON Y.id_pelaksana_pd = a.id_pelaksana_pd
                    INNER JOIN trx_anggaran_kegiatan_pd AS b ON a.id_kegiatan_pd = b.id_kegiatan_pd
                    INNER JOIN trx_anggaran_program_pd AS c ON b.id_program_pd = c.id_program_pd
                    INNER JOIN trx_anggaran_pelaksana AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                    INNER JOIN trx_anggaran_program AS e ON d.id_anggaran_pemda = e.id_anggaran_pemda
                    INNER JOIN trx_anggaran_dokumen AS f ON e.id_dokumen_keu = f.id_dokumen_keu
                    WHERE X.tahun_anggaran=' . $request->tahun . '  AND Y.status_pelaksanaan = 0 AND f.id_dokumen_keu=' .$request->id_dokumen.') A 
                    ON O.kd_rek_1 = A.kd_rek_1 AND O.kd_rek_2 = A.kd_rek_2  WHERE O.kd_rek_1 IN (4,5,6) AND O.kd_rek_1=' . $row->kd_rek_1 . ' GROUP BY O.kd_rek_2,O.nama_kd_rek_2');
            foreach ($rek2 AS $row2) {
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
                
                $rek3 = DB::SELECT('SELECT O.kd_rek_3,O.nama_kd_rek_3, COALESCE(SUM(A.jml_belanja_forum),0) AS blj 
                    FROM ref_rek_3 O LEFT OUTER JOIN (SELECT X.tahun_anggaran AS tahun_forum, X.id_rekening_ssh, Z.Kd_Rek_1,
                    Z.Kd_Rek_2, Z.Kd_Rek_3, Z.Kd_Rek_4, Z.Kd_Rek_5, X.jml_belanja AS jml_belanja_forum
                    FROM trx_anggaran_belanja_pd X
                    INNER JOIN trx_anggaran_aktivitas_pd Y ON X.id_aktivitas_pd = Y.id_aktivitas_pd
                    INNER JOIN ref_rek_5 Z ON X.id_rekening_ssh = Z.id_rekening
                    INNER JOIN trx_anggaran_pelaksana_pd AS a ON Y.id_pelaksana_pd = a.id_pelaksana_pd
                    INNER JOIN trx_anggaran_kegiatan_pd AS b ON a.id_kegiatan_pd = b.id_kegiatan_pd
                    INNER JOIN trx_anggaran_program_pd AS c ON b.id_program_pd = c.id_program_pd
                    INNER JOIN trx_anggaran_pelaksana AS d ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                    INNER JOIN trx_anggaran_program AS e ON d.id_anggaran_pemda = e.id_anggaran_pemda
                    INNER JOIN trx_anggaran_dokumen AS f ON e.id_dokumen_keu = f.id_dokumen_keu
                    WHERE X.tahun_anggaran=' . $request->tahun . '   AND Y.status_pelaksanaan = 0 AND f.id_dokumen_keu='. $request->id_dokumen.'
                    ) A ON O.kd_rek_1 = A.kd_rek_1 AND O.kd_rek_2 = A.kd_rek_2 AND O.kd_rek_3 = A.kd_rek_3 
					WHERE O.kd_rek_1 IN (4,5,6) AND O.kd_rek_1=' . $row->kd_rek_1 . ' AND O.kd_rek_2=' . $row2->kd_rek_2 . ' 
					GROUP BY O.kd_rek_3,O.nama_kd_rek_3');
                
                foreach ($rek3 AS $row3) {
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
        PDF::Output('RingkasApbd-' . $request->tahun . '.pdf', 'I');
    }

public function PrakiraanMaju(Request $request)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();

        $countrow = 0;
        $totalrow = 30;
        $id_unit = $request->sub_unit;
        $tahun1 = $request->tahun + 1;
        $pemda = Session::get('xPemda');
        $nm_unit = "";
        $hitung = 0;
        if ($sub_unit < 1) {
            $Unit = DB::SELECT('SELECT g.kd_urusan, f.kd_bidang,e.kd_unit,d.id_sub_unit,d.kd_sub, g.nm_urusan, f.nm_bidang, e.nm_unit, d.nm_sub FROM trx_forum_skpd a
                    INNER JOIN trx_forum_skpd_program b ON a.id_forum_program=b.id_forum_program
                    INNER JOIN trx_forum_skpd_pelaksana c ON a.id_forum_skpd=c.id_aktivitas_forum
                    INNER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
                    INNER JOIN ref_unit e ON d.id_unit=e.id_unit
                    INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                    INNER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
                    GROUP BY g.kd_urusan, f.kd_bidang,e.kd_unit,d.id_sub_unit,d.kd_sub, g.nm_urusan, f.nm_bidang, e.nm_unit, d.nm_sub ');
        } ELSE {
            $Unit = DB::SELECT('SELECT g.kd_urusan, f.kd_bidang,e.kd_unit,d.id_sub_unit,d.kd_sub, g.nm_urusan, f.nm_bidang, e.nm_unit, d.nm_sub
                    FROM trx_forum_skpd a
                    INNER JOIN trx_forum_skpd_program b ON a.id_forum_program=b.id_forum_program
                    INNER JOIN trx_forum_skpd_pelaksana c ON a.id_forum_skpd=c.id_aktivitas_forum
                    INNER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
                    INNER JOIN ref_unit e ON d.id_unit=e.id_unit
                    INNER JOIN  ref_bidang f ON e.id_bidang=f.id_bidang
                    INNER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan WHERE c.id_sub_unit=' . $request->sub_unit . '
                    GROUP BY g.kd_urusan, f.kd_bidang,e.kd_unit,d.id_sub_unit,d.kd_sub, g.nm_urusan, f.nm_bidang, e.nm_unit, d.nm_sub');
        }

        PDF::SetFont('helvetica', '', 6);

        $header = array( 'Kode', 'Program/Kegiatan', 'Lokasi Detail', 'Indikator Program/Kegiatan', 'Rencana tahun N',
            'Catatan Penting', 'Prakiraan Maju Tahun N+1' );

        $header2 = array( '', '', '', '', 'Target Capaian Kinerja', 'Kebutuhan Dana', 'Sumber Dana', '',
            'Target Capaian Kinerja', 'Kebutuhan Dana' );

        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0);
        
        PDF::SetFont('helvetica', 'B', 10);

        PDF::Cell('275', 5, $pemda, 1, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Cell('275', 5, 'KOMPILASI KEGIATAN RENCANA KINERJA', 1, 0, 'C', 0);
        PDF::Ln();
        PDF::Ln();
        $countrow ++;
        PDF::SetFont('', 'B');
        PDF::SetFont('helvetica', 'B', 6);
        PDF::SetLineStyle(array(
            'width' => 0.1,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0.1,
            'color' => array( 0, 0, 0
            )
        )); 

        $wh = array( 20, 30, 45, 45,70, 20, 45 );
        $wh2 = array( 20, 30, 45, 45, 20, 25, 25, 20, 20, 25 );
        $w = array( 275 ); // unit
        $w1 = array( 20, 120, 20, 25, 25, 20, 20, 25 ); // prog
        $w2 = array( 20, 3, 27, 45, 45, 20, 25, 25, 20, 20, 25 ); // keg
        $w3 = array( 20, 30, 45, 45, 20, 25, 25, 20, 20, 25 ); // indprog
        $w4 = array(20,30,45,3,42,20,25,25,20,20,25); // indkeg

        PDF::SetFillColor(224, 235, 255);
        PDF::SetTextColor(0);
        PDF::SetFont('helvetica', '', 6);

        $fill = 0;
        foreach ($Unit AS $row) {            
            $nm_unit = $nm_unit . $row->nm_unit;            
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
            PDF::Cell('30', 5, 'Sub Perangkat Daerah', 'LB', 0, 'L', 0);
            PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
            PDF::Cell('15', 5, $row->kd_urusan . '.' . $row->kd_bidang . '.' . $row->kd_unit . '.' . $row->kd_sub, 'B', 0, 'L', 0);
            PDF::Cell('225', 5, $row->nm_sub, 'RB', 0, 'L', 0);
            PDF::Ln();
            $countrow ++;
            PDF::Ln();
            PDF::SetFont('helvetica', 'B', 6);
            $num_headers = count($header);
            for ($i = 0; $i < $num_headers; ++ $i) {
                PDF::SetFont('helvetica', 'B', 7);
                if ($i == 4 || $i == 6) {
                    PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
                } ELSE {
                    PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                }
            }
            PDF::Ln();
            $countrow ++;
            $countrow ++;
            $num_headers2 = count($header2);
            for ($i = 0; $i < $num_headers2; ++ $i) {
                PDF::SetFont('helvetica', 'B', 7);
                PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
            }
            PDF::Ln();
            $countrow ++;
            $countrow ++;
            
            if ($countrow >= $totalrow) {
                PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                PDF::AddPage('L');
                $countrow = 0;
                for ($i = 0; $i < $num_headers; ++ $i) {
                    PDF::SetFont('helvetica', 'B', 7);
                    if ($i == 4 || $i == 6) {
                        PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
                    } ELSE {
                        PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                    }
                }
                PDF::Ln();
                $countrow ++;
                $countrow ++;
                $num_headers2 = count($header2);
                for ($i = 0; $i < $num_headers2; ++ $i) {
                    PDF::SetFont('helvetica', 'B', 7);
                    PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
                }
                PDF::Ln();
                $countrow ++;
                $countrow ++;
            }

            $program = DB::SELECT(' SELECT g.kd_urusan AS ur_unit, g.kd_bidang AS bid_unit, c.kd_unit, c.nm_unit,e.uraian_program AS uraian_program_renstra,
                d.id_forum_program, f.kd_urusan AS ur_pro, f.kd_bidang AS bid_pro, e.kd_program,
                SUM(i.pagu_aktivitas_forum) AS pagu_program, k.pagu_tahun_program,
                CASE a.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" END AS status_program
                FROM trx_forum_skpd_program a
                INNER JOIN trx_forum_skpd d ON a.id_forum_program=d.id_forum_program
                INNER JOIN ref_program e ON a.id_program_ref=e.id_program
                INNER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
                INNER JOIN trx_forum_skpd_pelaksana h ON d.id_forum_skpd=h.id_aktivitas_forum
                INNER JOIN trx_forum_skpd_aktivitas i ON h.id_pelaksana_forum=i.id_forum_skpd
                INNER JOIN ref_sub_unit j ON h.id_sub_unit=j.id_sub_unit
                INNER JOIN ref_unit c ON j.id_unit=c.id_unit
                INNER JOIN ref_bidang g ON c.id_bidang = g.id_bidang
                LEFT OUTER JOIN  (SELECT id_program_renstra, pagu_tahun_program FROM trx_rkpd_renstra WHERE tahun_rkpd=' . $tahun1 . '  
                    GROUP BY id_program_renstra, pagu_tahun_program) k ON a.id_program_renstra=k.id_program_renstra
                WHERE  h.id_sub_unit=' . $row->id_sub_unit . '  AND a.jenis_belanja=0 AND a.tahun_forum=' . $request->tahun . '
                GROUP BY g.kd_urusan , g.kd_bidang , c.kd_unit, c.nm_unit,e.uraian_program ,
                d.id_forum_program, f.kd_urusan ,k.pagu_tahun_program, f.kd_bidang , e.kd_program,a.status_data ');
            foreach ($program AS $row2) {
                $height1 = ceil((strlen($row2->ur_pro . '.' . $row2->bid_pro . '  ' . $row2->ur_unit . '.' . $row2->bid_unit . '.' . $row2->kd_unit . '.' . $row2->kd_program) / 20) * 6);
                $height2 = ceil((strlen($row2->uraian_program_renstra) / 120) * 6);
                $height4 = ceil((strlen($row2->pagu_program) / 25) * 6);
                $height5 = ceil((strlen($row2->pagu_tahun_program) / 25) * 6);                
                $maxhigh = array( $height1, $height2, $height4, $height5 );
                $height = max($maxhigh);
                PDF::SetFont('helvetica', 'B', 6);
                $kode = "";
                if (strlen($row2->kd_program) == 2) {
                    $kode = $row2->ur_pro . '.' . $row2->bid_pro . '  ' . $row2->ur_unit . '.' . $row2->bid_unit . '.' . $row2->kd_unit . ' ' . $row2->kd_program;
                } ELSE {
                    $kode = $row2->ur_pro . '.' . $row2->bid_pro . '  ' . $row2->ur_unit . '.' . $row2->bid_unit . '.' . $row2->kd_unit . ' 0' . $row2->kd_program;
                }
                
                if ($countrow >= $totalrow) {
                    PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                    PDF::AddPage('L');
                    $countrow = 0;
                    for ($i = 0; $i < $num_headers; ++ $i) {
                        PDF::SetFont('helvetica', 'B', 7);
                        if ($i == 4 || $i == 6) {
                            PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
                        } ELSE {
                            PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                        }
                    }
                    PDF::Ln();
                    $countrow ++;
                    $countrow ++;
                    $num_headers2 = count($header2);
                    for ($i = 0; $i < $num_headers2; ++ $i) {
                        PDF::SetFont('helvetica', 'B', 7);
                        PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
                    }
                    PDF::Ln();
                    $countrow ++;
                    $countrow ++;
                }

                $indikatorprog = DB::SELECT('SELECT  DISTINCT d.uraian_program_renstra,b.uraian_indikator_program,
                        b.tolok_ukur_indikator,b.target_renstra,b.target_renja,
                        CASE b.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" END AS status_indikator,f.singkatan_satuan
                        FROM  trx_forum_skpd_program d
                        INNER JOIN trx_forum_skpd_program_indikator b ON d.id_forum_program=b.id_forum_program
                        INNER JOIN trx_forum_skpd g ON d.id_forum_program=g.id_forum_program
                        INNER JOIN trx_forum_skpd_pelaksana h ON g.id_forum_skpd=h.id_aktivitas_forum
                        LEFT OUTER JOIN ref_indikator e ON b.kd_indikator=e.id_indikator
                        LEFT OUTER JOIN ref_satuan f ON e.id_satuan_output=f.id_satuan                    
                        WHERE h.id_sub_unit=' . $row->id_sub_unit . ' AND d.id_forum_program=' . $row2->id_forum_program);
                
                $a = 0;
                foreach ($indikatorprog AS $row5) {                    
                    PDF::SetFont('helvetica', 'B', 6);
                    $height1 = ceil((strlen($row5->uraian_indikator_program) / 45) * 6);
                    $height2 = ceil((strlen($row5->uraian_program_renstra) / 75) * 6);
                    $height3 = ceil((strlen($row5->target_renja . ' ' . $row5->singkatan_satuan) / 20) * 6);                    
                    $maxhigh = array( $height1, $height2, $height3);
                    $height = max($maxhigh);
                    
                    if ($countrow >= $totalrow) {
                        PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                        PDF::AddPage('L');
                        $countrow = 0;
                        for ($i = 0; $i < $num_headers; ++ $i) {
                            PDF::SetFont('helvetica', 'B', 7);
                            if ($i == 4 || $i == 6) {
                                PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
                            } ELSE {
                                PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                            }
                        }
                        PDF::Ln();
                        $countrow ++;
                        $countrow ++;
                        $num_headers2 = count($header2);
                        for ($i = 0; $i < $num_headers2; ++ $i) {
                            PDF::SetFont('helvetica', 'B', 7);
                            PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
                        }
                        PDF::Ln();
                        $countrow ++;
                        $countrow ++;
                    }
                    if ($a == 0) {
                        PDF::MultiCell(20, $height, $kode, 'LT', 'L', 0, 0);
                        PDF::MultiCell(75, $height, $row2->uraian_program_renstra, 'LT', 'L', 0, 0);
                        PDF::MultiCell(45, $height, $row5->uraian_indikator_program, 'LT', 'L', 0, 0);
                        PDF::MultiCell(20, $height, $row5->target_renja . ' ' . $row5->singkatan_satuan, 'LT', 'L', 0, 0);
                        PDF::MultiCell(25, $height, number_format($row2->pagu_program, 2, ',', '.'), 'LT', 'R', 0, 0);
                        PDF::MultiCell(25, $height, '', 'LT', 'L', 0, 0);
                        PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                        PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                        PDF::MultiCell(25, $height, number_format($row2->pagu_tahun_program, 2, ',', '.'), 'LRT', 'R', 0, 0);
                    } ELSE {
                        PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                        PDF::MultiCell(75, $height, '', 'LT', 'L', 0, 0);
                        PDF::MultiCell(45, $height, $row5->uraian_indikator_program, 'LT', 'L', 0, 0);
                        PDF::MultiCell(20, $height, $row5->target_renja . ' ' . $row5->singkatan_satuan, 'LT', 'L', 0, 0);
                        PDF::MultiCell(25, $height, '', 'LT', 'R', 0, 0);
                        PDF::MultiCell(25, $height, '', 'LT', 'L', 0, 0);
                        PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                        PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                        PDF::MultiCell(25, $height, '', 'LRT', 'R', 0, 0);
                    }
                    $a ++;
                    PDF::Ln();
                    $countrow = $countrow + $height / 5;
                }

                $kegiatan = DB::SELECT('SELECT  a.id_forum_program,n.sumber_dana,a.id_forum_skpd,h.id_pelaksana_forum,coalesce(m.lokasi,"Belum Ada") AS lokasi,
                    e.uraian_program,  k.nm_kegiatan AS uraian_kegiatan_renstra, k.kd_kegiatan, SUM(i.pagu_aktivitas_forum) AS pagu_kegiatan, k.pagu_tahun_kegiatan,
                    CASE a.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" END AS status_kegiatan
                    FROM trx_forum_skpd a
                    INNER JOIN trx_forum_skpd_program d ON a.id_forum_program=d.id_forum_program
                    INNER JOIN ref_program e ON d.id_program_ref=e.id_program
                    INNER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
                    INNER JOIN trx_forum_skpd_pelaksana h ON a.id_forum_skpd=h.id_aktivitas_forum
                    INNER JOIN trx_forum_skpd_aktivitas i ON h.id_pelaksana_forum=i.id_forum_skpd                    
                    INNER JOIN ref_sub_unit j ON h.id_sub_unit=j.id_sub_unit
                    INNER JOIN ref_unit c ON j.id_unit=c.id_unit
                    INNER JOIN ref_bidang g ON c.id_bidang = g.id_bidang
                    LEFT OUTER JOIN ref_kegiatan k ON a.id_kegiatan_ref=k.id_kegiatan
                    LEFT OUTER JOIN (SELECT DISTINCT group_concat(c.nama_lokasi) AS lokasi, a.id_aktivitas_forum FROM trx_forum_skpd_aktivitas a
                        LEFT OUTER JOIN trx_forum_skpd_lokasi b ON a.id_aktivitas_forum=b.id_pelaksana_forum
                        INNER JOIN ref_lokasi c ON b.id_lokasi=c.id_lokasi                    
                        GROUP BY a.id_aktivitas_forum) m ON i.id_aktivitas_forum=m.id_aktivitas_forum
                    LEFT OUTER JOIN  (SELECT id_kegiatan_renstra, pagu_tahun_kegiatan FROM trx_rkpd_renstra WHERE tahun_rkpd=' . $tahun1 . '  GROUP BY id_kegiatan_renstra, pagu_tahun_kegiatan) k ON a.id_kegiatan_renstra=k.id_kegiatan_renstra
                    LEFT OUTER JOIN (SELECT GROUP_CONCAT(a.uraian_sumber_dana) AS sumber_dana,a.id_forum_skpd
                        FROM (SELECT b.uraian_sumber_dana, a.id_forum_skpd FROM trx_forum_skpd_aktivitas a
                        INNER JOIN ref_sumber_dana b ON a.sumber_dana=b.id_sumber_dana
                        GROUP BY a.id_forum_skpd,b.uraian_sumber_dana) a
                    GROUP BY a.id_forum_skpd) n ON a.id_forum_skpd=n.id_forum_skpd
                    WHERE h.id_sub_unit=' . $row->id_sub_unit . ' AND a.id_forum_program=' . $row2->id_forum_program . ' 
                    GROUP BY a.id_forum_program,n.sumber_dana,k.pagu_tahun_kegiatan,a.status_data,
                    a.id_forum_skpd,h.id_pelaksana_forum,a.uraian_kegiatan_forum,m.lokasi,e.uraian_program,k.nm_kegiatan,d.status_data, k.kd_kegiatan');
                
                foreach ($kegiatan AS $row3) {
                    PDF::SetFont('helvetica', '', 6);
                    $height = ceil((strlen($row3->uraian_kegiatan_renstra) / 37)) * 5;
                    $kode2 = "";
                    if (strlen($row3->kd_kegiatan) == 2) {
                        $kode2 = $kode . '.' . $row3->kd_kegiatan;
                    } ELSE {
                        $kode2 = $kode . '.0' . $row3->kd_kegiatan;
                    }

                    if ($countrow >= $totalrow) {
                        PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                        PDF::AddPage('L');
                        $countrow = 0;
                        for ($i = 0; $i < $num_headers; ++ $i) {
                            PDF::SetFont('helvetica', 'B', 7);
                            if ($i == 4 || $i == 6) {
                                PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
                            } ELSE {
                                PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                            }
                        }
                        PDF::Ln();
                        $countrow ++;
                        $countrow ++;
                        $num_headers2 = count($header2);
                        for ($i = 0; $i < $num_headers2; ++ $i) {
                            PDF::SetFont('helvetica', 'B', 7);
                            PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
                        }
                        PDF::Ln();
                        $countrow ++;
                        $countrow ++;
                    }
                    $indikator = DB::SELECT('SELECT  DISTINCT d.uraian_kegiatan_forum,b.uraian_indikator_kegiatan,
                            b.tolok_ukur_indikator,b.target_renstra,b.target_renja,f.singkatan_satuan,
                            CASE b.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" END AS status_indikator
                            FROM  trx_forum_skpd d
                            INNER JOIN trx_forum_skpd_kegiatan_indikator b ON d.id_forum_skpd=b.id_forum_skpd
                            INNER JOIN trx_forum_skpd_pelaksana h ON d.id_forum_skpd=h.id_aktivitas_forum
                            LEFT OUTER JOIN ref_indikator e ON b.kd_indikator=e.id_indikator
                            LEFT OUTER JOIN ref_satuan f ON e.id_satuan_output=f.id_satuan                        
                             WHERE h.id_sub_unit=' . $row->id_sub_unit . ' AND d.id_forum_program=' . $row2->id_forum_program . ' AND d.id_forum_skpd=' . $row3->id_forum_skpd);
                    $b = 0;
                    foreach ($indikator AS $row4) {
                        PDF::SetFont('helvetica', '', 6);
                        $height1 = ceil((strlen($row4->uraian_indikator_kegiatan) / 42)) * 5;
                        $height2 = ceil((strlen($row3->uraian_kegiatan_renstra) / 27)) * 5;
                        $height3 = ceil((strlen($row3->lokasi) / 45)) * 5;
                        $height4 = ceil((strlen($row3->sumber_dana) / 20)) * 5;
                        $height = max($height1, $height2, $height3, $height4);
                        if ($countrow >= $totalrow) {
                            PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                            PDF::AddPage('L');
                            $countrow = 0;
                            for ($i = 0; $i < $num_headers; ++ $i) {
                                PDF::SetFont('helvetica', 'B', 7);
                                if ($i == 4 || $i == 6) {
                                    PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
                                } ELSE {
                                    PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                                }
                            }
                            PDF::Ln();
                            $countrow ++;
                            $countrow ++;
                            $num_headers2 = count($header2);
                            for ($i = 0; $i < $num_headers2; ++ $i) {
                                PDF::SetFont('helvetica', 'B', 7);
                                PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
                            }
                            PDF::Ln();
                            $countrow ++;
                            $countrow ++;
                        }
                        if ($b == 0) {
                            PDF::MultiCell(20, $height, $kode2, 'LT', 'L', 0, 0);
                            PDF::MultiCell(3, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(27, $height, $row3->uraian_kegiatan_renstra, 'T', 'L', 0, 0);
                            PDF::MultiCell(45, $height, $row3->lokasi, 'LT', 'L', 0, 0);
                            PDF::MultiCell(3, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(42, $height, $row4->uraian_indikator_kegiatan, 'T', 'L', 0, 0);
                            PDF::MultiCell(20, $height, $row4->target_renja . ' ' . $row4->singkatan_satuan, 'LT', 'L', 0, 0);
                            PDF::MultiCell(25, $height, number_format($row3->pagu_kegiatan, 2, ',', '.'), 'LT', 'R', 0, 0);
                            PDF::MultiCell(25, $height, $row3->sumber_dana, 'LT', 'L', 0, 0);
                            PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(25, $height, number_format($row3->pagu_tahun_kegiatan, 2, ',', '.'), 'LRT', 'R', 0, 0);
                        } ELSE {
                            PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(3, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(72, $height, '', 'T', 'L', 0, 0);
                            PDF::MultiCell(3, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(42, $height, $row4->uraian_indikator_kegiatan, 'T', 'L', 0, 0);
                            PDF::MultiCell(20, $height, $row4->target_renja . ' ' . $row4->singkatan_satuan, 'LT', 'L', 0, 0);
                            PDF::MultiCell(25, $height, '', 'LT', 'R', 0, 0);
                            PDF::MultiCell(25, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                            PDF::MultiCell(25, $height, number_format($row3->pagu_tahun_kegiatan, 2, ',', '.'), 'LRT', 'R', 0, 0);
                        }
                        $b ++;
                        PDF::Ln();
                        $countrow = $countrow + $height / 5;
                    }
                }
            }
        }
        PDF::Cell(array_SUM($w), 0, '', 'T');
        PDF::Output('PrakiraanMaju-' . $sub_unit . '.pdf', 'I');
    }
    
    public function SasaranProgramRenjaFinal(Request $request)
    {
        Template::settingPageLandscape();
        Template::headerLandscape();

        PDF::SetFont('helvetica', '', 6);
        
        $data_tujuan = DB::SELECT('SELECT a.id_tujuan_rpjmd, a.uraian_tujuan_rpjmd FROM trx_rpjmd_tujuan AS a
                        INNER JOIN trx_rpjmd_sasaran b ON a.id_tujuan_rpjmd=b.id_tujuan_rpjmd
                        INNER JOIN trx_rpjmd_program c ON b.id_sasaran_rpjmd=c.id_sasaran_rpjmd
                        INNER JOIN trx_renstra_program d ON c.id_program_rpjmd=d.id_program_rpjmd
                        INNER JOIN trx_anggaran_program_pd e ON d.id_program_renstra=e.id_program_renstra
                        WHERE e.id_program_pd='.$request->id_program.' GROUP BY a.id_tujuan_rpjmd, a.uraian_tujuan_rpjmd');
        
        $data = DB::SELECT('SELECT A.id_sasaran_rpjmd, C.id_program_renstra, N.id_kegiatan_renstra, 
                        A.uraian_sasaran_rpjmd, C.uraian_program_renstra, N.uraian_kegiatan_renstra, H.nm_unit,                 
                        (   SELECT count(H.id_program_rpjmd) FROM trx_renstra_kegiatan F
                            INNER JOIN trx_renstra_program G ON F.id_program_renstra=G.id_program_renstra
                            INNER JOIN trx_rpjmd_program H ON G.id_program_rpjmd=H.id_program_rpjmd
                            INNER JOIN trx_rpjmd_sasaran I ON H.id_sasaran_rpjmd = I.id_sasaran_rpjmd
                            WHERE A.id_sasaran_rpjmd = I.id_sasaran_rpjmd ) AS level_1,
                        (   SELECT  count(L.id_program_rpjmd) FROM trx_renstra_kegiatan J
                            INNER JOIN trx_renstra_program K ON J.id_program_renstra=K.id_program_renstra
                            INNER JOIN trx_rpjmd_program L ON K.id_program_rpjmd=L.id_program_rpjmd
                            INNER JOIN trx_rpjmd_sasaran M ON L.id_sasaran_rpjmd = M.id_sasaran_rpjmd
                            WHERE C.id_program_renstra = K.id_program_renstra ) AS level_2
                        FROM trx_rpjmd_sasaran A
                        INNER JOIN trx_rpjmd_program B ON A.id_sasaran_rpjmd=B.id_sasaran_rpjmd
                        INNER JOIN trx_renstra_program C ON B.id_program_rpjmd=C.id_program_rpjmd
                        INNER JOIN trx_renstra_kegiatan N ON C.id_program_renstra=N.id_program_renstra
                        INNER JOIN trx_renstra_sasaran D ON C.id_sasaran_renstra=D.id_sasaran_renstra
                        INNER JOIN trx_renstra_tujuan E ON D.id_tujuan_renstra=E.id_tujuan_renstra
                        INNER JOIN trx_renstra_misi F ON E.id_misi_renstra=F.id_misi_renstra
                        INNER JOIN trx_renstra_visi G ON F.id_visi_renstra=G.id_visi_renstra
                        INNER JOIN ref_unit H ON G.id_unit=H.id_unit
                        INNER JOIN trx_anggaran_program_pd I ON C.id_program_renstra=I.id_program_renstra
                        WHERE I.id_program_pd='.$request->id_program.'
                        GROUP BY A.id_sasaran_rpjmd, C.id_program_renstra, N.id_kegiatan_renstra, A.uraian_sasaran_rpjmd,
                        C.uraian_program_renstra, N.uraian_kegiatan_renstra, level_1, level_2, H.nm_unit
                        ORDER BY A.id_sasaran_rpjmd ASC, C.id_program_renstra ASC');
       
        $jum_level_1 = 1;
        $jum_level_2 = 1;
        
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
        $nm_unit = '';
        foreach ($data_tujuan AS $tujuan) {
            $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">Matrik Sasaran Program Renja Final </div>';
            $html .= '<div style="text-align: left; font-size:12px; font-weight: bold;">Tujuan : ' . $tujuan->uraian_tujuan_rpjmd . '</div>';
        }
        ;
        $html .= '<br>';
        $html .= '<table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>            
                    <th style="text-align: center; vertical-align:middle">Sasaran</th>
                    <th style="text-align: center; vertical-align:middle">Program</th>
                    <th style="text-align: center; vertical-align:middle">Kegiatan</th>
                </tr>
                </thead>
            <tbody >';
        
        foreach ($data AS $row) {
            $html .= '<tr nobr="true">';
            
            if ($jum_level_1 <= 1) {
                $html .= '<td rowspan="' . $row->level_1 . '" style="padding: 50px; text-align: justify;"><div><span style="font-weight: bold;">' . $row->uraian_sasaran_rpjmd . '</span></div>';
                $html .= '<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
                $sasaran_ind = DB::SELECT('SELECT (@id :=@id + 1) AS urut, a.thn_id, a.no_urut, a.id_sasaran_rpjmd, a.id_indikator_sasaran_rpjmd,
                        a.id_perubahan, a.kd_indikator, a.uraian_indikator_sasaran_rpjmd, a.tolok_ukur_indikator, c.uraian_satuan, a.sumber_data,
                        COALESCE (b.nm_indikator, "Kosong") AS nm_indikator, a.created_at, a.updated_at,
                        CASE(h.tahun_5-'.$request->tahun.') WHEN 4 THEN a.angka_tahun1
                        WHEN 3 THEN a.angka_tahun2
                        WHEN 2 THEN a.angka_tahun3
                        WHEN 1 THEN a.angka_tahun4
                        ELSE a.angka_tahun5 END AS angka_tahun
                        FROM trx_rpjmd_sasaran_indikator AS a
                        LEFT OUTER  JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                        LEFT OUTER JOIN ref_satuan c ON b.id_satuan_output=c.id_satuan
                        INNER JOIN trx_rpjmd_sasaran d ON a.id_sasaran_rpjmd=d.id_sasaran_rpjmd
                        INNER JOIN trx_rpjmd_tujuan e ON d.id_tujuan_rpjmd=e.id_tujuan_rpjmd
                        INNER JOIN trx_rpjmd_misi f ON e.id_misi_rpjmd=f.id_misi_rpjmd
                        INNER JOIN trx_rpjmd_visi g ON f.id_visi_rpjmd=g.id_visi_rpjmd
                        INNER JOIN ref_tahun h ON g.id_rpjmd=h.id_rpjmd,(SELECT @id := 0) x 
                        WHERE a.id_sasaran_rpjmd=' . $row->id_sasaran_rpjmd);
                $html .= '<table border="0.1" cellpadding="2" cellspacing="0">';
                foreach ($sasaran_ind AS $sasarans) {
                    $html .= '<tr><td width="10%" style="text-align: center;"> ' . $sasarans->urut . ' </td>';
                    $html .= '<td width="60%" style="text-align: justify;">' . $sasarans->uraian_indikator_sasaran_rpjmd . '</td>';
                    $html .= '<td width="20%" style="text-align: right;">' . number_format($sasarans->angka_tahun, 2, ',', '.') . '</td>';
                    $html .= '<td width="10%" style="text-align: left;">' . $sasarans->uraian_satuan. '</td></tr>';
                }
                $html .= '</table>';
                $html .= '</td>';
                $jum_level_1 = $row->level_1;
            } ELSE {
                $jum_level_1 = $jum_level_1 - 1;
            }
            ;
            if ($jum_level_2 <= 1) {
                $html .= '<td rowspan="' . $row->level_2 . '" style="padding: 50px; text-align: justify;"><div><span style="font-weight: bold;">' . $row->uraian_program_renstra . '</span></div>';
                $html .= '<div><span style="font-weight: bold; font-style: italic">UNIT :'.$row->nm_unit.'</span></div>';
                $html .= '<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
                $program_ind = DB::SELECT('SELECT (@id :=@id + 1) AS urut, a.uraian_indikator_program, a.tolok_ukur_indikator, a.target_renja, 
                    a.sumber_data, COALESCE (b.nm_indikator, "Kosong") AS nm_indikator, c.uraian_satuan
                    FROM trx_anggaran_prog_indikator_pd AS a
                    INNER JOIN trx_anggaran_program_pd AS d ON a.id_program_pd=d.id_program_pd
                    LEFT OUTER  JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                    LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output=c.id_satuan, (SELECT @id := 0) x  
                    WHERE a.id_program_renstra=' . $row->id_program_renstra.' AND a.tahun_anggaran='.$request->tahun.' AND d.id_dokumen_keu='.$request->id_dokumen);
                $html .= '<table border="0.1" cellpadding="2" cellspacing="0">';
                foreach ($program_ind AS $programs) {
                    $html .= '<tr><td width="10%" style="text-align: center;"> ' . $programs->urut . ' </td>';
                    $html .= '<td width="60%" style="text-align: justify;">' . $programs->uraian_indikator_program . '</td>';
                    $html .= '<td width="20%" style="text-align: right;">' . number_format($programs->target_renja, 2, ',', '.') . '</td>';
                    $html .= '<td width="10%" style="text-align: left;">' . $programs->uraian_satuan. '</td></tr>';
                }
                $html .= '</table>';
                $html .= '</td>';                
                $jum_level_2 = $row->level_2;
            } ELSE {
                $jum_level_2 = $jum_level_2 - 1;
            }
            ;
            $html .= '<td style="padding: 50px; text-align: justify;"><div><span style="font-weight: bold;">' . $row->uraian_kegiatan_renstra . '</span></div>';
            
            $html .= '<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
            $kegiatan_ind = DB::SELECT('SELECT (@id :=@id + 1) AS urut, a.uraian_indikator_kegiatan, a.tolok_ukur_indikator, a.target_renja,
                        a.sumber_data ,COALESCE (b.nm_indikator, "Kosong") AS nm_indikator, c.uraian_satuan
                        FROM trx_anggaran_keg_indikator_pd AS a
                        INNER JOIN trx_anggaran_kegiatan_pd AS d ON a.id_kegiatan_pd=d.id_kegiatan_pd
                        INNER JOIN trx_anggaran_program_pd AS e ON d.id_program_pd=e.id_program_pd
                        LEFT OUTER  JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                        LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output=c.id_satuan ,(SELECT @id := 0) x  
                        WHERE d.id_kegiatan_renstra=' . $row->id_kegiatan_renstra.' AND a.tahun_anggaran='.$request->tahun.' AND e.id_dokumen_keu='.$request->id_dokumen);
            $html .= '<table border="0.1" cellpadding="2" cellspacing="0">';
            foreach ($kegiatan_ind AS $kegiatans) {
                $html .= '<tr><td width="10%" style="text-align: center;"> ' . $kegiatans->urut . ' </td>';
                $html .= '<td width="60%" style="text-align: justify;">' . $kegiatans->uraian_indikator_kegiatan . '</td>';
                $html .= '<td width="20%" style="text-align: right;">' . number_format($kegiatans->target_renja, 2, ',', '.') . '</td>';
                $html .= '<td width="10%" style="text-align: left;">' . $kegiatans->uraian_satuan. '</td></tr>';
            }
            $html .= '</table>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        ;
        
        $html .= '</tbody></table>';
        
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();

        PDF::Output('MatrikSasaranProgramRenjaFinal-' . $nm_unit . '.pdf', 'I');
    }
    


} //endFile

