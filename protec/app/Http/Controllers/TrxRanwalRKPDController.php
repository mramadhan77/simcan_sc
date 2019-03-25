<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;
use Datatables;
use Session;
use Response;
use Validator;
use Auth;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\TrxRkpdRanwal;
use App\Models\TrxRkpdRanwalPelaksana;
use App\Models\TrxRkpdRanwalIndikator;
use App\Models\TrxRkpdRanwalUrusan;


class TrxRanwalRKPDController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function CheckProgram($id_program)
    {
       $CheckProgram=DB::SELECT('SELECT * FROM trx_rkpd_ranwal
            WHERE id_rkpd_ranwal = '.$id_program.' LIMIT 1');
       return ($CheckProgram);
    }

    public function getCekProgram($id_ranwal){
    $CekProgram=DB::select('SELECT a.id_rkpd_ranwal,a.tahun_rkpd,IFNULL(d.jml_indi,0) as jml_indikator, 
          IFNULL(d.jml_0,0) as indikator_0, IFNULL(e.jml_data,0) as jml_unit, IFNULL(e.jml_0,0) as unit_0
          FROM trx_rkpd_ranwal a 
          LEFT OUTER JOIN (SELECT b.tahun_rkpd, b.id_rkpd_ranwal, count(*) as jml_indi, a.jml_0 
          FROM trx_rkpd_ranwal_indikator b
          LEFT OUTER JOIN (SELECT tahun_rkpd, id_rkpd_ranwal, count(id_indikator_program_rkpd) as jml_0 
          FROM trx_rkpd_ranwal_indikator
          WHERE status_data=1
          GROUP BY tahun_rkpd, id_rkpd_ranwal) a ON b.tahun_rkpd=a.tahun_rkpd AND b.id_rkpd_ranwal = a.id_rkpd_ranwal
          GROUP BY b.tahun_rkpd, b.id_rkpd_ranwal, a.jml_0) d ON a.tahun_rkpd=d.tahun_rkpd AND a.id_rkpd_ranwal = d.id_rkpd_ranwal
          LEFT OUTER JOIN (SELECT a.tahun_rkpd, a.id_rkpd_ranwal, COUNT(*) as jml_data, b.jml_0
          FROM trx_rkpd_ranwal_pelaksana a
          LEFT OUTER JOIN (SELECT tahun_rkpd, id_rkpd_ranwal, COUNT(*) as jml_0
          FROM trx_rkpd_ranwal_pelaksana
          WHERE status_data=1
          GROUP BY tahun_rkpd, id_rkpd_ranwal) b ON a.tahun_rkpd=b.tahun_rkpd AND a.id_rkpd_ranwal = b.id_rkpd_ranwal
          GROUP BY a.tahun_rkpd,a.id_rkpd_ranwal, b.jml_0) e ON a.tahun_rkpd=e.tahun_rkpd AND a.id_rkpd_ranwal = e.id_rkpd_ranwal
          WHERE a.id_rkpd_ranwal = '.$id_ranwal.' LIMIT 1');

    return $CekProgram;
    }

    public function getCekIndikkator($id_ranwal){
    $result=DB::select('SELECT a.tahun_rkpd, a.id_rkpd_ranwal,sum(a.target_rkpd) as jml_target
            FROM trx_rkpd_ranwal_indikator a 
            WHERE a.id_rkpd_ranwal='.$id_ranwal.' GROUP BY a.tahun_rkpd, a.id_rkpd_ranwal');
    return $result;
    }

    public function getData(Request $req)
    {
        if ($req->id_x == 'pdt'){
            $index_x = '(b.no_urut = 98)';
        } else {
            if ($req->id_x == 'btl'){
                $index_x = '(b.no_urut = 99)';
            } else {                
                $index_x = '((b.no_urut <> 99 and b.no_urut <> 98) OR b.no_urut is Null)';
            }
        };
        
        
        $dataranwal=DB::Select('SELECT (@id:=@id+1) as urut,a.* FROM (SELECT a.id_rkpd_ranwal,a.tahun_rkpd,a.id_rkpd_rpjmd,a.thn_id_rpjmd,a.id_visi_rpjmd,a.id_misi_rpjmd,b.no_urut as no_misi ,a.id_tujuan_rpjmd,a.id_sasaran_rpjmd,a.id_program_rpjmd,a.uraian_program_rpjmd,a.pagu_rpjmd,a.pagu_ranwal,a.keterangan_program,a.status_pelaksanaan,a.status_data,a.ket_usulan,a.jenis_belanja,
          CASE a.status_data
          WHEN 0 THEN "Draft"
          WHEN 1 THEN "Posting"
          END AS ur_usulan,
          a.sumber_data,        
          CASE a.sumber_data 
          WHEN 0 THEN "RPJMD" 
          WHEN 1 THEN "Baru" 
          WHEN 2 THEN "Tahun Sebelumnya" 
          END AS sumber_display,c.uraian_program_rpjmd as "program_pemda", IFNULL(d.jml_indi,0) as jml_indikator, 
          IFNULL(d.jml_0,0) as indikator_0, IFNULL(e.jml_data,0) as jml_unit, IFNULL(e.jml_0,0) as unit_0,
          CASE a.status_data
              WHEN 0 THEN "fa fa-question"
              WHEN 1 THEN "fa fa-check-square-o"
              WHEN 2 THEN "fa fa-thumbs-o-up"
              ELSE "fa fa-exclamation"
          END AS status_icon,
          CASE a.status_data
              WHEN 0 THEN "red"
              WHEN 1 THEN "green"
              WHEN 2 THEN "blue"
              ELSE "red"
          END AS warna,        
          CASE a.status_pelaksanaan 
              WHEN 0 THEN "Tepat Waktu" 
              WHEN 1 THEN "Dimajukan" 
              WHEN 2 THEN "Ditunda" 
              WHEN 3 THEN "Dibatalkan"  
              WHEN 5 THEN "Tanpa Anggaran"  
          END AS pelaksanaan_display       
          FROM trx_rkpd_ranwal a
          LEFT OUTER JOIN trx_rpjmd_misi b ON  a.id_misi_rpjmd = b.id_misi_rpjmd and a.id_visi_rpjmd = b.id_visi_rpjmd
          LEFT OUTER JOIN trx_rpjmd_program c ON a.id_sasaran_rpjmd = c.id_sasaran_rpjmd AND a.id_program_rpjmd = c.id_program_rpjmd
          LEFT OUTER JOIN (SELECT b.tahun_rkpd, b.id_rkpd_ranwal, count(*) as jml_indi, a.jml_0 
          FROM trx_rkpd_ranwal_indikator b
          LEFT OUTER JOIN (SELECT tahun_rkpd, id_rkpd_ranwal, count(id_indikator_program_rkpd) as jml_0 
          FROM trx_rkpd_ranwal_indikator
          WHERE status_data=1
          GROUP BY tahun_rkpd, id_rkpd_ranwal) a ON b.tahun_rkpd=a.tahun_rkpd AND b.id_rkpd_ranwal = a.id_rkpd_ranwal
          GROUP BY b.tahun_rkpd, b.id_rkpd_ranwal, a.jml_0) d ON a.tahun_rkpd=d.tahun_rkpd AND a.id_rkpd_ranwal = d.id_rkpd_ranwal
          LEFT OUTER JOIN (SELECT a.tahun_rkpd, a.id_rkpd_ranwal, COUNT(*) as jml_data, b.jml_0
          FROM trx_rkpd_ranwal_pelaksana a
          LEFT OUTER JOIN (SELECT tahun_rkpd, id_rkpd_ranwal, COUNT(*) as jml_0
          FROM trx_rkpd_ranwal_pelaksana
          WHERE status_data= 1
          GROUP BY tahun_rkpd, id_rkpd_ranwal) b ON a.tahun_rkpd=b.tahun_rkpd AND a.id_rkpd_ranwal = b.id_rkpd_ranwal
          GROUP BY a.tahun_rkpd,a.id_rkpd_ranwal, b.jml_0) e ON a.tahun_rkpd=e.tahun_rkpd AND a.id_rkpd_ranwal = e.id_rkpd_ranwal
       WHERE '.$index_x.' and a.tahun_rkpd='.Session::get('tahun').') a,(SELECT @id:=0) var_no');

      return DataTables::of($dataranwal)
      ->addColumn('action', function ($dataranwal) {
          if ($dataranwal->status_data==0)
            return '                         
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-indikator dropdown-item"><i class="fa fa-bullseye fa-fw fa-lg"></i> Lihat Indikator Program RKPD</a>
                        </li>
                        <li>
                            <a class="view-pelaksana dropdown-item"><i class="fa fa-male fa-fw fa-lg"></i> Lihat Pelaksana Program RKPD</a>
                        </li>
                        <li>
                            <a class="edit-program dropdown-item"><i class="fa fa-pencil fa-fw fa-lg"></i> Ubah Program RKPD</a>
                        </li>
                        <li>
                            <a id="btnUnProgram" class="dropdown-item"><i class="fa fa-check-square-o fa-fw fa-lg"></i> Posting Program</a>
                        </li>                          
                    </ul>
                </div>
            ';
          if ($dataranwal->status_data==1)
            return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="dropdown-item view-indikator dropdown-item"><i class="fa fa-bullseye fa-fw fa-lg" aria-hidden="true"></i> Lihat Indikator Program RKPD</a>
                        </li>
                        <li>
                            <a class="dropdown-item view-pelaksana dropdown-item"><i class="fa fa-male fa-fw fa-lg" aria-hidden="true"></i> Lihat Pelaksana Program RKPD</a>
                        </li>
                        <li>
                            <a id="btnUnProgram" class="dropdown-item"><i class="fa fa-times fa-fw fa-lg"></i> Un-Posting Program</a>
                        </li>                         
                    </ul>
              </div>
            ';
            if ($dataranwal->status_data==2)
            return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-indikator dropdown-item"><i class="fa fa-bullseye fa-fw fa-lg"></i> Lihat Indikator Program RKPD</a>
                        </li>
                        <li>
                            <a class="view-pelaksana dropdown-item"><i class="fa fa-male fa-fw fa-lg"></i> Lihat Pelaksana Program RKPD</a>
                        </li>                         
                    </ul>
              </div>
            ';
        })
      ->make(true);
    }

    public function getRePostingData($tahun)
    {
        // $jmldata=DB::select('SELECT (@id:=@id+1) as no_urut, b.id_rkpd_rpjmd, b.tahun_rkpd,b.id_program_rpjmd,c.uraian_program_rpjmd
        //         FROM trx_rkpd_ranwal AS a
        //         RIGHT OUTER JOIN trx_rkpd_rpjmd_ranwal AS b ON a.id_rkpd_rpjmd = b.id_rkpd_rpjmd AND a.tahun_rkpd = b.tahun_rkpd
        //         LEFT OUTER JOIN trx_rpjmd_program c ON b.id_sasaran_rpjmd = c.id_sasaran_rpjmd AND b.id_program_rpjmd = c.id_program_rpjmd,
        //         (SELECT @id:=0) d 
        //         WHERE a.tahun_rkpd is null and b.tahun_rkpd='.$tahun);
        
        $jmldata=DB::SELECT('SELECT (@id:=@id+1) as no_urut, c.id_program_rpjmd, '.$tahun.' as tahun_rkpd, c.uraian_program_rpjmd
                FROM (SELECT * FROM trx_rkpd_ranwal WHERE tahun_rkpd='.$tahun.') AS a
                RIGHT OUTER JOIN trx_rpjmd_program c ON a.id_program_rpjmd = c.id_program_rpjmd,
                (SELECT @id:=0) d WHERE a.uraian_program_rpjmd is null');

        return DataTables::of($jmldata)
        ->addColumn('action',function($jmldata){
          return '
              <button id="btnReLoad" type="button" data-toggle="popover" data-trigger="hover" data-contadata-html="true" data-content="Load Ulang Program Ranwal RKPD" title="Load Ulang Program Ranwal RKPD" class="btn btn-primary"><i class="fa fa-download fa-fw fa-lg"></i> Load Data</button>
          ' ;})
        ->make(true);
    }

    public function getJmlData()
    {
        $jmldata=DB::SELECT('SELECT '.Session::get('tahun').' as tahun_rkpd, COUNT(COALESCE(a.id_rkpd_rpjmd,0)) as jml_data
            FROM  trx_rkpd_rpjmd_ranwal AS b
            INNER JOIN trx_rkpd_ranwal AS a ON a.id_rkpd_rpjmd = b.id_rkpd_rpjmd AND a.tahun_rkpd = b.tahun_rkpd
            WHERE b.tahun_rkpd='.Session::get('tahun'));

        return json_encode($jmldata);
    }

    public function getDataRekap($tahun)
    {
        $dataranwal=DB::Select('SELECT (@id:=@id+1) as urut,a.* FROM (SELECT a.id_rkpd_ranwal,a.tahun_rkpd,a.id_rkpd_rpjmd,a.thn_id_rpjmd,a.id_visi_rpjmd,a.id_misi_rpjmd,b.no_urut as no_misi ,a.id_tujuan_rpjmd,a.id_sasaran_rpjmd,a.id_program_rpjmd,a.uraian_program_rpjmd,a.pagu_rpjmd,a.pagu_ranwal,a.keterangan_program,a.status_pelaksanaan,a.status_data,a.ket_usulan,
          CASE a.status_data
          WHEN 0 THEN "Draft"
          WHEN 1 THEN "Posting"
          END AS ur_usulan,
          a.sumber_data,        
          CASE a.sumber_data 
          WHEN 0 THEN "RPJMD" 
          WHEN 1 THEN "Baru" 
          WHEN 2 THEN "Tahun Sebelumnya" 
          END AS sumber_display,c.uraian_program_rpjmd as "program_pemda", IFNULL(d.jml_indi,0) as jml_indikator, 
          IFNULL(d.jml_0,0) as indikator_0, IFNULL(e.jml_data,0) as jml_unit, IFNULL(e.jml_0,0) as unit_0,
          CASE a.status_data
              WHEN 0 THEN "fa fa-question"
              WHEN 1 THEN "fa fa-check-square-o"
          END AS status_icon,
          CASE a.status_data
              WHEN 0 THEN "red"
              WHEN 1 THEN "green"
          END AS warna  
          FROM trx_rkpd_ranwal a
          LEFT OUTER JOIN trx_rpjmd_misi b ON  a.id_misi_rpjmd = b.id_misi_rpjmd and a.id_visi_rpjmd = b.id_visi_rpjmd
          LEFT OUTER JOIN trx_rpjmd_program c ON a.id_sasaran_rpjmd = c.id_sasaran_rpjmd AND a.id_program_rpjmd = c.id_program_rpjmd
          LEFT OUTER JOIN (SELECT b.tahun_rkpd, b.id_rkpd_ranwal, count(*) as jml_indi, a.jml_0 
          FROM trx_rkpd_ranwal_indikator b
          LEFT OUTER JOIN (SELECT tahun_rkpd, id_rkpd_ranwal, count(id_indikator_program_rkpd) as jml_0 
          FROM trx_rkpd_ranwal_indikator
          WHERE status_data=1
          GROUP BY tahun_rkpd, id_rkpd_ranwal) a ON b.tahun_rkpd=a.tahun_rkpd AND b.id_rkpd_ranwal = a.id_rkpd_ranwal
          GROUP BY b.tahun_rkpd, b.id_rkpd_ranwal, a.jml_0) d ON a.tahun_rkpd=d.tahun_rkpd AND a.id_rkpd_ranwal = d.id_rkpd_ranwal
          LEFT OUTER JOIN (SELECT a.tahun_rkpd, a.id_rkpd_ranwal, COUNT(*) as jml_data, b.jml_0
          FROM trx_rkpd_ranwal_pelaksana a
          LEFT OUTER JOIN (SELECT tahun_rkpd, id_rkpd_ranwal, COUNT(*) as jml_0
          FROM trx_rkpd_ranwal_pelaksana
          WHERE status_data= 1
          GROUP BY tahun_rkpd, id_rkpd_ranwal) b ON a.tahun_rkpd=b.tahun_rkpd AND a.id_rkpd_ranwal = b.id_rkpd_ranwal
          GROUP BY a.tahun_rkpd,a.id_rkpd_ranwal, b.jml_0) e ON a.tahun_rkpd=e.tahun_rkpd AND a.id_rkpd_ranwal = e.id_rkpd_ranwal
       WHERE a.tahun_rkpd='.$tahun.') a,(SELECT @id:=0) var_no');

      return DataTables::of($dataranwal)
      ->addColumn('action', function ($dataranwal) {
        return '<a class="btnUnload btn btn-labeled btn-danger" data-toggle="tooltip" title="Unload Program RKPD" data-id_rkpd_ranwal="'.$dataranwal->id_rkpd_ranwal.'" data-tahun_rkpd="'.$dataranwal->tahun_rkpd.'" data-uraian_program_rpjmd="'.$dataranwal->uraian_program_rpjmd.'"><span class="btn-label"><i class="fa fa-chain-broken fa-fw fa-lg"></i></span> Unload Data</a>';
        })
      ->make(true);
    }   

    public function unLoadProgramRkpd(Request $req){

      $data_cek=$this->getChildRenja($req->id_ranwal);

      if($data_cek[0]->flag == 0){
            try{
                $result=DB::DELETE('DELETE FROM trx_rkpd_ranwal 
                WHERE tahun_rkpd = '.$req->tahun.' AND id_rkpd_ranwal='.$req->id_ranwal);
            return response ()->json (['pesan'=>'Data Program Berhasil Unload','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Program Gagal Unload ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'Data Program Gagal Unload. Silahkan Cek Program Renja, Program RKPD masih ada digunakan.. !','status_pesan'=>'0']);
        }

    }

    public function getChildRenja($id_rkpd_ranwal){
      $getChildRenja=DB::select('SELECT DISTINCT b.id_rkpd_ranwal, COALESCE(c.flag,0) AS flag FROM trx_rkpd_ranwal b 
            LEFT OUTER JOIN trx_rkpd_ranwal_dokumen c ON b.id_dokumen = c.id_dokumen_ranwal 
            WHERE b.id_rkpd_ranwal='.$id_rkpd_ranwal);
      return $getChildRenja;
    }

    public function getUrusan(){
      $urusan=DB::select('SELECT kd_urusan, nm_urusan FROM ref_urusan');
        return json_encode($urusan);
    }

    public function getBidang($kd_urusan){
      $urusan=DB::select('SELECT id_bidang, kd_urusan, kd_bidang, nm_bidang, kd_fungsi
          FROM ref_bidang WHERE kd_urusan='.$kd_urusan.'');
          return json_encode($urusan);
    }

    public function getCheck($id_rkpd_ranwal){
      $checkData=DB::select('SELECT a.id_rkpd_ranwal,a.tahun_rkpd,IFNULL(d.jml_indi,0) as jml_indikator, 
          IFNULL(d.jml_0,0) as indikator_0, IFNULL(e.jml_data,0) as jml_unit, IFNULL(e.jml_0,0) as unit_0
          FROM trx_rkpd_ranwal a 
          LEFT OUTER JOIN (SELECT b.tahun_rkpd, b.id_rkpd_ranwal, count(*) as jml_indi, a.jml_0 
          FROM trx_rkpd_ranwal_indikator b
          LEFT OUTER JOIN (SELECT tahun_rkpd, id_rkpd_ranwal, count(id_indikator_program_rkpd) as jml_0 
          FROM trx_rkpd_ranwal_indikator
          WHERE status_data=0
          GROUP BY tahun_rkpd, id_rkpd_ranwal) a ON b.tahun_rkpd=a.tahun_rkpd AND b.id_rkpd_ranwal = a.id_rkpd_ranwal
          GROUP BY b.tahun_rkpd, b.id_rkpd_ranwal, a.jml_0) d ON a.tahun_rkpd=d.tahun_rkpd AND a.id_rkpd_ranwal = d.id_rkpd_ranwal
          LEFT OUTER JOIN (SELECT a.tahun_rkpd, a.id_rkpd_ranwal, COUNT(*) as jml_data, b.jml_0
          FROM trx_rkpd_ranwal_pelaksana a
          LEFT OUTER JOIN (SELECT tahun_rkpd, id_rkpd_ranwal, COUNT(*) as jml_0
          FROM trx_rkpd_ranwal_pelaksana
          WHERE status_data= 0
          GROUP BY tahun_rkpd, id_rkpd_ranwal) b ON a.tahun_rkpd=b.tahun_rkpd AND a.id_rkpd_ranwal = b.id_rkpd_ranwal
          GROUP BY a.tahun_rkpd,a.id_rkpd_ranwal, b.jml_0) e ON a.tahun_rkpd=e.tahun_rkpd AND a.id_rkpd_ranwal = e.id_rkpd_ranwal
          WHERE a.id_rkpd_ranwal='.$id_rkpd_ranwal);

          return $checkData;
    }

    public function getIndikatorRKPD($id_rkpd)
    {
      $indikatorRKPD=DB::select('SELECT a.tahun_rkpd,(@id:=@id+1) as urut,a.id_rkpd_ranwal,a.id_indikator_program_rkpd,a.id_perubahan,a.kd_indikator,a.uraian_indikator_program_rkpd,
            a.tolok_ukur_indikator,a.target_rpjmd,a.target_rkpd,a.status_data,a.sumber_data,a.indikator_output,a.id_satuan_output, b.status_data as status_data_program,b.status_pelaksanaan as status_program,
            CASE a.status_data
              WHEN 0 THEN "fa fa-question"
              WHEN 1 THEN "fa fa-check-square-o"
            END AS status_reviu,
          CASE a.status_data
              WHEN 0 THEN "red"
              WHEN 1 THEN "green"
          END AS warna  
            FROM trx_rkpd_ranwal_indikator a
            INNER JOIN trx_rkpd_ranwal b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal AND a.tahun_rkpd = b.tahun_rkpd,(SELECT @id:=0) var_no where a.id_rkpd_ranwal='.$id_rkpd);

      return DataTables::of($indikatorRKPD)
        ->addColumn('action', function ($indikatorRKPD) {
          if($indikatorRKPD->status_data_program==0 ){
          if($indikatorRKPD->status_program!=2 && $indikatorRKPD->status_program!=3){
            return '<a class="edit-indikator btn btn-labeled btn-primary" data-toggle="tooltip" title="Edit Indikator Program RKPD" data-id_indikator_ranwal="'.$indikatorRKPD->id_indikator_program_rkpd.'" data-id_rkpd_ranwal="'.$indikatorRKPD->id_rkpd_ranwal.'" data-no_urut="'.$indikatorRKPD->urut.'" data-ur_indikator_rkpd="'.$indikatorRKPD->uraian_indikator_program_rkpd.'" data-ur_toloukur_rkpd="'.$indikatorRKPD->tolok_ukur_indikator.'" data-kd_indikator_rkpd="'.$indikatorRKPD->kd_indikator.'" data-status_data="'.$indikatorRKPD->status_data.'" data-sumber_data="'.$indikatorRKPD->sumber_data.'" data-target_rpjmd="'.$indikatorRKPD->target_rpjmd.'" data-target_rkpd="'.$indikatorRKPD->target_rkpd.'"><span class="btn-label"><i class="fa fa-pencil-square-o fa-fw fa-lg"></i></span> Edit Indikator</a>';
            }
            }
          })
        ->make(true);

    }

    public function getUrusanRKPD($id_rkpd)
    {
      $urusanRKPD=DB::select('SELECT (@id:=@id+1) as urut, x.* FROM ( 
                SELECT a.tahun_rkpd, a.no_urut, a.id_rkpd_ranwal,a.id_urusan_rkpd, 
                a.id_bidang, b.kd_bidang, b.nm_bidang, c.kd_urusan, c.nm_urusan, a.sumber_data,d.status_data as status_data_program,d.status_pelaksanaan as status_program, IFNULL(e.jml_data,0) as jml_data, IFNULL(e.jml_0,0) as jml_0 
                FROM trx_rkpd_ranwal_urusan a
                INNER JOIN ref_bidang b on a.id_bidang = b.id_bidang
                INNER JOIN ref_urusan c on b.kd_urusan = c.kd_urusan
                INNER JOIN trx_rkpd_ranwal d on a.id_rkpd_ranwal = d.id_rkpd_ranwal
                LEFT OUTER JOIN (SELECT a.tahun_rkpd, a.id_rkpd_ranwal, a.id_urusan_rkpd,COUNT(*) as jml_data, b.jml_0
                FROM trx_rkpd_ranwal_pelaksana a
                LEFT OUTER JOIN (SELECT tahun_rkpd, id_rkpd_ranwal, id_urusan_rkpd, COUNT(*) as jml_0
                FROM trx_rkpd_ranwal_pelaksana
                WHERE status_data= 1
                GROUP BY tahun_rkpd, id_rkpd_ranwal,id_urusan_rkpd) b ON a.tahun_rkpd=b.tahun_rkpd AND a.id_rkpd_ranwal = b.id_rkpd_ranwal AND a.id_urusan_rkpd = b.id_urusan_rkpd
                GROUP BY a.tahun_rkpd,a.id_rkpd_ranwal, a.id_urusan_rkpd, b.jml_0) e ON a.tahun_rkpd=e.tahun_rkpd AND a.id_rkpd_ranwal = e.id_rkpd_ranwal AND a.id_urusan_rkpd = e.id_urusan_rkpd) x, 
                (SELECT @id:=0) var_no where x.id_rkpd_ranwal='.$id_rkpd.' order By kd_urusan, kd_bidang');

      return DataTables::of($urusanRKPD)
        ->addColumn('action', function ($urusanRKPD) {
          if($urusanRKPD->status_program!=2 && $urusanRKPD->status_program!=3){
          if ($urusanRKPD->status_data_program==0) {
            if ($urusanRKPD->sumber_data==1) {
            return '
            <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="view-unit dropdown-item" data-nm_bidang="'.$urusanRKPD->nm_bidang.'" data-id_rkpd_ranwal="'.$urusanRKPD->id_rkpd_ranwal.'" data-nm_urusan="'.$urusanRKPD->nm_urusan.'" data-id_urusan_rkpd="'.$urusanRKPD->id_urusan_rkpd.'" data-sumber_data="'.$urusanRKPD->sumber_data.'" data-status_data_program="'.$urusanRKPD->status_data_program.'" data-status_program="'.$urusanRKPD->status_program.'"><i class="fa fa-eye fa-fw fa-lg"></i> Lihat Unit</a>
                        </li>
                        <li>
                            <a class="hapus-urusan dropdown-item" href="#" data-toggle="tooltip" title="Hapus Urusan Program RKPD" data-nm_bidang="'.$urusanRKPD->nm_bidang.'" data-id_rkpd_ranwal="'.$urusanRKPD->id_rkpd_ranwal.'" data-nm_urusan="'.$urusanRKPD->nm_urusan.'" data-id_urusan_rkpd="'.$urusanRKPD->id_urusan_rkpd.'" data-sumber_data="'.$urusanRKPD->sumber_data.'"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus</a>
                        </li>                        
                    </ul>
              </div>            
              ';
              }
            if ($urusanRKPD->sumber_data==0) {
            return '<a class="view-unit btn btn-labeled btn-success" data-nm_bidang="'.$urusanRKPD->nm_bidang.'" data-id_rkpd_ranwal="'.$urusanRKPD->id_rkpd_ranwal.'" data-nm_urusan="'.$urusanRKPD->nm_urusan.'" data-id_urusan_rkpd="'.$urusanRKPD->id_urusan_rkpd.'" data-sumber_data="'.$urusanRKPD->sumber_data.'" data-status_data_program="'.$urusanRKPD->status_data_program.'" data-status_program="'.$urusanRKPD->status_program.'"><span class="btn-label"><i class="fa fa-eye fa-fw fa-lg"></i></span> Lihat Unit</a>';
              }
            }
            if ($urusanRKPD->status_data_program==1) {
            return '<a class="view-unit btn btn-labeled btn-success" data-nm_bidang="'.$urusanRKPD->nm_bidang.'" data-id_rkpd_ranwal="'.$urusanRKPD->id_rkpd_ranwal.'" data-nm_urusan="'.$urusanRKPD->nm_urusan.'" data-id_urusan_rkpd="'.$urusanRKPD->id_urusan_rkpd.'" data-sumber_data="'.$urusanRKPD->sumber_data.'" data-status_data_program="'.$urusanRKPD->status_data_program.'" data-status_program="'.$urusanRKPD->status_program.'"><span class="btn-label"><i class="fa fa-eye fa-fw fa-lg"></i></span> Lihat Unit</a>';
              }

          }
          
          })
        ->make(true);

    }

    public function getPelaksanaRKPD($id_rkpd,$id_urusan)
    {
      $pelaksanaRKPD=DB::select('SELECT (@id:=@id+1) as urut,a.id_unit,a.tahun_rkpd,a.id_rkpd_ranwal,a.id_urusan_rkpd,a.id_pelaksana_rpjmd,a.status_data,
      if(a.sumber_data=0,"RPJMD","Baru") as keterangan,b.kd_unit,b.nm_unit,a.sumber_data,a.status_pelaksanaan, a.hak_akses, a.ket_pelaksanaan,
      c.status_data as status_data_program,c.status_pelaksanaan as status_program,
            CASE a.status_data
              WHEN 0 THEN "fa fa-question"
              WHEN 1 THEN "fa fa-check-square-o"
            END AS status_reviu,
          CASE a.status_data
              WHEN 0 THEN "red"
              WHEN 1 THEN "green"
          END AS warna  
            FROM trx_rkpd_ranwal_pelaksana AS a
            INNER JOIN trx_rkpd_ranwal c ON a.id_rkpd_ranwal = c.id_rkpd_ranwal AND a.tahun_rkpd = c.tahun_rkpd 
            INNER JOIN ref_unit AS b ON a.id_unit = b.id_unit,(SELECT @id:=0) var_no where a.id_rkpd_ranwal='.$id_rkpd.' AND a.id_urusan_rkpd='.$id_urusan);

      return DataTables::of($pelaksanaRKPD)
        ->addColumn('action', function ($pelaksanaRKPD) {
          if ($pelaksanaRKPD->status_data_program==0) {
            if($pelaksanaRKPD->status_program!=2 && $pelaksanaRKPD->status_program!=3){
              if($pelaksanaRKPD->sumber_data==1){
              return '
              <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="edit-pelaksana dropdown-item" data-id_pelaksana_ranwal="'.$pelaksanaRKPD->id_pelaksana_rpjmd.'" data-no_urut="'.$pelaksanaRKPD->urut.'" data-id_unit="'.$pelaksanaRKPD->id_unit.'" data-id_urusan_rkpd="'.$pelaksanaRKPD->id_urusan_rkpd.'" data-nm_unit="'.$pelaksanaRKPD->nm_unit.'" data-status_data="'.$pelaksanaRKPD->status_data.'" data-sumber_data="'.$pelaksanaRKPD->sumber_data.'" data-id_rkpd_ranwal="'.$pelaksanaRKPD->id_rkpd_ranwal.'" data-hak_akses="'.$pelaksanaRKPD->hak_akses.'" data-ket_pelaksanaan="'.$pelaksanaRKPD->ket_pelaksanaan.'" data-status_pelaksanaan="'.$pelaksanaRKPD->status_pelaksanaan.'" data-status_program="'.$pelaksanaRKPD->status_program.'"><i class="fa fa-trash fa-fw fa-lg"></i> Edit Pelaksana</a>
                        </li>
                        <li>
                            <a class="hapus-pelaksana dropdown-item" data-id_pelaksana_ranwal="'.$pelaksanaRKPD->id_pelaksana_rpjmd.'" data-no_urut="'.$pelaksanaRKPD->urut.'" data-id_unit="'.$pelaksanaRKPD->id_unit.'" data-id_urusan_rkpd="'.$pelaksanaRKPD->id_urusan_rkpd.'" data-nm_unit="'.$pelaksanaRKPD->nm_unit.'" data-status_data="'.$pelaksanaRKPD->status_data.'" data-sumber_data="'.$pelaksanaRKPD->sumber_data.'" data-id_rkpd_ranwal="'.$pelaksanaRKPD->id_rkpd_ranwal.'" data-hak_akses="'.$pelaksanaRKPD->hak_akses.'" data-ket_pelaksanaan="'.$pelaksanaRKPD->ket_pelaksanaan.'" data-status_pelaksanaan="'.$pelaksanaRKPD->status_pelaksanaan.'" data-status_program="'.$pelaksanaRKPD->status_program.'"><i class="fa fa-pencil-square-o fa-fw fa-lg"></i> Hapus</a>
                        </li>                        
                    </ul>
              </div>               
                ';
            }
            if($pelaksanaRKPD->sumber_data==0){
              return '<a class="edit-pelaksana btn btn-labeled btn-primary" data-id_pelaksana_ranwal="'.$pelaksanaRKPD->id_pelaksana_rpjmd.'" data-no_urut="'.$pelaksanaRKPD->urut.'" data-id_unit="'.$pelaksanaRKPD->id_unit.'" data-id_urusan_rkpd="'.$pelaksanaRKPD->id_urusan_rkpd.'" data-nm_unit="'.$pelaksanaRKPD->nm_unit.'" data-status_data="'.$pelaksanaRKPD->status_data.'" data-sumber_data="'.$pelaksanaRKPD->sumber_data.'" data-id_rkpd_ranwal="'.$pelaksanaRKPD->id_rkpd_ranwal.'" data-hak_akses="'.$pelaksanaRKPD->hak_akses.'" data-ket_pelaksanaan="'.$pelaksanaRKPD->ket_pelaksanaan.'" data-status_pelaksanaan="'.$pelaksanaRKPD->status_pelaksanaan.'" data-status_program="'.$pelaksanaRKPD->status_program.'"><span class="btn-label"><i class="fa fa-pencil-square-o fa-fw fa-lg"></i></span> Edit</a>';
            }
          }
        }
          
          })
        ->make(true);

    }

    public function loadData()
    {
        // if(Auth::check()){ 
            $unit=DB::select('SELECT id_unit,id_bidang,kd_unit,nm_unit FROM ref_unit');
            return view('ranwalrkpd.load')->with(compact('unit'));
        // } else {
            // return view ( 'errors.401' );
        // }        
    }

    public function getRefIndikator(){
      $refindikator=DB::SELECT('SELECT (@id:=@id+1) as no_urut, id_indikator, jenis_indikator,  
          sifat_indikator, nm_indikator, flag_iku, asal_indikator, sumber_data_indikator,id_satuan_output,
          case jenis_indikator 
          when 1 then "Positif"
          when 2 then "Negatif"
          end as jenis_display,
          case sifat_indikator
          when 0 then "Belum terindentifikasi" 
          when 1 then "Incremental"
          when 2 then "Absolut"
          when 3 then "Komulatif"
          end as sifat_display
          FROM ref_indikator,(SELECT @id:=0 ) var_id ');
      return DataTables::of($refindikator)
      ->make(true);
    }

    public function getRefUnit(){
      $refunit=DB::SELECT('SELECT (@id:=@id+1) as no_urut, id_unit, id_bidang, kd_unit, nm_unit FROM ref_unit,(SELECT @id:=0 ) var_id ');
      return DataTables::of($refunit)
      ->make(true);
    }

    public function getRefProgramRPJMD(){
      $refindikator=DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.thn_id,a.id_visi_rpjmd,  b.id_misi_rpjmd, c.id_tujuan_rpjmd, d.id_sasaran_rpjmd, 
            e.id_program_rpjmd, e.uraian_program_rpjmd, CONCAT(a.no_urut,".",b.no_urut,".",c.no_urut,".",d.no_urut,".",e.no_urut) as kd_program_rpjmd
            FROM trx_rpjmd_visi a
            INNER JOIN trx_rpjmd_misi b ON a.id_visi_rpjmd = b.id_visi_rpjmd
            INNER JOIN trx_rpjmd_tujuan c ON b.id_misi_rpjmd = c.id_misi_rpjmd
            INNER JOIN trx_rpjmd_sasaran d ON c.id_tujuan_rpjmd = d.id_tujuan_rpjmd
            INNER JOIN trx_rpjmd_program e ON d.id_sasaran_rpjmd = e.id_sasaran_rpjmd,(SELECT @id:=0 ) var_id ');
      return DataTables::of($refindikator)
      ->make(true);
    }

    public function prosesTransferData(Request $req)
    {
        $tahun = DB::SELECT('SELECT a.tahun, a.tahun_ke, a.kolom FROM (
            SELECT tahun_0 as tahun, 0 as tahun_ke, "tahun_0" as kolom FROM ref_tahun
            UNION SELECT tahun_1 as tahun, 1 as tahun_ke, "tahun_1" as kolom FROM ref_tahun
            UNION SELECT tahun_2 as tahun, 2 as tahun_ke, "tahun_2" as kolom FROM ref_tahun
            UNION SELECT tahun_3 as tahun, 3 as tahun_ke, "tahun_3" as kolom FROM ref_tahun
            UNION SELECT tahun_4 as tahun, 4 as tahun_ke, "tahun_4" as kolom FROM ref_tahun
            UNION SELECT tahun_5 as tahun, 5 as tahun_ke, "tahun_5" as kolom FROM ref_tahun) a
            WHERE a.tahun='.$req->tahun_rkpd.' LIMIT 1');

    //   $result=DB::Insert('INSERT INTO trx_rkpd_ranwal(no_urut, tahun_rkpd, jenis_belanja, id_rkpd_rpjmd, thn_id_rpjmd, id_visi_rpjmd,
    //             id_misi_rpjmd, id_tujuan_rpjmd, id_sasaran_rpjmd, id_program_rpjmd, uraian_program_rpjmd, pagu_rpjmd, 
    //             pagu_ranwal, status_pelaksanaan, status_data,sumber_data) 
    //             SELECT (@id:=@id+1) as no_urut, a.tahun_rkpd, 
    //             CASE c.no_urut 
    //               WHEN 98 THEN 1
    //               WHEN 99 THEN 2
    //               ELSE 0
    //             END AS jenis_belanja,a.id_rkpd_rpjmd,a.thn_id_rpjmd,a.id_visi_rpjmd,
    //             a.id_misi_rpjmd,a.id_tujuan_rpjmd,a.id_sasaran_rpjmd,a.id_program_rpjmd,
    //             b.uraian_program_rpjmd,a.pagu_program_rpjmd,a.pagu_program_rpjmd,0,0,0  
    //             FROM trx_rkpd_rpjmd_ranwal a 
    //             INNER JOIN trx_rpjmd_program b ON a.id_program_rpjmd = b.id_program_rpjmd
    //             INNER JOIN trx_rpjmd_misi c ON a.id_misi_rpjmd = c.id_misi_rpjmd,
    //             (SELECT @id:=0 ) z Where tahun_rkpd ='.$req->tahun_rkpd);

        $result=DB::Insert('INSERT INTO trx_rkpd_ranwal(no_urut, tahun_rkpd, jenis_belanja, id_rkpd_rpjmd, thn_id_rpjmd, id_visi_rpjmd,
                id_misi_rpjmd, id_tujuan_rpjmd, id_sasaran_rpjmd, id_program_rpjmd, uraian_program_rpjmd, pagu_rpjmd, 
                pagu_ranwal, status_pelaksanaan, status_data,sumber_data) 
                SELECT (@id:=@id+1) as no_urut, a.tahun_rkpd, 
                CASE c.no_urut 
                WHEN 98 THEN 1
                WHEN 99 THEN 2
                ELSE 0
                END AS jenis_belanja, id_program_rpjmd AS id_rkpd_rpjmd,a.thn_id_rpjmd,d.id_visi_rpjmd,
                d.id_misi_rpjmd,c.id_tujuan_rpjmd,a.id_sasaran_rpjmd,a.id_program_rpjmd,
                a.uraian_program_rpjmd,a.pagu_program_rpjmd,a.pagu_program_rpjmd,0,0,0  
                FROM (SELECT thn_id as thn_id_rpjmd, id_sasaran_rpjmd,id_program_rpjmd, uraian_program_rpjmd, pagu_tahun'.$tahun[0]->tahun_ke.' as pagu_program_rpjmd, 
                '.$tahun[0]->tahun.' as tahun_rkpd FROM trx_rpjmd_program ) a 								
                INNER JOIN trx_rpjmd_sasaran b ON a.id_sasaran_rpjmd = b.id_sasaran_rpjmd  
                INNER JOIN trx_rpjmd_tujuan c ON b.id_tujuan_rpjmd = c.id_tujuan_rpjmd     
                INNER JOIN trx_rpjmd_misi d ON c.id_misi_rpjmd = d.id_misi_rpjmd,(SELECT @id := 0 ) z');

      if ($result==0 ) {
        return redirect()->action('TrxRanwalRKPDController@loadData');
        }
      else {
        return redirect()->action('TrxRanwalRKPDController@transferIndikator',['tahun_rkpd'=>$req->tahun_rkpd]);
      }
    }

    public function transferIndikator($tahun_rkpd)
    {
        $tahun = DB::SELECT('SELECT a.tahun, a.tahun_ke, a.kolom FROM (
            SELECT tahun_0 as tahun, 0 as tahun_ke, "tahun_0" as kolom FROM ref_tahun
            UNION SELECT tahun_1 as tahun, 1 as tahun_ke, "tahun_1" as kolom FROM ref_tahun
            UNION SELECT tahun_2 as tahun, 2 as tahun_ke, "tahun_2" as kolom FROM ref_tahun
            UNION SELECT tahun_3 as tahun, 3 as tahun_ke, "tahun_3" as kolom FROM ref_tahun
            UNION SELECT tahun_4 as tahun, 4 as tahun_ke, "tahun_4" as kolom FROM ref_tahun
            UNION SELECT tahun_5 as tahun, 5 as tahun_ke, "tahun_5" as kolom FROM ref_tahun) a
            WHERE a.tahun='.$req->tahun_rkpd.' LIMIT 1');
        
        $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_indikator (tahun_rkpd,no_urut,id_rkpd_ranwal,id_perubahan,kd_indikator,uraian_indikator_program_rkpd,
                tolok_ukur_indikator,target_rpjmd,target_rkpd,status_data,sumber_data) 
                SELECT a.tahun_rkpd,(@id:=@id+1) as no_urut,c.id_rkpd_ranwal,a.id_perubahan,a.id_indikator,a.uraian_indikator_program_rpjmd, 
                a.tolok_ukur_indikator,a.angka_tahun,a.angka_tahun,0,0 
                FROM (SELECT DISTINCT '.$tahun[0]->tahun.' AS tahun_rkpd, a.id_program_rpjmd, a.id_perubahan, a.id_indikator,  
                    a.uraian_indikator_program_rpjmd,a.tolok_ukur_indikator,a.angka_tahun'.$tahun[0]->tahun_ke.' as angka_tahun  
                    FROM trx_rpjmd_program_indikator a) AS a 
                INNER JOIN trx_rkpd_ranwal AS c ON a.id_program_rpjmd = c.id_rkpd_rpjmd AND a.tahun_rkpd = c.tahun_rkpd,
                (SELECT @id:=0) var_id Where c.tahun_rkpd ='.$tahun[0]->tahun);

        // $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_indikator (tahun_rkpd,no_urut,id_rkpd_ranwal,id_perubahan,kd_indikator,uraian_indikator_program_rkpd,
        //     tolok_ukur_indikator,target_rpjmd,target_rkpd,status_data,sumber_data) 
        //     SELECT a.tahun_rkpd,(@id:=@id+1) as no_urut,c.id_rkpd_ranwal,a.id_perubahan,a.kd_indikator,a.uraian_indikator_program_rpjmd, 
        //     a.tolok_ukur_indikator,a.angka_tahun,a.angka_tahun,0,0 
        //     FROM trx_rkpd_rpjmd_program_indikator AS a 
        //     INNER JOIN trx_rkpd_rpjmd_ranwal AS b ON a.id_rkpd_rpjmd = b.id_rkpd_rpjmd 
        //     INNER JOIN trx_rkpd_ranwal AS c ON b.id_rkpd_rpjmd = c.id_rkpd_rpjmd AND b.tahun_rkpd = c.tahun_rkpd AND b.thn_id_rpjmd = c.thn_id_rpjmd 
        //     AND b.id_visi_rpjmd = c.id_visi_rpjmd  AND b.id_misi_rpjmd = c.id_misi_rpjmd AND b.id_tujuan_rpjmd = c.id_tujuan_rpjmd AND b.id_sasaran_rpjmd = c.id_sasaran_rpjmd 
        //     AND b.id_program_rpjmd = c.id_program_rpjmd, (SELECT @id:=0) var_id Where a.tahun_rkpd ='.$tahun_rkpd);

          if ($result==0 ) {
            return redirect()->action('TrxRanwalRKPDController@loadData');
            }
          else {
            return redirect()->action('TrxRanwalRKPDController@transferUrusan',['tahun_rkpd'=>$tahun_rkpd]);
          }
    }

    public function transferUrusan($tahun_rkpd)
    {
        $tahun = DB::SELECT('SELECT a.tahun, a.tahun_ke, a.kolom FROM (
            SELECT tahun_0 as tahun, 0 as tahun_ke, "tahun_0" as kolom FROM ref_tahun
            UNION SELECT tahun_1 as tahun, 1 as tahun_ke, "tahun_1" as kolom FROM ref_tahun
            UNION SELECT tahun_2 as tahun, 2 as tahun_ke, "tahun_2" as kolom FROM ref_tahun
            UNION SELECT tahun_3 as tahun, 3 as tahun_ke, "tahun_3" as kolom FROM ref_tahun
            UNION SELECT tahun_4 as tahun, 4 as tahun_ke, "tahun_4" as kolom FROM ref_tahun
            UNION SELECT tahun_5 as tahun, 5 as tahun_ke, "tahun_5" as kolom FROM ref_tahun) a
            WHERE a.tahun='.$req->tahun_rkpd.' LIMIT 1');
        
        $result=DB::INSERT('INSERT INTO trx_rkpd_ranwal_urusan (tahun_rkpd, no_urut, id_rkpd_ranwal, id_bidang,sumber_data) 
            SELECT x.tahun_rkpd,(@id:=@id+1) as no_urut,x.id_rkpd_ranwal, x.id_bidang, x.id_urbid_rpjmd 
            FROM (SELECT a.tahun_rkpd, a.id_rkpd_ranwal, a.id_program_rpjmd, c.id_unit, b.id_urbid_rpjmd, b.id_bidang  
			FROM trx_rkpd_ranwal a   
			INNER JOIN trx_rpjmd_program_urusan b ON a.id_program_rpjmd = b.id_program_rpjmd     
			INNER JOIN trx_rpjmd_program_pelaksana c ON b.id_urbid_rpjmd = c.id_urbid_rpjmd
			WHERE a.tahun_rkpd='.$req->tahun_rkpd.') x, (SELECT @id:=0) var_id');
            
    //   $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_urusan (tahun_rkpd, no_urut, id_rkpd_ranwal, id_urusan_rkpd, id_bidang,sumber_data) 
    //         SELECT x.tahun_rkpd,(@id:=@id+1) as no_urut,x.id_rkpd_ranwal,x.id_urbid_rpjmd,x.id_bidang,0 
    //         FROM (SELECT a.tahun_rkpd,c.id_rkpd_ranwal,a.id_urbid_rpjmd,a.id_bidang
    //         FROM trx_rkpd_rpjmd_program_pelaksana AS a 
    //         INNER JOIN trx_rkpd_rpjmd_ranwal AS b ON a.id_rkpd_rpjmd = b.id_rkpd_rpjmd
    //         INNER JOIN trx_rkpd_ranwal AS c ON b.id_rkpd_rpjmd = c.id_rkpd_rpjmd AND b.tahun_rkpd = c.tahun_rkpd AND 
    //         b.thn_id_rpjmd = c.thn_id_rpjmd AND b.id_visi_rpjmd = c.id_visi_rpjmd  AND b.id_misi_rpjmd = c.id_misi_rpjmd AND 
    //         b.id_tujuan_rpjmd = c.id_tujuan_rpjmd AND b.id_sasaran_rpjmd = c.id_sasaran_rpjmd AND 
    //         b.id_program_rpjmd = c.id_program_rpjmd Where a.tahun_rkpd ='.$tahun_rkpd.' 
    //         GROUP BY a.tahun_rkpd,c.id_rkpd_ranwal,a.id_urbid_rpjmd,a.id_bidang) x, (SELECT @id:=0) var_id');

          if ($result==0 ) {
            return redirect()->action('TrxRanwalRKPDController@loadData');
            }
          else {
            return redirect()->action('TrxRanwalRKPDController@transferPelaksana',['tahun_rkpd'=>$tahun_rkpd]);
          }
    }

    public function transferPelaksana($tahun_rkpd)
    {
        $tahun = DB::SELECT('SELECT a.tahun, a.tahun_ke, a.kolom FROM (
            SELECT tahun_0 as tahun, 0 as tahun_ke, "tahun_0" as kolom FROM ref_tahun
            UNION SELECT tahun_1 as tahun, 1 as tahun_ke, "tahun_1" as kolom FROM ref_tahun
            UNION SELECT tahun_2 as tahun, 2 as tahun_ke, "tahun_2" as kolom FROM ref_tahun
            UNION SELECT tahun_3 as tahun, 3 as tahun_ke, "tahun_3" as kolom FROM ref_tahun
            UNION SELECT tahun_4 as tahun, 4 as tahun_ke, "tahun_4" as kolom FROM ref_tahun
            UNION SELECT tahun_5 as tahun, 5 as tahun_ke, "tahun_5" as kolom FROM ref_tahun) a
            WHERE a.tahun='.$req->tahun_rkpd.' LIMIT 1');

        $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_pelaksana(tahun_rkpd,no_urut,id_rkpd_ranwal,id_urusan_rkpd,id_unit,
            pagu_rpjmd,pagu_rkpd,status_data,sumber_data,status_pelaksanaan) 
            SELECT a.tahun_rkpd, (@id:=@id+1) as no_urut, a.id_rkpd_ranwal, b.id_urusan_rkpd, c.id_unit, a.pagu_rpjmd, a.pagu_ranwal, 0,0,0   
			FROM trx_rkpd_ranwal a   
			INNER JOIN trx_rkpd_ranwal_urusan b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal						   
			INNER JOIN trx_rpjmd_program_pelaksana c ON b.sumber_data = c.id_urbid_rpjmd
			WHERE a.tahun_rkpd='.$req->tahun_rkpd.', (SELECT @id:=1) var_no');

        // $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_pelaksana(tahun_rkpd,no_urut,id_rkpd_ranwal,id_urusan_rkpd,id_unit,
        //     pagu_rpjmd,pagu_rkpd,status_data,sumber_data,status_pelaksanaan) 
        //     SELECT c.tahun_rkpd,(@id:=@id+1) as no_urut,c.id_rkpd_ranwal,b.id_urbid_rpjmd,b.id_unit,b.pagu_tahun,b.pagu_tahun,0,0,0 
        //     FROM trx_rkpd_rpjmd_ranwal AS a 
        //     INNER JOIN trx_rkpd_ranwal AS c ON a.id_rkpd_rpjmd = c.id_rkpd_rpjmd AND a.tahun_rkpd = c.tahun_rkpd AND a.thn_id_rpjmd = c.thn_id_rpjmd 
        //     AND a.id_visi_rpjmd = c.id_visi_rpjmd AND a.id_misi_rpjmd = c.id_misi_rpjmd AND a.id_tujuan_rpjmd = c.id_tujuan_rpjmd AND a.id_sasaran_rpjmd = c.id_sasaran_rpjmd 
        //     AND  a.id_program_rpjmd = c.id_program_rpjmd 
        //     INNER JOIN trx_rkpd_rpjmd_program_pelaksana AS b ON b.id_rkpd_rpjmd = a.id_rkpd_rpjmd AND a.tahun_rkpd = b.tahun_rkpd, 
        //     (SELECT @id:=1) var_no WHERE a.tahun_rkpd ='.$tahun_rkpd);

          if ($result==0 ) {
            return redirect()->action('TrxRanwalRKPDController@loadData');
            }
          else {
            $update=DB::UPDATE('UPDATE trx_rkpd_ranwal_urusan SET sumber_data=0 WHERE tahun_rkpd='.$req->tahun_rkpd);
            return redirect()->action('TrxRanwalRKPDController@index');
          }
    }

    

    public function ReprosesTransferData(Request $req)
    {
    //   $result=DB::Insert('INSERT INTO trx_rkpd_ranwal(no_urut, tahun_rkpd, jenis_belanja, id_rkpd_rpjmd, thn_id_rpjmd, id_visi_rpjmd,
    //             id_misi_rpjmd, id_tujuan_rpjmd, id_sasaran_rpjmd, id_program_rpjmd, uraian_program_rpjmd, pagu_rpjmd, 
    //             pagu_ranwal, status_pelaksanaan, status_data,sumber_data) 
    //             SELECT (@id:=@id+1) as no_urut, a.tahun_rkpd, 
    //             CASE c.no_urut 
    //               WHEN 98 THEN 1
    //               WHEN 99 THEN 2
    //               ELSE 0
    //             END AS jenis_belanja,a.id_rkpd_rpjmd,a.thn_id_rpjmd,a.id_visi_rpjmd,
    //             a.id_misi_rpjmd,a.id_tujuan_rpjmd,a.id_sasaran_rpjmd,a.id_program_rpjmd,
    //             b.uraian_program_rpjmd,a.pagu_program_rpjmd,a.pagu_program_rpjmd,0,0,0  
    //             FROM trx_rkpd_rpjmd_ranwal a 
    //             INNER JOIN trx_rpjmd_program b ON a.id_program_rpjmd = b.id_program_rpjmd
    //             INNER JOIN trx_rpjmd_misi c ON a.id_misi_rpjmd = c.id_misi_rpjmd,
    //             (SELECT @id:=0 ) z Where a.tahun_rkpd ='.$req->tahun_rkpd.' and a.id_rkpd_rpjmd='.$req->id_rkpd_rpjmd);

        $tahun = DB::SELECT('SELECT a.tahun, a.tahun_ke, a.kolom FROM (
            SELECT tahun_0 as tahun, 0 as tahun_ke, "tahun_0" as kolom FROM ref_tahun
            UNION SELECT tahun_1 as tahun, 1 as tahun_ke, "tahun_1" as kolom FROM ref_tahun
            UNION SELECT tahun_2 as tahun, 2 as tahun_ke, "tahun_2" as kolom FROM ref_tahun
            UNION SELECT tahun_3 as tahun, 3 as tahun_ke, "tahun_3" as kolom FROM ref_tahun
            UNION SELECT tahun_4 as tahun, 4 as tahun_ke, "tahun_4" as kolom FROM ref_tahun
            UNION SELECT tahun_5 as tahun, 5 as tahun_ke, "tahun_5" as kolom FROM ref_tahun) a
            WHERE a.tahun='.$req->tahun_rkpd.' LIMIT 1');

        $result=DB::Insert('INSERT INTO trx_rkpd_ranwal(no_urut, tahun_rkpd, jenis_belanja, id_rkpd_rpjmd, thn_id_rpjmd, id_visi_rpjmd,
            id_misi_rpjmd, id_tujuan_rpjmd, id_sasaran_rpjmd, id_program_rpjmd, uraian_program_rpjmd, pagu_rpjmd, 
            pagu_ranwal, status_pelaksanaan, status_data,sumber_data) 
            SELECT (@id:=@id+1) as no_urut, a.tahun_rkpd, 
            CASE c.no_urut 
            WHEN 98 THEN 1
            WHEN 99 THEN 2
            ELSE 0
            END AS jenis_belanja, a.id_program_rpjmd AS id_rkpd_rpjmd,a.thn_id_rpjmd,d.id_visi_rpjmd,
            d.id_misi_rpjmd,c.id_tujuan_rpjmd,a.id_sasaran_rpjmd,a.id_program_rpjmd,
            a.uraian_program_rpjmd,a.pagu_program_rpjmd,a.pagu_program_rpjmd,0,0,0  
            FROM (SELECT thn_id as thn_id_rpjmd, id_sasaran_rpjmd,id_program_rpjmd, uraian_program_rpjmd, pagu_tahun'.$tahun[0]->tahun_ke.' as pagu_program_rpjmd, 
            '.$tahun[0]->tahun.' as tahun_rkpd FROM trx_rpjmd_program ) a 								
            INNER JOIN trx_rpjmd_sasaran b ON a.id_sasaran_rpjmd = b.id_sasaran_rpjmd  
            INNER JOIN trx_rpjmd_tujuan c ON b.id_tujuan_rpjmd = c.id_tujuan_rpjmd     
            INNER JOIN trx_rpjmd_misi d ON c.id_misi_rpjmd = d.id_misi_rpjmd,(SELECT @id := 0 ) z
            WHERE a.id_program_rpjmd='.$req->id_rkpd_rpjmd);
    }

    public function RetransferIndikator(Request $req)
    {
    //   $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_indikator (tahun_rkpd,no_urut,id_rkpd_ranwal,id_perubahan,kd_indikator,uraian_indikator_program_rkpd,
    //     tolok_ukur_indikator,target_rpjmd,target_rkpd,status_data,sumber_data) 
    //     SELECT a.tahun_rkpd,(@id:=@id+1) as no_urut,c.id_rkpd_ranwal,a.id_perubahan,a.kd_indikator,a.uraian_indikator_program_rpjmd, 
    //     a.tolok_ukur_indikator,a.angka_tahun,a.angka_tahun,0,0 
    //     FROM trx_rkpd_rpjmd_program_indikator AS a 
    //     INNER JOIN trx_rkpd_rpjmd_ranwal AS b ON a.id_rkpd_rpjmd = b.id_rkpd_rpjmd 
    //     INNER JOIN trx_rkpd_ranwal AS c ON b.id_rkpd_rpjmd = c.id_rkpd_rpjmd AND b.tahun_rkpd = c.tahun_rkpd AND b.thn_id_rpjmd = c.thn_id_rpjmd 
    //     AND b.id_visi_rpjmd = c.id_visi_rpjmd  AND b.id_misi_rpjmd = c.id_misi_rpjmd AND b.id_tujuan_rpjmd = c.id_tujuan_rpjmd AND b.id_sasaran_rpjmd = c.id_sasaran_rpjmd 
    //     AND b.id_program_rpjmd = c.id_program_rpjmd, (SELECT @id:=0) var_id Where a.tahun_rkpd ='.$req->tahun_rkpd.' and b.id_rkpd_rpjmd='.$req->id_rkpd_rpjmd);

        $tahun = DB::SELECT('SELECT a.tahun, a.tahun_ke, a.kolom FROM (
            SELECT tahun_0 as tahun, 0 as tahun_ke, "tahun_0" as kolom FROM ref_tahun
            UNION SELECT tahun_1 as tahun, 1 as tahun_ke, "tahun_1" as kolom FROM ref_tahun
            UNION SELECT tahun_2 as tahun, 2 as tahun_ke, "tahun_2" as kolom FROM ref_tahun
            UNION SELECT tahun_3 as tahun, 3 as tahun_ke, "tahun_3" as kolom FROM ref_tahun
            UNION SELECT tahun_4 as tahun, 4 as tahun_ke, "tahun_4" as kolom FROM ref_tahun
            UNION SELECT tahun_5 as tahun, 5 as tahun_ke, "tahun_5" as kolom FROM ref_tahun) a
            WHERE a.tahun='.$req->tahun_rkpd.' LIMIT 1');

        $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_indikator (tahun_rkpd,no_urut,id_rkpd_ranwal,id_perubahan,kd_indikator,uraian_indikator_program_rkpd,
                tolok_ukur_indikator,target_rpjmd,target_rkpd,status_data,sumber_data) 
                SELECT a.tahun_rkpd,(@id:=@id+1) as no_urut,c.id_rkpd_ranwal,a.id_perubahan,a.id_indikator,a.uraian_indikator_program_rpjmd, 
                a.tolok_ukur_indikator,a.angka_tahun,a.angka_tahun,0,0 
                FROM (SELECT DISTINCT '.$tahun[0]->tahun.' AS tahun_rkpd, a.id_program_rpjmd, a.id_perubahan, a.id_indikator,  
                    a.uraian_indikator_program_rpjmd,a.tolok_ukur_indikator,a.angka_tahun'.$tahun[0]->tahun_ke.' as angka_tahun  
                    FROM trx_rpjmd_program_indikator a) AS a 
                INNER JOIN trx_rkpd_ranwal AS c ON a.id_program_rpjmd = c.id_rkpd_rpjmd AND a.tahun_rkpd = c.tahun_rkpd,
                (SELECT @id:=0) var_id Where c.tahun_rkpd ='.$tahun[0]->tahun.' and c.id_rkpd_rpjmd='.$req->id_rkpd_rpjmd);

    }

    public function RetransferUrusan(Request $req)
    {
    //   $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_urusan (tahun_rkpd, no_urut, id_rkpd_ranwal, id_urusan_rkpd, id_bidang,sumber_data) 
    //         SELECT x.tahun_rkpd,(@id:=@id+1) as no_urut,x.id_rkpd_ranwal,x.id_urbid_rpjmd,x.id_bidang,0 
    //         FROM (SELECT a.tahun_rkpd,c.id_rkpd_ranwal,a.id_urbid_rpjmd,a.id_bidang
    //         FROM trx_rkpd_rpjmd_program_pelaksana AS a 
    //         INNER JOIN trx_rkpd_rpjmd_ranwal AS b ON a.id_rkpd_rpjmd = b.id_rkpd_rpjmd
    //         INNER JOIN trx_rkpd_ranwal AS c ON b.id_rkpd_rpjmd = c.id_rkpd_rpjmd AND b.tahun_rkpd = c.tahun_rkpd AND 
    //         b.thn_id_rpjmd = c.thn_id_rpjmd AND b.id_visi_rpjmd = c.id_visi_rpjmd  AND b.id_misi_rpjmd = c.id_misi_rpjmd AND 
    //         b.id_tujuan_rpjmd = c.id_tujuan_rpjmd AND b.id_sasaran_rpjmd = c.id_sasaran_rpjmd AND 
    //         b.id_program_rpjmd = c.id_program_rpjmd Where a.tahun_rkpd ='.$req->tahun_rkpd.' and b.id_rkpd_rpjmd='.$req->id_rkpd_rpjmd.'  
    //         GROUP BY a.tahun_rkpd,c.id_rkpd_ranwal,a.id_urbid_rpjmd,a.id_bidang) x, (SELECT @id:=0) var_id');

        $result=DB::INSERT('INSERT INTO trx_rkpd_ranwal_urusan (tahun_rkpd, no_urut, id_rkpd_ranwal, id_bidang,sumber_data) 
            SELECT x.tahun_rkpd,(@id:=@id+1) as no_urut,x.id_rkpd_ranwal, x.id_bidang, x.id_urbid_rpjmd 
            FROM (SELECT a.tahun_rkpd, a.id_rkpd_ranwal, a.id_program_rpjmd, b.id_urbid_rpjmd, b.id_bidang  
            FROM trx_rkpd_ranwal a   
            INNER JOIN trx_rpjmd_program_urusan b ON a.id_program_rpjmd = b.id_program_rpjmd  
            WHERE a.tahun_rkpd='.$req->tahun_rkpd.' and a.id_rkpd_rpjmd='.$req->id_rkpd_rpjmd.' ) x, (SELECT @id:=0) var_id');
    }

    public function RetransferPelaksana(Request $req)
    {
    //   $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_pelaksana(tahun_rkpd,no_urut,id_rkpd_ranwal, id_urusan_rkpd, id_unit, pagu_rpjmd, pagu_rkpd, 
    //     status_data,sumber_data,status_pelaksanaan)  
    //     SELECT c.tahun_rkpd,(@id:=@id+1) as no_urut,c.id_rkpd_ranwal,b.id_urbid_rpjmd,b.id_unit,b.pagu_tahun,b.pagu_tahun,0,0,0  
    //     FROM trx_rkpd_rpjmd_ranwal AS a  
    //     INNER JOIN trx_rkpd_ranwal AS c ON a.id_rkpd_rpjmd = c.id_rkpd_rpjmd AND a.tahun_rkpd = c.tahun_rkpd AND a.thn_id_rpjmd = c.thn_id_rpjmd AND a.id_visi_rpjmd = c.id_visi_rpjmd
    //      AND a.id_misi_rpjmd = c.id_misi_rpjmd AND a.id_tujuan_rpjmd = c.id_tujuan_rpjmd AND a.id_sasaran_rpjmd = c.id_sasaran_rpjmd AND  a.id_program_rpjmd = c.id_program_rpjmd  
    //     INNER JOIN trx_rkpd_rpjmd_program_pelaksana AS b ON b.id_rkpd_rpjmd = a.id_rkpd_rpjmd AND a.tahun_rkpd = b.tahun_rkpd, 
    //     (SELECT @id:=1) var_no  
    //     WHERE a.tahun_rkpd ='.$req->tahun_rkpd.' and b.id_rkpd_rpjmd='.$req->id_rkpd_rpjmd);

        $result=DB::Insert('INSERT INTO trx_rkpd_ranwal_pelaksana(tahun_rkpd,no_urut,id_rkpd_ranwal,id_urusan_rkpd,id_unit,
            pagu_rpjmd,pagu_rkpd,status_data,sumber_data,status_pelaksanaan) 
            SELECT a.tahun_rkpd, (@id:=@id+1) as no_urut, a.id_rkpd_ranwal, b.id_urusan_rkpd, c.id_unit, a.pagu_rpjmd, a.pagu_ranwal, 0,0,0   
            FROM trx_rkpd_ranwal a   
            INNER JOIN trx_rkpd_ranwal_urusan b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal						   
            INNER JOIN trx_rpjmd_program_pelaksana c ON b.sumber_data = c.id_urbid_rpjmd, (SELECT @id:=1) var_no
            WHERE a.tahun_rkpd='.$req->tahun_rkpd.' and a.id_rkpd_rpjmd='.$req->id_rkpd_rpjmd);

        

    }

    public function RetransferUpdate(Request $req)
    {
        $update=DB::UPDATE('UPDATE trx_rkpd_ranwal_urusan a
        INNER JOIN trx_rkpd_ranwal b SET a.sumber_data=0 WHERE a.tahun_rkpd='.$req->tahun_rkpd.' AND b.id_rkpd_rpjmd='.$req->id_rkpd_rpjmd);
        // $update=DB::UPDATE('UPDATE trx_rkpd_ranwal_urusan SET sumber_data=0 WHERE tahun_rkpd='.$req->tahun_rkpd);
    }

    public function BatchTransfer(Request $req)
    {
        $tahun = DB::SELECT('SELECT a.tahun, a.tahun_ke, a.kolom FROM (
            SELECT tahun_0 as tahun, 0 as tahun_ke, "tahun_0" as kolom FROM ref_tahun
            UNION SELECT tahun_1 as tahun, 1 as tahun_ke, "tahun_1" as kolom FROM ref_tahun
            UNION SELECT tahun_2 as tahun, 2 as tahun_ke, "tahun_2" as kolom FROM ref_tahun
            UNION SELECT tahun_3 as tahun, 3 as tahun_ke, "tahun_3" as kolom FROM ref_tahun
            UNION SELECT tahun_4 as tahun, 4 as tahun_ke, "tahun_4" as kolom FROM ref_tahun
            UNION SELECT tahun_5 as tahun, 5 as tahun_ke, "tahun_5" as kolom FROM ref_tahun) a
            WHERE a.tahun='.$req->tahun_rkpd.' LIMIT 1');

        $transProg=DB::Insert('INSERT INTO trx_rkpd_ranwal(no_urut, tahun_rkpd, jenis_belanja, id_rkpd_rpjmd, thn_id_rpjmd, id_visi_rpjmd,
            id_misi_rpjmd, id_tujuan_rpjmd, id_sasaran_rpjmd, id_program_rpjmd, uraian_program_rpjmd, pagu_rpjmd, 
            pagu_ranwal, status_pelaksanaan, status_data,sumber_data) 
            SELECT (@id:=@id+1) as no_urut, a.tahun_rkpd, 
            CASE c.no_urut 
            WHEN 98 THEN 1
            WHEN 99 THEN 2
            ELSE 0
            END AS jenis_belanja, a.id_program_rpjmd AS id_rkpd_rpjmd,a.thn_id_rpjmd,d.id_visi_rpjmd,
            d.id_misi_rpjmd,c.id_tujuan_rpjmd,a.id_sasaran_rpjmd,a.id_program_rpjmd,
            a.uraian_program_rpjmd,a.pagu_program_rpjmd,a.pagu_program_rpjmd,0,0,0  
            FROM (SELECT thn_id as thn_id_rpjmd, id_sasaran_rpjmd,id_program_rpjmd, uraian_program_rpjmd, pagu_tahun'.$tahun[0]->tahun_ke.' as pagu_program_rpjmd, 
            '.$tahun[0]->tahun.' as tahun_rkpd FROM trx_rpjmd_program ) a 								
            INNER JOIN trx_rpjmd_sasaran b ON a.id_sasaran_rpjmd = b.id_sasaran_rpjmd  
            INNER JOIN trx_rpjmd_tujuan c ON b.id_tujuan_rpjmd = c.id_tujuan_rpjmd     
            INNER JOIN trx_rpjmd_misi d ON c.id_misi_rpjmd = d.id_misi_rpjmd,(SELECT @id := 0 ) z
            WHERE a.id_program_rpjmd='.$req->id_rkpd_rpjmd);

        

        
    }

    public function addProgramRkpd(Request $req){
    try{
           $data = new TrxRkpdRanwal();
           $data->no_urut = $req->no_urut ;
           $data->tahun_rkpd = Session::get('tahun') ;
           $data->id_rkpd_rpjmd = null ;
           $data->thn_id_rpjmd = $req->thn_id_rpjmd ;
           $data->jenis_belanja = $req->jenis_belanja ;
           $data->id_visi_rpjmd = $req->id_visi_rpjmd ;
           $data->id_misi_rpjmd = $req->id_misi_rpjmd ;
           $data->id_tujuan_rpjmd = $req->id_tujuan_rpjmd ;
           $data->id_sasaran_rpjmd = $req->id_sasaran_rpjmd ;
           $data->id_program_rpjmd = $req->id_program_rpjmd ;
           $data->uraian_program_rpjmd = $req->uraian_program_rpjmd ;
           $data->pagu_rpjmd = 0 ;
           $data->pagu_ranwal = $req->pagu_ranwal ;
           $data->keterangan_program = null ;
           $data->status_pelaksanaan = 5 ;
           $data->status_data = 0 ;
           $data->ket_usulan = $req->ket_usulan ;
           $data->sumber_data = 1 ;
           $data->save (['timestamps' => false]);
           return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         // $error_code = $e->getMessage() ;
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }
    }

    public function editProgramRKPD(Request $req)
      {
      try{    
          $data = TrxRkpdRanwal::find($req->id_rkpd_ranwal);
          $data->no_urut = $req->no_urut ;
           $data->uraian_program_rpjmd = $req->uraian_program_rpjmd ;
           $data->jenis_belanja = $req->jenis_belanja ;
           $data->thn_id_rpjmd = $req->thn_id_rpjmd ;
           $data->id_visi_rpjmd = $req->id_visi_rpjmd ;
           $data->id_misi_rpjmd = $req->id_misi_rpjmd ;
           $data->id_tujuan_rpjmd = $req->id_tujuan_rpjmd ;
           $data->id_sasaran_rpjmd = $req->id_sasaran_rpjmd ;
           $data->id_program_rpjmd = $req->id_program_rpjmd ;
           $data->pagu_ranwal = $req->pagu_ranwal ;
           $data->status_data = $req->status_data ;
           $data->status_pelaksanaan = $req->status_pelaksanaan ;
           $data->ket_usulan = $req->ket_usulan ;
           $data->save (['timestamps' => false]);          
           return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         // $error_code = $e->getMessage() ;
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }
      }

    public function postProgram(Request $req)
    {
        $cek=$this->getCekProgram($req->id_rkpd_ranwal);
        $CekProgram=$this->CheckProgram($req->id_rkpd_ranwal);
        $CekIndikator=$this->getCekIndikkator($req->id_rkpd_ranwal); 


        $data = TrxRkpdRanwal::find($req->id_rkpd_ranwal);
        $data->status_data = $req->status_data ;

        if($CekIndikator != null && $CekIndikator[0]->jml_target > 0 ) {
            if($req->status_data==1){
                if($CekProgram[0]->status_pelaksanaan==2 || $CekProgram[0]->status_pelaksanaan==3){
                  try{
                        $data->save (['timestamps' => false]);
                        return response ()->json (['pesan'=>'Data Program Ranwal RKPD Berhasil di-Posting','status_pesan'=>'1']);
                      }
                      catch(QueryException $e){
                         $error_code = $e->errorInfo[1] ;
                         return response ()->json (['pesan'=>'Data Program Ranwal RKPD Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
                      } 
                } else {
                  if($cek[0]->indikator_0 !=0 && $cek[0]->unit_0 !=0){
                    try{
                        $data->save (['timestamps' => false]);
                        return response ()->json (['pesan'=>'Data Program Ranwal RKPD Berhasil di-Posting','status_pesan'=>'1']);
                      }
                      catch(QueryException $e){
                         $error_code = $e->errorInfo[1] ;
                         return response ()->json (['pesan'=>'Data Program Ranwal RKPD Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
                      }             
                  } else {
                            return response ()->json (['pesan'=>'Maaf Program Ranwal RKPD belum dapat di-Posting. Silahkan cek Indikator/Pelaksana Program.. !!!','status_pesan'=>'0']);                     
                        }
                }       
            } else {
                try{
                    $data->save (['timestamps' => false]);
                    return response ()->json (['pesan'=>'Data Program Ranwal RKPD Berhasil di-UnPosting','status_pesan'=>'1']);
                  }
                  catch(QueryException $e){
                     $error_code = $e->errorInfo[1] ;
                     return response ()->json (['pesan'=>'Data Program Ranwal RKPD Gagal di-UnPosting ('.$error_code.')','status_pesan'=>'0']);
                  }
            }
        } else {
          return response ()->json (['pesan'=>'Maaf Program Ranwal RKPD belum dapat di-Posting. Silahkan cek Indikator, Indikator harus lebih besar dari 0.. !!!','status_pesan'=>'0']); 
        }

    }

    public function hapusProgramRKPD(Request $req)
      {
        TrxRkpdRanwal::where('id_rkpd_ranwal',$req->id_rkpd_ranwal)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil dihapus']);
      }

    public function addIndikatorRKPD(Request $req){

      
                $data= new TrxRkpdRanwalIndikator();
                $data->tahun_rkpd=Session::get('tahun') ; 
                $data->no_urut=$req->no_urut; 
                $data->id_rkpd_ranwal=$req->id_rkpd_ranwal; 
                $data->id_perubahan=0;
                $data->kd_indikator=$req->kd_indikator; 
                $data->uraian_indikator_program_rkpd=$req->uraian_indikator; 
                $data->tolok_ukur_indikator=$req->tolok_ukur_indikator; 
                $data->target_rpjmd=0; 
                $data->target_rkpd=$req->target_rkpd; 
                $data->status_data=0;
                $data->sumber_data=1; 
                $data->id_satuan_output=$req->id_satuan_output;
      try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         // $error_code = $e->getMessage() ;
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }
    }

    public function editIndikatorRKPD(Request $req)
    {

      $CekProgram=$this->CheckProgram($req->id_rkpd_ranwal);

        $data= TrxRkpdRanwalIndikator::find($req->id_indikator_program_rkpd);
        $data->no_urut=$req->no_urut; 
        $data->id_rkpd_ranwal=$req->id_rkpd_ranwal;
        $data->kd_indikator=$req->kd_indikator; 
        $data->uraian_indikator_program_rkpd=$req->uraian_indikator; 
        $data->tolok_ukur_indikator=$req->tolok_ukur_indikator;
        $data->target_rkpd=$req->target_rkpd;
        $data->status_data=$req->status_data; 
        $data->id_satuan_output=$req->id_satuan_output;

        // if($req->target_rkpd > 0){
          try{  
              $data->save (['timestamps' => false]);
              return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
               $error_code = $e->errorInfo[1] ;
               return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        //   } else {
        //     if($CekProgram[0]->status_pelaksanaan==2 || $CekProgram[0]->status_pelaksanaan==3){
        //       try{  
        //         $data->save (['timestamps' => false]);
        //         return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        //       }
        //       catch(QueryException $e){
        //          $error_code = $e->errorInfo[1] ;
        //          return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        //       }  
        //     } else {
        //       return response ()->json (['pesan'=>'Jumlah Target Indikator harus lebih besar dari 0','status_pesan'=>'0']);
        //     }

        // };
    }

    public function postIndikatorRKPD(Request $req){
    try{     
        $data= TrxRkpdRanwalIndikator::find($req->id_indikator_program_rkpd);        
        if ($req->status_data == 0) {
            $data->status_data=1;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Direviu','status_pesan'=>'1']);
        } else {
            $data->status_data=0;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Tidak Direviu','status_pesan'=>'1']);
        }           
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal diproses ('.$error_code.')','status_pesan'=>'0']);
    }
    }

    public function hapusIndikatorRKPD(Request $req)
      {
        TrxRkpdRanwalIndikator::where('id_indikator_program_rkpd',$req->id_indikator_program_rkpd)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
      }

    public function addUrusanRKPD(Request $req){
      try{
           $data = new TrxRkpdRanwalUrusan();
           $data->no_urut = $req->no_urut ;
           $data->tahun_rkpd = Session::get('tahun') ;
           $data->id_rkpd_ranwal = $req->id_rkpd_ranwal ;
           $data->id_bidang = $req->id_bidang ;
           $data->sumber_data=1;
           $data->save (['timestamps' => false]);
          return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         // $error_code = $e->getMessage() ;
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }

           
    }
    
    public function hapusUrusanRKPD(Request $req){

          TrxRkpdRanwalUrusan::where('id_urusan_rkpd',$req->id_urusan_rkpd)->delete ();

          return response ()->json (['pesan'=>'Data Berhasil Dihapus']);

    }

    public function addPelaksanaRKPD(Request $req){
    try{
           $data = new TrxRkpdRanwalPelaksana();
           $data->no_urut = $req->no_urut ;
           $data->tahun_rkpd = Session::get('tahun') ;
           $data->id_urusan_rkpd = $req->id_urusan_rkpd ;
           $data->id_rkpd_ranwal = $req->id_rkpd_ranwal ;
           $data->id_unit = $req->id_unit ;
           $data->pagu_rpjmd=0;
           $data->pagu_rkpd=0;
           $data->sumber_data=1;
           $data->hak_akses=$req->hak_akses;
           $data->status_data=$req->status_data;
           $data->status_pelaksanaan=5;
           $data->ket_pelaksanaan=$req->ket_pelaksanaan;           
           $data->save (['timestamps' => false]);
           return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         // $error_code = $e->getMessage() ;
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }
    }

    public function editPelaksanaRKPD(Request $req){
    try{
           $data = TrxRkpdRanwalPelaksana::find($req->id_pelaksana_rpjmd);
           $data->no_urut = $req->no_urut ;
           $data->id_urusan_rkpd = $req->id_urusan_rkpd ;
           $data->id_rkpd_ranwal = $req->id_rkpd_ranwal ;
           $data->id_unit = $req->id_unit ;
           $data->hak_akses=$req->hak_akses;
           $data->status_data=$req->status_data;
           $data->status_pelaksanaan=$req->status_pelaksanaan;
           $data->ket_pelaksanaan=$req->ket_pelaksanaan; 
           $data->save (['timestamps' => false]);
           return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         // $error_code = $e->getMessage() ;
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }
    }

    public function postPelaksanaRKPD(Request $req){
    try{     
        $data = TrxRkpdRanwalPelaksana::find($req->id_pelaksana_rpjmd);        
        if ($req->status_data == 0) {
            $data->status_data=1;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Direviu','status_pesan'=>'1']);
        } else {
            $data->status_data=0;
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data Berhasil Tidak Direviu','status_pesan'=>'1']);
        }           
    }
    catch(QueryException $e){
        $error_code = $e->errorInfo[1] ;
        return response ()->json (['pesan'=>'Data Gagal diproses ('.$error_code.')','status_pesan'=>'0']);
    }
    }

    public function hapusPelaksanaRKPD(Request $req){

          TrxRkpdRanwalPelaksana::where('id_pelaksana_rpjmd',$req->id_pelaksana_rpjmd)->delete ();
          return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
    }

    public function getPostingPelaksana(Request $req){
      $data=DB::SELECT('SELECT a.id_rkpd_ranwal, a.tahun_rkpd, b.id_urusan_rkpd, c.id_unit, c.id_pelaksana_rpjmd
            FROM trx_rkpd_ranwal a
            INNER JOIN trx_rkpd_ranwal_urusan b ON a.id_rkpd_ranwal = b.id_rkpd_ranwal and a.tahun_rkpd = b.tahun_rkpd
            INNER JOIN trx_rkpd_ranwal_pelaksana c ON b.id_rkpd_ranwal = c.id_rkpd_ranwal and b.tahun_rkpd = c.tahun_rkpd and b.id_urusan_rkpd = c.id_urusan_rkpd
            WHERE a.id_rkpd_ranwal ='.$req->id_rkpd_ranwal.' AND a.tahun_rkpd='.$req->tahun_renja);

      return response()->json_encode($data);
    }

    public function PostingPelaksanaRKPD(Request $req){
      $result=DB::UPDATE('UPDATE trx_rkpd_ranwal_pelaksana
              SET status_pelaksanaan='.$req->status_pelaksanaan.' WHERE id_pelaksana_rpjmd='.$req->id_pelaksana_rpjmd);
      
      return response()->json_encode();
    }


    public function belanjalangsung(Request $request)
    {
        // if(Auth::check()){  
            if(Session::has('tahun')){ 
              return view('ranwalrkpd.index');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function dapat(Request $request)
    {
        // if(Auth::check()){  
            if(Session::has('tahun')){ 
              return view('ranwalrkpd.index_dapat');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function tidaklangsung(Request $request)
    {
        // if(Auth::check()){  
            if(Session::has('tahun')){ 
              return view('ranwalrkpd.index_btl');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
        // if(Auth::check()){
            $dataRekap=DB::select('SELECT a.tahun_rkpd, COALESCE(a.pagu_0,0) as pagu_0, COALESCE(b.pagu_1,0) as pagu_1, 
                    COALESCE(c.pagu_2,0) as pagu_2 FROM
                    (SELECT tahun_rkpd, SUM(pagu_ranwal/1000000) as pagu_0 FROM trx_rkpd_ranwal
                    WHERE jenis_belanja = 0
                    GROUP BY tahun_rkpd) a
                    LEFT OUTER JOIN
                    (SELECT tahun_rkpd, SUM(pagu_ranwal/1000000) as pagu_1 FROM trx_rkpd_ranwal
                    WHERE jenis_belanja = 1
                    GROUP BY tahun_rkpd) b
                    ON a.tahun_rkpd = b.tahun_rkpd
                    LEFT OUTER JOIN
                    (SELECT tahun_rkpd, SUM(pagu_ranwal/1000000) as pagu_2 FROM trx_rkpd_ranwal
                    WHERE jenis_belanja = 2
                    GROUP BY tahun_rkpd) c
                    ON a.tahun_rkpd = c.tahun_rkpd 
                    WHERE a.tahun_rkpd = '.Session::get('tahun'));

            return view('ranwalrkpd.dashboard')->with(compact('dataRekap'));
        // } else {
            // return view ( 'errors.401' );
        // }
    }

    public function ssTahun()
    {
        $sesiTahun = Session::get('tahun');
        if(isset($sesiTahun) && $sesiTahun != NULL){
            $tahun = Session::get('tahun') ;
         }else{
             $tahun = date('Y');
        } 

        return $tahun;
        
    }
   
}
