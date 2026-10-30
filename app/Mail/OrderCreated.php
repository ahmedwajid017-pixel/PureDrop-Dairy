<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $timeout = 120;

    public function __construct(public Order $order)
    {
        //
    }

    public function build(): static
    {
        return $this->subject('New PureDrop Dairy Order')
            ->markdown('emails.orders.created');
    }
}