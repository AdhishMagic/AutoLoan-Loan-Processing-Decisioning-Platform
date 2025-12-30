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
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin@123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role_id' => $roles['admin']->id,
            ]
        );

        // Create or update a manager user
        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('Manager@123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role_id' => $roles['manager']->id,
            ]
        );

        // Create or update a customer service user
        User::updateOrCreate(
            ['email' => 'support@example.com'],
            [
                'name' => 'Customer Support',
                'password' => Hash::make('Support@123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role_id' => $roles['customer_service']->id,
            ]
        );

        // Backwards-compatible sample officer account (same role as manager)
        User::updateOrCreate(
            ['email' => 'officer@example.com'],
            [
                'name' => 'Loan Officer',
                'password' => Hash::make('Officer@123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role_id' => $roles['manager']->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Sample User',
                'password' => Hash::make('User@123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role_id' => $roles['user']->id,
            ]
        );
    }
}
