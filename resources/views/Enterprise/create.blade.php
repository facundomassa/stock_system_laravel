@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{url('/enterprise')}}" method="post">
        @csrf
        @include('enterprise.form', ['modo' => 'Crear'])
    </form>
</div>
@endsection