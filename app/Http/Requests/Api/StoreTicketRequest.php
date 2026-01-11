<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

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
            'sistema_id' => ['required', 'integer', 'exists:sistemas,id'],
        ];
    }
}
