<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class AdminRoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json([
            'data' => $roles,
        ]);
    }
}

