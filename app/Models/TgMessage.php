<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TgMessage extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'action',
        'tg_message_id',
        'tg_user_id',
        'group_id',
    ];
}
