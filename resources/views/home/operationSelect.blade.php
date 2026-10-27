@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')
        <div class="flex-column align-items-center justify-content-center">
            <h3 class="text-center m-3">Hola {{ auth()->user()->name }}, selecciona una operación</h3>
            <div class="d-flex align-items-center justify-content-center m-4">
                <form action="{{ url('operation-select') }}" method="POST">
                    @csrf <!-- Protección CSRF -->
                    <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                        @forelse(get_allowed_operations() as $operation)
                            <button type="submit" class="btn btn-outline-secondary" name="selected_operation" value="{{ $operation->name }}">
                                Seleccionar Operación: {{ $operation->name }}
                            </button>
                        @empty
                            <div class="alert alert-warning text-center" role="alert">
                                Tu usuario no tiene operaciones asignadas. Pedile a un administrador que te habilite.
                            </div>
                        @endforelse
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
