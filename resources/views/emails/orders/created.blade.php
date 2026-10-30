
<x-mail::message>
# New Order Received

A customer has placed a new order with PureDrop Dairy.

**Order ID:** {{ $order->id }}

**Customer:** {{ $order->user->name }}

**Customer Email:** {{ $order->user->email }}

**Product:** {{ $order->product->name }}

**Quantity:** {{ $order->quantity }}

**Total Price:** {{ $order->total_price }}

**Status:** {{ $order->status }}

Thank you,

PureDrop Dairy
</x-mail::message>
