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
        DB::table('estados_ticket')->updateOrInsert(
            ['nombre' => 'Nuevo'],
            [
                'es_terminal' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
