<div class="modal fade" id="asignaturasModal{{ $docente->dni }}" tabindex="-1" role="dialog"
    aria-labelledby="asignaturasModalLabel{{ $docente->dni }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="asignaturasModalLabel{{ $docente->dni }}">Asignaturas de
                    {{ $docente->nombre1 }} {{ $docente->apellidop }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($docente->asignaturas->isEmpty())
                    <div class="alert alert-info" role="alert">
                        No hay asignaturas asignadas a este docente.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Asignatura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($docente->asignaturas as $asignatura)
                                    <tr>
                                        <td>{{ $asignatura->nombre }}</td>
                                        <td>
                                            <form
                                                action="{{ route('eliminar_asignatura', ['docente_dni' => $docente->dni, 'asignatura_id' => $asignatura->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                                    data-toggle="tooltip" data-placement="top" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>