<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMaterialProgress extends Model
{
    protected $table = 'user_material_progress';
    protected $guarded = [];
    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}