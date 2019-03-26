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
use Auth;

class TrxRenjaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function createLog($tahun, $batchCat, $blamableCat, $blamable_id){
        $model = new \App\TrxBatchProcess();
        $model->tahun = $tahun;
        $model->batchCat = $batchCat;
        $model->blamable_cat = $blamableCat;
        $model->blamable_id = $blamable_id;
        $model->user_id = Auth::user()->id;
        if($model->save()) {
            $model->save();
        }else{
            throw new Exception("Load gagal pada data.(log $batchCat)");
        }
    }


    protected function insertRkpdRenstraPelaksana($tahun, $unit_id){
        // query goes here
        $rkpdRenstraPelaksana = DB::statement("
            INSERT INTO trx_rkpd_renstra_pelaksana
                (tahun_rkpd,
                id_rkpd_renstra,
                id_sub_unit,
                pagu_tahun,
                target_output)        
            SELECT
            a.tahun_rkpd,
            a.id_rkpd_renstra,
            b.id_sub_unit,
            a.pagu_tahun_kegiatan,
            a.target_tahun_indikator
            FROM
            trx_rkpd_renstra a
            INNER JOIN trx_renstra_kegiatan_pelaksana b ON b.id_kegiatan_renstra = a.id_kegiatan_renstra
            WHERE a.tahun_rkpd = :tahun AND a.id_unit = :kd_unit      
        ",[ 
        ':tahun' => $tahun,
        ':kd_unit' => $unit_id
        ]);

        if(!$rkpdRenstraPelaksana) throw new Exception('Load gagal pada data RKPD-Renstra.');    
    }

    protected function insertRkpdRenstra($tahun, $unit_id){
        // SET @id_tahun = (SELECT id_rpjmd FROM trx_rpjmd_dokumen WHERE tahun_1 = @p_tahun OR tahun_2 = @p_tahun OR tahun_3 = @p_tahun OR tahun_4 = @p_tahun OR tahun_5 LIMIT 0,1);
        $id_tahun = \App\TrxRpjmdDokumen::select('id_rpjmd')
                    ->where('tahun_1', $tahun)
                    ->orWhere('tahun_2', $tahun)
                    ->orWhere('tahun_3', $tahun)
                    ->orWhere('tahun_4', $tahun)
                    ->orWhere('tahun_5', $tahun)
                    ->first()->id_rpjmd;
        // query goes here
        $rkpdRenstra = DB::statement("
            INSERT INTO trx_rkpd_renstra
                        (tahun_rkpd,
                        id_rkpd_rpjmd,
                        id_program_rpjmd,
                        pagu_tahun_rpjmd,
                        id_unit,
                        id_visi_renstra,
                        uraian_visi_renstra,
                        id_misi_renstra,
                        uraian_misi_renstra,
                        id_tujuan_renstra,
                        uraian_tujuan_renstra,
                        id_sasaran_renstra,
                        uraian_sasaran_renstra,
                        id_program_renstra,
                        uraian_program_renstra,
                        id_kegiatan_renstra,
                        uraian_kegiatan_renstra,
                        kd_indikator,
                        uraian_indikator,
                        tolak_ukur_indikator,
                        target_tahun_indikator,
                        pagu_tahun_kegiatan,
                        sumber_data)        
            SELECT
            h.tahun_rkpd,
            h.id_rkpd_rpjmd,
            h.id_program_rpjmd,
            h.pagu_program_rpjmd,
            g.id_unit,
            g.id_visi_renstra,
            g.uraian_visi_renstra,
            f.id_misi_renstra,
            f.uraian_misi_renstra,
            e.id_tujuan_renstra,
            e.uraian_tujuan_renstra,
            d.id_sasaran_renstra,
            d.uraian_sasaran_renstra,
            c.id_program_renstra,
            c.uraian_program_renstra,
            a.id_kegiatan_renstra,
            a.uraian_program_renstra AS uraian_kegiatan_renstra,
            b.kd_indikator,
            b.uraian_indikator_kegiatan_renstra,
            b.tolok_ukur_indikator,
            b.angka_tahun1,
            a.pagu_tahun1 AS pagu_tahun_kegiatan,
            0 AS sumber_data
            FROM 
            trx_renstra_kegiatan a 
            LEFT JOIN trx_renstra_kegiatan_indikator b ON a.id_kegiatan_renstra = b.id_kegiatan_renstra
            LEFT JOIN trx_renstra_program c ON a.id_program_renstra = c.id_program_renstra
            LEFT JOIN trx_renstra_sasaran d ON c.id_sasaran_renstra = d.id_sasaran_renstra
            LEFT JOIN trx_renstra_tujuan e ON d.id_tujuan_renstra = e.id_tujuan_renstra
            LEFT JOIN trx_renstra_misi f ON e.id_misi_renstra = f.id_misi_renstra
            LEFT JOIN trx_renstra_visi g ON f.id_visi_renstra = g.id_visi_renstra
            LEFT JOIN trx_rkpd_rpjmd_ranwal h ON c.id_program_rpjmd = h.id_program_rpjmd
            WHERE a.thn_id = :tahun AND g.id_unit = :kd_unit        
        ",[ 
        ':tahun' => $id_tahun,
        ':kd_unit' => $unit_id
        ]);

        if(!$rkpdRenstra) throw new Exception('Load gagal pada data RKPD-Renstra.');

        $this->insertRkpdRenstraPelaksana($tahun, $unit_id);
        $this->createLog($tahun, 5011, 1, $unit_id);
        return true;
    }

    protected function insertRenjaRancanganKebijakan($tahun, $unit_id){
        // query goes here
        $renjaRancanganKebijakan = DB::statement("
            INSERT INTO trx_renja_rancangan_kebijakan
                (tahun_renja,
                no_urut,
                id_renja,
                id_unit,
                id_sasaran_renstra,
                uraian_kebijakan)        
            SELECT
            a.tahun_renja,
            a.no_urut,
            a.id_renja,
            a.id_unit,
            c.id_sasaran_renstra,
            c.uraian_kebijakan_renstra
            FROM
            trx_renja_rancangan AS a
            INNER JOIN trx_rkpd_renstra AS b ON a.id_rkpd_renstra = b.id_rkpd_renstra
            INNER JOIN trx_renstra_kebijakan AS c ON c.id_sasaran_renstra = b.id_sasaran_renstra AND c.id_sasaran_renstra = a.id_unit
            WHERE a.tahun_rkpd = :tahun AND a.id_unit = :kd_unit
        ",[ 
        ':tahun' => $tahun,
        ':kd_unit' => $unit_id
        ]);

        if(!$renjaRancanganKebijakan) throw new Exception('Load gagal pada data Rancangan Renja. (Kebijakan)');    
    }

    protected function insertRenjaRancangan($tahun, $unit_id){
        // query goes here
        $renjaRancangan = DB::statement("
            INSERT INTO trx_renja_rancangan
                (tahun_renja,
                no_urut,
                id_rkpd_renstra,
                id_rkpd_ranwal,
                id_program_rpjmd,
                pagu_tahun_ranwal,
                id_unit,
                id_visi_renstra,
                uraian_visi_renstra,
                id_misi_renstra,
                uraian_misi_renstra,
                id_tujuan_renstra,
                uraian_tujuan_renstra,
                id_sasaran_renstra,
                uraian_sasaran_renstra,
                id_program_renstra,
                uraian_program_renstra,
                id_kegiatan_renstra,
                uraian_kegiatan_renstra,
                kd_indikator,
                uraian_indikator,
                tolak_ukur_indikator,
                target_tahun_indikator,
                pagu_tahun_kegiatan,
                pagu_musren,
                status_musren)       
            SELECT
            a.tahun_rkpd,
            a.id_rkpd_renstra AS no_urut,
            a.id_rkpd_renstra,
            b.id_rkpd_ranwal,
            a.id_program_rpjmd,
            b.pagu_program_rpjmd,
            a.id_unit,
            a.id_visi_renstra,
            a.uraian_visi_renstra,
            a.id_misi_renstra,
            a.uraian_misi_renstra,
            a.id_tujuan_renstra,
            a.uraian_tujuan_renstra,
            a.id_sasaran_renstra,
            a.uraian_sasaran_renstra,
            a.id_program_renstra,
            a.uraian_program_renstra,
            a.id_kegiatan_renstra,
            a.uraian_kegiatan_renstra,
            a.kd_indikator,
            a.uraian_indikator,
            a.tolak_ukur_indikator,
            a.target_tahun_indikator,
            a.pagu_tahun_kegiatan,
            NULL as pagu_musren,
            0 AS status_musren
            FROM
            trx_rkpd_renstra AS a
            INNER JOIN trx_rkpd_ranwal AS b ON b.id_rkpd_rpjmd = a.id_rkpd_rpjmd
            WHERE a.tahun_rkpd = :tahun AND a.id_unit = :kd_unit      
        ",[ 
        ':tahun' => $tahun,
        ':kd_unit' => $unit_id
        ]);

        if(!$renjaRancangan) throw new Exception('Load gagal pada data Rancangan Renja.');
        $this->insertRenjaRancanganKebijakan($tahun, $unit_id);
        $this->createLog($tahun, 5012, 1, $unit_id);        
    }

    public function loadData(Request $request, Builder $htmlBuilder, $id = null)
    {
        if($request->isMethod('post')){
            try{
                if($this->insertRkpdRenstra(2016, $id) == true) $this->insertRenjaRancangan(2016, $id);
            }catch( Exception $e ){
                echo 'Terjadi Kesalahan, ', $e->getMessage(), "\n";
            }finally{
                return redirect('renja/loadData/');                
            }
        }

        $unit = \App\RefSubUnit::select();
        if(isset(Auth::user()->getUserSubUnit)){
            foreach(Auth::user()->getUserSubUnit as $data){
                $unit->orWhere(['id_unit' => $data->kd_unit, 'kd_sub' => $data->kd_sub]);                
            }
        }
        $unit = $unit->get();

        if($request->ajax()){
            return Datatables::of($unit)
                ->addColumn('action', function ($unit) {
                    $data = $unit;
                    return 
                    '                                    
                    <form method="POST" action="'.url('/renja/loadData/'.$unit->id_sub_unit).'"  onsubmit="return confirm(\'Do you really want to submit the form?\');">
                        <button class="btn btn-xs btn-default" id="submit" type="submit">
                        <i class="glyphicon glyphicon-download bg-white"></i>
                        </button>
                    </form>';
                })            
                ->make(true);
        }

        return view('renja.load')->with(compact('unit'));
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

        return view('renja.dashboard')->with(compact('dataRekap'));
    }

    public function belanjalangsung(Request $request, Builder $htmlBuilder)
    {
                
        $dataunit=DB::select('SELECT id_unit,id_bidang,kd_unit,nm_unit FROM ref_unit');
        $tahunrkpd=DB::select('SELECT tahun_rkpd from trx_rkpd_ranwal group by tahun_rkpd');
        // $refaktivitasasb=DB::select('SELECT b.id_aktivitas_asb,c.nm_aktivitas_asb
        //             FROM trx_perhitungan_asb AS a
        //             INNER JOIN trx_perhitungan_aktivitas AS b ON b.id_perhitungan = a.id_perhitungan
        //             INNER JOIN trx_asb_aktivitas AS c ON b.id_aktivitas_asb = c.id_aktivitas_asb
        //             INNER JOIN trx_asb_sub_kelompok AS d ON c.id_asb_sub_kelompok = d.id_asb_sub_kelompok
        //             INNER JOIN trx_asb_kelompok AS e ON d.id_asb_sub_kelompok = e.id_asb_kelompok
        //             INNER JOIN trx_asb_perkada AS f ON e.id_asb_perkada = f.id_asb_perkada
        //             WHERE f.flag=1
        //             GROUP BY b.id_aktivitas_asb,c.nm_aktivitas_asb');

        // return view('renja.index')->with(compact('dataunit','tahunrkpd','refaktivitasasb'));
        return view('renja.index')->with(compact('dataunit','tahunrkpd'));
    }


    /**
    * This is ajax table
    * This ajax table load unit table
    **/
    public function getUnit(Request $request){
        $unit = \App\RefUnit::select();
        if(isset(Auth::user()->getUserSubUnit)){
            foreach(Auth::user()->getUserSubUnit as $data){
                $unit->orWhere(['id_unit' => $data->kd_unit]);                
            }
        }
        $unit = $unit->get();
        if($request->ajax()){
            return Datatables::of()->make(true);
        }
    }

    /**
    * --hoaaah
    * Populate belanja tidak langsung
    * Show existing year and existing user satker
    */
    protected function getUserSub(){
        /* value references
        * $this->userSub return an array
        * $this->count return int
        */
        $userSub = Auth::user()->getUserSubUnit()->get();
        $countUserSub = count($userSub);
        return (object)['userSub' => $userSub, 'count' => $countUserSub];
    }

    protected function getUserVisi(){
        /* count User sub first
        * if user sub not exist, pupolate all
        * if exist, populate only with user sub criteria
        */
        $user = $this->getUserSub();

        $renstraVisi = \App\TrxRenstraVisi::select();
        if($user->count != 0){
            foreach ($user->userSub as $data) {
                // criteria for renstra visi based on user sub
                $renstraVisi->orWhere(['id_unit' => $data->kd_unit]);
            }
        }
        $renstraVisi = $renstraVisi->get();
        return $renstraVisi;
    }

    protected function getUserMisi($no_urut){
        /* count user sub first
        * getUserVisi. If user visi = 0  && usersub != 0 populate nothing
        * if user sub not exist, populate all
        * 
        */
        $user = $this->getUserSub();
        $visi = $this->getUserVisi();
        // this line use to check if visi are 0
        $renstraMisi = [];
        if($user->count !=0){
            if(count($visi) != 0){
                $renstraMisi = \App\TrxRenstraMisi::select();
                foreach($visi as $visi){
                    // $renstraMisi->orWhere(['id_visi_renstra' => $visi->id_visi_renstra, 'no_urut' => $no_urut]);
                    // codes above didn't work like Yii2. It result or-(or) query, not or-(and) as expected. (I think it changes after update composer. Latest L5.4 rollback to previous behavior) Already create issue in laravel-github to change orWhere behavior
                    // temporary we use raw instead. Please don't use following line with user input condition.
                    $renstraMisi->orWhereRaw("(id_visi_renstra = $visi->id_visi_renstra AND no_urut = $no_urut)");
                }
                $renstraMisi = $renstraMisi->get();
            }
        }else{
            $renstraMisi = \App\TrxRenstraMisi::where(['no_urut' => $no_urut])->get();
        }
        return $renstraMisi;
    }

    protected function getUserRenjaRancangan($no_urut){
        $sesiTahun = Session::get('tahun');
        if(isset($sesiTahun) && $sesiTahun != NULL){
            $tahun = Session::get('tahun') ;
         }else{
             $tahun = date('Y');
        }
        $user = $this->getUserSub();
        $misi = $this->getUserMisi($no_urut);

        if(count($misi) != 0 )
        {    
            $renjaRancangan = \App\TrxRenjaRancangan::select();
            foreach($misi as $misi){
                // $renjaRancangan->orWhere(['id_misi_renstra' => $misi->id_misi_renstra]);
                $renjaRancangan->orWhereRaw("id_misi_renstra = $misi->id_misi_renstra AND tahun_renja = $tahun");
            }
            $renjaRancangan = $renjaRancangan->get();
        }else{
            $renjaRancangan = \App\TrxRenjaRancangan::select()->where(['tahun_renja' => $tahun, 'no_urut' => 99999])->get();
        }        
        return $renjaRancangan;
    }

    public function pdt(Request $request, Builder $htmlBuilder)
    {
        $title = ['Pendapatan', 'pdt'];
        $renjaRancangan = $this->getUserRenjaRancangan(98);

        if($request->ajax()){
            return Datatables::of($renjaRancangan)
                ->addColumn('action', function ($renjaRancangan) use($title) {
                    $data = $renjaRancangan;
                    return 
                    // '<a class="btn btn-default btn-xs" data-href="'.url('/ranwalrkpd/btl/'.$data->id_renja.'/ubah').'" data-toggle="modal" data-target="#myModal" data-title="Sesuaikan Program #'.$data->uraian_kegiatan_renstra.'"><i class="glyphicon glyphicon-pencil bg-white"></i> Ubah</a>'.
                    '<a id="rincian-'.$data->id_renja.'" class="btn btn-default btn-xs" data-href="'.url('/renja/'.$title[1].'/'.$data->id_renja.'/pelaksana').'" ><i class="glyphicon glyphicon-menu-right bg-white"></i> Rincian</a>';
                })
                ->make(true);
        }
        
        return view('renja.nbl')->with(compact('trxforumskpd', 'rpjmdMisi', 'title'));
    }

    public function pdttambah(Request $request, Builder $htmlBuilder)
    {
        $title = ['Pendapatan', 'pdt'];
        $rpjmdMisi = \App\TrxRpjmdMisi::where('no_urut', 98)->first();
        $rpjmdDokumen = \App\TrxRpjmdDokumen::where('id_rpjmd', $rpjmdMisi->thn_id_rpjmd)->orderBy('id_rpjmd', 'desc')->first();
        $rpjmdVisi = \App\TrxRpjmdVisi::where('id_visi_rpjmd', $rpjmdMisi->id_visi_rpjmd)->first();
        $rpjmdTujuan = \App\TrxRpjmdTujuan::where('id_misi_rpjmd', $rpjmdMisi->id_misi_rpjmd)->first();
        $rpjmdSasaran = \App\TrxRpjmdSasaran::where('id_tujuan_rpjmd', $rpjmdTujuan->id_tujuan_rpjmd)->first();
        $rek3Dropdown = \App\RefRek3::where('kd_rek_1', 4)->get();

        IF($request->all()){
            $data = $request->all();
            $rkpd = new \App\TrxRkpdRanwal;
            $rkpd->no_urut = $data['no_urut'];
            // $rkpd->id_rkpd_rpjmd = $rpjmdDokumen['id_rpjmd'];
            $rkpd->tahun_rkpd = $data['tahun_rkpd'];
            $rkpd->thn_id_rpjmd = $rpjmdDokumen['id_rpjmd'];
            $rkpd->id_visi_rpjmd = $rpjmdMisi['id_visi_rpjmd'];
            $rkpd->uraian_visi_rpjmd = $rpjmdVisi['uraian_visi_rpjmd'];
            $rkpd->id_misi_rpjmd = $rpjmdMisi['id_misi_rpjmd'];
            $rkpd->uraian_misi_rpjmd = $rpjmdMisi['uraian_misi_rpjmd'];
            $rkpd->id_tujuan_rpjmd = $rpjmdTujuan['id_tujuan_rpjmd'];
            $rkpd->uraian_tujuan_rpjmd = $rpjmdTujuan['uraian_tujuan_rpjmd'];
            $rkpd->id_sasaran_rpjmd = $rpjmdSasaran['id_sasaran_rpjmd'];
            $rkpd->uraian_sasaran_rpjmd = $rpjmdSasaran['uraian_sasaran_rpjmd'];
            $rkpd->id_program_rpjmd = $data['id_program_rpjmd'];
            $rkpd->uraian_program_rpjmd = $data['uraian_program_rpjmd'];
            $rkpd->pagu_program_rpjmd = $data['pagu_program_rpjmd'];
            $rkpd->status_data = 2;
            // if($rkpd->save()) return response()->json(1);
            if($rkpd->save()) return redirect('ranwalrkpd/pdt/');
        }

        return view('ranwalrkpd._formrkpd')->with(compact('title', 'rpjmdMisi', 'rpjmdDokumen', 'rek3Dropdown'))->render();
    }

    public function pdtubah(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rpjmdMisi = \App\TrxRenstraMisi::where('no_urut', 98)->first();
        $model = \App\TrxRenjaRancangan::find($id);

        IF($request->all()){
            $rkpd = $model;
            $data = $request->all();
            $rkpd->no_urut = $data['no_urut'];
            $rkpd->tahun_renja = $data['tahun_rkpd'];
            $rkpd->uraian_kegiatan_renja = $data['uraian_program_rpjmd'];
            $rkpd->pagu_program_rpjmd = $data['pagu_program_rpjmd'];
            if($rkpd->status_data != 2) $rkpd->status_data = 1;
            // if($rkpd->save()) return response()->json(1);
            if($rkpd->save()) return redirect('ranwalrkpd/pdt/');
        }

        return view('ranwalrkpd._formrkpd')->with(compact('title', 'rpjmdMisi', 'rpjmdDokumen', 'model', 'rek3Dropdown'))->render();
    }    

    public function pdtpelaksana(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rkpd = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $pelaksana = \App\TrxRenjaRancanganPelaksana::where('id_renja', $id)->get();
        $indikator = \App\TrxRenjaRancanganIndikator::where('id_renja', $id)->get();
        $kebijakan = \App\TrxRenjaRancanganKebijakan::where('id_renja', $id)->get();        
        
        return view('renja.pelaksana')->with(compact('pelaksana', 'rkpd', 'indikator', 'kebijakan', 'title'))->render();
    }

    public function pdtpelaksanabelanja(Request $request, Builder $htmlBuilder, $id, $sub_unit_id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rkpd = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $pelaksana = \App\TrxRenjaRancanganPelaksana::where('id_pelaksana_renja', $sub_unit_id)->first();
        $belanja = \App\TrxRenjaRancanganBelanja::where('id_pelaksana_renja', $sub_unit_id)->get();
        $lokasi = \App\TrxRenjaRancanganLokasi::where('id_pelaksana_renja', $sub_unit_id)->get();
        
        return view('renja.belanja')->with(compact('pelaksana', 'rkpd', 'belanja', 'title', 'lokasi'))->render();
    }

    public function pdtpelaksanabelanjatambah(Request $request, $id, $sub_unit_id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rkpd = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $pelaksana = \App\TrxRenjaRancanganPelaksana::where('id_pelaksana_renja', $sub_unit_id)->first();
        $renja = \App\TrxRenjaRancangan::where('id_renja', $id)->first();

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('belanja', $referrer);
        $referrer = $referrer[0].'belanja';  

        // generate dropdown rekening ssh
        $sshRekeningAll = \App\Models\RefRek5::where(['kd_rek_1' => 4])->get();
        $sshRekeningDropdown = NULL;
        foreach ($sshRekeningAll as $sshAll) {
            $sshRekeningDropdown[$sshAll->id_rekening] = $sshAll->id_rekening.'.'.$sshAll->nama_kd_rek_5;
            // $sshRekeningDropdown[$sshAll->id_rekening_ssh] = $sshAll->id_rekening_ssh.'.'.$sshAll->rekening->nama_kd_rek_5;
        }
        // generate dropdown satuan
        $satuanAll = \App\Models\RefSatuan::get();
        $satuanDropdown = NULL;
        foreach ($satuanAll as $satuan) {
            $satuanDropdown[$satuan->id_satuan] = $satuan->id_satuan.'. '.$satuan->uraian_satuan;
        }
        
        IF($request->all()){
            $data = $request->all();
            $belanja = new \App\TrxRenjaRancanganBelanja;
            // generate uraian_belanja from input
            $uraian_belanja = \App\Models\RefSshRekening::where(['id_rekening_ssh' => $data['id_rekening_ssh']])->first()['rekening']['nama_kd_rek_5'];
            $belanja->no_urut = $data['no_urut'];
            $belanja->tahun_renja = $renja->tahun_renja;
            $belanja->id_pelaksana_renja = $sub_unit_id;
            $belanja->id_rekening_ssh = $data['id_rekening_ssh'];
            $belanja->uraian_belanja = $uraian_belanja; //ambil dari refrek5
            $belanja->volume_belanja = $data['volume_belanja'];
            $belanja->id_satuan = $data['id_satuan'];
            $belanja->koefisien = $data['koefisien'];
            $belanja->harga_satuan = $data['harga_satuan'];
            $belanja->jml_belanja = $data['harga_satuan'] * $data['volume_belanja'];
            if($belanja->save()) return redirect('renja/pdt/');
        }

        return view('renja._formrenjabelanja')->with(compact('title', 'renja', 'sshRekeningDropdown', 'satuanDropdown', 'referrer'))->render();
    }

    public function pdtpelaksanabelanjaubah(Request $request, $id, $sub_unit_id, $belanja_id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rkpd = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $pelaksana = \App\TrxRenjaRancanganPelaksana::where('id_pelaksana_renja', $sub_unit_id)->first();
        $renja = \App\TrxRenjaRancangan::where('id_renja', $id)->first();

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('belanja', $referrer);
        $referrer = $referrer[0].'belanja';   

        // generate dropdown rekening ssh
        $sshRekeningAll = \App\Models\RefRek5::where(['kd_rek_1' => 4])->get();
        $sshRekeningDropdown = NULL;
        foreach ($sshRekeningAll as $sshAll) {
            $sshRekeningDropdown[$sshAll->id_rekening] = $sshAll->id_rekening.'.'.$sshAll->nama_kd_rek_5;
            // $sshRekeningDropdown[$sshAll->id_rekening_ssh] = $sshAll->id_rekening_ssh.'.'.$sshAll->rekening->nama_kd_rek_5;
        }
        // generate dropdown satuan
        $satuanAll = \App\Models\RefSatuan::get();
        $satuanDropdown = NULL;
        foreach ($satuanAll as $satuan) {
            $satuanDropdown[$satuan->id_satuan] = $satuan->id_satuan.'. '.$satuan->uraian_satuan;
        }
        $model = \App\TrxRenjaRancanganBelanja::where(['id_belanja_renja' => $belanja_id]);
        
        IF($request->all()){
            $data = $request->all();
            $belanja = $model;
            // generate uraian_belanja from input
            $uraian_belanja = \App\Models\RefSshRekening::where(['id_rekening_ssh' => $data['id_rekening_ssh']])->first()['rekening']['nama_kd_rek_5'];
            $belanja->no_urut = $data['no_urut'];
            $belanja->tahun_renja = $renja->tahun_renja;
            $belanja->id_pelaksana_renja = $sub_unit_id;
            $belanja->id_rekening_ssh = $data['id_rekening_ssh'];
            $belanja->uraian_belanja = $uraian_belanja; //ambil dari refrek5
            $belanja->volume_belanja = $data['volume_belanja'];
            $belanja->id_satuan = $data['id_satuan'];
            $belanja->koefisien = $data['koefisien'];
            $belanja->harga_satuan = $data['harga_satuan'];
            $belanja->jml_belanja = $data['harga_satuan'] * $data['volume_belanja'];
            if($belanja->save()) return redirect('renja/pdt/');
        }

        return view('renja._formrenjabelanja')->with(compact('title', 'renja', 'sshRekeningDropdown', 'satuanDropdown', 'model', 'referrer'))->render();
    }    

    public function pdtkebijakantambah(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rpjmdMisi = \App\TrxRenstraMisi::where('no_urut', 98)->first();
        $renja = \App\TrxRenjaRancangan::where('id_renja', $id)->first();

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('kebijakan', $referrer);
        $referrer = $referrer[0].'pelaksana';

        IF($request->all()){
            $data = $request->all();
            $rkpd = new \App\TrxRenjaRancanganKebijakan;
            $rkpd->no_urut = $data['no_urut'];
            $rkpd->tahun_renja = $renja->tahun_renja;
            $rkpd->id_renja = $id;
            $rkpd->id_unit = $renja->id_unit;
            $rkpd->id_sasaran_renstra = $renja->id_sasaran_renstra;
            $rkpd->uraian_kebijakan = $data['uraian_kebijakan'];
            if($rkpd->save()) return redirect('renja/pdt/');
        }

        return view('renja._formrenjakebijakan')->with(compact('title', 'rpjmdMisi', 'referrer'))->render();
    } 

    public function pdtkebijakanubah(Request $request, Builder $htmlBuilder, $id, $kebijakan_id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rpjmdMisi = \App\TrxRenstraMisi::where('no_urut', 98)->first();
        $renja = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $rkpd = \App\TrxRenjaRancanganKebijakan::find($kebijakan_id);

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('kebijakan', $referrer);
        $referrer = $referrer[0].'pelaksana';        

        IF($request->all()){
            $data = $request->all();
            $rkpd->no_urut = $data['no_urut'];
            $rkpd->tahun_renja = $renja->tahun_renja;
            $rkpd->id_renja = $id;
            $rkpd->id_unit = $renja->id_unit;
            $rkpd->id_sasaran_renstra = $renja->id_sasaran_renstra;
            $rkpd->uraian_kebijakan = $data['uraian_kebijakan'];
            if($rkpd->save()) return redirect('renja/pdt/');
        }

        return view('renja._formrenjakebijakan', [
            'title' => $title, 
            'rpjmdMisi' => $rpjmdMisi,
            'model' => $rkpd,
            'referrer' => $referrer
        ])->render();
    }
    
    public function pdtkebijakandelete(Request $request, Builder $htmlBuilder, $id, $kebijakan_id)
    {
        $rkpd = \App\TrxRenjaRancanganKebijakan::destroy($kebijakan_id);
        return redirect('renja/pdt/');
    }            


    public function pdtpelaksanatambah(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rpjmdMisi = \App\TrxRenstraMisi::where('no_urut', 98)->first();
        $renja = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $pelaksana = \App\TrxRenjaRancanganPelaksana::where('id_renja', $id)->get();

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('pelaksana', $referrer);
        $referrer = $referrer[0].'pelaksana';        

        IF($request->all()){
            $data = $request->all();
            $pelaksana = new \App\TrxRenjaRancanganPelaksana;
            $pelaksana->tahun_renja = $renja->tahun_renja;
            $pelaksana->no_urut = $data['no_urut'];
            $pelaksana->id_renja = $renja->id_renja;
            $pelaksana->id_aktivitas_kegiatan = 0;
            $pelaksana->id_sub_unit = $data['id_sub_unit'];
            $pelaksana->pagu_aktivitas = $data['pagu_aktivitas'];
            $pelaksana->uraian_aktivitas_kegiatan = $renja->uraian_kegiatan_renstra;
            isset($data['status_musren_aktivitas']) ? $pelaksana->status_musren_aktivitas = 1 : $pelaksana->status_musren_aktivitas = 0;
            if($pelaksana->save()) return redirect('renja/pdt/');
        }

        $subUnitAll = \App\RefSubUnit::where(['id_unit' => $renja->id_unit])->get();
        $subUnitDropdown = NULL;
        foreach ($subUnitAll as $unitAll) {
            $subUnitDropdown[$unitAll->id_sub_unit] = $unitAll->id_sub_unit.'.'.$unitAll->nm_sub;
        }        

        return view('renja._formrenjapelaksana')->with(compact('title', 'rpjmdMisi', 'rpjmdDokumen', 'subUnitDropdown', 'referrer'))->render();
    }
    
    public function pdtpelaksanadelete(Request $request, Builder $htmlBuilder, $id, $unit_id)
    {
        $rkpd = \App\TrxRenjaRancanganPelaksana::destroy($unit_id);
        return redirect('renja/pdt/');
    }

    public function btl(Request $request, Builder $htmlBuilder)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $renjaRancangan = $this->getUserRenjaRancangan(99);

        if($request->ajax()){
            return Datatables::of($renjaRancangan)
                ->addColumn('action', function ($renjaRancangan) use($title) {
                    $data = $renjaRancangan;
                    return 
                    // '<a class="btn btn-default btn-xs" data-href="'.url('/ranwalrkpd/btl/'.$data->id_renja.'/ubah').'" data-toggle="modal" data-target="#myModal" data-title="Sesuaikan Program #'.$data->uraian_kegiatan_renstra.'"><i class="glyphicon glyphicon-pencil bg-white"></i> Ubah</a>'.
                    '<a id="rincian-'.$data->id_renja.'" class="btn btn-default btn-xs" data-href="'.url('/renja/'.$title[1].'/'.$data->id_renja.'/pelaksana').'" ><i class="glyphicon glyphicon-menu-right bg-white"></i> Rincian</a>';
                })            
                ->make(true);
        }
        
        return view('renja.nbl')->with(compact('trxforumskpd', 'rpjmdMisi', 'title'));
    }    
    
    public function btlpelaksana(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rkpd = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $pelaksana = \App\TrxRenjaRancanganPelaksana::where('id_renja', $id)->get();
        $indikator = \App\TrxRenjaRancanganIndikator::where('id_renja', $id)->get();
        $kebijakan = \App\TrxRenjaRancanganKebijakan::where('id_renja', $id)->get();        
        
        return view('renja.pelaksana')->with(compact('pelaksana', 'rkpd', 'indikator', 'kebijakan', 'title'))->render();
    }

    public function btlpelaksanabelanja(Request $request, Builder $htmlBuilder, $id, $sub_unit_id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rkpd = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $pelaksana = \App\TrxRenjaRancanganPelaksana::where('id_pelaksana_renja', $sub_unit_id)->first();
        $belanja = \App\TrxRenjaRancanganBelanja::where('id_pelaksana_renja', $sub_unit_id)->get();
        $lokasi = \App\TrxRenjaRancanganLokasi::where('id_pelaksana_renja', $sub_unit_id)->get();
        
        return view('renja.belanja')->with(compact('pelaksana', 'rkpd', 'belanja', 'title', 'lokasi'))->render();
    }

    public function btlkebijakantambah(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rpjmdMisi = \App\TrxRenstraMisi::where('no_urut', 98)->first();
        $renja = \App\TrxRenjaRancangan::where('id_renja', $id)->first();

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('kebijakan', $referrer);
        $referrer = $referrer[0].'pelaksana';           

        IF($request->all()){
            $data = $request->all();
            $rkpd = new \App\TrxRenjaRancanganKebijakan;
            $rkpd->no_urut = $data['no_urut'];
            $rkpd->tahun_renja = $renja->tahun_renja;
            $rkpd->id_renja = $id;
            $rkpd->id_unit = $renja->id_unit;
            $rkpd->id_sasaran_renstra = $renja->id_sasaran_renstra;
            $rkpd->uraian_kebijakan = $data['uraian_kebijakan'];
            if($rkpd->save()) return redirect('renja/btl/');
        }

        return view('renja._formrenjakebijakan')->with(compact('title', 'rpjmdMisi', 'referrer'))->render();
    } 

    public function btlkebijakanubah(Request $request, Builder $htmlBuilder, $id, $kebijakan_id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rpjmdMisi = \App\TrxRenstraMisi::where('no_urut', 98)->first();
        $renja = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $rkpd = \App\TrxRenjaRancanganKebijakan::find($kebijakan_id);

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('kebijakan', $referrer);
        $referrer = $referrer[0].'pelaksana';           

        IF($request->all()){
            $data = $request->all();
            $rkpd->no_urut = $data['no_urut'];
            $rkpd->tahun_renja = $renja->tahun_renja;
            $rkpd->id_renja = $id;
            $rkpd->id_unit = $renja->id_unit;
            $rkpd->id_sasaran_renstra = $renja->id_sasaran_renstra;
            $rkpd->uraian_kebijakan = $data['uraian_kebijakan'];
            if($rkpd->save()) return redirect('renja/pdt/');
        }

        return view('renja._formrenjakebijakan', [
            'title' => $title, 
            'rpjmdMisi' => $rpjmdMisi,
            'model' => $rkpd,
            'referrer' => $referrer
        ])->render();
    }
    
    public function btlkebijakandelete(Request $request, Builder $htmlBuilder, $id, $kebijakan_id)
    {
        $rkpd = \App\TrxRenjaRancanganKebijakan::destroy($kebijakan_id);
        return redirect('renja/btl/');
    }            

    public function btlpelaksanatambah(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rpjmdMisi = \App\TrxRenstraMisi::where('no_urut', 98)->first();
        $renja = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $pelaksana = \App\TrxRenjaRancanganPelaksana::where('id_renja', $id)->get();

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('pelaksana', $referrer);
        $referrer = $referrer[0].'pelaksana';           

        IF($request->all()){
            $data = $request->all();
            $pelaksana = new \App\TrxRenjaRancanganPelaksana;
            $pelaksana->tahun_renja = $renja->tahun_renja;
            $pelaksana->no_urut = $data['no_urut'];
            $pelaksana->id_renja = $renja->id_renja;
            $pelaksana->id_aktivitas_kegiatan = 1;
            $pelaksana->id_sub_unit = $data['id_sub_unit'];
            $pelaksana->pagu_aktivitas = $data['pagu_aktivitas'];
            isset($data['status_musren_aktivitas']) ? $pelaksana->status_musren_aktivitas = 1 : $pelaksana->status_musren_aktivitas = 0;
            if($pelaksana->save()) return redirect('renja/btl/');
        }

        $subUnitAll = \App\RefSubUnit::where(['id_unit' => $renja->id_unit])->get();
        $subUnitDropdown = NULL;
        foreach ($subUnitAll as $unitAll) {
            $subUnitDropdown[$unitAll->id_sub_unit] = $unitAll->id_sub_unit.'.'.$unitAll->nm_sub;
        }        

        return view('renja._formrenjapelaksana')->with(compact('title', 'rpjmdMisi', 'rpjmdDokumen', 'subUnitDropdown', 'referrer'))->render();
    }
    
    public function btlpelaksanadelete(Request $request, Builder $htmlBuilder, $id, $unit_id)
    {
        $rkpd = \App\TrxRenjaRancanganPelaksana::destroy($unit_id);
        return redirect('renja/btl/');
    }

    public function btlpelaksanabelanjatambah(Request $request, $id, $sub_unit_id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rkpd = \App\TrxRenjaRancangan::where('id_renja', $id)->first();
        $pelaksana = \App\TrxRenjaRancanganPelaksana::where('id_pelaksana_renja', $sub_unit_id)->first();
        $renja = \App\TrxRenjaRancangan::where('id_renja', $id)->first();

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('belanja', $referrer);
        $referrer = $referrer[0].'belanja';   

        // generate dropdown rekening ssh
        $sshRekeningAll = \App\Models\RefRek5::where(['kd_rek_1' => 5, 'kd_rek_2' => 1])->get();
        $sshRekeningDropdown = NULL;
        foreach ($sshRekeningAll as $sshAll) {
            $sshRekeningDropdown[$sshAll->id_rekening] = $sshAll->id_rekening.'.'.$sshAll->nama_kd_rek_5;
            // $sshRekeningDropdown[$sshAll->id_rekening_ssh] = $sshAll->id_rekening_ssh.'.'.$sshAll->rekening->nama_kd_rek_5;
        }
        // generate dropdown satuan
        $satuanAll = \App\Models\RefSatuan::get();
        $satuanDropdown = NULL;
        foreach ($satuanAll as $satuan) {
            $satuanDropdown[$satuan->id_satuan] = $satuan->id_satuan.'. '.$satuan->uraian_satuan;
        }
        
        IF($request->all()){
            $data = $request->all();
            $belanja = new \App\TrxRenjaRancanganBelanja;
            // generate uraian_belanja from input
            $uraian_belanja = \App\Models\RefSshRekening::where(['id_rekening_ssh' => $data['id_rekening_ssh']])->first()['rekening']['nama_kd_rek_5'];
            $belanja->no_urut = $data['no_urut'];
            $belanja->tahun_renja = $renja->tahun_renja;
            $belanja->id_pelaksana_renja = $sub_unit_id;
            $belanja->id_rekening_ssh = $data['id_rekening_ssh'];
            $belanja->uraian_belanja = $uraian_belanja; //ambil dari refrek5
            $belanja->volume_belanja = $data['volume_belanja'];
            $belanja->id_satuan = $data['id_satuan'];
            $belanja->koefisien = $data['koefisien'];
            $belanja->harga_satuan = $data['harga_satuan'];
            $belanja->jml_belanja = $data['harga_satuan'] * $data['volume_belanja'];
            if($belanja->save()) return redirect('renja/btl/');
        }

        return view('renja._formrenjabelanja')->with(compact('title', 'renja', 'sshRekeningDropdown', 'satuanDropdown', 'referrer'))->render();
    } 
    
    public function transProgramRenja(Request $req)
    {
      $result=DB::INSERT('INSERT INTO trx_renja_rancangan_program(tahun_renja,no_urut,id_rkpd_ranwal,id_program_rpjmd,id_unit,
                id_visi_renstra,id_misi_renstra,id_tujuan_renstra,id_sasaran_renstra,id_program_renstra,uraian_program_renstra,
                pagu_tahun_ranwal,pagu_tahun_renstra)
                SELECT a.tahun_rkpd,(@id:=@id+1) as no_urut,a.id_rkpd_ranwal,a.id_program_rpjmd,a.id_unit,
                a.id_visi_renstra,a.id_misi_renstra,a.id_tujuan_renstra,a.id_sasaran_renstra,a.id_program_renstra,a.uraian_program_renstra,
                a.pagu_tahun_program,a.pagu_tahun_program FROM
                (SELECT DISTINCT a.tahun_rkpd,c.id_rkpd_ranwal,a.id_sasaran_renstra,a.id_program_rpjmd,a.id_unit,a.id_visi_renstra,a.id_misi_renstra,
                a.id_tujuan_renstra,a.id_program_renstra,a.pagu_tahun_program,b.uraian_program_renstra
                FROM trx_rkpd_renstra AS a
                INNER JOIN trx_rkpd_ranwal as c ON a.tahun_rkpd = c.tahun_rkpd and a.id_rkpd_rpjmd = c.id_rkpd_rpjmd
                INNER JOIN trx_renstra_program AS b ON a.id_sasaran_renstra = b.id_sasaran_renstra AND a.id_program_renstra = b.id_program_renstra AND a.id_program_rpjmd = b.id_program_rpjmd
                WHERE c.status_data = 1 and a.tahun_rkpd = '.$req->tahun_renja.' AND a.id_unit ='.$req->id_unit
                .'GROUP BY a.tahun_rkpd,c.id_rkpd_ranwal,a.id_sasaran_renstra,a.id_program_rpjmd,a.id_unit,a.id_visi_renstra,a.id_misi_renstra,
                a.id_tujuan_renstra,a.id_program_renstra,a.pagu_tahun_program,b.uraian_program_renstra) a, (SELECT @id:=0) b');

      if ($result==0 ) {
              return redirect()->action('TrxRenjaController@loadData');
            }
        else {
              return redirect()->action('TrxRenjaController@transProgramIndikatorRenja',['tahun_renja'=>$req->tahun_renja,'id_unit'=>$req->id_unit]);
            }

    }
    public function transProgramIndikatorRenja($tahun_renja,$id_unit)
    {
      $result=DB::INSERT('INSERT INTO trx_renja_rancangan_program_indikator(tahun_renja,no_urut,id_renja_program,id_program_renstra,
                id_perubahan,kd_indikator,uraian_indikator_program_renja,tolok_ukur_indikator,target_renstra,target_renja)
                SELECT a.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja_program,a.id_program_renstra,a.id_perubahan,a.kd_indikator,
                a.uraian_indikator_program_renstra,a.tolok_ukur_indikator,a.angka_tahun1,a.angka_tahun1 FROM
                (SELECT DISTINCT a.id_unit,f.id_program_renstra,f.id_perubahan,f.kd_indikator,f.uraian_indikator_program_renstra,
                f.tolok_ukur_indikator,f.angka_tahun1,h.tahun_1 AS tahun_renja
                FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                INNER JOIN trx_renstra_program_indikator AS f ON f.id_program_renstra = e.id_program_renstra,ref_tahun AS h
                UNION
                SELECT DISTINCT a.id_unit,f.id_program_renstra,f.id_perubahan,f.kd_indikator,f.uraian_indikator_program_renstra,
                f.tolok_ukur_indikator,f.angka_tahun2,h.tahun_2 AS tahun_renja
                FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                INNER JOIN trx_renstra_program_indikator AS f ON f.id_program_renstra = e.id_program_renstra,ref_tahun AS h
                UNION
                SELECT DISTINCT a.id_unit,f.id_program_renstra,f.id_perubahan,f.kd_indikator,f.uraian_indikator_program_renstra,
                f.tolok_ukur_indikator,f.angka_tahun3,h.tahun_3 AS tahun_renja
                FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                INNER JOIN trx_renstra_program_indikator AS f ON f.id_program_renstra = e.id_program_renstra,ref_tahun AS h
                UNION
                SELECT DISTINCT a.id_unit,f.id_program_renstra,f.id_perubahan,f.kd_indikator,f.uraian_indikator_program_renstra,
                f.tolok_ukur_indikator,f.angka_tahun4,h.tahun_4 AS tahun_renja
                FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                INNER JOIN trx_renstra_program_indikator AS f ON f.id_program_renstra = e.id_program_renstra,ref_tahun AS h
                UNION
                SELECT DISTINCT a.id_unit,f.id_program_renstra,f.id_perubahan,f.kd_indikator,f.uraian_indikator_program_renstra,
                f.tolok_ukur_indikator,f.angka_tahun5,h.tahun_5 AS tahun_renja
                FROM trx_renstra_visi AS a
                INNER JOIN trx_renstra_misi AS b ON b.id_visi_renstra = a.id_visi_renstra
                INNER JOIN trx_renstra_tujuan AS c ON c.id_misi_renstra = b.id_misi_renstra
                INNER JOIN trx_renstra_sasaran AS d ON d.id_tujuan_renstra = c.id_tujuan_renstra
                INNER JOIN trx_renstra_program AS e ON e.id_sasaran_renstra = d.id_sasaran_renstra
                INNER JOIN trx_renstra_program_indikator AS f ON f.id_program_renstra = e.id_program_renstra,ref_tahun AS h
                ) a
                INNER JOIN trx_renja_rancangan_program b
                ON a.tahun_renja = b.tahun_renja AND a.id_unit = b.id_unit AND a.id_program_renstra = b.id_program_renstra,
                (SELECT @id:=0) c WHERE a.tahun_renja='.$tahun_renja.' and a.id_unit='.$id_unit);

      if ($result==0 ) {
            return redirect()->action('TrxRenjaController@loadData');
            }
        else {
            return redirect()->action('TrxRenjaController@transKegiatanRenja',['tahun_renja'=>$tahun_renja,'id_unit'=>$id_unit]);
            }

    }
    public function transKegiatanRenja($tahun_renja,$id_unit)
    {
        $result=DB::INSERT('INSERT INTO trx_renja_rancangan(
                tahun_renja,no_urut,id_renja_program,id_rkpd_renstra,id_rkpd_ranwal,id_unit,id_visi_renstra,id_misi_renstra,
                id_tujuan_renstra,id_sasaran_renstra,id_program_renstra,uraian_program_renstra,id_kegiatan_renstra,uraian_kegiatan_renstra,
                pagu_tahun_ranwal,pagu_tahun_kegiatan)
                SELECT
                a.tahun_rkpd,(@id:=@id+1) AS no_urut,b.id_renja_program,a.id_rkpd_renstra,b.id_rkpd_ranwal,a.id_unit,a.id_visi_renstra,
                a.id_misi_renstra,a.id_tujuan_renstra,a.id_sasaran_renstra,a.id_program_renstra,b.uraian_program_renstra,
                a.id_kegiatan_renstra,a.uraian_kegiatan_renstra,a.pagu_tahun_kegiatan,a.pagu_tahun_kegiatan
                FROM
                trx_rkpd_renstra AS a
                INNER JOIN trx_renja_rancangan_program AS b ON a.tahun_rkpd = b.tahun_renja AND a.id_program_rpjmd = b.id_program_rpjmd
                AND a.id_unit = b.id_unit AND a.id_visi_renstra = b.id_visi_renstra AND a.id_misi_renstra = b.id_misi_renstra
                AND a.id_tujuan_renstra = b.id_tujuan_renstra AND a.id_sasaran_renstra = b.id_sasaran_renstra AND a.id_program_renstra = b.id_program_renstra,
                (SELECT @id:=0) c WHERE a.tahun_rkpd='.$tahun_renja.' and a.id_unit='.$id_unit);

        if ($result==0 ) {
                return redirect()->action('TrxRenjaController@loadData');
              }
        else {
              return redirect()->action('TrxRenjaController@transKegiatanIndikatorRenja',['tahun_renja'=>$tahun_renja,'id_unit'=>$id_unit]);
              }
    }
    public function transKegiatanIndikatorRenja($tahun_renja,$id_unit)
    {
      $result=DB::INSERT('INSERT INTO trx_renja_rancangan_indikator(tahun_renja,no_urut,id_renja,id_perubahan,kd_indikator,
              uraian_indikator_kegiatan_renja,tolok_ukur_indikator,angka_tahun,angka_renstra,status_data)
              SELECT b.tahun_renja,(@id:=@id+1) as no_urut,b.id_renja,0 as id_perubahan,a.kd_indikator,a.uraian_indikator_kegiatan,
              a.tolokukur_kegiatan,a.target_output,a.target_output,0 as status_data
              FROM trx_rkpd_renstra_indikator AS a
              INNER JOIN trx_renja_rancangan AS b ON b.tahun_renja = a.tahun_rkpd AND b.id_rkpd_renstra = a.id_rkpd_renstra,
              (SELECT @id:=0) c WHERE b.tahun_rkpd='.$tahun_renja.' and b.id_unit='.$id_unit);

      // if ($result==0 ) {
      //         return redirect()->action('TrxRenjaController@loadData');
      //       }
      // else {
              return redirect()->action('TrxRenjaController@loadData');
            // }
    }
    public function getProgramRenja($tahun_renja,$id_unit)
    {
      $programrenja=DB::select('SELECT (@id:=@id+1) as no_urut,b.* FROM (SELECT a.tahun_renja,CASE c.no_urut
                WHEN 99 THEN "Belanja Tidak Langsung"
                WHEN 98 THEN "Pendapatan"
                ELSE "Belanja Langsung"
                END AS kelompok_program,a.id_renja_program,a.id_rkpd_ranwal,a.id_program_rpjmd,a.id_unit,a.id_program_renstra,
                a.uraian_program_renstra,a.pagu_tahun_ranwal,a.pagu_tahun_renstra,(a.pagu_tahun_ranwal/1000000) AS pagu_ranwal_display,(a.pagu_tahun_renstra/1000000) AS pagu_renstra_display,CASE a.status_data
                WHEN 0 THEN "Draft"
                WHEN 1 THEN "Posting"
                END AS status_display,a.status_data
                FROM trx_renja_rancangan_program AS a
                INNER JOIN trx_renstra_visi AS b ON a.id_unit = b.id_unit AND a.id_visi_renstra = b.id_visi_renstra
                INNER JOIN trx_renstra_misi AS c ON c.id_visi_renstra = b.id_visi_renstra AND a.id_misi_renstra = c.id_misi_renstra'
                .' WHERE c.no_urut < 98 and  a.tahun_renja='.$tahun_renja.' and a.id_unit='.$id_unit.')b,(SELECT @id:=0) c ');

      return DataTables::of($programrenja)
          ->addColumn('action', function ($programrenja) {
              return '<button data-toggle="tooltip" title="Lihat Kegiatan Renja" class="view-renjakegiatan btn btn-sm btn-warning" data-id_renja_program="'.$programrenja->id_renja_program.'" data-id_tahun_renja="'.$programrenja->tahun_renja.'" data-id_unit="'.$programrenja->id_unit.'"><i class="glyphicon glyphicon-eye-open"></i></button> <button data-toggle="tooltip" title="Lihat Indikator Program Renja" class="view-renjaindikator btn btn-sm btn-primary" data-id_renja_program="'.$programrenja->id_renja_program.'" data-id_tahun_renja="'.$programrenja->tahun_renja.'" data-id_unit="'.$programrenja->id_unit.'"><i class="glyphicon glyphicon-eye-open"></i></button> <button data-toggle="tooltip" title="Edit Program Renja" class="edit-program btn btn-sm btn-info" data-id_renja_program="'.$programrenja->id_renja_program.'" data-id_tahun_renja="'.$programrenja->tahun_renja.'" data-id_unit="'.$programrenja->id_unit.'" data-uraian_renja_program="'.$programrenja->uraian_program_renstra.'"  data-pagu_renstra="'.$programrenja->pagu_tahun_renstra.'" data-pagu_renja="'.$programrenja->pagu_tahun_ranwal.'"><i class="glyphicon glyphicon-edit"></i></button>';})
          ->make(true);
    }
    public function getProgramIndikatorRenja($tahun_renja,$id_unit,$id_program)
    {
      $programrenja=DB::select('SELECT (@id:=@id+1) as no_urut,b.* FROM (SELECT b.tahun_renja, b.id_renja_program, b.id_program_renstra, b.id_indikator_program_renja,
                b.kd_indikator,b.uraian_indikator_program_renja,b.tolok_ukur_indikator,b.target_renstra,b.target_renja,b.status_data,a.uraian_program_renstra,CASE a.status_data
                WHEN 0 THEN "Draft"
                WHEN 1 THEN "Posting"
                END AS status_display
                FROM trx_renja_rancangan_program AS a
                INNER JOIN trx_renja_rancangan_program_indikator AS b ON b.id_program_renstra = a.id_program_renstra
                AND a.tahun_renja = b.tahun_renja AND a.id_renja_program = b.id_renja_program'
                .' WHERE a.tahun_renja='.$tahun_renja.' and a.id_unit='.$id_unit.' and b.id_renja_program='.$id_program.')b,(SELECT @id:=0) c ');

      return DataTables::of($programrenja)
          ->addColumn('action', function ($programrenja) {
              return '<button class="edit-indikator btn btn-sm btn-info" data-id_renja_program="'.$programrenja->id_renja_program.'" data-id_tahun_renja="'.$programrenja->tahun_renja.'" data-id_indikator_program_renja="'.$programrenja->id_indikator_program_renja.'" data-uraian_indikator_program_renja="'.$programrenja->uraian_indikator_program_renja.'"  data-target_renstra="'.$programrenja->target_renstra.'" data-target_renja="'.$programrenja->target_renja.'" data-status_data="'.$programrenja->status_data.'" data-tolok_ukur_indikator="'.$programrenja->tolok_ukur_indikator.'"><i class="glyphicon glyphicon-edit"></i></button>';})
          ->make(true);
    }
    public function getKegiatanRenja($tahun_renja,$id_unit,$id_program)
    {
      $programrenja=DB::select('SELECT (@id:=@id+1) as no_urut,b.* FROM (SELECT a.tahun_renja,a.id_renja,a.id_renja_program,a.id_rkpd_renstra,a.id_rkpd_ranwal,
                a.id_unit,a.id_kegiatan_renstra,a.pagu_tahun_ranwal,a.pagu_tahun_kegiatan,a.pagu_musren,a.status_musren,a.status_data,b.uraian_kegiatan_renstra,
                CASE a.status_data
                  WHEN 0 THEN "Draft"
                  WHEN 1 THEN "Posting"
                END AS status_display,
                CASE a.status_musren
                  WHEN 0 THEN "Renja SKPD"
                  WHEN 1 THEN "Musrenbang"
                END AS status_bahas
                FROM trx_renja_rancangan AS a
                INNER JOIN trx_renstra_kegiatan AS b ON a.id_program_renstra = b.id_program_renstra AND
                a.id_kegiatan_renstra = b.id_kegiatan_renstra'
                .' WHERE a.tahun_renja='.$tahun_renja.' and a.id_unit='.$id_unit.' and a.id_renja_program='.$id_program.')b,(SELECT @id:=0) c ');

      return DataTables::of($programrenja)
          ->addColumn('action', function ($programrenja) {
              return '<button class="view-kegiatanindikator btn btn-sm btn-warning" data-id_renja_program="'.$programrenja->id_renja_program.'" data-id_tahun_renja="'.$programrenja->tahun_renja.'" data-id_renja="'.$programrenja->id_renja.'" data-id_unit="'.$programrenja->id_unit.'"><i class="glyphicon glyphicon-eye-open"></i></button> <button class="view-renjaaktivitas btn btn-sm btn-primary" data-id_renja_program="'.$programrenja->id_renja_program.'" data-id_tahun_renja="'.$programrenja->tahun_renja.'" data-id_renja="'.$programrenja->id_renja.'" data-id_unit="'.$programrenja->id_unit.'"><i class="glyphicon glyphicon-eye-open"></i></button> <button class="edit-kegiatanrkpd btn btn-sm btn-info" data-id_renja_program="'.$programrenja->id_renja_program.'" data-id_tahun_renja="'.$programrenja->tahun_renja.'" data-id_renja="'.$programrenja->id_renja.'" data-id_unit="'.$programrenja->id_unit.'" data-uraian_kegiatan_renstra="'.$programrenja->uraian_kegiatan_renstra.'"  data-pagu_renstra="'.$programrenja->pagu_tahun_ranwal.'" data-pagu_renja="'.$programrenja->pagu_tahun_kegiatan.'" data-status_data="'.$programrenja->status_data.'" data-pagu_musren="'.$programrenja->pagu_musren.'" data-status_musren="'.$programrenja->status_musren.'"><i class="glyphicon glyphicon-edit"></i></button>';})
          ->make(true);
    }
    public function getKegiatanIndikatorRenja($tahun_renja,$id_unit,$id_renja)
    {
      $kegiatanrenja=DB::select('SELECT (@id:=@id+1) as no_urut,b.* FROM (SELECT b.tahun_renja,a.id_unit,b.id_renja,b.id_indikator_kegiatan_renja,b.kd_indikator,b.uraian_indikator_kegiatan_renja,
      b.tolok_ukur_indikator,b.angka_tahun,b.angka_renstra,b.status_data,CASE a.status_data
      WHEN 0 THEN "Draft"
      WHEN 1 THEN "Posting"
      END AS status_display FROM trx_renja_rancangan AS a
      INNER JOIN trx_renja_rancangan_indikator AS b ON a.tahun_renja = b.tahun_renja AND a.id_renja = b.id_renja'
      .' WHERE a.tahun_renja='.$tahun_renja.' and a.id_unit='.$id_unit.' and a.id_renja='.$id_renja.')b,(SELECT @id:=0) c ');

      return DataTables::of($kegiatanrenja)
      ->addColumn('action', function ($kegiatanrenja) {
              return '<button class="edit-kegiatanindikator btn btn-sm btn-info" data-id_renja="'.$kegiatanrenja->id_renja.'" data-id_tahun_renja="'.$kegiatanrenja->tahun_renja.'" data-id_indikator_kegiatan_renja="'.$kegiatanrenja->id_indikator_kegiatan_renja.'" data-id_unit="'.$kegiatanrenja->id_unit.'" data-uraian_indikator_kegiatan_renja="'.$kegiatanrenja->uraian_indikator_kegiatan_renja.'"  data-angka_renstra="'.$kegiatanrenja->angka_renstra.'" data-angka_tahun="'.$kegiatanrenja->angka_tahun.'" data-status_data="'.$kegiatanrenja->status_data.'" data-tolok_ukur_indikator="'.$kegiatanrenja->tolok_ukur_indikator.'"><i class="glyphicon glyphicon-edit"></i></button>';})
      ->make(true);
    }
    public function getAktivitasRenja($tahun_renja,$id_unit,$id_renja)
    {
      $aktivitasrenja=DB::select('SELECT (@id:=@id+1) as no_urut,b.* FROM (SELECT b.tahun_renja,a.id_unit,b.id_renja,b.id_aktivitas_renja,b.id_aktivitas_asb,b.uraian_aktivitas_kegiatan,
      b.tolak_ukur_aktivitas,b.target_output_aktivitas,b.pagu_aktivitas,b.pagu_musren,b.status_data,b.status_musren,
      CASE a.status_data
      WHEN 0 THEN "Draft"
      WHEN 1 THEN "Posting"
      END AS status_display,
      CASE a.status_musren
      WHEN 0 THEN "Renja SKPD"
      WHEN 1 THEN "Musrenbang"
      END AS status_bahas
      FROM trx_renja_rancangan AS a
      INNER JOIN trx_renja_rancangan_aktivitas AS b ON b.id_renja = a.id_renja AND a.tahun_renja = b.tahun_renja'
      .' WHERE a.tahun_renja='.$tahun_renja.' and a.id_unit='.$id_unit.' and a.id_renja='.$id_renja.')b,(SELECT @id:=0) c ');

      return DataTables::of($aktivitasrenja)
      ->addColumn('action', function ($aktivitasrenja) {
              return '<button class="add-aktivitasrenja btn btn-sm btn-warning" data-id_renja_program="'.$programrenja->id_renja_program.'" data-id_tahun_renja="'.$programrenja->tahun_renja.'" data-id_renja="'.$programrenja->id_renja.'" data-id_unit="'.$programrenja->id_unit.'"><i class="glyphicon glyphicon-eye-open"></i></button> <button class="edit-aktivitasrenja btn btn-sm btn-info" data-id_renja="'.$aktivitasrenja->id_renja.'" data-id_tahun_renja="'.$aktivitasrenja->tahun_renja.'" data-id_indikator_kegiatan_renja="'.$aktivitasrenja->id_indikator_kegiatan_renja.'" data-id_unit="'.$aktivitasrenja->id_unit.'" data-uraian_indikator_kegiatan_renja="'.$aktivitasrenja->uraian_indikator_kegiatan_renja.'"  data-angka_renstra="'.$aktivitasrenja->angka_renstra.'" data-angka_tahun="'.$aktivitasrenja->angka_tahun.'" data-status_data="'.$aktivitasrenja->status_data.'" data-tolok_ukur_indikator="'.$aktivitasrenja->tolok_ukur_indikator.'"><i class="glyphicon glyphicon-edit"></i></button>';})
      ->make(true);
    }   

}
