@extends('adminlte::page')
@section('title', 'Crear Alumno')
@section('content_header')
    <h1>Crear Alumno</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('alumnos.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="maestria_id">Maestría:</label>
                    <select class="form-control" id="maestria_id" name="maestria_id" required>
                        <option value="">Seleccione una maestría</option>
                        @foreach($maestrias as $maestria)
                            <option value="{{ $maestria->id }}">{{ $maestria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="nombre">Primer Nombre:</label>
                    <input type="text" name="nombre1" id="nombre" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nombre">Segundo Nombre:</label>
                    <input type="text" name="nombre2" id="nombre" class="form-control">
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido Paterno:</label>
                    <input type="text" name="apellidop" id="pellido" class="form-control">
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido Materno:</label>
                    <input type="text" name="apellidom" id="pellido" class="form-control">
                </div>
                <div class="form-group">
                    <label for="email">Email Personal:</label>
                    <input type="email" name="email_per" id="email" class="form-control">
                </div>
                <div class="form-group">
                    <label for="email">Email Institucional:</label>
                    <input type="email" name="email_ins" id="email" class="form-control">
                </div>
                <div class="form-group">
                    <label for="sexo">Sexo:</label>
                    <select name="sexo" id="sexo" class="form-control">
                        <option value="">Seleccione el sexo</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dni">Cédula / Pasaporte</label>
                    <input type="text" class="form-control" id="dni" name="dni" required>
                </div>
                <div class="form-group">
                    <label for="estado_civil">Estado Civil</label>
                    <select class="form-control" id="estado_civil" name="estado_civil" required>
                        <option value="">Seleccione el estado civil</option>
                        <option value="Soltero/a">Soltero/a</option>
                        <option value="Casado/a">Casado/a</option>
                        <option value="Viudo/a">Viudo/a</option>
                        <option value="Divorciado/a">Divorciado/a</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                </div>
                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <select name="provincia" id="provincia" class="form-control">
                        <option value="">Selecciona una provincia</option>
                        @foreach ($provincias as $provincia)
                            <option value="{{ $provincia }}">{{ $provincia }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="canton">Ciudad / Canton</label>
                    <input type="text" class="form-control" id="canton" name="canton" required>
                </div>
                <div class="form-group">
                    <label for="barrio">Parroquia / Barrio</label>
                    <input type="text" class="form-control" id="barrio" name="barrio" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <textarea class="form-control" id="direccion" name="direccion" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="nacionalidad">Nacionalidad</label>
                    <input type="text" class="form-control" id="nacionalidad" name="nacionalidad">
                </div>
                <div class="form-group">
                    <label for="etnia">Etnia</label>
                    <input type="text" class="form-control" id="etnia" name="etnia">
                </div>
                <div class="form-group">
                    <label for="discapacidad">¿Tiene discapacidad?</label>
                    <select class="form-control" id="discapacidad" name="discapacidad">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>
                <div class="discapacidad-campos" style="display: none;">
                    <div class="form-group">
                        <label for="carnet_discapacidad">Carnet de discapacidad</label>
                        <input type="text" class="form-control" id="carnet_discapacidad" name="carnet_discapacidad">
                    </div>
                    <div class="form-group">
                        <label for="tipo_discapacidad">Tipo de Discapacidad</label>
                        <select class="form-control" id="tipo_discapacidad" name="tipo_discapacidad">
                            <option value="" disabled selected>Seleccione el tipo de discapacidad</option>
                            <option value="Física">Física</option>
                            <option value="Sensorial">Sensorial</option>
                            <option value="Intelectual">Intelectual</option>
                            <option value="Mental">Mental</option>
                            <option value="Otra">Otra</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="porcentaje_discapacidad">Porcentaje de discapacidad:</label>
                        <input type="number" step="0.1" class="form-control" id="porcentaje_discapacidad" name="porcentaje_discapacidad" value="{{ old('porcentaje_discapacidad') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="image">Foto:</label>
                    <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                    <div id="preview-container" style="margin-top: 10px;">
                        <img id="preview-image" src="#" alt="Vista previa" style="display: none; max-width: 150px; max-height: 150px; object-fit: cover;" />
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('docentes.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    <script>
        $(document).ready(function(){
            $('#discapacidad').on('change', function() {
                if (this.value == '1') {
                    $('.discapacidad-campos').show();
                } else {
                    $('.discapacidad-campos').hide();
                }
            });
        });
    </script>
    <script>
        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById('preview-image');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                previewImage.src = '#';
                previewImage.style.display = 'none';
            }
        });
    </script>
@stop
