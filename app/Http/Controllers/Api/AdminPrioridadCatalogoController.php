<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StorePrioridadCatalogoRequest;
use App\Http\Requests\Api\Admin\UpdatePrioridadCatalogoRequest;
use App\Models\Prioridad;
use Illuminate\Http\JsonResponse;

class AdminPrioridadCatalogoController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Prioridad::query()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'orden', 'activo']);

        return response()->json(['data' => $items]);
    }

    public function store(StorePrioridadCatalogoRequest $request): JsonResponse
    {
        $data = $request->validated();

        $prioridad = Prioridad::create([
            'nombre' => $data['nombre'],
            'orden' => $data['orden'] ?? 0,
            'activo' => array_key_exists('activo', $data) ? (bool) $data['activo'] : true,
        ]);

        return response()->json([
            'data' => $prioridad->only(['id', 'nombre', 'orden', 'activo']),
        ], 201);
    }

    public function update(UpdatePrioridadCatalogoRequest $request, Prioridad $prioridad): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('nombre', $data)) {
            $prioridad->nombre = $data['nombre'];
        }

        if (array_key_exists('orden', $data)) {
            $prioridad->orden = (int) $data['orden'];
        }

        if (array_key_exists('activo', $data)) {
            $prioridad->activo = (bool) $data['activo'];
        }

        $prioridad->save();

        return response()->json([
            'data' => $prioridad->fresh()->only(['id', 'nombre', 'orden', 'activo']),
        ]);
    }
}

