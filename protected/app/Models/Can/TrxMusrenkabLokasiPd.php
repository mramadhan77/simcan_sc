<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxMusrenkabLokasiPd extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_musrenkab_lokasi_pd';
    protected $primaryKey = 'id_lokasi_pd';
    protected $fillable = ['tahun_forum', 'no_urut', 'id_aktivitas_pd', 'id_lokasi_forum', 'id_lokasi_pd', 'id_lokasi', 'id_lokasi_renja', 'id_lokasi_teknis', 'jenis_lokasi', 'volume_1', 'volume_usulan_1', 'volume_2', 'volume_usulan_2', 'id_satuan_1', 'id_satuan_2', 'id_desa', 'id_kecamatan', 'rt', 'rw', 'uraian_lokasi', 'lat', 'lang', 'status_data', 'status_pelaksanaan', 'ket_lokasi', 'sumber_data'
				];


}