<?php

namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranPelaksana extends Model
{
    protected $table = 'trx_anggaran_pelaksana';
    protected $fillable = ['id_pelaksana_anggaran',
            'id_anggaran_pemda',
            'tahun_anggaran',
            'no_urut',
            'id_urusan_anggaran',
            'id_pelaksana_rkpd_final',
            'id_unit',
            'pagu_rkpd_final',
            'pagu_anggaran',
            'hak_akses',
            'sumber_data',
            'status_pelaksanaan',
            'ket_pelaksanaan',
            'status_data',];
    protected $primaryKey = 'id_pelaksana_anggaran';
    public $timestamps = false;


}
