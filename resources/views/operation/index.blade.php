@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')
        <table class="table table-striped table-hover table-md" >
            <thead >
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">Nombre</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($operations as $operation)
                    <tr>
                        <td>{{ $operation->id }}</td>
                        <td>{{ $operation->name }}</td>
                        <td>
                            <a class="btn btn-warning py-0" href="{{ url('/operation/' . $operation->id . '/edit') }}">Editar</a>

                            <form class="d-inline" action="{{ url('/operation/' . $operation->id) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <input class="btn btn-danger py-0" type="submit" onclick="return confirm('¿Quieres borrar?')"
                                    value="Borrar">
                            </form>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/operation/' . $operation->id) }}"><i class="bi bi-eye-fill"></i></a>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $operations->links('vendor.pagination.bootstrap-5')!!}
        <a class="btn btn-success" href="{{ url('/operation/create') }}">Nueva sucursal</a>
    </div>
@endsection