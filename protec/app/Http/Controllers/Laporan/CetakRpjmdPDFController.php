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
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;


class CetakRpjmdPDFController extends Controller
{

	public function KompilasiProgramdanPaguRpjmdPDF()
  {
		
  	$countrow=0;
  	$totalrow=18;
  	$totalpagu1=0;
  	$totalpagu2=0;
  	$totalpagu3=0;
  	$totalpagu4=0;
  	$totalpagu5=0;
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
    $header = array('Kode','Misi/Program RPJMD/Unit/Program Renstra', 'Tahun 1', 'Tahun 2', 'Tahun 3', 'Tahun 4', 'Tahun 5');

    // Colors, line width and bold font
    PDF::SetFillColor(200, 200, 200);
    PDF::SetTextColor(0);
    PDF::SetDrawColor(255, 255, 255);
    PDF::SetLineWidth(0);
    PDF::SetFont('helvetica', 'B', 10);

    //Header
    PDF::Cell('275', 5, Session::get('xPemda') , 1, 0, 'C', 0);
    PDF::Ln();
    $countrow++;
    PDF::Cell('275', 5, 'KOMPILASI PROGRAM RPJMD', 1, 0, 'C', 0);
    PDF::Ln();
    $countrow++;
    PDF::SetFont('', 'B');
    PDF::SetFont('helvetica', 'B', 6);
    
    // Header Column
    
    $wh = array(13,137,25,25,25,25,25);
    $w = array(13,137,125);
    $w1 = array(13,5,132,25,25,25,25,25);
    $w2 = array(13,10,127,125);
    $w3 = array(13,15,122,25,25,25,25,25);
    
    $num_headers = count($header);
    for($i = 0; $i < $num_headers; ++$i) {
            PDF::MultiCell($wh[$i], 7, $header[$i], 0, 'C', 1, 0);
    }
    PDF::Ln();
    $countrow++;
        // Color and font restoration

    PDF::SetFillColor(224, 235, 255);
    PDF::SetTextColor(0);
    PDF::SetFont('helvetica', '', 6);
        // Data
    $fill = 0;
    
    	//$fill=!$fill;
    	$misi = DB::select('SELECT distinct m.id_misi_rpjmd,concat(v.id_visi_rpjmd,".",
m.id_misi_rpjmd) as kode, m.uraian_misi_rpjmd FROM
trx_rpjmd_visi AS v
INNER JOIN trx_rpjmd_misi AS m ON m.id_visi_rpjmd = v.id_visi_rpjmd
INNER JOIN trx_rpjmd_tujuan AS t ON t.id_misi_rpjmd = m.id_misi_rpjmd
INNER JOIN trx_rpjmd_sasaran AS s ON s.id_tujuan_rpjmd = t.id_tujuan_rpjmd
INNER JOIN trx_rpjmd_program AS rpp ON rpp.id_sasaran_rpjmd = s.id_sasaran_rpjmd
INNER JOIN trx_renstra_program AS rep ON rep.id_program_rpjmd = rpp.id_program_rpjmd
INNER JOIN trx_renstra_sasaran AS resa ON rep.id_sasaran_renstra = resa.id_sasaran_renstra
INNER JOIN trx_renstra_tujuan AS retu ON resa.id_tujuan_renstra = retu.id_tujuan_renstra
INNER JOIN trx_renstra_misi AS remi ON retu.id_misi_renstra = remi.id_misi_renstra
INNER JOIN trx_renstra_visi AS revi ON remi.id_visi_renstra = revi.id_visi_renstra
INNER JOIN ref_unit AS ru ON ru.id_unit = revi.id_unit');
    	
    	foreach($misi as $row) {
    		PDF::SetFont('helvetica', 'B', 7);
    		PDF::MultiCell($w[0], 12, $row->kode, 0, 'L', 0, 0);
    		PDF::MultiCell($w[1], 12, $row->uraian_misi_rpjmd, 0, 'L', 0, 0);
    		PDF::MultiCell($w[2], 12, '', 0, 'L', 0, 0);
    		PDF::Ln();
    		$countrow++;
    		if($countrow>=$totalrow)
    		{
    			PDF::AddPage('L');
    			$countrow=0;
    			for($i = 0; $i < $num_headers; ++$i) {
    				PDF::MultiCell($wh[$i], 7, $header[$i], 0, 'C', 1, 0);
    			}
    			PDF::Ln();
    			$countrow++;
    		}
    		$programRPJMD = DB::select('select distinct rpp.id_program_rpjmd, CONCAT( m.id_misi_rpjmd,".",
t.id_tujuan_rpjmd,".",
s.id_sasaran_rpjmd,".",
rpp.id_program_rpjmd) as kode,
rpp.uraian_program_rpjmd,
rpp.pagu_tahun1,
rpp.pagu_tahun2,
rpp.pagu_tahun3,
rpp.pagu_tahun4,
rpp.pagu_tahun5 FROM
trx_rpjmd_visi AS v
INNER JOIN trx_rpjmd_misi AS m ON m.id_visi_rpjmd = v.id_visi_rpjmd
INNER JOIN trx_rpjmd_tujuan AS t ON t.id_misi_rpjmd = m.id_misi_rpjmd
INNER JOIN trx_rpjmd_sasaran AS s ON s.id_tujuan_rpjmd = t.id_tujuan_rpjmd
INNER JOIN trx_rpjmd_program AS rpp ON rpp.id_sasaran_rpjmd = s.id_sasaran_rpjmd
INNER JOIN trx_renstra_program AS rep ON rep.id_program_rpjmd = rpp.id_program_rpjmd
INNER JOIN trx_renstra_sasaran AS resa ON rep.id_sasaran_renstra = resa.id_sasaran_renstra
INNER JOIN trx_renstra_tujuan AS retu ON resa.id_tujuan_renstra = retu.id_tujuan_renstra
INNER JOIN trx_renstra_misi AS remi ON retu.id_misi_renstra = remi.id_misi_renstra
INNER JOIN trx_renstra_visi AS revi ON remi.id_visi_renstra = revi.id_visi_renstra
INNER JOIN ref_unit AS ru ON ru.id_unit = revi.id_unit
where m.id_misi_rpjmd='. $row->id_misi_rpjmd);
    		
    		foreach($programRPJMD as $row2) {
    			PDF::SetFont('helvetica', 'B', 6);
    			PDF::MultiCell($w1[0], 7, $row2->kode, 0, 'L', 0, 0);
    			PDF::MultiCell($w1[1], 7, '', 0, 'L', 0, 0);
    			PDF::MultiCell($w1[2], 7, $row2->uraian_program_rpjmd, 0, 'L', 0, 0);
    			PDF::MultiCell($w1[3], 7, number_format($row2->pagu_tahun1,2,',','.'), 0, 'R', 0, 0);
    			PDF::MultiCell($w1[4], 7, number_format($row2->pagu_tahun2,2,',','.'), 0, 'R', 0, 0);
    			PDF::MultiCell($w1[5], 7, number_format($row2->pagu_tahun3,2,',','.'), 0, 'R', 0, 0);
    			PDF::MultiCell($w1[6], 7, number_format($row2->pagu_tahun4,2,',','.'), 0, 'R', 0, 0);
    			PDF::MultiCell($w1[7], 7, number_format($row2->pagu_tahun5,2,',','.'), 0, 'R', 0, 0);
    			PDF::Ln();
    			$countrow++;
    			if($countrow>=$totalrow)
    			{
    				PDF::AddPage('L');
    				$countrow=0;
    				for($i = 0; $i < $num_headers; ++$i) {
    					PDF::MultiCell($wh[$i], 7, $header[$i], 0, 'C', 1, 0);
    				}
    				PDF::Ln();
    				$countrow++;
    			}
    			
    			$Unit = DB::select('select distinct ru.nm_unit,ru.id_unit FROM
trx_rpjmd_visi AS v
INNER JOIN trx_rpjmd_misi AS m ON m.id_visi_rpjmd = v.id_visi_rpjmd
INNER JOIN trx_rpjmd_tujuan AS t ON t.id_misi_rpjmd = m.id_misi_rpjmd
INNER JOIN trx_rpjmd_sasaran AS s ON s.id_tujuan_rpjmd = t.id_tujuan_rpjmd
INNER JOIN trx_rpjmd_program AS rpp ON rpp.id_sasaran_rpjmd = s.id_sasaran_rpjmd
INNER JOIN trx_renstra_program AS rep ON rep.id_program_rpjmd = rpp.id_program_rpjmd
INNER JOIN trx_renstra_sasaran AS resa ON rep.id_sasaran_renstra = resa.id_sasaran_renstra
INNER JOIN trx_renstra_tujuan AS retu ON resa.id_tujuan_renstra = retu.id_tujuan_renstra
INNER JOIN trx_renstra_misi AS remi ON retu.id_misi_renstra = remi.id_misi_renstra
INNER JOIN trx_renstra_visi AS revi ON remi.id_visi_renstra = revi.id_visi_renstra
INNER JOIN ref_unit AS ru ON ru.id_unit = revi.id_unit
where m.id_misi_rpjmd='. $row->id_misi_rpjmd.' and rpp.id_program_rpjmd='.$row2->id_program_rpjmd);
    			$totalpagu1=0;
    			$totalpagu2=0;
    			$totalpagu3=0;
    			$totalpagu4=0;
    			$totalpagu5=0;
    			foreach($Unit as $row3) {
    				
    				PDF::SetFont('helvetica', '', 6);
    				PDF::MultiCell($w2[0], 7, '', 0, 'L', 0, 0);
    				PDF::MultiCell($w2[1], 7, '', 0, 'L', 0, 0);
    				PDF::MultiCell($w2[2], 7, $row3->nm_unit, 0, 'L', 0, 0);
    				PDF::MultiCell($w2[3], 7, '', 0, 'L', 0, 0);
    				
    				PDF::Ln();
    				$countrow++;
    				if($countrow>=$totalrow)
    				{
    					PDF::AddPage('L');
    					$countrow=0;
    					for($i = 0; $i < $num_headers; ++$i) {
    						PDF::MultiCell($wh[$i], 7, $header[$i], 0, 'C', 1, 0);
    					}
    					PDF::Ln();
    					$countrow++;
    				}
    				
    				$programRenstra = DB::select('SELECT DISTINCT
concat(remi.id_misi_renstra,".",
retu.id_tujuan_renstra,".",
resa.id_sasaran_renstra,".",
rep.id_program_renstra) as kode,
ru.nm_unit,
rep.uraian_program_renstra,
rep.pagu_tahun1,
rep.pagu_tahun2,
rep.pagu_tahun3,
rep.pagu_tahun4,
rep.pagu_tahun5
FROM
trx_rpjmd_visi AS v
INNER JOIN trx_rpjmd_misi AS m ON m.id_visi_rpjmd = v.id_visi_rpjmd
INNER JOIN trx_rpjmd_tujuan AS t ON t.id_misi_rpjmd = m.id_misi_rpjmd
INNER JOIN trx_rpjmd_sasaran AS s ON s.id_tujuan_rpjmd = t.id_tujuan_rpjmd
INNER JOIN trx_rpjmd_program AS rpp ON rpp.id_sasaran_rpjmd = s.id_sasaran_rpjmd
INNER JOIN trx_renstra_program AS rep ON rep.id_program_rpjmd = rpp.id_program_rpjmd
INNER JOIN trx_renstra_sasaran AS resa ON rep.id_sasaran_renstra = resa.id_sasaran_renstra
INNER JOIN trx_renstra_tujuan AS retu ON resa.id_tujuan_renstra = retu.id_tujuan_renstra
INNER JOIN trx_renstra_misi AS remi ON retu.id_misi_renstra = remi.id_misi_renstra
INNER JOIN trx_renstra_visi AS revi ON remi.id_visi_renstra = revi.id_visi_renstra
INNER JOIN ref_unit AS ru on revi.id_unit=ru.id_unit

where m.id_misi_rpjmd='. $row->id_misi_rpjmd.' and rpp.id_program_rpjmd='.$row2->id_program_rpjmd.' and ru.id_unit='.$row3->id_unit);
    				
    				
    				foreach($programRenstra as $row4) {
    					PDF::SetFont('helvetica', '', 6);
    					PDF::MultiCell($w3[0], 6, $row4->kode, 0, 'L', 0, 0);
    					PDF::MultiCell($w3[1], 6, '', 0, 'L', 0, 0);
    					PDF::MultiCell($w3[2], 6, $row4->uraian_program_renstra, 0, 'L', 0, 0);
    					PDF::MultiCell($w3[3], 6, number_format($row4->pagu_tahun1,2,',','.'), 0, 'R', 0, 0);
    					PDF::MultiCell($w3[4], 6, number_format($row4->pagu_tahun2,2,',','.'), 0, 'R', 0, 0);
    					PDF::MultiCell($w3[5], 6, number_format($row4->pagu_tahun3,2,',','.'), 0, 'R', 0, 0);
    					PDF::MultiCell($w3[6], 6, number_format($row4->pagu_tahun4,2,',','.'), 0, 'R', 0, 0);
    					PDF::MultiCell($w3[7], 6, number_format($row4->pagu_tahun5,2,',','.'), 0, 'R', 0, 0);
    					$totalpagu1=$totalpagu1+$row4->pagu_tahun1;
    					$totalpagu2=$totalpagu2+$row4->pagu_tahun2;
    					$totalpagu3=$totalpagu3+$row4->pagu_tahun3;
    					$totalpagu4=$totalpagu4+$row4->pagu_tahun4;
    					$totalpagu5=$totalpagu5+$row4->pagu_tahun5;
    					PDF::Ln();
    					$countrow++;
    					if($countrow>=$totalrow)
    					{
    						PDF::AddPage('L');
    						$countrow=0;
    						for($i = 0; $i < $num_headers; ++$i) {
    							PDF::MultiCell($wh[$i], 7, $header[$i], 0, 'C', 1, 0);
    						}
    						PDF::Ln();
    						$countrow++;
    					}   					
    					
    				}
    				
    			}
    			PDF::SetFont('helvetica', 'B', 6);
    			PDF::MultiCell($w3[0], 6, '', 0, 'R', 0, 0);
    			PDF::MultiCell($w3[1], 6, '', 0, 'R', 0, 0);
    			PDF::MultiCell($w3[2], 6, 'Total Pagu Program Renstra :', 0, 'R', 0, 0);
    			PDF::MultiCell($w3[3], 6, number_format($totalpagu1,2,',','.'), 0, 'R', 0, 0);
    			PDF::MultiCell($w3[4], 6, number_format($totalpagu2,2,',','.'), 0, 'R', 0, 0);
    			PDF::MultiCell($w3[5], 6, number_format($totalpagu3,2,',','.'), 0, 'R', 0, 0);
    			PDF::MultiCell($w3[6], 6, number_format($totalpagu4,2,',','.'), 0, 'R', 0, 0);
    			PDF::MultiCell($w3[7], 6, number_format($totalpagu5,2,',','.'), 0, 'R', 0, 0);
    			PDF::Ln();
    			PDF::SetFont('helvetica', 'B', 6);
    			PDF::MultiCell($w3[0], 6, '', 0, 'R', 0, 0);
    			PDF::MultiCell($w3[1], 6, '', 0, 'R', 0, 0);
    			PDF::MultiCell($w3[2], 6, 'Selisih Total Pagu Program Renstra - Pagu Program RPJMD :', 0, 'R', 0, 0);
    			PDF::SetTextColor(0,0,0);
    			if ($totalpagu1-$row2->pagu_tahun1==0)
    			{
    				PDF::SetTextColor(0,0,0);
    				
    			}
    			else 
    			{
    				PDF::SetTextColor(255,0,0);
    			}
    			PDF::MultiCell($w3[3], 6, number_format($totalpagu1-$row2->pagu_tahun1,2,',','.'), 0, 'R', 0, 0);
    			PDF::SetTextColor(0,0,0);
    			if ($totalpagu2-$row2->pagu_tahun2==0)
    			{
    				PDF::SetTextColor(0,0,0);
    				
    			}
    			else
    			{
    				PDF::SetTextColor(255,0,0);
    			}
    			PDF::MultiCell($w3[4], 6, number_format($totalpagu2-$row2->pagu_tahun2,2,',','.'), 0, 'R', 0, 0);
    			PDF::SetTextColor(0,0,0);
    			if ($totalpagu3-$row2->pagu_tahun3==0)
    			{
    				PDF::SetTextColor(0,0,0);
    				
    			}
    			else
    			{
    				PDF::SetTextColor(255,0,0);
    			}
    			PDF::MultiCell($w3[5], 6, number_format($totalpagu3-$row2->pagu_tahun3,2,',','.'), 0, 'R', 0, 0);
    			PDF::SetTextColor(0,0,0);
    			if ($totalpagu4-$row2->pagu_tahun4==0)
    			{
    				PDF::SetTextColor(0,0,0);
    				
    			}
    			else
    			{
    				PDF::SetTextColor(255,0,0);
    			}
    			PDF::MultiCell($w3[6], 6, number_format($totalpagu4-$row2->pagu_tahun4,2,',','.'), 0, 'R', 0, 0);
    			PDF::SetTextColor(0,0,0);
    			if ($totalpagu5-$row2->pagu_tahun5==0)
    			{
    				PDF::SetTextColor(0,0,0);
    				
    			}
    			else
    			{
    				PDF::SetTextColor(255,0,0);
    			}
    			PDF::MultiCell($w3[7], 6, number_format($totalpagu5-$row2->pagu_tahun5,2,',','.'), 0, 'R', 0, 0);
    			PDF::SetTextColor(0,0,0);
    			PDF::Ln();
    		}

    	}
        
    PDF::Cell(array_sum($w), 0, '', 'T');

    // ---------------------------------------------------------

    // close and output PDF document
    PDF::Output('KompilasiRpjmdPDF.pdf', 'I');
  }

  
}

/*
 * route
 * Route::get('/PrintProgramRPJMD','Laporan\CetakRpjmdPDFController@KompilasiProgramdanPaguRpjmdPDF');
Route::get('/PrintKompilasiKegiatandanPaguRpjmdPDF/{id_unit}','Laporan\CetakRpjmdPDFController@KompilasiKegiatandanPaguRpjmdPDF');

 * 
 * 
 * JS
 * $(document).on('click', '.btnPrintKompilasiProgramdanPagu', function() {

    location.replace('../PrintProgramRPJMD/');
    
  });
$(document).on('click', '.btnPrintKompilasiKegiatandanPaguRpjmdPDF', function() {

    location.replace('../PrintKompilasiKegiatandanPaguRpjmdPDF/'+ $('#id_unit').val());
    
  });

 *
 *
 *
 *VIEW
 *  <div class="form-group">
                    <label class="control-label col-sm-3 text-left" for="id_unit">Unit Penyusun Renstra :</label>
                        <div class="col-sm-5">
                            <select class="form-control id_Unit" name="id_unit" id="id_unit"></select>
                        </div>
                </div>
                <div class="printPrintKompilasiProgramdanPagu">
              <p><a class="btnPrintKompilasiProgramdanPagu btn btn-sm btn-success" ><i class="glyphicon glyphicon-print"></i> Cetak Kompilasi Program dan Pagu</a></p>
            </div>
            <div class="PrintKompilasiKegiatandanPaguRpjmdPDF">
              <p><a class="btnPrintKompilasiKegiatandanPaguRpjmdPDF btn btn-sm btn-success" ><i class="glyphicon glyphicon-print"></i> Cetak Kompilasi Kegiatan dan Pagu</a></p>
            </div>
            
                </form>
 
 
 * /
 */

