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
            PDF::SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT, TRUE);
            PDF::SetHeaderMargin(PDF_MARGIN_HEADER);            
            PDF::SetFooterMargin(PDF_MARGIN_FOOTER); 

            PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
            PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
       
            PDF::SetPrintHeader(TRUE);         
            PDF::SetPrintFooter(TRUE); 
            PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);   

            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
               require_once(dirname(__FILE__).'/lang/eng.php');
               PDF::setLanguageArray($l);
            }

            PDF::SetCreator('BPKP');
            PDF::SetAuthor('BPKP');
            PDF::SetTitle('Simd@Perencanaan');  

            if($layout == 'P'){
               PDF::AddPage('P', $sizePage);
            }else if($layout == 'L'){
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
            $nama_pemda     = 'Pemerintah Daerah ' .Session::get('xPemda');
            $alamat_pemda   = Session::get('xAlamat'); 
            $kontak_pemda   = Session::get('xKontak'); 
            // $image_file     = K_PATH_IMAGES.$nama_image; 
            $image_file     = 'vendor/default.png'; 
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

            $html = '<hr>
                    <table width="100%" border="0" table-layout="fixed">
                        <tr>
                            <td align="center">'.$judul_laporan.'</td>
                        </tr>
                    </table>';

            PDF::writeHTML($html, true, false, true, false, '');
            
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
                
            $html = '<hr>
                    <table width="100%" border="0" table-layout="fixed">
                        <tr>
                            <td align="center">'.$judul_laporan.'</td>
                        </tr>
                    </table>';

            PDF::writeHTML($html, true, false, true, false, '');
                
            PDF::Ln(4); 
            PDF::SetFillColor(224, 224, 209);
            PDF::SetTextColor(0);
            PDF::SetFont('helvetica', '', 8);
        } 


        public static function setFooter(){
            PDF::setFooterCallback(function($pdf) {
                $tgl_cetak     = "\t\t\t\t\t\t\t\t\t\t\t\t".'Tgl Cetak : '.date('d/m/Y');
                $page_number   = 'Hal : '.PDF::getAliasNumPage().' / '.PDF::getAliasNbPages();
                $pdf->SetY(-15);
                $pdf->SetFont('helvetica', '', 8);
                $html ='<hr><table width="100%" cellpadding="0" table-layout="fixed" border="0">
                                <tr>
                                    <td width="50%" style="text-align: left;">'.$tgl_cetak.'</td>
                                    <td width="50%" style="text-align: right;">'.$page_number.'</td>
                                </tr>
                        </table>';
                $pdf->writeHTML($html, true, false, true, false, '');
            });
        }
            
        // -------------------------------- END OF POTRAIT -------------------------------- // 


    }//end of class