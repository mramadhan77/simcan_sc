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
use App\Models\Kin\KinTrxCascadingProgramOpd;
use App\Models\Kin\KinTrxCascadingIndikatorProgramOpd;
use App\Models\Kin\KinTrxCascadingKegiatanOpd;
use App\Models\Kin\KinTrxCascadingIndikatorKegiatanOpd;


class KinPokinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
	{
		// if(Auth::check()){ 
		    return view("kin.pokin.index");			
		// } else {
			// return view ( 'errors.401' );
		// }	
    }
    
    public function jenis_pokin()
    {
       $getJenis=DB::SELECT('select 0 as id, "Pilih Pohon Kinerja" as uraian_laporan
            union
            select 1 as id, "Pohon Kinerja RPJMD" as uraian_laporan
            union
            select 2 as id, "Pohon Kinerja Renstra OPD" as uraian_laporan
            union
            select 3 as id, "Pohon Kinerja RPJMD-Renstra" as uraian_laporan
            union
            select 4 as id, "Pohon Kinerja Sasaran OPD" as uraian_laporan
            ');
       return json_encode($getJenis);
    }

    public function indexChart($id_rpjmd)
    {
            $data = TrxRpjmdVisi::with(array("TrxRpjmdMisis" => function ($q){
                    $q->where("no_urut","<","90")->with('TrxRpjmdTujuans.TrxRpjmdSasarans.TrxRpjmdPrograms');}))
                    ->where('id_rpjmd','=',$id_rpjmd)
                    ->get(); 

            // $data = DB::SELECT('SELECT a.no_urut, a.uraian_visi_rpjmd, a.id_visi_rpjmd, b.id_misi_rpjmd, b.no_urut, b.uraian_misi_rpjmd,
            //             c.no_urut, c.id_tujuan_rpjmd, c.uraian_tujuan_rpjmd, d.no_urut, d.id_sasaran_rpjmd, d.uraian_sasaran_rpjmd,
            //             e.no_urut, e.id_program_rpjmd, e.uraian_program_rpjmd
            //             FROM trx_rpjmd_visi AS a
            //             INNER JOIN trx_rpjmd_misi AS b ON b.id_visi_rpjmd = a.id_visi_rpjmd
            //             INNER JOIN trx_rpjmd_tujuan AS c ON c.id_misi_rpjmd = b.id_misi_rpjmd
            //             INNER JOIN trx_rpjmd_sasaran AS d ON d.id_tujuan_rpjmd = c.id_tujuan_rpjmd
            //             INNER JOIN trx_rpjmd_program AS e ON e.id_sasaran_rpjmd = d.id_sasaran_rpjmd')
            
            $dok = DB::SELECT('SELECT id_rpjmd, id_rpjmd_old, thn_dasar, tahun_1, tahun_2, tahun_3, tahun_4, tahun_5, no_perda, 
                    tgl_perda, id_revisi, id_status_dokumen, sumber_data, created_at, updated_at
                    FROM trx_rpjmd_dokumen 
                    WHERE id_rpjmd='.$id_rpjmd);
                    
            return view('kin.pokin.FrmRpjmdChart',['data' => $data,'dok' => $dok]);
    }

    public function indexChartPD($id_renstra,$id_unit)
    {
        // if(Auth::check()){ 
            $data = TrxRenstraVisi::with(array("TrxRenstraMisis" => function ($q){
                    $q->where("no_urut","<","90")->with('TrxRenstraTujuans.TrxRenstraSasarans.TrxRenstraPrograms.TrxRenstraKegiatans');}))
                    ->where('id_renstra','=',$id_renstra)
                    ->where('id_unit','=',$id_unit)
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

    public function indexChartLintas($id_rpjmd)
    {
            $data = TrxRpjmdVisi::with(array("TrxRpjmdMisis" => function ($q){
                $q->where("no_urut","<","90")->with('TrxRpjmdTujuans.TrxRpjmdSasarans.TrxRpjmdPrograms');}))
                ->where('id_rpjmd','=',$id_rpjmd)
                ->get(); 
            
            $dok = DB::SELECT('SELECT id_rpjmd, id_rpjmd_old, thn_dasar, tahun_1, tahun_2, tahun_3, tahun_4, tahun_5, no_perda, 
                    tgl_perda, id_revisi, id_status_dokumen, sumber_data, created_at, updated_at
                    FROM trx_rpjmd_dokumen 
                    WHERE id_rpjmd='.$id_rpjmd);
                    
            return view('kin.pokin.FrmLintasChart',['data' => $data,'dok' => $dok]);
    }



    public function indexChartSasaranPD($id_renstra,$id_unit)
    {
            $data = TrxRenstraVisi::with(array("TrxRenstraMisis" => function ($q){
                    $q->where("no_urut","<","90")->with('TrxRenstraTujuans.TrxRenstraSasarans.KinTrxCascadingProgramOpds.KinTrxCascadingKegiatanOpds');}))
                    ->where('id_renstra','=',$id_renstra)
                    ->where('id_unit','=',$id_unit)
                    ->get(); 
            
            $dok = DB::SELECT('SELECT id_rpjmd, id_renstra, id_unit, nomor_renstra, tanggal_renstra, uraian_renstra, nm_pimpinan, 
                    nip_pimpinan, jabatan_pimpinan, sumber_data, created_at, update_at
                    FROM trx_renstra_dokumen 
                    WHERE id_renstra='.$id_renstra);

            $unit = DB::SELECT('SELECT CONCAT(b.kd_urusan,".",RIGHT(CONCAT("0",b.kd_bidang),2),".",RIGHT(CONCAT("0",a.kd_unit),2)) AS kd_unit_display, 
                    a.id_unit, a.id_bidang, a.kd_unit, a.nm_unit
                    FROM ref_unit AS a INNER JOIN ref_bidang AS b ON a.id_bidang = b.id_bidang 
                    WHERE a.id_unit='.$id_unit);
                    
            return view('kin.pokin.FrmRenstraSasaranChart',['data' => $data,'dok' => $dok, 'unit' => $unit]);
    }
    
}