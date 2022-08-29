@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')
        <table class="table table-striped table-hover table-md">
            <thead>
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">Nombre</th>
                    <th class="table-light">Correo</th>
                    <th class="table-light">Cuit</th>
                    <th class="table-light">Telefono</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($persons as $person)
                    <tr>
                        <td>{{ $person->id }}</td>
                        <td>{{ $person->fullName }}</td>
                        <td>{{ $person->email }}</td>
                        <td>{{ $person->cuit }}</td>
                        <td>{{ $person->telephone }}</td>
                        <td>
                            <a class="btn btn-warning py-0" href="{{ url('/person/' . $person->id . '/edit') }}">Editar</a>

                            <form class="d-inline" action="{{ url('/person/' . $person->id) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <input class="btn btn-danger py-0" type="submit" onclick="return confirm('¿Quieres borrar?')"
                                    value="Borrar">
                            </form>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/person/' . $person->id) }}"><i class="bi bi-eye-fill"></i></a>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $persons->links('vendor.pagination.bootstrap-5')!!}
        <a class="btn btn-success" href="{{ url('/person/create') }}">Nuevo ingreso</a>
    </div>
@endsection