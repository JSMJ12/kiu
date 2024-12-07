@extends('adminlte::page')

@section('title', 'Proceso de Titulación')

@section('content_header')
    <h1 class="text-center text-success">Proceso Completo de Titulación</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white text-center">
                <h4>Solicitud de Aprobación de Tema y Tutorías</h4>
            </div>
            <div class="card-body">
                <!-- Verifica si la tesis está aprobada -->
                @if (!$tesis || !$tesis->estado == 'aprobado')
                    <!-- Mostrar formulario de solicitud -->
                    <form action="{{ route('tesis.store') }}" method="POST" enctype="multipart/form-data" id="proceso-form">
                        @csrf

                        <!-- Paso 1: Información del Tema -->
                        <div class="form-step" id="step-1">
                            <h5 class="text-primary">Paso 1: Información del tema</h5>
                            <div class="mb-3">
                                <label for="tema" class="form-label">Tema de la Tesis</label>
                                <input type="text" class="form-control" id="tema" name="tema"
                                    placeholder="Ingrese el tema de su tesis" value="{{ old('tema') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                    placeholder="Describa brevemente el tema de su tesis" required>{{ old('descripcion') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('tesis.downloadPDF') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-download"></i> Descargar Formato de Solicitud
                                </a>
                            </div>
                            <button type="button" class="btn btn-primary next-step w-100">
                                Siguiente <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                        <!-- Paso 2: Carga de PDF -->
                        <div class="form-step d-none" id="step-2">
                            <h5 class="text-primary">Paso 2: Carga de la solicitud en PDF</h5>
                            <div class="mb-3">
                                <label for="solicitud_pdf" class="form-label">Archivo PDF de Solicitud</label>
                                <input type="file" class="form-control" id="solicitud_pdf" name="solicitud_pdf"
                                    accept="application/pdf" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary previous-step">
                                    <i class="fas fa-arrow-left"></i> Anterior
                                </button>
                                <button type="button" class="btn btn-success next-step">
                                    Continuar con Tutorías <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <!-- Mostrar mensaje si el tema está aprobado -->
                    <div class="alert alert-success text-center">
                        <h5>Tu tema de tesis ha sido aprobado.</h5>
                        <p>Continúa con las tutorías asignadas a tu tesis.</p>
                    </div>
                @endif

                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Tipo</th>
                            <th>Ubicación/Link</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tesis->tutorias as $tutoria)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($tutoria->fecha)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $tutoria->tipo == 'presencial' ? 'bg-success' : 'bg-primary' }}">
                                        {{ ucfirst($tutoria->tipo) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($tutoria->tipo == 'virtual')
                                        <a href="{{ $tutoria->link_reunion }}" target="_blank">
                                            <i class="fas fa-link"></i> {{ $tutoria->link_reunion }}
                                        </a>
                                    @else
                                        <i class="fas fa-map-marker-alt"></i> {{ $tutoria->lugar }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $tutoria->estado == 'realizada' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($tutoria->estado) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $tutoria->observaciones ?? 'Sin observaciones' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const steps = document.querySelectorAll('.form-step');
            const progressBar = document.getElementById('progress-bar');
            const totalSteps = steps.length;

            function updateStep(step) {
                steps.forEach((el, index) => el.classList.toggle('d-none', index !== step - 1));
                progressBar.style.width = `${(step / totalSteps) * 100}%`;
                progressBar.innerHTML = `Paso ${step} de ${totalSteps}`;
            }

            document.querySelectorAll('.next-step').forEach(btn => btn.addEventListener('click', () => {
                currentStep++;
                updateStep(currentStep);
            }));
            document.querySelectorAll('.previous-step').forEach(btn => btn.addEventListener('click', () => {
                currentStep--;
                updateStep(currentStep);
            }));

            updateStep(currentStep);
        });
    </script>
@stop
