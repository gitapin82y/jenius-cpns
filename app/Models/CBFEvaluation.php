<?php
// app/Models/CBFEvaluation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CBFEvaluation extends Model
{
    protected $table = 'cbf_evaluations'; // Explicit table name
    protected $guarded = [];
    
    protected $casts = [
        'is_recommended' => 'boolean',
        'user_feedback' => 'boolean',
        'expert_validation' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
    
    public function setSoal()
    {
        return $this->belongsTo(SetSoal::class, 'set_soal_id');
    }
    
    public function getFinalRelevanceAttribute()
    {
        return $this->expert_validation ?? $this->user_feedback;
    }
}