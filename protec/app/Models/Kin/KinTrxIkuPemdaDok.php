<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxIkuPemdaDok extends Model
{
    protected $table = 'kin_trx_iku_pemda_dok';
    protected $primaryKey = 'id_dokumen';
    protected $fillable = [ 'no_dokumen', 'tgl_dokumen', 'uraian_dokumen', 
        'id_rpjmd', 'id_perubahan', 'status_dokumen', 'created_at', 'updated_at'];

    public $timestamps = true;


}