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

    <div class="container centered-container"  style="background-color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; padding: 20px; border-radius: 10px;">
        <h1>Formulario de Inscripción</h1>


        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->has('eventos'))
            <div class="alert alert-danger">
                {{ $errors->first('eventos') }}
            </div>
        @endif

        <form action="{{ route('inscripcion.store') }}" method="POST">
            @csrf

            <h2>Seleccionar Eventos</h2>

            <table class="table table-bordered table-padding">
                <thead>
                <tr>

                    <th>Tipo</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Lugar</th>
                    <th>Seleccionar</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($eventos as $evento)
                    <tr>

                        <td>{{ ucfirst($evento->tipo) }}</td>
                        <td>{{ $evento->nombre }}</td>
                        <td style="max-width: 700px; font-size: 0.8rem; ">{{ $evento->descripcion }}</td>
                        <td>{{ \Carbon\Carbon::parse($evento->fecha)->format('d M') }}</td>
                        <td>{{ \Carbon\Carbon::parse($evento->hora_inicio)->format('H:i') }}</td>
                        <td>{{ $evento->lugar }}</td>
                        <td>
                            <input type="checkbox" name="eventos[]" value="{{ $evento->id }}">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div style="text-align: center; margin-top: 30px; margin-bottom: 30px">

                @auth
                    <h3>Seleccionar tipo de inscripción</h3>

                    <label style="margin-right: 20px;">
                        <input type="radio" name="tipo_inscripcion" value="virtual" required> Virtual
                    </label>
                    <label style="margin-right: 20px;">
                        <input type="radio" name="tipo_inscripcion" value="presencial"> Presencial
                    </label>
                    <label>
                        <input type="radio" name="tipo_inscripcion" value="gratuita"> Gratuita (Soy alumno)
                    </label>

                    <div>
                        <button type="submit" class="morado-boton" style="margin-top: 20px;">Inscribirse</button>
                    </div>
                @else
                    <!-- Mostrar botón Iniciar sesión si NO hay usuario autenticado -->
                    <div>
                        <a href="{{ route('login') }}" class="morado-boton" style="margin-top: 50px; text-decoration: none;">Inicia sesión</a>
                    </div>
                 @endauth



@endsection

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

