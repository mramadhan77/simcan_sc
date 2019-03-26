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
use Validator;
use App\Fungsi as Fungsi;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\Can\TrxRenstraKegiatanIndikator;

class TrxRenstraKegiatanIndikatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getIndikatorKegiatan($id_kegiatan_renstra)
    {
        $indikatorProg=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.thn_id, a.no_urut, a.id_kegiatan_renstra, a.id_indikator_kegiatan_renstra, 
        a.id_perubahan, a.kd_indikator, a.uraian_indikator_kegiatan_renstra,
        a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2, 
        a.angka_tahun3, a.angka_tahun4, a.angka_tahun5, a.angka_akhir_periode,
        COALESCE(b.nm_indikator,"N/A") AS nm_indikator,COALESCE(c.uraian_satuan,"N/A") AS uraian_satuan
        FROM trx_renstra_kegiatan_indikator AS a
        LEFT OUTER JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
        LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan,(SELECT @id:=0) x  WHERE a.id_kegiatan_renstra='.$id_kegiatan_renstra);

      return DataTables::of($indikatorProg)
        ->addColumn('action', function ($indikatorProg) {            
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditIndikatorKegiatan dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Lihat Indikator</a>
                        </li>
                        <li>
                            <a class="btnHapusIndikatorKegiatan dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Indikator</a>
                        </li>
                    </ul>
                    </div>
                ';
          })
        ->make(true);
    }

    public function addIndikator(Request $request)
    {
        $rules = [
            'thn_id' => 'required',
            'no_urut' => 'required',
            'id_kegiatan_renstra'=> 'required',
            'id_perubahan' => 'required',
            'kd_indikator' => 'required',
            'angka_awal_periode' => 'required',
            'angka_tahun1' => 'required',
            'angka_tahun2' => 'required',
            'angka_tahun3' => 'required',
            'angka_tahun4' => 'required',
            'angka_tahun5' => 'required',
            'angka_akhir_periode' => 'required',
        ];
        $messages =[
            'thn_id.required' => 'Tahun Kosong',
            'no_urut.required' => 'No Urut Kosong',
            'id_kegiatan_renstra.required'=> 'No Kegiatan Kosong',
            'id_perubahan.required' => 'Id Perubahan Kosong',
            'kd_indikator.required' => 'Uraian Indikator Kosong',
            'angka_awal_periode.required' => 'Angka Awal Kosong',
            'angka_tahun1.required' => 'Target Tahun 1 Kosong',
            'angka_tahun2.required' => 'Target Tahun 2 Kosong',
            'angka_tahun3.required' => 'Target Tahun 3 Kosong',
            'angka_tahun4.required' => 'Target Tahun 4 Kosong',
            'angka_tahun5.required' => 'Target Tahun 5 Kosong',
            'angka_akhir_periode.required' => 'Angka Akhir Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new TrxRenstraKegiatanIndikator();
            $data->thn_id= $request->thn_id;
            $data->no_urut= $request->no_urut;
            $data->id_kegiatan_renstra= $request->id_kegiatan_renstra;
            $data->id_perubahan= $request->id_perubahan;
            $data->kd_indikator= $request->kd_indikator;
            $data->uraian_indikator_kegiatan_renstra= $request->uraian_indikator_kegiatan_renstra;
            $data->tolok_ukur_indikator= $request->tolok_ukur_indikator;
            $data->angka_awal_periode= $request->angka_awal_periode;
            $data->angka_tahun1= $request->angka_tahun1;
            $data->angka_tahun2= $request->angka_tahun2;
            $data->angka_tahun3= $request->angka_tahun3;
            $data->angka_tahun4= $request->angka_tahun4;
            $data->angka_tahun5= $request->angka_tahun5;
            $data->angka_akhir_periode= $request->angka_akhir_periode;
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

    
    public function editIndikator(Request $request)
    {
        $rules = [
            'id_indikator_kegiatan_renstra' => 'required',
            'thn_id' => 'required',
            'no_urut' => 'required',
            'id_kegiatan_renstra'=> 'required',
            'id_perubahan' => 'required',
            'kd_indikator' => 'required',
            'angka_awal_periode' => 'required',
            'angka_tahun1' => 'required',
            'angka_tahun2' => 'required',
            'angka_tahun3' => 'required',
            'angka_tahun4' => 'required',
            'angka_tahun5' => 'required',
            'angka_akhir_periode' => 'required',
        ];
        $messages =[
            'id_indikator_kegiatan_renstra.required' => 'Id Indikator Kosong',
            'thn_id.required' => 'Tahun Kosong',
            'no_urut.required' => 'No Urut Kosong',
            'id_kegiatan_renstra.required'=> 'No Kegiatan Kosong',
            'id_perubahan.required' => 'Id Perubahan Kosong',
            'kd_indikator.required' => 'Uraian Indikator Kosong',
            'angka_awal_periode.required' => 'Angka Awal Kosong',
            'angka_tahun1.required' => 'Target Tahun 1 Kosong',
            'angka_tahun2.required' => 'Target Tahun 2 Kosong',
            'angka_tahun3.required' => 'Target Tahun 3 Kosong',
            'angka_tahun4.required' => 'Target Tahun 4 Kosong',
            'angka_tahun5.required' => 'Target Tahun 5 Kosong',
            'angka_akhir_periode.required' => 'Angka Akhir Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = TrxRenstraKegiatanIndikator::find($request->id_indikator_kegiatan_renstra);
            $data->thn_id= $request->thn_id;
            $data->no_urut= $request->no_urut;
            $data->id_kegiatan_renstra= $request->id_kegiatan_renstra;
            $data->id_perubahan= $request->id_perubahan;
            $data->kd_indikator= $request->kd_indikator;
            $data->uraian_indikator_kegiatan_renstra= $request->uraian_indikator_kegiatan_renstra;
            $data->tolok_ukur_indikator= $request->tolok_ukur_indikator;
            $data->angka_awal_periode= $request->angka_awal_periode;
            $data->angka_tahun1= $request->angka_tahun1;
            $data->angka_tahun2= $request->angka_tahun2;
            $data->angka_tahun3= $request->angka_tahun3;
            $data->angka_tahun4= $request->angka_tahun4;
            $data->angka_tahun5= $request->angka_tahun5;
            $data->angka_akhir_periode= $request->angka_akhir_periode;
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
    

    public function delIndikator(Request $request){
        $rules = [
            'id_indikator_kegiatan_renstra' => 'required',
        ];
        $messages =[
            'id_indikator_kegiatan_renstra.required' => 'Id Indikator Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = TrxRenstraKegiatanIndikator::where('id_indikator_kegiatan_renstra',$request->id_indikator_kegiatan_renstra)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }
    

}
