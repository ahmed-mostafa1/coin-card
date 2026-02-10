@component('emails.layout', [
    'title' => 'Arab 8bp.in - إشعار طلب شحن رصيد جديد',
    'subtitle' => 'طلب شحن جديد يحتاج مراجعة',
    'preheader' => 'طلب شحن جديد رقم #' . $deposit->id
])

@php
    $paymentMethodName = $deposit->paymentMethod->name ?? $deposit->payment_method ?? 'غير محدد';
    $payload = $deposit->payload ?? [];
    $payloadFields = $deposit->paymentMethod?->fields?->keyBy('name_key') ?? collect();
@endphp

<p class="intro-text">
    تم إنشاء طلب شحن رصيد جديد على الموقع، وهذه تفاصيله:
</p>

<div class="section-title">بيانات العميل</div>
<table class="details-table">
    <tr>
        <td class="label">اسم المستخدم:</td>
        <td class="value rtl">{{ $deposit->user->name }}</td>
    </tr>
    <tr>
        <td class="label">البريد الإلكتروني:</td>
        <td class="value"><a href="mailto:{{ $deposit->user->email }}">{{ $deposit->user->email }}</a></td>
    </tr>
    <tr>
        <td class="label">رقم المستخدم:</td>
        <td class="value">#{{ $deposit->user->id }}</td>
    </tr>
</table>

<div class="section-title">تفاصيل الطلب</div>
<table class="details-table">
    <tr>
        <td class="label">رقم طلب الشحن:</td>
        <td class="value highlight-value">#{{ $deposit->id }}</td>
    </tr>
    <tr>
        <td class="label">اسم المستخدم:</td>
        <td class="value rtl">{{ $deposit->user->name }}</td>
    </tr>
    <tr>
        <td class="label">إيميل المستخدم:</td>
        <td class="value"><a href="mailto:{{ $deposit->user->email }}">{{ $deposit->user->email }}</a></td>
    </tr>
    <tr>
        <td class="label">قيمة الشحن:</td>
        <td class="value highlight-value">$ {{ number_format($deposit->user_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="label">طريقة الدفع:</td>
        <td class="value rtl">{{ $paymentMethodName }}</td>
    </tr>
    <tr>
        <td class="label">تاريخ الطلب:</td>
        <td class="value">{{ $deposit->created_at->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
        <td class="label">آخر تحديث:</td>
        <td class="value">{{ $deposit->updated_at->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
        <td class="label">حالة الطلب الحالية:</td>
        <td class="value">
            <span class="status-badge status-pending">قيد المراجعة</span>
        </td>
    </tr>
    @if($deposit->user_note)
    <tr>
        <td class="label">ملاحظة العميل:</td>
        <td class="value rtl">{{ $deposit->user_note }}</td>
    </tr>
    @endif
</table>

@if(!empty($payload))
<div class="section-title">بيانات التحويل</div>
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
    لمراجعة طلب الشحن وتغيير حالته، يمكنك الدخول مباشرة إلى <a href="{{ route('admin.deposits.show', $deposit) }}">لوحة التحكم</a>.
</div>

@endcomponent
