<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreTicketRequest;
use App\Models\EstadoTicket;
use App\Models\Role;
use App\Models\Sistema;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use App\Services\TicketVisibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $perPage = min(max($request->integer('per_page', 50), 1), 200);

        $tickets = $this->ticketVisibility
            ->visibleTicketsQuery($request->user())
            ->with(['estado:id,nombre', 'sistema:id,nombre'])
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->through(fn (Ticket $ticket) => [
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

    public function show(Request $request, Ticket $ticket): Response
    {
        $this->authorize('view', $ticket);

        $ticket->load(['estado:id,nombre', 'sistema:id,nombre', 'responsableActual:id,nombre']);

        $user = $request->user();
        $canOperate = $user ? $user->can('operate', $ticket) : false;
        $canCloseOrCancel = $user ? $user->can('closeOrCancel', $ticket) : false;
        $canAssign = $user ? $user->can('assign', $ticket) : false;

        $transiciones = collect();

        if ($user) {
            $transiciones = DB::table('reglas_transicion_estado as reglas')
                ->join('estados_ticket as estado', 'estado.id', '=', 'reglas.estado_destino_id')
                ->where('reglas.estado_origen_id', $ticket->estado_id)
                ->where('reglas.rol_id', $user->rol_id)
                ->orderBy('estado.nombre')
                ->get([
                    'estado.id',
                    'estado.nombre',
                    'reglas.requiere_responsable',
                ]);
        }

        $prioridades = DB::table('prioridades')
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id', 'nombre']);

        $tiposSolicitud = DB::table('tipos_solicitud')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $sistemas = Sistema::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $responsables = User::query()
            ->where('activo', true)
            ->whereHas('rol', fn ($query) => $query->where('nombre', Role::SOPORTE))
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $usuarios = collect();
        if ($user && $canAssign) {
            $usuarios = User::query()
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'email']);
        }

        $estados = EstadoTicket::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return Inertia::render('Tickets/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'asunto' => $ticket->asunto,
                'descripcion' => $ticket->descripcion,
                'solicitante_id' => $ticket->solicitante_id,
                'estado_id' => $ticket->estado_id,
                'estado' => $ticket->estado?->nombre,
                'sistema_id' => $ticket->sistema_id,
                'sistema' => $ticket->sistema?->nombre,
                'responsable_actual_id' => $ticket->responsable_actual_id,
                'responsable' => $ticket->responsableActual?->nombre,
                'interno' => (bool) $ticket->interno,
                'prioridad_id' => $ticket->prioridad_id,
                'tipo_solicitud_id' => $ticket->tipo_solicitud_id,
                'fecha_compromiso' => optional($ticket->fecha_compromiso)->toISOString(),
                'fecha_entrega' => optional($ticket->fecha_entrega)->toISOString(),
                'resolucion' => $ticket->resolucion,
                'cerrado_at' => optional($ticket->cerrado_at)->toISOString(),
                'cancelado_at' => optional($ticket->cancelado_at)->toISOString(),
                'created_at' => optional($ticket->created_at)->toISOString(),
                'updated_at' => optional($ticket->updated_at)->toISOString(),
            ],
            'catalogs' => [
                'estados' => $estados,
                'prioridades' => $prioridades,
                'tipos_solicitud' => $tiposSolicitud,
                'sistemas' => $sistemas,
                'responsables' => $responsables,
                'usuarios' => $usuarios,
            ],
            'transiciones' => $transiciones,
            'permissions' => [
                'role' => $user?->roleName(),
                'can_operate' => $canOperate,
                'can_close_cancel' => $canCloseOrCancel,
                'can_assign' => $canAssign,
            ],
            'pollInterval' => 60000,
        ]);
    }
}
