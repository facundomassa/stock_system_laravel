@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('/person/' . $person->id) }}" method="post">
            @csrf
            {{ method_field('PATCH') }}
            @include('person.form', ['modo' => 'Actualizar'])
        </form>
    </div>
@endsection