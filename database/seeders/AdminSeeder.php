<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('shop.admin_email');
        $password = config('shop.admin_password');

        if (! $email || ! $password) {
            $this->command?->error('Définissez ADMIN_EMAIL et ADMIN_PASSWORD dans le fichier .env.');

            return;
        }

        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => $user->name ?: 'Administrateur',
            'password' => Hash::make($password),
            'is_admin' => true,
        ])->save();

        $this->command?->info("Administrateur prêt : {$email}");
    }
}