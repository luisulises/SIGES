<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Sistema;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    /**
     * Seed demo users and base catalogs for local development.
     */
    public function run(): void
    {
        $roles = [Role::CLIENTE_INTERNO, Role::SOPORTE, Role::COORDINADOR, Role::ADMIN];
        foreach ($roles as $rol) {
            Role::query()->firstOrCreate(['nombre' => $rol]);
        }

        $roleIds = Role::query()->pluck('id', 'nombre');

        $users = [
            ['nombre' => 'Cliente Demo', 'email' => 'cliente@siges.test', 'rol' => Role::CLIENTE_INTERNO],
            ['nombre' => 'Soporte Demo', 'email' => 'soporte@siges.test', 'rol' => Role::SOPORTE],
            ['nombre' => 'Coordinador Demo', 'email' => 'coordinador@siges.test', 'rol' => Role::COORDINADOR],
            ['nombre' => 'Admin Demo', 'email' => 'admin@siges.test', 'rol' => Role::ADMIN],
        ];

        foreach ($users as $data) {
            User::query()->updateOrCreate(['email' => $data['email']], [
                'nombre' => $data['nombre'],
                'password' => Hash::make('password'),
                'rol_id' => $roleIds[$data['rol']],
                'activo' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }

        if (Sistema::query()->count() === 0) {
            Sistema::query()->create(['nombre' => 'SIGES', 'activo' => true]);
        }

        $coordinador = User::query()->where('email', 'coordinador@siges.test')->first();
        if ($coordinador) {
            $sistemaIds = Sistema::query()->where('activo', true)->pluck('id')->all();
            $coordinador->sistemasCoordinados()->syncWithoutDetaching($sistemaIds);
        }

        if (DB::table('prioridades')->count() === 0) {
            $now = now();
            DB::table('prioridades')->insert([
                ['nombre' => 'Baja', 'orden' => 1, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
                ['nombre' => 'Media', 'orden' => 2, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
                ['nombre' => 'Alta', 'orden' => 3, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('tipos_solicitud')->count() === 0) {
            $now = now();
            DB::table('tipos_solicitud')->insert([
                ['nombre' => 'Incidente', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
                ['nombre' => 'Requerimiento', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }
}

