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
use App\Models\Kin\KinTrxRealEs2Dok;
use App\Models\Kin\KinTrxRealEs2Program;
use App\Models\Kin\KinTrxRealEs2Sasaran;
use App\Models\Kin\KinTrxRealEs2SasaranIndikator;
use App\Models\Kin\KinTrxRealEs3ProgramIndikator;


class KinRealEs2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
	{
		// if(Auth::check()){ 
		    return view("kin.real.es2.FrmRealIndex");			
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
            $getJabatan=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es2, a.id_sotk_es2, a.nama_eselon, a.tingkat_eselon, a.id_unit,
            CASE a.tingkat_eselon 
                WHEN 0 THEN "I"
                WHEN 1 THEN "II"
                WHEN 2 THEN "III"
                WHEN 3 THEN "IV"
            ELSE "((Error))" END AS eselon_display
            FROM  ref_sotk_level_1 AS a ,(SELECT @id:=0) x
            WHERE a.id_unit='.$id_eselon2);
            return json_encode($getJabatan);
        }

        public function getDokumenEs2($id_eselon4,$tahun)
        {
            if($tahun > 2010) {
                $query = ' WHERE b.id_unit='.$id_eselon4.' AND a.tahun ='.$tahun;
            } else {
                $query = ' WHERE b.id_unit='.$id_eselon4;
            }

            $getJabatan=DB::SELECT('SELECT (@id:=@id+1) AS urut, a.id_dokumen_perkin, a.id_sotk_es2, a.tahun, a.no_dokumen, a.tgl_dokumen, 
                a.tanggal_mulai, a.id_pegawai, a.nama_penandatangan, a.jabatan_penandatangan, a.pangkat_penandatangan, a.uraian_pangkat_penandatangan, 
                a.nip_penandatangan, a.status_data, a.created_at, a.updated_at
                FROM kin_trx_perkin_opd_dok AS a
                INNER JOIN ref_sotk_level_1 AS b ON a.id_sotk_es2 = b.id_sotk_es2,(SELECT @id:=0) x '.$query);
            return json_encode($getJabatan);
        }

    public function getDokumen($id_unit)
    {
        $dokumen = DB::select('SELECT DISTINCT (@id:=@id+1) as no_urut,a.id_dokumen_real, a.id_dokumen_perkin, c.id_unit, a.tahun, a.triwulan, a.no_dokumen, a.tgl_dokumen, a.id_pegawai, 
            a.nama_penandatangan, a.jabatan_penandatangan, a.nip_penandatangan, a.status_data, a.created_at, a.updated_at,
            b.nama_pegawai, b.nip_pegawai, a.pangkat_penandatangan, a.uraian_pangkat_penandatangan, a.id_sotk_es2, c.nama_eselon, c.tingkat_eselon,
            CASE c.tingkat_eselon 
                WHEN 0 THEN "I"
                WHEN 1 THEN "II"
                WHEN 2 THEN "III"
                WHEN 3 THEN "IV"
            ELSE "((Error))" END AS eselon_display
            FROM kin_trx_real_es2_dok AS a
            LEFT OUTER JOIN ref_pegawai AS b ON a.id_pegawai = b.id_pegawai
            LEFT OUTER JOIN ref_sotk_level_1 AS c ON a.id_sotk_es2 = c.id_sotk_es2, (SELECT @id:=0) x
            WHERE c.id_unit ='.$id_unit);

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
                            <a class="btnLihatRealEs3 dropdown-item"><i class="fa fa-file-text-o fa-fw fa-lg blue"></i> Realisasi Level 2</a>
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
            WHEN 0 THEN "I"
            WHEN 1 THEN "II"
            WHEN 2 THEN "III"
            WHEN 3 THEN "IV"
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
            WHERE a.pangkat_pegawai >11');
        return json_encode($getPegawai);
    }

    public function getPejabat($id_pegawai)
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
            WHERE a.id_pegawai ='.$id_pegawai.' LIMIT 1');
        return json_encode($getPegawai);
    }

    public function addDokumen(Request $request)
    {
        $rules = [
            'id_sotk_es2'=>'required',
            'tahun'=>'required',
            'no_dokumen'=>'required',
            'tgl_dokumen'=>'required',
            'triwulan'=>'required',
            'id_pegawai'=>'required',
            'nama_penandatangan'=>'required',
            'jabatan_penandatangan'=>'required',
            'pangkat_penandatangan'=>'required',
            'uraian_pangkat_penandatangan'=>'required',
            'nip_penandatangan'=>'required',
            'id_dokumen_perkin'=> 'required',
        ];
        $messages =[
            'id_sotk_es2.required'=>'Jabatan Eselon Kosong',
            'tahun.required'=>'Tahun Dokumen Kosong',
            'no_dokumen.required'=>'Nomor Dokumen Kosong',
            'tgl_dokumen.required'=>'Tanggal Dokumen Kosong',
            'triwulan.required'=>'Triwulan Realisasi Kosong',
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
			$data = new KinTrxRealEs2Dok();
            $data->id_sotk_es2= $request->id_sotk_es2;
            $data->tahun= $request->tahun;
            $data->id_dokumen_perkin= $request->id_dokumen_perkin;
            $data->no_dokumen= $request->no_dokumen;
            $data->tgl_dokumen= $request->tgl_dokumen;
            $data->triwulan= $request->triwulan;
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
            'id_sotk_es2'=>'required',
            'tahun'=>'required',
            'no_dokumen'=>'required',
            'tgl_dokumen'=>'required',
            'id_pegawai'=>'required',
            'nama_penandatangan'=>'required',
            'jabatan_penandatangan'=>'required',
            'pangkat_penandatangan'=>'required',
            'uraian_pangkat_penandatangan'=>'required',
            'nip_penandatangan'=>'required',
            'id_dokumen_perkin'=> 'required',
            'id_dokumen_real'=> 'required',
        ];
        $messages =[
            'id_sotk_es2.required'=>'Jabatan Eselon Kosong',
            'tahun.required'=>'Tahun Dokumen Kosong',
            'no_dokumen.required'=>'Nomor Dokumen Kosong',
            'tgl_dokumen.required'=>'Tanggal Dokumen Kosong',
            'id_pegawai.required'=>'ID Pegawai Kosong',
            'nama_penandatangan.required'=>'Nama Pegawai Kosong',
            'jabatan_penandatangan.required'=>'Jabatan Eselon Kosong',
            'pangkat_penandatangan.required'=>'Pangkat Golongan Kosong',
            'uraian_pangkat_penandatangan.required'=>'Jabatan Pegawai Kosong',
            'nip_penandatangan.required'=>'NIP Pegawai Kosong',
            'id_dokumen_perkin.required'=> 'ID Dokumen Kosong',
            'id_dokumen_real.required'=> 'ID Dokumen Realisasi Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = KinTrxRealEs2Dok::find($request->id_dokumen_real);  
            $data->id_sotk_es2= $request->id_sotk_es2;
            $data->tahun= $request->tahun;
            $data->id_dokumen_perkin= $request->id_dokumen_perkin;
            $data->no_dokumen= $request->no_dokumen;
            $data->tgl_dokumen= $request->tgl_dokumen;
            $data->triwulan= $request->triwulan;
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
            'id_dokumen_real'=> 'required',
        ];
        $messages =[
            'id_dokumen_real.required'=> 'ID Dokumen Realisasi Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = KinTrxRealEs2Dok::where('id_dokumen_real',$request->id_dokumen_real)->delete();

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
            'triwulan'=> 'required',
        ];
        $messages =[
            'tahun.required'=> 'Tahun Pengukuran Kinerja Kosong', 
            'id_sotk_es3.required'=> 'Unit Perangkat Daerah Kosong', 
            'triwulan.required'=> 'Triwulan Realisasi Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
		} else {        
            $Sasaran=DB::INSERT('INSERT INTO kin_trx_real_es2_sasaran
                (id_dokumen_real, id_perkin_sasaran, id_sasaran_renstra, status_data)                
                SELECT d.id_dokumen_real, a.id_perkin_sasaran, a.id_sasaran_renstra, 0 AS status_data
                FROM kin_trx_perkin_opd_sasaran AS a
                INNER JOIN kin_trx_perkin_opd_dok AS c ON a.id_dokumen_perkin = c.id_dokumen_perkin
                INNER JOIN kin_trx_real_es2_dok AS d ON c.id_sotk_es2 = d.id_sotk_es2 AND c.id_dokumen_perkin = d.id_dokumen_perkin AND c.tahun = d.tahun
                WHERE d.id_sotk_es2 ='.$request->id_sotk_es3.' AND d.tahun='.$request->tahun.' AND d.triwulan='.$request->triwulan);
            if($Sasaran==0){
                return response ()->json (['pesan'=>'Data Gagal Proses Sasaran Renstra ke Perjanjian Kinerja','status_pesan'=>'0']);
            } else {
                $Indikator=DB::INSERT('INSERT INTO kin_trx_real_es2_sasaran_indikator
                    (id_real_sasaran, id_perkin_indikator, id_indikator_sasaran_renstra, target_tahun, target_t1, target_t2, target_t3, target_t4, 
                    real_t1, real_t2, real_t3, real_t4, status_data)
                    SELECT  c.id_real_sasaran, x.id_perkin_indikator, x.id_indikator_sasaran_renstra, x.target_tahun, x.target_t1, x.target_t2, x.target_t3, x.target_t4,
                    0 AS real_t1, 0 AS real_t2, 0 AS real_t3, 0 AS real_t4, 0 AS status_data
                    FROM kin_trx_perkin_opd_sasaran_indikator AS x
                    INNER JOIN kin_trx_perkin_opd_sasaran AS a ON x.id_perkin_sasaran = a.id_perkin_sasaran
                    INNER JOIN kin_trx_real_es2_sasaran AS c ON a.id_perkin_sasaran = c.id_perkin_sasaran
                    INNER JOIN kin_trx_real_es2_dok AS d ON c.id_dokumen_real = d.id_dokumen_real
                    WHERE d.id_sotk_es2 ='.$request->id_sotk_es3.' AND d.tahun='.$request->tahun.' AND d.triwulan='.$request->triwulan);
                if($Indikator==0){
                    return response ()->json (['pesan'=>'Data Gagal Proses Sasaran Indikator Renstra ke Perjanjian Kinerja','status_pesan'=>'0']);
                } else {
                    return response ()->json (['pesan'=>'Data Sukses Proses Renstra ke Perjanjian Kinerja','status_pesan'=>'1']);
                }
            }  
        }         
    }
    
    public function getSasaran($id_dokumen_perkin)
    {
        $sasaran=DB::SELECT('SELECT (@id:=@id+1) AS urut,d.id_real_sasaran, d.id_dokumen_real, d.id_perkin_sasaran, d.id_sasaran_renstra, a.uraian_sasaran_renstra, d.status_data,
            (SELECT COUNT(y.id_real_indikator) FROM kin_trx_real_es2_sasaran_indikator AS y WHERE y.id_real_sasaran = d.id_real_sasaran) AS jml_indikator
            FROM trx_renstra_sasaran AS a
            INNER JOIN kin_trx_real_es2_sasaran AS d ON a.id_sasaran_renstra = d.id_sasaran_renstra
            INNER JOIN kin_trx_real_es2_dok AS e ON d.id_dokumen_real = e.id_dokumen_real,
            (SELECT @id:=0) x WHERE e.id_dokumen_real='.$id_dokumen_perkin );

        return DataTables::of($sasaran)
        ->addColumn('details_url', function($sasaran) {
            return url('real/getIndikatorSasaran/'.$sasaran->id_real_sasaran);
        })
        ->addColumn('action', function ($sasaran) {
            return '
                <button type="button" class="btn btn-info btn-sm btnDetailSasaran btn-labeled"><span class="btn-label">
                <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
            })
        ->make(true);
    }

    public function getIndikatorSasaran($id_perkin_kegiatan)
    {
        $indikator=DB::SELECT('SELECT (@id:=@id+1) AS urut,d.id_indikator_sasaran_renstra, d.id_real_sasaran, d.id_real_indikator, d.target_tahun, d.target_t1, d.target_t2, d.target_t3, d.target_t4,
            d.status_data, b.id_indikator, b.nm_indikator, b.id_satuan_output, c.uraian_satuan, d.real_t1, d.real_t2, d.real_t3, d.real_t4,
            d.real_t1,d.real_t2,d.real_t3,d.real_t4
            FROM trx_renstra_sasaran_indikator AS a
            INNER JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
            LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan
            INNER JOIN kin_trx_real_es2_sasaran_indikator AS d ON a.id_indikator_sasaran_renstra = d.id_indikator_sasaran_renstra
            INNER JOIN kin_trx_real_es2_sasaran AS e ON d.id_real_sasaran = e.id_real_sasaran
            INNER JOIN kin_trx_real_es2_dok AS f ON e.id_dokumen_real = f.id_dokumen_real,
            (SELECT @id:=0) x WHERE e.id_real_sasaran='.$id_perkin_kegiatan);

      return DataTables::of($indikator)
        ->addColumn('action', function ($indikator) {            
              return '
              <button type="button" class="btn btn-info btn-sm btnDetailIndikatorSasaran btn-labeled"><span class="btn-label">
              <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
          })
        ->make(true);
        }
    
        public function getDokRealEs3($id_unit,$tahun,$triwulan)
        {
            $dokumen = DB::select('SELECT DISTINCT (@id:=@id+1) as no_urut,a.id_dokumen_real, a.id_dokumen_perkin, a.id_sotk_es3, a.tahun, a.triwulan, a.no_dokumen, a.tgl_dokumen, a.id_pegawai, 
                a.nama_penandatangan, a.jabatan_penandatangan, a.nip_penandatangan, a.status_data, a.created_at, a.updated_at,
                b.nama_pegawai, b.nip_pegawai, a.pangkat_penandatangan, a.uraian_pangkat_penandatangan, c.id_sotk_es2, c.nama_eselon, c.tingkat_eselon,
                CASE c.tingkat_eselon 
                    WHEN 0 THEN "I"
                    WHEN 1 THEN "II"
                    WHEN 2 THEN "III"
                    WHEN 3 THEN "IV"
                ELSE "((Error))" END AS eselon_display
                FROM kin_trx_real_es3_dok AS a
                LEFT OUTER JOIN ref_pegawai AS b ON a.id_pegawai = b.id_pegawai
                LEFT OUTER JOIN ref_sotk_level_2 AS c ON a.id_sotk_es3 = c.id_sotk_es3                
                INNER JOIN ref_sotk_level_1 AS d ON c.id_sotk_es2 = d.id_sotk_es2, (SELECT @id:=0) x
                WHERE d.id_unit ='.$id_unit.' AND a.tahun='.$tahun.' AND a.triwulan='.$triwulan);
    
            return DataTables::of($dokumen)
                ->addColumn('action', function ($dokumen) {
                    return
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="btnPostingDokumenEs3 dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-primary"></i> Posting Dokumen </a>
                            </li>
                        </ul>
                    </div>';
                })
                ->make(true);
    
        }
        
    public function editIndikatorSasaran(Request $request)
        {
            $rules = [
                'id_real_indikator'=>'required',
                'real_t1'=>'required',
                'real_t2'=>'required',
                'real_t3'=>'required',
                'real_t4'=>'required',
            ];
            $messages =[
                'id_real_indikator.required'=>'ID Perjanjian Kinerja Indikator Kosong',
                'real_t1.required'=>'Realisasi Triwulan I Kosong',
                'real_t2.required'=>'Realisasi Triwulan II Kosong',
                'real_t3.required'=>'Realisasi Triwulan III Kosong',
                'real_t4.required'=>'Realisasi Triwulan IV Kosong',
            ];
            $validation = Validator::make($request->all(),$rules,$messages);
            
            if($validation->fails()) {
                $errors = Fungsi::validationErrorsToString($validation->errors());
                return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
                }
            else {
                $data = KinTrxRealEs2SasaranIndikator::find($request->id_real_indikator);  
                $data->real_t1= $request->real_t1;
                $data->real_t2= $request->real_t2;
                $data->real_t3= $request->real_t3;
                $data->real_t4= $request->real_t4;
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

        public function getIndikatorProgramEs3($id_perkin_dokumen)
        {
            $indikator=DB::SELECT('SELECT (@id:=@id+1) AS urut,d.id_indikator_program_renstra, e.id_dokumen_real,d.id_real_program, d.id_real_indikator, d.target_tahun, d.target_t1, d.target_t2, d.target_t3, d.target_t4,
                d.status_data, b.id_indikator, b.nm_indikator, b.id_satuan_output, c.uraian_satuan, d.real_t1, d.real_t2, d.real_t3, d.real_t4, d.uraian_deviasi, d.uraian_renaksi,d.reviu_real, d.reviu_deviasi, d.reviu_renaksi
                FROM trx_renstra_program_indikator AS a
                INNER JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
                LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan
                INNER JOIN kin_trx_real_es3_program_indikator AS d ON a.id_indikator_program_renstra = d.id_indikator_program_renstra
                INNER JOIN kin_trx_real_es3_program AS e ON d.id_real_program = e.id_real_program
                INNER JOIN kin_trx_real_es3_dok AS f ON e.id_dokumen_real = f.id_dokumen_real,
                (SELECT @id:=0) x WHERE e.id_dokumen_real='.$id_perkin_dokumen);
    
          return DataTables::of($indikator)
            ->addColumn('action', function ($indikator) {            
                  return '
                  <button type="button" class="btn btn-success btnDetailIndikatorKegiatan btn-labeled"><span class="btn-label">
                  <i class="fa fa-check-square-o fa-fw fa-lg"></i></span>Detail</button>';
              })
            ->make(true);
        }

        public function reviuRealisasi(Request $request)
        {
            $rules = [
                'id_real_indikator'=>'required',
                'real_reviu'=>'required',
                'reviu_deviasi'=>'required',
                'reviu_renaksi'=>'required',
                'status_data'=>'required',
            ];
            $messages =[
                'id_real_indikator.required'=>'ID Perjanjian Kinerja Indikator Kosong',
                'real_reviu.required'=>'Reviu Realisasi Kosong',
                'reviu_deviasi.required'=>'Reviu Penyebab Kosong',
                'reviu_renaksi.required'=>'Reviu Rencana Aksi Kosong',
                'status_data.required'=>'Status Reviu Kosong',
            ];
            $validation = Validator::make($request->all(),$rules,$messages);
            
            if($validation->fails()) {
                $errors = Fungsi::validationErrorsToString($validation->errors());
                return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
                }
            else {
                $data = KinTrxRealEs3ProgramIndikator::find($request->id_real_indikator);
                $data->reviu_real= $request->real_reviu;
                $data->reviu_deviasi= $request->reviu_deviasi;
                $data->reviu_renaksi= $request->reviu_renaksi;
                $data->status_data= $request->status_data;
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