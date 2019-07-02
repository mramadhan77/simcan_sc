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

class CetakMusrendesController extends Controller
{

  public function __construct()
    {
        $this->middleware('auth');
    }

	public function printusulanrw(Request $request)
  
  {
      Template::settingPageLandscape();
      Template::headerLandscape();
      $pemda=Session::get('xPemda');
      $tahun_musren=$request->tahun;
      if($request->desa == null || $request->desa < 1){
                $sysDesa = '';
            } else {
                $sysDesa = '  AND a.id_desa='.$request->desa.'  ';
            };

      if($request->kecamatan == null || $request->kecamatan < 1){
                $sysKecamatan = '';
            } else {
                $sysKecamatan = '  AND c.id_kecamatan='.$request->kecamatan.'  ';
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
      
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Usulan Musrenbang RW Tahun '. $tahun_musren.'</div>';
      // $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"></div>';
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
      $program = DB::SELECT('SELECT DISTINCT g.id_renja_program ,g.uraian_program_renstra 
        FROM trx_musrendes_rw AS a
        INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
        INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
        INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
        INNER JOIN trx_renja_ranwal_program AS g on d.id_renja_program=g.id_renja_program
        INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
        INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan 
        WHERE a.tahun_musren='.$tahun_musren.  $sysDesa.  $sysStatus. $sysKecamatan);

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
              FROM trx_musrendes_rw AS a
              INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
              INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
              INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
              INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
              INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
              WHERE d.id_renja_program='.$programs->id_renja_program. ' AND a.tahun_musren='.$tahun_musren.  $sysDesa.  $sysStatus. $sysKecamatan);

          foreach ($kegiatan as $kegiatans) {
              PDF::SetFont('helvetica', 'B', 7);
              $html .= '<tr nobr="true">';
              $html .= '<td width="100%" style="padding: 50px; text-align: justify;"><div> ' . $kegiatans->uraian_kegiatan_renstra . '</div></td>';
              $html .= '</tr>';
              $aktivitas = DB::select('SELECT a.uraian_aktivitas_kegiatan, c.nama_kecamatan,
                  b.nama_desa, a.rt, a.rw, a.volume, f.singkatan_satuan, a.jml_pagu, e.nm_unit
                  FROM trx_musrendes_rw AS a
                  INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
                  INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
                  INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
                  INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
                  INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
                  WHERE  a.id_renja='. $kegiatans->id_renja.' AND a.tahun_musren='.$tahun_musren. $sysDesa.  $sysStatus. $sysKecamatan);

              foreach ($aktivitas as $aktivitass) {
                  PDF::SetFont('helvetica', '', 7);
                  $html .= '<tr nobr="true">';
                  $html .= '<td width="24%" style="padding: 50px; text-align: justify;"><div>  ' . $aktivitass->uraian_aktivitas_kegiatan . '</div></td>';
                  $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $aktivitass->volume .' '. $aktivitass->singkatan_satuan . '</div></td>';
                  $html .= '<td width="15%" style="padding: 50px; text-align: right;"><div>' . number_format($aktivitass->jml_pagu, 2, ',', '.') . '</div></td>';
                  $html .= '<td width="7%" style="padding: 50px; text-align: justify;"><div>RT ' . $aktivitass->rt . ' / RW '. $aktivitass->rw . '</div></td>';
                  $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $aktivitass->nama_desa . ', '. $aktivitass->nama_kecamatan . '</div></td>';
                  $html .= '<td width="16%" style="padding: 50px; text-align: justify;"><div>' . $aktivitass->nm_unit . '</div></td>';
                  $html .= '<td width="18%" style="padding: 50px; text-align: justify;"><div></div></td>';                  
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
      PDF::Output('MusrenRW.pdf', 'I');
  }
  
  public function printusulandesa(Request $request)
  
  {
      Template::settingPageLandscape();
      Template::headerLandscape();
      $pemda=Session::get('xPemda');
      $tahun_musren=$request->tahun;
      if($request->desa == null || $request->desa < 1){
                $sysDesa = '';
            } else {
                $sysDesa = '  AND a.id_desa='.$request->desa.'  ';
            };

      if($request->kecamatan == null || $request->kecamatan < 1){
                $sysKecamatan = '';
            } else {
                $sysKecamatan = '  AND c.id_kecamatan='.$request->kecamatan.'  ';
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
      
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Usulan Musrenbang Desa Tahun '. $tahun_musren.'</div>';
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">'. Session::get('xPemda').'</div>';
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
      $program = DB::select('SELECT DISTINCT g.id_renja_program ,g.uraian_program_renstra
            FROM trx_musrendes AS a
            INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
            INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
            INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
            inner join trx_renja_ranwal_program AS g on d.id_renja_program=g.id_renja_program
            INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
            INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
            WHERE a.tahun_renja='.$tahun_musren.  $sysDesa.  $sysStatus. $sysKecamatan);

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
                FROM trx_musrendes AS a
                INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
                INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
                INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
                INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
                INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
                WHERE d.id_renja_program='.$programs->id_renja_program.' AND a.tahun_renja='.$tahun_musren.  $sysDesa.  $sysStatus. $sysKecamatan);
            foreach ($kegiatan as $kegiatans) {
                PDF::SetFont('helvetica', 'B', 7);
                $html .= '<tr nobr="true">';
                $html .= '<td width="100%" style="padding: 50px; text-align: justify;"><div> ' . $kegiatans->uraian_kegiatan_renstra . '</div></td>';
                $html .= '</tr>';
                $aktivitas = DB::select('SELECT a.uraian_aktivitas_kegiatan, c.nama_kecamatan, b.nama_desa, g.rt, g.rw, a.volume, f.singkatan_satuan, a.jml_pagu, e.nm_unit,
                  CASE a.status_pelaksanaan
                      WHEN 0 THEN "Diterima Tanpa Perubahan"
                      WHEN 1 THEN "Diterima Dengan Perubahan"
                      WHEN 2 THEN "Digabungkan"
                      ELSE "Ditolak" END status
                  FROM trx_musrendes AS a
                  LEFT OUTER JOIN trx_musrendes_rw AS g on a.id_usulan_rw=g.id_musrendes_rw
                  INNER JOIN ref_desa AS b ON a.id_desa = b.id_desa
                  INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
                  INNER JOIN trx_renja_ranwal_kegiatan AS d ON a.id_renja = d.id_renja
                  INNER JOIN ref_unit AS e ON d.id_unit = e.id_unit
                  INNER JOIN ref_satuan AS f ON a.id_satuan = f.id_satuan
                  WHERE  a.id_renja='. $kegiatans->id_renja. ' AND a.tahun_renja='.$tahun_musren.  $sysDesa.  $sysStatus. $sysKecamatan);

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
      PDF::Output('MusrenDes.pdf', 'I');
  }
  
}

