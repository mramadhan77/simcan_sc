<?php
namespace App\Http\Controllers\Kin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use DB;
use Response;
use Session;
use Auth;
use CekAkses;
use App\Fungsi as Fungsi;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use Doctrine\DBAL\Query\QueryException;
use App\Models\RefUnit;
use App\Models\Kin\KinTrxPerkinEs3Dok;
use App\Models\Kin\KinTrxPerkinEs3Program;
use App\Models\Kin\KinTrxPerkinEs3ProgramIndikator;
use App\Models\Kin\KinTrxPerkinEs3Kegiatan;


class KinPerkinEs3Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
	{
		// if(Auth::check()){ 
		    return view("kin.perkin.es3.FrmPerkinIndex");			
		// } else {
			// return view ( 'errors.401' );
		// }	
    }

    public function getUnit(Request $request){
        $unit = RefUnit::select();
        if(isset(Auth::User()->getUserSubUnit)){
            foreach(Auth::User()->getUserSubUnit as $data){
                $unit->Where(['id_unit' => $data->kd_unit]);                
            }
        }
        $unit = $unit->get();
        if($request->ajax()){
            return json_encode($unit);
        }
    }

    public function getEselon3($id_eselon2)
        {
            $getJabatan=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es3, a.id_sotk_es2, a.nama_eselon, a.tingkat_eselon, b.id_unit,
            CASE a.tingkat_eselon 
                WHEN 0 THEN "Ia" /*0*/
                WHEN 1 THEN "Ib"
                WHEN 2 THEN "IIa" /*1*/
                WHEN 3 THEN "IIb"
                WHEN 4 THEN "IIIa" /*2*/
                WHEN 5 THEN "IIIb"
                WHEN 6 THEN "IVa" /*3*/
                WHEN 7 THEN "IVb"
            ELSE "((Error))" END AS eselon_display
            FROM ref_sotk_level_2 AS a
			INNER JOIN ref_sotk_level_1 AS b ON a.id_sotk_es2=b.id_sotk_es2,(SELECT @id:=0) x
            WHERE b.id_unit='.$id_eselon2);
            return json_encode($getJabatan);
        }

        public function getEselon4($id_eselon3)
        {
            $getJabatan=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es3, a.id_sotk_es4, a.nama_eselon, a.tingkat_eselon,
            CASE a.tingkat_eselon 
                WHEN 0 THEN "Ia" /*0*/
                WHEN 1 THEN "Ib"
                WHEN 2 THEN "IIa" /*1*/
                WHEN 3 THEN "IIb"
                WHEN 4 THEN "IIIa" /*2*/
                WHEN 5 THEN "IIIb"
                WHEN 6 THEN "IVa" /*3*/
                WHEN 7 THEN "IVb"
            ELSE "((Error))" END AS eselon_display
            FROM ref_sotk_level_3 AS a,(SELECT @id:=0) x
            WHERE a.id_sotk_es3='.$id_eselon3);
            return json_encode($getJabatan);
        }

        public function getDokumenEs2($id_eselon2,$tahun)
        {
            $getJabatan=DB::SELECT('SELECT (@id:=@id+1) AS urut, a.id_dokumen_perkin, a.id_sotk_es2, a.tahun, a.no_dokumen, a.tgl_dokumen, 
            a.tanggal_mulai, a.id_pegawai, a.nama_penandatangan, a.jabatan_penandatangan, a.pangkat_penandatangan, a.uraian_pangkat_penandatangan, 
            a.nip_penandatangan, a.status_data, a.created_at, a.updated_at
            FROM kin_trx_perkin_opd_dok AS a,(SELECT @id:=0) x
            WHERE a.id_sotk_es2='.$id_eselon2.' AND a.tahun ='.$tahun);
            return json_encode($getJabatan);
        }

    public function getDokumen($id_unit)
    {
        $dokumen = DB::select('SELECT DISTINCT (@id:=@id+1) as no_urut,a.id_dokumen_perkin, a.id_sotk_es3, a.tahun, a.no_dokumen, a.tgl_dokumen, a.tanggal_mulai, a.id_pegawai, 
            a.nama_penandatangan, a.jabatan_penandatangan, a.nip_penandatangan, a.status_data, a.created_at, a.updated_at,
            b.nama_pegawai, b.nip_pegawai, a.pangkat_penandatangan, a.uraian_pangkat_penandatangan, c.id_sotk_es2, c.nama_eselon, c.tingkat_eselon,
            CASE c.tingkat_eselon 
                WHEN 0 THEN "Ia" /*0*/
                WHEN 1 THEN "Ib"
                WHEN 2 THEN "IIa" /*1*/
                WHEN 3 THEN "IIb"
                WHEN 4 THEN "IIIa" /*2*/
                WHEN 5 THEN "IIIb"
                WHEN 6 THEN "IVa" /*3*/
                WHEN 7 THEN "IVb"
            ELSE "((Error))" END AS eselon_display
            FROM kin_trx_perkin_es3_dok AS a
            LEFT OUTER JOIN ref_pegawai AS b ON a.id_pegawai = b.id_pegawai
            LEFT OUTER JOIN ref_sotk_level_2 AS c ON a.id_sotk_es3 = c.id_sotk_es3, (SELECT @id:=0) x
            WHERE a.id_sotk_es3 ='.$id_unit);

        return DataTables::of($dokumen)
            ->addColumn('action', function ($dokumen) {
                return
                '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditDokumen dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Detail Data</a>
                        </li>
                        <li>
                            <a class="btnHapusDokumen dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Data</a>
                        </li>
                        <li>
                            <a class="btnPostingDokumen dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-primary"></i> Posting Dokumen </a>
                        </li>
                    </ul>
                </div>';
            })
            ->make(true);

    }

    public function getJabatan($id_unit)
    {
        $getJabatan=DB::SELECT('SELECT (@id:=@id+1) AS urut,id_sotk_es2, id_unit, nama_eselon, tingkat_eselon, created_at, updated_at,
        CASE tingkat_eselon 
                WHEN 0 THEN "Ia" /*0*/
                WHEN 1 THEN "Ib"
                WHEN 2 THEN "IIa" /*1*/
                WHEN 3 THEN "IIb"
                WHEN 4 THEN "IIIa" /*2*/
                WHEN 5 THEN "IIIb"
                WHEN 6 THEN "IVa" /*3*/
                WHEN 7 THEN "IVb"
        ELSE "((Error))" END AS eselon_display
        FROM ref_sotk_level_1,(SELECT @id:=0) x
        WHERE id_unit='.$id_unit);
        return json_encode($getJabatan);
    }

    public function getPegawai()
    {
        $getPegawai=DB::SELECT('SELECT (@id:=@id+1) AS urut, a.id_pangkat, a.id_pegawai, a.pangkat_pegawai, 
            a.tmt_pangkat, a.created_at, a.updated_at, b.nama_pegawai, b.nip_pegawai,
            CASE a.pangkat_pegawai
                WHEN  9 THEN "Penata Muda (III/a)"
                WHEN 10 THEN "Penata Muda Tk.I (III/b)"
                WHEN 11 THEN "Penata (III/c)" 
                WHEN 12 THEN "Penata Tk.I (III/d)" 
                WHEN 13 THEN "Pembina (IV/a)"
                WHEN 14 THEN "Pembina Tk. I (IV/b)" 
                WHEN 15 THEN "Pembina Utama Muda (IV/c)"
                WHEN 16 THEN "Pembina Utama Madya (IV/d)"
                WHEN 17 THEN "Pembina Utama (IV/e)"
            END AS pangkat_display
            FROM ref_pegawai_pangkat AS a
            INNER JOIN ref_pegawai AS b ON a.id_pegawai=b.id_pegawai,(SELECT @id:=0) x
            WHERE a.pangkat_pegawai >10');
        return json_encode($getPegawai);
    }

    public function getPejabat($id_pegawai)
    {
        $getPegawai=DB::SELECT('SELECT a.id_pegawai, a.nama_pegawai, a.nip_pegawai, b.nama_jabatan, c.pangkat_pegawai, b.id_unit, b.id_sotk,
            CASE c.pangkat_pegawai
                WHEN  9 THEN "Penata Muda (III/a)"
                WHEN 10 THEN "Penata Muda Tk.I (III/b)"
                WHEN 11 THEN "Penata (III/c)" 
                WHEN 12 THEN "Penata Tk.I (III/d)" 
                WHEN 13 THEN "Pembina (IV/a)"
                WHEN 14 THEN "Pembina Tk. I (IV/b)" 
                WHEN 15 THEN "Pembina Utama Muda (IV/c)"
                WHEN 16 THEN "Pembina Utama Madya (IV/d)"
                WHEN 17 THEN "Pembina Utama (IV/e)"
            END AS pangkat_display 
            FROM ref_pegawai AS a
            INNER JOIN (SELECT a.id_pegawai, a.id_unit, a.id_sotk, a.nama_jabatan, a.tmt_unit
            FROM ref_pegawai_unit AS a WHERE a.tmt_unit = (SELECT MAX(tmt_unit) FROM ref_pegawai_unit AS b WHERE a.id_sotk = b.id_sotk
            AND b.tingkat_eselon=1 AND b.id_sotk='.$id_pegawai.') ) AS b ON a.id_pegawai = b.id_pegawai
            INNER JOIN (SELECT a.id_pangkat, a.id_pegawai, a.pangkat_pegawai, a.tmt_pangkat
            FROM ref_pegawai_pangkat AS a WHERE a.tmt_pangkat = (SELECT MAX(tmt_pangkat) FROM ref_pegawai_pangkat AS b WHERE a.id_pegawai = b.id_pegawai)
            ) AS c ON a.id_pegawai = c.id_pegawai
            WHERE b.id_sotk ='.$id_pegawai);
        return json_encode($getPegawai);
    }

    public function addDokumen(Request $request)
    {
        $rules = [
            'id_sotk_es3'=>'required',
            'tahun'=>'required',
            'no_dokumen'=>'required',
            'tgl_dokumen'=>'required',
            'tanggal_mulai'=>'required',
            'id_pegawai'=>'required',
            'nama_penandatangan'=>'required',
            'jabatan_penandatangan'=>'required',
            'pangkat_penandatangan'=>'required',
            'uraian_pangkat_penandatangan'=>'required',
            'nip_penandatangan'=>'required',
        ];
        $messages =[
            'id_sotk_es3.required'=>'Jabatan Eselon Kosong',
            'tahun.required'=>'Tahun Dokumen Kosong',
            'no_dokumen.required'=>'Nomor Dokumen Kosong',
            'tgl_dokumen.required'=>'Tanggal Dokumen Kosong',
            'tanggal_mulai.required'=>'TMT Perkin Kosong',
            'id_pegawai.required'=>'ID Pegawai Kosong',
            'nama_penandatangan.required'=>'Nama Pegawai Kosong',
            'jabatan_penandatangan.required'=>'Jabatan Eselon Kosong',
            'pangkat_penandatangan.required'=>'Pangkat Golongan Kosong',
            'uraian_pangkat_penandatangan.required'=>'Jabatan Pegawai Kosong',
            'nip_penandatangan.required'=>'NIP Pegawai Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new KinTrxPerkinEs3Dok();
            $data->id_sotk_es3= $request->id_sotk_es3;
            $data->tahun= $request->tahun;
            $data->no_dokumen= $request->no_dokumen;
            $data->tgl_dokumen= $request->tgl_dokumen;
            $data->tanggal_mulai= $request->tanggal_mulai;
            $data->id_pegawai= $request->id_pegawai;
            $data->nama_penandatangan= $request->nama_penandatangan;
            $data->jabatan_penandatangan= $request->jabatan_penandatangan;
            $data->pangkat_penandatangan= $request->pangkat_penandatangan;
            $data->uraian_pangkat_penandatangan= $request->uraian_pangkat_penandatangan;
            $data->nip_penandatangan= $request->nip_penandatangan;
            $data->status_data= 0;
            try{
                $data->save (['timestamps' => true]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
		}
    }
    
    public function editDokumen(Request $request)
    {
        $rules = [
            'id_sotk_es3'=>'required',
            'tahun'=>'required',
            'no_dokumen'=>'required',
            'tgl_dokumen'=>'required',
            'tanggal_mulai'=>'required',
            'id_pegawai'=>'required',
            'nama_penandatangan'=>'required',
            'jabatan_penandatangan'=>'required',
            'pangkat_penandatangan'=>'required',
            'uraian_pangkat_penandatangan'=>'required',
            'nip_penandatangan'=>'required',
            'id_dokumen_perkin'=> 'required',
        ];
        $messages =[
            'id_sotk_es3.required'=>'Jabatan Eselon Kosong',
            'tahun.required'=>'Tahun Dokumen Kosong',
            'no_dokumen.required'=>'Nomor Dokumen Kosong',
            'tgl_dokumen.required'=>'Tanggal Dokumen Kosong',
            'tanggal_mulai.required'=>'TMT Perkin Kosong',
            'id_pegawai.required'=>'ID Pegawai Kosong',
            'nama_penandatangan.required'=>'Nama Pegawai Kosong',
            'jabatan_penandatangan.required'=>'Jabatan Eselon Kosong',
            'pangkat_penandatangan.required'=>'Pangkat Golongan Kosong',
            'uraian_pangkat_penandatangan.required'=>'Jabatan Pegawai Kosong',
            'nip_penandatangan.required'=>'NIP Pegawai Kosong',
            'id_dokumen_perkin.required'=> 'ID Dokumen Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = KinTrxPerkinEs3Dok::find($request->id_dokumen_perkin);  
            $data->id_sotk_es3= $request->id_sotk_es3;
            $data->tahun= $request->tahun;
            $data->no_dokumen= $request->no_dokumen;
            $data->tgl_dokumen= $request->tgl_dokumen;
            $data->tanggal_mulai= $request->tanggal_mulai;
            $data->id_pegawai= $request->id_pegawai;
            $data->nama_penandatangan= $request->nama_penandatangan;
            $data->jabatan_penandatangan= $request->jabatan_penandatangan;
            $data->pangkat_penandatangan= $request->pangkat_penandatangan;
            $data->uraian_pangkat_penandatangan= $request->uraian_pangkat_penandatangan;
            $data->nip_penandatangan= $request->nip_penandatangan;
            try{
                $data->save (['timestamps' => true]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }    

    public function delDokumen(Request $request){
        $rules = [
            'id_dokumen_perkin'=> 'required',
        ];
        $messages =[
            'id_dokumen_perkin.required'=> 'ID Dokumen Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = KinTrxPerkinEs3Dok::where('id_dokumen_perkin',$request->id_dokumen_perkin)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

    public function getTahunKe($tahun)
    { 
        $tahunke=DB::select('SELECT a.tahun_ke FROM (SELECT tahun_1 as tahun, 1 as tahun_ke from ref_tahun
                UNION SELECT tahun_2 as tahun, 2 as tahun_ke from ref_tahun
                UNION SELECT tahun_3 as tahun, 3 as tahun_ke from ref_tahun
                UNION SELECT tahun_4 as tahun, 4 as tahun_ke from ref_tahun
                UNION SELECT tahun_5 as tahun, 5 as tahun_ke from ref_tahun) a
                WHERE a.tahun='.$tahun);

        foreach($tahunke as $tahunX) {
            return $tahunX->tahun_ke;
        }
    }

    public function transSasaranRenstra(Request $request)
    {
        $rules = [
            'tahun'=> 'required',
            'id_sotk_es3'=> 'required',
        ];
        $messages =[
            'tahun.required'=> 'Tahun Perjanjian Kinerja Kosong', 
            'id_sotk_es3.required'=> 'Unit Perangkat Daerah Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $Sasaran=DB::INSERT('INSERT INTO kin_trx_perkin_es3_program (id_dokumen_perkin, id_perkin_program_opd, id_program_renstra, pagu_tahun, status_data)
                SELECT d.id_dokumen_perkin, a.id_perkin_program, a.id_program_renstra, a.pagu_tahun, 0 
                FROM kin_trx_perkin_opd_program AS a
                INNER JOIN kin_trx_perkin_opd_sasaran AS b ON a.id_perkin_sasaran = b.id_perkin_sasaran
                INNER JOIN kin_trx_perkin_opd_dok AS c ON b.id_dokumen_perkin = c.id_dokumen_perkin
                INNER JOIN kin_trx_perkin_es3_dok AS d ON a.id_sotk_es3 = d.id_sotk_es3
                LEFT OUTER JOIN (SELECT p.* FROM kin_trx_perkin_es3_program AS p
                        INNER JOIN kin_trx_perkin_es3_dok AS q ON p.id_dokumen_perkin = q.id_dokumen_perkin
                        WHERE q.id_sotk_es3 ='.$request->id_sotk_es3.' AND q.tahun='.$request->tahun.') AS e ON a.id_perkin_program = e.id_perkin_program_opd
                WHERE e.id_perkin_program IS NULL AND d.id_sotk_es3 ='.$request->id_sotk_es3.' AND d.tahun='.$request->tahun);
            if($Sasaran==0){
                return response ()->json (['pesan'=>'Data Gagal Proses Sasaran Renstra ke Perjanjian Kinerja','status_pesan'=>'0']);
            } else {
                $Indikator=DB::INSERT('INSERT INTO kin_trx_perkin_es3_program_indikator
                    (id_perkin_program, id_indikator_program_renstra, target_tahun, target_t1, target_t2, target_t3, target_t4, status_data)
                    SELECT b.id_perkin_program, a.id_indikator_program_renstra, a.angka_tahun'.$this->getTahunKe($request->tahun).' AS target_tahun, 
                    IF(a.angka_tahun'.$this->getTahunKe($request->tahun).' > 0, 25, 0) AS target_t1, 
                    IF(a.angka_tahun'.$this->getTahunKe($request->tahun).' > 0, 25, 0) AS target_t2, 
                    IF(a.angka_tahun'.$this->getTahunKe($request->tahun).' > 0, 25, 0) AS target_t3, 
                    IF(a.angka_tahun'.$this->getTahunKe($request->tahun).' > 0, 25, 0) AS target_t4, 
                    0 AS status_data 
                    FROM trx_renstra_program_indikator AS a
                    INNER JOIN kin_trx_perkin_es3_program AS b ON a.id_program_renstra = b.id_program_renstra
                    INNER JOIN kin_trx_perkin_es3_dok AS c ON b.id_dokumen_perkin = c.id_dokumen_perkin
                    LEFT OUTER JOIN (SELECT  p.* FROM kin_trx_perkin_es3_program_indikator AS p
                        INNER JOIN kin_trx_perkin_es3_program AS q ON p.id_perkin_program = q.id_perkin_program
                        INNER JOIN kin_trx_perkin_es3_dok AS r ON q.id_dokumen_perkin = r.id_dokumen_perkin
                        WHERE r.id_sotk_es3 ='.$request->id_sotk_es3.' AND r.tahun='.$request->tahun.') AS e ON a.id_indikator_program_renstra = e.id_indikator_program_renstra
                    WHERE e.id_perkin_indikator IS NULL AND c.id_sotk_es3 ='.$request->id_sotk_es3.' AND c.tahun='.$request->tahun);
                if($Indikator==0){
                    return response ()->json (['pesan'=>'Data Gagal Proses Sasaran Indikator Renstra ke Perjanjian Kinerja','status_pesan'=>'0']);
                } else {
                    $Program=DB::INSERT('INSERT INTO kin_trx_perkin_es3_kegiatan (id_perkin_program, id_kegiatan_renstra, pagu_tahun, id_sotk_es4, status_data)
                        SELECT b.id_perkin_program, a.id_kegiatan_renstra, a.pagu_tahun'.$this->getTahunKe($request->tahun).' AS pagu_tahun, 0, 0 
                        FROM trx_renstra_kegiatan AS a
                        INNER JOIN kin_trx_perkin_es3_program AS b ON a.id_program_renstra = b.id_program_renstra
                        INNER JOIN kin_trx_perkin_es3_dok AS c ON b.id_dokumen_perkin = c.id_dokumen_perkin
                        LEFT OUTER JOIN (SELECT  p.* FROM kin_trx_perkin_es3_kegiatan AS p
                            INNER JOIN kin_trx_perkin_es3_program AS q ON p.id_perkin_program = q.id_perkin_program
                            INNER JOIN kin_trx_perkin_es3_dok AS r ON q.id_dokumen_perkin = r.id_dokumen_perkin
                            WHERE r.id_sotk_es3 ='.$request->id_sotk_es3.' AND r.tahun='.$request->tahun.') AS e ON a.id_kegiatan_renstra = e.id_kegiatan_renstra
                        WHERE e.id_perkin_kegiatan IS NULL AND c.id_sotk_es3 ='.$request->id_sotk_es3.' AND c.tahun='.$request->tahun);
                    if($Program==0){
                        return response ()->json (['pesan'=>'Data Gagal Proses Program Renstra ke Perjanjian Kinerja','status_pesan'=>'0']);
                    } else {
                        return response ()->json (['pesan'=>'Data Sukse Proses Renstra ke Perjanjian Kinerja','status_pesan'=>'2']);
                    }
                }
            }  
        }         
    }
    
    public function getSasaran($id_dokumen_perkin)
    {
        $sasaran=DB::SELECT('SELECT (@id:=@id+1) AS urut,d.id_perkin_program, d.id_dokumen_perkin, d.id_perkin_program_opd, d.id_program_renstra, a.id_program_ref, b.uraian_program,
                CONCAT(c.kd_urusan,".",RIGHT(CONCAT("0",c.kd_bidang),2),".",RIGHT(CONCAT("0",b.kd_program),2)) as kd_program, 
                CONCAT("(",c.kd_urusan,".",RIGHT(CONCAT("0",c.kd_bidang),2),".",RIGHT(CONCAT("0",b.kd_program),2),") ", b.uraian_program) AS uraian_program_display,
                d.pagu_tahun, (SELECT COUNT(a.id_perkin_indikator) FROM kin_trx_perkin_es3_program_indikator AS a WHERE a.id_perkin_program=d.id_perkin_program ) AS jml_indikator
                FROM trx_renstra_program AS a
                INNER JOIN ref_program AS b ON a.id_program_ref = b.id_program
                INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang
                INNER JOIN kin_trx_perkin_es3_program AS d ON a.id_program_renstra = d.id_program_renstra
                INNER JOIN kin_trx_perkin_es3_dok AS e ON d.id_dokumen_perkin = e.id_dokumen_perkin,
                (SELECT @id:=0) x
                WHERE e.id_dokumen_perkin='.$id_dokumen_perkin );

        return DataTables::of($sasaran)
        ->addColumn('details_url', function($sasaran) {
            return url('perkin/es3/getIndikatorSasaran/'.$sasaran->id_perkin_program);
        })
        ->addColumn('action', function ($sasaran) {
            return '
                <button type="button" class="btn btn-info btn-sm btnDetailSasaran btn-labeled"><span class="btn-label">
                <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
            })
        ->make(true);
    }

    public function getIndikatorSasaran($id_perkin_program)
    {
        $indikator=DB::SELECT('SELECT (@id:=@id+1) AS urut,d.id_indikator_program_renstra, d.id_perkin_program, d.id_perkin_indikator, d.target_tahun, d.target_t1, d.target_t2, d.target_t3, d.target_t4,
            d.status_data, b.id_indikator, b.nm_indikator, b.id_satuan_output, c.uraian_satuan
            FROM trx_renstra_program_indikator AS a
            INNER JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
            LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan
            INNER JOIN kin_trx_perkin_es3_program_indikator AS d ON a.id_indikator_program_renstra = d.id_indikator_program_renstra
            INNER JOIN kin_trx_perkin_es3_program AS e ON d.id_perkin_program = e.id_perkin_program
            INNER JOIN kin_trx_perkin_es3_dok AS f ON e.id_dokumen_perkin = f.id_dokumen_perkin,
            (SELECT @id:=0) x WHERE d.id_perkin_program='.$id_perkin_program);

      return DataTables::of($indikator)
        ->addColumn('action', function ($indikator) {            
              return '
              <button type="button" class="btn btn-info btn-sm btnDetailIndikatorSasaran btn-labeled"><span class="btn-label">
              <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
          })
        ->make(true);
        }
            
    public function editIndikatorSasaran(Request $request)
        {
            $rules = [
                'id_perkin_indikator'=>'required',
                'target_t1'=>'required',
                'target_t2'=>'required',
                'target_t3'=>'required',
                'target_t4'=>'required',
            ];
            $messages =[
                'id_perkin_indikator.required'=>'ID Perjanjian Kinerja Indikator Kosong',
                'target_t1.required'=>'Target Triwulan I Kosong',
                'target_t2.required'=>'Target Triwulan II Kosong',
                'target_t3.required'=>'Target Triwulan III Kosong',
                'target_t4.required'=>'Target Triwulan IV Kosong',
            ];
            $validation = Validator::make($request->all(),$rules,$messages);
            
            if($validation->fails()) {
                $errors = Fungsi::validationErrorsToString($validation->errors());
                return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
                }
            else {
                $data = KinTrxPerkinEs3ProgramIndikator::find($request->id_perkin_indikator);  
                $data->target_t1= $request->target_t1;
                $data->target_t2= $request->target_t2;
                $data->target_t3= $request->target_t3;
                $data->target_t4= $request->target_t4;
                try{
                    $data->save (['timestamps' => true]);
                    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                }
            }
        } 
        
        public function getProgram($id_perkin_sasaran)
        {
            $program=DB::SELECT('SELECT (@id:=@id+1) AS urut, e.id_perkin_program, e.id_perkin_kegiatan, e.id_kegiatan_renstra, e.pagu_tahun, a.id_kegiatan_ref, b.nm_kegiatan,
                CONCAT(d.kd_urusan,".",RIGHT(CONCAT("0",d.kd_bidang),2),".",RIGHT(CONCAT("0",c.kd_program),2),".",RIGHT(CONCAT("0",b.kd_kegiatan),2)) as kd_kegiatan, 
                CONCAT("(",d.kd_urusan,".",RIGHT(CONCAT("0",d.kd_bidang),2),".",RIGHT(CONCAT("0",c.kd_program),2),".",RIGHT(CONCAT("0",b.kd_kegiatan),2),") ", b.nm_kegiatan) AS uraian_kegiatan_display,
                e.pagu_tahun, e.status_data, e.id_sotk_es4, COALESCE(h.nama_eselon,"KOSONG") as nama_eselon
                FROM trx_renstra_kegiatan AS a
                INNER JOIN ref_kegiatan AS b ON a.id_kegiatan_ref = b.id_kegiatan
                INNER JOIN ref_program AS c ON b.id_program = c.id_program
                INNER JOIN ref_bidang AS d ON c.id_bidang = d.id_bidang                
                INNER JOIN kin_trx_perkin_es3_kegiatan AS e ON a.id_kegiatan_renstra = e.id_kegiatan_renstra
                INNER JOIN kin_trx_perkin_es3_program AS f ON e.id_perkin_program = f.id_perkin_program
                INNER JOIN kin_trx_perkin_es3_dok AS g ON f.id_dokumen_perkin = g.id_dokumen_perkin
				LEFT OUTER JOIN ref_sotk_level_3 AS h ON e.id_sotk_es4 = h.id_sotk_es4,
                (SELECT @id:=0) x  WHERE e.id_perkin_program='.$id_perkin_sasaran);
    
          return DataTables::of($program)
            ->addColumn('action', function ($program) {            
                  return '
                  <button type="button" class="btn btn-info btn-sm btnDetailProgram btn-labeled"><span class="btn-label">
                  <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
              })
            ->make(true);
        }
        
        

        public function editProgram(Request $request)
        {
            $rules = [
                'id_perkin_kegiatan'=>'required',
                'id_sotk_es4'=>'required',
            ];
            $messages =[
                'id_perkin_kegiatan.required'=>'ID Kegiatan Kosong',
                'id_sotk_es4.required'=>'Pelaksana Kegiatan Kosong',
            ];
            $validation = Validator::make($request->all(),$rules,$messages);
            
            if($validation->fails()) {
                $errors = Fungsi::validationErrorsToString($validation->errors());
                return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
                }
            else {
                $data = KinTrxPerkinEs3Kegiatan::find($request->id_perkin_kegiatan);  
                $data->id_sotk_es4= $request->id_sotk_es4;
                try{
                    $data->save (['timestamps' => true]);
                    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                }
            }
        }
        
}