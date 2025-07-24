<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    
    public function __construct()
    {
        $this->order = $order->load('customer', 'items.product');
    }

    public function build()
    {
        $pdf = Pdf::loadView('admin.orders.invoice', ['order' => $this->order]);

        return $this->subject('Your Order Invoice - Order #' . $this->order->id)
            ->markdown('emails.invoice')
            ->attachData($pdf->output(), "invoice_order_{$this->order->id}.pdf");
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
