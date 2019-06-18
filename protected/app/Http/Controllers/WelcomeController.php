<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Response;
use Session;
use Auth;
use Khill\Lavacharts\Lavacharts;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Http\Controllers\SettingController;
use App\Models\RefPemda;
use App\Models\RefUnit;
use App\Models\RefIndikator;
use App\Models\RefUrusan;
use App\Models\RefBidang;
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
use Carbon\Carbon;
use App\MenuForm;


class WelcomeController extends Controller
{
	
	public function index()
	{
		$trxRpjmd = DB::SELECT('SELECT a.no_urut as no_misi, a.uraian_misi_rpjmd
					FROM trx_rpjmd_misi AS a WHERE a.no_urut < 90');

		$trxVisi = DB::SELECT('SELECT b.no_urut, b.uraian_visi_rpjmd, a.tahun_1, a.tahun_5
					FROM trx_rpjmd_dokumen AS a
					INNER JOIN trx_rpjmd_visi AS b ON b.id_rpjmd = a.id_rpjmd
					LIMIT 1');

		$test = new SettingController;
        $rPemda=$test->dePemda();

        $testAppX = new MenuForm;
        $xApp=$testAppX->testAppX();

        if($test->getState()==1){
        	$iPemda = '<img class="box-icon" src="asset("/vendor/default.png")" />';
        } else {
        	$iPemda = '<img class="box-icon" src="asset("/protected/vendor/tecnickcom/tcpdf/examples/images/logo_example.png")" />';
        }

        // $xdata = $this->getApi();
		// Log::info($xdata);
		$app = require(base_path().'/app/rilis.php');        
        $a = $app['appVersi'];
        $b = $app['rilis'];

        Session::put('versiApp','rilis ' . $b);

		return view("umum.welcome")->with(compact('trxRpjmd','trxVisi','rPemda','iPemda','xApp'));	
	}

	public function index_tahunan()
	{
		if(Auth::check()){ 
			if(session::get('AppType') == 0) {
				return view("umum.dash_tahunanprop");
			} else {
				return view("umum.dash_tahunan");
			}
		} else {
			return view ( 'errors.401' );
		}	
	}

	public function index_asb()
	{
		if(Auth::check()){ 
			return view("umum.dash_asb");
		} else {
			return view ( 'errors.401' );
		}	
	}

	public function index_parameter()
	{
		if(Auth::check()){ 
			return view("umum.dash_parameter");
		} else {
			return view ( 'errors.401' );
		}	
	}

	public function getApi(){
        $menu = require(base_path().'/config/menu.php');
        $getApp = $menu['li'];

      	$getLog = DB::SELECT('SELECT fl1 FROM ref_log_akses WHERE `id_log`= "'.$getApp.'" and fr4 >="'.Carbon::now().'"');

	        if($getLog != null){
	          $json = $getLog;
	        } else {
	          $getUrl = json_decode(@file_get_contents('http://simda-online.com/scapi2/api/get/0&'.$getApp));

	          if($getUrl === false){
	              echo 'Koneksi Internet Server Tidak Ada... (error_log:msg2)';
	              die();
	          }

	          if($getUrl != true){
	              echo 'Silahkan Cek File Config, atau Hubungi Tim Perwakilan ... (error_log:msg1)';
	              die();
	          } else{           
	            $cekLog = DB::SELECT('SELECT * FROM ref_log_akses WHERE `id_log`= "'.$getApp.'"');
	            $getLog = \App\Models\RefLogAkses::where(['id_log' => $getApp]);

	            if($cekLog != null){
	              $addLog = DB::UPDATE('UPDATE ref_log_akses SET `id_log`= "'.$getApp.'",`fd1`="'.$getUrl[0]->nm.'", `fp2`="'.$getUrl[0]->fu.'", 
	                    `fu3`="'.$getUrl[0]->fu.'", `fr4`="'.$getUrl[0]->fn.'", `fl1`="'.$getUrl[0]->l1.'" WHERE `id_log`= "'.$getApp.'"');
	            } else {
	              $addLog = DB::INSERT('INSERT INTO ref_log_akses (`id_log`, `fd1`, `fp2`, `fu3`, `fr4`,`fl1`) VALUES 
	                    ("'.$getApp.'","'.$getUrl[0]->nm.'","'.$getUrl[0]->fu.'","'.$getUrl[0]->fu.'","'.$getUrl[0]->fn.'","'.$getUrl[0]->l1.'")');
	            }

	            if($addLog != 0){
	              return $getUrl;
	            } else {
	              echo 'Hubungi Admin...(error_log:1)';
	              die();
	            }
	          }
	        } 
    }

}