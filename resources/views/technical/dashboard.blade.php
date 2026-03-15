@extends('layouts.app')

@section('content')
    @include('layouts.newNavbar')

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold mb-0">Dashboard Técnico</h2>
                <p class="text-muted">Almacén Móvil Asignado: <strong>{{ $stockcenter->name }}</strong></p>
            </div>
        </div>

        @include('layouts.alert')

        <div class="row g-4">
            <!-- Stock Actual -->
            <div class="col-md-8 col-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold"><i class="fas fa-boxes me-2"></i>Mi Stock</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="table-light">Artículo</th>
                                        <th class="table-light">Código</th>
                                        <th class="table-light text-center">Cantidad</th>
                                        <th class="table-light text-center">Alerta</th>
                                        <th class="table-light">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stocks as $stock)
                                        <tr {{ $stock->warning ? "class=table-alert" : "class=bg-light" }}>
                                            <td>{{ $stock->article->name }}</td>
                                            <td>{{ $stock->article->code }}</span></td>
                                            <td class="text-center">
                                                {{ $stock->quantity }}
                                            </td>
                                            <td class="text-center">{{ $stock->quantity_alert ? $stock->quantity_alert : "-" }}
                                            </td>
                                            <td>
                                                <a class="btn btn-outline-dark py-0" href="{{ url('/stock/' . $stock->id) }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No hay stock disponible en este
                                                almacén.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $stocks->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>

            <!-- Alertas y Acciones -->
            <div class="col-md-4 col-12">
                <div class="row g-4">
                    <!-- Alertas -->
                    <div class="col-12">
                        <div class="card shadow-sm border-0 border-start border-danger border-4">
                            <div class="card-header bg-white border-bottom-0 pb-0">
                                <h6 class="card-title text-danger fw-bold"><i
                                        class="fas fa-exclamation-triangle me-2"></i>Alertas de Stock</h6>
                            </div>
                            <div class="card-body">
                                @if($alerts->count() > 0)
                                    <ul class="list-group list-group-flush">
                                        @foreach($alerts as $alert)
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                {{ $alert->article->name }}
                                                <span class="badge bg-danger rounded-pill">{{ $alert->quantity }} /
                                                    {{ $alert->quantity_alert }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-success mb-0"><i class="fas fa-check-circle me-1"></i>Stock en niveles
                                        óptimos.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="card-title mb-0 fw-bold"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h6>
                            </div>
                            <div class="card-body d-grid gap-3">
                                <a href="{{ route('technical.request.form') }}"
                                    class="btn btn-primary d-flex align-items-center justify-content-center py-3">
                                    <i class="fas fa-hand-holding-box fs-4 me-2"></i>
                                    <span>Solicitar Materiales</span>
                                </a>
                                <a href="{{ route('technical.consume.form') }}"
                                    class="btn btn-outline-success d-flex align-items-center justify-content-center py-3">
                                    <i class="fas fa-clipboard-check fs-4 me-2"></i>
                                    <span>Reportar Consumo del Día</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* Styling adjustments for mobile responsibility and modern look */
        .card {
            border-radius: 12px;
        }

        .table th {
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table-alert {
            background-color: #f8dbdb !important;
            border-left: 4px solid #dc3545;
        }

        .btn {
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
@endpush