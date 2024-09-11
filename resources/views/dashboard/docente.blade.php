@extends('adminlte::page')

@section('title', 'Dashboard Docente')

@section('content_header')
    <h1>Dashboard Docente</h1>
@stop

@section('content')
    <div class="container">
        @foreach ($data as $asignatura)
            <div class="card mb-4">
                <div class="card-header toggle-body" data-toggle="cohorte_{{ $loop->index }}">
                    <strong>{{ $asignatura['nombre'] }}</strong>
                    <div class="card-body">
                        @if($asignatura['silabo'])
                            <a href="{{ asset('storage/'.$asignatura['silabo']) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Ver Sílabo</a>
                        @else
                            <form action="{{ route('updateSilabo') }}" method="POST" class="form-silabo" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="asignatura_id" value="{{ $asignatura['id'] }}">
                                <div class="mb-3">
                                    <label for="silabo_{{ $asignatura['id'] }}" class="form-label">Subir Sílabo</label>
                                    <input type="file" id="silabo_{{ $asignatura['id'] }}" name="silabo" class="form-control">
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                                </div>
                            </form>
                        @endif
                    </div>
        
                </div>
                @foreach ($asignatura['cohortes'] as $cohorte)
                    <div class="card-header toggle-body" data-toggle="cohorte_{{ $loop->parent->index }}_{{ $loop->index }}">
                        <strong>{{ $cohorte['nombre'] }}</strong>
                        <span>Aula: {{ $cohorte['aula'] ?? 'N/A' }} | Paralelo: {{ $cohorte['paralelo'] ?? 'N/A' }} | Fecha límite: {{ $cohorte['fechaLimite'] }}</span>
                        <div class="float-right" id="botones">
                            <a href="{{ $cohorte['excelUrl'] }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Lista de Alumnos
                            </a>
                            @php
                                $editar = false;
                                $calificacionVerificacion = DB::table('calificacion_verificacion')
                                    ->where('docente_dni', $cohorte['docenteId'])
                                    ->where('asignatura_id', $cohorte['asignaturaId'])
                                    ->where('cohorte_id', $cohorte['cohorteId'])
                                    ->first();
                                if ($calificacionVerificacion) {
                                    $editar = $calificacionVerificacion->editar;
                                }
                            @endphp
                            @if ($editar || ($cohorte['fechaLimite'] >= now() && auth()->user()->can('calificar') && $cohorte['pdfNotasUrl'] == null))
                                <a href="{{ $cohorte['calificarUrl'] }}" class="btn btn-primary btn-sm">Calificar</a>
                            @endif
                            @if ($cohorte['pdfNotasUrl'] !== null)
                                <a href="{{ $cohorte['pdfNotasUrl'] }}" class="btn btn-danger btn-sm" target="_blank">
                                    <i class="fas fa-file-pdf"></i> PDF de Notas
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body" id="cohorte_{{ $loop->parent->index }}_{{ $loop->index }}" style="display: none;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>Imagen</th>
                                    <th>Nota Actividades</th>
                                    <th>Nota Prácticas</th>
                                    <th>Nota Autónomo</th>
                                    <th>Examen Final</th>
                                    <th>Recuperación</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cohorte['alumnos'] as $alumno)
                                    <tr>
                                        <td>{{ $alumno['nombreCompleto'] }}</td>
                                        <td>
                                            <img src="{{ asset($alumno['imagen']) }}" alt="Imagen del alumno" class="img-thumbnail rounded-circle" style="width: 80px;">
                                        </td>
                                        @if ($alumno['notas']['nota_actividades'] !== 'N/A' || $alumno['notas']['nota_practicas'] !== 'N/A' || $alumno['notas']['nota_autonomo'] !== 'N/A' || $alumno['notas']['examen_final'] !== 'N/A' || $alumno['notas']['recuperacion'] !== 'N/A')
                                            <td>{{ $alumno['notas']['nota_actividades'] }}</td>
                                            <td>{{ $alumno['notas']['nota_practicas'] }}</td>
                                            <td>{{ $alumno['notas']['nota_autonomo'] }}</td>
                                            <td>{{ $alumno['notas']['examen_final'] }}</td>
                                            <td>{{ $alumno['notas']['recuperacion'] }}</td>
                                            <td>{{ $alumno['notas']['total'] }}</td>
                                        @else
                                            <td colspan="6">Notas no disponibles</td>
                                        @endif
                                        <td>
                                            @if ($cohorte['pdfNotasUrl'] !== null)
                                                <a href="{{ $alumno['verNotasUrl'] }}" class="btn btn-info btn-sm">Ver Notas</a>
                                            @else
                                                <a href="{{ $cohorte['calificarUrl'] }}" class="btn btn-primary btn-sm">Calificar</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">No hay alumnos en este cohorte.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function(){
            $(".toggle-body").click(function(){
                var target = $(this).data('toggle');
                $("#" + target).toggle();
            });
        });
    </script>
@stop

@section('css')

@stop

