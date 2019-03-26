<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Datatables;
use Session;
use Auth;
use CekAkses;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use App\Models\RefPemda;
use App\Models\RefUnit;
use App\Models\RefIndikator;
use App\Models\RefUrusan;
use App\Models\RefBidang;
use App\Models\Can\TrxRenstraMisi;

class TrxRenstraMisiController extends Controller
{
  public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getMisiRenstra($id_visi_renstra)
    {
      $misirenstra=DB::SELECT('SELECT thn_id, no_urut, id_visi_renstra, id_misi_renstra, id_perubahan, uraian_misi_renstra
                FROM trx_renstra_misi WHERE id_visi_renstra='.$id_visi_renstra.' ORDER BY no_urut DESC');

      return DataTables::of($misirenstra)
          ->addColumn('action', function ($misirenstra) {
              return 
              '<div class="btn-group">
                  <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                  <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                      <a class="btnDetailMisi dropdown-item"><i class="fa fa-location-arrow fa-fw fa-lg text-success"></i> Detail Data Tujuan</a>
                    </li>
                  </ul>
                </div>'
              ;})
          ->make(true);
    }
}
