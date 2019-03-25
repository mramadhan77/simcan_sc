<?php

namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranUrusan extends Model
{
    protected $table = 'trx_anggaran_urusan';
    protected $fillable = ['id_urusan_anggaran',
        'id_anggaran_pemda',
        'tahun_anggaran',
        'no_urut',
        'id_bidang',
        'sumber_data',
        'id_urusan_rkpd_final'];
    protected $primaryKey = 'id_urusan_anggaran';
    public $timestamps = false;


}
