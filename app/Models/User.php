<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function jawabanUsers()
    {
        return $this->hasMany(JawabanUser::class);
    }

    public function hasilTryouts()
    {
        return $this->hasMany(HasilTryout::class);
    }
    
    public function materialProgress()
    {
        return $this->hasMany(UserMaterialProgress::class);
    }
    
    /**
     * Check if user has completed a specific material
     */
    public function hasCompletedMaterial($materialId)
    {
        return $this->materialProgress()
            ->where('material_id', $materialId)
            ->where('is_completed', true)
            ->exists();
    }
    
    /**
     * Check if user has completed all materials in a category
     */
    public function hasCompletedCategory($category)
    {
        $totalMaterials = Material::where('kategori', $category)
            ->where('status', 'Publish')
            ->count();
            
        $completedMaterials = $this->materialProgress()
            ->whereHas('material', function($query) use ($category) {
                $query->where('kategori', $category)
                    ->where('status', 'Publish');
            })
            ->where('is_completed', true)
            ->count();
            
        return $totalMaterials > 0 && $completedMaterials >= $totalMaterials;
    }
    
    /**
     * Check if user has completed a specific latihan
     */
    public function hasCompletedLatihan($setSoalId)
    {
        return $this->hasilTryouts()
            ->where('set_soal_id', $setSoalId)
            ->exists();
    }
    
    /**
     * Check if user has completed all latihan in a category
     */
    public function hasCompletedCategoryLatihan($category)
    {
        $latihan = SetSoal::where('kategori', 'Latihan')
            ->whereHas('soal', function($query) use ($category) {
                $query->where('kategori', $category);
            })
            ->first();
            
        if (!$latihan) {
            return true; // No latihan exists, so consider it completed
        }
        
        return $this->hasCompletedLatihan($latihan->id);
    }
}