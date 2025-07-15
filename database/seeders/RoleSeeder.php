<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'administrator',
            'description' => 'Full access to user management, no access to items'
        ]);

        Role::create([
            'name' => 'operational',
            'description' => 'Full CRUD access to items'
        ]);

        Role::create([
            'name' => 'sales',
            'description' => 'Read-only access to items'
        ]);
    }
}
