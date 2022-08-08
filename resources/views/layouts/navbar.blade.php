<div class="container">

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left Side Of Navbar -->
        <ul class="navbar-nav me-auto">
            <!-- Navigation -->
            <nav id="slide-menu">
                <ul>
                    <li><a href="{{ url('/article') }}">Articulos</a></li>
                    <li><a href="{{ url('/direction') }}">Direcciones</a></li>
                    <li><a href="{{ url('/enterprise') }}">Operaciones</a></li>
                    <li><a href="{{ url('/movement') }}">Movimientos</a></li>
                    <li><a href="{{ url('/permission') }}">Permisos</a></li>
                    <li><a href="{{ url('/person') }}">Personas</a></li>
                    <li><a href="{{ url('/refer') }}">Remitos</a></li>
                    <li><a href="{{ url('/stockcenter') }}">Centros de Stock</a></li>
                    <li><a href="{{ url('/stock') }}">Stock</a></li>
                </ul>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto buttom-zero">
                    <!-- Authentication Links -->
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
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                                {{ Auth::user()->surname }}
                            </a>

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
                        </li>
                    @endguest
                </ul>
            </nav>

            <!-- Content panel -->

        </ul>


    </div>
</div>
