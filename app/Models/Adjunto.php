<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adjunto extends Model
{
    use HasFactory;

    protected $table = 'adjuntos';

    protected $fillable = [
        'ticket_id',
        'comentario_id',
        'cargado_por_id',
        'nombre_archivo',
        'clave_almacenamiento',
        'visibilidad',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function comentario(): BelongsTo
    {
        return $this->belongsTo(ComentarioTicket::class, 'comentario_id');
    }

    public function cargadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cargado_por_id');
    }
}

