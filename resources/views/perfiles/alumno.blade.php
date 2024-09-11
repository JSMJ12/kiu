@extends('adminlte::page')
@section('title', 'Datos Personales')

@section('content_header')
    <h1>Actualizar Datos Personales</h1>
@stop

@section('content')
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('update_datosAlumnos') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-center mb-4">
                                        <img src="{{ asset($alumno->image) }}" alt="Imagen del Alumno" id="currentImage" class="img-fluid rounded-circle shadow" style="max-width: 150px;">
                                        <br>
                                        <label for="image" class="mt-2">Cambiar foto:</label>
                                        <input type="file" id="imageInput" name="image" accept="image/*" class="form-control">
                                        <img id="previewImage" src="#" alt="Previsualización de la Imagen" class="img-fluid rounded-circle shadow mt-2" style="display: none; max-width: 150px;">
                                    </div>

                                    <div class="form-group">
                                        <label for="correo_electronico">Correo Electrónico Personal:</label>
                                        <input type="email" name="correo_electronico" class="form-control" placeholder="correo@example.com" value="{{ old('correo_electronico', $alumno->email_personal) }}" required>
                                        @error('correo_electronico')
                                            @if ($message == 'The correo_electronico field is required.')
                                                <div class="alert alert-danger">
                                                    <strong>Error:</strong> El Correo Electrónico es obligatorio.
                                                </div>
                                            @endif
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="celular">Celular:</label>
                                        <input type="text" name="celular" class="form-control" placeholder="Número de celular" value="{{ old('celular', $alumno->celular) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="estado_civil">Estado Civil:</label>
                                        <select name="estado_civil" id="estado_civil" class="form-control">
                                            <option value="">Selecciona tu Estado Civil</option>
                                            @foreach ($estadosCiviles as $estadoCivil)
                                                <option value="{{ $estadoCivil }}" {{ old('estado_civil', $alumno->estado_civil) == $estadoCivil ? 'selected' : '' }}>
                                                    {{ $estadoCivil }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
    
                                    <div class="form-group">
                                        <label for="titulo_profesional">Título Profesional:</label>
                                        <input type="text" name="titulo_profesional" class="form-control" placeholder="Título profesional" value="{{ old('titulo_profesional', $alumno->titulo_profesional) }}" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="universidad_titulo">Universidad en la que obtuvo el título de tercer nivel:</label>
                                        <input type="text" name="universidad_titulo" class="form-control" placeholder="Nombre de la universidad" value="{{ old('universidad_titulo', $alumno->universidad_titulo) }}" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="sexo">Sexo:</label>
                                        <select name="sexo" class="form-control" required>
                                            <option value="HOMBRE" {{ old('sexo', $alumno->sexo) == 'HOMBRE' ? 'selected' : '' }}>Hombre</option>
                                            <option value="MUJER" {{ old('sexo', $alumno->sexo) == 'MUJER' ? 'selected' : '' }}>Mujer</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                                        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento) }}" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="nacionalidad">Nacionalidad:</label>
                                        <input type="text" name="nacionalidad" class="form-control" placeholder="Nacionalidad" value="{{ old('nacionalidad', $alumno->nacionalidad) }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discapacidad">Posee alguna Discapacidad:</label>
                                        <select name="discapacidad" class="form-control" required>
                                            <option value="No">No</option>
                                            <option value="Si">Sí</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="divPorcentajeDiscapacidad" style="display: none;">
                                        <label for="porcentaje_discapacidad">Porcentaje de discapacidad (en caso de no tener, ingresar 0):</label>
                                        <input type="number" name="porcentaje_discapacidad" class="form-control" min="0" max="100">
                                    </div>

                                    <div class="form-group" id="divCodigoConadis" style="display: none;">
                                        <label for="codigo_conadis">Código CONADIS (en caso de tener carnet del MSP ingresar número de cédula):</label>
                                        <input type="text" name="codigo_conadis" class="form-control" placeholder="Código CONADIS">
                                    </div>

                                    <div class="form-group">
                                        <label for="provincia">Provincia:</label>
                                        <select name="provincia" id="provincia" class="form-control">
                                            <option value="">Selecciona una provincia</option>
                                            @foreach ($provincias as $provincia)
                                                <option value="{{ $provincia }}" {{ old('provincia', $alumno->provincia) == $provincia ? 'selected' : '' }}>
                                                    {{ $provincia }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="etnia">Etnia:</label>
                                        <input type="text" name="etnia" class="form-control" placeholder="Etnia" value="{{ old('etnia', $alumno->etnia) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="nacionalidad_indigena">Nacionalidad (en caso que se auto identifique como Indígena, caso contrario ingresar NO APLICA):</label>
                                        <input type="text" name="nacionalidad_indigena" class="form-control" placeholder="Nacionalidad Indígena">
                                    </div>

                                    <div class="form-group">
                                        <label for="canton">Cantón:</label>
                                        <input type="text" name="canton" class="form-control" placeholder="Cantón" value="{{ old('canton', $alumno->canton) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="direccion">Dirección:</label>
                                        <input type="text" name="direccion" class="form-control" placeholder="Dirección" value="{{ old('direccion', $alumno->direccion) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="tipo_colegio">Tipo de Colegio:</label>
                                        <select name="tipo_colegio" id="tipo_colegio" class="form-control">
                                            <option value="">Selecciona el Tipo de Colegio</option>
                                            @foreach ($tipo_colegio as $tp)
                                                <option value="{{ $tp }}" {{ old('tipo_colegio', $alumno->tipo_colegio) == $tp ? 'selected' : '' }}>
                                                    {{ $tp }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="cantidad_miembros_hogar">Cantidad de Miembros en el Hogar:</label>
                                        <input type="number" name="cantidad_miembros_hogar" class="form-control" min="0" value="{{ old('cantidad_miembros_hogar', $alumno->cantidad_miembros_hogar) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="ingreso_total_hogar">Ingreso Total del Hogar:</label>
                                        <select name="ingreso_total_hogar" id="ingreso_total_hogar" class="form-control">
                                            <option value="">Selecciona el Ingreso Total del Hogar</option>
                                            @foreach ($ingreso_hogar as $tp)
                                                <option value="{{ $tp }}" {{ old('ingreso_total_hogar', $alumno->ingreso_total_hogar) == $tp ? 'selected' : '' }}>
                                                    {{ $tp }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="nivel_formacion_padre">Nivel Formación Padre:</label>
                                        <select name="nivel_formacion_padre" id="nivel_formacion_padre" class="form-control">
                                            <option value="">Selecciona el Nivel Formación Padre</option>
                                            @foreach ($formacion_padre as $tp)
                                                <option value="{{ $tp }}" {{ old('nivel_formacion_padre', $alumno->nivel_formacion_padre) == $tp ? 'selected' : '' }}>
                                                    {{ $tp }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="nivel_formacion_madre">Nivel Formación Madre:</label>
                                        <select name="nivel_formacion_madre" id="nivel_formacion_madre" class="form-control">
                                            <option value="">Selecciona el Nivel Formación Madre</option>
                                            @foreach ($formacion_padre as $tp)
                                                <option value="{{ $tp }}" {{ old('nivel_formacion_madre', $alumno->nivel_formacion_madre) == $tp ? 'selected' : '' }}>
                                                    {{ $tp }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="origen_recursos_estudios">Origen de los Recursos de Estudios:</label>
                                        <select name="origen_recursos_estudios" id="origen_recursos_estudios" class="form-control">
                                            <option value="">Selecciona el Origen de los Recursos de Estudios</option>
                                            @foreach ($origen_recursos as $tp)
                                                <option value="{{ $tp }}" {{ old('origen_recursos_estudios', $alumno->origen_recursos_estudios) == $tp ? 'selected' : '' }}>
                                                    {{ $tp }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                                </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            const discapacidadSelect = document.querySelector('[name="discapacidad"]');
            const divPorcentajeDiscapacidad = document.getElementById('divPorcentajeDiscapacidad');
            const divCodigoConadis = document.getElementById('divCodigoConadis');

            discapacidadSelect.addEventListener('change', function () {
                if (this.value === 'Si') {
                    divPorcentajeDiscapacidad.style.display = 'block';
                    divCodigoConadis.style.display = 'block';
                } else {
                    divPorcentajeDiscapacidad.style.display = 'none';
                    divCodigoConadis.style.display = 'none';
                }
            });
        });

        document.getElementById('imageInput').addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImage = document.getElementById('previewImage');
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@stop
