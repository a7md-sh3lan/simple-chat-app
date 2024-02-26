<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    protected $sender_id;

    protected $receiver_id;

    protected $notification_message = ' send a message';

    protected $page = 'My Chat';


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message,$sender_id,$receiver_id)
    {
        //
        $this->message = $message;
        $this->sender_id = $sender_id;
        $this->receiver_id = $receiver_id;
        $this->notification_message = User::find($sender_id)->name . $this->notification_message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast'];
    }

    public function broadcastOn() {
        return ['notification.' . $this->receiver_id]; 
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $notifiable->id,
            'notification_message' => $this->notification_message,
            'receiver_id' => $notifiable->id,
            'section_name' => $this->page,
            'date' => $notifiable->created_at->diffForHumans()
        ];
    }
}
