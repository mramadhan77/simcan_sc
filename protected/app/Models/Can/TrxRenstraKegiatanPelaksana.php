<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraKegiatanPelaksana extends Model
{
    public $timestamps = true;
    protected $table = 'trx_renstra_kegiatan_pelaksana';
    protected $primaryKey = 'id_kegiatan_renstra_pelaksana';
    protected $fillable = ['thn_id','no_urut','id_kegiatan_renstra','id_perubahan','id_sub_unit','sumber_data','created_at','updated_at'];
    
    public function trx_renstra_kegiatan()
    {
        return $this->belongsTo('App\Models\Can\TrxRenstraKegiatan','id_kegiatan_renstra');
    }
    
    
    public function ref_sub_unit()
    {
        return $this->belongsTo('App\Models\RefSubUnit','id_sub_unit','id_sub_unit');
    }
    
}
