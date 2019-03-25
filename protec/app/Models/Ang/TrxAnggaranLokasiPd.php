<?php
namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranLokasiPd extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_anggaran_lokasi_pd';
    protected $primaryKey = 'id_lokasi_pd';
    protected $fillable = ['id_lokasi_pd',
        'id_lokasi_rkpd_final',
        'id_aktivitas_pd',
        'tahun_anggaran',
        'no_urut',
        'jenis_lokasi',
        'id_lokasi',
        'id_lokasi_teknis',
        'volume_1',
        'volume_usulan_1',
        'volume_2',
        'volume_usulan_2',
        'id_satuan_1',
        'id_satuan_2',
        'id_desa',
        'id_kecamatan',
        'rt',
        'rw',
        'uraian_lokasi',
        'lat',
        'lang',
        'status_data',
        'status_pelaksanaan',
        'ket_lokasi',
        'sumber_data',];


}