<?php

namespace Tests\Feature;

use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OperationAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Cada operación tiene un permiso con el mismo nombre
        foreach (['POP36', 'POP20', 'ADMIN'] as $name) {
            Permission::create(['name' => $name]);
            Operation::create(['name' => $name]);
        }

        Role::create(['name' => 'tecnico']);
    }

    public function test_el_registro_publico_esta_deshabilitado()
    {
        $this->get('/register')->assertNotFound();

        $this->post('/register', [
            'name' => 'Intruso',
            'surname' => 'Intruso',
            'email' => 'intruso@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'intruso@example.com']);
    }

    public function test_el_registro_por_api_esta_deshabilitado()
    {
        $this->postJson('/api/register', [
            'name' => 'Intruso',
            'surname' => 'Intruso',
            'email' => 'intruso@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'intruso@example.com']);
    }

    public function test_operation_select_rechaza_una_operacion_no_permitida()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['POP36', 'POP20']);

        foreach (['ADMIN', 'NO-EXISTE'] as $operation) {
            $this->actingAs($user)
                ->from('/operation-select')
                ->post('/operation-select', ['selected_operation' => $operation])
                ->assertRedirect('/operation-select')
                ->assertSessionHasErrors('selected_operation')
                ->assertSessionMissing('selected_operation');
        }
    }

    public function test_operation_select_acepta_una_operacion_permitida()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['POP36', 'POP20']);

        $this->actingAs($user)
            ->post('/operation-select', ['selected_operation' => 'POP20'])
            ->assertRedirect(route('home'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('selected_operation', 'POP20');

        $this->get('/notifications/count')->assertOk();
    }

    public function test_un_usuario_sin_permisos_no_puede_acceder_a_home()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/home')
            ->assertRedirect('/operation-select');

        $this->post('/operation-select', ['selected_operation' => 'POP36'])
            ->assertSessionHasErrors('selected_operation')
            ->assertSessionMissing('selected_operation');

        $this->get('/home')->assertRedirect('/operation-select');
        $this->get('/stock')->assertRedirect('/operation-select');
    }

    public function test_una_operacion_en_sesion_que_no_esta_permitida_se_descarta()
    {
        // Usuario sin permisos con una operación cargada en la sesión
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['selected_operation' => 'POP36'])
            ->get('/home')
            ->assertRedirect('/operation-select')
            ->assertSessionMissing('selected_operation');

        // Usuario al que se le quitó el permiso de la operación que tenía seleccionada
        $user = User::factory()->create();
        $user->givePermissionTo(['POP36', 'POP20']);

        $this->actingAs($user)
            ->withSession(['selected_operation' => 'POP36'])
            ->get('/notifications/count')
            ->assertOk();

        $user->revokePermissionTo('POP36');

        $this->get('/notifications/count')
            ->assertRedirect('/operation-select')
            ->assertSessionMissing('selected_operation');
    }

    public function test_el_tecnico_sigue_entrando_sin_operacion_seleccionada()
    {
        $user = User::factory()->create();
        $user->assignRole('tecnico');
        $user->givePermissionTo('POP36');

        $this->actingAs($user)
            ->get('/notifications/count')
            ->assertOk();
    }

    public function test_un_usuario_inactivo_no_puede_acceder()
    {
        $user = User::factory()->create(['is_active' => false]);
        $user->givePermissionTo('POP36');

        $this->actingAs($user)
            ->withSession(['selected_operation' => 'POP36'])
            ->get('/home')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
