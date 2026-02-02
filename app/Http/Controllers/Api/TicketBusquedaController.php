<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BuscarTicketsRequest;
use App\Http\Resources\TicketResource;
use App\Services\TicketVisibilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class TicketBusquedaController extends Controller
{
    public function __construct(private readonly TicketVisibilityService $visibilityService)
    {
    }

    public function index(BuscarTicketsRequest $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $perPage = (int) ($request->validated()['per_page'] ?? 50);

        $query = $this->visibilityService
            ->visibleTicketsQuery($user)
            ->with(['estado:id,nombre', 'sistema:id,nombre'])
            ->orderByDesc('updated_at');

        $asunto = trim((string) ($request->validated()['asunto'] ?? ''));
        if ($asunto !== '') {
            $query->where('asunto', 'like', "%{$asunto}%");
        }

        if (array_key_exists('estado_id', $request->validated()) && $request->validated()['estado_id']) {
            $query->where('estado_id', (int) $request->validated()['estado_id']);
        }

        if (array_key_exists('sistema_id', $request->validated()) && $request->validated()['sistema_id']) {
            $query->where('sistema_id', (int) $request->validated()['sistema_id']);
        }

        return TicketResource::collection($query->paginate($perPage));
    }

    public function metrics(Request $request): JsonResponse
    {
        $user = $request->user();
        $baseQuery = $this->visibilityService->visibleTicketsQuery($user);

        $total = (clone $baseQuery)->count();

        $porEstado = (clone $baseQuery)
            ->join('estados_ticket', 'estados_ticket.id', '=', 'tickets.estado_id')
            ->select(
                'tickets.estado_id',
                'estados_ticket.nombre as estado',
                DB::raw('count(*) as total')
            )
            ->groupBy('tickets.estado_id', 'estados_ticket.nombre')
            ->orderBy('estados_ticket.nombre')
            ->get();

        $porPrioridad = (clone $baseQuery)
            ->leftJoin('prioridades', 'prioridades.id', '=', 'tickets.prioridad_id')
            ->select(
                'tickets.prioridad_id',
                'prioridades.nombre as prioridad',
                'prioridades.orden as orden',
                DB::raw('count(*) as total')
            )
            ->groupBy('tickets.prioridad_id', 'prioridades.nombre', 'prioridades.orden')
            ->orderByRaw('orden is null, orden asc')
            ->get()
            ->map(function ($row) {
                if ($row->prioridad_id === null) {
                    $row->prioridad = 'Sin prioridad';
                }

                unset($row->orden);

                return $row;
            })
            ->values();

        return response()->json([
            'data' => [
                'total' => $total,
                'por_estado' => $porEstado,
                'por_prioridad' => $porPrioridad,
            ],
        ]);
    }
}
