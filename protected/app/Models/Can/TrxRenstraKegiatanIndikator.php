<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraKegiatanIndikator extends Model
{
    public $timestamps = true;
    protected $table = 'trx_renstra_kegiatan_indikator';
    protected $primaryKey = 'id_indikator_kegiatan_renstra';
    protected $fillable = ['thn_id','no_urut','id_kegiatan_renstra','kd_indikator','id_perubahan','uraian_indikator_kegiatan_renstra','tolok_ukur_indikator','angka_awal_periode','angka_tahun1',
        'angka_tahun2','angka_tahun3','angka_tahun4','angka_tahun5','angka_akhir_periode','created_at','updated_at','sumber_data'];
    
    public function trx_renstra_kegiatan()
    {
        return $this->belongsTo('App\Models\Can\TrxRenstraKegiatan','id_kegiatan_renstra');
    }
    
    
    public function ref_indikator()
    {
        return $this->belongsTo('App\Models\RefIndikator','kd_indikator','id_indikator');
    }
    
}
