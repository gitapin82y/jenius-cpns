<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTryout extends Model
{
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

    public function posttests()
{
    return $this->hasMany(HasilTryout::class, 'pretest_id');
}

public function pretest()
{
    return $this->belongsTo(HasilTryout::class, 'pretest_id');
}

}