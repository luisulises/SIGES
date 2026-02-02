<?php

namespace Tests\Feature\Api;

use App\Models\Prioridad;
use App\Models\Role;
use App\Models\Sistema;
use App\Models\TipoSolicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminCatalogoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_admin_puede_gestionar_catalogos(): void
    {
        $admin = User::factory()->create([
            'rol_id' => $this->roleId(Role::ADMIN),
        ]);
        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/catalogos/sistemas', [
            'nombre' => 'Sistema X',
            'activo' => true,
        ])->assertCreated()
            ->assertJsonPath('data.nombre', 'Sistema X');

        $sistema = Sistema::query()->where('nombre', 'Sistema X')->firstOrFail();

        $this->patchJson("/api/admin/catalogos/sistemas/{$sistema->id}", [
            'activo' => false,
        ])->assertOk()
            ->assertJsonPath('data.activo', false);

        $this->postJson('/api/admin/catalogos/prioridades', [
            'nombre' => 'Urgente',
            'orden' => 10,
            'activo' => true,
        ])->assertCreated()
            ->assertJsonPath('data.nombre', 'Urgente')
            ->assertJsonPath('data.orden', 10);

        $prioridad = Prioridad::query()->where('nombre', 'Urgente')->firstOrFail();

        $this->patchJson("/api/admin/catalogos/prioridades/{$prioridad->id}", [
            'orden' => 5,
        ])->assertOk()
            ->assertJsonPath('data.orden', 5);

        $this->postJson('/api/admin/catalogos/tipos-solicitud', [
            'nombre' => 'Incidente',
            'activo' => true,
        ])->assertCreated()
            ->assertJsonPath('data.nombre', 'Incidente');

        $tipo = TipoSolicitud::query()->where('nombre', 'Incidente')->firstOrFail();

        $this->patchJson("/api/admin/catalogos/tipos-solicitud/{$tipo->id}", [
            'activo' => false,
        ])->assertOk()
            ->assertJsonPath('data.activo', false);
    }

    public function test_no_admin_no_puede_gestionar_catalogos(): void
    {
        $soporte = User::factory()->create([
            'rol_id' => $this->roleId(Role::SOPORTE),
        ]);
        Sanctum::actingAs($soporte);

        $this->getJson('/api/admin/catalogos/sistemas')->assertStatus(403);
        $this->postJson('/api/admin/catalogos/prioridades', [
            'nombre' => 'X',
        ])->assertStatus(403);
    }

    private function roleId(string $nombre): int
    {
        return (int) Role::query()->where('nombre', $nombre)->value('id');
    }
}

