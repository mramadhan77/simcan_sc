<?php
   
   //  ---------------------------------------------------------------------------------------------------  //
   //  | Title        : Template Laporan untuk Layout Page Potrait & Landscape SIMDA Perencanaan Tahunan |  //   
   //  | Author       : Viera Lestari Vahendra                                                           |  //  
   //  | NIP          : 19930504 201801 2 003                                                            |  //
   //  | Created Date : 10 APRIL 2018                                                                    |  //
   //  ---------------------------------------------------------------------------------------------------  //

// MultiCell($w, $h, $txt, $border=0, $align='J/L/R/C', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $vAlign='T/M/B')
     
   
   namespace App\Http\Controllers\Laporan;
      
   use PDF;
   use Session;

class TemplateReport{
   
      public static function settingPagePotrait(){
         PDF::SetCreator('BPKP');
         PDF::SetAuthor('BPKP');
         PDF::SetTitle('Simd@Perencanaan');

         PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
         PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, true);
         PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
         PDF::SetFooterMargin(0);  
         
         PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
         PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
         PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
         
         PDF::SetAutoPageBreak(TRUE, 0);   
         
         if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            PDF::setLanguageArray($l);
         }
         
         PDF::AddPage('P');
      }
      
      public static function settingPageLandscape(){
         PDF::SetCreator('BPKP');
         PDF::SetAuthor('BPKP');
         PDF::SetTitle('Simd@Perencanaan');

         PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
         PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, true);
         PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
         PDF::SetFooterMargin(PDF_MARGIN_FOOTER);  
         
         PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
         PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
         PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
         
         PDF::SetAutoPageBreak(TRUE, 10);
         
         if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            PDF::setLanguageArray($l);
         }
         
         PDF::AddPage('L');
      }
      
      
      public static function namaReport($nama_laporan = ''){
         $cetak_laporan = $nama_laporan.'.pdf';         
         PDF::Output($cetak_laporan, 'I');
      }
      
      
      public static function judulReport($judul_laporan = ''){
         PDF::SetLineWidth(0);
         PDF::SetFont('helvetica', 'B', 9);
         PDF::Cell(0, 5, $judul_laporan, 0, false, 'C', 0, '', 0, false, 'M', 'M');
         PDF::Ln(4); 
         PDF::SetFillColor(224, 224, 209);
         PDF::SetTextColor(0);
         PDF::SetFont('helvetica', '', 8);
      } 

         
      // ----------------------------------- POTRAIT ----------------------------------- // 
      
         public static function headerPotrait(){

            $nama_image  = 'default.png';
            $nama_pemda = 'PEMERINTAH ' .strtoupper(Session::get('xPemda'));
            $alamat_pemda     = Session::get('xAlamat'); 
            $kontak_pemda     = Session::get('xKontak'); 
            
            // $image_file = K_PATH_IMAGES.$nama_image; 
            
            $image_file     = 'vendor/default.png'; 
                        
            PDF::setJPEGQuality(75);
            PDF::setImageScale(PDF_IMAGE_SCALE_RATIO); 
            
            PDF::Image($image_file, 20, 10, 14, 18, 'PNG', '', 'T', true, 300, '', false, false, 0, false, false, false);
            // PDF::Image($image_file, 30, 10, 15, '', 'PNG', '', 'T', false, 200, '', false, false, 0, false, false, false); 

            // $tbl_header = '<table border="0" width="100%" align="center" table-layout="fixed">
            //         <thead>
            //           <tr>
            //               <th width="80" height="90" rowspan="4" style="text-align: center; vertical-align:middle"><img src="vendor/default.png"/></th>
            //               <th width="140" height="20" style="text-align: center; vertical-align:middle">'.$nama_pemda.'</th>
            //           </tr>
            //           <tr>
            //               <th width="140" height="20" style="text-align: center; vertical-align:middle">'.$alamat_pemda.'</th>
            //           </tr>
            //           <tr>
            //               <th width="140" height="20" style="text-align: center; vertical-align:middle">'.$kontak_pemda.'</th>
            //           </tr>
            //         </thead>
            //       </table>';

            PDF::Ln(3);    
            PDF::SetFont('helvetica', 'B', 12);
            PDF::Cell(0, 5, $nama_pemda, 0, false, 'C', 0, '', 0, false, 'M', 'M');
            PDF::Ln();          
            PDF::SetFont('helvetica', 'B', 8);
            PDF::Cell(0, 5, $alamat_pemda, 0, false, 'C', 0, '', 0, false, 'M', 'M');
            PDF::Ln();    
            PDF::Cell(0, 5, $kontak_pemda, 0, false, 'C', 0, '', 0, false, 'M', 'M');
            PDF::Ln(); 

            // PDF::writeHTML($tbl_header, true, false, true, false, '');   

            PDF::SetTextColor(0, 0, 0, 100);
            PDF::MultiCell($w = 180, $h = 100, $txt = '', $border = 'T', $align = 'L', $fill = 0, $ln = 0, $x = '', $y = '', $reseth = true);   
            PDF::Ln(1);  
            PDF::MultiCell($w = 180, $h = 100, $txt = '', $border = 'T', $align = 'L', $fill = 0, $ln = 0, $x = '', $y = '', $reseth = true);  
            PDF::Ln(5); 

         }
         
         
         public static function headerLaporanPotrait($judul_laporan = '', $nama_pemda = ''){
            PDF::SetY(15);
            PDF::SetFont('helvetica', 'I', 5);
            PDF::SetTextColor(100, 100, 100, 100);
            
            $w = 4; 
            
            PDF::MultiCell(90, $w, $judul_laporan, 'B', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
            PDF::MultiCell(90, $w, $nama_pemda, 'B', 'R', 0, 0, '', '', true, 0, false, true, $w, 'M');
            PDF::Ln(6);
            PDF::SetFont('helvetica', '', 8);
         }
         
         
         public static function footerPotrait(){
            $nama_aplikasi = "\t\t\t\t\t\t\t\t\t\t\t\t".'SIMDA Perencanaan';
            $tgl_cetak     = 'Tgl Cetak : '.date('d/m/Y');
            $page_number   = 'Page '.PDF::getAliasNumPage().' / '.PDF::getAliasNbPages();
      
            PDF::SetY(-15);
            PDF::SetFont('helvetica', 'I', 6);
            PDF::SetTextColor(0, 0, 0, 100);
            
            $w = 4;

            $style = array(
                'border' => 1,
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );

            PDF::MultiCell(135, $w, $tgl_cetak, 'T', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
            PDF::MultiCell(50, $w, $page_number, 'T', 'R', 0, 1, '', '', true, 0, false, true, $w, 'M');

            // PDF::write2DBarcode('simda_perencanaan', 'QRCODE,H', 20, 275, 15, 15, $style, 'N'); 
            
            // PDF::MultiCell(60, $w, $nama_aplikasi, 'T', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
            
            PDF::SetFont('helvetica', '', 8);
         }
      
      
      // -------------------------------- END OF POTRAIT -------------------------------- // 
      
      
      // ---------------------------------- LANDSCAPE ---------------------------------- //  
      
         public static function headerLandscape(){
             
            $nama_pemda = Session::get('xPemda');
            $alamat_pemda     = Session::get('xAlamat'); 
            $kontak_pemda     = Session::get('xKontak'); 

            // $nama_pemda = "PEMERINTAH KABUPATEN/KOTA SIMULASI";
            // $alamat_pemda = "Jalan Raya Simulasi Nomor 987 SIMULASI Provinsi SIMULASI, INDONESIA"; 
            // $kontak_pemda = "Telepon : 021-99999999 (Hunting) Web : http//www.simulasikab.go.id"; 

            PDF::SetY(5);
            $html_header = '';
            $html_header .= '<table cellpadding="0" cellspacing="0" style="border-bottom-style: double;">';
            $html_header .= '<tr>';
            $html_header .= '<th width="10%" rowspan="3" style="text-align: center;">
                                <img src="vendor/default.png" class="img-thumbnail" width="50" height="60" >
                            </th>';
            $html_header .= '<th width="90%" style="text-align: left; font-size:16px; font-weight: bold;">'.$nama_pemda.'</th>';
            $html_header .= '</tr>';
            $html_header .= '<tr>';
            $html_header .= '<th style="text-align: left; font-size:12px">'.$alamat_pemda.'</th>';
            $html_header .= '</tr>';
            $html_header .= '<tr>';
            $html_header .= '<th style="text-align: left; font-size:10px">'.$kontak_pemda.'</th>';
            $html_header .= '</tr>';
            $html_header .= '</table>';
            PDF::writeHTML($html_header, true, false, true, false, '');

         }
         
         
         public static function headerLaporanLandscape($judul_laporan = '', $nama_pemda = ''){
            PDF::SetY(15);
            PDF::SetFont('helvetica', 'I', 5);
            PDF::SetTextColor(100, 100, 100, 100);
            
            $w = 4; 
            
            PDF::MultiCell(132, $w, $judul_laporan, 'B', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
            PDF::MultiCell(132, $w, $nama_pemda, 'B', 'R', 0, 0, '', '', true, 0, false, true, $w, 'M');
            PDF::Ln(7);
            PDF::SetFont('helvetica', '', 8);
         }
         
         
         public static function footerLandscape(){
            $nama_aplikasi = "\t\t\t\t\t\t\t\t\t\t\t\t".'SIMDA Perencanaan';
            $tgl_cetak     = 'Tgl Cetak : '.date('d/m/Y');
            $page_number   = 'Page '.PDF::getAliasNumPage().' / '.PDF::getAliasNbPages();
      
            PDF::SetY(-15);
            PDF::SetFont('helvetica', 'I', 6);
            PDF::SetTextColor(100, 100, 100, 100);
            
            $w = 4; 
            
            // PDF::MultiCell(88, $w, $nama_aplikasi, 'T', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
            PDF::MultiCell(200, $w, $tgl_cetak, 'T', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
            PDF::MultiCell(75, $w, $page_number, 'T', 'R', 0, 1, '', '', true, 0, false, true, $w, 'M');
            
            PDF::SetFont('helvetica', '', 8);  
         }     
      
      
      // ------------------------------ END OF LANDSCAPE ------------------------------- // 
      
      
      // ------------------------------ SETTING TABLE ------------------------------- // 
        
         //2 kolom
         public static function MultiRow($left, $right) {
            
            $page_start = PDF::getPage();
            $y_start    = PDF::GetY();

            PDF::MultiCell(17, 6, $left, 1, 'L', 0, 2, '', '', true, 0);// write the left cell
            
            $page_end_1 = PDF::getPage();
            $y_end_1    = PDF::GetY();

            PDF::setPage($page_start);
            
            PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');//write the right cell    
                
            $page_end_2 = PDF::getPage();
            $y_end_2    = PDF::GetY();

            // set the new row position by case
            if(max($page_end_1,$page_end_2) == $page_start){
                $ynew = max($y_end_1, $y_end_2);
            }elseif ($page_end_1 == $page_end_2){
                $ynew = max($y_end_1, $y_end_2);
            }elseif ($page_end_1 > $page_end_2){
                $ynew = $y_end_1;
            }else{
                $ynew = $y_end_2;
            }

            PDF::setPage(max($page_end_1,$page_end_2));
            PDF::SetXY(PDF::GetX(),$ynew);
         }

         public static function MultiRowAndColoumn($jmhRow) {
            
            $page_start = PDF::getPage();
            $y_start    = PDF::GetY();

            PDF::MultiCell(17, 6, $left, 1, 'L', 0, 2, '', '', true, 0);// write the left cell
            
            $page_end_1 = PDF::getPage();
            $y_end_1    = PDF::GetY();

            PDF::setPage($page_start);
            
            //write the right cell
            if($jmhRow == 2){
                
                PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                
            }else if($jmhRow == 3){
                
                $right = $right / 2;
                
                PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(1, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                
            }else if($jmhRow == 4){
                
                $right = $right / 3;
                
                PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(1, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(2, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                
            }else if($jmhRow == 5){
                
                $right = $right / 4;
                
                PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(1, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(2, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(3, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                
            }else if($jmhRow == 6){
                
                PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(1, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(2, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(3, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(4, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                
            }else if($jmhRow == 7){
                
                PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(1, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(2, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(3, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(4, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(5, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                
            }else if($jmhRow == 8){
                
                PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(1, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(2, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(3, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(4, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(5, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(6, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                
            }else if($jmhRow == 9){
                
                PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(1, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(2, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(3, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(4, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(5, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(6, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(7, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                
            }else if($jmhRow == 10){
                
                PDF::MultiCell(0, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(1, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(2, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(3, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(4, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(5, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(6, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(7, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                PDF::MultiCell(8, 6, $right, 1, 'J', 0, 1, PDF::GetX() ,$y_start, true, 0, false, true, 0, 'M');
                
            }
                
            $page_end_2 = PDF::getPage();
            $y_end_2    = PDF::GetY();

            // set the new row position by case
            if(max($page_end_1,$page_end_2) == $page_start){
                $ynew = max($y_end_1, $y_end_2);
            }elseif ($page_end_1 == $page_end_2){
                $ynew = max($y_end_1, $y_end_2);
            }elseif ($page_end_1 > $page_end_2){
                $ynew = $y_end_1;
            }else{
                $ynew = $y_end_2;
            }

            PDF::setPage(max($page_end_1,$page_end_2));
            PDF::SetXY(PDF::GetX(),$ynew);
         }
         
//         public static function tenColoumn($maxlength){
//            $landscape_max = 264;
//            
//            $kolom2 = ($landscape_max - $kolom1) / 7;
//            
//            if ($maxlength >= 200 && $kolom2) {
//               $rh = 8;                     
//            }else if ($maxlength >= 300 && $kolom2) {
//               $rh = 10;                     
//            }else if ($maxlength >= 400 && $kolom2) {
//               $rh = 12;                     
//            }else if ($maxlength >= 500 && $kolom2) {
//               $rh = 14;                      
//            }else{
//               $rh = 6;  
//            } 
//            return $rh;  
//         }
      
      // -------------------------- END OF SETTING TABLE ---------------------------- // 

   }//end of class