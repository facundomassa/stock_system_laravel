@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{url('/direction')}}" method="post">
        @csrf
        @include('direction.form', ['modo' => 'Crear'])
    </form>
</div>
@endsection