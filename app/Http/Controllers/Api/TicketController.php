<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use App\Services\TicketVisibilityService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
        private readonly TicketVisibilityService $ticketVisibility,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 50), 1), 200);

        $tickets = $this->ticketVisibility
            ->visibleTicketsQuery($request->user())
            ->with(['estado:id,nombre', 'sistema:id,nombre'])
            ->orderByDesc('updated_at')
            ->paginate($perPage);

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request): TicketResource
    {
        $ticket = $this->ticketService->createTicket($request->user(), $request->validated());

        $ticket->load(['estado:id,nombre', 'sistema:id,nombre']);

        return new TicketResource($ticket);
    }

    public function show(Ticket $ticket): TicketResource
    {
        $this->authorize('view', $ticket);

        $ticket->load(['estado:id,nombre', 'sistema:id,nombre']);

        return new TicketResource($ticket);
    }
}
