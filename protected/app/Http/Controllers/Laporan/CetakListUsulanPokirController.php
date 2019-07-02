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


class CetakListUsulanPokirController extends Controller
{

  public function __construct()
    {
        $this->middleware('auth');
    }

    public function TB57(Request $request)
  
  {
      Template::settingPageLandscape();
      Template::headerLandscape();
      $pemda=Session::get('xPemda');      
      $tahun_musren=$request->tahun;
      
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
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;">Tabel T-B.57.</div>';
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Rumusan Usulan Program/Kegiatan</div>';
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Hasil Penelaahan Pokok-pokok Pikiran DPRD dan Validasi</div>';
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Tahun '. $tahun_musren.'</div>';
      $html .= '<br>';
      $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="5%" style="padding: 50px; text-align: center; font-weight: bold;" >No</td>
                        <td width="25%"  style="padding: 50px; text-align: center; font-weight: bold;" >Program / Kegiatan</td>
                        <td width="25%"   style="padding: 50px; text-align: center; font-weight: bold;" >Indikator Kinerja</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Volume</td>
                        <td width="15%"   style="padding: 50px; text-align: center; font-weight: bold;" >Lokasi</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Perangkat Daerah Terkait</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Validasi / Keterangan</td>
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
      $pokir = DB::select('SELECT (@id:=@id+1) as no_urut, b.id_judul_usulan, b.volume,  f.singkatan_satuan,  e.nama_kecamatan,  d.nama_desa,  c.rw,  c.rt,   b.id_satuan,  c.id_kecamatan,  c.id_desa,  g.nm_unit  
        FROM   trx_pokir AS a
        INNER JOIN trx_pokir_usulan AS b ON b.id_pokir = a.id_pokir
        INNER JOIN trx_pokir_lokasi AS c ON c.id_pokir_usulan = b.id_pokir_usulan
        INNER JOIN ref_desa AS d ON c.id_desa = d.id_desa
        INNER JOIN ref_kecamatan AS e ON c.id_kecamatan = e.id_kecamatan
        INNER JOIN ref_satuan AS f ON b.id_satuan = f.id_satuan
        INNER JOIN ref_unit as g on b.id_unit=g.id_unit , (SELECT @id:=0) x
        WHERE a.id_tahun ='.$tahun_musren);
      
if($pokir == null){
          PDF::SetFont('helvetica', 'B', 9);
          $html .= '<tr nobr="true">';
          $html .= '<td width="100%" style="padding: 50px; text-align: center;"><div> TIDAK ADA DATA </div></td>';
          $html .= '</tr>';
      } else {  
        foreach ($pokir as $pokirs) {
            PDF::SetFont('helvetica', '', 9);
            $html .= '<tr nobr="true">';
            $html .= '<td width="5%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->no_urut . '</div></td>';
            $html .= '<td width="25%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->id_judul_usulan . '</div></td>';
            $html .= '<td width="25%" style="padding: 50px; text-align: justify;"><div></div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->volume . ' '.$pokirs->singkatan_satuan .'</div></td>';
            $html .= '<td width="15%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->nama_desa. ', '.$pokirs->nama_kecamatan . '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->nm_unit . '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div></div></td>';
            $html .= '</tr>';
        }
    }

      $html .= '</tbody>';
      $html .= '</table>
                </body>
                </html>';
      PDF::writeHTML($html, true, false, true, false, '');
      Template::footerLandscape();

      PDF::Output('TB57.pdf', 'I');
  }
  
  public function printlistusulanpokir(Request $request)
  
  {
      Template::settingPageLandscape();
      Template::headerLandscape();
      $pemda=Session::get('xPemda');  
      $tahun_musren=$request->tahun;
      
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
      
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> List Usulan Pokok-pokok Pikiran DPRD Tahun '.Session::get('tahun').'</div>';
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Tahun  '.$tahun_musren.'</div>';
      $html .= '<br>';
      
      $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="5%" style="padding: 50px; text-align: center; font-weight: bold;" >No</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >Tanggal</td>
                        <td width="15%"   style="padding: 50px; text-align: center; font-weight: bold;" >Nama</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Asal</td>
                        <td width="30%"   style="padding: 50px; text-align: center; font-weight: bold;" >Deskripsi Usulan</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Volume (Satuan)</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Nama Unit</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Lokasi</td>
                </tr>
          
                <tr height=19 >
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(1)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(2)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(3)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(4)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(5)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(6)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(7)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(8)</td>
          
                </tr>
            </thead>';
      $html .= '<tbody>';
      $ListPokir = DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.tanggal_pengusul,
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
                            WHEN 4 THEN "Anggota"
                            ELSE "Jabatan Lainnya"
                            END AS jabatan_pengusul,
            a.nama_pengusul, CAST(b.diskripsi_usulan as char) as deskripsi_usulan,
            b.volume, e.uraian_satuan, d.nm_unit, CONCAT("RT/RW ",c.rt,"/",c.rw, ", Desa ",f.nama_desa,", Kec. ",g.nama_kecamatan) AS alamat          
            FROM trx_pokir AS a
            LEFT OUTER  JOIN  trx_pokir_usulan AS b ON b.id_pokir = a.id_pokir
            LEFT OUTER  JOIN  trx_pokir_lokasi AS c ON c.id_pokir_usulan = b.id_pokir_usulan
            LEFT OUTER  JOIN  ref_unit AS d ON b.id_unit = d.id_unit
            LEFT OUTER  JOIN  ref_satuan AS e ON b.id_satuan = e.id_satuan
            LEFT OUTER  JOIN  ref_desa AS f ON c.id_desa = f.id_desa
            LEFT OUTER  JOIN  ref_kecamatan AS g ON f.id_kecamatan = g.id_kecamatan
            , (SELECT @id:=0) x
            WHERE  a.id_tahun ='.$tahun_musren);

  if($ListPokir == null){
          PDF::SetFont('helvetica', 'B', 9);
          $html .= '<tr nobr="true">';
          $html .= '<td width="100%" style="padding: 50px; text-align: center;"><div> TIDAK ADA DATA </div></td>';
          $html .= '</tr>';
      } else {      
        foreach ($ListPokir as $pokirs) {
            PDF::SetFont('helvetica', '', 9);
            $html .= '<tr nobr="true">';
            $html .= '<td width="5%" style="padding: 50px; text-align: center;"><div>' . $pokirs->no_urut . '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: center;"><div>' . $pokirs->tanggal_pengusul . '</div></td>';
            $html .= '<td width="15%" style="padding: 50px; text-align: left;"><div>' . $pokirs->nama_pengusul. '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: center;"><div>' . $pokirs->jabatan_pengusul. '</div></td>';
            $html .= '<td width="30%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->deskripsi_usulan. '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: right;"><div>' . $pokirs->volume . ' '.$pokirs->uraian_satuan .'</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: left;"><div>' . $pokirs->nm_unit . '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: left;"><div>' . $pokirs->alamat. '</div></td>';
            $html .= '</tr>';
        }
    }

      $html .= '</tbody>';
      $html .= '</table>
                </body>
                </html>';
      PDF::writeHTML($html, true, false, true, false, '');
      Template::footerLandscape();

      PDF::Output('ListUsulanPokir.pdf', 'I');
  }
 
  public function printListTLPokir(Request $request)  
  {
      Template::settingPageLandscape();
      Template::headerLandscape();
      $pemda=Session::get('xPemda');
      $tahun_musren=$request->tahun;
      
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
      
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> List Tindak Lanjut Usulan Pokok-pokok Pikiran DPRD Tahun '.Session::get('tahun').' Oleh BAPPEDA</div>';
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Tahun '.$tahun_musren.'</div>';
      $html .= '<br>';
      // $header = array('No','Tanggal','Nama','','Volume (Satuan)','Unit Usulan','Unit Review Bappeda','Lokasi','Status','Posting');
      
      $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="5%" style="padding: 50px; text-align: center; font-weight: bold;" >No</td>
                        <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >Tanggal</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Nama</td>
                        <td width="20%"   style="padding: 50px; text-align: center; font-weight: bold;" >Deskripsi Usulan</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Volume (Satuan)</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Unit Usulan</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Unit Review Bappeda</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Lokasi</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Status</td>
                        <td width="5%"   style="padding: 50px; text-align: center; font-weight: bold;" >Posting</td>
                </tr>
          
                <tr height=19 >
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(1)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(2)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(3)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(4)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(5)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(6)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(7)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(8)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(9)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(10)</td>
          
                </tr>
            </thead>';
      $html .= '<tbody>';
      $ListPokir = DB::select('SELECT (@id:=@id+1) as no_urut,a.tanggal_pengusul,
a.nama_pengusul, cast(b.diskripsi_usulan as char) as deskripsi_usulan, b.volume,
e.uraian_satuan, d.nm_unit as unit_usulan, i.nm_unit as unit_review, CONCAT("RT/RW ",c.rt,"/",c.rw, ", Desa ",f.nama_desa,", Kec. ",g.nama_kecamatan) AS alamat,
CASE h.status_tl 
  WHEN 1 THEN "Disposisi Ke Unit"
  WHEN 2 THEN "Dipending"
  WHEN 3 THEN "Perlu Dibahas Kembali"
  WHEN 4 THEN "Tidak Diakomodir"
ELSE"Belum TL" END AS status_tl,
CASE h.status_data
  WHEN 1 THEN "Ya"
ELSE "Tidak" END AS status_posting
FROM trx_pokir AS a
INNER  JOIN  trx_pokir_usulan AS b ON b.id_pokir = a.id_pokir
INNER  JOIN  trx_pokir_lokasi AS c ON c.id_pokir_usulan = b.id_pokir_usulan
INNER JOIN trx_pokir_tl AS h ON c.id_pokir_lokasi=h.id_pokir_lokasi and b.id_pokir_usulan=h.id_pokir_usulan and b.id_pokir=h.id_pokir
LEFT OUTER  JOIN  ref_unit AS d ON b.id_unit = d.id_unit
LEFT OUTER  JOIN  ref_satuan AS e ON b.id_satuan = e.id_satuan
LEFT OUTER  JOIN  ref_desa AS f ON c.id_kecamatan = f.id_kecamatan AND c.id_desa = f.id_desa
LEFT OUTER  JOIN  ref_kecamatan AS g ON f.id_kecamatan = g.id_kecamatan
LEFT OUTER  JOIN  ref_unit AS i ON h.unit_tl = i.id_unit
, (SELECT @id:=0) x
WHERE a.id_tahun ='.$tahun_musren);

  if($ListPokir == null){
          PDF::SetFont('helvetica', 'B', 9);
          $html .= '<tr nobr="true">';
          $html .= '<td width="100%" style="padding: 50px; text-align: center;"><div> TIDAK ADA DATA </div></td>';
          $html .= '</tr>';
      } else {      
        foreach ($ListPokir as $pokirs) {
            PDF::SetFont('helvetica', '', 9);
            $html .= '<tr nobr="true">';
            $html .= '<td width="5%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->no_urut . '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->tanggal_pengusul . '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->nama_pengusul. '</div></td>';
            $html .= '<td width="20%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->deskripsi_usulan. '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->volume . ' '.$pokirs->uraian_satuan .'</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->unit_usulan . '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->unit_review. '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->alamat. '</div></td>';
            $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->status_tl. '</div></td>';
            $html .= '<td width="5%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->status_posting. '</div></td>';
            $html .= '</tr>';
        }
    }

      $html .= '</tbody>';
      $html .= '</table>
                </body>
                </html>';
      PDF::writeHTML($html, true, false, true, false, '');
      Template::footerLandscape();

      PDF::Output('ListPokirTLBappeda.pdf', 'I');
  }
  
  public function printListTLUnitPokir(Request $request)
  
  {
      Template::settingPageLandscape();
      Template::headerLandscape();
      $pemda=Session::get('xPemda');
      $tahun_musren=$request->tahun;
      if($request->unit == null || $request->unit < 1){
          $sysUnit = '';
      } else {
          $sysUnit = '  AND h.unit_tl ='.$request->unit.'  ';
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
      
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> List Tindak Lanjut Usulan Pokok-pokok Pikiran DPRD Tahun '.Session::get('tahun').' Oleh Unit</div>';
      $html .= '<div style="text-align: center; font-size:12px; font-weight: bold;"> Tahun '.$tahun_musren.'</div>';
      $html .= '<br>';
      //  $header = array('No','Tanggal','Nama','Deskripsi Usulan','Unit Review Bappeda','Aktivitas Renja/Forum','Lokasi','Status','Posting');
      
      $html .= '<table border="0.5" cellpadding="4" cellspacing="0">
            <thead>
                <tr height=19>
                        <td width="5%" style="padding: 50px; text-align: center; font-weight: bold;" >No</td>
                        <td width="7%"  style="padding: 50px; text-align: center; font-weight: bold;" >Tanggal</td>
                        <td width="23%"   style="padding: 50px; text-align: center; font-weight: bold;" >Deskripsi Usulan</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Volume (Satuan)</td>
                        <td width="15%"   style="padding: 50px; text-align: center; font-weight: bold;" >Unit Review Bappeda</td>
                        <td width="15%"   style="padding: 50px; text-align: center; font-weight: bold;" >Aktivitas Renja/Forum</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Lokasi</td>
                        <td width="10%"   style="padding: 50px; text-align: center; font-weight: bold;" >Status</td>
                        <td width="5%"   style="padding: 50px; text-align: center; font-weight: bold;" >Posting</td>
                </tr>
          
                <tr height=19 >
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(1)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(2)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(3)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(4)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(5)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(6)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(7)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(8)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(9)</td>
                        <td  style="padding: 50px; text-align: center; font-weight: bold;">(10)</td>
          
                </tr>
            </thead>';
      $html .= '<tbody>';
      $ListPokir = DB::select('SELECT (@id:=@id+1) as no_urut, a.tanggal_pengusul,
a.nama_pengusul, cast(b.diskripsi_usulan as char) as deskripsi_usulan, b.volume,
e.uraian_satuan, i.nm_unit as unit_pelaksana,
CONCAT("RT/RW ",c.rt,"/",c.rw, ", Desa ",f.nama_desa,", Kec. ",g.nama_kecamatan) AS alamat,
CASE j.status_tl 
WHEN 1 THEN "Diakomodir Renja"
WHEN 2 THEN "Diakomodir Forum"
WHEN 3 THEN "Tidak diakomodir"
ELSE "Belum TL" END AS status_tl,
CASE j.status_data
WHEN 1 THEN "Ya"
ELSE "Tidak" END AS status_posting,
CASE j.status_tl 
WHEN 1 THEN ifnull(k.uraian_aktivitas_kegiatan,"-")
WHEN 2 THEN ifnull(l.uraian_aktivitas_kegiatan,"-")
ELSE "-" END AS uraian_aktivitas_kegiatan 
FROM trx_pokir AS a
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
LEFT OUTER JOIN trx_forum_skpd_aktivitas AS l on j.id_aktivitas_forum=l.id_aktivitas_forum, (SELECT @id:=0) x
WHERE a.id_tahun ='.$tahun_musren. $sysUnit);

    if($ListPokir == null){
          PDF::SetFont('helvetica', 'B', 9);
          $html .= '<tr nobr="true">';
          $html .= '<td width="100%" style="padding: 50px; text-align: center;"><div> TIDAK ADA DATA </div></td>';
          $html .= '</tr>';
      } else {  
          foreach ($ListPokir as $pokirs) {
              PDF::SetFont('helvetica', '', 9);
              $html .= '<tr nobr="true">';
              $html .= '<td width="5%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->no_urut . '</div></td>';
              $html .= '<td width="7%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->tanggal_pengusul . '</div></td>';
              $html .= '<td width="23%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->deskripsi_usulan. '</div></td>';
              $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->volume . ' '.$pokirs->uraian_satuan .'</div></td>';
              $html .= '<td width="15%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->unit_pelaksana . '</div></td>';
              $html .= '<td width="15%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->uraian_aktivitas_kegiatan. '</div></td>';
              $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->alamat. '</div></td>';
              $html .= '<td width="10%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->status_tl. '</div></td>';
              $html .= '<td width="5%" style="padding: 50px; text-align: justify;"><div>' . $pokirs->status_posting. '</div></td>';
              $html .= '</tr>';
          }
        }

      $html .= '</tbody>';
      $html .= '</table>
                </body>
                </html>';
      PDF::writeHTML($html, true, false, true, false, '');
      Template::footerLandscape();

      PDF::Output('ListPokirTLUnit.pdf', 'I');
  }
  
}

