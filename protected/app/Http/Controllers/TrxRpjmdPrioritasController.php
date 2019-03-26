<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Fungsi as Fungsi;
use Validator;
use DB;
use Response;
use Session;
use Auth;
use CekAkses;

class TrxRpjmdPrioritasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

      $dataperdarpjmd=DB::select('SELECT * FROM trx_rpjmd_prioritas');
      return view('prarpjmd.prioritas')->with(compact('dataperdarpjmd'));

    }
    
    public function getDokumen()
    {

      $dataperdarpjmd=DB::select('SELECT * FROM trx_rpjmd_dokumen WHERE id_status_dokumen=1');
      return json_encode($dataperdarpjmd);

    }

    public function getPrioritas()
    {
      $rpjmdvisi = DB::select('SELECT * FROM trx_rpjmd_prioritas ORDER BY no_urut, urutan_prioritas ASC');

      return DataTables::of($rpjmdvisi)
        ->addColumn('action', function ($rpjmdvisi) {
            return
            '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
            	<ul class="dropdown-menu dropdown-menu-right">
            		<li>
            			<a class="edit-visi dropdown-item" data-id_visi_rpjmd="'.$rpjmdvisi->id_visi_rpjmd.'" data-thn_id="'.$rpjmdvisi->thn_id.'" data-id_rpjmd="'.$rpjmdvisi->id_rpjmd.'" data-id_perubahan="'.$rpjmdvisi->id_perubahan.'"  data-uraian_visi_rpjmd="'.$rpjmdvisi->uraian_visi_rpjmd.'" data-no_urut="'.$rpjmdvisi->no_urut.'"><i class="fa fa-paper-plane-o fa-fw fa-lg text-success"></i> Lihat Data Visi</a>
                    </li>
                    <li>
                        <a class="btnLihatChart dropdown-item" href="'.url('rpjmd/getRpjmdChart/'.$rpjmdvisi->id_rpjmd).'"><i class="fa fa-sitemap fa-fw fa-lg text-primary"></i> Pohon Kinerja RPJMD </a>
                    </li>
            	</ul>
            </div>';
        })
        ->make(true);
    }
    public function editVisi(Request $req)
    {
    	$data = TrxRpjmdVisi::find($req->id_visi_rpjmd_edit);
    	$data->thn_id= $req->thn_id_edit;
    	$data->id_rpjmd= $req->id_rpjmd_edit;
    	$data->uraian_visi_rpjmd= $req->ur_visi_rpjmd_edit;
    	$data->id_perubahan= $req->id_perubahan_edit;
    	$data->no_urut= $req->no_urut_edit;
    	try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }
    public function getMisiRPJMD($id_visi_rpjmd)
    {
      $rpjmdmisi = DB::select('SELECT * FROM trx_rpjmd_misi WHERE id_visi_rpjmd = '.$id_visi_rpjmd.' ORDER BY no_urut desc');

      return DataTables::of($rpjmdmisi)
        ->addColumn('action', function ($rpjmdmisi) {
        	return '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
            	<ul class="dropdown-menu dropdown-menu-right">
            		<li>
            			<a class="edit-misi dropdown-item" data-id_misi_rpjmd_misi="'.$rpjmdmisi->id_misi_rpjmd.'" data-thn_id_misi="'.$rpjmdmisi->thn_id_rpjmd.'" data-id_visi_rpjmd_misi="'.$rpjmdmisi->id_visi_rpjmd.'" data-id_perubahan_misi="'.$rpjmdmisi->id_perubahan.'"  data-uraian_misi_rpjmd_misi="'.$rpjmdmisi->uraian_misi_rpjmd.'" data-no_urut_misi="'.$rpjmdmisi->no_urut.'"><i class="fa fa-location-arrow fa-fw fa-lg text-success"></i> Lihat Data Misi</a>
            		</li>
            	</ul>
            </div>';})
        ->make(true);
    }
    public function editMisi(Request $req)
    {
    	$data = TrxRpjmdMisi::find($req->id_misi_rpjmd_edit);
    	$data->thn_id_rpjmd= $req->thn_id_misi_edit;
    	$data->id_visi_rpjmd= $req->id_visi_rpjmd_misi_edit;
    	$data->uraian_misi_rpjmd= $req->ur_misi_rpjmd_edit;
    	$data->id_perubahan= $req->id_perubahan_misi_edit;
    	$data->no_urut= $req->no_urut_misi_edit;
    	try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }
    public function getTujuanRPJMD($id_misi_rpjmd)
    {
      $rpjmdtujuan = DB::select('SELECT c.thn_id_rpjmd, c.no_urut, c.id_misi_rpjmd, c.id_tujuan_rpjmd, c.id_perubahan, c.uraian_tujuan_rpjmd, 
                        b.id_visi_rpjmd, b.id_misi_rpjmd, b.no_urut as id_misi, COALESCE(d.jml_indikator,0) as jml_indikator
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
            				<a class="edit-tujuan dropdown-item" data-id_tujuan_rpjmd_tujuan="'.$rpjmdtujuan->id_tujuan_rpjmd.'" data-thn_id_tujuan="'.$rpjmdtujuan->thn_id_rpjmd.'" data-id_misi_rpjmd_tujuan="'.$rpjmdtujuan->id_misi_rpjmd.'" data-id_perubahan_tujuan="'.$rpjmdtujuan->id_perubahan.'"  data-uraian_tujuan_rpjmd_tujuan="'.$rpjmdtujuan->uraian_tujuan_rpjmd.'" data-no_urut_tujuan="'.$rpjmdtujuan->no_urut.'"><i class="fa fa-bullseye fa-fw fa-lg text-success"></i></i> Lihat Data Tujuan</a>
                        </li>
                        <li>
                            <a class="add-indikator dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Tujuan</a>
                        </li>
            		</ul>
            </div>';})
        ->make(true);
    }

    public function editTujuan(Request $req)
    {
    	$data = TrxRpjmdTujuan::find($req->id_tujuan_rpjmd_edit);
    	$data->thn_id_rpjmd= $req->thn_id_tujuan_edit;
    	$data->id_misi_rpjmd= $req->id_misi_rpjmd_tujuan_edit;
    	$data->uraian_tujuan_rpjmd= $req->ur_tujuan_rpjmd_edit;
    	$data->id_perubahan= $req->id_perubahan_tujuan_edit;
    	$data->no_urut= $req->no_urut_tujuan_edit;
    	try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }
    public function getSasaranRPJMD($id_tujuan_rpjmd)
    {
      $rpjmdsasaran = DB::select('SELECT d.thn_id_rpjmd, d.no_urut, d.id_tujuan_rpjmd, d.id_sasaran_rpjmd, d.id_perubahan, d.uraian_sasaran_rpjmd, b.id_visi_rpjmd,
                        b.id_misi_rpjmd, b.no_urut as id_misi, c.no_urut as id_tujuan, COALESCE(e.jml_indikator,0) as jml_indikator
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
                            <a class="btnEditSasaran dropdown-item" data-id_sasaran_rpjmd_sasaran="'.$rpjmdsasaran->id_sasaran_rpjmd.'" data-thn_id_sasaran="'.$rpjmdsasaran->thn_id_rpjmd.'" data-id_tujuan_rpjmd_sasaran="'.$rpjmdsasaran->id_tujuan_rpjmd.'" data-id_perubahan_sasaran="'.$rpjmdsasaran->id_perubahan.'"  data-uraian_sasaran_rpjmd_sasaran="'.$rpjmdsasaran->uraian_sasaran_rpjmd.'" data-no_urut_sasaran="'.$rpjmdsasaran->no_urut.'"><i class="fa fa-pencil fa-fw fa-lg text-success"></i> Lihat Data Sasaran</a>
                        </li>
                        <li>
                            <a class="view-rpjmdstrategi dropdown-item" data-id_sasaran="'.$rpjmdsasaran->id_sasaran_rpjmd.'" ><i class="fa fa-map-o fa-fw fa-lg text-info"></i> Lihat Strategi</a>
                        </li>
                        <li>
                            <a class="view-rpjmdkebijakan dropdown-item" data-id_sasaran="'.$rpjmdsasaran->id_sasaran_rpjmd.'" ><i class="fa fa-gavel fa-fw fa-lg text-primary"></i> Lihat Kebijakan</a>
                        </li>
                        <li>
                            <a class="btnAddIndikatorSasaran dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Indikator Sasaran</a>
                        </li>
            		</ul>
            </div> 
					';})
        ->make(true);
    }
    public function editSasaran(Request $req)
    {
    	$data = TrxRpjmdSasaran::find($req->id_sasaran_rpjmd_edit);
    	$data->thn_id_rpjmd= $req->thn_id_sasaran_edit;
    	$data->id_tujuan_rpjmd= $req->id_tujuan_rpjmd_sasaran_edit;
    	$data->uraian_sasaran_rpjmd= $req->ur_sasaran_rpjmd_edit;
    	$data->id_perubahan= $req->id_perubahan_sasaran_edit;
    	$data->no_urut= $req->no_urut_sasaran_edit;
    	try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
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
    public function editKebijakan(Request $req)
    {
    	$data = TrxRpjmdKebijakan::find($req->id_kebijakan_rpjmd_edit);
    	$data->thn_id= $req->thn_id_kebijakan_edit;
    	$data->id_sasaran_rpjmd= $req->id_sasaran_rpjmd_kebijakan_edit;
    	$data->uraian_kebijakan_rpjmd= $req->ur_kebijakan_rpjmd_edit;
    	$data->id_perubahan= $req->id_perubahan_kebijakan_edit;
    	$data->no_urut= $req->no_urut_kebijakan_edit;
    	try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
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
    public function editStrategi(Request $req)
    {
    	$data = TrxRpjmdStrategi::find($req->id_strategi_rpjmd_edit);
    	$data->thn_id= $req->thn_id_strategi_edit;
    	$data->id_sasaran_rpjmd= $req->id_sasaran_rpjmd_strategi_edit;
    	$data->uraian_strategi_rpjmd= $req->ur_strategi_rpjmd_edit;
    	$data->id_perubahan= $req->id_perubahan_strategi_edit;
    	$data->no_urut= $req->no_urut_strategi_edit;
    	try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
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
                            <a class="repivot-renstra dropdown-item"><i class="fa fa-refresh fa-fw fa-lg text-warning"></i> Re-Pivot Renstra</a>
                        </li>
                        <li>
                            <a class="post-urbidprog dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg text-success"></i> Posting Program Urusan</a>
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


    public function editProgram(Request $req)
    {
        $xData=DB::select('SELECT * FROM trx_rpjmd_program WHERE thn_id ='.$req->thn_id_program_edit.' AND id_sasaran_rpjmd = '.$req->id_sasaran_rpjmd_program_edit.' AND no_urut ='.$req->no_urut_program_edit);

    	if ($xData != null){
            $data = TrxRpjmdProgram::find($req->id_program_rpjmd_edit);
        	$data->thn_id= $req->thn_id_program_edit;
        	$data->id_sasaran_rpjmd= $req->id_sasaran_rpjmd_program_edit;
        	$data->uraian_program_rpjmd= $req->ur_program_rpjmd_edit;
        	$data->id_perubahan= $req->id_perubahan_program_edit;
        	$data->pagu_tahun1= $req->pagu1_edit;
        	$data->pagu_tahun2= $req->pagu2_edit;
        	$data->pagu_tahun3= $req->pagu3_edit;
        	$data->pagu_tahun4= $req->pagu4_edit;
        	$data->pagu_tahun5= $req->pagu5_edit;
          $data->total_pagu= $req->pagu_total_edit;
        	$data->no_urut= $req->no_urut_program_edit;
        } else {
            $data = new TrxRpjmdProgram ();
            $data->thn_id= $req->thn_id_program_edit;
            $data->id_sasaran_rpjmd= $req->id_sasaran_rpjmd_program_edit;
            $data->uraian_program_rpjmd= $req->ur_program_rpjmd_edit;
            $data->id_perubahan= $req->id_perubahan_program_edit;
            $data->pagu_tahun1= $req->pagu1_edit;
            $data->pagu_tahun2= $req->pagu2_edit;
            $data->pagu_tahun3= $req->pagu3_edit;
            $data->pagu_tahun4= $req->pagu4_edit;
            $data->pagu_tahun5= $req->pagu5_edit;
            $data->total_pagu= $req->pagu_total_edit;
            $data->no_urut= $req->no_urut_program_edit;
        }

    	try{
            $data->save (['timestamps' => true]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
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

    public function addUrusan(Request $req)
    {
        $xData=DB::select('SELECT * FROM trx_rpjmd_program_urusan WHERE thn_id ='.$req->thn_id.' AND id_program_rpjmd = '.$req->id_program_rpjmd.' AND no_urut ='.$req->no_urut.' AND id_bidang ='.$req->id_bidang);

        if ($xData != null){
            return response ()->json (['pesan'=>'Data Gagal Disimpan (Urusan - Bidang sudah dipakai)','status_pesan'=>'0']);
        } else {
            $data = new TrxRpjmdProgramUrusan ();
            $data->thn_id= $req->thn_id;
            $data->no_urut= $req->no_urut;
            $data->id_program_rpjmd= $req->id_program_rpjmd;
            $data->id_bidang= $req->id_bidang;
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

    public function editUrusan(Request $req)
    {        
      $xData=DB::select('SELECT * FROM trx_rpjmd_program_urusan 
            WHERE thn_id ='.$req->thn_id.' AND id_program_rpjmd = '.$req->id_program_rpjmd.' AND no_urut ='.$req->no_urut.' AND id_bidang ='.$req->id_bidang.' AND id_urbid_rpjmd ='.$req->id_urbid_rpjmd);

      if ($xData != null){
            return response ()->json (['pesan'=>'Data Tetap Sama Tidak Ada Perubahan','status_pesan'=>'0']);
        } else {
          $data = TrxRpjmdProgramUrusan::find($req->id_urbid_rpjmd);;
          $data->thn_id= $req->thn_id;
          $data->no_urut= $req->no_urut;
          $data->id_program_rpjmd= $req->id_program_rpjmd;
          $data->id_bidang= $req->id_bidang;
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

    public function delUrusan(Request $req){

        $xData=DB::select('SELECT * FROM trx_rpjmd_program_urusan WHERE id_urbid_rpjmd ='.$req->id_urbid_rpjmd.' AND no_urut > 50');

        if ($xData != null){
            $data = TrxRpjmdProgramUrusan::where('id_urbid_rpjmd',$req->id_urbid_rpjmd)->delete();
            if($data != 0){
              return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
              return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
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
                                <a class="del-pelaksanaprog dropdown-item"><i class="fa fa-trash fa-fw fa-lg text-danger"></i> Hapus Pelaksana</a>
                            </li>
                        </ul>
                    </div>';})
            ->make(true);
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

    public function addPelaksana(Request $req)
    {
        $xData=DB::select('SELECT * FROM trx_rpjmd_program_pelaksana WHERE thn_id ='.$req->thn_id.' AND id_urbid_rpjmd = '.$req->id_urbid_rpjmd.' AND id_unit ='.$req->id_unit);

        if ($xData != null){
            return response ()->json (['pesan'=>'Data Gagal Disimpan (Unit Pelaksana sudah dipakai)','status_pesan'=>'0']);
        } else {
            $data = new TrxRpjmdProgramPelaksana();
            $data->thn_id= $req->thn_id;
            $data->id_urbid_rpjmd= $req->id_urbid_rpjmd;
            $data->no_urut= $req->no_urut;
            $data->id_unit= $req->id_unit;
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

    public function delPelaksana(Request $req){

        $xData=DB::select('SELECT * FROM trx_rpjmd_program_pelaksana WHERE id_pelaksana_rpjmd ='.$req->id_pelaksana_rpjmd.' AND no_urut > 50');

        if ($xData != null){
            $data = TrxRpjmdProgramPelaksana::where('id_pelaksana_rpjmd',$req->id_pelaksana_rpjmd)->delete();
            if($data != 0){
              return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
              return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus (Data Hasil Import Aplikasi 5 tahunan)','status_pesan'=>'0']);
        }       
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
    

}
