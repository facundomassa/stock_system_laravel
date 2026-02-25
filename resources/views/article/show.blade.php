@extends('layouts.app')

@section('content')
    <div class="container">
        <p>{{$article->id }}</p>
        <div class="form-group">
            <label for="name">Nombre:</label>
            <p>{{$article->name }}</p>
        </div>
        <div class="form-group">
            <label for="code">Codigo:</label>
            <p>{{$article->code }}</p>
        </div>
        <div class="form-group">
            <label for="unit">Unidad:</label>
            <p>{{$article->unitName }}</p>
        </div>
        <div class="form-group">
            <label for="type">Tipo:</label>
            <p>{{$article->type }}</p>
        </div>
        <div class="form-group">
            <label for="telephone">Ultima Actualizacion:</label>
            <p>{{$article->updated_at }}</p>
        </div>
        <br>
        <a class="btn btn-success" href="{{ url('article/' . $article->id . '/edit') }}">Editar</a>
        <a class="btn btn-primary" href="{{ url('article/') }}">Regresar</a>
        <form class="d-inline" action="{{ url('/article/' . $article->id) }}" method="post">
            @csrf
            {{ method_field('DELETE') }}
            <input class="btn btn-danger" type="submit" onclick="return confirm('¿Quieres borrar?')"
                value="Borrar">
        </form>
    </div>
@endsection
