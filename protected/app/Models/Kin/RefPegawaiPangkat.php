<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class RefPegawaiPangkat extends Model
{
    protected $table = 'ref_pegawai_pangkat';
    protected $primaryKey = 'id_pangkat';
    protected $fillable = ['id_pegawai', 'pangkat_pegawai', 'tmt_pangkat', 'created_at', 'updated_at'];

    public $timestamps = true;


}
