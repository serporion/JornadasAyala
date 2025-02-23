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
            padding: 15px;
        }

        .ponente-image {
            max-width: 100px;
            height: auto;
            border-radius: 50%;
        }

        .morado-boton {
            background-color: #6f42c1;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .morado-boton:hover {
            background-color: #5a379a;
        }
    </style>

    <div class="container centered-container mt-5" style="background-color: rgba(255, 255, 255, 0.9); font-size: 0.9rem; padding: 20px; border-radius: 10px;">
        <h1 style="font-size: 2.5rem; text-align: center;">Lista de Ponentes</h1>

        <table class="table table-bordered table-padding">
            <thead>
            <tr>
                <th>Fotografía</th>
                <th>Nombre</th>
                <th>Área de Experiencia</th>
                <th>Red Social</th>
                <th>Eventos Asociados</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($ponentes as $ponente)
                <tr>
                    <!-- Mostrar la fotografía -->
                    <td>
                        <div class="flex justify-center">
                        @if ($ponente->fotografia)
                            <img src="{{ route('mostrar-imagen', ['filename' => $ponente->fotografia]) }}" class="max-h-20 h-1/2 rounded" alt="Imagen del Ponente">
                        @else
                            No disponible
                        @endif
                        </div>
                    </td>

                    <!-- Datos del ponente -->
                    <td>{{ $ponente->nombre }}</td>
                    <td>{{ $ponente->area_experiencia }}</td>

                    <!-- Red Social (validar si hay un enlace) -->
                    <td>
                        @if ($ponente->red_social)
                            <a href="{{ $ponente->red_social }}" target="_blank">Visitar</a>
                        @else
                            No disponible
                        @endif
                    </td>

                    <!-- Eventos Asociados -->
                    <td>
                        <ul>
                            @foreach ($ponente->eventos as $evento)
                                <li>{{ $evento->nombre }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No hay ponentes disponibles.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ url()->previous() }}" class="morado-boton">Volver</a>
        </div>
    </div>
@endsection
