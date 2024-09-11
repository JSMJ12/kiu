@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@extends('adminlte::page')
@section('title', 'Calificar')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Notas del alumno</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Actividades</th>
                                <th>Prácticas</th>
                                <th>Autónomo</th>
                                <th>Examen final</th>
                                <th>Recuperación</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notas as $nota)
                                <tr>
                                    <td>{{ $nota->nota_actividades }}</td>
                                    <td>{{ $nota->nota_practicas }}</td>
                                    <td>{{ $nota->nota_autonomo }}</td>
                                    <td>{{ $nota->examen_final }}</td>
                                    <td>{{ $nota->recuperacion }}</td>
                                    <td>{{ $nota->total }}</td>
                                    <td>
                                        @if ($tienePermisoVerNotas)
                                            @if ($fechaLimite >= now())
                                                <a href="{{ route('calificaciones.edit1', [$nota->alumno_dni, $nota->docente_dni, $nota->asignatura_id, $nota->cohorte_id]) }}" class="btn btn-sm btn-primary">Editar</a>
                                            @else
                                                <span class="text-danger">No se puede editar las notas después de la fecha límite.</span>
                                            @endif
                                        @else
                                            <span class="text-danger">No tienes permiso para editar las notas.</span>
                                        @endif
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

@stop