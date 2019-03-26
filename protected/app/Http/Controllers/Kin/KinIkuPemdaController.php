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
use App\Models\Kin\KinTrxIkuPemdaDok;
use App\Models\Kin\KinTrxIkuPemdaRinci;


class KinIkuPemdaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
	{
		// if(Auth::check()){ 
		    return view("kin.iku.iku_pemda.FrmIkuIndex");			
		// } else {
			// return view ( 'errors.401' );
		// }	
    }

    public function getUnit(Request $request){
        $unit = DB::SELECT('SELECT DISTINCT a.id_unit, a.nm_unit FROM ref_unit AS a
        INNER JOIN trx_renstra_visi AS b ON a.id_unit = b.id_unit
        INNER JOIN trx_renstra_misi AS c ON b.id_visi_renstra = c.id_visi_renstra
        INNER JOIN trx_renstra_tujuan AS d ON c.id_misi_renstra = d.id_misi_renstra
        INNER JOIN trx_renstra_sasaran AS e ON d.id_tujuan_renstra = e.id_tujuan_renstra
        WHERE e.id_sasaran_rpjmd='.$request->id);
        return json_encode($unit);
    }

    public function getDokumen()
    {
        $dokumen = DB::select('SELECT DISTINCT (@id:=@id+1) as no_urut, a.id_dokumen, a.no_dokumen, a.tgl_dokumen, a.uraian_dokumen, a.id_rpjmd, 
            a.id_perubahan, a.status_dokumen, a.created_at, a.updated_at
            FROM kin_trx_iku_pemda_dok AS a, (SELECT @id:=0) x');

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
            'id_rpjmd'=>'required',
        ];
        $messages =[
            'no_dokumen.required'=>'Nomor Dokumen Kosong',
            'tgl_dokumen.required'=>'Tanggal Dokumen Kosong',
            'uraian_dokumen.required'=>'Uraian Dokumen Kosong',
            'id_rpjmd.required'=>'nomor RPJMD Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new KinTrxIkuPemdaDok();
            $data->no_dokumen= $request->no_dokumen;
            $data->tgl_dokumen= $request->tgl_dokumen;
            $data->uraian_dokumen= $request->uraian_dokumen;
            $data->id_rpjmd= $request->id_rpjmd;
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
            'id_rpjmd'=>'required',
        ];
        $messages =[
            'no_dokumen.required'=>'Nomor Dokumen Kosong',
            'tgl_dokumen.required'=>'Tanggal Dokumen Kosong',
            'uraian_dokumen.required'=>'Uraian Dokumen Kosong',
            'id_rpjmd.required'=>'nomor RPJMD Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = KinTrxIkuPemdaDok::find($request->id_dokumen);  
            $data->no_dokumen= $request->no_dokumen;
            $data->tgl_dokumen= $request->tgl_dokumen;
            $data->uraian_dokumen= $request->uraian_dokumen;
            $data->id_rpjmd= $request->id_rpjmd;
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
            $data = KinTrxIkuPemdaDok::where('id_dokumen',$request->id_dokumen)->delete();

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
            'id_rpjmd'=> 'required',
        ];
        $messages =[
            'id_dokumen.required'=> 'No Dokumen Kosong Kosong', 
            'id_rpjmd.required'=> 'No RPJMD Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $Sasaran=DB::INSERT('INSERT INTO kin_trx_iku_pemda_rinci
                (id_dokumen, id_indikator_sasaran_rpjmd, id_indikator, flag_iku, status_data)
                SELECT '.$request->id_dokumen.', a.id_indikator_sasaran_rpjmd, a.kd_indikator, 0, 0
                FROM trx_rpjmd_sasaran_indikator AS a
                INNER JOIN trx_rpjmd_sasaran AS b ON a.id_sasaran_rpjmd = b.id_sasaran_rpjmd
                INNER JOIN trx_rpjmd_tujuan AS c ON b.id_tujuan_rpjmd = c.id_tujuan_rpjmd
                INNER JOIN trx_rpjmd_misi AS d ON c.id_misi_rpjmd = d.id_misi_rpjmd
                INNER JOIN trx_rpjmd_visi AS e ON d.id_visi_rpjmd = e.id_visi_rpjmd 
                LEFT OUTER JOIN kin_trx_iku_pemda_rinci AS p ON a.id_indikator_sasaran_rpjmd = p.id_indikator_sasaran_rpjmd
                WHERE p.id_iku_pemda IS NULL AND e.id_rpjmd ='.$request->id_rpjmd);
            if($Sasaran==0){
                return response ()->json (['pesan'=>'Data Gagal Proses','status_pesan'=>'0']);
            } else {
                return response ()->json (['pesan'=>'Data Sukses Proses','status_pesan'=>'1']);
            }
        }       
    }
    
    public function getSasaran($id_dokumen_perkin)
    {
        $sasaran=DB::SELECT('SELECT (@id:=@id+1) AS urut, b.id_sasaran_rpjmd, b.uraian_sasaran_rpjmd,
            SUM(IF(p.flag_iku = 0 , 1, 0)) AS jml_indikator_non, SUM(IF(p.flag_iku = 1 , 1, 0)) AS jml_indikator_iku
            FROM kin_trx_iku_pemda_rinci AS p
            INNER JOIN trx_rpjmd_sasaran_indikator AS a ON p.id_indikator_sasaran_rpjmd = a.id_indikator_sasaran_rpjmd
            INNER JOIN trx_rpjmd_sasaran AS b ON a.id_sasaran_rpjmd = b.id_sasaran_rpjmd, (SELECT @id:=0) x
            WHERE p.id_dokumen='.$id_dokumen_perkin.' GROUP BY b.id_sasaran_rpjmd, b.uraian_sasaran_rpjmd' );

        return DataTables::of($sasaran)
        ->addColumn('details_url', function($sasaran) {
            return url('iku/getIndikatorSasaran/'.$sasaran->id_sasaran_rpjmd);
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
        $indikator=DB::SELECT('SELECT (@id:=@id+1) AS urut, p.id_iku_pemda,p.id_dokumen,p.id_indikator_sasaran_rpjmd,p.id_indikator,p.flag_iku,p.status_data,
        a.angka_akhir_periode, b.nm_indikator, b.sumber_data_indikator, b.id_satuan_output, c.uraian_satuan, b.kualitas_indikator,b.metode_penghitungan,
        b.type_indikator, b.sifat_indikator, b.jenis_indikator,a.id_sasaran_rpjmd, p.unit_penanggung_jawab, COALESCE(d.nm_unit,"Belum Dipilih") AS nama_unit,
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
           WHEN 1 THEN "IKU Pemda"
        END AS flag_display
            FROM kin_trx_iku_pemda_rinci AS p
            INNER JOIN trx_rpjmd_sasaran_indikator AS a ON p.id_indikator_sasaran_rpjmd = a.id_indikator_sasaran_rpjmd
            INNER JOIN ref_indikator AS b ON p.id_indikator = b.id_indikator
            LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan
            LEFT OUTER JOIN ref_unit AS d ON p.unit_penanggung_jawab = d.id_unit,
            (SELECT @id:=0) x  WHERE a.id_sasaran_rpjmd='.$id_perkin_sasaran);

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
                'id_iku_pemda'=>'required',
                'flag_iku'=>'required',
                'unit_penanggung_jawab'=>'required',
            ];
            $messages =[
                'id_iku_pemda.required'=>'ID IKU Rinci Kosong',
                'flag_iku.required'=>'Flag Pemilihan IKU Kosong',
                'unit_penanggung_jawab.required'=>'Unit Penanggung Jawab IKU belum dipilih',
            ];
            $validation = Validator::make($request->all(),$rules,$messages);
            
            if($validation->fails()) {
                $errors = Fungsi::validationErrorsToString($validation->errors());
                return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
                }
            else {
                $data = KinTrxIkuPemdaRinci::find($request->id_iku_pemda);  
                $data->flag_iku= $request->flag_iku;
                $data->unit_penanggung_jawab= $request->unit_penanggung_jawab;
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