<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxRenjaRancanganAktivitas extends Model
{
    protected $table = 'trx_renja_rancangan_aktivitas';
    protected $primaryKey = 'id_aktivitas_renja';
    protected $fillable =['tahun_renja',
                'no_urut',
                'id_aktivitas_renja',
                'id_renja',
                'sumber_aktivitas',
                'id_aktivitas_asb',
                'uraian_aktivitas_kegiatan',
                'tolak_ukur_aktivitas',
                'target_output_aktivitas',
                'id_program_nasional',
                'id_program_provinsi',
                'jenis_kegiatan',
                'sumber_dana',
                'pagu_aktivitas',
                'pagu_musren',
                'status_data',
                'status_musren',
                'id_satuan_publik',
            ];
    // protected $relations = [];

    public $timestamps = false;

    // public $fillable = [
    // 	'name',
    // 	'address',
    // 	'lat',
    // 	'lng',
    // ];
}
