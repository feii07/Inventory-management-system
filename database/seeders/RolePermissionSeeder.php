<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage_users', 'description' => 'Buat, edit, hapus user'],
            ['name' => 'manage_roles', 'description' => 'Buat, edit, hapus role'],
            ['name' => 'create_item', 'description' => 'Buat data item'],
            ['name' => 'read_item', 'description' => 'Lihat data item'],
            ['name' => 'update_item', 'description' => 'Edit data item'],
            ['name' => 'delete_item', 'description' => 'Hapus data item'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        // Ambil semua permissions
        $manageUsers = Permission::where('name', 'manage_users')->first();
        $manageRoles = Permission::where('name', 'manage_roles')->first();
        $createItem = Permission::where('name', 'create_item')->first();
        $readItem = Permission::where('name', 'read_item')->first();
        $updateItem = Permission::where('name', 'update_item')->first();
        $deleteItem = Permission::where('name', 'delete_item')->first();

        // Buat Roles
        $admin = Role::firstOrCreate(['name' => 'administrator'], ['description' => 'Full access to user and role management']);
        $operational = Role::firstOrCreate(['name' => 'operational'], ['description' => 'CRUD item management']);
        $sales = Role::firstOrCreate(['name' => 'sales'], ['description' => 'Read-only item access']);

        // Assign Permissions
        $admin->permissions()->sync([$manageUsers->id, $manageRoles->id]); // admin hanya urus user dan role
        $operational->permissions()->sync([$createItem->id, $readItem->id, $updateItem->id, $deleteItem->id]);
        $sales->permissions()->sync([$readItem->id]);
    }
}
