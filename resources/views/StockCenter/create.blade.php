@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{url('/stockcenter')}}" method="post">
        @csrf
        @include('stockcenter.form', ['modo' => 'Crear'])
    </form>
</div>
@endsection