<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePrioridadCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:prioridades,nombre'],
            'orden' => ['sometimes', 'integer', 'min:0'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}

