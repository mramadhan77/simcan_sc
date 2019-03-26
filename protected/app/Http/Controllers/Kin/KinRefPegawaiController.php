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

use App\Models\Kin\RefPegawai;
use App\Models\Kin\RefPegawaiPangkat;
use App\Models\Kin\RefPegawaiUnit;


class KinRefPegawaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        // if(Auth::check()){ 
            return view('kin.parameter.FrmPegawaiIndex');
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function getPegawai()
    {
        $getPegawai=DB::SELECT('SELECT (@id:=@id+1) AS urut,id_pegawai, nama_pegawai, nip_pegawai, status_pegawai, created_at, updated_at
            FROM ref_pegawai,(SELECT @id:=0) x');

      return DataTables::of($getPegawai)
        ->addColumn('details_url', function($getPegawai) {
            return url('kinparam/pegawai/getPegawaiPangkat/'.$getPegawai->id_pegawai);
        })
        ->addColumn('action', function ($getPegawai) {            
              return 
                '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a class="btnDetailPegawai dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Detail Data</a>
                    </li>
                    <li>
                        <a class="btnDelPegawai dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Data</a>
                    </li>
                    
                </ul>
                </div>'
                ;
          })
        ->make(true); 
    }

    // <li>
    //                     <a class="btnTambahPangkat dropdown-item" ><i class="fa fa-plus fa-fw fa-lg text-primary"></i> Tambah Pangkat</a>
    //                 </li>

    public function getPegawaiPangkat($id_pegawai)
    {
        $getPegawaiPangkat=DB::SELECT('SELECT (@id:=@id+1) AS urut, a.id_pangkat, a.id_pegawai, a.pangkat_pegawai, 
            a.tmt_pangkat, a.created_at, a.updated_at, b.nama_pegawai, b.nip_pegawai,
            CASE pangkat_pegawai
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
            WHERE a.id_pegawai='.$id_pegawai);

        return DataTables::of($getPegawaiPangkat)
        ->addColumn('action', function ($getPegawaiPangkat) {            
              return 
                '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a class="btnDetailPangkat dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Detail Data</a>
                    </li>
                    <li>
                        <a class="btnDelRiwayat dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Data</a>
                    </li>
                </ul>
                </div>'
                ;
          })
        ->make(true); 
    }

    public function getPegawaiUnit($id_pegawai)
    {
        $getPegawaiPangkat=DB::SELECT('SELECT b.id_unit_pegawai, a.id_pegawai, a.nip_pegawai, a.nama_pegawai, b.id_unit, b.tingkat_eselon, b.id_sotk,  c.id_sotk, b.tmt_unit, 
            b.nama_jabatan, c.nama_eselon AS nama_jabatan, d.nm_unit, a.created_at, a.updated_at
            FROM ref_pegawai as a
            INNER JOIN ref_pegawai_unit as b ON a.id_pegawai = b.id_pegawai
            INNER JOIN ref_unit AS d ON b.id_unit = d.id_unit
            INNER JOIN ( SELECT  0 as tingkat_eselon, a.id_unit, a.id_sotk_es2 as id_sotk,  a.nama_eselon FROM ref_sotk_level_1 AS a
                UNION SELECT  1 as tingkat_eselon, a.id_unit, b.id_sotk_es3 as id_sotk,  b.nama_eselon FROM ref_sotk_level_1 AS a
                INNER JOIN ref_sotk_level_2 AS b ON a.id_sotk_es2 = b.id_sotk_es2 
                UNION SELECT  2 as tingkat_eselon, a.id_unit, c.id_sotk_es4 as id_sotk,  c.nama_eselon FROM ref_sotk_level_1 AS a
                INNER JOIN ref_sotk_level_2 AS b ON a.id_sotk_es2 = b.id_sotk_es2
                INNER JOIN ref_sotk_level_3 AS c ON b.id_sotk_es3 = c.id_sotk_es3 ) c 
            ON b.id_sotk = c.id_sotk AND b.id_unit = c.id_unit AND b.tingkat_eselon = c.tingkat_eselon
            WHERE a.id_pegawai='.$id_pegawai);

        return DataTables::of($getPegawaiPangkat)        
        ->addIndexColumn()
        ->addColumn('action', function ($getPegawaiPangkat) {            
              return 
                '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a class="btnDetailUnit dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Detail Data</a>
                    </li>
                    <li>
                        <a class="btnDelRiwayatUnit dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Data</a>
                    </li>
                </ul>
                </div>'
                ;
          })
        ->make(true); 
    }

    public function addPegawai(Request $request)
    {
        $rules = [
            'nama_pegawai' => 'required',
            'nip_pegawai' => 'required|unique:ref_pegawai|max:18|min:18',
            'status_pegawai'=> 'required',
        ];
        $messages =[
            'nama_pegawai.required' => 'Nama Pegawai Kosong',
            'nip_pegawai.required' => 'NIP Pegawai Kosong',
            'nip_pegawai.unique' => 'NIP Pegawai Sudah Dipakai',            
            'nip_pegawai.max' => 'NIP Pegawai harus 18 angka',           
            'nip_pegawai.min' => 'NIP Pegawai harus 18 angka',
            'status_pegawai.required'=> 'Status Pegawai Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new RefPegawai();
            $data->nama_pegawai= $request->nama_pegawai;
            $data->nip_pegawai= $request->nip_pegawai;
            $data->status_pegawai= $request->status_pegawai;
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
    
    public function editPegawai(Request $request)
    {
        $rules = [
            'nama_pegawai' => 'required',
            'nip_pegawai' => 'required|max:18|min:18',
            'status_pegawai'=> 'required',
            'id_pegawai'=> 'required',
        ];
        $messages =[
            'nama_pegawai.required' => 'Nama Pegawai Kosong',
            'nip_pegawai.required' => 'NIP Pegawai Kosong',            
            'nip_pegawai.max' => 'NIP Pegawai harus 18 angka',           
            'nip_pegawai.min' => 'NIP Pegawai harus 18 angka',
            'status_pegawai.required'=> 'Status Pegawai Kosong',
            'id_pegawai.required'=> 'ID Pegawai Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = RefPegawai::find($request->id_pegawai);
            $data->nama_pegawai= $request->nama_pegawai;
            $data->nip_pegawai= $request->nip_pegawai;
            $data->status_pegawai= $request->status_pegawai;
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

    public function delPegawai(Request $request){
        $rules = [
            'id_pegawai'=> 'required',
        ];
        $messages =[
            'id_pegawai.required'=> 'ID Pegawai Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = RefPegawai::where('id_pegawai',$request->id_pegawai)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

    public function jenis_pangkat()
    {
       $getJenis=DB::SELECT('select 0 as id, "Pilih Pangkat / Golongan" as uraian_pangkat, "" as uraian_golongan
            union
            select 9 as id, "Penata Muda" as uraian_pangkat, "III/a" as uraian_golongan
            union
            select 10 as id, "Penata Muda Tk.I" as uraian_pangkat, "III/b" as uraian_golongan
            union
            select 11 as id, "Penata" as uraian_pangkat, "III/c" as uraian_golongan
            union
            select 12 as id, "Penata Tk.I" as uraian_pangkat, "III/d" as uraian_golongan
            union
            select 13 as id, "Pembina" as uraian_pangkat, "IV/a" as uraian_golongan
            union
            select 14 as id, "Pembina Tk. I" as uraian_pangkat, "IV/b" as uraian_golongan
            union
            select 15 as id, "Pembina Utama Muda" as uraian_pangkat, "IV/c" as uraian_golongan
            union
            select 16 as id, "Pembina Utama Madya" as uraian_pangkat, "IV/d" as uraian_golongan
            union
            select 17 as id, "Pembina Utama" as uraian_pangkat, "IV/e" as uraian_golongan
            ');
       return json_encode($getJenis);
    }

    
    public function addPangkat(Request $request)
    {
        $rules = [
            'id_pegawai' => 'required',
            'pangkat_pegawai' => 'required',
            'tmt_pangkat'=> 'required',
        ];
        $messages =[
            'id_pegawai.required' => 'Nama Pegawai Kosong',
            'pangkat_pegawai.required' => 'Pangkat Pegawai Kosong',
            'tmt_pangkat.required'=> 'TMT Pangkat Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new RefPegawaiPangkat();
            $data->id_pegawai= $request->id_pegawai;
            $data->pangkat_pegawai= $request->pangkat_pegawai;
            $data->tmt_pangkat= $request->tmt_pangkat;
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
    
    public function editPangkat(Request $request)
    {
        $rules = [
            'id_pegawai' => 'required',
            'pangkat_pegawai' => 'required',
            'tmt_pangkat'=> 'required',
            'id_pangkat'=> 'required',
        ];
        $messages =[
            'id_pegawai.required' => 'Nama Pegawai Kosong',
            'pangkat_pegawai.required' => 'Pangkat Pegawai Kosong',
            'tmt_pangkat.required'=> 'TMT Pangkat Kosong',
            'id_pangkat.required'=> 'ID Pegawai Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = RefPegawaiPangkat::find($request->id_pangkat);            
            $data->id_pegawai= $request->id_pegawai;
            $data->pangkat_pegawai= $request->pangkat_pegawai;
            $data->tmt_pangkat= $request->tmt_pangkat;
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

    public function delPangkat(Request $request){
        $rules = [
            'id_pangkat'=> 'required',
        ];
        $messages =[
            'id_pangkat.required'=> 'ID Pegawai Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = RefPegawaiPangkat::where('id_pangkat',$request->id_pangkat)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

    public function addUnitJabatan(Request $request)
    {
        $rules = [
            'id_pegawai' => 'required',
            'id_unit' => 'required',
            'tingkat_eselon' => 'required',
            'id_jabatan_eselon' => 'required',
            'nama_jabatan' => 'required',
            'tmt_unit'=> 'required',
        ];
        $messages =[
            'id_pegawai.required' => 'Nama Pegawai Kosong',
            'id_unit.required' => 'Unit Kosong',
            'tingkat_eselon.required'=> 'Eselon Kosong',
            'id_jabatan_eselon.required'=> 'Jabatan Kosong',
            'nama_jabatan.required'=> 'Nama Jabatan Kosong',
            'tmt_unit.required'=> 'TMT Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new RefPegawaiUnit();           
            $data->id_pegawai= $request->id_pegawai;
            $data->id_unit= $request->id_unit;
            $data->tingkat_eselon= $request->tingkat_eselon;
            $data->id_sotk= $request->id_jabatan_eselon;            
            $data->nama_jabatan= $request->nama_jabatan;
            $data->tmt_unit= $request->tmt_unit;
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
    
    public function editUnitJabatan(Request $request)
    {
        $rules = [
            'id_unit_pegawai' => 'required',
            'id_pegawai' => 'required',
            'id_unit' => 'required',
            'tingkat_eselon' => 'required',
            'id_jabatan_eselon' => 'required',
            'nama_jabatan' => 'required',
            'tmt_unit'=> 'required',
        ];
        $messages =[
            'id_unit_pegawai.required' => 'ID Unit Pegawai Kosong',
            'id_pegawai.required' => 'Nama Pegawai Kosong',
            'id_unit.required' => 'Unit Kosong',
            'tingkat_eselon.required'=> 'Eselon Kosong',
            'id_jabatan_eselon.required'=> 'Jabatan Kosong',
            'nama_jabatan.required'=> 'Nama Jabatan Kosong',
            'tmt_unit.required'=> 'TMT Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = RefPegawaiUnit::find($request->id_unit_pegawai);            
            $data->id_pegawai= $request->id_pegawai;
            $data->id_unit= $request->id_unit;
            $data->tingkat_eselon= $request->tingkat_eselon;
            $data->id_sotk= $request->id_jabatan_eselon;         
            $data->nama_jabatan= $request->nama_jabatan;
            $data->tmt_unit= $request->tmt_unit;
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

    public function delUnitJabatan(Request $request){
        $rules = [
            'id_unit_pegawai'=> 'required',
        ];
        $messages =[
            'id_unit_pegawai.required' => 'ID Unit Pegawai Kosong',           
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = RefPegawaiUnit::where('id_unit_pegawai',$request->id_unit_pegawai)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

    public function getSotkLevel($id_unit, $level){
        if ($level==0){
            $getSotkLevel1=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es2 as id_sotk, a.id_unit, a.nama_eselon, a.tingkat_eselon, 
            a.created_at, a.updated_at,
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
                FROM ref_sotk_level_1 AS a, (SELECT @id:=0) x
                WHERE a.id_unit='.$id_unit);
            return json_encode($getSotkLevel1);
        }
        if ($level==1){
            $getSotkLevel2=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es3 as id_sotk, a.id_sotk_es2, a.nama_eselon, 
                a.tingkat_eselon, a.created_at, a.updated_at,
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
                INNER JOIN ref_sotk_level_1 AS b ON a.id_sotk_es2 = b.id_sotk_es2, (SELECT @id:=0) x
                WHERE b.id_unit='.$id_unit);
            return json_encode($getSotkLevel2);
        }
        if ($level==2){
            $getSotkLevel3=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es4 as id_sotk, a.id_sotk_es3, a.id_sotk_es4 ,a.nama_eselon, 
                a.tingkat_eselon, a.created_at, a.updated_at,
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
                FROM ref_sotk_level_3 AS a
                INNER JOIN ref_sotk_level_2 AS b ON a.id_sotk_es3 = b.id_sotk_es3
                INNER JOIN ref_sotk_level_1 AS c ON b.id_sotk_es2 = c.id_sotk_es2, (SELECT @id:=0) x
                WHERE c.id_unit='.$id_unit);
            return json_encode($getSotkLevel3);
        }
    }
}