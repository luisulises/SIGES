<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAdjuntoRequest;
use App\Http\Resources\AdjuntoResource;
use App\Models\Ticket;
use App\Services\TicketAdjuntoService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketAdjuntoController extends Controller
{
    public function __construct(private readonly TicketAdjuntoService $adjuntoService)
    {
    }

    public function index(Request $request, Ticket $ticket): AnonymousResourceCollection
    {
        $adjuntos = $this->adjuntoService->list($request->user(), $ticket);

        return AdjuntoResource::collection($adjuntos);
    }

    public function store(StoreAdjuntoRequest $request, Ticket $ticket): AdjuntoResource
    {
        $adjunto = $this->adjuntoService->store(
            $request->user(),
            $ticket,
            $request->file('archivo'),
            (int) $request->validated()['comentario_id']
        );

        return new AdjuntoResource($adjunto);
    }
}
