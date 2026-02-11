@php
    $amount = (float) ($order->amount_held ?? $order->price_at_purchase ?? 0);
@endphp
@component('emails.layout', [
    'title' => 'تم إنشاء طلبك / Your order was created',
    'preheader' => 'Order #' . $order->id
])
<div class="section rtl">
    <h3 class="lang-title">العربية</h3>
    <p>مرحبًا {{ $order->user->name ?? ($user->name ?? 'عميلنا') }}، تم إنشاء طلبك بنجاح.</p>
    <table class="details-table">
        <tr><td class="label">رقم الطلب</td><td class="value">#{{ $order->id }}</td></tr>
        <tr><td class="label">الخدمة</td><td class="value">{{ $order->service->name ?? '-' }}</td></tr>
        @if($order->variant)
        <tr><td class="label">الباقة</td><td class="value">{{ $order->variant->name }}</td></tr>
        @endif
        <tr><td class="label">المبلغ</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr><td class="label">الحالة</td><td class="value">{{ $order->status }}</td></tr>
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('account.orders.show', $order) }}">عرض الطلب</a></p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p>Hello {{ $order->user->name ?? ($user->name ?? 'Customer') }}, your order has been created successfully.</p>
    <table class="details-table">
        <tr><td class="label">Order ID</td><td class="value">#{{ $order->id }}</td></tr>
        <tr><td class="label">Service</td><td class="value">{{ $order->service->name ?? '-' }}</td></tr>
        @if($order->variant)
        <tr><td class="label">Variant</td><td class="value">{{ $order->variant->name }}</td></tr>
        @endif
        <tr><td class="label">Amount</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr><td class="label">Status</td><td class="value">{{ $order->status }}</td></tr>
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('account.orders.show', $order) }}">View Order</a></p>
</div>
@endcomponent
