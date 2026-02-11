@php
    $amount = (float) ($deposit->user_amount ?? 0);
@endphp
@component('emails.layout', [
    'title' => 'طلب شحن جديد للإدارة / New deposit for admin',
    'preheader' => 'New Deposit #' . $deposit->id
])
<div class="section rtl">
    <h3 class="lang-title">العربية</h3>
    <p>تم إنشاء طلب شحن جديد ويحتاج مراجعة من الإدارة.</p>
    <table class="details-table">
        <tr><td class="label">رقم الطلب</td><td class="value">#{{ $deposit->id }}</td></tr>
        <tr><td class="label">المستخدم</td><td class="value">{{ $deposit->user->name ?? '-' }} ({{ $deposit->user->email ?? '-' }})</td></tr>
        <tr><td class="label">المبلغ</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr><td class="label">الطريقة</td><td class="value">{{ $deposit->paymentMethod->name ?? '-' }}</td></tr>
        <tr><td class="label">الحالة</td><td class="value">{{ $deposit->status }}</td></tr>
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('admin.deposits.show', $deposit) }}">فتح الطلب</a></p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p>A new deposit request has been created and requires admin review.</p>
    <table class="details-table">
        <tr><td class="label">Request ID</td><td class="value">#{{ $deposit->id }}</td></tr>
        <tr><td class="label">User</td><td class="value">{{ $deposit->user->name ?? '-' }} ({{ $deposit->user->email ?? '-' }})</td></tr>
        <tr><td class="label">Amount</td><td class="value highlight-value">${{ number_format($amount, 2) }}</td></tr>
        <tr><td class="label">Method</td><td class="value">{{ $deposit->paymentMethod->name ?? '-' }}</td></tr>
        <tr><td class="label">Status</td><td class="value">{{ $deposit->status }}</td></tr>
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('admin.deposits.show', $deposit) }}">Open Request</a></p>
</div>
@endcomponent
