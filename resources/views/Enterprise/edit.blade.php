@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('/enterprise/' . $enterprise->id) }}" method="post">
            @csrf
            {{ method_field('PATCH') }}
            @include('enterprise.form', ['modo' => 'Actualizar'])
        </form>
    </div>
@endsection