<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTicket extends Model
{
    use HasFactory;

    public const NUEVO = 'Nuevo';
    public const EN_ANALISIS = 'En analisis';
    public const ASIGNADO = 'Asignado';
    public const EN_PROGRESO = 'En progreso';
    public const RESUELTO = 'Resuelto';
    public const CERRADO = 'Cerrado';
    public const CANCELADO = 'Cancelado';

    protected $table = 'estados_ticket';

    protected $fillable = [
        'nombre',
        'es_terminal',
    ];

    protected $casts = [
        'es_terminal' => 'boolean',
    ];
}
