@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{url('/person')}}" method="post">
        @csrf
        @include('person.form', ['modo' => 'Crear'])
    </form>
</div>
@endsection