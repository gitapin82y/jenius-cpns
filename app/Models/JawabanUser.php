<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanUser extends Model
{
     protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    public function setSoal()
    {
        return $this->belongsTo(SetSoal::class, 'set_soal_id');
    }
}