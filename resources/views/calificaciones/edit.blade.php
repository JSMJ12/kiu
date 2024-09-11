@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@extends('adminlte::page')
@section('title', 'Calificar')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Editar notas del alumno</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('calificaciones.update', $nota->id) }}">
                        @csrf
                        @method('PUT')
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Actividades</th>
                                    <th>Prácticas</th>
                                    <th>Autónomo</th>
                                    <th>Examen final</th>
                                    <th>Recuperación</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="number" step="0.01" class="form-control nota-input" name="nota_actividades" value="{{ $nota->nota_actividades }}" max="3.0" oninput="calcularTotal(this)"></td>
                                    <td><input type="number" step="0.01" class="form-control nota-input" name="nota_practicas" value="{{ $nota->nota_practicas }}" max="3.0" oninput="calcularTotal(this)"></td>
                                    <td><input type="number" step="0.01" class="form-control nota-input" name="nota_autonomo" value="{{ $nota->nota_autonomo }}" max="3.0" oninput="calcularTotal(this)"></td>
                                    <td><input type="number" step="0.01" class="form-control nota-input" name="examen_final" value="{{ $nota->examen_final }}" max="3.0" oninput="calcularTotal(this)"></td>
                                    <td><input type="number" step="0.01" class="form-control nota-input" name="recuperacion" value="{{ $nota->recuperacion }}" max="3.0" oninput="calcularTotal(this)"></td>
                                    <td><input type="number" step="0.01" class="form-control total-input" name="total" value="{{ $nota->total }}" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary">Actualizar notas</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        function calcularTotal(input) {
            var fila = input.closest('tr');
            var notas = fila.querySelectorAll('.nota-input');
            var total = 0;
            
            notas.forEach(function(nota) {
                total += parseFloat(nota.value) || 0;
            });

            fila.querySelector('.total-input').value = total.toFixed(2);
        }
    </script>
@stop