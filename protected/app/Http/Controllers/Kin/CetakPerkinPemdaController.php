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


class CetakPerkinPemdaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function PerkinPemdaBA(Request $request)
    {
        $rules = [
            'tahun'=>'required',
            'kota'=>'required',
            'tanggal'=>'required',
        ];
        $messages =[
            'tahun.required'=>'Tahun Perkin Kosong',
            'kota.required'=>'Kota Pelaporan Kosong',
            'tanggal.required'=>'Tanggal Pelaporan Kosong',
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
        $html .= '<div style="text-align: justify; font-size:12px; font-weight: normal; padding:0;">Dalam rangka mewujudkan manajemen pemerintahan yang efektif, transparan dan akuntabel  serta berorientasi pada hasil, 
                    yang bertanda tangan dibawah ini:</div>';
        $html .= '<br>';
        
        $kada=DB::select('SELECT nama_kepala_daerah, nama_jabatan_kepala_daerah FROM ref_pemda LIMIT 1');
        foreach ($kada as $row)
        {
            $html .= '<table cellpadding="0" cellspacing="5" style="font-size:12px; font-weight: normal;" >';
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
            $html .= '<div style="text-align: justify; font-size:12px; font-weight: normal; padding:0;">berjanji akan mewujudkan target kinerja yang seharusnya sesuai lampiran perjanjian ini, 
                        dalam rangka mencapai target kinerja jangka menengah seperti yang telah ditetapkan dalam dokumen perencanaan.</div>';
            $html .= '<br>';
            $html .= '<div style="text-align: justify; font-size:12px; font-weight: normal; padding:0;">Keberhasilan dan kegagalan pencapaian target kinerja tersebut menjadi tanggung jawab kami.</div>';
            $html .= '<br>';
            
            $html .= '<br style ="line-height:130px;">';
            
            $html .= '<table style="font-size:12px; font-weight: bold;" >';
            $html .= '<tr><td></td><td style="text-align: center; font-weight: normal;">'.$request->kota.', '.$request->tanggal.'</td></tr>';
            $html .= '<tr><td></td><td style="text-align: center;">'.$row->nama_jabatan_kepala_daerah.'</td></tr>';
            $html .= '<tr><td height="80"></td><td height="80"></td></tr>';
            $html .= '<tr><td></td><td style="text-align: center;">'.$row->nama_kepala_daerah.'</td></tr>';
            $html .= '</table>';
        
        }

        $html .= '</body></html>';
        Template::setFooter();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('PerkinPemda.pdf', 'I');
    }

    public function PerkinPemdaLamp(Request $request)
    {
        $rules = [
            'tahun'=>'required',
            'kota'=>'required',
            'tanggal'=>'required',
        ];
        $messages =[
            'tahun.required'=>'Tahun Perkin Kosong',
            'kota.required'=>'Kota Pelaporan Kosong',
            'tanggal.required'=>'Tanggal Pelaporan Kosong',
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
                <tr nobr="true" height=19 >        
                    <td width="50%"  style="padding: 50px; text-align: center; font-weight: bold;" >SASARAN STRATEGIS</td>
                    <td width="40%"  style="padding: 50px; text-align: center; font-weight: bold;" >INDIKATOR KINERJA</td>
                    <td width="10%"  style="padding: 50px; text-align: center; font-weight: bold;" >TARGET</td>
                </tr>
            </thead>';
            $sasaran=DB::select('SELECT a.uraian_sasaran_rpjmd,b.uraian_indikator_sasaran_rpjmd,
                    CASE (SELECT tahun_5 FROM ref_tahun)-'.$request->tahun.' 
                        WHEN 0 THEN b.angka_tahun5
                        WHEN 1 THEN b.angka_tahun4
                        WHEN 2 THEN b.angka_tahun3
                        WHEN 3 THEN b.angka_tahun2
                    ELSE b.angka_tahun1 END AS angka_tahun, d.uraian_satuan
                    FROM trx_rpjmd_sasaran a
                    LEFT OUTER  join trx_rpjmd_sasaran_indikator b ON a.id_sasaran_rpjmd=b.id_sasaran_rpjmd
                    LEFT OUTER JOIN ref_indikator c ON b.kd_indikator=c.id_indikator
                    LEFT OUTER JOIN ref_satuan d ON c.id_satuan_output=d.id_satuan');
        $html .= '<tbody>';
        foreach($sasaran as $row)
            {
                $html.='<tr nobr="true">
                        <td width="50%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_sasaran_rpjmd.'</td>
                        <td width="40%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_indikator_sasaran_rpjmd.'</td>
                        <td width="10%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->angka_tahun, 2, ',', '.').' '.$row->uraian_satuan.'</td>
                    </tr>';   
            }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<br>';                  
        $html .= '<br style ="line-height:30px;">';
        $html .= '<table border="0.5" cellpadding="4" cellspacing="0" style="border-bottom-style: double;">';
        $html .= '<thead>
                <tr height=19 nobr="true">                                    
                    <td width="70%"  style="padding: 50px; text-align: center; font-weight: bold;" >PROGRAM</td>
                    <td width="30%"  style="padding: 50px; text-align: center; font-weight: bold;" >ANGGARAN (Rp.)</td>
                </tr></thead>';
        $program=DB::SELECT('SELECT a.uraian_program_rpjmd,
                        CASE  (SELECT tahun_5 FROM ref_tahun)-'.$request->tahun.'  
                            WHEN 0 THEN a.pagu_tahun5
                            WHEN 1 THEN a.pagu_tahun4
                            WHEN 2 THEN a.pagu_tahun3
                            WHEN 3 THEN a.pagu_tahun2
                        ELSE a.pagu_tahun1 END AS pagu_tahun
                        FROM trx_rpjmd_program a');
        $html .= '<tbody>';
        foreach($program as $row)
            {
                $html.='<tr nobr="true">
                            <td width="70%"  style="padding: 50px; text-align: left; font-weight: normal;" >'.$row->uraian_program_rpjmd.'</td>
                            <td width="30%"  style="padding: 50px; text-align: right; font-weight: normal;" >'.number_format($row->pagu_tahun, 2, ',', '.').'</td>
                        </tr>';
            }
        $html .= '</tbody>';
        $html .= '  </table>';
        $kada=DB::select('SELECT nama_kepala_daerah, nama_jabatan_kepala_daerah FROM ref_pemda LIMIT 1');
        foreach ($kada as $row)
        {
            $html .= '<br>'; 
            $html .= '<br style ="line-height:70px;">';            
            $html .= '<table style="font-size:12px; font-weight: bold;" >';
            $html .= '<tr><td></td><td style="text-align: center; font-weight: normal;">'.$request->kota.', '.$request->tanggal.'</td></tr>';
            $html .= '<tr><td></td><td style="text-align: center;">'.$row->nama_jabatan_kepala_daerah.'</td></tr>';
            $html .= '<tr><td height="80"></td><td height="80"></td></tr>';
            $html .= '<tr><td></td><td style="text-align: center;">'.$row->nama_kepala_daerah.'</td></tr>';
            $html .= '</table>';
        
        }
                  
        $html .= '</body></html>';

        Template::setFooter();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('LampPerkinPemda.pdf', 'I');
    }

}
