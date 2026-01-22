<!-- layouts/movementbox.blade.php -->
<div class="position-relative">
    <button class="btn btn-outline-light rounded-pill px-3 py-1 position-relative" 
            role="button" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            id="movementDropdown">
        <i class="bx bx-transfer"></i>
        @php
            $movementService = app(\App\Services\MovementService::class);
            $transit = $movementService->getTransitMovements(100); // Obtener todos para contar, pero paginar en lista
            $transitCount = $transit->total();
        @endphp
        @if($transitCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                {{ $transitCount > 99 ? '99+' : $transitCount }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" 
         aria-labelledby="movementDropdown"
         style="width: 50vw; max-height: 80vh; overflow-y: auto; border-radius: 12px;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Movimientos en Tránsito</h5>
            <div class="d-flex gap-2">
                <!-- Puedes agregar botones para acciones globales si es necesario, como refrescar o algo -->
            </div>
        </div>

        <!-- Lista de movimientos agrupados por refer -->
        <ul class="list-group list-group-flush">
            @forelse($transit as $referId => $group)
                @php
                    $refer = $group->first()->refer; // Asumiendo relación
                    $totalItems = $group->count();
                    $time = $group->first()->created_at->diffForHumans();
                @endphp

                <li class="list-group-item px-4 py-3 d-flex gap-3 align-items-start">
                    <i class="bx bxs-truck fs-4 text-primary mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between mb-1">
                            <strong>Remito #{{ $referId }}</strong>
                            <small class="text-muted">{{ $time }}</small>
                        </div>
                        <p class="mb-1 small">Estado: Emitido - {{ $totalItems }} ítems en tránsito</p>
                        @if($refer)
                            <div class="small text-muted">Desde: {{ $refer->nameOrigin ?? 'Desconocido' }} - Hacia: {{ $refer->nameDestiny ?? 'Desconocido' }}</div>
                        @endif
                        <div class="mt-2 d-flex gap-2">
                            <a href="{{ url('/refer/' . $referId) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i> Ver Detalles
                            </a>
                            <!-- Agrega más acciones si es necesario, como confirmar recepción -->
                        </div>
                    </div>
                    <span class="badge bg-warning text-dark rounded-pill ms-auto mt-2">Tránsito</span>
                </li>
            @empty
                <li class="list-group-item text-center py-5">
                    <i class="bx bx-transfer fs-1 text-muted"></i>
                    <p class="mt-3 mb-0">No hay movimientos en tránsito</p>
                </li>
            @endforelse
        </ul>

        <!-- Footer -->
        @if($transit->total() > $transit->perPage())
            <div class="px-4 py-3 border-top text-center">
                <a href="{{ url('/transit') }}" class="text-decoration-none">
                    Ver todos <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropdown = document.querySelector('#movementDropdown').nextElementSibling;
    // Puedes agregar funciones para actualizar conteo o acciones AJAX si es necesario
    // Por ejemplo, setInterval para refrescar conteo cada 30s
    const updateCount = () => {
        // Implementa fetch a una ruta que devuelva el count, similar a notifications
    };
    // setInterval(updateCount, 30000);
});
</script>

<!-- Estilos -->
<style>
.dropdown-menu {
    background-color: white !important;
    color: black !important;
}
.list-group-item {
    transition: background-color 0.2s;
}
.list-group-item:hover {
    background-color: #f8f9fa;
}
</style>