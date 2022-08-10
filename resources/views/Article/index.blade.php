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
                    <th class="table-light">Codigo</th>
                    <th class="table-light">Unidad</th>
                    <th class="table-light">Tipo</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articles as $article)
                    <tr>
                        <td>{{ $article->id }}</td>
                        <td>{{ $article->name }}</td>
                        <td>{{ $article->code }}</td>
                        <td>{{ $article->unit }}</td>
                        <td>{{ $article->type }}</td>
                        <td>
                            <a class="btn btn-warning py-0" href="{{ url('/article/' . $article->id . '/edit') }}">Editar</a>

                            <form class="d-inline" action="{{ url('/article/' . $article->id) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <input class="btn btn-danger py-0" type="submit" onclick="return confirm('¿Quieres borrar?')"
                                    value="Borrar">
                            </form>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/article/' . $article->id) }}"><i class="bi bi-eye-fill"></i></a>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $articles->links()!!}
        <a class="btn btn-success" href="{{ url('/article/create') }}">Nuevo ingreso</a>
    </div>
@endsection