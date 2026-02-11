@php
    $statusAr = [
        'new' => '????',
        'processing' => '??? ???????',
        'done' => '?????',
        'rejected' => '?????',
    ][$order->status] ?? $order->status;

    $statusEn = [
        'new' => 'New',
        'processing' => 'Processing',
        'done' => 'Done',
        'rejected' => 'Rejected',
    ][$order->status] ?? $order->status;

    $badgeClass = match($order->status) {
        'processing' => 'status-processing',
        'done' => 'status-done',
        'rejected' => 'status-rejected',
        default => 'status-pending',
    };

    $amount = (float) ($order->amount_held ?? $order->price_at_purchase ?? 0);
@endphp
@component('emails.layout', [
    'title' => '????? ???? ????? / Order status updated',
    'preheader' => 'Order #' . $order->id . ' status changed'
])
<div class="section rtl">
    <h3 class="lang-title">???????</h3>
    <p>?? ????? ???? ????. ???????? ???????:</p>
    <table class="details-table">
        <tr><td class="label">??? ?????</td><td class="value">#{{ $order->id }}</td></tr>
        <tr><td class="label">??????</td><td class="value">{{ $order->service->name ?? '-' }}</td></tr>
        <tr><td class="label">??????</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr>
            <td class="label">??????</td>
            <td class="value"><span class="status-badge {{ $badgeClass }}">{{ $statusAr }}</span></td>
        </tr>
        @if($order->admin_note)
        <tr><td class="label">?????? ???????</td><td class="value">{{ $order->admin_note }}</td></tr>
        @endif
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('account.orders.show', $order) }}">??? ?????</a></p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p>Your order status has been updated. Current details:</p>
    <table class="details-table">
        <tr><td class="label">Order ID</td><td class="value">#{{ $order->id }}</td></tr>
        <tr><td class="label">Service</td><td class="value">{{ $order->service->name ?? '-' }}</td></tr>
        <tr><td class="label">Amount</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr>
            <td class="label">Status</td>
            <td class="value"><span class="status-badge {{ $badgeClass }}">{{ $statusEn }}</span></td>
        </tr>
        @if($order->admin_note)
        <tr><td class="label">Admin Note</td><td class="value">{{ $order->admin_note }}</td></tr>
        @endif
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('account.orders.show', $order) }}">View Order</a></p>
</div>
@endcomponent
