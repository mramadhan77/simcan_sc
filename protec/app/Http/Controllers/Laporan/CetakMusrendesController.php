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
use App\Models\TrxMusrenbangRw;
use App\Models\TrxMusrenbangDesa;
use App\Models\TrxMusrenbangDesaLokasi;
use App\Models\RefSshRekening;
use App\Models\RefRek5;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;


class CetakMusrendesController extends Controller
{

	public function printusulanrw()
  {
		
  	$countrow=0;
  	$totalrow=18;
		
		

    // set document information
    $pemda=Session::get('xPemda');
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
    $header = array('SKPD/Kegiatan/Aktivitas','Kecamatan','Desa','Volume','Nilai Musrendes');

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
    PDF::Cell('275', 5, 'USULAN MUSRENBANG RW', 1, 0, 'C', 0);
    PDF::Ln();
    $countrow++;
    PDF::SetFont('', 'B');
    PDF::SetFont('helvetica', 'B', 6);
    
    // Header Column
    
    $wh = array(125,40,40,30,40);
    $w = array(125,150);
    $w1 = array(5,120,150);
    $w2 = array(10,115,40,40,30,40);
    
    $num_headers = count($header);
    for($i = 0; $i < $num_headers; ++$i) {
            PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
    }
    PDF::Ln();
    $countrow++;
        // Color and font restoration

    PDF::SetFillColor(224, 235, 255);
    PDF::SetTextColor(0);
    PDF::SetFont('helvetica', '', 6);
        // Data
    $fill = 0;
    $Unit=DB::select('SELECT DISTINCT
e.id_unit,
e.nm_unit
FROM
trx_musrendes_rw AS a
INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan

');
    foreach($Unit as $row) {
    	PDF::MultiCell($w[0], 10, $row->nm_unit, 0, 'L', 0, 0);
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
    	$kegiatan = DB::select('SELECT DISTINCT
a.id_renja,
d.uraian_kegiatan_renstra
FROM
trx_musrendes_rw AS a
INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
where e.id_unit='.$row->id_unit);
    	foreach($kegiatan as $row2) {
    		PDF::MultiCell($w1[0], 10, '', 0, 'L', 0, 0);
    		PDF::MultiCell($w1[1], 10, $row2->uraian_kegiatan_renstra, 0, 'L', 0, 0);
    		PDF::MultiCell($w1[2], 10, '', 0, 'L', 0, 0);
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
    		$aktivitas = DB::select('SELECT
a.uraian_aktivitas_kegiatan,
c.nama_kecamatan,
b.nama_desa,
a.volume,
f.uraian_satuan,
a.jml_pagu
FROM
trx_musrendes_rw AS a
INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
where e.id_unit='. $row->id_unit .' and a.id_renja='. $row2->id_renja);
    		
    		foreach($aktivitas as $row3) {
    			PDF::MultiCell($w2[0], 10, '', 0, 'L', 0, 0);
    			PDF::MultiCell($w2[1], 10, $row3->uraian_aktivitas_kegiatan, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[2], 10, $row3->nama_kecamatan, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[3], 10, $row3->nama_desa, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[4], 10, $row3->volume.' '.$row3->uraian_satuan, 0, 'L', 0, 0);
    			PDF::MultiCell($w2[5], 10, number_format($row3->jml_pagu,2,',','.'), 0, 'R', 0, 0);
    			
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

    // close and output PDF document
    PDF::Output('KompilasiUsulanRW.pdf', 'I');
  }

  public function printusulanrdesa()
  {
  	
  	$countrow=0;
  	$totalrow=18;
  	
  	
  	
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
  	$header = array('SKPD/Kegiatan/Aktivitas','Kecamatan','Desa','Volume','Nilai Musrendes');
  	
  	// Colors, line width and bold font
  	PDF::SetFillColor(200, 200, 200);
  	PDF::SetTextColor(0);
  	PDF::SetDrawColor(255, 255, 255);
  	PDF::SetLineWidth(0);
  	PDF::SetFont('helvetica', 'B', 10);
  	
  	//Header
  	PDF::Cell('275', 5, 'PEMERINTAH DAERAH KABUPATEN PURWOREJO', 1, 0, 'C', 0);
  	PDF::Ln();
  	$countrow++;
  	PDF::Cell('275', 5, 'USULAN MUSRENBANG RW', 1, 0, 'C', 0);
  	PDF::Ln();
  	$countrow++;
  	PDF::SetFont('', 'B');
  	PDF::SetFont('helvetica', 'B', 6);
  	
  	// Header Column
  	
  	$wh = array(125,40,40,30,40);
  	$w = array(125,150);
  	$w1 = array(5,120,150);
  	$w2 = array(10,115,40,40,30,40);
  	
  	$num_headers = count($header);
  	for($i = 0; $i < $num_headers; ++$i) {
  		PDF::MultiCell($wh[$i], 10, $header[$i], 0, 'C', 1, 0);
  	}
  	PDF::Ln();
  	$countrow++;
  	// Color and font restoration
  	
  	PDF::SetFillColor(224, 235, 255);
  	PDF::SetTextColor(0);
  	PDF::SetFont('helvetica', '', 6);
  	// Data
  	$fill = 0;
  	$Unit=DB::select('SELECT DISTINCT
e.id_unit,
e.nm_unit
FROM
trx_musrendes AS a
INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
  			
');
  	foreach($Unit as $row) {
  		PDF::MultiCell($w[0], 10, $row->nm_unit, 0, 'L', 0, 0);
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
  		$kegiatan = DB::select('SELECT DISTINCT
a.id_renja,
d.uraian_kegiatan_renstra
FROM
trx_musrendes AS a
INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
where e.id_unit='.$row->id_unit);
  		foreach($kegiatan as $row2) {
  			PDF::MultiCell($w1[0], 10, '', 0, 'L', 0, 0);
  			PDF::MultiCell($w1[1], 10, $row2->uraian_kegiatan_renstra, 0, 'L', 0, 0);
  			PDF::MultiCell($w1[2], 10, '', 0, 'L', 0, 0);
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
  			$aktivitas = DB::select('SELECT
a.uraian_aktivitas_kegiatan,
c.nama_kecamatan,
b.nama_desa,
a.volume,
f.uraian_satuan,
a.jml_pagu
FROM
trx_musrendes AS a
INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
where e.id_unit='. $row->id_unit .' and a.id_renja='. $row2->id_renja);
  			
  			foreach($aktivitas as $row3) {
  				PDF::MultiCell($w2[0], 10, '', 0, 'L', 0, 0);
  				PDF::MultiCell($w2[1], 10, $row3->uraian_aktivitas_kegiatan, 0, 'L', 0, 0);
  				PDF::MultiCell($w2[2], 10, $row3->nama_kecamatan, 0, 'L', 0, 0);
  				PDF::MultiCell($w2[3], 10, $row3->nama_desa, 0, 'L', 0, 0);
  				PDF::MultiCell($w2[4], 10, $row3->volume.' '.$row3->uraian_satuan, 0, 'L', 0, 0);
  				PDF::MultiCell($w2[5], 10, number_format($row3->jml_pagu,2,',','.'), 0, 'R', 0, 0);
  				
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
  	
  	// close and output PDF document
  	PDF::Output('KompilasiUsulanDesa.pdf', 'I');
  }
  
 
  
}

/*
 * route
 * Route::get('/PrintKompilasiProgramdanPaguRenja/{id_unit}','Laporan\CetakRenjaController@KompilasiProgramdanPaguRenja');
Route::get('/PrintKompilasiKegiatandanPaguRenja/{id_unit}','Laporan\CetakRenjaController@KompilasiKegiatandanPaguRenja');

 * 
 * 
 * JS
 * $(document).on('click', '.btnPrintKompilasiProgramdanPagu', function() {

    location.replace('../PrintKompilasiProgramdanPaguRenja/'+ $('#id_unit').val());
    
  });
$(document).on('click', '.btnPrintKompilasiKegiatandanPaguRenja', function() {

    location.replace('../PrintKompilasiKegiatandanPaguRenja/'+ $('#id_unit').val());
    
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
            <div class="PrintKompilasiKegiatandanPaguRenja">
              <p><a class="btnPrintKompilasiKegiatandanPaguRenja btn btn-sm btn-success" ><i class="glyphicon glyphicon-print"></i> Cetak Kompilasi Kegiatan dan Pagu</a></p>
            </div>
            
                </form>
 
 
 * /
 */

