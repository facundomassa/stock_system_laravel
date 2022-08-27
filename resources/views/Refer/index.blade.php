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
        <table class="table table-striped table-hover table-md">
            <thead>
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">Origen</th>
                    <th class="table-light">Destino</th>
                    <th class="table-light">Fecha Creado</th>
                    <th class="table-light">Fecha Finalizado</th>
                    <th class="table-light">Creado por</th>
                    <th class="table-light">Estaus</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($refers as $refer)
                    <tr>
                        <td>{{ $refer->id }}</td>
                        <td>{{ $refer->origen_id_stockcenter }}</td>
                        <td>{{ $refer->destiny_id_stockcenter }}</td>
                        <td>{{ $refer->created_at }}</td>
                        <td>{{ $refer->date_ended }}</td>
                        <td>{{ $refer->id_user }}</td>
                        <td>{{ $refer->status }}</td>
                        <td>
                            @if ($refer->status_n == 'I')
                                <a class="btn btn-success py-0" href="{{ url('refer/' . $refer->id . '/edit') }}">Emitir</a>
                            @endif
                            @if ($refer->status_n == 'I' || $refer->status_n == 'E')
                                <a class="btn btn-warning py-0"
                                    href="{{ url('/refer/' . $refer->id . '/edit') }}">Editar</a>
                                <form class="d-inline" action="{{ url('/refer/' . $refer->id) }}" method="post">
                                    @csrf
                                    {{ method_field('DELETE') }}
                                    <input class="btn btn-danger py-0" type="submit"
                                        onclick="return confirm('¿Cancelar remito?')" value="Cancelar">
                                </form>
                            @endif
                            <a class="btn btn-outline-dark py-0" href="{{ url('/refer/' . $refer->id) }}"><i
                                    class="bi bi-eye-fill"></i></a>

                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $refers->links() !!}
        <a class="btn btn-success" href="{{ url('/refer/create') }}">Nuevo ingreso</a>
    </div>
@endsection
