<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxRealEs4Kegiatan extends Model
{
    protected $table = 'kin_trx_real_es4_kegiatan';
    protected $primaryKey = 'id_real_kegiatan';
    protected $fillable = [ 'id_dokumen_real', 'id_perkin_kegiatan', 'id_kegiatan_renstra', 
    'pagu_tahun', 'pagu_t1', 'pagu_t2', 'pagu_t3', 'pagu_t4', 'real_t1', 'real_t2', 'real_t3', 'real_t4', 'uraian_deviasi', 'uraian_renaksi',
    'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;


}
