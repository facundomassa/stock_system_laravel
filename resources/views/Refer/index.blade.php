@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')
          
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
                        <td>{{ $refer->nameOrigin }}</td>
                        <td>{{ $refer->nameDestiny }}</td>
                        <td>{{ $refer->created_at_formatted }}</td>
                        <td>{{ $refer->dateEndedFormatted }}</td>
                        <td>{{ $refer->fullNameUser }}</td>
                        <td>{{ $refer->statusName }}</td>
                        <td>
                            @if ($refer->status == 'I')
                                <a class="btn btn-success py-0" href="{{ url('refer/emited/' . $refer->id) }}">Emitir</a>
                            @endif
                            @if ($refer->status == 'I' || $refer->status == 'E')
                                <a class="btn btn-warning py-0"
                                    href="{{ url('/refer/' . $refer->id . '/edit') }}">Editar</a>
                                <form class="d-inline" action="{{ url('/refer/finalized/' . $refer->id) }}" method="post">
                                    @csrf
                                    {{ method_field('PATCH') }}
                                    <input class="btn btn-danger py-0" type="submit"
                                        onclick="return confirm('¿Finalizar remito?')" value="Finalizar">
                                </form>
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
        {!! $refers->links('vendor.pagination.bootstrap-5') !!}
        <a class="btn btn-success" href="{{ url('/refer/create') }}">Nuevo ingreso</a>
    </div>
@endsection
