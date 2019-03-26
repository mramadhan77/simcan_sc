<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class RefPegawaiUnit extends Model
{
    protected $table = 'ref_pegawai_unit';
    protected $primaryKey = 'id_unit_pegawai';
    protected $fillable = [ 'id_pegawai', 'id_unit', 'tingkat_eselon', 'id_jabatan_eselon', 'nama_jabatan', 'tmt_unit', 'created_at', 'updated_at'];

    public $timestamps = true;


}
