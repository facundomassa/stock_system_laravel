@extends('layouts.app')

@section('content')
    <div class="container">
        <p>{{$person->id }}</p>
        <div class="form-group">
            <label for="name">Nombre:</label>
            <p>{{$person->name }}</p>
        </div>
        <div class="form-group">
            <label for="surname">Apellido:</label>
            <p>{{$person->surname }}</p>
        </div>
        <div class="form-group">
            <label for="email">Correo:</label>
            <p>{{$person->email }}</p>
        </div>
        <div class="form-group">
            <label for="cuit">CUIT:</label>
            <p>{{$person->cuit }}</p>
        </div>
        <div class="form-group">
            <label for="telephone">Numero de Telefono:</label>
            <p>{{$person->telephone }}</p>
        </div>
        <div class="form-group">
            <label for="telephone">Ultima Actualizacion:</label>
            <p>{{$person->updated_at }}</p>
        </div>
        <br>
        <a class="btn btn-success" href="{{ url('person/' . $person->id . '/edit') }}">Editar</a>
        <a class="btn btn-primary" href="{{ url('person/') }}">Regresar</a>
        <form class="d-inline" action="{{ url('/person/' . $person->id) }}" method="post">
            @csrf
            {{ method_field('DELETE') }}
            <input class="btn btn-danger" type="submit" onclick="return confirm('¿Quieres borrar?')"
                value="Borrar">
        </form>
    </div>
@endsection
