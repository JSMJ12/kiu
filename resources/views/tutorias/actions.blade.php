<!-- Botón para abrir modal -->
<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#crearTutoriaModal{{ $item->id }}">
    <i class="fas fa-plus"></i> Asignar Tutoría
</button>
<a href="{{ route('tutorias.listar', $item->id) }}" class="btn btn-sm btn-primary">
    <i class="fas fa-list"></i> Ver Tutorías
</a>

<!-- Modal -->
<div class="modal fade" id="crearTutoriaModal{{ $item->id }}" tabindex="-1" aria-labelledby="crearTutoriaModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title">Asignar Nueva Tutoría</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('tutorias.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="tesis_id" value="{{ $item->id }}">

                    <!-- Selección del tipo de tutoría -->
                    <div class="mb-3">
                        <label for="tipo">
                            <i class="fas fa-chalkboard-teacher"></i> Tipo de Tutoría
                        </label>
                        <select class="form-control tipo-select" id="tipo-tutoria" name="tipo">
                            <option value="virtual">Virtual</option>
                            <option value="presencial">Presencial</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label id="dynamic-label" for="dynamic-input" class="form-label">
                            <i class="fas fa-map-marker-alt" id="icon-lugar"></i>
                            <i class="fas fa-link" id="icon-link" style="display: none;"></i> 
                            Lugar / Link de la Reunión
                        </label>
                        <input type="text" id="dynamic-input" name="detalle" class="form-control" placeholder="Ingrese el lugar o el link">
                    </div>

                    <div class="mb-3">
                        <label for="fecha">
                            <i class="fas fa-calendar-alt"></i> Fecha y Hora
                        </label>
                        <input type="datetime-local" name="fecha" class="form-control" required>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label for="observaciones">
                            <i class="fas fa-comments"></i> Observaciones
                        </label>
                        <textarea name="observaciones" class="form-control" placeholder="Ingrese observaciones adicionales"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Asignar Tutoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para cambiar dinámicamente el input -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectTipo = document.getElementById('tipo-tutoria');
        const dynamicLabel = document.getElementById('dynamic-label');
        const dynamicInput = document.getElementById('dynamic-input');

        function toggleInput() {
            if (selectTipo.value === 'presencial') {
                dynamicLabel.innerHTML = '<i class="fas fa-map-marker-alt"></i> Lugar';
                dynamicInput.placeholder = 'Ingrese el lugar';
                dynamicInput.type = 'text';
            } else {
                dynamicLabel.innerHTML = '<i class="fas fa-link"></i> Link de Reunión';
                dynamicInput.placeholder = 'Ingrese el link de reunión';
                dynamicInput.type = 'url';
            }
        }

        selectTipo.addEventListener('change', toggleInput);
        toggleInput(); // Inicializar en la carga
    });
</script>
