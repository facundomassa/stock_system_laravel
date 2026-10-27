<?php

namespace Tests\Feature;

use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['POP36', 'POP20', 'ADMIN'] as $name) {
            Permission::create(['name' => $name]);
            Operation::create(['name' => $name]);
        }

        Role::create(['name' => 'admin'])->givePermissionTo('ADMIN');
        Role::create(['name' => 'dispatcher']);
        Role::create(['name' => 'tecnico']);
    }

    private function userData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Juan',
            'surname' => 'Pérez',
            'email' => 'juan@example.com',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
            'role' => 'dispatcher',
            'permissions' => ['POP36'],
            'is_active' => '1',
            'stockcenter_id' => '',
        ], $overrides);
    }

    public function test_el_admin_puede_crear_un_usuario_desde_el_panel()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post('/admin/users', $this->userData())
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasNoErrors();

        $user = User::where('email', 'juan@example.com')->firstOrFail();

        $this->assertSame('Pérez', $user->surname);
        $this->assertTrue(Hash::check('secreto123', $user->password));
        $this->assertTrue($user->hasRole('dispatcher'));
        $this->assertSame(['POP36'], $user->getPermissionNames()->all());
        $this->assertTrue((bool) $user->is_active);
        $this->assertNull($user->stockcenter_id);
    }

    public function test_el_admin_puede_crear_un_usuario_inactivo()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post('/admin/users', $this->userData(['is_active' => '0', 'permissions' => []]))
            ->assertSessionHasNoErrors();

        $user = User::where('email', 'juan@example.com')->firstOrFail();

        $this->assertFalse((bool) $user->is_active);
        $this->assertCount(0, $user->getPermissionNames());
    }

    public function test_el_alta_valida_los_datos()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->from('/admin/users/create')
            ->post('/admin/users', $this->userData([
                'email' => $admin->email,
                'password_confirmation' => 'otra-clave',
                'role' => 'no-existe',
                'permissions' => ['NO-EXISTE'],
            ]))
            ->assertRedirect('/admin/users/create')
            ->assertSessionHasErrors(['email', 'password', 'role', 'permissions.0']);

        $this->assertDatabaseMissing('users', ['name' => 'Juan']);
    }

    public function test_un_usuario_que_no_es_admin_no_puede_crear_usuarios()
    {
        $dispatcher = User::factory()->create();
        $dispatcher->assignRole('dispatcher');
        $dispatcher->givePermissionTo('POP36');

        $this->actingAs($dispatcher)
            ->post('/admin/users', $this->userData(['role' => 'admin']))
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'juan@example.com']);
    }
}
