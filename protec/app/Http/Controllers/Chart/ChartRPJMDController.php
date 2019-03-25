<?php

namespace App\Http\Controllers\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Response;
use Session;
use Auth;
use Khill\Lavacharts\Lavacharts;
use App\Models\TrxRpjmdVisi;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\TrxRpjmdProgram;
use PhpParser\Node\Stmt\Foreach_;

class ChartRPJMDController extends Controller {
	public function misi5tahun_view() {
		$viewer = DB::table ( 'trx_rpjmd_program' )
		->select ( DB::raw ( "trx_rpjmd_misi.uraian_misi_rpjmd,sum(trx_rpjmd_program.total_pagu) as count" ) )
		->join ( 'trx_rpjmd_sasaran', 'trx_rpjmd_program.id_sasaran_rpjmd', '=', 'trx_rpjmd_sasaran.id_sasaran_rpjmd' )
		->join ( 'trx_rpjmd_tujuan', 'trx_rpjmd_sasaran.id_tujuan_rpjmd', '=', 'trx_rpjmd_tujuan.id_tujuan_rpjmd' )
		->join ( 'trx_rpjmd_misi', 'trx_rpjmd_tujuan.id_misi_rpjmd', '=', 'trx_rpjmd_misi.id_misi_rpjmd' )
		->GroupBy ( 'trx_rpjmd_misi.uraian_misi_rpjmd' )
		->get ();
		
		return $viewer;
	}

