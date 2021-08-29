<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

class MobileNotification extends Notification
{
    use Queueable;
    
    private $message;
    
    private $title;
    
    private $priority;

    public function __construct($message,$title,$priority){
        $this->message = $message;
        $this->title = $title;
        $this->priority = $priority;
    }
   
    public function via($notifiable)
    {
        return [ExpoChannel::class];
    }
    public function toExpoPush($notifiable)
    {        
        return ExpoMessage::create()
        ->badge(1)
        ->enableSound()
        ->title($this->title)
        ->body($this->message)
        ->setChannelId("begowork")
        ->priority($this->priority)
        ->setTtl(600000);
    }
}
