<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxIkuPemdaRinci extends Model
{
    protected $table = 'kin_trx_iku_pemda_rinci';
    protected $primaryKey = 'id_iku_pemda';
    protected $fillable = [ 'id_dokumen', 'id_indikator_sasaran_rpjmd', 'id_indikator', 'flag_iku', 'status_data', 'unit_penanggung_jawab', 
    'created_at', 'updated_at'];

    public $timestamps = true;


}