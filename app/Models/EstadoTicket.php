<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTicket extends Model
{
    use HasFactory;

    public const NUEVO = 'Nuevo';

    protected $table = 'estados_ticket';

    protected $fillable = [
        'nombre',
        'es_terminal',
    ];

    protected $casts = [
        'es_terminal' => 'boolean',
    ];
}
