<?php
namespace App\Http\Controllers\Kin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use DB;
use Response;
use Session;
use Auth;
use CekAkses;
use App\Fungsi as Fungsi;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use Doctrine\DBAL\Query\QueryException;

use App\Models\Kin\RefSotkLevel1;
use App\Models\Kin\RefSotkLevel2;
use App\Models\Kin\RefSotkLevel3;
use App\Models\TrxRpjmdDokumen;
use App\Models\TrxRpjmdVisi;
use App\Models\TrxRpjmdMisi;
use App\Models\TrxRpjmdTujuan;
use App\Models\TrxRpjmdSasaran;
use App\Models\TrxRpjmdKebijakan;
use App\Models\TrxRpjmdStrategi;
use App\Models\TrxRpjmdProgram;
use App\Models\TrxRpjmdProgramIndikator;
use App\Models\TrxRpjmdProgramUrusan;
use App\Models\TrxRpjmdProgramPelaksana;
use App\Models\Can\TrxRenstraDokumen;
use App\Models\Can\TrxRenstraVisi;
use App\Models\Can\TrxRenstraMisi;
use App\Models\Can\TrxRenstraTujuan;
use App\Models\Can\TrxRenstraSasaran;
use App\Models\Can\TrxRenstraKebijakan;
use App\Models\Can\TrxRenstraStrategi;
use App\Models\Can\TrxRenstraProgram;
use App\Models\Can\TrxRenstraProgramIndikator;
use App\Models\Can\TrxRenstraKegiatan;
use App\Models\Can\TrxRenstraKegiatanIndikator;


class KinReportTapkinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
	{
		// if(Auth::check()){ 
		    return view("kin.laporan.perkin");			
		// } else {
			// return view ( 'errors.401' );
		// }	
    }
    
    public function jenis_pokin()
    {
       $getJenis=DB::SELECT('SELECT 1 AS id, "1. RPJMD" AS uraian_laporan
            UNION
            SELECT 2 AS id, "2. Renstra" AS uraian_laporan
            UNION
            SELECT 6 AS id, "3. IKU Pemda" AS uraian_laporan
            UNION
            SELECT 7 AS id, "4. IKU Perangkat Daerah Level 1" AS uraian_laporan
            UNION
            SELECT 8 AS id, "5. IKU Perangkat Daerah Level 2" AS uraian_laporan
            UNION
            SELECT 9 AS id, "6. IKU Perangkat Daerah Level 3" AS uraian_laporan
            UNION
            SELECT 3 AS id, "7. RKT" AS uraian_laporan
            UNION
            SELECT 4 AS id, "8. Perjanjian Kinerja Pemda" AS uraian_laporan
            UNION
            SELECT 17 AS id, "9. Lampiran Perjanjian Kinerja Pemda" AS uraian_laporan
            UNION
            SELECT 10 AS id, "10. Perjanjian Kinerja OPD Level 1" AS uraian_laporan
            UNION
            SELECT 18 AS id, "11. Lampiran Perjanjian Kinerja OPD Level 1" AS uraian_laporan
            UNION
            SELECT 11 AS id, "12. Perjanjian Kinerja OPD Level 2" AS uraian_laporan
            UNION
            SELECT 19 AS id, "13. Lampiran Perjanjian Kinerja OPD Level 2" AS uraian_laporan
            UNION
            SELECT 12 AS id, "14. Perjanjian Kinerja OPD Level 3" AS uraian_laporan
            UNION
            SELECT 20 AS id, "15. Lampiran Perjanjian Kinerja OPD Level 3" AS uraian_laporan
            UNION
            SELECT 13 AS id, "16. Rencana Aksi Perjanjian Kinerja OPD" AS uraian_laporan
            UNION
            SELECT 5 AS id, "17. Pengukuran Kinerja Pemda" AS uraian_laporan
            UNION
            SELECT 14 AS id, "18. Pengukuran Kinerja OPD Level 1 per Triwulan" AS uraian_laporan
            UNION
            SELECT 15 AS id, "19. Pengukuran Kinerja OPD Level 2 per Triwulan" AS uraian_laporan
            UNION
            SELECT 16 AS id, "20. Pengukuran Kinerja OPD Level 3 per Triwulan" AS uraian_laporan');
       return json_encode($getJenis);
    }

    public function getTahun()
        {
            $getTahun=DB::select('SELECT tahun_1 AS tahun FROM ref_tahun
                UNION ALL
                SELECT tahun_2 AS tahun FROM ref_tahun
                UNION ALL
                SELECT tahun_3 AS tahun FROM ref_tahun
                UNION ALL
                SELECT tahun_4 AS tahun FROM ref_tahun
                UNION ALL
                SELECT tahun_5 AS tahun FROM ref_tahun');
            return json_encode($getTahun);
        }

    public function getDokIkuPemda()
        {
            $getDokIkuPemda=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, a.id_dokumen, a.no_dokumen, a.tgl_dokumen, a.uraian_dokumen, a.id_rpjmd, 
                a.id_perubahan, a.status_dokumen, a.created_at, a.updated_at
                FROM kin_trx_iku_pemda_dok AS a, (SELECT @id:=0) x');
            return json_encode($getDokIkuPemda);
        }

    public function getDokIkuOPD($id_unit)
        {
            $getDokIkuOPD=DB::SELECT('SELECT DISTINCT (@id:=@id+1) as no_urut, a.id_dokumen, a.no_dokumen, a.tgl_dokumen, a.uraian_dokumen, a.id_renstra, 
                a.id_perubahan, a.status_dokumen, a.created_at, a.updated_at, a.id_unit
                FROM kin_trx_iku_opd_dok AS a, (SELECT @id:=0) x
                WHERE a.id_unit='.$id_unit);
            return json_encode($getDokIkuOPD);
        }

    public function getSotkLevel1($id_unit)
        {
            $getSotkLevel1=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es2, a.id_unit, a.nama_eselon, a.tingkat_eselon, a.created_at, a.updated_at,
                CASE a.tingkat_eselon 
                    WHEN 0 THEN "I"
                    WHEN 1 THEN "II"
                    WHEN 2 THEN "III"
                    WHEN 3 THEN "IV"
                ELSE "((Error))" END AS eselon_display
                FROM ref_sotk_level_1 AS a,(SELECT @id:=0) x
                WHERE a.id_unit='.$id_unit);
            return json_encode($getSotkLevel1);
        }

    public function getSotkLevel2($id_sotk_es2)
        {
            $getSotkLevel2=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es2, a.id_sotk_es3, a.nama_eselon AS nama_eselon3, 
                a.tingkat_eselon, a.created_at, a.updated_at,
                CASE a.tingkat_eselon 
                    WHEN 0 THEN "I"
                    WHEN 1 THEN "II"
                    WHEN 2 THEN "III"
                    WHEN 3 THEN "IV"
                ELSE "((Error))" END AS eselon_display
                FROM ref_sotk_level_2 AS a,(SELECT @id:=0) x
                WHERE a.id_sotk_es2='.$id_sotk_es2);
            return json_encode($getSotkLevel2);
        }

    public function getSotkLevel3($id_sotk_es3)
        {
            $getSotkLevel3=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.id_sotk_es3, a.id_sotk_es3, a.id_sotk_es4 ,a.nama_eselon AS nama_eselon4, 
                a.tingkat_eselon, a.created_at, a.updated_at,
                CASE a.tingkat_eselon 
                    WHEN 0 THEN "I"
                    WHEN 1 THEN "II"
                    WHEN 2 THEN "III"
                    WHEN 3 THEN "IV"
                ELSE "((Error))" END AS eselon_display
                FROM ref_sotk_level_3 AS a,(SELECT @id:=0) x
                WHERE a.id_sotk_es3='.$id_sotk_es3);
            return json_encode($getSotkLevel3);
        }

    public function indexChart($id_rpjmd)
    {
        // if(Auth::check()){ 
            $data = TrxRpjmdVisi::with(array("TrxRpjmdMisis" => function ($q){
                    $q->where("no_urut","<","90")->with('TrxRpjmdTujuans.TrxRpjmdSasarans.TrxRpjmdPrograms');}))
                    ->where('id_rpjmd','=',$id_rpjmd)
                    ->get(); 
            
            $dok = DB::SELECT('SELECT id_rpjmd, id_rpjmd_old, thn_dasar, tahun_1, tahun_2, tahun_3, tahun_4, tahun_5, no_perda, 
                    tgl_perda, id_revisi, id_status_dokumen, sumber_data, created_at, updated_at
                    FROM trx_rpjmd_dokumen 
                    WHERE id_rpjmd='.$id_rpjmd);
                    
            return view('kin.pokin.FrmRpjmdChart',['data' => $data,'dok' => $dok]);
        // } else {
            // // return view ( 'errors.401' );
        // }
    }

    public function indexChartPD($id_renstra,$id_unit)
    {
        // if(Auth::check()){ 
            $data = TrxRenstraVisi::with(array("TrxRenstraMisis" => function ($q){
                    $q->where("no_urut","<","90")->with('TrxRenstraTujuans.TrxRenstraSasarans.TrxRenstraPrograms.TrxRenstraKegiatans');}))
                    ->where('id_renstra','=',$id_renstra)
                    ->get(); 
            
            $dok = DB::SELECT('SELECT id_rpjmd, id_renstra, id_unit, nomor_renstra, tanggal_renstra, uraian_renstra, nm_pimpinan, 
                    nip_pimpinan, jabatan_pimpinan, sumber_data, created_at, update_at
                    FROM trx_renstra_dokumen 
                    WHERE id_renstra='.$id_renstra);

            $unit = DB::SELECT('SELECT CONCAT(b.kd_urusan,".",RIGHT(CONCAT("0",b.kd_bidang),2),".",RIGHT(CONCAT("0",a.kd_unit),2)) AS kd_unit_display, 
                    a.id_unit, a.id_bidang, a.kd_unit, a.nm_unit
                    FROM ref_unit AS a INNER JOIN ref_bidang AS b ON a.id_bidang = b.id_bidang 
                    WHERE a.id_unit='.$id_unit);
                    
            return view('kin.pokin.FrmRenstraChart',['data' => $data,'dok' => $dok, 'unit' => $unit]);
        // } else {
            // return view ( 'errors.401' );
        // }
    }
    
}