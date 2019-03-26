<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;

use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Response;
use Session;
use PDF;
use DB;
use App\Models\TrxIsianDataDasar;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;


class CetakDataDasarController extends Controller
{

    public function printPdrb($tahun)
  {
  	
  	//return $id_aktiv;
  	// set document information
  	PDF::SetCreator('BPKP');
  	PDF::SetAuthor('BPKP');
  	PDF::SetTitle('Simd@Perencanaan');
  	PDF::SetSubject('ASB Komponen');
  	
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
  	
  	$pemda=session::get('xPemda');
  	$tahun_awal=$tahun-5;
  	$tahun_akhir=$tahun-1;
  	
  	
  	// add a page
  	PDF::AddPage('P');
  	$countrow=0;
  	$totalrow=43;
  	PDF::SetFont('helvetica', 'B', 10);
  	// Header
  	PDF::SetLineWidth(0);
  	PDF::Cell('180', 5, 'Nilai dan Kontribusi Sektor dalam PDRB Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
  	PDF::Ln();
  	$countrow ++;
  	PDF::Cell('180', 5, 'atas Dasar Harga Konstan Tahun '.$tahun, 0, 0, 'C', 0);
  	PDF::Ln();
  	$countrow ++;
  	PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
  	PDF::Ln();
  	$countrow ++;
  	// set font
  	PDF::SetFont('helvetica', '', 6);
  	// column titles
  	$header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
  	$header1 = array('No','Kecamatan / Sektor','(Rp)','%','(Rp)','%','(Rp)','%','(Rp)','%','(Rp)','%');
  	
  	// Colors, line width and bold font
  	PDF::SetFillColor(200, 200, 200);
  	PDF::SetTextColor(0);
  	PDF::SetDrawColor(255, 255, 255);
  	PDF::SetLineWidth(0.1);
  	
  	//Header
  	//$v1=20;
  	//$v2=30;
  	
  	
  	
  	$Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=1
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
  	
  		// Header Column
  		$wh = array(7,30,30,30,30,30,30);
  		$wh1 = array(7,30,20,10,20,10,20,10,20,10,20,10);
  		$w = array(7,30,20,10,20,10,20,10,20,10,20,10);
  		$w1 = array(7,3,27,20,10,20,10,20,10,20,10,20,10);
  		
  		
  		PDF::SetFillColor(225, 225, 225);
  		PDF::SetTextColor(0);
  		PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
  		PDF::SetFont('helvetica', 'B', 8);
  		$num_headers = count($header);
  		for($i = 0; $i < $num_headers; ++$i) {
  		    if($i<2)
  		    {
  		        PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
  		    }
  		    else 
  		    {
  		    PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
  		    }
  		}
  		PDF::Ln();
  		$countrow++;
  		$num_headers1 = count($header1);
  		for($i = 0; $i < $num_headers1; ++$i) {
  		    if($i<2)
  		    {
  		        PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
  		    }
  		    else
  		    {
  		        PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
  		    }
  		}
  		PDF::Ln();
  		$countrow++;
  		// Color and font restoration
  		
  		//PDF::SetLineStyle(1);
  		PDF::SetFont('helvetica', '', 7);
  		// Data
  		
  		foreach($Kecamatan as $row) {
  		    $height=5;
//   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
//   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
//   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
//   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
  		    PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
  		   // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
  		    PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[7], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[8], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[9], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[10], $height, '', 1, 'L', 0, 0);
  		    PDF::MultiCell($w[11], $height, '', 1, 'L', 0, 0);
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
  			$Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from 
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=1 and a.id_kecamatan='.$row->id_kecamatan);
  			foreach($Pdrb as $row2) {
  			    //$height=5;
  			    $height=ceil((strlen($row2->nama_kolom)/20))*4;
  			    PDF::MultiCell($w1[0], $height, $row2->no_urut, 1, 'C', 0, 0);
  			    PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
  			    PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
  			    PDF::MultiCell($w1[3], $height, number_format($row2->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
  			    PDF::MultiCell($w1[4], $height, $row2->nmin5_persen, 1, 'R', 0, 0);
  			    PDF::MultiCell($w1[5], $height, number_format($row2->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
  			    PDF::MultiCell($w1[6], $height, $row2->nmin4_persen, 1, 'R', 0, 0);
  			    PDF::MultiCell($w1[7], $height, number_format($row2->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
  			    PDF::MultiCell($w1[8], $height, $row2->nmin3_persen, 1, 'R', 0, 0);
  			    PDF::MultiCell($w1[9], $height, number_format($row2->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
  			    PDF::MultiCell($w1[10], $height, $row2->nmin2_persen, 1, 'R', 0, 0);
  			    PDF::MultiCell($w1[11], $height, number_format($row2->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
  			    PDF::MultiCell($w1[12], $height, $row2->nmin1_persen, 1, 'R', 0, 0);
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
  		}
  		PDF::Output('CetakPDRB.pdf', 'I');
    }
  
    public function printPdrbHb($tahun)
    {
        
        //return $id_aktiv;
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('ASB Komponen');
        
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
        
        $pemda=session::get('xPemda');
        $tahun_awal=$tahun-5;
        $tahun_akhir=$tahun-1;
        
        
        // add a page
        PDF::AddPage('P');
        $countrow=0;
        $totalrow=43;
        PDF::SetFont('helvetica', 'B', 10);
        // Header
        PDF::SetLineWidth(0);
        PDF::Cell('180', 5, 'Nilai dan Kontribusi Sektor dalam PDRB Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Cell('180', 5, 'atas Dasar Harga Berlaku', 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        // set font
        PDF::SetFont('helvetica', '', 6);
        // column titles
        $header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
        $header1 = array('No','Kecamatan / Sektor','(Rp)','%','(Rp)','%','(Rp)','%','(Rp)','%','(Rp)','%');
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0.1);
        
        //Header
        //$v1=20;
        //$v2=30;
        
        
        
        $Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=2
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
        
        // Header Column
        $wh = array(7,30,30,30,30,30,30);
        $wh1 = array(7,30,20,10,20,10,20,10,20,10,20,10);
        $w = array(7,30,20,10,20,10,20,10,20,10,20,10);
        $w1 = array(7,3,27,20,10,20,10,20,10,20,10,20,10);
        
        
        PDF::SetFillColor(225, 225, 225);
        PDF::SetTextColor(0);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        PDF::SetFont('helvetica', 'B', 8);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        $num_headers1 = count($header1);
        for($i = 0; $i < $num_headers1; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        // Color and font restoration
        
        //PDF::SetLineStyle(1);
        PDF::SetFont('helvetica', '', 7);
        // Data
        
        foreach($Kecamatan as $row) {
            $height=5;
            //   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
            //   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
            //   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
            //   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
            PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
            // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
            PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[7], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[8], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[9], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[10], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[11], $height, '', 1, 'L', 0, 0);
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
            $Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=2 and a.id_kecamatan='.$row->id_kecamatan);
            foreach($Pdrb as $row2) {
                //$height=5;
                $height=ceil((strlen($row2->nama_kolom)/20))*4;
                PDF::MultiCell($w1[0], $height, $row2->no_urut, 1, 'C', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, number_format($row2->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[4], $height, $row2->nmin5_persen, 1, 'R', 0, 0);
                PDF::MultiCell($w1[5], $height, number_format($row2->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[6], $height, $row2->nmin4_persen, 1, 'R', 0, 0);
                PDF::MultiCell($w1[7], $height, number_format($row2->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[8], $height, $row2->nmin3_persen, 1, 'R', 0, 0);
                PDF::MultiCell($w1[9], $height, number_format($row2->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[10], $height, $row2->nmin2_persen, 1, 'R', 0, 0);
                PDF::MultiCell($w1[11], $height, number_format($row2->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[12], $height, $row2->nmin1_persen, 1, 'R', 0, 0);
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
        }
        PDF::Output('CetakPDRBHB.pdf', 'I');
    }
    
    public function printAmh($tahun)
    {
        
        //return $id_aktiv;
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('ASB Komponen');
        
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
        
        $pemda=session::get('xPemda');
        $tahun_awal=$tahun-5;
        $tahun_akhir=$tahun-1;
        
        
        // add a page
        PDF::AddPage('P');
        $countrow=0;
        $totalrow=43;
        PDF::SetFont('helvetica', 'B', 10);
        // Header
        PDF::SetLineWidth(0);
        PDF::Cell('180', 5, 'Perkembangan Angka Melek Huruf Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
//         PDF::Cell('180', 5, 'atas Dasar Harga Berlaku', 0, 0, 'C', 0);
//         PDF::Ln();
//         $countrow ++;
        PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        // set font
        PDF::SetFont('helvetica', '', 6);
        // column titles
        $header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
        $header1 = array('No','Kecamatan / Uraian','orang','orang','orang','orang','orang');
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0.1);
        
        //Header
        //$v1=20;
        //$v2=30;
        
        
        
        $Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=3
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
        
        // Header Column
        $wh = array(7,30,30,30,30,30,30);
        $wh1 = array(7,30,30,30,30,30,30);
        $w = array(7,30,30,30,30,30,30);
        $w1 = array(7,3,27,30,30,30,30,30);
        
        
        PDF::SetFillColor(225, 225, 225);
        PDF::SetTextColor(0);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        PDF::SetFont('helvetica', 'B', 8);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        $num_headers1 = count($header1);
        for($i = 0; $i < $num_headers1; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        // Color and font restoration
        
        //PDF::SetLineStyle(1);
        PDF::SetFont('helvetica', '', 7);
        // Data
        
        foreach($Kecamatan as $row) {
            $height=5;
            //   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
            //   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
            //   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
            //   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
            PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
            // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
            PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
            
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
            $Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=3 and a.id_kecamatan='.$row->id_kecamatan);
            foreach($Pdrb as $row2) {
                //$height=5;
                $height=ceil((strlen($row2->nama_kolom)/20))*4;
                PDF::MultiCell($w1[0], $height, $row2->no_urut, 1, 'C', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, number_format($row2->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
               
                PDF::MultiCell($w1[4], $height, number_format($row2->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
               
                PDF::MultiCell($w1[5], $height, number_format($row2->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
               
                PDF::MultiCell($w1[6], $height, number_format($row2->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
               
                PDF::MultiCell($w1[7], $height, number_format($row2->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
               
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
        }
        PDF::Output('CetakAMH.pdf', 'I');
    }
    
    public function printRLS($tahun)
    {
        
        //return $id_aktiv;
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('ASB Komponen');
        
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
        
        $pemda=session::get('xPemda');
        $tahun_awal=$tahun-5;
        $tahun_akhir=$tahun-1;
        
        
        // add a page
        PDF::AddPage('P');
        $countrow=0;
        $totalrow=43;
        PDF::SetFont('helvetica', 'B', 10);
        // Header
        PDF::SetLineWidth(0);
        PDF::Cell('180', 5, 'Rata-rata Lama Sekolah Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Cell('180', 5, '', 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        // set font
        PDF::SetFont('helvetica', '', 6);
        // column titles
        $header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
        $header1 = array('No','Kecamatan / Uraian','L','P','L','P','L','P','L','P','L','P');
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0.1);
        
        //Header
        //$v1=20;
        //$v2=30;
        
        
        
        $Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=4
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
        
        // Header Column
        $wh = array(7,30,30,30,30,30,30);
        $wh1 = array(7,30,15,15,15,15,15,15,15,15,20,10);
        $w = array(7,30,15,15,15,15,15,15,15,15,20,10);
        $w1 = array(7,3,27,15,15,15,15,15,15,15,15,20,10);
        
        
        PDF::SetFillColor(225, 225, 225);
        PDF::SetTextColor(0);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        PDF::SetFont('helvetica', 'B', 8);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        $num_headers1 = count($header1);
        for($i = 0; $i < $num_headers1; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        // Color and font restoration
        
        //PDF::SetLineStyle(1);
        PDF::SetFont('helvetica', '', 7);
        // Data
        
        foreach($Kecamatan as $row) {
            $height=5;
            //   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
            //   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
            //   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
            //   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
            PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
            // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
            PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[7], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[8], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[9], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[10], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[11], $height, '', 1, 'L', 0, 0);
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
            $Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=4 and a.id_kecamatan='.$row->id_kecamatan);
            foreach($Pdrb as $row2) {
                //$height=5;
                $height=ceil((strlen($row2->nama_kolom)/20))*4;
                PDF::MultiCell($w1[0], $height, $row2->no_urut, 1, 'C', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, number_format($row2->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[4], $height, $row2->nmin5_persen, 1, 'R', 0, 0);
                PDF::MultiCell($w1[5], $height, number_format($row2->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[6], $height, $row2->nmin4_persen, 1, 'R', 0, 0);
                PDF::MultiCell($w1[7], $height, number_format($row2->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[8], $height, $row2->nmin3_persen, 1, 'R', 0, 0);
                PDF::MultiCell($w1[9], $height, number_format($row2->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[10], $height, $row2->nmin2_persen, 1, 'R', 0, 0);
                PDF::MultiCell($w1[11], $height, number_format($row2->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
                PDF::MultiCell($w1[12], $height, $row2->nmin1_persen, 1, 'R', 0, 0);
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
        }
        PDF::Output('CetakPDRBHB.pdf', 'I');
    }
 
    public function printSeniOR($tahun)
    {
        
        //return $id_aktiv;
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('ASB Komponen');
        
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
        
        $pemda=session::get('xPemda');
        $tahun_awal=$tahun-5;
        $tahun_akhir=$tahun-1;
        
        
        // add a page
        PDF::AddPage('P');
        $countrow=0;
        $totalrow=43;
        PDF::SetFont('helvetica', 'B', 10);
        // Header
        PDF::SetLineWidth(0);
        PDF::Cell('180', 5, 'Perkembangan Seni, Budaya dan Olahraga Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        //         PDF::Cell('180', 5, 'atas Dasar Harga Berlaku', 0, 0, 'C', 0);
        //         PDF::Ln();
        //         $countrow ++;
        PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        // set font
        PDF::SetFont('helvetica', '', 6);
        // column titles
        $header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
        $header1 = array('No','Kecamatan / Uraian','buah','buah','buah','buah','buah');
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0.1);
        
        //Header
        //$v1=20;
        //$v2=30;
        
        
        
        $Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=5
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
        
        // Header Column
        $wh = array(7,30,30,30,30,30,30);
        $wh1 = array(7,30,30,30,30,30,30);
        $w = array(7,30,30,30,30,30,30);
        $w1 = array(7,3,27,30,30,30,30,30);
        
        
        PDF::SetFillColor(225, 225, 225);
        PDF::SetTextColor(0);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        PDF::SetFont('helvetica', 'B', 8);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        $num_headers1 = count($header1);
        for($i = 0; $i < $num_headers1; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        // Color and font restoration
        
        //PDF::SetLineStyle(1);
        PDF::SetFont('helvetica', '', 7);
        // Data
        
        foreach($Kecamatan as $row) {
            $height=5;
            //   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
            //   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
            //   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
            //   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
            PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
            // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
            PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
            
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
            $Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=5 and a.id_kecamatan='.$row->id_kecamatan);
            foreach($Pdrb as $row2) {
                //$height=5;
                $height=ceil((strlen($row2->nama_kolom)/20))*4;
                PDF::MultiCell($w1[0], $height, $row2->no_urut, 1, 'C', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, number_format($row2->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[4], $height, number_format($row2->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[5], $height, number_format($row2->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[6], $height, number_format($row2->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[7], $height, number_format($row2->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
                
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
        }
        PDF::Output('CetakSeniOR.pdf', 'I');
    }
    
    public function printAps($tahun)
    {
        
        //return $id_aktiv;
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('ASB Komponen');
        
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
        
        $pemda=session::get('xPemda');
        $tahun_awal=$tahun-5;
        $tahun_akhir=$tahun-1;
        
        
        // add a page
        PDF::AddPage('P');
        $countrow=0;
        $totalrow=43;
        PDF::SetFont('helvetica', 'B', 10);
        // Header
        PDF::SetLineWidth(0);
        PDF::Cell('180', 5, 'Angka Partisipasi Sekolah Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        //         PDF::Cell('180', 5, 'atas Dasar Harga Berlaku', 0, 0, 'C', 0);
        //         PDF::Ln();
        //         $countrow ++;
        PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        // set font
        PDF::SetFont('helvetica', '', 6);
        // column titles
        $header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
        $header1 = array('No','Kecamatan / Uraian','orang','orang','orang','orang','orang');
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0.1);
        
        //Header
        //$v1=20;
        //$v2=30;
        
        
        
        $Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=6
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
        
        // Header Column
        $wh = array(7,30,30,30,30,30,30);
        $wh1 = array(7,30,30,30,30,30,30);
        $w = array(7,30,30,30,30,30,30);
        $w1 = array(7,3,27,30,30,30,30,30);
        
        
        PDF::SetFillColor(225, 225, 225);
        PDF::SetTextColor(0);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        PDF::SetFont('helvetica', 'B', 8);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        $num_headers1 = count($header1);
        for($i = 0; $i < $num_headers1; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        // Color and font restoration
        
        //PDF::SetLineStyle(1);
        PDF::SetFont('helvetica', '', 7);
        // Data
        
        foreach($Kecamatan as $row) {
            $height=5;
            //   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
            //   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
            //   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
            //   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
            PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
            // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
            PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
            
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
            $Tingkat = DB::select(' select c.nama_kolom 
from trx_isian_data_dasar a
inner join ref_kolom_tabel_dasar b
on a.id_kolom_tabel_dasar=b.id_kolom_tabel_dasar
inner  join ref_kolom_tabel_dasar c
on b.parent_id=c.id_kolom_tabel_dasar
 
            where  c.level=0 and a.tahun='.$tahun.' and b.id_tabel_dasar=6 and a.id_kecamatan='.$row->id_kecamatan);
            foreach($Tingkat as $row2) {
                //$height=5;
                $height=ceil((strlen($row2->nama_kolom)/20))*4;
                PDF::MultiCell($w1[0], $height, '', 1, 'C', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[4], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[5], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[6], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[7], $height, '', 1, 'R', 0, 0);
                
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
                $Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=6 and a.id_kecamatan='.$row->id_kecamatan);
                foreach($Pdrb as $row3) {
                    //$height=5;
                    $height=ceil((strlen($row3->nama_kolom)/20))*4;
                    PDF::MultiCell($w1[0], $height, $row3->no_urut, 1, 'C', 0, 0);
                    PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                    PDF::MultiCell($w1[2], $height, $row3->nama_kolom, 'RTB', 'L', 0, 0);
                    PDF::MultiCell($w1[3], $height, number_format($row3->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[4], $height, number_format($row3->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[5], $height, number_format($row3->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[6], $height, number_format($row3->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[7], $height, number_format($row3->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
                    
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
            }
            
        }
        PDF::Output('CetakAPS.pdf', 'I');
    }
    
    public function printKts($tahun)
    {
        
        //return $id_aktiv;
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('ASB Komponen');
        
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
        
        $pemda=session::get('xPemda');
        $tahun_awal=$tahun-5;
        $tahun_akhir=$tahun-1;
        
        
        // add a page
        PDF::AddPage('P');
        $countrow=0;
        $totalrow=43;
        PDF::SetFont('helvetica', 'B', 10);
        // Header
        PDF::SetLineWidth(0);
        PDF::Cell('180', 5, 'Ketersediaan Sekolah dan Penduduk Usia Sekolah Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        //         PDF::Cell('180', 5, 'atas Dasar Harga Berlaku', 0, 0, 'C', 0);
        //         PDF::Ln();
        //         $countrow ++;
        PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        // set font
        PDF::SetFont('helvetica', '', 6);
        // column titles
        $header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
        $header1 = array('No','Kecamatan / Uraian','orang','orang','orang','orang','orang');
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0.1);
        
        //Header
        //$v1=20;
        //$v2=30;
        
        
        
        $Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=7
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
        
        // Header Column
        $wh = array(7,30,30,30,30,30,30);
        $wh1 = array(7,30,30,30,30,30,30);
        $w = array(7,30,30,30,30,30,30);
        $w1 = array(7,3,27,30,30,30,30,30);
        
        
        PDF::SetFillColor(225, 225, 225);
        PDF::SetTextColor(0);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        PDF::SetFont('helvetica', 'B', 8);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        $num_headers1 = count($header1);
        for($i = 0; $i < $num_headers1; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        // Color and font restoration
        
        //PDF::SetLineStyle(1);
        PDF::SetFont('helvetica', '', 7);
        // Data
        
        foreach($Kecamatan as $row) {
            $height=5;
            //   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
            //   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
            //   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
            //   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
            PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
            // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
            PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
            
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
            $Tingkat = DB::select(' select c.nama_kolom
from trx_isian_data_dasar a
inner join ref_kolom_tabel_dasar b
on a.id_kolom_tabel_dasar=b.id_kolom_tabel_dasar
inner  join ref_kolom_tabel_dasar c
on b.parent_id=c.id_kolom_tabel_dasar
                
            where  c.level=0 and a.tahun='.$tahun.' and b.id_tabel_dasar=7 and a.id_kecamatan='.$row->id_kecamatan);
            foreach($Tingkat as $row2) {
                //$height=5;
                $height=ceil((strlen($row2->nama_kolom)/20))*4;
                PDF::MultiCell($w1[0], $height, '', 1, 'C', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[4], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[5], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[6], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[7], $height, '', 1, 'R', 0, 0);
                
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
                $Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=7 and a.id_kecamatan='.$row->id_kecamatan);
                foreach($Pdrb as $row3) {
                    //$height=5;
                    $height=ceil((strlen($row3->nama_kolom)/20))*4;
                    PDF::MultiCell($w1[0], $height, $row3->no_urut, 1, 'C', 0, 0);
                    PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                    PDF::MultiCell($w1[2], $height, $row3->nama_kolom, 'RTB', 'L', 0, 0);
                    PDF::MultiCell($w1[3], $height, number_format($row3->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[4], $height, number_format($row3->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[5], $height, number_format($row3->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[6], $height, number_format($row3->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[7], $height, number_format($row3->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
                    
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
            }
            
        }
        PDF::Output('CetakKTS.pdf', 'I');
    }
    
    public function printGuruMurid($tahun)
    {
        
        //return $id_aktiv;
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('ASB Komponen');
        
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
        
        $pemda=session::get('xPemda');
        $tahun_awal=$tahun-5;
        $tahun_akhir=$tahun-1;
        
        
        // add a page
        PDF::AddPage('P');
        $countrow=0;
        $totalrow=43;
        PDF::SetFont('helvetica', 'B', 10);
        // Header
        PDF::SetLineWidth(0);
        PDF::Cell('180', 5, 'Ketersediaan Sekolah dan Penduduk Usia Sekolah Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        //         PDF::Cell('180', 5, 'atas Dasar Harga Berlaku', 0, 0, 'C', 0);
        //         PDF::Ln();
        //         $countrow ++;
        PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        // set font
        PDF::SetFont('helvetica', '', 6);
        // column titles
        $header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
        $header1 = array('No','Kecamatan / Uraian','orang','orang','orang','orang','orang');
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0.1);
        
        //Header
        //$v1=20;
        //$v2=30;
        
        
        
        $Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=8
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
        
        // Header Column
        $wh = array(7,30,30,30,30,30,30);
        $wh1 = array(7,30,30,30,30,30,30);
        $w = array(7,30,30,30,30,30,30);
        $w1 = array(7,3,27,30,30,30,30,30);
        
        
        PDF::SetFillColor(225, 225, 225);
        PDF::SetTextColor(0);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        PDF::SetFont('helvetica', 'B', 8);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        $num_headers1 = count($header1);
        for($i = 0; $i < $num_headers1; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        // Color and font restoration
        
        //PDF::SetLineStyle(1);
        PDF::SetFont('helvetica', '', 7);
        // Data
        
        foreach($Kecamatan as $row) {
            $height=5;
            //   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
            //   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
            //   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
            //   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
            PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
            // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
            PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
            
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
            $Tingkat = DB::select(' select c.nama_kolom
from trx_isian_data_dasar a
inner join ref_kolom_tabel_dasar b
on a.id_kolom_tabel_dasar=b.id_kolom_tabel_dasar
inner  join ref_kolom_tabel_dasar c
on b.parent_id=c.id_kolom_tabel_dasar
                
            where  c.level=0 and a.tahun='.$tahun.' and b.id_tabel_dasar=8 and a.id_kecamatan='.$row->id_kecamatan);
            foreach($Tingkat as $row2) {
                //$height=5;
                $height=ceil((strlen($row2->nama_kolom)/20))*4;
                PDF::MultiCell($w1[0], $height, '', 1, 'C', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[4], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[5], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[6], $height, '', 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[7], $height, '', 1, 'R', 0, 0);
                
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
                $Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=8 and a.id_kecamatan='.$row->id_kecamatan);
                foreach($Pdrb as $row3) {
                    //$height=5;
                    $height=ceil((strlen($row3->nama_kolom)/20))*4;
                    PDF::MultiCell($w1[0], $height, $row3->no_urut, 1, 'C', 0, 0);
                    PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                    PDF::MultiCell($w1[2], $height, $row3->nama_kolom, 'RTB', 'L', 0, 0);
                    PDF::MultiCell($w1[3], $height, number_format($row3->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[4], $height, number_format($row3->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[5], $height, number_format($row3->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[6], $height, number_format($row3->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
                    
                    PDF::MultiCell($w1[7], $height, number_format($row3->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
                    
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
            }
            
        }
        PDF::Output('CetakGuruMurid.pdf', 'I');
    }
    
    public function printInvestor($tahun)
    {
        
        //return $id_aktiv;
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('ASB Komponen');
        
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
        
        $pemda=session::get('xPemda');
        $tahun_awal=$tahun-5;
        $tahun_akhir=$tahun-1;
        
        
        // add a page
        PDF::AddPage('P');
        $countrow=0;
        $totalrow=43;
        PDF::SetFont('helvetica', 'B', 10);
        // Header
        PDF::SetLineWidth(0);
        PDF::Cell('180', 5, 'Jumlah Investor PMDN/PMA Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        //         PDF::Cell('180', 5, 'atas Dasar Harga Berlaku', 0, 0, 'C', 0);
        //         PDF::Ln();
        //         $countrow ++;
        PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        // set font
        PDF::SetFont('helvetica', '', 6);
        // column titles
        $header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
        $header1 = array('No','Kecamatan / Uraian','orang','orang','orang','orang','orang');
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0.1);
        
        //Header
        //$v1=20;
        //$v2=30;
        
        
        
        $Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=9
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
        
        // Header Column
        $wh = array(7,30,30,30,30,30,30);
        $wh1 = array(7,30,30,30,30,30,30);
        $w = array(7,30,30,30,30,30,30);
        $w1 = array(7,3,27,30,30,30,30,30);
        
        
        PDF::SetFillColor(225, 225, 225);
        PDF::SetTextColor(0);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        PDF::SetFont('helvetica', 'B', 8);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        $num_headers1 = count($header1);
        for($i = 0; $i < $num_headers1; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        // Color and font restoration
        
        //PDF::SetLineStyle(1);
        PDF::SetFont('helvetica', '', 7);
        // Data
        
        foreach($Kecamatan as $row) {
            $height=5;
            //   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
            //   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
            //   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
            //   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
            PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
            // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
            PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
            
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
            $Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=9 and a.id_kecamatan='.$row->id_kecamatan);
            foreach($Pdrb as $row2) {
                //$height=5;
                $height=ceil((strlen($row2->nama_kolom)/20))*4;
                PDF::MultiCell($w1[0], $height, $row2->no_urut, 1, 'C', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, number_format($row2->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[4], $height, number_format($row2->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[5], $height, number_format($row2->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[6], $height, number_format($row2->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[7], $height, number_format($row2->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
                
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
        }
        PDF::Output('CetakInvestor.pdf', 'I');
    }
    
    public function printInvestasi($tahun)
    {
        
        //return $id_aktiv;
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('ASB Komponen');
        
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
        
        $pemda=session::get('xPemda');
        $tahun_awal=$tahun-5;
        $tahun_akhir=$tahun-1;
        
        
        // add a page
        PDF::AddPage('P');
        $countrow=0;
        $totalrow=43;
        PDF::SetFont('helvetica', 'B', 10);
        // Header
        PDF::SetLineWidth(0);
        PDF::Cell('180', 5, 'Jumlah Investasi PMDN/PMA Tahun '.$tahun_awal.' dan '.$tahun_akhir, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        //         PDF::Cell('180', 5, 'atas Dasar Harga Berlaku', 0, 0, 'C', 0);
        //         PDF::Ln();
        //         $countrow ++;
        PDF::Cell('180', 5,$pemda, 0, 0, 'C', 0);
        PDF::Ln();
        $countrow ++;
        // set font
        PDF::SetFont('helvetica', '', 6);
        // column titles
        $header=array('','','(n-5)','(n-4)','(n-3)','(n-2)','(n-1)');
        $header1 = array('No','Kecamatan / Uraian','orang','orang','orang','orang','orang');
        
        // Colors, line width and bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0.1);
        
        //Header
        //$v1=20;
        //$v2=30;
        
        
        
        $Kecamatan = DB::select('select b.nama_kecamatan, b.id_kecamatan from trx_isian_data_dasar a
            inner join ref_kecamatan b
            on a.id_kecamatan=b.id_kecamatan
            inner join ref_kolom_tabel_dasar c
            on a.id_kolom_tabel_dasar=c.id_kolom_tabel_dasar
            where a.tahun='.$tahun.'  and c.id_tabel_dasar=10
            GROUP BY b.nama_kecamatan, b.id_kecamatan');
        
        // Header Column
        $wh = array(7,30,30,30,30,30,30);
        $wh1 = array(7,30,30,30,30,30,30);
        $w = array(7,30,30,30,30,30,30);
        $w1 = array(7,3,27,30,30,30,30,30);
        
        
        PDF::SetFillColor(225, 225, 225);
        PDF::SetTextColor(0);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        PDF::SetFont('helvetica', 'B', 8);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 'LRT', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh[$i], 7, $header[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        $num_headers1 = count($header1);
        for($i = 0; $i < $num_headers1; ++$i) {
            if($i<2)
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 'LRB', 'C', 1,0);
            }
            else
            {
                PDF::MultiCell($wh1[$i], 7, $header1[$i], 1, 'C', 1,0);
            }
        }
        PDF::Ln();
        $countrow++;
        // Color and font restoration
        
        //PDF::SetLineStyle(1);
        PDF::SetFont('helvetica', '', 7);
        // Data
        
        foreach($Kecamatan as $row) {
            $height=5;
            //   		    $height=ceil(((strlen($row4->uraian_tarif_ssh.' - '.$row4->jenis_biaya)/10)
            //   		        +(strlen(number_format($row4->koefisien1,4,',','.').' '.$row4->satuan1)/11)
            //   		        +(strlen(number_format($row4->koefisien2,4,',','.').' '.$row4->satuan2)/11)
            //   		        +(strlen(number_format($row4->koefisien3,4,',','.').' '.$row4->satuan3)/11))/4)*3;
            PDF::MultiCell($w[0], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 0);
            // PDF::MultiCell($w[1], $height, $row->nama_kecamatan, 1, 'L', 0, 1, '', '', true);
            PDF::MultiCell($w[2], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[3], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[4], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
            PDF::MultiCell($w[6], $height, '', 1, 'L', 0, 0);
            
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
            $Pdrb = DB::select('SELECT (@id:=@id+1) as no_urut, b.nama_kolom,
            nmin1,
            nmin2,
            nmin3,
            nmin4,
            nmin5,
            nmin1_persen,
            nmin2_persen,
            nmin3_persen,
            nmin4_persen,
            nmin5_persen
            from
            trx_isian_data_dasar a
            inner join ref_kolom_tabel_dasar b
            on a.id_kolom_tabel_dasar = b.id_kolom_tabel_dasar
            , (SELECT @id:=0) x
            where a.tahun='.$tahun.' and b.id_tabel_dasar=10 and a.id_kecamatan='.$row->id_kecamatan);
            foreach($Pdrb as $row2) {
                //$height=5;
                $height=ceil((strlen($row2->nama_kolom)/20))*4;
                PDF::MultiCell($w1[0], $height, $row2->no_urut, 1, 'C', 0, 0);
                PDF::MultiCell($w1[1], $height, '', 'LTB', 'L', 0, 0);
                PDF::MultiCell($w1[2], $height, $row2->nama_kolom, 'RTB', 'L', 0, 0);
                PDF::MultiCell($w1[3], $height, number_format($row2->nmin5, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[4], $height, number_format($row2->nmin4, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[5], $height, number_format($row2->nmin3, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[6], $height, number_format($row2->nmin2, 2, ',', '.'), 1, 'R', 0, 0);
                
                PDF::MultiCell($w1[7], $height, number_format($row2->nmin1, 2, ',', '.'), 1, 'R', 0, 0);
                
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
        }
        PDF::Output('CetakInvestasi.pdf', 'I');
    }
    
    
    
}
