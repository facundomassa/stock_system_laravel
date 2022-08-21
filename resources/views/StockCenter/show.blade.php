@extends('layouts.app')

@section('content')
    <div class="container">
        <p>{{$stockcenter->id }}</p>
        <div class="form-group">
            <label for="code">Operacion:</label>
            <p>{{$stockcenter->id_enterprise }}</p>
        </div>
        <div class="form-group">
            <label for="name">Nombre:</label>
            <p>{{$stockcenter->name }}</p>
        </div>
        <div class="form-group">
            <label for="code">Tipo:</label>
            <p>{{$stockcenter->type }}</p>
        </div>
        <div class="form-group">
            <label for="unit">Direccion:</label>
            <p>{{$stockcenter->id_direction }}</p>
        </div>
        <div class="form-group">
            <label for="type">Encargado:</label>
            <p>{{$stockcenter->id_person }}</p>
        </div>
        <div class="form-group">
            <label for="telephone">Ultima Actualizacion:</label>
            <p>{{$stockcenter->updated_at }}</p>
        </div>
        <br>
        <a class="btn btn-success" href="{{ url('stockcenter/' . $stockcenter->id . '/edit') }}">Editar</a>
        <a class="btn btn-primary" href="{{ url('stockcenter/') }}">Regresar</a>
        <form class="d-inline" action="{{ url('/stockcenter/' . $stockcenter->id) }}" method="post">
            @csrf
            {{ method_field('DELETE') }}
            <input class="btn btn-danger" type="submit" onclick="return confirm('¿Quieres borrar?')"
                value="Borrar">
        </form>
    </div>
@endsection
