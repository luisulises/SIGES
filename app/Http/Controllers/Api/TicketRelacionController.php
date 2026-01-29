<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRelacionTicketRequest;
use App\Http\Resources\RelacionTicketResource;
use App\Models\Ticket;
use App\Services\TicketRelacionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketRelacionController extends Controller
{
    public function __construct(private readonly TicketRelacionService $relacionService)
    {
    }

    public function index(Request $request, Ticket $ticket): AnonymousResourceCollection
    {
        $relaciones = $this->relacionService->list($request->user(), $ticket);

        return RelacionTicketResource::collection($relaciones);
    }

    public function store(StoreRelacionTicketRequest $request, Ticket $ticket): RelacionTicketResource
    {
        $relacion = $this->relacionService->create($request->user(), $ticket, $request->validated());

        return new RelacionTicketResource($relacion);
    }
}

