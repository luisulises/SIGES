<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUsuarioService
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): User
    {
        return User::query()->create([
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'rol_id' => (int) $data['rol_id'],
            'password' => Hash::make((string) $data['password']),
            'activo' => true,
            'desactivado_at' => null,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(User $usuario, array $data): User
    {
        return DB::transaction(function () use ($usuario, $data) {
            $now = now();

            if (array_key_exists('nombre', $data)) {
                $usuario->nombre = $data['nombre'];
            }

            if (array_key_exists('email', $data)) {
                $usuario->email = $data['email'];
            }

            if (array_key_exists('rol_id', $data)) {
                $usuario->rol_id = (int) $data['rol_id'];
            }

            if (array_key_exists('password', $data) && $data['password']) {
                $usuario->password = Hash::make((string) $data['password']);
            }

            if (array_key_exists('activo', $data)) {
                $wantsActive = (bool) $data['activo'];

                if ($wantsActive) {
                    $usuario->activo = true;
                    $usuario->desactivado_at = null;
                } else {
                    $usuario->activo = false;
                    $usuario->desactivado_at = $usuario->desactivado_at ?? $now;

                    $this->cleanupResponsableAssignments($usuario->id, $now);
                    $this->revokeTokens($usuario->id);
                }
            }

            $usuario->save();

            return $usuario;
        });
    }

    private function cleanupResponsableAssignments(int $usuarioId, $now): void
    {
        Ticket::query()
            ->where('responsable_actual_id', $usuarioId)
            ->update([
                'responsable_actual_id' => null,
                'updated_at' => $now,
            ]);

        DB::table('asignaciones_ticket')
            ->where('responsable_id', $usuarioId)
            ->whereNull('desasignado_at')
            ->update([
                'desasignado_at' => $now,
                'updated_at' => $now,
            ]);
    }

    private function revokeTokens(int $usuarioId): void
    {
        DB::table('personal_access_tokens')
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', $usuarioId)
            ->delete();
    }
}

