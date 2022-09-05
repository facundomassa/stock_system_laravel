@extends('layouts.app')



@section('content')
<div class="container">
    <form action="{{url('/movement')}}" method="post">
        @csrf
        @include('movement.form', ['modo' => 'Crear'])
    </form>
    <a class="btn btn-primary m-2" href="{{ url('/refer') }}">Regresar</a>
</div>
@endsection
