<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxForumSkpdDokumen extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_forum_skpd_dokumen';
    protected $primaryKey = 'id_dokumen_ranwal';
    protected $fillable = ['id_dokumen_ranwal', 'id_unit_renja', 'nomor_ranwal', 'tanggal_ranwal', 'tahun_ranwal',
     'uraian_perkada', 'jabatan_tandatangan', 'nama_tandatangan', 'nip_tandatangan', 'flag'];
    


}