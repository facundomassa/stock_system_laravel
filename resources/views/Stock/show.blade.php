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
        <table class="table table-striped table-hover table-md" id="movement-t">
            <thead>
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">Codigo</th>
                    <th class="table-light">Nombre</th>
                    <th class="table-light">Unidad</th>
                    <th class="table-light">Tipo</th>
                    <th class="table-light">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($movements))
                    @foreach ($movements as $movement)
                        <tr>
                            <td>{{ $movement->id_article->id }}</td>
                            <td>{{ $movement->id_article->code }}</td>
                            <td>{{ $movement->id_article->name }}</td>
                            <td>{{ $movement->id_article->unit }}</td>
                            <td>{{ $movement->id_article->type }}</td>
                            <td>{{ $movement->quantity }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        @if ($refer->status == 'I')
            <a class="btn btn-success" href="{{ url('movement/' . $refer->id . '/edit') }}">Emitir</a>
        @endif
        @if ($refer->status == 'I' || $refer->status == 'E')
            <a class="btn btn-warning" href="{{ url('movement/' . $refer->id . '/edit') }}">Editar</a>

            <form class="d-inline" action="{{ url('/movement/' . $refer->id) }}" method="post">
                @csrf
                {{ method_field('DELETE') }}
                <input class="btn btn-danger" type="submit" onclick="return confirm('??Cancelar remito?')" value="Cancelar">
            </form>
        @endif
        <a class="btn btn-primary" href="{{ url('movement/') }}">Regresar</a>
    </div>
@endsection
