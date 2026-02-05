<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdjuntoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'comentario_id' => $this->comentario_id,
            'cargado_por' => $this->whenLoaded('cargadoPor', fn () => [
                'id' => $this->cargadoPor?->id,
                'nombre' => $this->cargadoPor?->nombre,
            ]),
            'nombre_archivo' => $this->nombre_archivo,
            'download_url' => "/api/tickets/{$this->ticket_id}/adjuntos/{$this->id}/download",
            'visibilidad' => $this->visibilidad,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
