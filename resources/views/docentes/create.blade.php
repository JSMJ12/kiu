@extends('adminlte::page')
@section('title', 'Crear Docente')
@section('content_header')
    <h1>Crear Docente</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('docentes.store') }}" enctype="multipart/form-data">
                @csrf
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
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control">
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
                    <label for="tipo">Tipo de docente</label>
                    <select class="form-control" id="tipo" name="tipo" required>
                        <option value="">Seleccione el tipo de docente</option>
                        <option value="NOMBRADO">Nombrado</option>
                        <option value="CONTRATADO">Contratado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="docen_foto">Foto:</label>
                    <input type="file" name="docen_foto" id="docen_foto" class="form-control-file" accept="image/*">
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
        document.getElementById('docen_foto').addEventListener('change', function(event) {
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
