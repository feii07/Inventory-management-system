<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Permission::all()
        ]);
    }
}
