<?php

namespace App\Http\Controllers\Kin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use DB;
use Response;
use Session;
use Auth;
use CekAkses;
use Validator;
use App\Fungsi as Fungsi;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\RefUnit;
use App\Models\Kin\KinTrxCascadingProgramOpd;
use App\Models\Kin\KinTrxCascadingIndikatorProgramOpd;
use App\Models\Kin\KinTrxCascadingKegiatanOpd;
use App\Models\Kin\KinTrxCascadingIndikatorKegiatanOpd;

class TrxRenstraKinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
      return view('kin.cascading.index');
    }

    public function getSasaranRenstra($id_unit)
    {
        $sasaranrenstra=DB::SELECT('SELECT (@id:=@id+1) as no_urut, y.* 
            FROM (SELECT CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut) as kd_sasaran,d.id_sasaran_renstra,
            d.id_perubahan,d.uraian_sasaran_renstra, d.sumber_data,
            COALESCE((SELECT COUNT(x.id_hasil_program) AS jml_sasaran_program  FROM kin_trx_cascading_program_opd  AS x 
            WHERE x.id_renstra_sasaran= d.id_sasaran_renstra
            GROUP BY x.id_renstra_sasaran),0) AS jml_sasaran,
            COALESCE((SELECT COUNT(y.id_program_renstra) AS jml_sasaran_mapping FROM trx_renstra_program AS y
            LEFT OUTER JOIN kin_trx_cascading_program_opd  AS x ON y.id_program_renstra=x.id_renstra_program
            WHERE x.id_renstra_program IS NULL AND y.id_sasaran_renstra= d.id_sasaran_renstra
            GROUP BY y.id_sasaran_renstra ),0) AS jml_mapping 
            FROM trx_renstra_visi AS a
            INNER JOIN trx_renstra_misi AS b ON a.id_visi_renstra = b.id_visi_renstra
            INNER JOIN trx_renstra_tujuan AS c ON b.id_misi_renstra = c.id_misi_renstra
            INNER JOIN trx_renstra_sasaran AS d ON c.id_tujuan_renstra = d.id_tujuan_renstra
            WHERE a.id_unit='.$id_unit.' ORDER BY a.no_urut,b.no_urut,c.no_urut,d.no_urut ASC) AS y, (SELECT @id:=0) x' );

        return DataTables::of($sasaranrenstra)
        ->addColumn('details_url', function($sasaranrenstra) {
            return url('cascading/getIndikatorSasaran/'.$sasaranrenstra->id_sasaran_renstra);
        })
        ->addColumn('action', function ($sasaranrenstra) {
            return 
            '<button type="button" class="btn btn-lg btn-labeled btn-success btnLihatSasaranProg"><span class="btn-label"><i class="fa fa-list-alt fa-fw fa-lg"></i></span> Lihat Sasaran Program</button>';
            })
        ->make(true);
    }
    
    public function getSubUnit($id_sub_unit)
    {
        $subunit=DB::select('select id_sub_unit,nm_sub from ref_sub_unit
            where id_sub_unit <>'.$id_sub_unit);
            
        return json_encode($subunit);
    }

    public function getIndikatorSasaran($id_sasaran_renstra)
    {
        $indikatorProg=DB::SELECT('SELECT (@id:=@id+1) AS urut,a.thn_id, a.no_urut, a.id_sasaran_renstra, a.id_indikator_sasaran_renstra, 
        a.id_perubahan, a.kd_indikator, a.uraian_indikator_sasaran_renstra,id_indikator_sasaran_rpjmd,
        a.tolok_ukur_indikator, a.angka_awal_periode, a.angka_tahun1, a.angka_tahun2, 
        a.angka_tahun3, a.angka_tahun4, a.angka_tahun5, a.angka_akhir_periode,
        COALESCE(b.nm_indikator,"N/A") AS nm_indikator,COALESCE(c.uraian_satuan,"N/A") AS uraian_satuan
        FROM trx_renstra_sasaran_indikator AS a
        LEFT OUTER JOIN ref_indikator AS b ON a.kd_indikator = b.id_indikator
        LEFT OUTER JOIN ref_satuan AS c ON b.id_satuan_output = c.id_satuan,(SELECT @id:=0) x  WHERE a.id_sasaran_renstra='.$id_sasaran_renstra);

      return DataTables::of($indikatorProg)
        ->addColumn('action', function ($indikatorProg) {
            return'
                <button type="button" class="btn btn-labeled btn-success btnEditIndikatorSasaran"><span class="btn-label"><i class="fa fa-list-alt fa-fw fa-lg"></i></span> Lihat Indikator</button>
            ';
          })
        ->make(true);
    }

    public function getSasaranProgram($id_sasaran)
    {
        $sasaranprogram=DB::SELECT('SELECT (@id:=@id+1) AS no_urut, a.id_hasil_program, a.id_unit, a.id_renstra_sasaran, a.id_renstra_program, 
            b.uraian_program_renstra, a.uraian_hasil_program,
            CONCAT(f.no_urut,".",e.no_urut,".",d.no_urut,".",c.no_urut) as kd_sasaran,
            CONCAT(f.no_urut,".",e.no_urut,".",d.no_urut,".",c.no_urut,".",b.no_urut) as kd_program,
            CONCAT(h.kd_urusan,".",h.kd_bidang,".",g.kd_program) as kd_program_ref,
            COALESCE((SELECT COUNT(x.id_renstra_program_indikator) as jml_indikator FROM kin_trx_cascading_indikator_program_pd AS x
            WHERE x.id_hasil_program = a.id_hasil_program
            GROUP BY x.id_hasil_program),0) AS jml_indikator
            FROM kin_trx_cascading_program_opd AS a
            LEFT OUTER JOIN trx_renstra_program AS b ON a.id_renstra_program = b.id_program_renstra
            INNER JOIN trx_renstra_sasaran AS c ON b.id_sasaran_renstra = c.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan AS d ON c.id_tujuan_renstra = d.id_tujuan_renstra
            INNER JOIN trx_renstra_misi AS e ON d.id_misi_renstra = e.id_misi_renstra
            INNER JOIN trx_renstra_visi AS f ON e.id_visi_renstra = f.id_visi_renstra
            INNER JOIN ref_program AS g ON b.id_program_ref = g.id_program
            INNER JOIN ref_bidang AS h ON g.id_bidang = h.id_bidang,(SELECT @id:=0) x
            WHERE a.id_renstra_sasaran='.$id_sasaran );

        return DataTables::of($sasaranprogram)
        ->addColumn('details_url', function($sasaranprogram) {
            return url('cascading/getIndikatorProgram/'.$sasaranprogram->id_hasil_program);
        })
        ->addColumn('action', function ($sasaranprogram) {
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditHSP dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Lihat Sasaran Program </a>
                        </li>
                        <li>
                            <a class="btnHapusHSP dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Sasaran Program</a>
                        </li>
                        <li>
                            <a class="btnTambahIndikatorHSP dropdown-item"><i class="fa fa-plus fa-fw fa-lg text-success"></i> Tambah Indikator</a>
                        </li>
                    </ul>
                    </div>
                ';
            })
        ->make(true);
    }

    public function getIndikatorProgram($id_hasil_program)
    {
        $indikatorProg=DB::SELECT('SELECT (@id:=@id+1) AS urut,b.id_indikator_program_pd, b.id_hasil_program, b.id_renstra_program_indikator, a.kd_indikator, p.nm_indikator, 
            COALESCE(q.uraian_satuan,"Belum ada satuan") AS nama_satuan 
            FROM trx_renstra_program_indikator AS a
            INNER JOIN kin_trx_cascading_indikator_program_pd AS b ON a.id_indikator_program_renstra=b.id_renstra_program_indikator
            INNER JOIN ref_indikator AS p ON a.kd_indikator = p.id_indikator
            LEFT OUTER JOIN ref_satuan AS q ON p.id_satuan_output = q.id_satuan,(SELECT @id:=0) x  
            WHERE b.id_hasil_program ='.$id_hasil_program);

      return DataTables::of($indikatorProg)
        ->addColumn('action', function ($indikatorProg) {
            return'
                <button type="button" class="btn btn-labeled btn-danger btnHapusIndikatorSasaran"><span class="btn-label"><i class="fa fa-trash-o fa-fw fa-lg"></i></span> Hapus Indikator</button>
            ';
          })
        ->make(true);
    }

    public function getSasaranKegiatan($id_hasil_program)
    {
        $sasarankegiatan=DB::SELECT('SELECT (@id:=@id+1) AS no_urut, m.id_hasil_program, m.id_hasil_kegiatan, m.id_unit, m.id_renstra_kegiatan, 
            a.uraian_kegiatan_renstra, m.uraian_hasil_kegiatan,
            CONCAT(f.no_urut,".",e.no_urut,".",d.no_urut,".",c.no_urut,".",b.no_urut,".",a.no_urut) as kd_kegiatan,
            CONCAT(h.kd_urusan,".",h.kd_bidang,".",g.kd_program,".",i.kd_kegiatan) as kd_kegiatan_ref,
            COALESCE((SELECT COUNT(x.id_renstra_kegiatan_indikator) as jml_indikator FROM kin_trx_cascading_indikator_kegiatan_pd AS x
            WHERE x.id_hasil_kegiatan = m.id_hasil_kegiatan
            GROUP BY x.id_hasil_kegiatan),0) AS jml_indikator
            FROM kin_trx_cascading_kegiatan_opd AS m
            INNER JOIN trx_renstra_kegiatan AS a ON m.id_renstra_kegiatan = a.id_kegiatan_renstra
            INNER JOIN trx_renstra_program AS b ON a.id_program_renstra = b.id_program_renstra
            INNER JOIN trx_renstra_sasaran AS c ON b.id_sasaran_renstra = c.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan AS d ON c.id_tujuan_renstra = d.id_tujuan_renstra
            INNER JOIN trx_renstra_misi AS e ON d.id_misi_renstra = e.id_misi_renstra
            INNER JOIN trx_renstra_visi AS f ON e.id_visi_renstra = f.id_visi_renstra
            INNER JOIN ref_kegiatan AS i ON a.id_kegiatan_ref = i.id_kegiatan
            INNER JOIN ref_program AS g ON i.id_program = g.id_program
            INNER JOIN ref_bidang AS h ON g.id_bidang = h.id_bidang,(SELECT @id:=0) x
            WHERE m.id_hasil_program='.$id_hasil_program );

        return DataTables::of($sasarankegiatan)
        ->addColumn('details_url', function($sasarankegiatan) {
            return url('cascading/getIndikatorKegiatan/'.$sasarankegiatan->id_hasil_kegiatan);
        })
        ->addColumn('action', function ($sasarankegiatan) {
              return '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="btnEditHSK dropdown-item"><i class="fa fa-list-alt fa-fw fa-lg text-primary"></i> Lihat Sasaran Kegiatan </a>
                        </li>
                        <li>
                            <a class="btnHapusHSK dropdown-item"><i class="fa fa-trash-o fa-fw fa-lg text-danger"></i> Hapus Sasaran Kegiatan</a>
                        </li>
                        <li>
                            <a class="btnTambahIndikatorHSK dropdown-item"><i class="fa fa-plus fa-fw fa-lg text-success"></i> Tambah Indikator</a>
                        </li>
                    </ul>
                    </div>
                ';
            })
        ->make(true);
    }

    public function getIndikatorKegiatan($id_hasil_kegiatan)
    {
        $indikatorkeg=DB::SELECT('SELECT (@id:=@id+1) AS urut,b.id_indikator_kegiatan_pd, b.id_hasil_kegiatan, b.id_renstra_kegiatan_indikator, a.kd_indikator, p.nm_indikator, 
            COALESCE(q.uraian_satuan,"Belum ada satuan") AS nama_satuan 
            FROM trx_renstra_kegiatan_indikator AS a
            INNER JOIN kin_trx_cascading_indikator_kegiatan_pd AS b ON a.id_indikator_kegiatan_renstra=b.id_renstra_kegiatan_indikator
            INNER JOIN ref_indikator AS p ON a.kd_indikator = p.id_indikator
            LEFT OUTER JOIN ref_satuan AS q ON p.id_satuan_output = q.id_satuan,(SELECT @id:=0) x  
            WHERE b.id_hasil_kegiatan ='.$id_hasil_kegiatan);

      return DataTables::of($indikatorkeg)
        ->addColumn('action', function ($indikatorkeg) {
            return'
                <button type="button" class="btn btn-labeled btn-danger btnHapusIndikatorHSK"><span class="btn-label"><i class="fa fa-trash-o fa-fw fa-lg"></i></span> Hapus Indikator</button>
            ';
          })
        ->make(true);
    }

    public function getProgramRenstra($id_sasaran_renstra)
    {
        $indikatorProg=DB::SELECT('SELECT DISTINCT  (@id:=@id+1) as no_urut, CONCAT(f.no_urut,".",e.no_urut,".",d.no_urut,".",c.no_urut,".",y.no_urut) as kd_program,
            CONCAT(h.kd_urusan,".",h.kd_bidang,".",g.kd_program) as kd_program_ref,y.id_sasaran_renstra, y.id_program_renstra, y.uraian_program_renstra ,
            0 as flag
            FROM trx_renstra_program AS y
            LEFT OUTER JOIN kin_trx_cascading_program_opd  AS x ON y.id_program_renstra=x.id_renstra_program
            INNER JOIN trx_renstra_sasaran AS c ON y.id_sasaran_renstra = c.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan AS d ON c.id_tujuan_renstra = d.id_tujuan_renstra
            INNER JOIN trx_renstra_misi AS e ON d.id_misi_renstra = e.id_misi_renstra
            INNER JOIN trx_renstra_visi AS f ON e.id_visi_renstra = f.id_visi_renstra
            INNER JOIN ref_program AS g ON y.id_program_ref = g.id_program
            INNER JOIN ref_bidang AS h ON g.id_bidang = h.id_bidang,(SELECT @id:=0) m
            WHERE x.id_renstra_program IS NULL AND y.id_sasaran_renstra='.$id_sasaran_renstra);

      return DataTables::of($indikatorProg)->make(true);
    }

    public function getKegiatanRenstra($id_program_renstra)
    {
        $indikatorProg=DB::SELECT('SELECT DISTINCT  (@id:=@id+1) as no_urut, CONCAT(f.no_urut,".",e.no_urut,".",d.no_urut,".",c.no_urut,".",b.no_urut,".",y.no_urut) as kd_kegiatan,
            CONCAT(h.kd_urusan,".",h.kd_bidang,".",g.kd_program,".",i.kd_kegiatan) as kd_kegiatan_ref,y.id_program_renstra, y.id_kegiatan_renstra, y.uraian_kegiatan_renstra ,
            1 as flag
            FROM trx_renstra_kegiatan AS y
            LEFT OUTER JOIN kin_trx_cascading_kegiatan_opd  AS x ON y.id_kegiatan_renstra=x.id_renstra_kegiatan
            INNER JOIN trx_renstra_program AS b ON y.id_program_renstra = b.id_program_renstra
            INNER JOIN trx_renstra_sasaran AS c ON b.id_sasaran_renstra = c.id_sasaran_renstra
            INNER JOIN trx_renstra_tujuan AS d ON c.id_tujuan_renstra = d.id_tujuan_renstra
            INNER JOIN trx_renstra_misi AS e ON d.id_misi_renstra = e.id_misi_renstra
            INNER JOIN trx_renstra_visi AS f ON e.id_visi_renstra = f.id_visi_renstra
            INNER JOIN ref_kegiatan AS i ON y.id_kegiatan_ref = i.id_kegiatan
            INNER JOIN ref_program AS g ON i.id_program = g.id_program
            INNER JOIN ref_bidang AS h ON g.id_bidang = h.id_bidang,(SELECT @id:=0) m
            WHERE x.id_renstra_kegiatan IS NULL  AND y.id_program_renstra='.$id_program_renstra);

      return DataTables::of($indikatorProg)->make(true);
    }

    public function getProgramIndikatorRenstra($id_program_renstra)
    {
        $indikatorProg=DB::SELECT('SELECT DISTINCT  (@id:=@id+1) as no_urut, a.id_indikator_program_renstra, a.kd_indikator, p.nm_indikator, 
            COALESCE(q.uraian_satuan,"Belum ada satuan") AS nama_satuan, 0 as flag 
            FROM trx_renstra_program_indikator AS a
            LEFT OUTER JOIN kin_trx_cascading_indikator_program_pd AS b ON a.id_indikator_program_renstra=b.id_renstra_program_indikator
            INNER JOIN ref_indikator AS p ON a.kd_indikator = p.id_indikator
            LEFT OUTER JOIN ref_satuan AS q ON p.id_satuan_output = q.id_satuan,(SELECT @id:=0) m
            WHERE b.id_indikator_program_pd IS NULL AND a.id_program_renstra='.$id_program_renstra);

      return DataTables::of($indikatorProg)->make(true);
    }

    public function getKegiatanIndikatorRenstra($id_kegiatan_renstra)
    {
        $indikatorProg=DB::SELECT('SELECT DISTINCT  (@id:=@id+1) as no_urut, a.id_indikator_kegiatan_renstra, a.kd_indikator, p.nm_indikator, 
            COALESCE(q.uraian_satuan,"Belum ada satuan") AS nama_satuan, 1 as flag 
            FROM trx_renstra_kegiatan_indikator AS a
            LEFT OUTER JOIN kin_trx_cascading_indikator_kegiatan_pd AS b ON a.id_indikator_kegiatan_renstra=b.id_renstra_kegiatan_indikator
            INNER JOIN ref_indikator AS p ON a.kd_indikator = p.id_indikator
            LEFT OUTER JOIN ref_satuan AS q ON p.id_satuan_output = q.id_satuan,(SELECT @id:=0) m
            WHERE b.id_indikator_kegiatan_pd IS NULL AND a.id_kegiatan_renstra='.$id_kegiatan_renstra);

      return DataTables::of($indikatorProg)->make(true);
    }

    public function addHasilProgram(Request $request)
    {
        $rules = [
            'id_unit' => 'required',
            'id_renstra_sasaran' => 'required',
            'id_renstra_program'=> 'required',
            'uraian_hasil_program'=> 'required',
        ];
        $messages =[
            'id_unit.required' => 'Kode Unit Kosong',
            'id_renstra_sasaran.required' => 'Kode Sasaran Renstra Kosong',
            'id_renstra_program.required' => 'Kode Program Renstra Kosong',            
            'uraian_hasil_program.required' => 'Sasaran Program Kosong',  
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {
            $data = new KinTrxCascadingProgramOpd();
            $data->id_unit= $request->id_unit;
            $data->id_renstra_sasaran= $request->id_renstra_sasaran;
            $data->id_renstra_program= $request->id_renstra_program;
            $data->uraian_hasil_program= $request->uraian_hasil_program;
            try{
                $data->save (['timestamps' => true]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }
    
    public function editHasilProgram(Request $request)
    {
        $rules = [
            'id_hasil_program' => 'required',
            'id_unit' => 'required',
            'id_renstra_sasaran' => 'required',
            'id_renstra_program'=> 'required',
            'uraian_hasil_program'=> 'required',
        ];
        $messages =[
            'id_hasil_program.required' => 'ID Sasaran Program Kosong',
            'id_unit.required' => 'Kode Unit Kosong',
            'id_renstra_sasaran.required' => 'Kode Sasaran Renstra Kosong',            
            'id_renstra_program.required' => 'Kode Program Renstra Kosong',            
            'uraian_hasil_program.required' => 'Sasaran Program Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {
            $data = KinTrxCascadingProgramOpd::find($request->id_hasil_program);
            $data->id_unit= $request->id_unit;
            $data->id_renstra_sasaran= $request->id_renstra_sasaran;
            $data->id_renstra_program= $request->id_renstra_program;
            $data->uraian_hasil_program= $request->uraian_hasil_program;
            try{
                $data->save (['timestamps' => true]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }    

    public function delHasilProgram(Request $request){
        $rules = [
            'id_hasil_program'=> 'required',
        ];
        $messages =[
            'id_hasil_program.required'=> 'Sasaran Program Kosong',            
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {        
            $data = KinTrxCascadingProgramOpd::where('id_hasil_program',$request->id_hasil_program)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

    public function addIndikatorProgram(Request $request)
    {
        $rules = [
            'id_renstra_program_indikator' => 'required',
            'id_hasil_program'=> 'required',
        ];
        $messages =[
            'id_renstra_program_indikator.required' => 'Kode Indikator Program Renstra Kosong',            
            'id_hasil_program.required' => 'Sasaran Program Kosong',  
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {
            $data = new KinTrxCascadingIndikatorProgramOpd();
            $data->id_renstra_program_indikator= $request->id_renstra_program_indikator;
            $data->id_hasil_program= $request->id_hasil_program;
            try{
                $data->save (['timestamps' => true]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }

    public function delIndikatorProgram(Request $request){
        $rules = [
            'id_indikator_program_pd'=> 'required',
        ];
        $messages =[
            'id_indikator_program_pd.required'=> 'Sasaran Program Kosong',            
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {        
            $data = KinTrxCascadingIndikatorProgramOpd::where('id_indikator_program_pd',$request->id_indikator_program_pd)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

    public function addHasilKegiatan(Request $request)
    {
        $rules = [
            'id_unit' => 'required',
            'id_hasil_program' => 'required',
            'id_renstra_kegiatan'=> 'required',
            'uraian_hasil_kegiatan'=> 'required',
        ];
        $messages =[
            'id_unit.required' => 'Kode Unit Kosong',
            'id_hasil_program.required' => 'Kode Sasaran Program Kosong',
            'id_renstra_kegiatan.required' => 'Kode Kegiatan Renstra Kosong',            
            'uraian_hasil_kegiatan.required' => 'Sasaran Kegiatan Kosong',  
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {
            $data = new KinTrxCascadingKegiatanOpd();
            $data->id_unit= $request->id_unit;
            $data->id_hasil_program= $request->id_hasil_program;
            $data->id_renstra_kegiatan= $request->id_renstra_kegiatan;
            $data->uraian_hasil_kegiatan= $request->uraian_hasil_kegiatan;
            try{
                $data->save (['timestamps' => true]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }
    
    public function editHasilKegiatan(Request $request)
    {
        $rules = [
            'id_hasil_kegiatan' => 'required',
            'id_unit' => 'required',
            'id_hasil_program' => 'required',
            'id_renstra_kegiatan'=> 'required',
            'uraian_hasil_kegiatan'=> 'required',
        ];
        $messages =[
            'id_hasil_kegiatan.required' => 'ID Sasaran Kegiatan Kosong',
            'id_unit.required' => 'Kode Unit Kosong',
            'id_hasil_program.required' => 'Kode Sasaran Program Kosong',            
            'id_renstra_kegiatan.required' => 'Kode Kegiatan Renstra Kosong',            
            'uraian_hasil_kegiatan.required' => 'Sasaran Kegiatan Kosong',
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {
            $data = KinTrxCascadingKegiatanOpd::find($request->id_hasil_kegiatan);
            $data->id_unit= $request->id_unit;
            $data->id_hasil_program= $request->id_hasil_program;
            $data->id_renstra_kegiatan= $request->id_renstra_kegiatan;
            $data->uraian_hasil_kegiatan= $request->uraian_hasil_kegiatan;
            try{
                $data->save (['timestamps' => true]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }    

    public function delHasilKegiatan(Request $request){
        $rules = [
            'id_hasil_kegiatan'=> 'required',
        ];
        $messages =[
            'id_hasil_kegiatan.required'=> 'Sasaran Kegiatan Kosong',            
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {        
            $data = KinTrxCascadingKegiatanOpd::where('id_hasil_kegiatan',$request->id_hasil_kegiatan)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

    public function addIndikatorKegiatan(Request $request)
    {
        $rules = [
            'id_renstra_kegiatan_indikator' => 'required',
            'id_hasil_kegiatan'=> 'required',
        ];
        $messages =[
            'id_renstra_kegiatan_indikator.required' => 'Kode Indikator Kegiatan Renstra Kosong',            
            'id_hasil_kegiatan.required' => 'Sasaran Kegiatanm Kosong',  
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {
            $data = new KinTrxCascadingIndikatorKegiatanOpd();
            $data->id_renstra_kegiatan_indikator= $request->id_renstra_kegiatan_indikator;
            $data->id_hasil_kegiatan= $request->id_hasil_kegiatan;
            try{
                $data->save (['timestamps' => true]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
              catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        }
    }

    public function delIndikatorKegiatan(Request $request){
        $rules = [
            'id_indikator_kegiatan_pd'=> 'required',
        ];
        $messages =[
            'id_indikator_kegiatan_pd.required'=> 'Sasaran Kegiatan Kosong',            
        ];
        $validation = Validator::make($request->all(),$rules,$messages);
        
        if($validation->fails()) {
            $errors = Fungsi::validationErrorsToString($validation->errors());
            return response ()->json (['pesan'=>$errors,'status_pesan'=>'0']);          
            }
        else {        
            $data = KinTrxCascadingIndikatorKegiatanOpd::where('id_indikator_kegiatan_pd',$request->id_indikator_kegiatan_pd)->delete();

            if($data != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
            } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
            }  
        } 
    }

} //end file
