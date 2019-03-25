<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use DB;
use Response;
use Session;
use PDF;
use Auth;

class CetakASBAktivitasValiditasController extends Controller
{

  public function printValiditasASB($perkada)
  {
  	
  	//return $id_aktiv;
  	// set document information
  	PDF::SetCreator('BPKP');
  	PDF::SetAuthor('BPKP');
  	PDF::SetTitle('Simd@Perencanaan');
  	PDF::SetSubject('Cek Validitas ASB');
  	
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
  	PDF::SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
  	
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
  	$countrow=0;
  	$totalrow=30;
  	
  	// column titles
    // Header

    $header = array('No Urut','Nama Aktivitas','Status');
      // $w = array(40, 35, 40, 45);
      // $num_headers = count($header);
      // for($i = 0; $i < $num_headers; ++$i) {
      //    PDF::Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
      // }
      // PDF::Ln();
  	
  	// Colors, line width and bold font
  	PDF::SetFillColor(200, 200, 200);
  	PDF::SetTextColor(0);
  	PDF::SetDrawColor(255, 255, 255);
  	PDF::SetLineWidth(0.3);
  	
  	//Header
  	//$v1=20;
  	//$v2=30;
  	$pemda=Session::get('xPemda');
  	// $akt=$id_aktivitas;
  	// $zona=1;
  	// $sum=0;
   //  $id_perhitungan=1;

    $ASBValiditas = DB::select('SELECT (@id:=@id+1) as no_urut, p.* FROM (SELECT  n.id_asb_sub_sub_kelompok, n.nm_aktivitas_asb, sum(n.validitas) as validitas,
            CASE WHEN sum(n.validitas) > 0 THEN "NON VALID" ELSE "OKE" END AS validitas_display 
            FROM (SELECT m.*, CASE WHEN (m.cek_1 = 0 OR m.cek_2 = 0) THEN 1 ELSE 0 END AS validitas FROM 
            (SELECT a.jenis_biaya, a.hub_driver, a.id_satuan1, a.id_satuan2, c.id_satuan_1, c.id_satuan_2, c.sat_derivatif_1, 
            c.sat_derivatif_2, a.koefisien1, a.koefisien2, a.koefisien3, c.id_asb_sub_sub_kelompok, c.nm_aktivitas_asb,
            CASE a.jenis_biaya WHEN 2 THEN 
              CASE a.hub_driver
                WHEN 1 THEN  CASE a.id_satuan1 WHEN c.id_satuan_1 THEN 1 ELSE 0 END
                WHEN 2 THEN CASE a.id_satuan1 WHEN c.id_satuan_2 THEN 1 ELSE 0 END
                WHEN 3 THEN CASE a.id_satuan1 WHEN c.id_satuan_1 THEN 
                    CASE a.id_satuan2 WHEN c.id_satuan_2 THEN 1 ELSE 0 END 
                ELSE 0 END
                WHEN 4 THEN CASE a.id_satuan1 WHEN c.sat_derivatif_1 THEN 1 ELSE 0 END
                WHEN 5 THEN CASE a.id_satuan1 WHEN c.sat_derivatif_2 THEN 1 ELSE 0 END
                WHEN 6 THEN CASE a.id_satuan1 WHEN c.sat_derivatif_1 THEN 
                    CASE a.id_satuan2 WHEN c.sat_derivatif_2 THEN 1 ELSE 0 END 
                ELSE 0 END
                WHEN 7 THEN CASE a.id_satuan1 WHEN c.sat_derivatif_1 THEN 
                    CASE a.id_satuan2 WHEN c.id_satuan_2 THEN 1 ELSE 0 END 
                ELSE 0 END
                WHEN 8 THEN CASE a.id_satuan1 WHEN c.sat_derivatif_2 THEN 
                    CASE a.id_satuan2 WHEN c.id_satuan_1 THEN 1 ELSE 0 END 
                ELSE 0 END
              ELSE 0 END 
            ELSE 1 END AS cek_1, 
            CASE WHEN (a.koefisien1 > 0) THEN 
                CASE WHEN (a.koefisien2 > 0) THEN
                    CASE WHEN (a.koefisien3 > 0) THEN 1 ELSE 0 END    
                ELSE 0 END
            ELSE 0 END AS cek_2
            FROM trx_asb_komponen_rinci a
            INNER JOIN trx_asb_komponen b ON a.id_komponen_asb = b.id_komponen_asb
            INNER JOIN trx_asb_aktivitas c ON b.id_aktivitas_asb = c.id_aktivitas_asb) m) n
            GROUP BY n.id_asb_sub_sub_kelompok, n.nm_aktivitas_asb ) p , (SELECT @id:=0) b
            ORDER BY (@id:=@id+1)');

  	PDF::SetFont('helvetica', 'B', 14);
  	PDF::Cell('180', 5, Session::get('xPemda') , 'L', 0, 'C', 0);
  	PDF::Ln();
  	PDF::Cell('180', 5, 'DAFTAR AKTIVITAS YANG BELUM VALID', 'L', 0, 'C', 0);
  	PDF::Ln();
  	PDF::Ln();
  	PDF::SetFont('helvetica', 'B', 9);	
  		
  		PDF::SetFont('', 'B');
  		PDF::SetFont('helvetica', 'B', 8);
  		PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));

  		$wh = array(15,100,50);
      // $w = array(80,100);
      // $w1 = array(5,75,100);
      $w2 = array(15,100,50);

      $num_headers = count($header);
      for($i = 0; $i < $num_headers; ++$i) {
        PDF::Cell($wh[$i], 7, $header[$i], 0, 0, 'C', 0);
      }
      PDF::Ln();
      $countrow++;

      // Color and font restoration      
      PDF::SetFillColor(224, 235, 255);
      PDF::SetTextColor(0);
      PDF::SetFont('helvetica', '', 7);

      foreach($ASBValiditas as $row4) {
          PDF::MultiCell($w2[0], 5, $row4->no_urut, 0, 'C', 0, 0);
          PDF::MultiCell($w2[1], 5, $row4->nm_aktivitas_asb, 0, 'L', 0, 0);
          PDF::MultiCell($w2[2], 5, $row4->validitas_display, 0, 'C', 0, 0);
          PDF::SetTextColor(0,0,0);
          PDF::Ln();
          $countrow++;
          if($countrow>=$totalrow)
          {
            PDF::AddPage('P');
            $countrow=0;
            for($i = 0; $i < $num_headers; ++$i) {
              PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
            }
            PDF::Ln();
            $countrow++;
          }
      }



			
    PDF::Output('CetakValiditasASB.pdf', 'I');
  }  
  
}
