<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLevelSakip extends Model
{
    protected $table = 'user_level_sakip';
    protected $primaryKey = 'id_user_level';

    public $timestamps = false;

    public $fillable = ['id_user_level',
    	'user_id',
    	'id_sotk_level_1',
        'id_sotk_level_2',
        'id_sotk_level_3'
    ];
}
