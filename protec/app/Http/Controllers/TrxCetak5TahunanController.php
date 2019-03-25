<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use DB;
use Response;
use Session;
use Auth;
use CekAkses;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;

class TrxCetak5TahunanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function dash_cetak_rpjmd()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_rpjmd');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function dash_cetak_renstra()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_renstra');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }

    public function jenis_rpjmd()
    {
       $getJenis=DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan
            union
            select 1 as id, "Matrik RPJMD Keseluruhan" as uraian_laporan
            union
            select 2 as id, "Matrik RPJMD (Belanja Langsung)" as uraian_laporan
            union
            select 3 as id, "Matrik Sasaran Program RPJMD" as uraian_laporan
            ');
       return json_encode($getJenis);
    }

    public function jenis_renstra()
    {
       $getJenis=DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan
            union
            select 1 as id, "Matrik Renstra Keseluruhan" as uraian_laporan
            union
            select 2 as id, "Matrik Renstra (Belanja Langsung)" as uraian_laporan
            union
            select 3 as id, "Sinkronisasi Sasaran Renstra-RPJMD" as uraian_laporan');
       return json_encode($getJenis);
    }

    public function getDokumenRpjmd()
    {
        $getDokumen=DB::select('SELECT a.id_rpjmd, a.id_rpjmd_old, a.thn_dasar, a.tahun_1, a.tahun_2, a.tahun_3, a.tahun_4, a.tahun_5, a.no_perda, a.tgl_perda, 
            a.id_revisi, a.id_status_dokumen, a.sumber_data, a.created_at, a.updated_at
            FROM trx_rpjmd_dokumen AS a');
        return json_encode($getDokumen);
    }

    public function getDokumenRenstra($id_unit)
    {
        $getDokumen=DB::select('SELECT a.id_rpjmd, a.id_renstra, a.id_unit, a.nomor_renstra, a.tanggal_renstra, a.uraian_renstra, a.nm_pimpinan, a.nip_pimpinan, 
                a.jabatan_pimpinan, a.sumber_data, a.created_at, a.update_at
                FROM trx_renstra_dokumen AS a WHERE a.id_unit='.$id_unit);
        return json_encode($getDokumen);
    }
        
    public function getMisiRpjmd()
    {
        $getMisi=DB::select('select id_misi_rpjmd, uraian_misi_rpjmd from trx_rpjmd_misi');
        return json_encode($getMisi);
    }
    public function getTujuanRpjmd($id_misi)
    {
        $getTujuan=DB::select('select id_tujuan_rpjmd,uraian_tujuan_rpjmd from trx_rpjmd_tujuan
        where id_misi_rpjmd='.$id_misi);
        return json_encode($getTujuan);
    }
    public function getSasaranRpjmd($id_tujuan)
    {
        $getTujuan=DB::select('select id_sasaran_rpjmd,uraian_sasaran_rpjmd from trx_rpjmd_sasaran
where id_tujuan_rpjmd='.$id_tujuan);
        return json_encode($getTujuan);
    }
        

}
