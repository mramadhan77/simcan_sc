<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use DB;
use Response;
use Session;
use Auth;
use CekAkses;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\Can\TrxRpjmdProgramIndikator;

class TrxRpjmdProgramIndikatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getIndikatorProgram($id_program_rpjmd)
    {
      $indikatorProg=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.thn_id, a.no_urut, a.id_program_rpjmd, a.id_indikator_program_rpjmd, 
      a.id_perubahan, a.id_indikator, a.uraian_indikator_program_rpjmd,
      a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2, 
      a.angka_tahun3, a.angka_tahun4, a.angka_tahun5, a.angka_akhir_periode,
      COALESCE(b.nm_indikator,"N/A") AS nm_indikator,COALESCE(c.uraian_satuan,"N/A") AS uraian_satuan
      FROM trx_rpjmd_program_indikator AS a
      LEFT OUTER JOIN ref_indikator AS b ON a.id_indikator = b.id_indikator
      LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan,(SELECT @id:=0) x  WHERE a.id_program_rpjmd='.$id_program_rpjmd);

      return DataTables::of($indikatorProg)
        ->addColumn('action', function ($indikatorProg) {            
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditIndikatorProgram dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Lihat Indikator</a>
                        </li>
                        <li>
                            <a class="btnHapusIndikatorProgram dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Indikator</a>
                        </li>
                    </ul>
                    </div>
                ';
          })
        ->make(true); 
    }

    public function addIndikator(Request $req)
    {
        
            $data = new TrxRpjmdProgramIndikator();
            $data->thn_id= $req->thn_id;
            $data->no_urut= $req->no_urut;
            $data->id_program_rpjmd= $req->id_program_rpjmd;
            $data->id_perubahan= $req->id_perubahan;
            $data->id_indikator= $req->kd_indikator;
            $data->uraian_indikator_program_rpjmd= $req->uraian_indikator_program_rpjmd;
            $data->tolok_ukur_indikator= $req->tolok_ukur_indikator;
            $data->angka_awal_periode= $req->angka_awal_periode;
            $data->angka_tahun1= $req->angka_tahun1;
            $data->angka_tahun2= $req->angka_tahun2;
            $data->angka_tahun3= $req->angka_tahun3;
            $data->angka_tahun4= $req->angka_tahun4;
            $data->angka_tahun5= $req->angka_tahun5;
            $data->angka_akhir_periode= $req->angka_akhir_periode;
            try{
                $data->save (['timestamps' => true]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        
    }

    
    public function editIndikator(Request $req)
    {
    	$data = TrxRpjmdProgramIndikator::find($req->id_indikator_program_rpjmd);
        $data->thn_id= $req->thn_id;
        $data->no_urut= $req->no_urut;
        $data->id_program_rpjmd= $req->id_program_rpjmd;
        $data->id_perubahan= $req->id_perubahan;
        $data->id_indikator= $req->kd_indikator;
        $data->uraian_indikator_program_rpjmd= $req->uraian_indikator_program_rpjmd;
        $data->tolok_ukur_indikator= $req->tolok_ukur_indikator;
        $data->angka_awal_periode= $req->angka_awal_periode;
        $data->angka_tahun1= $req->angka_tahun1;
        $data->angka_tahun2= $req->angka_tahun2;
        $data->angka_tahun3= $req->angka_tahun3;
        $data->angka_tahun4= $req->angka_tahun4;
        $data->angka_tahun5= $req->angka_tahun5;
        $data->angka_akhir_periode= $req->angka_akhir_periode;
    	try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }
    

    public function delIndikator(Request $req){
        
        $data = TrxRpjmdProgramIndikator::where('id_indikator_program_rpjmd',$req->id_indikator_program_rpjmd)->delete();

        if($data != 0){
          return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        } else {
          return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
        }   
    }
    
}
