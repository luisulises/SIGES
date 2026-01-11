<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asunto' => $this->asunto,
            'descripcion' => $this->descripcion,
            'sistema_id' => $this->sistema_id,
            'estado_id' => $this->estado_id,
            'solicitante_id' => $this->solicitante_id,
            'responsable_actual_id' => $this->responsable_actual_id,
            'interno' => $this->interno,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
