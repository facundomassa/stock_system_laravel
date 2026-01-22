@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('/operation/' . $operation->id) }}" method="post">
            @csrf
            {{ method_field('PATCH') }}
            @include('operation.form', ['modo' => 'Actualizar'])
        </form>
    </div>
@endsection