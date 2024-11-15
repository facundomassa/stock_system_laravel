@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{url('/operation')}}" method="post">
        @csrf
        @include('operation.form', ['modo' => 'Crear'])
    </form>
</div>
@endsection