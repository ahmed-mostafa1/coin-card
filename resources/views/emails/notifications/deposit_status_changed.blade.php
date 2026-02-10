@component('emails.layout', [
    'title' => 'Arab 8bp.in - تحديث حالة طلب الشحن',
    'subtitle' => 'إشعار بنتيجة المراجعة',
    'preheader' => 'تحديث حالة طلب الشحن رقم #' . $deposit->id
])

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

    $statusTimestamp = $deposit->reviewed_at ?? $deposit->updated_at;
    $paymentMethodName = $deposit->paymentMethod->name ?? $deposit->payment_method ?? 'غير محدد';
    $payload = $deposit->payload ?? [];
    $payloadFields = $deposit->paymentMethod?->fields?->keyBy('name_key') ?? collect();
@endphp

<p class="intro-text">
    {{ $greeting }}، وهذه تفاصيله:
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
        <td class="label">رقم طلب الشحن:</td>
        <td class="value highlight-value">#{{ $deposit->id }}</td>
    </tr>
    <tr>
        <td class="label">المبلغ المطلوب:</td>
        <td class="value">$ {{ number_format($deposit->user_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="label">طريقة الدفع:</td>
        <td class="value rtl">{{ $paymentMethodName }}</td>
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
    <tr>
        <td class="label">تاريخ الطلب:</td>
        <td class="value">{{ $deposit->created_at->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
        <td class="label">تاريخ تحديث الحالة:</td>
        <td class="value">{{ $statusTimestamp->format('Y-m-d H:i') }}</td>
    </tr>
    @if($deposit->user_note)
    <tr>
        <td class="label">ملاحظة العميل:</td>
        <td class="value rtl">{{ $deposit->user_note }}</td>
    </tr>
    @endif
    @if($deposit->admin_note)
    <tr>
        <td class="label">ملاحظة الإدارة:</td>
        <td class="value rtl">{{ $deposit->admin_note }}</td>
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
