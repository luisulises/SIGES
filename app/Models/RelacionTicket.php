<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelacionTicket extends Model
{
    use HasFactory;

    protected $table = 'relaciones_ticket';

    protected $fillable = [
        'ticket_id',
        'ticket_relacionado_id',
        'tipo_relacion',
        'creado_por_id',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function ticketRelacionado(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_relacionado_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por_id');
    }
}

