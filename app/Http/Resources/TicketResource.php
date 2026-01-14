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
            'prioridad_id' => $this->prioridad_id,
            'tipo_solicitud_id' => $this->tipo_solicitud_id,
            'fecha_compromiso' => optional($this->fecha_compromiso)->toISOString(),
            'fecha_entrega' => optional($this->fecha_entrega)->toISOString(),
            'resolucion' => $this->resolucion,
            'cerrado_at' => optional($this->cerrado_at)->toISOString(),
            'cancelado_at' => optional($this->cancelado_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
