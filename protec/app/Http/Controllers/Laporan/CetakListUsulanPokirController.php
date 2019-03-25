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


class CetakListUsulanPokirController extends Controller
{

    public function printListUsulanPokir()
  {
  

    // set document information
    PDF::SetCreator('BPKP');
    PDF::SetAuthor('BPKP');
    PDF::SetTitle('Simd@Perencanaan');
    PDF::SetSubject('Usulan Pokir');

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
    PDF::AddPage('L');

    // column titles
    $header = array('No','Tanggal','Nama','Asal','Jabatan','No','Deskripsi Usulan','Volume (Satuan)','Nama Unit','No','Lokasi');

    // Colors, line width and bold font
    PDF::SetFillColor(200, 200, 200);
    PDF::SetTextColor(0);
    PDF::SetDrawColor(255, 255, 255);
    PDF::SetLineWidth(0);
    PDF::SetFont('helvetica', 'B', 10);

    //Header
    PDF::Cell('275', 5, Session::get('xPemda'), 1, 0, 'C', 0);
    PDF::Ln();
    PDF::Cell('275', 5, 'LIST USULAN POKIR', 1, 0, 'C', 0);
    PDF::Ln();
    PDF::Ln();
    PDF::SetFont('', 'B');
    PDF::SetFont('helvetica', 'B', 6);
    $fill = 0;
    $countrow=0;
    $totalrow=43;
    // Header Column
    $wh = array(5,20,25,20,20,5,60,25,35,5,55);
    $w = array(5,20,25,20,20,5,60,25,35,5,55);
//     $w1 = array('15','3','57','16','10','10','16','16','16','10','10');
//     $w2 = array('15','6','54','16','10','10','16','16','16','10','10');
//     $w3 = array('15','9','51','16','10','10','16','16','16','10','10');
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

    PDF::SetFillColor(225, 225, 225);
    PDF::SetTextColor(0);
    PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
    PDF::SetFont('helvetica', '', 6);
        // Data
    
    
    	        $ListPokir = DB::select('SELECT
a.tanggal_pengusul,
CASE asal_pengusul
WHEN 0 THEN "Fraksi"
                WHEN 1 THEN "Pimpinan"
                WHEN 2 THEN "Badan Musyawarah"
                WHEN 3 THEN "Komisi"
                WHEN 4 THEN "Badan Legislasi Daerah"
                WHEN 5 THEN "Badan Anggaran"
                WHEN 6 THEN "Badan Kehormatan"
                WHEN 7 THEN "Panitia Ad Hoc"
                ELSE "Kelangkapan Dewan Lainnya"
                END AS asal_pengusul,
				CASE jabatan_pengusul
                WHEN 0 THEN "Ketua"
                WHEN 1 THEN "Wakil Ketua"
                WHEN 2 THEN "Sekretaris"
                WHEN 3 THEN "Bendahara"
                WHEN 4 THEN "Anggotan"
                ELSE "Jabatan Lainnya"
                END AS jabatan_pengusul,
a.nama_pengusul,

cast(b.diskripsi_usulan as char) as deskripsi_usulan,
b.volume,
e.uraian_satuan,
d.nm_unit,
CONCAT("RT/RW ",c.rt,"/",c.rw, ", Desa ",f.nama_desa,", Kec. ",g.nama_kecamatan) AS alamat

FROM
trx_pokir AS a
LEFT OUTER  JOIN  trx_pokir_usulan AS b ON b.id_pokir = a.id_pokir
LEFT OUTER  JOIN  trx_pokir_lokasi AS c ON c.id_pokir_usulan = b.id_pokir_usulan
LEFT OUTER  JOIN  ref_unit AS d ON b.id_unit = d.id_unit
LEFT OUTER  JOIN  ref_satuan AS e ON b.id_satuan = e.id_satuan
LEFT OUTER  JOIN  ref_desa AS f ON c.id_kecamatan = f.id_kecamatan AND c.id_desa = f.id_desa
LEFT OUTER  JOIN  ref_kecamatan AS g ON f.id_kecamatan = g.id_kecamatan
WHERE a.id_tahun ='.Session::get('tahun'));
    	       
    	        foreach($ListPokir as $row4) {
    	            PDF::SetTextColor(0,0,0);
    	            $height=(ceil(((strlen($row4->deskripsi_usulan)/60)+(strlen($row4->nm_unit)/30))/2)*6);
    	            
    	            PDF::MultiCell($w[0], $height, '',1, 'L', 0, 0);
    	            PDF::MultiCell($w[1], $height, date('d F Y', strtotime($row4->tanggal_pengusul)),1, 'L', 0, 0);
    	            PDF::MultiCell($w[2], $height, $row4->asal_pengusul,1, 'L', 0, 0);
    	            PDF::MultiCell($w[3], $height, $row4->jabatan_pengusul,1, 'L', 0, 0);
    	            PDF::MultiCell($w[4], $height, $row4->nama_pengusul,1, 'L', 0, 0);
    	            PDF::MultiCell($w[5], $height, '', 1, 'L', 0, 0);
    	            PDF::MultiCell($w[6], $height, $row4->deskripsi_usulan, 1, 'L', 0, 0);
    	            PDF::MultiCell($w[7], $height, number_format($row4->volume, 2, ',', '.').' ('.$row4->uraian_satuan.')', 1, 'L', 0, 0);
    	            PDF::MultiCell($w[8], $height, $row4->nm_unit, 1, 'L', 0, 0);
    	            PDF::MultiCell($w[9], $height, '', 1, 'L', 0, 0);
    	            PDF::MultiCell($w[10], $height, $row4->alamat, 1, 'L', 0, 0);
    	            
    	            
    	            PDF::Ln();
    	            $countrow++;
    	            if($countrow>=$totalrow)
    	            {
    	                PDF::AddPage('L');
    	                $countrow=0;
    	                for($i = 0; $i < $num_headers; ++$i) {
    	                    PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
    	                }
    	                PDF::Ln();
    	                $countrow++;
    	            }
    				//$fill=!$fill;
    			
        }
    PDF::Cell(array_sum($w), 0, '', 'T');

    // ---------------------------------------------------------

    // close and output PDF document
    PDF::Output('ListUsulanPokir.pdf', 'I');
  }

    public function printListTLPokir()
  {
      
      
      // set document information
      PDF::SetCreator('BPKP');
      PDF::SetAuthor('BPKP');
      PDF::SetTitle('Simd@Perencanaan');
      PDF::SetSubject('Tindak Lanjut Pokir');
      
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
      PDF::AddPage('L');
      
      // column titles
      $header = array('No','Tanggal','Nama','Deskripsi Usulan','Volume (Satuan)','Unit Usulan','Unit Review Bappeda','Lokasi','Status','Posting');
      
      // Colors, line width and bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      PDF::SetFont('helvetica', 'B', 10);
      
      //Header
      PDF::Cell('275', 5, Session::get('xPemda'), 1, 0, 'C', 0);
      PDF::Ln();
      PDF::Cell('275', 5, 'LIST REVIEW POKIR OLEH BAPPEDA', 1, 0, 'C', 0);
      PDF::Ln();
      PDF::Ln();
      PDF::SetFont('', 'B');
      PDF::SetFont('helvetica', 'B', 6);
      $fill = 0;
      $countrow=0;
      $totalrow=43;
      // Header Column
      $wh = array(5,20,25,60,25,35,35,45,12,13);
      $w = array(5,20,25,60,25,35,35,45,12,13);
      //     $w1 = array('15','3','57','16','10','10','16','16','16','10','10');
      //     $w2 = array('15','6','54','16','10','10','16','16','16','10','10');
      //     $w3 = array('15','9','51','16','10','10','16','16','16','10','10');
      PDF::SetFillColor(225, 225, 225);
      PDF::SetTextColor(0);
      PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
      
      $num_headers = count($header);
      for($i = 0; $i < $num_headers; ++$i) {
          PDF::MultiCell($wh[$i], 5, $header[$i],  'LRT', 'C', 1,0);
      }
      PDF::Ln();
      $countrow++;
      // Color and font restoration
      
      PDF::SetFillColor(225, 225, 225);
      PDF::SetTextColor(0);
      PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
      PDF::SetFont('helvetica', '', 6);
      // Data
      
      
      $ListPokir = DB::select('SELECT
a.tanggal_pengusul,
a.nama_pengusul,

cast(b.diskripsi_usulan as char) as deskripsi_usulan,
b.volume,
e.uraian_satuan,
d.nm_unit as unit_usulan,
i.nm_unit as unit_review,
CONCAT("RT/RW ",c.rt,"/",c.rw, ", Desa ",f.nama_desa,", Kec. ",g.nama_kecamatan) AS alamat,
case h.status_tl 
when 1 then "Disposisi Ke Unit"
when 2 then "Dipending"
when 3 then "Perlu Dibahas Kembali"
when 4 then "Tidak Diakomodir"
else "Belum TL" end as status_tl,
case h.status_data
when 1 then "Ya"
else "Tidak" end as status_posting

FROM
trx_pokir AS a
INNER  JOIN  trx_pokir_usulan AS b ON b.id_pokir = a.id_pokir
INNER  JOIN  trx_pokir_lokasi AS c ON c.id_pokir_usulan = b.id_pokir_usulan
INNER JOIN trx_pokir_tl AS h ON c.id_pokir_lokasi=h.id_pokir_lokasi and b.id_pokir_usulan=h.id_pokir_usulan and b.id_pokir=h.id_pokir
LEFT OUTER  JOIN  ref_unit AS d ON b.id_unit = d.id_unit
LEFT OUTER  JOIN  ref_satuan AS e ON b.id_satuan = e.id_satuan
LEFT OUTER  JOIN  ref_desa AS f ON c.id_kecamatan = f.id_kecamatan AND c.id_desa = f.id_desa
LEFT OUTER  JOIN  ref_kecamatan AS g ON f.id_kecamatan = g.id_kecamatan
LEFT OUTER  JOIN  ref_unit AS i ON h.unit_tl = i.id_unit
WHERE a.id_tahun ='.Session::get('tahun'));
      
      foreach($ListPokir as $row4) {
          PDF::SetTextColor(0,0,0);
          $height=(ceil(((strlen($row4->deskripsi_usulan)/60)+(strlen($row4->unit_usulan)/30)+(strlen($row4->unit_review)/30))/3)*6);
          
          PDF::MultiCell($w[0], $height, '',1, 'L', 0, 0);
          PDF::MultiCell($w[1], $height, date('d F Y', strtotime($row4->tanggal_pengusul)),1, 'L', 0, 0);
          PDF::MultiCell($w[2], $height, $row4->nama_pengusul,1, 'L', 0, 0);
          PDF::MultiCell($w[3], $height, $row4->deskripsi_usulan,1, 'L', 0, 0);
          PDF::MultiCell($w[4], $height, number_format($row4->volume, 2, ',', '.').' ('.$row4->uraian_satuan.')',1, 'L', 0, 0);
          PDF::MultiCell($w[5], $height, $row4->unit_usulan, 1, 'L', 0, 0);
          PDF::MultiCell($w[6], $height, $row4->unit_review, 1, 'L', 0, 0);
          PDF::MultiCell($w[7], $height, $row4->alamat, 1, 'L', 0, 0);
          PDF::MultiCell($w[8], $height, $row4->status_tl, 1, 'L', 0, 0);
          PDF::MultiCell($w[9], $height, $row4->status_posting, 1, 'L', 0, 0);
          
          
          
          PDF::Ln();
          $countrow++;
          if($countrow>=$totalrow)
          {
              PDF::AddPage('L');
              $countrow=0;
              for($i = 0; $i < $num_headers; ++$i) {
                  PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
              }
              PDF::Ln();
              $countrow++;
          }
          //$fill=!$fill;
          
      }
      PDF::Cell(array_sum($w), 0, '', 'T');
      
      // ---------------------------------------------------------
      
      // close and output PDF document
      PDF::Output('ListTLPokir.pdf', 'I');
  }
  
  public function printListTLUnitPokir()
  {
      
      
      // set document information
      PDF::SetCreator('BPKP');
      PDF::SetAuthor('BPKP');
      PDF::SetTitle('Simd@Perencanaan');
      PDF::SetSubject('Tindak Lanjut Pokir Oleh Unit');
      
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
      PDF::AddPage('L');
      
      // column titles
      $header = array('No','Tanggal','Nama','Deskripsi Usulan','Unit Review Bappeda','Aktivitas Renja/Forum','Lokasi','Status','Posting');
      
      // Colors, line width and bold font
      PDF::SetFillColor(200, 200, 200);
      PDF::SetTextColor(0);
      PDF::SetDrawColor(255, 255, 255);
      PDF::SetLineWidth(0);
      
      PDF::SetFont('helvetica', 'B', 10);
      
      //Header
      PDF::Cell('275', 5, Session::get('xPemda'), 1, 0, 'C', 0);
      PDF::Ln();
      PDF::Cell('275', 5, 'LIST TINDAK LANJUT POKIR OLEH UNIT', 1, 0, 'C', 0);
      PDF::Ln();
      PDF::Ln();
      PDF::SetFont('', 'B');
      PDF::SetFont('helvetica', 'B', 6);
      $fill = 0;
      $countrow=0;
      $totalrow=43;
      // Header Column
      $wh = array(5,20,25,60,35,35,45,14,11);
      $w = array(5,20,25,60,35,35,45,14,11);
      //     $w1 = array('15','3','57','16','10','10','16','16','16','10','10');
      //     $w2 = array('15','6','54','16','10','10','16','16','16','10','10');
      //     $w3 = array('15','9','51','16','10','10','16','16','16','10','10');
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
      
     
      PDF::SetFont('helvetica', '', 6);
      // Data
      
      
      $ListPokir = DB::select('SELECT
a.tanggal_pengusul,
a.nama_pengusul,

cast(b.diskripsi_usulan as char) as deskripsi_usulan,
b.volume,
e.uraian_satuan,
i.nm_unit as unit_pelaksana,
CONCAT("RT/RW ",c.rt,"/",c.rw, ", Desa ",f.nama_desa,", Kec. ",g.nama_kecamatan) AS alamat,
case j.status_tl 
when 1 then "Diakomodir Renja"
when 2 then "Diakomodir Forum"
when 3 then "Tidak diakomodir"
else "Belum TL" end as status_tl,
case j.status_data
when 1 then "Ya"
else "Tidak" end as status_posting,
case j.status_tl 
when 1 then ifnull(k.uraian_aktivitas_kegiatan,"-")
when 2 then ifnull(l.uraian_aktivitas_kegiatan,"-")
else "-" end as uraian_aktivitas_kegiatan 




FROM
trx_pokir AS a
INNER  JOIN  trx_pokir_usulan AS b ON b.id_pokir = a.id_pokir
INNER  JOIN  trx_pokir_lokasi AS c ON c.id_pokir_usulan = b.id_pokir_usulan
INNER JOIN trx_pokir_tl AS h ON c.id_pokir_lokasi=h.id_pokir_lokasi and b.id_pokir_usulan=h.id_pokir_usulan and b.id_pokir=h.id_pokir
INNER JOIN trx_pokir_tl_unit AS j ON h.id_pokir_tl=j.id_pokir_tl and c.id_pokir_lokasi=j.id_pokir_lokasi and b.id_pokir_usulan=j.id_pokir_usulan and b.id_pokir=j.id_pokir
LEFT OUTER  JOIN  ref_unit AS d ON b.id_unit = d.id_unit
LEFT OUTER  JOIN  ref_satuan AS e ON b.id_satuan = e.id_satuan
LEFT OUTER  JOIN  ref_desa AS f ON c.id_kecamatan = f.id_kecamatan AND c.id_desa = f.id_desa
LEFT OUTER  JOIN  ref_kecamatan AS g ON f.id_kecamatan = g.id_kecamatan
LEFT OUTER  JOIN  ref_unit AS i ON h.unit_tl = i.id_unit
LEFT OUTER JOIN trx_renja_rancangan_aktivitas AS k on j.id_aktivitas_renja=k.id_aktivitas_renja
LEFT OUTER JOIN trx_forum_skpd_aktivitas AS l on j.id_aktivitas_forum=l.id_aktivitas_forum
WHERE a.id_tahun ='.Session::get('tahun'));
      
      foreach($ListPokir as $row4) {
          PDF::SetTextColor(0,0,0);
          $height=(ceil(((strlen($row4->deskripsi_usulan)/60)+(strlen($row4->unit_pelaksana)/30)+(strlen($row4->uraian_aktivitas_kegiatan)/30))/3)*6);
          
          PDF::MultiCell($w[0], $height, '',1, 'L', 0, 0);
          PDF::MultiCell($w[1], $height, date('d F Y', strtotime($row4->tanggal_pengusul)),1, 'L', 0, 0);
          PDF::MultiCell($w[2], $height, $row4->nama_pengusul,1, 'L', 0, 0);
          PDF::MultiCell($w[3], $height, $row4->deskripsi_usulan,1, 'L', 0, 0);
          PDF::MultiCell($w[4], $height, $row4->unit_pelaksana,1, 'L', 0, 0);
          PDF::MultiCell($w[5], $height, $row4->uraian_aktivitas_kegiatan, 1, 'L', 0, 0);
          PDF::MultiCell($w[6], $height, $row4->alamat, 1, 'L', 0, 0);
          PDF::MultiCell($w[7], $height, $row4->status_tl, 1, 'L', 0, 0);
          PDF::MultiCell($w[8], $height, $row4->status_posting, 1, 'L', 0, 0);
          
          
          
          PDF::Ln();
          $countrow++;
          if($countrow>=$totalrow)
          {
              PDF::AddPage('L');
              $countrow=0;
              for($i = 0; $i < $num_headers; ++$i) {
                  PDF::Cell($wh[$i], 7, $header[$i], 1, 0, 'C', 1);
              }
              PDF::Ln();
              $countrow++;
          }
          //$fill=!$fill;
          
      }
      PDF::Cell(array_sum($w), 0, '', 'T');
      
      // ---------------------------------------------------------
      
      // close and output PDF document
      PDF::Output('ListTLUnitPokir.pdf', 'I');
  }
  
}

