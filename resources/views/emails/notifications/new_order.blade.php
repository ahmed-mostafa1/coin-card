@component('emails.layout', [
    'title' => 'Arab 8bp.in - إشعار طلب جديد',
    'subtitle' => 'تفاصيل طلب جديد للمراجعة',
    'preheader' => 'طلب جديد رقم #' . $order->id
])

<p class="intro-text">
    تم إنشاء طلب جديد على الموقع، وهذه تفاصيله الكاملة:
</p>

@php
    $externalTransaction = $order->external_bill_id ?? $order->external_uuid ?? null;
    $payload = $order->payload ?? [];
    $payloadFields = $order->service?->formFields?->keyBy('name_key') ?? collect();
@endphp

<div class="section-title">بيانات العميل</div>
<table class="details-table">
    <tr>
        <td class="label">اسم المستخدم:</td>
        <td class="value rtl">{{ $order->user->name }}</td>
    </tr>
    <tr>
        <td class="label">إيميل المستخدم:</td>
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
        <td class="label">قيمة الشحن:</td>
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
        <td class="value highlight-value">$ {{ number_format($order->discount_amount, 2) }}</td>
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
        <td class="label">تاريخ الإنشاء:</td>
        <td class="value">{{ $order->created_at->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
        <td class="label">آخر تحديث:</td>
        <td class="value">{{ $order->updated_at->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
        <td class="label">حالة الطلب الحالية:</td>
        <td class="value">
            <span class="status-badge status-pending">قيد المراجعة</span>
        </td>
    </tr>
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

<div class="action-text">
    لمراجعة طلب الشحن وتغيير حالته، يمكنك الدخول مباشرة إلى <a href="{{ route('admin.orders.show', $order) }}">لوحة التحكم</a>.
</div>

@endcomponent
