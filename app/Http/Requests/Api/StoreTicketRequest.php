<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asunto' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'sistema_id' => [
                'required',
                'integer',
                Rule::exists('sistemas', 'id')->where('activo', true),
            ],
            'referencia_ticket_id' => ['sometimes', 'nullable', 'integer', 'exists:tickets,id'],
        ];
    }
}
