@extends('layouts.app')

@section('content')

    <div class="container">
        @include('layouts/alert')
        <div class="d-flex justify-content-between">
            <div class="flex-grow-1">
                <h3 class="text-center">Detalle Remito</h3>
                <p>{{ $refer->id }}</p>
                <div class="form-group">
                    <label for="code">Origen:</label>
                    <p>{{ $refer->nameOrigin }}</p>
                </div>
                <div class="form-group">
                    <label for="name">Destino:</label>
                    <p>{{ $refer->nameDestiny }}</p>
                </div>
                <div class="form-group">
                    <label for="code">Estatus:</label>
                    <p>{{ $refer->statusName }}</p>
                </div>
                <div class="form-group">
                    <label for="unit">Creado por:</label>
                    <p>{{ $refer->fullNameUser }}</p>
                </div>
                <div class="form-group">
                    <label for="type">Creado:</label>
                    <p>{{ $refer->created_at }}</p>
                </div>
                <div class="form-group">
                    <label for="type">Finalizado:</label>
                    <p>{{ $refer->date_ended }}</p>
                </div>
                <div class="form-group">
                    <label for="telephone">Ultima Actualizacion:</label>
                    <p>{{ $refer->updated_at }}</p>
                </div>
                <br>
                @if ($refer->status == 'I')
                    <a class="btn btn-success" href="{{ url('refer/' . $refer->id . '/edit') }}">Emitir</a>
                @endif
                @if ($refer->status == 'I' || $refer->status == 'E')
                    <a class="btn btn-warning" href="{{ url('refer/' . $refer->id . '/edit') }}">Editar</a>
                    <form class="d-inline" action="{{ url('/refer/finalized/' . $refer->id) }}" method="post">
                        @csrf
                        {{ method_field('PATCH') }}
                        <input class="btn btn-danger" type="submit"
                            onclick="return confirm('¿Finalizar remito?')" value="Finalizar">
                    </form>
                    <form class="d-inline" action="{{ url('/refer/' . $refer->id) }}" method="post">
                        @csrf
                        {{ method_field('DELETE') }}
                        <input class="btn btn-danger" type="submit" onclick="return confirm('¿Cancelar remito?')" value="Cancelar">
                    </form>
                @endif
                <a class="btn btn-primary" href="{{ url('refer/') }}">Regresar</a>
            </div>
            <div class="d-flex flex-column w-50">
                <h3 class="text-center">Articulos</h3>
                <div class="flex-grow-1 bg-secondary p-2 align-self-stretch">
                    <table class="table table-striped table-hover table-md bg-light align-items-stretch" id="movement-t">
                        <thead>
                            <tr>
                                <th class="table-light">Codigo</th>
                                <th class="table-light">Nombre</th>
                                <th class="table-light">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($movements))
                                @foreach ($movements as $movement)
                                    <tr>
                                        <td>{{ $movement->Article->code }}</td>
                                        <td>{{ $movement->Article->name }}</td>
                                        <td>{{ $movement->quantity }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    
                </div>
                <br>
                @if ($refer->status == 'I' || $refer->status == 'E')
                        <a class="btn btn-warning" href="{{ url('movement/create/' . $refer->id ) }}">Editar Articulos</a>
                    @endif
            </div>

        </div>

        
    </div>
@endsection
