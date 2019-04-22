<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxRealEs4KegiatanIndikator extends Model
{
    protected $table = 'kin_trx_real_es4_kegiatan_indikator';
    protected $primaryKey = 'id_real_indikator';
    protected $fillable = ['id_real_kegiatan', 'id_perkin_indikator', 'id_indikator_kegiatan_renstra', 'target_tahun', 'real_fisik',
    'target_t1', 'target_t2', 'target_t3', 'target_t4', 'real_t1', 'real_t2', 'real_t3', 'real_t4', 'uraian_deviasi', 'uraian_renaksi',
    'reviu_real', 'reviu_deviasi', 'reviu_renaksi', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

}
