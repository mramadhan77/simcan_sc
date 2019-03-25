<?php
namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranAktivitasPd extends Model
{
    public $timestamps = true;
    protected $table = 'trx_anggaran_aktivitas_pd';
    protected $primaryKey = 'id_aktivitas_pd';
    protected $fillable = ['id_aktivitas_pd',
        'id_pelaksana_pd',
        'id_aktivitas_rkpd_final',
        'tahun_anggaran',
        'no_urut',
        'sumber_aktivitas',
        'sumber_dana',
        'id_perubahan',
        'id_aktivitas_asb',
        'id_program_nasional',
        'id_program_provinsi',
        'uraian_aktivitas_kegiatan',
        'volume_aktivitas_1',
        'volume_rkpd_1',
        'volume_aktivitas_2',
        'volume_rkpd_2',
        'id_satuan_1',
        'id_satuan_2',
        'id_satuan_publik',
        'jenis_kegiatan',
        'pagu_rkpd',
        'pagu_anggaran',
        'status_data',
        'status_pelaksanaan',
        'keterangan_aktivitas',
        'group_keu',
        'sumber_data',
        'created_at',
        'updated_at',
        'checksum',];
}