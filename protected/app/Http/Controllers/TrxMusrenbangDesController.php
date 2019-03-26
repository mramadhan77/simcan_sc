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
use App\Models\TrxMusrenbangDesa;
use App\Models\TrxMusrenbangDesaLokasi;



class TrxMusrenbangDesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getData($id_desa)
    {
        $getDataDesa = DB::SELECT('SELECT (@id:=@id+1) as urut,a.*, b.uraian_aktivitas_kegiatan as uraian_asb, c.kd_desa, c.nama_desa, 
                    d.uraian_satuan, c.id_kecamatan,COALESCE(e.jml_lokasi,0) as jml_lokasi, d.uraian_satuan, e.uraian_satuan as uraian_satuan_rw,
                    CASE a.status_usulan
                          WHEN 0 THEN "fa fa-question"
                          WHEN 1 THEN "fa fa-check-square-o"
                      END AS status_icon,
                      CASE a.status_usulan
                          WHEN 0 THEN "red"
                          WHEN 1 THEN "green"
                      END AS warna, COALESCE(f.cekData,0) as cekData   
                    FROM trx_musrendes AS a
                    LEFT OUTER JOIN  (SELECT x.id_renja,z.id_aktivitas_renja,z.uraian_aktivitas_kegiatan,z.id_aktivitas_asb
                    FROM trx_renja_rancangan AS x
                    INNER JOIN trx_renja_rancangan_pelaksana AS y ON y.id_renja = x.id_renja
                    INNER JOIN trx_renja_rancangan_aktivitas AS z ON z.id_renja = y.id_pelaksana_renja) AS b 
                    ON a.id_asb_aktivitas = b.id_aktivitas_asb AND a.id_kegiatan = b.id_aktivitas_renja
                    INNER JOIN ref_desa AS c ON a.id_desa = c.id_desa                    
                    INNER JOIN ref_satuan AS d ON a.id_satuan = d.id_satuan
                    LEFT OUTER JOIN ref_satuan AS e ON a.id_satuan_rw = e.id_satuan
                    LEFT OUTER JOIN  (SELECT id_musrendes, count(id_musrendes) as jml_lokasi
                    FROM trx_musrendes_lokasi WHERE volume_desa > 0 GROUP BY id_musrendes,volume_desa) AS e
                    ON a.id_musrendes = e.id_musrendes
                    LEFT OUTER JOIN (SELECT id_musrendes, COUNT(id_musrendes) as cekData from trx_musrencam_lokasi 
                    GROUP BY id_musrendes) f ON a.id_musrendes = f.id_musrendes, 
                    (SELECT @id:=0) z WHERE a.tahun_renja='.Session::get('tahun').' and a.id_desa='.$id_desa);

        return DataTables::of($getDataDesa)
            ->addColumn('details_url', function($getDataDesa) {
                    return url('musrendes/getLokasi/'.$getDataDesa->id_musrendes);
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
                            <a id="btnEditUsulanDesa" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit Usulan Desa</a>
                        </li>
                        <li>
                            <a id="btnPostUsulanDesa" class="dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Usulan Desa</a>
                        </li>
                        <li>
                            <a id="btnUnloadData" class="dropdown-item"><i class="fa fa-reply fa-fw fa-lg"></i> Unload Usulan Desa</a>
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
                            <a id="btnEditUsulanDesa" class="dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Edit Usulan Desa</a>
                        </li>
                        <li>
                            <a id="btnHapusMusrendes" class="dropdown-item"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus Usulan Desa</a>
                        </li>
                        <li>
                            <a id="btnPostUsulanDesa" class="dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Usulan Desa</a>
                        </li>
                    </ul>
                    </div>
                    ' ;
                if($getDataDesa->status_usulan == 1 && $getDataDesa->cekData == 0)
                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnEditUsulanDesa" class="dropdown-item"><i class="fa fa-eye fa-fw fa-lg"></i> Lihat Usulan Desa</a>
                        </li>
                        <li>
                            <a id="btnPostUsulanDesa" class="dropdown-item"><i class="fa fa-times fa-fw fa-lg"></i> Un-Posting Usulan Desa</a>
                        </li>
                    </ul>
                    </div>
                    ' ;
                if($getDataDesa->status_usulan == 1 && $getDataDesa->cekData != 0)
                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a id="btnEditUsulanDesa" class="dropdown-item"><i class="fa fa-eye fa-fw fa-lg"></i> Lihat Usulan Desa</a>
                        </li>
                    </ul>
                    </div>
                    ' ;

            })
        ->make(true);
    }

public function getLokasi($id_musrendes)
    {
        $getDataDesa = DB::SELECT('SELECT (@id:=@id+1) as urut, a.* FROM (SELECT a.tahun_musren, a.no_urut, a.id_musrendes,
                            a.id_lokasi_musrendes, a.id_lokasi, a.id_desa, a.rt, a.rw, a.uraian_kondisi, a.file_foto,
                            a.lat, a.lang, a.sumber_data, a.volume_rw, a.volume_desa, b.status_usulan,
                            b.status_pelaksanaan,
                            CASE a.sumber_data
                                  WHEN 0 THEN "Usulan RW"
                                  WHEN 1 THEN "Musrenbang Desa"
                            END AS sumber_display 
                        FROM trx_musrendes_lokasi AS a
                        INNER JOIN trx_musrendes AS b ON a.id_musrendes = b.id_musrendes) a, 
                        (SELECT @id:=0) z WHERE a.id_musrendes='.$id_musrendes);

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

public function unLoadData(Request $req){
    $result_lokasi = DB::DELETE('delete from trx_musrendes where id_musrendes='.$req->id_musrendes);
    if ($result_lokasi != 0) {
        $result_usulan = DB::DELETE('delete from trx_musrendes_lokasi where id_musrendes='.$req->id_musrendes);
        if ($result_usulan != 0){
            return response ()->json (['pesan'=>'Data Berhasil Un-Load','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal Un-Load (err:2)','status_pesan'=>'0']);
        }
    } else {
        return response ()->json (['pesan'=>'Data Gagal Un-Load (err:1)','status_pesan'=>'0']);
    }
}


public function ImportDataRW(Request $req)
    {       
        $TransferRW=DB::INSERT('INSERT INTO trx_musrendes(tahun_renja, no_urut,id_renja,id_desa,id_kegiatan,id_asb_aktivitas,uraian_aktivitas_kegiatan,uraian_kondisi,tolak_ukur_aktivitas,target_output_aktivitas,id_satuan,id_satuan_rw,volume,volume_rw,harga_satuan,harga_satuan_rw,jml_pagu,jml_pagu_rw,id_usulan_rw,pagu_aktivitas,sumber_usulan,status_usulan)
                SELECT y.* FROM (SELECT a.tahun_musren,(@id:=@id+1) as no_urut,a.id_renja,a.id_desa,a.id_kegiatan,a.id_asb_aktivitas,a.uraian_aktivitas_kegiatan,a.uraian_kondisi,Null as tolak_ukur,0 as target_ouput, a.id_satuan,a.id_satuan as id_satuan_rw,a.volume,a.volume as volume_rw,a.harga_satuan,a.harga_satuan as harga_satuan_rw,a.jml_pagu,a.jml_pagu as jml_pagu_rw,a.id_musrendes_rw,0 as pagu_aktivitas, 1 as sumber_usulan,0 as status_usulan_des 
                FROM trx_musrendes_rw AS a
                LEFT OUTER JOIN trx_musrendes AS b ON a.id_musrendes_rw = b.id_usulan_rw, (SELECT @id:=0) z WHERE a.status_usulan=1 AND b.id_usulan_rw is null) y
                WHERE y.id_desa ='.$req->id_desa.' and y.tahun_musren='.Session::get('tahun'));
    
        if($TransferRW != 0){          
            $TransferLokasiRW=DB::INSERT('INSERT INTO trx_musrendes_lokasi(tahun_musren,no_urut,id_musrendes,id_lokasi,id_desa,rt,rw,uraian_kondisi,sumber_data,volume_rw,volume_desa)
                SELECT y.* FROM (SELECT a.tahun_musren,(@id:=@id+1) as no_urut,b.id_musrendes, a.id_desa as id_lokasi,a.id_desa,a.rt,a.rw,a.uraian_kondisi,0 as sumber_data,a.volume,a.volume as volume_desa 
                FROM trx_musrendes_rw AS a 
                INNER JOIN trx_musrendes b ON a.id_musrendes_rw=b.id_usulan_rw
                LEFT OUTER JOIN trx_musrendes_lokasi c ON b.id_musrendes = c.id_musrendes, 
                (SELECT @id:=0) z WHERE a.status_usulan=1 AND c.id_musrendes is null) y
                WHERE y.id_desa ='.$req->id_desa.' and y.tahun_musren='.Session::get('tahun'));
                if($TransferLokasiRW != 0){ 
                    return response ()->json (['pesan'=>'Data Berhasil Import Data','status_pesan'=>'1']);
                } else {
                    return response ()->json (['pesan'=>'Data Gagal Import Lokasi Data (err:2)','status_pesan'=>'0']);
                }
        } else {
            return response ()->json (['pesan'=>'Data Gagal Import Data (err:1)','status_pesan'=>'0']);
        }
    }

public function getDataASB()
    {
        $getDataRW = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_aktivitas_asb,b.pagu_rata2, a.nm_aktivitas_asb, 
                d.nm_unit, c.id_unit, c.id_renja, b.id_aktivitas_renja, c.uraian_kegiatan_renstra,
                                CASE b.id_satuan_publik 
                                    WHEN 0 THEN b.id_satuan_1
                                    WHEN 1 THEN b.id_satuan_2
                                END AS id_satuan_musren,
                                CASE b.id_satuan_publik 
                                    WHEN 0 THEN g.uraian_satuan
                                    WHEN 1 THEN h.uraian_satuan
                                END AS nm_satuan_musren
                FROM trx_asb_aktivitas AS a 
                INNER JOIN (SELECT a.* FROM trx_renja_ranwal_aktivitas a WHERE a.status_musren = 1 ) AS b 
                ON a.id_aktivitas_asb = b.id_aktivitas_asb
                INNER JOIN trx_renja_ranwal_pelaksana AS f ON b.id_renja = f.id_pelaksana_renja
                INNER JOIN trx_renja_ranwal_kegiatan AS c ON f.id_renja = c.id_renja
                INNER JOIN trx_renja_ranwal_program AS i ON c.id_renja_program = i.id_renja_program
                INNER JOIN ref_unit AS d ON c.id_unit = d.id_unit
                LEFT OUTER JOIN ref_satuan AS g ON b.id_satuan_1 = g.id_satuan
                LEFT OUTER JOIN ref_satuan AS h ON b.id_satuan_2 = g.id_satuan, (SELECT @id:=0) z
                WHERE i.status_data=2 and b.tahun_renja ='.Session::get('tahun'));

        return DataTables::of($getDataRW)
            ->addColumn('action',function($getDataRW){
                return '
                    <button id="btnPilihASB" type="button" class="btn btn-info btn-labeled"><span class="btn-label"><i class="fa fa-check fa-fw fa-lg"></i></span>Pilih Aktivitas</button>
                    ' ;
            })
        ->make(true);
    }

public function getHitungASB($id_asb,$id_zona,$vol1,$vol2){
    $getHitung=DB::SELECT('SELECT a.id_aktivitas_asb,a.nm_aktivitas_asb,sum(a.jml_pagu) as jml_pagu_asb FROM
                    (SELECT a.id_aktivitas_asb,a.nm_aktivitas_asb,a.volume_1,a.id_satuan_1,a.volume_2,a.id_satuan_2,a.range_max,a.kapasitas_max,
                    a.range_max1,a.kapasitas_max1,c.koefisien1,c.koefisien2,c.koefisien3,c.harga_satuan,
                    PaguASB(c.jenis_biaya,c.hub_driver,'.$vol1.','.$vol2.',a.range_max,a.range_max1,a.kapasitas_max,a.kapasitas_max1,c.koefisien1,c.koefisien2,c.koefisien3,c.harga_satuan) AS jml_pagu
                    FROM trx_asb_aktivitas AS a
                    INNER JOIN trx_renja_rancangan_aktivitas AS b ON a.id_aktivitas_asb = b.id_aktivitas_asb
                    INNER JOIN (SELECT a.id_komponen_asb_rinci,a.id_komponen_asb,a.id_aktivitas_asb,a.id_asb_sub_sub_kelompok,a.id_tarif_ssh,a.id_zona,a.harga_satuan,b.jenis_biaya,b.hub_driver,b.koefisien1,b.id_satuan1,b.koefisien2,b.id_satuan2,b.koefisien3,b.id_satuan3 
                    FROM trx_asb_perhitungan_rinci AS a
                    INNER JOIN trx_asb_komponen_rinci AS b ON a.id_komponen_asb_rinci = b.id_komponen_asb_rinci AND a.id_komponen_asb = b.id_komponen_asb
                    INNER JOIN trx_asb_perhitungan AS c ON a.id_perhitungan = c.id_perhitungan where a.id_zona= '.$id_zona.'  and c.flag_aktif = 1) AS c ON a.id_aktivitas_asb = c.id_aktivitas_asb AND a.id_asb_sub_sub_kelompok = c.id_asb_sub_sub_kelompok WHERE a.id_aktivitas_asb='.$id_asb.') a
                    GROUP BY a.id_aktivitas_asb,a.nm_aktivitas_asb');

     return json_encode($getHitung);
}

public function addMusrendes(Request $req){
    try{
        $data = new TrxMusrenbangDesa();
        $data->tahun_renja= $req->tahun_renja;
        $data->no_urut= $req->no_urut;
        $data->id_renja= $req->id_renja;
        $data->id_desa= $req->id_desa;
        $data->id_kegiatan= $req->id_kegiatan;
        $data->id_asb_aktivitas= $req->id_asb_aktivitas;
        $data->uraian_aktivitas_kegiatan= $req->uraian_aktivitas_kegiatan;
        $data->uraian_kondisi= $req->uraian_kondisi;
        $data->tolak_ukur_aktivitas= $req->tolak_ukur_aktivitas;
        $data->target_output_aktivitas= $req->target_output_aktivitas;
        $data->id_satuan= $req->id_satuan;
        $data->id_satuan_rw= Null;
        $data->volume= $req->volume;
        $data->volume_rw= 0;
        $data->harga_satuan= $req->harga_satuan;
        $data->harga_satuan_rw= 0;
        $data->jml_pagu= $req->jml_pagu;
        $data->jml_pagu_rw= 0;
        $data->id_usulan_rw= Null;
        $data->pagu_aktivitas= $req->pagu_aktivitas;
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

public function editMusrendes(Request $req){
    try{
        $data = TrxMusrenbangDesa::find($req->id_musrendes);
        $data->tahun_renja= $req->tahun_renja;
        $data->no_urut= $req->no_urut;
        $data->id_renja= $req->id_renja;
        $data->id_desa= $req->id_desa;
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
        $data->pagu_aktivitas= $req->pagu_aktivitas;
        $data->status_pelaksanaan= $req->status_pelaksanaan;
        $data->save (['timestamps' => false]);
    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function postMusrendes(Request $req){    
    try{
        if($req->status_usulan==0){
                    $status = 'Un-Posting';
                } else {
                    $status = 'Posting';
                }
        $data = TrxMusrenbangDesa::find($req->id_musrendes);
        $data->status_usulan = $req->status_usulan ;    
        $data->save (['timestamps' => false]);
        return response ()->json (['pesan'=>'Status Berhasil '.$status.'','status_pesan'=>'1']);
    }
        catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Status Gagal '.$status.'','status_pesan'=>'0']);
    }
}

public function hapusMusrendes(Request $req){

    try{
        TrxMusrenbangDesa::destroy($req->id_musrendes);
        return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal dihapus ('.$error_code.')','status_pesan'=>'0']);
    }

}

public function addMusrendesLokasi(Request $req){
    try{
        $data = new TrxMusrenbangDesaLokasi();
        $data->tahun_musren= $req->tahun_musren;
        $data->no_urut= $req->no_urut;
        $data->id_musrendes= $req->id_musrendes;
        $data->id_lokasi= $req->id_lokasi;
        $data->id_desa= $req->id_desa;
        $data->rt= $req->rt;
        $data->rw= $req->rw;
        $data->uraian_kondisi= $req->uraian_kondisi;
        $data->sumber_data= 1;
        $data->volume_rw= $req->volume_rw;
        $data->volume_desa= $req->volume_desa;
        $data->save (['timestamps' => false]);

    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function editMusrendesLokasi(Request $req){
    try{
        $data = TrxMusrenbangDesaLokasi::find($req->id_lokasi_musrendes);        
        $data->tahun_musren= $req->tahun_musren;
        $data->no_urut= $req->no_urut;
        $data->id_musrendes= $req->id_musrendes;
        $data->id_lokasi= $req->id_lokasi;
        $data->id_desa= $req->id_desa;
        $data->rt= $req->rt;
        $data->rw= $req->rw;
        $data->uraian_kondisi= $req->uraian_kondisi;
        $data->volume_rw= $req->volume_rw;
        $data->volume_desa= $req->volume_desa;
        $data->save (['timestamps' => false]);
    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
    }
}

public function hapusMusrendesLokasi(Request $req){

    try{
        TrxMusrenbangDesaLokasi::destroy($req->id_lokasi_musrendes);
        return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal dihapus ('.$error_code.')','status_pesan'=>'0']);
    }

}

public function loadData(){ 
    // if(Auth::check()){      
        return view('musrendes.desa.load');
    // } else {
        // return view ( 'errors.401' );
    // } 
}

public function index(){ 
    // if(Auth::check()){     
        return view('musrendes.desa.index');
    // } else {
        // return view ( 'errors.401' );
    // } 
}



    
}
