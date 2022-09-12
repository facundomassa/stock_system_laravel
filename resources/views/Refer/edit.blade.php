@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('/refer/' . $refer->id) }}" method="post">
            @csrf
            {{ method_field('PATCH') }}
            @include('refer.form', ['modo' => 'Actualizar'])
        </form>
        <a class="btn btn-primary m-2" href="{{ url('/refer/' . $refer->id) }}">Regresar</a>
    </div>
@endsection