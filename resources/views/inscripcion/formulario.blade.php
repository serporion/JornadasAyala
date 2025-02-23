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
            padding: 10px;
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

    <div class="container centered-container mt-5"  style="background-color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; padding: 20px; border-radius: 10px;">

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


        <h1 style="font-size: 2.5rem">Formulario de Inscripción</h1>

        <h2 style="text-align: center; font-size: 1.5rem">Seleccionar Eventos</h2>

        <form action="{{ route('inscripcion.store') }}" method="POST">
            @csrf


            <table class="table table-bordered table-padding">
                <thead>
                <tr>

                    <th>Tipo</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Lugar</th>
                    <th>Marcar</th>
                    <th>V</th>
                    <th>P</th>
                    <th>G</th>

                </tr>
                </thead>
                <tbody>


                @foreach ($eventos as $evento)
                    <tr>

                        <td>{{ ucfirst($evento->tipo) }}</td>
                        <td style="max-width: 120px">{{ $evento->nombre }}</td>
                        <td style="max-width: 550px; font-size: 0.8rem; ">{{ $evento->descripcion }}</td>
                        <td>{{ \Carbon\Carbon::parse($evento->fecha)->format('d M') }}</td>
                        <td>{{ \Carbon\Carbon::parse($evento->hora_inicio)->format('H:i') }}</td>
                        <td>{{ $evento->lugar }}</td>
                        <td>
                            <input type="checkbox" name="eventos[]" value="{{ $evento->id }}">
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="tipo_inscripcion_{{ $evento->id }}" value="virtual">
                            </label>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="tipo_inscripcion_{{ $evento->id }}" value="presencial">
                            </label>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="tipo_inscripcion_{{ $evento->id }}" value="gratuita">
                            </label>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div style="text-align: center; margin-top: 30px; margin-bottom: 30px">


                <div style="margin-top: 20px; font-size: 14px; margin-bottom: 30px">
                    <strong>Leyenda:</strong>
                    <p><strong>V:</strong> Evento virtual. 10 €</p>
                    <p><strong>P:</strong> Evento presencial. 20 € </p>
                    <p><strong>G:</strong> Evento gratuito (exclusivo para alumnos).</p>
                </div>

                @auth
                    <div>
                        <button type="submit" class="morado-boton">Inscríbete a los cursos</button>
                    </div>
                @else
                    <!-- Mostrar botón Iniciar sesión si NO hay usuario autenticado -->
                    <div>
                        <a href="{{ route('login') }}" class="morado-boton; text-decoration: none;">Inicia sesión</a>
                    </div>
                 @endauth

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const maxConferencias = 5; // Límite máximo de conferencias
                            const maxTalleres = 4; // Límite máximo de talleres

                            // Seleccionamos todos los checkboxes
                            document.querySelectorAll('input[type="checkbox"]').forEach(function (checkbox) {
                                // Añadimos un evento cuando cambie el estado (click del usuario)
                                checkbox.addEventListener('change', function () {
                                    // Filtrar y contar los seleccionados según su tipo
                                    const seleccionadosConferencias = document.querySelectorAll('input[type="checkbox"][data-tipo="conferencia"]:checked').length;
                                    const seleccionadosTalleres = document.querySelectorAll('input[type="checkbox"][data-tipo="taller"]:checked').length;

                                    // Validar el límite de conferencias
                                    if (seleccionadosConferencias > maxConferencias) {
                                        alert('Solo puedes seleccionar un máximo de 5 conferencias.');
                                        this.checked = false; // Desmarcar el último marcado
                                    }

                                    // Validar el límite de talleres
                                    if (seleccionadosTalleres > maxTalleres) {
                                        alert('Solo puedes seleccionar un máximo de 4 talleres.');
                                        this.checked = false; // Desmarcar el último marcado
                                    }
                                });
                            });
                        });
                    </script>


@endsection



