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
use App\Http\Controllers\SettingController;
use Auth;
use App\Models\TrxRkpdRanwalDokumen;
use App\Models\TrxRkpdRanwal;



class TrxRanhirRKPDController extends Controller
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

        return view('ranhirrkpd.load')->with(compact('unit'));
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
       return view('ranhirrkpd.dashboard');
    }

    public function blangsung(Request $request, Builder $htmlBuilder)
    {
        
        return view('ranhirrkpd.blangsung');
    }

    public function getDataDokumen()
    {
        $getDataDokumen = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_dokumen_ranwal,a.nomor_ranwal,a.tanggal_ranwal,a.tahun_ranwal,
                a.uraian_perkada, a.id_unit_perencana, a.jabatan_tandatangan,a.nama_tandatangan,a.nip_tandatangan,a.flag,b.nm_unit,
                CASE a.flag
                    WHEN 0 THEN "fa fa-question"
                    WHEN 1 THEN "fa fa-check-square-o"
                END AS status_icon,
                CASE a.flag
                    WHEN 0 THEN "red"
                    WHEN 1 THEN "green"
                END AS warna
                FROM trx_rkpd_ranwal_dokumen AS a
                INNER JOIN ref_unit AS b ON a.id_unit_perencana = b.id_unit,
                (SELECT @id:=0) z');

        return DataTables::of($getDataDokumen)
        ->addColumn('action', function ($getDataDokumen) {
            if ($getDataDokumen->flag==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnEditDokumen" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-warning"></i> Ubah Dokumen RKPD</a>
                        </li>
                        <li>
                            <a id="btnPostingRkpd" class="dropdown-item"><i class="fa fa-check fa-fw fa-lg text-success"></i> Posting Dokumen RKPD</a>
                        </li>                          
                    </ul>
                </div>
            ';
            if ($getDataDokumen->flag==1)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnEditDokumen" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg text-warning"></i> Ubah Dokumen RKPD</a>
                        </li>
                        <li>
                            <a id="btnPostingRkpd" class="dropdown-item"><i class="fa fa-times fa-fw fa-lg text-danger"></i> Un-Posting Dokumen RKPD</a>
                        </li>                          
                    </ul>
                </div>
            ';
        })
        ->make(true);
    }

    public function getUnit(Request $request){
        $unit = \App\Models\RefUnit::select();
        if(isset(Auth::user()->getUserSubUnit)){
            foreach(Auth::user()->getUserSubUnit as $data){
                $unit->orWhere(['id_unit' => $data->kd_unit]);                
            }
        }
        $unit = $unit->get();
        if($request->ajax()){
          return json_encode($unit);
        }
    }

    public function dokumen(Request $request, Builder $htmlBuilder)
    {
        return view('ranhirrkpd.doku');
    }

    public function getDataPerencana()
    {
        $dataPerencana = DB::SELECT('SELECT a.kd_kab,a.id_pemda,a.prefix_pemda,a.nm_prov,a.nm_kabkota,a.ibu_kota,a.nama_jabatan_kepala_daerah,a.nama_kepala_daerah,a.nama_jabatan_sekretariat_daerah,a.nama_sekretariat_daerah,a.nip_sekretariat_daerah,a.unit_perencanaan,a.nama_kepala_bappeda,a.nip_kepala_bappeda,a.unit_keuangan,a.nama_kepala_bpkad,a.nip_kepala_bpkad,b.nm_unit
            FROM ref_pemda AS a
            LEFT OUTER JOIN ref_unit AS b ON a.unit_perencanaan = b.id_unit LIMIT 1');

        return json_encode($dataPerencana);
    }

    public function addDokumen(Request $req)
    {
        
            $data = new TrxRkpdRanwalDokumen;
            $data->nomor_ranwal = $req->nomor_rkpd ;
            $data->tanggal_ranwal = $req->tanggal_rkpd ;
            $data->tahun_ranwal = $req->tahun_rkpd ;
            $data->uraian_perkada = $req->uraian_perkada ;
            $data->id_unit_perencana = $req->id_unit_perencana ;
            $data->jabatan_tandatangan = "Kepala" ;
            $data->nama_tandatangan = $req->nama_tandatangan ;
            $data->nip_tandatangan = $req->nip_tandatangan ;
            $data->flag = 0 ;

        if($req->id_unit_perencana!= '' || $req->id_unit_perencana != null){
            try{
            $data->save (['timestamps' => false]);
              return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
               $error_code = $e->errorInfo[1] ;
               return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Gagal Disimpan (Unit Perencana Masih Kosong)','status_pesan'=>'0']);
        }
    }

    public function editDokumen(Request $req)
    {
        try{
            $data = TrxRkpdRanwalDokumen::find($req->id_dokumen_rkpd);
            $data->nomor_ranwal = $req->nomor_rkpd ;
            $data->tanggal_ranwal = $req->tanggal_rkpd ;
            $data->tahun_ranwal = $req->tahun_rkpd ;
            $data->uraian_perkada = $req->uraian_perkada ;
            $data->id_unit_perencana = $req->id_unit_perencana ;
            $data->jabatan_tandatangan = "Kepala" ;
            $data->nama_tandatangan = $req->nama_tandatangan ;
            $data->nip_tandatangan = $req->nip_tandatangan ;
            // $data->flag = $req->flag ;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
          }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
          }
    }

    public function postDokumen(Request $req)
    {
        
        $data = DB::UPDATE('UPDATE trx_rkpd_ranwal_dokumen SET flag ='.$req->flag.' WHERE tahun_ranwal='.$req->tahun_rkpd.' AND id_dokumen_ranwal='.$req->id_dokumen_rkpd);
       
            if($data != 0){
                $dataProg=DB::UPDATE('UPDATE trx_rkpd_ranwal SET status_data ='.$req->status.', id_dokumen='.$req->id_dokumen_rkpd.' WHERE tahun_rkpd='.$req->tahun_rkpd.' AND status_data='.$req->status_awal);

                if($dataProg != 0){
                    return response ()->json (['pesan'=>'Data Berhasil Posting','status_pesan'=>'1']);
                } else {
                    return response ()->json (['pesan'=>'Data Gagal Diposting (1cprPD)','status_pesan'=>'0']);
                }
            } else {
                return response ()->json (['pesan'=>'Data Gagal Diposting (0cdrPD)','status_pesan'=>'0']);
            }
    }

    public function hapusDokumen(Request $req)
    {
        $result = TrxRkpdRanwalDokumen::destroy($req->id_dokumen_rkpd);
    
        if($result != 0){
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal dihapus','status_pesan'=>'0']);
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

    protected function getUserRkpdRanhir($no_urut){
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
            $rkpdRanhir = \App\TrxRkpdRanhir::select();
            foreach($misi as $misi){
                // $rkpdRanhir->orWhere(['id_misi_renstra' => $misi->id_misi_renstra]);
                $rkpdRanhir->orWhereRaw("id_misi_renstra = $misi->id_misi_renstra AND tahun_rkpd = $tahun");
            }
            $rkpdRanhir = $rkpdRanhir->get();
        }else{
            $rkpdRanhir = \App\TrxRkpdRanhir::select()->where(['tahun_rkpd' => $tahun, 'no_urut' => 99999])->get();
        }        
        return $rkpdRanhir;
    }

    public function pdt(Request $request, Builder $htmlBuilder)
    {
        $title = ['Pendapatan', 'pdt'];
        $data = $this->getUserRkpdRanhir(98);
        
        if($request->ajax()){
            return Datatables::of($data)
                ->addColumn('action', function ($data) {
                    return 
                    // '<a class="btn btn-default btn-xs" data-href="'.url('/ranwalrkpd/btl/'.$data->id_renja.'/ubah').'" data-toggle="modal" data-target="#myModal" data-title="Sesuaikan Program #'.$data->uraian_kegiatan_renstra.'"><i class="glyphicon glyphicon-pencil bg-white"></i> Ubah</a>'.
                    '<a id="rincian-'.$data->id_rkpd.'" class="btn btn-default btn-xs" data-href="'.url('/ranhirrkpd/pdt/'.$data->id_rkpd.'/pelaksana').'" ><i class="glyphicon glyphicon-menu-right bg-white"></i> Rincian</a>';
                })            
                ->make(true);
        }        
        return view('ranhirrkpd.nbl')->with(compact('data', 'title'));

    }

    public function pdtpelaksana(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();
        $pelaksana = \App\TrxRkpdRanhirPelaksana::where('id_rkpd', $id)->get();
        $indikator = \App\TrxRkpdRanhirIndikator::where('id_program_rpjmd', $rkpd->id_program_rpjmd)->get();     
        
        return view('ranhirrkpd.pelaksana')->with(compact('pelaksana', 'rkpd', 'indikator', 'title'))->render();
    }


    public function pdtpelaksanatambah(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('pelaksana', $referrer);
        $referrer = $referrer[0].'pelaksana';          

        IF($request->all()){
            $data = $request->all();
            $pelaksana = new \App\TrxRkpdRanhirPelaksana;
            $pelaksana->tahun_rkpd = $rkpd->tahun_rkpd;
            $pelaksana->no_urut = $data['no_urut'];
            $pelaksana->id_rkpd = $id;
            $pelaksana->id_musrencam = 0;
            $pelaksana->uraian_aktivitas_kegiatan = $rkpd->uraian_kegiatan_renstra;
            $pelaksana->pagu_aktivitas_forum = $data['pagu_aktivitas'];
            $pelaksana->pagu_aktivitas_sebelumnya = $data['pagu_aktivitas_sebelumnya'];
            $pelaksana->id_sub_unit = $data['id_sub_unit'];
            $pelaksana->id_aktivitas_kegiatan = 0;
            isset($data['status_musren_aktivitas']) ? $pelaksana->sumber_usulan = 1 : $pelaksana->sumber_usulan = 0;
            if($pelaksana->save()) return redirect('rancanganrkpd/pdt/');
        }

        $subUnitAll = \App\RefSubUnit::where(['id_unit' => $rkpd->id_unit])->get();
        $subUnitDropdown = NULL;
        foreach ($subUnitAll as $unitAll) {
            $subUnitDropdown[$unitAll->id_sub_unit] = $unitAll->id_sub_unit.'.'.$unitAll->nm_sub;
        }        

        return view('ranhirrkpd._formrenjapelaksana')->with(compact('title', 'subUnitDropdown', 'referrer'))->render();
    }

    public function pdtpelaksanadelete(Request $request, Builder $htmlBuilder, $id, $unit_id)
    {
        $rkpd = \App\TrxRkpdRanhirPelaksana::destroy($unit_id);
        return redirect('rancanganrkpd/pdt/');
    }

    public function pdtpelaksanabelanja(Request $request, Builder $htmlBuilder, $id, $sub_unit_id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();
        $pelaksana = \App\TrxRkpdRanhirPelaksana::where('id_pelaksana_rkpd', $sub_unit_id)->first();
        $belanja = \App\TrxRkpdRanhirBelanja::where('id_pelaksana_rkpd', $sub_unit_id)->get();
        $lokasi = \App\TrxRkpdRanhirLokasi::where('id_pelaksana_rkpd', $sub_unit_id)->get();
        
        return view('ranhirrkpd.belanja')->with(compact('pelaksana', 'rkpd', 'belanja', 'title', 'lokasi'))->render();
    }

    public function pdtpelaksanabelanjatambah(Request $request, $id, $sub_unit_id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();
        $pelaksana = \App\TrxRkpdRanhirPelaksana::where('id_pelaksana_rkpd', $sub_unit_id)->first();

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
            $belanja = new \App\TrxRkpdRanhirBelanja;
            // generate uraian_belanja from input
            $uraian_belanja = \App\Models\RefSshRekening::where(['id_rekening_ssh' => $data['id_rekening_ssh']])->first()['rekening']['nama_kd_rek_5'];
            $belanja->no_urut = $data['no_urut'];
            $belanja->tahun_rkpd = $rkpd->tahun_rkpd;
            $belanja->id_pelaksana_rkpd = $sub_unit_id;
            $belanja->id_rekening_ssh = $data['id_rekening_ssh'];
            $belanja->uraian_belanja = $uraian_belanja; //ambil dari refrek5
            $belanja->volume_belanja = $data['volume_belanja'];
            $belanja->id_satuan = $data['id_satuan'];
            $belanja->koefisien = $data['koefisien'];
            $belanja->harga_satuan = $data['harga_satuan'];
            $belanja->jml_belanja = $data['harga_satuan'] * $data['volume_belanja'];
            if($belanja->save()) return redirect('rancanganrkpd/pdt/');
        }

        return view('ranhirrkpd._formrenjabelanja')->with(compact('title', 'renja', 'sshRekeningDropdown', 'satuanDropdown', 'referrer'))->render();
    }

    public function pdtpelaksanabelanjaubah(Request $request, $id, $sub_unit_id, $belanja_id)
    {
        $title = ['Pendapatan', 'pdt'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();
        $pelaksana = \App\TrxRkpdRanhirPelaksana::where('id_pelaksana_rkpd', $sub_unit_id)->first();

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
            $belanja->tahun_rkpd = $rkpd->tahun_rkpd;
            $belanja->id_pelaksana_rkpd = $sub_unit_id;
            $belanja->id_rekening_ssh = $data['id_rekening_ssh'];
            $belanja->uraian_belanja = $uraian_belanja; //ambil dari refrek5
            $belanja->volume_belanja = $data['volume_belanja'];
            $belanja->id_satuan = $data['id_satuan'];
            $belanja->koefisien = $data['koefisien'];
            $belanja->harga_satuan = $data['harga_satuan'];
            $belanja->jml_belanja = $data['harga_satuan'] * $data['volume_belanja'];
            if($belanja->save()) return redirect('rancanganrkpd/pdt/');
        }

        return view('renja._formrenjabelanja')->with(compact('title', 'renja', 'sshRekeningDropdown', 'satuanDropdown', 'model', 'referrer'))->render();
    }

    public function btl(Request $request, Builder $htmlBuilder)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $data = $this->getUserRkpdRanhir(99);
        
        if($request->ajax()){
            return Datatables::of($data)
                ->addColumn('action', function ($data) {
                    return 
                    // '<a class="btn btn-default btn-xs" data-href="'.url('/ranwalrkpd/btl/'.$data->id_renja.'/ubah').'" data-toggle="modal" data-target="#myModal" data-title="Sesuaikan Program #'.$data->uraian_kegiatan_renstra.'"><i class="glyphicon glyphicon-pencil bg-white"></i> Ubah</a>'.
                    '<a id="rincian-'.$data->id_rkpd.'" class="btn btn-default btn-xs" data-href="'.url('/ranhirrkpd/pdt/'.$data->id_rkpd.'/pelaksana').'" ><i class="glyphicon glyphicon-menu-right bg-white"></i> Rincian</a>';
                })            
                ->make(true);
        }        
        return view('ranhirrkpd.nbl')->with(compact('data', 'title'));

    }

    public function btlpelaksana(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();
        $pelaksana = \App\TrxRkpdRanhirPelaksana::where('id_rkpd', $id)->get();
        $indikator = \App\TrxRkpdRanhirIndikator::where('id_program_rpjmd', $rkpd->id_program_rpjmd)->get();     
        
        return view('ranhirrkpd.pelaksana')->with(compact('pelaksana', 'rkpd', 'indikator', 'title'))->render();
    }


    public function btlpelaksanatambah(Request $request, Builder $htmlBuilder, $id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();

        //this code need to be refactored/reoop/
        $referrer = $request->url();
        $referrer = explode('pelaksana', $referrer);
        $referrer = $referrer[0].'pelaksana';          

        IF($request->all()){
            $data = $request->all();
            $pelaksana = new \App\TrxRkpdRanhirPelaksana;
            $pelaksana->tahun_rkpd = $rkpd->tahun_rkpd;
            $pelaksana->no_urut = $data['no_urut'];
            $pelaksana->id_rkpd = $id;
            $pelaksana->id_musrencam = 0;
            $pelaksana->uraian_aktivitas_kegiatan = $rkpd->uraian_kegiatan_renstra;
            $pelaksana->pagu_aktivitas_forum = $data['pagu_aktivitas'];
            $pelaksana->pagu_aktivitas_sebelumnya = $data['pagu_aktivitas_sebelumnya'];
            $pelaksana->id_sub_unit = $data['id_sub_unit'];
            $pelaksana->id_aktivitas_kegiatan = 0;
            isset($data['status_musren_aktivitas']) ? $pelaksana->sumber_usulan = 1 : $pelaksana->sumber_usulan = 0;
            if($pelaksana->save()) return redirect('rancanganrkpd/btl/');
        }

        $subUnitAll = \App\RefSubUnit::where(['id_unit' => $rkpd->id_unit])->get();
        $subUnitDropdown = NULL;
        foreach ($subUnitAll as $unitAll) {
            $subUnitDropdown[$unitAll->id_sub_unit] = $unitAll->id_sub_unit.'.'.$unitAll->nm_sub;
        }        

        return view('ranhirrkpd._formrenjapelaksana')->with(compact('title', 'subUnitDropdown', 'referrer'))->render();
    }

    public function btlpelaksanadelete(Request $request, Builder $htmlBuilder, $id, $unit_id)
    {
        $rkpd = \App\TrxRkpdRanhirPelaksana::destroy($unit_id);
        return redirect('rancanganrkpd/btl/');
    }

    public function btlpelaksanabelanja(Request $request, Builder $htmlBuilder, $id, $sub_unit_id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();
        $pelaksana = \App\TrxRkpdRanhirPelaksana::where('id_pelaksana_rkpd', $sub_unit_id)->first();
        $belanja = \App\TrxRkpdRanhirBelanja::where('id_pelaksana_rkpd', $sub_unit_id)->get();
        $lokasi = \App\TrxRkpdRanhirLokasi::where('id_pelaksana_rkpd', $sub_unit_id)->get();
        
        return view('ranhirrkpd.belanja')->with(compact('pelaksana', 'rkpd', 'belanja', 'title', 'lokasi'))->render();
    }

    public function btlpelaksanabelanjatambah(Request $request, $id, $sub_unit_id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();
        $pelaksana = \App\TrxRkpdRanhirPelaksana::where('id_pelaksana_rkpd', $sub_unit_id)->first();

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
            $belanja = new \App\TrxRkpdRanhirBelanja;
            // generate uraian_belanja from input
            $uraian_belanja = \App\Models\RefSshRekening::where(['id_rekening_ssh' => $data['id_rekening_ssh']])->first()['rekening']['nama_kd_rek_5'];
            $belanja->no_urut = $data['no_urut'];
            $belanja->tahun_rkpd = $rkpd->tahun_rkpd;
            $belanja->id_pelaksana_rkpd = $sub_unit_id;
            $belanja->id_rekening_ssh = $data['id_rekening_ssh'];
            $belanja->uraian_belanja = $uraian_belanja; //ambil dari refrek5
            $belanja->volume_belanja = $data['volume_belanja'];
            $belanja->id_satuan = $data['id_satuan'];
            $belanja->koefisien = $data['koefisien'];
            $belanja->harga_satuan = $data['harga_satuan'];
            $belanja->jml_belanja = $data['harga_satuan'] * $data['volume_belanja'];
            if($belanja->save()) return redirect('rancanganrkpd/btl/');
        }

        return view('ranhirrkpd._formrenjabelanja')->with(compact('title', 'renja', 'sshRekeningDropdown', 'satuanDropdown', 'referrer'))->render();
    }

    public function btlpelaksanabelanjaubah(Request $request, $id, $sub_unit_id, $belanja_id)
    {
        $title = ['Belanja Tidak Langsung', 'btl'];
        $rkpd = \App\TrxRkpdRanhir::where('id_rkpd', $id)->first();
        $pelaksana = \App\TrxRkpdRanhirPelaksana::where('id_pelaksana_rkpd', $sub_unit_id)->first();

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
        $model = \App\TrxRenjaRancanganBelanja::where(['id_belanja_renja' => $belanja_id]);
        
        IF($request->all()){
            $data = $request->all();
            $belanja = $model;
            // generate uraian_belanja from input
            $uraian_belanja = \App\Models\RefSshRekening::where(['id_rekening_ssh' => $data['id_rekening_ssh']])->first()['rekening']['nama_kd_rek_5'];
            $belanja->no_urut = $data['no_urut'];
            $belanja->tahun_rkpd = $rkpd->tahun_rkpd;
            $belanja->id_pelaksana_rkpd = $sub_unit_id;
            $belanja->id_rekening_ssh = $data['id_rekening_ssh'];
            $belanja->uraian_belanja = $uraian_belanja; //ambil dari refrek5
            $belanja->volume_belanja = $data['volume_belanja'];
            $belanja->id_satuan = $data['id_satuan'];
            $belanja->koefisien = $data['koefisien'];
            $belanja->harga_satuan = $data['harga_satuan'];
            $belanja->jml_belanja = $data['harga_satuan'] * $data['volume_belanja'];
            if($belanja->save()) return redirect('rancanganrkpd/btl/');
        }

        return view('renja._formrenjabelanja')->with(compact('title', 'renja', 'sshRekeningDropdown', 'satuanDropdown', 'model', 'referrer'))->render();
    }       
}
