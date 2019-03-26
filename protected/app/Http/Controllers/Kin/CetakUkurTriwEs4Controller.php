<?php

namespace App\Http\Controllers\Kin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\TemplateReport As Template;
use App\Fungsi as Fungsi;
use CekAkses;
use Validator;
use Response;
use Session;
use PDF;
use Auth;


class CetakUkurTriwEs4Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function UkurTriwEs4Lamp(Request $request)
    {
        $rules = [
            'tahun'=>'required',
            'triwulan'=>'required',
            'kota'=>'required',
            'tanggal'=>'required',
            'unit'=>'required',
        ];
        $messages =[
            'tahun.required'=>'Tahun Pengukuran Kosong',
            'triwulan.required'=>'Triwulan Pengukuran Kosong',
            'kota.required'=>'Kota Pelaporan Kosong',
            'tanggal.required'=>'Tanggal Pelaporan Kosong',
            'unit.required'=>'SOTK Level 3 Pelaporan Kosong',
        ];

        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return redirect()->back() ->with('alert', $errors);			
        };

        Template::settingPage('L','F4');
        Template::setHeader('L');

        $pemda = Session::get('xPemda');
        
        PDF::SetFont('helvetica', 'B', 10);

        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '</head>';
        $html .= '<body>';

        $html .= '<div style="text-align: center; font-size:14px; font-weight: bold;">CAPAIAN PERJANJIAN KINERJA<br>';
        $html .= 'TRIWULAN '.$request->triwulan.' TAHUN '.$request->tahun.'</div>';
        $html .= '<br><br>';
        
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<thead>
            <tr nobr="true" height=19>                
                <td width="40%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN KEGIATAN</td>
                <td width="35%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA</td>
                <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >TARGET</td>
                <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >REALISASI</td>
                <td width="5%"  style="padding: 50px; text-align: center; font-weight: bold;" >%</td>
            </tr></thead>';
        $sasaran=DB::select('SELECT COALESCE(a.uraian_sasaran_kegiatan,"tidak ada data") AS uraian_sasaran_kegiatan,
            COALESCE(c.nm_indikator,"tidak ada data") AS uraian_indikator_kegiatan_renstra,
            COALESCE(CASE 4 - '.$request->triwulan.'
                WHEN 0 THEN g.target_t4
                WHEN 1 THEN g.target_t3
                WHEN 2 THEN g.target_t2
                WHEN 3 THEN g.target_t1
            END,0) AS angka_tahun,
            COALESCE(d.uraian_satuan,"tidak ada data") AS uraian_satuan,
            COALESCE(CASE 4 - '.$request->triwulan.'
                WHEN 0 THEN g.real_t4
                WHEN 1 THEN g.real_t3
                WHEN 2 THEN g.real_t2
                WHEN 3 THEN g.real_t1
            END,0) AS angka_real,
            COALESCE(CASE 4 - '.$request->triwulan.'
                WHEN 0 THEN (g.real_t4 / g.target_t4)*100
                WHEN 1 THEN (g.real_t3 / g.target_t3)*100
                WHEN 2 THEN (g.real_t2 / g.target_t2)*100
                WHEN 3 THEN (g.real_t1 / g.target_t1)*100
            END,0) AS capaian_real
            FROM trx_renstra_kegiatan a
            LEFT OUTER JOIN trx_renstra_kegiatan_indikator b ON a.id_kegiatan_renstra=b.id_kegiatan_renstra
            LEFT OUTER JOIN ref_indikator c ON b.kd_indikator=c.id_indikator
            LEFT OUTER JOIN ref_satuan d ON c.id_satuan_output=d.id_satuan
            INNER JOIN kin_trx_real_es4_kegiatan_indikator g ON b.id_indikator_kegiatan_renstra = g.id_indikator_kegiatan_renstra
            INNER JOIN kin_trx_real_es4_kegiatan e ON g.id_real_kegiatan = e.id_real_kegiatan
            INNER JOIN kin_trx_real_es4_dok f ON e.id_dokumen_real = f.id_dokumen_real
            WHERE f.id_sotk_es4='.$request->unit.' AND f.tahun='.$request->tahun.' AND f.triwulan='.$request->triwulan);
        $html .= '<tbody>';
        foreach($sasaran as $row)
        {
            $html .='<tr nobr="true">';
            $html .='<td width="40%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_kegiatan.'</td>';
            $html .='<td width="35%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_kegiatan_renstra.'</td>';
            $html .='<td width="10%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->angka_tahun, 2, ',', '.').'</td>';
            $html .='<td width="10%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->angka_real, 2, ',', '.').'</td>';
            $html .='<td width="5%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->capaian_real, 2, ',', '.').'</td>';
            $html .='</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<br>';                  
        $html .= '<br style ="line-height:30px;">';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<thead>
            <tr nobr="true" height=19>            
            <td width="45%"  style="padding: 50px; text-align: center; font-weight: bold;" >KEGIATAN</td>
            <td width="25%"  style="padding: 50px; text-align: center; font-weight: bold;" >ANGGARAN (Rp.)</td>
            <td width="25%"  style="padding: 50px; text-align: center; font-weight: bold;" >REALISASI</td>
            <td width="5%"  style="padding: 50px; text-align: center; font-weight: bold;" >%</td>
            </tr></thead>';
        $program=DB::SELECT('SELECT COALESCE(a.uraian_kegiatan_renstra,"tidak ada data") AS uraian_kegiatan_renstra,
            COALESCE(CASE 4 - '.$request->triwulan.'
                WHEN 0 THEN e.pagu_t4
                WHEN 1 THEN e.pagu_t3
                WHEN 2 THEN e.pagu_t2
                WHEN 3 THEN e.pagu_t1
            END,0) AS angka_tahun,
            COALESCE(CASE 4 - '.$request->triwulan.'
                WHEN 0 THEN e.real_t4
                WHEN 1 THEN e.real_t3
                WHEN 2 THEN e.real_t2
                WHEN 3 THEN e.real_t1
            END,0) AS angka_real,
            COALESCE(CASE 4 - '.$request->triwulan.'
                WHEN 0 THEN (e.real_t4 / e.pagu_t4)*100
                WHEN 1 THEN (e.real_t3 / e.pagu_t3)*100
                WHEN 2 THEN (e.real_t2 / e.pagu_t2)*100
                WHEN 3 THEN (e.real_t1 / e.pagu_t1)*100
            END,0) AS capaian_real
            FROM trx_renstra_kegiatan a
            INNER JOIN kin_trx_real_es4_kegiatan e ON a.id_kegiatan_renstra = e.id_kegiatan_renstra
            INNER JOIN kin_trx_real_es4_dok f ON e.id_dokumen_real = f.id_dokumen_real
            WHERE f.id_sotk_es4='.$request->unit.' AND f.tahun='.$request->tahun.' AND f.triwulan='.$request->triwulan);
        $html .= '<tbody>';
        foreach($program as $row)
        {
            $html.='<tr nobr="true">';
            $html.='<td width="45%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_kegiatan_renstra.'</td>';
            $html.='<td width="25%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->angka_tahun, 2, ',', '.').'</td>';
            $html.='<td width="25%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->angka_real, 2, ',', '.').'</td>';
            $html.='<td width="5%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->capaian_real, 2, ',', '.').'</td>';
            $html.='</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $kaban=DB::select('SELECT a.id_dokumen_perkin, a.id_sotk_es4, a.tahun, a.no_dokumen, a.tgl_dokumen, a.tanggal_mulai, a.id_pegawai, 
            a.nama_penandatangan, a.jabatan_penandatangan, a.pangkat_penandatangan, a.uraian_pangkat_penandatangan, 
            a.nip_penandatangan, a.status_data, a.created_at, a.updated_at, c.nama_penandatangan AS nama_atasan, c.jabatan_penandatangan AS jabatan_atasan
            FROM kin_trx_perkin_es4_dok a
            INNER JOIN ref_sotk_level_3 b ON a.id_sotk_es4=b.id_sotk_es4
            INNER JOIN kin_trx_perkin_es3_dok c ON b.id_sotk_es3 = c.id_sotk_es3 AND a.tahun = c.tahun
            WHERE a.id_sotk_es4='.$request->unit.' AND a.tahun='.$request->tahun.' LIMIT 1');

        foreach ($kaban as $row)
        {
            $html .= '<br>';
            $html .= '<br>';
            
            $html .= '<table style="font-size:11px; font-weight: bold;" nobr="true">';
            $html .= '<tr><td width="40%" style="text-align: center;"></td><td width="10%"></td><td width="40%" style="text-align: center; font-weight: normal;">'.$request->kota.', '.$request->tanggal.'</td></tr>';
            $html .= '<tr><td width="40%" style="text-align: center;">Pihak Kedua,</td><td width="10%"></td><td width="40%" style="text-align: center;">Pihak Pertama,</td></tr>';
            $html .= '<tr><td width="40%" height="80"></td><td width="10%" height="80"></td><td width="40%" height="80"></td></tr>';
            $html .= '<tr><td width="40%" style="text-align: center;">'.$row->nama_atasan.'</td><td width="10%"></td><td width="40%" style="text-align: center;">'.$row->nama_penandatangan.'</td></tr>';
            $html .= '</table>'; 
        }
        $html .= '</body></html>';

        Template::setFooter();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('LampPerkinEs4.pdf', 'I');
    }

}
