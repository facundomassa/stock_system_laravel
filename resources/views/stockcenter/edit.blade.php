@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('/stockcenter/' . $stockcenter->id) }}" method="post">
            @csrf
            {{ method_field('PATCH') }}
            @include('stockcenter.form', ['modo' => 'Actualizar'])
        </form>
    </div>
@endsection