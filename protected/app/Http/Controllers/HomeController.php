<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Codedge\Updater\UpdaterFacade;
use Codedge\Updater\UpdaterServiceProvider;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Carbon\Carbon;
use ReflectionClass;
use DB;
use Datatables;
use Session;
use Auth;
use App\CekAkses;
use App\MenuForm;
use App\Models\RefSetting;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    $dt = Carbon::now()->setTimeZone('Asia/Jakarta');
            // $dt = Carbon::today();

    if($dt > '2019-06-01 00:00:00'){
            echo 'Maaf Aplikasi ini hanya dipakai saat WORKSHOP SAKIP 2019 - tidak untuk disebarkan ke Pemda Pengguna';
            die();
    } else {

        $tahun=DB::select('SELECT a.* FROM ref_setting AS a
            WHERE a.status_setting = 1 ORDER BY a.tahun_rencana LIMIT 1');
        $AppType=DB::select('SELECT a.kd_kab FROM ref_pemda AS a LIMIT 1');
        $pemda = DB::SELECT('SELECT kd_prov, kd_kab, id_pemda, prefix_pemda, nm_prov, nm_kabkota, ibu_kota, nama_jabatan_kepala_daerah, 
            nama_kepala_daerah, nama_jabatan_sekretariat_daerah, nama_sekretariat_daerah, 
            nip_sekretariat_daerah, unit_perencanaan, nama_kepala_bappeda, nip_kepala_bappeda, 
            unit_keuangan, nama_kepala_bpkad, nip_kepala_bpkad, 
            alamat, no_telepon, no_faksimili, email,
            CONCAT(kd_prov,".",kd_kab) AS kode_pemda,
            CONCAT("No Telp : ",no_telepon," No Faks : ", no_faksimili, " email : ", email) AS kontak
            FROM ref_pemda LIMIT 1');

        $app = require(base_path().'/app/rilis.php');        
        $a = $app['appVersi'];
        $b = $app['rilis'];

        if (Session::has('versiApp')) {
            Session::forget('versiApp');
            Session::put('versiApp','rilis ' . $b);
        } else {
            Session::put('versiApp','rilis ' . $b);
        };        
        

        $menu = require(base_path().'/config/menu.php');
        $ax = new MenuForm;       
        $bx = $ax->reveal($menu['state']);
        $dx = $menu['ul'];
        if($bx == 'demo'){
            $xPemda = "Pemda Simulasi";
          }else{
            $test = new SettingController;
            $result=$test->dePemda();
            $xPemda = $result;
          }

        Session::forget('xPemda');
        Session::forget('xAlamat');
        Session::forget('xKontak');

        if(Auth::user()->status_user != 1) {
           return back();
        }  else {
            Session::put('xPemda', $xPemda);
            Session::put('xAlamat', $pemda[0]->alamat);
            Session::put('xKontak', $pemda[0]->kontak);
            Session::put('xIdPemda', $pemda[0]->id_pemda);
            if (Session::has('AppType')) {
                Session::forget('AppType');
                // Session::put('AppType',$AppType[0]->kd_kab);
                Session::put('AppType',$dx);
            } else {
                // Session::put('AppType',$AppType[0]->kd_kab);
                Session::put('AppType',$dx);
            };
            // if (Session::has('tahun')) {
            //     Session::forget('tahun');
            //     if($tahun == null){
            //         Session::put('tahun',date('Y')); 
            //     } else {
            //         Session::put('tahun',$tahun[0]->tahun_rencana); 
            //     }
            // } else {                
            //     if($tahun == null){
            //         Session::put('tahun',date('Y')); 
            //     } else {
            //         Session::put('tahun',$tahun[0]->tahun_rencana); 
            //     }
            // };

            return view('home'); 
            }
        }             
    }

    public function getState()
    {
        $menu = require(base_path().'/config/menu.php');        
        $b = $a->reveal($menu['state']);
        $c = $a->getApp();

        return json_encode($b);
    }

    public function getTahunSetting(){
        $tahun=DB::select('SELECT a.tahun_rencana FROM ref_setting AS a ORDER BY a.tahun_rencana LIMIT 5');
        if($tahun == null){
                $tahun=date('Y');
                return json_encode($tahun); 
            } else {
                return json_encode($tahun); 
            }
        
    }

    public function getUser(){ 
        $user = DB::select('SELECT (@id:=@id+1) as no_urut, a.id,a.group_id,a.`name`,a.email,a.id_unit,a.`password`,
                a.remember_token,a.created_at,a.updated_at,a.status_user,b.kd_unit,b.nm_unit,
                CASE a.status_user
                          WHEN 1 THEN "Aktif"
                          WHEN 0 THEN "Non Aktif"
                END AS status_display FROM users AS a
                LEFT OUTER JOIN ref_unit AS b ON a.id_unit = b.id_unit , (SELECT @id:=0) b WHERE a.id='.Auth::User()->id);
        return json_encode($user);
    }

    public function gantiPass(Request $request)
    {
        $validator = $this->validate($request, [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ]); 

        $data = User::find(Auth::User()->id);        
        $data->name= $request->nama;
        $data->password= bcrypt($request->password);
        try{
            $data->save (['timestamps' => false]);
            return response ()->json (['pesan'=>'Data User '.$request->nama.' Berhasil Disimpan','status_pesan'=>'1']);
         }
         catch(QueryException $e){
              $error_code = $e->errorInfo[1] ;
             return response ()->json (['pesan'=>'Data User '.$request->nama.' Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
         }
    }

}
