<?php

namespace App\Http\Controllers\Ang;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\QueryException;
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
use App\Models\RefUnit;
use App\Models\Ang\TrxAnggaranDokumen;
use App\Models\Ang\TrxAnggaranProgram;
use App\Models\Ang\TrxAnggaranIndikator;
use App\Models\Ang\TrxAnggaranUrusan;
use App\Models\Ang\TrxAnggaranPelaksana;
use App\Models\Ang\TrxAnggaranProgramPd;
use App\Models\Ang\TrxAnggaranProgIndikatorPd;
use App\Models\Ang\TrxAnggaranKegiatanPd;
use App\Models\Ang\TrxAnggaranKegIndikatorPd;
use App\Models\Ang\TrxAnggaranPelaksanaPd;
use App\Models\Ang\TrxAnggaranAntivitasPd;
use App\Models\Ang\TrxAnggaranLokasiPd;
use App\Models\Ang\TrxAnggaranBelanjaPd;


class TrxApbdRkpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function CheckProgram($id_program)
    {
       $CheckProgram=DB::SELECT('SELECT id_anggaran_pemda, status_pelaksanaan, status_data FROM trx_anggaran_program WHERE id_anggaran_pemda = '.$id_program.' LIMIT 1');

        if($CheckProgram != null){
            if($CheckProgram[0]->status_pelaksanaan == 2 && $CheckProgram[0]->status_pelaksanaan == 3){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function CheckStatusProgram($id_program)
    {
       $CheckProgram=DB::SELECT('SELECT id_anggaran_pemda, status_pelaksanaan, status_data FROM trx_anggaran_program WHERE id_anggaran_pemda = '.$id_program.' LIMIT 1');

        if($CheckProgram != null && $CheckProgram[0]->status_data == 0){            
            return true;
        } else {
            return false;
        }
    }

    public function getCekProgram($id_ranwal){

        $CheckProgram=DB::SELECT('SELECT a.id_anggaran_pemda, a.pagu_rkpd, a.pagu_keuangan,  
            COALESCE((SELECT COUNT(*) AS jml_indi FROM trx_anggaran_indikator b
                WHERE b.tahun_rkpd=a.tahun_anggaran AND b.id_anggaran_pemda=a.id_anggaran_pemda
                GROUP BY b.tahun_rkpd, b.id_anggaran_pemda),0) as jml_indikator, 
            COALESCE((SELECT COUNT(*) AS jml_indi FROM trx_anggaran_indikator b
                WHERE b.tahun_rkpd=a.tahun_anggaran AND b.id_anggaran_pemda=a.id_anggaran_pemda AND b.status_data=1
                GROUP BY b.tahun_rkpd, b.id_anggaran_pemda),0) as indikator_0, 
            COALESCE((SELECT COUNT(*) AS jml_indi FROM trx_anggaran_pelaksana b
                WHERE b.tahun_anggaran=a.tahun_anggaran AND b.id_anggaran_pemda=a.id_anggaran_pemda
                GROUP BY b.tahun_anggaran, b.id_anggaran_pemda),0) as jml_unit, 
            COALESCE((SELECT COUNT(*) AS jml_indi FROM trx_anggaran_pelaksana b
                WHERE b.tahun_anggaran=a.tahun_anggaran AND b.id_anggaran_pemda=a.id_anggaran_pemda AND b.status_data=1
                GROUP BY b.tahun_anggaran, b.id_anggaran_pemda),0) as unit_0,
                        COALESCE((SELECT SUM(d.pagu_anggaran) FROM trx_anggaran_program_pd d
                INNER JOIN trx_anggaran_pelaksana c ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
                WHERE c.id_anggaran_pemda = a.id_anggaran_pemda AND (d.status_pelaksanaan <> 2 AND d.status_pelaksanaan <> 3)
                GROUP BY c.id_anggaran_pemda),0) AS pagu_prog_renja  
            FROM trx_anggaran_program AS a
            WHERE a.id_anggaran_pemda = '.$id_ranwal.' LIMIT 1');

        $selisihpagu = ($CheckProgram[0]->pagu_keuangan) - ($CheckProgram[0]->pagu_prog_renja);

        if($CheckProgram != null){
            if($CheckProgram[0]->indikator_0 > 0 && $CheckProgram[0]->unit_0 > 0 && $selisihpagu == 0 ){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getCekIndikkator($id_ranwal){
        $cek=DB::SELECT('SELECT SUM(CASE WHEN a.status_data = 0 then 1 else 0 end) as jml_target FROM trx_anggaran_indikator a 
            WHERE a.id_anggaran_pemda='.$id_ranwal.' GROUP BY a.id_anggaran_pemda');
    
        if($cek != null && $cek[0]->jml_target == 0){
            return true;
        } else {
            return false;
        }
    }

    public function getDokumenKeuangan(Request $req){
        $getDataDokumen = DB::SELECT('SELECT (@id:=@id+1) as no_urut, a.id_dokumen_keu, a.jns_dokumen_keu, a.kd_dokumen_keu,a.id_perubahan, a.id_dokumen_ref, 
                a.tahun_anggaran, a.nomor_keu, a.tanggal_keu, a.uraian_perkada, a.id_unit_ppkd, a.jabatan_tandatangan, 
                a.nama_tandatangan, a.nip_tandatangan, a.flag, a.created_at, a.updated_at,
                CONCAT("No : ",a.nomor_keu," (TA:",a.tahun_anggaran,")") AS nomor_display
                FROM trx_anggaran_dokumen AS a, (SELECT @id:=0) z
                WHERE a.jns_dokumen_keu = 1 AND a.kd_dokumen_keu = 0 AND a.tahun_anggaran ='.$req->tahun);
        return json_encode($getDataDokumen);
      }

    public function getData(Request $req)
    {   
        $dataranwal=DB::Select('SELECT (@id:=@id+1) as urut,a.* FROM (SELECT a.id_anggaran_pemda, a.id_rkpd_ranwal, a.id_rkpd_final, a.no_urut, a.tahun_anggaran, a.jenis_belanja, a.id_rkpd_rpjmd, 
        a.thn_id_rpjmd, a.id_visi_rpjmd, a.id_misi_rpjmd, a.id_tujuan_rpjmd, a.id_sasaran_rpjmd, a.id_program_rpjmd, a.uraian_program_rpjmd, a.pagu_rkpd, a.pagu_keuangan, a.keterangan_program, 
        a.status_pelaksanaan, a.status_data, a.ket_usulan, a.sumber_data,
        COALESCE((SELECT COUNT(*) AS jml_indi FROM trx_anggaran_indikator x
            WHERE x.tahun_rkpd =a.tahun_anggaran AND x.id_anggaran_pemda=a.id_anggaran_pemda
            GROUP BY x.tahun_rkpd, x.id_anggaran_pemda),0) as jml_indikator, 
        COALESCE((SELECT COUNT(*) AS jml_indi FROM trx_anggaran_indikator y
            WHERE y.tahun_rkpd=a.tahun_anggaran AND y.id_anggaran_pemda=a.id_anggaran_pemda
            GROUP BY y.tahun_rkpd, y.id_anggaran_pemda),0) as indikator_0, 
        COALESCE((SELECT COUNT(*) AS jml_indi FROM trx_anggaran_pelaksana u
            WHERE u.tahun_anggaran=a.tahun_anggaran AND u.id_anggaran_pemda=a.id_anggaran_pemda
            GROUP BY u.tahun_anggaran, u.id_anggaran_pemda),0) as jml_unit, 
        COALESCE((SELECT COUNT(*) AS jml_indi FROM trx_anggaran_pelaksana v
            WHERE v.tahun_anggaran=a.tahun_anggaran AND v.id_anggaran_pemda=a.id_anggaran_pemda
            GROUP BY v.tahun_anggaran, v.id_anggaran_pemda),0) as unit_0, 
        CASE a.status_data
            WHEN 0 THEN "Draft"
            WHEN 1 THEN "Posting"
        END AS ur_usulan,    
        CASE a.sumber_data 
            WHEN 0 THEN "RPJMD" 
            WHEN 1 THEN "Baru" 
            WHEN 2 THEN "Tahun Sebelumnya" 
        END AS sumber_display, 
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
        END AS pelaksanaan_display,
        COALESCE((SELECT SUM(d.pagu_anggaran) FROM trx_anggaran_program_pd d
            INNER JOIN trx_anggaran_pelaksana c ON c.id_pelaksana_anggaran = d.id_pelaksana_anggaran
            INNER JOIN trx_anggaran_urusan b ON c.id_urusan_anggaran = b.id_urusan_anggaran
            WHERE b.id_anggaran_pemda = a.id_anggaran_pemda AND (d.status_pelaksanaan <> 2 AND d.status_pelaksanaan <> 3)
            GROUP BY b.id_anggaran_pemda),0) AS pagu_prog_renja        
        FROM trx_anggaran_program AS a
        WHERE a.tahun_anggaran='.Session::get('tahun').' AND a.id_dokumen_keu='.$req->id_dokumen_keu.') a,(SELECT @id:=0) x');

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

    public function getUrusan(){
      $urusan=DB::select('SELECT kd_urusan, nm_urusan FROM ref_urusan');
        return json_encode($urusan);
    }

    public function getBidang($kd_urusan){
      $urusan=DB::select('SELECT id_bidang, kd_urusan, kd_bidang, nm_bidang, kd_fungsi
          FROM ref_bidang WHERE kd_urusan='.$kd_urusan.'');
          return json_encode($urusan);
    }

    public function getIndikatorRKPD($id_rkpd)
    {
      $indikatorRKPD=DB::select('SELECT a.tahun_rkpd,(@id:=@id+1) as urut, a.no_urut, a.id_anggaran_pemda,a.id_indikator_program_rkpd,a.id_perubahan,a.kd_indikator,a.uraian_indikator_program_rkpd,a.id_indikator_rkpd_final, a.tolok_ukur_indikator,a.target_keuangan,a.target_rkpd,a.status_data,a.sumber_data,a.indikator_output,a.id_satuan_output, b.status_data as status_data_program,b.status_pelaksanaan as status_program,
        CASE a.status_data
            WHEN 0 THEN "fa fa-question"
            WHEN 1 THEN "fa fa-check-square-o"
        END AS status_reviu,
        CASE a.status_data
            WHEN 0 THEN "red"
            WHEN 1 THEN "green"
        END AS warna  
        FROM trx_anggaran_indikator a
        INNER JOIN trx_anggaran_program b ON a.id_anggaran_pemda = b.id_anggaran_pemda AND a.tahun_rkpd = b.tahun_anggaran,(SELECT @id:=0) x where a.id_anggaran_pemda='.$id_rkpd);

      return DataTables::of($indikatorRKPD)
        ->addColumn('action', function ($indikatorRKPD) {
          if($indikatorRKPD->status_data_program==0 ){
          if($indikatorRKPD->status_program!=2 && $indikatorRKPD->status_program!=3){
            return '<a class="edit-indikator btn btn-labeled btn-primary" data-toggle="tooltip" title="Edit Indikator Program RKPD"><span class="btn-label"><i class="fa fa-pencil-square-o fa-fw fa-lg"></i></span> Edit Indikator</a>';
            }
            }
          })
        ->make(true);

    }

    public function getUrusanRKPD($id_rkpd)
    {
      $urusanRKPD=DB::select('SELECT (@id:=@id+1) as urut, x.* FROM (SELECT a.tahun_anggaran, a.no_urut, a.id_anggaran_pemda,a.id_urusan_anggaran, a.id_bidang, b.kd_bidang, b.nm_bidang, 
                c.kd_urusan, c.nm_urusan, a.sumber_data,d.status_data as status_data_program,d.status_pelaksanaan as status_program, IFNULL(e.jml_data,0) as jml_data, IFNULL(e.jml_0,0) as jml_0 
                FROM trx_anggaran_urusan a
                INNER JOIN ref_bidang b on a.id_bidang = b.id_bidang
                INNER JOIN ref_urusan c on b.kd_urusan = c.kd_urusan
                INNER JOIN trx_anggaran_program d on a.id_anggaran_pemda = d.id_anggaran_pemda
                LEFT OUTER JOIN (SELECT a.tahun_anggaran, a.id_anggaran_pemda, a.id_urusan_anggaran,COUNT(*) as jml_data, b.jml_0
                FROM trx_anggaran_pelaksana a
                LEFT OUTER JOIN (SELECT tahun_anggaran, id_anggaran_pemda, id_urusan_anggaran, COUNT(*) as jml_0
                FROM trx_anggaran_pelaksana
                WHERE status_data= 1
                GROUP BY tahun_anggaran, id_anggaran_pemda,id_urusan_anggaran) b ON a.tahun_anggaran=b.tahun_anggaran AND a.id_urusan_anggaran = b.id_urusan_anggaran
                GROUP BY a.tahun_anggaran,a.id_anggaran_pemda, a.id_urusan_anggaran, b.jml_0) e ON a.tahun_anggaran=e.tahun_anggaran AND a.id_urusan_anggaran = e.id_urusan_anggaran) x, 
                (SELECT @id:=0) var_no where x.id_anggaran_pemda='.$id_rkpd.' order By kd_urusan, kd_bidang');

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
                            <a class="view-unit dropdown-item"><i class="fa fa-eye fa-fw fa-lg"></i> Lihat Unit</a>
                        </li>
                        <li>
                            <a class="hapus-urusan dropdown-item" href="#" data-toggle="tooltip" title="Hapus Urusan Program RKPD"><i class="fa fa-trash fa-fw fa-lg"></i> Hapus</a>
                        </li>                        
                    </ul>
              </div>            
              ';
              }
            if ($urusanRKPD->sumber_data==0) {
              return '<a class="view-unit btn btn-labeled btn-success"><span class="btn-label"><i class="fa fa-eye fa-fw fa-lg"></i></span> Lihat Unit</a>';
              }
            }
            if ($urusanRKPD->status_data_program==1) {
              return '<a class="view-unit btn btn-labeled btn-success"><span class="btn-label"><i class="fa fa-eye fa-fw fa-lg"></i></span> Lihat Unit</a>';
              }

          }
          
          })
        ->make(true);

    }

    public function getPelaksanaRKPD($id_rkpd,$id_urusan)
    {
      $pelaksanaRKPD=DB::select('SELECT (@id:=@id+1) as urut, a.no_urut, a.id_pelaksana_anggaran, a.id_unit,a.tahun_anggaran,a.id_anggaran_pemda,
            a.id_urusan_anggaran,a.id_pelaksana_rkpd_final,a.status_data,a.pagu_rkpd_final,a.pagu_anggaran,
            IF(a.sumber_data=0,"RPJMD","Baru") as keterangan,b.kd_unit,b.nm_unit,a.sumber_data,a.status_pelaksanaan, a.hak_akses, a.ket_pelaksanaan,
            c.status_data as status_data_program,c.status_pelaksanaan as status_program,
                CASE a.status_data
                WHEN 0 THEN "fa fa-question"
                WHEN 1 THEN "fa fa-check-square-o"
                END AS status_reviu,
            CASE a.status_data
                WHEN 0 THEN "red"
                WHEN 1 THEN "green"
            END AS warna,
            COALESCE((SELECT SUM(d.pagu_anggaran) FROM trx_anggaran_program_pd d
            WHERE d.id_pelaksana_anggaran = a.id_pelaksana_anggaran AND d.id_unit = a.id_unit AND (d.status_pelaksanaan <> 2 AND d.status_pelaksanaan <> 3)
            GROUP BY d.id_pelaksana_anggaran,d.id_unit),0) AS pagu_prog_renja   
            FROM trx_anggaran_pelaksana AS a
            INNER JOIN trx_anggaran_urusan AS d ON a.tahun_anggaran=d.tahun_anggaran AND a.id_urusan_anggaran = d.id_urusan_anggaran
            INNER JOIN trx_anggaran_program c ON d.id_anggaran_pemda = c.id_anggaran_pemda AND d.tahun_anggaran = c.tahun_anggaran 
            INNER JOIN ref_unit AS b ON a.id_unit = b.id_unit,(SELECT @id:=0) var_no where a.id_anggaran_pemda='.$id_rkpd.' AND a.id_urusan_anggaran='.$id_urusan);

      return DataTables::of($pelaksanaRKPD)
        ->addColumn('action', function ($pelaksanaRKPD) {
          if ($pelaksanaRKPD->status_data_program==0) {
            if($pelaksanaRKPD->status_program!=2 && $pelaksanaRKPD->status_program!=3){
              if($pelaksanaRKPD->sumber_data==1){
                if($pelaksanaRKPD->status_data==0){
                    return '
                    <div class="btn-group">
                          <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                          <ul class="dropdown-menu dropdown-menu-right">
                              <li>
                                  <a class="edit-pelaksana dropdown-item"><i class="fa fa-pencil-square-o fa-fw fa-lg text-success"></i> Edit Pelaksana</a>
                              </li>
                              <li>
                                  <a class="hapus-pelaksana dropdown-item"><i class="fa fa-trash fa-fw fa-lg text-danger"></i> Hapus Pelaksana</a>
                              </li>                        
                          </ul>
                    </div>               
                      ';
                    } else {
                      return '<a class="edit-pelaksana btn btn-labeled btn-primary"><span class="btn-label"><i class="fa fa-pencil-square-o fa-fw fa-lg "></i></span> Edit</a>';
                    }
            }
            if($pelaksanaRKPD->sumber_data==0){
              return '<a class="edit-pelaksana btn btn-labeled btn-primary"><span class="btn-label"><i class="fa fa-pencil-square-o fa-fw fa-lg"></i></span> Edit</a>';
            }
          }
        }
          
          })
        ->make(true);
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

    public function getRefSubUnit(Request $req){
        $refunit=DB::SELECT('SELECT a.id_sub_unit, a.nm_sub, CONCAT(RIGHT(CONCAT("0",c.kd_urusan),2),".",RIGHT(CONCAT("0",c.kd_bidang),2),".",RIGHT(CONCAT("0",b.kd_unit),2),".",RIGHT(CONCAT("000",a.kd_sub),4)," -- ",a.nm_sub) AS nama_display FROM ref_sub_unit AS a
        INNER JOIN ref_unit AS b ON a.id_unit = b.id_unit
        INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang
        WHERE a.id_unit ='.$req->unit.'
        ORDER BY c.kd_urusan,c.kd_bidang,b.kd_unit,a.kd_sub');
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

    public function addProgramRkpd(Request $req){

    // $cek = DB::SELECT('SELECT tahun_rkpd, flag FROM trx_rkpd_final_dokumen WHERE tahun_rkpd = '.session::get('tahun').' LIMIT 1');
           $data = new TrxAnggaranProgram();
           $data->id_dokumen_keu = $req->id_dokumen_keu;
           $data->no_urut = $req->no_urut ;
           $data->tahun_anggaran = session::get('tahun') ;
           $data->id_rkpd_ranwal = 0 ;
           $data->id_rkpd_final = 0 ;
           $data->id_rkpd_rpjmd = null ;
           $data->thn_id_rpjmd = $req->thn_id_rpjmd ;
           $data->jenis_belanja = $req->jenis_belanja ;
           $data->id_visi_rpjmd = $req->id_visi_rpjmd ;
           $data->id_misi_rpjmd = $req->id_misi_rpjmd ;
           $data->id_tujuan_rpjmd = $req->id_tujuan_rpjmd ;
           $data->id_sasaran_rpjmd = $req->id_sasaran_rpjmd ;
           $data->id_program_rpjmd = $req->id_program_rpjmd ;
           $data->uraian_program_rpjmd = $req->uraian_program_rpjmd ;
           $data->pagu_rkpd = 0 ;
           $data->pagu_keuangan = $req->pagu_keuangan ;
           $data->keterangan_program = null ;
           $data->status_pelaksanaan = 4 ;
           $data->status_data = 0 ;
           $data->ket_usulan = $req->ket_usulan ;
           $data->sumber_data = 1 ;

    // if($cek != null && $cek[0]->flag == 0){ 
    try{
           $data->save (['timestamps' => false]);
           return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
      }
      catch(QueryException $e){
         $error_code = $e->errorInfo[1] ;
         return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
      }
    // } else {
       // return response ()->json (['pesan'=>'Data Gagal Disimpan, Dokumen Rancangan RKPD telah di-Posting','status_pesan'=>'0']); 
    // }
    }

    public function editProgramRKPD(Request $req)
    {
        // $CekProgram=$this->CheckStatusProgram($req->id_anggaran_pemda);
        // if($CekProgram == true){
            try{    
                $data = TrxAnggaranProgram::find($req->id_anggaran_pemda);
                $data->id_dokumen_keu = $req->id_dokumen_keu;
                $data->no_urut = $req->no_urut ;
                $data->thn_id_rpjmd = $req->thn_id_rpjmd ;
                $data->jenis_belanja = $req->jenis_belanja ;
                $data->id_visi_rpjmd = $req->id_visi_rpjmd ;
                $data->id_misi_rpjmd = $req->id_misi_rpjmd ;
                $data->id_tujuan_rpjmd = $req->id_tujuan_rpjmd ;
                $data->id_sasaran_rpjmd = $req->id_sasaran_rpjmd ;
                $data->id_program_rpjmd = $req->id_program_rpjmd ;
                $data->uraian_program_rpjmd = $req->uraian_program_rpjmd ;
                $data->pagu_keuangan = $req->pagu_keuangan ;
                $data->keterangan_program = null ;
                $data->status_pelaksanaan = $req->status_pelaksanaan ;
                $data->status_data = $req->status_data ;
                $data->ket_usulan = $req->ket_usulan ;
                $data->save (['timestamps' => false]);          
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        // } else {
        //     return response ()->json (['pesan'=>'Data Gagal Disimpan (Cek Status Program)','status_pesan'=>'0']);
        // } 
      }

    public function postProgram(Request $req)
    {
        $cek=$this->getCekProgram($req->id_rkpd_ranwal);
        $CekProgram=$this->CheckProgram($req->id_rkpd_ranwal);
        $CekIndikator=$this->getCekIndikkator($req->id_rkpd_ranwal); 

        $data = TrxAnggaranProgram::find($req->id_rkpd_ranwal);
        $data->status_data = $req->status_data ;

        if ($CekIndikator == true) {
            if($req->status_data==1){
                if($CekProgram == true){
                    try{
                        $data->save (['timestamps' => false]);
                        return response ()->json (['pesan'=>'Data Program Rancangan RKPD Berhasil di-Posting','status_pesan'=>'1']);
                    }
                    catch(QueryException $e){
                        $error_code = $e->errorInfo[1] ;
                        return response ()->json (['pesan'=>'Data Program Rancangan RKPD Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
                    } 
                } else {
                    if($cek == true){
                        try{
                            $data->save (['timestamps' => false]);
                            return response ()->json (['pesan'=>'Data Program Rancangan RKPD Berhasil di-Posting','status_pesan'=>'1']);
                        }
                        catch(QueryException $e){
                            $error_code = $e->errorInfo[1] ;
                            return response ()->json (['pesan'=>'Data Program Rancangan RKPD Gagal di-Posting ('.$error_code.')','status_pesan'=>'0']);
                        }             
                    } else {
                            return response ()->json (['pesan'=>'Maaf Program Rancangan RKPD belum dapat di-Posting. Silahkan cek Pagu/Indikator/Pelaksana Program .. !!!','status_pesan'=>'0']);                     
                        }
                }
            } else {
                try{
                    $data->save (['timestamps' => false]);
                    return response ()->json (['pesan'=>'Data Program Rancangan RKPD Berhasil di-UnPosting','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Program Rancangan RKPD Gagal di-UnPosting ('.$error_code.')','status_pesan'=>'0']);
                }
            }
        } else {
            return response ()->json (['pesan'=>'Maaf Program Rancangan RKPD belum dapat di-Posting. Silahkan cek Indikator, Indikator harus lebih besar dari 0.. !!!','status_pesan'=>'0']);
        }
    }

    public function hapusProgramRKPD(Request $req)
      {
        // $CekProgram=$this->CheckStatusProgram($req->id_rkpd_rancangan);
        // if($CekProgram == true){
            TrxAnggaranProgram::where('id_anggaran_pemda',$req->id_anggaran_pemda)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil dihapus','status_pesan'=>'1']);
        // } else {
        //     return response ()->json (['pesan'=>'Data Gagal Hapus (Cek Status Program)','status_pesan'=>'0']);
        // }
      }

    public function addIndikatorRKPD(Request $req){
    
        // $CekProgram=$this->CheckStatusProgram($req->id_rkpd_rancangan);

        // if($CekProgram == true){
            $data= new TrxAnggaranIndikator();
            $data->tahun_rkpd=session::get('tahun') ; 
            $data->no_urut=$req->no_urut; 
            $data->id_rkpd_rancangan=$req->id_rkpd_rancangan;
            $data->id_indikator_program_rkpd=0; 
            $data->id_perubahan=0;
            $data->kd_indikator=$req->kd_indikator; 
            $data->uraian_indikator_program_rkpd=$req->uraian_indikator; 
            $data->tolok_ukur_indikator=$req->tolok_ukur_indikator; 
            $data->target_rpjmd=0; 
            $data->target_rkpd=$req->target_rkpd; 
            $data->status_data=0;
            $data->sumber_data=1; 
            $data->id_satuan_ouput=$req->id_satuan_output;

            try{
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
              }
              catch(QueryException $e){
                 $error_code = $e->getMessage() ;
                 $error_code = $e->errorInfo[1] ;
                 return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
              }
        // } else {
            // return response ()->json (['pesan'=>'Data Gagal Disimpan (Cek Status Program)','status_pesan'=>'0']);
        // }              
    }

    public function editIndikatorRKPD(Request $req)
    {
        // $CekProgram=$this->CheckStatusProgram($req->id_rkpd_rancangan);

            $data= TrxAnggaranIndikator::find($req->id_indikator_rkpd);
            $data->no_urut=$req->no_urut; 
            $data->kd_indikator=$req->kd_indikator; 
            $data->uraian_indikator_program_rkpd=$req->uraian_indikator; 
            $data->tolok_ukur_indikator=$req->tolok_ukur_indikator;
            $data->target_rkpd=$req->target_rkpd;
            $data->status_data=$req->status_data; 
            $data->id_satuan_ouput=$req->id_satuan_output;

        // if($CekProgram == true){
            try{  
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                }
        // } else {
            // return response ()->json (['pesan'=>'Data Gagal Disimpan (Cek Status Program)','status_pesan'=>'0']);
        // }
    }

    public function postIndikatorRKPD(Request $req){
        try{     
            $data= TrxAnggaranIndikator::find($req->id_indikator_program_rkpd);        
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
        TrxAnggaranIndikator::where('id_indikator_program_rkpd',$req->id_indikator_program_rkpd)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus'] );
    }

    public function addUrusanRKPD(Request $req){
        // $CekProgram=$this->CheckStatusProgram($req->id_rkpd_ranwal);
        // if($CekProgram == true){
            try{
                $data = new TrxAnggaranUrusan();
                $data->no_urut = $req->no_urut ;
                $data->tahun_anggaran = session::get('tahun') ;
                $data->id_anggaran_pemda = $req->id_anggaran_pemda ;
                $data->id_bidang = $req->id_bidang ;
                $data->sumber_data=1;
                $data->save (['timestamps' => false]);
                return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            }
            catch(QueryException $e){
                $error_code = $e->getMessage() ;
                $error_code = $e->errorInfo[1] ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        // } else {
            // return response ()->json (['pesan'=>'Data Gagal Disimpan (Cek Status Program)','status_pesan'=>'0']);
        // }           
    }
    
    public function hapusUrusanRKPD(Request $req){

      $cek = DB::SELECT('SELECT COALESCE(COUNT(id_pelaksana_anggaran),0) as jml_pelaksana FROM trx_anggaran_pelaksana 
      WHERE id_urusan_anggaran ='.$req->id_urusan_anggaran.' GROUP BY id_urusan_anggaran');

    //   if($cek == NULL || $cek[0]->jml_pelaksana == 0){
        TrxAnggaranUrusan::where('id_urusan_anggaran',$req->id_urusan_anggaran)->delete ();
        return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
    //   } else {
        // return response ()->json (['pesan'=>'Data Gagal Dihapus, Data Pelaksana Urusan masih ada','status_pesan'=>'0']);
    //   }          
    }

    public function addPelaksanaRKPD(Request $req){
    try{
           $data = new TrxAnggaranPelaksana();
           $data->no_urut = $req->no_urut ;
           $data->tahun_anggaran = session::get('tahun') ;
           $data->id_urusan_anggaran = $req->id_urusan_anggaran ;
           $data->id_anggaran_pemda = $req->id_anggaran_pemda ;
           $data->id_pelaksana_rkpd_final=0;
           $data->id_unit = $req->id_unit ;
           $data->pagu_rkpd_final=0;
           $data->pagu_anggaran=0;
           $data->sumber_data=1;
           $data->hak_akses=$req->hak_akses;
           $data->status_data=$req->status_data;
           $data->status_pelaksanaan=4;
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
        // $cekpagu = DB::SELECT('SELECT SUM(d.pagu_forum) AS jml_pagu FROM trx_rkpd_rancangan_program_pd d
        //     WHERE d.id_unit = '.$req->id_unit.' AND (d.status_pelaksanaan <> 2 AND d.status_pelaksanaan <> 3) AND d.id_rkpd_rancangan = '.$req->id_pelaksana_rkpd.' 
        //     GROUP BY d.id_rkpd_rancangan,d.id_unit');
        
        $data = TrxAnggaranPelaksana::find($req->id_pelaksana_anggaran);
        $data->no_urut = $req->no_urut ;
        $data->id_unit = $req->id_unit ;
        $data->hak_akses=$req->hak_akses;
        $data->status_data=$req->status_data;
        $data->status_pelaksanaan=$req->status_pelaksanaan;
        $data->ket_pelaksanaan=$req->ket_pelaksanaan; 
        // if($req->status_data == 1) {
            // if($cekpagu != NULL && $cekpagu[0]->jml_pagu > 0){
                try{
                    $data->save (['timestamps' => false]);
                    return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
                }
                catch(QueryException $e){
                    $error_code = $e->errorInfo[1] ;
                    return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
                }
            // } else {
            //     return response ()->json (['pesan'=>'Data Gagal Disimpan, belum ada data program OPD','status_pesan'=>'0']);
            // }

        // } else {
            // try{
                // $data->save (['timestamps' => false]);
                // return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
            // }
            // catch(QueryException $e){
                // $error_code = $e->errorInfo[1] ;
                // return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            // }
        // } 
    }

    public function postPelaksanaRKPD(Request $req){ 
        $cekpagu = DB::SELECT('SELECT SUM(d.pagu_anggaran) AS jml_pagu FROM trx_anggaran_program_pd d
            WHERE d.id_unit = '.$req->id_unit.' AND (d.status_pelaksanaan <> 2 AND d.status_pelaksanaan <> 3) AND d.id_pelaksana_anggaran = '.$req->id_pelaksana_rkpd.' 
            GROUP BY d.id_pelaksana_anggaran,d.id_unit');

        $cekpagu = DB::SELECT('SELECT x.id_pelaksana_anggaran, 
                (COALESCE(a.pagu_anggaran,0) - COALESCE(b.pagu_kegiatan,0)) +
                (COALESCE(b.pagu_kegiatan,0) - COALESCE(c.pagu_aktivitas_forum,0)) + 
                (COALESCE(c.pagu_aktivitas_forum,0) - COALESCE(d.jml_belanja_forum,0)) AS selisih
                FROM trx_anggaran_pelaksana x 
                LEFT OUTER JOIN (SELECT a.id_pelaksana_anggaran, SUM(a.pagu_anggaran) AS pagu_anggaran FROM trx_anggaran_program_pd a 
                WHERE a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3 AND a.status_data=1
                GROUP BY a.id_pelaksana_anggaran, a.status_data) a ON x.id_pelaksana_anggaran = a. id_pelaksana_anggaran
                LEFT OUTER JOIN (SELECT b.id_pelaksana_anggaran, SUM(b.pagu_forum)  AS pagu_kegiatan FROM 
                (SELECT p.id_pelaksana_anggaran, a.pagu_forum FROM trx_anggaran_kegiatan_pd a 
                INNER JOIN trx_anggaran_program_pd p ON a.id_program_pd = p.id_program_pd
                WHERE a.status_data=1 AND a.status_pelaksanaan <> 2 AND a.status_pelaksanaan <> 3 AND p.status_pelaksanaan <> 2 AND p.status_pelaksanaan <> 3) b 
                GROUP BY b.id_pelaksana_anggaran) b ON x.id_pelaksana_anggaran=b.id_pelaksana_anggaran
                LEFT OUTER JOIN (SELECT c.id_pelaksana_anggaran, SUM(c.pagu_anggaran) AS pagu_aktivitas_forum FROM 
                (SELECT p.id_pelaksana_anggaran, y.pagu_anggaran  FROM trx_anggaran_aktivitas_pd y 
                INNER JOIN trx_anggaran_pelaksana_pd z ON y.id_pelaksana_pd = z.id_pelaksana_pd
                INNER JOIN trx_anggaran_kegiatan_pd x ON z.id_kegiatan_pd = x.id_kegiatan_pd 
                INNER JOIN trx_anggaran_program_pd p ON x.id_program_pd = p.id_program_pd
                WHERE y.status_data=1 AND y.status_pelaksanaan <> 1 AND x.status_pelaksanaan <> 2 AND x.status_pelaksanaan <> 3 AND p.status_pelaksanaan <> 2 AND p.status_pelaksanaan <> 3) c 
                GROUP BY c.id_pelaksana_anggaran) c ON x.id_pelaksana_anggaran=c.id_pelaksana_anggaran
                LEFT OUTER JOIN (SELECT d.id_pelaksana_anggaran, SUM(d.jml_belanja_rkpd) AS jml_belanja_forum FROM 
                (SELECT p.id_pelaksana_anggaran, w.jml_belanja_rkpd  FROM trx_anggaran_belanja_pd w
                INNER JOIN trx_anggaran_aktivitas_pd y ON w.id_aktivitas_pd = y.id_aktivitas_pd
                INNER JOIN trx_anggaran_pelaksana_pd z ON y.id_pelaksana_pd = z.id_pelaksana_pd
                INNER JOIN trx_anggaran_kegiatan_pd x ON z.id_kegiatan_pd = x.id_kegiatan_pd 
                INNER JOIN trx_anggaran_program_pd p ON x.id_program_pd = p.id_program_pd
                WHERE y.status_data=1 AND y.status_pelaksanaan <> 1 AND x.status_pelaksanaan <> 2 AND x.status_pelaksanaan <> 3 AND p.status_pelaksanaan <> 2 AND p.status_pelaksanaan <> 3) d 
                GROUP BY d.id_pelaksana_anggaran) d ON x.id_pelaksana_anggaran=d.id_pelaksana_anggaran
                WHERE x.id_unit = '.$req->id_unit.' AND x.id_pelaksana_anggaran = '.$req->id_pelaksana_rkpd);

        try{     
            $data = TrxAnggaranPelaksana::find($req->id_pelaksana_rkpd);        
            if ($req->status_data == 0) {
                $data->status_data=1;
                if($cekpagu != NULL && $cekpagu[0]->selisih == 0){
                    try{
                        $data->save (['timestamps' => false]);
                        return response ()->json (['pesan'=>'Data Berhasil Direviu','status_pesan'=>'1']);
                    }
                    catch(QueryException $e){
                        $error_code = $e->errorInfo[1] ;
                        return response ()->json (['pesan'=>'Data Gagal Direviu ('.$error_code.')','status_pesan'=>'0']);
                    }
                } else {
                    return response ()->json (['pesan'=>'Data Gagal Direviu, Cek Kembali Program, Kegiata, Aktivitas dan Belanja OPD','status_pesan'=>'0']);
                }
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

    public function hapusPelaksanaRKPD(Request $req)
    {
        // $cek = DB::SELECT('SELECT COUNT(*) AS jml_data FROM trx_rkpd_final_program_pd WHERE id_pelaksana_rkpd='.$req->id_pelaksana_rkpd);
        // if($cek == NULL || $cek[0]->jml_data == 0){
            TrxAnggaranPelaksana::where('id_pelaksana_anggaran',$req->id_pelaksana_anggaran)->delete ();
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1'] );
        // } else {
            // return response ()->json (['pesan'=>'Data Gagal Dihapus, Data Pelaksana Masih digunakan Oleh OPD','status_pesan'=>'0']);
        // }
    }
}
