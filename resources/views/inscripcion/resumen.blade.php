@extends('layouts.app')

@section('content')

    <style>

        .centered-container {
            display: flex;
            justify-content: center;
            flex-direction: column;
            position: absolute;
            top: 100px;
            left: 50%;
            transform: translate(-50%, 0);
        }
        .table-padding td {
            padding: 10px;
        }

        .table-padding th {
            padding: 10px; /* Opcional */
        }

        .morado-boton {
            background-color: #6f42c1; /* Color morado */
            color: white; /* Texto en blanco */
            border: none; /* Quitar bordes predeterminados */
            padding: 10px 20px; /* Espaciado interno */
            border-radius: 5px; /* Bordes ligeramente redondeados */
            font-size: 1rem; /* Tamaño del texto */
            cursor: pointer; /* Cambiar cursor al pasar sobre el botón */
        }

        .morado-boton:hover {
            background-color: #5a379a; /* Morado más oscuro al pasar el mouse */
        }

    </style>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 border border-red-400 px-4 py-3 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container centered-container" style="background-color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; padding: 20px; border-radius: 10px; margin-top: 20px">
        <h1 style="font-size: 2.5rem">Resumen de Inscripciones</h1>
        <div style="text-align: right;">
            <a href="{{ route('inscripcion.index') }}" class="morado-boton">Volver</a>
        </div>


        <h2 style="text-align: center; font-size: 1.5rem">Evento(s) Seleccionado(s)</h2>

        <table class="table table-bordered table-padding" >
            <thead>
            <tr>
                <th>Tipo</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Lugar</th>
                <th>Coste</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($eventos as $evento)
                <tr>
                    <td>{{ ucfirst($evento->tipo) }}</td>
                    <td style="max-width: 120px">{{ $evento->nombre }}</td>
                    <td style="max-width: 700px; font-size: 0.8rem;">{{ $evento->descripcion }}</td>
                    <td>{{ \Carbon\Carbon::parse($evento->fecha)->format('d M') }}</td>
                    <td>{{ \Carbon\Carbon::parse($evento->hora_inicio)->format('H:i') }}</td>
                    <td>{{ $evento->lugar }}</td>
                    <td>{{ $evento->costo > 0 ? '$' . number_format($evento->costo, 2) : 'Gratuito' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
            <h3>Coste Total de Inscripción: {{ number_format($totalCoste, 2) }} €</h3>
        </div>

        <form action="{{ route('inscripcion.confirmacion') }}" method="POST" >
            @csrf
            <input type="hidden" name="eventos" value="{{ json_encode($eventos->pluck('id')) }}">
            <input type="hidden" name="tipo_inscripcion" value="{{ $tipo_inscripcion }}">

            <div style="text-align: center;">
                <button type="submit" class="morado-boton">Confirmar Inscripción</button>
            </div>

        </form>

    </div>

@endsection

