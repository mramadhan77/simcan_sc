<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxRealEs2SasaranIndikator extends Model
{
    protected $table = 'kin_trx_real_es2_sasaran_indikator';
    protected $primaryKey = 'id_real_indikator';
    protected $fillable = [ 'id_real_sasaran', 'id_perkin_indikator', 'id_indikator_sasaran_renstra', 'target_tahun', 'target_t1', 
    'target_t2', 'target_t3', 'target_t4', 'real_t1', 'real_t2', 'real_t3', 'real_t4', 'uraian_deviasi', 'uraian_renaksi', 'status_data', 'created_at'];

    public $timestamps = true;


}