<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxIkuOpdSasaran extends Model
{
    protected $table = 'kin_trx_iku_opd_sasaran';
    protected $primaryKey = 'id_iku_opd_sasaran';
    protected $fillable = ['id_dokumen', 'id_indikator_sasaran_renstra', 'id_sasaran_renstra', 'id_indikator', 'flag_iku', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

}
