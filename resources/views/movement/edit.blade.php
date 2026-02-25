@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('/movement/' . $movement->id) }}" method="post">
            @csrf
            {{ method_field('PATCH') }}
            @include('movement.form', ['modo' => 'Actualizar'])
        </form>
    </div>
@endsection