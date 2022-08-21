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
                    <th class="table-light">Operacion</th>
                    <th class="table-light">Nombre</th>
                    <th class="table-light">Tipo</th>
                    <th class="table-light">Direccion</th>
                    <th class="table-light">Encargado</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stockcenters as $stockcenter)
                    <tr>
                        <td>{{ $stockcenter->id }}</td>
                        <td>{{ $stockcenter->id_enterprise }}</td>
                        <td>{{ $stockcenter->name }}</td>
                        <td>{{ $stockcenter->type }}</td>
                        <td>{{ $stockcenter->id_direction }}</td>
                        <td>{{ $stockcenter->id_person }}</td>
                        <td>
                            <a class="btn btn-warning py-0" href="{{ url('/stockcenter/' . $stockcenter->id . '/edit') }}">Editar</a>

                            <form class="d-inline" action="{{ url('/stockcenter/' . $stockcenter->id) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <input class="btn btn-danger py-0" type="submit" onclick="return confirm('¿Quieres borrar?')"
                                    value="Borrar">
                            </form>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/stockcenter/' . $stockcenter->id) }}"><i class="bi bi-eye-fill"></i></a>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $stockcenters->links()!!}
        <a class="btn btn-success" href="{{ url('/stockcenter/create') }}">Nuevo ingreso</a>
    </div>
@endsection