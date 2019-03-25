<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\TrxPokir;
use App\Models\TrxPokirUsulan;
use App\Models\TrxPokirLokasi;
use App\Models\TrxPokirTL;
use App\Models\TrxPokirUnit;
use App\CekAkses;
use Auth;



class TrxPokirUnitController extends Controller
{
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

    public function getData($id_tahun,$unit_tl)
    {
        $datapokir=DB::select('SELECT (@id:=@id+1) as no_urut,a.id_pokir_tl,a.id_pokir,a.id_pokir_usulan,a.id_pokir_lokasi,
              a.unit_tl,a.status_tl,a.keterangan_status,COALESCE(e.nama_desa,"Tidak Spesifik") as nama_desa, 
              COALESCE(f.nama_kecamatan,"Tidak Spesifik") as nama_kecamatan,c.id_judul_usulan,d.nama_pengusul,d.id_tahun,COALESCE(c.volume,0) as volume, 
              COALESCE(c.jml_anggaran,0) as jml_anggaran, COALESCE(g.uraian_satuan,"Tidak Spesifik") as uraian_satuan,
              c.diskripsi_usulan,a.status_data,a.id_aktivitas_renja,a.id_aktivitas_forum,a.id_pokir_unit,h.uraian_aktivitas_kegiatan,
              COALESCE(a.volume_tl,0) as volume_tl, COALESCE(a.pagu_tl,0) as pagu_tl,
              CASE a.status_data
                  WHEN 0 THEN "fa fa-question"
                  WHEN 1 THEN "fa fa-check-square-o"
              END AS status_icon,
              CASE a.status_data
                  WHEN 0 THEN "red"
                  WHEN 1 THEN "green"
              END AS warna,
              CASE a.status_tl
                  WHEN 0 THEN "label alert-info"
                  WHEN 1 THEN "label alert-success"
                  WHEN 2 THEN "label alert-warning"
                  WHEN 3 THEN "label alert-danger"
              END AS tl_color,
              CASE a.status_tl
                  WHEN 0 THEN "Belum TL"
                  WHEN 1 THEN "Diakomodir"
                  WHEN 3 THEN "Tidak Dapat di-TL"
              END AS tl_text 
            FROM trx_pokir_tl_unit AS a
            LEFT OUTER JOIN trx_pokir_lokasi AS b ON a.id_pokir_lokasi = b.id_pokir_lokasi
            LEFT OUTER JOIN trx_pokir_usulan AS c ON a.id_pokir_usulan = c.id_pokir_usulan
            LEFT OUTER JOIN trx_pokir AS d ON a.id_pokir = d.id_pokir
            LEFT OUTER JOIN ref_desa AS e ON b.id_desa = e.id_desa
            LEFT OUTER JOIN ref_kecamatan AS f ON e.id_kecamatan = f.id_kecamatan
            LEFT OUTER JOIN ref_satuan AS g ON c.id_satuan = g.id_satuan
            LEFT OUTER JOIN trx_renja_rancangan_aktivitas AS h ON a.id_aktivitas_renja = h.id_aktivitas_renja, 
            (Select @id:=0) j WHERE d.id_tahun='.$id_tahun.' and a.unit_tl='.$unit_tl);

        return DataTables::of($datapokir)
          ->addColumn('action', function ($datapokir) {
              return '
                <button id="edit-idenpokir" type="button" class="btn btn-primary btn-sm btn-labeled" aria-haspopup="true" aria-expanded="false"><span class="btn-label"><i class="fa fa-tags fa-fw fa-lg"></i></span> Proses TL</button>
              ';})
          ->make(true);
    }

    public function getDataAktivitas($id_tahun,$unit_tl)
    {
        $dataaktivitas=DB::SELECT('SELECT (@id:=@id+1) as no_urut,a.uraian_kegiatan_forum, c.id_aktivitas_pd,
            c.uraian_aktivitas_kegiatan, c.pagu_aktivitas_forum FROM trx_rkpd_rancangan_kegiatan_pd AS a
            INNER JOIN trx_rkpd_rancangan_pelaksana_pd AS b ON b.id_kegiatan_pd = a.id_program_pd
            INNER JOIN trx_rkpd_rancangan_aktivitas_pd AS c ON c.id_pelaksana_pd = b.id_pelaksana_pd, 
            (Select @id:=0) j WHERE a.tahun_forum='.$id_tahun.' and a.id_unit='.$unit_tl);

        return DataTables::of($dataaktivitas)
          ->addColumn('action', function ($dataaktivitas) {
              return '
                <button id="btnPilihAktivitas" type="button" class="btn btn-success btn-sm btn-labeled" aria-haspopup="true" aria-expanded="false"><span class="btn-label"><i class="fa fa-clone fa-fw fa-lg"></i></span>Pilih</button>
              ';})
          ->make(true);
    }

    public function importData(Request $req)
    {
        $getData=DB::INSERT('INSERT INTO trx_pokir_tl_unit ( unit_tl, id_pokir_tl, id_pokir, id_pokir_usulan, id_pokir_lokasi, id_aktivitas_renja, id_aktivitas_forum, status_tl, keterangan_status, status_data)
          SELECT a.unit_tl, a.id_pokir_tl, a.id_pokir, a.id_pokir_usulan, COALESCE(a.id_pokir_lokasi,0) as id_pokir_lokasi, 0, 0, 0, Null, 0
          FROM trx_pokir_tl AS a
          LEFT OUTER JOIN trx_pokir_tl_unit AS b ON a.id_pokir_tl = b.id_pokir_tl
          WHERE b.id_pokir_tl is null AND a.unit_tl='.$req->unit_tl.' AND a.status_data=1 AND a.status_tl=1');

        if($getData != 0){
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); 
        } else {
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
        if(Auth::check()){ 
            return view('pokir.index_unit');
        } else {
            return view ( 'errors.401' );
        }
    }

    public function Posting(Request $req)
    {
        // $cek = DB::SELECT('SELECT id_pokir_tl FROM trx_pokir_tl_unit WHERE id_pokir_tl = '.$req->id_pokir_unit);

        // if($cek == null){
            $data = TrxPokirUnit::find($req->id_pokir_unit);
            $data->status_data = $req->status_data;
            try{
                $data->save (['timestamps' => false]);
                if($req->status_data == 1) {
                    return response ()->json (['pesan'=>'Data Berhasil Di-Posting','status_pesan'=>'1']);
                } else {
                    return response ()->json (['pesan'=>'Data Berhasil Di-UnPosting','status_pesan'=>'1']);
                }
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        // } else {
            // return response ()->json (['pesan'=>'Maaf Usulan Pokir ini telah ditindaklanjuti oleh Perangkat Daerah','status_pesan'=>'0']);
        // }
    }

    public function unloadData(Request $req)
    {
        // $cek = DB::SELECT('SELECT id_pokir_tl FROM trx_pokir_tl_unit WHERE id_pokir_tl = '.$req->id_pokir_unit);

        if($req->status_data == 0){
            $getData=DB::DELETE('DELETE FROM trx_pokir_tl_unit WHERE id_pokir_unit='.$req->id_pokir_unit);
            if($getData != 0){
                return response ()->json (['pesan'=>'Data Berhasil Di-UnLoad','status_pesan'=>'1']); 
            } else {
                return response ()->json (['pesan'=>'Data Gagal Di-UnLoad ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Maaf Usulan Pokir ini telah di-Posting','status_pesan'=>'0']);
        }
    }

    public function editPokirUnit(Request $req)
      {
        if($req->status_data==0){
            $data = TrxPokirUnit::find($req->id_pokir_unit);
            $data->status_tl = $req->status_tl;
            $data->id_aktivitas_renja = $req->id_aktivitas_renja;
            $data->id_aktivitas_forum = $req->id_aktivitas_forum;
            $data->keterangan_status = $req->keterangan_status;
            $data->volume_tl = $req->volume_tl;
            $data->pagu_tl = $req->pagu_tl;

            // if($req->status_data==1 && $req->status_tl==0){
            // return response ()->json (['pesan'=>'Data Usulan tidak dapat disimpan, Status Usulan Belum di-TL sehingga Data Belum Dapat di-Posting','status_pesan'=>'0']);
            // } else {
            if(($req->id_aktivitas_renja==0 || $req->id_aktivitas_renja== Null) && $req->status_tl==1){
            return response ()->json (['pesan'=>'Data Usulan tidak dapat disimpan, Status Usulan diakomodir tetapi Aktivitas belum dipilih','status_pesan'=>'0']);
            } else {
                try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                }
            }
            // }
        } else {
            return response ()->json (['pesan'=>'Data Usulan tidak dapat disimpan, Status Data Telah di-Posting','status_pesan'=>'0']);
        }
      }

}
