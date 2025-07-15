<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $totalItems = Item::count();
        $inStockItems = Item::where('stock', '>', 0)->count();
        $lowStockItems = Item::whereColumn('stock', '<', 'min_stock')->get(); 
        $outOfStockItems = Item::where('stock', '<=', 0)->count();

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count(); 
        $totalRoles = Role::count();

        return response()->json([
            'success' => true,
            'data' => [
                'totalItems'     => $totalItems,
                'inStockItems'   => $inStockItems,
                'lowStockItems'  => $lowStockItems, 
                'outOfStockItems'=> $outOfStockItems,

                'totalUsers'     => $totalUsers,
                'activeUsers'    => $activeUsers,
                'totalRoles'     => $totalRoles,
            ]
        ]);
    }
}
