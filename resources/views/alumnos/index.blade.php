@extends('adminlte::page')
@section('title', 'Alumnos')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Alumnos</h1>
@stop
@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Alumnos</h3>
                <div class="card-tools">
                    <a href="{{ route('alumnos.create') }}" class="btn btn-light btn-sm"><i class="fas fa-plus"></i> Agregar
                        nuevo</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="alumnos">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Cédula / Pasaporte</th>
                                <th>Foto</th>
                                <th>Nombre Completo</th>
                                <th>Maestria</th>
                                <th>Email Institucional</th>
                                <th>Sexo</th>
                                <th>Matriculas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alumnos as $alumno)
                                <tr>
                                    <td>{{ $alumno->dni }}</td>
                                    <td class="text-center">
                                        <img src="{{ asset($alumno->image) }}" alt="Imagen de {{ $alumno->name }}"
                                            style="max-width: 60px; border-radius: 50%;">
                                    </td>
                                    <td>
                                        {{ $alumno->nombre1 }}<br>
                                        {{ $alumno->nombre2 }}<br>
                                        {{ $alumno->apellidop }}<br>
                                        {{ $alumno->apellidom }}
                                    </td>
                                    <td>{{ $alumno->maestria->nombre }}</td>
                                    <td>{{ $alumno->email_institucional }}</td>
                                    <td>{{ $alumno->sexo }}</td>
                                    <td>
                                        <!-- Botón para abrir el modal -->
                                        <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal"
                                            data-target="#matriculasModal{{ $alumno->dni }}" title="Ver Matrícula">
                                            <i class="fas fa-eye"></i> <!-- Icono de ojo -->
                                        </button>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column align-items-center text-center">
                                            @can('dashboard_admin')
                                                <div class="mb-2">
                                                    <a href="{{ route('alumnos.edit', $alumno->dni) }}"
                                                        class="btn btn-outline-primary btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            @endcan

                                            <div class="d-flex flex-row">
                                                @if ($alumno->maestria->cohorte->count() > 0 && $alumno->matriculas->count() == 0)
                                                    <div class="mr-2">
                                                        @php
                                                            $alumnoDNI = $alumno->dni;
                                                        @endphp
                                                        <a href="{{ url('/matriculas/create', $alumnoDNI) }}"
                                                            class="btn btn-outline-success btn-sm" title="Matricular">
                                                            <i class="fas fa-plus-circle"></i>
                                                        </a>
                                                    </div>
                                                @endif

                                                <div class="align-self-center mb-2">
                                                    @if (
                                                        $alumno->notas->count() > 0 &&
                                                            $alumno->maestria->asignaturas->count() > 0 &&
                                                            $alumno->notas->count() == $alumno->maestria->asignaturas->count())
                                                        <a href="{{ route('record.show', $alumno->dni) }}"
                                                            class="btn btn-outline-warning btn-sm" title="Record Académico">
                                                            <i class="fas fa-file-alt"></i>
                                                        </a>
                                                    @endif
                                                </div>

                                                @if (
                                                    $alumno->notas->count() > 0 &&
                                                        $alumno->maestria->asignaturas->count() > 0 &&
                                                        $alumno->notas->count() == $alumno->maestria->asignaturas->count())
                                                    <div class="ml-2">
                                                        <a href="#" class="btn btn-outline-danger btn-sm"
                                                            title="Titulación">
                                                            <!-- Agrega aquí el icono adecuado para la titulación -->
                                                            <i class="fas fa-graduation-cap"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>

                                            @can('dashboard_admin')
                                                <div class="mt-2">
                                                    @php
                                                        $alumnoDNI = $alumno->dni;
                                                    @endphp
                                                    <a href="{{ url('/notas/create', $alumnoDNI) }}"
                                                        class="btn btn-outline-info btn-sm" title="Calificar">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    </div>

    </div>

    @include('alumnos.modals')
@stop

@section('js')
    <script>
        $('#alumnos').DataTable({
            paging: true, // Asegúrate de que la paginación esté habilitada
            lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100],
            pageLength: {{ $perPage }},
            responsive: true,
            colReorder: true,
            keys: true,
            autoFill: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
    </script>
@stop
