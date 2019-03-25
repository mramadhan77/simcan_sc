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

class TrxCetakSshAsbController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function dash_cetak_ssh()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_ssh');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }

    public function dash_cetak_asb()
    {
    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_renja');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }

    public function jenis_report_ssh()
    {
       $getJenis=DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan
            union
            select 1 as id, "Struktur Golongan" as uraian_laporan
            union
            select 2 as id, "Struktur Kelompok" as uraian_laporan
            union
            select 3 as id, "Struktur Sub Kelompok" as uraian_laporan
            union
            select 4 as id, "Struktur Item SSH" as uraian_laporan
            union
            select 5 as id, "Perkada SSH" as uraian_laporan
            ');
       return json_encode($getJenis);
    }

    public function getPerkadaSsh()
    {
        $getPerkadaSsh=DB::select('SELECT id_perkada, nomor_perkada, tanggal_perkada, tahun_berlaku, id_perkada_induk, 
            id_perubahan, uraian_perkada, `status`, flag, created_at, updated_at
            FROM ref_ssh_perkada');
        return json_encode($getPerkadaSsh);
    }

    public function getZonaPerkada($id_perkada)
    {
        $query = 'SELECT a.id_zona_perkada, a.no_urut, a.id_perkada, a.id_perubahan, a.id_zona, a.nama_zona, b.keterangan_zona, b.diskripsi_zona
            FROM ref_ssh_perkada_zona AS a
            INNER JOIN ref_ssh_zona AS b ON a.id_zona = b.id_zona WHERE a.id_perkada='.$id_perkada;

        $getZonaPerkada=DB::select($query);
        return json_encode($getZonaPerkada);
    }

    public function getGolonganSsh()
    {
        $getGolonganSsh=DB::select('SELECT id_golongan_ssh, jenis_ssh, no_urut, uraian_golongan_ssh
            FROM ref_ssh_golongan');
        return json_encode($getGolonganSsh);
    }
        
    public function getKelompokSsh($id_golongan_ssh)
    {
        $getKelompokSsh=DB::select('SELECT id_kelompok_ssh, id_golongan_ssh, no_urut, uraian_kelompok_ssh
            FROM ref_ssh_kelompok WHERE id_golongan_ssh='.$id_golongan_ssh);
        return json_encode($getKelompokSsh);
    }

    public function getSubKelompokSsh($id_kelompok_ssh)
    {
        $getSubKelompokSsh=DB::select('SELECT id_sub_kelompok_ssh, id_kelompok_ssh, no_urut, uraian_sub_kelompok_ssh
            FROM ref_ssh_sub_kelompok WHERE id_kelompok_ssh='.$id_kelompok_ssh);
        return json_encode($getSubKelompokSsh);
    }

}
