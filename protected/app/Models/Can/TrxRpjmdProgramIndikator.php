<?php

namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRpjmdProgramIndikator extends Model
{
    protected $table = 'trx_rpjmd_program_indikator';
    protected $fillable = ['thn_id', 'no_urut', 'id_program_rpjmd', 'id_indikator_program_rpjmd', 'id_perubahan', 'id_indikator', 'uraian_indikator_program_rpjmd', 'tolok_ukur_indikator', 
    'angka_awal_periode', 'angka_tahun1', 'angka_tahun2', 'angka_tahun3', 'angka_tahun4', 'angka_tahun5', 'angka_akhir_periode', 'created_at', 'updated_at','sumber_data'];
    protected $primaryKey = 'id_indikator_program_rpjmd';
    public $timestamps = true;

}
