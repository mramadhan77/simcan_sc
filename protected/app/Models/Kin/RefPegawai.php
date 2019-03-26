<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class RefPegawai extends Model
{
    protected $table = 'ref_pegawai';
    protected $primaryKey = 'id_pegawai';
    protected $fillable = ['nama_pegawai', 'nip_pegawai', 'status_pegawai', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function RefPegawaiPangkats()
    {
      return $this->hasMany('App\Models\Kin\RefPegawaiPangkat','id_pegawai','id_pegawai');
    }

}
