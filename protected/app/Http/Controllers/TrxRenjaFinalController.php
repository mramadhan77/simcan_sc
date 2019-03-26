<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\CekAkses;
use App\Models\TrxRenjaRancanganProgram;
use App\Models\TrxRenjaRancanganProgramIndikator;
use App\Models\TrxRenjaRancangan;
use App\Models\TrxRenjaRancanganIndikator;
use App\Models\TrxRenjaRancanganPelaksana;
use App\Models\TrxRenjaRancanganAktivitas;
use App\Models\TrxRenjaRancanganLokasi;
use App\Models\TrxRenjaRancanganBelanja;
use Auth;



class TrxRenjaFinalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getData()
    {
        // $dataForum = TrxForumSkpd::paginate(15);
    }

    public function loadData()
    {
        
        $unit=DB::table('ref_unit')->get();

        return view('renjafinal.load')->with(compact('unit'));
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
        $dataRekap=DB::select('SELECT a.tahun_renja, COALESCE(a.pagu_0,0) as pagu_0, COALESCE(b.pagu_1,0) as pagu_1, 
                    COALESCE(c.pagu_2,0) as pagu_2 FROM
                    (SELECT  a.tahun_renja,  COALESCE(SUM(COALESCE(b.pagu_tahun_kegiatan,0)/1000000),0) as pagu_0
                    FROM trx_renja_rancangan_program AS a
                    LEFT OUTER JOIN trx_renja_rancangan AS b ON b.id_renja_program = a.id_renja_program
                    WHERE a.jenis_belanja = 0
                    GROUP BY a.tahun_renja) a
                    LEFT OUTER JOIN
                    (SELECT  a.tahun_renja,  COALESCE(SUM(COALESCE(b.pagu_tahun_kegiatan,0)/1000000),0) as pagu_1
                    FROM trx_renja_rancangan_program AS a
                    LEFT OUTER JOIN trx_renja_rancangan AS b ON b.id_renja_program = a.id_renja_program
                    WHERE a.jenis_belanja = 1
                    GROUP BY a.tahun_renja) b
                    ON a.tahun_renja = b.tahun_renja
                    LEFT OUTER JOIN
                    (SELECT  a.tahun_renja,  COALESCE(SUM(COALESCE(b.pagu_tahun_kegiatan,0)/1000000),0) as pagu_2
                    FROM trx_renja_rancangan_program AS a
                    LEFT OUTER JOIN trx_renja_rancangan AS b ON b.id_renja_program = a.id_renja_program
                    WHERE a.jenis_belanja = 2
                    GROUP BY a.tahun_renja) c
                    ON a.tahun_renja = b.tahun_renja 
                    WHERE a.tahun_renja = '.Session::get('tahun'));

        return view('renjafinal.dashboard')->with(compact('dataRekap'));
        
    }

    public function dokumen(Request $request, Builder $htmlBuilder)
    {
        return view('renjafinal.doku');
    }

    public function blangsung(Request $request, Builder $htmlBuilder)
    {
        
        return view('renjafinal.blangsung');
    }
}
