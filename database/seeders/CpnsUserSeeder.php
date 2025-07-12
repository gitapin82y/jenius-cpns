<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CpnsUserSeeder extends Seeder
{
     public function run()
    {
        $users = [
            ['name' => 'sarinah wulandari', 'email' => 'sarinahwulandari@gmail.com'],
            ['name' => 'Rukmini Ayu Sasmita', 'email' => 'rukminiayusasmita@gmail.com'],
            ['name' => 'lestari handayani', 'email' => 'lestarihandayani@gmail.com'],
            ['name' => 'Nayara Elvani Putri', 'email' => 'nayaraelvaniputri@gmail.com'],
            ['name' => 'alvhea kinanti', 'email' => 'alvheakinanti@gmail.com'],
            ['name' => 'Naylee Atharaya', 'email' => 'nayleeatharaya@gmail.com'],
            ['name' => 'Syafa Aurelian', 'email' => 'syafaaurelian@gmail.com'],
            ['name' => 'putri melati', 'email' => 'melatiputri2025@gmail.com'],
            ['name' => 'Rendra Purnama', 'email' => 'rendrapurnama246@gmail.com'],
            ['name' => 'fauzan Rizki', 'email' => 'fauzan.rizki88@gmail.com'],
            ['name' => 'Nadia Permata', 'email' => 'nadiapermata.id@gmail.com'],
            ['name' => 'Danu Iskandara', 'email' => 'danuiskandara1997@gmail.com'],
            ['name' => 'Reza Alfian', 'email' => 'rezaalfian777@gmail.com'],
            ['name' => 'Salsabila', 'email' => 'salsabila.writes@gmail.com'],
            ['name' => 'Dimas Sahputra', 'email' => 'dimas.techzone@gmail.com'],
            ['name' => 'Dewi murni', 'email' => 'dewi.murni25@gmail.com'],
            ['name' => 'Alissa Srikandi', 'email' => 'alissasrikandi@gmail.com'],
            ['name' => 'hakim Raihan', 'email' => 'hakimraihan00@gmail.com'],
            ['name' => 'Lutfiah', 'email' => 'lutfiahcreative@gmail.com'],
            ['name' => 'Anandita Sakira', 'email' => 'ananditasakira@gmail.com'],
            ['name' => 'Arika Sintari', 'email' => 'arikasintari@gmail.com'],
            ['name' => 'Abila Asikara', 'email' => 'abilaasikara@gmail.com'],
            ['name' => 'Asila Abila', 'email' => 'asileabile@gmail.com'],
            ['name' => 'Asla karina', 'email' => 'aslavsort@gmail.com'],
            ['name' => 'Cinta Harsumi', 'email' => 'cintaharsumi@gmail.com'],
            ['name' => 'Della Karunia', 'email' => 'dellakarunia6@gmail.com'],
            ['name' => 'Devi Nursari', 'email' => 'devinursari900@gmail.com'],
            ['name' => 'Ellina Rantika', 'email' => 'ellinarantika@gmail.com'],
            ['name' => 'Intan Nuraini', 'email' => 'intannnaaa@gmail.com'],
            ['name' => 'Eka Anggraini', 'email' => 'ekaanggraini@gmail.com'],
            ['name' => 'Dinda Herlina', 'email' => 'dindherlina@gmail.com'],
            ['name' => 'Andini Ayu', 'email' => 'andiniayyy@gmail.com'],
            ['name' => 'Aura Cinta', 'email' => 'auraacinnta@gmail.com'],
            ['name' => 'Aniela Amanda', 'email' => 'veyraamnd@gmail.com'],
            ['name' => 'Anindya Amelia', 'email' => 'anindyamelia@gmail.com'],
            ['name' => 'Sri Fitriana', 'email' => 'srifitriana@gmail.com'],
            ['name' => 'Inayah Khairunnisa', 'email' => 'inayahsyafitr@gmail.com'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'is_cpns' => true,
                'password' => Hash::make('asd'),
                'status' => 'active',
                'is_review' => 0,
            ]);
        }
    }
}
