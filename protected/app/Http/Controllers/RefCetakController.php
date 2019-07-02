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
        $getJenis = DB::SELECT('SELECT 0 AS id, "Pilih Jenis Laporan" AS uraian_laporan
            UNION
            SELECT 1 AS id, "Kapitulasi Kegiatan Renja" AS uraian_laporan
            UNION
            SELECT 2 AS id, "Ringkasan Pendapatan dan Belanja" AS uraian_laporan
            UNION
            SELECT 3 AS id, "Rekapitulasi Pendapatan dan Belanja" AS uraian_laporan
            UNION
            SELECT 4 AS id, "Rekap Belanja per Kegiatan" AS uraian_laporan
            UNION
            SELECT 5 AS id, "Rincian Belanja per Kegiatan" AS uraian_laporan
            UNION
            SELECT 6 AS id, "Rincian Aktivitas Per Kegiatan Perangkat Daerah" AS uraian_laporan
            UNION
            SELECT 7 AS id, "Ringkasan Pendapatan Dan Belanja Menurut Organisasi Dan Urusan Pemerintahan" AS uraian_laporan
            UNION
            SELECT 8 AS id, "Ringkasan Pendapatan Dan Belanja Menurut Urusan Pemerintahan Dan Organisasi 1" AS uraian_laporan
			UNION
            SELECT 9 AS id, "Prakiraan Maju" AS uraian_laporan
            UNION
            SELECT 10 AS id, "Matrik Form 1" AS uraian_laporan
            UNION
            SELECT 11 AS id, "Matrik Form 2" AS uraian_laporan
            UNION
            SELECT 12 AS id, "Matrik Form 3" AS uraian_laporan
            UNION
            SELECT 13 AS id, "Musren Rancangan Renja" AS uraian_laporan
            UNION
            SELECT 14 AS id, "Perbandingan Pagu dengan Belanja" AS uraian_laporan
            ');
        return json_encode($getJenis);
    }

    public function jenis_renja_ranwal()
    {
        $getJenis = DB::SELECT('SELECT 0 AS id, "Pilih Jenis Laporan" AS uraian_laporan
            UNION
            SELECT 1 AS id, "Kapitulasi Program Ranwal Renja" AS uraian_laporan
            UNION
            SELECT 2 AS id, "Kapitulasi Kegiatan Ranwal Renja" AS uraian_laporan
            UNION
            SELECT 5 AS id, "Matriks Sasaran Program Ranwal" AS uraian_laporan
            UNION
            SELECT 6 AS id, "Prakiraan Maju" AS uraian_laporan');
        return json_encode($getJenis);
    }

    public function jenis_pokir()
    {
        $getJenis = DB::SELECT('
            SELECT 0 AS id, "Pilih Jenis Laporan" AS uraian_laporan
            UNION
            SELECT 1 AS id, "List Usulan Pokir" AS uraian_laporan
            UNION
            SELECT 2 AS id, "List TL Usulan Pokir oleh Bappeda" AS uraian_laporan
            UNION
            SELECT 3 AS id, "List TL Usulan Pokir oleh Perangkat Daerah" AS uraian_laporan
            UNION
            SELECT 4 AS id, "Rumusan Usulan Program/Kegiatan Hasil Penelaahan Pokir DPRD" AS uraian_laporan');        
        return json_encode($getJenis);
    }

    public function jenis_musrenbang()
    {
       $getJenis=DB::SELECT('SELECT 0 AS id, "Pilih Jenis Laporan" AS uraian_laporan
            UNION
            SELECT 1 AS id, "Rekapitulasi Usulan RW" AS uraian_laporan
            UNION
            SELECT 2 AS id, "Rekapitulasi Usulan Musrenbang Desa" AS uraian_laporan
            UNION
            SELECT 3 AS id, "Rekapitulasi Usulan Musrenbang Kecamatan" AS uraian_laporan
            ');
       return json_encode($getJenis);
    }    

    public function jenis_forum()
    {
        $getJenis = DB::SELECT('SELECT 0 AS id, "Pilih Jenis Laporan" AS uraian_laporan
            UNION
            SELECT 1 AS id, "Kapitulasi Kegiatan Forum" AS uraian_laporan
            UNION
            SELECT 2 AS id, "Ringkasan Pendapatan dan Belanja" AS uraian_laporan            
            UNION
            SELECT 3 AS id, "Rekapitulasi Pendapatan dan Belanja" AS uraian_laporan
            UNION
            SELECT 4 AS id, "Rekap Belanja per Kegiatan" AS uraian_laporan
            UNION
            SELECT 5 AS id, "Rincian Belanja per Kegiatan" AS uraian_laporan
            UNION
            SELECT 9 AS id, "Prakiraan Maju (Tabel T-C.33)" AS uraian_laporan
            
            ');
        return json_encode($getJenis);
    }

    public function getProgram_RanwalRenja($unit, $tahun)
    {
        $getProgram = DB::SELECT('SELECT id_renja_program, uraian_program_renstra FROM trx_renja_ranwal_program where id_unit=' . $unit . ' and tahun_renja=' . $tahun);
        return json_encode($getProgram);
    }

    public function getKegiatan_RanwalRenja($program)
    {
        $getKegiatan = DB::SELECT('SELECT id_renja, uraian_kegiatan_renstra FROM trx_renja_ranwal_kegiatan where id_renja_program=' . $program);
        return json_encode($getKegiatan);
    }

    public function getProgram_RancanganRenja($unit, $tahun)
    {
        $getProgram = DB::SELECT('SELECT id_renja_program, uraian_program_renstra FROM trx_renja_rancangan_program where id_unit=' . $unit . ' and tahun_renja=' . $tahun);
        return json_encode($getProgram);
    }

    public function getKegiatan_RancanganRenja($program)
    {
        $getKegiatan = DB::SELECT('SELECT id_renja, uraian_kegiatan_renstra FROM trx_renja_rancangan where id_renja_program=' . $program);
        return json_encode($getKegiatan);
    }

    public function getProgram_Forum($unit, $tahun)
    {
        $getProgram = DB::SELECT('SELECT id_forum_program, uraian_program_renstra FROM trx_forum_skpd_program where id_unit=' . $unit . ' and tahun_forum=' . $tahun);
        return json_encode($getProgram);
    }

    public function getKegiatan_Forum($program)
    {
        $getKegiatan = DB::SELECT('SELECT id_forum_skpd, uraian_kegiatan_forum FROM trx_forum_skpd where id_forum_program=' . $program);
        return json_encode($getKegiatan);
    }

    public function jenis_rkpd_ranwal()
    {
        $getJenis = DB::SELECT('
            SELECT 0 AS id, "Pilih Jenis Laporan" AS uraian_laporan
            UNION
            SELECT 1 AS id, "Tujuan, Sasaran dan Indikator RKPD" AS uraian_laporan
            UNION
            SELECT 2 AS id, "Review Terhadap Rancangan Awal RKPD" AS uraian_laporan
            UNION
            SELECT 3 AS id, "Prakiraan Maju Rancangan Awal RKPD" AS uraian_laporan
            ');
        return json_encode($getJenis);
    }

    public function jenis_rpkd_final()
    {
        $getJenis = DB::SELECT('SELECT 0 AS id, "Pilih Jenis Laporan" AS uraian_laporan            
            UNION
            SELECT 2 AS id, "Ringkasan Pendapatan dan Belanja" AS uraian_laporan
            UNION
            SELECT 3 AS id, "Rekapitulasi Pendapatan dan Belanja" AS uraian_laporan
            UNION
            SELECT 4 AS id, "Rekap Belanja per Kegiatan" AS uraian_laporan
            UNION
            SELECT 5 AS id, "Rincian Belanja per Kegiatan" AS uraian_laporan');
        return json_encode($getJenis);
    }
    
    public function getProgram_Rkpd_Final($unit, $tahun)
    {
        $getProgram = DB::SELECT('SELECT id_program_pd, uraian_program_renstra FROM trx_rkpd_final_program_pd where id_unit=' . $unit . ' and tahun_forum=' . $tahun);
        return json_encode($getProgram);
    }
    
    public function getKegiatan_Rkpd_Final($program)
    {
        $getKegiatan = DB::SELECT('SELECT id_kegiatan_pd, uraian_kegiatan_forum FROM trx_rkpd_final_kegiatan_pd where id_program_pd=' . $program);
        return json_encode($getKegiatan);
    }

    public function jenis_APBD()
    {
        $getJenis = DB::SELECT('SELECT 0 AS id, "Pilih Jenis Laporan" AS uraian_laporan
                UNION
                SELECT 2 AS id, "Ringkasan Pendapatan dan Belanja" AS uraian_laporan
                UNION
                SELECT 3 AS id, "Rekapitulasi Pendapatan dan Belanja" AS uraian_laporan
                UNION
                SELECT 4 AS id, "Rekap Belanja per Kegiatan" AS uraian_laporan
                UNION
                SELECT 5 AS id, "Rincian Belanja per Kegiatan" AS uraian_laporan 
                UNION
                SELECT 14 AS id, "Matriks Sasaran Program Renja Final" AS uraian_laporan                  
            ');
        return json_encode($getJenis);
    }
    
    public function getProgram_APBD($unit, $tahun)
    {
        $getProgram = DB::SELECT('SELECT id_program_pd, uraian_program_renstra FROM trx_anggaran_program_pd AS a
        INNER JOIN trx_anggaran_pelaksana AS j ON a.id_pelaksana_anggaran = j.id_pelaksana_anggaran
        INNER JOIN trx_anggaran_program AS l ON j.id_anggaran_pemda = l.id_anggaran_pemda
        INNER JOIN trx_anggaran_dokumen AS m ON l.id_dokumen_keu = m.id_dokumen_keu
        WHERE  m.jns_dokumen_keu=1 and m.kd_dokumen_keu=0 and a.id_unit=' . $unit . ' and a.tahun_anggaran=' . $tahun);
        return json_encode($getProgram);
    }
    
    public function getKegiatan_APBD($program)
    {
        $getKegiatan = DB::SELECT('SELECT id_kegiatan_pd, uraian_kegiatan_forum FROM trx_anggaran_kegiatan_pd AS b
        INNER JOIN trx_anggaran_program_pd AS a on a.id_program_pd=b.id_program_pd
        INNER JOIN trx_anggaran_pelaksana AS j ON a.id_pelaksana_anggaran = j.id_pelaksana_anggaran
        INNER JOIN trx_anggaran_program AS l ON j.id_anggaran_pemda = l.id_anggaran_pemda
        INNER JOIN trx_anggaran_dokumen AS m ON l.id_dokumen_keu = m.id_dokumen_keu 
        WHERE m.jns_dokumen_keu=1 and m.kd_dokumen_keu=0 and b.id_program_pd=' . $program);
        return json_encode($getKegiatan);
    }

    public function getKegiatan_PPAS($program)
    {
        $getKegiatan = DB::SELECT('SELECT id_kegiatan_pd, uraian_kegiatan_forum FROM trx_anggaran_kegiatan_pd AS b
        INNER JOIN trx_anggaran_program_pd AS a on a.id_program_pd=b.id_program_pd
        INNER JOIN trx_anggaran_pelaksana AS j ON a.id_pelaksana_anggaran = j.id_pelaksana_anggaran
        INNER JOIN trx_anggaran_program AS l ON j.id_anggaran_pemda = l.id_anggaran_pemda
        INNER JOIN trx_anggaran_dokumen AS m ON l.id_dokumen_keu = m.id_dokumen_keu 
        WHERE b.id_program_pd=' . $program);
        return json_encode($getKegiatan);
    }

    public function getDokAPBD($program)
    {
        $getKegiatan = DB::SELECT('SELECT a.id_dokumen_keu, a.nomor_keu, a.uraian_perkada FROM trx_anggaran_dokumen AS a
        WHERE a.tahun_anggaran='.Session::get('tahun').' and a.jns_dokumen_keu=1 and a.kd_dokumen_keu=0');
        return json_encode($getKegiatan);
    }

}