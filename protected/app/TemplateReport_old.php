<?php
   
    //  ---------------------------------------------------------------------------------------------------  //
    //  | Title        : Template Laporan untuk Layout Page Potrait & Landscape SIMDA Perencanaan Tahunan |  //   
    //  | Author       : Viera Lestari Vahendra                                                           |  //  
    //  | NIP          : 19930504 201801 2 003                                                            |  //
    //  | Created Date : 16 APRIL 2018                                                                    |  //
    //  ---------------------------------------------------------------------------------------------------  //
     
   
    namespace App;
      
    use PDF;
    use Session;

    class TemplateReport{

        public static function settingPage($layout, $sizePage){
            PDF::SetDefaultMonospacedFont('PDF_FONT_MONOSPACED');
            PDF::SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT, true);
            PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
            PDF::SetFooterMargin(0);  

            PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
            PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

              

            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
               require_once(dirname(__FILE__).'/lang/eng.php');
               PDF::setLanguageArray($l);
            }

            PDF::SetCreator('BPKP');
            PDF::SetAuthor('BPKP');
            PDF::SetTitle('Simd@Perencanaan');  

            if($layout == 'P'){
                PDF::SetAutoPageBreak(TRUE, 15); 
               PDF::AddPage('P', $sizePage);

            }else if($layout == 'L'){
                PDF::SetAutoPageBreak(TRUE, 10); 
               PDF::AddPage('L', $sizePage);
            }
        }


        public static function cetakNamaReport($nama_laporan){
            $cetak_laporan = $nama_laporan.'.pdf';

            PDF::Output($cetak_laporan, 'I');
        }


        // ----------------------------------- Header and Footer ----------------------------------- // 

        public static function setHeader($layout){            
            $nama_image     = 'default.png';
            $nama_pemda     = 'Pemerintah Daerah Provinsi Simulasi'; //Session::get('xPemda');
            $alamat_pemda   = 'Jl. Raya Simulasi No. 123'; //Session::get('xAlamat'); 
            $kontak_pemda   = '021 1234567';//Session::get('xKontak'); 
            $image_file     = K_PATH_IMAGES.$nama_image; 

            if($layout == 'P'){
              PDF::SetFont('helvetica', 'B', 12);

                $html = '<table width="100%" border="0" table-layout="fixed">
                            <tr>
                                <td width="16%" height="75" align="center"><img src="'.$image_file.'" style="max-height:100%; max-width:auto"></td>
                                <td width="84%" align="center" vertical-align="middle">
                                    <b><font size="12">'.$nama_pemda.'</font></b>
                                    <br>
                                    <font size="11">'.$alamat_pemda.'</font>
                                    <br>
                                    <font size="10">'.$kontak_pemda.'</font>
                                </td>
                            </tr>
                        </table>
                        <hr>';

                PDF::writeHTML($html, true, false, true, false, '');
            }else if($layout == 'L'){
                $html = '<table width="100%" border="0" table-layout="fixed">
                            <tr>
                                <td width="8%" height="75" align="center"><img src="'.$image_file.'"></td>
                                <td width="92%" align="center" vertical-align="middle">
                                    <b><font size="12">'.$nama_pemda.'</font></b>
                                    <br>
                                    <font size="11">'.$alamat_pemda.'</font>
                                    <br>
                                    <font size="10">'.$kontak_pemda.'</font>
                                </td>
                            </tr>
                        </table>
                        <hr>';

                PDF::writeHTML($html, true, false, true, false, '');
            }
        }


        public static function setHeaderOtherPage($judul_laporan,array $size_coloumn ,array $headers, $layout){            
            PDF::SetY(15);
            PDF::SetFont('helvetica', 'I', 5);
            PDF::SetTextColor(100, 100, 100, 100);

            $w = 4; 
            
            if($layout == 'P'){
                PDF::MultiCell(180, $w, $judul_laporan, 'B', 'C', 0, 0, '', '', true, 0, false, true, $w, 'M');
            }else{
                PDF::MultiCell(300, $w, $judul_laporan, 'B', 'C', 0, 0, '', '', true, 0, false, true, $w, 'M');
            }
            
            PDF::Ln(6);
            PDF::SetFont('helvetica', '', 8);
            
            for($i = 0; $i < count($headers); ++$i) {
                PDF::Cell($size_coloumn[$i], 7, $headers[$i], 1, 0, 'C', 1);
            }   
        }


        public static function setHeaderTable(array $size_coloumn ,array $headers){           
            for($i = 0; $i < count($headers); ++$i) {
                PDF::Cell($size_coloumn[$i], 7, $headers[$i], 1, 0, 'C', 1);
            }            
        }

        
        public static function judulReport($judul_laporan, $layout){
            PDF::SetLineWidth(0);
            PDF::SetFont('helvetica', 'B', 9);
                
            if($layout == 'P'){
                PDF::Cell(0, 5, $judul_laporan, 0, false, 'C', 0, '', 0, false, 'M', 'M');
                
            }else{
                PDF::Cell(0, 5, $judul_laporan, 0, false, 'C', 0, '', 0, false, 'M', 'M');
               
            }
            PDF::Ln(4); 
            PDF::SetFillColor(224, 224, 209);
            PDF::SetTextColor(0);
            PDF::SetFont('helvetica', '', 8);
        } 


        public static function setFooter($layout){
            $tgl_cetak     = "\t\t\t\t\t\t\t\t\t\t\t\t".'Tgl Cetak : '.date('d/m/Y');
            $page_number   = 'Page '.PDF::getAliasNumPage().' / '.PDF::getAliasNbPages();

            
            PDF::SetFont('helvetica', 'I', 6);
            PDF::SetTextColor(0, 0, 0, 100);

            $w = 4; 

            if(PDF::getAliasNbPages()){
                if($layout == 'P'){
                    PDF::SetY(-15);
                    PDF::MultiCell(120, $w, $tgl_cetak, 'T', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
                    PDF::MultiCell(60, $w, $page_number, 'T', 'R', 0, 1, '', '', true, 0, false, true, $w, 'M');
                }else{
                    PDF::SetY(-10);
                    PDF::MultiCell(150, $w, $tgl_cetak, 'T', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
                    PDF::MultiCell(150, $w, $page_number, 'T', 'R', 0, 1, '', '', true, 0, false, true, $w, 'M'); 
                }
            }else{
                if($layout == 'P'){
                    PDF::SetY(-15);
                    PDF::MultiCell(120, $w, $tgl_cetak, 'T', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
                    PDF::MultiCell(60, $w, $page_number, 'T', 'R', 0, 1, '', '', true, 0, false, true, $w, 'M');
                }else{
                    PDF::SetY(-10);
                    PDF::MultiCell(150, $w, $tgl_cetak, 'T', 'L', 0, 0, '', '', true, 0, false, true, $w, 'M');
                    PDF::MultiCell(150, $w, $page_number, 'T', 'R', 0, 1, '', '', true, 0, false, true, $w, 'M'); 
                }
            }
            
            PDF::SetFont('helvetica', '', 8);
        }
            
        // -------------------------------- END OF POTRAIT -------------------------------- // 


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

        // -------------------------- END OF SETTING TABLE ---------------------------- // 

    }//end of class