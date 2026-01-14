<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChangeTicketEstadoRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketWorkflowService;

class TicketWorkflowController extends Controller
{
    public function __construct(private readonly TicketWorkflowService $workflowService)
    {
    }

    public function changeState(ChangeTicketEstadoRequest $request, Ticket $ticket): TicketResource
    {
        $ticket = $this->workflowService->transition(
            $request->user(),
            $ticket,
            $request->validated()['estado']
        );

        return new TicketResource($ticket->fresh());
    }

    public function close(Ticket $ticket): TicketResource
    {
        $ticket = $this->workflowService->close(request()->user(), $ticket);

        return new TicketResource($ticket->fresh());
    }

    public function cancel(Ticket $ticket): TicketResource
    {
        $ticket = $this->workflowService->cancel(request()->user(), $ticket);

        return new TicketResource($ticket->fresh());
    }
}
