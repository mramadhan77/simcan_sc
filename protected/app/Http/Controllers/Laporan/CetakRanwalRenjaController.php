<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Laporan\TemplateReport AS Template;
use App\Fungsi as Fungsi;
use PhpParser\Node\Stmt\Foreach_;
use Excel;
use PHPExcel_IOFactory;
use CekAkses;
use Validator;
use Response;
use Session;
use PDF;
use Auth;


class CetakRanwalRenjaController extends Controller
{
  public function __construct()
    {
        $this->middleware('auth');
    }
// MultiCell($w, $h, $txt, $border=0, $align='J/L/R/C', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $vAlign='T/M/B')

 public function KompilasiProgramdanPaguRanwalRenja(Request $request) {		
  	$countrow=0;
  	$totalrow=30;
		if($request->id_unit<1)
		  {$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit FROM ref_unit a 
      INNER JOIN trx_renja_ranwal_program b ON a.id_unit=b.id_unit  ');}
		ELSE 
		  {$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit FROM ref_unit a 
      INNER JOIN trx_renja_ranwal_program b ON a.id_unit=b.id_unit WHERE a.id_unit='.$request->id_unit);}
		

    Template::settingPageLandscape();
    Template::headerLandscape();
    PDF::SetFont('helvetica', '', 6);
    $template = new TemplateReport();

    // column titles
    $header = array('SKPD/Program/Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra','Pagu Program','Status Program','Status Pelaksanaan');

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
    $wh = array(75,60,10,10,20,25,25,20,20);
    $w = array(265);
    $w1 = array(10,165,25,25,20,20);
    $w2 = array(5,10,60,60,10,10,20,90);
    
    $num_headers = count($header);

    for($i = 0; $i < $num_headers; ++$i) {
            PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 7, 'M');
    }
    PDF::Ln();
    $countrow++;

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
          CASE a.status_data WHEN 0 THEN "Belum direview" WHEN 1 THEN "Telah direview" ELSE "Posting" end as status_program,
          CASE a.status_pelaksanaan WHEN 0 THEN "Tepat Waktu" WHEN 1 THEN "Dimajukan" 
          WHEN 2 THEN "Ditunda" WHEN 3 THEN "Dibatalkan" WHEN 4 THEN "Baru" end as status_pelaksanaan
          FROM trx_renja_ranwal_program a
          INNER JOIN trx_renja_ranwal_kegiatan d ON a.id_renja_program=d.id_renja_program
          INNER JOIN ref_unit c  ON a.id_unit=c.id_unit, 
          (SELECT @id:=0) x
          WHERE c.id_unit='.$row->id_unit.' AND a.tahun_renja ='.$request->tahun.' 
          group by c.nm_unit,d.uraian_program_renstra,d.id_renja_program,a.status_data, c.id_unit, a.tahun_renja,a.status_pelaksanaan
          ORDER BY d.id_renja_program,d.uraian_program_renstra');
    	foreach($program as $row2) {

        $height1=ceil((PDF::GetStringWidth($row2->uraian_program_renstra)/$w1[1]))*3;
        $height2=ceil((PDF::GetStringWidth($row2->pagu_renstra)/$w1[2]))*3;
        $height3=ceil((PDF::GetStringWidth($row2->pagu_program)/$w1[3]))*3;
        $height4=ceil((PDF::GetStringWidth($row2->status_program)/$w1[4]))*3;
        $height5=ceil((PDF::GetStringWidth($row2->status_pelaksanaan)/$w1[5]))*3;         
          
        $maxhigh =array($height1,$height2,$height3,$height4,$height5);
        $height = max($maxhigh);
        PDF::SetFont('helvetica', '', 6);
        PDF::MultiCell($w1[0], $height, $row2->no_urut, 'LT', 'C', 0, 0);
    		PDF::MultiCell($w1[1], $height, $row2->uraian_program_renstra, 'T', 'L', 0, 0);
    		PDF::MultiCell($w1[2], $height, number_format($row2->pagu_renstra,2,',','.'), 'LT', 'R', 0, 0);
    		PDF::MultiCell($w1[3], $height, number_format($row2->pagu_program,2,',','.'), 'LT', 'R', 0, 0);
    		PDF::MultiCell($w1[4], $height, $row2->status_program, 'LRT', 'C', 0, 0);
        PDF::MultiCell($w1[5], $height, $row2->status_pelaksanaan, 'LRT', 'C', 0, 0);
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
              b.target_renstra,b.target_renja,CASE b.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" end as status_indikator
              FROM trx_renja_ranwal_program a
              INNER JOIN trx_renja_ranwal_program_indikator b ON a.id_renja_program=b.id_renja_program
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
          PDF::SetFont('helvetica', '', 6);

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

    PDF::Output('KompilasiProgramRanwalRenja_'.$request->id_unit.'.pdf', 'I');
  }

  public function KompilasiKegiatandanPaguRanwalRenja(Request $request)
  {
  	
  	$countrow=0;
  	$totalrow=30;
  	$pemda=Session::get('xPemda');
  	$nm_unit="";
  	if($request->id_unit<1)
  	{$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit,a.kd_unit, c.kd_bidang, c.nm_bidang,d.kd_urusan,d.nm_urusan  FROM ref_unit a INNER JOIN
        trx_renja_ranwal_program b ON a.id_unit=b.id_unit 
        INNER JOIN 	ref_bidang c ON a.id_bidang=c.id_bidang 
        INNER JOIN ref_urusan d ON c.kd_urusan=d.kd_urusan');}
  	else
  	{$Unit = DB::SELECT('SELECT distinct a.nm_unit,a.id_unit,a.kd_unit, c.kd_bidang, c.nm_bidang,d.kd_urusan,d.nm_urusan FROM ref_unit a INNER JOIN
        trx_renja_ranwal_program b ON a.id_unit=b.id_unit 
        INNER JOIN 	ref_bidang c ON a.id_bidang=c.id_bidang 
        INNER JOIN ref_urusan d ON c.kd_urusan=d.kd_urusan WHERE b.id_unit='.$request->id_unit);}
  	
  	Template::settingPageLandscape();
    Template::headerLandscape();

  	PDF::SetFont('helvetica', '', 6);

    $template = new TemplateReport();

  	$header = array('Kode','Program/Kegiatan','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra Program/ Kegiatan','Pagu Program/ Kegiatan','Status Program/ Kegiatan','Status Pelaksanaan');
  	
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
  	$countrow++;
  	PDF::SetFont('', 'B');
  	PDF::SetFont('helvetica', 'B', 6);
  	PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
  	// Header Column
  	
  	$wh = array(20,25,45,45,15,15,20,25,25,20,20);
  	$w = array(275);
  	$w1 = array(20,115,15,15,20,25,25,20,20);
  	$w2 = array(20,3,112,15,15,20,25,25,20,20);
  	$w3 = array(20,25,45,45,15,15,20,25,25,20,20);
  	$w4 = array(20,25,3,42,3,42,15,15,20,25,25,20,20);
  	
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
        CASE a.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" end as status_program,
          CASE a.status_pelaksanaan WHEN 0 THEN "Tepat Waktu" WHEN 1 THEN "Dimajukan" 
          WHEN 2 THEN "Ditunda" WHEN 3 THEN "Dibatalkan" WHEN 4 THEN "Baru" end as status_pelaksanaan
        FROM trx_renja_ranwal_program a
        INNER JOIN trx_renja_ranwal_kegiatan d ON a.id_renja_program=d.id_renja_program
        INNER JOIN ref_unit c ON a.id_unit=c.id_unit
        INNER JOIN ref_bidang g ON c.id_bidang = g.id_bidang
        INNER JOIN ref_program e ON a.id_program_ref=e.id_program
        INNER JOIN ref_bidang f ON e.id_bidang = f.id_bidang
        WHERE  c.id_unit='.$row->id_unit.' and a.id_program_rpjmd not in 
        (SELECT a.id_program_rpjmd FROM trx_rpjmd_program a
        INNER JOIN trx_rpjmd_sasaran b ON a.id_sasaran_rpjmd=b.id_sasaran_rpjmd
        INNER JOIN trx_rpjmd_tujuan c ON b.id_tujuan_rpjmd=c.id_tujuan_rpjmd
        INNER JOIN trx_rpjmd_misi d ON c.id_misi_rpjmd=d.id_misi_rpjmd
        WHERE d.no_urut in (98,99)) and a.tahun_renja='.$request->tahun.' 
        group by g.kd_urusan, g.kd_bidang, c.kd_unit, c.nm_unit,d.uraian_program_renstra,d.id_renja_program, f.kd_urusan, f.kd_bidang, e.kd_program,a.status_data,a.status_pelaksanaan
        ');
  		foreach($program as $row2) {
          $height1=ceil((PDF::GetStringWidth($row2->ur_pro.'.'.$row2->bid_pro.'  '.$row2->ur_unit.'.'.$row2->bid_unit.'.'.$row2->kd_unit.'.'.$row2->kd_program)/$w1[0]))*3;
          $height2=ceil((PDF::GetStringWidth($row2->uraian_program_renstra)/$w1[1]))*3;
          $height3=ceil((PDF::GetStringWidth($row2->pagu_renstra)/$w1[5]))*3;
          $height4=ceil((PDF::GetStringWidth($row2->pagu_program)/$w1[6]))*3;
          $height5=ceil((PDF::GetStringWidth($row2->status_program)/$w1[7]))*3;
          $height6=ceil((PDF::GetStringWidth($row2->status_pelaksanaan)/$w1[8]))*3;
  		   
  		    
  		    $maxhigh =array($height1,$height2,$height3,$height4,$height5,$height6);
  		    $height = max($maxhigh);
  			PDF::SetFont('helvetica', 'B', 6);
  			//$height=ceil((strlen($row2->uraian_program_renstra)/47))*5;
  			$kode="";
  			if(strlen($row2->kd_program)==2)
  			{
  			    $kode=$row2->ur_pro.'.'.$row2->bid_pro.'  '.$row2->ur_unit.'.'.$row2->bid_unit.'.'.$row2->kd_unit.' '.$row2->kd_program;
  			}
  			ELSE 
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
        PDF::MultiCell($w1[8], $height, $row2->status_pelaksanaan, 'LRT', 'L', 0, 0);
  			
  			PDF::Ln();
  			$countrow=$countrow+$height/3;
  			
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
            CASE b.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" end as status_indikator,f.singkatan_satuan
            FROM  trx_renja_ranwal_program d
            INNER JOIN trx_renja_ranwal_program_indikator b ON d.id_renja_program=b.id_renja_program
            left outer join ref_indikator e ON b.kd_indikator=e.id_indikator
            left outer join ref_satuan f ON e.id_satuan_output=f.id_satuan
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
  			    PDF::MultiCell($w3[9], $height, '', 'LBRT', 'L', 0, 0);
            PDF::MultiCell($w3[10], $height, '', 'LBRT', 'L', 0, 0);
  			    
  			    PDF::Ln();
  			    $countrow=$countrow+$height/3;
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
            CASE b.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" END AS status_kegiatan,
            CASE b.status_pelaksanaan_kegiatan WHEN 0 THEN "Tepat Waktu" WHEN 1 THEN "Dimajukan" 
            WHEN 2 THEN "Ditunda" WHEN 3 THEN "Dibatalkan" WHEN 4 THEN "Baru" end as status_pelaksanaan,sum(d.cek) as cek
            FROM trx_renja_ranwal_program a
            INNER JOIN trx_renja_ranwal_kegiatan b ON a.id_renja_program=b.id_renja_program
            LEFT OUTER JOIN (SELECT b.id_renja, COALESCE(COUNT(a.id_indikator_kegiatan_renja),0) as cek 
            FROM trx_renja_ranwal_kegiatan_indikator a
            INNER JOIN trx_renja_ranwal_kegiatan b ON a.id_renja = b.id_renja 
            WHERE (b.status_pelaksanaan_kegiatan <> 2 AND b.status_pelaksanaan_kegiatan <> 3) AND a.status_data=0 
            group by b.id_renja) d ON b.id_renja = d.id_renja
            LEFT OUTER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
            WHERE b.id_unit='. $row->id_unit .' and a.id_renja_program='. $row2->id_renja_program.' group by 
            a.id_renja_program,b.id_renja,a.uraian_program_renstra,b.uraian_kegiatan_renstra,b.status_data, kd_kegiatan, b.status_pelaksanaan_kegiatan');
              			
  			foreach($kegiatan as $row3) {
  				PDF::SetFont('helvetica', '', 6);

            PDF::SetTextColor(0,0,0);
                    if ($row3->cek >0) {
                        PDF::SetTextColor(255,0,0);                  
                    } ELSE {
                        PDF::SetTextColor(0,0,0);
                    }

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
          PDF::MultiCell($w2[9], $height, $row3->status_pelaksanaan, 'LRT', 'L', 0, 0);
  				
  				
  				PDF::Ln();
  				$countrow=$countrow+$height/3;
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
              CASE b.status_data WHEN 1 THEN "Telah direview" ELSE "Belum direview" END AS status_indikator,
              CASE WHEN b.status_data = 0 AND (d.status_pelaksanaan_kegiatan <> 2 AND d.status_pelaksanaan_kegiatan <> 3)  THEN 0 ELSE 1 END AS cek
              FROM  trx_renja_ranwal_kegiatan d
              INNER JOIN trx_renja_ranwal_kegiatan_indikator b ON d.id_renja=b.id_renja
              WHERE d.id_unit='. $row->id_unit .' AND d.id_renja_program='. $row2->id_renja_program.' AND d.id_renja='.$row3->id_renja);
  				
  				foreach($indikator as $row4) {
  					PDF::SetFont('helvetica', '', 6);

            $height=ceil((PDF::GetStringWidth($row4->uraian_indikator_kegiatan_renja)/$w4[3]))*3;
  					PDF::MultiCell($w4[0], $height, '', 'LBT', 'L', 0, 0);
  					PDF::MultiCell($w4[1], $height, '', 'LBT', 'L', 0, 0);
  					PDF::MultiCell($w4[2], $height, '', 'LBT', 'L', 0, 0);
  					PDF::MultiCell($w4[3], $height, $row4->uraian_indikator_kegiatan_renja, 'BT', 'L', 0, 0);
  					PDF::MultiCell($w4[4], $height, '', 'LBT', 'L', 0, 0);
  					PDF::MultiCell($w4[5], $height, $row4->tolok_ukur_indikator, 'BT', 'L', 0, 0);
  					PDF::MultiCell($w4[6], $height, number_format($row4->angka_renstra,2,',','.'), 'LBT', 'R', 0, 0);
  					PDF::MultiCell($w4[7], $height, number_format($row4->angka_tahun,2,',','.'), 'LBT', 'R', 0, 0);
  					PDF::MultiCell($w4[8], $height, $row4->status_indikator, 'LBT', 'L', 0, 0);
  					PDF::MultiCell($w4[9], $height, '', 'LBRT', 'L', 0, 0);
  					PDF::MultiCell($w4[10], $height, '', 'LBT', 'L', 0, 0);
  					PDF::MultiCell($w4[11], $height, '', 'LBRT', 'L', 0, 0);
            PDF::MultiCell($w4[11], $height, '', 'LBRT', 'L', 0, 0);
  					
  					PDF::Ln();
  					$countrow=$countrow+$height/3;
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

    $template->footerLandscape();
  	PDF::Output('KompilasiKegiatanRanwalRenja-'.$nm_unit.'.pdf', 'I');
  }

public function CekProgressRanwalRenja(Request $request)
  {  
  Excel::create('CekProgressRanwalRenja', function($excel) {
    // Set the title
    $excel->setTitle('Simda Perencanaan');
    $excel->setCreator('SIMDA')->setCompany('BPKP');
    $excel->setDescription('Cek Progress Ranwal Renja');
    
    $excel->sheet('CekProgressRanwalRenja', function($sheet) {
      
    $sheet->setColumnFormat(array(
          'O' => '#,##0.00',
          'P' => '#,##0.00',
          'V' => '#,##0.00',
          'X' => '#,##0.00',
    ));


      $sheet->prependRow(4, array(
          Session::get('xPemda')
      ));
      $sheet->prependRow(5, array(
          'Cek Progress Rancangan Awal Renja tahun '.$request->tahun
      ));
      $sheet->prependRow(7, array( 'Tahun', 'Kd Unit', 'Nama Unit', 'Kd Sub Unit', 'Nama Sub Unit', 'Kd Urusan', 'Kd Bidang', 'Uraian Bidang', 'Kd Program', 'Uraian Program',
          'Status Program', 'Status Posting Prog', 'Kd Kegiatan', 'Uraian Kegiatan Renstra', 'Pagu Renstra', 'Pagu Ranwal', 'Status Kegiatan',
          'Status Posting Keg', 'Uraian Aktivitas', 'Sumber Dana', 'Jenis Aktivitas', 'Pagu Aktivitas', 'Status Musren', 'Pagu Musren', ));

      $sheet->row(7, function($row) {
        $row->setBackground('#AAAAFF');
      });
      $sheet->getStyle('A7:X7')
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('C8:C' . $sheet->getHighestRow())
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('E8:E' . $sheet->getHighestRow())
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('H8:H' . $sheet->getHighestRow())
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('J8:J' . $sheet->getHighestRow())
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('N8:N' . $sheet->getHighestRow())
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('S8:S' . $sheet->getHighestRow())
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('T8:T' . $sheet->getHighestRow())
        ->getAlignment()->setWrapText(true);        
        $sheet->setAutoSize(false);
        // $sheet->setAutoSize(array(
        //     'C','E','H','J','N','S','T'
        // ));
        
        $sheet->cells('A4:X5', function($cells) {
          $cells->setFont(array(
              'family'     => 'Calibri',
              'size'       => '16',
              'bold'       =>  true
          ));
        });

          $sheet->mergeCells('A4:X4');
          $sheet->mergeCells('A5:X5');
            
          $sheet->cells('A4:X5', function($cells) {
            $cells->setAlignment('center');
          });

          $sheet->cells('A7:X7', function($cells) {
            $cells->setAlignment('center');
            $cells->setVAlignment('center');
          });

          $sheet->cells('A8:X10000', function($cells) {
            $cells->setValignment('top');
          });

          $sheet->setBorder('A7:X7', 'thin');
          $sheet->setBorder('A8:X100', 'thin', "D8572C");

          $sheet->setWidth('A', 10);
          $sheet->setWidth('B', 10);
          $sheet->setWidth('C', 50);
          $sheet->setWidth('D', 10);
          $sheet->setWidth('E', 50);
          $sheet->setWidth('F', 10);
          $sheet->setWidth('G', 10);
          $sheet->setWidth('H', 50);
          $sheet->setWidth('I', 10);
          $sheet->setWidth('J', 50);
          $sheet->setWidth('K', 20);
          $sheet->setWidth('L', 20);
          $sheet->setWidth('M', 10);
          $sheet->setWidth('N', 50);
          $sheet->setWidth('O', 20);
          $sheet->setWidth('P', 20);
          $sheet->setWidth('Q', 20);
          $sheet->setWidth('R', 20);
          $sheet->setWidth('S', 50);
          $sheet->setWidth('T', 50);
          $sheet->setWidth('U', 20);
          $sheet->setWidth('V', 20);
          $sheet->setWidth('W', 20);
          $sheet->setWidth('X', 20);
          // $sheet->protect('password', function(\PHPExcel_Worksheet_Protection $protection) {
            // $protection->setSort(true);
            // $protection->setSheet(true);
          // });
      
    $dataQuery= DB::select('SELECT B.tahun_renja,I.kd_unit,I.nm_unit,H.kd_sub,H.nm_sub,F.kd_urusan,F.kd_bidang,F.nm_bidang,E.kd_program,D.uraian_program_renstra,
        CASE D.status_pelaksanaan WHEN 0 THEN "Tepat Waktu" WHEN 1 THEN "Maju" WHEN 2 THEN "Tunda" WHEN 3 THEN "Batal" ELSE "Baru" END AS Status_Prog,
        CASE D.status_data WHEN 2 THEN "Posting Dokumen" WHEN 1 THEN "Posting Program" ELSE "Belum Posting" end as Status_Posting_Prog,
        C.kd_kegiatan, B.uraian_kegiatan_renstra,
        B.pagu_tahun_renstra AS Pagu_Renstra,
        B.pagu_tahun_kegiatan AS Pagu_Ranwal, 
        CASE B.status_pelaksanaan_kegiatan WHEN 0 THEN "Tepat Waktu" WHEN 1 THEN "Maju" WHEN 2 THEN "Tunda" WHEN 3 THEN "Batal" ELSE "Baru" END AS Status_Keg, 
        CASE B.status_data WHEN 1 THEN "Posting Keg" ELSE "Belum Review" end as Status_Posting_Keg, 
        A.uraian_aktivitas_kegiatan, J.uraian_sumber_dana,
        CASE A.sumber_aktivitas WHEN 0 THEN "ASB" WHEN 1 THEN "Non ASB" ELSE NULL END AS Aktivitas, A.pagu_aktivitas,
        CASE A.status_musren WHEN 1 THEN "Musren" WHEN 0 THEN "Non Musren" ELSE NULL END AS Status_Musren,
        A.pagu_aktivitas*(A.pagu_musren/100) AS Pagu_Musren
        FROM trx_renja_ranwal_kegiatan AS B LEFT OUTER JOIN
            trx_renja_ranwal_pelaksana AS P ON B.id_renja = P.id_renja
            LEFT OUTER JOIN
              trx_renja_ranwal_aktivitas AS A ON A.tahun_renja = B.tahun_renja AND A.id_renja = P.id_pelaksana_renja INNER JOIN
              ref_kegiatan AS C ON C.id_kegiatan = B.id_kegiatan_ref INNER JOIN
              trx_renja_ranwal_program AS D ON D.tahun_renja = B.tahun_renja AND D.id_renja_program = B.id_renja_program INNER JOIN
              ref_program AS E ON E.id_program = D.id_program_ref INNER JOIN
              ref_bidang AS F ON F.id_bidang = E.id_bidang INNER JOIN
              trx_renja_ranwal_pelaksana AS G ON G.tahun_renja = B.tahun_renja AND G.id_renja = B.id_renja INNER JOIN
              ref_sub_unit AS H ON H.id_sub_unit = G.id_sub_unit INNER JOIN
              ref_unit AS I ON I.id_unit = B.id_unit LEFT OUTER JOIN
              ref_sumber_dana AS J ON J.id_sumber_dana = A.sumber_dana
        WHERE B.tahun_renja = '.$request->tahun.' AND B.status_pelaksanaan_kegiatan IN (0,1)');


      foreach($dataQuery as $row) {        
        $data[] = array(
            $row->tahun_renja,
            $row->kd_unit,
            $row->nm_unit,
            $row->kd_sub,
            $row->nm_sub,
            $row->kd_urusan,
            $row->kd_bidang,
            $row->nm_bidang,
            $row->kd_program,
            $row->uraian_program_renstra,
            $row->Status_Prog,
            $row->Status_Posting_Prog,
            $row->kd_kegiatan,
            $row->uraian_kegiatan_renstra,
            number_format($row->Pagu_Renstra,2,',','.'),
            number_format($row->Pagu_Ranwal,2,',','.'),
            $row->Status_Keg,
            $row->Status_Posting_Keg,
            $row->uraian_aktivitas_kegiatan,
            $row->uraian_sumber_dana,
            $row->Aktivitas,
            number_format($row->pagu_aktivitas,2,',','.'),
            $row->Status_Musren,
            number_format($row->Pagu_Musren,2,',','.'),            
        );        
      }
      $sheet->fromArray($data, null, 'A8', false, false);
    });
  })->download('xlsx');
  return back();

  }
  
  public function SasaranProgramRenjaRanwal(Request $request)
  {
      Template::settingPageLandscape();
      Template::headerLandscape();

      PDF::SetFont('helvetica', '', 6);
      
      $data_tujuan = DB::SELECT('SELECT a.id_tujuan_rpjmd, a.uraian_tujuan_rpjmd
          FROM trx_rpjmd_tujuan AS a
          INNER JOIN trx_rpjmd_sasaran b ON a.id_tujuan_rpjmd=b.id_tujuan_rpjmd
          INNER JOIN trx_rpjmd_program c ON b.id_sasaran_rpjmd=c.id_sasaran_rpjmd
          INNER JOIN trx_renstra_program d ON c.id_program_rpjmd=d.id_program_rpjmd
          INNER JOIN trx_renja_ranwal_program e ON d.id_program_renstra=e.id_program_renstra
          WHERE e.id_renja_program='.$request->id_program.' GROUP BY a.id_tujuan_rpjmd, a.uraian_tujuan_rpjmd');
      
      $data = DB::SELECT('SELECT A.id_sasaran_rpjmd, C.id_program_renstra, N.id_kegiatan_renstra,
          A.uraian_sasaran_rpjmd, C.uraian_program_renstra, N.uraian_kegiatan_renstra, H.nm_unit,
         (  SELECT count(H.id_program_rpjmd)
            FROM trx_renstra_kegiatan F
            INNER JOIN trx_renstra_program G ON F.id_program_renstra=G.id_program_renstra
            INNER JOIN trx_rpjmd_program H ON G.id_program_rpjmd=H.id_program_rpjmd
            INNER JOIN trx_rpjmd_sasaran I ON H.id_sasaran_rpjmd = I.id_sasaran_rpjmd
            WHERE A.id_sasaran_rpjmd = I.id_sasaran_rpjmd ) AS level_1,
          ( SELECT count(L.id_program_rpjmd)
            FROM trx_renstra_kegiatan J
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
          INNER JOIN trx_renja_ranwal_program I ON C.id_program_renstra=I.id_program_renstra
          WHERE I.id_renja_program='.$request->id_program.'
          GROUP BY A.id_sasaran_rpjmd, C.id_program_renstra, N.id_kegiatan_renstra, A.uraian_sasaran_rpjmd,
          C.uraian_program_renstra, N.uraian_kegiatan_renstra, level_1, level_2, H.nm_unit          
          ORDER BY A.id_sasaran_rpjmd asc, C.id_program_renstra asc');
      
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
      foreach ($data_tujuan as $tujuan) {
          $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">Matrik Sasaran Program Rancangan Awal Renja </div>';
          $html .= '<div style="text-align: left; font-size:12px; font-weight: bold;">Tujuan : ' . $tujuan->uraian_tujuan_rpjmd . '</div>';
      };
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
      
      foreach ($data as $row) {
          $html .= '<tr nobr="true">';          
          if ($jum_level_1 <= 1) {
              $html .= '<td rowspan="' . $row->level_1 . '" style="padding: 50px; text-align: justify;"><div><span style="font-weight: bold;">' . $row->uraian_sasaran_rpjmd . '</span></div>';
              $html .= '<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
              $sasaran_ind = DB::SELECT('SELECT (@id :=@id + 1) AS urut,  a.thn_id,  a.no_urut,  a.id_sasaran_rpjmd,  a.id_indikator_sasaran_rpjmd,  a.id_perubahan,
                        a.kd_indikator,  a.uraian_indikator_sasaran_rpjmd,  a.tolok_ukur_indikator,  a.sumber_data,  a.created_at,  a.updated_at, 
                        COALESCE (b.nm_indikator, "Kosong") AS nm_indikator, c.uraian_satuan,
                        CASE(h.tahun_5-'.$request->tahun.') WHEN 4 THEN a.angka_tahun1
                        WHEN 3 THEN a.angka_tahun2
                        WHEN 2 THEN a.angka_tahun3
                        WHEN 1 THEN a.angka_tahun4
                        ELSE a.angka_tahun5 end as angka_tahun
                        FROM trx_rpjmd_sasaran_indikator AS a
                        LEFT OUTER  JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                        LEFT OUTER JOIN ref_satuan c ON b.id_satuan_output=c.id_satuan
                        INNER JOIN trx_rpjmd_sasaran d ON a.id_sasaran_rpjmd=d.id_sasaran_rpjmd
                        INNER JOIN trx_rpjmd_tujuan e ON d.id_tujuan_rpjmd=e.id_tujuan_rpjmd
                        INNER JOIN trx_rpjmd_misi f ON e.id_misi_rpjmd=f.id_misi_rpjmd
                        INNER JOIN trx_rpjmd_visi g ON f.id_visi_rpjmd=g.id_visi_rpjmd
                        INNER JOIN ref_tahun h ON g.id_rpjmd=h.id_rpjmd, (SELECT @id := 0) x  
                        WHERE a.id_sasaran_rpjmd=' . $row->id_sasaran_rpjmd);
              $html .= '<table border="0.1" cellpadding="2" cellspacing="0">';
              foreach ($sasaran_ind as $sasarans) {
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
          };

          if ($jum_level_2 <= 1) {
              $html .= '<td rowspan="' . $row->level_2 . '" style="padding: 50px; text-align: justify;"><div><span style="font-weight: bold;">' . $row->uraian_program_renstra . '</span></div>';
              $html .= '<div><span style="font-weight: bold; font-style: italic">UNIT :'.$row->nm_unit.'</span></div>';
              $html .= '<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
              $program_ind = DB::SELECT('SELECT (@id :=@id + 1) AS urut, a.uraian_indikator_program_renja, a.tolok_ukur_indikator, a.target_renja,
                        a.sumber_data, c.uraian_satuan , COALESCE (b.nm_indikator, "Kosong") AS nm_indikator
                        FROM trx_renja_ranwal_program_indikator AS a
                        inner join trx_renja_ranwal_program AS d ON a.id_renja_program=d.id_renja_program
                        LEFT OUTER  JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                        LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output=c.id_satuan , (SELECT @id := 0) x  
                        WHERE a.id_program_renstra=' . $row->id_program_renstra.' and a.tahun_renja='.$request->tahun);
              $html .= '<table border="0.1" cellpadding="2" cellspacing="0">';
              foreach ($program_ind as $programs) {
                  $html .= '<tr><td width="10%" style="text-align: center;"> ' . $programs->urut . ' </td>';
                  $html .= '<td width="60%" style="text-align: justify;">' . $programs->uraian_indikator_program_renja . '</td>';
                  $html .= '<td width="20%" style="text-align: right;">' . number_format($programs->target_renja, 2, ',', '.') . '</td>';
                  $html .= '<td width="10%" style="text-align: left;">' . $programs->uraian_satuan. '</td></tr>';
              }
              $html .= '</table>';
              $html .= '</td>';              
              $jum_level_2 = $row->level_2;
          } ELSE {
              $jum_level_2 = $jum_level_2 - 1;
          };
          $html .= '<td style="padding: 50px; text-align: justify;"><div><span style="font-weight: bold;">' . $row->uraian_kegiatan_renstra . '</span></div>';          
          $html .= '<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
          $kegiatan_ind = DB::SELECT('SELECT  (@id :=@id + 1) AS urut, a.uraian_indikator_kegiatan_renja, a.tolok_ukur_indikator, a.angka_tahun,
                a.sumber_data, c.uraian_satuan , COALESCE (b.nm_indikator, "Kosong") AS nm_indikator
                FROM trx_renja_ranwal_kegiatan_indikator AS a
                INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja=d.id_renja
                INNER JOIN trx_renja_ranwal_program AS e ON d.id_renja_program=e.id_renja_program
                 LEFT OUTER  JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output=c.id_satuan, (SELECT @id := 0) x  
                WHERE d.id_kegiatan_renstra=' . $row->id_kegiatan_renstra.' and a.tahun_renja='.$request->tahun);
          $html .= '<table border="0.1" cellpadding="2" cellspacing="0">';
          foreach ($kegiatan_ind as $kegiatans) {
              $html .= '<tr><td width="10%" style="text-align: center;"> ' . $kegiatans->urut . ' </td>';
              $html .= '<td width="60%" style="text-align: justify;">' . $kegiatans->uraian_indikator_kegiatan_renja . '</td>';
              $html .= '<td width="20%" style="text-align: right;">' . number_format($kegiatans->angka_tahun, 2, ',', '.') . '</td>';
              $html .= '<td width="10%" style="text-align: left;">' . $kegiatans->uraian_satuan. '</td></tr>';
          }
          $html .= '</table>';
          $html .= '</td>';
          $html .= '</tr>';
      };
      
      $html .= '</tbody></table>';
      
      PDF::writeHTML($html, true, false, true, false, '');
      Template::footerLandscape();

      PDF::Output('MatrikSasaranProgramRenjaRanwal-' . $nm_unit . '.pdf', 'I');
  }
  
  
}

