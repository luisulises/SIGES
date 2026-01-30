<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRegistroTiempoTicketRequest;
use App\Http\Resources\RegistroTiempoTicketResource;
use App\Models\Ticket;
use App\Services\TicketTiempoService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketTiempoController extends Controller
{
    public function __construct(private readonly TicketTiempoService $tiempoService)
    {
    }

    public function index(Request $request, Ticket $ticket): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 50), 1), 200);
        $registros = $this->tiempoService->list($request->user(), $ticket, $perPage);

        return RegistroTiempoTicketResource::collection($registros);
    }

    public function store(StoreRegistroTiempoTicketRequest $request, Ticket $ticket): RegistroTiempoTicketResource
    {
        $registro = $this->tiempoService->create($request->user(), $ticket, $request->validated());

        return new RegistroTiempoTicketResource($registro);
    }
}
