<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRelacionTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_relacionado_id' => ['required', 'integer', 'exists:tickets,id'],
            'tipo_relacion' => ['required', 'string', Rule::in(['relacionado', 'duplicado_de', 'reabre'])],
        ];
    }
}

