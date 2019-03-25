<?php

namespace App\Http\Controllers;

// use Request;
use Illuminate\Http\Request;
use Validator;
use DB;
use Datatables;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;

use App\Models\User;
use App\Models\RefGroup;
use App\Models\RefUserRole;
use App\Models\TrxGroupMenu;

class RefGroupMenuController extends Controller
{
    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->middleware('auth');
        $this->request = $request;
    }

// Bagian ini untuk pengelolaan group user
// ===================================================================================

	public function Group(Request $request, Builder $htmlBuilder){        
        if (request()->ajax()) {
            $group = DB::select('SELECT (@id:=@id+1) as no_urut, a.* FROM ref_group a, (SELECT @id:=0) b WHERE a.id<>1');
            return Datatables::of($group)
                ->addColumn('action', function ($group) {
                    return 
                    '                        
                        <a href="'.url('/admin/parameter/user/group/'.$group->id.'/akses').'"><button id="btnAksesGroup" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Pemberian Hak Akses" title="Pemberian Hak Akses" class="btn btn-primary"><i class="fa fa-ban fa-fw"></i></button></a> 
                        <button id="btnEditGroup" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Edit Group" title="Edit Group" class="btn btn-warning"><i class="fa fa-pencil fa-fw"></i></button>
                        <button id="btnHapusGroup" type="button" data-toggle="popover" data-trigger="hover" data-container="body" data-html="true" data-content="Hapus Group" title="Hapus Group" class="btn btn-danger"><i class="fa fa-trash fa-fw"></i></button>                  
                    '                
                    ;
                })
            ->make(true);
        }
        return view('user.group');
        
    }

    protected $request;

    public function Akses($id, Request $request){
        // Load Selected Group
    	$model = RefGroup::find($id);

        // If Post from form
        IF($this->request->all()){
            // catch all input
            $input = $this->request->all();
            // create an array from selected input
            $submitteds = explode(',', $input['selected']);
            foreach($submitteds as $submitted){
                // check if data exist first, if not create new menu
                if(TrxGroupMenu::where(['group_id' => $id, 'menu' => $submitted])->first() == NULL){
                    $model = new TrxGroupMenu();
                    $model->group_id = $id;
                    $model->menu = $submitted;
                    $model->save();
                }
            }
            // delete all non-selected row
            TrxGroupMenu::where(['group_id' => $id])->whereNotIn('menu', $submitteds)->delete();
        }    

        // load data menu from selected group
        $data = TrxGroupMenu::where(['group_id' => $id]);
        $data = $data->get();

        // fill to menu to $selected
        $selected[] = NULL;
        foreach($data as $v){
            $selected[] = $v->menu;
        }
        $selected = '['.(implode(',', $selected)).']';    
        return view('user.akses', ['model' => $model, 'data' => $data, 'selected' => $selected]);
    }

    public function deletegroup($id, Request $request){
        $model = RefGroup::destroy($id);
        return redirect('admin/parameter/user/group');
    }

    public function addGroup (Request $request)
    {
        $data = new RefGroup();
        $data->name= $request->nama_group;
        $data->keterangan= $request->keterangan;
        $data->id_roles= $request->id_roles;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editGroup (Request $request)
    {
        $data = RefGroup::find($request->id);        
        $data->name= $request->name_group;
        $data->keterangan= $request->keterangan;
        $data->id_roles= $request->id_roles;
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusGroup (Request $request)
    {
        RefGroup::where('id',$request->id)->delete();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
    } 

    
    public function getPeranGroup(){ 
        $peran = DB::select('SELECT a.* FROM ref_user_role a Order by a.id');
        return json_encode($peran);
    }

    public function getPeran(Request $request, Builder $htmlBuilder){        
        if (request()->ajax()) {
            $group = DB::select('SELECT (@id:=@id+1) as no_urut, a.* ,
                CASE a.tambah
                    WHEN 0 THEN "fa fa-times"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_tambah,
                CASE a.edit
                    WHEN 0 THEN "fa fa-times"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_edit,
                CASE a.hapus
                    WHEN 0 THEN "fa fa-times"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_hapus,
                CASE a.lihat
                    WHEN 0 THEN "fa fa-times"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_lihat,
                CASE a.reviu
                    WHEN 0 THEN "fa fa-times"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_reviu,
                CASE a.posting
                    WHEN 0 THEN "fa fa-times"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_posting,
                CASE a.tambah
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna_tambah,
                CASE a.edit
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna_edit,
                CASE a.hapus
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna_hapus,
                CASE a.lihat
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna_lihat,
                CASE a.reviu
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna_reviu,
                CASE a.posting
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna_posting
                FROM ref_user_role a, (SELECT @id:=0) b');
            return Datatables::of($group)
                ->addColumn('action', function ($group) {
                    return 
                    '  <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="btneditPeran dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit</a>
                            </li>
                            <li>
                                <a class="btnhapusPeran dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg"></i> Hapus</a>
                            </li>
                        </ul>
                        </div>                 
                    '                
                    ;
                })
            ->make(true);
        }
        // if(Auth::check()){ 
            return view('user.peran');
        // } else {
            // return view ( 'errors.401' );
        // } 
        
    }

    public function addPeran (Request $request)
    {
        $data = new RefUserRole();
        $data->uraian_peran= $request->uraian_peran;
        $data->tambah= $request->tambah;
        $data->edit= $request->edit;
        $data->hapus= $request->hapus;
        $data->lihat= $request->lihat;
        $data->reviu= $request->reviu;
        $data->posting= $request->posting;
        $data->status_role= $request->status_role;        
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function editPeran (Request $request)
    {
        $data = RefUserRole::find($request->id);  
        $data->uraian_peran= $request->uraian_peran;
        $data->tambah= $request->tambah;
        $data->edit= $request->edit;
        $data->hapus= $request->hapus;
        $data->lihat= $request->lihat;
        $data->reviu= $request->reviu;
        $data->posting= $request->posting;
        $data->status_role= $request->status_role;  
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function hapusPeran (Request $request)
    {
        $cek=DB::SELECT('SELECT id_roles FROM ref_group where id_roles='.$request->id);

        if($cek==null){
            RefUserRole::where('id',$request->id)->delete();
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal dihapus karena telah dipakai','status_pesan'=>'0']);
        }
        
    } 

}