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

class CetakRpjmdHtmlController extends Controller
{

  public function __construct()
    {
        $this->middleware('auth');
    }

	public function index()
  {
    PDF::SetCreator('BPKP');
    PDF::SetAuthor('BPKP');
    PDF::SetTitle('Simd@Perencanaan');
    PDF::SetSubject('SSH Kelompok');
    PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
    PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
    PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
    PDF::SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
    PDF::SetFont('helvetica', '', 6);
    PDF::AddPage('L');

    $misi = DB::SELECT('SELECT distinct (@id:=@id+1) as no_urut, m.id_misi_rpjmd,concat(v.id_visi_rpjmd,".",
            m.id_misi_rpjmd) as kode, m.uraian_misi_rpjmd FROM
            trx_rpjmd_visi AS v
            INNER JOIN trx_rpjmd_misi AS m ON m.id_visi_rpjmd = v.id_visi_rpjmd, (SELECT @id:=0) x');

    // column titles
    $html_1 = '<table border="1" cellspacing="0" width="100%" align="center">
                  <thead>
                      <tr>
                          <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
                          <th width="5%" style="text-align: center; vertical-align:middle">Kode Misi</th>
                          <th width="90%" style="text-align: center; vertical-align:middle">Uraian Misi</th>
                      </tr>
                  </thead>
                  <tbody>';
    $html_2 = '</tbody>
            </table>';

    foreach ($misi as $row) {
        $html_3 = '<tr>
                        <td width="5%" style="text-align: center; vertical-align:top">'.$row->no_urut.'</td>
                        <td width="5%" style="text-align: center; vertical-align:top">'.$row->kode.'</td>
                        <td width="90%" style="text-align: left; vertical-align:top;">'.$row->uraian_misi_rpjmd.'</td>
                    </tr>';
    };

    $tbl_header = '<table border="1" cellspacing="0" width="100%" align="center">
                    <thead>
                      <tr>
                          <th width="20%" rowspan="3" style="text-align: center; vertical-align:middle">Logo</th>
                          <th width="80%" style="text-align: center; vertical-align:middle">Nama Pemda</th>
                      </tr>
                      <tr>
                          <th width="80%" style="text-align: center; vertical-align:middle">Alamat</th>
                      </tr>
                      <tr>
                          <th width="80%" style="text-align: center; vertical-align:middle">NoTelpon</th>
                      </tr>
                    </thead>
                  </table>';

    PDF::writeHTML($tbl_header, true, false, true, false, '');
    PDF::Output('KompilasiRpjmdHTML.pdf', 'I');
  }

