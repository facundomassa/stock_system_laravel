@extends('layouts.app')

@section('content')
    <div class="container">
        <p>{{$direction->id }}</p>
        <div class="form-group">
            <label for="country">Pais:</label>
            <p>{{$direction->country }}</p>
        </div>
        <div class="form-group">
            <label for="state">Provincia:</label>
            <p>{{$direction->state }}</p>
        </div>
        <div class="form-group">
            <label for="city">Ciudad:</label>
            <p>{{$direction->city }}</p>
        </div>
        <div class="form-group">
            <label for="locality">Localidad:</label>
            <p>{{$direction->locality }}</p>
        </div>
        <div class="form-group">
            <label for="street">Calle:</label>
            <p>{{$direction->street }}</p>
        </div>
        <div class="form-group">
            <label for="number">Numero:</label>
            <p>{{$direction->number }}</p>
        </div>
        <div class="form-group">
            <label for="department">Departamento:</label>
            <p>{{$direction->department }}</p>
        </div>
        <div class="form-group">
            <label for="house">Casa:</label>
            <p>{{$direction->house }}</p>
        </div>
        <div class="form-group">
            <label for="floor">Piso:</label>
            <p>{{$direction->floor }}</p>
        </div>
        <div class="form-group">
            <label for="cp">Codigo Postal:</label>
            <p>{{$direction->cp }}</p>
        </div>
        <div class="form-group">
            <label for="updated_at">Ultima Actualizacion:</label>
            <p>{{$direction->updated_at }}</p>
        </div>
        <br>
        <a class="btn btn-success" href="{{ url('direction/' . $direction->id . '/edit') }}">Editar</a>
        <a class="btn btn-primary" href="{{ url('direction/') }}">Regresar</a>
        <form class="d-inline" action="{{ url('/direction/' . $direction->id) }}" method="post">
            @csrf
            {{ method_field('DELETE') }}
            <input class="btn btn-danger" type="submit" onclick="return confirm('¿Quieres borrar?')"
                value="Borrar">
        </form>
    </div>
@endsection
