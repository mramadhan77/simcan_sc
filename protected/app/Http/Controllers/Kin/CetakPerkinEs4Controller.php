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


class CetakPerkinEs4Controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function PerkinEs4BA(Request $request)
    {
        $rules = [
            'tahun'=>'required',
            'kota'=>'required',
            'tanggal'=>'required',
            'unit'=>'required',
        ];
        $messages =[
            'tahun.required'=>'Tahun Perkin Kosong',
            'kota.required'=>'Kota Pelaporan Kosong',
            'tanggal.required'=>'Tanggal Pelaporan Kosong',
            'unit.required'=>'SOTK Level 3 Pelaporan Kosong',
        ];

        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return redirect()->back() ->with('alert', $errors);			
        };

        Template::settingPage('P','A4');
        Template::setHeader('P');

        $pemda = Session::get('xPemda');
        
        PDF::SetFont('helvetica', 'B', 10);

        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '</head>';
        $html .= '<body>';

        $html .= '<div style="text-align: center; font-size:14px; font-weight: bold;">PERJANJIAN KINERJA TAHUN '.$request->tahun.'</div>';
        $html .= '<br><br>';
        $html .= '<div style="text-align: justify; font-size:11px; font-weight: normal; padding:0;">Dalam rangka mewujudkan manajemen pemerintahan yang efektif, transparan dan akuntabel  serta berorientasi pada hasil, 
                    yang bertanda tangan dibawah ini:</div>';
        $html .= '<br>';

        $kaban=DB::select('SELECT a.id_dokumen_perkin, a.id_sotk_es4, a.tahun, a.no_dokumen, a.tgl_dokumen, a.tanggal_mulai, a.id_pegawai, 
                a.nama_penandatangan, a.jabatan_penandatangan, a.pangkat_penandatangan, a.uraian_pangkat_penandatangan, 
                a.nip_penandatangan, a.status_data, a.created_at, a.updated_at, c.nama_penandatangan AS nama_atasan, c.jabatan_penandatangan AS jabatan_atasan
                FROM kin_trx_perkin_es4_dok a
                INNER JOIN ref_sotk_level_3 b ON a.id_sotk_es4=b.id_sotk_es4
                INNER JOIN kin_trx_perkin_es3_dok c ON b.id_sotk_es3 = c.id_sotk_es3 AND a.tahun = c.tahun
                WHERE a.id_sotk_es4='.$request->unit.' AND a.tahun='.$request->tahun.' LIMIT 1');

        foreach ($kaban as $row)
        {
            $html .= '<table cellpadding="0" cellspacing="5" style="font-size:11px; font-weight: normal;" >';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Nama</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$row->nama_penandatangan.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Jabatan</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$row->jabatan_penandatangan.'</td>';            
            $html .= '</tr></table>';

            $html .= '<br>';
            $html .= '<div style="text-align: justify; font-size:11px; font-weight: normal; padding:0;">selanjutnya disebut <span style="font-weight: bold;">pihak pertama</span></div>';
            $html .= '<br>';

            $html .= '<table cellpadding="0" cellspacing="5" style="font-size:11px; font-weight: normal;" >';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Nama</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$row->nama_atasan.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Jabatan</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$row->jabatan_atasan.'</td>';            
            $html .= '</tr></table>';

            $html .= '<br>';
            $html .= '<div style="text-align: justify; font-size:11px; font-weight: normal; padding:0;">selaku atasan pihak pertama, selanjutnya disebut <span style="font-weight: bold;">pihak kedua</span></div>';
            $html .= '<br style ="line-height:5px;">';
            $html .= '<div style="text-align: justify; font-size:11px; font-weight: normal; padding:0;">Pihak pertama berjanji akan mewujudkan target kinerja yang seharusnya sesuai lampiran perjanjian ini, 
                    dalam rangka mencapai target kinerja jangka menengah seperti yang telah ditetapkan dalam dokumen perencanaan. 
                    Keberhasilan dan kegagalan pencapaian target kinerja tersebut menjadi tanggung jawab kami.</div>';
            $html .= '<br style ="line-height:5px;">';
            $html .= '<div style="text-align: justify; font-size:11px; font-weight: normal; padding:0;">Pihak kedua akan melakukan supervisi yang diperlukan serta akan melakukan evaluasi terhadap capaian 
                    kinerja dari perjanjian ini dan mengambil tindakan yang diperlukan dalam rangka pemberian penghargaan dan sanksi.</div>';        
            
            $html .= '<br style ="line-height:70px;">';
            
            $html .= '<table style="font-size:11px; font-weight: bold;" >';
            $html .= '<tr><td width="40%" style="text-align: center;"></td><td width="10%"></td><td width="40%" style="text-align: center; font-weight: normal;">'.$request->kota.', '.$request->tanggal.'</td></tr>';
            $html .= '<tr><td width="40%" style="text-align: center;">Pihak Kedua,</td><td width="10%"></td><td width="40%" style="text-align: center;">Pihak Pertama,</td></tr>';
            $html .= '<tr><td width="40%" height="80"></td><td width="10%" height="80"></td><td width="40%" height="80"></td></tr>';
            $html .= '<tr><td width="40%" style="text-align: center;">'.$row->nama_atasan.'</td><td width="10%"></td><td width="40%" style="text-align: center;">'.$row->nama_penandatangan.'</td></tr>';
            $html .= '</table>';  

        }
        $html .= '</body></html>';
        Template::setFooter();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('PerkinEs3.pdf', 'I');
    }

    public function PerkinEs4Lamp(Request $request)
    {
        $rules = [
            'tahun'=>'required',
            'kota'=>'required',
            'tanggal'=>'required',
            'unit'=>'required',
        ];
        $messages =[
            'tahun.required'=>'Tahun Perkin Kosong',
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

        $html .= '<div style="text-align: center; font-size:14px; font-weight: bold;">PERJANJIAN KINERJA TAHUN '.$request->tahun.'</div>';
        $html .= '<br><br>';
        
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<thead>
            <tr nobr="true" height=19>                
                <td width="50%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN KEGIATAN</td>
                <td width="40%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA</td>
                <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >TARGET</td>
            </tr></thead>';
        $sasaran=DB::select('SELECT COALESCE(a.uraian_hasil_kegiatan,"tidak ada data") AS uraian_sasaran_kegiatan,
            COALESCE(c.nm_indikator,"tidak ada data") AS uraian_indikator_kegiatan_renstra,
            COALESCE(CASE (SELECT tahun_5 FROM ref_tahun)-'.$request->tahun.'
            WHEN 0 THEN b.angka_tahun5
            WHEN 1 THEN b.angka_tahun4
            WHEN 2 THEN b.angka_tahun3
            WHEN 3 THEN b.angka_tahun2
            ELSE b.angka_tahun1 end,"tidak ada data") AS angka_tahun,
            COALESCE(d.uraian_satuan,"tidak ada data") AS uraian_satuan
            FROM kin_trx_cascading_kegiatan_opd a
            INNER JOIN kin_trx_cascading_indikator_kegiatan_pd p ON a.id_hasil_kegiatan = p.id_hasil_kegiatan
            LEFT OUTER JOIN trx_renstra_kegiatan_indikator b ON p.id_renstra_kegiatan_indikator=b.id_indikator_kegiatan_renstra
            LEFT OUTER JOIN ref_indikator c ON b.kd_indikator=c.id_indikator
            LEFT OUTER JOIN ref_satuan d ON c.id_satuan_output=d.id_satuan
            INNER JOIN kin_trx_perkin_es4_kegiatan e ON a.id_renstra_kegiatan = e.id_kegiatan_renstra
            INNER JOIN kin_trx_perkin_es4_dok f ON e.id_dokumen_perkin = f.id_dokumen_perkin
            WHERE f.id_sotk_es4='.$request->unit.' AND f.tahun='.$request->tahun);
        $html .= '<tbody>';
        foreach($sasaran as $row)
        {
            $html.='<tr nobr="true">
                <td width="50%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_kegiatan.'</td>
                <td width="40%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_kegiatan_renstra.'</td>';
            if(is_numeric($row->angka_tahun))
            {
                $html.='<td width="10%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.number_format($row->angka_tahun, 2, ',', '.').' '.$row->uraian_satuan.'</td>';
            }
            else 
            {
                $html.='<td width="10%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->angka_tahun.'</td>';
            }
            $html .='</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<br>';                  
        $html .= '<br style ="line-height:30px;">';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<thead>
            <tr nobr="true" height=19>            
            <td width="70%"  style="padding: 50px; text-align: center; font-weight: bold;" >KEGIATAN</td>
            <td width="30%"  style="padding: 50px; text-align: center; font-weight: bold;" >ANGGARAN (Rp.)</td>
            </tr></thead>';
        $program=DB::SELECT('SELECT a.uraian_kegiatan_renstra,
            CASE  (SELECT tahun_5 FROM ref_tahun)-'.$request->tahun.'
            WHEN 0 THEN a.pagu_tahun5
            WHEN 1 THEN a.pagu_tahun4
            WHEN 2 THEN a.pagu_tahun3
            WHEN 3 THEN a.pagu_tahun2
            ELSE a.pagu_tahun1 END AS pagu_tahun            
            FROM trx_renstra_kegiatan a
            INNER JOIN kin_trx_perkin_es4_kegiatan e ON a.id_kegiatan_renstra = e.id_kegiatan_renstra
            INNER JOIN kin_trx_perkin_es4_dok f ON e.id_dokumen_perkin = f.id_dokumen_perkin
            WHERE f.id_sotk_es4='.$request->unit.' AND f.tahun='.$request->tahun);
        $html .= '<tbody>';
        foreach($program as $row)
        {
            $html.='<tr nobr="true">
                <td width="70%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_kegiatan_renstra.'</td>
                <td width="30%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->pagu_tahun, 2, ',', '.').'</td>
                </tr>';
        }
        $html .= '</tbody>';
        $html .= '  </table>';
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
