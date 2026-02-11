@php
    $amount = (float) ($order->amount_held ?? $order->price_at_purchase ?? 0);
@endphp
@component('emails.layout', [
    'title' => 'طلب جديد للإدارة / New order for admin',
    'preheader' => 'New Order #' . $order->id
])
<div class="section rtl">
    <h3 class="lang-title">العربية</h3>
    <p>تم إنشاء طلب جديد ويحتاج متابعة من الإدارة.</p>
    <table class="details-table">
        <tr><td class="label">رقم الطلب</td><td class="value">#{{ $order->id }}</td></tr>
        <tr><td class="label">المستخدم</td><td class="value">{{ $order->user->name ?? '-' }} ({{ $order->user->email ?? '-' }})</td></tr>
        <tr><td class="label">الخدمة</td><td class="value">{{ $order->service->name ?? '-' }}</td></tr>
        <tr><td class="label">المبلغ</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr><td class="label">الحالة</td><td class="value">{{ $order->status }}</td></tr>
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('admin.orders.show', $order) }}">فتح الطلب</a></p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p>A new order has been created and requires admin review.</p>
    <table class="details-table">
        <tr><td class="label">Order ID</td><td class="value">#{{ $order->id }}</td></tr>
        <tr><td class="label">User</td><td class="value">{{ $order->user->name ?? '-' }} ({{ $order->user->email ?? '-' }})</td></tr>
        <tr><td class="label">Service</td><td class="value">{{ $order->service->name ?? '-' }}</td></tr>
        <tr><td class="label">Amount</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr><td class="label">Status</td><td class="value">{{ $order->status }}</td></tr>
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('admin.orders.show', $order) }}">Open Order</a></p>
</div>
@endcomponent
