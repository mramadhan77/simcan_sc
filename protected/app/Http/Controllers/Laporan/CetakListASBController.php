<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Response;
use Session;
use PDF;
use DB;
use App\Http\Controllers\Laporan\TemplateReport As Template;
use App\Models\RefSshGolongan;
use App\Models\RefSshKelompok;
use App\Models\RefSshSubKelompok;
use App\Models\refsshtarif;
use App\Models\RefSshRekening;
use App\Models\RefRek5;

class CetakListASBController extends Controller
{

	public function printListASB($perkada)
  {
  
    $fill = 0;
    $countrow=0;
    $totalrow=43;

    Template::settingPagePotrait();
    Template::headerPotrait();
    $countrow++;
    $judul_laporan = 'DAFTAR ANALISIS STANDAR BIAYA';
    Template::judulReport($judul_laporan);   
    $countrow++;

    // // set document information
    // PDF::SetCreator('BPKP');
    // PDF::SetAuthor('BPKP');
    // PDF::SetTitle('Simd@Perencanaan');
    // PDF::SetSubject('SSH Kelompok');

    // // set default header data
    // PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
    // PDF::SetFooterData(array(0,64,0), array(0,64,128));

    // // set header and footer fonts
    // PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    // PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // // set default monospaced font
    // PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // // set margins
    // PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    // PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
    // PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

    // // set auto page breaks
    // PDF::SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

    // // set image scale factor
    // // PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

    // // set some language-dependent strings (optional)
    // // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    // //     require_once(dirname(__FILE__).'/lang/eng.php');
    // //     $pdf->setLanguageArray($l);
    // // }

    // // ---------------------------------------------------------

    // // set font
    // PDF::SetFont('helvetica', '', 6);

    // // add a page
    // PDF::AddPage();

    // column titles
    $header = array('Kode','Kelompok/Sub/Sub-Sub/Aktivitas','Pemicu 1','Derivatif 1','Kap Max 1','Range 1','Pemicu 2','Derivatif 2','Kap Max 2','Range 2','Status');

    // Colors, line width and bold font
    PDF::SetFillColor(200, 200, 200);
    PDF::SetTextColor(0);
    PDF::SetDrawColor(255, 255, 255);
    PDF::SetLineWidth(0);

    
   
    $ASBAktivitas = DB::Select('Select nomor_perkada,tahun_berlaku,tanggal_perkada from trx_asb_perkada where id_asb_perkada='.$perkada);
    
    PDF::SetFont('helvetica', 'B', 5);
    foreach($ASBAktivitas as $row) {
        PDF::Cell('138', '3', '', 0, 0, 'L', 0);
        PDF::Cell('15', '3', 'Nomor Perkada', 0, 0, 'L', 0);
        PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
        PDF::Cell('20', '3', $row->nomor_perkada . '  Tahun ' . $row->tahun_berlaku, 0, 0, 'L', 0);
        PDF::Ln();
        $countrow++;
        PDF::Cell('138', '3', '', 0, 0, 'L', 0);
        PDF::Cell('15', '3', 'Tanggal Perkada', 0, 0, 'L', 0);
        PDF::Cell('2', '3', ':', 0, 0, 'L', 0);
        PDF::Cell('20', '3', date('d F Y', strtotime($row->tanggal_perkada)), 0, 0, 'L', 0);
        PDF::Ln();
        $countrow++;
        PDF::Ln();
        $countrow++;
    }
    // //Header
    // PDF::SetFont('helvetica', 'B', 10);
    // PDF::Cell('180', 5, Session::get('xPemda'), 1, 0, 'C', 0);
    // PDF::Ln();
    // PDF::Cell('180', 5, 'DAFTAR ANALISIS STANDAR BIAYA', 1, 0, 'C', 0);
    // PDF::Ln();
    // PDF::Ln();
    // PDF::SetFont('', 'B');
    // PDF::SetFont('helvetica', 'B', 6);
   
    // Header Column
    $wh = array('12','53','16','16','10','10','16','16','10','10','10');
    $w  = array('12','167');
    $w1 = array('12','3','164');
    $w2 = array('12','6','161');
    $w3 = array('12','9','44','16','16','10','10','16','16','10','10','10');

    $num_headers = count($header);
    PDF::SetFillColor(225, 225, 225);
    PDF::SetTextColor(0);
    PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
    for($i = 0; $i < $num_headers; ++$i) {
        PDF::MultiCell($wh[$i], 8, $header[$i],  1, 'C', 1, 0, '', '', true, 1, false, true, 8 , 'M');
    }
    PDF::Ln();
    $countrow++;
        // Color and font restoration

    PDF::SetFillColor(224, 235, 255);
    PDF::SetTextColor(0);
    PDF::SetFont('helvetica', '', 6);
        // Data
    
    $ListKelompok = DB::select('SELECT (@id:=@id+1) as no_urut, id_asb_kelompok, uraian_kelompok_asb from trx_asb_kelompok,  (SELECT @id:=0) x
                    where id_asb_perkada='.$perkada);
    foreach($ListKelompok as $row) {
        $height=ceil((PDF::GetStringWidth($row->uraian_kelompok_asb)/$w[1]))*3;

        PDF::SetTextColor(0,0,0);
        PDF::MultiCell($w[0], $height, $row->no_urut,'LRB', 'L', 0, 0);
        PDF::MultiCell($w[1], $height, $row->uraian_kelompok_asb,'LRB', 'L', 0, 0);
        // PDF::MultiCell($w[2], $height, '','LRB', 'L', 0, 0);
        // PDF::MultiCell($w[3], $height, '','LRB', 'L', 0, 0);
        // PDF::MultiCell($w[4], $height, '','LRB', 'L', 0, 0);
        // PDF::MultiCell($w[5], $height, '','LRB', 'L', 0, 0);
        // PDF::MultiCell($w[6], $height, '','LRB', 'L', 0, 0);
        // PDF::MultiCell($w[7], $height, '','LRB', 'L', 0, 0);
        // PDF::MultiCell($w[8], $height, '','LRB', 'L', 0, 0);
        // PDF::MultiCell($w[9], $height, '','LRB', 'L', 0, 0);
        // PDF::MultiCell($w[10], $height, '','LRB', 'L', 0, 0);
        
    	
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
    	$ListSub = DB::select('Select (@id:=@id+1) as no_urut, id_asb_kelompok,id_asb_sub_kelompok,uraian_sub_kelompok_asb from trx_asb_sub_kelompok,  (SELECT @id:=0) x
                    where id_asb_kelompok='.$row->id_asb_kelompok);
    	foreach($ListSub as $row2) {
            $height=ceil((PDF::GetStringWidth($row2->uraian_sub_kelompok_asb)/$w1[2]))*3;

    	    PDF::SetTextColor(0,0,0);
    	    PDF::MultiCell($w1[0], $height, $row->no_urut.'.'.$row2->no_urut,1, 'L', 0, 0);
    	    PDF::MultiCell($w1[1], $height, '','LTB', 'L', 0, 0);
    	    PDF::MultiCell($w1[2], $height, $row2->uraian_sub_kelompok_asb,'RTB', 'L', 0, 0);
    	   
    	    
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

    	    $ListSubSub = DB::select('Select (@id:=@id+1) as no_urut, id_asb_sub_kelompok,id_asb_sub_sub_kelompok,uraian_sub_sub_kelompok_asb from trx_asb_sub_sub_kelompok,  (SELECT @id:=0) x
                        where id_asb_sub_kelompok='.$row2->id_asb_sub_kelompok);
    	    foreach($ListSubSub as $row3) {
                $height=ceil((PDF::GetStringWidth($row3->uraian_sub_sub_kelompok_asb)/$w2[2]))*3;

    	        PDF::SetTextColor(0,0,0);
    	        PDF::MultiCell($w2[0], $height, $row->no_urut.'.'.$row2->no_urut.'.'.$row3->no_urut,1, 'L', 0, 0);
    	        PDF::MultiCell($w2[1], $height, '','LTB', 'L', 0, 0);
    	        PDF::MultiCell($w2[2], $height, $row3->uraian_sub_sub_kelompok_asb,'RTB', 'L', 0, 0);
    	        
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
    	        $ListAkt = DB::select('SELECT (@id:=@id+1) as no_urut, y.* FROM (select a.id_aktivitas_asb, a.id_asb_sub_sub_kelompok, a.nm_aktivitas_asb, a.volume_1, a.id_satuan_1,
                            CASE COALESCE(a.id_satuan_1,999999)
                                      WHEN 0 THEN "Tidak Digunakan"
                                      WHEN -1 THEN "Belum Ditentukan"
                                      WHEN 999999 THEN "Kosong"
                                    ELSE b.uraian_satuan  END as satuan1,
                            ifnull(b.uraian_satuan,"-") as sat_1, 
                            a.sat_derivatif_1,
                                    CASE COALESCE(a.sat_derivatif_1,999999)
                                      WHEN 0 THEN "Tidak Digunakan"
                                      WHEN -1 THEN "Belum Ditentukan"
                                      WHEN 999999 THEN "Kosong"
                                    ELSE d.uraian_satuan END AS uraian_derivatif_1,
                            ifnull(d.singkatan_satuan,"-") as der_1, case when a.kapasitas_max <=1 then "No Limit" else a.kapasitas_max end as kapasitas_max,
                            case when a.range_max <=1 then "No Limit" else a.range_max end as range_max, a.volume_2, a.id_satuan_2,
                            CASE COALESCE(a.id_satuan_2,999999)
                                      WHEN 0 THEN "Tidak Digunakan"
                                      WHEN -1 THEN "Belum Ditentukan"
                                      WHEN 999999 THEN "Kosong"
                                    ELSE c.uraian_satuan  END as satuan2,
                                            ifnull(c.singkatan_satuan,"-") as sat_2,
                                    a.sat_derivatif_2, 
                                    CASE COALESCE(a.sat_derivatif_2,999999)
                                      WHEN 0 THEN "Tidak Digunakan"
                                      WHEN -1 THEN "Belum Ditentukan"
                                      WHEN 999999 THEN "Kosong"
                                    ELSE e.uraian_satuan END AS uraian_derivatif_2,
                            ifnull(e.singkatan_satuan,"-") as der_2, case when a.kapasitas_max1 <=1 then "No Limit" else a.kapasitas_max1 end as kapasitas_max1,
                            case when a.range_max1 <=1 then "No Limit" else a.range_max1 end as range_max1, count(DISTINCT f.id_komponen_asb) as COUNT, count(DISTINCT g.id_komponen_asb) as COUNT1
                            FROM trx_asb_aktivitas a
                            LEFT OUTER JOIN ref_satuan b ON a.id_satuan_1=b.id_satuan
                            LEFT OUTER JOIN ref_satuan c ON a.id_satuan_2=c.id_satuan
                            LEFT OUTER JOIN ref_satuan d ON a.sat_derivatif_1=d.id_satuan
                            LEFT OUTER JOIN ref_satuan e ON a.sat_derivatif_2=e.id_satuan
                            LEFT OUTER JOIN trx_asb_komponen f ON a.id_aktivitas_asb=f.id_aktivitas_asb
                            LEFT Outer JOIN trx_asb_komponen_rinci g ON f.id_komponen_asb=g.id_komponen_asb
                            WHERE id_asb_sub_sub_kelompok='.$row3->id_asb_sub_sub_kelompok.'
                            GROUP BY a.id_aktivitas_asb, a.id_asb_sub_sub_kelompok, a.nm_aktivitas_asb, a.volume_1, sat_1, der_1, kapasitas_max, range_max, a.volume_2, sat_2, der_2,a.id_satuan_1, a.id_satuan_2,
                            kapasitas_max1, range_max1, b.uraian_satuan, c.uraian_satuan, d.uraian_satuan, e.uraian_satuan, a.sat_derivatif_1, a.sat_derivatif_2) y,  (SELECT @id:=0) x');
    	       
    	        foreach($ListAkt as $row4) {
                    $ASBValiditas = DB::select('SELECT n.id_aktivitas_asb, n.nm_aktivitas_asb, sum(n.validitas) as validitas,
                                    CASE WHEN sum(n.validitas) > 0 THEN "NON VALID" ELSE "OKE" END AS validitas_display
                                    FROM (SELECT m.*, CASE WHEN (m.cek_1 = 0 OR m.cek_2 = 0) THEN 1 ELSE 0 END AS validitas FROM 
                                    (SELECT a.jenis_biaya, a.hub_driver, a.id_satuan1, a.id_satuan2, c.id_satuan_1, c.id_satuan_2, c.sat_derivatif_1, 
                                    c.sat_derivatif_2, a.koefisien1, a.koefisien2, a.koefisien3, c.id_aktivitas_asb, c.nm_aktivitas_asb,
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
                                    WHERE n.id_aktivitas_asb = '.$row4->id_aktivitas_asb.'
                                    GROUP BY n.id_aktivitas_asb, n.nm_aktivitas_asb');

                    foreach($ASBValiditas as $row5) {
    	            PDF::SetTextColor(0,0,0);
                    if ($row4->COUNT==0||$row4->COUNT1<$row4->COUNT||$row5->validitas>0||$row4->range_max>$row4->kapasitas_max||$row4->range_max1>$row4->kapasitas_max1) {
                        PDF::SetTextColor(255,0,0);
                        $Status = "Tidak Valid";                    
                    } else {
                        PDF::SetTextColor(0,0,0);
                        $Status = "Valid"; 
                    }

                    $height1=ceil((PDF::GetStringWidth($row4->nm_aktivitas_asb)/$w3[2]))*3;
                    $height2=ceil((PDF::GetStringWidth($row4->satuan1)/$w3[3]))*3;
                    $height3=ceil((PDF::GetStringWidth($row4->uraian_derivatif_1)/$w3[4]))*3;
                    $height4=ceil((PDF::GetStringWidth($row4->satuan2)/$w3[7]))*3;
                    $height5=ceil((PDF::GetStringWidth($row4->uraian_derivatif_2)/$w3[8]))*3;
                    $height6=ceil((PDF::GetStringWidth($Status)/$w3[11]))*3;

                    $maxhigh =array($height1,$height2,$height3,$height4,$height5,$height6);
                    $height = max($maxhigh);                    

                    $tinggi = $height.' ('.$height1.','.$height2.','.$height3.','.$height4.','.$height5.','.$height6.')';

    	            PDF::MultiCell($w3[0], $height, $row->no_urut.'.'.$row2->no_urut.'.'.$row3->no_urut.'.'.$row4->no_urut,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[1], $height, '','LTB', 'L', 0, 0);
    	            PDF::MultiCell($w3[2], $height, $row4->nm_aktivitas_asb,'RTB', 'L', 0, 0);
    	            PDF::MultiCell($w3[3], $height, $row4->satuan1,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[4], $height, $row4->uraian_derivatif_1,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[5], $height, $row4->kapasitas_max,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[6], $height, $row4->range_max,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[7], $height, $row4->satuan2,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[8], $height, $row4->uraian_derivatif_2,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[9], $height, $row4->kapasitas_max1,1, 'L', 0, 0);
    	            PDF::MultiCell($w3[10], $height, $row4->range_max1,1, 'L', 0, 0);
                    PDF::MultiCell($w3[11], $height, $Status,1, 'L', 0, 0);
    	            
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
    			}
    			//$fill=!$fill;
    		}
    		//$fill=!$fill;
    	}
        }
    PDF::Cell(array_sum($w), 0, '', 'T');

    //Footer code here
    // PDF::SetFont('Courier', 'I', 8);
    // PDF::SetY(-15);
    // $wf = array('60','60','60');
    // PDF::MultiCell($wf[0], 10, 'Print by Simd@',  'T', 'L', 0, 0, '', '', true, 1, false, true, 10 , 'T');
    // PDF::MultiCell($wf[1], 10, '',  'T', 'C', 0, 0, '', '', true, 1, false, true, 10 , 'T');
    // PDF::MultiCell($wf[1], 10, 'Hal : ' . PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages(),  'T', 'R', 0, 0, '', '', true, 1, false, true, 10 , 'T');

    // ---------------------------------------------------------

    // close and output PDF document

    Template::footerPotrait();
    PDF::Output('ListASB.pdf', 'I');
  }

}

