<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            Role::CLIENTE_INTERNO,
            Role::SOPORTE,
            Role::COORDINADOR,
            Role::ADMIN,
        ];

        foreach ($roles as $rol) {
            Role::query()->firstOrCreate(['nombre' => $rol]);
        }
    }
}
