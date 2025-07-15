<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('manage_roles')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => Role::with('permissions','menus')->get()
        ]);
    }

    public function store(RoleStoreRequest $request)
    {
        $role = Role::create(array_merge(
            $request->only(['name', 'description']),
            ['created_by' => $request->user()->id] // pastikan kolom ini ada di tabel roles
        ));

        if ($request->has('menu_ids')) {
            $role->menus()->sync($request->input('menu_ids', []));
        }

        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->input('permission_ids', []));
        }
        
        return response()->json([
            'success' => true,
            'data' => $role->load(['menus', 'permissions'])
        ], 201);
    }

    public function show(Role $role)
    {
        return response()->json([
            'success' => true,
            'data' => $role->load(['menus', 'permissions'])
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $role->update($request->only(['name', 'description']));

        // sinkronisasi menu & permission
        if ($request->has('menu_ids')) {
            $role->menus()->sync($request->input('menu_ids', []));
        }

        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->input('permission_ids', []));
        }

        return response()->json([
            'success' => true,
            'data' => $role->load(['menus', 'permissions'])
        ]);
    }
}
