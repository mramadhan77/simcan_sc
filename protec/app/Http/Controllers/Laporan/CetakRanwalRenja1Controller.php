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


class CetakRanwalRenja1Controller extends Controller
{

// MultiCell($w, $h, $txt, $border=0, $align='J/L/R/C', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $vAlign='T/M/B')

 public function KompilasiProgramdanPaguRanwalRenja($id_unit,$tahun)
  {
		
  	$countrow=0;
  	$totalrow=35;
		if($id_unit<1)
		  {$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit FROM ref_unit a 
      INNER JOIN trx_renja_ranwal_program b on a.id_unit=b.id_unit  ');}
		else 
		  {$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit FROM ref_unit a 
      INNER JOIN trx_renja_ranwal_program b on a.id_unit=b.id_unit WHERE a.id_unit='.$id_unit);}
		

    // set document information
    PDF::SetCreator('BPKP');
    PDF::SetAuthor('BPKP');
    PDF::SetTitle('Simd@Perencanaan');
    PDF::SetSubject('SSH Kelompok');

    // set default header data
    PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);

    // set header and footer fonts
    PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
    PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

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
    $template = new TemplateReport();
    PDF::AddPage('L');

    // column titles
    $header = array('SKPD/Program/Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra','Pagu Program','Status Program');

    // Colors, line width and bold font
    PDF::SetFillColor(200, 200, 200);
    PDF::SetTextColor(0);
    PDF::SetDrawColor(255, 255, 255);
    PDF::SetLineWidth(0);
    PDF::SetFont('helvetica', 'B', 10);

    //Header
    PDF::Cell('265', 5, Session::get('xPemda') , 1, 0, 'C', 0);
    PDF::Ln();
    $countrow++;
    PDF::Cell('265', 5, 'KOMPILASI PROGRAM RANCANGAN AWAL RENJA', 1, 0, 'C', 0);
    PDF::Ln(2);
    PDF::Ln();
    $countrow++;
    PDF::SetFont('', 'B');
    PDF::SetFont('helvetica', 'B', 6);
    PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
    
    // Header Column
    
    $wh = array(85,70,10,10,20,25,25,20);
    $w = array(265);
    $w1 = array(10,185,25,25,20);
    $w2 = array(5,10,70,70,10,10,20,70);
    
    $num_headers = count($header);

    for($i = 0; $i < $num_headers; ++$i) {
            PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 7, 'M');
    }
    PDF::Ln();
    $countrow++;
        // Color and font restoration

    PDF::SetFillColor(224, 235, 255);
    PDF::SetTextColor(0);
    PDF::SetFont('helvetica', '', 6);
        // Data
    $fill = 0;
    foreach($Unit as $row) {
    	PDF::MultiCell($w[0], 3, $row->nm_unit, 1, 'L', 0,0);
    	PDF::Ln();
    	$countrow++;
    	if($countrow>=$totalrow)
    	{
        $template->footerLandscape();
    		PDF::AddPage('L');
    		$countrow=0;
    		for($i = 0; $i < $num_headers; ++$i) {
    			PDF::MultiCell($wh[$i], 5, $header[$i], 1, 'C', 1, 0);
    		}
    		PDF::Ln();
    		$countrow++;
    	}
    	//$fill=!$fill;
    	$program = DB::SELECT('SELECT (@id:=@id+1) as no_urut,c.nm_unit,a.uraian_program_renstra,d.id_renja_program,
          sum(d.pagu_tahun_kegiatan) as pagu_program, sum(d.pagu_tahun_renstra) as pagu_renstra,
          case a.status_data when 1 then "Telah direview" else "Belum direview" end as status_program
          FROM trx_renja_ranwal_program a
          INNER JOIN trx_renja_ranwal_kegiatan d on a.id_renja_program=d.id_renja_program
          INNER JOIN ref_unit c  on a.id_unit=c.id_unit, 
          (SELECT @id:=0) x
          WHERE c.id_unit='.$row->id_unit.' AND a.tahun_renja ='.$tahun.' 
          group by c.nm_unit,d.uraian_program_renstra,d.id_renja_program,a.status_data, c.id_unit, a.tahun_renja
          ORDER BY d.id_renja_program,d.uraian_program_renstra');
    	foreach($program as $row2) {

        $height1=ceil((PDF::GetStringWidth($row2->uraian_program_renstra)/$w1[1]))*3;
        $height2=ceil((PDF::GetStringWidth($row2->pagu_renstra)/$w1[2]))*3;
        $height3=ceil((PDF::GetStringWidth($row2->pagu_program)/$w1[3]))*3;
        $height4=ceil((PDF::GetStringWidth($row2->status_program)/$w1[4]))*3;
         
          
        $maxhigh =array($height1,$height2,$height3,$height4);
        $height = max($maxhigh);

    		// PDF::MultiCell($w1[0], $height, '', 'LT', 'L', 0, 0);
        PDF::MultiCell($w1[0], $height, $row2->no_urut, 'LT', 'C', 0, 0);
    		PDF::MultiCell($w1[1], $height, $row2->uraian_program_renstra, 'T', 'L', 0, 0);
    		PDF::MultiCell($w1[2], $height, number_format($row2->pagu_renstra,2,',','.'), 'LT', 'R', 0, 0);
    		PDF::MultiCell($w1[3], $height, number_format($row2->pagu_program,2,',','.'), 'LT', 'R', 0, 0);
    		PDF::MultiCell($w1[4], $height, $row2->status_program, 'LRT', 'C', 0, 0);
    		PDF::Ln();
    		$countrow++;
    		if($countrow>=$totalrow)
    		{
          $template->footerLandscape();
    			PDF::AddPage('L');
    			$countrow=0;
    			for($i = 0; $i < $num_headers; ++$i) {
    				PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 7, 'M');
    			}
    			PDF::Ln();
    			$countrow++;
    		}
    		$indikator = DB::SELECT('SELECT (@id:=@id+1) as no_urut,c.nm_unit,a.uraian_program_renstra,b.uraian_indikator_program_renja,b.tolok_ukur_indikator,
              b.target_renstra,b.target_renja,case b.status_data when 1 then "Telah direview" else "Belum direview" end as status_indikator
              FROM trx_renja_ranwal_program a
              INNER JOIN trx_renja_ranwal_program_indikator b on a.id_renja_program=b.id_renja_program
              INNER JOIN ref_unit c ON a.id_unit=c.id_unit, 
              (SELECT @id:=0) x
              WHERE c.id_unit='. $row->id_unit .' and a.id_renja_program='. $row2->id_renja_program);
    		
			foreach($indikator as $row3) {
          $height1=ceil((PDF::GetStringWidth($row3->uraian_indikator_program_renja)/$w2[2]))*3;
          $height2=ceil((PDF::GetStringWidth($row3->tolok_ukur_indikator)/$w2[3]))*3;
          $height3=ceil((PDF::GetStringWidth($row3->target_renstra)/$w2[4]))*3;
          $height4=ceil((PDF::GetStringWidth($row3->target_renja)/$w2[5]))*3;
          $height5=ceil((PDF::GetStringWidth($row3->status_indikator)/$w2[6]))*3;
         
          
          $maxhigh =array($height1,$height2,$height3,$height4,$height5);
          $height = max($maxhigh);

    			PDF::MultiCell($w2[0], $height, '', 'LTB', 'C', 0, 0);
          PDF::MultiCell($w2[1], $height, $row3->no_urut, 'TB', 'C', 0, 0);
    			PDF::MultiCell($w2[2], $height, $row3->uraian_indikator_program_renja, 'RTB', 'L', 0, 0);
    			PDF::MultiCell($w2[3], $height, $row3->tolok_ukur_indikator, 'LRTB', 'L', 0, 0);
    			PDF::MultiCell($w2[4], $height, number_format($row3->target_renstra,2,',','.'), 'LRTB', 'R', 0, 0);
    			PDF::MultiCell($w2[5], $height, number_format($row3->target_renja,2,',','.'), 'LRTB', 'R', 0, 0);
    			PDF::MultiCell($w2[6], $height, $row3->status_indikator, 'LRTB', 'C', 0, 0);
    			PDF::MultiCell($w2[7], $height, '', 'LRTB', 'L', 0, 0);
    			PDF::Ln();
    			$countrow++;
    			if($countrow>=$totalrow)
    			{
            $template->footerLandscape();
    				PDF::AddPage('L');
    				$countrow=0;
    				for($i = 0; $i < $num_headers; ++$i) {
    					PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 7, 'M');
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
    $template->footerLandscape();

    // ---------------------------------------------------------

    // close and output PDF document
    PDF::Output('KompilasiProgramRanwalRenja_'.$id_unit.'.pdf', 'I');
  }

  public function KompilasiKegiatandanPaguRanwalRenja($id_unit,$tahun)
  {
  	
  	$countrow=0;
  	$totalrow=30;
  	//$id_unit=28;
  	$pemda=Session::get('xPemda');
  	$nm_unit="";
  	if($id_unit<1)
  	{$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit,a.kd_unit, c.kd_bidang, c.nm_bidang,d.kd_urusan,d.nm_urusan  FROM ref_unit a INNER JOIN
        trx_renja_ranwal_program b on a.id_unit=b.id_unit 
        INNER JOIN 	ref_bidang c on a.id_bidang=c.id_bidang 
        INNER JOIN ref_urusan d on c.kd_urusan=d.kd_urusan');}
  	else
  	{$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit,a.kd_unit, c.kd_bidang, c.nm_bidang,d.kd_urusan,d.nm_urusan FROM ref_unit a INNER JOIN
        trx_renja_ranwal_program b on a.id_unit=b.id_unit 
        INNER JOIN 	ref_bidang c on a.id_bidang=c.id_bidang 
        INNER JOIN ref_urusan d on c.kd_urusan=d.kd_urusan WHERE b.id_unit='.$id_unit);}
  	
  	
  	// set document information
  	PDF::SetCreator('BPKP');
  	PDF::SetAuthor('BPKP');
  	PDF::SetTitle('Simd@Perencanaan');
  	PDF::SetSubject('SSH Kelompok');
  	
  	// set default header data
  	PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
  	
  	// set header and footer fonts
  	PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  	PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  	
  	// set default monospaced font
  	PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  	
  	// set margins
  	PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  	PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
  	PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
  	
  	// set auto page breaks
  	PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  	
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
    $template = new TemplateReport();
  	PDF::AddPage('L');
  	
  	// column titles
  	$header = array('Kode','Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/Kegiatan','Pagu Program/Kegiatan','Status Program/Kegiatan');
  	
  	// Colors, line width and bold font
  	PDF::SetFillColor(200, 200, 200);
  	PDF::SetTextColor(0);
  	PDF::SetDrawColor(255, 255, 255);
  	PDF::SetLineWidth(0);
  	
  	PDF::SetFont('helvetica', 'B', 10);
  	
  	//Header
  	PDF::Cell('275', 5, $pemda, 1, 0, 'C', 0);
  	PDF::Ln();
  	$countrow++;
  	PDF::Cell('275', 5, 'KOMPILASI KEGIATAN RANCANGAN AWAL RENCANA KINERJA', 1, 0, 'C', 0);
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
  	
  	// Color and font restoration
  	
  	PDF::SetFillColor(224, 235, 255);
  	PDF::SetTextColor(0);
  	PDF::SetFont('helvetica', '', 6);
  	// Data
  	$fill = 0;
  	foreach($Unit as $row) {
  		
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
  		    PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
  		}
  		PDF::Ln();
  		$countrow++;
  		
  		if($countrow>=$totalrow)
  		{
  		    PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
        $template->footerLandscape();
  			PDF::AddPage('L');
  			$countrow=0;
  			for($i = 0; $i < $num_headers; ++$i) {
  			    PDF::SetFont('helvetica', 'B', 7);
  				  PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
  			}
  			PDF::Ln();
  			$countrow++;
  			$countrow++;
  		}
  		//$fill=!$fill;
  		$program = DB::SELECT('SELECT g.kd_urusan as ur_unit, g.kd_bidang as bid_unit, c.kd_unit, c.nm_unit,e.uraian_program as uraian_program_renstra, 
        d.id_renja_program, f.kd_urusan as ur_pro, f.kd_bidang as bid_pro, e.kd_program,
        sum(d.pagu_tahun_kegiatan) as pagu_program,
        sum(d.pagu_tahun_renstra) as pagu_renstra,
        case a.status_data when 1 then "Telah direview" else "Belum direview" end as status_program
        FROM trx_renja_ranwal_program a
        INNER JOIN trx_renja_ranwal_kegiatan d on a.id_renja_program=d.id_renja_program
        INNER JOIN ref_unit c on a.id_unit=c.id_unit
        INNER JOIN ref_bidang g on c.id_bidang = g.id_bidang
        INNER JOIN ref_program e on a.id_program_ref=e.id_program
        INNER JOIN ref_bidang f on e.id_bidang = f.id_bidang
        WHERE  c.id_unit='.$row->id_unit.' and a.id_program_rpjmd not in 
        (SELECT a.id_program_rpjmd FROM trx_rpjmd_program a
        INNER JOIN trx_rpjmd_sasaran b on a.id_sasaran_rpjmd=b.id_sasaran_rpjmd
        INNER JOIN trx_rpjmd_tujuan c on b.id_tujuan_rpjmd=c.id_tujuan_rpjmd
        INNER JOIN trx_rpjmd_misi d on c.id_misi_rpjmd=d.id_misi_rpjmd
        WHERE d.no_urut in (98,99)) and a.tahun_renja='.$tahun.' 
        group by g.kd_urusan, g.kd_bidang, c.kd_unit, c.nm_unit,d.uraian_program_renstra,d.id_renja_program, f.kd_urusan, f.kd_bidang, e.kd_program,a.status_data
        ');
  		foreach($program as $row2) {
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
          $template->footerLandscape();
  				PDF::AddPage('L');
  				$countrow=0;
  				for($i = 0; $i < $num_headers; ++$i) {
  				    PDF::SetFont('helvetica', 'B', 7);
  					  PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
  				}
  				PDF::Ln();
  				$countrow++;
  				$countrow++;
  			}
  			$indikatorprog = DB::SELECT('SELECT DISTINCT d.uraian_program_renstra,b.uraian_indikator_program_renja,
            b.tolok_ukur_indikator,b.target_renstra,b.target_renja,
            case b.status_data when 1 then "Telah direview" else "Belum direview" end as status_indikator,f.singkatan_satuan
            FROM  trx_renja_ranwal_program d
            INNER JOIN trx_renja_ranwal_program_indikator b on d.id_renja_program=b.id_renja_program
            left outer join ref_indikator e on b.kd_indikator=e.id_indikator
            left outer join ref_satuan f on e.id_satuan_output=f.id_satuan
            WHERE d.id_unit='. $row->id_unit .' and d.id_renja_program='. $row2->id_renja_program);
  			
  			foreach($indikatorprog as $row5) {
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
  			    PDF::MultiCell($w3[4], $height, number_format($row5->target_renstra,2,',','.').' '.$row5->singkatan_satuan, 'LT', 'R', 0, 0);
  			    PDF::MultiCell($w3[5], $height, number_format($row5->target_renja,2,',','.').' '.$row5->singkatan_satuan, 'LT', 'R', 0, 0);
  			    PDF::MultiCell($w3[6], $height, $row5->status_indikator, 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[7], $height, '', 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[8], $height, '', 'LT', 'L', 0, 0);
  			    PDF::MultiCell($w3[9], $height, '', 'LRT', 'L', 0, 0);
  			    
  			    PDF::Ln();
  			    $countrow=$countrow+$height/5;
  			    if($countrow>=$totalrow)
  			    {
  			        PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
                $template->footerLandscape();
  			        PDF::AddPage('L');
  			        $countrow=0;
  			        for($i = 0; $i < $num_headers; ++$i) {
  			            PDF::SetFont('helvetica', 'B', 7);
  			            PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
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
            CASE b.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" END AS status_kegiatan
            FROM trx_renja_ranwal_program a
            INNER JOIN trx_renja_ranwal_kegiatan b ON a.id_renja_program=b.id_renja_program
            LEFT OUTER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
            WHERE b.id_unit='. $row->id_unit .' and a.id_renja_program='. $row2->id_renja_program.' group by 
            a.id_renja_program,b.id_renja,a.uraian_program_renstra,b.uraian_kegiatan_renstra,b.status_data, kd_kegiatan');
              			
  			foreach($kegiatan as $row3) {
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
            $template->footerLandscape();
  					PDF::AddPage('L');
  					$countrow=0;
  					for($i = 0; $i < $num_headers; ++$i) {
  					    PDF::SetFont('helvetica', 'B', 7);
  						PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
  					}
  					PDF::Ln();
  					$countrow++;
  					$countrow++;
  				}
  				$indikator = DB::SELECT('SELECT DISTINCT d.uraian_kegiatan_renstra,b.uraian_indikator_kegiatan_renja,
              b.tolok_ukur_indikator,b.angka_renstra,b.angka_tahun,
              CASE b.status_data WHEN 1 then "Telah direview" ELSE "Belum direview" END AS status_indikator
              FROM  trx_renja_ranwal_kegiatan d
              INNER JOIN trx_renja_ranwal_kegiatan_indikator b ON d.id_renja=b.id_renja
              WHERE d.id_unit='. $row->id_unit .' AND d.id_renja_program='. $row2->id_renja_program.' AND d.id_renja='.$row3->id_renja);
  				
  				foreach($indikator as $row4) {
  					PDF::SetFont('helvetica', '', 6);
            $height=ceil((PDF::GetStringWidth($row4->uraian_indikator_kegiatan_renja)/$w4[3]))*3;
  					PDF::MultiCell($w4[0], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[1], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[2], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[3], $height, $row4->uraian_indikator_kegiatan_renja, 'T', 'L', 0, 0);
  					PDF::MultiCell($w4[4], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[5], $height, $row4->tolok_ukur_indikator, 'T', 'L', 0, 0);
  					PDF::MultiCell($w4[6], $height, number_format($row4->angka_renstra,2,',','.'), 'LT', 'R', 0, 0);
  					PDF::MultiCell($w4[7], $height, number_format($row4->angka_tahun,2,',','.'), 'LT', 'R', 0, 0);
  					PDF::MultiCell($w4[8], $height, $row4->status_indikator, 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[9], $height, '', 'LRT', 'L', 0, 0);
  					PDF::MultiCell($w4[10], $height, '', 'LT', 'L', 0, 0);
  					PDF::MultiCell($w4[11], $height, '', 'LRT', 'L', 0, 0);
  					
  					PDF::Ln();
  					$countrow=$countrow+$height/5;
  					if($countrow>=$totalrow)
  					{
  					    PDF::MultiCell('275', 7, '', 'T', 'R', 0, 0);
              $template->footerLandscape();
  						PDF::AddPage('L');
  						$countrow=0;
  						for($i = 0; $i < $num_headers; ++$i) {
  						    PDF::SetFont('helvetica', 'B', 7);
  							PDF::MultiCell($wh[$i], 10, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
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
  	
  	// close and output PDF document
    $template->footerLandscape();
  	PDF::Output('KompilasiKegiatanRanwalRenja-'.$nm_unit.'.pdf', 'I');
  }
  
  
}

