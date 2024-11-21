@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')
        <div class="d-flex align-items-center justify-content-center">
            <h3>Hola {{ auth()->user()->name }}, selecciona una operación</h3>
            <form action="{{ url('operation-select') }}" method="POST">
                @csrf <!-- Protección CSRF -->
                <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                    @foreach($allowed_operations as $operation)
                        <button type="submit" class="btn btn-outline-secondary" name="selected_operation" value="{{ $operation->name }}">
                            Seleccionar Operación: {{ $operation->name }}
                        </button>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
@endsection
