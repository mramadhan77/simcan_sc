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
use App\Models\TrxPokir;
use App\Models\TrxPokirUsulan;
use App\Models\TrxPokirLokasi;
use App\Models\TrxPokirTL;



class TrxTLPokirController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getData($id_tahun)
    {
        $datapokir=DB::select('SELECT (@id:=@id+1) as no_urut,a.id_pokir_tl,a.id_pokir,a.id_pokir_usulan,a.id_pokir_lokasi,
              a.unit_tl,a.status_tl,a.keterangan_status,COALESCE(e.nama_desa,"Tidak Spesifik") as nama_desa, COALESCE(f.nama_kecamatan,"Tidak Spesifik") as nama_kecamatan,c.id_judul_usulan,d.nama_pengusul,d.id_tahun,COALESCE(c.volume,0) as volume, COALESCE(c.jml_anggaran,0) as jml_anggaran, COALESCE(g.uraian_satuan,"Tidak Spesifik") as uraian_satuan,c.diskripsi_usulan,a.status_data, COALESCE(h.nm_unit,"Unit Kosong") as uraian_unit,
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
                  WHEN 3 THEN "label label-primary"
                  WHEN 4 THEN "label alert-danger"
              END AS tl_color,
              CASE a.status_tl
                  WHEN 0 THEN "Belum TL"
                  WHEN 1 THEN "Diteruskan"
                  WHEN 2 THEN "Tidak Sesuai dengan Prioritas Pemda"
                  WHEN 3 THEN "Diproses dgn Sistem Hibah/Bansos"
                  WHEN 4 THEN "Tidak Dapat di-TL"
              END AS tl_text 
            FROM trx_pokir_tl AS a
            LEFT OUTER JOIN trx_pokir_lokasi AS b ON a.id_pokir_lokasi = b.id_pokir_lokasi
            LEFT OUTER JOIN trx_pokir_usulan AS c ON a.id_pokir_usulan = c.id_pokir_usulan
            LEFT OUTER JOIN trx_pokir AS d ON a.id_pokir = d.id_pokir
            LEFT OUTER JOIN ref_desa AS e ON b.id_desa = e.id_desa
            LEFT OUTER JOIN ref_kecamatan AS f ON e.id_kecamatan = f.id_kecamatan
            LEFT OUTER JOIN ref_satuan AS g ON c.id_satuan = g.id_satuan
            LEFT OUTER JOIN ref_unit as h ON a.unit_tl = h.id_unit, 
            (Select @id:=0) j WHERE d.id_tahun='.$id_tahun);

        return DataTables::of($datapokir)
          ->addColumn('action', function ($datapokir) {
              return '
                <button id="edit-idenpokir" type="button" class="btn btn-info btn-sm btn-labeled"><span class="btn-label"><i class="fa fa-list-alt fa-fw fa-lg"></i></span> Edit Usulan</button>
              ';})
          ->make(true);
    }

    public function getDataPokir()
    {
        $getRenja=DB::select('SELECT a.id_pokir, a.nama_pengusul
                FROM trx_pokir AS a
                LEFT OUTER JOIN trx_pokir_tl AS b ON a.id_pokir = b.id_pokir
                WHERE b.id_pokir IS NULL AND a.id_tahun ='.Session::get('tahun'));

        return json_encode($getRenja);
    }

    public function importData(Request $req)
    {
        $getData=DB::INSERT('INSERT INTO trx_pokir_tl (id_pokir, id_pokir_usulan, id_pokir_lokasi, unit_tl, status_tl, keterangan_status)
          SELECT a.id_pokir, b.id_pokir_usulan, COALESCE(c.id_pokir_lokasi,0) as id_pokir_lokasi, b.id_unit, 0, Null
          FROM trx_pokir AS a
          INNER JOIN trx_pokir_usulan AS b ON b.id_pokir = a.id_pokir
          LEFT OUTER JOIN trx_pokir_lokasi AS c ON c.id_pokir_usulan = b.id_pokir_usulan
          WHERE a.id_pokir='.$req->id_pokir);

        if($getData != 0){
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); 
        } else {
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    }

    public function Posting(Request $req)
    {
        $cek = DB::SELECT('SELECT id_pokir_tl FROM trx_pokir_tl_unit WHERE id_pokir_tl = '.$req->id_pokir_tl);

        if($cek == null){
            $data = TrxPokirTL::find($req->id_pokir_tl);
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
        } else {
            return response ()->json (['pesan'=>'Maaf Usulan Pokir ini telah ditindaklanjuti oleh Perangkat Daerah','status_pesan'=>'0']);
        }
    }

    public function unloadData(Request $req)
    {
        $cek = DB::SELECT('SELECT id_pokir_tl FROM trx_pokir_tl_unit WHERE id_pokir_tl = '.$req->id_pokir_tl);

        if($cek == null){
            $getData=DB::DELETE('DELETE FROM trx_pokir_tl WHERE id_pokir_tl='.$req->id_pokir_tl);
            if($getData != 0){
                return response ()->json (['pesan'=>'Data Berhasil Di-UnLoad','status_pesan'=>'1']); 
            } else {
                return response ()->json (['pesan'=>'Data Gagal Di-UnLoad ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Maaf Usulan Pokir ini telah ditindaklanjuti oleh Perangkat Daerah','status_pesan'=>'0']);
        }
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
        // if(Auth::check()){ 
            return view('pokir.index_tl');
        // } else {
            // return view ( 'errors.401' );
        // }
        
    }

    public function editUsulan(Request $req)
    {
        if($req->status_data==0) {
            $data = TrxPokirTL::find($req->id_pokir_tl);
            $data->unit_tl = $req->unit_tl;
            $data->status_tl = $req->status_tl;
            $data->keterangan_status = $req->keterangan_status;
            //   $data->status_data = $req->status_data;

            if($req->status_data==1 && $req->status_tl==0){
            return response ()->json (['pesan'=>'Data Usulan tidak dapat disimpan, Status Usulan Belum di-TL sehingga Data Belum Dapat di-Posting','status_pesan'=>'0']);
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
        } else {
            return response ()->json (['pesan'=>'Maaf Usulan Pokir ini telah ditindaklanjuti oleh Perangkat Daerah','status_pesan'=>'0']);
        }      
    }



}
