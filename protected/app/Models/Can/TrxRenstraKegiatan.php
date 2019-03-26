<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraKegiatan extends Model
{
    public $timestamps = true;
    protected $table = 'trx_renstra_kegiatan';
    protected $primaryKey = 'id_kegiatan_renstra';
    protected $fillable = ['thn_id','no_urut','id_program_renstra','id_kegiatan_ref','id_perubahan','uraian_kegiatan_renstra','pagu_tahun1',
        'pagu_tahun2','pagu_tahun3','pagu_tahun4','pagu_tahun5','total_pagu','sumber_data','created_at','updated_at','uraian_sasaran_kegiatan'];
    
    public function TrxRenstraProgram()
    {
        return $this->belongsTo('App\Models\Can\TrxRenstraProgram','id_program_renstra');
    }
    public function TrxRenstraKegiatanIndikators()
    {
        return $this->hasMany('App\Models\Can\TrxRenstraKegiatanIndikator','id_kegiatan_renstra');
    }
    public function TrxRenstraKegiatanPelaksanas()
    {
        return $this->hasMany('App\Models\Can\TrxRenstraKegiatanPelaksana','id_kegiatan_renstra');
    }
    public function RefKegiatan()
    {
        return $this->belongsTo('App\Models\RefKegiatan','id_kegiatan_ref','id_kegiatan');
    }
    
}
