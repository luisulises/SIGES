<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User|null $usuario */
        $usuario = $this->route('usuario');

        return [
            'nombre' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore($usuario?->id),
            ],
            'rol_id' => ['sometimes', 'integer', 'exists:roles,id'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}

