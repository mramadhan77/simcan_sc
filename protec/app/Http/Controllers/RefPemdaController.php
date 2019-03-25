<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;
use Yajra\Datatables\Datatables;
use Session;
use DB;
use Validator;
use Response;
use App\Models\RefPemda;
use App\Http\Controllers\SettingController;
use App\MenuForm;
use App\CekAkses;
use Auth;

class RefPemdaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        if($this->getState()==1){
            $getImage = '<img src="{{ asset("vendor/default.png") }}" class="img-thumbnail" width="260" height="300" >';
        } else {
            $getImage = '<img src="{{ asset("/protected/vendor/tecnickcom/tcpdf/examples/images/logo_example.png") }}" class="img-thumbnail" width="260" height="300" >';
        }
        // if(Auth::check()){ 
            return view('parameter.ref_pemda');
        // } else {
            // return view ( 'errors.401' );
        // }
        
    }

    public function getState()
    {
        $test = new MenuForm;
        $result=$test->getApp();

        if ($result == 1){
          $getState = 0;
        } else {
          $getState = 1;
        } 
        return json_encode($getState);
    }

    public function getUnit()
    {
      $refunit = DB::SELECT('SELECT kd_prov,kd_kab,id_pemda,nm_prov,nm_kabkota,ibu_kota,nama_jabatan_kepala_daerah,nama_kepala_daerah,
          nama_jabatan_sekretariat_daerah,nama_sekretariat_daerah,nip_sekretariat_daerah,unit_perencanaan,nama_kepala_bappeda,nip_kepala_bappeda,
          unit_keuangan,nama_kepala_bpkad,nip_kepala_bpkad,alamat,no_telepon,no_faksimili,email FROM ref_pemda LIMIT 1');

      return DataTables::of($refunit)
      ->make(true); 
    }
        
    public function getPemda()
    {
        
        if($this->getState()==0){
        $refpemda = DB::SELECT('SELECT kd_prov,kd_kab,id_pemda,nm_prov,nm_kabkota,ibu_kota,nama_jabatan_kepala_daerah,
                    nama_kepala_daerah, nama_jabatan_sekretariat_daerah,nama_sekretariat_daerah,nip_sekretariat_daerah,
                    unit_perencanaan,nama_kepala_bappeda,nip_kepala_bappeda,unit_keuangan,nama_kepala_bpkad,nip_kepala_bpkad,
                    CONCAT(RIGHT(CONCAT("0",kd_prov),2),".",RIGHT(CONCAT("0",kd_kab),2)) AS kode_pemda,
                    b.nm_unit as nm_perencana, c.nm_unit as nm_keuangan, "<img src=""'.asset('vendor/default.png').'"" class=""img-thumbnail"" width=""260"" height=""300"" >" as  iPemda,
                    alamat, no_telepon, no_faksimili, email
                    FROM ref_pemda a
                    LEFT OUTER JOIN ref_unit b ON a.unit_perencanaan = b.id_unit
                    LEFT OUTER JOIN ref_unit c ON a.unit_keuangan = c.id_unit
                    LIMIT 1'); 
        } else {
        $refpemda = DB::SELECT('SELECT 99 as kd_prov,99 as kd_kab,id_pemda,nm_prov,"KABUPATEN/KOTA SIMULASI" as nm_kabkota,ibu_kota,"BUPATI/WALIKOTA SIMULASI" as nama_jabatan_kepala_daerah,"Kepala Daerah Simulasi" as nama_kepala_daerah,
            nama_jabatan_sekretariat_daerah,"Sekretaris Daerah Simulasi" as nama_sekretariat_daerah,"000000000000000000" as nip_sekretariat_daerah,unit_perencanaan,"Kepala Badan Perencanaan Simulasi" as nama_kepala_bappeda,"000000000000000000" as nip_kepala_bappeda,unit_keuangan,"Kepala Badan Keuangan Simulasi" as nama_kepala_bpkad,"000000000000000000" as nip_kepala_bpkad,"Perencanaan Simulasi" as nm_perencana, "Keuangan Simulasi" as nm_keuangan,
            "99.99" AS kode_pemda, "<img src=""'.asset('/protected/vendor/tecnickcom/tcpdf/examples/images/logo_example.png').'"" class=""img-thumbnail"" width=""260"" height=""300"" >" as  iPemda, 
            alamat, no_telepon, no_faksimili, email FROM ref_pemda LIMIT 1');   
        }
        
        return json_encode($refpemda);
    }

    public function editPemda(Request $req)
    {
        $data = RefPemda::find($req->id_pemda) ;
        $data->ibu_kota = $req->ibu_kota ;
        $data->nama_jabatan_kepala_daerah = $req->jabatan_kada ;
        $data->nama_kepala_daerah = $req->nama_kada ;
        $data->nama_jabatan_sekretariat_daerah = 'Sekretaris Daerah' ;
        $data->nama_sekretariat_daerah = $req->nama_sekda ;
        $data->nip_sekretariat_daerah = $req->nip_sekda ;
        $data->unit_perencanaan = $req->unit_perencana ;
        $data->nama_kepala_bappeda = $req->nama_kabappeda ;
        $data->nip_kepala_bappeda = $req->nip_kabappeda ;
        $data->unit_keuangan = $req->unit_keuangan ;
        $data->nama_kepala_bpkad = $req->nama_kabpkad ;
        $data->nip_kepala_bpkad = $req->nip_kabpkad ;
        $data->alamat = $req->alamat ;
        $data->no_telepon = $req->no_telepon ;
        $data->no_faksimili = $req->no_faksimili ;
        $data->email = $req->email ;

    if($this->getState()==0){
        try{
        	$data->save (['timestamps' => false]);
        	return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']);
        }
          catch(QueryException $e){
             $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        }
    } else {
        return response ()->json (['pesan'=>'Belum Terdaftar, Silahkan Hubungi Perwakilan BPKP setempat...','status_pesan'=>'0']);
    }

    }

    public function getRefUnit(){
      $refunit=DB::SELECT('SELECT (@id:=@id+1) as no_urut, id_unit, id_bidang, kd_unit, nm_unit FROM ref_unit,(SELECT @id:=0 ) var_id ');
      return DataTables::of($refunit)
      ->make(true);
    }

    public function getImage(Request $req)
    {
      $this->validate($request, [
            'logo_pemda' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

      $file = $request->file('logo_pemda');
    }

    public function dehashPemda()
    {
        $test = new SettingController;
        $result=$test->dePemda();
        return response ()->json ($result);
    }

    public function getPemdaX1(Request $req)
    {
        
        $test = new SettingController;

        // $result=DB::SELECT('SELECT * FROM ref_kabupaten WHERE UPPER(Left(nama_kab,9)) = "KABUPATEN"');
        $result=$test->hashPemda("PEMERINTAH DAERAH SIMULASI");

        
        return json_encode($result);
    }

    public function hashPemda(Request $req)
    {
        
        // $test = new SettingController;
        // $xPemda = $test->hashPemda($req->nama_kab);
        // $data=DB::UPDATE('UPDATE ref_kabupaten set pemdax="'.$xPemda.'" WHERE id_kab='.$req->id_kab);

    }

    // public function getPemdaX1()
    // {
    //     $test = new SettingController;
    //     $result=$test->dehashPemda("dVFtYlgxTW9COTZzZWEzaGQ2OFVzMGozOFVJU3RLY0lQeDFyNFZvYkRiMD0=");

    //     return json_encode($result);
    // }

}