	public function misi1tahun_view() {
		$data = DB::table ( 'trx_rpjmd_program' )
		->select ( DB::raw ( 'trx_rpjmd_misi.uraian_misi_rpjmd,
							  sum(trx_rpjmd_program.pagu_tahun1)/1000000 as pagu_tahun1,
							  sum(trx_rpjmd_program.pagu_tahun2)/1000000 as pagu_tahun2,
							  sum(trx_rpjmd_program.pagu_tahun3)/1000000 as pagu_tahun3,
							  sum(trx_rpjmd_program.pagu_tahun4)/1000000 as pagu_tahun4,
							  sum(trx_rpjmd_program.pagu_tahun5)/1000000 as pagu_tahun5, 
							  trx_rpjmd_dokumen.tahun_1, 
							  trx_rpjmd_dokumen.tahun_2, 
							  trx_rpjmd_dokumen.tahun_3, 
							  trx_rpjmd_dokumen.tahun_4, 
							  trx_rpjmd_dokumen.tahun_5' ) )
		->join ( 'trx_rpjmd_sasaran', 'trx_rpjmd_program.id_sasaran_rpjmd', '=', 'trx_rpjmd_sasaran.id_sasaran_rpjmd' )
		->join ( 'trx_rpjmd_tujuan', 'trx_rpjmd_sasaran.id_tujuan_rpjmd', '=', 'trx_rpjmd_tujuan.id_tujuan_rpjmd' )
		->join ( 'trx_rpjmd_misi', 'trx_rpjmd_tujuan.id_misi_rpjmd', '=', 'trx_rpjmd_misi.id_misi_rpjmd' )
		->join ( 'trx_rpjmd_visi', 'trx_rpjmd_misi.id_visi_rpjmd', '=', 'trx_rpjmd_visi.id_visi_rpjmd' )
		->join ( 'trx_rpjmd_dokumen', 'trx_rpjmd_visi.id_rpjmd', '=', 'trx_rpjmd_dokumen.id_rpjmd' )
		->where ( 'trx_rpjmd_misi.no_urut', '<>', 99 )
		->where ( 'trx_rpjmd_misi.no_urut', '<>', 98 )
		->GroupBy ( 'trx_rpjmd_misi.uraian_misi_rpjmd', 'trx_rpjmd_dokumen.tahun_1', 'trx_rpjmd_dokumen.tahun_2', 'trx_rpjmd_dokumen.tahun_3', 'trx_rpjmd_dokumen.tahun_4', 'trx_rpjmd_dokumen.tahun_5' )
		->get ();
		return $data;
	}

	public function urusan5tahun_view() {
	$data = DB::table ( 'trx_renstra_kegiatan' )->select ( DB::raw ( 'ref_urusan.nm_urusan,
			(sum(trx_renstra_kegiatan.pagu_tahun1)+
			sum(trx_renstra_kegiatan.pagu_tahun2)+
			sum(trx_renstra_kegiatan.pagu_tahun3)+
			sum(trx_renstra_kegiatan.pagu_tahun4)+
			sum(trx_renstra_kegiatan.pagu_tahun5)) as total_pagu' ) )
			->join ( 'trx_renstra_kegiatan_pelaksana', 'trx_renstra_kegiatan.id_kegiatan_renstra', '=', 'trx_renstra_kegiatan_pelaksana.id_kegiatan_renstra' )
			->join ( 'ref_sub_unit', 'trx_renstra_kegiatan_pelaksana.id_sub_unit', '=', 'ref_sub_unit.id_sub_unit' )
			->join ( 'ref_unit', 'ref_sub_unit.id_unit', '=', 'ref_unit.id_unit' )
			->join ( 'ref_bidang', 'ref_unit.id_bidang', '=', 'ref_bidang.id_bidang' )
			->join ( 'ref_urusan', 'ref_bidang.kd_urusan', '=', 'ref_urusan.kd_urusan' )
			->GroupBy ( 'ref_urusan.nm_urusan' )
			->get ();
			
			return $data;
	}

	public function urusan1_view() {
	$data = DB::table ( 'trx_renstra_kegiatan' )->select ( DB::raw ( 'ref_bidang.nm_bidang,
	(sum(trx_renstra_kegiatan.pagu_tahun1)+sum(trx_renstra_kegiatan.pagu_tahun2)+sum(trx_renstra_kegiatan.pagu_tahun3)+
	sum(trx_renstra_kegiatan.pagu_tahun4)+sum(trx_renstra_kegiatan.pagu_tahun5)) as total_pagu' ) )
	->join ( 'trx_renstra_kegiatan_pelaksana', 'trx_renstra_kegiatan.id_kegiatan_renstra', '=', 'trx_renstra_kegiatan_pelaksana.id_kegiatan_renstra' )
	->join ( 'ref_sub_unit', 'trx_renstra_kegiatan_pelaksana.id_sub_unit', '=', 'ref_sub_unit.id_sub_unit' )
	->join ( 'ref_unit', 'ref_sub_unit.id_unit', '=', 'ref_unit.id_unit' )
	->join ( 'ref_bidang', 'ref_unit.id_bidang', '=', 'ref_bidang.id_bidang' )
	->join ( 'ref_urusan', 'ref_bidang.kd_urusan', '=', 'ref_urusan.kd_urusan' )
	->where ( 'ref_urusan.kd_urusan', '=', '1' )
	->GroupBy ( 'ref_bidang.nm_bidang' )
	->get ();
	return $data;
	}

	public function urusan2_view() {
		$data = DB::table ( 'trx_renstra_kegiatan' )->select ( DB::raw ( 'ref_bidang.nm_bidang,
	(sum(trx_renstra_kegiatan.pagu_tahun1)+sum(trx_renstra_kegiatan.pagu_tahun2)+sum(trx_renstra_kegiatan.pagu_tahun3)+
	sum(trx_renstra_kegiatan.pagu_tahun4)+sum(trx_renstra_kegiatan.pagu_tahun5)) as total_pagu' ) )
	->join ( 'trx_renstra_kegiatan_pelaksana', 'trx_renstra_kegiatan.id_kegiatan_renstra', '=', 'trx_renstra_kegiatan_pelaksana.id_kegiatan_renstra' )
	->join ( 'ref_sub_unit', 'trx_renstra_kegiatan_pelaksana.id_sub_unit', '=', 'ref_sub_unit.id_sub_unit' )
	->join ( 'ref_unit', 'ref_sub_unit.id_unit', '=', 'ref_unit.id_unit' )
	->join ( 'ref_bidang', 'ref_unit.id_bidang', '=', 'ref_bidang.id_bidang' )
	->join ( 'ref_urusan', 'ref_bidang.kd_urusan', '=', 'ref_urusan.kd_urusan' )
	->where ( 'ref_urusan.kd_urusan', '=', '2' )
	->GroupBy ( 'ref_bidang.nm_bidang' )
	->get ();
	return $data;
	}

	public function urusan3_view() {
		$data = DB::table ( 'trx_renstra_kegiatan' )->select ( DB::raw ( 'ref_bidang.nm_bidang,
	(sum(trx_renstra_kegiatan.pagu_tahun1)+sum(trx_renstra_kegiatan.pagu_tahun2)+sum(trx_renstra_kegiatan.pagu_tahun3)+
	sum(trx_renstra_kegiatan.pagu_tahun4)+sum(trx_renstra_kegiatan.pagu_tahun5)) as total_pagu' ) )
	->join ( 'trx_renstra_kegiatan_pelaksana', 'trx_renstra_kegiatan.id_kegiatan_renstra', '=', 'trx_renstra_kegiatan_pelaksana.id_kegiatan_renstra' )
	->join ( 'ref_sub_unit', 'trx_renstra_kegiatan_pelaksana.id_sub_unit', '=', 'ref_sub_unit.id_sub_unit' )
	->join ( 'ref_unit', 'ref_sub_unit.id_unit', '=', 'ref_unit.id_unit' )
	->join ( 'ref_bidang', 'ref_unit.id_bidang', '=', 'ref_bidang.id_bidang' )
	->join ( 'ref_urusan', 'ref_bidang.kd_urusan', '=', 'ref_urusan.kd_urusan' )
	->where ( 'ref_urusan.kd_urusan', '=', '3' )
	->GroupBy ( 'ref_bidang.nm_bidang' )
	->get ();
	return $data;
	}

	public function urusan4_view() {
		$data = DB::table ( 'trx_renstra_kegiatan' )->select ( DB::raw ( 'ref_bidang.nm_bidang,
	(sum(trx_renstra_kegiatan.pagu_tahun1)+sum(trx_renstra_kegiatan.pagu_tahun2)+sum(trx_renstra_kegiatan.pagu_tahun3)+
	sum(trx_renstra_kegiatan.pagu_tahun4)+sum(trx_renstra_kegiatan.pagu_tahun5)) as total_pagu' ) )
	->join ( 'trx_renstra_kegiatan_pelaksana', 'trx_renstra_kegiatan.id_kegiatan_renstra', '=', 'trx_renstra_kegiatan_pelaksana.id_kegiatan_renstra' )
	->join ( 'ref_sub_unit', 'trx_renstra_kegiatan_pelaksana.id_sub_unit', '=', 'ref_sub_unit.id_sub_unit' )
	->join ( 'ref_unit', 'ref_sub_unit.id_unit', '=', 'ref_unit.id_unit' )
	->join ( 'ref_bidang', 'ref_unit.id_bidang', '=', 'ref_bidang.id_bidang' )
	->join ( 'ref_urusan', 'ref_bidang.kd_urusan', '=', 'ref_urusan.kd_urusan' )
	->where ( 'ref_urusan.kd_urusan', '=', '4' )
	->GroupBy ( 'ref_bidang.nm_bidang' )
	->get ();
	return $data;
	}

	public function bidang5tahun_view() {
	$data = DB::table ( 'trx_renstra_kegiatan' )
	->select ( DB::raw ( 'ref_bidang.nm_bidang,
	(sum(trx_renstra_kegiatan.pagu_tahun1)+
	sum(trx_renstra_kegiatan.pagu_tahun2)+
	sum(trx_renstra_kegiatan.pagu_tahun3)+
	sum(trx_renstra_kegiatan.pagu_tahun4)+
	sum(trx_renstra_kegiatan.pagu_tahun5)) as total_pagu' ) )
	->join ( 'trx_renstra_kegiatan_pelaksana', 'trx_renstra_kegiatan.id_kegiatan_renstra', '=', 'trx_renstra_kegiatan_pelaksana.id_kegiatan_renstra' )
	->join ( 'ref_sub_unit', 'trx_renstra_kegiatan_pelaksana.id_sub_unit', '=', 'ref_sub_unit.id_sub_unit' )
	->join ( 'ref_unit', 'ref_sub_unit.id_unit', '=', 'ref_unit.id_unit' )
	->join ( 'ref_bidang', 'ref_unit.id_bidang', '=', 'ref_bidang.id_bidang' )
	->join ( 'ref_urusan', 'ref_bidang.kd_urusan', '=', 'ref_urusan.kd_urusan' )
	->GroupBy ( 'ref_bidang.nm_bidang' )
	->OrderBy ( 'total_pagu', 'Desc' )
	->get ();
	return $data;
	}

	public function chartjs() {
		if(Auth::check()){ 
			return view ( 'umum.dash_rpjmd' );
		} else {
			return view ( 'errors.401' );
		}
	}
}


/*
routes
--------------------------------------------------------------
Route::get('/dash','Chart\ChartRPJMDController@ChartRPJMD');
--------------------------------------------------------------------------------


*/