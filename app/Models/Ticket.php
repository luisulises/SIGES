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
        'prioridad_id',
        'tipo_solicitud_id',
        'fecha_compromiso',
        'fecha_entrega',
        'resolucion',
        'cerrado_at',
        'cancelado_at',
    ];

    protected $casts = [
        'interno' => 'boolean',
        'fecha_compromiso' => 'datetime',
        'fecha_entrega' => 'datetime',
        'cerrado_at' => 'datetime',
        'cancelado_at' => 'datetime',
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
