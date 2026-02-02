<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BuscarTicketsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asunto' => ['sometimes', 'string', 'max:255'],
            'estado_id' => ['sometimes', 'nullable', 'integer', 'exists:estados_ticket,id'],
            'sistema_id' => ['sometimes', 'nullable', 'integer', 'exists:sistemas,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:200'],
        ];
    }
}

