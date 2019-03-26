<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\CekAkses;
use Auth;

class RefCetakController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function jenis_renja()
    {
        $getJenis = DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan
            union
            select 1 as id, "Kapitulasi Kegiatan Renja" as uraian_laporan
            union
            select 2 as id, "Ringkasan Pendapatan dan Belanja" as uraian_laporan
            union
            select 3 as id, "Rekapitulasi Pendapatan dan Belanja" as uraian_laporan
            union
            select 4 as id, "Rekap Belanja per Kegiatan" as uraian_laporan
            union
            select 5 as id, "Rincian Belanja per Kegiatan" as uraian_laporan
            union
            select 6 as id, "Rincian Aktivitas Per Kegiatan Perangkat Daerah" as uraian_laporan
            union
            select 7 as id, "Ringkasan Pendapatan Dan Belanja Menurut Organisasi Dan Urusan Pemerintahan" as uraian_laporan
            union
            select 8 as id, "Ringkasan Pendapatan Dan Belanja Menurut Urusan Pemerintahan Dan Organisasi 1" as uraian_laporan
			union
            select 9 as id, "Prakiraan Maju" as uraian_laporan
            union
            select 10 as id, "Matrik Form 1" as uraian_laporan
            union
            select 11 as id, "Matrik Form 2" as uraian_laporan
            union
            select 12 as id, "Matrik Form 3" as uraian_laporan
            union
            select 13 as id, "Musren Rancangan Renja" as uraian_laporan
            union
            select 14 as id, "Perbandingan Pagu dengan Belanja" as uraian_laporan
            ');
        return json_encode($getJenis);
    }

    public function jenis_renja_ranwal()
    {
        // $getJenis = DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan
        //     union
        //     select 1 as id, "Kapitulasi Program Ranwal Renja" as uraian_laporan
        //     union
        //     select 2 as id, "Kapitulasi Kegiatan Ranwal Renja" as uraian_laporan
        //     union
        //     select 3 as id, "Cek Progress Ranwal Renja (XLS)" as uraian_laporan
        //     union
        //     select 4 as id, "Musren Ranwal Renja" as uraian_laporan
        //     union
        //     select 5 as id, "Matriks Sasaran Program Ranwal" as uraian_laporan');
        $getJenis = DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan
            union
            select 1 as id, "Kapitulasi Program Ranwal Renja" as uraian_laporan
            union
            select 2 as id, "Kapitulasi Kegiatan Ranwal Renja" as uraian_laporan
            union
            select 5 as id, "Matriks Sasaran Program Ranwal" as uraian_laporan');
        return json_encode($getJenis);
    }

    public function jenis_musrenbang()
    {
       $getJenis=DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan
            union
            select 1 as id, "Rekapitulasi Usulan RW (XLS)" as uraian_laporan
            union
            select 2 as id, "Status Usulan Musrencan per Kecamatan" as uraian_laporan
            union
            select 3 as id, "Status Usulan Musrencan per OPD" as uraian_laporan
            ');
       return json_encode($getJenis);
    }    

    public function jenis_forum()
    {
        $getJenis = DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan
            union
            select 1 as id, "Kapitulasi Kegiatan Forum" as uraian_laporan
            union
            select 2 as id, "Ringkasan Pendapatan dan Belanja" as uraian_laporan            
            union
            select 3 as id, "Rekapitulasi Pendapatan dan Belanja" as uraian_laporan
            union
            select 4 as id, "Rekap Belanja per Kegiatan" as uraian_laporan
            union
            select 5 as id, "Rincian Belanja per Kegiatan" as uraian_laporan
            union
            select 9 as id, "Prakiraan Maju (Tabel T-C.33)" as uraian_laporan
            
            ');
        return json_encode($getJenis);
    }

    public function getProgram_RanwalRenja($unit, $tahun)
    {
        $getProgram = DB::select('SELECT id_renja_program, uraian_program_renstra FROM trx_renja_ranwal_program where id_unit=' . $unit . ' and tahun_renja=' . $tahun);
        return json_encode($getProgram);
    }

    public function getKegiatan_RanwalRenja($program)
    {
        $getKegiatan = DB::select('SELECT id_renja, uraian_kegiatan_renstra FROM trx_renja_ranwal_kegiatan where id_renja_program=' . $program);
        return json_encode($getKegiatan);
    }

    public function getProgram_RancanganRenja($unit, $tahun)
    {
        $getProgram = DB::select('SELECT id_renja_program, uraian_program_renstra FROM trx_renja_rancangan_program where id_unit=' . $unit . ' and tahun_renja=' . $tahun);
        return json_encode($getProgram);
    }

    public function getKegiatan_RancanganRenja($program)
    {
        $getKegiatan = DB::select('SELECT id_renja, uraian_kegiatan_renstra FROM trx_renja_rancangan where id_renja_program=' . $program);
        return json_encode($getKegiatan);
    }

    public function getProgram_Forum($unit, $tahun)
    {
        $getProgram = DB::select('SELECT id_forum_program, uraian_program_renstra FROM trx_forum_skpd_program where id_unit=' . $unit . ' and tahun_forum=' . $tahun);
        return json_encode($getProgram);
    }

    public function getKegiatan_Forum($program)
    {
        $getKegiatan = DB::select('SELECT id_forum_skpd, uraian_kegiatan_forum FROM trx_forum_skpd where id_forum_program=' . $program);
        return json_encode($getKegiatan);
    }

    public function jenis_rpkd_final()
    {
        $getJenis = DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan            
            union
            select 2 as id, "Ringkasan Pendapatan dan Belanja" as uraian_laporan
            union
            select 3 as id, "Rekapitulasi Pendapatan dan Belanja" as uraian_laporan
            union
            select 4 as id, "Rekap Belanja per Kegiatan" as uraian_laporan
            union
            select 5 as id, "Rincian Belanja per Kegiatan" as uraian_laporan');
        return json_encode($getJenis);
    }
    
    public function getProgram_Rkpd_Final($unit, $tahun)
    {
        $getProgram = DB::select('SELECT id_program_pd, uraian_program_renstra FROM trx_rkpd_final_program_pd where id_unit=' . $unit . ' and tahun_forum=' . $tahun);
        return json_encode($getProgram);
    }
    
    public function getKegiatan_Rkpd_Final($program)
    {
        $getKegiatan = DB::select('SELECT id_kegiatan_pd, uraian_kegiatan_forum FROM trx_rkpd_final_kegiatan_pd where id_program_pd=' . $program);
        return json_encode($getKegiatan);
    }

    public function jenis_APBD()
    {
        $getJenis = DB::SELECT('select 0 as id, "Pilih Jenis Laporan" as uraian_laporan
                union
                select 2 as id, "Ringkasan Pendapatan dan Belanja" as uraian_laporan
                union
                select 3 as id, "Rekapitulasi Pendapatan dan Belanja" as uraian_laporan
                union
                select 4 as id, "Rekap Belanja per Kegiatan" as uraian_laporan
                union
                select 5 as id, "Rincian Belanja per Kegiatan" as uraian_laporan 
                union
                select 14 as id, "Matriks Sasaran Program Renja Final" as uraian_laporan                  
            ');
        return json_encode($getJenis);
    }
    
    public function getProgram_APBD($unit, $tahun)
    {
        $getProgram = DB::select('SELECT id_program_pd, uraian_program_renstra FROM trx_anggaran_program_pd AS a
        INNER JOIN trx_anggaran_pelaksana AS j ON a.id_pelaksana_anggaran = j.id_pelaksana_anggaran
        INNER JOIN trx_anggaran_program AS l ON j.id_anggaran_pemda = l.id_anggaran_pemda
        INNER JOIN trx_anggaran_dokumen AS m ON l.id_dokumen_keu = m.id_dokumen_keu
        WHERE  m.jns_dokumen_keu=1 and m.kd_dokumen_keu=0 and a.id_unit=' . $unit . ' and a.tahun_anggaran=' . $tahun);
        return json_encode($getProgram);
    }
    
    public function getKegiatan_APBD($program)
    {
        $getKegiatan = DB::select('SELECT id_kegiatan_pd, uraian_kegiatan_forum FROM trx_anggaran_kegiatan_pd as b
        INNER JOIN trx_anggaran_program_pd AS a on a.id_program_pd=b.id_program_pd
        INNER JOIN trx_anggaran_pelaksana AS j ON a.id_pelaksana_anggaran = j.id_pelaksana_anggaran
        INNER JOIN trx_anggaran_program AS l ON j.id_anggaran_pemda = l.id_anggaran_pemda
        INNER JOIN trx_anggaran_dokumen AS m ON l.id_dokumen_keu = m.id_dokumen_keu 
        WHERE m.jns_dokumen_keu=1 and m.kd_dokumen_keu=0 and b.id_program_pd=' . $program);
        return json_encode($getKegiatan);
    }

    public function getKegiatan_PPAS($program)
    {
        $getKegiatan = DB::select('SELECT id_kegiatan_pd, uraian_kegiatan_forum FROM trx_anggaran_kegiatan_pd as b
        INNER JOIN trx_anggaran_program_pd AS a on a.id_program_pd=b.id_program_pd
        INNER JOIN trx_anggaran_pelaksana AS j ON a.id_pelaksana_anggaran = j.id_pelaksana_anggaran
        INNER JOIN trx_anggaran_program AS l ON j.id_anggaran_pemda = l.id_anggaran_pemda
        INNER JOIN trx_anggaran_dokumen AS m ON l.id_dokumen_keu = m.id_dokumen_keu 
        WHERE b.id_program_pd=' . $program);
        return json_encode($getKegiatan);
    }

    public function getDokAPBD($program)
    {
        $getKegiatan = DB::select('SELECT a.id_dokumen_keu, a.nomor_keu, a.uraian_perkada FROM trx_anggaran_dokumen as a
        WHERE a.tahun_anggaran='.Session::get('tahun').' and a.jns_dokumen_keu=1 and a.kd_dokumen_keu=0');
        return json_encode($getKegiatan);
    }

}