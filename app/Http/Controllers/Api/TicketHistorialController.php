<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventoAuditoriaTicketResource;
use App\Models\Ticket;
use App\Services\TicketHistorialService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketHistorialController extends Controller
{
    public function __construct(private readonly TicketHistorialService $historialService)
    {
    }

    public function index(Request $request, Ticket $ticket): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 50), 1), 200);
        $eventos = $this->historialService->list($request->user(), $ticket, $perPage);

        return EventoAuditoriaTicketResource::collection($eventos);
    }
}
