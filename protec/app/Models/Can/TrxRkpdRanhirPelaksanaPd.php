<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRkpdRanhirPelaksanaPd extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_rkpd_ranhir_pelaksana_pd';
    protected $primaryKey = 'id_pelaksana_pd';
    protected $fillable = ['id_pelaksana_pd',
			'tahun_forum',
			'no_urut',
			'id_kegiatan_pd',
			'id_pelaksana_forum',
			'id_sub_unit',
			'id_pekasana_renja',
			'id_lokasi',
			'sumber_data',
			'ket_pelaksana',
			'status_data',
			'status_pelaksanaan',
			 ];


}