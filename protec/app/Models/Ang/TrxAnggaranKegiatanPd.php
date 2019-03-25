<?php
namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranKegiatanPd extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_anggaran_kegiatan_pd';
    protected $primaryKey = 'id_kegiatan_pd';
    protected $fillable = ['id_kegiatan_pd',
        'id_program_pd',
        'id_kegiatan_pd_rkpd_final',
        'id_unit',
        'id_perubahan',
        'tahun_anggaran',
        'no_urut',
        'id_renja',
        'id_rkpd_renstra',
        'id_program_renstra',
        'id_kegiatan_renstra',
        'id_kegiatan_ref',
        'uraian_kegiatan_forum',
        'pagu_tahun_kegiatan',
        'pagu_kegiatan_renstra',
        'pagu_plus1_renja',
        'pagu_plus1_forum',
        'pagu_forum',
        'keterangan_status',
        'status_data',
        'status_pelaksanaan',
        'sumber_data',
        'kelompok_sasaran',
        'created_at',
        'updated_at',
        'checksum',];


}