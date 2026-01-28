<?php

namespace Database\Seeders;

use App\Models\Operation;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear permisos primero (esto es lo más importante)
        $permissions = ['POP36', 'POP20', 'ADMIN'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. Crear operaciones
        foreach (['POP36', 'POP20', 'ADMIN'] as $op) {
            Operation::firstOrCreate(['name' => $op]);
        }

        // 3. Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'dispatcher']);
        Role::firstOrCreate(['name' => 'tecnico']);

        // 4. Asignar permisos a roles
        $adminRole->syncPermissions(['ADMIN']);
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'admin',
                'surname' => 'admin',
                'password' => Hash::make('admin'),
            ]
        );
        $admin->assignRole('admin');

        $tecnico = User::firstOrCreate(
            ['email' => 'tecnico@tecnico.com'],
            [
                'name' => 'tecnico',
                'surname' => 'tecnico',
                'password' => Hash::make('tecnico'),
            ]
        );
        $tecnico->assignRole('tecnico');
        $tecnico->givePermissionTo('POP36');

        $dispatcher36 = User::firstOrCreate(
            ['email' => 'dispatcher36@dispatcher.com'],
            [
                'name' => 'dispatcher36',
                'surname' => 'dispatcher36',
                'password' => Hash::make('dispatcher36'),
            ]
        );
        $dispatcher36->assignRole('dispatcher');
        $dispatcher36->givePermissionTo('POP36');

        $dispatcher20 = User::firstOrCreate(
            ['email' => 'dispatcher20@dispatcher.com'],
            [
                'name' => 'dispatcher20',
                'surname' => 'dispatcher20',
                'password' => Hash::make('dispatcher20'),
            ]
        );
        $dispatcher20->assignRole('dispatcher');
        $dispatcher20->givePermissionTo('POP20');
    }
}