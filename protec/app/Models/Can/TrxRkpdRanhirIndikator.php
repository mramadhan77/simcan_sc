<?php

namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRkpdRanhirIndikator extends Model
{
    protected $table = 'trx_rkpd_ranhir_indikator';
    protected $primaryKey = 'id_indikator_rkpd';
    protected $fillable = ['tahun_rkpd', 'no_urut', 'id_rkpd_rancangan', 'id_indikator_program_rkpd', 'id_perubahan', 'kd_indikator', 'uraian_indikator_program_rkpd', 'tolok_ukur_indikator', 'target_rpjmd', 'target_rkpd', 'status_data','sumber_data'];
    public $timestamps = false;


}
