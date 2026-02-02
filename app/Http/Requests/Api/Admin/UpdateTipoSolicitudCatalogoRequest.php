<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\TipoSolicitud;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoSolicitudCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var TipoSolicitud|null $tipoSolicitud */
        $tipoSolicitud = $this->route('tipoSolicitud');

        return [
            'nombre' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('tipos_solicitud', 'nombre')->ignore($tipoSolicitud?->id),
            ],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}

