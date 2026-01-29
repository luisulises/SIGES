<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistroTiempoTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'minutos' => ['required', 'integer', 'min:1'],
            'nota' => ['nullable', 'string'],
        ];
    }
}

