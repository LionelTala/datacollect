<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'lioneltala93230@gmail.com'],
            [
                'name' => 'Lionel Tala',
                'password' => Hash::make('lionel2805'), // Change moi à la première connexion
                'is_admin' => true,
                'avatar' => null,
            ]
        );

        $this->command->info('✅ Compte admin créé : admin@datacollect.com / Admin123!');
    }
}
