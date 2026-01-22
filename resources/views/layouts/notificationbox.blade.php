<!-- layouts/notificationbox.blade.php -->
<div class="position-relative">
    <button class="btn btn-outline-light rounded-pill px-3 py-1 position-relative" 
            type="button" 
            id="notificationButton" 
            data-bs-toggle="dropdown" 
            aria-expanded="false">
        <i class="bi bi-bell-fill m-0"></i>
        @if(Auth::user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ Auth::user()->unreadNotifications->count() > 99 ? '99+' : Auth::user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" 
         aria-labelledby="notificationButton"
         style="width: 50vw; max-height: 80vh; overflow-y: auto; border-radius: 12px;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Notificaciones</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" 
                        id="markAllReadBtn" 
                        title="Marcar todas como leídas">
                    <i class="bi bi-check-all"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" 
                        id="clearAllBtn" 
                        title="Eliminar todas">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <!-- Lista de notificaciones -->
        <ul class="list-group list-group-flush">
            @forelse(Auth::user()->notifications->take(20) as $notification)
                @php
                    $data = $notification->data;
                    $unread = !$notification->read_at;
                    $type = $data['type'] ?? 'info';
                    $icon = match($type) {
                        'stock_alert' => 'bi-box',
                        'refer_status' => 'bi-file-earmark-text',
                        'system_alert' => 'bi-gear',
                        default => 'bi-info-circle'
                    };
                    $color = match($data['priority'] ?? 'medium') {
                        'high' => 'text-danger',
                        'medium' => 'text-warning',
                        'low' => 'text-info',
                        default => 'text-primary'
                    };
                    $time = $notification->created_at->diffForHumans();
                @endphp

                <li class="list-group-item px-4 py-3 {{ $unread ? 'bg-light' : '' }} d-flex gap-3 align-items-start" 
                    data-id="{{ $notification->id }}">
                    <i class="bi {{ $icon }} fs-4 {{ $color }} mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between mb-1">
                            <strong>{{ $data['title'] ?? 'Notificación' }}</strong>
                            <small class="text-muted">{{ $time }}</small>
                        </div>
                        <p class="mb-1 small">{{ $data['message'] ?? '' }}</p>
                        @if(isset($data['details']))
                            <div class="small text-muted">{{ $data['details'] }}</div>
                        @endif
                        <div class="mt-2 d-flex gap-2">
                            @if(isset($data['url']))
                                <a href="{{ $data['url'] }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                            @endif
                            @if($unread)
                                <button class="btn btn-sm btn-outline-success markReadBtn">
                                    <i class="bi bi-check"></i>
                                </button>
                            @endif
                            <button class="btn btn-sm btn-outline-danger deleteBtn">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    @if($unread)
                        <span class="badge bg-primary rounded-pill ms-auto mt-2">Nuevo</span>
                    @endif
                </li>
            @empty
                <li class="list-group-item text-center py-5">
                    <i class="bi bi-bell-slash fs-1 text-muted"></i>
                    <p class="mt-3 mb-0">No hay notificaciones</p>
                </li>
            @endforelse
        </ul>

        <!-- Footer -->
        @if(Auth::user()->notifications->count() > 20)
            <div class="px-4 py-3 border-top text-center">
                <a href="{{ route('notifications.index') }}" class="text-decoration-none">
                    Ver todas <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Forms ocultos para acciones globales -->
<form id="markAllReadForm" action="{{ route('notifications.markAllRead') }}" method="POST" class="d-none">
    @csrf
</form>
<form id="clearAllForm" action="{{ route('notifications.clearAll') }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropdown = document.querySelector('#notificationButton').nextElementSibling;
    const updateCount = () => {
        fetch('{{ route("notifications.count") }}')
            .then(res => res.json())
            .then(data => {
                const badge = document.querySelector('#notificationButton .badge');
                if (badge) {
                    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.style.display = data.unread_count ? '' : 'none';
                }
            });
    };

    const handleAction = async (url, method = 'POST', body = {}) => {
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: method !== 'GET' ? JSON.stringify(body) : null
        });
        if (res.ok) {
            updateCount();
            return await res.json();
        }
        throw new Error('Error en la acción');
    };

    // Mark all read
    document.getElementById('markAllReadBtn')?.addEventListener('click', async () => {
        if (confirm('¿Marcar todas como leídas?')) {
            await handleAction('{{ route("notifications.markAllRead") }}');
            location.reload();
        }
    });

    // Clear all
    document.getElementById('clearAllBtn')?.addEventListener('click', async () => {
        if (confirm('¿Eliminar todas?')) {
            await handleAction('{{ route("notifications.clearAll") }}', 'DELETE');
            location.reload();
        }
    });

    // Individual actions
    dropdown.querySelectorAll('.markReadBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const item = btn.closest('.list-group-item');
            const id = item.dataset.id;
            await handleAction(`{{ url('notifications') }}/${id}/read`);
            item.classList.remove('bg-light');
            btn.remove();
            item.querySelector('.badge.bg-primary')?.remove();
        });
    });

    dropdown.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (confirm('¿Eliminar?')) {
                const item = btn.closest('.list-group-item');
                const id = item.dataset.id;
                await handleAction(`{{ url('notifications') }}/${id}`, 'DELETE');
                item.remove();
            }
        });
    });

    setInterval(updateCount, 30000);
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