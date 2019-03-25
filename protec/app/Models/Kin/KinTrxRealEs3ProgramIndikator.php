<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxRealEs3ProgramIndikator extends Model
{
    protected $table = 'kin_trx_real_es3_program_indikator';
    protected $primaryKey = 'id_real_indikator';
    protected $fillable = ['id_real_program', 'id_perkin_indikator', 'id_indikator_program_renstra', 'target_tahun', 
    'target_t1', 'target_t2', 'target_t3', 'target_t4', 'real_t1', 'real_t2', 'real_t3', 'real_t4', 'uraian_deviasi', 'uraian_renaksi',
    'reviu_real', 'reviu_deviasi', 'reviu_renaksi', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

}
