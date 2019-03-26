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
use App\Models\Kin\KinTrxIkuOpdDok;
use App\Models\Kin\KinTrxIkuOpdSasaran;
use App\Models\Kin\KinTrxIkuOpdProgram;
use App\Models\Kin\KinTrxIkuOpdKegiatan;


class KinIkuOpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
	{
		// if(Auth::check()){ 
		    return view("kin.iku.iku_unit.FrmIkuIndex");			
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

    public function getDokumen($id_unit)
    {
        $dokumen = DB::select('SELECT DISTINCT (@id:=@id+1) as no_urut, a.id_dokumen, a.no_dokumen, a.tgl_dokumen, a.uraian_dokumen, a.id_renstra, 
            a.id_perubahan, a.status_dokumen, a.created_at, a.updated_at, a.id_unit
            FROM kin_trx_iku_opd_dok AS a, (SELECT @id:=0) x
            WHERE a.id_unit='.$id_unit);

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

    public function addDokumen(Request $request)
    {
        $rules = [
            'no_dokumen'=>'required',
            'tgl_dokumen'=>'required',
            'uraian_dokumen'=>'required',
            'id_renstra'=>'required',
            'id_unit'=>'required',
        ];
        $messages =[
            'no_dokumen.required'=>'Nomor Dokumen Kosong',
            'tgl_dokumen.required'=>'Tanggal Dokumen Kosong',
            'uraian_dokumen.required'=>'Uraian Dokumen Kosong',
            'id_renstra.required'=>'Nomor Renstra Kosong',
            'id_unit.required'=>'Nama Unit IKU Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new KinTrxIkuOpdDok();
            $data->no_dokumen= $request->no_dokumen;
            $data->tgl_dokumen= $request->tgl_dokumen;
            $data->uraian_dokumen= $request->uraian_dokumen;
            $data->id_renstra= $request->id_renstra;
            $data->id_unit= $request->id_unit;
            $data->id_perubahan= 0;
            $data->status_dokumen= 0;
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
            'no_dokumen'=>'required',
            'tgl_dokumen'=>'required',
            'uraian_dokumen'=>'required',
            'id_renstra'=>'required',
            'id_unit'=>'required',
        ];
        $messages =[
            'no_dokumen.required'=>'Nomor Dokumen Kosong',
            'tgl_dokumen.required'=>'Tanggal Dokumen Kosong',
            'uraian_dokumen.required'=>'Uraian Dokumen Kosong',
            'id_renstra.required'=>'Nomor Renstra Kosong',
            'id_unit.required'=>'Nama Unit IKU Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = KinTrxIkuOpdDok::find($request->id_dokumen);  
            $data->no_dokumen= $request->no_dokumen;
            $data->tgl_dokumen= $request->tgl_dokumen;
            $data->uraian_dokumen= $request->uraian_dokumen;
            $data->id_renstra= $request->id_renstra;
            $data->id_unit= $request->id_unit;
            $data->id_perubahan= 0;
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
            'id_dokumen'=> 'required',
        ];
        $messages =[
            'id_dokumen.required'=> 'ID Dokumen Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = KinTrxIkuOpdDok::where('id_dokumen',$request->id_dokumen)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }


    public function transIndikatorSasaran(Request $request)
    {
        $rules = [
            'id_dokumen'=> 'required',
            'id_renstra'=> 'required',
            'id_unit'=> 'required',
        ];
        $messages =[
            'id_dokumen.required'=> 'No Dokumen Kosong Kosong', 
            'id_renstra.required'=> 'No Renstra Kosong',   
            'id_unit.required'=> 'Unit Kosong',             
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $Sasaran=DB::INSERT('INSERT INTO kin_trx_iku_opd_sasaran 
                (id_dokumen, id_indikator_sasaran_renstra, id_sasaran_renstra, id_indikator, flag_iku, status_data)
                SELECT '.$request->id_dokumen.', a.id_indikator_sasaran_renstra, a.id_sasaran_renstra, a.kd_indikator, 0 , 0  
                FROM trx_renstra_sasaran_indikator AS a
                INNER JOIN trx_renstra_sasaran AS b ON a.id_sasaran_renstra = b.id_sasaran_renstra
                INNER JOIN trx_renstra_tujuan AS c ON b.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_misi AS d ON c.id_misi_renstra = d.id_misi_renstra
                INNER JOIN trx_renstra_visi AS e ON d.id_visi_renstra = e.id_visi_renstra
                LEFT OUTER JOIN kin_trx_iku_opd_sasaran AS p ON a.id_indikator_sasaran_renstra = p.id_indikator_sasaran_renstra
                WHERE p.id_iku_opd_sasaran IS NULL AND e.id_unit = '.$request->id_unit.' AND e.id_renstra = '.$request->id_renstra);
            if($Sasaran==0){
                return response ()->json (['pesan'=>'Data Gagal Proses (0)','status_pesan'=>'0']);
            } else {
                $Program=DB::INSERT('INSERT INTO kin_trx_iku_opd_program
                    (id_iku_opd_sasaran, id_indikator_program_renstra, id_program_renstra, id_indikator,id_esl3, flag_iku, status_data)
                    SELECT DISTINCT p.id_iku_opd_sasaran, a.id_indikator_program_renstra, a.id_program_renstra, a.kd_indikator, 0, 0 , 0  
                    FROM trx_renstra_program_indikator AS a
                    INNER JOIN trx_renstra_program AS f ON a.id_program_renstra = f.id_program_renstra
                    INNER JOIN (SELECT min(id_iku_opd_sasaran) AS id_iku_opd_sasaran, id_dokumen, id_sasaran_renstra FROM kin_trx_iku_opd_sasaran 
                    GROUP BY id_dokumen, id_sasaran_renstra) AS p ON f.id_sasaran_renstra = p.id_sasaran_renstra
                    INNER JOIN kin_trx_iku_opd_dok AS q ON p.id_dokumen = q.id_dokumen
                    LEFT OUTER JOIN kin_trx_iku_opd_program AS x ON a.id_indikator_program_renstra = x.id_indikator_program_renstra
                    WHERE x.id_iku_opd_program IS NULL AND q.id_dokumen = '.$request->id_dokumen.' AND q.id_unit = '.$request->id_unit.' AND q.id_renstra = '.$request->id_renstra);
                if($Program==0){
                    return response ()->json (['pesan'=>'Data Gagal Proses (1)','status_pesan'=>'0']);
                } else {
                    $Kegiatan=DB::INSERT('INSERT INTO kin_trx_iku_opd_kegiatan
                        (id_iku_opd_program, id_indikator_kegiatan_renstra, id_kegiatan_renstra, id_indikator, flag_iku, id_esl4, status_data)
                        SELECT r.id_iku_opd_program, a.id_indikator_kegiatan_renstra, a.id_kegiatan_renstra, a.kd_indikator, 0, 0 , 0  
                        FROM trx_renstra_kegiatan_indikator AS a
                        INNER JOIN trx_renstra_kegiatan AS g ON a.id_kegiatan_renstra = g.id_kegiatan_renstra
                        INNER JOIN (SELECT min(id_iku_opd_program) AS id_iku_opd_program, id_iku_opd_sasaran, id_program_renstra FROM kin_trx_iku_opd_program 
                        GROUP BY id_iku_opd_sasaran, id_program_renstra)  AS r ON g.id_program_renstra = r.id_program_renstra
                        INNER JOIN (SELECT min(id_iku_opd_sasaran) AS id_iku_opd_sasaran, id_dokumen, id_sasaran_renstra FROM kin_trx_iku_opd_sasaran 
                        GROUP BY id_dokumen, id_sasaran_renstra) AS p ON r.id_iku_opd_sasaran = p.id_iku_opd_sasaran
                        INNER JOIN kin_trx_iku_opd_dok AS q ON p.id_dokumen = q.id_dokumen
                        LEFT OUTER JOIN kin_trx_iku_opd_kegiatan AS x ON a.id_indikator_kegiatan_renstra = x.id_indikator_kegiatan_renstra
                        WHERE x.id_iku_opd_kegiatan IS NULL AND q.id_dokumen = '.$request->id_dokumen.' AND q.id_unit = '.$request->id_unit.' AND q.id_renstra = '.$request->id_renstra);
                    if($Kegiatan==0){
                        return response ()->json (['pesan'=>'Data Gagal Proses (2)','status_pesan'=>'0']);
                    } else {
                        return response ()->json (['pesan'=>'Data Sukses Proses','status_pesan'=>'1']);
                    }
                }
            }
        }       
    }
    
    public function getSasaran($id_dokumen_perkin)
    {
        $sasaran=DB::SELECT('SELECT (@id:=@id+1) AS urut, p.id_sasaran_renstra, a.uraian_sasaran_renstra,
            SUM(IF(p.flag_iku = 0 , 1, 0)) AS jml_indikator_non, SUM(IF(p.flag_iku = 1 , 1, 0)) AS jml_indikator_iku
            FROM kin_trx_iku_opd_sasaran AS p
            INNER JOIN trx_renstra_sasaran AS a ON p.id_sasaran_renstra = a.id_sasaran_renstra, (SELECT @id:=0) x
            WHERE p.id_dokumen='.$id_dokumen_perkin.' GROUP BY p.id_sasaran_renstra, a.uraian_sasaran_renstra' );

        return DataTables::of($sasaran)
        ->addColumn('details_url', function($sasaran) {
            return url('iku/opd/getIndikatorSasaran/'.$sasaran->id_sasaran_renstra);
        })
        ->addColumn('action', function ($sasaran) {
            return '
                <button type="button" class="btn btn-info btn-sm btnDetailSasaran btn-labeled"><span class="btn-label">
                <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
            })
        ->make(true);
    }

    public function getIndikatorSasaran($id_perkin_sasaran)
    {
        $indikator=DB::SELECT('SELECT (@id:=@id+1) AS urut, p.id_iku_opd_sasaran,p.id_dokumen,p.id_indikator_sasaran_renstra,p.id_indikator,p.flag_iku,p.status_data,
            a.angka_akhir_periode, b.nm_indikator, b.sumber_data_indikator, b.id_satuan_output, c.uraian_satuan, b.kualitas_indikator,b.metode_penghitungan,
            b.type_indikator, b.sifat_indikator, b.jenis_indikator,a.id_sasaran_renstra, 
            CASE b.kualitas_indikator 
                WHEN 0 THEN "Output"
                WHEN 1 THEN "Outcome Immediate"
                WHEN 2 THEN "Outcome Intermediate"
                WHEN 3 THEN "Outcome Ultimate"
            END AS kualitas_indikator_display,
            CASE b.type_indikator 
                WHEN 0 THEN "Kualitas"
                WHEN 1 THEN "Kuantitatif"
                WHEN 2 THEN "Persentase"
                WHEN 3 THEN "Rasio"
                WHEN 4 THEN "Rata-rata"
                WHEN 5 THEN "Indeks"
            END AS type_indikator_display,
            CASE b.jenis_indikator 
                WHEN 0 THEN "Negatif"
                WHEN 1 THEN "Positif"
            END AS jenis_indikator_display,
            CASE b.sifat_indikator 
                WHEN 0 THEN "Incremental"
                WHEN 1 THEN "Absolut"
                WHEN 2 THEN "Komulatif"
            END AS sifat_indikator_display,
            CASE p.flag_iku
                WHEN 0 THEN "fa fa-times"
                WHEN 1 THEN "fa fa-check-square-o"
            END AS status_icon,
            CASE p.flag_iku
                WHEN 0 THEN "red"
                WHEN 1 THEN "green"
            END AS warna,
            CASE p.flag_iku
                WHEN 0 THEN "Bukan IKU"
                WHEN 1 THEN "IKU OPD"
            END AS flag_display
            FROM kin_trx_iku_opd_sasaran AS p
            INNER JOIN trx_renstra_sasaran_indikator AS a ON p.id_indikator_sasaran_renstra = a.id_indikator_sasaran_renstra
            INNER JOIN ref_indikator AS b ON p.id_indikator = b.id_indikator
            LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan,
            (SELECT @id:=0) x  WHERE a.id_sasaran_renstra='.$id_perkin_sasaran);

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
                'id_iku_opd_sasaran'=>'required',
                'flag_iku'=>'required',
            ];
            $messages =[
                'id_iku_opd_sasaran.required'=>'ID IKU Rinci Kosong',
                'flag_iku.required'=>'Flag Pemilihan IKU Kosong',
            ];
            $validation = Validator::make($request->all(),$rules,$messages);
            
            if($validation->fails()) {
                $errors = Fungsi::validationErrorsToString($validation->errors());
                return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
                }
            else {
                $data = KinTrxIkuOpdSasaran::find($request->id_iku_opd_sasaran);  
                $data->flag_iku= $request->flag_iku;
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
        
        public function getProgram($id_dokumen_perkin)
        {
            $program=DB::SELECT('SELECT (@id:=@id+1) AS urut, p.id_program_renstra, a.uraian_program_renstra, b.uraian_program,
			    CONCAT(c.kd_urusan,".",RIGHT(CONCAT(0,c.kd_bidang),2),".",RIGHT(CONCAT(0,b.kd_program),2)) AS kd_program,                
                SUM(IF(p.flag_iku = 0 , 1, 0)) AS jml_indikator_non, SUM(IF(p.flag_iku = 1 , 1, 0)) AS jml_indikator_iku
                FROM kin_trx_iku_opd_program AS p
                INNER JOIN trx_renstra_program AS a ON p.id_program_renstra = a.id_program_renstra
                INNER JOIN ref_program AS b ON a.id_program_ref=b.id_program
                INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang, (SELECT @id:=0) x
                WHERE a.id_sasaran_renstra='.$id_dokumen_perkin.' GROUP BY p.id_program_renstra, a.uraian_program_renstra, b.uraian_program, 
                c.kd_urusan, c.kd_bidang, b.kd_program'  );
    
            return DataTables::of($program)
            ->addColumn('details_url', function($program) {
                return url('iku/opd/getIndikatorProgram/'.$program->id_program_renstra);
            })
            ->addColumn('action', function ($program) {
                return '
                    <button type="button" class="btn btn-info btn-sm btnDetailProgram btn-labeled"><span class="btn-label">
                    <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
                })
            ->make(true);
        }
    
        public function getIndikatorProgram($id_perkin_sasaran)
        {
            $indikator=DB::SELECT('SELECT (@id:=@id+1) AS urut, p.id_iku_opd_sasaran,p.id_iku_opd_program,p.id_indikator_program_renstra,p.id_indikator,p.flag_iku,p.status_data,
                a.angka_akhir_periode, b.nm_indikator, b.sumber_data_indikator, b.id_satuan_output, c.uraian_satuan, b.kualitas_indikator,b.metode_penghitungan,
                b.type_indikator, b.sifat_indikator, b.jenis_indikator,a.id_program_renstra, p.id_esl3, 
                COALESCE(d.nama_eselon,"Bidang/Bagian Belum Dipilih") AS nama_esl3,
                CASE b.kualitas_indikator 
                    WHEN 0 THEN "Output"
                    WHEN 1 THEN "Outcome Immediate"
                    WHEN 2 THEN "Outcome Intermediate"
                    WHEN 3 THEN "Outcome Ultimate"
                END AS kualitas_indikator_display,
                CASE b.type_indikator 
                    WHEN 0 THEN "Kualitas"
                    WHEN 1 THEN "Kuantitatif"
                    WHEN 2 THEN "Persentase"
                    WHEN 3 THEN "Rasio"
                    WHEN 4 THEN "Rata-rata"
                    WHEN 5 THEN "Indeks"
                END AS type_indikator_display,
                CASE b.jenis_indikator 
                    WHEN 0 THEN "Negatif"
                    WHEN 1 THEN "Positif"
                END AS jenis_indikator_display,
                CASE b.sifat_indikator 
                    WHEN 0 THEN "Incremental"
                    WHEN 1 THEN "Absolut"
                    WHEN 2 THEN "Komulatif"
                END AS sifat_indikator_display,
                CASE p.flag_iku
                    WHEN 0 THEN "fa fa-times"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_icon,
                CASE p.flag_iku
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna,
                CASE p.flag_iku
                    WHEN 0 THEN "Bukan IKU"
                    WHEN 1 THEN "IKU OPD"
                END AS flag_display
                FROM kin_trx_iku_opd_program AS p
                INNER JOIN trx_renstra_program_indikator AS a ON p.id_indikator_program_renstra = a.id_indikator_program_renstra
                INNER JOIN ref_indikator AS b ON p.id_indikator = b.id_indikator
                LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan
                LEFT OUTER JOIN ref_sotk_level_2 AS d ON p.id_esl3=d.id_sotk_es3,
                (SELECT @id:=0) x  WHERE a.id_program_renstra='.$id_perkin_sasaran);
    
            return DataTables::of($indikator)
            ->addColumn('action', function ($indikator) {            
                  return '
                  <button type="button" class="btn btn-info btn-sm btnDetailIndikatorProgram btn-labeled"><span class="btn-label">
                  <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
              })
            ->make(true);
        }

        public function getKegiatan($id_dokumen_perkin)
        {
            $program=DB::SELECT('SELECT (@id:=@id+1) AS urut, p.id_kegiatan_renstra, a.uraian_kegiatan_renstra, d.kd_kegiatan,
                    CONCAT(c.kd_urusan,".",RIGHT(CONCAT(0,c.kd_bidang),2),".",RIGHT(CONCAT(0,b.kd_program),2),".",RIGHT(CONCAT("00",d.kd_kegiatan),2)) AS kd_kegiatan,                
                    SUM(IF(p.flag_iku = 0 , 1, 0)) AS jml_indikator_non, SUM(IF(p.flag_iku = 1 , 1, 0)) AS jml_indikator_iku
                    FROM kin_trx_iku_opd_kegiatan AS p
                    INNER JOIN trx_renstra_kegiatan AS a ON p.id_kegiatan_renstra = a.id_kegiatan_renstra
                    INNER JOIN ref_kegiatan AS d ON a.id_kegiatan_ref = d.id_kegiatan
                    INNER JOIN ref_program AS b ON d.id_program = b.id_program
                    INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang, (SELECT @id:=0) x
                    WHERE a.id_program_renstra='.$id_dokumen_perkin.' GROUP BY p.id_kegiatan_renstra, a.uraian_kegiatan_renstra, d.kd_kegiatan,
                    c.kd_urusan, c.kd_bidang, b.kd_program,d.kd_kegiatan' );
        
            return DataTables::of($program)
                ->addColumn('details_url', function($program) {
                    return url('iku/opd/getIndikatorKegiatan/'.$program->id_kegiatan_renstra);
                })
                ->addColumn('action', function ($program) {
                    return '
                        <button type="button" class="btn btn-info btn-sm btnDetailKegiatan btn-labeled"><span class="btn-label">
                        <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
                    })
                ->make(true);
        }
        
        public function getIndikatorKegiatan($id_perkin_sasaran)
        {
            $indikator=DB::SELECT('SELECT DISTINCT (@id:=@id+1) AS urut, p.id_iku_opd_kegiatan,p.id_iku_opd_program,p.id_indikator_kegiatan_renstra,p.id_indikator,p.flag_iku,p.status_data,
                    a.angka_akhir_periode, b.nm_indikator, b.sumber_data_indikator, b.id_satuan_output, c.uraian_satuan, b.kualitas_indikator,b.metode_penghitungan,
                    b.type_indikator, b.sifat_indikator, b.jenis_indikator,a.id_kegiatan_renstra, p.id_esl4, 
                    COALESCE(d.nama_eselon,"Sub Bidang/Bagian Belum Dipilih") AS nama_esl4,
                    CASE b.kualitas_indikator 
                        WHEN 0 THEN "Output"
                        WHEN 1 THEN "Outcome Immediate"
                        WHEN 2 THEN "Outcome Intermediate"
                        WHEN 3 THEN "Outcome Ultimate"
                    END AS kualitas_indikator_display,
                    CASE b.type_indikator 
                        WHEN 0 THEN "Kualitas"
                        WHEN 1 THEN "Kuantitatif"
                        WHEN 2 THEN "Persentase"
                        WHEN 3 THEN "Rasio"
                        WHEN 4 THEN "Rata-rata"
                        WHEN 5 THEN "Indeks"
                    END AS type_indikator_display,
                    CASE b.jenis_indikator 
                        WHEN 0 THEN "Negatif"
                        WHEN 1 THEN "Positif"
                    END AS jenis_indikator_display,
                    CASE b.sifat_indikator 
                        WHEN 0 THEN "Incremental"
                        WHEN 1 THEN "Absolut"
                        WHEN 2 THEN "Komulatif"
                    END AS sifat_indikator_display,
                    CASE p.flag_iku
                        WHEN 0 THEN "fa fa-times"
                        WHEN 1 THEN "fa fa-check-square-o"
                    END AS status_icon,
                    CASE p.flag_iku
                        WHEN 0 THEN "red"
                        WHEN 1 THEN "green"
                    END AS warna,
                    CASE p.flag_iku
                        WHEN 0 THEN "Bukan IKU"
                        WHEN 1 THEN "IKU OPD"
                    END AS flag_display
                    FROM kin_trx_iku_opd_kegiatan AS p
                    INNER JOIN trx_renstra_kegiatan_indikator AS a ON p.id_indikator_kegiatan_renstra = a.id_indikator_kegiatan_renstra
                    INNER JOIN ref_indikator AS b ON p.id_indikator = b.id_indikator
                    LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan
                    LEFT OUTER JOIN ref_sotk_level_3 AS d ON p.id_esl4=d.id_sotk_es4,
                    (SELECT @id:=0) x  WHERE a.id_kegiatan_renstra='.$id_perkin_sasaran);
        
            return DataTables::of($indikator)
            ->addColumn('action', function ($indikator) {            
                return '
                    <button type="button" class="btn btn-info btn-sm btnDetailIndikatorKegiatan btn-labeled"><span class="btn-label">
                    <i class="fa fa-list-alt fa-fw fa-lg"></i></span>Detail</button>';
                })
            ->make(true);
        }

        public function editIndikatorProgram(Request $request)
        {
            $rules = [
                'id_iku_opd_program'=>'required',
                'flag_iku'=>'required',
                'id_esl3'=>'required',
            ];
            $messages =[
                'id_iku_opd_program.required'=>'ID IKU Rinci Kosong',
                'flag_iku.required'=>'Flag Pemilihan IKU Kosong',
                'id_esl3.required'=>'Bidang/Bagian Kosong',
            ];
            $validation = Validator::make($request->all(),$rules,$messages);
                    
            if($validation->fails()) {
                $errors = Fungsi::validationErrorsToString($validation->errors());
                return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
                }
            else {
                $data = KinTrxIkuOpdProgram::find($request->id_iku_opd_program);  
                $data->flag_iku= $request->flag_iku;
                $data->id_esl3= $request->id_esl3;
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
        
        public function editIndikatorKegiatan(Request $request)
        {
            $rules = [
                'id_iku_opd_kegiatan'=>'required',
                'flag_iku'=>'required',
                'id_esl4'=>'required',
            ];
            $messages =[
                'id_iku_opd_kegiatan.required'=>'ID IKU Rinci Kosong',
                'flag_iku.required'=>'Flag Pemilihan IKU Kosong',
                'id_esl4.required'=>'Seksi/Sub Bidang/Sub Bagian Kosong',
            ];
            $validation = Validator::make($request->all(),$rules,$messages);
                    
            if($validation->fails()) {
                $errors = Fungsi::validationErrorsToString($validation->errors());
                return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
                }
            else {
                $data = KinTrxIkuOpdKegiatan::find($request->id_iku_opd_kegiatan);  
                $data->flag_iku= $request->flag_iku;
                $data->id_esl4= $request->id_esl4;
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
    
        public function getEselon3($id_eselon2)
        {
            $getJabatan=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es3, a.id_sotk_es2, a.nama_eselon, a.tingkat_eselon, b.id_unit,
            CASE a.tingkat_eselon 
                WHEN 0 THEN "I"
                WHEN 1 THEN "II"
                WHEN 2 THEN "III"
                WHEN 3 THEN "IV"
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
                WHEN 0 THEN "I"
                WHEN 1 THEN "II"
                WHEN 2 THEN "III"
                WHEN 3 THEN "IV"
            ELSE "((Error))" END AS eselon_display
            FROM ref_sotk_level_3 AS a
            INNER JOIN ref_sotk_level_2 AS b ON a.id_sotk_es3=b.id_sotk_es3
            INNER JOIN ref_sotk_level_1 AS c ON b.id_sotk_es2=c.id_sotk_es2,(SELECT @id:=0) x
            WHERE c.id_unit='.$id_eselon3);
            return json_encode($getJabatan);
        }
        
}