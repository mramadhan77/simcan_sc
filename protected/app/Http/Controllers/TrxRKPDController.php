<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Auth;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
// use App\TrxForumSkpd;



class TrxRKPDController extends Controller
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

        return view('rkpd.load')->with(compact('unit'));
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
        
        // $trxforumskpd = TrxForumSkpd::select()->get();
        
        // return view('rkpd.index')->with(compact('trxforumskpd'));
        return view('rkpd.dashboard');
    }

    public function blangsung(Request $request, Builder $htmlBuilder)
    {
        
        return view('rkpd.blangsung');
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
}
