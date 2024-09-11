@extends('adminlte::page')
@section('title', 'Crear Cohorte')
@section('content_header')
    <h1>Crear Cohorte</h1>
@stop
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center bg-success">Información de Cohorte</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cohortes.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="maestria_id">Maestría:</label>
                            <select id="maestria_id" class="form-control" name="maestria_id" required>
                                <option value="" selected disabled>-- Seleccione una opción --</option>
                                @foreach($maestrias as $maestria)
                                    <option value="{{ $maestria->id }}">{{ $maestria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="periodo_academico_id">Periodo Académico:</label>
                            <select id="periodo_academico_id" class="form-control" name="periodo_academico_id" required>
                                <option value="" selected disabled>-- Seleccione una opción --</option>
                                @foreach($periodos_academicos as $periodo_academico)
                                    <option value="{{ $periodo_academico->id }}">{{ $periodo_academico->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="aforo">Aforo:</label>
                            <input id="aforo" type="number" class="form-control" name="aforo" value="{{ old('aforo') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="modalidad">Modalidad:</label>
                            <select id="modalidad" class="form-control" name="modalidad" required>
                                <option value="">--Seleccione--</option>
                                <option value="presencial">Presencial</option>
                                <option value="hibrida">Híbrida</option>
                                <option value="virtual">Virtual</option>
                            </select>
                        </div>

                        <div class="form-group" id="aula_id">
                            <label for="aula_id">Aula:</label>
                            <select class="form-control" name="aula_id">
                                <option value="">--Seleccione--</option>
                                @foreach($aulas as $aula)
                                    <option value="{{ $aula->id }}">{{ $aula->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio:</label>
                            <input id="fecha_inicio" type="date" class="form-control" name="fecha_inicio">
                        </div>

                        <div class="form-group">
                            <label for="fecha_fin">Fecha de Fin:</label>
                            <input id="fecha_fin" type="date" class="form-control" name="fecha_fin">
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Crear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalidadSelect = document.getElementById("modalidad");
        const aulaSelect = document.getElementById("aula_id");

        // Ocultar el campo de selección de aulas al cargar la página
        aulaSelect.style.display = "none";

        modalidadSelect.addEventListener("change", function() {
            if (modalidadSelect.value === "virtual") {
                // Si la modalidad es virtual, ocultar el campo de selección de aulas
                aulaSelect.style.display = "none";
                // También, deseleccionar cualquier valor previamente seleccionado
                aulaSelect.selectedIndex = 0;
            } else {
                // Si la modalidad no es virtual, mostrar el campo de selección de aulas
                aulaSelect.style.display = "block";
            }
        });
    });
</script>

@stop