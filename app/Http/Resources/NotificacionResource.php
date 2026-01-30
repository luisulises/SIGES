<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificacionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'ticket' => $this->whenLoaded('ticket', fn () => [
                'id' => $this->ticket?->id,
                'asunto' => $this->ticket?->asunto,
            ]),
            'ticket_id' => $this->ticket_id,
            'tipo_evento' => $this->tipo_evento,
            'canal' => $this->canal,
            'leido_at' => optional($this->leido_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}

