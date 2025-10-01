<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

/**
 * Notifies customer when an order is placed.
 * Uses mail and database channels
 */
class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
        // You may specify a queue name if you want:
        // $this->onQueue('emails');
    }

    public function via(object $notifiable): array
    {
        // mail + database for auditability
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('messages.order_confirmation_subject'))
            ->greeting(Lang::get('messages.order_confirmation_greeting', [
                'name' => $notifiable->name
            ]))
            ->line(Lang::get('messages.order_confirmation_line', [
                'id' => $this->order->id
            ]))
            ->line(Lang::get('messages.order_confirmation_total', [
                'amount' => $this->order->total_amount
            ]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id'     => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'status'       => $this->order->status, // enum cast returns value
            'message'      => __('order.db.message', ['id' => $this->order->id]),
        ];
    }
}
