@extends('adminlte::page')
@section('title', 'Docentes')
@section('content_header')
    <h1><i class="fas fa-chalkboard"></i> Gestión de Docentes</h1>
@stop
@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Docentes</h3>
                <div class="card-tools">
                    <a href="{{ route('docentes.create') }}" class="btn btn-light btn-sm"><i class="fas fa-plus"></i>
                        Agregar nuevo</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="docentes">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Cédula / Pasaporte</th>
                                <th>Foto</th>
                                <th>Nombre completo</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Asignaturas</th>
                                <th>Cohortes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($docentes as $docente)
                                <tr>
                                    <td>{{ $docente->dni }}</td>
                                    <td class="text-center">
                                        <img src="{{ asset($docente->image) }}" alt="Imagen de {{ $docente->name }}"
                                            style="max-width: 60px; border-radius: 50%;">
                                    </td>
                                    <td>{{ $docente->nombre1 }}<br>{{ $docente->nombre2 }}<br>{{ $docente->apellidop }}<br>{{ $docente->apellidom }}
                                    </td>
                                    <td>{{ $docente->email }}</td>
                                    <td>{{ $docente->tipo }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#asignaturasModal{{ $docente->dni }}" title="Ver Asignaturas">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#cohortesModal{{ $docente->dni }}" title="Ver Cohortes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <a href="{{ route('docentes.edit', $docente->dni) }}"
                                                class="btn btn-primary custom-btn btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                    
                                            <a href="{{ route('asignaturas_docentes.create1', $docente->dni) }}"
                                                class="btn btn-success custom-btn btn-sm" title="Agregar Asignaturas">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                    
                                            <a href="{{ route('cohortes_docentes.create1', $docente->dni) }}"
                                                class="btn btn-warning custom-btn btn-sm" title="Agregar Cohortes">
                                                <i class="fas fa-plus"></i>
                                            </a>
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

    @foreach ($docentes as $docente)
        @include('modales.cohortes_docente_modal')
        @include('modales.asignaturas_docente_modal')
    @endforeach
@stop

@section('js')
    <script>
        $('#docentes').DataTable({
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
    <script>
        $(document).ready(function() {
            $('.delete-btn').click(function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var nombreAsignatura = $(this).closest('tr').find('td:first').text().trim();

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Se eliminará la asignatura '" + nombreAsignatura + "'",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop
