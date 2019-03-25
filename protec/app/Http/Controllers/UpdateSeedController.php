<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Datatables;
use Yajra\Datatables\Html\Builder;
use DB;
use App\Http\Requests;
use App\MenuForm;
use App\CekAkses;
use Codedge\Updater\UpdaterFacade;
use Codedge\Updater\UpdaterServiceProvider;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use ReflectionClass;
use Auth;

class UpdateController extends Controller
{
    protected $client;

	public function index(Request $request, MenuForm $a){
        // $versionJson = @file_get_contents(realpath(base_path().'/composer.json'));
        // $version = json_decode($versionJson);

        // $app = require(base_path().'/app/rilis.php');        
        // $x = $app['appVersi'];
        // $y = $app['rilis'];
        // check here
        // $menu = require(base_path().'/config/menu.php');        
        // $b = $a->reveal($menu['state']);
        // $c = $a->getApp();
        // $check = json_decode(@file_get_contents($b."get/version?id=smdspr&version=".$version->version."&kd=".$menu['li']));
        // $result = ($version->version == $check->version);
        // $result = true;
        // $kronologi = json_decode(@file_get_contents($b."get/versionupdate?id=smdspr&version=".$version->version."&kd=".$menu['li']));
        // return view('update.index', [
        //     'currentVersion' => $version,
        //     'available' => $check,
        //     'result' => $x,
        //     'kronologi' => $y,
        //     'alamat' => $b 
        // ]);

        return view('update.index');
    }

    public function getApi(){

        $menu = require(base_path().'/config/menu.php');
        $getApp = $menu['li'];
        $getUrl = json_decode(@file_get_contents('http://simda-online.com/scapi2/api/get/0&'.$getApp));

        $cekLog = DB::SELECT('SELECT * FROM ref_log_akses WHERE `id_log`= "'.$getApp.'"');

        if($cekLog != null){
                $addLog = DB::UPDATE('UPDATE ref_log_akses SET `id_log`= "'.$getApp.'",`fd1`="'.$getUrl[0]->nm.'", `fp2`="'.$getUrl[0]->fu.'", 
                    `fu3`="'.$getUrl[0]->fu.'", `fr4`"'.$getUrl[0]->fn.'" WHERE `id_log`= "'.$getApp.'"');
        } else {
                $addLog = DB::INSERT('INSERT INTO ref_log_akses (`id_log`, `fd1`, `fp2`, `fu3`, `fr4`) VALUES 
                    ("'.$getApp.'","'.$getUrl[0]->nm.'","'.$getUrl[0]->fu.'","'.$getUrl[0]->fu.'","'.$getUrl[0]->fn.'")');
        }
        
        if($addLog != 0){
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); 
        } else {
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        } 
    }

    public function updateDB(Request $req){

        

        $files = require(base_path().'/database/'.$req->nama_file); 
        $versionJson = @file_get_contents($files);

        $update=DB::unprepared($versionJson);

        if($update != 0){
            return response ()->json (['pesan'=>'Data Berhasil Dihapus','status_pesan'=>'1']);
        } else {
            return response ()->json (['pesan'=>'Data Gagal Dihapus','status_pesan'=>'0']);
        }

        // return response ()->json ($versionJson);

    }


    // public function index()
    // {
    //     // This downloads and install the latest version of your repo
    //     Updater::update();
        
    //     // Just download the source and do the actual update elsewhere
    //     Updater::fetch();
        
    //     // Check if a new version is available and pass current version
    //     Updater::isNewVersionAvailable('1.2');
    // }

    public function testGetFacadeAccessor()
    {
        $accessor = 'updater';
        $class = UpdaterFacade::class;

        $reflection = new ReflectionClass($class);

        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $msg = "Expected class '$class' to have an accessor of '$accessor'.";

        $this->assertSame($accessor, $method->invoke(null), $msg);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('self-update', [
            'default' => 'github',
            'version_installed' => '',
            'repository_types' => [
                'github' => [
                    'type' => 'github',
                    'repository_vendor' => 'laravel',
                    'repository_name' => 'laravel',
                    'repository_url' => '',
                    'download_path' => '/tmp',
                ],
            ],
            'log_events' => false,
            'mail_to' => [
                'address' => '',
                'name' => '',
            ],
        ]);
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            UpdaterServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Updater' => UpdaterFacade::class,
        ];
    }

}