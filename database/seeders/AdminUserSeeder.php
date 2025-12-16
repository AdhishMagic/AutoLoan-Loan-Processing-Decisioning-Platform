<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure core roles exist
        $roles = collect(['admin', 'manager', 'customer_service', 'user'])
            ->mapWithKeys(fn ($name) => [$name => Role::firstOrCreate(['name' => $name])]);

        // Create or update an admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin@123'),
                'status' => 'active',
                'role_id' => $roles['admin']->id,
            ]
        );

        // Optionally create a sample loan officer and user
        User::updateOrCreate(
            ['email' => 'officer@example.com'],
            [
                'name' => 'Loan Officer',
                'password' => Hash::make('Officer@123'),
                'status' => 'active',
                'role_id' => $roles['manager']->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Sample User',
                'password' => Hash::make('User@123'),
                'status' => 'active',
                'role_id' => $roles['user']->id,
            ]
        );
    }
}
