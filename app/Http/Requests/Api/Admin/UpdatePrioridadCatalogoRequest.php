<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\Prioridad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePrioridadCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Prioridad|null $prioridad */
        $prioridad = $this->route('prioridad');

        return [
            'nombre' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('prioridades', 'nombre')->ignore($prioridad?->id),
            ],
            'orden' => ['sometimes', 'integer', 'min:0'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}

