<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdjuntoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var object|null $ticket */
        $ticket = $this->route('ticket');
        $ticketId = $ticket?->id;

        return [
            'archivo' => ['required', 'file', 'max:10240', 'mimes:pdf,png,jpg,jpeg,docx,xlsx,txt'],
            'comentario_id' => [
                'required',
                'integer',
                Rule::exists('comentarios_ticket', 'id')->where('ticket_id', $ticketId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'comentario_id.exists' => 'El comentario no pertenece a este ticket.',
        ];
    }
}
