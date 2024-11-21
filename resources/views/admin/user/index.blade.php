@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Administración de Usuarios</h2>
        @include('layouts.alert')

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol Actual</th>
                    <th>Permisos Actuales</th>
                    <th>Editar Usuario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->getRoleNames()->join(', ') }}</td>
                        <td>{{ $user->getPermissionNames()->join(', ') }}</td>
                        <td>
                            <a class="btn btn-warning py-0" href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">Editar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
