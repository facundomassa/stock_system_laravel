@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="flex-grow-1">
                <h3 class="text-center">Stock</h3>
                
                <!-- Mostrar errores de validación -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Formulario para editar alerta -->
                <form action="{{ route('stock.update', $stock->id) }}" method="POST" class="mb-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">ID Stock:</label>
                                        <p class="form-control-plaintext">{{ $stock->id }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Centro de Stock:</label>
                                        <p class="form-control-plaintext">{{ $stock->stockCenter->name ?? 'N/A' }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Artículo:</label>
                                        <p class="form-control-plaintext">{{ $stock->article->name ?? 'N/A' }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Código:</label>
                                        <p class="form-control-plaintext">{{ $stock->article->code ?? '-' }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Unidad:</label>
                                        <p class="form-control-plaintext">{{ $stock->article->unit_name ?? '-' }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo:</label>
                                        <p class="form-control-plaintext">{{ $stock->article->type ?? '-' }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Cantidad Actual:</label>
                                        <p class="form-control-plaintext display-6">{{ $stock->quantity }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Última Actualización:</label>
                                        <p class="form-control-plaintext">
                                            {{ $stock->updated_at->format('d/m/Y H:i:s') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Campo para editar la alerta -->
                            <div class="mb-3">
                                <label for="quantity_alert" class="form-label fw-bold">Límite de Alerta:</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('quantity_alert') is-invalid @enderror" 
                                           name="quantity_alert" 
                                           id="quantity_alert"
                                           value="{{ old('quantity_alert', $stock->quantity_alert) }}"
                                           min="0"
                                           required>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save"></i> Actualizar Alerta
                                    </button>
                                    @error('quantity_alert')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    Se activará una alerta cuando la cantidad sea menor o igual a este valor.
                                    <span class="badge bg-{{ $stock->warning ? 'danger' : 'success' }} ms-2">
                                        {{ $stock->warning ? 'ALERTA ACTIVA' : 'NORMAL' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Botones de acción -->
                <div class="d-flex gap-2">
                    <a href="{{ route('stock.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Volver al Listado
                    </a>
                    
                    <!-- Formulario para eliminar (si es necesario) -->
                    @if(auth()->user()->can('delete_stock'))
                        <form action="{{ route('stock.destroy', $stock->id) }}" method="POST" 
                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar este registro de stock?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            
            <!-- Historial de movimientos -->
            <div class="d-flex flex-column w-50 ms-4">
                <h3 class="text-center">Historial de Movimientos</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>N° Remito</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Fecha</th>
                                <th>Cantidad</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movements as $movement)
                                @php
                                    $refer = $movement->refer;
                                    $isOrigin = ($refer->name_origin ?? null) == ($stock->stockCenter->name ?? null);
                                @endphp
                                <tr>
                                    <td>{{ $movement->id }}</td>
                                    <td>
                                        <a href="{{ route('refer.show', $refer->id ?? '#') }}" 
                                           class="text-decoration-none">
                                            {{ $refer->id ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $refer->name_origin ?? 'N/A' }}</td>
                                    <td>{{ $refer->name_destiny ?? 'N/A' }}</td>
                                    <td>{{ $refer->date_ended_formatted ?? 'N/A' }}</td>
                                    <td>
                                        @if($isOrigin)
                                            <span class="text-danger">
                                                <i class="bi bi-dash-circle"></i> -{{ $movement->quantity }}
                                            </span>
                                        @else
                                            <span class="text-success">
                                                <i class="bi bi-plus-circle"></i> +{{ $movement->quantity }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('movement.show', $refer->id ?? '#') }}" 
                                           class="btn btn-sm btn-outline-info" title="Ver movimiento">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No hay movimientos registrados para este stock.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                @if($movements->hasPages())
                    <div class="mt-3">
                        {!! $movements->links('vendor.pagination.bootstrap-5') !!}
                    </div>
                @endif
                
                <!-- Resumen -->
                <div class="mt-3 p-3 bg-light rounded">
                    <h6>Resumen:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Movimientos totales: {{ $movements->total() }}</small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                Mostrando {{ $movements->firstItem() ?? 0 }} - {{ $movements->lastItem() ?? 0 }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .form-control-plaintext {
        padding: 0.375rem 0;
        margin-bottom: 0;
        line-height: 1.5;
        background-color: transparent;
        border: solid transparent;
        border-width: 1px 0;
    }
    .table tr:hover {
        background-color: rgba(0,0,0,.02);
    }
</style>
@endpush