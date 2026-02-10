@component('emails.layout', [
    'title' => 'Arab 8bp.in - تم استلام طلب الشحن',
    'subtitle' => 'طلبك الآن قيد المراجعة',
    'preheader' => 'تم استلام طلب الشحن رقم #' . $deposit->id
])

@php
    $paymentMethodName = $deposit->paymentMethod->name ?? $deposit->payment_method ?? 'غير محدد';
    $payload = $deposit->payload ?? [];
    $payloadFields = $deposit->paymentMethod?->fields?->keyBy('name_key') ?? collect();
@endphp

<p class="intro-text">
    أهلاً {{ $deposit->user->name }}،
    <br>
    تم استلام طلب شحن الرصيد الخاص بك وهو الآن قيد المراجعة.
</p>

<div class="section-title">بيانات الحساب</div>
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
        <td class="label">رقم الطلب:</td>
        <td class="value highlight-value">#{{ $deposit->id }}</td>
    </tr>
    <tr>
        <td class="label">المبلغ:</td>
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
        <td class="label">حالة الطلب:</td>
        <td class="value">
            <span class="status-badge status-pending">قيد المراجعة</span>
        </td>
    </tr>
    @if($deposit->user_note)
    <tr>
        <td class="label">ملاحظتك:</td>
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
    سيتم إشعارك فور اكتمال مراجعة الطلب. يمكنك متابعة حالة طلبك من خلال <a href="{{ route('account.deposits.show', $deposit) }}">حسابك</a>.
</div>

@endcomponent
