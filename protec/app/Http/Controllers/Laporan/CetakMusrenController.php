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
use PhpParser\Node\Stmt\Foreach_;


class CetakMusrenController extends Controller
{
    
    public function UsulanPerUnit($id_unit,$tahun)
    {
        
        $countrow=0;
        $totalrow=30;
        if($id_unit<1)
        {$Unit = DB::SELECT('SELECT d.id_unit,d.nm_unit FROM trx_musrencam a
            INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
            INNER JOIN ref_unit d ON b.id_unit=d.id_unit
            WHERE tahun_musren='.$tahun.' GROUP BY d.nm_unit');}
        ELSE
        {$Unit = DB::SELECT('SELECT d.id_unit,d.nm_unit FROM trx_musrencam a
            INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
            INNER JOIN ref_unit d ON b.id_unit=d.id_unit
            WHERE b.id_unit='.$id_unit.' AND tahun_musren='.$tahun.' GROUP BY d.id_unit,d.nm_unit');}
        
        
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('SSH Kelompok');
        
        // set default header data
        PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
        
        // set header AND footer fonts
        PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
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
        //     require_once(dirname(__FILE__).'/lang/eng.php');
        //     $pdf->setLanguageArray($l);
        // }
        
        // ---------------------------------------------------------
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        // add a page
        PDF::AddPage('L');
        
        // column titles
        $header = array('SKPD/Program','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra','Pagu Program','Status Program');
        
        // Colors, line width AND bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0);
        PDF::SetFont('helvetica', 'B', 10);
        foreach($Unit as $row) {
        //Header
        PDF::Cell('275', 5, Session::get('xPemda') , 1, 0, 'C', 0);
        PDF::Ln();
        $countrow++;
        PDF::Cell('275', 5, 'Daftar Usulan Desa Musrenbang RKPD per Kecamatan', 1, 0, 'C', 0);
        PDF::Ln();
        $countrow++;
        PDF::Cell(275, 5, $row->nm_unit, 1, 0, 'C', 0);
        PDF::Ln();
        $countrow++;
        PDF::Cell(275, 5, "Tahun ".$tahun, 1, 0, 'C', 0);
        PDF::Ln();
        $countrow++;
        PDF::Ln();
        $countrow++;

        PDF::SetFont('', 'B');
        PDF::SetFont('helvetica', 'B', 6);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
        // Header Column
        
        $wh = array(45,30,30,20,20,20,20,20,20);
        $w = array(225);
        $w1 = array(5,40,120,20,20,20);
        $w2 = array(45,30,30,20,20,20,60);
        
        PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
        PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
        PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
        PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
        PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
        PDF::Ln();
        $countrow++;
        // Color AND font restoration
        
        PDF::SetFillColor(224, 235, 255);
        PDF::SetTextColor(0);
        PDF::SetFont('helvetica', '', 6);
        // Data
        $fill = 0;
       
            
            //$fill=!$fill;
            $kecamatan = DB::SELECT('SELECT g.id_kecamatan,g.nama_kecamatan FROM trx_musrencam a
                                    INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                                    INNER JOIN ref_kecamatan g ON a.id_kecamatan=g.id_kecamatan
                                    WHERE b.id_unit='.$row->id_unit.' AND tahun_musren='.$tahun.'  GROUP BY g.id_kecamatan,g.nama_kecamatan');
            foreach($kecamatan as $row2) {
                PDF::MultiCell(20, 5, '', 1, 'L', 0, 0);
                PDF::MultiCell(100, 5, $row2->nama_kecamatan, 1, 'L', 0, 0);
                PDF::MultiCell(60, 5, '', 1, 'L', 0, 0);
                PDF::MultiCell(40, 5, '', 1, 'L', 0, 0);
                PDF::MultiCell(55, 5, '', 1, 'L', 0, 0);
                
                PDF::Ln();
                $countrow++;
                if($countrow>=$totalrow)
                {
                    PDF::AddPage('L');
                    $countrow=0;
                    PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
                    PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
                    PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
                    PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
                    PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
                    PDF::Ln();
                    $countrow++;
                }
                $kegiatan = DB::SELECT('SELECT CONCAT(f.kd_urusan,".",f.kd_bidang,".",e.kd_program,".",c.kd_kegiatan) as kode, c.nm_kegiatan, b.id_kegiatan_ref
                        FROM trx_musrencam a
                        INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                        INNER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
                        INNER JOIN ref_unit d ON b.id_unit=d.id_unit
                        INNER JOIN ref_program e ON c.id_program=e.id_program
                        INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                        WHERE b.id_unit='.$row->id_unit.' AND tahun_musren='.$tahun.' AND a.id_kecamatan='.$row2->id_kecamatan.' 
                        GROUP BY kode,c.nm_kegiatan,  b.id_kegiatan_ref');
                
                foreach($kegiatan as $row3) {
                    PDF::MultiCell(20, 5, $row3->kode, 1, 'L', 0, 0);
                    PDF::MultiCell(5, 5, '', 'LBT', 'L', 0, 0);
                    PDF::MultiCell(95, 5, $row3->nm_kegiatan, 'RBT', 'L', 0, 0);
                    PDF::MultiCell(60, 5, '', 1, 'L', 0, 0);
                    PDF::MultiCell(40, 5, '', 1, 'L', 0, 0);
                    PDF::MultiCell(55, 5, '', 1, 'L', 0, 0);
                    PDF::Ln();
                    $countrow++;
                    if($countrow>=$totalrow)
                    {
                        PDF::AddPage('L');
                        $countrow=0;
                        PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
                        PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
                        PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
                        PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
                        PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
                        PDF::Ln();
                        $countrow++;
                    }
                    $aktivitas = DB::SELECT('SELECT a.uraian_aktivitas_kegiatan, a.id_musrencam
                            FROM trx_musrencam a
                            INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                            INNER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
                            INNER JOIN ref_unit d ON b.id_unit=d.id_unit
                            INNER JOIN ref_program e ON c.id_program=e.id_program
                            INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                            INNER JOIN ref_kecamatan g ON a.id_kecamatan=g.id_kecamatan
                            WHERE b.id_unit='.$row->id_unit.' AND a.tahun_musren='.$tahun.' AND a.id_kecamatan='.$row2->id_kecamatan.' AND  b.id_kegiatan_ref='.$row3->id_kegiatan_ref.' 
                            GROUP BY a.uraian_aktivitas_kegiatan, a.id_musrencam');
                    $hitung=0;
                    foreach($aktivitas as $row4) {
                        $lokasi = DB::SELECT('SELECT a.uraian_aktivitas_kegiatan,i.nama_desa,j.volume_usulan_1,k.uraian_satuan, CASE j.status_pelaksanaan
                                WHEN 0 THEN "Diterima Tanpa Perubahan"
                                WHEN 1 THEN "Diterima Dengan Perubahan"
                                WHEN 2 THEN "Digabungkan"
                                ELSE "Ditolak" END status
                                FROM trx_musrencam a
                                INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                                INNER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
                                INNER JOIN ref_unit d ON b.id_unit=d.id_unit
                                INNER JOIN ref_program e ON c.id_program=e.id_program
                                INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                                INNER JOIN ref_kecamatan g ON a.id_kecamatan=g.id_kecamatan
                                INNER JOIN trx_musrencam_lokasi h ON a.id_musrencam=h.id_musrencam
                                INNER JOIN ref_desa i ON h.id_desa=i.id_desa
                                INNER JOIN trx_forum_skpd_lokasi j ON h.id_lokasi_musrencam=j.id_lokasi_renja AND j.sumber_data=2
                                INNER JOIN ref_satuan k ON j.id_satuan_1=k.id_satuan                            
                                WHERE b.id_unit='.$row->id_unit.' AND a.tahun_musren='.$tahun.' AND a.id_kecamatan='.$row2->id_kecamatan.' AND a.id_musrencam='.$row4->id_musrencam);
                        
                        foreach($lokasi as $row5) {
                            if($hitung==0)
                            {
                                PDF::MultiCell(20, 5, '', 1, 'L', 0, 0);
                                PDF::MultiCell(10, 5, '', 'LBT', 'L', 0, 0);
                                PDF::MultiCell(90, 5, $row4->uraian_aktivitas_kegiatan, 'RBT', 'L', 0, 0);
                                PDF::MultiCell(60, 5, $row5->nama_desa, 1, 'L', 0, 0);
                                PDF::MultiCell(40, 5, $row5->volume_usulan_1.' '.$row5->uraian_satuan, 1, 'L', 0, 0);
                                PDF::MultiCell(55, 5, $row5->status, 1, 'L', 0, 0);
                                PDF::Ln();
                            }
                            ELSE
                            {
                                PDF::MultiCell(20, 5, '', 1, 'L', 0, 0);
                                PDF::MultiCell(100, 5, '', 1, 'L', 0, 0);
                                PDF::MultiCell(60, 5, $row5->nama_desa, 1, 'L', 0, 0);
                                PDF::MultiCell(40, 5, $row5->volume_usulan_1.' '.$row5->uraian_satuan, 1, 'L', 0, 0);
                                PDF::MultiCell(55, 5, $row5->status, 1, 'L', 0, 0);
                                PDF::Ln();
                                
                                
                            }
                        $countrow++;
                        
                        if($countrow>=$totalrow)
                        {
                            PDF::AddPage('L');
                            $countrow=0;
                            PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
                            PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
                            PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
                            PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
                            PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
                            PDF::Ln();
                            $countrow++;
                        }
                        $hitung++;
                        //$fill=!$fill;
                        }
                    }
                    //$fill=!$fill;
                }
                //$fill=!$fill;
            }
        }
        //PDF::Cell(array_sum($w), 0, '', 'T');
        
        // ---------------------------------------------------------
        
        // close AND output PDF document
        PDF::Output('Musrenbang.pdf', 'I');
    }
  
    public function UsulanPerKecamatan($id_kecamatan,$tahun)
    {
        
        $countrow=0;
        $totalrow=30;
        if($id_kecamatan<1)
        {$Kecamatan = DB::SELECT('SELECT d.id_kecamatan,d.nama_kecamatan FROM trx_musrencam a
            INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
            INNER JOIN ref_kecamatan d ON a.id_kecamatan=d.id_kecamatan
            WHERE tahun_musren='.$tahun.' GROUP BY d.nama_kecamatan');}
        ELSE
        {$Kecamatan = DB::SELECT('SELECT d.id_kecamatan,d.nama_kecamatan FROM trx_musrencam a
                INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                INNER JOIN ref_kecamatan d ON a.id_kecamatan=d.id_kecamatan
                WHERE a.id_kecamatan='.$id_kecamatan.' AND tahun_musren='.$tahun.' GROUP BY d.nama_kecamatan');}
        
        
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('SSH Kelompok');
        
        // set default header data
        PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
        
        // set header AND footer fonts
        PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
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
        //     require_once(dirname(__FILE__).'/lang/eng.php');
        //     $pdf->setLanguageArray($l);
        // }
        
        // ---------------------------------------------------------
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        // add a page
        PDF::AddPage('L');
        
        // column titles
        $header = array('SKPD/Program','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra','Pagu Program','Status Program');
        
        // Colors, line width AND bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0);
        PDF::SetFont('helvetica', 'B', 10);
        foreach($Kecamatan as $row) {
            //Header
            PDF::Cell('275', 5, Session::get('xPemda') , 1, 0, 'C', 0);
            PDF::Ln();
            $countrow++;
            PDF::Cell('275', 5, 'Daftar Usulan Desa Musrenbang RKPD per OPD', 1, 0, 'C', 0);
            PDF::Ln();
            $countrow++;
            PDF::Cell(275, 5, "Kecamatan ".$row->nama_kecamatan, 1, 0, 'C', 0);
            PDF::Ln();
            $countrow++;
            PDF::Cell(275, 5, "Tahun ".$tahun, 1, 0, 'C', 0);
            PDF::Ln();
            $countrow++;
            PDF::Ln();
            $countrow++;
            PDF::SetFont('', 'B');
            PDF::SetFont('helvetica', 'B', 6);
            PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
            // Header Column
            
            $wh = array(45,30,30,20,20,20,20,20,20);
            $w = array(225);
            $w1 = array(5,40,120,20,20,20);
            $w2 = array(45,30,30,20,20,20,60);
            
            PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
            PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
            PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
            PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
            PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
            PDF::Ln();
            $countrow++;
            // Color AND font restoration
            
            PDF::SetFillColor(224, 235, 255);
            PDF::SetTextColor(0);
            PDF::SetFont('helvetica', '', 6);
            // Data
            $fill = 0;
            
            
            //$fill=!$fill;
            $unit = DB::SELECT('SELECT g.id_unit,g.nm_unit FROM trx_musrencam a
                                    INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                                    INNER JOIN ref_unit g ON b.id_unit=g.id_unit
                                    WHERE a.id_kecamatan='.$row->id_kecamatan.' AND tahun_musren='.$tahun.'  GROUP BY g.id_unit,g.nm_unit');
            foreach($unit as $row2) {
                PDF::MultiCell(20, 5, '', 1, 'L', 0, 0);
                PDF::MultiCell(100, 5, $row2->nm_unit, 1, 'L', 0, 0);
                PDF::MultiCell(60, 5, '', 1, 'L', 0, 0);
                PDF::MultiCell(40, 5, '', 1, 'L', 0, 0);
                PDF::MultiCell(55, 5, '', 1, 'L', 0, 0);
                
                PDF::Ln();
                $countrow++;
                if($countrow>=$totalrow)
                {
                    PDF::AddPage('L');
                    $countrow=0;
                    PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
                    PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
                    PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
                    PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
                    PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
                    PDF::Ln();
                    $countrow++;
                }
                $kegiatan = DB::SELECT('SELECT CONCAT(f.kd_urusan,".",f.kd_bidang,".",e.kd_program,".",c.kd_kegiatan) as kode, c.nm_kegiatan, b.id_kegiatan_ref
                        FROM trx_musrencam a
                        INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                        INNER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
                        INNER JOIN ref_unit d ON b.id_unit=d.id_unit
                        INNER JOIN ref_program e ON c.id_program=e.id_program
                        INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                        WHERE b.id_unit='.$row2->id_unit.' AND tahun_musren='.$tahun.' AND a.id_kecamatan='.$row->id_kecamatan.'
                        GROUP BY kode,c.nm_kegiatan,  b.id_kegiatan_ref');
                
                foreach($kegiatan as $row3) {
                    PDF::MultiCell(20, 5, $row3->kode, 1, 'L', 0, 0);
                    PDF::MultiCell(5, 5, '', 'LBT', 'L', 0, 0);
                    PDF::MultiCell(95, 5, $row3->nm_kegiatan, 'RBT', 'L', 0, 0);
                    PDF::MultiCell(60, 5, '', 1, 'L', 0, 0);
                    PDF::MultiCell(40, 5, '', 1, 'L', 0, 0);
                    PDF::MultiCell(55, 5, '', 1, 'L', 0, 0);
                    PDF::Ln();
                    $countrow++;
                    if($countrow>=$totalrow)
                    {
                        PDF::AddPage('L');
                        $countrow=0;
                        PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
                        PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
                        PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
                        PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
                        PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
                        PDF::Ln();
                        $countrow++;
                    }
                    $aktivitas = DB::SELECT('SELECT a.uraian_aktivitas_kegiatan, a.id_musrencam
                            FROM trx_musrencam a
                            INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                            INNER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
                            INNER JOIN ref_unit d ON b.id_unit=d.id_unit
                            INNER JOIN ref_program e ON c.id_program=e.id_program
                            INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                            INNER JOIN ref_kecamatan g ON a.id_kecamatan=g.id_kecamatan                        
                            WHERE b.id_unit='.$row2->id_unit.' AND a.tahun_musren='.$tahun.' AND a.id_kecamatan='.$row->id_kecamatan.' AND  b.id_kegiatan_ref='.$row3->id_kegiatan_ref.'
                            GROUP BY a.uraian_aktivitas_kegiatan, a.id_musrencam');
                    $hitung=0;
                    foreach($aktivitas as $row4) {
                        $lokasi = DB::SELECT('SELECT a.uraian_aktivitas_kegiatan,i.nama_desa,j.volume_usulan_1,k.uraian_satuan, CASE j.status_pelaksanaan
                            WHEN 0 THEN "Diterima Tanpa Perubahan"
                            WHEN 1 THEN "Diterima Dengan Perubahan"
                            WHEN 2 THEN "Digabungkan"
                            ELSE "Ditolak" END status
                            FROM trx_musrencam a
                            INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                            INNER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
                            INNER JOIN ref_unit d ON b.id_unit=d.id_unit
                            INNER JOIN ref_program e ON c.id_program=e.id_program
                            INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                            INNER JOIN ref_kecamatan g ON a.id_kecamatan=g.id_kecamatan
                            INNER JOIN trx_musrencam_lokasi h ON a.id_musrencam=h.id_musrencam
                            INNER JOIN ref_desa i ON h.id_desa=i.id_desa
                            INNER JOIN trx_forum_skpd_lokasi j ON h.id_lokasi_musrencam=j.id_lokasi_renja AND j.sumber_data=2
                            INNER JOIN ref_satuan k ON j.id_satuan_1=k.id_satuan                            
                            WHERE b.id_unit='.$row2->id_unit.' AND a.tahun_musren='.$tahun.' AND a.id_kecamatan='.$row->id_kecamatan.' AND a.id_musrencam='.$row4->id_musrencam);
                        
                        foreach($lokasi as $row5) {
                            if($hitung==0)
                            {
                                PDF::MultiCell(20, 5, '', 1, 'L', 0, 0);
                                PDF::MultiCell(10, 5, '', 'LBT', 'L', 0, 0);
                                PDF::MultiCell(90, 5, $row4->uraian_aktivitas_kegiatan, 'RBT', 'L', 0, 0);
                                PDF::MultiCell(60, 5, $row5->nama_desa, 1, 'L', 0, 0);
                                PDF::MultiCell(40, 5, $row5->volume_usulan_1.' '.$row5->uraian_satuan, 1, 'L', 0, 0);
                                PDF::MultiCell(55, 5, $row5->status, 1, 'L', 0, 0);
                                PDF::Ln();
                            }
                            ELSE
                            {
                                PDF::MultiCell(20, 5, '', 1, 'L', 0, 0);
                                PDF::MultiCell(100, 5, '', 1, 'L', 0, 0);
                                PDF::MultiCell(60, 5, $row5->nama_desa, 1, 'L', 0, 0);
                                PDF::MultiCell(40, 5, $row5->volume_usulan_1.' '.$row5->uraian_satuan, 1, 'L', 0, 0);
                                PDF::MultiCell(55, 5, $row5->status, 1, 'L', 0, 0);
                                PDF::Ln();
                                
                                
                            }
                            $countrow++;
                            
                            if($countrow>=$totalrow)
                            {
                                PDF::AddPage('L');
                                $countrow=0;
                                PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
                                PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
                                PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
                                PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
                                PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
                                PDF::Ln();
                                $countrow++;
                            }
                            $hitung++;
                            //$fill=!$fill;
                        }
                    }
                    //$fill=!$fill;
                }
                //$fill=!$fill;
            }
        }
      //  PDF::Cell(array_sum($w), 0, '', 'T');
        
        // ---------------------------------------------------------
        
        // close AND output PDF document
        PDF::Output('Musrenbang.pdf', 'I');
    }
    
}
