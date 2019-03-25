<?php
namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranKegIndikatorPd extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_anggaran_keg_indikator_pd';
    protected $primaryKey = 'id_indikator_kegiatan';
    protected $fillable = [
    	'id_indikator_kegiatan',
		'id_kegiatan_pd',
		'no_urut',
		'id_indikator_rkpd_final',
		'tahun_anggaran',
		'id_perubahan',
		'kd_indikator',
		'uraian_indikator_kegiatan',
		'tolok_ukur_indikator',
		'target_renstra',
		'target_renja',
		'indikator_output',
		'id_satuan_output',
		'indikator_input',
		'target_input',
		'id_satuan_input',
		'status_data',
		'sumber_data',];
}