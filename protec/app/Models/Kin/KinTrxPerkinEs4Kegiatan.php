<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxPerkinEs4Kegiatan extends Model
{
    protected $table = 'kin_trx_perkin_es4_kegiatan';
    protected $primaryKey = 'id_perkin_kegiatan';
    protected $fillable = [ 'id_dokumen_perkin', 'id_perkin_kegiatan_es3', 'id_kegiatan_renstra', 
    'pagu_tahun', 'pagu_t1', 'pagu_t2', 'pagu_t3', 'pagu_t4', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function KinTrxPerkinEs3Program()
    {
      return $this->belongsTo('App\Models\Kin\KinTrxPerkinEs3Program','id_perkin_program','id_perkin_program');
    }

}
