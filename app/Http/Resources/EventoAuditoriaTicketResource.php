<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoAuditoriaTicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'actor' => $this->whenLoaded('actor', fn () => [
                'id' => $this->actor?->id,
                'nombre' => $this->actor?->nombre,
            ]),
            'tipo_evento' => $this->tipo_evento,
            'valor_antes' => $this->valor_antes,
            'valor_despues' => $this->valor_despues,
            'metadatos' => $this->metadatos,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

