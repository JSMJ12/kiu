@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-white text-center">
                    <h3>Perfil del Estudiante</h3>
                </div>
                <div class="card-body">
                    <div class="profile-picture text-center mb-3">
                        <img src="{{ asset('storage/' . $alumno->image) }}" alt="Foto de perfil" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <div class="profile-info">
                        <h4 class="text-center">{{ $alumno->nombre1 }} {{ $alumno->nombre2 }} {{ $alumno->apellidop }} {{ $alumno->apellidom }}</h4>
                        <hr>
                        <p><strong>ID:</strong> {{ $alumno->dni }}</p>
                        <p><strong>Número de Estudiante:</strong> {{ $alumno->registro }}</p>
                        <p><strong>Email Institucional:</strong> {{ $alumno->email_institucional }}</p>
                        <p><strong>Email Personal:</strong> {{ $alumno->email_personal }}</p>
                        <p><strong>Título Profesional:</strong> {{ $alumno->titulo_profesional }}</p>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- Contenido Principal -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2>Asignaturas Matriculadas</h2>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($asignaturas as $asignatura)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $asignatura->nombre }}</span>
                                @if($asignatura->silabo)
                                    <a href="{{ asset('storage/'.$asignatura->silabo) }}" target="_blank" class="btn btn-success btn-sm">Ver Sílabo</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $('#nota').DataTable({
        lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100],
        pageLength: 10,
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

@section('css')
    <style>
    .profile-picture img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
    }

    .profile-info h4 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .card-header h2, .card-header h3 {
        margin: 0;
    }

    </style>
@stop