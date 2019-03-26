<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxIkuOpdDok extends Model
{
    protected $table = 'kin_trx_iku_opd_dok';
    protected $primaryKey = 'id_dokumen';
    protected $fillable = ['no_dokumen', 'tgl_dokumen', 'uraian_dokumen', 'id_renstra', 'id_unit', 'id_perubahan', 'status_dokumen', 'created_at', 'updated_at'];

    public $timestamps = true;

}
