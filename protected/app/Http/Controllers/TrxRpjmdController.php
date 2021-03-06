<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
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
use App\Models\RefPemda;
use App\Models\RefUnit;
use App\Models\RefIndikator;
use App\Models\RefUrusan;
use App\Models\RefBidang;
use App\Models\TrxRpjmdDokumen;
use App\Models\TrxRpjmdVisi;
use App\Models\TrxRpjmdMisi;
use App\Models\TrxRpjmdTujuan;
use App\Models\TrxRpjmdSasaran;
use App\Models\TrxRpjmdKebijakan;
use App\Models\TrxRpjmdStrategi;
use App\Models\TrxRpjmdProgram;
use App\Models\TrxRpjmdProgramIndikator;
use App\Models\TrxRpjmdProgramUrusan;
use App\Models\TrxRpjmdProgramPelaksana;

class TrxRpjmdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

      $dataperdarpjmd=DB::select('SELECT * FROM trx_rpjmd_dokumen WHERE id_status_dokumen=1');
      return view('rpjmd.index')->with(compact('dataperdarpjmd'));

    }
    public function indexChart($id_rpjmd)
    {
            $data = TrxRpjmdVisi::with('TrxRpjmdMisis.TrxRpjmdTujuans.TrxRpjmdSasarans.TrxRpjmdPrograms')
                    ->where('id_rpjmd','=',$id_rpjmd)
                    ->get();                    
            return view('rpjmd.FrmRpjmdChart',['data' => $data]);
    }

    public function getJnsDokumen()
    {
      $dataperdarpjmd=DB::SELECT('SELECT * FROM ref_dokumen WHERE jenis_proses = 1 ORDER BY urut_tampil');
      return json_encode($dataperdarpjmd);
    }

    public function getDokumen()
    {
      $dataperdarpjmd=DB::SELECT('SELECT * FROM trx_rpjmd_dokumen WHERE id_status_dokumen=1');
      return json_encode($dataperdarpjmd);
    }

    public function getDokumenRef(Request $request)
    {                
      if($request->jns==0){
            $where = '';
        } else {
            $where = ' AND jns_dokumen='.$request->jns;
        }
      $dataperdarpjmd=DB::SELECT('SELECT * FROM trx_rpjmd_dokumen  WHERE id_status_dokumen=1 '.$where);
      return json_encode($dataperdarpjmd);
    }

    public function getDokumenRpjmd()
    {
      $dokrpjmd = DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, p.* FROM  (SELECT a.id_pemda, a.id_rpjmd, COALESCE(a.id_rpjmd_old) AS id_rpjmd_old, a.thn_dasar, a.tahun_1, a.tahun_2, a.tahun_3, a.tahun_4, a.tahun_5,a.id_rpjmd_ref,
                a.no_perda, a.tgl_perda, a.keterangan_dokumen, a.jns_dokumen, c.nm_dokumen, Coalesce(a.id_revisi,0) AS id_revisi, a.id_status_dokumen, a.sumber_data, 
                a.created_at, a.updated_at, TglIndonesia(a.tgl_perda) AS tgl_perda_view,
                CASE a.id_status_dokumen
                                    WHEN 0 THEN "fa fa-question"
                                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_icon,
                CASE a.id_status_dokumen
                                    WHEN 0 THEN "red"
                                    WHEN 1 THEN "green"
                END AS warna
                FROM trx_rpjmd_dokumen AS a
                INNER JOIN ref_pemda AS b ON a.id_pemda = b.id_pemda
                INNER JOIN ref_dokumen AS c ON a.jns_dokumen = c.id_dokumen
                WHERE b.id_pemda = '.Session::get('xIdPemda').' ORDER BY a.id_pemda, a.jns_dokumen, a.id_revisi, a.id_rpjmd) AS p, 
                (SELECT @id:=0) x');

      return DataTables::of($dokrpjmd)
        ->addColumn('details_url', function($dokrpjmd) {
                return url('rpjmd/visi/'.$dokrpjmd->id_rpjmd);
            })
        ->addColumn('action', function ($dokrpjmd) {
            if ($dokrpjmd->id_status_dokumen==0)
            return
            '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a class="btnViewDok dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Lihat Data Dokumen</a>
                    </li>
                    <li>
                        <a class="btnAddVisi dropdown-item"><i class="fa fa-plus fa-fw fa-lg text-success"></i> Tambah Visi RPJMD</a>
                    </li>
                    <li>
                        <a class="btnViewBtl dropdown-item" ><i class="fa fa-check-square-o fa-fw fa-lg text-warning"></i> Posting Dokumen </a>
                    </li>
                </ul>
            </div>';
            if ($dokrpjmd->id_status_dokumen==1)
            return
            '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a class="btnViewDok dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Lihat Data Dokumen</a>
                    </li>
                    <li>
                        <a class="btnViewBtl dropdown-item" ><i class="fa fa-check-square-o fa-fw fa-lg text-warning"></i> un-Posting Dokumen </a>
                    </li>
                </ul>
            </div>';
        })
        ->make(true);
    }

    public function addDokumen(Request $request)
    {
        $rules = [
            'tahun_1'=>'required',
            'tahun_5'=>'required',
            'no_perda'=>'required',
            'tgl_perda'=>'required',
            'keterangan_dokumen'=>'required',
            'jns_dokumen'=>'required',
            'jns_dokumen'=>'required',
            'id_revisi'=>'required',
        ];
        $messages =[
            'tahun_1.required'=>'tahun_1 Kosong',
            'tahun_5.required'=>'tahun_5 Kosong',
            'no_perda.required'=>'no_perda Kosong',
            'tgl_perda.required'=>'tgl_perda Kosong',
            'keterangan_dokumen.required'=>'keterangan_dokumen Kosong',
            'jns_dokumen.required'=>'jns_dokumen Kosong',
            'id_rpjmd_ref.required'=>'referensi dokumen Kosong',
            'id_revisi.required'=>'id_revisi Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {
            $data = new TrxRpjmdDokumen();
            $data->id_pemda=Session::get('xIdPemda');
            $data->thn_dasar=$request->tahun_1 - 1;
            $data->tahun_1=$request->tahun_1;
            $data->tahun_2=$request->tahun_1 + 1;
            $data->tahun_3=$request->tahun_1 + 2;
            $data->tahun_4=$request->tahun_1 + 3;
            $data->tahun_5=$request->tahun_5;
            $data->no_perda=$request->no_perda;
            $data->tgl_perda=$request->tgl_perda;
            $data->keterangan_dokumen=$request->keterangan_dokumen;
            $data->jns_dokumen=$request->jns_dokumen;
            $data->id_rpjmd_ref=$request->id_rpjmd_ref;
            $data->id_revisi=$request->id_revisi;
            $data->id_status_dokumen=0;
            $data->sumber_data=1;
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

    public function editDokumen(Request $request)
    {
        $rules = [
            'id_rpjmd'=>'required',
            'tahun_1'=>'required',
            'tahun_5'=>'required',
            'no_perda'=>'required',
            'tgl_perda'=>'required',
            'keterangan_dokumen'=>'required',
            'jns_dokumen'=>'required',
            'id_rpjmd_ref'=>'required',
            'id_revisi'=>'required',
        ];
        $messages =[
            'id_rpjmd.required'=>'id_rpjmd Kosong',
            'tahun_1.required'=>'tahun_1 Kosong',
            'tahun_5.required'=>'tahun_5 Kosong',
            'no_perda.required'=>'no_perda Kosong',
            'tgl_perda.required'=>'tgl_perda Kosong',
            'keterangan_dokumen.required'=>'keterangan_dokumen Kosong',
            'jns_dokumen.required'=>'jns_dokumen Kosong',
            'id_rpjmd_ref.required'=>'referensi dokumen Kosong',
            'id_revisi.required'=>'id_revisi Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {
            $data = TrxRpjmdDokumen::find($request->id_rpjmd);
            if($data->id_status_dokumen == 0){
                        $data->thn_dasar=$request->tahun_1 - 1;
                        $data->tahun_1=$request->tahun_1;
                        $data->tahun_2=$request->tahun_1 + 1;
                        $data->tahun_3=$request->tahun_1 + 2;
                        $data->tahun_4=$request->tahun_1 + 3;
                        $data->tahun_5=$request->tahun_5;
                        $data->no_perda=$request->no_perda;
                        $data->tgl_perda=$request->tgl_perda;
                        $data->keterangan_dokumen=$request->keterangan_dokumen;
                        $data->jns_dokumen=$request->jns_dokumen;
                        $data->id_rpjmd_ref=$request->id_rpjmd_ref;
                        $data->id_revisi=$request->id_revisi;
                        try{
                            $data->save (['timestamps' => true]);
                            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                        }
                          catch(QueryException $e){
                             $error_code = $e->errorInfo[1] ;
                             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                        }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Disimpan (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        }
    }

    public function deleteDokumen(Request $request){
        $rules = [
            'id_rpjmd'=> 'required',
        ];
        $messages =[
            'id_rpjmd.required'=> 'ID Dokumen Kosong',            
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $data = TrxRpjmdDokumen::find($request->id_rpjmd);
            if($data->id_status_dokumen == 0){
                try{
                    $data->delete();
                     return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                }
                    catch(QueryException $e){
                     $error_code = $e->errorInfo[1] ;
                     return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
                }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        } 
    }

    public function getVisiRPJMD($id_rpjmd)
    {
      $rpjmdvisi = DB::select('SELECT b.*, a.tahun_1, a.tahun_5, a.id_status_dokumen FROM trx_rpjmd_visi AS b
                INNER JOIN trx_rpjmd_dokumen AS a ON b.id_rpjmd = a.id_rpjmd WHERE a.id_rpjmd='.$id_rpjmd.' 
                ORDER BY a.id_rpjmd, b.no_urut ASC');

      return DataTables::of($rpjmdvisi)
        ->addColumn('action', function ($rpjmdvisi) {
            return
            '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
            	<ul class="dropdown-menu dropdown-menu-right">
            		<li>
            			<a class="edit-visi dropdown-item"><i class="fa fa-paper-plane-o fa-fw fa-lg text-success"></i> Lihat Data Visi</a>
                    </li>
                    <li>
                        <a class="btnViewBtl dropdown-item" ><i class="fa fa-building fa-fw fa-lg text-warning"></i> Belanja Non Program</a>
                    </li>
                    <li>
                        <a class="btnViewPendapatan dropdown-item"><i class="fa fa-money fa-fw fa-lg text-success"></i> Pendapatan</a>
                    </li>
                    <li>
                        <a class="btnLihatChart dropdown-item" href="'.url('rpjmd/getRpjmdChart/'.$rpjmdvisi->id_rpjmd).'"><i class="fa fa-sitemap fa-fw fa-lg text-primary"></i> Pohon Kinerja RPJMD</a>
                    </li>
            	</ul>
            </div>';
        })
        ->make(true);
    }

    public function addVisi(Request $request)
    {
        $rules = [
            'id_rpjmd_edit'=>'required',
            'ur_visi_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_rpjmd_edit.required'=>'ID Dokumen RPJMD Kosong',
            'ur_visi_rpjmd_edit.required'=>'Uraian Visi Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Visi Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {            
            $data = new TrxRpjmdVisi();
            $data->thn_id= Session::get('xIdPemda');
            $data->id_rpjmd= $request->id_rpjmd_edit;
            $data->uraian_visi_rpjmd= $request->ur_visi_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
            $data->sumber_data= 1;
            $data->status_data= 0;
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

    public function editVisi(Request $request)
    {
        $rules = [
            'id_rpjmd_edit'=>'required',
            'id_visi_rpjmd_edit'=>'required',
            'ur_visi_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
            'status_data'=>'required',
        ];
        $messages =[
            'id_rpjmd_edit.required'=>'ID Dokumen RPJMD Kosong',
            'id_visi_rpjmd_edit.required'=>'ID Visi RPJMD Kosong',
            'ur_visi_rpjmd_edit.required'=>'Uraian Visi Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Visi Kosong',
            'status_data.required'=>'Status Data Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            } else {   
                $cek = $this->checkVisi($request->id_rpjmd_edit);

                if($cek[0]->id_status_dokumen == 0){
                    $data = TrxRpjmdVisi::find($request->id_visi_rpjmd_edit);
                    $data->id_rpjmd= $request->id_rpjmd_edit;
                    $data->uraian_visi_rpjmd= $request->ur_visi_rpjmd_edit;
                    $data->id_perubahan= $request->id_perubahan_edit;
                    $data->no_urut= $request->no_urut_edit;
                    $data->status_data= $request->status_data;
                    try{
                        $data->save (['timestamps' => true]);
                        return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                    }
                      catch(QueryException $e){
                         $error_code = $e->errorInfo[1] ;
                         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                    }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Disimpan (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        }
    }

    public function deleteVisi(Request $request){
        $rules = [
            'id_rpjmd_edit'=>'required',
            'id_visi_rpjmd_edit'=>'required',
        ];
        $messages =[
            'id_rpjmd_edit.required'=>'ID Dokumen RPJMD Kosong',
            'id_visi_rpjmd_edit.required'=>'ID Visi RPJMD Kosong',           
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $cek = $this->checkVisi($request->id_rpjmd_edit);

                if($cek[0]->id_status_dokumen == 0){
                    $xData=DB::select('SELECT * FROM trx_rpjmd_visi WHERE id_visi_rpjmd ='.$request->id_visi_rpjmd_edit.' AND sumber_data = 0');
                    if ($xData == null){
                        try{
                            $data = TrxRpjmdVisi::find($request->id_visi_rpjmd_edit);
                            $data->delete();
                             return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                        }
                            catch(QueryException $e){
                             $error_code = $e->errorInfo[1] ;
                             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
                    }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        } 
    }

    public function getMisiRPJMD($id_visi_rpjmd)
    {
      $rpjmdmisi = DB::select('SELECT a.uraian_visi_rpjmd, a.no_urut AS no_visi, b.* FROM trx_rpjmd_visi AS a
            INNER JOIN trx_rpjmd_misi AS b On a.id_visi_rpjmd = b.id_visi_rpjmd 
            WHERE b.no_urut not in (98,99) AND a.id_visi_rpjmd = '.$id_visi_rpjmd.' ORDER BY a.no_urut, b.no_urut DESC');

      return DataTables::of($rpjmdmisi)
        ->addColumn('action', function ($rpjmdmisi) {
        	return '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
            	<ul class="dropdown-menu dropdown-menu-right">
            		<li>
            			<a class="edit-misi dropdown-item"><i class="fa fa-location-arrow fa-fw fa-lg text-success"></i> Lihat Data Misi</a>
            		</li>
            	</ul>
            </div>';})
        ->make(true);
    }

    public function addMisi(Request $request)
    {
        $rules = [
            'id_visi_rpjmd_edit'=>'required',
            'ur_misi_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_visi_rpjmd_edit.required'=>'ID Visi RPJMD Kosong',
            'ur_misi_rpjmd_edit.required'=>'Uraian Misi Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Misi Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {            
            $data = new TrxRpjmdMisi();
            $data->thn_id_rpjmd= Session::get('xIdPemda');
            $data->id_visi_rpjmd= $request->id_visi_rpjmd_edit;
            $data->uraian_misi_rpjmd= $request->ur_misi_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
            $data->status_data= 0;            
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

    public function editMisi(Request $request)
    {
        $rules = [
            'id_misi_rpjmd_edit'=>'required',
            'id_visi_rpjmd_edit'=>'required',
            'ur_misi_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
            // 'status_data'=>'required',
        ];
        $messages =[
            'id_misi_rpjmd_edit.required'=>'ID Misi RPJMD Kosong',
            'id_visi_rpjmd_edit.required'=>'ID Visi RPJMD Kosong',
            'ur_misi_rpjmd_edit.required'=>'Uraian Misi Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Misi Kosong',
            // 'status_data.required'=>'Status Data Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {      
        	$data = TrxRpjmdMisi::find($request->id_misi_rpjmd_edit);
        	$data->id_visi_rpjmd= $request->id_visi_rpjmd_edit;
        	$data->uraian_misi_rpjmd= $request->ur_misi_rpjmd_edit;
        	$data->id_perubahan= $request->id_perubahan_edit;
        	$data->no_urut= $request->no_urut_edit;            
            // $data->status_data= $request->status_data;
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

    public function deleteMisi(Request $request){
        $rules = [
            'id_misi_rpjmd_edit'=>'required',
            'id_visi_rpjmd_edit'=>'required',
        ];
        $messages =[
            'id_misi_rpjmd_edit.required'=>'ID Misi RPJMD Kosong',
            'id_visi_rpjmd_edit.required'=>'ID Visi RPJMD Kosong',           
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $cek = $this->checkMisi($request->id_visi_rpjmd_edit);

                if($cek[0]->id_status_dokumen == 0){
                    $xData=DB::select('SELECT * FROM trx_rpjmd_misi WHERE id_misi_rpjmd ='.$request->id_misi_rpjmd_edit.' AND sumber_data = 0');
                    if ($xData == null){
                        try{
                            $data = TrxRpjmdMisi::find($request->id_misi_rpjmd_edit);
                            $data->delete();
                             return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                        }
                            catch(QueryException $e){
                             $error_code = $e->errorInfo[1] ;
                             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
                    }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        } 
    }

    public function getTujuanRPJMD($id_misi_rpjmd)
    {
      $rpjmdtujuan = DB::SELECT('SELECT a.uraian_visi_rpjmd, a.no_urut AS no_visi, b.uraian_misi_rpjmd, b.no_urut AS no_misi, 
                        c.thn_id_rpjmd, c.no_urut, c.id_misi_rpjmd, c.id_tujuan_rpjmd, c.id_perubahan, c.uraian_tujuan_rpjmd, 
                        a.id_visi_rpjmd, b.id_misi_rpjmd,  COALESCE(d.jml_indikator,0) as jml_indikator
                        FROM trx_rpjmd_visi AS a
                        INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd
                        INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd
                        LEFT OUTER JOIN 
                        (SELECT id_tujuan_rpjmd, COUNT(id_indikator_tujuan_rpjmd) as jml_indikator FROM trx_rpjmd_tujuan_indikator GROUP BY id_tujuan_rpjmd) AS d 
                        ON c.id_tujuan_rpjmd = d.id_tujuan_rpjmd 
                        WHERE c.id_misi_rpjmd = '.$id_misi_rpjmd.' ORDER BY c.no_urut ASC');

      return DataTables::of($rpjmdtujuan)
        ->addColumn('details_url', function($rpjmdtujuan) {
            return url('rpjmd/getIndikatorTujuan/'.$rpjmdtujuan->id_tujuan_rpjmd);
        })
        ->addColumn('action', function ($rpjmdtujuan) {
        	return '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                </button>
            		<ul class="dropdown-menu dropdown-menu-right">
            			<li>
            				<a class="edit-tujuan dropdown-item"><i class="fa fa-bullseye fa-fw fa-lg text-success"></i></i> Lihat Data Tujuan</a>
                        </li>
                        <li>
                            <a class="add-indikator dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Tujuan</a>
                        </li>
            		</ul>
            </div>';})
        ->make(true);
    }

    public function addTujuan(Request $request)
    {
        $rules = [
            'id_misi_rpjmd_edit'=>'required',
            'ur_tujuan_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_misi_rpjmd_edit.required'=>'ID Misi RPJMD Kosong',
            'ur_tujuan_rpjmd_edit.required'=>'Uraian Tujuan Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Tujuan Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {            
            $data = new TrxRpjmdTujuan();
            $data->thn_id_rpjmd= Session::get('xIdPemda');
            $data->id_misi_rpjmd= $request->id_misi_rpjmd_edit;
            $data->uraian_tujuan_rpjmd= $request->ur_tujuan_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
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

    public function editTujuan(Request $request)
    {
        $rules = [
            'id_misi_rpjmd_edit'=>'required',
            'id_tujuan_rpjmd_edit'=>'required',
            'ur_tujuan_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_misi_rpjmd_edit.required'=>'ID Misi RPJMD Kosong',
            'id_tujuan_rpjmd_edit.required'=>'ID Tujuan RPJMD Kosong',
            'ur_tujuan_rpjmd_edit.required'=>'Uraian Tujuan Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Tujuan Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {   
            $data = TrxRpjmdTujuan::find($request->id_tujuan_rpjmd_edit);
            $data->id_misi_rpjmd= $request->id_misi_rpjmd_edit;
            $data->uraian_tujuan_rpjmd= $request->ur_tujuan_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
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

    public function deleteTujuan(Request $request){
        $rules = [
            'id_misi_rpjmd_edit'=>'required',
            'id_tujuan_rpjmd_edit'=>'required',
        ];
        $messages =[
            'id_misi_rpjmd_edit.required'=>'ID Misi RPJMD Kosong',
            'id_tujuan_rpjmd_edit.required'=>'ID Tujuan RPJMD Kosong',           
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $cek = $this->checkTujuan($request->id_misi_rpjmd_edit);

                if($cek[0]->id_status_dokumen == 0){
                    $xData=DB::select('SELECT * FROM trx_rpjmd_tujuan WHERE id_tujuan_rpjmd ='.$request->id_tujuan_rpjmd_edit.' AND sumber_data = 0');
                    if ($xData == null){
                        try{
                            $data = TrxRpjmdTujuan::find($request->id_tujuan_rpjmd_edit);
                            $data->delete();
                             return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                        }
                            catch(QueryException $e){
                             $error_code = $e->errorInfo[1] ;
                             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
                    }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        } 
    }

    public function getSasaranRPJMD($id_tujuan_rpjmd)
    {
      $rpjmdsasaran = DB::select('SELECT d.thn_id_rpjmd, d.no_urut, d.id_tujuan_rpjmd, d.id_sasaran_rpjmd, d.id_perubahan, d.uraian_sasaran_rpjmd, b.id_visi_rpjmd,
                        b.id_misi_rpjmd, b.no_urut as no_misi, c.no_urut as no_tujuan, a.no_urut as no_visi, COALESCE(e.jml_indikator,0) as jml_indikator
                        FROM trx_rpjmd_visi AS a
                        INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd
                        INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd
                        INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd
                        LEFT OUTER JOIN 
                            (SELECT id_sasaran_rpjmd, COUNT(id_indikator_sasaran_rpjmd) as jml_indikator FROM trx_rpjmd_sasaran_indikator GROUP BY id_sasaran_rpjmd) AS e 
                        ON d.id_sasaran_rpjmd = e.id_sasaran_rpjmd
                        WHERE d.id_tujuan_rpjmd = '.$id_tujuan_rpjmd.' ORDER BY d.no_urut ASC');

      return DataTables::of($rpjmdsasaran)
        ->addColumn('details_url', function($rpjmdsasaran) {
            return url('rpjmd/getIndikatorSasaran/'.$rpjmdsasaran->id_sasaran_rpjmd);
        })
        ->addColumn('action', function ($rpjmdsasaran) {
            return '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                </button>
            		<ul class="dropdown-menu dropdown-menu-right">
            			<li>
                            <a class="btnEditSasaran dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Lihat Data Sasaran</a>
                        </li>
                        <li>
                            <a class="view-rpjmdstrategi dropdown-item"><i class="fa fa-map-o fa-fw fa-lg text-info"></i> Lihat Strategi</a>
                        </li>
                        <li>
                            <a class="view-rpjmdkebijakan dropdown-item"><i class="fa fa-gavel fa-fw fa-lg text-primary"></i> Lihat Kebijakan</a>
                        </li>
                        <li>
                            <a class="btnAddIndikatorSasaran dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Sasaran</a>
                        </li>
            		</ul>
            </div> 
					';})
        ->make(true);
    }

    public function addSasaran(Request $request)
    {
        $rules = [
            'id_tujuan_rpjmd_edit'=>'required',
            'ur_sasaran_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_tujuan_rpjmd_edit.required'=>'ID Tujuan RPJMD Kosong',
            'ur_sasaran_rpjmd_edit.required'=>'Uraian Sasaran Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Sasaran Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {            
            $data = new TrxRpjmdSasaran();
            $data->thn_id_rpjmd= Session::get('xIdPemda');
            $data->id_tujuan_rpjmd= $request->id_tujuan_rpjmd_edit;
            $data->uraian_sasaran_rpjmd= $request->ur_sasaran_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
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

    public function editSasaran(Request $request)
    {
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'id_tujuan_rpjmd_edit'=>'required',
            'ur_sasaran_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'id_tujuan_rpjmd_edit.required'=>'ID Tujuan RPJMD Kosong',
            'ur_sasaran_rpjmd_edit.required'=>'Uraian Sasarn Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Sasaran Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {   
            $data = TrxRpjmdSasaran::find($request->id_sasaran_rpjmd_edit);
            $data->id_tujuan_rpjmd= $request->id_tujuan_rpjmd_edit;
            $data->uraian_sasaran_rpjmd= $request->ur_sasaran_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
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

    public function deleteSasaran(Request $request){
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'id_tujuan_rpjmd_edit'=>'required',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'id_tujuan_rpjmd_edit.required'=>'ID Tujuan RPJMD Kosong',           
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $cek = $this->checkSasaran($request->id_tujuan_rpjmd_edit);

                if($cek[0]->id_status_dokumen == 0){
                    $xData=DB::select('SELECT * FROM trx_rpjmd_sasaran WHERE id_sasaran_rpjmd ='.$request->id_sasaran_rpjmd_edit.' AND sumber_data = 0');
                    if ($xData == null){
                        try{
                            $data = TrxRpjmdSasaran::find($request->id_sasaran_rpjmd_edit);
                            $data->delete();
                             return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                        }
                            catch(QueryException $e){
                             $error_code = $e->errorInfo[1] ;
                             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
                    }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        } 
    }

    public function getKebijakanRPJMD($id_sasaran_rpjmd)
    {
      $rpjmdkebijakan = DB::select('SELECT d.no_urut AS id_sasaran, b.id_visi_rpjmd, b.id_misi_rpjmd, b.no_urut AS id_misi, c.no_urut AS id_tujuan,
                        e.thn_id, e.no_urut, e.id_sasaran_rpjmd, e.id_kebijakan_rpjmd, e.id_perubahan, e.uraian_kebijakan_rpjmd
                        FROM trx_rpjmd_visi AS a
                        INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd
                        INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd
                        INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd
                        INNER JOIN trx_rpjmd_kebijakan AS e ON e.id_sasaran_rpjmd = d.id_sasaran_rpjmd 
                        WHERE e.id_sasaran_rpjmd = '.$id_sasaran_rpjmd.' ORDER BY e.no_urut DESC');

      return DataTables::of($rpjmdkebijakan)
          ->addColumn('action',function($rpjmdkebijakan){
        	return '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                </button>
            		<ul class="dropdown-menu dropdown-menu-right">
            			<li>
            				<a class="edit-kebijakan dropdown-item" data-id_kebijakan_rpjmd_kebijakan="'.$rpjmdkebijakan->id_kebijakan_rpjmd.'" data-thn_id_kebijakan="'.$rpjmdkebijakan->thn_id.'" data-id_sasaran_rpjmd_kebijakan="'.$rpjmdkebijakan->id_sasaran_rpjmd.'" data-id_perubahan_kebijakan="'.$rpjmdkebijakan->id_perubahan.'"  data-uraian_kebijakan_rpjmd_kebijakan="'.$rpjmdkebijakan->uraian_kebijakan_rpjmd.'" data-no_urut_kebijakan="'.$rpjmdkebijakan->no_urut.'"><i class="fa fa-gavel fa-fw fa-lg text-success"></i> Lihat Data kebijakan</a>
						</li>
            		</ul>
            </div>
					';})
        ->make(true);
    }

    public function addKebijakan(Request $request)
    {
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'ur_kebijakan_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'ur_kebijakan_rpjmd_edit.required'=>'Uraian Kebijakan Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Kebijakan Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {            
            $data = new TrxRpjmdKebijakan();
            $data->thn_id= Session::get('xIdPemda');
            $data->id_sasaran_rpjmd= $request->id_sasaran_rpjmd_edit;
            $data->uraian_kebijakan_rpjmd= $request->ur_kebijakan_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
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

    public function editKebijakan(Request $request)
    {
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'id_kebijakan_rpjmd_edit'=>'required',
            'ur_kebijakan_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'id_kebijakan_rpjmd_edit.required'=>'ID Kebijakan RPJMD Kosong',
            'ur_kebijakan_rpjmd_edit.required'=>'Uraian Kebijakan Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Kebijakan Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {   
            $data = TrxRpjmdKebijakan::find($request->id_kebijakan_rpjmd_edit);
            $data->id_sasaran_rpjmd= $request->id_sasaran_rpjmd_edit;
            $data->uraian_kebijakan_rpjmd= $request->ur_kebijakan_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
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

    public function deleteKebijakan(Request $request){
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'id_kebijakan_rpjmd_edit'=>'required',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'id_kebijakan_rpjmd_edit.required'=>'ID Kebijakan RPJMD Kosong',           
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $cek = $this->checkProgram($request->id_sasaran_rpjmd_edit);

                if($cek[0]->id_status_dokumen == 0){
                    $xData=DB::select('SELECT * FROM trx_rpjmd_kebijakan WHERE id_kebijakan_rpjmd ='.$request->id_kebijakan_rpjmd_edit.' AND sumber_data = 0');
                    if ($xData == null){
                        try{
                            $data = TrxRpjmdKebijakan::find($request->id_kebijakan_rpjmd_edit);
                            $data->delete();
                             return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                        }
                            catch(QueryException $e){
                             $error_code = $e->errorInfo[1] ;
                             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
                    }

            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        } 
    }

    public function getStrategiRPJMD($id_sasaran_rpjmd)
    {
      $rpjmdstrategi = DB::select('SELECT d.no_urut AS id_sasaran, b.id_visi_rpjmd, b.id_misi_rpjmd, b.no_urut AS id_misi, c.no_urut AS id_tujuan, e.thn_id,
                        e.no_urut, e.id_sasaran_rpjmd, e.id_strategi_rpjmd, e.id_perubahan, e.uraian_strategi_rpjmd
                        FROM trx_rpjmd_visi AS a
                        INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd
                        INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd
                        INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd
                        INNER JOIN trx_rpjmd_strategi AS e ON e.id_sasaran_rpjmd = d.id_sasaran_rpjmd 
                        WHERE e.id_sasaran_rpjmd = '.$id_sasaran_rpjmd.' ORDER BY e.no_urut DESC');


      return DataTables::of($rpjmdstrategi)
          ->addColumn('action',function($rpjmdstrategi){
          	return '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                </button>
            		<ul class="dropdown-menu dropdown-menu-right">
            			<li>
            				<a class="edit-strategi dropdown-item" data-id_strategi_rpjmd_strategi="'.$rpjmdstrategi->id_strategi_rpjmd.'" data-thn_id_strategi="'.$rpjmdstrategi->thn_id.'" data-id_sasaran_rpjmd_strategi="'.$rpjmdstrategi->id_sasaran_rpjmd.'" data-id_perubahan_strategi="'.$rpjmdstrategi->id_perubahan.'"  data-uraian_strategi_rpjmd_strategi="'.$rpjmdstrategi->uraian_strategi_rpjmd.'" data-no_urut_strategi="'.$rpjmdstrategi->no_urut.'"><i class="fa fa-map-o fa-fw fa-lg text-success"></i> Lihat Data strategi</a>
						</li>
            		</ul>
            </div>
					';})
        ->make(true);
    }

    public function addStrategi(Request $request)
    {
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'ur_strategi_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'ur_strategi_rpjmd_edit.required'=>'Uraian Strategi Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Strategi Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {            
            $data = new TrxRpjmdStrategi();
            $data->thn_id= Session::get('xIdPemda');
            $data->id_sasaran_rpjmd= $request->id_sasaran_rpjmd_edit;
            $data->uraian_strategi_rpjmd= $request->ur_strategi_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
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

    public function editStrategi(Request $request)
    {
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'id_strategi_rpjmd_edit'=>'required',
            'ur_strategi_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'id_strategi_rpjmd_edit.required'=>'ID Strategi RPJMD Kosong',
            'ur_strategi_rpjmd_edit.required'=>'Uraian Strategi Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Strategi Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {   
            $data = TrxRpjmdStrategi::find($request->id_strategi_rpjmd_edit);
            $data->id_sasaran_rpjmd= $request->id_sasaran_rpjmd_edit;
            $data->uraian_strategi_rpjmd= $request->ur_strategi_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
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

    public function deleteStrategi(Request $request){
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'id_strategi_rpjmd_edit'=>'required',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'id_strategi_rpjmd_edit.required'=>'ID Strategi RPJMD Kosong',           
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $cek = $this->checkProgram($request->id_sasaran_rpjmd_edit);

                if($cek[0]->id_status_dokumen == 0){
                    $xData=DB::select('SELECT * FROM trx_rpjmd_strategi WHERE id_strategi_rpjmd ='.$request->id_strategi_rpjmd_edit.' AND sumber_data = 0');
                    if ($xData == null){
                        try{
                            $data = TrxRpjmdStrategi::find($request->id_strategi_rpjmd_edit);
                            $data->delete();
                             return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                        }
                            catch(QueryException $e){
                             $error_code = $e->errorInfo[1] ;
                             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
                    }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        } 
    }

    public function getProgramRPJMD($id_sasaran_rpjmd)
    {

      $rpjmdprogram = DB::Select('SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) as kd_sasaran,e.id_program_rpjmd, 
                        e.no_urut,e.uraian_program_rpjmd,e.thn_id,e.id_sasaran_rpjmd,e.id_perubahan,(e.pagu_tahun1/1000000) as pagu_tahun1,(e.pagu_tahun2/1000000) as pagu_tahun2, 
                        (e.pagu_tahun3/1000000) as pagu_tahun3,(e.pagu_tahun4/1000000) as pagu_tahun4,(e.pagu_tahun5/1000000) as pagu_tahun5,(e.total_pagu/1000000) as total_pagu,
                        (e.pagu_tahun1) as pagu_tahun1a, (e.pagu_tahun2) as pagu_tahun2a, (e.pagu_tahun3) as pagu_tahun3a,(e.pagu_tahun4) as pagu_tahun4a,(e.pagu_tahun5) as pagu_tahun5a,
                        (e.total_pagu) as total_pagua, d.no_urut as id_sasaran 
                        FROM trx_rpjmd_visi AS a 
                        INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd 
                        INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd 
                        INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd 
                        INNER JOIN trx_rpjmd_program AS e ON e.id_sasaran_rpjmd = d.id_sasaran_rpjmd 
                        WHERE d.id_sasaran_rpjmd = '.$id_sasaran_rpjmd);

      return DataTables::of($rpjmdprogram)
      ->addColumn('details_url', function($rpjmdprogram) {
        return url('rpjmd/getIndikatorProgram/'.$rpjmdprogram->id_program_rpjmd);
      })
      ->addColumn('action', function ($rpjmdprogram) {
      	return '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                </button>
            		<ul class="dropdown-menu dropdown-menu-right">
            			<li>
                            <a class="edit-program dropdown-item" data-id_program_rpjmd_program="'.$rpjmdprogram->id_program_rpjmd.'" data-thn_id_program="'.$rpjmdprogram->thn_id.'" data-id_sasaran_rpjmd_program="'.$rpjmdprogram->id_sasaran_rpjmd.'" data-id_perubahan_program="'.$rpjmdprogram->id_perubahan.'"  data-uraian_program_rpjmd_program="'.$rpjmdprogram->uraian_program_rpjmd.'" data-no_urut_program="'.$rpjmdprogram->no_urut.'"  data-pagu_tahun1_program="'.$rpjmdprogram->pagu_tahun1a.'" data-pagu_tahun2_program="'.$rpjmdprogram->pagu_tahun2a.'" data-pagu_tahun3_program="'.$rpjmdprogram->pagu_tahun3a.'" data-pagu_tahun4_program="'.$rpjmdprogram->pagu_tahun4a.'" data-pagu_tahun5_program="'.$rpjmdprogram->pagu_tahun5a.'" ><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Lihat Data Program</a>
                        </li>
                        <li>
                            <a class="btnAddIndikatorProgram dropdown-item" data-id_program="'.$rpjmdprogram->id_program_rpjmd.'" ><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator</a>
                        </li>
                        <li>
                            <a class="view-rpjmdurusan dropdown-item" data-id_program="'.$rpjmdprogram->id_program_rpjmd.'" ><i class="fa fa-puzzle-piece fa-fw fa-lg text-info"></i> Lihat Urusan</a>
                        </li>
                        <li>
                            <a class="repivot-renstra dropdown-item hidden"><i class="fa fa-refresh fa-fw fa-lg text-warning"></i> Re-Pivot Renstra</a>
                        </li>
                        <li>
                            <a class="post-urbidprog dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-success"></i> Posting Program Urusan</a>
            			</li>
            		</ul>
                </div>
				';})
				->make(true);
    }

    public function addProgram(Request $request)
    {
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'ur_program_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
            'pagu1_edit'=>'required | numeric',
            'pagu2_edit'=>'required | numeric',
            'pagu3_edit'=>'required | numeric',
            'pagu4_edit'=>'required | numeric',
            'pagu5_edit'=>'required | numeric',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'ur_program_rpjmd_edit.required'=>'Uraian Program Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Program Kosong',
            'pagu1_edit.required'=>'Nilai Pagu Tahun ke-1 Kosong',
            'pagu2_edit.required'=>'Nilai Pagu Tahun ke-2 Kosong',
            'pagu3_edit.required'=>'Nilai Pagu Tahun ke-3 Kosong',
            'pagu4_edit.required'=>'Nilai Pagu Tahun ke-4 Kosong',
            'pagu5_edit.required'=>'Nilai Pagu Tahun ke-5 Kosong',
            'pagu1_edit.numeric'=>'Nilai Pagu Tahun ke-1 Harus Angka',
            'pagu2_edit.numeric'=>'Nilai Pagu Tahun ke-2 Harus Angka',
            'pagu3_edit.numeric'=>'Nilai Pagu Tahun ke-3 Harus Angka',
            'pagu4_edit.numeric'=>'Nilai Pagu Tahun ke-4 Harus Angka',
            'pagu5_edit.numeric'=>'Nilai Pagu Tahun ke-5 Harus Angka',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {            
            $data = new TrxRpjmdProgram();
            $data->thn_id= Session::get('xIdPemda');
            $data->id_sasaran_rpjmd= $request->id_sasaran_rpjmd_edit;
            $data->uraian_program_rpjmd= $request->ur_program_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
            $data->pagu_tahun1= $request->pagu1_edit;
            $data->pagu_tahun2= $request->pagu2_edit;
            $data->pagu_tahun3= $request->pagu3_edit;
            $data->pagu_tahun4= $request->pagu4_edit;
            $data->pagu_tahun5= $request->pagu5_edit;
            $data->total_pagu= $request->pagu1_edit+$request->pagu2_edit+$request->pagu3_edit+$request->pagu4_edit+$request->pagu5_edit;
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
            'id_program_rpjmd_edit'=>'required',
            'id_sasaran_rpjmd_edit'=>'required',
            'ur_program_rpjmd_edit'=>'required',
            'id_perubahan_edit'=>'required',
            'no_urut_edit'=>'required',
            'pagu1_edit'=>'required | numeric',
            'pagu2_edit'=>'required | numeric',
            'pagu3_edit'=>'required | numeric',
            'pagu4_edit'=>'required | numeric',
            'pagu5_edit'=>'required | numeric',
        ];
        $messages =[
            'id_program_rpjmd_edit.required'=>'ID Program RPJMD Kosong',
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'ur_program_rpjmd_edit.required'=>'Uraian Program Kosong',
            'id_perubahan_edit.required'=>'ID Revisi Kosong',
            'no_urut_edit.required'=>'Nomor Urut Program Kosong',
            'pagu1_edit.required'=>'Nilai Pagu Tahun ke-1 Kosong',
            'pagu2_edit.required'=>'Nilai Pagu Tahun ke-2 Kosong',
            'pagu3_edit.required'=>'Nilai Pagu Tahun ke-3 Kosong',
            'pagu4_edit.required'=>'Nilai Pagu Tahun ke-4 Kosong',
            'pagu5_edit.required'=>'Nilai Pagu Tahun ke-5 Kosong',
            'pagu1_edit.numeric'=>'Nilai Pagu Tahun ke-1 Harus Angka',
            'pagu2_edit.numeric'=>'Nilai Pagu Tahun ke-2 Harus Angka',
            'pagu3_edit.numeric'=>'Nilai Pagu Tahun ke-3 Harus Angka',
            'pagu4_edit.numeric'=>'Nilai Pagu Tahun ke-4 Harus Angka',
            'pagu5_edit.numeric'=>'Nilai Pagu Tahun ke-5 Harus Angka',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {   
            $data = TrxRpjmdProgram::find($request->id_program_rpjmd_edit);
            $data->id_sasaran_rpjmd= $request->id_sasaran_rpjmd_edit;
            $data->uraian_program_rpjmd= $request->ur_program_rpjmd_edit;
            $data->id_perubahan= $request->id_perubahan_edit;
            $data->no_urut= $request->no_urut_edit;
            $data->pagu_tahun1= $request->pagu1_edit;
            $data->pagu_tahun2= $request->pagu2_edit;
            $data->pagu_tahun3= $request->pagu3_edit;
            $data->pagu_tahun4= $request->pagu4_edit;
            $data->pagu_tahun5= $request->pagu5_edit;
            $data->total_pagu= $request->pagu1_edit+$request->pagu2_edit+$request->pagu3_edit+$request->pagu4_edit+$request->pagu5_edit;
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

    public function deleteProgram(Request $request){
        $rules = [
            'id_sasaran_rpjmd_edit'=>'required',
            'id_program_rpjmd_edit'=>'required',
        ];
        $messages =[
            'id_sasaran_rpjmd_edit.required'=>'ID Sasaran RPJMD Kosong',
            'id_program_rpjmd_edit.required'=>'ID Program RPJMD Kosong',           
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $cek = $this->checkProgram($request->id_sasaran_rpjmd_edit);

                if($cek[0]->id_status_dokumen == 0){
                    $xData=DB::select('SELECT * FROM trx_rpjmd_program WHERE id_program_rpjmd ='.$request->id_program_rpjmd_edit.' AND sumber_data = 0');
                    if ($xData == null){
                        try{
                            $data = TrxRpjmdProgram::find($request->id_program_rpjmd_edit);
                            $data->delete();
                             return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                        }
                            catch(QueryException $e){
                             $error_code = $e->errorInfo[1] ;
                             return response ()->json (['pesan'=>'Data Gagal Dihapus ('.$error_code.')','status_pesan'=>'0']);
                        }
                    } else {
                        return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
                    }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        } 
    }

    public function getIndikatorProgramRPJMD($id_program_rpjmd)
    {
      $rpjmdindikator = DB::Select('SELECT b.thn_id, b.no_urut,b.id_program_rpjmd,b.id_indikator_program_rpjmd,b.id_perubahan,b.id_indikator,b.uraian_indikator_program_rpjmd,
                        b.tolok_ukur_indikator,b.angka_awal_periode,b.angka_tahun1,b.angka_tahun2,b.angka_tahun3,b.angka_tahun4,b.angka_tahun5,b.angka_akhir_periode,a.no_urut,
                        CONCAT(f.no_urut,".",e.no_urut,".",d.no_urut,".",c.no_urut,".",a.no_urut) as kd_program
                        FROM trx_rpjmd_program AS a
                        INNER JOIN trx_rpjmd_program_indikator AS b ON b.id_program_rpjmd = a.id_program_rpjmd
                        INNER JOIN trx_rpjmd_sasaran AS c ON a.id_sasaran_rpjmd = c.id_sasaran_rpjmd
                        INNER JOIN trx_rpjmd_tujuan AS d ON c.id_tujuan_rpjmd = d.id_tujuan_rpjmd
                        INNER JOIN trx_rpjmd_misi AS e ON d.id_misi_rpjmd = e.id_misi_rpjmd
                        INNER JOIN trx_rpjmd_visi AS f ON e.id_visi_rpjmd = f.id_visi_rpjmd 
                        WHERE b.id_program_rpjmd='.$id_program_rpjmd.' ORDER BY b.no_urut DESC');

      return DataTables::of($rpjmdindikator)
            ->addColumn('action', function ($rpjmdindikator) {
            return '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="edit-indikatorprog dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Lihat Indikator</a>
                            </li>
                        </ul>
                    </div>';})
            ->make(true);
    }

    public function getUrusanProgramRPJMD($id_program_rpjmd)
    {
      $rpjmdurusan = DB::Select('SELECT CONCAT(f.no_urut,".",e.no_urut,".",d.no_urut,".",c.no_urut,".",a.no_urut) AS kd_program,
                    CONCAT(h.kd_urusan,".",h.kd_bidang) AS kode_bid,h.kd_urusan,
                    g.thn_id,g.no_urut,g.id_urbid_rpjmd,g.id_program_rpjmd,g.id_bidang,h.nm_bidang,i.nm_urusan
                    FROM trx_rpjmd_program AS a
                    INNER JOIN trx_rpjmd_sasaran AS c ON a.id_sasaran_rpjmd = c.id_sasaran_rpjmd
                    INNER JOIN trx_rpjmd_tujuan AS d ON c.id_tujuan_rpjmd = d.id_tujuan_rpjmd
                    INNER JOIN trx_rpjmd_misi AS e ON d.id_misi_rpjmd = e.id_misi_rpjmd
                    INNER JOIN trx_rpjmd_visi AS f ON e.id_visi_rpjmd = f.id_visi_rpjmd
                    INNER JOIN trx_rpjmd_program_urusan AS g ON g.id_program_rpjmd = a.id_program_rpjmd
                    INNER JOIN ref_bidang AS h ON g.id_bidang = h.id_bidang
                    INNER JOIN ref_urusan AS i ON h.kd_urusan = i.kd_urusan
                    WHERE g.id_program_rpjmd='.$id_program_rpjmd.' ORDER BY g.id_bidang DESC');

      return DataTables::of($rpjmdurusan)
            ->addColumn('action', function ($rpjmdurusan) {
                
            return '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="edit-urbidprog dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Edit Urusan</a>
                                <a class="del-urbidprog dropdown-item"><i class="fa fa-trash fa-fw fa-lg text-danger"></i> Hapus Urusan</a>
                                <a class="view-rpjmdpelaksana dropdown-item" data-id_urusan="'.$rpjmdurusan->id_urbid_rpjmd.'"><i class="fa fa-users fa-fw fa-lg text-warning"></i> Lihat Pelaksana</a>
                            </li>
                        </ul>
                    </div>';})
            ->make(true);
    }

    public function addUrusan(Request $request)
    {
        $rules = [
            'no_urut'=>'required',
            'id_program_rpjmd'=>'required',
            'id_bidang'=>'required',
        ];
        $messages =[
            'no_urut.required'=>'Nomor Urut Kosong',
            'id_program_rpjmd.required'=>'ID Program RPJMD Kosong',  
            'id_bidang.required'=>'ID Bidang Kosong',          
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {              
            $xData=DB::select('SELECT * FROM trx_rpjmd_program_urusan WHERE thn_id ='.Session::get('xIdPemda').' AND id_program_rpjmd = '.$request->id_program_rpjmd.' AND id_bidang ='.$request->id_bidang);

            if ($xData != null){
                return response ()->json (['pesan'=>'Data Gagal Disimpan (Urusan - Bidang sudah dipakai)','status_pesan'=>'0']);
            } else {
                $data = new TrxRpjmdProgramUrusan ();
                $data->thn_id= Session::get('xIdPemda');
                $data->no_urut= $request->no_urut;
                $data->id_program_rpjmd= $request->id_program_rpjmd;
                $data->id_bidang= $request->id_bidang;
                $data->status_data= 0;
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
    }

    public function editUrusan(Request $request)
    {  
        $rules = [
            'no_urut'=>'required',
            'id_program_rpjmd'=>'required',
            'id_bidang'=>'required',
            'id_urbid_rpjmd'=>'required',
            'status_data'=>'required',
        ];
        $messages =[
            'no_urut.required'=>'Nomor Urut Kosong',
            'id_program_rpjmd.required'=>'ID Program RPJMD Kosong',  
            'id_bidang.required'=>'ID Bidang Kosong',        
            'id_urbid_rpjmd.required'=>'ID Urusan RPJMD Kosong',  
            'status_data.required'=>'Status Data Urusan RPJMD Kosong',       
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {              
              $xData=DB::select('SELECT * FROM trx_rpjmd_program_urusan 
                    WHERE id_program_rpjmd = '.$request->id_program_rpjmd.' AND no_urut ='.$request->no_urut.' AND id_bidang ='.$request->id_bidang.' AND id_urbid_rpjmd ='.$request->id_urbid_rpjmd);

              if ($xData != null){
                    return response ()->json (['pesan'=>'Data Tetap Sama Tidak Ada Perubahan','status_pesan'=>'0']);
                } else {
                  $data = TrxRpjmdProgramUrusan::find($request->id_urbid_rpjmd);
                  $data->no_urut= $request->no_urut;
                  $data->id_program_rpjmd= $request->id_program_rpjmd;
                  $data->id_bidang= $request->id_bidang;
                  $data->status_data= $request->status_data;
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
    }

    public function delUrusan(Request $request)
    {
        $rules = [
            'id_urbid_rpjmd'=>'required',
            'id_program_rpjmd_edit'=>'required',
        ];
        $messages =[
            'id_urbid_rpjmd.required'=>'ID Urusan RPJMD Kosong',
            'id_program_rpjmd_edit.required'=>'ID Program RPJMD Kosong',           
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $cek = $this->checkUrusan($request->id_program_rpjmd_edit);

            if($cek[0]->id_status_dokumen == 0){
                $xData=DB::select('SELECT * FROM trx_rpjmd_program_urusan WHERE id_urbid_rpjmd ='.$request->id_urbid_rpjmd.' AND sumber_data = 0');
                if ($xData == null){
                    $data = TrxRpjmdProgramUrusan::where('id_urbid_rpjmd',$request->id_urbid_rpjmd)->delete();
                    if($data != 0){
                      return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                    } else {
                      return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
                    }
                } else {
                    return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
                }   
            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        } 
    }

    public function getPelaksanaProgramRPJMD($id_urbid_rpjmd)
    {
      $rpjmdpelaksana = DB::Select('SELECT CONCAT(f.no_urut,".",e.no_urut,".",d.no_urut,".",c.no_urut,".",a.no_urut) AS kd_program,
                        CONCAT(h.kd_urusan,".",h.kd_bidang,".",i.kd_unit) AS kd_unit,i.nm_unit,j.thn_id, j.no_urut, j.id_urbid_rpjmd,
                        j.id_pelaksana_rpjmd,j.id_unit,j.id_perubahan,j.pagu_tahun1,j.pagu_tahun2,j.pagu_tahun3,j.pagu_tahun4,j.pagu_tahun5
                        FROM trx_rpjmd_program AS a
                        INNER JOIN trx_rpjmd_sasaran AS c ON a.id_sasaran_rpjmd = c.id_sasaran_rpjmd
                        INNER JOIN trx_rpjmd_tujuan AS d ON c.id_tujuan_rpjmd = d.id_tujuan_rpjmd
                        INNER JOIN trx_rpjmd_misi AS e ON d.id_misi_rpjmd = e.id_misi_rpjmd
                        INNER JOIN trx_rpjmd_visi AS f ON e.id_visi_rpjmd = f.id_visi_rpjmd
                        INNER JOIN trx_rpjmd_program_urusan AS g ON g.id_program_rpjmd = a.id_program_rpjmd
                        INNER JOIN trx_rpjmd_program_pelaksana AS j ON j.id_urbid_rpjmd = g.id_urbid_rpjmd
                        INNER JOIN ref_unit AS i ON i.id_unit = j.id_unit
                        INNER JOIN ref_bidang AS h ON h.id_bidang = i.id_bidang
                        WHERE g.id_urbid_rpjmd = '.$id_urbid_rpjmd.' ORDER BY i.id_unit DESC');

      return DataTables::of($rpjmdpelaksana)
            ->addColumn('action', function ($rpjmdpelaksana) {
            return '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>  
                                <a class="edit-pelaksanaprog dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Edit Pelaksana</a>                              
                                <a class="del-pelaksanaprog dropdown-item"><i class="fa fa-trash fa-fw fa-lg text-danger"></i> Hapus Pelaksana</a>
                            </li>
                        </ul>
                    </div>';})
            ->make(true);
    }

    public function addPelaksana(Request $request)
    {
        $rules = [
            'id_urbid_rpjmd'=>'required',
            'no_urut'=>'required',
            'id_unit'=>'required',
        ];
        $messages =[
            'id_urbid_rpjmd.required'=>'ID Urusan RPJMD Kosong',
            'no_urut.required'=>'Nomor Urut Kosong',  
            'id_unit.required'=>'Unit Perangkat Daerah Kosong',          
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $xData=DB::select('SELECT * FROM trx_rpjmd_program_pelaksana WHERE thn_id ='.Session::get('xIdPemda').' AND id_urbid_rpjmd = '.$request->id_urbid_rpjmd.' AND id_unit ='.$request->id_unit);

            if ($xData != null){
                return response ()->json (['pesan'=>'Data Gagal Disimpan (Unit Pelaksana sudah dipakai)','status_pesan'=>'0']);
            } else {
                $data = new TrxRpjmdProgramPelaksana();
                $data->thn_id= Session::get('xIdPemda');
                $data->id_urbid_rpjmd= $request->id_urbid_rpjmd;
                $data->no_urut= $request->no_urut;
                $data->id_unit= $request->id_unit;
                $data->status_data= 0;
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
    }

    public function editPelaksana(Request $request)
    {
        $rules = [
            'id_urbid_rpjmd'=>'required',
            'no_urut'=>'required',
            'id_unit'=>'required',
            'id_pelaksana_rpjmd'=>'required',
            'status_data'=>'required',
        ];
        $messages =[
            'id_urbid_rpjmd.required'=>'ID Urusan RPJMD Kosong',
            'no_urut.required'=>'Nomor Urut Kosong',  
            'id_unit.required'=>'Unit Perangkat Daerah Kosong',  
            'id_pelaksana_rpjmd.required'=>'ID Pelaksana RPJMD Kosong',  
            'status_data.required'=>'Status Data Pelaksana RPJMD Kosong',          
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else { 

                $data = TrxRpjmdProgramPelaksana::find($request->id_pelaksana_rpjmd);
                $data->id_urbid_rpjmd= $request->id_urbid_rpjmd;
                $data->no_urut= $request->no_urut;
                $data->id_unit= $request->id_unit;
                $data->status_data= $request->status_data;
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

    public function delPelaksana(Request $request)
    {
        $rules = [
            'id_urbid_rpjmd'=>'required',
            'id_pelaksana_rpjmd'=>'required',
        ];
        $messages =[
            'id_urbid_rpjmd.required'=>'ID Urusan RPJMD Kosong',
            'id_pelaksana_rpjmd.required'=>'ID Pelaksana RPJMD Kosong',           
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {  
            $cek = $this->checkPelaksana($request->id_urbid_rpjmd);

            if($cek[0]->id_status_dokumen == 0){
                $xData=DB::select('SELECT * FROM trx_rpjmd_program_pelaksana WHERE id_pelaksana_rpjmd ='.$request->id_pelaksana_rpjmd.' AND sumber_data = 0');
                if ($xData == null){
                    $data = TrxRpjmdProgramPelaksana::where('id_pelaksana_rpjmd',$request->id_pelaksana_rpjmd)->delete();
                    if($data != 0){
                      return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
                    } else {
                      return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
                    }
                } else {
                    return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
                }   
            } else {
                return response ()->json (['pesan'=>'Data Gagal Dihapus (Status Dokumen telah terposting)','status_pesan'=>'0']);
            }
        }               
    }

    public function getPendapatanRPJMD($id_visi_rpjmd)
    {
      $rpjmdprogram = DB::Select('SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) as kd_sasaran,e.id_program_rpjmd, 
                        e.no_urut,e.uraian_program_rpjmd,e.thn_id,e.id_sasaran_rpjmd,e.id_perubahan,(e.pagu_tahun1/1000000) as pagu_tahun1,(e.pagu_tahun2/1000000) as pagu_tahun2, 
                        (e.pagu_tahun3/1000000) as pagu_tahun3,(e.pagu_tahun4/1000000) as pagu_tahun4,(e.pagu_tahun5/1000000) as pagu_tahun5,(e.total_pagu/1000000) as total_pagu,
                        (e.pagu_tahun1) as pagu_tahun1a, (e.pagu_tahun2) as pagu_tahun2a, (e.pagu_tahun3) as pagu_tahun3a,(e.pagu_tahun4) as pagu_tahun4a,(e.pagu_tahun5) as pagu_tahun5a,
                        (e.total_pagu) as total_pagua, d.no_urut as id_sasaran 
                        FROM trx_rpjmd_visi AS a 
                        INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd 
                        INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd 
                        INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd 
                        INNER JOIN trx_rpjmd_program AS e ON e.id_sasaran_rpjmd = d.id_sasaran_rpjmd 
                        WHERE b.no_urut = 98 AND b. id_visi_rpjmd = '.$id_visi_rpjmd);

      return DataTables::of($rpjmdprogram)
      ->addColumn('action', function ($rpjmdprogram) {
        return '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditPendapatan dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Lihat Data Pendapatan</a>
                        </li>
                        <li>
                            <a class="btnViewUrusanPdt dropdown-item"><i class="fa fa-puzzle-piece fa-fw fa-lg text-info"></i> Lihat Urusan</a>
                        </li>
                        <li>
                            <a class="btnPostUrusanPdt dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-success"></i> Posting Urusan</a>
                        </li>
                    </ul>
                </div>
                ';})
                ->make(true);
    }

    public function getBtlRPJMD($id_visi_rpjmd)
    {
      $rpjmdprogram = DB::Select('SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) as kd_sasaran,e.id_program_rpjmd, 
                        e.no_urut,e.uraian_program_rpjmd,e.thn_id,e.id_sasaran_rpjmd,e.id_perubahan,(e.pagu_tahun1/1000000) as pagu_tahun1,(e.pagu_tahun2/1000000) as pagu_tahun2, 
                        (e.pagu_tahun3/1000000) as pagu_tahun3,(e.pagu_tahun4/1000000) as pagu_tahun4,(e.pagu_tahun5/1000000) as pagu_tahun5,(e.total_pagu/1000000) as total_pagu,
                        (e.pagu_tahun1) as pagu_tahun1a, (e.pagu_tahun2) as pagu_tahun2a, (e.pagu_tahun3) as pagu_tahun3a,(e.pagu_tahun4) as pagu_tahun4a,(e.pagu_tahun5) as pagu_tahun5a,
                        (e.total_pagu) as total_pagua, d.no_urut as id_sasaran 
                        FROM trx_rpjmd_visi AS a 
                        INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd 
                        INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd 
                        INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd 
                        INNER JOIN trx_rpjmd_program AS e ON e.id_sasaran_rpjmd = d.id_sasaran_rpjmd 
                        WHERE b.no_urut = 99 AND b. id_visi_rpjmd = '.$id_visi_rpjmd);

      return DataTables::of($rpjmdprogram)
      ->addColumn('action', function ($rpjmdprogram) {
        return '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditBtl dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Lihat Data Belanja</a>
                        </li>
                        <li>
                            <a class="btnViewUrusanBtl dropdown-item"><i class="fa fa-puzzle-piece fa-fw fa-lg text-info"></i> Lihat Urusan</a>
                        </li>
                        <li>
                            <a class="btnPostUrusanBtl dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-success"></i> Posting Urusan</a>
                        </li>
                    </ul>
                </div>
                ';})
                ->make(true);
    }

    public function ReprosesPivotPelaksana(Request $req)
    {
      
      $xDelete = DB::delete('DELETE a.* FROM trx_rkpd_rpjmd_program_pelaksana a inner join trx_rkpd_rpjmd_ranwal b on a.id_rkpd_rpjmd = b.id_rkpd_rpjmd
            WHERE b.id_program_rpjmd='.$req->id_program_rpjmd);

      if ($xDelete != 0){
        $result=DB::Insert('INSERT INTO trx_rkpd_rpjmd_program_pelaksana(tahun_rkpd,id_rkpd_rpjmd,id_pelaksana_rpjmd,id_unit,id_urbid_rpjmd,id_bidang)
                    SELECT x.tahun_rkpd, x.id_rkpd_rpjmd, (@id:=@id+1) as id_pelaksana_rpjmd, x.id_unit,x.id_urbid_rpjmd,x.id_bidang  FROM
                    (SELECT a.tahun_rkpd, a.id_rkpd_rpjmd, id_unit,b.id_urbid_rpjmd,b.id_bidang 
                    FROM trx_rkpd_rpjmd_ranwal  a  
                    INNER JOIN trx_rpjmd_program_urusan b ON a.id_program_rpjmd = b.id_program_rpjmd     
                    INNER JOIN trx_rpjmd_program_pelaksana c ON c.id_urbid_rpjmd = b.id_urbid_rpjmd where a.id_program_rpjmd='.$req->id_program_rpjmd.') x,
                    (select @id:=max(id_pelaksana_rpjmd) from trx_rkpd_rpjmd_program_pelaksana) x_id');

            if ($result != 0 ) {
                    return response ()->json (['pesan'=>'Data Pelaksana Berhasil Diposting','status_pesan'=>'1']);
                } else {
                    return response ()->json (['pesan'=>'Data Pelaksana Gagal Diposting','status_pesan'=>'0']);
                }
      } else {
        return response ()->json (['pesan'=>'Tidak Ada Data Pelaksana Diposting karena sudah di-Posting','status_pesan'=>'0']);
      }
    }

    public function RePivotRenstra(Request $req)
    {
      $vDelete_1 = DB::select('SELECT a.* FROM trx_rkpd_renstra_indikator as a
                  INNER JOIN trx_rkpd_renstra AS b ON a.id_rkpd_renstra = b.id_rkpd_renstra
                  WHERE b.id_program_rpjmd = '.$req->id_program_rpjmd);

      if ($vDelete_1 != null) {      
            $xDelete_1 = DB::delete('DELETE a.* FROM trx_rkpd_renstra_indikator as a
                        INNER JOIN trx_rkpd_renstra AS b ON a.id_rkpd_renstra = b.id_rkpd_renstra
                        WHERE b.id_program_rpjmd = '.$req->id_program_rpjmd);      
            if ($xDelete_1 == 0){return response ()->json (['pesan'=>'Mengkosongkan Indikator Renstra Gagal','status_pesan'=>'0']);}
      };

      $vDelete_2 = DB::select('SELECT a.* FROM trx_rkpd_renstra_pelaksana as a
                  INNER JOIN trx_rkpd_renstra AS b ON a.id_rkpd_renstra = b.id_rkpd_renstra
                  WHERE b.id_program_rpjmd = '.$req->id_program_rpjmd);

      if ($vDelete_2 != null) {
            $xDelete_2 = DB::delete('DELETE a.* FROM trx_rkpd_renstra_pelaksana as a
                        INNER JOIN trx_rkpd_renstra AS b ON a.id_rkpd_renstra = b.id_rkpd_renstra
                        WHERE b.id_program_rpjmd = '.$req->id_program_rpjmd);
      if ($xDelete_2 == 0){return response ()->json (['pesan'=>'Mengkosongkan Pelaksana Renstra Gagal','status_pesan'=>'0']);}
      };

      $vDelete_3 = DB::select('SELECT b.* FROM trx_rkpd_renstra AS b
                  WHERE b.id_program_rpjmd = '.$req->id_program_rpjmd);

      if ($vDelete_3 != null) {
            $xDelete_3 = DB::delete('DELETE a.* FROM trx_rkpd_renstra AS a WHERE a.id_program_rpjmd = '.$req->id_program_rpjmd);      
            if ($xDelete_3 == 0){return response ()->json (['pesan'=>'Mengkosongkan Kegiatan Renstra Gagal','status_pesan'=>'0']);}
      };
      
      $xRenstra = DB::Insert('INSERT INTO trx_rkpd_renstra(tahun_rkpd,id_rkpd_renstra,id_rkpd_rpjmd,id_program_rpjmd,id_unit,      
                id_visi_renstra,id_misi_renstra,id_tujuan_renstra,id_sasaran_renstra,id_program_renstra,     
                pagu_tahun_program,id_kegiatan_renstra,pagu_tahun_kegiatan,sumber_data)    
                SELECT DISTINCT g.tahun_rkpd,(@id:=@id+1) as id_rkpd_renstra,g.id_rkpd_rpjmd,g.id_program_rpjmd,i.id_unit,       
                i.id_visi_renstra,i.id_misi_renstra,i.id_tujuan_renstra,i.id_sasaran_renstra,i.id_program_renstra,      
                i.pagu_program_renja,i.id_kegiatan_renstra,i.pagu_kegiatan_renja,0 FROM       
                (SELECT DISTINCT e.id_program_rpjmd,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,     
                e.id_program_renstra,e.id_program_ref,e.pagu_tahun1 as pagu_program_renja,f.id_kegiatan_renstra,f.id_kegiatan_ref,        
                f.uraian_kegiatan_renstra,f.pagu_tahun1 as pagu_kegiatan_renja,g.tahun_1 as thn_rkpd     
                FROM trx_renstra_visi AS a     
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra       
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra       
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra      
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra     
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra,     
                ref_tahun g 
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'  
                UNION        
                SELECT DISTINCT e.id_program_rpjmd,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,      
                e.id_program_renstra,e.id_program_ref,e.pagu_tahun2 as pagu_program_renja,f.id_kegiatan_renstra,f.id_kegiatan_ref,       
                f.uraian_kegiatan_renstra,f.pagu_tahun2 as pagu_kegiatan_renja,g.tahun_2 as thn_rkpd      
                FROM trx_renstra_visi AS a      
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra         
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra          
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra      
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra      
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra,     
                ref_tahun g  
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'
                UNION        
                SELECT DISTINCT e.id_program_rpjmd,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,    
                e.id_program_renstra,e.id_program_ref,e.pagu_tahun3 as pagu_program_renja,f.id_kegiatan_renstra,f.id_kegiatan_ref,         
                f.uraian_kegiatan_renstra,f.pagu_tahun3 as pagu_kegiatan_renja,g.tahun_3 as thn_rkpd      
                FROM trx_renstra_visi AS a      
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra          
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra          
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra      
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra      
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra,     
                ref_tahun g  
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'              
                UNION         
                SELECT DISTINCT e.id_program_rpjmd,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,    
                e.id_program_renstra,e.id_program_ref,e.pagu_tahun4 as pagu_program_renja,f.id_kegiatan_renstra,f.id_kegiatan_ref,     
                f.uraian_kegiatan_renstra,f.pagu_tahun4 as pagu_kegiatan_renja,g.tahun_4 as thn_rkpd      
                FROM trx_renstra_visi AS a     
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra        
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra          
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra      
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra     
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra,    
                ref_tahun g   
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'      
                UNION          
                SELECT DISTINCT e.id_program_rpjmd,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,    
                e.id_program_renstra,e.id_program_ref,e.pagu_tahun5 as pagu_program_renja,f.id_kegiatan_renstra,f.id_kegiatan_ref,      
                f.uraian_kegiatan_renstra,f.pagu_tahun5 as pagu_kegiatan_renja,g.tahun_5 as thn_rkpd    
                FROM trx_renstra_visi AS a     
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra     
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra      
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra     
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra      
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra,     
                ref_tahun g
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.') AS i     
                INNER JOIN (SELECT DISTINCT a.id_rkpd_rpjmd,a.tahun_rkpd,a.thn_id_rpjmd,a.id_visi_rpjmd,a.id_misi_rpjmd,     
                a.id_tujuan_rpjmd,a.id_sasaran_rpjmd,a.id_program_rpjmd,a.pagu_program_rpjmd,b.id_bidang,b.id_unit,a.status_data     
                FROM trx_rkpd_rpjmd_ranwal a     
                INNER JOIN trx_rkpd_rpjmd_program_pelaksana  b ON b.id_rkpd_rpjmd = a.id_rkpd_rpjmd 
                WHERE a.id_program_rpjmd = '.$req->id_program_rpjmd.'
                ) AS g    
                ON i.id_program_rpjmd = g.id_program_rpjmd AND i.id_unit = g.id_unit and i.thn_rkpd=g.tahun_rkpd,    
                (Select @id:=max(id_rkpd_renstra) from trx_rkpd_renstra) j');

      if ($xRenstra != 0){
        $xIndikator=DB::Insert('INSERT INTO trx_rkpd_renstra_indikator (tahun_rkpd,id_rkpd_renstra,id_indikator_renstra,kd_indikator,   
                uraian_indikator_kegiatan,tolokukur_kegiatan,target_output)    
                SELECT b.tahun_rkpd,b.id_rkpd_renstra,(@id:=@id+1) as id_indikator_renstra,a.kd_indikator,a.uraian_indikator_kegiatan_renstra,    
                a.tolok_ukur_indikator,a.target_output FROM    
                (SELECT DISTINCT g.kd_indikator,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,  
                f.id_kegiatan_renstra,g.uraian_indikator_kegiatan_renstra,g.tolok_ukur_indikator,g.angka_tahun1 as target_output,h.tahun_1 as thn_rkpd    
                FROM trx_renstra_visi AS a    
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra    
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra   
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra   
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra    
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra      
                INNER JOIN trx_renstra_kegiatan_indikator AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,  
                ref_tahun h  
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'   
                UNION     
                SELECT DISTINCT g.kd_indikator,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,    
                f.id_kegiatan_renstra,g.uraian_indikator_kegiatan_renstra,g.tolok_ukur_indikator,g.angka_tahun2 as target_output,h.tahun_2 as thn_rkpd    
                FROM trx_renstra_visi AS a     
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra   
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra    
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra    
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra    
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra   
                INNER JOIN trx_renstra_kegiatan_indikator AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,    
                ref_tahun h 
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'     
                UNION      
                SELECT DISTINCT g.kd_indikator,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,    
                f.id_kegiatan_renstra,g.uraian_indikator_kegiatan_renstra,g.tolok_ukur_indikator,g.angka_tahun3 as target_output,h.tahun_3 as thn_rkpd      
                FROM trx_renstra_visi AS a    
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra    
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra   
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra   
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra   
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra    
                INNER JOIN trx_renstra_kegiatan_indikator AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,    
                ref_tahun h 
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'     
                UNION        
                SELECT DISTINCT g.kd_indikator,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,    
                f.id_kegiatan_renstra,g.uraian_indikator_kegiatan_renstra,g.tolok_ukur_indikator,g.angka_tahun4 as target_output,h.tahun_4 as thn_rkpd    
                FROM trx_renstra_visi AS a     
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra      
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra    
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra    
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra    
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra    
                INNER JOIN trx_renstra_kegiatan_indikator AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,   
                ref_tahun h  
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'   
                UNION      
                SELECT DISTINCT g.kd_indikator,a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,    
                f.id_kegiatan_renstra,g.uraian_indikator_kegiatan_renstra,g.tolok_ukur_indikator,g.angka_tahun5 as target_output,h.tahun_5 as thn_rkpd    
                FROM trx_renstra_visi AS a     
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra    
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra   
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra   
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra    
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra     
                INNER JOIN trx_renstra_kegiatan_indikator AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,    
                ref_tahun h
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.' ) a  
                INNER JOIN trx_rkpd_renstra b ON b.tahun_rkpd = a.thn_rkpd AND b.id_kegiatan_renstra = a.id_kegiatan_renstra,   
                (Select @id:=max(id_indikator_renstra) from trx_rkpd_renstra_indikator) j ');

            if ($xIndikator != 0 ) {
                $xPelaksana=DB::Insert('INSERT INTO trx_rkpd_renstra_pelaksana(tahun_rkpd,id_rkpd_renstra,id_pelaksana_renstra,id_sub_unit)     
                SELECT DISTINCT b.tahun_rkpd,b.id_rkpd_renstra,(@id:=@id+1) as id_pelaksana_renstra,a.id_sub_unit FROM     
                (SELECT DISTINCT a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,     
                f.id_kegiatan_renstra,g.id_sub_unit,h.tahun_1 as thn_rkpd      
                FROM trx_renstra_visi AS a    
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra     
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra       
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra     
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra    
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra    
                INNER JOIN trx_renstra_kegiatan_pelaksana AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,ref_tahun h                   
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'      
                UNION        
                SELECT DISTINCT a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,     
                f.id_kegiatan_renstra,g.id_sub_unit,h.tahun_2 as thn_rkpd        
                FROM trx_renstra_visi AS a         
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra     
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra      
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra     
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra     
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra     
                INNER JOIN trx_renstra_kegiatan_pelaksana AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,ref_tahun h    
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'    
                UNION      
                SELECT DISTINCT a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,     
                f.id_kegiatan_renstra,g.id_sub_unit,h.tahun_3 as thn_rkpd       
                FROM trx_renstra_visi AS a         
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra        
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra        
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra     
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra    
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra     
                INNER JOIN trx_renstra_kegiatan_pelaksana AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,ref_tahun h    
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'      
                UNION           
                SELECT DISTINCT a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,     
                f.id_kegiatan_renstra,g.id_sub_unit,h.tahun_4 as thn_rkpd        
                FROM trx_renstra_visi AS a       
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra      
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra       
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra    
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra    
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra      
                INNER JOIN trx_renstra_kegiatan_pelaksana AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,ref_tahun h    
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.'    
                UNION     
                SELECT DISTINCT a.id_unit,a.id_visi_renstra,b.id_misi_renstra,c.id_tujuan_renstra,d.id_sasaran_renstra,e.id_program_renstra,     
                f.id_kegiatan_renstra,g.id_sub_unit,h.tahun_5 as thn_rkpd      
                FROM trx_renstra_visi AS a   
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra      
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra       
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra     
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra      
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra      
                INNER JOIN trx_renstra_kegiatan_pelaksana AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra,ref_tahun h  
                WHERE e.id_program_rpjmd='.$req->id_program_rpjmd.' )a     
                INNER JOIN trx_rkpd_renstra b ON b.tahun_rkpd = a.thn_rkpd AND b.id_kegiatan_renstra = a.id_kegiatan_renstra,    
                (Select @id:=max(id_pelaksana_renstra) from trx_rkpd_renstra_pelaksana) j');

                if ($xPelaksana != 0 ) {
                        return response ()->json (['pesan'=>'Re-Pivot Renstra Berhasil','status_pesan'=>'1']);
                    } else {
                        return response ()->json (['pesan'=>'Gagal melakukan Re-Pivot Pelaksana Renstra','status_pesan'=>'0']);
                    }
            } else {
              return response ()->json (['pesan'=>'Gagal melakukan Re-Pivot Indikator Renstra','status_pesan'=>'0']);
            }
      } else {
        return response ()->json (['pesan'=>'Gagal melakukan Re-Pivot Renstra','status_pesan'=>'0']);
      }
    }

    public function getUnitPelaksana($id_program_rpjmd,$id_bidang)
    {
      $rpjmdpelaksana = DB::Select('SELECT DISTINCT f.id_unit, f.id_bidang, f.kd_unit, f.nm_unit
                        FROM trx_renstra_visi AS a
                        INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                        INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
                        INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
                        INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                        INNER JOIN ref_unit AS f ON a.id_unit = f.id_unit
                        INNER JOIN ref_program AS g ON e.id_program_ref = g.id_program
                        WHERE e.id_program_rpjmd = '.$id_program_rpjmd.' AND g.id_bidang = '.$id_bidang.' ORDER BY f.id_bidang ASC');

      return DataTables::of($rpjmdpelaksana)
            ->addColumn('action', function ($rpjmdpelaksana) {
            return '<a class="add-unitpelaksana btn btn-success btn-labeled"><span class="btn-label"><i class="fa fa-plus fa-fw fa-lg"></i></span>Tambahkan</a>';})
            ->make(true);
    }
    
    public function getCheckSum2()
    {
    	$tahun=2017;
    	$sumtahun=substr($tahun,0,1)+substr($tahun,1,1)+substr($tahun,2,1)+substr($tahun,3,1);
    	$tahap=2;
    	$no_urut=1;
    	$pagu=15098324900.89;    	
    	$nilai=$sumtahun.substr($pagu,2,1).$no_urut.substr($pagu,1,1).$tahap.substr($pagu,-1,1);
    	return $nilai;
    }

    public function getCheckSum($tahun,$tahap,$no_urut,$pagu)
    {    	
    	$sumtahun=substr($tahun,0,1)+substr($tahun,1,1)+substr($tahun,2,1)+substr($tahun,3,1);    	
    	$nilai=$sumtahun.substr($pagu,2,1).$no_urut.substr($pagu,1,1).$tahap.substr($pagu,-1,1);
    	return $nilai;
    }

    public function getUrusan($id_program_rpjmd){
      $urusan=DB::select('SELECT DISTINCT d.kd_urusan, d.nm_urusan
            FROM trx_renstra_program AS a
            INNER JOIN ref_program AS b ON a.id_program_ref = b.id_program
            INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang
            INNER JOIN ref_urusan AS d ON c.kd_urusan = d.kd_urusan
            WHERE a.id_program_rpjmd='.$id_program_rpjmd);

        return json_encode($urusan);
    }

    public function getBidang($id_urusan){
        $bidang=DB::select('SELECT DISTINCT c.* FROM trx_renstra_program AS a
            INNER JOIN ref_program AS b ON a.id_program_ref = b.id_program
            INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang 
            WHERE c.kd_urusan='.$id_urusan); 

        return json_encode($bidang);
    }

    public function checkVisi($id)
    {       
        $cek = DB::SELECT('SELECT id_status_dokumen FROM trx_rpjmd_dokumen WHERE id_rpjmd ='.$id);
        return $cek;
    }

    public function checkMisi($id)
    {       
        $cek = DB::SELECT('SELECT a.id_status_dokumen FROM trx_rpjmd_dokumen AS a
                INNER JOIN trx_rpjmd_visi AS b ON a.id_rpjmd = b.id_rpjmd
                WHERE b.id_visi_rpjmd ='.$id);
        return $cek;
    }

    public function checkTujuan($id)
    {       
        $cek = DB::SELECT('SELECT a.id_status_dokumen FROM trx_rpjmd_dokumen AS a
                INNER JOIN trx_rpjmd_visi AS b ON a.id_rpjmd = b.id_rpjmd
                INNER JOIN trx_rpjmd_misi AS c ON b.id_visi_rpjmd = c.id_visi_rpjmd
                WHERE c.id_misi_rpjmd ='.$id);
        return $cek;
    }

    public function checkSasaran($id)
    {       
        $cek = DB::SELECT('SELECT a.id_status_dokumen FROM trx_rpjmd_dokumen AS a
                INNER JOIN trx_rpjmd_visi AS b ON a.id_rpjmd = b.id_rpjmd
                INNER JOIN trx_rpjmd_misi AS c ON b.id_visi_rpjmd = c.id_visi_rpjmd
                INNER JOIN trx_rpjmd_tujuan AS d ON c.id_misi_rpjmd = d.id_misi_rpjmd
                WHERE d.id_tujuan_rpjmd ='.$id);
        return $cek;
    }

    public function checkProgram($id)
    {       
        $cek = DB::SELECT('SELECT a.id_status_dokumen FROM trx_rpjmd_dokumen AS a
                INNER JOIN trx_rpjmd_visi AS b ON a.id_rpjmd = b.id_rpjmd
                INNER JOIN trx_rpjmd_misi AS c ON b.id_visi_rpjmd = c.id_visi_rpjmd
                INNER JOIN trx_rpjmd_tujuan AS d ON c.id_misi_rpjmd = d.id_misi_rpjmd
                INNER JOIN trx_rpjmd_sasaran AS e ON d.id_tujuan_rpjmd = e.id_tujuan_rpjmd 
                WHERE e.id_sasaran_rpjmd ='.$id);
        return $cek;
    }

    public function checkUrusan($id)
    {       
        $cek = DB::SELECT('SELECT a.id_status_dokumen FROM trx_rpjmd_dokumen AS a
                INNER JOIN trx_rpjmd_visi AS b ON a.id_rpjmd = b.id_rpjmd
                INNER JOIN trx_rpjmd_misi AS c ON b.id_visi_rpjmd = c.id_visi_rpjmd
                INNER JOIN trx_rpjmd_tujuan AS d ON c.id_misi_rpjmd = d.id_misi_rpjmd
                INNER JOIN trx_rpjmd_sasaran AS e ON d.id_tujuan_rpjmd = e.id_tujuan_rpjmd 
                INNER JOIN trx_rpjmd_program AS f ON e.id_sasaran_rpjmd = f.id_sasaran_rpjmd 
                WHERE f.id_program_rpjmd ='.$id);
        return $cek;
    }

    public function checkPelaksana($id)
    {       
        $cek = DB::SELECT('SELECT a.id_status_dokumen FROM trx_rpjmd_dokumen AS a
                INNER JOIN trx_rpjmd_visi AS b ON a.id_rpjmd = b.id_rpjmd
                INNER JOIN trx_rpjmd_misi AS c ON b.id_visi_rpjmd = c.id_visi_rpjmd
                INNER JOIN trx_rpjmd_tujuan AS d ON c.id_misi_rpjmd = d.id_misi_rpjmd
                INNER JOIN trx_rpjmd_sasaran AS e ON d.id_tujuan_rpjmd = e.id_tujuan_rpjmd 
                INNER JOIN trx_rpjmd_program AS f ON e.id_sasaran_rpjmd = f.id_sasaran_rpjmd 
                INNER JOIN trx_rpjmd_program_urusan AS g ON f.id_urbid_rpjmd = g.id_urbid_rpjmd 
                WHERE g.id_urbid_rpjmd ='.$id);
        return $cek;
    }
    

}
