<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxRealEs2Sasaran extends Model
{
    protected $table = 'kin_trx_real_es2_sasaran';
    protected $primaryKey = 'id_real_sasaran';
    protected $fillable = [ 'id_dokumen_real', 'id_perkin_sasaran', 'id_sasaran_renstra', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;


}