<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefIndikator extends Model
{
    protected $table = 'ref_indikator';
    protected $primaryKey = 'id_indikator';
    protected $fillable = ['id_indikator',
				'jenis_indikator',
				'sifat_indikator',
				'nm_indikator',
				'flag_iku',
				'asal_indikator',
				'sumber_data_indikator',
				'type_indikator',
				'id_satuan_output', 
				'kualitas_indikator', 
				'id_bidang', 
				'id_aspek', 
				'status_data',
				'nama_file',
				];

    public $timestamps = false;
}
