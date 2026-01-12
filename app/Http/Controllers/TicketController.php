<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreTicketRequest;
use App\Models\Sistema;
use App\Models\Ticket;
use App\Services\TicketService;
use App\Services\TicketVisibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketVisibilityService $ticketVisibility,
        private readonly TicketService $ticketService,
    ) {
    }

    public function index(Request $request): Response
    {
        $tickets = $this->ticketVisibility
            ->visibleTicketsQuery($request->user())
            ->with(['estado:id,nombre', 'sistema:id,nombre'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'asunto' => $ticket->asunto,
                'estado' => $ticket->estado?->nombre,
                'sistema' => $ticket->sistema?->nombre,
                'updated_at' => optional($ticket->updated_at)->toISOString(),
            ]);

        $sistemas = Sistema::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'sistemas' => $sistemas,
            'pollInterval' => 60000,
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = $this->ticketService->createTicket($request->user(), $request->validated());

        return redirect()->route('tickets.show', $ticket);
    }

    public function show(Ticket $ticket): Response
    {
        $this->authorize('view', $ticket);

        $ticket->load(['estado:id,nombre', 'sistema:id,nombre']);

        return Inertia::render('Tickets/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'asunto' => $ticket->asunto,
                'descripcion' => $ticket->descripcion,
                'estado' => $ticket->estado?->nombre,
                'sistema' => $ticket->sistema?->nombre,
                'created_at' => optional($ticket->created_at)->toISOString(),
                'updated_at' => optional($ticket->updated_at)->toISOString(),
            ],
            'pollInterval' => 60000,
        ]);
    }
}
