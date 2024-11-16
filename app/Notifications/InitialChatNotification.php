<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InitialChatNotification extends Notification
{
    use Queueable;
    public User $send;
    public String $message;
    /**
     * Create a new notification instance.
     */
    public function __construct(
        User $send,
        String $message
    )
    {
        $this->send = $send;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(env('URL_FRONT'));
        return (new MailMessage)
            ->subject('Nova mensagem no Toolzz!')
            ->greeting('Obaaaáaaaa!')
            ->line($notifiable->name.' verifique no sistema sua nova mensagem recebida.')
            ->line($this->send['name'].' acaba de manda a seguinte mensagem para voce:')
            ->line('" '.$this->message.' " ')
            ->action('Entra no Toolzz', url(env('URL_FRONT')))
            ->line('Obrigado por usar nosso aplicativo!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'send' => $this->send->name,
            'message' => $this->message,
            'icone' => 'fa-brands fa-rocketchat'
        ];
    }
}
