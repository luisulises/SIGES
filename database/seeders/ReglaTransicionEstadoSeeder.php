<?php

namespace Database\Seeders;

use App\Models\EstadoTicket;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReglaTransicionEstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesOperativos = [
            Role::SOPORTE,
            Role::COORDINADOR,
            Role::ADMIN,
        ];

        $transicionesOperativas = [
            [EstadoTicket::NUEVO, EstadoTicket::EN_ANALISIS, false],
            [EstadoTicket::EN_ANALISIS, EstadoTicket::ASIGNADO, false],
            [EstadoTicket::ASIGNADO, EstadoTicket::EN_PROGRESO, true],
            [EstadoTicket::EN_PROGRESO, EstadoTicket::RESUELTO, false],
        ];

        foreach ($transicionesOperativas as [$origen, $destino, $requiereResponsable]) {
            $this->insertRules($origen, $destino, $rolesOperativos, $requiereResponsable);
        }

        $rolesCierre = [
            Role::CLIENTE_INTERNO,
            Role::SOPORTE,
            Role::COORDINADOR,
            Role::ADMIN,
        ];

        $this->insertRules(EstadoTicket::RESUELTO, EstadoTicket::CERRADO, $rolesCierre, false);

        $rolesCancelacion = [
            Role::CLIENTE_INTERNO,
            Role::COORDINADOR,
            Role::ADMIN,
        ];

        $cancelables = [
            EstadoTicket::NUEVO,
            EstadoTicket::EN_ANALISIS,
            EstadoTicket::ASIGNADO,
            EstadoTicket::EN_PROGRESO,
            EstadoTicket::RESUELTO,
        ];

        foreach ($cancelables as $origen) {
            $this->insertRules($origen, EstadoTicket::CANCELADO, $rolesCancelacion, false);
        }
    }

    private function insertRules(string $origen, string $destino, array $roles, bool $requiereResponsable): void
    {
        $estadoOrigenId = $this->estadoId($origen);
        $estadoDestinoId = $this->estadoId($destino);

        foreach ($roles as $rolNombre) {
            $rolId = Role::query()->where('nombre', $rolNombre)->value('id');
            if (! $rolId) {
                continue;
            }

            DB::table('reglas_transicion_estado')->updateOrInsert(
                [
                    'estado_origen_id' => $estadoOrigenId,
                    'estado_destino_id' => $estadoDestinoId,
                    'rol_id' => $rolId,
                ],
                [
                    'requiere_responsable' => $requiereResponsable,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function estadoId(string $nombre): int
    {
        return (int) EstadoTicket::query()
            ->where('nombre', $nombre)
            ->value('id');
    }
}
