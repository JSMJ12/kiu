@extends('adminlte::page')

@section('title', 'Mensajeria')

@section('content_header')
    <h1>Mensajeria</h1>
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Mensajes</h3>
                </div> 
                <div class="card-body">
                    @if (session('notifications'))
                        @include('partials.notifications', ['notifications' => session('notifications')])
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered" id='mensajes'>
                            <thead>
                                <tr>
                                    <th>De</th>
                                    <th>Para</th>
                                    <th>Mensaje</th>
                                    <th>Adjunto</th>
                                    <th>Fecha</th>
                                    <th>Responder</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                    <tr>
                                        <td>{{ $message->sender->name }} {{ $message->sender->apellido }}</td>
                                        <td>{{ $message->receiver->name }} {{ $message->receiver->apellido }}</td>
                                        <td>{{ $message->message }}</td>
                                        <td>
                                            @if($message->attachment)
                                                <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank">
                                                    <i class="fas fa-paperclip"></i> Ver adjunto
                                                </a>
                                            @else
                                                Sin adjunto
                                            @endif
                                        </td>
                                        <td>{{ $message->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td class="text-center">
                                            <i class="fas fa-envelope send-message" data-toggle="modal" data-target="#sendMessageModal{{ $message->sender->id }}" title="Enviar mensaje"></i>
                                            <!-- Modal de mensajes -->
                                            <div class="modal fade" id="sendMessageModal{{ $message->sender->id }}" tabindex="-1" role="dialog" aria-labelledby="sendMessageModalLabel{{ $message->sender->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="sendMessageModalLabel{{ $message->sender->id }}">Enviar mensaje a {{ $message->sender->name }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Formulario de envío de mensaje aquí -->
                                                            <form id="sendMessageForm{{ $message->sender->id }}" action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <!-- Campos del formulario -->
                                                                <div class="form-group">
                                                                    <label for="message">Mensaje</label>
                                                                    <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="attachment">Adjunto</label>
                                                                    <input type="file" class="form-control-file" id="attachment" name="attachment">
                                                                </div>
                                                                <!-- Campo oculto para receiver_id -->
                                                                <input type="hidden" name="receiver_id" value="{{ $message->sender->id }}">
                                                                <!-- Fin de campos del formulario -->
                                                                <button type="submit" class="btn btn-primary">Enviar</button>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Fin del modal -->
                                        </td>
                                        <td>
                                            <form action="{{ route('messages.destroy', $message->id) }}" method="POST" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay mensajes disponibles.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('js')
<script>
    $('#mensajes').DataTable({
        lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100], 
        pageLength: {{ $perPage }},
        responsive: true, 
        colReorder: true,
        keys: true,
        autoFill: true, 
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });
    // Escuchar el evento MessageUpdated en el canal 'messages'
    Echo.channel('messages')
    .listen('MessageUpdated', (event) => {
        // Accede a los datos del evento
        const message = event.message;

        // Actualiza la interfaz de usuario con los datos recibidos
        updateTable(message);
    });

    // Función para actualizar la tabla con un nuevo mensaje
    function updateTable(message) {
        // Asumiendo que tienes una tabla con id 'mensajes'
        const tablaMensajes = $('#mensajes tbody');

        // Crear una nueva fila con los datos del mensaje
        const nuevaFila = `<tr>
                                <td>${message.sender.name} ${message.sender.apellido}</td>
                                <td>${message.receiver.name} ${message.receiver.apellido}</td>
                                <td>${message.message}</td>
                                <td>
                                    ${message.attachment ? `<a href="${message.attachment}" target="_blank"><i class="fas fa-paperclip"></i> Ver adjunto</a>` : 'Sin adjunto'}
                                </td>
                                <td>${new Date(message.created_at).toLocaleString()}</td>
                                <td>
                                    <form action="{{ route('messages.destroy', '') }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="message_id" value="${message.id}">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                        </tr>`;

        // Agregar la nueva fila al principio de la tabla
        tablaMensajes.prepend(nuevaFila);
    }
</script>
@stop