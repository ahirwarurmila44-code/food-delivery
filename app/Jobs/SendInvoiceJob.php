<?php

namespace App\Jobs;

use App\Models\Order;
use App\Mail\InvoiceMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class SendInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct()
    {
         $this->order = $order;
    }

    public function handle(): void
    {
         \Log::info('SendInvoiceJob: Sending invoice to ' . $this->order->customer->email);
        \Mail::to($this->order->customer->email)
            ->send(new \App\Mail\InvoiceMail($this->order));
    }
}
