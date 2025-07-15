<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'administrator')->first();
        $operationalRole = Role::where('name', 'operational')->first();
        $salesRole = Role::where('name', 'sales')->first();

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // Create operational user
        User::create([
            'name' => 'Operational User',
            'email' => 'operational@example.com',
            'password' => Hash::make('password'),
            'role_id' => $operationalRole->id,
        ]);

        // Create sales user
        User::create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
            'role_id' => $salesRole->id,
        ]);
    }
}
