<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $roles = [
            ['name' => 'admin', 'description' => 'System administrator', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'manager', 'description' => 'Loan approving officer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customer_service', 'description' => 'support for customer to apply loan', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'user', 'description' => 'Loan applicant', 'created_at' => $now, 'updated_at' => $now],
        ];

        // Upsert to avoid duplicate errors if re-seeded
        DB::table('roles')->upsert($roles, ['name'], ['description', 'updated_at']);
    }
}
