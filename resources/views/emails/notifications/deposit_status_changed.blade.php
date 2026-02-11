@php
    $statusAr = [
        'pending' => '??? ????????',
        'approved' => '?????',
        'rejected' => '?????',
    ][$deposit->status] ?? $deposit->status;

    $statusEn = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ][$deposit->status] ?? $deposit->status;

    $badgeClass = match($deposit->status) {
        'approved' => 'status-done',
        'rejected' => 'status-rejected',
        default => 'status-pending',
    };

    $requestedAmount = (float) ($deposit->user_amount ?? 0);
    $approvedAmount = $deposit->approved_amount !== null ? (float) $deposit->approved_amount : null;
@endphp
@component('emails.layout', [
    'title' => '????? ???? ????? / Deposit status updated',
    'preheader' => 'Deposit #' . $deposit->id . ' status changed'
])
<div class="section rtl">
    <h3 class="lang-title">???????</h3>
    <p>?? ????? ???? ??? ????? ????? ??.</p>
    <table class="details-table">
        <tr><td class="label">??? ?????</td><td class="value">#{{ $deposit->id }}</td></tr>
        <tr><td class="label">?????? ???????</td><td class="value">${{ number_format($requestedAmount, 2) }}</td></tr>
        @if(!is_null($approvedAmount))
        <tr><td class="label">?????? ???????</td><td class="value highlight-value">${{ number_format($approvedAmount, 2) }}</td></tr>
        @endif
        <tr><td class="label">??????</td><td class="value"><span class="status-badge {{ $badgeClass }}">{{ $statusAr }}</span></td></tr>
        @if($deposit->admin_note)
        <tr><td class="label">?????? ???????</td><td class="value">{{ $deposit->admin_note }}</td></tr>
        @endif
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('account.deposits.show', $deposit) }}">??? ?????</a></p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p>Your deposit request status has been updated.</p>
    <table class="details-table">
        <tr><td class="label">Request ID</td><td class="value">#{{ $deposit->id }}</td></tr>
        <tr><td class="label">Requested Amount</td><td class="value">${{ number_format($requestedAmount, 2) }}</td></tr>
        @if(!is_null($approvedAmount))
        <tr><td class="label">Approved Amount</td><td class="value highlight-value">${{ number_format($approvedAmount, 2) }}</td></tr>
        @endif
        <tr><td class="label">Status</td><td class="value"><span class="status-badge {{ $badgeClass }}">{{ $statusEn }}</span></td></tr>
        @if($deposit->admin_note)
        <tr><td class="label">Admin Note</td><td class="value">{{ $deposit->admin_note }}</td></tr>
        @endif
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ route('account.deposits.show', $deposit) }}">View Request</a></p>
</div>
@endcomponent
