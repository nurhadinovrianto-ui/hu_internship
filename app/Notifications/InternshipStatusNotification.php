<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InternshipStatusNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $url;

    public function __construct($title, $message, $url = '#')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database']; // Only database notifications for now
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}
