<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Jornadas de Videojuegos - IES Francisco Ayala')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        /* Estilos base de Tailwind */
        @import url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');

        /* Estilos personalizados */
        .bg-jornadas {
            background-image: url('{{ asset("img/hero.jpeg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .menu a {
            color: #ffffff;
            font-weight: bold;
            padding: 0.5rem 1rem;
            text-decoration: none;
            transition: color 0.3s ease-in-out;
        }
        .menu a:hover {
            color: #FFD700; /* Color dorado al pasar el cursor */
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
        }
        footer {
            background-color: rgba(0, 0, 0, 0.7);
            color: #ffffff;
        }
        .content {
            min-height: 70vh; /* Espacio mínimo para el contenido */
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-jornadas">

<header>
    <nav class="menu flex justify-between items-center p-5 bg-gray-800 text-white">
        <!-- Logo o Título -->
        <h1 class="text-xl font-bold">
            <a href="{{ url('/') }}">Jornadas de Videojuegos</a>
        </h1>

        <div>

            @auth
                @include('layouts.navigation')

                @hasSection('hero')
                    <hero class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('hero')
                    </hero>
                @endif
            @else
                <a href="{{ route('login') }}" class="ml-4">Iniciar Sesión</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-4">Regístrate</a>
                @endif
            @endauth

        </div>
    </nav>
</header>



<div class="content">
    @yield('content')
</div>

<!-- Footer -->
<footer class="p-6 text-center">
    © {{ date('Y') }} IES Francisco Ayala - Jornadas de Videojuegos
</footer>
</body>
</html>
