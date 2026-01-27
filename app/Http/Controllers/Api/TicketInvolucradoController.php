<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreInvolucradoRequest;
use App\Http\Resources\InvolucradoTicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketInvolucradoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketInvolucradoController extends Controller
{
    public function __construct(private readonly TicketInvolucradoService $involucradoService)
    {
    }

    public function index(Request $request, Ticket $ticket): AnonymousResourceCollection
    {
        $involucrados = $this->involucradoService->list($request->user(), $ticket);

        return InvolucradoTicketResource::collection($involucrados);
    }

    public function store(StoreInvolucradoRequest $request, Ticket $ticket): InvolucradoTicketResource
    {
        $involucrado = $this->involucradoService->add(
            $request->user(),
            $ticket,
            (int) $request->validated()['usuario_id']
        );

        return new InvolucradoTicketResource($involucrado);
    }

    public function destroy(Request $request, Ticket $ticket, User $usuario): Response
    {
        $this->involucradoService->remove($request->user(), $ticket, $usuario->id);

        return response()->noContent();
    }
}

