@extends('adminlte::page')
@section('title', ' Editar Alumno')
@section('content_header')
    <h1>Editar Alumno</h1>
@stop
@section('content')
<div class="container">
    <hr>
    <form method="POST" action="{{ route('alumnos.update', $alumno->dni) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="maestria_id">Maestría:</label>
            <select class="form-control" id="maestria_id" name="maestria_id" required>
                <option value="">Seleccione una maestría</option>
                @foreach($maestrias as $maestria)
                    <option value="{{ $maestria->id }}" @if($maestria->id == $alumno->maestria_id) selected @endif>{{ $maestria->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="nombre1">Primer nombre:</label>
                <input type="text" name="nombre1" class="form-control" value="{{ $alumno->nombre1 }}">
            </div>
            <div class="form-group col-md-4">
                <label for="nombre2">Segundo nombre:</label>
                <input type="text" name="nombre2" class="form-control" value="{{ $alumno->nombre2 }}">
            </div>
            <div class="form-group col-md-4">
                <label for="apellidop">Apellido paterno:</label>
                <input type="text" name="apellidop" class="form-control" value="{{ $alumno->apellidop }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="apellidom">Apellido materno:</label>
                <input type="text" name="apellidom" class="form-control" value="{{ $alumno->apellidom }}">
            </div>
            <div class="form-group col-md-6">
                <label for="dni">DNI:</label>
                <input type="text" name="dni" class="form-control" value="{{ $alumno->dni }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="estado_civil">Estado civil:</label>
                <input type="text" name="estado_civil" class="form-control" value="{{ $alumno->estado_civil }}">
            </div>
            <div class="form-group col-md-4">
                <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ $alumno->fecha_nacimiento }}">
            </div>
            <div class="form-group col-md-4">
                <label for="provincia">Provincia:</label>
                <select name="provincia" class="form-control">
                    @foreach($provincias as $prov)
                        <option value="{{ $prov }}" {{ $alumno->provincia == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <select name="sexo" id="sexo" class="form-control">
                <option value="">Seleccione el sexo</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="canton">Cantón/Ciudad:</label>
                <input type="text" name="canton" class="form-control" value="{{ $alumno->canton }}">
            </div>
            <div class="form-group col-md-4">
                <label for="barrio">Barrio:</label>
                <input type="text" name="barrio" class="form-control" value="{{ $alumno->barrio }}">
            </div>
            <div class="form-group col-md-4">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" class="form-control" value="{{ $alumno->direccion }}">
            </div>
            <div class="form-group col-md-6">
                <label for="nacionalidad">Nacionalidad:</label>
                <input type="text" name="nacionalidad" class="form-control" value="{{ $alumno->nacionalidad }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="etnia">Etnia:</label>
                <input type="text" name="etnia" class="form-control" value="{{ $alumno->etnia }}">
            </div>
            <div class="form-group col-md-6">
                <label for="email_personal">Correo electrónico personal:</label>
                <input type="email" name="email_personal" class="form-control" value="{{ $alumno->email_personal }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="email_institucional">Correo electrónico institucional:</label>
                <input type="email" name="email_institucional" class="form-control" value="{{ $alumno->email_institucional }}">
            </div>
            <div class="form-group col-md-6">
                <label for="carnet_discapacidad">Carnet de discapacidad:</label>
                <input type="text" name="carnet_discapacidad" class="form-control" value="{{ $alumno->carnet_discapacidad }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tipo_discapacidad">Tipo de discapacidad:</label>
                <input type="text" name="tipo_discapacidad" class="form-control" value="{{ $alumno->tipo_discapacidad }}">
            </div>
            <div class="form-group col-md-6">
                <label for="porcentaje_discapacidad">Porcentaje de discapacidad:</label>
                <input type="number" name="porcentaje_discapacidad" class="form-control" step="0.1" value="{{ $alumno->porcentaje_discapacidad }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@stop