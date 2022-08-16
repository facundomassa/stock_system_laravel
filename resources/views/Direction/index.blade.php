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
        <table class="table table-striped table-hover table-md" >
            <thead >
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">Pais</th>
                    <th class="table-light">Provincia</th>
                    <th class="table-light">Ciudad</th>
                    <th class="table-light">Localidad</th>
                    <th class="table-light">Calle</th>
                    <th class="table-light">Numero</th>
                    <th class="table-light">Dpto</th>
                    <th class="table-light">Casa</th>
                    <th class="table-light">Piso</th>
                    <th class="table-light">CP</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($directions as $direction)
                    <tr>
                        <td>{{ $direction->id }}</td>
                        <td>{{ $direction->country }}</td>
                        <td>{{ $direction->state }}</td>
                        <td>{{ $direction->city }}</td>
                        <td>{{ $direction->locality }}</td>
                        <td>{{ $direction->street }}</td>
                        <td>{{ $direction->number }}</td>
                        <td>{{ $direction->deparment }}</td>
                        <td>{{ $direction->house }}</td>
                        <td>{{ $direction->floor }}</td>
                        <td>{{ $direction->cp }}</td>
                        <td>
                            <a class="btn btn-warning py-0" href="{{ url('/direction/' . $direction->id . '/edit') }}">Editar</a>

                            <form class="d-inline" action="{{ url('/direction/' . $direction->id) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <input class="btn btn-danger py-0" type="submit" onclick="return confirm('¿Quieres borrar?')"
                                    value="Borrar">
                            </form>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/direction/' . $direction->id) }}"><i class="bi bi-eye-fill"></i></a>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $directions->links()!!}
        <a class="btn btn-success" href="{{ url('/direction/create') }}">Nuevo ingreso</a>
    </div>
@endsection