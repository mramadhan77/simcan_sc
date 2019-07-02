<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Laporan\TemplateReport AS Template;
use App\Fungsi as Fungsi;
use CekAkses;
use Validator;
use Response;
use Session;
use PDF;
use Auth;
use PhpParser\Node\Stmt\Foreach_;


class CetakMusrenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function UsulanPerUnit($id_unit,$tahun)
    {
        
        $countrow=0;
        $totalrow=30;
        if($id_unit<1)
        {$Unit = DB::SELECT('SELECT d.id_unit,d.nm_unit FROM trx_musrencam a
            INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
            INNER JOIN ref_unit d ON b.id_unit=d.id_unit
            WHERE tahun_musren='.$tahun.' GROUP BY d.nm_unit');}
        ELSE
        {$Unit = DB::SELECT('SELECT d.id_unit,d.nm_unit FROM trx_musrencam a
            INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
            INNER JOIN ref_unit d ON b.id_unit=d.id_unit
            WHERE b.id_unit='.$id_unit.' AND tahun_musren='.$tahun.' GROUP BY d.id_unit,d.nm_unit');}
        
        
        // set document information
        PDF::SetCreator('BPKP');
        PDF::SetAuthor('BPKP');
        PDF::SetTitle('Simd@Perencanaan');
        PDF::SetSubject('SSH Kelompok');
        
        // set default header data
        PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
        
        // set header AND footer fonts
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
        $header = array('SKPD/Program','Uraian Indikator','Tolak Ukur','Target Renstra','Target Renja','Status Indikator','Pagu Renstra','Pagu Program','Status Program');
        
        // Colors, line width AND bold font
        PDF::SetFillColor(200, 200, 200);
        PDF::SetTextColor(0);
        PDF::SetDrawColor(255, 255, 255);
        PDF::SetLineWidth(0);
        PDF::SetFont('helvetica', 'B', 10);
        foreach($Unit as $row) {
        //Header
        PDF::Cell('275', 5, Session::get('xPemda') , 1, 0, 'C', 0);
        PDF::Ln();
        $countrow++;
        PDF::Cell('275', 5, 'Daftar Usulan Desa Musrenbang RKPD per Kecamatan', 1, 0, 'C', 0);
        PDF::Ln();
        $countrow++;
        PDF::Cell(275, 5, $row->nm_unit, 1, 0, 'C', 0);
        PDF::Ln();
        $countrow++;
        PDF::Cell(275, 5, "Tahun ".$tahun, 1, 0, 'C', 0);
        PDF::Ln();
        $countrow++;
        PDF::Ln();
        $countrow++;

        PDF::SetFont('', 'B');
        PDF::SetFont('helvetica', 'B', 6);
        PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
        // Header Column
        
        $wh = array(45,30,30,20,20,20,20,20,20);
        $w = array(225);
        $w1 = array(5,40,120,20,20,20);
        $w2 = array(45,30,30,20,20,20,60);
        
        PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
        PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
        PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
        PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
        PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
        PDF::Ln();
        $countrow++;
        // Color AND font restoration
        
        PDF::SetFillColor(224, 235, 255);
        PDF::SetTextColor(0);
        PDF::SetFont('helvetica', '', 6);
        // Data
        $fill = 0;
       
            
            //$fill=!$fill;
            $kecamatan = DB::SELECT('SELECT g.id_kecamatan,g.nama_kecamatan FROM trx_musrencam a
                                    INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                                    INNER JOIN ref_kecamatan g ON a.id_kecamatan=g.id_kecamatan
                                    WHERE b.id_unit='.$row->id_unit.' AND tahun_musren='.$tahun.'  GROUP BY g.id_kecamatan,g.nama_kecamatan');
            foreach($kecamatan as $row2) {
                PDF::MultiCell(20, 5, '', 1, 'L', 0, 0);
                PDF::MultiCell(100, 5, $row2->nama_kecamatan, 1, 'L', 0, 0);
                PDF::MultiCell(60, 5, '', 1, 'L', 0, 0);
                PDF::MultiCell(40, 5, '', 1, 'L', 0, 0);
                PDF::MultiCell(55, 5, '', 1, 'L', 0, 0);
                
                PDF::Ln();
                $countrow++;
                if($countrow>=$totalrow)
                {
                    PDF::AddPage('L');
                    $countrow=0;
                    PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
                    PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
                    PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
                    PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
                    PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
                    PDF::Ln();
                    $countrow++;
                }
                $kegiatan = DB::SELECT('SELECT CONCAT(f.kd_urusan,".",f.kd_bidang,".",e.kd_program,".",c.kd_kegiatan) as kode, c.nm_kegiatan, b.id_kegiatan_ref
                        FROM trx_musrencam a
                        INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                        INNER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
                        INNER JOIN ref_unit d ON b.id_unit=d.id_unit
                        INNER JOIN ref_program e ON c.id_program=e.id_program
                        INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                        WHERE b.id_unit='.$row->id_unit.' AND tahun_musren='.$tahun.' AND a.id_kecamatan='.$row2->id_kecamatan.' 
                        GROUP BY kode,c.nm_kegiatan,  b.id_kegiatan_ref');
                
                foreach($kegiatan as $row3) {
                    PDF::MultiCell(20, 5, $row3->kode, 1, 'L', 0, 0);
                    PDF::MultiCell(5, 5, '', 'LBT', 'L', 0, 0);
                    PDF::MultiCell(95, 5, $row3->nm_kegiatan, 'RBT', 'L', 0, 0);
                    PDF::MultiCell(60, 5, '', 1, 'L', 0, 0);
                    PDF::MultiCell(40, 5, '', 1, 'L', 0, 0);
                    PDF::MultiCell(55, 5, '', 1, 'L', 0, 0);
                    PDF::Ln();
                    $countrow++;
                    if($countrow>=$totalrow)
                    {
                        PDF::AddPage('L');
                        $countrow=0;
                        PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
                        PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
                        PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
                        PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
                        PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
                        PDF::Ln();
                        $countrow++;
                    }
                    $aktivitas = DB::SELECT('SELECT a.uraian_aktivitas_kegiatan, a.id_musrencam
                            FROM trx_musrencam a
                            INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                            INNER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
                            INNER JOIN ref_unit d ON b.id_unit=d.id_unit
                            INNER JOIN ref_program e ON c.id_program=e.id_program
                            INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                            INNER JOIN ref_kecamatan g ON a.id_kecamatan=g.id_kecamatan
                            WHERE b.id_unit='.$row->id_unit.' AND a.tahun_musren='.$tahun.' AND a.id_kecamatan='.$row2->id_kecamatan.' AND  b.id_kegiatan_ref='.$row3->id_kegiatan_ref.' 
                            GROUP BY a.uraian_aktivitas_kegiatan, a.id_musrencam');
                    $hitung=0;
                    foreach($aktivitas as $row4) {
                        $lokasi = DB::SELECT('SELECT a.uraian_aktivitas_kegiatan,i.nama_desa,j.volume_usulan_1,k.uraian_satuan, CASE j.status_pelaksanaan
                                WHEN 0 THEN "Diterima Tanpa Perubahan"
                                WHEN 1 THEN "Diterima Dengan Perubahan"
                                WHEN 2 THEN "Digabungkan"
                                ELSE "Ditolak" END status
                                FROM trx_musrencam a
                                INNER JOIN trx_renja_rancangan b ON a.id_renja=b.id_renja
                                INNER JOIN ref_kegiatan c ON b.id_kegiatan_ref=c.id_kegiatan
                                INNER JOIN ref_unit d ON b.id_unit=d.id_unit
                                INNER JOIN ref_program e ON c.id_program=e.id_program
                                INNER JOIN ref_bidang f ON e.id_bidang=f.id_bidang
                                INNER JOIN ref_kecamatan g ON a.id_kecamatan=g.id_kecamatan
                                INNER JOIN trx_musrencam_lokasi h ON a.id_musrencam=h.id_musrencam
                                INNER JOIN ref_desa i ON h.id_desa=i.id_desa
                                INNER JOIN trx_forum_skpd_lokasi j ON h.id_lokasi_musrencam=j.id_lokasi_renja AND j.sumber_data=2
                                INNER JOIN ref_satuan k ON j.id_satuan_1=k.id_satuan                            
                                WHERE b.id_unit='.$row->id_unit.' AND a.tahun_musren='.$tahun.' AND a.id_kecamatan='.$row2->id_kecamatan.' AND a.id_musrencam='.$row4->id_musrencam);
                        
                        foreach($lokasi as $row5) {
                            if($hitung==0)
                            {
                                PDF::MultiCell(20, 5, '', 1, 'L', 0, 0);
                                PDF::MultiCell(10, 5, '', 'LBT', 'L', 0, 0);
                                PDF::MultiCell(90, 5, $row4->uraian_aktivitas_kegiatan, 'RBT', 'L', 0, 0);
                                PDF::MultiCell(60, 5, $row5->nama_desa, 1, 'L', 0, 0);
                                PDF::MultiCell(40, 5, $row5->volume_usulan_1.' '.$row5->uraian_satuan, 1, 'L', 0, 0);
                                PDF::MultiCell(55, 5, $row5->status, 1, 'L', 0, 0);
                                PDF::Ln();
                            }
                            ELSE
                            {
                                PDF::MultiCell(20, 5, '', 1, 'L', 0, 0);
                                PDF::MultiCell(100, 5, '', 1, 'L', 0, 0);
                                PDF::MultiCell(60, 5, $row5->nama_desa, 1, 'L', 0, 0);
                                PDF::MultiCell(40, 5, $row5->volume_usulan_1.' '.$row5->uraian_satuan, 1, 'L', 0, 0);
                                PDF::MultiCell(55, 5, $row5->status, 1, 'L', 0, 0);
                                PDF::Ln();
                                
                                
                            }
                        $countrow++;
                        
                        if($countrow>=$totalrow)
                        {
                            PDF::AddPage('L');
                            $countrow=0;
                            PDF::MultiCell(20, 5, 'Kode', 1, 'C', 0, 0);
                            PDF::MultiCell(100, 5, 'Kegiatan/Aktivitas', 1, 'C', 0, 0);
                            PDF::MultiCell(60, 5, 'Lokasi Desa', 1, 'C', 0, 0);
                            PDF::MultiCell(40, 5, 'Volume', 1, 'C', 0, 0);
                            PDF::MultiCell(55, 5, 'Status Usulan', 1, 'C', 0, 0);
                            PDF::Ln();
                            $countrow++;
                        }
                        $hitung++;
                        //$fill=!$fill;
                        }
                    }
                    //$fill=!$fill;
                }
                //$fill=!$fill;
            }
        }
        //PDF::Cell(array_sum($w), 0, '', 'T');
        
        // ---------------------------------------------------------
        
        // close AND output PDF document
        PDF::Output('Musrenbang.pdf', 'I');
    }
  
    public function UsulanPerKecamatan(Request $request)
    
    {
        Template::settingPageLandscape();
        Template::headerLandscape();
        $pemda=Session::get('xPemda');
        $tahun_musren=$request->tahun;

          if($request->kecamatan == null || $request->kecamatan < 1){
                    $sysKecamatan = '';
                } else {
                    $sysKecamatan = '  AND a.id_kecamatan='.$request->kecamatan.'  ';
                };

          if($request->status_data == null || $request->status_data < 0){
                    $sysStatus = '';
                } else {
                    $sysStatus = '  AND a.status_usulan='.$request->status_data.'  ';
                };
        
        // set font
        PDF::SetFont('helvetica', '', 6);
        
        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>
                    td, th {
                    }
                </style>';
        $html .= '</head>';
        $html .= '<body>';
        PDF::SetFont('helvetica', 'B', 10);
        
        $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Usulan Musrenbang Kecamatan Tahun '. $tahun_musren.'</div>';
        // $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">'. Session::get('xPemda').'</div>';
        $html .= '<br>';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="24%"  style="padding: 50px; text-align: center; font-weight: bold;" >Program / Kegiatan / Aktivitas</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Volume(satuan)</td>
                        <td width="15%"   style="padding: 50px; text-align: center; font-weight: bold;" >Harga Total (Rp)</td>
                        <td width="7%"  style="padding: 50px; text-align: center; font-weight: bold;" >RT / RW</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >Desa / Kecamatan</td>
                        <td width="16%"   style="padding: 50px; text-align: center; font-weight: bold;" >OPD</td>
                        <td width="18%"   style="padding: 50px; text-align: center; font-weight: bold;" >Keterangan</td>
            
                </tr>
                <tr height=19 >
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(1)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(2)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(3)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(4)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(5)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(6)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(7)</td>
                </tr>
            </thead>';
        $html .= '<tbody>';
        $program = DB::select('SELECT DISTINCT g.id_renja_program, g.uraian_program_renstra
            FROM trx_musrencam AS a
            INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
            INNER JOIN  trx_renja_ranwal_program AS g on d.id_renja_program=g.id_renja_program
            INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
            INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
            WHERE a.tahun_musren='.$tahun_musren.   $sysStatus. $sysKecamatan);

        if($program == null){
          PDF::SetFont('helvetica', 'B', 9);
          $html .= '<tr nobr="true">';
          $html .= '<td width="100%" style="padding: 50px; text-align: center;"><div> TIDAK ADA DATA </div></td>';
          $html .= '</tr>';
      } else {
            foreach ($program as $programs) {
                PDF::SetFont('helvetica', 'B', 9);
                $html .= '<tr nobr="true">';
                $html .= '<td width="100%" style="padding: 50px; text-align: justify;"><div>' . $programs->uraian_program_renstra . '</div></td>';
                $html .= '</tr>';
                
                $kegiatan = DB::select('SELECT DISTINCT a.id_renja, d.uraian_kegiatan_renstra
                    FROM trx_musrencam AS a
                    INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
                    INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
                    INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
                    WHERE d.id_renja_program='.$programs->id_renja_program. ' AND a.tahun_musren='.$tahun_musren.   $sysStatus. $sysKecamatan);

                foreach ($kegiatan as $kegiatans) {
                    PDF::SetFont('helvetica', 'B', 7);
                    $html .= '<tr nobr="true">';
                    $html .= '<td width="100%" style="padding: 50px; text-align: justify;"><div> ' . $kegiatans->uraian_kegiatan_renstra . '</div></td>';
                    $html .= '</tr>';
                    $aktivitas = DB::SELECT('SELECT a.uraian_aktivitas_kegiatan, c.nama_kecamatan, b.nama_desa, h.rt, h.rw, a.volume, f.singkatan_satuan, a.jml_pagu, e.nm_unit,
                        CASE a.status_pelaksanaan
                            WHEN 0 THEN "Diterima Tanpa Perubahan"
                            WHEN 1 THEN "Diterima Dengan Perubahan"
                            WHEN 2 THEN "Digabungkan"
                            ELSE "Ditolak" END status
                        FROM trx_musrencam AS a
                        LEFT OUTER JOIN trx_musrendes AS g on a.id_usulan_desa=g.id_musrendes
                        LEFT OUTER JOIN trx_musrendes_rw AS h on g.id_usulan_rw=h.id_musrendes_rw
                        INNER JOIN ref_desa AS b ON h.id_desa = b.id_desa
                        INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
                        INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
                        INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
                        INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
                        WHERE  a.id_renja='. $kegiatans->id_renja.' AND a.tahun_musren='.$tahun_musren.   $sysStatus. $sysKecamatan);

                    foreach ($aktivitas as $aktivitass) {
                        PDF::SetFont('helvetica', '', 7);
                        $html .= '<tr nobr="true">';
                        $html .= '<td width="24%" style="padding: 50px; text-align: justify;"><div>  ' . $aktivitass->uraian_aktivitas_kegiatan . '</div></td>';
                        $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $aktivitass->volume .' '. $aktivitass->singkatan_satuan . '</div></td>';
                        $html .= '<td width="15%" style="padding: 50px; text-align: right;"><div>' . number_format($aktivitass->jml_pagu, 2, ',', '.') . '</div></td>';
                        $html .= '<td width="7%" style="padding: 50px; text-align: justify;"><div>RT ' . $aktivitass->rt . ' / RW '. $aktivitass->rw . '</div></td>';
                        $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $aktivitass->nama_desa . ', '. $aktivitass->nama_kecamatan . '</div></td>';
                        $html .= '<td width="16%" style="padding: 50px; text-align: justify;"><div>' . $aktivitass->nm_unit . '</div></td>';
                        $html .= '<td width="18%" style="padding: 50px; text-align: justify;"><div>' . $aktivitass->status . '</div></td>';
                        
                        $html .= '</tr>';
                    }                    
                }                
            }
        }

        $html .= '</tbody>';
        $html .= '</table>
                </body>
                </html>';
        PDF::writeHTML($html, true, false, true, false, '');
        Template::footerLandscape();
        // ---------------------------------------------------------
        
        // close and output PDF document
        PDF::Output('MusrenCam.pdf', 'I');
    }
    
}
