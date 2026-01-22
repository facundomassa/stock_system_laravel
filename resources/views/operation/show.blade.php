@extends('layouts.app')

@section('content')
    <div class="container">
        <p>{{$operation->id }}</p>
        <div class="form-group">
            <label for="name">Nombre:</label>
            <p>{{$operation->name }}</p>
        </div>
        <br>
        <a class="btn btn-success" href="{{ url('operation/' . $operation->id . '/edit') }}">Editar</a>
        <a class="btn btn-primary" href="{{ url('operation/') }}">Regresar</a>
        <form class="d-inline" action="{{ url('/operation/' . $operation->id) }}" method="post">
            @csrf
            {{ method_field('DELETE') }}
            <input class="btn btn-danger" type="submit" onclick="return confirm('¿Quieres borrar?')"
                value="Borrar">
        </form>
    </div>
@endsection
