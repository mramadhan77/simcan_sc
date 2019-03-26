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


class CetakPerkinEs2Controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function PerkinEs2BA(Request $request)
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
            'unit.required'=>'Unit Perangkat Daerah Pelaporan Kosong',
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

        $kaban=DB::select('SELECT a.id_dokumen_perkin, a.id_sotk_es2, a.tahun, a.no_dokumen, 
                a.tgl_dokumen, a.tanggal_mulai, a.id_pegawai, a.nama_penandatangan, a.jabatan_penandatangan, 
                a.pangkat_penandatangan, a.uraian_pangkat_penandatangan, a.nip_penandatangan, a.status_data, a.created_at, a.updated_at
                FROM kin_trx_perkin_opd_dok a
                INNER JOIN ref_sotk_level_1 b ON a.id_sotk_es2=b.id_sotk_es2
                WHERE b.id_unit='.$request->unit.' AND a.tahun='.$request->tahun.' LIMIT 1');

        foreach ($kaban as $kabans)
        {
            $html .= '<table cellpadding="0" cellspacing="5" style="font-size:11px; font-weight: normal;" >';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Nama</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$kabans->nama_penandatangan.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Jabatan</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$kabans->jabatan_penandatangan.'</td>';            
            $html .= '</tr></table>';

            $html .= '<br>';
            $html .= '<div style="text-align: justify; font-size:11px; font-weight: normal; padding:0;">selanjutnya disebut <span style="font-weight: bold;">pihak pertama</span></div>';
            $html .= '<br>';

        
        $kada=DB::select('SELECT nama_kepala_daerah, nama_jabatan_kepala_daerah FROM ref_pemda LIMIT 1');

        foreach ($kada as $row)
        {
            $html .= '<table cellpadding="0" cellspacing="5" style="font-size:11px; font-weight: normal;" >';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Nama</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$row->nama_kepala_daerah.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td width="5%"></td>';
            $html .= '<td width="10%">Jabatan</td>';
            $html .= '<td width="3%">:</td>';
            $html .= '<td width="82%">'.$row->nama_jabatan_kepala_daerah.'</td>';            
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
            $html .= '<tr><td width="40%" style="text-align: center;">'.$row->nama_jabatan_kepala_daerah.'</td><td width="10%"></td><td width="40%" style="text-align: center;">Pihak Pertama,</td></tr>';
            $html .= '<tr><td width="40%" height="80"></td><td width="10%" height="80"></td><td width="40%" height="80"></td></tr>';
            $html .= '<tr><td width="40%" style="text-align: center;">'.$row->nama_kepala_daerah.'</td><td width="10%"></td><td width="40%" style="text-align: center;">'.$kabans->nama_penandatangan.'</td></tr>';
            $html .= '</table>';      
            }
        }
        $html .= '</body></html>';
        Template::setFooter();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('PerkinEs2.pdf', 'I');
    }

    public function PerkinEs2Lamp(Request $request)
    {
        $rules = [
            'tahun'=>'integer|digits:4',
            'kota'=>'required',
            'tanggal'=>'required',
            'unit'=>'required',
        ];
        $messages =[
            'tahun.digits'=>'Tahun Perkin Kosong',
            'kota.required'=>'Kota Pelaporan Kosong',
            'tanggal.required'=>'Tanggal Pelaporan Kosong',
            'unit.required'=>'Unit Perangkat Daerah Pelaporan Kosong',
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

        $html .= '<div style="text-align: center; font-size:14px; font-weight: bold;">LAMPIRAN PERJANJIAN KINERJA TAHUN '.$request->tahun.'</div>';
        $html .= '<br><br>';
        
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<thead>
            <tr nobr="true" height=19>                
                <td width="50%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN STRATEGIS</td>
                <td width="40%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA</td>
                <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >TARGET</td>
            </tr></thead>';
        $sasaran=DB::select('SELECT COALESCE(a.uraian_sasaran_renstra,"tidak ada data") AS uraian_sasaran_renstra,
                COALESCE(b.uraian_indikator_sasaran_renstra,"tidak ada data") AS uraian_indikator_sasaran_renstra,
                COALESCE(CASE (SELECT tahun_5 FROM ref_tahun)-'.$request->tahun.'
                WHEN 0 THEN b.angka_tahun5
                WHEN 1 THEN b.angka_tahun4
                WHEN 2 THEN b.angka_tahun3
                WHEN 3 THEN b.angka_tahun2
                ELSE b.angka_tahun1 end,"tidak ada data") AS angka_tahun,
                COALESCE(d.uraian_satuan,"tidak ada data") AS uraian_satuan
                FROM trx_renstra_sasaran a
                LEFT OUTER JOIN trx_renstra_sasaran_indikator b ON a.id_sasaran_renstra=b.id_sasaran_renstra
                LEFT OUTER JOIN ref_indikator c ON b.kd_indikator=c.id_indikator
                LEFT OUTER JOIN ref_satuan d ON c.id_satuan_output=d.id_satuan
                INNER JOIN kin_trx_perkin_opd_sasaran e ON a.id_sasaran_renstra = e.id_sasaran_renstra
                INNER JOIN kin_trx_perkin_opd_dok f ON e.id_dokumen_perkin = f.id_dokumen_perkin
                INNER JOIN ref_sotk_level_1 g ON f.id_sotk_es2 = g.id_sotk_es2
                WHERE g.id_unit='.$request->unit.' AND f.tahun='.$request->tahun);
        $html .= '<tbody>';
        foreach($sasaran as $row)
        {
            $html.='<tr nobr="true">
                <td width="50%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_renstra.'</td>
                <td width="40%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_sasaran_renstra.'</td>';
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
            <td width="70%"  style="padding: 50px; text-align: center; font-weight: bold;" >PROGRAM</td>
            <td width="30%"  style="padding: 50px; text-align: center; font-weight: bold;" >ANGGARAN (Rp.)</td>
            </tr></thead>';
        $program=DB::SELECT('SELECT a.uraian_program_renstra,
                CASE  (SELECT tahun_5 FROM ref_tahun)-'.$request->tahun.'
                WHEN 0 THEN a.pagu_tahun5
                WHEN 1 THEN a.pagu_tahun4
                WHEN 2 THEN a.pagu_tahun3
                WHEN 3 THEN a.pagu_tahun2
                ELSE a.pagu_tahun1 END AS pagu_tahun            
                FROM trx_renstra_program a                
                INNER JOIN kin_trx_perkin_opd_program b ON a.id_program_renstra=b.id_program_renstra
                INNER JOIN kin_trx_perkin_opd_sasaran e ON b.id_perkin_sasaran = e.id_perkin_sasaran
                INNER JOIN kin_trx_perkin_opd_dok f ON e.id_dokumen_perkin = f.id_dokumen_perkin
                INNER JOIN ref_sotk_level_1 g ON f.id_sotk_es2 = g.id_sotk_es2
                WHERE g.id_unit='.$request->unit.' AND f.tahun='.$request->tahun);
        $html .= '<tbody>';
        foreach($program as $row)
        {
            $html.='<tr nobr="true">
                <td width="70%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_program_renstra.'</td>
                <td width="30%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->pagu_tahun, 2, ',', '.').'</td>
                </tr>';
        }
        $html .= '</tbody>';
        $html .= '  </table>';
        $kaban=DB::select('SELECT a.id_dokumen_perkin, a.id_sotk_es2, a.tahun, a.no_dokumen, 
                a.tgl_dokumen, a.tanggal_mulai, a.id_pegawai, a.nama_penandatangan, a.jabatan_penandatangan, 
                a.pangkat_penandatangan, a.uraian_pangkat_penandatangan, a.nip_penandatangan, a.status_data, a.created_at, a.updated_at
                FROM kin_trx_perkin_opd_dok a
                INNER JOIN ref_sotk_level_1 b ON a.id_sotk_es2=b.id_sotk_es2
                WHERE b.id_unit='.$request->unit.' AND a.tahun='.$request->tahun.' LIMIT 1');
        $kada=DB::select('SELECT nama_kepala_daerah, nama_jabatan_kepala_daerah FROM ref_pemda LIMIT 1');
        foreach ($kaban as $kabans)
        {
            foreach ($kada as $row)
            {
                $html .= '<br>'; 
                $html .= '<br">';            
                $html .= '<table style="font-size:11px; font-weight: bold;" nobr="true">';
                $html .= '<tr><td width="40%" style="text-align: center;"></td><td width="10%"></td><td width="40%" style="text-align: center; font-weight: normal;">'.$request->kota.', '.$request->tanggal.'</td></tr>';
                $html .= '<tr><td width="40%" style="text-align: center;">'.$row->nama_jabatan_kepala_daerah.',</td><td width="10%"></td><td width="40%" style="text-align: center;">'.$kabans->jabatan_penandatangan.',</td></tr>';
                $html .= '<tr><td width="40%" height="80"></td><td width="10%" height="80"></td><td width="40%" height="80"></td></tr>';
                $html .= '<tr><td width="40%" style="text-align: center;">'.$row->nama_kepala_daerah.'</td><td width="10%"></td><td width="40%" style="text-align: center;">'.$kabans->nama_penandatangan.'</td></tr>';
                $html .= '</table>';
            
            }
        }
        $html .= '</body></html>';

        Template::setFooter();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('LampPerkinOPD.pdf', 'I');
    }

}
