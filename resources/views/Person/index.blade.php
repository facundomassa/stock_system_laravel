@extends('layouts.app')

@section('content')
    <div class="container">
        @if (Session::has('mensaje'))
        <div class="alert alert-success alert-dismissible" role="alert">
            
                <strong>{{ Session::get('mensaje') }}</strong>
            
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Cuit</th>
                    <th>Telefono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($persons as $person)
                    <tr>
                        <td>{{ $person->id }}</td>
                        <td>{{ $person->name }}</td>
                        <td>{{ $person->surname }}</td>
                        <td>{{ $person->email }}</td>
                        <td>{{ $person->cuit }}</td>
                        <td>{{ $person->telephone }}</td>
                        <td>
                            <a class="btn btn-warning" href="{{ url('/person/' . $person->id . '/edit') }}">Editar</a>

                            <form class="d-inline" action="{{ url('/person/' . $person->id) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <input class="btn btn-danger" type="submit" onclick="return confirm('¿Quieres borrar?')"
                                    value="Borrar">
                            </form>
                            <a class="btn btn-outline-dark" href="{{ url('/person/' . $person->id) }}"><i class="bi bi-eye-fill"></i></a>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $persons->links()!!}
        <a class="btn btn-success" href="{{ url('/person/create') }}">Nuevo ingreso</a>
    </div>
@endsection