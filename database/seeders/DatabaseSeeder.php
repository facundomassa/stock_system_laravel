<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Operation;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Crear permisos
        Permission::create(['name' => 'POP36']);
        Permission::create(['name' => 'POP20']);
        Permission::create(['name' => 'ADMIN']);

        Operation::create(['name' => 'POP36']);
        Operation::create(['name' => 'POP20']);
        Operation::create(['name' => 'ADMIN']);
        
        // Crear roles y asignar permisos a roles
        Role::create(['name' => 'dispatcher']);
        Role::create(['name' => 'admin'])->givePermissionTo('ADMIN');
        Role::create(['name' => 'tecnico']);

        // Crear usuarios
        User::create([
            'name' => 'admin',
            'surname' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
        ])->assignRole('admin');
        User::create([
            'name' => 'tecnico',
            'surname' => 'tecnico',
            'email' => 'tecnico@tecnico.com',
            'password' => Hash::make('tecnico'),
        ])->assignRole('tecnico');
        User::create([
            'name' => 'dispatcher36',
            'surname' => 'dispatcher36',
            'email' => 'dispatcher36@dispatcher.com',
            'password' => Hash::make('dispatcher36')
        ])->assignRole('dispatcher')->givePermissionTo('POP36');
        User::create([
            'name' => 'dispatcher20',
            'surname' => 'dispatcher20',
            'email' => 'dispatcher20@dispatcher.com',
            'password' => Hash::make('dispatcher20')
        ])->assignRole('dispatcher')->givePermissionTo('POP20');
    }
}
