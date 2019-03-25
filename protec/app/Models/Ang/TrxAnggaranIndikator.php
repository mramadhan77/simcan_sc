<?php

namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranIndikator extends Model
{
    protected $table = 'trx_anggaran_indikator';
    protected $primaryKey = 'id_indikator_program_rkpd';
    protected $fillable = ['id_indikator_program_rkpd',
            'id_anggaran_pemda',
            'id_indikator_rkpd_final',
            'tahun_rkpd',
            'no_urut',
            'id_perubahan',
            'kd_indikator',
            'uraian_indikator_program_rkpd',
            'tolok_ukur_indikator',
            'target_rkpd',
            'target_keuangan',
            'indikator_input',
            'target_input',
            'id_satuan_input',
            'indikator_output',
            'id_satuan_output',
            'status_data',
            'sumber_data',];
    public $timestamps = false;

}
