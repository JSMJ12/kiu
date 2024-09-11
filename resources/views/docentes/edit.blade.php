@extends('adminlte::page')
@section('title', 'Actualizar Docente')
@section('content_header')
    <h1>Actualizar Docente</h1>
@stop
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('docentes.update', $docente->dni) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group row">
                                <label for="nombre" class="col-md-4 col-form-label text-md-right">{{ __('Nombre') }}</label>

                                <div class="col-md-6">
                                    <input id="nombre1" type="text" class="form-control @error('nombre1') is-invalid @enderror" name="nombre1" value="{{ $docente->nombre1 }}" required autocomplete="nombre1" autofocus>

                                    @error('nombre1')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="apellido" class="col-md-4 col-form-label text-md-right">{{ __('Apellido') }}</label>

                                <div class="col-md-6">
                                    <input id="apellidop" type="text" class="form-control @error('apellidop') is-invalid @enderror" name="apellidop" value="{{ $docente->apellidop }}" required autocomplete="apellidop">

                                    @error('apellidop')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tipo">Tipo de docente</label>
                                <select class="form-control" id="tipo" name="tipo" required>
                                    <option value="">Seleccione el tipo de docente</option>
                                    <option value="NOMBRADO">Nombrado</option>
                                    <option value="CONTRATADO">Contratado</option>
                                </select>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Actualizar') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop