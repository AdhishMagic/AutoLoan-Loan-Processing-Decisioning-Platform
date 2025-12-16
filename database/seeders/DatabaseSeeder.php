<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            AdminUserSeeder::class,
        ]);

        // Optionally create a demo user
        if (! User::where('email', 'test@example.com')->exists()) {
            $roleId = (int) DB::table('roles')->where('name', 'user')->value('id');
            if (! $roleId) {
                $roleId = (int) DB::table('roles')->insertGetId([
                    'name' => 'user',
                    'description' => 'Loan applicant',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role_id' => $roleId,
                'status' => 'active',
            ]);
        }
    }
}
