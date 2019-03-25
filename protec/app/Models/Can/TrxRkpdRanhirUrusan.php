<?php

namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRkpdRanhirUrusan extends Model
{
    protected $table = 'trx_rkpd_ranhir_urusan';
    protected $fillable = ['tahun_rkpd', 'no_urut', 'id_rkpd_rancangan', 'id_urusan_rkpd', 'id_bidang', 'sumber_data'];
    protected $primaryKey = 'id_urusan_rkpd';
    public $timestamps = false;


}
