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

use app\Models\RefDesa;
use App\Models\TrxMusrenbangKecamatan;
use App\Models\TrxMusrenbangKecamatanLokasi;



class TrxMusrenbangCamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getLoadData($id_kecamatan)
    {
        $getDataDesa = DB::SELECT('SELECT (@id:=@id+1) as urut,a.*, b.uraian_aktivitas_kegiatan as uraian_asb, c.kd_desa, c.nama_desa, 
                    d.uraian_satuan, c.id_kecamatan,d.uraian_satuan, e.uraian_satuan as uraian_satuan_desa,h.nama_kecamatan,
                    CASE a.status_usulan
                          WHEN 0 THEN "fa fa-question"
                          WHEN 1 THEN "fa fa-check-square-o"
                      END AS status_icon,
                      CASE a.status_usulan
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna,COALESCE(g.jml_lokasi,0) as jml_lokasi  
                    FROM trx_musrencam AS a
                    LEFT OUTER JOIN (SELECT x.id_renja,z.id_aktivitas_renja,z.uraian_aktivitas_kegiatan,z.id_aktivitas_asb
                    FROM trx_renja_rancangan AS x
                    INNER JOIN trx_renja_rancangan_pelaksana AS y ON y.id_renja = x.id_renja
                    INNER JOIN trx_renja_rancangan_aktivitas AS z ON z.id_renja = y.id_pelaksana_renja) AS b 
                    ON a.id_asb_aktivitas = b.id_aktivitas_asb AND a.id_kegiatan = b.id_aktivitas_renja
                    LEFT OUTER JOIN trx_musrendes f ON a.id_usulan_desa = f.id_musrendes
                    LEFT OUTER JOIN ref_desa AS c ON f.id_desa = c.id_desa                    
                    INNER JOIN ref_satuan AS d ON a.id_satuan = d.id_satuan
                    LEFT OUTER JOIN ref_satuan AS e ON a.id_satuan_desa = e.id_satuan
                    LEFT OUTER JOIN  (SELECT id_musrencam, count(id_musrencam) as jml_lokasi
                    FROM trx_musrencam_lokasi WHERE volume > 0 GROUP BY id_musrencam) AS g
                    ON a.id_musrencam =g.id_musrencam
                    INNER JOIN ref_kecamatan as h ON a.id_kecamatan = h.id_kecamatan, 
                    (SELECT @id:=0) z WHERE a.sumber_usulan=0 and a.tahun_musren='.Session::get('tahun').' and a.id_kecamatan='.$id_kecamatan);

        return DataTables::of($getDataDesa)
            ->addColumn('details_url', function($getDataDesa) {
                    return url('musrencam/getLokasi/'.$getDataDesa->id_musrencam);
                })
            ->addColumn('action',function($getDataDesa){
                if($getDataDesa->status_usulan == 0)
                return '
                    <button id="btnUnLoadMusren" class="btn btn-danger btn-labeled" data-toggle="tooltip" title="Un-Load Lokasi"><span class="btn-label"><i class="fa fa-reply fa-fw fa-lg"></i></span> Un-Load Data</button>
                    ' ;              

            })
        ->make(true);
    }

    public function getLokasi($id_musrencam)
    {
        $getDataDesa = DB::SELECT('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_musren,a.no_urut,a.id_musrencam,
            a.id_lokasi_musrencam,a.id_lokasi,a.id_desa,a.rt,a.rw,a.uraian_kondisi,a.file_foto,a.lat,a.lang,a.id_musrendes,
            a.sumber_data,a.volume_desa,a.volume,c.kd_desa,c.nama_desa,b.status_usulan,CONCAT(a.rt,"/",a.rw) as rtrw,b.id_kecamatan,
            CASE a.sumber_data
                  WHEN 0 THEN "Usulan Desa"
                  WHEN 1 THEN "Musrenbang Kecamatan"
            END AS sumber_display 
            FROM trx_musrencam_lokasi AS a
            LEFT OUTER JOIN trx_musrencam AS b ON a.id_musrencam = b.id_musrencam
            LEFT OUTER JOIN ref_desa AS c ON a.id_desa = c.id_desa) a, 
            (SELECT @id:=0) z WHERE a.id_musrencam='.$id_musrencam);

        return DataTables::of($getDataDesa)
        ->make(true);
    }

    public function unLoadData(Request $req){

        try{
            TrxMusrenbangKecamatan::destroy($req->id_musrencam);
            return response ()->json (['pesan'=>'Data Berhasil un-Load','status_pesan'=>'1']);
        }
        catch(QueryException $e){
            $error_code = $e->errorInfo[1] ;
            return response ()->json (['pesan'=>'Data Gagal un-Load ('.$error_code.')','status_pesan'=>'0']);
        }

    }

    public function getData($id_kecamatan)
    {
        $getDataDesa = DB::SELECT('SELECT (@id:=@id+1) as urut,a.*, b.uraian_aktivitas_kegiatan as uraian_asb, c.kd_desa, c.nama_desa, 
                    d.uraian_satuan, c.id_kecamatan,d.uraian_satuan, e.uraian_satuan as uraian_satuan_desa,h.nama_kecamatan,
                    CASE a.status_usulan
                          WHEN 0 THEN "fa fa-question"
                          WHEN 1 THEN "fa fa-check-square-o"
                      END AS status_icon,
                      CASE a.status_usulan
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna,COALESCE(g.jml_lokasi,0) as jml_lokasi  
                    FROM trx_musrencam AS a
                    LEFT OUTER JOIN trx_renja_rancangan_aktivitas AS b 
                    ON a.id_renja = b.id_renja AND a.id_asb_aktivitas = b.id_aktivitas_asb
                    LEFT OUTER JOIN trx_musrendes f ON a.id_usulan_desa = f.id_musrendes
                    LEFT OUTER JOIN ref_desa AS c ON f.id_desa = c.id_desa                    
                    INNER JOIN ref_satuan AS d ON a.id_satuan = d.id_satuan
                    LEFT OUTER JOIN ref_satuan AS e ON a.id_satuan_desa = e.id_satuan
                    LEFT OUTER JOIN  (SELECT id_musrencam, count(id_musrencam) as jml_lokasi
                    FROM trx_musrencam_lokasi WHERE volume > 0 GROUP BY id_musrencam) AS g
                    ON a.id_musrencam =g.id_musrencam
                    INNER JOIN ref_kecamatan as h ON a.id_kecamatan = h.id_kecamatan, 
                    (SELECT @id:=0) z WHERE a.tahun_musren='.Session::get('tahun').' and a.id_kecamatan='.$id_kecamatan);

        return DataTables::of($getDataDesa)
            ->addColumn('details_url', function($getDataDesa) {
                    return url('musrencam/getLokasiData/'.$getDataDesa->id_musrencam);
                })
            ->addColumn('action',function($getDataDesa){
                if($getDataDesa->status_usulan == 0 && $getDataDesa->sumber_usulan != 2)
                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnAddLokasiUsulan" class="dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Lokasi</a>
                        </li>
                        <li>
                            <a id="btnEditUsulanKec" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit Usulan Kecamatan</a>
                        </li>
                        <li>
                            <a id="btnPostUsulanKec" class="dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Usulan Kecamatan</a>
                        </li>
                    </ul>
                    </div>
                    ' ;
                if($getDataDesa->status_usulan == 0 && $getDataDesa->sumber_usulan == 2)
                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnAddLokasiUsulan" class="dropdown-item"><i class="fa fa-plus fa-fw fa-lg"></i> Tambah Lokasi</a>
                        </li>
                        <li>
                            <a id="btnEditUsulanKec" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit Usulan Kecamatan</a>
                        </li>
                        <li>
                            <a id="btnHapusMusrenCam" class="dropdown-item"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Usulan Kecamatan</a>
                        </li>
                        <li>
                            <a id="btnPostUsulanKec" class="dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Usulan Desa</a>
                        </li>
                    </ul>
                    </div>
                    ' ;
                // if($getDataDesa->status_usulan == 1 && $getDataDesa->cekData == 0)
                if($getDataDesa->status_usulan == 1)
                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnEditUsulanKec" class="dropdown-item"><i class="fa fa-eye fa-fw fa-lg"></i> Lihat Usulan Kecamatan</a>
                        </li>
                        <li>
                            <a id="btnPostUsulanKec" class="dropdown-item"><i class="fa fa-times fa-fw fa-lg"></i> Un-Posting Usulan Desa</a>
                        </li>
                    </ul>
                    </div>
                    ' ;
                // if($getDataDesa->status_usulan == 1 && $getDataDesa->cekData != 0)
                // return '
                //     <div class="btn-group">
                //     <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                //     <ul class="dropdown-menu dropdown-menu-right">
                //         <li>
                //             <a id="btnEditUsulanDesa" class="dropdown-item"><i class="fa fa-eye fa-fw fa-lg"></i> Lihat Usulan Desa</a>
                //         </li>
                //     </ul>
                //     </div>
                //     ' ;

            })
        ->make(true);
    }

    public function getLokasiData($id_musrencam)
    {
        $getDataDesa = DB::SELECT('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_musren,a.no_urut,a.id_musrencam, d.nama_lokasi,
            a.id_lokasi_musrencam,a.id_lokasi,a.id_desa,a.rt,a.rw,a.uraian_kondisi,a.file_foto,a.lat,a.lang,a.id_musrendes,
            a.sumber_data,a.volume_desa,a.volume,c.kd_desa,c.nama_desa,b.status_usulan,CONCAT(a.rt,"/",a.rw) as rtrw,b.id_kecamatan,
            CASE a.sumber_data
                WHEN 0 THEN "Usulan Desa"
                WHEN 1 THEN "Musrenbang Kecamatan"
            END AS sumber_display 
            FROM trx_musrencam_lokasi AS a
            LEFT OUTER JOIN trx_musrencam AS b ON a.id_musrencam = b.id_musrencam
            LEFT OUTER JOIN ref_desa AS c ON a.id_desa = c.id_desa
            LEFT OUTER JOIN ref_lokasi AS d ON a.id_lokasi = d.id_lokasi) a, 
            (SELECT @id:=0) z WHERE a.id_musrencam='.$id_musrencam);

        return DataTables::of($getDataDesa)
        ->addColumn('action',function($getDataDesa){
                if($getDataDesa->status_usulan == 0 && $getDataDesa->sumber_data !=0)
                return '
                    <button id="btnEditLokasiMusren" class="btn btn-warning" data-toggle="tooltip" title="Edit Lokasi"><i class="fa fa-pencil fa-fw fa-lg"></i></button>
                    <button id="btnHapusLokasiMusren" class="btn btn-danger" data-toggle="tooltip" title="Hapus Lokasi"><i class="fa fa-trash fa-fw fa-lg"></i></button>
                    ' ;
                if($getDataDesa->status_usulan == 0 && $getDataDesa->sumber_data ==0)
                return '
                    <button id="btnEditLokasiMusren" class="btn btn-warning" data-toggle="tooltip" title="Edit Lokasi"><i class="fa fa-pencil fa-fw fa-lg"></i></button>
                    ' ;
                if($getDataDesa->status_usulan == 1)
                return '
                    <button id="btnEditLokasiMusren" class="btn btn-warning" data-toggle="tooltip" title="Edit Lokasi"><i class="fa fa-pencil fa-fw fa-lg"></i></button>
                    ' ;

            })
        ->make(true);
    }

    public function importData(Request $req){
        $importData=DB::INSERT('INSERT INTO trx_musrencam(tahun_musren,no_urut,id_renja,id_kecamatan,id_kegiatan,
            id_asb_aktivitas,uraian_aktivitas_kegiatan,uraian_kondisi,tolak_ukur_aktivitas,target_output_aktivitas,id_satuan,
            id_satuan_desa,volume,volume_desa,harga_satuan,harga_satuan_desa,jml_pagu,jml_pagu_desa,id_usulan_desa,pagu_aktivitas,
            sumber_usulan,status_usulan,status_pelaksanaan)
            SELECT a.* FROM (SELECT a.tahun_renja,(@id:=@id+1) as no_urut,a.id_renja,b.id_kecamatan,a.id_kegiatan,a.id_asb_aktivitas,
            a.uraian_aktivitas_kegiatan,a.uraian_kondisi,a.tolak_ukur_aktivitas,a.target_output_aktivitas,a.id_satuan,
            a.id_satuan as id_satuan_desa, a.volume,a.volume as volume_desa,a.harga_satuan,a.harga_satuan as harga_satuan_desa,
            a.jml_pagu,a.jml_pagu as jml_pagu_desa,a.id_musrendes,a.pagu_aktivitas, 0 as sumber_usulan_kec, 0 as status_usulan_kec, 
            0 as status_pelaksanaan_kec FROM trx_musrendes AS a
            INNER JOIN ref_desa as b ON a.id_desa = b.id_desa
            LEFT OUTER JOIN trx_musrencam AS c ON a.id_musrendes = c.id_usulan_desa,
            (SELECT @id:=0) z WHERE a.status_usulan=1 AND c.id_usulan_desa is null and b.id_desa ='.$req->id_desa.' 
            and b.id_kecamatan ='.$req->id_kecamatan.' and a.tahun_renja='.Session::get('tahun').') a');

        if($importData!=0){            
            $importLokasi=DB::INSERT('INSERT INTO trx_musrencam_lokasi(tahun_musren,no_urut,id_musrencam,id_lokasi,id_desa,
                rt,rw,uraian_kondisi,file_foto,lat,lang,id_musrendes,sumber_data,volume_desa,volume,id_lokasi_musrendes)
                SELECT y.* FROM (SELECT a.tahun_musren,(@id:=@id+1) as no_urut,c.id_musrencam,COALESCE((SELECT DISTINCT x.id_lokasi FROM ref_lokasi AS x 
                WHERE x.id_desa = a.id_desa AND x.jenis_lokasi = 0),0) AS id_lokasi,a.id_desa,a.rt,
                a.rw,a.uraian_kondisi,a.file_foto,a.lat,a.lang,a.id_musrendes, 0 as sumber_data_kec,a.volume_desa,
                a.volume_desa as volume_kec, a.id_lokasi_musrendes FROM trx_musrendes_lokasi AS a 
                INNER JOIN trx_musrencam c ON a.id_musrendes = c.id_usulan_desa
                LEFT OUTER JOIN trx_musrencam_lokasi as b ON a.id_musrendes = b.id_musrendes and a.id_lokasi_musrendes = b.id_lokasi_musrendes, 
                (SELECT @id:=0) z  WHERE b.id_musrendes is null and c.id_kecamatan ='.$req->id_kecamatan.' and a.id_desa='.$req->id_desa.') y WHERE y.tahun_musren='.Session::get('tahun'));

                if($importLokasi != 0){ 
                    return response ()->json (['pesan'=>'Data Berhasil Import Data','status_pesan'=>'1']);
                } else {
                    return response ()->json (['pesan'=>'Data Gagal Import Lokasi Data','status_pesan'=>'0']);
                }
        } else {
            return response ()->json (['pesan'=>'Data Gagal Import Data','status_pesan'=>'0']);
        }

    }

    public function addMusrenCamLokasi(Request $req){
    $idlokasi = DB::SELECT('SELECT DISTINCT x.id_lokasi FROM ref_lokasi AS x 
        WHERE x.id_desa ='.$req->id_desa.' AND x.jenis_lokasi = 0');

    try{
        $data = new TrxMusrenbangKecamatanLokasi();
        $data->tahun_musren= $req->tahun_musren;
        $data->no_urut= $req->no_urut;
        $data->id_musrencam= $req->id_musrencam;
        $data->id_lokasi= $idlokasi[0]->id_lokasi;
        $data->id_desa= $req->id_desa;
        $data->rt= $req->rt;
        $data->rw= $req->rw;
        $data->uraian_kondisi= $req->uraian_kondisi;
        $data->id_musrendes= $req->id_musrendes;
        $data->sumber_data= 1;
        $data->volume_desa= $req->volume_desa;
        $data->volume= $req->volume;
        $data->save (['timestamps' => false]);
    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function editMusrenCamLokasi(Request $req){
    $idlokasi = DB::SELECT('SELECT DISTINCT x.id_lokasi FROM ref_lokasi AS x 
        WHERE x.id_desa ='.$req->id_desa.' AND x.jenis_lokasi = 0');

    try{
        $data = TrxMusrenbangKecamatanLokasi::find($req->id_lokasi_musrencam);
        $data->tahun_musren= $req->tahun_musren;
        $data->no_urut= $req->no_urut;
        $data->id_musrencam= $req->id_musrencam;
        $data->id_lokasi= $idlokasi[0]->id_lokasi;
        $data->id_desa= $req->id_desa;
        $data->rt= $req->rt;
        $data->rw= $req->rw;
        $data->uraian_kondisi= $req->uraian_kondisi;
        $data->id_musrendes= $req->id_musrendes;
        $data->volume_desa= $req->volume_desa;
        $data->volume= $req->volume;
        $data->save (['timestamps' => false]);
    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function hapusMusrenCamLokasi(Request $req){

    try{
        TrxMusrenbangKecamatanLokasi::destroy($req->id_lokasi_musrencam);
        return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal dihapus ('.$error_code.')','status_pesan'=>'0']);
    }

}

public function addMusrenCam(Request $req){
    try{
        $data = new TrxMusrenbangKecamatan();
        $data->tahun_musren= $req->tahun_musren;
        $data->no_urut= $req->no_urut;
        $data->id_renja= $req->id_renja;
        $data->id_kecamatan= $req->id_kecamatan;
        $data->id_kegiatan= $req->id_kegiatan;
        $data->id_asb_aktivitas= $req->id_asb_aktivitas;
        $data->uraian_aktivitas_kegiatan= $req->uraian_aktivitas_kegiatan;
        $data->uraian_kondisi= $req->uraian_kondisi;
        $data->tolak_ukur_aktivitas= $req->tolak_ukur_aktivitas;
        $data->target_output_aktivitas= $req->target_output_aktivitas;
        $data->id_satuan= $req->id_satuan;
        $data->id_satuan_desa= Null;
        $data->volume= $req->volume;
        $data->volume_desa= 0;
        $data->harga_satuan= $req->harga_satuan;
        $data->harga_satuan_desa= 0;
        $data->jml_pagu= $req->jml_pagu;
        $data->jml_pagu_desa= 0;
        $data->id_usulan_desa= Null;
        $data->pagu_aktivitas= $req->jml_pagu;
        $data->sumber_usulan= 2;
        $data->status_usulan= 0;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->save (['timestamps' => false]);

    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function editMusrenCam(Request $req){
    try{
        $data = TrxMusrenbangKecamatan::find($req->id_musrencam);
        $data->tahun_musren= $req->tahun_musren;
        $data->no_urut= $req->no_urut;
        $data->id_renja= $req->id_renja;
        $data->id_kegiatan= $req->id_kegiatan;
        $data->id_asb_aktivitas= $req->id_asb_aktivitas;
        $data->uraian_aktivitas_kegiatan= $req->uraian_aktivitas_kegiatan;
        $data->uraian_kondisi= $req->uraian_kondisi;
        $data->tolak_ukur_aktivitas= $req->tolak_ukur_aktivitas;
        $data->target_output_aktivitas= $req->target_output_aktivitas;
        $data->id_satuan= $req->id_satuan;
        $data->volume= $req->volume;
        $data->harga_satuan= $req->harga_satuan;
        $data->jml_pagu= $req->jml_pagu;
        $data->pagu_aktivitas= $req->jml_pagu;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->save (['timestamps' => false]);
    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function postMusrenCam(Request $req){    
    try{
        if($req->status_usulan==0){
                    $status = 'Un-Posting';
                } else {
                    $status = 'Posting';
                }
        $data = TrxMusrenbangKecamatan::find($req->id_musrencam);
        $data->status_usulan = $req->status_usulan ;    
        $data->save (['timestamps' => false]);
        return response ()->json (['pesan'=>'Status Berhasil '.$status.'','status_pesan'=>'1']);
    }
        catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Status Gagal '.$status.'','status_pesan'=>'0']);
    }
}

public function hapusMusrenCam(Request $req){

    try{
        // TrxMusrenbangKecamatan::where('id_musrencam',$req->id_musrencam)->delete ();
        TrxMusrenbangKecamatan::destroy($req->id_musrencam);
        return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal dihapus ('.$error_code.')','status_pesan'=>'0']);
    }

}

    public function loadData()
    {
        // if(Auth::check()){      
            return view('musrencam.load');
        // } else {
            // return view ( 'errors.401' );
        // } 
    }

    public function postingData()
    {
        // if(Auth::check()){      
            return view('musrencam.posting');
        // } else {
            // return view ( 'errors.401' );
        // } 
    }

    public function index(Request $request, Builder $htmlBuilder)
    {  
        // if(Auth::check()){      
            return view('musrencam.index');
        // } else {
            // return view ( 'errors.401' );
        // } 
    }

}
