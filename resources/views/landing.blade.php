@extends('layouts.app')

@section('title', 'Jornadas de Videojuegos')

@section('content') <!-- Inicia la sección de contenido -->
<div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-gray-900 bg-opacity-50 selection:bg-red-500 selection:text-white">
    <div class="max-w-7xl mx-auto p-6 lg:p-8">
        <!-- Encabezado -->
        <div class="flex justify-center">
            <h1 class="text-5xl font-bold text-white drop-shadow-lg">Jornadas de Videojuegos</h1>
        </div>

        <!-- Navegación -->
        <nav class="menu flex justify-center mt-8 space-x-4">
            <a href="#programa">Programa</a>
            <a href="{{ route('ponentes.index') }}">Ponentes</a>
            <a href="{{ route('inscripcion.index') }}">Inscripción</a>
        </nav>
        <div class="mt-8 flex justify-center">
            <a href="{{ route('register') }}" class="bg-yellow-500 hover:bg-yellow-600 text-violet-900 py-2 px-4 rounded">
                Regístrate ahora
            </a>
        </div>
        <!-- Contenido principal -->
        <div class="mt-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Conferencias -->
                <a href="#" class="relative group scale-100 p-6 bg-gray-900/80 backdrop-blur-lg border border-purple-500/50 rounded-lg shadow-2xl shadow-purple-500/30 flex motion-safe:hover:scale-[1.05] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
                    <div class="card">
                        <h2 class="text-2xl font-bold text-black group-hover:text-purple-300 transition duration-300 drop-shadow-lg">
                            Conferencias
                        </h2>
                        <p class="mt-4 text-black-200 drop-shadow-md">
                            Asiste a conferencias impartidas por expertos de la industria del videojuego.
                        </p>
                    </div>
                </a>

                <!-- Talleres -->
                <a href="#" class="relative group scale-100 p-6 bg-gray-900/80 backdrop-blur-lg border border-blue-500/50 rounded-lg shadow-2xl shadow-blue-500/30 flex motion-safe:hover:scale-[1.05] transition-all duration-250 focus:outline focus:outline-2 focus:outline-blue-500">
                    <div class="card">
                        <h2 class="text-2xl font-bold text-black group-hover:text-blue-300 transition duration-300 drop-shadow-lg">
                            Talleres
                        </h2>
                        <p class="mt-4 text-black-200 drop-shadow-md">
                            Participa en talleres prácticos y mejora tus habilidades en desarrollo de videojuegos.
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection <!-- Fin de la sección -->
