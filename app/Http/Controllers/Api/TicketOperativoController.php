<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateTicketOperativoRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketOperativoService;

class TicketOperativoController extends Controller
{
    public function __construct(private readonly TicketOperativoService $operativoService)
    {
    }

    public function update(UpdateTicketOperativoRequest $request, Ticket $ticket): TicketResource
    {
        $ticket = $this->operativoService->update(
            $request->user(),
            $ticket,
            $request->validated()
        );

        $ticket->load(['estado:id,nombre', 'sistema:id,nombre']);

        return new TicketResource($ticket);
    }
}
