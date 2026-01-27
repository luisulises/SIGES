<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreComentarioTicketRequest;
use App\Http\Resources\ComentarioTicketResource;
use App\Models\Ticket;
use App\Services\TicketComentarioService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketComentarioController extends Controller
{
    public function __construct(private readonly TicketComentarioService $comentarioService)
    {
    }

    public function index(Request $request, Ticket $ticket): AnonymousResourceCollection
    {
        $comentarios = $this->comentarioService->list($request->user(), $ticket);

        return ComentarioTicketResource::collection($comentarios);
    }

    public function store(StoreComentarioTicketRequest $request, Ticket $ticket): ComentarioTicketResource
    {
        $comentario = $this->comentarioService->create(
            $request->user(),
            $ticket,
            $request->validated()
        );

        return new ComentarioTicketResource($comentario);
    }
}

