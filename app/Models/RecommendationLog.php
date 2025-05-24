<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationLog extends Model
{
    protected $guarded = [];
    protected $casts = [
        'recommendations' => 'array',
        'debug_info' => 'array'
    ];
}
