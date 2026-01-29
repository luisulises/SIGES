<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoAuditoriaTicket extends Model
{
    use HasFactory;

    protected $table = 'eventos_auditoria_ticket';

    protected $fillable = [
        'ticket_id',
        'actor_id',
        'tipo_evento',
        'valor_antes',
        'valor_despues',
        'metadatos',
    ];

    protected $casts = [
        'valor_antes' => 'array',
        'valor_despues' => 'array',
        'metadatos' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}

