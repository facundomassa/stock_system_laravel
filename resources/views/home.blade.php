@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @include('layouts.alert')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> Acciones Rápidas</h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <a href="{{ route('refer.create') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                        <i class="fas fa-plus-circle fa-2x mb-2"></i>
                        <span>Nuevo Remito</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="/" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                        <i class="fas fa-sliders-h fa-2x mb-2"></i>
                        <span>Ajuste de Stock</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="/" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                        <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                        <span>Inventario</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="/" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                        <span>Reporte Personalizado</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Sección de Prioridad Alta: Stock Negativo -->
    @if($negativeStocks->isNotEmpty())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="alert-heading mb-2">
                    <i class="fas fa-exclamation-triangle me-2"></i> Stock Negativo - Prioridad Alta
                </h5>
                <p class="mb-1">Los siguientes artículos tienen stock negativo:</p>
                <div class="row mt-2">
                    @foreach($negativeStocks as $stock)
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
    
    <!-- Resumen del Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Artículos</h6>
                            <h3 class="mb-0">{{ $summary['total_articles'] ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Stock Total</h6>
                            <h3 class="mb-0">{{ $summary['total_stock_value'] ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-database fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Alertas Activas</h6>
                            <h3 class="mb-0">{{ $summary['alert_stocks'] ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Remitos Pendientes</h6>
                            <h3 class="mb-0">{{ $summary['pending_refers'] ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-truck-loading fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenido Principal -->
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
                            @method('DELETE')
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
    .negative-stock-item {
        border-left: 4px solid #dc3545;
        padding-left: 1rem;
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
            const chartData = @json($chartData);
            
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

        // Manejar envío de formularios de notificaciones con confirmación
        const notificationForms = document.querySelectorAll('form[action*="notifications"]');
        
        notificationForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const action = this.getAttribute('action');
                
                if (action.includes('markAsRead') || action.includes('mark-all-read')) {
                    // Para marcar como leído, no necesitamos confirmación adicional
                    // a menos que ya esté especificada en el botón
                    return true;
                }
                
                // Para eliminar, ya tenemos confirmación en el botón
                return true;
            });
        });
        
        // Mostrar mensajes de éxito/error
        @if(session('success') || session('error') || session('info'))
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const alertInstance = new bootstrap.Alert(alert);
                setTimeout(() => {
                    alertInstance.close();
                }, 5000);
            });
        }, 1000);
        @endif
    });
</script>
@endpush