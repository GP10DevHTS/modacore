<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@modacore.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'job_title' => 'System Administrator',
                'is_active' => true,
            ]
        );

        if (! $user->hasRole('superadmin')) {
            $user->assignRole('superadmin');
        }
    }
}