  public function SasaranProgram($id_tujuan, $id_sasaran)
  {
      Template::settingPageLandscape();
      Template::headerLandscape();
      PDF::SetFont('helvetica', '', 6);
      
      $data_tujuan = DB::SELECT('SELECT  id_tujuan_rpjmd,  uraian_tujuan_rpjmd
              FROM   trx_rpjmd_tujuan WHERE   id_tujuan_rpjmd =' . $id_tujuan);

      if ($id_sasaran > 0) {
          $data = DB::SELECT('SELECT A.id_sasaran_rpjmd, C.id_program_renstra, N.id_kegiatan_renstra,              
                      ( SELECT  count(H.id_program_rpjmd) FROM trx_renstra_kegiatan F
                        INNER JOIN trx_renstra_program G ON F.id_program_renstra=G.id_program_renstra
                        INNER JOIN trx_rpjmd_program H ON G.id_program_rpjmd=H.id_program_rpjmd
                        INNER JOIN trx_rpjmd_sasaran I ON H.id_sasaran_rpjmd = I.id_sasaran_rpjmd
                        WHERE A.id_sasaran_rpjmd = I.id_sasaran_rpjmd ) AS level_1,
                      ( SELECT count(L.id_program_rpjmd) FROM trx_renstra_kegiatan J
                        INNER JOIN trx_renstra_program K ON J.id_program_renstra=K.id_program_renstra
                        INNER JOIN trx_rpjmd_program L ON K.id_program_rpjmd=L.id_program_rpjmd
                        INNER JOIN trx_rpjmd_sasaran M ON L.id_sasaran_rpjmd = M.id_sasaran_rpjmd
                        WHERE C.id_program_renstra = K.id_program_renstra ) AS level_2,
                    A.uraian_sasaran_rpjmd, C.uraian_program_renstra, N.uraian_kegiatan_renstra, H.nm_unit
                    FROM trx_rpjmd_sasaran A
                    INNER JOIN trx_rpjmd_program B on A.id_sasaran_rpjmd=B.id_sasaran_rpjmd
                    INNER JOIN trx_renstra_program C on B.id_program_rpjmd=C.id_program_rpjmd
                    INNER JOIN trx_renstra_kegiatan N on C.id_program_renstra=N.id_program_renstra
                    INNER JOIN trx_renstra_sasaran D on C.id_sasaran_renstra=D.id_sasaran_renstra
                    INNER JOIN trx_renstra_tujuan E on D.id_tujuan_renstra=E.id_tujuan_renstra
                    INNER JOIN trx_renstra_misi F on E.id_misi_renstra=F.id_misi_renstra
                    INNER JOIN trx_renstra_visi G on F.id_visi_renstra=G.id_visi_renstra
                    INNER JOIN ref_unit H on G.id_unit=H.id_unit
                    where A.id_sasaran_rpjmd='.$id_sasaran.'
                    order by A.id_sasaran_rpjmd asc, C.id_program_renstra asc');
      } else {
          $data = DB::SELECT('SELECT A.id_sasaran_rpjmd, C.id_program_renstra, N.id_kegiatan_renstra,
                      ( SELECT count(H.id_program_rpjmd) FROM trx_renstra_kegiatan F
                        INNER JOIN trx_renstra_program G ON F.id_program_renstra=G.id_program_renstra
                        INNER JOIN trx_rpjmd_program H ON G.id_program_rpjmd=H.id_program_rpjmd
                        INNER JOIN trx_rpjmd_sasaran I ON H.id_sasaran_rpjmd = I.id_sasaran_rpjmd
                        WHERE A.id_sasaran_rpjmd = I.id_sasaran_rpjmd ) AS level_1,
                      ( SELECT count(L.id_program_rpjmd) FROM trx_renstra_kegiatan J
                        INNER JOIN trx_renstra_program K ON J.id_program_renstra=K.id_program_renstra
                        INNER JOIN trx_rpjmd_program L ON K.id_program_rpjmd=L.id_program_rpjmd
                        INNER JOIN trx_rpjmd_sasaran M ON L.id_sasaran_rpjmd = M.id_sasaran_rpjmd
                        WHERE C.id_program_renstra = K.id_program_renstra ) AS level_2,
                      A.uraian_sasaran_rpjmd, C.uraian_program_renstra, N.uraian_kegiatan_renstra, H.nm_unit
                      FROM trx_rpjmd_sasaran A
                      INNER JOIN trx_rpjmd_program B on A.id_sasaran_rpjmd=B.id_sasaran_rpjmd
                      INNER JOIN trx_renstra_program C on B.id_program_rpjmd=C.id_program_rpjmd
                      INNER JOIN trx_renstra_kegiatan N on C.id_program_renstra=N.id_program_renstra
                      INNER JOIN trx_renstra_sasaran D on C.id_sasaran_renstra=D.id_sasaran_renstra
                      INNER JOIN trx_renstra_tujuan E on D.id_tujuan_renstra=E.id_tujuan_renstra
                      INNER JOIN trx_renstra_misi F on E.id_misi_renstra=F.id_misi_renstra
                      INNER JOIN trx_renstra_visi G on F.id_visi_renstra=G.id_visi_renstra
                      INNER JOIN ref_unit H on G.id_unit=H.id_unit
                      where A.id_tujuan_rpjmd=' . $id_tujuan . 'order by A.id_sasaran_rpjmd asc, C.id_program_renstra asc');
      }

      $jum_level_1 = 1;
      $jum_level_2 = 1;
      
      $html = '';
      $html .= '<html>';
      $html .= '<head>';
      $html .= '<style>
                    td, th {
                    }
                </style>';
      $html .= '</head>';
      $html .= '<body>';
      $nm_unit = '';
      foreach ($data_tujuan as $tujuan) {
          $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">Matrik Sasaran Program RPJMD </div>';
          $html .= '<div style="text-align: left; font-size:12px; font-weight: bold;">Tujuan : ' . $tujuan->uraian_tujuan_rpjmd . '</div>';
      };

      $html .= '<br>';
      $html .= '<table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>          
                    <th style="text-align: center; vertical-align:middle">Sasaran</th>
                    <th style="text-align: center; vertical-align:middle">Program</th>
                    <th style="text-align: center; vertical-align:middle">Kegiatan</th>
                </tr>
                </thead>
            <tbody >';
      
      foreach ($data as $row) {
          $html .= '<tr nobr="true">';          
          if ($jum_level_1 <= 1) {
              $html .= '<td rowspan="' . $row->level_1 . '" style="padding: 50px; text-align: justify;"><div><span style="font-weight: bold;">' . $row->uraian_sasaran_rpjmd . '</span></div>';
              $html .= '<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
              $sasaran_ind = DB::SELECT('SELECT  (@id :=@id + 1) AS urut, a.thn_id, a.no_urut,  a.id_sasaran_rpjmd,  a.id_indikator_sasaran_rpjmd, a.id_perubahan, a.kd_indikator,
                    a.uraian_indikator_sasaran_rpjmd, a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2, a.angka_tahun3, a.angka_tahun4, a.angka_tahun5,
                    a.angka_akhir_periode, a.sumber_data, a.created_at, a.updated_at ,COALESCE (b.nm_indikator, "Kosong") AS nm_indikator, c.uraian_satuan
                    FROM trx_rpjmd_sasaran_indikator AS a
                    LEFT OUTER  JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                    LEFT OUTER JOIN ref_satuan c on b.id_satuan_output=c.id_satuan ,(SELECT @id := 0) x 
                    WHERE a.id_sasaran_rpjmd=' . $row->id_sasaran_rpjmd);

              $html .= '<table border="0.1" cellpadding="2" cellspacing="0">';
              foreach ($sasaran_ind as $sasarans) {
                  $html .= '<tr><td width="10%" style="text-align: center;"> ' . $sasarans->urut . ' </td>';
                  $html .= '<td width="40%" style="text-align: justify;">' . $sasarans->uraian_indikator_sasaran_rpjmd . '('.$sasarans->uraian_satuan.')</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($sasarans->angka_tahun1, 2, ',', '.') . '</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($sasarans->angka_tahun2, 2, ',', '.') . '</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($sasarans->angka_tahun3, 2, ',', '.') . '</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($sasarans->angka_tahun4, 2, ',', '.') . '</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($sasarans->angka_tahun5, 2, ',', '.') . '</td></tr>';
              }
              $html .= '</table>';
              $html .= '</td>';
              $jum_level_1 = $row->level_1;
          } else {
              $jum_level_1 = $jum_level_1 - 1;
          };

          if ($jum_level_2 <= 1) {
              $html .= '<td rowspan="' . $row->level_2 . '" style="padding: 50px; text-align: justify;"><div><span style="font-weight: bold;">' . $row->uraian_program_renstra . '</span></div>';
              $html .= '<div><span style="font-weight: bold; font-style: italic">UNIT :'.$row->nm_unit.'</span></div>';
              $html .= '<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
              $program_ind = DB::SELECT('SELECT (@id :=@id + 1) AS urut, a.thn_id, a.no_urut, a.id_program_renstra, a.id_indikator_program_renstra, a.id_perubahan, a.kd_indikator,
                      a.uraian_indikator_program_renstra, a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2, a.angka_tahun3, a.angka_tahun4, a.angka_tahun5,
                      a.angka_akhir_periode, a.sumber_data, a.created_at, a.updated_at ,COALESCE (b.nm_indikator, "Kosong") AS nm_indikator, c.uraian_satuan
                      FROM trx_renstra_program_indikator AS a
                      LEFT OUTER  JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                      LEFT OUTER JOIN ref_satuan AS c on b.id_satuan_output=c.id_satuan,(SELECT @id := 0) x  
                      WHERE a.id_program_renstra=' . $row->id_program_renstra);

              $html .= '<table border="0.1" cellpadding="2" cellspacing="0">';
              foreach ($program_ind as $programs) {
                  $html .= '<tr><td width="10%" style="text-align: center;"> ' . $programs->urut . ' </td>';
                  $html .= '<td width="40%" style="text-align: justify;">' . $programs->uraian_indikator_program_renstra . '('.$programs->uraian_satuan.')</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($programs->angka_tahun1, 2, ',', '.') . '</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($programs->angka_tahun2, 2, ',', '.') . '</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($programs->angka_tahun3, 2, ',', '.') . '</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($programs->angka_tahun4, 2, ',', '.') . '</td>';
                  $html .= '<td width="10%" style="text-align: justify;">' . number_format($programs->angka_tahun5, 2, ',', '.') . '</td></tr>';
              }
              $html .= '</table>';
              $html .= '</td>';              
              $jum_level_2 = $row->level_2;
          } else {
              $jum_level_2 = $jum_level_2 - 1;
          };

          $html .= '<td style="padding: 50px; text-align: justify;"><div><span style="font-weight: bold;">' . $row->uraian_kegiatan_renstra . '</span></div>';          
          $html .= '<div><span style="font-weight: bold; font-style: italic">INDIKATOR :</span></div>';
          $kegiatan_ind = DB::SELECT('SELECT (@id :=@id + 1) AS urut, a.thn_id, a.no_urut, a.id_kegiatan_renstra, a.id_indikator_kegiatan_renstra, a.id_perubahan, a.kd_indikator,
                a.uraian_indikator_kegiatan_renstra, a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2, a.angka_tahun3, a.angka_tahun4, a.angka_tahun5,
                a.angka_akhir_periode, a.sumber_data, a.created_at, a.updated_at ,COALESCE (b.nm_indikator, "Kosong") AS nm_indikator, c.uraian_satuan
                FROM trx_renstra_kegiatan_indikator AS a
                LEFT OUTER  JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                LEFT OUTER JOIN ref_satuan AS c on b.id_satuan_output=c.id_satuan, (SELECT @id := 0) x  
                WHERE a.id_kegiatan_renstra=' . $row->id_kegiatan_renstra);

          $html .= '<table border="0.1" cellpadding="2" cellspacing="0">';
          foreach ($kegiatan_ind as $kegiatans) {
              $html .= '<tr><td width="10%" style="text-align: center;"> ' . $kegiatans->urut . ' </td>';
              $html .= '<td width="40%" style="text-align: justify;">' . $kegiatans->uraian_indikator_kegiatan_renstra . '('.$kegiatans->uraian_satuan.')</td>';
              $html .= '<td width="10%" style="text-align: justify;">' . number_format($kegiatans->angka_tahun1, 2, ',', '.') . '</td>';
              $html .= '<td width="10%" style="text-align: justify;">' . number_format($kegiatans->angka_tahun2, 2, ',', '.') . '</td>';
              $html .= '<td width="10%" style="text-align: justify;">' . number_format($kegiatans->angka_tahun3, 2, ',', '.') . '</td>';
              $html .= '<td width="10%" style="text-align: justify;">' . number_format($kegiatans->angka_tahun4, 2, ',', '.') . '</td>';
              $html .= '<td width="10%" style="text-align: justify;">' . number_format($kegiatans->angka_tahun5, 2, ',', '.') . '</td></tr>';
          }
          $html .= '</table>';
          $html .= '</td>';
          $html .= '</tr>';
      };
      
      $html .= '</tbody></table>';
      
      PDF::writeHTML($html, true, false, true, false, '');
      Template::footerLandscape();
      PDF::Output('MatrikSasaranProgramRPJMD-' . $nm_unit . '.pdf', 'I');
  }

  
}