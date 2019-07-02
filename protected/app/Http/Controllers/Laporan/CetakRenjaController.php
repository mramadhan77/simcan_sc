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
use PhpParser\Node\Stmt\Foreach_;
use App\Http\Controllers\Laporan\TemplateReport As Template;


class CetakRenjaController extends Controller
{

 public function KompilasiProgramdanPaguRenja($id_unit)
  {
		
  	$countrow=0;
  	$totalrow=18;
		if($id_unit<1)
		{$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit FROM ref_unit a INNER JOIN
trx_renja_rancangan_program b
on a.id_unit=b.id_unit  ');}
		else 
		{$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit FROM ref_unit a INNER JOIN
trx_renja_rancangan_program b
on a.id_unit=b.id_unit WHERE a.id_unit='.$id_unit);}
		

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

    //Header
    PDF::Cell('225', 5, Session::get('xPemda') , 1, 0, 'C', 0);
    PDF::Ln();
    $countrow++;
    PDF::Cell('225', 5, 'KOMPILASI PROGRAM RENJA', 1, 0, 'C', 0);
    PDF::Ln();
    $countrow++;
    PDF::SetFont('', 'B');
    PDF::SetFont('helvetica', 'B', 6);
    
    // Header Column
    
    $wh = array(45,30,30,20,20,20,20,20,20);
    $w = array(225);
    $w1 = array(5,40,120,20,20,20);
    $w2 = array(45,30,30,20,20,20,60);
    
    $num_headers = count($header);
    for($i = 0; $i < $num_headers; ++$i) {
            PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
    }
    PDF::Ln();
    $countrow++;
        // Color AND font restoration

    PDF::SetFillColor(224, 235, 255);
    PDF::SetTextColor(0);
    PDF::SetFont('helvetica', '', 6);
        // Data
    $fill = 0;
    foreach($Unit AS $row) {
    	PDF::MultiCell($w[0], 10, $row->nm_unit, 0, 'L', 0,0);
    	PDF::Ln();
    	$countrow++;
    	if($countrow>=$totalrow)
    	{
    		PDF::AddPage('L');
    		$countrow=0;
    		for($i = 0; $i < $num_headers; ++$i) {
    			PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
    		}
    		PDF::Ln();
    		$countrow++;
    	}
    	//$fill=!$fill;
    	$program = DB::SELECT('SELECT c.nm_unit,d.uraian_program_renstra,d.id_renja_program,
sum(d.pagu_tahun_kegiatan) AS pagu_program,
sum(d.pagu_tahun_renstra) AS pagu_renstra,
case a.status_data when 1 then "Telah direview" else "Belum direview" end AS status_program
FROM trx_renja_rancangan_program a
INNER JOIN trx_renja_rancangan d
on a.id_renja_program=d.id_renja_program
INNER JOIN ref_unit c
on a.id_unit=c.id_unit
WHERE c.id_unit='.$row->id_unit.'
group by c.nm_unit,d.uraian_program_renstra,d.id_renja_program,a.status_data');
    	foreach($program AS $row2) {
    		PDF::MultiCell($w1[0], 10, '', 0, 'L', 0, 0);
    		PDF::MultiCell($w1[1], 10, $row2->uraian_program_renstra, 0, 'L', 0, 0);
    		PDF::MultiCell($w1[2], 10, '', 0, 'L', 0, 0);
    		PDF::MultiCell($w1[3], 10, number_format($row2->pagu_renstra,2,',','.'), 0, 'L', 0, 0);
    		PDF::MultiCell($w1[4], 10, number_format($row2->pagu_program,2,',','.'), 0, 'L', 0, 0);
    		PDF::MultiCell($w1[5], 10, $row2->status_program, 0, 'L', 0, 0);
    		PDF::Ln();
    		$countrow++;
    		if($countrow>=$totalrow)
    		{
    			PDF::AddPage('L');
    			$countrow=0;
    			for($i = 0; $i < $num_headers; ++$i) {
    				PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
    			}
    			PDF::Ln();
    			$countrow++;
    		}
    		$indikator = DB::SELECT('SELECT c.nm_unit,a.uraian_program_renstra,b.uraian_indikator_program_renja,b.tolok_ukur_indikator,
b.target_renstra,b.target_renja,case b.status_data when 1 then "Telah direview" else "Belum direview" end AS status_indikator
FROM trx_renja_rancangan_program a
INNER JOIN trx_renja_rancangan_program_indikator b
on a.id_renja_program=b.id_renja_program
INNER JOIN ref_unit c
on a.id_unit=c.id_unit
WHERE c.id_unit='. $row->id_unit .' AND a.id_renja_program='. $row2->id_renja_program);
    		
			foreach($indikator AS $row3) {
    			PDF::MultiCell($w2[0], 10, '', 0, 'L', 0, 0);
    			PDF::MultiCell($w2[1], 10, $row3->uraian_indikator_program_renja, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[2], 10, $row3->tolok_ukur_indikator, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[3], 10, $row3->target_renstra, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[4], 10, $row3->target_renja, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[5], 10, $row3->status_indikator, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[6], 10, '', 0, 'L', 0, 0);
    			PDF::Ln();
    			$countrow++;
    			if($countrow>=$totalrow)
    			{
    				PDF::AddPage('L');
    				$countrow=0;
    				for($i = 0; $i < $num_headers; ++$i) {
    					PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
    				}
    				PDF::Ln();
    				$countrow++;
    			}
    			//$fill=!$fill;
    		}
    		//$fill=!$fill;
    	}
        }
    PDF::Cell(array_sum($w), 0, '', 'T');

    // ---------------------------------------------------------

    // close AND output PDF document
    PDF::Output('KompilasiRenja.pdf', 'I');
  }

  public function KompilasiKegiatandanPaguRenja($id_unit,$tahun)
  {
  	
  	$countrow=0;
  	$totalrow=30;
  	//$id_unit=28;
  	$pemda=Session::get('xPemda');
  	$nm_unit="";
  	if($id_unit<1)
  	{$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit,a.kd_unit, c.kd_bidang, c.nm_bidang,d.kd_urusan,d.nm_urusan  FROM ref_unit a INNER JOIN
        trx_renja_rancangan_program b ON a.id_unit=b.id_unit 
        INNER JOIN 	ref_bidang c ON a.id_bidang=c.id_bidang 
        INNER JOIN ref_urusan d ON c.kd_urusan=d.kd_urusan');}
  	else
  	{$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit,a.kd_unit, c.kd_bidang, c.nm_bidang,d.kd_urusan,d.nm_urusan FROM ref_unit a INNER JOIN
        trx_renja_rancangan_program b ON a.id_unit=b.id_unit 
        INNER JOIN 	ref_bidang c ON a.id_bidang=c.id_bidang 
        INNER JOIN ref_urusan d ON c.kd_urusan=d.kd_urusan WHERE b.id_unit='.$id_unit);}
  	
  	
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
  	$header = array('Kode','Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
  	
  	// Colors, line width AND bold font
  	PDF::SetFillColor(200, 200, 200);
  	PDF::SetTextColor(0);
  	PDF::SetDrawColor(255, 255, 255);
  	PDF::SetLineWidth(0);
  	
  	PDF::SetFont('helvetica', 'B', 10);
  	
  	//Header
  	PDF::Cell('275', 5, $pemda, 1, 0, 'C', 0);
  	PDF::Ln();
  	$countrow++;
  	PDF::Cell('275', 5, 'KOMPILASI KEGIATAN RENCANA KINERJA', 1, 0, 'C', 0);
  	PDF::Ln();
  	PDF::Ln();
  	$countrow++;
  	PDF::SetFont('', 'B');
  	PDF::SetFont('helvetica', 'B', 6);
  	PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
  	// Header Column
  	
  	$wh = array(20,30,45,45,20,20,20,25,25,25);
  	$w = array(275);
  	$w1 = array(20,120,20,20,20,25,25,25);
  	$w2 = array(20,3,117,20,20,20,25,25,25);
  	$w3 = array(20,30,45,45,20,20,20,25,25,25);
  	$w4 = array(20,30,3,42,3,42,20,20,20,25,25,25);
  	
  	// Color AND font restoration
  	
  	PDF::SetFillColor(224, 235, 255);
  	PDF::SetTextColor(0);
  	PDF::SetFont('helvetica', '', 6);
  	// Data
  	$fill = 0;
  	foreach($Unit AS $row) {
  		
  	    $nm_unit=$nm_unit.$row->nm_unit;
  		
  		PDF::Cell('30', 5, 'Urusan Pemerintahan', 'LT', 0, 'L', 0);
  		PDF::Cell('5', 5, ':', 'T', 0, 'L', 0);
  		PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang, 'T', 0, 'L', 0);
  		PDF::Cell('225', 5,$row->nm_urusan.' '.$row->nm_bidang, 'RT', 0, 'L', 0);
  		PDF::Ln();
  		$countrow++;
  		
  		PDF::Cell('30', 5, 'Perangkat Daerah', 'LB', 0, 'L', 0);
  		PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
  		PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang.'.'.$row->kd_unit, 'B', 0, 'L', 0);
  		PDF::Cell('225', 5,$row->nm_unit, 'RB', 0, 'L', 0);
  		PDF::Ln();
  		$countrow++;
  		PDF::Ln();
  		PDF::SetFont('helvetica', 'B', 6);
  		$num_headers = count($header);
  		for($i = 0; $i < $num_headers; ++$i) {
  		    //PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
  		    PDF::SetFont('helvetica', 'B', 7);
  		    PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
  		}
  		PDF::Ln();
  		$countrow++;
  		
  		if($countrow>=$totalrow)
  		{
  		    PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
  			PDF::AddPage('L');
  			$countrow=0;
  			for($i = 0; $i < $num_headers; ++$i) {
  			    PDF::SetFont('helvetica', 'B', 7);
  				PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
  			}
  			PDF::Ln();
  			$countrow++;
  			$countrow++;
  		}
  		//$fill=!$fill;
  		$program = DB::SELECT('SELECT g.kd_urusan AS ur_unit, g.kd_bidang AS bid_unit, c.kd_unit, c.nm_unit,e.uraian_program AS uraian_program_renstra, 
        d.id_renja_program, f.kd_urusan AS ur_pro, f.kd_bidang AS bid_pro, e.kd_program,
        sum(d.pagu_tahun_kegiatan) AS pagu_program,
        sum(d.pagu_tahun_renstra) AS pagu_renstra,
        case a.status_data when 1 then "Telah direview" else "Belum direview" end AS status_program
        FROM trx_renja_rancangan_program a
        INNER JOIN trx_renja_rancangan d ON a.id_renja_program=d.id_renja_program
        INNER JOIN ref_unit c ON a.id_unit=c.id_unit
        INNER JOIN ref_bidang g ON c.id_bidang = g.id_bidang
        INNER JOIN ref_program e ON a.id_program_ref=e.id_program
        INNER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
        WHERE  c.id_unit='.$row->id_unit.' AND a.id_program_rpjmd not in 
        (SELECT a.id_program_rpjmd FROM trx_rpjmd_program a
        INNER JOIN trx_rpjmd_sasaran b ON a.id_sasaran_rpjmd=b.id_sasaran_rpjmd
        INNER JOIN trx_rpjmd_tujuan c ON b.id_tujuan_rpjmd=c.id_tujuan_rpjmd
        INNER JOIN trx_rpjmd_misi d ON c.id_misi_rpjmd=d.id_misi_rpjmd
        WHERE d.no_urut in (98,99)) AND a.tahun_renja='.$tahun.' 
        GROUP BY g.kd_urusan, g.kd_bidang, c.kd_unit, c.nm_unit,d.uraian_program_renstra,d.id_renja_program, f.kd_urusan, f.kd_bidang, e.kd_program,a.status_data, e.uraian_program');
  		foreach($program AS $row2) {
          $height1=ceil((PDF::GetStringWidth($row2->ur_pro.'.'.$row2->bid_pro.'  '.$row2->ur_unit.'.'.$row2->bid_unit.'.'.$row2->kd_unit.'.'.$row2->kd_program)/$w1[0]))*3;
          $height2=ceil((PDF::GetStringWidth($row2->uraian_program_renstra)/$w1[1]))*3;
          $height3=ceil((PDF::GetStringWidth($row2->pagu_renstra)/$w1[5]))*3;
          $height4=ceil((PDF::GetStringWidth($row2->pagu_program)/$w1[6]))*3;
          $height5=ceil((PDF::GetStringWidth($row2->status_program)/$w1[7]))*3;
  		   
  		    
  		    $maxhigh =array($height1,$height2,$height3,$height4,$height5);
  		    $height = max($maxhigh);
  			PDF::SetFont('helvetica', 'B', 6);
  			//$height=ceil((strlen($row2->uraian_program_renstra)/47))*5;
  			$kode="";
  			if(strlen($row2->kd_program)==2)
  			{
  			    $kode=$row2->ur_pro.'.'.$row2->bid_pro.'  '.$row2->ur_unit.'.'.$row2->bid_unit.'.'.$row2->kd_unit.' '.$row2->kd_program;
  			}
  			else 
  			{
  			    $kode=$row2->ur_pro.'.'.$row2->bid_pro.'  '.$row2->ur_unit.'.'.$row2->bid_unit.'.'.$row2->kd_unit.' 0'.$row2->kd_program;
  			}
  			PDF::MultiCell($w1[0], $height, $kode, 'LT', 'L', 0, 0);
  			PDF::MultiCell($w1[1], $height, $row2->uraian_program_renstra, 'LT', 'L', 0, 0);
  			PDF::MultiCell($w1[2], $height, '', 'LT', 'L', 0, 0);
  			PDF::MultiCell($w1[3], $height, '', 'LT', 'L', 0, 0);
  			PDF::MultiCell($w1[4], $height, '', 'LT', 'L', 0, 0);
  			PDF::MultiCell($w1[5], $height, number_format($row2->pagu_renstra,2,',','.'), 'LT', 'R', 0, 0);
  			PDF::MultiCell($w1[6], $height, number_format($row2->pagu_program,2,',','.'), 'LT', 'R', 0, 0);
  			PDF::MultiCell($w1[7], $height, $row2->status_program, 'LRT', 'L', 0, 0);
  			
  			PDF::Ln();
  			$countrow=$countrow+$height/5;
  			
  			if($countrow>=$totalrow)
  			{
  			    PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
  				PDF::AddPage('L');
  				$countrow=0;
  				for($i = 0; $i < $num_headers; ++$i) {
  				    PDF::SetFont('helvetica', 'B', 7);
  					PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
  				}
  				PDF::Ln();
  				$countrow++;
  				$countrow++;
  			}
  			$indikatorprog = DB::SELECT('SELECT distinct d.uraian_program_renstra,b.uraian_indikator_program_renja,
            b.tolok_ukur_indikator,b.target_renstra,b.target_renja,
            case b.status_data when 1 then "Telah direview" else "Belum direview" end AS status_indikator,f.singkatan_satuan
            FROM  trx_renja_rancangan_program d
            INNER JOIN trx_renja_rancangan_program_indikator b ON d.id_renja_program=b.id_renja_program
            left outer join ref_indikator e ON b.kd_indikator=e.id_indikator
            left outer join ref_satuan f ON e.id_satuan_output=f.id_satuan
            WHERE d.id_unit='. $row->id_unit .' AND d.id_renja_program='. $row2->id_renja_program);
  			
  			foreach($indikatorprog AS $row5) {
  			    PDF::SetFont('helvetica', 'B', 6);
  			    $height1=ceil((PDF::GetStringWidth($row5->uraian_indikator_program_renja)/$w3[2]))*3;
            $height2=ceil((PDF::GetStringWidth($row5->tolok_ukur_indikator)/$w3[3]))*3;
            $height3=ceil((PDF::GetStringWidth($row5->target_renstra)/$w3[4]))*3;
            $height4=ceil((PDF::GetStringWidth($row5->target_renja)/$w3[5]))*3;
            $height5=ceil((PDF::GetStringWidth($row5->status_indikator)/$w3[6]))*3;
  			    
  			    
  			    $maxhigh =array($height1,$height2,$height3,$height4,$height5);
  			    $height = max($maxhigh);
  			    
  			    PDF::MultiCell($w3[0], $height, '', 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[1], $height, '', 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[2], $height, $row5->uraian_indikator_program_renja, 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[3], $height, $row5->tolok_ukur_indikator, 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[4], $height, $row5->target_renstra.' '.$row5->singkatan_satuan, 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[5], $height, $row5->target_renja.' '.$row5->singkatan_satuan, 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[6], $height, $row5->status_indikator, 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[7], $height, '', 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[8], $height, '', 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[9], $height, '', 'LRT', 'L', 0, 0);
  			    
  			    PDF::Ln();
  			    $countrow=$countrow+$height/5;
  			    if($countrow>=$totalrow)
  			    {
  			        PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
  			        PDF::AddPage('L');
  			        $countrow=0;
  			        for($i = 0; $i < $num_headers; ++$i) {
  			            PDF::SetFont('helvetica', 'B', 7);
  			            PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
  			        }
  			        PDF::Ln();
  			        $countrow++;
  			        $countrow++;
  			    }
  			    //$fill=!$fill;
  			}
  			$kegiatan = DB::SELECT('SELECT a.id_renja_program,b.id_renja,a.uraian_program_renstra,c.nm_kegiatan AS uraian_kegiatan_renstra, kd_kegiatan,
            sum(b.pagu_tahun_kegiatan) AS pagu_kegiatan,
            sum(b.pagu_tahun_renstra) AS pagu_renstra,
            case b.status_data when 1 then "Telah direview" else "Belum direview" end AS status_kegiatan
            FROM trx_renja_rancangan_program a
            INNER JOIN trx_renja_rancangan b ON a.id_renja_program=b.id_renja_program
            left outer join ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
            WHERE b.id_unit='. $row->id_unit .' AND a.id_renja_program='. $row2->id_renja_program.' group by 
            a.id_renja_program,b.id_renja,a.uraian_program_renstra,b.uraian_kegiatan_renstra,b.status_data, kd_kegiatan,c.nm_kegiatan');
              			
  			foreach($kegiatan AS $row3) {
  				PDF::SetFont('helvetica', '', 6);
          $height=ceil((PDF::GetStringWidth($row3->uraian_kegiatan_renstra)/$w2[2]))*3;
  				$kode2="";
  				if(strlen($row3->kd_kegiatan)==2)
  				{
  				    $kode2=$kode.'.'.$row3->kd_kegiatan;
  				}
  				else
  				{
  				    $kode2=$kode.'.0'.$row3->kd_kegiatan;
  				}
  				PDF::MultiCell($w2[0], $height, $kode2, 'LT', 'L', 0, 0);
  				PDF::MultiCell($w2[1], $height, '', 'LT', 'L', 0, 0);
  				PDF::MultiCell($w2[2], $height, $row3->uraian_kegiatan_renstra, 'T', 'L', 0, 0);
  				PDF::MultiCell($w2[3], $height, '', 'LT', 'L', 0, 0);
  				PDF::MultiCell($w2[4], $height, '', 'LT', 'L', 0, 0);
  				PDF::MultiCell($w2[5], $height, '', 'LT', 'L', 0, 0);
  				PDF::MultiCell($w2[6], $height, number_format($row3->pagu_renstra,2,',','.'), 'LT', 'R', 0, 0);
  				PDF::MultiCell($w2[7], $height, number_format($row3->pagu_kegiatan,2,',','.'), 'LT', 'R', 0, 0);
  				PDF::MultiCell($w2[8], $height, $row3->status_kegiatan, 'LRT', 'L', 0, 0);
  				
  				
  				PDF::Ln();
  				$countrow=$countrow+$height/5;
  				if($countrow>=$totalrow)
  				{
  				    PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
  					PDF::AddPage('L');
  					$countrow=0;
  					for($i = 0; $i < $num_headers; ++$i) {
  					    PDF::SetFont('helvetica', 'B', 7);
  						PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
  					}
  					PDF::Ln();
  					$countrow++;
  					$countrow++;
  				}
  				$indikator = DB::SELECT('SELECT DISTINCT d.uraian_kegiatan_renstra,b.uraian_indikator_kegiatan_renja,
              b.tolok_ukur_indikator,b.angka_renstra,b.angka_tahun,
              CASE b.status_data WHEN 1 then "Telah direview" ELSE "Belum direview" END AS status_indikator
              FROM  trx_renja_rancangan d
              INNER JOIN trx_renja_rancangan_indikator b ON d.id_renja=b.id_renja
              WHERE d.id_unit='. $row->id_unit .' AND d.id_renja_program='. $row2->id_renja_program.' AND d.id_renja='.$row3->id_renja);
  				
  				foreach($indikator AS $row4) {
  					PDF::SetFont('helvetica', '', 6);
            $height=ceil((PDF::GetStringWidth($row4->uraian_indikator_kegiatan_renja)/$w4[3]))*3;
  					PDF::MultiCell($w4[0], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[1], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[2], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[3], $height, $row4->uraian_indikator_kegiatan_renja, 'T', 'L', 0, 0);
  					PDF::MultiCell($w4[4], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[5], $height, $row4->tolok_ukur_indikator, 'T', 'L', 0, 0);
  					PDF::MultiCell($w4[6], $height, $row4->angka_renstra, 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[7], $height, $row4->angka_tahun, 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[8], $height, $row4->status_indikator, 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[9], $height, '', 'LRT', 'L', 0, 0);
  					PDF::MultiCell($w4[10], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[11], $height, '', 'LRT', 'L', 0, 0);
  					
  					PDF::Ln();
  					$countrow=$countrow+$height/5;
  					if($countrow>=$totalrow)
  					{
  					    PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
  						PDF::AddPage('L');
  						$countrow=0;
  						for($i = 0; $i < $num_headers; ++$i) {
  						    PDF::SetFont('helvetica', 'B', 7);
  							PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
  						}
  						PDF::Ln();
  						$countrow++;
  						$countrow++;
  					}
  					//$fill=!$fill;
  				}
  				//$fill=!$fill;
  			}
  			//$fill=!$fill;
  		}
  		
  	}
  	PDF::Cell(array_sum($w), 0, '', 'T');
  	
  	// ---------------------------------------------------------
  	
  	// close AND output PDF document

    $template = new TemplateReport();
    $template->footerLandscape();
  	PDF::Output('PPAS-'.$nm_unit.'.pdf', 'I');
  }
 
  public function PraRKA($id_kegiatan,$sub_unit)
  {
      
      $countrow=0;
      $totalrow=48;
      $id_renja=$id_kegiatan;
      $nama_keg="";
      $pemda=Session::get('xPemda');
      
      // set document information
      PDF::SetCreator('BPKP');
      PDF::SetAuthor('BPKP');
      PDF::SetTitle('Simd@Perencanaan');
      PDF::SetSubject('Pra RKA');
      
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
      PDF::AddPage('P');
      
      // column titles
      $header=array('INDIKATOR');
      $header2 = array('SKPD/Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
      
      // Colors, line width AND bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
      PDF::SetFont('helvetica', '', 10);
      
      //Header
      $kegiatan=DB::SELECT('SELECT a.tahun_renja,g.kd_urusan,f.kd_bidang,e.kd_unit,d.kd_sub,h.kd_program AS no_urut_pro,i.kd_kegiatan AS no_urut_keg,
          a.uraian_kegiatan_renstra,b.uraian_program_renstra, d.nm_sub, e.nm_unit,g.nm_urusan, f.nm_bidang
          FROM trx_renja_rancangan a
          INNER JOIN trx_renja_rancangan_program b on a.id_renja_program=b.id_renja_program
          INNER JOIN trx_renja_rancangan_pelaksana c on a.id_renja=c.id_renja
          INNER JOIN ref_sub_unit d on c.id_sub_unit=d.id_sub_unit
          INNER JOIN ref_unit e on d.id_unit=e.id_unit
          INNER JOIN ref_bidang f on e.id_bidang=f.id_bidang
          INNER JOIN ref_urusan g on f.kd_urusan=g.kd_urusan
          INNER JOIN ref_program h on b.id_program_ref=h.id_program
          INNER JOIN ref_kegiatan i on a.id_kegiatan_ref=i.id_kegiatan
          where a.id_renja='.$id_renja.' AND c.id_sub_unit='.$sub_unit);

      foreach($kegiatan AS $row)
      {
      $countrow++;
      $nama_keg=$row->uraian_kegiatan_renstra;
      PDF::SetFont('helvetica', '', 10);
      PDF::Cell('185', 5, $pemda, 'LRT', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::SetFont('helvetica', '', 8);
      PDF::Cell('185', 5, 'Tahun Anggaran : '.$row->tahun_renja, 'LRB', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      
      $countrow++;
      // PDF::SetFont('', 'B');
      PDF::SetFont('helvetica', '', 7);
      PDF::Cell('30', 5, 'Urusan Pemerintahan', 'LT', 0, 'L', 0);
      PDF::Cell('5', 5, ':', 'T', 0, 'L', 0);
      PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang, 'T', 0, 'L', 0);
      PDF::Cell('135', 5,$row->nm_urusan.'.'.$row->nm_bidang, 'RT', 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('30', 5, 'Perangkat Daerah', 'L', 0, 'L', 0);
      PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
      PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang.'.'.$row->kd_unit, 0, 0, 'L', 0);
      PDF::Cell('135', 5,$row->nm_unit, 'R', 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('30', 5, 'Sub Perangkat Daerah', 'L', 0, 'L', 0);
      PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
      PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang.'.'.$row->kd_unit.'.'.$row->kd_sub, 0, 0, 'L', 0);
      PDF::Cell('135', 5,$row->nm_sub, 'R', 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('30', 5, 'Program', 'L', 0, 'L', 0);
      PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
      PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang.'.'.$row->kd_unit.'.'.$row->kd_sub.'.'.$row->no_urut_pro, 0, 0, 'L', 0);
      PDF::Cell('135', 5,$row->uraian_program_renstra, 'R', 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('30', 5, 'Kegiatan', 'LB', 0, 'L', 0);
      PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
      PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang.'.'.$row->kd_unit.'.'.$row->kd_sub.'.'.$row->no_urut_pro.'.'.$row->no_urut_keg, 'B', 0, 'L', 0);
      PDF::Cell('135', 5,$row->uraian_kegiatan_renstra, 'RB', 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      }
      
      $lokasi=DB::SELECT('SELECT a.uraian_kegiatan_renstra,e.nama_lokasi FROM trx_renja_rancangan a
          INNER JOIN trx_renja_rancangan_pelaksana b on a.id_renja=b.id_renja
					INNER JOIN trx_renja_rancangan_aktivitas c on b.id_pelaksana_renja=c.id_renja
          INNER JOIN trx_renja_rancangan_lokasi d on c.id_aktivitas_renja=d.id_pelaksana_renja
          INNER JOIN ref_lokasi e on d.id_lokasi=e.id_lokasi
          where a.id_renja='.$id_renja.' AND b.id_sub_unit='.$sub_unit);
      PDF::Cell('30', 5, 'Lokasi', 'LB', 0, 'L', 0);
      PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
      $c=0;
      $gablok="";
      foreach($lokasi AS $row)
      {          
          $countrow++;
          if($c==0)
          {
              $gablok=$gablok.''.$row->nama_lokasi;
          }
          else 
          {
              $gablok=$gablok.', '.$row->nama_lokasi;
          }
          $c=$c+1;
      }
      PDF::Cell('150', 5,$gablok, 'RB', 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      
      $pagu=DB::SELECT('SELECT a.tahun_renja-c.tahun_rkpd AS selisih,c.tahun_rkpd,c.pagu_tahun_kegiatan,a.pagu_tahun_kegiatan AS pagu_n FROM trx_renja_rancangan a
            INNER JOIN trx_rkpd_renstra b ON a.id_rkpd_renstra=b.id_rkpd_renstra
            INNER JOIN trx_rkpd_renstra c ON b.id_kegiatan_renstra=c.id_kegiatan_renstra
            WHERE a.id_renja='.$id_renja.' AND (a.tahun_renja-c.tahun_rkpd in (-1,0,1)) order by c.tahun_rkpd ASC ');
      $c=0;
      foreach ($pagu AS $row)
      {
          if($pagu>0)
          {
            if($row->selisih==1)
            {
                PDF::Cell('30', 5, 'Jumlah Tahun n-1', 'L', 0, 'L', 0);
                PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
                PDF::Cell('40', 5, 'Rp.'.number_format($row->pagu_tahun_kegiatan,2,',','.'), 0, 0, 'R', 0);
                PDF::Cell('110', 5, '', 'R', 0, 'R', 0);
                PDF::Ln();
                $countrow++;
            }
            else if ($row->selisih==0)
            {
                PDF::Cell('30', 5, 'Jumlah Tahun n', 'L', 0, 'L', 0);
                PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
                PDF::Cell('40', 5, 'Rp.'.number_format($row->pagu_n,2,',','.'), 0, 0, 'R', 0);
                PDF::Cell('110', 5, '', 'R', 0, 'R', 0);
                PDF::Ln();
                $countrow++;
            }
            else if($row->selisih==-1)
            {
                PDF::Cell('30', 5, 'Jumlah Tahun n+1', 'LB', 0, 'L', 0);
                PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
                PDF::Cell('40', 5, 'Rp.'.number_format($row->pagu_tahun_kegiatan,2,',','.'), 'B', 0, 'R', 0);
                PDF::Cell('110', 5, '', 'RB', 0, 'R', 0);
                PDF::Ln();
                $countrow++;
            }
          }
          else
          {
              /////////////////////PR mikirin else nya gimana ketika hanya ada -1,0 atau 0,1/////////////////////
//               if($selisih==1)
//               {
//                   PDF::Cell('30', 5, 'Jumlah Tahun n-1', 'LB', 0, 'L', 0);
//                   PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
//                   PDF::Cell('150', 5, $row->pagu_tahun_kegiatan, 'B', 0, 'L', 0);
//               }
//               else if ($selisih==0)
//               {
//                   PDF::Cell('30', 5, 'Jumlah Tahun n', 'LB', 0, 'L', 0);
//                   PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
//                   PDF::Cell('150', 5, $row->pagu_n, 'B', 0, 'L', 0);
//               }
//               else if($selisih==-1)
//               {
//                   PDF::Cell('30', 5, 'Jumlah Tahun n+1', 'LB', 0, 'L', 0);
//                   PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
//                   PDF::Cell('150', 5, $row->pagu_tahun_kegiatan, 'B', 0, 'L', 0);
//               }
          }
          
          $c=$c+1;
      }
      PDF::Cell('185', 5, 'INDIKATOR DAN TOLOK UKUR KINERJA BELANJA LANGSUNG', 'LRB', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('30', 5, 'INDIKATOR', 'LBR', 0, 'C', 0);
      PDF::Cell('80', 5, 'TOLOK UKUR KINERJA', 'BR', 0, 'C', 0);
      PDF::Cell('75', 5, 'TARGET KINERJA', 'RB', 0, 'C', 0);
      PDF::Ln();
      $countrow++;

      $pagu2=DB::SELECT('SELECT a.pagu_tahun_kegiatan  FROM trx_renja_rancangan a  WHERE a.id_renja='.$id_renja);
      PDF::Cell('30', 5, 'MASUKAN', 'LBR', 0, 'L', 0);
      PDF::Cell('80', 5, 'Jumlah Dana', 'BR', 0, 'L', 0);
      foreach($pagu2 AS $row)
      {
          PDF::Cell('75', 5, 'Rp.'.number_format($row->pagu_tahun_kegiatan,2,',','.'), 'RB', 0, 'L', 0);
      }
      PDF::Ln();
      $countrow++;

      $ind=DB::SELECT('SELECT b.tolok_ukur_indikator,b.angka_tahun FROM trx_renja_rancangan a
      INNER JOIN trx_renja_rancangan_indikator b ON a.id_renja=b.id_renja
      WHERE a.id_renja='.$id_renja);
      $c=0;
      foreach($ind AS $row)
      {
          if($c==0)
          {
            PDF::Cell('30', 5, 'KELUARAN', 'LR', 0, 'L', 0);
            PDF::Cell('80', 5, $row->tolok_ukur_indikator, 'R', 0, 'L', 0);
            PDF::Cell('75', 5,number_format($row->angka_tahun,2,',','.'), 'R', 0, 'L', 0);
          }
          else 
          {
              PDF::Cell('30', 5, '', 'LR', 0, 'L', 0);
              PDF::Cell('80', 5, $row->tolok_ukur_indikator, 'R', 0, 'L', 0);
              PDF::Cell('75', 5,number_format($row->angka_tahun,2,',','.'), 'R', 0, 'L', 0);
          }
          
          PDF::Ln();
          $countrow++;
          $c=$c+1;
      }
      // Header Column///////////////////////////////////////////////////////////////////////////////////////////////////////////
      PDF::Cell('185', 5, 'RINCIAN  BELANJA  MENURUT PROGRAM DAN  KEGIATAN  PERANGKAT DAERAH', 1, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
      PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('25', 5, 'KODE REKENING', 'LB', 0, 'C', 0);
      PDF::Cell('70', 5, 'URAIAN', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
      PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      //$line=PDF::getNumLines("aisfknv;lasjfnvo;asjfnv;jzlnc",10);
      PDF::Cell('25', 5, '', 'LB', 0, 'C', 0);
      PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
      PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      
      // End Header Column/////////////////////////////////////////////////////////////////////////////////////////////////////
    
    $rek1=DB::SELECT('SELECT k.kd_rek_1,k.nama_kd_rek_1,sum(e.jml_belanja) AS jumlah
          FROM trx_renja_rancangan a
          INNER JOIN trx_renja_rancangan_pelaksana c on a.id_renja=c.id_renja
          INNER JOIN trx_renja_rancangan_aktivitas d on c.id_pelaksana_renja=d.id_renja
          INNER JOIN trx_renja_rancangan_belanja e on d.id_aktivitas_renja=e.id_lokasi_renja
          INNER JOIN ref_ssh_tarif f on e.id_tarif_ssh=f.id_tarif_ssh
          LEFT OUTER  join ref_rek_5 g on e.id_rekening_ssh=g.id_rekening
          LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
          LEFT OUTER JOIN ref_rek_3 i ON  h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
          LEFT OUTER JOIN ref_rek_2 j ON   i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
          LEFT OUTER JOIN ref_rek_1 k ON   j.kd_rek_1=k.kd_rek_1
          where a.id_renja='.$id_renja.' AND c.id_sub_unit='.$sub_unit.' GROUP BY k.kd_rek_1,k.nama_kd_rek_1');
    foreach ($rek1 AS $row)
    {
        PDF::MultiCell('25', 3, $row->kd_rek_1, 'LB', 'L',0, 0);
        PDF::MultiCell('70', 3, $row->nama_kd_rek_1, 'LB',  'L',0, 0);
        PDF::MultiCell('20', 3, '', 'LB',  'L',0, 0);
        PDF::MultiCell('20', 3, '', 'LB',  'L',0, 0);
        PDF::MultiCell('20', 3, '', 'LB',  'L',0, 0);
        PDF::MultiCell('30', 3, number_format($row->jumlah,2,',','.'), 'LRB', 'R',0, 0);
        PDF::Ln();
        $countrow++;
        
        if($countrow>=$totalrow)
        {
            PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
            PDF::AddPage('P');
            $countrow=0;
            PDF::Cell('185', 5, 'RINCIAN BELANJA MENURUT PROGRAM DAN KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
            PDF::Ln();
            $countrow++;
            PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
            PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
            PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
            PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
            PDF::Ln();
            $countrow++;
            PDF::Cell('25', 5, 'KODE REKENING', 'LB', 0, 'C', 0);
            PDF::Cell('70', 5, 'URAIAN', 'LB', 0, 'C', 0);
            PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
            PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
            PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
            PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
            PDF::Ln();
            
            $countrow++;
            PDF::Cell('25', 5, '1', 'LB', 0, 'C', 0);
            PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
            PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
            PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
            PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
            PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
            PDF::Ln();
            $countrow++;
        }
        $rek2=DB::SELECT('SELECT j.kd_rek_2,j.nama_kd_rek_2,sum(e.jml_belanja) AS jumlah
            FROM trx_renja_rancangan a
            INNER JOIN trx_renja_rancangan_pelaksana c on a.id_renja=c.id_renja
            INNER JOIN trx_renja_rancangan_aktivitas d on c.id_pelaksana_renja=d.id_renja
            INNER JOIN trx_renja_rancangan_belanja e on d.id_aktivitas_renja=e.id_lokasi_renja
            INNER JOIN ref_ssh_tarif f on e.id_tarif_ssh=f.id_tarif_ssh
            LEFT OUTER JOIN ref_rek_5 g on e.id_rekening_ssh=g.id_rekening
            LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
            LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
            LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
            LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
            where a.id_renja='.$id_renja.' AND c.id_sub_unit='.$sub_unit.' AND k.kd_rek_1='.$row->kd_rek_1.' GROUP BY j.kd_rek_2,j.nama_kd_rek_2');
        foreach ($rek2 AS $row2)
        {
            PDF::MultiCell('25', 3,  $row->kd_rek_1.'.'.$row2->kd_rek_2, 'LB', 'L', 0, 0);
            PDF::MultiCell('3', 3, '', 'LB', 'L',0, 0);
            PDF::MultiCell('67', 3, $row2->nama_kd_rek_2, 'B', 'L', 0, 0);
            PDF::MultiCell('20', 3, '', 'LB', 'L', 0, 0);
            PDF::MultiCell('20', 3, '', 'LB', 'L', 0, 0);
            PDF::MultiCell('20', 3, '', 'LB', 'L', 0, 0);
            PDF::MultiCell('30', 3, number_format($row2->jumlah,2,',','.'), 'LRB', 'R', 0, 0);
            PDF::Ln();
            $countrow++;
            
            if($countrow>=$totalrow)
            {
                PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                PDF::AddPage('P');
                $countrow=0;
                PDF::Cell('275', 5, 'RINCIAN BELANJA MENURUT PROGRAM DAN KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                PDF::Ln();
                $countrow++;
                PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
                PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
                PDF::Ln();
                $countrow++;
                PDF::Cell('25', 5, 'KODE REKENING', 'LB', 0, 'C', 0);
                PDF::Cell('70', 5, 'URAIAN', 'LB', 0, 'C', 0);
                PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
                PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
                PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
                PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
                PDF::Ln();
                $countrow++;
                PDF::Cell('25', 5, '1', 'LB', 0, 'C', 0);
                PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
                PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
                PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
                PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
                PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
                PDF::Ln();
                $countrow++;
            }
            
            $rek3=DB::SELECT('SELECT i.kd_rek_3,i.nama_kd_rek_3,sum(e.jml_belanja) AS jumlah
                FROM trx_renja_rancangan a
                INNER JOIN trx_renja_rancangan_pelaksana c on a.id_renja=c.id_renja
                INNER JOIN trx_renja_rancangan_aktivitas d on c.id_pelaksana_renja=d.id_renja
                INNER JOIN trx_renja_rancangan_belanja e on d.id_aktivitas_renja=e.id_lokasi_renja
                INNER JOIN ref_ssh_tarif f on e.id_tarif_ssh=f.id_tarif_ssh
                LEFT OUTER  join ref_rek_5 g on e.id_rekening_ssh=g.id_rekening
                LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
                where a.id_renja='.$id_renja.' AND c.id_sub_unit='.$sub_unit.' AND k.kd_rek_1='.$row->kd_rek_1.' AND j.kd_rek_2='.$row2->kd_rek_2.'  GROUP BY i.kd_rek_3,i.nama_kd_rek_3');
            foreach ($rek3 AS $row3)
            {
                PDF::MultiCell('25', 3, $row->kd_rek_1.'.'.$row2->kd_rek_2.'.'.$row3->kd_rek_3, 'LB',  'L',0, 0);
                PDF::MultiCell('6', 3, '', 'LB', 'L', 0, 0);
                PDF::MultiCell('64', 3, $row3->nama_kd_rek_3, 'B',  'L',0, 0);
                PDF::MultiCell('20', 3, '', 'LB', 'L',0, 0);
                PDF::MultiCell('20', 3, '', 'LB', 'L',0, 0);
                PDF::MultiCell('20', 3, '', 'LB', 'L',0, 0);
                PDF::MultiCell('30', 3, number_format($row3->jumlah,2,',','.'), 'LRB', 'R',0, 0);
                PDF::Ln();
                $countrow++;
                
                if($countrow>=$totalrow)
                {
                    PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                    PDF::AddPage('P');
                    $countrow=0;
                    PDF::Cell('275', 5, 'RINCIAN BELANJA MENURUT PROGRAM DAN KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                    PDF::Ln();
                    $countrow++;
                    PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                    PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
                    PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
                    PDF::Ln();
                    $countrow++;
                    PDF::Cell('25', 5, 'KODE REKENING', 'LB', 0, 'C', 0);
                    PDF::Cell('70', 5, 'URAIAN', 'LB', 0, 'C', 0);
                    PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
                    PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
                    PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
                    PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
                    PDF::Ln();
                    $countrow++;
                    PDF::Cell('25', 5, '1', 'LB', 0, 'C', 0);
                    PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
                    PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
                    PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
                    PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
                    PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
                    PDF::Ln();
                    $countrow++;
                }
                
                $rek4=DB::SELECT('SELECT h.kd_rek_4,h.nama_kd_rek_4,sum(e.jml_belanja) AS jumlah
                  FROM trx_renja_rancangan a
                  INNER JOIN trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
                  INNER JOIN trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
                  INNER JOIN trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
                  INNER JOIN ref_ssh_tarif f ON e.id_tarif_ssh=f.id_tarif_ssh
                  LEFT OUTER JOIN ref_rek_5 g ON e.id_rekening_ssh=g.id_rekening
                  LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                  LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                  LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                  LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
                  WHERE a.id_renja='.$id_renja.' AND c.id_sub_unit='.$sub_unit.' AND k.kd_rek_1='.$row->kd_rek_1.' AND j.kd_rek_2='.$row2->kd_rek_2.' AND i.kd_rek_3='.$row3->kd_rek_3.'  GROUP BY h.kd_rek_4,h.nama_kd_rek_4');
                foreach ($rek4 AS $row4)
                {
                    $height=ceil((PDF::GetStringWidth($row4->nama_kd_rek_4)/61))*3;
                    PDF::MultiCell('25', $height, $row->kd_rek_1.'.'.$row2->kd_rek_2.'.'.$row3->kd_rek_3.'.'.$row4->kd_rek_4, 'LB', 'L', 0, 0);
                    PDF::MultiCell('9', $height, '', 'LB', 'L', 0, 0);
                    PDF::MultiCell('61', $height, $row4->nama_kd_rek_4, 'B', 'L', 0, 0);
                    PDF::MultiCell('20', $height, '', 'LB', 'L', 0, 0);
                    PDF::MultiCell('20', $height, '', 'LB', 'L', 0, 0);
                    PDF::MultiCell('20', $height, '', 'LB', 'L', 0, 0);
                    PDF::MultiCell('30', $height, number_format($row4->jumlah,2,',','.'), 'LRB', 'R', 0, 0);
                    PDF::Ln();
                    $countrow=$countrow+$height/5;
                    
                    if($countrow>=$totalrow)
                    {
                        PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                        PDF::AddPage('P');
                        $countrow=0;
                        PDF::Cell('185', 5, 'RINCIAN  BELANJA  MENURUT PROGRAM DAN  KEGIATAN  PERANGKAT DAERAH', 1, 0, 'C', 0);
                        PDF::Ln();
                        $countrow++;
                        PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                        PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
                        PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
                        PDF::Ln();
                        $countrow++;
                        PDF::Cell('25', 5, 'KODE REKENING', 'LB', 0, 'C', 0);
                        PDF::Cell('70', 5, 'URAIAN', 'LB', 0, 'C', 0);
                        PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
                        PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
                        PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
                        PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
                        PDF::Ln();
                        $countrow++;
                        PDF::Cell('25', 5, '1', 'LB', 0, 'C', 0);
                        PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
                        PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
                        PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
                        PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
                        PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
                        PDF::Ln();
                        $countrow++;
                    }
                    
                    $rek5=DB::SELECT('SELECT g.kd_rek_5, g.nama_kd_rek_5, sum(e.jml_belanja) AS jumlah
                          FROM trx_renja_rancangan a
                          INNER JOIN trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
                          INNER JOIN trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
                          INNER JOIN trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
                          INNER JOIN ref_ssh_tarif f ON e.id_tarif_ssh=f.id_tarif_ssh
                          LEFT OUTER JOIN ref_rek_5 g ON e.id_rekening_ssh=g.id_rekening
                          LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                          LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                          LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                          LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
                          WHERE a.id_renja='.$id_renja.' AND c.id_sub_unit='.$sub_unit.' AND k.kd_rek_1='.$row->kd_rek_1.' AND j.kd_rek_2='.$row2->kd_rek_2.' AND i.kd_rek_3='.$row3->kd_rek_3.' AND h.kd_rek_4='.$row4->kd_rek_4.'  GROUP BY g.kd_rek_5,g.nama_kd_rek_5');
                    foreach ($rek5 AS $row5)
                    {
                        $height=ceil((PDF::GetStringWidth($row5->nama_kd_rek_5)/58))*3;
                        PDF::MultiCell('25', $height, $row->kd_rek_1.'.'.$row2->kd_rek_2.'.'.$row3->kd_rek_3.'.'.$row4->kd_rek_4.'.'.$row5->kd_rek_5, 'LB',  'L',0, 0);
                        PDF::MultiCell('12', $height, '', 'LB',  'L',0, 0);
                        PDF::MultiCell('58', $height, $row5->nama_kd_rek_5, 'B',  'L',0, 0);
                        PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
                        PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
                        PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
                        PDF::MultiCell('30', $height, number_format($row5->jumlah,2,',','.'), 'LRB', 'R',0, 0);
                        PDF::Ln();
                        $countrow=$countrow+$height/5;
                        
                        if($countrow>=$totalrow)
                        {
                            PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                            PDF::AddPage('P');
                            $countrow=0;
                            PDF::Cell('185', 5, 'RINCIAN BELANJA MENURUT PROGRAM DAN KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                            PDF::Ln();
                            $countrow++;
                            PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                            PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                            PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
                            PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
                            PDF::Ln();
                            $countrow++;
                            PDF::Cell('25', 5, 'KODE REKENING', 'LB', 0, 'C', 0);
                            PDF::Cell('70', 5, 'URAIAN', 'LB', 0, 'C', 0);
                            PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
                            PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
                            PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
                            PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
                            PDF::Ln();
                            $countrow++;
                            PDF::Cell('25', 5, '1', 'LB', 0, 'C', 0);
                            PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
                            PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
                            PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
                            PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
                            PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
                            PDF::Ln();
                            $countrow++;
                        }
                        
                        $akt=DB::SELECT('SELECT d.id_aktivitas_renja,d.uraian_aktivitas_kegiatan,sum(e.jml_belanja) AS jumlah
                            FROM trx_renja_rancangan a 
                            INNER JOIN trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
                            INNER JOIN trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
                            INNER JOIN trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
                            INNER JOIN ref_ssh_tarif f ON e.id_tarif_ssh=f.id_tarif_ssh
                            LEFT OUTER  join ref_rek_5 g ON e.id_rekening_ssh=g.id_rekening
                            LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                            LEFT OUTER JOIN ref_rek_3 i ON  h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                            LEFT OUTER JOIN ref_rek_2 j ON  i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                            LEFT OUTER JOIN ref_rek_1 k ON  j.kd_rek_1=k.kd_rek_1
                            where a.id_renja='.$id_renja.' AND c.id_sub_unit='.$sub_unit.' AND k.kd_rek_1='.$row->kd_rek_1.' AND j.kd_rek_2='.$row2->kd_rek_2.' AND i.kd_rek_3='.$row3->kd_rek_3.' AND h.kd_rek_4='.$row4->kd_rek_4.' AND g.kd_rek_5='.$row5->kd_rek_5.' GROUP BY d.id_aktivitas_renja, d.uraian_aktivitas_kegiatan');
                        foreach ($akt AS $row6)
                        {
                            $height=ceil((PDF::GetStringWidth($row6->uraian_aktivitas_kegiatan)/55))*3;
                            PDF::MultiCell('25', $height, '', 'LB',  'L',0, 0);
                            PDF::MultiCell('15', $height, '', 'LB',  'L',0, 0);
                            PDF::MultiCell('55', $height, $row6->uraian_aktivitas_kegiatan, 'B',  'L',0, 0);
                            PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
                            PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
                            PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
                            PDF::MultiCell('30', $height, number_format($row6->jumlah,2,',','.'), 'LRB',  'R',0, 0);
                            PDF::Ln();
                            $countrow=$countrow+$height/5;
                            
                            if($countrow>=$totalrow)
                            {
                                PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                                PDF::AddPage('P');
                                $countrow=0;
                                PDF::Cell('185', 5, 'RINCIAN BELANJA MENURUT PROGRAM DAN KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                                PDF::Ln();
                                $countrow++;
                                PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                                PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                                PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
                                PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
                                PDF::Ln();
                                $countrow++;
                                PDF::Cell('25', 5, 'KODE REKENING', 'LB', 0, 'C', 0);
                                PDF::Cell('70', 5, 'URAIAN', 'LB', 0, 'C', 0);
                                PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
                                PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
                                PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
                                PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
                                PDF::Ln();
                                $countrow++;
                                PDF::Cell('25', 5, '1', 'LB', 0, 'C', 0);
                                PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
                                PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
                                PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
                                PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
                                PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
                                PDF::Ln();
                                $countrow++;
                            }
                            
                            $belanja=DB::SELECT(' SELECT e.id_rekening_ssh,a.uraian_kegiatan_renstra,d.uraian_aktivitas_kegiatan,
                            CONCAT(GantiEnter(f.uraian_tarif_ssh),COALESCE(f.keterangan_tarif_ssh,CONCAT(" - ",f.keterangan_tarif_ssh),"")) AS uraian_tarif_ssh
                            , e.volume_1,m.uraian_satuan AS satuan1
                            ,e.volume_2,n.uraian_satuan AS satuan2
                            ,e.harga_satuan, e.jml_belanja                            		
                            FROM trx_renja_rancangan a
                            INNER JOIN trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
                            INNER JOIN trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
                            INNER JOIN trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
                            INNER JOIN ref_ssh_tarif f ON e.id_tarif_ssh=f.id_tarif_ssh
                            LEFT OUTER  join ref_rek_5 g ON e.id_rekening_ssh=g.id_rekening
                            LEFT OUTER JOIN ref_rek_4 h ON g.kd_rek_4=h.kd_rek_4 AND g.kd_rek_3=h.kd_rek_3 AND g.kd_rek_2=h.kd_rek_2 AND g.kd_rek_1=h.kd_rek_1
                            LEFT OUTER JOIN ref_rek_3 i ON h.kd_rek_3=i.kd_rek_3 AND h.kd_rek_2=i.kd_rek_2 AND h.kd_rek_1=i.kd_rek_1
                            LEFT OUTER JOIN ref_rek_2 j ON i.kd_rek_2=j.kd_rek_2 AND i.kd_rek_1=j.kd_rek_1
                            LEFT OUTER JOIN ref_rek_1 k ON j.kd_rek_1=k.kd_rek_1
                            LEFT OUTER JOIN ref_satuan m ON e.id_satuan_1=m.id_satuan
                            LEFT OUTER JOIN ref_satuan n ON e.id_satuan_2=n.id_satuan
                            where a.id_renja='.$id_renja.' AND c.id_sub_unit='.$sub_unit.' AND k.kd_rek_1='.$row->kd_rek_1.' AND j.kd_rek_2='.$row2->kd_rek_2.'
                            AND i.kd_rek_3='.$row3->kd_rek_3.' AND h.kd_rek_4='.$row4->kd_rek_4.' AND g.kd_rek_5='.$row5->kd_rek_5.' AND e.id_lokasi_renja='.$row6->id_aktivitas_renja);
                            foreach ($belanja AS $row7)
                            {
                                $height=ceil((PDF::GetStringWidth('- '.$row7->uraian_tarif_ssh)/50))*3;
                                $lenght=PDF::GetStringWidth('- '.$row7->uraian_tarif_ssh);
                                if($row7->satuan2>0)
                                {
                                    PDF::MultiCell('25', $height, '', 'LB',  'L',0, 0);
                                    PDF::MultiCell('18', $height, '', 'LB',  'L',0, 0);
                                    PDF::MultiCell('52', $height, '- '.$row7->uraian_tarif_ssh, 'B', 'L',0, 0);
                                    PDF::MultiCell('20', $height, number_format($row7->volume_1*$row7->volume_2,2,',','.'), 'LB',  'L',0, 0);
                                    PDF::MultiCell('20', $height, $row7->satuan1.' x '.$row7->satuan2, 'LB',  'L', 0,0);
                                    PDF::MultiCell('20', $height, number_format($row7->harga_satuan,2,',','.'), 'LB', 'R',0, 0);
                                    PDF::MultiCell('30', $height, number_format($row7->jml_belanja,2,',','.'), 'LRB', 'R',0, 0);
                                    PDF::Ln();
                                }
                                else
                                {
                                    PDF::MultiCell('25', $height, '', 'LB', 'L',0, 0);
                                    PDF::MultiCell('18', $height, '', 'LB', 'L',0, 0);
                                    PDF::MultiCell('52', $height, '- '.$row7->uraian_tarif_ssh, 'B', 'L',0, 0);
                                    PDF::MultiCell('20', $height, number_format($row7->volume_1*$row7->volume_2,2,',','.'), 'LB', 'L',0, 0);
                                    PDF::MultiCell('20', $height, $row7->satuan1, 'LB', 'L',0, 0);
                                    PDF::MultiCell('20', $height, number_format($row7->harga_satuan,2,',','.'), 'LB',  'R',0, 0);
                                    PDF::MultiCell('30', $height, number_format($row7->jml_belanja,2,',','.'), 'LRB',  'R',0, 0);
                                    PDF::Ln();
                                }
                                $countrow=$countrow+$height/5;
                                
                                if($countrow>=$totalrow)
                                {
                                    PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                                    PDF::AddPage('P');
                                    $countrow=0;
                                    PDF::Cell('185', 5, 'RINCIAN BELANJA MENURUT PROGRAM DAN KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                                    PDF::Ln();
                                    $countrow++;
                                    PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                                    PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                                    PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
                                    PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
                                    PDF::Ln();
                                    $countrow++;
                                    PDF::Cell('25', 5, 'KODE REKENING', 'LB', 0, 'C', 0);
                                    PDF::Cell('70', 5, 'URAIAN', 'LB', 0, 'C', 0);
                                    PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
                                    PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
                                    PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
                                    PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
                                    PDF::Ln();
                                    $countrow++;
                                    PDF::Cell('25', 5, '1', 'LB', 0, 'C', 0);
                                    PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
                                    PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
                                    PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
                                    PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
                                    PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
                                    PDF::Ln();
                                    $countrow++;
                                }
                                
                            }
                        }
                    }
                }
            }
        }
    }
        
      // close AND output PDF document
    $template = new TemplateReport();
    $template->footerPotrait();
    PDF::Output('PraRKA-'.$nama_keg.'.pdf', 'I');
  }
 ///////////////////////////////////////// 
  public function PraRKA2old($sub_unit,$tahun)
  {
      
      $countrow=0;
      $totalrow=30;
      $id_renja=20;
      //$sub_unit=7;
      $nama_sub="";
      $pagu_prog_peg=0;
      $pagu_prog_bj=0;
      $pagu_prog_mod=0;
      $pagu_prog=0;
      $pemda=Session::get('xPemda');
      
      // set document information
      PDF::SetCreator('BPKP');
      PDF::SetAuthor('BPKP');
      PDF::SetTitle('Simd@Perencanaan');
      PDF::SetSubject('Pra RKA');
      
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
      $header=array('INDIKATOR');
      $header2 = array('SKPD/Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
      
      // Colors, line width AND bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
      PDF::SetFont('helvetica', '', 10);
      
      //Header
      $sub=DB::SELECT('SELECT a.tahun_renja,g.kd_urusan,f.kd_bidang,e.kd_unit,d.kd_sub, d.nm_sub, e.nm_unit,g.nm_urusan,f.nm_bidang
          FROM trx_renja_rancangan a
          INNER JOIN trx_renja_rancangan_program b ON a.id_renja_program=b.id_renja_program
          INNER JOIN trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
          INNER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
          INNER JOIN ref_unit e ON d.id_unit=e.id_unit
          INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
          INNER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
          WHERE c.id_sub_unit='.$sub_unit.' AND b.tahun_renja='.$tahun.' limit 1');
      foreach($sub AS $row)
      {
          $countrow++;
          // $nama_keg=$row->uraian_kegiatan_renstra;
          PDF::SetFont('helvetica', 'B', 10);
          PDF::Cell('275', 5, $pemda, 'LRT', 0, 'C', 0);
          PDF::Ln();
          $countrow++;
          PDF::SetFont('helvetica', 'B', 8);
          PDF::Cell('275', 5, 'Tahun Anggaran '.$row->tahun_renja, 'LRB', 0, 'C', 0);
          PDF::Ln();
          $countrow++;
          // PDF::SetFont('', 'B');
          PDF::SetFont('helvetica', 'B', 7);
          PDF::Cell('30', 5, 'Urusan Pemerintahan', 'LT', 0, 'L', 0);
          PDF::Cell('5', 5, ':', 'T', 0, 'L', 0);
          PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang, 'T', 0, 'L', 0);
          PDF::Cell('225', 5,$row->nm_urusan.' '.$row->nm_bidang, 'RT', 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('30', 5, 'Perangkat Daerah', 'L', 0, 'L', 0);
          PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
          PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang.'.'.$row->kd_unit, 0, 0, 'L', 0);
          PDF::Cell('225', 5,$row->nm_unit, 'R', 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('30', 5, 'Sub Perangkat Daerah', 'L', 0, 'L', 0);
          PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
          PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_bidang.'.'.$row->kd_unit.'.'.$row->kd_sub, 0, 0, 'L', 0);
          PDF::Cell('225', 5,$row->nm_sub, 'R', 0, 'L', 0);
          PDF::Ln();
          $countrow++;
//           PDF::Cell('30', 5, 'Program', 'L', 0, 'L', 0);
//           PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
//           PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_unit.'.'.$row->kd_sub.'.'.$row->no_urut_pro, 0, 0, 'L', 0);
//           PDF::Cell('135', 5,$row->uraian_program_renstra, 'R', 0, 'L', 0);
//           PDF::Ln();
//           PDF::Cell('30', 5, 'Kegiatan', 'LB', 0, 'L', 0);
//           PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
//           PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_unit.'.'.$row->kd_sub.'.'.$row->no_urut_pro.'.'.$row->no_urut_keg, 'B', 0, 'L', 0);
//           PDF::Cell('135', 5,$row->uraian_kegiatan_renstra, 'RB', 0, 'L', 0);
//           PDF::Ln();
          $nama_sub=$nama_sub.$row->nm_sub;
      }
      
      
      
      
      // Header Column///////////////////////////////////////////////////////////////////////////////////////////////////////////
      PDF::Cell('275', 5, 'REKAPITULASI BELANJA LANGSUNG MENURUT PROGRAM DAN PER KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('112', 5, 'JUMLAH', 'LB', 0, 'C', 0);
      PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('112', 5, 'Tahun n', 'LB', 0, 'C', 0);
      PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::MultiCell('25', 15, 'KODE', 'LB', 'C', 0, 0);
      PDF::MultiCell('70', 15, 'URAIAN', 'LB', 'C', 0, 0);
      PDF::MultiCell('20', 15, 'LOKASI', 'LB', 'C', 0, 0);
      PDF::MultiCell('20', 15, 'TARGET KINERJA (KUANTITATIF)', 'LB', 'C', 0, 0);
      PDF::MultiCell('28', 15, 'Blj. Pegawai', 'LB', 'C', 0, 0);
      PDF::MultiCell('28', 15, 'Blj. Barang & Jasa ', 'LB', 'C', 0, 0);
      PDF::MultiCell('28', 15, 'Blj. Modal', 'LB', 'C', 0, 0);
      PDF::MultiCell('28', 15, 'Jumlah', 'LB', 'C', 0, 0);
      PDF::MultiCell('28', 15, 'Tahun n+1', 'LRB', 'C', 0, 0);
      PDF::Ln();
      $countrow=$countrow+3;
      
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
      $countrow++;
//       PDF::Cell('25', 5, '1', 'LB', 0, 'C', 0);
//       PDF::Cell('70', 5, '2', 'LB', 0, 'C', 0);
//       PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
//       PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
//       PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
//       PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
//       PDF::Ln();
      
      // End Header Column/////////////////////////////////////////////////////////////////////////////////////////////////////
      
      $prog=DB::SELECT('SELECT a.id_renja_program,a.kode,a.kd_program, a.uraian_program_renstra,sum(a.blj_peg) AS blj_peg,sum(a.blj_bj) AS blj_bj, 
            sum(a.blj_modal) AS blj_modal, b.pagu_tahun_ranwal FROM
            (SELECT a.id_renja_program,CONCAT(o.kd_urusan,".",o.kd_bidang,"  ",g.kd_urusan,".",f.kd_bidang,".",e.kd_unit,".",d.kd_sub," ") AS kode, n.kd_program, a.uraian_program_renstra,
            case m.kd_rek_3 when 1 then (i.jml_belanja) else 0 end AS blj_peg, case m.kd_rek_3 when 2 then (i.jml_belanja) else 0 end AS blj_bj,
            case m.kd_rek_3 when 3 then (i.jml_belanja) else 0 end AS blj_modal, m.kd_rek_3
            FROM trx_renja_rancangan_program a
            INNER JOIN trx_renja_rancangan b ON a.id_renja_program=b.id_renja_program
            INNER JOIN trx_renja_rancangan_pelaksana c ON b.id_renja=c.id_renja
            LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
            LEFT OUTER JOIN ref_unit e ON d.id_unit=e.id_unit
            LEFT OUTER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
            LEFT OUTER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
            INNER JOIN trx_renja_rancangan_aktivitas h ON c.id_pelaksana_renja=h.id_renja
            INNER JOIN trx_renja_rancangan_belanja i ON h.id_aktivitas_renja=i.id_lokasi_renja
            INNER JOIN ref_ssh_tarif j ON i.id_tarif_ssh=j.id_tarif_ssh
            LEFT OUTER  join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
            LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1 
            LEFT OUTER JOIN ref_rek_3 m ON  l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1 
            INNER JOIN ref_program n ON a.id_program_ref=n.id_program
            INNER JOIN ref_bidang o ON o.id_bidang = n.id_bidang 
            WHERE  c.id_sub_unit='.$sub_unit.' AND a.id_program_rpjmd not in 
            (SELECT a.id_program_rpjmd FROM trx_rpjmd_program a
            INNER JOIN trx_rpjmd_sasaran b ON a.id_sasaran_rpjmd=b.id_sasaran_rpjmd
            INNER JOIN trx_rpjmd_tujuan c ON b.id_tujuan_rpjmd=c.id_tujuan_rpjmd
            INNER JOIN trx_rpjmd_misi d ON c.id_misi_rpjmd=d.id_misi_rpjmd
            WHERE d.no_urut in (98,99)) AND a.tahun_renja='.$tahun.')a
            INNER JOIN trx_renja_rancangan_program b ON a.id_renja_program=b.id_renja_program
            GROUP BY a.id_renja_program,a.kode,a.kd_program, a.uraian_program_renstra, b.pagu_tahun_ranwal');

      foreach ($prog AS $row)
      {
          $pagu_prog=$pagu_prog+$row->blj_peg+$row->blj_bj+$row->blj_modal;
          $pagu_prog_peg=$pagu_prog_peg+$row->blj_peg;
          $pagu_prog_bj=$pagu_prog_bj+$row->blj_bj;
          $pagu_prog_mod=$pagu_prog_mod+$row->blj_modal;
          PDF::SetFont('helvetica', 'B', 7);

          $height=ceil((PDF::GetStringWidth($row->uraian_program_renstra)/70))*3;
          $kode="";
          if(strlen($row->kd_program)==2)
          {
          $kode=$row->kode.$row->kd_program;
          } 
          else 
          {
              $kode=$row->kode.'0'.$row->kd_program;
          }
          PDF::MultiCell('25', $height, $kode, 'L', 'L', 0, 0);
          PDF::MultiCell('70', $height, $row->uraian_program_renstra, 'L', 'L', 0, 0);
          PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
          PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
          PDF::MultiCell('28', $height, number_format($row->blj_peg,2,',','.'), 'LBT', 'R', 0, 0);
          PDF::MultiCell('28', $height, number_format($row->blj_bj,2,',','.'), 'LBT', 'R', 0, 0);
          PDF::MultiCell('28', $height, number_format($row->blj_modal,2,',','.'), 'LBT', 'R', 0, 0);
          PDF::MultiCell('28', $height, number_format($row->blj_peg+$row->blj_bj+$row->blj_modal,2,',','.'), 'LBT', 'R', 0, 0);
          PDF::MultiCell('28', $height, '', 'LRB', 'R', 0, 0);
          PDF::Ln();
          $countrow=$countrow+$height/5;
          
          if($countrow>=$totalrow)
          {
              PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
              PDF::AddPage('L');
              $countrow=0;
              
              PDF::Cell('275', 5, 'REKAPITULASI BELANJA LANGSUNG MENURUT PROGRAM DAN PER KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
              PDF::Ln();
              PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
              PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
              PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
              PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
              PDF::Cell('112', 5, 'JUMLAH', 'LB', 0, 'C', 0);
              PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
              PDF::Ln();
              $countrow++;
              PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
              PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
              PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
              PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
              PDF::Cell('112', 5, 'Tahun n', 'LB', 0, 'C', 0);
              PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
              PDF::Ln();
              $countrow++;
              PDF::MultiCell('25', 15, 'KODE', 'LB', 'C', 0, 0);
              PDF::MultiCell('70', 15, 'URAIAN', 'LB', 'C', 0, 0);
              PDF::MultiCell('20', 15, 'LOKASI', 'LB', 'C', 0, 0);
              PDF::MultiCell('20', 15, 'TARGET KINERJA (KUANTITATIF)', 'LB', 'C', 0, 0);
              PDF::MultiCell('28', 15, 'Blj. Pegawai', 'LB', 'C', 0, 0);
              PDF::MultiCell('28', 15, 'Blj. Barang & Jasa ', 'LB', 'C', 0, 0);
              PDF::MultiCell('28', 15, 'Blj. Modal', 'LB', 'C', 0, 0);
              PDF::MultiCell('28', 15, 'Jumlah', 'LB', 'C', 0, 0);
              PDF::MultiCell('28', 15, 'Tahun n+1', 'LRB', 'C', 0, 0);
              PDF::Ln();
              $countrow=$countrow+3;
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
              $countrow++;
          }
          $keg=DB::SELECT('SELECT a.id_renja,a.kd_kegiatan, a.uraian_kegiatan_renstra,sum(a.blj_peg) AS blj_peg,sum(a.blj_bj) AS blj_bj, 
              sum(a.blj_modal) AS blj_modal, b.pagu_tahun_kegiatan  FROM
              (SELECT b.id_renja,n.kd_kegiatan, b.uraian_kegiatan_renstra, case m.kd_rek_3 when 1 then (i.jml_belanja) else 0 end AS blj_peg,
              case m.kd_rek_3 when 2 then (i.jml_belanja) else 0 end AS blj_bj, case m.kd_rek_3 when 3 then (i.jml_belanja) else 0 end AS blj_modal, m.kd_rek_3
              FROM trx_renja_rancangan b
              INNER JOIN trx_renja_rancangan_pelaksana c ON b.id_renja=c.id_renja
              LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
              LEFT OUTER JOIN ref_unit e ON d.id_unit=e.id_unit
              LEFT OUTER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
              LEFT OUTER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan 
              INNER JOIN trx_renja_rancangan_aktivitas h ON c.id_pelaksana_renja=h.id_renja
              INNER JOIN trx_renja_rancangan_belanja i ON h.id_aktivitas_renja=i.id_lokasi_renja
              INNER JOIN ref_ssh_tarif j ON i.id_tarif_ssh=j.id_tarif_ssh
              LEFT OUTER  join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
              LEFT OUTER JOIN ref_rek_4 l  ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1 
              LEFT OUTER JOIN ref_rek_3 m  ON  l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1 
              INNER JOIN ref_kegiatan n ON b.id_kegiatan_ref=n.id_kegiatan
              INNER JOIN ref_program o ON o.id_program = n.id_program
              INNER JOIN ref_bidang p ON o.id_bidang = p.id_bidang 
              WHERE b.id_renja_program='.$row->id_renja_program.')a
              INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
              GROUP BY a.id_renja, a.uraian_kegiatan_renstra, b.pagu_tahun_kegiatan');
          foreach ($keg AS $row2)
          {
              $height=ceil((PDF::GetStringWidth($row2->uraian_kegiatan_renstra)/67))*3;
              PDF::SetFont('helvetica', '', 7);
              $kode2="";
              if(strlen($row2->kd_kegiatan)==2)
              {
                  $kode2=$kode.'.'.$row2->kd_kegiatan;
              }
              else 
              {
                  $kode2=$kode.'.0'.$row2->kd_kegiatan;
              }
              PDF::MultiCell('25', $height, $kode2, 'L', 'L', 0, 0);
              PDF::MultiCell('3', $height, '', 'L', 'L', 0, 0);
              PDF::MultiCell('67', $height, $row2->uraian_kegiatan_renstra, '', 'L', 0, 0);
              PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
              PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
              PDF::MultiCell('28', $height, number_format($row2->blj_peg,2,',','.'), 'L', 'R', 0, 0);
              PDF::MultiCell('28', $height, number_format($row2->blj_bj,2,',','.'), 'L', 'R', 0, 0);
              PDF::MultiCell('28', $height, number_format($row2->blj_modal,2,',','.'), 'L', 'R', 0, 0);
              PDF::MultiCell('28', $height, number_format($row2->blj_peg+$row2->blj_bj+$row2->blj_modal,2,',','.'), 'L', 'R', 0, 0);
              PDF::MultiCell('28', $height, '', 'LR', 'R', 0, 0);
              PDF::Ln();
              $countrow=$countrow+$height/5;
              
              if($countrow>=$totalrow)
              {
                  PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                  PDF::AddPage('L');
                  $countrow=0;
                  PDF::Cell('275', 5, 'REKAPITULASI BELANJA LANGSUNG MENURUT PROGRAM DAN PER KEGIATAN PERANGKAT DAERAH', 1, 0, 'C', 0);
                  PDF::Ln();
                  PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('112', 5, 'JUMLAH', 'LB', 0, 'C', 0);
                  PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                  PDF::Ln();
                  $countrow++;
                  PDF::Cell('25', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('70', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('112', 5, 'Tahun n', 'LB', 0, 'C', 0);
                  PDF::Cell('28', 5, '', 'LR', 0, 'C', 0);
                  PDF::Ln();
                  $countrow++;
                  PDF::MultiCell('25', 15, 'KODE', 'LB', 'C', 0, 0);
                  PDF::MultiCell('70', 15, 'URAIAN', 'LB', 'C', 0, 0);
                  PDF::MultiCell('20', 15, 'LOKASI', 'LB', 'C', 0, 0);
                  PDF::MultiCell('20', 15, 'TARGET KINERJA (KUANTITATIF)', 'LB', 'C', 0, 0);
                  PDF::MultiCell('28', 15, 'Blj. Pegawai', 'LB', 'C', 0, 0);
                  PDF::MultiCell('28', 15, 'Blj. Barang & Jasa ', 'LB', 'C', 0, 0);
                  PDF::MultiCell('28', 15, 'Blj. Modal', 'LB', 'C', 0, 0);
                  PDF::MultiCell('28', 15, 'Jumlah', 'LB', 'C', 0, 0);
                  PDF::MultiCell('28', 15, 'Tahun n+1', 'LRB', 'C', 0, 0);
                  PDF::Ln();
                  $countrow=$countrow+3;
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
                  $countrow++;
              }       
          }
      }
      PDF::SetFont('helvetica', 'B', 8);
      PDF::MultiCell('135', 7, 'Total : ', 'LT', 'R', 0, 0);
      PDF::MultiCell('28', 7, number_format($pagu_prog_peg,2,',','.'), 'LT', 'R', 0, 0);
      PDF::MultiCell('28', 7, number_format($pagu_prog_bj,2,',','.'), 'LT', 'R', 0, 0);
      PDF::MultiCell('28', 7, number_format($pagu_prog_mod,2,',','.'), 'LT', 'R', 0, 0);
      PDF::MultiCell('28', 7, number_format($pagu_prog,2,',','.'), 'LT', 'R', 0, 0);
      PDF::MultiCell('28', 7, '', 1, 'R', 0, 0);
      PDF::Ln();
      $countrow++;
      PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
      // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
      
      
      // close AND output PDF document
      $template = new TemplateReport();
      $template->footerLandscape();
      PDF::Output('PraRKA2-'.$nama_sub.'.pdf', 'I');
  }
  
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
      $pagu_prog_1=0;
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
      $sub = DB::select('SELECT a.tahun_renja,g.kd_urusan,f.kd_bidang,e.kd_unit,d.kd_sub, d.nm_sub, e.nm_unit,g.nm_urusan,f.nm_bidang
          from trx_renja_rancangan a
          inner join trx_renja_rancangan_program b on a.id_renja_program=b.id_renja_program
          inner join trx_renja_rancangan_pelaksana c on a.id_renja=c.id_renja
          inner join ref_sub_unit d on c.id_sub_unit=d.id_sub_unit
          INNER JOIN ref_unit e on d.id_unit=e.id_unit
          inner join ref_bidang f on e.id_bidang=f.id_bidang
          INNER JOIN ref_urusan g on f.kd_urusan=g.kd_urusan
          where c.id_sub_unit=' . $sub_unit . ' and b.tahun_renja=' . $tahun . ' limit 1');
      foreach ($sub as $row) {
          $countrow ++;
          // $nama_keg=$row->uraian_kegiatan_renstra;
          PDF::SetFont('helvetica', 'B', 10);
          PDF::Cell('275', 5, $pemda, 'LRT', 0, 'C', 0);
          PDF::Ln();
          $countrow ++;
          PDF::SetFont('helvetica', 'B', 8);
          PDF::Cell('275', 5, 'Tahun Anggaran ' . $row->tahun_renja, 'LRB', 0, 'C', 0);
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
      $prog = DB::select('SELECT a.id_renja_program,a.kode,a.kd_program, a.uraian_program_renstra,sum(a.blj_peg) as blj_peg,sum(a.blj_bj) as blj_bj,
sum(a.blj_modal) as blj_modal, b.pagu_tahun_ranwal, a.pagu_tahun_program
 from
(select a.id_renja_program,CONCAT(o.kd_urusan,".",o.kd_bidang,"  ",g.kd_urusan,".",f.kd_bidang,".",e.kd_unit,".",d.kd_sub," ") as kode, n.kd_program, a.uraian_program_renstra,
case m.kd_rek_3 when 1 then (i.jml_belanja) else 0 end as blj_peg,
case m.kd_rek_3 when 2 then (i.jml_belanja) else 0 end as blj_bj,
case m.kd_rek_3 when 3 then (i.jml_belanja) else 0 end as blj_modal,
m.kd_rek_3, p.pagu_tahun_program
from trx_renja_rancangan_program a
inner join trx_renja_rancangan b on a.id_renja_program=b.id_renja_program
inner join trx_renja_rancangan_pelaksana c on b.id_renja=c.id_renja
LEFT OUTER JOIN ref_sub_unit d on c.id_sub_unit=d.id_sub_unit
LEFT OUTER JOIN ref_unit e on d.id_unit=e.id_unit
LEFT OUTER JOIN ref_bidang f on e.id_bidang=f.id_bidang
LEFT OUTER JOIN ref_urusan g on f.kd_urusan=g.kd_urusan
inner join trx_renja_rancangan_aktivitas h on c.id_pelaksana_renja=h.id_renja
inner join trx_renja_rancangan_belanja i on h.id_aktivitas_renja=i.id_lokasi_renja
inner join ref_ssh_tarif j on i.id_tarif_ssh=j.id_tarif_ssh
LEFT OUTER  join ref_rek_5 k on i.id_rekening_ssh=k.id_rekening
LEFT OUTER JOIN ref_rek_4 l on k.kd_rek_4=l.kd_rek_4 and k.kd_rek_3=l.kd_rek_3 and k.kd_rek_2=l.kd_rek_2 and k.kd_rek_1=l.kd_rek_1
LEFT OUTER JOIN ref_rek_3 m on  l.kd_rek_3=m.kd_rek_3 and l.kd_rek_2=m.kd_rek_2 and l.kd_rek_1=m.kd_rek_1
INNER join ref_program n on a.id_program_ref=n.id_program
inner JOIN ref_bidang o on o.id_bidang = n.id_bidang
LEFT OUTER JOIN  (select id_program_renstra, pagu_tahun_program from trx_rkpd_renstra where tahun_rkpd=' . $tahunn1 . ' group by id_program_renstra, pagu_tahun_program) p on a.id_program_renstra=p.id_program_renstra         
where  c.id_sub_unit=' . $sub_unit . ' and a.jenis_belanja=0 and a.tahun_renja=' . $tahun . ' )a
inner join trx_renja_rancangan_program b on a.id_renja_program=b.id_renja_program
GROUP BY a.id_renja_program,a.kode,a.kd_program, a.uraian_program_renstra, b.pagu_tahun_ranwal, a.pagu_tahun_program
');
      foreach ($prog as $row) {
          $pagu_prog = $pagu_prog + $row->blj_peg + $row->blj_bj + $row->blj_modal;
          $pagu_prog_peg = $pagu_prog_peg + $row->blj_peg;
          $pagu_prog_bj = $pagu_prog_bj + $row->blj_bj;
          $pagu_prog_mod = $pagu_prog_mod + $row->blj_modal;
          $pagu_prog_1=$pagu_prog_1+ $row->pagu_tahun_program;
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
          $indikatorprog = DB::select('SELECT  distinct d.uraian_program_renstra,b.uraian_indikator_program_renja,
b.tolok_ukur_indikator,b.target_renstra,b.target_renja,f.singkatan_satuan
from  trx_renja_rancangan_program d
inner JOIN trx_renja_rancangan_program_indikator b on d.id_renja_program=b.id_renja_program
inner JOIN trx_renja_rancangan g on d.id_renja_program=g.id_renja_program
inner join trx_renja_rancangan_pelaksana h on g.id_renja=h.id_renja
left outer join ref_indikator e on b.kd_indikator=e.id_indikator
left outer join ref_satuan f on e.id_satuan_output=f.id_satuan
where h.id_sub_unit=' . $sub_unit . ' and d.id_renja_program=' . $row->id_renja_program);
          
          foreach ($indikatorprog as $row3) {
              PDF::SetFont('helvetica', 'B', 7);
              $height = ceil((strlen($row3->uraian_indikator_program_renja) / 49)) * 4;
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
              PDF::MultiCell('67', $height, '- '.$row3->uraian_indikator_program_renja, 0, 'L', 0, 0);
              PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
              PDF::MultiCell('20', $height, $row3->target_renja.' '.$row3->singkatan_satuan, 'L', 'L', 0, 0);
              PDF::MultiCell('28',$height, '', 'LBT', 'R', 0, 0);
              PDF::MultiCell('28',$height, '', 'LBT', 'R', 0, 0);
              PDF::MultiCell('28',$height, '', 'LBT', 'R', 0, 0);
              PDF::MultiCell('28',$height, '', 'LBT', 'R', 0, 0);
              PDF::MultiCell('28',$height, '', 1, 'R', 0, 0);
              PDF::Ln();
          }
          $keg = DB::select('SELECT a.id_renja,a.kd_kegiatan, a.uraian_kegiatan_renstra,coalesce(c.gablok,"belum ada") as gablok,sum(a.blj_peg) as blj_peg,sum(a.blj_bj) as blj_bj, sum(a.blj_modal) as blj_modal, b.pagu_tahun_kegiatan,a.pagu_tahun_kegiatan
FROM
(select b.id_renja,n.kd_kegiatan, b.uraian_kegiatan_renstra,
case m.kd_rek_3 when 1 then (i.jml_belanja) else 0 end as blj_peg,
case m.kd_rek_3 when 2 then (i.jml_belanja) else 0 end as blj_bj,
case m.kd_rek_3 when 3 then (i.jml_belanja) else 0 end as blj_modal,
m.kd_rek_3,q.pagu_tahun_kegiatan
 from trx_renja_rancangan b
inner join trx_renja_rancangan_pelaksana c on b.id_renja=c.id_renja
LEFT OUTER JOIN ref_sub_unit d on c.id_sub_unit=d.id_sub_unit
LEFT OUTER JOIN ref_unit e on d.id_unit=e.id_unit
LEFT OUTER JOIN ref_bidang f on e.id_bidang=f.id_bidang
LEFT OUTER JOIN ref_urusan g on f.kd_urusan=g.kd_urusan
inner join trx_renja_rancangan_aktivitas h on c.id_pelaksana_renja=h.id_renja
inner join trx_renja_rancangan_belanja i on h.id_aktivitas_renja=i.id_lokasi_renja
inner join ref_ssh_tarif j on i.id_tarif_ssh=j.id_tarif_ssh
LEFT OUTER  join ref_rek_5 k on i.id_rekening_ssh=k.id_rekening
LEFT OUTER JOIN ref_rek_4 l on k.kd_rek_4=l.kd_rek_4 and k.kd_rek_3=l.kd_rek_3 and k.kd_rek_2=l.kd_rek_2 and k.kd_rek_1=l.kd_rek_1
LEFT OUTER JOIN ref_rek_3 m on  l.kd_rek_3=m.kd_rek_3 and l.kd_rek_2=m.kd_rek_2 and l.kd_rek_1=m.kd_rek_1
INNER join ref_kegiatan n on b.id_kegiatan_ref=n.id_kegiatan
inner JOIN ref_program o on o.id_program = n.id_program
inner JOIN ref_bidang p on o.id_bidang = p.id_bidang
left outer JOIN  (SELECT id_kegiatan_renstra, pagu_tahun_kegiatan from trx_rkpd_renstra where tahun_rkpd=' . $tahunn1 . '  group by id_kegiatan_renstra, pagu_tahun_kegiatan) q on b.id_kegiatan_renstra=q.id_kegiatan_renstra
where b.id_renja_program=' . $row->id_renja_program . ' )a
inner join trx_renja_rancangan b
on a.id_renja=b.id_renja
left outer join (select GROUP_CONCAT(c.nama_lokasi) as gablok, d.id_renja FROM
trx_renja_rancangan d
inner join trx_renja_rancangan_pelaksana a on d.id_renja=a.id_renja
inner join trx_renja_rancangan_aktivitas b on a.id_pelaksana_renja=b.id_renja
LEFT OUTER JOIN trx_renja_rancangan_lokasi e on b.id_aktivitas_renja=e.id_pelaksana_renja
inner join ref_lokasi c on e.id_lokasi=c.id_lokasi
GROUP BY d.id_renja) c
on b.id_renja=c.id_renja              
GROUP BY a.id_renja, a.kd_kegiatan, a.uraian_kegiatan_renstra, c.gablok,b.pagu_tahun_kegiatan,a.pagu_tahun_kegiatan');
          foreach ($keg as $row2) {
              $height1 = ceil((strlen($row2->uraian_kegiatan_renstra) / 54)) * 4;
              $height2= ceil((strlen($row2->gablok) / 15)) * 4;
              $height=max($height1,$height2);
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
              PDF::MultiCell('64', $height, $row2->uraian_kegiatan_renstra, '', 'L', 0, 0);
              PDF::MultiCell('20', $height, $row2->gablok, 'L', 'L', 0, 0);
              PDF::MultiCell('20', $height, '' , 'L', 'L', 0, 0);
              PDF::MultiCell('28', $height, number_format($row2->blj_peg, 2, ',', '.'), 'L', 'R', 0, 0);
              PDF::MultiCell('28', $height, number_format($row2->blj_bj, 2, ',', '.'), 'L', 'R', 0, 0);
              PDF::MultiCell('28', $height, number_format($row2->blj_modal, 2, ',', '.'), 'L', 'R', 0, 0);
              PDF::MultiCell('28', $height, number_format($row2->blj_peg + $row2->blj_bj + $row2->blj_modal, 2, ',', '.'), 'L', 'R', 0, 0);
              PDF::MultiCell('28', $height, number_format($row2->pagu_tahun_kegiatan, 2, ',', '.'), 'LR', 'R', 0, 0);
              PDF::Ln();
              $indikator = DB::select('SELECT  distinct d.uraian_kegiatan_renstra,b.uraian_indikator_kegiatan_renja,
b.tolok_ukur_indikator,b.angka_renstra,b.angka_tahun,f.singkatan_satuan,
case b.status_data when 1 then "Telah direview" else "Belum direview" end as status_indikator
from  trx_renja_rancangan d
inner JOIN trx_renja_rancangan_indikator b
on d.id_renja=b.id_renja
inner join trx_renja_rancangan_pelaksana h on d.id_renja=h.id_renja
left outer join ref_indikator e
on b.kd_indikator=e.id_indikator
left outer join ref_satuan f
on e.id_satuan_output=f.id_satuan
where h.id_sub_unit=' . $sub_unit . ' and d.id_renja_program=' . $row->id_renja_program . ' and d.id_renja=' . $row2->id_renja);
              foreach($indikator as $row4)
              {
                  $height = ceil((strlen($row4->uraian_indikator_kegiatan_renja) / 48)) * 4;
                  
                  
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
                  PDF::MultiCell('61', $height, '- '.$row4->uraian_indikator_kegiatan_renja, '', 'L', 0, 0);
                  PDF::MultiCell('20', $height, '', 'L', 'L', 0, 0);
                  PDF::MultiCell('20', $height, $row4->angka_tahun.' '.$row4->singkatan_satuan , 'L', 'L', 0, 0);
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
  
  
  
  public function Apbd22($tahun)
  {
      
      
      $countrow=0;
      $totalrow=18;
      $id_renja=20;
      $sub_unit=7;
      $nama_sub="";
      $pagu_skpd_peg=0;
      $pagu_skpd_bj=0;
      $pagu_skpd_mod=0;
      $pagu_skpd_pend=0;
      $pagu_skpd_btl=0;
      $pagu_skpd=0;
      $pemda=Session::get('xPemda');
      
      // set document information
      PDF::SetCreator('BPKP');
      PDF::SetAuthor('BPKP');
      PDF::SetTitle('Simd@Perencanaan');
      PDF::SetSubject('Pra RKA');
      
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
      $header=array('INDIKATOR');
      $header2 = array('SKPD/Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
      //$tahun=DB::SELECT('SELECT tahun_renja FROM trx_renja_rancangan_program
       // GROUP BY tahun_renja');
      // Colors, line width AND bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
      PDF::SetFont('helvetica', '', 10);
//       foreach($tahun AS $row)
//       {
      PDF::SetFont('helvetica', 'B', 10);
      PDF::Cell('275', 5, $pemda, 0, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::SetFont('helvetica', 'B', 9);
      PDF::Cell('275', 5, 'REKAPITULASI RENCANA PENDAPATAN DAN BELANJA  PERANGKAT DAERAH', 0, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::SetFont('helvetica', 'B', 8);
      PDF::Cell('275', 5, 'Tahun Anggaran : '.$tahun, 0, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Ln();
      $countrow++;
      //}
      $apbd=DB::SELECT('SELECT concat(a.kd_urusan,".",a.kd_bidang,".",a.kd_unit) AS kode,a.id_unit,a.nm_unit, sum(a.jml_pend) AS pend, sum(a.jml_btl) AS btl,
            sum(a.jml_peg) AS peg, sum(a.jml_bj) AS bj, sum(a.jml_mod) AS modal  FROM
            (SELECT f.kd_urusan,f.kd_bidang,e.kd_unit,e.id_unit,e.nm_unit, 
            case o.kd_rek_1 when 4 then i.jml_belanja else 0 end AS jml_pend,
            case o.kd_rek_1 when 5 THEN 
            	case n.kd_rek_2 when 1 then i.jml_belanja else 0 END
            else 0 end AS jml_btl,
            case o.kd_rek_1 when 5 THEN 
            	case n.kd_rek_2 when 2 then 
            		case m.kd_rek_3 when 1 then i.jml_belanja else 0 end
            	else 0 END
            else 0 end AS jml_peg,
            case o.kd_rek_1 when 5 THEN 
            	case n.kd_rek_2 when 2 then 
            		case m.kd_rek_3 when 2 then i.jml_belanja else 0 end
            	else 0 END
            else 0 end AS jml_bj,
            case o.kd_rek_1 when 5 THEN 
            	case n.kd_rek_2 when 2 then 
            		case m.kd_rek_3 when 3 then i.jml_belanja else 0 end
            	else 0 END
            else 0 end AS jml_mod
            FROM trx_renja_rancangan_program a
            INNER JOIN trx_renja_rancangan b ON a.id_renja_program=b.id_renja_program
            INNER JOIN trx_renja_rancangan_pelaksana c ON b.id_renja=c.id_renja
            LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
            LEFT OUTER JOIN ref_unit e ON d.id_unit=e.id_unit
            LEFT OUTER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
            LEFT OUTER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
            INNER JOIN trx_renja_rancangan_aktivitas h ON c.id_pelaksana_renja=h.id_renja
            INNER JOIN trx_renja_rancangan_belanja i ON h.id_aktivitas_renja=i.id_lokasi_renja
            INNER JOIN ref_ssh_tarif j ON i.id_tarif_ssh=j.id_tarif_ssh
            LEFT OUTER  join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
            LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1 
            LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1 
            LEFT OUTER JOIN ref_rek_2 n ON m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1 
            LEFT OUTER JOIN ref_rek_1 o ON n.kd_rek_1=o.kd_rek_1
            WHERE a.tahun_renja='.$tahun.' ) a GROUP BY a.kd_urusan,a.kd_bidang,a.id_unit,a.nm_unit  ');
      
      // header
      PDF::SetFont('helvetica', 'B', 8);
      PDF::MultiCell('15', 7, 'Kode', 'LT', 'C', 0, 0);
      PDF::MultiCell('40', 7, 'Perangkat Daerah', 'LT', 'C', 0, 0);
      PDF::MultiCell('40', 7, 'Pendapatan', 'LT', 'C', 0, 0);
      PDF::MultiCell('140', 7, 'Belanja', 'LT', 'C', 0, 0);
      PDF::MultiCell('40', 7, 'Total Belanja', 'LRT', 'C', 0, 0);
      PDF::Ln();
      $countrow++;
      PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
      PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
      PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
      PDF::MultiCell('35', 7, 'Belanja Tidak Langsung', 'LT', 'C', 0, 0);
      PDF::MultiCell('105', 7, 'Belanja Langsung', 'LT', 'C', 0, 0);
      PDF::MultiCell('40', 7, '', 'LR', 'C', 0, 0);
      PDF::Ln();
      $countrow++;
      PDF::MultiCell('15', 10, '', 'LB', 'C', 0, 0);
      PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
      PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
      PDF::MultiCell('35', 10, '', 'LB', 'C', 0, 0);
      PDF::MultiCell('35', 10, 'Belanja Pegawai', 'LBT', 'C', 0, 0);
      PDF::MultiCell('35', 10, 'Belanja Barang & Jasa', 'LBT', 'C', 0, 0);
      PDF::MultiCell('35', 10, 'Belanja Modal', 'LBT', 'C', 0, 0);
      PDF::MultiCell('40', 10, '', 'LRB', 'C', 0, 0);
      PDF::Ln();
      $countrow++;
      foreach ($apbd AS $row)
      {

        $height=ceil((PDF::GetStringWidth($row->nm_unit)/45))*3;

          PDF::SetFont('helvetica', '', 7);
          PDF::MultiCell('15', $height, $row->kode, 'LB', 'L', 0, 0);
          PDF::MultiCell('40', $height, $row->nm_unit, 'LB', 'L', 0, 0);
          PDF::MultiCell('40', $height, number_format($row->pend,2,',','.'), 'LB', 'R', 0, 0);
          PDF::MultiCell('35', $height, number_format($row->btl,2,',','.'), 'LB', 'R', 0, 0);
          PDF::MultiCell('35', $height, number_format($row->peg,2,',','.'), 'LBT', 'R', 0, 0);
          PDF::MultiCell('35', $height, number_format($row->bj,2,',','.'), 'LBT', 'R', 0, 0);
          PDF::MultiCell('35', $height, number_format($row->modal,2,',','.'), 'LBT', 'R', 0, 0);
          PDF::MultiCell('40', $height, number_format($row->btl+$row->peg+$row->bj+$row->modal,2,',','.'), 'LRB', 'R', 0, 0);
          PDF::Ln();
          $pagu_skpd_pend=$pagu_skpd_pend+$row->pend;
          $pagu_skpd_btl=$pagu_skpd_btl+$row->btl;
          $pagu_skpd_peg=$pagu_skpd_peg+$row->peg;
          $pagu_skpd_bj=$pagu_skpd_bj+$row->bj;
          $pagu_skpd_mod=$pagu_skpd_mod+$row->modal;
          $pagu_skpd=$pagu_skpd+$row->btl+$row->peg+$row->bj+$row->modal;
          $countrow++;
          
          if($countrow>=$totalrow)
          {
              PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
              PDF::AddPage('L');
              $countrow=0;
              PDF::SetFont('helvetica', 'B', 8);
              PDF::MultiCell('15', 7, 'Kode', 'LT', 'C', 0, 0);
              PDF::MultiCell('40', 7, 'Perangkat Daerah', 'LT', 'C', 0, 0);
              PDF::MultiCell('40', 7, 'Pendapatan', 'LT', 'C', 0, 0);
              PDF::MultiCell('140', 7, 'Belanja', 'LT', 'C', 0, 0);
              PDF::MultiCell('40', 7, 'Total Belanja', 'LRT', 'C', 0, 0);
              PDF::Ln();
              $countrow++;
              PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
              PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
              PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
              PDF::MultiCell('35', 7, 'Belanja Tidak Langsung', 'LT', 'C', 0, 0);
              PDF::MultiCell('105', 7, 'Belanja Langsung', 'LT', 'C', 0, 0);
              PDF::MultiCell('40', 7, '', 'LR', 'C', 0, 0);
              PDF::Ln();
              $countrow++;
              PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
              PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
              PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
              PDF::MultiCell('35', 10, '', 'LB', 'C', 0, 0);
              PDF::MultiCell('35', 10, 'Belanja Pegawai', 'LBT', 'C', 0, 0);
              PDF::MultiCell('35', 10, 'Belanja Barang & Jasa', 'LBT', 'C', 0, 0);
              PDF::MultiCell('35', 10, 'Belanja Modal', 'LBT', 'C', 0, 0);
              PDF::MultiCell('40', 10, '', 'LRB', 'C', 0, 0);
              PDF::Ln();
              $countrow++;
          }
              
      }
      PDF::SetFont('helvetica', 'B', 7);
      PDF::MultiCell('55', 10, 'Total : ', 'LB', 'R', 0, 0);
      PDF::MultiCell('40', 10, number_format($pagu_skpd_pend,2,',','.'), 'LB', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_btl,2,',','.'), 'LB', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_peg,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_bj,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_mod,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('40', 10, number_format($pagu_skpd,2,',','.'), 'LRB', 'R', 0, 0);

      $template = new TemplateReport();
      $template->footerLandscape();
      PDF::Output('Apbd-'.$pemda.'.pdf', 'I');
  }
  
  public function RingkasApbd($tahun)
  {
      
      
      $countrow=0;
      $totalrow=37;
      $id_renja=20;
      $sub_unit=7;
      $nama_sub="";
      $pagu_skpd_peg=0;
      $pagu_skpd_bj=0;
      $pagu_skpd_mod=0;
      $pagu_skpd_pend=0;
      $pagu_skpd_btl=0;
      $pagu_skpd=0;
      $pemda=Session::get('xPemda');
      
      // set document information
      PDF::SetCreator('BPKP');
      PDF::SetAuthor('BPKP');
      PDF::SetTitle('Simd@Perencanaan');
      PDF::SetSubject('Pra RKA');
      
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
      PDF::AddPage('P');
      
      // column titles
      $header=array('INDIKATOR');
      $header2 = array('SKPD/Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
//       $tahun=DB::SELECT('SELECT tahun_renja FROM trx_renja_rancangan_program
//         GROUP BY tahun_renja');
      // Colors, line width AND bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
      PDF::SetFont('helvetica', '', 10);
//       foreach($tahun AS $row)
//       {
          PDF::SetFont('helvetica', 'B', 10);
          PDF::Cell('185', 5, $pemda, 0, 0, 'C', 0);
          PDF::Ln();
          $countrow++;
          PDF::SetFont('helvetica', 'B', 8);
          PDF::Cell('185', 5, 'Tahun Anggaran : '.$tahun, 0, 0, 'C', 0);
          PDF::Ln();
          $countrow++;
          PDF::SetFont('helvetica', 'B', 9);
          PDF::Cell('185', 5, 'RINGKASAN PENDAPATAN, BELANJA DAN PEMBIAYAAN PERANGKAT DAERAH', 0, 0, 'C', 0);
          PDF::Ln();
          $countrow++;
          PDF::Ln();
          $countrow++;
   //   }
      $rek1=DB::SELECT('SELECT o.kd_rek_1,o.nama_kd_rek_1,sum(a.jml_belanja) AS blj FROM ref_rek_5 k
          INNER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1 
          INNER JOIN ref_rek_3 m ON  l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1 
          INNER JOIN ref_rek_2 n ON   m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1 
          INNER JOIN ref_rek_1 o ON    n.kd_rek_1=o.kd_rek_1
          LEFT OUTER JOIN (SELECT tahun_renja,id_rekening_ssh, jml_belanja FROM trx_renja_rancangan_belanja WHERE tahun_renja='.$tahun.') a
          ON k.id_rekening=a.id_rekening_ssh WHERE o.kd_rek_1 in (4,5,6) GROUP BY o.kd_rek_1,o.nama_kd_rek_1');
      
      // header
      PDF::SetFont('helvetica', 'B', 8);
      PDF::MultiCell('30', 7, 'Kode Rekening', 'LBT', 'C', 0, 0);
      PDF::MultiCell('115', 7, 'Uraian', 'LBT', 'C', 0, 0);
      PDF::MultiCell('40', 7, 'Jumlah (Rp)', 1, 'C', 0, 0);
      PDF::Ln();
      $countrow++;
      PDF::MultiCell('30', 7, '1', 'LBT', 'C', 0, 0);
      PDF::MultiCell('115', 7, '2', 'LBT', 'C', 0, 0);
      PDF::MultiCell('40', 7, '3', 1, 'C', 0, 0);
      PDF::Ln();
      $countrow++;
      
      foreach ($rek1 AS $row)
      {
          $height=ceil((PDF::GetStringWidth($row->nama_kd_rek_1)/115))*3;
          PDF::SetFont('helvetica', 'B', 8);
          PDF::MultiCell('30', $height, $row->kd_rek_1, 'L', 'L', 0, 0);
          PDF::MultiCell('115', $height, $row->nama_kd_rek_1, 'L', 'L', 0, 0);
          PDF::MultiCell('40', $height, number_format($row->blj,2,',','.'), 'LRB', 'R', 0, 0);
          PDF::Ln();
          $countrow++;
          
          if($countrow>=$totalrow)
          {
              PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
              PDF::AddPage('P');
              $countrow=0;
              PDF::SetFont('helvetica', 'B', 8);
              PDF::MultiCell('30', 7, 'Kode Rekening', 'LBT', 'C', 0, 0);
              PDF::MultiCell('115', 7, 'Uraian', 'LBT', 'C', 0, 0);
              PDF::MultiCell('40', 7, 'Jumlah (Rp)', 1, 'C', 0, 0);
              PDF::Ln();
              $countrow++;
              PDF::MultiCell('30', 7, '1', 'LBT', 'C', 0, 0);
              PDF::MultiCell('115', 7, '2', 'LBT', 'C', 0, 0);
              PDF::MultiCell('40', 7, '3', 1, 'C', 0, 0);
              PDF::Ln();
              $countrow++;
          }
          
          $rek2=DB::SELECT('SELECT n.kd_rek_2,n.nama_kd_rek_2,sum(a.jml_belanja) AS blj FROM ref_rek_5 k
            INNER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1 
            INNER JOIN ref_rek_3 m ON  l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1 
            INNER JOIN ref_rek_2 n ON   m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1 
            INNER JOIN ref_rek_1 o ON    n.kd_rek_1=o.kd_rek_1
            LEFT OUTER JOIN (SELECT tahun_renja,id_rekening_ssh, jml_belanja FROM trx_renja_rancangan_belanja WHERE tahun_renja='.$tahun.') a
            ON k.id_rekening=a.id_rekening_ssh WHERE o.kd_rek_1 in (4,5,6) AND n.kd_rek_1='.$row->kd_rek_1.' GROUP BY n.kd_rek_2,n.nama_kd_rek_2');
          foreach ($rek2 AS $row2)
          {
              $height=ceil((PDF::GetStringWidth($row2->nama_kd_rek_2)/110))*3;
              PDF::SetFont('helvetica', 'B', 7);
              PDF::MultiCell('30', $height, $row->kd_rek_1.'.'.$row2->kd_rek_2, 'L', 'L', 0, 0);
              PDF::MultiCell('5', $height, '', 'L', 'L', 0, 0);
              PDF::MultiCell('110', $height, $row2->nama_kd_rek_2, 0, 'L', 0, 0);
              PDF::MultiCell('40', $height, number_format($row2->blj,2,',','.'), 'LRB', 'R', 0, 0);
              PDF::Ln();
              $countrow++;
              
              if($countrow>=$totalrow)
              {
                  PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                  PDF::AddPage('P');
                  $countrow=0;
                  PDF::SetFont('helvetica', 'B', 8);
                  PDF::MultiCell('30', 7, 'Kode Rekening', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('115', 7, 'Uraian', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, 'Jumlah (Rp)', 1, 'C', 0, 0);
                  PDF::Ln();
                  $countrow++;
                  PDF::MultiCell('30', 7, '1', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('115', 7, '2', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, '3', 1, 'C', 0, 0);
                  PDF::Ln();
                  $countrow++;
              }
              
              $rek3=DB::SELECT('SELECT m.kd_rek_3,m.nama_kd_rek_3,sum(a.jml_belanja) AS blj FROM ref_rek_5 k
                    INNER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1
                    INNER JOIN ref_rek_3 m ON  l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1
                    INNER JOIN ref_rek_2 n ON   m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1
                    INNER JOIN ref_rek_1 o ON    n.kd_rek_1=o.kd_rek_1
                    LEFT OUTER JOIN (SELECT tahun_renja,id_rekening_ssh, jml_belanja FROM trx_renja_rancangan_belanja WHERE tahun_renja='.$tahun.') a
                    ON k.id_rekening=a.id_rekening_ssh WHERE o.kd_rek_1 in (4,5,6) AND n.kd_rek_1='.$row->kd_rek_1.' AND m.kd_rek_2='.$row2->kd_rek_2.' 
                    GROUP BY m.kd_rek_3,m.nama_kd_rek_3');

              foreach ($rek3 AS $row3)
              {
                  $height=ceil((PDF::GetStringWidth($row3->nama_kd_rek_3)/105))*3;
                  PDF::SetFont('helvetica', '', 7);
                  PDF::MultiCell('30', $height, $row->kd_rek_1.'.'.$row2->kd_rek_2.'.'.$row3->kd_rek_3, 'L', 'L', 0, 0);
                  PDF::MultiCell('10', $height, '', 'L', 'L', 0, 0);
                  PDF::MultiCell('105', $height, $row3->nama_kd_rek_3, 0, 'L', 0, 0);
                  PDF::MultiCell('40', $height, number_format($row3->blj,2,',','.'), 'LR', 'R', 0, 0);
                  PDF::Ln();
                  $countrow++;
                  
                  if($countrow>=$totalrow)
                  {
                      PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                      PDF::AddPage('P');
                      $countrow=0;
                      PDF::SetFont('helvetica', 'B', 8);
                      PDF::MultiCell('30', 7, 'Kode Rekening', 'LBT', 'C', 0, 0);
                      PDF::MultiCell('115', 7, 'Uraian', 'LBT', 'C', 0, 0);
                      PDF::MultiCell('40', 7, 'Jumlah (Rp)', 1, 'C', 0, 0);
                      PDF::Ln();
                      $countrow++;
                      PDF::MultiCell('30', 7, '1', 'LBT', 'C', 0, 0);
                      PDF::MultiCell('115', 7, '2', 'LBT', 'C', 0, 0);
                      PDF::MultiCell('40', 7, '3', 1, 'C', 0, 0);
                      PDF::Ln();
                      $countrow++;
                  }
                  
              }
     }
      }
      PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
      $template = new TemplateReport();
      $template->footerPotrait();
      PDF::Output('RingkasApbd-'.$pemda.'.pdf', 'I');
  }

  
  public function KompilasiAktivitas($id_kegiatan)
  {
      
      $countrow=0;
      $totalrow=36;
      $id_renja=$id_kegiatan;
      $nama_keg="";
      $pemda=Session::get('xPemda');
      $kode_kegiatan="";
      $nama_kegiatan="";
      
      // set document information
      PDF::SetCreator('BPKP');
      PDF::SetAuthor('BPKP');
      PDF::SetTitle('Simd@Perencanaan');
      PDF::SetSubject('Pra RKA');
      
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
      PDF::AddPage('P');
      
      // column titles
      $header=array('INDIKATOR');
      $header2 = array('SKPD/Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
      
      // Colors, line width AND bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
      PDF::SetFont('helvetica', '', 10);
      
      //Header
      $kegiatan=DB::SELECT('SELECT a.tahun_renja,g.kd_urusan,f.kd_bidang,e.kd_unit,d.kd_sub,b.no_urut AS no_urut_pro,a.no_urut AS no_urut_keg, a.uraian_kegiatan_renstra,
        b.uraian_program_renstra, d.nm_sub, e.nm_unit,g.nm_urusan FROM trx_renja_rancangan a
        INNER JOIN trx_renja_rancangan_program b ON a.id_renja_program=b.id_renja_program
        INNER JOIN trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
        INNER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
        INNER JOIN ref_unit e ON d.id_unit=e.id_unit
        INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
        INNER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
        WHERE a.id_renja='.$id_renja);
      foreach($kegiatan AS $row)
      {
          $countrow++;
          $nama_keg=$row->uraian_kegiatan_renstra;
          PDF::SetFont('helvetica', '', 10);
          PDF::Cell('185', 5, $pemda, 'LRT', 0, 'C', 0);
          PDF::Ln();
          $countrow++;
          PDF::SetFont('helvetica', '', 8);
          PDF::Cell('185', 5, 'Tahun Anggaran : '.$row->tahun_renja, 'LRB', 0, 'C', 0);
          PDF::Ln();
          $countrow++;
          
          $countrow++;
          // PDF::SetFont('', 'B');
          PDF::SetFont('helvetica', '', 7);
          PDF::Cell('30', 5, 'Urusan', 'LT', 0, 'L', 0);
          PDF::Cell('5', 5, ':', 'T', 0, 'L', 0);
          PDF::Cell('15', 5,$row->kd_urusan, 'T', 0, 'L', 0);
          PDF::Cell('135', 5,$row->nm_urusan, 'RT', 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('30', 5, 'Perangkat Daerah', 'L', 0, 'L', 0);
          PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
          PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_unit, 0, 0, 'L', 0);
          PDF::Cell('135', 5,$row->nm_unit, 'R', 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('30', 5, 'Sub Perangkat Daerah', 'L', 0, 'L', 0);
          PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
          PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_unit.'.'.$row->kd_sub, 0, 0, 'L', 0);
          PDF::Cell('135', 5,$row->nm_sub, 'R', 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('30', 5, 'Program', 'L', 0, 'L', 0);
          PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
          PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_unit.'.'.$row->kd_sub.'.'.$row->no_urut_pro, 0, 0, 'L', 0);
          PDF::Cell('135', 5,$row->uraian_program_renstra, 'R', 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          PDF::Cell('30', 5, 'Kegiatan', 'LB', 0, 'L', 0);
          PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
          PDF::Cell('15', 5,$row->kd_urusan.'.'.$row->kd_unit.'.'.$row->kd_sub.'.'.$row->no_urut_pro.'.'.$row->no_urut_keg, 'B', 0, 'L', 0);
          PDF::Cell('135', 5,$row->uraian_kegiatan_renstra, 'RB', 0, 'L', 0);
          PDF::Ln();
          $countrow++;
          $kode_kegiatan=$row->kd_urusan.'.'.$row->kd_unit.'.'.$row->kd_sub.'.'.$row->no_urut_pro.'.'.$row->no_urut_keg;
          $nama_kegiatan=$row->uraian_kegiatan_renstra;
      }
      
      $lokasi=DB::SELECT('SELECT a.uraian_kegiatan_renstra,d.nama_lokasi FROM trx_renja_rancangan a 
        INNER JOIN trx_renja_rancangan_pelaksana b ON a.id_renja=b.id_renja
        INNER JOIN trx_renja_rancangan_lokasi c ON b.id_pelaksana_renja=c.id_pelaksana_renja
        INNER JOIN ref_lokasi d ON c.id_lokasi=d.id_lokasi
        WHERE a.id_renja='.$id_renja);

      PDF::Cell('30', 5, 'Lokasi', 'LB', 0, 'L', 0);
      PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
      $c=0;
      $gablok="";
      foreach($lokasi AS $row)
      {          
          $countrow++;
          if($c==0)
          {
              $gablok=$gablok.''.$row->nama_lokasi;
          }
          else
          {
              $gablok=$gablok.', '.$row->nama_lokasi;
          }
          $c=$c+1;
      }
      PDF::Cell('150', 5,$gablok, 'RB', 0, 'L', 0);
      PDF::Ln();
      $countrow++;
      
      $pagu=DB::SELECT('SELECT a.tahun_renja-c.tahun_rkpd AS selisih,c.tahun_rkpd,c.pagu_tahun_kegiatan,a.pagu_tahun_kegiatan AS pagu_n FROM trx_renja_rancangan a
          INNER JOIN trx_rkpd_renstra c ON a.id_kegiatan_renstra=c.id_kegiatan_renstra
          WHERE a.id_renja='.$id_renja.' AND (a.tahun_renja-c.tahun_rkpd in (-1,0,1)) order by c.tahun_rkpd ASC');
      $c=0;
      foreach ($pagu AS $row)
      {
          if($pagu>0)
          {
              if($row->selisih==1)
              {
                  PDF::Cell('30', 5, 'Jumlah Tahun n-1', 'L', 0, 'L', 0);
                  PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
                  PDF::Cell('40', 5, 'Rp.'.number_format($row->pagu_tahun_kegiatan,2,',','.'), 0, 0, 'R', 0);
                  PDF::Cell('110', 5, '', 'R', 0, 'R', 0);
                  PDF::Ln();
                  $countrow++;
              }
//               else if ($row->selisih==0)
//               {
//                   PDF::Cell('30', 5, 'Jumlah Tahun n', 'L', 0, 'L', 0);
//                   PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
//                   PDF::Cell('40', 5, 'Rp.'.number_format($row->pagu_n,2,',','.'), 0, 0, 'R', 0);
//                   PDF::Cell('110', 5, '', 'R', 0, 'R', 0);
//                   PDF::Ln();
//                   $countrow++;
//               }
//               else if($row->selisih==-1)
//               {
//                   PDF::Cell('30', 5, 'Jumlah Tahun n+1', 'LB', 0, 'L', 0);
//                   PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
//                   PDF::Cell('40', 5, 'Rp.'.number_format($row->pagu_tahun_kegiatan,2,',','.'), 'B', 0, 'R', 0);
//                   PDF::Cell('110', 5, '', 'RB', 0, 'R', 0);
//                   PDF::Ln();
//                   $countrow++;
//               }
          }
         
          $c=$c+1;
      }
      $pagu2=DB::SELECT('SELECT  a.id_renja,IFNULL(sum(e.jml_belanja),0) AS jml_belanja FROM trx_renja_rancangan a
          left outer join trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
          left outer join trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
          left outer join trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
          left outer join ref_ssh_tarif f ON e.id_tarif_ssh=f.id_tarif_ssh
          WHERE a.id_renja='.$id_renja.'  GROUP BY a.id_renja');
      foreach($pagu2 AS $row)
      {         
            PDF::Cell('30', 5, 'Jumlah Tahun n', 'L', 0, 'L', 0);
            PDF::Cell('5', 5, ':', 0, 0, 'L', 0);
            PDF::Cell('40', 5, 'Rp.'.number_format($row->jml_belanja,2,',','.'), 0, 0, 'R', 0);
            PDF::Cell('110', 5, '', 'R', 0, 'R', 0);
            PDF::Ln();
            $countrow++;
                        
      }
      $pagu3=DB::SELECT('SELECT a.tahun_renja-c.tahun_rkpd AS selisih,c.tahun_rkpd,c.pagu_tahun_kegiatan,a.pagu_tahun_kegiatan AS pagu_n FROM trx_renja_rancangan a
          INNER JOIN trx_rkpd_renstra c ON a.id_kegiatan_renstra=c.id_kegiatan_renstra
          WHERE a.id_renja='.$id_renja.' AND (a.tahun_renja-c.tahun_rkpd in (-1,0,1)) order by c.tahun_rkpd ASC');
      $c=0;
      foreach ($pagu3 AS $row)
      {
          if($pagu>0)
          {
              if($row->selisih==-1)
              {
                  PDF::Cell('30', 5, 'Jumlah Tahun n+1', 'LB', 0, 'L', 0);
                  PDF::Cell('5', 5, ':', 'B', 0, 'L', 0);
                  PDF::Cell('40', 5, 'Rp.'.number_format($row->pagu_tahun_kegiatan,2,',','.'), 'B', 0, 'R', 0);
                  PDF::Cell('110', 5, '', 'RB', 0, 'R', 0);
                  PDF::Ln();
                  $countrow++;
              }
          }
          
          $c=$c+1;
      }
      PDF::Cell('185', 5, 'INDIKATOR DAN TOLOK UKUR KINERJA BELANJA LANGSUNG', 'LRB', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('30', 5, 'INDIKATOR', 'LBR', 0, 'C', 0);
      PDF::Cell('80', 5, 'TOLOK UKUR KINERJA', 'BR', 0, 'C', 0);
      PDF::Cell('75', 5, 'TARGET KINERJA', 'RB', 0, 'C', 0);
      PDF::Ln();
      $countrow++;

      $pagu2=DB::SELECT('SELECT  a.id_renja,IFNULL(sum(e.jml_belanja),0) AS jml_belanja FROM trx_renja_rancangan a
          left outer join trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
          left outer join trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
          left outer join trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
          left outer join ref_ssh_tarif f ON e.id_tarif_ssh=f.id_tarif_ssh
          WHERE a.id_renja='.$id_renja.'  GROUP BY a.id_renja');

      PDF::Cell('30', 5, 'MASUKAN', 'LBR', 0, 'L', 0);
      PDF::Cell('80', 5, 'Jumlah Dana', 'BR', 0, 'L', 0);
      foreach($pagu2 AS $row)
      {
          PDF::Cell('75', 5, 'Rp.'.number_format($row->jml_belanja,2,',','.'), 'RB', 0, 'L', 0);
      }
      PDF::Ln();
      $countrow++;

      $ind=DB::SELECT('SELECT b.tolok_ukur_indikator,b.angka_tahun FROM trx_renja_rancangan a
        INNER JOIN trx_renja_rancangan_indikator b ON a.id_renja=b.id_renja
        WHERE a.id_renja='.$id_renja);

      $c=0;
      foreach($ind AS $row)
      {
          if($c==0)
          {
              PDF::Cell('30', 5, 'KELUARAN', 'LR', 0, 'L', 0);
              PDF::Cell('80', 5, $row->tolok_ukur_indikator, 'R', 0, 'L', 0);
              PDF::Cell('75', 5,number_format($row->angka_tahun,2,',','.'), 'R', 0, 'L', 0);
          }
          else
          {
              PDF::Cell('30', 5, '', 'LR', 0, 'L', 0);
              PDF::Cell('80', 5, $row->tolok_ukur_indikator, 'R', 0, 'L', 0);
              PDF::Cell('75', 5,number_format($row->angka_tahun,2,',','.'), 'R', 0, 'L', 0);
          }
          
          PDF::Ln();
          $countrow++;
          $c=$c+1;
      }
      // Header Column///////////////////////////////////////////////////////////////////////////////////////////////////////////
      PDF::Cell('185', 5, 'RINCIAN  AKTIVITAS  PER  KEGIATAN  PERANGKAT DAERAH', 1, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('75', 5, '', 'L', 0, 'C', 0);
      PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
      PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      
      PDF::Cell('20', 5, 'KODE AKTIVITAS', 'LB', 0, 'C', 0);
      PDF::Cell('75', 5, 'URAIAN', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
      PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      
      PDF::Cell('20', 5, '1', 'LB', 0, 'C', 0);
      PDF::Cell('75', 5, '2', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
      PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
      PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      
      // End Header Column/////////////////////////////////////////////////////////////////////////////////////////////////////
      $total=DB::SELECT('SELECT  sum(e.jml_belanja) AS jml_belanja FROM trx_renja_rancangan a
          left outer join trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
          left outer join trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
          left outer join trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
          left outer join ref_ssh_tarif f ON e.id_tarif_ssh=f.id_tarif_ssh
          WHERE a.id_renja='.$id_renja.'  GROUP BY d.id_aktivitas_renja,d.uraian_aktivitas_kegiatan');

      $totalpagu=0;
      foreach ($total AS $row)
      {
          $totalpagu=$totalpagu+$row->jml_belanja;
      }
      
          $akt=DB::SELECT('SELECT  d.id_aktivitas_renja,d.uraian_aktivitas_kegiatan, sum(e.jml_belanja) AS jml_belanja, e.volume_1, f.singkatan_satuan AS satuan1,
              e.volume_2, g.singkatan_satuan AS satuan2, d.pagu_rata2 AS harga_satuan FROM trx_renja_rancangan a
              left outer join trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
              left outer join trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
              left outer join trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
              LEFT OUTER JOIN ref_satuan f ON d.id_satuan_1 =f.id_satuan
              LEFT OUTER JOIN ref_satuan g ON d.id_satuan_2=g.id_satuan
              WHERE a.id_renja='.$id_renja.' AND e.sumber_aktivitas not in (1)
              GROUP BY d.id_aktivitas_renja,d.uraian_aktivitas_kegiatan,e.volume_1, f.singkatan_satuan,
              e.volume_2, g.singkatan_satuan , d.pagu_rata2
              union all
              SELECT  d.id_aktivitas_renja,d.uraian_aktivitas_kegiatan, sum(e.jml_belanja) AS jml_belanja,"" AS volume_1,"" AS volume_2, "" AS satuan1, "" AS satuan2,"" AS harga_satuan
              FROM trx_renja_rancangan a 
              left outer join trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
              left outer join trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
              left outer join trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
              LEFT OUTER JOIN ref_satuan f ON d.id_satuan_1 =f.id_satuan
              LEFT OUTER JOIN ref_satuan g ON d.id_satuan_2=g.id_satuan
              WHERE a.id_renja='.$id_renja.' AND d.id_aktivitas_asb is null
              GROUP BY d.id_aktivitas_renja,d.uraian_aktivitas_kegiatan');

          $height=ceil((PDF::GetStringWidth($nama_kegiatan)/75))*3;

          PDF::SetFont('helvetica', 'B', 7);
          PDF::MultiCell('20', $height, $kode_kegiatan, 'LB',  'L',0, 0);
          PDF::MultiCell('75', $height, $nama_kegiatan, 'LB',  'L',0, 0);
          PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
          PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
          PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
          PDF::MultiCell('30', $height, number_format($totalpagu,2,',','.'), 'LRB', 'R',0, 0);
          PDF::Ln();
          $r=1;
          foreach ($akt AS $row2)
          {   
              $height=ceil((PDF::GetStringWidth($row2->uraian_aktivitas_kegiatan)/75))*3;

              PDF::SetFont('helvetica', '', 7);
              PDF::MultiCell('20', $height, $kode_kegiatan.'.'.$r, 'LB',  'L',0, 0);
              PDF::MultiCell('75', $height, $row2->uraian_aktivitas_kegiatan, 'LB',  'L',0, 0);
              if($row2->volume_1>0)
              {
              PDF::MultiCell('20', $height, number_format($row2->volume_1*$row2->volume_2,2,',','.'), 'LB',  'R',0, 0);
              }
              else {
                  PDF::MultiCell('20', $height, '', 'LB',  'R',0, 0);
              }
              if($row2->satuan2>0)
              {
              PDF::MultiCell('20', $height, $row2->satuan1.' x '.$row2->satuan2, 'LB',  'L',0, 0);
              }
              else 
              {
                  PDF::MultiCell('20', $height, $row2->satuan1, 'LB',  'L',0, 0);
              }
              if($row2->harga_satuan>0)
              {
                  PDF::MultiCell('20', $height, number_format($row2->harga_satuan,2,',','.'), 'LB',  'R',0, 0);
              }
              else 
              {
                  PDF::MultiCell('20', $height, '', 'LB',  'R',0, 0);
              }
              
              PDF::MultiCell('30', $height, number_format($row2->jml_belanja,2,',','.'), 'LRB', 'R',0, 0);
              PDF::Ln();
              $countrow++;
              
              if($countrow>=$totalrow)
              {
                  PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                  PDF::AddPage('P');
                  $countrow=0;
                  PDF::Cell('185', 5, 'RINCIAN  AKTIVITAS  PER  KEGIATAN  PERANGKAT DAERAH', 1, 0, 'C', 0);
                  PDF::Ln();
                  $countrow++;
                  PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('75', 5, '', 'L', 0, 'C', 0);
                  PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
                  PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
                  PDF::Ln();
                  $countrow++;
                  
                  PDF::Cell('20', 5, 'Kode', 'LB', 0, 'C', 0);
                  PDF::Cell('75', 5, 'URAIAN', 'LB', 0, 'C', 0);
                  PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
                  PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
                  PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
                  PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
                  PDF::Ln();
                  $countrow++;
                  
                  PDF::Cell('20', 5, '1', 'LB', 0, 'C', 0);
                  PDF::Cell('75', 5, '2', 'LB', 0, 'C', 0);
                  PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
                  PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
                  PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
                  PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
                  PDF::Ln();
                  $countrow++;
              }
              $rinc=DB::SELECT('SELECT e.id_belanja_renja,f.uraian_tarif_ssh,e.volume_1,e.volume_2,g.uraian_satuan AS satuan_1, h.uraian_satuan AS satuan_2, e.harga_satuan, e.jml_belanja
                  FROM trx_renja_rancangan a
                  INNER JOIN trx_renja_rancangan_pelaksana c ON a.id_renja=c.id_renja
                  INNER JOIN trx_renja_rancangan_aktivitas d ON c.id_pelaksana_renja=d.id_renja
                  INNER JOIN trx_renja_rancangan_belanja e ON d.id_aktivitas_renja=e.id_lokasi_renja
                  INNER JOIN ref_ssh_tarif f ON e.id_tarif_ssh=f.id_tarif_ssh
                  LEFT OUTER JOIN ref_satuan g ON e.id_satuan_1=g.id_satuan
                  left OUTER JOIN ref_satuan h ON e.id_satuan_2=h.id_satuan
                  WHERE a.id_renja='.$id_renja.' AND d.id_aktivitas_renja='.$row2->id_aktivitas_renja);
              foreach ($rinc AS $row3)
              {
                  
                  $height=ceil((PDF::GetStringWidth($row3->uraian_tarif_ssh)/70))*3;

                  PDF::MultiCell('20', $height, '', 'LB',  'L',0, 0);
                  PDF::MultiCell('5', $height, '', 'LB',  'L',0, 0);
                  PDF::MultiCell('70', $height, '- '.$row3->uraian_tarif_ssh, 'B',  'L',0, 0);
                  PDF::MultiCell('20', $height, number_format($row3->volume_1*$row3->volume_2,2,',','.'), 'LB',  'R',0, 0);
                  if($row3->satuan_2>0)
                  {
                  PDF::MultiCell('20', $height, $row3->satuan_1.' x '.$row3->satuan_2, 'LB',  'L', 0,0);
                  }
                  else 
                  {
                      PDF::MultiCell('20', $height, $row3->satuan_1, 'LB',  'L', 0,0);
                  }
                  PDF::MultiCell('20', $height, number_format($row3->harga_satuan,2,',','.'), 'LB', 'R',0, 0);
                  PDF::MultiCell('30', $height, number_format($row3->jml_belanja,2,',','.'), 'LRB', 'R',0, 0);
                  PDF::Ln();
                  $countrow++;
                  
                  if($countrow>=$totalrow)
                  {
                      PDF::MultiCell('185', 7, '', 'T', 'R', 0, 0);
                      PDF::AddPage('P');
                      $countrow=0;
                      PDF::Cell('185', 5, 'RINCIAN  AKTIVITAS  PER  KEGIATAN  PERANGKAT DAERAH', 1, 0, 'C', 0);
                      PDF::Ln();
                      $countrow++;
                      PDF::Cell('20', 5, '', 'L', 0, 'C', 0);
                      PDF::Cell('75', 5, '', 'L', 0, 'C', 0);
                      PDF::Cell('60', 5, 'RINCIAN PERHITUNGAN', 'LB', 0, 'C', 0);
                      PDF::Cell('30', 5, '', 'LR', 0, 'C', 0);
                      PDF::Ln();
                      $countrow++;
                      
                      PDF::Cell('20', 5, 'Kode', 'LB', 0, 'C', 0);
                      PDF::Cell('75', 5, 'URAIAN', 'LB', 0, 'C', 0);
                      PDF::Cell('20', 5, 'VOLUME', 'LB', 0, 'C', 0);
                      PDF::Cell('20', 5, 'SATUAN', 'LB', 0, 'C', 0);
                      PDF::Cell('20', 5, 'HARGA (Rp.)', 'LB', 0, 'C', 0);
                      PDF::Cell('30', 5, 'JUMLAH (Rp.)', 'LRB', 0, 'C', 0);
                      PDF::Ln();
                      $countrow++;
                      
                      PDF::Cell('20', 5, '1', 'LB', 0, 'C', 0);
                      PDF::Cell('75', 5, '2', 'LB', 0, 'C', 0);
                      PDF::Cell('20', 5, '3', 'LB', 0, 'C', 0);
                      PDF::Cell('20', 5, '4', 'LB', 0, 'C', 0);
                      PDF::Cell('20', 5, '5', 'LB', 0, 'C', 0);
                      PDF::Cell('30', 5, '6', 'LRB', 0, 'C', 0);
                      PDF::Ln();
                      $countrow++;
                  }
                  
              }
              $r++;
          }
      
      
      
      // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
      
      
      // close AND output PDF document
      $template = new TemplateReport();
      $template->footerPotrait();
      PDF::Output('Kompilasi Aktivitas -'.$nama_keg.'.pdf', 'I');
  }
 
  public function ApbdUrusan($tahun)
  {
      
      
      $countrow=0;
      $totalrow=18;
      $id_renja=20;
      $sub_unit=7;
      $nama_sub="";
      $pagu_skpd_peg=0;
      $pagu_skpd_bj=0;
      $pagu_skpd_mod=0;
      $pagu_skpd_pend=0;
      $pagu_skpd_btl=0;
      $pagu_skpd=0;
      $pemda=Session::get('xPemda');
      
      // set document information
      PDF::SetCreator('BPKP');
      PDF::SetAuthor('BPKP');
      PDF::SetTitle('Simd@Perencanaan');
      PDF::SetSubject('Pra RKA');
      
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
      $header=array('INDIKATOR');
      $header2 = array('SKPD/Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
      //$tahun=DB::SELECT('SELECT tahun_renja FROM trx_renja_rancangan_program
      // GROUP BY tahun_renja');
      // Colors, line width AND bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
      PDF::SetFont('helvetica', '', 10);
      //       foreach($tahun AS $row)
          //       {
      PDF::SetFont('helvetica', 'B', 10);
      PDF::Cell('275', 5, $pemda, 0, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::SetFont('helvetica', 'B', 9);
      PDF::Cell('275', 5, 'RINGKASAN PENDAPATAN DAN BELANJA MENURUT ORGANISASI DAN URUSAN PEMERINTAHAN', 0, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::SetFont('helvetica', 'B', 8);
      PDF::Cell('275', 5, 'Tahun Anggaran : '.$tahun, 0, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Ln();
      $countrow++;
      //}
      $apbd=DB::SELECT('SELECT concat(a.kd_urusan,".",a.kd_bidang,".",a.kd_unit) AS kode,a.id_unit,a.nm_unit, sum(a.jml_pend) AS pend, sum(a.jml_btl) AS btl, 
          sum(a.jml_peg) AS peg, sum(a.jml_bj) AS bj, sum(a.jml_mod) AS modal  FROM
          (SELECT f.kd_urusan,f.kd_bidang,e.kd_unit,e.id_unit,e.nm_unit,
          case o.kd_rek_1 when 4 then i.jml_belanja else 0 end AS jml_pend,
          case o.kd_rek_1 when 5 THEN
          	case n.kd_rek_2 	when 1 then i.jml_belanja else 0 END
          else 0 end AS jml_btl,
          case o.kd_rek_1 when 5 THEN
          	case n.kd_rek_2 when 2 then
          		case m.kd_rek_3 when 1 then i.jml_belanja else 0 end
          	else 0 END
          else 0 end AS jml_peg,
          case o.kd_rek_1 when 5 THEN
          	case n.kd_rek_2 when 2 then
          		case m.kd_rek_3 when 2 then i.jml_belanja else 0 end
          	else 0 END
          else 0 end AS jml_bj,
          case o.kd_rek_1 when 5 THEN
          	case n.kd_rek_2 when 2 then
          		case m.kd_rek_3 when 3 then i.jml_belanja else 0 end
          	else 0 END
          else 0 end AS jml_mod          
          FROM trx_renja_rancangan_program a
          INNER JOIN trx_renja_rancangan b ON a.id_renja_program=b.id_renja_program
          INNER JOIN trx_renja_rancangan_pelaksana c ON b.id_renja=c.id_renja
          LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
          LEFT OUTER JOIN ref_unit e ON d.id_unit=e.id_unit
          LEFT OUTER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
          LEFT OUTER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
          INNER JOIN trx_renja_rancangan_aktivitas h ON c.id_pelaksana_renja=h.id_renja
          INNER JOIN trx_renja_rancangan_belanja i ON h.id_aktivitas_renja=i.id_lokasi_renja
          INNER JOIN ref_ssh_tarif j ON i.id_tarif_ssh=j.id_tarif_ssh
          LEFT OUTER  join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
          LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1
          LEFT OUTER JOIN ref_rek_3 m ON  l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1
          LEFT OUTER JOIN ref_rek_2 n ON   m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1
          LEFT OUTER JOIN ref_rek_1 o ON    n.kd_rek_1=o.kd_rek_1
          WHERE a.tahun_renja='.$tahun.' ) a GROUP BY a.kd_urusan,a.kd_bidang,a.kd_unit,a.id_unit,a.nm_unit  ');
      
      // header
      PDF::SetFont('helvetica', 'B', 8);
      PDF::MultiCell('15', 7, 'Kode', 'LT', 'C', 0, 0);
      PDF::MultiCell('40', 7, 'Perangkat Daerah', 'LT', 'C', 0, 0);
      PDF::MultiCell('40', 7, 'Pendapatan', 'LT', 'C', 0, 0);
      PDF::MultiCell('140', 7, 'Belanja', 'LT', 'C', 0, 0);
      PDF::MultiCell('40', 7, 'Total Belanja', 'LRT', 'C', 0, 0);
      PDF::Ln();
      $countrow++;
      PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
      PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
      PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
      PDF::MultiCell('35', 7, 'Belanja Tidak Langsung', 'LT', 'C', 0, 0);
      PDF::MultiCell('105', 7, 'Belanja Langsung', 'LT', 'C', 0, 0);
      PDF::MultiCell('40', 7, '', 'LR', 'C', 0, 0);
      PDF::Ln();
      $countrow++;
      PDF::MultiCell('15', 10, '', 'LB', 'C', 0, 0);
      PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
      PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
      PDF::MultiCell('35', 10, '', 'LB', 'C', 0, 0);
      PDF::MultiCell('35', 10, 'Belanja Pegawai', 'LBT', 'C', 0, 0);
      PDF::MultiCell('35', 10, 'Belanja Barang & Jasa', 'LBT', 'C', 0, 0);
      PDF::MultiCell('35', 10, 'Belanja Modal', 'LBT', 'C', 0, 0);
      PDF::MultiCell('40', 10, '', 'LRB', 'C', 0, 0);
      PDF::Ln();
      $countrow++;
      foreach ($apbd AS $row)
      {
          PDF::SetFont('helvetica', '', 7);
          PDF::MultiCell('15', 10, $row->kode, 'LT', 'L', 0, 0);
          PDF::MultiCell('40', 10, $row->nm_unit, 'LT', 'L', 0, 0);
          PDF::MultiCell('40', 10, number_format($row->pend,2,',','.'), 'LT', 'R', 0, 0);
          PDF::MultiCell('35', 10, number_format($row->btl,2,',','.'), 'LT', 'R', 0, 0);
          PDF::MultiCell('35', 10, number_format($row->peg,2,',','.'), 'LT', 'R', 0, 0);
          PDF::MultiCell('35', 10, number_format($row->bj,2,',','.'), 'LT', 'R', 0, 0);
          PDF::MultiCell('35', 10, number_format($row->modal,2,',','.'), 'LT', 'R', 0, 0);
          PDF::MultiCell('40', 10, number_format($row->btl+$row->peg+$row->bj+$row->modal,2,',','.'), 'LRT', 'R', 0, 0);
          PDF::Ln();
          $pagu_skpd_pend=$pagu_skpd_pend+$row->pend;
          $pagu_skpd_btl=$pagu_skpd_btl+$row->btl;
          $pagu_skpd_peg=$pagu_skpd_peg+$row->peg;
          $pagu_skpd_bj=$pagu_skpd_bj+$row->bj;
          $pagu_skpd_mod=$pagu_skpd_mod+$row->modal;
          $pagu_skpd=$pagu_skpd+$row->btl+$row->peg+$row->bj+$row->modal;
          $countrow++;
          
          if($countrow>=$totalrow)
          {
              PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
              PDF::AddPage('L');
              $countrow=0;
              PDF::SetFont('helvetica', 'B', 8);
              PDF::MultiCell('15', 7, 'Kode', 'LT', 'C', 0, 0);
              PDF::MultiCell('40', 7, 'Perangkat Daerah', 'LT', 'C', 0, 0);
              PDF::MultiCell('40', 7, 'Pendapatan', 'LT', 'C', 0, 0);
              PDF::MultiCell('140', 7, 'Belanja', 'LT', 'C', 0, 0);
              PDF::MultiCell('40', 7, 'Total Belanja', 'LRT', 'C', 0, 0);
              PDF::Ln();
              $countrow++;
              PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
              PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
              PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
              PDF::MultiCell('35', 7, 'Belanja Tidak Langsung', 'LT', 'C', 0, 0);
              PDF::MultiCell('105', 7, 'Belanja Langsung', 'LT', 'C', 0, 0);
              PDF::MultiCell('40', 7, '', 'LR', 'C', 0, 0);
              PDF::Ln();
              $countrow++;
              PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
              PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
              PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
              PDF::MultiCell('35', 10, '', 'LB', 'C', 0, 0);
              PDF::MultiCell('35', 10, 'Belanja Pegawai', 'LBT', 'C', 0, 0);
              PDF::MultiCell('35', 10, 'Belanja Barang & Jasa', 'LBT', 'C', 0, 0);
              PDF::MultiCell('35', 10, 'Belanja Modal', 'LBT', 'C', 0, 0);
              PDF::MultiCell('40', 10, '', 'LRB', 'C', 0, 0);
              PDF::Ln();
              $countrow++;
          }
          $apbd_ur=DB::SELECT('SELECT concat(a.kd_urusan,".",a.kd_bidang) AS kode, concat(a.nm_urusan," - ",a.nm_bidang) AS uraian,
                sum(a.jml_pend) AS pend, sum(a.jml_btl) AS btl, sum(a.jml_peg) AS peg, sum(a.jml_bj) AS bj, sum(a.jml_mod) AS modal FROM
                (SELECT f.kd_urusan,f.kd_bidang, f.nm_bidang, g.nm_urusan,
                case o.kd_rek_1 when 4 then i.jml_belanja else 0 end AS jml_pend,
                case o.kd_rek_1 when 5 THEN
                	case n.kd_rek_2 when 1 then i.jml_belanja else 0 END
                else 0 end AS jml_btl,
                case o.kd_rek_1 when 5 THEN
                	case n.kd_rek_2 when 2 then
                		case m.kd_rek_3 when 1 then i.jml_belanja	else 0 end
                	else 0 END
                else 0 end AS jml_peg,
                case o.kd_rek_1 when 5 THEN
                	case n.kd_rek_2 when 2 then
                		case m.kd_rek_3 when 2 then i.jml_belanja else 0 end
                	else 0 END
                else 0 end AS jml_bj,
                case o.kd_rek_1 when 5 THEN
                	case n.kd_rek_2 when 2 then
                		case m.kd_rek_3 when 3 then i.jml_belanja else 0 end
                	else 0 END
                else 0 end AS jml_mod              
                FROM trx_renja_rancangan_program a
                INNER JOIN trx_renja_rancangan b ON a.id_renja_program=b.id_renja_program
                INNER JOIN trx_renja_rancangan_pelaksana c ON b.id_renja=c.id_renja
                LEFT OUTER JOIN ref_bidang f ON a.id_bidang=f.id_bidang
                LEFT OUTER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
                INNER JOIN trx_renja_rancangan_aktivitas h ON c.id_pelaksana_renja=h.id_renja
                INNER JOIN trx_renja_rancangan_belanja i ON h.id_aktivitas_renja=i.id_lokasi_renja
                INNER JOIN ref_ssh_tarif j ON i.id_tarif_ssh=j.id_tarif_ssh
                LEFT OUTER  join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
                LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1
                LEFT OUTER JOIN ref_rek_3 m ON  l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1
                LEFT OUTER JOIN ref_rek_2 n ON   m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1
                LEFT OUTER JOIN ref_rek_1 o ON    n.kd_rek_1=o.kd_rek_1
                WHERE a.tahun_renja='.$tahun.' AND a.id_unit='.$row->id_unit.' ) a GROUP BY a.kd_urusan,a.kd_bidang,a.nm_urusan, a.nm_bidang ');
          
          
          foreach ($apbd_ur AS $row2)
          {
              PDF::SetFont('helvetica', '', 7);
              PDF::MultiCell('15', 10, $row2->kode, 'L', 'R', 0, 0);
              PDF::MultiCell('3', 10, '', 'L', 'L', 0, 0);
              PDF::MultiCell('37', 10, $row2->uraian, 'B', 'L', 0, 0);
              PDF::MultiCell('40', 10, number_format($row2->pend,2,',','.'), 'L', 'R', 0, 0);
              PDF::MultiCell('35', 10, number_format($row2->btl,2,',','.'), 'L', 'R', 0, 0);
              PDF::MultiCell('35', 10, number_format($row2->peg,2,',','.'), 'L', 'R', 0, 0);
              PDF::MultiCell('35', 10, number_format($row2->bj,2,',','.'), 'L', 'R', 0, 0);
              PDF::MultiCell('35', 10, number_format($row2->modal,2,',','.'), 'L', 'R', 0, 0);
              PDF::MultiCell('40', 10, number_format($row2->btl+$row2->peg+$row2->bj+$row2->modal,2,',','.'), 'LR', 'R', 0, 0);
              PDF::Ln();
              
              $countrow++;
              
              if($countrow>=$totalrow)
              {
                  PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                  PDF::AddPage('L');
                  $countrow=0;
                  PDF::SetFont('helvetica', 'B', 8);
                  PDF::MultiCell('15', 7, 'Kode', 'LT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, 'Perangkat Daerah', 'LT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, 'Pendapatan', 'LT', 'C', 0, 0);
                  PDF::MultiCell('140', 7, 'Belanja', 'LT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, 'Total Belanja', 'LRT', 'C', 0, 0);
                  PDF::Ln();
                  $countrow++;
                  PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
                  PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
                  PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
                  PDF::MultiCell('35', 7, 'Belanja Tidak Langsung', 'LT', 'C', 0, 0);
                  PDF::MultiCell('105', 7, 'Belanja Langsung', 'LT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, '', 'LR', 'C', 0, 0);
                  PDF::Ln();
                  $countrow++;
                  PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
                  PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
                  PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
                  PDF::MultiCell('35', 10, '', 'LB', 'C', 0, 0);
                  PDF::MultiCell('35', 10, 'Belanja Pegawai', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('35', 10, 'Belanja Barang & Jasa', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('35', 10, 'Belanja Modal', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('40', 10, '', 'LRB', 'C', 0, 0);
                  PDF::Ln();
                  $countrow++;
              }
              
          }
      }
      PDF::SetFont('helvetica', 'B', 7);
      PDF::MultiCell('55', 10, 'Total : ', 'LBT', 'R', 0, 0);
      PDF::MultiCell('40', 10, number_format($pagu_skpd_pend,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_btl,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_peg,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_bj,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_mod,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('40', 10, number_format($pagu_skpd,2,',','.'), 'LRBT', 'R', 0, 0);

      $template = new TemplateReport();
      $template->footerLandscape();
      PDF::Output('Apbd_Organisasi-'.$pemda.'.pdf', 'I');
  }
 
  public function ApbdOrganisasi($tahun)
  {
      
      
      $countrow=0;
      $totalrow=18;
      $id_renja=20;
      $sub_unit=7;
      $nama_sub="";
      $pagu_skpd_peg=0;
      $pagu_skpd_bj=0;
      $pagu_skpd_mod=0;
      $pagu_skpd_pend=0;
      $pagu_skpd_btl=0;
      $pagu_skpd=0;
      $pemda=Session::get('xPemda');
      
      // set document information
      PDF::SetCreator('BPKP');
      PDF::SetAuthor('BPKP');
      PDF::SetTitle('Simd@Perencanaan');
      PDF::SetSubject('Pra RKA');
      
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
      $header=array('INDIKATOR');
      $header2 = array('SKPD/Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
      //$tahun=DB::SELECT('SELECT tahun_renja FROM trx_renja_rancangan_program
      // GROUP BY tahun_renja');
      // Colors, line width AND bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
      PDF::SetFont('helvetica', '', 10);
      //       foreach($tahun AS $row)
      //       {
      PDF::SetFont('helvetica', 'B', 10);
      PDF::Cell('275', 5, $pemda, 0, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::SetFont('helvetica', 'B', 9);
      PDF::Cell('275', 5, 'RINGKASAN PENDAPATAN DAN BELANJA MENURUT URUSAN PEMERINTAHAN DAN ORGANISASI', 0, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::SetFont('helvetica', 'B', 8);
      PDF::Cell('275', 5, 'Tahun Anggaran : '.$tahun, 0, 0, 'C', 0);
      PDF::Ln();
      $countrow++;
      PDF::Ln();
      $countrow++;
      //}
      
          $apbd_ur=DB::SELECT('SELECT concat(a.kd_urusan,".",a.kd_bidang) AS kode, concat(a.nm_urusan," - ",a.nm_bidang) AS uraian, a.id_bidang,
            sum(a.jml_pend) AS pend, sum(a.jml_btl) AS btl, sum(a.jml_peg) AS peg, sum(a.jml_bj) AS bj, sum(a.jml_mod) AS modal FROM
            (SELECT f.kd_urusan,f.kd_bidang, f.nm_bidang, g.nm_urusan,f.id_bidang,
            case o.kd_rek_1 when 4 then i.jml_belanja else 0 end AS jml_pend,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 1 then i.jml_belanja else 0 END
            else 0 end AS jml_btl,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 2 then
            		case m.kd_rek_3 when 1 then i.jml_belanja else 0 end
            	else 0 END
            else 0 end AS jml_peg,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 2 then
            		case m.kd_rek_3 when 2 then i.jml_belanja else 0 end
            	else 0 END
            else 0 end AS jml_bj,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 2 then
            		case m.kd_rek_3 when 3 then i.jml_belanja else 0 end
            	else 0 END
            else 0 end AS jml_mod              
            FROM trx_renja_rancangan_program a
            INNER JOIN trx_renja_rancangan b ON a.id_renja_program=b.id_renja_program
            INNER JOIN trx_renja_rancangan_pelaksana c ON b.id_renja=c.id_renja
            INNER JOIN ref_bidang f ON a.id_bidang=f.id_bidang
            LEFT OUTER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
            INNER JOIN trx_renja_rancangan_aktivitas h ON c.id_pelaksana_renja=h.id_renja
            INNER JOIN trx_renja_rancangan_belanja i ON h.id_aktivitas_renja=i.id_lokasi_renja
            INNER JOIN ref_ssh_tarif j ON i.id_tarif_ssh=j.id_tarif_ssh
            LEFT OUTER join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
            LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1
            LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1
            LEFT OUTER JOIN ref_rek_2 n ON m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1
            LEFT OUTER JOIN ref_rek_1 o ON n.kd_rek_1=o.kd_rek_1
            WHERE a.tahun_renja='.$tahun.') a GROUP BY a.kd_urusan,a.kd_bidang,a.nm_urusan, a.nm_bidang, a.id_bidang ');
          // header
          PDF::SetFont('helvetica', 'B', 8);
          PDF::MultiCell('15', 7, 'Kode', 'LT', 'C', 0, 0);
          PDF::MultiCell('40', 7, 'Perangkat Daerah', 'LT', 'C', 0, 0);
          PDF::MultiCell('40', 7, 'Pendapatan', 'LT', 'C', 0, 0);
          PDF::MultiCell('140', 7, 'Belanja', 'LT', 'C', 0, 0);
          PDF::MultiCell('40', 7, 'Total Belanja', 'LRT', 'C', 0, 0);
          PDF::Ln();
          $countrow++;
          PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
          PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
          PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
          PDF::MultiCell('35', 7, 'Belanja Tidak Langsung', 'LT', 'C', 0, 0);
          PDF::MultiCell('105', 7, 'Belanja Langsung', 'LT', 'C', 0, 0);
          PDF::MultiCell('40', 7, '', 'LR', 'C', 0, 0);
          PDF::Ln();
          $countrow++;
          PDF::MultiCell('15', 10, '', 'LB', 'C', 0, 0);
          PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
          PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
          PDF::MultiCell('35', 10, '', 'LB', 'C', 0, 0);
          PDF::MultiCell('35', 10, 'Belanja Pegawai', 'LBT', 'C', 0, 0);
          PDF::MultiCell('35', 10, 'Belanja Barang & Jasa', 'LBT', 'C', 0, 0);
          PDF::MultiCell('35', 10, 'Belanja Modal', 'LBT', 'C', 0, 0);
          PDF::MultiCell('40', 10, '', 'LRB', 'C', 0, 0);
          PDF::Ln();
          $countrow++;
          
          foreach ($apbd_ur AS $row)
          {
              PDF::SetFont('helvetica', '', 7);
              PDF::MultiCell('15', 10, $row->kode, 'LT', 'L', 0, 0);
              PDF::MultiCell('40', 10, $row->uraian, 'LT', 'L', 0, 0);
              PDF::MultiCell('40', 10, number_format($row->pend,2,',','.'), 'LT', 'R', 0, 0);
              PDF::MultiCell('35', 10, number_format($row->btl,2,',','.'), 'LT', 'R', 0, 0);
              PDF::MultiCell('35', 10, number_format($row->peg,2,',','.'), 'LT', 'R', 0, 0);
              PDF::MultiCell('35', 10, number_format($row->bj,2,',','.'), 'LT', 'R', 0, 0);
              PDF::MultiCell('35', 10, number_format($row->modal,2,',','.'), 'LT', 'R', 0, 0);
              PDF::MultiCell('40', 10, number_format($row->btl+$row->peg+$row->bj+$row->modal,2,',','.'), 'LRT', 'R', 0, 0);
              PDF::Ln();
              $pagu_skpd_pend=$pagu_skpd_pend+$row->pend;
              $pagu_skpd_btl=$pagu_skpd_btl+$row->btl;
              $pagu_skpd_peg=$pagu_skpd_peg+$row->peg;
              $pagu_skpd_bj=$pagu_skpd_bj+$row->bj;
              $pagu_skpd_mod=$pagu_skpd_mod+$row->modal;
              $pagu_skpd=$pagu_skpd+$row->btl+$row->peg+$row->bj+$row->modal;
              $countrow++;
              
              if($countrow>=$totalrow)
              {
                  PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                  PDF::AddPage('L');
                  $countrow=0;
                  PDF::SetFont('helvetica', 'B', 8);
                  PDF::MultiCell('15', 7, 'Kode', 'LT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, 'Perangkat Daerah', 'LT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, 'Pendapatan', 'LT', 'C', 0, 0);
                  PDF::MultiCell('140', 7, 'Belanja', 'LT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, 'Total Belanja', 'LRT', 'C', 0, 0);
                  PDF::Ln();
                  $countrow++;
                  PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
                  PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
                  PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
                  PDF::MultiCell('35', 7, 'Belanja Tidak Langsung', 'LT', 'C', 0, 0);
                  PDF::MultiCell('105', 7, 'Belanja Langsung', 'LT', 'C', 0, 0);
                  PDF::MultiCell('40', 7, '', 'LR', 'C', 0, 0);
                  PDF::Ln();
                  $countrow++;
                  PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
                  PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
                  PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
                  PDF::MultiCell('35', 10, '', 'LB', 'C', 0, 0);
                  PDF::MultiCell('35', 10, 'Belanja Pegawai', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('35', 10, 'Belanja Barang & Jasa', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('35', 10, 'Belanja Modal', 'LBT', 'C', 0, 0);
                  PDF::MultiCell('40', 10, '', 'LRB', 'C', 0, 0);
                  PDF::Ln();
                  $countrow++;
              }
              $apbd=DB::SELECT('SELECT concat(a.kd_urusan,".",a.kd_bidang,".",a.kd_unit) AS kode,a.id_unit,a.nm_unit, sum(a.jml_pend) AS pend, sum(a.jml_btl) AS btl,
                    sum(a.jml_peg) AS peg, sum(a.jml_bj) AS bj, sum(a.jml_mod) AS modal  FROM
                    (SELECT f.kd_urusan,f.kd_bidang,e.kd_unit,e.id_unit,e.nm_unit,
                    case o.kd_rek_1 when 4 then i.jml_belanja else 0 end AS jml_pend,
                    case o.kd_rek_1 when 5 THEN
                    	case n.kd_rek_2 when 1 then i.jml_belanja else 0 END
                    else 0 end AS jml_btl,
                    case o.kd_rek_1 when 5 THEN
                    	case n.kd_rek_2 when 2 then
                    		case m.kd_rek_3 when 1 then i.jml_belanja else 0 end
                    	else 0 END
                    else 0 end AS jml_peg,
                    case o.kd_rek_1 when 5 THEN
                    	case n.kd_rek_2 when 2 then
                    		case m.kd_rek_3 when 2 then i.jml_belanja else 0 end
                    	else 0 END
                    else 0 end AS jml_bj,
                    case o.kd_rek_1 when 5 THEN
                    	case n.kd_rek_2 when 2 then
                    		case m.kd_rek_3 when 3 then i.jml_belanja	else 0 end
                    	else 0 END
                    else 0 end AS jml_mod                  
                    FROM trx_renja_rancangan_program a
                    INNER JOIN trx_renja_rancangan b ON a.id_renja_program=b.id_renja_program
                    INNER JOIN trx_renja_rancangan_pelaksana c ON b.id_renja=c.id_renja
                    LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
                    LEFT OUTER JOIN ref_unit e ON d.id_unit=e.id_unit
                    LEFT OUTER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                    LEFT OUTER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
                    INNER JOIN trx_renja_rancangan_aktivitas h ON c.id_pelaksana_renja=h.id_renja
                    INNER JOIN trx_renja_rancangan_belanja i ON h.id_aktivitas_renja=i.id_lokasi_renja
                    INNER JOIN ref_ssh_tarif j ON i.id_tarif_ssh=j.id_tarif_ssh
                    LEFT OUTER  join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
                    LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1
                    LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1
                    LEFT OUTER JOIN ref_rek_2 n ON m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1
                    LEFT OUTER JOIN ref_rek_1 o ON n.kd_rek_1=o.kd_rek_1
                    WHERE a.tahun_renja='.$tahun.' AND a.id_bidang='.$row->id_bidang.'  ) a GROUP BY a.kd_urusan,a.kd_bidang,a.kd_unit,a.id_unit,a.nm_unit  ');
              
              
              foreach ($apbd AS $row2)
              {
                  PDF::SetFont('helvetica', '', 7);
                  PDF::MultiCell('15', 10, $row2->kode, 'L', 'R', 0, 0);
                  PDF::MultiCell('3', 10, '', 'L', 'L', 0, 0);
                  PDF::MultiCell('37', 10, $row2->nm_unit, 'B', 'L', 0, 0);
                  PDF::MultiCell('40', 10, number_format($row2->pend,2,',','.'), 'L', 'R', 0, 0);
                  PDF::MultiCell('35', 10, number_format($row2->btl,2,',','.'), 'L', 'R', 0, 0);
                  PDF::MultiCell('35', 10, number_format($row2->peg,2,',','.'), 'L', 'R', 0, 0);
                  PDF::MultiCell('35', 10, number_format($row2->bj,2,',','.'), 'L', 'R', 0, 0);
                  PDF::MultiCell('35', 10, number_format($row2->modal,2,',','.'), 'L', 'R', 0, 0);
                  PDF::MultiCell('40', 10, number_format($row2->btl+$row2->peg+$row2->bj+$row2->modal,2,',','.'), 'LR', 'R', 0, 0);
                  
                  PDF::Ln();
                
                  $countrow++;
                  
                  if($countrow>=$totalrow)
                  {
                      PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                      PDF::AddPage('L');
                      $countrow=0;
                      PDF::SetFont('helvetica', 'B', 8);
                      PDF::MultiCell('15', 7, 'Kode', 'LT', 'C', 0, 0);
                      PDF::MultiCell('40', 7, 'Perangkat Daerah', 'LT', 'C', 0, 0);
                      PDF::MultiCell('40', 7, 'Pendapatan', 'LT', 'C', 0, 0);
                      PDF::MultiCell('140', 7, 'Belanja', 'LT', 'C', 0, 0);
                      PDF::MultiCell('40', 7, 'Total Belanja', 'LRT', 'C', 0, 0);
                      PDF::Ln();
                      $countrow++;
                      PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
                      PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
                      PDF::MultiCell('40', 7, '', 'L', 'C', 0, 0);
                      PDF::MultiCell('35', 7, 'Belanja Tidak Langsung', 'LT', 'C', 0, 0);
                      PDF::MultiCell('105', 7, 'Belanja Langsung', 'LT', 'C', 0, 0);
                      PDF::MultiCell('40', 7, '', 'LR', 'C', 0, 0);
                      PDF::Ln();
                      $countrow++;
                      PDF::MultiCell('15', 7, '', 'L', 'C', 0, 0);
                      PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
                      PDF::MultiCell('40', 10, '', 'LB', 'C', 0, 0);
                      PDF::MultiCell('35', 10, '', 'LB', 'C', 0, 0);
                      PDF::MultiCell('35', 10, 'Belanja Pegawai', 'LBT', 'C', 0, 0);
                      PDF::MultiCell('35', 10, 'Belanja Barang & Jasa', 'LBT', 'C', 0, 0);
                      PDF::MultiCell('35', 10, 'Belanja Modal', 'LBT', 'C', 0, 0);
                      PDF::MultiCell('40', 10, '', 'LRB', 'C', 0, 0);
                      PDF::Ln();
                      $countrow++;
                  }
          }
      }
      PDF::SetFont('helvetica', 'B', 7);
      PDF::MultiCell('55', 10, 'Total : ', 'LBT', 'R', 0, 0);
      PDF::MultiCell('40', 10, number_format($pagu_skpd_pend,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_btl,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_peg,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_bj,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('35', 10, number_format($pagu_skpd_mod,2,',','.'), 'LBT', 'R', 0, 0);
      PDF::MultiCell('40', 10, number_format($pagu_skpd,2,',','.'), 'LRBT', 'R', 0, 0);

      $template = new TemplateReport();
      $template->footerLandscape();
      PDF::Output('Apbd-Urusan-'.$pemda.'.pdf', 'I');
  }
  
  public function PrakiraanMaju($sub_unit, $tahun)
  {
      $countrow = 0;
      $totalrow = 30;
      $id_unit = $sub_unit;
      $pemda = Session::get('xPemda');
      $nm_unit = "";
      $hitung=0;
      if ($sub_unit < 1) {
          $Unit = DB::select('select g.kd_urusan, f.kd_bidang,e.kd_unit,d.id_sub_unit,d.kd_sub, g.nm_urusan, f.nm_bidang, e.nm_unit, d.nm_sub
from trx_renja_rancangan a
inner join trx_renja_rancangan_program b
on a.id_renja_program=b.id_renja_program
inner join trx_renja_rancangan_pelaksana c
on a.id_renja=c.id_renja
inner join ref_sub_unit d
on c.id_sub_unit=d.id_sub_unit
inner join ref_unit e
on d.id_unit=e.id_unit
INNER JOIN 	ref_bidang f
on e.id_bidang=f.id_bidang
inner join ref_urusan g
on f.kd_urusan=g.kd_urusan
GROUP BY g.kd_urusan, f.kd_bidang,e.kd_unit,d.kd_sub, g.nm_urusan, f.nm_bidang, e.nm_unit, d.nm_sub  ');
      } else {
          $Unit = DB::select('select g.kd_urusan, f.kd_bidang,e.kd_unit,d.id_sub_unit,d.kd_sub, g.nm_urusan, f.nm_bidang, e.nm_unit, d.nm_sub
from trx_renja_rancangan a
inner join trx_renja_rancangan_program b
on a.id_renja_program=b.id_renja_program
inner join trx_renja_rancangan_pelaksana c
on a.id_renja=c.id_renja
inner join ref_sub_unit d
on c.id_sub_unit=d.id_sub_unit
inner join ref_unit e
on d.id_unit=e.id_unit
INNER JOIN 	ref_bidang f
on e.id_bidang=f.id_bidang
inner join ref_urusan g
on f.kd_urusan=g.kd_urusan
where c.id_sub_unit=' . $sub_unit . '
GROUP BY g.kd_urusan, f.kd_bidang,e.kd_unit,d.kd_sub, g.nm_urusan, f.nm_bidang, e.nm_unit, d.nm_sub');
      }
      
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
          'Kode',
          'Program/Kegiatan',
          'Lokasi Detail',
          'Indikator Program/Kegiatan',
          'Rencana tahun N',
          'Catatan Penting',
          'Prakiraan Maju Tahun N+1'
      );
      $header2 = array(
          '',
          '',
          '',
          '',
          'Target Capaian Kinerja',
          'Kebutuhan Dana',
          'Sumber Dana',
          '',
          'Target Capaian Kinerja',
          'Kebutuhan Dana'
      );
      // Colors, line width and bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      
      PDF::SetFont('helvetica', 'B', 10);
      
      // Header
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
          'color' => array(
              0,
              0,
              0
          )
      ));
      // Header Column
      
      $wh = array(
          20,
          30,
          45,
          45,
          70,
          20,
          45
      );
      $wh2 = array(
          20,
          30,
          45,
          45,
          20,
          25,
          25,
          20,
          20,
          25
      );
      $w = array(
          275
      ); // unit
      $w1 = array(
          20,
          120,
          20,
          25,
          25,
          20,
          20,
          25
      ); // prog
      $w2 = array(
          20,
          3,
          27,
          45,
          45,
          20,
          25,
          25,
          20,
          20,
          25
      ); // keg
      $w3 = array(
          20,
          30,
          45,
          45,
          20,
          25,
          25,
          20,
          20,
          25
      ); // indprog
      $w4 = array(
          20,
          30,
          45,
          3,
          42,
          20,
          25,
          25,
          20,
          20,
          25
      ); // indkeg
      
      // Color and font restoration
      
      PDF::SetFillColor(224, 235, 255);
      PDF::SetTextColor(0);
      PDF::SetFont('helvetica', '', 6);
      // Data
      $fill = 0;
      foreach ($Unit as $row) {
          
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
              // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
              PDF::SetFont('helvetica', 'B', 7);
              if ($i == 4 || $i == 6) {
                  PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
              } else {
                  PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
              }
          }
          PDF::Ln();
          $countrow ++;
          $countrow ++;
          $num_headers2 = count($header2);
          for ($i = 0; $i < $num_headers2; ++ $i) {
              // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
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
                  } else {
                      PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                  }
              }
              PDF::Ln();
              $countrow ++;
              $countrow ++;
              $num_headers2 = count($header2);
              for ($i = 0; $i < $num_headers2; ++ $i) {
                  // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                  PDF::SetFont('helvetica', 'B', 7);
                  PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
              }
              PDF::Ln();
              $countrow ++;
              $countrow ++;
          }
          // $fill=!$fill;
          $tahunn1 = $tahun + 1;
          $program = DB::select('
SELECT g.kd_urusan as ur_unit, g.kd_bidang as bid_unit, c.kd_unit, c.nm_unit,e.uraian_program as uraian_program_renstra,d.id_renja_program, f.kd_urusan as ur_pro, f.kd_bidang as bid_pro, e.kd_program,
sum(i.pagu_aktivitas) as pagu_program, k.pagu_tahun_program,
case a.status_data when 1 then "Telah direview" else "Belum direview" end as status_program
from trx_renja_rancangan_program a
inner JOIN trx_renja_rancangan d on a.id_renja_program=d.id_renja_program
INNER join ref_program e on a.id_program_ref=e.id_program
inner JOIN ref_bidang f on e.id_bidang = f.id_bidang
inner join trx_renja_rancangan_pelaksana h on d.id_renja=h.id_renja
inner join trx_renja_rancangan_aktivitas i on h.id_pelaksana_renja=i.id_renja
inner join ref_sub_unit j on h.id_sub_unit=j.id_sub_unit
inner join ref_unit c on j.id_unit=c.id_unit
INNER JOIN ref_bidang g on c.id_bidang = g.id_bidang
LEFT OUTER JOIN  (select id_program_renstra, pagu_tahun_program from trx_rkpd_renstra where tahun_rkpd=' . $tahunn1 . ' group by id_program_renstra, pagu_tahun_program) k on a.id_program_renstra=k.id_program_renstra
where  h.id_sub_unit=' . $row->id_sub_unit . ' and a.jenis_belanja=0 and a.tahun_renja=' . $tahun . '
group by g.kd_urusan , g.kd_bidang , c.kd_unit, c.nm_unit,e.uraian_program ,
d.id_renja_program, f.kd_urusan ,k.pagu_tahun_program, f.kd_bidang , e.kd_program
              
');
          foreach ($program as $row2) {
              $height1 = ceil((strlen($row2->ur_pro . '.' . $row2->bid_pro . '  ' . $row2->ur_unit . '.' . $row2->bid_unit . '.' . $row2->kd_unit . '.' . $row2->kd_program) / 20) * 6);
              $height2 = ceil((strlen($row2->uraian_program_renstra) / 120) * 6);
              // $height3=ceil((strlen($row2->pagu_renstra)/25)*6);
              $height4 = ceil((strlen($row2->pagu_program) / 25) * 6);
              $height5 = ceil((strlen($row2->pagu_tahun_program) / 25) * 6);
              
              $maxhigh = array(
                  $height1,
                  $height2,
                  $height4,
                  $height5
              );
              $height = max($maxhigh);
              PDF::SetFont('helvetica', 'B', 6);
              // $height=ceil((strlen($row2->uraian_program_renstra)/47))*5;
              $kode = "";
              if (strlen($row2->kd_program) == 2) {
                  $kode = $row2->ur_pro . '.' . $row2->bid_pro . '  ' . $row2->ur_unit . '.' . $row2->bid_unit . '.' . $row2->kd_unit . ' ' . $row2->kd_program;
              } else {
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
                      } else {
                          PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                      }
                  }
                  PDF::Ln();
                  $countrow ++;
                  $countrow ++;
                  $num_headers2 = count($header2);
                  for ($i = 0; $i < $num_headers2; ++ $i) {
                      // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                      PDF::SetFont('helvetica', 'B', 7);
                      PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
                  }
                  PDF::Ln();
                  $countrow ++;
                  $countrow ++;
              }
              $indikatorprog = DB::select('select  distinct d.uraian_program_renstra,b.uraian_indikator_program_renja,
b.tolok_ukur_indikator,b.target_renstra,b.target_renja,
case b.status_data when 1 then "Telah direview" else "Belum direview" end as status_indikator,f.singkatan_satuan
from  trx_renja_rancangan_program d
inner JOIN trx_renja_rancangan_program_indikator b
on d.id_renja_program=b.id_renja_program
inner JOIN trx_renja_rancangan g on d.id_renja_program=g.id_renja_program
inner join trx_renja_rancangan_pelaksana h on g.id_renja=h.id_renja
left outer join ref_indikator e
on b.kd_indikator=e.id_indikator
left outer join ref_satuan f
on e.id_satuan_output=f.id_satuan
                  
where h.id_sub_unit=' . $row->id_sub_unit . ' and d.id_renja_program=' . $row2->id_renja_program);
              $a=0;
              foreach ($indikatorprog as $row5) {
                  
                  PDF::SetFont('helvetica', 'B', 6);
                  // $height=ceil((strlen($row5->uraian_indikator_program_renja)/47))*5;
                  $height1 = ceil((strlen($row5->uraian_indikator_program_renja) / 45) * 6);
                  $height2 = ceil((strlen($row5->uraian_program_renstra) / 75) * 6);
                  $height3 = ceil((strlen( $row5->target_renja . ' ' . $row5->singkatan_satuan) / 20) * 6);
                  
                  
                  $maxhigh = array(
                      $height1,
                      $height2,
                      $height3,
                      
                  );
                  $height = max($maxhigh);
                  
                  
                  
                  if ($countrow >= $totalrow) {
                      PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                      PDF::AddPage('L');
                      $countrow = 0;
                      for ($i = 0; $i < $num_headers; ++ $i) {
                          PDF::SetFont('helvetica', 'B', 7);
                          if ($i == 4 || $i == 6) {
                              PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
                          } else {
                              PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                          }
                      }
                      PDF::Ln();
                      $countrow ++;
                      $countrow ++;
                      $num_headers2 = count($header2);
                      for ($i = 0; $i < $num_headers2; ++ $i) {
                          // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                          PDF::SetFont('helvetica', 'B', 7);
                          PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
                      }
                      PDF::Ln();
                      $countrow ++;
                      $countrow ++;
                      
                  }
                  if($a==0){
                      PDF::MultiCell(20, $height, $kode, 'LT', 'L', 0, 0);
                      PDF::MultiCell(75, $height, $row2->uraian_program_renstra, 'LT', 'L', 0, 0);
                      PDF::MultiCell(45, $height, $row5->uraian_indikator_program_renja, 'LT', 'L', 0, 0);
                      PDF::MultiCell(20, $height, $row5->target_renja . ' ' . $row5->singkatan_satuan, 'LT', 'L', 0, 0);
                      PDF::MultiCell(25, $height, number_format($row2->pagu_program, 2, ',', '.'), 'LT', 'R', 0, 0);
                      PDF::MultiCell(25, $height, '', 'LT', 'L', 0, 0);
                      PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                      PDF::MultiCell(20, $height,  '', 'LT', 'L', 0, 0);
                      PDF::MultiCell(25, $height, number_format($row2->pagu_tahun_program, 2, ',', '.'), 'LRT', 'R', 0, 0);
                      
                  }
                  else{
                      PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                      PDF::MultiCell(75, $height, '', 'LT', 'L', 0, 0);
                      PDF::MultiCell(45, $height, $row5->uraian_indikator_program_renja, 'LT', 'L', 0, 0);
                      PDF::MultiCell(20, $height, $row5->target_renja . ' ' . $row5->singkatan_satuan, 'LT', 'L', 0, 0);
                      PDF::MultiCell(25, $height, '', 'LT', 'R', 0, 0);
                      PDF::MultiCell(25, $height, '', 'LT', 'L', 0, 0);
                      PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                      PDF::MultiCell(20, $height,  '', 'LT', 'L', 0, 0);
                      PDF::MultiCell(25, $height, '', 'LRT', 'R', 0, 0);
                  }
                  $a++;
                  PDF::Ln();
                  $countrow = $countrow + $height / 5;
                  // $fill=!$fill;
              }
              $kegiatan = DB::select('select  a.id_renja_program,n.sumber_dana,a.id_renja,h.id_pelaksana_renja,coalesce(m.lokasi,"Belum Ada") as lokasi,a.uraian_program_renstra,
 k.nm_kegiatan as uraian_kegiatan_renstra,
 kd_kegiatan,
sum(i.pagu_aktivitas) as pagu_kegiatan,
k.pagu_tahun_kegiatan,
case a.status_data when 1 then "Telah direview" else "Belum direview" end as status_kegiatan
 from trx_renja_rancangan a
inner JOIN trx_renja_rancangan_program d on a.id_renja_program=d.id_renja_program
INNER join ref_program e on d.id_program_ref=e.id_program
inner JOIN ref_bidang f on e.id_bidang = f.id_bidang
inner join trx_renja_rancangan_pelaksana h on a.id_renja=h.id_renja
inner join trx_renja_rancangan_aktivitas i on h.id_pelaksana_renja=i.id_renja
                  
inner join ref_sub_unit j on h.id_sub_unit=j.id_sub_unit
inner join ref_unit c on j.id_unit=c.id_unit
INNER JOIN ref_bidang g on c.id_bidang = g.id_bidang
left outer join ref_kegiatan k on a.id_kegiatan_ref=k.id_kegiatan
left outer join (select distinct group_concat(c.nama_lokasi) as lokasi, a.id_aktivitas_renja from trx_renja_rancangan_aktivitas a
left outer join trx_renja_rancangan_lokasi b on a.id_aktivitas_renja=b.id_pelaksana_renja
inner join ref_lokasi c on b.id_lokasi=c.id_lokasi
                  
group by a.id_aktivitas_renja) m on i.id_aktivitas_renja=m.id_aktivitas_renja
left outer JOIN  (select id_kegiatan_renstra, pagu_tahun_kegiatan from trx_rkpd_renstra where tahun_rkpd=' . $tahunn1 . '  group by id_kegiatan_renstra, pagu_tahun_kegiatan) k on a.id_kegiatan_renstra=k.id_kegiatan_renstra
left outer join (select GROUP_CONCAT(a.uraian_sumber_dana) as sumber_dana,a.id_renja
from
(select b.uraian_sumber_dana, a.id_renja
from trx_renja_rancangan_aktivitas a
inner join ref_sumber_dana b on a.sumber_dana=b.id_sumber_dana
GROUP BY a.id_renja,b.uraian_sumber_dana)a
GROUP BY a.id_renja) n on a.id_renja=n.id_renja
where h.id_sub_unit=' . $row->id_sub_unit . ' and a.id_renja_program=' . $row2->id_renja_program . ' group by a.id_renja_program,k.pagu_tahun_kegiatan,a.id_renja,h.id_pelaksana_renja,a.uraian_program_renstra,m.lokasi,k.nm_kegiatan,d.status_data, kd_kegiatan');
              
              foreach ($kegiatan as $row3) {
                  PDF::SetFont('helvetica', '', 6);
                  $height = ceil((strlen($row3->uraian_kegiatan_renstra) / 37)) * 5;
                  $kode2 = "";
                  if (strlen($row3->kd_kegiatan) == 2) {
                      $kode2 = $kode . '.' . $row3->kd_kegiatan;
                  } else {
                      $kode2 = $kode . '.0' . $row3->kd_kegiatan;
                  }
                  //                     PDF::MultiCell($w2[0], $height, $kode2, 'LT', 'L', 0, 0);
                  //                     PDF::MultiCell($w2[1], $height, '', 'LT', 'L', 0, 0);
                  //                     PDF::MultiCell($w2[2], $height, $row3->uraian_kegiatan_renstra, 'T', 'L', 0, 0);
                  //                     PDF::MultiCell($w2[3], $height, $row3->lokasi, 'LT', 'L', 0, 0);
                  //                     PDF::MultiCell($w2[4], $height, '', 'LT', 'L', 0, 0);
                  //                     PDF::MultiCell($w2[5], $height, '', 'LT', 'R', 0, 0);
                  //                     PDF::MultiCell($w2[6], $height, number_format($row3->pagu_kegiatan, 2, ',', '.'), 'LT', 'R', 0, 0);
                  //                     PDF::MultiCell($w2[7], $height, '', 'LT', 'R', 0, 0);
                  //                     PDF::MultiCell($w2[8], $height, '', 'LRT', 'L', 0, 0);
                  //                     PDF::MultiCell($w2[9], $height, '', 'LRT', 'L', 0, 0);
                  //                     PDF::MultiCell($w2[10], $height, number_format($row3->pagu_tahun_kegiatan, 2, ',', '.'), 'LRT', 'R', 0, 0);
                  
                  //                     PDF::Ln();
                  //                     $countrow = $countrow + $height / 5;
                  if ($countrow >= $totalrow) {
                      PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                      PDF::AddPage('L');
                      $countrow = 0;
                      for ($i = 0; $i < $num_headers; ++ $i) {
                          PDF::SetFont('helvetica', 'B', 7);
                          if ($i == 4 || $i == 6) {
                              PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
                          } else {
                              PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                          }
                      }
                      PDF::Ln();
                      $countrow ++;
                      $countrow ++;
                      $num_headers2 = count($header2);
                      for ($i = 0; $i < $num_headers2; ++ $i) {
                          // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                          PDF::SetFont('helvetica', 'B', 7);
                          PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
                      }
                      PDF::Ln();
                      $countrow ++;
                      $countrow ++;
                  }
                  $indikator = DB::select('select  distinct d.uraian_kegiatan_renstra,b.uraian_indikator_kegiatan_renja,
b.tolok_ukur_indikator,b.angka_renstra,b.angka_tahun,f.singkatan_satuan,
case b.status_data when 1 then "Telah direview" else "Belum direview" end as status_indikator
from  trx_renja_rancangan d
inner JOIN trx_renja_rancangan_indikator b
on d.id_renja=b.id_renja
inner join trx_renja_rancangan_pelaksana h on d.id_renja=h.id_renja
left outer join ref_indikator e
on b.kd_indikator=e.id_indikator
left outer join ref_satuan f
on e.id_satuan_output=f.id_satuan
                      
where h.id_sub_unit=' . $row->id_sub_unit . ' and d.id_renja_program=' . $row2->id_renja_program . ' and d.id_renja=' . $row3->id_renja);
                  $b=0;
                  foreach ($indikator as $row4) {
                      PDF::SetFont('helvetica', '', 6);
                      $height1 = ceil((strlen($row4->uraian_indikator_kegiatan_renja) / 42)) * 5;
                      $height2 = ceil((strlen($row3->uraian_kegiatan_renstra) / 27)) * 5;
                      $height3 = ceil((strlen($row3->lokasi) / 45)) * 5;
                      $height4 = ceil((strlen($row3->sumber_dana) / 20)) * 5;
                      $height=max($height1,$height2,$height3,$height4);
                      if ($countrow >= $totalrow) {
                          PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                          PDF::AddPage('L');
                          $countrow = 0;
                          for ($i = 0; $i < $num_headers; ++ $i) {
                              PDF::SetFont('helvetica', 'B', 7);
                              if ($i == 4 || $i == 6) {
                                  PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0);
                              } else {
                                  PDF::MultiCell($wh[$i], 10, $header[$i], 'LRT', 'C', 0, 0);
                              }
                          }
                          PDF::Ln();
                          $countrow ++;
                          $countrow ++;
                          $num_headers2 = count($header2);
                          for ($i = 0; $i < $num_headers2; ++ $i) {
                              // PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
                              PDF::SetFont('helvetica', 'B', 7);
                              PDF::MultiCell($wh2[$i], 10, $header2[$i], 'LRB', 'C', 0, 0);
                          }
                          PDF::Ln();
                          $countrow ++;
                          $countrow ++;
                      }
                      if($b==0){
                          PDF::MultiCell(20, $height, $kode2, 'LT', 'L', 0, 0);
                          PDF::MultiCell(3, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(27, $height, $row3->uraian_kegiatan_renstra, 'T', 'L', 0, 0);
                          PDF::MultiCell(45, $height, $row3->lokasi, 'LT', 'L', 0, 0);
                          PDF::MultiCell(3, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(42, $height, $row4->uraian_indikator_kegiatan_renja, 'T', 'L', 0, 0);
                          PDF::MultiCell(20, $height, $row4->angka_tahun . ' ' . $row4->singkatan_satuan, 'LT', 'L', 0, 0);
                          PDF::MultiCell(25, $height, number_format($row3->pagu_kegiatan, 2, ',', '.'), 'LT', 'R', 0, 0);
                          PDF::MultiCell(25, $height, $row3->sumber_dana, 'LT', 'L', 0, 0);
                          PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(25, $height, number_format($row3->pagu_tahun_kegiatan, 2, ',', '.'), 'LRT', 'R', 0, 0);
                          
                      }
                      else{
                          PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(3, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(72, $height, '', 'T', 'L', 0, 0);
                          PDF::MultiCell(3, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(42, $height, $row4->uraian_indikator_kegiatan_renja, 'T', 'L', 0, 0);
                          PDF::MultiCell(20, $height, $row4->angka_tahun . ' ' . $row4->singkatan_satuan, 'LT', 'L', 0, 0);
                          PDF::MultiCell(25, $height, '', 'LT', 'R', 0, 0);
                          PDF::MultiCell(25, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(20, $height, '', 'LT', 'L', 0, 0);
                          PDF::MultiCell(25, $height, number_format($row3->pagu_tahun_kegiatan, 2, ',', '.'), 'LRT', 'R', 0, 0);
                      }
                      $b++;
                      PDF::Ln();
                      $countrow = $countrow + $height / 5;
                      // $fill=!$fill;
                  }
                  // $fill=!$fill;
              }
              // $fill=!$fill;
          }
      }
      PDF::Cell(array_sum($w), 0, '', 'T');
      
      // ---------------------------------------------------------
      
      // close and output PDF document
      PDF::Output('PrakiraanMaju-' . $sub_unit . '.pdf', 'I');
  }
  
  public function Apbd($tahun)
  {
      
      Template::settingPageLandscape();
      Template::headerLandscape();
            $id_kegiatan=20;
      $sub_unit=7;
      $nama_sub="";
      $pagu_skpd_peg=0;
      $pagu_skpd_bj=0;
      $pagu_skpd_mod=0;
      $pagu_skpd_pend=0;
      $pagu_skpd_btl=0;
      $pagu_skpd=0;
      $pemda=Session::get('xPemda');
      
      // set document information
      
      
      // set default header data
            PDF::SetFont('helvetica', '', 6);
      
      // add a page
      
      
      // column titles
      
      $html ='';
      $html .=  '<html>';
      $html .= '<head>';
      $html .=  '<style>
                    td, th {
                    }
                </style>';
      $html .= '</head>';
      $html .= '<body>';
      PDF::SetFont('helvetica', 'B', 10);
      $html .='<div style="text-align: center; font-size:12px; font-weight: bold;">REKAPITULASI RENCANA PENDAPATAN DAN BELANJA  PERANGKAT DAERAH</div>';
      $html .='<div style="text-align: center; font-size:12px; font-weight: bold;"> Tahun Anggaran : '.$tahun. '</div>';
      $html .='<br>';
      $html .='<table border="0.5" cellpadding="4" cellspacing="0">
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
      
      
      $apbd=DB::SELECT('SELECT concat(a.kd_urusan,".",a.kd_bidang,".",a.kd_unit) AS kode,a.id_unit,a.nm_unit, sum(a.jml_pend) AS pend, sum(a.jml_btl) AS btl,
            sum(a.jml_peg) AS peg, sum(a.jml_bj) AS bj, sum(a.jml_mod) AS modal  FROM
            (SELECT f.kd_urusan,f.kd_bidang,e.kd_unit,e.id_unit,e.nm_unit,
            case o.kd_rek_1 when 4 then i.jml_belanja else 0 end AS jml_pend,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 1 then i.jml_belanja else 0 END
            else 0 end AS jml_btl,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 2 then
            		case m.kd_rek_3 when 1 then i.jml_belanja else 0 end
            	else 0 END
            else 0 end AS jml_peg,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 2 then
            		case m.kd_rek_3 when 2 then i.jml_belanja else 0 end
            	else 0 END
            else 0 end AS jml_bj,
            case o.kd_rek_1 when 5 THEN
            	case n.kd_rek_2 when 2 then
            		case m.kd_rek_3 when 3 then i.jml_belanja else 0 end
            	else 0 END
            else 0 end AS jml_mod
            FROM trx_renja_rancangan_program a
            INNER JOIN trx_renja_rancangan b ON a.id_renja_program=b.id_renja_program
            INNER JOIN trx_renja_rancangan_pelaksana c ON b.id_renja=c.id_renja
            LEFT OUTER JOIN ref_sub_unit d ON c.id_sub_unit=d.id_sub_unit
            LEFT OUTER JOIN ref_unit e ON d.id_unit=e.id_unit
            LEFT OUTER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
            LEFT OUTER JOIN ref_urusan g ON f.kd_urusan=g.kd_urusan
            INNER JOIN trx_renja_rancangan_aktivitas h ON c.id_pelaksana_renja=h.id_renja
            INNER JOIN trx_renja_rancangan_belanja i ON h.id_aktivitas_renja=i.id_lokasi_renja
            INNER JOIN ref_ssh_tarif j ON i.id_tarif_ssh=j.id_tarif_ssh
            LEFT OUTER  join ref_rek_5 k ON i.id_rekening_ssh=k.id_rekening
            LEFT OUTER JOIN ref_rek_4 l ON k.kd_rek_4=l.kd_rek_4 AND k.kd_rek_3=l.kd_rek_3 AND k.kd_rek_2=l.kd_rek_2 AND k.kd_rek_1=l.kd_rek_1
            LEFT OUTER JOIN ref_rek_3 m ON l.kd_rek_3=m.kd_rek_3 AND l.kd_rek_2=m.kd_rek_2 AND l.kd_rek_1=m.kd_rek_1
            LEFT OUTER JOIN ref_rek_2 n ON m.kd_rek_2=n.kd_rek_2 AND m.kd_rek_1=n.kd_rek_1
            LEFT OUTER JOIN ref_rek_1 o ON n.kd_rek_1=o.kd_rek_1
            WHERE a.tahun_renja='.$tahun.' ) a GROUP BY a.kd_urusan,a.kd_bidang,a.id_unit,a.nm_unit,a.kd_unit  ');
      
      // header
      $html .='<tbody>';
      foreach ($apbd AS $row)
      {
          
          
          PDF::SetFont('helvetica', '', 9);
          $html.='<tr nobr="true">';
          $html.='<td width="7%" style="padding: 50px; text-align: justify;"><div>'.$row->kode.'</div></td>';
          $html.='<td width="15%" style="padding: 50px; text-align: justify;"><div>'.$row->nm_unit.'</div></td>';
          $html.='<td width="13%" style="padding: 50px; text-align: right;"><div>'.number_format($row->pend,2,',','.').'</div></td>';
          $html.='<td width="13%" style="padding: 50px; text-align: right;"><div>'.number_format($row->btl,2,',','.').'</div></td>';
          $html.='<td width="13%" style="padding: 50px; text-align: right;"><div>'.number_format($row->peg,2,',','.').'</div></td>';
          $html.='<td width="13%" style="padding: 50px; text-align: right;"><div>'.number_format($row->bj,2,',','.').'</div></td>';
          $html.='<td width="13%" style="padding: 50px; text-align: right;"><div>'.number_format($row->modal,2,',','.').'</div></td>';
          $html.='<td width="13%" style="padding: 50px; text-align: right;"><div>'.number_format($row->btl+$row->peg+$row->bj+$row->modal,2,',','.').'</div></td>';
          $html.='</tr>';
      }
      $html .='</tbody>';
      $html .='</table>
                  </body>';
      
      //       PDF::SetFont('helvetica', 'B', 7);
      //       PDF::MultiCell('55', 10, 'Total : ', 'LB', 'R', 0, 0);
      //       PDF::MultiCell('40', 10, number_format($pagu_skpd_pend,2,',','.'), 'LB', 'R', 0, 0);
      //       PDF::MultiCell('35', 10, number_format($pagu_skpd_btl,2,',','.'), 'LB', 'R', 0, 0);
      //       PDF::MultiCell('35', 10, number_format($pagu_skpd_peg,2,',','.'), 'LBT', 'R', 0, 0);
      //       PDF::MultiCell('35', 10, number_format($pagu_skpd_bj,2,',','.'), 'LBT', 'R', 0, 0);
      //       PDF::MultiCell('35', 10, number_format($pagu_skpd_mod,2,',','.'), 'LBT', 'R', 0, 0);
      //       PDF::MultiCell('40', 10, number_format($pagu_skpd,2,',','.'), 'LRB', 'R', 0, 0);
      
      //       $template = new TemplateReport();
      //       $template->footerLandscape();
      PDF::writeHTML($html, true, false, true, false, '');
      PDF::Output('Apbd-'.$pemda.'.pdf', 'I');
  }
  
}
