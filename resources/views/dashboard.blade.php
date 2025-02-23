@extends('layouts.app')

@section('title', 'Jornadas de Videojuegos')

@section('header')
<div class="container mx-auto">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
</div>
@endsection

@section('content') <!-- Inicia la sección de contenido -->

@if (session('success'))
    <div class="max-w-4xl mx-auto mt-4">
        <div class="bg-green-500 text-white font-bold py-2 px-4 rounded">
            {{ session('success') }}
        </div>
    </div>
@endif

@if (session('error'))
    <div class="max-w-4xl mx-auto mt-4">
        <div class="bg-red-500 text-white font-bold py-2 px-4 rounded">
            {{ session('error') }}
        </div>
    </div>
@endif


<!-- Contenido principal del dashboard -->
<nav class="menu flex justify-center mt-8 space-x-4">
    <a href="#programa" class="text-blue-500 hover:text-blue-700 transition">Programa</a>
    <a href="#ponentes" class="text-blue-500 hover:text-blue-700 transition">Ponentes</a>
    <a href="{{ route('inscripcion.index') }}" class="text-blue-500 hover:text-blue-700 transition">Inscripción</a>
</nav>
<div class="py-12">
    <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mt-16 text-center">
            <h1 class="text-2xl font-extrabold text-white">Bienvenido a tu área</h1>
            <p class="mt-4 text-yellow-400 font-extrabold">Aquí puedes administrar tus datos y tu inscripción</p>

        </div>

    </div>
</div>

@endsection <!-- Fin de la sección -->
