<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRpjmdTujuan extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_rpjmd_tujuan';
    protected $primaryKey = 'id_tujuan_rpjmd';
    protected $fillable = ['thn_id_rpjmd','no_urut','id_tujuan_rpjmd','id_misi_rpjmd','id_perubahan','uraian_tujuan_rpjmd','sumber_data','created_at','updated_at'];

    public function trx_rpjmd_misi()
    {
      return $this->belongsTo('App\Models\TrxRpjmdMisi','id_misi_rpjmd');
    }

    public function TrxRpjmdSasarans()
    {
      return $this->hasMany('App\Models\TrxRpjmdSasaran','id_tujuan_rpjmd');
    }

}
