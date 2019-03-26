<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxIkuOpdKegiatan extends Model
{
    protected $table = 'kin_trx_iku_opd_kegiatan';
    protected $primaryKey = 'id_iku_opd_kegiatan';
    protected $fillable = ['id_iku_opd_program', 'id_indikator_kegiatan_renstra', 'id_kegiatan_renstra', 'id_indikator', 'flag_iku', 
        'id_esl4', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

}
