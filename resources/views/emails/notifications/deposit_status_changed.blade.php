@component('emails.layout', ['title' => 'Arab 8bp.in - تحديث حالة طلب الشحن'])

@php
    $statusText = match($deposit->status) {
        'pending' => 'قيد المراجعة',
        'approved' => 'مقبول',
        'rejected' => 'مرفوض',
        default => $deposit->status
    };
    
    $statusClass = match($deposit->status) {
        'pending' => 'status-pending',
        'approved' => 'status-done',
        'rejected' => 'status-rejected',
        default => 'status-pending'
    };
    
    $greeting = match($deposit->status) {
        'approved' => 'تم قبول طلب الشحن الخاص بك',
        'rejected' => 'تم رفض طلب الشحن الخاص بك',
        default => 'تم تحديث حالة طلب الشحن'
    };
@endphp

<p class="intro-text">
    {{ $greeting }}، وهذه تفاصيله:
</p>

<table class="details-table">
    <tr>
        <td class="label">رقم طلب الشحن:</td>
        <td class="value highlight-value">#{{ $deposit->id }}</td>
    </tr>
    <tr>
        <td class="label">المبلغ المطلوب:</td>
        <td class="value">$ {{ number_format($deposit->user_amount, 2) }}</td>
    </tr>
    @if($deposit->approved_amount && $deposit->approved_amount != $deposit->user_amount)
    <tr>
        <td class="label">المبلغ المعتمد:</td>
        <td class="value highlight-value">$ {{ number_format($deposit->approved_amount, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">حالة الطلب:</td>
        <td class="value">
            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
        </td>
    </tr>
    @if($deposit->admin_note)
    <tr>
        <td class="label">ملاحظة الإدارة:</td>
        <td class="value rtl">{{ $deposit->admin_note }}</td>
    </tr>
    @endif
</table>

@if($deposit->status === 'approved')
<div class="action-text">
    تم إضافة الرصيد إلى حسابك بنجاح! يمكنك الآن استخدامه لطلب الخدمات من خلال <a href="{{ route('home') }}">الموقع</a>.
</div>
@elseif($deposit->status === 'rejected')
<div class="action-text">
    نعتذر عن عدم إمكانية قبول طلب الشحن. للمزيد من التفاصيل، يرجى مراجعة <a href="{{ route('account.deposits.show', $deposit) }}">صفحة الطلب</a> أو التواصل مع الدعم.
</div>
@else
<div class="action-text">
    يمكنك متابعة حالة طلبك من خلال <a href="{{ route('account.deposits.show', $deposit) }}">حسابك</a>.
</div>
@endif

@endcomponent
