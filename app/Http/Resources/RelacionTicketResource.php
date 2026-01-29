<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelacionTicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'ticket_relacionado_id' => $this->ticket_relacionado_id,
            'tipo_relacion' => $this->tipo_relacion,
            'creado_por' => $this->whenLoaded('creadoPor', fn () => [
                'id' => $this->creadoPor?->id,
                'nombre' => $this->creadoPor?->nombre,
            ]),
            'ticket' => $this->whenLoaded('ticket', fn () => [
                'id' => $this->ticket?->id,
                'asunto' => $this->ticket?->asunto,
                'estado_id' => $this->ticket?->estado_id,
                'sistema_id' => $this->ticket?->sistema_id,
                'updated_at' => optional($this->ticket?->updated_at)->toISOString(),
            ]),
            'ticket_relacionado' => $this->whenLoaded('ticketRelacionado', fn () => [
                'id' => $this->ticketRelacionado?->id,
                'asunto' => $this->ticketRelacionado?->asunto,
                'estado_id' => $this->ticketRelacionado?->estado_id,
                'sistema_id' => $this->ticketRelacionado?->sistema_id,
                'updated_at' => optional($this->ticketRelacionado?->updated_at)->toISOString(),
            ]),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
