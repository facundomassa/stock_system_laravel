@if($notifications->isEmpty())
<div class="text-center py-4">
    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
    <p class="text-muted mb-0">No tienes alertas pendientes.</p>
</div>
@else
<div class="list-group">
    @foreach($notifications as $notification)
    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">
                        <i class="fas fa-exclamation-circle me-2 text-{{ $notification->read_at ? 'secondary' : 'danger' }}"></i>
                        {{ $notification->data['message'] ?? $notification->data['menssage'] ?? 'Alerta' }}
                    </h6>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        {{ $notification->created_at->diffForHumans() }}
                        @if($notification->read_at)
                        · <i class="fas fa-check text-success me-1"></i> Leída
                        @endif
                    </small>
                </div>
                <div class="btn-group btn-group-sm">
                    @if(!$notification->read_at)
                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm" title="Marcar como leída">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                               onclick="return confirm('¿Eliminar esta notificación?')"
                               title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif