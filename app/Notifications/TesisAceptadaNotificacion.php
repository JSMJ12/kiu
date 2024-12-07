<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TesisAceptadaNotificacion extends Notification
{
    protected $tesis;
    protected $usuario;  // Agregado: Para almacenar el usuario
    protected $email;    // Agregado: Para almacenar el email

    public function __construct($tesis, $usuario)
    {
        $this->tesis = $tesis;
        $this->usuario = $usuario;  // Guardar el usuario
        $this->email = $usuario->email;  // Guardar el correo electrónico
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Notificación por correo y base de datos
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Notificación de Aprobación de Tema de Tesis')
            ->greeting('¡Hola, ' . $this->usuario->nombre1 . '!')
            ->line('El tema de tesis "' . $this->tesis->tema . '" ha sido aceptado.')
            ->action('Ver Detalles', route('tesis.create')) // Redirige a la ruta 'tesis.create'
            ->line('¡Felicitaciones por tu avance en el proceso de titulación!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'El tema de tesis ha sido aceptado.',
        ];
    }
}
