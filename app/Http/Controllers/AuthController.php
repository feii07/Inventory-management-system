<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
     public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        // Seandainya nanti mau SPA
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->load('role'),
                'token' => $token
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // ambil semua menu induk beserta roles dan children.roles
        $menus = Menu::with(['roles', 'children.roles'])
            ->whereNull('parent_id')
            ->orderBy('id')
            ->get()
            ->filter(function ($menu) use ($user) {
                return $this->menuAllowedForRole($menu, $user);
            })
            ->map(function ($menu) use ($user) {
                // filter children juga
                $children = $menu->children->filter(function ($child) use ($user) {
                    return $this->menuAllowedForRole($child, $user);
                })->map(function ($child) {
                    return [
                        'name'  => $child->name,
                        'icon'  => $child->icon,
                        'route' => $child->route,
                    ];
                })->values();

                return [
                    'name'     => $menu->name,
                    'icon'     => $menu->icon,
                    'route'    => $menu->route,
                    'children' => $children->isNotEmpty() ? $children : null
                ];
            })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id'   => $user->id,
                    'name' => $user->name,
                    'role' => $user->role->name,
                ],
                'menu' => $menus
            ]
        ]);
    }

    private function menuAllowedForRole(Menu $menu, User $user): bool
    {
        // kalau menu ini tidak punya relasi role sama sekali, kamu bisa tentukan:
        // return false; (biar gak muncul)
        if ($menu->roles->isEmpty()) {
            return false;
        }

        // cek apakah salah satu role menu cocok dengan role user
        return $menu->roles->contains(function ($role) use ($user) {
            return strtolower($role->name) === strtolower($user->role->name);
        });
    }
}
