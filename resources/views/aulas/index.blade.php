@extends('adminlte::page')

@section('title', 'Aulas')

@section('content_header')
    <h1><i class="fas fa-chalkboard"></i> Gestión de Aulas</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow-lg">
        <div class="card-header text-white" style="background-color: #3007b8;">
            <h3 class="card-title">Listado de Aulas</h3>
            <div class="card-tools">
                <button id="crearAulaBtn" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#crearAulaModal">
                    <i class="fas fa-plus"></i> Crear Aula
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped" id="aulas">
                    <thead style="background-color: #28a745; color: white;">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Piso</th>
                            <th>Código</th>
                            <th>Paralelo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($aulas as $aula)
                            <tr data-bs-toggle="modal" data-bs-target="#editarAulaModal-{{ $aula->id }}">
                                <td>{{ $aula->id }}</td>
                                <td>{{ $aula->nombre }}</td>
                                <td>{{ $aula->piso }}</td>
                                <td>{{ $aula->codigo }}</td>
                                <td>{{ $aula->paralelo }}</td>
                                <td class="text-center">
                                    <form action="{{ route('aulas.destroy', $aula->id) }}" method="POST" style="display: inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta aula?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Modal Editar Aula -->
                            @include('modales.editar_aula_modal')
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <small class="text-muted">Total de Aulas: {{ $aulas->count() }}</small>
        </div>
    </div>
</div>


@include('modales.crear_aula_modal')

@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#aulas').DataTable({
            lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100],
            pageLength: {{ $perPage }},
            responsive: true,
            colReorder: true,
            keys: true,
            autoFill: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            columnDefs: [
                { targets: 5, orderable: false }
            ]
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#crearAulaBtn').on('click', function() {
            $('#crearAulaModal').modal('show');
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#aulas').on('click', 'tbody tr', function() {
            var targetModal = $(this).data('bs-target');
            $(targetModal).modal('show');
        });
    });
</script>
@stop
