@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @include('layouts.alert')
    
    <!-- Sección de Prioridad Alta: Stock Negativo -->
    @if($negative_stocks->isNotEmpty())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="alert-heading mb-2">
                    <i class="fas fa-exclamation-triangle me-2"></i> Stock Negativo - Prioridad Alta
                </h5>
                <p class="mb-1">Los siguientes artículos tienen stock negativo:</p>
                <div class="row mt-2">
                    @foreach($negative_stocks as $stock)
                    <div class="col-md-4 mb-2">
                        <div class="card bg-danger text-white">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="d-block">
                                            <i class="fas fa-warehouse me-1"></i> 
                                            {{ $stock->stockCenter->name ?? 'N/A' }}
                                        </small>
                                        <strong class="d-block">{{ $stock->article->name ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-danger fs-6">
                                            {{ $stock->quantity }}
                                        </span>
                                        <br>
                                        <small>Stock Negativo</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endif
    
    <!-- Nuevos KPIs -->
    <div class="row mb-4">
        <!-- Tasa de Rotación -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tasa de Rotación
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $rotation['rotation_rate'] }} veces
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="{{ $rotation['trend'] > 0 ? 'text-success' : ($rotation['trend'] < 0 ? 'text-danger' : 'text-muted') }} mr-2">
                                    <i class="fas fa-arrow-{{ $rotation['trend'] > 0 ? 'up' : ($rotation['trend'] < 0 ? 'down' : 'right') }}"></i> 
                                    {{ abs($rotation['trend']) }}%
                                </span>
                                <span>vs mes anterior</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Eficiencia de Despacho -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Eficiencia Despacho
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $dispatch_efficiency['avg_dispatch_time'] }}h
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="{{ $dispatch_efficiency['trend'] > 0 ? 'text-success' : ($dispatch_efficiency['trend'] < 0 ? 'text-danger' : 'text-muted') }} mr-2">
                                    <i class="fas fa-arrow-{{ $dispatch_efficiency['trend'] > 0 ? 'down' : ($dispatch_efficiency['trend'] < 0 ? 'up' : 'right') }}"></i> 
                                    {{ abs($dispatch_efficiency['trend']) }}%
                                </span>
                                <span>vs mes anterior</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stock Muerto -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Stock Sin Movimiento
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $dead_stock->count() }}
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span>Últimos 6 meses</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-skull-crossbones fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Resumen del Resumen Existente -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Alertas Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['alert_stocks'] ?? 0 }}
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-danger mr-2">
                                    <i class="fas fa-exclamation-circle"></i> 
                                    {{ $negative_stocks->count() }} negativos
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenido Principal -->
    <div class="row">
        <!-- Sección de Recomendaciones -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2 text-warning"></i> 
                        Recomendaciones del Sistema
                    </h5>
                    <span class="badge bg-warning">{{ count($recommendations) }}</span>
                </div>
                <div class="card-body">
                    @if(empty($recommendations))
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted mb-0">¡Todo en orden! No hay recomendaciones pendientes.</p>
                    </div>
                    @else
                    <div class="list-group">
                        @foreach($recommendations as $rec)
                        <div class="list-group-item list-group-item-{{ $rec['type'] }} mb-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-{{ $rec['icon'] }} fa-2x me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $rec['title'] }}</h6>
                                    <p class="mb-2">{{ $rec['message'] }}</p>
                                    @if($rec['action'])
                                    <a href="{{ $rec['action_url'] }}" class="btn btn-sm btn-{{ $rec['type'] }}">
                                        {{ $rec['action'] }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sección de Stock Muerto -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-skull-crossbones me-2 text-danger"></i> 
                        Stock Sin Movimiento (Últimos 6 meses)
                    </h5>
                    <span class="badge bg-danger">{{ $dead_stock->count() }}</span>
                </div>
                <div class="card-body">
                    @if($dead_stock->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted mb-0">¡Excelente! No hay stock sin movimiento.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Centro</th>
                                    <th>Cantidad</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dead_stock as $stock)
                                <tr>
                                    <td>
                                        <a href="{{ route('article.show', $stock->article->id ?? '#') }}" 
                                           class="text-decoration-none">
                                            {{ $stock->article->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $stock->stockCenter->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-warning">{{ $stock->quantity }}</span>
                                    </td>
                                    <td>{{ $stock->article->type ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Segunda Fila: Alertas y Reportes -->
    <div class="row">
        <!-- Sección de Alertas -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i> Alertas y Notificaciones</h5>
                    @if($notifications->isNotEmpty())
                    <div class="btn-group btn-group-sm">
                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary" 
                                   onclick="return confirm('¿Marcar todas como leídas?')">
                                <i class="fas fa-check-double"></i> Marcar todas
                            </button>
                        </form>
                        <form action="{{ route('notifications.clearAll') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger" 
                                   onclick="return confirm('¿Eliminar todas las notificaciones?')">
                                <i class="fas fa-trash"></i> Limpiar
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    @include('home.alerts')
                </div>
            </div>
        </div>
        
        <!-- Sección de Reportes -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Reportes y Estadísticas</h5>
                </div>
                <div class="card-body">
                    @include('home.reports')
                </div>
            </div>
        </div>
    </div>
    <div id="chart-container" data-chart='@json($chartData)'></div>
    <!-- Gráficos (si hay datos) -->
    @if(!empty($chartData['labels']) && count($chartData['labels']) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Movimientos Recientes</h5>
                    <small class="text-muted">Últimos 30 días</small>
                </div>
                <div class="card-body">
                    <canvas id="movementsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de movimientos
        const ctx = document.getElementById('movementsChart');
        if (ctx) {
            const element = document.getElementById('chart-container');
            const chartData = JSON.parse(element.dataset.chart);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Cantidad Movida',
                        data: chartData.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Cantidad: ${context.raw.toLocaleString()}`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Auto-submit para filtros de fecha
        const dateInputs = document.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            input.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endpush
