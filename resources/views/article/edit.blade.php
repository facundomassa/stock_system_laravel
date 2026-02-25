@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('/article/' . $article->id) }}" method="post">
            @csrf
            {{ method_field('PATCH') }}
            @include('article.form', ['modo' => 'Actualizar'])
        </form>
    </div>
@endsection