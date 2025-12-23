<nav class="sticky-top navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar"
            aria-controls="offcanvasDarkNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        

        <div class="d-flex flex-row">
            <div class="dropdown-center">
                <button class="btn btn-outline-light me-4 position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                
                {{get_selected_operation()}}
                </button>
                <ul class="dropdown-menu">
                    <form action="{{ url('operation-select') }}" method="POST">
                        @csrf <!-- Protección CSRF -->
                    @foreach(get_allowed_operations() as $operation)
                        <li>            
                            <button type="submit" class="btn btn-outline-secondary" name="selected_operation" value="{{ $operation->name }}">
                                Seleccionar Operación: {{ $operation->name }}
                            </button>
                        </li>
                        @endforeach
                    </form> 
                </ul>
                
            </div>
            <div class="dropdown-center">
                <button class="btn btn-outline-light rounded-circle me-4 position-relative" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class='bi bi-bell-fill'></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{Auth::user()->unreadNotifications->count();}}
                        
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </button>
                    
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-lg-end">
                    @if (Auth::user()->notifications->isEmpty())
                    <li><p class="dropdown-item text-wrap text-muted" style="width: 30rem;">No tiene alertas.</p></li>
                    @else
                    @foreach (Auth::user()->notifications as $notification)
                        <li><p class="dropdown-item text-wrap" style="width: 30rem;">{{$notification->data['menssage']}}</p></li>
                    @endforeach
                    @endif
                </ul>
                
                
            </div>
            <a class="navbar-brand" href="/home">Stock System</a>
        </div>
        <div class="offcanvas offcanvas-start bg-black" tabindex="-1" id="offcanvasDarkNavbar"
            aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">
                    Menu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <ul class="navbar-nav justify-content-start flex-grow-1">
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('home') ? 'active' : '' }}" href="{{ url('/home') }}">
                            <i class='bx bx-tachometer'></i> Dashboard
                        </a>
                    </li>
                    <!-- Sección: Personas y Direcciones -->
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('person') ? 'active' : '' }}" href="{{ url('/person') }}">
                            <i class='bx bx-group'></i> Personas
                        </a>
                    </li>     
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('direction') ? 'active' : '' }}" href="{{ url('/direction') }}">
                            <i class='bx bx-map'></i> Direcciones
                        </a>
                    </li>   
                    <!-- Sección: Movimientos -->
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('movement') ? 'active' : '' }}" href="{{ url('/movement') }}">
                            <i class='bx bx-transfer'></i> Movimientos
                        </a>
                    </li>
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('transit') ? 'active' : '' }}" href="{{ url('/transit') }}">
                            <i class='bx bxs-truck'></i> En Tránsito
                        </a>
                    </li>
                    <!-- Sección: Inventario -->
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('article') ? 'active' : '' }}" href="{{ url('/article') }}">
                            <i class='bx bx-news'></i> Artículos
                        </a>
                    </li>
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('stockcenter') ? 'active' : '' }}" href="{{ url('/stockcenter') }}">
                            <i class='bx bx-buildings'></i> Centros de Stock
                        </a>
                    </li>
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('stock') ? 'active' : '' }}" href="{{ url('/stock') }}">
                            <i class='bx bx-box'></i> Stock
                        </a>
                    </li>
                    <!-- Sección: Documentos -->
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('refer') ? 'active' : '' }}" href="{{ url('/refer') }}">
                            <i class='bx bx-file'></i> Remitos
                        </a>
                    </li>
                    <!-- Sección: Documentos -->
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('refer') ? 'active' : '' }}" href="{{ url('/refer') }}">
                            <i class='bx bx-line-chart'></i> Reportes
                        </a>
                    </li>
                    <!-- Sección para Admin -->
                    @if (get_selected_operation() == 'admin')
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('admin/users') ? 'active' : '' }}" href="{{ url('/admin/users') }}">
                            <i class='bx bx-shield'></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item fs-4 bg-dark">
                        <a class="nav-link px-3 {{ request()->is('operation') ? 'active' : '' }}" href="{{ url('/operation') }}">
                            <i class='bx bx-cog'></i> Operaciones
                        </a>
                    </li>
                    @endif  
                </ul>
            </div>
            <div class="offcanvas-header">
                <ul class="navbar-nav justify-content-start flex-grow-1 pe-3">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <div class="dropdown-menu dropdown-menu-end bottom-top" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>

                            <a id="navbarDropdown" class="nav-link dropdown-toggle fs-4" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                                {{ Auth::user()->surname }}
                            </a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </div>
</nav>
