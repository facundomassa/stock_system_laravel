@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{url('/refer')}}" method="post">
        @csrf
        @include('refer.form', ['modo' => 'Crear'])
    </form>
</div>
@endsection