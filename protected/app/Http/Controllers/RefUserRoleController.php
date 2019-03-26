<?php

namespace App\Http\Controllers;

// use Request;
use Illuminate\Http\Request;
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
use App\Models\RefGroup;
use App\Models\TrxGroupMenu;

class RefUserRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

// Bagian ini untuk pengelolaan group user
// ===================================================================================

	public function Peran(Request $request, Builder $htmlBuilder){        
        if (request()->ajax()) {
            $group = DB::select('SELECT (@id:=@id+1) as no_urut, a.* FROM ref_user_role a, (SELECT @id:=0) b');
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
        // if(Auth::check()){ 
            return view('user.group');
        // } else {
            // return view ( 'errors.401' );
        // } 
        
    }

    protected $request;

    // public function __construct(\Illuminate\Http\Request $request)
    // {
    //     $this->request = $request;
    // }

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

}