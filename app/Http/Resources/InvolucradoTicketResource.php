<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvolucradoTicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'usuario' => $this->whenLoaded('usuario', fn () => [
                'id' => $this->usuario?->id,
                'nombre' => $this->usuario?->nombre,
                'email' => $this->usuario?->email,
            ]),
            'created_at' => optional($this->created_at)->toISOString(),
            'deleted_at' => optional($this->deleted_at)->toISOString(),
        ];
    }
}

