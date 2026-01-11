<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol_id',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'desactivado_at' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function sistemasCoordinados(): BelongsToMany
    {
        return $this->belongsToMany(Sistema::class, 'sistemas_coordinadores', 'usuario_id', 'sistema_id')
            ->withTimestamps();
    }

    public function roleName(): ?string
    {
        return $this->rol?->nombre;
    }

    public function isAdmin(): bool
    {
        return $this->roleName() === Role::ADMIN;
    }

    public function isCoordinador(): bool
    {
        return $this->roleName() === Role::COORDINADOR;
    }

    public function isSoporte(): bool
    {
        return $this->roleName() === Role::SOPORTE;
    }

    public function isClienteInterno(): bool
    {
        return $this->roleName() === Role::CLIENTE_INTERNO;
    }
}
