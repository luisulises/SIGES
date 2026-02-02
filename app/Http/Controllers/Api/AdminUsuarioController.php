<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreUsuarioRequest;
use App\Http\Requests\Api\Admin\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\User;
use App\Services\AdminUsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class AdminUsuarioController extends Controller
{
    public function __construct(private readonly AdminUsuarioService $usuarioService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 50), 1), 200);
        $q = trim((string) $request->query('q', ''));

        $query = User::query()
            ->with('rol:id,nombre')
            ->orderBy('nombre');

        if ($q !== '') {
            $query->where(function ($subquery) use ($q) {
                $subquery->where('nombre', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        return UsuarioResource::collection($query->paginate($perPage));
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $usuario = $this->usuarioService->create($request->validated());

        return (new UsuarioResource($usuario->load('rol:id,nombre')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateUsuarioRequest $request, User $usuario): UsuarioResource
    {
        $actor = $request->user();

        if ($actor && (int) $actor->id === (int) $usuario->id && $request->has('activo') && ! $request->boolean('activo')) {
            throw ValidationException::withMessages([
                'activo' => 'No puedes desactivarte a ti mismo.',
            ]);
        }

        $usuario = $this->usuarioService->update($usuario, $request->validated());

        return new UsuarioResource($usuario->fresh()->load('rol:id,nombre'));
    }
}
