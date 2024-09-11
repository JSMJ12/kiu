<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostulanteAceptadoNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;
    protected $postulante;

    public function __construct($postulante)
    {
        $this->postulante = $postulante;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('¡Felicidades! Has sido aceptado como alumno')
                    ->line('¡Enhorabuena! Tu solicitud ha sido aceptada y ahora eres oficialmente un alumno.')
                    ->action('Pagar Matrícula', route('inicio'))
                    ->line('Por favor, haz clic en el botón de abajo para pagar la matrícula y completar tu proceso de inscripción.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'PostulanteAceptadoNotification',
            'message' => '¡Felicidades! Tu solicitud ha sido aceptada y ahora eres oficialmente un alumno. Para completar tu proceso de ingreso, te pedimos que realices el pago de la matrícula.'
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('canal_p');
    }

    public function broadcastWith()
    {
        return [
            'postulante' => $this->postulante,
            'message' => '¡Tu solicitud ha sido aceptada y ahora eres oficialmente un alumno! Por favor, haz clic en el enlace para pagar la matrícula.'
        ];
    }
}
