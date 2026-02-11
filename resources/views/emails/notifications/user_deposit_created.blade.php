@php
    $amount = (float) ($deposit->user_amount ?? 0);
@endphp
@component('emails.layout', [
    'title' => 'تم استلام طلب الشحن / Deposit request received',
    'preheader' => 'Deposit #' . $deposit->id
])
<div class="section rtl">
    <h3 class="lang-title">العربية</h3>
    <p>تم استلام طلب شحن رصيدك بنجاح وهو الآن قيد المراجعة.</p>
    <table class="details-table">
        <tr><td class="label">رقم طلب الشحن</td><td class="value">#{{ $deposit->id }}</td></tr>
        <tr><td class="label">المبلغ</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr><td class="label">الطريقة</td><td class="value">{{ $deposit->paymentMethod->name ?? '-' }}</td></tr>
        <tr><td class="label">الحالة</td><td class="value">{{ $deposit->status }}</td></tr>
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('account.deposits.show', $deposit) }}">عرض الطلب</a></p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p>Your deposit request has been received successfully and is now under review.</p>
    <table class="details-table">
        <tr><td class="label">Deposit ID</td><td class="value">#{{ $deposit->id }}</td></tr>
        <tr><td class="label">Amount</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr><td class="label">Method</td><td class="value">{{ $deposit->paymentMethod->name ?? '-' }}</td></tr>
        <tr><td class="label">Status</td><td class="value">{{ $deposit->status }}</td></tr>
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('account.deposits.show', $deposit) }}">View Request</a></p>
</div>
@endcomponent
