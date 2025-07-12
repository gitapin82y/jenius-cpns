<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class NonCpnsUserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'Karina', 'email' => 'karinaptrii@gmail.com'],
            ['name' => 'Kiranti', 'email' => 'kirantivierra@gmail.com'],
            ['name' => 'Layli nuril', 'email' => 'lailynuril02@gmail.com'],
            ['name' => 'Dwi Salsabila', 'email' => 'dwisapsabila@gmail.com'],
            ['name' => 'Tanto wijaya', 'email' => 'tantowijaya@gmail.com'],
            ['name' => 'Iskandar Hamid', 'email' => 'iskandarhamid@gmail.com'],
            ['name' => 'Diah Astuti', 'email' => 'diahastuti@gmail.com'],
            ['name' => 'Romeo David', 'email' => 'romeodavid@gmail.com'],
            ['name' => 'Sandy Arhan', 'email' => 'sandyarhan@gmail.com'],
            ['name' => 'Bella Audina', 'email' => 'bellaaudina@gmail..com'],
            ['name' => 'Vincent', 'email' => 'vincent@gmail.com'],
            ['name' => 'Karina Louis', 'email' => 'karinalouis@gmail.com'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'is_cpns' => false,
                'password' => Hash::make('asd'),
                'status' => 'active',
                'is_review' => 0,
            ]);
        }
    }
}
