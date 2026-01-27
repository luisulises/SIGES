<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvolucradoTicket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'involucrados_ticket';

    protected $fillable = [
        'ticket_id',
        'usuario_id',
        'agregado_por_id',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function agregadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agregado_por_id');
    }
}

