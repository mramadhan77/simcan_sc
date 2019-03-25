<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefKabupaten extends Model
{
    protected $table = 'ref_kabupaten';
    protected $primaryKey = 'id_kab';
    protected $fillable = ['id_pemda', 'id_prov', 'id_kab', 'kd_kab', 'nama_kab_kota'];

    public $timestamps = false;

    public function ref_pemda()
    {
      return $this->belongsTo('App\Models\RefPemda','id_pemda');
    }

}
