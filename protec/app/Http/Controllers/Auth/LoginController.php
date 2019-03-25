<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Carbon\Carbon;
use App\MenuForm;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    // protected $redirectTo = '/kin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    protected function authenticated($request, $user){
        if(!$user->is_active){
            $menu = require(base_path().'/config/menu.php');
            $getApp = $menu['li'];  
            $cekRef = DB::SELECT('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'" AND TABLE_NAME="ref_log_akses" 
                GROUP BY TABLE_NAME');

            if($cekRef!= null){            
                $cekData = DB::SELECT('SELECT * FROM ref_log_akses WHERE id_log = "'.$getApp.'" AND fr4 >= "'.Carbon::now().'"');
                $cekLog = DB::SELECT('SELECT * FROM ref_log_akses WHERE id_log = "'.$getApp.'"');
            } else {
                $createTableSqlString =
                    "CREATE TABLE IF NOT EXISTS `ref_log_akses` (
                        `id_log` varchar(255) NOT NULL,
                        `fl1` varchar(255) DEFAULT NULL,
                        `fd1` varchar(255) DEFAULT NULL,
                        `fp2` varchar(255) DEFAULT NULL,
                        `fu3` varchar(255) DEFAULT NULL,
                        `fr4` varchar(255) DEFAULT NULL,
                        PRIMARY KEY (`id_log`) USING BTREE
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;";

                $query = DB::statement($createTableSqlString);
            };            

            $menuform = new MenuForm;

            $dt = Carbon::now()->setTimeZone('Asia/Jakarta');

            if($menuform->reveal($menu['state']) == 'demo'){
                $json = 1;
            } else {
                if($cekData != null){
                  $json = 1;
                } else {
                  $getUrl = json_decode(@file_get_contents('http://simda-online.com/scapi2/api/get/0&'.$getApp));

                  if($getUrl === false) {
                      // echo $menuform->reveal($menuform->msg2);
                      echo 'Koneksi Internet Server Tidak Ada... (error_log:msg2)';
                      die();
                  }

                  if($getUrl != true) {
                      // echo $menuform->reveal($menuform->msg);
                      echo 'Silahkan Cek File Config, atau Hubungi Tim Perwakilan ... (error_log:msg1)';
                      die();
                  } else { 

                    if($cekLog != null) {
                      $addLog = DB::UPDATE('UPDATE ref_log_akses SET `id_log`= "'.$getApp.'",`fd1`="'.$getUrl[0]->nm.'", `fp2`="'.$getUrl[0]->fu.'", 
                            `fu3`="'.$getUrl[0]->fu.'", `fr4`="'.$getUrl[0]->fn.'", `fl1`="'.$getUrl[0]->l1.'" WHERE `id_log`= "'.$getApp.'"');
                    } else {
                      $addLog = DB::INSERT('INSERT INTO ref_log_akses (`id_log`, `fd1`, `fp2`, `fu3`, `fr4`,`fl1`) VALUES 
                            ("'.$getApp.'","'.$getUrl[0]->nm.'","'.$getUrl[0]->fu.'","'.$getUrl[0]->fu.'","'.$getUrl[0]->fn.'","'.$getUrl[0]->l1.'")');
                    }

                    if($addLog != 0){
                      $json = 1;
                        } else {
                          echo 'Hubungi Super-super Admin......(error_log:1)';
                          die();
                        }
                      }
                }   
            }
        }
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), 3, 15
        );
    }

    public function showLoginForm()
    {
        $tahun=DB::select('SELECT a.tahun_rencana FROM ref_setting AS a WHERE a.status_setting IN (0,1)ORDER BY a.status_setting DESC, a.tahun_rencana ASC LIMIT 5');
        return view('user.login')->with(compact('tahun'));
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function username()
    {
        return 'email';
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    protected function attemptLogin(Request $request)
    {
        $request->session()->put('tahun', $request->input('id_tahun'));

        return $this->guard()->attempt(
            $this->credentials($request), $request->has('remember')
        );
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/login');
    }
}
