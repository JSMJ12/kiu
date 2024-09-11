<div class="modal fade" id="editModal{{ $seccion->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $seccion->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="editModalLabel{{ $seccion->id }}">Editar Sección</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('secciones.update', $seccion) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nombre">{{ __('Nombre') }}</label>
                        <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre', $seccion->nombre) }}" required autofocus>
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
                                    <input id="maestria_{{ $maestria->id }}" type="checkbox" class="@error('maestrias') is-invalid @enderror" name="maestrias[]" value="{{ $maestria->id }}" {{ $seccion->maestrias->contains($maestria->id) ? 'checked' : '' }}>
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
                            {{ __('Actualizar') }}
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {{ __('Cancelar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>