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
use App\Models\Can\TrxRenstraVisi;

class TrxRenstraVisiController extends Controller
{
  public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function getVisiRenstra($id_unit)
    {
          $visirenstra=DB::SELECT('SELECT thn_id, no_urut, id_visi_renstra, id_unit, id_perubahan, thn_awal_renstra, thn_akhir_renstra, uraian_visi_renstra, 
            id_status_dokumen, CONCAT(thn_awal_renstra," s.d ",thn_akhir_renstra) as thn_periode
            FROM trx_renstra_visi
            WHERE id_unit ='.$id_unit);  

      return DataTables::of($visirenstra)
          ->addColumn('action', function ($visirenstra) {
              return 
              '<div class="btn-group">
                  <button type="button" class="btn btn-info dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-wrench fa-fw fa-lg"></i></span>Aksi <span class="caret"></span></button>
                  <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                      <a class="btnDetailVisi dropdown-item"><i class="fa fa-paper-plane-o fa-fw fa-lg text-info"></i> Detail Visi</a>
                    </li>
                  </ul>
                </div>'
              ;})
          ->make(true);
    }
}
