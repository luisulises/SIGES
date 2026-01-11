<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'asunto',
        'descripcion',
        'solicitante_id',
        'sistema_id',
        'estado_id',
        'responsable_actual_id',
        'interno',
    ];

    protected $casts = [
        'interno' => 'boolean',
    ];

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function responsableActual(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_actual_id');
    }

    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class, 'sistema_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoTicket::class, 'estado_id');
    }
}
