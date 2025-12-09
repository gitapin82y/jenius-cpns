<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomaticCBFEvaluation extends Model
{

    protected $table = 'automatic_cbf_evaluations'; 

    protected $fillable = [
        'user_id',
        'set_soal_id',
        'soal_id',
        'material_id',
        'soal_keywords',
        'material_keywords',
        'intersection_keywords',
        'intersection_count',
        'similarity_score',
        'is_relevant',
        'is_recommended',
        'classification'
    ];

    protected $casts = [
        'soal_keywords' => 'array',
        'material_keywords' => 'array',
        'intersection_keywords' => 'array',
        'is_relevant' => 'boolean',
        'is_recommended' => 'boolean',
    ];

    // Relationships
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