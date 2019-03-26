<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use DB;
use Response;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
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

      // $dataperdarpjmd=TrxRpjmdDokumen::select('id_rpjmd','thn_dasar','tahun_1','tahun_2','tahun_3','tahun_4','tahun_5','no_perda','tgl_perda','id_revisi','id_status_dokumen')
      //     ->where('id_status_dokumen','=','1')
      //     ->get();

      $dataperdarpjmd=DB::SELECT('SELECT a.id_rpjmd, a.thn_dasar, a.tahun_1, a.tahun_2, a.tahun_3, a.tahun_4,
              a.tahun_5, a.no_perda, a.tgl_perda, a.id_revisi, a.id_status_dokumen as id_status,
              CASE a.id_status_dokumen
                   WHEN 0 THEN "DRAFT"
                   WHEN 1 THEN "RPJMD Final"
                   WHEN 2 THEN "RPJMD Revisi 1"
              END AS id_status_dokumen 
              FROM trx_rpjmd_dokumen AS a WHERE a.id_status_dokumen = 1');

      return view('rpjmd.index')->with(compact('dataperdarpjmd'));

    }

    public function getVisiRPJMD()
    {
      $rpjmdvisi = TrxRpjmdVisi::select('id_visi_rpjmd','thn_id','id_rpjmd','uraian_visi_rpjmd','id_perubahan')
            ->orderby('id_rpjmd','asc')
            ->get();

      return DataTables::of($rpjmdvisi)
        ->addColumn('action', function ($rpjmdvisi) {
            return '<button class="view-rpjmdmisi btn btn-sm btn-warning" data-id_visi="'.$rpjmdvisi->id_visi_rpjmd.'"><i class="glyphicon glyphicon-eye-open"></i></button>';})
        ->make(true);
    }

    public function getMisiRPJMD($id_visi_rpjmd)
    {
      $rpjmdmisi = TrxRpjmdMisi::select('thn_id_rpjmd','no_urut','id_visi_rpjmd','id_misi_rpjmd','id_perubahan','uraian_misi_rpjmd')
            ->where('id_visi_rpjmd','=',$id_visi_rpjmd)
            ->orderby('no_urut','desc')
            ->get();

      return DataTables::of($rpjmdmisi)
        ->addColumn('action', function ($rpjmdmisi) {
            return '<button class="view-rpjmdtujuan btn btn-sm btn-warning" data-id_misi="'.$rpjmdmisi->id_misi_rpjmd.'"><i class="glyphicon glyphicon-eye-open"></i></button>';})
        ->make(true);
    }

    public function getTujuanRPJMD($id_misi_rpjmd)
    {
      $rpjmdtujuan = TrxRpjmdTujuan::select('thn_id_rpjmd','no_urut','id_tujuan_rpjmd','id_misi_rpjmd','id_perubahan','uraian_tujuan_rpjmd')
            ->where('id_misi_rpjmd','=',$id_misi_rpjmd)
            ->orderby('no_urut','desc')
            ->get();

      return DataTables::of($rpjmdtujuan)
        ->addColumn('id_visi_rpjmd',function($rpjmdtujuan){
            return $rpjmdtujuan->trx_rpjmd_misi->id_visi_rpjmd;
          })
        ->addColumn('id_misi',function($rpjmdtujuan){
            return $rpjmdtujuan->trx_rpjmd_misi->no_urut;
          })
        ->addColumn('action', function ($rpjmdtujuan) {
            return '<button class="view-rpjmdsasaran btn btn-sm btn-warning" data-id_tujuan="'.$rpjmdtujuan->id_tujuan_rpjmd.'"><i class="glyphicon glyphicon-eye-open"></i></button>';})
        ->make(true);
    }

    public function getSasaranRPJMD($id_tujuan_rpjmd)
    {
      $rpjmdsasaran = TrxRpjmdSasaran::select('thn_id_rpjmd','no_urut','id_tujuan_rpjmd','id_sasaran_rpjmd','id_perubahan','uraian_sasaran_rpjmd')
            ->where('id_tujuan_rpjmd','=',$id_tujuan_rpjmd)
            ->orderby('no_urut','desc')
            ->get();

      return DataTables::of($rpjmdsasaran)
        ->addColumn('id_visi_rpjmd',function($rpjmdsasaran){
            return $rpjmdsasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->id_visi_rpjmd;
          })
        ->addColumn('id_misi',function($rpjmdsasaran){
            return $rpjmdsasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->no_urut;
          })
        ->addColumn('id_tujuan',function($rpjmdsasaran){
              return $rpjmdsasaran->trx_rpjmd_tujuan->no_urut;
          })
        ->addColumn('action', function ($rpjmdsasaran) {
            return '<button class="view-rpjmdprogram btn btn-sm btn-warning" data-id_sasaran="'.$rpjmdsasaran->id_sasaran_rpjmd.'"><i class="glyphicon glyphicon-eye-open"></i></button> <button class="view-rpjmdkebijakan btn btn-sm btn-info" data-id_sasaran="'.$rpjmdsasaran->id_sasaran_rpjmd.'"><i class="glyphicon glyphicon-play-circle"></i></button> <button class="view-rpjmdstrategi btn btn-sm btn-success" data-id_sasaran="'.$rpjmdsasaran->id_sasaran_rpjmd.'"><i class="glyphicon glyphicon-play-circle"></i></button>';})
        ->make(true);
    }

    public function getKebijakanRPJMD($id_sasaran_rpjmd)
    {
      $rpjmdkebijakan = TrxRpjmdKebijakan::select('thn_id','no_urut','id_sasaran_rpjmd','id_kebijakan_rpjmd','id_perubahan','uraian_kebijakan_rpjmd')
            ->where('id_sasaran_rpjmd','=',$id_sasaran_rpjmd)
            ->orderby('no_urut','desc')
            ->get();

      return DataTables::of($rpjmdkebijakan)
        ->addColumn('id_visi_rpjmd',function($rpjmdkebijakan){
            return $rpjmdkebijakan->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->id_visi_rpjmd;
          })
        ->addColumn('id_misi',function($rpjmdkebijakan){
            return $rpjmdkebijakan->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->no_urut;
          })
        ->addColumn('id_tujuan',function($rpjmdkebijakan){
              return $rpjmdkebijakan->trx_rpjmd_sasaran->trx_rpjmd_tujuan->no_urut;
          })
        ->addColumn('id_sasaran',function($rpjmdkebijakan){
              return $rpjmdkebijakan->trx_rpjmd_sasaran->no_urut;
          })
        ->make(true);
    }

    public function getStrategiRPJMD($id_sasaran_rpjmd)
    {
      $rpjmdstrategi = TrxRpjmdstrategi::select('thn_id','no_urut','id_sasaran_rpjmd','id_strategi_rpjmd','id_perubahan','uraian_strategi_rpjmd')
            ->where('id_sasaran_rpjmd','=',$id_sasaran_rpjmd)
            ->orderby('no_urut','desc')
            ->get();

      return DataTables::of($rpjmdstrategi)
        ->addColumn('id_visi_rpjmd',function($rpjmdstrategi){
            return $rpjmdstrategi->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->id_visi_rpjmd;
          })
        ->addColumn('id_misi',function($rpjmdstrategi){
            return $rpjmdstrategi->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->no_urut;
          })
        ->addColumn('id_tujuan',function($rpjmdstrategi){
              return $rpjmdstrategi->trx_rpjmd_sasaran->trx_rpjmd_tujuan->no_urut;
          })
        ->addColumn('id_sasaran',function($rpjmdstrategi){
              return $rpjmdstrategi->trx_rpjmd_sasaran->no_urut;
          })
        ->make(true);
    }

    public function getProgramRPJMD($id_sasaran_rpjmd)
    {

      $rpjmdprogram = DB::Select('SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) as kd_sasaran,e.id_program_rpjmd, '
            .'e.no_urut,e.uraian_program_rpjmd,(e.pagu_tahun1/1000000) as pagu_tahun1,(e.pagu_tahun2/1000000) as pagu_tahun2, '
            .'(e.pagu_tahun3/1000000) as pagu_tahun3,(e.pagu_tahun4/1000000) as pagu_tahun4,(e.pagu_tahun5/1000000) as pagu_tahun5,(e.total_pagu/1000000) as total_pagu '
            .'FROM trx_rpjmd_visi AS a '
            .'INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd '
            .'INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd '
            .'INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd '
            .'INNER JOIN trx_rpjmd_program AS e ON e.id_sasaran_rpjmd = d.id_sasaran_rpjmd '
            .'WHERE d.id_sasaran_rpjmd = '.$id_sasaran_rpjmd);

      return DataTables::of($rpjmdprogram)
            ->addColumn('action', function ($rpjmdprogram) {
                return '<button class="view-rpjmdindikator btn btn-sm btn-warning" data-id_program="'.$rpjmdprogram->id_program_rpjmd.'"><i class="glyphicon glyphicon-eye-open"></i></button> <button class="view-rpjmdurusan btn btn-sm btn-info" data-id_program="'.$rpjmdprogram->id_program_rpjmd.'"><i class="glyphicon glyphicon-play-circle"></i></button>';})
            ->make(true);
    }

    public function getIndikatorProgramRPJMD($id_program_rpjmd)
    {
      $rpjmdindikator = TrxRpjmdProgramIndikator::select('thn_id','no_urut','id_program_rpjmd','id_indikator_program_rpjmd','id_perubahan','id_indikator','uraian_indikator_program_rpjmd',
      'tolok_ukur_indikator','angka_awal_periode','angka_tahun1','angka_tahun2','angka_tahun3','angka_tahun4','angka_tahun5','angka_akhir_periode')
            ->where('id_program_rpjmd','=',$id_program_rpjmd)
            ->orderby('no_urut','desc')
            ->get();

      return DataTables::of($rpjmdindikator)
            ->addColumn('kd_program', function ($rpjmdindikator) { return
                $rpjmdindikator->trx_rpjmd_program->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->id_visi_rpjmd.".".$rpjmdindikator->trx_rpjmd_program->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->no_urut.".".$rpjmdindikator->trx_rpjmd_program->trx_rpjmd_sasaran->trx_rpjmd_tujuan->no_urut.".".$rpjmdindikator->trx_rpjmd_program->trx_rpjmd_sasaran->no_urut.".".$rpjmdindikator->trx_rpjmd_program->no_urut;
              })
            ->make(true);
    }

    public function getUrusanProgramRPJMD($id_program_rpjmd)
    {
      $rpjmdurusan = TrxRpjmdProgramUrusan::select('thn_id','no_urut','id_urbid_rpjmd','id_program_rpjmd','id_bidang')
            ->where('id_program_rpjmd','=',$id_program_rpjmd)
            ->orderby('id_bidang','desc')
            ->get();

      return DataTables::of($rpjmdurusan)
            ->addColumn('kd_program', function ($rpjmdurusan) { return
                $rpjmdurusan->trx_rpjmd_program->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->id_visi_rpjmd.".".$rpjmdurusan->trx_rpjmd_program->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->no_urut.".".$rpjmdurusan->trx_rpjmd_program->trx_rpjmd_sasaran->trx_rpjmd_tujuan->no_urut.".".$rpjmdurusan->trx_rpjmd_program->trx_rpjmd_sasaran->no_urut.".".$rpjmdurusan->trx_rpjmd_program->no_urut;
              })
            ->addColumn('kode_bid', function ($rpjmdurusan) { return
                $rpjmdurusan->ref_bidang->kd_urusan.".".$rpjmdurusan->ref_bidang->kd_bidang;
              })
            ->addColumn('nm_bidang',function($rpjmdurusan){
                return $rpjmdurusan->ref_bidang->nm_bidang;
              })
            ->addColumn('nm_urusan',function($rpjmdurusan){
                return $rpjmdurusan->ref_bidang->ref_urusan->nm_urusan;
              })
            ->addColumn('action', function ($rpjmdurusan) {
                return '<button class="view-rpjmdpelaksana btn btn-sm btn-warning" data-id_urusan="'.$rpjmdurusan->id_urbid_rpjmd.'"><i class="glyphicon glyphicon-eye-open"></i></button>';
              })
            ->make(true);
    }

    public function getPelaksanaProgramRPJMD($id_urbid_rpjmd)
    {
      $rpjmdpelaksana = TrxRpjmdProgramPelaksana::select('thn_id','no_urut','id_urbid_rpjmd','id_pelaksana_rpjmd','id_unit','id_perubahan','pagu_tahun1','pagu_tahun2','pagu_tahun3','pagu_tahun4','pagu_tahun5')
            ->where('id_urbid_rpjmd','=',$id_urbid_rpjmd)
            ->orderby('id_unit','desc')
            ->get();

      return DataTables::of($rpjmdpelaksana)
            ->addColumn('kd_program', function ($rpjmdpelaksana) { return
                $rpjmdpelaksana->trx_rpjmd_program_urusan->trx_rpjmd_program->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->id_visi_rpjmd.".".$rpjmdpelaksana->trx_rpjmd_program_urusan->trx_rpjmd_program->trx_rpjmd_sasaran->trx_rpjmd_tujuan->trx_rpjmd_misi->no_urut.".".$rpjmdpelaksana->trx_rpjmd_program_urusan->trx_rpjmd_program->trx_rpjmd_sasaran->trx_rpjmd_tujuan->no_urut.".".$rpjmdpelaksana->trx_rpjmd_program_urusan->trx_rpjmd_program->trx_rpjmd_sasaran->no_urut.".".$rpjmdpelaksana->trx_rpjmd_program_urusan->trx_rpjmd_program->no_urut;
              })
            ->addColumn('nm_unit',function($rpjmdpelaksana){
                return $rpjmdpelaksana->ref_unit->nm_unit;
              })
            ->addColumn('kd_unit',function($rpjmdpelaksana){
                return $rpjmdpelaksana->ref_unit->ref_bidang->kd_urusan.".".$rpjmdpelaksana->ref_unit->ref_bidang->kd_bidang.".".$rpjmdpelaksana->ref_unit->kd_unit;
              })
            ->make(true);
    }

}
