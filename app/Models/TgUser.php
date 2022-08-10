<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TgUser extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'tg_id',
        'username',
        'first_name',
        'last_name',
        'phone',
        'last_action',
    ];
}
