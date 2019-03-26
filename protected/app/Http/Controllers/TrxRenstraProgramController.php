<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Auth;
use CekAkses;
use Validator;
use App\Fungsi as Fungsi;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\Can\TrxRenstraProgram;

class TrxRenstraProgramController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getProgramRenstra($id_sasaran_renstra)
    {
        $programrenstra=DB::select('SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) AS kd_sasaran, a.thn_id,
                f.uraian_program_rpjmd as nm_program_rpjmd,e.id_program_renstra,e.no_urut,e.id_sasaran_renstra,e.uraian_sasaran_program,d.id_sasaran_rpjmd,
                e.uraian_program_renstra,g.uraian_program as nm_program,e.id_program_rpjmd,e.id_program_ref,d.uraian_sasaran_renstra,e.id_perubahan,
                (e.pagu_tahun1) AS pagu_tahun1a,
                (e.pagu_tahun2) AS pagu_tahun2a,
                (e.pagu_tahun3) AS pagu_tahun3a,
                (e.pagu_tahun4) AS pagu_tahun4a,
                (e.pagu_tahun5) AS pagu_tahun5a,
                (e.pagu_tahun1+e.pagu_tahun2+e.pagu_tahun3+e.pagu_tahun4+e.pagu_tahun5) AS pagu_tahun6a,  
                (e.pagu_tahun1/1000000) AS pagu_tahun1,
                (e.pagu_tahun2/1000000) AS pagu_tahun2,
                (e.pagu_tahun3/1000000) AS pagu_tahun3,
                (e.pagu_tahun4/1000000) AS pagu_tahun4,
                (e.pagu_tahun5/1000000) AS pagu_tahun5,
				CONCAT(h.kd_urusan,".",h.kd_bidang,".",g.kd_program) AS kd_program  
                FROM trx_renstra_visi AS a 
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra 
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra 
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra 
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra 
                INNER JOIN trx_rpjmd_program AS f ON e.id_program_rpjmd = f.id_program_rpjmd 
                INNER JOIN ref_program AS g ON e.id_program_ref = g.id_program
                INNER JOIN ref_bidang AS h ON g.id_bidang = h.id_bidang 
                WHERE e.id_sasaran_renstra='.$id_sasaran_renstra.' ORDER BY kd_sasaran, e.no_urut');

        return DataTables::of($programrenstra)
        ->addColumn('details_url', function($programrenstra) {
            return url('renstra/getIndikatorProgram/'.$programrenstra->id_program_renstra);
        })
        ->addColumn('action', function ($programrenstra) {
            return 
            '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a class="btnDetailProgram dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Detail Data Program</a>
                    </li>
                    <li>
                        <a class="btnAddProgramIndikator dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Program</a>
                    </li>
                </ul>
              </div>'
              ;})
        ->make(true);
    }

    public function getProgramRPJMD($id_renstra)
    {
        $getProgramRPJMD=DB::SELECT('SELECT DISTINCT (@id:=@id+1) AS urut, y.*,4 as level_rpjmd FROM
            (SELECT DISTINCT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut,".",f.no_urut) as kd_program, f.id_program_rpjmd, f.uraian_program_rpjmd,d.id_sasaran_rpjmd 
            FROM trx_rpjmd_visi AS a
            INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd
            INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd
            INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd
            INNER JOIN trx_rpjmd_program AS f ON d.id_sasaran_rpjmd = f.id_sasaran_rpjmd 
            WHERE d.id_sasaran_rpjmd =  '.$id_renstra.' 
            GROUP BY a.no_urut,b.no_urut,c.no_urut,d.no_urut,f.no_urut,d.id_sasaran_rpjmd,f.id_program_rpjmd, f.uraian_program_rpjmd ) AS y, (SELECT @id:=0) x');        
        return DataTables::of($getProgramRPJMD)->make(true);
    }

    public function getCariSasaranRenstra($id_unit)
    {
        $getCariSasaranRenstra=DB::select('SELECT (@id:=@id+1) AS urut, y.*,4 as level_item FROM
                (SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) AS kd_sasaran, d.id_sasaran_renstra,d.uraian_sasaran_renstra,d.id_sasaran_rpjmd
                FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON a.id_visi_renstra = b.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON b.id_misi_renstra = c.id_misi_renstra
                INNER JOIN trx_renstra_sasaran as d ON c.id_tujuan_renstra =d.id_tujuan_renstra
                WHERE a.id_unit='.$id_unit.'
                GROUP BY a.id_unit,a.no_urut,b.no_urut,c.no_urut,d.no_urut,d.id_sasaran_renstra,d.uraian_sasaran_renstra) as y,(SELECT @id:=0) x');        
        return DataTables::of($getCariSasaranRenstra)->make(true);
    }

    public function getBidangRef($id_unit,$id_program)
    {
        $getBidangRef=DB::SELECT('SELECT  CONCAT(b.kd_urusan,".",b.kd_bidang," ",b.nm_bidang) as nm_bidang_display, b.id_bidang, b.nm_bidang 
            FROM ref_bidang AS b 
            INNER JOIN trx_rpjmd_program_urusan AS c ON b.id_bidang = c.id_bidang
            INNER JOIN trx_rpjmd_program_pelaksana AS d ON c.id_urbid_rpjmd = d.id_urbid_rpjmd
            WHERE d.id_unit = '.$id_unit.' AND c.id_program_rpjmd = '.$id_program.'
            GROUP BY b.kd_urusan,b.kd_bidang,b.nm_bidang,d.id_unit,c.id_program_rpjmd'); 
        return json_encode($getBidangRef);
    }

    public function getProgramRef($id_program)
    {
        $getProgramRef=DB::SELECT('SELECT DISTINCT (@id:=@id+1) AS no_urut, y.* 
            FROM (SELECT  CONCAT(b.kd_urusan,".",b.kd_bidang,".",a.kd_program) as kd_program_display, b.nm_bidang, a.id_program,a.uraian_program 
            FROM ref_program AS a
            INNER JOIN ref_bidang AS b ON a.id_bidang = b.id_bidang
            WHERE a.id_bidang = '.$id_program.') AS y, (SELECT @id:=0) x');        
        return DataTables::of($getProgramRef)->make(true);
    }

    public function addProgram(Request $request)
    {
        $rules = [
            'thn_id' => 'required', 
            'no_urut' => 'required', 
            'id_sasaran_renstra' => 'required', 
            'id_program_rpjmd' => 'required', 
            'id_program_ref' => 'required', 
            'id_perubahan' => 'required', 
            'uraian_program_renstra' => 'required', 
            'uraian_sasaran_program' => 'required', 
            'pagu_tahun1' => 'required', 
            'pagu_tahun2' => 'required', 
            'pagu_tahun3' => 'required', 
            'pagu_tahun4' => 'required', 
            'pagu_tahun5' => 'required', 
        ];
        $messages =[
            'thn_id.required' => 'Tahun Kosong', 
            'no_urut.required' => 'No Urut Kosong', 
            'id_sasaran_renstra.required' => 'Sasaran Renstra Kosong', 
            'id_program_rpjmd.required' => 'Program RPJMD Kosong', 
            'id_program_ref.required' => 'Program Refrensi Permendagri Kosong', 
            'id_perubahan.required' => 'Perubahan Kosong', 
            'uraian_program_renstra.required' => 'Uraian Program Kosong', 
            'uraian_sasaran_program.required' => 'Sasaran Program Kosong', 
            'pagu_tahun1.required' => 'Pagu Tahun ke 1 Kosong', 
            'pagu_tahun2.required' => 'Pagu Tahun ke 2 Kosong', 
            'pagu_tahun3.required' => 'Pagu Tahun ke 3 Kosong', 
            'pagu_tahun4.required' => 'Pagu Tahun ke 4 Kosong', 
            'pagu_tahun5.required' => 'Pagu Tahun ke 5 Kosong', 
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new TrxRenstraProgram();
            $data->thn_id= $request->thn_id;
            $data->no_urut= $request->no_urut;
            $data->id_sasaran_renstra= $request->id_sasaran_renstra;
            $data->id_program_rpjmd= $request->id_program_rpjmd;            
            $data->id_program_ref= $request->id_program_ref;
            $data->id_perubahan= $request->id_perubahan;
            $data->uraian_program_renstra= $request->uraian_program_renstra;
            $data->uraian_sasaran_program= $request->uraian_sasaran_program;
            $data->pagu_tahun1= $request->pagu_tahun1;
            $data->pagu_tahun2= $request->pagu_tahun2;
            $data->pagu_tahun3= $request->pagu_tahun3;
            $data->pagu_tahun4= $request->pagu_tahun4;
            $data->pagu_tahun5= $request->pagu_tahun5;
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

    
    public function editProgram(Request $request)
    {
        $rules = [
            'thn_id' => 'required', 
            'no_urut' => 'required', 
            'id_sasaran_renstra' => 'required', 
            'id_program_renstra' => 'required', 
            'id_program_rpjmd' => 'required', 
            'id_program_ref' => 'required', 
            'id_perubahan' => 'required', 
            'uraian_program_renstra' => 'required', 
            'uraian_sasaran_program' => 'required', 
            'pagu_tahun1' => 'required', 
            'pagu_tahun2' => 'required', 
            'pagu_tahun3' => 'required', 
            'pagu_tahun4' => 'required', 
            'pagu_tahun5' => 'required', 
        ];
        $messages =[
            'thn_id.required' => 'Tahun Kosong', 
            'no_urut.required' => 'No Urut Kosong', 
            'id_sasaran_renstra.required' => 'Sasaran Renstra Kosong', 
            'id_program_renstra.required' => 'Program Renstra Kosong', 
            'id_program_rpjmd.required' => 'Program RPJMD Kosong', 
            'id_program_ref.required' => 'Program Refrensi Permendagri Kosong', 
            'id_perubahan.required' => 'Perubahan Kosong', 
            'uraian_program_renstra.required' => 'Uraian Program Kosong', 
            'uraian_sasaran_program.required' => 'Sasaran Program Kosong', 
            'pagu_tahun1.required' => 'Pagu Tahun ke 1 Kosong', 
            'pagu_tahun2.required' => 'Pagu Tahun ke 2 Kosong', 
            'pagu_tahun3.required' => 'Pagu Tahun ke 3 Kosong', 
            'pagu_tahun4.required' => 'Pagu Tahun ke 4 Kosong', 
            'pagu_tahun5.required' => 'Pagu Tahun ke 5 Kosong', 
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = TrxRenstraProgram::find($request->id_program_renstra);
            $data->thn_id= $request->thn_id;
            $data->no_urut= $request->no_urut;
            $data->id_sasaran_renstra= $request->id_sasaran_renstra;
            $data->id_program_rpjmd= $request->id_program_rpjmd;            
            $data->id_program_ref= $request->id_program_ref;
            $data->id_perubahan= $request->id_perubahan;
            $data->uraian_program_renstra= $request->uraian_program_renstra;
            $data->uraian_sasaran_program= $request->uraian_sasaran_program;
            $data->pagu_tahun1= $request->pagu_tahun1;
            $data->pagu_tahun2= $request->pagu_tahun2;
            $data->pagu_tahun3= $request->pagu_tahun3;
            $data->pagu_tahun4= $request->pagu_tahun4;
            $data->pagu_tahun5= $request->pagu_tahun5;
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
    

    public function delProgram(Request $request){
        $rules = [
            'id_program_renstra' => 'required',
        ];
        $messages =[
            'id_program_renstra.required' => 'Program Renstra Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = TrxRenstraProgram::where('id_program_renstra',$request->id_program_renstra)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }
}
