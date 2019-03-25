<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use DB;
use Session;
use Auth;
use CekAkses;
use Validator;
use App\Fungsi as Fungsi;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\Can\TrxRenstraSasaran;

class TrxRenstraSasaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getSasaranRenstra($id_tujuan_renstra)
    {
        $sasaranrenstra=DB::SELECT('SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut) as kd_tujuan,
        d.thn_id,d.no_urut,d.id_tujuan_renstra,d.id_sasaran_renstra,d.id_perubahan,d.uraian_sasaran_renstra,
        COALESCE(e.uraian_sasaran_rpjmd,"Sasaran RPJMD belum dipilih") as uraian_sasaran_rpjmd, d.id_sasaran_rpjmd,
        d.sumber_data, c.uraian_tujuan_renstra
        FROM trx_renstra_visi AS a
        INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
        INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
        INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
        LEFT OUTER JOIN trx_rpjmd_sasaran AS e ON e.id_sasaran_rpjmd = d.id_sasaran_rpjmd
        WHERE d.id_tujuan_renstra='.$id_tujuan_renstra );

        return DataTables::of($sasaranrenstra)
        ->addColumn('details_url', function($sasaranrenstra) {
            return url('renstra/getIndikatorSasaran/'.$sasaranrenstra->id_sasaran_renstra);
        })
        ->addColumn('action', function ($sasaranrenstra) {
            return 
            '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a class="btnDetailSasaran dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Detail Data Sasaran</a>
                    </li>
                    <li>
                        <a class="view-renstrakebijakan dropdown-item" data-id_sasaran="'.$sasaranrenstra->id_sasaran_renstra.'"><i class="fa fa-gavel fa-fw fa-lg text-warning"></i> Lihat Data Kebijakan</a>
                    </li>
                    <li>
                        <a class="view-renstrastrategi dropdown-item" data-id_sasaran="'.$sasaranrenstra->id_sasaran_renstra.'"><i class="fa fa-map-o fa-fw fa-lg text-danger"></i> Lihat Data Strategi</a>
                    </li>
                    <li>
                        <a class="btnAddSasaranIndikator dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Sasaran</a>
                    </li>
                </ul>
              </div>'
              ;})
        ->make(true);
    }

    public function getSasaranRPJMD($id_unit)
    {
        $getSasaranRpjmd=DB::select('SELECT DISTINCT (@id:=@id+1) AS urut, y.*,3 as level_rpjmd FROM
            (SELECT DISTINCT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) as kd_sasaran, d.id_sasaran_rpjmd, d.uraian_sasaran_rpjmd 
            FROM trx_rpjmd_visi AS a
            INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd
            INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd
            INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd
            INNER JOIN trx_rpjmd_program AS f ON d.id_sasaran_rpjmd = f.id_sasaran_rpjmd 
            INNER JOIN trx_rpjmd_program_urusan AS g ON f.id_program_rpjmd = g.id_program_rpjmd
            INNER JOIN trx_rpjmd_program_pelaksana AS h ON g.id_urbid_rpjmd = h.id_urbid_rpjmd
            WHERE h.id_unit =  '.$id_unit.'
            GROUP BY h.id_unit,a.no_urut,b.no_urut,c.no_urut,d.no_urut,d.id_sasaran_rpjmd, d.uraian_sasaran_rpjmd ) AS y, (SELECT @id:=0) x');        
        return DataTables::of($getSasaranRpjmd)->make(true);
    }

    public function getCariTujuanRenstra($id_unit)
    {
        $getCariTujuanRenstra=DB::select('SELECT (@id:=@id+1) AS urut, CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut) AS kd_misi, c.thn_id,c.no_urut,
                c.id_misi_renstra,c.id_tujuan_renstra,c.id_perubahan,c.uraian_tujuan_renstra,c.sumber_data,3 as level_item
                FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra,(SELECT @id:=0) x
                WHERE a.id_unit='.$id_unit);        
        return DataTables::of($getCariTujuanRenstra)->make(true);
    }

    public function addSasaran(Request $request)
    {
        $rules = [
            'thn_id' => 'required',
            'no_urut' => 'required',
            'id_tujuan_renstra'=> 'required',
            'id_perubahan' => 'required',
            'uraian_sasaran_renstra' => 'required',
            'id_sasaran_rpjmd' => 'required',
        ];
        $messages =[
            'thn_id.required' => 'Tahun Kosong',
            'no_urut.required' => 'No Urut Kosong',
            'id_tujuan_renstra.required'=> 'No Sasaran Kosong',
            'id_perubahan.required' => 'Id Perubahan Kosong',
            'uraian_sasaran_renstra.required' => 'Uraian Sasaran Renstra Kosong',
            'id_sasaran_rpjmd.required' => 'Uraian Sasaran RPJMD Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new TrxRenstraSasaran();
            $data->thn_id= $request->thn_id;
            $data->no_urut= $request->no_urut;
            $data->id_tujuan_renstra= $request->id_tujuan_renstra;
            $data->id_perubahan= $request->id_perubahan;
            $data->uraian_sasaran_renstra= $request->uraian_sasaran_renstra;
            $data->id_sasaran_rpjmd= $request->id_sasaran_rpjmd;
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
    
    public function editSasaran(Request $request)
    {
        $rules = [
            'thn_id' => 'required',
            'no_urut' => 'required',
            'id_tujuan_renstra'=> 'required',
            'id_perubahan' => 'required',
            'uraian_sasaran_renstra' => 'required',
            'id_sasaran_rpjmd' => 'required',
        ];
        $messages =[
            'thn_id.required' => 'Tahun Kosong',
            'no_urut.required' => 'No Urut Kosong',
            'id_tujuan_renstra.required'=> 'No Sasaran Kosong',
            'id_perubahan.required' => 'Id Perubahan Kosong',
            'uraian_sasaran_renstra.required' => 'Uraian Sasaran Renstra Kosong',
            'id_sasaran_rpjmd.required' => 'Uraian Sasaran RPJMD Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = TrxRenstraSasaran::find($request->id_sasaran_renstra);
            $data->thn_id= $request->thn_id;
            $data->no_urut= $request->no_urut;
            $data->id_tujuan_renstra= $request->id_tujuan_renstra;
            $data->id_perubahan= $request->id_perubahan;
            $data->uraian_sasaran_renstra= $request->uraian_sasaran_renstra;
            $data->id_sasaran_rpjmd= $request->id_sasaran_rpjmd;
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

    public function delSasaran(Request $request){
        $rules = [
            'id_sasaran_renstra' => 'required',
        ];
        $messages =[
            'id_sasaran_renstra.required' => 'Id Sasaran Renstra Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = TrxRenstraSasaran::where('id_sasaran_renstra',$request->id_sasaran_renstra)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }
}
