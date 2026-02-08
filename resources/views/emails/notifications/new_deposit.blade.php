@component('emails.layout', ['title' => 'Coin7Card - إشعار طلب شحن رصيد جديد'])

<p class="intro-text">
    تم إنشاء طلب شحن رصيد جديد على الموقع، وهذه تفاصيله:
</p>

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
        <td class="value rtl">{{ $deposit->payment_method ?? 'غير محدد' }}</td>
    </tr>
    <tr>
        <td class="label">حالة الطلب الحالية:</td>
        <td class="value">
            <span class="status-badge status-pending">قيد المراجعة</span>
        </td>
    </tr>
</table>

<div class="action-text">
    لمراجعة طلب الشحن وتغيير حالته، يمكنك الدخول مباشرة إلى <a href="{{ route('admin.deposits.show', $deposit) }}">لوحة التحكم</a>.
</div>

@endcomponent
