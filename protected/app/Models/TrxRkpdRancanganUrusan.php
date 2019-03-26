<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRkpdRancanganUrusan extends Model
{
    protected $table = 'trx_rkpd_rancangan_urusan';
    protected $fillable = ['tahun_rkpd', 'no_urut', 'id_rkpd_rancangan', 'id_urusan_rkpd', 'id_bidang'];
    protected $primaryKey = 'id_urusan_rkpd';
    public $timestamps = false;


}
