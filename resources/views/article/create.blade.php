@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{url('/article')}}" method="post">
        @csrf
        @include('article.form', ['modo' => 'Crear'])
    </form>
</div>
@endsection