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
use App\Models\RefSshGolongan;
use App\Models\RefSshKelompok;
use App\Models\RefSshSubKelompok;
use App\Models\refsshtarif;
use App\Models\RefSshRekening;
use App\Models\RefRek5;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;


class CetakListASBRedundantController extends Controller
{

    public function printDuplikasiASB($perkada)
  {
  

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
    PDF::AddPage();

    // column titles
    $header = array('Kode','Aktivitas','Satuan 1','Derivatif 1','Kapasitas 1','Range 1','Satuan 2','Derivatif 2','Kapasitas 2','Range 2');

    // Colors, line width and bold font
    PDF::SetFillColor(200, 200, 200);
    PDF::SetTextColor(0);
    PDF::SetDrawColor(255, 255, 255);
    PDF::SetLineWidth(0);
    $fill = 0;
    $countrow=0;
    $totalrow=43;
    $ASBAktivitas = DB::select('select nomor_perkada,tahun_berlaku,tanggal_perkada from trx_asb_perkada
where id_asb_perkada='.$perkada);
    
    PDF::SetFont('helvetica', 'B', 5);
    foreach($ASBAktivitas as $row) {
        PDF::Cell('143', '3', '', 0, 0, 'L', 0);
        PDF::Cell('15', '3', 'Nomor Perkada', 0, 0, 'L', 0);
        PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
        PDF::Cell('20', '3', $row->nomor_perkada . '  Tahun ' . $row->tahun_berlaku, 0, 0, 'L', 0);
        PDF::Ln();
        $countrow++;
        PDF::Cell('143', '3', '', 0, 0, 'L', 0);
        PDF::Cell('15', '3', 'Tanggal Perkada', 0, 0, 'L', 0);
        PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
        PDF::Cell('20', '3', date('d F Y', strtotime($row->tanggal_perkada)), 0, 0, 'L', 0);
        PDF::Ln();
        $countrow++;
        PDF::Ln();
        $countrow++;
    }
    PDF::SetFont('helvetica', 'B', 10);

    //Header
    PDF::Cell('180', 5, Session::get('xPemda'), 1, 0, 'C', 0);
    PDF::Ln();
    PDF::Cell('180', 5, 'CEK DUPLIKASI ANALISIS STANDAR BIAYA', 1, 0, 'C', 0);
    PDF::Ln();
    PDF::Ln();
    PDF::SetFont('', 'B');
    PDF::SetFont('helvetica', 'B', 6);
    
    // Header Column
    $wh = array('15','60','16','16','10','10','16','16','10','10');
  
    $w3 = array('15','60','16','16','10','10','16','16','10','10');
    $num_headers = count($header);
    PDF::SetFillColor(225, 225, 225);
    PDF::SetTextColor(0);
    PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
    for($i = 0; $i < $num_headers; ++$i) {
        PDF::MultiCell($wh[$i], 5, $header[$i],  'LRT', 'C', 1,0);
    }
    PDF::Ln();
    $countrow++;
        // Color and font restoration

    PDF::SetFillColor(224, 235, 255);
    PDF::SetTextColor(0);
    PDF::SetFont('helvetica', '', 6);
        // Data
    
    
    	        $ListAkt = DB::select('select distinct a.id_aktivitas_asb,
a.id_asb_sub_sub_kelompok,
f.id_asb_sub_kelompok,
g.id_asb_kelompok,
a.nm_aktivitas_asb,
a.volume_1,
ifnull(b.uraian_satuan,"-") as sat_1,
ifnull(d.singkatan_satuan,"-") as der_1,
case a.kapasitas_max when 1 then "N/A" else a.kapasitas_max end as kapasitas_max,
case a.range_max when 1 then "N/A" else a.range_max end as range_max,
a.volume_2,
ifnull(c.singkatan_satuan,"-") as sat_2,
ifnull(e.singkatan_satuan,"-") as der_2,
case a.kapasitas_max1 when 1 then "N/A" else a.kapasitas_max1 end as kapasitas_max1,
case a.range_max1 when 1 then "N/A" else a.range_max1 end as range_max1
 from trx_asb_aktivitas a
LEFT OUTER JOIN ref_satuan b 
on a.id_satuan_1=b.id_satuan
LEFT OUTER JOIN ref_satuan c 
on a.id_satuan_2=c.id_satuan
LEFT OUTER JOIN ref_satuan d 
on a.sat_derivatif_1=d.id_satuan
LEFT OUTER JOIN ref_satuan e 
on a.sat_derivatif_2=e.id_satuan
inner join trx_asb_sub_sub_kelompok f
on a.id_asb_sub_sub_kelompok=f.id_asb_sub_sub_kelompok
inner join trx_asb_sub_kelompok g
on f.id_asb_sub_kelompok=g.id_asb_sub_kelompok


Order BY 
a.nm_aktivitas_asb asc');
    	       
    	        foreach($ListAkt as $row4) {
    	            PDF::SetTextColor(0,0,0);
    	            $height=(ceil(strlen($row4->nm_aktivitas_asb)/60))*6;
    	            
    	            
    	            PDF::MultiCell($w3[0], $height, $row4->id_asb_kelompok.'.'.$row4->id_asb_sub_kelompok.'.'.$row4->id_asb_sub_sub_kelompok.'.'.$row4->id_aktivitas_asb,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[1], $height, $row4->nm_aktivitas_asb,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[2], $height, $row4->sat_1,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[3], $height, $row4->der_1,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[4], $height, $row4->kapasitas_max,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[5], $height, $row4->range_max,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[6], $height, $row4->sat_2,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[7], $height, $row4->der_2,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[8], $height, $row4->kapasitas_max1,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[9], $height, $row4->range_max1,1, 'L', 0, 0);
    	            
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
    				//$fill=!$fill;
    			
        }
    PDF::Cell(array_sum($w3), 0, '', 'T');

    // ---------------------------------------------------------

    // close and output PDF document
    PDF::Output('ListASB.pdf', 'I');
  }

}

