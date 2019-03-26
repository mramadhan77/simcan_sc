<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Auth;
use Datatables;
use Session;
use Validator;
use Response;
use XMLWriter;
use SimpleXMLElement;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Database\QueryException;
use App\Fungsi AS Fungsi;
use App\MenuForm;
use App\CekAkses;

class TransferDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $menu = require(base_path().'/config/simda.php');
        $getUrl = $menu['url'];

        // if(Auth::check()){ 
            return view('apbd.index')->with(compact('getUrl'));
        // } else {
            // return view ( 'errors.401' );
        // }        
        
    }

    public function KirimData(Request $request)
    {
        $menu = require(base_path().'/config/simda.php');
        $getUrl = $menu['url'];
        $url = $getUrl.'/'.$request->jnsData.'/'.$request->KodeMinta;
        // $getUrl = json_decode(@file_get_contents('http://193.168.97.20:8080/datasnap/rest/scapi/KirimUrusan/'.$request->KodeMinta));
        $getUrl = json_decode(@file_get_contents($url));

        return response ()->json ($getUrl); 
    }

}