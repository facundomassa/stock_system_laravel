<nav class="sticky-top navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar"
            aria-controls="offcanvasDarkNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="dropdown-center">
            @if (Auth::check())
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
            @endif
            <a class="navbar-brand" href="/home">Stock System</a>
        </div>
        
        <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar"
            aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">
                    Menu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-start flex-grow-1 pe-3">
                    <li class="nav-item fs-4">
                        <a class="nav-link" aria-current="page" href="{{ url('/home') }}">Dashboard</a>
                    </li>
                    <li class="nav-item fs-4">
                        <a class="nav-link" aria-current="page" href="{{ url('/article') }}">Articulos</a>
                    </li>
                    <li class="nav-item fs-4">
                        <a class="nav-link" aria-current="page" href="{{ url('/direction') }}">Direcciones</a>
                    </li>
                    <li class="nav-item fs-4">
                        <a class="nav-link" aria-current="page" href="{{ url('/enterprise') }}">Operaciones</a>
                    </li>
                    <li class="nav-item fs-4">
                        <a class="nav-link" aria-current="page" href="{{ url('/movement') }}">Movimientos</a>
                    </li>
                    <li class="nav-item fs-4">
                        <a class="nav-link" aria-current="page" href="{{ url('/person') }}">Personas</a>
                    </li>
                    <li class="nav-item fs-4">
                        <a class="nav-link" aria-current="page" href="{{ url('/refer') }}">Remitos</a>
                    </li>
                    <li class="nav-item fs-4">
                        <a class="nav-link" aria-current="page" href="{{ url('/stockcenter') }}">Centros de Stock</a>
                    </li>
                    <li class="nav-item fs-4">
                        <a class="nav-link" aria-current="page" href="{{ url('/stock') }}">Stock</a>
                    </li>
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
