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

use App\Models\Kin\RefSotkLevel1;
use App\Models\Kin\RefSotkLevel2;
use App\Models\Kin\RefSotkLevel3;

class KinRefSotkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index_sakip()
	{
		// if(Auth::check()){ 
		    return view("kin.dash_sakip");			
		// } else {
			// return view ( 'errors.401' );
		// }	
	}

    public function index()
    {
        if(Auth::check()){ 
            return view('kin.parameter.FrmSotkIndex');
        } else {
            return view ( 'errors.401' );
        }
    }

    public function indexChart($id_unit)
    {
        if(Auth::check()){ 
            $unit = DB::SELECT('SELECT CONCAT(b.kd_urusan,".",RIGHT(CONCAT("0",b.kd_bidang),2),".",RIGHT(CONCAT("0",a.kd_unit),2)) AS kd_unit_display, 
                a.id_unit, a.id_bidang, a.kd_unit, a.nm_unit
                FROM ref_unit AS a INNER JOIN ref_bidang AS b ON a.id_bidang = b.id_bidang 
                WHERE a.id_unit='.$id_unit);

            $data = RefSotkLevel1::with('level2s.level3s')
                    ->where('id_unit','=',$id_unit)
                    ->get();

            return view('kin.parameter.FrmSotkChart',['data' => $data, 'unit'=>$unit]);
        } else {
            return view ( 'errors.401' );
        }
    }

    public function getUnitSotk()
    {
        $getUnitSotk=DB::SELECT('SELECT (@id:=@id+1) AS urut,CONCAT(b.kd_urusan,".",RIGHT(CONCAT("0",b.kd_bidang),2),".",RIGHT(CONCAT("0",a.kd_unit),2)) AS kd_unit_display, 
                a.id_unit, a.id_bidang, a.kd_unit, a.nm_unit
                FROM ref_unit AS a INNER JOIN ref_bidang AS b ON a.id_bidang = b.id_bidang,(SELECT @id:=0) x');

      return DataTables::of($getUnitSotk)
        ->addColumn('action', function ($getUnitSotk) {            
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnLihatChart dropdown-item" href="'.url('kinparam/sotk/getSotkChart/'.$getUnitSotk->id_unit).'"><i class="fa fa-sitemap fa-fw fa-lg text-primary"></i> Lihat Bagan Organisasi </a>
                        </li>
                    </ul>
                    </div>
                ';
          })
        ->make(true); 
    }

    public function getSotkLevel1($id_unit)
    {
        $getSotkLevel1=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es2, a.id_unit, a.nama_eselon, a.tingkat_eselon, a.created_at, a.updated_at,
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
            FROM ref_sotk_level_1 AS a,(SELECT @id:=0) x
            WHERE a.id_unit='.$id_unit);

        return DataTables::of($getSotkLevel1)
        ->addColumn('action', function ($getSotkLevel1) {            
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditLevel1 dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Detail Data</a>
                        </li>
                        <li>
                            <a class="btnHapusLevel1 dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Data</a>
                        </li>
                    </ul>
                    </div>
                ';
          })
        ->make(true); 
    }

    public function addLevel1(Request $request)
    {
        $rules = [
            'id_unit' => 'required',
            'nama_eselon' => 'required',
            'tingkat_eselon'=> 'required',
        ];
        $messages =[
            'id_unit.required' => 'Unit Kosong',
            'nama_eselon.required' => 'Nama Jabatan Eselon Kosong',
            'tingkat_eselon.required'=> 'Tingkat Eselon Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new RefSotkLevel1();
            $data->id_unit= $request->id_unit;
            $data->nama_eselon= $request->nama_eselon;
            $data->tingkat_eselon= $request->tingkat_eselon;
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
    
    public function editLevel1(Request $request)
    {
        $rules = [
            'id_unit' => 'required',
            'nama_eselon' => 'required',
            'tingkat_eselon'=> 'required',
            'id_sotk_es2'=> 'required',
        ];
        $messages =[
            'id_unit.required' => 'Unit Kosong',
            'nama_eselon.required' => 'Nama Jabatan Eselon Kosong',
            'tingkat_eselon.required'=> 'Tingkat Eselon Kosong',
            'id_sotk_es2.required'=> 'ID SOTK Level 1 Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = RefSotkLevel1::find($request->id_sotk_es2);
            $data->id_unit= $request->id_unit;
            $data->nama_eselon= $request->nama_eselon;
            $data->tingkat_eselon= $request->tingkat_eselon;
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

    public function delLevel1(Request $request){
        $rules = [
            'id_sotk_es2'=> 'required',
        ];
        $messages =[
            'id_sotk_es2.required'=> 'ID SOTK Level 1 Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = RefSotkLevel1::where('id_sotk_es2',$request->id_sotk_es2)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

    public function getSotkLevel2($id_sotk_es2)
    {
        $getSotkLevel1=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es2, b.nama_eselon AS nama_eselon2, a.id_sotk_es3, a.nama_eselon AS nama_eselon3, a.tingkat_eselon, a.created_at, a.updated_at,
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
            INNER JOIN ref_sotk_level_1 AS b ON. a.id_sotk_es2 = b.id_sotk_es2,(SELECT @id:=0) x
            WHERE a.id_sotk_es2='.$id_sotk_es2);

        return DataTables::of($getSotkLevel1)
        ->addColumn('action', function ($getSotkLevel1) {            
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditLevel2 dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Detail Data</a>
                        </li>
                        <li>
                            <a class="btnHapusLevel2 dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Data</a>
                        </li>
                    </ul>
                    </div>
                ';
          })
        ->make(true); 
    }

    public function addLevel2(Request $request)
    {
        $rules = [
            'id_sotk_es2' => 'required',
            'nama_eselon' => 'required',
            'tingkat_eselon'=> 'required',
        ];
        $messages =[
            'id_sotk_es2.required' => 'ID SOTK Level 1 Kosong',
            'nama_eselon.required' => 'Nama Jabatan Eselon Kosong',
            'tingkat_eselon.required'=> 'Tingkat Eselon Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new RefSotkLevel2();
            $data->id_sotk_es2= $request->id_sotk_es2;
            $data->nama_eselon= $request->nama_eselon;
            $data->tingkat_eselon= $request->tingkat_eselon;
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
    
    public function editLevel2(Request $request)
    {
        $rules = [
            'nama_eselon' => 'required',
            'tingkat_eselon'=> 'required',
            'id_sotk_es2'=> 'required',
            'id_sotk_es3'=> 'required',
        ];
        $messages =[
            'nama_eselon.required' => 'Nama Jabatan Eselon Kosong',
            'tingkat_eselon.required'=> 'Tingkat Eselon Kosong',
            'id_sotk_es2.required'=> 'ID SOTK Level 1 Kosong',
            'id_sotk_es3.required'=> 'ID SOTK Level 2 Kosong',             
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = RefSotkLevel2::find($request->id_sotk_es3);
            $data->id_sotk_es2= $request->id_sotk_es2;
            $data->nama_eselon= $request->nama_eselon;
            $data->tingkat_eselon= $request->tingkat_eselon;
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

    public function delLevel2(Request $request){
        $rules = [
            'id_sotk_es3'=> 'required',
        ];
        $messages =[
            'id_sotk_es3.required'=> 'ID SOTK Level 2 Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = RefSotkLevel2::where('id_sotk_es3',$request->id_sotk_es3)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

    
    public function getSotkLevel3($id_sotk_es3)
    {
        $getSotkLevel1=DB::SELECT('SELECT (@id:=@id+1) AS urut,b.id_sotk_es2, c.nama_eselon AS nama_eselon2, a.id_sotk_es3, b.nama_eselon AS nama_eselon3, a.id_sotk_es4 ,a.nama_eselon AS nama_eselon4, a.tingkat_eselon, a.created_at, a.updated_at,
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
            INNER JOIN ref_sotk_level_2 AS b ON. a.id_sotk_es3 = b.id_sotk_es3
            INNER JOIN ref_sotk_level_1 AS c ON. b.id_sotk_es2 = c.id_sotk_es2,(SELECT @id:=0) x
            WHERE a.id_sotk_es3='.$id_sotk_es3);

        return DataTables::of($getSotkLevel1)
        ->addColumn('action', function ($getSotkLevel1) {            
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditLevel3 dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Detail Data</a>
                        </li>
                        <li>
                            <a class="btnHapusLevel3 dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Data</a>
                        </li>
                    </ul>
                    </div>
                ';
          })
        ->make(true); 
    }

    public function addLevel3(Request $request)
    {
        $rules = [
            'id_sotk_es3' => 'required',
            'nama_eselon' => 'required',
            'tingkat_eselon'=> 'required',
        ];
        $messages =[
            'id_sotk_es3.required' => 'ID SOTK Level 2 Kosong',
            'nama_eselon.required' => 'Nama Jabatan Eselon Kosong',
            'tingkat_eselon.required'=> 'Tingkat Eselon Kosong',
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
			$data = new RefSotkLevel3();
            $data->id_sotk_es3= $request->id_sotk_es3;
            $data->nama_eselon= $request->nama_eselon;
            $data->tingkat_eselon= $request->tingkat_eselon;
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
    
    public function editLevel3(Request $request)
    {
        $rules = [
            'nama_eselon' => 'required',
            'tingkat_eselon'=> 'required',
            'id_sotk_es4'=> 'required',
            'id_sotk_es3'=> 'required',
        ];
        $messages =[
            'nama_eselon.required' => 'Nama Jabatan Eselon Kosong',
            'tingkat_eselon.required'=> 'Tingkat Eselon Kosong',
            'id_sotk_es4.required'=> 'ID SOTK Level 3 Kosong',
            'id_sotk_es3.required'=> 'ID SOTK Level 2 Kosong',             
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {
            $data = RefSotkLevel3::find($request->id_sotk_es4);
            $data->id_sotk_es3= $request->id_sotk_es3;
            $data->nama_eselon= $request->nama_eselon;
            $data->tingkat_eselon= $request->tingkat_eselon;
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

    public function delLevel3(Request $request){
        $rules = [
            'id_sotk_es4'=> 'required',
        ];
        $messages =[
            'id_sotk_es4.required'=> 'ID SOTK Level 3 Kosong',            
		];
        $validation = Validator::make($request->all(),$rules,$messages);
        
		if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);			
			}
		else {        
            $data = RefSotkLevel3::where('id_sotk_es4',$request->id_sotk_es4)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }
    
}