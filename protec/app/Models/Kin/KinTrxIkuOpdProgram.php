<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxIkuOpdProgram extends Model
{
    protected $table = 'kin_trx_iku_opd_program';
    protected $primaryKey = 'id_iku_opd_program';
    protected $fillable = ['id_iku_opd_sasaran', 'id_indikator_program_renstra', 'id_program_renstra', 'id_indikator', 'flag_iku',
        'id_esl3', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

}
