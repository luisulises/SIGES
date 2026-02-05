<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComentarioTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cuerpo' => ['required', 'string', 'max:5000'],
            'visibilidad' => ['required', 'string', Rule::in(['publico', 'interno'])],
        ];
    }
}
