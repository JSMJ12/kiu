@extends('adminlte::page')
@section('title', 'Editar Cohorte')
@section('content_header')
    <h1>Editar Cohorte</h1>
@stop
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Editar Cohorte') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('cohortes.update', $cohorte->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $cohorte->nombre) }}" required>
                        </div>
                        <div class="form-group row">
                            <label for="maestria_id" class="col-md-4 col-form-label text-md-right">{{ __('Maestría') }}</label>
                
                            <div class="col-md-6">
                                <select id="maestria_id" class="form-control @error('maestria_id') is-invalid @enderror" name="maestria_id" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    @foreach($maestrias as $maestria)
                                        <option value="{{ $maestria->id }}" {{ old('maestria_id', $cohorte->maestria_id) == $maestria->id ? 'selected' : '' }}>{{ $maestria->nombre }}</option>
                                    @endforeach
                                </select>
                
                                @error('maestria_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                
                        <div class="form-group row">
                            <label for="periodo_academico_id" class="col-md-4 col-form-label text-md-right">{{ __('Periodo Académico') }}</label>
                
                            <div class="col-md-6">
                                <select id="periodo_academico_id" class="form-control @error('periodo_academico_id') is-invalid @enderror" name="periodo_academico_id" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    @foreach($periodos_academicos as $periodo_academico)
                                        <option value="{{ $periodo_academico->id }}" {{ old('periodo_academico_id', $cohorte->periodo_academico_id) == $periodo_academico->id ? 'selected' : '' }}>{{ $periodo_academico->nombre }}</option>
                                    @endforeach
                                </select>
                
                                @error('periodo_academico_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="aula_id" class="col-md-4 col-form-label text-md-right">{{ __('Aula') }}</label>
                        
                            <div class="col-md-6">
                                <select id="aula_id" class="form-control @error('aula_id') is-invalid @enderror" name="aula_id" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    @foreach($aulas as $aula)
                                        <option value="{{ $aula->id }}" {{ old('aula_id', $cohorte->aula_id) == $aula->id ? 'selected' : '' }}>{{ $aula->nombre }}</option>
                                    @endforeach
                                </select>
                        
                                @error('aula_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="aforo" class="col-md-4 col-form-label text-md-right">{{ __('Aforo') }}</label>
                        
                            <div class="col-md-6">
                                <input id="aforo" type="number" class="form-control @error('aforo') is-invalid @enderror" name="aforo" value="{{ old('aforo', $cohorte->aforo) }}" required>
                        
                                @error('aforo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="modalidad" class="col-md-4 col-form-label text-md-right">{{ __('Modalidad') }}</label>
                        
                            <div class="col-md-6">
                                <select id="modalidad" class="form-control @error('modalidad') is-invalid @enderror" name="modalidad" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    <option value="presencial" {{ old('modalidad', $cohorte->modalidad) == 'presencial' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hibrida" {{ old('modalidad', $cohorte->modalidad) == 'hibrida' ? 'selected' : '' }}>Híbrida</option>
                                    <option value="virtual" {{ old('modalidad', $cohorte->modalidad) == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                </select>
                        
                                @error('modalidad')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="fecha_inicio" class="col-md-4 col-form-label text-md-right">{{ __('Fecha de Inicio') }}</label>
                            <div class="col-md-6">
                                <input id="fecha_inicio" type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" name="fecha_inicio" value="{{ old('fecha_inicio', $cohorte->fecha_inicio) }}" required>
                                @error('fecha_inicio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                
                        <div class="form-group row">
                            <label for="fecha_fin" class="col-md-4 col-form-label text-md-right">{{ __('Fecha de Fin') }}</label>
                            <div class="col-md-6">
                                <input id="fecha_fin" type="date" class="form-control @error('fecha_fin') is-invalid @enderror" name="fecha_fin" value="{{ old('fecha_fin', $cohorte->fecha_fin) }}" required>
                                @error('fecha_fin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
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