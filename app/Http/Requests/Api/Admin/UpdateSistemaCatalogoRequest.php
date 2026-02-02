<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\Sistema;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSistemaCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Sistema|null $sistema */
        $sistema = $this->route('sistema');

        return [
            'nombre' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('sistemas', 'nombre')->ignore($sistema?->id),
            ],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}

