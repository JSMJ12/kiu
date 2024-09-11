@extends('adminlte::page')
@section('title', 'Secretarios')
@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Secretarios</h1>
@stop
@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Secretarios</h3>
                <div class="card-tools">
                    <a href="{{ route('secretarios.create') }}" class="btn btn-light btn-sm"><i class="fas fa-plus"></i>
                        Agregar
                        nuevo</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="secretarios">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Cédula / Pasaporte</th>
                                <th>Foto</th>
                                <th>Nombres</th>
                                <th>Email</th>
                                <th>Seccion</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($secretarios as $secretario)
                                <tr>
                                    <td>{{ $secretario->dni }}</td>
                                    <td class="text-center">
                                        <img src="{{ asset($secretario->image) }}" alt="Imagen de {{ $secretario->name1 }}"
                                            style="max-width: 60px; border-radius: 50%;">
                                    </td>
                                    <td>
                                        {{ $secretario->apellidop }}<br>
                                        {{ $secretario->apellidom }}<br>
                                        {{ $secretario->nombre1 }}<br>
                                        {{ $secretario->nombre2 }}
                                    </td>
                                    <td>{{ $secretario->email }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal"
                                            data-target="#mostrarSeccionModal_{{ $secretario->seccion->id }}"
                                            title="Mostrar Sección">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-warning" data-toggle="modal"
                                            data-target="#editarSeccionModal_{{ $secretario->seccion->id }}"
                                            title="Editar Seccion">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('secretarios.edit', $secretario->id) }}" class="btn btn-primary"
                                            title="Editar Secretario">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if (isset($secretarios))
        @foreach ($secretarios as $secretario)
            @include('modales.mostrar_seccion_modal')
            @include('modales.edit_secciones_secretario_modal')
        @endforeach
    @endif
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#secretarios').DataTable({
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
        });
    </script>
@stop
