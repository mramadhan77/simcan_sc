<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRpjmdDokumen extends Model
{
    public $timestamps = true;
    protected $table = 'trx_rpjmd_dokumen';
    protected $primaryKey = 'id_rpjmd';
    protected $fillable = ['id_pemda', 'id_rpjmd_old','id_rpjmd_ref', 'thn_dasar', 'tahun_1', 'tahun_2', 'tahun_3', 'tahun_4', 'tahun_5', 'no_perda', 'tgl_perda', 'keterangan_dokumen', 'jns_dokumen', 'id_revisi', 'id_status_dokumen', 'sumber_data'];

}
