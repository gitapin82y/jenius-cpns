<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTryout extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setSoal()
    {
        return $this->belongsTo(SetSoal::class, 'set_soal_id');
    }
}