<nav
    class="main-header navbar {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }} {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">
    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
        {{-- Mostrar el icono de notificaciones con el número --}}

    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Custom right links --}}
        @yield('content_top_nav_right')
        <li class="nav-item">
            <a id="notificacionesModalLink" class="nav-link" href="#">
                <i class="fa fa-bell"></i> {{-- Icono de campana para notificaciones --}}
                <span class="badge badge-warning" id="cantidadDeNuevasNotificaciones">
                    {{ $cantidadDeNuevasNotificaciones ?? 0 }}
                </span> {{-- Número de nuevos mensajes --}}
            </a>
        </li>
        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu link --}}
        @if (Auth::user())
            @if (config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if (config('adminlte.right_sidebar'))
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>
</nav>

<div class="modal fade" id="notificacionesModal" tabindex="-1" role="dialog"
    aria-labelledby="notificacionesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="notificacionesModalLabel">Notificaciones</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Aquí se llenarán dinámicamente las notificaciones mediante JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<script>
    $(document).ready(function() {
        $('#notificacionesModalLink').click(function(event) {
            event.preventDefault(); // Evitar el comportamiento predeterminado del enlace

            // Realizar una solicitud AJAX para obtener los datos de las notificaciones
            $.get('/notificaciones', function(data) {
                // Limpiar el contenido del modal antes de actualizarlo
                $('#notificacionesModal .modal-body').empty();

                // Verificar si hay notificaciones
                var modalContent;
                if (data.notificaciones && data.notificaciones.length > 0) {
                    // Construir el contenido del modal con los datos de las notificaciones
                    modalContent = '<ul>';
                    data.notificaciones.forEach(function(notificacion) {
                        var mensaje;
                        var remitente;

                        if (notificacion.data.type === 'NewMessageNotification') {
                            mensaje = notificacion.data.message;
                            remitente = notificacion.data.sender.name;
                            modalContent += '<li data-message-id="' + notificacion.id +
                                '" data-type="NewMessageNotification">De: ' +
                                remitente + '<br>Mensaje: ' + mensaje + '</li>';
                        } else if (notificacion.data.type ===
                            'PostulanteAceptadoNotification') {
                            mensaje = notificacion.data.message;
                            modalContent += '<li data-message-id="' + notificacion.id +
                                '" data-type="PostulanteAceptadoNotification">Mensaje: ' +
                                mensaje + '</li>';
                        } else if (notificacion.data.type ===
                            'SubirArchivoNotification') {
                            mensaje = notificacion.data.message;
                            modalContent += '<li data-message-id="' + notificacion.id +
                                '" data-type="PostulanteAceptadoNotification">Mensaje: ' +
                                mensaje + '</li>';
                        }
                    });
                    modalContent += '</ul>';
                } else {
                    // Si no hay notificaciones, mostrar un mensaje en el modal
                    modalContent = '<p>No hay notificaciones.</p>';
                }

                // Agregar el contenido al cuerpo del modal
                $('#notificacionesModal .modal-body').html(modalContent);

                // Agregar un evento de clic a cada mensaje para redirigir
                $('#notificacionesModal .modal-body li').click(function() {
                    var messageId = $(this).data('message-id');
                    var notificationType = $(this).data('type');

                    if (notificationType === 'NewMessageNotification') {
                        window.location.href = '/mensajes/buzon/';
                    } else if (notificationType === 'PostulanteAceptadoNotification') {
                        window.location.href = '/inicio';
                    } else if (notificationType === 'SubirArchivoNotification') {
                        window.location.href = '/inicio';
                    }
                });

                // Mostrar la cantidad de nuevas notificaciones en el badge del icono de campana
                $('#cantidadDeNuevasNotificaciones').text(data.cantidadNotificacionesNuevas);

                // Mostrar el modal
                $('#notificacionesModal').modal('show');
            }).fail(function() {
                // En caso de error en la solicitud AJAX, mostrar un mensaje de error
                $('#notificacionesModal .modal-body').html(
                    '<p>Error al obtener las notificaciones.</p>');
                $('#cantidadDeNuevasNotificaciones').text('0');
                $('#notificacionesModal').modal('show');
            });

            // Configurar Laravel Echo para suscribirse a los eventos de Pusher
            var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
            });

            var channel = pusher.subscribe('brief-valley-786');

            // Manejar evento de nuevo mensaje
            channel.bind('App\\Events\\NewMessageNotificationEvent', function(data) {
                console.log('Nuevo mensaje recibido:', data);

                var newNotification;
                if (data.type === 'NewMessageNotification') {
                    newNotification = '<li data-message-id="' + data.id +
                        '" data-type="NewMessageNotification">De: ' + data.sender.name +
                        '<br>Mensaje: ' + data.message + '</li>';
                } else if (data.type === 'PostulanteAceptadoNotification') {
                    newNotification = '<li data-message-id="' + data.id +
                        '" data-type="PostulanteAceptadoNotification">Mensaje: ' + data
                        .message + '</li>';
                } else if (data.type === 'SubirArchivoNotification') {
                    newNotification = '<li data-message-id="' + data.id +
                        '" data-type="SubirArchivoNotification">Mensaje: ' + data.message +
                        '</li>';
                }

                if ($('#notificacionesModal .modal-body ul').length === 0) {
                    $('#notificacionesModal .modal-body').html('<ul>' + newNotification +
                        '</ul>');
                } else {
                    $('#notificacionesModal .modal-body ul').prepend(newNotification);
                }

                $('#notificacionesModal .modal-body li').first().click(function() {
                    var messageId = $(this).data('message-id');
                    var notificationType = $(this).data('type');

                    if (notificationType === 'NewMessageNotification') {
                        window.location.href = '/mensajes/buzon/';
                    } else if (notificationType === 'PostulanteAceptadoNotification') {
                        window.location.href = '/inicio';
                    } else if (notificationType === 'SubirArchivoNotification') {
                        window.location.href = '/inicio';
                    }
                });
            });

            // Manejar evento de aceptación de postulante
            channel.bind('App\\Events\\PostulanteAceptado', function(data) {
                console.log('Nuevo postulante aceptado:', data);

                var newNotification;
                if (data.type === 'NewMessageNotification') {
                    newNotification = '<li data-message-id="' + data.id +
                        '" data-type="NewMessageNotification">De: ' + data.sender.name +
                        '<br>Mensaje: ' + data.message + '</li>';
                } else if (data.type === 'PostulanteAceptadoNotification') {
                    newNotification = '<li data-message-id="' + data.id +
                        '" data-type="PostulanteAceptadoNotification">Mensaje: ' + data
                        .message + '</li>';
                } else if (data.type === 'SubirArchivoNotification') {
                    newNotification = '<li data-message-id="' + data.id +
                        '" data-type="SubirArchivoNotification">Mensaje: ' + data.message +
                        '</li>';
                }

                if ($('#notificacionesModal .modal-body ul').length === 0) {
                    $('#notificacionesModal .modal-body').html('<ul>' + newNotification +
                        '</ul>');
                } else {
                    $('#notificacionesModal .modal-body ul').prepend(newNotification);
                }

                $('#notificacionesModal .modal-body li').first().click(function() {
                    var messageId = $(this).data('message-id');
                    var notificationType = $(this).data('type');

                    if (notificationType === 'NewMessageNotification') {
                        window.location.href = '/mensajes/buzon/';
                    } else if (notificationType === 'PostulanteAceptadoNotification') {
                        window.location.href = '/inicio';
                    } else if (notificationType === 'SubirArchivoNotification') {
                        window.location.href = '/inicio';
                    }
                });
            });
            channel.bind('App\\Events\\SubirArchivoEvent', function(data) {
                console.log('Subir archivo:', data);

                var newNotification;
                if (data.type === 'NewMessageNotification') {
                    newNotification = '<li data-message-id="' + data.id +
                        '" data-type="NewMessageNotification">De: ' + data.sender.name +
                        '<br>Mensaje: ' + data.message + '</li>';
                } else if (data.type === 'PostulanteAceptadoNotification') {
                    newNotification = '<li data-message-id="' + data.id +
                        '" data-type="PostulanteAceptadoNotification">Mensaje: ' + data
                        .message + '</li>';
                } else if (data.type === 'SubirArchivoNotification') {
                    newNotification = '<li data-message-id="' + data.id +
                        '" data-type="SubirArchivoNotification">Mensaje: ' + data.message +
                        '</li>';
                }

                if ($('#notificacionesModal .modal-body ul').length === 0) {
                    $('#notificacionesModal .modal-body').html('<ul>' + newNotification +
                        '</ul>');
                } else {
                    $('#notificacionesModal .modal-body ul').prepend(newNotification);
                }

                $('#notificacionesModal .modal-body li').first().click(function() {
                    var messageId = $(this).data('message-id');
                    var notificationType = $(this).data('type');

                    if (notificationType === 'NewMessageNotification') {
                        window.location.href = '/mensajes/buzon/';
                    } else if (notificationType === 'PostulanteAceptadoNotification') {
                        window.location.href = '/inicio';
                    } else if (notificationType === 'SubirArchivoNotification') {
                        window.location.href = '/inicio';
                    }
                });
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        function actualizarContador() {
            $.get('/cantidad-notificaciones', function(data) {
                $('#cantidadDeNuevasNotificaciones').text(data.cantidadNotificacionesNuevas);
            }).fail(function() {
                $('#cantidadDeNuevasNotificaciones').text('0');
            });
        }

        setInterval(actualizarContador, 1000);
    });
</script>
