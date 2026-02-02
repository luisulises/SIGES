<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreTipoSolicitudCatalogoRequest;
use App\Http\Requests\Api\Admin\UpdateTipoSolicitudCatalogoRequest;
use App\Models\TipoSolicitud;
use Illuminate\Http\JsonResponse;

class AdminTipoSolicitudCatalogoController extends Controller
{
    public function index(): JsonResponse
    {
        $items = TipoSolicitud::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'activo']);

        return response()->json(['data' => $items]);
    }

    public function store(StoreTipoSolicitudCatalogoRequest $request): JsonResponse
    {
        $tipo = TipoSolicitud::create([
            'nombre' => $request->validated()['nombre'],
            'activo' => $request->boolean('activo', true),
        ]);

        return response()->json([
            'data' => $tipo->only(['id', 'nombre', 'activo']),
        ], 201);
    }

    public function update(UpdateTipoSolicitudCatalogoRequest $request, TipoSolicitud $tipoSolicitud): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('nombre', $data)) {
            $tipoSolicitud->nombre = $data['nombre'];
        }

        if (array_key_exists('activo', $data)) {
            $tipoSolicitud->activo = (bool) $data['activo'];
        }

        $tipoSolicitud->save();

        return response()->json([
            'data' => $tipoSolicitud->fresh()->only(['id', 'nombre', 'activo']),
        ]);
    }
}

