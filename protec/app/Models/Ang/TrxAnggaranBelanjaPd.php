<?php
namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranBelanjaPd extends Model
{
    public $timestamps = true;
    protected $table = 'trx_anggaran_belanja_pd';
    protected $primaryKey = 'id_belanja_pd';
    protected $fillable = ['id_belanja_pd',
        'id_aktivitas_pd',
        'id_belanja_rkpd_final',
        'tahun_anggaran',
        'no_urut',
        'id_zona_ssh',
        'sumber_belanja',
        'id_aktivitas_asb',
        'id_item_ssh',
        'id_rekening_ssh',
        'uraian_belanja',
        'id_satuan_1',
        'id_satuan_2',
        'volume_1',
        'volume_2',
        'koefisien',
        'harga_satuan',
        'jml_belanja',
        'volume_1_rkpd',
        'volume_2_rkpd',
        'koefisien_rkpd',
        'harga_satuan_rkpd',
        'jml_belanja_rkpd',
        'status_data',
        'sumber_data',
        'created_at',
        'updated_at',
        'checksum',];
}

