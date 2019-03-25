<?php

namespace App\Http\Controllers;

// use Request;
use Illuminate\Http\Request;
use Session;
use Validator;
use DB;
use Datatables;
use Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;

use App\Models\User;
use App\Models\UserDesa;
use App\Models\UserSubUnit;
use App\Models\RefGroup;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

// Bagian ini untuk pengelolaan user
// ===================================================================================

	public function index(Request $request, Builder $htmlBuilder){        
        if (request()->ajax()) {
            $users = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id,a.group_id,a.`name`,a.email,a.id_unit,a.`password`,
                a.remember_token,a.created_at,a.updated_at,a.status_user,b.kd_unit,b.nm_unit,
                CASE a.status_user
                          WHEN 1 THEN "Aktif"
                          WHEN 0 THEN "Non Aktif"
                END AS status_display FROM users AS a
                LEFT OUTER JOIN ref_unit AS b ON a.id_unit = b.id_unit , (SELECT @id:=0) b');

            return Datatables::of($users)
                ->addColumn('action', function ($users) {
                    if(Session::get('AppType')==0)
                        return  '
                        <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="glyphicon glyphicon-wrench"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a id="btnViewUnit" class="dropdown-item"><i class="fa fa-university fa-fw"></i> Akses Unit</a>
                            </li>
                            <li>
                                <a id="btnViewWilayahKab" class="dropdown-item"><i class="fa fa-map-o fa-fw"></i> Akses Wilayah</a>
                            </li>
                            <li>
                                <a id="btnGantiPass" class="dropdown-item"><i class="fa fa-key fa-fw"></i> Ganti Password</a>
                            </li>
                            <li>
                                <a id="btnEditUser" class="dropdown-item"><i class="fa fa-pencil fa-fw"></i> Edit User</a>
                            </li>
                            <li>
                                <a id="btnHapusUser" class="dropdown-item"><i class="fa fa-trash fa-fw"></i> Hapus User</a>
                            </li>
                        </ul>
                        </div>  ';

                    if(Session::get('AppType')!=0)
                        return  '
                        <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="glyphicon glyphicon-wrench"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a id="btnViewUnit" class="dropdown-item"><i class="fa fa-university fa-fw"></i> Akses Unit</a>
                            </li>
                            <li>
                                <a id="btnViewWilayah" class="dropdown-item"><i class="fa fa-map-o fa-fw"></i> Akses Wilayah</a>
                            </li>
                            <li>
                                <a id="btnGantiPass" class="dropdown-item"><i class="fa fa-key fa-fw"></i> Ganti Password</a>
                            </li>
                            <li>
                                <a id="btnEditUser" class="dropdown-item"><i class="fa fa-pencil fa-fw"></i> Edit User</a>
                            </li>
                            <li>
                                <a id="btnHapusUser" class="dropdown-item"><i class="fa fa-trash fa-fw"></i> Hapus User</a>
                            </li>
                        </ul>
                        </div>  ';
                })
            ->make(true);

            

        }
        if(Auth::check()){ 
            return view('user.index');
        } else {
            return view ( 'errors.401' );
        }
        
    }

    public function getGroup(){ 
        $group = DB::select('SELECT a.* FROM ref_group a Order by a.id');
        return json_encode($group);
    }

    public function getUnitIndex(){ 
        $unit = DB::select('SELECT a.id_unit, a.id_bidang, a.kd_unit, a.nm_unit
                FROM ref_unit AS a ORDER BY a.id_bidang, a.kd_unit');
        return json_encode($unit);
    }

    public function addUser (Request $request)
    {
        $validator = $this->validate($request, [
            'nama' => 'required|string|max:50',
            'group_id' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ]);  
        
        // if($validator == true){
            try{
                    $user = User::create([
                        'name' => $request->nama,
                        'group_id' => $request->group_id,
                        'email' => $request->email,
                        'status_user' => $request->status_user,
                        'id_unit' => $request->id_unit,
                        'password' => bcrypt($request->password)
                    ]);
                    return response ()->json (['pesan'=>'Data User '.$request->nama.' Berhasil Disimpan','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data User '.$request->nama.' Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                }
        // } else {
            // return response ()->json (['pesan'=>'Ada Field Yang Belum Diisi','status_pesan'=>'0']);
        // }
                
    }

    public function editUser (Request $request)
    {
        $data = User::find($request->id);        
        $data->name= $request->nama;
        $data->group_id= $request->group_id;
        $data->email= $request->email;
        $data->id_unit = $request->id_unit;
        $data->status_user= $request->status_user;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data User '.$request->nama.' Berhasil Disimpan','status_pesan'=>'1']);
         }
         catch(QueryException $e){
              $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data User '.$request->nama.' Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
         }
    }

    public function gantiPass (Request $request)
    {
        $validator = $this->validate($request, [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ]); 

        $data = User::find($request->id);
        $data->password= bcrypt($request->password);
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Password Berhasil Diubah','status_pesan'=>'1']);
         }
         catch(QueryException $e){
              $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Password Gagal Diubah ('.$error_code.')','status_pesan'=>'0']);
         }
    }

    public function cekUserAdmin()
    {
        $getPass = DB::SELECT('SELECT password FROM users WHERE email = "super@simcan.dev" LIMIT 1');
        $defaultPass = '$2y$10$076o9z6B8i/dzkVBtg/SfOWVcmxNoUHLIHUvxQrsC4CguHM80qwJS';

        if ($defaultPass == $getPass[0]->password){
            return response ()->json (['pesan'=>'Password Super Admin masih DEFAULT, Mohon segera diganti','status_pesan'=>'1','test'=>bcrypt('s03p3r4dm1n')]);
         } else {
            return response ()->json (['pesan'=>'Terima Kasih, Password Super Admin telah dilakukan pergantian','status_pesan'=>'0','test'=>bcrypt('s03p3r4dm1n')]);
         }
    }

    public function hapusUser (Request $req)
    {
        User::where('id',$req->id)->delete();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    }

    public function getUnit(Request $request){
        $unit = DB::SELECT('Select (@id:=@id+1) as no_urut, a.* from ref_unit a, (SELECT @id:=0) x');            
        return DataTables::of($unit)
        ->addColumn('action',function($unit){
        return '
            <button id="btnPilihUnit" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="Pilih Unit" title="Pilih Unit" class="btn btn-primary btn-sm"><i class="fa fa-check-square-o fa-fw"></i> Pilih Unit</button>
        ' ;
    })
   ->make(true);
    }

    public function addUnit (Request $req)
    {
        $data = new UserSubUnit();
        $data->user_id= $req->user_id;
        $data->kd_unit= $req->kd_unit;
        $data->kd_sub= null;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusUnit (Request $req)
    {
        UserSubUnit::where('id_user_unit',$req->id_user_unit)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    } 

    public function getListUnit($id_user)
    {
        $getListUnit=DB::select('SELECT (@id:=@id+1) as no_urut, a.user_id,a.kd_unit,b.nm_unit,a.kd_sub,a.id_user_unit 
                FROM user_sub_unit AS a
                LEFT OUTER JOIN ref_unit AS b ON a.kd_unit = b.id_unit, (SELECT @id:=0) x WHERE a.user_id='.$id_user);

        return DataTables::of($getListUnit)
        ->addColumn('action',function($getListUnit){
                    return '
                        <button type="button" id="btnHapusUnit" class="btn btn-labeled btn-danger btn-sm">
                            <span class="btn-label"><i class="fa fa-trash fa-lg fa-fw"></i></span>Hapus</button> 
                    ' ;
            })
        ->make(true);
    }

    public function getKecamatan()
    {
       $getSB=DB::SELECT('SELECT id_pemda, kd_kecamatan, id_kecamatan, nama_kecamatan
                FROM ref_kecamatan');
       return json_encode($getSB);
    }

    public function getDesa($id_kecamatan)
    {
        $getDesa=DB::select('SELECT (@id:=@id+1) as no_urut, a.id_kecamatan, a.kd_desa, a.id_desa, a.status_desa, a.nama_desa, a.id_zona
            FROM ref_desa a, (SELECT @id:=0) x WHERE a.id_kecamatan='.$id_kecamatan);

        return DataTables::of($getDesa)
            ->addColumn('action',function($getDesa){
                    return '
                        <button id="btnPilihDesa" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="Pilih Desa" title="Pilih Desa" class="btn btn-primary btn-sm"><i class="fa fa-check-square-o fa-fw"></i> Pilih Desa</button>
                    ' ;
            })
        ->make(true);
    }

    public function getKab()
    {
        $getKab=DB::select('SELECT (@id:=@id+1) as no_urut, a.id_kab, a.kd_kab, a.id_prov, a.nama_kab_kota
            FROM ref_kabupaten a, (SELECT @id:=0) x');

        return DataTables::of($getKab)
            ->addColumn('action',function($getKab){
                    return '
                        <button id="btnPilihKab" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="Pilih Kabupaten/Kota" title="Pilih Kabupaten/Kota" class="btn btn-primary btn-sm"><i class="fa fa-check-square-o fa-fw"></i> Pilih</button>
                    ' ;
            })
        ->make(true);
    }

    public function getListDesa($id_user)
    {
        $getListDesa=DB::select('SELECT (@id:=@id+1) as no_urut, a.user_id, a.kd_desa as id_desa, a.rw, b.kd_desa, b.nama_desa, c.nama_kecamatan,
                b.id_kecamatan, c.kd_kecamatan,a.id_user_wil, d.nama_kab_kota 
                FROM user_desa AS a
                INNER JOIN ref_desa AS b ON a.kd_kecamatan = b.id_kecamatan AND a.kd_desa = b.id_desa
                INNER JOIN ref_kecamatan AS c ON b.id_kecamatan = c.id_kecamatan
                INNER JOIN ref_kabupaten as d ON c.id_pemda = d.id_kab, (SELECT @id:=0) x WHERE a.user_id='.$id_user);

        return DataTables::of($getListDesa)
        ->addColumn('action',function($getListDesa){
                    return '
                        <button type="button" id="btnHapusDesa" class="btn btn-labeled btn-danger btn-sm">
                            <span class="btn-label"><i class="fa fa-trash fa-lg fa-fw"></i></span>Hapus</button> 
                    ' ;
            })
        ->make(true);
    }

    public function getListKab($id_user)
    {
        $getListKab=DB::select('SELECT (@id:=@id+1) as no_urut, a.user_id, a.kd_kecamatan as id_desa, b.id_kab, b.id_prov, 
                b.kd_kab, b.nama_kab_kota,  a.id_user_wil, c.nm_prov 
                FROM user_desa AS a
                INNER JOIN ref_kabupaten AS b ON a.kd_desa = b.id_kab
                INNER JOIN ref_pemda as c ON b.id_pemda = c.id_pemda, (SELECT @id:=0) x WHERE a.user_id='.$id_user);

        return DataTables::of($getListKab)
        ->addColumn('action',function($getListKab){
                    return '
                        <button type="button" id="btnHapusKab" class="btn btn-labeled btn-danger btn-sm">
                            <span class="btn-label"><i class="fa fa-trash fa-lg fa-fw"></i></span>Hapus</button> 
                    ' ;
            })
        ->make(true);
    }

    public function addWilayah (Request $req)
    {
        $data = new UserDesa();
        $data->user_id= $req->user_id;
        $data->kd_kecamatan= $req->kd_kecamatan;
        $data->kd_desa= $req->kd_desa;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusWilayah (Request $req)
    {
        UserDesa::where('id_user_wil',$req->id_user_wil)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    } 

}