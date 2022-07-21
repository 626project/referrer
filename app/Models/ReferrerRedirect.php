<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferrerRedirect extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'ip',
        'referrer_link_id',
    ];
}
