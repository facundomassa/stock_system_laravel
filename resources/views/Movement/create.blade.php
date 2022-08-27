@extends('layouts.app')



@section('content')
<div class="container">
    <form action="{{url('/movement')}}" method="post">
        @csrf
        @include('movement.form', ['modo' => 'Crear'])
    </form>
</div>
@endsection
