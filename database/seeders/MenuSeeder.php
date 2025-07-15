<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::truncate();
        DB::table('menu_role')->truncate();

        // buat menu
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'icon' => 'fas fa-home',
            'route' => 'dashboard',
            'parent_id' => null,
        ]);

        $items = Menu::create([
            'name' => 'Items',
            'icon' => 'fas fa-box',
            'route' => 'items',
            'parent_id' => null,
        ]);

        $userManagement = Menu::create([
            'name' => 'User Management',
            'icon' => 'fas fa-users',
            'route' => null,
            'parent_id' => null,
        ]);

        $users = Menu::create([
            'name' => 'Users',
            'icon' => 'fas fa-user',
            'route' => 'users',
            'parent_id' => $userManagement->id,
        ]);

        $roles = Menu::create([
            'name' => 'Roles',
            'icon' => 'fas fa-user-tag',
            'route' => 'roles',
            'parent_id' => $userManagement->id,
        ]);

        $admin = Role::find(1);
        $operational = Role::find(2);
        $sales = Role::find(3);

        $dashboard->roles()->attach([$admin->id, $operational->id, $sales->id]);
        $items->roles()->attach([$admin->id, $operational->id, $sales->id]);
        $userManagement->roles()->attach($admin->id);
        $users->roles()->attach($admin->id);
        $roles->roles()->attach($admin->id);
    }
}
