<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreSistemaCatalogoRequest;
use App\Http\Requests\Api\Admin\UpdateSistemaCatalogoRequest;
use App\Models\Sistema;
use Illuminate\Http\JsonResponse;

class AdminSistemaCatalogoController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Sistema::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'activo']);

        return response()->json(['data' => $items]);
    }

    public function store(StoreSistemaCatalogoRequest $request): JsonResponse
    {
        $sistema = Sistema::create([
            'nombre' => $request->validated()['nombre'],
            'activo' => $request->boolean('activo', true),
        ]);

        return response()->json([
            'data' => $sistema->only(['id', 'nombre', 'activo']),
        ], 201);
    }

    public function update(UpdateSistemaCatalogoRequest $request, Sistema $sistema): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('nombre', $data)) {
            $sistema->nombre = $data['nombre'];
        }

        if (array_key_exists('activo', $data)) {
            $sistema->activo = (bool) $data['activo'];
        }

        $sistema->save();

        return response()->json([
            'data' => $sistema->fresh()->only(['id', 'nombre', 'activo']),
        ]);
    }
}

