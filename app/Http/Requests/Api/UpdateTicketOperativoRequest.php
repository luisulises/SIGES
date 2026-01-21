<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketOperativoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responsable_id' => ['sometimes', 'nullable', 'integer', 'exists:usuarios,id'],
            'prioridad_id' => ['sometimes', 'nullable', 'integer', 'exists:prioridades,id'],
            'tipo_solicitud_id' => ['sometimes', 'nullable', 'integer', 'exists:tipos_solicitud,id'],
            'fecha_compromiso' => ['sometimes', 'nullable', 'date'],
            'fecha_entrega' => ['sometimes', 'nullable', 'date'],
            'resolucion' => ['sometimes', 'nullable', 'string'],
            'sistema_id' => ['sometimes', 'integer', 'exists:sistemas,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'responsable_id.integer' => 'El responsable seleccionado no es valido.',
        ];
    }
}
