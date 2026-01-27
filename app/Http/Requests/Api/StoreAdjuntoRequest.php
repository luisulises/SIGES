<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdjuntoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'archivo' => ['required', 'file', 'max:10240', 'mimes:pdf,png,jpg,jpeg,docx,xlsx,txt'],
            'comentario_id' => ['required', 'integer', 'exists:comentarios_ticket,id'],
        ];
    }
}
