<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRkpdRanhirKegiatanPd extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_rkpd_ranhir_kegiatan_pd';
    protected $primaryKey = 'id_kegiatan_pd';
    protected $fillable = ['id_kegiatan_pd', 'id_program_pd', 'id_unit', 'tahun_forum', 'no_urut', 'id_forum_skpd', 'id_renja', 'id_rkpd_renstra', 'id_program_renstra', 'id_kegiatan_renstra', 'id_kegiatan_ref', 'uraian_kegiatan_forum', 'pagu_tahun_kegiatan', 'pagu_kegiatan_renstra', 'pagu_plus1_renja', 'pagu_plus1_forum', 'pagu_forum', 'keterangan_status', 'status_data', 'status_pelaksanaan', 'sumber_data', 'kelompok_sasaran'
			];


}