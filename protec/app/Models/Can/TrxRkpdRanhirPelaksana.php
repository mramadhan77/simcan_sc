<?php

namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRkpdRanhirPelaksana extends Model
{
    protected $table = 'trx_rkpd_ranhir_pelaksana';
    protected $fillable = ['tahun_rkpd', 'no_urut', 'id_pelaksana_rkpd', 'id_rkpd_rancangan', 'id_urusan_rkpd', 
    'id_pelaksana_rpjmd', 'id_unit', 'pagu_rpjmd', 'pagu_rkpd', 'hak_akses', 'sumber_data', 'status_pelaksanaan', 
    'ket_pelaksanaan', 'status_data'];
    protected $primaryKey = 'id_pelaksana_rkpd';
    public $timestamps = false;


}
