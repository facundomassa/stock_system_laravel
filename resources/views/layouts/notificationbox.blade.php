<div class="dropdown-center">
    <button class="btn btn-outline-light rounded-circle me-4 position-relative" 
            role="button" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            id="notificationDropdown">
        <i class='bi bi-bell-fill'></i>
        @if(Auth::user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ Auth::user()->unreadNotifications->count() }}
                <span class="visually-hidden">unread messages</span>
            </span>
        @endif
    </button>
    
    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-lg-end p-0" 
        style="min-width: 450px; max-height: 500px; overflow-y: auto;"
        aria-labelledby="notificationDropdown">
        
        <!-- Cabecera del dropdown -->
        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 bg-secondary">
            <div>
                <span class="fw-bold">Notificaciones</span>
                <span class="badge bg-danger ms-2">{{ Auth::user()->unreadNotifications->count() }} nuevas</span>
            </div>
            <div class="btn-group btn-group-sm">
                <!-- Form para marcar todas como leídas -->
                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline" id="markAllReadForm">
                    @csrf
                    <button type="submit" 
                            class="btn btn-outline-light btn-sm" 
                            title="Marcar todas como leídas">
                        <i class="bi bi-check-all"></i>
                    </button>
                </form>
                <!-- Form para eliminar todas -->
                <form action="{{ route('notifications.clearAll') }}" method="POST" class="d-inline" id="clearAllForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-outline-danger btn-sm" 
                            onclick="return confirm('¿Eliminar todas las notificaciones?')"
                            title="Eliminar todas">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </li>
        
        <!-- Lista de notificaciones -->
        @forelse(Auth::user()->notifications->take(10) as $notification)
            @php
                // Obtener datos de la notificación según la nueva estructura
                $notificationData = $notification->data;
                $isUnread = is_null($notification->read_at);
                
                // Determinar icono y color según tipo de notificación
                $icon = match($notificationData['type'] ?? 'stock_alert') {
                    'stock_alert' => match($notificationData['priority'] ?? 'medium') {
                        'high' => 'bi-exclamation-triangle-fill text-danger',
                        'medium' => 'bi-exclamation-circle-fill text-warning',
                        'low' => 'bi-info-circle-fill text-info',
                        default => 'bi-bell-fill text-secondary'
                    },
                    'refer_status' => 'bi-truck text-primary',
                    'system_alert' => 'bi-gear-fill text-light',
                    default => 'bi-bell-fill'
                };
                
                // Determinar badge de prioridad
                $priorityBadge = match($notificationData['priority'] ?? 'medium') {
                    'high' => '<span class="badge bg-danger ms-1">Alta</span>',
                    'medium' => '<span class="badge bg-warning ms-1 text-dark">Media</span>',
                    'low' => '<span class="badge bg-info ms-1">Baja</span>',
                    default => ''
                };
                
                // Formatear fecha
                $formattedTime = $notification->created_at->diffForHumans();
                $fullTime = $notification->created_at->format('d/m/Y H:i:s');
            @endphp
            
            <li class="notification-item {{ $isUnread ? 'bg-dark bg-opacity-25' : '' }}">
                <div class="dropdown-item px-3 py-2">
                    <div class="d-flex align-items-start">
                        <!-- Icono de la notificación -->
                        <div class="flex-shrink-0 me-3">
                            <i class="bi {{ $icon }} fs-5"></i>
                            @if($isUnread)
                                <div class="mt-1">
                                    <span class="badge bg-danger rounded-circle" style="width: 8px; height: 8px;"></span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Contenido de la notificación -->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <strong class="text-light">{{ $notificationData['article_name'] ?? 'Sistema de Stock' }}</strong>
                                    {!! $priorityBadge !!}
                                </div>
                                <small class="text-muted" title="{{ $fullTime }}">
                                    {{ $formattedTime }}
                                </small>
                            </div>
                            
                            <p class="mb-1 text-wrap">{{ $notificationData['message'] ?? 'Notificación del sistema' }}</p>
                            
                            <!-- Información adicional si existe -->
                            @if(isset($notificationData['stockcenter_name']) || isset($notificationData['current_quantity']))
                                <div class="text-muted small">
                                    @if(isset($notificationData['stockcenter_name']))
                                        <i class="bi bi-buildings me-1"></i>
                                        {{ $notificationData['stockcenter_name'] }}
                                    @endif
                                    
                                    @if(isset($notificationData['current_quantity']) && isset($notificationData['alert_quantity']))
                                        <span class="ms-2">
                                            <i class="bi bi-box me-1"></i>
                                            {{ $notificationData['current_quantity'] }}/{{ $notificationData['alert_quantity'] }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Acciones de la notificación -->
                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                @if(isset($notificationData['url']))
                                    <a href="{{ $notificationData['url'] }}" 
                                       class="btn btn-sm btn-outline-light">
                                        <i class="bi bi-eye me-1"></i> Ver detalles
                                    </a>
                                @else
                                    <span></span>
                                @endif
                                
                                <div class="btn-group btn-group-sm">
                                    @if($isUnread)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" 
                                          method="POST" 
                                          class="d-inline mark-as-read-form">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-outline-success btn-sm" 
                                                title="Marcar como leída">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                    
                                    <form action="{{ route('notifications.delete', $notification->id) }}" 
                                          method="POST" 
                                          class="d-inline delete-notification-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger btn-sm" 
                                                title="Eliminar notificación">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            
            <!-- Separador entre notificaciones -->
            @if(!$loop->last)
                <li><hr class="dropdown-divider my-0"></li>
            @endif
            
        @empty
            <li class="text-center py-4">
                <i class="bi bi-bell-slash display-5 text-muted mb-2"></i>
                <p class="text-muted mb-0">No tienes notificaciones</p>
            </li>
        @endforelse
        
        <!-- Footer del dropdown -->
        @if(Auth::user()->notifications->count() > 10)
            <li class="dropdown-footer bg-secondary px-3 py-2">
                <div class="text-center">
                    <a href="{{ route('notifications.index') }}" 
                       class="text-decoration-none text-light">
                        Ver todas las notificaciones 
                        <span class="badge bg-dark ms-1">
                            {{ Auth::user()->notifications->count() }}
                        </span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </li>
        @endif
    </ul>
</div>

<!-- Script para manejar acciones AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar formularios de notificaciones con AJAX
    const handleNotificationForm = async (form, action) => {
        try {
            const response = await fetch(form.action, {
                method: form.method,
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            });

            if (response.ok) {
                const result = await response.json();
                
                // Mostrar mensaje de éxito
                showNotificationToast(result.message || 'Acción completada', 'success');
                
                // Si fue marcar como leída, actualizar contador
                if (action === 'markAsRead' || action === 'markAllRead') {
                    updateNotificationCount();
                }
                
                // Si fue eliminar, remover el elemento del DOM
                if (action === 'delete') {
                    form.closest('.notification-item').remove();
                }
                
                // Si se marcaron todas como leídas, refrescar la lista
                if (action === 'markAllRead') {
                    setTimeout(() => location.reload(), 1000);
                }
                
                return true;
            } else {
                throw new Error('Error en la petición');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotificationToast('Error al procesar la acción', 'error');
            return false;
        }
    };

    // Función para mostrar toasts de notificación
    function showNotificationToast(message, type = 'info') {
        const toastId = 'notification-toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toast = new bootstrap.Toast(document.getElementById(toastId));
        toast.show();
        
        // Remover toast después de desaparecer
        setTimeout(() => {
            const toastElement = document.getElementById(toastId);
            if (toastElement) {
                toastElement.remove();
            }
        }, 5000);
    }

    // Crear contenedor de toasts si no existe
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1060';
        document.body.appendChild(container);
        return container;
    }

    // Actualizar contador de notificaciones
    function updateNotificationCount() {
        fetch('{{ route("notifications.count") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.badge.bg-danger');
                if (badge) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
    }

    // Asignar eventos a los formularios
    document.querySelectorAll('.mark-as-read-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await handleNotificationForm(form, 'markAsRead');
        });
    });

    document.querySelectorAll('.delete-notification-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (confirm('¿Eliminar esta notificación?')) {
                await handleNotificationForm(form, 'delete');
            }
        });
    });

    document.getElementById('markAllReadForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (confirm('¿Marcar todas las notificaciones como leídas?')) {
            await handleNotificationForm(e.target, 'markAllRead');
        }
    });

    document.getElementById('clearAllForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (confirm('¿Eliminar todas las notificaciones? Esta acción no se puede deshacer.')) {
            await handleNotificationForm(e.target, 'clearAll');
        }
    });

    // Actualizar notificaciones cada 30 segundos
    setInterval(updateNotificationCount, 30000);

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('notificationDropdown');
        const dropdownMenu = dropdown.nextElementSibling;
        
        if (!dropdown.contains(e.target) && !dropdownMenu.contains(e.target)) {
            const bsDropdown = bootstrap.Dropdown.getInstance(dropdown);
            if (bsDropdown && dropdownMenu.classList.contains('show')) {
                bsDropdown.hide();
            }
        }
    });
});
</script>

<!-- Estilos adicionales -->
<style>
.notification-item {
    transition: background-color 0.2s ease;
}

.notification-item:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
}

.notification-item .dropdown-item {
    border-left: 3px solid transparent;
}

.notification-item .dropdown-item:hover {
    background-color: transparent;
}

.notification-item[data-priority="high"] .dropdown-item {
    border-left-color: #dc3545;
}

.notification-item[data-priority="medium"] .dropdown-item {
    border-left-color: #ffc107;
}

.notification-item[data-priority="low"] .dropdown-item {
    border-left-color: #0dcaf0;
}

.dropdown-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

/* Estilos para notificaciones nuevas (no leídas) */
.notification-item .unread-indicator {
    width: 8px;
    height: 8px;
    background-color: #dc3545;
    border-radius: 50%;
    display: inline-block;
}

/* Scrollbar personalizado para el dropdown */
.dropdown-menu::-webkit-scrollbar {
    width: 6px;
}

.dropdown-menu::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.dropdown-menu::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.dropdown-menu::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}
</style>