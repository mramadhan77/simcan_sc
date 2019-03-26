<?php
namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranPelaksanaPd extends Model
{
    public $timestamps = false;
    protected $table = 'trx_anggaran_pelaksana_pd';
    protected $primaryKey = 'id_pelaksana_pd';
    protected $fillable = ['id_pelaksana_pd',
		'id_kegiatan_pd',
		'no_urut',
		'id_pelaksana_rkpd_final',
		'tahun_anggaran',
		'id_sub_unit',
		'id_pelaksana_renja',
		'id_lokasi',
		'sumber_data',
		'ket_pelaksana',
		'status_pelaksanaan',
		'status_data',
		'hak_akses'	 ];


}