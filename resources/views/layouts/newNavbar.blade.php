<nav class="sticky-top navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar"
            aria-controls="offcanvasDarkNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#">Stock System</a>
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
                        <a class="nav-link" aria-current="page" href="{{ url('/home') }}">Home</a>
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
