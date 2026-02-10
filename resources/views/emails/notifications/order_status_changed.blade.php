@component('emails.layout', [
    'title' => 'Arab 8bp.in - تحديث حالة الطلب',
    'subtitle' => 'تم تحديث حالة طلبك',
    'preheader' => 'تحديث حالة الطلب رقم #' . $order->id
])

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

    $statusTimestamp = match($order->status) {
        'done' => $order->settled_at,
        'rejected' => $order->released_at,
        default => $order->updated_at
    };

    $externalTransaction = $order->external_bill_id ?? $order->external_uuid ?? null;
    $payload = $order->payload ?? [];
    $payloadFields = $order->service?->formFields?->keyBy('name_key') ?? collect();
@endphp

<p class="intro-text">
    {{ $greeting }}، وهذه تفاصيله:
</p>

<div class="section-title">بيانات الحساب</div>
<table class="details-table">
    <tr>
        <td class="label">اسم المستخدم:</td>
        <td class="value rtl">{{ $order->user->name }}</td>
    </tr>
    <tr>
        <td class="label">البريد الإلكتروني:</td>
        <td class="value"><a href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a></td>
    </tr>
    <tr>
        <td class="label">رقم المستخدم:</td>
        <td class="value">#{{ $order->user->id }}</td>
    </tr>
</table>

<div class="section-title">تفاصيل الطلب</div>
<table class="details-table">
    <tr>
        <td class="label">رقم الطلب / المعاملة:</td>
        <td class="value highlight-value">#{{ $order->id }}</td>
    </tr>
    @if($externalTransaction)
    <tr>
        <td class="label">معرّف المعاملة الخارجية:</td>
        <td class="value">{{ $externalTransaction }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">الخدمة:</td>
        <td class="value rtl">{{ $order->service->name }}</td>
    </tr>
    @if($order->variant)
    <tr>
        <td class="label">الباقة المختارة:</td>
        <td class="value rtl">{{ $order->variant->name }}</td>
    </tr>
    @endif
    @if($order->qty)
    <tr>
        <td class="label">الكمية:</td>
        <td class="value">{{ $order->qty }}</td>
    </tr>
    @endif
    @if($order->customer_identifier)
    <tr>
        <td class="label">بيانات العميل:</td>
        <td class="value rtl">{{ $order->customer_identifier }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">المبلغ المدفوع:</td>
        <td class="value highlight-value">$ {{ number_format($order->amount_held, 2) }}</td>
    </tr>
    @if($order->price_at_purchase && $order->price_at_purchase != $order->amount_held)
    <tr>
        <td class="label">السعر عند الشراء:</td>
        <td class="value">$ {{ number_format($order->price_at_purchase, 2) }}</td>
    </tr>
    @endif
    @if($order->original_price && $order->discount_percentage)
    <tr>
        <td class="label">السعر الأصلي:</td>
        <td class="value">$ {{ number_format($order->original_price, 2) }}</td>
    </tr>
    <tr>
        <td class="label">نسبة الخصم:</td>
        <td class="value">{{ number_format($order->discount_percentage, 2) }}%</td>
    </tr>
    @endif
    @if($order->discount_amount > 0)
    <tr>
        <td class="label">الخصم المطبق:</td>
        <td class="value">$ {{ number_format($order->discount_amount, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">طريقة الدفع:</td>
        <td class="value rtl">{{ $order->payment_method ?? 'رصيد الحساب' }}</td>
    </tr>
    @if($order->external_status)
    <tr>
        <td class="label">حالة المعاملة الخارجية:</td>
        <td class="value rtl">{{ $order->external_status }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">حالة الطلب:</td>
        <td class="value">
            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
        </td>
    </tr>
    <tr>
        <td class="label">تاريخ إنشاء الطلب:</td>
        <td class="value">{{ $order->created_at->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
        <td class="label">تاريخ تحديث الحالة:</td>
        <td class="value">{{ optional($statusTimestamp)->format('Y-m-d H:i') ?? $order->updated_at->format('Y-m-d H:i') }}</td>
    </tr>
    @if($order->settled_at)
    <tr>
        <td class="label">تاريخ التسوية:</td>
        <td class="value">{{ $order->settled_at->format('Y-m-d H:i') }}</td>
    </tr>
    @endif
    @if($order->released_at)
    <tr>
        <td class="label">تاريخ الإرجاع:</td>
        <td class="value">{{ $order->released_at->format('Y-m-d H:i') }}</td>
    </tr>
    @endif
    @if($order->admin_note)
    <tr>
        <td class="label">ملاحظة الإدارة:</td>
        <td class="value rtl">{{ $order->admin_note }}</td>
    </tr>
    @endif
</table>

@if(!empty($payload))
<div class="section-title">بيانات الطلب الإضافية</div>
<table class="details-table">
    @foreach($payload as $key => $value)
        @php
            $label = $payloadFields->get($key)?->label ?? $key;
            $displayValue = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;
            $displayValue = trim($displayValue) !== '' ? $displayValue : '-';
        @endphp
        <tr>
            <td class="label">{{ $label }}:</td>
            <td class="value rtl">{{ $displayValue }}</td>
        </tr>
    @endforeach
</table>
@endif

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
