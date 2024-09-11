@extends('adminlte::page')
@section('title', 'Crear Secciones')
@section('content_header')
    <h1>Crear Secciones</h1>
@stop
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('secciones.store') }}">
                @csrf
                <div class="form-group">
                    <label for="nombre">{{ __('Nombre') }}</label>
                    <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autofocus>
                    @error('nombre')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="maestrias">{{ __('Maestrías') }}</label>
                    <div class="checkbox-grid">
                        @foreach ($maestrias as $maestria)
                            <div class="checkbox-item">
                                <input id="maestria_{{ $maestria->id }}" type="checkbox" class="@error('maestrias') is-invalid @enderror" name="maestrias[]" value="{{ $maestria->id }}" {{ in_array($maestria->id, old('maestrias', [])) ? 'checked' : '' }}>
                                <label for="maestria_{{ $maestria->id }}"> {{ $maestria->nombre }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('maestrias')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>               

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Guardar') }}
                    </button>
                    <a href="{{ route('secciones.index') }}" class="btn btn-secondary">
                        {{ __('Cancelar') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
@section('css')
<style>
    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(4, 2fr);
        grid-gap: 10px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
    }
</style>
@stop