<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroTiempoTicket extends Model
{
    use HasFactory;

    protected $table = 'registros_tiempo_ticket';

    protected $fillable = [
        'ticket_id',
        'autor_id',
        'minutos',
        'nota',
    ];

    protected $casts = [
        'minutos' => 'integer',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }
}
