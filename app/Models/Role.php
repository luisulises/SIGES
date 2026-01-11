<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public const CLIENTE_INTERNO = 'cliente_interno';
    public const SOPORTE = 'soporte';
    public const COORDINADOR = 'coordinador';
    public const ADMIN = 'admin';

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
    ];
}
