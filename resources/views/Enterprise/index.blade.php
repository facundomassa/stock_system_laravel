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
                    <th class="table-light">Nombre</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($enterprises as $enterprise)
                    <tr>
                        <td>{{ $enterprise->id }}</td>
                        <td>{{ $enterprise->name }}</td>
                        <td>
                            <a class="btn btn-warning py-0" href="{{ url('/enterprise/' . $enterprise->id . '/edit') }}">Editar</a>

                            <form class="d-inline" action="{{ url('/enterprise/' . $enterprise->id) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <input class="btn btn-danger py-0" type="submit" onclick="return confirm('¿Quieres borrar?')"
                                    value="Borrar">
                            </form>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/enterprise/' . $enterprise->id) }}"><i class="bi bi-eye-fill"></i></a>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $enterprises->links()!!}
        <a class="btn btn-success" href="{{ url('/enterprise/create') }}">Nueva sucursal</a>
    </div>
@endsection