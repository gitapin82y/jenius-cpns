<?php
// app/Models/Recommendation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $fillable = [
        'user_id',
        'set_soal_id',
        'soal_id',
        'material_id',
        'similarity_score'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setSoal()
    {
        return $this->belongsTo(SetSoal::class);
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}