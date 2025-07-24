@component('mail::message')
# Hello {{ $order->customer->name }},

Thank you for your order. Please find your invoice attached.

**Order ID:** #{{ $order->id }}  
**Date:** {{ $order->created_at->format('d M Y') }}

@component('mail::button', ['url' => route('orders.invoice', $order->id)])
View Online Invoice
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
