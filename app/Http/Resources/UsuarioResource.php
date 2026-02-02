<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'rol_id' => $this->rol_id,
            'rol' => $this->whenLoaded('rol', fn () => [
                'id' => $this->rol?->id,
                'nombre' => $this->rol?->nombre,
            ]),
            'activo' => (bool) $this->activo,
            'desactivado_at' => optional($this->desactivado_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

