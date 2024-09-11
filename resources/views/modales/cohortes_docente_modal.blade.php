<div class="modal fade" id="cohortesModal{{ $docente->dni }}" tabindex="-1" role="dialog"
    aria-labelledby="cohortesModalLabel{{ $docente->dni }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="cohortesModalLabel{{ $docente->dni }}">Cohortes de
                    {{ $docente->nombre1 }} {{ $docente->apellidop }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($docente->cohortes->isEmpty())
                    <div class="alert alert-info" role="alert">
                        No hay cohortes asignados a este docente.
                    </div>
                @else
                    <form action="{{ route('guardarCambios') }}" method="POST">
                        @csrf
                        <input type="hidden" name="docente_dni" value="{{ $docente->dni }}">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Maestría</th>
                                        <th>Nombre del Cohorte</th>
                                        <th>Modalidad</th>
                                        <th>Aula</th>
                                        <th>Paralelo</th>
                                        <th>Asignaturas</th>
                                        <th>Estado</th>
                                        <th>Permiso de Editar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $shownCohortes = []; @endphp
                                    @foreach ($docente->cohortes as $cohorte)
                                        @if (!in_array($cohorte->id, $shownCohortes))
                                            @php $first = true; @endphp
                                            @foreach ($cohorte->asignaturas as $asignatura)
                                                @if ($docente->asignaturas->contains($asignatura))
                                                    <tr>
                                                        @if ($first)
                                                            <td rowspan="{{ count($cohorte->asignaturas) }}">
                                                                {{ $cohorte->maestria->nombre }}</td>
                                                            <td rowspan="{{ count($cohorte->asignaturas) }}">
                                                                {{ $cohorte->nombre }}</td>
                                                            <td rowspan="{{ count($cohorte->asignaturas) }}">
                                                                {{ $cohorte->modalidad }}</td>
                                                            @if ($cohorte->aula)
                                                                <td rowspan="{{ count($cohorte->asignaturas) }}">
                                                                    {{ $cohorte->aula->nombre }}</td>
                                                            @else
                                                                <td rowspan="{{ count($cohorte->asignaturas) }}">No
                                                                    disponible</td>
                                                            @endif

                                                            @if ($cohorte->aula && $cohorte->aula->paralelo)
                                                                <td rowspan="{{ count($cohorte->asignaturas) }}">
                                                                    {{ $cohorte->aula->paralelo }}</td>
                                                            @else
                                                                <td rowspan="{{ count($cohorte->asignaturas) }}">No
                                                                    disponible</td>
                                                            @endif

                                                            @php $first = false; @endphp
                                                        @endif
                                                        <td>{{ $asignatura->nombre }}</td>
                                                        <td>
                                                            <input type="hidden" name="asignatura_id[]"
                                                                value="{{ $asignatura->id }}">
                                                            <input type="hidden" name="cohorte_id[]"
                                                                value="{{ $cohorte->id }}">
                                                            @php
                                                                $calificacion = $asignatura
                                                                    ->calificacionVerificaciones()
                                                                    ->where('docente_dni', $docente->dni)
                                                                    ->where('cohorte_id', $cohorte->id)
                                                                    ->first();
                                                                $calificado = $calificacion
                                                                    ? ($calificacion->calificado
                                                                        ? 'Calificado'
                                                                        : 'No calificado')
                                                                    : 'No calificado';
                                                            @endphp
                                                            {{ $calificado }}
                                                        </td>
                                                        <td>
                                                            <div class="form-check form-check-inline">
                                                                <input type="hidden"
                                                                    name="permiso_editar[{{ $docente->dni }}][{{ $asignatura->id }}][{{ $cohorte->id }}]"
                                                                    value="0">
                                                                <input type="radio"
                                                                    id="permiso_editar_si_{{ $cohorte->id }}"
                                                                    name="permiso_editar[{{ $docente->dni }}][{{ $asignatura->id }}][{{ $cohorte->id }}]"
                                                                    value="1" class="form-check-input"
                                                                    {{ $calificacion && $calificacion->editar ? 'checked' : '' }}>
                                                                <label for="permiso_editar_si_{{ $cohorte->id }}"
                                                                    class="form-check-label">Sí</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="radio"
                                                                    id="permiso_editar_no_{{ $cohorte->id }}"
                                                                    name="permiso_editar[{{ $docente->dni }}][{{ $asignatura->id }}][{{ $cohorte->id }}]"
                                                                    value="0" class="form-check-input"
                                                                    {{ !$calificacion || !$calificacion->editar ? 'checked' : '' }}>
                                                                <label for="permiso_editar_no_{{ $cohorte->id }}"
                                                                    class="form-check-label">No</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            @php $shownCohortes[] = $cohorte->id; @endphp
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>