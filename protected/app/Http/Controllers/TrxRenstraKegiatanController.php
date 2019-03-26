<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Validator;
use DB;
use Datatables;
use Session;
use Auth;
use CekAkses;
use App\Fungsi as Fungsi;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\Can\TrxRenstraKegiatan;

class TrxRenstraKegiatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getKegiatanRenstra($id_program_renstra)
    {
      $kegiatanrenstra=DB::select('SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut,".",e.no_urut) AS kd_program, 
                CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut,".",e.no_urut," ",e.uraian_program_renstra) AS nm_program,f.no_urut, a.thn_id,
                f.id_kegiatan_renstra,g.nm_kegiatan,f.id_kegiatan_ref,f.uraian_kegiatan_renstra as ur_kegiatan,g.kd_kegiatan,f.id_program_renstra,
                (f.pagu_tahun1/1000000) AS pagu_tahun1,(f.pagu_tahun2/1000000) AS pagu_tahun2,(f.pagu_tahun3/1000000) AS pagu_tahun3,
                (f.pagu_tahun4/1000000) AS pagu_tahun4,(f.pagu_tahun5/1000000) AS pagu_tahun5,
                (f.pagu_tahun1) AS pagu_tahun1a,(f.pagu_tahun2) AS pagu_tahun2a,(f.pagu_tahun3) AS pagu_tahun3a,
                (f.pagu_tahun4) AS pagu_tahun4a,(f.pagu_tahun5) AS pagu_tahun5a,
                ((f.pagu_tahun1+f.pagu_tahun2+f.pagu_tahun3+f.pagu_tahun4+f.pagu_tahun5)/1000000) AS total_pagu,
                (f.pagu_tahun1+f.pagu_tahun2+f.pagu_tahun3+f.pagu_tahun4+f.pagu_tahun5) AS pagu_tahun6a,
				CONCAT(h.kd_urusan,".",h.kd_bidang,".",i.kd_program,".",g.kd_kegiatan) AS kd_kegiatan, f.uraian_sasaran_kegiatan, f.id_perubahan  
                FROM trx_renstra_visi AS a 
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra 
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra 
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra 
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra 
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra 
                INNER JOIN ref_kegiatan AS g ON f.id_kegiatan_ref = g.id_kegiatan 
                INNER JOIN ref_program AS i ON g.id_program = i.id_program
                INNER JOIN ref_bidang AS h ON i.id_bidang = h.id_bidang 
                WHERE f.id_program_renstra='.$id_program_renstra.' ORDER BY kd_program, f.no_urut');

      return DataTables::of($kegiatanrenstra)
      ->addColumn('details_url', function($kegiatanrenstra) {
            return url('renstra/getIndikatorKegiatan/'.$kegiatanrenstra->id_kegiatan_renstra);
        })
      ->addColumn('action', function ($kegiatanrenstra) {
                  return '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                </button>
            		<ul class="dropdown-menu dropdown-menu-right">
            			<li>
                            <a class="btnDetailKegiatan dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i></i> Detail Data Kegiatan</a>
                        </li>
                        <li>    
                            <a class="view-kegiatanpelaksana dropdown-item" data-id_kegiatan="'.$kegiatanrenstra->id_kegiatan_renstra.'" ><i class="fa fa-users fa-fw fa-lg text-warning"></i></i> Lihat Pelaksana Kegiatan</a>
                        </li>
                        <li>
                            <a class="btnAddKegiatanIndikator dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Kegiatan</a>
                        </li>
            		</ul>
            </div>';})
          ->make(true);
    }

    public function getKegiatanRef($id_program)
    {
       $KegRef=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.id_kegiatan, a.id_program, a.kd_kegiatan, a.nm_kegiatan,
            CONCAT(LEFT(CONCAT(0,d.kd_urusan),2),".",RIGHT(CONCAT(0,c.kd_bidang),2),".",RIGHT(CONCAT("00",b.kd_program),3),".",RIGHT(CONCAT("00",a.kd_kegiatan),3)) AS kd_kegiatan
            FROM ref_kegiatan a
            INNER JOIN ref_program b ON a.id_program=b.id_program
            INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
            INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan,(SELECT @id:=0) x
            WHERE a.id_program='.$id_program);

       return DataTables::of($KegRef)->make(true);
    }

    public function addKegiatan(Request $request)
    {
        $rules = [
            'thn_id' => 'required', 
            'no_urut' => 'required', 
            'id_program_renstra' => 'required', 
            'id_kegiatan_ref' => 'required', 
            'id_perubahan' => 'required', 
            'uraian_kegiatan_renstra' => 'required', 
            'uraian_sasaran_kegiatan' => 'required', 
            'pagu_tahun1' => 'required', 
            'pagu_tahun2' => 'required', 
            'pagu_tahun3' => 'required', 
            'pagu_tahun4' => 'required', 
            'pagu_tahun5' => 'required',  
            'total_pagu' => 'required',
        ];

        $messages =[
            'thn_id.required' => 'Tahun Kosong', 
            'no_urut.required' => 'No Urut Kosong', 
            'id_program_renstra.required' => 'Program Renstra Kosong', 
            'id_kegiatan_ref.required' => 'Kegiatan Refrensi Permendagri Kosong', 
            'id_perubahan.required' => 'Perubahan Kosong', 
            'uraian_kegiatan_renstra.required' => 'Uraian Program Kosong', 
            'uraian_sasaran_kegiatan.required' => 'Sasaran Program Kosong', 
            'pagu_tahun1.required' => 'Pagu Tahun ke 1 Kosong', 
            'pagu_tahun2.required' => 'Pagu Tahun ke 2 Kosong', 
            'pagu_tahun3.required' => 'Pagu Tahun ke 3 Kosong', 
            'pagu_tahun4.required' => 'Pagu Tahun ke 4 Kosong', 
            'pagu_tahun5.required' => 'Pagu Tahun ke 5 Kosong', 
            'total_pagu.required' => 'Pagu Total Kosong', 
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new TrxRenstraKegiatan();
            $data->thn_id= $request->thn_id;
            $data->no_urut= $request->no_urut;
            $data->id_program_renstra= $request->id_program_renstra;
            $data->id_kegiatan_ref= $request->id_kegiatan_ref;  
            $data->id_perubahan= $request->id_perubahan;
            $data->uraian_kegiatan_renstra= $request->uraian_kegiatan_renstra;
            $data->uraian_sasaran_kegiatan= $request->uraian_sasaran_kegiatan;
            $data->pagu_tahun1= $request->pagu_tahun1;
            $data->pagu_tahun2= $request->pagu_tahun2;
            $data->pagu_tahun3= $request->pagu_tahun3;
            $data->pagu_tahun4= $request->pagu_tahun4;
            $data->pagu_tahun5= $request->pagu_tahun5;          
            $data->total_pagu= $request->total_pagu;
            $data->sumber_data= 1;
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

    
    public function editKegiatan(Request $request)
    {
        $rules = [
            'thn_id' => 'required', 
            'no_urut' => 'required', 
            'id_program_renstra' => 'required', 
            'id_kegiatan_renstra' => 'required', 
            'id_kegiatan_ref' => 'required', 
            'id_perubahan' => 'required', 
            'uraian_kegiatan_renstra' => 'required', 
            'uraian_sasaran_kegiatan' => 'required', 
            'pagu_tahun1' => 'required', 
            'pagu_tahun2' => 'required', 
            'pagu_tahun3' => 'required', 
            'pagu_tahun4' => 'required', 
            'pagu_tahun5' => 'required',  
            'total_pagu' => 'required',
        ];

        $messages =[
            'thn_id.required' => 'Tahun Kosong', 
            'no_urut.required' => 'No Urut Kosong', 
            'id_program_renstra.required' => 'Program Renstra Kosong', 
            'id_kegiatan_renstra.required' => 'Kegiatan Renstra Kosong', 
            'id_kegiatan_ref.required' => 'Kegiatan Refrensi Permendagri Kosong', 
            'id_perubahan.required' => 'Perubahan Kosong', 
            'uraian_kegiatan_renstra.required' => 'Uraian Program Kosong', 
            'uraian_sasaran_kegiatan.required' => 'Sasaran Program Kosong', 
            'pagu_tahun1.required' => 'Pagu Tahun ke 1 Kosong', 
            'pagu_tahun2.required' => 'Pagu Tahun ke 2 Kosong', 
            'pagu_tahun3.required' => 'Pagu Tahun ke 3 Kosong', 
            'pagu_tahun4.required' => 'Pagu Tahun ke 4 Kosong', 
            'pagu_tahun5.required' => 'Pagu Tahun ke 5 Kosong', 
            'total_pagu.required' => 'Pagu Total Kosong',  
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = TrxRenstraKegiatan::find($request->id_kegiatan_renstra);
            $data->thn_id= $request->thn_id;
            $data->no_urut= $request->no_urut;
            $data->id_program_renstra= $request->id_program_renstra;
            $data->id_kegiatan_ref= $request->id_kegiatan_ref;  
            $data->id_perubahan= $request->id_perubahan;
            $data->uraian_kegiatan_renstra= $request->uraian_kegiatan_renstra;
            $data->uraian_sasaran_kegiatan= $request->uraian_sasaran_kegiatan;
            $data->pagu_tahun1= $request->pagu_tahun1;
            $data->pagu_tahun2= $request->pagu_tahun2;
            $data->pagu_tahun3= $request->pagu_tahun3;
            $data->pagu_tahun4= $request->pagu_tahun4;
            $data->pagu_tahun5= $request->pagu_tahun5;          
            $data->total_pagu= $request->total_pagu;
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
    

    public function delKegiatan(Request $request){
        $rules = [
            'id_kegiatan_renstra' => 'required',
        ];
        $messages =[
            'id_kegiatan_renstra.required' => 'Kegiatan Renstra Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = TrxRenstraKegiatan::where('id_kegiatan_renstra',$request->id_kegiatan_renstra)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }
}
