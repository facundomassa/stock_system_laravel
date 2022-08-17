@extends('layouts.app')

@section('content')
    <div class="container">
        <p>{{$enterprise->id }}</p>
        <div class="form-group">
            <label for="name">Nombre:</label>
            <p>{{$enterprise->name }}</p>
        </div>
        <br>
        <a class="btn btn-success" href="{{ url('enterprise/' . $enterprise->id . '/edit') }}">Editar</a>
        <a class="btn btn-primary" href="{{ url('enterprise/') }}">Regresar</a>
        <form class="d-inline" action="{{ url('/enterprise/' . $enterprise->id) }}" method="post">
            @csrf
            {{ method_field('DELETE') }}
            <input class="btn btn-danger" type="submit" onclick="return confirm('¿Quieres borrar?')"
                value="Borrar">
        </form>
    </div>
@endsection
