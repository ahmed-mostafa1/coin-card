@component('emails.layout', ['title' => 'Arab 8bp.in - تحديث حالة الطلب'])

@php
    $statusText = match($order->status) {
        'new', 'pending' => 'قيد المراجعة',
        'processing' => 'قيد التنفيذ',
        'done' => 'مكتمل',
        'rejected' => 'مرفوض',
        default => $order->status
    };
    
    $statusClass = match($order->status) {
        'new', 'pending' => 'status-pending',
        'processing' => 'status-processing',
        'done' => 'status-done',
        'rejected' => 'status-rejected',
        default => 'status-pending'
    };
    
    $greeting = match($order->status) {
        'processing' => 'تم البدء في معالجة طلبك',
        'done' => 'تم إكمال طلبك بنجاح',
        'rejected' => 'تم رفض طلبك',
        default => 'تم تحديث حالة طلبك'
    };
@endphp

<p class="intro-text">
    {{ $greeting }}، وهذه تفاصيله:
</p>

<table class="details-table">
    <tr>
        <td class="label">رقم الطلب:</td>
        <td class="value highlight-value">#{{ $order->id }}</td>
    </tr>
    <tr>
        <td class="label">الخدمة:</td>
        <td class="value rtl">{{ $order->service->name }}</td>
    </tr>
    <tr>
        <td class="label">المبلغ المدفوع:</td>
        <td class="value highlight-value">$ {{ number_format($order->amount_held, 2) }}</td>
    </tr>
    @if($order->discount_amount > 0)
    <tr>
        <td class="label">الخصم المطبق:</td>
        <td class="value">$ {{ number_format($order->discount_amount, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">حالة الطلب:</td>
        <td class="value">
            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
        </td>
    </tr>
    @if($order->admin_note)
    <tr>
        <td class="label">ملاحظة الإدارة:</td>
        <td class="value rtl">{{ $order->admin_note }}</td>
    </tr>
    @endif
</table>

@if($order->status === 'done')
<div class="action-text">
    شكراً لاستخدامك خدماتنا! يمكنك مراجعة تفاصيل الطلب من خلال <a href="{{ route('account.orders.show', $order) }}">حسابك</a>.
</div>
@elseif($order->status === 'rejected')
<div class="action-text">
    نعتذر عن عدم إمكانية إتمام طلبك. للمزيد من التفاصيل، يرجى مراجعة <a href="{{ route('account.orders.show', $order) }}">صفحة الطلب</a> أو التواصل مع الدعم.
</div>
@else
<div class="action-text">
    يمكنك متابعة حالة طلبك من خلال <a href="{{ route('account.orders.show', $order) }}">حسابك</a>.
</div>
@endif

@endcomponent
