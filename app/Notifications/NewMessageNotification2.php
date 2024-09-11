<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Carbon\Carbon;


class NewMessageNotification2 extends Notification
{
    use Queueable;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail', 'broadcast', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Tienes un nuevo mensaje!')
            ->action('Ver mensaje', route('messages.index'));
    }

    public function toBroadcast($notifiable)
    {
        $senderName = $this->message->sender ? $this->message->sender->name : 'Remitente Desconocido';

        return new BroadcastMessage([
            'type' => 'NewMessageNotification',
            'message' => $this->message->message,
            'sender' => [
                'name' => $senderName,
            ],
            'receiver' => [
                'name' => $this->message->receiver->name,
            ],
            'link' => route('messages.index'),
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'NewMessageNotification',
            'message' => $this->message->message,
            'sender' => [
                'name' => $this->message->sender->name,
            ],
            'receiver' => [
                'name' => $this->message->receiver->name,
            ],
            'time' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toMailUsing($notifiable, $recipient)
    {
        return parent::toMailUsing($notifiable, $recipient)->introLines([]);
    }
}