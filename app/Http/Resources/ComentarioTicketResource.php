<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComentarioTicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'autor' => $this->whenLoaded('autor', fn () => [
                'id' => $this->autor?->id,
                'nombre' => $this->autor?->nombre,
            ]),
            'cuerpo' => $this->cuerpo,
            'visibilidad' => $this->visibilidad,
            'adjuntos' => AdjuntoResource::collection($this->whenLoaded('adjuntos')),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

