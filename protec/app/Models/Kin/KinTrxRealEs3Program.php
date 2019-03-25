<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxRealEs3Program extends Model
{
    protected $table = 'kin_trx_real_es3_program';
    protected $primaryKey = 'id_real_program';
    protected $fillable = [ 'id_dokumen_real', 'id_perkin_program', 'id_program_renstra', 
    'pagu_tahun', 'pagu_t1', 'pagu_t2', 'pagu_t3', 'pagu_t4', 'real_t1', 'real_t2', 'real_t3', 'real_t4', 'uraian_deviasi', 'uraian_renaksi',
    'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;


}
