<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sistema extends Model
{
    use HasFactory;

    protected $table = 'sistemas';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function coordinadores(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sistemas_coordinadores', 'sistema_id', 'usuario_id')
            ->withTimestamps();
    }
}
