<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferrerLink extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'link',
        'caption',
        'count',
        'uniq_count',
    ];
}
