@extends('adminlte::page')

@section('title', 'Pagos')

@section('content_header')
    <h1>Descuentos</h1>
@stop

@section('content')
<div class="container">
    @if ($alumno->descuento)
        <div class="alert alert-info mt-4">
            <p>Ya has seleccionado el descuento "{{ ucfirst($alumno->descuento) }}". No puedes seleccionar otro descuento.</p>
        </div>
    @else
        <h1 class="program-title">Descuentos Disponibles para {{ $programa['nombre'] }}</h1>

        <form id="descuentos-form" action="{{ route('pago.descuento.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="descuentos-panel">
                <div class="descuento-item bg-success text-dark">
                    <h2>Descuento Académico</h2>
                    <p>Arancel Original: ${{ $programa['arancel'] }}</p>
                    <p>Descuento: ${{ $programa['descuento_academico'] }}</p>
                    <p>Total con Descuento: ${{ $programa['total_con_academico'] }}</p>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="descuento" id="descuentoAcademico" value="academico">
                        <label class="form-check-label text-dark" for="descuentoAcademico">
                            Seleccionar
                        </label>
                    </div>
                    <div class="requisitos">
                        <strong>Requisitos:</strong>
                        <ul>
                            <li>Tener un promedio mayor a 9.6</li>
                        </ul>
                    </div>
                </div>
                <div class="descuento-item bg-warning text-dark">
                    <h2>Descuento Socioeconómico</h2>
                    <p>Arancel Original: ${{ $programa['arancel'] }}</p>
                    <p>Descuento: ${{ $programa['descuento_socioeconomico'] }}</p>
                    <p>Total con Descuento: ${{ $programa['total_con_socioeconomico'] }}</p>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="descuento" id="descuentoSocioeconomico" value="socioeconomico">
                        <label class="form-check-label text-dark" for="descuentoSocioeconomico">
                            Seleccionar
                        </label>
                    </div>
                    <div class="requisitos">
                        <strong>Requisitos:</strong>
                        <ul>
                            <li>Ser de escasos recursos socioeconómicos</li>
                        </ul>
                    </div>
                </div>
                <div class="descuento-item bg-danger text-dark">
                    <h2>Descuento para Graduados</h2>
                    <p>Arancel Original: ${{ $programa['arancel'] }}</p>
                    <p>Descuento: ${{ $programa['descuento_graduados'] }}</p>
                    <p>Total con Descuento: ${{ $programa['total_con_graduados'] }}</p>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="descuento" id="descuentoGraduados" value="graduados">
                        <label class="form-check-label text-dark" for="descuentoGraduados">
                            Seleccionar
                        </label>
                    </div>
                    <div class="requisitos">
                        <strong>Requisitos:</strong>
                        <ul>
                            <li>Ser graduado en cualquier programa de pregrado ofrecido por UNESUM</li>
                        </ul>
                    </div>
                </div>
                <div class="descuento-item bg-primary text-dark">
                    <h2>Descuento Mejor Graduado</h2>
                    <p>Arancel Original: ${{ $programa['arancel'] }}</p>
                    <p>Descuento: ${{ $programa['descuento_mejor_graduado'] }}</p>
                    <p>Total con Descuento: ${{ $programa['total_con_mejor_graduado'] }}</p>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="descuento" id="descuentoMejorGraduado" value="mejor_graduado">
                        <label class="form-check-label text-dark" for="descuentoMejorGraduado">
                            Seleccionar
                        </label>
                    </div>
                    <div class="requisitos">
                        <strong>Requisitos:</strong>
                        <ul>
                            <li>Tener el documento que pruebe que es el mejor graduado de su promoción</li>
                            <li>Certificación de los dos últimos periodos académicos</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="documento-autenticidad" class="mt-4" style="display:none;">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Documento de Autenticidad</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="documento">Subir Documento de Autenticidad:</label>
                            <input type="file" class="form-control" id="documento" name="documento">
                        </div>
                        <div class="alert alert-info">
                            Para poder ser asignado esta beca tiene que cumplir con lo siguiente: los dos últimos periodos académicos, tener la certificación que le otorgue la carrera.
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Confirmar Selección</button>
        </form>

        <div class="alert alert-info mt-4">
            <p><strong>Nota:</strong> Solo puedes postular a una sola beca. Debes cumplir con los requisitos de la beca a la que postules.</p>
        </div>
    @endif
</div>
@stop

@section('css')
<style>
    .program-title {
        background-color: #04c60a;
        color: white;
        padding: 20px;
        text-align: center;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .descuentos-panel {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-around;
    }

    .descuento-item {
        position: relative;
        padding: 20px;
        min-height: 250px;
        flex: 1 1 calc(45% - 20px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        border-radius: 10px;
        max-width: 45%;
        cursor: pointer;
        overflow: visible;
    }

    .descuento-item h2, .descuento-item p {
        color: white;
    }

    .requisitos {
        display: none;
        background-color: white;
        color: #333;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
        text-align: left;
        width: 150px;
        position: absolute;
        right: -160px;
        top: 0;
        z-index: 5;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    }

    .descuento-item:hover .requisitos {
        display: block;
    }

    .form-check {
        margin-top: 10px;
    }

    .btn {
        display: block;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        .descuento-item {
            flex: 1 1 100%;
            max-width: 100%;
        }

        .requisitos {
            right: auto;
            left: 0;
            position: static;
            max-width: 100%;
        }
    }
</style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const descuentosForm = document.getElementById('descuentos-form');
        const documentoAutenticidadDiv = document.getElementById('documento-autenticidad');

        descuentosForm.addEventListener('change', function(e) {
            const selectedDescuento = descuentosForm.querySelector('input[name="descuento"]:checked').value;
            documentoAutenticidadDiv.style.display = selectedDescuento === 'mejor_graduado' ? 'block' : 'none';
        });
    });
</script>
@stop
