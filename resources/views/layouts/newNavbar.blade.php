<!-- layouts/newNavbar.blade.php -->
<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid px-3">

        <!-- Toggler + Brand -->
        <button class="navbar-toggler border-0 me-2 d-block" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"
            aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand fw-bold fs-5" href="{{ url('/home') }}">
            Stock System
        </a>

        <!-- Zona derecha: operación + notificaciones + usuario -->
        <div class="ms-auto d-flex align-items-center gap-3">

            <!-- Selector de operación -->
            @if(Auth::check() && Session::has('selected_operation'))
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle px-3 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-buildings me-1"></i>
                    {{ get_selected_operation() ?? 'Operación' }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    @foreach(get_allowed_operations() as $operation)
                    <li>
                        <form action="{{ url('operation-select') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item" name="selected_operation" value="{{ $operation->name }}">
                                {{ $operation->name }}
                            </button>
                        </form>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Nueva caja de movimientos -->
            @include('layouts.movementbox')

            <!-- Notificaciones -->
            @include('layouts.notificationbox')

            <!-- Menú de usuario -->
            @if (Auth::check())
            <div class="dropdown">
                <a class="text-light text-decoration-none d-flex align-items-center gap-2 dropdown-toggle" href="#" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-user-circle fs-4"></i>
                    <span class="d-none d-md-inline">
                        {{ Auth::user()->name }} {{ Auth::user()->surname ?? '' }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-log-out me-2"></i> Cerrar sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </div>
</nav>

<!-- Sidebar / Offcanvas (movido fuera del nav para evitar problemas de layout) -->
<div class="offcanvas offcanvas-start bg-dark text-bg-dark" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">
            Stock System
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <ul class="navbar-nav flex-column fs-5">

            <!-- Sección Dashboard -->
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('home') ? 'active' : '' }}" href="{{ url('/home') }}">
                    <i class="bx bx-tachometer me-2"></i> Dashboard
                </a>
            </li>

            <!-- Sección Gestión -->
            <li class="nav-item mt-2 border-top border-secondary pt-2">
                <div class="px-4 py-2 text-uppercase small fw-bold text-secondary">
                    Gestión
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('person') ? 'active' : '' }}" href="{{ url('/person') }}">
                    <i class="bx bx-group me-2"></i> Técnicos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('direction') ? 'active' : '' }}" href="{{ url('/direction') }}">
                    <i class="bx bx-map me-2"></i> Direcciones
                </a>
            </li>

            <!-- Sección Movimientos -->
            <li class="nav-item mt-3 border-top border-secondary pt-2">
                <div class="px-4 py-2 text-uppercase small fw-bold text-secondary">
                    Movimientos
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('movement') ? 'active' : '' }}" href="{{ url('/movement') }}">
                    <i class="bx bx-transfer me-2"></i> Movimientos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('transit') ? 'active' : '' }}" href="{{ url('/transit') }}">
                    <i class="bx bxs-truck me-2"></i> En Tránsito
                </a>
            </li>

            <!-- Sección Inventario -->
            <li class="nav-item mt-3 border-top border-secondary pt-2">
                <div class="px-4 py-2 text-uppercase small fw-bold text-secondary">
                    Inventario
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('article') ? 'active' : '' }}" href="{{ url('/article') }}">
                    <i class="bx bx-news me-2"></i> Artículos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('stockcenter') ? 'active' : '' }}" href="{{ url('/stockcenter') }}">
                    <i class="bx bx-buildings me-2"></i> Centros de Stock
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('stock') ? 'active' : '' }}" href="{{ url('/stock') }}">
                    <i class="bx bx-box me-2"></i> Stock
                </a>
            </li>

            <!-- Sección Documentos -->
            <li class="nav-item mt-3 border-top border-secondary pt-2">
                <div class="px-4 py-2 text-uppercase small fw-bold text-secondary">
                    Documentos
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('refer') ? 'active' : '' }}" href="{{ url('/refer') }}">
                    <i class="bx bx-file me-2"></i> Remitos
                </a>
            </li>

            <!-- Sección Admin -->
            @if (get_selected_operation() === 'ADMIN')
            <li class="nav-item mt-4 border-top border-secondary pt-3">
                <div class="px-4 py-2 text-uppercase small fw-bold text-secondary">
                    Administración
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('admin/users') ? 'active' : '' }}" href="{{ url('/admin/users') }}">
                    <i class="bx bx-shield me-2"></i> Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('operation') ? 'active' : '' }}" href="{{ url('/operation') }}">
                    <i class="bx bx-cog me-2"></i> Operaciones
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ request()->is('importar') ? 'active' : '' }}" href="{{ route('import.create') }}">
                    <i class="bx bx-upload me-2"></i> Importar CSV
                </a>
            </li>
            @endif

        </ul>
    </div>
</div>
