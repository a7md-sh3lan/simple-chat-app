<?php

namespace App\Listeners;

use App\Events\MessageEvent;
use App\Models\User;
use App\Notifications\MessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMessageNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageEvent  $event
     * @return void
     */
    public function handle(MessageEvent $event)
    {
        $message = $event->message;
        $sender_id = $event->sender_id;
        $receiver_id = $event->receiver_id;        
        $target = User::find($receiver_id);
        
        $target->notify(new MessageNotification($message,$sender_id,$receiver_id));
    }
}
