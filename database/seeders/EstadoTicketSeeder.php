<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Nuevo', 'es_terminal' => false],
            ['nombre' => 'En analisis', 'es_terminal' => false],
            ['nombre' => 'Asignado', 'es_terminal' => false],
            ['nombre' => 'En progreso', 'es_terminal' => false],
            ['nombre' => 'Resuelto', 'es_terminal' => false],
            ['nombre' => 'Cerrado', 'es_terminal' => true],
            ['nombre' => 'Cancelado', 'es_terminal' => true],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_ticket')->updateOrInsert(
                ['nombre' => $estado['nombre']],
                [
                    'es_terminal' => $estado['es_terminal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
