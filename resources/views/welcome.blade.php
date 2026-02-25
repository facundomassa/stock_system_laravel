<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StockSystem - Control de Inventario</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(-45deg, #0d1127, #1a203c, #2d3748, #4a5568);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="antialiased text-white gradient-bg">
    <div class="relative min-h-screen flex flex-col items-center justify-center">
        @if (Route::has('login'))
            <div class="absolute top-0 right-0 p-6 text-right">
                @auth
                    <a href="{{ url('/home') }}" class="font-semibold text-gray-300 hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Home</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-300 hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-300 hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <h1 class="text-5xl font-bold mb-4">StockSystem</h1>
            </div>

            <div class="mt-8 text-center">
                <p class="text-xl text-gray-400">Sistema interno de control de inventario y stock multi-almacén, diseñado para empresas con equipos técnicos en campo.</p>
                <p class="mt-2 text-sm text-gray-500">Actualmente el sistema no maneja valores monetarios, solo cantidades físicas de stock.</p>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                    <div class="scale-100 p-6 bg-gray-800/50 bg-gradient-to-bl from-gray-700/50 via-transparent to-gray-700/50 rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250">
                        <div>
                            <h2 class="mt-6 text-xl font-semibold text-white">Estado del Proyecto</h2>
                            <ul class="mt-4 text-gray-400 text-sm leading-relaxed list-disc list-inside">
                                <li>Gestión de Artículos: Funcional</li>
                                <li>Stock por almacén: Funcional</li>
                                <li>Movimientos (entradas/salidas/traslados): Funcional</li>
                                <li>Movimientos en tránsito: Funcional</li>
                                <li>Remitos: Funcional</li>
                                <li>Usuarios y permisos: Funcional</li>
                                <li>Filtro por operación/empresa: Parcial</li>
                                <li>Módulo Técnicos: Pendiente</li>
                                <li>Reportes básicos: Parcial</li>
                            </ul>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-gray-800/50 bg-gradient-to-bl from-gray-700/50 via-transparent to-gray-700/50 rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250">
                        <div>
                            <h2 class="mt-6 text-xl font-semibold text-white">Roadmap</h2>
                            <ul class="mt-4 text-gray-400 text-sm leading-relaxed list-disc list-inside">
                                <li>Implementar scope global para filtrar por operación/empresa.</li>
                                <li>Desarrollar módulo completo para Técnicos (solicitudes, consumos, validación).</li>
                                <li>Mejorar UI/UX.</li>
                                <li>Agregar reportes avanzados.</li>
                                <li>Implementar tests unitarios y de características.</li>
                                <li>Documentación técnica detallada.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-16 px-6 sm:items-center sm:justify-between">
                <div class="text-center text-sm sm:text-left">
                    &nbsp;
                </div>
                <div class="text-center text-sm text-gray-400 sm:text-right sm:ml-0">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </div>
            </div>
        </div>
    </div>
</body>
</html>
