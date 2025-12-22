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
            'threshold',              // ✅ TAMBAHKAN
    'meets_threshold',        // ✅ TAMBAHKAN

        'is_relevant',
        'is_recommended',
        'classification',
        'user_feedback',              // ✅ BARU
        'user_evaluated_at',          // ✅ BARU
        'final_classification'        // ✅ BARU
    ];

    protected $casts = [
        'soal_keywords' => 'array',
        'material_keywords' => 'array',
        'intersection_keywords' => 'array',
        'is_relevant' => 'boolean',
        'is_recommended' => 'boolean',
         'meets_threshold' => 'boolean', 
           'user_feedback' => 'boolean',  // ✅ BARU
        'user_evaluated_at' => 'datetime', // ✅ BARU
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

      /**
     * ✅ Accessor: Get final classification (prioritas manual)
     */
    public function getFinalClassificationAttribute($value)
    {
        // Jika ada penilaian manual, pakai itu
        if ($this->user_feedback !== null) {
            return $this->user_feedback ? 'TP' : 'FP';
        }
        
        // Jika belum dinilai manual, pakai otomatis
        return $this->classification;
    }

        /**
     * ✅ Accessor: Cek apakah sudah dinilai user
     */
    public function getHasUserFeedbackAttribute()
    {
        return $this->user_feedback !== null;
    }
    
}