<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    
    protected $guarded = [];

     // Relationships
     public function setSoal()
     {
         return $this->belongsTo(SetSoal::class, 'set_soal_id');
     }
 
     public function jawabanUsers()
     {
         return $this->hasMany(JawabanUser::class);
     }
}
