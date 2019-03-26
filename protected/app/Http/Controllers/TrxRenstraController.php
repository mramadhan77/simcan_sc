<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Auth;
use App\Fungsi as Fungsi;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\RefPemda;
use App\Models\RefUnit;
use App\Models\RefIndikator;
use App\Models\RefUrusan;
use App\Models\RefBidang;
use App\Models\TrxRpjmdDokumen;
use App\Models\Can\TrxRenstraVisi;
use App\Models\Can\TrxRenstraMisi;
use App\Models\Can\TrxRenstraTujuan;
use App\Models\Can\TrxRenstraSasaran;
use App\Models\Can\TrxRenstraKebijakan;
use App\Models\Can\TrxRenstraStrategi;
use App\Models\Can\TrxRenstraProgram;
use App\Models\Can\TrxRenstraProgramIndikator;
use App\Models\Can\TrxRpjmdProgramUrusan;
use App\Models\Can\TrxRpjmdProgramPelaksana;
use App\Models\Can\TrxRenstraKegiatan;
use App\Models\Can\TrxRenstraKegiatanIndikator;
use App\Models\Can\TrxRenstraKegiatanPelaksana;

class TrxRenstraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
      $dataunit=RefUnit::select('id_unit','id_bidang','kd_unit','nm_unit')
          ->get();
      $dataperda=TrxRpjmdDokumen::select('id_rpjmd','thn_dasar','tahun_1','tahun_2','tahun_3','tahun_4','tahun_5','no_perda','tgl_perda','id_revisi','id_status_dokumen')
          ->where('id_status_dokumen','=','1')
          ->get();

      return view('renstra.index')->with(compact('dataunit','dataperda'));
    }

    public function getVisiRenstra($id_unit)
    {

          $visirenstra=TrxRenstraVisi::select('SELECT a.thn_id, a.no_urut, a.id_renstra, a.id_visi_renstra, a.id_unit, a.id_perubahan, a.thn_awal_renstra,
            a.thn_akhir_renstra, a.uraian_visi_renstra, a.id_status_dokumen, a.sumber_data, a.created_at, a.updated_at
            FROM trx_renstra_visi AS a
            WHERE a.id_unit='.$id_unit);

      return DataTables::of($visirenstra)
          ->addColumn('action', function ($visirenstra) {
              return 
              '<div class="btn-group">
                  <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                  <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                      <a class="btnDetailVisi dropdown-item"><i class="fa fa-paper-plane-o fa-fw fa-lg text-info"></i> Detail Visi</a>
                    </li>
                  </ul>
                </div>'
              ;})
          ->make(true);
    }

    public function getMisiRenstra($id_visi_renstra)
    {
        $misirenstra=DB::select('SELECT a.thn_id, a.no_urut, a.id_visi_renstra, a.id_misi_renstra, a.id_perubahan, a.uraian_misi_renstra, 
            a.sumber_data, a.created_at, a.updated_at
            FROM trx_renstra_misi AS a
            WHERE a.id_visi_renstra='.$id_visi_renstra);

        return DataTables::of($misirenstra)
          ->addColumn('action', function ($misirenstra) {
              return 
              // '<button class="view-renstratujuan btn btn-sm btn-warning" title="Lihat Tujuan" data-id_misi="'.$misirenstra->id_misi_renstra.'"><i class="glyphicon glyphicon-eye-open"></i></button>'
              '<div class="btn-group">
                  <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                  <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                      <a class="view-renstratujuan dropdown-item" data-id_misi="'.$misirenstra->id_misi_renstra.'"><i class="fa fa-location-arrow fa-fw fa-lg text-success"></i> Lihat Data Tujuan</a>
                    </li>
                  </ul>
                </div>'
              ;})
        ->make(true);

    }

    public function getKebijakanRenstra($id_sasaran_renstra)
    {
        
        $kebijakanrenstra=DB::select('SELECT a.thn_id,a.no_urut,a.id_sasaran_renstra,a.id_kebijakan_renstra,a.id_perubahan,a.uraian_kebijakan_renstra,a.sumber_data,a.created_at,a.update_at,
            CONCAT(e.no_urut,".",d.no_urut,".",c.no_urut,".",b.no_urut) AS kd_sasaran
            FROM trx_renstra_kebijakan AS a
            INNER JOIN trx_renstra_sasaran AS b ON a.id_sasaran_renstra = b.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan AS c ON b.id_tujuan_renstra = c.id_tujuan_renstra
            INNER JOIN trx_renstra_misi AS d ON c.id_misi_renstra = d.id_misi_renstra
            INNER JOIN trx_renstra_visi AS e ON d.id_visi_renstra = e.id_visi_renstra
            WHERE a.id_sasaran_renstra='.$id_sasaran_renstra);

      return DataTables::of($kebijakanrenstra)
        //   ->addColumn('kd_sasaran', function ($kebijakanrenstra) { return
        //       $kebijakanrenstra->trx_renstra_sasaran->trx_renstra_tujuan->trx_renstra_misi->trx_renstra_visi->no_urut.".".$kebijakanrenstra->trx_renstra_sasaran->trx_renstra_tujuan->trx_renstra_misi->no_urut.".".$kebijakanrenstra->trx_renstra_sasaran->trx_renstra_tujuan->no_urut.".".$kebijakanrenstra->trx_renstra_sasaran->no_urut;})
          ->make(true);

    }
    public function getStrategiRenstra($id_sasaran_renstra)
    {

        $strategirenstra=DB::select('SELECT a.thn_id, a.no_urut, a.id_sasaran_renstra, a.id_strategi_renstra, a.id_perubahan, a.uraian_strategi_renstra, a.sumber_data, a.created_at, a.updated_at,
            CONCAT(e.no_urut,".",d.no_urut,".",c.no_urut,".",b.no_urut) AS kd_sasaran
            FROM trx_renstra_strategi AS a
            INNER JOIN trx_renstra_sasaran AS b ON a.id_sasaran_renstra = b.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan AS c ON b.id_tujuan_renstra = c.id_tujuan_renstra
            INNER JOIN trx_renstra_misi AS d ON c.id_misi_renstra = d.id_misi_renstra
            INNER JOIN trx_renstra_visi AS e ON d.id_visi_renstra = e.id_visi_renstra
            WHERE a.id_sasaran_renstra='.$id_sasaran_renstra);

      return DataTables::of($strategirenstra)
        //   ->addColumn('kd_sasaran', function ($strategirenstra) 
        //     { return
        //       $strategirenstra->trx_renstra_sasaran->trx_renstra_tujuan->trx_renstra_misi->trx_renstra_visi->no_urut.".".$strategirenstra->trx_renstra_sasaran->trx_renstra_tujuan->trx_renstra_misi->no_urut.".".$strategirenstra->trx_renstra_sasaran->trx_renstra_tujuan->no_urut.".".$strategirenstra->trx_renstra_sasaran->no_urut;})
          ->make(true);

    }
    
    public function getKegiatanPelaksana($id_kegiatan_renstra)
    {
      $kegiatanpelaksana=DB::select('SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut,".",e.no_urut,".",f.no_urut) AS kd_kegiatan, '
                .'CONCAT(i.kd_unit,".",h.kd_sub) AS kd_sub,g.no_urut,h.nm_sub,h.id_sub_unit,g.id_kegiatan_renstra_pelaksana FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                INNER JOIN trx_renstra_kegiatan AS f ON f.id_program_renstra = e.id_program_renstra
                INNER JOIN trx_renstra_kegiatan_pelaksana AS g ON g.id_kegiatan_renstra = f.id_kegiatan_renstra
                INNER JOIN ref_sub_unit AS h ON g.id_sub_unit = h.id_sub_unit
                INNER JOIN ref_unit AS i ON h.id_unit = i.id_unit
                WHERE f.id_kegiatan_renstra='.$id_kegiatan_renstra .' ORDER BY kd_kegiatan, kd_sub');

      return DataTables::of($kegiatanpelaksana)
      ->addColumn('action', function ($kegiatanpelaksana) {
          return '<div class="btn-group">
            	<button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                </button>
            		<ul class="dropdown-menu dropdown-menu-right">
            			<li>
            				<a class="edit-pelaksana-kegiatan dropdown-item" data-id_kegiatan_renstra_pelaksana="'.$kegiatanpelaksana->id_kegiatan_renstra_pelaksana.'" data-id_sub_unit="'.$kegiatanpelaksana->id_sub_unit.'" data-nm_sub_unit="'.$kegiatanpelaksana->nm_sub.'" ><i class="fa fa-pencil fa-fw fa-lg text-success"></i></i> Detail Data Pelaksana Kegiatan</a>
					    </li>
            		</ul>
            </div>';})
          ->make(true);
    }
    
    public function getSubUnit($id_sub_unit)
    {
        $subunit=DB::select('select id_sub_unit,nm_sub from ref_sub_unit
            where id_sub_unit <>'.$id_sub_unit);
            
        return json_encode($subunit);
    }
}
