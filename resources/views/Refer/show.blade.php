@extends('layouts.app')

@section('content')
    <div class="container">
        <p>{{ $refer->id }}</p>
        <div class="form-group">
            <label for="code">Origen:</label>
            <p>{{ $refer->origen_id_stockcenter }}</p>
        </div>
        <div class="form-group">
            <label for="name">Destino:</label>
            <p>{{ $refer->destiny_id_stockcenter }}</p>
        </div>
        <div class="form-group">
            <label for="code">Estatus:</label>
            <p>{{ $refer->status }}</p>
        </div>
        <div class="form-group">
            <label for="unit">Creado por:</label>
            <p>{{ $refer->id_user }}</p>
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
        @if ($refer->status_n == 'I')
            <a class="btn btn-success" href="{{ url('refer/' . $refer->id . '/edit') }}">Emitir</a>
        @endif
        @if ($refer->status_n == 'I' || $refer->status_n == 'E')
            <a class="btn btn-warning" href="{{ url('refer/' . $refer->id . '/edit') }}">Editar</a>
            
            <form class="d-inline" action="{{ url('/refer/' . $refer->id) }}" method="post">
                @csrf
                {{ method_field('DELETE') }}
                <input class="btn btn-danger" type="submit" onclick="return confirm('¿Cancelar remito?')" value="Cancelar">
            </form>
        @endif
        <a class="btn btn-primary" href="{{ url('refer/') }}">Regresar</a>
    </div>
@endsection
