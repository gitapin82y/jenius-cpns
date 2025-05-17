<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetSoal extends Model
{
     protected $guarded = [];


    public function soal()
    {
        return $this->hasMany(Soal::class);
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    public function hasilTryouts()
    {
        return $this->hasMany(HasilTryout::class);
    }

    public function jawabanUsers()
    {
        return $this->hasMany(JawabanUser::class);
    }
}
