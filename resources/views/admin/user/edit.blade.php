@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Editar Perfil de Usuario</h2>
        @include('layouts.alert')

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Información Básica -->
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
            </div>

            <div class="mb-3">
                <label for="surname" class="form-label">Apellido</label>
                <input type="text" name="surname" id="surname" class="form-control" value="{{ $user->surname }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña (Opcional)</label>
                <input type="password" name="password" id="password" class="form-control">
                <small class="text-muted">Déjalo en blanco si no deseas cambiar la contraseña.</small>
            </div>

            <div class="mb-3">
                <label for="is_active" class="form-label">Activo</label>
                <input type="hidden" name="is_active" id="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ $user->is_active ? 'checked' : '' }}>
                <small class="text-muted">Si está activo, el usuario podrá iniciar sesión.</small>
            </div>

            <!-- Roles -->
            <div class="mb-3">
                <label for="roles" class="form-label">Roles</label>
                <select name="role" id="roles" class="form-control">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Permisos -->
            <div class="mb-3">
                <label class="form-label">Permisos</label>
                <div class="form-check">
                    @foreach($permissions as $permission)
                        <div class="mb-2">
                            <input 
                                type="checkbox" 
                                name="permissions[]" 
                                value="{{ $permission->name }}" 
                                id="perm-{{ $permission->id }}"
                                {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                class="form-check-input"
                            >
                            <label for="perm-{{ $permission->id }}" class="form-check-label">
                                {{ $permission->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Botón para Guardar -->
            <input class="btn btn-success" type="submit" value="Actualizar Datos">
            <a class="btn btn-primary" href="{{ url('admin/users') }}">Regresar</a>
        </form>
    </div>
@endsection
