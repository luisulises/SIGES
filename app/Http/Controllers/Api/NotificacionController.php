<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificacionResource;
use App\Models\Notificacion;
use App\Services\TicketVisibilityService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class NotificacionController extends Controller
{
    public function __construct(private readonly TicketVisibilityService $visibilityService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $visibleTicketsQuery = $this->visibilityService
            ->visibleTicketsQuery($user)
            ->select('tickets.id');

        $unreadCount = Notificacion::query()
            ->where('usuario_id', $user->id)
            ->where('canal', 'in_app')
            ->whereNull('leido_at')
            ->whereIn('ticket_id', $visibleTicketsQuery)
            ->count();

        $notifications = Notificacion::query()
            ->where('usuario_id', $user->id)
            ->where('canal', 'in_app')
            ->whereIn('ticket_id', $visibleTicketsQuery)
            ->with('ticket:id,asunto')
            ->orderByDesc('created_at')
            ->paginate(20);

        return NotificacionResource::collection($notifications)->additional([
            'meta' => [
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    public function markAsRead(Request $request, Notificacion $notificacion): NotificacionResource
    {
        $user = $request->user();

        if ((int) $notificacion->usuario_id !== (int) $user->id) {
            throw ValidationException::withMessages([
                'notificacion' => 'No autorizado.',
            ]);
        }

        if (! $notificacion->leido_at) {
            $notificacion->leido_at = now();
            $notificacion->save();
        }

        $canSeeTicket = $this->visibilityService
            ->visibleTicketsQuery($user)
            ->whereKey($notificacion->ticket_id)
            ->exists();

        if (! $canSeeTicket) {
            return new NotificacionResource($notificacion);
        }

        return new NotificacionResource($notificacion->load('ticket:id,asunto'));
    }
}
