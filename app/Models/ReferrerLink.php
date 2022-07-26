<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferrerLink extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'link',
        'label',
        'caption',
        'count',
        'uniq_count',
    ];
}
